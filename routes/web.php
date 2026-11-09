<?php

use App\Http\Controllers\PagesController;
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

// Auth routes - Laravel UI handles these
Auth::routes();

// Fallback
Route::redirect('/', '/home');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
