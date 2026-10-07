<?php
/** @var array $settings */
$features = $settings['about']['features'] ?? [];
if (empty($features)) {
    $features = [
        'Fast Reliable Service — Real-time technician dispatched with immediate telemetry diagnostics and parts provisioning to resolve machine downtime under 3 hours.',
        'Skill & Trained Specialists — Every engineer undergoes stringent manufacturer verification audits and rigorous testing to guarantee pristine precision-level outcomes.',
        'Emergency Service — Critical system down? Our 24/7 hotline gets a physical machinist at your bay doorstep equipped with diagnostic components immediately.',
    ];
}
?>
<section id="about-section" class="py-20 sm:py-28 bg-white border-y border-slate-50">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mb-16">
      <div class="lg:col-span-7 text-left">
        <span class="text-xs font-bold text-[var(--primary-color)] uppercase tracking-widest bg-primary-light px-3 py-1 rounded-full">Who We Are</span>
        <h2 class="text-3xl font-black tracking-tight text-[var(--secondary-color)] sm:text-4xl lg:text-5xl font-heading mt-3 max-w-xl leading-tight"><?= e($settings['about']['title']) ?></h2>
      </div>
      <div class="lg:col-span-5 text-left pt-2">
        <p class="text-gray-500 font-body leading-relaxed text-sm sm:text-base"><?= e($settings['about']['text']) ?></p>
      </div>
    </div>
    <div class="grid grid-cols-1 gap-12 lg:grid-cols-12 lg:items-center">
      <div class="lg:col-span-5 relative">
        <div class="relative mx-auto max-w-sm lg:max-w-none">
          <div class="absolute inset-4 rounded-2xl border-2 border-slate-100 -z-10 translate-x-4 translate-y-4"></div>
          <img src="<?= e($settings['about']['image']) ?>" alt="Expert mechanical craftsman" class="relative rounded-2xl object-cover shadow-2xl aspect-[3/4] w-full" referrerpolicy="no-referrer">
          <div class="absolute top-10 -left-6 bg-amber-400 p-5 rounded-2xl shadow-xl border border-amber-300 text-left min-w-[140px] transform -rotate-3 transition-transform hover:rotate-0 duration-300">
            <?= renderIcon('hard-hat', 'w-5 h-5 shrink-0 text-slate-900') ?>
            <div class="text-3xl font-black font-heading text-slate-950 mt-1 leading-none">200+</div>
            <div class="text-[10px] font-black text-slate-900 uppercase tracking-wider leading-tight mt-1.5">Expert<br>Engineers</div>
          </div>
        </div>
      </div>
      <div class="lg:col-span-7 space-y-4 text-left">
        <?php foreach ($features as $idx => $feature):
            $parsed = parseFeature($feature);
            $icon = FEATURE_ICONS[$idx % count(FEATURE_ICONS)];
        ?>
        <div class="flex items-start gap-4 p-5 rounded-2xl bg-slate-50 hover:bg-slate-100/50 border border-slate-100 transition-all duration-300">
          <div class="p-3 bg-amber-500 text-white rounded-xl shrink-0 shadow-md">
            <?= renderIcon($icon, 'w-5 h-5') ?>
          </div>
          <div class="space-y-1">
            <h3 class="text-sm font-black text-[var(--secondary-color)] uppercase tracking-wider"><?= e($parsed['title']) ?></h3>
            <?php if ($parsed['description']): ?>
            <p class="text-xs text-gray-500 leading-relaxed"><?= e($parsed['description']) ?></p>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
