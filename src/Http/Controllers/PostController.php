<?php

namespace Vientodigital\LaravelForum\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Vientodigital\LaravelForum\Models\Discussion;
use Vientodigital\LaravelForum\Models\Post;

class PostController
{
    /**
     * Manage "settings" of some post (approved, private, hide, etc).
     */
    public function status(Request $request, Post $post)
    {
        if (!$post->canEdit(Auth::user()->id)) {
            // Forbidden
            dd('No');
        }
        $key = $request->get('key');
        $value = 1 === intval($request->get('value', 0));
        switch ($key) {
            case 'approve':
                $post->is_approved = true === $value ? 1 : 0;

            break;
            case 'private':
                $post->is_private = true === $value ? 1 : 0;

            break;
            case 'hide':
                if ($value) {
                    $post->hidden_at = null;
                    $post->hidden_user_id = null;
                } else {
                    $post->hidden_at = Carbon::now()->toDateTimeString();
                    $post->hidden_user_id = Auth::user()->id;
                }

            break;
        }
        $post->save();

        return back()->with('laravel-forum-status', __('laravel-forum::words.record_updated'));
    }

    /**
     * Store a new post.
     * Aditionaly can be setted "from" param to define where redirects at last.
     */
    public function store(Request $request)
    {
        $data = $request->only('discussion_id', 'content');
        $redirectTo = $request->get('from', 'posts');

        $validator = Validator::make($data, [
            'discussion_id' => ['required', 'numeric', 'exists:' . config('laravel-forum.table_names.discussions') . ',id'],
            'content' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $discussion = Discussion::find($data['discussion_id']);

        if ($validator->fails()) {
            if ('discussion' === $redirectTo) {
                return redirect()->route('discussions.show', ['discussion' => $discussion->slug])
                    ->withErrors($validator)
                    ->withInput();
            }
            if (is_string($redirectTo) && !empty($redirectTo)) {
                return redirect()->route($redirectTo)->withErrors($validator)->withInput();
            }
            redirect()
                ->route('posts.create')
                ->withErrors($validator)
                ->withInput();
        }

        $data['user_id'] = Auth::user()->id;
        $data['is_private'] = 0;
        $data['is_approved'] = 1;
        $data['number'] = $discussion->post_number_index + 1;
        $data['ip_address'] = $request->ip();
        $post = Post::create($data);

        $discussion->comment_count = ($discussion->comment_count + 1);
        $discussion->participant_count = count($discussion->posts()->get()->unique('user_id'));
        $discussion->post_number_index = $post->number;
        $discussion->last_posted_at = $post->created_at;
        $discussion->last_posted_user_id = $post->user_id;
        $discussion->last_post_id = $post->id;

        if (1 === $post->number) {
            $discussion->first_post_id = $post->id;
        }
        $discussion->save();

        if ('discussion' === $redirectTo) {
            return redirect()->route('discussions.show', ['discussion' => $discussion->slug])->with('laravel-forum-status', __('laravel-forum::words.record_created'));
        }
        if (is_string($redirectTo) && !empty($redirectTo)) {
            // return redirect()->route($redirectTo)->with('laravel-forum-status', __('laravel-forum::words.record_created'));
            return redirect()->back();
        }

        return redirect()->route('posts.index')->with('laravel-forum-status', __('laravel-forum::words.record_created'));
    }

    /**
     * Update an existing post.
     * Aditionaly can be setted "from" param to define where redirects at last.
     */
    public function update(Request $request, Post $post)
    {
        $data = $request->only('discussion_id', 'content');
        $redirectTo = $request->get('from', 'posts');

        $validator = Validator::make($data, [
            'content' => ['required', 'string', 'min:5', 'max:1000'],
        ]);
        $data['edited_at'] = Carbon::now()->toDateTimeString();
        $data['edited_user_id'] = Auth::user()->id;

        $post->fill($data);
        $post->save();
        $discussion = $post->discussion;

        if ('discussion' === $redirectTo) {
            return redirect()->route('discussions.show', ['discussion' => $discussion->slug])->with('laravel-forum-status', __('laravel-forum::words.record_updated'));
        }
        if (is_string($redirectTo) && !empty($redirectTo)) {
            return redirect()->route($redirectTo)->with('laravel-forum-status', __('laravel-forum::words.record_updated'));
        }

        return redirect()->route('posts.index')->with('laravel-forum-status', __('laravel-forum::words.record_updated'));
    }

    /**
     * Removes an existing post.
     * Aditionaly can be setted "from" param to define where redirects at last.
     */
    public function destroy(Request $request, Post $post)
    {
        $data = $request->only('discussion_id', 'content');
        $redirectTo = $request->get('from', 'posts');
        $discussion = $post->discussion;
        $post->delete();
        $discussion->comment_count = ($discussion->comment_count - 1);
        $discussion->save();

        if ('discussion' === $redirectTo) {
            return redirect()->route('discussions.show', ['discussion' => $discussion->slug])->with('laravel-forum-status', __('laravel-forum::words.record_destroyed'));
        }
        if (is_string($redirectTo) && !empty($redirectTo)) {
            return redirect()->route($redirectTo)->with('laravel-forum-status', __('laravel-forum::words.record_destroyed'));
        }

        return redirect()->route('posts.index')->with('laravel-forum-status', __('laravel-forum::words.record_destroyed'));
    }
}
