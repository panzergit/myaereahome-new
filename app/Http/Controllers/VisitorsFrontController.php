<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Crypt;
use App\Models\v7\User;
use App\Models\v7\UserMoreInfo;
use App\Models\v7\Property;
use App\Models\v7\VisitorBooking;
use App\Models\v7\VisitorType;
use App\Models\v7\Defect;
use App\Models\v7\JoininspectionAppointment;
use App\Models\v7\EformMovingInOut;
use App\Models\v7\EformChangeAddress;
use App\Models\v7\EformDoorAccess;
use App\Models\v7\EformParticular;
use App\Models\v7\EformRegVehicle;
use App\Models\v7\EformRenovation;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\FinanceShareSetting;
use App\Models\v7\FinanceInvoice;
use App\Models\v7\FinanceInvoiceInfo;
use App\Models\v7\FinanceInvoicePayment;
use App\Models\v7\FinanceInvoicePaymentDetail;
use App\Models\v7\Unit;
use App\Models\v7\Building;
use Carbon\Carbon;
use PDF;

class VisitorsFrontController extends Controller
{
    /**
     * Handles Registration Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        return view('visitors.user.visitor-expired');
    }

    public function pre_registration($ticket)
    {
        $bookingObj = VisitorBooking::where('ticket', $ticket)->first();

        if (isset($bookingObj) && $bookingObj->id > 0)
        {
            $property = Property::find($bookingObj->account_id);
            $visiting_time = Carbon::now()->format('Y-m-d H:i:s');
            $today = Carbon::now()->format('Y-m-d');

            $visiting_purpose = VisitorType::where('account_id', $bookingObj->account_id)->where('id', $bookingObj->visiting_purpose)->first();
            if ($visiting_purpose->limit_set == 0) {
                $slot_available = 5;
            } else {
                $visitor_types = VisitorType::where('account_id', $property->id)->where('limit_set', 1)->where('status', 1)->get();
                $types = array();
                foreach ($visitor_types as $type) {
                    $types[] = $type->id;
                }

                $total_visitor = 0;
                $visitor_records = VisitorBooking::where('account_id', $bookingObj->account_id)->where('visiting_date', $bookingObj->visiting_date)->whereIn('visiting_purpose', $types)->whereIn('status', [0, 2])->get();
                foreach ($visitor_records as $records) {
                    $total_visitor += $records->visitors->count();
                }

                $slot_available = $property->visitors_allowed - $total_visitor;
            }
            if ($slot_available < 1)
                return view('visitors.user.visitor-message');
            else if ($visiting_time <= $bookingObj->visiting_end_time) {
                return view('visitors.user.visitor-registration', compact('bookingObj', 'property', 'slot_available'));
            } else {
                return view('visitors.user.visitor-expired');
            }
        } else {
            return view('visitors.user.visitor-expired');
        }
    }
    public function visitor_summary($ticket)
    {

        $bookingObj = VisitorBooking::where('ticket', $ticket)->first();

        if (isset($bookingObj) && $bookingObj->id > 0) {
            $property = Property::find($bookingObj->account_id);
            $visiting_time = Carbon::now()->format('Y-m-d H:i:s');

            $today = Carbon::now()->format('Y-m-d');

            $qrcode_path = image_storage_domain() . '/';
            
            if ($visiting_time <= $bookingObj->visiting_end_time) {
                return view('visitors.user.visitor-summary', compact('bookingObj', 'property', 'qrcode_path'));
            } else {
                if ($bookingObj->visiting_start_time > $visiting_time) {

                    return view('visitors.user.visitor-notactive');
                } else {
                    return view('visitors.user.visitor-expired');
                }
            }
        } else {
            return view('visitors.user.visitor-expired');
        }
    }

    public function generatePDF($id)
    {
        //$bookingObj = VisitorBooking::where('ticket',$ticket)->first();

        $defectObj = Defect::find($id);
        $inspectionObj = JoininspectionAppointment::where('def_id', $id)->orderby('id', 'desc')->first();

        $obj = new JoininspectionAppointment();

        $file_path = image_storage_domain();

        $signatureUserName = User::find($defectObj->user_id)->name ?? null;
        $signatureUserName = $signatureUserName != null ? Crypt::decryptString($signatureUserName) : null;
        $signatureUserTime = \Carbon\Carbon::parse($defectObj->created_at)->format('d/m/y h:i:s A');

        $inspectedOwnerName = User::find($defectObj->inspection_owner_user)->name ?? null;
        $inspectedOwnerName = $inspectedOwnerName != null ? Crypt::decryptString($inspectedOwnerName) : null;
        $inspectedOwnerTime = \Carbon\Carbon::parse($defectObj->inspection_owner_timestamp)->format('d/m/y h:i:s A');

        $inspectedTeamName = User::find($defectObj->inspection_team_user)->name ?? null;
        $inspectedTeamName = $inspectedTeamName != null ? Crypt::decryptString($inspectedTeamName) : null;
        $inspectedTeamTime = \Carbon\Carbon::parse($defectObj->inspection_team_timestamp)->format('d/m/y h:i:s A');

        $handOverOwnerName = User::find($defectObj->handover_owner_user)->name ?? null;
        $handOverOwnerName = $handOverOwnerName != null ? Crypt::decryptString($handOverOwnerName) : null;
        $handOverOwnerTime = \Carbon\Carbon::parse($defectObj->handover_owner_timestamp)->format('d/m/y h:i:s A');

        $handOverTeamName = User::find($defectObj->handover_team_user)->name ?? null;
        $handOverTeamName = $handOverTeamName != null ? Crypt::decryptString($handOverTeamName) : null;
        $handOverTeamTime = \Carbon\Carbon::parse($defectObj->handover_team_timestamp)->format('d/m/y h:i:s A');


        $pdf = PDF::loadview('visitors.defect.defect', compact(
            'defectObj',
            'file_path',
            'inspectionObj',
            'inspectedOwnerName',
            'inspectedOwnerTime',
            'inspectedTeamName',
            'inspectedTeamTime',
            'handOverOwnerName',
            'handOverOwnerTime',
            'handOverTeamName',
            'handOverTeamTime',
            'signatureUserName',
            'signatureUserTime'
        ));

        $pdf_name = "Ticket-" . $defectObj->ticket . ".pdf";

        return $pdf->stream($pdf_name);
    }

    public function batchinvoices($id)
    {
        $batch_id = $id;
        $invoice = FinanceInvoiceInfo::find($id);
        $BatchInvoices = FinanceInvoice::where('info_id', $id)->get();
        $invoicesLists = array();
        $account_id = $invoice->account_id;
        $sharesettings = FinanceShareSetting::where("account_id", $account_id)->where('status', 1)->orderby('id', 'desc')->first();
        $qr_file = '';
        if ($sharesettings->qrcode_file != '') $qr_file = image_storage_domain()."/" . $sharesettings->qrcode_file;
        
        foreach ($BatchInvoices as $Unitinvoice) {
            $id = $Unitinvoice->id;
            $Unitinvoice = FinanceInvoice::find($id);
            $account_id = $Unitinvoice->account_id;
            $buildings = Building::where('account_id', $account_id)->get();
            $currentDetails = FinanceInvoicePaymentDetail::where('invoice_id', $Unitinvoice->id)->where("reference_invoice", $Unitinvoice->id)->orderby('id', 'asc')->get();
            $LastInvoice = FinanceInvoice::where('id', '<', $id)->where('unit_no', $Unitinvoice->unit_no)->orderby('id', 'desc')->first();
            if (isset($LastInvoice)) {
                $LastInvoicePayments = FinanceInvoicePayment::where('invoice_id', $LastInvoice->id)->orderby('id', 'asc')->get();
                $previousDetails = FinanceInvoicePaymentDetail::where('invoice_id', $LastInvoice->id)->where("reference_invoice", '!=', $Unitinvoice->id)->orderby('id', 'asc')->get();
            } else {
                $LastInvoicePayments = array();
                $previousDetails = array();
            }
            $CurrentInvoicePayments = FinanceInvoicePayment::where('invoice_id', $Unitinvoice->id)->orderby('id', 'asc')->get();
            $UserPurchaserRecords = UserPurchaserUnit::where('property_id', $account_id)->where('unit_id', $Unitinvoice->unit_no)->where('role_id', 2)->where('status', 1)->orderby('id', 'asc')->get();
            $UserPurchaserLists = array();
            if (isset($UserPurchaserRecords)) {
                //echo "Purchaser contact";
                foreach ($UserPurchaserRecords as $UserPurchaserRecord) {
                    $UserPurchaserLists[] = $UserPurchaserRecord->user_info_id;
                }
            }
            $total_rows = 0;
            if (isset($previousDetails)) {
                foreach ($previousDetails as $k => $detail) {
                    if ($detail->total_amount > 0 && $detail->status == 0)
                        $total_rows++;
                }
            }
            if (isset($LastInvoicePayments)) {
                foreach ($LastInvoicePayments as $LastInvoicePayment) {
                    $total_rows++;
                }
            }
            if (isset($currentDetails)) {
                foreach ($currentDetails as $k => $detail) {
                    if ($detail->total_amount > 0 && $detail->status == 0)
                        $total_rows++;
                }
            }
            if (isset($CurrentInvoicePayments)) {
                foreach ($CurrentInvoicePayments as $CurrentInvoicePayment) {
                    $total_rows++;
                }
            }
            $total_rows + 1;
            $totalpages =  $total_rows / 20;
            $total_pages =  ceil($totalpages);
            //print_r($UserPurchaserLists);
            $purchasers = UserMoreInfo::WhereIn('id', $UserPurchaserLists)->where('status', 1)->orderby('id', 'asc')->get();

            $unitPrimaryContactRec = UserPurchaserUnit::where('property_id', $account_id)->where('unit_id', $Unitinvoice->unit_no)->where('primary_contact', 1)->where('role_id', 2)->where('status', 1)->orderby('id', 'asc')->first();
            $primayContactIds = array();
            if ($unitPrimaryContactRec) {
                //echo "primary contact";
                $primayContactIds[] = $unitPrimaryContactRec->user_info_id;
            }
            $primary_contact = UserMoreInfo::WhereIn('id', $primayContactIds)->where('status', 1)->orderby('id', 'asc')->first();


            //$purchasers = User::where('role_id',2)->where('status',1)->where('unit_no',$Unitinvoice->unit_no)->orderby('id','asc')->get();   
            //$primary_contact = User::where('role_id',2)->where('status',1)->where('primary_contact',1)->where('unit_no',$Unitinvoice->unit_no)->orderby('id','asc')->first();   
            $result_array['Unitinvoice'] = $Unitinvoice;
            $result_array['invoice'] = $invoice;
            $result_array['buildings'] = $buildings;
            $result_array['LastInvoice'] = $LastInvoice;
            $result_array['LastInvoicePayments'] = $LastInvoicePayments;
            $result_array['CurrentInvoicePayments'] = $CurrentInvoicePayments;
            $result_array['previousDetails'] = $previousDetails;
            $result_array['currentDetails'] = $currentDetails;
            $result_array['purchasers'] = $purchasers;
            $result_array['primary_contact'] = $primary_contact;
            $result_array['total_pages'] = $total_pages;

            $amount_received = 0;
            if ($Unitinvoice->payments) {
                foreach ($Unitinvoice->payments as $k => $payment) {
                    \Log::info('Payment Id => ' . $payment->id);
                    if ($payment->payment_option == 1 && $payment->status != 2 && $payment->cheque_amount > 0 && is_numeric($payment->cheque_amount))
                        $amount_received += $payment->cheque_amount;
                    else if ($payment->payment_option == 2 && is_numeric($payment->bt_amount_received))
                        $amount_received += $payment->bt_amount_received;
                    else if ($payment->payment_option == 5 && is_numeric($payment->online_amount_received))
                        $amount_received += $payment->online_amount_received;
                    else if ($payment->payment_option == 6 && is_numeric($payment->credit_amount))
                        $amount_received += $payment->credit_amount;
                    else if ($payment->payment_option == 7 && is_numeric($payment->add_amt_received))
                        $amount_received += $payment->add_amt_received;
                    else if (is_numeric($payment->cash_amount_received))
                        $amount_received += $payment->cash_amount_received;
                }
            }


            //$amount_received = number_format($amount_received,2);
            $balance_amount = ($Unitinvoice->payable_amount - $amount_received);
            $result_array['balance_amount'] = $balance_amount;
            $invoicesLists[] = $result_array;
        }
        $file_path = image_storage_domain();
        $print_date = date("d/m/y");

        $pdf = PDF::loadview('visitors.finance.batchpdf', compact('invoicesLists', 'invoice', 'print_date', 'qr_file'));

        $pdf_name = "Batch-" . $invoice->batch_no . "-Invoices.pdf";

        return $pdf->stream($pdf_name);
    }

    public function invoicePDF($id)
    {
        $Unitinvoice = FinanceInvoice::find($id);
        $account_id = $Unitinvoice->account_id;
        $sharesettings = FinanceShareSetting::where("account_id", $account_id)->where('status', 1)->orderby('id', 'desc')->first();
        $qr_file = '';
        if ($sharesettings->qrcode_file != '') $qr_file = image_storage_domain()."/" . $sharesettings->qrcode_file;

        $buildings = Building::where('account_id', $account_id)->get();
        $invoice = FinanceInvoiceInfo::find($Unitinvoice->info_id);
        $currentDetails = FinanceInvoicePaymentDetail::where('invoice_id', $Unitinvoice->id)->where("reference_invoice", $Unitinvoice->id)->orderby('id', 'asc')->get();
        $CurrentInvoicePayments = FinanceInvoicePayment::where('invoice_id', $Unitinvoice->id)->orderby('id', 'asc')->get();
        $LastInvoice = FinanceInvoice::where('id', '<', $id)->where('unit_no', $Unitinvoice->unit_no)->orderby('id', 'desc')->first();
        if (isset($LastInvoice)) {
            $LastInvoicePayments = FinanceInvoicePayment::where('invoice_id', $LastInvoice->id)->orderby('id', 'asc')->get();
            $previousDetails = FinanceInvoicePaymentDetail::where('invoice_id', $LastInvoice->id)->where("reference_invoice", '!=', $Unitinvoice->id)->orderby('id', 'asc')->get();
        } else {
            $LastInvoicePayments = array();
            $previousDetails = array();
        }
        $total_rows = 0;
        if (isset($previousDetails)) {
            foreach ($previousDetails as $k => $detail) {
                if ($detail->total_amount > 0 && $detail->status == 0)
                    $total_rows++;
            }
        }
        if (isset($LastInvoicePayments)) {
            foreach ($LastInvoicePayments as $LastInvoicePayment) {
                $total_rows++;
            }
        }
        if (isset($currentDetails)) {
            foreach ($currentDetails as $k => $detail) {
                if ($detail->total_amount > 0 && $detail->status == 0)
                    $total_rows++;
            }
        }
        if (isset($CurrentInvoicePayments)) {
            foreach ($CurrentInvoicePayments as $CurrentInvoicePayment) {
                $total_rows++;
            }
        }
        $total_rows + 1;
        $totalpages =  $total_rows / 20;
        $total_pages =  ceil($totalpages);



        $UserPurchaserRecords = UserPurchaserUnit::where('property_id', $account_id)->where('unit_id', $Unitinvoice->unit_no)->where('role_id', 2)->where('status', 1)->orderby('id', 'asc')->get();
        $UserPurchaserLists = array();
        if (isset($UserPurchaserRecords)) {
            //echo "Purchaser contact";
            foreach ($UserPurchaserRecords as $UserPurchaserRecord) {
                $UserPurchaserLists[] = $UserPurchaserRecord->user_info_id;
            }
        }
        //print_r($UserPurchaserLists);
        $purchasers = UserMoreInfo::WhereIn('id', $UserPurchaserLists)->where('status', 1)->orderby('id', 'asc')->get();
        //print_r($purchasers->first_name);
        $unitPrimaryContactRecs = UserPurchaserUnit::where('property_id', $account_id)->where('unit_id', $Unitinvoice->unit_no)->where('primary_contact', 1)->where('role_id', 2)->where('status', 1)->orderby('id', 'asc')->get();
        $primayContactIds = array();
        if ($unitPrimaryContactRecs) {
            //echo "primary contact";
            foreach ($unitPrimaryContactRecs as $unitPrimaryContactRec) {
                $primayContactIds[] = $unitPrimaryContactRec->user_info_id;
            }
        }
        //print_r($primayContactIds);
        $primary_contact = UserMoreInfo::WhereIn('id', $primayContactIds)->where('status', 1)->orderby('id', 'asc')->first();


        //$purchasers = User::where('role_id',2)->where('status',1)->where('unit_no',$Unitinvoice->unit_no)->orderby('id','asc')->get();   
        //$primary_contact = User::where('role_id',2)->where('status',1)->where('primary_contact',1)->where('unit_no',$Unitinvoice->unit_no)->orderby('id','asc')->first();   

        $amount_received = 0;
        if ($Unitinvoice->payments) {
            foreach ($Unitinvoice->payments as $k => $payment) {
                if ($payment->payment_option == 1 && $payment->status != 2)
                    $amount_received += $payment->cheque_amount;
                else if ($payment->payment_option == 2)
                    $amount_received += $payment->bt_amount_received;
                else if ($payment->payment_option == 5)
                    $amount_received += $payment->online_amount_received;
                else if ($payment->payment_option == 6)
                    $amount_received += $payment->credit_amount;
                else if ($payment->payment_option == 7)
                    $amount_received += $payment->add_amt_received;
                else
                    $amount_received += $payment->cash_amount_received;
            }
        }
        //$amount_received = number_format($amount_received,2);
        $balance_amount = ($Unitinvoice->payable_amount - $amount_received);


        $print_date = date("d/m/y");
        $file_path = image_storage_domain();
        $pdf = PDF::loadview('visitors.finance.newview', compact('Unitinvoice', 'buildings', 'purchasers', 'invoice', 'LastInvoice', 'primary_contact', 'LastInvoicePayments', 'previousDetails', 'currentDetails', 'CurrentInvoicePayments', 'balance_amount', 'print_date', 'total_pages', 'qr_file'));
        $pdf_name = "invoice-" . $Unitinvoice->invoice_no . ".pdf";
        return $pdf->stream($pdf_name);
    }

    public function setPdfRows(Request $request)
    {
        $height = $request->has('height') ? $request->height : 0;
        switch ($height != 0) {
            case $height < 100:
                $rows = 30;
                break;
            case $height >= 100 && $height <= 200:
                $rows = 25;
                break;
            case $height >= 200 && $height <= 300:
                $rows = 15;
                break;
            case $height > 300:
                $rows = 10;
                break;
            default:
                $rows = 5;
                break;
        }
        session()->put('pdfCustomRows', $rows);
        return response()->json([
            'status' => $height > 0
        ]);
    }

    public function testinvoicePDF(Request $request, $id)
    {
        $Unitinvoice = FinanceInvoice::find($id);
        $account_id = $Unitinvoice->account_id;
        $sharesettings = FinanceShareSetting::where("account_id", $account_id)->where('status', 1)->orderby('id', 'desc')->first();
        $qr_file = '';
        if ($sharesettings->qrcode_file != '') $qr_file = image_storage_domain()."/" . $sharesettings->qrcode_file;

        $buildings = Building::where('account_id', $account_id)->get();
        $invoice = FinanceInvoiceInfo::find($Unitinvoice->info_id);
        $currentDetails = FinanceInvoicePaymentDetail::where('invoice_id', $Unitinvoice->id)->where("reference_invoice", $Unitinvoice->id)->orderby('id', 'asc')->get();
        $CurrentInvoicePayments = FinanceInvoicePayment::where('invoice_id', $Unitinvoice->id)->orderby('id', 'asc')->get();
        $LastInvoice = FinanceInvoice::where('id', '<', $id)->where('unit_no', $Unitinvoice->unit_no)->orderby('id', 'desc')->first();
        if (isset($LastInvoice)) {
            $LastInvoicePayments = FinanceInvoicePayment::where('invoice_id', $LastInvoice->id)->orderby('id', 'asc')->get();
            $previousDetails = FinanceInvoicePaymentDetail::where('invoice_id', $LastInvoice->id)->where("reference_invoice", '!=', $Unitinvoice->id)->orderby('id', 'asc')->get();
        } else {
            $LastInvoicePayments = array();
            $previousDetails = array();
        }
        $total_rows = 0;
        if (isset($previousDetails)) {
            foreach ($previousDetails as $k => $detail) {
                if ($detail->total_amount > 0 && $detail->status == 0)
                    $total_rows++;
            }
        }
        if (isset($LastInvoicePayments)) {
            foreach ($LastInvoicePayments as $LastInvoicePayment) {
                $total_rows++;
            }
        }
        if (isset($currentDetails)) {
            foreach ($currentDetails as $k => $detail) {
                if ($detail->total_amount > 0 && $detail->status == 0)
                    $total_rows++;
            }
        }
        if (isset($CurrentInvoicePayments)) {
            foreach ($CurrentInvoicePayments as $CurrentInvoicePayment) {
                $total_rows++;
            }
        }
        $total_rows + 1;
        $totalpages =  $total_rows / 20;
        $total_pages =  ceil($totalpages);



        $UserPurchaserRecords = UserPurchaserUnit::where('property_id', $account_id)->where('unit_id', $Unitinvoice->unit_no)->where('role_id', 2)->where('status', 1)->orderby('id', 'asc')->get();
        $UserPurchaserLists = array();
        if (isset($UserPurchaserRecords)) {
            //echo "Purchaser contact";
            foreach ($UserPurchaserRecords as $UserPurchaserRecord) {
                $UserPurchaserLists[] = $UserPurchaserRecord->user_info_id;
            }
        }
        //print_r($UserPurchaserLists);
        $purchasers = UserMoreInfo::WhereIn('id', $UserPurchaserLists)->where('status', 1)->orderby('id', 'asc')->get();
        //print_r($purchasers->first_name);
        $unitPrimaryContactRecs = UserPurchaserUnit::where('property_id', $account_id)->where('unit_id', $Unitinvoice->unit_no)->where('primary_contact', 1)->where('role_id', 2)->where('status', 1)->orderby('id', 'asc')->get();
        $primayContactIds = array();
        if ($unitPrimaryContactRecs) {
            //echo "primary contact";
            foreach ($unitPrimaryContactRecs as $unitPrimaryContactRec) {
                $primayContactIds[] = $unitPrimaryContactRec->user_info_id;
            }
        }
        //print_r($primayContactIds);
        $primary_contact = UserMoreInfo::WhereIn('id', $primayContactIds)->where('status', 1)->orderby('id', 'asc')->first();


        //$purchasers = User::where('role_id',2)->where('status',1)->where('unit_no',$Unitinvoice->unit_no)->orderby('id','asc')->get();   
        //$primary_contact = User::where('role_id',2)->where('status',1)->where('primary_contact',1)->where('unit_no',$Unitinvoice->unit_no)->orderby('id','asc')->first();   

        $amount_received = 0;
        if ($Unitinvoice->payments) {
            foreach ($Unitinvoice->payments as $k => $payment) {
                if ($payment->payment_option == 1 && $payment->status != 2)
                    $amount_received += $payment->cheque_amount;
                else if ($payment->payment_option == 2)
                    $amount_received += $payment->bt_amount_received;
                else if ($payment->payment_option == 5)
                    $amount_received += $payment->online_amount_received;
                else if ($payment->payment_option == 6)
                    $amount_received += $payment->credit_amount;
                else if ($payment->payment_option == 7)
                    $amount_received += $payment->add_amt_received;
                else
                    $amount_received += $payment->cash_amount_received;
            }
        }
        //$amount_received = number_format($amount_received,2);
        $balance_amount = ($Unitinvoice->payable_amount - $amount_received);


        $print_date = date("d/m/y");
        $file_path = image_storage_domain();
        $showPdf = 0;
        if (session()->has('pdfCustomRows')) {
            $showPdf = 1;
            $pdfCustomRows = session()->get('pdfCustomRows');
            $request->session()->forget('pdfCustomRows');
            $pdf = PDF::loadview('visitors.finance.testview', compact('Unitinvoice', 'buildings', 'purchasers', 'invoice', 'LastInvoice', 'primary_contact', 'LastInvoicePayments', 'previousDetails', 'currentDetails', 'CurrentInvoicePayments', 'balance_amount', 'print_date', 'total_pages', 'qr_file', 'pdfCustomRows', 'showPdf'));
            $pdf_name = "invoice-" . $Unitinvoice->invoice_no . ".pdf";
            return $pdf->stream($pdf_name);
        } else {
            return view('visitors.finance.testview', compact('Unitinvoice', 'buildings', 'purchasers', 'invoice', 'LastInvoice', 'primary_contact', 'LastInvoicePayments', 'previousDetails', 'currentDetails', 'CurrentInvoicePayments', 'balance_amount', 'print_date', 'total_pages', 'qr_file', 'showPdf'));
        }
    }

    public function consolidatedPrint(request $request)
    {
        $unit_id = $request->input('unit_id');
        $Invoices = explode(",", $request->input('invoice_ids'));
        $latestInvoice = FinanceInvoice::where('unit_no', $unit_id)->orderby('id', 'desc')->first();

        if (isset($latestInvoice)) {
            $amount_received = 0;
            if ($latestInvoice->payments) {
                foreach ($latestInvoice->payments as $k => $payment) {
                    if ($payment->payment_option == 1 && $payment->status != 2)
                        $amount_received += $payment->cheque_amount;
                    else if ($payment->payment_option == 2)
                        $amount_received += $payment->bt_amount_received;
                    else if ($payment->payment_option == 5)
                        $amount_received += $payment->online_amount_received;
                    else if ($payment->payment_option == 6)
                        $amount_received += $payment->credit_amount;
                    else if ($payment->payment_option == 7)
                        $amount_received += $payment->add_amt_received;
                    else
                        $amount_received += $payment->cash_amount_received;
                }
            }
            //echo $latestInvoice->payable_amount;
            //echo $amount_received = number_format($amount_received,2);
            $latest_amount = ($latestInvoice->payable_amount - $amount_received);
            if ($latest_amount <= 0)
                $latest_balance_amount = number_format(0, 2);
            else
                $latest_balance_amount = number_format($latest_amount, 2);
        }


        $invoicesLists = array();
        $unitObj = Unit::find($unit_id);
        $total_rows = 0;
        foreach ($Invoices as $Unitinvoice) {
            $id = $Unitinvoice;
            $Unitinvoice = FinanceInvoice::find($id);
            $invoice = FinanceInvoiceInfo::find($Unitinvoice->info_id);
            $account_id = $unitObj->account_id;
            $buildings = Building::where('account_id', $account_id)->get();
            $currentDetails = FinanceInvoicePaymentDetail::where('invoice_id', $id)->where("reference_invoice", $id)->orderby('id', 'asc')->get();
            $LastInvoice = FinanceInvoice::where('id', '<', $id)->where('unit_no', $unit_id)->orderby('id', 'desc')->first();
            if (isset($LastInvoice)) {
                $LastInvoicePayments = FinanceInvoicePayment::where('invoice_id', $LastInvoice->id)->orderby('id', 'asc')->get();
                $previousDetails = FinanceInvoicePaymentDetail::where('invoice_id', $LastInvoice->id)->where("reference_invoice", '!=', $Unitinvoice->id)->orderby('id', 'asc')->get();
            } else {
                $LastInvoicePayments = array();
                $previousDetails = array();
            }
            $CurrentInvoicePayments = FinanceInvoicePayment::where('invoice_id', $id)->orderby('id', 'asc')->get();
            $UserPurchaserRecords = UserPurchaserUnit::where('property_id', $account_id)->where('unit_id', $unit_id)->where('role_id', 2)->where('status', 1)->orderby('id', 'asc')->get();
            $UserPurchaserLists = array();
            if (isset($UserPurchaserRecords)) {
                //echo "Purchaser contact";
                foreach ($UserPurchaserRecords as $UserPurchaserRecord) {
                    $UserPurchaserLists[] = $UserPurchaserRecord->user_info_id;
                }
            }


            if (isset($previousDetails)) {
                foreach ($previousDetails as $k => $detail) {
                    if ($detail->total_amount > 0 && $detail->status == 0)
                        $total_rows++;
                }
            }
            if (isset($LastInvoicePayments)) {
                foreach ($LastInvoicePayments as $LastInvoicePayment) {
                    $total_rows++;
                }
            }
            if (isset($currentDetails)) {
                foreach ($currentDetails as $k => $detail) {
                    if ($detail->total_amount > 0 && $detail->status == 0)
                        $total_rows++;
                }
            }
            if (isset($CurrentInvoicePayments)) {
                foreach ($CurrentInvoicePayments as $CurrentInvoicePayment) {
                    $total_rows++;
                }
            }
            $total_rows + 2;

            //print_r($UserPurchaserLists);
            $purchasers = UserMoreInfo::WhereIn('id', $UserPurchaserLists)->where('status', 1)->orderby('id', 'asc')->get();

            $unitPrimaryContactRec = UserPurchaserUnit::where('property_id', $account_id)->where('unit_id', $unit_id)->where('primary_contact', 1)->where('role_id', 2)->where('status', 1)->orderby('id', 'asc')->first();
            $primayContactIds = array();
            if ($unitPrimaryContactRec) {
                //echo "primary contact";
                $primayContactIds[] = $unitPrimaryContactRec->user_info_id;
            }
            $primary_contact = UserMoreInfo::WhereIn('id', $primayContactIds)->where('status', 1)->orderby('id', 'asc')->first();


            //$purchasers = User::where('role_id',2)->where('status',1)->where('unit_no',$Unitinvoice->unit_no)->orderby('id','asc')->get();   
            //$primary_contact = User::where('role_id',2)->where('status',1)->where('primary_contact',1)->where('unit_no',$Unitinvoice->unit_no)->orderby('id','asc')->first();   
            $result_array['InvoiceInfo'] = $Unitinvoice;
            $result_array['InvoiceMoreInfo'] = $invoice;
            $result_array['LastInvoice'] = $LastInvoice;
            $result_array['LastInvoicePayments'] = $LastInvoicePayments;
            $result_array['CurrentInvoicePayments'] = $CurrentInvoicePayments;

            $result_array['previousDetails'] = $previousDetails;
            $result_array['currentDetails'] = $currentDetails;

            $amount_received = 0;
            if ($Unitinvoice->payments) {
                foreach ($Unitinvoice->payments as $k => $payment) {
                    if ($payment->payment_option == 1 && $payment->status != 2)
                        $amount_received += $payment->cheque_amount;
                    else if ($payment->payment_option == 2)
                        $amount_received += $payment->bt_amount_received;
                    else if ($payment->payment_option == 5)
                        $amount_received += $payment->online_amount_received;
                    else if ($payment->payment_option == 6)
                        $amount_received += $payment->credit_amount;
                    else if ($payment->payment_option == 7)
                        $amount_received += $payment->add_amt_received;
                    else
                        $amount_received += $payment->cash_amount_received;
                }
            }
            //$amount_received = number_format($amount_received,2);
            $balance_amount = ($Unitinvoice->payable_amount - $amount_received);
            $result_array['balance_amount'] = $balance_amount;
            $invoicesLists[] = $result_array;
        }
        $sharesettings = FinanceShareSetting::where("account_id", $account_id)->where('status', 1)->orderby('id', 'desc')->first();
        $qr_file = '';
        if ($sharesettings->qrcode_file != '') $qr_file = image_storage_domain()."/" . $sharesettings->qrcode_file;

        $totalpages =  $total_rows / 20;
        $total_pages =  ceil($totalpages);

        $file_path = image_storage_domain();
        $print_date = date("d/m/y");

        $pdf = PDF::loadview('visitors.finance.newconsolidated', compact('invoicesLists', 'invoice', 'print_date', 'Unitinvoice', 'invoice', 'buildings', 'purchasers', 'primary_contact', 'total_pages', 'qr_file', 'latest_balance_amount'));

        $pdf_name = "Report-" . $unit_id . "-Invoices.pdf";

        return $pdf->stream($pdf_name);
    }
    public function trailinvoicePDF($id)
    {
        $Unitinvoice = FinanceInvoice::find($id);
        $account_id = $Unitinvoice->account_id;
        $buildings = Building::where('account_id', $account_id)->get();
        $invoice = FinanceInvoiceInfo::find($Unitinvoice->info_id);
        $currentDetails = FinanceInvoicePaymentDetail::where('invoice_id', $Unitinvoice->id)->where("reference_invoice", $Unitinvoice->id)->orderby('id', 'asc')->get();
        $CurrentInvoicePayments = FinanceInvoicePayment::where('invoice_id', $Unitinvoice->id)->orderby('id', 'asc')->get();
        $LastInvoice = FinanceInvoice::where('id', '<', $id)->where('unit_no', $Unitinvoice->unit_no)->orderby('id', 'desc')->first();
        if (isset($LastInvoice)) {
            $LastInvoicePayments = FinanceInvoicePayment::where('invoice_id', $LastInvoice->id)->orderby('id', 'asc')->get();
            $previousDetails = FinanceInvoicePaymentDetail::where('invoice_id', $LastInvoice->id)->where("reference_invoice", '!=', $Unitinvoice->id)->orderby('id', 'asc')->get();
        } else {
            $LastInvoicePayments = array();
            $previousDetails = array();
        }

        $UserPurchaserRecords = UserPurchaserUnit::where('property_id', $account_id)->where('unit_id', $Unitinvoice->unit_no)->where('role_id', 2)->where('status', 1)->orderby('id', 'asc')->get();
        $UserPurchaserLists = array();
        if (isset($UserPurchaserRecords)) {
            //echo "Purchaser contact";
            foreach ($UserPurchaserRecords as $UserPurchaserRecord) {
                $UserPurchaserLists[] = $UserPurchaserRecord->user_id;
            }
        }
        //print_r($UserPurchaserLists);
        $purchasers = User::WhereIn('id', $UserPurchaserLists)->where('status', 1)->orderby('id', 'asc')->get();

        $unitPrimaryContactRec = UserPurchaserUnit::where('property_id', $account_id)->where('unit_id', $Unitinvoice->unit_no)->where('primary_contact', 1)->where('role_id', 2)->where('status', 1)->orderby('id', 'asc')->first();
        $primayContactIds = array();
        if ($unitPrimaryContactRec) {
            //echo "primary contact";
            $primayContactIds[] = $unitPrimaryContactRec->user_id;
        }
        $primary_contact = User::WhereIn('id', $primayContactIds)->where('status', 1)->where('unit_no', $Unitinvoice->unit_no)->orderby('id', 'asc')->first();


        //$purchasers = User::where('role_id',2)->where('status',1)->where('unit_no',$Unitinvoice->unit_no)->orderby('id','asc')->get();   
        //$primary_contact = User::where('role_id',2)->where('status',1)->where('primary_contact',1)->where('unit_no',$Unitinvoice->unit_no)->orderby('id','asc')->first();   

        $amount_received = 0;
        if ($Unitinvoice->payments) {
            foreach ($Unitinvoice->payments as $k => $payment) {
                if ($payment->payment_option == 1 && $payment->status != 2)
                    $amount_received += $payment->cheque_amount;
                else if ($payment->payment_option == 2)
                    $amount_received += $payment->bt_amount_received;
                else if ($payment->payment_option == 5)
                    $amount_received += $payment->online_amount_received;
                else if ($payment->payment_option == 6)
                    $amount_received += $payment->credit_amount;
                else
                    $amount_received += $payment->cash_amount_received;
            }
        }
        //$amount_received = number_format($amount_received,2);
        $balance_amount = ($Unitinvoice->payable_amount - $amount_received);
        $print_date = date("d/m/y");
        $file_path = image_storage_domain();
        $pdf = PDF::loadview('visitors.finance.trailview', compact('Unitinvoice', 'buildings', 'purchasers', 'invoice', 'LastInvoice', 'primary_contact', 'LastInvoicePayments', 'previousDetails', 'currentDetails', 'CurrentInvoicePayments', 'balance_amount', 'print_date'));
        $pdf_name = "invoice-" . $Unitinvoice->invoice_no . ".pdf";
        return $pdf->stream($pdf_name);
    }

    public function paymentPDF($id)
    {
        $invoiceObj = FinanceInvoice::find($id);
        $account_id = $invoiceObj->account_id;
        $amount_received = 0;
        if ($invoiceObj->payments) {
            foreach ($invoiceObj->payments as $k => $payment) {
                if ($payment->payment_option == 1 && $payment->status != 2)
                    $amount_received += $payment->cheque_amount;
                else if ($payment->payment_option == 2)
                    $amount_received += $payment->bt_amount_received;
                else if ($payment->payment_option == 5)
                    $amount_received += $payment->online_amount_received;
                else if ($payment->payment_option == 6)
                    $amount_received += $payment->credit_amount;
                else
                    $amount_received += $payment->cash_amount_received;
            }
        }
        //$amount_received = number_format($amount_received,2);
        $balance_amount = ($invoiceObj->payable_amount - $amount_received);

        $invoiceLists = FinanceInvoice::where('unit_no', $invoiceObj->unit_no)->where('id', '!=', $id)->get();
        $invInIds = array();
        if ($invoiceLists) {
            foreach ($invoiceLists as $k => $invoiceList) {
                $invInIds[] = $invoiceList->id;
            }
        }
        $paymentHistory = FinanceInvoicePayment::whereIn('invoice_id', $invInIds)->get();
        $file_path = image_storage_domain();
        $pdf = PDF::loadview('visitors.finance.payment', compact('invoiceObj', 'amount_received', 'balance_amount', 'paymentHistory'));
        $pdf_name = "invoice-payment-" . $invoiceObj->invoice_no . ".pdf";
        return $pdf->stream($pdf_name);
    }

    public function moveinginoutPDF($id)
    {
        $eformObj = EformMovingInOut::find($id);
        $file_path = image_storage_domain();
        $pdf = PDF::loadView('visitors.eform.movinginout', compact('eformObj', 'file_path'));
        $pdf_name = "MoveInOut-Ticket-" . $eformObj->ticket . ".pdf";
        return $pdf->stream($pdf_name);
    }

    public function renovationPDF($id)
    {
        $eformObj = EformRenovation::find($id);
        $file_path = image_storage_domain();
        $pdf = PDF::loadView('visitors.eform.renovation', compact('eformObj', 'file_path'));
        $pdf_name = "Renovation-Ticket-" . $eformObj->ticket . ".pdf";
        return $pdf->stream($pdf_name);
    }

    public function dooraccessPDF($id)
    {
        $eformObj = EformDoorAccess::find($id);
        $file_path = image_storage_domain();
        $pdf = PDF::loadView('visitors.eform.dooraccess', compact('eformObj', 'file_path'));
        $pdf_name = "DoorAccessCard-Ticket-" . $eformObj->ticket . ".pdf";
        return $pdf->stream($pdf_name);
    }

    public function vehicleiuPDF($id)
    {
        $eformObj = EformRegVehicle::find($id);
        $file_path = image_storage_domain();
        $pdf = PDF::loadView('visitors.eform.vehicle', compact('eformObj', 'file_path'));
        $pdf_name = "RegisterVehicleIU-Ticket-" . $eformObj->ticket . ".pdf";
        return $pdf->stream($pdf_name);
    }

    public function addressPDF($id)
    {
        $eformObj = EformChangeAddress::find($id);
        $file_path = image_storage_domain();
        $pdf = PDF::loadView('visitors.eform.address', compact('eformObj', 'file_path'));
        $pdf_name = "AddressUpdate-Ticket-" . $eformObj->ticket . ".pdf";
        return $pdf->stream($pdf_name);
    }

    public function particularsPDF($id)
    {
        $eformObj = EformParticular::find($id);
        $file_path = image_storage_domain();
        $pdf = PDF::loadView('visitors.eform.particular', compact('eformObj', 'file_path'));
        $pdf_name = "ParticularsUpdate-Ticket-" . $eformObj->ticket . ".pdf";
        return $pdf->stream($pdf_name);
    }
}
