@extends('layouts.app')

@section('title', 'Reset Password - Nestly')

@section('content')
<div style="min-height: 100vh; background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 100%); display: flex; align-items: center; justify-content: center; padding: 20px;">
    <div style="max-width: 420px; width: 100%;">
        
        <div style="text-align: center; margin-bottom: 32px;">
            <div style="font-size: 3rem;">🔐</div>
            <h1 style="color: white; font-size: 1.8rem; margin-top: 8px;">Reset Password</h1>
            <p style="color: #aaa; margin-top: 4px;">We'll send you a link to reset your password</p>
        </div>
        
        <div style="background: rgba(18, 18, 24, 0.95); backdrop-filter: blur(10px); border-radius: 32px; padding: 32px 24px; border: 1px solid rgba(250, 204, 21, 0.2);">
            
            @if (session('status'))
                <div style="background: #1e2a1e; border: 1px solid #4caf50; border-radius: 16px; padding: 12px; margin-bottom: 20px;">
                    <p style="color: #4caf50; font-size: 0.9rem; text-align: center;">{{ session('status') }}</p>
                </div>
            @endif
            
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                
                <div style="margin-bottom: 24px;">
                    <label style="color: #aaa; display: block; margin-bottom: 8px;">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           style="width: 100%; padding: 14px 16px; background: #1e1e2a; border: 1px solid {{ $errors->has('email') ? '#e53935' : '#333' }}; border-radius: 16px; color: white;">
                    @error('email')
                        <p style="color: #e53935; font-size: 0.75rem; margin-top: 6px;">{{ $message }}</p>
                    @enderror
                </div>
                
                <button type="submit" style="width: 100%; background: #facc15; color: #1a1e24; padding: 14px; border-radius: 40px; border: none; font-weight: 600; cursor: pointer;">
                    Send Password Reset Link
                </button>
                
                <div style="text-align: center; margin-top: 24px;">
                    <a href="{{ route('login') }}" style="color: #888; text-decoration: none;">← Back to Login</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection