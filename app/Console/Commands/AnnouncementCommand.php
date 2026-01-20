<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\v7\User;
use App\Models\v7\Property;
use App\Models\v7\Announcement;
use App\Models\v7\UserNotification;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\AnnouncementDetail;
use App\Models\v7\UserNotificationSetting;
use App\Models\v7\InboxMessage;
use App\Models\v7\UserLog;

class AnnouncementCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'announcement:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send announcement notification';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info('Announcement Start! => ');
        $records = AnnouncementDetail::where('notification_status',0)->orderby('id','asc')->limit(50)->get();
        if(count($records) >0){
			foreach($records as $record){
				$fcm_token_array ='';
				$user_token = ',';
				$ios_devices_to_send = array();
				$android_devices_to_send = array();

				$user = User::find($record->user_id);
				\Log::info('Announcement Id:'.$record->a_id);

				$announcement = Announcement::find($record->a_id);
				$push_status = 0;
				$string='';
				if(isset($user) && !empty($announcement)){
					$inbox['account_id'] = $record->account_id;
					$inbox['unit_no'] = $record->unit_no;
					$inbox['user_id'] = $record->user_id;
					$inbox['type'] = 1;
					$inbox['ref_id'] = $record->id;
					$inbox['title'] = $announcement->title;
					$inbox['message'] = $announcement->notes;
					$inbox['status'] =  0; 
					$inbox['view_status'] =  0;   
					$inbox['created_at'] =  $announcement->created_at;     
					$inboxs[] = $inbox;
					$email = $user->email;
					InboxMessage::insert($inboxs);

					//Start Insert into notification module
					$notification = array();
					$notification['account_id'] = $record->account_id;
					$notification['user_id'] = $user->id;
					$notification['unit_no'] = $record->unit_no;
					$notification['module'] = 'announcement';
					$notification['ref_id'] = $record->id;
					$notification['title'] = $announcement->title;
					$notification['message'] = $announcement->notes;
					UserNotification::insert($notification);
					//End Insert into notification module
					$SettingsObj = UserNotificationSetting::where('user_id',$record->user_id)->where('account_id',$record->account_id)->first();
					if(empty($SettingsObj) || $SettingsObj->announcement ==1){
						\Log::info('notificatgion Id:'.$record->id);
						//Firebase Notification - Preparing ids START
						$logs = UserLog::where('user_id',$user->id)->where('status',1)->orderby('id','desc')->first();
						if(isset($logs->fcm_token) && $logs->fcm_token !=''){
							$user_token .=$logs->fcm_token.",";
							$fcm_token_array .=$logs->fcm_token.',';
							$appSipAccountList[] = $user->id;
							if($logs->login_from ==1)
								$ios_devices_to_send[] = $logs->fcm_token;
							if($logs->login_from ==2)
								$android_devices_to_send[] = $logs->fcm_token;
						}
						//echo $logs->fcm_token;
						//Firebase Notification  - Preparing ids END
						//Push notification to Mobile app for IOS
						$probObj = Property::find($record->account_id);
						$title = "Aerea Home - ".$probObj->company_name;
						$message = "New Announcement";
						$notofication_data = array();
						$notofication_data['body'] =$announcement->title;
						$notofication_data['unit_no'] =$record->unit_id;   
						$notofication_data['user_id'] =$record->user_id;   
						$notofication_data['property'] =$record->account_id;
						$purObj = UserPurchaserUnit::where('property_id',$record->account_id)->where('unit_id',$record->unit_id)->where('user_id',$record->user_id)->first(); 
						if(isset($purObj))
							$notofication_data['switch_id'] =$record->id;
							
						$NotificationObj = new \App\Models\v7\FirebaseNotification();
						if(count($ios_devices_to_send) >0){
							$push_result = $NotificationObj->ios_msg_notification($title,$message,$ios_devices_to_send,$notofication_data); //ios notification
							//\Log::info('IOS Push Log :'.print_r($push_result));
							$string = json_encode($push_result);
							\Log::info('IOS Push Log :'.print_r($string));
						}
						if(count($android_devices_to_send) >0){
							$push_result = $NotificationObj->android_msg_notification($title,$message,$android_devices_to_send,$notofication_data); //android notification
							$string = json_encode($push_result);
							//\Log::info('Android Push Log :'.print_r($push_result));
						}
						
						//\Log::info('Push Log :'.print_r($push_result));

						$push_status = 1;
					}	

					/*$subject = 'Aerea : '.$announcement->title;                        
					if(env('MAIL_SEND')== 1){
						$admin = Setting::findOrFail(1);
						//Mail::to($admin->company_email)->cc($emails)->send(new AnnouncementNotification($announcement,$subject,'Admin'));
						Mail::to($email)->send(new AnnouncementNotification($announcement,$subject,'Admin'));
					}*/
	
				}
				AnnouncementDetail::where('id',$record->id)->update(['notification_status'=>'1','push_status'=>$push_status,'push_result'=>$string]);
			}
		}
        \Log::info('Announcement End! => ');


    }
}
