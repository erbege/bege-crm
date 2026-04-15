<?php

namespace App\Services\Whatsapp;

interface WhatsappServiceInterface
{
    /**
     * Send a text message to a specific number.
     *
     * @param string $target The phone number (e.g., '081234567890').
     * @param string $message The message content.
     * @param array $options Optional parameters (buttons, mediaUrl, etc.).
     * @return array|bool Response from the provider or true/false.
     */
    /**
     * Send a message.
     *
     * @param string $target
     * @param string $message
     * @param array $options
     * @return mixed
     */
    public function send(string $target, string $message, array $options = []);

    /**
     * Get device connection status.
     *
     * @return array|bool
     */
    public function getDeviceStatus();

    /**
     * Get QR Code for connection (if applicable).
     *
     * @return string|null Base64 image or raw string
     */
    public function getQrCode();
}
