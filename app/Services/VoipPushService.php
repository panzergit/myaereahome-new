<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;

class VoipPushService
{
    public function sendCallPush(string $voipToken, array $payload)
    {
        $jwt = $this->generateJwt();

        print_r($jwt);

        $url = config('apns.use_sandbox')
            ? "https://api.sandbox.push.apple.com/3/device/{$voipToken}"
            : "https://api.push.apple.com/3/device/{$voipToken}";

        return Http::withHeaders([
            'authorization' => "bearer {$jwt}",
            'apns-topic' => config('apns.bundle_id') . '.voip',
            'apns-push-type' => 'voip',
            'apns-priority' => '10',
        ])->withBody(json_encode([
            'aps' => [
                'alert' => 'Incoming Call',
                'sound' => 'default',
            ],
            'call' => $payload,
        ]), 'application/json')->post($url);
    }

    private function generateJwt()
    {
        $key = file_get_contents(config('apns.key_path'));

        return JWT::encode([
            'iss' => config('apns.team_id'),
            'iat' => time(),
        ], $key, 'ES256', config('apns.key_id'));
    }
}

?>