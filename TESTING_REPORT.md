# Laporan Pengujian (Testing Report) - RubbyBooks

Dokumen ini berisi panduan skenario pengujian aplikasi e-commerce RubbyBooks beserta daftar akun _dummy_ yang telah disiapkan untuk melakukan pengetesan.

## 1. Daftar Akun Uji Coba (Dummy Accounts)

Semua akun ini menggunakan password yang sama:
**Password:** `password`

| Role                 | Nama                | Email                       | Deskripsi                                                           |
| :------------------- | :------------------ | :-------------------------- | :------------------------------------------------------------------ |
| **Admin**            | Admin Demo          | `admindemo@rubbybooks.com`  | Memiliki akses penuh ke sistem (dashboard admin, kelola user, dll). |
| **Penjual (Seller)** | Seller Demo         | `sellerdemo@rubbybooks.com` | Akun demo untuk manajemen pesanan dan produk.                       |
| **Penjual (Seller)** | Buku Keigo Official | `keigoofficial@gmail.com`   | Menjual buku-buku karya Keigo Higashino.                            |
| **Penjual (Seller)** | Literasi Jaya       | `literasijaya@gmail.com`    | Akun penjual tambahan.                                              |
| **Pembeli (Buyer)**  | Buyer Demo          | `buyerdemo@rubbybooks.com`  | Akun demo utama untuk pembeli.                                      |
| **Pembeli (Buyer)**  | Nasyla Putri        | `nasylaputri@gmail.com`     | Pembeli tambahan.                                                   |
| **Pembeli (Buyer)**  | Dimas Prasetyo      | `dimasprasetyo@gmail.com`   | Pembeli tambahan.                                                   |

## 2. Skenario Pengujian Fungsional (Functional Testing)

Berikut adalah urutan _flow_ yang harus Anda uji untuk membuktikan bahwa seluruh fungsionalitas berjalan dengan baik sesuai permintaan tugas.

### A. Pengujian Pembeli (Buyer Flow)

1. **Login & Registrasi:**
   - Coba register akun baru sebagai pembeli.
   - Login menggunakan akun `buyerdemo@rubbybooks.com`.
2. **Katalog & Wishlist:**
   - Masuk ke halaman **Katalog**.
   - Coba lakukan pencarian dan gunakan filter kategori/harga.
   - Klik ikon "♡" untuk menambahkan buku ke **Wishlist**.
   - Buka halaman Wishlist dan pastikan buku yang disukai muncul.
3. **Keranjang Belanja (Cart) & Checkout:**
   - Klik tombol **+ Keranjang** pada beberapa buku.
   - Buka **Keranjang Belanja**, coba _update quantity_ (tambah/kurang) dan hapus item.
   - Lanjutkan ke **Checkout**.
   - Isi form alamat pengiriman, pilih metode pembayaran, dan klik submit.
   - Upload bukti pembayaran (gambar/foto dummy).
4. **Order Tracking & Review:**
   - Buka halaman **Pesanan Saya**, cek pesanan yang berstatus _Pending / Menunggu Pembayaran_.
   - Jika pesanan sudah diselesaikan oleh Penjual (status _Delivered/Selesai_), berikan **Rating & Ulasan**.

### B. Pengujian Penjual (Seller Flow)

1. **Dashboard & Manajemen Produk:**
   - Login menggunakan akun `sellerdemo@rubbybooks.com`.
   - Buka menu **Produk**. Coba tambahkan produk baru (upload gambar buku, isi harga, stok).
   - Edit produk dan coba ubah ketersediaan stok atau harga.
2. **Manajemen Pesanan:**
   - Masuk ke menu **Pesanan**.
   - Cari pesanan yang baru saja dibuat oleh pembeli.
   - Ubah status pesanan menjadi **Diproses (Processing)**, lalu menjadi **Dikirim (Shipped)** sambil memasukkan Nomor Resi.
3. **Laporan & Review:**
   - Buka menu **Laporan Penjualan**, pastikan grafik dan perhitungan pendapatan tampil.
   - Buka menu **Ulasan (Review)**, cek apakah ulasan dari pembeli masuk dan coba untuk memberikan balasan (_reply_).

### C. Pengujian Admin

1. **Manajemen Pengguna:**
   - Login menggunakan akun `admindemo@rubbybooks.com`.
   - Buka menu **Pengguna**. Pastikan admin dapat melihat seluruh daftar akun yang ada.
   - Coba daftar akun penjual (_seller_) baru menggunakan form registrasi di beranda.
   - Admin perlu memberikan persetujuan (Approve) agar _seller_ baru bisa mulai berjualan.
2. **Sistem Notifikasi & Log:**
   - Pastikan terdapat notifikasi sistem (seperti ketika ada pengguna baru atau ketika status order diupdate).

## 3. Sistem Otomatisasi (System Automation)

1. **Penurunan Stok Otomatis:** Ketika pesanan berhasil di-_checkout_, pastikan jumlah ketersediaan stok produk akan berkurang.
2. **Perhitungan Ongkos Kirim:** Ongkir akan diproses secara otomatis saat _checkout_ berdasarkan opsi kota/lokasi yang dipilih.
3. **Pembuatan Invoice & Notifikasi:** Invoice order ter- _generate_ otomatis (contoh: `INV-2026xxxx`), dan notifikasi terkirim baik untuk pembeli (notifikasi berhasil order) maupun penjual (pesanan masuk).
