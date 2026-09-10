<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'SKM Engineering') – SKM Engineering</title>
<style>
* { box-sizing:border-box; margin:0; padding:0; }
body { font-family:'Segoe UI',Arial,sans-serif; background:#f1f5f9; color:#1e293b; min-height:100vh; }

/* Sidebar */
.sidebar { position:fixed; top:0; left:0; width:220px; height:100vh; background:#1e1e2e; color:#fff; display:flex; flex-direction:column; z-index:100; }
.sidebar-brand { padding:20px 20px 16px; border-bottom:1px solid rgba(255,255,255,0.08); }
.sidebar-logo { width:44px; height:44px; border-radius:10px; overflow:hidden; background:#fff; margin-bottom:10px; }
.sidebar-logo img { width:44px; height:44px; object-fit:cover; }
.brand-name { font-size:1rem; font-weight:800; color:#fff; letter-spacing:0.5px; line-height:1.3; }
.brand-sub  { font-size:0.72rem; color:rgba(255,255,255,0.45); margin-top:2px; }
nav { padding:12px 0; flex:1; overflow-y:auto; }
.nav-section { padding:8px 16px 4px; font-size:0.68rem; font-weight:700; color:rgba(255,255,255,0.3); text-transform:uppercase; letter-spacing:1px; }
.nav-link { display:flex; align-items:center; gap:10px; padding:10px 16px; color:rgba(255,255,255,0.65); text-decoration:none; font-size:0.88rem; font-weight:500; transition:all 0.15s; border-left:3px solid transparent; }
.nav-link:hover { background:rgba(255,255,255,0.07); color:#fff; }
.nav-link.active { background:rgba(123,19,19,0.3); color:#fff; border-left-color:#7b1313; font-weight:600; }
.nav-link .icon { font-size:1rem; width:20px; text-align:center; }
.sidebar-footer { padding:14px 16px; border-top:1px solid rgba(255,255,255,0.08); }
.sidebar-footer a { display:flex; align-items:center; gap:8px; color:rgba(255,255,255,0.5); text-decoration:none; font-size:0.82rem; }
.sidebar-footer a:hover { color:#fff; }

/* Main */
.main { margin-left:220px; min-height:100vh; }
.topbar { background:#fff; border-bottom:1px solid #e2e8f0; padding:0 28px; height:56px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:50; }
.topbar-title { font-size:1.1rem; font-weight:700; color:#1e293b; }
.topbar-user { display:flex; align-items:center; gap:10px; font-size:0.88rem; color:#64748b; }
.user-avatar { width:32px; height:32px; background:#7b1313; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:0.8rem; }
.content { padding:28px; }

/* Buttons */
.btn { display:inline-flex; align-items:center; gap:6px; padding:9px 18px; border-radius:6px; font-size:0.88rem; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:all 0.15s; }
.btn-primary { background:#7b1313; color:#fff; }
.btn-primary:hover { background:#9b1a1a; }
.btn-secondary { background:#f1f5f9; color:#1e293b; border:1px solid #e2e8f0; }
.btn-secondary:hover { background:#e2e8f0; }
.btn-sm { padding:5px 12px; font-size:0.78rem; }
.btn-view   { background:#1d4ed8; color:#fff; }
.btn-view:hover { background:#1e40af; }
.btn-edit   { background:#d97706; color:#fff; }
.btn-edit:hover { background:#b45309; }
.btn-del    { background:#dc2626; color:#fff; }
.btn-del:hover  { background:#b91c1c; }
.btn-print  { background:#059669; color:#fff; }
.btn-print:hover { background:#047857; }
.btn-convert { background:#7c3aed; color:#fff; }
.btn-convert:hover { background:#6d28d9; }

/* Cards */
.card { background:#fff; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,0.07); overflow:hidden; margin-bottom:24px; }
.card-header { padding:16px 24px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; }
.card-header h2 { font-size:1rem; font-weight:700; color:#1e293b; }
.form-card { background:#fff; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,0.07); padding:24px 28px; margin-bottom:20px; }
.form-card h3 { font-size:0.95rem; font-weight:700; color:#7b1313; margin-bottom:16px; text-transform:uppercase; letter-spacing:0.5px; border-bottom:2px solid #f1f5f9; padding-bottom:8px; }

/* Forms */
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; }
.form-row.three { grid-template-columns:1fr 1fr 1fr; }
.form-group { display:flex; flex-direction:column; gap:5px; }
.form-group.full { grid-column:1/-1; }
label { font-size:0.78rem; font-weight:600; color:#475569; text-transform:uppercase; letter-spacing:0.4px; }
input[type=text],input[type=date],input[type=number],input[type=password],input[type=email],select,textarea { padding:9px 12px; border:1px solid #e2e8f0; border-radius:6px; font-size:0.92rem; color:#1e293b; background:#fff; transition:border 0.15s; width:100%; font-family:inherit; }
input:focus,select:focus,textarea:focus { outline:none; border-color:#7b1313; box-shadow:0 0 0 3px rgba(123,19,19,0.08); }
textarea { resize:vertical; min-height:80px; }

/* Table */
.badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; }
table.data-table { width:100%; border-collapse:collapse; }
table.data-table thead { background:#7b1313; color:#fff; }
table.data-table thead th { padding:12px 16px; text-align:left; font-size:0.8rem; font-weight:600; }
table.data-table tbody tr { border-bottom:1px solid #f1f5f9; transition:background 0.1s; }
table.data-table tbody tr:hover { background:#f8fafc; }
table.data-table tbody td { padding:12px 16px; font-size:0.88rem; vertical-align:middle; }

/* Stats */
.stat-cards { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:28px; }
.stat-card { background:#fff; border-radius:12px; padding:20px; box-shadow:0 1px 4px rgba(0,0,0,0.07); border-left:4px solid; }
.stat-card .stat-val { font-size:1.8rem; font-weight:800; margin-bottom:4px; }
.stat-card .stat-lbl { font-size:0.82rem; color:#64748b; font-weight:500; }

/* Alerts */
.alert { padding:12px 18px; border-radius:8px; margin-bottom:20px; font-weight:500; font-size:0.9rem; }
.alert-success { background:#f0fdf4; color:#15803d; border:1px solid #86efac; }
.alert-error   { background:#fef2f2; color:#b91c1c; border:1px solid #fca5a5; }

/* Page header */
.page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; }
.page-header h1 { font-size:1.4rem; font-weight:700; }

/* Actions */
.actions { display:flex; gap:5px; flex-wrap:wrap; }

/* Toggle */
.toggle-row { display:flex; align-items:center; gap:12px; padding:12px 14px; background:#f8fafc; border-radius:8px; margin-bottom:14px; border:1px solid #e2e8f0; }
.switch { position:relative; display:inline-block; width:42px; height:22px; flex-shrink:0; }
.switch input { opacity:0; width:0; height:0; }
.slider { position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background:#cbd5e1; border-radius:22px; transition:0.3s; }
.slider:before { position:absolute; content:""; height:16px; width:16px; left:3px; bottom:3px; background:#fff; border-radius:50%; transition:0.3s; }
input:checked + .slider { background:#7b1313; }
input:checked + .slider:before { transform:translateX(20px); }
.toggle-label { font-size:0.9rem; font-weight:600; color:#1e293b; }
.toggle-sub { font-size:0.78rem; color:#64748b; }

/* RTE */
.rte-wrap { border:1px solid #e2e8f0; border-radius:6px; overflow:hidden; background:#fff; }
.rte-wrap:focus-within { border-color:#7b1313; box-shadow:0 0 0 3px rgba(123,19,19,0.08); }
.rte-toolbar { display:flex; gap:2px; padding:5px 6px; background:#f8fafc; border-bottom:1px solid #e2e8f0; flex-wrap:wrap; }
.rte-btn { background:none; border:1px solid transparent; border-radius:4px; padding:3px 8px; cursor:pointer; font-size:0.8rem; color:#475569; font-family:inherit; line-height:1.5; white-space:nowrap; }
.rte-btn:hover { background:#e2e8f0; }
.rte-btn.rte-active { background:#7b1313; color:#fff; }
.rte-sep { width:1px; height:18px; background:#d1d5db; margin:0 3px; }
.rte-editor { min-height:80px; padding:9px 11px; font-size:0.9rem; color:#1e293b; line-height:1.7; outline:none; font-family:'Segoe UI',Arial,sans-serif; }
.rte-editor:empty::before { content:attr(data-placeholder); color:#94a3b8; pointer-events:none; display:block; }
.rte-editor ul { padding-left:20px; margin:3px 0; list-style-type:disc; }
.rte-editor ol { padding-left:20px; margin:3px 0; list-style-type:decimal; }
.rte-hidden { display:none; }

/* Items table */
.items-table { width:100%; border-collapse:collapse; }
.items-table th { background:#7b1313; color:#fff; padding:9px 10px; text-align:left; font-size:0.78rem; font-weight:600; }
.items-table td { padding:6px 5px; border-bottom:1px solid #f1f5f9; vertical-align:top; }
.items-table td:first-child { width:40px; color:#94a3b8; text-align:center; font-weight:600; vertical-align:middle; }
.items-table td:last-child  { width:46px; text-align:center; vertical-align:middle; }
.qty-col { display:none; }
.show-qty .qty-col { display:table-cell; }
.num-field { text-align:right; }
.remove-row { background:none; border:none; cursor:pointer; color:#dc2626; font-size:1rem; padding:4px 8px; border-radius:4px; }
.remove-row:hover { background:#fef2f2; }
.btn-add-row { background:#f0fdf4; color:#15803d; border:1px dashed #86efac; font-size:0.82rem; padding:7px 14px; border-radius:6px; cursor:pointer; margin-top:10px; }

.total-box { background:#f8fafc; border-radius:8px; padding:16px; margin-top:12px; }
.total-row { display:flex; justify-content:space-between; padding:5px 0; font-size:0.92rem; }
.total-row.grand { font-size:1.05rem; font-weight:700; color:#7b1313; border-top:2px solid #e2e8f0; padding-top:10px; margin-top:5px; }
.total-row.dep-row { color:#1d4ed8; font-weight:600; }

/* Search bar */
.search-bar { display:flex; gap:10px; margin-bottom:20px; align-items:center; }
.search-bar input { max-width:280px; }
.search-bar select { max-width:160px; }

/* Pagination */
.pagination { display:flex; gap:5px; justify-content:center; padding:16px; }
.pagination a,.pagination span { padding:6px 12px; border-radius:6px; font-size:0.85rem; text-decoration:none; border:1px solid #e2e8f0; color:#1e293b; }
.pagination .active span { background:#7b1313; color:#fff; border-color:#7b1313; }
</style>
@stack('styles')
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <div class="sidebar-brand">
    <div class="sidebar-logo">
      <img src="{{ asset('assets/logo.png') }}" alt="SKM" onerror="this.style.display='none'">
    </div>
    <div class="brand-name">SKM Engineering</div>
    <div class="brand-sub">Pte Ltd</div>
  </div>
  <nav>
    <div class="nav-section">Main</div>
    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <span class="icon">🏠</span> Dashboard
    </a>
    <div class="nav-section">Documents</div>
    <a href="{{ route('invoices.index') }}" class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
      <span class="icon">📄</span> Invoices
    </a>
    <a href="{{ route('quotations.index') }}" class="nav-link {{ request()->routeIs('quotations.*') ? 'active' : '' }}">
      <span class="icon">📋</span> Quotations
    </a>
    <div class="nav-section">Settings</div>
    <a href="{{ route('clients.index') }}" class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
      <span class="icon">👥</span> Clients
    </a>
    <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
      <span class="icon">⚙️</span> Settings
    </a>
    <a href="{{ route('activity.index') }}" class="nav-link {{ request()->routeIs('activity.*') ? 'active' : '' }}">
      <span class="icon">📜</span> Activity Log
    </a>
  </nav>
  <div class="sidebar-footer">
    <a href="{{ route('logout') }}">
      <span>🚪</span> Logout ({{ session('user_name') }})
    </a>
  </div>
</div>

<!-- Main -->
<div class="main">
  <div class="topbar">
    <div class="topbar-title">@yield('page_title', 'Dashboard')</div>
    <div class="topbar-user">
      <div class="user-avatar">{{ strtoupper(substr(session('user_name','A'),0,1)) }}</div>
      {{ session('user_name') }}
    </div>
  </div>

  <div class="content">
    @if(session('success'))
      <div class="alert alert-success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-error">⚠️ {{ session('error') }}</div>
    @endif
    @if($errors->any())
      <div class="alert alert-error">
        @foreach($errors->all() as $e)<p>⚠️ {{ $e }}</p>@endforeach
      </div>
    @endif

    @yield('content')
  </div>
</div>

@stack('scripts')
<script>
function fmt(cmd, btn) {
  const ed = btn.closest('.rte-wrap').querySelector('.rte-editor');
  ed.focus(); document.execCommand(cmd, false, null); refreshTB(ed);
}
function clearFmt(btn) {
  const ed = btn.closest('.rte-wrap').querySelector('.rte-editor');
  ed.focus(); document.execCommand('removeFormat', false, null);
  ['insertUnorderedList','insertOrderedList'].forEach(c => { if(document.queryCommandState(c)) document.execCommand(c,false,null); });
  refreshTB(ed);
}
function refreshTB(ed) {
  const map = {'Bold':'bold','Italic':'italic','Underline':'underline','Bullet':'insertUnorderedList','Numbered':'insertOrderedList'};
  ed.closest('.rte-wrap').querySelectorAll('.rte-btn[title]').forEach(b => {
    const k = map[b.title]; if(k) b.classList.toggle('rte-active', document.queryCommandState(k));
  });
}
document.addEventListener('selectionchange', () => {
  const sel = window.getSelection(); if (!sel||sel.rangeCount===0) return;
  const n = sel.getRangeAt(0).commonAncestorContainer;
  const ed = (n.nodeType===3?n.parentElement:n)?.closest('.rte-editor');
  if (ed) refreshTB(ed);
});
function syncRTE() {
  document.querySelectorAll('.rte-editor').forEach(ed => {
    const h = ed.closest('.rte-wrap').nextElementSibling;
    if (h&&h.classList.contains('rte-hidden')) h.value = ed.innerHTML;
  });
  return true;
}
function buildRTE() {
  return `<div class="rte-wrap"><div class="rte-toolbar">
    <button type="button" class="rte-btn" title="Bold" onclick="fmt('bold',this)"><b>B</b></button>
    <button type="button" class="rte-btn" title="Italic" onclick="fmt('italic',this)"><i>I</i></button>
    <button type="button" class="rte-btn" title="Underline" onclick="fmt('underline',this)"><u>U</u></button>
    <div class="rte-sep"></div>
    <button type="button" class="rte-btn" title="Bullet" onclick="fmt('insertUnorderedList',this)">• Bullet</button>
    <button type="button" class="rte-btn" title="Numbered" onclick="fmt('insertOrderedList',this)">1. Number</button>
    <div class="rte-sep"></div>
    <button type="button" class="rte-btn" style="font-size:0.72rem;color:#94a3b8;" onclick="clearFmt(this)">✕ Clear</button>
  </div><div class="rte-editor" contenteditable="true" data-placeholder="Describe work done..."></div></div>
  <textarea class="rte-hidden" name="description[]"></textarea>`;
}
function addRow() {
  const tbody = document.getElementById('itemsBody'); const tr = document.createElement('tr');
  tr.innerHTML = `<td style="color:#94a3b8;text-align:center;font-weight:600;">${tbody.rows.length+1}</td>
    <td>${buildRTE()}</td>
    <td class="qty-col"><input type="number" name="qty[]" step="0.01" min="0" placeholder="0" class="num-field qty-f" oninput="calcRow(this)"></td>
    <td class="qty-col"><input type="text" name="unit[]" placeholder="m², no..."></td>
    <td class="qty-col"><input type="number" name="unit_price[]" step="0.01" min="0" placeholder="0.00" class="num-field up-f" oninput="calcRow(this)"></td>
    <td><input type="number" name="amount[]" step="0.01" min="0" placeholder="0.00" class="num-field amt-f" oninput="calcTotal()"></td>
    <td><button type="button" class="remove-row" onclick="removeRow(this)">✕</button></td>`;
  tbody.appendChild(tr); reIndex();
}
function removeRow(btn) { if(document.querySelectorAll('#itemsBody tr').length<=1)return; btn.closest('tr').remove(); reIndex(); calcTotal(); }
function reIndex() { document.querySelectorAll('#itemsBody tr').forEach((tr,i)=>{ tr.cells[0].textContent=i+1; }); }
function calcRow(inp) {
  const tr=inp.closest('tr');
  const q=parseFloat(tr.querySelector('.qty-f')?.value)||0;
  const u=parseFloat(tr.querySelector('.up-f')?.value)||0;
  const af=tr.querySelector('.amt-f'); if(af) af.value=(q*u).toFixed(2); calcTotal();
}
function calcTotal() {
  let total=0; document.querySelectorAll('.amt-f').forEach(f=>total+=parseFloat(f.value)||0);
  const showDep = document.getElementById('toggleDep')?.checked;
  const dep = showDep?(parseFloat(document.getElementById('depPct')?.value)||0):0;
  const net = showDep?total*dep/100:total;
  if(document.getElementById('totalAmt')) document.getElementById('totalAmt').textContent=total.toFixed(2);
  if(document.getElementById('depAmt'))   document.getElementById('depAmt').textContent=(total*dep/100).toFixed(2);
  if(document.getElementById('netAmt'))   document.getElementById('netAmt').textContent=net.toFixed(2);
}
</script>
</body>
</html>
