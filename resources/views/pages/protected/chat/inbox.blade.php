@extends('layouts.app')

@section('title', 'Messages - Nestly')

@section('content')
<div style="height: 100vh; background: #0a0a0f; padding-top: 70px;">
    <div class="chatlist-header" style="position: fixed; top: 0; left: 0; right: 0; background: #121218; padding: 16px; z-index: 10;">
        <a href="{{ route('explore') }}" style="color: #facc15; text-decoration: none; font-size: 1.2rem;">← Back</a>
        <span style="color: white; font-size: 1.2rem; margin-left: 12px;">Chats</span>
    </div>
    <div id="chatListContainer" style="margin-top: 70px;">
        @forelse($conversations ?? [] as $conv)
            <a href="{{ route('chat.show', $conv['id']) }}" class="chatlist-item" style="display: flex; align-items: center; gap: 14px; padding: 16px; border-bottom: 1px solid #1a1a22; text-decoration: none;">
                <div class="chatlist-avatar" style="width: 52px; height: 52px; background: #facc15; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">{{ substr($conv['name'], 0, 1) }}</div>
                <div class="chatlist-info" style="flex: 1;">
                    <div class="chatlist-name" style="color: white; font-weight: 600;">{{ $conv['name'] }}</div>
                    <div class="chatlist-preview" style="color: #aaa; font-size: 0.8rem;">{{ $conv['last_message'] ?? 'Tap to start chatting' }}</div>
                </div>
                <div class="chatlist-time" style="color: #666; font-size: 0.7rem;">{{ $conv['last_time'] ?? '' }}</div>
            </a>
        @empty
            <div style="text-align: center; padding: 60px 20px; color: #aaa;">
                <div style="font-size: 3rem; margin-bottom: 16px;">💬</div>
                <h3 style="color: white;">No messages yet</h3>
                <p>Start a conversation with a landlord from the explore page</p>
                <a href="{{ route('explore') }}" style="display: inline-block; margin-top: 20px; background: #facc15; color: #1a1e24; padding: 12px 24px; border-radius: 40px; text-decoration: none;">Browse Properties</a>
            </div>
        @endforelse
    </div>
</div>
@endsection