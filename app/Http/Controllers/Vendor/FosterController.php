<?php

namespace App\Http\Controllers\Vendor;

use App\Classes\DasMailer;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Generalsetting;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use Auth;
use Carbon\Carbon;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class FosterController extends Controller
{


    public function store(Request $request){

        $this->validate($request, [
            'shop_name'   => 'unique:users',],
            [ 'shop_name.unique' => 'This shop name has already been taken.']);

     
     $user = Auth::user();
     $subs = Subscription::findOrFail($request->subs_id);
   
     $txnid = "fosterTXN_".uniqid();

     if (Session::has('currency'))
     {
         $curr = Currency::find(Session::get('currency'));
         $item_amount = $request->amount;
     }
     else
     {
         $curr = Currency::where('is_default','=',1)->first();
         $item_amount = $request->amount;
     }

     if($curr->name != "BDT")
     {
         return redirect()->back()->with('unsuccess','Please Select BDT Currency For SSLCommerz.');
     }

     


     $order['item_name'] = $subs->title." Plan";
     $order['item_number'] = Str::random(4).time();
     $order['item_amount'] = $subs->price;



     $sub = new UserSubscription;
     $sub->user_id = $user->id;
     $sub->subscription_id = $subs->id;
     $sub->title = $subs->title;
     $sub->currency = $subs->currency;
     $sub->currency_code = $subs->currency_code;
     $sub->price = $subs->price;
     $sub->days = $subs->days;
     $sub->allowed_products = $subs->allowed_products;
     $sub->details = $subs->details;
     $sub->method = 'foster';
     $sub->txnid = $txnid;
     $sub->status = 0;
     $sub->save();



     $gs = Generalsetting::find(1);


 



# $post_data['multi_card_name'] = "mastercard,visacard,amexcard";  # DISABLE TO DISPLAY ALL AVAILABLE

// dd($input);

//foster

$redirect_url =url("api/foster/vendor/notify") ;
//$redirect_url= action('Front\FosterController@notify');

//dd($redirect_url);
$cancel_url = action('Vendor\FosterController@cancle');
$fail_url = action('Vendor\FosterController@cancle');
$urlparamForHash = http_build_query(array(
  'mcnt_AccessCode' => $gs->foster_access_code,
  'mcnt_TxnNo' => $txnid, //Ymdhmsu//PNR 
  'mcnt_ShortName' => $gs->foster_short_name,
  'mcnt_OrderNo' => $order['item_number'],
  'mcnt_ShopId' => $gs->foster_shop_id,
  'mcnt_Amount' => $order['item_amount'],
  'mcnt_Currency' => 'BDT'
));
$secretkey = $gs->foster_secretkey;
$secret = strtoupper($secretkey);
$hashinput = hash_hmac('SHA256', $urlparamForHash, $secret);

$domain =   $_SERVER["SERVER_NAME"]; // or Manually put your domain name  
$ip =request()->server('SERVER_ADDR');  //domain ip  
// $ip = "23.227.186.26";
//echo $ip."======================";

//dd($hashinput);


$urlparam = array(
  'mcnt_TxnNo' => $txnid,
  'mcnt_ShortName' =>$gs->foster_short_name, //No Need to Change       
  'mcnt_OrderNo' =>   $order['item_number'],
  'mcnt_ShopId' => $gs->foster_shop_id, //No Need to Change 
  'mcnt_Amount' => $order['item_amount'],
  'mcnt_Currency' => 'BDT',
  'cust_InvoiceTo' => $user->shop_name,
  'cust_CustomerServiceName' => 'E-commarce', //must
  'cust_CustomerName' => $user->shop_name, //must 
  'cust_CustomerEmail' => $user->email, //must  
  'cust_CustomerAddress' =>$user->shop_address,
  'cust_CustomerContact' => $user->phone, //must 
  'cust_CustomerGender' => 'N/A',
  'cust_CustomerCity' => "Dhaka", //must 
  'cust_CustomerState' => "Dhaka",
  'cust_CustomerPostcode' => "1207",
  'cust_CustomerCountry' => 'Bangladesh',
  'cust_Billingaddress' => 'Bangladesh', //must if not put ‘N/A’ 
  'cust_ShippingAddress' => 'Bangladesh',
  'cust_orderitems' => 1, //must  
  'GW' => '', //optional        
  'CardType' => '', //optional
  'success_url' => $redirect_url, //must   
  'cancel_url' => $cancel_url, //must   
  'fail_url' => $fail_url, //must  
  'emi_amout_per_month' => '', //optional 
  'emi_duration' => '', //optional                           
  'merchentdomainname' => $domain, // must           
  'merchentip' => $ip,
  'mcnt_SecureHashValue' => $hashinput
);

if($gs->foster_sandbox_check==1){
 $url = 'https://demo.fosterpayments.com.bd/fosterpayments/paymentrequest.php';
}else{
 $url ='https://payment.fosterpayments.com.bd/fosterpayments/paymentrequest.php';
}




// dd($hashinput);

$data_string = json_encode($urlparam);
$handle = curl_init();
curl_setopt($handle, CURLOPT_URL, $url);
curl_setopt($handle, CURLOPT_TIMEOUT, 30);
curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($handle, CURLOPT_POST, 1);
curl_setopt($handle, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC
curl_setopt(
  $handle,
  CURLOPT_HTTPHEADER,
  array('Content-Type: application/json', 'Content-Length: ' . strlen($data_string))
);
$content = curl_exec($handle);

$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);


// dd($content);
if ($code == 200 && !(curl_errno($handle))) {
  curl_close($handle);
  $response = $content;
} else {
  curl_close($handle);
  return redirect()->back()->with('unsuccess', "FAILED TO CONNECT WITH FOSTER API");
  exit;
}

$responsedate = json_decode($response, true);

 //dd($responsedate);
//echo $response ;
$data = $responsedate['data'];

//  die;
$redirect_url = $data['redirect_url'];
$payment_id = $data['payment_id'];
$cancel_url = action('Front\PaymentController@paycancle');


if($redirect_url==""){
 return redirect($cancel_url);
}



$url = $redirect_url . "?payment_id=" . $payment_id;
//dd($url);
//header("Location:" . $url);
echo "<meta http-equiv='refresh' content='0;url=" . $url . "'>";
exit;
//dd($url);
// echo $url;

//end foster



 }


public function cancle(Request $request){
  return redirect()->route('vendor-package')->with('unsuccess','Payment Cancelled.');
}
public function notify(Request $request){



  $success_url = action('Vendor\PaypalController@payreturn');
  $cancel_url = action('Vendor\PaypalController@paycancle');

        $input = $request->all();


        if($input['TxnResponse'] == '2'){

            $settings = Generalsetting::findOrFail(1);
            $subs = UserSubscription::where('txnid','=',$input['MerchantTxnNo'])->orderBy('id','desc')->first();
            $subs->status = 1;
            $subs->update();

            $user = User::findOrFail($subs->user_id);
            $package = $user->subscribes()->where('status',1)->orderBy('id','desc')->first();


            $today = Carbon::now()->format('Y-m-d');
            $input = $request->all();  
            $user->is_vendor = 2;
                        if(!empty($package))
                        {
                            if($package->subscription_id == $request->subs_id)
                            {
                                $newday = strtotime($today);
                                $lastday = strtotime($user->date);
                                $secs = $lastday-$newday;
                                $days = $secs / 86400;
                                $total = $days+$subs->days;
                                $user->date = date('Y-m-d', strtotime($today.' + '.$total.' days'));
                            }
                            else
                            {
                                $user->date = date('Y-m-d', strtotime($today.' + '.$subs->days.' days'));
                            }
                        }
                        else
                        {
                            $user->date = date('Y-m-d', strtotime($today.' + '.$subs->days.' days'));
                        }
                        $user->mail_sent = 1;     
                        $user->update($input);

                        if($settings->is_smtp == 1)
                        {
                        $data = [
                            'to' => $user->email,
                            'type' => "vendor_accept",
                            'cname' => $user->name,
                            'oamount' => "",
                            'aname' => "",
                            'aemail' => "",
                            'onumber' => "",
                        ];
                        //$mailer = new DasMailer();
                      //  $mailer->sendAutoMail($data);        
                        }
                        else
                        {
                        //$headers = "From: ".$settings->from_name."<".$settings->from_email.">";
                       // mail($user->email,'Your Vendor Account Activated','Your Vendor Account Activated Successfully. Please Login to your account and build your own shop.',$headers);
                        }

            return redirect($success_url);
        }
        else {
            return redirect($cancel_url);
        }

}
}