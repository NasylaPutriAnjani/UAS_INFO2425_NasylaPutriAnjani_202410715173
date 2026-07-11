// ══════════════════════════════════════════
//  STATE
// ══════════════════════════════════════════
let currentUser = null;   // { name, role: 'buyer'|'seller'|'admin' }

// Role display config
const ROLE_CONFIG = {
  buyer: {
    roleIcon:     '🛒',
    chipLabel:    'Akun Saya',
    chipSublabel: 'Pembeli · RubbyBooks',
    showCart:     true,
    showSearch:   true,
    dropdown: [
      { icon:'🏠', label:'Beranda', action:"_showPageDirect('buyer');closeUserDropdown()" },
      { icon:'📖', label:'Katalog Buku',    action:"_showPageDirect('catalog');closeUserDropdown()" },
      { icon:'📦', label:'Pesanan Saya',    action:"_showPageDirect('buyer_orders');closeUserDropdown()" },
      { icon:'❤️', label:'Wishlist',        action:"_showPageDirect('buyer_wishlist');closeUserDropdown()" },
      { divider: true },
      { icon:'⚙️', label:'Pengaturan Akun', action:"_showPageDirect('account_settings');closeUserDropdown()" },
      { divider: true },
      { icon:'🚪', label:'Keluar',          action:'doLogout()', danger: true }
    ]
  },
  seller: {
    roleIcon:     '📦',
    chipLabel:    'Seller Dashboard',
    chipSublabel: 'Penjual · RubbyBooks',
    showCart:     false,
    showSearch:   true,
    dropdown: [
      { icon:'📊', label:'Dashboard Penjual', action:"_showPageDirect('seller');closeUserDropdown()" },
      { icon:'📦', label:'Kelola Produk',    action:"_showPageDirect('seller_products');closeUserDropdown()" },
      { icon:'📋', label:'Pesanan Masuk',    action:"_showPageDirect('seller_orders');closeUserDropdown()" },
      { icon:'💬', label:'Ulasan & Rating',   action:"_showPageDirect('seller_reviews');closeUserDropdown()" },
      { icon:'📈', label:'Laporan Penjualan',action:"_showPageDirect('seller_report');closeUserDropdown()" },
      { divider: true },
      { icon:'⚙️', label:'Pengaturan Akun', action:"_showPageDirect('account_settings');closeUserDropdown()" },
      { divider: true },
      { icon:'🚪', label:'Keluar',           action:'doLogout()', danger: true }
    ]
  },
  admin: {
    roleIcon:     '🔐',
    chipLabel:    'Admin Panel',
    chipSublabel: 'Administrator · RubbyBooks',
    showCart:     false,
    showSearch:   true,
    dropdown: [
      { icon:'🖥️', label:'Panel Admin',     action:"_showPageDirect('admin');closeUserDropdown()" },
      { icon:'👥', label:'Manajemen User',  action:"_showPageDirect('admin_users');closeUserDropdown()" },
      { icon:'🏷️', label:'Kategori',        action:"_showPageDirect('admin_categories');closeUserDropdown()" },
      { icon:'📚', label:'Produk',          action:"_showPageDirect('admin_products');closeUserDropdown()" },
      { icon:'🛒', label:'Pesanan',         action:"_showPageDirect('admin_orders');closeUserDropdown()" },
      { divider: true },
      { icon:'⚙️', label:'Pengaturan Akun', action:"_showPageDirect('account_settings');closeUserDropdown()" },
      { icon:'⚙️', label:'Pengaturan Sistem', action:"_showPageDirect('admin_settings');closeUserDropdown()" },
      { divider: true },
      { icon:'🚪', label:'Keluar',          action:'doLogout()', danger: true }
    ]
  }
};

// ══════════════════════════════════════════
function attachDropdownBadgeKeys() {
  const apply = (role, label, badgeKey, warn = false) => {
    const item = ROLE_CONFIG[role]?.dropdown.find(entry => entry.label === label);
    if (!item) return;
    item.badgeKey = badgeKey;
    item.warn = warn;
  };
  apply('buyer', 'Pesanan Saya', 'orders');
  apply('buyer', 'Keranjang', 'cart');
  apply('seller', 'Pesanan Masuk', 'orders');
  apply('admin', 'Pesanan', 'orders');
}

function ensureDropdownCountItems() {
  const buyerItems = ROLE_CONFIG.buyer?.dropdown || [];
  if (!buyerItems.some(item => item.label === 'Keranjang')) {
    const wishlistIndex = buyerItems.findIndex(item => item.label === 'Wishlist');
    buyerItems.splice(wishlistIndex + 1, 0, {
      icon: 'Cart',
      label: 'Keranjang',
      action: "_showPageDirect('buyer_cart');closeUserDropdown()",
      badgeKey: 'cart'
    });
  }
}

function removeNotificationDropdownItems() {
  Object.values(ROLE_CONFIG).forEach(config => {
    config.dropdown = config.dropdown.filter(item => item.divider || item.label !== 'Notifikasi');
  });
}

attachDropdownBadgeKeys();
ensureDropdownCountItems();
removeNotificationDropdownItems();

//  ROLE-BASED ACCESS CONTROL (RBAC)
// ══════════════════════════════════════════

// Pages each role is ALLOWED to access
const PAGE_ACCESS = {
  guest:  ['home', 'catalog'],
  buyer:  ['home', 'catalog', 'checkout', 'tracking', 'buyer', 'buyer_account', 'buyer_wishlist', 'buyer_cart', 'buyer_orders', 'buyer_reviews', 'buyer_notifications', 'cart', 'account_settings'],
  seller: ['home', 'catalog', 'seller', 'seller_products', 'seller_orders', 'seller_reviews', 'seller_notifications', 'seller_report', 'account_settings'],
  admin:  ['home', 'catalog', 'admin', 'admin_users', 'admin_analytics', 'admin_categories', 'admin_products', 'admin_orders', 'admin_notifications', 'account_settings', 'admin_settings']
};


