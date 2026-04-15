<?php

namespace App\Services\Whatsapp\Drivers;

use App\Services\Whatsapp\WhatsappServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteDriver implements WhatsappServiceInterface
{
    protected string $token;
    protected string $endpoint = 'https://api.fonnte.com/send';

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function send(string $target, string $message, array $options = [])
    {
        try {
            $payload = [
                'target' => $target,
                'message' => $message,
            ];

            // Handle options if needed (e.g. url, filename for media)
            if (isset($options['url'])) {
                $payload['url'] = $options['url'];
            }
            if (isset($options['filename'])) {
                $payload['filename'] = $options['filename'];
            }

            // Fonnte specific options could be handled here
            // e.g. schedule, countryCode, etc.

            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->endpoint, $payload);

            if ($response->failed()) {
                Log::error('Fonnte API Error: ' . $response->body());
                return false;
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Fonnte Driver Exception: ' . $e->getMessage());
            return false;
        }
    }
    public function getDeviceStatus()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post('https://api.fonnte.com/device');

            if ($response->failed()) {
                Log::error('Fonnte Device Status Error: ' . $response->body());
                return false;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Fonnte Device Status Exception: ' . $e->getMessage());
            return false;
        }
    }

    public function getQrCode()
    {
        // Fonnte returns QR code data in the device status response usually,
        // or we might need another endpoint.
        // Based on common Fonnte usage, /device endpoint returns connection status,
        // name, quota, and sometimes QR if disconnected.
        // For now, let's assume the QR is handled via the device status which returns
        // url or base64 if disconnected.

        $status = $this->getDeviceStatus();

        if ($status && isset($status['url'])) {
            return $status['url']; // Fonnte often returns a QR image URL
        }

        return null;
    }
}
