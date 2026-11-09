<?php

use App\Http\Controllers\PagesController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PropertyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

/*
 * |--------------------------------------------------------------------------
 * | Web Routes
 * |--------------------------------------------------------------------------
 */
Route::get('/agent', [PagesController::class, 'agent'])->name('agent');
Route::get('/blog', [PagesController::class, 'blog'])->name('blog');
Route::get('/contact', [PagesController::class, 'contact'])->name('contact');
Route::post('/contact', [PagesController::class, 'storeContact'])->name('contact.store');

Route::get('/newsletter/subscribe', [PagesController::class, 'subscribe'])->name('newsletter.subscribe');

Route::get('/properties', [PagesController::class, 'properties'])->name('properties');
Route::get('/property/{property:slug}', [PagesController::class, 'propertyShow'])->name('property.show');
Route::get('/properties/filter', [PagesController::class, 'filterProperties'])->name('properties.filter');

// Admin routes
Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [DashboardController::class, 'userCount'])->name('users');
    Route::get('/properties', [PropertyController::class, 'index'])->name('admin.properties');
    Route::get('/properties/{property}/show', [PropertyController::class, 'show'])->name('admin.properties.show');
    Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('admin.properties.destroy');
    Route::post('/properties/{property}/verify', [PropertyController::class, 'verify'])->name('admin.properties.verify');
    Route::post('/properties/{property}/feature', [PropertyController::class, 'feature'])->name('admin.properties.feature');
    Route::delete('/properties/{property}/image/{image}', [PropertyController::class, 'deleteImage'])->name('admin.properties.delete-image');
});

// Auth routes - Laravel UI handles these
Auth::routes();

// Fallback
Route::redirect('/', '/home');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
