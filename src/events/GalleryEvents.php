<?php

namespace Infinety\Gallery\Events;

use Illuminate\Support\Facades\Storage;

trait GalleryEvents
{
    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {

            $storage = Storage::disk('gallery');
            $path = 'galleries/'.$model->id;
            if (! $storage->exists($path)) {
                $storage->makeDirectory($path);

                return true;
            }

            return false;

        });

        static::updating(function ($model) {
                return true;
//            $changed = $model->getDirty();
//            $original = $model->getOriginal();
//            $storage = Storage::disk('gallery');
//            $existsPath = 'galleries/'.$changed['slug'];
//            if (! $storage->exists($existsPath)) {
//                $oldPath = public_path().'/gallery_assets/galleries/'.$original['slug'];
//                $newPath = public_path().'/gallery_assets/galleries/'.$changed['slug'];
//                if (rename($oldPath, $newPath)) {
//                    $model->setAttribute('slug', $changed['slug']);
//
//                    return true;
//                }
//            }
//
//            return false;

        });

        static::deleting(function ($model) {
            $storage = Storage::disk('gallery');
            $path = 'galleries/'.$model->id;
            if ($storage->exists($path)) {
                $storage->deleteDirectory($path);
            }
        });
    }
}
