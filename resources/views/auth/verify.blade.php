@extends('layouts.app')

@section('title', 'Verify Email - Nestly')

@section('content')
<div style="min-height: 100vh; background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 100%); display: flex; align-items: center; justify-content: center; padding: 20px;">
    <div style="max-width: 480px; width: 100%;">
        
        <div style="text-align: center; margin-bottom: 32px;">
            <div style="font-size: 3rem;">📧</div>
            <h1 style="color: white; font-size: 1.8rem; margin-top: 8px;">Verify Your Email</h1>
        </div>
        
        <div style="background: rgba(18, 18, 24, 0.95); backdrop-filter: blur(10px); border-radius: 32px; padding: 32px 24px; border: 1px solid rgba(250, 204, 21, 0.2); text-align: center;">
            
            <p style="color: white; margin-bottom: 16px;">A verification link has been sent to your email address.</p>
            <p style="color: #aaa; margin-bottom: 24px; font-size: 0.9rem;">Please check your inbox and click the link to verify your account.</p>
            
            @if (session('resent'))
                <p style="color: #4caf50; margin-bottom: 16px;">✓ A fresh verification link has been sent!</p>
            @endif
            
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" style="background: #facc15; color: #1a1e24; padding: 12px 24px; border-radius: 40px; border: none; font-weight: 600; cursor: pointer;">
                    Resend Verification Email
                </button>
            </form>
            
            <div style="margin-top: 24px;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: #888; cursor: pointer;">← Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection