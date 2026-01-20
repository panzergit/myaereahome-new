<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rule;

use App\Models\v7\DefectLocation;
use App\Models\v7\DefectType;
use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use Auth;
use DB;


class DefectLocationController extends Controller
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
        $types = DefectLocation::where('account_id',$account_id)->paginate(150);   
        return view('admin.defecttype.index', compact('types','q'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::pluck('name','id')->all();
        return view('admin.defecttype.create', compact('users'));
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

       $input['account_id'] = Auth::user()->account_id;
        $validator = Validator::make($request->all(), [ 
            'defect_location' =>[
                'required', 
                Rule::unique('defect_locations')
                       ->where('account_id', $input['account_id'])
            ],
            
        ]);
        if ($validator->fails()) { 

             return redirect('opslogin/configuration/defect/create#defectsettings')->with('status', 'Defect location already exist!');         
        }
        

        $location =  DefectLocation::create($input);

         /********** INSERT Defect Type******************/
         for ($i = 1; $i <= 10; $i++) {
           
            $type_name = 'defect_type_' . $i;
            if ($input[$type_name] != null) {
                
                $type['location_id'] = $location->id;
                $type['account_id'] = Auth::user()->account_id;
                $type['defect_type'] = $input[$type_name];
                $type['created_at'] = $location->created_at;
                $type['updated_at'] = $location->updated_at;
                $defect_types[] = $type;
            }
        }

        if (isset($defect_types)) {
            DefectType::insert($defect_types);
        }

        
        
        return redirect('opslogin/configuration/defect#defectsettings')->with('status', 'Location has been updated!');
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
        $defect_types =array();
        $defectObj = DefectLocation::find($id);
        //print_r($defectObj->types);
        //exit;
        if (isset($defectObj->types)) {
            foreach ($defectObj->types as $k => $defect) {
                $d_types['key'] = $k + 1;
                $d_types['id'] = $defect['id'];
                $d_types['defect_type'] = $defect['defect_type'];
                $defect_types[$k + 1] = $d_types;
            }
        }
        //exit;

        return view('admin.defecttype.edit', compact('defectObj','defect_types'));
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

        $locationObj = DefectLocation::find($id);

        $input = $request->all();

        if($request->input('account_id') !='')
            $locationObj->account_id = $request->input('account_id');
        else
            $locationObj->account_id= Auth::user()->account_id;


        $validator = Validator::make($request->all(), [ 
            'defect_location' =>[
                'required', 
                Rule::unique('defect_locations')
                       ->where('account_id', $locationObj->account_id)
                       ->whereNotIn('id',[$id])
            ],
            
        ]);
        if ($validator->fails()) { 
             return redirect("opslogin/configuration/defect/$id/edit#defectsettings")->with('status', 'Defect type already exist!');         
        }


        $locationObj->defect_location = $request->input('defect_location');
        
        $defectObj =  $locationObj->save();

        print_r($locationObj);
        //exit;

         /********** INSERT documents START******************/
         for ($i = 1; $i <= 10; $i++) {
            $type_title = 'defect_type_' . $i;
            $type_id = 'type_id_' . $i;

            if ($request->input($type_id) != null) {

                if($request->input($type_title) ==''){
                    DefectType::findOrFail($request->input($type_id))->delete();
                }else{
                    $typeObj = DefectType::find($request->input($type_id));
                    $typeObj->location_id = $id;
                    $typeObj->defect_type = $request->input($type_title);
                    $typeObj->created_at = $locationObj->created_at;
                    $typeObj->updated_at = $locationObj->updated_at;
                    $typeObj->save();
                }

            } else if ($request->input($type_title) != null) {
                $type['location_id'] = $id;
                $type['account_id'] = Auth::user()->account_id;
                $type['defect_type'] = $request->input($type_title);
                $type['created_at'] = $locationObj->created_at;
                $type['updated_at'] = $locationObj->updated_at;
                $allowance = DefectType::create($type);
                
            }
        }


        return redirect('opslogin/configuration/defect#defectsettings')->with('status', 'Defect type has been updated!');
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

        DefectType::where('location_id', $id)->delete();

        DefectLocation::findOrFail($id)->delete();
        return redirect('opslogin/configuration/defect#defectsettings')->with('status', 'Defect type deleted successfully!');
    }

   
}
