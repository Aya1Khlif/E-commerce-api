<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = Category::paginate(10);
            return response()->json($categories, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to fetch categories'], 500);
        }
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
            $validated = $request->validate([
                'name' => 'required|unique:categories,name',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            $category = new Category();
            $category->name = $request->name;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/uploads/category'), $filename);
                $category->image = $filename;
            }

            $category->save();

            return response()->json(['message' => 'Category added successfully'], 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = Category::find($id);

        if ($category) {
            return response()->json($category, 200);
        } else {
            return response()->json(['message' => 'Category not found'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|unique:categories,name,' . $id,
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            $category = Category::find($id);

            if ($category) {
                if ($request->hasFile('image')) {
                    // حذف الصورة القديمة إذا كانت موجودة
                    $oldImagePath = public_path('assets/uploads/category/' . $category->image);
                    if (File::exists($oldImagePath)) {
                        File::delete($oldImagePath);
                    }

                    // تحميل الصورة الجديدة
                    $file = $request->file('image');
                    $filename = time() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('assets/uploads/category'), $filename);
                    $category->image = $filename;
                }

                $category->name = $request->name;
                $category->save();

                return response()->json(['message' => 'Category updated successfully'], 200);
            } else {
                return response()->json(['message' => 'Category not found'], 404);
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
            $category = Category::find($id);

            if ($category) {
                // حذف الصورة المرتبطة بالفئة إذا كانت موجودة
                $imagePath = public_path('assets/uploads/category/' . $category->image);
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
                $category->delete();
                return response()->json(['message' => 'Category deleted successfully'], 200);
            } else {
                return response()->json(['message' => 'Category not found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
