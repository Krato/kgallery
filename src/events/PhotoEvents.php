<?php

namespace Infinety\Gallery\Events;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\File;

trait PhotoEvents
{
    protected static function boot()
    {
        parent::boot();

        /**
         * Rename file after rename it on model.
         */
        static::updating(function ($model) {

            $changed = $model->getDirty();
            if (isset($changed['name'])) {
                $id = $model->gallery->id;
                $path = $id;

                $storage = Storage::disk(config('gallery.disk'));

                //Get old file
                $oldPath = $path.'/'.$model->file;
                $ext = pathinfo($model->file, PATHINFO_EXTENSION);

                //Set the new file with original extension
                $newName = strtolower(str_slug($changed['name']).'_'.str_random(5)).'.'.$ext;
                $renamed = $path.'/'.$newName;

                //Rename asset
                if ($storage->move($oldPath, $renamed)) {
                    $model->file = $newName;
                    $model->save();

                    return true;
                } else {
                    return false;
                }
            }

            return true;

        });

        /**
         * Remove file when photo is going to be deleted.
         */
        static::deleting(function ($model) {
            $id = $model->gallery->id;
            $storage = Storage::disk(config('gallery.disk'));

            $path = $id.'/'.$model->file;
            $storage->delete($path);

            return true;
        });

        /**
         * Once deleted reorder images of gallery.
         */
        static::deleted(function ($model) {
            reorderPhotos($model->gallery);
        });

        /**
         * Reorders gallery photos.
         *
         * @param Gallery $gallery
         */
        function reorderPhotos($gallery)
        {
            $x = 0;
            foreach ($gallery->photos as $photo) {
                Log::info($photo);
                $photo->position = $x;
                $photo->save();
                ++$x;
            }
        }
    }
}
