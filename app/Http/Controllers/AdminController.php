<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminController extends Controller
{
    // // View roles and users
    public function users()
    {
        $users = User::with('role')->get();
        $roles = DB::table('roles')->get();
        return Inertia::render('Admin', ['users' => $users, 'roles' => $roles]);
    }


    // Delete a user
    public function deleteUser(User $user)
    {
        // Ensure you're working with the user ID, not the entire user object
        $userId = is_object($user) ? $user->id : $user;

        // Ensure the userId is valid
        if (empty($userId) || !is_numeric($userId)) {
            return redirect()->back()->with('error', 'Invalid user ID');
        }

        // Check if the user is an admin
        $userRecord = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('users.id', $userId)
            ->first();

        if ($userRecord && $userRecord->role_user === 'admin') {
            return redirect()->back()->with('error', 'Admin users cannot be deleted.');
        }

        // If the user is not an admin, delete the user
        DB::table('users')->where('id', $userId)->delete();

        return redirect()->back()->with('message', 'User deleted successfully');
    }





    // // Manage and update roles of users
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
            'role_id' => 'required|exists:roles,id', //ENSURE THE ROLE EXIST SA ROLE TABLE
        ]);

        DB::table('users')->where('id', $userId)->update(['role_id' => $validated['role_id']]);
        return back()->with('message', 'User role updated successfully!');
    }

    // View all ice cream records
    // public function viewIceCream()
    // {
    //     $iceCreams = DB::table('icecreams')->get();
    //     return Inertia::render('IceCreamList', ['iceCreams' => $iceCreams]);
    // }
}
