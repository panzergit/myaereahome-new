<?php

namespace App\Http\Controllers;

use App\Models\v7\Role;
use App\Models\v7\Module;
use App\Models\v7\ModuleSetting;
use Illuminate\Http\Request;
use Validator;



class ModuleSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::paginate(50);   
        return view('admin.modulesetting.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $modules = Module::where('status',1)->orderBy('orderby','ASC')->get();
        return view('admin.modulesetting.create', compact('modules'));
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
            'name' => 'required|unique:roles' 
        ]);
        if ($validator->fails()) { 
             return redirect('opslogin/configuration/menu/create')->with('status', 'Role already exist!');         
        }

        $result = Role::create($input); 
        //echo $result->id;
        //exit;

        $modules = Module::where('status',1)->orderBy('orderby','ASC')->get();

        foreach($modules as $module) {
            echo $input['role_id'] = $result->id;
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

           $role = ModuleSetting::create($input);  
        }
    
        return redirect('opslogin/configuration/menu')->with('status', 'Role has been added!');;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\v7\ModuleSetting  $moduleSetting
     * @return \Illuminate\Http\Response
     */
    public function show(ModuleSetting $moduleSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\v7\ModuleSetting  $moduleSetting
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $RoleObj = Role::find($id);
        $role_access = array();
        foreach($RoleObj->Permissions as $permission){
            $role_access[$permission->module_id] = array($permission->view,$permission->create,$permission->edit,$permission->delete);
           
        }


        $modules = Module::where('status',1)->orderBy('orderby','ASC')->get();
        return view('admin.modulesetting.edit', compact('RoleObj','modules','role_access'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\v7\ModuleSetting  $moduleSetting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $roleObj = Role::find($id);

        $input = $request->all();

        $roleObj->name = $request->input('name');

        $roleObj->save();

        ModuleSetting::where('role_id',$id)->delete();

        $modules = Module::where('status',1)->orderBy('orderby','ASC')->get();

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
    
        return redirect('opslogin/configuration/menu')->with('status', 'Role & Permissions has been added!');;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\v7\ModuleSetting  $moduleSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ModuleSetting::where('role_id',$id)->delete();

        Role::findOrFail($id)->delete();
        return redirect('opslogin/configuration/menu')->with('status', 'Role deleted successfully!');
    }
}
