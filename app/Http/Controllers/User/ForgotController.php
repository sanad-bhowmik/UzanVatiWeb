<?php

namespace App\Http\Controllers\User;

use Validator;
use App\Models\User;
use App\Models\SmsLog;
use App\Classes\DasMailer;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Generalsetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Input;

class ForgotController extends Controller
{
  public function __construct()
  {
    $this->middleware('guest');
  }

  public function showForgotForm()
  {
    return view('user.forgot');
  }

  // public function forgot(Request $request)
  // {
  //   $gs = Generalsetting::findOrFail(1);
  //   $input =  $request->all();
  //   if (User::where('phone', '=', $request->phone)->count() > 0) {
  //     // user found
  //     $admin = User::where('phone', '=', $request->phone)->firstOrFail();
  //     $autopass =  "uzan" . mt_rand(111, 999);     //Str::random(6);
  //     $input['password'] = bcrypt($autopass);

  //     $msg = "Dear Customer,\n Your New Password is: " . $autopass . "\nUse this password to login into your account.\n Thanks www.uzanvati.com";
  //     // if($gs->is_smtp == 1)
  //     // {
  //     //     $data = [
  //     //             'to' => $request->email,
  //     //             'subject' => $subject,
  //     //             'body' => $msg,
  //     //     ];

  //     //     $mailer = new DasMailer();
  //     //     $mailer->sendCustomMail($data);                
  //     // }
  //     // else
  //     // {
  //     //     $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
  //     //     mail($request->email,$subject,$msg,$headers);            
  //     // }

  //     if ($gs->is_sms == 1) {

  //       $admin->update($input);
  //       $number = "88" . $request->phone;
  //       /* $response = Http::get('http://joy.metrotel.com.bd/smspanel/smsapi', [
  //        'api_key' => '$2y$10$UGffpZIkHXi7k1xI5T6KoOSoPahpz8Kj3FvbK05JGQA0h2yr4/b62501',
  //        'type' => 'text',
  //        'contacts' =>$number,
  //        'senderid' =>'8809612440465',
  //        'msg' => $msg
  //        ]); 

  //       */

  //       $response = Http::get('https://mshastra.com/sendurl.aspx', [
  //         'user' => 'playon24',
  //         'pwd' => 'sesbheje',
  //         'senderid' => '8809612440465',
  //         'mobileno' => $number,
  //         'msgtext' => $msg,
  //         'priority' => 'High',
  //         'CountryCode' => '880',
  //       ]);



  //       SmsLog::create(
  //         [
  //           'from' => 'Password Reset',
  //           'to' => $number,
  //           'message' => $msg,
  //           'status' => $response->body(),
  //           'sent_by' => "System"
  //         ]
  //       );
  //       return response()->json('Your Password Reseted Successfully. Please Check your inbox for new Password.');
  //     } else {
  //       return response()->json('Request can not be process now, try later !!');
  //     }
  //   } else {
  //     // user not found
  //     return response()->json(array('errors' => [0 => 'No User Found With This Number.']));
  //   }
  // }

  public function forgot(Request $request)
  {
    $gs = Generalsetting::findOrFail(1);
    $input = $request->all();

    if (User::where('phone', '=', $request->phone)->count() > 0) {
      // User found
      $admin = User::where('phone', '=', $request->phone)->firstOrFail();
      // $autopass = "uzan" . mt_rand(111, 999);
      // $input['password'] = bcrypt($autopass);

      $admin->password = bcrypt($input['password']);


      // Update the user's password
      $admin->update();
      // dd($admin);
      return response()->json(['message' => 'Your Password has been reset successfully. ']);
    } else {
      return response()->json(['error' => 'No User Found With This Number.']);
    }
  }
}
