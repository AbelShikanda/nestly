@extends('layouts.app')

@section('title', 'Register - Nestly')

@section('content')
<div style="min-height: 100vh; background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 100%); display: flex; align-items: center; justify-content: center; padding: 20px;">
    <div style="max-width: 480px; width: 100%;">
        
        {{-- Logo / Brand --}}
        <div style="text-align: center; margin-bottom: 32px;">
            <div style="font-size: 3rem;">🏠</div>
            <h1 style="color: white; font-size: 1.8rem; margin-top: 8px;">Nestly</h1>
            <p style="color: #aaa; margin-top: 4px;">Create your account</p>
        </div>
        
        {{-- Register Card --}}
        <div style="background: rgba(18, 18, 24, 0.95); backdrop-filter: blur(10px); border-radius: 32px; padding: 32px 24px; border: 1px solid rgba(250, 204, 21, 0.2);">
            
            <h2 style="color: white; font-size: 1.5rem; margin-bottom: 24px;">Join Nestly</h2>
            
            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                {{-- Name --}}
                <div style="margin-bottom: 16px;">
                    <label style="color: #aaa; display: block; margin-bottom: 8px;">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                           style="width: 100%; padding: 14px 16px; background: #1e1e2a; border: 1px solid {{ $errors->has('name') ? '#e53935' : '#333' }}; border-radius: 16px; color: white;">
                    @error('name')
                        <p style="color: #e53935; font-size: 0.75rem; margin-top: 6px;">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- Email --}}
                <div style="margin-bottom: 16px;">
                    <label style="color: #aaa; display: block; margin-bottom: 8px;">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           style="width: 100%; padding: 14px 16px; background: #1e1e2a; border: 1px solid {{ $errors->has('email') ? '#e53935' : '#333' }}; border-radius: 16px; color: white;">
                    @error('email')
                        <p style="color: #e53935; font-size: 0.75rem; margin-top: 6px;">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- Phone --}}
                <div style="margin-bottom: 16px;">
                    <label style="color: #aaa; display: block; margin-bottom: 8px;">Phone Number (Optional)</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                           style="width: 100%; padding: 14px 16px; background: #1e1e2a; border: 1px solid #333; border-radius: 16px; color: white;">
                </div>
                
                {{-- Role Selection --}}
                <div style="margin-bottom: 16px;">
                    <label style="color: #aaa; display: block; margin-bottom: 8px;">I am a...</label>
                    <div style="display: flex; gap: 16px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="role" value="tenant" checked style="accent-color: #facc15;">
                            <span style="color: white;">Tenant</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="role" value="landlord" style="accent-color: #facc15;">
                            <span style="color: white;">Landlord</span>
                        </label>
                    </div>
                </div>
                
                {{-- Password --}}
                <div style="margin-bottom: 16px;">
                    <label style="color: #aaa; display: block; margin-bottom: 8px;">Password</label>
                    <input type="password" name="password" required
                           style="width: 100%; padding: 14px 16px; background: #1e1e2a; border: 1px solid {{ $errors->has('password') ? '#e53935' : '#333' }}; border-radius: 16px; color: white;">
                    @error('password')
                        <p style="color: #e53935; font-size: 0.75rem; margin-top: 6px;">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- Confirm Password --}}
                <div style="margin-bottom: 24px;">
                    <label style="color: #aaa; display: block; margin-bottom: 8px;">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                           style="width: 100%; padding: 14px 16px; background: #1e1e2a; border: 1px solid #333; border-radius: 16px; color: white;">
                </div>
                
                {{-- Submit Button --}}
                <button type="submit" style="width: 100%; background: #facc15; color: #1a1e24; padding: 14px; border-radius: 40px; border: none; font-weight: 600; font-size: 1rem; cursor: pointer;">
                    Register
                </button>
                
                {{-- Login Link --}}
                <div style="text-align: center; margin-top: 24px;">
                    <p style="color: #aaa;">Already have an account? 
                        <a href="{{ route('login') }}" style="color: #facc15; text-decoration: none;">Login here</a>
                    </p>
                </div>
            </form>
        </div>
        
        <div style="text-align: center; margin-top: 24px;">
            <a href="{{ route('explore') }}" style="color: #888; text-decoration: none; font-size: 0.85rem;">← Back to Explore</a>
        </div>
    </div>
</div>
@endsection