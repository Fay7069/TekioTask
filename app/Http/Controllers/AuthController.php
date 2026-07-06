<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'role'     => 'required|in:Administrator,Therapist,Teacher,Parent,Student',
            'email'    => 'required|email|max:100',
            'password' => 'required|string|min:6|max:255',
        ]);

        $user = User::where('email', $request->email)
                    ->where('role', $request->role)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            Log::warning('Failed login attempt', [
                'email' => $request->email,
                'role'  => $request->role,
                'ip'    => $request->ip(),
            ]);

            return back()
                ->withInput($request->only('email', 'role'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $request->session()->regenerate();
        Auth::login($user, $request->boolean('remember'));
        session(['last_activity' => time()]);

        Log::info('Successful login', [
            'user_id' => $user->user_id,
            'role'    => $user->role,
            'ip'      => $request->ip(),
        ]);

        return $this->redirectByRole($user->role);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect with no-cache headers so back button
        // can't return to the authenticated page
        return redirect()->route('login')
                         ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
                         ->header('Pragma', 'no-cache')
                         ->header('Expires', '0');
    }

    private function redirectByRole(string $role)
    {
        return match ($role) {
            'Administrator' => redirect()->route('admin.dashboard'),
            'Therapist'     => redirect()->route('therapist.dashboard'),
            'Teacher'       => redirect()->route('teacher.dashboard'),
            'Parent'        => redirect()->route('parent.dashboard'),
            'Student'       => redirect()->route('student.dashboard'),
            default         => redirect()->route('login'),
        };
    }
}
