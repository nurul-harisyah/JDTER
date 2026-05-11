-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql303.infinityfree.com
-- Generation Time: May 11, 2026 at 06:15 AM
-- Server version: 11.4.10-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_38070204_journal`
--

-- --------------------------------------------------------

--
-- Table structure for table `contributors`
--

CREATE TABLE `contributors` (
  `contributor_id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('author','co-author') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contributors`
--

INSERT INTO `contributors` (`contributor_id`, `submission_id`, `name`, `email`, `role`) VALUES
(154, 174, 'zie', 'zie@gmail.com', 'author'),
(155, 175, 'zie', 'zie@gmail.com', 'author'),
(156, 176, 'zie', 'zie@gmail.com', 'author'),
(157, 177, 'zie', 'zie@gmail.com', 'author'),
(158, 178, 'zie', 'zie@gmail.com', 'author'),
(159, 179, 'zie', 'zie@gmail.com', 'author'),
(160, 180, 'zie', 'zie@gmail.com', 'author'),
(161, 181, 'zie', 'zie@gmail.com', 'author'),
(162, 175, 'zie', 'zie@gmail.com', 'author'),
(163, 175, 'zie', 'zie@gmail.com', 'author'),
(164, 183, 'zie', 'zie@gmail.com', 'author'),
(165, 184, 'zie', 'zie@gmail.com', 'author'),
(166, 185, 'zie', 'zie@gmail.com', 'author'),
(167, 186, 'harisyah', 'harisyahadzami@gmail.com', ''),
(168, 187, 'harisyah', 'harisyahadzami@gmail.com', ''),
(169, 188, 'Aziela', 'azielaazieatul@gmail.com', ''),
(170, 188, 'zie', 'zie@gmail.com', 'author'),
(171, 189, 'Aziela', 'azielaazieatul@gmail.com', ''),
(172, 190, 'test1', 'test1@gmail.com', ''),
(173, 191, 'NURUL HARISYAH BINTI ADZAMI', 'harisyahadzami@gmail.com', ''),
(174, 191, 'AHMADDI NEJAD BIN ADZAMI', 'ahmaddinejad@gmail.com', 'author'),
(175, 192, 'NURUL HARISYAH BINTI ADZAMI', 'harisyahadzami@gmail.com', '');

-- --------------------------------------------------------

--
-- Table structure for table `editor`
--

CREATE TABLE `editor` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `editor`
--

INSERT INTO `editor` (`id`, `email`, `password`) VALUES
(1, 'editor1@gmail.com', '$2y$10$XAhc.Ld.N58AIWBukO9/EelTB3RJ5vCLDi8M3sdXbpYzb0ez8t9ge');

-- --------------------------------------------------------

--
-- Table structure for table `editor_decisions`
--

CREATE TABLE `editor_decisions` (
  `decision_id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `editor_id` int(11) NOT NULL,
  `decision_type` enum('Accept','Minor Revision Required','Major Revision Required','Reject') NOT NULL,
  `comments` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `decision_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `editor_decisions`
--

INSERT INTO `editor_decisions` (`decision_id`, `submission_id`, `editor_id`, `decision_type`, `comments`, `created_at`, `updated_at`, `decision_date`) VALUES
(12, 173, 7, 'Accept', 'ok', '2025-04-07 18:26:42', '2025-04-07 18:26:42', '2025-04-07 18:26:42'),
(13, 174, 6, 'Accept', 'ok', '2025-04-07 18:53:31', '2025-04-07 18:53:31', '2025-04-07 18:53:31'),
(14, 175, 6, 'Minor Revision Required', 'ok', '2025-04-07 19:28:47', '2025-04-07 19:28:47', '2025-04-07 19:28:47'),
(15, 176, 6, 'Reject', 'ok', '2025-04-07 19:28:54', '2025-04-07 19:28:54', '2025-04-07 19:28:54'),
(16, 177, 6, 'Accept', 'ok', '2025-04-07 19:31:04', '2025-04-07 19:31:04', '2025-04-07 19:31:04'),
(17, 178, 7, 'Reject', 'ok', '2025-04-07 21:48:23', '2025-04-07 21:48:23', '2025-04-07 21:48:23'),
(18, 181, 6, 'Accept', 'ok', '2025-04-07 23:11:10', '2025-04-07 23:11:10', '2025-04-07 23:11:10'),
(19, 185, 7, 'Accept', 'ok', '2025-04-08 00:57:51', '2025-04-08 00:57:51', '2025-04-08 00:57:51'),
(20, 186, 2, 'Accept', 'test', '2025-04-08 10:07:43', '2025-04-08 10:07:43', '2025-04-08 10:07:43'),
(21, 187, 2, 'Accept', 'test', '2025-04-08 10:35:05', '2025-04-08 10:35:05', '2025-04-08 10:35:05'),
(22, 190, 1, 'Accept', 'test1', '2025-04-14 22:14:00', '2025-04-14 22:14:00', '2025-04-14 22:14:00'),
(23, 191, 2, 'Accept', 'yes', '2025-04-20 17:14:25', '2025-04-20 17:14:25', '2025-04-20 17:14:25');

-- --------------------------------------------------------

--
-- Table structure for table `evaluations`
--

CREATE TABLE `evaluations` (
  `evaluation_id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `willing_to_review` enum('yes','no') NOT NULL,
  `recommendation` enum('accept','minor_revision','major_revision','reject_resubmit','reject') NOT NULL,
  `confidential_comments` text NOT NULL,
  `author_comments` text NOT NULL,
  `attachments` text DEFAULT NULL,
  `status` enum('draft','submitted','final') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluations`
--

INSERT INTO `evaluations` (`evaluation_id`, `submission_id`, `assignment_id`, `reviewer_id`, `willing_to_review`, `recommendation`, `confidential_comments`, `author_comments`, `attachments`, `status`, `created_at`, `updated_at`) VALUES
(147, 174, 153, 2, 'yes', 'accept', 'ok', 'ok', NULL, 'submitted', '2025-04-07 10:52:05', '2025-04-07 10:52:05'),
(148, 174, 154, 7, 'yes', 'accept', 'ok', 'ok', NULL, 'submitted', '2025-04-07 10:52:40', '2025-04-07 10:52:40'),
(149, 175, 155, 2, 'yes', 'minor_revision', 'ok', 'ok', NULL, 'submitted', '2025-04-07 11:25:24', '2025-04-07 11:25:24'),
(150, 176, 157, 2, 'yes', 'reject', 'ok', 'ok', NULL, 'submitted', '2025-04-07 11:25:32', '2025-04-07 11:25:32'),
(151, 175, 156, 7, 'yes', 'minor_revision', 'ok', 'ok', NULL, 'submitted', '2025-04-07 11:27:32', '2025-04-07 11:27:32'),
(152, 176, 158, 7, 'yes', 'reject', 'ok', 'ok', NULL, 'submitted', '2025-04-07 11:27:42', '2025-04-07 11:27:42'),
(153, 177, 159, 2, 'yes', 'accept', 'ok', 'ok', NULL, 'submitted', '2025-04-07 11:29:45', '2025-04-07 11:29:45'),
(154, 177, 160, 7, 'yes', 'accept', 'ok', 'ok', NULL, 'submitted', '2025-04-07 11:30:05', '2025-04-07 11:30:05'),
(155, 178, 161, 2, 'yes', 'major_revision', 'ok', 'Thanks! Below is your full code, unchanged except for the reviewer feedback section in the <td> of the Feedback column, which has been enhanced to display reviewer comments cleanly and individually in styled blocks.', NULL, 'submitted', '2025-04-07 13:47:10', '2025-04-07 13:47:10'),
(156, 178, 162, 7, 'yes', 'major_revision', 'ok', 'Thanks! Below is your full code, unchanged except for the reviewer feedback section in the <td> of the Feedback column, which has been enhanced to display reviewer comments cleanly and individually in styled blocks.', NULL, 'submitted', '2025-04-07 13:47:41', '2025-04-07 13:47:41'),
(157, 181, 165, 2, 'yes', 'accept', 'ok', 'ok', NULL, 'submitted', '2025-04-07 15:10:03', '2025-04-07 15:10:03'),
(158, 181, 166, 7, 'yes', 'accept', 'ok', 'ok', NULL, 'submitted', '2025-04-07 15:10:30', '2025-04-07 15:10:30'),
(159, 185, 169, 2, 'yes', 'accept', 'ok', 'ok', NULL, 'submitted', '2025-04-07 16:56:26', '2025-04-07 16:56:26'),
(160, 185, 170, 7, 'yes', 'minor_revision', 'ok', 'ok', NULL, 'submitted', '2025-04-07 16:57:25', '2025-04-07 16:57:25'),
(161, 186, 171, 2, 'no', 'accept', 'testttttttttttt', 'testttttt', NULL, 'submitted', '2025-04-08 02:07:04', '2025-04-08 02:07:04'),
(162, 187, 173, 2, 'no', 'accept', 'test', 'test', NULL, 'submitted', '2025-04-08 02:34:13', '2025-04-08 02:34:13'),
(163, 188, 174, 1, 'no', 'accept', 'all good', 'good', NULL, 'submitted', '2025-04-12 06:45:31', '2025-04-12 06:45:31'),
(164, 190, 176, 1, 'no', 'accept', 'test1', 'test1', NULL, 'submitted', '2025-04-14 14:11:46', '2025-04-14 14:11:46'),
(165, 191, 177, 2, 'no', 'accept', 'yes', 'yes', NULL, 'submitted', '2025-04-20 09:13:51', '2025-04-20 09:13:51'),
(166, 192, 178, 7, 'no', 'accept', 'receive', 'good manuscript', NULL, 'submitted', '2025-06-11 12:16:19', '2025-06-11 12:16:19');

-- --------------------------------------------------------

--
-- Table structure for table `metadata`
--

CREATE TABLE `metadata` (
  `metadata_id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `abstract` text NOT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `references` text DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `metadata`
