<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\Product;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $product=Product::with('product_images')->find($request->id);
        if($product == null)
        {
            return response()->json([
                'status'=>false,
                'message'=>'Product not found'
            ]);
        }
        if(Cart::count()>0)
        {
            //echo "Product already in cart";
            //Products found in cart
            //Check if this product already in cart
            //Return as message that product is laready added in your cart
            //if product not found in the cart , then add product in cart

            $cartContent=Cart::content();
            $productAlreadyExist =false;
            foreach ($cartContent as $item)
            {
                if($item->id==$product->id)
                {
                    $productAlreadyExist=true;

                }
            }
            if($productAlreadyExist ==false)
            {
                Cart::add($product->id,$product->title,1,$product->price,['productImage'=>(!empty($product->product_images))? $product->product_images->first():'']);
                $status =true;
                $message=$product->title."  added in Cart";
                session()->flash('success',$message);
            }
            else{
                $status =false;
                $message=$product->title."  already added in Cart";
            }


        }
        else{
            echo "Cart is Empty now addding a rproduct in cart";
            Cart::add($product->id,$product->title,1,$product->price,['productImage'=>(!empty($product->product_images))? $product->product_images->first():'']);
            $status =true;
            $message=$product->title."added in Cart";
            session()->flash('success',$message);
        }
        return response()->json([
            'status'=>$status,
            'message'=>$message
        ]);
    }

    public function cart()
    {
      $cartContent=Cart::content();
       $data['cartContent']=$cartContent;
      return view('front.cart',$data); 
    }

    public function updateCart(Request $request)
    {
      $rowId=$request->rowId;
      $qty=$request->qty;
      $itemInfo=Cart::get($rowId);
      $product=Product::find($itemInfo->id);
      //Check stock 
      if($product->track_qty == "Yes")
      {
        if($qty<=$product->qty)
        {
            Cart::update($rowId,$qty);
            $message="Cart updated successfully";
            $status=true;
            session()->flash('success',$message);

        }
        else{
            $message ="Requested quantity($qty) not avaiable in stock";
            $status=false;
            session()->flash('error',$message);
        }
      }
      else
      {

        Cart::update($rowId,$qty);
            $message="Cart updated successfully";
            $status=true;
            session()->flash('success',$message);
      }
     
      return response()->json([
        'status'=>$status,
        'message'=>$message
      ]);
    }

    public function deleteItem(Request $request)
    {
        $itemInfo=Cart::get($request->rowId);
        if($itemInfo == null)
        {
            $message="Item not found in cart";
            session()->flash('error',$message);
            return response()->json([
                'status'=>false,
                'message'=>$message
            ]); 
        }
        Cart::remove($request->rowId);
        $message="Item Remove From Cart Successfully";
        session()->flash('success',$message);
        return response()->json([
            'status'=>true,
            'message'=>$message
        ]); 

    }
}
