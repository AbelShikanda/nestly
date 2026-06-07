@extends('layouts.app')

@section('title', 'Manage Properties - Admin')

@section('content')
<div style="min-height: 100vh; background: #0a0a0f; padding: 80px 20px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h2 style="color: white; margin-bottom: 24px;">Manage Properties</h2>
        
        @foreach($properties ?? [] as $property)
            <div style="background: #121218; border-radius: 20px; padding: 16px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #1f1f2a;">
                <div>
                    <div style="color: white; font-weight: 600;">{{ $property['title'] }}</div>
                    <div style="color: #aaa; font-size: 0.8rem;">{{ $property['location'] }} • {{ $property['priceFormatted'] }}</div>
                </div>
                <div>
                    <form method="POST" action="{{ route('admin.properties.destroy', $property['id']) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: none; border: none; color: #e53935; cursor: pointer;">🗑️ Delete</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection