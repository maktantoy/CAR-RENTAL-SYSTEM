-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2024 at 02:33 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `car_rental_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `persons` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `pickup_location` varchar(255) NOT NULL,
  `pickup_date` date NOT NULL,
  `pickup_time` time NOT NULL,
  `return_date` date NOT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `name`, `phone`, `persons`, `car_id`, `pickup_location`, `pickup_date`, `pickup_time`, `return_date`, `booking_date`, `status`) VALUES
(2, 'tristan', '09108025729', 2, 0, 'day-as cordova cebu', '2024-11-27', '12:00:00', '2024-12-13', '2024-11-27 03:34:44', 'pending'),
(33, 'maki', '0944654654', 2, 0, 'day-as cordova cebu', '2024-11-29', '20:18:52', '2024-11-30', '2024-11-28 08:18:52', 'pending'),
(34, 'jhon', '0909012909', 2, 0, 'bangbang', '2024-11-27', '14:00:00', '2024-11-30', '2024-11-28 08:29:52', 'pending'),
(38, 'shin aika', '09108025729', 2, 0, 'buagsong cordova cebu', '2024-11-29', '21:46:00', '2024-12-01', '2024-11-29 08:47:21', 'pending'),
(39, 'randy escartin', '09108025729', 5, 0, 'buagsong cordova cebu', '2024-11-29', '22:47:00', '2024-11-30', '2024-11-29 08:47:40', 'pending'),
(47, 'maki', '09108025729', 1, 0, 'day-as cordova cebu', '2024-12-11', '17:27:00', '2024-12-13', '2024-12-11 08:26:55', 'pending'),
(48, 'mark b inoc', '09108025729', 2, 0, 'day-as cordova cebu', '2024-12-11', '19:29:00', '2024-12-13', '2024-12-11 08:29:28', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `model_year` int(11) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `passengers` int(11) NOT NULL,
  `transmission` enum('Automatic','Manual','CVT','Semi-Automatic','Dual-Clutch','Automated Manual','Tiptronic') NOT NULL DEFAULT 'Automatic',
  `image_url` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL,
  `horsepower` int(11) NOT NULL DEFAULT 0,
  `mileage` int(11) NOT NULL DEFAULT 0,
  `fuel_type` varchar(50) NOT NULL DEFAULT 'Petrol',
  `engine_size` varchar(50) NOT NULL DEFAULT '2.0L',
  `top_speed` int(11) NOT NULL DEFAULT 0,
  `acceleration_0_60` decimal(4,1) NOT NULL DEFAULT 0.0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `name`, `price_per_day`, `model_year`, `brand`, `passengers`, `transmission`, `image_url`, `category`, `horsepower`, `mileage`, `fuel_type`, `engine_size`, `top_speed`, `acceleration_0_60`) VALUES
(6, '911 carrera gts', '3000.00', 2023, 'sedan', 4, '', 'images.jpg', 'family', 0, 0, 'Petrol', '2.0L', 0, '0.0'),
(8, 'Ford fiesta', '1000.00', 2015, 'ford', 5, '', 'ford.jpg', 'family', 0, 0, 'Petrol', '2.0L', 0, '0.0'),
(9, 'SUV', '1500.00', 2019, 'SUV', 6, '', 'suv.avif', 'family', 300, 33, 'Petrol', '2.0L', 0, '0.0'),
(10, 'Honda', '2000.00', 2020, 'Sedan', 5, '', 'sedan.webp', 'family', 0, 0, 'Petrol', '2.0L', 0, '0.0'),
(11, 'Honda', '1200.00', 2023, 'sedan', 4, '', 'sedan2.png', 'family', 0, 0, 'Petrol', '2.0L', 0, '0.0'),
(12, 'Kelley Blue Book', '1500.00', 2024, 'SUV', 7, '', 'suv2.avif', 'family', 0, 0, 'Petrol', '2.0L', 0, '0.0'),
(13, 'Toyota HT Auto', '5000.00', 2024, 'TOYOTA', 10, '', 'honda.jpg', 'family', 0, 0, 'Diesel', '2.0L', 0, '0.0'),
(14, 'Kia', '1200.00', 2023, 'Kia Sportage', 3, '', 'uploads/cars/674ecadcd0db3.jpg', '', 0, 0, 'Diesel', '2.0L', 0, '0.0');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `amount`, `status`, `created_at`) VALUES
(1, 17, '2000.00', 'pending', '2024-12-11 08:29:36');

-- --------------------------------------------------------

--
-- Table structure for table `rentals`
--

CREATE TABLE `rentals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `status` enum('active','completed','cancelled') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'Car Rental System', '2024-12-09 04:37:46', '2024-12-09 04:37:46'),
(2, 'contact_email', 'admin@example.com', '2024-12-09 04:37:46', '2024-12-09 04:37:46'),
(3, 'maintenance_mode', 'false', '2024-12-09 04:37:46', '2024-12-09 04:37:46');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` varchar(20) DEFAULT 'user',
  `is_admin` tinyint(1) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `createdd_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `name`, `created_at`, `first_name`, `last_name`, `phone`, `address`, `role`, `is_admin`, `active`, `createdd_at`) VALUES
