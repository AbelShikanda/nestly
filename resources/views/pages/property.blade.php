@extends('layouts.app')

@section('title', $property['title'] ?? 'Property Details')

@section('content')
<div class="detail-overlay active" style="visibility: visible; opacity: 1;">
    <div class="detail-card" style="transform: scale(1);">
        <div class="detail-scroll">
            <div class="detail-hero">
                @if($property['mainMedia']['type'] === 'video')
                    <video autoplay muted loop playsinline>
                        <source src="{{ $property['mainMedia']['url'] }}" type="video/mp4">
                    </video>
                @else
                    <img src="{{ $property['mainMedia']['url'] }}" alt="{{ $property['title'] }}">
                @endif
            </div>
            
            <div class="detail-thumbnails">
                @foreach($property['gallery'] as $index => $item)
                    <div class="detail-thumb {{ $index === 0 ? 'active-detail-thumb' : '' }}">
                        @if($item['type'] === 'image')
                            <img src="{{ $item['url'] }}" alt="thumbnail">
                        @else
                            <video src="{{ $item['url'] }}" muted loop playsinline></video>
                        @endif
                    </div>
                @endforeach
            </div>
            
            <div class="detail-info">
                <div class="detail-title">{{ $property['title'] }}</div>
                <div class="detail-location" style="color:#aaa; margin:8px 0">📍 {{ $property['location'] }}</div>
                <div class="detail-specs" style="display:flex; gap:16px; margin:12px 0">
                    <span>🛏️ {{ $property['beds'] }}</span>
                    <span>🛁 {{ $property['baths'] }}</span>
                    <span>📏 {{ $property['area'] }}</span>
                </div>
                <div class="detail-price">{{ $property['priceFormatted'] }}</div>
                <div class="detail-desc" style="color:#bbb">{{ $property['description'] }}</div>
                
                <div class="action-buttons">
                    <a href="{{ route('chat.show', ['user' => $property['landlord']['id'] ?? 1]) }}" class="chat-btn">💬 Chat with {{ $property['landlord']['name'] }}</a>
                    <a href="{{ route('explore') }}" class="close-detail-btn">← Back to Explore</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection