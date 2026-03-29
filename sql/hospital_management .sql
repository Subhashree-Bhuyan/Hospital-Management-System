-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 29, 2026 at 08:24 PM
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
-- Database: `hospital_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `appointment_time` time DEFAULT NULL,
  `status` enum('Pending','Completed','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `patient_id`, `doctor_id`, `appointment_date`, `appointment_time`, `status`, `created_at`) VALUES
(3, 1, 1, '2026-03-14', '08:00:00', 'Pending', '2026-03-13 15:03:49'),
(4, 1, 1, '2026-03-14', '10:00:00', 'Pending', '2026-03-13 15:16:20'),
(5, 3, 1, '2026-03-14', '09:00:00', 'Pending', '2026-03-13 15:35:54'),
(6, 3, 1, '2026-03-13', '08:00:00', 'Pending', '2026-03-13 15:41:19'),
(7, 1, 1, '2026-05-02', '08:00:00', 'Pending', '2026-03-29 09:18:36'),
(8, 1, 1, '2026-05-02', '08:30:00', 'Pending', '2026-03-29 09:18:43'),
(9, 1, 1, '2026-05-02', '09:00:00', 'Pending', '2026-03-29 09:18:48'),
(10, 1, 1, '2026-05-02', '09:30:00', 'Pending', '2026-03-29 09:18:52'),
(11, 1, 1, '2026-05-02', '10:00:00', 'Pending', '2026-03-29 09:18:57'),
(12, 1, 1, '2026-05-02', '10:30:00', 'Pending', '2026-03-29 09:24:38'),
(16, 3, 1, '2026-05-02', '11:00:00', 'Pending', '2026-03-29 09:30:15'),
(17, 1, 1, '2026-03-30', '10:00:00', 'Pending', '2026-03-29 14:10:32'),
(21, 1, 1, '2026-03-30', '09:30:00', 'Pending', '2026-03-29 15:09:54'),
(22, 1, 1, '2026-03-30', '09:00:00', 'Pending', '2026-03-29 15:22:26'),
(23, 1, 1, '2026-03-29', '11:30:00', 'Pending', '2026-03-29 18:01:01');

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `bill_id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `consultation_fee` decimal(10,2) DEFAULT NULL,
  `test_fee` decimal(10,2) DEFAULT NULL,
  `medicine_fee` decimal(10,2) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `paid_amount` decimal(10,2) DEFAULT NULL,
  `pending_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('Pending','Partial','Paid') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bills`
--

INSERT INTO `bills` (`bill_id`, `appointment_id`, `patient_id`, `doctor_id`, `consultation_fee`, `test_fee`, `medicine_fee`, `total_amount`, `paid_amount`, `pending_amount`, `status`, `created_at`) VALUES
(3, 3, 1, 1, 500.00, NULL, NULL, 500.00, 0.00, 500.00, 'Pending', '2026-03-29 14:45:22'),
(4, 21, 1, 1, 500.00, NULL, NULL, 500.00, 0.00, 500.00, 'Pending', '2026-03-29 15:09:54'),
(5, 22, 1, 1, 500.00, NULL, NULL, 500.00, 0.00, 500.00, 'Pending', '2026-03-29 15:22:26');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`) VALUES
(1, 'Cardiology'),
(2, 'Neurology'),
(3, 'Orthopedics'),
(4, 'Pediatrics'),
(5, 'Dermatology');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `experience` int(11) DEFAULT NULL,
  `available_days` varchar(100) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `consultation_fee` decimal(10,2) DEFAULT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `title` enum('Mr','Mrs','Miss','Ms','Dr','Other') DEFAULT 'Dr'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `user_id`, `first_name`, `last_name`, `department_id`, `phone`, `experience`, `available_days`, `start_time`, `end_time`, `consultation_fee`, `middle_name`, `date_of_birth`, `title`) VALUES
(1, 2, 'Amit', 'Sharma', 1, '9876543210', 10, 'Monday,Wednesday,Friday', '10:00:00', '14:00:00', 500.00, NULL, NULL, 'Dr');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_notes`
--

CREATE TABLE `doctor_notes` (
  `note_id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `prescription` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `patient_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `title` enum('Mr','Mrs','Miss','Ms','Dr','Other') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`patient_id`, `user_id`, `first_name`, `last_name`, `gender`, `phone`, `address`, `created_at`, `email`, `password`, `middle_name`, `date_of_birth`, `title`) VALUES
(1, NULL, 'Subhashree', 'Bhuyan', NULL, '8658097060', NULL, '2026-03-12 14:28:46', 'subhashreebhuyan918@gmail.com', 'deepa@123', NULL, NULL, NULL),
(3, NULL, 'Deboleena', 'Ganguly', NULL, '101213141516171', NULL, '2026-03-13 15:30:06', 'deboleena@gmail.com', 'debo@123', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','doctor','patient') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Admin', 'admin@hospital.com', 'admin123', 'admin', '2026-03-05 11:55:29'),
(2, 'Dr Sharma', 'drsharma@hospital.com', 'doctor123', 'doctor', '2026-03-05 11:56:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`bill_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `doctor_notes`
--
ALTER TABLE `doctor_notes`
  ADD PRIMARY KEY (`note_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `bill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `doctor_notes`
--
ALTER TABLE `doctor_notes`
  MODIFY `note_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`);

--
-- Constraints for table `bills`
--
ALTER TABLE `bills`
  ADD CONSTRAINT `bills_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`);

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `doctors_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`);

--
-- Constraints for table `doctor_notes`
--
ALTER TABLE `doctor_notes`
  ADD CONSTRAINT `doctor_notes_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`);

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `patients_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
