# RubbyBooks - Aplikasi E-Commerce Buku

RubbyBooks adalah platform e-commerce khusus untuk jual beli buku baru maupun bekas yang mempertemukan pembeli dan penjual secara langsung.

## Arsitektur & Teknologi

*   **Frontend:** HTML5, CSS3 murni (Native) terstruktur, Vanilla JavaScript
*   **Backend:** PHP 8 (Native) dengan arsitektur MVC (Model-View-Controller)
*   **Database:** MySQL (minimum 8 tabel spesifikasi, total 13 tabel pada implementasi aktual)
*   **Security:** Password Hashing (Bcrypt), proteksi SQL Injection dengan PDO Prepared Statements, validasi file upload

## Skema 4 Role User

1.  **Pembeli (Buyer):** Dapat melakukan registrasi, mencari buku, menambah ke wishlist/keranjang belanja, melakukan checkout, upload bukti bayar, melacak pesanan, hingga memberikan ulasan.
2.  **Penjual (Seller):** Dapat mengelola inventaris toko/produk, memantau riwayat pesanan masuk, meng-update status pesanan (termasuk nomor resi pengiriman), memantau performa penjualan lewat dashboard, dan merespons ulasan pembeli.
3.  **Admin:** Bertanggung jawab memonitor sistem. Mengelola daftar user (blokir/terima registrasi penjual), mengelola kategori, serta memantau ringkasan statistik (pendapatan kotor platform, pesanan selesai, dan log aktivitas).
4.  **System Automation:** Berjalan di latar belakang untuk melakukan penyesuaian otomatis seperti pembaruan kuantitas/stok, perhitungan harga ongkos kirim (berdasarkan kota simulasi), auto-generate nomor faktur/invoice secara sistem, dan logging aktivitas/notifikasi.

## Cara Instalasi di Localhost (Laragon/XAMPP)

1.  Tempatkan folder `Nanas` (atau nama folder proyek ini) di dalam direktori `www` (Laragon) atau `htdocs` (XAMPP).
2.  Buat database baru di MySQL dengan nama `rubbybooks`.
3.  Lakukan import file `database/database.sql` ke dalam database `rubbybooks` yang baru dibuat.
4.  Pastikan detail koneksi di `src/config/database.php` sesuai dengan environment Anda (default: root tanpa password).
5.  Akses aplikasi melalui browser, misalnya `http://localhost/Nanas/`.
