<?php

namespace Vientodigital\LaravelForum\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Vientodigital\LaravelForum\Models\Setting;

class SettingController
{
    public function index()
    {
        $settings = Setting::all();
        return $settings;
    }

    public function show(Request $request, Setting $setting)
    {
        return $setting;
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'key' => 'required',
            'value' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->route('settings.create')
                        ->withErrors($validator)
                        ->withInput();
        }
        Setting::create($data);
        return redirect()->route('settings.index')->with('status', 'Setting created!');
    }

    public function edit(Request $request, Setting $setting)
    {
        return view('laravel-forum::settings.edit', compact('setting'));
    }

    public function update(Request $request, Setting $setting)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'key' => 'required',
            'value' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->route('settings.edit', ['setting' => $setting])
                        ->withErrors($validator)
                        ->withInput();
        }

        $setting->fill($data);
        $setting->save();
        return redirect()->route('settings.index')->with('status', 'Setting updated!');
    }

    public function destroy(Request $request, Setting $setting)
    {
        $setting->delete();
        return redirect()->route('settings.index')->with('status', 'Setting destroyed!');
    }
}
