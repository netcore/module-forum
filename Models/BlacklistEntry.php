<?php

namespace Modules\Forum\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Category\Models\Category;

class BlacklistEntry extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'netcore_forum__blacklist_entries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'type',
        'thread_id',
        'category_id',
        'expires_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public $dates = [
        'expires_at',
    ];

    /** -------------------- Relations -------------------- */

    /**
     * Blacklist entry belongs to thread.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('netcore.module-forum.user.model', '\App\Models\User'));
    }

    /**
     * Blacklist entry belongs to the thread.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Blacklist entry belongs to the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** -------------------- Accessors -------------------- */

    /**
     * Get the level of blacklist entry.
     *
     * @return string
     */
    public function getLevelAttribute(): string
    {
        if (!$this->thread_id && !$this->category_id) {
            return 'forum';
        }

        return $this->category_id ? 'category' : 'thread';
    }

    /**
     * Get the description of blacklist entry.
     *
     * @return string
     */
    public function getDescriptionAttribute(): string
    {
        if ($this->level == 'forum') {
            return 'Entire forum';
        }

        if ($this->level == 'category') {
            return 'Category :: ' . $this->category->getChainedNameAttribute();
        }

        return 'Thread :: ' . $this->thread->title;
    }

    /** -------------------- Query scopes -------------------- */

    /**
     * Scope only active entries.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $builder): Builder
    {
        return $builder->whereNull('expires_at')->orWhere(function (Builder $subQuery) {
            return $subQuery->whereNotNull('expires_at')->where('expires_at', '>', Carbon::now());
        });
    }

    /**
     * Scope only expired entries.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired(Builder $builder): Builder
    {
        return $builder->whereNotNull('expires_at')->where('expires_at', '<=', Carbon::now());
    }

    /** -------------------- Helpers -------------------- */

    /**
     * Determine if blacklist entry is permanent.
     *
     * @return bool
     */
    public function isPermanent(): bool
    {
        return $this->expires_at === null;
    }

    /**
     * Determine if blacklist entry is expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expires_at && ($this->expires_at < Carbon::now());
    }
}