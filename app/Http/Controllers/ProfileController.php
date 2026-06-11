<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show user profile
     */
    public function show()
    {
        $user = Auth::user();
        $subscription = $user->subscriptions()->where('is_active', true)->first();
        $propertiesCount = $user->properties()->count();
        $messagesCount = $user->sentMessages()->count();
        
        return view('protected.profile.show', compact('user', 'subscription', 'propertiesCount', 'messagesCount'));
    }

    /**
     * Show edit profile form
     */
    public function edit()
    {
        $user = Auth::user();
        return view('protected.profile.edit', compact('user'));
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|max:2048',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        // Update basic info
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        // Update password if provided
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }
            $user->password = Hash::make($request->new_password);
        }

        // Update avatar if uploaded
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
    }

    /**
     * Delete user account
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'password' => 'required|current_password',
        ]);
        
        // Soft delete or force delete
        $user->delete();
        
        Auth::logout();
        
        return redirect('/')->with('success', 'Account deleted successfully');
    }

    /**
     * Upgrade subscription
     */
    public function upgradeSubscription(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:standard,gold,platinum',
        ]);

        $user = Auth::user();
        $plans = [
            'standard' => 499,
            'gold' => 999,
            'platinum' => 1999,
        ];

        // Deactivate old subscription
        $user->subscriptions()->update(['is_active' => false]);

        // Create new subscription
        Subscription::create([
            'user_id' => $user->id,
            'plan' => $request->plan,
            'amount' => $plans[$request->plan],
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
            'is_active' => true,
        ]);

        $user->subscription_plan = $request->plan;
        $user->save();

        return back()->with('success', 'Subscription upgraded to ' . ucfirst($request->plan) . ' plan!');
    }

    // ============================================================
    // API METHODS (For AJAX/Frontend integration)
    // ============================================================

    /**
     * API: Get current user profile
     */
    public function apiShow(Request $request)
    {
        $user = $request->user();
        $subscription = $user->activeSubscription()->first();
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
                'role' => $user->role,
                'subscription_plan' => $user->subscription_plan ?? 'free',
                'subscription_expires_at' => $subscription?->expires_at,
                'is_verified' => $user->is_verified,
                'created_at' => $user->created_at,
            ]
        ]);
    }

    /**
     * API: Update user profile
     */
    public function apiUpdate(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:500',
        ]);
        
        $user->update($request->only(['name', 'email', 'phone', 'company', 'bio']));
        
        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'Profile updated successfully',
        ]);
    }

    /**
     * API: Upload user avatar
     */
    public function apiUploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);
        
        $user = $request->user();
        
        // Delete old avatar
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();
        
        return response()->json([
            'success' => true,
            'data' => ['avatar_url' => asset('storage/' . $path)],
            'message' => 'Avatar uploaded successfully',
        ]);
    }

    /**
     * API: Upgrade subscription
     */
    public function apiUpgradeSubscription(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:standard,gold,platinum',
            'payment_method' => 'required|string|in:mpesa,card,bank',
            'transaction_id' => 'required|string',
        ]);
        
        $user = $request->user();
        $plans = [
            'standard' => 499,
            'gold' => 999,
            'platinum' => 1999,
        ];
        
        // Deactivate old subscription
        $user->subscriptions()->update(['is_active' => false]);
        
        // Create new subscription
        Subscription::create([
            'user_id' => $user->id,
            'plan' => $request->plan,
            'amount' => $plans[$request->plan],
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
            'is_active' => true,
        ]);
        
        $user->subscription_plan = $request->plan;
        $user->save();
        
        return response()->json([
            'success' => true,
            'data' => ['plan' => $request->plan],
            'message' => 'Subscription upgraded to ' . ucfirst($request->plan) . ' plan',
        ]);
    }

    /**
     * API: Get current subscription
     */
    public function apiCurrentSubscription(Request $request)
    {
        $user = $request->user();
        $subscription = $user->activeSubscription()->first();
        
        $plans = [
            'free' => ['name' => 'Free', 'price' => 0, 'features' => ['1 listing', '3 photos', 'Basic support']],
            'standard' => ['name' => 'Standard', 'price' => 499, 'features' => ['10 listings', '15 photos', 'SMS replies', 'Analytics']],
            'gold' => ['name' => 'Gold', 'price' => 999, 'features' => ['25 listings', '30 photos', 'Priority support', 'Featured badge']],
            'platinum' => ['name' => 'Platinum', 'price' => 1999, 'features' => ['Unlimited listings', 'Unlimited photos', 'Verified badge', 'Top placement']],
        ];
        
        return response()->json([
            'success' => true,
            'data' => [
                'current_plan' => $user->subscription_plan ?? 'free',
                'plan_details' => $plans[$user->subscription_plan ?? 'free'],
                'subscription' => $subscription,
                'expires_at' => $subscription?->expires_at,
                'days_remaining' => $subscription?->days_remaining ?? 0,
            ],
        ]);
    }

    /**
     * API: Change password
     */
    public function apiChangePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|min:8|confirmed',
        ]);
        
        $user = $request->user();
        $user->password = Hash::make($request->new_password);
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully',
        ]);
    }

    /**
     * API: Delete account
     */
    public function apiDeleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);
        
        $user = $request->user();
        
        // Delete user's properties and related data
        foreach ($user->properties as $property) {
            if ($property->main_image && Storage::disk('public')->exists($property->main_image)) {
                Storage::disk('public')->delete($property->main_image);
            }
            foreach ($property->gallery as $image) {
                if (Storage::disk('public')->exists($image->image_url)) {
                    Storage::disk('public')->delete($image->image_url);
                }
            }
        }
        
        $user->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Account deleted successfully',
        ]);
    }
}