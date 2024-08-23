<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Products::paginate(10);
        if ($products) {
            return response()->json($products, 200);
        } else    return response()->json('no products');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'discount' => 'nullable|numeric',
            'amount' => 'required|integer|min:1',
            'image' => 'required|image',
        ]);

        try {
            $products = new Products();
            $products->name = $validatedData['name'];
            $products->price = $validatedData['price'];
            $products->category_id = $validatedData['category_id'];
            $products->brand_id = $validatedData['brand_id'];
            $products->discount = $validatedData['discount'];
            $products->amount = $validatedData['amount'];

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/uploads/category'), $filename);
                $products->image = $filename;
            }

            $products->save();
            return response()->json('Product added successfully', 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while adding the product'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Products $products, $id)
    {
        $products = Products::find($id);
        if ($products) {
            return response()->json($products, 200);
        } else  return response()->json('no products found');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Products $products)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required|numeric',
            'brand_id' => 'required|numeric',
            'discount' => 'required|numeric',
            'amount' => 'required|numeric',
            'image' => 'required'
        ]);
        $products=Products::find($id);
        if($products){
            $products->name = $request->name;
            $products->price = $request->price;
            $products->category_id = $request->category_id;
            $products->brand_id = $request->brand_id;
            $products->discount = $request->discount;
            $products->amount = $request->amount;
            $products->image = $request->image;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/uploads/category'), $filename);
                $products->image = $filename;
            }

            $products->save();
            return response()->json('products updated',201);
        }else return response()->json('products not found');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Products $products ,$id)
    {
        $products=Products::find($id);
        if($products){
            $products->delete();
            return response()->json('products deleted',201);
        }else return response()->json('products not found');
    }
}
