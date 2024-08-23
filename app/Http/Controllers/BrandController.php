<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Exception;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::paginate(10);
        return response()->json($brands, 200);
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
        try {
            $request->validate([
                'name' => 'required|unique:brands,name'
            ]);

            $brand = new Brand();
            $brand->name = $request->name; // تصحيح تعيين القيم
            $brand->save();

            return response()->json(['message' => 'Brand added successfully'], 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $brand = Brand::find($id);

        if ($brand) {
            return response()->json($brand, 200);
        } else {
            return response()->json(['message' => 'Brand not found'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => "required|unique:brands,name,$id" // تأكد من أن التحقق يستثني السجل الحالي
            ]);

            $brand = Brand::find($id);

            if ($brand) {
                $brand->name = $request->name;
                $brand->save();

                return response()->json(['message' => 'Brand updated successfully'], 200);
            } else {
                return response()->json(['message' => 'Brand not found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $brand = Brand::find($id);

            if ($brand) {
                $brand->delete();
                return response()->json(['message' => 'Brand deleted successfully'], 200);
            } else {
                return response()->json(['message' => 'Brand not found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
