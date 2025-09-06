<?php

namespace App\Services;
use GuzzleHttp\Client;
use Google_Client;
use Illuminate\Support\Facades\Log;

class FcmService
{

    protected string $project_id;
    protected string $credentials_path;
    protected Client $http;

    public function __construct()
    {
        $this->project_id = config('fcm.project_id');
        $this->credentials_path = config('fcm.credentials_json');
        $this->http = new Client(['timeout' => 10]);
    }
    protected function get_access_token():string
    {
        if (!file_exists($this->credentials_path)) {
            Log::error("FCM credentials file not found: {$this->credentials_path}");
            return '';
        }
        $client = new Google_Client();
        $client->setAuthConfig($this->credentials_path);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        if (isset($token['error'])) {
            Log::error('FCM fetch token error', ['error' => $token]);
            return '';
        }

        return $token['access_token'] ?? '';
    }

    public function send_to_token(string $deviceToken, array $notification, array $data = []): bool
    {
        if (!$deviceToken) {
            Log::warning('FCM: empty device token');
            return false;
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$this->project_id}/messages:send";
        $accessToken = $this->get_access_token();
        $payload = [
            'message' => [
                'token' => $deviceToken,
                'notification' => [
                    'title' => $notification['title'] ?? '',
                    'body'  => $notification['body'] ?? '',
                ],
                'data' => array_map('strval', $data),
                'android' => [
                    'priority' => 'HIGH',
                    'notification' => [
                        'sound' => 'default',
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'content-available' => 1,
                        ],
                    ],
                ],
                'webpush' => [
                    'headers' => ['Urgency' => 'high'],
                    'notification' => [
                        'icon' => '/icons/icon-192x192.png',
                        'badge' => '/icons/badge-72x72.png',
                    ],
                ],
            ],
        ];
        try {
            $res = $this->http->post($url, [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                    'Content-Type'  => 'application/json',
                ],
                'json' => $payload,
            ]);

            $ok = $res->getStatusCode() >= 200 && $res->getStatusCode() < 300;
            if (!$ok) {
                Log::error('FCM send failed', ['status' => $res->getStatusCode(), 'body' => (string) $res->getBody()]);
            }

            return $ok;
        } catch (\Throwable $e) {
            Log::error('FCM exception: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return false;
        }
    }


}
