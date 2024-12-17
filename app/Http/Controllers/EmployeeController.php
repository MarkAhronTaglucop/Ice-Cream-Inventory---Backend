<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function icecream_inventory()
    {
        $icecream = DB::table('all_icecreams')->get();
        return Inertia::render('Employee', ['icecreams' => $icecream]);
    }

    public function updateIcecream(Request $request, $id)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'manufactured_date' => 'nullable|date',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Check if the image is provided and handle the upload
        if ($request->hasFile('image_url')) {
            $imagePath = $request->file('image_url')->store('images', 'public');
            $imageUrl = Storage::url($imagePath);
            $validated['image_url'] = $imageUrl;
        }

        try {
            // Call the PostgreSQL function
            $result = DB::select('SELECT edit_icecreams(?, ?, ?, ?, ?, ?)', [
                $id, // Ice cream ID
                $validated['name'], // Name
                $validated['description'], // Description
                $validated['manufactured_date'], // Manufactured date
                $validated['price'], // Price
                $validated['image_url'] ?? null, // Image URL
            ]);

            // If the function returns an error
            if (isset($result[0]->edit_icecreams) && strpos($result[0]->edit_icecreams, 'Error') !== false) {
                return redirect()->back()->with('error', $result[0]->edit_icecreams);
            }

            return redirect()->route('icecream.index')->with('success', 'Ice cream updated successfully!');
        } catch (\Exception $e) {
            // Handle errors
            return redirect()->back()->with(['error' => 'Failed to update ice cream: ' . $e->getMessage()], 500);
        }
    }





    public function addIcecream(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'manufactured_date' => 'nullable|date',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Check if the image is provided and handle the upload
        if ($request->hasFile('image_url')) {
            $imagePath = $request->file('image_url')->store('images', 'public');
            $imageUrl = Storage::url($imagePath);
            $validated['image_url'] = $imageUrl; // Update 'image_url' with the stored URL
        }

        try {
            // Call the PostgreSQL function to insert the ice cream
            $result = DB::select('SELECT add_icecreams(?, ?, ?, ?, ?)', [
                $validated['name'],                // Name
                $validated['description'],         // Description
                $validated['manufactured_date'],   // Manufactured Date
                $validated['price'],               // Price
                $validated['image_url'] ?? null,   // Image URL
            ]);

            // Check for errors from the database function
            if (isset($result[0]->add_icecreams) && strpos($result[0]->add_icecreams, 'Error') !== false) {
                return redirect()->back()->with('error', $result[0]->add_icecreams);
            }

            return redirect()->route('icecream.index')->with('success', 'Ice cream added successfully!');
        } catch (\Exception $e) {
            // Handle exceptions
            return redirect()->back()->with(['error' => 'Failed to add ice cream: ' . $e->getMessage()], 500);
        }
    }



    public function updateStock(Request $request, $icecream_id)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'new_stock' => 'required|integer|min:0', // Ensure the new stock is an integer and not negative
        ]);

        try {
            // Call the PostgreSQL function to update the stock
            $result = DB::select('SELECT update_icecream_stock(?, ?)', [
                $icecream_id, // The ID of the ice cream
                $validated['new_stock'], // The new stock value
            ]);

            return redirect()->back()->with('success', 'Update success');
        } catch (\Exception $e) {
            // Log the error for debugging
            return redirect()->back()->with('error', 'Failed to update stock. Please try again.');
        }
    }

    public function deleteIcecream($id)
    {
        try {
            // Call the PostgreSQL function
            $result = DB::select('SELECT delete_icecream(?)', [$id]);

            return redirect()->back()->with('success', 'deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete ice cream. Please try again.');
        }
    }
}
