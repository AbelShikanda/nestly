<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Add this for DB queries

class ExploreController extends Controller
{
    /**
     * Welcome/Landing page
     */
    public function welcome()
    {
        return view('pages.welcome');
    }

    /**
     * Main explore/reels page
     */
    public function index()
    {
        // Get active properties with landlord info
        $properties = Property::with('user', 'gallery')
            ->where('status', 'active')
            ->latest()
            ->get()
            ->map(function ($property) {
                return $this->formatPropertyForFrontend($property);
            });

        return view('pages.explore', compact('properties'));
    }

    /**
     * API: Get paginated properties for frontend (infinite scroll)
     * 
     * This method is called by the JavaScript fetchProperties() function
     * to load properties dynamically as the user scrolls.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiIndex(Request $request)
    {
        // Number of properties per page (default: 6)
        $perPage = $request->input('per_page', 6);

        // Query active properties with their relationships
        $properties = Property::with(['user', 'gallery'])
            ->where('status', 'active')
            ->latest()
            ->paginate($perPage);

        // Format each property for frontend consumption
        $formattedProperties = collect($properties->items())->map(function ($property) {
            return $this->formatPropertyForFrontend($property);
        });

        // Return JSON response with pagination metadata
        return response()->json([
            'success' => true,
            'data' => $formattedProperties,
            'current_page' => $properties->currentPage(),
            'last_page' => $properties->lastPage(),
            'per_page' => $properties->perPage(),
            'total' => $properties->total(),
        ]);
    }

    /**
     * NEW: API for smart infinite scroll with guidance
     * 
     * This method provides intelligent property loading based on:
     * - User preferences
     * - Property popularity
     * - Location
     * - Price drops
     * - Featured status
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiInfiniteScroll(Request $request)
    {
        $type = $request->input('type', 'featured');
        $limit = $request->input('limit', 6);

        // Start query with active properties only
        $query = Property::with(['user', 'gallery'])
            ->where('status', 'active');

        // Apply different strategies based on guidance type
        switch ($type) {
            case 'featured':
                $query->featured();
                break;

            case 'popular':
                $query->popular();
                break;

            case 'recent':
                $query->recent();
                break;

            case 'price_drop':
                $query->priceDrop();
                break;

            case 'near_you':
                if ($request->filled('latitude') && $request->filled('longitude')) {
                    $query->nearYou(
                        $request->latitude,
                        $request->longitude,
                        $request->input('distance', 10)
                    );
                } else {
                    $query->recent();
                }
                break;

            case 'personalized':
                // Get user's preferred property type from session
                $preferredType = $request->session()->get('preferred_property_type');
                $query->personalized($preferredType);
                break;

            case 'similar':
                // Get similar properties based on last viewed
                $lastViewedId = $request->input('last_viewed_id');
                if ($lastViewedId) {
                    $lastViewed = Property::find($lastViewedId);
                    if ($lastViewed) {
                        $query->where('property_type', $lastViewed->property_type)
                            ->whereBetween('price', [
                                $lastViewed->price * 0.7,
                                $lastViewed->price * 1.3
                            ])
                            ->where('id', '!=', $lastViewedId);
                    }
                }
                $query->recent();
                break;

            default:
                $query->recent();
        }

        $properties = $query->limit($limit)->get();

        // If no properties found with specific type, fallback to recent
        if ($properties->isEmpty()) {
            $properties = Property::with(['user', 'gallery'])
                ->where('status', 'active')
                ->recent()
                ->limit($limit)
                ->get();
        }

        $formattedProperties = $properties->map(function ($property) {
            return $this->formatPropertyForFrontend($property);
        });

        return response()->json([
            'success' => true,
            'data' => $formattedProperties,
            'guidance_type' => $type,
            'count' => $formattedProperties->count()
        ]);
    }

    /**
     * NEW: Track property view for personalization
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function trackPropertyView(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        // Increment view count and update last viewed timestamp
        $property->incrementViews();

        // Store user preference in session for personalization
        if ($property->property_type) {
            $preferences = $request->session()->get('property_preferences', []);
            $preferences[$property->property_type] = ($preferences[$property->property_type] ?? 0) + 1;
            $request->session()->put('property_preferences', $preferences);

            // Set the most viewed property type as preferred
            $preferredType = array_keys($preferences, max($preferences))[0] ?? null;
            $request->session()->put('preferred_property_type', $preferredType);
        }

        // Store last viewed property
        $request->session()->put('last_viewed_property_id', $id);
        $request->session()->put('last_viewed_property_type', $property->property_type);

        return response()->json([
            'success' => true,
            'message' => 'View tracked'
        ]);
    }

    /**
     * NEW: Get user preferences for personalization
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserPreferences(Request $request)
    {
        return response()->json([
            'success' => true,
            'preferences' => [
                'viewed_types' => $request->session()->get('property_preferences', []),
                'preferred_type' => $request->session()->get('preferred_property_type'),
                'last_viewed_id' => $request->session()->get('last_viewed_property_id'),
                'last_viewed_type' => $request->session()->get('last_viewed_property_type'),
            ]
        ]);
    }

    /**
     * NEW: Track user preference (called from frontend)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function trackPreference(Request $request)
    {
        $propertyType = $request->input('property_type');

        if ($propertyType) {
            $preferences = $request->session()->get('property_preferences', []);
            $preferences[$propertyType] = ($preferences[$propertyType] ?? 0) + 1;
            $request->session()->put('property_preferences', $preferences);

            // Update preferred type
            $preferredType = array_keys($preferences, max($preferences))[0] ?? null;
            $request->session()->put('preferred_property_type', $preferredType);
        }

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * API: Get single property details
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiShow($id)
    {
        $property = Property::with(['user', 'gallery'])->findOrFail($id);

        // Increment view count
        $property->increment('views_count');

        $formattedProperty = $this->formatPropertyForFrontend($property);

        return response()->json([
            'success' => true,
            'data' => $formattedProperty,
        ]);
    }

    /**
     * Show single property details
     */
    public function show($id)
    {
        $property = Property::with('user', 'gallery')
            ->findOrFail($id);

        // Increment view count
        $property->increment('views_count');

        $formattedProperty = $this->formatPropertyForFrontend($property);

        return view('pages.property', ['property' => $formattedProperty]);
    }

