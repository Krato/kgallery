<?php

namespace Infinety\Gallery\Events;

use Illuminate\Support\Facades\Storage;
use Infinety\Gallery\Models\Photos;
use Symfony\Component\HttpFoundation\File\File;

trait PhotoTranslatationsEvents
{
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {

            $changed = $model->getDirty();

            if (isset($changed['name'])) {
                if (renamePhoto($model, $changed)) {
                    return true;
                } else {
                    return false;
                }
            }

            return true;

        });

        static::saving(function ($model) {
            $changed = $model->getDirty();
            if (isset($changed['name'])) {
                if (renamePhoto($model, $changed)) {
                    return true;
                } else {
                    return false;
                }
            }
        });

        function renamePhoto($model, $changed)
        {
            $model = Photos::find($model->photo_id);
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
    }
}
