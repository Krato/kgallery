<?php

namespace Infinety\Gallery\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Infinety\Gallery\Events\PhotoEvents;
use Vinkla\Translator\Translatable;

/**
 * Class Photos.
 */
class Photos extends Model
{
    use PhotoEvents, Translatable;

    protected $table = 'photo';
    protected $fillable = ['file', 'name', 'description', 'position', 'state', 'gallery_id'];
    protected $translatable = ['name', 'description'];

    /**
     * Get the translations relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations()
    {
        return $this->hasMany(PhotosTranslations::class, 'photo_id', 'id');
    }

    public function gallery()
    {
        return $this->hasOne('Infinety\Gallery\Models\Gallery', 'id', 'gallery_id');
    }

    public function getUrl()
    {
        $gallery = Gallery::find($this->gallery_id);

        return $this->generatePublicUrl($gallery->id.'/'.$this->file);
        //return url($this->hasPrepend().$storagePath, [$gallery->id, $this->file]);
    }

    public function scopeFilterByGallery($query, $id)
    {
        return $query->where('gallery_id', $id);
    }

    private function hasPrepend()
    {
        if (config('gallery.prepend-url') != null) {
            return config('gallery.prepend-url').'/';
        } else {
            return '';
        }
    }

    private function generatePublicUrl($filePath)
    {
        $path = Storage::disk(config('gallery.disk'))->getDriver()->getAdapter()->applyPathPrefix($filePath);

        return asset(str_replace(public_path(), '', $path));
    }
}
