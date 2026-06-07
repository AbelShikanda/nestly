<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * List all properties
     */
    public function index(Request $request)
    {
        $properties = Property::with('user');
        
        if ($request->filled('search')) {
            $properties->where('title', 'like', '%' . $request->search . '%')
                      ->orWhere('location', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('status')) {
            $properties->where('status', $request->status);
        }
        
        $properties = $properties->latest()->paginate(20);
        
        return view('admin.properties.index', compact('properties'));
    }

    /**
     * Show single property
     */
    public function show($id)
    {
        $property = Property::with('user', 'gallery')->findOrFail($id);
        return view('admin.properties.show', compact('property'));
    }

    /**
     * Delete property
     */
    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        
        // Delete images
        if ($property->main_image) {
            Storage::disk('public')->delete($property->main_image);
        }
        
        foreach ($property->gallery as $image) {
            Storage::disk('public')->delete($image->image_url);
        }
        
        $property->delete();
        
        return redirect()->route('admin.properties.index')
            ->with('success', 'Property deleted successfully!');
    }

    /**
     * Verify property
     */
    public function verify($id)
    {
        $property = Property::findOrFail($id);
        $property->update(['is_verified' => !$property->is_verified]);
        
        $status = $property->is_verified ? 'verified' : 'unverified';
        return back()->with('success', "Property has been {$status}!");
    }

    /**
     * Feature/unfeature property
     */
    public function feature($id)
    {
        $property = Property::findOrFail($id);
        $property->update(['is_featured' => !$property->is_featured]);
        
        return back()->with('success', $property->is_featured ? 'Property featured!' : 'Property unfeatured.');
    }

    /**
     * Delete gallery image
     */
    public function deleteImage($propertyId, $imageId)
    {
        $image = PropertyGallery::where('property_id', $propertyId)->findOrFail($imageId);
        
        Storage::disk('public')->delete($image->image_url);
        $image->delete();
        
        return back()->with('success', 'Image deleted successfully!');
    }

    /**
     * Bulk action on properties
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'property_ids' => 'required|array',
            'action' => 'required|in:delete,verify,feature,unfeature,activate,suspend',
        ]);
        
        $properties = Property::whereIn('id', $request->property_ids);
        
        switch ($request->action) {
            case 'delete':
                foreach ($properties->get() as $property) {
                    if ($property->main_image) {
                        Storage::disk('public')->delete($property->main_image);
                    }
                    $property->delete();
                }
                $message = 'Properties deleted!';
                break;
            case 'verify':
                $properties->update(['is_verified' => true]);
                $message = 'Properties verified!';
                break;
            case 'feature':
                $properties->update(['is_featured' => true]);
                $message = 'Properties featured!';
                break;
            case 'unfeature':
                $properties->update(['is_featured' => false]);
                $message = 'Properties unfeatured!';
                break;
            case 'activate':
                $properties->update(['status' => 'active']);
                $message = 'Properties activated!';
                break;
            case 'suspend':
                $properties->update(['status' => 'inactive']);
                $message = 'Properties suspended!';
                break;
        }
        
        return back()->with('success', $message);
    }
}