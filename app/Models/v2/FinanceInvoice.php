<?php

namespace App\Models\v2;
use DateTime;

use Session;

use App\Models\v2\FinanceInvoicePayment;

use Illuminate\Database\Eloquent\Model;

class FinanceInvoice extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'finance_invoices';

    protected $fillable = [
        'account_id','invoice_no','invoice_date','due_date','batch_file_no','info_id','unit_no','unit_share','tax_percentage','invoice_amount','payable_amount','balance_amount','balance_type','notes','status','remarks'
    ];

    public function invoice_info(){
        return $this->belongsTo('App\Models\v2\FinanceInvoiceInfo','info_id');
    }

    public function paymentdetails(){
        return $this->hasMany('App\Models\v2\FinanceInvoicePaymentDetail','invoice_id')->orderBy('display_order')->orderBy('order');
    }

    public function payments(){
        return $this->hasMany('App\Models\v2\FinanceInvoicePayment','invoice_id')->orderBy('id','desc');
    }

    public function AdvancePayment(){
        return $this->hasOne('App\Models\v2\FinanceAdvancePayment','invoice_id')->orderBy('id','desc');
    }

    public function CreditPayments(){
        return $this->hasMany('App\Models\v2\FinanceCreditPayment','invoice_id')->orderBy('id','desc');
    }

    public function PaymentLog(){
        return $this->hasOne('App\Models\v2\FinancePaymentLog','invoice_id')->orderBy('id','desc');
    }

    public function getunit(){
        return $this->belongsTo('App\Models\v2\Unit','unit_no');
    }

    public function propertydetail(){
        return $this->belongsTo('App\Models\v2\Property','account_id');
    }

    public function invoicecounts($info_id){
        $records = FinanceInvoice::where('info_id',$info_id)->orderby('id','desc')->get();
        $count=count($records);
        return $count;
        //return $this->belongsTo('App\Models\v2\FinanceInvoice','info_id');
    }


    public function ticketgen($code) {
        $date = new DateTime('now');
        //$autonumver = rand(0000000, 9999999);	
        $ticket = $code.$date->format('ymd');
        return $ticket;
        
    }

    public function interest_calculation($amount,$percentage,$startdate,$enddate){

        $start_date = strtotime($startdate);
        $end_date = strtotime($enddate);
        $days = abs($end_date -$start_date)/(60*60*24);
        $interest_per_year = (($amount/100)*$percentage)/365;
        $interest_amount = $interest_per_year * $days;
        return $interest_amount;

    }


    public function previousinvoice_new($unit,$interest_percentage,$invoice_date) {

        $invoiceObj = FinanceInvoice::where('unit_no',$unit)->orderby('id','desc')->first();
        $result = array();
        //$thisclass = new \App\Models\v2\FinanceInvoice();

        if(isset($invoiceObj->id)){
            $result['id'] = $invoiceObj->id;
            $result['payable_amount'] = $invoiceObj->payable_amount;
            $amount_received = 0;
            $interest_amount_received = 0;
            $notes = '';
            //echo "here";

            $startdate = $invoiceObj->due_date;
            $duedate = $invoiceObj->due_date;
            //print_r($invoiceObj->payments);

            if($invoiceObj->payments){
                foreach($invoiceObj->payments as $k => $payment){
                    if($payment->payment_option ==1){
                        $amount_received += $payment->cheque_amount; 
                        $enddate =$payment->payment_received_date; 
                        $interest_amount_per_payment = FinanceInvoice::interest_calculation_per_payment($invoiceObj,$interest_percentage,$invoice_date,$duedate,$payment->id,$startdate, $enddate);
                        $interest_amount_received += $interest_amount_per_payment['interest_charges']; 
                        $notes .= $interest_amount_per_payment['notes']; 
                        $startdate = $payment->payment_received_date; //resetting startdate

                    }else if($payment->payment_option ==2){
                        $amount_received += $payment->bt_amount_received;
                        $enddate =$payment->payment_received_date; 
                        $interest_amount_per_payment = FinanceInvoice::interest_calculation_per_payment($invoiceObj,$interest_percentage,$invoice_date,$duedate,$payment->id,$startdate,$enddate);
                        $interest_amount_received += $interest_amount_per_payment['interest_charges']; 
                        $notes .= $interest_amount_per_payment['notes']; 
                        $startdate = $payment->payment_received_date; //resetting startdate


                    }else{

                        $amount_received += $payment->cash_amount_received;
                        $enddate =$payment->payment_received_date; 
                        $interest_amount_per_payment = FinanceInvoice::interest_calculation_per_payment($invoiceObj,$interest_percentage,$invoice_date,$duedate,$payment->id,$startdate,$enddate);
                        $interest_amount_received += $interest_amount_per_payment['interest_charges']; 
                        $notes .= $interest_amount_per_payment['notes']; 
                        $startdate = $payment->payment_received_date; //resetting startdate

                        
                    }
                }
            }
            
            //echo  $startdate;
           /* echo "<br />";
            echo "Payable Amount :".$invoiceObj->payable_amount;
            echo "Invoice Amount :".$invoiceObj->invoice_amount;
            echo "Received Amount :".$amount_received; 
            $balance_amount = $invoiceObj->payable_amount - $amount_received;
           // exit;  */
            $balance_amount = $invoiceObj->invoice_amount - $amount_received;
            $result['balance_amount'] = $balance_amount;

            //Check MF / SF balanceamount
            $DetailObj = FinanceInvoiceDetail::where('info_id',$invoiceObj->info_id)->get();
            $balance_of_mf_sf = 0;
            $mf_balance =0;
            $sf_balance =0;

            $invoice_details = array();
            if(isset($DetailObj)){           
                foreach($DetailObj as $detail){
                    $inv_detail = array();
                    $amount = ($detail->tot_amount *  $invoiceObj->unit_share);
                
                    $PaymentDetailsObj = FinanceInvoicePaymentDetail::where('invoice_id',$invoiceObj->id)->where('invoice_info_detail_id',$detail->id)->get();
                    $payment_paid = 0;
                    if(isset($PaymentDetailsObj)){    
                        foreach($PaymentDetailsObj as $PaymentDetail){
                            $payment_paid +=$PaymentDetail->amount;
                        }
                    }              
                    
                    if($detail->types->id ==1){
                        $tot_amount = ($amount + $invoiceObj->previous_mf_amount);
                        $mf_balance = $tot_amount - $payment_paid;
                    }
                    if($detail->types->id ==2){
                        $tot_amount = ($amount + $invoiceObj->previous_sf_amount);
                        $sf_balance = $tot_amount - $payment_paid;
                    }
                    
                }
               
                    $balance_of_mf_sf = ($mf_balance + $sf_balance);
               

            }

          

            $days = 0;
            if( $balance_of_mf_sf >0){
                //echo "here";
                $balance_amount = $balance_of_mf_sf;
                $notes .= "balance_amount :".$balance_amount.",";

                $end_date = $invoice_date;
                if(strtotime($startdate) > strtotime($duedate) )
                    $start_date = $startdate;
                else
                    $start_date = $duedate;

                $notes .= "interest_start_date :".$start_date.",";
                //$result['interest_start_date'] = date("Y-m-d",strtotime($invoiceObj->due_date));

                if($balance_amount >0) {//balance not cleared until new invoice created
                    
                    $start_date = strtotime($start_date);
                    $end_date = strtotime($end_date);
                    $days = abs($end_date -$start_date)/(60*60*24);
                    $notes .= "interest_end_date :".$invoice_date.",";
                }else { //balnce amount cleared before invoice creation
                    $start_date = strtotime($start_date);
                    $end_date = strtotime($end_date);
                    $days = abs($end_date -$start_date)/(60*60*24);
                    $notes .= "interest_end_date :".$invoice_date.",";
                }
                //echo "<br /> Days:".$days;
                //echo "<br />";
                //echo "interest_percentage :". $interest_percentage;
                //echo "<br />";
                //$balance_amount = ($invoiceObj->payable_amount - $amount_received);
                $interest_per_year = (($balance_amount/100)*$interest_percentage)/365;
                //echo "<br />";
                $interest_for_days = $interest_per_year * ($days);
                //echo "<br />";
                $interest_amount_received += number_format($interest_for_days,2);
                $notes .= "interest_charges :".number_format($interest_for_days,2)." || ";
                //echo "<br /> Interest Amount:".$interest_for_days; 
                //echo "<br />Sample :";
                //echo (((1000/100)*15)/365) * 31;
                $result['balance_amount'] = $balance_amount;


            }

            //$result['interest_applied_amount'] = $interest_applied_amount;
            $result['interest_charges'] = $interest_amount_received;
            $result['notes'] = $notes;

            /*echo "interest_percentage :".$interest_percentage;
            echo "<br /> Amount :".$balance_amount;
            echo "<br /> Days :".$days;
            echo "<hr />";  */

            //exit;
            return $result;
        }
        else
            return $result;
        
    }

    public function interest_calculation_per_payment($invoiceObj,$interest_percentage,$invoice_date,$duedate,$paymentid,$startdate, $enddate){
        
            $result['id'] = $invoiceObj->id;
            $result['payable_amount'] = $invoiceObj->payable_amount;
            $amount_received = 0;
            $notes = '';
            $paymentlists = FinanceInvoicePayment::where('invoice_id',$invoiceObj->id)->where('payment_received_date','<',$enddate)->orderby('id','asc')->get();

            //echo "<br />Start Date : ".$startdate;
            //echo "<br />End Date :".$enddate;
            //echo "<br />Due Date :".$duedate;
            $payment_received = 0;
            if($paymentlists){
                $payment_ids = array();
                foreach($paymentlists as $k => $payment){
                    $payment_ids[] = $payment->id;
                    if($payment->payment_option ==1 )
                        $payment_received += $payment->cheque_amount; 
                    else if($payment->payment_option ==2 )
                        $payment_received += $payment->bt_amount_received;
                    else if($payment->payment_option ==3 )
                        $payment_received += $payment->cash_amount_received;

                }
                

            }
            //echo "<br />Total:";
            //Check MF / SF balanceamount
            $DetailObj = FinanceInvoiceDetail::where('info_id',$invoiceObj->info_id)->get();
            $balance_of_mf_sf = 0;
            $mf_balance =0;
            $sf_balance =0;

            $invoice_details = array();
            if(isset($DetailObj)){           
                foreach($DetailObj as $detail){
                    $inv_detail = array();
                    $amount = ($detail->tot_amount *  $invoiceObj->unit_share);
                
                    $PaymentDetailsObj = FinanceInvoicePaymentDetail::whereIn('payment_id',$payment_ids)->get();
                    $payment_paid = 0;
                    if(isset($PaymentDetailsObj)){    
                        foreach($PaymentDetailsObj as $PaymentDetail){
                            $payment_paid +=$PaymentDetail->amount;
                        }
                    }              
                    
                    if($detail->types->id ==1){
                        $tot_amount = ($amount + $invoiceObj->previous_mf_amount);
                        $mf_balance = $tot_amount - $payment_paid;
                    }
                    if($detail->types->id ==2){
                        $tot_amount = ($amount + $invoiceObj->previous_sf_amount);
                        $sf_balance = $tot_amount - $payment_paid;
                    }
                    
                }
                if($invoiceObj->previous_bill_balance_type ==2){ // excess to minus
                    $balance_previous = $invoiceObj->previous_bill_balance - ($invoiceObj->tax_amount + $invoiceObj->interest_charges);
                    $balance_of_mf_sf = ($mf_balance + $sf_balance) - $balance_previous;
                }
                else
                    $balance_of_mf_sf = ($mf_balance + $sf_balance);
               

            }

            //$interest_applied_amount = $invoiceObj->payable_amount - $payment_received;
            $interest_applied_amount = $balance_of_mf_sf;
            $days =0;

            //echo "Balance :". $interest_applied_amount;
           
            if( $interest_applied_amount >0 && ($enddate > $duedate)){
                //$result['interest_applied_amount'] = $interest_applied_amount;
                $notes = "interest_applied_amount :".$interest_applied_amount.",";
                $start_date = $startdate;
                $end_date = $enddate;
                $notes .= "interest_start_date :".$startdate.",";
                if($interest_applied_amount >0) {//balance not cleared until new invoice created
                    $start_date = strtotime($start_date);
                    $end_date = strtotime($end_date);
                    $days = abs($end_date -$start_date)/(60*60*24);

                    //$days = $end_date->diff($start_date)->format("%a"); //3
                    //$result['interest_end_date'] = date("Y-m-d",strtotime($invoice_date));
                    $notes .= "interest_end_date :".$enddate.",";
                    
                    //echo "<br />interest_applied_amount : ".$interest_applied_amount;
                    //echo "<br />interest_percentage : ".$interest_percentage;
                    //exit;
                    $interest_per_year = (($interest_applied_amount/100)*$interest_percentage)/365;
                    //echo "interest_per_year :".$interest_per_year;
                    //exit;
                    

                    $interest_for_days = $interest_per_year * $days;
                    $result['balance'] = $interest_applied_amount;
                    $result['interest_charges'] = number_format($interest_for_days,2);
                    $notes .= "interest_charges :".number_format($interest_for_days,2)." || ";
                    $result['notes'] = $notes;
                }

            }else{
                $result['balance'] = 0;
                $result['interest_charges'] =0;
                $result['notes'] = "No Interest charges ||";

            }
            echo "interest_percentage :".$interest_percentage;
            echo "<br /> Amount :".$interest_applied_amount;
            echo "<br /> Days :".$days;
            echo "<hr />";
            //exit;
            //print_r($result);
            //exit;
            return $result;
    }

    public function previousinvoice_data($unit,$interest_percentage,$invoice_date) {

        $invoiceObj = FinanceInvoice::where('unit_no',$unit)->orderby('id','desc')->first();
        $result = array();
        if(isset($invoiceObj->id)){
            $result['id'] = $invoiceObj->id;
            $result['payable_amount'] = $invoiceObj->payable_amount;
            $amount_received = 0;
            if($invoiceObj->payments){
                foreach($invoiceObj->payments as $k => $payment){
                    if($payment->payment_option ==1){
                        $amount_received += $payment->cheque_amount; 
                        $last_payment_received_on = $payment->cheque_received_date; 

                    }else if($payment->payment_option ==2){
                        $amount_received += $payment->bt_amount_received;
                        $last_payment_received_on =$payment->bt_received_date; 

                    }else{
                        $amount_received += $payment->cash_amount_received;
                        $last_payment_received_on =$payment->cash_received_date; 

                    }
                }
            }
            $balance_amount = number_format(($invoiceObj->payable_amount - $amount_received),2);
            $result['balance_amount'] = $balance_amount;

            //interest calculation
            //echo "Id  :".$invoiceObj->id;
            //echo "<br />Interest % :".$interest_percentage;
            //echo "<br />Invoice Date :".$invoice_date;
            //echo "<br />Due Date :".$invoiceObj->due_date;
            //echo "<br />last Payment Date :".$last_payment_received_on;
            $payment_received = 0;
            if($invoiceObj->payments){
                $payment_due_date = $invoiceObj->due_date;
                foreach($invoiceObj->payments as $k => $payment){
                    if($payment->payment_option ==1 && $payment->cheque_received_date <=$payment_due_date)
                        $payment_received += $payment->cheque_amount; 
                    else if($payment->payment_option ==2 && $payment->bt_received_date <=$payment_due_date)
                        $payment_received += $payment->bt_amount_received;
                    else if($payment->payment_option ==3 && $payment->cash_received_date <=$payment_due_date)
                        $payment_received += $payment->cash_amount_received;
                }
            }
            $interest_applied_amount = number_format(($invoiceObj->payable_amount - $payment_received),2);
            if( $interest_applied_amount >0){
                $result['interest_applied_amount'] = $interest_applied_amount;
                $due_date = new DateTime($invoiceObj->due_date);
                $new_invoice_date = new DateTime($invoice_date);
                $result['interest_start_date'] = date("Y-m-d",strtotime($invoiceObj->due_date));

                if($balance_amount >0) {//balance not cleared until new invoice created
                    $days = $new_invoice_date->diff($due_date)->format("%a"); //3
                    $result['interest_end_date'] = date("Y-m-d",strtotime($invoice_date));
                }else { //balnce amount cleared before invoice creation
                    //echo "<br />last Payment Date :".$last_payment_received_on;
                    $last_payment_received_date = new DateTime($last_payment_received_on);
                    $days = $last_payment_received_date->diff($due_date)->format("%a"); //3
                    $result['interest_end_date'] = date("Y-m-d",strtotime($last_payment_received_on));
                }
                //echo "<br /> Days:".$days;
                $interest_per_year = (($interest_applied_amount/100)*$interest_percentage)/365;
                $interest_for_days = $interest_per_year * $days;
                $result['interest_charges'] = number_format($interest_for_days,2);
                //echo "<br /> Interest Amount:".$interest_for_days; 
                //echo "<br />Sample :";
                //echo (((1000/100)*15)/365) * 31;

            }


            return $result;
        }
        else
            return $result;
        
    }

    public function payments_before_due_date($invoice, $date) {
        $payments = FinanceInvoicePayment::where('invoice_id',$invoice)->orderby('id','asc')->get();

        $data =array();
        if($payments){
            
            foreach($payments as $k => $payment){
                //echo "ID : ".$payment->id. " Date :".$date;
                //echo "<br />"; 
                
                $result =array();
                if($payment->payment_option ==1 && $payment->cheque_received_date <=$date){
                    $result['id'] = $payment->id;
                    $result['payment_option'] = "Cheque";
                    $result['date'] = $payment->cheque_received_date;
                    $result['amount'] = $payment->cheque_amount;
                }
                else if($payment->payment_option ==2 && $payment->bt_received_date <=$date){
                    $result['id'] = $payment->id;
                    $result['payment_option'] = "Bank Transfer";
                    $result['date'] = $payment->bt_received_date;
                    $result['amount'] = $payment->bt_amount_received;
                }
                else if($payment->payment_option ==3 && $payment->cash_received_date <=$date){
                    $result['id'] = $payment->id;
                    $result['payment_option'] = "Cash";
                    $result['date'] = $payment->cash_received_date;
                    $result['amount'] = $payment->cash_amount_received;
                }
                if(!empty($result))
                    $data[] =  $result;
            
            }
        }
        //exit;
        return $data;

    }

    public function CheckNewInvoice($invoice,$unit_no){
        $ref_invoice = FinanceInvoice::where('id',">",$invoice)->where('unit_no',$unit_no)->orderby("id","asc")->first();
        return $ref_invoice;

    }

    public function CheckOverDue($invoice_id){
        $invoice = FinanceInvoice::where('id',$invoice_id)->first();

        $due_date = new DateTime($invoice->due_date);
        $today = new DateTime(date("y-m-d"));

        if($today > $due_date){
            //echo $invoice->due_date;
            $days = $today->diff($due_date)->format("%a"); 
            //$result = "<font color='red'>".$days ." Day(s) Over Due</font>";
            $result = $days ." Day(s) Over Due";

        }   
        else{
            $days = $due_date->diff($today)->format("%a"); 
            $result = $days ." Day(s) Remaining";
        }

            return $result;

    }

    public function checkUnit($unit, $invoice_date){
        $check_unit = FinanceInvoice::where('unit_no',$unit)->where('due_date','>=',$invoice_date)->first();
        if(isset($check_unit))
            return 1;
        else
            return 0;

    }


    public function sendnotification($Unitinvoice) {
		
			$logo = url('/').'/public/assets/admin/img/aerea-logo.png';		
			
			$companyname = 'Aerea Home';
			$adminemail = 'otp@myaereahome.com';
			$replyto = 'no-reply@myaereahome.com';
            $primary_contact = User::where('role_id',2)->where('status',1)->where('primary_contact',1)->where('unit_no',$Unitinvoice->unit_no)->orderby('id','asc')->first();   
            $inv_no = isset($Unitinvoice->invoice_no);
            $date = isset($Unitinvoice->invoice_date)?date('d/m/y',strtotime($Unitinvoice->invoice_date)):'';
            $due_date = isset($Unitinvoice->due_date)?date('d/m/y',strtotime($Unitinvoice->due_date)):'';
            
            $building = isset($Unitinvoice->getunit->buildinginfo->id)?str_pad($Unitinvoice->getunit->buildinginfo->id, 4, '0', STR_PAD_LEFT):'';
            $unit =isset($Unitinvoice->getunit->unit)?$Unitinvoice->getunit->unit:'';
            $act_no = $building."-#".$unit;

            $advance_amount = isset($Unitinvoice->AdvancePayment->amount)?$Unitinvoice->AdvancePayment->amount:0;
            $invoice_amount  = $Unitinvoice->payable_amount - $advance_amount;
            $amount = number_format($invoice_amount,2);

            if(isset($primary_contact)){
                $first_name = $primary_contact->name;
                $last_name = isset($primary_contact->userinfo->last_name)?$primary_contact->userinfo->last_name:'';
                $name = $first_name." ".$last_name;
                $email = $primary_contact->email;
                
                $emailcontent = file_get_contents(public_path().'/emails/invoice.php');
                $emailcontent = str_replace('#logo#', $logo, $emailcontent);
                $emailcontent = str_replace('#companyname#', $companyname, $emailcontent);
                $emailcontent = str_replace('#date#', $date, $emailcontent);
                $emailcontent = str_replace('#name#', $name, $emailcontent);
                $emailcontent = str_replace('#duedate#', $due_date, $emailcontent);
                $emailcontent = str_replace('#act_no#', $act_no, $emailcontent);
                $emailcontent = str_replace('#amount#', $amount, $emailcontent);

                
                $subject = 'Your '.$companyname.' statement for '.$date.' is ready';
                
                $headers = 'From: '.$companyname.' <'.$adminemail.'/> ' . "\r\n" ;
                $headers .='Reply-To: '. $replyto . "\r\n" ;
                $headers .='X-Mailer: PHP/' . phpversion();
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";  
                $status = @mail($email, $subject, $emailcontent, $headers);
                return $status;
            }else{
                return false;
            }
		
    }
    
    public function excesspaymentdate($lastinvoice,$unit) {
        $ExcessInvoice = FinanceInvoice::where('id','<',$lastinvoice)->where('unit_no',$unit)->orderby('id','DESC')->first();
        //print_r($ExcessInvoice->id);
        $ExcessPaidDate ='';
        if(isset($ExcessInvoice)){
            $LastInvoicePayments = FinanceInvoicePayment::where('invoice_id',$ExcessInvoice->id)->orderby('id','DESC')->first();
            $ExcessPaidDate = $LastInvoicePayments->payment_received_date;
        }
        return $ExcessPaidDate;
    }


}
