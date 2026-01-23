<?php

namespace App\Http\Controllers;

use App\Models\v7\FinanceShareSetting;
use App\Models\v7\Property;
use Session;


use Illuminate\Validation\Rule;

use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use DB;
use Auth;

class FinanceShareSettingController extends Controller
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
            $shares = FinanceShareSetting::paginate(env('PAGINATION_ROWS')); 
        else
            $shares = FinanceShareSetting::where('account_id',$account_id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));  

        $currentURL = url()->full();
        $page = explode("=",$currentURL);
        if(isset($page[1]) && $page[1]>0){
                session()->put('page', $page[1]);
        }else{
                session()->forget('page');
        }

        //$shares = FinanceShareSetting::paginate(150);   
        return view('admin.share.index', compact('shares','q'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $account_id = Auth::user()->account_id;

        $property_info = Property::where('id',$account_id)->first();

        //print_r($property_info);

        $properties = Property::pluck('company_name', 'id')->all();

        return view('admin.share.create', compact('properties','property_info'));
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

        $unit =  FinanceShareSetting::where('account_id', $input['account_id'])->where('status',1)->first(); 

        if(isset($unit)){
        FinanceShareSetting::where('account_id', $input['account_id'])->where('id',$unit->id)
        ->update(['status' => 0]);
        }

        if ($request->file('qrcode_file') != null) {
            $input['qrcode_file'] = $request->file('qrcode_file')->store(upload_path('finance'));
        }
        
        FinanceShareSetting::create($input);
        
        return redirect('opslogin/configuration/sharesettings#settings')->with('status', 'Settings has been added!');
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

        $unitObj = FinanceShareSetting::find($id);
        $properties = Property::pluck('company_name', 'id')->all();
        $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();

        return view('admin.share.edit', compact('unitObj','properties','buildings'));
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

        $unitObj = FinanceShareSetting::find($id);

        if(isset($input['account_id']))
            $input['account_id'] = $input['account_id'];
        else
            $input['account_id'] = Auth::user()->account_id;

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
             return redirect("opslogin/configuration/unit/$id/edit")->with('status', 'FinanceShareSetting already exist!');         
        }

        
        if($request->input('code') ==''){
            $buildingObj = Building::find($unitObj->building_id);
            //$code = $buildingObj->building_no.$unitObj->id;
            $char = array("_","-","_");
            $code = str_replace($char,"",$request->input('unit'));
            $unitObj->code = $code;
        }

        $unitObj->building_id = $request->input('building_id');
        $unitObj->unit = $request->input('unit');
        //$unitObj->code = $request->input('code');
        $unitObj->size = $request->input('size');
        $unitObj->share_amount = $request->input('share_amount');
        $unitObj->save();

        if($unitObj->id >0){

            $auth = new \App\Models\v7\Property();
            $thinmoo_access_token = $auth->thinmoo_auth_api();  
           
            $api_obj = new \App\Models\v7\FinanceShareSetting();
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
            return redirect("opslogin/configuration/unit?page=$page#settings")->with('status', 'FinanceShareSetting has been updated!');}
        else
            return redirect('opslogin/configuration/unit#settings')->with('status', 'FinanceShareSetting has been updated!');

        
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

        $FinanceShareSettingObj = FinanceShareSetting::find($id);

        if($FinanceShareSettingObj->id >0){
            $auth = new \App\Models\v7\Property();
            $thinmoo_access_token = $auth->thinmoo_auth_api();  
           
            
            $api_obj = new \App\Models\v7\FinanceShareSetting();
            $unit_result = $api_obj->unit_check_record($thinmoo_access_token,$FinanceShareSettingObj);
            
            if($unit_result['code'] ==0){
                $unit_info = $api_obj->unit_delete_api($thinmoo_access_token,$FinanceShareSettingObj);
            }
           
        }

        FinanceShareSetting::findOrFail($id)->delete();
        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/configuration/unit?page=$page#settings")->with('status', 'FinanceShareSetting deleted successfully!');}
        else
            return redirect('opslogin/configuration/unit#settings')->with('status', 'FinanceShareSetting deleted successfully!');
    }

     public function search(Request $request){
        $q = $request->input('q');
        if($q != "" ){

        $shares = FinanceShareSetting::where('unit', 'LIKE', '%'.$q .'%')->paginate(50);
         
        return view('admin.share.index', compact('shares','q'));
        }
       
        else{
         return redirect('opslogin/project');
        }
   }


   public function getunitlist(Request $request)
    {
        
        $shares = array();

        $term = $request->term;
        
        $shares = DB::table("unites")->where("status",1)->where('unit','like', "%" . $term . "%")->orderby('unit','asc')->pluck("unit","id");

         return json_encode($shares);

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
       
        $file = public_path('import/'.Auth::user()->account_id.'/shares.csv');

        $unitArr = $this->csvToArray($file);
      

        for ($i = 0; $i < count($unitArr); $i ++)
        {
            $unitArr[$i]['unit'] = str_replace("#",'',$unitArr[$i]['unit']);
            $unitArr[$i]['account_id'] = Auth::user()->account_id;
            $userinfo = FinanceShareSetting::create($unitArr[$i]);
        }

        return redirect('opslogin/configuration/unit#settings')->with('status', 'Records has been imported!');
    }

}
