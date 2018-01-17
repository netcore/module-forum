<?php

namespace Modules\Forum\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Forum\Models\Post;
use Modules\Forum\Models\Thread;

class ForumPostsController extends Controller
{
    /**
     * Update post.
     *
     * @param \Modules\Forum\Models\Thread $thread
     * @param \Modules\Forum\Models\Post $post
     * @return \Modules\Forum\Models\Post
     */
    public function update(Thread $thread, Post $post): Post
    {
        $post->content = e(request()->input('content'));
        $post->save();

        return $post;
    }
}