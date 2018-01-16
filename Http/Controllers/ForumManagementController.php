<?php

namespace Modules\Forum\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Forum\Traits\Controllers\ControllerStatsTrait;
use Modules\Forum\Traits\Controllers\ThreadsPaginationTrait;
use Modules\Category\Models\CategoryGroup;

class ForumManagementController extends Controller
{
    use ThreadsPaginationTrait;
    use ControllerStatsTrait;

    /**
     * Display forum management index page.
     *
     * @param null|int $selectedCategory
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index($selectedCategory = null)
    {
        if ($categoryId = request()->query('category_id')) {
            return redirect()->route('forum::admin.management.index', $categoryId);
        }

        $threadsInLeavesOnly = config('netcore.module-forum.threads_in_leaves_only');

        $categoryGroup = CategoryGroup::where('key', 'forum')->firstOrFail();
        $categories = $categoryGroup->categories()->with('ancestors');
        $categories = $threadsInLeavesOnly ? $categories->leaves() : $categories->get();

        if ($selectedCategory) {
            $selectedCategory = $categoryGroup->categories()->findOrFail($selectedCategory);
        }

        $stats = $this->collectStatsForSelectedCategory($selectedCategory, $categoryGroup);

        return view('forum::management.index', compact('categories', 'selectedCategory', 'stats'));
    }
}
