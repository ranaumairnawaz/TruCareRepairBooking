<?php /** @var array $settings */ ?>
<section id="contact-section" class="py-16 sm:py-24 bg-white relative">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="text-center space-y-3 mb-12">
      <span class="text-xs font-bold text-[var(--primary-color)] uppercase tracking-wider bg-primary-light px-3 py-1 rounded-full">Reach Out</span>
      <h2 class="text-3xl font-bold tracking-tight text-[var(--secondary-color)] sm:text-4xl font-heading"><?= e($settings['contact']['title']) ?></h2>
      <p class="mx-auto max-w-2xl text-gray-500 text-sm"><?= e($settings['contact']['subtitle']) ?></p>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      <div class="lg:col-span-4 space-y-4">
        <?php
        $cards = [
            ['icon' => 'phone', 'label' => 'Phone Number', 'value' => $settings['contact']['phone'], 'hint' => 'Call us for immediate dispatch'],
            ['icon' => 'mail', 'label' => 'Email Address', 'value' => $settings['contact']['email'], 'hint' => 'General & corporate queries'],
            ['icon' => 'map-pin', 'label' => 'Our Location', 'value' => $settings['contact']['address'], 'hint' => 'Walk-ins welcome for diagnostics'],
            ['icon' => 'clock', 'label' => 'Business Hours', 'value' => $settings['contact']['hours'], 'hint' => 'Emergency support calls 24/7'],
        ];
        foreach ($cards as $card):
        ?>
        <div class="flex items-start gap-4 p-5 rounded-2xl bg-gray-50 border border-gray-100/50 text-left">
          <div class="p-3 bg-primary-light text-[var(--primary-color)] rounded-xl shrink-0"><?= renderIcon($card['icon'], 'w-5 h-5') ?></div>
          <div class="space-y-0.5">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider"><?= e($card['label']) ?></div>
            <div class="text-sm font-bold text-gray-800 break-all"><?= e($card['value']) ?></div>
            <div class="text-[11px] text-gray-500"><?= e($card['hint']) ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="lg:col-span-8 space-y-6">
        <div class="h-full min-h-[320px] rounded-2xl overflow-hidden border border-gray-100 shadow-sm relative bg-gray-50">
          <?php if (!empty($settings['contact']['mapEmbedUrl'])): ?>
          <iframe title="Our Location" src="<?= e($settings['contact']['mapEmbedUrl']) ?>" width="100%" height="100%" style="border:0;min-height:320px" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          <?php else: ?>
          <div class="flex flex-col items-center justify-center h-full min-h-[320px] p-8 text-center text-gray-400">
            <?= renderIcon('map-pin', 'w-12 h-12 text-gray-300 mb-2') ?>
            <p class="text-sm font-bold">No Map Configured</p>
            <p class="text-xs">Add a map embed URL from the Admin panel.</p>
          </div>
          <?php endif; ?>
        </div>
        <?php if (!empty($settings['serviceLocations'])): ?>
        <div class="rounded-2xl border border-gray-100 shadow-sm bg-gray-50 p-6 sm:p-8">
          <div class="flex items-center gap-2 mb-6">
            <div class="p-2.5 bg-primary-light text-[var(--primary-color)] rounded-xl"><?= renderIcon('globe-2', 'w-5 h-5') ?></div>
            <div>
              <h3 class="text-base font-bold text-gray-900">Areas We Serve</h3>
              <p class="text-xs text-gray-500">Countries and cities currently covered by our technicians</p>
            </div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php foreach ($settings['serviceLocations'] as $loc): ?>
            <div class="bg-white rounded-xl border border-gray-100 p-4">
              <div class="text-sm font-bold text-gray-800 mb-2"><?= e($loc['country']) ?></div>
              <div class="flex flex-wrap gap-1.5">
                <?php foreach ($loc['cities'] as $city): ?>
                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-gray-600 bg-gray-50 border border-gray-100 px-2.5 py-1 rounded-full">
                  <?= renderIcon('building-2', 'w-3 h-3 text-[var(--primary-color)]') ?>
                  <?= e($city) ?>
                </span>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
