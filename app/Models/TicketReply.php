<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    use HasFactory;

    protected $table = 'ticket_replies';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'attachment_url',
        'is_admin_reply',
    ];

    protected $casts = [
        'is_admin_reply' => 'boolean',
    ];

    /**
     * Get the ticket this reply belongs to
     */
    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    /**
     * Get the user who wrote this reply
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Check if reply has attachment
     */
    public function hasAttachment()
    {
        return !is_null($this->attachment_url);
    }

    /**
     * Check if reply is from admin
     */
    public function isFromAdmin()
    {
        return $this->is_admin_reply;
    }

    /**
     * Get sender type (Admin or User)
     */
    public function getSenderTypeAttribute()
    {
        return $this->is_admin_reply ? 'Admin' : 'User';
    }
}