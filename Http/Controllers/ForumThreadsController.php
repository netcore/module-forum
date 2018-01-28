<?php

namespace Modules\Forum\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Forum\Models\Thread;

class ForumThreadsController extends Controller
{
    /**
     * Display thread management page.
     *
     * @param \Modules\Forum\Models\Thread $deletedThread
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Thread $deletedThread)
    {
        $thread = $deletedThread;

        $thread->load([
            'firstPost' => function ($q) {
                return $q->withTrashed();
            },
        ]);

        return view('forum::threads.edit', compact('thread'));
    }

    /**
     * Toggle thread attribute.
     *
     * @param \Modules\Forum\Models\Thread $deletedThread
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleThreadState(Thread $deletedThread): JsonResponse
    {
        $thread = $deletedThread;

        $attribute = request('attribute');

        try {
            if ($attribute === 'deleted_at') {
                $thread->trashed() ? $thread->restore() : $thread->delete();
            } else {
                $thread->update([
                    $attribute => !$thread->$attribute,
                ]);
            }
        } catch (Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Successfully updated!',
        ]);
    }

    /**
     * Validate and save data from x-editable.
     *
     * @param \Modules\Forum\Models\Thread $deletedThread
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveEditableData(Thread $deletedThread, Request $request): JsonResponse
    {
        $field = $request->input('name');
        $value = $request->input('value');

        $rules = [
            'title' => 'required|max:255',
        ];

        if (!isset($rules[$field])) {
            abort(404);
        }

        $validator = validator([$field => $value], $rules);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
            ]);
        }

        try {
            $deletedThread->{$field} = $value;
            $deletedThread->save();
        } catch (Exception $exception) {
            return response()->json([
                'status'  => 'error',
                'message' => $exception->getMessage(),
            ]);
        }

        return response()->json([
            'status' => 'success',
        ]);
    }
}