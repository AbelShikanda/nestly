@extends('layouts.app')

@section('title', 'Welcome to Nestly - Kenya Real Estate')

@section('content')
<div style="min-height: 100vh; background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 100%); display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 40px 20px;">
    <div style="text-align: center; max-width: 600px;">
        <h1 style="font-size: 3rem; margin-bottom: 20px; background: linear-gradient(135deg, #facc15, #ff8c00); -webkit-background-clip: text; background-clip: text; color: transparent;">🏠 Nestly</h1>
        <p style="font-size: 1.2rem; color: #aaa; margin-bottom: 30px;">Find your dream home in Kenya. Swipe, match, move in.</p>
        <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('explore') }}" style="background: #facc15; color: #1a1e24; padding: 14px 32px; border-radius: 50px; text-decoration: none; font-weight: 600;">Start Exploring →</a>
            <a href="{{ route('login') }}" style="border: 1px solid #facc15; color: #facc15; padding: 14px 32px; border-radius: 50px; text-decoration: none; font-weight: 600;">Login</a>
            <a href="{{ route('register') }}" style="border: 1px solid #aaa; color: #aaa; padding: 14px 32px; border-radius: 50px; text-decoration: none; font-weight: 600;">Register</a>
        </div>
    </div>
    
    <div style="display: flex; justify-content: center; gap: 40px; flex-wrap: wrap; margin-top: 80px; max-width: 1000px;">
        <div style="text-align: center; flex: 1; min-width: 200px;">
            <div style="font-size: 2.5rem; margin-bottom: 16px;">📱</div>
            <h3 style="color: white;">Swipe to Discover</h3>
            <p style="color: #888;">TikTok-style property browsing</p>
        </div>
        <div style="text-align: center; flex: 1; min-width: 200px;">
            <div style="font-size: 2.5rem; margin-bottom: 16px;">💬</div>
            <h3 style="color: white;">Chat with Landlords</h3>
            <p style="color: #888;">Direct messaging, no phone numbers needed</p>
        </div>
        <div style="text-align: center; flex: 1; min-width: 200px;">
            <div style="font-size: 2.5rem; margin-bottom: 16px;">✓</div>
            <h3 style="color: white;">Verified Listings</h3>
            <p style="color: #888;">Real properties, real landlords</p>
        </div>
    </div>
    
    <footer style="margin-top: 80px; text-align: center; color: #666; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 40px; width: 100%;">
        <p>© {{ date('Y') }} Nestly - Kenya's smartest way to find a home</p>
    </footer>
</div>
@endsection
