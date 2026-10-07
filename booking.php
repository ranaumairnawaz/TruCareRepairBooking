<?php
require_once __DIR__ . '/includes/bootstrap.php';
$settings = getPublicSettings();
$currentPage = 'booking';
$seo = pageSeo($settings, 'booking');

$secrets = getSetting('secrets') ?: [];
$googleMapsApiKey = $secrets['googleMapsApiKey'] ?? '';

$quickBook = null;
if (isset($_GET['urgent'])) {
    $isUrgent = $_GET['urgent'] === '1';
    $quickBook = [
        'isUrgent' => $isUrgent,
        'date' => $isUrgent ? 'Same-Day Emergency' : sanitizeInput($_GET['date'] ?? '', 60),
        'timeSlot' => $isUrgent ? 'ASAP (Emergency Dispatch)' : sanitizeInput($_GET['timeSlot'] ?? '', 60),
    ];
}

require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/nav.php';
?>
<main class="flex-grow py-12 bg-gradient-to-br from-primary-light/40 via-white to-slate-50/20">
    <div id="booking-wizard-root"
        class="w-full max-w-3xl mx-auto bg-white rounded-3xl shadow-2xl border border-slate-100 p-6 sm:p-10 transition-all duration-300 text-left">
    </div>
</main>
<script>
    window.BOOKING_CONFIG = <?= json_encode([
        'services' => $settings['services'] ?? [],
        'serviceLocations' => $settings['serviceLocations'] ?? [],
        'csrfToken' => csrfToken(),
        'apiUrl' => url('api/booking'),
        'quickBook' => $quickBook,
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>
<?php
$extraScripts = ['booking-wizard.js'];
if ($googleMapsApiKey) {
    echo '<script src="https://maps.googleapis.com/maps/api/js?key=' . e($googleMapsApiKey) . '&libraries=places"></script>';
}
require __DIR__ . '/includes/footer.php';
