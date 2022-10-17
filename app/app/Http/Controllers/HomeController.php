<?php

namespace App\Http\Controllers;

use App\Category;
use App\Order;
use App\Product;
use Illuminate\Http\Request;
use App\OrderItem;
use Illuminate\Support\Carbon;
use App\Review;
use App\Notification;
use App\User;
use App\Menu;
use App\Slider;
use App\Shipping;
use App\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
 
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
               $products = session()->get('products.recently_viewed');
               if($products){
                $datas = array_slice(array_unique($products), -5, 5, true);
                $data['recents'] = Product::whereIn('id', $datas)->take(5)->get();
               }else{
                $data['recents']  = [];
               }
        return view('users.home', $data)
            ->with('title', 'Index') 
            ->with('products', Product::simplePaginate(30))
            ->with('sliders', Slider::get())
            ->with('categories', Category::take(4)->get());
    }

    public function productDetails($id){
        $id = decrypt($id);
   
        $product = Product::where('id', $id)->first();
        session()->push('products.recently_viewed', $product->getKey());
                Product::where('id', $id)->update(['views' => $product->views + 1]);
                $data['rating'] = Review::where('product_id', $product->id)->take(3)->orderBy('created_at', 'desc')->get();
                $products = session()->get('products.recently_viewed');
                $datas = array_slice(array_unique($products), -5, 5, true);
                $data['recent'] = Product::whereIn('id', $datas)->take(5)->get();
                $data['related']= product::where('category_id', $product->category_id)->take(5)->get();

        return view('users.products.products', $data)
                ->with('title', $product->name)
                ->with('product', $product)
                ->with('products', Product::latest()->take(5)->get())
                ->with('breadcrumb', $product->name)
                ->with('category', Category::latest()->get());
        }

        public function Account(){
          $title = "My Details";
          $addresses= DB::table('shippings')->where(['user_id'=> auth()->user()->id, 'is_default' => '1'])->get();
          return view('users.accounts.index')->with('addresses', $addresses)->with('title',$title );  
        }

        public function UserAddress(){
            $title = "My Details";
            $addresses= DB::table('shippings')->where(['user_id'=> auth()->user()->id])->paginate(4);  
            return view('users.accounts.address', compact('addresses', 'title'));
        }

        public function myOrders(){
            $orders = DB::table('orders')
            ->join('order_items', 'orders.order_No', '=' ,'order_items.order_No')
            ->where('orders.user_id', auth()->user()->id)
            ->latest('orders.created_at')
            ->paginate(8);
    return view('users.accounts.orders', compact('orders'));
        }


    public function orderDetails($id){
    
        $items= OrderItem::where(['order_No'=> decrypt($id)])->get();
        $orders = Order::where(['order_No' => decrypt($id)])->first();
        return view('users.accounts.orderDetails', compact('orders', 'items'));

    }


    public function recentViews(){

        $products = session()->get('products.recently_viewed');
        if(is_array($products)){
        $data = array_unique($products);
         $datas = array_slice($data, -10, 10, true);
         $products['recent'] = product::whereIn('id', $datas)->take(10)->latest()->get();
        }else{
            $products['recent'] = [];
        }
           return view('users.accounts.recent-view', $products);
        }

        
        public function myTransactions(){
            return view('users.accounts.transactions')
            ->with('transactions', Transaction::where(['user_id' =>auth()->user()->id])->latest()->simplePaginate(10))
            ->with('user', User::where('id', auth()->user()->id)->first());

        }


        public function UserDetails(){
            return view('users.accounts.profile');
        }

        public function updateDetails(Request $request){
            if($request->name){
             $update_user = [
                 'name' => $request->name,
                 'email'=> $request->email
             ];
    
             DB::table('users')
              ->where('id', auth()->user()->id)
               ->update($update_user);
    
               if(!isset($request->password)){
               return redirect()->back()->with('success', 'Details Updated Successfully');
            }
        }
    
            if($request->password){
             $this->validate($request, [
     
            'oldpassword' => 'required',
            'password' => 'required|min:6|confirmed',
            'password_confirmation'=>'required'
            ]);
     
           $hashedPassword = auth()->user()->password;
            
            if (\Hash::check($request->oldpassword , $hashedPassword)) {
            if (!\Hash::check($request->password , $hashedPassword)) {
                  $users =user::find(Auth()->user()->id);
                  $users->password = bcrypt($request->password);
                  user::where( 'id' , auth()->user()->id)->update( array( 'password' =>  $users->password));
                  return redirect()->back()->with('success', 'Details Updated Successfully');
                }
                else{return redirect()->back()->with('error', 'Old Password / New Password Cannot be the Same');}
            } else{
                return redirect()->back()->with('error', 'Old Password is Incorrect');
            }
        }
    }




    public function createAddresss(){

        $addresses['addresses']= DB::table('shippings')->where(['user_id'=> auth()->user()->id])->get();
        return view('users.accounts.add-address',$addresses);
    }

    public function Updateship($id){
        $addresses = shipping::find(decrypt($id));
        $addresses['addresses']= DB::table('shippings')->where(['id'=>  $addresses->id])->get();
        return view('users.accounts.update-address',$addresses);
    }

    public function AddressDelete($id){
        $address = Shipping::find(decrypt($id));
        $address->delete();
        return redirect()->back()->with('error', 'Address Deleted Successfully');
    }

    public function Shipping(Request $request, $id){  
        if($request->input('update')){
            $shipping = shipping::find($id);
            $shipping->receiver_name = $request->name;
            $shipping->address = $request->address;
            $shipping->receiver_phone = $request->phone;
            $shipping->receiver_email = $request->email;
            if(isset($request->default))
                {
                DB::table('shippings')
                ->where(['user_id'=>  auth()->user()->id, 'is_default' => '1'])
                ->update(['is_default' => 0]);
            $shipping->is_default = $request->default;
                }else{
                    $shipping->is_default = 0;
                }
            $shipping->zip_code = $request->postal_code;
            $shipping->city = $request->city;
            $shipping->state = $request->state;
            if($shipping->save()){
                return redirect()->back()->with('success', 'Address updated successfully');
            }
        }  
                $shipping = new Shipping;
                $shipping->user_id = $id;
                $shipping->receiver_name = $request->name;
                $shipping->address = $request->address;
                $shipping->receiver_phone = $request->phone;
                $test = DB::table('shippings')->where(['user_id' => auth()->user()->id, 'is_default' => 1])->get();
                if(count($test) > 0){
                    $shipping->is_default = 0;
                }else{
                    $shipping->is_default = 1;
                } 
                $shipping->receiver_email = $request->email;
                $shipping->zip_code = $request->postal_code;
                $shipping->city = $request->city;
                $shipping->state = $request->state;
                if($shipping->save()){
                    return redirect()->back()->with('success', 'Address Added successfully');
                }
    }
    
    public function Categories($id){
        $id = decrypt($id);
        return view('users.pages.products')

            ->with('products',  Product::where('category_id', $id)->simplePaginate(20))
            ->with('prod', Product::latest()->take(5)->get());
    }

    public function Pages($id){
        $id = decrypt($id);
        $menu = Menu::where('id', $id)->first();
        $menus = preg_replace("/\s+/", "",$menu->name);
        $product = Product::where('id', 19)->first();
        return view('users.pages'.".".$menus, compact('product'));
    }
    
    public function search(Request $request)
   {
    if(isset($request->search)){
    $search = $request->get('search');
     $product['products'] = Product::where( 'name', 'LIKE', "%$search%" )->simplePaginate(18);
    }
    return view ( 'users.pages.products',$product)
    ->with('prod', Product::latest()->take(5)->get())
    ->with('search',$search);
    }

    public function Addreview(Request $request, $id){
        if(isset($request->name)){
        
        $proid = product::findorfail($id);
        
        $review = new Review;
        $review->product_id = $proid->id;
        $review->name = $request->name;
        $review->email = $request->email;
        $review->rating = $request->rated;
        $review->description = $request->description;
        
        if($review->save()){
            
            return back();
            
        }else{
            
            return redirect('customer.product-details');
        }
    }
}

               

}
