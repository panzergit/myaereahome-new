<?php

namespace App\Http\Controllers;


use App\Models\v7\HolidaySetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use File;
use Auth;
use Session;


class HolidaySettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->role_id ==1)
        {
           
            $HolidayObj = HolidaySetting::paginate(50);   
            return view('admin.holiday.index', compact('HolidayObj'));
        }else{
            $account_id = Auth::user()->account_id;
            $HolidayObj = HolidaySetting::where('account_id',$account_id)->first();
            if(empty($HolidayObj) && $HolidayObj->id==''){
                $holiday['account_id'] = $account_id;
                HolidaySetting::create($holiday);
                $HolidayObj = HolidaySetting::where('account_id',$account_id)->first();
            }
        
            return view('admin.holiday.edit', compact('HolidayObj'));

            //$file_path = env('APP_URL')."/storage/app/";
           // $properities = Property::where('id',$account_id)->paginate(50);   
            //return view('admin.holoday.index', compact('properities','file_path'));
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        return view('admin.holoday.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
       
        return redirect('opslogin/configuration/holoday/')->with('status', 'Property has been added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\v7\holoday  $holoday
     * @return \Illuminate\Http\Response
     */
    public function show(holoday $holoday)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\v7\holoday  $holoday
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $HolidayObj = Property::find($id);
        $file_path = env('APP_URL')."/storage/app/";

        return view('admin.holoday.edit', compact('HolidayObj','file_path'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\v7\holoday  $holoday
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $holidayObj = HolidaySetting::find($id);
        if ($request->input('public_holidays') != null){
            $holidayObj->public_holidays = $request->input('public_holidays');
        } 
        
        $holidayObj->save();

       
        return redirect('opslogin/configuration/holiday_setting/')->with('status', 'Holiday list has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\v7\holoday  $holoday
     * @return \Illuminate\Http\Response
     */
    public function destroy(holoday $holoday)
    {
        //
    }

    public function access($id)
    {
        $HolidayObj = Property::find($id);
        $file_path = env('APP_URL')."/storage/app/";
        $modules = Module::where('status',1)->orderBy('name','ASC')->get();
        $role_access = array();
        foreach($HolidayObj->Permissions as $permission){
            $role_access[$permission->module_id] = array($permission->view,$permission->create,$permission->edit,$permission->delete);
           
        }

        return view('admin.holoday.access', compact('HolidayObj','file_path','modules','role_access'));
    }

    public function accessupdate(Request $request, $id)
    {
        $input = $request->all();

        $HolidayObj = Property::find($id);

        PropertyPermission::where('holoday_id',$id)->delete();

        $modules = Module::where('status',1)->orderBy('name','ASC')->get();

        foreach($modules as $module) {
            $input['holoday_id'] = $id;
            $input['module_id'] = $module->id;
            $view_field = "mod_view_".$module->id;
            if(isset($input[$view_field]))
                {
                    $input['view'] = 1;
                    $input['create'] = 1;
                    $input['edit'] = 1;
                    $input['delete'] = 1;
                }
            else
               { 
                $input['view'] = 0;
                $input['create'] = 0;
                $input['edit'] = 0;
                $input['delete'] = 0;
                }

            

            PropertyPermission::create($input);  
        }
    
        return redirect('opslogin/configuration/holoday')->with('status', 'Property modules has been updated!');;

        
    }


    public function deleteCompanyLogo(Request $request){

        $id = $request->input('id');
        $file_path = $request->input('file_path');

        $configObj = holoday::find($id);
        $configObj->logo = '';
        $configObj->save();
        
        $file_path = $file_path;  // Value is not URL but directory file path
            if(File::exists($file_path)) {
                File::delete($file_path);
            }
        
        
         $success['message'] = "success";

          $request->session()->flash('message', " ClaimRequest has been removed.");
          $request->session()->flash('message-type', 'success');

         return response()->json(['success'=>$success], 200); 
    }

    public function activate($id)
    {
        $result = Property::where( 'id' , $id)->update( array( 'status' => 1));
        return redirect('opslogin/configuration/holoday')->with('status', 'Property account activated!');;

    }

    public function deactivate($id)
    {
        $result = Property::where( 'id' , $id)->update( array( 'status' => 0));
        return redirect('opslogin/configuration/holoday')->with('status', 'Property account de-activated!');;

    }

}
