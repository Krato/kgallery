<?php

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {

    Route::controller('/galleries', 'GalleryController');
    Route::controller('/k_categories', 'CategoryController');
});
