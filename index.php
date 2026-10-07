<?php
require_once __DIR__ . '/includes/bootstrap.php';
$settings = getPublicSettings();
$currentPage = 'home';
$seo = pageSeo($settings, 'home');
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/nav.php';
?>
<main class="flex-grow">
  <?php require __DIR__ . '/includes/sections/hero.php'; ?>
  <?php require __DIR__ . '/includes/sections/services.php'; ?>
  <?php require __DIR__ . '/includes/sections/about.php'; ?>
  <?php require __DIR__ . '/includes/sections/contact.php'; ?>
</main>
<?php
$extraScripts = ['hero-quickbook.js'];
require __DIR__ . '/includes/footer.php';
