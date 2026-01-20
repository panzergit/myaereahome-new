<?php

namespace App\Http\Controllers;

use App\Models\v7\VisitorType;
use App\Models\v7\Property;
use App\Models\v7\VisitorTypeSubcategory;
use Illuminate\Validation\Rule;

use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use DB;
use Auth;

class VisitorTypeController extends Controller
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
            $lists = VisitorType::paginate(env('PAGINATION_ROWS')); 
        else
            $lists = VisitorType::where('account_id',$account_id)->paginate(env('PAGINATION_ROWS'));  

        $propertyObj = Property::where('id',$account_id)->first();
       
        return view('admin.purpose.index', compact('lists','q','propertyObj'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $properties = Property::pluck('company_name', 'id')->all();
        return view('admin.purpose.create', compact('properties'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       // print_r($request->visitortype);
        $input = $request->all();

       if(isset($input['account_id']))
            $input['account_id'] = $input['account_id'];
        else
            $input['account_id'] = Auth::user()->account_id;

       $validator = Validator::make($request->all(), [ 
            'visiting_purpose' =>[
                'required', 
                Rule::unique('visitor_types')
                       ->where('account_id', $input['account_id'])
            ],
            
        ]);
        if ($validator->fails()) { 

             return redirect('opslogin/configuration/purpose/create')->with('status', 'Visiting Purpose already exist!');         
        }

        $typeObj = VisitorType::create($input);


        $details=array();
        for($i=1;$i<=15;$i++){

            $list = 'sub_category_list_'.$i;
            if(!empty($request->input($list))){
                $data['type_id'] = $typeObj->id;
                $data['sub_category'] = $request->input($list);
                $data['account_id'] = $input['account_id'];
                $data['created_at'] = $typeObj->updated_at;
                $data['updated_at'] = $typeObj->updated_at;
                $details[] = $data;
            }
            
        }
        $record = VisitorTypeSubcategory::insert($details);

        return redirect('opslogin/configuration/purpose')->with('status', 'Visiting Purpose has been added!');
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
       
        $Obj = VisitorType::find($id);
        $properties = Property::pluck('company_name', 'id')->all();

        $subcategories=array();
        if(isset($Obj->subcategory)){
            foreach($Obj->subcategory as $k => $category){
                
                $sub_array = array();
                $sub_array['id'] = $category->id;
                $sub_array['sub_category'] = $category->sub_category;
                $subcategories[$k+1] = $sub_array;
            
            }
        }

        return view('admin.purpose.edit', compact('Obj','properties','subcategories'));
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

        $unitObj = VisitorType::find($id);

        if(isset($input['account_id']))
            $input['account_id'] = $input['account_id'];
        else
            $input['account_id'] = Auth::user()->account_id;

        $validator = Validator::make($request->all(), [ 
            'visiting_purpose' =>[
                'required', 
                Rule::unique('visitor_types')
                    ->where('account_id', $input['account_id'])
                    ->whereNotIn('id',[$id])
            ],
            
        ]);

        if ($validator->fails()) { 
             return redirect("opslogin/configuration/purpose/$id/edit")->with('status', 'Visiting Purpose already exist!');         
        }

        

        $unitObj->visiting_purpose = $request->input('visiting_purpose');
        $unitObj->id_required = $request->input('id_required');
        $unitObj->limit_set = $request->input('limit_set');
        $unitObj->end_date_required = $request->input('end_date_required');
        $unitObj->qr_scan_limit = $request->input('qr_scan_limit');
        $unitObj->compinfo_required = $request->input('compinfo_required');
        $unitObj->cat_dropdown = $request->input('cat_dropdown');
        $unitObj->sub_category = $request->input('sub_category');
        $unitObj->save();

       

        $type_id = $unitObj->id;
        VisitorTypeSubcategory::where('type_id', $type_id)->delete();
        $details=array();
        for($i=1;$i<=15;$i++){

            $list = 'sub_category_list_'.$i;
            if(!empty($request->input($list))){
                $data['type_id'] = $type_id;
                $data['sub_category'] = $request->input($list);
                $data['account_id'] = $input['account_id'];
                $data['created_at'] = $unitObj->updated_at;
                $data['updated_at'] = $unitObj->updated_at;
                $details[] = $data;
            }
            
        }
    
        $record = VisitorTypeSubcategory::insert($details);


        return redirect('opslogin/configuration/purpose')->with('status', 'Purpose has been updated!');
    }

    public function updatesettings(Request $request){
        $account_id = Auth::user()->account_id;
        
        $probObj = Property::find($account_id);
       
        $probObj->visitor_limit = $request->input('visitor_limit');
        if($request->input('visitor_limit') ==1)
            $probObj->visitors_allowed = $request->input('visitors_allowed');
        else
            $probObj->visitors_allowed = '';

        $probObj->save();

        return redirect('opslogin/configuration/purpose')->with('status', 'Settings has been updated!');

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
        VisitorTypeSubcategory::where('type_id', $id)->delete();

        VisitorType::findOrFail($id)->delete();
        return redirect('opslogin/configuration/purpose')->with('status', 'Visiting Purpose deleted successfully!');
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
       
        $file = public_path('import/'.Auth::user()->account_id.'/units.csv');

        $unitArr = $this->csvToArray($file);
      

        for ($i = 0; $i < count($unitArr); $i ++)
        {
            $unitArr[$i]['visitortype'] = str_replace("#",'',$unitArr[$i]['visitortype']);
            $unitArr[$i]['account_id'] = Auth::user()->account_id;
            $userinfo = VisitorType::create($unitArr[$i]);
        }

        return redirect('opslogin/configuration/visitortype')->with('status', 'Records has been imported!');
    }

}
