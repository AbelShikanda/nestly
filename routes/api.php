<?php

use App\Http\Controllers\ExploreController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\ListingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// ============================================================
// PUBLIC API ROUTES (No authentication required)
// ============================================================

// Properties API
Route::get('/properties', [ExploreController::class, 'apiIndex'])->name('api.properties.index');
Route::get('/properties/{id}', [ExploreController::class, 'apiShow'])->name('api.properties.show');
Route::get('/properties/search', [ExploreController::class, 'apiSearch'])->name('api.properties.search');

// ============================================================
// NEW: SMART INFINITE SCROLL ROUTES (Public)
// ============================================================

// Smart infinite scroll with guidance (featured, popular, recent, price_drop, near_you, personalized, similar)
Route::get('/properties/infinite', [ExploreController::class, 'apiInfiniteScroll'])->name('api.properties.infinite');

// Track property views for personalization (can be public, but we'll make it available to all)
Route::post('/properties/{id}/track-view', [ExploreController::class, 'trackPropertyView'])->name('api.properties.track-view');

// Get user preferences from session (public, uses session)
Route::get('/user/preferences', [ExploreController::class, 'getUserPreferences'])->name('api.user.preferences');

// Track user preference (public, uses session)
Route::post('/properties/track-preference', [ExploreController::class, 'trackPreference'])->name('api.properties.track-preference');

// Property types and locations (for filters)
Route::get('/locations', function () {
    return response()->json([
        'success' => true,
        'data' => [
            'Nairobi, Kilimani',
            'Nairobi, Westlands',
            'Nairobi, Karen',
            'Nairobi, Lavington',
            'Nairobi, Kileleshwa',
            'Nairobi, Runda',
            'Mombasa, Nyali',
            'Mombasa, Bamburi',
            'Kisumu, Milimani',
            'Kiambu, Thika Road',
            'Nakuru, Milimani',
            'Eldoret, Kapsoya',
        ]
    ]);
})->name('api.locations');

// Property types enum (for frontend filters)
Route::get('/property-types', function () {
    return response()->json([
        'success' => true,
        'data' => [
            ['value' => 'apartment', 'label' => 'Apartment'],
            ['value' => 'house', 'label' => 'House'],
            ['value' => 'villa', 'label' => 'Villa'],
            ['value' => 'commercial', 'label' => 'Commercial'],
            ['value' => 'land', 'label' => 'Land'],
            ['value' => 'townhouse', 'label' => 'Townhouse'],
        ]
    ]);
})->name('api.property-types');

// Listing types enum
Route::get('/listing-types', function () {
    return response()->json([
        'success' => true,
        'data' => [
            ['value' => 'sale', 'label' => 'For Sale'],
            ['value' => 'rent', 'label' => 'For Rent'],
            ['value' => 'short_stay', 'label' => 'Short Stay'],
        ]
    ]);
})->name('api.listing-types');

// ============================================================
// AUTHENTICATED API ROUTES (Login required)
// ============================================================
Route::middleware(['auth:api'])->group(function () {

    // User profile
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ]);
    })->name('api.user');

    Route::put('/user/profile', [ProfileController::class, 'apiUpdate'])->name('api.user.update');
    Route::post('/user/avatar', [ProfileController::class, 'apiUploadAvatar'])->name('api.user.avatar');

    // Chat System
    Route::prefix('/chat')->name('api.chat.')->group(function () {
        Route::get('/conversations', [ChatController::class, 'apiInbox'])->name('conversations');
        Route::get('/conversations/{userId}', [ChatController::class, 'apiShow'])->name('show');
        Route::post('/conversations/{userId}/send', [ChatController::class, 'apiSend'])->name('send');
        Route::post('/conversations/{conversationId}/read', [ChatController::class, 'apiMarkAsRead'])->name('mark-read');
        Route::get('/unread-count', [ChatController::class, 'apiUnreadCount'])->name('unread-count');
    });

    // User's Properties (for landlords)
    Route::prefix('/listings')->name('api.listings.')->group(function () {
        Route::get('/', [ListingController::class, 'apiIndex'])->name('index');
        Route::post('/', [ListingController::class, 'apiStore'])->name('store');
        Route::get('/{id}', [ListingController::class, 'apiShow'])->name('show');
        Route::put('/{id}', [ListingController::class, 'apiUpdate'])->name('update');
        Route::delete('/{id}', [ListingController::class, 'apiDestroy'])->name('destroy');
        Route::post('/{id}/feature', [ListingController::class, 'apiFeature'])->name('feature');
    });

    // Support Tickets
    Route::prefix('/support')->name('api.support.')->group(function () {
        Route::get('/tickets', [SupportController::class, 'apiIndex'])->name('index');
        Route::post('/tickets', [SupportController::class, 'apiStore'])->name('store');
        Route::get('/tickets/{id}', [SupportController::class, 'apiShow'])->name('show');
        Route::post('/tickets/{id}/reply', [SupportController::class, 'apiReply'])->name('reply');
    });

    // Subscriptions
    Route::prefix('/subscription')->name('api.subscription.')->group(function () {
        Route::get('/plans', function () {
            return response()->json([
                'success' => true,
                'data' => [
                    'standard' => ['name' => 'Standard', 'price' => 499, 'features' => ['10 active listings', 'SMS replies', 'Basic analytics']],
                    'gold' => ['name' => 'Gold', 'price' => 999, 'features' => ['25 active listings', 'Priority support', 'Featured badge']],
                    'platinum' => ['name' => 'Platinum', 'price' => 1999, 'features' => ['Unlimited listings', 'Verified badge', 'Top placement']],
                ]
            ]);
        })->name('plans');
        Route::post('/upgrade', [ProfileController::class, 'apiUpgradeSubscription'])->name('upgrade');
        Route::get('/current', [ProfileController::class, 'apiCurrentSubscription'])->name('current');
    });

    // Favorites (Saved properties)
    Route::prefix('/favorites')->name('api.favorites.')->group(function () {
        Route::get('/', [ExploreController::class, 'apiFavorites'])->name('index');
        Route::post('/{propertyId}', [ExploreController::class, 'apiAddFavorite'])->name('add');
        Route::delete('/{propertyId}', [ExploreController::class, 'apiRemoveFavorite'])->name('remove');
    });
});

// In routes/api.php
// Route::prefix('/user')->group(function () {
//     Route::get('/preferences', [UserPreferenceController::class, 'getPreferences']);
//     Route::post('/preferences/track', [UserPreferenceController::class, 'trackPreference']);
//     Route::post('/preferences/clear', [UserPreferenceController::class, 'clearPreferences']);
//     Route::get('/recommendations', [UserPreferenceController::class, 'getRecommendations']);

//     // Saved searches
//     Route::get('/saved-searches', [UserPreferenceController::class, 'getSavedSearches']);
//     Route::post('/saved-searches', [UserPreferenceController::class, 'saveSearch']);
//     Route::delete('/saved-searches/{searchId}', [UserPreferenceController::class, 'deleteSavedSearch']);

//     // Favorite locations
//     Route::post('/favorite-locations', [UserPreferenceController::class, 'saveFavoriteLocation']);

//     // Price range
//     Route::post('/price-range', [UserPreferenceController::class, 'updatePriceRange']);
// });

// Property view tracking
// Route::post('/properties/{propertyId}/track-view', [UserPreferenceController::class, 'trackPropertyView']);

// ============================================================
// FALLBACK ROUTE (404 for API)
// ============================================================
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found.',
    ], 404);
});
