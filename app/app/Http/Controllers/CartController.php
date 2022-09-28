<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Product;
use App\ProductVariation;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function storeImage($image){
        $file = request()->file('images');
        foreach($file  as $image){
          //  dd($image);
        $name =  $image->getClientOriginalName();
        $FileName = \pathinfo($name, PATHINFO_FILENAME);
        $ext =  $image->getClientOriginalExtension();
        $time = time().$FileName;
        $fileName = $time.'.'.$ext;
        $image->move('images/products/', $fileName);
        $images[] = $fileName;
    }
        return $images;
     }
    public function add(Request $request, $id)
    { 
        
        $product = Product::find($id);
        $res = \Cart::add([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->sale_price,
            'qty' => $request->qty,
            'image' => $product->image,
            'weight'=>1, 
        ])->associate('App\Product');
    
        if($res){
         //   dd($res);
            return response()->json($res);
        }
    }

    public function index()
    { 
      //  dd(\Cart::content());
        return view('users.products.carts')
        ->with('cart', \Cart::content())
        ->with('breadcrumb', 'Shopping Cart');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }
   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function remove()
    {
        \Cart::destroy();
        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $dd = ProductVariation::where(['product_id' => decrypt($id), 'qty' => $request->qty])->first();
        $product = [
            'price' => $dd->price,
            'qty' =>  request()->qty
        ];
           $cart =  \Cart::update(request()->rowId,$product);
          // dd($cart);
           \Session()->flash('message', 'Cart Updated Successfully');
            return redirect()->back();
    }

 


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
      //dd($id.' '.$request->rowId);
        \Cart::remove($id);
        \Session()->flash('message', 'Cart Deleted Successfully');
        return back();
    }
}
