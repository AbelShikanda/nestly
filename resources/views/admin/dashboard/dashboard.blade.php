@extends('layouts.app')

@section('title', 'Admin Dashboard - Nestly')

@section('content')
<div style="min-height: 100vh; background: #0a0a0f; padding: 80px 20px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h2 style="color: white; margin-bottom: 24px;">Admin Dashboard</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 32px;">
            <div style="background: #121218; border-radius: 20px; padding: 24px; text-align: center; border: 1px solid #1f1f2a;">
                <div style="font-size: 2rem;">👥</div>
                <div style="color: #facc15; font-size: 1.5rem; font-weight: bold;">{{ $userCount ?? 0 }}</div>
                <div style="color: #aaa;">Total Users</div>
            </div>
            <div style="background: #121218; border-radius: 20px; padding: 24px; text-align: center; border: 1px solid #1f1f2a;">
                <div style="font-size: 2rem;">🏠</div>
                <div style="color: #facc15; font-size: 1.5rem; font-weight: bold;">{{ $propertyCount ?? 0 }}</div>
                <div style="color: #aaa;">Total Properties</div>
            </div>
            <div style="background: #121218; border-radius: 20px; padding: 24px; text-align: center; border: 1px solid #1f1f2a;">
                <div style="font-size: 2rem;">💬</div>
                <div style="color: #facc15; font-size: 1.5rem; font-weight: bold;">{{ $messageCount ?? 0 }}</div>
                <div style="color: #aaa;">Messages</div>
            </div>
        </div>
        
        <div style="display: flex; gap: 16px; flex-wrap: wrap;">
            <a href="{{ route('admin.users.index') }}" style="background: #121218; border: 1px solid #1f1f2a; border-radius: 20px; padding: 16px 24px; text-decoration: none; color: white; flex: 1; text-align: center;">Manage Users</a>
            <a href="{{ route('admin.properties.index') }}" style="background: #121218; border: 1px solid #1f1f2a; border-radius: 20px; padding: 16px 24px; text-decoration: none; color: white; flex: 1; text-align: center;">Manage Properties</a>
        </div>
    </div>
</div>
@endsection