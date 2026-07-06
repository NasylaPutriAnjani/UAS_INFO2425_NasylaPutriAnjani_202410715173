CREATE DATABASE IF NOT EXISTS rubbybooks CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rubbybooks;

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS system_logs, seller_verifications, notifications, reviews, payments, order_items, orders, carts, shipping_addresses, products, categories, users, wishlists, system_settings;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('buyer','seller','admin') NOT NULL DEFAULT 'buyer',
  status ENUM('active','pending','banned','suspended') NOT NULL DEFAULT 'active',
  avatar VARCHAR(255) NULL,
  phone VARCHAR(40) NULL,
  dob DATE NULL,
  alternate_phone VARCHAR(40) NULL,
  delete_requested TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description TEXT NULL
);

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  seller_id INT NOT NULL,
  category_id INT NOT NULL,
  name VARCHAR(160) NOT NULL,
  description TEXT,
  price INT NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  book_condition ENUM('new', 'used_good', 'used_fair') NOT NULL DEFAULT 'new',
  image VARCHAR(255),
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE carts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  buyer_id INT NOT NULL,
  product_id INT NOT NULL,
  qty INT NOT NULL DEFAULT 1,
  UNIQUE KEY buyer_product (buyer_id, product_id),
  FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE shipping_addresses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  buyer_id INT NOT NULL,
  recipient_name VARCHAR(120) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  address TEXT NOT NULL,
  city VARCHAR(80) NOT NULL,
  postal_code VARCHAR(20) NOT NULL,
  FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  buyer_id INT NOT NULL,
  shipping_address_id INT NULL,
  invoice_number VARCHAR(40) NOT NULL UNIQUE,
  total INT NOT NULL,
  shipping_cost INT NOT NULL DEFAULT 0,
  receipt_number VARCHAR(80) NULL,
  status ENUM('pending','paid','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (buyer_id) REFERENCES users(id),
  FOREIGN KEY (shipping_address_id) REFERENCES shipping_addresses(id)
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  qty INT NOT NULL,
  price INT NOT NULL,
  subtotal INT NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  method VARCHAR(80) NOT NULL,
  proof VARCHAR(255),
  status ENUM('waiting','accepted','rejected') NOT NULL DEFAULT 'waiting',
  paid_at TIMESTAMP NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

CREATE TABLE reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  buyer_id INT NOT NULL,
  rating TINYINT NOT NULL,
  comment TEXT,
  seller_reply TEXT NULL,
  photo VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  message TEXT NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE seller_verifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  seller_id INT NOT NULL,
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  approved_by INT NULL,
  approved_at TIMESTAMP NULL,
  FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE wishlists (
  id INT AUTO_INCREMENT PRIMARY KEY,
  buyer_id INT NOT NULL,
  product_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY user_product (buyer_id, product_id),
  FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE system_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  activity TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE system_settings (
  `key` VARCHAR(80) NOT NULL PRIMARY KEY,
  `value` TEXT NOT NULL
);

INSERT INTO system_settings (`key`, `value`) VALUES
('currency', 'IDR'),
('timezone', 'Asia/Jakarta'),
('min_order', '50000'),
('ppn_rate', '11'),
('ppn_included', '0'),
('low_stock_alert', '1'),
('low_stock_threshold', '10'),
('show_stock_display', '1');

INSERT INTO users (name,email,password,role,status) VALUES
('Budi Admin','budiadmin@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin','active'),
('Toko Buku Cahaya','tokobukucahaya@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','seller','active'),
('Putri Lestari','putrilestari@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','buyer','active'),
('Buku Keigo Official','keigoofficial@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','seller','active'),
('Literasi Jaya','literasijaya@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','seller','active'),
('Rina Amelia','rinaamelia@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','buyer','active'),
('Dimas Prasetyo','dimasprasetyo@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','buyer','active');

INSERT INTO seller_verifications (seller_id,status,approved_by,approved_at) VALUES 
(2,'approved',1,NOW()),
(4,'approved',1,NOW()),
(5,'approved',1,NOW());

INSERT INTO categories (name,description) VALUES
('Novel','Fiksi, roman, dan sastra populer'),('Komik','Komik lokal dan manga'),('Teknologi','Pemrograman, data, dan digital'),
('Bisnis','Bisnis, karier, dan finansial'),('Agama','Kajian dan spiritualitas'),('Sejarah','Sejarah Indonesia dan dunia');

INSERT INTO products (seller_id,category_id,name,description,price,stock,status) VALUES
(2,3,'Atomic Habits','Panduan membangun kebiasaan kecil yang berdampak besar.',89000,20,'active'),
(2,1,'Laskar Pelangi','Novel inspiratif karya Andrea Hirata.',65000,15,'active'),
(2,4,'Rich Dad Poor Dad','Buku finansial personal populer.',75000,12,'active'),
(2,1,'The Midnight Library','Novel tentang kesempatan kedua dan pilihan hidup.',95000,9,'active'),
(4,1,'Keajaiban Toko Kelontong Namiya','Novel best-seller karya Keigo Higashino yang penuh dengan misteri dan pesan moral yang menyentuh.',85000,10,'active'),
(4,1,'Kesetiaan Mr. X (The Devotion of Suspect X)','Kisah investigasi pembunuhan yang penuh teka-teki dan intrik psikologis yang brilian oleh Keigo Higashino.',78000,5,'active');
