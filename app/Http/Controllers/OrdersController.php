<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Order_items;
use App\Models\Orders;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Orders::with('user', 'items.product')->paginate(20);

        foreach ($orders as $order) {
            foreach ($order->items as $order_item) {
                $order_item->product_name = $order_item->product->name;
            }
        }

        return response()->json($orders, 200);
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
        try {
            $location = Location::where('user_id', Auth::id())->first();
            $request->validate(
                [
                    'order_items' => 'required',
                    'total_price' => 'required',
                    'quantity' => 'required',
                    'date_of_delivery' => 'required',
                ]
            );
            //create order
            $order = new Orders();
            $order->user_id = Auth::id();
            $order->location_id = $location->id;
            $order->total_price = $request->total_price;
            $order->data_of_delivery = $request->date_of_delivery;
            $order->save();
            foreach ($request->order_items as $order_items) {
                $item = new Order_items();
                $item->order_id = $order->id;
                $item->price = $order_items['price'];
                $item->product_id = $order_items['product_id'];
                $item->quantity = $order_items['quantity'];
                $item->save();
                //crate product order
                $product = Products::where('id', $order_items['product_id'])->first();
                $product->quantity = $order_items['quantity'];
                $product->save();
            }
            return response()->json('order is added', 200);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Orders $orders, $id)
    {
        $order = Orders::find($id);
        return response()->json($order, 200);
    }
    /**
     * get order items by id
     */
    public function get_order_items($id)
    {
        $order_items = Order_items::where('order_id', $id)->get();
        if ($order_items) {
            foreach ($order_items as $order_item) {
                $product = Products::where('id', $order_item->product_id)->pluck('name');
                $order_item->product_name = $product['0'];
            }
            return response()->json($order_items);
        } else  return response()->json('no item found ');
    }
    /**
     * get order user by id
     */
    public function get_user_id($id)
    {
        $orders = Orders::where('user_id', $id)->with('items.product')->get();

        foreach ($orders as $order) {
            foreach ($order->items as $order_item) {
                $order_item->product_name = $order_item->product->name;
            }
        }

        return response()->json($orders);
    }

    public function change_order_status($id, Request $request)
    {
        $order = Orders::find($id);
        if ($order) {
            $order->update(['status' => $request->status]);
            return response()->json('Status Changeed successfully');
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Orders $orders)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $order = Orders::findOrFail($id);

            $request->validate([
                'order_items' => 'required|array',
                'total_price' => 'required|numeric',
                'date_of_delivery' => 'required|date',
            ]);

            // تحديث بيانات الطلب
            $order->total_price = $request->total_price;
            $order->date_of_delivery = $request->date_of_delivery;
            $order->save();

            // حذف العناصر القديمة
            Order_items::where('order_id', $order->id)->delete();

            // إضافة العناصر الجديدة
            foreach ($request->order_items as $order_item) {
                $item = new Order_items();
                $item->order_id = $order->id;
                $item->product_id = $order_item['product_id'];
                $item->quantity = $order_item['quantity'];
                $item->price = $order_item['price'];
                $item->save();
            }

            return response()->json(['message' => 'Order updated successfully', 'order' => $order], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $order = Orders::find($id);
        if ($order) {
            $order->delete();
            return response()->json(['message' => 'Order deleted successfully.']);
        } else {
            return response()->json(['message' => 'Order not found.'], 404);
        }
    }
}
