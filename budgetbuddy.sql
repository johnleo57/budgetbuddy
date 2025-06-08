-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2025 at 06:54 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `budgetbuddy`
--

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `BudgetID` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `Date` date NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `balance` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budgets`
--

INSERT INTO `budgets` (`BudgetID`, `user_id`, `Date`, `Amount`, `balance`) VALUES
(1, 922445061, '0000-00-00', 80000.00, 0),
(2, 374163220, '0000-00-00', 70000.00, 0),
(4, 516089344, '0000-00-00', 90000.00, 0),
(5, 182433002, '0000-00-00', 50009.00, 0),
(13, 1300739497, '0000-00-00', 90000.00, 0),
(14, 228698946, '0000-00-00', 80000.00, 0),
(15, 1300739497, '2025-05-01', 90000.00, 0),
(16, 374163220, '2025-05-01', 90000.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `CategoryID` int(11) NOT NULL,
  `CategoryName` varchar(255) NOT NULL,
  `IconPath` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`CategoryID`, `CategoryName`, `IconPath`) VALUES
(1, 'Food', 'icons/food.png'),
(2, 'Transportation', 'icons/transportation.png'),
(3, 'Utilities', 'icons/utilities.png'),
(4, 'Entertainment', 'icons/entertainment.png'),
(5, 'Health', 'icons/health.png'),
(6, 'Education', 'icons/education.png'),
(7, 'Travel', 'icons/travel.png'),
(8, 'Savings', 'icons/savings.png'),
(9, 'Others', 'icons/others.png');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `exp_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `CategoryID` int(11) DEFAULT NULL,
  `CategoryName` varchar(255) NOT NULL,
  `Date` date NOT NULL,
  `exp_name` varchar(255) NOT NULL,
  `exp_price` decimal(10,2) NOT NULL,
  `Description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`exp_id`, `user_id`, `CategoryID`, `CategoryName`, `Date`, `exp_name`, `exp_price`, `Description`) VALUES
(45, 1300739497, 1, 'Food', '2025-05-22', 'rice', 1000.00, ''),
(46, 1300739497, 2, 'Transportation', '2025-05-22', 'airplane', 6000.00, ''),
(51, 1300739497, 2, 'Transportation', '2025-05-22', 'jeep', 24.00, ''),
(52, 1300739497, 1, 'Food', '2025-05-22', 'candy', 100.00, ''),
(53, 1300739497, 4, 'Entertainment', '2025-05-22', 'cinema', 500.00, ''),
(54, 1300739497, 6, 'Education', '2025-05-22', 'tuition', 30000.00, ''),
(55, 1300739497, 2, 'Transportation', '2025-05-22', 'tricycle', 20.00, ''),
(56, 1300739497, 4, 'Entertainment', '2025-05-22', 'Spotify', 300.00, ''),
(57, 1300739497, 9, 'Others', '2025-05-22', 'Mouse', 1500.00, ''),
(58, 1300739497, 3, 'Utilities', '2025-05-22', 'water bill', 1300.00, ''),
(77, 922445061, 2, 'Transportation', '2025-05-22', 'Jeepney', 200.00, ''),
(78, 922445061, 2, 'Transportation', '2025-05-22', 'Jeepney', 1000.00, ''),
(79, 228698946, 7, 'Travel', '2025-05-22', 'boracay', 70000.00, ''),
(80, 228698946, 3, 'Utilities', '2025-05-22', 'Electric bill', 5000.00, ''),
(81, 374163220, 4, 'Entertainment', '2025-05-23', 'Harry Potter', 1000.00, ''),
(82, 374163220, 3, 'Utilities', '2025-05-23', 'Electric bill', 2300.00, ''),
(83, 516089344, 2, 'Transportation', '2025-05-23', 'airplane', 10000.00, ''),
(84, 516089344, 3, 'Utilities', '2025-05-23', 'Electric bill', 1300.00, ''),
(85, 516089344, 1, 'Food', '2025-05-23', 'rice', 23.00, ''),
(86, 374163220, 7, 'Travel', '2025-05-23', 'Palawan', 60000.00, ''),
(87, 1300739497, 2, 'Transportation', '2025-05-23', 'Jeepney', 232.00, ''),
(88, 1300739497, 5, 'Health', '2025-05-23', 'check up', 1000.00, ''),
(89, 1300739497, 7, 'Travel', '2025-05-23', 'Palawan', 30000.00, ''),
(90, 1300739497, 8, 'Savings', '2025-05-23', 'Emergency Fund', 2000.00, ''),
(91, 1300739497, 2, 'Transportation', '2025-05-23', 'Jeepney', 22.00, ''),
(92, 374163220, 2, 'Transportation', '2025-05-23', 'Jeepney', 100.00, ''),
(93, 374163220, 3, 'Utilities', '2025-05-23', 'Water Bill', 500.00, ''),
(94, 374163220, 4, 'Entertainment', '2025-05-23', 'Spotify', 200.00, ''),
(95, 374163220, 9, 'Others', '2025-05-23', 'GPU', 2000.00, ''),
(96, 374163220, 8, 'Savings', '2025-05-23', 'Emergency Fund', 1000.00, ''),
(97, 1300739497, 6, 'Education', '2025-05-24', 'books', 1000.00, ''),
(98, 1300739497, 9, 'Others', '2025-05-24', 'monitor', 1500.00, ''),
(99, 1300739497, 6, 'Education', '2025-05-24', 'notebooks', 100.00, ''),
(100, 374163220, 5, 'Health', '2025-05-24', 'Lab test', 20000.00, '');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `reportID` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `Date` date NOT NULL,
  `reportData` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`reportID`, `user_id`, `Date`, `reportData`) VALUES
(19, 1300739497, '2025-05-01', '{\"budget\":\"90000.00\",\"total_expenses\":\"76598.00\",\"balance\":\"13402.00\",\"savings_rate\":\"14.89\",\"savingGoals\":[{\"goalName\":\"New Phone (Poco X7 Pro)\",\"targetAmount\":\"17000.00\",\"currentAmount\":\"17000.00\",\"variance\":\"0.00\",\"progressPercent\":\"100.00\"},{\"goalName\":\"Nike Vomero 5\",\"targetAmount\":\"18000.00\",\"currentAmount\":\"10000.00\",\"variance\":\"8000.00\",\"progressPercent\":\"55.56\"},{\"goalName\":\"new phone\",\"targetAmount\":\"3333.00\",\"currentAmount\":\"212.00\",\"variance\":\"3121.00\",\"progressPercent\":\"6.36\"},{\"goalName\":\"Monitor\",\"targetAmount\":\"12000.00\",\"currentAmount\":\"1200.00\",\"variance\":\"10800.00\",\"progressPercent\":\"10.00\"},{\"goalName\":\"new phone\",\"targetAmount\":\"123.00\",\"currentAmount\":\"12.00\",\"variance\":\"111.00\",\"progressPercent\":\"9.76\"},{\"goalName\":\"sdad\",\"targetAmount\":\"231.00\",\"currentAmount\":\"32.00\",\"variance\":\"199.00\",\"progressPercent\":\"13.85\"},{\"goalName\":\"sads\",\"targetAmount\":\"232.00\",\"currentAmount\":\"23.00\",\"variance\":\"209.00\",\"progressPercent\":\"9.91\"},{\"goalName\":\"asd\",\"targetAmount\":\"123.00\",\"currentAmount\":\"123.00\",\"variance\":\"0.00\",\"progressPercent\":\"100.00\"},{\"goalName\":\"New Pc\",\"targetAmount\":\"70000.00\",\"currentAmount\":\"50000.00\",\"variance\":\"20000.00\",\"progressPercent\":\"71.43\"},{\"goalName\":\"new phone\",\"targetAmount\":\"12321.00\",\"currentAmount\":\"1232.00\",\"variance\":\"11089.00\",\"progressPercent\":\"10.00\"},{\"goalName\":\"adidas\",\"targetAmount\":\"1000.00\",\"currentAmount\":\"600.00\",\"variance\":\"400.00\",\"progressPercent\":\"60.00\"},{\"goalName\":\"panda dunks\",\"targetAmount\":\"6000.00\",\"currentAmount\":\"2500.00\",\"variance\":\"3500.00\",\"progressPercent\":\"41.67\"},{\"goalName\":\"dsa\",\"targetAmount\":\"232.00\",\"currentAmount\":\"232.00\",\"variance\":\"0.00\",\"progressPercent\":\"100.00\"},{\"goalName\":\"Slippers\",\"targetAmount\":\"500.00\",\"currentAmount\":\"300.00\",\"variance\":\"200.00\",\"progressPercent\":\"60.00\"}],\"expensesInsights\":[\"You are within your budget. Great job managing your expenses!\"],\"savingsRateInsights\":[\"Your overall savings rate is below 40%. Consider cutting discretionary spending to increase your savings rate.\"]}'),
(20, 1300739497, '2025-01-01', '{\"budget\":\"0.00\",\"total_expenses\":\"0.00\",\"balance\":\"0.00\",\"savings_rate\":\"0.00\",\"savingGoals\":[{\"goalName\":\"New Phone (Poco X7 Pro)\",\"targetAmount\":\"17000.00\",\"currentAmount\":\"17000.00\",\"variance\":\"0.00\",\"progressPercent\":\"100.00\"},{\"goalName\":\"Nike Vomero 5\",\"targetAmount\":\"18000.00\",\"currentAmount\":\"10000.00\",\"variance\":\"8000.00\",\"progressPercent\":\"55.56\"},{\"goalName\":\"new phone\",\"targetAmount\":\"3333.00\",\"currentAmount\":\"212.00\",\"variance\":\"3121.00\",\"progressPercent\":\"6.36\"},{\"goalName\":\"Monitor\",\"targetAmount\":\"12000.00\",\"currentAmount\":\"1200.00\",\"variance\":\"10800.00\",\"progressPercent\":\"10.00\"},{\"goalName\":\"new phone\",\"targetAmount\":\"123.00\",\"currentAmount\":\"12.00\",\"variance\":\"111.00\",\"progressPercent\":\"9.76\"},{\"goalName\":\"sdad\",\"targetAmount\":\"231.00\",\"currentAmount\":\"32.00\",\"variance\":\"199.00\",\"progressPercent\":\"13.85\"},{\"goalName\":\"sads\",\"targetAmount\":\"232.00\",\"currentAmount\":\"23.00\",\"variance\":\"209.00\",\"progressPercent\":\"9.91\"},{\"goalName\":\"New Phone (Poco X7 Pro)\",\"targetAmount\":\"123.00\",\"currentAmount\":\"121.00\",\"variance\":\"2.00\",\"progressPercent\":\"98.37\"},{\"goalName\":\"new shoes\",\"targetAmount\":\"1233.00\",\"currentAmount\":\"121.00\",\"variance\":\"1112.00\",\"progressPercent\":\"9.81\"},{\"goalName\":\"Guitar\",\"targetAmount\":\"15000.00\",\"currentAmount\":\"10000.00\",\"variance\":\"5000.00\",\"progressPercent\":\"66.67\"},{\"goalName\":\"asd\",\"targetAmount\":\"123.00\",\"currentAmount\":\"123.00\",\"variance\":\"0.00\",\"progressPercent\":\"100.00\"},{\"goalName\":\"New Pc\",\"targetAmount\":\"70000.00\",\"currentAmount\":\"50000.00\",\"variance\":\"20000.00\",\"progressPercent\":\"71.43\"},{\"goalName\":\"new phone\",\"targetAmount\":\"12321.00\",\"currentAmount\":\"1232.00\",\"variance\":\"11089.00\",\"progressPercent\":\"10.00\"},{\"goalName\":\"adidas\",\"targetAmount\":\"1000.00\",\"currentAmount\":\"600.00\",\"variance\":\"400.00\",\"progressPercent\":\"60.00\"},{\"goalName\":\"panda dunks\",\"targetAmount\":\"6000.00\",\"currentAmount\":\"2500.00\",\"variance\":\"3500.00\",\"progressPercent\":\"41.67\"},{\"goalName\":\"dsa\",\"targetAmount\":\"232.00\",\"currentAmount\":\"232.00\",\"variance\":\"0.00\",\"progressPercent\":\"100.00\"}],\"expensesInsights\":[\"You are within your budget. Great job managing your expenses!\"],\"savingsRateInsights\":[\"Your overall savings rate is below 40%. Consider cutting discretionary spending to increase your savings rate.\"]}'),
(21, 1300739497, '2025-02-01', '{\"budget\":\"0.00\",\"total_expenses\":\"0.00\",\"balance\":\"0.00\",\"savings_rate\":\"0.00\",\"savingGoals\":[{\"goalName\":\"New Phone (Poco X7 Pro)\",\"targetAmount\":\"17000.00\",\"currentAmount\":\"17000.00\",\"variance\":\"0.00\",\"progressPercent\":\"100.00\"},{\"goalName\":\"Nike Vomero 5\",\"targetAmount\":\"18000.00\",\"currentAmount\":\"10000.00\",\"variance\":\"8000.00\",\"progressPercent\":\"55.56\"},{\"goalName\":\"new phone\",\"targetAmount\":\"3333.00\",\"currentAmount\":\"212.00\",\"variance\":\"3121.00\",\"progressPercent\":\"6.36\"},{\"goalName\":\"Monitor\",\"targetAmount\":\"12000.00\",\"currentAmount\":\"1200.00\",\"variance\":\"10800.00\",\"progressPercent\":\"10.00\"},{\"goalName\":\"new phone\",\"targetAmount\":\"123.00\",\"currentAmount\":\"12.00\",\"variance\":\"111.00\",\"progressPercent\":\"9.76\"},{\"goalName\":\"sdad\",\"targetAmount\":\"231.00\",\"currentAmount\":\"32.00\",\"variance\":\"199.00\",\"progressPercent\":\"13.85\"},{\"goalName\":\"sads\",\"targetAmount\":\"232.00\",\"currentAmount\":\"23.00\",\"variance\":\"209.00\",\"progressPercent\":\"9.91\"},{\"goalName\":\"Guitar\",\"targetAmount\":\"15000.00\",\"currentAmount\":\"10000.00\",\"variance\":\"5000.00\",\"progressPercent\":\"66.67\"},{\"goalName\":\"asd\",\"targetAmount\":\"123.00\",\"currentAmount\":\"123.00\",\"variance\":\"0.00\",\"progressPercent\":\"100.00\"},{\"goalName\":\"New Pc\",\"targetAmount\":\"70000.00\",\"currentAmount\":\"50000.00\",\"variance\":\"20000.00\",\"progressPercent\":\"71.43\"},{\"goalName\":\"new phone\",\"targetAmount\":\"12321.00\",\"currentAmount\":\"1232.00\",\"variance\":\"11089.00\",\"progressPercent\":\"10.00\"},{\"goalName\":\"adidas\",\"targetAmount\":\"1000.00\",\"currentAmount\":\"600.00\",\"variance\":\"400.00\",\"progressPercent\":\"60.00\"},{\"goalName\":\"panda dunks\",\"targetAmount\":\"6000.00\",\"currentAmount\":\"2500.00\",\"variance\":\"3500.00\",\"progressPercent\":\"41.67\"},{\"goalName\":\"dsa\",\"targetAmount\":\"232.00\",\"currentAmount\":\"232.00\",\"variance\":\"0.00\",\"progressPercent\":\"100.00\"}],\"expensesInsights\":[\"You are within your budget. Great job managing your expenses!\"],\"savingsRateInsights\":[\"Your overall savings rate is below 40%. Consider cutting discretionary spending to increase your savings rate.\"]}'),
(22, 1300739497, '2025-06-01', '{\"budget\":\"0.00\",\"total_expenses\":\"0.00\",\"balance\":\"0.00\",\"savings_rate\":\"0.00\",\"savingGoals\":[{\"goalName\":\"New Phone (Poco X7 Pro)\",\"targetAmount\":\"17000.00\",\"currentAmount\":\"17000.00\",\"variance\":\"0.00\",\"progressPercent\":\"100.00\"},{\"goalName\":\"Nike Vomero 5\",\"targetAmount\":\"18000.00\",\"currentAmount\":\"10000.00\",\"variance\":\"8000.00\",\"progressPercent\":\"55.56\"},{\"goalName\":\"new phone\",\"targetAmount\":\"3333.00\",\"currentAmount\":\"212.00\",\"variance\":\"3121.00\",\"progressPercent\":\"6.36\"},{\"goalName\":\"Monitor\",\"targetAmount\":\"12000.00\",\"currentAmount\":\"1200.00\",\"variance\":\"10800.00\",\"progressPercent\":\"10.00\"},{\"goalName\":\"new phone\",\"targetAmount\":\"123.00\",\"currentAmount\":\"12.00\",\"variance\":\"111.00\",\"progressPercent\":\"9.76\"},{\"goalName\":\"sdad\",\"targetAmount\":\"231.00\",\"currentAmount\":\"32.00\",\"variance\":\"199.00\",\"progressPercent\":\"13.85\"},{\"goalName\":\"sads\",\"targetAmount\":\"232.00\",\"currentAmount\":\"23.00\",\"variance\":\"209.00\",\"progressPercent\":\"9.91\"},{\"goalName\":\"New Phone (Poco X7 Pro)\",\"targetAmount\":\"123.00\",\"currentAmount\":\"121.00\",\"variance\":\"2.00\",\"progressPercent\":\"98.37\"},{\"goalName\":\"new shoes\",\"targetAmount\":\"1233.00\",\"currentAmount\":\"121.00\",\"variance\":\"1112.00\",\"progressPercent\":\"9.81\"},{\"goalName\":\"Guitar\",\"targetAmount\":\"15000.00\",\"currentAmount\":\"10000.00\",\"variance\":\"5000.00\",\"progressPercent\":\"66.67\"},{\"goalName\":\"asd\",\"targetAmount\":\"123.00\",\"currentAmount\":\"123.00\",\"variance\":\"0.00\",\"progressPercent\":\"100.00\"},{\"goalName\":\"New Pc\",\"targetAmount\":\"70000.00\",\"currentAmount\":\"50000.00\",\"variance\":\"20000.00\",\"progressPercent\":\"71.43\"},{\"goalName\":\"new phone\",\"targetAmount\":\"12321.00\",\"currentAmount\":\"1232.00\",\"variance\":\"11089.00\",\"progressPercent\":\"10.00\"},{\"goalName\":\"adidas\",\"targetAmount\":\"1000.00\",\"currentAmount\":\"600.00\",\"variance\":\"400.00\",\"progressPercent\":\"60.00\"},{\"goalName\":\"panda dunks\",\"targetAmount\":\"6000.00\",\"currentAmount\":\"2500.00\",\"variance\":\"3500.00\",\"progressPercent\":\"41.67\"},{\"goalName\":\"dsa\",\"targetAmount\":\"232.00\",\"currentAmount\":\"232.00\",\"variance\":\"0.00\",\"progressPercent\":\"100.00\"}],\"expensesInsights\":[\"You are within your budget. Great job managing your expenses!\"],\"savingsRateInsights\":[\"Your overall savings rate is below 40%. Consider cutting discretionary spending to increase your savings rate.\"]}'),
(23, 1300739497, '2025-09-01', '{\"budget\":\"0.00\",\"total_expenses\":\"0.00\",\"balance\":\"0.00\",\"savings_rate\":\"0.00\",\"savingGoals\":[{\"goalName\":\"New Phone (Poco X7 Pro)\",\"targetAmount\":\"17000.00\",\"currentAmount\":\"17000.00\",\"variance\":\"0.00\",\"progressPercent\":\"100.00\"},{\"goalName\":\"Nike Vomero 5\",\"targetAmount\":\"18000.00\",\"currentAmount\":\"10000.00\",\"variance\":\"8000.00\",\"progressPercent\":\"55.56\"},{\"goalName\":\"new phone\",\"targetAmount\":\"3333.00\",\"currentAmount\":\"212.00\",\"variance\":\"3121.00\",\"progressPercent\":\"6.36\"},{\"goalName\":\"Monitor\",\"targetAmount\":\"12000.00\",\"currentAmount\":\"1200.00\",\"variance\":\"10800.00\",\"progressPercent\":\"10.00\"},{\"goalName\":\"new phone\",\"targetAmount\":\"123.00\",\"currentAmount\":\"12.00\",\"variance\":\"111.00\",\"progressPercent\":\"9.76\"},{\"goalName\":\"sdad\",\"targetAmount\":\"231.00\",\"currentAmount\":\"32.00\",\"variance\":\"199.00\",\"progressPercent\":\"13.85\"},{\"goalName\":\"sads\",\"targetAmount\":\"232.00\",\"currentAmount\":\"23.00\",\"variance\":\"209.00\",\"progressPercent\":\"9.91\"},{\"goalName\":\"New Phone (Poco X7 Pro)\",\"targetAmount\":\"123.00\",\"currentAmount\":\"121.00\",\"variance\":\"2.00\",\"progressPercent\":\"98.37\"},{\"goalName\":\"new shoes\",\"targetAmount\":\"1233.00\",\"currentAmount\":\"121.00\",\"variance\":\"1112.00\",\"progressPercent\":\"9.81\"},{\"goalName\":\"Guitar\",\"targetAmount\":\"15000.00\",\"currentAmount\":\"10000.00\",\"variance\":\"5000.00\",\"progressPercent\":\"66.67\"},{\"goalName\":\"asd\",\"targetAmount\":\"123.00\",\"currentAmount\":\"123.00\",\"variance\":\"0.00\",\"progressPercent\":\"100.00\"},{\"goalName\":\"New Pc\",\"targetAmount\":\"70000.00\",\"currentAmount\":\"50000.00\",\"variance\":\"20000.00\",\"progressPercent\":\"71.43\"},{\"goalName\":\"new phone\",\"targetAmount\":\"12321.00\",\"currentAmount\":\"1232.00\",\"variance\":\"11089.00\",\"progressPercent\":\"10.00\"},{\"goalName\":\"adidas\",\"targetAmount\":\"1000.00\",\"currentAmount\":\"600.00\",\"variance\":\"400.00\",\"progressPercent\":\"60.00\"}],\"expensesInsights\":[\"You are within your budget. Great job managing your expenses!\"],\"savingsRateInsights\":[\"Your overall savings rate is below 40%. Consider cutting discretionary spending to increase your savings rate.\"]}'),
(24, 1300739497, '2025-03-01', '{\"budget\":\"0.00\",\"total_expenses\":\"0.00\",\"balance\":\"0.00\",\"savings_rate\":\"0.00\",\"savingGoals\":[{\"goalName\":\"New Phone (Poco X7 Pro)\",\"targetAmount\":\"17000.00\",\"currentAmount\":\"17000.00\",\"variance\":\"0.00\",\"progressPercent\":\"100.00\"},{\"goalName\":\"Nike Vomero 5\",\"targetAmount\":\"18000.00\",\"currentAmount\":\"10000.00\",\"variance\":\"8000.00\",\"progressPercent\":\"55.56\"},{\"goalName\":\"new phone\",\"targetAmount\":\"3333.00\",\"currentAmount\":\"212.00\",\"variance\":\"3121.00\",\"progressPercent\":\"6.36\"},{\"goalName\":\"Monitor\",\"targetAmount\":\"12000.00\",\"currentAmount\":\"1200.00\",\"variance\":\"10800.00\",\"progressPercent\":\"10.00\"},{\"goalName\":\"new phone\",\"targetAmount\":\"123.00\",\"currentAmount\":\"12.00\",\"variance\":\"111.00\",\"progressPercent\":\"9.76\"},{\"goalName\":\"sdad\",\"targetAmount\":\"231.00\",\"currentAmount\":\"32.00\",\"variance\":\"199.00\",\"progressPercent\":\"13.85\"},{\"goalName\":\"sads\",\"targetAmount\":\"232.00\",\"currentAmount\":\"23.00\",\"variance\":\"209.00\",\"progressPercent\":\"9.91\"},{\"goalName\":\"New Phone (Poco X7 Pro)\",\"targetAmount\":\"123.00\",\"currentAmount\":\"121.00\",\"variance\":\"2.00\",\"progressPercent\":\"98.37\"},{\"goalName\":\"new shoes\",\"targetAmount\":\"1233.00\",\"currentAmount\":\"121.00\",\"variance\":\"1112.00\",\"progressPercent\":\"9.81\"},{\"goalName\":\"Guitar\",\"targetAmount\":\"15000.00\",\"currentAmount\":\"10000.00\",\"variance\":\"5000.00\",\"progressPercent\":\"66.67\"},{\"goalName\":\"asd\",\"targetAmount\":\"123.00\",\"currentAmount\":\"123.00\",\"variance\":\"0.00\",\"progressPercent\":\"100.00\"},{\"goalName\":\"New Pc\",\"targetAmount\":\"70000.00\",\"currentAmount\":\"50000.00\",\"variance\":\"20000.00\",\"progressPercent\":\"71.43\"},{\"goalName\":\"new phone\",\"targetAmount\":\"12321.00\",\"currentAmount\":\"1232.00\",\"variance\":\"11089.00\",\"progressPercent\":\"10.00\"},{\"goalName\":\"adidas\",\"targetAmount\":\"1000.00\",\"currentAmount\":\"600.00\",\"variance\":\"400.00\",\"progressPercent\":\"60.00\"},{\"goalName\":\"panda dunks\",\"targetAmount\":\"6000.00\",\"currentAmount\":\"2500.00\",\"variance\":\"3500.00\",\"progressPercent\":\"41.67\"},{\"goalName\":\"dsa\",\"targetAmount\":\"232.00\",\"currentAmount\":\"232.00\",\"variance\":\"0.00\",\"progressPercent\":\"100.00\"}],\"expensesInsights\":[\"You are within your budget. Great job managing your expenses!\"],\"savingsRateInsights\":[\"Your overall savings rate is below 40%. Consider cutting discretionary spending to increase your savings rate.\"]}'),
(25, 1300739497, '2025-04-01', '{\"budget\":\"0.00\",\"total_expenses\":\"0.00\",\"balance\":\"0.00\",\"savings_rate\":\"0.00\",\"savingGoals\":[{\"goalName\":\"New Phone (Poco X7 Pro)\",\"targetAmount\":\"17000.00\",\"currentAmount\":\"17000.00\",\"variance\":\"0.00\",\"progressPercent\":\"100.00\"},{\"goalName\":\"Nike Vomero 5\",\"targetAmount\":\"18000.00\",\"currentAmount\":\"10000.00\",\"variance\":\"8000.00\",\"progressPercent\":\"55.56\"},{\"goalName\":\"new phone\",\"targetAmount\":\"3333.00\",\"currentAmount\":\"212.00\",\"variance\":\"3121.00\",\"progressPercent\":\"6.36\"},{\"goalName\":\"Monitor\",\"targetAmount\":\"12000.00\",\"currentAmount\":\"1200.00\",\"variance\":\"10800.00\",\"progressPercent\":\"10.00\"},{\"goalName\":\"new phone\",\"targetAmount\":\"123.00\",\"currentAmount\":\"12.00\",\"variance\":\"111.00\",\"progressPercent\":\"9.76\"},{\"goalName\":\"sdad\",\"targetAmount\":\"231.00\",\"currentAmount\":\"32.00\",\"variance\":\"199.00\",\"progressPercent\":\"13.85\"},{\"goalName\":\"sads\",\"targetAmount\":\"232.00\",\"currentAmount\":\"23.00\",\"variance\":\"209.00\",\"progressPercent\":\"9.91\"},{\"goalName\":\"New Phone (Poco X7 Pro)\",\"targetAmount\":\"123.00\",\"currentAmount\":\"121.00\",\"variance\":\"2.00\",\"progressPercent\":\"98.37\"},{\"goalName\":\"new shoes\",\"targetAmount\":\"1233.00\",\"currentAmount\":\"121.00\",\"variance\":\"1112.00\",\"progressPercent\":\"9.81\"},{\"goalName\":\"Guitar\",\"targetAmount\":\"15000.00\",\"currentAmount\":\"10000.00\",\"variance\":\"5000.00\",\"progressPercent\":\"66.67\"},{\"goalName\":\"asd\",\"targetAmount\":\"123.00\",\"currentAmount\":\"123.00\",\"variance\":\"0.00\",\"progressPercent\":\"100.00\"},{\"goalName\":\"New Pc\",\"targetAmount\":\"70000.00\",\"currentAmount\":\"50000.00\",\"variance\":\"20000.00\",\"progressPercent\":\"71.43\"},{\"goalName\":\"new phone\",\"targetAmount\":\"12321.00\",\"currentAmount\":\"1232.00\",\"variance\":\"11089.00\",\"progressPercent\":\"10.00\"},{\"goalName\":\"adidas\",\"targetAmount\":\"1000.00\",\"currentAmount\":\"600.00\",\"variance\":\"400.00\",\"progressPercent\":\"60.00\"},{\"goalName\":\"panda dunks\",\"targetAmount\":\"6000.00\",\"currentAmount\":\"2500.00\",\"variance\":\"3500.00\",\"progressPercent\":\"41.67\"},{\"goalName\":\"dsa\",\"targetAmount\":\"232.00\",\"currentAmount\":\"232.00\",\"variance\":\"0.00\",\"progressPercent\":\"100.00\"}],\"expensesInsights\":[\"You are within your budget. Great job managing your expenses!\"],\"savingsRateInsights\":[\"Your overall savings rate is below 40%. Consider cutting discretionary spending to increase your savings rate.\"]}'),
(26, 374163220, '2025-05-01', '{\"budget\":\"90000.00\",\"total_expenses\":\"87100.00\",\"balance\":\"2900.00\",\"savings_rate\":\"3.22\",\"savingGoals\":[{\"goalName\":\"new phone\",\"targetAmount\":\"13000.00\",\"currentAmount\":\"10000.00\",\"variance\":\"3000.00\",\"progressPercent\":\"76.92\"},{\"goalName\":\"Toyota Camry\",\"targetAmount\":\"2500000.00\",\"currentAmount\":\"200000.00\",\"variance\":\"2300000.00\",\"progressPercent\":\"8.00\"},{\"goalName\":\"New Phone (Poco X7 Pr)\",\"targetAmount\":\"17000.00\",\"currentAmount\":\"10000.00\",\"variance\":\"7000.00\",\"progressPercent\":\"58.82\"},{\"goalName\":\"Necklace\",\"targetAmount\":\"5000.00\",\"currentAmount\":\"1000.00\",\"variance\":\"4000.00\",\"progressPercent\":\"20.00\"},{\"goalName\":\"New Phone (Poco X7 Pro)\",\"targetAmount\":\"17000.00\",\"currentAmount\":\"15000.00\",\"variance\":\"2000.00\",\"progressPercent\":\"88.24\"}],\"expensesInsights\":[\"You are within your budget. Great job managing your expenses!\"],\"savingsRateInsights\":[\"Your overall savings rate is below 40%. Consider cutting discretionary spending to increase your savings rate.\"]}');

-- --------------------------------------------------------

--
-- Table structure for table `savinggoals`
--

CREATE TABLE `savinggoals` (
  `goalID` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `goalName` varchar(255) NOT NULL,
  `Date` date DEFAULT NULL,
  `targetAmount` int(11) NOT NULL,
  `currentAmount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `savinggoals`
