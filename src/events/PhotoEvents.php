<?php

namespace Infinety\Gallery\Events;

use Symfony\Component\HttpFoundation\File\File;

trait PhotoEvents
{
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {

            $changed = $model->getDirty();
            if (isset($changed['name'])) {
                $id = $model->gallery->id;
                $path = public_path().'/gallery_assets/galleries/'.$id;
                //Get old file
                $oldPath = $path.'/'.$model->file;
                $file = new File($oldPath);

                //Set the new file with original extension
                $newName = strtolower(str_slug($model->name).'_'.str_random(5)).'.'.$file->getExtension();
                $renamed = $path.'/'.$newName;

                //Rename asset
                if (rename($file, $renamed)) {
                    $model->setAttribute('file', $newName);

                    return true;
                } else {
                    return false;
                }
            }

            return true;

        });

        static::deleting(function ($model) {
            $id = $model->gallery->id;
            $path = public_path().'/gallery_assets/galleries/'.$id;
            $oldPath = $path.'/'.$model->file;
            $file = new File($oldPath);
            @unlink($file); //@ to prevent errors
            return true;
        });
    }
}
