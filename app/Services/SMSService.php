<?php

namespace App\Services;
use Exception;

class SMSService
{
    public function sendSMS($username,$password,$sender,$number,$message,$userId)
    {
        $smsGatewayUrl = "https://ww3.isms.com.my/isms_send_all_id.php";
        $number = '+'.trim(str_replace('%2b','',$number));
        $message = trim(str_replace('+',' ',$message));
        
        $curl = curl_init();
        $postData = [
            'un' => $username,
            'pwd' => $password,
            'dstno' => $number,
            'msg' => $message,
            'type' => 1,
            'sendid' => $sender,
            'agreedterm' => 'YES'
        ];
    
        curl_setopt_array($curl, [
            CURLOPT_URL => $smsGatewayUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
        ]);
    
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        
        if ($err) {
            \Log::info("SMS CURL ERROR => ".$err.' = MOBILE NUMBER : '.$number.' = USER ID '.$userId);
        } else {
            \Log::info("SMS RETURN RESPONSE => ".serialize($response).' = MOBILE NUMBER : '.$number.' = USER ID '.$userId);
        }
        \Log::info("SMS DATA => ".serialize($postData));
    }
}
?>