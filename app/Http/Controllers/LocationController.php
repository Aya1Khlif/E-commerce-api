<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::where('user_id', Auth::id())->paginate(10);
        return response()->json($locations, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::check()) {
            $userId = Auth::id();
        } else {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $request->validate([
            'street' => 'required|string|max:255',
            'building' => 'required|string|max:255',
            'area' => 'required|string|max:255',
        ]);

        $location = new Location();
        $location->user_id = $userId;
        $location->street = $request->street;
        $location->area = $request->area;
        $location->building = $request->building;
        $location->save();
        return response()->json('Location added', 201);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $location = Location::find($id);

        if ($location && $location->user_id == Auth::id()) {
            return response()->json($location, 200);
        }

        return response()->json(['message' => 'Location not found or access denied'], 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'street' => 'required|string|max:255',
            'building' => 'required|string|max:255',
            'area' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $location = Location::find($id);

        if ($location && $location->user_id == Auth::id()) {
            $location->update([
                'street' => $request->street,
                'building' => $request->building,
                'area' => $request->area,
            ]);

            return response()->json(['message' => 'Location updated successfully', 'location' => $location], 200);
        }

        return response()->json(['message' => 'Location not found or access denied'], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $location = Location::find($id);

        if ($location && $location->user_id == Auth::id()) {
            $location->delete();
            return response()->json(['message' => 'Location deleted successfully'], 200);
        }

        return response()->json(['message' => 'Location not found or access denied'], 404);
    }
}
