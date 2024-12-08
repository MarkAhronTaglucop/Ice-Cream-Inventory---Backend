<?php

namespace App\Http\Controllers;

use App\Models\IceCream;
use Illuminate\Http\Request;

class IceCreamController extends Controller
{
    // Fetch all ice creams for employee dashboard
    public function index()
    {
        // Get all ice creams or only available ones
        $iceCreams = IceCream::all(); // You can use where('status', 'Available') to filter

        return response()->json($iceCreams);
    }

    // Store a new ice cream (for adding)
    public function store(Request $request)
    {
        // Validate incoming data
        $request->validate([
            'flavor' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|url', // Assuming image URLs are provided
        ]);

        // Create a new ice cream record
        $iceCream = IceCream::create([
            'flavor' => $request->flavor,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'status' => 'Available',  // Set default status
            'image' => $request->image ?? null,
        ]);

        // Return a response
        return response()->json(['message' => 'Ice cream added successfully', 'iceCream' => $iceCream]);
    }

    // Update an ice cream (for editing)
    public function update(Request $request, $id)
    {
        // Find the ice cream by ID
        $iceCream = IceCream::findOrFail($id);

        // Validate the updated data
        $request->validate([
            'flavor' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|url',
        ]);

        // Update the ice cream
        $iceCream->update([
            'flavor' => $request->flavor,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'status' => $request->status ?? $iceCream->status,
            'image' => $request->image ?? $iceCream->image,
        ]);

        // Return a response
        return response()->json(['message' => 'Ice cream updated successfully', 'iceCream' => $iceCream]);
    }

    // Delete an ice cream
    public function destroy($id)
    {
        // Find the ice cream by ID and delete it
        $iceCream = IceCream::findOrFail($id);
        $iceCream->delete();

        // Return a response
        return response()->json(['message' => 'Ice cream deleted successfully']);
    }
}
