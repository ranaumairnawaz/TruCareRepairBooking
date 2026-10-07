<?php /** @var array $settings */ ?>
<section id="hero-section" class="relative overflow-hidden bg-gradient-to-b from-slate-50 via-white to-slate-100/50 pt-12 pb-24 sm:pt-16 sm:pb-32">
  <div class="absolute top-0 left-0 -z-10 h-96 w-96 rounded-full bg-primary-light/40 blur-3xl"></div>
  <div class="absolute right-0 top-1/2 -z-10 h-72 w-72 rounded-full bg-slate-200/50 blur-3xl"></div>

  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 gap-12 lg:grid-cols-12 lg:items-center">
      <div class="lg:col-span-4 text-left space-y-6 animate-fade-in">
        <div class="inline-flex items-center gap-1.5 rounded-full bg-primary-light px-3 py-1 text-[11px] font-bold text-[var(--primary-color)] border border-primary-border/20">
          <?= renderIcon('sparkles', 'w-3.5 h-3.5 animate-spin') ?>
          Sleek Industrial Care
        </div>
        <h1 class="text-4xl font-black tracking-tight text-[var(--secondary-color)] sm:text-5xl lg:text-6.5xl font-heading leading-[1.05]">
          <?= e($settings['hero']['title']) ?>
        </h1>
        <p class="text-gray-500 text-sm sm:text-base leading-relaxed max-w-sm font-semibold">
          <?= e($settings['hero']['subtitle']) ?>
        </p>
      </div>

      <div class="lg:col-span-4 relative flex flex-col items-center">
        <div class="absolute -top-10 right-4 hidden lg:block text-slate-400">
          <svg class="w-16 h-16 transform -rotate-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
          </svg>
          <span class="absolute -top-4 left-6 text-[10px] font-mono tracking-widest uppercase">Fast booking</span>
        </div>
        <div class="relative w-full max-w-[280px] sm:max-w-[320px] aspect-[4/5]">
          <div class="absolute inset-0 rounded-3xl bg-slate-100 border border-slate-200/50 -rotate-3 transition-transform hover:rotate-0 duration-500"></div>
          <img src="<?= e($settings['hero']['image']) ?>" alt="Expert Mechanical Specialist" class="absolute inset-0 h-full w-full object-cover rounded-3xl shadow-xl border border-white" referrerpolicy="no-referrer">
          <div class="absolute -top-3 -right-3 bg-white p-2.5 rounded-2xl shadow-lg border border-slate-100 flex items-center gap-2">
            <?= renderIcon('check-circle-2', 'w-4 h-4 text-[var(--primary-color)]') ?>
            <span class="text-[10px] font-bold text-slate-700">ISO-9001 Cert</span>
          </div>
        </div>
        <div class="mt-6 w-full max-w-sm bg-amber-400 text-slate-900 px-6 py-4 rounded-2xl shadow-md grid grid-cols-2 gap-4 text-center border border-amber-300">
          <div class="border-r border-slate-900/10">
            <div class="text-xl font-black tracking-tight font-heading">1.2K+</div>
            <div class="text-[9px] font-bold uppercase tracking-wider text-slate-800">Satisfied Customer</div>
          </div>
          <div>
            <div class="text-xl font-black tracking-tight font-heading">2K+</div>
            <div class="text-[9px] font-bold uppercase tracking-wider text-slate-800">Completed Projects</div>
          </div>
        </div>
      </div>

      <div class="lg:col-span-4">
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-100/80 text-left relative" id="hero-quick-book">
          <div class="flex justify-between items-center mb-3">
            <span class="text-[9px] font-black uppercase tracking-wider bg-primary-light text-[var(--primary-color)] px-2.5 py-0.5 rounded-full border border-primary-border/20">Step 1 of 4: Schedule Mode</span>
            <span class="w-2 h-2 rounded-full bg-[var(--primary-color)] animate-ping"></span>
          </div>
          <h3 class="text-lg font-black tracking-tight text-slate-900 font-heading leading-snug">Request Shop Services</h3>
          <p class="text-[11px] text-slate-400 mt-1 mb-5 leading-normal">Choose an option below to configure immediate emergency dispatch or schedule planned shop maintenance.</p>
          <div id="hero-book-error" class="hidden mb-4 p-2.5 bg-red-50 text-red-700 text-xs rounded-xl border-l-4 border-red-500"></div>
          <form id="hero-book-form" class="space-y-4" action="<?= e(url('booking.php')) ?>" method="get">
            <div class="space-y-2.5">
              <button type="button" data-urgent="1" class="hero-urgency-btn w-full flex items-start gap-3 p-3.5 rounded-xl border text-left transition-all duration-200 cursor-pointer border-slate-200 hover:border-slate-300 hover:bg-slate-50/50">
                <div class="p-2 rounded-lg shrink-0 bg-red-50 text-red-500"><?= renderIcon('alert-triangle', 'w-4 h-4') ?></div>
                <div>
                  <h4 class="text-xs font-bold text-slate-800">🚨 Urgent Emergency</h4>
                  <p class="text-[10px] text-slate-400 mt-0.5 leading-snug">Immediate technician dispatch. Under 3 hours arrival.</p>
                </div>
              </button>
              <button type="button" data-urgent="0" class="hero-urgency-btn w-full flex items-start gap-3 p-3.5 rounded-xl border text-left transition-all duration-200 cursor-pointer border-slate-200 hover:border-slate-300 hover:bg-slate-50/50">
                <div class="p-2 rounded-lg shrink-0 bg-blue-50 text-blue-500"><?= renderIcon('calendar', 'w-4 h-4') ?></div>
                <div>
                  <h4 class="text-xs font-bold text-slate-800">📅 Book Another Time</h4>
                  <p class="text-[10px] text-slate-400 mt-0.5 leading-snug">Select custom operations calendar date &amp; slot.</p>
                </div>
              </button>
            </div>
            <input type="hidden" name="urgent" id="hero-urgent" value="">
            <div id="hero-scheduled-fields" class="hidden space-y-3 pt-2 overflow-hidden border-t border-slate-100">
              <div>
                <label class="block text-[9px] font-black uppercase text-slate-400 tracking-wider mb-1">Preferred Date</label>
                <div class="relative">
                  <?= renderIcon('calendar', 'w-3.5 h-3.5 text-slate-400 absolute left-3 top-2.5') ?>
                  <input type="date" name="date" id="hero-date" min="<?= date('Y-m-d') ?>" class="w-full pl-9 pr-3 py-2 text-xs border border-slate-200 rounded-xl bg-slate-50 focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)] font-semibold text-slate-700 cursor-pointer">
                </div>
              </div>
              <div>
                <label class="block text-[9px] font-black uppercase text-slate-400 tracking-wider mb-1">Operational Slot</label>
                <select name="timeSlot" id="hero-timeslot" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl bg-slate-50 focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)] font-semibold text-slate-700 cursor-pointer">
                  <option value="">-- Choose time range --</option>
                  <option>Morning (8:00 AM - 11:00 AM)</option>
                  <option>Midday (11:00 AM - 2:00 PM)</option>
                  <option>Afternoon (2:00 PM - 5:00 PM)</option>
                  <option>Evening (5:00 PM - 8:00 PM)</option>
                </select>
              </div>
            </div>
            <div class="pt-2">
              <button type="submit" class="w-full bg-[var(--primary-color)] hover:opacity-95 text-white font-extrabold text-xs uppercase tracking-widest py-3.5 px-4 rounded-xl shadow-lg hover:shadow-xl transition-all cursor-pointer text-center flex items-center justify-center gap-1.5">
                <span id="hero-cta-text"><?= e($settings['hero']['ctaText']) ?></span>
                <?= renderIcon('arrow-right', 'w-3.5 h-3.5') ?>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
