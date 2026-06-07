@extends('layouts.app')

@section('title', 'Manage Users - Admin')

@section('content')
<div style="min-height: 100vh; background: #0a0a0f; padding: 80px 20px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h2 style="color: white; margin-bottom: 24px;">Manage Users</h2>
        
        @foreach($users ?? [] as $user)
            <div style="background: #121218; border-radius: 20px; padding: 16px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #1f1f2a;">
                <div>
                    <div style="color: white; font-weight: 600;">{{ $user['name'] }}</div>
                    <div style="color: #aaa; font-size: 0.8rem;">{{ $user['email'] }} • {{ ucfirst($user['role']) }}</div>
                </div>
                <div style="display: flex; gap: 12px;">
                    <form method="POST" action="{{ route('admin.users.toggle-role', $user['id']) }}" style="display: inline;">
                        @csrf
                        <button type="submit" style="background: #1e1e2a; border: none; color: #facc15; padding: 8px 16px; border-radius: 20px; cursor: pointer;">Toggle Role</button>
                    </form>
                    <form method="POST" action="{{ route('admin.users.destroy', $user['id']) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: none; border: none; color: #e53935; cursor: pointer;">🗑️</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection