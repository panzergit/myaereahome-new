<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rule;

use App\Models\v7\Econcierge;
use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use Auth;
use DB;

class EconciergeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $q ='';
        $file_path = env('APP_URL')."/storage/app/";
        $econcierges = Econcierge::paginate(50);   
        return view('admin.econcierge.index', compact('econcierges','file_path'));
    }

    public function create()
    {   
        //$properities = Property::paginate(50);   
        return view('admin.econcierge.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {      
       $input = $request->all();
        $validator = Validator::make($request->all(), [ 
            'banner_title' =>[
                'required', 
                Rule::unique('econcierges')
            ],            
        ]); 
       
        if ($validator->fails()) { 
             return redirect('opslogin/configuration/econcierge/create')->with('status', 'E-Concierge already exist!');         
        }
        if ($request->file('banner_image') != null) {
            $input['banner_image'] = $request->file('banner_image')->store(upload_path('econcierge'));
        }

        $result = Econcierge::create($input); 
        
        $last_display_order = Econcierge::orderby('display_order','desc')->first();

        $new_display_order = isset($last_display_order->display_order)?($last_display_order->display_order+1):1;

        Econcierge::where( 'id' , $result->id)->update( array( 'display_order' => $new_display_order));

        return redirect('opslogin/configuration/econcierge/')->with('status', 'E-Concierge added'); 
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
        $econciergeObj = Econcierge::find($id);
        $file_path = env('APP_URL')."/storage/app/";

        return view('admin.econcierge.edit', compact('econciergeObj','file_path'));
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

        
        $BannerObj = Econcierge::find($id);

        $input = $request->all();

        $validator = Validator::make($request->all(), [ 
            'banner_title' =>[
                'required', 
                Rule::unique('econcierges')
                       ->whereNotIn('id',[$id])
            ],
            
        ]);
        if ($validator->fails()) { 
             return redirect("opslogin/configuration/econcierge/$id/edit")->with('status', 'E-Concierge already exist!');         
        } 

        if ($request->file('banner_image') != null) {
            $BannerObj->banner_image = $request->file('banner_image')->store(upload_path('econcierge'));
        }
        $BannerObj->banner_title = $request->input('banner_title');
        $BannerObj->description = $request->input('description');

        $BannerObj->save();

        return redirect('opslogin/configuration/econcierge')->with('status', 'E-Concierge has been updated!'); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\v7\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       // $FileObj = Econcierge::find($id);
       Econcierge::findOrFail($id)->delete();
        return redirect('opslogin/configuration/econcierge')->with('status', 'E-Concierge deleted successfully!');
    }

    public function activate($id)
    {
        $result = Econcierge::where( 'id' , $id)->update( array( 'status' => 1));
        return redirect('opslogin/configuration/econcierge#settings')->with('status', 'E-Concierge activated!');;

    }

    public function deactivate($id)
    {
        $result = Econcierge::where( 'id' , $id)->update( array( 'status' => 0));
        return redirect('opslogin/configuration/econcierge#settings')->with('status', 'E-Concierge de-activated!');;

    }
}
