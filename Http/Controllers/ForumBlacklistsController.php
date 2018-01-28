<?php

namespace Modules\Forum\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Category\Models\Category;
use Modules\Category\Models\CategoryGroup;
use Modules\Forum\Models\BlacklistEntry;
use Modules\Forum\Models\Thread;

class ForumBlacklistsController extends Controller
{
    /**
     * Get the data for blacklist modal.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchEntries(Request $request): JsonResponse
    {
        $threadId = $request->get('thread_id');
        $userId = $request->get('user_id');

        $user = $this->findUserOrFail($userId);
        $thread = Thread::find($threadId);

        // Get entries and map them.
        $entries = $user->forumBlacklistEntries()->active()->get();
        $entries = $entries->map(function (BlacklistEntry $entry) {
            return [
                'id'         => $entry->id,
                'expires_at' => $entry->expires_at ? $entry->expires_at->format('d.m.Y / H:i:s') : 'Never',
                'level'      => $entry->level,
                'text'       => $entry->description,
            ];
        });

        // Collect data where user is blocked.
        $blockedInEntireForum = $user->forumHelpers()->isBlockedInEntireForum();
        $blockedInThreadOrCategory = false;

        if (($thread && $user->forumHelpers()->isBlockedInThread($thread)) || $user->forumHelpers()->isBlockedInCategory($thread->category)) {
            $blockedInThreadOrCategory = true;
        }

        $blockedIn = [
            'forum'      => $blockedInEntireForum,
            'thread'     => $blockedInThreadOrCategory,
            'categories' => [],
        ];

        $blockedCategoryEntries = $user
            ->forumBlacklistEntries()
            ->with('category.descendants')
            ->whereNotNull('category_id')
            ->where(function (Builder $subQuery) {
                return $subQuery
                    ->whereNull('expires_at')
                    ->orWhere('expires_at', '>', Carbon::now());
            })
            ->get();

        $blockedCategoryEntries->each(function (BlacklistEntry $entry) use (&$blockedIn) {
            $blockedIn['categories'] = array_merge(
                $blockedIn['categories'],
                [$entry->category->id],
                $entry->category->descendants->pluck('id')->toArray()
            );
        });

        return response()->json([
            'entries'  => $entries,
            'disabled' => $blockedIn,
        ]);
    }

    /**
     * Fetch categories for blacklist modal.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchCategories(Request $request): JsonResponse
    {
        $key = config('netcore.module-forum.used_category_group.key', 'forum');

        $categories = CategoryGroup::where('key', $key)->firstOrFail()->categories;
        $categories->load('descendants');

        $disabledIDs = (array)$request->get('disable');
        $results = [];
        $i = 0;

        $categories->each(function (Category $category) use ($disabledIDs, &$results, &$i) {
            if ($category->isRoot() && $i) {
                $results[] = [
                    'id'       => 0,
                    'text'     => str_repeat('-', 100),
                    'disabled' => true,
                ];
            }

            $results[] = [
                'id'       => $category->id,
                'text'     => $category->getChainedNameAttribute(),
                'disabled' => in_array($category->id, $disabledIDs),
            ];

            $i++;
        });

        return response()->json([
            'results'    => $results,
            'pagination' => [
                'more' => false,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $user = $this->findUserOrFail(
            $request->input('user_id')
        );

        $level = $request->input('level');
        $categoryId = $request->input('category_id');

        if (!$level || !in_array($level, ['forum', 'thread', 'category'])) {
            abort(422, 'Please select entry type!');
        }

        if ($level == 'category' && !$categoryId) {
            abort(422, 'Please select category!');
        }

        $entryData = [
            'user_id' => $user->id,
        ];

        switch ($level) {
            case 'forum':
                BlacklistEntry::whereUserId($user->id)->delete();
                break;

            case 'thread':
                $thread = Thread::findOrFail(
                    $request->input('thread_id')
                );

                BlacklistEntry::whereUserId($user->id)->whereThreadId($thread->id)->delete();
                $entryData['thread_id'] = $thread->id;

                break;

            case 'category':
                $category = Category::with('descendants')->findOrFail($categoryId);

                $ids = array_merge(
                    $category->descendants->pluck('id')->toArray(),
                    [$category->id]
                );

                BlacklistEntry::whereUserId($user->id)->whereIn('category_id', $ids)->delete();
                $entryData['category_id'] = $category->id;

                break;
        }

        if ($expires = $request->input('expires_at')) {
            $entryData['expires_at'] = Carbon::parse($expires)->endOfDay();
        }

        BlacklistEntry::create($entryData);

        return response()->json([
            'state' => 'success',
        ]);
    }

    /**
     * Delete blacklist entry.
     *
     * @param \Modules\Forum\Models\BlacklistEntry $blacklistEntry
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(BlacklistEntry $blacklistEntry)
    {
        try {
            $blacklistEntry->delete();
        } catch (Exception $e) {
            abort(500, $e->getMessage());
        }

        return response()->json([
            'state' => 'success',
        ]);
    }

    /**
     * Find user by id or throw an exception.
     *
     * @param $id
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    protected function findUserOrFail($id): ?Authenticatable
    {
        $userQuery = app(config('netcore.module-admin.user.model'));

        if (in_array(SoftDeletes::class, class_uses($userQuery))) {
            $userQuery->withTrashed();
        }

        return $userQuery->findOrFail($id);
    }
}