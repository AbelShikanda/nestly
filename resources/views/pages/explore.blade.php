@extends('layouts.app')

@section('title', 'Explore Properties - Kenya Real Estate')

@section('content')
    <div class="reels-container" id="reelsContainer">
        {{-- Properties loaded dynamically via JavaScript --}}
    </div>
@endsection

@push('scripts')
<script>
    // Pass data from Laravel to JavaScript
    const initialProperties = @json($properties ?? []);
    const currentUser = @json(auth()->user());
    const isAuthenticated = @json(auth()->check());
</script>
<!-- <script src="{{ asset('js/reels.js') }}"></script>
<script src="{{ asset('js/chat.js') }}"></script>
<script src="{{ asset('js/profile.js') }}"></script> -->
@endpush