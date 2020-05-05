<?php

namespace Vientodigital\LaravelForum\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Vientodigital\LaravelForum\Models\Tag;
use Vientodigital\LaravelForum\Rules\Color;

class TagController
{
    /**
     * Shows all tags.
     */
    public function index()
    {
        $tags = Tag::orderBy('name')->get();
        return view('laravel-forum::tags.index', compact('tags'));
    }

    /**
     * Shows tag config.
     */
    public function show(Request $request, Tag $tag)
    {
        return view('laravel-forum::tags.show', compact('tag'));
    }

    /**
     * Renders tag creation view.
     */
    public function create()
    {
        return view('laravel-forum::tags.create');
    }

    /**
     * Stores a new tag.
     */
    public function store(Request $request)
    {
        $data = $request->only('name', 'description', 'color', 'background_color');

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:100', 'unique:tags,name'],
            'description' => 'nullable|string|max:500',
            'color' => [new Color()],
            'background_color' => [new Color()],
        ]);

        if ($validator->fails()) {
            return redirect()->route('tags.create')
                ->withErrors($validator)
                ->withInput();
        }

        $data['slug'] = $this->makeSlug($data['name']);

        Tag::create($data);

        return redirect()->route('tags.index')->with('laravel-forum-status', __('laravel-forum::words.record_created'));
    }

    /**
     * Renders tag edition view.
     */
    public function edit(Request $request, Tag $tag)
    {
        return view('laravel-forum::tags.edit', compact('tag'));
    }

    /**
     * Updates an existing tag.
     */
    public function update(Request $request, Tag $tag)
    {
        $data = $request->only('name', 'description', 'color', 'background_color');
        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:100', 'unique:tags,name,' . $tag->id],
            'description' => 'nullable|string|max:500',
            'color' => [new Color()],
            'background_color' => [new Color()],
        ]);
        if ($validator->fails()) {
            return redirect()->route('tags.edit', ['tag' => $tag])
                ->withErrors($validator)
                ->withInput();
        }
        if ($data['name'] !== $tag->name) {
            $data['slug'] = $this->makeSlug($data['name']);
        }
        $tag->fill($data);
        $tag->save();

        return redirect()->route('tags.index')->with('laravel-forum-status', __('laravel-forum::words.record_updated'));
    }

    /**
     * Deletes an existing tag.
     */
    public function destroy(Request $request, Tag $tag)
    {
        $tag->delete();

        return redirect()->route('tags.index')->with('laravel-forum-status', __('laravel-forum::words.record_destroyed'));
    }

    /**
     * Helper to generate tag slug from name.
     *
     * @param string name
     * @param mixed $title
     * @param mixed $name
     */
    protected function makeSlug($name)
    {
        $slug = Str::slug($name, '-');

        $counter = 1;
        while (1) {
            $test = $slug . '-' . $counter;
            if (!Tag::where('slug', $test)->first()) {
                return $test;
            }
            ++$counter;
        }
    }
}
