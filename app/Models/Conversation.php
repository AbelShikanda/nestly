<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'conversations';

    protected $fillable = [
        'property_id',
        'tenant_id',
        'landlord_id',
        'last_message_at',
        'is_tenant_read',
        'is_landlord_read',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'is_tenant_read' => 'boolean',
        'is_landlord_read' => 'boolean',
    ];

    /**
     * Get the property being discussed
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    /**
     * Get the tenant user
     */
    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    /**
     * Get the landlord user
     */
    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    /**
     * Get all messages in this conversation
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'conversation_id');
    }

    /**
     * Get the last message in this conversation
     */
    public function lastMessage()
    {
        return $this->hasOne(Message::class, 'conversation_id')->latest();
    }

    /**
     * Get unread count for a specific user
     */
    public function getUnreadCountForUser($userId)
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Mark as read for a specific user
     */
    public function markAsRead($userId)
    {
        if ($userId == $this->tenant_id) {
            $this->update(['is_tenant_read' => true]);
        } elseif ($userId == $this->landlord_id) {
            $this->update(['is_landlord_read' => true]);
        }
        
        $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    /**
     * Check if conversation has unread messages for user
     */
    public function hasUnreadForUser($userId)
    {
        if ($userId == $this->tenant_id) {
            return !$this->is_tenant_read;
        } elseif ($userId == $this->landlord_id) {
            return !$this->is_landlord_read;
        }
        return false;
    }
}