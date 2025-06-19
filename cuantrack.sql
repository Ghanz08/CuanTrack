-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 18, 2025 at 03:43 PM
-- Server version: 10.11.10-MariaDB-log
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cuantrack`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_recalculate_wallet_balances` ()   BEGIN
    UPDATE `wallets` w
    SET 
        `total_income` = COALESCE((
            SELECT SUM(t.amount) 
            FROM `transactions` t 
            WHERE t.id_wallet = w.id_wallet AND t.type = 'income'
        ), 0),
        `total_expenses` = COALESCE((
            SELECT SUM(t.amount) 
            FROM `transactions` t 
            WHERE t.id_wallet = w.id_wallet AND t.type = 'expense'
        ), 0),
        `updated_at` = CURRENT_TIMESTAMP;
    
    UPDATE `wallets` 
    SET `balance` = `total_income` - `total_expenses`;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `id_budget` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_wallet` int(11) DEFAULT NULL,
  `id_category` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `budgets`
--

INSERT INTO `budgets` (`id_budget`, `id_user`, `id_wallet`, `id_category`, `amount`, `start_date`, `end_date`) VALUES
(2, 1, 1, 3, '300000.00', '2025-05-01', '2025-05-31'),
(3, 2, 3, 5, '500000.00', '2025-05-01', '2025-05-31'),
(4, 1, 1, 2, '600000.00', '2025-06-01', '2025-06-30'),
(7, 2, 3, 5, '350000.00', '2025-06-01', '2025-06-30'),
(11, 1, 2, 10, '100000.00', '2025-06-01', '2025-06-30');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id_category` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `type` enum('income','expense') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id_category`, `id_user`, `name`, `type`) VALUES
(1, 1, 'Gaji', 'income'),
(2, 1, 'Makan', 'expense'),
(3, 1, 'Transportasi', 'expense'),
(4, 2, 'Freelance', 'income'),
(5, 2, 'Belanja', 'expense'),
(6, 6, 'Fashion', 'expense'),
(7, 6, 'Food', 'expense'),
(8, 6, 'Transportation', 'expense'),
(9, 6, 'Salary', 'income'),
(10, 1, 'Minum', 'expense'),
(11, 7, 'Open BO', 'expense'),
(12, 7, 'Hasil BO', 'income');

-- --------------------------------------------------------

--
-- Table structure for table `goals`
--

CREATE TABLE `goals` (
  `id_goal` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `target_amount` decimal(10,2) DEFAULT NULL,
  `current_amount` decimal(10,2) DEFAULT NULL,
  `target_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `goals`
--

