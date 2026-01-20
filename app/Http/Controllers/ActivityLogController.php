<?php

namespace App\Http\Controllers;

use App\Models\v7\ActivityLog;
use App\Models\v7\UserMoreInfo;
use App\Models\v7\Property;
use Illuminate\Validation\Rule;

use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use DB;
use Auth;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index()
    {
        $q = $property = $first_name = $last_name = $email = '';
        $account_id = Auth::user()->account_id;

        $lists = ActivityLog::orderby('id','desc')->paginate(env('PAGINATION_ROWS')); 

        $properties = Property::orderby('company_name','asc')->pluck('company_name', 'id')->all();
       
        return view('admin.log.index', compact('lists','q','properties','property','first_name','last_name','email'));
    }

    
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\v7\Leave  $leave
     * @return \Illuminate\Http\Respons
     */
    public function show($id)
    {
        //
    }

   

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\v7\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        ActivityLog::findOrFail($id)->delete();
        return redirect('opslogin/loghistory')->with('status', 'Record deleted successfully!');
    }

    

    public function search(Request $request)
    {
        $q = $property = $first_name = $last_name = $email = '';
        $first_name = $request->input('first_name');
        $email = $request->input('email');
        $last_name = $request->input('last_name');
        $property = $request->input('property');

        $userids =array();
        if($first_name !=''){
            $user_more_info = UserMoreInfo::where('first_name', 'LIKE', '%'.$first_name .'%')->orderby('id','desc')->get();
               foreach($user_more_info as $k =>$v){
                $userids[] = $v->id;
               }
        }

        if($last_name !=''){
            $user_more_info = UserMoreInfo::where('last_name', 'LIKE', '%'.$last_name .'%')->orderby('id','desc')->get();
               foreach($user_more_info as $k =>$v){
                $userids[] = $v->id;
               }
        }

        if($email !=''){
            $email_userids =array();
            $user_emailids = User::where('email', 'LIKE', '%' . $email . '%')->orderby('id','desc')->get();        
            foreach($user_emailids as $v3){
                $email_userids[] = $v3->id;
            }
            if($property !='')
            {
                $user_more_info = UserMoreInfo::whereIn('user_id',$email_userids)->where('account_id',$property)->get();
                foreach($user_more_info as $k =>$v){
                $userids[] = $v->id;
                }
            }
            else{
                $user_more_info = UserMoreInfo::whereIn('user_id',$email_userids)->get();
                foreach($user_more_info as $k =>$v){
                $userids[] = $v->id;
                }
            }
        }
        
        $lists = ActivityLog::where(function ($query) use ($property,$userids) {
            if($property !=''){
                $query->where('account_id', $property);
            } 
            if(count($userids) >0){
                $query->whereIn('ref_id',$userids);
            }  
        })->orderby('id','desc')->paginate(env('PAGINATION_ROWS')); 

        $properties = Property::orderby('company_name','asc')->pluck('company_name', 'id')->all();
       
        return view('admin.log.index', compact('lists','q','properties','property','first_name','last_name','email'));
    }
}
