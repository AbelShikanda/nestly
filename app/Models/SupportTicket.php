<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $table = 'support_tickets';

    protected $fillable = [
        'user_id',
        'ticket_id',
        'subject',
        'category',
        'priority',
        'message',
        'status',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    // Status constants
    const STATUS_OPEN = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';

    // Category constants
    const CATEGORY_TECHNICAL = 'technical';
    const CATEGORY_BILLING = 'billing';
    const CATEGORY_PROPERTY = 'property';
    const CATEGORY_ACCOUNT = 'account';
    const CATEGORY_OTHER = 'other';

    // Priority constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    /**
     * Get the user who created this ticket
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all replies for this ticket
     */
    public function replies()
    {
        return $this->hasMany(TicketReply::class, 'ticket_id');
    }

    /**
     * Get the latest reply
     */
    public function latestReply()
    {
        return $this->hasOne(TicketReply::class, 'ticket_id')->latest();
    }

    /**
     * Generate unique ticket ID
     */
    public static function generateTicketId()
    {
        return 'TKT-' . strtoupper(uniqid());
    }

    /**
     * Check if ticket is open
     */
    public function isOpen()
    {
        return in_array($this->status, [self::STATUS_OPEN, self::STATUS_IN_PROGRESS]);
    }

    /**
     * Mark ticket as resolved
     */
    public function markAsResolved()
    {
        $this->update([
            'status' => self::STATUS_RESOLVED,
            'resolved_at' => now(),
        ]);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_OPEN => 'warning',
            self::STATUS_IN_PROGRESS => 'info',
            self::STATUS_RESOLVED => 'success',
            self::STATUS_CLOSED => 'secondary',
        ];
        return $badges[$this->status] ?? 'secondary';
    }

    /**
     * Get priority badge class
     */
    public function getPriorityBadgeAttribute()
    {
        $badges = [
            self::PRIORITY_LOW => 'secondary',
            self::PRIORITY_MEDIUM => 'primary',
            self::PRIORITY_HIGH => 'warning',
            self::PRIORITY_URGENT => 'danger',
        ];
        return $badges[$this->priority] ?? 'secondary';
    }
}