@extends('layouts.app')

@section('title', 'Manage Properties - Admin')

@section('content')
<div style="min-height: 100vh; background: #0a0a0f; padding: 80px 20px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h2 style="color: white; margin-bottom: 24px;">Manage Properties</h2>

        <!-- Create Property Button -->
        <div style="margin-bottom: 24px;">
            <button style="background: #1f7a3c; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 500;">
                + Add Property
            </button>
        </div>

        @if(isset($properties) && $properties->count() > 0)
        @foreach($properties as $property)
            <div style="background: #121218; border-radius: 20px; padding: 16px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #1f1f2a; flex-wrap: wrap;">
                <div style="flex: 1;">
                    <div style="color: white; font-weight: 600;">{{ Str::substr($property->title, 0, 40) }}</div>
                    <div style="color: #aaa; font-size: 0.8rem;">{{ Str::substr($property->location, 0, 30) }} • {{ $property->priceFormatted }}</div>
                </div>
                <div style="white-space: nowrap;">
                    @if($property->is_verified)
                        <span style="background: #38a169; color: white; padding: 4px 8px; border-radius: 10px; font-size: 0.7rem; margin-right: 8px;">Verified</span>
                    @else
                        <span style="background: #e53935; color: white; padding: 4px 8px; border-radius: 10px; font-size: 0.7rem; margin-right: 8px;">Pending</span>
                    @endif
                    @if($property->is_featured)
                        <span style="background: #ffb300; color: #1a1a1f; padding: 4px 8px; border-radius: 10px; font-size: 0.7rem;">Featured</span>
                    @endif
                </div>
                <div style="width: 120px;">
                    <form method="POST" action="{{ route('admin.properties.destroy', $property->id) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: none; border: none; color: #e53935; cursor: pointer; padding: 4px; font-size: 0.8rem;">🗑️</button>
                    </form>
                    <a href="{{ route('admin.properties.show', $property->id) }}" style="background: none; border: none; color: #64b5f6; cursor: pointer; padding: 4px; font-size: 0.8rem;">👁️</a>
                </div>
            </div>
        @endforeach
        @else
            <div style="color: #666; padding: 40px; text-align: center;">No properties found</div>
        @endif
    </div>
</div>
@endsection