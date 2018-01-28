<?php

namespace Modules\Forum\PassThroughs\User;

use Illuminate\Contracts\Auth\Authenticatable;
use Modules\Category\Models\Category;
use Modules\Forum\Models\Thread;
use Modules\Forum\PassThroughs\PassThrough;

class ForumHelpers extends PassThrough
{
    /**
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    protected $user;

    /**
     * ForumHelpers constructor.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $authenticatable
     * @return void
     */
    public function __construct(Authenticatable $authenticatable)
    {
        $this->user = $authenticatable;
    }

    /**
     * Check if user is blocked in entire forum.
     *
     * @return bool
     */
    public function isBlockedInEntireForum(): bool
    {
        return !!$this
            ->user
            ->forumBlacklistEntries
            ->where('thread_id', null)
            ->where('category_id', null)
            ->reject(function ($entry) {
                return $entry->isExpired();
            })
            ->count();
    }

    /**
     * Check if user is blocked within given category or it's ancestors.
     *
     * @param \Modules\Category\Models\Category $category
     * @param bool $globalCheck
     * @return bool
     */
    public function isBlockedInCategory(Category $category, bool $globalCheck = false): bool
    {
        if ($globalCheck && $this->isBlockedInEntireForum()) {
            return true;
        }

        $categoryIds = array_merge(
            $category->ancestors->pluck('id')->toArray(),
            [$category->id]
        );

        return !!$this
            ->user
            ->forumBlacklistEntries
            ->whereIn('category_id', $categoryIds)
            ->reject(function ($entry) {
                return $entry->isExpired();
            })
            ->count();
    }

    /**
     * Check if user is blocked within given thread.
     *
     * @param \Modules\Forum\Models\Thread $thread
     * @param bool $globalCheck
     * @return bool
     */
    public function isBlockedInThread(Thread $thread, bool $globalCheck = false): bool
    {
        if ($globalCheck && $this->isBlockedInCategory($thread->category, true)) {
            return true;
        }

        return !!$this
            ->user
            ->forumBlacklistEntries
            ->where('thread_id', $thread->id)
            ->reject(function ($entry) {
                return $entry->isExpired();
            })
            ->count();
    }
}