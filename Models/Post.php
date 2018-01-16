<?php

namespace Modules\Forum\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Forum\Traits\Models\HasAuthor;

class Post extends Model
{
    use SoftDeletes, HasAuthor;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'netcore_forum__posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'thread_id',
        'user_id',
        'post_id',
        'content',
    ];

    /** -------------------- Relations -------------------- */

    /**
     * Post belongs to thread.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class)->withTrashed();
    }

    /**
     * Post belongs to post (reply).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}