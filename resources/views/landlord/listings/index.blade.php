@extends('layouts.app')

@section('title', 'My Listings - Nestly')

@section('content')
<div style="min-height: 100vh; background: #0a0a0f; padding: 80px 20px;">
    <div style="max-width: 600px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2 style="color: white;">My Listings</h2>
            <a href="{{ route('landlord.listings.create') }}" style="background: #facc15; color: #1a1e24; padding: 10px 20px; border-radius: 40px; text-decoration: none;">+ Add Listing</a>
        </div>
        
        @forelse($listings ?? [] as $listing)
            <div style="background: #121218; border-radius: 20px; margin-bottom: 16px; overflow: hidden; border: 1px solid #1f1f2a;">
                <img src="{{ $listing['image'] }}" style="width: 100%; height: 180px; object-fit: cover;">
                <div style="padding: 16px;">
                    <h3 style="color: white;">{{ $listing['title'] }}</h3>
                    <p style="color: #aaa;">{{ $listing['location'] }}</p>
                    <div class="price" style="color: #facc15; font-weight: bold;">{{ $listing['priceFormatted'] }}</div>
                    <div style="display: flex; gap: 12px; margin-top: 16px;">
                        <a href="{{ route('landlord.listings.edit', $listing['id']) }}" style="color: #facc15; text-decoration: none;">✏️ Edit</a>
                        <form method="POST" action="{{ route('landlord.listings.destroy', $listing['id']) }}" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: none; border: none; color: #e53935; cursor: pointer;">🗑️ Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 60px 20px; background: #121218; border-radius: 32px;">
                <div style="font-size: 3rem; margin-bottom: 16px;">🏠</div>
                <h3 style="color: white;">No listings yet</h3>
                <p style="color: #aaa;">Start by adding your first property</p>
                <a href="{{ route('landlord.listings.create') }}" style="display: inline-block; margin-top: 20px; background: #facc15; color: #1a1e24; padding: 12px 24px; border-radius: 40px; text-decoration: none;">Add Listing</a>
            </div>
        @endforelse
    </div>
</div>
@endsection