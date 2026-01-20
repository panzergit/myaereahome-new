<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rule;
use App\Models\v7\Property;
use App\Models\v7\HomeBannerProperty;
use App\Models\v7\HomeBanner;
use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use Auth;
use DB;

class HomeBannerController extends Controller
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
        $banners = HomeBanner::orderby('id','desc')->paginate(1000);   
        return view('admin.banner.index', compact('banners','file_path'));
    }

    public function create()
    {   
        //$properities = Property::paginate(50); 
        $assigned_property = array();
        
        $agent_properties = Property::where('status',1)->get();
        return view('admin.banner.create', compact('agent_properties','assigned_property'));
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
                Rule::unique('home_banners')
            ],            
        ]); 
       
        if ($validator->fails()) { 
             return redirect('opslogin/configuration/banner/create')->with('status', 'Banner already exist!');         
        }
        if ($request->file('banner_image') != null) {
            $input['banner_image'] = $request->file('banner_image')->store('banner');
        }

        $result = HomeBanner::create($input); 

        //for assign property to user agent
        if(@Auth::user()->role_id ==1){
            $properties = Property::where('status',1)->get();
            foreach($properties as $property) {
                $property_input = array();
                $property_checked = "property_".$property->id;
                if(isset($input[$property_checked]))
                    {                   
                        $property_input['banner_id'] = $result->id;
                        $property_input['property_id'] = $property->id;
                        HomeBannerProperty::create($property_input);  
                    }               
            }
        }

        
        $last_display_order = HomeBanner::orderby('display_order','desc')->first();

        $new_display_order = isset($last_display_order->display_order)?($last_display_order->display_order+1):1;

        HomeBanner::where( 'id' , $result->id)->update( array( 'display_order' => $new_display_order));

        return redirect('opslogin/configuration/banner/')->with('status', 'Banner added'); 
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
        $bannerObj = HomeBanner::find($id);
        $file_path = env('APP_URL')."/storage/app/";
        
        $agent_properties = Property::where('status',1)->get();

        $assigned_property = array();
        foreach($bannerObj->bannerproperties as $bannerproperty){
            $assigned_property[] = $bannerproperty->property_id;

        }
        //print_r($bannerObj->bannerproperties);

        return view('admin.banner.edit', compact('bannerObj','file_path','agent_properties','assigned_property'));
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

        
        $BannerObj = HomeBanner::find($id);

        $input = $request->all();

        $validator = Validator::make($request->all(), [ 
            'banner_title' =>[
                'required', 
                Rule::unique('home_banners')
                       ->whereNotIn('id',[$id])
            ],
            
        ]);
        if ($validator->fails()) { 
             return redirect("opslogin/configuration/banner/$id/edit")->with('status', 'Banner already exist!');         
        } 

        if ($request->file('banner_image') != null) {
            $BannerObj->banner_image = $request->file('banner_image')->store('banner');
        }
        $BannerObj->banner_title = $request->input('banner_title');
        $BannerObj->banner_url_type = $request->input('banner_url_type');
        if($request->input('banner_url_type') ==1){
            $BannerObj->banner_url = $request->input('banner_url');
            $BannerObj->module ='';
            $BannerObj->ref_id ='';
        }
        else if($request->input('banner_url_type') ==2){
            $BannerObj->module = $request->input('module');
            $BannerObj->ref_id = $request->input('ref_id');
            $BannerObj->banner_url ='';
        }
        else{
            $BannerObj->banner_url ='';
            $BannerObj->module ='';
            $BannerObj->ref_id ='';
        }
        $BannerObj->save();

         //for assign property to user agent
         if(@Auth::user()->role_id ==1){
            $properties = Property::where('status',1)->get();
            HomeBannerProperty::where('banner_id',$BannerObj->id)->delete();
            foreach($properties as $property) {
                $property_input = array();
                $property_checked = "property_".$property->id;
                if(isset($input[$property_checked]))
                    {                   
                        $property_input['banner_id'] = $BannerObj->id;
                        $property_input['property_id'] = $property->id;
                        HomeBannerProperty::create($property_input);  
                    }               
            }
        }

        return redirect('opslogin/configuration/banner')->with('status', 'Banner has been updated!'); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\v7\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       // $FileObj = HomeBanner::find($id);
       HomeBanner::findOrFail($id)->delete();
        return redirect('opslogin/configuration/banner')->with('status', 'Banner deleted successfully!');
    }

    public function activate($id)
    {
        $result = HomeBanner::where( 'id' , $id)->update( array( 'status' => 1));
        return redirect('opslogin/configuration/banner#settings')->with('status', 'Banner activated!');;

    }

    public function deactivate($id)
    {
        $result = HomeBanner::where( 'id' , $id)->update( array( 'status' => 0));
        return redirect('opslogin/configuration/banner#settings')->with('status', 'Banner de-activated!');;

    }
}
