@extends('layouts.app')

@section('title', 'Support Tickets - Nestly')

@section('content')
<div style="min-height: 100vh; background: #0a0a0f; padding: 80px 20px;">
    <div style="max-width: 600px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2 style="color: white;">Support Tickets</h2>
            <a href="{{ route('support.create') }}" style="background: #facc15; color: #1a1e24; padding: 10px 20px; border-radius: 40px; text-decoration: none;">+ New Ticket</a>
        </div>
        
        @forelse($tickets ?? [] as $ticket)
            <div style="background: #121218; border-radius: 20px; padding: 16px; margin-bottom: 12px; border: 1px solid #1f1f2a;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: white; font-weight: 600;">#{{ $ticket['id'] }} - {{ $ticket['subject'] }}</span>
                    <span style="color: #facc15; font-size: 0.8rem;">{{ $ticket['status'] }}</span>
                </div>
                <p style="color: #aaa; font-size: 0.9rem;">{{ $ticket['message'] }}</p>
                <div style="margin-top: 12px; font-size: 0.7rem; color: #666;">{{ $ticket['created_at'] }}</div>
            </div>
        @empty
            <div style="text-align: center; padding: 60px 20px; background: #121218; border-radius: 32px;">
                <div style="font-size: 3rem; margin-bottom: 16px;">🎫</div>
                <h3 style="color: white;">No tickets yet</h3>
                <p style="color: #aaa;">Need help? Create a support ticket</p>
                <a href="{{ route('support.create') }}" style="display: inline-block; margin-top: 20px; background: #facc15; color: #1a1e24; padding: 12px 24px; border-radius: 40px; text-decoration: none;">Create Ticket</a>
            </div>
        @endforelse
    </div>
</div>
@endsection