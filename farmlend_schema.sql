-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 03, 2026 at 07:00 AM
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
-- Database: `farmlend`
--
CREATE DATABASE IF NOT EXISTS `farmlend` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `farmlend`;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `booking_status` enum('pending','approved','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `equipment_id`, `start_date`, `end_date`, `total_cost`, `booking_status`, `created_at`) VALUES
(1, 1, 4, '2026-08-03', '2026-08-10', 280000.00, 'pending', '2026-08-03 04:44:04'),
(2, 1, 7, '2026-08-03', '2026-08-07', 10000.00, 'approved', '2026-08-03 04:44:39'),
(3, 1, 1, '2026-08-03', '2026-08-09', 84000.00, 'pending', '2026-08-03 04:45:30'),
(4, 1, 11, '2026-08-03', '2026-08-12', 67500.00, 'approved', '2026-08-03 04:46:04'),
(5, 2, 3, '2026-08-03', '2026-08-10', 38500.00, 'pending', '2026-08-03 04:50:51'),
(6, 2, 3, '2026-08-20', '2026-08-30', 55000.00, 'approved', '2026-08-03 04:51:43'),
(7, 2, 9, '2026-08-03', '2026-08-13', 45000.00, 'pending', '2026-08-03 04:52:05');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Tractors', 'Heavy and light duty tractors for field work', '2026-08-01 20:53:13'),
(2, 'Harvesting', 'Harvesters, reapers, and threshers', '2026-08-01 20:53:13'),
(3, 'Irrigation', 'Water pumps and pipe systems', '2026-08-01 20:53:13'),
(4, 'Soil Preparation', 'Plows, harrows, and cultivators', '2026-08-01 20:53:13');

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

DROP TABLE IF EXISTS `equipment`;
CREATE TABLE `equipment` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `daily_rate` decimal(10,2) NOT NULL,
  `status` enum('available','rented','maintenance') DEFAULT 'available',
  `image_url` varchar(255) DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `owner_id`, `category_id`, `name`, `description`, `daily_rate`, `status`, `image_url`, `created_at`) VALUES
(1, 1, 1, 'Kubota L4508 Compact Tractor', 'A 45 HP four-wheel-drive compact tractor that handles easily in paddy fields and vegetable plots. It suits ploughing, transport, and running mounted implements on small to medium farms.', 14000.00, 'available', 'images/Kubota-L4508-Compact-Tractor.jpg', '2026-08-03 04:10:51'),
(2, 1, 1, 'Massey Ferguson 385 4WD', 'An 85 HP four-wheel-drive utility tractor built for heavy field work on larger holdings. It handles deep ploughing, harrowing, and trailer haulage with ease.', 19000.00, 'rented', 'images/Massey-Ferguson-385-4WD.jpg', '2026-08-03 04:22:52'),
(3, 1, 1, 'Kubota Two-Wheel Power Tiller', 'A walk-behind two-wheel tractor ideal for small paddy plots and puddling before transplanting. It is light, fuel efficient, and simple to operate in tight or wet fields.', 5500.00, 'available', 'images/Kubota-Two-Wheel-Power-Tiller.jpg', '2026-08-03 04:25:11'),
(4, 1, 2, 'Kubota DC-70 Plus Combine Harvester', 'A track-type combine harvester widely used in Sri Lankan paddy fields. It cuts, threshes, and cleans the grain in a single pass, reducing harvest time and labour sharply.', 40000.00, 'available', 'images/Kubota-DC-70-Plus-Combine-Harvester.jpg', '2026-08-03 04:27:00'),
(5, 1, 2, 'Self-Propelled Paddy Reaper', 'A walk-behind reaper that cuts and lays paddy neatly for collection. It is a cost effective option for small and medium plots where a full combine is not needed.', 7500.00, 'available', 'images/Self-Propelled-Paddy-Reaper.jpg', '2026-08-03 04:29:16'),
(6, 1, 2, 'Motorised Paddy Thresher', 'A motorised thresher that separates paddy grain from the straw after reaping. It raises output and reduces the manual effort of traditional threshing.', 6000.00, 'maintenance', 'images/Motorised-Paddy-Thresher.jpg', '2026-08-03 04:30:52'),
(7, 1, 3, 'Honda WB30 Water Pump (3 inch)', 'A 3 inch petrol water pump for moving water from canals, wells, or tanks into the field. It is reliable, portable, and quick to start for daily irrigation.', 2500.00, 'available', 'images/Honda-WB30-Water-Pump-(3-inch).jpg', '2026-08-03 04:32:24'),
(8, 1, 3, 'Diesel Water Pump (4 inch, 7 HP)', 'A 7 HP 4 inch diesel pump with a high flow rate for irrigating larger fields or filling storage tanks. It is economical to run over long pumping hours.', 3800.00, 'available', 'images/Diesel-Water-Pump-(4-inch,-7-HP).jpg', '2026-08-03 04:34:07'),
(9, 1, 3, 'Portable Sprinkler Irrigation Set', 'A movable sprinkler set with pipes and sprinkler heads for even watering of vegetable and field crops. It sets up quickly and shifts easily between plots.', 4500.00, 'available', 'images/portable-sprinkler-set.jpg', '2026-08-03 04:36:27'),
(10, 1, 4, 'Three-Disc Plough (Tractor-Mounted)', 'A tractor-mounted three-disc plough for breaking and turning hard or uncultivated soil. It is well suited to the first deep tillage of the season.', 6000.00, 'available', 'images/Three-Disc-Plough-(Tractor-Mounted).jpg', '2026-08-03 04:38:07'),
(11, 1, 4, 'Rotavator 6ft (Rotary Tiller)', 'A tractor-mounted rotary tiller that breaks clods and prepares a fine, level seedbed in one pass. It is ideal for puddling paddy fields and preparing vegetable beds.', 7500.00, 'available', 'images/Rotavator-6ft-(Rotary-Tiller).jpg', '2026-08-03 04:39:45'),
(12, 1, 4, 'Nine-Tine Cultivator (Tractor-Mounted)', 'A nine-tine tractor-mounted cultivator for secondary tillage, weeding between rows, and loosening the topsoil before planting.', 5000.00, 'available', 'images/Nine-Tine-Cultivator-(Tractor-Mounted).jpg', '2026-08-03 04:42:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','farmer','owner') DEFAULT 'farmer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `full_name`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'admin', 'System Administrator', 'admin@farmlend.lk', '$2b$12$e0Uot5ZAb2DJMgz9hZX/g.YF2Gu5VTxD4R3UV2rndF.35BtyDRhda', 'admin', '2026-08-01 20:53:13'),
(2, 'uoc', 'Default User', 'uoc@farmlend.lk', '$2b$12$wmPMoL8NoaGrVkp/2TmMA..izWlbfqO5aIXLDJaEE1GvhA/ymnWW2', 'farmer', '2026-08-01 20:53:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `equipment_id` (`equipment_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `equipment_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `equipment_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
