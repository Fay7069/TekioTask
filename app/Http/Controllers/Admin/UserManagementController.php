<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    // ── List all staff ────────────────────────────────────────
    public function index()
    {
        $users = User::whereIn('role', ['Administrator', 'Teacher', 'Therapist', 'Parent'])
                     ->orderBy('role')
                     ->orderBy('name')
                     ->get();

        return view('admin.users', compact('users'));
    }

    // ── Create form ───────────────────────────────────────────
    public function create()
    {
        return view('admin.user-form');
    }

    // ── Store ─────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email|max:100',
            'role'     => 'required|in:Administrator,Teacher,Therapist,Parent',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'role'          => $request->role,
            'password_hash' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', $request->name . ' account created.');
    }

    // ── Edit form ─────────────────────────────────────────────
    public function edit(User $user)
    {
        abort_if($user->role === 'Student', 404);
        return view('admin.user-form', compact('user'));
    }

    // ── Update ────────────────────────────────────────────────
    public function update(Request $request, User $user)
    {
        abort_if($user->role === 'Student', 404);

        $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,' . $user->user_id . ',user_id',
            'role'  => 'required|in:Administrator,Teacher,Therapist,Parent',
        ]);

        DB::table('users')->where('user_id', $user->user_id)->update([
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ]);

        // Only update password if provided.
        // No 'confirmed' rule here — the edit form intentionally has no
        // password_confirmation field, so requiring it always failed.
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            DB::table('users')->where('user_id', $user->user_id)->update([
                'password_hash' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('admin.users.index')
                         ->with('success', $user->name . ' account updated.');
    }

    // ── Delete ────────────────────────────────────────────────
    public function destroy(User $user)
    {
        abort_if($user->role === 'Student', 404);
        DB::table('users')->where('user_id', $user->user_id)->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', 'Account deleted.');
    }
}
