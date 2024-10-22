<?php

namespace App\Http\Controllers\Api\Auth;

use App\{
  Models\User,
  Models\Generalsetting
};

use App\{
  Http\Controllers\Controller,
  Http\Resources\UserResource
};
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Str;
use JWTAuth;
use App\Classes\DasMailer;
use App\Models\SmsLog;
use Tymon\JWTAuth\JWTAuth as JWTAuthJWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
  /**
   * Create a new AuthController instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('auth:api', ['except' => ['login', 'register', 'logout', 'social_login', 'forgot', 'forgot_submit']]);
    $this->middleware('setapi');
  }


  public function register(Request $request)
  {
    try {
      $rules = [
        'fullname' => 'required',
        'email' => 'required|email|unique:users',
        'phone' => 'required',
        'address' => 'required',
        'password' => 'required'
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['status' => false, 'data' => [], 'error' => $validator->errors()]);
      }

      $gs = Generalsetting::first();

      $user = new User;
      $user->name = $request->fullname;
      $user->email = $request->email;
      $user->phone = $request->phone;
      $user->address = $request->address;
      $user->password = bcrypt($request->password);

      if ($gs->is_verification_email == 0) {
        $user->email_verified = 'Yes';
      }

      // Generate a 4-digit OTP
      $otp = sprintf("%04d", mt_rand(1, 9999));
      $msg = 'Your 4 Digit OTP is: ' . $otp . ' Please use this code to verify your number. Thanks For Staying with www.uzanvati.com';

      // Save the user
      $user->save();

      // Send SMS verification
      $response = Http::get('https://sms.songbirdtelecom.com', [
        'user' => 'playon24',
        'pwd' => 'admin@123',
        'senderid' => '8809612440465',
        'mobileno' => $request->phone,
        'msgtext' => $msg,
        'priority' => 'High',
        'CountryCode' => '880',
      ]);

      // Log SMS sending
      SmsLog::create([
        'from' => 'Registration/Verification',
        'to' => $request->phone,
        'message' => $msg,
        'status' => $response->body(),
        'sent_by' => "System"
      ]);

      // Return a success response
      $responseData = [
        'message' => 'User registered successfully. Verification code sent via SMS.',
      ];
      return response()->json(['status' => true, 'data' => $responseData]);
    } catch (\Exception $e) {
      return response()->json(['status' => false, 'data' => [], 'error' => ['message' => $e->getMessage()]]);
    }
  }





  /**
   * Get a JWT via given credentials.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function login(Request $request)
  {
    try {
      $rules = [
        'phone' => 'required',
        'password' => 'required'
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['status' => false, 'data' => $validator->errors()], 422);
      }

      $credentials = request(['phone', 'password']);

      if (!$token = auth()->attempt($credentials)) {
        return response()->json(['status' => false, 'data' => ["message" => "Email / password didn't match."]], 401);
      }

      if (auth()->user()->email_verified == 'No') {
        auth()->logout();
        return response()->json(['status' => false, 'data' => ["message" => 'Your Email is not Verified!']], 401);
      }

      if (auth()->user()->ban == 1) {
        auth()->logout();
        return response()->json(['status' => false, 'data' => ["message" => 'Your Account Has Been Banned.']], 401);
      }

      // Include phone number in the response
      $user = auth()->user();
      $responseData = [
        'token' => $token,
        'user' => [
          'name' => $user->name,
          'email' => $user->email,
          'phone' => $user->phone,
          'address' => $user->address,
          'photo' => $user->photo,
          'isVerified' => $user->email_verified,
        ],
      ];

      return response()->json(['status' => true, 'data' => $responseData], 200);
    } catch (\Exception $e) {
      return response()->json(['status' => false, 'data' => ['message' => $e->getMessage()]]);
    }
  }



  /**
   * Get a JWT via given credentials.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function social_login(Request $request)
  {
    try {
      $rules = [
        'name' => 'required',
        'email' => 'required'
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['status' => false, 'data' => [], 'error' => $validator->errors()]);
      }

      $user = User::where('email', '=', $request->email)->first();

      if (!$user) {

        $rules = [
          'email' => 'email|unique:users'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
          return response()->json(['status' => false, 'data' => [], 'error' => $validator->errors()]);
        }

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->email_verified = 'Yes';
        $user->affilate_code = md5($request->email);
        $user->save();

        $token = auth()->login($user);
        return response()->json(['status' => true, 'data' => ['token' => $token], 'error' => []]);
      }

      $userToken = JWTAuth::fromUser($user);

      if ($user->email_verified == 'No') {
        return response()->json(['status' => false, 'data' => [], 'error' => ["message" => 'Your Email is not Verified!']]);
      }

      if ($user->ban == 1) {
        return response()->json(['status' => false, 'data' => [], 'error' => ["message" => 'Your Account Has Been Banned.']]);
      }

      auth()->login($user);

      return response()->json(['status' => true, 'data' => ['token' => $userToken,  'user' => new UserResource(auth()->user())], 'error' => []]);
    } catch (\Exception $e) {
      return response()->json(['status' => true, 'data' => [], 'error' => ['message' => $e->getMessage()]]);
    }
  }


  /**
   * Get the authenticated User.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function details()
  {
    try {
      return response()->json(['status' => true, 'data' => new UserResource(auth()->user()), 'error' => []]);
    } catch (\Exception $e) {
      return response()->json(['status' => true, 'data' => [], 'error' => ['message' => $e->getMessage()]]);
    }
  }

  /**
   * Log the user out (Invalidate the token).
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function logout()
  {
    auth()->logout();
    return response()->json(['status' => true, 'data' => ['message' => 'Successfully logged out.'], 'error' => []]);
  }

  public function sendVerificationCode(Request $request)
  {
    $gs = Generalsetting::first();
  }

  /**
   * Refresh a token.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function refresh()
  {
    return $this->respondWithToken(auth()->refresh());
  }

  /**
   * Get the token array structure.
   *
   * @param  string $token
   *
   * @return \Illuminate\Http\JsonResponse
   */
  protected function respondWithToken($token)
  {
    return response()->json([
      'access_token' => $token,
      'token_type' => 'bearer',
      'expires_in' => auth()->factory()->getTTL() * 300
    ]);
  }



  public function forgot(Request $request)
  {
    $gs = Generalsetting::findOrFail(1);
    $user = User::where('email', $request->email)->first();
    if ($user) {

      $token = Str::random(6);

      $subject = "Reset Password Request";
      $msg = "Your Forgot Password Token: " . $token;
      $user->reset_token = $token;
      $user->update();

      if ($gs->is_smtp == 1) {
        $data = [
          'to' => $request->email,
          'subject' => $subject,
          'body' => $msg,
        ];

        $mailer = new DasMailer();
        $mailer->sendCustomMail($data);
      } else {
        $headers = "From: " . $gs->from_name . "<" . $gs->from_email . ">";
        mail($request->email, $subject, $msg, $headers);
      }

      return response()->json(['status' => true, 'data' => ['name' => $user->id, 'reset_token' => $user->reset_token], 'error' => []]);
    } else {
      return response()->json(['status' => false, 'data' => [], 'error' => 'Account not found']);
    }
  }


  public function forgot_submit(Request $request)
  {

    if ($request->new_password != $request->confirm_password) {
      return response()->json(['status' => false, 'data' => [], 'error' => 'New password & confirm password not match']);
    }

    $user = User::where('id', $request->user_id)->where('reset_token', $request->reset_token)->first();
    if ($user) {

      $password = Hash::make($request->new_password);
      $user->password = $password;
      $user->reset_token = null;
      $user->update();
      return response()->json(['status' => true, 'data' => ['message' => 'Password Changed Successfully'], 'error' => []]);
    } else {
      return response()->json(['status' => false, 'data' => [], 'error' => 'Something is wrong']);
    }
  }
}
