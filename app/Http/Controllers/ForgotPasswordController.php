<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'role'  => 'required|in:Administrator,Therapist,Teacher,Parent,Student',
        ]);

        $user = User::where('email', $request->email)
                    ->where('role', $request->role)
                    ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'No account found with that email and role combination.',
            ])->withInput();
        }

        $tempPassword = Str::random(10);

        DB::table('users')
          ->where('user_id', $user->user_id)
          ->update(['password_hash' => Hash::make($tempPassword)]);

        Mail::raw(
            "Hello {$user->name},\n\n" .
            "Your temporary password for TekioTask is:\n\n" .
            "    {$tempPassword}\n\n" .
            "Please log in and change your password immediately from Profile > Change Password.\n\n" .
            "Role: {$user->role}\n" .
            "Email: {$user->email}\n\n" .
            "TekioTask — Smart Integrated Therapy Centre",
            function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('TekioTask — Your Temporary Password');
            }
        );

        return redirect()->route('password.request')
                         ->with('success', 'A temporary password has been sent to ' . $user->email . '. Check your inbox.');
    }
}
