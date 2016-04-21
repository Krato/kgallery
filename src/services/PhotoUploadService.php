<?php

namespace Infinety\Gallery\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Image.
 */
class PhotoUploadService
{
    /**
     * PhotoUploadService constructor.
     */
    public function __construct()
    {
        $this->path = public_path().'/gallery_assets/galleries/';
    }

    public function photoUpload(UploadedFile $file = null, $folder)
    {
        if ($file && substr($file->getMimeType(), 0, 5) == 'image') {
            return $this->upload($file, $folder);
        }
    }

    /**
     * Handles Upload files.
     *
     * @param UploadedFile $file
     * @param $folder
     * @return string
     */
    private function upload(UploadedFile $file, $folder)
    {
        $path = $this->path.$folder.'/';
        $name = md5(uniqid(rand(), 1)).'.'.$file->getClientOriginalExtension();
        if ($file->move($path, $name)) {
            return $name;
        } else {
            return;
        }
    }
}
