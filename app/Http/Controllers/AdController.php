<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rule;

use App\Models\v7\Ad;
use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use Auth;
use DB;

class AdController extends Controller
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
        $ads = Ad::paginate(50);   
        return view('admin.ad.index', compact('ads','file_path'));
    }

    public function create()
    {   
        //$properities = Property::paginate(50);   
        return view('admin.ad.create');

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
            'ad_title' =>[
                'required', 
                Rule::unique('ads')
            ],            
        ]); 
       
        if ($validator->fails()) { 
             return redirect('opslogin/configuration/ads/create')->with('status', 'Ad already exist!');         
        }

        if ($request->hasFile('ad_image')) $input['ad_image'] = $request->file('ad_image')->store(upload_path('ad'));

        $result = Ad::create($input); 
        
        $last_display_order = Ad::orderby('display_order','desc')->first();

        $new_display_order = isset($last_display_order->display_order)?($last_display_order->display_order+1):1;

        Ad::where( 'id' , $result->id)->update( array( 'display_order' => $new_display_order));

        return redirect('opslogin/configuration/ads')->with('status', 'Ad added'); 
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
        $adObj = Ad::find($id);
        $file_path = env('APP_URL')."/storage/app/";

        return view('admin.ad.edit', compact('adObj','file_path'));
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

        
        $AdObj = Ad::find($id);

        $input = $request->all();

        $validator = Validator::make($request->all(), [ 
            'ad_title' =>[
                'required', 
                Rule::unique('ads')
                       ->whereNotIn('id',[$id])
            ],
            
        ]);
        if ($validator->fails()) { 
             return redirect("opslogin/configuration/ads/$id/edit")->with('status', 'Ad already exist!');         
        } 

        if ($request->hasFile('ad_image')) $AdObj->ad_image = $request->file('ad_image')->store(upload_path('ad'));

        $AdObj->ad_title = $request->input('ad_title');
        $AdObj->email = $request->input('email');
        $AdObj->phone = $request->input('phone');
        $AdObj->website = $request->input('website');
        $AdObj->description = $request->input('description');

        $AdObj->save();

        return redirect('opslogin/configuration/ads')->with('status', 'Ad has been updated!'); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\v7\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       // $FileObj = Ad::find($id);
       Ad::findOrFail($id)->delete();
        return redirect('opslogin/configuration/ads')->with('status', 'Ad deleted successfully!');
    }

    public function activate($id)
    {
        $result = Ad::where( 'id' , $id)->update( array( 'status' => 1));
        return redirect('opslogin/configuration/ads#settings')->with('status', 'Ad activated!');;

    }

    public function deactivate($id)
    {
        $result = Ad::where( 'id' , $id)->update( array( 'status' => 0));
        return redirect('opslogin/configuration/ads#settings')->with('status', 'Ad de-activated!');;

    }
}
