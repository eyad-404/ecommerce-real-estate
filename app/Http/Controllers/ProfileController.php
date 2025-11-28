<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show profile page.
     */
    public function show()
    {
        return view('myauth.profile', ['user' => Auth::user()]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'location' => 'nullable|string|max:255',
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:6|confirmed',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:3072',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->birth_date = $request->birth_date;
        $user->gender = $request->gender;
        $user->location = $request->location;

        $secretKey = env('PASSWORD_HMAC_KEY');

        if ($request->filled('password')) {
            $hashedCurrentInput = $secretKey
                ? hash_hmac('sha256', $request->current_password ?? '', $secretKey)
                : ($request->current_password ?? '');
            if (
                !$request->filled('current_password') ||
                !Hash::check($hashedCurrentInput, $user->password)
            ) {
                $error = ['current_password' => ['Current password is incorrect.']];
                if ($request->ajax()) {
                    return response()->json(['errors' => $error], 422);
                }

                return back()->withErrors($error);
            }

            $newHash = $secretKey ? hash_hmac('sha256', $request->password, $secretKey) : $request->password;
            $user->password = Hash::make($newHash);

            $token = Password::createToken($user);
            $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

            Mail::send('emails.password_changed', ['user' => $user, 'resetUrl' => $resetUrl], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Your password has been changed');
            });

            event(new PasswordReset($user));
        }

        $profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);

        if ($request->hasFile('profile_image')) {
            $destinationPath = public_path('images/profile');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            if ($profile->profile_image) {
                $existingImage = $destinationPath.'/'.$profile->profile_image;
                if (File::exists($existingImage)) {
                    File::delete($existingImage);
                }
            }

            $filename = uniqid('profile_', true).'.'.$request->file('profile_image')->extension();
            $request->file('profile_image')->move($destinationPath, $filename);
            $profile->profile_image = $filename;
        }

        if (!$profile->exists) {
            $user->profile()->save($profile);
        } elseif ($profile->isDirty()) {
            $profile->save();
        }

        $user->save();
        $user->load('profile');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'profile_image' => $user->profile_image_url,
                'has_profile_image' => (bool) optional($user->profile)->profile_image,
                'message' => 'Profile updated successfully!',
            ]);
        }

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Delete the authenticated user's profile picture.
     */
    public function deletePic()
    {
        $user = Auth::user();
        $profile = $user->profile;

        if ($profile && $profile->profile_image) {
            $destinationPath = public_path('images/profile/'.$profile->profile_image);
            if (File::exists($destinationPath)) {
                File::delete($destinationPath);
            }

            $profile->profile_image = null;
            $profile->save();
        }

        $user->load('profile');

        return response()->json([
            'success' => true,
            'profile_image' => $user->profile_image_url,
            'has_profile_image' => false,
            'message' => 'Profile picture deleted!',
        ]);
    }

    /**
     * Validate the current password via AJAX.
     */
    public function checkPassword(Request $request)
    {
        $user = Auth::user();
        $currentPassword = $request->input('current_password');

        $secretKey = env('PASSWORD_HMAC_KEY');

        if (!$currentPassword) {
            return response()->json(['valid' => false, 'message' => 'Enter your current password']);
        }

        $hashedInput = $secretKey ? hash_hmac('sha256', $currentPassword, $secretKey) : $currentPassword;

        if (Hash::check($hashedInput, $user->password)) {
            return response()->json(['valid' => true, 'message' => 'Password is correct']);
        }

        return response()->json(['valid' => false, 'message' => 'Password is incorrect']);
    }
}