// Human-readable page names for error messages
const PAGE_NAMES = {
  home:     'Beranda',
  catalog:  'Katalog Buku',
  checkout: 'Checkout',
  tracking: 'Lacak Pesanan',
  account_settings: 'Pengaturan Akun',
  buyer:    'Beranda',
  buyer_account: 'Akun Saya',
  buyer_wishlist: 'Wishlist',
  buyer_cart: 'Keranjang',
  buyer_orders: 'Pesanan Saya',
  buyer_reviews: 'Review Saya',
  buyer_notifications: 'Notifikasi Pembeli',
  cart:     'Keranjang',
  seller:   'Dashboard Penjual',
  seller_products:      'Produk Saya',
  seller_orders:        'Pesanan Masuk',
  seller_reviews:       'Ulasan & Rating',
  seller_notifications: 'Notifikasi Penjual',
  seller_report:        'Laporan Penjualan',
  admin:    'Panel Admin',
  admin_users:          'Manajemen User',
  admin_analytics:      'Analitik Performa',
  admin_categories:     'Manajemen Kategori',
  admin_products:       'Manajemen Produk',
  admin_orders:         'Semua Pesanan',
  admin_notifications:  'Notifikasi Admin',
  admin_settings:       'Pengaturan Sistem'
};

function canAccess(pageName) {
  const role = currentUser ? currentUser.role : 'guest';
  return (PAGE_ACCESS[role] || []).includes(pageName);
}

function goHome() {
  window.location.href = 'index.php?page=home';
}

function toggleMobileMenu() {
  const panel = document.getElementById('mobileNavPanel');
  const overlay = document.getElementById('mobileMenuOverlay');
  if (!panel || !overlay) return;
  const willOpen = !panel.classList.contains('open');
  panel.classList.toggle('open', willOpen);
  overlay.classList.toggle('open', willOpen);
  document.body.style.overflow = willOpen ? 'hidden' : '';
}

function closeMobileMenu() {
  const panel = document.getElementById('mobileNavPanel');
  const overlay = document.getElementById('mobileMenuOverlay');
  if (panel) panel.classList.remove('open');
  if (overlay) overlay.classList.remove('open');
  document.body.style.overflow = '';
}

document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeMobileMenu();
});

