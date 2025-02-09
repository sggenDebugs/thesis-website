-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 09, 2025 at 03:31 PM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u388284544_server`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_signed_in_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `first_name` varchar(45) NOT NULL,
  `last_name` varchar(45) NOT NULL,
  `email` varchar(150) NOT NULL,
  `gov_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `created_at`, `last_signed_in_at`, `first_name`, `last_name`, `email`, `gov_id`) VALUES
(1, '2024-12-14 09:29:20', '2024-12-31 12:04:20', 'John', 'Doe', 'mikechow@gmail.com', 'test2.com'),
(2, '2025-01-31 13:29:58', '2025-01-31 13:29:58', 'Inigo', 'Gonzalez', 'inigopgonzalez@su.edu.ph', 'test'),
(4, '2025-02-04 07:07:46', '2025-02-04 07:07:46', 'Arise', 'Fromthedead', 'arisefromthedead@gmail.com', 'arise.png');

-- --------------------------------------------------------

--
-- Table structure for table `bikes`
--

CREATE TABLE `bikes` (
  `id` int(11) NOT NULL,
  `rider_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `size` enum('small','large') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_used_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','inactive','under maintenance','removed') NOT NULL DEFAULT 'inactive',
  `longitude` float DEFAULT NULL,
  `latitude` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nfc_tags`
--

CREATE TABLE `nfc_tags` (
  `id` int(11) NOT NULL,
  `uid` varchar(255) NOT NULL DEFAULT uuid(),
  `client_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','inactive','lost','blocked') NOT NULL DEFAULT 'inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nfc_tags`
--

INSERT INTO `nfc_tags` (`id`, `uid`, `client_id`, `admin_id`, `created_at`, `updated_at`, `status`) VALUES
(2, '9db81ac8-b9ba-11ef-8baa-cef870491cb1', NULL, NULL, '2024-12-14 09:27:43', '2024-12-14 09:30:19', 'inactive');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `invoice_num` int(11) NOT NULL,
  `payment_method` enum('Gcash','Credit Card','Cash') NOT NULL,
  `amount_due` decimal(6,2) NOT NULL,
  `status` enum('pending','failed','completed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_signed_in_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `first_name` varchar(45) NOT NULL,
  `last_name` varchar(45) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact_num` varchar(15) DEFAULT NULL,
  `gov_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `created_at`, `last_signed_in_at`, `first_name`, `last_name`, `email`, `password`, `contact_num`, `gov_id`) VALUES
(7, '2025-02-04 21:53:01', '2025-02-04 21:53:01', 'George', 'Carlos', 'georgecarlos@gmail.com', '$2y$10$HX3fkb1tQUGH/9yWCJPZJuEylSOXVogL7BFpanaanLDbEtgaQGyMO', '', 'georgecarlos.com'),
(8, '2025-02-04 21:57:14', '2025-02-04 21:57:14', 'George', 'Carlos', 'gonzalezinigo24@yahoo.com', '$2y$10$Zo3oTVu3E8IR/3USpFQkKutVfvJuSQMx6KphULjPtXKHR6Gwluium', '', 'gonzalez.com'),
(9, '2025-02-04 22:06:11', '2025-02-04 22:06:11', 'jennifer', 'gonzalez', 'jg@gmail.com', '$2y$10$BksH0d4GW4oek6yjMm9n.Oeyp0M1c7yNkyKwU9f8N3cMNjZMqaJIG', '', 'jennifer.com'),
(10, '2025-02-04 15:02:17', '2025-02-04 15:02:17', 'john', 'jaymar', 'johnjaymar@gmail.com', '$2y$10$wlvZmU5APYNQAtUfRcHwCuzBStOmzm4nM35K562Fhaw1aNDHGNJFu', '09653352456', 'john_jaymar.png'),
(11, '2025-02-07 01:55:22', '2025-02-07 01:55:22', 'George Bernard', 'Carlos', 'georgeecarlos@su.edu.ph', '$2y$10$2AZv6YyF.0eue2/yO4vWy.bkqFheGPoXRqx5iWiwnlXaz4epUChUm', '', 'test'),
(12, '2025-02-07 16:54:11', '2025-02-07 16:54:11', 'Inigo', 'Gonzalez', 'inigopgonzalez@su.edu.ph', '$2y$10$A4gx16i7ZwYFinIY9Z5nvO3LEy2bVpbOr6lKkNUiJVlREEMt8SzLC', '939493439', 'ieeiei');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`),
  ADD UNIQUE KEY `gov_id_UNIQUE` (`gov_id`);

--
-- Indexes for table `bikes`
--
ALTER TABLE `bikes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id_idx` (`rider_id`),
  ADD KEY `fk_bikes_nfc_tags1_idx` (`tag_id`);

--
-- Indexes for table `nfc_tags`
--
ALTER TABLE `nfc_tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uid_UNIQUE` (`uid`),
  ADD KEY `fk_nfc_cards_1_idx` (`client_id`),
  ADD KEY `fk_nfc_tags_1` (`admin_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_num_UNIQUE` (`invoice_num`),
  ADD KEY `fk_transactions_users1_idx` (`client_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gov_id_UNIQUE` (`gov_id`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bikes`
--
ALTER TABLE `bikes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `nfc_tags`
--
ALTER TABLE `nfc_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bikes`
--
ALTER TABLE `bikes`
  ADD CONSTRAINT `fk_bikes_nfc_tags1` FOREIGN KEY (`tag_id`) REFERENCES `nfc_tags` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_users_1` FOREIGN KEY (`rider_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `nfc_tags`
--
ALTER TABLE `nfc_tags`
  ADD CONSTRAINT `fk_nfc_tags_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_users` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_transactions_users1` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
