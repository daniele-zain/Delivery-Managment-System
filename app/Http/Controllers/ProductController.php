<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Market;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;

class ProductController extends Controller
{
    public function show_markets(){
        $markets=Market::all();

        return response()->json($markets,200);
    }

    public function show_products_in_market(Request $request){
        $data = $request->all();
        $validator = Validator::make($data, [
            'market_id' => 'required|numeric',
            ]);
        
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all(); // Get all error messages as an array
            $errorText = implode(", ", $errorMessages); // Join the error messages into a single string
        
            return response()->json([
                "message" => $errorText, // Return the errors as a single string
            ], 400);
        }
        
        $products=Product::where('market_id',$data['market_id'])->get();

        if(count($products)==0){

            return response()->json('No products found',400);

        }
        return response()->json($products,200);

    }

    public function show_product_detaild(Request $request){
        $data = $request->all();
        $validator = Validator::make($data, [
            'product_id' => 'required|numeric',
            ]);
        
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all(); // Get all error messages as an array
            $errorText = implode(", ", $errorMessages); // Join the error messages into a single string
        
            return response()->json([
                "message" => $errorText, // Return the errors as a single string
            ], 400);
        }
        
        $product=Product::where('id',$data['product_id'])->first();

        if(!$product){

            return response()->json('product not found',400);

        }
        return response()->json($product,200);

    }

    public function add_to_cart(Request $request){

        $data = $request->all();
        $validator = Validator::make($data, [

            'product_id' => 'required|numeric',
            'quantity'=>'required|numeric',
        ]);
        
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all(); // Get all error messages as an array
            $errorText = implode(", ", $errorMessages); // Join the error messages into a single string
        
            return response()->json([
                "message" => $errorText, // Return the errors as a single string
            ], 400);
        }

        $user_id=Auth::id();

       

        if(!$user_id){
            return response()->json(['user not found'],400);
        }

        $product=DB::table('products')
        ->where('products.id',$data['product_id'])
        ->first();

        if(!$product){

            return response()->json(['product not found'],400);
        }

        $check=CartItem::
        where('cart_items.product_id',$product->id)
        ->where('cart_items.user_id',$user_id)
        ->first();

        if($check){
            return response()->json(['already exist'],400);

        }

        $cart = CartItem::create([
            'user_id'=>$user_id,
            'product_id'=>$product->id,
            'quantity'=>$data['quantity']
        ]);

        return response()->json(['added to the cart',$product,$cart],200);

    }

    public function show_cart(){
     
        $user_id=Auth::id();

        if(!$user_id){
            return response()->json(['user not found'],400);
        }

        $cart=DB::table('cart_items')
        ->where('cart_items.user_id',$user_id)
        ->join('products','products.id','cart_items.product_id')
        ->select('products.*','cart_items.*')
        ->get();

        if(count($cart)==0){

            return response()->json(['there is no products in your cart'],400);
        }

        return response()->json($cart,200);

    }

    public function create_order(Request $request)
    {
        $data = $request->all();

        // Validate the input
        $validator = Validator::make($data, [
            'cart_item_id' => 'required|array',
            'cart_item_id.*' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            $errorText = implode(", ", $errorMessages);

            return response()->json([
                "message" => $errorText,
            ], 400);
        }

        $user_id = Auth::id();

        if (!$user_id) {
            return response()->json(['message' => 'User not found'], 400);
        }

        // Retrieve cart items
        
        $cartItems = CartItem::whereIn('id', $data['cart_item_id'])->get(); 

        if ($cartItems->count() !== count($data['cart_item_id'])) {
            return response()->json(['message' => 'One or more cart items not found'], 400);
        }


        $total_price = 0;

        // Calculate total price
        foreach ($cartItems as $cartItem) {
            //$product=Product::where('id',$cartItem['product_id'])->get();
            $total_price += $cartItem->product->price * $cartItem->quantity; 
        }

        // Create the order
        $order = Order::create([
            'user_id' => $user_id,
            'status' => 'pending', // Default status
            'total_price' => $total_price,
        ]);

        // Create order items
        $orderItems_id=[];
        foreach ($cartItems as $cartItem) {
            $orderItem=OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id, // Assuming CartItem has a 'product_id' field
                'quantity' => $cartItem->quantity,
            ]);
            $orderItems_id[]=$orderItem->id;
        }
  
        $Items = DB::table('order_items')
        ->wherein('order_items.id',$orderItems_id)
        ->join('products','products.id','order_items.product_id')
        ->select('products.*','order_items.*')
        ->get();

        // Optionally, clear the cart after order creation
        //CartItem::whereIn('id', $data['cart_item_id'])->delete();

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order,
            'orderItem'=>$Items
        ], 201);
    }
}
