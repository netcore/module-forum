<?php

namespace Modules\Forum\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Forum\Traits\Models\HasAuthor;

class Thread extends Model
{
    use SoftDeletes, HasAuthor;

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

    /** -------------------- Relations -------------------- */

    /**
     * Topic has many posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}