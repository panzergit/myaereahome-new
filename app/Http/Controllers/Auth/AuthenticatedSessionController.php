<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\v7\LoginOTP;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
        
        if(Auth::check()){
            $user = Auth::user();
            
            $login_role =  $user->role_id;
            $env_roles  = (array_key_exists('USER_APP_ROLE', $_ENV)) ? $_ENV['USER_APP_ROLE'] : '';
            $roles = explode(",", $env_roles);
            if(in_array($login_role, $roles)) return redirect('/opslogin')->with('status', 'Login to Aerea mobile app instead');;
            
            LoginOTP::sendotpnew(trim($user->name), trim($user->email));
            return redirect('loginotp');
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
