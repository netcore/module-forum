<?php

namespace Modules\Forum\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Forum\Models\Post;
use Modules\Forum\Models\Thread;

class ForumPostsController extends Controller
{
    /**
     * Paginate posts.
     *
     * @param \Modules\Forum\Models\Thread $deletedThread
     * @return \Illuminate\Http\JsonResponse
     */
    public function paginate(Thread $deletedThread): JsonResponse
    {
        $posts = $deletedThread
            ->posts()
            ->withTrashed()
            ->with(['user.forumBlacklistEntries' => function($query) {
                return $query->active();
            }])
            ->paginate(10);

        $posts->each(function (Post $post) use ($deletedThread) {
            $post->admin_routes = [
                'update'  => route('forum::admin.management.posts.update', [$deletedThread, $post]),
                'destroy' => route('forum::admin.management.posts.destroy', [$deletedThread, $post]),
            ];

            $post->author = [
                'name'            => $post->user->forum_name ?? 'Deleted',
                'avatar'          => $post->user->forum_avatar ?? null,
                'blacklist_count' => $post->user->forumBlacklistEntries->count()
            ];

            return $post;
        });

        return response()->json($posts);
    }

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

    /**
     * Delete or restore post.
     *
     * @param \Modules\Forum\Models\Thread $thread
     * @param \Modules\Forum\Models\Post $post
     * @return \Modules\Forum\Models\Post
     */
    public function destroy(Thread $thread, Post $post): Post
    {
        try {
            $post->trashed() ? $post->restore() : $post->delete();
        } catch (Exception $e) {
            abort(500, $e->getMessage());
        }

        return $post;
    }
}