    /**
     * API: Get weighted random properties (biased by tags)
     * 
     * This method returns properties with higher weight for:
     * - Featured properties
     * - Verified properties  
     * - Price drops
     * - New listings.
     * - Popular properties
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    // public function apiWeightedRandom(Request $request)
    // {
    //     $limit = $request->input('limit', 20);

    //     // Get all active properties
    //     $properties = Property::with(['user', 'gallery'])
    //         ->where('status', 'active')
    //         ->get();

    //     // Calculate weights for each property
    //     $weightedProperties = [];
    //     foreach ($properties as $property) {
    //         $weight = 10; // Base weight

    //         // Featured: +30 (3x more likely)
    //         if ($property->is_featured) $weight += 30;

    //         // Verified: +20 (2x more likely)
    //         if ($property->is_verified) $weight += 20;

    //         // Price drop: +25 (2.5x more likely)
    //         if ($property->featured_tag === 'price_drop') $weight += 25;

    //         // New listing (less than 7 days): +20
    //         if ($property->created_at && $property->created_at->diffInDays(now()) < 7) {
    //             $weight += 20;
    //         }

    //         // Popular based on views: up to +30
    //         if ($property->views_count > 500) $weight += 30;
    //         elseif ($property->views_count > 100) $weight += 20;
    //         elseif ($property->views_count > 50) $weight += 10;

    //         // Property type bias
    //         if ($property->property_type === 'apartment') $weight += 15;
    //         if ($property->property_type === 'house') $weight += 10;

    //         $weightedProperties[] = [
    //             'property' => $property,
    //             'weight' => $weight
    //         ];
    //     }

    //     // Shuffle with weights (higher weight = more likely to be early)
    //     usort($weightedProperties, function ($a, $b) {
    //         $rand = mt_rand(1, 100);
    //         if ($rand > 70) {
    //             // 30% chance to compare by weight
    //             return $b['weight'] <=> $a['weight'];
    //         }
    //         // 70% chance random
    //         return mt_rand(-1, 1);
    //     });

    //     // Take top N properties
    //     $selectedProperties = array_slice($weightedProperties, 0, $limit);
    //     $selectedProperties = collect($selectedProperties)->pluck('property');

    //     $formattedProperties = $selectedProperties->map(function ($property) {
    //         return $this->formatPropertyForFrontend($property);
    //     });

    //     return response()->json([
    //         'success' => true,
    //         'data' => $formattedProperties,
    //         'biased' => true,
    //         'count' => $formattedProperties->count()
    //     ]);
    // }

    /**
     * Search properties
     */
    public function search(Request $request)
    {
        $query = Property::with('user', 'gallery')
            ->where('status', 'active');

        // Search by location
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('location', 'like', '%' . $request->q . '%')
                    ->orWhere('neighborhood', 'like', '%' . $request->q . '%')
                    ->orWhere('title', 'like', '%' . $request->q . '%');
            });
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by bedrooms
        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', $request->bedrooms);
        }

        // Filter by property type
        if ($request->filled('type')) {
            $query->where('title', 'like', '%' . $request->type . '%');
        }

        $properties = $query->latest()->get()
            ->map(function ($property) {
                return $this->formatPropertyForFrontend($property);
            });

        if ($request->ajax()) {
            return response()->json($properties);
        }

        return view('public.search', compact('properties'));
    }

    /**
     * API: Search properties (for AJAX requests)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiSearch(Request $request)
    {
        $query = Property::with(['user', 'gallery'])
            ->where('status', 'active');

        // Search by location or title
        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('location', 'like', '%' . $searchTerm . '%')
                    ->orWhere('neighborhood', 'like', '%' . $searchTerm . '%')
                    ->orWhere('title', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by bedrooms
        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', $request->bedrooms);
        }

        // Filter by property type
        if ($request->filled('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        // Filter by listing type
        if ($request->filled('listing_type')) {
            $query->where('listing_type', $request->listing_type);
        }

        // Pagination
        $perPage = $request->input('per_page', 10);
        $properties = $query->latest()->paginate($perPage);

        $formattedProperties = collect($properties->items())->map(function ($property) {
            return $this->formatPropertyForFrontend($property);
        });

        return response()->json([
            'success' => true,
            'data' => $formattedProperties,
            'current_page' => $properties->currentPage(),
            'last_page' => $properties->lastPage(),
            'total' => $properties->total(),
        ]);
    }

    /**
     * Format property for frontend display
     * 
     * This method ensures consistent data structure for both
     * web views and API responses.
     * 
     * @param \App\Models\Property $property
     * @return array
     */
    private function formatPropertyForFrontend($property)
    {
        // Build gallery array from database gallery relation
        $gallery = $property->gallery->map(function ($item) {
            return [
                'type' => $item->type ?? 'image',
                'url' => $item->video_url ?? $item->image_url,
                'label' => $item->caption ?? ($item->type === 'video' ? 'video tour' : 'photo'),
            ];
        });

        // If no gallery images, use main image as fallback
        if ($gallery->isEmpty() && $property->main_image) {
            $gallery->push([
                'type' => 'image',
                'url' => $property->main_image,
                'label' => 'main view',
            ]);
        }

        // If still no gallery, add placeholder
        if ($gallery->isEmpty()) {
            $gallery->push([
                'type' => 'image',
                'url' => 'https://placehold.co/800x600/1a1a2e/facc15?text=No+Image',
                'label' => 'no image',
            ]);
        }

        // Determine badge based on location
        $badge = 'KENYA';
        $location = strtolower($property->location ?? '');
        if (str_contains($location, 'nairobi')) {
            $badge = 'NAIROBI';
        } elseif (str_contains($location, 'mombasa')) {
            $badge = 'MOMBASA';
        } elseif (str_contains($location, 'kisumu')) {
            $badge = 'KISUMU';
        }

        // Add featured tag if property has one
        if ($property->featured_tag) {
            $badge = $property->featured_tag;
        }

        return [
            'id' => $property->id,
            'location' => $property->location,
            'title' => $property->title,
            'beds' => $property->bedrooms . ' bed' . ($property->bedrooms != 1 ? 's' : ''),
            'baths' => $property->bathrooms . ' bath' . ($property->bathrooms != 1 ? 's' : ''),
            'area' => $property->area_sqft ? $property->area_sqft . ' sqft' : 'N/A',
            'price' => $property->price,
            'priceFormatted' => 'KES ' . number_format($property->price),
            'description' => $property->description,
            'badge' => $badge,
            'property_type' => $property->property_type,
            'listing_type' => $property->listing_type,
            'furnishing' => $property->furnishing,
            'amenities' => $property->amenities_list,
            'is_featured' => $property->is_featured,
            'featured_tag' => $property->featured_tag,
            'popularity_score' => $property->popularity_score,
            'mainMedia' => [
                'type' => $gallery->first()['type'] ?? 'image',
                'url' => $gallery->first()['url'] ?? $property->main_image,
            ],
            'gallery' => $gallery->toArray(),
            'landlord' => [
                'id' => $property->user->id ?? null,
                'name' => $property->user->name ?? 'Property Owner',
                'company' => $property->user->company ?? 'Individual Owner',
                'phone' => $property->user->phone ?? 'Contact via chat',
                'email' => $property->user->email ?? 'N/A',
                'verified' => $property->is_verified ?? false,
                'responseTime' => '< 1 hour',
                'memberSince' => $property->user && $property->user->created_at
                    ? $property->user->created_at->format('Y')
                    : date('Y'),
            ],
        ];
    }
}
