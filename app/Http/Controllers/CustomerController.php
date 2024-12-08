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
        // Fetch all ice creams with their details
        $iceCreams = IceCream::where('status', 'Available')->get(); // Only show available ice creams

        return Inertia::render('Customer', [
            'iceCreams' => $iceCreams, // Pass ice cream data to the front-end
        ]);
    }

    // // Search for ice creams based on name or flavor
    // public function search(Request $request)
    // {
    //     $searchQuery = $request->input('searchQuery', '');

    //     if (empty($searchQuery)) {
    //         return Inertia::render('Customer', [
    //             'searchedIceCreams' => [],
    //         ]);
    //     }

    //     try {
    //         // Perform a search for ice creams based on name or flavor (modify as needed)
    //         $searchedIceCreams = IceCream::where('name', 'like', '%' . $searchQuery . '%')
    //             ->orWhere('flavor', 'like', '%' . $searchQuery . '%')
    //             ->where('status', 'Available') // Only show available ice creams
    //             ->get();
    //     } catch (\Exception $e) {
    //         // Handle any exceptions during the search process
    //         return Inertia::render('Customer', [
    //             'searchedIceCreams' => [],
    //             'error' => $e->getMessage(), // Optional: Include error message
    //         ]);
    //     }

    //     return Inertia::render('Customer', [
    //         'searchedIceCreams' => $searchedIceCreams, // Return the search results to the front-end
    //     ]);
    // }
}
