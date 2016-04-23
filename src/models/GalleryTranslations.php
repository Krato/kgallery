<?php

namespace Infinety\Gallery\Models;

use Illuminate\Database\Eloquent\Model;

use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;

class GalleryTranslations extends Model implements SluggableInterface
{
    use SluggableTrait;

    protected $table = 'gallery_translations';
    protected $guarded = ['_token', '_method'];
    protected $fillable = ['title'];

    protected $sluggable = [
        'build_from'    => 'title',
        'save_to'       => 'slug',
        'on_update'     => true,
    ];




}
