<?php


Route::group(['prefix' => config('gallery.route-prefix'), 'middleware' => config('gallery.middleware'), 'as' => ''], function () {
     // Galleries Routes
    Route::get('/galleries/getData', ['as' => 'galleries-data', 'uses' => 'GalleryController@getData']);
    Route::post('/galleries/upload-photo', ['as' => 'upload-photo', 'uses' => 'GalleryController@uploadPhoto']);
    Route::put('/galleries/info-photo', ['as' => 'info-photo', 'uses' => 'GalleryController@infoPhoto']);
    Route::post('/galleries/reorder', ['as' => 'reorder-photo', 'uses' => 'GalleryController@reorder']);
    Route::post('/galleries/delete-photo', ['as' => 'delete-photo', 'uses' => 'GalleryController@deletePhoto']);
    // Route::resource('/galleries', 'GalleryController', ['as' => 'gallery']);

    Route::group([
        'as' => 'galleries.',
    ], function () {
        Route::get('/galleries', ['as' => 'index', 'uses' => 'GalleryController@index']);
        Route::get('/galleries/{id}/show', ['as' => 'show', 'uses' => 'GalleryController@show']);
        Route::post('/galleries/store', ['as' => 'store', 'uses' => 'GalleryController@store']);
        Route::get('/galleries/create', ['as' => 'create', 'uses' => 'GalleryController@create']);
        Route::delete('/galleries/{id}', ['as' => 'destroy', 'uses' => 'GalleryController@destroy']);
        Route::get('/galleries/{id}/edit', ['as' => 'edit', 'uses' => 'GalleryController@edit']);
        Route::put('/galleries/{id}', ['as' => 'update', 'uses' => 'GalleryController@update']);
    });

    Route::get('/categories/getData', ['as' => 'categories-data', 'uses' => 'CategoryController@getData']);
    Route::group([
        'as' => 'categories.',
    ], function () {
        Route::get('/categories', ['as' => 'index', 'uses' => 'CategoryController@index']);
        Route::post('/categories/store', ['as' => 'store', 'uses' => 'CategoryController@store']);
        Route::get('/categories/create', ['as' => 'create', 'uses' => 'CategoryController@create']);
        Route::delete('/categories/{category}', ['as' => 'destroy', 'uses' => 'CategoryController@destroy']);
        Route::get('/categories/{category}', ['as' => 'show', 'uses' => 'CategoryController@show']);
        Route::get('/categories/{category}/edit', ['as' => 'edit', 'uses' => 'CategoryController@edit']);
        Route::put('/categories/{category}', ['as' => 'update', 'uses' => 'CategoryController@update']);
    });

    // Route::resource('/categories', 'CategoryController', ['as' => '']);
});
