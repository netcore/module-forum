<?php

namespace Modules\Forum\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;
use Modules\Forum\Traits\Models\HasAuthor;
use Thunder\Shortcode\HandlerContainer\HandlerContainer;
use Thunder\Shortcode\Parser\RegularParser;
use Thunder\Shortcode\Processor\Processor;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

class Post extends Model
{
    use
        SoftDeletes,
        HasAuthor,
        NodeTrait;

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
}