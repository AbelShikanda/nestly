<?php

use App\Http\Controllers\ExploreController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PropertyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ========== PUBLIC ROUTES (No Login Required) ==========
Route::get('/', [ExploreController::class, 'welcome'])->name('welcome');
Route::get('/explore', [ExploreController::class, 'index'])->name('explore');
Route::get('/property/{id}', [ExploreController::class, 'show'])->name('property.show');
Route::get('/search', [ExploreController::class, 'search'])->name('search');

// ========== AUTH ROUTES (Laravel UI) ==========
Auth::routes(['verify' => true]); // Enable email verification

// ========== EMAIL VERIFICATION ROUTES (Custom) ==========
Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

// ========== PROTECTED ROUTES (Login Required) ==========
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard / Home after login
    Route::get('/dashboard', [ExploreController::class, 'index'])->name('dashboard');
    
    // Chat System
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'inbox'])->name('inbox');
        Route::get('/{user}', [ChatController::class, 'show'])->name('show');
        Route::post('/{user}', [ChatController::class, 'send'])->name('send');
        Route::post('/{conversation}/mark-read', [ChatController::class, 'markAsRead'])->name('mark-read');
    });
    
    // Profile Management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
    
    // Support Tickets
    Route::resource('/support', SupportController::class);
    
    // ========== LANDLORD ONLY ROUTES ==========
    Route::middleware(['role:landlord'])->prefix('landlord')->name('landlord.')->group(function () {
        Route::resource('/listings', ListingController::class);
        Route::get('/analytics', [ListingController::class, 'analytics'])->name('analytics');
        Route::post('/listings/{listing}/feature', [ListingController::class, 'feature'])->name('listings.feature');
    });
    
    // ========== ADMIN ONLY ROUTES ==========
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/stats', [DashboardController::class, 'stats'])->name('stats');
        
        // User Management
        Route::resource('/users', UserController::class);
        Route::post('/users/{user}/toggle-role', [UserController::class, 'toggleRole'])->name('users.toggle-role');
        Route::post('/users/{user}/verify', [UserController::class, 'verify'])->name('users.verify');
        Route::post('/users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
        
        // Property Management
        Route::resource('/properties', PropertyController::class);
        Route::post('/properties/{property}/verify', [PropertyController::class, 'verify'])->name('properties.verify');
        Route::post('/properties/{property}/feature', [PropertyController::class, 'feature'])->name('properties.feature');
        Route::delete('/properties/{property}/image/{image}', [PropertyController::class, 'deleteImage'])->name('properties.delete-image');
        
        // Support Tickets Management
        Route::get('/tickets', [SupportController::class, 'adminIndex'])->name('tickets.index');
        Route::get('/tickets/{ticket}', [SupportController::class, 'adminShow'])->name('tickets.show');
        Route::post('/tickets/{ticket}/reply', [SupportController::class, 'adminReply'])->name('tickets.reply');
        Route::put('/tickets/{ticket}/status', [SupportController::class, 'updateStatus'])->name('tickets.status');
        
        // Reports
        Route::get('/reports/users', [DashboardController::class, 'userReport'])->name('reports.users');
        Route::get('/reports/properties', [DashboardController::class, 'propertyReport'])->name('reports.properties');
        Route::get('/reports/payments', [DashboardController::class, 'paymentReport'])->name('reports.payments');
    });
});

// ========== GUEST REDIRECT (If trying to access protected routes) ==========
Route::get('/login-redirect', function () {
    return redirect()->route('login')->with('warning', 'Please login to access that page.');
})->name('login.redirect');

// ========== FALLBACK ROUTE (404 handling) ==========
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});