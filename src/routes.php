<?php

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {

    Route::controller('/galleries', 'infinety\gallery\Controllers\GalleryController');
    Route::controller('/k_categories', 'infinety\gallery\Controllers\CategoryController');
});
