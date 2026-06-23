// ══════════════════════════════════════════
//  STATE
// ══════════════════════════════════════════
let cartCount = 3;
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
      { icon:'🏠', label:'Dashboard Pembeli', action:"_showPageDirect('buyer');closeUserDropdown()" },
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
    showSearch:   false,
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
    showSearch:   false,
    dropdown: [
      { icon:'🖥️', label:'Panel Admin',     action:"_showPageDirect('admin');closeUserDropdown()" },
      { icon:'👥', label:'Manajemen User',  action:"_showPageDirect('admin_users');closeUserDropdown()" },
      { icon:'🏷️', label:'Kategori',        action:"_showPageDirect('admin_categories');closeUserDropdown()" },
      { divider: true },
      { icon:'⚙️', label:'Pengaturan Sistem', action:"_showPageDirect('admin_settings');closeUserDropdown()" },
      { divider: true },
      { icon:'🚪', label:'Keluar',          action:'doLogout()', danger: true }
    ]
  }
};

// ══════════════════════════════════════════
//  ROLE-BASED ACCESS CONTROL (RBAC)
// ══════════════════════════════════════════

// Pages each role is ALLOWED to access
const PAGE_ACCESS = {
  guest:  ['home', 'catalog'],
  buyer:  ['home', 'catalog', 'checkout', 'tracking', 'buyer', 'buyer_account', 'buyer_wishlist', 'buyer_cart', 'buyer_orders', 'buyer_reviews', 'buyer_notifications', 'cart', 'account_settings'],
  seller: ['home', 'catalog', 'seller', 'seller_products', 'seller_orders', 'seller_reviews', 'seller_notifications', 'seller_report', 'account_settings'],
  admin:  ['home', 'catalog', 'admin', 'admin_users', 'admin_categories', 'admin_notifications', 'account_settings', 'admin_settings']
};


// Human-readable page names for error messages
const PAGE_NAMES = {
  home:     'Beranda',
  catalog:  'Katalog Buku',
  checkout: 'Checkout',
  tracking: 'Lacak Pesanan',
  account_settings: 'Pengaturan Akun',
  buyer:    'Dashboard Pembeli',
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
  admin_categories:     'Manajemen Kategori',
  admin_notifications:  'Notifikasi Admin',
  admin_settings:       'Pengaturan Sistem'
};

function canAccess(pageName) {
  const role = currentUser ? currentUser.role : 'guest';
  return (PAGE_ACCESS[role] || []).includes(pageName);
}

// ══════════════════════════════════════════
//  PAGE NAVIGATION  (with RBAC guard)
// ══════════════════════════════════════════
function showPage(name) {
  if (!canAccess(name)) {
    handleAccessDenied(name);
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
  if (user.role === 'buyer') return user.name.split(' ')[0] || cfg.chipLabel;
  return cfg.chipLabel;
}

function applyNavUser(user) {
  if (!user || !ROLE_CONFIG[user.role]) return;
  currentUser = user;
  const cfg = ROLE_CONFIG[user.role];

  document.getElementById('nav-guest').style.display = 'none';
  document.getElementById('nav-loggedin').style.display = 'block';

  const avatar = document.getElementById('nav-avatar-icon');
  avatar.textContent = cfg.roleIcon;
  avatar.className = 'nav-user-avatar nav-user-avatar--role';
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

  // Always show nav-center; hide all groups first
  navCenter.style.display = 'flex';
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
    return `<button class="dropdown-item${item.danger ? ' danger' : ''}" onclick="${item.action}"><span>${item.icon}</span> ${item.label}</button>`;
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
  window.location.href = 'index.php?page=logout';
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

// ══════════════════════════════════════════
//  ADD TO CART
// ══════════════════════════════════════════
function addToCart(e, productId, name) {
  e.stopPropagation();
  // Only buyers (and guests who will be prompted to login) can add to cart
  if (currentUser && currentUser.role !== 'buyer') {
    showToast('⛔ Fitur keranjang hanya tersedia untuk Pembeli');
    return;
  }
  if (!currentUser) {
    showToast('🔐 Masuk sebagai Pembeli untuk menambahkan ke keranjang');
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
  const text = el.textContent.trim().replace(/^[^\s]+ /, ''); // hapus icon
  if (text === 'Semua') {
    window.location.href = 'index.php?page=catalog';
  } else {
    // Ideally we would map names to IDs, but for a simple fix, we'll search by query for now,
    // or just pass a special param. Since we don't know the ID exactly from the UI text, 
    // we can trigger a search.
    window.location.href = 'index.php?page=catalog&q=' + encodeURIComponent(text);
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
  // Global search
  const searchInput = document.querySelector('#nav-search input');
  if (searchInput) {
    searchInput.addEventListener('keydown', e => {
      if (e.key === 'Enter') {
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