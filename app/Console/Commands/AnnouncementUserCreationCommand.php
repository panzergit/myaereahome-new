<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\UserMoreInfo;
use App\Models\v7\AnnouncementCron;
use App\Models\v7\AnnouncementDetail;

class AnnouncementUserCreationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'announcement_user:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'announcement user creation for details table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info('Announcement User Creation Start! => ');
		$announcement = AnnouncementCron::where('cron_status',0)->orderby('id','asc')->first();
        if(isset($announcement) && $announcement->roles !='')
        {
            $emails =array();
			$role_array = explode(",",$announcement->roles);

            $user_lists = UserPurchaserUnit::where('property_id',$announcement->account_id)->whereIn('role_id',$role_array)->groupBy('user_id')->get();
                if(count($user_lists) >0){
                    $details = array();
                    foreach($user_lists as $user_list){
                        $userinfo = UserMoreInfo::where('id',$user_list->user_info_id)->where('status',1)->first();
                        if(isset($userinfo)){
                            $detail = array();
                            $detail['a_id'] = $announcement->a_id;
                            $detail['user_id'] = $userinfo->user_id;
                            $detail['account_id'] = $userinfo->account_id;
                            $detail['unit_no'] = $user_list->unit_id;
                            $detail['name'] = isset($userinfo->first_name)?Crypt::decryptString($userinfo->first_name):'';
							$detail['last_name'] =  isset($userinfo->last_name)?Crypt::decryptString($userinfo->last_name):'';
                            $detail['email'] = $userinfo->getuser->email;
                            $detail['status'] =  0; 
                            $detail['notification_status'] =  0; 
                            $detail['created_at'] =  $announcement->created_at; 
                            $detail['updated_at'] =  $announcement->created_at; 
							$details[] = $detail;
							\Log::info('Detail:'.print_r($detail));
                        }
                    }
					$result = AnnouncementDetail::insert($details);
					\Log::info('Announcement ID:'.$announcement->id);
                }
			AnnouncementCron::where('id',$announcement->id)->update(['cron_status'=>'1']);
			\Log::info('Announcement ID:'.$announcement->id);
         }
        \Log::info('Announcement User Creation End! => ');


    }
}
