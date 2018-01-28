<?php

namespace Modules\Forum\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Category\Models\Category;
use Modules\Forum\Traits\Models\HasAuthor;

class Thread extends Model
{
    use SoftDeletes,
        HasAuthor,
        Sluggable,
        SluggableScopeHelpers;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'netcore_forum__threads';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'user_id',
        'is_locked',
        'is_pinned',
        'title',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'firstPost',
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source'         => 'title',
                'includeTrashed' => true,
            ],
        ];
    }

    /** -------------------- Relations -------------------- */

    /**
     * Thread has one post that is content of thread (first post).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function firstPost(): HasOne
    {
        return $this->hasOne(Post::class)->whereIsFirst(true);
    }

    /**
     * Topic has many posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Thread belongs to the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}