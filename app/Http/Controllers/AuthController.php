<?php
namespace App\Http\Controllers;

use App\Mail\EmailverificationMail;
use App\Mail\WelcomeMail;
use App\Models\v7\User;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function signup(Request $request)
    {

        $token = $request->bearerToken();
        if ($token != "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImJkNDdjZDY1Nz") {
            return response()->json([
                'message' => 'Unauthorized !',
            ], 401);
        }
//|unique:users
        $request->validate([
            'firstname' => 'required|string|max:50',

            'email' => 'required|string|email',

            'password' => 'required|string|confirmed',
        ]);
        $passcode = str_random(8);

        $digits = 4;
        $passcode = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
        $fbid = isset($request->fb_id) ? $request->fb_id : 0;
        $password = bcrypt($request->password);
        $u = User::where('email', $request->email)->where('status', '!=', '2')->first();

        if ($u !== null) {

            if ($u->email_verified == 0) {
                $app_url = env('APP_URL', ' ');

                $msg['text'] = 'Your  Account  verification  code  is ' . $u->email_verification_code;
                $msg['user'] = $u->firstname . ' ' . $u->lastname;
                $msg['link'] = $app_url . "emailverification?token=" . $u->email_verification_hash;
                $toEmail = $request->email;

                Mail::to($toEmail)->send(new EmailverificationMail($msg));

                return response()->json([
                    'message' => 'A new confirmation link has been sent to your email. Please check your email and verify your account.',
                ], 400);

            }
            return response()->json([
                'message' => 'The email has been already taken',
            ], 400);

        }

        $user = new User([
            'firstname' => $request->firstname,
            'lastname' => isset($request->lastname) ? $request->lastname : "",
            'email' => $request->email,
            'mobile' => isset($request->mobile) ? $request->mobile : "",
            'aboutme' => '',
            'avatar' => '',
            'gender' => '',
            'dob' => '',
            'country' => '',
            'occupation' => '',
            'language' => '',
            'creditcard' => '',
            'fb_id' => $fbid,
            'passport_photo' => '',
            'creditcard_id' => '',
            'device_token' => isset($request->device_token) ? $request->device_token : "",
            'device_type' => isset($request->device_type) ? $request->device_type : "",
            'password' => $password,

            'email_verification_code' => $passcode,
        ]);
        $user->save();
        $user->email_verification_hash = md5($user->id);
        $user->save();
        $app_url = env('APP_URL', ' ');

        $msg['text'] = 'Your  Account  verification  code  is ' . $passcode;
        $msg['user'] = $request->firstname . ' ' . $request->lastname;
        $msg['link'] = $app_url . "emailverification?token=" . $user->email_verification_hash;
        $toEmail = $request->email;

        if ($fbid == 0) {
            Mail::to($toEmail)->send(new EmailverificationMail($msg));

        } else {
            $msg['name'] = $request->firstname . ' ' . $request->lastname;
            $msg['text'] = "  Your    Lumiere account  activated with facebook  Successfully ";
            $toEmail = $request->email;
            Mail::to($toEmail)->send(new WelcomeMail($msg));

        }
        $credentials = request(['email', 'password']);
        /* if (!Auth::attempt($credentials)) {
        return response()->json([
        'message' => 'Unauthorized',
        ], 401);
        }*/

        //$user = $u ;
        $user->email_verification_hash = md5($user->id);

        //$user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        return response()->json([
            'message' => 'Successfully created user!',
            'UserID' => $user->id,
            'access_token' => $tokenResult->accessToken,

            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
        ], 201);
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        echo "hai";
        exit;
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        $userData = User::select(['id', 'firstname', 'lastname', 'email', 'mobile', 'aboutme', 'avatar', 'gender',
            'dob', 'country', 'occupation', 'language', 'passport_photo', 'email_verified_at', 'email_verified',
            'fb_id'])
            ->where('id', auth()->id())
            ->with('countrys')
            ->get();

        $userData = $userData->map(function ($value) {

            $value->country = (object) [];

            if (!empty($value->countrys)) {
                $value->country = [
                    'id' => $value->countrys->country_id,
                    'name' => $value->countrys->country,
                ];
            }

            unset($value->countrys);
            return $value;
        });

        return response()->json([
            'status' => 'success',
            'data' => $userData,
        ]);
    }

    public function FbLogin(Request $request)
    {

        $validator = Validator::make($request->all(), 
            [
                'email' => 'required',
                'fb_id' => 'required'],
            [
                'email.required' => 'email missing.',
                'fb_id.required' => 'facebook id  missing.'
        ]);

        if ($validator->fails()) {
            $messages = $validator->messages();
            return response()->json([
                'message' => $messages->all(),
            ], 400);
        }

        $user = User::where('email', $request->input('email'))->first();

        if ($user === null) {
            return response()->json([
                'message' => 'user  not found',
            ], 400);
        }

        if ($user->status == 0) {
            return response()->json([
                'message' => 'Account is deactivated, please contact administrator',
            ], 401);
        }

        if ($user->fb_id == 0) {

            $user->fb_id = $request->input('fb_id');
            $user->save();

        } else {
            if ($user->fb_id != $request->input('fb_id')) {
                return response()->json([
                    'message' => 'Invalid Fb ID',
                ], 400);
            }
        }

        $credentials = request(['email', 'fb_id']);
        /*if (!Auth::attempt($credentials)) {
        return response()->json([
        'message' => 'Unauthorized',
        ], 401);
        } */

        //$user = $request->user();
        $user->device_token = isset($request->device_token) ? $request->device_token : $user->device_token;
        $user->device_type = isset($request->device_type) ? $request->device_type : $user->device_type;
        $user->save();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        return response()->json([
            'message' => 'user found',
            'UserID' => $user->id,
            'access_token' => $tokenResult->accessToken,

            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
        ], 200);
    }

    public function usersearch(Request $request)
    {

        $search = $request->get('term');

        $result = User::where('name', 'LIKE', '%' . $search . '%')->get();

        return response()->json($result);
    }

}
