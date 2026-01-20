<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Models\v7\Unit;
use App\Models\v7\Property;
use App\Models\v7\Building;

use App\Models\v7\Role;
use App\Models\v7\User;
use App\Models\v7\Employee;
use App\Models\v7\UserMoreInfo;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\UserLog;
use App\Models\v7\Device;
use App\Models\v7\UserDevice;
use App\Models\v7\UserRemoteDevice;
use App\Models\v7\Setting;
use App\Models\v7\Module;
use App\Models\v7\UserPermission;
use App\Models\v7\UserProperty;
use App\Models\v7\Card;

use App\Models\v7\UnittakeoverAppointment;

use App\Models\v7\Defect;
use App\Models\v7\DefectLocation;
use App\Models\v7\DefectType;
use App\Models\v7\DefectSubmission;
use App\Models\v7\JoininspectionAppointment;

use App\Models\v7\FacilityType;
use App\Models\v7\FacilityBooking;

use App\Models\v7\FeedbackOption;
use App\Models\v7\FeedbackSubmission;

use App\Models\v7\EformSetting;
use App\Models\v7\EformMovingInOut;
use App\Models\v7\EformMovingSubCon;
use App\Models\v7\EformRenovation;
use App\Models\v7\EformRenovationSubCon;
use App\Models\v7\EformRenovationDetail;
use App\Models\v7\EformDoorAccess;
use App\Models\v7\EformRegVehicle;
use App\Models\v7\EformRegVehicleDoc;
use App\Models\v7\EformChangeAddress;
use App\Models\v7\EformParticular;
use App\Models\v7\EformParticularOwner;
use App\Models\v7\EformParticularTenant;
use App\Models\v7\EformRegVehicleFileCat;

use App\Models\v7\VisitorType;
use App\Models\v7\VisitorBooking;
use App\Models\v7\VisitorList;
use App\Models\v7\ResidentFileSubmission;

use App\Models\v7\FinanceShareSetting;
use App\Models\v7\FinanceInvoice;
use App\Models\v7\FinanceInvoiceInfo;
use App\Models\v7\FinanceInvoiceDetail;
use App\Models\v7\FinanceInvoicePayment;
use App\Models\v7\FinanceReferenceType;
use App\Models\v7\FinanceInvoicePaymentDetail;

use App\Models\v7\BluetoothDoorOpen;
use App\Models\v7\FailDoorOpenRecord;
use App\Models\v7\CallPushRecord;
use App\Models\v7\UserFacialId;
use App\Models\v7\FacialRecoOption;
use App\Models\v7\QrcodeOpenRecord;

use App\Models\v7\UserLicensePlate;

