-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 25, 2025 at 08:59 AM
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
-- Database: `internetbanking`
--

-- --------------------------------------------------------

--
-- Table structure for table `client_feedback`
--

CREATE TABLE `client_feedback` (
  `id` int(10) UNSIGNED NOT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `subject` varchar(255) NOT NULL,
  `feedback_message` text NOT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client_feedback`
--

INSERT INTO `client_feedback` (`id`, `client_id`, `subject`, `feedback_message`, `submission_date`) VALUES
(3, 10, 'credit issue', 'Here\'s a detailed feedback message for a credit issue:\r\n\r\n\"I am writing to report ongoing issues with my credit card account. Over the past three months, I\'ve noticed several concerning problems that require immediate attention. First, there have been multiple instances of delayed transaction postings, sometimes taking 5-7 business days to appear on my account, making it difficult to track my spending accurately. On two occasions (January 15th and February 1st), I made payments at retail stores that were initially processed but then mysteriously disappeared from my transaction history, only to reappear several days later with different posting dates.\r\n\r\nAdditionally, I\'ve observed that my credit limit was suddenly reduced from Rs. 100,000 to Rs. 75,000 without any prior notification. This has caused significant inconvenience as I rely on this credit line for my business expenses. I\'ve tried contacting customer service through your phone system three times (reference numbers: #CR78901, #CR78955, and #CR79012), but have received inconsistent information about these issues.\r\n\r\nMost concerning is the fact that my last payment of Rs. 25,000, made on January 28th, 2025, was correctly deducted from my bank account but hasn\'t been reflected in my credit card balance even after 9 days. This has resulted in incorrect late payment fees being applied to my account.\r\n\r\nI would greatly appreciate if someone could thoroughly investigate these issues, particularly the missing payment and unauthorized credit limit reduction. This situation has caused considerable stress and has impacted my ability to manage my finances effectively. Please review my account history and provide a detailed explanation of these discrepancies, along with steps being taken to prevent such issues in the future.\r\n\r\nFor reference, I have maintained an excellent payment history for the past four years and would like to resolve these matters as soon as possible to continue our banking relationship posit', '2025-02-06 08:58:13');

-- --------------------------------------------------------

--
-- Table structure for table `ib_acc_types`
--

CREATE TABLE `ib_acc_types` (
  `acctype_id` int(20) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` longtext NOT NULL,
  `rate` varchar(200) NOT NULL,
  `code` varchar(200) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `min_balance` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `ib_acc_types`
--

INSERT INTO `ib_acc_types` (`acctype_id`, `name`, `description`, `rate`, `code`, `is_active`, `min_balance`) VALUES
(1, 'Savings', '<p>Savings accounts&nbsp;are typically the first official bank account anybody opens. Children may open an account with a parent to begin a pattern of saving. Teenagers open accounts to stash cash earned&nbsp;from a first job&nbsp;or household chores.</p><p>Savings accounts are an excellent place to park&nbsp;emergency cash. Opening a savings account also marks the beginning of your relationship with a financial institution. For example, when joining a credit union, your &ldquo;share&rdquo; or savings account establishes your membership.</p>', '20', 'ACC-CAT-4EZFO', 1, 1000.00),
(2, ' Retirement', '<p>Retirement accounts&nbsp;offer&nbsp;tax advantages. In very general terms, you get to&nbsp;avoid paying income tax on interest&nbsp;you earn from a savings account or CD each year. But you may have to pay taxes on those earnings at a later date. Still, keeping your money sheltered from taxes may help you over the long term. Most banks offer IRAs (both&nbsp;Traditional IRAs&nbsp;and&nbsp;Roth IRAs), and they may also provide&nbsp;retirement accounts for small businesses</p>', '10', 'ACC-CAT-1QYDV', 1, 0.00),
(4, 'Recurring deposit', '<p><strong>Recurring deposit account or RD account</strong> is opened by those who want to save certain amount of money regularly for a certain period of time and earn a higher interest rate.&nbsp;In RD&nbsp;account a&nbsp;fixed amount is deposited&nbsp;every month for a specified period and the total amount is repaid with interest at the end of the particular fixed period.&nbsp;</p><p>The period of deposit is minimum six months and maximum ten years.&nbsp;The interest rates vary&nbsp;for different plans based on the amount one saves and the period of time and also on banks. No withdrawals are allowed from the RD account. However, the bank may allow to close the account before the maturity period.</p><p>These accounts can be opened in single or joint names. Banks are also providing the Nomination facility to the RD account holders.&nbsp;</p>', '15', 'ACC-CAT-VBQLE', 1, 2000.00),
(5, 'Fixed Deposit Account', '<p>In <strong>Fixed Deposit Account</strong> (also known as <strong>FD Account</strong>), a particular sum of money is deposited in a bank for specific&nbsp;period of time. It&rsquo;s one time deposit and one time take away (withdraw) account.&nbsp;The money deposited in this account can not be withdrawn before the expiry of period.&nbsp;</p><p>However, in case of need,&nbsp; the depositor can ask for closing the fixed deposit prematurely by paying a penalty. The penalty amount varies with banks.</p><p>A high interest rate is paid on fixed deposits. The rate of interest paid for fixed deposit vary according to amount, period and also from bank to bank.</p>', '40', 'ACC-CAT-A86GO', 1, 10000.00),
(7, 'Current account ', '<p><strong>Current account</strong> is mainly for business per<strong>s</strong>ons, firms, companies, public enterprises etc and are never used for the purpose of investment or savings.These deposits are the most liquid deposits and there are no limits for number of transactions or the amount of transactions in a day. While, there is no interest paid on amount held in the account, banks charges certain &nbsp;service charges, on such accounts. The current accounts do not have any fixed maturity as thegadegagagase are on continuous basis accounts.</p>', '19', 'ACC-CAT-4O8QW', 1, 5000.00),
(8, 'Salary Account', '<p>A <strong>salary account</strong> is a bank account where an employer directly deposits an employee&rsquo;s salary. It usually has <strong>zero balance requirements</strong>, <strong>free debit card and chequebook</strong>, <strong>higher transaction limits</strong>, <strong>overdraft facility</strong>, and <strong>easy loan approvals</strong>. It also provides <strong>internet and mobile banking</strong> for seamless transactions.</p>', '6.5', 'ACC-CAT-27DQV', 1, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `ib_admin`
--

CREATE TABLE `ib_admin` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `number` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `profile_pic` varchar(200) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `otp` varchar(6) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `ib_admin`
--

INSERT INTO `ib_admin` (`admin_id`, `name`, `email`, `number`, `password`, `profile_pic`, `is_active`, `otp`, `otp_expiry`) VALUES
(2, 'System Administrator', 'dholajenil2024.katargam@gmail.com', 'iBank-ADM-0516', '$2y$10$jahdlQB9wxGjbIrq2ZgJteKStOf8E6O5vAJblpWXadOhCUXkRBPlG', 'admin-icn.png', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ib_bankaccounts`
--

CREATE TABLE `ib_bankaccounts` (
  `account_id` int(20) NOT NULL,
  `acc_name` varchar(200) NOT NULL,
  `account_number` varchar(200) NOT NULL,
  `acc_type` varchar(200) NOT NULL,
  `acc_rates` varchar(200) NOT NULL,
  `acc_status` varchar(200) NOT NULL,
  `acc_amount` decimal(10,2) NOT NULL,
  `client_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `ib_bankaccounts`
--

INSERT INTO `ib_bankaccounts` (`account_id`, `acc_name`, `account_number`, `acc_type`, `acc_rates`, `acc_status`, `acc_amount`, `client_id`, `created_at`, `is_active`) VALUES
(14, 'Hari pandya', '357146928', 'Current account ', '20', 'Active', 70361.00, 5, '2025-02-25 07:56:15.621750', 1),
(15, 'Arin Gabani', '287359614', 'Savings ', '20', 'Active', 2004000.00, 8, '2025-02-25 07:55:18.818226', 1),
(16, 'Vraj Gujrati', '705239816', ' Retirement ', '10', 'Active', 10339.00, 6, '2025-02-23 14:25:50.735691', 1),
(19, 'Jenil Dhola', '864790325', 'Current account  ', '19', 'Active', 2000.00, 10, '2025-02-25 07:55:01.008708', 1),
(20, 'kirtan moradiya', '730459816', 'Recurring deposit', '15', 'Active', 921340.00, 10, '2025-02-25 07:55:18.819070', 1),
(23, 'Yashpal Chavda', '573608192', 'Savings', '15', 'Active', 26486.00, 11, '2025-02-25 06:05:18.844339', 1),
(24, 'Rohit Dhull', '529714806', 'Salary Account', '6.5', 'Active', 9312.00, 11, '2025-02-25 07:55:09.613763', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ib_clients`
--

CREATE TABLE `ib_clients` (
  `client_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `phone` varchar(200) NOT NULL,
  `address` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `profile_pic` varchar(200) NOT NULL,
  `client_number` varchar(200) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `otp` varchar(10) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `ib_clients`
--

INSERT INTO `ib_clients` (`client_id`, `name`, `phone`, `address`, `email`, `password`, `profile_pic`, `client_number`, `is_active`, `otp`, `otp_expiry`) VALUES
(3, 'Babar Azam', '9897890089', '127007 Localhost', 'johndoe@gmail.com', 'a69681bcf334ae130217fea4505fd3c994f5683f', '', 'iBank-CLIENT-8127', 1, NULL, NULL),
(5, 'Harry Den', '7412560000', '114 Allace Avenue', 'harryden@mail.com', '55c3b5386c486feb662a0785f340938f518d547f', '', 'iBank-CLIENT-7014', 1, NULL, NULL),
(6, 'Harshit Rana', '7412545454', '23 Hinkle Deegan Lake Road', 'reyes@mail.com', '55c3b5386c486feb662a0785f340938f518d547f', 'user-profile-min.png', 'iBank-CLIENT-1698', 1, NULL, NULL),
(8, 'Arin Gabani', '7850000014', '92 Maple Street', 'amanda@mail.com', '55c3b5386c486feb662a0785f340938f518d547f', 'user-profile-min.png', 'iBank-CLIENT-0423', 1, NULL, NULL),
(9, 'Aakash Chopra', '7014569696', '46 Timberbrook Lane', 'Achopra@mail.com', '55c3b5386c486feb662a0785f340938f518d547f', '', 'iBank-CLIENT-4716', 1, NULL, NULL),
(10, 'Utsav Chheta', '8799050118', 'A/2 -203,devi complex,dabholi char rasta,ved road,surat', 'jenildhola1811@gmail.com', '$2y$10$5JzYj7ScY6LnJw/gLWKM5eWjNc1BJZfz3IuskJ8RYKgRLtLHUCW1y', '', 'iBank-CLIENT-6482', 1, '181001', '2025-02-20 11:23:59'),
(11, 'Jenil Dhola', '9979735065', 'A-2/203,DEVI COMPLEX,DABHOLI CHAR RASTA', 'shreeji.gamer.bot@gmail.com', '$2y$10$TJhMZCz1DCdTDSGoKgD9le9MZx/vpCumj9WPxCwY2oVza3w9YkP06', '', 'iBank-CLIENT-2438', 1, '762044', '2025-02-20 11:36:16');

-- --------------------------------------------------------

--
-- Table structure for table `ib_nominees`
--

CREATE TABLE `ib_nominees` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `nominee_name` varchar(255) NOT NULL,
  `relation` varchar(100) NOT NULL,
  `nominee_email` varchar(255) DEFAULT NULL,
  `nominee_phone` varchar(20) DEFAULT NULL,
  `nominee_address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ib_nominees`
--

INSERT INTO `ib_nominees` (`id`, `client_id`, `nominee_name`, `relation`, `nominee_email`, `nominee_phone`, `nominee_address`, `created_at`, `is_active`) VALUES
(1, 11, 'Bhavnaben Dhola', 'Mother', 'jenildhola1811@gmail.com', '99025063124', 'A-2/203,DEVI COMPLEX,DABHOLI CHAR RASTA', '2025-02-20 06:29:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ib_notifications`
--

CREATE TABLE `ib_notifications` (
  `notification_id` int(20) NOT NULL,
  `notification_details` text NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `ib_notifications`
--

INSERT INTO `ib_notifications` (`notification_id`, `notification_details`, `created_at`, `is_active`) VALUES
(30, 'Christine Moore Has Transfered Rs.20 From Bank Account 421873905 To Bank Account 287359614', '2024-12-16 14:37:17.891954', 1),
(31, 'Jenil Dhola has deposited Rs.100000 into bank account 864790325', '2024-12-16 14:42:23.963486', 1),
(32, 'Jenil Dhola Has Withdrawn Rs. 100 From Bank Account 864790325', '2024-12-16 14:42:35.789915', 1),
(33, 'Jenil Dhola Has Transferred Rs.50000 From Bank Account 864790325 To Bank Account 724310586', '2024-12-16 14:42:46.593408', 1),
(34, 'Jenil Dhola has deposited Rs.100 into bank account 864790325', '2025-01-16 09:28:30.966334', 1),
(35, 'kirtanmoradiya has deposited Rs.50000 into bank account 573608192', '2025-02-02 05:55:05.862578', 1),
(36, 'kirtanmoradiya Has Withdrawn Rs. 10000 From Bank Account 573608192', '2025-02-02 07:12:37.265701', 1),
(37, 'kirtanmoradiya Has Transferred Rs.100 From Bank Account 573608192 To Bank Account 287359614', '2025-02-02 07:12:56.670711', 1),
(38, 'Christine Moore has deposited Rs.100 to bank account 421873905', '2025-02-03 17:20:33.553365', 1),
(44, 'Jenil Dhola has deposited Rs.50000 into bank account ', '2025-02-19 04:50:02.093556', 1),
(45, 'Jenil Dhola Has Withdrawn Rs. 50000 From Bank Account 864790325', '2025-02-19 05:27:49.696966', 1),
(46, 'Jenil Dhola Has Withdrawn Rs. 100 From Bank Account 864790325', '2025-02-19 05:27:55.390326', 1),
(47, 'Jenil Dhola Has Transferred Rs.20 From Bank Account 864790325 To Bank Account 864790325', '2025-02-19 05:32:22.010848', 1),
(48, 'Jenil Dhola has deposited Rs.789 into bank account ', '2025-02-19 07:53:30.468552', 1),
(49, 'Harry Den has deposited Rs.50000 into bank account ', '2025-02-19 07:55:14.389922', 1),
(60, 'Jenil Dhola has deposited Rs. 50000 into bank account 23', '2025-02-21 14:25:42.244813', 1),
(61, 'Utsav Chheta has deposited Rs. 1000000 into bank account 20', '2025-02-21 14:32:41.558193', 1),
(62, 'Utsav Chheta Has Withdrawn Rs. 50000 From Bank Account 730459816', '2025-02-22 08:51:35.204500', 1),
(63, 'Utsav Chheta has transferred Rs. 50000 from Bank Account 730459816 to Bank Account 573608192', '2025-02-22 08:55:20.094948', 1),
(64, 'Jenil Dhola has deposited Rs. 10000 into bank account 23', '2025-02-23 12:09:58.242033', 1),
(65, 'Jenil Dhola Has Withdrawn Rs. 1500 From Bank Account 573608192', '2025-02-23 12:10:13.835518', 1),
(66, 'A deposit of Rs. 100 has been made into Bank Account 357146928', '2025-02-23 12:29:48.061887', 1),
(67, 'Jenil Dhola has deposited Rs. 10000 into bank account 24', '2025-02-23 12:30:06.881084', 1),
(68, 'A deposit of Rs. 1000000 has been made into Bank Account 287359614', '2025-02-23 12:44:19.052409', 1),
(69, 'A deposit of Rs. 1000000 has been made into Bank Account 287359614', '2025-02-23 12:47:10.886948', 1),
(70, 'A deposit of Rs. 100000 has been made into Bank Account 573608192', '2025-02-23 17:12:16.964737', 1),
(71, 'Harry Den has withdrawn Rs. 222 from Bank Account 357146928', '2025-02-25 06:49:22.746139', 1),
(72, 'Harry Den has withdrawn Rs. 100 from Bank Account 357146928', '2025-02-25 06:49:26.454014', 1),
(73, 'Harry Den has withdrawn Rs. 100 from Bank Account 357146928', '2025-02-25 06:49:30.749347', 1),
(74, 'Harry Den has withdrawn Rs. 5000 from Bank Account 357146928', '2025-02-25 06:49:34.124488', 1),
(75, 'Harry Den has withdrawn Rs. 100 from Bank Account 357146928', '2025-02-25 06:49:46.156092', 1),
(76, 'Harry Den has withdrawn Rs. 100 from Bank Account 357146928', '2025-02-25 06:49:50.267011', 1),
(77, 'Harry Den has withdrawn Rs. 100 from Bank Account 357146928', '2025-02-25 06:49:53.830524', 1),
(78, 'Harry Den has withdrawn Rs. 100 from Bank Account 357146928', '2025-02-25 06:51:09.058123', 1),
(79, 'Harry Den has withdrawn Rs. 100 from Bank Account 357146928', '2025-02-25 06:51:15.887096', 1),
(80, 'Harry Den has withdrawn Rs. 100 from Bank Account 357146928', '2025-02-25 06:51:21.204757', 1),
(81, 'Harry Den has withdrawn Rs. 100 from Bank Account 357146928', '2025-02-25 06:51:37.327515', 1),
(82, 'Harry Den has withdrawn Rs. 100 from Bank Account 357146928', '2025-02-25 06:53:10.349008', 1),
(83, 'Harry Den has withdrawn Rs. 100 from Bank Account 357146928', '2025-02-25 06:53:17.204486', 1),
(84, 'Harry Den has withdrawn Rs. 100 from Bank Account 357146928', '2025-02-25 06:53:55.687697', 1),
(86, 'Harry Den has withdrawn Rs. 100 from Bank Account 357146928', '2025-02-25 07:27:00.944058', 1),
(87, 'Harry Den has withdrawn Rs. 100 from Bank Account 357146928', '2025-02-25 07:29:45.407215', 1),
(88, 'Harry Den has withdrawn Rs. 100 from Bank Account 357146928', '2025-02-25 07:31:05.870439', 1),
(89, 'Harry Den has withdrawn Rs. 1200 from Bank Account 357146928', '2025-02-25 07:33:22.520159', 1),
(90, 'Harry Den has withdrawn Rs. 1200 from Bank Account 357146928', '2025-02-25 07:34:30.657984', 1),
(91, 'Arin Gabani has withdrawn Rs. 18000 from Bank Account 287359614', '2025-02-25 07:35:25.769536', 1),
(92, 'Arin Gabani has withdrawn Rs. 18000 from Bank Account 287359614', '2025-02-25 07:35:56.496813', 1),
(93, 'Arin Gabani has withdrawn Rs. 18000 from Bank Account 287359614', '2025-02-25 07:38:06.903347', 1),
(94, 'Arin Gabani has withdrawn Rs. 1200 from Bank Account 287359614', '2025-02-25 07:51:10.546682', 1),
(95, 'Harry Den has withdrawn Rs. 100 from Bank Account 357146928', '2025-02-25 07:52:08.535846', 1),
(96, 'A deposit of Rs. 18000 has been made into Bank Account 357146928', '2025-02-25 07:56:06.177748', 1),
(97, 'A deposit of Rs. 52000 has been made into Bank Account 357146928', '2025-02-25 07:56:15.624178', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ib_staff`
--

CREATE TABLE `ib_staff` (
  `staff_id` int(20) NOT NULL,
  `name` varchar(200) NOT NULL,
  `staff_number` varchar(200) NOT NULL,
  `phone` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `sex` varchar(200) NOT NULL,
  `profile_pic` varchar(200) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `otp` varchar(6) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `ib_staff`
--

INSERT INTO `ib_staff` (`staff_id`, `name`, `staff_number`, `phone`, `email`, `password`, `sex`, `profile_pic`, `is_active`, `otp`, `otp_expiry`) VALUES
(3, 'Jay Shah', 'iBank-STAFF-6785', '7049757429', 'dharmika192@gmail.com', '$2y$10$Kh5mefahn9r3uUbthwxXi.XuCSOV9Y5Nsk8SrhT82uFR15pufqYz6', 'Male', '', 1, '719130', '2025-02-20 10:48:33'),
(4, 'Rahul Dravid', 'iBank-STAFF-6724', '9265460571', 'wall@gmail.com', 'd95d3bbedb4dcba5a8e891968853002354b028e9', 'Male', 'rahul.jpg', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ib_systemsettings`
--

CREATE TABLE `ib_systemsettings` (
  `id` int(20) NOT NULL,
  `sys_name` longtext NOT NULL,
  `sys_tagline` longtext NOT NULL,
  `sys_logo` varchar(200) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `ib_systemsettings`
--

INSERT INTO `ib_systemsettings` (`id`, `sys_name`, `sys_tagline`, `sys_logo`, `is_active`) VALUES
(1, 'DigiBankX', 'Digital banking revolution', 'bank.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ib_transactions`
--

CREATE TABLE `ib_transactions` (
  `tr_id` int(20) NOT NULL,
  `tr_code` varchar(200) NOT NULL,
  `account_id` int(20) NOT NULL,
  `tr_type` varchar(200) NOT NULL,
  `tr_status` varchar(200) NOT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `transaction_amt` varchar(200) NOT NULL,
  `receiving_acc_no` varchar(200) DEFAULT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `ib_transactions`
--

INSERT INTO `ib_transactions` (`tr_id`, `tr_code`, `account_id`, `tr_type`, `tr_status`, `client_id`, `transaction_amt`, `receiving_acc_no`, `created_at`, `is_active`) VALUES
(90, '3Fj2KywUPhZqsbn90xRp', 20, 'Deposit', 'Success', 10, '1000000', '', '2025-02-21 14:32:41.556773', 1),
(91, 'VvKlencjTo1f6NPQ9siH', 20, 'Withdrawal', 'Success ', 10, '50000', '', '2025-02-22 08:51:35.201512', 1),
(92, 'b5qYQ40gvnauUOD8iMfl', 20, 'Transfer', 'Success ', 10, '50000', '', '2025-02-22 08:55:20.094697', 1),
(95, 'dU1ykHVsqDFP7g9ShcZR', 23, 'Deposit', 'Success', 11, '10000', '', '2025-02-23 12:09:58.241214', 1),
(96, 'vgmrt9xW5d1n2aMQkV6j', 23, 'Withdrawal', 'Success ', 11, '1500', '', '2025-02-23 12:10:13.832713', 1),
(97, 'DJyFP59gKakXtfqrpViW', 23, 'Transfer', 'Success', 11, '10000', '705239816', '2025-02-23 12:27:23.000000', 1),
(98, 'EybJDPiQRet4VY9WBg0v', 14, 'Deposit', 'Success', 5, '100', '', '2025-02-23 12:29:48.061610', 1),
(99, '27UFKMjygt6oicNfavxS', 24, 'Deposit', 'Success', 11, '10000', '', '2025-02-23 12:30:06.880880', 1),
(100, '4eE7TRJAi5vklOWbstVQ', 23, 'Transfer', 'Success', 11, '100', '705239816', '2025-02-23 12:36:08.000000', 1),
(102, 'vZRaVb5SqrjT4wJsQF8A', 15, 'Deposit', 'Success', 8, '1000000', '', '2025-02-23 12:47:10.885519', 1),
(105, '02Ixq5XiranltFu8gPA9', 14, 'Transfer', 'Success', 5, '59', '573608192', '2025-02-23 13:09:58.000000', 1),
(106, '4eE7TRJAi5vklOWbstVQ', 23, 'Transfer', 'Success', 11, '100', '705239816', '2025-02-23 13:10:15.000000', 1),
(107, 'lRWZzpieXyKbJhcuHfw5', 23, 'Transfer', 'Success', 11, '10000', '357146928', '2025-02-23 13:11:41.000000', 1),
(110, 'adpbo3BrLsPFcu6VgKTh', 14, 'Transfer', 'Success', 5, '100', '705239816', '2025-02-23 13:45:24.000000', 1),
(111, 'P8lqXfcMVSADgUoreLCb', 15, 'Transfer', 'Success', 8, '100', '529714806', '2025-02-23 14:15:41.000000', 1),
(115, 'DZiuj4F1pw5OEUSTvoC3', 14, 'Transfer', 'Success', 5, '100', '730459816', '2025-02-23 14:23:39.000000', 1),
(120, '2yXC9BRlKaMUjquiSGdN', 23, 'Deposit', 'Success', 11, '100000', '', '2025-02-23 17:12:16.964154', 1),
(121, '9oAhuYv0sZIFJe2EWOkN', 23, 'Transfer', 'Success', 11, '4000', '730459816', '2025-02-23 17:12:36.000000', 1),
(122, 'wzKmyf1F38cYgPTj7H2R', 23, 'Transfer', 'Success', 11, '78000', '287359614', '2025-02-23 17:12:42.000000', 1),
(123, 'BqdH0EAstU2F6L1MgZuK', 14, 'Withdrawal', 'Success', 5, '222', NULL, '2025-02-25 06:49:22.744531', 1),
(124, 'D6Kadpc3qVZTPuX5veyj', 14, 'Withdrawal', 'Success', 5, '100', NULL, '2025-02-25 06:49:26.452823', 1),
(125, 'fASsyFwtn3Vk2U8zcBrY', 14, 'Withdrawal', 'Success', 5, '100', NULL, '2025-02-25 06:49:30.749126', 1),
(126, 'NHfBG9jUsmzVohAD2Xg1', 14, 'Withdrawal', 'Success', 5, '5000', NULL, '2025-02-25 06:49:34.124232', 1),
(127, 'fASsyFwtn3Vk2U8zcBrY', 14, 'Withdrawal', 'Success', 5, '100', NULL, '2025-02-25 06:49:46.155632', 1),
(128, '7kb6ryQwBIs5Y4hCEOUo', 14, 'Withdrawal', 'Success', 5, '100', NULL, '2025-02-25 06:49:50.266794', 1),
(129, '376rajymil4kbHQM9VxB', 14, 'Withdrawal', 'Success', 5, '100', NULL, '2025-02-25 06:49:53.829640', 1),
(130, '376rajymil4kbHQM9VxB', 14, 'Withdrawal', 'Success', 5, '100', NULL, '2025-02-25 06:51:09.057776', 1),
(131, '376rajymil4kbHQM9VxB', 14, 'Withdrawal', 'Success', 5, '100', NULL, '2025-02-25 06:51:15.886412', 1),
(132, '376rajymil4kbHQM9VxB', 14, 'Withdrawal', 'Success', 5, '100', NULL, '2025-02-25 06:51:21.204215', 1),
(133, '376rajymil4kbHQM9VxB', 14, 'Withdrawal', 'Success', 5, '100', NULL, '2025-02-25 06:51:37.326988', 1),
(134, '376rajymil4kbHQM9VxB', 14, 'Withdrawal', 'Success', 5, '100', NULL, '2025-02-25 06:53:10.348573', 1),
(135, '376rajymil4kbHQM9VxB', 14, 'Withdrawal', 'Success', 5, '100', NULL, '2025-02-25 06:53:17.203085', 1),
(136, 'fASsyFwtn3Vk2U8zcBrY', 14, 'Withdrawal', 'Success', 5, '100', NULL, '2025-02-25 06:53:55.687404', 1),
(137, 'fASsyFwtn3Vk2U8zcBrY', 14, 'Withdrawal', 'Success', 5, '100', NULL, '2025-02-25 06:53:57.534846', 1),
(138, 'MjYvzOZbg2n93oh5xBJG', 14, 'Withdrawal', 'Success', 5, '100', NULL, '2025-02-25 07:27:00.942325', 1),
(139, 'X95YxNhuHbSkvDo0pzmq', 14, 'Withdrawal', 'Success', 5, '100', NULL, '2025-02-25 07:29:45.406100', 1),
(140, 'X95YxNhuHbSkvDo0pzmq', 14, 'Withdrawal', 'Success', 5, '100', NULL, '2025-02-25 07:31:05.869613', 1),
(141, 'QbI8dyB49G5HXW2ju1Nt', 14, 'Withdrawal', 'Success', 5, '1200', NULL, '2025-02-25 07:33:22.519599', 1),
(142, 'QbI8dyB49G5HXW2ju1Nt', 14, 'Withdrawal', 'Success', 5, '1200', NULL, '2025-02-25 07:34:30.657476', 1),
(143, 'OJGSDurUIQlo8F9q42Xp', 15, 'Withdrawal', 'Success', 8, '18000', NULL, '2025-02-25 07:35:25.769176', 1),
(144, 'OJGSDurUIQlo8F9q42Xp', 15, 'Withdrawal', 'Success', 8, '18000', NULL, '2025-02-25 07:35:56.496047', 1),
(145, 'OJGSDurUIQlo8F9q42Xp', 15, 'Withdrawal', 'Success', 8, '18000', NULL, '2025-02-25 07:38:06.902375', 1),
(146, 'eFv5sWty7h2z6HN4VpfI', 15, 'Withdrawal', 'Success', 8, '1200', NULL, '2025-02-25 07:51:10.544527', 1),
(147, '1mds6QFGXI8CoB2afuTi', 14, 'Withdrawal', 'Success', 5, '100', NULL, '2025-02-25 07:52:08.535054', 1),
(148, 'w7t3buIPxXLBYz69NWl2', 15, 'Transfer', 'Success', 8, '1200', '864790325', '2025-02-25 07:53:05.000000', 1),
(149, 'blA6BnF4Tzk3x10KyJpu', 20, 'Transfer', 'Success', 10, '800', '529714806', '2025-02-25 07:54:40.000000', 1),
(150, 'AUm0v2wPWYG9Sl1yHOFV', 24, 'Transfer', 'Success', 11, '800', '864790325', '2025-02-25 07:55:01.000000', 1),
(151, 'HJ74caPU2iXYfbkDL6Eo', 24, 'Transfer', 'Success', 11, '800', '287359614', '2025-02-25 07:55:09.000000', 1),
(152, 'hQR23ziWvyEAeUZmtMrN', 15, 'Transfer', 'Success', 8, '18000', '730459816', '2025-02-25 07:55:18.000000', 1),
(153, 'jJKdkWTtnmchzbHQXg8Y', 14, 'Deposit', 'Success', 5, '18000', NULL, '2025-02-25 07:56:06.176874', 1),
(154, 'OtXrJaKoAG6uvDR37BeM', 14, 'Deposit', 'Success', 5, '52000', NULL, '2025-02-25 07:56:15.623547', 1);

-- --------------------------------------------------------

--
-- Table structure for table `loan_applications`
--

CREATE TABLE `loan_applications` (
  `id` int(10) UNSIGNED NOT NULL,
  `applicant_name` varchar(255) NOT NULL,
  `loan_amount` decimal(10,2) NOT NULL,
  `application_date` datetime DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected','recommended') NOT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `admin_review_id` int(11) DEFAULT NULL,
  `review_date` datetime DEFAULT NULL,
  `staff_remark` text DEFAULT NULL,
  `admin_remark` text NOT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `loan_type_id` int(11) DEFAULT NULL,
  `is_approved_by_staff` tinyint(1) DEFAULT 0,
  `income_salary` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_applications`
--

INSERT INTO `loan_applications` (`id`, `applicant_name`, `loan_amount`, `application_date`, `status`, `reviewed_by`, `admin_review_id`, `review_date`, `staff_remark`, `admin_remark`, `client_id`, `loan_type_id`, `is_approved_by_staff`, `income_salary`) VALUES
(8, 'Darshan', 300000.00, '2025-02-04 12:59:46', 'rejected', 3, 2, '2025-02-17 13:18:49', 'yoo', 'sadssdsdsdf', 11, 10, 0, 60000.00),
(13, 'Jenil Dineshbhai dhola', 10000.00, '2025-02-16 18:36:01', 'approved', 3, 2, '2025-02-17 13:20:03', 'GOOD', 'reject', 10, 3, 0, 10000.00),
(14, 'utsav cheta', 100000.00, '2025-02-16 18:55:04', 'pending', 3, 2, NULL, 'HEYY', '', 10, 2, 0, 50000.00),
(16, 'HARH ', 1000.00, '2025-02-19 11:03:50', 'approved', 3, 2, '2025-02-19 11:04:33', 'good', 'approved', 10, 9, 0, 50000.00);

-- --------------------------------------------------------

--
-- Table structure for table `loan_types`
--

CREATE TABLE `loan_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `max_amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_types`
--

INSERT INTO `loan_types` (`id`, `type_name`, `description`, `interest_rate`, `max_amount`, `created_at`, `is_active`) VALUES
(2, 'Home_Loan', 'A Home Loan is a long-term financial product designed to help individuals buy property.', 8.25, 5000000.00, '2025-02-04 05:48:43', 1),
(3, 'Car_Loan', 'A Car Loan is used to finance the purchase of a vehicle, often with fixed interest rates.', 7.50, 30000.00, '2025-02-04 05:48:43', 1),
(4, 'Personal_Loan', 'A Personal Loan is an unsecured loan for personal expenses such as travel or medical needs.', 10.00, 20000.00, '2025-02-04 05:48:43', 1),
(5, 'Education_Loan', 'An Education Loan helps students finance their tuition and other academic expenses.', 5.75, 100000.00, '2025-02-04 05:48:43', 1),
(6, 'Business_Loan', 'A Business Loan provides funds to entrepreneurs and companies for business growth.', 9.00, 250000.00, '2025-02-04 05:48:43', 1),
(7, 'Gold_Loan', 'A Gold Loan is secured against gold jewelry and has a lower interest rate.', 6.50, 50000.00, '2025-02-04 05:48:43', 1),
(8, 'Credit_Card_Loan', 'A Credit Card Loan is an extension of credit card limits for financial flexibility.', 12.50, 15000.00, '2025-02-04 05:48:43', 1),
(9, 'Agriculture_Loan', 'An Agriculture Loan supports farmers in purchasing equipment, seeds, and livestock.', 4.50, 120000.00, '2025-02-04 05:48:43', 1),
(10, 'Mortgage_Loan', 'A Mortgage Loan allows individuals to borrow against the value of their property.', 7.80, 450000.00, '2025-02-04 05:48:43', 1),
(11, 'Two_Wheeler_Loan', 'A Two-Wheeler Loan is used to finance the purchase of motorcycles and scooters.', 8.00, 80000.00, '2025-02-04 05:48:43', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expiry` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `admin_id`, `token`, `expiry`) VALUES
(1, 2, 'bdbae7f327ee9d12d0c5c47a5bee32db6828d3fed359ef8caaf6d77fb192350a', '2025-01-21 15:43:18'),
(2, 2, 'd5cd7042b2dfc604af69a4f7fbf752bffc0f95c564f3bcf1a15a12fbb5714b72', '2025-01-23 15:41:40'),
(3, 2, 'bdb9e6e793564daa0689436de8b68a372335dcb5a040edcb4a47cf326a3d476e', '2025-01-23 15:41:42'),
(4, 2, '4478e3d37bfbf2e8e4f5816ca4559aff7de939cf3105d4514c9fd2e6636ad151', '2025-01-23 16:02:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `client_feedback`
--
ALTER TABLE `client_feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `ib_acc_types`
--
ALTER TABLE `ib_acc_types`
  ADD PRIMARY KEY (`acctype_id`);

--
-- Indexes for table `ib_admin`
--
ALTER TABLE `ib_admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `ib_bankaccounts`
--
ALTER TABLE `ib_bankaccounts`
  ADD PRIMARY KEY (`account_id`),
  ADD KEY `fk_ib_bankaccounts_clients` (`client_id`);

--
-- Indexes for table `ib_clients`
--
ALTER TABLE `ib_clients`
  ADD PRIMARY KEY (`client_id`);

--
-- Indexes for table `ib_nominees`
--
ALTER TABLE `ib_nominees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ib_notifications`
--
ALTER TABLE `ib_notifications`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `ib_staff`
--
ALTER TABLE `ib_staff`
  ADD PRIMARY KEY (`staff_id`);

--
-- Indexes for table `ib_systemsettings`
--
ALTER TABLE `ib_systemsettings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ib_transactions`
--
ALTER TABLE `ib_transactions`
  ADD PRIMARY KEY (`tr_id`),
  ADD KEY `fk_account` (`account_id`),
  ADD KEY `fk_client` (`client_id`);

--
-- Indexes for table `loan_applications`
--
ALTER TABLE `loan_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviewed_by` (`reviewed_by`),
  ADD KEY `loan_type_id` (`loan_type_id`),
  ADD KEY `idx_client_id` (`client_id`),
  ADD KEY `fk_admin_review` (`admin_review_id`);

--
-- Indexes for table `loan_types`
--
ALTER TABLE `loan_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `client_feedback`
--
ALTER TABLE `client_feedback`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ib_acc_types`
--
ALTER TABLE `ib_acc_types`
  MODIFY `acctype_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `ib_admin`
--
ALTER TABLE `ib_admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ib_bankaccounts`
--
ALTER TABLE `ib_bankaccounts`
  MODIFY `account_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `ib_clients`
--
ALTER TABLE `ib_clients`
  MODIFY `client_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `ib_nominees`
--
ALTER TABLE `ib_nominees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ib_notifications`
--
ALTER TABLE `ib_notifications`
  MODIFY `notification_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `ib_staff`
--
ALTER TABLE `ib_staff`
  MODIFY `staff_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ib_systemsettings`
--
ALTER TABLE `ib_systemsettings`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ib_transactions`
--
ALTER TABLE `ib_transactions`
  MODIFY `tr_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;

--
-- AUTO_INCREMENT for table `loan_applications`
--
ALTER TABLE `loan_applications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `loan_types`
--
ALTER TABLE `loan_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `client_feedback`
--
ALTER TABLE `client_feedback`
  ADD CONSTRAINT `client_feedback_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `ib_clients` (`client_id`) ON DELETE CASCADE;

--
-- Constraints for table `ib_bankaccounts`
--
ALTER TABLE `ib_bankaccounts`
  ADD CONSTRAINT `fk_ib_bankaccounts_clients` FOREIGN KEY (`client_id`) REFERENCES `ib_clients` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ib_transactions`
--
ALTER TABLE `ib_transactions`
  ADD CONSTRAINT `fk_account` FOREIGN KEY (`account_id`) REFERENCES `ib_bankaccounts` (`account_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_client` FOREIGN KEY (`client_id`) REFERENCES `ib_clients` (`client_id`) ON DELETE CASCADE;

--
-- Constraints for table `loan_applications`
--
ALTER TABLE `loan_applications`
  ADD CONSTRAINT `fk_admin_review` FOREIGN KEY (`admin_review_id`) REFERENCES `ib_admin` (`admin_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_client_id` FOREIGN KEY (`client_id`) REFERENCES `ib_clients` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `loan_applications_ibfk_1` FOREIGN KEY (`reviewed_by`) REFERENCES `ib_staff` (`staff_id`),
  ADD CONSTRAINT `loan_applications_ibfk_2` FOREIGN KEY (`loan_type_id`) REFERENCES `loan_types` (`id`);

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `ib_admin` (`admin_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
