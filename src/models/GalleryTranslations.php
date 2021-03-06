<?php

namespace Infinety\Gallery\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class GalleryTranslations extends Model
{
    use Sluggable;

    protected $table = 'gallery_translations';
    protected $guarded = ['_token', '_method'];
    protected $fillable = ['title', 'locale'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
        ];
    }
}
