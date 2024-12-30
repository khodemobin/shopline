<?php

namespace Modules\Media\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Media\Database\Factories\MediaFactory;
use Modules\Media\Services\MediaFileService;

class Media extends Model
{
    use HasFactory;

    protected $table = 'medias';

    protected $casts = ['files' => 'json'];

    protected static function booted()
    {
        static::deleting(static function ($media) {
            MediaFileService::delete($media);
        });
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return MediaFactory
     */
    protected static function newFactory()
    {
        return MediaFactory::new();
    }

    /**
     * Get thumb media.
     */
    public function getThumbAttribute()
    {
        return MediaFileService::thumb($this);
    }

    /**
     * Get original media.
     */
    public function getOriginalAttribute()
    {
        return MediaFileService::original($this);
    }
}
