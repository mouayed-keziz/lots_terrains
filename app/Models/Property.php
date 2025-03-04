<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model implements HasMedia
{
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'content',
        'sections',
    ];

    protected $casts = [
        'sections' => 'array',
    ];

    /**
     * Get the submissions for the property.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Get the image URL or placeholder.
     * 
     * @return string
     */
    public function getImageAttribute(): string
    {
        return $this->hasMedia("image") ? $this->getFirstMediaUrl("image") : "https://via.placeholder.com/640x480?text=No+Image";
    }
}
