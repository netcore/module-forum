<?php

use DaveJamesMiller\Breadcrumbs\Facades\DuplicateBreadcrumbException;

try {
    Breadcrumbs::register('forum::management.index', function ($breadcrumbs, $category = null) {
        $breadcrumbs->parent('admin');
        $breadcrumbs->push('Forum', route('forum::admin.management.index', $category));
    });

    Breadcrumbs::register('forum::threads.edit', function ($breadcrumbs, $thread) {
        $breadcrumbs->parent('forum::management.index', $thread->category_id);
        $breadcrumbs->push('Manage thread - ' . $thread->title, route('forum::admin.management.threads.edit', $thread));
    });
} catch (DuplicateBreadcrumbException $e) {
    logger()->error('[Breadcrumbs Exception] ' . $e->getMessage());
}