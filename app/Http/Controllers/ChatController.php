<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show chat inbox
     */
    public function inbox()
    {
        $user = Auth::user();
        
        $conversations = Conversation::where('tenant_id', $user->id)
            ->orWhere('landlord_id', $user->id)
            ->with('property', 'tenant', 'landlord', 'lastMessage')
            ->latest('last_message_at')
            ->get()
            ->map(function ($conversation) use ($user) {
                $otherUser = $user->id === $conversation->tenant_id 
                    ? $conversation->landlord 
                    : $conversation->tenant;
                
                return [
                    'id' => $conversation->id,
                    'name' => $otherUser->name,
                    'avatar' => $otherUser->avatar ?? substr($otherUser->name, 0, 1),
                    'property_title' => $conversation->property->title,
                    'last_message' => $conversation->lastMessage->message ?? 'No messages yet',
                    'last_time' => $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : '',
                    'unread' => $user->id === $conversation->tenant_id 
                        ? !$conversation->is_tenant_read 
                        : !$conversation->is_landlord_read,
                ];
            });

        return view('protected.chat.inbox', compact('conversations'));
    }

    /**
     * Show specific conversation
     */
    public function show($userId)
    {
        $user = Auth::user();
        $otherUser = User::findOrFail($userId);
        
        // Find or create conversation
        $conversation = Conversation::where(function ($query) use ($user, $otherUser) {
            $query->where('tenant_id', $user->id)->where('landlord_id', $otherUser->id);
        })->orWhere(function ($query) use ($user, $otherUser) {
            $query->where('tenant_id', $otherUser->id)->where('landlord_id', $user->id);
        })->first();

        if (!$conversation) {
            // Find a property from this landlord to associate the conversation
            $property = Property::where('user_id', $otherUser->id)->first();
            
            if (!$property) {
                return redirect()->route('chat.inbox')->with('error', 'No property found to chat about');
            }
            
            $conversation = Conversation::create([
                'property_id' => $property->id,
                'tenant_id' => $user->role === 'tenant' ? $user->id : $otherUser->id,
                'landlord_id' => $user->role === 'landlord' ? $user->id : $otherUser->id,
                'last_message_at' => now(),
            ]);
        }

        // Mark messages as read
        if ($user->id === $conversation->tenant_id) {
            $conversation->is_tenant_read = true;
        } else {
            $conversation->is_landlord_read = true;
        }
        $conversation->save();
        
        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        $messages = $conversation->messages()->with('sender')->get()->map(function ($message) use ($user) {
            return [
                'id' => $message->id,
                'text' => $message->message,
                'sender' => $message->sender_id === $user->id ? 'user' : 'landlord',
                'time' => $message->created_at->format('H:i'),
            ];
        });

        return view('protected.chat.conversation', [
            'conversationId' => $conversation->id,
            'landlordName' => $otherUser->name,
            'messages' => $messages,
        ]);
    }

    /**
     * Send a message
     */
    public function send(Request $request, $userId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $otherUser = User::findOrFail($userId);

        // Find or create conversation
        $conversation = Conversation::firstOrCreate(
            [
                'tenant_id' => min($user->id, $otherUser->id),
                'landlord_id' => max($user->id, $otherUser->id),
            ],
            [
                'property_id' => Property::where('user_id', $otherUser->id)->first()->id ?? 1,
                'last_message_at' => now(),
            ]
        );

        // Create message
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message' => $request->message,
        ]);

        // Update conversation
        $conversation->last_message_at = now();
        
        if ($user->id === $conversation->tenant_id) {
            $conversation->is_landlord_read = false;
        } else {
            $conversation->is_tenant_read = false;
        }
        $conversation->save();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back();
    }

    /**
     * Mark conversation as read
     */
    public function markAsRead($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        $user = Auth::user();

        if ($user->id === $conversation->tenant_id) {
            $conversation->is_tenant_read = true;
        } else {
            $conversation->is_landlord_read = true;
        }
        $conversation->save();

        return response()->json(['success' => true]);
    }
}