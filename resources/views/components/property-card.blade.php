@props(['property', 'index'])

<div class="slide" data-city="{{ strtolower($property['location']) }}" 
     data-property="{{ strtolower($property['title']) }}" 
     data-price="{{ $property['price'] }}">
    
    <div class="hero-media" id="hero-{{ $index }}">
        @if($property['mainMedia']['type'] === 'video')
            <video class="main-video" autoplay muted loop playsinline>
                <source src="{{ $property['mainMedia']['url'] }}" type="video/mp4">
            </video>
        @else
            <img class="main-img" src="{{ $property['mainMedia']['url'] }}" alt="{{ $property['title'] }}">
        @endif
    </div>
    
    <div class="landlord-btn">
        <div class="landlord-avatar">👤</div>
        <div>
            <div class="landlord-name">{{ $property['landlord']['name'] }}</div>
            <div class="landlord-badge">{{ $property['landlord']['verified'] ? '✓ Verified' : 'Owner' }}</div>
        </div>
    </div>
    
    <div class="overlay">
        <div class="badge">🇰🇪 {{ str_contains($property['location'], 'Nairobi') ? 'NAIROBI' : (str_contains($property['location'], 'Mombasa') ? 'MOMBASA' : 'KENYA') }}</div>
        <div class="property-title property-title-{{ $index }}">{{ $property['title'] }}</div>
        <div class="property-detail">📍 {{ $property['location'] }} | {{ $property['beds'] }} · {{ $property['baths'] }} · {{ $property['area'] }}</div>
        <div class="price">{{ $property['priceFormatted'] }}</div>
    </div>
    
    <div class="thumbnail-strip" id="thumbstrip-{{ $index }}"></div>
</div>