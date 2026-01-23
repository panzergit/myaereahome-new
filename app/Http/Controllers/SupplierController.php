<?php

namespace App\Http\Controllers;

use App\Models\v7\Supplier;
use App\Models\v7\Property;
use App\Models\v7\Country;
use App\Models\v7\VisitorTypeSubcategory;
use Illuminate\Validation\Rule;

use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use DB;
use Auth;

class SupplierController extends Controller
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

        $lists = Supplier::where('account_id',$account_id)->paginate(env('PAGINATION_ROWS'));  
       
        $propertyObj = Property::where('id',$account_id)->first();

        return view('admin.supplier.index', compact('lists','q','propertyObj'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $properties = Property::pluck('company_name', 'id')->all();
        $countries = Country::where('status',1)->pluck('country_name', 'id')->all();

        return view('admin.supplier.create', compact('properties','countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       // print_r($request->visitortype);
        $input = $request->all();

       if(isset($input['account_id']))
            $input['account_id'] = $input['account_id'];
        else
            $input['account_id'] = Auth::user()->account_id;

       /*$validator = Validator::make($request->all(), [ 
            'visiting_purpose' =>[
                'required', 
                Rule::unique('visitor_types')
                       ->where('account_id', $input['account_id'])
            ],
            
        ]);
        if ($validator->fails()) { 

             return redirect('opslogin/configuration/purpose/create')->with('status', 'Visiting Purpose already exist!');         
        }*/
        if ($request->file('attachment') != null) {
			$input['attachment'] = $request->file('attachment')->store(upload_path('supplier'));
        }
        $typeObj = Supplier::create($input);

        return redirect('opslogin/supplier')->with('status', 'Supplier has been added!');
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
       
        $SupplierObj = Supplier::find($id);
        $properties = Property::pluck('company_name', 'id')->all();
        $countries = Country::where('status',1)->pluck('country_name', 'id')->all();

        return view('admin.supplier.edit', compact('SupplierObj','properties','countries'));
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

        $supplierObj = Supplier::find($id);

        $supplierObj->title = $request->input('title');
        $supplierObj->first_name = $request->input('first_name');
        $supplierObj->middle_name = $request->input('middle_name');
        $supplierObj->last_name = $request->input('last_name');
        $supplierObj->suffix = $request->input('suffix');
        $supplierObj->supplier_display_name = $request->input('supplier_display_name');
        $supplierObj->company_name = $request->input('company_name');
        $supplierObj->email = $request->input('email');
        $supplierObj->phone_number = $request->input('phone_number');
        $supplierObj->mobile_number = $request->input('mobile_number');
        $supplierObj->fax = $request->input('fax');
        $supplierObj->website = $request->input('website');
        $supplierObj->address1 = $request->input('address1');
        $supplierObj->address2 = $request->input('address2');
        $supplierObj->city = $request->input('city');
        $supplierObj->province = $request->input('province');
        $supplierObj->postal_code = $request->input('postal_code');
        $supplierObj->country = $request->input('country');
        $supplierObj->notes = $request->input('notes');
        $supplierObj->attachement = $request->input('attachement');
        $supplierObj->business_id = $request->input('business_id');
        $supplierObj->billing_rate = $request->input('billing_rate');
        $supplierObj->payment_term = $request->input('payment_term');
        $supplierObj->account_no = $request->input('account_no');
        $supplierObj->expense_category = $request->input('expense_category');
        $supplierObj->opening_balance = $request->input('opening_balance');
        $supplierObj->opening_balance_date = $request->input('opening_balance_date');

        $supplierObj->save();


        return redirect('opslogin/supplier')->with('status', 'Supplier has been updated!');
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
        supplier::findOrFail($id)->delete();
        return redirect('opslogin/supplier')->with('status', 'Supplier deleted successfully!');
    }

    



}
