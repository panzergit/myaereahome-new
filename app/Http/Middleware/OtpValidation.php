<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Models\ModuleSetting;

class OtpValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user(); 
        if($user->otp !=''){
           // return redirect('/loginotp');
        }
        return $next($request);
    }
}
