<?php

namespace App\Services\Whatsapp;

use App\Models\Setting;
use App\Models\WhatsappMessageTemplate;
use App\Services\Whatsapp\Drivers\FonnteDriver;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected ?WhatsappServiceInterface $driver = null;

    public function __construct()
    {
        $this->resolveDriver();
    }

    protected function resolveDriver()
    {
        $provider = Setting::get('whatsapp.provider');
        $token = Setting::get('whatsapp.token');

        if (!$provider || !$token) {
            // Log::warning('WhatsApp provider or token not configured.');
            return;
        }

        switch ($provider) {
            case 'fonnte':
                $this->driver = new FonnteDriver($token);
                break;
            // Future drivers:
            // case 'qontak':
            //     $this->driver = new QontakDriver($token);
            //     break;
            default:
                Log::error("Unsupported WhatsApp provider: {$provider}");
                break;
        }
    }

    /**
     * Send a message using the configured driver.
     */
    public function send(string $target, string $message, array $options = [])
    {
        if (!$this->driver) {
            Log::warning('WhatsApp driver not initialized. Message not sent to: ' . $target);
            return false;
        }

        return $this->driver->send($target, $message, $options);
    }

    /**
     * Send a message using a template.
     */
    public function sendTemplate(string $target, string $templateName, array $data = [], array $options = [])
    {
        $template = WhatsappMessageTemplate::where('name', $templateName)
            ->where('is_active', true)
            ->first();

        if (!$template) {
            Log::warning("WhatsApp template '{$templateName}' not found or inactive.");
            return false;
        }

        $message = $template->format($data);

        return $this->send($target, $message, $options);
    }
    public function getDeviceStatus()
    {
        if (!$this->driver) {
            return false;
        }
        return $this->driver->getDeviceStatus();
    }

    public function getQrCode()
    {
        if (!$this->driver) {
            return null;
        }
        return $this->driver->getQrCode();
    }
}
