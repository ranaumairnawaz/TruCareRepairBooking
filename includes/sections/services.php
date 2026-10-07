<?php
/** @var array $settings */
$bookUrl = url('booking.php');
?>
<section id="services-section" class="py-16 sm:py-24 bg-gray-50/50 relative">
  <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

    <!-- ── Machines / Appliances We Service ─────────────────────── -->
    <div class="text-center space-y-3 mb-12">
      <span
        class="text-xs font-bold text-[var(--primary-color)] uppercase tracking-wider bg-primary-light px-3 py-1 rounded-full">What
        We Support</span>
      <h2 class="text-3xl font-bold tracking-tight text-[var(--secondary-color)] sm:text-4xl font-heading">Appliances
        &amp; Machines We Service</h2>
      <p class="mx-auto max-w-2xl text-gray-500 text-sm">Our expert technicians are trained to handle a wide variety of
        household and commercial appliances.</p>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 mb-20">
      <?php foreach ($settings['machines'] ?? [] as $machine): ?>
        <div
          class="group relative bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col items-center text-center">
          <?php if (!empty($machine['image'])): ?>
            <!-- Image present: show it as the card background with overlay -->
            <div class="w-full aspect-square relative overflow-hidden">
              <img src="<?= e($machine['image']) ?>" alt="<?= e($machine['name']) ?>"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
              <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
              <div class="absolute inset-0 flex flex-col items-center justify-end p-4">
                <div
                  class="p-2.5 rounded-xl bg-white/20 backdrop-blur-sm text-white mb-2 group-hover:bg-[var(--primary-color)] transition-colors">
                  <?= renderIcon($machine['icon'], 'w-5 h-5') ?>
                </div>
                <h3 class="text-xs font-bold text-white leading-tight"><?= e($machine['name']) ?></h3>
              </div>
            </div>
          <?php else: ?>
            <!-- No image: icon + name layout -->
            <div class="p-5 flex flex-col items-center">
              <div
                class="p-3.5 rounded-full bg-primary-light text-[var(--primary-color)] mb-3 group-hover:scale-110 group-hover:bg-[var(--primary-color)] group-hover:text-white transition-all duration-300">
                <?= renderIcon($machine['icon'], 'w-6 h-6') ?>
              </div>
              <h3
                class="text-xs font-bold text-[var(--secondary-color)] group-hover:text-[var(--primary-color)] transition-colors">
                <?= e($machine['name']) ?>
              </h3>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- ── Services Grid ─────────────────────────────────────────── -->
    <div class="text-center space-y-3 mb-12">
      <span
        class="text-xs font-bold text-[var(--primary-color)] uppercase tracking-wider bg-primary-light px-3 py-1 rounded-full">Available
        Services</span>
      <h2 class="text-3xl font-bold tracking-tight text-[var(--secondary-color)] sm:text-4xl font-heading">Our Specialty
        Repair Services</h2>
      <p class="mx-auto max-w-2xl text-gray-500 text-sm">We provide comprehensive diagnostic assessments. Every service
        is handled with original OEM parts.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <?php foreach ($settings['services'] ?? [] as $service):
        $detailsUrl = url('service.php?service=' . urlencode(slugify($service['name'])));
        $hasImage = !empty($service['image']);
        $iconColors = ['#4f46e5', '#0891b2', '#059669', '#d97706', '#dc2626', '#7c3aed'];
        $iconBg = $iconColors[crc32($service['id'] ?? '') % count($iconColors)];
        ?>
        <div
          class="group bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col relative">

          <?php if ($hasImage): ?>
            <!-- Card with image hero -->
            <div class="relative h-48 overflow-hidden shrink-0">
              <img src="<?= e($service['image']) ?>" alt="<?= e($service['name']) ?>"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
              <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>
              <!-- Price badge -->
              <?php if (!empty($service['price'])): ?>
                <div
                  class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm text-[var(--primary-color)] text-xs font-black px-2.5 py-1 rounded-full shadow">
                  <?= e($service['price']) ?>
                </div>
              <?php endif; ?>
              <!-- Icon overlay -->
              <div class="absolute bottom-3 left-4">
                <div class="p-2 rounded-xl bg-white/20 backdrop-blur-sm text-white border border-white/20">
                  <?= renderIcon($service['icon'] ?? 'wrench', 'w-4 h-4') ?>
                </div>
              </div>
            </div>
            <div class="p-5 flex flex-col flex-1">
            <?php else: ?>
              <!-- Card without image: colour accent bar + icon -->
              <div class="h-1.5 w-full shrink-0" style="background: <?= e($iconBg) ?>;"></div>
              <div class="p-5 flex flex-col flex-1">
                <div class="flex items-center justify-between mb-4">
                  <div class="p-3 rounded-xl text-white shadow-sm" style="background: <?= e($iconBg) ?>;">
                    <?= renderIcon($service['icon'] ?? 'wrench', 'w-5 h-5') ?>
                  </div>
                  <?php if (!empty($service['price'])): ?>
                    <span
                      class="text-xs font-black text-[var(--primary-color)] bg-primary-light px-2 py-0.5 rounded-full"><?= e($service['price']) ?></span>
                  <?php endif; ?>
                </div>
              <?php endif; ?>

              <!-- Body -->
              <div class="space-y-1.5 flex-1">
                <h3
                  class="text-sm font-bold text-[var(--secondary-color)] group-hover:text-[var(--primary-color)] transition-colors leading-snug">
                  <?= e($service['name']) ?>
                </h3>
                <p class="text-xs text-gray-500 leading-relaxed line-clamp-3"><?= e($service['description']) ?></p>
              </div>

              <!-- Actions -->
              <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between">
                <a href="<?= e($detailsUrl) ?>"
                  class="text-[10px] font-bold text-gray-400 hover:text-[var(--primary-color)] flex items-center gap-1 transition-colors">
                  <?= renderIcon('eye', 'w-3 h-3') ?> View Details
                </a>
                <a href="<?= e(url('booking.php?service=' . urlencode(slugify($service['name'])))) ?>"
                  class="inline-flex items-center gap-1 text-xs font-bold text-white bg-[var(--primary-color)] px-3 py-1.5 rounded-lg hover:opacity-90 transition-opacity shadow-sm">
                  <?= renderIcon('calendar', 'w-3 h-3') ?> Book Now
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- ── CTA Banner ─────────────────────────────────────────────── -->
      <div
        class="mt-16 bg-[var(--secondary-color)] rounded-2xl p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-6 text-left shadow-lg">
        <div class="space-y-1">
          <h3 class="text-base font-bold text-white font-heading">Need custom or enterprise commercial repairs?</h3>
          <p class="text-xs text-gray-400 max-w-xl">We service major kitchen equipment, laundry lines, and central air
            systems for restaurants, hotels, and apartments. Contact us today for corporate service plans.</p>
        </div>
        <a href="<?= e($bookUrl) ?>"
          class="w-full sm:w-auto flex items-center justify-center gap-2 bg-[var(--primary-color)] text-white font-bold px-6 py-3 rounded-xl text-xs hover:opacity-95 shadow-md hover:shadow-lg transition-all whitespace-nowrap shrink-0">
          <?= renderIcon('calendar', 'w-4 h-4') ?>
          Schedule Inspection
        </a>
      </div>

    </div>
</section>