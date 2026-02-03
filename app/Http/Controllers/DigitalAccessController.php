<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Models\v7\Role;
use App\Models\v7\User;
use App\Models\v7\Device;
use App\Models\v7\RemoteDoorOpen;
use App\Models\v7\BluetoothDoorOpen;
use App\Models\v7\FailDoorOpenRecord;
use App\Models\v7\NormalDoorOpenRecord;
use App\Models\v7\CallPushRecord;
use App\Models\v7\UserMoreInfo;
use App\Models\v7\UserFacialId;
use App\Models\v7\FacialRecoOption;
use App\Models\v7\QrcodeOpenRecord;
use App\Models\v7\Building;
use App\Models\v7\Unit;
use App\Models\v7\VisitorBooking;
use Auth;
use DB;
use File;
use Validator;
use Session;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;



class DigitalAccessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $option = $relationship = $name = $last_name = $faceids = $unit ='';
      
        $account_id = Auth::user()->account_id;
        $auth = new \App\Models\v7\Property();
        $token = $auth->thinmoo_auth_api(); 

        $device = new \App\Models\v7\Device();
        $devices = $device->device_count_api($token,$account_id); 
        $device_count = str_pad($devices, 5, '0', STR_PAD_LEFT);

        $room = new \App\Models\v7\Room();
        $rooms = $room->room_count_api($token,$account_id); 
        $room_count = str_pad($rooms, 5, '0', STR_PAD_LEFT);

        $household = new \App\Models\v7\User();
        $households = $household->household_count_api($token,$account_id); 
        $household_count = str_pad($households, 5, '0', STR_PAD_LEFT);

         return view('admin.digital.index', compact('device_count','room_count','household_count'));
    }

    public function dooropennew(Request $request)
    {
        $account_id = Auth::user()->account_id;
        $relationships =  FacialRecoOption::where('status',1)->pluck('option', 'id')->all();
        $devices = [];
        $devicesMain = NormalDoorOpenRecord::where('account_id',$account_id)->orderBy('devname')->pluck('devname')->unique()->all();
        foreach ($devicesMain as $d) $devices[$d] = $d;
        
        $units = [];
        $allUnitIds = NormalDoorOpenRecord::where('account_id',$account_id)->pluck('unit_no')->unique()->all();
        $searchUnits = Unit::whereIn('id',$allUnitIds)->get()->map(fn($e) => ['id' => $e->id,'name' => Crypt::decryptString($e->unit)]);
        
        if($request->has('search')){
            
            $unit = $request->input('unit');
            $unit = trim($unit)!='' ? str_replace("#",'',$unit) : '';
            
            $userIDs = [];
            
            if($request->filled('name')) $userIDs = User::where('account_id',$account_id)->get()
                    ->filter(fn($e) => str_contains(strtolower(trim(Crypt::decryptString($e->name))),strtolower(trim($request->name))))
                    ->pluck('id')->toArray();
            
            $startDate = trim($request->startDate);
            $endDate = trim($request->endDate);
            $startTime = trim($request->startTime);
            $endTime = trim($request->endTime);
            $startdatetime=$enddatetime=null;
    
            if($startDate !='') $startdatetime = $startDate.($startTime !='' ? " ".$startTime.":00" : " 00:00:00");
            if($endDate !='') $enddatetime = $endDate.($endTime !='' ? " ".$endTime.":00" : " 23:59:59");
            
            
            $records = NormalDoorOpenRecord::where('account_id',$account_id)
                ->when(($request->has('unit') && trim($request->unit)!=''),fn($e) => $e->where('unit_no',trim($request->unit)))
                ->when(($request->has('doorName') && trim($request->doorName)!=''),fn($e) => $e->where('devname',trim($request->doorName)))
                ->when(!empty($userIDs),fn($e) => $e->whereIn('empuuid',$userIDs))
                ->when($startdatetime,fn($e) => $e->where('created_at','>=',$startdatetime))
                ->when($enddatetime,fn($e) => $e->where('created_at','<=',$enddatetime))
                ->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS'));
        }else{
            $records = NormalDoorOpenRecord::where('account_id',$account_id)->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS'));
        }
        
        return view('admin.digital.normaldooropen', compact('records','devices','searchUnits'));
    }

    public function dooropen()
    {
        $option = $device = $name = $doorName =$date = $devSn = $eventType = $records = $unit = $startDate =  $endDate = $startTime = $endTime ='';
      
        $account_id = Auth::user()->account_id;

        $auth = new \App\Models\v7\Property();
        $token = $auth->thinmoo_auth_api();  

        $url = env('THINMOO_API_URL')."normalOpenDoorlog/extapi/list";

        //The data you want to send via POST
        $fields = [
            'accessToken'       => $token,
            'extCommunityUuid'  => $account_id,
            'currPage'          =>1,
            'pageSize'          =>50,
            'startDatetime'     =>date('Y-m-d 00:00:00'),
            'endDatetime'       =>date('Y-m-d H:i:s')
        ];

        $fields_string = http_build_query($fields);

        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

        
        $results = curl_exec($ch);
        $json = json_decode($results,true);
        $err = curl_error($ch);
        curl_close($ch);

        $records =array();
        if(isset($json['data'])){
            $record_total_count = $json['data']['totalCount'];
            $records = $json['data']['list'];
        }
      
        /*foreach($lists as $k => $list){
            print_r($list);
            echo "<hr />";
        }*/

        $device = new \App\Models\v7\Device();
        $dev_lists = $device->device_lists_api($token,$account_id); 
        $devices = array();
        foreach($dev_lists as $dev_list){
            $devices[$dev_list['name']] =$dev_list['name'];
        }

       
        return view('admin.digital.dooropen', compact('records','option','doorName','eventType','date','devSn','startDate','endDate','startTime','endTime','devices'));
    }

    public function searchdooropen(Request $request)
    {
        $option = $device = $name = $doorName =$date = $devSn = $eventType = $records = $unit = $startDate =  $endDate = $startTime = $endTime = $startdatetime = $enddatetime ='';
      
        $account_id = Auth::user()->account_id;

        $auth = new \App\Models\v7\Property();
        $token = $auth->thinmoo_auth_api();  

        $url = env('THINMOO_API_URL')."normalOpenDoorlog/extapi/list";

        $doorName = $request->input('doorName');
        $eventType = $request->input('eventType');
        $empName = $request->input('empName');

        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $startTime = $request->input('startTime');
        $endTime = $request->input('endTime');

        if($startDate !='' || $endDate !='' ){
            if($startDate !=''){
                if($startTime !='')
                    $startdatetime = $startDate." ".$startTime.":00";
                else
                    $startdatetime = $startDate." 00:00:00";
            }
            
            if($endDate !=''){    
                if($endTime !='')    
                    $enddatetime = $endDate." ".$endTime.":00";
                else
                    $enddatetime = $endDate." 00:00:00";
            }
            if($startDate =='')
                $startdatetime = $enddatetime;
            if($endDate =='')
                $enddatetime = $startdatetime;
        }
        //echo "startdatetime :".$startdatetime." enddatetime :".$enddatetime;

        //The data you want to send via POST
        $fields = [
            'accessToken'       => $token,
            'extCommunityUuid'  => $account_id,
            'currPage'          =>1,
            'pageSize'          =>50,
            'startDateTime'     =>$startdatetime,
            'endDateTime'       =>$enddatetime,
            'devName'           =>$doorName,
            'empName'           =>$empName,
            'eventType'         =>$eventType
        ];

        //print_r($fields);
        $fields_string = http_build_query($fields);

        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

        
        $results = curl_exec($ch);
        $json = json_decode($results,true);
        $err = curl_error($ch);
        curl_close($ch);
        //print_r($json );
        $record_total_count = 0;
        $records =array();
        if(isset($json['data'])){
            $record_total_count = $json['data']['totalCount'];
            $records = $json['data']['list'];
        }
      
        /*foreach($lists as $k => $list){
            print_r($list);
            echo "<hr />";
        }*/
        $device = new \App\Models\v7\Device();
        $dev_lists = $device->device_lists_api($token,$account_id); 
        $devices = array();
        foreach($dev_lists as $dev_list){
            $devices[$dev_list['name']] =$dev_list['name'];
        }
       
        //$devices = Device::where('account_id',$account_id)->pluck('device_name', 'id')->all();
        return view('admin.digital.dooropen', compact('records','option','doorName','eventType','date','devSn','startDate','endDate','startTime','endTime','devices','empName','record_total_count'));
    }

    public function remotedooropen(Request $request)
    {
        $account_id = Auth::user()->account_id;
        $devices = [];
        $devicesMain = RemoteDoorOpen::where('account_id',$account_id)->orderBy('devName')->pluck('devName')->unique()->all();
        foreach ($devicesMain as $d) $devices[$d] = $d;
        
        $units = [];
        $allUnitIds = RemoteDoorOpen::where('account_id',$account_id)->pluck('unit_no')->unique()->all();
        $searchUnits = Unit::whereIn('id',$allUnitIds)->get()->map(fn($e) => ['id' => $e->id,'name' => Crypt::decryptString($e->unit)]);
        $buildings = Building::where("status",1)->where('account_id',$account_id)->orderby('building','asc')->get();

        if($request->has('search')){
            
            $userIDs = [];
            
            if($request->filled('name')) $userIDs = User::where('account_id',$account_id)->get()
                ->filter(fn($e) => str_contains(strtolower(trim(Crypt::decryptString($e->name))),strtolower(trim($request->name))))
                ->pluck('id')->toArray();
            
            $startDate = trim($request->startDate);
            $endDate = trim($request->endDate);
            $startTime = trim($request->startTime);
            $endTime = trim($request->endTime);
            $startdatetime=$enddatetime=null;
    
            if($startDate !='') $startdatetime = $startDate.($startTime !='' ? " ".$startTime.":00" : " 00:00:00");
            if($endDate !='') $enddatetime = $endDate.($endTime !='' ? " ".$endTime.":00" : " 23:59:59");
            
            
            $records = RemoteDoorOpen::where('account_id',$account_id)
                ->when(($request->has('building') && trim($request->building)!=''),fn($e) => $e->wherehas('getunit',fn($q)=> $q->where('building_id',trim($request->building))))
                ->when(($request->has('unit') && trim($request->unit)!=''),fn($e) => $e->where('unit_no',trim($request->unit)))
                ->when(($request->has('doorName') && trim($request->doorName)!=''),fn($e) => $e->where('devname',trim($request->doorName)))
                ->when(!empty($userIDs),fn($e) => $e->whereIn('user_id',$userIDs))
                ->when($startdatetime,fn($e) => $e->where('created_at','>=',$startdatetime))
                ->when($enddatetime,fn($e) => $e->where('created_at','<=',$enddatetime))
                ->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS'));
        }else{
            $records = RemoteDoorOpen::where('account_id',$account_id)->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS'));
        }

        return view('admin.digital.remote', compact('records','devices','searchUnits','buildings','account_id'));
    }

    public function searchremotedooropen(Request $request)
    {
        $option = $device = $name = $doorName = $records = $unit = $startDate =  $endDate = $startTime = $endTime = $startdatetime = $enddatetime = '';
        $account_id = Auth::user()->account_id;

        $option = $request->input('option');
        $device_rec = '';
        $name = $request->input('name');
        $unit = $request->input('unit');
        $units = array();
        if($unit !=''){   
            $unit = str_replace("#",'',$unit);
            $unitObj = Unit::select('id','unit')->where('account_id',$account_id)->where(function ($query) use ($unit) {
            })->get();   
            if(isset($unitObj)){
                foreach($unitObj as $unitid){
                    if(Crypt::decryptString($unitid->unit) ===$request->input('unit'))
                        $units[] = $unitid->id;
                }
            }
        } 
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $startTime = $request->input('startTime');
        $endTime = $request->input('endTime');
        if($startDate !='' || $endDate !='' ){
            if($startDate !=''){
                if($startTime !='')
                    $startdatetime = $startDate." ".$startTime.":00";
                else
                    $startdatetime = $startDate." 00:00:00";
            }
            
            if($endDate !=''){    
                if($endTime !='')    
                    $enddatetime = $endDate." ".$endTime.":00";
                else
                    $enddatetime = $endDate." 00:00:00";
            }
            if($startDate =='')
                $startdatetime = $enddatetime;
            if($endDate =='')
                $enddatetime = $startdatetime;
        }    
                
        $records =  RemoteDoorOpen::where('remote_door_opens.account_id',$account_id)
            ->join('users', 'users.id', '=', 'remote_door_opens.user_id')
            ->when(($request->has('doorName') && trim($request->doorName)!=''),fn($e) => $e->where('devName', $request->doorName))
            ->where(function ($query) use ($doorName,$device_rec,$name,$unit,$units,$startdatetime,$enddatetime) {
                if($name !='')
                    $query->where('users.name', 'LIKE', '%'.$name .'%');
                if($unit !='')
                    $query->whereIn('remote_door_opens.unit_no', $units);
                if($startdatetime !='')
                    $query->whereBetween('remote_door_opens.call_date_time',[$startdatetime,$enddatetime]);
                
            })->paginate(env('PAGINATION_ROWS'));
        
            $visitor_app_url = url('visitors');
            return view('admin.digital.remote', compact('records','devices','doorName','name','option','unit','startDate','endDate','startTime','endTime'));

    }

    public function bluetoothdooropen(Request $request)
    {
        $account_id = Auth::user()->account_id;
        $devices = [];
        $devicesMain = BluetoothDoorOpen::where('account_id',$account_id)->orderBy('devName')->pluck('devName')->unique()->all();
        foreach ($devicesMain as $d) $devices[$d] = $d;
        
        $units = [];
        $allUnitIds = BluetoothDoorOpen::where('account_id',$account_id)->pluck('unit_no')->unique()->all();
        $searchUnits = Unit::whereIn('id',$allUnitIds)->get()->map(fn($e) => ['id' => $e->id,'name' => Crypt::decryptString($e->unit)]);
        $buildings = Building::where("status",1)->where('account_id',$account_id)->orderby('building','asc')->get();

        if($request->has('search')){
            
            $userIDs = [];
            
            if($request->filled('name')) $userIDs = User::where('account_id',$account_id)->get()
                    ->filter(fn($e) => str_contains(strtolower(trim(Crypt::decryptString($e->name))),strtolower(trim($request->name))))
                    ->pluck('id')->toArray();
            
            $startDate = trim($request->startDate);
            $endDate = trim($request->endDate);
            $startTime = trim($request->startTime);
            $endTime = trim($request->endTime);
            $startdatetime=$enddatetime=null;
    
            if($startDate !='') $startdatetime = $startDate.($startTime !='' ? " ".$startTime.":00" : " 00:00:00");
            if($endDate !='') $enddatetime = $endDate.($endTime !='' ? " ".$endTime.":00" : " 23:59:59");
            
            
            $records = BluetoothDoorOpen::where('account_id',$account_id)
                ->when(($request->has('building') && trim($request->building)!=''),fn($e) => $e->wherehas('getunit',fn($q)=> $q->where('building_id',trim($request->building))))
                ->when(($request->has('unit') && trim($request->unit)!=''),fn($e) => $e->where('unit_no',trim($request->unit)))
                ->when(($request->has('doorName') && trim($request->doorName)!=''),fn($e) => $e->where('devname',trim($request->doorName)))
                ->when(!empty($userIDs),fn($e) => $e->whereIn('user_id',$userIDs))
                ->when($startdatetime,fn($e) => $e->where('created_at','>=',$startdatetime))
                ->when($enddatetime,fn($e) => $e->where('created_at','<=',$enddatetime))
                ->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS'));
        }else{
            $records = BluetoothDoorOpen::where('account_id',$account_id)->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS'));
        }

        return view('admin.digital.bluetooth', compact('records','devices','searchUnits','buildings','account_id'));
    }

    public function searchbluetoothdooropen(Request $request)
    {
        $option = $device = $name = $doorName = $records = $unit = $startDate =  $endDate = $startTime = $endTime = $startdatetime = $enddatetime = '';
        $account_id = Auth::user()->account_id;

        $option = $request->input('option');
        $device_rec = '';
        $name = $request->input('name');
        $unit = $request->input('unit');
        $units = array();
        if($unit !=''){   
            $unit = str_replace("#",'',$unit);
            $unitObj = Unit::select('id','unit')->where('account_id',$account_id)->where(function ($query) use ($unit) {
            })->get();   
            if(isset($unitObj)){
                foreach($unitObj as $unitid){
                    if(Crypt::decryptString($unitid->unit) ===$request->input('unit'))
                        $units[] = $unitid->id;
                }
            }
        } 
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $startTime = $request->input('startTime');
        $endTime = $request->input('endTime');
        if($startDate !='' || $endDate !='' ){
            if($startDate !=''){
                if($startTime !='')
                    $startdatetime = $startDate." ".$startTime.":00";
                else
                    $startdatetime = $startDate." 00:00:00";
            }
            
            if($endDate !=''){    
                if($endTime !='')    
                    $enddatetime = $endDate." ".$endTime.":00";
                else
                    $enddatetime = $endDate." 00:00:00";
            }
            if($startDate =='')
                $startdatetime = $enddatetime;
            if($endDate =='')
                $enddatetime = $startdatetime;
        }    
                
        $records =  BluetoothDoorOpen::where('bluetooth_door_opens.account_id',$account_id)
            ->join('users', 'users.id', '=', 'bluetooth_door_opens.user_id')
            ->when(($request->has('doorName') && trim($request->doorName)!=''),fn($e) => $e->where('devName', $request->doorName))
            ->where(function ($query) use ($doorName,$device_rec,$name,$unit,$units,$startdatetime,$enddatetime) {
                if($name !='')
                    $query->where('users.name', 'LIKE', '%'.$name .'%');
                if($unit !='')
                    $query->whereIn('bluetooth_door_opens.unit_no', $units);
                if($startdatetime !='')
                    $query->whereBetween('bluetooth_door_opens.call_date_time',[$startdatetime,$enddatetime]);
                
            })->paginate(env('PAGINATION_ROWS'));
        
            $visitor_app_url = url('visitors');
            return view('admin.digital.bluetooth', compact('records','devices','doorName','name','option','unit','startDate','endDate','startTime','endTime'));

    }

    public function dooropenfailed(Request $request)
    {
        $option = $device = $name = $doorName = $records = $unit = $startDate =  $endDate = $startTime = $endTime ='';
      
        $account_id = Auth::user()->account_id;
       
        $devices = [];
        $devicesMain = FailDoorOpenRecord::where('account_id',$account_id)->orderBy('devname')->pluck('devname')->unique()->all();
        foreach ($devicesMain as $d) $devices[$d] = $d;
        
        $units = [];
        $allUnitIds = FailDoorOpenRecord::where('account_id',$account_id)->pluck('unit_no')->unique()->all();
        $searchUnits = Unit::whereIn('id',$allUnitIds)->get()->map(fn($e) => ['id' => $e->id,'name' => Crypt::decryptString($e->unit)]);
        
        if($request->has('search')){
            
            $userIDs = [];
            
            if($request->filled('name')) $userIDs = User::where('account_id',$account_id)->get()
                ->filter(fn($e) => str_contains(strtolower(trim(Crypt::decryptString($e->name))),strtolower(trim($request->name))))
                ->pluck('id')->toArray();
            
            $startDate = trim($request->startDate);
            $endDate = trim($request->endDate);
            $startTime = trim($request->startTime);
            $endTime = trim($request->endTime);
            $startdatetime=$enddatetime=null;
    
            if($startDate !='') $startdatetime = $startDate.($startTime !='' ? " ".$startTime.":00" : " 00:00:00");
            if($endDate !='') $enddatetime = $endDate.($endTime !='' ? " ".$endTime.":00" : " 23:59:59");
            
            
            $records = FailDoorOpenRecord::where('account_id',$account_id)
                ->when(($request->has('unit') && trim($request->unit)!=''),fn($e) => $e->where('unit_no',trim($request->unit)))
                ->when(($request->has('doorName') && trim($request->doorName)!=''),fn($e) => $e->where('devname',$request->doorName))
                ->when(!empty($userIDs),fn($e) => $e->whereIn('user_id',$userIDs))
                ->when($startdatetime,fn($e) => $e->where('created_at','>=',$startdatetime))
                ->when($enddatetime,fn($e) => $e->where('created_at','<=',$enddatetime))
                ->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS'));
        }else{
            $records = FailDoorOpenRecord::where('account_id',$account_id)->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS'));
        }

        return view('admin.digital.dooropenfailed', compact('records','devices','searchUnits'));
    }

    public function searchdooropenfailed(Request $request)
    {
        $option = $device = $name = $doorName = $records = $unit = $startDate =  $endDate = $startTime = $endTime = $enddatetime = $startdatetime ='';
      
        $account_id = Auth::user()->account_id;
       
        $allUnitIds = NormalDoorOpenRecord::where('account_id',$account_id)->pluck('unit_no')->unique()->all();
        $searchUnits = Unit::whereIn('id',$allUnitIds)->get()->map(fn($e) => ['id' => $e->id,'name' => Crypt::decryptString($e->unit)]);

        $relationships =  FacialRecoOption::where('status',1)->pluck('option', 'id')->all();

        $auth = new \App\Models\v7\Property();
        $token = $auth->thinmoo_auth_api(); 

        $device = new \App\Models\v7\Device();
        $records = $device->device_lists_api($token,$account_id); 
        $devices = array();
        foreach($records as $record){
            $devices[$record['name']] =$record['name'];
        }

        $option = $request->input('option');

        $doorName = $request->input('doorName');
        $device_rec = '';
        if( $doorName !='')
            $device_rec = Device::where('device_name',$doorName)->first();
        
        $name = $request->input('name');
        $unit = $request->input('unit');
        $units = array();
        if($unit !=''){   
            $unit = str_replace("#",'',$unit);
            $unitObj = Unit::select('id','unit')->where('account_id',$account_id)->where(function ($query) use ($unit) {
            })->get();   
            if(isset($unitObj)){
                foreach($unitObj as $unitid){
                    if(Crypt::decryptString($unitid->unit) ===$request->input('unit'))
                        $units[] = $unitid->id;
                }
            }
        } 
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $startTime = $request->input('startTime');
        $endTime = $request->input('endTime');
        if($startDate !='' || $endDate !='' ){
            if($startDate !=''){
                if($startTime !='')
                    $startdatetime = $startDate." ".$startTime.":00";
                else
                    $startdatetime = $startDate." 00:00:00";
            }
            
            if($endDate !=''){    
                if($endTime !='')    
                    $enddatetime = $endDate." ".$endTime.":00";
                else
                    $enddatetime = $endDate." 00:00:00";
            }
            if($startDate =='')
                $startdatetime = $enddatetime;
            if($endDate =='')
                $enddatetime = $startdatetime;
        }    
                
        $records =  FailDoorOpenRecord::where('fail_door_open_records.account_id',$account_id)
            ->where(function ($query) use ($doorName,$device_rec,$name,$unit,$units,$startdatetime,$enddatetime) {
                if($doorName !='')
                    $query->where('fail_door_open_records.devSn', $device_rec->device_serial_no);
                if($name !='')
                    $query
                    ->join('users', 'users.id', '=', 'fail_door_open_records.empuuid')->where('fail_door_open_records.empname', 'LIKE', '%'.$name .'%');
                if($unit !='')
                    $query->whereIn('fail_door_open_records.unit_no', $units);
                if($startdatetime !='')
                    $query->whereBetween('fail_door_open_records.eventtime',[$startdatetime,$enddatetime]);
                
            })->paginate(env('PAGINATION_ROWS'));

            //$records =  FailDoorOpenRecord::where('account_id',$account_id)->where('devSn', $device_rec->device_serial_no)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));

       /* if ($option != '') {
            if($option == 'doorName') {
                $doorName = $request->input('doorName');
                $device_rec = Device::where('device_name',$doorName)->first();
               
                $records =  FailDoorOpenRecord::where('account_id',$account_id)->where('devSn', $device_rec->device_serial_no)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
            }

            if($option == 'name') {
                $name = $request->input('name');
                $records =  FailDoorOpenRecord::where('account_id',$account_id)->where('empname', 'LIKE', '%'.$name .'%')
                    ->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
            }
            

            if($option == 'unit' ) { 
                $unit = $request->input('unit');
                
                $unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->get();
                $units = array();
                if(isset($unitObj)){
                    foreach($unitObj as $unitid){
                        $units[] = $unitid->id;
                    }
                }
                
                $records =  FailDoorOpenRecord::where('fail_door_open_records.account_id',$account_id)
                    ->join('users', 'users.id', '=', 'fail_door_open_records.empuuid')->whereIn('users.unit_no', $units)
                    ->orderby('fail_door_open_records.id','desc')->paginate(env('PAGINATION_ROWS'));
               
                
            }

            if($option == 'date' ) { 
                $startDate = $request->input('startDate');
                $endDate = $request->input('endDate');
                $startTime = $request->input('startTime');
                $endTime = $request->input('endTime');
                if($startTime !='')
                    $startdatetime = $startDate." ".$startTime.":00";
                else
                    $startdatetime = $startDate." 00:00:00";
                
                if($endTime !='')    
                    $enddatetime = $endDate." ".$endTime.":00";
                else
                    $enddatetime = $endDate." 00:00:00";

                $records =  FailDoorOpenRecord::where('account_id',$account_id)->whereBetween('eventtime',[$startdatetime,$enddatetime])->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
                
            }
        }
        else{
            $records =  FailDoorOpenRecord::where('account_id',$account_id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
        } */
  

        return view('admin.digital.dooropenfailed', compact('records','devices','doorName','name','option','unit','startDate','endDate','startTime','endTime','searchUnits'));
    }

    public function callunit()
    {
        $option = $device = $name = $doorName = $records = $unit = $startDate =  $endDate = $startTime = $endTime ='';
      
        $account_id = Auth::user()->account_id;

        $relationships =  FacialRecoOption::where('status',1)->pluck('option', 'id')->all();

        $auth = new \App\Models\v7\Property();
        $token = $auth->thinmoo_auth_api(); 

        $device = new \App\Models\v7\Device();
        $records = $device->device_lists_api($token,$account_id); 
        $devices = [];
        $devicesMain = CallPushRecord::where('account_id',$account_id)->orderBy('devSn')->pluck('devSn')->unique()->all();
        foreach ($devicesMain as $d) $devices[$d] = $d;
        
        $units = [];
        $allUnitIds = FailDoorOpenRecord::where('account_id',$account_id)->pluck('unit_no')->unique()->all();
        $searchUnits = Unit::whereIn('id',$allUnitIds)->get()->map(fn($e) => ['id' => $e->id,'name' => Crypt::decryptString($e->unit)]);

        $records =  CallPushRecord::where('account_id',$account_id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));

        $unitObj = Unit::where('account_id',$account_id)->get();
        $unitlists =array();
        $buildinglists =array();
        if(isset($unitObj)){
			foreach($unitObj as $unitname){
                $roomcode = Crypt::decryptString($unitname->code);
                $unitcode = "0".$roomcode;
                $rawcode = str_replace("00","",$unitcode);

				$unitlists[$roomcode] =  Crypt::decryptString($unitname->unit);
                $unitlists[$unitcode] =  Crypt::decryptString($unitname->unit);
                $unitlists[$rawcode] =  Crypt::decryptString($unitname->unit);

                $buildinglists[$roomcode] =  (isset($unitname->buildinginfo->building))?$unitname->buildinginfo->building:'';
                $buildinglists[$unitcode] =  (isset($unitname->buildinginfo->building))?$unitname->buildinginfo->building:'';
                $buildinglists[$rawcode] =  (isset($unitname->buildinginfo->building))?$unitname->buildinginfo->building:'';


			}
		}
       
        return view('admin.digital.callunit', compact('records','devices','doorName','name','option','unit','startDate','endDate','startTime','endTime','unitlists','buildinglists','searchUnits'));
    }

    public function searchcallunit(Request $request)
    {
        $option = $device = $name = $doorName = $records = $unit = $startDate =  $endDate = $startTime = $endTime = $startdatetime = $enddatetime = '';
      
        $account_id = Auth::user()->account_id;

        $relationships =  FacialRecoOption::where('status',1)->pluck('option', 'id')->all();

        $auth = new \App\Models\v7\Property();
        $token = $auth->thinmoo_auth_api(); 

        $device = new \App\Models\v7\Device();
        $records = $device->device_lists_api($token,$account_id); 
        $devices = array();
        foreach($records as $record){
            $devices[$record['name']] =$record['name'];
        }

        $option = $request->input('option');

        $doorName = $request->input('doorName');
        $device_rec = '';
        if( $doorName !='')
            $device_rec = Device::where('device_name',$doorName)->first();
        
        $unit = $request->input('unit');
        $unit_code = '';
        /*if($unit !=''){
            $unitObj = Unit::where('account_id',$account_id)->where('unit',$unit)->first();
            $unit_code = isset($unitObj->code)?$unitObj->code:'';
                
            $unit_code = (int)$unit_code;
        } 
        */
        $units = array();
        if($unit !=''){   
            $unit = str_replace("#",'',$unit);
            $unitObj = Unit::select('id','unit','code')->where('account_id',$account_id)->where(function ($query) use ($unit) {
            })->get();   
            if(isset($unitObj)){
                foreach($unitObj as $unitid){
                    if(Crypt::decryptString($unitid->unit) ===$request->input('unit')){
                        $units[] = $unitid->id;
                        $unit_code = isset($unitObj->code)?Crypt::decryptString($unitObj->code):'';
                        $unit_code = (int)$unit_code;
                    }
                }
            }
        } 
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $startTime = $request->input('startTime');
        $endTime = $request->input('endTime');
        if($startDate !='' || $endDate !='' ){
            if($startDate !=''){
                if($startTime !='')
                    $startdatetime = $startDate." ".$startTime.":00";
                else
                    $startdatetime = $startDate." 00:00:00";
            }
            
            if($endDate !=''){    
                if($endTime !='')    
                    $enddatetime = $endDate." ".$endTime.":00";
                else
                    $enddatetime = $endDate." 00:00:00";
            }
            if($startDate =='')
                $startdatetime = $enddatetime;
            if($endDate =='')
                $enddatetime = $startdatetime;
        }    
                
        $records =  CallPushRecord::where('account_id',$account_id)
            ->where(function ($query) use ($doorName,$device_rec,$name,$unit,$unit_code,$startdatetime,$enddatetime) {
                if($doorName !='')
                    $query->where('devSn', $device_rec->device_serial_no);
                if($unit !='')
                    $query->where('roomCode', 'LIKE', '%'.$unit_code);
                if($startdatetime !='')
                    $query->whereBetween('created_at',[$startdatetime,$enddatetime]);
                
            })->paginate(env('PAGINATION_ROWS'));

        
        $unitObj = Unit::where('account_id',$account_id)->get();
        $unitlists =array();
        $buildinglists =array();
        if(isset($unitObj)){
			foreach($unitObj as $unitname){
                $roomcode = Crypt::decryptString($unitname->code);
                $unitcode = "0".$roomcode;
                $rawcode = str_replace("00","",$unitcode);

				$unitlists[$roomcode] =  Crypt::decryptString($unitname->unit);
                $unitlists[$unitcode] =  Crypt::decryptString($unitname->unit);
                $unitlists[$rawcode] =  Crypt::decryptString($unitname->unit);

                $buildinglists[$roomcode] =  $unitname->buildinginfo->building;
                $buildinglists[$unitcode] =  $unitname->buildinginfo->building;
                $buildinglists[$rawcode] =  $unitname->buildinginfo->building;


			}
		}

         return view('admin.digital.callunit', compact('records','devices','doorName','name','option','unit','startDate','endDate','startTime','endTime','unitlists','buildinglists'));
    }

    public function qropenrecords()
    {
        $option = $device = $name = $doorName = $records = $unit = $startDate =  $endDate = $startTime = $endTime = $building = $bookingid = '';
      
        $account_id = Auth::user()->account_id;

        $relationships =  FacialRecoOption::where('status',1)->pluck('option', 'id')->all();

        $auth = new \App\Models\v7\Property();
        $token = $auth->thinmoo_auth_api(); 

        $device = new \App\Models\v7\Device();
        $records = $device->device_lists_api($token,$account_id); 
        $devices = array();
        foreach($records as $record){
            $devices[$record['name']] =$record['name'];
        }

        $buildings = Building::where("status",1)->where('account_id',$account_id)->orderby('building','asc')->pluck("building","id")->all();

        $records =  QrcodeOpenRecord::where('account_id',$account_id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));

        $units = array();

         return view('admin.digital.qrcode', compact('records','devices','doorName','name','option','unit','startDate','endDate','startTime','endTime','buildings','building','bookingid','units'));
    }

    public function searchqropenrecords(Request $request)
    {
        $option = $device = $name = $doorName = $records = $unit = $startDate =  $endDate = $startTime = $endTime = $startdatetime = $enddatetime ='';
      
        $account_id = Auth::user()->account_id;

        $relationships =  FacialRecoOption::where('status',1)->pluck('option', 'id')->all();

        $auth = new \App\Models\v7\Property();
        $token = $auth->thinmoo_auth_api(); 

        $device = new \App\Models\v7\Device();
        $records = $device->device_lists_api($token,$account_id); 
        $devices = array();
        foreach($records as $record){
            $devices[$record['name']] =$record['name'];
        }

        $option = $request->input('option');
        $doorName = $request->input('doorName');
        $building = $request->input('building');
        $bookingid = $request->input('bookingid');
        $device_rec = '';
        if( $doorName !='')
            $device_rec = Device::where('device_name',$doorName)->first();
        
        $unit = $request->input('unit');
        $units = array();
        if($building !='' && $unit ==''){   
            $unit = str_replace("#",'',$unit);
            $unitObj = Unit::select('id','unit')->where('status',1)->where('building_id',$building)->get();   
            if(isset($unitObj)){
                foreach($unitObj as $unitid){
                    $units[] = $unitid->id;
                }
            }
        }
        /*if($unit !=''){   
            $units = array();
            $unitObj = Unit::select('id','unit')->where('id',$unit)->first();   
            if(isset($unitObj)){
                $units[] = Crypt::decryptString($unitObj->unit);
            }
        }
        $bookingids = array();
        if($bookingid !=''){
            $visitors= VisitorBooking::where('ticket','LIKE', '%' . $bookingid . '%') ->get();
            if(isset($visitors)){
                foreach($visitors as $visitor){
                    $bookingids[] = $visitor->id;
                }
            }
        }
        */

        $visitors= VisitorBooking::where('account_id',$account_id)->where(function ($query) use ($building,$unit,$bookingid,$units) {
            if($building !='' && $unit =='')
                $query->whereIn('unit_no',$units);
            if($unit !='')
                $query->where('unit_no',$unit);
            if($bookingid !='')
                $query->where('ticket', 'LIKE', '%' . $bookingid . '%');
        })->get();
        if(isset($visitors)){
            foreach($visitors as $visitor){
                $bookingids[] = $visitor->id;
            }
        }

        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $startTime = $request->input('startTime');
        $endTime = $request->input('endTime');
        if($startDate !='' || $endDate !='' ){
            if($startDate !=''){
                if($startTime !='')
                    $startdatetime = $startDate." ".$startTime.":00";
                else
                    $startdatetime = $startDate." 00:00:00";
            }           
            if($endDate !=''){    
                if($endTime !='')    
                    $enddatetime = $endDate." ".$endTime.":00";
                else
                    $enddatetime = $endDate." 00:00:00";
            }
            if($startDate =='')
                $startdatetime = $enddatetime;
            if($endDate =='')
                $enddatetime = $startdatetime;
        }    
                
        $records =  QrcodeOpenRecord::where('account_id',$account_id)
            ->where(function ($query) use ($doorName,$device_rec,$name,$unit,$units,$startdatetime,$enddatetime,$bookingid,$building,$bookingids) {
                if($doorName !='')
                    $query->where('devSn', $device_rec->device_serial_no);
                if(count($bookingids) >0)
                    $query->whereIn('booking_id', $bookingids);
                if($startdatetime !='')
                    $query->whereBetween('created_at',[$startdatetime,$enddatetime]);                
            })->paginate(env('PAGINATION_ROWS'));


        $buildings = Building::where("status",1)->where('account_id',$account_id)->orderby('building','asc')->pluck("building","id")->all();
        
        $units = array();
        if($building !=''){
             $units = Unit::select("id","unit")->where([['status','=',1],['unit','!=',''],['building_id',$building]])->get()->map(function($e){
                $e->unit = Crypt::decryptString($e->unit);
                return $e;
            })->pluck("building","id")->all();
        }
         return view('admin.digital.qrcode', compact('records','devices','doorName','name','option','unit','startDate','endDate','startTime','endTime','buildings','building','bookingid','units'));
    }


    public function facerecognition()
    {
        $option = $relationship = $name = $last_name = $faceids = $unit ='';
      
        $account_id = Auth::user()->account_id;
        $faceids = UserFacialId::where('account_id',$account_id)->where('status',2)->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS'));
        $file_path = image_storage_domain();
        $option = $request->input('option');
        if ($option != '') {
            if($option == 'doorName') {
                $doorName = $request->input('doorName');
                $device_rec = Device::where('device_name',$doorName)->first();
               
                $records =  FailDoorOpenRecord::where('account_id',$account_id)->where('devSn', $device_rec->device_serial_no)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
            }

           

            if($option == 'unit' ) { 
                $unit = $request->input('unit');
                
                $unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->first();
                $unit_id = isset($unitObj->id)?$unitObj->id:'';
                
                $records =  FailDoorOpenRecord::where('fail_door_open_records.account_id',$account_id)
                    ->join('users', 'users.id', '=', 'fail_door_open_records.empuuid')->where('users.unit_no', $unit_id)
                    ->orderby('fail_door_open_records.id','desc')->paginate(env('PAGINATION_ROWS'));
               
                
            }

            if($option == 'date' ) { 
                $startDate = $request->input('startDate');
                $endDate = $request->input('endDate');
                $startTime = $request->input('startTime');
                $endTime = $request->input('endTime');
                if($startTime !='')
                    $startdatetime = $startDate." ".$startTime.":00";
                else
                    $startdatetime = $startDate." 00:00:00";
                
                if($endTime !='')    
                    $enddatetime = $endDate." ".$endTime.":00";
                else
                    $enddatetime = $endDate." 00:00:00";

                $records =  FailDoorOpenRecord::where('account_id',$account_id)->whereBetween('eventtime',[$startdatetime,$enddatetime])->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
                
            }
        }
        else{
            $records =  FailDoorOpenRecord::where('account_id',$account_id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
        }
        $relationships =  FacialRecoOption::where('status',1)->pluck('option', 'id')->all();

         return view('admin.digital.facerecognition', compact('faceids','relationships','relationship','name','option','unit','file_path'));
    }

    public function accessfaceid(Request $request){

        $input = $request->all();
        $code = $input['code']; //access code
        $img_url = $input['img_url'];
        if(isset($img_url) && $img_url !=''){
            $userId =  Auth::user()->id;
            $MoreInfoObj = UserMoreInfo::where('user_id',$userId)->whereNotIn('status',[2])->first();
            if(isset($MoreInfoObj)){
                if($code == $MoreInfoObj->faceid_access_code){
                    $result['status'] = 1;
                    $result['img'] = $faceidObj->face_picture;
                    $result['64img'] = $faceidObj->face_picture_base64;
                }
                else{
                    $result['status'] = 3;
                }
            }
            else{
                $result['status'] = 2;
            }
        }
        else{
            $result['status'] = 0;
        }
        return json_encode($result);
    }



}
