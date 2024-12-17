<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\IceCream; // Assuming IceCream model is created
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CustomerController extends Controller
{
    // Display the customer dashboard with available ice creams
    public function display_info()
    {
        $icecreams = DB::select('SELECT * FROM available_icecreams');


        return Inertia::render('Customer', [
            'icecreams' => $icecreams
        ]);
    }


    public function search(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            // Call the PostgreSQL function
            $results = DB::select('SELECT * FROM SearchIceCreamByName(?)', [
                $validated['name']
            ]);
            // Pass results to the Inertia component
            return Inertia::render('Customer', [
                'icecreamsresult' => $results,
            ]);
        } catch (\Exception $e) {
            // Log the error and return an error response

            return back()->with('error', 'Failed to search for ice creams. Please try again.');
        }
    }

}
