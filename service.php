<?php
require_once __DIR__ . '/includes/bootstrap.php';
$settings = getPublicSettings();

$serviceIdentifier = $_GET['service'] ?? ($_GET['id'] ?? null);
$service = null;

foreach ($settings['services'] ?? [] as $s) {
  if ($s['id'] === $serviceIdentifier || slugify($s['name']) === $serviceIdentifier) {
    $service = $s;
    break;
  }
}

if (!$service) {
  header('Location: ' . url('services.php'));
  exit;
}

$currentPage = 'service';

$seo = [
  'title' => !empty($service['seoTitle']) ? $service['seoTitle'] : ($service['name'] . ' - ' . $settings['platformName']),
  'description' => !empty($service['seoDescription']) ? $service['seoDescription'] : $service['description'],
  'ogImage' => !empty($service['image']) ? $service['image'] : ($settings['seo']['home']['ogImage'] ?? '')
];

$pageTitle = $seo['title'];

require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/nav.php';

$bookUrl = url('booking.php?service=' . urlencode(slugify($service['name'])));
?>
<main class="flex-grow bg-white py-16 sm:py-24 relative overflow-hidden">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">

    <!-- Main Service Layout (Left: Text, Right: Image) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-16 items-center animate-step-in mb-24">

      <!-- Text Section -->
      <div class="flex flex-col justify-center">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-[var(--secondary-color)] font-heading leading-tight mb-6">
          <?= e($service['name']) ?>
        </h1>

        <div class="prose prose-lg text-gray-600 leading-relaxed mb-10 max-w-none">
          <p><?= nl2br(e($service['description'])) ?></p>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 ">
          <a href="<?= e($bookUrl) ?>"
            class="inline-flex items-center justify-center w-full sm:w-auto bg-[var(--primary-color)] text-white px-4 py-4 rounded-xl font-bold shadow hover:shadow-md hover:opacity-95 transition-all">
            Book <?= e($service['name']) ?>
          </a>
          <a href="<?= url('contact.php') ?>"
            class="inline-flex items-center justify-center w-full sm:w-auto border border-[var(--primary-color)] text-[var(--primary-color)] hover:bg-gray-50 px-4 py-4 rounded-xl font-bold transition-all">
            Talk to an Expert
          </a>
        </div>
      </div>

      <!-- Image Section -->
      <?php if (!empty($service['image'])): ?>
        <div class="relative w-full rounded-3xl shadow-lg border border-gray-100 overflow-hidden"
          style="min-height: 400px; height: 100%;">
          <img src="<?= e($service['image']) ?>" alt="<?= e($service['name']) ?>"
            class="absolute inset-0 w-full h-full object-cover">
        </div>
      <?php else: ?>
        <div
          class="relative w-full bg-gray-50 flex flex-col items-center justify-center rounded-3xl border border-gray-200"
          style="min-height: 400px; height: 100%;">
          <?= renderIcon($service['icon'] ?? 'wrench', 'w-24 h-24 text-gray-300 mb-6') ?>
          <span class="font-bold text-xl text-gray-400"><?= e($service['name']) ?></span>
        </div>
      <?php endif; ?>

    </div>

    <!-- Other Services Section -->
    <div class="border-t border-gray-100 pt-16 mt-16">
      <div class="text-left mb-5">
        <h2 class="text-2xl font-bold tracking-tight text-[var(--secondary-color)] sm:text-3xl font-heading">Explore
          Other Services</h2>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($settings['services'] ?? [] as $s):
          if ($s['id'] === $service['id'])
            continue; // Skip current service
          $detailsUrl = url('service.php?service=' . urlencode(slugify($s['name'])));
          $hasImage = !empty($s['image']);
          $iconColors = ['#4f46e5', '#0891b2', '#059669', '#d97706', '#dc2626', '#7c3aed'];
          $iconBg = $iconColors[crc32($s['id'] ?? '') % count($iconColors)];
          ?>
          <div
            class="group bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col relative">
            <?php if ($hasImage): ?>
              <div class="relative h-48 overflow-hidden shrink-0">
                <img src="<?= e($s['image']) ?>" alt="<?= e($s['name']) ?>"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>
                <div class="absolute bottom-3 left-4">
                  <div class="p-2 rounded-xl bg-white/20 backdrop-blur-sm text-white border border-white/20">
                    <?= renderIcon($s['icon'] ?? 'wrench', 'w-4 h-4') ?>
                  </div>
                </div>
              </div>
              <div class="p-5 flex flex-col flex-1">
              <?php else: ?>
                <div class="h-1.5 w-full shrink-0" style="background: <?= e($iconBg) ?>;"></div>
                <div class="p-5 flex flex-col flex-1">
                  <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-xl text-white shadow-sm" style="background: <?= e($iconBg) ?>;">
                      <?= renderIcon($s['icon'] ?? 'wrench', 'w-5 h-5') ?>
                    </div>
                  </div>
                <?php endif; ?>
                <div class="space-y-1.5 flex-1">
                  <h3
                    class="text-sm font-bold text-[var(--secondary-color)] group-hover:text-[var(--primary-color)] transition-colors leading-snug">
                    <?= e($s['name']) ?>
                  </h3>
                  <p class="text-xs text-gray-500 leading-relaxed line-clamp-3"><?= e($s['description']) ?></p>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between">
                  <a href="<?= e($detailsUrl) ?>"
                    class="text-[10px] font-bold text-gray-400 hover:text-[var(--primary-color)] flex items-center gap-1 transition-colors">
                    <?= renderIcon('eye', 'w-3 h-3') ?> View Details
                  </a>
                  <a href="<?= e(url('booking.php?service=' . urlencode(slugify($s['name'])))) ?>"
                    class="inline-flex items-center gap-1 text-xs font-bold text-white bg-[var(--primary-color)] px-3 py-1.5 rounded-lg hover:opacity-90 transition-opacity shadow-sm">
                    <?= renderIcon('calendar', 'w-3 h-3') ?> Book Now
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>