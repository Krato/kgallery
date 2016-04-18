<?php

namespace Infinety\Gallery\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;
use Infinety\Gallery\Events\GalleryEvents;

/**
 * Class Gallery.
 */
class Gallery extends Model implements SluggableInterface
{
    protected $table = 'gallery';
    protected $fillable = ['title'];
    protected $guarded = ['_token', '_method'];

    use SluggableTrait;
    use GalleryEvents;

    protected $sluggable = [
        'build_from'    => 'title',
        'save_to'       => 'slug',
        'on_update'     => true,
    ];

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