use Illuminate\Http\Request;
use Validator;
use DB;
use Auth;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index()
    {
        $q ='';
        $account_id = Auth::user()->account_id;

        if(Auth::user()->role_id ==1)
            $units = Unit::paginate(env('PAGINATION_ROWS')); 
        else
            $units = Unit::where('account_id',$account_id)->orderBy('id','desc')->paginate(env('PAGINATION_ROWS'));  

        $currentURL = url()->full();
        $page = explode("=",$currentURL);
        if(isset($page[1]) && $page[1]>0){
                session()->put('page', $page[1]);
        }else{
                session()->forget('page');
        }

        //$units = Unit::paginate(150);   
        return view('admin.unit.index', compact('units','q'));
    }
    public function unitlist()
    {
        $q= $option = $buildings = $building  = $unit ='';

        $account_id = Auth::user()->account_id;

        if(Auth::user()->role_id ==1)
            $units = Unit::paginate(env('PAGINATION_ROWS')); 
        else
            $units = Unit::where('account_id',$account_id)->paginate(env('PAGINATION_ROWS'));  

        $currentURL = url()->full();
        $page = explode("=",$currentURL);
        if(isset($page[1]) && $page[1]>0){
                session()->put('page', $page[1]);
        }else{
                session()->forget('page');
        }
        $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

        //$units = Unit::paginate(150);   
        return view('admin.unit.unitlist', compact('units','q','option','buildings','building','unit'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $account_id = Auth::user()->account_id;

        $properties = Property::pluck('company_name', 'id')->all();
        $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

        return view('admin.unit.create', compact('properties','buildings'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       // print_r($request->unit);
        $input = $request->all();

       if(isset($input['account_id']))
            $input['account_id'] = $input['account_id'];
        else
            $input['account_id'] = Auth::user()->account_id;

        $PropertyObj = Property::find($input['account_id']);

        if($PropertyObj->unit_validation ==1 && 1==2){
            $validator = Validator::make($request->all(), [ 
                'unit' =>[
                    'required', 
                    Rule::unique('unites')
                        ->where('account_id', $input['account_id'])
                ],
                
            ]);
        }else{
            $validator = Validator::make($request->all(), [ 
                'unit' =>[
                    'required', 
                    Rule::unique('unites')
                        ->where('account_id', $input['account_id'])
                        ->where('building_id', $input['building_id'])
                ],
                
            ]);
        }

        if ($validator->fails()) { 

             return redirect('opslogin/configuration/unit/create#settings')->with('status', 'Unit already exist!');         
        }
        $char = array("_","-","_");
        $code = str_replace($char,"",$input['unit']);
        $input['code'] = Crypt::encryptString($code);
        $input['unit'] = Crypt::encryptString($input['unit']);
        $input['encrypted'] = 1;
        $unit =  Unit::create($input);
        $buildingObj = Building::find($unit->building_id);

        $unitObj = Unit::find($unit->id);

        

        if($unit->id >0){

            $auth = new \App\Models\v7\Property();
            $thinmoo_access_token = $auth->thinmoo_auth_api();  
           
            
            $api_obj = new \App\Models\v7\Unit();
            $unit_result = $api_obj->unit_check_record($thinmoo_access_token,$unitObj);
            
            if($unit_result['code'] ==0){
                $unit_info = $api_obj->unit_update_api($thinmoo_access_token,$unitObj);
            }
            else{
                $unit_info= $api_obj->unit_add_api($thinmoo_access_token,$unitObj);
            }
        }

       
        
        return redirect('opslogin/configuration/unit#settings')->with('status', 'Unit has been added!');
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
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\v7\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $account_id = Auth::user()->account_id;

        $unitObj = Unit::find($id);
        $properties = Property::pluck('company_name', 'id')->all();
        $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

        return view('admin.unit.edit', compact('unitObj','properties','buildings'));
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

        $unitObj = Unit::find($id);

        if(isset($input['account_id']))
            $input['account_id'] = $input['account_id'];
        else
            $input['account_id'] = Auth::user()->account_id;

        $PropertyObj = Property::find($input['account_id']);

        if($PropertyObj->unit_validation ==1){
            $validator = Validator::make($request->all(), [ 
                'unit' =>[
                    'required', 
                    Rule::unique('unites')
                        ->where('account_id', $input['account_id'])
                        ->whereNotIn('id',[$id])
                ],
                
            ]);
        }else{
            $validator = Validator::make($request->all(), [ 
                'unit' =>[
                    'required', 
                    Rule::unique('unites')
                        ->where('account_id', $input['account_id'])
                        ->where('building_id', $request->input('building_id'))
                        ->whereNotIn('id',[$id])
                ],
                
            ]);
        }

        if ($validator->fails()) { 
             return redirect("opslogin/configuration/unit/$id/edit")->with('status', 'Unit already exist!');         
        }

        
        if($request->input('code') ==''){
            $buildingObj = Building::find($unitObj->building_id);
            //$code = $buildingObj->building_no.$unitObj->id;
            $char = array("_","-","_");
            $code = str_replace($char,"",$request->input('unit'));
            $unitObj->code = Crypt::encryptString($code);
        }

        $unitObj->building_id = $request->input('building_id');
        $unitObj->unit = Crypt::encryptString($request->input('unit'));
        //$unitObj->code = $request->input('code');
        $unitObj->size = $request->input('size');
        $unitObj->share_amount = $request->input('share_amount');
        $unitObj->encrypted = 1;

        $unitObj->save();

        if($unitObj->id >0){

            $auth = new \App\Models\v7\Property();
            $thinmoo_access_token = $auth->thinmoo_auth_api();  
           
            $api_obj = new \App\Models\v7\Unit();
            $unit_result = $api_obj->unit_check_record($thinmoo_access_token,$unitObj);

           
            if($unit_result['code'] ==0){
                $unit_info = $api_obj->unit_update_api($thinmoo_access_token,$unitObj);
            }
            else{
                $unit_info= $api_obj->unit_add_api($thinmoo_access_token,$unitObj);
            }
        }
       
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/configuration/unit?page=$page#settings")->with('status', 'Unit has been updated!');}
        else
            return redirect('opslogin/configuration/unit#settings')->with('status', 'Unit has been updated!');

        
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

        $UnitObj = Unit::find($id);

        if($UnitObj->id >0){
            $auth = new \App\Models\v7\Property();
            $thinmoo_access_token = $auth->thinmoo_auth_api();  
           
            
            $api_obj = new \App\Models\v7\Unit();
            $unit_result = $api_obj->unit_check_record($thinmoo_access_token,$UnitObj);
            
            if($unit_result['code'] ==0){
                $unit_info = $api_obj->unit_delete_api($thinmoo_access_token,$UnitObj);
            }
           
        }

        Unit::findOrFail($id)->delete();
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/configuration/unit?page=$page#settings")->with('status', 'Unit deleted successfully!');}
        else
            return redirect('opslogin/configuration/unit#settings')->with('status', 'Unit deleted successfully!');
    }

    /** SEARCH function  START*/
     public function search(Request $request){
        $q= $option = $buildings = $building  = $units = $unit ='';
        $account_id = Auth::user()->account_id;

        //$option = $request->input('option'); 
        $building = $request->input('building');
        $unit = $request->input('unit'); 

        $building_units = Unit::where('account_id',$account_id)->where(function ($query) use ($building,$unit) {
            if($building !='')
                $query->where('building_id',$building);
        })->orderBy('id','desc')->get();

        $unitlists = array();
        if($unit !='' && !empty($building_units)){
            foreach($building_units as $unitid){
                if(Crypt::decryptString($unitid->unit) ===$unit)
                    $unitlists[] = $unitid->id;
                else if ($request->input('unit') =='')
                    $unitlists[] = $unitid->id;
            }
        }

        $units = Unit::where('account_id',$account_id)->where(function ($query) use ($building,$unit,$unitlists) {
            if($building !='')
                $query->where('building_id',$building);
            if(isset($unitlists) && count($unitlists) >0)
                $query->WhereIn('id', $unitlists);

        })->orderBy('id','desc')->paginate(env('PAGINATION_ROWS'));
        //$query->where('unit', 'LIKE', '%'.$unit .'%');

        $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

         
        return view('admin.unit.unitlist' , compact('units','q','option','buildings','building','unit'));
        
   }

   /** SEARCH function  END*/

    public function getunitlist(Request $request)
    {
        $finalUnits = [];
        if($request->filled('term'))
        {
            
            $term = trim($request->term);
            $allUnits = Unit::select("id","unit")->where([['status','=',1],['unit','!=',''],['account_id','=',Auth::user()->account_id]])->get()->map(function($e){
                $e->unit = trim(Crypt::decryptString($e->unit));
                return $e;
            })->unique('unit')->values();
            
            $filtered = $allUnits->filter(fn($v) => str_contains($v['unit'], $term));
            $result = $filtered->values();
            if(count($result)>0) foreach($result as $f) $finalUnits[$f['id']] = $f['unit'];
            sort($finalUnits);
        }
        
        return json_encode($finalUnits);
    }

    public function uploadcsv()
    {
        $properties = Property::pluck('company_name', 'id')->all();
        return view('admin.unit.uploadcsv', compact('properties'));
    }

    public function importcsv(Request $request){
        $input = $request->all();
        if(isset($input['account_id']))
            $property = $input['account_id'];
        else
            $property = Auth::user()->account_id;

        if ($request->file('csv_file') != null) {
            $extension = $request->file('csv_file')->getClientOriginalName();
            $filename = uniqid().'.'.$extension; 
            $csv_file_path = $request->file('csv_file')->storeAs("units/$property",$filename);
        }
        $file_path = "app/".$csv_file_path;
        $filename =  base_path() .\Storage::url($file_path);
        $unitArr = $this->csvToArray($filename);
        //print_r($buildingArr);
        $json_data = array();
        if(isset($unitArr)){
            foreach($unitArr as $k => $unit){
                //print_r($unit);
                //echo "here";
                $buildingObj = Building::where('building',$unit[0])->where('account_id',$property)->first();
                if(isset($buildingObj)){
                    //echo "hello";
                    $validation = Unit::where('unit',$unit[1])->where('building_id', $buildingObj->id)->where('account_id',$property)->first();
                    //print_r($validation);
                    if(empty($validation)){
                       // echo "hi";
                        $data = array();
                        $unit_name = str_replace("#","",$unit[1]);
                        $data['unit'] =  Crypt::encryptString($unit_name);
                        $data['building_id'] =  $buildingObj->id;
                        $data['account_id'] = $property;
                        $char = array("_","-","_");
                        $code = str_replace($char,"",$unit_name);
                        $data['code'] = Crypt::encryptString($code);
                        $data['size'] = $unit[2];
                        $data['share_amount'] = $unit[3];
                        $unit = Unit::create($data);
                        if($unit->id >0){
                            $json_data[] = array('name'=>$unit_name,'uuid'=>$unit->id,'buildingUuid'=>$buildingObj->id,'code'=>$code);
                        }
                    }
                }
            }
            //print_r( $json_data);

            if(count($json_data)>0){
                $auth = new \App\Models\v7\Property();
                $thinmoo_access_token = $auth->thinmoo_auth_api();
                $api_obj = new \App\Models\v7\Unit();
                $unit_info= $api_obj->unit_bulkadd_api($thinmoo_access_token,$json_data,$property);
            } 

            //print_r($unit_info);
            //exit;

            
        }
        
        return redirect('opslogin/configuration/unit#settings')->with('status', 'Unit(s) has been imported!');


    }


    function csvToArray($filename = '', $delimiter = ',')
    {
       
        if (!file_exists($filename)){
            echo "File not exist";
        } if(!is_readable($filename)){
            echo "File not readable";
        }

       
        $header = null;
        $data = array();
       
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                    $data[] = $row;
            }
            fclose($handle);
        }

        return $data;
    }

    function csvToArray1($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }

    public function importcsv1()
    {
       
        $file = public_path('import/'.Auth::user()->account_id.'/units.csv');

        $unitArr = $this->csvToArray($file);
      

        for ($i = 0; $i < count($unitArr); $i ++)
        {
            $unitArr[$i]['unit'] = str_replace("#",'',$unitArr[$i]['unit']);
            $unitArr[$i]['account_id'] = Auth::user()->account_id;
            $userinfo = Unit::create($unitArr[$i]);
        }

        return redirect('opslogin/configuration/unit#settings')->with('status', 'Records has been imported!');
    }
    public function redirect_summary($id, $tab = 1){

        return redirect("opslogin/unit_summary/$id/$tab");
    }

    public function unit_summary($id, $tab = 1)
    {
        $account_id = Auth::user()->account_id;
        $file_path = env('APP_URL')."/storage/app";
        $visitor_app_url = env('VISITOR_APP_URL');
        $app_user_lists = explode(",",env('USER_APP_ROLE'));
        session()->forget('searchpage');
        session()->forget('search_url');
        $UnitObj = Unit::find($id);
        $userids = array();
		$userObj = User::select('id')->where('account_id',$account_id)->where('unit_no',$UnitObj->id)->get();
		/*foreach($userObj as $k => $user){
			$userids[] = $user->id;
        }*/
        session()->put('current_page', 'unit_summary');

        $unitids_byusers = UserPurchaserUnit::where('property_id', $account_id)->where('unit_id', $id)->get();

        $prop_userids =array();
        foreach($unitids_byusers as $k =>$v){
            $prop_userids[] = $v->user_info_id;
        }
        //print_r($prop_userids);

        $users = UserMoreInfo::WhereIn('id',$prop_userids)->orderBy('user_id','DESC')->get();

        //$users = User::where('account_id',$account_id)->where('unit_no',$UnitObj->id)->orderBy('id','DESC')->get();
        //$users = User::WhereIn('id',$prop_userids)->orderBy('id','DESC')->get();


        $cards = Card::where('account_id',$account_id)->where('unit_no',$UnitObj->id)->orderBy('id','DESC')->get();
        $keycollections = UnittakeoverAppointment::where('account_id',$account_id)->where('unit_no',$UnitObj->id)->orderBy('id','DESC')->get();
        $defects = Defect::where('account_id',$account_id)->where('unit_no',$UnitObj->id)->orderBy('id','DESC')->get();
        $feedbacks = FeedbackSubmission::where('account_id',$account_id)->where('unit_no',$UnitObj->id)->orderBy('id','DESC')->get();
        $visitors_bookings = VisitorBooking::where('account_id',$account_id)->where('unit_no',$UnitObj->id)->orderBy('id','DESC')->get();
        $facilities = FacilityBooking::where('account_id',$account_id)->where('unit_no', $UnitObj->id)->orderby('id','desc')->get();   

        $moveinouts = EformMovingInOut::where('account_id',$account_id)->where('unit_no',$UnitObj->id)->orderBy('id','DESC')->get();
        $renovations = EformRenovation::where('account_id',$account_id)->where('unit_no',$UnitObj->id)->orderBy('id','DESC')->get();
        $dooraccess = EformDoorAccess::where('account_id',$account_id)->where('unit_no',$UnitObj->id)->orderBy('id','DESC')->get();
        $vehicleuis = EformRegVehicle::where('account_id',$account_id)->where('unit_no',$UnitObj->id)->orderBy('id','DESC')->get();
        $addresses = EformChangeAddress::where('account_id',$account_id)->where('unit_no',$UnitObj->id)->orderBy('id','DESC')->get();
        $particulars = EformParticular::where('account_id',$account_id)->where('unit_no',$UnitObj->id)->orderBy('id','DESC')->get();

        $invoices = FinanceInvoice::where('account_id',$account_id)->where('unit_no',$UnitObj->id)->orderBy('id','DESC')->get();
        $openrecords = BluetoothDoorOpen::where('account_id',$account_id)->where('unit_no',$UnitObj->id)->orderBy('id','DESC')->get();
        $fileuploads = ResidentFileSubmission::where('account_id',$account_id)->where('unit_no',$UnitObj->id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS')); 

        $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

        $License_plates = UserLicensePlate::where('property_id',$account_id)->where('unit_id',$UnitObj->id)->orderBy('id','DESC')->get();


        return view('admin.unit.summary', compact('UnitObj','file_path','visitor_app_url','tab','users','cards','keycollections','defects','feedbacks','visitors_bookings','moveinouts','renovations','dooraccess','vehicleuis','addresses','particulars','invoices','openrecords','facilities','buildings','fileuploads','app_user_lists','License_plates'));
    }

     /** SEARCH function  START*/
     public function summarysearch(Request $request){
        $q= $option = $buildings = $building  = $units = $unit ='';
        $account_id = Auth::user()->account_id;
        $building = $request->input('building');
        $unit = $request->input('unit'); 
        $currect_unit = $request->input('current_unit'); 

        $result = Unit::where('account_id',$account_id)->where(function ($query) use ($building,$unit) {
            if($building !='')
                $query->where('building_id',$building);
            if($unit !='')
                $query->where('unit', 'LIKE', '%'.$unit .'%');
        })->first();
       
        if(isset($result)){
            return redirect("opslogin/unit_summary/$result->id")->with('status', 'Unit summary changed');   
        }else{
            return redirect("opslogin/unit_summary/$currect_unit")->with('status', 'Selected Unit not available!'); 
        }

        
   }

    public function encrypt(Request $request){
        $unitsObj = Unit::where('encrypted',0)->get();
        if(isset($unitsObj)){
            foreach($unitsObj as $k => $unitobj){
                $unit_name =Crypt::encryptString($unitobj->unit);
                $unit_code =Crypt::encryptString($unitobj->code);
                Unit::where('id',$unitobj->id)->update(['unit_name'=>Crypt::decryptString($unit_name),'unit_code'=>Crypt::decryptString($unit_code)]);
            }
            return redirect('opslogin/configuration/unit#settings')->with('status', 'Data has been encrypted!');
        }
        else{
            return redirect('opslogin/configuration/unit#settings')->with('status', 'All data encrypted already!');
        }
    }

    public function batchupdate(Request $request){
        $account_id = Auth::user()->account_id;

        $unitsObj = Unit::where('account_id',$account_id)->where('test_field',0)->orderby('id','asc')->limit(50)->get();
        if(isset($unitsObj)){
            $auth = new \App\Models\v7\Property();
            $thinmoo_access_token = $auth->thinmoo_auth_api();  
            foreach($unitsObj as $k => $unitObj){
                //echo $unitobj->id ." : ".Crypt::decryptString($unitobj->unit)."<br />";
               
                $api_obj = new \App\Models\v7\Unit();
                $unit_info= $api_obj->unit_add_api($thinmoo_access_token,$unitObj);
                
                Unit::where('id',$unitObj->id)->update(['test_field'=>1]);
            }
            //exit;
            return redirect('opslogin/configuration/unit#settings')->with('status', 'Data has been updated!');
        }
        else{
            return redirect('opslogin/configuration/unit#settings')->with('status', 'All data updated!');
        }
    }


}
