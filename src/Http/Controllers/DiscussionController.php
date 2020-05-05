<?php

namespace Vientodigital\LaravelForum\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Vientodigital\LaravelForum\Models\Discussion;
use Vientodigital\LaravelForum\Models\Discussion\Tag as DiscussionTag;
use Vientodigital\LaravelForum\Models\Discussion\User as DiscussionUser;
use Vientodigital\LaravelForum\Models\Tag;

class DiscussionController
{
    public function index(Request $request)
    {
        $currentSort = [
            request()->get('sort_by', 'created_at'),
            'ASC' === request()->get('sort_dir', 'DESC') ? 'ASC' : 'DESC',
        ];
        $currentTag = Tag::where('slug', request()->get('tag', ''))->first();
        $currentSearch = trim((string) request()->get('search', ''));
        $discussions = $this->makeSearch($currentSearch, $currentSort, $currentTag, false);
        $stickies = $this->makeSearch($currentSearch, $currentSort, $currentTag, true);
        $tags = Tag::orderBy('name')->get();
        $user = Auth::user();

        $allRead = true;
        $discussionIds = [];

        foreach ([$discussions, $stickies] as $current) {
            foreach ($current as $discussion) {
                if (!in_array($discussion->id, $discussionIds)) {
                    $discussionIds[] = $discussion->id;
                }
                if (!$discussion->isRead()) {
                    $allRead = false;
                }
            }
        }

        return view(
            'laravel-forum::' . config('laravel-forum.views.folder') . 'discussions.index',
            compact('discussionIds', 'allRead', 'user', 'stickies', 'discussions', 'tags', 'currentSort', 'currentTag', 'currentSearch')
        );
    }

    /**
     * Manage "settings" of many discussions (or all).
     * Atm only to sets read/unread all.
     */
    public function statusAll(Request $request)
    {
        $key = $request->get('key');
        $value = 1 === intval($request->get('value', 0));

        switch ($key) {
            case 'read':
                $ids = array_map(function ($value) {
                    return (int) trim($value);
                }, explode(',', $request->get('ids')));
                foreach ($ids as $id) {
                    $this->setRead($value, $id);
                }

            break;
        }

        return back()->with('status', __('laravel-forum::words.status_changed'));
    }

    /**
     * Manage "settings" of some post (locked, sticky, readed, etc).
     */
    public function status(Request $request, Discussion $discussion)
    {
        $key = $request->get('key');
        $value = 1 === intval($request->get('value', 0));
        if ('read' !== $key && !$discussion->canEdit(Auth::user()->id)) {
            // Forbidden
            dd('No');
        }
        switch ($key) {
            case 'lock':
                $discussion->is_locked = true === $value ? 1 : 0;
                $discussion->save();

            break;
            case 'private':
                $discussion->is_private = true === $value ? 1 : 0;
                $discussion->save();

            break;
            case 'read':
                $this->setRead($value, $discussion);

            break;
        }

        return back()->with('status', __('laravel-forum::words.status_changed'));
    }

    /**
     * Shows an existing discussion.
     *
     * @param mixed $slug
     */
    public function show(Request $request, $slug)
    {
        $discussion = Discussion::where('slug', $slug)->firstOrFail();

        $posts = (Auth::user()->id === $discussion->user_id)
            ? $discussion->posts()->orderBy('created_at', 'ASC')->get()
            : $discussion->posts()->where('is_approved', 1)
                ->orderBy('created_at', 'ASC')
                ->get();

        $discussionUser = DiscussionUser::where('user_id', Auth::user()->id)
            ->where('discussion_id', $discussion->id)
            ->first();
        if (!$discussionUser) {
            $discussionUser = new DiscussionUser();
            $discussionUser->fill([
                'discussion_id' => $discussion->id,
                'user_id' => Auth::user()->id,
            ]);
        }
        $discussionUser->last_read_at = Carbon::now()->toDateTimeString();
        $discussionUser->last_read_post_number = $discussion->post_number_index;
        $discussionUser->save();

        return view('laravel-forum::discussions.show', compact('discussion', 'posts'));
    }

    /**
     * Create a new discussion.
     */
    public function create()
    {
        $tags = Tag::orderBy('name')->get();

        return view('laravel-forum::discussions.create', compact('tags'));
    }

    /**
     * Store a new discussion.
     */
    public function store(Request $request)
    {
        $data = $request->only('tags', 'title', 'is_private', 'is_approved', 'is_locked', 'is_sticky');

        $validator = Validator::make($data, [
            'title' => ['required', 'string', 'max:200'],
            'is_private' => ['nullable', 'boolean'],
            'is_approved' => ['nullable', 'boolean'],
            'is_locked' => ['nullable', 'boolean'],
            'is_sticky' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['numeric', 'exists:tags,id'],
        ]);

        $data['user_id'] = Auth::user()->id;
        if ($validator->fails()) {
            return redirect()->route('discussions.create')
                ->withErrors($validator)
                ->withInput();
        }

        $data['slug'] = $this->makeSlug($data['title']);

        $discussion = Discussion::create($data);
        $this->setTags($discussion, isset($data['tags']) ? $data['tags'] : []);

        return redirect()->route('forum.index')->with('status', __('laravel-forum::words.record_created'));
    }

