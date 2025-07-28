-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2025 at 02:58 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecommerce_demo`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action_type` varchar(50) DEFAULT NULL,
  `page_accessed` varchar(100) DEFAULT NULL,
  `interaction_details` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `admin_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `can_ban_users` tinyint(1) DEFAULT 0,
  `can_archive_posts` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bids`
--

CREATE TABLE `bids` (
  `bid_id` int(11) NOT NULL,
  `exclusive_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bid_amount` decimal(10,2) NOT NULL,
  `bid_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `size` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bids`
--

INSERT INTO `bids` (`bid_id`, `exclusive_id`, `user_id`, `bid_amount`, `bid_time`, `size`) VALUES
(1, 9, 2, 96798.00, '2025-06-21 00:01:49', NULL),
(2, 9, 2, 99999999.99, '2025-06-21 16:17:42', NULL),
(3, 9, 2, 99999999.99, '2025-06-21 16:18:25', NULL),
(4, 9, 2, 99999999.99, '2025-06-21 19:59:33', NULL),
(5, 9, 2, 99999999.99, '2025-06-21 19:59:45', NULL),
(6, 9, 2, 999999.99, '2025-06-21 19:59:57', NULL),
(7, 9, 2, 99999999.99, '2025-06-21 20:02:16', NULL),
(8, 9, 2, 99999999.99, '2025-06-21 20:02:21', NULL),
(9, 9, 2, 999999.99, '2025-06-21 20:14:34', NULL),
(10, 9, 2, 999999.99, '2025-06-21 20:15:24', NULL),
(11, 9, 2, 999999.99, '2025-06-21 20:16:04', NULL),
(12, 9, 2, 999999.99, '2025-06-21 20:16:50', NULL),
(13, 9, 2, 999999.99, '2025-06-21 20:17:55', NULL),
(14, 11, 2, 289.99, '2025-06-22 04:00:51', 'US 9'),
(15, 12, 2, 229.00, '2025-06-22 04:01:17', 'US 10'),
(16, 11, 2, 309.99, '2025-06-22 04:04:13', 'US 9'),
(17, 15, 2, 189.95, '2025-06-22 07:18:38', 'US 9'),
(18, 13, 2, 299.99, '2025-06-22 11:08:51', 'US 9'),
(19, 13, 2, 309.99, '2025-06-22 11:13:15', 'US 7');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`cart_item_id`, `user_id`, `product_id`, `quantity`) VALUES
(11, 2, 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `exclusive_products`
--

CREATE TABLE `exclusive_products` (
  `exclusive_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exclusive_products`
--

INSERT INTO `exclusive_products` (`exclusive_id`, `product_id`, `start_time`, `end_time`) VALUES
(9, 4, '2025-06-21 09:50:47', '2025-06-22 09:50:47'),
(11, 101, '2025-06-22 13:06:32', '2025-06-29 13:06:32'),
(12, 102, '2025-06-22 13:06:32', '2025-06-27 13:06:32'),
(13, 103, '2025-06-22 13:06:32', '2025-07-02 13:06:32'),
(14, 104, '2025-06-22 13:06:32', '2025-06-25 13:06:32'),
(15, 105, '2025-06-22 13:06:32', '2025-06-26 13:06:32'),
(16, 106, '2025-06-22 13:06:32', '2025-06-28 13:06:32');

-- --------------------------------------------------------

--
-- Table structure for table `external_api_data`
--

CREATE TABLE `external_api_data` (
  `api_id` int(11) NOT NULL,
  `api_name` varchar(100) DEFAULT NULL,
  `data_fetched` text DEFAULT NULL,
  `fetched_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forum_posts`
--

CREATE TABLE `forum_posts` (
  `post_id` int(11) NOT NULL,
  `thread_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forum_threads`
--

CREATE TABLE `forum_threads` (
  `thread_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `coupon_code` varchar(50) DEFAULT NULL,
  `delivery_address` text DEFAULT NULL,
  `payment_status` enum('pending','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_price`, `coupon_code`, `delivery_address`, `payment_status`, `created_at`) VALUES
(1, 2, 39.96, NULL, '', 'completed', '2025-06-18 17:04:36'),
(2, 2, 19.99, NULL, '', 'completed', '2025-06-18 17:16:02');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 4, 4, 9.99),
(2, 2, 1, 1, 19.99);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `reset_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `reset_token` varchar(100) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL,
  `thread_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `stock` int(11) NOT NULL,
  `brand` text NOT NULL,
  `isFeatured` tinyint(1) NOT NULL,
  `isNewArrival` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `price`, `image_url`, `description`, `stock`, `brand`, `isFeatured`, `isNewArrival`) VALUES
(5, 'Air Jordan 1 Retro High', 299.99, 'img/jordan1.jpeg', 'Iconic red and black design, high-top silhouette.', 10, 'Nike', 1, 1),
(6, 'Nike Dunk Low Panda', 169.95, 'img/dunk_panda.jpeg', 'Popular low-top sneakers with black & white colorway.', 15, 'Nike', 0, 1),
(7, 'Yeezy Boost 350 V2 Zebra', 399.00, 'img/yeezy_zebra.jpg', 'Zebra pattern primeknit and Boost cushioning.', 8, 'Adidas', 1, 0),
(8, 'New Balance 550 White Green', 159.99, 'img/nb550.jpeg', 'Classic basketball-inspired silhouette.', 20, 'New Balance', 0, 1),
(9, 'Nike Air Max 90 Infrared', 189.00, 'img/airmax90.jpg', 'Classic Nike Air Max silhouette with infrared highlights.', 12, 'Nike', 1, 1),
(10, 'Adidas Superstar White Black', 129.00, 'img/superstar.jpeg', 'Timeless shell-toe design with black stripes.', 18, 'Adidas', 0, 1),
(11, 'Puma RS-X³ Puzzle', 140.00, 'img/rsx.jpg', 'Bold color blocking with advanced cushioning.', 14, 'Puma', 1, 0),
(12, 'Reebok Club C 85 Vintage', 119.00, 'img/clubc85.jpeg', 'Clean, classic white tennis shoe design.', 10, 'Reebok', 0, 0),
(13, 'Air Jordan 4 Fire Red', 289.00, 'img/jordan4.jpeg', 'Retro 1989 basketball sneaker in red/white.', 9, 'Nike', 1, 1),
(14, 'Yeezy Slide Bone', 99.00, 'img/yeezy_slide.jpeg', 'Minimalist slip-on in neutral bone colorway.', 20, 'Adidas', 0, 1),
(15, 'Converse Chuck 70 High Black', 110.00, 'img/chuck70.jpeg', 'Premium black canvas high-top sneaker.', 13, 'Converse', 0, 0),
(16, 'New Balance 327 Moonbeam', 150.00, 'img/nb327.jpeg', 'Retro silhouette with oversized N logo.', 11, 'New Balance', 1, 0),
(17, 'Nike Blazer Mid 77 Vintage', 140.00, 'img/blazer77.jpeg', 'Throwback basketball style with suede panels.', 15, 'Nike', 0, 1),
(18, 'Air Jordan 3 Cement Grey', 250.00, 'img/jordan3.jpeg', 'Elephant print retro 3 with grey accents.', 6, 'Nike', 1, 1),
(19, 'Adidas NMD R1 Black Red', 180.00, 'img/nmd_r1.jpeg', 'Boost midsole with minimalist styling.', 12, 'Adidas', 0, 0),
(20, 'Vans Old Skool Classic Black', 99.00, 'img/vans_oldskool.jpeg', 'Iconic skate shoe with side stripe.', 22, 'Vans', 0, 1),
(101, 'Nike Air Max Elite', 249.99, 'img/airmax_elite.jpeg', 'Limited edition sneaker', 0, 'Nike', 1, 1),
(102, 'Adidas UltraBoost Prime', 229.00, 'img/ultraboost_prime.jpg', 'Rare drop release', 0, 'Adidas', 1, 0),
(103, 'Yeezy Quantum Flash', 299.99, 'img/yeezy_flash.jpeg', 'High demand sneaker', 0, 'Adidas', 1, 1),
(104, 'Puma Future Rider X', 199.50, 'img/future_rider_x.jpg', 'Exclusive collab edition', 0, 'Puma', 1, 0),
(105, 'New Balance 990v6', 189.95, 'img/nb_990v6.jpeg', 'Collector’s pick', 0, 'New Balance', 0, 1),
(106, 'Nike Dunk Low Tokyo', 279.00, 'img/dunk_tokyo.jpeg', 'Japan-exclusive release', 0, 'Nike', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `threads`
--

CREATE TABLE `threads` (
  `thread_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `shippingaddress` varchar(5000) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `theme_preference` enum('light','dark') DEFAULT 'light',
  `is_archived` tinyint(1) DEFAULT 0,
  `failed_logins` int(11) DEFAULT 0,
  `account_locked_until` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `fullname`, `shippingaddress`, `password_hash`, `profile_image`, `theme_preference`, `is_archived`, `failed_logins`, `account_locked_until`, `created_at`, `user_type`) VALUES
(1, 'Jahangir Alam', 'sharminjahangir2330@gmail.com', 'test', 'aaa', '$2y$10$sPxL4XV.qfpJl/4pua7ue.k4aPVpaqgcOlBAyjj46GIN/yIRguQHq', 'img/profile/6851b21d78fb88.87306863.jpg', 'light', 0, 0, NULL, '2025-06-16 20:27:53', 'customer'),
(2, 'CM Shahriar Zaki', 'shahriarzakiaus@gmail.com', 'CM Shahriar Zaki', '111 dudley', '$2y$10$b0W80LAZdRAEMunvlso.a.gpeLVSHe9JZWdWs6Ox2N/oALBHacN7y', 'img/profile/6852b94ce1a302.86110035.jpg', 'light', 0, 0, NULL, '2025-06-18 13:02:54', 'admin'),
(3, 'Sifat Siddique', 'siddiquesifat300@gmail.com', 'Sifat Siddque', '111', '$2y$10$JpqV9Yp3Z6CBf6xIJgZK8OWK2k5JLn0YeUy.INLCQdz8RXi0/pUpW', 'img/profile/6852bbb1d0a035.48086940.jpg', 'light', 0, 0, NULL, '2025-06-18 13:13:31', 'customer'),
(4, 'mahim sk', 'skred@gmail.com', '', '', '$2y$10$7yCN.pqqraKZIjO7rKQrme/wbxIcT2zKQLoiZll.lLRdtDnC/KBda', NULL, 'light', 0, 0, NULL, '2025-06-21 22:39:53', 'customer');

-- --------------------------------------------------------

--
-- Table structure for table `user_logs`
--

CREATE TABLE `user_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` text DEFAULT NULL,
  `page` varchar(100) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_logs`
--

INSERT INTO `user_logs` (`log_id`, `user_id`, `action`, `page`, `timestamp`) VALUES
(1, 2, 'Accessed Admin Dashboard', 'admin.php', '2025-06-22 09:48:10'),
(2, 2, 'Accessed Admin Panel', 'admin.php', '2025-06-22 09:50:29'),
(3, 2, 'Accessed Admin Panel', 'admin.php', '2025-06-22 09:54:59'),
(4, 2, 'Accessed Admin Panel', 'admin.php', '2025-06-22 09:56:10'),
(5, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:08:12'),
(6, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:08:27'),
(7, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:08:42'),
(8, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:08:51'),
(9, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:09:06'),
(10, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:09:21'),
(11, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:09:36'),
(12, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:09:51'),
(13, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:10:06'),
(14, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:10:21'),
(15, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:10:37'),
(16, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:10:52'),
(17, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:11:07'),
(18, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:11:22'),
(19, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:11:37'),
(20, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:11:52'),
(21, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:12:07'),
(22, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:12:22'),
(23, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:12:37'),
(24, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:12:52'),
(25, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:13:07'),
(26, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:13:15'),
(27, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:13:30'),
(28, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:13:37'),
(29, 2, 'Page View', 'bidDetail.php', '2025-06-22 11:13:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `bids`
--
ALTER TABLE `bids`
  ADD PRIMARY KEY (`bid_id`),
  ADD KEY `exclusive_id` (`exclusive_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `exclusive_products`
--
ALTER TABLE `exclusive_products`
  ADD PRIMARY KEY (`exclusive_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `external_api_data`
--
ALTER TABLE `external_api_data`
  ADD PRIMARY KEY (`api_id`);

--
-- Indexes for table `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `thread_id` (`thread_id`);

--
-- Indexes for table `forum_threads`
--
ALTER TABLE `forum_threads`
  ADD PRIMARY KEY (`thread_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`reset_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `thread_id` (`thread_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `threads`
--
ALTER TABLE `threads`
  ADD PRIMARY KEY (`thread_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_logs`
--
ALTER TABLE `user_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bids`
--
ALTER TABLE `bids`
  MODIFY `bid_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `exclusive_products`
--
ALTER TABLE `exclusive_products`
  MODIFY `exclusive_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `external_api_data`
--
ALTER TABLE `external_api_data`
  MODIFY `api_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forum_posts`
--
ALTER TABLE `forum_posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forum_threads`
--
ALTER TABLE `forum_threads`
  MODIFY `thread_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `threads`
--
ALTER TABLE `threads`
  MODIFY `thread_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_logs`
--
ALTER TABLE `user_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD CONSTRAINT `admin_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `bids`
--
ALTER TABLE `bids`
  ADD CONSTRAINT `bids_ibfk_1` FOREIGN KEY (`exclusive_id`) REFERENCES `exclusive_products` (`exclusive_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bids_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `exclusive_products`
--
ALTER TABLE `exclusive_products`
  ADD CONSTRAINT `exclusive_products_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD CONSTRAINT `forum_posts_ibfk_1` FOREIGN KEY (`thread_id`) REFERENCES `forum_threads` (`thread_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`thread_id`) REFERENCES `threads` (`thread_id`),
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `threads`
--
ALTER TABLE `threads`
  ADD CONSTRAINT `threads_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
