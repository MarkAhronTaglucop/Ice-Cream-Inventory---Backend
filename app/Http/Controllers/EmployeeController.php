<?php

namespace App\Http\Controllers;

use App\Models\IceCream; // Make sure you have an IceCream model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function icecream_inventory()
    {
        $icecream = DB::table('icecream_inventory')->get();
        return Inertia::render('Employee', [ 'icecreams' => $icecream]);
    }

    public function updateIcecream(Request $request, $id)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'flavor' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'manufactured_date' => 'nullable|date',
            'status' => 'nullable|in:Available,Out of Stock',
        ]);

        try {
            // Call the PostgreSQL function
            DB::select('SELECT edit_icecream(?, ?, ?, ?, ?, ?, ?)', [
                $id, // ICECREAM_ID
                $validated['flavor'] ?? null,
                $validated['description'] ?? null,
                $validated['price'] ?? null,
                $validated['stock'] ?? null,
                $validated['manufactured_date'] ?? null,
                $validated['status'] ?? null,
            ]);

            return redirect()->back()->with(['message' => 'Ice cream details updated successfully.'], 200);
        } catch (\Exception $e) {
            // Handle errors
            return redirect()->back()->with(['error' => 'Failed to update ice cream: ' . $e->getMessage()], 500);
        }
    }
    







    public function addIcecream(Request $request)
{
    // Validate the incoming form request
    $validated = $request->validate([
        'flavor' => 'required|string|max:255',  // This corresponds to 'name' in the function
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'manufactured_date' => 'required|date',
        'status' => 'required|in:Available,Out of Stock',
    ]);

    // Use dd to inspect the validated data
    // This will output the data to the browser and stop execution

    try {
        // Insert the new ice cream record into the database using the custom function
        DB::statement('SELECT add_icecream( ?::varchar, ?::text, ?::numeric, ?::int, ?::date, ?::varchar)', [
            $validated['flavor'],                           // p_name (VARCHAR)
            $validated['description'] ?? null,              // p_description (TEXT)
            $validated['price'],                            // p_price (NUMERIC)
            $validated['stock'],                            // p_stock (INTEGER)
            $validated['manufactured_date'],                // p_manufactured_date (DATE)
            $validated['status'],                           // p_status (VARCHAR)
        ]);
        
        

        return redirect()->back()->with('success', 'Ice cream added successfully.');
    } catch (\Exception $e) {
        // Handle errors
        dd('Failed to add ice cream:', $e->getMessage());  // Use dd here for error debugging
    }
}




}