// ══════════════════════════════════════════
//  PAGE NAVIGATION  (with RBAC guard)
// ══════════════════════════════════════════
function showPage(name) {
  if (!canAccess(name)) {
    handleAccessDenied(name);
    return;
  }
  const serverPages = [
    'home',
    'buyer', 'buyer_account', 'buyer_wishlist', 'buyer_cart', 'buyer_orders', 'buyer_reviews', 'buyer_notifications',
    'seller', 'seller_products', 'seller_orders', 'seller_reviews', 'seller_notifications', 'seller_report',
    'admin', 'admin_users', 'admin_analytics', 'admin_categories', 'admin_products', 'admin_orders', 'admin_notifications', 'admin_settings',
    'account_settings', 'checkout', 'tracking'
  ];
  if (serverPages.includes(name)) {
    _showPageDirect(name);
    return;
  }
  const page = document.getElementById('page-' + name);
  if (!page) {
    _showPageDirect(name);
    return;
  }
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  page.classList.add('active');
  updateNavActive(name);
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function scrollToHomeSection(sectionId) {
  const homePage = document.getElementById('page-home');
  if (!homePage) {
    // Not on home page at all — redirect with hash
    window.location.href = 'index.php?page=home#' + sectionId;
    return;
  }
  if (!homePage.classList.contains('active')) {
    document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
    homePage.classList.add('active');
    updateNavActive('home');
  }
  setTimeout(() => {
    const target = document.getElementById(sectionId);
    if (target) {
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }, 50);
}

function goToNotifications() {
  const role = currentUser ? currentUser.role : 'guest';
  if (role === 'buyer')  { _showPageDirect('buyer_notifications'); return; }
  if (role === 'seller') { _showPageDirect('seller_notifications'); return; }
  if (role === 'admin')  { _showPageDirect('admin_notifications');  return; }
  openAuth();
}

function handleAccessDenied(pageName) {
  const role = currentUser ? currentUser.role : 'guest';
  const pageLbl = PAGE_NAMES[pageName] || pageName;

  if (!currentUser) {
    showToast('🔐 Masuk dulu untuk mengakses ' + pageLbl);
    setTimeout(openAuth, 300);
    return;
  }

  if (role === 'seller') {
    showAccessGuard({
      icon: '🏪',
      title: 'Akses Dibatasi',
      desc: 'Sebagai <b>Penjual</b>, Anda hanya dapat mengakses Dashboard Penjual. Keluar dan masuk sebagai Pembeli untuk mengakses halaman ini.',
      primaryLabel: 'Ke Dashboard Penjual',
      primaryAction: () => _showPageDirect('seller'),
      secondaryLabel: 'Keluar / Ganti Role',
      secondaryAction: doLogout
    });
  } else if (role === 'admin') {
    showAccessGuard({
      icon: '🔐',
      title: 'Akses Dibatasi',
      desc: 'Sebagai <b>Administrator</b>, Anda hanya dapat mengakses Panel Admin. Keluar untuk beralih role.',
      primaryLabel: 'Ke Panel Admin',
      primaryAction: () => _showPageDirect('admin'),
      secondaryLabel: 'Keluar',
      secondaryAction: doLogout
    });
  } else if (role === 'buyer' && (pageName === 'seller' || pageName === 'admin')) {
    showAccessGuard({
      icon: '⛔',
      title: 'Halaman Tidak Tersedia',
      desc: 'Halaman <b>' + pageLbl + '</b> tidak dapat diakses oleh Pembeli. Silakan gunakan akun Penjual atau Admin.',
      primaryLabel: 'Kembali ke Beranda',
      primaryAction: () => _showPageDirect('home'),
      secondaryLabel: null
    });
  }
}

function showAccessGuard(cfg) {
  const existing = document.getElementById('access-guard-overlay');
  if (existing) existing.remove();

  const overlay = document.createElement('div');
  overlay.id = 'access-guard-overlay';
  overlay.className = 'page-access-guard';
  overlay.innerHTML = `
    <div class="guard-box">
      <div class="guard-icon">${cfg.icon}</div>
      <div class="guard-title">${cfg.title}</div>
      <div class="guard-desc">${cfg.desc}</div>
      <div class="guard-actions">
        <button class="btn-primary" onclick="document.getElementById('access-guard-overlay').remove();(${cfg.primaryAction.toString()})()">${cfg.primaryLabel}</button>
        ${cfg.secondaryLabel ? `<button class="btn-secondary" onclick="document.getElementById('access-guard-overlay').remove();(${cfg.secondaryAction.toString()})()">${cfg.secondaryLabel}</button>` : ''}
      </div>
    </div>`;
  document.body.appendChild(overlay);
  overlay.addEventListener('click', e => { if (e.target === overlay) overlay.remove(); });
}

function updateNavActive(pageName) {
  document.querySelectorAll('.nav-link').forEach(b => b.classList.remove('active'));
  const map = { home:'Beranda', catalog:'Katalog', checkout:'Checkout', tracking:'Lacak Pesanan',
                seller:'📊 Dashboard', admin:'🖥️ Panel Admin' };
  const label = map[pageName];
  if (label) {
    document.querySelectorAll('.nav-link').forEach(b => {
      if (b.textContent.trim().includes(label.replace(/^[^\s]+ /,''))) b.classList.add('active');
    });
  }
}

function setActive(btn) {
  document.querySelectorAll('.nav-link').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}

function setActiveByName(name) {
  document.querySelectorAll('.nav-link').forEach(b => {
    b.classList.toggle('active', b.textContent.trim() === name);
  });
}

// ══════════════════════════════════════════
//  AUTH MODAL
// ══════════════════════════════════════════
function openAuth(hint) {
  selectRegRole(hint === 'seller' ? 'seller' : 'buyer');
  switchTab(hint === 'seller' || hint === 'daftar' ? 'daftar' : 'masuk');
  document.getElementById('authModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeAuth() {
  document.getElementById('authModal').classList.remove('open');
  document.body.style.overflow = '';
}

function switchTab(tab) {
  document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
  document.getElementById('tab-' + tab).classList.add('active');
  document.getElementById('body-masuk').style.display = tab === 'masuk' ? 'block' : 'none';
  document.getElementById('body-daftar').style.display = tab === 'daftar' ? 'block' : 'none';
}

function selectRegRole(role) {
  const input = document.getElementById('reg-role');
  if (input) input.value = role;
  document.querySelectorAll('.auth-role-type').forEach(el => {
    el.classList.toggle('active', el.dataset.role === role);
  });
}

function validateRegister(e) {
  const pass = document.getElementById('reg-pass').value;
  const confirm = document.getElementById('reg-pass-confirm').value;
  if (pass.length < 6) {
    e.preventDefault();
    showToast('❌ Password minimal 6 karakter');
    return false;
  }
  if (pass !== confirm) {
    e.preventDefault();
    showToast('❌ Konfirmasi password tidak cocok');
    return false;
  }
  return true;
}

function chipLabelFor(user, cfg) {
  return (user.name || '').trim().split(/\s+/)[0] || cfg.chipLabel;
}

function applyNavUser(user) {
  if (!user || !ROLE_CONFIG[user.role]) return;
  currentUser = user;
  const cfg = ROLE_CONFIG[user.role];

  document.getElementById('nav-guest').style.display = 'none';
  document.getElementById('nav-loggedin').style.display = 'block';

  const avatar = document.getElementById('nav-avatar-icon');
  if (user.avatar) {
    avatar.innerHTML = `<img src="src/${String(user.avatar).replace(/^\/+/, '')}" alt="Foto profil">`;
    avatar.className = 'nav-user-avatar nav-user-avatar--role user-photo-avatar';
  } else {
    avatar.textContent = cfg.roleIcon;
    avatar.className = 'nav-user-avatar nav-user-avatar--role';
  }
  avatar.dataset.role = user.role;

  const chip = document.getElementById('nav-user-chip');
  chip.className = 'nav-user-chip';
  chip.title = user.name;

  document.getElementById('nav-username').textContent = chipLabelFor(user, cfg);
  document.getElementById('nav-userrole').textContent = cfg.chipSublabel;

  const cartBtn = document.getElementById('nav-cart-btn');
  if (cartBtn) cartBtn.style.display = cfg.showCart ? 'flex' : 'none';

  const search = document.getElementById('nav-search');
  if (search) search.style.display = cfg.showSearch ? '' : 'none';

  renderNavLinks(user.role);
  buildDropdown(cfg.dropdown);
  applyRoleUI(user.role);
}

// Internal nav render — switches to the correct nav link group per role
function renderNavLinks(role) {
  const navCenter = document.getElementById('nav-center');
  if (!navCenter) return;

  // Let CSS decide desktop/mobile visibility.
  navCenter.style.display = '';
  ['buyer', 'seller', 'admin'].forEach(r => {
    const el = document.getElementById('nav-links-' + r);
    if (el) el.style.display = 'none';
  });

  // Show the matching group (buyer for guest too)
  const group = (role === 'seller' || role === 'admin') ? role : 'buyer';
  const el = document.getElementById('nav-links-' + group);
  if (el) el.style.display = '';
}

// Apply role-specific element visibility (buyer-only CTAs etc.)
function applyRoleUI(role) {
  const buyerOnly = document.querySelectorAll('[data-role="buyer"]');
  buyerOnly.forEach(el => {
    el.style.display = (role === 'buyer') ? '' : 'none';
  });
}

// Direct page switch (skips RBAC guard — used internally after role is already validated)
function _showPageDirect(name) {
  // Langsung route server-side supaya URL berubah dan view dirender ulang.
  window.location.href = 'index.php?page=' + encodeURIComponent(name);
}

// ══════════════════════════════════════════
//  DROPDOWN (dynamic per role)
// ══════════════════════════════════════════
function buildDropdown(items) {
  const el = document.getElementById('userDropdown');
  el.innerHTML = items.map(item => {
    if (item.divider) return '<div class="dropdown-divider"></div>';
    const count = item.badgeKey ? Number(window.__RB_USER__?.counts?.[item.badgeKey] || 0) : 0;
    const badge = count > 0 ? `<span class="dropdown-badge${item.warn ? ' warn' : ''}">${count}</span>` : '';
    return `<button class="dropdown-item${item.danger ? ' danger' : ''}" onclick="${item.action}"><span>${item.icon}</span><span class="dropdown-label">${item.label}</span>${badge}</button>`;
  }).join('');
}

function toggleUserDropdown() {
  document.getElementById('userDropdown').classList.toggle('open');
}

function closeUserDropdown() {
  document.getElementById('userDropdown').classList.remove('open');
}

document.addEventListener('click', e => {
  const wrap = document.querySelector('.nav-user-wrap');
  if (wrap && !wrap.contains(e.target)) closeUserDropdown();
});

// ══════════════════════════════════════════
//  LOGOUT
// ══════════════════════════════════════════
function doLogout() {
  closeUserDropdown();
  window.location.href = 'index.php?action=logout';
}

// ══════════════════════════════════════════
//  CART
// ══════════════════════════════════════════
function openCart() {
  if (currentUser && currentUser.role !== 'buyer') {
    showToast('⛔ Keranjang belanja hanya tersedia untuk Pembeli');
    return;
  }
  document.getElementById('cartOverlay').classList.add('open');
  document.getElementById('cartDrawer').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeCart() {
  document.getElementById('cartOverlay').classList.remove('open');
  document.getElementById('cartDrawer').classList.remove('open');
  document.body.style.overflow = '';
}

function requireBuyerAction(featureName) {
  const user = window.__RB_USER__ || currentUser;
  if (user && user.role !== 'buyer') {
    showToast('⛔ ' + featureName + ' hanya tersedia untuk Pembeli');
    return false;
  }
  if (!user) {
    openAuth();
    return false;
  }
  return true;
}

// ══════════════════════════════════════════
//  ADD TO CART
// ══════════════════════════════════════════
function addToCart(e, productId, name, qty = 1, redirectPage = '') {
  if (e) e.stopPropagation();
  const user = window.__RB_USER__ || currentUser;
  // Only buyers (and guests who will be prompted to login) can add to cart
  if (user && user.role !== 'buyer') {
    showToast('⛔ Fitur keranjang hanya tersedia untuk Pembeli');
    return;
  }
  if (!user) {
    showToast('🔐 Masuk dahulu untuk menambahkan ke keranjang');
    setTimeout(openAuth, 400);
    return;
  }
  
  // Create hidden form to post to backend
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = 'index.php?action=add_cart';
  
  const input = document.createElement('input');
  input.type = 'hidden';
  input.name = 'product_id';
  input.value = productId;
  form.appendChild(input);

  const qtyInput = document.createElement('input');
  qtyInput.type = 'hidden';
  qtyInput.name = 'qty';
  qtyInput.value = Math.max(1, parseInt(qty) || 1);
  form.appendChild(qtyInput);

  if (redirectPage) {
    const redirectInput = document.createElement('input');
    redirectInput.type = 'hidden';
    redirectInput.name = 'redirect';
    redirectInput.value = redirectPage;
    form.appendChild(redirectInput);
  }

  document.body.appendChild(form);
  form.submit();
}

// ══════════════════════════════════════════
//  WISHLIST
// ══════════════════════════════════════════
function toggleWish(btn) {
  const active = btn.textContent === '♥';
  btn.textContent = active ? '♡' : '♥';
  btn.style.color = active ? '' : 'var(--rose)';
  if (!active) showToast('❤️ Ditambahkan ke Wishlist!');
}
function toggleWishFeatured(btn) {
  const active = btn.textContent.includes('♥');
  btn.innerHTML = active ? '♡ Wishlist' : '♥ Tersimpan';
  if (!active) showToast('❤️ Ditambahkan ke Wishlist!');
}

// ══════════════════════════════════════════
//  FILTERS
// ══════════════════════════════════════════
function filterCat(el) {
  const catId = el.getAttribute('data-id');
  if (!catId) {
    window.location.href = 'index.php?page=catalog';
  } else {
    window.location.href = 'index.php?page=catalog&category[]=' + catId;
  }
}
function selectPay(el) {
  (el.closest('.pay-grid') || el.parentElement).querySelectorAll('.pay-opt').forEach(o => o.classList.remove('active'));
  el.classList.add('active');
}
function updatePrice(input) {
  const el = document.getElementById('price-max');
  if (el) el.textContent = 'Rp ' + parseInt(input.value).toLocaleString('id-ID');
}

function submitCatalogFilters() {
  const form = document.getElementById('catalog-form');
  if (!form) return;
  const pageInput = document.getElementById('page-input');
  if (pageInput) pageInput.value = '1';
  form.submit();
}

function changePage(page) {
  const form = document.getElementById('catalog-form');
  const pageInput = document.getElementById('page-input');
  if (!form || !pageInput) return;
  pageInput.value = page;
  form.submit();
}

// ══════════════════════════════════════════
//  TOAST
// ══════════════════════════════════════════
let toastTimer;
function showToast(msg) {
  const t = document.getElementById('toast');
  const icon = msg.match(/^([\u{1F300}-\u{1FFFF}]|[\u2600-\u27FF]|✅|❌|⚠️|🔐|🔍|🛒|📋|👋|🏪)/u)?.[0] || '✅';
  t.querySelector('.toast-icon').textContent = icon;
  document.getElementById('toast-msg').textContent = msg.replace(/^[\u{1F300}-\u{1FFFF}]|^[\u2600-\u27FF]|^✅|^❌|^⚠️|^🔐|^🔍|^🛒|^📋|^👋|^🏪/u, '').trim();
  t.classList.add('show');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => t.classList.remove('show'), 3200);
}

// ══════════════════════════════════════════
//  KEYBOARD
// ══════════════════════════════════════════
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') { closeCart(); closeAuth(); closeUserDropdown(); }
});

// Setup on page load
document.addEventListener('DOMContentLoaded', () => {
  const catalogForm = document.getElementById('catalog-form');
  if (catalogForm) {
    const catAll = document.getElementById('cat-all');
    const catChecks = Array.from(catalogForm.querySelectorAll('.cat-checkbox'));
    const rangeInput = catalogForm.querySelector('.range-input');
    const sortSelect = document.querySelector('.sort-select[form="catalog-form"]') || catalogForm.querySelector('.sort-select');

    catalogForm.addEventListener('change', e => {
      const target = e.target;
      if (!(target instanceof HTMLInputElement || target instanceof HTMLSelectElement)) return;

      if (target === catAll) {
        catChecks.forEach(cb => { cb.checked = false; });
        submitCatalogFilters();
        return;
      }

      if (catChecks.includes(target)) {
        if (catAll) catAll.checked = false;
        submitCatalogFilters();
        return;
      }

      if (target === rangeInput || target === sortSelect) {
        submitCatalogFilters();
      }
    });

    if (rangeInput) {
      rangeInput.addEventListener('input', () => updatePrice(rangeInput));
      updatePrice(rangeInput);
    }

    if (sortSelect && !catalogForm.contains(sortSelect)) {
      sortSelect.addEventListener('change', submitCatalogFilters);
    }
  }

  // Global search
  const searchInput = document.querySelector('#nav-search input');
  if (searchInput) {
    const currentParams = new URLSearchParams(window.location.search);
    searchInput.value = currentParams.get('q') || '';

    searchInput.addEventListener('keydown', e => {
      if (e.key === 'Enter') {
        e.preventDefault();
        const val = searchInput.value.trim();
        if (val) {
          window.location.href = 'index.php?page=catalog&q=' + encodeURIComponent(val);
        } else {
          window.location.href = 'index.php?page=catalog';
        }
      }
    });
  }

  // Nav + Auth init
  const params = new URLSearchParams(window.location.search);
  const page = params.get('page') || 'home';
  updateNavActive(page);

  if (window.__RB_USER__) {
    applyNavUser(window.__RB_USER__);
  }

  const authTab = params.get('auth');
  if (authTab === 'daftar') {
    openAuth('daftar');
  } else if (authTab === 'masuk' || page === 'login') {
    openAuth('masuk');
  } else if (page === 'register') {
    openAuth('seller');
  }

  if (window.location.hash) {
    const sectionId = window.location.hash.substring(1);
    if (page === 'home' && document.getElementById(sectionId)) {
      setTimeout(() => {
        document.getElementById(sectionId).scrollIntoView({ behavior: 'smooth', block: 'start' });
      }, 300);
    }
  }
});

// ══════════════════════════════════════════
//  PRODUCT DETAIL MODAL
// ══════════════════════════════════════════

const AVATAR_GRADS = [
  '#7c3aed,#a855f7','#1e40af,#1d4ed8','#065f46,#047857',
  '#b91c1c,#dc2626','#b45309,#d97706','#0e7490,#0891b2'
];
function _avatarGrad(name) {
  let h = 0; for (let c of name) h = (h * 31 + c.charCodeAt(0)) & 0xffffffff;
  return AVATAR_GRADS[Math.abs(h) % AVATAR_GRADS.length];
}
function _initials(name) {
  return name.split(' ').slice(0,2).map(w => w[0]||'').join('').toUpperCase();
}
function _avatarUrl(path) {
  if (!path) return '';
  const clean = String(path).replace(/^\/+/, '');
  return clean.startsWith('http') ? clean : `src/${clean}`;
}
function _stars(n) {
  n = Math.round(n);
  return '★'.repeat(n) + '☆'.repeat(5 - n);
}
function _rupiah(n) {
  return 'Rp ' + Number(n).toLocaleString('id-ID');
}
function _timeAgo(dateStr) {
  if (!dateStr) return '';
  const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
  if (diff < 60) return 'baru saja';
  if (diff < 3600) return Math.floor(diff/60) + ' menit lalu';
  if (diff < 86400) return Math.floor(diff/3600) + ' jam lalu';
  if (diff < 604800) return Math.floor(diff/86400) + ' hari lalu';
  if (diff < 2592000) return Math.floor(diff/604800) + ' minggu lalu';
  return Math.floor(diff/2592000) + ' bulan lalu';
}

let _pdQty = 1;
let _pdProduct = null;

function openPD(productId) {
  const overlay = document.getElementById('pd-overlay');
  const body = document.getElementById('pd-body');
  overlay.style.display = 'flex';
  document.body.style.overflow = 'hidden';
  body.innerHTML = '<div class="pd-loading">⏳ Memuat detail buku...</div>';
  _pdQty = 1;

  fetch(`index.php?action=get_product&id=${productId}`)
    .then(r => r.json())
    .then(data => {
      if (data.error) { body.innerHTML = `<div class="pd-loading">❌ ${data.error}</div>`; return; }
      _pdProduct = data.product;
      body.innerHTML = _buildPD(data);
      // Re-run tab click on first tab
      document.querySelector('.pd-tab-btn')?.click();
    })
    .catch(() => { body.innerHTML = '<div class="pd-loading">❌ Gagal memuat data.</div>'; });
}

function _buildPD(data) {
  const p = data.product;
  const avg = parseFloat(p.avg_rating) || 0;
  const avgStr = avg > 0 ? avg.toFixed(1) : '—';
  const starsStr = avg > 0 ? _stars(avg) : '☆☆☆☆☆';
  const bc = ['bc1','bc2','bc3','bc4','bc5','bc6'][p.id % 6];
  const coverStyle = p.image
    ? `background-image:url('${p.image}');background-size:cover;background-position:center;font-size:0;`
    : '';
  const condLabel = {new:'Baru', used_good:'Bekas - Baik', used_fair:'Bekas - Cukup'}[p.book_condition] || p.book_condition;
  const stockClass = p.stock <= 0 ? 'out' : p.stock <= 5 ? 'low' : '';
  const stockLabel = p.stock <= 0 ? 'Stok habis' : `Stok tersedia — ${p.stock} buku`;
  const sellerInit = _initials(p.seller_name);
  const sellerGrad = _avatarGrad(p.seller_name);
  const sellerAvatar = p.seller_avatar
    ? `<div class="pd-seller-avatar user-photo-avatar"><img src="${_avatarUrl(p.seller_avatar)}" alt="Foto profil"></div>`
    : `<div class="pd-seller-avatar" style="background:linear-gradient(135deg,${sellerGrad})">${sellerInit}</div>`;

  // Review breakdown
  const total = parseInt(p.review_count) || 0;
  const bd = data.breakdown || {};
  const bdHtml = [5,4,3,2,1].map(s => {
    const cnt = bd[s] || 0;
    const pct = total > 0 ? Math.round(cnt / total * 100) : 0;
    return `<div class="pd-bar-row">
      <span>${s} ★</span>
      <div class="pd-bar-track"><div class="pd-bar-fill" style="width:${pct}%"></div></div>
      <span class="pd-bar-pct">${pct}%</span>
    </div>`;
  }).join('');

  const wishIcon = data.in_wishlist ? '❤️' : '♡';
  const wishActive = data.in_wishlist ? 'active' : '';

  // Reviews list
  const revHtml = data.reviews.length === 0
    ? `<div class="pd-no-reviews">📭 Belum ada ulasan untuk buku ini.</div>`
    : data.reviews.map(r => {
        const init = _initials(r.buyer_name);
        const grad = _avatarGrad(r.buyer_name);
        return `<div class="pd-review-item">
          <div class="pd-rev-top">
            <div class="pd-rev-avatar" style="background:linear-gradient(135deg,${grad})">${init}</div>
            <div>
              <div class="pd-rev-name">${r.buyer_name}</div>
              <div class="pd-rev-stars">${_stars(parseInt(r.rating))}</div>
            </div>
            <div class="pd-rev-date">${_timeAgo(r.created_at)}</div>
          </div>
          <div class="pd-rev-comment">${r.comment || ''}</div>
        </div>`;
      }).join('');

  return `
  <div class="pd-top">
    <div class="pd-cover-col">
      <div class="pd-cover-main ${bc}" style="${coverStyle}">
        ${p.book_condition === 'new' ? '<span class="pd-badge">Baru</span>' : ''}
        <button class="pd-wish-btn ${wishActive}" onclick="pdToggleWish(${p.id},this)" title="Wishlist">${wishIcon}</button>
      </div>
      <div class="pd-seller-box">
        ${sellerAvatar}
        <div>
          <div class="pd-seller-name">🏪 ${p.seller_name}</div>
          <div class="pd-seller-sub">Penjual Terverifikasi ✓</div>
        </div>
      </div>
    </div>
    <div class="pd-info-col">
      <div class="pd-breadcrumb">Katalog / ${p.category} / <span>${p.name}</span></div>
      <div>
        <div class="pd-category">${p.category}</div>
        <div class="pd-title">${p.name}</div>
      </div>
      <div class="pd-rating-row">
        <span class="stars">${starsStr}</span>
        <span class="pd-rating-val">${avgStr}</span>
        <span>(${total} ulasan)</span>
        <span>·</span>
        <span>${p.sold_count} terjual</span>
      </div>
      <div class="pd-price-box">
        <div class="pd-price">${_rupiah(p.price)}</div>
        <div class="pd-price-note">Harga sudah termasuk PPN, belum termasuk ongkos kirim</div>
        <div class="pd-stock ${stockClass}">${stockLabel}</div>
      </div>
      <div class="pd-qty-row">
        <span class="pd-qty-label">Jumlah</span>
        <div class="pd-qty-ctrl">
          <button onclick="pdQty(-1)">−</button>
          <input type="number" id="pd-qty-inp" value="1" min="1" max="${p.stock}" readonly>
          <button onclick="pdQty(1)">+</button>
        </div>
      </div>
      <div class="pd-actions">
        <button class="pd-btn-cart" onclick="pdAddCart()">+ Keranjang</button>
        <button class="pd-btn-buy" onclick="pdBuyNow()">🛒 Beli Sekarang</button>
      </div>
      <div class="pd-meta-grid">
        <div class="pd-meta-item"><span class="pd-meta-label">Kondisi</span><span class="pd-meta-val">${condLabel}</span></div>
        <div class="pd-meta-item"><span class="pd-meta-label">Kategori</span><span class="pd-meta-val">${p.category}</span></div>
        <div class="pd-meta-item"><span class="pd-meta-label">Stok</span><span class="pd-meta-val">${p.stock} buku</span></div>
        <div class="pd-meta-item"><span class="pd-meta-label">Terjual</span><span class="pd-meta-val">${p.sold_count}+ eksemplar</span></div>
      </div>
    </div>
  </div>

  <div class="pd-tabs">
    <div class="pd-tab-nav">
      <button class="pd-tab-btn" onclick="pdTab('desc',this)">Deskripsi</button>
      <button class="pd-tab-btn" onclick="pdTab('spec',this)">Spesifikasi</button>
      <button class="pd-tab-btn" onclick="pdTab('rev',this)">Ulasan (${total})</button>
    </div>
    <div id="pd-pane-desc" class="pd-tab-pane">
      <div class="pd-desc">${p.description || 'Tidak ada deskripsi.'}</div>
    </div>
    <div id="pd-pane-spec" class="pd-tab-pane">
      <div class="pd-meta-grid" style="gap:16px 32px">
        <div class="pd-meta-item"><span class="pd-meta-label">Judul</span><span class="pd-meta-val">${p.name}</span></div>
        <div class="pd-meta-item"><span class="pd-meta-label">Penjual</span><span class="pd-meta-val">${p.seller_name}</span></div>
        <div class="pd-meta-item"><span class="pd-meta-label">Kategori</span><span class="pd-meta-val">${p.category}</span></div>
        <div class="pd-meta-item"><span class="pd-meta-label">Kondisi</span><span class="pd-meta-val">${condLabel}</span></div>
        <div class="pd-meta-item"><span class="pd-meta-label">Harga</span><span class="pd-meta-val">${_rupiah(p.price)}</span></div>
        <div class="pd-meta-item"><span class="pd-meta-label">Stok</span><span class="pd-meta-val">${p.stock} buku</span></div>
      </div>
    </div>
    <div id="pd-pane-rev" class="pd-tab-pane">
      <div class="pd-review-header">
        <div>
          <div class="pd-review-big-score">${avgStr}</div>
          <div class="pd-review-stars">${starsStr}</div>
          <div class="pd-review-count">Dari ${total} ulasan</div>
        </div>
        <div class="pd-breakdown">${bdHtml}</div>
      </div>
      <div class="pd-review-list">${revHtml}</div>
    </div>
  </div>`;
}

function pdTab(pane, btn) {
  document.querySelectorAll('.pd-tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.pd-tab-pane').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('pd-pane-' + pane)?.classList.add('active');
}

function pdQty(delta) {
  const inp = document.getElementById('pd-qty-inp');
  if (!inp) return;
  _pdQty = Math.max(1, Math.min(parseInt(inp.max) || 99, _pdQty + delta));
  inp.value = _pdQty;
}

function pdAddCart() {
  if (!_pdProduct) return;
  if (!requireBuyerAction('Fitur keranjang')) return;
  addToCart(null, _pdProduct.id, _pdProduct.name);
  closePDBtn();
}

function pdBuyNow() {
  if (!_pdProduct) return;
  if (!requireBuyerAction('Fitur beli sekarang')) return;
  addToCart(null, _pdProduct.id, _pdProduct.name, _pdQty, 'checkout');
}

function pdToggleWish(productId, btn) {
  const user = window.__RB_USER__;
  if (!user || user.role !== 'buyer') { openAuth(); return; }
  // Submit wishlist toggle via hidden form
  const f = document.createElement('form');
  f.method = 'POST';
  f.action = `index.php?action=toggle_wishlist`;
  const inp = document.createElement('input');
  inp.type = 'hidden'; inp.name = 'product_id'; inp.value = productId;
  f.appendChild(inp); document.body.appendChild(f); f.submit();
}

function closePD(e) {
  if (e.target.id === 'pd-overlay') closePDBtn();
}
function closePDBtn() {
  document.getElementById('pd-overlay').style.display = 'none';
  document.body.style.overflow = '';
  _pdProduct = null;
}

// Close on Escape
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closePDBtn();
});

