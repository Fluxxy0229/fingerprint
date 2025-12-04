-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 04, 2025 at 04:50 PM
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
-- Database: `fingerprint_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `senior_list`
--

CREATE TABLE `senior_list` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `address` varchar(100) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` enum('Male','Female','Prefer not to say') NOT NULL,
  `civil_status` enum('Single','Married','Separated','Widowed') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `senior_list`
--

INSERT INTO `senior_list` (`id`, `full_name`, `age`, `address`, `contact_number`, `birthdate`, `gender`, `civil_status`, `created_at`) VALUES
(1, 'Mark Vince F. Gammad', 21, 'Marasat Grande, San Mateo, Isabela', '09876543210', '2004-10-02', 'Male', 'Single', '2025-12-04 07:37:37'),
(2, 'Jhoe Renz G. Nitura', 70, 'Harana, Luna, Isabela', '09876543210', '2003-10-04', 'Male', 'Single', '2025-12-04 07:38:18'),
(3, 'Mark Oaren S. Fayosal', 71, 'Malasin, San Mateo, Isabela', '09876543210', '2001-10-19', 'Male', 'Single', '2025-12-04 07:38:51'),
(4, 'Maryphil S. Galapon', 70, 'Cabatuan, Isabela', '09876543210', '2000-01-01', 'Male', 'Single', '2025-12-04 07:39:20'),
(5, 'Desiree Quijano', 75, 'Alicia, Isabela', '09876543210', '9999-09-09', 'Female', 'Single', '2025-12-04 07:43:49'),
(6, 'Fluxxy Dev', 80, 'Marasat Grande, San Mateo, Isabela', '09876543210', '1940-10-02', 'Male', 'Single', '2025-12-04 07:49:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Staff') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'BBSAISC Admin', '$2y$10$qyBawxB.oByBpTZne0vC4uZ2ZxRPSf8akU0mf5bKAYBhWIe8U46RG', 'Admin'),
(2, 'Mateo Drug Store', '$2y$10$JwZEQ0PX2CEq9vvqzMG5KOdGz29cpAmDiFPuBrxVH1WApTmxEwsRW', 'Staff');

-- --------------------------------------------------------

--
-- Table structure for table `user_activities`
--

CREATE TABLE `user_activities` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `action` varchar(80) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_activities`
--

INSERT INTO `user_activities` (`id`, `user_id`, `username`, `action`, `details`, `created_at`) VALUES
(1, 1, 'BBSAISC Admin', 'add_senior', 'Mark Vince F. Gammad', '2025-12-04 07:37:37'),
(2, 1, 'BBSAISC Admin', 'add_senior', 'Jhoe Renz G. Nitura', '2025-12-04 07:38:18'),
(3, 1, 'BBSAISC Admin', 'add_senior', 'Mark Oaren S. Fayosal', '2025-12-04 07:38:51'),
(4, 1, 'BBSAISC Admin', 'add_senior', 'Maryphil S. Galapon', '2025-12-04 07:39:20'),
(5, 1, 'BBSAISC Admin', 'add_senior', 'Desiree Quijano', '2025-12-04 07:43:49'),
(6, 1, 'BBSAISC Admin', 'add_senior', 'Fluxxy Dev', '2025-12-04 07:49:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `senior_list`
--
ALTER TABLE `senior_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_activities`
--
ALTER TABLE `user_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_at` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `senior_list`
--
ALTER TABLE `senior_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_activities`
--
ALTER TABLE `user_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
