<?php
/**
 * whatsapp.php — sends WhatsApp messages via the configured HTTP gateway,
 * using cURL (available on essentially all shared hosts).
 */

class WhatsAppSendException extends Exception {}

/**
 * @throws WhatsAppSendException
 */
function sendWhatsAppMessage(string $gatewayUrl, string $apiKey, string $to, string $message): void {
    $ch = curl_init($gatewayUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(['to' => $to, 'message' => $message]),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ],
        CURLOPT_TIMEOUT => 15,
        CURLOPT_CONNECTTIMEOUT => 10
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        throw new WhatsAppSendException("cURL error: {$curlError}");
    }
    if ($httpCode < 200 || $httpCode >= 300) {
        throw new WhatsAppSendException("Gateway responded with {$httpCode}: {$response}");
    }
}
