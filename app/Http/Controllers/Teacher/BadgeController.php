<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BadgeController extends Controller
{
    public function index()
    {
        $badges = DB::table('badges')
                    ->where('teacher_id', Auth::id())
                    ->orderBy('threshold')
                    ->get();

        return view('teacher.badges', compact('badges'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'icon_letter' => 'required|string|size:1',
            'threshold'   => 'required|integer|min:1|max:999',
        ]);

        // Prevent duplicate thresholds for same teacher
        $exists = DB::table('badges')
                    ->where('teacher_id', Auth::id())
                    ->where('threshold', $request->threshold)
                    ->exists();

        if ($exists) {
            return redirect()->route('teacher.badges.index')
                             ->withErrors(['threshold' => 'You already have a badge at that threshold.']);
        }

        DB::table('badges')->insert([
            'teacher_id'   => Auth::id(),
            'name'         => $request->name,
            'icon_letter'  => strtoupper($request->icon_letter),
            'threshold'    => $request->threshold,
            'created_at'   => now(),
        ]);

        return redirect()->route('teacher.badges.index')
                         ->with('success', 'Badge "' . $request->name . '" created.');
    }

    public function destroy(int $id)
    {
        DB::table('badges')
          ->where('badge_id', $id)
          ->where('teacher_id', Auth::id())
          ->delete();

        return redirect()->route('teacher.badges.index')
                         ->with('success', 'Badge deleted.');
    }
}
