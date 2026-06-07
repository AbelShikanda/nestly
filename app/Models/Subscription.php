<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id',
        'plan',
        'amount',
        'payment_method',
        'transaction_id',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Plan constants
    const PLAN_FREE = 'free';
    const PLAN_STANDARD = 'standard';
    const PLAN_GOLD = 'gold';
    const PLAN_PLATINUM = 'platinum';

    // Plan prices in KES
    const PLAN_PRICES = [
        self::PLAN_FREE => 0,
        self::PLAN_STANDARD => 499,
        self::PLAN_GOLD => 999,
        self::PLAN_PLATINUM => 1999,
    ];

    // Plan features
    const PLAN_FEATURES = [
        self::PLAN_FREE => [
            'listings' => 1,
            'photos' => 3,
            'sms_replies' => false,
            'featured_badge' => false,
            'analytics' => false,
        ],
        self::PLAN_STANDARD => [
            'listings' => 10,
            'photos' => 15,
            'sms_replies' => true,
            'featured_badge' => false,
            'analytics' => true,
        ],
        self::PLAN_GOLD => [
            'listings' => 25,
            'photos' => 30,
            'sms_replies' => true,
            'featured_badge' => true,
            'analytics' => true,
        ],
        self::PLAN_PLATINUM => [
            'listings' => -1, // Unlimited
            'photos' => -1,   // Unlimited
            'sms_replies' => true,
            'featured_badge' => true,
            'analytics' => true,
        ],
    ];

    /**
     * Get the user who owns this subscription
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Check if subscription is active and not expired
     */
    public function isValid()
    {
        return $this->is_active && $this->expires_at->isFuture();
    }

    /**
     * Check if subscription has expired
     */
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    /**
     * Get days remaining
     */
    public function getDaysRemainingAttribute()
    {
        if ($this->expires_at->isPast()) {
            return 0;
        }
        return now()->diffInDays($this->expires_at);
    }

    /**
     * Get plan features
     */
    public function getFeaturesAttribute()
    {
        return self::PLAN_FEATURES[$this->plan] ?? self::PLAN_FEATURES[self::PLAN_FREE];
    }

    /**
     * Get max listings allowed
     */
    public function getMaxListingsAttribute()
    {
        $features = $this->getFeaturesAttribute();
        return $features['listings'];
    }

    /**
     * Check if plan allows unlimited listings
     */
    public function hasUnlimitedListings()
    {
        return $this->getMaxListingsAttribute() === -1;
    }
}