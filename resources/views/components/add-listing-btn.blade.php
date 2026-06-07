@auth
    @if(auth()->user() && auth()->user()->role === 'landlord')
        <button class="add-listing-btn" id="addListingBtn">➕</button>
    @endif
@endauth