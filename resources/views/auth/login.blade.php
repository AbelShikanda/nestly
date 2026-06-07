@extends('layouts.app')

@section('title', 'Login - Nestly')

@section('content')
<div style="min-height: 100vh; background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 100%); display: flex; align-items: center; justify-content: center; padding: 20px;">
    <div style="max-width: 420px; width: 100%;">
        
        {{-- Logo / Brand --}}
        <div style="text-align: center; margin-bottom: 32px;">
            <div style="font-size: 3rem;">🏠</div>
            <h1 style="color: white; font-size: 1.8rem; margin-top: 8px;">Nestly</h1>
            <p style="color: #aaa; margin-top: 4px;">Welcome back</p>
        </div>
        
        {{-- Login Card --}}
        <div style="background: rgba(18, 18, 24, 0.95); backdrop-filter: blur(10px); border-radius: 32px; padding: 32px 24px; border: 1px solid rgba(250, 204, 21, 0.2); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
            
            <h2 style="color: white; font-size: 1.5rem; margin-bottom: 24px;">Login to your account</h2>
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                {{-- Email --}}
                <div style="margin-bottom: 20px;">
                    <label style="color: #aaa; display: block; margin-bottom: 8px; font-size: 0.9rem;">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           style="width: 100%; padding: 14px 16px; background: #1e1e2a; border: 1px solid {{ $errors->has('email') ? '#e53935' : '#333' }}; border-radius: 16px; color: white; font-size: 1rem; outline: none; transition: all 0.2s;">
                    @error('email')
                        <p style="color: #e53935; font-size: 0.75rem; margin-top: 6px;">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- Password --}}
                <div style="margin-bottom: 20px;">
                    <label style="color: #aaa; display: block; margin-bottom: 8px; font-size: 0.9rem;">Password</label>
                    <input type="password" name="password" required
                           style="width: 100%; padding: 14px 16px; background: #1e1e2a; border: 1px solid {{ $errors->has('password') ? '#e53935' : '#333' }}; border-radius: 16px; color: white; font-size: 1rem; outline: none;">
                    @error('password')
                        <p style="color: #e53935; font-size: 0.75rem; margin-top: 6px;">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- Remember Me & Forgot Password --}}
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="remember" style="width: 18px; height: 18px; accent-color: #facc15;">
                        <span style="color: #aaa; font-size: 0.85rem;">Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" style="color: #facc15; text-decoration: none; font-size: 0.85rem;">Forgot Password?</a>
                </div>
                
                {{-- Submit Button --}}
                <button type="submit" style="width: 100%; background: #facc15; color: #1a1e24; padding: 14px; border-radius: 40px; border: none; font-weight: 600; font-size: 1rem; cursor: pointer; transition: 0.2s;">
                    Login
                </button>
                
                {{-- Register Link --}}
                <div style="text-align: center; margin-top: 24px;">
                    <p style="color: #aaa;">Don't have an account? 
                        <a href="{{ route('register') }}" style="color: #facc15; text-decoration: none; font-weight: 500;">Register here</a>
                    </p>
                </div>
            </form>
        </div>
        
        {{-- Back to Explore --}}
        <div style="text-align: center; margin-top: 24px;">
            <a href="{{ route('explore') }}" style="color: #888; text-decoration: none; font-size: 0.85rem;">← Back to Explore</a>
        </div>
    </div>
</div>
@endsection