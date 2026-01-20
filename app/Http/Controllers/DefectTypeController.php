<?php

namespace App\Http\Controllers;

use App\Models\v7\DefectType;
use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use Auth;
use DB;

class DefectTypeController extends Controller
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
        $types = DefectType::where('account_id',$account_id)->paginate(150);   
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
        $validator = Validator::make($request->all(), [ 
            'defect_type' => 'required|unique:defect_types' 
        ]);
        if ($validator->fails()) { 

             return redirect('opslogin/configuration/defect/create#defectsettings')->with('status', 'Defect type already exist!');         
        }
        $input = $request->all();

        $input['account_id'] = Auth::user()->account_id;

        DefectType::create($input);
        
        return redirect('opslogin/configuration/defect#defectsettings');
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

        $defectObj = DefectType::find($id);
        return view('admin.defecttype.edit', compact('defectObj'));
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

        $unitObj = DefectType::find($id);

        $validator = Validator::make($request->all(), [ 

            'defect_type' => 'required|unique:defect_types,defect_type,'.$id

        ]);
        if ($validator->fails()) { 
             return redirect("opslogin/configuration/defect/$id/edit#defectsettings")->with('status', 'Defect type already exist!');         
        }


        $unitObj->defect_type = $request->input('defect_type');
        
        
        $unitObj->save();
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

        DefectType::findOrFail($id)->delete();
        return redirect('opslogin/configuration/defect#defectsettings')->with('status', 'Defect type deleted successfully!');
    }

   
}