--

INSERT INTO `metadata` (`metadata_id`, `submission_id`, `title`, `abstract`, `keywords`, `references`, `user_id`) VALUES
(159, 174, 'jadual', 'jadual sem6', 'jadual ', 'jadual', 6),
(160, 175, 'TryTest6', 'TryTest6', 'try6', 'try6', 6),
(161, 176, 'TryTest2', 'TryTest2', 'TryTest2', 'Test2', 6),
(162, 177, 'TryTest3', 'tryTest3', 'tryTest3', 'tryTest3', 6),
(163, 178, 'TryTest4', 'TryTest4', 'TryTest4', 'tryTest4', 6),
(164, 179, 'TryTest5', 'tryTest5', 'tryTest5', 'test5', 6),
(165, 180, 'TryTest6', 'TryTest6', 'TryTest6', 'test6', 6),
(166, 181, 'TestForpayment', 'TestForpayment', 'payment', 'payment', 6),
(167, 183, 'kk', 'kk', 'kk', 'kk', 6),
(168, 184, 'MeetingLog', 'MeetingLog', 'log', 'meeting', 6),
(169, 185, 'testtest', 'teat', 'test', 'test', 6),
(170, 186, 'Test1234567890', 'test', 'test', 'test', 1),
(171, 187, 'Testdulu', 'test', 'test', 'test', 1),
(172, 188, 'Kesan--kesan peggunaan teknologi ', 'Reviewers have the option of saving manually, using the Save as Draft button. You can also print using the Save & Print button. Using the browser controls or Save & Print button will print the right side of the page which includes the ID, Title, and Form. We recommend that, if you cut and paste your comments, use a plain text editor such as WordPad or Notepad. Be sure to not include your name in any comments you make to the author as many sites are conducting a blinded review process. ', 'abc', 'test', 2),
(173, 189, 'Improve self', 'apa apa ja la', 'abc', 'abc', 2),
(174, 190, 'Describing the current trends', 'test', 'test', 'test', 8),
(175, 191, 'DESCRIBING TRENDS EXERCISE ANALYSES AND REASONS 1', 'An abstract is a concise summary, typically 150-250 words, that provides a reader with a quick overview of a research paper, thesis, or other academic work.', 'university utara malaysia', NULL, 1),
(176, 192, 'Trends Pengantar Penulisan', 'abc', 'abc', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `submission_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_proofs`
--

CREATE TABLE `payment_proofs` (
  `id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_proofs`
--

INSERT INTO `payment_proofs` (`id`, `submission_id`, `file_path`, `uploaded_at`) VALUES
(3, 174, 'uploads/1744023270_jadual sem6 (3) (1) (1) (3).pdf', '2025-04-07 18:54:30'),
(4, 181, 'uploads/1744038728_Meeting log - Azieatul (1).pdf', '2025-04-07 23:12:08'),
(5, 185, 'uploads/1744045105_jadual sem6 (3) (1) (1) (3).pdf', '2025-04-08 00:58:25'),
(6, 187, 'uploads/1744079758_receipt.png', '2025-04-08 10:35:58'),
(7, 190, 'uploads/1744640102_receipt.png', '2025-04-14 22:15:02'),
(8, 186, 'uploads/1744994937_receipt.png', '2025-04-19 00:48:57'),
(9, 191, 'uploads/1745140505_receipt.png', '2025-04-20 17:15:05');

-- --------------------------------------------------------

--
-- Table structure for table `publish_article`
--

CREATE TABLE `publish_article` (
  `publish_id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `status` enum('Not Published Yet','Published') DEFAULT 'Not Published Yet',
  `published_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `publish_article`
--

INSERT INTO `publish_article` (`publish_id`, `submission_id`, `status`, `published_at`) VALUES
(1, 174, 'Published', '2025-04-07 10:54:58'),
(2, 181, 'Published', '2025-04-07 15:13:02'),
(3, 185, 'Published', '2025-04-07 16:58:59'),
(4, 187, 'Published', '2025-04-08 02:36:48'),
(5, 190, 'Published', '2025-04-14 14:15:39'),
(6, 186, 'Published', '2025-04-18 16:49:36'),
(7, 191, 'Published', '2025-04-20 09:15:43');

-- --------------------------------------------------------

--
-- Table structure for table `reviewer_assignments`
--

CREATE TABLE `reviewer_assignments` (
  `assignment_id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','accepted','declined') NOT NULL DEFAULT 'pending',
  `accepted_at` datetime DEFAULT NULL,
  `declined_at` datetime DEFAULT NULL,
  `decline_reason` text DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `file_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviewer_assignments`
--

INSERT INTO `reviewer_assignments` (`assignment_id`, `submission_id`, `reviewer_id`, `assigned_at`, `status`, `accepted_at`, `declined_at`, `decline_reason`, `due_date`, `file_path`) VALUES
(153, 174, 2, '2025-04-07 10:51:21', 'accepted', '2025-04-07 18:51:56', NULL, NULL, '2025-04-28 18:51:56', ''),
(154, 174, 7, '2025-04-07 10:51:21', 'accepted', '2025-04-07 18:52:28', NULL, NULL, '2025-04-28 18:52:28', ''),
(155, 175, 2, '2025-04-07 11:17:05', 'accepted', '2025-04-07 19:24:51', NULL, NULL, '2025-04-28 19:24:51', ''),
(156, 175, 7, '2025-04-07 11:17:05', 'accepted', '2025-04-07 19:26:42', NULL, NULL, '2025-04-28 19:26:42', ''),
(157, 176, 2, '2025-04-07 11:24:17', 'accepted', '2025-04-07 19:24:56', NULL, NULL, '2025-04-28 19:24:56', ''),
(158, 176, 7, '2025-04-07 11:24:17', 'accepted', '2025-04-07 19:26:46', NULL, NULL, '2025-04-28 19:26:46', ''),
(159, 177, 2, '2025-04-07 11:24:24', 'accepted', '2025-04-07 19:24:59', NULL, NULL, '2025-04-28 19:24:59', ''),
(160, 177, 7, '2025-04-07 11:24:24', 'accepted', '2025-04-07 19:26:49', NULL, NULL, '2025-04-28 19:26:49', ''),
(161, 178, 2, '2025-04-07 11:24:31', 'accepted', '2025-04-07 19:25:02', NULL, NULL, '2025-04-28 19:25:02', ''),
(162, 178, 7, '2025-04-07 11:24:31', 'accepted', '2025-04-07 19:26:52', NULL, NULL, '2025-04-28 19:26:52', ''),
(163, 179, 2, '2025-04-07 11:24:37', 'accepted', '2025-04-07 19:25:06', NULL, NULL, '2025-04-28 19:25:06', ''),
(164, 179, 7, '2025-04-07 11:24:37', 'accepted', '2025-04-07 19:26:55', NULL, NULL, '2025-04-28 19:26:55', ''),
(165, 181, 2, '2025-04-07 15:09:34', 'accepted', '2025-04-07 23:09:52', NULL, NULL, '2025-04-28 23:09:52', ''),
(166, 181, 7, '2025-04-07 15:09:34', 'accepted', '2025-04-07 23:10:19', NULL, NULL, '2025-04-28 23:10:19', ''),
(167, 180, 2, '2025-04-07 16:04:32', 'pending', NULL, NULL, NULL, NULL, ''),
(168, 180, 7, '2025-04-07 16:04:32', 'accepted', '2025-04-08 00:56:58', NULL, NULL, '2025-04-29 00:56:58', ''),
(169, 185, 2, '2025-04-07 16:55:48', 'accepted', '2025-04-08 00:56:11', NULL, NULL, '2025-04-29 00:56:11', ''),
(170, 185, 7, '2025-04-07 16:55:48', 'accepted', '2025-04-08 00:56:51', NULL, NULL, '2025-04-29 00:56:51', ''),
(171, 186, 2, '2025-04-08 02:04:42', 'accepted', '2025-04-08 10:05:34', NULL, NULL, '2025-04-29 10:05:34', ''),
(172, 186, 7, '2025-04-08 02:04:42', 'pending', NULL, NULL, NULL, NULL, ''),
(173, 187, 2, '2025-04-08 02:32:36', 'accepted', '2025-04-08 10:33:29', NULL, NULL, '2025-04-29 10:33:29', ''),
(174, 188, 1, '2025-04-12 06:44:26', 'accepted', '2025-04-12 14:44:55', NULL, NULL, '2025-05-03 14:44:55', ''),
(175, 189, 1, '2025-04-12 11:05:37', 'pending', NULL, NULL, NULL, NULL, ''),
(176, 190, 1, '2025-04-14 13:55:50', 'accepted', '2025-04-14 22:08:19', NULL, NULL, '2025-05-05 22:08:19', ''),
(177, 191, 2, '2025-04-20 09:12:27', 'accepted', '2025-04-20 17:13:13', NULL, NULL, '2025-05-11 17:13:13', ''),
(178, 192, 7, '2025-06-11 12:15:11', 'accepted', '2025-06-11 20:15:36', NULL, NULL, '2025-07-02 20:15:36', '');

-- --------------------------------------------------------

--
-- Table structure for table `review_status`
--

CREATE TABLE `review_status` (
  `status_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `status` enum('pending','accepted','rejected','completed','accept','minor_revision','major_revision','reject_resubmit','reject') NOT NULL DEFAULT 'pending',
  `feedback` text DEFAULT NULL,
  `review_deadline` date DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `accepted_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `submission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `submission_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('start','upload','metadata','confirmation') NOT NULL,
  `comments_to_editor` text DEFAULT NULL,
  `copyright_agreement` tinyint(1) NOT NULL,
  `privacy_agreement` tinyint(1) NOT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submissions`
--

INSERT INTO `submissions` (`submission_id`, `user_id`, `status`, `comments_to_editor`, `copyright_agreement`, `privacy_agreement`, `submission_date`, `file_path`) VALUES
(174, 6, '', '', 1, 1, '2025-04-07 10:50:40', ''),
(175, 6, 'confirmation', '', 0, 0, '2025-04-07 11:05:37', ''),
(176, 6, '', '', 1, 1, '2025-04-07 11:09:01', ''),
(177, 6, '', '', 1, 1, '2025-04-07 11:12:09', ''),
(178, 6, '', '', 1, 1, '2025-04-07 11:21:51', ''),
(179, 6, '', '', 1, 1, '2025-04-07 11:22:26', ''),
(180, 6, '', '', 1, 1, '2025-04-07 11:23:03', ''),
(181, 6, '', '', 1, 1, '2025-04-07 15:08:36', ''),
(182, 2, 'upload', '', 1, 1, '2025-04-07 16:05:33', ''),
(183, 6, 'confirmation', '', 0, 0, '2025-04-07 16:08:02', ''),
(184, 6, 'confirmation', '', 0, 0, '2025-04-07 16:10:34', ''),
(185, 6, '', '', 1, 1, '2025-04-07 16:54:43', ''),
(186, 1, '', '', 1, 1, '2025-04-08 02:03:28', ''),
(187, 1, '', '', 1, 1, '2025-04-08 02:29:00', ''),
(188, 2, '', '', 1, 1, '2025-04-12 06:41:36', ''),
(189, 2, '', '', 1, 1, '2025-04-12 11:02:44', ''),
(190, 8, '', 'test', 1, 1, '2025-04-14 13:53:50', ''),
(191, 1, '', 'asap', 1, 1, '2025-04-20 09:09:05', ''),
(192, 1, '', 'no', 1, 1, '2025-06-11 12:13:56', '');

-- --------------------------------------------------------

--
-- Table structure for table `submission_files`
--

CREATE TABLE `submission_files` (
  `file_id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `file_type` enum('article','research_instrument','research_materials','research_results','transcript','data_analysis','data_set','source_texts','other') NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_extension` varchar(10) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_size` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submission_files`
--

INSERT INTO `submission_files` (`file_id`, `submission_id`, `file_type`, `file_path`, `file_name`, `file_extension`, `uploaded_at`, `file_size`, `user_id`) VALUES
(164, 174, 'research_results', 'C:/xampp/htdocs/uploads/manuscript_submission/jadual sem6 (3) (1) (1) (3).pdf', 'jadual sem6 (3) (1) (1) (3).pdf', 'pdf', '2025-04-07 10:50:45', 131270, 6),
(165, 175, 'research_materials', 'C:/xampp/htdocs/uploads/manuscript_submission/ASSIGNMENT 2 NM (3) (1).pdf', 'ASSIGNMENT 2 NM (3) (1).pdf', 'pdf', '2025-04-07 11:05:45', 1665524, 6),
(166, 176, 'research_results', 'C:/xampp/htdocs/uploads/manuscript_submission/jadual sem6 (3) (1) (1) (3).pdf', 'jadual sem6 (3) (1) (1) (3).pdf', 'pdf', '2025-04-07 11:09:06', 131270, 6),
(167, 177, 'research_results', 'C:/xampp/htdocs/uploads/manuscript_submission/jadual sem6 (3) (1).pdf', 'jadual sem6 (3) (1).pdf', 'pdf', '2025-04-07 11:12:15', 131270, 6),
(168, 178, 'research_materials', 'C:/xampp/htdocs/uploads/manuscript_submission/jadual sem6 (3) (1) (1) (3).pdf', 'jadual sem6 (3) (1) (1) (3).pdf', 'pdf', '2025-04-07 11:21:56', 131270, 6),
(169, 179, 'research_results', 'C:/xampp/htdocs/uploads/manuscript_submission/jadual sem6 (3) (1) (1) (3).pdf', 'jadual sem6 (3) (1) (1) (3).pdf', 'pdf', '2025-04-07 11:22:32', 131270, 6),
(170, 180, 'research_instrument', 'C:/xampp/htdocs/uploads/manuscript_submission/jadual sem6 (3) (1) (1) (3).pdf', 'jadual sem6 (3) (1) (1) (3).pdf', 'pdf', '2025-04-07 11:23:08', 131270, 6),
(171, 181, 'research_materials', 'C:/xampp/htdocs/uploads/manuscript_submission/jadual sem6 (3) (1) (1) (3).pdf', 'jadual sem6 (3) (1) (1) (3).pdf', 'pdf', '2025-04-07 15:08:41', 131270, 6),
(172, 182, 'research_instrument', 'C:/xampp/htdocs/uploads/manuscript_submission/jadual sem6 (3) (1) (1) (3).pdf', 'jadual sem6 (3) (1) (1) (3).pdf', 'pdf', '2025-04-07 16:05:38', 131270, 2),
(173, 183, 'research_materials', 'C:/xampp/htdocs/uploads/manuscript_submission/jadual sem6 (3) (1) (1) (3).pdf', 'jadual sem6 (3) (1) (1) (3).pdf', 'pdf', '2025-04-07 16:08:06', 131270, 6),
(174, 184, 'research_materials', 'C:/xampp/htdocs/uploads/manuscript_submission/Meeting log - Azieatul (1).pdf', 'Meeting log - Azieatul (1).pdf', 'pdf', '2025-04-07 16:10:41', 126498, 6),
(175, 185, 'research_materials', 'C:/xampp/htdocs/uploads/manuscript_submission/jadual sem6 (3) (1) (1) (3).pdf', 'jadual sem6 (3) (1) (1) (3).pdf', 'pdf', '2025-04-07 16:54:50', 131270, 6),
(176, 186, 'research_instrument', 'C:/xampp/htdocs/uploads/manuscript_submission/STIWK2114 A241 Final Assesment (1).pdf', 'STIWK2114 A241 Final Assesment (1).pdf', 'pdf', '2025-04-08 02:03:37', 68746, 1),
(177, 187, 'article', 'C:/xampp/htdocs/uploads/manuscript_submission/STID3113_1_2023_2024-A231.pdf', 'STID3113_1_2023_2024-A231.pdf', 'pdf', '2025-04-08 02:29:09', 1958025, 1),
(178, 188, 'article', 'C:/xampp/htdocs/uploads/manuscript_submission/STIWK2114 A241 Final Assesment.pdf', 'STIWK2114 A241 Final Assesment.pdf', 'pdf', '2025-04-12 06:42:01', 68746, 2),
(179, 189, 'source_texts', 'C:/xampp/htdocs/uploads/manuscript_submission/patients__clients__expectation_toward_and.5.pdf', 'patients__clients__expectation_toward_and.5.pdf', 'pdf', '2025-04-12 11:03:05', 511040, 2),
(180, 190, 'research_instrument', 'C:/xampp/htdocs/uploads/manuscript_submission/6A. DESCRIBING TRENDS EXERCISE ANALYSES AND REASONS 1.pdf', '6A. DESCRIBING TRENDS EXERCISE ANALYSES AND REASONS 1.pdf', 'pdf', '2025-04-14 13:54:01', 50055, 8),
(181, 191, 'transcript', 'C:/xampp/htdocs/uploads/manuscript_submission/6A. DESCRIBING TRENDS EXERCISE ANALYSES AND REASONS 1.pdf', '6A. DESCRIBING TRENDS EXERCISE ANALYSES AND REASONS 1.pdf', 'pdf', '2025-04-20 09:09:27', 50055, 1),
(182, 192, 'research_materials', 'C:/xampp/htdocs/uploads/manuscript_submission/Pengantar Penulisan (Penulisan Modul).pdf', 'Pengantar Penulisan (Penulisan Modul).pdf', 'pdf', '2025-06-11 12:14:05', 1533234, 1);

-- --------------------------------------------------------

--
-- Table structure for table `submission_status`
--

CREATE TABLE `submission_status` (
  `status_id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `status` enum('submitted','pending','under review','accept','minor revision required','major revision required','reject','pending:payment','published') NOT NULL DEFAULT 'submitted',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submission_status`
--

INSERT INTO `submission_status` (`status_id`, `submission_id`, `status`, `updated_at`) VALUES
(146, 174, 'under review', '2025-04-07 10:51:54'),
(147, 175, '', '2025-04-07 16:01:19'),
(148, 176, 'under review', '2025-04-07 11:24:48'),
(149, 177, 'under review', '2025-04-07 11:24:48'),
(150, 178, 'under review', '2025-04-07 11:24:48'),
(151, 179, 'under review', '2025-04-07 11:24:48'),
(152, 180, 'under review', '2025-04-07 16:56:07'),
(153, 181, 'under review', '2025-04-07 15:09:48'),
(154, 182, 'submitted', '2025-04-07 16:05:38'),
(155, 183, 'submitted', '2025-04-07 16:08:06'),
(156, 184, 'submitted', '2025-04-07 16:10:41'),
(157, 185, 'under review', '2025-04-07 16:56:07'),
(158, 186, 'under review', '2025-04-08 02:05:29'),
(159, 187, 'under review', '2025-04-08 02:33:22'),
(160, 188, 'under review', '2025-04-12 06:44:46'),
(161, 189, 'under review', '2025-04-12 11:06:01'),
(162, 190, 'under review', '2025-04-14 13:56:13'),
(163, 191, 'under review', '2025-04-20 09:12:57'),
(164, 192, 'under review', '2025-06-11 12:15:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `affiliation` varchar(100) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('author','reviewer') DEFAULT 'author',
  `is_agreed_privacy` tinyint(1) DEFAULT 0,
  `is_notified` tinyint(1) DEFAULT 0,
  `is_reviewer` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `certification_file` varchar(255) DEFAULT NULL,
  `expertise` varchar(255) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `profile_image` varchar(255) DEFAULT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `affiliation`, `country`, `username`, `email`, `password`, `role`, `is_agreed_privacy`, `is_notified`, `is_reviewer`, `created_at`, `certification_file`, `expertise`, `organization`, `is_verified`, `profile_image`, `password_reset_token`) VALUES
(1, 'NURUL HARISYAH BINTI ADZAMI', 'School of Computing', 'Malaysia', 'harisyah', 'harisyahadzami@gmail.com', '$2y$10$kVSo34rLzV6JOlrhWS681OfHn2WuNoan7vEv7oeGik6JUmN86IP4G', 'reviewer', 1, 1, 1, '2024-11-30 23:26:21', 'uploads/reviewer_certifications/677ce7ca1bfed-STIDK3013-PROPOSAL RESEARCH METHOD IN IT.pdf', 'Data Science', 'UUM', 1, '677282421b0ff.jpg', NULL),
(2, 'AZIEATUL AZIELA', 'School of Computer Science', 'Malaysia', 'Aziela', 'azielaazieatul@gmail.com', '$2y$10$A..s7ft7onJNqXnzaHVE8eL7ap3BFx1QFPNaPMO8duqRFmRO/lwc.', 'reviewer', 1, 1, 1, '2024-12-30 16:48:34', 'uploads/reviewer_certifications/6772cf1d9fa70-Design.pdf', 'Artificial Intelligence', 'UUM', 1, '67f3a3ff847a3.jpg', NULL),
(3, 'FARAH', 'School of Education', 'Malaysia', 'farah', 'farah00@gmail.com', '$2y$10$ewAvFOwAIZ14BJJQaonI3eJH2gu6RfRF4sqroJaMHjsFrS0P3lmTK', 'author', 1, 1, 0, '2025-01-02 15:10:35', 'uploads/reviewer_certifications/6776acb53411a-design-network.png', '', '', 1, NULL, NULL),
(6, 'zie', 'uum', 'Malaysia', 'zie', 'zie@gmail.com', '$2y$10$rBoRCSqwxITiYUpfYrywwOwnllhy8XtWWfCVsQjd.l7XoOu8rPvJS', 'author', 1, 1, 0, '2025-03-27 04:44:09', NULL, NULL, NULL, 0, NULL, NULL),
(7, 'rai', 'UUM', 'Malaysia', 'rai', 'rai@gmail.com', '$2y$10$LmA1WQpryVaJu4u/yeXQ3ueMJ3zWKqr5mT2EuPmco/MgU2Py8RNru', 'reviewer', 1, 1, 1, '2025-04-06 12:21:09', 'uploads/reviewer_certifications/67f271d52218a-Meeting log - Azieatul (1).pdf', 'Artificial Intelligence', 'UUM', 1, NULL, NULL),
(8, 'Test1', 'School of Computation', 'Malaysia', 'test1', 'test1@gmail.com', '$2y$10$3Php0sb9VbIXS2v0XI4pO.8LPO3tahg412mYd58xrSzVF4YoapAZ.', 'reviewer', 1, 1, 1, '2025-04-14 13:51:55', 'uploads/reviewer_certifications/67fd13515f7d5-6A. DESCRIBING TRENDS EXERCISE ANALYSES AND REASONS 1.pdf', 'Networking', 'UUM', 1, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contributors`
--
ALTER TABLE `contributors`
  ADD PRIMARY KEY (`contributor_id`),
  ADD KEY `submission_id` (`submission_id`);

--
-- Indexes for table `editor`
--
ALTER TABLE `editor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `editor_decisions`
--
ALTER TABLE `editor_decisions`
  ADD PRIMARY KEY (`decision_id`),
  ADD KEY `submission_id` (`submission_id`),
  ADD KEY `editor_id` (`editor_id`);

--
-- Indexes for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`evaluation_id`),
  ADD UNIQUE KEY `unique_assignment` (`assignment_id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `submission_id` (`submission_id`);

--
-- Indexes for table `metadata`
--
ALTER TABLE `metadata`
  ADD PRIMARY KEY (`metadata_id`),
  ADD KEY `submission_id` (`submission_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `payment_proofs`
--
ALTER TABLE `payment_proofs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submission_id` (`submission_id`);

--
-- Indexes for table `publish_article`
--
ALTER TABLE `publish_article`
  ADD PRIMARY KEY (`publish_id`),
  ADD KEY `submission_id` (`submission_id`);

--
-- Indexes for table `reviewer_assignments`
--
ALTER TABLE `reviewer_assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `submission_id` (`submission_id`),
  ADD KEY `reviewer_id` (`reviewer_id`);

--
-- Indexes for table `review_status`
--
ALTER TABLE `review_status`
  ADD PRIMARY KEY (`status_id`),
  ADD KEY `assignment_id` (`assignment_id`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`submission_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `submission_files`
--
ALTER TABLE `submission_files`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `submission_id` (`submission_id`);

--
-- Indexes for table `submission_status`
--
ALTER TABLE `submission_status`
  ADD PRIMARY KEY (`status_id`),
  ADD KEY `submission_id` (`submission_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contributors`
--
ALTER TABLE `contributors`
  MODIFY `contributor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=176;

--
-- AUTO_INCREMENT for table `editor`
--
ALTER TABLE `editor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `editor_decisions`
--
ALTER TABLE `editor_decisions`
  MODIFY `decision_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `evaluation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=167;

--
-- AUTO_INCREMENT for table `metadata`
--
ALTER TABLE `metadata`
  MODIFY `metadata_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=177;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_proofs`
--
ALTER TABLE `payment_proofs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `publish_article`
--
ALTER TABLE `publish_article`
  MODIFY `publish_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `reviewer_assignments`
--
ALTER TABLE `reviewer_assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=179;

--
-- AUTO_INCREMENT for table `review_status`
--
ALTER TABLE `review_status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;

--
-- AUTO_INCREMENT for table `submission_files`
--
ALTER TABLE `submission_files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT for table `submission_status`
--
ALTER TABLE `submission_status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contributors`
--
ALTER TABLE `contributors`
  ADD CONSTRAINT `contributors_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`submission_id`);

--
-- Constraints for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `reviewer_assignments` (`assignment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_submission_id` FOREIGN KEY (`submission_id`) REFERENCES `reviewer_assignments` (`submission_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `metadata`
--
ALTER TABLE `metadata`
  ADD CONSTRAINT `metadata_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`submission_id`);

--
-- Constraints for table `payment_proofs`
--
ALTER TABLE `payment_proofs`
  ADD CONSTRAINT `payment_proofs_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `editor_decisions` (`submission_id`);

--
-- Constraints for table `publish_article`
--
ALTER TABLE `publish_article`
  ADD CONSTRAINT `publish_article_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`submission_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviewer_assignments`
--
ALTER TABLE `reviewer_assignments`
  ADD CONSTRAINT `reviewer_assignments_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`submission_id`),
  ADD CONSTRAINT `reviewer_assignments_ibfk_2` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `review_status`
--
ALTER TABLE `review_status`
  ADD CONSTRAINT `review_status_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `reviewer_assignments` (`assignment_id`) ON DELETE CASCADE;

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `submission_files`
--
ALTER TABLE `submission_files`
  ADD CONSTRAINT `submission_files_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`submission_id`);

--
-- Constraints for table `submission_status`
--
ALTER TABLE `submission_status`
  ADD CONSTRAINT `submission_status_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `submission_files` (`submission_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
