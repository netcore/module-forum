<?php

return [
    /**
     * Allow to create threads in category leaves only.
     */
    'threads_in_leaves_only' => true,

    /**
     * Category group config used by forum module.
     *
     * @see https://github.com/netcore/module-category#category-groups
     */
    'used_category_group' => [
        'key'                  => 'forum',
        'title'                => 'Forum categories',
        'has_icons'            => true,
        'icons_for_only_roots' => true,
        'icons_type'           => 'file',
        'levels'               => 2,
    ],

    /**
     * User relation configuration.
     */
    'user' => [
        'model'         => config('netcore.module-admin.user.model', 'App\Models\User'),
        'name_accessor' => 'fullName',
    ],
];
