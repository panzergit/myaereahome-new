<?php

namespace App\Models\v7;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\v7\Building;
use App\Models\v7\Unit;
use App\Models\v7\UserPurchaserUnit;
use App\Models\v7\Role;
use Illuminate\Database\Eloquent\Model;
use App\Services\PHPMailerService;

class UserRegistrationRequest extends Model
{
    protected $table = 'user_registration_requests';

    protected $fillable = [
      'account_id','user_id','profile_picture','building_no','unit_no','first_name','last_name','email','role_id', 'phone','mailing_address','country','postal_code','company_name','status','approved_date','contract_file','receive_intercom','accept_PDPA','first_vehicle','second_vehicle','reason'
    ];

   public function propertyinfo(){
        return $this->belongsTo('App\Models\v7\Property','account_id');
    }
    public function role(){
        return $this->belongsTo('App\Models\v7\Role','role_id');
    }

    public function getunit(){
        return $this->belongsTo('App\Models\v7\Unit','unit_no');
    }

    public function getcountry(){
        return $this->belongsTo('App\Models\v7\Country','country');
    }

    public function buildinginfo(){
        return $this->belongsTo('App\Models\v7\Building','building_no');
    }

    public function user(){
        return $this->belongsTo('App\Models\v7\User','user_id');
    }

    public static function sendemail($customername = '', $email = '', $from ='') {
        //$source = str_replace("+"," ",$source);
        $companyname = 'Aerea Home';
        $service = app(PHPMailerService::class);
        $returnMailData = $service->sendMail(
            trim($email),
            $companyname.': Your Account Has Been Activated',
            'emails.accountdetail', 
            [
                'companyname' => $companyname,
                'customername' => $customername
            ]
        );
        return  $returnMailData;
    }
    public static function cancelemail($customername = '', $email = '', $reason ='') {
        //$source = str_replace("+"," ",$source);
        $companyname = 'Aerea Home';
        $service = app(PHPMailerService::class);
        $returnMailData = $service->sendMail(
            trim($email),
            $companyname.': Your Registration Has Been Cancelled',
            'emails.canceldetail', 
            [
                'companyname' => $companyname,
                'customername' => $customername,
                'reason' => $reason
            ]
        );
        return  $returnMailData;
    }
}