// Wire up book cards: click -> openPD  (but not clicking inner buttons/forms)
document.addEventListener('click', function(e) {
  const card = e.target.closest('.book-card');
  if (!card) return;
  // If clicked inside a form or button, let those handle it
  if (e.target.closest('form') || e.target.closest('button')) return;
  const pid = card.dataset.productId;
  if (pid) openPD(pid);
});

// Shared account page
/* ── Tab switching ── */
function switchAccountTab(btn, tabId) {
  document.querySelectorAll('#profile-tabs .tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  const pane = document.getElementById(tabId);
  if (pane) pane.classList.add('active');
}

/* ── Password strength ── */
function checkPasswordStrength(val) {
  const hasLen   = val.length >= 8;
  const hasUpper = /[A-Z]/.test(val);
  const hasNum   = /[0-9]/.test(val);
  const hasSym   = /[!@#$%^&*(),.?":{}|<>]/.test(val);
  let   strength = [hasLen, hasUpper, hasNum, hasSym].filter(Boolean).length;

  const colors   = ['', '#ef4444', '#f59e0b', '#10b981', '#059669'];
  const labels   = ['—', 'Lemah', 'Sedang', 'Kuat', 'Sangat Kuat'];
  const color    = strength ? colors[strength] : 'var(--border)';

  const ruleColor = (ok) => ok ? 'var(--rose-deep)' : 'var(--ink-muted)';
  document.getElementById('rule-len').style.color   = ruleColor(hasLen);
  document.getElementById('rule-upper').style.color = ruleColor(hasUpper);
  document.getElementById('rule-num').style.color   = ruleColor(hasNum);
  document.getElementById('rule-sym').style.color   = ruleColor(hasSym);

  const txt = document.getElementById('pwd-text');
  txt.textContent = labels[strength] || '—';
  txt.style.color = color;

  for (let i = 1; i <= 4; i++) {
    document.getElementById('pwd-bar-' + i).style.background = i <= strength ? color : 'var(--border)';
  }
}

/* ── Delete account gate ── */
function checkDeleteStatus() {
  const input = document.getElementById('delete-confirm-input');
  const cb    = document.getElementById('delete-checkbox');
  const btn   = document.getElementById('btn-delete-account');
  if (!btn) return;

  const ok = input && input.value === 'DELETE' && cb && cb.checked;
  btn.disabled      = !ok;
  btn.style.opacity = ok ? '1' : '0.6';
  btn.style.cursor  = ok ? 'pointer' : 'not-allowed';
  btn.style.background   = ok ? 'var(--rose-deep)' : 'var(--rose-pale)';
  btn.style.color        = ok ? '#fff' : 'var(--rose-deep)';
}

// Buyer checkout page
function updateCheckoutTotal() {
  const checkoutPage = document.getElementById('page-checkout');
  const cityInput = document.getElementById('checkoutCity');
  const subtotalEl = document.getElementById('coSubtotal');
  const shippingEl = document.getElementById('coShipping');
  const totalEl = document.getElementById('coTotal');
  if (!checkoutPage || !cityInput || !subtotalEl || !shippingEl || !totalEl) return;

  const city = (cityInput.value || '').trim().toLowerCase();
  const costMap = { jakarta: 10000, bandung: 15000, surabaya: 20000 };
  const shippingCost = costMap[city] || 25000;
  const subtotal = parseInt(subtotalEl.getAttribute('data-value') || '0', 10);
  const total = subtotal + shippingCost;

  shippingEl.textContent = 'Rp ' + shippingCost.toLocaleString('id-ID');
  totalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function initCheckoutPage() {
  if (!document.getElementById('page-checkout')) return;

  document.querySelectorAll('.numeric-only').forEach(input => {
    input.addEventListener('input', () => {
      input.value = input.value.replace(/\D/g, '');
    });
  });

  const cityInput = document.getElementById('checkoutCity');
  if (cityInput) {
    cityInput.addEventListener('input', updateCheckoutTotal);
    cityInput.addEventListener('change', updateCheckoutTotal);
  }

  updateCheckoutTotal();
}

// Public product page
function prodTab(pane, btn) {
  document.querySelectorAll('.prod-tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.prod-tab-pane').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('prod-pane-' + pane)?.classList.add('active');
}

function prodQty(delta) {
  const inp = document.getElementById('prod-qty');
  if (!inp) return;
  const max = parseInt(inp.max || '99', 10);
  inp.value = Math.max(1, Math.min(max, parseInt(inp.value || '1', 10) + delta));
}

function prodAddCart(id, name) {
  if (!requireBuyerAction('Fitur keranjang')) return;
  const qty = parseInt(document.getElementById('prod-qty')?.value || '1', 10) || 1;
  addToCart(null, id, name, qty);
}

function prodBuyNow(id, name) {
  if (!requireBuyerAction('Fitur beli sekarang')) return;
  const qty = parseInt(document.getElementById('prod-qty')?.value || '1', 10) || 1;
  addToCart(null, id, name, qty, 'checkout');
}

// Seller products page
function openProductModal() {
  const modal = document.getElementById('productModal');
  if (!modal) return;
  modal.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeProductModal() {
  const modal = document.getElementById('productModal');
  if (!modal) return;
  modal.classList.remove('open');
  document.body.style.overflow = '';
}

function openAddModal() {
  const categorySelect = document.getElementById('formProductCategory');
  document.getElementById('modalTitle').textContent = 'Tambah Produk';
  document.getElementById('formProductId').value = '';
  document.getElementById('productForm').action = 'index.php?page=seller_products';
  document.getElementById('formProductName').value = '';
  document.getElementById('formProductCategory').value = categorySelect?.options[0]?.value || '';
  document.getElementById('formProductStatus').value = 'active';
  document.getElementById('formProductPrice').value = '';
  document.getElementById('formProductStock').value = '';
  document.getElementById('formProductImage').value = '';
  document.getElementById('formProductDescription').value = '';
  document.getElementById('formProductImageHelp').textContent = 'Akan menggunakan cover default jika dikosongkan.';
  document.getElementById('submitBtn').textContent = 'Tambah Buku';
  openProductModal();
}

function openEditModal(product) {
  document.getElementById('modalTitle').textContent = 'Edit Produk';
  document.getElementById('formProductId').value = product.id;
  document.getElementById('productForm').action = 'index.php?page=seller_products';
  document.getElementById('formProductName').value = product.name;
  document.getElementById('formProductCategory').value = product.category_id;
  document.getElementById('formProductStatus').value = product.status;
  document.getElementById('formProductPrice').value = product.price;
  document.getElementById('formProductStock').value = product.stock;
  document.getElementById('formProductImage').value = '';
  document.getElementById('formProductDescription').value = product.description || '';
  document.getElementById('formProductImageHelp').textContent = 'Kosongkan jika tidak ingin mengubah cover.';
  document.getElementById('submitBtn').textContent = 'Simpan Perubahan';
  openProductModal();
}

// Seller orders page
function openShippingModal(orderId, invoice) {
  const orderInput = document.getElementById('shippingOrderId');
  const invoiceLabel = document.getElementById('modalInvoice');
  const modal = document.getElementById('shippingModal');
  if (!orderInput || !invoiceLabel || !modal) return;
  orderInput.value = orderId;
  invoiceLabel.textContent = invoice;
  modal.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeShippingModal() {
  const modal = document.getElementById('shippingModal');
  if (!modal) return;
  modal.classList.remove('open');
  document.body.style.overflow = '';
}

function openDetailModal(order) {
  const statusEl = document.getElementById('detailStatus');
  const itemsEl = document.getElementById('detailItems');
  const modal = document.getElementById('orderDetailModal');
  if (!statusEl || !itemsEl || !modal) return;

  document.getElementById('detailInvoice').textContent = '#' + order.invoice;
  document.getElementById('detailBuyer').textContent = order.buyer;
  document.getElementById('detailDate').textContent = order.date;
  document.getElementById('detailReceipt').textContent = order.receipt || '-';
  document.getElementById('detailTotal').textContent = 'Rp ' + order.total;

  statusEl.textContent = order.status;
  statusEl.className = 'order-status-badge ' + order.statusClass;

  itemsEl.innerHTML = order.items.map(item => `
    <div style="display:flex;justify-content:space-between;align-items:center;background:#f8fafc;border-radius:10px;padding:10px 14px;">
      <div>
        <div style="font-size:13px;font-weight:700;color:var(--ink);">📚 ${item.name}</div>
        <div style="font-size:11.5px;color:#94a3b8;margin-top:2px;">Qty: ${item.qty}</div>
      </div>
      <div style="font-family:var(--font-serif);font-size:14px;font-weight:700;color:var(--ink);">Rp ${item.subtotal}</div>
    </div>
  `).join('');

  modal.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeDetailModal() {
  const modal = document.getElementById('orderDetailModal');
  if (!modal) return;
  modal.classList.remove('open');
  document.body.style.overflow = '';
}

// Buyer orders page
function openReviewModal(items) {
  const select = document.getElementById('reviewProductId');
  const overlay = document.getElementById('reviewOverlay');
  const modal = document.getElementById('reviewModal');
  if (!select || !overlay || !modal) return;

  select.innerHTML = '';
  items.forEach(item => {
    const opt = document.createElement('option');
    opt.value = item.product_id;
    opt.textContent = item.name;
    select.appendChild(opt);
  });

  overlay.classList.add('open');
  modal.style.opacity = '1';
  modal.style.pointerEvents = 'auto';
}

function closeReviewModal() {
  const overlay = document.getElementById('reviewOverlay');
  const modal = document.getElementById('reviewModal');
  if (!overlay || !modal) return;
  overlay.classList.remove('open');
  modal.style.opacity = '0';
  modal.style.pointerEvents = 'none';
}

function initSellerProductsPage() {
  const productModal = document.getElementById('productModal');
  if (!productModal) return;
  productModal.addEventListener('click', function(e) {
    if (e.target === this) closeProductModal();
  });
}

function initSellerOrdersPage() {
  const shippingModal = document.getElementById('shippingModal');
  if (shippingModal) {
    shippingModal.addEventListener('click', function(e) {
      if (e.target === this) closeShippingModal();
    });
  }

  const detailModal = document.getElementById('orderDetailModal');
  if (detailModal) {
    detailModal.addEventListener('click', function(e) {
      if (e.target === this) closeDetailModal();
    });
  }
}

function initSellerReportsPage() {
  if (!document.getElementById('page-seller-report')) return;
  const triggers = document.querySelectorAll('.graph-trigger');
  const tooltip = document.getElementById('chart-tooltip');
  const ttLabel = document.getElementById('tt-label');
  const ttRev = document.getElementById('tt-rev');
  const ttOrd = document.getElementById('tt-ord');
  const svgContainer = document.querySelector('.svg-container');

  if (!triggers.length || !tooltip || !svgContainer || !ttLabel || !ttRev || !ttOrd) return;

  triggers.forEach(trigger => {
    trigger.addEventListener('mousemove', e => {
      const rect = svgContainer.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;

      ttLabel.textContent = trigger.getAttribute('data-label') || '';
      ttRev.textContent = trigger.getAttribute('data-rev') || '';
      ttOrd.textContent = trigger.getAttribute('data-ord') || '';

      tooltip.style.left = x + 'px';
      tooltip.style.top = y + 'px';
      tooltip.style.display = 'block';
    });

    trigger.addEventListener('mouseleave', () => {
      tooltip.style.display = 'none';
    });
  });
}

function initBuyerCartPage() {
  if (!document.getElementById('page-buyer_cart')) return;
  const selectAll = document.getElementById('cart-select-all');
  const itemChecks = Array.from(document.querySelectorAll('.cart-item-select'));
  const totalEl = document.getElementById('cart-selected-total');
  const checkoutBtn = document.getElementById('cart-checkout-btn');
  if (!itemChecks.length) return;

  function rupiah(value) {
    return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
  }

  function syncCartSummary() {
    let total = 0;
    let selected = 0;

    itemChecks.forEach(check => {
      const item = check.closest('.cart-shop-item');
      if (!item || !check.checked) return;
      total += Number(item.dataset.price || 0) * Number(item.dataset.qty || 0);
      selected++;
    });

    if (totalEl) totalEl.textContent = selected ? rupiah(total) : '-';
    if (checkoutBtn) checkoutBtn.classList.toggle('disabled', selected === 0);
    if (selectAll) selectAll.checked = selected === itemChecks.length;
  }

  if (selectAll) {
    selectAll.addEventListener('change', () => {
      itemChecks.forEach(check => {
        check.checked = selectAll.checked;
      });
      syncCartSummary();
    });
  }

  itemChecks.forEach(check => check.addEventListener('change', syncCartSummary));
  syncCartSummary();
}

function initAdminAnalyticsPage() {
  if (!document.getElementById('page-admin_analytics')) return;
  document.addEventListener('click', function(e) {
    if (e.target.closest('[onclick*="nextElementSibling"]')) return;
    document.querySelectorAll('#page-admin_analytics div[style*="position:absolute"]').forEach(dropdown => {
      if (dropdown.style.display === 'block') dropdown.style.display = 'none';
    });
  });
}

function initMigratedPageScripts() {
  initCheckoutPage();
  initSellerProductsPage();
  initSellerOrdersPage();
  initSellerReportsPage();
  initBuyerCartPage();
  initAdminAnalyticsPage();
}

document.addEventListener('DOMContentLoaded', initMigratedPageScripts);
