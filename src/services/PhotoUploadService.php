<?php

namespace Infinety\Gallery\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Class Image.
 */
class PhotoUploadService
{
    protected $storage;
    /**
     * PhotoUploadService constructor.
     */
    public function __construct()
    {
        $this->storage = Storage::disk(config('gallery.disk'));
    }

    public function photoUpload(UploadedFile $file, $folder)
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
     *
     * @return string
     */
    private function upload(UploadedFile $file, $folder)
    {
        $name = md5(uniqid(rand(), 1)).'.'.$file->getClientOriginalExtension();

        $this->storage->putFileAs($folder, $file, $name);

        return $name;
    }
}
