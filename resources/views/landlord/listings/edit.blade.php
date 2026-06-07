@extends('layouts.app')

@section('title', 'Edit Listing - Nestly')

@section('content')
<div style="min-height: 100vh; background: #0a0a0f; padding: 80px 20px;">
    <div style="max-width: 500px; margin: 0 auto; background: #121218; border-radius: 32px; padding: 24px; border: 1px solid rgba(250,204,21,0.2);">
        <h2 style="color: white; margin-bottom: 24px;">Edit Property</h2>
        
        <form method="POST" action="{{ route('landlord.listings.update', $listing['id']) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div style="margin-bottom: 16px;">
                <label style="color: #aaa; display: block; margin-bottom: 8px;">Property Title</label>
                <input type="text" name="title" value="{{ $listing['title'] }}" required style="width: 100%; padding: 12px; background: #1e1e2a; border: 1px solid #333; border-radius: 12px; color: white;">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="color: #aaa; display: block; margin-bottom: 8px;">Location</label>
                <input type="text" name="location" value="{{ $listing['location'] }}" required style="width: 100%; padding: 12px; background: #1e1e2a; border: 1px solid #333; border-radius: 12px; color: white;">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                <div>
                    <label style="color: #aaa; display: block; margin-bottom: 8px;">Bedrooms</label>
                    <select name="beds" style="width: 100%; padding: 12px; background: #1e1e2a; border: 1px solid #333; border-radius: 12px; color: white;">
                        <option {{ $listing['beds'] == 1 ? 'selected' : '' }}>1</option>
                        <option {{ $listing['beds'] == 2 ? 'selected' : '' }}>2</option>
                        <option {{ $listing['beds'] == 3 ? 'selected' : '' }}>3</option>
                        <option {{ $listing['beds'] == 4 ? 'selected' : '' }}>4</option>
                    </select>
                </div>
                <div>
                    <label style="color: #aaa; display: block; margin-bottom: 8px;">Bathrooms</label>
                    <select name="baths" style="width: 100%; padding: 12px; background: #1e1e2a; border: 1px solid #333; border-radius: 12px; color: white;">
                        <option {{ $listing['baths'] == 1 ? 'selected' : '' }}>1</option>
                        <option {{ $listing['baths'] == 2 ? 'selected' : '' }}>2</option>
                        <option {{ $listing['baths'] == 3 ? 'selected' : '' }}>3</option>
                    </select>
                </div>
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="color: #aaa; display: block; margin-bottom: 8px;">Price (KES)</label>
                <input type="number" name="price" value="{{ $listing['price'] }}" required style="width: 100%; padding: 12px; background: #1e1e2a; border: 1px solid #333; border-radius: 12px; color: white;">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="color: #aaa; display: block; margin-bottom: 8px;">New Main Image (optional)</label>
                <input type="file" name="main_image" accept="image/*" style="width: 100%; padding: 12px; background: #1e1e2a; border: 1px solid #333; border-radius: 12px; color: white;">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="color: #aaa; display: block; margin-bottom: 8px;">Description</label>
                <textarea name="description" rows="4" style="width: 100%; padding: 12px; background: #1e1e2a; border: 1px solid #333; border-radius: 12px; color: white;">{{ $listing['description'] }}</textarea>
            </div>
            
            <button type="submit" style="width: 100%; background: #facc15; color: #1a1e24; padding: 14px; border-radius: 40px; border: none; font-weight: 600; cursor: pointer;">Update Listing</button>
            <a href="{{ route('landlord.listings.index') }}" style="display: block; text-align: center; margin-top: 16px; color: #aaa; text-decoration: none;">Cancel</a>
        </form>
    </div>
</div>
@endsection