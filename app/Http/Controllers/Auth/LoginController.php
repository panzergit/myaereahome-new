<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\v7\LoginOTP;
use Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function showLoginForm()
    {
        return redirect('opslogin');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $user = Auth::user();
            $email = $user->email;
            $name = $user->name;
            LoginOTP::sendotpnew($name, $email);
            return redirect('loginotp'); //->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->withInput($request->except('password'));
    }

    protected function authenticated(Request $request, $user)
    {
        $login_role =  $user->role_id;
        $env_roles  = (array_key_exists('USER_APP_ROLE', $_ENV)) ? $_ENV['USER_APP_ROLE'] : '';
        $roles = explode(",", $env_roles);
        if(in_array($login_role, $roles)) return redirect('/opslogin')->with('status', 'Login to Aerea mobile app instead');;
        return redirect('/loginotp');
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect('/loginotp');
    }
}