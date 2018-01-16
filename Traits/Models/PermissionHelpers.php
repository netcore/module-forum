<?php

namespace Modules\Forum\Traits\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Modules\Forum\Models\BlacklistEntry;

trait PermissionHelpers
{
    /**
     * Determine if user is able to post within current thread.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable|null $authenticatable
     * @return bool
     */
    public function isUserAbleToPostWithinThread(?Authenticatable $authenticatable): bool
    {
        if (auth()->guest()) {
            return false;
        }

        return !$this->isUserBlacklisted(
            $authenticatable ?: auth()->user()
        );
    }

    /**
     * Determine if current user is not blacklisted in current thread, thread category or globally in forum.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $authenticatable
     * @return bool
     */
    protected function isUserBlacklisted(Authenticatable $authenticatable): bool
    {
        static $isBlacklisted;

        if (is_bool($isBlacklisted)) {
            return $isBlacklisted;
        }

        $blacklistEntries = BlacklistEntry::whereUserId($authenticatable->getAuthIdentifier());

        $blacklistEntries->where(function($subQuery) {
            // Globally blacklisted.
            $subQuery->where(function ($query) {
                return $query->whereNull('thread_id')->whereNull('category_id');
            });

            // Blacklisted in thread categories (ancestors + self).
            $subQuery->orWhere(function ($query) {
                $categoryIds = array_merge(
                    $this->category->ancestors->pluck('id')->toArray(),
                    [$this->category->id]
                );

                return $query->whereIn('category_id', $categoryIds);
            });

            // Blacklisted in current thread.
            $subQuery->orWhere(function($query) {
                return $query->where('thread_id', $this->id);
            });
        });

        foreach ($blacklistEntries->get() as $entry) {
            if ($entry->isPermanent() || $entry->expires_at > Carbon::now()) {
                return $isBlacklisted = true;
            }
        }

        return $isBlacklisted = false;
    }
}