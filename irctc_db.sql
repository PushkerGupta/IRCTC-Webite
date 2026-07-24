-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 21, 2026 at 10:30 AM
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
-- Database: `irctc_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `items` text NOT NULL,
  `restaurant_name` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'CONFIRMED',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pnr_master`
--

CREATE TABLE `pnr_master` (
  `id` int(11) NOT NULL,
  `pnr` varchar(20) NOT NULL,
  `train_name` varchar(100) DEFAULT NULL,
  `from_station` varchar(100) DEFAULT NULL,
  `to_station` varchar(100) DEFAULT NULL,
  `journey_date` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pnr_master`
--

INSERT INTO `pnr_master` (`id`, `pnr`, `train_name`, `from_station`, `to_station`, `journey_date`) VALUES
(2147483647, '1234567890', 'Amarkantak express', 'Jabalpur', 'Bhopal', '22-08-2026');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `pnr` varchar(10) NOT NULL,
  `train_name` varchar(100) DEFAULT NULL,
  `station` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `pnr`, `train_name`, `station`) VALUES
(1, '1234567890', 'Rajdhani Express', 'New Delhi');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `win_bin_points` int(11) DEFAULT 0
) ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `mobile`, `email`, `created_at`, `win_bin_points`) VALUES
(1, 'Pushker gupta', '06267695369', 'pushker176@gmail.com', '2026-06-20 08:25:09', 0),
(2, 'abcd', '9399311425', 'abcd@gmail.com', '2026-06-20 08:26:39', 0),
(3, 'Gupta', '9406584185', 'gupta@gmail.com', '2026-06-20 08:50:45', 5),
(4, 'Vaishnavi', '9826164902', 'vt@gmail.com', '2026-06-20 15:09:36', 6);

-- --------------------------------------------------------

--
-- Table structure for table `winbin_ledger`
--

CREATE TABLE `winbin_ledger` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activity` varchar(255) DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `win_bin`
--

CREATE TABLE `win_bin` (
  `id` int(11) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `pnr_number` varchar(15) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `points` int(11) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `win_bin`
--

INSERT INTO `win_bin` (`id`, `phone_number`, `pnr_number`, `user_name`, `points`, `updated_at`) VALUES
(1, '9826164902', '123456789', 'Rahul Sharma', 5, '2026-06-20 17:23:59'),
(2, '8765432109', '5678901234', 'Amit Verma', 12, '2026-06-20 17:20:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pnr_master`
--
ALTER TABLE `pnr_master`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pnr` (`pnr`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pnr` (`pnr`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mobile` (`mobile`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `winbin_ledger`
--
ALTER TABLE `winbin_ledger`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `win_bin`
--
ALTER TABLE `win_bin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_phone_pnr` (`phone_number`,`pnr_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pnr_master`
--
ALTER TABLE `pnr_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2147483648;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `winbin_ledger`
--
ALTER TABLE `winbin_ledger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `win_bin`
--
ALTER TABLE `win_bin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
