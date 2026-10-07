<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (empty($_SESSION['admin_logged_in'])) {
  header('Location: ' . url('admin.php'));
  exit;
}
$settings = getPublicSettings();
$currentPage = 'admin';
$seo = pageSeo($settings, 'home');
require __DIR__ . '/../includes/header.php';
// Removed nav.php to ensure dedicated admin layout
?>
<style>
  footer {
    display: none !important;
  }
</style>
<main class="h-screen w-screen bg-[#F4F7FE] overflow-hidden flex text-gray-800 font-sans">
  <div id="admin-root" class="w-full h-full"></div>
</main>
<script>
  window.ADMIN_CONFIG = <?= json_encode([
    'settings' => $settings,
    'csrfToken' => csrfToken(),
    'apiBase' => url('api'),
    'isAuthenticated' => !empty($_SESSION['admin_logged_in']),
  ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>
<?php
$extraScripts = ['admin.js'];
require __DIR__ . '/../includes/footer.php';
