<?php

namespace App\Models\v2;
//use Illuminate\Support\Facades\Mail;

use Mail;
use App\Models\v2\User;

use Illuminate\Database\Eloquent\Model;



class FirebaseNotification extends Model
{
   
    public function ios_notification($title,$body,$ios_devices_to_send,$data='') {
		//$ios_devices_to_send  =array();
		//$ios_devices_to_send[]= 'f7i_crEbekPEsQQz8nX74s:APA91bHIAZZbEEMmC-X2liMX_YQh0oEVDIRxtbO9BFsWFp9192Q8-GTuMO_9DqXAc1smqtwZGIpsJANaN9EMFClQYl-Z1a9fIJXoquf5f7LzUizA0NFRFvNorUFW6IMEKZaSerg234GF';
		
		$array_data = array(
			'registration_ids'=>$ios_devices_to_send,
			'priority'=>'high',
			'notification'=>array("body" => $body,"title"=>$title,"sound"=> "ring.mp3"),
			'data'=>array('body'=>$body)
			); 
		
	
		$curl_url = env('FIREBASE_URL');
		$server_key = env('SERVER_KEY');

		$jsonDataEncoded_get  = json_encode($array_data);
		$ch_get = curl_init($curl_url);
		curl_setopt($ch_get, CURLOPT_CUSTOMREQUEST, "POST"); 
		curl_setopt($ch_get, CURLOPT_POSTFIELDS, $jsonDataEncoded_get );
		curl_setopt($ch_get, CURLOPT_RETURNTRANSFER, true);    
		curl_setopt($ch_get, CURLOPT_HTTPHEADER, array(                                                                          
			'Content-Type: application/json',                                                                                
			'Authorization: ' .$server_key)                                                                       
	
		);    
		$response = curl_exec($ch_get);
		$errno = curl_errno($ch_get);
		
		if ($errno) {
			return false;
		}
		
	}

	public function android_notification($title,$body,$android_devices_to_send,$data='') {
		//$android_devices_to_send = "";
		$array_data = array(
			'registration_ids'=>$android_devices_to_send,
			'priority'=>'high',
			'data'=>array('body'=>$body)
			); 
		
		$curl_url = env('FIREBASE_URL');
		$server_key = env('SERVER_KEY');
		
		$jsonDataEncoded_get  = json_encode($array_data);
		$ch_get = curl_init($curl_url);

		curl_setopt($ch_get, CURLOPT_CUSTOMREQUEST, "POST"); 
		curl_setopt($ch_get, CURLOPT_POSTFIELDS, $jsonDataEncoded_get );
		curl_setopt($ch_get, CURLOPT_RETURNTRANSFER, true);    
		curl_setopt($ch_get, CURLOPT_HTTPHEADER, array(                                                                          
			'Content-Type: application/json',                                                                                
			'Authorization: ' .$server_key)                                                                       
	
		);    
		$response = curl_exec($ch_get);
			$errno = curl_errno($ch_get);
			if ($errno) {
				return false;
			}
			curl_close($ch_get);
	}
	//Push notifications
	public function ios_msg_notification($title,$body,$ios_devices_to_send,$data='') {
	
		$fields = [
            'project_id'         => 'aerea-staging',
            'device_tokens'      => $ios_devices_to_send,
            'title'              => $title,
            'message'            => $body,
            'additional_data'    => $data,
			'to'    			 => 'ios'
        ];
        $fields_string = json_encode($fields);
        $ch = curl_init();
		$url = env('FIREBASE_URL');
        curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string); 
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array('accept: application/json',
    'content-type: application/json')); 
        $result = curl_exec($ch);
        $json = json_decode($result,true);
        $err = curl_error($ch);
        curl_close($ch);
        return $json;
		
	}

	public function android_msg_notification($title,$body,$android_devices_to_send,$data='') {
		$fields = [
            'project_id'         => 'aerea-staging',
            'device_tokens'      => $android_devices_to_send,
            'title'              => $title,
            'message'            => $body,
            'additional_data'    => $data,
			'to'    			 => 'android'
        ];

		$fields_string = json_encode($fields);
        $ch = curl_init();
		$url = env('FIREBASE_URL');
        curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string); 
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array('accept: application/json',
    'content-type: application/json')); 
        $result = curl_exec($ch);
        $json = json_decode($result,true);
        $err = curl_error($ch);
        curl_close($ch);
        return $json;
			
	}

	public function ios_release_notification($title,$body,$ios_devices_to_send,$data='') {
		$fields = [
            'project_id'         => 'aerea-staging',
            'device_tokens'      => $ios_devices_to_send,
            'title'              => $title,
            'message'            => $body,
            'additional_data'    => $data,
			'to'    			 => 'ios'
        ];

		$fields_string = json_encode($fields);
        $ch = curl_init();
		$url = env('FIREBASE_URL');
        curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string); 
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array('accept: application/json',
    'content-type: application/json')); 
        $result = curl_exec($ch);
        $json = json_decode($result,true);
        $err = curl_error($ch);
        curl_close($ch);
        return $json;
		
	}

	public function android_release_notification($title,$body,$android_devices_to_send,$data='') {
		$fields = [
            'project_id'         => 'aerea-staging',
            'device_tokens'      => $android_devices_to_send,
            'title'              => $title,
            'message'            => $body,
            'additional_data'    => $data,
			'to'    			 => 'android'
        ];

        $fields_string = json_encode($fields);
        $ch = curl_init();
		$url = env('FIREBASE_URL');
        curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string); 
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array('accept: application/json',
    'content-type: application/json')); 
        $result = curl_exec($ch);
        $json = json_decode($result,true);
        $err = curl_error($ch);
        curl_close($ch);
        return $json;
			
	}


	//Push notifications
	public function ios_manager_notification($title,$body,$ios_devices_to_send,$data='') {
		$fields = [
            'project_id'         => 'aerea-staging',
            'device_tokens'      => $ios_devices_to_send,
            'title'              => $title,
            'message'            => $body,
            'additional_data'    => $data,
			'to'    			 => 'ios'
        ];

        $fields_string = json_encode($fields);
        $ch = curl_init();
		$url = env('FIREBASE_URL');
        curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string); 
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array('accept: application/json',
    'content-type: application/json')); 
        $result = curl_exec($ch);
        $json = json_decode($result,true);
        $err = curl_error($ch);
        curl_close($ch);
        return $json;
		
	}

	public function android_manager_notification($title,$body,$android_devices_to_send,$data='') {
		$fields = [
            'project_id'         => 'aerea-staging',
            'device_tokens'      => $android_devices_to_send,
            'title'              => $title,
            'message'            => $body,
            'additional_data'    => $data,
			'to'    			 => 'android'
        ];

		$fields_string = json_encode($fields);
        $ch = curl_init();
		$url = env('FIREBASE_URL');
        curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string); 
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array('accept: application/json',
    'content-type: application/json')); 
        $result = curl_exec($ch);
        $json = json_decode($result,true);
        $err = curl_error($ch);
        curl_close($ch);
        return $json;
			
	}


}
