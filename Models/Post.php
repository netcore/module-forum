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

    public $appends = [
        'parsed_content',
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

    /** -------------------- Accessors -------------------- */

    /**
     * Get the parsed content of post (BB codes -> HTML).
     *
     * @return string
     */
    public function getParsedContentAttribute(): string
    {
        return self::parseBBCodes($this->content);
    }

    /**
     * Parse BB codes to HTML.
     *
     * @param string $content
     * @return string
     */
    public static function parseBBCodes(string $content): string
    {
        static $processor;

        if (!$processor) {
            $handlers = new HandlerContainer;
            $processor = new Processor(new RegularParser, $handlers);
        }

        return (string)$processor->process($content);
    }

    /** -------------------- Helpers -------------------- */

    /**
     * Mark current post as first post.
     *
     * @return bool
     */
    public function markAsFirst(): bool
    {
        $this->thread->posts()->getQuery()->update([
            'is_first' => false,
        ]);

        $this->is_first = true;

        return $this->save();
    }
}