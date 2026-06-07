<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ListingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:landlord')->except(['index', 'show']);
    }

    /**
     * Display landlord's listings
     */
    public function index()
    {
        $listings = Property::where('user_id', auth()->id())
            ->with('gallery')
            ->latest()
            ->get()
            ->map(function ($property) {
                return [
                    'id' => $property->id,
                    'title' => $property->title,
                    'location' => $property->location,
                    'price' => $property->price,
                    'priceFormatted' => 'KES ' . number_format($property->price),
                    'image' => $property->main_image ?? ($property->gallery->first()->image_url ?? null),
                    'status' => $property->status,
                    'views' => $property->views_count,
                    'inquiries' => $property->inquiry_count,
                    'created_at' => $property->created_at->format('M d, Y'),
                ];
            });

        return view('landlord.listings.index', compact('listings'));
    }

    /**
     * Show create listing form
     */
    public function create()
    {
        return view('landlord.listings.create');
    }

    /**
     * Store new listing
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'beds' => 'required|integer|min:0',
            'baths' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'main_image' => 'required|image|max:5120',
            'gallery_images.*' => 'nullable|image|max:5120',
        ]);

        // Store main image
        $mainImagePath = $request->file('main_image')->store('properties', 'public');

        // Create property
        $property = Property::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'bedrooms' => $request->beds,
            'bathrooms' => $request->baths,
            'area_sqft' => $request->area,
            'price' => $request->price,
            'price_period' => $request->price_period ?? 'monthly',
            'main_image' => $mainImagePath,
            'status' => 'active',
        ]);

        // Store gallery images
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $path = $image->store('properties/gallery', 'public');
                PropertyGallery::create([
                    'property_id' => $property->id,
                    'image_url' => $path,
                    'type' => 'image',
                    'order' => 0,
                ]);
            }
        }

        return redirect()->route('landlord.listings.index')
            ->with('success', 'Property listed successfully!');
    }

    /**
     * Show edit listing form
     */
    public function edit($id)
    {
        $listing = Property::where('user_id', auth()->id())
            ->with('gallery')
            ->findOrFail($id);
        
        return view('landlord.listings.edit', compact('listing'));
    }

    /**
     * Update listing
     */
    public function update(Request $request, $id)
    {
        $property = Property::where('user_id', auth()->id())->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'beds' => 'required|integer|min:0',
            'baths' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'main_image' => 'nullable|image|max:5120',
        ]);

        $property->update([
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'bedrooms' => $request->beds,
            'bathrooms' => $request->baths,
            'price' => $request->price,
        ]);

        // Update main image if provided
        if ($request->hasFile('main_image')) {
            // Delete old image
            if ($property->main_image) {
                Storage::disk('public')->delete($property->main_image);
            }
            $path = $request->file('main_image')->store('properties', 'public');
            $property->update(['main_image' => $path]);
        }

        return redirect()->route('landlord.listings.index')
            ->with('success', 'Property updated successfully!');
    }

    /**
     * Delete listing
     */
    public function destroy($id)
    {
        $property = Property::where('user_id', auth()->id())->findOrFail($id);
        
        // Delete images
        if ($property->main_image) {
            Storage::disk('public')->delete($property->main_image);
        }
        
        foreach ($property->gallery as $image) {
            Storage::disk('public')->delete($image->image_url);
        }
        
        $property->delete();

        return redirect()->route('landlord.listings.index')
            ->with('success', 'Property deleted successfully!');
    }

    /**
     * Feature/unfeature listing
     */
    public function feature($id)
    {
        $property = Property::where('user_id', auth()->id())->findOrFail($id);
        $property->update(['is_featured' => !$property->is_featured]);
        
        return back()->with('success', $property->is_featured ? 'Property featured!' : 'Property unfeatured.');
    }

    /**
     * Get listing analytics
     */
    public function analytics()
    {
        $listings = Property::where('user_id', auth()->id())->get();
        
        $totalViews = $listings->sum('views_count');
        $totalInquiries = $listings->sum('inquiry_count');
        
        return view('landlord.analytics', compact('listings', 'totalViews', 'totalInquiries'));
    }
}