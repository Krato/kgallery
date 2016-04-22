<?php

namespace Infinety\Gallery\Events;

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
                if(renamePhoto($model, $changed)){
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


        function renamePhoto($model, $changed){
            $model = Photos::find($model->photos_id);
            $id = $model->gallery->id;
            $path = public_path().'/gallery_assets/galleries/'.$id;

            //Get old file
            $oldPath = $path.'/'.$model->file;
            $file = new File($oldPath);

            //Set the new file with original extension
            $newName = strtolower(str_slug($changed['name']).'_'.str_random(5)).'.'.$file->getExtension();
            $renamed = $path.'/'.$newName;

            //Rename asset
            if (rename($file, $renamed)) {
                $model->file = $newName;
                $model->save();
                return true;
            } else {
                return false;
            }
        }
    }
}