(1, 'markinoc@gmail.com', '$2y$10$dz7gX5dC5J3lH3qFsacBO.ChSFYWg8uXrQBq2G83gqvIMl7SzxJTa', 'mark', '2024-11-21 05:53:33', '', '', NULL, NULL, 'user', 0, 1, '2024-12-03 08:48:54'),
(2, 'otoy123@gmail.com', '$2y$10$Cddt2OwJmnU3T06vbZhxT.kDiHAp7y9BBYM572SsZH3Rv45THOdZW', 'otoy123', '2024-11-26 08:11:55', 'jhon', 'ritania', '0909012909', 'day-as cordova cebu', 'user', 0, 1, '2024-12-03 08:48:54'),
(3, 'markinoc83@gmail.com', '$2y$10$ceyD7hPFx0Fr6XA3VBas.uDTj.WvjuSkrg.EmfwzfiiO0luF1qjYW', 'markinoc83', '2024-11-27 04:54:08', 'mark', 'inoc', '09108025729', 'day-as cordova cebu', 'user', 0, 1, '2024-12-03 08:48:54'),
(4, 'jamesb@gmail.com', '$2y$10$mZm9/wzRpaTf1PzAKuXKou2M1nW9Jb28Skg/gRfF85jJnBQCdqDT.', 'jamesb', '2024-11-27 05:05:27', 'james', 'branzuela', NULL, NULL, 'user', 0, 1, '2024-12-03 08:48:54'),
(5, 'randyescartin@gmail.com', '$2y$10$pmuxWu02dJ2lLYy3FQJct.lMihFwGPtNvKQWExsYauLxfJ/cwz9xO', 'randyescartin', '2024-11-27 05:10:59', 'randy', 'escartin', '0945465484984', 'day-as cordova cebu', 'user', 0, 1, '2024-12-03 08:48:54'),
(6, 'shinaika@gmail.com', '$2y$10$H49lpO2wLEKDxMK1oTi1JOYfSx59fMjtbjt.XS/5qjf92FRYS5p3S', 'shinaika', '2024-11-27 05:12:53', 'shin aika', 'pagobo', '09456465487968', 'buagsong cordova cebu', 'user', 0, 1, '2024-12-03 08:48:54'),
(7, 'jhonotoy@gmail.com', '$2y$10$P6cyu8DTloYW1hyWDoXDzePIMSczqLFKm96ly9Y.nsBGXXNgzvzOq', 'jhonotoy', '2024-11-28 08:35:52', 'jhon rhead', 'ritania', '0909245678', 'alegria cordova cebu', 'user', 0, 1, '2024-12-03 08:48:54'),
(13, 'kodichocopao@gmail.com', '$2y$10$FpZfjJ254ruarqWF.Qye5uSOmu3o1I9guTqcKA0Gx/yEJHcKEITzm', 'kodichocopao', '2024-12-03 06:37:42', 'kodi', 'chocopao', '09154566930', 'buagsong cordova cebu', 'user', 0, 1, '2024-12-03 08:48:54'),
(15, 'admin@example.com', '$2y$10$idROp1CIEeLW76l2P0M27.S8j.CWVPq8aeoYe4F6Q2X3V0k./ZwIy', 'admin', '2024-12-03 06:43:52', 'Admin', 'User', '1234567890', 'Admin Address', 'admin', 1, 1, '2024-12-03 08:48:54'),
(16, 'maktantoy1@gmail.com', '$2y$10$4NHuI44wcXSANWK2y4IyD.Evk3ORGNE0TPNUImpSOtOensqAsvqwm', 'maktantoy1', '2024-12-03 09:14:24', '', '', NULL, NULL, 'user', 0, 1, '2024-12-03 09:14:24'),
(17, 'adminmark1@gmail.com', '$2y$10$/1SFOpzpPsh.JUIlFkAbw.VHyOLYyZP/ZXFZ2QlxFG6f2jcunrSGC', 'adminmark1', '2024-12-03 09:14:49', '', '', NULL, NULL, 'user', 1, 1, '2024-12-03 09:14:49'),
(18, 'misochocopao@gmail.com', '$2y$10$YwOqPJSMJZ5Vn9AI9Tx9PeWWfkBIyJlgrwTEqRCRaXPKL1ijaz0Xu', 'misochocopao', '2024-12-09 05:33:58', '', '', NULL, NULL, 'user', 0, 1, '2024-12-09 05:33:58'),
(19, 'makichocopao@gmail.com', '$2y$10$1Hzk.qXVwgdlI9jYxuMnC.xk.xnqc0zEyxWThIfyrTHuc/hwelt0u', 'makichocopao', '2024-12-09 05:34:26', '', '', NULL, NULL, 'user', 0, 1, '2024-12-09 05:34:26'),
(20, 'garfieldchocopao@gmail.com', '$2y$10$saGALALJN9D2Oc95up.dIuNn4xbQxPdueqeFXt2uN6nwJ1w1qCxwS', 'garfieldchocopao', '2024-12-09 05:34:50', '', '', NULL, NULL, 'user', 0, 1, '2024-12-09 05:34:50'),
(21, 'nalachocopao@gmail.com', '$2y$10$KxD57NbnryHOQg2f8gRPZeRs.caEHZI8Cj/xV1WJXQzCcsu0vyuwK', 'nalachocopao', '2024-12-09 05:35:36', '', '', NULL, NULL, 'user', 0, 1, '2024-12-09 05:35:36'),
(22, 'makochocopao@gmail.com', '$2y$10$WuyJoruKUCfEYKSOfVl2quSJC.iXc9R/.vHXVKGKorBYoYf3w3hmC', 'makochocopao', '2024-12-09 05:35:54', '', '', NULL, NULL, 'user', 0, 1, '2024-12-09 05:35:54'),
(23, 'kankanchocopao@gmail.com', '$2y$10$u.fqPNvlG6FZeVo0ljP1le83lXRQhDikiZpmuhOsnljt5gz7T84Oy', 'kankanchocopao', '2024-12-09 05:39:16', '', '', NULL, NULL, 'user', 0, 1, '2024-12-09 05:39:16'),
(24, 'tatiichocopao@gmail.com', '$2y$10$Kq5riXEj5ae4Zn6AmyU0MO5DNn2R.mWp/5DpHkJg9dugFo3KnwewK', 'tatiichocopao', '2024-12-09 05:39:41', '', '', NULL, NULL, 'user', 0, 1, '2024-12-09 05:39:41'),
(25, 'bowiichocopao@gmail.com', '$2y$10$CsHKOCB1dkuwXZdVSWawh.1vPlALFb.ZeQ2IcioILZgKqGESIkBle', 'bowiichocopao', '2024-12-09 05:39:59', '', '', NULL, NULL, 'user', 0, 1, '2024-12-09 05:39:59'),
(26, 'aikachocopao@gmail.com', '$2y$10$dE6sKirNlkWZCncORbk7e.FMLCTBeE8VYQ4Z86jtB08RZWemhGYD2', 'aika', '2024-12-09 05:41:13', '', '', NULL, NULL, 'user', 0, 1, '2024-12-09 05:41:13'),
(27, 'makiboychocopao@gmail.com', '$2y$10$0DTgvnxU76jrNzQ7e2HaPefBdbAAUFzHoIyW4yzrMi9PPVmf.LUdq', 'makiboychocopao', '2024-12-09 05:41:29', '', '', NULL, NULL, 'user', 0, 1, '2024-12-09 05:41:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rentals`
--
ALTER TABLE `rentals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `car_id` (`car_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_user_email` (`email`),
  ADD KEY `idx_user_active` (`active`),
  ADD KEY `idx_user_created_at` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rentals`
--
ALTER TABLE `rentals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `rentals`
--
ALTER TABLE `rentals`
  ADD CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `rentals_ibfk_2` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Example insert statement for payments
INSERT INTO `payments` (`user_id`, `amount`, `payment_method`, `status`, `created_at`) VALUES
(1, '2000.00', 'GCash', 'completed', NOW()),
(2, '1500.00', 'Credit Card', 'pending', NOW());