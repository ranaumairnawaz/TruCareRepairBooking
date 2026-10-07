(function () {
  'use strict';

  document.getElementById('watch-intro-btn')?.addEventListener('click', function () {
    alert("🎬 Launching PrecisionMech's interactive facility walk-through and service standards introduction video!");
  });

  var logo = document.getElementById('site-logo');
  if (logo) {
    logo.addEventListener('dblclick', function (e) {
      e.preventDefault();
      window.location.href = (window.BASE_PATH || '') + '/admin/';
    });
  }

  document.getElementById('admin-logout-btn')?.addEventListener('click', async function () {
    localStorage.removeItem('repair_admin_token');
    try {
      await fetch((window.BASE_PATH || '') + '/api/admin/logout', { method: 'POST', cache: 'no-store' });
    } catch (_) {}
    window.location.reload();
  });

  if (localStorage.getItem('repair_admin_token') && !document.getElementById('admin-logout-btn')) {
    // Token exists but nav wasn't server-rendered with admin — optional refresh not required
  }
})();
