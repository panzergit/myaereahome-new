<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Session;

use Illuminate\Validation\Rule;
use App\Models\v7\Property;
use App\Models\v7\UserLicensePlate;
use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use App\Models\v7\Unit;
use App\Models\v7\UserMoreInfo;
use App\Models\v7\UserPurchaserUnit;

use Auth;
use DB;

class UserLicensePlateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $q ='';
        $file_path = env('APP_URL')."/storage/app/";
        $banners = HomeBanner::orderby('id','desc')->paginate(1000);   
        return view('admin.banner.index', compact('banners','file_path'));
    }

     public function list($id)
    {
        $q ='';
        $moreInfoObj = UserMoreInfo::find($id);
        $userPurchaseRec = UserPurchaserUnit::where('user_info_id', $id)->where('status',1)->orderby('id','desc')->get();
        $units_data = array();
        if($userPurchaseRec){
            foreach($userPurchaseRec as $rec){
                if($rec->addunitinfo->unit !=''){
                    $data =array();
                    $data['id']= $rec->unit_id;
                    $data['block']= ($rec->addubuildinginfo)?$rec->addubuildinginfo->building:'';
                    $data['unit']= Crypt::decryptString($rec->addunitinfo->unit);
                    $units_data[] = $data;
                }
            }
        }
        $license_plates =  UserLicensePlate::where('user_info_id',$id)->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS'));
        return view('admin.license.list', compact('license_plates','units_data','moreInfoObj'));
    }

    public function create() 
    {   
        //$properities = Property::paginate(50); 
        $assigned_property = array();
        
        $agent_properties = Property::where('status',1)->get();
        return view('admin.license.create', compact('agent_properties','assigned_property'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {      
       $input = $request->all();
        $validator = Validator::make($request->all(), [ 
            'banner_title' =>[
                'required', 
                Rule::unique('home_banners')
            ],            
        ]); 
       
        if ($validator->fails()) { 
             return redirect('opslogin/configuration/banner/create')->with('status', 'Banner already exist!');         
        }
        if ($request->file('banner_image') != null) {
            $input['banner_image'] = $request->file('banner_image')->store(upload_path('banner'));
        }

        $result = HomeBanner::create($input); 

        //for assign property to user agent
        if(@Auth::user()->role_id ==1){
            $properties = Property::where('status',1)->get();
            foreach($properties as $property) {
                $property_input = array();
                $property_checked = "property_".$property->id;
                if(isset($input[$property_checked]))
                    {                   
                        $property_input['banner_id'] = $result->id;
                        $property_input['property_id'] = $property->id;
                        HomeBannerProperty::create($property_input);  
                    }               
            }
        }

        
        $last_display_order = HomeBanner::orderby('display_order','desc')->first();

        $new_display_order = isset($last_display_order->display_order)?($last_display_order->display_order+1):1;

        HomeBanner::where( 'id' , $result->id)->update( array( 'display_order' => $new_display_order));

        return redirect('opslogin/configuration/banner/')->with('status', 'Banner added'); 
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
    public function add($id)
    {
        //
        $moreInfoObj = UserMoreInfo::find($id);
        $userPurchaseRec = UserPurchaserUnit::where('user_info_id', $id)->where('status',1)->orderby('id','desc')->get();
        $units_data = array();
        if($userPurchaseRec){
            foreach($userPurchaseRec as $rec){
                if($rec->addunitinfo->unit !=''){
                    $data =array();
                    $data['id']= $rec->unit_id;
                    $data['block']= ($rec->addubuildinginfo)?$rec->addubuildinginfo->building:'';
                    $data['unit']= Crypt::decryptString($rec->addunitinfo->unit);
                    $units_data[] = $data;
                }
            }
        }
        //print_r($units_data);
        return view('admin.license.create', compact('moreInfoObj','units_data'));
    }

    public function save(Request $request)
    {
        //
        $account_id = Auth::user()->account_id;
        $input = $request->all();
        $plate = '';
        if(isset($input['unit1']) && $input['unit1'] !='' && isset($input['license_plate1']) &&$input['license_plate1'] !=''){
            $data = array();
            $total_counts =  UserLicensePlate::where('unit_id',$input['unit1'])->count();
            if($total_counts <2){
                $unitObj = Unit::where('id',$input['unit1'])->first();
                $data['building_id'] =$unitObj->buildinginfo->id;
                $data['property_id'] =$account_id;
                $data['user_id'] =$input['user_id'];
                $data['user_info_id'] =$input['user_info_id'];
                $data['unit_id'] =$input['unit1'];
                $data['license_plate'] =$input['license_plate1'];
                UserLicensePlate::create($data);
                $plate .= $input['license_plate1'].",";
            }
        }
        if(isset($input['unit2']) && $input['unit2'] !='' && isset($input['license_plate2']) &&$input['license_plate2'] !=''){
            $total_counts =  UserLicensePlate::where('unit_id',$input['unit2'])->count();
            if($total_counts <2){
                $data = array();
                $unitObj = Unit::where('id',$input['unit2'])->first();
                $data['building_id'] =$unitObj->buildinginfo->id;
                $data['property_id'] =$account_id;
                $data['user_id'] =$input['user_id'];
                $data['user_id'] =$input['user_id'];
                $data['user_info_id'] =$input['user_info_id'];
                $data['unit_id'] =$input['unit2'];
                $data['license_plate'] =$input['license_plate2'];
                UserLicensePlate::create($data);
                $plate .= $input['license_plate2'];

            }
        }
        $user_info_id = $input['user_info_id'];
        if(Session::get('current_page') =='unit_summary'){
            $return_url = "opslogin/unit_summary/$licenseObj->unit_id/16";
            return redirect($return_url)->with('status', 'License Plate has been updated!');
        }
        else{
            $return_url = "opslogin/user/info/$user_info_id";
            if($plate !='')
                return redirect($return_url)->with('status', "License plate($plate) has been added!");
            else
                return redirect($return_url)->with('status', 'License plate already reached maximum limit!');
        }      
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\v7\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $LicenseObj = UserLicensePlate::find($id);
        return view('admin.license.edit', compact('LicenseObj'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\v7\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $licenseObj = UserLicensePlate::find($id);
        $input = $request->all();
        $licenseObj->license_plate =$request->input('license_plate');;
        $licenseObj->save();
        if(Session::get('current_page') =='unit_summary'){
            $return_url = "opslogin/unit_summary/$licenseObj->unit_id/16";
            return redirect($return_url)->with('status', 'License Plate has been updated!');
        }
        else{
            $return_url = "opslogin/user/info/$licenseObj->user_info_id";
            return redirect($return_url)->with('status', 'License Plate has been updated!');
        }      
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\v7\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       // $FileObj = HomeBanner::find($id);
        $licenseObj = UserLicensePlate::find($id);
      
        UserLicensePlate::findOrFail($id)->delete();
         if(Session::get('current_page') =='unit_summary'){
            $return_url = "opslogin/unit_summary/$licenseObj->unit_id/16";
            return redirect($return_url)->with('status', 'License plate has been deleted!');
        }
        else{
            $return_url = "opslogin/user/info/$licenseObj->user_info_id";
            return redirect($return_url)->with('status', 'License plate has been deleted!');
        }      
              
    }

   

    public function activate($id)
    {
        $result = HomeBanner::where( 'id' , $id)->update( array( 'status' => 1));
        return redirect('opslogin/configuration/banner#settings')->with('status', 'Banner activated!');;

    }

    public function deactivate($id)
    {
        $result = HomeBanner::where( 'id' , $id)->update( array( 'status' => 0));
        return redirect('opslogin/configuration/banner#settings')->with('status', 'Banner de-activated!');;

    }
}
