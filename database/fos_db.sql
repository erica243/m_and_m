-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 04, 2024 at 03:36 PM
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
-- Database: `fos_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_ip` varchar(20) NOT NULL,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `qty` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `client_ip`, `user_id`, `product_id`, `qty`) VALUES
(11, '', 2, 6, 2),
(16, '', 3, 9, 1),
(17, '', 3, 8, 1),
(28, '', 4, 12, 1);

-- --------------------------------------------------------

--
-- Table structure for table `category_list`
--

CREATE TABLE `category_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category_list`
--

INSERT INTO `category_list` (`id`, `name`) VALUES
(7, 'Birthdayd Cake'),
(8, 'Baptismal Cake '),
(10, 'Fathers/Mothers Day Cake');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_number` text NOT NULL,
  `order_date` text NOT NULL,
  `name` text NOT NULL,
  `address` text NOT NULL,
  `mobile` text NOT NULL,
  `email` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `delivery_method` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `order_date`, `name`, `address`, `mobile`, `email`, `status`, `delivery_method`, `created_at`) VALUES
(1, '001', '2024-07-03', 'James Smith', 'adasdasd asdadasd', '4756463215', 'jsmith@sample.com', 1, 'Courier', '2024-07-03 12:24:05'),
(2, '002', '2024-07-03', 'James Smith', 'adasdasd asdadasd', '4756463215', 'jsmith@sample.com', 1, 'Courier', '2024-07-03 15:28:37'),
(3, '003', '2024-07-04', 'Claire Blake', 'Sample Address', '0912365487', 'cblake@mail.com', 1, 'Courier', '2024-07-04 10:58:53'),
(4, '004', '2024-07-03', 'erica adlit', 'tarong', '0915829634', 'manilyndemesa87@gmail.com', 0, 'Courier', '2024-07-03 15:28:37'),
(5, '005', '2024-07-03', 'erica adlit', 'tarong', '0915829634', 'manilyndemesa87@gmail.com', 1, 'Courier', '2024-07-03 15:28:37'),
(6, '006', '2024-07-03', 'Keneth Ducay', 'Atop-Atop, Bantayan, Cebu', '0915829634', 'kenethducay12@gmail.com', 1, 'Courier', '2024-07-03 12:40:31'),
(7, '007', '2024-07-03', 'Keneth Ducay', 'Atop-Atop, Bantayan, Cebu', '0915829634', 'kenethducay12@gmail.com', 1, 'Courier', '2024-07-03 15:29:49'),
(8, '008', '2024-07-03', 'Keneth Ducay', 'Atop-Atop, Bantayan, Cebu', '0915829634', 'kenethducay12@gmail.com', 1, 'Courier', '2024-07-03 09:46:18'),
(9, '009', '2024-07-04', 'Keneth Ducay', 'Atop-Atop, Bantayan, Cebu', '0915829634', 'kenethducay12@gmail.com', 1, 'Courier', '2024-07-04 11:35:01'),
(10, '010', '2024-07-04', 'erica adlit', 'tarong', '0915829634', 'mdemesa@gmail.com', 0, 'Courier', '2024-07-04 17:55:24'),
(11, '011', '2024-07-04', 'erica adlit', 'tarong', '0915829634', 'mdemesa@gmail.com', 0, 'Courier', '2024-07-04 17:57:17'),
(12, '012', '2024-07-04', 'erica adlit', 'tarong', '0915829634', 'mdemesa@gmail.com', 0, 'Courier', '2024-07-04 18:05:25');

-- --------------------------------------------------------

--
-- Table structure for table `order_list`
--

CREATE TABLE `order_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `qty` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_list`
--

INSERT INTO `order_list` (`id`, `order_id`, `product_id`, `qty`) VALUES
(1, 1, 3, 1),
(2, 1, 5, 1),
(3, 1, 3, 1),
(4, 1, 6, 3),
(5, 2, 1, 2),
(6, 3, 1, 2),
(7, 4, 1, 1),
(8, 4, 1, 1),
(9, 5, 7, 1),
(10, 5, 9, 2),
(11, 6, 8, 2),
(12, 6, 9, 1),
(13, 7, 10, 2),
(14, 8, 8, 2),
(15, 9, 9, 1),
(16, 10, 9, 2),
(17, 10, 12, 1),
(18, 10, 12, 1),
(19, 11, 12, 2),
(20, 12, 15, 5);

-- --------------------------------------------------------

--
-- Table structure for table `product_list`
--

CREATE TABLE `product_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` float NOT NULL DEFAULT 0,
  `img_path` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0= unavailable, 2=Available',
  `rating` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_list`
