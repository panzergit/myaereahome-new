<?php

namespace App\Models\v7;
//use Illuminate\Support\Facades\Mail;

use Mail;
use App\Models\v7\User;

use Illuminate\Database\Eloquent\Model;



class Thinmoo extends Model
{
   
    public function call_api($customername = '', $email = '') {

			$url = env('THINMOO_API_URL');

			//The data you want to send via POST
			$fields = [
				'__VIEWSTATE '      => $state,
				'__EVENTVALIDATION' => $valid,
				'btnSubmit'         => 'Submit'
			];

			$fields_string = http_build_query($fields);

			$ch = curl_init();

			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

			curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

			
			$result = curl_exec($ch);
			$err = curl_error($ch);
			curl_close($ch);
			echo $result;


	
	}

	

}
