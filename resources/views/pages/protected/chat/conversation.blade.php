@extends('layouts.app')

@section('title', 'Chat with ' . ($landlordName ?? 'Landlord'))

{{-- Override header with empty content to hide it --}}
@section('header')
@endsection

{{-- Override bottom nav with empty content to hide it --}}
@section('bottom-nav')
@endsection

@section('content')
<div class="chat-modal active" style="transform: translateX(0);">
    <div class="chat-header">
        <a href="{{ route('chat.inbox') }}" class="chat-back-btn" style="background: none; border: none; color: #facc15; font-size: 1.6rem; cursor: pointer;">←</a>
        <div class="chat-avatar" style="width: 40px; height: 40px; background: #facc15; border-radius: 50%; display: flex; align-items: center; justify-content: center;">👤</div>
        <div>
            <div class="chat-name" style="font-weight: 600; color: white;">{{ $landlordName ?? 'Landlord' }}</div>
            <div class="chat-status" style="font-size: 0.7rem; color: #aaa;">online</div>
        </div>
    </div>
    <div class="chat-messages" id="chatMessages" style="flex: 1; overflow-y: auto; padding: 16px; background: #0d0d12;">
        @foreach($messages ?? [] as $msg)
            <div class="message {{ $msg['sender'] === 'user' ? 'sent' : 'received' }}" style="max-width: 80%; padding: 10px 14px; border-radius: 20px; margin-bottom: 8px; {{ $msg['sender'] === 'user' ? 'background: #1e2a2a; color: #facc15; align-self: flex-end;' : 'background: #1a1f2a; color: #e0e0e0; align-self: flex-start;' }}">
                {{ $msg['text'] }}
            </div>
        @endforeach
    </div>
    <div class="chat-input-area" style="background: #121218; padding: 12px 16px; display: flex; gap: 12px;">
        <input type="text" class="chat-input" id="chatInput" placeholder="Type a message..." style="flex: 1; background: #1e1e2a; border: none; padding: 10px 16px; border-radius: 30px; color: white;">
        <button class="chat-send-btn" id="chatSendBtn" style="background: #facc15; border: none; width: 44px; height: 44px; border-radius: 50%; cursor: pointer;">📤</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const conversationId = {{ $conversationId ?? 0 }};
    const landlordName = "{{ $landlordName ?? '' }}";
</script>
@endpush