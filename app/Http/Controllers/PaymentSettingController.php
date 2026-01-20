<?php

namespace App\Http\Controllers;


use App\Models\v7\PaymentSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use File;
use Auth;
use Session;


class PaymentSettingController extends Controller
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
           
            $PaymentObj = PaymentSetting::paginate(50);   
            return view('admin.payment.index', compact('PaymentObj'));
        }else{
            $account_id = Auth::user()->account_id;
            $PaymentObj = PaymentSetting::where('account_id',$account_id)->first();
        
            return view('admin.payment.edit', compact('PaymentObj'));

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
        
        return view('admin.property.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
       
        return redirect('opslogin/configuration/property/')->with('status', 'Property has been added!');
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\v7\property  $property
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $PropertyObj = Property::find($id);
        $file_path = env('APP_URL')."/storage/app/";

        return view('admin.property.edit', compact('PropertyObj','file_path'));
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
        $paymentObj = PaymentSetting::find($id);

       if($request->input('terms1') != null){
            if($request->input('cheque_payable_to') != null)
            $paymentObj->cheque_payable_to = $request->input('cheque_payable_to');
       }else{
            $paymentObj->cheque_payable_to ='';
       }
        
       if($request->input('terms3') != null){
            if($request->input('cash_payment_info') != null)
            $paymentObj->cash_payment_info = $request->input('cash_payment_info');
       }else{
            $paymentObj->cash_payment_info = '';
       }
        if($request->input('terms2') != null){
            if($request->input('account_holder_name') != null)
                $paymentObj->account_holder_name = $request->input('account_holder_name');

            if($request->input('account_number') != null)
                $paymentObj->account_number = $request->input('account_number');

            if($request->input('account_type') != null){
                $paymentObj->account_type = $request->input('account_type');
            }

            if($request->input('bank_name') != null){
                $paymentObj->bank_name = $request->input('bank_name');
            }

            if($request->input('bank_address') != null){
                $paymentObj->bank_address = $request->input('bank_address');
            }

            if ($request->input('swift_code') != null){
                $paymentObj->swift_code = $request->input('swift_code');
            } 
        }else{
            $paymentObj->account_holder_name = '';
            $paymentObj->account_number = '';
            $paymentObj->account_type = '';
            $paymentObj->bank_name = '';
            $paymentObj->bank_address = '';
            $paymentObj->swift_code = '';
        }      

       

        $paymentObj->save();

       
        return redirect('opslogin/configuration/payment_setting/')->with('status', 'Payment information has been updated!');
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
        $modules = Module::where('status',1)->orderBy('name','ASC')->get();
        $role_access = array();
        foreach($PropertyObj->Permissions as $permission){
            $role_access[$permission->module_id] = array($permission->view,$permission->create,$permission->edit,$permission->delete);
           
        }

        return view('admin.property.access', compact('PropertyObj','file_path','modules','role_access'));
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
    
        return redirect('opslogin/configuration/property')->with('status', 'Property modules has been updated!');;

        
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
        return redirect('opslogin/configuration/property')->with('status', 'Property account activated!');;

    }

    public function deactivate($id)
    {
        $result = Property::where( 'id' , $id)->update( array( 'status' => 0));
        return redirect('opslogin/configuration/property')->with('status', 'Property account de-activated!');;

    }

}
