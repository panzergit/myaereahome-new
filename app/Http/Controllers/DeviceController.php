<?php

namespace App\Http\Controllers;

use App\Models\v7\Device;
use App\Models\v7\Unit;
use Session;
use App\Models\v7\RoleDevice;
use App\Models\v7\RoleRemoteDevice;
use App\Models\v7\Building;
use App\Models\v7\Property;
use App\Models\v7\FacilityType;
use App\Models\v7\UserDevice;
use App\Models\v7\UserRemoteDevice;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\UserProperty;
use Illuminate\Validation\Rule;

use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use DB;
use Auth;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index()
    {
        $q= $option = $name = $status  = $serial_no ='';

        $account_id = Auth::user()->account_id;

        if(Auth::user()->role_id ==1)
            $devices = Device::get(); 
        else
            $devices = Device::where('account_id',$account_id)->paginate(env('PAGINATION_ROWS'));  

        $auth = new \App\Models\v7\Property();
        $token = $auth->thinmoo_auth_api();  

        //$devices = Device::paginate(150);   
        return view('admin.device.index', compact('devices','q','name','option','status','serial_no','account_id','token'));
    }
    public function new()
    {
        $q= $option = $name = $status  = $serial_no ='';

        $account_id = Auth::user()->account_id;

        if(Auth::user()->role_id ==1)
            $devices = Device::get(); 
        else
            $devices = Device::where('account_id',$account_id)->paginate(env('PAGINATION_ROWS'));  

        $auth = new \App\Models\v7\Property();
        $token = $auth->thinmoo_auth_api();  

        //$devices = Device::paginate(150);   
        return view('admin.device.indexnew', compact('devices','q','name','option','status','serial_no','account_id','token'));
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
        $unites = Unit::where('account_id',$account_id)->pluck('unit', 'id')->all();
        $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();
        $facilities = FacilityType::where('account_id',$account_id)->pluck('facility_type', 'id')->all();
        $start = strtotime('05:00');
        $end   = strtotime('22:00');
        $time_values = array();
        for ($i=$start; $i<=$end; $i = $i + 30*60){
            $key = date('H:i',$i);
            $time_values[$key] = date('g:i A',$i);
        }
        $advance_entry=array("60"=>"1:00","90"=>"1.30","120"=>"2:00");

        return view('admin.device.create', compact('properties','unites','buildings','facilities','time_values','advance_entry'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       // print_r($request->Device);
        $input = $request->all();

       if(isset($input['account_id']))
            $input['account_id'] = $input['account_id'];
        else
            $input['account_id'] = Auth::user()->account_id;

        $validator = Validator::make($request->all(), [ 
            'device_serial_no' =>[
                'required', 
                Rule::unique('devices')
                       ->where('account_id', $input['account_id'])
            ],
            
        ]);
        if ($validator->fails()) { 

             return redirect('opslogin/device/create')->withInput()->with('status', 'Device already exist!');         
        }

       /* if(isset($input['building_nos']) && count($input['building_nos']) >0){
            $input['locations'] = implode(",",$input['building_nos']);
        }*/

        $auth = new \App\Models\v7\Property();
        $thinmoo_access_token = $auth->thinmoo_auth_api();  
        $api_obj = new \App\Models\v7\Device();
        /*$result = $api_obj->device_status($thinmoo_access_token,$input['account_id'],$input['device_serial_no']);

        if( $result !=0)
            $device = Device::create($input);
        else{
            return redirect('opslogin/device/create')->with('status', 'Device serial no. does not exist!');         

        }  

        */
        if( $request->input('device_type') ==2){
            $input['locations'] = implode(",", $request->input('locations'));
        }else{
            $input['locations'] = $request->input('location');
        }
        //exit;
        $device = Device::create($input);

        if($device->id >0){
            
            $device_input['role_id'] = 3;
            $device_input['account_id'] = $device->account_id;
            $device_input['device_id'] = $device->id;
            $device_input['device_svn'] = $device->device_serial_no;
            RoleDevice::create($device_input);  
            RoleRemoteDevice::create($device_input); 

           
            $device_result = $api_obj->device_check_record($thinmoo_access_token,$device);
            
            if($device_result['code'] ==0){
                $device_info = $api_obj->device_update_api($thinmoo_access_token,$device);
            }
            else{
               
                $device_info= $api_obj->device_add_api($thinmoo_access_token,$device);
            }
        }



        
        return redirect('opslogin/device')->with('status', 'Device has been added!');
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

        $DeviceObj = Device::find($id);
        $properties = Property::pluck('company_name', 'id')->all();
        $unites = Unit::where('account_id',$account_id)->pluck('unit', 'id')->all();
        $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();
        $facilities = FacilityType::where('account_id',$account_id)->pluck('facility_type', 'id')->all();
        $start = strtotime('05:00');
        $end   = strtotime('22:00');
        $time_values = array();
        for ($i=$start; $i<=$end; $i = $i + 30*60){
            $key = date('H:i',$i);
            $time_values[$key] = date('g:i A',$i);
        }
        $advance_entry=array("60"=>"1:00","90"=>"1.30","120"=>"2:00");
        /*$auth = new \App\Models\v7\Property();
        $thinmoo_access_token = $auth->thinmoo_auth_api();  
       
        $api_obj = new \App\Models\v7\Device();
        $device_result = $api_obj->device_status($thinmoo_access_token,$account_id,$DeviceObj->device_serial_no,$DeviceObj->id);

        */
        return view('admin.device.edit', compact('DeviceObj','properties','unites','buildings','facilities','time_values','advance_entry'));
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

        $DeviceObj = Device::find($id);

        if(isset($input['account_id']))
            $input['account_id'] = $input['account_id'];
        else
            $input['account_id'] = Auth::user()->account_id;

        $validator = Validator::make($request->all(), [ 
            'device_serial_no' =>[
                'required', 
                Rule::unique('devices')
                    ->where('account_id', $input['account_id'])
                    ->whereNotIn('id',[$id])
            ],
            
        ]);

        if ($validator->fails()) { 
             return redirect("opslogin/device/$id/edit")->withInput()->with('status', 'Device already exist!');         
        }

        $input = $request->all();

        
        //$DeviceObj->locations = $request->input('locations');
        
        $DeviceObj->model = $request->input('model');
        $DeviceObj->device_name = $request->input('device_name');
        $DeviceObj->device_serial_no = $request->input('device_serial_no');
        $DeviceObj->status = 1;
        $DeviceObj->device_type = $request->input('device_type');
        if( $request->input('device_type') ==2){
            $DeviceObj->locations = implode(",", $request->input('locations'));
        }else{
            $DeviceObj->locations = $request->input('location');
        }
        $DeviceObj->proximity_setting = $request->input('proximity_setting');
        $DeviceObj->facility_type = $request->input('facility_type');
        $DeviceObj->entry_allowed_in_advance = $request->input('entry_allowed_in_advance');
        $DeviceObj->start_time = $request->input('start_time');
        $DeviceObj->end_time = $request->input('end_time');
        
        $auth = new \App\Models\v7\Property();
        $thinmoo_access_token = $auth->thinmoo_auth_api();  
        $api_obj = new \App\Models\v7\Device();
        $result = $api_obj->device_status($thinmoo_access_token,$DeviceObj->account_id, $DeviceObj->device_serial_no);

        if( $result !=0)
            $device = $DeviceObj->save();
        else{
            return redirect("opslogin/device/$id/edit")->with('status', 'Device serial no. does not exist!');         

        }

        



        if($DeviceObj->id >0){

          
            $device_result = $api_obj->device_check_record($thinmoo_access_token,$DeviceObj);
            
            if($device_result['code'] ==0){
                $device_info = $api_obj->device_update_api($thinmoo_access_token,$DeviceObj);
            }
            else{
                $device_info= $api_obj->device_add_api($thinmoo_access_token,$DeviceObj);
            }
        }

       
       
        return redirect('opslogin/device')->with('status','Device has been updated!');
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
        RoleDevice::where('device_id', $id)->delete();
        RoleRemoteDevice::where('device_id', $id)->delete();
        UserDevice::where('device_id', $id)->delete();
        UserRemoteDevice::where('device_id', $id)->delete();
        $DeviceObj = Device::find($id);

        if($DeviceObj->id >0){
            $auth = new \App\Models\v7\Property();
            $thinmoo_access_token = $auth->thinmoo_auth_api();  
           
            
            $api_obj = new \App\Models\v7\Device();
            $device_result = $api_obj->device_check_record($thinmoo_access_token,$DeviceObj);
            
            if($device_result['code'] ==0){
                $device_info = $api_obj->device_delete_api($thinmoo_access_token,$DeviceObj);
            }
           
        }

        Device::findOrFail($id)->delete();
        return redirect('opslogin/device')->with('status', 'Device deleted successfully!');
    }

    public function restart($id)
    {
        //
        $DeviceObj = Device::find($id);

        if($DeviceObj->id >0){
            $auth = new \App\Models\v7\Property();
            $thinmoo_access_token = $auth->thinmoo_auth_api();  
            
            $api_obj = new \App\Models\v7\Device();
            $device_result = $api_obj->device_restart($thinmoo_access_token,$DeviceObj);
            
           
        }

        return redirect('opslogin/device')->with('status', 'Command to restart device has been sent successfully. Device will restart within a minute.');
    }


     public function search(Request $request){

        $q= $option = $name = $status  = $serial_no ='';
        $option = $request->input('option'); 
        $name = $request->input('name');
        $serial_no = $request->input('serial_no');
        $status = $request->input('status');

        $account_id = Auth::user()->account_id;

        $devices = Device::where('account_id',$account_id)->where(function ($query) use ($name,$status,$serial_no) {
            if($name !='')
                $query->where('device_name', 'LIKE', '%' . $name . '%');
            if($status !='')
                $query->where('status',$status);
            if($serial_no !='')
                $query->where('device_serial_no', 'LIKE', '%' . $serial_no . '%');
            
        })->paginate(env('PAGINATION_ROWS')); 

        /*
        if ($option != '') {
            if($option == 'name') {
                $devices = Device::where('account_id',$account_id)->where(function ($query) use ($name) {
                        $query->where('device_name', 'LIKE', '%' . $name . '%');
                    })->paginate(env('PAGINATION_ROWS'));
            }
            
            if($option == 'status') {
                $devices = Device::where('account_id',$account_id)->where('status',$status)->paginate(env('PAGINATION_ROWS'));
            }

            if($option == 'serial_no' ) { 
                $devices = Device::where('account_id',$account_id)->where(function ($query) use ($serial_no) {
                    $query->where('device_serial_no', 'LIKE', '%' . $serial_no . '%');
                })->paginate(env('PAGINATION_ROWS'));
            }
            
        }
        */
            $auth = new \App\Models\v7\Property();
            $token = $auth->thinmoo_auth_api(); 
            return view('admin.device.index', compact('token', 'devices','q','option','name','status','serial_no','account_id'));

        
   }


   public function getdevices(Request $request)
    {
        
        $devices = array();

        $unit = $request->unit;
        
        $devices = DB::table("devices")->where("status",1)->where('unit_no',$unit)->orderby('Device','asc')->pluck("Device","id");

         return json_encode($devices);

       /*

        $employees =   DB::table('users')->select('id','name')->where('role_id', $role)->orderBy('name','asc')->get();
       // $employees = User::where('name', 'LIKE', "%" . $term . "%")->take(10)->get();

        $data = [];

        foreach ($employees as $key => $value) {
            $empname = $value->name;
            $data[] = ['id' => $value->id, 'value' => $empname];
        }
        return response()->json($data);
        $data = []; 
        */
    }

    function csvToArray($filename = '', $delimiter = ',')
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

    public function importcsv()
    {
       
        $file = public_path('import/'.Auth::user()->account_id.'/devices.csv');

        $DeviceArr = $this->csvToArray($file);
      

        for ($i = 0; $i < count($DeviceArr); $i ++)
        {
            $DeviceArr[$i]['Device'] = str_replace("#",'',$DeviceArr[$i]['Device']);
            $DeviceArr[$i]['account_id'] = Auth::user()->account_id;
            $userinfo = Device::create($DeviceArr[$i]);
        }

        return redirect('opslogin/device')->with('status', 'Records has been imported!');
    }

    
    public function	devicestatus(Request $request) {
		
        $serial_no = $request->serial_no;
        $account_id = $request->account_id;
        $DeviceObj = Device::where('id',$serial_no)->first();

        if($DeviceObj->id >0){
            $auth = new \App\Models\v7\Property();
            $thinmoo_access_token = $auth->thinmoo_auth_api();  
            $api_obj = new \App\Models\v7\Device();
            $device_result = $api_obj->device_status($thinmoo_access_token,$account_id,$DeviceObj->device_serial_no);
            if(isset($device_result['connectionStatus']) && $device_result['connectionStatus'] ==1)
                 $result=1;
            else
                $result=2;
        }
        else{
            $result=0;
        }
        return json_encode($result);
	}

    public function batchassign($id)
    {
        //
        $account_id = Auth::user()->account_id;
        $DeviceObj = Device::find($id);
        if($DeviceObj->id >0){
            $building_ids = explode(",",$DeviceObj->locations);
            $PurchasedUnits = UserPurchaserUnit::whereIn('building_id',$building_ids)->where('status',1)->get();
            $userids = '';
            if(!empty($PurchasedUnits)){
                foreach($PurchasedUnits as $PurchasedUnit){
                    $userids .=$PurchasedUnit->user_id.",";
                    $device_input['user_id'] = $PurchasedUnit->user_id;
                    $device_input['account_id'] = $account_id;
                    $device_input['building_id'] = $PurchasedUnit->building_id;
                    $device_input['unit_no'] = $PurchasedUnit->unit_id;
                    $device_input['device_id'] = $DeviceObj->id;
                    $device_input['device_svn'] = $DeviceObj->device_serial_no;
                    UserDevice::create($device_input);  
                    UserRemoteDevice::create($device_input); 

                    UserPurchaserUnit::where('id', $PurchasedUnit->id)
                    ->update(['receive_call' => 1]);
                }
                $useruuids = substr($userids,0,-1);
            }
            if($userids != ''){
                $auth = new \App\Models\v7\Property();
                $thinmoo_access_token = $auth->thinmoo_auth_api();  
                $api_obj = new \App\Models\v7\Device();
                $device_result = $api_obj->device_batch_add($thinmoo_access_token,$DeviceObj,$useruuids);
                return redirect('opslogin/device')->with('status', 'Device has been assigned to all the user(s) of device location.');
            }else{
                return redirect('opslogin/device')->with('status', 'User(s) not available in device location.');
            }
        }
    }

     public function batchassignemp($id){
            $account_id = Auth::user()->account_id;
            $DeviceObj = Device::find($id);
            if(!empty($DeviceObj)){
                $locations = explode(",",$DeviceObj->locations);
                $employees = UserProperty::where('property_id',$account_id)->get();
                if(!empty($employees)){
                    foreach($employees as $emp){
                        foreach($locations as $location){
                            //echo "Emp :".$emp->user_id."<br />";
                            //echo $location;
                            //echo "<br />";
                            $device_input['user_id'] = $emp->user_id;
                            $device_input['account_id'] = $account_id;
                            $device_input['building_id'] = $location;
                            //$device_input['unit_no'] = $PurchasedUnit->unit_id;
                            $device_input['device_id'] = $DeviceObj->id;
                            $device_input['device_svn'] = $DeviceObj->device_serial_no;
                            UserDevice::create($device_input);  
                            UserRemoteDevice::create($device_input); 
                        }
                    }
                    return redirect('opslogin/device')->with('status', 'Device has been assigned to all the Employe(s).');

                }
                return redirect('opslogin/device')->with('status', 'Employee(s) not available.');
            }
             return redirect('opslogin/device')->with('status', 'Invalid Device.');
        }
    
}
