<?php

namespace Modules\Forum\Traits\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Forum\Models\BlacklistEntry;
use Modules\Forum\Models\Post;
use Modules\Forum\Models\Thread;
use Modules\Forum\PassThroughs\User\ForumHelpers;

trait UserForumModel
{
    /** -------------------- Relations -------------------- */

    /**
     * User has many forum blacklist entries.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function forumBlacklistEntries(): HasMany
    {
        return $this->hasMany(BlacklistEntry::class);
    }

    /**
     * User has many forum threads.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function forumThreads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }

    /**
     * User has many forum posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function forumPosts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /** -------------------- Accessors -------------------- */

    /**
     * Get the author name used in forum.
     *
     * @return string
     */
    public function getForumNameAttribute(): string
    {
        return (string)$this->full_name ?? $this->first_name ?? 'Unknown';
    }

    /**
     * Get the author avatar used in forum.
     *
     * @return null|string
     */
    public function getForumAvatarAttribute(): ?string
    {
        $fallback = 'https://placehold.it';

        return method_exists($this, 'gravatar') ? $this->gravatar() : $fallback;
    }

    /** -------------------- PassThroughs -------------------- */

    public function forumHelpers(): ForumHelpers
    {
        return new ForumHelpers($this);
    }
}