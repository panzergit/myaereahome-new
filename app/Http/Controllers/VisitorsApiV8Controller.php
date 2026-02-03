<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\v7\User;
use App\Models\v7\VisitorType;
use App\Models\v7\VisitorBooking;
use App\Models\v7\VisitorList;
use App\Models\v7\Property;
use App\Models\v7\Unit;
use App\Models\v7\Device;
use App\Models\v7\VisitorInviteEmailList;
use App\Models\v7\QrcodeOpenRecord;
use App\Models\v7\InboxMessage;
use App\Models\v7\UserManagerLog;
use App\Models\v7\FirebaseNotification;
use App\Models\v7\UserNotification;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\UserLog;
use App\Models\v7\UserNotificationSetting;

use Carbon\Carbon;
use Auth;
use DB;
use QrCode;
use Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class VisitorsApiV8Controller extends Controller
{
    public function visitingPurpose(Request $request)
    {
        $rules = ['property' => 'required'];
        $messages = ['property.required' => 'Property id missing'];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }

        $input = $request->all();
        $account_id = $input['property'];
        $data = VisitorType::where('account_id', $account_id)->get();
        return response()->json(['data' => $data, 'response' => 1, 'message' => 'success!']);
    }

    public function visitorRegisitration(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'visiting_date' => 'required',
            'visiting_purpose' => 'required',
            'name_1' => 'required',
            'mobile_1' => 'required',
        ];

        $messages = [
            'user_id.required' => 'User id missing',
            'visiting_date.required' => 'Date is missing',
            'visiting_purpose.required' => 'Purpose of visit is missing',
            'name_1.required' => 'Name is missing',
            'mobile_1.required' => 'Mobile is missing',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }

        $input = $request->all();

        $details = [];
        $UserObj = User::find($input['user_id']);

        $ticket = new \App\Models\v7\VisitorBooking();
        $input['user_id'] = $input['user_id'];
        $input['account_id'] = $UserObj->account_id;
        $input['unit_no'] = $UserObj->unit_no;
        $visitingTypeObj = VisitorType::find($input['visiting_purpose']);

        $propObj = property::find($UserObj->account_id);
        $input['ticket'] = $ticket->ticketgen($propObj->short_code);

        $todate = (!empty($visitingTypeObj->end_date_required) && $visitingTypeObj->end_date_required == 1) ? $input['visiting_to_date'] : $input['visiting_date'];

        if (isset($input['validity_time_required']) && $input['validity_time_required'] == 1) {
            $input['visiting_start_time'] = $input['visiting_date'] . " " . $input['start_time'] . ":00";
            $input['visiting_end_time'] = $todate . " " . $input['end_time'] . ":00";
        } else {
            $input['visiting_start_time'] = $input['visiting_date'] . " 00:00:00";
            $input['visiting_end_time'] = $todate . " 23:59:59";
        }
        $input['qr_scan_limit'] = isset($visitingTypeObj->qr_scan_limit) ? $visitingTypeObj->qr_scan_limit : 1;
        $booking = VisitorBooking::create($input);
        $qr_code = '';
        if ($propObj->security_option == 2) {
            $qr_content = "bid=" . $booking->ticket . "&type=2";

            $qrsize = 350;
            $randnum = rand(10000, 99999) . '-' . now()->format('YmdHis');
            $filename = "visitorqr/{$randnum}.png";

            // $img =  base64_encode(QrCode::format('png')->size($qrsize)->errorCorrection('H')->margin(5)->generate($qr_content));
            // $qrdata = base64_decode($img);
            // $file = $_SERVER['DOCUMENT_ROOT'] . '/assets/visitorqr/' . $randnum . '.png';
            // file_put_contents($file, $qrdata);
            // $qr_code = $randnum . '.png';

            $qrPng = QrCode::format('png')
                ->size($qrsize)
                ->errorCorrection('H')
                ->margin(5)
                ->generate($qr_content);

            Storage::put(upload_path($filename), $qrPng);
            $qr_code = $filename;
        }

        $bookingObj = VisitorBooking::find($booking->id);
        $bookingObj->qr_scan_type = $propObj->security_option;
        $bookingObj->qrcode_file = $qr_code;
        $bookingObj->save();

        $data['book_id'] = $booking->id;

        $visiting_purpose = VisitorType::where('account_id', $bookingObj->account_id)->where('id', $bookingObj->visiting_purpose)->first();

        if (isset($visiting_purpose->limit_set) && $visiting_purpose->limit_set == 0) {
            $slot_available = 5;
        } else {
            if ($propObj->visitor_limit == 1) {
                $visitor_types = VisitorType::where('account_id', $bookingObj->account_id)->where('limit_set', 1)->where('status', 1)->get();
                $types = array();
                foreach ($visitor_types as $type) {
                    $types[] = $type->id;
                }

                $total_visitor = 0;
                $visitor_records = VisitorBooking::where('account_id', $bookingObj->account_id)->where('visiting_date', $bookingObj->visiting_date)->whereIn('visiting_purpose', $types)->whereIn('status', [0, 2])->get();
                foreach ($visitor_records as $records) {

                    $total_visitor += $records->visitors->count();
                }


                $count = $propObj->visitors_allowed - $total_visitor;

                $slot_available = ($count >= 0) ? $count : 0;
            } else {
                $slot_available = 5;
            }
        }

        if ($slot_available <= 0) {
            return response()->json(['response' => 200, 'message' => 'Slot(s) not available!']);
        }
        for ($i = 1; $i <= $slot_available; $i++) {
            $name = 'name_' . $i;
            $mobile = 'mobile_' . $i;
            $vehicle = 'vehicle_no_' . $i;
            $id_number = 'id_number_' . $i;
            $email = 'email_' . $i;
            $qrcode_file = 'qrcode_file_' . $i;

            if (!empty($request->input($name)) && !empty($request->input($mobile))) {

                $data['name'] = $request->input($name);
                $data['mobile'] = $request->input($mobile);
                $data['vehicle_no'] = $request->input($vehicle);
                //$data['id_number'] = $request->input($id_number);
                $data['email'] = $request->input($email);

                if ($request->input($id_number) != '')
                    $data['id_number'] = $request->input($id_number);
                else
                    $data['id_number'] = '';

                $data['created_at'] = $booking->created_at;
                $data['updated_at'] = $booking->updated_at;

                $record = VisitorList::create($data);

                if ($propObj->security_option != 2)
                {
                    $visitorObj = VisitorList::find($record->id);
                    $inviteurl = ($propObj->security_option == 3 && 1 == 2) ? 
                        url("/opslogin/visitor-summary/" . $bookingObj->id . "/facialscan/") : '';

                    $qr_content = $inviteurl . "bid=" . $bookingObj->ticket . "&vid=" . $visitorObj->id;
                    $qrsize = 350;
                    $randnum = rand(10000, 99999) . '-' . now()->format('YmdHis');
                    $filename = "visitorqr/{$randnum}.png";

                    // $img =  base64_encode(QrCode::format('png')->size($qrsize)->errorCorrection('H')->margin(5)->generate($qr_content));
                    // $qrdata = base64_decode($img);
                    // $file = $_SERVER['DOCUMENT_ROOT'] . '/assets/visitorqr/' . $randnum . '.png';
                    // file_put_contents($file, $qrdata);

                    $qrPng = QrCode::format('png')
                        ->size($qrsize)
                        ->errorCorrection('H')
                        ->margin(5)
                        ->generate($qr_content);

                    Storage::put(upload_path($filename), $qrPng);

                    $visitorObj->qrcode_file = $filename;
                    $visitorObj->save();
                }
                //$details[] = $data;

                if ($propObj->security_option != 2)
                    $qrcodefile_email =  $visitorObj->qrcode_file;
                else
                    $qrcodefile_email =   $bookingObj->qrcode_file;

                VisitorBooking::qrcode_emailnew($bookingObj->id, $bookingObj->user_id, $data['name'], $data['email'], $data['mobile'], $data['vehicle_no'], $qrcodefile_email, $data['id_number']);
            }
        }

        $notification = array();
        $notification['account_id'] = $bookingObj->account_id;
        $notification['user_id'] = $bookingObj->user_id;
        $notification['unit_no'] = $bookingObj->unit_no;
        $notification['type'] = 14;
        $notification['ref_id'] = $bookingObj->id;
        $notification['title'] = 'Visitor Management';
        $notification['message'] = 'Visitor Registration Update';
        $notification['booking_date'] = $bookingObj->visiting_date;
        $notification['booking_time'] = '';
        $notification['status'] =  0;
        $notification['view_status'] =  0;
        $notification['submitted_by'] =  1;
        $notification['created_at'] = date('Y-m-d H:i:s');
        $notification['updated_at'] = date('Y-m-d H:i:s');
        $result = InboxMessage::insert($notification);

        $probObj = Property::find($UserObj->account_id);
        if ($probObj->manager_push_notification == 1) { //if push notification activated for manager app

            $fcm_token_array = '';
            $user_token = ',';
            $ios_devices_to_send = array();
            $android_devices_to_send = array();
            $logs = UserManagerLog::where('account_id', $UserObj->account_id)->whereIn('role_id', [3])->where('status', 1)->orderby('id', 'desc')->first();
            if (isset($logs->fcm_token) && $logs->fcm_token != '') {
                $user_token .= $logs->fcm_token . ",";
                $fcm_token_array .= $logs->fcm_token . ',';
                $appSipAccountList[] = $bookingObj->user_id;
                if ($logs->login_from == 1)
                    $ios_devices_to_send[] = $logs->fcm_token;
                if ($logs->login_from == 2)
                    $android_devices_to_send[] = $logs->fcm_token;
            }

            $title = "Aerea Home - " . $probObj->company_name;
            $message = "Visitor Registration Update";
            $notofication_data = array();
            $notofication_data['body'] = $title;
            $notofication_data['unit_no'] = $bookingObj->unit_no;
            $notofication_data['user_id'] = $bookingObj->user_id;
            $notofication_data['property'] = $bookingObj->account_id;
            $NotificationObj = new \App\Models\v7\FirebaseNotification();

            $NotificationObj->ios_manager_notification($title, $message, $ios_devices_to_send, $notofication_data); //ios notification

            $NotificationObj->android_manager_notification($title, $message, $android_devices_to_send, $notofication_data); //
        }

        return response()->json(['result' => $bookingObj, 'response' => 1, 'message' => 'Visitor has been submitted!']);
    }

    public function visitorRegSummary(Request $request)
    {

        $userid = $request->user;
        $UserObj = User::find($userid);

        $file_path =  url('/assets/visitorqr/');

        $records = VisitorBooking::where('user_id', $userid)->where('unit_no', $UserObj->unit_no)->orderby('id', 'desc')->get();
        $data = array();
        foreach ($records as $k => $record) {
            $data[$k] = $record;

            if ($record->visited_count->count() > 0 && $record->visited_count->count() >= $record->visitors->count())
                $status = "Entered";
            else if ($record->visited_count->count() > 0 && $record->visited_count->count() < $record->visitors->count())
                $status = $record->visited_count->count() . " Entered";
            else if ($record->registered_count->count() > 0 && $record->registered_count->count() == $record->invitedemails->count())
                $status = "Registration Success";
            else if ($record->registered_count->count() > 0 && $record->registered_count->count() <= $record->visitors->count())
                $status = $record->registered_count->count() . " Registered";
            else if ($record->status == 0)
                $status = "Pending";
            else if ($record->status == 1)
                $status = "Cancelled";
            else
                $status = "Entered";

            $data[$k]['ticket_status'] = $status;
            $data[$k]['purpose'] = isset($record->visitpurpose->visiting_purpose) ? $record->visitpurpose->visiting_purpose : '';
            $data[$k]['invited_by'] = isset($record->user->name) ? Crypt::decryptString($record->user->name) : 'Walk-In';
            $data[$k]['entry_date'] = date('H:i', strtotime($record->entry_date));
            if (isset($record->visitors)) {
                $data[$k]['visitors'] = $record->visitors;
            }
        }
        $property = Property::find($UserObj->account_id);
        $type = VisitorType::where('account_id', $UserObj->account_id)->get();
        return response()->json([
            'booking' => $data,
            'purpose_lists' => $type,
            'file_path' => $file_path,
            'property' => $property,
            'status' => 'success'
        ]);
    }

    public function visitorBookingInfo(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'book_id' => 'required',
        ];

        $messages = [
            'user_id.required' => 'User id missing',
            'book_id.required' => 'Booking id is missing',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }

        $bookid = $request->book_id;
        $userid = $request->user_id;
        $UserObj = User::find($userid);

        $file_path = url('assets/visitorqr/');

        $record = VisitorBooking::where('id', $bookid)->first();
        if (empty($record)) return response()->json(['response' => 200, 'message' => 'Booking not found']);

        if (isset($record->visitors)) $record['visitors'] = $record->visitors;

        $type = VisitorType::where('account_id', $UserObj->account_id)->get();

        $total_visitor = 0;
        $visitor_records = VisitorBooking::where(['account_id' => $record->account_id, 'visiting_date' => $record->visiting_date])
            ->whereIn('status', [0, 2])->get();
        
        if (empty($visitor_records)) return response()->json(['response' => 200, 'message' => 'Record not found']);

        foreach ($visitor_records as $records) $total_visitor += $records->visitors->count();

        $property = Property::find($record->account_id);
        $slot_available = $property->visitors_allowed - $total_visitor;

        return response()->json([
            'booking' => $record,
            'purpose_lists' => $type,
            'file_path' => $file_path,
            'available_slots' => $slot_available,
            'property' => $property,
            'status' => 'success'
        ]);
    }

    public function visitorBookingCancel(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'book_id' => 'required',
        ];

        $messages = [
            'user_id.required' => 'User id missing',
            'book_id.required' => 'Booking id is missing',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }

        $bookid = $request->book_id;
        $userid = $request->user_id;
        $reason = $request->reason != '' ? $request->reason : '';

        VisitorBooking::where(['id' => $bookid, 'user_id' => $userid])
            ->update(['status' => 1, 'view_status' => 1, 'remarks' => $reason]);

        return response()->json(['response' => 1, 'message' => 'Cancelled']);
    }

    public function visitorSendInvite(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'visiting_date' => 'required',
            'visiting_purpose' => 'required',
            'email_1' => 'required',
        ];

        $messages = [
            'user_id.required' => 'User id missing',
            'visiting_date.required' => 'Date is missing',
            'visiting_purpose.required' => 'Purpose of visit is missing',
            'email_1.required' => 'Email is missing',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }

        $input = $request->all();
        $details = array();
        $UserObj = User::find($input['user_id']);

        $ticket = new \App\Models\v7\VisitorBooking();
        $visitingTypeObj = VisitorType::find($input['visiting_purpose']);
        if (empty($visitingTypeObj)) return response()->json(['response' => 200, 'message' => 'Visiting purpose not found']);

        $propObj = property::find($UserObj->account_id);
        $input['ticket'] = $ticket->ticketgen($propObj->short_code);

        $todate = ($visitingTypeObj->end_date_required == 1) ? $input['visiting_to_date'] : $input['visiting_date'];

        $input['visiting_start_time'] = $input['visiting_date'] . " 00:00:00";
        $input['visiting_end_time'] = $todate . " 23:59:59";

        if (isset($input['validity_time_required']) && $input['validity_time_required'] == 1) {
            if (!empty($input['start_time']) && !empty($input['end_time'])) {
                $input['visiting_start_time'] = $input['visiting_date'] . " " . $input['start_time'] . ":00";
                $input['visiting_end_time'] = $todate . " " . $input['end_time'] . ":00";
            } else {
                $input['visiting_start_time'] = $input['visiting_date'] . " 00:00:01";
            }
        }

        $input['qr_scan_limit'] = isset($visitingTypeObj->qr_scan_limit) ? $visitingTypeObj->qr_scan_limit : 1;
        $input['user_id'] = $input['user_id'];
        $input['account_id'] = $UserObj->account_id;
        $input['unit_no'] = $UserObj->unit_no;
        $booking = VisitorBooking::create($input);

        $data['book_id'] = $booking->id;

        for ($i = 1; $i <= 5; $i++) {
            $email = 'email_' . $i;
            $name = 'name_' . $i;
            if (!empty($request->input($email)) && !empty($request->input($email)))
            {
                VisitorBooking::invite_emailnew($booking->id, $UserObj->id, $UserObj->account_id, $request->input($email), $request->input($name));
                $data['name'] = $request->input($name);
                $data['email'] = $request->input($email);
                $data['created_at'] = $booking->created_at;
                $data['updated_at'] = $booking->updated_at;

                $details[] = $data;
            }
        }

        $record = VisitorInviteEmailList::insert($details);

        $qr_code = '';
        if ($propObj->security_option == 2) {
            $inviteurl = url("/opslogin/visitor-summary/" . $booking->id . "/manualscan");

            $qrsize = 300;
            $randnum = rand(10000, 99999) . '-' . now()->format('YmdHis');
            $filename = "visitorqr/{$randnum}.png";

            // $img =  base64_encode(QrCode::format('png')->size($qrsize)->errorCorrection('H')->margin(5)->generate($inviteurl));
            // $qrdata = base64_decode($img);
            // $file = $_SERVER['DOCUMENT_ROOT'] . '/assets/visitorqr/' . $randnum . '.png';
            // file_put_contents($file, $qrdata);
            // $qr_code = $randnum . '.png';

            $qrPng = QrCode::format('png')
                ->size($qrsize)
                ->errorCorrection('H')
                ->margin(5)
                ->generate($inviteurl);

            Storage::put(upload_path($filename), $qrPng);
            $qr_code = $filename;

        }

        $BookingObj = VisitorBooking::find($booking->id);
        $BookingObj->qrcode_file = $qr_code;
        $BookingObj->qr_scan_type = $propObj->security_option;
        $BookingObj->save();

        return response()->json(['result' => $record, 'response' => 1, 'message' => 'Invitation has been sent!']);
    }

    public function visitorRegValidation(Request $request)
    {
        $rules = [
            'property' => 'required',
            'purpose' => 'required',
            'visiting_date' => 'required',
        ];

        $messages = [
            'property.required' => 'Property id missing',
            'purpose.required' => 'Visiting purpose missing',
            'visiting_date.required' => 'Date is missing',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }

        $input = $request->all();

        $visiting_purpose = VisitorType::where('account_id', $input['property'])->where('id', $input['purpose'])->first();
        if ($visiting_purpose->limit_set == 0) {
            return response()->json(['slot_available' => 5, 'response' => 1, 'message' => 'Success']);
        } else {
            $visitor_types = VisitorType::where('account_id', $input['property'])->where('limit_set', 1)->where('status', 1)->get();
            $types = [];
            foreach ($visitor_types as $type) $types[] = $type->id;

            $total_visitor = 0;
            $visitor_records = VisitorBooking::where('account_id', $input['property'])->where('visiting_date', $input['visiting_date'])->whereIn('visiting_purpose', $types)->whereIn('status', [0, 2])->get();
            foreach ($visitor_records as $records) $total_visitor += $records->visitors->count();

            $property = Property::find($input['property']);
            $count = $property->visitors_allowed - $total_visitor;
            $slot_available = ($count >= 0) ? $count : 0;

            return response()->json(['slot_available' => $slot_available, 'response' => 1, 'message' => 'Success!']);
        }
    }

    public function visitingPurposeVal(Request $request)
    {
        $rules = [
            'property' => 'required',
            'purpose' => 'required',
            'visiting_date' => 'required',
        ];

        $messages = [
            'property.required' => 'Property id missing',
            'purpose.required' => 'Visiting purpose missing',
            'visiting_date.required' => 'Date is missing',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();
            return response()->json([
                'message' => $errors,
            ], 400);
        }

        $input = $request->all();
        return response()->json(['slot_available' => 5, 'response' => 1, 'message' => 'Success!']);

    }

    public function getqrstatus(Request $request)
    {
        date_default_timezone_set('Asia/Singapore');

        $input = array();
        $rawPostData = file_get_contents("php://input");
        $special_char = array("{", "}", "https://");
        $string = str_replace($special_char, "", $rawPostData);
        $values = explode(",", $string);
        $quote_char = array('"', '"', " ");
        foreach ($values as $value) {
            $var_string = explode(":", trim($value));
            if (isset($var_string[0]) && isset($var_string[1])) {
                $key_array = explode('"', trim($var_string[0]));
                $val_array = explode('"', trim($var_string[1]));
                $val = '';
                $key = trim($key_array[1]);

                if (isset($val_array[1]))
                    $val = trim($val_array[1]);
                else
                    $val = trim($var_string[1]);

                $input[$key] = $val;
            }
        }
        if (empty($input['validData']) || $input['validData'] == '') {
            return response()->json(['code' => 99999, 'msg' => 'No valid QR data']);
        }

        $validData = explode("facialscan/", $input['validData']);
        $qr_data = explode("&", ($validData[1] ?? $validData[0]));

        foreach ($qr_data as $data) {
            $values = explode("=", $data);
            if (isset($values[1])) $input[$values[0]] = $values[1];
        }

        if (empty($input['bid'])) return response()->json(['code' => 99999, 'msg' => 'No valid QR data']);

        $ticket = $input['bid'];
        $bookingObj = VisitorBooking::where('ticket', $ticket)->first();

        if (isset($bookingObj) && $bookingObj->id > 0)
        {
            $property = Property::find($bookingObj->account_id);
            
            $device_info = Device::where('device_serial_no', $input['devSn'])->first();
            if (empty($device_info)) return response()->json(['code' => 99999, 'msg' => 'Device not available in community']);
            
            $unitObj = Unit::find($bookingObj->unit_no);
            if (empty($unitObj)) return response()->json(['code' => 99999, 'msg' => 'Unit not available in community']);

            $locations = explode(",", $device_info->locations);
            if (!in_array($unitObj->building_id, $locations)) return response()->json(['code' => 99999, 'msg' => 'Permission denied']);

            $today = Carbon::now()->format('Y-m-d');
            $visiting_time = Carbon::now()->format('Y-m-d H:i:s');
            $qrcode_path = url('/assets/visitorqr/');
            
            if (isset($bookingObj->visiting_date) && $bookingObj->visiting_start_time <= $visiting_time && $bookingObj->visiting_end_time >= $visiting_time) {
                $env_max_scan_count = $bookingObj->qr_scan_limit;

                $vid = $input['vid'];
                $VisitorObj = VisitorList::where('book_id', $bookingObj->id)->where('id', $vid)->first();

                if (isset($VisitorObj) && $VisitorObj->id > 0 && $VisitorObj->visit_count <= $env_max_scan_count) {
                    $visit_count = $VisitorObj->visit_count + 1;
                    $entry_date = date("Y-m-d H:i:s");
                    $result = VisitorList::where('id', $VisitorObj->id)->update(array('visit_count' => $visit_count, 'visit_status' => 1, 'entry_date' => $entry_date));

                    $qrinput['account_id'] = $bookingObj->account_id;
                    $qrinput['booking_id'] = $bookingObj->id;
                    $qrinput['visitor_id'] = $VisitorObj->id;
                    $qrinput['devSn'] = $input['devSn'];
                    $qrinput['dataType'] = $input['dataType'];
                    $qrinput['message'] = 'Success';
                    QrcodeOpenRecord::create($qrinput);

                    //Start Insert into notification module
                    $notification = array();
                    $notification['account_id'] = $bookingObj->account_id;
                    $notification['user_id'] = $bookingObj->user_id;
                    $notification['unit_no'] = $bookingObj->unit_no;
                    $notification['module'] = 'vistor management';
                    $notification['ref_id'] = $bookingObj->id;
                    $notification['title'] = 'Visitor Update';
                    $notification['message'] = 'Visitor QR code scanned';
                    $notification['created_at'] = date('Y-m-d H:i:s');
                    $notification['updated_at'] = date('Y-m-d H:i:s');
                    $result = UserNotification::insert($notification);

                    $SettingsObj = UserNotificationSetting::where('user_id', $bookingObj->user_id)->where('account_id', $bookingObj->account_id)->first();
                    //print_r($SettingsObj);
                    if (empty($SettingsObj) || $SettingsObj->visitor_management == 1) {
                        $fcm_token_array = '';
                        $user_token = ',';
                        $ios_devices_to_send = array();
                        $android_devices_to_send = array();
                        $logs = UserLog::where('user_id', $bookingObj->user_id)->where('status', 1)->orderby('id', 'desc')->first();
                        if (isset($logs->fcm_token) && $logs->fcm_token != '') {
                            $user_token .= $logs->fcm_token . ",";
                            $fcm_token_array .= $logs->fcm_token . ',';
                            $appSipAccountList[] = $bookingObj->id;
                            if ($logs->login_from == 1)
                                $ios_devices_to_send[] = $logs->fcm_token;
                            if ($logs->login_from == 2)
                                $android_devices_to_send[] = $logs->fcm_token;
                        }

                        $title = "Aerea Home - " . $property->company_name;
                        $message = $notification['message'];
                        $notofication_data = array();
                        $notofication_data['body'] = $title;
                        $notofication_data['unit_no'] = $bookingObj->unit_no;
                        $notofication_data['user_id'] = $bookingObj->user_id;
                        $notofication_data['property'] = $bookingObj->account_id;
                        $purObj = UserPurchaserUnit::where('property_id', $bookingObj->account_id)->where('unit_id', $bookingObj->unit_no)->where('user_id', $bookingObj->user_id)->first();
                        if (isset($purObj))
                            $notofication_data['switch_id'] = $purObj->id;

                        $NotificationObj = new \App\Models\v7\FirebaseNotification();
                        $NotificationObj->ios_msg_notification($title, $message, $ios_devices_to_send, $notofication_data); //ios notification
                        $NotificationObj->android_msg_notification($title, $message, $android_devices_to_send, $notofication_data); //android notification
                        //End Insert into notification module
                    }

                    return response()->json(['code' => 0, 'msg' => 'Success']);

                } else if (isset($VisitorObj) && $VisitorObj->id > 0 && $VisitorObj->visit_status == 1 && $VisitorObj->visit_count > $env_max_scan_count) {
                    $qrinput['account_id'] = $bookingObj->account_id;
                    $qrinput['booking_id'] = $bookingObj->id;
                    $qrinput['visitor_id'] = $VisitorObj->id;
                    $qrinput['devSn'] = $input['devSn'];
                    $qrinput['dataType'] = $input['dataType'];
                    $qrinput['message'] = 'Already Visited';
                    QrcodeOpenRecord::create($qrinput);

                    return response()->json(['code' => 99999, 'msg' => 'Reached Maximum Visit Count']);

                } else {
                    $qrinput['account_id'] = $bookingObj->account_id;
                    $qrinput['booking_id'] = $bookingObj->id;
                    $qrinput['devSn'] = $input['devSn'];
                    $qrinput['dataType'] = $input['dataType'];
                    $qrinput['message'] = 'Visitor Details Not Registered';
                    QrcodeOpenRecord::create($qrinput);
                    return response()->json(['code' => 99999, 'msg' => 'Visitor Details Not Registered']);

                }
            } else {
                if (isset($bookingObj->visiting_date) && $bookingObj->visiting_end_time < $visiting_time) {
                    $qrinput['account_id'] = $bookingObj->account_id;
                    $qrinput['booking_id'] = $bookingObj->id;
                    $qrinput['devSn'] = $input['devSn'];
                    $qrinput['dataType'] = $input['dataType'];
                    $qrinput['message'] = 'Booking Expired';
                    QrcodeOpenRecord::create($qrinput);
                    return response()->json(['code' => 99999, 'msg' => 'Booking Expired']);
                } else {
                    $qrinput['account_id'] = $bookingObj->account_id;
                    $qrinput['booking_id'] = $bookingObj->id;
                    $qrinput['devSn'] = $input['devSn'];
                    $qrinput['dataType'] = $input['dataType'];
                    $qrinput['message'] = 'QR Code Not Active';
                    QrcodeOpenRecord::create($qrinput);
                    return response()->json(['code' => 99999, 'msg' => 'QR Code Not Active']);
                }
            }
        } else {
            return response()->json(['code' => 99999, 'msg' => 'No Booking Available']);
        }
    }

    public function trailgetqrstatus(Request $request)
    {
        date_default_timezone_set('Asia/Singapore');

        $input = [];
        $rawPostData = file_get_contents("php://input");
        $special_char = ['{', '}', 'https://'];
        $string = str_replace($special_char, "", $rawPostData);
        $values = explode(",", $string);

        $quote_char = ['"', '"', " "];
        foreach ($values as $value) {

            $var_string = explode(":", trim($value));
            if (isset($var_string[0]) && isset($var_string[1])) {
                $key_array = explode('"', trim($var_string[0]));
                $val_array = explode('"', trim($var_string[1]));
                $val = '';
                $key = trim($key_array[1]);
                $val = isset($val_array[1]) ? trim($val_array[1]) : trim($var_string[1]);
                $input[$key] = $val;
            }
        }

        $validData = explode("facialscan/", $input['validData']);
        $qr_data = explode("&", $validData[1] ?? $validData[0]);

        foreach ($qr_data as $data) {
            $values = explode("=", $data);
            if (isset($values[1])) $input[$values[0]] = $values[1];
        }

        if (empty($input['bid'])) return response()->json(['code' => 99999, 'msg' => 'No valid QR data']);

        $ticket = $input['bid'];

        $bookingObj = VisitorBooking::where('ticket', $ticket)->first();

        if (isset($bookingObj) && $bookingObj->id > 0)
        {
            $property = Property::find($bookingObj->account_id);
            
            $device_info = Device::where('device_serial_no', $input['devSn'])->first();
            if (!$device_info) return response()->json(['code' => 99999, 'msg' => 'Device not available in community']);
            
            $unitObj = Unit::find($bookingObj->unit_no);
            if (!$unitObj) return response()->json(['code' => 99999, 'msg' => 'Unit not available in community']);

            $locations = explode(",", $device_info->locations);
            if (!in_array($unitObj->building_id, $locations)) return response()->json(['code' => 99999, 'msg' => 'Permission denied']);

            $today = Carbon::now()->format('Y-m-d');
            $visiting_time = Carbon::now()->format('Y-m-d H:i:s');
            $qrcode_path = url('/assets/visitorqr/');
            
            if (isset($bookingObj->visiting_date) && $bookingObj->visiting_date >= $today) {

                $env_max_scan_count = $bookingObj->qr_scan_limit;

                $vid = $input['vid'];
                $VisitorObj = VisitorList::where('book_id', $bookingObj->id)->where('id', $vid)->first();

                if (isset($VisitorObj) && $VisitorObj->id > 0 && $VisitorObj->visit_count <= $env_max_scan_count) {
                    $visit_count = $VisitorObj->visit_count + 1;
                    $entry_date = date("Y-m-d H:i:s");
                    $result = VisitorList::where('id', $VisitorObj->id)->update(array('visit_count' => $visit_count, 'visit_status' => 1, 'entry_date' => $entry_date));

                    $qrinput['account_id'] = $bookingObj->account_id;
                    $qrinput['booking_id'] = $bookingObj->id;
                    $qrinput['visitor_id'] = $VisitorObj->id;
                    $qrinput['devSn'] = $input['devSn'];
                    $qrinput['dataType'] = $input['dataType'];
                    $qrinput['message'] = 'Success';
                    QrcodeOpenRecord::create($qrinput);

                    //Start Insert into notification module
                    $notification = array();
                    $notification['account_id'] = $bookingObj->account_id;
                    $notification['user_id'] = $bookingObj->user_id;
                    $notification['unit_no'] = $bookingObj->unit_no;
                    $notification['module'] = 'vistor management';
                    $notification['ref_id'] = $bookingObj->id;
                    $notification['title'] = 'Visitor Update';
                    $notification['message'] = 'Visitor QR code scanned';
                    $notification['created_at'] = date('Y-m-d H:i:s');
                    $notification['updated_at'] = date('Y-m-d H:i:s');
                    $result = UserNotification::insert($notification);

                    $SettingsObj = UserNotificationSetting::where('user_id', $bookingObj->user_id)->where('account_id', $bookingObj->account_id)->first();
                    if (empty($SettingsObj) || $SettingsObj->visitor_management == 1) {
                        $fcm_token_array = '';
                        $user_token = ',';
                        $ios_devices_to_send = array();
                        $android_devices_to_send = array();
                        $logs = UserLog::where('user_id', $bookingObj->user_id)->where('status', 1)->orderby('id', 'desc')->first();
                        if (isset($logs->fcm_token) && $logs->fcm_token != '') {
                            $user_token .= $logs->fcm_token . ",";
                            $fcm_token_array .= $logs->fcm_token . ',';
                            $appSipAccountList[] = $bookingObj->id;
                            if ($logs->login_from == 1)
                                $ios_devices_to_send[] = $logs->fcm_token;
                            if ($logs->login_from == 2)
                                $android_devices_to_send[] = $logs->fcm_token;
                        }

                        $title = "Aerea Home - " . $property->company_name;
                        $message = $notification['message'];
                        $notofication_data = array();
                        $notofication_data['body'] = $title;
                        $notofication_data['unit_no'] = $bookingObj->unit_no;
                        $notofication_data['user_id'] = $bookingObj->user_id;
                        $notofication_data['property'] = $bookingObj->account_id;
                        $purObj = UserPurchaserUnit::where('property_id', $bookingObj->account_id)->where('unit_id', $bookingObj->unit_no)->where('user_id', $bookingObj->user_id)->first();
                        if (isset($purObj))
                            $notofication_data['switch_id'] = $purObj->id;

                        $NotificationObj = new \App\Models\v7\FirebaseNotification();
                        $NotificationObj->ios_msg_notification($title, $message, $ios_devices_to_send, $notofication_data); //ios notification
                        $NotificationObj->android_msg_notification($title, $message, $android_devices_to_send, $notofication_data); //android notification
                        //End Insert into notification module
                    }


                    return response()->json(['code' => 0, 'msg' => 'Success']);
                } else if (isset($VisitorObj) && $VisitorObj->id > 0 && $VisitorObj->visit_status == 1 && $VisitorObj->visit_count > $env_max_scan_count) {
                    $qrinput['account_id'] = $bookingObj->account_id;
                    $qrinput['booking_id'] = $bookingObj->id;
                    $qrinput['visitor_id'] = $VisitorObj->id;
                    $qrinput['devSn'] = $input['devSn'];
                    $qrinput['dataType'] = $input['dataType'];
                    $qrinput['message'] = 'Already Visited';
                    QrcodeOpenRecord::create($qrinput);

                    return response()->json(['code' => 99999, 'msg' => 'Reached Maximum Visit Count']);
                } else {
                    $qrinput['account_id'] = $bookingObj->account_id;
                    $qrinput['booking_id'] = $bookingObj->id;
                    $qrinput['devSn'] = $input['devSn'];
                    $qrinput['dataType'] = $input['dataType'];
                    $qrinput['message'] = 'Visitor Details Not Registered';
                    QrcodeOpenRecord::create($qrinput);
                    return response()->json(['code' => 99999, 'msg' => 'Visitor Details Not Registered']);
                }
            } else {
                $qrinput['account_id'] = $bookingObj->account_id;
                $qrinput['booking_id'] = $bookingObj->id;
                $qrinput['devSn'] = $input['devSn'];
                $qrinput['dataType'] = $input['dataType'];
                $qrinput['message'] = 'Booking Expired';
                QrcodeOpenRecord::create($qrinput);
                return response()->json(['code' => 99999, 'msg' => 'QR Code Not Active']);
            }
        } else {

            return response()->json(['code' => 99999, 'msg' => 'No Booking Available']);
        }




        //return response()->json(['code' => 0, 'msg' => 'Success']);


    }

    public function testfirebase(Request $request)
    {

        $input = $request->all();

        //print_r($input);

        $message = $input['message'];
        $deviceid = $input['deviceid'];

        $deviceToken = '83e0c7f4a08a19d60e9dc79bdf3b6ca59b742d0f3ed62d1c2c55e3d86d2ba3e2';

        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification(['title' => 'something', 'body' => 'hi']);

        $messaging->send($message);


        return response()->json(['code' => 0, 'msg' => 'Success']);
    }


    public function sendWebNotification(Request $request)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $FcmToken = User::whereNotNull('device_key')->pluck('device_key')->all();

        $serverKey = 'server key goes here';

        $data = [
            "registration_ids" => $FcmToken,
            "notification" => [
                "title" => $request->title,
                "body" => $request->body,
            ]
        ];
        $encodedData = json_encode($data);

        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);
        // FCM response
        dd($result);
    }
}
