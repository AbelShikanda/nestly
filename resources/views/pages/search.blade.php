@extends('layouts.app')

@section('title', 'Search Results - Nestly')

@section('content')
<div style="padding: 80px 20px 100px;">
    <h2 style="color: white; margin-bottom: 20px;">🔍 Search Results for "{{ request('q') }}"</h2>
    
    @if(count($properties ?? []) > 0)
        <div class="reels-container" style="height: auto; overflow-y: visible; scroll-snap-type: none;">
            @foreach($properties as $property)
                @include('components.property-card', ['property' => $property, 'index' => $loop->index])
            @endforeach
        </div>
    @else
        <div style="text-align: center; color: #aaa; padding: 60px 20px;">
            <div style="font-size: 3rem; margin-bottom: 16px;">🔍</div>
            <h3 style="color: white;">No properties found</h3>
            <p>Try adjusting your search or browse all listings</p>
            <a href="{{ route('explore') }}" style="display: inline-block; margin-top: 20px; background: #facc15; color: #1a1e24; padding: 12px 24px; border-radius: 40px; text-decoration: none;">Browse All</a>
        </div>
    @endif
</div>
@endsection