<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Builder;

use App\Models\v7\FinanceShareSetting;
use App\Models\v7\FinanceInvoice;
use App\Models\v7\FinanceInvoiceInfo;
use App\Models\v7\FinanceInvoiceDetail;
use App\Models\v7\FinanceInvoicePayment;
use App\Models\v7\FinanceReferenceType;
use App\Models\v7\FinanceInvoicePaymentDetail;
use App\Models\v7\FinanceInvoicePaymentPaidDetail;
use App\Models\v7\FinanceNotificationDetail;
use App\Models\v7\FinanceAdvancePayment;
use App\Models\v7\FinancePaymentLog;
use App\Models\v7\FinanceCreditPayment;
use App\Models\v7\UserLog;
use App\Models\v7\FirebaseNotification;
use App\Models\v7\UserNotification;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\Property;
use App\Models\v7\UserNotificationSetting;
use App\Models\v7\Unit;
use App\Models\v7\Building;
use Carbon\Carbon;

use Session;


use Illuminate\Validation\Rule;

use Illuminate\Http\Request;
use Validator;
use App\Models\v7\User;
use App\Models\v7\UserMoreInfo;
use DB;
use Auth;

class FinanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function paymentoverview()
    {
        session()->forget('current_page');

        $q ='';
        $account_id = Auth::user()->account_id;

        //$month = date();
        $from_date = date('Y-m-01');
        $to_date = date('Y-m-t');

        //Current month Amount START
        $mf_amounts = FinanceInvoicePaymentPaidDetail::where('account_id',$account_id)->where('type',1)->whereBetween('payment_received_date',array($from_date,$to_date))->sum('amount'); 
        $sf_amounts = FinanceInvoicePaymentPaidDetail::where('account_id',$account_id)->where('type',2)->whereBetween('payment_received_date',array($from_date,$to_date))->sum('amount'); 
        $tax_amounts = FinanceInvoicePaymentPaidDetail::where('account_id',$account_id)->where('type',4)->whereBetween('payment_received_date',array($from_date,$to_date))->sum('amount'); 
        $int_amounts = FinanceInvoicePaymentPaidDetail::where('account_id',$account_id)->where('type',3)->whereBetween('payment_received_date',array($from_date,$to_date))->sum('amount'); 
        $monthly_fee = $mf_amounts + $sf_amounts + $tax_amounts + $int_amounts;
        //Current month Amount END

        //Current month records START
        $total_invoices = FinanceInvoice::where('account_id',$account_id)->whereBetween('invoice_date',array($from_date,$to_date))->orderby('id','desc')->count();  
        $pending_invoices = FinanceInvoice::where('account_id',$account_id)->where('status',1)->whereBetween('invoice_date',array($from_date,$to_date))->orderby('id','desc')->count(); 
        $partial_invoices = FinanceInvoice::where('account_id',$account_id)->where('status',2)->whereBetween('invoice_date',array($from_date,$to_date))->orderby('id','desc')->count(); 
        $paid_invoices = FinanceInvoice::where('account_id',$account_id)->where('status',3)->whereBetween('invoice_date',array($from_date,$to_date))->orderby('id','desc')->count(); 
        //Current month records END

        //Current month Amount START
        $tot_mf_amounts = FinanceInvoicePaymentPaidDetail::where('account_id',$account_id)->where('type',1)->sum('amount'); 
        $tot_sf_amounts = FinanceInvoicePaymentPaidDetail::where('account_id',$account_id)->where('type',2)->sum('amount'); 
        $tot_tax_amounts = FinanceInvoicePaymentPaidDetail::where('account_id',$account_id)->where('type',4)->sum('amount'); 
        $tot_int_amounts = FinanceInvoicePaymentPaidDetail::where('account_id',$account_id)->where('type',3)->sum('amount'); 
        

        $all_mf_amounts =DB::table('finance_invoice_payment_paid_details')
                            ->select(DB::raw('SUM(amount) as total_amount'),'payment_received_date')->where('account_id',$account_id)->where('type',1)
                            ->groupBy(DB::raw('YEAR(payment_received_date) DESC, MONTH(payment_received_date) DESC'))->orderby('payment_received_date','desc')->limit(5)->get();
        
        $all_sf_amounts =DB::table('finance_invoice_payment_paid_details')
                            ->select(DB::raw('SUM(amount) as total_amount'),'payment_received_date')->where('account_id',$account_id)->where('type',2)
                            ->groupBy(DB::raw('YEAR(payment_received_date) DESC, MONTH(payment_received_date) DESC'))->orderby('payment_received_date','desc')->limit(5)->get();
        $mf_y_axis = '';
        if(isset($all_mf_amounts)){
            foreach($all_mf_amounts as $all_mf_amount){
                $mf_y_axis .= "{ y: ".$all_mf_amount->total_amount.", label: '".date('M y',strtotime($all_mf_amount->payment_received_date))."'},";
            }
        }

        $sf_y_axis = '';
        if(isset($all_sf_amounts)){
            foreach($all_sf_amounts as $all_sf_amount){
                $sf_y_axis .= "{ y: ".$all_sf_amount->total_amount.", label: '".date('M y',strtotime($all_sf_amount->payment_received_date))."'},";
            }
        }
        $mf_y_axis = substr($mf_y_axis,0,-1);
        $sf_y_axis = substr($sf_y_axis,0,-1);
       
        return view('admin.finance.overview', compact('total_invoices','pending_invoices','partial_invoices','paid_invoices','monthly_fee', 'mf_amounts', 'sf_amounts', 'tax_amounts', 'int_amounts', 'tot_mf_amounts', 'tot_sf_amounts', 'tot_tax_amounts', 'tot_int_amounts','mf_y_axis','sf_y_axis'));
    }
     public function index()
    {
        session()->forget('current_page');

        $q = $option =$unit = $status = $invoice_no = $batch_file_no = $users = $month ='';
        $account_id = Auth::user()->account_id;
        $invoices = FinanceInvoiceInfo::where('account_id',$account_id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));       

        $currentURL = url()->full();
        $page = explode("=",$currentURL);
        if(isset($page[1]) && $page[1]>0){
                session()->put('page', $page[1]);
        }else{
                session()->forget('page');
        }
        $visitor_app_url = url('visitors');
        //$shares = FinanceShareSetting::paginate(150);   
        return view('admin.finance.index',compact('invoices','unit','status','invoice_no','batch_file_no','option','month','visitor_app_url'));
    }
    public function lists($id)
    {
        $q = $option =$unit = $status = $invoice_no = $batch_file_no = $users = $month ='';
        $account_id = Auth::user()->account_id;
        $info_id = $id;
        $invoices = FinanceInvoice::where('account_id',$account_id)->where('info_id',$id)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));       

        $currentURL = url()->full();
        $page = explode("=",$currentURL);
        session()->forget('current_page');
       
        if(isset($page[1]) && $page[1]>0){
                session()->put('page', $page[1]);
        }else{
                session()->forget('page');
        }
        $visitor_app_url = url('visitors');
        //$shares = FinanceShareSetting::paginate(150);   
        return view('admin.finance.invoices',compact('invoices','unit','status','invoice_no','batch_file_no','option','month','visitor_app_url','info_id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $create_invoice = 1;
        $account_id = Auth::user()->account_id;
        $login_id = Auth::user()->id;
        $buildings = Building::where('account_id',$account_id)->get();
        $property = Property::where('id',$account_id)->first();
        $invoice = FinanceInvoiceInfo::where('account_id',$account_id)->orderby('id','desc')->first();  
        $types = FinanceReferenceType::where('type',1)->pluck('reference_type', 'id')->all();
        $amounts = FinanceShareSetting::where("account_id",$account_id)->where('status',1)->first();

        $invoice_details = array();
        //print_r($invoice->details);
        if(isset($invoice->details)){
            foreach($invoice->details as $k => $detail){
               
                    $data =array();
                    $data['id'] = $detail->id;
                    $data['reference_type'] = $detail->reference_type;
                    $data['reference'] = $detail->reference;
                    $amount = 0.00;
                    if($detail->reference_type ==1){
                        $amount = number_format($amounts->management_fund_share,2);
                    }
                    else if($detail->reference_type ==2){
                        $amount = number_format($amounts->sinking_fund_share,2);
                    }
                    //$data['amount'] = $detail->amount;
                    $data['amount'] = $amount;
                    $data['description'] = $detail->description;
                    $invoice_details[$k+1] = $data;
            }
        }
        //print_r($invoice_details);
       
        return view('admin.finance.create', compact('buildings','invoice','invoice_details','types','property','create_invoice'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function back($id)
    {
        $invoiceObj = FinanceInvoiceInfo::find($id);
        $invoiceObj->status = 1;
        $invoiceObj->save();
        return redirect('opslogin/invoice/create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       // print_r($request->unit);
        $input = $request->all();
        $account_id = Auth::user()->account_id;
        $created_id = Auth::user()->id;
        
        $preview = 0;
        
        
        $reference_array = array_filter($input["reference_type"]);

        if(count($reference_array) !== count(array_unique($reference_array))){
            
            if(isset($input['invoice_info_id']) && $input['invoice_info_id'] >0){
                $id = $input["invoice_info_id"];
                $invoiceObj = FinanceInvoiceInfo::find($id);
                $invoiceObj->account_id = $account_id;
                $invoiceObj->created_by = $created_id;
                $invoiceObj->month = isset($input['month'])?$input['month']:'0000-00-00';
                $invoiceObj->comp_name = isset($input['comp_name'])?$input['comp_name']:'';
                $invoiceObj->comp_address = isset($input['comp_address'])?$input['comp_address']:'';
                $invoiceObj->bank_name = isset($input['bank_name'])?$input['bank_name']:'';
                $invoiceObj->account_no = isset($input['account_no'])?$input['account_no']:'';
                $invoiceObj->bank_code = isset($input['bank_code'])?$input['bank_code']:'';
                $invoiceObj->bank_address = isset($input['bank_address'])?$input['bank_address']:'';
                $invoiceObj->notes = isset($input['notes'])?$input['notes']:'';
                $invoiceObj->status = 1;
                $invoiceObj->save();

                FinanceInvoiceDetail::where('info_id', $invoiceObj->id)->delete();
                for($i=1;$i<=8;$i++){
                    
                    $data['info_id'] = $invoiceObj->id;
                    $data['reference_type'] = isset($input["reference_type"][$i])?$input["reference_type"][$i]:'';
                    $data['reference'] = isset($input["reference"][$i])?$input["reference"][$i]:'';
                    $data['amount'] = isset($input["amount"][$i])?$input["amount"][$i]:'';
                    $data['qty'] = 1;
                    $data['description'] = isset($input["description"][$i])?$input["description"][$i]:'';
                    $data['tot_amount'] = isset($input["amount"][$i])?$input["amount"][$i] * 1:'';
                    if($data['description'] !='')
                        $details = FinanceInvoiceDetail::create($data);
                }
            }
            else{
                //print_r($input["description"][1]);
                $input['account_id']= $account_id;
                $input['created_by']= $created_id;

                $input['status'] = 1;
                $info = FinanceInvoiceInfo::create($input);
                for($i=1;$i<=8;$i++){
                    //echo $input["description[$i]"];
                    $data['info_id'] = $info->id;
                    $data['reference_type'] = isset($input["reference_type"][$i])?$input["reference_type"][$i]:'';
                    $data['reference'] = isset($input["reference"][$i])?$input["reference"][$i]:'';
                    $data['amount'] = isset($input["amount"][$i])?$input["amount"][$i]:'';
                    $data['qty'] = 1;
                    $data['description'] = isset($input["description"][$i])?$input["description"][$i]:'';
                    $data['tot_amount'] = isset($input["amount"][$i])?$input["amount"][$i] * 1:'';
                    if($data['description'] !='')
                        $details = FinanceInvoiceDetail::create($data);
                }

            }
            return redirect('opslogin/invoice/create')->with('status', "Sorry, Will not allow duplicate reference type, please update!");
        }
        else {
            if($input['action'] =="save_as_draft"){ // save as draft
            
                if(isset($input['invoice_info_id']) && $input['invoice_info_id'] >0){
                    $id = $input["invoice_info_id"];
                    $invoiceObj = FinanceInvoiceInfo::find($id);
                    $invoiceObj->account_id = $account_id;
                    $invoiceObj->created_by = $created_id;
                    $invoiceObj->month = isset($input['month'])?$input['month']:'0000-00-00';
                    $invoiceObj->comp_name = isset($input['comp_name'])?$input['comp_name']:'';
                    $invoiceObj->comp_address = isset($input['comp_address'])?$input['comp_address']:'';
                    $invoiceObj->bank_name = isset($input['bank_name'])?$input['bank_name']:'';
                    $invoiceObj->account_no = isset($input['account_no'])?$input['account_no']:'';
                    $invoiceObj->bank_code = isset($input['bank_code'])?$input['bank_code']:'';
                    $invoiceObj->bank_address = isset($input['bank_address'])?$input['bank_address']:'';
                    $invoiceObj->notes = isset($input['notes'])?$input['notes']:'';
                    $invoiceObj->status = 1;
                    $invoiceObj->save();

                    FinanceInvoiceDetail::where('info_id', $invoiceObj->id)->delete();
                    for($i=1;$i<=8;$i++){
                        
                        $data['info_id'] = $invoiceObj->id;
                        $data['reference_type'] = isset($input["reference_type"][$i])?$input["reference_type"][$i]:'';
                        $data['reference'] = isset($input["reference"][$i])?$input["reference"][$i]:'';
                        $data['amount'] = isset($input["amount"][$i])?$input["amount"][$i]:'';
                        $data['qty'] = 1;
                        $data['description'] = isset($input["description"][$i])?$input["description"][$i]:'';
                        $data['tot_amount'] = isset($input["amount"][$i])?$input["amount"][$i] * 1:'';
                        if($data['description'] !='')
                            $details = FinanceInvoiceDetail::create($data);
                    }
                }
                else{
                    //print_r($input["description"][1]);
                    $input['account_id']= $account_id;
                    $input['created_by']= $created_id;

                    $input['status'] = 1;
                    $info = FinanceInvoiceInfo::create($input);
                    for($i=1;$i<=8;$i++){
                        //echo $input["description[$i]"];
                        $data['info_id'] = $info->id;
                        $data['reference_type'] = isset($input["reference_type"][$i])?$input["reference_type"][$i]:'';
                        $data['reference'] = isset($input["reference"][$i])?$input["reference"][$i]:'';
                        $data['amount'] = isset($input["amount"][$i])?$input["amount"][$i]:'';
                        $data['qty'] = 1;
                        $data['description'] = isset($input["description"][$i])?$input["description"][$i]:'';
                        $data['tot_amount'] = isset($input["amount"][$i])?$input["amount"][$i] * 1:'';
                        if($data['description'] !='')
                            $details = FinanceInvoiceDetail::create($data);
                    }

                }
            }
            else if($input['action'] =="submit_preview"){
                $preview = 1;
                if(isset($input['invoice_info_id']) && $input['invoice_info_id'] >0){
                    $id = $input["invoice_info_id"];
                    $invoiceObj = FinanceInvoiceInfo::find($id);
                    $invoiceObj->month = isset($input['month'])?$input['month']:'0000-00-00';

                    $sharesettings = FinanceShareSetting::where("account_id",$account_id)->where('status',1)->first();
                    $due_days = 0;
                    $invoice_date = $invoiceObj->month;
                    if(isset($sharesettings->due_period_type) && $sharesettings->due_period_type ==1){
                        $due_period = $sharesettings->due_period_value;
                        $due_date = date('Y-m-d', strtotime($invoice_date. " + $due_period days"));
                        $invoiceObj->due_date =  $due_date;
                        
                    }
                    else if(isset($sharesettings->due_period_type) && $sharesettings->due_period_type ==2){
                        $due_period = $sharesettings->due_period_value;
                        $due_date = date('Y-m-d',strtotime("+$due_period months", strtotime($invoice_date))); 
                        $invoiceObj->due_date =  $due_date;

                    }

                    $invoiceObj->account_id= $account_id;
                    $invoiceObj->created_by= $created_id;
                    // $invoiceObj->due_date = isset($input['month'])?$input['due_date']:'0000-00-00';


                    $invoiceObj->comp_name = isset($input['comp_name'])?$input['comp_name']:'';
                    $invoiceObj->comp_address = isset($input['comp_address'])?$input['comp_address']:'';
                    $invoiceObj->bank_name = isset($input['bank_name'])?$input['bank_name']:'';
                    $invoiceObj->account_no = isset($input['account_no'])?$input['account_no']:'';
                    $invoiceObj->bank_code = isset($input['bank_code'])?$input['bank_code']:'';
                    $invoiceObj->bank_address = isset($input['bank_address'])?$input['bank_address']:'';
                    $invoiceObj->notes = isset($input['notes'])?$input['notes']:'';
                    $invoiceObj->status = 2;

                    $invoiceObj->save();

                    FinanceInvoiceDetail::where('info_id', $invoiceObj->id)->delete();
                    for($i=1;$i<=8;$i++){
                        //echo isset($input["reference"][$i])?$input["reference"][$i]:'';
                        $data['info_id'] = $invoiceObj->id;
                        $data['reference_type'] = isset($input["reference_type"][$i])?$input["reference_type"][$i]:'';
                        $data['reference'] = isset($input["reference"][$i])?$input["reference"][$i]:'';
                        $data['amount'] = isset($input["amount"][$i])?$input["amount"][$i]:'';
                        $data['qty'] = 1;
                        $data['description'] = isset($input["description"][$i])?$input["description"][$i]:'';
                        $data['tot_amount'] = isset($input["amount"][$i])?$input["amount"][$i] * 1:'';
                        if($data['description'] !='')
                            $details = FinanceInvoiceDetail::create($data);
                    }
                }
                else{
                    //print_r($input["description"][1]);
                    $sharesettings = FinanceShareSetting::where("account_id",$account_id)->where('status',1)->first();
                    $due_days = 0;
                    $invoice_date = $input["month"];
                    if(isset($sharesettings->due_period_type) && $sharesettings->due_period_type ==1){
                        $due_period = $sharesettings->due_period_value;
                        $due_date = date('Y-m-d', strtotime($invoice_date. " + $due_period days"));
                        $input['due_date']=  $due_date;
                        
                    }
                    else if(isset($sharesettings->due_period_type) && $sharesettings->due_period_type ==2){
                        $due_period = $sharesettings->due_period_value;
                        $due_date = date('Y-m-d',strtotime("+$due_period months", strtotime($invoice_date))); 
                        $input['due_date'] =  $due_date;

                    }
                    $input['account_id']= $account_id;
                    $input['created_by']= $created_id;
                    $input['status'] = 2;
                  
                    $invoiceObj = FinanceInvoiceInfo::create($input);

                    for($i=1;$i<=8;$i++){
                        //echo isset($input["reference"][$i])?$input["reference"][$i]:'';
                        $data['info_id'] = $invoiceObj->id;
                        $data['reference_type'] = isset($input["reference_type"][$i])?$input["reference_type"][$i]:'';
                        $data['reference'] = isset($input["reference"][$i])?$input["reference"][$i]:'';
                        $data['amount'] = isset($input["amount"][$i])?$input["amount"][$i]:'';
                        $data['qty'] = 1;
                        $data['description'] = isset($input["description"][$i])?$input["description"][$i]:'';
                        $data['tot_amount'] = isset($input["amount"][$i])?$input["amount"][$i] * 1:'';
                        if($data['description'] !='')
                            $details = FinanceInvoiceDetail::create($data);
                    }

                }
            }

            if($preview ==1)
                return redirect("opslogin/invoicepreview/$invoiceObj->id")->with('status', 'Invoice has been submitted for preview!');
            else
                return redirect('opslogin/invoice/create')->with('status', 'Invoice has been in draft!');
        }
        
    }


    public function invoicepreview($id)
    {
        $account_id = Auth::user()->account_id;
        $shares = FinanceShareSetting::where('account_id',$account_id)->where('status',1)->orderby('id','desc')->first();  

        $buildings = Building::where('account_id',$account_id)->get();
        $invoice = FinanceInvoiceInfo::where('id',$id)->where('account_id',$account_id)->orderby('id','desc')->first();
   
        $invoice_details = array();
        //print_r($invoice->details);
        if(isset($invoice->details)){
            foreach($invoice->details as $k => $detail){
                $data =array();
                $data['id'] = $detail->id;
                $data['reference_type'] = $detail->reference_type;
                $data['reference_name'] = isset($detail->types)?$detail->types->reference_type:'';
                $data['reference'] = $detail->reference;
                $data['amount'] = $detail->amount;
                $data['description'] = $detail->description;

                $invoice_details[$k+1] = $data;
            }
        }  
         
        return view('admin.finance.preview', compact('invoice','invoice_details','buildings','shares'));
    }

    public function testinvoicesend(Request $request, $id)
    {
        
        $input = $request->all();
        $account_id = Auth::user()->account_id;
        $invoice = FinanceInvoiceInfo::find($id);

        $login_id = Auth::user()->id;
        $sharesettings = FinanceShareSetting::where("account_id",$account_id)->where('status',1)->first();
        $due_days = 0;
        $invoice_date = $invoice->month;
        if(isset($sharesettings->due_period_type) && $sharesettings->due_period_type ==1){
            $due_period = $sharesettings->due_period_value;
            $due_date = date('Y-m-d', strtotime($invoice_date. " + $due_period days"));          
        }
        else if(isset($sharesettings->due_period_type) && $sharesettings->due_period_type ==2){
            $due_period = $sharesettings->due_period_value;
            $due_date = date('Y-m-d',strtotime("+$due_period months", strtotime($invoice_date))); // returns timestamp

        }
        $invoice->due_date = $due_date;
        $invoice->save();

        $propObj = Property::find($account_id);
        $ticket = new \App\Models\v7\FinanceInvoice();
        $count_batches = FinanceInvoiceInfo::where('account_id',$account_id)->whereBetween('created_at', [Carbon::today()->startOfMonth(),Carbon::today()->endOfMonth()])->count();
        $batch_file_no = date("Ym")."-".($count_batches);
        FinanceInvoiceInfo::where('id' , $invoice->id)->update( array( 'batch_no' => $batch_file_no));
        $info_id = $invoice->id;
        if(isset($input['units'])){
            foreach($input['units'] as $unit){
                $unitObj = Unit::find($unit);

                $PreviousInvoiceObj = FinanceInvoice::where('unit_no',$unit)->orderby('id','desc')->first();
                FinanceInvoice::where('unit_no' , $unit)->update( array( 'active_status' => 2)); //deactivate all previous 
                $batch_id = str_pad($info_id, 4, '0', STR_PAD_LEFT);
               
                $invoice_input['account_id'] = $account_id;
                $invoice_input['unit_no'] = $unit;
                $invoice_input['due_date'] = $due_date;
                $invoice_input['info_id'] = $info_id;
                $invoice_input['batch_file_no'] = $batch_file_no;
                $invoice_input['invoice_date'] = $invoice_date;
                $invoice_input['unit_share'] = $unitObj->share_amount;
                if($sharesettings->tax ==2){
                    $invoice_input['tax_percentage'] =$sharesettings->tax_percentage;
                }
                $invoiceObj = FinanceInvoice::create($invoice_input);

                $FinanceObj = new \App\Models\v7\FinanceInvoice();


                $DetailObj = FinanceInvoiceDetail::where('info_id',$info_id)->orderby('id','asc')->get();
                $pre_invoice_details = array();
                $pre_invoice_list = array();
                $pre_invoice_gst = array();
                $pre_interest = array();
                $invoice_id = $invoiceObj->id;
                $payable_amount =0;
                $interest_avilable = $sharesettings->interest; 
                $interest_percentage = ($sharesettings->interest==2)?$sharesettings->int_percentage:0;

                /********  For Previous month records start here ***********/
                if(isset($PreviousInvoiceObj)){
                    $set_new_due_date = date('Y-m-d', strtotime($invoice_date. " + 1 days"));   
                    $previous_details = FinanceInvoicePaymentDetail::where('unit_no',$unit)->where('status',0)->where('invoice_id',$PreviousInvoiceObj->id)->get();
                    if(isset($previous_details)){
                        foreach($previous_details as $previousdetail){
                           //print_r($previousdetail);
                           //echo "<hr/>";
                            if($previousdetail->payment_status ==2) // Full paid records
                            {
                                
                                $detail_input = array();
                                $detail_input['account_id'] = $previousdetail->account_id;
                                $detail_input['unit_no'] = $previousdetail->unit_no;
                                $detail_input['due_date'] = $set_new_due_date;
                                $detail_input['invoice_id'] = $invoice_id;
                                $detail_input['reference_invoice'] = $previousdetail->reference_invoice;
                                $detail_input['reference_type'] = $previousdetail->reference_type;
                                $detail_input['reference_no'] = $previousdetail->reference_no;
                                $detail_input['detail'] = $previousdetail->detail;
                                $detail_input['total_amount'] = $previousdetail->amount;
                                $detail_input['amount'] = $previousdetail->balance;
                                $detail_input['balance'] = $previousdetail->balance;
                                $detail_input['status'] = 1; //full paid
                                $detail_input['created_at'] = $previousdetail->created_at;
                                $detail_input['updated_at'] = $previousdetail->updated_at;
                                $detail_input['order'] =  $previousdetail->order;
                                $detail_input['display_order'] = $previousdetail->display_order;
                                $detail_input['paid_by_credit'] = ($previousdetail->paid_by_credit==1)?2:0;

                                $pre_invoice_list[] = $detail_input;
                                //echo "previousdetail->balance :".$previousdetail->balance;
                                if($previousdetail->balance >0)
                                    $payable_amount += $previousdetail->balance;
                                
                                //echo "1 " .$payable_amount;
                                $display_order = $previousdetail->display_order;
                                if($interest_avilable ==2)
                                {
                                    if($previousdetail->reference_type ==1 || $previousdetail->reference_type ==2){
                                        $paid_histories = FinanceInvoicePaymentPaidDetail::where('detail_id',$previousdetail->id)->orderby('id','asc')->get();
                                        if(isset($paid_histories)){
                                            $startdate = $previousdetail->due_date;
                                            $interest_applied_amount = $previousdetail->amount;
                                            foreach($paid_histories as $paid_history){
                                                $todate = $paid_history->payment_received_date;
                                                if($todate >  $startdate && $interest_applied_amount >0) {
                                                    $interest_amount = $FinanceObj->interest_calculation($interest_applied_amount, $interest_percentage,$startdate,$todate);
                                                    $start_date = date('d/m/Y',strtotime($startdate));
                                                    $end_date =  date('d/m/Y',strtotime($todate));
                                                    $interestamount = round($interest_amount,2);
                                                    $detail_input = array();
                                                    $detail_input['account_id'] = $previousdetail->account_id;
                                                    $detail_input['unit_no'] = $previousdetail->unit_no;
                                                    $detail_input['due_date'] = $due_date;
                                                    $detail_input['invoice_id'] = $invoice_id;
                                                    $detail_input['reference_type'] = 3;
                                                    $detail_input['reference_no'] = $previousdetail->reference_no;
                                                    $detail_input['order'] = $previousdetail->order +3;
                                                    $detail_input['display_order'] = $display_order;
                                                    $detail_input['reference_invoice'] = $invoice_id;
                                                    $detail_input['detail'] = "Interest of ".$previousdetail->detail." (S$".$interest_applied_amount .", " .$start_date." - ". $end_date.")";
                                                    $detail_input['total_amount'] = $interestamount;
                                                    $detail_input['amount'] = $interestamount;
                                                    $detail_input['balance'] = $interestamount;
                                                    $detail_input['status'] = 0;
                                                    $detail_input['paid_by_credit'] = 0;
                                                    $detail_input['created_at'] = date("y-m-d H:i:s");
                                                    $detail_input['updated_at'] = date("y-m-d H:i:s");
                                                    $pre_interest[] = $detail_input;
                                                    $payable_amount += $interestamount;
                                                    //echo "<br /> Status 2";
                                                    //echo "<br />  Payable amount :".$payable_amount;
                                                    // reinitaite the values for next interest calculation
                                                    $startdate = date('Y-m-d', strtotime($todate. ' + 1 days'));; 
                                                    $interest_applied_amount = $previousdetail->amount - $paid_history->amount;
                                                }
                                                else{
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            else if($previousdetail->payment_status ==3) //Partially Paid
                            {
                                $detail_input = array();
                                $detail_input['account_id'] = $previousdetail->account_id;
                                $detail_input['unit_no'] = $previousdetail->unit_no;
                                $detail_input['due_date'] = $set_new_due_date;
                                $detail_input['invoice_id'] = $invoice_id;
                                $detail_input['reference_invoice'] = $previousdetail->reference_invoice;
                                $detail_input['reference_type'] = $previousdetail->reference_type;
                                $detail_input['reference_no'] = $previousdetail->reference_no;
                                $detail_input['detail'] = $previousdetail->detail;
                                $detail_input['total_amount'] = $previousdetail->amount;
                                $detail_input['amount'] = $previousdetail->balance;
                                $detail_input['balance'] = $previousdetail->balance;
                                $detail_input['status'] = 0;
                                $detail_input['paid_by_credit'] = ($previousdetail->paid_by_credit==1)?2:0;
                                $detail_input['created_at'] = $previousdetail->created_at;
                                $detail_input['updated_at'] = $previousdetail->updated_at;
                                $detail_input['order'] =  $previousdetail->order;
                                $detail_input['display_order'] = $previousdetail->display_order;
                                $pre_invoice_list[] = $detail_input;
                                $payable_amount +=$previousdetail->balance;
                                $display_order = $previousdetail->display_order;
                                if($interest_avilable ==2)
                                {
                                    if($previousdetail->reference_type ==1 || $previousdetail->reference_type ==2){
                                        $paid_histories = FinanceInvoicePaymentPaidDetail::where('detail_id',$previousdetail->id)->orderby('id','asc')->get();
                                        if(isset($paid_histories)){
                                            $startdate = $previousdetail->due_date;
                                            $interest_applied_amount = $previousdetail->amount;
                                            foreach($paid_histories as $paid_history){
                                                $todate = $paid_history->payment_received_date;
                                                if($todate >  $startdate && $interest_applied_amount >0) {
                                                    $interest_amount = $FinanceObj->interest_calculation($interest_applied_amount, $interest_percentage,$startdate,$todate);
                                                    $start_date = date('d/m/Y',strtotime($startdate));
                                                    $end_date =  date('d/m/Y',strtotime($todate));
                                                    $interestamount = round($interest_amount,2);
                                                    $detail_input = array();
                                                    $detail_input['account_id'] = $previousdetail->account_id;
                                                    $detail_input['unit_no'] = $previousdetail->unit_no;
                                                    $detail_input['due_date'] = $due_date;
                                                    $detail_input['invoice_id'] = $invoice_id;
                                                    $detail_input['reference_type'] = 3;
                                                    $detail_input['reference_no'] = $previousdetail->reference_no;
                                                    $detail_input['order'] = $previousdetail->order +3;
                                                    $detail_input['display_order'] = $display_order;;
                                                    $detail_input['reference_invoice'] = $invoice_id;
                                                    $detail_input['detail'] = "Interest of ".$previousdetail->detail." (S$".$interest_applied_amount .", " .$start_date." - ". $end_date.")";
                                                    $detail_input['total_amount'] = $interestamount;
                                                    $detail_input['amount'] = $interestamount;
                                                    $detail_input['balance'] = $interestamount;
                                                    $detail_input['status'] = 0;
                                                    $detail_input['paid_by_credit'] = 0;

                                                    $detail_input['created_at'] = date("y-m-d H:i:s");
                                                    $detail_input['updated_at'] = date("y-m-d H:i:s");
                                                    $pre_interest[] = $detail_input;
                                                    $payable_amount += $interestamount;
                                                    // reinitaite the values for next interest calculation
                                                    $startdate = date('Y-m-d', strtotime($todate. ' + 1 days')); 
                                                    $interest_applied_amount = $previousdetail->amount - $paid_history->amount;
                                                }
                                                else{
                                                    break;
                                                }
                                            }
                                        }
                                        if($previousdetail->balance >0) {
                                            $interest_amount = $FinanceObj->interest_calculation($previousdetail->balance, $interest_percentage,$startdate,$invoice_date);
                                            $start_date = date('d/m/Y',strtotime($startdate));
                                            $end_date =  date('d/m/Y',strtotime($invoice_date));
                                            $interestamount = round($interest_amount,2);
                                            $detail_input = array();
                                            $detail_input['account_id'] = $previousdetail->account_id;
                                            $detail_input['unit_no'] = $previousdetail->unit_no;
                                            $detail_input['due_date'] = $due_date;
                                            $detail_input['invoice_id'] = $invoice_id;
                                            $detail_input['reference_type'] = 3;
                                            $detail_input['reference_no'] = $previousdetail->reference_no;
                                            $detail_input['order'] = $previousdetail->order +3;
                                            $detail_input['display_order'] = $display_order;;
                                            $detail_input['reference_invoice'] = $invoice_id;
                                            $detail_input['detail'] = "Interest of ".$previousdetail->detail." (S$".$previousdetail->balance .", " .$start_date." - ". $end_date.")";
                                            $detail_input['total_amount'] = $interestamount;
                                            $detail_input['amount'] = $interestamount;
                                            $detail_input['balance'] = $interestamount;
                                            $detail_input['status'] = 0;
                                            $detail_input['paid_by_credit'] = ($previousdetail->paid_by_credit==1)?2:0;
                                            $detail_input['created_at'] = date("y-m-d H:i:s");
                                            $detail_input['updated_at'] = date("y-m-d H:i:s");
                                            $pre_interest[] = $detail_input;
                                            $payable_amount += $interestamount;
                                        }

                                    }
                                }
                            }
                            else // not Paid
                            {
                                $detail_input = array();
                                $detail_input['account_id'] = $previousdetail->account_id;
                                $detail_input['unit_no'] = $previousdetail->unit_no;
                                $detail_input['due_date'] = $set_new_due_date;
                                $detail_input['invoice_id'] = $invoice_id;
                                $detail_input['reference_invoice'] = $previousdetail->reference_invoice;
                                $detail_input['reference_type'] = $previousdetail->reference_type;
                                $detail_input['reference_no'] = $previousdetail->reference_no;
                                $detail_input['detail'] = $previousdetail->detail;
                                $detail_input['total_amount'] = $previousdetail->amount;
                                $detail_input['amount'] = $previousdetail->balance;
                                $detail_input['balance'] = $previousdetail->balance;
                                $detail_input['status'] = 0;
                                $detail_input['paid_by_credit'] = ($previousdetail->paid_by_credit==1)?2:0;
                                $detail_input['created_at'] = $previousdetail->created_at;
                                $detail_input['updated_at'] = $previousdetail->updated_at;
                                $detail_input['order'] =  $previousdetail->order;
                                $detail_input['display_order'] = $previousdetail->display_order;
                                $pre_invoice_list[] = $detail_input;
                                $payable_amount +=$previousdetail->balance;
                                if($interest_avilable ==2 && $previousdetail->balance >0)
                                {
                                    if($previousdetail->reference_type ==1 || $previousdetail->reference_type ==2){
                                        $interest_amount = $FinanceObj->interest_calculation($previousdetail->balance, $interest_percentage,$previousdetail->due_date,$invoice_date);
                                        $start_date = date('d/m/Y',strtotime($previousdetail->due_date));
                                        $end_date =  date('d/m/Y',strtotime($invoice_date));
                                        $interestamount = round($interest_amount,2);
                                        $detail_input = array();
                                        $detail_input['account_id'] = $previousdetail->account_id;
                                        $detail_input['unit_no'] = $previousdetail->unit_no;
                                        $detail_input['due_date'] = $due_date;
                                        $detail_input['invoice_id'] = $invoice_id;
                                        $detail_input['reference_type'] = 3;
                                        $detail_input['reference_no'] = $previousdetail->reference_no;
                                        $detail_input['order'] = $previousdetail->order +3;
                                        $detail_input['display_order'] = $previousdetail->display_order;
                                        $detail_input['reference_invoice'] = $invoice_id;
                                        $detail_input['detail'] = "Interest of ".$previousdetail->detail." (S$".$previousdetail->balance .", " .$start_date." - ". $end_date.")";
                                        $detail_input['total_amount'] = $interestamount;
                                        $detail_input['amount'] = $interestamount;
                                        $detail_input['balance'] = $interestamount;
                                        $detail_input['status'] = 0;
                                        $detail_input['paid_by_credit'] = 0;

                                        $detail_input['created_at'] = date("y-m-d H:i:s");
                                        $detail_input['updated_at'] = date("y-m-d H:i:s");
                                        $pre_interest[] = $detail_input;
                                        $payable_amount += $interestamount;
                                    }
                                }
                            }
                        }
                        //exit;
                        $pre_invoice_details =  array_merge($pre_invoice_list, $pre_interest);                  
                        FinanceInvoicePaymentDetail::insert($pre_invoice_details);
                    }
                }
              
                //echo "<br /> last ". $payable_amount;
                //exit; 
                /********  For Current month records start here ***********/
                $invoice_details = array();
                $invoice_list = array();
                $invoice_gst = array();
                $order = 1;
                $reference_type_array = array(1,2);
                if(isset($DetailObj)){            
                    foreach($DetailObj as $detail){
                        $detail_input = array();
                       
                        if(in_array($detail->reference_type,$reference_type_array))
                            $amount = (($detail->tot_amount *  $unitObj->share_amount) * $sharesettings->no_of_billing_month);
                        else
                            $amount = $detail->tot_amount;
                        /*echo "Tot :" .$detail->tot_amount;
                        echo "<br />Share :" .$unitObj->share_amount;
                        echo "<br />Month :" .$sharesettings->no_of_billing_month;
                        echo "<br />Final Amount :" .$amount;
                        echo "<hr />";*/

                        $detail_input['account_id'] = $account_id;
                        $detail_input['unit_no'] = $unit;
                        $detail_input['due_date'] = $due_date;
                        $detail_input['invoice_id'] = $invoice_id;
                        $detail_input['reference_invoice'] = $invoice_id;
                        $detail_input['reference_type'] = $detail->types->id;
                        $detail_input['reference_no'] = $detail->reference;
                        $detail_input['detail'] = $detail->description;
                        $detail_input['total_amount'] = $amount;
                        $detail_input['amount'] = $amount;
                        $detail_input['balance'] = $amount;
                        $detail_input['status'] = 0;
                        $detail_input['created_at'] = date("y-m-d H:i:s");
                        $detail_input['updated_at'] = date("y-m-d H:i:s");
                        $detail_input['order'] = $order;
                        $detail_input['display_order'] = $invoice_id;
                        $detail_input['paid_by_credit'] = 0;

                        $invoice_list[] = $detail_input;
                        //echo "amount :".$amount;
                        $payable_amount +=$amount;

                        if($sharesettings->tax ==2){
                            //echo "here";
                            $input['tax_percentage'] =$sharesettings->tax_percentage;
                            $tax_amount  = ($amount/100) * $sharesettings->tax_percentage;
                            $detail_input = array();
                            $detail_input['account_id'] = $account_id;
                            $detail_input['unit_no'] = $unit;
                            $detail_input['due_date'] = $due_date;
                            $detail_input['invoice_id'] = $invoice_id;
                            $detail_input['reference_invoice'] = $invoice_id;
                            $detail_input['reference_type'] = 4;
                            $detail_input['reference_no'] = $detail->reference;
                            $detail_input['order'] = $order+2;
                            $detail_input['display_order'] = $invoice_id;
                            $detail_input['reference_invoice'] = $invoice_id;
                            $detail_input['detail'] = "GST ".$sharesettings->tax_percentage ."% - ".$detail->description." (S$".$amount.")";
                            $detail_input['total_amount'] = $tax_amount;
                            $detail_input['amount'] = $tax_amount;
                            $detail_input['balance'] = $tax_amount;
                            $detail_input['status'] = 0;
                            $detail_input['paid_by_credit'] = 0;

                            $detail_input['created_at'] = date("y-m-d H:i:s");
                            $detail_input['updated_at'] = date("y-m-d H:i:s");
                            $invoice_gst[] = $detail_input;
                            $payable_amount += $tax_amount;

                        }
                        $order++;
                    }


                    $invoice_details =  array_merge($invoice_list, $invoice_gst);
                    FinanceInvoicePaymentDetail::insert($invoice_details);
                }
                //echo "payable_amount ".$payable_amount ;
                //exit;

                $auto_code =$ticket->ticketgen($propObj->short_code)."-".str_pad($invoice_id, 5, '0', STR_PAD_LEFT);
                FinanceInvoice::where('id' , $invoice_id)->update( array('invoice_no' => $auto_code,'invoice_amount' =>$payable_amount, 'payable_amount' => $payable_amount,'balance_amount' => $payable_amount));

                /**** Excess payment auto payment start */
                if(isset($PreviousInvoiceObj)){
                    $invoiceObj = FinanceInvoice::find($PreviousInvoiceObj->id);
                    if($invoiceObj->balance_type ==2 && $invoiceObj->balance_amount > 0){
                        $payment = array();
                        $payment['invoice_id'] = $invoice_id;
                        $payment['manager_received'] = $login_id;
                        $payment['payment_option'] = 4;
                        $payment['cash_amount_received'] = $invoiceObj->balance_amount;
                        $payment['cash_received_date'] = $invoiceObj->invoice_date;
                        $payment['payment_received_date'] = $invoiceObj->invoice_date;
                        $payment['created_at'] = date("Y-m-d H:i:s");
                        $payment['updated_at'] = date("Y-m-d H:i:s");
                        $paymentObj = FinanceInvoicePayment::create($payment);

                        /** Insert Advance Payment Record START **/
                        $advData['account_id'] = $account_id;
                        $advData['unit_no'] = $invoiceObj->unit_no;
                        $advData['invoice_id'] = $paymentObj->invoice_id;
                        $advData['payment_id'] = $paymentObj->id;
                        $advData['amount'] = $invoiceObj->balance_amount;  
                        $advData['payment_received_date']= date("Y-m-d H:i:s"); 
                        $advpaymentObj = FinanceAdvancePayment::create($advData);

                        $advance_payment = $invoiceObj->balance_amount;  

                        /** Insert Advance Payment Record END **/

                        $inital_excess_amount = $invoiceObj->balance_amount;
                        $after_interest_amount_balance = 0;
                        $after_tax_amount_balance = 0;
                        $after_mf_amount_balance = 0;
                        $after_sf_amount_balance = 0;

                        /** Auto Payment for Interest START */
                        $paid_array = array();
                        $int_records = FinanceInvoicePaymentDetail::where('invoice_id',$invoice_id)->where('reference_type',3)->where('balance','>',0)->orderby('id','asc')->get();
                        if(isset($int_records)){
                            $excess_amount =  $inital_excess_amount;
                            foreach($int_records as $k => $int_record)
                            {
                                if($excess_amount >0 ){
                                    if($int_record->total_amount <= $excess_amount)
                                    {
                                        $amount_of_paid = $int_record->total_amount; 
                                        $excess_amount =  $excess_amount - $int_record->total_amount;
                                    }
                                    else{
                                        $amount_of_paid = $excess_amount; 
                                        $excess_amount = 0;
                                    }
                                   
                                    
                                    $PaymentdetailData =array();
                                    $PaymentdetailData['account_id'] = $account_id;
                                    $PaymentdetailData['unit_no'] = $paymentObj->invoice_id;
                                    $PaymentdetailData['invoice_id'] = $paymentObj->invoice_id;
                                    $PaymentdetailData['payment_id'] = $paymentObj->id;
                                    $PaymentdetailData['detail_id'] = $int_record->id;
                                    $PaymentdetailData['type'] = $int_record->reference_type;
                                    $PaymentdetailData['amount'] = $amount_of_paid;                        
                                    $PaymentdetailData['payment_received_date'] = $paymentObj->payment_received_date;
                                    $PaymentdetailData['created_at'] = date("Y-m-d H:i:s");
                                    $PaymentdetailData['updated_at'] = date("Y-m-d H:i:s");
                                    $paid_array[] = $PaymentdetailData;
                        
                                    $paid_amount = $amount_of_paid;
                                    $detail_record = FinanceInvoicePaymentDetail::find($int_record->id);
                                    if(isset($detail_record->paymenthistory)){
                                        foreach($detail_record->paymenthistory as $record){
                                            $paid_amount += $record->amount; 
                                        }
                                    }
                                    $detail_balance_amount = ($detail_record->amount - $paid_amount);
                                    if($detail_balance_amount<=0)
                                        $payment_status = 2;
                                    else
                                        $payment_status = 3;
            
                                    FinanceInvoicePaymentDetail::where('id' , $int_record->id)->update( array('balance' => $detail_balance_amount,'payment_status'=>$payment_status,'payment_received_date'=>$paymentObj->payment_received_date));
                                }
                            }
                            FinanceInvoicePaymentPaidDetail::insert($paid_array);
                            $after_interest_amount_balance =  $excess_amount;
                        }
                        /** Auto Payment for Interest END */

                        /** Auto Payment for TAX START */
                        if($after_interest_amount_balance >0){
                            $paid_array = array();
                            $int_records = FinanceInvoicePaymentDetail::where('invoice_id',$invoice_id)->where('reference_type',4)->where('balance','>',0)->orderby('id','asc')->get();
                           
                            if(isset($int_records)){
                                $excess_amount =  $after_interest_amount_balance;
                                foreach($int_records as $k => $int_record)
                                {
                                    if($excess_amount >0 ){
                                        if($int_record->total_amount <= $excess_amount)
                                        {
                                            $amount_of_paid = $int_record->total_amount; 
                                            $excess_amount =  $excess_amount - $int_record->total_amount;
                                        }
                                        else{
                                            $amount_of_paid = $excess_amount; 
                                            $excess_amount = 0;
                                        }
                                        $PaymentdetailData =array();
                                        $PaymentdetailData['account_id'] = $account_id;
                                        $PaymentdetailData['unit_no'] = $paymentObj->invoice_id;
                                        $PaymentdetailData['invoice_id'] = $paymentObj->invoice_id;
                                        $PaymentdetailData['payment_id'] = $paymentObj->id;
                                        $PaymentdetailData['detail_id'] = $int_record->id;
                                        $PaymentdetailData['type'] = $int_record->reference_type;
                                        $PaymentdetailData['amount'] = $amount_of_paid;                        
                                        $PaymentdetailData['payment_received_date'] = $paymentObj->payment_received_date;
                                        $PaymentdetailData['created_at'] = date("Y-m-d H:i:s");
                                        $PaymentdetailData['updated_at'] = date("Y-m-d H:i:s");
                                        $paid_array[] = $PaymentdetailData;
                            
                                        $paid_amount = $amount_of_paid;
                                        $detail_record = FinanceInvoicePaymentDetail::find($int_record->id);
                                        if(isset($detail_record->paymenthistory)){
                                            foreach($detail_record->paymenthistory as $record){
                                                $paid_amount += $record->amount; 
                                            }
                                        }
                                        $detail_balance_amount = ($detail_record->amount - $paid_amount);
                                        if($detail_balance_amount<=0)
                                            $payment_status = 2;
                                        else
                                            $payment_status = 3;
                
                                        FinanceInvoicePaymentDetail::where('id' , $int_record->id)->update( array('balance' => $detail_balance_amount,'payment_status'=>$payment_status,'payment_received_date'=>$paymentObj->payment_received_date));
                                    }
                                }
                                FinanceInvoicePaymentPaidDetail::insert($paid_array);
                                $after_tax_amount_balance =  $excess_amount;
                            }
                        }
                        /** Auto Payment for Tax END */

                        /** Auto Payment for MF START */
                        if($after_tax_amount_balance >0){
                            $paid_array = array();
                            $int_records = FinanceInvoicePaymentDetail::where('invoice_id',$invoice_id)->where('reference_type',1)->where('balance','>',0)->orderby('id','asc')->get();
                            if(isset($int_records)){
                                $excess_amount =  $after_tax_amount_balance;
                                foreach($int_records as $k => $int_record)
                                {
                                    if($excess_amount >0 ){
                                        if($int_record->total_amount <= $excess_amount)
                                        {
                                            $amount_of_paid = $int_record->total_amount; 
                                            $excess_amount =  $excess_amount - $int_record->total_amount;
                                        }
                                        else{
                                            $amount_of_paid = $excess_amount; 
                                            $excess_amount = 0;
                                        }
                                       
                                        $PaymentdetailData =array();
                                        $PaymentdetailData['account_id'] = $account_id;
                                        $PaymentdetailData['unit_no'] = $paymentObj->invoice_id;
                                        $PaymentdetailData['invoice_id'] = $paymentObj->invoice_id;
                                        $PaymentdetailData['payment_id'] = $paymentObj->id;
                                        $PaymentdetailData['detail_id'] = $int_record->id;
                                        $PaymentdetailData['type'] = $int_record->reference_type;
                                        $PaymentdetailData['amount'] = $amount_of_paid;                        
                                        $PaymentdetailData['payment_received_date'] = $paymentObj->payment_received_date;
                                        $PaymentdetailData['created_at'] = date("Y-m-d H:i:s");
                                        $PaymentdetailData['updated_at'] = date("Y-m-d H:i:s");
                                        $paid_array[] = $PaymentdetailData;
                            
                                        $paid_amount = $amount_of_paid;
                                        $detail_record = FinanceInvoicePaymentDetail::find($int_record->id);
                                        
                                        if(isset($detail_record->paymenthistory)){
                                            foreach($detail_record->paymenthistory as $record){
                                                $paid_amount += $record->amount; 
                                            }
                                        }
                                       
                                        $detail_balance_amount = ($detail_record->amount - $paid_amount);
                                        if($detail_balance_amount<=0)
                                            $payment_status = 2;
                                        else
                                            $payment_status = 3;
                                        
                                        FinanceInvoicePaymentDetail::where('id' , $int_record->id)->update( array('balance' => $detail_balance_amount,'payment_status'=>$payment_status,'payment_received_date'=>$paymentObj->payment_received_date));
                                    }
                                }
                                FinanceInvoicePaymentPaidDetail::insert($paid_array);
                                $after_mf_amount_balance =  $excess_amount;
                            }
                        }
                        /** Auto Payment for MF END */


                        /** Auto Payment for SF START */
                        if($after_mf_amount_balance >0){
                            $paid_array = array();
                            $int_records = FinanceInvoicePaymentDetail::where('invoice_id',$invoice_id)->where('reference_type',2)->where('balance','>',0)->orderby('id','asc')->get();
                            if(isset($int_records)){
                                $excess_amount =  $after_mf_amount_balance;
                                foreach($int_records as $k => $int_record)
                                {
                                    if($excess_amount >0 ){
                                        if($int_record->total_amount <= $excess_amount)
                                        {
                                            $amount_of_paid = $int_record->total_amount; 
                                            $excess_amount =  $excess_amount - $int_record->total_amount;
                                        }
                                        else{
                                            $amount_of_paid = $excess_amount; 
                                            $excess_amount = 0;
                                        }
                                      
                                        $PaymentdetailData =array();
                                        $PaymentdetailData['account_id'] = $account_id;
                                        $PaymentdetailData['unit_no'] = $paymentObj->invoice_id;
                                        $PaymentdetailData['invoice_id'] = $paymentObj->invoice_id;
                                        $PaymentdetailData['payment_id'] = $paymentObj->id;
                                        $PaymentdetailData['detail_id'] = $int_record->id;
                                        $PaymentdetailData['type'] = $int_record->reference_type;
                                        $PaymentdetailData['amount'] = $amount_of_paid;                        
                                        $PaymentdetailData['payment_received_date'] = $paymentObj->payment_received_date;
                                        $PaymentdetailData['created_at'] = date("Y-m-d H:i:s");
                                        $PaymentdetailData['updated_at'] = date("Y-m-d H:i:s");
                                        $paid_array[] = $PaymentdetailData;
                            
                                        $paid_amount = $amount_of_paid;
                                        $detail_record = FinanceInvoicePaymentDetail::find($int_record->id);
                                        if(isset($detail_record->paymenthistory)){
                                            foreach($detail_record->paymenthistory as $record){
                                                $paid_amount += $record->amount; 
                                            }
                                        }
                                        $detail_balance_amount = ($detail_record->amount - $paid_amount);
                                        if($detail_balance_amount<=0)
                                            $payment_status = 2;
                                        else
                                            $payment_status = 3;
                
                                        FinanceInvoicePaymentDetail::where('id' , $int_record->id)->update( array('balance' => $detail_balance_amount,'payment_status'=>$payment_status,'payment_received_date'=>$paymentObj->payment_received_date));
                                    }
                                }
                                FinanceInvoicePaymentPaidDetail::insert($paid_array);
                                $after_sf_amount_balance =  $excess_amount;
                            }
                        }
                        /** Auto Payment for SF END */


                        $invoiceObj = FinanceInvoice::find($invoice_id);
                        $amount_received =0;
                        if(isset($invoiceObj->payments)){
                            foreach($invoiceObj->payments as $k => $payment){
                                if($payment->payment_option ==1 && $payment->status !=2)
                                    $amount_received += $payment->cheque_amount; 
                                else if($payment->payment_option ==2)
                                    $amount_received += $payment->bt_amount_received;
                                else if($payment->payment_option ==5)
                                    $amount_received += $payment->online_amount_received;
                                else if($payment->payment_option ==6)
                                    $amount_received += $payment->credit_amount;
                                else if($payment->payment_option ==7)
                                    $amount_received += $payment->add_amt_received;
                                else
                                    $amount_received += $payment->cash_amount_received;
                            }
                        }

                        //echo 
                       
                        $balance_amount = $invoiceObj->payable_amount - $amount_received;
                        
                        if($balance_amount <=0){
                            if($balance_amount ==0)
                                $balnce_type =1;
                            else{
                                $balnce_type =2;
                                $balance_amount = 0- $balance_amount;
                            }
                            FinanceInvoice::where('id' , $invoice_id)->update( array('status' => 3,'balance_amount'=>$balance_amount,'balance_type'=>$balnce_type));
                        }
                        else{
                            FinanceInvoice::where('id' , $invoice_id)->update( array( 'status' => 2,'balance_amount'=>$balance_amount,'balance_type'=>1));
                        }
                    }
                    /** Excess payment auto payment end */


                }

                //$notification = new \App\Models\v7\FinanceInvoice();
				//$email = $notification->sendnotification($invoiceObj);
            }
            
        }else{
            return redirect("opslogin/invoicepreview/$id")->with('status', 'Unit not selected!');   
        }
       //exit;
        
        return redirect("opslogin/invoice")->with('status', 'Invoice has been created');  

    }


    public function invoicesend(Request $request, $id)
    {
        
        $input = $request->all();
        $account_id = Auth::user()->account_id;
        $invoice = FinanceInvoiceInfo::find($id);

        $login_id = Auth::user()->id;
        $sharesettings = FinanceShareSetting::where("account_id",$account_id)->where('status',1)->first();
        $due_days = 0;
        $invoice_date = $invoice->month;
        if(isset($sharesettings->due_period_type) && $sharesettings->due_period_type ==1){
            $due_period = $sharesettings->due_period_value;
            $due_date = date('Y-m-d', strtotime($invoice_date. " + $due_period days"));          
        }
        else if(isset($sharesettings->due_period_type) && $sharesettings->due_period_type ==2){
            $due_period = $sharesettings->due_period_value;
            $due_date = date('Y-m-d',strtotime("+$due_period months", strtotime($invoice_date))); // returns timestamp

        }
        $invoice->due_date = $due_date;
        $invoice->save();

        $propObj = Property::find($account_id);
        $ticket = new \App\Models\v7\FinanceInvoice();
        $count_batches = FinanceInvoiceInfo::where('account_id',$account_id)->whereBetween('created_at', [Carbon::today()->startOfMonth(),Carbon::today()->endOfMonth()])->count();
        $batch_file_no = date("Ym")."-".($count_batches);
        FinanceInvoiceInfo::where('id' , $invoice->id)->update( array( 'batch_no' => $batch_file_no));
        $info_id = $invoice->id;
        if(isset($input['units'])){
            foreach($input['units'] as $unit){
                $unitObj = Unit::find($unit);

                $PreviousInvoiceObj = FinanceInvoice::where('unit_no',$unit)->orderby('id','desc')->first();
                FinanceInvoice::where('unit_no' , $unit)->update( array( 'active_status' => 2)); //deactivate all previous 
                $batch_id = str_pad($info_id, 4, '0', STR_PAD_LEFT);
               
                $invoice_input['account_id'] = $account_id;
                $invoice_input['unit_no'] = $unit;
                $invoice_input['due_date'] = $due_date;
                $invoice_input['info_id'] = $info_id;
                $invoice_input['batch_file_no'] = $batch_file_no;
                $invoice_input['invoice_date'] = $invoice_date;
                $invoice_input['unit_share'] = $unitObj->share_amount;
                if($sharesettings->tax ==2){
                    $invoice_input['tax_percentage'] =$sharesettings->tax_percentage;
                }
                $invoiceObj = FinanceInvoice::create($invoice_input);

                $FinanceObj = new \App\Models\v7\FinanceInvoice();


                $DetailObj = FinanceInvoiceDetail::where('info_id',$info_id)->orderby('id','asc')->get();
                $pre_invoice_details = array();
                $pre_invoice_list = array();
                $pre_invoice_gst = array();
                $pre_interest = array();
                $invoice_id = $invoiceObj->id;
                $payable_amount =0;
                $interest_avilable = $sharesettings->interest; 
                $interest_percentage = ($sharesettings->interest==2)?$sharesettings->int_percentage:0;

                /********  For Previous month records start here ***********/
                if(isset($PreviousInvoiceObj)){
                    $set_new_due_date = date('Y-m-d', strtotime($invoice_date. " + 1 days"));   
                    $previous_details = FinanceInvoicePaymentDetail::where('unit_no',$unit)->where('status',0)->where('invoice_id',$PreviousInvoiceObj->id)->get();
                    if(isset($previous_details)){
                        foreach($previous_details as $previousdetail){
                            //print_r($previousdetail);
                            if($previousdetail->payment_status ==2) // Full paid records
                            {
                                
                                $detail_input = array();
                                $detail_input['account_id'] = $previousdetail->account_id;
                                $detail_input['unit_no'] = $previousdetail->unit_no;
                                $detail_input['due_date'] = $set_new_due_date;
                                $detail_input['invoice_id'] = $invoice_id;
                                $detail_input['reference_invoice'] = $previousdetail->reference_invoice;
                                $detail_input['reference_type'] = $previousdetail->reference_type;
                                $detail_input['reference_no'] = $previousdetail->reference_no;
                                $detail_input['detail'] = $previousdetail->detail;
                                $detail_input['total_amount'] = $previousdetail->amount;
                                $detail_input['amount'] = $previousdetail->balance;
                                $detail_input['balance'] = $previousdetail->balance;
                                $detail_input['status'] = 1; //full paid
                                $detail_input['created_at'] = $previousdetail->created_at;
                                $detail_input['updated_at'] = $previousdetail->updated_at;
                                $detail_input['order'] =  $previousdetail->order;
                                $detail_input['display_order'] = $previousdetail->display_order;
                                $detail_input['paid_by_credit'] = ($previousdetail->paid_by_credit==1)?2:0;

                                $pre_invoice_list[] = $detail_input;
                                //echo "previousdetail->balance :".$previousdetail->balance;
                                if($previousdetail->balance >0)
                                    $payable_amount +=$previousdetail->balance;

                                $display_order = $previousdetail->display_order;
                                if($interest_avilable ==2)
                                {
                                    if($previousdetail->reference_type ==1 || $previousdetail->reference_type ==2){
                                        $paid_histories = FinanceInvoicePaymentPaidDetail::where('detail_id',$previousdetail->id)->orderby('id','asc')->get();
                                        if(isset($paid_histories)){
                                            $startdate = $previousdetail->due_date;
                                            $interest_applied_amount = $previousdetail->amount;
                                            foreach($paid_histories as $paid_history){
                                                $todate = $paid_history->payment_received_date;
                                                if($todate >  $startdate && $interest_applied_amount >0) {
                                                    $interest_amount = $FinanceObj->interest_calculation($interest_applied_amount, $interest_percentage,$startdate,$todate);
                                                    $start_date = date('d/m/Y',strtotime($startdate));
                                                    $end_date =  date('d/m/Y',strtotime($todate));
                                                    $interestamount = round($interest_amount,2);
                                                    $detail_input = array();
                                                    $detail_input['account_id'] = $previousdetail->account_id;
                                                    $detail_input['unit_no'] = $previousdetail->unit_no;
                                                    $detail_input['due_date'] = $due_date;
                                                    $detail_input['invoice_id'] = $invoice_id;
                                                    $detail_input['reference_type'] = 3;
                                                    $detail_input['reference_no'] = $previousdetail->reference_no;
                                                    $detail_input['order'] = $previousdetail->order +3;
                                                    $detail_input['display_order'] = $display_order;
                                                    $detail_input['reference_invoice'] = $invoice_id;
                                                    $detail_input['detail'] = "Interest of ".$previousdetail->detail." (S$".$interest_applied_amount .", " .$start_date." - ". $end_date.")";
                                                    $detail_input['total_amount'] = $interestamount;
                                                    $detail_input['amount'] = $interestamount;
                                                    $detail_input['balance'] = $interestamount;
                                                    $detail_input['status'] = 0;
                                                    $detail_input['paid_by_credit'] = 0;
                                                    $detail_input['created_at'] = date("y-m-d H:i:s");
                                                    $detail_input['updated_at'] = date("y-m-d H:i:s");
                                                    $pre_interest[] = $detail_input;
                                                    $payable_amount += $interestamount;
                                                    //echo "<br /> Status 2";
                                                    //echo "<br />  Payable amount :".$payable_amount;
                                                    // reinitaite the values for next interest calculation
                                                    $startdate = date('Y-m-d', strtotime($todate. ' + 1 days'));; 
                                                    $interest_applied_amount = $previousdetail->amount - $paid_history->amount;
                                                }
                                                else{
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            else if($previousdetail->payment_status ==3) //Partially Paid
                            {
                                $detail_input = array();
                                $detail_input['account_id'] = $previousdetail->account_id;
                                $detail_input['unit_no'] = $previousdetail->unit_no;
                                $detail_input['due_date'] = $set_new_due_date;
                                $detail_input['invoice_id'] = $invoice_id;
                                $detail_input['reference_invoice'] = $previousdetail->reference_invoice;
                                $detail_input['reference_type'] = $previousdetail->reference_type;
                                $detail_input['reference_no'] = $previousdetail->reference_no;
                                $detail_input['detail'] = $previousdetail->detail;
                                $detail_input['total_amount'] = $previousdetail->amount;
                                $detail_input['amount'] = $previousdetail->balance;
                                $detail_input['balance'] = $previousdetail->balance;
                                $detail_input['status'] = 0;
                                $detail_input['paid_by_credit'] = ($previousdetail->paid_by_credit==1)?2:0;
                                $detail_input['created_at'] = $previousdetail->created_at;
                                $detail_input['updated_at'] = $previousdetail->updated_at;
                                $detail_input['order'] =  $previousdetail->order;
                                $detail_input['display_order'] = $previousdetail->display_order;
                                $pre_invoice_list[] = $detail_input;
                                $payable_amount +=$previousdetail->balance;
                                $display_order = $previousdetail->display_order;
                                if($interest_avilable ==2)
                                {
                                    if($previousdetail->reference_type ==1 || $previousdetail->reference_type ==2){
                                        $paid_histories = FinanceInvoicePaymentPaidDetail::where('detail_id',$previousdetail->id)->orderby('id','asc')->get();
                                        if(isset($paid_histories)){
                                            $startdate = $previousdetail->due_date;
                                            $interest_applied_amount = $previousdetail->amount;
                                            foreach($paid_histories as $paid_history){
                                                $todate = $paid_history->payment_received_date;
                                                if($todate >  $startdate && $interest_applied_amount >0) {
                                                    $interest_amount = $FinanceObj->interest_calculation($interest_applied_amount, $interest_percentage,$startdate,$todate);
                                                    $start_date = date('d/m/Y',strtotime($startdate));
                                                    $end_date =  date('d/m/Y',strtotime($todate));
                                                    $interestamount = round($interest_amount,2);
                                                    $detail_input = array();
                                                    $detail_input['account_id'] = $previousdetail->account_id;
                                                    $detail_input['unit_no'] = $previousdetail->unit_no;
                                                    $detail_input['due_date'] = $due_date;
                                                    $detail_input['invoice_id'] = $invoice_id;
                                                    $detail_input['reference_type'] = 3;
                                                    $detail_input['reference_no'] = $previousdetail->reference_no;
                                                    $detail_input['order'] = $previousdetail->order +3;
                                                    $detail_input['display_order'] = $display_order;;
                                                    $detail_input['reference_invoice'] = $invoice_id;
                                                    $detail_input['detail'] = "Interest of ".$previousdetail->detail." (S$".$interest_applied_amount .", " .$start_date." - ". $end_date.")";
                                                    $detail_input['total_amount'] = $interestamount;
                                                    $detail_input['amount'] = $interestamount;
                                                    $detail_input['balance'] = $interestamount;
                                                    $detail_input['status'] = 0;
                                                    $detail_input['paid_by_credit'] = 0;

                                                    $detail_input['created_at'] = date("y-m-d H:i:s");
                                                    $detail_input['updated_at'] = date("y-m-d H:i:s");
                                                    $pre_interest[] = $detail_input;
                                                    $payable_amount += $interestamount;
                                                    // reinitaite the values for next interest calculation
                                                    $startdate = date('Y-m-d', strtotime($todate. ' + 1 days')); 
                                                    $interest_applied_amount = $previousdetail->amount - $paid_history->amount;
                                                }
                                                else{
                                                    break;
                                                }
                                            }
                                        }
                                        if($previousdetail->balance >0) {
                                            $interest_amount = $FinanceObj->interest_calculation($previousdetail->balance, $interest_percentage,$startdate,$invoice_date);
                                            $start_date = date('d/m/Y',strtotime($startdate));
                                            $end_date =  date('d/m/Y',strtotime($invoice_date));
                                            $interestamount = round($interest_amount,2);
                                            $detail_input = array();
                                            $detail_input['account_id'] = $previousdetail->account_id;
                                            $detail_input['unit_no'] = $previousdetail->unit_no;
                                            $detail_input['due_date'] = $due_date;
                                            $detail_input['invoice_id'] = $invoice_id;
                                            $detail_input['reference_type'] = 3;
                                            $detail_input['reference_no'] = $previousdetail->reference_no;
                                            $detail_input['order'] = $previousdetail->order +3;
                                            $detail_input['display_order'] = $display_order;;
                                            $detail_input['reference_invoice'] = $invoice_id;
                                            $detail_input['detail'] = "Interest of ".$previousdetail->detail." (S$".$previousdetail->balance .", " .$start_date." - ". $end_date.")";
                                            $detail_input['total_amount'] = $interestamount;
                                            $detail_input['amount'] = $interestamount;
                                            $detail_input['balance'] = $interestamount;
                                            $detail_input['status'] = 0;
                                            $detail_input['paid_by_credit'] = ($previousdetail->paid_by_credit==1)?2:0;
                                            $detail_input['created_at'] = date("y-m-d H:i:s");
                                            $detail_input['updated_at'] = date("y-m-d H:i:s");
                                            $pre_interest[] = $detail_input;
                                            $payable_amount += $interestamount;
                                        }

                                    }
                                }
                            }
                            else // not Paid
                            {
                                $detail_input = array();
                                $detail_input['account_id'] = $previousdetail->account_id;
                                $detail_input['unit_no'] = $previousdetail->unit_no;
                                $detail_input['due_date'] = $set_new_due_date;
                                $detail_input['invoice_id'] = $invoice_id;
                                $detail_input['reference_invoice'] = $previousdetail->reference_invoice;
                                $detail_input['reference_type'] = $previousdetail->reference_type;
                                $detail_input['reference_no'] = $previousdetail->reference_no;
                                $detail_input['detail'] = $previousdetail->detail;
                                $detail_input['total_amount'] = $previousdetail->amount;
                                $detail_input['amount'] = $previousdetail->balance;
                                $detail_input['balance'] = $previousdetail->balance;
                                $detail_input['status'] = 0;
                                $detail_input['paid_by_credit'] = ($previousdetail->paid_by_credit==1)?2:0;
                                $detail_input['created_at'] = $previousdetail->created_at;
                                $detail_input['updated_at'] = $previousdetail->updated_at;
                                $detail_input['order'] =  $previousdetail->order;
                                $detail_input['display_order'] = $previousdetail->display_order;
                                $pre_invoice_list[] = $detail_input;
                                $payable_amount +=$previousdetail->balance;
                                if($interest_avilable ==2 && $previousdetail->balance >0)
                                {
                                    if($previousdetail->reference_type ==1 || $previousdetail->reference_type ==2){
                                        $interest_amount = $FinanceObj->interest_calculation($previousdetail->balance, $interest_percentage,$previousdetail->due_date,$invoice_date);
                                        $start_date = date('d/m/Y',strtotime($previousdetail->due_date));
                                        $end_date =  date('d/m/Y',strtotime($invoice_date));
                                        $interestamount = round($interest_amount,2);
                                        $detail_input = array();
                                        $detail_input['account_id'] = $previousdetail->account_id;
                                        $detail_input['unit_no'] = $previousdetail->unit_no;
                                        $detail_input['due_date'] = $due_date;
                                        $detail_input['invoice_id'] = $invoice_id;
                                        $detail_input['reference_type'] = 3;
                                        $detail_input['reference_no'] = $previousdetail->reference_no;
                                        $detail_input['order'] = $previousdetail->order +3;
                                        $detail_input['display_order'] = $previousdetail->display_order;
                                        $detail_input['reference_invoice'] = $invoice_id;
                                        $detail_input['detail'] = "Interest of ".$previousdetail->detail." (S$".$previousdetail->balance .", " .$start_date." - ". $end_date.")";
                                        $detail_input['total_amount'] = $interestamount;
                                        $detail_input['amount'] = $interestamount;
                                        $detail_input['balance'] = $interestamount;
                                        $detail_input['status'] = 0;
                                        $detail_input['paid_by_credit'] = 0;

                                        $detail_input['created_at'] = date("y-m-d H:i:s");
                                        $detail_input['updated_at'] = date("y-m-d H:i:s");
                                        $pre_interest[] = $detail_input;
                                        $payable_amount += $interestamount;
                                    }
                                }
                            }
                        }
                        //exit;
                        $pre_invoice_details =  array_merge($pre_invoice_list, $pre_interest);                  
                        FinanceInvoicePaymentDetail::insert($pre_invoice_details);
                    }
                }

                /********  For Current month records start here ***********/
                $invoice_details = array();
                $invoice_list = array();
                $invoice_gst = array();
                $order = 1;
                $reference_type_array = array(1,2);
                if(isset($DetailObj)){            
                    foreach($DetailObj as $detail){
                        $detail_input = array();
                       
                        if(in_array($detail->reference_type,$reference_type_array))
                            $amount = (($detail->tot_amount *  $unitObj->share_amount) * $sharesettings->no_of_billing_month);
                        else
                            $amount = $detail->tot_amount;
                        /*echo "Tot :" .$detail->tot_amount;
                        echo "<br />Share :" .$unitObj->share_amount;
                        echo "<br />Month :" .$sharesettings->no_of_billing_month;
                        echo "<br />Final Amount :" .$amount;
                        echo "<hr />";*/

                        $detail_input['account_id'] = $account_id;
                        $detail_input['unit_no'] = $unit;
                        $detail_input['due_date'] = $due_date;
                        $detail_input['invoice_id'] = $invoice_id;
                        $detail_input['reference_invoice'] = $invoice_id;
                        $detail_input['reference_type'] = $detail->types->id;
                        $detail_input['reference_no'] = $detail->reference;
                        $detail_input['detail'] = $detail->description;
                        $detail_input['total_amount'] = $amount;
                        $detail_input['amount'] = $amount;
                        $detail_input['balance'] = $amount;
                        $detail_input['status'] = 0;
                        $detail_input['created_at'] = date("y-m-d H:i:s");
                        $detail_input['updated_at'] = date("y-m-d H:i:s");
                        $detail_input['order'] = $order;
                        $detail_input['display_order'] = $invoice_id;
                        $detail_input['paid_by_credit'] = 0;

                        $invoice_list[] = $detail_input;
                        //echo "amount :".$amount;
                        $payable_amount +=$amount;

                        if($sharesettings->tax ==2){
                            //echo "here";
                            $input['tax_percentage'] =$sharesettings->tax_percentage;
                            $tax_amount  = ($amount/100) * $sharesettings->tax_percentage;
                            $detail_input = array();
                            $detail_input['account_id'] = $account_id;
                            $detail_input['unit_no'] = $unit;
                            $detail_input['due_date'] = $due_date;
                            $detail_input['invoice_id'] = $invoice_id;
                            $detail_input['reference_invoice'] = $invoice_id;
                            $detail_input['reference_type'] = 4;
                            $detail_input['reference_no'] = $detail->reference;
                            $detail_input['order'] = $order+2;
                            $detail_input['display_order'] = $invoice_id;
                            $detail_input['reference_invoice'] = $invoice_id;
                            $detail_input['detail'] = "GST ".$sharesettings->tax_percentage ."% - ".$detail->description." (S$".$amount.")";
                            $detail_input['total_amount'] = $tax_amount;
                            $detail_input['amount'] = $tax_amount;
                            $detail_input['balance'] = $tax_amount;
                            $detail_input['status'] = 0;
                            $detail_input['paid_by_credit'] = 0;

                            $detail_input['created_at'] = date("y-m-d H:i:s");
                            $detail_input['updated_at'] = date("y-m-d H:i:s");
                            $invoice_gst[] = $detail_input;
                            $payable_amount += $tax_amount;
                        }
                        $order++;
                    }


                    $invoice_details =  array_merge($invoice_list, $invoice_gst);
                    FinanceInvoicePaymentDetail::insert($invoice_details);
                }
                //echo "payable_amount ".$payable_amount ;

                $auto_code =$ticket->ticketgen($propObj->short_code)."-".str_pad($invoice_id, 5, '0', STR_PAD_LEFT);
                FinanceInvoice::where('id' , $invoice_id)->update( array('invoice_no' => $auto_code,'invoice_amount' =>$payable_amount, 'payable_amount' => $payable_amount,'balance_amount' => $payable_amount));

                /**** Excess payment auto payment start */
                if(isset($PreviousInvoiceObj)){
                    $invoiceObj = FinanceInvoice::find($PreviousInvoiceObj->id);
                    if($invoiceObj->balance_type ==2 && $invoiceObj->balance_amount > 0){
                        $payment = array();
                        $payment['invoice_id'] = $invoice_id;
                        $payment['manager_received'] = $login_id;
                        $payment['payment_option'] = 4;
                        $payment['cash_amount_received'] = $invoiceObj->balance_amount;
                        $payment['cash_received_date'] = $invoiceObj->invoice_date;
                        $payment['payment_received_date'] = $invoiceObj->invoice_date;
                        $payment['created_at'] = date("Y-m-d H:i:s");
                        $payment['updated_at'] = date("Y-m-d H:i:s");
                        $paymentObj = FinanceInvoicePayment::create($payment);

                        /** Insert Advance Payment Record START **/
                        $advData['account_id'] = $account_id;
                        $advData['unit_no'] = $invoiceObj->unit_no;
                        $advData['invoice_id'] = $paymentObj->invoice_id;
                        $advData['payment_id'] = $paymentObj->id;
                        $advData['amount'] = $invoiceObj->balance_amount;  
                        $advData['payment_received_date']= date("Y-m-d H:i:s"); 
                        $advpaymentObj = FinanceAdvancePayment::create($advData);

                        $advance_payment = $invoiceObj->balance_amount;  

                        /** Insert Advance Payment Record END **/

                        $inital_excess_amount = $invoiceObj->balance_amount;
                        $after_interest_amount_balance = 0;
                        $after_tax_amount_balance = 0;
                        $after_mf_amount_balance = 0;
                        $after_sf_amount_balance = 0;

                        /** Auto Payment for Interest START */
                        $paid_array = array();
                        $int_records = FinanceInvoicePaymentDetail::where('invoice_id',$invoice_id)->where('reference_type',3)->where('balance','>',0)->orderby('id','asc')->get();
                        if(isset($int_records)){
                            $excess_amount =  $inital_excess_amount;
                            foreach($int_records as $k => $int_record)
                            {
                                if($excess_amount >0 ){
                                    if($int_record->total_amount <= $excess_amount)
                                    {
                                        $amount_of_paid = $int_record->total_amount; 
                                        $excess_amount =  $excess_amount - $int_record->total_amount;
                                    }
                                    else{
                                        $amount_of_paid = $excess_amount; 
                                        $excess_amount = 0;
                                    }
                                   
                                    
                                    $PaymentdetailData =array();
                                    $PaymentdetailData['account_id'] = $account_id;
                                    $PaymentdetailData['unit_no'] = $paymentObj->invoice_id;
                                    $PaymentdetailData['invoice_id'] = $paymentObj->invoice_id;
                                    $PaymentdetailData['payment_id'] = $paymentObj->id;
                                    $PaymentdetailData['detail_id'] = $int_record->id;
                                    $PaymentdetailData['type'] = $int_record->reference_type;
                                    $PaymentdetailData['amount'] = $amount_of_paid;                        
                                    $PaymentdetailData['payment_received_date'] = $paymentObj->payment_received_date;
                                    $PaymentdetailData['created_at'] = date("Y-m-d H:i:s");
                                    $PaymentdetailData['updated_at'] = date("Y-m-d H:i:s");
                                    $paid_array[] = $PaymentdetailData;
                        
                                    $paid_amount = $amount_of_paid;
                                    $detail_record = FinanceInvoicePaymentDetail::find($int_record->id);
                                    if(isset($detail_record->paymenthistory)){
                                        foreach($detail_record->paymenthistory as $record){
                                            $paid_amount += $record->amount; 
                                        }
                                    }
                                    $detail_balance_amount = ($detail_record->amount - $paid_amount);
                                    if($detail_balance_amount<=0)
                                        $payment_status = 2;
                                    else
                                        $payment_status = 3;
            
                                    FinanceInvoicePaymentDetail::where('id' , $int_record->id)->update( array('balance' => $detail_balance_amount,'payment_status'=>$payment_status,'payment_received_date'=>$paymentObj->payment_received_date));
                                }
                            }
                            FinanceInvoicePaymentPaidDetail::insert($paid_array);
                            $after_interest_amount_balance =  $excess_amount;
                        }
                        /** Auto Payment for Interest END */

                        /** Auto Payment for TAX START */
                        if($after_interest_amount_balance >0){
                            $paid_array = array();
                            $int_records = FinanceInvoicePaymentDetail::where('invoice_id',$invoice_id)->where('reference_type',4)->where('balance','>',0)->orderby('id','asc')->get();
                           
                            if(isset($int_records)){
                                $excess_amount =  $after_interest_amount_balance;
                                foreach($int_records as $k => $int_record)
                                {
                                    if($excess_amount >0 ){
                                        if($int_record->total_amount <= $excess_amount)
                                        {
                                            $amount_of_paid = $int_record->total_amount; 
                                            $excess_amount =  $excess_amount - $int_record->total_amount;
                                        }
                                        else{
                                            $amount_of_paid = $excess_amount; 
                                            $excess_amount = 0;
                                        }
                                        $PaymentdetailData =array();
                                        $PaymentdetailData['account_id'] = $account_id;
                                        $PaymentdetailData['unit_no'] = $paymentObj->invoice_id;
                                        $PaymentdetailData['invoice_id'] = $paymentObj->invoice_id;
                                        $PaymentdetailData['payment_id'] = $paymentObj->id;
                                        $PaymentdetailData['detail_id'] = $int_record->id;
                                        $PaymentdetailData['type'] = $int_record->reference_type;
                                        $PaymentdetailData['amount'] = $amount_of_paid;                        
                                        $PaymentdetailData['payment_received_date'] = $paymentObj->payment_received_date;
                                        $PaymentdetailData['created_at'] = date("Y-m-d H:i:s");
                                        $PaymentdetailData['updated_at'] = date("Y-m-d H:i:s");
                                        $paid_array[] = $PaymentdetailData;
                            
                                        $paid_amount = $amount_of_paid;
                                        $detail_record = FinanceInvoicePaymentDetail::find($int_record->id);
                                        if(isset($detail_record->paymenthistory)){
                                            foreach($detail_record->paymenthistory as $record){
                                                $paid_amount += $record->amount; 
                                            }
                                        }
                                        $detail_balance_amount = ($detail_record->amount - $paid_amount);
                                        if($detail_balance_amount<=0)
                                            $payment_status = 2;
                                        else
                                            $payment_status = 3;
                
                                        FinanceInvoicePaymentDetail::where('id' , $int_record->id)->update( array('balance' => $detail_balance_amount,'payment_status'=>$payment_status,'payment_received_date'=>$paymentObj->payment_received_date));
                                    }
                                }
                                FinanceInvoicePaymentPaidDetail::insert($paid_array);
                                $after_tax_amount_balance =  $excess_amount;
                            }
                        }
                        /** Auto Payment for Tax END */

                        /** Auto Payment for MF START */
                        if($after_tax_amount_balance >0){
                            $paid_array = array();
                            $int_records = FinanceInvoicePaymentDetail::where('invoice_id',$invoice_id)->where('reference_type',1)->where('balance','>',0)->orderby('id','asc')->get();
                            if(isset($int_records)){
                                $excess_amount =  $after_tax_amount_balance;
                                foreach($int_records as $k => $int_record)
                                {
                                    if($excess_amount >0 ){
                                        if($int_record->total_amount <= $excess_amount)
                                        {
                                            $amount_of_paid = $int_record->total_amount; 
                                            $excess_amount =  $excess_amount - $int_record->total_amount;
                                        }
                                        else{
                                            $amount_of_paid = $excess_amount; 
                                            $excess_amount = 0;
                                        }
                                       
                                        $PaymentdetailData =array();
                                        $PaymentdetailData['account_id'] = $account_id;
                                        $PaymentdetailData['unit_no'] = $paymentObj->invoice_id;
                                        $PaymentdetailData['invoice_id'] = $paymentObj->invoice_id;
                                        $PaymentdetailData['payment_id'] = $paymentObj->id;
                                        $PaymentdetailData['detail_id'] = $int_record->id;
                                        $PaymentdetailData['type'] = $int_record->reference_type;
                                        $PaymentdetailData['amount'] = $amount_of_paid;                        
                                        $PaymentdetailData['payment_received_date'] = $paymentObj->payment_received_date;
                                        $PaymentdetailData['created_at'] = date("Y-m-d H:i:s");
                                        $PaymentdetailData['updated_at'] = date("Y-m-d H:i:s");
                                        $paid_array[] = $PaymentdetailData;
                            
                                        $paid_amount = $amount_of_paid;
                                        $detail_record = FinanceInvoicePaymentDetail::find($int_record->id);
                                        
                                        if(isset($detail_record->paymenthistory)){
                                            foreach($detail_record->paymenthistory as $record){
                                                $paid_amount += $record->amount; 
                                            }
                                        }
                                       
                                        $detail_balance_amount = ($detail_record->amount - $paid_amount);
                                        if($detail_balance_amount<=0)
                                            $payment_status = 2;
                                        else
                                            $payment_status = 3;
                                        
                                        FinanceInvoicePaymentDetail::where('id' , $int_record->id)->update( array('balance' => $detail_balance_amount,'payment_status'=>$payment_status,'payment_received_date'=>$paymentObj->payment_received_date));
                                    }
                                }
                                FinanceInvoicePaymentPaidDetail::insert($paid_array);
                                $after_mf_amount_balance =  $excess_amount;
                            }
                        }
                        /** Auto Payment for MF END */


                        /** Auto Payment for SF START */
                        if($after_mf_amount_balance >0){
                            $paid_array = array();
                            $int_records = FinanceInvoicePaymentDetail::where('invoice_id',$invoice_id)->where('reference_type',2)->where('balance','>',0)->orderby('id','asc')->get();
                            if(isset($int_records)){
                                $excess_amount =  $after_mf_amount_balance;
                                foreach($int_records as $k => $int_record)
                                {
                                    if($excess_amount >0 ){
                                        if($int_record->total_amount <= $excess_amount)
                                        {
                                            $amount_of_paid = $int_record->total_amount; 
                                            $excess_amount =  $excess_amount - $int_record->total_amount;
                                        }
                                        else{
                                            $amount_of_paid = $excess_amount; 
                                            $excess_amount = 0;
                                        }
                                      
                                        $PaymentdetailData =array();
                                        $PaymentdetailData['account_id'] = $account_id;
                                        $PaymentdetailData['unit_no'] = $paymentObj->invoice_id;
                                        $PaymentdetailData['invoice_id'] = $paymentObj->invoice_id;
                                        $PaymentdetailData['payment_id'] = $paymentObj->id;
                                        $PaymentdetailData['detail_id'] = $int_record->id;
                                        $PaymentdetailData['type'] = $int_record->reference_type;
                                        $PaymentdetailData['amount'] = $amount_of_paid;                        
                                        $PaymentdetailData['payment_received_date'] = $paymentObj->payment_received_date;
                                        $PaymentdetailData['created_at'] = date("Y-m-d H:i:s");
                                        $PaymentdetailData['updated_at'] = date("Y-m-d H:i:s");
                                        $paid_array[] = $PaymentdetailData;
                            
                                        $paid_amount = $amount_of_paid;
                                        $detail_record = FinanceInvoicePaymentDetail::find($int_record->id);
                                        if(isset($detail_record->paymenthistory)){
                                            foreach($detail_record->paymenthistory as $record){
                                                $paid_amount += $record->amount; 
                                            }
                                        }
                                        $detail_balance_amount = ($detail_record->amount - $paid_amount);
                                        if($detail_balance_amount<=0)
                                            $payment_status = 2;
                                        else
                                            $payment_status = 3;
                
                                        FinanceInvoicePaymentDetail::where('id' , $int_record->id)->update( array('balance' => $detail_balance_amount,'payment_status'=>$payment_status,'payment_received_date'=>$paymentObj->payment_received_date));
                                    }
                                }
                                FinanceInvoicePaymentPaidDetail::insert($paid_array);
                                $after_sf_amount_balance =  $excess_amount;
                            }
                        }
                        /** Auto Payment for SF END */


                        $invoiceObj = FinanceInvoice::find($invoice_id);
                        $amount_received =0;
                        if(isset($invoiceObj->payments)){
                            foreach($invoiceObj->payments as $k => $payment){
                                if($payment->payment_option ==1 && $payment->status !=2)
                                    $amount_received += $payment->cheque_amount; 
                                else if($payment->payment_option ==2)
                                    $amount_received += $payment->bt_amount_received;
                                else if($payment->payment_option ==5)
                                    $amount_received += $payment->online_amount_received;
                                else if($payment->payment_option ==6)
                                    $amount_received += $payment->credit_amount;
                                else if($payment->payment_option ==7)
                                    $amount_received += $payment->add_amt_received;
                                else
                                    $amount_received += $payment->cash_amount_received;
                            }
                        }

                        //echo 
                       
                        $balance_amount = $invoiceObj->payable_amount - $amount_received;
                        
                        if($balance_amount <=0){
                            if($balance_amount ==0)
                                $balnce_type =1;
                            else{
                                $balnce_type =2;
                                $balance_amount = 0- $balance_amount;
                            }
                            FinanceInvoice::where('id' , $invoice_id)->update( array('status' => 3,'balance_amount'=>$balance_amount,'balance_type'=>$balnce_type));
                        }
                        else{
                            FinanceInvoice::where('id' , $invoice_id)->update( array( 'status' => 2,'balance_amount'=>$balance_amount,'balance_type'=>1));
                        }
                    }
                    /** Excess payment auto payment end */


                }

                //$notification = new \App\Models\v7\FinanceInvoice();
				//$email = $notification->sendnotification($invoiceObj);
            }
            
        }else{
            return redirect("opslogin/invoicepreview/$id")->with('status', 'Unit not selected!');   
        }
       //exit;
        
        return redirect("opslogin/invoice")->with('status', 'Invoice has been created');  

    }

    public function viewinvoice($id)
    {
        $account_id = Auth::user()->account_id;
        $Unitinvoice = FinanceInvoice::find($id);
        $invoice = FinanceInvoiceInfo::find($Unitinvoice->info_id); 
        $currentDetails = FinanceInvoicePaymentDetail::where('invoice_id',$Unitinvoice->id)->where("reference_invoice",$Unitinvoice->id)->orderby('id','asc')->get();
		$CurrentInvoicePayments = FinanceInvoicePayment::where('invoice_id',$Unitinvoice->id)->orderby('id','asc')->get();
        $LastInvoice = FinanceInvoice::where('id','<',$id)->where('unit_no',$Unitinvoice->unit_no)->orderby('id','dsc')->first();
        //print_r($LastInvoice->id);
        if(isset($LastInvoice)){
            $LastInvoicePayments = FinanceInvoicePayment::where('invoice_id',$LastInvoice->id)->orderby('id','asc')->get();
            $previousDetails = FinanceInvoicePaymentDetail::where('invoice_id',$LastInvoice->id)->where("reference_invoice",'!=',$Unitinvoice->id)->orderby('id','asc')->get();
        }else{
            $LastInvoicePayments =array();
            $previousDetails = array();
        }
        $buildings = Building::where('account_id',$account_id)->get();
        $UserPurchaserRecords = UserPurchaserUnit::where('property_id', $account_id)->where('unit_id',$Unitinvoice->unit_no)->where('role_id',2)->where('status',1)->orderby('id','asc')->get();
        $UserPurchaserLists = array();
        if(isset($UserPurchaserRecords)){
            //echo "Purchaser contact";
            foreach($UserPurchaserRecords as $UserPurchaserRecord){
                $UserPurchaserLists[] = $UserPurchaserRecord->user_info_id;
            }
        }
        //print_r($UserPurchaserLists);
        $purchasers = UserMoreInfo::WhereIn('id',$UserPurchaserLists)->where('status',1)->orderby('id','asc')->get();
        //print_r($purchasers->first_name);
        $unitPrimaryContactRecs = UserPurchaserUnit::where('property_id', $account_id)->where('unit_id',$Unitinvoice->unit_no)->where('primary_contact',1)->where('role_id',2)->where('status',1)->orderby('id','asc')->get();
        $primayContactIds = array();
        if($unitPrimaryContactRecs){
			//echo "primary contact";
			foreach($unitPrimaryContactRecs as $unitPrimaryContactRec){
				$primayContactIds[] = $unitPrimaryContactRec->user_info_id;
			}
		}
		//print_r($primayContactIds);
        $primary_contact = UserMoreInfo::WhereIn('id',$primayContactIds)->where('status',1)->orderby('id','asc')->first();   
        
        $amount_received =0;
        if($Unitinvoice->payments){
            foreach($Unitinvoice->payments as $k => $payment){
                    if($payment->payment_option ==1 && $payment->status !=2)
                        $amount_received += $payment->cheque_amount; 
                    else if($payment->payment_option ==2)
                        $amount_received += $payment->bt_amount_received;
                    else if($payment->payment_option ==5)
                        $amount_received += $payment->online_amount_received;
                    else if($payment->payment_option ==6)
                        $amount_received += $payment->credit_amount;
                    else if($payment->payment_option ==7)
                        $amount_received += $payment->add_amt_received;
                    else
                        $amount_received += $payment->cash_amount_received;
                
            }
        }
       
        //echo "amount received:".$amount_received = number_format($amount_received,2);

		$balance_amount = ($Unitinvoice->payable_amount - $amount_received);

        return view('admin.finance.view', compact('Unitinvoice','buildings','purchasers','invoice','LastInvoice','primary_contact','LastInvoicePayments','previousDetails','currentDetails','CurrentInvoicePayments','balance_amount'));
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
        $account_id = Auth::user()->account_id;

        $InvoiceObj = FinanceInvoice::find($id);

        $purchasers = User::where('role_id',2)->where('unit_no',$InvoiceObj->unit_no)->orderby('id','asc')->get();   

        return view('admin.finance.edit', compact('InvoiceObj','purchasers'));
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
        $InvoiceObj = FinanceInvoice::find($id);

        $InvoiceObj->remarks = $input['remarks'];
        
        $InvoiceObj->save();

        if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$InvoiceObj->unit_no.'/13';
            return redirect($return_url)->with('status', 'Remarks has been updated!');
        }
        else if(Session::get('current_page') =='search_page'){
            return redirect(Session::get('search_url'))->with('status', 'Remarks has been updated!');
            //$return_url = 'opslogin/invoice_report';
        }
        else if(Session::get('current_page') =='invoice_report'){
            if(Session::get('page') >0){
                $page = Session::get('page');
                return redirect("opslogin/invoice_report?page=$page")->with('status', 'Remarks has been updated!');
            }
            else
                return redirect('opslogin/invoice_report#vm')->with('status', 'Remarks has been updated!');
            //$return_url = 'opslogin/invoice_report';
        }
        else{
            $return_url = 'opslogin/invoice/lists/'.$InvoiceObj->info_id."#vm";
            return redirect("$return_url")->with('status', 'Remarks has been updated!');
            
        }
            
        //return redirect('opslogin/invoice_report#vm')->with('status', 'Remarks has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\v7\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $InvoiceObj = FinanceInvoice::find($id);
        FinanceAdvancePayment::where('invoice_id', $id)->delete();
        FinanceCreditPayment::where('invoice_id', $id)->delete();
        FinanceInvoicePayment::where('invoice_id', $id)->delete();
        FinanceInvoicePaymentDetail::where('invoice_id', $id)->delete();
        FinanceInvoice::findOrFail($id)->delete();

        $last_invoice = FinanceInvoice::where('unit_no' , $InvoiceObj->unit_no)->orderby('id','desc')->first(); 
        if(isset($last_invoice)){
            FinanceInvoice::where('id' , $last_invoice->id)->update( array( 'active_status' => 1)); //activate all previous 

        }


        if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$InvoiceObj->unit_no.'/13';
            return redirect($return_url)->with('status', 'Invoice deleted successfully!');
        }
        else if(Session::get('current_page') =='search_page'){
            return redirect(Session::get('search_url'))->with('status', 'Invoice deleted successfully!');
            //$return_url = 'opslogin/invoice_report';
        }
        else if(Session::get('current_page') =='invoice_report'){
            if(Session::get('page') >0){
                $page = Session::get('page');
                return redirect("opslogin/invoice_report?page=$page")->with('status', 'Invoice(s) deleted successfully');
            }
            else
                return redirect('opslogin/invoice_report#vm')->with('status', 'Invoice deleted successfully!');
            //$return_url = 'opslogin/invoice_report';
        }
        else{
            $return_url = 'opslogin/invoice/lists/'.$InvoiceObj->info_id."#vm";
            return redirect("$return_url")->with('status', 'Invoice deleted successfully!');
            
        }

       /* else if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/invoice?page=$page#settings")->with('status', 'Invoice deleted successfully!');
        }
        else
            return redirect('opslogin/invoice#settings')->with('status', 'Invoice deleted successfully!');
            */
    }

    public function bulkdelete(Request $request)
    {
        $input = $request->all();
       
        if(isset($input['invoices'])){
            foreach($input['invoices'] as $id){
                $InvoiceObj = FinanceInvoice::find($id);
                FinanceAdvancePayment::where('invoice_id', $id)->delete();
                FinanceCreditPayment::where('invoice_id', $id)->delete();
                FinanceInvoicePayment::where('invoice_id', $id)->delete();
                FinanceInvoicePaymentDetail::where('invoice_id', $id)->delete();
                FinanceInvoice::findOrFail($id)->delete();

                $last_invoice = FinanceInvoice::where('unit_no' , $InvoiceObj->unit_no)->orderby('id','desc')->first(); 
                if(isset($last_invoice)){
                    FinanceInvoice::where('id' , $last_invoice->id)->update( array( 'active_status' => 1)); //activate all previous 
                }
            }
        }

        if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$InvoiceObj->unit_no.'/13';
            return redirect($return_url)->with('status', 'Invoice(s) deleted successfully!');
        }
        else if(Session::get('current_page') =='search_page'){
            return redirect(Session::get('search_url'))->with('status', 'Invoice(s) deleted successfully!');
            //$return_url = 'opslogin/invoice_report';
        }
        else if(Session::get('current_page') =='invoice_report'){
            if(Session::get('page') >0){
                $page = Session::get('page');
                return redirect("opslogin/invoice_report?page=$page")->with('status', 'Invoice(s) deleted successfully');
            }
            else
                return redirect('opslogin/invoice_report#vm')->with('status', 'Invoice(s) deleted successfully!');
            //$return_url = 'opslogin/invoice_report';
        }
        else{
            $return_url = 'opslogin/invoice/lists/'.$InvoiceObj->info_id."#vm";
            return redirect("$return_url")->with('status', 'Invoice(s) deleted successfully!');
            
        }

       /* else if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/invoice?page=$page#settings")->with('status', 'Invoice deleted successfully!');
        }
        else
            return redirect('opslogin/invoice#settings')->with('status', 'Invoice deleted successfully!');
            */
    }

    public function batchdelete($id)
    {
        
        $batchObj = FinanceInvoiceInfo::find($id);

        if(isset($batchObj->invoices)){
            foreach($batchObj->invoices as $invoice){
                FinanceAdvancePayment::where('invoice_id', $id)->delete();
                FinanceCreditPayment::where('invoice_id', $id)->delete();
                FinanceInvoicePayment::where('invoice_id', $invoice->id)->delete();
                FinanceInvoicePaymentDetail::where('invoice_id', $invoice->id)->delete();
                FinanceInvoicePaymentPaidDetail::where('invoice_id', $invoice->id)->delete();
                FinanceInvoice::findOrFail($invoice->id)->delete();

               $last_invoice = FinanceInvoice::where('unit_no' , $invoice->unit_no)->orderby('id','desc')->first(); 
                if(isset($last_invoice)){
                    FinanceInvoice::where('id' , $last_invoice->id)->update( array( 'active_status' => 1)); //activate all previous 
                }
            }
        }
        
        FinanceInvoiceDetail::where('info_id', $id)->delete();

        FinanceInvoiceInfo::findOrFail($id)->delete();

        if(Session::get('page') >0){
            $page = Session::get('page');
            return redirect("opslogin/invoice?page=$page#settings")->with('status', 'Invoice deleted successfully!');}
        else
            return redirect('opslogin/invoice#settings')->with('status', 'Invoice deleted successfully!');
    }


    public function search(Request $request){

        $q = $option =$unit = $status = $invoice_no = $batch_file_no = $users = $month ='';
        $account_id = Auth::user()->account_id;
        $info_id  =    $request->input('info_id');  

        $fromdate = $request->input('fromdate');
        if($request->input('todate') !='')
            $todate = $request->input('todate');
        else
            $todate =$request->input('fromdate');
        $batch_file_no = $request->input('batch_file_no');
        $invoice_no = $request->input('invoice_no');
        $unit = $request->input('unit');
        $units = array();
        if($unit !=''){   
            $unit = str_replace("#",'',$unit);
            $unitObj = Unit::select('id','unit')->where('account_id',$account_id)->where(function ($query) use ($unit) {
            })->get();   
            if(isset($unitObj)){
                foreach($unitObj as $unitid){
                    if(Crypt::decryptString($unitid->unit) ===$request->input('unit'))
                        $units[] = $unitid->id;
                }
            }
        } 
        $status = $request->input('status');

        $invoices =  FinanceInvoice::where('account_id',$account_id)->where('info_id',$info_id)->where(function($query) use ($batch_file_no,$invoice_no,$unit,$units, $month,$status){
            if($month !='')
                $query->where('created_at',$month);
            if($batch_file_no !='')
                $query->where('batch_file_no', 'LIKE', '%'.$batch_file_no .'%');
            if($unit !='')
                $query->whereIn('unit_no', $units);
            if($invoice_no !='')
                $query->where('invoice_no', 'LIKE', '%'.$invoice_no .'%');
            if($status !='')
                $query->where('status', $status);    
        })->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS'));


       /* if ($option != '') {

            if($option == 'month') {
					
                $month = $request->input('month');

                $invoices =  FinanceInvoice::where('account_id',$account_id)->where(function($query) use ($month){
                    if($month !=''){
                        $query->where('created_at',$month);
                    }
                
                })->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS'));
                
            }

            if($option == 'batch') {
                $batch_file_no = $request->input('batch_file_no');
                $invoices =  FinanceInvoice::where('account_id',$account_id)->where('batch_file_no', 'LIKE', '%'.$batch_file_no .'%')
                ->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
            }
            if($option == 'invoice') {
                $invoice_no = $request->input('invoice_no');
                $invoices =  FinanceInvoice::where('account_id',$account_id)->where('invoice_no', 'LIKE', '%'.$invoice_no .'%')
                ->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
            }

            if($option == 'unit' ) { 
                $unit = $request->input('unit');
                $unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->get();
                $units = array();
                if(isset($unitObj)){
                    foreach($unitObj as $unitid){
                        $units[] = $unitid->id;
                    }
                }

                $invoices =  FinanceInvoice::where('account_id',$account_id)->whereIn('unit_no', $units)
                    ->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
                
            }
            if($option == 'status') {
                $status = $request->input('status');
                $invoices =  FinanceInvoice::where('account_id',$account_id)->where('status', $status)
                    ->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
            }
        }*/
           
            $visitor_app_url = url('visitors');

             return view('admin.finance.invoices',compact('invoices','unit','status','invoice_no','batch_file_no','option','month','visitor_app_url','info_id'));
        
   }

   public function batchsearch(Request $request){

    $q = $option = $batch_file_no  = $month = $from_date = $to_date = '';
    $account_id = Auth::user()->account_id;
    $option  =    $request->input('option');  
    $month = $request->input('month');
    if($month !=''){
        $from_date = date('Y-m-01', strtotime($month));
        $to_date = date('Y-m-t', strtotime($month));
    }
    $batch_file_no = $request->input('batch_file_no');

    $invoices =  FinanceInvoiceInfo::where('account_id',$account_id)->where(function($query)  use ($from_date,$to_date,$batch_file_no){
                    
        if($from_date !='')
            $query->whereBetween('created_at',array($from_date,$to_date));
        
        if($batch_file_no !='')
            $query->where('batch_no', 'LIKE', '%'.$batch_file_no .'%');
    
    })->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS'));

    /*if ($option != '') {

        if($option == 'month') {
                
            $month = $request->input('month');
            $from_date = date('Y-m-01', strtotime($month));
            $to_date = date('Y-m-t', strtotime($month));

            //    $from_date = $month."-1";
            //    $to_date  = $month."-31";

            $invoices =  FinanceInvoiceInfo::where('account_id',$account_id)->where(function($query)  use ($from_date,$to_date){
                    
                if($from_date !=''){
                    $query->whereBetween('created_at',array($from_date,$to_date));
                }
            
            })->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS'));
            
        }

        if($option == 'batch') {
            $batch_file_no = $request->input('batch_file_no');
            $invoices =  FinanceInvoiceInfo::where('account_id',$account_id)->where('batch_no', 'LIKE', '%'.$batch_file_no .'%')
            ->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
        }
    }*/
        
       
        $visitor_app_url = url('visitors');

         return view('admin.finance.index',compact('invoices','batch_file_no','option','month','visitor_app_url'));
        
    
}


   public function payment($id)
    {
        //
        $invoiceObj = FinanceInvoice::find($id);
        $account_id = Auth::user()->account_id;
        $amount_received =0;
        if($invoiceObj->payments){
            foreach($invoiceObj->payments as $k => $payment){
                    if($payment->payment_option ==1 && $payment->status !=2)
                        $amount_received += $payment->cheque_amount; 
                    else if($payment->payment_option ==2)
                        $amount_received += $payment->bt_amount_received;
                    else if($payment->payment_option ==5)
                        $amount_received += $payment->online_amount_received;
                    else if($payment->payment_option ==6)
                        $amount_received += $payment->credit_amount;
                    else if($payment->payment_option ==7)
                        $amount_received += $payment->add_amt_received;
                    else
                        $amount_received += $payment->cash_amount_received;
                
            }
        }
        //$amount_received = number_format($amount_received,2);
        $balance_amount = ($invoiceObj->payable_amount - $amount_received);

        $invoiceLists = FinanceInvoice::where('unit_no',$invoiceObj->unit_no)->where('id','<',$id)->get();
        $invInIds = array();
        if($invoiceLists){
            foreach($invoiceLists as $k => $invoiceList){
                $invInIds[] = $invoiceList->id;
            }
        }

        

        $paymentHistory = FinanceInvoicePayment::whereIn('invoice_id',$invInIds)->get();

        if(Session::get('current_page') =='unit_summary'){
            $return_url = 'opslogin/configuration/unit_summary/'.$invoiceObj->unit_no.'/13';
        }
        else if(Session::get('current_page') =='invoice_report'){
            $return_url = 'opslogin/invoice_report';
        }
        else{
            $return_url = 'opslogin/invoice/lists/'.$invoiceObj->info_id;
        }

        $visitor_app_url = url('visitors');

        return view('admin.finance.payment', compact('invoiceObj','amount_received','balance_amount','return_url','paymentHistory','visitor_app_url'));
    }

    public function paymentsave(Request $request,$id)
    {
        //
      
        $invoiceObj = FinanceInvoice::find($id);
        $account_id = Auth::user()->account_id;
        $login_id = Auth::user()->id;
        $paid_by_credit = 0;

        /**********Validation Start ******/
        $payment_option = $request->input('payment_option');

        $val_inforecords = $request->input('info_detail');
        $val_amounts = $request->input('amount');
        $balance_amount = $request->input('balance_amount');
        $invoice_amount = $request->input('invoice_amount');
        $amount_received =0;
        if($payment_option ==1){
            if($request->input('cheque_amount') != null)
                $amount_received = $request->input('cheque_amount');
        }
        else if($payment_option ==2){
            if($request->input('bt_amount_received') != null)
                $amount_received = $request->input('bt_amount_received');
        }
        else if($payment_option ==3){
            if($request->input('cash_amount_received') != null)
                $amount_received = $request->input('cash_amount_received');
        }
        else if($payment_option ==7){
            if($request->input('add_amt_received') != null)
                $amount_received = $request->input('add_amt_received');
        }
        else if($payment_option ==6){
            if($request->input('credit_amount') != null)
                $amount_received = $request->input('credit_amount');
        }

       /* echo "Method :".$payment_option;
        echo "Received :".$amount_received;
        echo "Balance :".$balance_amount;
       */

        $full_payment = 0;
        if($amount_received >= $balance_amount && $payment_option ==6){
            $paid_by_credit = 1;
        }

        if($amount_received >= $balance_amount){
            $full_payment = 1;
        }
        else 
        {
            $totamount= 0;
            
            foreach($val_inforecords as $inforecord){
                if(isset($val_amounts[$inforecord]))
                    $totamount += $val_amounts[$inforecord];
            }
            $balance_amount = number_format($balance_amount, 2, '.', '');
            $amount_received = number_format($amount_received, 2, '.', '');
            $totamount = number_format($totamount, 2, '.', '');
            //echo "Amount :".$amount_received;
            //echo "Received :".$totamount;
            //echo "Balance :".$balance_amount;
            //exit; 
            
            

            if($amount_received > 0 && $totamount ==0){
                return redirect()->back()->withInput()->with('status', 'Error: Allocation amount should not be empty!');
                //return redirect("opslogin/invoice/payment/$id")->with('status', 'Error: Allocation amount should not be empty!.');         
            }
            else if($balance_amount ==$amount_received && $totamount !=$amount_received){
                return redirect()->back()->withInput()->with('status', 'Error: Amount and allocation amount does not match!!');
                //return redirect("opslogin/invoice/payment/$id")->with('status', 'Error: Amount and allocation amount not match.');         
            }
            else if($balance_amount < $amount_received && $totamount !=$invoice_amount){
                return redirect()->back()->withInput()->with('status', 'Error: Amount and allocation amount does not match!!!');
                //return redirect("opslogin/invoice/payment/$id")->with('status', 'Error: Amount and allocation amount not match.');   
            } 
            else if($balance_amount > $amount_received && $totamount != $amount_received){
                //echo "here";
                return redirect()->back()->withInput()->with('status', 'Error: Amount and allocation amount does not match!!!!');
                //return redirect("opslogin/invoice/payment/$id")->with('status', 'Error: Amount and allocation amount not match.');   
            }    
            /****** Validation End */
        }

        //exit;
        $payment = array();
		$payment['invoice_id'] = $id;
		$payment['manager_received'] = $login_id;
		$payment['payment_option'] = $request->input('payment_option');
		if($payment['payment_option'] ==1){
            if($request->input('cheque_amount') != null)
				$payment['cheque_amount'] = $request->input('cheque_amount');
            if($request->input('cheque_no') != null)
                $payment['cheque_no'] = $request->input('cheque_no');
            if($request->input('cheque_received_date') != null){
                $payment['cheque_received_date'] = $request->input('cheque_received_date');
                $payment['payment_received_date'] = $request->input('cheque_received_date');
            }
			if($request->input('cheque_bank') != null)
				$payment['cheque_bank'] = $request->input('cheque_bank');
		}
		if($payment['payment_option'] ==2){
			if($request->input('bt_received_date') != null){
                $payment['bt_received_date'] = $request->input('bt_received_date');
                $payment['payment_received_date'] = $request->input('bt_received_date');
            }
            if($request->input('bt_amount_received') != null)
				$payment['bt_amount_received'] = $request->input('bt_amount_received');
        }
        if($payment['payment_option'] ==3){
			if($request->input('cash_amount_received') != null)
                $payment['cash_amount_received'] = $request->input('cash_amount_received');
            if($request->input('cash_received_date') != null){
                $payment['cash_received_date'] = $request->input('cash_received_date');
                $payment['payment_received_date'] = $request->input('cash_received_date');
            }					
        }
		if($payment['payment_option'] ==7){
            if($request->input('add_amt_received_by') != null)
                $payment['add_amt_received_by'] = $request->input('add_amt_received_by');
			if($request->input('add_amt_received') != null)
                $payment['add_amt_received'] = $request->input('add_amt_received');
            if($request->input('add_amt_date') != null){
                $payment['add_amt_date'] = $request->input('add_amt_date');
                $payment['payment_received_date'] = $request->input('add_amt_date');
            }	
            if($request->input('add_amt_notes') != null){
                $payment['add_amt_notes'] = $request->input('add_amt_notes');
            }					
        }
        
        if($payment['payment_option'] ==6){
			if($request->input('credit_amount') != null)
                $payment['credit_amount'] = $request->input('credit_amount');
            if($request->input('credit_date') != null){
                $payment['credit_date'] = $request->input('credit_date');
                $payment['payment_received_date']  = $request->input('credit_date');
            }	
            if($request->input('credit_notes') != null){
                $payment['credit_notes'] = $request->input('credit_notes');
            }	
            
            $paid_by_credit = 1;				
		}
		if($request->input('receipt_no') != null)   
			$payment['receipt_no'] = $request->input('receipt_no');

		$payment['created_at'] = date("Y-m-d H:i:s");
        $payment['updated_at'] = date("Y-m-d H:i:s");
        //print($payment);

        $paymentObj = FinanceInvoicePayment::create($payment);


        if(isset($paymentObj) && $payment['payment_option'] ==6){
             /** Insert Advance Payment Record START **/
             $creditData['account_id'] = $account_id;
             $creditData['invoice_id'] = $paymentObj->invoice_id;
             $creditData['payment_id'] = $paymentObj->id;
             $creditData['credit_amount'] = $request->input('credit_amount');
             $creditData['received_date'] = $request->input('credit_date');
             $creditData['credit_notes'] = $request->input('credit_notes');
             $creditpaymentObj = FinanceCreditPayment::create($creditData);
        }
        if(isset($paymentObj) && $balance_amount > 0){                  
            if($request->input('info_detail') !=''){
                $info_records = $request->input('info_detail');
                $references = $request->input('reference');
                $amounts = $request->input('amount');
                $full_balance = $request->input('bal_amount');
                $waves = $request->input('wave');
                $paid_array = array();
                foreach($info_records as $inforecord){
                    if($amounts[$inforecord] >0 || $full_payment == 1){
                        if($full_payment == 1)
                            $amount_of_paid = $full_balance[$inforecord]; 
                        else
                            $amount_of_paid = $amounts[$inforecord];  

                        $PaymentdetailData =array();
                        $PaymentdetailData['account_id'] = $account_id;
                        $PaymentdetailData['unit_no'] = $invoiceObj->unit_no;
                        $PaymentdetailData['invoice_id'] = $paymentObj->invoice_id;
                        $PaymentdetailData['payment_id'] = $paymentObj->id;
                        $PaymentdetailData['detail_id'] = $inforecord;
                        $PaymentdetailData['type'] = $references[$inforecord];
                        $PaymentdetailData['amount'] = $amount_of_paid;
                        $PaymentdetailData['paid_by_credit'] = ($paid_by_credit ==1)?1:0;                        
                        $PaymentdetailData['payment_received_date'] = $paymentObj->payment_received_date;
                        $PaymentdetailData['created_at'] = date("Y-m-d H:i:s");
                        $PaymentdetailData['updated_at'] = date("Y-m-d H:i:s");
                        $paid_array[] = $PaymentdetailData;
            
                        $paid_amount = $amount_of_paid;
                        $detail_record = FinanceInvoicePaymentDetail::find($inforecord);
                        if(isset($detail_record->paymenthistory)){
                            foreach($detail_record->paymenthistory as $record){
                                $paid_amount += $record->amount; 
                            }
                        }
                        $detail_balance_amount = ($detail_record->amount - $paid_amount);
                        if($detail_balance_amount<=0)
                            $payment_status = 2;
                        else
                            $payment_status = 3;
                        
                        if(isset($waves[$inforecord]) && $waves[$inforecord] ==1 ){
                            $paid_by_waver = 1;
                        }else{
                            $paid_by_waver = 0;
                        }


                        FinanceInvoicePaymentDetail::where('id' , $inforecord)->update( array('balance' => $detail_balance_amount,'payment_status'=>$payment_status,'payment_received_date'=>$paymentObj->payment_received_date,'paid_by_credit'=>$paid_by_waver));

                    }
                }
               
                FinanceInvoicePaymentPaidDetail::insert($paid_array);
            }                
        }
       
        $invoiceObj = FinanceInvoice::find($invoiceObj->id);
        $amount_received =0;
        if(isset($invoiceObj->payments)){
            foreach($invoiceObj->payments as $k => $payment){
                if($payment->payment_option ==1 && $payment->status !=2)
                    $amount_received += $payment->cheque_amount; 
                else if($payment->payment_option ==2)
                    $amount_received += $payment->bt_amount_received;
                else if($payment->payment_option ==5)
                    $amount_received += $payment->online_amount_received;
                else if($payment->payment_option ==6)
                    $amount_received += $payment->credit_amount;
                else if($payment->payment_option ==7)
                    $amount_received += $payment->add_amt_received;
                else
                    $amount_received += $payment->cash_amount_received;
            }
        }
       
        $balance_amount = $invoiceObj->payable_amount - $amount_received;
       
        if($balance_amount <=0){
            if($balance_amount ==0)
                $balnce_type =1;
            else{
                $balnce_type =2;
                $balance_amount = 0- $balance_amount;
            }
            FinanceInvoice::where('id' , $id)->update( array('status' => 3,'balance_amount'=>$balance_amount,'balance_type'=>$balnce_type));
        }
        else{

            FinanceInvoice::where('id' , $id)->update( array( 'status' => 2,'balance_amount'=>$balance_amount,'balance_type'=>1));
        }

        $unitPrimaryContactRecs = UserPurchaserUnit::where('property_id', $account_id)->where('unit_id',$invoiceObj->unit_no)->where('primary_contact',1)->where('role_id',2)->where('status',1)->orderby('id','asc')->get();
        $primayContactIds = array();
        if($unitPrimaryContactRecs){
			//echo "primary contact";
			foreach($unitPrimaryContactRecs as $unitPrimaryContactRec){
				$primayContactIds[] = $unitPrimaryContactRec->user_info_id;
			}
		}
		//print_r($primayContactIds);
        $primary_contact = UserMoreInfo::WhereIn('id',$primayContactIds)->where('status',1)->orderby('id','asc')->first(); 

        //$primary_contact = User::where('role_id',2)->where('status',1)->where('primary_contact',1)->where('unit_no',$invoiceObj->unit_no)->orderby('id','asc')->first();

            if(isset($primary_contact)){
                    //Start Insert into notification module
                    $notification = array();
                    $notification['account_id'] = $invoiceObj->account_id;
                    $notification['user_id'] = $primary_contact->user_id;
                    $notification['unit_no'] = $invoiceObj->unit_no;
                    $notification['module'] = 'resident management';
                    $notification['ref_id'] = $invoiceObj->id;
                    $notification['title'] = 'Resident Management';
                    $notification['message'] = 'Management has updated the status for the payment on invoice '.$invoiceObj->invoice_no;
                    $result = UserNotification::insert($notification);

                    $SettingsObj = UserNotificationSetting::where('user_id',$primary_contact->user_id)->where('account_id',$invoiceObj->account_id)->first();
		            if(empty($SettingsObj) || $SettingsObj->resident_management ==1){
                        $fcm_token_array ='';
                        $user_token = ',';
                        $ios_devices_to_send = array();
                        $android_devices_to_send = array();
                        $logs = UserLog::where('user_id',$primary_contact->user_id)->where('status',1)->orderby('id','desc')->first();
                        if(isset($logs->fcm_token) && $logs->fcm_token !=''){
                            $user_token .=$logs->fcm_token.",";
                            $fcm_token_array .=$logs->fcm_token.',';
                            $appSipAccountList[] = $primary_contact->user_id;
                            if($logs->login_from ==1)
                                $ios_devices_to_send[] = $logs->fcm_token;
                            if($logs->login_from ==2)
                                $android_devices_to_send[] = $logs->fcm_token;
                        }

                        $probObj = Property::find($account_id);
                        $title = "Aerea Home - ".$probObj->company_name;
                        $message = "Invoice Payment Updated";
                        $notofication_data = array();
                        $notofication_data['body'] =$title;                        
                        $notofication_data['unit_no'] =$invoiceObj->unit_no;   
                        $notofication_data['user_id'] =$primary_contact->user_id;   
                        $notofication_data['property'] =$invoiceObj->account_id;
                        $purObj = UserPurchaserUnit::where('property_id',$invoiceObj->account_id)->where('unit_id',$invoiceObj->unit_no)->where('user_info_id',$primary_contact->id)->where('status',1)->first(); 
                        if(isset($purObj))
                            $notofication_data['switch_id'] =$purObj->id;    
                                
                        $NotificationObj = new \App\Models\v7\FirebaseNotification();
                        $NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
                        $NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
                        //End Insert into notification module
                    }
            }
           // exit;
        return redirect("opslogin/invoice/payment/$id")->with('status', 'Payment details updated!'); 
                     
    }

    public function paymentdelete($id)
    {
        
        $paymentObj = FinanceInvoicePayment::find($id);
        $invoice_id = $paymentObj->invoice_id;
       
        FinanceInvoicePaymentPaidDetail::where('payment_id', $id)->delete();
        FinanceCreditPayment::where('payment_id', $id)->delete(); 
        
        $invoiceObj = FinanceInvoice::find($invoice_id);

        if(isset($invoiceObj->paymentdetails))
        {
            foreach($invoiceObj->paymentdetails as $key =>  $detail)
            {
                $detail_record = FinanceInvoicePaymentDetail::find($detail->id);
                $payment_received_date = "0000-00-00";
                $paid_amount =0;
                if(isset($detail_record->paymenthistory)){
                    foreach($detail_record->paymenthistory as $record){
                        $paid_amount += $record->amount; 
                        $payment_received_date = $record->payment_received_date;
                    }
                }
                //echo $detail_record->amount;
                $detail_balance_amount = ($detail_record->amount - $paid_amount);
                if($detail_balance_amount<=0)
                    $payment_status = 2;
                else
                    $payment_status = 3;

                FinanceInvoicePaymentDetail::where('id' , $detail->id)->update( array('balance' => $detail_balance_amount,'payment_status'=>$payment_status,'payment_received_date'=>$payment_received_date));

                echo "id : ".$detail->id;
                echo "balance : ".$detail_balance_amount;
                echo " payment_status : ".$payment_status;
                echo " payment_received_date : ".$payment_received_date;
                echo "<hr />"; 

            }
        }
        FinanceInvoicePayment::findOrFail($id)->delete(); 
        $invoiceObj = FinanceInvoice::find($invoice_id);
        $amount_received =0;
        if($invoiceObj->payments){
            foreach($invoiceObj->payments as $k => $payment){
                if($payment->payment_option ==1 && $payment->status !=2)
                    $amount_received += $payment->cheque_amount; 
                else if($payment->payment_option ==2)
                    $amount_received += $payment->bt_amount_received;
                else if($payment->payment_option ==5)
                    $amount_received += $payment->online_amount_received;
                else if($payment->payment_option ==6)
                    $amount_received += $payment->credit_amount;
                else if($payment->payment_option ==7)
                    $amount_received += $payment->add_amt_received;
                else
                    $amount_received += $payment->cash_amount_received;
            }
        }
        
       /* $mf_balnce_amount = $invoiceObj->previous_mf_balance_amount + $mf_paid;
        $sf_balnce_amount = $invoiceObj->previous_sf_balance_amount +$sf_paid;
        $int_balnce_amount = $invoiceObj->previous_int_balance_amount +$int_paid;
        $tax_balnce_amount = $invoiceObj->previous_tax_balance_amount +$tax_paid; */

        
        $balance_amount = ($invoiceObj->payable_amount - $amount_received);

    
        if($balance_amount <=0){
            if($balance_amount ==0)
                $balnce_type =1;
            else{
                $balnce_type =2;
                $balance_amount = 0- $balance_amount;
            }
            FinanceInvoice::where('id' , $invoiceObj->id)->update( array('status' => 3,'balance_amount'=>$balance_amount,'balance_type'=>$balnce_type));
        }
        else{
            FinanceInvoice::where('id' , $invoiceObj->id)->update( array( 'status' => 2,'balance_amount'=>$balance_amount,'balance_type'=>1));
        }

        
            return redirect("opslogin/invoice/payment/$invoice_id")->with('status', 'Payment details deleted successfully!');
    }

    public function bounceback(request $request)
    {   
        $input = $request->all();
        $id = $input['bookId'];
        $remark = $input['remark'];
        $bounced_date = isset($input['bounced_date'])?$input['bounced_date']:'0000-00-00';
        $paymentObj = FinanceInvoicePayment::find($id);
        $invoice_id = $paymentObj->invoice_id;
       
        FinanceInvoicePaymentPaidDetail::where('payment_id', $id)->delete();
        FinanceCreditPayment::where('payment_id', $id)->delete(); 
        
        $invoiceObj = FinanceInvoice::find($invoice_id);

        if(isset($invoiceObj->paymentdetails))
        {
            foreach($invoiceObj->paymentdetails as $key =>  $detail)
            {
                $detail_record = FinanceInvoicePaymentDetail::find($detail->id);
                $payment_received_date = "0000-00-00";
                $paid_amount =0;
                if(isset($detail_record->paymenthistory)){
                    foreach($detail_record->paymenthistory as $record){
                        $paid_amount += $record->amount; 
                        $payment_received_date = $record->payment_received_date;
                    }
                }
                //echo $detail_record->amount;
                $detail_balance_amount = ($detail_record->amount - $paid_amount);
                if($detail_balance_amount<=0)
                    $payment_status = 2;
                else
                    $payment_status = 3;

                FinanceInvoicePaymentDetail::where('id' , $detail->id)->update( array('balance' => $detail_balance_amount,'payment_status'=>$payment_status,'payment_received_date'=>$payment_received_date));

                /*echo "id : ".$detail->id;
                echo "balance : ".$detail_balance_amount;
                echo " payment_status : ".$payment_status;
                echo " payment_received_date : ".$payment_received_date;
                echo "<hr />"; */

            }
        }
        //FinanceInvoicePayment::findOrFail($id)->delete(); 
        FinanceInvoicePayment::where('id',$id)->update(array('status'=>2,'remarks'=>$remark,'bounced_cheque_date'=>$bounced_date)); 
        $invoiceObj = FinanceInvoice::find($invoice_id);
        $amount_received =0;
        if($invoiceObj->payments){
            foreach($invoiceObj->payments as $k => $payment){
                if($payment->payment_option ==1 && $payment->status !=2)
                    $amount_received += $payment->cheque_amount; 
                else if($payment->payment_option ==2)
                    $amount_received += $payment->bt_amount_received;
                else if($payment->payment_option ==5)
                    $amount_received += $payment->online_amount_received;
                else if($payment->payment_option ==6)
                    $amount_received += $payment->credit_amount;
                else if($payment->payment_option ==7)
                    $amount_received += $payment->add_amt_received;
                else
                    $amount_received += $payment->cash_amount_received;
            }
        }
        
       /* $mf_balnce_amount = $invoiceObj->previous_mf_balance_amount + $mf_paid;
        $sf_balnce_amount = $invoiceObj->previous_sf_balance_amount +$sf_paid;
        $int_balnce_amount = $invoiceObj->previous_int_balance_amount +$int_paid;
        $tax_balnce_amount = $invoiceObj->previous_tax_balance_amount +$tax_paid; */

        
        $balance_amount = ($invoiceObj->payable_amount - $amount_received);

    
        if($balance_amount <=0){
            if($balance_amount ==0)
                $balnce_type =1;
            else{
                $balnce_type =2;
                $balance_amount = 0- $balance_amount;
            }
            FinanceInvoice::where('id' , $invoiceObj->id)->update( array('status' => 3,'balance_amount'=>$balance_amount,'balance_type'=>$balnce_type));
        }
        else{
            FinanceInvoice::where('id' , $invoiceObj->id)->update( array( 'status' => 2,'balance_amount'=>$balance_amount,'balance_type'=>1));
        }

        
            return redirect("opslogin/invoice/payment/$invoice_id")->with('status', 'Cheque Status updated successfully!');
    }


    public function invoicetypeamount(Request $request)
    {
        $amount ='';
        //$account_id = Auth::user()->account_id;
        $type = $request->type;
        $account_id = $request->account_id;
        $amounts = FinanceShareSetting::where("account_id",$account_id)->where('status',1)->first();
        if($type ==1 && isset($amounts->management_fund_share))
            $amount = number_format($amounts->management_fund_share,2);
        else if($type ==2 && isset($amounts->sinking_fund_share))
            $amount = number_format($amounts->sinking_fund_share,2);
        else
            $amount = '';

        return json_encode($amount);

       
    }


    public function invoice_report()
    {
        $q = $option =$unit = $status = $invoice_no = $batch_file_no = $building = $users = $month = $unit_print = $unitno = '';
        $account_id = Auth::user()->account_id;
        $dateS = date("Y-m-d",strtotime(Carbon::now()->startOfMonth()->subMonth(12)));
        //echo " --- ";
        //echo $dateE = Carbon::now()->startOfMonth(); 

        $invoices = FinanceInvoice::where('account_id',$account_id)->where('created_at', '>=', $dateS)->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));       

        $currentURL = url()->full();
        $page = explode("=",$currentURL);

        session()->put('current_page', 'invoice_report');
        if(isset($page[1]) && $page[1]>0){
                session()->put('page', $page[1]);
        }else{
                session()->forget('page');
        }
        $visitor_app_url = url('visitors');
        $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();
        $property_info = Property::where('id',$account_id)->first();
		$file_path = image_storage_domain();
        //$shares = FinanceShareSetting::paginate(150);   
        return view('admin.finance.report',compact('invoices','unit','status','invoice_no','batch_file_no','option','month','building','visitor_app_url','buildings','property_info','file_path','unit_print','unitno'));
    }


    public function report_search(Request $request){

        $q = $option =$unit = $status = $invoice_no = $batch_file_no = $users = $month = $building = $fromdate = $todate = $unitno = '';
        $account_id = Auth::user()->account_id;
        $option  =    $request->input('option'); 
        $fromdate = $request->input('fromdate');
        if($request->input('todate') !='')
            $todate = $request->input('todate');
        else
            $todate =$request->input('fromdate');
        $batch_file_no = $request->input('batch_file_no');
        $invoice_no = $request->input('invoice_no');
        $unit = $request->input('unit');
        $building = $request->input('building');
        if($unit !='' || $building !=''){   
            $unit = str_replace("#",'',$unit);
            $unitObj = Unit::select('id','unit')->where('account_id',$account_id)->where(function ($query) use ($building,$unit) {
            if($building !='')
                $query->where('building_id',$building);
            })->get();   
            if(isset($unitObj)){
                foreach($unitObj as $unitid){
                    if(Crypt::decryptString($unitid->unit) ===$request->input('unit')){
                        $units[] = $unitid->id;
                        $unitno = $unitid->id;
                    }
                    else if ($request->input('unit') =='')
                        $units[] = $unitid->id;
                }
            }

        }
       /* $unitObj = Unit::select('id')->where('account_id',$account_id)->where(function($query) use ($unit, $building){
            if($unit !='')
                $query->where('unit',$unit);
            if($building !='')
                $query->where('building_id',$building);
        })->get();

        $units = array();
        if(isset($unitObj)){
            foreach($unitObj as $unitid){
                $units[] = $unitid->id;
                $unitno = $unitid->id;
            }
        }*/
        //print_r( $units);
        $unit_print = 0;
        if(isset($units) && count($units) ==1){
            $unit_print =1;
        }
        //echo $unit_print;

        $status = $request->input('status');
       
        $invoices =  FinanceInvoice::where('account_id',$account_id)->where(function($query) use ($batch_file_no,$invoice_no,$unit,$units, $fromdate,$todate,$status,$building){
            if($fromdate !='')
                $query->whereBetween('invoice_date',array($fromdate,$todate));
            if($batch_file_no !='')
                $query->where('batch_file_no', 'LIKE', '%'.$batch_file_no .'%');
            if($unit !='' || $building !='')
                $query->whereIn('unit_no', $units);
            if($invoice_no !='')
                $query->where('invoice_no', 'LIKE', '%'.$invoice_no .'%');
            if($status !='')
                $query->where('status', $status);
        
        })->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS'));
        
        /*if ($option != '') {

            if($option == 'date') {
					
                $fromdate = $request->input('fromdate');

                if($request->input('todate') !='')
                    $todate = $request->input('todate');
                else
                    $todate =$request->input('fromdate');

                $invoices =  FinanceInvoice::where('account_id',$account_id)->where(function($query) use ($fromdate,$todate){
                    if($fromdate !=''){
                        $query->whereBetween('invoice_date',array($fromdate,$todate));
                    }
                
                })->orderBy('id','DESC')->paginate(env('PAGINATION_ROWS'));
                
            }

            if($option == 'batch') {
                $batch_file_no = $request->input('batch_file_no');
                $invoices =  FinanceInvoice::where('account_id',$account_id)->where('batch_file_no', 'LIKE', '%'.$batch_file_no .'%')
                ->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
            }
            if($option == 'invoice') {
                $invoice_no = $request->input('invoice_no');
                $invoices =  FinanceInvoice::where('account_id',$account_id)->where('invoice_no', 'LIKE', '%'.$invoice_no .'%')
                ->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
            }

            if($option == 'unit' ) { 
                $unit = $request->input('unit');
                $unitObj = Unit::select('id')->where('account_id',$account_id)->where('unit',$unit)->get();
                $units = array();
                if(isset($unitObj)){
                    foreach($unitObj as $unitid){
                        $units[] = $unitid->id;
                    }
                }
                $invoices =  FinanceInvoice::where('account_id',$account_id)->whereIn('unit_no', $units)
                    ->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
                
            }
            if($option == 'status') {
                $status = $request->input('status');
                $invoices =  FinanceInvoice::where('account_id',$account_id)->where('status', $status)
                    ->orderby('id','desc')->paginate(env('PAGINATION_ROWS'));
            }
        }
        */
            $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();
            $property_info = Property::where('id',$account_id)->first();
            $file_path = image_storage_domain();
            $visitor_app_url = url('visitors');
            session()->put('current_page', 'search_page' );
            $search_url = $_SERVER['REQUEST_URI'];
            session()->put('search_url', $search_url );

            $currentURL = url()->full();
            $page = explode("=",$currentURL);
            if(isset($page[1]) && $page[1]>0){
                    session()->put('page', $page[1]);
            }else{
                    session()->forget('page');
            }

            return view('admin.finance.report',compact('invoices','unit','status','invoice_no','batch_file_no','option','month','building','visitor_app_url','fromdate','todate','buildings','property_info','file_path','unit_print','unitno'));
       
   }

   public function paidlists()
    {
        $q  =$unit  = $type = $fromdate = $todate = $buildings = $building = $date_option= '';
        $account_id = Auth::user()->account_id;
        $dateS = date("Y-m-d",strtotime(Carbon::now()->startOfMonth()->subMonth(12)));

        $paidlists = FinanceInvoicePaymentDetail::select('finance_invoice_payment_details.*')->where('finance_invoices.account_id',$account_id)->join('finance_invoices', 'finance_invoice_payment_details.invoice_id', '=', 'finance_invoices.id')->where('finance_invoice_payment_details.amount','>',0)->orderby('finance_invoice_payment_details.id','desc')->paginate(env('PAGINATION_ROWS')); 

        $currentURL = url()->full();
        $page = explode("=",$currentURL);
        session()->put('current_page', 'invoice_report');
        if(isset($page[1]) && $page[1]>0){
                session()->put('page', $page[1]);
        }else{
                session()->forget('page');
        }
        $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();
        $types = FinanceReferenceType::pluck('reference_type', 'id')->all();
        return view('admin.finance.paidlists',compact('paidlists','unit','types','type','fromdate','todate','buildings', 'building','date_option'));
    }

    public function paidlist_search(Request $request)
    {
        $q  =$unit  = $type = $fromdate = $todate = $buildings = $building = $date_option = '';
        $account_id = Auth::user()->account_id;

        $fromdate = $request->input('fromdate');
        if($request->input('todate') !='')
            $todate = $request->input('todate');
        else
            $todate =$request->input('fromdate');
        $type = $request->input('type');
        $unit = $request->input('unit');
        $building = $request->input('building');
        $date_option = $request->input('date_option');

        if($unit !='' || $building !=''){   
            $unit = str_replace("#",'',$unit);
            $unitObj = Unit::select('id','unit')->where('account_id',$account_id)->where(function ($query) use ($building,$unit) {
            if($building !='')
                $query->where('building_id',$building);
            })->get();   
            if(isset($unitObj)){
                foreach($unitObj as $unitid){
                    if(Crypt::decryptString($unitid->unit) ===$request->input('unit'))
                        $units[] = $unitid->id;
                    else if ($request->input('unit') =='')
                        $units[] = $unitid->id;
                }
            }

        }

        $paidlists =  FinanceInvoicePaymentDetail::select('finance_invoice_payment_details.*')->where('finance_invoices.account_id',$account_id)->join('finance_invoices', 'finance_invoice_payment_details.invoice_id', '=', 'finance_invoices.id')->where('finance_invoice_payment_details.amount','>',0)->where(function($query) use ($type,$building,$units,$unit, $fromdate,$todate,$date_option){
           
            if($unit !='' || $building !='')
                $query->whereIn('finance_invoice_payment_details.unit_no', $units);
            if($type !='')
                $query->where('finance_invoice_payment_details.reference_type', $type);
            if($fromdate !=''){
                    if($date_option ==1)
                        $query->whereBetween('finance_invoice_payment_details.invoice_date',array($fromdate,$todate));
                    else
                        $query->whereBetween('finance_invoice_payment_details.payment_received_date',array($fromdate,$todate));
                }
           
            })->orderBy('finance_invoice_payment_details.id','DESC')->paginate(env('PAGINATION_ROWS'));

        $currentURL = url()->full();
        $page = explode("=",$currentURL);
        session()->put('current_page', 'invoice_report');
        if(isset($page[1]) && $page[1]>0){
                session()->put('page', $page[1]);
        }else{
                session()->forget('page');
        }
        $buildings = Building::where('account_id',$account_id)->pluck('building', 'id')->all();
        $types = FinanceReferenceType::pluck('reference_type', 'id')->all();
        return view('admin.finance.paidlists',compact('paidlists','unit','types','type','fromdate','todate','buildings', 'building','date_option'));
    }

   public function invoice_first_reminder()
    {
        $q = $option =$unit = $status = $invoice_no = $batch_file_no = $building = $users = $month ='';
        $account_id = Auth::user()->account_id;
        $dt  =Carbon::now();
        //$date_format = $dt->subDays(5);
        $from_date = date("Y-m-d");
        $date_format = $dt->addDays(30);
        echo $to_date = date("Y-m-d",strtotime($date_format));
        
        $invoices = FinanceInvoice::where('account_id',$account_id)->whereBetween('due_date',[$from_date,$to_date])->whereNotIn('status',[3])->where('reminder_status',0)->orderby('id','desc')->get();       

        if(isset($invoices)){
            foreach($invoices as $invoice){

                echo "Due Date :".$invoice->due_date;
                echo "<br>";

                /*$invoiceObj = FinanceInvoice::find($invoice->id);
                $invoiceObj->reminder_status = 1;
                $invoiceObj->first_reminder_on = date("Y-m-d");
                $invoiceObj->save();*/
            }
        }
    }

    public function uploadcsv()
    {
        $properties = Property::pluck('company_name', 'id')->all();
        return view('admin.finance.uploadcsv', compact('properties'));
    }

    public function importcsv(Request $request){
        \Log::info('Import CSV START=> '.now());

        $input = $request->all();
        $property = Auth::user()->account_id;
        $created_id = Auth::user()->id;

        $sharesettings = FinanceShareSetting::where("account_id",$property)->where('status',1)->first();


        if ($request->file('csv_file') != null) {
            $extension = $request->file('csv_file')->getClientOriginalName();
            $filename = uniqid().'.'.$extension; 
            $csv_file_path = $request->file('csv_file')->storeAs("invoice/$property",$filename);
        }
        $file_path = "app/".$csv_file_path;
        $filename =  base_path() .\Storage::url($file_path);
        $invoiceArr = $this->csvToArray($filename);
        
        $propertyObj = Property::where('id',$property)->first();
        $input['account_id'] = $property;
        $input['created_by'] = $created_id;
        $input['batch_no']= date("Ym")."-1";
        //echo "hi";
        //echo $input['month'] = $invoiceArr[0][4];
        $input['month'] = Carbon::createFromFormat('d/m/y', $invoiceArr[0][4])->format('Y-m-d');
        //exit;
        

        $input['due_date'] = Carbon::createFromFormat('d/m/y', $invoiceArr[0][5])->format('Y-m-d');
        //$input['due_date'] = $invoiceArr[0][5];
        $input['comp_name'] = $propertyObj->management_company_name;
        $input['comp_address'] = $propertyObj->management_company_addr;;
        $input['notes'] = $propertyObj->invoice_notes;
        $input['status'] = 2;
        $info = FinanceInvoiceInfo::create($input); // insert Batch
        $order=0;
        $json_data = array();
        if(isset($invoiceArr)){
           
            foreach($invoiceArr as $k => $invoice){
                if($invoice[0] !=''){
                    $invoiceObj = FinanceInvoice::where('invoice_no',$invoice[3])->where('account_id',$property)->first();
                    $unit = substr($invoice[2], 1);
                    $buildingObj = Building::where('building',$invoice[1])->where('account_id',$property)->first();
                    if(empty($buildingObj)){
                        return redirect("opslogin/invoice")->with('status', "Error: Building name ".$invoice[1]." not valid, Please check.");  
                    }
                    $buildingunitObj = Unit::where('building_id', $buildingObj->id)->where('account_id',$property)->get();
                    if(!empty($buildingunitObj)){
                       \Log::info('Building :'.$buildingObj->id. " Property :".$property);
                        $unit_valid = 0;
                        foreach($buildingunitObj as $unitid){
                            if(Crypt::decryptString($unitid->unit) ===$unit){
                                $unit_valid = 1;
                                $unitObj = Unit::where('id', $unitid->id)->first();
                                break;
                            }
                        }
                        if($unit_valid ==0)
                            return redirect("opslogin/invoice")->with('status', "Error: Unit ".$unit." not valid, Please check.");

                    }else{
                        \Log::info('Building :'.$buildingObj->id. " Property :".$property);
                        return redirect("opslogin/invoice")->with('status', "Error: Units not available, Building name ".$invoice[1]);  

                    }
                    $allow = 1;
                    //exit;
                    if(empty($invoiceObj)){
                        if(isset($unitObj))
                        {
                            $allow = 1;
                            $balance_amount = $invoice[10] - $invoice[11];
                            $balnce_type = 1;
                            //echo "Invoice: ".$invoice[3];
                            //echo " balance_amount: ".$balance_amount;
                        
                            if($balance_amount <=0){
                                if($balance_amount ==0)
                                    $balnce_type =1;
                                else{
                                    $balnce_type =2;
                                    $balance_amount = 0- $balance_amount;
                                }
                                $status = 3;
                            }
                            else if($invoice[11] >0)
                                $status = 2;
                            else
                                $status = 1;

                            //echo " balnce_type: ".$balnce_type;
                            //echo "<hr>";
                            $due_date = $invoice[5];
                            $invoice_input['account_id'] = $property;
                            $invoice_input['unit_no'] = $unitObj->id;
                            $invoice_input['invoice_no'] = $invoice[3];
                        
                            $invoice_input['due_date'] = Carbon::createFromFormat('d/m/y', $invoice[5])->format('Y-m-d');
                            //$invoice_input['due_date'] = $invoice[5];
                            $invoice_input['info_id'] = $info->id;
                            $invoice_input['batch_file_no'] = $info->batch_no;
                            
                            $invoice_input['invoice_date'] = Carbon::createFromFormat('d/m/y', $invoice[4])->format('Y-m-d');
                            //$invoice_input['invoice_date'] = $invoice[4];
                            $invoice_input['invoice_amount'] = $invoice[10];
                            $invoice_input['payable_amount'] = $invoice[10];
                            $invoice_input['balance_amount'] = $balance_amount;
                            $invoice_input['balance_type'] = $balnce_type;
                            $invoice_input['status'] = $status;
                            $invoice_input['unit_share'] = $unitObj->share_amount;
                            if($sharesettings->tax ==2){
                                $invoice_input['tax_percentage'] =$sharesettings->tax_percentage;
                            }
                            $invoice_input['created_at'] = Carbon::createFromFormat('d/m/y', $invoiceArr[0][4])->format('Y-m-d');
                            //$invoice_input['created_at'] = $invoice[0][4];

                            $invoiceObj = FinanceInvoice::create($invoice_input); // insert Invoice
                        
                        }
                        else{
                            $allow =0;
                        }
                    }

                    /******* Insert Paymentinfo start*/
                    $paymentObj = FinanceInvoicePayment::where('invoice_id', $invoiceObj->id)->first();
                    if(empty($paymentObj) && $invoice[11] >0){ //
                        $payment =array();
                        $payment['invoice_id'] = $invoiceObj->id;
                        $payment['manager_received'] = $created_id;
                        $payment_date = $invoice[17];
                        $option = 0;
                        if($invoice[18] =="Cheque")
                            $option = 1;
                        else if($invoice[18] =="Bank Transfer")
                            $option = 2;
                        else if($invoice[18] =="Cash")
                            $option = 3;
                        if($option >0) {
                            $payment['payment_option'] =  $option;
                            if( $option ==1){
                                if($invoice[11] != null)
                                    $payment['cheque_amount'] = $invoice[11];
                                if($invoice[19] != null)
                                    $payment['cheque_no'] = $invoice[19];
                                if($invoice[17] != null){
                                    $payment['cheque_received_date'] = Carbon::createFromFormat('d/m/y', $payment_date)->format('Y-m-d');
                                    $payment['payment_received_date'] = Carbon::createFromFormat('d/m/y', $payment_date)->format('Y-m-d');
                                    //$payment['cheque_received_date'] = $payment_date;
                                    //$payment['payment_received_date'] = $payment_date;
                                }
                                if($invoice[20] != null)
                                    $payment['cheque_bank'] = $invoice[20];
                            }
                            if($option ==2){
                                if($invoice[17] != null){
                                    $payment['bt_received_date'] = Carbon::createFromFormat('d/m/y', $payment_date)->format('Y-m-d');
                                    $payment['payment_received_date'] = Carbon::createFromFormat('d/m/y', $payment_date)->format('Y-m-d');
                                    //$payment['bt_received_date'] = $payment_date;
                                    //$payment['payment_received_date'] = $payment_date;
                                }
                                if($invoice[11] != null)
                                    $payment['bt_amount_received'] = $invoice[11];
                            }
                            if( $option ==3){
                                if($invoice[11] != null)
                                    $payment['cash_amount_received'] = $invoice[11];
                                if($invoice[17] != null){
                                    $payment['cash_received_date'] = Carbon::createFromFormat('d/m/y', $payment_date)->format('Y-m-d');
                                    $payment['payment_received_date'] = Carbon::createFromFormat('d/m/y', $payment_date)->format('Y-m-d');
                                    //$payment['cash_received_date'] = $payment_date;
                                    //$payment['payment_received_date'] =$payment_date;
                                }					
                            }
                            if($invoice[19] != null)   
                                $payment['receipt_no'] = $invoice[19];

                            $payment['created_at'] = date("Y-m-d H:i:s");
                            $payment['updated_at'] = date("Y-m-d H:i:s");
                            
                           
                            

                            $paymentObj = FinanceInvoicePayment::create($payment);
                        }

                        
                    }
                   
                    /******* insert Paymentinfo end*/
                
                    /******* insert invoice details start*/
                    if($allow ==1){ 
                        $order++;
                        $type = FinanceReferenceType::where('reference_type',$invoice[12])->first();

                        if(empty($type)){
                            return redirect("opslogin/invoice")->with('status', "Error: Reference type ".$invoice[12]." not valid, Please check.");  
                        }
                        $reference_type =$type->id;
                        $balance = $invoice[15]-$invoice[16];
                        $detail_input =array();
                        $detail_input['account_id'] = $property;
                        $detail_input['unit_no'] = $unitObj->id;
                        $detail_input['due_date'] = Carbon::createFromFormat('d/m/y', $invoice[5])->format('Y-m-d');
                        $detail_input['invoice_id'] = $invoiceObj->id;
                        $detail_input['reference_invoice'] = $invoiceObj->id;
                        $detail_input['reference_type'] = $reference_type;
                        $detail_input['reference_no'] = $invoice[13];
                        $detail_input['order'] = $order;
                        $detail_input['display_order'] = $invoiceObj->id;
                        $detail_input['reference_invoice'] = $invoiceObj->id;
                        $detail_input['detail'] = $invoice[14];
                        $detail_input['total_amount'] = $invoice[15];
                        $detail_input['amount'] = $invoice[15];
                        $detail_input['balance'] = $balance;
                        $detail_input['status'] = 0;
                        $detail_input['created_at'] = date("y-m-d H:i:s");
                        $detail_input['updated_at'] = date("y-m-d H:i:s");
                        $detailObj = FinanceInvoicePaymentDetail::create($detail_input);

                        if(isset($paymentObj) && $invoice[16] >0){
                            $payment_date =$invoice[17];
                            $payment_received_date = Carbon::createFromFormat('d/m/y', $payment_date)->format('Y-m-d');
                            $PaymentdetailData =array();
                            $PaymentdetailData['account_id'] = $property;
                            $PaymentdetailData['unit_no'] = $invoiceObj->unit_no;
                            $PaymentdetailData['invoice_id'] = $paymentObj->invoice_id;
                            $PaymentdetailData['payment_id'] = $paymentObj->id;
                            $PaymentdetailData['detail_id'] = $detailObj->id;
                            $PaymentdetailData['type'] = $reference_type;
                            $PaymentdetailData['amount'] = $invoice[16];                        
                            $PaymentdetailData['payment_received_date'] = $payment_received_date;
                            $PaymentdetailData['created_at'] = date("Y-m-d H:i:s");
                            $PaymentdetailData['updated_at'] = date("Y-m-d H:i:s");
                            $detail_record = FinanceInvoicePaymentPaidDetail::create($PaymentdetailData);

                            if($balance <=0)
                                $payment_status = 2;
                            else
                                $payment_status = 3;
                                /*echo "id :".$detail_record->id;
                                echo "Payment Date :".$invoice[17];
                                echo  "Payment Formated Date :".$payment_received_date;
                                echo "payment_status:".$payment_status;
                                echo "<hr>";*/
                            FinanceInvoicePaymentDetail::where('id' , $detailObj->id)->update( array('payment_status'=>$payment_status,'payment_received_date'=>$payment_received_date));
                        }
                    }
                    /******* insert invoice details end*/
                }
            } 
        }
        \Log::info('Import CSV END=> '.now());

        //exit;
        return redirect("opslogin/invoice")->with('status', 'Invoice has been imported from CSV');  


    }


    function csvToArray($filename = '', $delimiter = ',')
    {
       
        if (!file_exists($filename)){
            echo "File not exist";
        } if(!is_readable($filename)){
            echo "File not readable";
        }

       
        $header = null;
        $data = array();
       
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                    $data[] = $row;
            }
            fclose($handle);
        }

        return $data;
    }


    public function send_notification($id)
    {
        $q = $option =$unit = $status = $invoice_no = $batch_file_no = $users = $month ='';
        $account_id = Auth::user()->account_id;
        $info_id = $id;

        $infoObj = FinanceInvoiceInfo::find($id);
        $count = $infoObj->notification_status+1;
        $infoObj->notification_status = $count;
        $infoObj->save();

        FinanceInvoice::where('info_id',$id)->update( array( 'user_access_status' => 1));// allow user to access invoice

        $invoices = FinanceInvoice::where('account_id',$account_id)->where('info_id',$id)->orderby('id','desc')->get();       
        if(isset($invoices)){
            foreach($invoices as $invoice){
                //echo "hi 1";
                //$notification = new \App\Models\v7\FinanceInvoice();
                //$email = $notification->sendnotification($invoice);
                
                //$unitPrimaryContactRecs = UserPurchaserUnit::where('property_id', $account_id)->where('unit_id',$invoice->unit_no)->where('primary_contact',1)->where('role_id',2)->where('status',1)->orderby('id','asc')->get();
                $unitPrimaryContactRecs = UserPurchaserUnit::whereHas('usermoreinfo', function (Builder $query) {
                    $query->where('status',1);
                })->where('property_id', $account_id)->where('unit_id',$invoice->unit_no)->where('primary_contact',1)->where('role_id',2)->get();

                $primayContactIds = array();
                //print_r($unitPrimaryContactRecs);
                if(isset($unitPrimaryContactRecs->usermoreinfo)){
                    //echo ".";
                    foreach($unitPrimaryContactRecs as $unitPrimaryContactRec){
                        $primayContactIds[] = $unitPrimaryContactRec->user_info_id;
                    }
                }
                else{
                    //echo "--";
                    $unitPrimaryContactRecs = UserPurchaserUnit::where('property_id', $account_id)->where('unit_id',$invoice->unit_no)->where('role_id',2)->where('status',1)->orderby('id','asc')->get(); 
                    if($unitPrimaryContactRecs){
                        foreach($unitPrimaryContactRecs as $unitPrimaryContactRec){
                            $primayContactIds[] = $unitPrimaryContactRec->user_info_id;
                        }
                    }
                }
                //print_r($primayContactIds);
                $primary_contacts = UserMoreInfo::WhereIn('id',$primayContactIds)->where('status',1)->orderby('id','asc')->get(); 
                if(isset($primary_contacts)){
                    foreach($primary_contacts as $primary_contact)
                    {
                       //echo "hello";
                        $notification = array();
                        $notification['unit_no'] = $invoice->unit_no;
                        $notification['info_id'] = $id;
                        $notification['invoice_id'] = $invoice->id;
                        $notification['account_id'] = $invoice->account_id;
                        $notification['user_id'] = $primary_contact->user_id;
                        $fname = ($primary_contact->first_name !='')?Crypt::decryptString($primary_contact->first_name):'';
                        $lname = ($primary_contact->last_name !='')?Crypt::decryptString($primary_contact->last_name):'';
                        $notification['name'] = $fname." ".$lname;
                        $notification['email'] = $primary_contact->getuser->email;
                        FinanceNotificationDetail::insert($notification);
                    }
                }

            }
        }
        //exit;
        $currentURL = url()->full();
        $page = explode("=",$currentURL);
        session()->forget('current_page');
       
        if(isset($page[1]) && $page[1]>0){
                session()->put('page', $page[1]);
        }else{
                session()->forget('page');
        }
        return redirect("opslogin/invoice")->with('status', 'Notification scheduled. Will start sending shortly.');  

    }

}
