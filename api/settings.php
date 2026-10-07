<?php
/**
 * settings.php — GET/POST /api/settings & /api/settings/secrets
 */

function handleGetPublicSettings(): void {
    $settings = getSetting('public') ?? getDefaultPublicSettings();
    jsonResponse($settings);
}

function handleGetSecretSettings(): void {
    requireAdmin();
    $secrets = getSetting('secrets') ?? getDefaultSecretSettings();
    $masked = $secrets;
    if (!empty($masked['smtpPass'])) {
        $masked['smtpPass'] = '••••••••••••';
    }
    if (!empty($masked['waApiKey'])) {
        $masked['waApiKey'] = '••••••••••••';
    }
    jsonResponse($masked);
}

function handleUpdatePublicSettings(): void {
    requireAdmin();
    $incoming = getJsonBody();
    $current = getSetting('public') ?? getDefaultPublicSettings();
    $merged = array_merge($current, $incoming);
    setSetting('public', $merged);
    jsonResponse(['success' => true, 'message' => 'Settings updated successfully', 'settings' => $merged]);
}

function handleUpdateSecretSettings(): void {
    requireAdmin();
    $current = getSetting('secrets') ?? getDefaultSecretSettings();
    $incoming = getJsonBody();

    // Preserve existing secret values if the client submitted the masked placeholder
    if (!isset($incoming['smtpPass']) || $incoming['smtpPass'] === '' || $incoming['smtpPass'] === '••••••••••••') {
        $incoming['smtpPass'] = $current['smtpPass'];
    }
    if (!isset($incoming['waApiKey']) || $incoming['waApiKey'] === '' || $incoming['waApiKey'] === '••••••••••••') {
        $incoming['waApiKey'] = $current['waApiKey'];
    }

    $merged = array_merge($current, $incoming);
    setSetting('secrets', $merged);
    jsonResponse(['success' => true, 'message' => 'Secrets updated successfully']);
}
