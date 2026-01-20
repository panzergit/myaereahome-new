<?php

namespace App\Http\Controllers;

use App\Models\v7\Module;
use App\Models\v7\Property;
use App\Models\v7\ModuleGroup;
use App\Models\v7\Employee;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\User;
use App\Models\v7\UserMoreInfo;
use App\Models\v7\Building;
use App\Models\v7\Unit;
use App\Models\v7\UserProperty;
use App\Models\v7\PropertyPermission;
use App\Models\v7\PropertyDashboardPermission;
use App\Models\v7\PaymentSetting;
use App\Models\v7\HolidaySetting;
use App\Models\v7\ThirdPartyService;
use App\Models\v7\PropertyReportSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use File;
use Auth;
use Session;


class PropertyController extends Controller
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
            $file_path = env('APP_URL')."/storage/app/";
            $properities = Property::paginate(100);   
            return view('admin.property.index', compact('properities','file_path'));
        }else{
            $account_id = Auth::user()->account_id;
            $PropertyObj = Property::find($account_id);
            $file_path = env('APP_URL')."/storage/app/";

            return view('admin.property.edit', compact('PropertyObj','file_path'));

            //$file_path = env('APP_URL')."/storage/app/";
           // $properities = Property::where('id',$account_id)->paginate(50);   
            //return view('admin.property.index', compact('properities','file_path'));
        }
        
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $services = ThirdPartyService::where('status',1)->pluck('name', 'id')->all();
        return view('admin.property.create', compact('services'));
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
        
        if ($request->file('company_logo') != null) {
            $input['company_logo'] = $request->file('company_logo')->store('property');
        }
        if ($request->file('default_bg') != null) {
            $input['default_bg'] = $request->file('default_bg')->store('property');
        }
        if ($request->file('announcement_bg') != null) {
            $input['announcement_bg'] = $request->file('announcement_bg')->store('property');
        }
        if ($request->file('takeover_bg') != null) {
            $input['takeover_bg'] = $request->file('takeover_bg')->store('property');
        }
        if ($request->file('defect_bg') != null) {
            $input['defect_bg'] = $request->file('defect_bg')->store('property');
        }
        if ($request->file('inspection_bg') != null) {
            $input['inspection_bg'] = $request->file('inspection_bg')->store('property');
        }
        if ($request->file('feedback_bg') != null) {
            $input['feedback_bg'] = $request->file('feedback_bg')->store('property');
        }
        if ($request->file('facilities_bg') != null) {
            $input['facilities_bg'] = $request->file('facilities_bg')->store('property');
        }

        if ($request->file('faq_bg') != null) {
            $input['faq_bg'] = $request->file('faq_bg')->store('property');
        }
        
        if ($request->file('condodocs_bg') != null) {
            $input['condodocs_bg'] = $request->file('condodocs_bg')->store('property');
        }
        if ($request->file('resident_fileupload_bg') != null) {
            $input['resident_fileupload_bg'] = $request->file('resident_fileupload_bg')->store('property');
        }

        if ($request->file('visitor_management_bg') != null) {
            $input['visitor_management_bg'] = $request->file('visitor_management_bg')->store('property');
        }
        if ($request->file('facial_reg_bg') != null) {
            $input['facial_reg_bg'] = $request->file('facial_reg_bg')->store('property');
        }

        $result = Property::create($input);

        $payment['account_id'] = $result->id;

        PaymentSetting::create($payment);

        HolidaySetting::create($payment);

        if($result->third_party_option >0)
        {
            $auth = new \App\Models\v7\Property();
            $thinmoo_access_token = $auth->thinmoo_auth_api();
            $property = $auth->property_add_api($thinmoo_access_token,$result->id,$result->company_name);
            $emp = new \App\Models\v7\Employee();
            $emp_rec['account_id'] = $result->id;
            $emp_rec['uuid'] = $result->id;
            $emp_rec['emp_type'] = 0;
            $emp_rec['name'] = $result->company_name. " Employee";
            $result =  Employee::create($emp_rec);
            $employee = $emp->primary_employee_add_api($thinmoo_access_token,$result,3);
        }
       
        return redirect('opslogin/configuration/property#settings')->with('status', 'Property has been added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\v7\property  $property
     * @return \Illuminate\Http\Response
     */
    public function show(property $property)
    {
        //
    }

    public function edit($id)
    {
        $PropertyObj = Property::find($id);
        $file_path = env('APP_URL')."/storage/app/";
        $services = ThirdPartyService::where('status',1)->pluck('name', 'id')->all();

        return view('admin.property.edit', compact('PropertyObj','file_path','services'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\v7\property  $property
     * @return \Illuminate\Http\Response
     */
    public function collectionappoinment()
    {
        $account_id = Auth::user()->account_id;
        $PropertyObj = Property::find($account_id);
        $file_path = env('APP_URL')."/storage/app/";
        return view('admin.property.collection', compact('PropertyObj','file_path'));
    }

    public function collectionappoinmentupdate(Request $request, $id)
    {
        $configObj = property::find($id);

        $configObj->takeover_timing = $request->input('takeover_timing');
        $configObj->takeover_availability_start = $request->input('takeover_availability_start');
        $configObj->takeover_blockout_days = $request->input('takeover_blockout_days');
        $configObj->takeover_notes = $request->input('takeover_notes');
        $configObj->public_holidays = $request->input('public_holidays');

        $configObj->save();

        return redirect('opslogin/configuration/collectionappoinment#settings')->with('status', 'Information has been updated!');
    }

    public function inspectionappoinment()
    {
        $account_id = Auth::user()->account_id;
        $PropertyObj = Property::find($account_id);
        $file_path = env('APP_URL')."/storage/app/";
        return view('admin.property.inspection', compact('PropertyObj','file_path'));
    }

    public function inspectionappoinmentupdate(Request $request, $id)
    {
        $configObj = property::find($id);
        $configObj->inspection_timing = $request->input('inspection_timing');
        $configObj->inspection_availability_start = $request->input('inspection_availability_start');
        $configObj->inspection_blockout_days = $request->input('inspection_blockout_days');
        $configObj->inspection_notes = $request->input('inspection_notes');
        $configObj->final_inspection_required = $request->input('final_inspection_required');
        $configObj->defect_max_limit = $request->input('defect_max_limit');
        $configObj->save();
        return redirect('opslogin/configuration/inspectionappoinment#settings')->with('status', 'Information has been updated!');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\v7\property  $property
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $configObj = property::find($id);

        if ($request->input('company_name') != null)
            $configObj->company_name = $request->input('company_name');

        if ($request->input('short_code') != null)
             $configObj->short_code = $request->input('short_code');
        
        if ($request->input('due_date') != null)
             $configObj->due_date = $request->input('due_date');

        if ($request->input('company_contact') != null)
             $configObj->company_contact = $request->input('company_contact');
        
        if ($request->input('company_email') != null)
            $configObj->company_email = $request->input('company_email');

        if ($request->input('company_address') != null)
            $configObj->company_address = $request->input('company_address');

        if ($request->input('management_company_name') != null)
            $configObj->management_company_name = $request->input('management_company_name');

        if ($request->input('management_company_addr') != null)
            $configObj->management_company_addr = $request->input('management_company_addr');
        
        if ($request->input('invoice_notes') != null)
            $configObj->invoice_notes = $request->input('invoice_notes');

        if ($request->input('mcst_code') != null)
            $configObj->mcst_code = $request->input('mcst_code');

        if ($request->input('third_party_option') != null)
            $configObj->third_party_option = $request->input('third_party_option');

        if ($request->input('enquiry_email') != null)
            $configObj->enquiry_email = $request->input('enquiry_email');

        if ($request->input('enquiry_contact') != null)
            $configObj->enquiry_contact = $request->input('enquiry_contact');

        if ($request->input('otp_option') != null || $request->input('manager_otp_option') != null){
            $configObj->otp_option = $request->input('otp_option');
            $configObj->manager_otp_option = $request->input('manager_otp_option');
            
            if ($request->input('sms_username') != null ){
                $configObj->sms_username = $request->input('sms_username');
            }
            if ($request->input('sms_password') != null){
                $configObj->sms_password = $request->input('sms_password');
            }
        }
       
        if ($request->input('qrcode_option') != null)
            $configObj->qrcode_option = $request->input('qrcode_option');

        if ($request->input('opn_secret_key') != null)
            $configObj->opn_secret_key = $request->input('opn_secret_key');

        if ($request->input('opn_password') != null){
            $configObj->opn_password = $request->input('opn_password');
        }

        if ($request->input('security_option') != null){
            $configObj->security_option = $request->input('security_option');
        }

        if ($request->file('company_logo') != null){
            $configObj->company_logo = $request->file('company_logo')->store('property');
        }
        if ($request->file('default_bg') != null) {
            $configObj->default_bg = $request->file('default_bg')->store('property');
        }
        if ($request->file('faq_bg') != null) {
            $configObj->faq_bg = $request->file('faq_bg')->store('property');
        }

        if ($request->file('announcement_bg') != null) {
            $configObj->announcement_bg = $request->file('announcement_bg')->store('property');
        }
        if ($request->file('takeover_bg') != null) {
            $configObj->takeover_bg = $request->file('takeover_bg')->store('property');
        }
        if ($request->file('defect_bg') != null) {
            $configObj->defect_bg = $request->file('defect_bg')->store('property');
        }
        if ($request->file('inspection_bg') != null) {
            $configObj->inspection_bg = $request->file('inspection_bg')->store('property');
        }
        if ($request->file('feedback_bg') != null) {
            $configObj->feedback_bg = $request->file('feedback_bg')->store('property');
        }
        if ($request->file('facilities_bg') != null) {
            $configObj->facilities_bg = $request->file('facilities_bg')->store('property');
        }

        if ($request->file('condodocs_bg') != null) {
            $configObj->condodocs_bg = $request->file('condodocs_bg')->store('property');
        }
        if ($request->file('resident_fileupload_bg') != null) {
            $configObj->resident_fileupload_bg = $request->file('resident_fileupload_bg')->store('property');
        }
        if ($request->file('visitor_management_bg') != null) {
            $configObj->visitor_management_bg = $request->file('visitor_management_bg')->store('property');
        }
        if ($request->file('facial_reg_bg') != null) {
            $configObj->facial_reg_bg = $request->file('facial_reg_bg')->store('property');
        }

        /*
        $configObj->takeover_timing = $request->input('takeover_timing');
        $configObj->takeover_availability_start = $request->input('takeover_availability_start');

        $configObj->inspection_timing = $request->input('inspection_timing');
        $configObj->inspection_availability_start = $request->input('inspection_availability_start');

        $configObj->takeover_blockout_days = $request->input('takeover_blockout_days');
        $configObj->inspection_blockout_days = $request->input('inspection_blockout_days');

        $configObj->takeover_notes = $request->input('takeover_notes');
        $configObj->inspection_notes = $request->input('inspection_notes');

        $configObj->public_holidays = $request->input('public_holidays');
        */
        if($request->input('report_available') != null){
            $configObj->report_available = $request->input('report_available');
        }
        $configObj->open_for_registration = $request->input('open_for_registration');
        $configObj->save();

        if($configObj->report_available ==1){
            $reportsObj = PropertyReportSetting::where('property_id',$configObj->id)->first();
            if($reportsObj){
                PropertyReportSetting::where('id', $reportsObj->id)
                    ->update(['emails' => $request->input('report_emails')]);
            }else{
                $report_data = array();
                $report_data['property_id'] = $configObj->id;
                $report_data['emails'] = $request->input('report_emails');
                $report_data['created_at'] = date("Y-m-d H:i:s");
                $report_data['updated_at'] = date("Y-m-d H:i:s");
                PropertyReportSetting::create($report_data);
            }
        }else{
            PropertyReportSetting::where('property_id',$configObj->id)->delete();

        }

        if($configObj->third_party_option >0)
        {
            $auth = new \App\Models\v7\Property();
            $thinmoo_access_token = $auth->thinmoo_auth_api();
            $property_result = $auth->property_check_record($thinmoo_access_token,$configObj->company_name);
            if($property_result['code'] ==0){
                $property = $auth->property_add_api($thinmoo_access_token,$configObj->id,$configObj->company_name);
            }
            else{
                $property = $auth->property_modify_api($thinmoo_access_token,$configObj->id,$configObj->company_name);
            }
            $emp = new \App\Models\v7\Employee();
            $emp_result = Employee::where('uuid',$configObj->id)->where('emp_type',0)->first();
            if(isset($emp_result))
                $emp_result = $emp->employee_check_record($thinmoo_access_token,$emp_result);
            else{
                $emp_rec['account_id'] = $configObj->id;
                $emp_rec['uuid'] = $configObj->id;
                $emp_rec['emp_type'] = 0;
                $emp_rec['name'] = $configObj->company_name. " Employee";
                $result =  Employee::create($emp_rec);
                $employee = $emp->primary_employee_add_api($thinmoo_access_token,$result,3);
                $emp_result = $emp->employee_check_record($thinmoo_access_token,$result);
            }

            
            if($emp_result['code'] !=0){
                $emp_rec['account_id'] = $configObj->id;
                $emp_rec['uuid'] = $configObj->id;
                $emp_rec['name'] = $configObj->company_name. " Employee";
                $emp = Employee::create($emp_rec);
                $emp_info= $emp->primary_employee_add_api($thinmoo_access_token,$emp,3);
            }
        }
      
      
        return redirect('opslogin/configuration/property#settings')->with('status', 'Information has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\v7\property  $property
     * @return \Illuminate\Http\Response
     */
    public function destroy(property $property)
    {
        //
    }

    public function access($id)
    {
        $PropertyObj = Property::find($id);
        $file_path = env('APP_URL')."/storage/app/";
        $groups = ModuleGroup::where('status',1)->orderBy('orderby','ASC')->get();

        //$modules = Module::where('status',1)->orderBy('name','ASC')->get();
        $role_access = array();
        foreach($PropertyObj->Permissions as $permission){
            $role_access[$permission->module_id] = array($permission->view,$permission->create,$permission->edit,$permission->delete);
           
        }

        return view('admin.property.access', compact('PropertyObj','file_path','groups','role_access'));
    }



    public function accessupdate(Request $request, $id)
    {
        $input = $request->all();

        $PropertyObj = Property::find($id);

        PropertyPermission::where('property_id',$id)->delete();

        $modules = Module::where('status',1)->orderBy('name','ASC')->get();

        foreach($modules as $module) {
            $input['property_id'] = $id;
            $input['module_id'] = $module->id;
            $view_field = "mod_view_".$module->id;
            if(isset($input[$view_field]))
                {
                    if($input[$view_field] ==1){
                    $input['view'] = 1;
                    $input['create'] = 1;
                    $input['edit'] = 1;
                    $input['delete'] = 1;
                    }
                    else if($input[$view_field] ==2){
                        $input['view'] = 2;
                        $input['create'] = 0;
                        $input['edit'] = 0;
                        $input['delete'] = 0;
                        }
                    else{
                        $input['view'] = 0;
                        $input['create'] = 0;
                        $input['edit'] = 0;
                        $input['delete'] = 0;
                    }
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
    
        return redirect('opslogin/configuration/property#settings')->with('status', 'Property modules has been updated!');;

        
    }

    public function dashboard()
    {
        $account_id = Auth::user()->account_id;
        $PropertyObj = Property::find($account_id);

        $all_modules = Module::where('status',1)->where('type',2)->orderBy('orderby','ASC')->get();
       
        $modules =array();
        $property = new \App\Models\v7\Property();

        foreach($all_modules as $module){
            $permission =  $property->check_property_permission($module->id,$account_id,1);
            if(isset($permission) &&  $permission->view==1)
                $modules[] = $module;

        }


        $role_access = array();
        if(isset($PropertyObj->dashboard_permissions)){
            foreach($PropertyObj->dashboard_permissions as $permission){
                $role_access[$permission->module_id] = array($permission->display_option,$permission->display_position);
               
            }
        }
        

        return view('admin.property.dashboard', compact('PropertyObj','role_access','modules'));
    }

    public function dashboardupdate(Request $request, $id)
    {
        $input = $request->all();

        $PropertyObj = Property::find($id);

        PropertyDashboardPermission::where('property_id',$id)->delete();

        $modules = Module::where('status',1)->where('type',2)->orderBy('orderby','ASC')->get();

        foreach($modules as $module) {
            $data['property_id'] = $id;
            $data['module_id'] = $module->id;
            $view_field = "mod_view_".$module->id;
            $display_position = "mod_position_".$module->id;
            if(isset($input[$view_field]) && $input[$view_field] ==1)
                {
                    $data['display_option'] = 1;
                    $data['display_position'] = $input[$display_position];
                    PropertyDashboardPermission::create($data);
                }            
        }
        return redirect('opslogin/configuration/dashboard#settings')->with('status', 'Mobile Application dashboard menu has been updated!');
    }


    public function deleteCompanyLogo(Request $request){

        $id = $request->input('id');
        $file_path = $request->input('file_path');

        $configObj = property::find($id);
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
        return redirect('opslogin/configuration/property#settings')->with('status', 'Property account activated!');;

    }

    public function deactivate($id)
    {
        $result = Property::where( 'id' , $id)->update( array( 'status' => 0));
        return redirect('opslogin/configuration/property#settings')->with('status', 'Property account de-activated!');;

    }

     public function delete($account_id)
    {
        $moreinfousers = UserMoreInfo::where('account_id',$account_id)->get();
        $user_roles = explode(",",env('USER_APP_ROLE'));
        if(isset($moreinfousers)){
            foreach($moreinfousers as $infouser){
                echo "<br />--:".$infouser->id ."--".$infouser->user_id. " - ".$infouser->account_id. "<br />";
                if(isset($infouser->getuser->role_id) && in_array($infouser->getuser->role_id,$user_roles)){
                    $unitcount = UserPurchaserUnit::where('user_id',$infouser->user_id)->where('property_id','!=',$account_id)->count();
                    if($unitcount==0){
                        //echo "1 Unit User info ID : ".$infouser->id. " User ID:".$infouser->user_id. "<br />";
                        UserMoreInfo::where('id', $infouser->id)->where('account_id',$account_id)->delete();
                        User::where('id', $infouser->user_id)->where('account_id',$account_id)->delete();
                    }
                    else{
                        $userinfo =  User::where('id' , $infouser->user_id)->where('account_id',$account_id)->first();
                        if(!empty($userinfo)){
                            echo "yes";
                            $currentUnitObj = UserPurchaserUnit::where('user_id',$infouser->user_id)->where('property_id','!=',$account_id)->first();
                            User::where( 'id' , $infouser->user_id)->update(array('unit_no'=>$currentUnitObj->unit_id,"building_no"=>$currentUnitObj->building_id,"role_id"=>$currentUnitObj->role_id,'account_id'=>$currentUnitObj->property_id));;
                        }
                        //echo "<br /> 2 units User info ID : ".$infouser->id. " User ID:".$infouser->user_id. "<br />";
                        UserMoreInfo::where('id', $infouser->id)->where('account_id',$account_id)->delete();
                    }
                }else{
                     $unitcount = UserProperty::where('user_id',$infouser->user_id)->where('property_id','!=',$account_id)->count();
                     if($unitcount==0){
                        //echo "Employee info ID : ".$infouser->id. " User ID:".$infouser->user_id. "<br />";
                        UserMoreInfo::where('id', $infouser->id)->where('account_id',$account_id)->delete();
                        User::where('id', $infouser->user_id)->where('account_id',$account_id)->delete();
                     }else{
                        $userinfo =  User::where('id' , $infouser->user_id)->where('account_id',$account_id)->first();
                        if(!empty($userinfo)){
                            $currentPropObj = UserProperty::where('user_id',$infouser->user_id)->where('property_id','!=',$account_id)->first();
                            User::where( 'id' , $infouser->user_id)->update(array('account_id'=>$currentPropObj->property_id));;
                        }
                        UserMoreInfo::where('id', $infouser->id)->where('account_id',$account_id)->delete();
                     }

                }
            }
        }
        UserProperty::where('property_id',$account_id)->delete();
        UserPurchaserUnit::where('property_id',$account_id)->delete();
        Building::where('account_id',$account_id)->delete();
        Unit::where('account_id',$account_id)->delete();

        Property::findOrFail($account_id)->delete();

        //exit;
        //$result = Property::where( 'id' , $id)->update( array( 'status' => 0));
        return redirect('opslogin/configuration/property#settings')->with('status', 'Property account has been deleted!');;

    }

}
