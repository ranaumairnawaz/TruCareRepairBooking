(function () {
  'use strict';

  var form = document.getElementById('hero-book-form');
  if (!form) return;

  var urgentInput = document.getElementById('hero-urgent');
  var scheduledFields = document.getElementById('hero-scheduled-fields');
  var errorEl = document.getElementById('hero-book-error');
  var ctaText = document.getElementById('hero-cta-text');
  var defaultCta = ctaText ? ctaText.textContent : 'Proceed to Service Selection';
  var selectedUrgent = null;

  function showError(msg) {
    if (!errorEl) return;
    errorEl.textContent = msg;
    errorEl.classList.remove('hidden');
  }

  function clearError() {
    if (errorEl) errorEl.classList.add('hidden');
  }

  function setUrgentStyle(isUrgent) {
    document.querySelectorAll('.hero-urgency-btn').forEach(function (btn, i) {
      var urgent = btn.getAttribute('data-urgent') === '1';
      btn.className = 'hero-urgency-btn w-full flex items-start gap-3 p-3.5 rounded-xl border text-left transition-all duration-200 cursor-pointer ';
      if ((isUrgent && urgent) || (!isUrgent && !urgent && selectedUrgent === false)) {
        btn.className += urgent
          ? 'border-red-500 bg-red-50/20 ring-2 ring-red-500/15'
          : 'border-[var(--primary-color)] bg-primary-light ring-2 ring-[var(--primary-color)]/15';
      } else {
        btn.className += 'border-slate-200 hover:border-slate-300 hover:bg-slate-50/50';
      }
    });
  }

  document.querySelectorAll('.hero-urgency-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var urgent = btn.getAttribute('data-urgent') === '1';
      selectedUrgent = urgent;
      urgentInput.value = urgent ? '1' : '0';
      clearError();
      setUrgentStyle(urgent);
      if (urgent) {
        scheduledFields.classList.add('hidden');
        if (ctaText) ctaText.textContent = 'Fast-Track Service Selection';
      } else {
        scheduledFields.classList.remove('hidden');
        if (ctaText) ctaText.textContent = defaultCta;
      }
    });
  });

  form.addEventListener('submit', function (e) {
    if (selectedUrgent === null) {
      e.preventDefault();
      showError('Please select a booking option first.');
      return;
    }
    if (selectedUrgent === false) {
      var date = document.getElementById('hero-date').value;
      var slot = document.getElementById('hero-timeslot').value;
      if (!date) {
        e.preventDefault();
        showError('Please select your preferred date.');
        return;
      }
      if (!slot) {
        e.preventDefault();
        showError('Please choose a convenient time slot.');
        return;
      }
    }
  });
})();
