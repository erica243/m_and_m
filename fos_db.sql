-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2024 at 08:55 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(30) NOT NULL,
  `client_ip` varchar(20) NOT NULL,
  `user_id` int(30) NOT NULL,
  `product_id` int(30) NOT NULL,
  `qty` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `client_ip`, `user_id`, `product_id`, `qty`) VALUES
(11, '', 2, 6, 2),
(16, '', 3, 9, 1),
(17, '', 3, 8, 1),
(28, '', 4, 12, 1),
(115, '', 14, 15, 1);

-- --------------------------------------------------------

--
-- Table structure for table `category_list`
--

CREATE TABLE `category_list` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category_list`
--

INSERT INTO `category_list` (`id`, `name`) VALUES
(8, 'Baptismal Cake '),
(10, 'Fathers/Mothers Day Cake'),
(19, 'Wedding Cake'),
(23, 'Birthday Cake');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `order_number` int(50) NOT NULL,
  `message` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_reply` text DEFAULT NULL,
  `user_reply` text DEFAULT NULL,
  `reply_date` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `email`, `order_number`, `message`, `image_path`, `created_at`, `admin_reply`, `user_reply`, `reply_date`, `status`) VALUES
(82, 'us1071591@gmail.com', 920893, 'sa', '', '2024-09-27 09:28:43', NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(30) NOT NULL,
  `order_number` int(11) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `name` text NOT NULL,
  `address` text NOT NULL,
  `mobile` text NOT NULL,
  `email` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `delivery_method` varchar(100) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `payment_method` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `shipping` int(11) NOT NULL,
  `pickup_date` date DEFAULT NULL,
  `pickup_time` time DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `order_date`, `name`, `address`, `mobile`, `email`, `status`, `delivery_method`, `transaction_id`, `payment_method`, `created_at`, `shipping`, `pickup_date`, `pickup_time`, `payment_proof`) VALUES
