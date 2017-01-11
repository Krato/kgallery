<?php

Route::group(['prefix' => config('gallery.route-prefix'), 'middleware' => config('gallery.middleware')], function () {

    // Galleries Routes
    Route::get('/galleries/getData', ['as' => 'galleries-data', 'uses' => 'GalleryController@getData']);
    Route::post('/galleries/upload-photo', ['as' => 'upload-photo', 'uses' => 'GalleryController@uploadPhoto']);
    Route::put('/galleries/info-photo', ['as' => 'info-photo', 'uses' => 'GalleryController@infoPhoto']);
    Route::post('/galleries/reorder', ['as' => 'reorder-photo', 'uses' => 'GalleryController@reorder']);
    Route::post('/galleries/delete-photo', ['as' => 'delete-photo', 'uses' => 'GalleryController@deletePhoto']);
    Route::resource('/galleries', 'GalleryController');

    Route::get('/categories/getData', ['as' => 'categories-data', 'uses' => 'CategoryController@getData']);
    Route::resource('/categories', 'CategoryController');

});