INSERT INTO `goals` (`id_goal`, `id_user`, `title`, `target_amount`, `current_amount`, `target_date`) VALUES
(1, 1, 'Beli Laptop', '10000000.00', '4000000.00', '2025-12-31'),
(2, 2, 'Liburan ke Bali', '5000000.00', '1000000.00', '2025-08-15'),
(3, 1, 'Beli Motor', '30000000.00', '5000000.00', '2030-12-03');

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id_subscription` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_wallet` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `billing_cycle` enum('daily','weekly','monthly','yearly') NOT NULL,
  `next_due_date` date DEFAULT NULL,
  `status` enum('active','paused','cancelled') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`id_subscription`, `id_user`, `id_wallet`, `name`, `amount`, `billing_cycle`, `next_due_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Netflix', '65000.00', 'monthly', '2025-06-01', 'active', '2025-05-24 22:21:35', '2025-05-24 22:21:35'),
(2, 1, 2, 'Spotify', '49000.00', 'monthly', '2025-06-10', 'active', '2025-05-24 22:21:35', '2025-05-24 22:21:35'),
(3, 2, 3, 'YouTube Premium', '59000.00', 'monthly', '2025-06-05', 'active', '2025-05-24 22:21:35', '2025-05-24 22:21:35'),
(4, 1, 1, 'Disney Plus', '90000.00', 'monthly', '2025-06-07', 'active', '2025-06-07 17:21:36', '2025-06-07 17:21:36');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id_transaction` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_wallet` int(11) DEFAULT NULL,
  `id_category` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `type` enum('income','expense') NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `transaction_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id_transaction`, `id_user`, `id_wallet`, `id_category`, `amount`, `type`, `description`, `transaction_date`) VALUES
(1, 1, 1, 1, '5000000.00', 'income', 'Gaji Bulanan', '2025-04-30 17:00:00'),
(2, 1, 1, 2, '50000.00', 'expense', 'Sarapan', '2025-05-01 17:00:00'),
(3, 1, 1, 3, '20000.00', 'expense', 'Naik bus', '2025-05-01 17:00:00'),
(4, 2, 3, 4, '1500000.00', 'income', 'Proyek freelance', '2025-04-30 17:00:00'),
(5, 2, 3, 5, '300000.00', 'expense', 'Beli baju', '2025-05-02 17:00:00'),
(6, 1, 1, 1, '1000000.00', 'income', 'Gaji Bulanan', '2025-05-28 17:00:00'),
(7, 1, 1, 2, '15000.00', 'expense', 'Makan nasduk warung pak imam', '2025-06-01 17:00:00'),
(8, 1, 2, 1, '1000000.00', 'income', 'Gaji freelance', '2025-06-01 17:00:00'),
(9, 1, 1, 2, '100000.00', 'expense', 'menang game', '2025-06-01 17:00:00'),
(10, 1, 2, 1, '3000000.00', 'income', 'menang game', '2025-06-02 17:00:00'),
(11, 1, 1, 2, '30000.00', 'expense', 'makan ayam', '2025-06-02 17:00:00'),
(12, 1, 1, 2, '10000.00', 'expense', 'beli minum', '2025-06-02 17:00:00'),
(13, 1, 1, 2, '40000.00', 'expense', 'jajan', '2025-06-02 17:00:00'),
(14, 1, 1, 1, '50000.00', 'income', 'ngegojek', '2025-06-02 17:00:00'),
(15, 6, 4, 6, '1500000.00', 'expense', 'Beli Sepatu', '2025-06-02 17:00:00'),
(16, 6, 4, 9, '50000.00', 'income', 'tes', '2025-06-02 17:00:00'),
(17, 1, 1, 10, '10000.00', 'expense', 'amwe', '2025-06-06 17:00:00'),
(18, 1, 1, 10, '10000.00', 'expense', 'Amwe', '2025-06-06 17:00:00'),
(19, 1, 5, 10, '10000.00', 'expense', 'amwe', '2025-06-06 17:00:00'),
(20, 7, 6, 11, '500000.00', 'expense', 'Diisep kontol', '2025-06-06 17:00:00'),
(21, 7, 6, 12, '600000.00', 'income', 'ngisep kontol', '2025-06-06 17:00:00');

--
-- Triggers `transactions`
--
DELIMITER $$
CREATE TRIGGER `tr_transactions_after_delete` AFTER DELETE ON `transactions` FOR EACH ROW BEGIN
    -- Revert transaction impact
    IF OLD.type = 'income' THEN
        UPDATE `wallets` 
        SET 
            `total_income` = `total_income` - OLD.amount,
            `balance` = `balance` - OLD.amount,
            `updated_at` = CURRENT_TIMESTAMP
        WHERE `id_wallet` = OLD.id_wallet;
    ELSE
        UPDATE `wallets` 
        SET 
            `total_expenses` = `total_expenses` - OLD.amount,
            `balance` = `balance` + OLD.amount,
            `updated_at` = CURRENT_TIMESTAMP
        WHERE `id_wallet` = OLD.id_wallet;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_transactions_after_insert` AFTER INSERT ON `transactions` FOR EACH ROW BEGIN
    IF NEW.type = 'income' THEN
        UPDATE `wallets` 
        SET 
            `total_income` = `total_income` + NEW.amount,
            `balance` = `balance` + NEW.amount,
            `updated_at` = CURRENT_TIMESTAMP
        WHERE `id_wallet` = NEW.id_wallet;
    ELSE
        UPDATE `wallets` 
        SET 
            `total_expenses` = `total_expenses` + NEW.amount,
            `balance` = `balance` - NEW.amount,
            `updated_at` = CURRENT_TIMESTAMP
        WHERE `id_wallet` = NEW.id_wallet;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_transactions_after_update` AFTER UPDATE ON `transactions` FOR EACH ROW BEGIN
    -- Revert old transaction impact
    IF OLD.type = 'income' THEN
        UPDATE `wallets` 
        SET 
            `total_income` = `total_income` - OLD.amount,
            `balance` = `balance` - OLD.amount
        WHERE `id_wallet` = OLD.id_wallet;
    ELSE
        UPDATE `wallets` 
        SET 
            `total_expenses` = `total_expenses` - OLD.amount,
            `balance` = `balance` + OLD.amount
        WHERE `id_wallet` = OLD.id_wallet;
    END IF;
    
    -- Apply new transaction impact
    IF NEW.type = 'income' THEN
        UPDATE `wallets` 
        SET 
            `total_income` = `total_income` + NEW.amount,
            `balance` = `balance` + NEW.amount,
            `updated_at` = CURRENT_TIMESTAMP
        WHERE `id_wallet` = NEW.id_wallet;
    ELSE
        UPDATE `wallets` 
        SET 
            `total_expenses` = `total_expenses` + NEW.amount,
            `balance` = `balance` - NEW.amount,
            `updated_at` = CURRENT_TIMESTAMP
        WHERE `id_wallet` = NEW.id_wallet;
    END IF;
    
    -- Handle wallet change (if id_wallet changed)
    IF OLD.id_wallet != NEW.id_wallet THEN
        -- Update old wallet's updated_at
        UPDATE `wallets` 
        SET `updated_at` = CURRENT_TIMESTAMP
        WHERE `id_wallet` = OLD.id_wallet;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `email`, `image`, `created_at`) VALUES
(1, 'Ghani Zulhusni Bahri', '$2y$10$w9WN3l9eM/YviV2srTVDWePtGuG/QuamJPJAAl8AbwsxU4RwaTeEC', 'ghani@example.com', 'public/images/profiles/user_1_1749290557.gif', '2025-05-15 08:48:38'),
(2, 'Rina Andini', '', 'rina@example.com', '', '2025-05-15 08:48:38'),
(3, 'Ghani Zulhusni', '$2y$10$l.PUOIZL.Ejqf./IN0ye/.npGi5wQ3990eGvd6E73eStVNs0tg5PG', 'ghanizulhusnib@gmail.com', NULL, '2025-05-29 17:28:36'),
(4, 'Ghani Zulhusni Bahri', '$2y$10$AwlE1qgNBsXp.BNiy.CYdODAnhgtsruZ21TnWW/Yu9tqsckt3U8s.', 'ghanizulhusni@gmail.com', NULL, '2025-06-02 16:16:19'),
(6, 'Ghanz', '$2y$10$gnlBO9m4st8XwKhSuwHxEu3Y.nTAy/XZBiWQGYnImDtG3Lh/8Et5i', 'zulhusnibahri@gmail.com', NULL, '2025-06-03 20:40:13'),
(7, 'safwan', '$2y$10$h9PrNxWAavGXebUVP1XC.ePDnhS0ytvjs585T68rGBC0lK8tfGU4a', 'safwan.arfian@gmail.com', 'public/images/profiles/user_7_1749294107.gif', '2025-06-07 17:50:06');

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id_wallet` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `balance` decimal(12,2) DEFAULT 0.00,
  `total_income` decimal(12,2) DEFAULT 0.00,
  `total_expenses` decimal(12,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id_wallet`, `id_user`, `name`, `currency`, `balance`, `total_income`, `total_expenses`, `created_at`, `updated_at`) VALUES
(1, 1, 'Bank BCA', 'IDR', '5765000.00', '6050000.00', '285000.00', '2025-05-24 22:20:31', '2025-06-07 17:44:49'),
(2, 1, 'OVO', 'IDR', '4000000.00', '4000000.00', '0.00', '2025-05-24 22:20:31', '2025-06-03 17:23:27'),
(3, 2, 'Bank Mandiri', 'IDR', '1200000.00', '1500000.00', '300000.00', '2025-05-24 22:20:31', '2025-06-01 13:17:05'),
(4, 6, 'BNI', 'IDR', '8550000.00', '50000.00', '1500000.00', '2025-06-03 20:44:00', '2025-06-03 21:16:29'),
(5, 1, 'BRI', 'IDR', '990000.00', '0.00', '10000.00', '2025-06-07 17:44:26', '2025-06-07 17:46:58'),
(6, 7, 'Cash', 'IDR', '2100000.00', '600000.00', '500000.00', '2025-06-07 17:51:29', '2025-06-07 17:54:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`id_budget`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_category` (`id_category`),
  ADD KEY `idx_wallet_budget` (`id_wallet`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id_category`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`id_goal`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id_subscription`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_wallet` (`id_wallet`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id_transaction`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_category` (`id_category`),
  ADD KEY `fk_transactions_wallet` (`id_wallet`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id_wallet`),
  ADD KEY `id_user` (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id_budget` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id_category` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `goals`
--
ALTER TABLE `goals`
  MODIFY `id_goal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id_subscription` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id_transaction` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id_wallet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `budgets`
--
ALTER TABLE `budgets`
  ADD CONSTRAINT `budgets_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `budgets_ibfk_2` FOREIGN KEY (`id_category`) REFERENCES `categories` (`id_category`),
  ADD CONSTRAINT `fk_budgets_wallet` FOREIGN KEY (`id_wallet`) REFERENCES `wallets` (`id_wallet`) ON DELETE SET NULL;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `goals`
--
ALTER TABLE `goals`
  ADD CONSTRAINT `goals_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `subscriptions_ibfk_2` FOREIGN KEY (`id_wallet`) REFERENCES `wallets` (`id_wallet`) ON DELETE SET NULL;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_transactions_wallet` FOREIGN KEY (`id_wallet`) REFERENCES `wallets` (`id_wallet`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`id_category`) REFERENCES `categories` (`id_category`);

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
