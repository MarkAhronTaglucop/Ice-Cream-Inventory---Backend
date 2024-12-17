<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SetDBConnection
{
    public function handle(Request $request, Closure $next)
    {
        // Default connection
        $connection = config('database.default');
        $userId = null; // Default to null if no user is authenticated

        

        if (Auth::check()) {
            $user = Auth::user();
            $userId = $user->id; // Retrieve the authenticated user's ID


            // Dynamically set connection based on role
            $connection = match ($user->role_id) {
                3 => 'pgsql_Customer',
                2 => 'pgsql_Employee',
                1 => 'pgsql_Admin',
                default => config('database.default'),
            };
        }

        // Purge and reconnect
        DB::purge($connection);
        config(['database.default' => $connection]);
        DB::reconnect($connection);

        // Set the session variable for the user ID in PostgreSQL
        if ($userId) {
            DB::statement("SET myapp.user_id = {$userId}");
        }


        return $next($request);
    }
}