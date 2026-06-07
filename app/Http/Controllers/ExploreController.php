<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyGallery;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    /**
     * Welcome/Landing page
     */
    public function welcome()
    {
        return view('public.welcome');
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

        return view('public.explore', compact('properties'));
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
        
        return view('public.property', ['property' => $formattedProperty]);
    }

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
     * Format property for frontend display
     */
    private function formatPropertyForFrontend($property)
    {
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

        return [
            'id' => $property->id,
            'location' => $property->location,
            'title' => $property->title,
            'beds' => $property->bedrooms . ' bed',
            'baths' => $property->bathrooms . ' bath',
            'area' => $property->area_sqft ? $property->area_sqft . ' sqft' : 'N/A',
            'price' => $property->price,
            'priceFormatted' => 'KES ' . number_format($property->price),
            'description' => $property->description,
            'mainMedia' => [
                'type' => $gallery->first()['type'] ?? 'image',
                'url' => $gallery->first()['url'] ?? $property->main_image,
            ],
            'gallery' => $gallery->toArray(),
            'landlord' => [
                'name' => $property->user->name,
                'company' => $property->user->company ?? 'Individual Owner',
                'phone' => $property->user->phone,
                'email' => $property->user->email,
                'verified' => $property->is_verified,
                'responseTime' => '< 1 hour',
                'memberSince' => $property->user->created_at->format('Y'),
            ],
        ];
    }
}