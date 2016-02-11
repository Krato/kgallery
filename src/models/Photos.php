<?php
namespace Infinety\Gallery\Models;

use Illuminate\Database\Eloquent\Model;
use Infinety\Gallery\Events\PhotoEvents;

/**
 * Class Photos
 * @package Infinety\Gallery\Models
 */
class Photos extends Model {

    use PhotoEvents;

    protected $table = 'photo';
    protected $fillable = ['file', 'name', 'description', 'position', 'state', 'gallery_id'];

    public function gallery(){
        return $this->hasOne('Infinety\Gallery\Models\Gallery', 'id', 'gallery_id');
    }


    public function getUrl(){
        $gallery = Gallery::find($this->gallery_id);
        return url('gallery_assets/galleries', [$gallery->slug, $this->file]);
    }

    public function scopeFilterByGallery($query, $id){
        return $query->where('gallery_id', $id);
    }

}