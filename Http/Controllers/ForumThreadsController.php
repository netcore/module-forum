<?php

namespace Modules\Forum\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Forum\Models\Thread;

class ForumThreadsController extends Controller
{
    /**
     * Toggle thread attribute.
     *
     * @param \Modules\Forum\Models\Thread $thread
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleThreadState(Thread $thread): JsonResponse
    {
        $attribute = request('attribute');

        try {
            if ($attribute === 'deleted_at') {
                $thread->trashed() ? $thread->restore() : $thread->delete();
            } else {
                $thread->update([
                    $attribute => !$thread->$attribute,
                ]);
            }
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Successfully updated!',
        ]);
    }
}