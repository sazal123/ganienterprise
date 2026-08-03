<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Productprice;
use App\Models\Product;
use Toastr;
use Cart;
use DB;
class ShoppingController extends Controller
{

    public function addTocartGet($id, Request $request){
        $qty = 1;
        $productInfo = Product::where('id', $id)->first();
        $cartImage = $productInfo->image->image ?? 'frontEnd/img/default-product.jpg';

        if ($request->product_color) {
            $colorModel = \App\Models\Color::where('colorName', $request->product_color)->first();
            if ($colorModel) {
                $colorImg = \App\Models\Productimage::where('product_id', $id)
                    ->where('color_id', $colorModel->id)
                    ->first();
                if (!$colorImg) {
                    $productcolors = \App\Models\Productcolor::where('product_id', $id)->get();
                    $colorIndex = $productcolors->pluck('color_id')->search($colorModel->id);
                    if ($colorIndex !== false) {
                        $allImages = \App\Models\Productimage::where('product_id', $id)->get();
                        $colorImg = $allImages->get($colorIndex);
                    }
                }
                if ($colorImg && !empty($colorImg->image)) {
                    $cartImage = $colorImg->image;
                }
            }
        }

        $cartinfo = Cart::instance('shopping')->add([
            'id' => $productInfo->id,
            'name' => $productInfo->name,
            'qty' => $qty,
            'price' => $productInfo->new_price,
            'options' => [
                'image' => $cartImage,
                'old_price' => $productInfo->old_price,
                'slug' => $productInfo->slug,
                'purchase_price' => $productInfo->purchase_price,
                'product_color' => $request->product_color,
                'product_size' => $request->product_size,
            ]
        ]);

        return response()->json($cartinfo);
    }

