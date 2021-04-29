<?php

Route::group(['middleware' => 'voyager.admin'], static function () {
    Route::get('/', ['uses' => 'VoyagerController@dashboard', 'as' => 'dashboard']);
    Route::post('globalsearch', ['uses' => 'VoyagerController@globalSearch', 'as' => 'globalsearch']);

    // BREAD builder
    Route::group([
        'as'     => 'bread.',
        'prefix' => 'bread',
    ], static function () {
        Route::get('/', ['uses' => 'BreadBuilderController@index', 'as' => 'index']);
        Route::get('create/{table}', ['uses' => 'BreadBuilderController@create', 'as' => 'create']);
        Route::get('edit/{table}', ['uses' => 'BreadBuilderController@edit', 'as' => 'edit']);
        Route::put('{table}', ['uses' => 'BreadBuilderController@update', 'as' => 'update']);
        Route::post('get-properties', ['uses' => 'BreadBuilderController@getProperties', 'as' => 'get-properties']);
        Route::post('get-breads', ['uses' => 'BreadBuilderController@getBreads', 'as' => 'get-breads']);
        Route::post('backup-bread', ['uses' => 'BreadBuilderController@backupBread', 'as' => 'backup-bread']);
        Route::post('rolback-bread', ['uses' => 'BreadBuilderController@rollbackBread', 'as' => 'rollback-bread']);
        Route::delete('{table}', ['uses' => 'BreadBuilderController@destroy', 'as' => 'delete']);
    });

    // UI Route
    Route::get('ui', ['uses' => 'VoyagerController@ui', 'as' => 'ui']);

    // Settings
    Route::get('settings', ['uses' => 'SettingsController@index', 'as' => 'settings.index']);
    Route::post('settings', ['uses' => 'SettingsController@store', 'as' => 'settings.store']);

    // Plugins
    Route::get('plugins', ['uses' => 'PluginsController@index', 'as' => 'plugins.index']);
    Route::post('plugins/enable', ['uses' => 'PluginsController@enable', 'as' => 'plugins.enable']);

    // Logout
    Route::get('logout', ['uses' => 'AuthController@logout', 'as' => 'logout']);

    // Media
    Route::get('media', ['uses' => 'MediaController@index', 'as' => 'media']);
    Route::post('upload', ['uses' => 'MediaController@uploadFile', 'as' => 'media.upload']);
    Route::post('download', ['uses' => 'MediaController@download', 'as' => 'media.download']);
    Route::post('list', ['uses' => 'MediaController@listFiles', 'as' => 'media.list']);
    Route::delete('delete', ['uses' => 'MediaController@delete', 'as' => 'media.delete']);
    Route::post('create_folder', ['uses' => 'MediaController@createFolder', 'as' => 'media.create_folder']);

    //
    Route::post('get-disks', ['uses' => 'VoyagerController@getDisks', 'as' => 'get-disks']);
    Route::post('get-thumbnail-options', ['uses' => 'VoyagerController@getThumbnailOptions', 'as' => 'get-thumbnail-options']);
});

// Login
Route::get('login', ['uses' => 'AuthController@login', 'as' => 'login']);
Route::post('login', ['uses' => 'AuthController@login']);
Route::post('forgot-password', ['uses' => 'AuthController@forgotPassword', 'as' => 'forgot_password']);

// Asset routes
Route::get('voyager-assets', ['uses' => 'VoyagerController@assets', 'as' => 'voyager_assets']);