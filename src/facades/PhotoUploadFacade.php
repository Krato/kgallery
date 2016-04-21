<?php

namespace Infinety\Gallery\Facades;

use Illuminate\Support\Facades\Facade;
use Infinety\Gallery\Services\PhotoUploadService;

class PhotoUploadFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return new PhotoUploadService();
    }
}
