<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminController extends Controller
{

    public function users()// this function handles that the ADMIN CAN VIEW THE ROLES AND THE USERS
    {
        $users = User::with('role')->get();
        $roles = DB::table('roles')->get();
        return Inertia::render('Admin', ['users' => $users, 'roles' => $roles]);
    }





    
}
