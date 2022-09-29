<?php
namespace App\Http\Controllers;

use App\AdminNotify;
use App\User;
use App\Shipping;
use App\Product;
use App\Category;
use App\Http\Requests\CheckoutRequest;
use App\Traits\CheckoutStore;
use App\Traits\decryptId;
use App\Order;
use App\Mail\UserMail;
use App\Mail\OrderMail;
use App\Mail\PaymentMail;
use App\Delivery;
use App\Notification;
use Illuminate\Support\Facades\Mail;
use App\Transaction;
use App\OrderItem;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\notifications;
use App\Wallet;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Session\Session;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    use CheckoutStore;
    use decryptId;
    private $user;
    private $OrderItem;
    private $Order;
    private $Shipping;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function send($data){
     //  dd($data);
        Mail::to($data['email'])->send(new UserMail($data));
    }
    
    public function sendMail($data){
        Mail::to($data['email'],  'orders@sofarsolar.ng')->send(new PaymentMail($data));
    }
     public function OrderMail($data){
        Mail::to($data['email'], 'orders@sofarsolar.ng')->send(new OrderMail($data));
    }

     public function __construct()
     {
         $this->User = new User();
         $this->OrderItem = new OrderItem();
         $this->Order = new Order();
         $this->Shipping = new Shipping();
         $this->API_Token = 'FLWSECK_TEST-01f7c01d568c1e538b2f28c89fe190fb-X';
     }
     public function getCustomerLocation($address){
        $key = 'AIzaSyCHsIJX1EHXN_tLXbQ75pMHcB60L3XVOeU';
        $url = "https://maps.google.com/maps/api/geocode/json?key=$key&address=".urlencode($address);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    
        $responseJson = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($responseJson, true);
      //  dd($response);
        if ($response['status'] == 'OK') {
            $res['lat'] = $response['results']['0']['geometry']['location']['lat'];
            $res['lng'] = $response['results']['0']['geometry']['location']['lng'];
            return $res;
        }


     }

     public function getShippingPrice($lan, $lng){

        $shipping =  Shipping::where('user_id', auth()->user()->id)->latest()->first();
       // dd($shipping);
        $host = "https://api.gokada.ng/api/developer/order_create/";
        $data = array(
            'api_key' => "HJqbXNrycOO8tgehrmWqKrTOUKg65njvF6NDfd385TwGtvpq60CuGwelSRBt_test",
            'delivery_latitude' => '6.594770',
            'delivery_longitude' => '3.344280',
            'delivery_name' => 'sofarsolar',
            'delivery_phone' => '+2348039366207',
            'delivery_address' => '1 adeolad adeoye street ikeja',
            'pickup_address' => $shipping->address.','.$shipping->city,
            'pickup_name' => $shipping->receiver_name,
            'pickup_phone' => '+234'.$shipping->receiver_phone,
            'pickup_latitude' => $lan,
            'pickup_longitude' => $lng,
        );
        $curl  = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $host,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
        ));
        $response = curl_exec($curl);
        
        $res  = json_decode($response,true);
      //  dd($res);
        if($res['order_id']){
        
     $delivery = Delivery::create([
            'delivery_id' => $res['order_id'],
            'user_id' => auth()->user()->id,
            'shipping_id' => $shipping->id,
            'delivery_fee' => $res['fare'],
            'distance' => $res['distance'],
            'time' => $res['time'],
            'status' => 'pending'
            ]);
     }else{

    return redirect()->back();
     }
    }

    public function index(){
        $cart= \Cart::content();
        if(count($cart) == null){
            return redirect()->route('index');
        }

        $orderNo = rand(111111111,99999999);
        foreach($cart as $cat){
            $order_list = new orderItem;
            $order_list->order_No = $orderNo;
            $order_list->product_name = $cat->model->name;
            $order_list->product_name = $cat->name;
            $order_list->qty = $cat->qty;
            $order_list->price = $cat->price;
            $order_list->image = $cat->model->image;
            $order_list->payable = $cat->qty*$order_list->price;
            $order_list->user_id = auth()->user()->id;
            $order_list->save();
        }
        $shipping= Shipping::where(['user_id' => auth()->user()->id, 'is_default' => 1])->get();
        return view('users.products.checkout')
        ->with('shipping', $shipping)
        ->with('cart', \Cart::content());
    }


    public function modal(Request $request){
        $this->validate($request, [
            'name' => 'required',
            'address'=>'required',
            'phone' => 'required',
        ]);
              
    if($request->name){
        $shipping = new Shipping;
        $shipping->user_id = auth()->user()->id;
        $shipping->address = $request->address;
        $shipping->user_id = auth()->user()->id;
        $shipping->receiver_phone = $request->phone;
        $shipping->receiver_email = $request->email;
        $shipping->is_default = $request->default;
        if($request->default)
        {
            $update = [
                'is_default' => 0
            ];
        DB::table('shippings')
        ->where(['user_id'=>  auth()->user()->id, 'is_default' => '1'])
        ->update($update);
        }
        $shipping->zip_code = $request->postal_code;
        $shipping->city = $request->city;
        $shipping->state = $request->state;
        if( $shipping->save()){
            return redirect()->back()->with('success', 'Address Updated Successfully');
        }
            
    }
}
  
   
     public function store(Request $request){
        $orderNo= DB::table('order_items')->where(['user_id'=> auth()->user()->id])->orderBy('created_at', 'desc')->first();
        $order_exist = order::where(['order_No' => $orderNo->order_No])->first();
        if($order_exist){ 
            return redirect()->route('checkout.index')->withErrors('Order number already exist, please check your order details and proceed with manual payment');
            exit();
        }
        if(!isset($request->payment_method)){
            return redirect()->route('checkout.index')->withErrors('Please select a payment method below');
            }
        $order = new order;
        $order->user_id = auth()->user()->id;
        $order->order_No = $orderNo->order_No;
        $order->payment_method = $request->payment_method;   
        $order->is_paid = 0;
        $order->is_delivered = 0;
        $order->amount = \Cart::priceTotalFloat();
        $shipping = Shipping::where(['user_id' => auth()->user()->id, 'is_default' => 1])->first();
        if(!isset($shipping)){
            return redirect()->route('checkout.index')->withErrors('Please select a default Address');
            }
        $order->shipping_id = $shipping->id;
        if($order->save()){
        $item = DB::table('orders')->where('user_id', auth()->user()->id)->latest()->first();
        $order_items = DB::table('order_items')->where(['order_No' => $orderNo->order_No])->get();
                   if($request->payment_method == 'card'){
                    return view('users.products.checkouts')
                    ->with('address', Shipping::where(['user_id' => auth()->user()->id, 'is_default' => 1 ])->first())
                    ->with('cart', \Cart::content());
                    }else{
                    $items = orderItem::where(['order_No' => $orderNo->order_No])->get();
                    $orders = Order::where('user_id', auth()->user()->id)->latest()->first();
                    \Session::flash('success', 'Order was sent successfully');
                     \Cart::destroy();
                     return view('users.products.orders', compact('items', 'orders'))->with('success', 'Order was sent successfully');
                 }
        }
    }
    


    public function addNew(){
        //dd('aj jere');
        return view('users.products.edit')
                ->with('title', 'Checkout')
                ->with('user', User::where('id', auth()->user()->id)->first())
                ->with('carts', \Cart::content());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

     public function Add(CheckoutRequest $request){
       //  dd($request);
        $user_address =  $this->StoreShippingAddress($request);
        $ss= $this->Shipping->create($user_address);
        if(count(\Cart::content())>0){
        $address = Shipping::where('user_id', auth()->user()->id)->latest()->first();
            return view('users.products.payment')
                ->with('title', 'Checkout')
                ->with('address', $address)
                ->with('user', User::where('id', auth()->user()->id)->first())
                ->with('carts', \Cart::content());
        }else{

            return redirect()->route('carts.index');
        }
     }
    public function update(Request $request, $id)
    {
        $id = $this->decryptId($id);
        $address = Shipping::where('id', $id)->first();
        $address->receiver_name = $request->receiver_name;
        $address->receiver_phone = $request->receiver_phone;
        $address->receiver_email = $request->receiver_email;
        $address->address = $request->address;
        $address->city = $request->city;
        $address->state = $request->state;
        $address->zip_code = $request->zip_code;
        $address->save();
        Session()->flash('message', 'Address Updated');
        return redirect()->back();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function verify($trxref){
        $trnx_ref_exists = Transaction::where(['external_ref' => $trxref])->first();
        if ($trnx_ref_exists) {
            return response()->json($trnx_ref_exists);
        }

        $cURLConnection = curl_init();
        curl_setopt($cURLConnection, CURLOPT_URL, 'https://api.flutterwave.com/v3/transactions/'.$trxref.'/verify/');
        curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer ".$this->API_Token
        ));
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true); 
        $se = curl_exec($cURLConnection);
        curl_close($cURLConnection);  
        $resp = json_decode($se, true);
      
        if ($resp['status'] == 'error') {
            $message = 'Transaction not found, Please contact support';
            return response($message);
        }
        $res = $resp['status'];
        $amount = $resp['data']['amount'];
        $custemail = $resp['data']['customer']['email'];
        $payment_ref = $resp['data']['tx_ref'];
        $external_ref = $resp['data']['flw_ref'];
        if (($res == "success")) {     
            $getUser = User::where('email', $custemail)->first();
            $order = DB::table('orders')->where('user_id', $getUser->id)->latest()->first();
           Transaction::create([
                'user_id' => $getUser->id,
                'payment_ref'=>$payment_ref,
                'type'=>'payment for',
                'payment_method' => 'card',
                'external_ref'=>$external_ref,
                'amount'=>$amount,
                'order_No' => $order->order_No
            ]);
            $orders = order::where(['order_No' => $order->order_No])
            ->update([
                'payment_ref' => $payment_ref,
                'external_ref'=>$external_ref,
                 'is_paid' => 1, 
                 'amount'=>$amount,
                 'order_No' => $order->order_No
                 ]);
                 $items = orderItem::where(['order_No' => $order->order_No])->get();
                 \Cart::destroy();
                 $orders = Order::where('user_id', $getUser->id)->latest()->first();
                 \Session::flash('success', 'Order was sent successfully');
                 return view('users.products.completed', compact('items', 'orders'))->with('success', 'Order completed successfully');
        } else {
            //Dont Give Value and return to Failure page
            \Session::flash('flash_message', 'Something went wrong');
            $message = 'Your payment was not successful. Something went wrong.';
            return view('customer.cart.checkout', compact('message'));
        }
          
    }


    public function sendNotify($title, $message){
        $getUser = User::where('id', auth()->user()->id)->first();
        $notify = new Notification;
        $notify->user_id = $getUser->id;
        $notify->title = $title;
        $notify->message = 'Dear '.$getUser->name. ', <br>'.$message;
        $notify->save();
        $admin = new AdminNotify;
        $admin->message = $title;
        $admin->save();

}

}
