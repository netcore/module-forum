<?php

namespace Modules\Forum\Traits\Controllers;

use DataTables;
use Modules\Category\Models\Category;
use Modules\Forum\Models\Thread;

trait ThreadsPaginationTrait
{
    /**
     * Prepare data for datatable.
     *
     * @return mixed
     * @throws \Exception
     */
    public function paginate(Category $category)
    {
        $leaves = Category::descendantsAndSelf(
            $category->id
        )->pluck('id')->toArray();

        $query = Thread::withTrashed()->with('user')->whereIn('category_id', $leaves);

        $datatable = DataTables::of(
            $query
        );

        $datatable->editColumn('user', function(Thread $thread) {
            return view('forum::management.datatable.author-link', compact('thread'));
        });

        $datatable->editColumn('is_locked', function(Thread $thread) {
            return view('forum::management.datatable.state-label', ['state' => $thread->is_locked])->render();
        });

        $datatable->editColumn('is_pinned', function(Thread $thread) {
            return view('forum::management.datatable.state-label', ['state' => $thread->is_pinned])->render();
        });

        $datatable->editColumn('deleted_at', function(Thread $thread) {
            return $thread->deleted_at ?: '-';
        });

        $datatable->addColumn('actions', function (Thread $thread) {
            return view('forum::management.datatable.actions', compact('thread'))->render();
        });

        $datatable->rawColumns([
            'user',
            'is_pinned',
            'is_locked',
            'actions',
        ]);

        return $datatable->make(true);
    }
}