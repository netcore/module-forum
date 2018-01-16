<?php

namespace Modules\Forum\Traits\Controllers;

use Illuminate\Support\Collection;
use Modules\Category\Models\Category;
use Modules\Category\Models\CategoryGroup;
use Modules\Forum\Models\Thread;

trait ControllerStatsTrait
{
    /**
     * Get the statistics for selected category.
     *
     * @param $category
     * @param \Modules\Category\Models\CategoryGroup $categoryGroup
     * @return \Illuminate\Support\Collection
     */
    protected function collectStatsForSelectedCategory($category, CategoryGroup $categoryGroup): Collection
    {
        if ($category instanceof Category) {
            $IDs = $categoryGroup
                ->categories()
                ->descendantsAndSelf($category->id)
                ->pluck('id')
                ->toArray();
        } else {
            $IDs = $categoryGroup
                ->categories()
                ->pluck('id')
                ->toArray();
        }

        // Threads count.
        $threadsCount = Thread::whereIn('category_id', $IDs)->withTrashed()->count();
        $postsCount = Thread::whereIn('category_id', $IDs)->withTrashed()->sum('replies');
        $latestThread = Thread::whereIn('category_id', $IDs)->withTrashed()->latest()->first();

        return collect(compact('threadsCount', 'postsCount', 'latestThread'));
    }
}