(26, 7360, '2024-09-22 09:35:38', 'Erica Adlit', 'Tarong Madridejos Cebu', '0915829634', 'erica204chavez@gmail.com', 1, 'delivery', 0, 'gcash', '0000-00-00 00:00:00', 0, '0000-00-00', '00:00:00', 'uploads/payment_proof/1726990538_5a1eacf9-2be3-4cfd-a074-09995925d758.jpg'),
(27, 2689, '0000-00-00 00:00:00', 'Erica wan', 'Tarong Madridejos Cebu', '0915829634', 'erica204chavez@gmail.com', 0, 'delivery', 0, 'gcash', '2024-09-22 17:55:46', 0, '0000-00-00', '00:00:00', NULL),
(28, 1884, '0000-00-00 00:00:00', 'Erica wan', 'Tarong Madridejos Cebu', '0915829634', '', 0, 'Delivery', 0, 'gcash', '2024-09-22 17:57:56', 0, '0000-00-00', '00:00:00', NULL),
(29, 4116, '0000-00-00 00:00:00', 'Erica wan', 'Tarong Madridejos Cebu', '0915829634', '', 0, 'delivery', 0, 'gcash', '2024-09-22 17:59:42', 0, '0000-00-00', '00:00:00', NULL),
(30, 1921, '0000-00-00 00:00:00', 'Erica wan', 'Tarong Madridejos Cebu', '0915829634', '', 0, 'delivery', 0, 'gcash', '2024-09-22 18:03:48', 0, '0000-00-00', '00:00:00', NULL),
(31, 3638, '2024-09-22 12:05:15', 'Erica wan', 'Tarong Madridejos Cebu', '0915829634', '', 0, 'Delivery', 0, 'gcash', '2024-09-22 18:05:15', 0, '0000-00-00', '00:00:00', 'uploads/payment_proof/1726999515_Screenshot_2024-08-17-08-08-13-49.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `order_list`
--

CREATE TABLE `order_list` (
  `id` int(30) NOT NULL,
  `order_id` int(30) NOT NULL,
  `product_id` int(30) NOT NULL,
  `qty` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_list`
--

INSERT INTO `order_list` (`id`, `order_id`, `product_id`, `qty`) VALUES
(54, 0, 12, 1),
(55, 0, 15, 1),
(56, 5, 10, 1),
(57, 6, 11, 1),
(58, 7, 15, 1),
(59, 8, 15, 1),
(60, 9, 12, 1),
(61, 10, 11, 1),
(62, 11, 15, 1),
(63, 12, 19, 1),
(64, 13, 16, 1),
(65, 14, 10, 1),
(66, 15, 10, 1),
(67, 15, 18, 1),
(68, 16, 16, 6),
(69, 17, 16, 1),
(70, 18, 14, 1),
(71, 19, 18, 1),
(72, 20, 14, 1),
(73, 20, 14, 1),
(74, 21, 16, 1),
(75, 22, 14, 1),
(76, 23, 14, 1),
(77, 24, 14, 1),
(78, 25, 16, 1),
(79, 26, 16, 1),
(80, 27, 14, 1),
(81, 28, 16, 1),
(82, 29, 18, 1),
(83, 30, 16, 1),
(84, 31, 18, 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_list`
--

CREATE TABLE `product_list` (
  `id` int(30) NOT NULL,
  `category_id` int(30) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` float NOT NULL DEFAULT 0,
  `img_path` text NOT NULL,
  `status` varchar(100) NOT NULL,
  `size` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_list`
--

INSERT INTO `product_list` (`id`, `category_id`, `name`, `description`, `price`, `img_path`, `status`, `size`) VALUES
(11, 7, 'Wedding Cake', 'fsdf', 55543, '1720082160_b8.jpg', 'Unavailable', '0'),
(14, 8, 'sdsd', 'sdsd', 22, '1720083240_b1.jpg', 'Available', '0'),
(15, 8, 'qq', 'as', 1, '1720083240_b6.jpg', 'Unavailable', '0');

-- --------------------------------------------------------

--
-- Table structure for table `product_ratings`
--

CREATE TABLE `product_ratings` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `feedback` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `replies`
--

CREATE TABLE `replies` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `reply` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `email` varchar(200) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `cover_img` text NOT NULL,
  `about_content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `name`, `email`, `contact`, `cover_img`, `about_content`) VALUES
(1, 'M&M Cake Ordering System', 'erica204chavez@gmail.com', '+639158259643', '1721754180_bg.jpg', '&lt;h1 style=&quot;text-align: center; background: transparent; position: relative;&quot;&gt;&lt;span style=&quot;color:rgb(68,68,68);text-align: center; background: transparent; position: relative;&quot;&gt;&lt;h1&gt;&lt;span style=&quot;text-align: center; background: transparent; position: relative; color: rgb(68, 68, 68);&quot;&gt;&lt;sup style=&quot;text-align: center; background: transparent; position: relative; color: rgb(68, 68, 68);&quot;&gt;&lt;b style=&quot;text-align: center; background: transparent; position: relative; color: rgb(68, 68, 68);&quot;&gt;ABOUT US&lt;/b&gt;&lt;/sup&gt;&lt;/span&gt;&lt;/h1&gt;&lt;h1&gt;&lt;span style=&quot;text-align: center; background: transparent; position: relative; color: rgb(68, 68, 68);&quot;&gt;&lt;sup style=&quot;text-align: center; background: transparent; position: relative; color: rgb(68, 68, 68);&quot;&gt;&amp;nbsp;&lt;/sup&gt;&lt;/span&gt;&lt;/h1&gt;&lt;/span&gt;&lt;span style=&quot;font-size:20px;text-align: center; background: transparent; position: relative; color: rgb(68, 68, 68);&quot;&gt;&lt;span style=&quot;color: rgb(68, 68, 68); text-align: center; background: transparent; position: relative; font-size: 20px;&quot;&gt;&lt;h1 style=&quot;font-size: 20px;&quot;&gt;&lt;span style=&quot;text-align: center; background: transparent; position: relative; color: rgb(68, 68, 68); font-size: 20px;&quot;&gt;&lt;sup style=&quot;text-align: center; background: transparent; position: relative; color: rgb(68, 68, 68); font-size: 20px;&quot;&gt;&lt;/sup&gt;&lt;/span&gt;&lt;/h1&gt;&lt;/span&gt;&lt;span style=&quot;font-size: 24px; text-align: center; background: transparent; position: relative; color: rgb(68, 68, 68);&quot;&gt;&lt;span style=&quot;color: rgb(68, 68, 68); text-align: center; background: transparent; position: relative; font-size: 24px;&quot;&gt;&lt;h1 style=&quot;font-size: 24px;&quot;&gt;&lt;span style=&quot;text-align: center; background: transparent; position: relative; color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;&lt;sup style=&quot;text-align: center; background: transparent; position: relative; color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;&lt;b style=&quot;text-align: center; background: transparent; position: relative; color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;Welcome to the M&amp;amp;M Cake Ordering System, home of beautifully tasty cakes, an unforgettable cake for every one! We at M&amp;amp;M believe that every occasion is of the highest importance: Celebrate with a cake as exceptional and unique as you. Our selection of beautifully crafted cakes is perfect for your special occasions, whether it&rsquo;s celebrating a birthday, wedding, anniversary - you name it.&lt;/b&gt;&lt;/sup&gt;&lt;/span&gt;&lt;/h1&gt;&lt;h3 style=&quot;font-size: 24px;&quot;&gt;&lt;b style=&quot;font-size: 24px;&quot;&gt;&lt;sup style=&quot;color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;&amp;nbsp; &amp;nbsp;&amp;nbsp;&lt;br style=&quot;font-size: 24px;&quot;&gt;&lt;/sup&gt;&lt;sup style=&quot;color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;&amp;nbsp; &amp;nbsp;&lt;sup style=&quot;color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;&lt;/sup&gt;&lt;/sup&gt;&lt;/b&gt;&lt;/h3&gt;&lt;/span&gt;&lt;span style=&quot;color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;&lt;span style=&quot;color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;&lt;span style=&quot;font-size: 24px; color: rgb(68, 68, 68);&quot;&gt;&lt;span style=&quot;color: rgb(68, 68, 68); text-align: center; background: transparent; position: relative; font-size: 24px;&quot;&gt;&lt;h3 style=&quot;font-size: 24px;&quot;&gt;&lt;b style=&quot;font-size: 24px;&quot;&gt;&lt;sup style=&quot;color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;&lt;sup style=&quot;color: rgb(68, 68, 68); font-size: 24px;&quot;&gt; The story of M&amp;amp;M started in the 1980s with a love of baking and a dedication to perfection. The name &quot;M&amp;amp;M&quot; stands for &quot;Money and Millions,&quot; symbolizing our commitment to delivering value and abundance in every creation we make.&lt;br style=&quot;color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;&lt;/sup&gt;&lt;/sup&gt;&lt;sup style=&quot;font-size: 24px;&quot;&gt;&lt;span style=&quot;color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;&amp;nbsp; &amp;nbsp;&amp;nbsp;&lt;br style=&quot;font-size: 24px;&quot;&gt;&lt;/span&gt;&lt;span style=&quot;color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;&amp;nbsp; &amp;nbsp; We pride ourselves on selecting only the best ingredients, meaning that every cake we make not only looks amazing but tastes delicious too. Our talented bakers and decorators bring some of your favorite classic flavors to new heights, as well as one-of-a-kind creations inspired by your sweetest visions.&lt;/span&gt;&lt;/sup&gt;&lt;/b&gt;&lt;/h3&gt;&lt;/span&gt;&lt;p style=&quot;text-align: center; font-size: 24px;&quot;&gt;&lt;/p&gt;&lt;/span&gt;&lt;p style=&quot;text-align: center; font-size: 24px;&quot;&gt;&lt;/p&gt;&lt;/span&gt;&lt;span style=&quot;color: rgb(68, 68, 68); font-size: 24px;&quot;&gt;&lt;p style=&quot;text-align: center; font-size: 24px;&quot;&gt;&lt;/p&gt;&lt;/span&gt;&lt;span style=&quot;color: rgb(68, 68, 68); font-size: 16px;&quot;&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;br&gt;&lt;/p&gt;&lt;p&gt;&lt;/p&gt;&lt;/span&gt;&lt;/h1&gt;');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(30) NOT NULL,
  `name` varchar(200) NOT NULL,
  `username` text NOT NULL,
  `password` varchar(200) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 2 COMMENT '1=admin , 2 = staff',
  `profile_picture` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `reset_token` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `type`, `profile_picture`, `email`, `reset_token`) VALUES
(1, 'Erica Adlit', 'erica204chavez@gmail.com', '$2y$10$If7IBsUX7uApBJV4Pe7m9OnIio4Ajfhczq5xGZTYiw3FmQlZnXNae', 1, NULL, NULL, '065d149999e6a352b92c0c9208606c90fce617f82837f68e63a753751a23ae9e70f04327dfa92c6b357677b79e7e9c65ffd9');

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

CREATE TABLE `user_info` (
  `user_id` int(10) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(300) NOT NULL,
  `password` varchar(300) NOT NULL,
  `mobile` varchar(10) NOT NULL,
  `address` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`user_id`, `first_name`, `last_name`, `email`, `password`, `mobile`, `address`) VALUES
(13, 'irish', 'illustrisimo', 'manilyndemesa87@gmail.com', '$2y$10$A7t7glpYSDAyvKLwFPFHy.Lt.asyt1sNk8wc0zQtK8LRLoRIxoS/q', '0915829634', 'maalat'),
(14, 'keneth', 'ducay', 'us1071591@gmail.com', '$2y$10$GSbBQ4J.VhqShJNaRtyHoelDwfxLN.8TrWnAuGjFtCSiusMRCOZdS', '0915829634', 'atoop atop');

-- --------------------------------------------------------

--
-- Table structure for table `user_messages`
--

CREATE TABLE `user_messages` (
  `message_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `message_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_reply` text DEFAULT NULL,
  `reply_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category_list`
--
ALTER TABLE `category_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_list`
--
ALTER TABLE `order_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_list`
--
ALTER TABLE `product_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_ratings`
--
ALTER TABLE `product_ratings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `replies`
--
ALTER TABLE `replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_messages`
--
ALTER TABLE `user_messages`
  ADD PRIMARY KEY (`message_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `category_list`
--
ALTER TABLE `category_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `order_list`
--
ALTER TABLE `order_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `product_list`
--
ALTER TABLE `product_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `product_ratings`
--
ALTER TABLE `product_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `replies`
--
ALTER TABLE `replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user_messages`
--
ALTER TABLE `user_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `replies`
--
ALTER TABLE `replies`
  ADD CONSTRAINT `replies_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
