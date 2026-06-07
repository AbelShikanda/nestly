@props(['landlord'])

<div class="landlord-btn" data-landlord-name="{{ $landlord['name'] }}" data-landlord-phone="{{ $landlord['phone'] ?? '' }}">
    <div class="landlord-avatar">👤</div>
    <div>
        <div class="landlord-name">{{ $landlord['name'] }}</div>
        <div class="landlord-badge">{{ $landlord['verified'] ? '✓ Verified' : 'Owner' }}</div>
    </div>
</div>