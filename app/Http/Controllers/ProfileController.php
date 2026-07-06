<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index()
    {
        return view('shared.profile', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:100',
            'high_contrast' => 'nullable|boolean',
            'large_buttons' => 'nullable|boolean',
            'mute_sounds'   => 'nullable|boolean',
            'text_size'     => 'nullable|integer|in:14,16,18,20,24',
        ]);

        $user = Auth::user();

        // accessibility_settings is cast to array in the model,
        // but if it was stored as a JSON string in DB it may come back
        // as a string on first read — force it to be an array here
        $current = $user->accessibility_settings;
        if (!is_array($current)) {
            $current = json_decode($current, true) ?? [];
        }

        $newSettings = array_merge($current, [
            'high_contrast' => $request->boolean('high_contrast'),
            'large_buttons' => $request->boolean('large_buttons'),
            'mute_sounds'   => $request->boolean('mute_sounds'),
            'text_size'     => (int) ($request->text_size ?? 16),
        ]);

        // Use DB directly to avoid any $hidden / $fillable issues
        DB::table('users')
          ->where('user_id', $user->user_id)
          ->update([
              'name'                    => $request->name,
              'accessibility_settings'  => json_encode($newSettings),
          ]);

        return redirect()->route('profile.index')
                         ->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password_hash)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.',
            ]);
        }

        DB::table('users')
          ->where('user_id', $user->user_id)
          ->update(['password_hash' => Hash::make($request->password)]);

        return redirect()->route('profile.index')
                         ->with('success', 'Password changed successfully.');
    }
}
