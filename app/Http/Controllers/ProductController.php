<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Market;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Favorite;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

    public function search(Request $request)
    {

        $query = $request->input('query');
        Log::info('Search query received', ['query' => $query]);

        DB::enableQueryLog();

        $products = Product::where('name', 'like', '%' . $query . '%')->get();
        Log::info('Products fetched', ['products' => $products]);

        $markets = Market::where('name', 'like', '%' . $query . '%')->get();
        Log::info('Markets fetched', ['markets' => $markets]);

        Log::info('Executed queries', ['queries' => DB::getQueryLog()]);

        return response()->json([
            'products' => $products,
            'markets' => $markets,
        ]);
    }

    public function create_order(Request $request)
    {
        $data = $request->all();

        // Validate the input
        $validator = Validator::make($data, [
            'cart_item_id' => 'required|array',
            'cart_item_id.*' => 'required|numeric',
            'address' => 'string|nullable'
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

        $user = User::find($user_id);
        if (!$user) {
            return response()->json(['message' => 'User profile not found'], 400);
        }

        $address = $data['address'] ?? $user->address;

        // Retrieve cart items

        $cartItems = CartItem::whereIn('id', $data['cart_item_id'])->get();

        if ($cartItems->count() !== count($data['cart_item_id'])) {
            return response()->json(['message' => 'One or more cart items not found'], 400);
        }


        $total_price = 0;

        foreach ($cartItems as $cartItem) {

            $product=Product::where('products.id',$cartItem->product_id)->first();

            if($cartItem->quantity>$product->quantity){

                return response()->json(['message' => 'One or more quantity not available'], 400);

            }
        }
        // Calculate total price
        foreach ($cartItems as $cartItem) {

            $product=Product::where('products.id',$cartItem->product_id)->first();

            $total_price += $cartItem->product->price * $cartItem->quantity;
        }

        $order = Order::create([
            'user_id' => $user_id,
            'status' => 'pending',
            'total_price' => $total_price,
            'address' => $address
        ]);

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

        CartItem::whereIn('id', $data['cart_item_id'])->delete();

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order,
            'orderItem'=>$Items
        ], 201);
    }

    public function edit_order(Request $request, $order_id)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'address' => 'required|string',
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

        $order = Order::where('id', $order_id)->where('user_id', $user_id)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found or access denied'], 404);
        }

        $order->address = $data['address'];
        $order->save();

        return response()->json([
            'message' => 'Order address updated successfully',
            'order' => $order,
        ], 200);
    }

    public function delete_order($order_id)
    {
        $user_id = Auth::id();

        if (!$user_id) {
            return response()->json(['message' => 'User not found'], 400);
        }

        $order = Order::where('id', $order_id)->where('user_id', $user_id)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found or access denied'], 404);
        }

        OrderItem::where('order_id', $order->id)->delete();

        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully',
        ], 200);
    }

    public function show_order(Request $request, $order_id)
    {
        $user_id = Auth::id();

        if (!$user_id) {
            return response()->json(['message' => 'User not found'], 400);
        }

        $order = Order::where('id', $order_id)
                    ->where('user_id', $user_id)
                    ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $orderItems = DB::table('order_items')
            ->where('order_id', $order->id)
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->select('products.name as product_name', 'products.price as product_price', 'order_items.*')
            ->get();

        return response()->json([
            'order' => $order,
            'order_items' => $orderItems,
        ], 200);
    }

    public function show_all_orders(Request $request)
    {
        $user_id = Auth::id();

        if (!$user_id) {
            return response()->json(['message' => 'User not found'], 400);
        }

        $orders = Order::where('user_id', $user_id)->get();

        if ($orders->isEmpty()) {
            return response()->json(['message' => 'No orders found'], 404);
        }

        $ordersData = [];
        foreach ($orders as $order) {
            $orderItems = DB::table('order_items')
                ->where('order_items.order_id', $order->id)
                ->join('products', 'products.id', '=', 'order_items.product_id')
                ->select('products.name', 'products.price', 'order_items.quantity')
                ->get();

            $ordersData[] = [
                'order_id' => $order->id,
                'status' => $order->status,
                'total_price' => $order->total_price,
                'address' => $order->address,
                'created_at' => $order->created_at,
                'order_items' => $orderItems,
            ];
        }

        return response()->json([
            'message' => 'Orders retrieved successfully',
            'orders' => $ordersData,
        ], 200);
    }

    public function show_profile(Request $request)
    {
        $user = Auth::user();

        if (!$user)
        {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'message' => 'Profile retrieved successfully',
            'profile' => $user,
        ], 200);
    }

    public function edit_profile(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $data = $request->only(['first_name', 'last_name', 'phone', 'image_url', 'address']);
        $validator = Validator::make($data, [
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'phone' => 'nullable|string|max:15',
            'image_url' => 'nullable|url',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            $errorText = implode(", ", $errorMessages);

            return response()->json([
                "message" => $errorText,
            ], 400);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'profile' => $user,
        ], 200);
    }


    public function addToFavorites($product_id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 401);
        }

        $product = Product::find($product_id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $exists = Favorite::where('user_id', $user->id)
            ->where('product_id', $product_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Product is already in favorites'], 400);
        }

        Favorite::create([
            'user_id' => $user->id,
            'product_id' => $product_id,
        ]);

        return response()->json(['message' => 'Product added to favorites'], 201);
    }


    public function removeFromFavorites($product_id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 401);
        }

        $favorite = Favorite::where('user_id', $user->id)
            ->where('product_id', $product_id)
            ->first();

        if (!$favorite) {
            return response()->json(['message' => 'Product is not in favorites'], 404);
        }


        $favorite->delete();

        return response()->json(['message' => 'Product removed from favorites'], 200);
    }


    public function getFavorites()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 401);
        }

        $favorites = Favorite::where('user_id', $user->id)
            ->with('product')
            ->get();

        return response()->json(['favorites' => $favorites], 200);
    }

}
