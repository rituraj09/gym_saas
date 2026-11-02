<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePlatformSettingsRequest;
use App\Models\PlatformSetting;

class AdminSettingController extends Controller
{
    public function index()
    {
        $settings = PlatformSetting::grouped();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(UpdatePlatformSettingsRequest $request)
    {
        foreach ($request->settings as $key => $value) {
            PlatformSetting::set($key, $value ?? '');
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
