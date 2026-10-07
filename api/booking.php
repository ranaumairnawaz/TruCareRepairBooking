<?php
/**
 * booking.php — POST /api/booking (public, rate-limited)
 *
 * Validates input, stores the booking, then attempts to send a confirmation
 * email (PHPMailer/SMTP) and WhatsApp notification (cURL gateway).
 */

function handleCreateBooking(): void {
    if (!rateLimit('booking', 60 * 60, 20)) {
        jsonResponse(['error' => 'Too many booking requests from this device. Please try again later.'], 429);
    }

    requireCsrf();

    $body = getJsonBody();

    $name = clean($body['name'] ?? null, 120);
    $email = clean($body['email'] ?? null, 180);
    $phone = clean($body['phone'] ?? null, 40);
    $machine = clean($body['machine'] ?? null, 120);
    $symptom = clean($body['symptom'] ?? null, 120) ?: 'Other';
    $country = clean($body['country'] ?? null, 100);
    $city = clean($body['city'] ?? null, 100);
    $address = clean($body['address'] ?? null, 300);
    $date = clean($body['date'] ?? null, 60);
    $timeSlot = clean($body['timeSlot'] ?? null, 60);
    $message = clean($body['message'] ?? null, 1000);

    $emailPattern = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
    if (!$name || !$email || !preg_match($emailPattern, $email) || !$phone || !$machine
        || !$country || !$city || !$address || !$date || !$timeSlot) {
        jsonResponse(['error' => 'Missing or invalid required booking fields'], 400);
    }

    $bookingId = generateUuidV4();
    $createdAt = gmdate('Y-m-d\TH:i:s.v\Z');

    $booking = [
        'id' => $bookingId,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'machine' => $machine,
        'symptom' => $symptom,
        'country' => $country,
        'city' => $city,
        'address' => $address,
        'date' => $date,
        'timeSlot' => $timeSlot,
        'message' => $message,
        'status' => 'pending',
        'createdAt' => $createdAt
    ];
    insertBooking($booking);

    $secrets = getSetting('secrets') ?? getDefaultSecretSettings();
    $publicSettings = getSetting('public') ?? getDefaultPublicSettings();
    $platformName = $publicSettings['platformName'] ?? 'PrecisionMech';
    $appointmentDateTime = "{$date} at {$timeSlot}";
    $location = "{$city}, {$country}";

    $emailSent = false;
    $whatsappSent = false;
    $emailError = '';
    $whatsappError = '';

    // ---- Email confirmation via PHPMailer/SMTP ----
    $emailResult = sendBookingEmail($secrets, $platformName, $email, $name, [
        'name' => $name,
        'machine' => $machine,
        'symptom' => $symptom,
        'location' => $location,
        'datetime' => $appointmentDateTime,
        'phone' => $phone
    ]);
    $emailSent = $emailResult['sent'];
    $emailError = $emailResult['error'];

    // ---- WhatsApp notification via cURL gateway (owner/company only — not the customer) ----
    $gatewayUrl = trim($secrets['waGatewayUrl'] ?? 'http://13.60.229.151:3000/api/messages/send');
    if (!empty($secrets['waApiKey']) && $gatewayUrl && !empty($secrets['waReceiverNumber'])) {
        try {
            $service = $symptom && $symptom !== 'Other' ? "{$machine} - {$symptom}" : $machine;
            $fullLocation = "{$address}, {$city}, {$country}";

            $lines = [
                '*Booking Details*',
                "Time: {$appointmentDateTime}",
                "Service: {$service}",
                "Location: {$fullLocation}",
                '',
                '*Contact Details*',
                "Name: {$name}",
                "Phone: {$phone}",
                "Email: {$email}"
            ];
            if ($message) {
                $lines[] = "Note: {$message}";
            }
            $adminMsg = implode("\n", $lines);

            sendWhatsAppMessage($gatewayUrl, $secrets['waApiKey'], $secrets['waReceiverNumber'], $adminMsg);
            $whatsappSent = true;
        } catch (Throwable $e) {
            error_log('Error sending WhatsApp message: ' . $e->getMessage());
            $whatsappError = 'Could not send the WhatsApp notification.';
        }
    } else {
        $whatsappError = 'WhatsApp gateway is not fully configured. Set it up in the admin panel.';
    }

    jsonResponse([
        'success' => true,
        'bookingId' => $bookingId,
        'emailSent' => $emailSent,
        'whatsappSent' => $whatsappSent,
        'emailError' => $emailError,
        'whatsappError' => $whatsappError
    ]);
}

function generateUuidV4(): string {
    $data = random_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
