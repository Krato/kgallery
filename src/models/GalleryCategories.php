<?php
namespace Infinety\Gallery\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;

/**
 * Class GalleryCategories
 * @package Infinety\Gallery\Models
 */
class GalleryCategories extends Model implements SluggableInterface{

    use SluggableTrait;

    protected $sluggable = [
        'build_from'    => 'title',
        'save_to'       => 'slug',
        'on_update'     => true,
    ];

    protected $table = 'gallery_categories';

    protected $fillable = ['title'];

    public function gallery(){
        return $this->belongsToMany('Infinety\Gallery\Models\Gallery');
    }

}