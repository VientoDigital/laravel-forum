<?php

namespace Vientodigital\LaravelForum\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Vientodigital\LaravelForum\Models\Setting;

class SettingController
{
    public function index()
    {
        $settings = Setting::all();

        return view('laravel-forum::settings.index', compact('settings'));
    }

    public function show(Request $request, Setting $setting)
    {
        return view('laravel-forum::settings.show', compact('setting'));
    }

    public function create()
    {
        return view('laravel-forum::settings.create');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'key' => 'required|unique:settings,key',
            'value' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->route('settings.create')
                ->withErrors($validator)
                ->withInput();
        }
        Setting::create($data);

        return redirect()->route('settings.index')->with('laravel-forum-status', __('laravel-forum::words.record_created'));
    }

    public function edit(Request $request, Setting $setting)
    {
        return view('laravel-forum::settings.edit', compact('setting'));
    }

    public function update(Request $request, Setting $setting)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'key' => [
                'required',
                Rule::unique('settings')->ignore($setting->key, 'key'),
            ],
            'value' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->route('settings.edit', ['setting' => $setting])
                ->withErrors($validator)
                ->withInput();
        }

        $setting->fill($data);
        $setting->save();

        return redirect()->route('settings.index')->with('laravel-forum-status', __('laravel-forum::words.record_updated'));
    }

    public function destroy(Request $request, Setting $setting)
    {
        $setting->delete();

        return redirect()->route('settings.index')->with('laravel-forum-status', __('laravel-forum::words.record_destroyed'));
    }
}
