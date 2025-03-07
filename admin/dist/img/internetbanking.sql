-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 06, 2025 at 11:23 AM
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
(7, 'Current account', '<p><strong>Current account</strong> is mainly for business per<strong>s</strong>ons, firms, companies, public enterprises etc and are never used for the purpose of investment or savings.These deposits are the most liquid deposits and there are no limits for number of transactions or the amount of transactions in a day. While, there is no interest paid on amount held in the account, banks charges certain &nbsp;service charges, on such accounts. The current accounts do not have any fixed maturity as thegadegagagase are on continuous basis accounts.</p>', '20', 'ACC-CAT-4O8QW', 1, 5000.00),
(8, 'Salary Account', '<p>A <strong>salary account</strong> is a bank account where an employer directly deposits an employee&rsquo;s salary. It usually has <strong>zero balance requirements</strong>, <strong>free debit card and chequebook</strong>, <strong>higher transaction limits</strong>, <strong>overdraft facility</strong>, and <strong>easy loan approvals</strong>. It also provides <strong>internet and mobile banking</strong> for seamless transactions.</p>', '6.5', 'ACC-CAT-27DQV', 1, 0.00),
(9, 'Minor Account', '<p>A Minor Account is a special type of bank account designed for individuals below the legal adult age, typically managed by a parent or guardian. These accounts help minors develop financial literacy and savings habits from an early age. While the account is in the minor&rsquo;s name, the guardian has control over transactions, withdrawals, and fund management until the minor reaches adulthood. Many banks offer minor accounts with benefits like zero minimum balance, higher interest rates, and restrictions on certain transactions to ensure financial safety. Upon reaching the legal age, the minor can convert the account into a regular savings account.</p>', '3.5', 'ACC-CAT-UBY52', 1, 500.00),
(11, 'Joint Account', 'A Joint Account is a shared bank account owned by two or more individuals, allowing them to manage and access funds collectively. It is commonly used by spouses, business partners, family members, or anyone who wishes to share financial responsibilities. This type of account offers convenience for handling shared expenses such as household bills, savings, or business transactions. Depending on the account terms, withdrawals and transactions may require authorization from one or all account holders. A joint account promotes financial transparency and trust while providing an efficient way to manage funds together.', '10', 'ACC-CAT-8IQVP', 1, 0.00);

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
(2, 'system administrater ', 'dholajenil2024.katargam@gmail.com', 'iBank-ADM-0516', '$2y$10$RE2rcBl/BwzXLr7C7PQN2uDyoOSnI8QTcpGlpU215q9.YEi8SrYJq', 'admin-icn.png', 1, NULL, NULL);

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
(14, 'Hari pandya', '357146928', 'Current account ', '20', 'Active', 31063.00, 5, '2025-03-02 15:26:59.154925', 1),
(15, 'Arin Gabani', '287359614', 'Recurring deposit ', '15', 'Active', 820836.00, 8, '2025-03-02 15:11:23.771478', 1),
(16, 'Harshit Rana', '705239816', ' Retirement ', '10', 'Active', 61712.00, 6, '2025-03-06 10:07:10.847487', 1),
(23, 'Jenil Dhola', '573608192', 'Savings', '15', 'Active', 21503.00, 11, '2025-03-06 10:07:31.418188', 1),
(35, 'Sahil Gohil', '964031285', 'Joint Account ', '10', 'Active', 0.00, 15, '2025-03-06 10:07:41.268040', 1);

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
  `aadhar_number` varchar(12) NOT NULL,
  `pan_number` varchar(10) NOT NULL,
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