--

INSERT INTO `product_list` (`id`, `category_id`, `name`, `description`, `price`, `img_path`, `status`, `rating`) VALUES
(3, 1, 'Choco Mocha', 'Description for Choco Mocha', 600, '', 1, 0),
(5, 1, 'Choco Mousse Cake', 'Description for Choco Mousse Cake', 600, '', 1, 0),
(6, 1, 'Mocha Cake', 'Description for Mocha Cake', 650, '', 1, 0),
(7, 1, 'Ube Cake', 'Description for Ube Cake', 650, '', 1, 0),
(8, 1, 'Blueberry Cheesecake', 'Description for Blueberry Cheesecake', 800, '', 1, 0),
(9, 1, 'Red Velvet Cake', 'Description for Red Velvet Cake', 800, '', 1, 0),
(10, 1, 'Choco Butternut Cake', 'Description for Choco Butternut Cake', 700, '', 1, 0),
(11, 1, 'Black Forest Cake', 'Description for Black Forest Cake', 700, '', 1, 0),
(12, 1, 'Carrot Cake', 'Description for Carrot Cake', 700, '', 1, 0),
(13, 1, 'Mango Cake', 'Description for Mango Cake', 650, '', 1, 0),
(14, 1, 'Pineapple Cake', 'Description for Pineapple Cake', 650, '', 1, 0),
(15, 1, 'Sans Rival', 'Description for Sans Rival', 650, '', 1, 0),
(16, 1, 'Fruit Cake', 'Description for Fruit Cake', 650, '', 1, 0),
(17, 1, 'Banana Cake', 'Description for Banana Cake', 650, '', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `firstname` text NOT NULL,
  `middlename` text NOT NULL,
  `lastname` text NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `avatar` text NOT NULL,
  `address` text NOT NULL,
  `contact` text NOT NULL,
  `email` text NOT NULL,
  `type` int NOT NULL DEFAULT 2 COMMENT '1=admin 2=user',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `middlename`, `lastname`, `username`, `password`, `avatar`, `address`, `contact`, `email`, `type`, `created_at`) VALUES
(2, 'James', 'D', 'Smith', 'jsmith', 'password', '', '123 Main St', '4756463215', 'jsmith@sample.com', 2, '2024-07-03 15:29:49'),
(3, 'Claire', '', 'Blake', 'cblake', 'password', '', '456 Elm St', '0912365487', 'cblake@mail.com', 2, '2024-07-03 10:58:53'),
(4, 'Keneth', '', 'Ducay', 'kenethducay12', 'password', '', 'Atop-Atop, Bantayan, Cebu', '0915829634', 'kenethducay12@gmail.com', 2, '2024-07-03 09:46:18');
--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_date` datetime NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `transaction_id` int NOT NULL,
  `mode_of_payment` varchar(50) NOT NULL,
  `qty` int NOT NULL,
  `total_amount` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    message VARCHAR(255),
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- Uncomment and modify the following section to insert data into the `reports` table if needed
-- INSERT INTO `reports` (`order_date`, `product_name`, `transaction_id`, `mode_of_payment`, `qty`, `total_amount`) VALUES
-- (NOW(), 'Sample Product', 1, 'Cash', 2, 1200.00);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
