<?php

namespace Modules\Forum\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Forum\Traits\Controllers\ThreadsPaginationTrait;
use Modules\Category\Models\CategoryGroup;

class ForumManagementController extends Controller
{
    use ThreadsPaginationTrait;

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

        $categoryGroup = CategoryGroup::where('key', 'forum')->firstOrFail();
        $categories = $categoryGroup->categories()->with('ancestors')->leaves();

        if ($selectedCategory) {
            $selectedCategory = $categoryGroup->categories()->findOrFail($selectedCategory);
        }

        return view('forum::management.index', compact('categories', 'selectedCategory'));
    }
    
}
