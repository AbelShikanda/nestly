<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'role',
        'subscription_plan',
        'subscription_expires_at',
        'is_verified',
        'verification_document',
        'company',
        'bio',
        'location',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    /**
     * Properties owned by this user (landlord)
     */
    public function properties()
    {
        return $this->hasMany(Property::class, 'user_id');
    }

    /**
     * Conversations where user is tenant
     */
    public function tenantConversations()
    {
        return $this->hasMany(Conversation::class, 'tenant_id');
    }

    /**
     * Conversations where user is landlord
     */
    public function landlordConversations()
    {
        return $this->hasMany(Conversation::class, 'landlord_id');
    }

    /**
     * All conversations user participates in
     */
    public function conversations()
    {
        return Conversation::where('tenant_id', $this->id)
            ->orWhere('landlord_id', $this->id);
    }

    /**
     * Messages sent by user
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Subscriptions
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'user_id');
    }

    /**
     * Active subscription
     */
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class, 'user_id')
            ->where('is_active', true)
            ->where('expires_at', '>', now());
    }

    /**
     * Support tickets
     */
    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class, 'user_id');
    }

    /**
     * Ticket replies
     */
    public function ticketReplies()
    {
        return $this->hasMany(TicketReply::class, 'user_id');
    }

    /**
     * Favorite properties
     */
    public function favorites()
    {
        return $this->belongsToMany(Property::class, 'user_favorites', 'user_id', 'property_id')
            ->withTimestamps();
    }

    /**
     * Check if user is landlord
     */
    public function isLandlord()
    {
        return $this->role === 'landlord';
    }

    /**
     * Check if user is tenant
     */
    public function isTenant()
    {
        return $this->role === 'tenant';
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Get avatar URL attribute
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?background=facc15&color=1a1e24&name=' . urlencode($this->name);
    }

    /**
     * Get unread messages count
     */
    public function getUnreadMessagesCountAttribute()
    {
        $count = 0;
        $conversations = Conversation::where('tenant_id', $this->id)
            ->orWhere('landlord_id', $this->id)
            ->get();
        
        foreach ($conversations as $conversation) {
            $count += $conversation->getUnreadCountForUser($this->id);
        }
        
        return $count;
    }

    /**
     * Get user's full name
     */
    public function getFullNameAttribute()
    {
        return $this->name;
    }
}