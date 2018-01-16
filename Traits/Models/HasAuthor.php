<?php

namespace Modules\Forum\Traits\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasAuthor
{
    /**
     * Entry belongs to user (author).
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('netcore.module-forum.user.model'), 'user_id');
    }

    /**
     * Get the author name.
     *
     * @return null|string
     */
    public function getAuthorNameAttribute(): ?string
    {
        $attribute = config('netcore.module-forum.user.name_accessor');

        if ($this->user) {
            return $this->user->$attribute;
        }

        return null;
    }
}