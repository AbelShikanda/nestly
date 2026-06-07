@extends('layouts.app')

@section('title', 'Edit Profile - Nestly')

@section('content')
<div style="min-height: 100vh; background: #0a0a0f; padding: 80px 20px;">
    <div style="max-width: 500px; margin: 0 auto; background: #121218; border-radius: 32px; padding: 24px; border: 1px solid rgba(250,204,21,0.2);">
        <h2 style="color: white; margin-bottom: 24px;">Edit Profile</h2>
        
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')
            
            <div style="margin-bottom: 16px;">
                <label style="color: #aaa; display: block; margin-bottom: 8px;">Full Name</label>
                <input type="text" name="name" value="{{ auth()->user()->name }}" style="width: 100%; padding: 12px; background: #1e1e2a; border: 1px solid #333; border-radius: 12px; color: white;">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="color: #aaa; display: block; margin-bottom: 8px;">Email</label>
                <input type="email" name="email" value="{{ auth()->user()->email }}" style="width: 100%; padding: 12px; background: #1e1e2a; border: 1px solid #333; border-radius: 12px; color: white;">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="color: #aaa; display: block; margin-bottom: 8px;">Phone Number</label>
                <input type="tel" name="phone" value="{{ auth()->user()->phone ?? '' }}" style="width: 100%; padding: 12px; background: #1e1e2a; border: 1px solid #333; border-radius: 12px; color: white;">
            </div>
            
            <button type="submit" style="width: 100%; background: #facc15; color: #1a1e24; padding: 14px; border-radius: 40px; border: none; font-weight: 600; cursor: pointer;">Save Changes</button>
            <a href="{{ route('profile.show') }}" style="display: block; text-align: center; margin-top: 16px; color: #aaa; text-decoration: none;">Cancel</a>
        </form>
    </div>
</div>
@endsection