<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Models\v7\ModuleSetting;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $module)
    {
        $user = Auth::user();  
        $rold_id = $user->role_id;
        $module_id = $module;
        $routeAction = explode("@",$request->route()->getActionName());
        $action = $routeAction[1];
        $result = ModuleSetting::where('role_id',$rold_id)->where('module_id',$module_id)->first();


        switch($action){

            case 'search':
                if($result->view ==1)
                    return $next($request);
            break;

            case 'index':
                if($result->view ==1)
                    return $next($request);
            break;

            case 'create':
                if($result->create ==1)
                    return $next($request);
            break;

            case 'store':
                if($result->create ==1)
                    return $next($request);
            break;

            case 'edit':
               if($result->edit ==1)
                    return $next($request);
            break;

            case 'update':
               if($result->edit ==1)
                    return $next($request);
            break;

            case 'delete':
                if($result->delete ==1 || $rold_id ==1)
                    return $next($request);
            break;

            default:
                return $next($request);
            break;
        }

        //dd($action);
         return response()->view('errors.permission-error');
        
    }
}
