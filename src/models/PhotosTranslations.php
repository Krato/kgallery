<?php

namespace Infinety\Gallery\Models;

use Illuminate\Database\Eloquent\Model;
use Infinety\Gallery\Events\PhotoTranslatationsEvents;

class PhotosTranslations extends Model
{
    use PhotoTranslatationsEvents;

    protected $table = 'photo_translations';

    protected $fillable = ['name', 'description', 'locale'];
}
