<?php

namespace App\Http\Controllers;

use App\Models\v7\Setting;
use App\Models\v7\Property;


use File;
use App\Models\v7\ModuleSetting;
use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use Auth;
use DB;
use App\Models\v7\PropertyPermission;
class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $configObj = Setting::find(1);      
        $img_full_path = env('APP_URL')."/storage/app/";
        return view('admin.setting.index', compact('configObj','img_full_path'));
        
    }

    public function landing()
    {
        $account_id = Auth::user()->account_id;
        $PropertyObj = Property::find($account_id);
        $img_full_path = env('APP_URL')."/storage/app/";
        return view('admin.setting.landing', compact('PropertyObj','img_full_path'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\v7\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\v7\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function edit(Setting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\v7\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $configObj = Setting::find($id);

        $configObj->company_name = $request->input('company_name');
        $configObj->company_reg_no = $request->input('company_reg_no');
        $configObj->company_contact = $request->input('company_contact');
        $configObj->company_email = $request->input('company_email');
        $configObj->company_address = $request->input('company_address');

        $configObj->prefix_code = $request->input('prefix_code');
        $configObj->no_of_digits = $request->input('no_of_digits');
        $configObj->payroll_notes = $request->input('payroll_notes');

        if ($request->file('logo') != null){
            $configObj->logo = $request->file('logo')->store(upload_path('setting'));
        }
        $configObj->save();
        return redirect('opslogin/configuration/setting')->with('status', 'Settings has been updated!');;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\v7\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Setting $setting)
    {
        //
    }

    public function deleteCompanyLogo(Request $request){

        $id = $request->input('id');
        $file_path = $request->input('file_path');

        $configObj = Setting::find($id);
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

}
