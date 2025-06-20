<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('backend.config.general-configuration');
    }

    public function save(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:png,jpg,webp|max:5120',
            'favicon' => 'nullable|image|mimes:png,jpg,webp|max:5120',
        ]);

        return transaction(function () use ($request) {

            $data = $request->all();
            $setting = Setting::first();

            if ($request->hasFile('logo')) {
                $data['logo'] = uploadImages('logo', 'setting');
                deleteImage($setting->logo);
            }

            if ($request->hasFile('favicon')) {
                $data['favicon'] = uploadImages('favicon', 'setting');
                deleteImage($setting->favicon);
            }

            if ($setting) {
                $setting->update($data);
            } else {
                Setting::create($data);
            }

            return successResponse("Lưu thay đổi thành công.", $setting, 200, true, false);
        });
    }
}
