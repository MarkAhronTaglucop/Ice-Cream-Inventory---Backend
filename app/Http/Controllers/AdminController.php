<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminController extends Controller
{
    // View roles and users
    public function users()
    {
        $users = User::with('role')->get();
        $roles = DB::table('roles')->get();
        return Inertia::render('Admin', ['users' => $users, 'roles' => $roles]);
    }

    // Manage and update roles of users
    public function updateUserRole(Request $request, $userId)
    {
        $user = DB::table('users')->where('id', $userId)->first();

        if (!$user) {
            return back()->withErrors(['message' => 'User not found.']);
        }

        // Prevent changes for admin roles
        if ($user->role_id == 1) {
            return back()->withErrors(['message' => 'Admin roles cannot be edited or demoted.']);
        }

        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        DB::table('users')->where('id', $userId)->update(['role_id' => $validated['role_id']]);
        return back()->with('message', 'User role updated successfully!');
    }

    // Delete a user
    public function deleteUser($id)
    {
        $user = DB::table('users')->find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        if ($user->role_id == 1) {
            return redirect()->back()->with('error', 'Admin users cannot be deleted.');
        }

        DB::table('users')->where('id', $id)->delete();
        return redirect()->back()->with('message', 'User deleted successfully');
    }

    // View all ice cream records
    public function viewIceCream()
    {
        $iceCreams = DB::table('icecreams')->get();
        return Inertia::render('IceCreamList', ['iceCreams' => $iceCreams]);
    }
}
