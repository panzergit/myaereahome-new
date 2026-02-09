<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ApplePushService
{
    protected string $teamId;
    protected string $keyId;
    protected string $bundleId;
    protected string $p8Path;
    protected bool $sandbox;

    public function __construct()
    {
        $this->teamId  = config('apns.team_id');
        $this->keyId   = config('apns.key_id');
        $this->bundleId = config('apns.bundle_id');
        $this->p8Path  = config('apns.key_path');
        $this->sandbox = config('apns.use_sandbox', true);
    }

    /**
     * Send VoIP Push Notification
     */
    public function sendVoip(string $deviceToken, array $payload): array
    {
        $jwt = $this->generateJwt();

        $url = $this->sandbox
            ? "https://api.sandbox.push.apple.com/3/device/{$deviceToken}"
            : "https://api.push.apple.com/3/device/{$deviceToken}";

        $headers = [
            "authorization: bearer {$jwt}",
            "apns-topic: {$this->bundleId}.voip",
            "apns-push-type: voip",
            "apns-priority: 10",
            "content-type: application/json",
        ];

        return $this->curlRequest($url, $headers, $payload);
    }

    /**
     * Generate JWT for APNs
     */
    protected function generateJwt(): string
    {
        $header = base64_encode(json_encode([
            'alg' => 'ES256',
            'kid' => $this->keyId,
        ]));

        $claims = base64_encode(json_encode([
            'iss' => $this->teamId,
            'iat' => time(),
        ]));

        $unsignedToken = $header . '.' . $claims;

        $privateKey = openssl_pkey_get_private(file_get_contents($this->p8Path));
        openssl_sign($unsignedToken, $signature, $privateKey, 'sha256');

        return $unsignedToken . '.' . base64_encode($signature);
    }

    /**
     * CURL HTTP/2 request
     */
    protected function curlRequest(string $url, array $headers, array $payload): array
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            Log::error('APNs CURL Error', ['error' => curl_error($ch)]);
            return ['success' => false, 'error' => curl_error($ch)];
        }

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'success' => $status === 200,
            'status' => $status,
            'response' => $response,
        ];
    }
}
