<?php

namespace App\Http\Controllers\Front;

use Log;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Cart\CartRepository;
use App\Repositories\Cart\CartModelRepository;

class CartController extends Controller
{
    // protected $cart;
    // public function __construct(CartRepository $cart)
    // {
    //     $this->cart= $cart;
    // }
    /**
     * Display a listing of the resource.
     */
    public function index(CartRepository $cart)
    {
        // $repository = new CartModelRepository();
        // $items = $cart->get();

        return view("front.cart", ["cart" => $cart]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, CartRepository $cart)
    {
        $request->validate([
            "product_id" => "required|int|exists:products,id",
            "quantity" => "nullable|int|min:1"
        ]);

        $product = Product::findOrFail($request->post("product_id"));
        $cart->add($product, $request->post("quantity"));
        if($request->expectsJson()){
            return response()->json([
                "message"=>"item added to cart !",
            ],201);
        }

        return redirect()->route("cart.index")->with("success","product added to cart !");
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            "quantity" => "required|int|min:1"
        ]);
        $cartItem = Cart::findOrFail($id);
        $cartItem->quantity = $request->input("quantity");
        $cartItem->save();
    }
    // public function update(Request $request, $id)
    // {
    //     // التحقق من صحة الطلب
    //     $request->validate([
    //         "quantity" => "required|int|min:1"
    //     ]);

    //     try {
    //         // البحث عن العنصر في السلة باستخدام الـ id
    //         $cartItem = Cart::findOrFail($id);
    //         $cartItem->quantity = $request->input("quantity");
    //         $cartItem->save();

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Cart updated successfully',
    //             'quantity' => $cartItem->quantity
    //         ]);
    //     } catch (ValidationException $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Validation error: ' . $e->getMessage()
    //         ], 422);
    //     } catch (\Exception $e) {
    //         // تسجيل الخطأ في سجل Laravel
    //         Log::error('Error updating cart item: '.$e->getMessage());
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Failed to update cart'
    //         ], 500);
    //     }
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CartRepository $cart, $id)
    {

        $cart->delete($id);
        return response()->json([
            "message"=>"item deleted !",
        ]);
    }
}
