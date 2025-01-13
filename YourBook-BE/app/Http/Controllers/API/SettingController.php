<?php

namespace App\Http\Controllers\API;

use App\Enums\SettingName;
use App\Enums\SettingValue;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Illuminate\Support\Facades\Hash;
use App\Models\Auth\User;
use App\Http\Requests\ProfileUpdateRequest;

class SettingController extends Controller
{
    use ApiResponse;

    public function store(Request $request)
    {
        try {
            // validate request
            $request->validate([
                // 'user_id' => ['required', 'numeric', 'exists:users,id'],
                'settings' => ['required', 'array'],
                'settings.*.setting_name' => ['required', Rule::in(SettingName::values())],
                'settings.*.setting_value' => ['required', Rule::in(SettingValue::values())],
            ]);

            $user_id = Auth::id();

            // Loop through each setting in the request and update or create it
            foreach ($request->settings as $settingData) {
                // Check if the setting already exists for the user
                $setting = Setting::where('user_id', $user_id)
                    ->where('setting_name', $settingData['setting_name'])
                    ->first();

                if ($setting) {
                    // Update existing setting
                    $setting->update([
                        'setting_value' => $settingData['setting_value'],
                    ]);
                } else {
                    // Create a new setting
                    Setting::create([
                        'user_id' => $user_id,
                        'setting_name' => $settingData['setting_name'],
                        'setting_value' => $settingData['setting_value'],
                    ]);
                }
            }
            return $this->success('Settings saved successfully.', null, 200);
        } catch (ValidationException $e) {
            // Catch validation exception and return a custom response with errors using ApiResponse trait
            return $this->error('Validation failed', $e->errors(), 422);
        }
    }


    // change password
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->toArray(), [
            'current_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (!Hash::check($request->input('current_password'), Auth::user()->password)) {
            return $this->error('the old password is incorrect.',
                ['current_password' => ['the old password is incorrect.']],
                self::$responseCode::HTTP_UNPROCESSABLE_ENTITY);
        }
        $user = User::find(Auth::user()->id);
        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return $this->success("Password Updated", null, self::$responseCode::HTTP_OK);
    }

    // Update the user's profile information.
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return $this->success("profile-updated", null, 200);
    }
}
