@extends('layouts.app')

@section('title', 'Explore Properties - Nestly | Kenya Real Estate')

@section('content')
    <div class="reels-container" id="reelsContainer">
        <div class="loading-initial" style="display: flex; justify-content: center; align-items: center; height: 100vh; color: #facc15;">
            <div style="text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 16px;">🏠</div>
                <p>Loading properties...</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // ============================================================
    // ATTACH LARAVEL DATA TO WINDOW OBJECT
    // ============================================================
    window.currentUser = @json(auth()->user());
    window.isAuthenticated = @json(auth()->check());
    
    // API endpoints
    window.API_URL = @json(url('/api'));
    window.PROPERTIES_API_URL = @json(url('/api/properties'));
    window.PROPERTY_SEARCH_API_URL = @json(url('/api/properties/search'));
    
    // Initial properties (fallback if API fails)
    window.initialProperties = @json($properties ?? []);
    
    // Storage URL for uploaded images
    window.STORAGE_URL = @json(asset('storage'));
    
    console.log('Blade data loaded:', { 
        isAuthenticated: window.isAuthenticated,
        currentUser: window.currentUser,
        initialPropertiesCount: window.initialProperties.length
    });
</script>

<!-- Main JavaScript bundle -->
<script src="{{ asset('nestly/js/main.js') }}"></script>
@endpush