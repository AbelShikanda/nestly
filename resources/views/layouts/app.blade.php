<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kenya Real Estate')</title>
    <link rel="stylesheet" href="{{ asset('nestly/css/style.css') }}">
    @stack('styles')
</head>
<body>

    {{-- Unified Header --}}
    @section('header')
        <div class="search-header" id="searchHeader">
            <div class="search-container">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-input" id="searchInput" placeholder="Search Nairobi, Mombasa, Kisumu..." autocomplete="off">
                <button class="search-clear" id="searchClearBtn">✕</button>
            </div>
            @auth
                <button class="header-notification-btn" id="notificationBtn">
                    💬<span class="header-notification-badge" id="chatBadge">0</span>
                </button>
            @endauth
        </div>
    @show

<<<<<<< HEAD
    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>
=======
    <!-- Mobile Nav -->
    <div id="fh5co-mobile-nav">
        <ul>
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('properties') }}">Explore</a></li>
            <li><a href="{{ route('properties.create') }}" style="border-left: 1px solid #fff; border-right: 1px solid #fff;">Add</a></li>
            <li><a href="{{ route('properties') }}">Search</a></li>
            <li><a href="#" id="mobile-profile">Profile</a></li>
        </ul>
    </div>
    </div>

    <!-- jQuery -->
>>>>>>> 5dca717 (Phase 1: PostgreSQL migration + mobile-first UI + functional buttons/forms)

    {{-- Bottom Navigation --}}
    @section('bottom-nav')
        <div class="bottom-nav" id="bottomNav">
            <div class="nav-item" data-nav="explore">
                <div class="nav-icon">🔍</div>
                <div class="nav-label">Explore</div>
            </div>
            @auth
                <div class="nav-item" data-nav="profile">
                    <div class="nav-icon">👤</div>
                    <div class="nav-label">Profile</div>
                </div>
            @endauth
        </div>
    @show

    {{-- Role-based Add Button --}}
    @auth
        @if(auth()->user() && auth()->user()->role === 'landlord')
            @section('add-button')
                <button class="add-listing-btn" id="addListingBtn">➕</button>
            @show
        @endif
    @endauth

    <div class="doubletap-hint" id="doubleTapHint">✨ Double tap to hide/show UI · Tap title for details</div>
    <div class="demo-toast" id="demoToast">🇰🇪 Kenya Real Estate</div>
    <div class="no-results-toast" id="noResultsToast">✨ No matching properties</div>

    @include('components.detail-card')
    @include('components.profile-card')
    @include('components.chat-modal')
    @include('components.chat-list')

    <script src="{{ asset('nestly/js/main.js') }}"></script>
    @stack('scripts')
</body>
</html>