<?php
require_once __DIR__ . '/includes/bootstrap.php';
$settings = getPublicSettings();
$currentPage = 'contact';
$seo = pageSeo($settings, 'contact');
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/nav.php';
?>
<main class="flex-grow py-8">
  <?php require __DIR__ . '/includes/sections/contact.php'; ?>
</main>
<?php require __DIR__ . '/includes/footer.php';
