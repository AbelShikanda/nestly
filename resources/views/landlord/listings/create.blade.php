@extends('layouts.app')

@section('title', 'Add Listing - Nestly')

@section('content')
<div style="min-height: 100vh; background: #0a0a0f; padding: 80px 20px;">
    <div style="max-width: 500px; margin: 0 auto; background: #121218; border-radius: 32px; padding: 24px; border: 1px solid rgba(250,204,21,0.2);">
        <h2 style="color: white; margin-bottom: 24px;">Add New Property</h2>
        
        <form method="POST" action="{{ route('landlord.listings.store') }}" enctype="multipart/form-data">
            @csrf
            
            <div style="margin-bottom: 16px;">
                <label style="color: #aaa; display: block; margin-bottom: 8px;">Property Title</label>
                <input type="text" name="title" required style="width: 100%; padding: 12px; background: #1e1e2a; border: 1px solid #333; border-radius: 12px; color: white;">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="color: #aaa; display: block; margin-bottom: 8px;">Location</label>
                <input type="text" name="location" required placeholder="e.g., Nairobi, Kilimani" style="width: 100%; padding: 12px; background: #1e1e2a; border: 1px solid #333; border-radius: 12px; color: white;">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                <div>
                    <label style="color: #aaa; display: block; margin-bottom: 8px;">Bedrooms</label>
                    <select name="beds" style="width: 100%; padding: 12px; background: #1e1e2a; border: 1px solid #333; border-radius: 12px; color: white;">
                        <option>1</option><option>2</option><option>3</option><option>4</option>
                    </select>
                </div>
                <div>
                    <label style="color: #aaa; display: block; margin-bottom: 8px;">Bathrooms</label>
                    <select name="baths" style="width: 100%; padding: 12px; background: #1e1e2a; border: 1px solid #333; border-radius: 12px; color: white;">
                        <option>1</option><option>2</option><option>3</option>
                    </select>
                </div>
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="color: #aaa; display: block; margin-bottom: 8px;">Price (KES)</label>
                <input type="number" name="price" required style="width: 100%; padding: 12px; background: #1e1e2a; border: 1px solid #333; border-radius: 12px; color: white;">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="color: #aaa; display: block; margin-bottom: 8px;">Main Image</label>
                <input type="file" name="main_image" accept="image/*" style="width: 100%; padding: 12px; background: #1e1e2a; border: 1px solid #333; border-radius: 12px; color: white;">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="color: #aaa; display: block; margin-bottom: 8px;">Description</label>
                <textarea name="description" rows="4" style="width: 100%; padding: 12px; background: #1e1e2a; border: 1px solid #333; border-radius: 12px; color: white;"></textarea>
            </div>
            
            <button type="submit" style="width: 100%; background: #facc15; color: #1a1e24; padding: 14px; border-radius: 40px; border: none; font-weight: 600; cursor: pointer;">Publish Listing</button>
            <a href="{{ route('landlord.listings.index') }}" style="display: block; text-align: center; margin-top: 16px; color: #aaa; text-decoration: none;">Cancel</a>
        </form>
    </div>
</div>
@endsection