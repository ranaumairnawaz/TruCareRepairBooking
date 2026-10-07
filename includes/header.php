<?php
/** @var array $settings */
/** @var string $currentPage */
/** @var array $seo */
/** @var string $pageTitle */
if (!isset($settings)) {
    require_once __DIR__ . '/bootstrap.php';
    $settings = getPublicSettings();
}
$seo = $seo ?? pageSeo($settings, $currentPage ?? 'home');
$pageTitle = $pageTitle ?? ($seo['title'] ?? $settings['platformName']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <title><?= e($pageTitle) ?></title>
  <meta name="description" content="<?= e($seo['description'] ?? '') ?>">
  <meta property="og:title" content="<?= e($seo['title'] ?? $pageTitle) ?>">
  <meta property="og:description" content="<?= e($seo['description'] ?? '') ?>">
  <meta property="og:image" content="<?= e($seo['ogImage'] ?? '') ?>">
  <link rel="icon" href="<?= e($settings['favicon'] ?? '') ?>">
  <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
  <link rel="stylesheet" href="<?= e(googleFontsUrl($settings)) ?>">
  <?= themeStyleBlock($settings) ?>
  <meta name="csrf-token" content="<?= e(csrfToken()) ?>">
  <script>window.BASE_PATH = <?= json_encode(BASE_PATH) ?>;</script>
  <style>
    @keyframes stepIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    .animate-step-in { animation: stepIn 0.25s ease-out; }
  </style>
</head>
<body class="min-h-screen flex flex-col justify-between bg-brand-bg text-brand-text">