INSERT INTO `ib_clients` (`client_id`, `name`, `phone`, `address`, `email`, `aadhar_number`, `pan_number`, `password`, `profile_pic`, `client_number`, `is_active`, `otp`, `otp_expiry`) VALUES
(5, 'Hari Pandya', '7412560000', '114 Allace Avenue', 'harryden@mail.com', '996544392776', 'FXYYY8710N', '55c3b5386c486feb662a0785f340938f518d547f', '', 'iBank-CLIENT-7014', 1, NULL, NULL),
(6, 'Harshit Rana', '7412545454', '23 Hinkle Deegan Lake Road', 'reyes@mail.com', '489954692767', 'YFGOZ3386S', '55c3b5386c486feb662a0785f340938f518d547f', 'user-profile-min.png', 'iBank-CLIENT-1698', 1, NULL, NULL),
(8, 'Arin gabani', '7850000014', '92 Maple Street', 'arin123a@mail.com', '460140316244', 'OPKFW7221Z', '55c3b5386c486feb662a0785f340938f518d547f', 'beet.jpg', 'iBank-CLIENT-0423', 1, NULL, NULL),
(11, 'Jenil Dhola', '9979735065', 'A-2/203,DEVI COMPLEX,DABHOLI CHAR RASTA', 'shreeji.gamer.bot@gmail.com', '978546321574', 'PAXE23467P', '$2y$10$6yxMhsckua3wiAT2cx3rzuTM6Uhr54Safv0nkS/xDymtFPdFDXMkq', '', 'iBank-CLIENT-2438', 1, '333165', '2025-02-28 13:39:16'),
(15, 'Sahil Gohil', '6352419685', 'A-103, Riivanta Riverview, Variyav, Surat', 'cleints2023@gmail.com', '741852963159', 'OPKFW7221Z', '$2y$10$tnXEHcPfGDA/hDMbeQd/Z.CFd5AlFASauXVLC7BN7ImCIbogDP/BS', '', 'iBank-CLIENT-0458', 1, '625736', '2025-03-02 22:08:23');

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
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `aadhar_number` varchar(12) NOT NULL,
  `pan_number` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ib_nominees`
--

INSERT INTO `ib_nominees` (`id`, `client_id`, `nominee_name`, `relation`, `nominee_email`, `nominee_phone`, `nominee_address`, `created_at`, `is_active`, `aadhar_number`, `pan_number`) VALUES
(1, 11, 'Bhavnaben Dhola', 'Mother', 'jenildhola1811@gmail.com', '99025063124', 'A-2/203,DEVI COMPLEX,DABHOLI CHAR RASTA', '2025-02-20 06:29:08', 1, '785496325896', 'IHXOD1193P'),
(6, 11, 'dineshbhai dhola', 'Father', 'jenildhola1811@gmail.com', '8799050118', 'A-2/203,DEVI COMPLEX,DABHOLI CHAR RASTA', '2025-02-28 17:00:36', 1, '978546321574', 'OPKFW7221Z');

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
(97, 'A deposit of Rs. 52000 has been made into Bank Account 357146928', '2025-02-25 07:56:15.624178', 1),
(98, 'Jenil Dhola Has Withdrawn Rs. 25000 From Bank Account 573608192', '2025-03-01 11:55:50.296623', 1),
(99, 'Harry Den Has Withdrawn Rs. 100 From Bank Account 357146928', '2025-03-01 12:08:44.103562', 1),
(100, 'Jenil Dhola Has Withdrawn Rs. 9000 From Bank Account 529714806', '2025-03-01 12:09:19.146649', 1),
(101, 'A deposit of Rs. 1000000 has been made into Bank Account 287359614', '2025-03-01 12:09:37.984075', 1),
(102, 'Arin gabani Has Withdrawn Rs. 50000 From Bank Account 287359614', '2025-03-01 12:09:59.891171', 1),
(103, 'Arin gabani Has Withdrawn Rs. 50000 From Bank Account 287359614', '2025-03-01 12:10:07.797564', 1),
(104, 'Jenil Dhola has deposited Rs. 6000 into bank account 23', '2025-03-01 12:17:09.335715', 1),
(105, 'Jenil Dhola has deposited Rs. 5000 into bank account 23', '2025-03-02 11:36:14.289504', 1),
(106, 'Harry Den Has Withdrawn Rs. 50000 From Bank Account 357146928', '2025-03-02 11:41:27.165799', 1),
(107, 'Jenil Dhola Has Withdrawn Rs. 100 From Bank Account 573608192', '2025-05-08 09:26:34.840175', 1);

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
(3, 'Jay Shah', 'iBank-STAFF-6785', '7049757429', 'dharmika192@gmail.com', '$2y$10$Kh5mefahn9r3uUbthwxXi.XuCSOV9Y5Nsk8SrhT82uFR15pufqYz6', 'Male', 'jay.jpg', 1, '719130', '2025-02-20 10:48:33'),
(4, 'Rahul Dravid', 'iBank-STAFF-6724', '9265460571', 'wall@gmail.com', 'd95d3bbedb4dcba5a8e891968853002354b028e9', 'Male', 'rahul.jpg', 1, NULL, NULL),
(6, 'jeff besos', 'iBank-STAFF-0974', '7049757429', 'jenildhola1811@gmail.com', '2b76cc7ede474c4bdd63b8b26929625b6e6441d7', 'Male', 'aadu.jpg', 1, NULL, NULL);

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
(1, 'DigitalBankx', 'Digital banking revolution', 'bank.png', 1);

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
(95, 'dU1ykHVsqDFP7g9ShcZR', 23, 'Deposit', 'Success', 11, '10000', '', '2025-02-23 12:09:58.241214', 1),
(96, 'vgmrt9xW5d1n2aMQkV6j', 23, 'Withdrawal', 'Success ', 11, '1500', '', '2025-02-23 12:10:13.832713', 1),
(97, 'DJyFP59gKakXtfqrpViW', 23, 'Transfer', 'Success', 11, '10000', '705239816', '2025-02-23 12:27:23.000000', 1),
(98, 'EybJDPiQRet4VY9WBg0v', 14, 'Deposit', 'Success', 5, '100', '', '2025-02-23 12:29:48.061610', 1),
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
(152, 'hQR23ziWvyEAeUZmtMrN', 15, 'Transfer', 'Success', 8, '18000', '730459816', '2025-02-25 07:55:18.000000', 1),
(153, 'jJKdkWTtnmchzbHQXg8Y', 14, 'Deposit', 'Success', 5, '18000', NULL, '2025-02-25 07:56:06.176874', 1),
(154, 'OtXrJaKoAG6uvDR37BeM', 14, 'Deposit', 'Success', 5, '52000', NULL, '2025-02-25 07:56:15.623547', 1),
(155, 'rHSMnDi7cXoEZhFIJwut', 16, 'Transfer', 'Success', 6, '100', '287359614', '2025-02-25 08:52:27.000000', 1),
(156, 'ZSnEfPBAlHI5hLo7vCX4', 23, 'Withdrawal', 'Success ', 11, '25000', NULL, '2025-03-01 11:55:50.293460', 1),
(157, '8uxO7NrUqoGQYehiZmfK', 14, 'Withdrawal', 'Success ', 5, '100', NULL, '2025-03-01 12:08:44.101392', 1),
(159, 'gtKpC6rMczAyUaYu5Slj', 15, 'Deposit', 'Success', 8, '1000000', NULL, '2025-03-01 12:09:37.983716', 1),
(160, 'Xmaz801KbMOI3nu64xVi', 15, 'Withdrawal', 'Success ', 8, '50000', NULL, '2025-03-01 12:09:59.889484', 1),
(161, 'QTgSnD7s2XZWI4h5Hxbu', 15, 'Withdrawal', 'Success ', 8, '50000', NULL, '2025-03-01 12:10:07.795601', 1),
(162, 'WLmHJwBo14XyjF72r3u8', 23, 'Transfer', 'Success', 11, '100', '357146928', '2025-03-01 12:11:07.000000', 1),
(165, '9FnwvaJcdq0B8yWko1pV', 23, 'Deposit', 'Success', 11, '6000', NULL, '2025-03-01 12:17:09.335452', 1),
(166, 'FbQurL7NqXKdWEozBCji', 23, 'Transfer', 'Success', 11, '6000', '529714806', '2025-03-01 12:18:48.000000', 1),
(167, 'mfjgDwEaTxvrCR8cI6uq', 23, 'Deposit', 'Success', 11, '5000', NULL, '2025-03-02 11:36:14.286271', 1),
(168, '8pWHm4oSnVQx5fzrPh2Z', 15, 'Transfer', 'Success', 8, '10000', '573608192', '2025-03-02 11:40:54.000000', 1),
(169, 'gN6TabdDErCO2yXKRQhe', 14, 'Withdrawal', 'Success ', 5, '50000', NULL, '2025-03-02 11:41:27.164055', 1),
(184, 'q3VHEGI1U0NOle47L96D', 23, 'Transfer', 'Success', 11, '487', '705239816', '2025-03-02 13:01:59.000000', 1),
(202, 'epkXFV51faQcD06mEPCT', 23, 'Withdrawal', 'Success ', 11, '100', NULL, '2025-05-08 09:26:34.837289', 1);

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
  `income_salary` decimal(10,2) NOT NULL,
  `loan_duration_years` int(11) NOT NULL DEFAULT 0,
  `loan_duration_months` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_applications`