    /**
     * Edits an existing discussion.
     */
    public function edit(Request $request, Discussion $discussion)
    {
        $tags = Tag::orderBy('name')->get();
        $discussionTags = [];
        if (Session::get('errors')) {
            if (is_array($request->old('tags'))) {
                $discussionTags = $request->old('tags');
            }
        } else {
            foreach (DiscussionTag::where('discussion_id', $discussion->id)->get() as $current) {
                $discussionTags[$current->tag_id] = $current->tag_id;
            }
        }

        return view('laravel-forum::discussions.edit', compact('discussion', 'tags', 'discussionTags'));
    }

    /**
     * Updates an existing discussion.
     */
    public function update(Request $request, Discussion $discussion)
    {
        $data = $request->only('tags', 'title', 'is_private', 'is_approved', 'is_locked', 'is_sticky');

        $validator = Validator::make($data, [
            'title' => ['required', 'string', 'max:200'],
            'is_approved' => ['nullable', 'boolean'],
            'is_locked' => ['nullable', 'boolean'],
            'is_sticky' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['numeric', 'exists:tags,id'],
        ]);
        if ($validator->fails()) {
            return redirect()->route('discussions.edit', ['discussion' => $discussion])
                ->withErrors($validator)
                ->withInput();
        }
        if ($data['title'] !== $discussion->title) {
            $data['slug'] = $this->makeSlug($data['title']);
        }
        $discussion->fill($data);
        $discussion->save();
        $this->setTags($discussion, isset($data['tags']) ? $data['tags'] : []);

        return redirect()->route('forum.index')->with('status', __('laravel-forum::words.record_updated'));
    }

    /**
     * Deletes an existing discussion.
     */
    public function destroy(Request $request, Discussion $discussion)
    {
        $discussion->delete();

        return redirect()->route('forum.index')->with('status', __('laravel-forum::words.record_destroyed'));
    }

    /**
     * Helper to sets read/unread discussions.
     *
     * @param bool status True to sets read, false to sets unread
     * @param mixed   discussion ID or instance of related discussion
     * @param user    User ID. If not setted it takes current user as User.
     * @param mixed      $status
     * @param mixed      $discussion
     * @param null|mixed $user
     */
    protected function setRead($status, $discussion, $user = null)
    {
        if (!($discussion instanceof Discussion)) {
            $discussion = Discussion::find($discussion);
        }
        if (!$user) {
            $user = Auth::user()->id;
        }
        $read = DiscussionUser::where('discussion_id', $discussion->id)
            ->where('user_id', $user)
            ->first();
        if ($status) {
            if (!$read) {
                $read = DiscussionUser::create([
                    'user_id' => $user,
                    'discussion_id' => $discussion->id,
                ]);
            }
            $read->fill([
                'last_read_at' => Carbon::now()->toDateTimeString(),
                'last_read_post_number' => $discussion->post_number_index,
            ]);
            $read->save();
        } else {
            if ($read) {
                $read->delete();
            }
        }
    }

    /**
     * Helper to make a search.
     *
     * @param string  search   Word(s) to search
     * @param array   sort     Sort field and order
     * @param string  tag      Tag to discriminate
     * @param bool sticky   Determines if current searchs runs over sticky discussions
     * @param mixed      $search
     * @param mixed      $sort
     * @param null|mixed $tag
     * @param mixed      $sticky
     */
    protected function makeSearch($search, $sort, $tag = null, $sticky = false)
    {
        $query = null;
        if ($tag) {
            $ids = DiscussionTag::where('tag_id', $tag->id)
                ->pluck('discussion_id')
                ->all();
            $query = Discussion::whereIn('id', $ids);
        } else {
            $query = Discussion::where('id', '>', 0);
        }
        if (!empty($search)) {
            $query->where('title', 'LIKE', '%' . $search . '%');
        }
        if ($sticky) {
            $query->where('is_sticky', 1);
        } else {
            $query->where('is_sticky', '!=', 1);
        }
        $query->orderBy($sort[0], $sort[1]);

        return $query->get();
    }

    /**
     * Helper to generate discussion slug from title.
     *
     * @param string title
     * @param mixed $title
     */
    protected function makeSlug($title)
    {
        $slug = Str::slug($title, '-');

        $counter = 1;
        while (1) {
            $test = $slug . '-' . $counter;
            if (!Discussion::where('slug', $test)->first()) {
                return $test;
            }
            ++$counter;
        }
    }

    /**
     * Sets discussion tags.
     *
     * @param Discussion $discussion discussion
     * @param array      $tags       tags to link
     */
    protected function setTags(Discussion $discussion, $tags)
    {
        if (!is_array($tags)) {
            $tags = [];
        }
        $tags = array_values(array_map('intval', $tags));
        DiscussionTag::where('discussion_id', $discussion->id)->whereNotIn('tag_id', $tags)->delete();
        foreach ($tags as $id) {
            $assoc = DiscussionTag::where('discussion_id', $discussion->id)
                ->where('tag_id', $id)->first();
            // discussion_tag assoc exists, continue
            if ($assoc) {
                continue;
            }
            $tag = Tag::find($id);
            if ($tag) {
                // discussion_tag assoc
                $assoc = DiscussionTag::create([
                    'discussion_id' => $discussion->id,
                    'tag_id' => $id,
                ]);
                // update tag info
                $tag->fill([
                    'discussion_count' => ($tag->discussion_count + 1),
                    'last_posted_at' => $discussion->updated_at,
                    'last_posted_discussion_id' => $discussion->id,
                    'last_posted_user_id' => $discussion->user_id,
                ]);
                // save tag
                $tag->save();
            }
        }
    }
}
