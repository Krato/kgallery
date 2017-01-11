<?php

namespace Infinety\Gallery\Models;

use Illuminate\Database\Eloquent\Model;
use Infinety\Gallery\Events\GalleryEvents;
use Vinkla\Translator\Translatable;

/**
 * Class Gallery.
 */
class Gallery extends Model
{
    use GalleryEvents, Translatable;

    protected $table = 'gallery';
    protected $guarded = ['_token', '_method'];

    protected $translatable = ['title', 'slug'];

    /**
     * Get the translations relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations()
    {
        return $this->hasMany(GalleryTranslations::class);
    }

    public function photos()
    {
        return $this->hasMany('Infinety\Gallery\Models\Photos')->orderBy('position');
    }

    public function categories()
    {
        return $this->belongsToMany(GalleryCategories::class);
    }

    public function getPrincipalPhoto()
    {
        $principal = $this->photos()->where('position', 0)->first();
        if ($principal) {
            return asset($principal->getUrl());
        } else {
            return asset('/assets/img/no-image.jpg');
        }
    }
}
