<?php

namespace App\Http\Controllers;

use App\Models\v7\EformSetting;
use App\Models\v7\Property;
use Illuminate\Validation\Rule;
use App\Models\v7\Module;

use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use DB;
use Auth;

class EformSettingController extends Controller
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
            $eformsettings = EformSetting::get(); 
        else
            $eforms = EformSetting::where('account_id',$account_id)->paginate(env('PAGINATION_ROWS'));  

        //$eforms = EformSetting::paginate(150);   
        return view('admin.eform_setting.index', compact('eforms','q'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $properties = Property::pluck('company_name', 'id')->all();
        $eforms = Module::where('group_id',10)->pluck('name', 'id')->all();
        return view('admin.eform_setting.create', compact('properties','eforms'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       // print_r($request->eform);
        $input = $request->all();

       if(isset($input['account_id']))
            $input['account_id'] = $input['account_id'];
        else
            $input['account_id'] = Auth::user()->account_id;

        $validator = Validator::make($request->all(), [ 
            'eform_type' =>[
                'required', 
                Rule::unique('eform_settings')
                       ->where('account_id', $input['account_id'])
            ],
            
        ]);
        if ($validator->fails()) { 

             return redirect('opslogin/configuration/eform_setting/create')->with('status', 'Eform Setting already exist!');         
        }

        EformSetting::create($input);    
        return redirect('opslogin/configuration/eform_setting')->with('status', 'Eform Setting has been added!');
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

        $eformObj = EformSetting::find($id);
        $properties = Property::pluck('company_name', 'id')->all();
        $eforms = Module::where('group_id',10)->pluck('name', 'id')->all();
        return view('admin.eform_setting.edit', compact('eformObj','eforms','properties'));
    }

    public function preview($id)
    {
        //

        $eformObj = EformSetting::find($id);
        $properties = Property::pluck('company_name', 'id')->all();
        $eforms = Module::where('group_id',10)->pluck('name', 'id')->all();
        return view('admin.eform_setting.preview', compact('eformObj','eforms','properties'));
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

        $eformObj = EformSetting::find($id);

        if(isset($input['account_id']))
            $input['account_id'] = $input['account_id'];
        else
            $input['account_id'] = Auth::user()->account_id;

        $validator = Validator::make($request->all(), [ 
            'eform_type' =>[
                'required', 
                Rule::unique('eform_settings')
                    ->where('account_id', $input['account_id'])
                    ->whereNotIn('id',[$id])
            ],
            
        ]);

        if ($validator->fails()) { 
             return redirect("opslogin/configuration/eform_setting/$id/edit")->with('status', 'Eform Setting already exist!');         
        }

        

        $eformObj->eform_type = $request->input('eform_type');
        $eformObj->general_info = $request->input('general_info');    
        $eformObj->refund_amount = $request->input('refund_amount');
        //$eformObj->payable_to = $request->input('payable_to'); 
        $eformObj->payment_mode_cheque = $request->input('payment_mode_cheque');
        $eformObj->payment_mode_bank = $request->input('payment_mode_bank'); 
        $eformObj->payment_mode_cash = $request->input('payment_mode_cash');
        $eformObj->official_notes = $request->input('official_notes'); 
        
        if($request->input('eform_type') ==41){
            $eformObj->padding_lift_fee = $request->input('padding_lift_fee');
            $eformObj->hacking_work_permitted_days = $request->input('hacking_work_permitted_days');
            $eformObj->hacking_work_not_permitted_saturday = $request->input('hacking_work_not_permitted_saturday');
            $eformObj->hacking_work_not_permitted_sunday = $request->input('hacking_work_not_permitted_sunday');
            $eformObj->hacking_work_not_permitted_holiday = $request->input('hacking_work_not_permitted_holiday');
            $eformObj->hacking_work_start_time = $request->input('hacking_work_start_time');
            $eformObj->hacking_work_end_time = $request->input('hacking_work_end_time');
        } 
        else if($request->input('eform_type') ==40){
            $eformObj->padding_lift_fee = $request->input('padding_lift_fee');
            $eformObj->hacking_work_permitted_days ='';
            $eformObj->hacking_work_not_permitted_saturday ='';
            $eformObj->hacking_work_not_permitted_sunday = '';
            $eformObj->hacking_work_not_permitted_holiday ='';
            $eformObj->hacking_work_end_time = '';
    
        }else{
            $eformObj->hacking_work_permitted_days ='';
            $eformObj->hacking_work_not_permitted_saturday ='';
            $eformObj->hacking_work_not_permitted_sunday = '';
            $eformObj->hacking_work_not_permitted_holiday ='';
            $eformObj->hacking_work_end_time = '';
        }
        $eformObj->save();
        return redirect('opslogin/configuration/eform_setting')->with('status', 'Eform Setting has been updated!');
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

        EformSetting::findOrFail($id)->delete();
        return redirect('opslogin/configuration/eform_setting')->with('status', 'Eform Setting deleted successfully!');
    }

     public function search(Request $request){
        $q = $request->input('q');
        if($q != "" ){

        $eforms = EformSetting::where('eform', 'LIKE', '%'.$q .'%')->paginate(50);
         
        return view('admin.eform_setting.index', compact('eforms','q'));
        }
       
        else{
         return redirect('opslogin/project');
        }
   }


   public function geteformlist(Request $request)
    {
        
        $eforms = array();

        $term = $request->term;
        
        $eforms = DB::table("cards")->where("status",1)->where('eform','like', "%" . $term . "%")->orderby('eform','asc')->pluck("eform","id");

         return json_encode($eforms);

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
       
        $file = public_path('import/'.Auth::user()->account_id.'/eforms.csv');

        $eformArr = $this->csvToArray($file);
      

        for ($i = 0; $i < count($eformArr); $i ++)
        {
            $eformArr[$i]['eform'] = str_replace("#",'',$eformArr[$i]['eform']);
            $eformArr[$i]['account_id'] = Auth::user()->account_id;
            $userinfo = EformSetting::create($eformArr[$i]);
        }

        return redirect('opslogin/configuration/eform_setting')->with('status', 'Records has been imported!');
    }

}
