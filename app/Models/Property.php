<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'properties';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'location',
        'neighborhood',
        'latitude',
        'longitude',
        'bedrooms',
        'bathrooms',
        'area_sqft',
        'price',
        'price_period',
        'main_image',
        'status',
        'is_featured',
        'is_verified',
        'views_count',
        'inquiry_count',
        'expires_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        'views_count' => 'integer',
        'inquiry_count' => 'integer',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_PENDING = 'pending';
    const STATUS_SOLD = 'sold';
    const STATUS_RENTED = 'rented';
    const STATUS_INACTIVE = 'inactive';

    const PRICE_PERIOD_MONTHLY = 'monthly';
    const PRICE_PERIOD_YEARLY = 'yearly';
    const PRICE_PERIOD_SALE = 'sale';

    /**
     * Get the landlord who owns this property
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the gallery images for this property
     */
    public function gallery()
    {
        return $this->hasMany(PropertyGallery::class, 'property_id')->orderBy('order');
    }

    /**
     * Get all conversations about this property
     */
    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'property_id');
    }

    /**
     * Scope for active properties
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for featured properties
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for verified properties
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for properties by price range
     */
    public function scopePriceRange($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    /**
     * Scope for properties by bedrooms
     */
    public function scopeMinBedrooms($query, $bedrooms)
    {
        return $query->where('bedrooms', '>=', $bedrooms);
    }

    /**
     * Get formatted price with currency
     */
    public function getFormattedPriceAttribute()
    {
        return 'KES ' . number_format($this->price);
    }

    /**
     * Get full address string
     */
    public function getFullAddressAttribute()
    {
        $address = $this->location;
        if ($this->neighborhood) {
            $address .= ', ' . $this->neighborhood;
        }
        return $address;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_ACTIVE => 'success',
            self::STATUS_PENDING => 'warning',
            self::STATUS_SOLD => 'danger',
            self::STATUS_RENTED => 'info',
            self::STATUS_INACTIVE => 'secondary',
        ];
        return $badges[$this->status] ?? 'secondary';
    }

    /**
     * Increment view count
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Increment inquiry count
     */
    public function incrementInquiries()
    {
        $this->increment('inquiry_count');
    }
}