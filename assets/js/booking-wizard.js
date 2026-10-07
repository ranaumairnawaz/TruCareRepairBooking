(function () {
  'use strict';

  var cfg = window.BOOKING_CONFIG;
  var root = document.getElementById('booking-wizard-root');
  if (!cfg || !root) return;

  var TIME_SLOTS = [
    'Morning (8:00 AM - 11:00 AM)',
    'Midday (11:00 AM - 2:00 PM)',
    'Afternoon (2:00 PM - 5:00 PM)',
    'Evening (5:00 PM - 8:00 PM)'
  ];

  var LOADING_PHRASES = [
    'Locking in shop coordinate pins... 📍',
    'Paging our closest certified service specialist... 🛠️',
    'Formatting electronic repair worksheets... 📋',
    'Sending secure dispatch notification packets... 📡',
    'Polishing heavy tools... We are ready to roll! 🔧'
  ];

  var state = {
    step: 1,
    isUrgent: null,
    date: '',
    timeSlot: '',
    selectedMachine: '',
    selectedCountry: '',
    selectedCity: '',
    address: '',
    name: '',
    email: '',
    phone: '',
    message: '',
    isSubmitting: false,
    submitSuccess: false,
    bookingResponse: null,
    errorMsg: '',
    loadingIndex: 0,
    loadingTimer: null
  };

  function initDefaults() {
    if (cfg.serviceLocations && cfg.serviceLocations.length > 0) {
      var firstLoc = cfg.serviceLocations[0];
      if (!state.selectedCountry) state.selectedCountry = firstLoc.country;
      if (!state.selectedCity && firstLoc.cities && firstLoc.cities.length > 0) {
        state.selectedCity = firstLoc.cities[0];
      }
    }
  }

  function esc(s) {
    var d = document.createElement('div');
    d.textContent = s == null ? '' : String(s);
    return d.innerHTML;
  }

  function icon(name, cls) {
    return '<i data-lucide="' + esc(name) + '" class="' + (cls || 'w-5 h-5') + '"></i>';
  }

  function iconFromPascal(pascal) {
    return pascal.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
  }

  function todayStr() {
    return new Date().toISOString().split('T')[0];
  }

  function applyQuickBook() {
    var q = cfg.quickBook;
    if (!q) return;
    if (q.isUrgent !== undefined) {
      state.isUrgent = q.isUrgent;
      if (q.isUrgent) {
        state.date = 'Same-Day Emergency';
        state.timeSlot = 'ASAP (Emergency Dispatch)';
      } else {
        state.date = q.date || '';
        state.timeSlot = q.timeSlot || '';
      }
      state.step = 2;
    }
  }

  function stepTitle() {
    if (state.submitSuccess) return 'We are on our way! 🛠️';
    if (state.isSubmitting) return 'Dispatching Specialist...';
    return ['', 'Select Appointment Urgency', 'Choose Required Service', 'Select Your Service Area', 'Complete Dispatch Details'][state.step];
  }

  function stepSubtitle() {
    if (state.submitSuccess) return 'Your service window is locked. Notifications have been dispatched.';
    if (state.isSubmitting) return 'Processing your request through our instant-notification server.';
    var subs = ['', 'Tell us how critical your machinery downtime is so we can route technicians.', 'Select which mechanical repair or fabrication program you require.', 'Choose your country and city from our serviced locations, then add your street address.', 'Fill in your facility coordinates, contact profile, and submit.'];
    return subs[state.step];
  }

  function breadcrumbs() {
    if (state.isSubmitting || state.submitSuccess) return '';
    var html = '<div class="flex items-center justify-center gap-1.5 sm:gap-3 mb-8 px-2">';
    for (var s = 1; s <= 4; s++) {
      html += '<div class="flex items-center">';
      html += '<button type="button" data-goto="' + s + '" ' + (s >= state.step ? 'disabled' : '') + ' class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300 ' + (state.step >= s ? 'bg-[var(--primary-color)] text-white scale-110 shadow-md' : 'bg-gray-200 text-gray-500 cursor-not-allowed') + '">' + s + '</button>';
      html += '<span class="ml-2 text-[10px] sm:text-xs font-bold hidden md:inline ' + (state.step === s ? 'text-[var(--primary-color)]' : 'text-gray-400') + '">' + ['', 'Timing', 'Service', 'Location', 'Contact'][s] + '</span></div>';
      if (s < 4) html += '<div class="w-6 sm:w-12 h-0.5 transition-all duration-300 ' + (state.step > s ? 'bg-[var(--primary-color)]' : 'bg-gray-200') + '"></div>';
    }
    return html + '</div>';
  }

  function renderStep1() {
    var html = '<div class="wizard-step space-y-6 animate-step-in">';
    html += '<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">';
    html += '<button type="button" data-urgent="1" class="urgency-card flex flex-col items-start p-6 rounded-2xl border text-left transition-all duration-300 relative cursor-pointer group ' + (state.isUrgent === true ? 'border-red-500 bg-red-50/30 ring-2 ring-red-500/20 shadow-lg scale-[1.02]' : 'border-slate-200 hover:border-red-300 hover:bg-slate-50/50') + '">';
    html += '<div class="absolute top-4 right-4 bg-red-100 text-red-600 text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full">Fast-Track</div>';
    html += '<div class="p-3 rounded-2xl mb-4 ' + (state.isUrgent === true ? 'bg-red-500 text-white' : 'bg-red-50 text-red-500 group-hover:scale-110 duration-200') + '">' + icon('alert-triangle', 'w-6 h-6 animate-pulse') + '</div>';
    html += '<h3 class="text-base font-bold text-slate-900 font-heading">🚨 Critical Urgent Service</h3>';
    html += '<p class="text-xs text-slate-500 mt-2 leading-relaxed">Technician dispatched immediately to your facility. Under 3 hours arrivals.</p></button>';

    html += '<button type="button" data-urgent="0" class="urgency-card flex flex-col items-start p-6 rounded-2xl border text-left transition-all duration-300 relative cursor-pointer group ' + (state.isUrgent === false ? 'border-[var(--primary-color)] bg-primary-light ring-2 ring-[var(--primary-color)]/20 shadow-lg scale-[1.02]' : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50/50') + '">';
    html += '<div class="absolute top-4 right-4 bg-blue-100 text-blue-600 text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full">Operational</div>';
    html += '<div class="p-3 rounded-2xl mb-4 ' + (state.isUrgent === false ? 'bg-[var(--primary-color)] text-white' : 'bg-blue-50 text-blue-500 group-hover:scale-110 duration-200') + '">' + icon('calendar', 'w-6 h-6') + '</div>';
    html += '<h3 class="text-base font-bold text-slate-900 font-heading">📅 Book Another Time</h3>';
    html += '<p class="text-xs text-slate-500 mt-2 leading-relaxed">Schedule routine diagnostics and maintenance. Flexible time blocks.</p></button></div>';

    if (state.isUrgent === false) {
      html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50/50 p-5 rounded-2xl border border-slate-100">';
      html += '<div><label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider mb-2">Preferred Service Date</label>';
      html += '<div class="relative">' + icon('calendar', 'w-4 h-4 text-slate-400 absolute left-3.5 top-3');
      html += '<input type="date" id="bw-date" min="' + todayStr() + '" value="' + esc(state.date) + '" class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)] cursor-pointer"></div></div>';
      html += '<div><label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider mb-2">Operational Time Slot</label><div class="grid grid-cols-2 gap-2">';
      TIME_SLOTS.forEach(function (slot) {
        var sel = state.timeSlot === slot;
        html += '<button type="button" data-slot="' + esc(slot) + '" class="slot-btn flex items-center gap-1.5 px-3 py-2 border rounded-xl text-[10px] sm:text-xs text-left font-semibold ' + (sel ? 'border-[var(--primary-color)] bg-[var(--primary-color)] text-white shadow-md' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50') + '">' + icon('clock', 'w-3 h-3 shrink-0') + '<span class="truncate">' + esc(slot.split(' ')[0]) + '</span></button>';
      });
      html += '</div></div></div>';
    }

    if (state.isUrgent === false) {
      html += '<div class="flex justify-end pt-4"><button type="button" id="bw-next-1" class="flex items-center gap-2 bg-[var(--primary-color)] text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:opacity-90 transition-all cursor-pointer shadow-lg">Configure Service ' + icon('arrow-right', 'w-4 h-4') + '</button></div>';
    }
    return html + '</div>';
  }

  function renderStep2() {
    var html = '<div class="wizard-step space-y-6 animate-step-in"><label class="block text-xs font-black uppercase text-slate-400 tracking-wider mb-4">Select One Service Program</label>';
    html += '<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">';
    (cfg.services || []).forEach(function (svc) {
      var sel = state.selectedMachine === svc.name;
      html += '<button type="button" data-service="' + esc(svc.name) + '" class="service-card flex gap-4 items-center p-4 rounded-2xl border text-left transition-all duration-300 cursor-pointer relative group ' + (sel ? 'border-[var(--primary-color)] bg-primary-light ring-2 ring-[var(--primary-color)]/20 shadow-md scale-[1.01]' : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50/50') + '">';
      if (svc.image) {
        html += '<div style="width:56px;height:56px;min-width:56px;border-radius:12px;overflow:hidden;' + (sel ? 'outline:2px solid var(--primary-color);' : '') + ' flex-shrink:0;transition:all .3s"><img src="' + esc(svc.image) + '" alt="' + esc(svc.name) + '" style="width:100%;height:100%;object-fit:cover;display:block;"></div>';
      } else {
        html += '<div class="p-3 rounded-xl shrink-0 ' + (sel ? 'bg-[var(--primary-color)] text-white scale-105' : 'bg-slate-100 text-slate-500 group-hover:scale-105') + ' transition-transform duration-300">' + icon(iconFromPascal(svc.icon || 'wrench'), 'w-5 h-5') + '</div>';
      }
      html += '<div class="flex-grow min-w-0"><h4 class="text-xs sm:text-sm font-bold text-slate-800 truncate">' + esc(svc.name) + '</h4>';
      html += '<p class="text-[11px] text-slate-400 mt-1 leading-normal line-clamp-2">' + esc(svc.description) + '</p></div>';
      if (sel) html += '<div class="absolute top-2 right-2 w-4 h-4 rounded-full bg-[var(--primary-color)] text-white flex items-center justify-center">' + icon('check', 'w-2.5 h-2.5 stroke-[3]') + '</div>';
      html += '</button>';
    });
    html += '</div><div class="flex justify-between pt-6 border-t border-slate-100">';
    html += '<button type="button" data-back class="flex items-center gap-1.5 border border-slate-300 text-slate-700 px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-50 cursor-pointer">' + icon('arrow-left', 'w-4 h-4') + ' Back</button>';
    html += '<button type="button" data-next class="flex items-center gap-1.5 bg-[var(--primary-color)] text-white px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:opacity-90 cursor-pointer shadow-md">Define Location ' + icon('arrow-right', 'w-4 h-4') + '</button></div></div>';
    return html;
  }

  function renderStep3() {
    var html = '<div class="wizard-step space-y-6 animate-step-in"><div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
    html += '<div><label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider mb-1.5">Country</label><div class="relative">' + icon('globe-2', 'w-4 h-4 text-slate-400 absolute left-3.5 top-3.5 pointer-events-none');
    html += '<select id="bw-country" class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)] bg-slate-50 appearance-none cursor-pointer"><option value="">Select a country...</option>';
    (cfg.serviceLocations || []).forEach(function (loc) {
      html += '<option value="' + esc(loc.country) + '"' + (state.selectedCountry === loc.country ? ' selected' : '') + '>' + esc(loc.country) + '</option>';
    });
    html += '</select></div></div>';
    html += '<div><label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider mb-1.5">City</label><div class="relative">' + icon('building-2', 'w-4 h-4 text-slate-400 absolute left-3.5 top-3.5 pointer-events-none');
    html += '<select id="bw-city" class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)] bg-slate-50 appearance-none cursor-pointer"' + (!state.selectedCountry ? ' disabled' : '') + '><option value="">' + (state.selectedCountry ? 'Select a city...' : 'Select a country first') + '</option>';
    var loc = (cfg.serviceLocations || []).find(function (l) { return l.country === state.selectedCountry; });
    if (loc) loc.cities.forEach(function (city) {
      html += '<option value="' + esc(city) + '"' + (state.selectedCity === city ? ' selected' : '') + '>' + esc(city) + '</option>';
    });
    html += '</select></div></div></div>';
    html += '<div><label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider mb-1.5">Shop or Facility Street Address</label><div class="relative">' + icon('map-pin', 'w-4 h-4 text-slate-400 absolute left-3.5 top-3.5');
    html += '<input type="text" id="bw-address" value="' + esc(state.address) + '" placeholder="e.g. 750 Industrial Parkway, Suite 4" class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)] bg-slate-50"></div></div>';
    html += '<div class="flex justify-between pt-6 border-t border-slate-100"><button type="button" data-back class="flex items-center gap-1.5 border border-slate-300 text-slate-700 px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-50 cursor-pointer">' + icon('arrow-left', 'w-4 h-4') + ' Back</button>';
    html += '<button type="button" data-next class="flex items-center gap-1.5 bg-[var(--primary-color)] text-white px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:opacity-90 cursor-pointer shadow-md">Contact Details ' + icon('arrow-right', 'w-4 h-4') + '</button></div></div>';
    return html;
  }

  function renderStep4() {
    return '<div class="wizard-step space-y-6 animate-step-in"><div class="grid grid-cols-1 md:grid-cols-2 gap-4">' +
      field('Your Full Name', 'user', 'bw-name', state.name, 'text', 'e.g. Richard Feynman') +
      field('Your Email (Receipt & Status Reports)', 'mail', 'bw-email', state.email, 'email', 'e.g. feynman@caltech.edu') +
      '</div>' +
      field('WhatsApp Phone Number', 'phone', 'bw-phone', state.phone, 'tel', 'e.g. +14155552671', 'Include country code for WhatsApp status updates.') +
      '<div><label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider mb-1.5">Dramatic backstory or extra notes (Optional)</label><div class="relative">' + icon('message-square', 'w-4 h-4 text-slate-400 absolute left-3.5 top-3') +
      '<textarea id="bw-message" rows="3" placeholder="Optional notes..." class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)] bg-slate-50">' + esc(state.message) + '</textarea></div></div>' +
      '<div class="flex justify-between pt-6 border-t border-slate-100"><button type="button" data-back class="flex items-center gap-1.5 border border-slate-300 text-slate-700 px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-50 cursor-pointer">' + icon('arrow-left', 'w-4 h-4') + ' Back</button>' +
      '<button type="button" id="bw-submit" class="flex items-center gap-1.5 bg-[var(--primary-color)] text-white px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:opacity-90 cursor-pointer shadow-md">Confirm Booking 🚀</button></div></div>';
  }

  function field(label, ic, id, val, type, ph, hint) {
    var h = '<div><label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider mb-1.5">' + esc(label) + '</label><div class="relative">' + icon(ic, 'w-4 h-4 text-slate-400 absolute left-3.5 top-3.5');
    h += '<input type="' + type + '" id="' + id + '" value="' + esc(val) + '" placeholder="' + esc(ph) + '" class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)] bg-slate-50"></div>';
    if (hint) h += '<p class="text-[10px] text-slate-400 mt-1 italic font-medium">' + esc(hint) + '</p>';
    return h + '</div>';
  }

  function renderLoading() {
    return '<div class="flex flex-col items-center justify-center py-12 text-center animate-step-in"><div class="relative mb-6"><div class="w-16 h-16 rounded-full border-4 border-slate-100 border-t-[var(--primary-color)] animate-spin"></div>' + icon('wrench', 'w-7 h-7 text-[var(--primary-color)] absolute top-4 left-4 animate-bounce') + '</div><p class="text-slate-700 font-bold text-base max-w-md h-12">' + esc(LOADING_PHRASES[state.loadingIndex]) + '</p><p class="text-slate-400 text-[10px] mt-4 font-mono">[COMMISSIONING_SPECIALIST_ROUTING]</p></div>';
  }

  function renderSuccess() {
    return '<div class="text-center py-6 space-y-6 animate-step-in"><div class="inline-flex p-4 bg-green-50 rounded-full border-4 border-green-100 text-green-500 mb-2">' + icon('check-circle', 'w-16 h-16 animate-bounce') + '</div>' +
      '<div class="space-y-1"><h3 class="text-2xl font-black text-slate-900 font-heading">Specialist Dispatched Successfully!</h3>' +
      '<p class="text-slate-400 text-xs sm:text-sm font-medium">Repair Reference ID: <span class="font-mono text-xs font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">' + esc(state.bookingResponse.bookingId) + '</span></p></div>' +
      '<div class="max-w-md mx-auto bg-primary-light/40 border border-primary-border/40 rounded-2xl p-5 text-left shadow-sm"><h4 class="text-xs font-bold text-slate-900 flex items-center gap-2 mb-3 font-heading uppercase tracking-wide">' + icon('shield', 'w-4 h-4 text-[var(--primary-color)]') + ' Pre-Arrival Preparations Checklist</h4><ul class="text-xs text-slate-600 space-y-2.5">' +
      '<li class="flex gap-2"><span class="font-bold text-[var(--primary-color)]">1.</span><span>Isolate mechanical drives if active warning signs persist on your <strong>' + esc(state.selectedMachine) + '</strong>.</span></li>' +
      '<li class="flex gap-2"><span class="font-bold text-[var(--primary-color)]">2.</span><span>Ensure the facility space has a 3-foot clearance for diagnosis tools.</span></li>' +
      '<li class="flex gap-2"><span class="font-bold text-[var(--primary-color)]">3.</span><span>Provide the technician with on-site access authorizations on arrival.</span></li></ul></div>' +
      '<div class="pt-4"><button type="button" id="bw-reset" class="bg-slate-800 text-white font-bold px-6 py-3 rounded-xl text-xs font-black uppercase tracking-wider hover:bg-slate-950 cursor-pointer">Register Another Booking</button></div></div>';
  }

  function render() {
    var body = '';
    if (state.isSubmitting && !state.submitSuccess) body = renderLoading();
    else if (state.submitSuccess) body = renderSuccess();
    else if (state.step === 1) body = renderStep1();
    else if (state.step === 2) body = renderStep2();
    else if (state.step === 3) body = renderStep3();
    else body = renderStep4();

    var err = state.errorMsg ? '<div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl flex items-start gap-3 text-red-700 text-xs sm:text-sm">' + icon('alert-triangle', 'w-5 h-5 shrink-0 text-red-500 mt-0.5') + '<div>' + esc(state.errorMsg) + '</div></div>' : '';

    root.innerHTML =
      '<div class="text-center mb-8"><span class="inline-flex items-center gap-1 bg-primary-light px-3.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider text-[var(--primary-color)] border border-primary-border/20">' + icon('activity', 'w-3.5 h-3.5 text-[var(--primary-color)] animate-pulse') + ' Heavy Machinery Dispatch Engine</span>' +
      '<h2 class="text-2xl sm:text-3.5xl font-black tracking-tight text-[var(--secondary-color)] mt-2 font-heading leading-tight">' + esc(stepTitle()) + '</h2>' +
      '<p class="text-slate-400 text-xs sm:text-sm mt-1 max-w-md mx-auto">' + esc(stepSubtitle()) + '</p></div>' +
      err + breadcrumbs() + body;

    if (typeof lucide !== 'undefined') lucide.createIcons();
    bindEvents();
  }

  function validateStep() {
    state.errorMsg = '';
    if (state.step === 1) {
      if (state.isUrgent === null) { state.errorMsg = 'Please select whether this repair request is Urgent or Scheduled.'; return false; }
      if (state.isUrgent === false) {
        if (!state.date) { state.errorMsg = 'Please select an appointment date.'; return false; }
        if (!state.timeSlot) { state.errorMsg = 'Please select a convenient time slot.'; return false; }
      }
    } else if (state.step === 2) {
      if (!state.selectedMachine) { state.errorMsg = 'Please select which service program is required.'; return false; }
    } else if (state.step === 3) {
      if (!state.selectedCountry) { state.errorMsg = 'Please select the country where your facility is located.'; return false; }
      if (!state.selectedCity) { state.errorMsg = 'Please select your city from our serviced locations.'; return false; }
      if (!state.address.trim()) { state.errorMsg = 'Please enter your facility or shop street address.'; return false; }
    }
    return true;
  }

  function bindEvents() {
    root.querySelectorAll('[data-goto]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var s = parseInt(btn.getAttribute('data-goto'), 10);
        if (s < state.step) { state.step = s; render(); }
      });
    });

    root.querySelectorAll('.urgency-card').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var urgent = btn.getAttribute('data-urgent') === '1';
        state.isUrgent = urgent;
        state.errorMsg = '';
        if (urgent) {
          state.date = 'Same-Day Emergency';
          state.timeSlot = 'ASAP (Emergency Dispatch)';
          state.step = 2;
        } else {
          state.date = '';
          state.timeSlot = '';
        }
        render();
      });
    });

    var dateEl = document.getElementById('bw-date');
    if (dateEl) dateEl.addEventListener('change', function () { state.date = dateEl.value; state.errorMsg = ''; });

    root.querySelectorAll('.slot-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        state.timeSlot = btn.getAttribute('data-slot');
        state.errorMsg = '';
        render();
      });
    });

    document.getElementById('bw-next-1')?.addEventListener('click', function () {
      if (validateStep()) { state.step++; render(); }
      else render();
    });

    root.querySelectorAll('.service-card').forEach(function (btn) {
      btn.addEventListener('click', function () {
        state.selectedMachine = btn.getAttribute('data-service');
        state.errorMsg = '';
        render();
      });
    });

    var countryEl = document.getElementById('bw-country');
    if (countryEl) countryEl.addEventListener('change', function () {
      state.selectedCountry = countryEl.value;
      state.selectedCity = '';
      state.errorMsg = '';
      var loc = (cfg.serviceLocations || []).find(function (l) { return l.country === state.selectedCountry; });
      if (loc && loc.cities && loc.cities.length > 0) { state.selectedCity = loc.cities[0]; }
      render();
    });

    var cityEl = document.getElementById('bw-city');
    if (cityEl) cityEl.addEventListener('change', function () { state.selectedCity = cityEl.value; state.errorMsg = ''; });

    var addrEl = document.getElementById('bw-address');
    if (addrEl) {
      addrEl.addEventListener('input', function () { state.address = addrEl.value; state.errorMsg = ''; });
      if (window.google && window.google.maps && window.google.maps.places && !addrEl.dataset.gmapInit) {
        addrEl.dataset.gmapInit = '1';
        var cityRestriction = state.selectedCity || 'Dubai';
        var autocomplete = new window.google.maps.places.Autocomplete(addrEl, {
          types: ['address'],
          componentRestrictions: { country: 'ae' }
        });
        if (window.google.maps.places.Autocomplete.prototype.setBounds) {
          var geocoder = new window.google.maps.Geocoder();
          geocoder.geocode({ address: cityRestriction + ', UAE' }, function (results, status) {
            if (status === 'OK' && results[0]) { autocomplete.setBounds(results[0].geometry.viewport); }
          });
        }
        autocomplete.addListener('place_changed', function () {
          var place = autocomplete.getPlace();
          if (place && place.formatted_address) {
            state.address = place.formatted_address;
            addrEl.value = place.formatted_address;
          } else if (place && place.name) {
            state.address = place.name;
            addrEl.value = place.name;
          }
        });
      }
    }

    root.querySelectorAll('[data-back]').forEach(function (btn) {
      btn.addEventListener('click', function () { state.errorMsg = ''; state.step--; render(); });
    });

    root.querySelectorAll('[data-next]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        if (state.step === 3) {
          var a = document.getElementById('bw-address');
          if (a) state.address = a.value;
        }
        if (validateStep()) { state.step++; render(); }
        else render();
      });
    });

    document.getElementById('bw-submit')?.addEventListener('click', submitBooking);
    document.getElementById('bw-reset')?.addEventListener('click', resetWizard);
  }

  async function submitBooking() {
    state.name = document.getElementById('bw-name')?.value || '';
    state.email = document.getElementById('bw-email')?.value || '';
    state.phone = document.getElementById('bw-phone')?.value || '';
    state.message = document.getElementById('bw-message')?.value || '';

    if (!state.name.trim()) { state.errorMsg = 'Please provide your name.'; render(); return; }
    if (!state.email.trim() || state.email.indexOf('@') === -1) { state.errorMsg = 'Please provide a valid email address.'; render(); return; }
    if (!state.phone.trim()) { state.errorMsg = 'Please provide your phone number.'; render(); return; }

    state.isSubmitting = true;
    state.errorMsg = '';
    state.loadingIndex = 0;
    render();

    state.loadingTimer = setInterval(function () {
      if (state.loadingIndex < LOADING_PHRASES.length - 1) {
        state.loadingIndex++;
        render();
      }
    }, 1200);

    var compounds = 'Urgency Mode: ' + (state.isUrgent ? '🔴 IMMEDIATE CRITICAL EMERGENCY DISPATCH' : '🟢 STANDARD SCHEDULED APPOINTMENT') + '\n' +
      (state.message ? 'Backstory & Extra Notes: ' + state.message : 'No further description details provided.');

    try {
      var res = await fetch(cfg.apiUrl, {
        method: 'POST',
        cache: 'no-store',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': cfg.csrfToken },
        body: JSON.stringify({
          csrfToken: cfg.csrfToken,
          name: state.name,
          email: state.email,
          phone: state.phone,
          machine: state.selectedMachine,
          symptom: state.isUrgent ? '🔴 Critical Urgent Service' : '📅 Scheduled Appointment',
          country: state.selectedCountry,
          city: state.selectedCity,
          address: state.address,
          date: state.isUrgent ? 'Same-Day Emergency' : state.date,
          timeSlot: state.isUrgent ? 'ASAP (Emergency Dispatch)' : state.timeSlot,
          message: compounds.trim()
        })
      });
      var data = await res.json();
      clearInterval(state.loadingTimer);
      if (res.ok && data.success) {
        state.bookingResponse = data;
        state.submitSuccess = true;
        state.isSubmitting = false;
      } else {
        state.errorMsg = data.error || 'Failed to submit booking. Please try again.';
        state.isSubmitting = false;
      }
    } catch (e) {
      clearInterval(state.loadingTimer);
      state.errorMsg = 'Connection error. Could not contact the booking server.';
      state.isSubmitting = false;
    }
    render();
  }

  function resetWizard() {
    state.step = 1;
    state.isUrgent = null;
    state.date = '';
    state.timeSlot = '';
    state.selectedMachine = '';
    state.selectedCountry = '';
    state.selectedCity = '';
    state.address = '';
    state.name = '';
    state.email = '';
    state.phone = '';
    state.message = '';
    state.submitSuccess = false;
    state.bookingResponse = null;
    state.errorMsg = '';
    render();
  }

  applyQuickBook();
  initDefaults();
  render();
})();
