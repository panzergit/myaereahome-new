<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Console\Command;

use App\Models\v7\FinanceNotificationDetail;
use App\Models\v7\FinanceInvoice;
use App\Models\v7\UserNotification;
use App\Models\v7\UserNotificationSetting;
use App\Models\v7\UserLog;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\Property;
use App\Services\PHPMailerService;

class SendInvoiceNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send_invoice_notification:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send invoice notification';

	public function sendNotificationMail($invoice,$name,$email)
	{
		$companyname = 'Aerea Home';
        
			$date = isset($invoice->invoice_date)?date('d/m/y',strtotime($invoice->invoice_date)):'';
			$due_date = isset($invoice->due_date)?date('d/m/y',strtotime($invoice->due_date)):'';
			
			$building = isset($invoice->getunit->buildinginfo->id)?str_pad($invoice->getunit->buildinginfo->id, 4, '0', STR_PAD_LEFT):'';
			$unit =isset($invoice->getunit->unit)?Crypt::decryptString($invoice->getunit->unit):'';
			$act_no = $building."-#".$unit;

			$advance_amount = isset($invoice->AdvancePayment->amount)?$invoice->AdvancePayment->amount:0;
			$amount = number_format(($invoice->payable_amount - $advance_amount),2);
			
			$service = app(PHPMailerService::class);
			$returnMailData = $service->sendMail(
                trim($email),
                'Your '.$companyname.' statement for '.$date.' is ready',
                'emails.invoice',
                [
                    'date' => $date,
					'act_no' => $act_no,
					'amount' => $amount,
					'duedate' => $due_date,
					'name' => $name,
					'companyname' => $companyname
                ]
            );
	}
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		$pendingNotifications = FinanceNotificationDetail::where('notification_status',0)->orderBy('id', 'asc')->limit(100)->get();

        \Log::info('Notification Start => '.now());
		foreach ($pendingNotifications as $notificationDetail)
		{
        	$invoice = FinanceInvoice::find($notificationDetail->invoice_id);

            if(empty($invoice)){
                FinanceNotificationDetail::where('id',$notificationDetail->id)->update([
					'notification_status' => 2
				]);
                continue;
            }

			//mail send
			$this->sendNotificationMail($invoice,$notificationDetail->name,$notificationDetail->email);
			
			FinanceNotificationDetail::where('id',$notificationDetail->id)->update([
				'notification_status' => 1
			]);

			//Start Insert into notification module
			UserNotification::firstOrCreate([
				'account_id' => $invoice->account_id,
				'unit_no' => $invoice->unit_no,
				'user_id' => $notificationDetail->user_id,
				'module' => 'resident management',
				'ref_id' => $invoice->id,
				'title' => 'Resident Management',
				'message' => 'Your latest invoice is now available for viewing.'
			]);

			$settingsObj = UserNotificationSetting::where([
				['user_id','=',$notificationDetail->user_id],
				['account_id','=',$notificationDetail->account_id]
			])->first();

            if(!$settingsObj || $settingsObj->resident_management ==1)
			{
				$ios_devices_to_send = [];
				$android_devices_to_send = [];
				$logs = UserLog::where([
					['user_id','=',$notificationDetail->user_id],
					['status','=',1]
				])->latest()->first();

				if($logs && trim($logs->fcm_token)!='')
				{
					if($logs->login_from ==1) $ios_devices_to_send[] = $logs->fcm_token;
					if($logs->login_from ==2) $android_devices_to_send[] = $logs->fcm_token;

					$probObj = Property::find($notificationDetail->account_id);
					$title = "Aerea Home - ".$probObj->company_name;
					$message = "Resident Management Update";
					$notofication_data = [];
					$notofication_data['body'] =$title; 
					$notofication_data['unit_no'] = $invoice->unit_no;   
					$notofication_data['user_id'] = $notificationDetail->user_id;   
					$notofication_data['property'] = $invoice->account_id; 
					
					$purObj = UserPurchaserUnit::where([
						['property_id','=',$invoice->account_id],
						['unit_id','=',$invoice->unit_no],
						['user_id','=',$notificationDetail->user_id],
						['status','=',1]
					])->first();

					if($purObj){
						$notofication_data['switch_id'] =$purObj->id;       
						$NotificationObj = new \App\Models\v7\FirebaseNotification();

						\Log::info('Total Ios => '.serialize($ios_devices_to_send));
						\Log::info('Total Android => '.serialize($android_devices_to_send));

						//ios notification
						if(count($ios_devices_to_send) >0 ) $NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data);

						//android notification
						if(count($android_devices_to_send) >0 ) $NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data);
					}
				}
			}
			//End Insert into notification module
		}
        \Log::info('Notification End => '.now());
    }
}
