<?php
require 'config/database.php';

// Empty the products table for a fresh start
$pdo->query('SET FOREIGN_KEY_CHECKS = 0');
$pdo->query('TRUNCATE TABLE products');
$pdo->query('TRUNCATE TABLE order_items');
$pdo->query('TRUNCATE TABLE reviews');
$pdo->query('TRUNCATE TABLE carts');
$pdo->query('TRUNCATE TABLE wishlists');
$pdo->query('SET FOREIGN_KEY_CHECKS = 1');

$cats = $pdo->query('SELECT * FROM categories')->fetchAll(PDO::FETCH_ASSOC);

$booksData = [
    'Novel' => [
        'name' => 'Bumi Manusia',
        'desc' => 'Sebuah roman historis yang menggambarkan kehidupan Minke di era kolonial Belanda. Novel pertama dari Tetralogi Buru karya Pramoedya Ananta Toer yang menghadirkan kisah cinta mengharukan dan perjuangan hak asasi manusia.',
        'price' => 125000
    ],
    'Komik' => [
        'name' => 'Naruto Vol. 1',
        'desc' => 'Komik aksi petualangan ninja karya Masashi Kishimoto. Mengisahkan perjalanan Uzumaki Naruto yang bercita-cita menjadi Hokage, pemimpin desa Konoha.',
        'price' => 45000
    ],
    'Teknologi' => [
        'name' => 'Clean Code: A Handbook of Agile Software Craftsmanship',
        'desc' => 'Buku panduan legendaris karya Robert C. Martin (Uncle Bob) untuk menulis kode yang bersih, mudah dibaca, dan mudah di-maintain.',
        'price' => 350000
    ],
    'Bisnis' => [
        'name' => 'Rich Dad Poor Dad',
        'desc' => 'Buku panduan literasi keuangan karya Robert T. Kiyosaki yang membandingkan pola pikir finansial antara "Ayah Kaya" dan "Ayah Miskin".',
        'price' => 95000
    ],
    'Agama' => [
        'name' => 'La Tahzan (Jangan Bersedih)',
        'desc' => 'Buku motivasi Islam karya Dr. Aidh al-Qarni yang mengajak pembacanya untuk selalu optimis dan tidak berputus asa dalam menghadapi cobaan hidup.',
        'price' => 110000
    ],
    'Sejarah' => [
        'name' => 'Sapiens: Riwayat Singkat Umat Manusia',
        'desc' => 'Buku sejarah manusia karya Yuval Noah Harari yang membahas evolusi spesies Homo Sapiens sejak Zaman Batu hingga abad ke-21.',
        'price' => 185000
    ]
];

$seller = $pdo->query("SELECT id FROM users WHERE role='seller' LIMIT 1")->fetchColumn();
if (!$seller) {
    $seller = $pdo->query("SELECT id FROM users LIMIT 1")->fetchColumn();
}

$count = 0;
$stmt = $pdo->prepare("INSERT INTO products (seller_id, category_id, name, description, price, stock, status, book_condition) VALUES (?, ?, ?, ?, ?, 25, 'active', 'new')");

foreach ($cats as $c) {
    $catName = $c['name'];
    if (isset($booksData[$catName])) {
        $b = $booksData[$catName];
        $stmt->execute([$seller, $c['id'], $b['name'], $b['desc'], $b['price']]);
        $count++;
    }
}

echo "Inserted $count real books for pitching.";
