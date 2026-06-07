<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display user's support tickets
     */
    public function index()
    {
        $tickets = SupportTicket::where('user_id', auth()->id())
            ->latest()
            ->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'ticket_id' => $ticket->ticket_id,
                    'subject' => $ticket->subject,
                    'message' => $ticket->message,
                    'status' => ucfirst($ticket->status),
                    'created_at' => $ticket->created_at->format('M d, Y'),
                ];
            });

        return view('protected.support.index', compact('tickets'));
    }

    /**
     * Show create ticket form
     */
    public function create()
    {
        return view('protected.support.create');
    }

    /**
     * Store new support ticket
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        SupportTicket::create([
            'user_id' => auth()->id(),
            'ticket_id' => 'TKT-' . strtoupper(uniqid()),
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'open',
            'category' => $request->category ?? 'other',
            'priority' => 'medium',
        ]);

        return redirect()->route('support.index')->with('success', 'Ticket created successfully!');
    }

    /**
     * Show single ticket
     */
    public function show($id)
    {
        $ticket = SupportTicket::where('user_id', auth()->id())
            ->with('replies.user')
            ->findOrFail($id);
        
        return view('protected.support.show', compact('ticket'));
    }

    /**
     * Reply to ticket
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = SupportTicket::where('user_id', auth()->id())->findOrFail($id);
        
        $ticket->replies()->create([
            'user_id' => auth()->id(),
            'message' => $request->message,
            'is_admin_reply' => false,
        ]);

        $ticket->update(['status' => 'in_progress']);

        return back()->with('success', 'Reply added!');
    }

    /**
     * Admin: View all tickets
     */
    public function adminIndex()
    {
        $this->authorize('admin');
        
        $tickets = SupportTicket::with('user')->latest()->get();
        return view('admin.tickets.index', compact('tickets'));
    }

    /**
     * Admin: View single ticket
     */
    public function adminShow($id)
    {
        $this->authorize('admin');
        
        $ticket = SupportTicket::with('user', 'replies.user')->findOrFail($id);
        return view('admin.tickets.show', compact('ticket'));
    }

    /**
     * Admin: Reply to ticket
     */
    public function adminReply(Request $request, $id)
    {
        $this->authorize('admin');
        
        $request->validate(['message' => 'required|string']);
        
        $ticket = SupportTicket::findOrFail($id);
        
        $ticket->replies()->create([
            'user_id' => auth()->id(),
            'message' => $request->message,
            'is_admin_reply' => true,
        ]);
        
        $ticket->update(['status' => $request->status ?? 'in_progress']);
        
        return back()->with('success', 'Reply sent!');
    }

    /**
     * Admin: Update ticket status
     */
    public function updateStatus(Request $request, $id)
    {
        $this->authorize('admin');
        
        $ticket = SupportTicket::findOrFail($id);
        $ticket->update(['status' => $request->status]);
        
        if ($request->status === 'resolved') {
            $ticket->update(['resolved_at' => now()]);
        }
        
        return back()->with('success', 'Ticket status updated!');
    }
}