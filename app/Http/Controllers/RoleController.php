<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rule;

use App\Models\v7\Role;
use App\Models\v7\Property;
use App\Models\v7\Module;
use App\Models\v7\Device;
use App\Models\v7\RoleDevice;
use App\Models\v7\RoleProperty;
use App\Models\v7\RoleRemoteDevice;
use App\Models\v7\UserProperty;

use App\Models\v7\ModuleSetting;
use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use Auth;
use DB;
use App\Models\v7\PropertyPermission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index()
    {
        $q ='';
        $env_roles 	= env('USER_APP_ROLE');
        $user_roles = explode(",",$env_roles);
        
        $account_id = Auth::user()->account_id;
        if(Auth::user()->role_id ==1)
            $roles = Role::paginate(50); 
        else
            $roles = Role::WhereRaw("CONCAT(',',account_id,',') LIKE ?", '%,'.$account_id .',%')->paginate(50); 
        

        return view('admin.role.index', compact('roles','q','user_roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $account_id = Auth::user()->account_id;
        $login_id = Auth::user()->id;
        if(Auth::user()->role_id ==1){
            $properties = Property::get();
        }
        else{
            $prop_ids = UserProperty::where('user_id',$login_id)->get();
            $properties =array();
        
            if(isset($prop_ids)){
                $assigned_property = array();
                foreach($prop_ids as $prop_id){
                    $assigned_property[] = $prop_id->property_id;
                }
                $properties = Property::whereIn('id',$assigned_property)->get();
            }
            
        }
        //$users = User::pluck('name','id')->all();
        return view('admin.role.create', compact('properties'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       // print_r($request->role);
       $input = $request->all();
       if(isset($input['account_id']))
           $input['account_id'] = $input['account_id'];
       else
           $input['account_id'] = Auth::user()->account_id;

        $validator = Validator::make($request->all(), [ 
            'name' =>[
                'required', 
                Rule::unique('roles')
                       ->where('account_id', $input['account_id'])
            ],
            
        ]);
        if ($validator->fails()) { 

            return redirect('opslogin/configuration/role/create#settings')->with('status', 'Role already exist!');         
        }

       
        if(isset($_REQUEST['props'])){
            $auth = new \App\Models\v7\Property();
            $thinmoo_access_token = $auth->thinmoo_auth_api();
            $properties = '';
            foreach($_REQUEST['props'] as $property){
                $validation = Role::WhereRaw("CONCAT(',',account_id,',') LIKE ?", '%,'.$property .',%')->where('name',$request->name)->first();
                if(empty($validation))
                    $properties .= $property.",";
            }
            if($properties !=''){
                $input['account_id'] = substr($properties,0,-1);
                $role_data = Role::create($input);
            }else{
                return redirect('opslogin/configuration/role/create#settings')->with('status', 'Role already exist!');         

            }


            foreach($_REQUEST['props'] as $property){
                $roles_array = array();
                $roles_array['account_id'] = $property;
                $roles_array['name'] = $request->name;
                $validation = Role::WhereRaw("CONCAT(',',account_id,',') LIKE ?", '%,'.$property .',%')->where('name',$request->name)->first();
                //$validation = Role::where('account_id', 'LIKE', '%'.$property .'%')->where('name',$request->name)->first();
                if(isset($validation)){
                    //$role_data =  Role::create($roles_array);
                    $role_obj = new \App\Models\v7\Role();
                    $role_result = $role_obj->role_check_record($thinmoo_access_token, $property,$role_data->id);
                   
                    if($role_result['code'] !=0){
                        $role_data->parentUuid = 3;
                        $add_role_result = $role_obj->role_add_api($thinmoo_access_token, $property,$role_data);
                    }
                   
                }
            }
           
           
        }else{
            $role_data = Role::create($input);

            if(isset($roleObj->account_id) && $roleObj->account_id >0){
                $auth = new \App\Models\v7\Property();
                $thinmoo_access_token = $auth->thinmoo_auth_api();

                $role_obj = new \App\Models\v7\Role();
                $role_result = $role_obj->role_check_record($thinmoo_access_token, $role_data->account_id,$role_data->id);

                if($role_result['code'] !=0){
                    $role_data->parentUuid = 3;
                    $add_role_result = $role_obj->role_add_api($thinmoo_access_token, $role_data->account_id,$role_data);
                }
            }

        }

        return redirect('opslogin/configuration/role#settings')->with('status', 'Role has been updated!');
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

        $userObj = Auth::user();
        $roleObj = Role::find($id);
        $env_roles 	= env('USER_APP_ROLE');
        $user_app_roles = explode(",",$env_roles);

        if($id ==3)
            $devices = Device::get();
        else
            $devices = Device::where('account_id',$roleObj->account_id)->get();

        $device_access = array();
        foreach($roleObj->roledevices as $selecteddevices){
            $device_access[] = $selecteddevices->device_id;
           
        }
        $device_remote_access = array();
        foreach($roleObj->roleremotedevices as $selectedremotedevices){
            $device_remote_access[] = $selectedremotedevices->device_id;
           
        }
        $role_access = array();
        foreach($roleObj->Permissions as $permission){
            $role_access[$permission->module_id] = array($permission->view,$permission->create,$permission->edit,$permission->delete);
           
        }
        if($userObj->role_id ==1)
            $modules = Module::where('status',1)->orderBy('name','ASC')->get();
            // echo "hai"
        else{
            $property_access_lists = PropertyPermission::where('property_id',$userObj->account_id)->where('view',1)->get();
            $list_array = array();
            foreach($property_access_lists as $list){
                $list_array[] = $list->module_id;
            }
            //print_r($list_array);
            $modules = Module::whereIn('id',$list_array)->where('type','!=',3)->where('status',1)->orderBy('name','ASC')->get();
        }
        $device_display = 0;
        if(!in_array($roleObj->id,$user_app_roles))
            $device_display = 1;

        $properties = Property::pluck('company_name', 'id')->all();
        return view('admin.role.edit', compact('roleObj','properties','modules','role_access','user_app_roles','devices','device_access','device_remote_access','device_display'));
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
        $input = $request->all();
        $userObj = Auth::user();
        $roleObj = Role::find($id);
       /* if($request->input('account_id') !='')
            $roleObj->account_id = $request->input('account_id');
        else
            $roleObj->account_id= Auth::user()->account_id;
        
        $validator = Validator::make($request->all(), [ 
            'name' =>[
                'required', 
                Rule::unique('roles')
                    ->where('account_id', $roleObj->account_id)
                    ->whereNotIn('id',[$id])
            ],
            
        ]);
        */
        if($request->input('account_id') !='')
            $account_id = $request->input('account_id');
        else
            $account_id= Auth::user()->account_id;

       /* $validator = Validator::make($request->all(), [ 
            'name' =>[
                'required', 
                Rule::unique('roles')
                    ->WhereRaw("CONCAT(',',account_id,',') LIKE ?", '%,'.$account_id .',%')
                    ->whereNotIn('id',[$id])
            ],
            
        ]);
        
        if ($validator->fails()) { 
            return redirect("opslogin/configuration/role/$id/edit")->with('status', 'Role already exist!');         
        }*/
        $roleObj->name = $request->input('name');
        $roleObj->save();

        if($id ==3)
            $devices = Device::get();
        else
            $devices = Device::where('account_id',$roleObj->account_id)->get();
        //$devices = Device::where('account_id',$roleObj->account_id)->get();

        $device_serial_no = array();
        $device_serial_lists ='';
        //Bluetooth door access devices
        RoleDevice::where('role_id',$id)->delete();
        foreach($devices as $device) {
            $device_input = array();
            $device_checked = "device_".$device->id;
            if(isset($input[$device_checked]))
                {                   
                    if(!in_array($device->device_serial_no,$device_serial_no)){
                        $device_serial_no[] = $device->device_serial_no;
                        $device_serial_lists .= $device->device_serial_no.",";
                    }

                    $device_input['role_id'] = $id;
                    $device_input['account_id'] = $device->account_id;
                    $device_input['device_id'] = $device->id;
                    $device_input['device_svn'] = $device->device_serial_no;
                    RoleDevice::create($device_input);  
                }               
        }
        //Remote door access devices
        RoleRemoteDevice::where('role_id',$id)->delete();
        foreach($devices as $device) {
            $device_input = array();
            $device_checked = "device_remote_".$device->id;
            if(isset($input[$device_checked]))
                {          
                    if(!in_array($device->device_serial_no,$device_serial_no)){
                        $device_serial_no[] = $device->device_serial_no;
                        $device_serial_lists .= $device->device_serial_no.",";
                    }

                    $device_input['role_id'] = $id;
                    $device_input['account_id'] = $device->account_id;
                    $device_input['device_id'] = $device->id;
                    $device_input['device_svn'] = $device->device_serial_no;
                    RoleRemoteDevice::create($device_input);  
                }               
        }

        $device_serial_lists= substr($device_serial_lists,0,-1);
        
        if(isset($roleObj->account_id) && $roleObj->account_id >0){
            $auth = new \App\Models\v7\Property();
            $thinmoo_access_token = $auth->thinmoo_auth_api();
            //echo "Role_id".$roleObj->id;

            $role_obj = new \App\Models\v7\Role();
            $role_result = $role_obj->role_check_record($thinmoo_access_token, $account_id,$roleObj->id);
           /* print_r($role_result);
            echo "<hr /><br /> Token :".$thinmoo_access_token;
            echo "<br /> Role :".$roleObj->id;
            echo "<br /> Account :".$account_id;
            echo "<br /> Devices:";
            print_r($device_serial_lists);*/
            //exit;
            if($role_result['code'] ==0){  
                $add_role_result = $role_obj->role_modify_api($thinmoo_access_token,$roleObj);
                $role_access = $role_obj->role_access_api($thinmoo_access_token,$roleObj->id,$account_id,$device_serial_lists);
            }
            else{        
                $roleObj->parentUuid = 3;
                $add_role_result = $role_obj->role_add_api($thinmoo_access_token,$account_id,$roleObj);
                $role_access = $role_obj->role_access_api($thinmoo_access_token,$roleObj->id,$account_id,$device_serial_lists);
            }
        }
        //print_r($add_role_result);
        //exit;
       //echo "hai";
        $env_roles 	= env('USER_APP_ROLE');
        $user_app_roles = explode(",",$env_roles);

        if(!in_array($id,$user_app_roles)){

            ModuleSetting::where('role_id',$id)->delete();

            if($userObj->role_id ==1)
                $modules = Module::where('status',1)->orderBy('name','ASC')->get();
                // echo "hai"
            else{
                $property_access_lists = PropertyPermission::where('property_id',$userObj->account_id)->where('view',1)->get();
                $list_array = array();
                foreach($property_access_lists as $list){
                    $list_array[] = $list->module_id;
                }
                //print_r($list_array);
                $modules = Module::whereIn('id',$list_array)->where('type','!=',3)->where('status',1)->orderBy('name','ASC')->get();
            }

            foreach($modules as $module) {
                $input['role_id'] = $id;
                $input['module_id'] = $module->id;
                $view_field = "mod_view_".$module->id;
                if(isset($input[$view_field]))
                    $input['view'] = 1;
                else
                    $input['view'] = 0;

                $add_field = "mod_add_".$module->id;
                if(isset($input[$add_field]))
                    $input['create'] = 1;
                else
                    $input['create'] = 0;

                $edit_field = "mod_edit_".$module->id;
                if(isset($input[$edit_field]))
                    $input['edit'] = 1;
                else
                    $input['edit'] = 0;

                $delete_field = "mod_delete_".$module->id;
                if(isset($input[$delete_field]))
                    $input['delete'] = 1;
                else
                    $input['delete'] = 0;

                ModuleSetting::create($input);  
            }
        }

        return redirect('opslogin/configuration/role#settings')->with('status', 'Role has been updated!');
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

        $roleObj = Role::find($id);
        $auth = new \App\Models\v7\Property();
        $thinmoo_access_token = $auth->thinmoo_auth_api();
        $role_obj = new \App\Models\v7\Role();
        $roles = explode(",",$roleObj->account_id);
        if(isset($roles) && count($roles) >0){
            foreach($roles as $role){
                $role_result = $role_obj->role_check_record($thinmoo_access_token, $role,$roleObj->id);
                if($role_result['code'] ==0){            
                    $delete_role_result = $role_obj->role_delete_api($thinmoo_access_token,$roleObj);
                }
            }
        }
        Role::findOrFail($id)->delete();
        return redirect('opslogin/configuration/role#settings')->with('status', 'Role deleted successfully!');
    }

     public function search(Request $request){
        $q = $request->input('q');
        if($q != "" ){

        $roles = Role::where('role', 'LIKE', '%'.$q .'%')->paginate(50);
         
        return view('admin.role.index', compact('roles','q'));
        }
       
        else{
         return redirect('opslogin/project');
        }
   }
}