--

INSERT INTO `loan_applications` (`id`, `applicant_name`, `loan_amount`, `application_date`, `status`, `reviewed_by`, `admin_review_id`, `review_date`, `staff_remark`, `admin_remark`, `client_id`, `loan_type_id`, `is_approved_by_staff`, `income_salary`, `loan_duration_years`, `loan_duration_months`) VALUES
(27, 'utsav cheta', 800000.00, '2025-03-05 11:38:24', 'approved', 3, 2, '2025-03-05 11:52:42', 'good', 'all good', 11, 2, 0, 80000.00, 10, 6),
(28, 'darshan lakhani', 120000.00, '2025-03-05 13:49:35', 'approved', 3, 2, '2025-03-05 13:50:23', 'good', 'ok', 15, 11, 0, 25000.00, 1, 6);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ib_acc_types`
--
ALTER TABLE `ib_acc_types`
  MODIFY `acctype_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `ib_admin`
--
ALTER TABLE `ib_admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ib_bankaccounts`
--
ALTER TABLE `ib_bankaccounts`
  MODIFY `account_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `ib_clients`
--
ALTER TABLE `ib_clients`
  MODIFY `client_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `ib_nominees`
--
ALTER TABLE `ib_nominees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `ib_notifications`
--
ALTER TABLE `ib_notifications`
  MODIFY `notification_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `ib_staff`
--
ALTER TABLE `ib_staff`
  MODIFY `staff_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ib_systemsettings`
--
ALTER TABLE `ib_systemsettings`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ib_transactions`
--
ALTER TABLE `ib_transactions`
  MODIFY `tr_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;

--
-- AUTO_INCREMENT for table `loan_applications`
--
ALTER TABLE `loan_applications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

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
