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
            'name' => 'nullable|string|max:255',
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
                $validated['name'] ?? null,
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
    


}
