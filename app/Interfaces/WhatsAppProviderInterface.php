<?php

namespace App\Interfaces;

use App\Models\ClinicWhatsappSetting;

interface WhatsAppProviderInterface
{
    /**
     * Send a simple text message
     * 
     * @param ClinicWhatsappSetting $settings
     * @param string $phone
     * @param array $params
     * @param int|null $appointmentId
     * @return bool
     */
    public function sendSimpleMessage(ClinicWhatsappSetting $settings, string $phone, array $params = [], ?int $appointmentId = null): bool;

    /**
     * Send a template message
     * 
     * @param ClinicWhatsappSetting $settings
     * @param string $phone
     * @param string $templateName
     * @param array $params
     * @param int|null $appointmentId
     * @return bool
     */
    public function sendTemplateMessage(ClinicWhatsappSetting $settings, string $phone, string $templateName, array $params = [], ?int $appointmentId = null): bool;

    /**
     * Get connection/authentication status
     * 
     * @param ClinicWhatsappSetting $settings
     * @return array ['status' => string, 'details' => mixed]
     */
    public function getStatus(ClinicWhatsappSetting $settings): array;

    /**
     * Get QR Code for connection (if applicable)
     * 
     * @param ClinicWhatsappSetting $settings
     * @return string|null
     */
    public function getQrCode(ClinicWhatsappSetting $settings): ?string;
}
