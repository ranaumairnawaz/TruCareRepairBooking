<?php
require_once __DIR__ . '/includes/bootstrap.php';
$settings = getPublicSettings();
$currentPage = 'services';
$seo = pageSeo($settings, 'about');
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/nav.php';
?>
<main class="flex-grow py-8">
  <?php require __DIR__ . '/includes/sections/services.php'; ?>
</main>
<?php require __DIR__ . '/includes/footer.php';
