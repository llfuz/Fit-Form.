-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2026 at 12:43 PM
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
-- Database: `fit_form`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`) VALUES
(1, 1),
(2, 3),
(3, 5),
(4, 4),
(5, 8),
(6, 9);

-- --------------------------------------------------------

--
-- Table structure for table `cart_item`
--

CREATE TABLE `cart_item` (
  `cart_item_id` int(11) NOT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `size` varchar(10) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `height` varchar(20) DEFAULT NULL,
  `waist` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_item`
--

INSERT INTO `cart_item` (`cart_item_id`, `cart_id`, `product_id`, `quantity`, `price`, `size`, `color`, `notes`, `height`, `waist`) VALUES
(24, 4, 3, 1, 210.00, NULL, NULL, NULL, NULL, NULL),
(25, 4, 2, 2, 220.00, NULL, NULL, NULL, NULL, NULL),
(26, 4, 4, 1, 250.00, NULL, NULL, NULL, NULL, NULL),
(41, 3, 10, 1, 280.00, '', '#000000', '', NULL, NULL),
(42, 3, 4, 1, 250.00, '', '#8f5656', '', NULL, NULL),
(43, 3, 15, 1, 280.00, '38', '#643a3a', '', NULL, NULL),
(44, 3, 4, 1, 250.00, '', '#000000', '', NULL, NULL),
(45, 3, 2, 1, 220.00, 'S', '#000000', '', NULL, NULL),
(46, 3, 35, 2, 240.00, NULL, '#000000', '', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `name`) VALUES
(1, 'Dress'),
(2, 'Shoes'),
(3, 'Shirt'),
(4, 'Skirt'),
(5, 'Pants'),
(6, 'Accessories'),
(7, 'Bags');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `message_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`message_id`, `full_name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'Fajer', 'fajer.hussain1766@gmail.com', 'try', 'Hi', '2026-04-17 13:42:13');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `name` varchar(20) NOT NULL,
  `address` varchar(100) NOT NULL,
  `payment` varchar(100) NOT NULL,
  `total` int(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_price`, `status`, `name`, `address`, `payment`, `total`) VALUES
(1, 1, 250.00, 'placed', '', '', '0', 0),
(2, 1, 100.00, 'placed', '', '', '0', 0),
(3, 1, NULL, 'cancelled', 'Ahad Alhudhaif', 'Dammam, Ø§Ù„Ø¯Ù…Ø§Ù…, 31441, Saudi Arabia', 'cash', 0),
(4, 1, NULL, NULL, 'Ahad Alhudhaif', 'Dammam, Ø§Ù„Ø¯Ù…Ø§Ù…, 31441, Saudi Arabia', 'card', 475),
(5, 1, NULL, NULL, 'Ahad Alhudhaif', 'Dammam, Ø§Ù„Ø¯Ù…Ø§Ù…, 31441, Saudi Arabia', 'card', 0),
(6, 1, NULL, NULL, 'Ahad Alhudhaif', 'Dammam, Ø§Ù„Ø¯Ù…Ø§Ù…, 31441, Saudi Arabia', 'cash', 250),
(7, 1, NULL, NULL, 'Ahad Alhudhaif', 'Dammam, Ø§Ù„Ø¯Ù…Ø§Ù…, 31441, Saudi Arabia', 'cash', 250),
(8, 1, NULL, NULL, 'Ahad Alhudhaif', 'Dammam, Ø§Ù„Ø¯Ù…Ø§Ù…, 31441, Saudi Arabia', 'cash', 250),
(9, 1, NULL, NULL, 'Ahad Alhudhaif', 'Dammam, Ø§Ù„Ø¯Ù…Ø§Ù…, 31441, Saudi Arabia', 'cash', 140),
(10, 3, NULL, NULL, 'Fajer', '7662, dammam, 32424, المملكة العربية السعودية', 'card', 120),
(11, 3, NULL, NULL, 'Fajer', '7662, dammam, 32424, المملكة العربية السعودية', 'cash', 0),
(12, 3, NULL, 'delivered', 'Fajer', '7662, dammam, 32424, المملكة العربية السعودية', 'card', 90),
(13, 8, NULL, NULL, 'Fajer', '7662, Dammam, 32424, المملكة العربية السعودية', 'cash', 290),
(14, 9, NULL, NULL, 'Fajer', '7662, Dammam, 32424, المملكة العربية السعودية', 'cash', 260);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `color`) VALUES
(1, 4, 3, 1, NULL),
(2, 4, 4, 1, NULL),
(3, 4, 1, 1, NULL),
(4, 6, 2, 1, NULL),
(5, 7, 2, 1, NULL),
(6, 8, 2, 1, NULL),
(7, 9, 3, 1, NULL),
(8, 10, 12, 1, NULL),
(9, 12, 7, 1, NULL),
(10, 13, 39, 1, NULL),
(11, 14, 7, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `name`, `description`, `price`, `stock`, `category_id`, `image`) VALUES
(1, 'Elegant Maxi Dress', NULL, 180.00, NULL, 1, 'Dress1.jpg'),
(2, 'Classic Evening Dress', NULL, 220.00, NULL, 1, 'Dress2.jpg'),
(3, 'Midi Dress', NULL, 210.00, NULL, 1, 'Dress3.jpg'),
(4, 'Floral Maxi Dress', NULL, 250.00, NULL, 1, 'Dress4.jpg'),
(5, 'Flowing Gown', NULL, 270.00, NULL, 1, 'Dress5.jpg'),
(6, 'Formal Evening Gown', NULL, 290.00, NULL, 1, 'Dress6.jpg'),
(7, 'Elegant Occasion Dress', NULL, 260.00, NULL, 1, 'Dress7.jpg'),
(8, 'Satin Dress', NULL, 240.00, NULL, 1, 'Dress8.jpg'),
(9, 'Soft Flow Dress', NULL, 230.00, NULL, 1, 'Dress9.jpg'),
(10, 'Statement Dress', NULL, 280.00, NULL, 1, 'Dress10.jpg'),
(11, 'Classic Heels', NULL, 250.00, NULL, 2, 'Heel1.jpg'),
(12, 'Elegant Heels', NULL, 240.00, NULL, 2, 'Heel2.jpg'),
(13, 'Strap Heels', NULL, 260.00, NULL, 2, 'Heel3.jpg'),
(15, 'Pearl Lace Heel', NULL, 280.00, NULL, 2, 'Heel4.jpg'),
(16, 'Elegant Blouse', NULL, 140.00, NULL, 3, 'Shirt1.jpg'),
(17, 'Formal Shirt', NULL, 160.00, NULL, 3, 'Shirt2.jpg'),
(18, 'Floral Skirt', NULL, 150.00, NULL, 4, 'Skirt1.jpg'),
(19, 'Flow Skirt', NULL, 170.00, NULL, 4, 'Skirt2.jpg'),
(20, 'Classic Pants', NULL, 180.00, NULL, 5, 'Pants1.jpg'),
(21, 'Wide Leg Pants', NULL, 190.00, NULL, 5, 'Pants2.jpg'),
(22, 'Pendant Necklace', NULL, 85.00, NULL, 6, 'Accessories1.jpg'),
(23, 'Layered Necklace', NULL, 95.00, NULL, 6, 'Accessories2.jpg'),
(24, 'Bracelet Set', NULL, 75.00, NULL, 6, 'Accessories3.jpg'),
(25, 'Elegant Bracelet', NULL, 80.00, NULL, 6, 'Accessories4.jpg'),
(34, 'Mini Handbag', NULL, 220.00, NULL, 7, 'Bag1.jpg'),
(35, 'Structured Handbag', NULL, 240.00, NULL, 7, 'Bag2.jpg'),
(36, 'Clutch Bag', NULL, 210.00, NULL, 7, 'Bag3.jpg'),
(37, 'Classic Tote Bag', NULL, 260.00, NULL, 7, 'Bag4.jpg'),
(38, 'Luxury Silver Gold Watch', NULL, 320.00, NULL, 6, 'accessories5.jpg'),
(39, 'Elegant Gold Bracelet Watch', NULL, 290.00, NULL, 6, 'accessories6.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `role` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `name`, `email`, `phone`, `gender`, `password`, `role`) VALUES
(4, 'ehda', 'ehda@gmail.com', NULL, NULL, '$2y$10$8aKbnD8FF8MaUNjHexZbceGxmqftcfCHC30z6iEAPlz8g3woWKVqe', NULL),
(5, 'Fajer', '2230004316@iau.edu.sa', '966537784544', 'Female', '$2y$10$PD4XFif8SX034sHx9tpVKOe9k8GEyxqm.zby9qPbY.AFoTvA7up9.', NULL),
(6, 'budur', 'budur@gmail.com', '0561241914', 'Female', '$2y$10$1Z6NAL4eQ9fdo2Yz6aYkzeaGMQ/A2jO9BplWYWF5qrUwO3JyYhWIi', NULL),
(7, 'Admin', 'admin@gmail.com', '966537784544', 'Female', '$2y$10$P2TKLV874vFRto.bHLSA1u8RoVbc9jnMsRj/ot3OrhM37uWEMhDae', 'admin'),
(8, 'fajer', '2230004316@iau.edu', '+966537784544', 'Female', '$2y$10$8VQobnUKU4yMjntBEm3lX.p4tFu1cDo9vv7GLwaJEZZd6JzmjRk4W', 'user'),
(9, 'zainab', 'z044275@gmail.com', '+966537784544', 'Female', '$2y$10$6StE9p0EQk3QihYhFK/5POJ8XP4TvcrQPsJBou7Uh41ogz2O.4lUW', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `wishlist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cart_item`
--
ALTER TABLE `cart_item`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD CONSTRAINT `cart_item_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`),
  ADD CONSTRAINT `cart_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
