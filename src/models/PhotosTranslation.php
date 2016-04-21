<?php

namespace Infinety\Gallery\Models;

use Illuminate\Database\Eloquent\Model;
use Infinety\Gallery\Events\PhotoTranslatationsEvents;

class PhotosTranslation extends Model
{
    use PhotoTranslatationsEvents;
}