    public function cart_store(Request $request)
    {
        $product = Product::where(['id' => $request->id])->first();

        // Determine variant price based on selected color/size
        $variantPrice = $product->new_price;

        if ($request->product_color) {
            $colorVariant = \App\Models\Productcolor::where('product_id', $product->id)
                ->whereHas('color', function($q) use ($request) {
                    $q->where('colorName', $request->product_color);
                })
                ->whereNotNull('price')
                ->first();
            if ($colorVariant && $colorVariant->price > 0) {
                $variantPrice = $colorVariant->price;
            }
        }

        if ($request->product_size) {
            $sizeVariant = \App\Models\Productsize::where('product_id', $product->id)
                ->whereHas('size', function($q) use ($request) {
                    $q->where('sizeName', $request->product_size);
                })
                ->whereNotNull('price')
                ->first();
            if ($sizeVariant && $sizeVariant->price > 0) {
                $variantPrice = $sizeVariant->price;
            }
        }

        // Determine product image based on selected color
        $cartImage = $product->image->image ?? 'frontEnd/img/default-product.jpg';

        if ($request->product_color) {
            $colorModel = \App\Models\Color::where('colorName', $request->product_color)->first();
            if ($colorModel) {
                $colorImg = \App\Models\Productimage::where('product_id', $product->id)
                    ->where('color_id', $colorModel->id)
                    ->first();

                if (!$colorImg) {
                    $productcolors = \App\Models\Productcolor::where('product_id', $product->id)->get();
                    $colorIndex = $productcolors->pluck('color_id')->search($colorModel->id);
                    if ($colorIndex !== false) {
                        $allImages = \App\Models\Productimage::where('product_id', $product->id)->get();
                        $colorImg = $allImages->get($colorIndex);
                    }
                }

                if ($colorImg && !empty($colorImg->image)) {
                    $cartImage = $colorImg->image;
                }
            }
        }

        Cart::instance('shopping')->add([
            'id' => $product->id,
            'name' => $product->name,
            'qty' => $request->qty,
            'price' => $variantPrice,
            'options' => [
                'slug' => $product->slug,
                'image' => $cartImage,
                'old_price' => $product->new_price,
                'purchase_price' => $product->purchase_price,
                'product_size' => $request->product_size,
                'product_color' => $request->product_color,
                'pro_unit' => $request->pro_unit,
            ],
        ]);

        Toastr::success('Product successfully add to cart', 'Success!');
        return redirect()->route('customer.checkout');

    }
    public function cart_update(Request $request)
    {
        // Get the row ID of the cart item
        $rowId = $request->id;
        // Fetch the current cart item using the row ID
        $cartItem = Cart::instance('shopping')->get($rowId);
        if ($cartItem) {
            $product = Product::find($cartItem->id);
            $newColor = $request->product_color ?: $cartItem->options->product_color;
            $newSize = $request->product_size ?: $cartItem->options->product_size;
            $cartImage = $cartItem->options->image;

            if ($newColor && $product) {
                $colorModel = \App\Models\Color::where('colorName', $newColor)->first();
                if ($colorModel) {
                    $colorImg = \App\Models\Productimage::where('product_id', $product->id)
                        ->where('color_id', $colorModel->id)
                        ->first();

                    if (!$colorImg) {
                        $productcolors = \App\Models\Productcolor::where('product_id', $product->id)->get();
                        $colorIndex = $productcolors->pluck('color_id')->search($colorModel->id);
                        if ($colorIndex !== false) {
                            $allImages = \App\Models\Productimage::where('product_id', $product->id)->get();
                            $colorImg = $allImages->get($colorIndex);
                        }
                    }

                    if ($colorImg && !empty($colorImg->image)) {
                        $cartImage = $colorImg->image;
                    }
                }
            }

            // Update the options for the cart item
            Cart::instance('shopping')->update($rowId, [
                'options' => [
                    'product_size' => $newSize,
                    'product_color' => $newColor,
                    'slug' => $cartItem->options->slug,
                    'image' => $cartImage,
                    'old_price' => $cartItem->options->old_price,
                    'purchase_price' => $cartItem->options->purchase_price,
                    'pro_unit' => $cartItem->options->pro_unit,
                ],
            ]);
        }

        $data = Cart::instance('shopping')->content();
        return view('frontEnd.layouts.ajax.cart', compact('data'));
    }
    public function cart_remove(Request $request)
    {
        $remove = Cart::instance('shopping')->update($request->id, 0);
        $data = Cart::instance('shopping')->content();
        return view('frontEnd.layouts.ajax.cart', compact('data'));
    }
    public function cart_increment(Request $request)
    {
        $item = Cart::instance('shopping')->get($request->id);
        $qty = $item->qty + 1;
        $increment = Cart::instance('shopping')->update($request->id, $qty);
        $data = Cart::instance('shopping')->content();
        return view('frontEnd.layouts.ajax.cart', compact('data'));
    }
    public function cart_decrement(Request $request)
    {
        $item = Cart::instance('shopping')->get($request->id);
        $qty = $item->qty - 1;
        $decrement = Cart::instance('shopping')->update($request->id, $qty);
        $data = Cart::instance('shopping')->content();
        return view('frontEnd.layouts.ajax.cart', compact('data'));
    }
    public function cart_count(Request $request)
    {
        $data = Cart::instance('shopping')->count();
        return view('frontEnd.layouts.ajax.cart_count', compact('data'));
    }
    public function mobilecart_qty(Request $request)
    {
        $data = Cart::instance('shopping')->count();
        return view('frontEnd.layouts.ajax.mobilecart_qty', compact('data'));
    }
    public function changeProduct(Request $request)
    {


        // Get the selected product
        $productId = $request->input('id');
        $product = Product::find($productId); // Fetch the product by ID



        if ($product) {
            // Clear existing items in the cart if necessary
            Cart::instance('shopping')->destroy(); // Or adjust this logic as needed

            // Add the selected product to the cart
            Cart::instance('shopping')->add([
                'id' => $product->id,
                'name' => $product->name,
                'qty' => 1, // Adjust quantity as needed
                'price' => $product->new_price,
                'options' => [
                    'slug' => $product->slug,
                    'image' => $product->image->image,
                    'old_price' => $product->old_price,
                    'purchase_price' => $product->purchase_price,
                ],
            ]);
            $data = Cart::instance('shopping')->content();
            return view('frontEnd.layouts.ajax.cart', compact('data'));

        }

        return response()->json(['success' => false, 'message' => 'Product not found.']);
    }

}
