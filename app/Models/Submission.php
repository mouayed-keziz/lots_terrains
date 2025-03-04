<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Submission extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'property_id',
        'answers',
    ];

    protected $casts = [
        'answers' => 'array',
    ];

    /**
     * Register media collections for the submission
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }

    /**
     * Get the property that owns the submission.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the visitor that owns the submission.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the file attachments for a given fileId
     */
    public function getFileByFileId(string $fileId): ?Media
    {
        return $this->getMedia('attachments')
            ->where('custom_properties.fileId', $fileId)
            ->first();
    }

    /**
     * Get all file attachments with their metadata
     */
    public function getFilesWithMetadata(): array
    {
        $files = [];

        foreach ($this->getMedia('attachments') as $media) {
            $files[] = [
                'id' => $media->id,
                'fileId' => $media->getCustomProperty('fileId'),
                'name' => $media->file_name,
                'url' => $media->getUrl(),
                'size' => $media->size,
                'mime_type' => $media->mime_type,
                'fieldLabel' => $media->getCustomProperty('fieldLabel'),
                'fileType' => $media->getCustomProperty('fileType'),
            ];
        }

        return $files;
    }
}
