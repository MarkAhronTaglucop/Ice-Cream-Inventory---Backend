<?php

namespace App\Http\Controllers;

use App\Models\IceCream; // Make sure you have an IceCream model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    // Display ice cream inventory to the employee
    public function display_info()
    {
        // Get the list of available ice creams (with their details)
        $iceCreams = IceCream::where('status', 'Available')->get(); // Only show available ice creams

        return Inertia::render('Employee', [
            'iceCreams' => $iceCreams, // Passing ice creams data to the front-end
        ]);
    }

    // Search for ice creams by flavor or description
    public function search(Request $request)
    {
        $searchQuery = $request->input('searchQuery', '');

        if (empty($searchQuery)) {
            return Inertia::render('Employee', [
                'searchedIceCreams' => []
            ]);
        }

        try {
            // Search for ice creams by flavor or description
            $searchedIceCreams = IceCream::where('flavor', 'like', '%' . $searchQuery . '%')
                ->orWhere('description', 'like', '%' . $searchQuery . '%')
                ->where('status', 'Available') // Only show available ice creams
                ->get();
        } catch (\Exception $e) {
            return Inertia::render('Employee', [
                'searchedIceCreams' => [],
                'error' => $e->getMessage() // Optional: Include error for debugging
            ]);
        }

        return Inertia::render('Employee', [
            'searchedIceCreams' => $searchedIceCreams
        ]);
    }

    // Allow employees to add a new ice cream to the inventory
    public function create(Request $request)
    {
        $validated = $request->validate([
            'flavor' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'status' => 'required|in:Available,Out of Stock',
            'image' => 'nullable|url', // Assuming image URLs are provided
        ]);

        try {
            // Create a new ice cream record
            $iceCream = IceCream::create([
                'flavor' => $validated['flavor'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'status' => $validated['status'],
                'image' => $validated['image'] ?? null,
            ]);

            return Inertia::render('Employee', [
                'message' => 'Ice cream added successfully!',
                'iceCreams' => IceCream::all() // Refresh ice cream list
            ]);

        } catch (\Exception $e) {
            return Inertia::render('Employee', [
                'error' => $e->getMessage(),
                'iceCreams' => IceCream::all()
            ]);
        }
    }

    // Allow employees to update ice cream details
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'flavor' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'status' => 'required|in:Available,Out of Stock',
            'image' => 'nullable|url',
        ]);

        try {
            // Find the ice cream by ID
            $iceCream = IceCream::findOrFail($id);

            // Update the ice cream record
            $iceCream->update([
                'flavor' => $validated['flavor'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'status' => $validated['status'],
                'image' => $validated['image'] ?? $iceCream->image,
            ]);

            return Inertia::render('Employee', [
                'message' => 'Ice cream updated successfully!',
                'iceCreams' => IceCream::all() // Refresh ice cream list
            ]);

        } catch (\Exception $e) {
            return Inertia::render('Employee', [
                'error' => $e->getMessage(),
                'iceCreams' => IceCream::all()
            ]);
        }
    }

    // Allow employees to delete ice cream from the inventory
    public function destroy($id)
    {
        try {
            // Find the ice cream by ID and delete it
            $iceCream = IceCream::findOrFail($id);
            $iceCream->delete();

            return Inertia::render('Employee', [
                'message' => 'Ice cream deleted successfully!',
                'iceCreams' => IceCream::all() // Refresh ice cream list
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Employee', [
                'error' => $e->getMessage(),
                'iceCreams' => IceCream::all()
            ]);
        }
    }
}
