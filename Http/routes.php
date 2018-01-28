<?php

Route::group([
    'middleware' => ['web', 'auth.admin'],
    'prefix'     => 'admin/forum',
    'as'         => 'forum::admin.',
    'namespace'  => 'Modules\Forum\Http\Controllers',
], function () {

    // Report routes.
    Route::get('reports', [
        'as'   => 'reports.index',
        'uses' => 'ForumReportsController@index',
    ]);

    // Management routes.
    Route::get('management/{category?}', [
        'as'   => 'management.index',
        'uses' => 'ForumManagementController@index',
    ]);

    Route::get('management/{category?}/paginate', [
        'as'   => 'management.paginate',
        'uses' => 'ForumManagementController@paginate',
    ]);

    Route::post('management/threads/{deletedThread}/toggle-state', [
        'as'   => 'management.threads.toggle-state',
        'uses' => 'ForumThreadsController@toggleThreadState',
    ]);

    Route::post('management/threads/{deletedThread}/xeditable', [
        'as'   => 'management.threads.x-editable',
        'uses' => 'ForumThreadsController@saveEditableData',
    ]);

    Route::get('management/threads/{deletedThread}/edit', [
        'as'   => 'management.threads.edit',
        'uses' => 'ForumThreadsController@edit',
    ]);

    Route::get('management/threads/{deletedThread}/posts', [
        'as'   => 'management.threads.paginate',
        'uses' => 'ForumPostsController@paginate',
    ]);

    Route::put('management/threads/{deletedThread}/posts/{deletedPost}', [
        'as'   => 'management.posts.update',
        'uses' => 'ForumPostsController@update',
    ]);

    Route::delete('management/threads/{deletedThread}/posts/{deletedPost}', [
        'as'   => 'management.posts.destroy',
        'uses' => 'ForumPostsController@destroy',
    ]);

    // Blacklist routes
    Route::get('blacklist/get-entries', [
        'as'   => 'blacklists.fetch-entries',
        'uses' => 'ForumBlacklistsController@fetchEntries',
    ]);

    Route::get('blacklist/get-categories', [
        'as'   => 'blacklists.fetch-categories',
        'uses' => 'ForumBlacklistsController@fetchCategories',
    ]);

    Route::post('blacklist/create', [
        'as'   => 'blacklists.store',
        'uses' => 'ForumBlacklistsController@store',
    ]);

    Route::delete('blacklist/{blacklistEntry}', [
        'as'   => 'blacklists.destroy',
        'uses' => 'ForumBlacklistsController@destroy',
    ]);
});
