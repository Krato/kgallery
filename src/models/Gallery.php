<?php

namespace Infinety\Gallery\Models;

use Illuminate\Database\Eloquent\Model;

use Infinety\Gallery\Events\GalleryEvents;

use Vinkla\Translator\Translatable;
use Vinkla\Translator\Contracts\Translatable as TranslatableContract;

/**
 * Class Gallery.
 */
class Gallery extends Model implements TranslatableContract
{

    use GalleryEvents, Translatable;


    protected $table = 'gallery';
    protected $guarded = ['_token', '_method'];

    protected $translator = 'Infinety\Gallery\Models\GalleryTranslations';
    protected $translatedAttributes = ['title'];

    public function photos()
    {
        return $this->hasMany('Infinety\Gallery\Models\Photos')->orderBy('position');
    }

    public function categories()
    {
        return $this->belongsToMany('Infinety\Gallery\Models\GalleryCategories');
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
