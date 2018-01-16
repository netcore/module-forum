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

    Route::post('management/threads/{thread}/toggle-state', [
        'as'   => 'management.threads.toggle-state',
        'uses' => 'ForumThreadsController@toggleThreadState',
    ]);

});
