-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 20, 2023 at 06:19 PM
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
-- Database: `inventorydb`
--

-- --------------------------------------------------------

--
-- Table structure for table `barrowed_equipment`
--

CREATE TABLE `barrowed_equipment` (
  `barrow_id` int(11) NOT NULL COMMENT 'Primary Key',
  `costumer_id` int(11) NOT NULL COMMENT 'Foreign Key',
  `equipment_id` int(11) NOT NULL COMMENT 'Foreign Key',
  `barrow_status` tinyint(5) NOT NULL DEFAULT 1 COMMENT '1 = Pending| 0 = Complete',
  `barrow_date` datetime NOT NULL DEFAULT current_timestamp(),
  `return_date` datetime DEFAULT NULL,
  `barrow_qty` int(11) NOT NULL,
  `returned_qty` int(100) NOT NULL,
  `subtotal_amount` int(100) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Issued by | Foreign Key',
  `admin_id` int(255) DEFAULT NULL COMMENT 'FK'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barrowed_equipment`
--

INSERT INTO `barrowed_equipment` (`barrow_id`, `costumer_id`, `equipment_id`, `barrow_status`, `barrow_date`, `return_date`, `barrow_qty`, `returned_qty`, `subtotal_amount`, `user_id`, `admin_id`) VALUES
(78, 211, 119, 0, '2023-10-19 01:03:25', '2023-10-19 21:00:03', 0, 8, 0, 109, 109),
(79, 211, 126, 0, '2023-10-19 01:03:25', '2023-10-19 21:03:24', 0, 4, 0, 109, 109),
(85, 211, 119, 0, '2023-10-19 21:08:12', '2023-10-19 21:18:03', 0, 2, 0, 109, 109),
(86, 211, 119, 0, '2023-10-19 21:37:25', '2023-10-19 21:39:57', 0, 2, 0, 109, 109),
(87, 211, 119, 0, '2023-10-19 21:41:31', '2023-10-19 21:57:08', 0, 2, 0, 109, 109),
(88, 211, 119, 0, '2023-10-19 21:58:21', '2023-10-19 22:03:15', 0, 2, 0, 109, 109),
(89, 211, 126, 0, '2023-10-19 21:59:38', '2023-10-19 22:03:15', 0, 1, 0, 109, 109),
(90, 211, 119, 1, '2023-10-19 23:21:25', NULL, 2, 0, 5000, 109, NULL),
(91, 211, 126, 0, '2023-10-19 23:21:25', '2023-10-20 01:10:33', 0, 1, 0, 109, 109),
(98, 213, 119, 1, '2023-10-20 03:54:20', NULL, 1, 0, 2500, 109, NULL),
(99, 213, 126, 0, '2023-10-20 03:54:20', '2023-10-20 03:55:03', 0, 1, 0, 109, 109);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL COMMENT 'Must be Unique',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL COMMENT 'Foreign key',
  `category_status` int(2) NOT NULL DEFAULT 1 COMMENT '1=Available | 0=NOT'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `date_added`, `user_id`, `category_status`) VALUES
(84, 'Chair', '2023-10-15 17:10:39', 133, 1),
(85, 'Laptop', '2023-10-16 19:10:50', 134, 1),
(86, 'Mouse', '2023-10-16 22:17:58', 109, 1);

-- --------------------------------------------------------

--
-- Table structure for table `costumers`
--

CREATE TABLE `costumers` (
  `costumer_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `phone_number` varchar(50) NOT NULL,
  `school_id` int(50) NOT NULL COMMENT 'Foreign Key',
  `role_position` text NOT NULL,
  `costumer_status` tinyint(2) NOT NULL DEFAULT 1 COMMENT '1=Allowed | 0=Block',
  `admin_id` int(100) NOT NULL COMMENT 'Added by | Foreign Key'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `costumers`
--

INSERT INTO `costumers` (`costumer_id`, `name`, `phone_number`, `school_id`, `role_position`, `costumer_status`, `admin_id`) VALUES
(211, 'testbarrower1', '09887625544', 19, 'Student', 1, 109),
(213, 'testborrower2', '09247926611', 19, 'Student', 1, 109),
(214, 'testborrower3', '09782648523', 21, 'Student', 1, 109);

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `category_id` int(50) NOT NULL COMMENT 'Foreign key',
  `equipment_name` varchar(100) NOT NULL COMMENT 'Must be Unique',
  `type_id` int(10) NOT NULL COMMENT 'Foreign key',
  `location_id` int(10) NOT NULL COMMENT 'Foreign key',
  `roomcode_id` int(11) NOT NULL COMMENT 'FK',
  `unit_id` int(10) NOT NULL COMMENT 'Foreign key',
  `price` int(100) NOT NULL,
  `stock` int(100) NOT NULL,
  `in_use` int(100) NOT NULL COMMENT 'Equipment Used',
  `quantity` int(100) NOT NULL COMMENT 'Equipment Available',
  `amount` int(100) NOT NULL,
  `status` int(2) NOT NULL DEFAULT 1 COMMENT '1=Available | 0=NOT',
  `conditions` int(250) DEFAULT NULL COMMENT 'Good | Critical',
  `equipment_img` varchar(255) NOT NULL DEFAULT 'noimage',
  `img_extension` varchar(10) NOT NULL DEFAULT 'png',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(255) NOT NULL COMMENT 'Added By | Foreign key',
  `date_updated` datetime DEFAULT NULL COMMENT 'Date modified',
  `m_userid` int(255) DEFAULT NULL COMMENT 'Modified By'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `category_id`, `equipment_name`, `type_id`, `location_id`, `roomcode_id`, `unit_id`, `price`, `stock`, `in_use`, `quantity`, `amount`, `status`, `conditions`, `equipment_img`, `img_extension`, `date_added`, `user_id`, `date_updated`, `m_userid`) VALUES
