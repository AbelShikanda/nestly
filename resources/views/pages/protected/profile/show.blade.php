@extends('layouts.app')

@section('title', 'My Profile - Nestly')

@section('content')
<div class="profile-modal active" style="visibility: visible; opacity: 1;">
    <div class="profile-card" style="transform: scale(1);">
        <div class="profile-header">
            <div class="profile-avatar">👤</div>
            <div class="profile-info">
                <h3>{{ auth()->user()->name }}</h3>
                <p>{{ auth()->user()->role === 'landlord' ? 'Landlord · Premium ready' : 'Tenant · Looking for home' }}</p>
            </div>
        </div>
        <div class="profile-scroll">
            <div class="section-title">📋 Account Type</div>
            <div style="background:#121218; padding:12px; border-radius:20px; margin-bottom:16px;">
                <span style="color:#aaa">Current plan:</span> 
                <span style="color:#facc15; font-weight:bold">{{ auth()->user()->subscription_plan ?? 'Free' }}</span>
            </div>
            
            <div class="section-title">💎 Subscription Plans</div>
            <div class="plan-card" data-plan="standard" style="background:#121218; border-radius:20px; padding:16px; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <div class="plan-name" style="font-weight:bold; color:white;">Standard</div>
                    <div class="plan-features" style="font-size:0.7rem; color:#888;">✓ 10 active listings · ✓ SMS replies</div>
                </div>
                <div class="plan-price" style="color:#facc15; font-weight:bold;">KES 499/mo</div>
            </div>
            <div class="plan-card" data-plan="gold" style="background:#121218; border-radius:20px; padding:16px; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <div class="plan-name" style="font-weight:bold; color:white;">Gold</div>
                    <div class="plan-features" style="font-size:0.7rem; color:#888;">✓ 25 listings · ✓ Priority support</div>
                </div>
                <div class="plan-price" style="color:#facc15; font-weight:bold;">KES 999/mo</div>
            </div>
            <div class="plan-card" data-plan="platinum" style="background:#121218; border-radius:20px; padding:16px; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <div class="plan-name" style="font-weight:bold; color:white;">Platinum</div>
                    <div class="plan-features" style="font-size:0.7rem; color:#888;">✓ Unlimited · ✓ Verified badge</div>
                </div>
                <div class="plan-price" style="color:#facc15; font-weight:bold;">KES 1,999/mo</div>
            </div>
            
            <div class="section-title">🛠️ Support</div>
            <a href="{{ route('support.create') }}" class="support-ticket-btn" style="background:#1a1f2a; border:1px solid #facc15; border-radius:40px; padding:14px; display:block; text-align:center; color:#facc15; text-decoration:none;">📧 Raise a Support Ticket</a>
            <a href="{{ route('profile.edit') }}" class="close-profile-btn" style="background:#facc15; border:none; padding:14px; border-radius:40px; display:block; text-align:center; text-decoration:none; color:#1a1e24; margin-top:16px; font-weight:bold;">Edit Profile</a>
            <a href="{{ route('explore') }}" class="close-profile-btn" style="background:#1a1f2a; border:1px solid #333; padding:14px; border-radius:40px; display:block; text-align:center; text-decoration:none; color:white; margin-top:12px;">← Back to Explore</a>
        </div>
    </div>
</div>
@endsection