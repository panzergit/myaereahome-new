<?php

namespace App\Http\Controllers;

use App\Models\v7\Building;
use App\Models\v7\Unit;
use App\Models\v7\Property;
use Session;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use DB;
use Auth;

class BuildingController extends Controller
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
            $buildings = Building::paginate(env('PAGINATION_ROWS')); 
        else
            $buildings = Building::where('account_id',$account_id)->orderBy('id','desc')->paginate(env('PAGINATION_ROWS'));  

        //$buildings = Building::paginate(150);   
        return view('admin.building.index', compact('buildings','q'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $properties = Property::pluck('company_name', 'id')->all();
        return view('admin.building.create', compact('properties'));
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       // print_r($request->building);
        $input = $request->all();

       if(isset($input['account_id']))
            $input['account_id'] = $input['account_id'];
        else
            $input['account_id'] = Auth::user()->account_id;

        /*$validator = Validator::make($request->all(), [ 
            'building' =>[
                'required', 
                Rule::unique('buildings')
                       ->where('account_id', $input['account_id'])
            ],
            
        ]);
        if ($validator->fails()) { 

             return redirect('opslogin/configuration/building/create')->with('status', 'Block already exist!');         
        } */

        for($i=1;$i<=5;$i++){
            $building_name ='building_'. $i;

            if(isset($input[$building_name]) && $input[$building_name] !='') {
                $data['building'] =  $input[$building_name];
                $data['account_id'] = $input['account_id'];

                $building = Building::create($data);
                $building_no = $input['account_id'].$building->id;
                Building::where('id', $building->id)
                    ->update(['building_no' => $building_no]);

                $buildingObj = Building::find($building->id);

                
                if($building->id >0){


                    $auth = new \App\Models\v7\Property();
                    $thinmoo_access_token = $auth->thinmoo_auth_api();

                    
                    $api_obj = new \App\Models\v7\Building();
                    $building_result = $api_obj->building_check_record($thinmoo_access_token,$buildingObj);
                    
                    if($building_result['code'] ==0){
                        $building_info = $api_obj->building_update_api($thinmoo_access_token,$buildingObj);
                    }
                    else{
                        $building_info= $api_obj->building_add_api($thinmoo_access_token,$buildingObj);
                    }
                }
            }
        }

            /********** INSERT Unit ******************
                $units = array();
                for ($i = 1; $i <= 10; $i++) {
                    $unit = 'unit_' . $i;
                    $size = 'size_' . $i;
                    $share_amount ='share_amount_'. $i;

                    if ($input[$unit] != null) { 
                        $data['account_id'] =  $input['account_id'];
                        $data['building_id'] = $building->id ;
                        $data['unit'] = $input[$unit];
                        $data['size'] = $input[$size];
                        $data['share_amount'] = $input[$share_amount];
                        $data['created_at'] = $building->created_at;
                        $data['updated_at'] = $building->updated_at;
                        $unit =  Unit::create($data);

                        $buildingObj = Building::find($unit->building_id);
                        $code = $buildingObj->building_no.$unit->id;
                        Unit::where('id', $unit->id)
                            ->update(['code' => $code]);

                        $unitObj = Unit::find($unit->id);
                        if($unit->id >0){
                            $api_obj = new \App\Models\v7\Unit();
                            $unit_result = $api_obj->unit_check_record($thinmoo_access_token,$unitObj);
                            
                            if($unit_result['code'] ==0){
                                $unit_info = $api_obj->unit_update_api($thinmoo_access_token,$unitObj);
                            }
                            else{
                                $unit_info= $api_obj->unit_add_api($thinmoo_access_token,$unitObj);
                            }
                        }
                    }
                }

            */
        

        
        
        return redirect('opslogin/configuration/building#settings')->with('status', 'Block has been added!');
    }

    public function uploadcsv()
    {
        $properties = Property::pluck('company_name', 'id')->all();
        return view('admin.building.uploadcsv', compact('properties'));
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
            $csv_file_path = $request->file('csv_file')->storeAs("buildings/$property",$filename);
        }
        $file_path = "app/".$csv_file_path;
        $filename =  base_path() .\Storage::url($file_path);
        $buildingArr = $this->csvToArray($filename);
        $json_data = array();
        if(isset($buildingArr)){
            foreach($buildingArr as $k => $building){
                $data = array();
                $data['building'] =  $building[0];
                $data['account_id'] = $property;
                $validation = Building::where('building', $building[0])->where('account_id',$property)->first();
                if(empty($validation)){
                    $building = Building::create($data);
                    $building_no = $property.$building->id;
                    Building::where('id', $building->id)->update(['building_no' => $building_no]);
                    $buildingObj = Building::find($building->id);
                    if($building->id >0){
                        $json_data[] = array('name'=>$building->building,'uuid'=>$building->id);
                    }
                }
            }
           
            if(count($json_data)>0){
                $auth = new \App\Models\v7\Property();
                $thinmoo_access_token = $auth->thinmoo_auth_api();
                $api_obj = new \App\Models\v7\Building();
                $building_info= $api_obj->building_bulkadd_api($thinmoo_access_token,$json_data,$property);
            } 
        }
        return redirect('opslogin/configuration/building#settings')->with('status', 'Block(s) has been imported!');


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
    

    public function bulkupload(Request $request)
    {

        $auth = new \App\Models\v7\Property();
        $thinmoo_access_token = $auth->thinmoo_auth_api();
        $api_obj = new \App\Models\v7\Building();

        $building_info= $api_obj->building_bulkadd_api($thinmoo_access_token);
        exit;
        
        return redirect('opslogin/configuration/building#settings')->with('status', 'Block has been added!');
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

        $buildingObj = Building::find($id);
        $properties = Property::pluck('company_name', 'id')->all();

        return view('admin.building.edit', compact('buildingObj','properties'));
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

        $buildingObj = Building::find($id);

        if(isset($input['account_id']))
            $input['account_id'] = $input['account_id'];
        else
            $input['account_id'] = Auth::user()->account_id;

        $validator = Validator::make($request->all(), [ 
            'building_no' =>[
                'required', 
                Rule::unique('buildings')
                    ->where('account_id', $input['account_id'])
                    ->whereNotIn('id',[$id])
            ],
            
        ]);

        if ($validator->fails()) { 
             return redirect("opslogin/configuration/building/$id/edit")->with('status', 'Block already exist!');         
        }

        $buildingObj->building = $request->input('building');
        $buildingObj->building_no = $request->input('building_no');
        $buildingObj->save();

        if($buildingObj->id >0){

            $auth = new \App\Models\v7\Property();
            $thinmoo_access_token = $auth->thinmoo_auth_api();
            
            $api_obj = new \App\Models\v7\Building();
            $building_result = $api_obj->building_check_record($thinmoo_access_token,$buildingObj);

           
            
            if($building_result['code'] ==0){
                $building_info = $api_obj->building_update_api($thinmoo_access_token,$buildingObj);
            }
            else{
                $building_info= $api_obj->building_add_api($thinmoo_access_token,$buildingObj);
            }
        }

        

        return redirect('opslogin/configuration/building#settings')->with('status', 'Block has been updated!');
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

        $BuildingObj = Building::find($id);

        if($BuildingObj->id >0){
            $auth = new \App\Models\v7\Property();
            if(Session::has('thinmoo_acess_tocken')){
                $thinmoo_access_token = Session::get('thinmoo_acess_tocken');
            }
            else{
                $thinmoo_access_token = $auth->thinmoo_auth_api();  
            }
            
            $api_obj = new \App\Models\v7\Building();
            $building_result = $api_obj->building_check_record($thinmoo_access_token,$BuildingObj);
            
            if($building_result['code'] ==0){
                $building_info = $api_obj->building_delete_api($thinmoo_access_token,$BuildingObj);
            }
           
        }
        Building::findOrFail($id)->delete();
      
        return redirect('opslogin/configuration/building#settings')->with('status', 'Block deleted successfully!');
    }

     public function search(Request $request){
        $q = $request->input('q');
        if($q != "" ){

        $buildings = Building::where('building', 'LIKE', '%'.$q .'%')->paginate(50);
         
        return view('admin.building.index', compact('buildings','q'));
        }
       
        else{
         return redirect('opslogin/project');
        }
   }


   public function getbuildinglist(Request $request)
    {
        
        $buildings = array();

        $term = $request->term;
        
        $buildings = DB::table("buildings")->where("status",1)->where('building','like', "%" . $term . "%")->orderby('building','asc')->pluck("building","id");

         return json_encode($buildings);

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

    public function getbuildings(Request $request)
    {
        
        $buildings = array();

        $property = $request->property;
        
        $buildings = DB::table("buildings")->where("status",1)->where('account_id',$property)->orderby('building','asc')->pluck("building","id");

         return json_encode($buildings);
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
       
        $file = public_path('import/'.Auth::user()->account_id.'/buildings.csv');

        $buildingArr = $this->csvToArray($file);
      

        for ($i = 0; $i < count($buildingArr); $i ++)
        {
            $buildingArr[$i]['building'] = str_replace("#",'',$buildingArr[$i]['building']);
            $buildingArr[$i]['account_id'] = Auth::user()->account_id;
            $userinfo = Building::create($buildingArr[$i]);
        }

        return redirect('opslogin/configuration/building#settings')->with('status', 'Records has been imported!');
    }

}
