# Laporan Pengujian (Testing Report) - RubbyBooks

Dokumen ini berisi panduan skenario pengujian aplikasi e-commerce RubbyBooks beserta daftar akun *dummy* yang telah disiapkan untuk melakukan pengetesan.

## 1. Daftar Akun Uji Coba (Dummy Accounts)

Semua akun ini menggunakan password yang sama:
**Password:** `password`

| Role | Nama | Email | Deskripsi |
| :--- | :--- | :--- | :--- |
| **Admin** | Budi Admin | `budiadmin@gmail.com` | Memiliki akses penuh ke sistem (dashboard admin, kelola user, dll). |
| **Penjual (Seller)** | Toko Buku Cahaya | `tokobukucahaya@gmail.com` | Bisa mengelola produk (4 buku default). |
| **Penjual (Seller)** | Buku Keigo Official | `keigoofficial@gmail.com` | Menjual buku-buku karya Keigo Higashino. |
| **Penjual (Seller)** | Literasi Jaya | `literasijaya@gmail.com` | Akun penjual tambahan. |
| **Pembeli (Buyer)** | Putri Lestari | `putrilestari@gmail.com` | Pembeli utama. |
| **Pembeli (Buyer)** | Rina Amelia | `rinaamelia@gmail.com` | Pembeli tambahan. |
| **Pembeli (Buyer)** | Dimas Prasetyo | `dimasprasetyo@gmail.com` | Pembeli tambahan. |

## 2. Skenario Pengujian Fungsional (Functional Testing)

Berikut adalah urutan *flow* yang harus Anda uji untuk membuktikan bahwa seluruh fungsionalitas berjalan dengan baik sesuai permintaan tugas.

### A. Pengujian Pembeli (Buyer Flow)
1. **Login & Registrasi:**
   - Coba register akun baru sebagai pembeli.
   - Login menggunakan akun `buyer@rubbybooks.test`.
2. **Katalog & Wishlist:**
   - Masuk ke halaman **Katalog**.
   - Coba lakukan pencarian dan gunakan filter kategori/harga.
   - Klik ikon "♡" untuk menambahkan buku ke **Wishlist**.
   - Buka halaman Wishlist dan pastikan buku yang disukai muncul.
3. **Keranjang Belanja (Cart) & Checkout:**
   - Klik tombol **+ Keranjang** pada beberapa buku.
   - Buka **Keranjang Belanja**, coba *update quantity* (tambah/kurang) dan hapus item.
   - Lanjutkan ke **Checkout**.
   - Isi form alamat pengiriman, pilih metode pembayaran, dan klik submit.
   - Upload bukti pembayaran (gambar/foto dummy).
4. **Order Tracking & Review:**
   - Buka halaman **Pesanan Saya**, cek pesanan yang berstatus *Pending / Menunggu Pembayaran*.
   - Jika pesanan sudah diselesaikan oleh Penjual (status *Delivered/Selesai*), berikan **Rating & Ulasan**.

### B. Pengujian Penjual (Seller Flow)
1. **Dashboard & Manajemen Produk:**
   - Login menggunakan akun `seller@rubbybooks.test`.
   - Buka menu **Produk**. Coba tambahkan produk baru (upload gambar buku, isi harga, stok).
   - Edit produk dan coba ubah ketersediaan stok atau harga.
2. **Manajemen Pesanan:**
   - Masuk ke menu **Pesanan**. 
   - Cari pesanan yang baru saja dibuat oleh pembeli.
   - Ubah status pesanan menjadi **Diproses (Processing)**, lalu menjadi **Dikirim (Shipped)** sambil memasukkan Nomor Resi.
3. **Laporan & Review:**
   - Buka menu **Laporan Penjualan**, pastikan grafik dan perhitungan pendapatan tampil.
   - Buka menu **Ulasan (Review)**, cek apakah ulasan dari pembeli masuk dan coba untuk memberikan balasan (*reply*).

### C. Pengujian Admin
1. **Manajemen Pengguna:**
   - Login menggunakan akun `admin@rubbybooks.test`.
   - Buka menu **Pengguna**. Pastikan admin dapat melihat seluruh daftar akun yang ada.
   - Coba daftar akun penjual (*seller*) baru menggunakan form registrasi di beranda.
   - Admin perlu memberikan persetujuan (Approve) agar *seller* baru bisa mulai berjualan.
2. **Sistem Notifikasi & Log:**
   - Pastikan terdapat notifikasi sistem (seperti ketika ada pengguna baru atau ketika status order diupdate).

## 3. Sistem Otomatisasi (System Automation)
1. **Penurunan Stok Otomatis:** Ketika pesanan berhasil di-*checkout*, pastikan jumlah ketersediaan stok produk akan berkurang.
2. **Perhitungan Ongkos Kirim:** Ongkir akan diproses secara otomatis saat *checkout* berdasarkan opsi kota/lokasi yang dipilih.
3. **Pembuatan Invoice & Notifikasi:** Invoice order ter- *generate* otomatis (contoh: `INV-2026xxxx`), dan notifikasi terkirim baik untuk pembeli (notifikasi berhasil order) maupun penjual (pesanan masuk).
