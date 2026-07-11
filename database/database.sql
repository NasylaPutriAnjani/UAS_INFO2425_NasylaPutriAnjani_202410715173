-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: rubbybooks
-- ------------------------------------------------------
-- Server version	8.4.3

CREATE DATABASE IF NOT EXISTS `rubbybooks` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `rubbybooks`;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `buyer_id` int NOT NULL,
  `product_id` int NOT NULL,
  `qty` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `buyer_product` (`buyer_id`,`product_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
INSERT INTO `carts` VALUES (3,7,6,1),(4,7,5,1),(10,6,7,2);
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Novel','Fiksi, roman, dan sastra populer'),(2,'Komik','Komik lokal dan manga'),(3,'Teknologi','Pemrograman, data, dan digital'),(4,'Bisnis','Bisnis, karier, dan finansial'),(5,'Agama','Kajian dan spiritualitas'),(6,'Sejarah','Sejarah Indonesia dan dunia');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,3,'Pesanan demo Anda telah selesai dan dapat ditampilkan di presentasi.',0,'2026-07-11 06:01:57'),(2,2,'Anda menerima review demo dari Buyer Demo pada produk Laskar Pelangi.',1,'2026-07-11 06:01:57'),(3,7,'Pesanan INV-20260711-002 dibuat. Status pending.',0,'2026-07-11 06:41:40'),(4,6,'Pesanan INV-20260711-003 dibuat dan bukti pembayaran terkirim. Menunggu konfirmasi penjual.',0,'2026-07-11 08:09:44'),(5,4,'Pesanan masuk INV-20260711-003: Malice x1. Menunggu konfirmasi penjual.',0,'2026-07-11 08:28:03');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `qty` int NOT NULL,
  `price` int NOT NULL,
  `subtotal` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,1,1,65000,65000),(2,2,1,1,65000,65000),(3,2,6,1,115000,115000),(4,3,7,1,74000,74000);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `buyer_id` int NOT NULL,
  `shipping_address_id` int DEFAULT NULL,
  `invoice_number` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` int NOT NULL,
  `shipping_cost` int NOT NULL DEFAULT '0',
  `receipt_number` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','paid','processing','shipped','delivered','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `buyer_id` (`buyer_id`),
  KEY `shipping_address_id` (`shipping_address_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`),
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`shipping_address_id`) REFERENCES `shipping_addresses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,3,1,'INV-20260711-001',80000,15000,'REG-DEMO-20260711','delivered','2026-07-11 06:01:57'),(2,7,2,'INV-20260711-002',190000,10000,'rreg-123123','delivered','2026-07-11 06:41:40'),(3,6,3,'INV-20260711-003',99000,25000,'reg-12057815','shipped','2026-07-11 08:09:44');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `method` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `proof` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('waiting','accepted','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `paid_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,1,'Transfer Bank BCA','uploads/payments/demo-payment.jpg','accepted','2026-07-11 06:01:57'),(2,2,'Transfer Bank','uploads/payments/rb_6a51e5a40e1f95.10542019.png','waiting',NULL),(3,3,'E-Wallet','uploads/payments/rb_6a51fa4828ed20.45992228.jpg','waiting',NULL);
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `seller_id` int NOT NULL,
  `category_id` int NOT NULL,
  `name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` int NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `book_condition` enum('new','used_good','used_fair') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `seller_id` (`seller_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,2,1,'Laskar Pelangi','Novel inspiratif karya Andrea Hirata tentang mimpi, pendidikan, dan persahabatan.',65000,17,'new','uploads/products/rb_6a51e804cb8315.88536020.jpg','active','2026-07-11 06:01:57'),(2,2,2,'Naruto, Vol. 1: Uzumaki Naruto','Manga pembuka kisah Naruto karya Masashi Kishimoto.',55000,25,'new','uploads/products/rb_6a51e8184136e9.77046482.jpg','active','2026-07-11 06:01:57'),(3,2,3,'Clean Code: A Handbook of Agile Software Craftsmanship','Panduan klasik untuk menulis kode yang bersih, mudah dibaca, dan terawat.',125000,14,'new','uploads/products/rb_6a51e82deb3525.47685094.jpg','active','2026-07-11 06:01:57'),(4,2,4,'Rich Dad Poor Dad','Buku finansial populer tentang pola pikir dan literasi keuangan.',75000,20,'new','uploads/products/rb_6a51e83cb85b70.16165874.jpg','active','2026-07-11 06:01:57'),(5,2,5,'The Purpose Driven Life','Buku rohani populer tentang tujuan hidup dan refleksi spiritual.',99000,12,'new','uploads/products/rb_6a51e84a5b4674.13541459.jpg','active','2026-07-11 06:01:57'),(6,2,6,'Sapiens: A Brief History of Humankind','Buku sejarah populer yang merangkum perjalanan peradaban manusia.',115000,9,'new','uploads/products/rb_6a51e7d62edc62.95640556.jpg','active','2026-07-11 06:01:57'),(7,4,1,'Malice','Novelis laris Hidaka Kunihiko ditemukan tewas di rumahnya pada malam sebelum ia meninggalkan Jepang untuk pindah ke Kanada. Tubuhnya ditemukan di ruang kerjanya yang terkunci di rumahnya yang juga terkunci oleh istri dan sahabatnya. Keduanya punya alibi kuat. Mungkin.',74000,11,'new','uploads/products/rb_6a521d2e40d6e7.40130141.jpg','active','2026-07-11 07:12:07');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `buyer_id` int NOT NULL,
  `rating` tinyint NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `seller_reply` text COLLATE utf8mb4_unicode_ci,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `buyer_id` (`buyer_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES (1,1,3,5,'Cerita yang hangat, inspiratif, dan sangat cocok untuk presentasi demo aplikasi.','Terima kasih atas ulasannya!',NULL,'2026-07-11 06:01:57'),(2,1,7,5,'mantapp jos gandos','anjay mabar',NULL,'2026-07-11 06:54:41');
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seller_verifications`
--

DROP TABLE IF EXISTS `seller_verifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seller_verifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `seller_id` int NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by` int DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `seller_id` (`seller_id`),
  KEY `approved_by` (`approved_by`),
  CONSTRAINT `seller_verifications_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `seller_verifications_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seller_verifications`
--

LOCK TABLES `seller_verifications` WRITE;
/*!40000 ALTER TABLE `seller_verifications` DISABLE KEYS */;
INSERT INTO `seller_verifications` VALUES (1,2,'approved',1,'2026-07-11 06:01:57'),(2,4,'approved',1,'2026-07-11 06:01:57'),(3,5,'approved',1,'2026-07-11 06:01:57'),(4,8,'pending',NULL,NULL),(5,9,'pending',NULL,NULL),(6,10,'pending',NULL,NULL);
/*!40000 ALTER TABLE `seller_verifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_addresses`
--

DROP TABLE IF EXISTS `shipping_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipping_addresses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `buyer_id` int NOT NULL,
  `recipient_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `buyer_id` (`buyer_id`),
  CONSTRAINT `shipping_addresses_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_addresses`
--

LOCK TABLES `shipping_addresses` WRITE;
/*!40000 ALTER TABLE `shipping_addresses` DISABLE KEYS */;
INSERT INTO `shipping_addresses` VALUES (1,3,'Buyer Demo RubbyBooks','081234567890','Jl. Merdeka No. 10, Bandung','Bandung','40111'),(2,7,'Rafli Aryadika','085171076449','Jl. Perjuangan','jakarta','17121'),(3,6,'Nasyla Putri','082110201926','jl mekarsari kampung mede','Kota Bekasi','17121');
/*!40000 ALTER TABLE `shipping_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_logs`
--

DROP TABLE IF EXISTS `system_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `activity` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_logs`
--

LOCK TABLES `system_logs` WRITE;
/*!40000 ALTER TABLE `system_logs` DISABLE KEYS */;
INSERT INTO `system_logs` VALUES (1,'Login seller: keigoofficial@gmail.com','2026-07-11 06:07:49'),(2,'Login seller: keigoofficial@gmail.com','2026-07-11 06:07:49'),(3,'Login seller: keigoofficial@gmail.com','2026-07-11 06:07:49'),(4,'Login seller: keigoofficial@gmail.com','2026-07-11 06:16:05'),(5,'Login admin: admindemo@rubbybooks.com','2026-07-11 06:16:15'),(6,'Login seller: sellerdemo@rubbybooks.com','2026-07-11 06:16:58'),(7,'Login admin: admindemo@rubbybooks.com','2026-07-11 06:18:55'),(8,'Login admin: admindemo@rubbybooks.com','2026-07-11 06:18:55'),(9,'Admin memperbarui pengaturan sistem.','2026-07-11 06:19:02'),(10,'Admin memperbarui pengaturan sistem.','2026-07-11 06:19:18'),(11,'Login admin: admindemo@rubbybooks.com','2026-07-11 06:22:16'),(12,'Login buyer: rinaamelia@gmail.com','2026-07-11 06:29:22'),(13,'Login buyer: rinaamelia@gmail.com','2026-07-11 06:29:22'),(14,'Login seller: sellerdemo@rubbybooks.com','2026-07-11 06:29:40'),(15,'Login buyer: rinaamelia@gmail.com','2026-07-11 06:29:57'),(16,'Login seller: keigoofficial@gmail.com','2026-07-11 06:31:54'),(17,'Login buyer: dimasprasetyo@gmail.com','2026-07-11 06:33:52'),(18,'Login buyer: dimasprasetyo@gmail.com','2026-07-11 06:33:52'),(19,'Checkout invoice INV-20260711-002','2026-07-11 06:41:40'),(20,'Login seller: keigoofficial@gmail.com','2026-07-11 06:47:19'),(21,'Login seller: sellerdemo@rubbybooks.com','2026-07-11 06:48:32'),(22,'Login buyer: dimasprasetyo@gmail.com','2026-07-11 06:48:56'),(23,'Login buyer: dimasprasetyo@gmail.com','2026-07-11 06:48:56'),(24,'Login seller: sellerdemo@rubbybooks.com','2026-07-11 06:50:09'),(25,'Login buyer: dimasprasetyo@gmail.com','2026-07-11 06:54:29'),(26,'Login seller: sellerdemo@rubbybooks.com','2026-07-11 06:55:04'),(27,'Login seller: keigoofficial@gmail.com','2026-07-11 07:07:44'),(28,'Update akun user #4','2026-07-11 07:07:50'),(29,'Update akun user #4','2026-07-11 07:13:57'),(30,'Login admin: admindemo@rubbybooks.com','2026-07-11 07:15:33'),(31,'Login seller: sellerdemo@rubbybooks.com','2026-07-11 07:20:30'),(32,'Update akun user #2','2026-07-11 07:22:40'),(33,'Login admin: admindemo@rubbybooks.com','2026-07-11 07:23:43'),(34,'Update akun user #1','2026-07-11 07:25:09'),(35,'Login seller: sellerdemo@rubbybooks.com','2026-07-11 07:25:25'),(36,'Login buyer: rinaamelia@gmail.com','2026-07-11 07:30:37'),(37,'Update akun user #6','2026-07-11 07:40:40'),(38,'Update akun user #6','2026-07-11 07:40:58'),(39,'Login buyer: nasylaputri@gmail.com','2026-07-11 07:55:54'),(40,'Login seller: sellerdemo@rubbybooks.com','2026-07-11 08:06:33'),(41,'Login buyer: nasylaputri@gmail.com','2026-07-11 08:07:29'),(42,'Checkout invoice INV-20260711-003','2026-07-11 08:09:44'),(43,'Login seller: keigoofficial@gmail.com','2026-07-11 08:16:56'),(44,'Update akun user #4','2026-07-11 08:20:04'),(45,'Login buyer: nasylaputri@gmail.com','2026-07-11 08:20:20'),(46,'Login seller: sellerdemo@rubbybooks.com','2026-07-11 08:20:37'),(47,'Update akun user #2','2026-07-11 08:20:47'),(48,'Login admin: admindemo@rubbybooks.com','2026-07-11 08:20:54'),(49,'Login seller: keigoofficial@gmail.com','2026-07-11 08:27:42'),(50,'Login buyer: buyerdemo@rubbybooks.com','2026-07-11 08:31:30'),(51,'Login admin: admindemo@rubbybooks.com','2026-07-11 08:33:21'),(52,'Login seller: keigoofficial@gmail.com','2026-07-11 08:43:18'),(53,'Login buyer: nasylaputri@gmail.com','2026-07-11 08:54:33'),(54,'Login seller: keigoofficial@gmail.com','2026-07-11 10:33:56'),(55,'Update akun user #4','2026-07-11 10:38:50'),(56,'Registrasi seller: testseller@example.com','2026-07-11 10:42:16'),(57,'Registrasi seller: seller2@example.com','2026-07-11 10:43:04'),(58,'Registrasi seller: seller3@example.com','2026-07-11 10:44:00'),(59,'Update akun user #4','2026-07-11 10:46:37'),(60,'Update akun user #4','2026-07-11 10:46:43'),(61,'Update akun user #4','2026-07-11 10:46:52');
/*!40000 ALTER TABLE `system_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_settings` (
  `key` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_settings`
--

LOCK TABLES `system_settings` WRITE;
/*!40000 ALTER TABLE `system_settings` DISABLE KEYS */;
INSERT INTO `system_settings` VALUES ('currency','IDR'),('low_stock_alert','1'),('low_stock_threshold','5'),('min_order','35000'),('ppn_included','0'),('ppn_rate','5'),('show_stock_display','1'),('timezone','Asia/Jakarta');
/*!40000 ALTER TABLE `system_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('buyer','seller','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'buyer',
  `status` enum('active','pending','banned','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `alternate_phone` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_requested` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin Demo','admindemo@rubbybooks.com','$2y$12$xHqU3v/45UMrZHQBKyjBt.qjGyGfTzAnX9FwxxgBA.L1LcRlcdXCG','admin','active','uploads/avatars/avatar_1_1783754709.png','',NULL,'',0,'2026-07-11 06:01:57'),(2,'Seller Demo','sellerdemo@rubbybooks.com','$2y$12$xHqU3v/45UMrZHQBKyjBt.qjGyGfTzAnX9FwxxgBA.L1LcRlcdXCG','seller','active','uploads/avatars/avatar_2_1783758047.jpg','',NULL,'',0,'2026-07-11 06:01:57'),(3,'Buyer Demo','buyerdemo@rubbybooks.com','$2y$12$xHqU3v/45UMrZHQBKyjBt.qjGyGfTzAnX9FwxxgBA.L1LcRlcdXCG','buyer','active',NULL,NULL,NULL,NULL,0,'2026-07-11 06:01:57'),(4,'Keigo Official','keigoofficial@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','seller','active','uploads/avatars/avatar_4_1783766330.jpg','082129571824',NULL,'',0,'2026-07-11 06:01:57'),(5,'Literasi Jaya','literasijaya@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','seller','active',NULL,NULL,NULL,NULL,0,'2026-07-11 06:01:57'),(6,'Nasyla Putri','nasylaputri@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','buyer','active','uploads/avatars/avatar_6_1783755658.jpg','086219285212','2006-04-02','',0,'2026-07-11 06:01:57'),(7,'Dimas Prasetyo','dimasprasetyo@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','buyer','active',NULL,NULL,NULL,NULL,0,'2026-07-11 06:01:57'),(8,'Test Seller','testseller@example.com','$2y$10$R08OtfJeZDvg9zEm7gqCAOAKVSQJrxISqBOxn9EP5zjHndtEjeEF2','seller','pending',NULL,NULL,NULL,NULL,0,'2026-07-11 10:42:16'),(9,'Seller Two','seller2@example.com','$2y$10$iiqEd9OQGNB5bpUdBFTdselxfTxdRp6oLF8nH1tXulNRC5bL7CPju','seller','pending',NULL,NULL,NULL,NULL,0,'2026-07-11 10:43:04'),(10,'Seller Three','seller3@example.com','$2y$10$tKad8nalOt38oEapSsB8kOdycMzMdFTynicEt7CRd88.dSH7Fl/w.','seller','pending',NULL,NULL,NULL,NULL,0,'2026-07-11 10:44:00');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wishlists`
--

DROP TABLE IF EXISTS `wishlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wishlists` (
  `id` int NOT NULL AUTO_INCREMENT,
  `buyer_id` int NOT NULL,
  `product_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_product` (`buyer_id`,`product_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `wishlists_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `wishlists_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlists`
--

LOCK TABLES `wishlists` WRITE;
/*!40000 ALTER TABLE `wishlists` DISABLE KEYS */;
INSERT INTO `wishlists` VALUES (1,7,6,'2026-07-11 06:33:56'),(2,7,1,'2026-07-11 06:34:03'),(3,7,5,'2026-07-11 06:43:43'),(4,6,7,'2026-07-11 07:37:13'),(5,6,5,'2026-07-11 07:37:14');
/*!40000 ALTER TABLE `wishlists` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-11 17:46:56
