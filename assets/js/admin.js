(function () {
  'use strict';

  var cfg = window.ADMIN_CONFIG;
  var root = document.getElementById('admin-root');
  if (!cfg || !root) return;

  var api = cfg.apiBase.replace(/\/$/, '');
  var FONT_OPTIONS = ['Space Grotesk', 'Inter', 'Outfit', 'Playfair Display', 'Roboto', 'Open Sans', 'Merriweather', 'JetBrains Mono', 'Syne', 'Cabinet Grotesk'];
  var ICON_OPTIONS = ['Wrench', 'Flame', 'Cpu', 'IceCream', 'Layers', 'Wind', 'Sparkles', 'Shield', 'Laptop', 'Tv', 'Microwave', 'Plug', 'Settings', 'Gauge', 'Hammer', 'HardHat', 'Radio', 'Tv2'];

  var state = {
    authenticated: cfg.isAuthenticated,
    activeTab: 'overview',
    isSaving: false,
    actionSuccess: '',
    actionError: '',
    uploadingField: null,
    uploadError: '',
    bookings: [],
    isLoadingBookings: false,
    showSmtpPass: false,
    modal: null, // 'service' | 'machine'
    editingItem: null,
    editingIndex: null,
    secrets: {
      waGatewayUrl: '', waApiKey: '', waReceiverNumber: '',
      smtpHost: 'smtp.gmail.com', smtpPort: '587', smtpUser: '', smtpPass: '', emailTemplate: ''
    },
    public: JSON.parse(JSON.stringify(cfg.settings))
  };

  function esc(s) {
    var d = document.createElement('div');
    d.textContent = s == null ? '' : String(s);
    return d.innerHTML;
  }

  function icon(n, c) { return '<i data-lucide="' + esc(n) + '" class="' + (c || 'w-4 h-4') + '"></i>'; }

  /* ── Inject admin-specific styles once ─────────────────────────── */
  (function injectAdminStyles() {
    if (document.getElementById('admin-styles')) return;
    var s = document.createElement('style');
    s.id = 'admin-styles';
    s.textContent = [
      /* Layout */
      '#admin-root { font-family: "Inter", "Outfit", system-ui, sans-serif; }',
      '.adm-shell { display:flex; height:100vh; width:100vw; overflow:hidden; background:#f1f5f9; }',
      /* Sidebar */
      '.adm-sidebar { width:260px; min-width:260px; background:#fff; height:100%; display:flex; flex-direction:column; border-right:1px solid #e8ecf0; box-shadow:2px 0 8px rgba(0,0,0,.04); }',
      '.adm-logo { display:flex; align-items:center; gap:12px; padding:0 24px; height:80px; font-weight:700; font-size:1rem; color:var(--primary-color); border-bottom:1px solid #f1f5f9; }',
      '.adm-logo-icon { width:36px; height:36px; border-radius:50%; background:var(--primary-light); display:flex; align-items:center; justify-content:center; flex-shrink:0; }',
      '.adm-logo-name { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:160px; }',
      '.adm-nav { flex:1; padding:16px 12px; overflow-y:auto; display:flex; flex-direction:column; gap:4px; }',
      '.adm-nav-btn { display:flex; align-items:center; gap:12px; padding:11px 16px; border-radius:12px; font-size:.875rem; font-weight:500; color:#64748b; background:none; border:none; width:100%; text-align:left; cursor:pointer; transition:all .15s; }',
      '.adm-nav-btn:hover { background:#f8fafc; color:#1e293b; }',
      '.adm-nav-btn.active { background:var(--primary-color); color:#fff; box-shadow:0 4px 12px rgba(0,0,0,0.1); }',
      '.adm-nav-footer { padding:16px 12px; border-top:1px solid #f1f5f9; display:flex; flex-direction:column; gap:4px; }',
      '.adm-nav-footer-btn { display:flex; align-items:center; gap:12px; padding:11px 16px; border-radius:12px; font-size:.875rem; font-weight:500; color:#64748b; background:none; border:none; width:100%; text-align:left; cursor:pointer; transition:all .15s; }',
      '.adm-nav-footer-btn:hover { background:#f8fafc; color:#1e293b; }',
      '.adm-logout { color:#ef4444 !important; }',
      '.adm-logout:hover { background:#fef2f2 !important; }',
      /* Topbar */
      '.adm-main { flex:1; display:flex; flex-direction:column; min-width:0; height:100%; }',
      '.adm-topbar { height:80px; padding:0 32px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0; background:transparent; }',
      '.adm-topbar-greeting { font-size:.75rem; color:#94a3b8; font-weight:500; }',
      '.adm-topbar-name { font-size:1.5rem; font-weight:700; color:#1e293b; line-height:1.2; }',
      '.adm-topbar-actions { display:flex; align-items:center; gap:20px; }',
      '.adm-icon-btn { background:none; border:none; cursor:pointer; color:#64748b; display:flex; align-items:center; position:relative; padding:4px; border-radius:8px; transition:color .15s; }',
      '.adm-icon-btn:hover { color:#1e293b; }',
      '.adm-notif-dot { position:absolute; top:0; right:0; width:8px; height:8px; background:#ef4444; border:2px solid #f1f5f9; border-radius:50%; }',
      '.adm-avatar-wrap { display:flex; align-items:center; gap:10px; cursor:pointer; padding:6px 10px; border-radius:12px; transition:background .15s; }',
      '.adm-avatar-wrap:hover { background:#f1f5f9; }',
      '.adm-avatar { width:36px; height:36px; border-radius:50%; object-fit:cover; }',
      '.adm-avatar-name { font-size:.875rem; font-weight:600; color:#334155; }',
      /* Content */
      '.adm-content { flex:1; overflow-y:auto; padding:0 32px 40px; }',
      '.adm-content::-webkit-scrollbar { width:6px; }',
      '.adm-content::-webkit-scrollbar-track { background:transparent; }',
      '.adm-content::-webkit-scrollbar-thumb { background:#e2e8f0; border-radius:4px; }',
      /* Banner */
      '.adm-banner { background:linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); border-radius:24px; padding:32px; position:relative; overflow:hidden; display:flex; align-items:center; justify-content:space-between; min-height:160px; }',
      '.adm-banner::after { content:""; position:absolute; right:-40px; top:-40px; width:200px; height:200px; background:rgba(255,255,255,.08); border-radius:50%; }',
      '.adm-banner-text h2 { color:#fff; font-size:1.75rem; font-weight:700; margin:0 0 6px; }',
      '.adm-banner-text p { color:rgba(255,255,255,.8); font-size:.9rem; margin:0; }',
      '.adm-banner-img { width:120px; height:120px; background:#fff; border-radius:50%; padding:6px; box-shadow:0 8px 24px rgba(0,0,0,.15); flex-shrink:0; z-index:1; overflow:hidden; }',
      '.adm-banner-img img { width:100%; height:100%; object-fit:cover; border-radius:50%; }',
      /* Stat cards */
      '.adm-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; }',
      '.adm-stat { background:#fff; border-radius:20px; padding:24px; display:flex; align-items:center; gap:16px; box-shadow:0 1px 3px rgba(0,0,0,.06); border:1px solid #f1f5f9; }',
      '.adm-stat-icon { width:48px; height:48px; border-radius:50%; background:var(--primary-light); color:var(--primary-color); display:flex; align-items:center; justify-content:center; flex-shrink:0; }',
      '.adm-stat-val { font-size:1.4rem; font-weight:700; color:#1e293b; }',
      '.adm-stat-lbl { font-size:.75rem; color:#94a3b8; font-weight:500; margin-top:2px; }',
      /* Chart card */
      '.adm-card { background:#fff; border-radius:20px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,.06); border:1px solid #f1f5f9; }',
      '.adm-card-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }',
      '.adm-card-title { font-weight:700; color:#1e293b; font-size:.95rem; }',
      '.adm-legend { display:flex; align-items:center; gap:16px; }',
      '.adm-legend-item { display:flex; align-items:center; gap:6px; font-size:.75rem; color:#64748b; font-weight:500; }',
      '.adm-legend-dot { width:8px; height:8px; border-radius:50%; }',
      '.adm-monthly-btn { font-size:.75rem; font-weight:500; color:#64748b; background:#f8fafc; padding:6px 14px; border-radius:20px; border:1px solid #e8ecf0; cursor:pointer; }',
      /* Table */
      '.adm-table { width:100%; text-align:left; font-size:.875rem; border-collapse:collapse; }',
      '.adm-table th { padding-bottom:14px; font-size:.75rem; text-transform:uppercase; letter-spacing:.05em; color:#94a3b8; font-weight:600; border-bottom:1px solid #f1f5f9; }',
      '.adm-table td { padding:16px 0; border-bottom:1px solid #f8fafc; }',
      '.adm-table tr:last-child td { border-bottom:none; }',
      '.adm-td-name { font-weight:600; color:#1e293b; }',
      '.adm-td-sub { font-size:.75rem; color:#94a3b8; margin-top:2px; }',
      '.adm-badge-pending { color:#f97316; font-weight:600; }',
      '.adm-badge-confirmed { color:var(--primary-color); font-weight:600; }',
      /* Right panel */
      '.adm-panel { background:#fff; border-radius:20px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,.06); border:1px solid #f1f5f9; }',
      '.adm-panel-title { font-weight:700; color:#1e293b; margin-bottom:16px; font-size:.95rem; }',
      '.adm-system-card { background:#1e293b; border-radius:20px; padding:24px; position:relative; overflow:hidden; margin-bottom:20px; }',
      '.adm-system-card::after { content:""; position:absolute; right:-30px; top:-30px; width:100px; height:100px; background:rgba(255,255,255,.05); border-radius:50%; }',
      '.adm-system-card-code { font-family:monospace; letter-spacing:.15em; font-size:.85rem; color:#e2e8f0; margin:24px 0 16px; }',
      '.adm-system-card-footer { display:flex; justify-content:space-between; align-items:flex-end; }',
      '.adm-system-card-lbl { font-size:.7rem; color:#94a3b8; }',
      '.adm-system-card-name { font-weight:700; color:#fff; font-size:.875rem; margin-top:4px; }',
      '.adm-dots { display:flex; justify-content:center; gap:6px; margin-bottom:20px; }',
      '.adm-dot-active { width:16px; height:4px; background:var(--primary-color); border-radius:2px; }',
      '.adm-dot { width:4px; height:4px; background:#e2e8f0; border-radius:50%; }',
      '.adm-quick-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; text-align:center; }',
      '.adm-quick-item { font-size:.65rem; font-weight:500; color:#94a3b8; }',
      '.adm-quick-icon { width:40px; height:40px; border-radius:12px; background:#f8fafc; border:1px solid #f1f5f9; display:flex; align-items:center; justify-content:center; margin:0 auto 8px; }',
      /* Recommended */
      '.adm-rec-item { display:flex; align-items:center; gap:14px; }',
      '.adm-rec-icon { width:48px; height:48px; border-radius:50%; background:#f8fafc; border:1px solid #f1f5f9; display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0; }',
      '.adm-rec-icon img { width:100%; height:100%; object-fit:cover; }',
      '.adm-rec-name { font-weight:600; color:#1e293b; font-size:.875rem; }',
      '.adm-rec-status { font-size:.7rem; color:#94a3b8; margin-top:2px; }',
      '.adm-rec-time { font-size:.7rem; color:#94a3b8; margin-left:auto; white-space:nowrap; }',
      /* Grid layout */
      '.adm-overview { display:grid; grid-template-columns:1fr 320px; gap:28px; }',
      '.adm-left { display:flex; flex-direction:column; gap:24px; }',
      /* Modal overlay */
      '.adm-modal-overlay { position:fixed; inset:0; background:rgba(15,23,42,.5); backdrop-filter:blur(4px); display:flex; align-items:center; justify-content:center; padding:16px; z-index:9999; animation:stepIn .2s ease-out; }',
      '.adm-modal { background:#fff; border-radius:20px; box-shadow:0 25px 60px rgba(0,0,0,.2); width:100%; max-width:580px; max-height:90vh; display:flex; flex-direction:column; overflow:hidden; }',
      '.adm-modal-head { padding:20px 24px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; background:#fff; z-index:1; }',
      '.adm-modal-head h3 { font-size:1.1rem; font-weight:700; color:#1e293b; }',
      '.adm-modal-body { padding:24px; flex:1; overflow-y:auto; }',
      '.adm-modal-foot { padding:16px 24px; border-top:1px solid #f1f5f9; display:flex; justify-content:flex-end; gap:10px; position:sticky; bottom:0; background:#fff; }',
      /* Toast */
      '.adm-toast { position:fixed; bottom:24px; right:24px; padding:14px 20px; border-radius:14px; box-shadow:0 8px 24px rgba(0,0,0,.15); font-size:.875rem; font-weight:600; z-index:10000; animation:stepIn .2s ease-out; }',
      '.adm-toast-ok { background:var(--primary-color); color:#fff; }',
      '.adm-toast-err { background:#ef4444; color:#fff; }',
      /* Buttons */
      '.adm-btn-primary { background:var(--primary-color); color:#fff; padding:10px 20px; border-radius:12px; font-size:.75rem; font-weight:700; border:none; cursor:pointer; transition:opacity .15s; }',
      '.adm-btn-primary:hover { opacity:.9; }',
      '.adm-btn-secondary { background:#f1f5f9; color:#334155; padding:10px 20px; border-radius:12px; font-size:.75rem; font-weight:700; border:none; cursor:pointer; }',
      '.adm-btn-secondary:hover { background:#e2e8f0; }',
      '.adm-btn-danger { background:#fef2f2; color:#ef4444; padding:10px 20px; border-radius:12px; font-size:.75rem; font-weight:700; border:none; cursor:pointer; }',
      '.adm-btn-icon-close { background:none; border:none; cursor:pointer; color:#94a3b8; padding:4px; border-radius:6px; }',
      '.adm-btn-icon-close:hover { color:#1e293b; background:#f1f5f9; }',
    ].join('\n');
    document.head.appendChild(s);
  }());

  function toast(msg, err) {
    if (err) { state.actionError = msg; state.actionSuccess = ''; }
    else { state.actionSuccess = msg; state.actionError = ''; }
    render();
    setTimeout(function () { state.actionSuccess = ''; state.actionError = ''; render(); }, 4000);
  }

  function authHeaders() {
    return { 'Content-Type': 'application/json' };
  }

  async function apiFetch(path, opts) {
    opts = opts || {};
    opts.cache = 'no-store';
    opts.credentials = 'same-origin';
    return fetch(api + path, opts);
  }

  async function handleLogout() {
    state.authenticated = false;
    localStorage.removeItem('repair_admin_token');
    localStorage.removeItem('repair_admin_username');
    try { await fetch(api + '/admin/logout', { method: 'POST' }); } catch (_) { }
    render();
  }

  async function loadSecrets() {
    try {
      var res = await apiFetch('/settings/secrets');
      if (res.ok) state.secrets = await res.json();
      else if (res.status === 401) handleLogout();
    } catch (_) { }
  }

  async function loadBookings() {
    state.isLoadingBookings = true; render();
    try {
      var res = await apiFetch('/bookings');
      if (res.ok) state.bookings = await res.json();
      else if (res.status === 401) handleLogout();
    } catch (_) { }
    state.isLoadingBookings = false; render();
  }

  async function savePublicSettings() {
    state.isSaving = true; render();
    try {
      var res = await apiFetch('/settings/public', { method: 'POST', headers: authHeaders(), body: JSON.stringify(state.public) });
      var data = await res.json();
      if (res.ok) toast('Branding & content settings saved successfully!');
      else toast(data.error || 'Failed to save.', true);
    } catch (_) { toast('Network error while saving.', true); }
    state.isSaving = false; render();
  }

  async function saveSecretSettings(e) {
    if (e) e.preventDefault();
    state.isSaving = true; render();
    try {
      var res = await apiFetch('/settings/secrets', { method: 'POST', headers: authHeaders(), body: JSON.stringify(state.secrets) });
      if (res.ok) { toast('SMTP & WhatsApp settings saved!'); await loadSecrets(); }
      else { var d = await res.json(); toast(d.error || 'Failed to save secrets.', true); }
    } catch (_) { toast('Network error.', true); }
    state.isSaving = false; render();
  }

  async function uploadImage(file, fieldKey, onUrl) {
    if (!file.type.startsWith('image/')) { state.uploadError = 'Please choose an image file.'; render(); return; }
    if (file.size > 5 * 1024 * 1024) { state.uploadError = 'Image must be under 5MB.'; render(); return; }
    state.uploadError = ''; state.uploadingField = fieldKey; render();
    var fd = new FormData();
    fd.append('image', file);
    try {
      var res = await fetch(api + '/upload', { method: 'POST', body: fd, credentials: 'same-origin' });
      var data = await res.json();
      if (res.ok && data.success) onUrl(data.url);
      else state.uploadError = data.error || 'Upload failed.';
    } catch (err) { state.uploadError = 'Network error during upload. ' + (err.message || ''); }
    state.uploadingField = null; render();
  }

  async function updateBookingStatus(id, status) {
    try {
      var res = await apiFetch('/bookings/' + id + '/status', { method: 'POST', headers: authHeaders(), body: JSON.stringify({ status: status }) });
      if (res.ok) {
        state.bookings = state.bookings.map(function (b) { return b.id === id ? Object.assign({}, b, { status: status }) : b; });
        toast('Booking status updated!');
      }
    } catch (_) { toast('Error updating status.', true); }
    render();
  }

  function input(label, value, onChange, type) {
    type = type || 'text';
    return '<div><label class="block text-xs font-bold text-gray-700 mb-1">' + esc(label) + '</label><input type="' + type + '" value="' + esc(value) + '" data-field="' + esc(onChange) + '" class="admin-input w-full p-2.5 border border-gray-200 rounded-lg text-xs"></div>';
  }

  function textarea(label, value, field, rows) {
    return '<div><label class="block text-xs font-bold text-gray-700 mb-1">' + esc(label) + '</label><textarea data-field="' + esc(field) + '" rows="' + (rows || 3) + '" class="admin-textarea w-full p-2.5 border border-gray-200 rounded-lg text-xs">' + esc(value) + '</textarea></div>';
  }

  function colorField(label, value, field) {
    return '<div><label class="block text-xs font-bold text-gray-700 mb-1">' + esc(label) + '</label><div class="flex gap-2"><input type="color" value="' + esc(value) + '" data-field="' + esc(field) + '" class="admin-color h-10 w-14 rounded cursor-pointer border border-gray-200"><input type="text" value="' + esc(value) + '" data-field="' + esc(field) + '-text" class="flex-1 p-2.5 border border-gray-200 rounded-lg text-xs font-mono"></div></div>';
  }

  function imageField(label, value, fieldKey) {
    var up = state.uploadingField === fieldKey;
    return '<div><label class="block text-xs font-bold text-gray-700 mb-1">' + esc(label) + '</label><div class="flex flex-col sm:flex-row gap-2"><input type="text" value="' + esc(value) + '" data-field="' + esc(fieldKey) + '" class="admin-input flex-1 p-2.5 border border-gray-200 rounded-lg text-xs" placeholder="https://... or upload"><label class="flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-lg text-xs font-bold cursor-pointer border shrink-0 ' + (up ? 'bg-gray-100 text-gray-400' : 'bg-gray-900 text-white border-gray-900 hover:bg-black') + '">' + icon('upload', 'w-3.5 h-3.5') + (up ? ' Uploading...' : ' Upload') + '<input type="file" accept="image/*" class="hidden" data-upload="' + esc(fieldKey) + '"></label></div>' + (value ? '<div class="mt-2 w-24 h-24 rounded-lg border overflow-hidden"><img src="' + esc(value) + '" class="w-full h-full object-cover" alt=""></div>' : '') + '</div>';
  }



  function tabBtn(id, label, ic) {
    var active = state.activeTab === id;
    return '<button type="button" data-tab="' + id + '" class="adm-nav-btn' + (active ? ' active' : '') + '">' + icon(ic, 'w-5 h-5') + ' ' + esc(label) + '</button>';
  }

  function renderOverview() {
    var totalSvc = (state.public.services || []).length;
    var totalMach = (state.public.machines || []).length;
    var heroImg = state.public.hero && state.public.hero.image
      ? state.public.hero.image
      : 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&w=300&q=80';

    var recentRows = state.bookings.slice(0, 5).map(function (b) {
      var badge = b.status === 'pending'
        ? '<span class="adm-badge-pending">Pending</span>'
        : b.status === 'cancelled'
          ? '<span style="color:#ef4444;font-weight:600;">Cancelled</span>'
          : '<span class="adm-badge-confirmed">Confirmed</span>';
      return '<tr>' +
        '<td><div class="adm-td-name">' + esc(b.name) + '</div><div class="adm-td-sub">' + esc(b.machine) + '</div></td>' +
        '<td style="color:#64748b;">' + esc(b.date) + '</td>' +
        '<td>' + badge + '</td>' +
        '<td style="text-align:right;"><button type="button" style="background:none;border:none;cursor:pointer;color:#94a3b8;">' + icon('trash-2', 'w-4 h-4') + '</button></td>' +
        '</tr>';
    }).join('');

    var recItems = (state.public.services || []).slice(0, 4).map(function (s) {
      var imgHtml = s.image
        ? '<img src="' + esc(s.image) + '"  style="width:100%;height:100%;object-fit:cover;">'
        : icon(s.icon || 'wrench', 'w-5 h-5');
      return '<div class="adm-rec-item" style="margin-bottom:16px;">' +
        '<div class="adm-rec-icon">' + imgHtml + '</div>' +
        '<div style="flex:1;"><div class="adm-rec-name">' + esc(s.name) + '</div><div class="adm-rec-status">Available</div></div>' +
        '<div class="adm-rec-time">Now</div>' +
        '</div>';
    }).join('');



    return '<div class="adm-overview">' +
      /* Left column */
      '<div class="adm-left">' +
      '<div class="adm-banner">' +
      '<div class="adm-banner-text"><h2>Welcome!</h2><p>Dashboard Overview</p></div>' +
      '<div class="adm-banner-img"><img src="' + esc(heroImg) + '"></div>' +
      '</div>' +

      '<div class="adm-stats">' +
      '<div class="adm-stat"><div class="adm-stat-icon">' + icon('calendar', 'w-5 h-5') + '</div><div><div class="adm-stat-val">' + state.bookings.length + '</div><div class="adm-stat-lbl">Total Bookings</div></div></div>' +
      '<div class="adm-stat"><div class="adm-stat-icon">' + icon('wrench', 'w-5 h-5') + '</div><div><div class="adm-stat-val">' + totalSvc + '</div><div class="adm-stat-lbl">Total Services</div></div></div>' +
      '<div class="adm-stat"><div class="adm-stat-icon">' + icon('cpu', 'w-5 h-5') + '</div><div><div class="adm-stat-val">' + totalMach + '</div><div class="adm-stat-lbl">Total Machines</div></div></div>' +
      '</div>' +

      '<div class="adm-card">' +
      '<div class="adm-card-header">' +
      '<span class="adm-card-title">Total Details</span>' +
      '<div class="adm-legend">' +
      '<div class="adm-legend-item"><span class="adm-legend-dot" style="background:#f97316;"></span> Total Meal</div>' +
      '<div class="adm-legend-item"><span class="adm-legend-dot" style="background:#3b82f6;"></span> Total Expense</div>' +
      '<button class="adm-monthly-btn">Monthly ▾</button>' +
      '</div>' +
      '</div>' +
      '<div style="height:200px;width:100%;">' +
      '<svg width="100%" height="100%" viewBox="0 0 500 150" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">' +
      '<path d="M0 100 C 50 120, 80 50, 150 70 C 200 80, 220 120, 250 120 C 300 120, 320 80, 380 90 C 420 100, 460 60, 500 70" fill="none" stroke="#f97316" stroke-width="2.5" stroke-linecap="round"/>' +
      '<path d="M0 130 C 50 140, 80 90, 150 110 C 200 120, 220 140, 250 140 C 300 140, 320 120, 380 130 C 420 140, 460 90, 500 100" fill="none" stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round"/>' +
      '</svg>' +
      '</div>' +
      '</div>' +

      '<div class="adm-card">' +
      '<table class="adm-table">' +
      '<thead><tr><th style="width:45%;">Customer</th><th>Date</th><th>Status</th><th style="text-align:right;">Clear</th></tr></thead>' +
      '<tbody>' + (recentRows || '<tr><td colspan="4" style="text-align:center;padding:24px;color:#94a3b8;">No bookings yet.</td></tr>') + '</tbody>' +
      '</table>' +
      '</div>' +
      '</div>' +

      /* Right column */
      '<div style="display:flex;flex-direction:column;gap:24px;">' +
      '<div class="adm-panel">' +
      '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;"><span class="adm-panel-title" style="margin:0;">Quick Actions</span><button style="background:none;border:none;cursor:pointer;color:#94a3b8;">' + icon('more-horizontal', 'w-5 h-5') + '</button></div>' +
      '<div class="adm-system-card">' +
      '<div style="display:flex;justify-content:space-between;align-items:center;position:relative;z-index:1;">' + icon('shield', 'w-6 h-6 text-[var(--primary-light)]') + '<button style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,.6);">' + icon('more-horizontal', 'w-5 h-5') + '</button></div>' +
      '<div class="adm-system-card-code">SYSTEM SECURE</div>' +
      '<div class="adm-system-card-footer" style="position:relative;z-index:1;">' +
      '<div><div class="adm-system-card-lbl">Administrator</div><div class="adm-system-card-name">' + esc(localStorage.getItem('repair_admin_username') || 'Admin') + '</div></div>' +
      '<div style="display:flex;">' +
      '<div style="width:22px;height:22px;border-radius:50%;background:#22c55e;opacity:.9;z-index:1;"></div>' +
      '<div style="width:22px;height:22px;border-radius:50%;background:#86efac;opacity:.9;margin-left:-8px;"></div>' +
      '</div>' +
      '</div>' +
      '</div>' +
      '<div class="adm-dots"><div class="adm-dot-active"></div><div class="adm-dot"></div><div class="adm-dot"></div></div>' +
      '<div class="adm-quick-grid">' +
      '<div class="adm-quick-item"><div class="adm-quick-icon" style="color:#a855f7;">' + icon('send', 'w-4 h-4') + '</div>Send</div>' +
      '<div class="adm-quick-item"><div class="adm-quick-icon" style="color:#22c55e;">' + icon('download', 'w-4 h-4') + '</div>Receive</div>' +
      '<div class="adm-quick-item"><div class="adm-quick-icon" style="color:#eab308;">' + icon('file-text', 'w-4 h-4') + '</div>Invoicing</div>' +
      '<div class="adm-quick-item"><div class="adm-quick-icon" style="color:#3b82f6;">' + icon('grid', 'w-4 h-4') + '</div>More</div>' +
      '</div>' +
      '</div>' +

      '<div class="adm-panel">' +
      '<div class="adm-panel-title">Recommended</div>' +
      (recItems || '<p style="color:#94a3b8;font-size:.8rem;">No services configured yet.</p>') +
      '</div>' +
      '</div>' +
      '</div>';
  }

  function renderBranding() {
    var p = state.public;
    return '<div class="space-y-6 text-left"><div class="border-b border-gray-100 pb-3"><h2 class="text-lg font-bold font-heading">Platform Identity & Styling Theme</h2></div>' +
      '<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">' + input('Platform Name', p.platformName, 'platformName') + input('Navbar Logo', p.logo, 'logo') + '</div>' +
      imageField('Browser Favicon', p.favicon, 'favicon') + textarea('Footer Description', p.footerDescription, 'footerDescription', 3) +
      '<h3 class="text-xs font-black uppercase text-gray-500">Theme Color Palette</h3><div class="grid grid-cols-2 sm:grid-cols-4 gap-4">' +
      colorField('Primary', p.primaryColor, 'primaryColor') + colorField('Secondary', p.secondaryColor, 'secondaryColor') +
      colorField('Text', p.textColor, 'textColor') + colorField('Background', p.backgroundColor, 'backgroundColor') + '</div>' +
      '<div class="grid grid-cols-1 sm:grid-cols-2 gap-4"><div><label class="block text-xs font-bold text-gray-700 mb-1">Heading Font</label><select data-field="headingFont" class="w-full p-2.5 border border-gray-200 rounded-lg text-xs">' +
      FONT_OPTIONS.map(function (f) { return '<option' + (p.headingFont === f ? ' selected' : '') + '>' + esc(f) + '</option>'; }).join('') + '</select></div>' +
      '<div><label class="block text-xs font-bold text-gray-700 mb-1">Body Font</label><select data-field="bodyFont" class="w-full p-2.5 border border-gray-200 rounded-lg text-xs">' +
      FONT_OPTIONS.map(function (f) { return '<option' + (p.bodyFont === f ? ' selected' : '') + '>' + esc(f) + '</option>'; }).join('') + '</select></div></div>' +
      saveBtn() + '</div>';
  }

  function renderContent() {
    var p = state.public;
    return '<div class="space-y-8 text-left"><div class="border-b pb-3"><h2 class="text-lg font-bold font-heading">Core Site Sections</h2></div>' +
      '<div><h3 class="text-sm font-bold mb-3">Hero Section</h3><div class="grid gap-4">' + input('Title', p.hero.title, 'hero.title') + textarea('Subtitle', p.hero.subtitle, 'hero.subtitle', 2) + input('CTA Text', p.hero.ctaText, 'hero.ctaText') + imageField('Hero Image', p.hero.image, 'hero.image') + '</div></div>' +
      '<div><h3 class="text-sm font-bold mb-3">About Section</h3><div class="grid gap-4">' + input('Title', p.about.title, 'about.title') + textarea('Text', p.about.text, 'about.text', 4) + imageField('About Image', p.about.image, 'about.image') +
      '<div><label class="block text-xs font-bold text-gray-700 mb-2">Features (Title — Description)</label><div class="space-y-2" id="about-features">' +
      (p.about.features || []).map(function (f, i) { return '<div class="flex gap-2"><input type="text" value="' + esc(f) + '" data-feature="' + i + '" class="flex-1 p-2 border rounded-lg text-xs"><button type="button" data-del-feature="' + i + '" class="text-red-500 px-2">' + icon('trash-2', 'w-4 h-4') + '</button></div>'; }).join('') +
      '</div><div class="flex gap-2 mt-2"><input type="text" id="new-feature" placeholder="New feature..." class="flex-1 p-2 border rounded-lg text-xs"><button type="button" id="add-feature" class="bg-gray-900 text-white px-3 py-2 rounded-lg text-xs font-bold">Add</button></div></div></div>' +
      '<div><h3 class="text-sm font-bold mb-3">Contact Section</h3><div class="grid sm:grid-cols-2 gap-4">' +
      input('Title', p.contact.title, 'contact.title') + input('Subtitle', p.contact.subtitle, 'contact.subtitle') +
      input('Phone', p.contact.phone, 'contact.phone') + input('Email', p.contact.email, 'contact.email') +
      input('Address', p.contact.address, 'contact.address') + input('Hours', p.contact.hours, 'contact.hours') +
      '<div class="sm:col-span-2">' + input('Map Embed URL', p.contact.mapEmbedUrl, 'contact.mapEmbedUrl') + '</div></div></div>' +
      '<div><h3 class="text-sm font-bold mb-3">Service Areas</h3><div id="locations-list" class="space-y-2">' +
      (p.serviceLocations || []).map(function (loc) {
        return '<div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg text-xs"><div><strong>' + esc(loc.country) + '</strong>: ' + esc(loc.cities.join(', ')) + '</div><button type="button" data-del-loc="' + esc(loc.id) + '" class="text-red-500">' + icon('trash-2', 'w-4 h-4') + '</button></div>';
      }).join('') +
      '</div><div class="grid sm:grid-cols-2 gap-2 mt-3"><input type="text" id="new-loc-country" placeholder="Country" class="p-2 border rounded-lg text-xs"><input type="text" id="new-loc-cities" placeholder="Cities (comma-separated)" class="p-2 border rounded-lg text-xs"></div><button type="button" id="add-location" class="mt-2 bg-gray-900 text-white px-4 py-2 rounded-lg text-xs font-bold">Add Location</button></div>' +
      saveBtn() + '</div>';
  }

  function renderServices() {
    var p = state.public;
    var svcList = (p.services || []).map(function (s, i) {
      return '<div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col gap-3 relative group">' +
        (s.image ? '<img src="' + esc(s.image) + '" class="w-full h-32 object-cover rounded-lg">' : '<div class="w-full h-32 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center text-gray-400">' + icon(s.icon, 'w-8 h-8') + '</div>') +
        '<div><h4 class="font-bold text-sm text-[var(--primary-color)]">' + esc(s.name) + '</h4><p class="text-xs text-gray-500 line-clamp-2 mt-1">' + esc(s.description) + '</p></div>' +
        '<div class="flex gap-2 mt-auto pt-2 border-t border-gray-100">' +
        '<button type="button" data-edit-svc="' + i + '" class="flex-1 text-xs font-bold text-gray-700 bg-gray-100 py-1.5 rounded-lg hover:bg-gray-200">Edit</button>' +
        '<button type="button" data-del-svc="' + esc(s.id) + '" class="flex-1 text-xs font-bold text-red-600 bg-red-50 py-1.5 rounded-lg hover:bg-red-100">Delete</button>' +
        '</div></div>';
    }).join('');

    var machList = (p.machines || []).map(function (m, i) {
      return '<div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-3">' +
        (m.image ? '<img src="' + esc(m.image) + '" class="w-12 h-12 object-cover rounded-lg">' : '<div class="w-12 h-12 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center text-gray-400 shrink-0">' + icon(m.icon, 'w-6 h-6') + '</div>') +
        '<div class="flex-1"><h4 class="font-bold text-sm text-[var(--primary-color)]">' + esc(m.name) + '</h4></div>' +
        '<div class="flex gap-2">' +
        '<button type="button" data-edit-mach="' + i + '" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg">' + icon('edit', 'w-4 h-4') + '</button>' +
        '<button type="button" data-del-mach="' + esc(m.id) + '" class="p-2 text-red-600 hover:bg-red-50 rounded-lg">' + icon('trash-2', 'w-4 h-4') + '</button>' +
        '</div></div>';
    }).join('');

    return '<div class="space-y-8 text-left"><div class="border-b border-gray-100 pb-3"><h2 class="text-lg font-bold font-heading">Services & Machines</h2></div>' +
      '<div><div class="flex items-center justify-between mb-4"><h3 class="text-sm font-bold">Services Dashboard</h3><button type="button" id="new-svc-btn" class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg text-xs font-bold flex items-center gap-1 hover:opacity-90">' + icon('plus', 'w-4 h-4') + ' Add Service</button></div>' +
      '<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">' + (svcList || '<p class="text-xs text-gray-500">No services found.</p>') + '</div></div>' +
      '<div><div class="flex items-center justify-between mb-4"><h3 class="text-sm font-bold">Supported Machines</h3><button type="button" id="new-mach-btn" class="bg-gray-900 text-white px-4 py-2 rounded-lg text-xs font-bold flex items-center gap-1 hover:opacity-90">' + icon('plus', 'w-4 h-4') + ' Add Machine</button></div>' +
      '<div class="grid sm:grid-cols-2 gap-4">' + (machList || '<p class="text-xs text-gray-500">No machines found.</p>') + '</div></div>' +
      saveBtn() + '</div>';
  }

  function renderApi() {
    var s = state.secrets;
    return '<div class="space-y-6 text-left"><h2 class="text-lg font-bold font-heading border-b pb-3">WhatsApp Gateway & SMTP Config</h2>' +
      '<p class="text-xs text-gray-400">WhatsApp alerts go to admin only. Customers receive email only.</p>' +
      '<h3 class="text-sm font-bold">WhatsApp</h3><div class="grid gap-4">' + input('Gateway URL', s.waGatewayUrl, 'secrets.waGatewayUrl') + input('API Key', s.waApiKey, 'secrets.waApiKey') + input('Receiver Number', s.waReceiverNumber, 'secrets.waReceiverNumber') + '</div>' +
      '<h3 class="text-sm font-bold mt-4">Integrations (Google Maps)</h3><div class="grid gap-4">' +
      input('Google Maps API Key', s.googleMapsApiKey, 'secrets.googleMapsApiKey') +
      '</div>' +
      '<h3 class="text-sm font-bold mt-4">SMTP Email</h3><div class="grid sm:grid-cols-2 gap-4">' +
      input('SMTP Host', s.smtpHost, 'secrets.smtpHost') + input('SMTP Port', s.smtpPort, 'secrets.smtpPort') +
      input('SMTP User', s.smtpUser, 'secrets.smtpUser') + input('SMTP Password', s.smtpPass, 'secrets.smtpPass', state.showSmtpPass ? 'text' : 'password') +
      '</div><button type="button" id="toggle-smtp-pass" class="text-xs text-[var(--primary-color)] font-bold">Toggle password visibility</button>' +
      textarea('Email Template (HTML)', s.emailTemplate, 'secrets.emailTemplate', 12) +
      '<button type="button" id="save-secrets" class="bg-gray-900 text-white px-6 py-3 rounded-xl text-xs font-bold cursor-pointer">' + (state.isSaving ? 'Saving...' : 'Save SMTP & WhatsApp Settings') + '</button></div>';
  }

  function renderSeo() {
    var seo = state.public.seo;
    function seoBlock(name, data) {
      return '<div class="p-4 bg-gray-50 rounded-xl space-y-3"><h3 class="text-sm font-bold capitalize">' + esc(name) + ' Page</h3>' +
        input('Title', data.title, 'seo.' + name + '.title') + textarea('Description', data.description, 'seo.' + name + '.description', 2) + imageField('OG Image', data.ogImage, 'seo.' + name + '.ogImage') + '</div>';
    }
    return '<div class="space-y-6 text-left"><h2 class="text-lg font-bold font-heading border-b pb-3">Page SEO Details</h2>' +
      seoBlock('home', seo.home) + seoBlock('about', seo.about) + seoBlock('contact', seo.contact) + seoBlock('booking', seo.booking) + saveBtn() + '</div>';
  }

  function renderBookings() {
    var rows = state.bookings.map(function (b) {
      var st = b.status || 'pending';
      var colors = { pending: 'bg-amber-100 text-amber-800', confirmed: 'bg-green-100 text-green-800', cancelled: 'bg-red-100 text-red-800' };
      return '<tr class="border-b border-gray-50 text-xs"><td class="py-3 pr-2 font-mono">' + esc(b.id.slice(0, 8)) + '…</td><td class="py-3">' + esc(b.name) + '<br><span class="text-gray-400">' + esc(b.email) + '</span></td><td class="py-3">' + esc(b.machine) + '</td><td class="py-3">' + esc(b.date) + '<br>' + esc(b.timeSlot) + '</td><td class="py-3"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold ' + (colors[st] || '') + '">' + esc(st) + '</span></td><td class="py-3"><select data-status-id="' + esc(b.id) + '" class="text-xs border rounded p-1"><option value="pending"' + (st === 'pending' ? ' selected' : '') + '>Pending</option><option value="confirmed"' + (st === 'confirmed' ? ' selected' : '') + '>Confirmed</option><option value="cancelled"' + (st === 'cancelled' ? ' selected' : '') + '>Cancelled</option></select></td></tr>';
    }).join('');
    return '<div class="text-left"><h2 class="text-lg font-bold font-heading border-b pb-3 mb-4">Bookings Manager</h2>' +
      (state.isLoadingBookings ? '<p class="text-xs text-gray-400">Loading...</p>' : '') +
      '<div class="overflow-x-auto"><table class="w-full text-left"><thead><tr class="text-[10px] uppercase text-gray-400 border-b"><th class="pb-2">ID</th><th class="pb-2">Customer</th><th class="pb-2">Service</th><th class="pb-2">When</th><th class="pb-2">Status</th><th class="pb-2">Update</th></tr></thead><tbody>' +
      (rows || '<tr><td colspan="6" class="py-8 text-center text-gray-400">No bookings yet.</td></tr>') + '</tbody></table></div></div>';
  }

  function saveBtn() {
    return '<button type="button" id="save-public" class="mt-6 bg-[var(--primary-color)] text-white px-6 py-3 rounded-xl text-xs font-bold cursor-pointer disabled:opacity-50" ' + (state.isSaving ? 'disabled' : '') + '>' + (state.isSaving ? 'Saving...' : 'Save Settings') + '</button>';
  }

  function renderModal() {
    if (!state.modal) return '';
    var isSvc = state.modal === 'service';
    var item = state.editingItem;
    var title = isSvc ? (state.editingIndex !== null ? 'Edit Service' : 'New Service') : (state.editingIndex !== null ? 'Edit Machine' : 'New Machine');

    var content = '';
    if (isSvc) {
      content = '<div class="grid gap-4 sm:grid-cols-2">' +
        '<div class="sm:col-span-2">' + input('Name', item.name, 'editingItem.name') + '</div>' +
        '<div class="sm:col-span-2">' + textarea('Description', item.description, 'editingItem.description', 2) + '</div>' +
        input('Price', item.price, 'editingItem.price') +
        '<div><label class="block text-xs font-bold text-gray-700 mb-1">Icon</label><select data-field="editingItem.icon" class="w-full p-2.5 border border-gray-200 rounded-lg text-xs">' + ICON_OPTIONS.map(function (i) { return '<option' + (item.icon === i ? ' selected' : '') + '>' + i + '</option>'; }).join('') + '</select></div>' +
        '<div class="sm:col-span-2">' + imageField('Service Image', item.image, 'editingItem.image') + '</div>' +
        '<div class="sm:col-span-2 mt-2 pt-4 border-t border-gray-100"><h4 class="text-xs font-bold text-gray-500 uppercase mb-3">SEO Meta Tags</h4></div>' +
        '<div class="sm:col-span-2">' + input('SEO Title', item.seoTitle, 'editingItem.seoTitle') + '</div>' +
        '<div class="sm:col-span-2">' + input('SEO Keywords', item.seoKeywords, 'editingItem.seoKeywords') + '</div>' +
        '<div class="sm:col-span-2">' + textarea('SEO Description', item.seoDescription, 'editingItem.seoDescription', 2) + '</div>' +
        '</div>';
    } else {
      content = '<div class="grid gap-4">' +
        input('Name', item.name, 'editingItem.name') +
        '<div><label class="block text-xs font-bold text-gray-700 mb-1">Icon</label><select data-field="editingItem.icon" class="w-full p-2.5 border border-gray-200 rounded-lg text-xs">' + ICON_OPTIONS.map(function (i) { return '<option' + (item.icon === i ? ' selected' : '') + '>' + i + '</option>'; }).join('') + '</select></div>' +
        imageField('Machine Image', item.image, 'editingItem.image') +
        '</div>';
    }

    return '<div class="adm-modal-overlay">' +
      '<div class="adm-modal">' +
      '<div class="adm-modal-head"><h3>' + esc(title) + '</h3><button type="button" id="close-modal" class="adm-btn-icon-close">' + icon('x', 'w-5 h-5') + '</button></div>' +
      '<div class="adm-modal-body">' + content + '</div>' +
      '<div class="adm-modal-foot"><button type="button" id="close-modal-btn" class="adm-btn-secondary">Cancel</button><button type="button" id="save-modal-btn" class="adm-btn-primary">Save ' + (isSvc ? 'Service' : 'Machine') + '</button></div>' +
      '</div>' +
      '</div>';
  }

  function renderDashboard() {
    var tabContent = state.activeTab === 'overview'
      ? renderOverview()
      : ({ branding: renderBranding, content: renderContent, services: renderServices, api: renderApi, seo: renderSeo, bookings: renderBookings }[state.activeTab] || renderOverview)();
    var userName = esc(localStorage.getItem('repair_admin_username') || cfg.settings.adminUsername || 'Admin');
    var platformName = esc(state.public.platformName || 'Admin Panel');

    root.innerHTML =
      (state.modal ? renderModal() : '') +
      (state.actionSuccess ? '<div class="adm-toast adm-toast-ok">' + esc(state.actionSuccess) + '</div>' : '') +
      (state.actionError ? '<div class="adm-toast adm-toast-err">' + esc(state.actionError) + '</div>' : '') +
      (state.uploadError ? '<div class="adm-toast adm-toast-err">' + esc(state.uploadError) + '</div>' : '') +

      '<div class="adm-shell">' +

      /* ── SIDEBAR ── */
      '<aside class="adm-sidebar">' +
      '<div class="adm-logo">' +
      '<div class="adm-logo-icon">' + icon('tool', 'w-4 h-4') + '</div>' +
      '<span class="adm-logo-name" title="' + platformName + '">' + platformName + '</span>' +
      '</div>' +

      '<nav class="adm-nav">' +
      tabBtn('overview', 'Dashboard', 'layout-dashboard') +
      tabBtn('bookings', 'Order History', 'file-text') +
      tabBtn('services', 'Services & Machines', 'wrench') +
      tabBtn('content', 'Site Sections', 'edit') +
      tabBtn('api', 'Integrations', 'zap') +
      tabBtn('branding', 'Branding & Theme', 'palette') +
      tabBtn('seo', 'SEO', 'globe') +
      '</nav>' +

      '<div class="adm-nav-footer">' +
      '<button class="adm-nav-footer-btn">' + icon('help-circle', 'w-5 h-5') + ' Get Help</button>' +
      '<button class="adm-nav-footer-btn">' + icon('settings', 'w-5 h-5') + ' Settings</button>' +
      '<button id="admin-logout" class="adm-nav-footer-btn adm-logout">' + icon('log-out', 'w-5 h-5') + ' Log Out</button>' +
      '</div>' +
      '</aside>' +

      /* ── MAIN ── */
      '<div class="adm-main">' +

      '<header class="adm-topbar">' +
      '<div>' +
      '<div class="adm-topbar-greeting">Good Morning!</div>' +
      '<div class="adm-topbar-name">' + userName.split(' ')[0] + '</div>' +
      '</div>' +
      '<div class="adm-topbar-actions">' +
      '<button class="adm-icon-btn" id="refresh-bookings">' + icon('refresh-cw', 'w-5 h-5') + '</button>' +
      '<button class="adm-icon-btn">' + icon('search', 'w-5 h-5') + '</button>' +
      '<button class="adm-icon-btn">' + icon('bell', 'w-5 h-5') + '<span class="adm-notif-dot"></span></button>' +
      '<div class="adm-avatar-wrap">' +
      '<img class="adm-avatar" src="https://ui-avatars.com/api/?name=' + encodeURIComponent(userName) + '&background=000000&color=fff" alt="' + userName + '">' +
      '<span class="adm-avatar-name">' + userName + '</span>' +
      icon('chevron-down', 'w-4 h-4') +
      '</div>' +
      '</div>' +
      '</header>' +

      '<div class="adm-content">' +
      (state.activeTab !== 'overview'
        ? '<div style="background:#fff;border-radius:20px;padding:32px;box-shadow:0 1px 3px rgba(0,0,0,.06);border:1px solid #f1f5f9;">' + tabContent + '</div>'
        : tabContent) +
      '</div>' +

      '</div>' + /* end adm-main */
      '</div>'; /* end adm-shell */

    bindDashboardEvents();
    if (typeof lucide !== 'undefined') lucide.createIcons();
  }

  function setNested(obj, path, val) {
    var parts = path.split('.');
    var cur = obj;
    for (var i = 0; i < parts.length - 1; i++) {
      if (!cur[parts[i]]) cur[parts[i]] = {};
      cur = cur[parts[i]];
    }
    cur[parts[parts.length - 1]] = val;
  }

  function bindDashboardEvents() {
    root.querySelectorAll('[data-tab]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        state.activeTab = btn.getAttribute('data-tab');
        if (state.activeTab === 'bookings') loadBookings();
        else render();
      });
    });

    document.getElementById('admin-logout')?.addEventListener('click', handleLogout);
    document.getElementById('refresh-bookings')?.addEventListener('click', loadBookings);
    document.getElementById('save-public')?.addEventListener('click', savePublicSettings);
    document.getElementById('save-secrets')?.addEventListener('click', saveSecretSettings);
    document.getElementById('toggle-smtp-pass')?.addEventListener('click', function () { state.showSmtpPass = !state.showSmtpPass; render(); });

    root.querySelectorAll('.admin-input, .admin-textarea').forEach(function (el) {
      el.addEventListener('input', function () {
        var f = el.getAttribute('data-field');
        if (f.startsWith('secrets.')) setNested(state, f, el.value);
        else if (f.startsWith('editingItem.')) setNested(state, f, el.value);
        else if (f.includes('.')) setNested(state.public, f, el.value);
        else state.public[f] = el.value;
      });
    });

    root.querySelectorAll('.admin-color').forEach(function (el) {
      el.addEventListener('input', function () {
        state.public[el.getAttribute('data-field')] = el.value;
        var t = root.querySelector('[data-field="' + el.getAttribute('data-field') + '-text"]');
        if (t) t.value = el.value;
      });
    });

    root.querySelectorAll('[data-field$="-text"]').forEach(function (el) {
      el.addEventListener('input', function () {
        var field = el.getAttribute('data-field').replace(/-text$/, '');
        state.public[field] = el.value;
        var c = root.querySelector('[data-field="' + field + '"].admin-color');
        if (c) c.value = el.value;
      });
    });

    root.querySelectorAll('select[data-field]').forEach(function (el) {
      el.addEventListener('change', function () {
        var f = el.getAttribute('data-field');
        if (f.startsWith('secrets.')) setNested(state, f, el.value);
        else if (f.startsWith('editingItem.')) setNested(state, f, el.value);
        else setNested(state.public, f, el.value);
      });
    });

    root.querySelectorAll('[data-upload]').forEach(function (input) {
      input.addEventListener('change', function () {
        var file = input.files[0];
        var key = input.getAttribute('data-upload');
        if (!file) return;
        uploadImage(file, key, function (url) {
          if (key.startsWith('editingItem.')) setNested(state, key, url);
          else if (key.startsWith('seo.')) setNested(state.public, key, url);
          else if (key.includes('.')) setNested(state.public, key, url);
          else state.public[key] = url;
          render();
        });
      });
    });

    document.getElementById('add-feature')?.addEventListener('click', function () {
      var v = document.getElementById('new-feature').value.trim();
      if (v) { state.public.about.features.push(v); render(); }
    });
    root.querySelectorAll('[data-del-feature]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        state.public.about.features.splice(parseInt(btn.getAttribute('data-del-feature'), 10), 1);
        render();
      });
    });
    root.querySelectorAll('[data-feature]').forEach(function (el) {
      el.addEventListener('input', function () {
        state.public.about.features[parseInt(el.getAttribute('data-feature'), 10)] = el.value;
      });
    });

    document.getElementById('add-location')?.addEventListener('click', function () {
      var country = document.getElementById('new-loc-country').value.trim();
      var cities = document.getElementById('new-loc-cities').value.split(',').map(function (c) { return c.trim(); }).filter(Boolean);
      if (country && cities.length) {
        state.public.serviceLocations.push({ id: Date.now().toString(), country: country, cities: cities });
        render();
      }
    });
    root.querySelectorAll('[data-del-loc]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var id = btn.getAttribute('data-del-loc');
        state.public.serviceLocations = state.public.serviceLocations.filter(function (l) { return l.id !== id; });
        render();
      });
    });

    document.getElementById('new-svc-btn')?.addEventListener('click', function () {
      state.modal = 'service';
      state.editingIndex = null;
      state.editingItem = { id: Date.now().toString(), name: '', description: '', icon: 'Wrench', price: 'From $49', image: '', seoTitle: '', seoKeywords: '', seoDescription: '' };
      render();
    });

    root.querySelectorAll('[data-edit-svc]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var idx = parseInt(btn.getAttribute('data-edit-svc'), 10);
        state.modal = 'service';
        state.editingIndex = idx;
        state.editingItem = JSON.parse(JSON.stringify(state.public.services[idx]));
        render();
      });
    });

    root.querySelectorAll('[data-del-svc]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        state.public.services = state.public.services.filter(function (s) { return s.id !== btn.getAttribute('data-del-svc'); });
        render();
      });
    });

    document.getElementById('new-mach-btn')?.addEventListener('click', function () {
      state.modal = 'machine';
      state.editingIndex = null;
      state.editingItem = { id: Date.now().toString(), name: '', icon: 'Wrench', image: '' };
      render();
    });

    root.querySelectorAll('[data-edit-mach]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var idx = parseInt(btn.getAttribute('data-edit-mach'), 10);
        state.modal = 'machine';
        state.editingIndex = idx;
        state.editingItem = JSON.parse(JSON.stringify(state.public.machines[idx]));
        render();
      });
    });

    root.querySelectorAll('[data-del-mach]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        state.public.machines = state.public.machines.filter(function (m) { return m.id !== btn.getAttribute('data-del-mach'); });
        render();
      });
    });

    var closeModals = function () { state.modal = null; state.editingItem = null; render(); };
    document.getElementById('close-modal')?.addEventListener('click', closeModals);
    document.getElementById('close-modal-btn')?.addEventListener('click', closeModals);

    document.getElementById('save-modal-btn')?.addEventListener('click', function () {
      if (!state.editingItem.name) return toast('Name is required', true);
      if (state.modal === 'service') {
        if (!state.public.services) state.public.services = [];
        if (state.editingIndex !== null) state.public.services[state.editingIndex] = state.editingItem;
        else state.public.services.push(state.editingItem);
      } else {
        if (!state.public.machines) state.public.machines = [];
        if (state.editingIndex !== null) state.public.machines[state.editingIndex] = state.editingItem;
        else state.public.machines.push(state.editingItem);
      }
      closeModals();
      savePublicSettings();
    });

    root.querySelectorAll('[data-status-id]').forEach(function (sel) {
      sel.addEventListener('change', function () {
        updateBookingStatus(sel.getAttribute('data-status-id'), sel.value);
      });
    });
  }

  function render() {
    if (state.authenticated) renderDashboard();
    else window.location.href = '../admin.php';
  }

  if (state.authenticated) {
    loadSecrets().then(loadBookings).then(render);
  } else {
    render();
  }
})();
