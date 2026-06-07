<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyGallery extends Model
{
    use HasFactory;

    protected $table = 'property_gallery';

    protected $fillable = [
        'property_id',
        'image_url',
        'video_url',
        'type',
        'order',
        'caption',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    // Media type constants
    const TYPE_IMAGE = 'image';
    const TYPE_VIDEO = 'video';

    /**
     * Get the property that owns this gallery item
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    /**
     * Get the media URL (image or video)
     */
    public function getMediaUrlAttribute()
    {
        return $this->video_url ?? $this->image_url;
    }

    /**
     * Check if this is an image
     */
    public function isImage()
    {
        return $this->type === self::TYPE_IMAGE;
    }

    /**
     * Check if this is a video
     */
    public function isVideo()
    {
        return $this->type === self::TYPE_VIDEO;
    }
}