<?php

namespace Infinety\Gallery\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Translator\Translatable;

/**
 * Class GalleryCategories.
 */
class GalleryCategories extends Model
{
    use Translatable;

    protected $table = 'gallery_categories';

    /**
     * A list of methods protected from mass assignment.
     *
     * @var string[]
     */
    protected $guarded = ['_token', '_method'];

    protected $fillable = ['title', 'locale'];

    protected $translatable = ['title', 'slug', 'locale'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['translations'];

    /**
     * Get the translations relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations()
    {
        return $this->hasMany(GalleryCategoriesTranslations::class);
    }

    /**
     * [gallery description].
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function gallery()
    {
        return $this->belongsToMany('Infinety\Gallery\Models\Gallery');
    }

    public function getTranslationsAttribute()
    {
        return $this->translations()->get();
    }
}
