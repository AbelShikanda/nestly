<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
}