(119, 84, 'Black High Chair', 35, 19, 1, 21, 2500, 20, 3, 17, 50000, 1, 10, 'noimage', 'png', '2023-10-16 02:22:56', 133, '2023-10-19 01:08:55', 109),
(124, 85, 'NEC Laptop', 37, 21, 4, 21, 5700, 8, 0, 8, 45600, 1, 10, 'noimage', 'png', '2023-10-16 20:02:19', 109, '2023-10-16 20:03:48', 109),
(125, 84, 'Mono Block', 36, 22, 1, 21, 2000, 30, 0, 30, 60000, 1, 10, 'noimage', 'png', '2023-10-16 20:07:40', 109, NULL, NULL),
(126, 86, 'Micro Mouse', 37, 19, 4, 21, 800, 20, 0, 20, 16000, 1, 10, 'noimage', 'png', '2023-10-16 22:18:48', 109, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `equipment_type`
--

CREATE TABLE `equipment_type` (
  `equip_id` int(11) NOT NULL,
  `equip_type` varchar(100) NOT NULL COMMENT 'Must be Unique',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL COMMENT 'Foreign key',
  `equip_status` int(2) NOT NULL DEFAULT 1 COMMENT '1=Available | 0=NOT'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment_type`
--

INSERT INTO `equipment_type` (`equip_id`, `equip_type`, `date_added`, `user_id`, `equip_status`) VALUES
(35, 'High Chair', '2023-10-15 17:10:47', 133, 1),
(36, 'Low Chair', '2023-10-15 17:10:54', 133, 1),
(37, 'Office Equipment', '2023-10-16 19:11:28', 134, 1);

-- --------------------------------------------------------

--
-- Table structure for table `equipment_unit`
--

CREATE TABLE `equipment_unit` (
  `id` int(100) NOT NULL,
  `unit_type` varchar(50) NOT NULL COMMENT 'Must be Unique',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL COMMENT 'Foreign key',
  `unit_status` int(2) NOT NULL DEFAULT 1 COMMENT '1 = Available | 0 = NOT'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment_unit`
--

INSERT INTO `equipment_unit` (`id`, `unit_type`, `date_added`, `user_id`, `unit_status`) VALUES
(21, 'Each(s)', '2023-03-22 21:49:07', 109, 1),
(23, 'Bundle(s)', '2023-03-27 20:43:12', 109, 1),
(33, 'Package(s)', '2023-04-19 19:16:21', 109, 1),
(37, 'N/A', '2023-10-16 19:04:59', 109, 1);

-- --------------------------------------------------------

--
-- Table structure for table `location_branch`
--

CREATE TABLE `location_branch` (
  `id` int(100) NOT NULL,
  `location` varchar(200) NOT NULL COMMENT 'Must be Unique',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL DEFAULT 1 COMMENT 'Foreign key',
  `location_status` int(2) NOT NULL DEFAULT 1 COMMENT '1 = Available | 0 = NOT'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `location_branch`
--

INSERT INTO `location_branch` (`id`, `location`, `date_added`, `user_id`, `location_status`) VALUES
(19, 'Golden Minds Colleges Sta. Maria, Bulacan', '2023-03-12 21:02:55', 109, 1),
(21, 'Golden Minds Colleges Balagtas, Bulacan', '2023-03-13 01:05:10', 109, 1),
(22, 'Golden Minds Academy Guiguinto, Bulacan', '2023-03-13 01:05:25', 109, 1);

-- --------------------------------------------------------

--
-- Table structure for table `room_code`
--

CREATE TABLE `room_code` (
  `room_code_id` int(11) NOT NULL COMMENT 'PK',
  `room_code_name` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(255) NOT NULL COMMENT 'FK',
  `room_code_status` tinyint(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `room_code`
--

INSERT INTO `room_code` (`room_code_id`, `room_code_name`, `date_added`, `user_id`, `room_code_status`) VALUES
(1, 'Science & Laboratory 2', '2023-10-15 14:12:27', 109, 1),
(4, 'Computer Laboratory 1', '2023-10-15 15:59:02', 133, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(255) NOT NULL,
  `acct_name` varchar(255) NOT NULL,
  `uname` varchar(100) NOT NULL COMMENT 'Must be Unique',
  `pword` varchar(255) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '1 = Active| 0 = Inactive',
  `school_branch` varchar(200) NOT NULL,
  `email` varchar(50) NOT NULL COMMENT 'Must be unique',
  `profile_img` varchar(255) NOT NULL,
  `img_extension` varchar(50) NOT NULL,
  `is_logged_in` tinyint(10) NOT NULL COMMENT '1=Account LoggedIn | 0=NOT',
  `login_time` datetime DEFAULT NULL,
  `logout_time` datetime DEFAULT NULL,
  `session_id` varchar(200) DEFAULT NULL COMMENT 'SID User Tracker'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `acct_name`, `uname`, `pword`, `status`, `school_branch`, `email`, `profile_img`, `img_extension`, `is_logged_in`, `login_time`, `logout_time`, `session_id`) VALUES
(109, 'Jerome Avecilla', 'jeromeadmin', '$2y$10$JkzfFwg/TVdIaInH0p/8R.qXgk2AxU32lOhzcpIN/jV6tjFCZatuu', 1, 'Golden Minds Colleges Sta. Maria, Bulacan', 'jeromesavc@gmail.com', 'javecilla', 'png', 1, '2023-10-20 23:07:35', '2023-10-20 23:07:07', 'f8c5np9iq6v9ucjalchefr8tui'),
(133, 'Derek Malibiran', 'derekadmin', '$2y$10$ErF9x5sr7YNlkyFWMYnNcuTcNqkJtRviHXx/pzOVaJYoegLDokokW', 1, 'Golden Minds Colleges Sta. Maria, Bulacan', 'malibiran030872@gmail.com', 'admin', 'png', 0, '2023-10-20 21:17:54', '2023-10-20 21:18:16', 'cq0o69hkrq5lamn3mdum517hrq'),
(134, 'Jelo Victoriano', 'jeloadmin', '$2y$10$e9PW2R4gbJe05rcxwjiEjuSGDEpPMvX0NSeD2GnZK0VPNA1Y/iKS.', 1, 'Golden Minds Colleges Sta. Maria, Bulacan', 'jelorider02@gmail.com', 'admin', 'png', 0, '2023-10-16 19:10:12', '2023-10-16 19:51:00', 'cf05avek4i838d3df5nheptkdh');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barrowed_equipment`
--
ALTER TABLE `barrowed_equipment`
  ADD PRIMARY KEY (`barrow_id`),
  ADD KEY `barrower_id` (`costumer_id`),
  ADD KEY `equipment_id` (`equipment_id`),
  ADD KEY `user_admin_id` (`user_id`),
  ADD KEY `admin_user_id` (`admin_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`),
  ADD KEY `users_id` (`user_id`);

--
-- Indexes for table `costumers`
--
ALTER TABLE `costumers`
  ADD PRIMARY KEY (`costumer_id`),
  ADD KEY `schoolBranch_id` (`school_id`),
  ADD KEY `id_admin` (`admin_id`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `equipment_name` (`equipment_name`),
  ADD KEY `catergory` (`category_id`),
  ADD KEY `equipment_type` (`type_id`),
  ADD KEY `equipment_unit` (`unit_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `location_rack` (`location_id`),
  ADD KEY `rcid` (`roomcode_id`),
  ADD KEY `idof_user` (`m_userid`);

--
-- Indexes for table `equipment_type`
--
ALTER TABLE `equipment_type`
  ADD PRIMARY KEY (`equip_id`),
  ADD UNIQUE KEY `equip_type` (`equip_type`),
  ADD KEY `et_user_id` (`user_id`);

--
-- Indexes for table `equipment_unit`
--
ALTER TABLE `equipment_unit`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unit_type` (`unit_type`),
  ADD KEY `ut_user_id` (`user_id`);

--
-- Indexes for table `location_branch`
--
ALTER TABLE `location_branch`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `location` (`location`),
  ADD KEY `lb_user_id` (`user_id`);

--
-- Indexes for table `room_code`
--
ALTER TABLE `room_code`
  ADD PRIMARY KEY (`room_code_id`),
  ADD KEY `uid` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uname` (`uname`),
  ADD KEY `email` (`email`),
  ADD KEY `school` (`school_branch`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barrowed_equipment`
--
ALTER TABLE `barrowed_equipment`
  MODIFY `barrow_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `costumers`
--
ALTER TABLE `costumers`
  MODIFY `costumer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=216;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT for table `equipment_type`
--
ALTER TABLE `equipment_type`
  MODIFY `equip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `equipment_unit`
--
ALTER TABLE `equipment_unit`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `location_branch`
--
ALTER TABLE `location_branch`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `room_code`
--
ALTER TABLE `room_code`
  MODIFY `room_code_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'PK', AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barrowed_equipment`
--
ALTER TABLE `barrowed_equipment`
  ADD CONSTRAINT `admin_user_id` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `barrower_id` FOREIGN KEY (`costumer_id`) REFERENCES `costumers` (`costumer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `equipment_id` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_admin_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `users_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `costumers`
--
ALTER TABLE `costumers`
  ADD CONSTRAINT `id_admin` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `schoolBranch_id` FOREIGN KEY (`school_id`) REFERENCES `location_branch` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `cid` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `etid` FOREIGN KEY (`type_id`) REFERENCES `equipment_type` (`equip_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `idof_user` FOREIGN KEY (`m_userid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lrid` FOREIGN KEY (`location_id`) REFERENCES `location_branch` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rcid` FOREIGN KEY (`roomcode_id`) REFERENCES `room_code` (`room_code_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `utid` FOREIGN KEY (`unit_id`) REFERENCES `equipment_unit` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `equipment_type`
--
ALTER TABLE `equipment_type`
  ADD CONSTRAINT `et_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `equipment_unit`
--
ALTER TABLE `equipment_unit`
  ADD CONSTRAINT `ut_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `location_branch`
--
ALTER TABLE `location_branch`
  ADD CONSTRAINT `lb_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `room_code`
--
ALTER TABLE `room_code`
  ADD CONSTRAINT `uid` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `school` FOREIGN KEY (`school_branch`) REFERENCES `location_branch` (`location`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