--

INSERT INTO `savinggoals` (`goalID`, `user_id`, `goalName`, `Date`, `targetAmount`, `currentAmount`) VALUES
(33, 1300739497, 'New Phone (Poco X7 Pro)', NULL, 17000, 17000),
(34, 1300739497, 'Nike Vomero 5', NULL, 18000, 10000),
(46, 374163220, 'new phone', NULL, 13000, 10000),
(58, 922445061, 'new phone', NULL, 32, 32),
(70, 228698946, 'iPhone 16 Pro Max Ultimate Max', NULL, 800000, 60000),
(71, 374163220, 'Toyota Camry', NULL, 2500000, 200000),
(72, 374163220, 'New Phone (Poco X7 Pr)', NULL, 17000, 10000),
(74, 1300739497, 'new phone', NULL, 3333, 212),
(75, 1300739497, 'Monitor', NULL, 12000, 1200),
(76, 374163220, 'Necklace', NULL, 5000, 1000),
(77, 1300739497, 'new phone', NULL, 123, 12),
(78, 1300739497, 'sdad', NULL, 231, 32),
(79, 1300739497, 'sads', NULL, 232, 23),
(84, 1300739497, 'asd', '2025-05-24', 123, 123),
(85, 1300739497, 'New Pc', '2025-05-24', 70000, 50000),
(86, 1300739497, 'new phone', '2025-05-24', 12321, 1232),
(87, 1300739497, 'adidas', '2025-05-24', 1000, 600),
(88, 1300739497, 'panda dunks', '2025-05-24', 6000, 2500),
(89, 1300739497, 'dsa', '2025-05-24', 232, 232),
(90, 1300739497, 'Slippers', '2025-05-24', 500, 300),
(91, 374163220, 'New Phone (Poco X7 Pro)', '2025-05-24', 17000, 15000);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `unique_id` int(11) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `unique_id`, `fname`, `lname`, `email`, `password`, `img`, `reset_token`, `token_expiry`) VALUES
(1, 374163220, 'John Paul', 'Cabaltera', 'paul@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 'Screenshot 2025-05-08 003946.png', NULL, NULL),
(2, 922445061, 'pol', 'pol', 'jp@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', '1747719448Screenshot 2025-05-07 223245.png', NULL, NULL),
(3, 516089344, 'paul', 'wew', 'p@gmail.com', 'd93591bdf7860e1e4ee2fca799911215', '1747728013Screenshot 2025-05-10 112858.png', NULL, NULL),
(4, 182433002, 'pj', 'kalbo', 'kalbo@gmail.com', 'b82927b699ff296985200b56615951dc', '1747732656Screenshot 2025-05-07 173021.png', NULL, NULL),
(5, 1652663942, 'emer', 'nodado', 'emer@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', '1747733207Screenshot 2025-05-08 003946.png', NULL, NULL),
(6, 1300739497, 'Paul', 'Cab', 'Paulv@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '1747828924Screenshot 2025-05-10 101649.png', NULL, NULL),
(7, 228698946, 'robert', 'cabaltera', 'robert@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', '1747921745Screenshot 2025-05-07 012810.png', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`BudgetID`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`CategoryID`),
  ADD UNIQUE KEY `CategoryName` (`CategoryName`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`exp_id`),
  ADD KEY `CategoryID` (`CategoryID`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `categoryName` (`CategoryName`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`reportID`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `savinggoals`
--
ALTER TABLE `savinggoals`
  ADD PRIMARY KEY (`goalID`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `BudgetID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `exp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `reportID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `savinggoals`
--
ALTER TABLE `savinggoals`
  MODIFY `goalID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `budgets`
--
ALTER TABLE `budgets`
  ADD CONSTRAINT `budgets_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`unique_id`);

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `categories` (`CategoryID`),
  ADD CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`unique_id`),
  ADD CONSTRAINT `expenses_ibfk_3` FOREIGN KEY (`categoryName`) REFERENCES `categories` (`CategoryName`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`unique_id`);

--
-- Constraints for table `savinggoals`
--
ALTER TABLE `savinggoals`
  ADD CONSTRAINT `savinggoals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`unique_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
