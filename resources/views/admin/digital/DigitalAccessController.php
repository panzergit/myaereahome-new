<?php

namespace App\Http\Controllers;


use App\Role;
use App\User;
use App\Device;
use App\BluetoothDoorOpen;
use App\UserMoreInfo;
use App\UserFacialId;
use App\FacialRecoOption;
use App\Unit;
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
        $auth = new \App\Property();
        $token = $auth->thinmoo_auth_api(); 

        $device = new \App\Device();
        $devices = $device->device_count_api($token,$account_id); 
        $device_count = str_pad($devices, 5, '0', STR_PAD_LEFT);


        $room = new \App\Room();
        $rooms = $room->room_count_api($token,$account_id); 
        $room_count = str_pad($rooms, 5, '0', STR_PAD_LEFT);

        $household = new \App\User();
        $households = $household->household_count_api($token,$account_id); 
        $household_count = str_pad($households, 5, '0', STR_PAD_LEFT);

         return view('admin.digital.index', compact('device_count','room_count','household_count'));
    }

    public function dooropen()
    {
        $option = $lists = $doorName = $eventType = $date = $devSn =$startDatetime = $endDatetime = '';
      
        $account_id = Auth::user()->account_id;

        $auth = new \App\Property();
        $token = $auth->thinmoo_auth_api();  

        $url = env('THINMOO_API_URL')."normalOpenDoorlog/extapi/list";

        //The data you want to send via POST
        $fields = [
            'accessToken'       => $token,
            'extCommunityUuid'  => $account_id,
            'currPage'          =>1,
            'pageSize'          =>1000,
            'startDatetime'     =>date('Y-m-d H-i-s'),
            'endDatetime'       =>date('Y-m-d 00:00:00')
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

        $lists =array();
        if(isset($json['data'])){
            $lists = $json['data']['list'];
        }
      
        /*foreach($lists as $k => $list){
            print_r($list);
            echo "<hr />";
        }*/

        $device = new \App\Device();
        $records = $device->device_lists_api($token,$account_id); 
        $devices = array();
        foreach($records as $record){
            $devices[$record['name']] =$record['name'];
        }

       
        return view('admin.digital.dooropen', compact('lists','option','doorName','eventType','date','devSn','startDatetime','endDatetime','devices'));
    }

    public function searchdooropen(Request $request)
    {
        $option = $lists = $doorName = $eventType = $date = $devSn =$startDatetime = $endDatetime = '';
      
        $account_id = Auth::user()->account_id;

        $auth = new \App\Property();
        $token = $auth->thinmoo_auth_api();  

        $url = env('THINMOO_API_URL')."normalOpenDoorlog/extapi/list";

        $option = $request->input('option'); 

        if ($option != '') {
            if($option == 'doorName') {
                //$device = Device::where('id',$request->input('doorName'))->first();
                $doorName = $request->input('doorName');
            }
            if($option == 'eventType') {
                $eventType = $request->input('eventType');
            }
            if($option == 'date') {
                $startDatetime = $request->input('startDatetime');
                $endDatetime = $request->input('endDatetime');
            }

        }

    
        //The data you want to send via POST
        $fields = [
            'accessToken'       => $token,
            'extCommunityUuid'  => $account_id,
            'currPage'          =>1,
            'pageSize'          =>1000,
            'startDateTime'     =>$startDatetime,
            'endDateTime'       =>$endDatetime,
            'doorName'          =>$doorName,
            'eventType'         =>$eventType
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

        $lists =array();
        if(isset($json['data'])){
            $lists = $json['data']['list'];
        }
      
        /*foreach($lists as $k => $list){
            print_r($list);
            echo "<hr />";
        }*/
        $device = new \App\Device();
        $records = $device->device_lists_api($token,$account_id); 
        $devices = array();
        foreach($records as $record){
            $devices[$record['name']] =$record['name'];
        }
       
        //$devices = Device::where('account_id',$account_id)->pluck('device_name', 'id')->all();
        return view('admin.digital.dooropen', compact('lists','option','doorName','eventType','date','devSn','startDatetime','endDatetime','devices'));
    }

    public function bluetoothdooropen()
    {
        $option = $device = $name = $doorName = $records = $unit = $startDate =  $endDate = $startTime = $endTime ='';
      
        $account_id = Auth::user()->account_id;

        $devices = array();
        $devices = Device::where('account_id',$account_id)->pluck('device_name', 'id')->all();


        $records = BluetoothDoorOpen::where('account_id',$account_id)->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS'));

        return view('admin.digital.bluetooth', compact('records','devices','doorName','name','option','unit','startDate','endDate','startTime','endTime'));
    }

    public function searchbluetoothdooropen(Request $request)
    {
        $option = $device = $name = $doorName = $records = $unit = $startDate =  $endDate = $startTime = $endTime ='';
      
        $account_id = Auth::user()->account_id;

        $devices = array();
        $devices = Device::where('account_id',$account_id)->pluck('device_name', 'id')->all();
        $option = $request->input('option');
        if ($option != '') {
            if($option == 'doorName') {
                $doorName = $request->input('doorName');
                $device_rec = Device::where('id',$doorName)->first();
               
                $records =  BluetoothDoorOpen::where('account_id',$account_id)->where('devSn', $device_rec->device_serial_no)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
            }

            if($option == 'name') {
                $name = $request->input('name');
                $records =  BluetoothDoorOpen::where('bluetooth_door_opens.account_id',$account_id)->join('users', 'users.id', '=', 'bluetooth_door_opens.user_id')->where('users.name', 'LIKE', '%'.$name .'%')
                    ->orderby('bluetooth_door_opens.id','desc')->paginate(env('PAGINATION_ROWS'));
            }
            

            if($option == 'unit' ) { 
                $unit = $request->input('unit');
                $unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->first();
                $unit_id = isset($unitObj->id)?$unitObj->id:'';

                $records =  BluetoothDoorOpen::where('bluetooth_door_opens.account_id',$account_id)
                    ->join('users', 'users.id', '=', 'bluetooth_door_opens.user_id')->where('users.unit_no', $unit_id)
                    ->orderby('bluetooth_door_opens.id','desc')->paginate(env('PAGINATION_ROWS'));
                
            }

            if($option == 'date' ) { 
                $startDate = $request->input('startDatetime');
                $endDate = $request->input('endDatetime');
                $startTime = $request->input('startDatetime');
                $endTime = $request->input('endDatetime');

                $unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->first();
                $unit_id = isset($unitObj->id)?$unitObj->id:'';

                $records =  BluetoothDoorOpen::where('bluetooth_door_opens.account_id',$account_id)
                    ->join('users', 'users.id', '=', 'bluetooth_door_opens.user_id')->where('users.unit_no', $unit_id)
                    ->orderby('bluetooth_door_opens.id','desc')->paginate(env('PAGINATION_ROWS'));
                
            }
            
           
            $visitor_app_url = env('VISITOR_APP_URL');
            return view('admin.digital.bluetooth', compact('records','devices','doorName','name','option','unit','startDate','endDate','startTime','endTime'));

        } else {
            return redirect('opslogin/digitalaccess/bluetoothdooropen');
        }



        return view('admin.digital.bluetooth', compact('records','devices','doorName','name','option','unit'));
    }

    public function dooropenfailed()
    {
        $option = $relationship = $name = $last_name = $faceids = $unit ='';
      
        $account_id = Auth::user()->account_id;
        $faceids = UserFacialId::where('account_id',$account_id)->where('status',2)->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS'));
        $file_path = env('APP_URL')."/storage/app";

        $relationships =  FacialRecoOption::where('status',1)->pluck('option', 'id')->all();

         return view('admin.digital.dooropenfailed', compact('faceids','relationships','relationship','name','option','unit','file_path'));
    }

    public function callunit()
    {
        $option = $relationship = $name = $last_name = $faceids = $unit ='';
      
        $account_id = Auth::user()->account_id;
        $faceids = UserFacialId::where('account_id',$account_id)->where('status',2)->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS'));
        $file_path = env('APP_URL')."/storage/app";

        $relationships =  FacialRecoOption::where('status',1)->pluck('option', 'id')->all();

         return view('admin.digital.callunit', compact('faceids','relationships','relationship','name','option','unit','file_path'));
    }

    public function facerecognition()
    {
        $option = $relationship = $name = $last_name = $faceids = $unit ='';
      
        $account_id = Auth::user()->account_id;
        $faceids = UserFacialId::where('account_id',$account_id)->where('status',2)->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS'));
        $file_path = env('APP_URL')."/storage/app";

        $relationships =  FacialRecoOption::where('status',1)->pluck('option', 'id')->all();

         return view('admin.digital.facerecognition', compact('faceids','relationships','relationship','name','option','unit','file_path'));
    }



}
