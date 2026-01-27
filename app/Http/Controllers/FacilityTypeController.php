<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rule;
use App\Models\v7\Property;
use App\Models\v7\FacilityType;
use App\Models\v7\FeedbackSubmission;
use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use Auth;
use DB;

class FacilityTypeController extends Controller
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
        
        $facilities = FacilityType::where('account_id',$account_id)->paginate(150);   
        return view('admin.facilitytype.index', compact('facilities','q'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $property_id =  Auth::user()->account_id;
        $propertyObj = Property::where('id',$property_id)->first();
        return view('admin.facilitytype.create', compact('propertyObj'));
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

        if(isset($input['account_id']))
            $input['account_id'] = $input['account_id'];
        else
            $input['account_id'] = Auth::user()->account_id;

        $validator = Validator::make($request->all(), [ 
            'facility_type' =>[
                'required', 
                Rule::unique('facility_types')
                    ->where('account_id', $input['account_id'])
            ],
            
        ]);

        if ($validator->fails()) { 
            return redirect('opslogin/configuration/facility/create')->with('status', 'Facility type already exist!');         
        }
        if ($request->file('facility_image') != null) {
            
            $input['facility_image'] = remove_upload_path($request->file('facility_image')->store(upload_path('facility')));
        }
        $propertyObj = Property::where('id',$input['account_id'])->first();

        if($propertyObj->opn_secret_key ==''){
            $input['payment_required'] =2;
            $input['booking_fee'] =0.00;
            $input['booking_deposit'] =0.00;
        }
        //$input['account_id'] = Auth::user()->account_id;
        FacilityType::create($input);

        return redirect('opslogin/configuration/facility')->with('status', 'Facility has been added!');
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

        $facilityObj = FacilityType::find($id);
        $file_path = env('APP_URL')."/storage/app/";
        $property_id =  Auth::user()->account_id;
        $propertyObj = Property::where('id',$property_id)->first();

        return view('admin.facilitytype.edit', compact('facilityObj','file_path','propertyObj'));
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

        $facilityObj = FacilityType::find($id);
        $input = $request->all();
        if(isset($input['account_id']))
            $facilityObj->account_id = $input['account_id'];
        else
            $facilityObj->account_id  = Auth::user()->account_id;

        $validator = Validator::make($request->all(), [ 
            'facility_type' =>[
                'required', 
                Rule::unique('facility_types')
                    ->where('account_id', $facilityObj->account_id)
                    ->whereNotIn('id',[$id])
            ],
            
        ]);

        if ($validator->fails()) { 
            return redirect("opslogin/configuration/facility/$id/edit")->with('status', 'Facility already exist!');         
        }

        $property_id =  Auth::user()->account_id;
        $propertyObj = Property::where('id',$property_id)->first();

        $facilityObj->facility_type = $request->input('facility_type');
        $facilityObj->calendar_availability_start = $request->input('calendar_availability_start');
        $facilityObj->next_booking_allowed = $request->input('next_booking_allowed');
        $facilityObj->allowed_booking_for = $request->input('allowed_booking_for');
        $facilityObj->next_booking_allowed_days = $request->input('next_booking_allowed_days');
        $facilityObj->timing = $request->input('timing');
        $facilityObj->blockout_days = $request->input('blockout_days');
       
        $facilityObj->cut_of_amount_percentage = $request->input('cut_of_amount_percentage');
        $facilityObj->cut_of_days = $request->input('cut_of_days');
        $facilityObj->notes = $request->input('notes');
      
        if($propertyObj->opn_secret_key !=''){
            $facilityObj->payment_required =$request->input('payment_required');
            $facilityObj->booking_fee = $request->input('booking_fee');
            $facilityObj->booking_deposit = $request->input('booking_deposit');
        }else{
            $facilityObj->payment_required =2;
            $facilityObj->booking_fee = 0.00;
            $facilityObj->booking_deposit = 0.00;
        }
        
        if(empty($request->input('payment_required'))){
            $facilityObj->payment_required =2;
            $facilityObj->booking_fee =0.00;
            $facilityObj->booking_deposit =0.00;
        }
        
        if ($request->file('facility_image') != null) {
            $facilityObj->facility_image = remove_upload_path($request->file('facility_image')->store(upload_path('facility')));
        }
        
        $facilityObj->save();
        return redirect('opslogin/configuration/facility')->with('status', 'Facility has been updated!');
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

        FacilityType::findOrFail($id)->delete();
        return redirect('opslogin/configuration/facility')->with('status', 'Facility type deleted successfully!');
    }

}
