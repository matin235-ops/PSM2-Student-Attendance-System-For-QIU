-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 14, 2025 at 05:08 PM
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
-- Database: `attendancemsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbladmin`
--

CREATE TABLE `tbladmin` (
  `Id` int(10) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `emailAddress` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbladmin`
--

INSERT INTO `tbladmin` (`Id`, `firstName`, `lastName`, `emailAddress`, `password`) VALUES
(1, 'Admin', '', 'admin@mail.com', 'D00F5D5217896FB7FD601412CB890830');

-- --------------------------------------------------------

--
-- Table structure for table `tblattendance`
--

CREATE TABLE `tblattendance` (
  `Id` int(10) NOT NULL,
  `admissionNo` varchar(255) NOT NULL,
  `classId` varchar(10) NOT NULL,
  `classArmId` varchar(10) NOT NULL,
  `sessionTermId` varchar(10) NOT NULL,
  `status` varchar(10) NOT NULL,
  `dateTimeTaken` varchar(20) NOT NULL,
  `sessionNumber` int(11) DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblattendance`
--

INSERT INTO `tblattendance` (`Id`, `admissionNo`, `classId`, `classArmId`, `sessionTermId`, `status`, `dateTimeTaken`, `sessionNumber`) VALUES
(778, 'QIU-586', '12', '20', '', '0', '2025-04-23', 2),
(777, 'QIU-586', '12', '20', '', '1', '2025-04-23', 1),
(776, 'QIU-459', '12', '19', '', '0', '2025-04-23', 2),
(775, 'QIU-875', '12', '19', '', '1', '2025-04-23', 2),
(774, 'QIU-459', '12', '19', '', '1', '2025-04-23', 1),
(773, 'QIU-875', '12', '19', '', '1', '2025-04-23', 1),
(772, 'QIU-586', '12', '20', '', '0', '2025-04-21', 2),
(771, 'QIU-586', '12', '20', '', '1', '2025-04-21', 1),
(770, 'QIU-459', '12', '19', '', '1', '2025-04-21', 2),
(769, 'QIU-875', '12', '19', '', '1', '2025-04-21', 2),
(768, 'QIU-459', '12', '19', '', '1', '2025-04-21', 1),
(767, 'QIU-875', '12', '19', '', '1', '2025-04-21', 1),
(766, 'QIU-890', '15', '25', '', '1', '2025-04-21', 2),
(765, 'QIU-218', '15', '25', '', '1', '2025-04-21', 2),
(764, 'QIU-1111', '15', '25', '', '1', '2025-04-21', 2),
(763, 'QIU-231', '15', '25', '', '1', '2025-04-21', 2),
(762, 'QIU-086', '15', '25', '', '0', '2025-04-21', 2),
(761, 'QIU-326', '15', '25', '', '0', '2025-04-21', 2),
(760, 'QIU-111', '15', '25', '', '0', '2025-04-21', 2),
(759, 'QIU-207', '15', '25', '', '0', '2025-04-21', 2),
(758, 'QIU-512', '15', '25', '', '0', '2025-04-21', 2),
(712, 'QIU-586', '12', '20', '', '0', '2025-04-20', 2),
(711, 'QIU-586', '12', '20', '', '1', '2025-04-20', 1),
(710, 'QIU-459', '12', '19', '', '0', '2025-04-20', 2),
(709, 'QIU-875', '12', '19', '', '0', '2025-04-20', 2),
(708, 'QIU-459', '12', '19', '', '0', '2025-04-20', 1),
(707, 'QIU-875', '12', '19', '', '0', '2025-04-20', 1),
(757, 'QIU-890', '15', '25', '', '0', '2025-04-21', 1),
(756, 'QIU-1111', '15', '25', '', '0', '2025-04-21', 1),
(755, 'QIU-231', '15', '25', '', '0', '2025-04-21', 1),
(754, 'QIU-218', '15', '25', '', '0', '2025-04-21', 1),
(753, 'QIU-086', '15', '25', '', '0', '2025-04-21', 1),
(752, 'QIU-326', '15', '25', '', '1', '2025-04-21', 1),
(751, 'QIU-512', '15', '25', '', '1', '2025-04-21', 1),
(750, 'QIU-111', '15', '25', '', '1', '2025-04-21', 1),
(749, 'QIU-207', '15', '25', '', '1', '2025-04-21', 1),
(799, 'QIU-875', '12', '19', '', '0', '2025-05-02', 2),
(798, 'QIU-459', '12', '19', '', '0', '2025-05-02', 1),
(797, 'QIU-875', '12', '19', '', '0', '2025-05-02', 1),
(796, 'QIU-245', '3', '16', '', '0', '2025-05-02', 2),
(795, 'QIU-231', '3', '16', '', '1', '2025-05-02', 2),
(794, 'QIU-562', '3', '16', '', '0', '2025-05-02', 2),
(793, 'QIU-245', '3', '16', '', '1', '2025-05-02', 1),
(792, 'QIU-231', '3', '16', '', '1', '2025-05-02', 1),
(791, 'QIU-562', '3', '16', '', '1', '2025-05-02', 1),
(800, 'QIU-459', '12', '19', '', '0', '2025-05-02', 2),
(801, 'QIU-586', '12', '20', '', '1', '2025-05-02', 1),
(802, 'QIU-512', '15', '25', '', '1', '2025-05-02', 1),
(803, 'QIU-207', '15', '25', '', '1', '2025-05-02', 1),
(804, 'QIU-111', '15', '25', '', '1', '2025-05-02', 1),
(805, 'QIU-326', '15', '25', '', '1', '2025-05-02', 1),
(806, 'QIU-086', '15', '25', '', '1', '2025-05-02', 1),
(807, 'QIU-231', '15', '25', '', '0', '2025-05-02', 1),
(808, 'QIU-1111', '15', '25', '', '1', '2025-05-02', 1),
(809, 'QIU-218', '15', '25', '', '1', '2025-05-02', 1),
(810, 'QIU-890', '15', '25', '', '0', '2025-05-02', 1),
(811, 'QIU-512', '15', '25', '', '1', '2025-05-02', 2),
(812, 'QIU-207', '15', '25', '', '0', '2025-05-02', 2),
(813, 'QIU-111', '15', '25', '', '1', '2025-05-02', 2),
(814, 'QIU-326', '15', '25', '', '0', '2025-05-02', 2),
(815, 'QIU-086', '15', '25', '', '1', '2025-05-02', 2),
(816, 'QIU-231', '15', '25', '', '0', '2025-05-02', 2),
(817, 'QIU-1111', '15', '25', '', '0', '2025-05-02', 2),
(818, 'QIU-218', '15', '25', '', '1', '2025-05-02', 2),
(819, 'QIU-890', '15', '25', '', '1', '2025-05-02', 2),
(820, 'QIU-239', '12', '18', '', '1', '2025-05-02', 1),
(821, 'QIU-121', '12', '18', '', '1', '2025-05-02', 1),
(822, 'QIU-218', '12', '18', '', '0', '2025-05-02', 1),
(823, 'QIU-586', '12', '20', '', '1', '2025-05-02', 2),
(843, 'QIU2167', '16', '28', '', '1', '2025-05-06', 2),
(842, 'QIU325', '16', '28', '', '1', '2025-05-06', 2),
(841, 'QIU2167', '16', '28', '', '0', '2025-05-06', 1),
(840, 'QIU325', '16', '28', '', '1', '2025-05-06', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblclass`
--

CREATE TABLE `tblclass` (
  `Id` int(10) NOT NULL,
  `className` varchar(255) NOT NULL,
  `stageName` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblclass`
--

INSERT INTO `tblclass` (`Id`, `className`, `stageName`) VALUES
(3, 'Eight', NULL),
(16, 'stage 4 group B', NULL),
(11, 'software', NULL),
(12, '1-A', NULL),
(13, 'Stage 4-A', NULL),
(15, 'stage:4 software engineering', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblclassarms`
--

CREATE TABLE `tblclassarms` (
  `Id` int(10) NOT NULL,
  `classId` varchar(10) NOT NULL,
  `classArmName` varchar(255) NOT NULL,
  `isAssigned` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblclassarms`
--

INSERT INTO `tblclassarms` (`Id`, `classId`, `classArmName`, `isAssigned`) VALUES
(16, '3', '1', '1'),
(15, '12', 'Digital', '1'),
(17, '12', 'Software Engineering', '1'),
(18, '12', 'Real Time software engineering', '1'),
(19, '12', 'Software Engineering 2', '1'),
(20, '12', 'Web Technology', '1'),
(21, '12', 'digital logic', '1'),
(22, '12', 'Web Programming', '1'),
(23, '3', 'G16', '1'),
(24, '14', 'asia', '0'),
(25, '15', 'Artificial Intelligence (AI) ', '1'),
(26, '13', 'SDA', '1'),
(27, '12', 'UI', '1'),
(28, '16', 'Software quality Asurance', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tblclassteacher`
--

CREATE TABLE `tblclassteacher` (
  `Id` int(11) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `emailAddress` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phoneNo` varchar(50) NOT NULL,
  `classId` varchar(10) NOT NULL,
  `classArmId` varchar(10) NOT NULL,
  `dateCreated` varchar(50) NOT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblclassteacher`
--

INSERT INTO `tblclassteacher` (`Id`, `firstName`, `lastName`, `emailAddress`, `password`, `phoneNo`, `classId`, `classArmId`, `dateCreated`, `status`) VALUES
(23, 'Matin', 'Khaled', 'matiin@gmail.com', '25d55ad283aa400af464c76d713c07ad', '07705554555', '', '', '2025-03-12', 1),
(28, 'matin', 'khaled', 'matin5@gmail.com', '202cb962ac59075b964b07152d234b70', '077022213', '', '', '2025-04-19', 1),
(42, 'Zheer', 'Ahmed', 'zheer@gmail.com', '202cb962ac59075b964b07152d234b70', '07712558565', '', '', '2025-04-20', 1),
(45, 'mr.kawa', 'Ahmed', 'kawa@gmail.com', '202cb962ac59075b964b07152d234b70', '07701234554', '', '', '2025-05-02', 1),
(48, 'kawa', 'Ahmed', 'kawa1@gmail.com', '202cb962ac59075b964b07152d234b70', '07702221445', '', '', '2025-05-06', 1),
(49, 'Hariss', 'Siduqe', 'harissiduqe@gmail.com', '25d55ad283aa400af464c76d713c07ad', '07701234565', '', '', '2025-05-06', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbllecturetimes`
--

CREATE TABLE `tbllecturetimes` (
  `Id` int(11) NOT NULL,
  `timeSlot` varchar(50) DEFAULT NULL,
  `startTime` time DEFAULT NULL,
  `endTime` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbllecturetimes`
--

INSERT INTO `tbllecturetimes` (`Id`, `timeSlot`, `startTime`, `endTime`) VALUES
(1, 'First Lecture', '09:00:00', '10:30:00'),
(2, 'Second Lecture', '11:00:00', '12:30:00'),
(3, 'Third Lecture', '13:00:00', '14:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `tblmessages`
--

CREATE TABLE `tblmessages` (
  `id` int(11) NOT NULL,
  `senderId` int(11) NOT NULL,
  `senderType` enum('admin','teacher') NOT NULL,
  `receiverId` int(11) NOT NULL,
  `receiverType` enum('admin','teacher') NOT NULL,
  `message` text NOT NULL,
  `isRead` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_by_teacher` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblmessages`
--

INSERT INTO `tblmessages` (`id`, `senderId`, `senderType`, `receiverId`, `receiverType`, `message`, `isRead`, `created_at`, `deleted_by_teacher`) VALUES
(107, 1, 'admin', 23, 'teacher', 'hi', 0, '2025-05-06 06:34:16', 0),
(110, 1, 'admin', 28, 'teacher', 'hiii', 0, '2025-05-06 06:38:56', 0),
(111, 49, 'teacher', 1, 'admin', 'please fix issue', 0, '2025-05-06 10:29:55', 0),
(112, 1, 'admin', 49, 'teacher', 'ok i fix ', 0, '2025-05-06 10:31:02', 0);

--
-- Triggers `tblmessages`
--
DELIMITER $$
CREATE TRIGGER `before_insert_message` BEFORE INSERT ON `tblmessages` FOR EACH ROW BEGIN
    IF NEW.senderType = 'admin' THEN
        IF NOT EXISTS (SELECT 1 FROM tbladmin WHERE Id = NEW.senderId) THEN
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'Invalid admin ID in sender';
        END IF;
    ELSEIF NEW.senderType = 'teacher' THEN
        IF NOT EXISTS (SELECT 1 FROM tblclassteacher WHERE Id = NEW.senderId) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Invalid teacher ID in sender';
        END IF;
    END IF;

    IF NEW.receiverType = 'admin' THEN
        IF NOT EXISTS (SELECT 1 FROM tbladmin WHERE Id = NEW.receiverId) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Invalid admin ID in receiver';
        END IF;
    ELSEIF NEW.receiverType = 'teacher' THEN
        IF NOT EXISTS (SELECT 1 FROM tblclassteacher WHERE Id = NEW.receiverId) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Invalid teacher ID in receiver';
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tblsessionterm`
--

CREATE TABLE `tblsessionterm` (
  `Id` int(10) NOT NULL,
  `sessionName` varchar(50) NOT NULL,
  `termId` varchar(50) NOT NULL,
  `isActive` varchar(10) NOT NULL,
  `dateCreated` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblsessionterm`
--

INSERT INTO `tblsessionterm` (`Id`, `sessionName`, `termId`, `isActive`, `dateCreated`) VALUES
(7, '2025/2026', '2', '0', '2025-03-12');

-- --------------------------------------------------------

--
-- Table structure for table `tblsettings`
--

CREATE TABLE `tblsettings` (
  `id` int(11) NOT NULL,
  `schoolName` varchar(255) NOT NULL,
  `schoolAddress` text DEFAULT NULL,
  `schoolPhone` varchar(20) DEFAULT NULL,
  `schoolEmail` varchar(100) DEFAULT NULL,
  `sessionYear` varchar(20) DEFAULT NULL,
  `schoolLogo` varchar(255) DEFAULT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `dateUpdated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblsettings`
--

INSERT INTO `tblsettings` (`id`, `schoolName`, `schoolAddress`, `schoolPhone`, `schoolEmail`, `sessionYear`, `schoolLogo`, `dateCreated`, `dateUpdated`) VALUES
(1, 'Qaiwan international university ', 'Slemani - Hawaryshar', '', 'qiu@uniq.edu.iq', '2025-2026', 'uploads/1740487220_UTM.png', '2025-02-25 12:38:39', '2025-02-25 12:40:20');

-- --------------------------------------------------------

--
-- Table structure for table `tblstudents`
--

CREATE TABLE `tblstudents` (
  `Id` int(10) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `otherName` varchar(255) NOT NULL,
  `admissionNumber` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(50) NOT NULL,
  `classId` varchar(10) NOT NULL,
  `classArmId` varchar(10) NOT NULL,
  `dateCreated` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblstudents`
--

INSERT INTO `tblstudents` (`Id`, `firstName`, `lastName`, `otherName`, `admissionNumber`, `email`, `password`, `classId`, `classArmId`, `dateCreated`) VALUES
(41, 'kazhin', 'jjjkoo', '', 'QIU-121', NULL, '12345', '12', '18', '2025-03-12'),
(42, 'matin', 'Khaled', '', 'QIU-231', NULL, '12345', '15', '25', '2025-03-12'),
(65, 'matin', 'Khaled', '', 'QIU-1111', NULL, '12345', '15', '25', '2025-04-20'),
(70, 'matin', 'Khalid', '', 'QIU2167', NULL, '12345', '16', '28', '2025-05-06'),
(68, 'ahmed', 'kawa', '', 'QIU-345', NULL, '12345', '3', '23', '2025-05-06'),
(69, 'matin', 'Khaled', '', 'QIU-529', NULL, '12345', '12', '15', '2025-05-06'),
(48, 'Matin', 'Hiwa', '', 'QIU-218', NULL, '12345', '12', '18', '2025-03-15'),
(49, 'Barin', 'fan', '', 'QIU-512', NULL, '12345', '15', '25', '2025-03-17'),
(50, 'Honyar', 'Bakhtyar', '', 'QIU-245', NULL, '12345', '3', '16', '2025-03-19'),
(67, 'Barin', 'khaled', '', 'QIU-207', NULL, '12345', '15', '25', '2025-04-20'),
(52, 'Ahmed', 'khaled', '', 'QIU-562', NULL, '12345', '3', '16', '2025-04-13'),
(64, 'kazhal', 'lona', '', 'QIU-086', NULL, '12345', '15', '25', '2025-04-20'),
(54, 'Dwin ', 'khaled', '', 'QIU-239', NULL, '12345', '12', '18', '2025-04-13'),
(55, 'matin', 'khaled', '', 'QIU-218', NULL, '12345', '15', '25', '2025-04-15'),
(56, 'Chatin', 'Khaled', '', 'QIU-231', NULL, '12345', '3', '16', '2025-04-15'),
(57, 'matin', 'khaled', '', 'QIU-586', NULL, '12345', '12', '20', '2025-04-19'),
(58, 'Barin', 'khaled', '', 'QIU-875', NULL, '12345', '12', '19', '2025-04-19'),
(59, 'Matin', 'hiewa', '', 'QIU-459', NULL, '12345', '12', '19', '2025-04-19'),
(60, 'Kawa ', 'Ahmed', '', 'QIU-326', NULL, '12345', '15', '25', '2025-04-20'),
(62, 'Barin', 'khaleed', '', 'QIU-111', NULL, '12345', '15', '25', '2025-04-20'),
(63, 'Mohammed', 'Ahmed', '', 'QIU-890', NULL, '12345', '15', '25', '2025-04-20'),
(71, 'ibrahim', 'Ahmed', '', 'QIU325', NULL, '12345', '16', '28', '2025-05-06');

-- --------------------------------------------------------

--
-- Table structure for table `tblsubjects`
--

CREATE TABLE `tblsubjects` (
  `Id` int(10) NOT NULL,
  `subjectName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblteacherlogs`
--

CREATE TABLE `tblteacherlogs` (
  `Id` int(10) NOT NULL,
  `teacherId` int(10) NOT NULL,
  `activity` varchar(255) NOT NULL,
  `logTime` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblteacherlogs`
--

INSERT INTO `tblteacherlogs` (`Id`, `teacherId`, `activity`, `logTime`) VALUES
(1, 23, 'Accessed dashboard', '2025-04-19 18:24:07'),
(2, 23, 'Accessed dashboard', '2025-04-19 18:24:17'),
(3, 23, 'Attempted to take attendance beyond daily limit', '2025-04-19 18:24:22'),
(4, 23, 'Accessed dashboard', '2025-04-19 18:24:24'),
(5, 28, 'Accessed dashboard', '2025-04-19 18:54:32'),
(6, 28, 'Took attendance for Session #2 - 1-A (Software Engineering 2)', '2025-04-19 18:54:39'),
(7, 28, 'Took attendance for Session #2 - Class: 1-A (Software Engineering 2), Subject:  - Present: 2, Absent: 0', '2025-04-19 18:54:39'),
(8, 28, 'Accessed dashboard', '2025-04-19 18:54:41'),
(9, 28, 'Accessed dashboard', '2025-04-19 19:03:00'),
(10, 28, 'Took attendance for Session #1 - 1-A-Web Technology ()', '2025-04-19 19:03:05'),
(11, 28, 'Accessed dashboard', '2025-04-19 19:03:07'),
(12, 28, 'Attempted to take attendance beyond daily limit', '2025-04-19 19:03:12'),
(13, 28, 'Accessed dashboard', '2025-04-19 19:03:13'),
(14, 42, 'Accessed dashboard', '2025-04-20 06:36:03'),
(15, 42, 'Took attendance for Session #1 - 1-A-Digital ()', '2025-04-20 06:36:25'),
(16, 42, 'Accessed dashboard', '2025-04-20 06:36:32'),
(17, 42, 'Took attendance for Session #1 - 1-A-Real Time software engineering ()', '2025-04-20 06:36:46'),
(18, 42, 'Accessed dashboard', '2025-04-20 06:36:48'),
(19, 42, 'Took attendance for Session #1 - stage:4 software engineering-Artificial Intelligence (AI)  ()', '2025-04-20 06:37:06'),
(20, 42, 'Accessed dashboard', '2025-04-20 06:37:09'),
(21, 42, 'Accessed dashboard', '2025-04-20 06:37:47'),
(22, 42, 'Accessed dashboard', '2025-04-20 06:40:06'),
(23, 42, 'Accessed dashboard', '2025-04-20 06:40:08'),
(24, 42, 'Accessed dashboard', '2025-04-20 06:49:23'),
(25, 42, 'Took attendance for Session #2 - 1-A-Digital ()', '2025-04-20 06:49:27'),
(26, 42, 'Accessed dashboard', '2025-04-20 06:49:29'),
(27, 42, 'Accessed dashboard', '2025-04-20 06:54:23'),
(28, 42, 'Took attendance for Session #2 - 1-A-Real Time software engineering ()', '2025-04-20 06:54:31'),
(29, 42, 'Accessed dashboard', '2025-04-20 07:44:14'),
(30, 42, 'Accessed dashboard', '2025-04-20 07:47:46'),
(31, 42, 'Accessed dashboard', '2025-04-20 07:47:51'),
(32, 42, 'Accessed dashboard', '2025-04-20 07:59:49'),
(33, 42, 'Accessed dashboard', '2025-04-20 08:05:34'),
(34, 42, 'Accessed dashboard', '2025-04-20 08:05:37'),
(35, 42, 'Took attendance for Session #2 - stage:4 software engineering-Artificial Intelligence (AI)  ()', '2025-04-20 08:05:48'),
(36, 42, 'Accessed dashboard', '2025-04-20 08:41:53'),
(37, 42, 'Accessed dashboard', '2025-04-20 08:42:01'),
(38, 42, 'Attempted to take attendance beyond daily limit', '2025-04-20 08:42:07'),
(39, 23, 'Accessed dashboard', '2025-04-20 12:20:00'),
(40, 23, 'Accessed dashboard', '2025-04-20 13:13:48'),
(41, 23, 'Took attendance for Session #1 - Eight-1 ()', '2025-04-20 13:14:18'),
(42, 23, 'Accessed dashboard', '2025-04-20 13:14:20'),
(43, 23, 'Accessed dashboard', '2025-04-20 13:14:59'),
(44, 23, 'Took attendance for Session #2 - Eight-1 ()', '2025-04-20 13:15:05'),
(45, 23, 'Attempted to take attendance beyond daily limit', '2025-04-20 13:15:10'),
(46, 23, 'Accessed dashboard', '2025-04-20 13:16:18'),
(47, 23, 'Took attendance for Session #1 - Eight-1 ()', '2025-04-20 13:16:26'),
(48, 23, 'Took attendance for Session #2 - Eight-1 ()', '2025-04-20 13:16:29'),
(49, 23, 'Attempted to take attendance beyond daily limit', '2025-04-20 13:16:42'),
(50, 28, 'Accessed dashboard', '2025-04-20 14:25:57'),
(51, 28, 'Accessed dashboard', '2025-04-20 14:50:25'),
(52, 28, 'Took attendance for Session #1 - 1-A-Software Engineering 2 ()', '2025-04-20 14:50:37'),
(53, 28, 'Accessed dashboard', '2025-04-20 14:50:59'),
(54, 28, 'Took attendance for Session #1 - 1-A-Web Technology ()', '2025-04-20 14:51:13'),
(55, 28, 'Accessed dashboard', '2025-04-20 14:51:15'),
(56, 28, 'Took attendance for Session #2 - 1-A-Software Engineering 2 ()', '2025-04-20 14:51:22'),
(57, 28, 'Accessed dashboard', '2025-04-20 14:51:24'),
(58, 28, 'Accessed dashboard', '2025-04-20 14:52:22'),
(59, 28, 'Accessed dashboard', '2025-04-20 14:55:10'),
(60, 23, 'Accessed dashboard', '2025-04-20 14:58:07'),
(61, 23, 'Attempted to take attendance beyond daily limit', '2025-04-20 14:58:13'),
(62, 23, 'Accessed dashboard', '2025-04-20 14:58:15'),
(63, 42, 'Accessed dashboard', '2025-04-20 15:03:19'),
(64, 42, 'Accessed dashboard', '2025-04-20 15:04:05'),
(65, 42, 'Accessed dashboard', '2025-04-20 15:04:08'),
(66, 42, 'Accessed dashboard', '2025-04-20 15:05:19'),
(67, 42, 'Took attendance for Session #1 - stage:4 software engineering-Artificial Intelligence (AI)  ()', '2025-04-20 15:05:31'),
(68, 42, 'Took attendance for Session #2 - stage:4 software engineering-Artificial Intelligence (AI)  ()', '2025-04-20 15:05:47'),
(69, 42, 'Accessed dashboard', '2025-04-20 15:05:49'),
(70, 28, 'Accessed dashboard', '2025-04-20 15:09:59'),
(71, 28, 'Accessed dashboard', '2025-04-20 15:10:12'),
(72, 23, 'Accessed dashboard', '2025-04-20 18:56:23'),
(73, 23, 'Accessed dashboard', '2025-04-20 18:57:00'),
(74, 23, 'Attempted to take attendance beyond daily limit', '2025-04-20 18:57:09'),
(75, 23, 'Accessed dashboard', '2025-04-20 18:57:11'),
(76, 23, 'Accessed dashboard', '2025-04-20 18:58:05'),
(77, 23, 'Accessed dashboard', '2025-04-20 19:03:56'),
(78, 23, 'Accessed dashboard', '2025-04-20 19:24:12'),
(79, 28, 'Accessed dashboard', '2025-04-20 19:35:46'),
(80, 28, 'Attempted to take attendance beyond daily limit', '2025-04-20 19:36:02'),
(81, 28, 'Accessed dashboard', '2025-04-20 19:36:04'),
(82, 28, 'Accessed dashboard', '2025-04-20 19:36:35'),
(83, 28, 'Took attendance for Session #2 - 1-A-Web Technology ()', '2025-04-20 19:36:42'),
(84, 28, 'Accessed dashboard', '2025-04-20 19:36:43'),
(85, 28, 'Accessed dashboard', '2025-04-20 19:37:54'),
(86, 28, 'Took attendance for Session #1 - 1-A-Software Engineering 2 ()', '2025-04-20 19:38:03'),
(87, 28, 'Took attendance for Session #2 - 1-A-Software Engineering 2 ()', '2025-04-20 19:38:06'),
(88, 28, 'Attempted to take attendance beyond daily limit', '2025-04-20 19:38:12'),
(89, 28, 'Accessed dashboard', '2025-04-20 19:38:13'),
(90, 28, 'Accessed dashboard', '2025-04-20 19:39:35'),
(91, 28, 'Took attendance for Session #1 - 1-A-Web Technology ()', '2025-04-20 19:39:41'),
(92, 28, 'Took attendance for Session #2 - 1-A-Web Technology ()', '2025-04-20 19:39:43'),
(93, 28, 'Accessed dashboard', '2025-04-20 19:39:44'),
(94, 28, 'Accessed dashboard', '2025-04-20 19:40:09'),
(95, 28, 'Accessed dashboard', '2025-04-20 19:40:22'),
(96, 28, 'Accessed dashboard', '2025-04-20 19:40:29'),
(97, 28, 'Accessed dashboard', '2025-04-20 19:40:35'),
(98, 28, 'Accessed dashboard', '2025-04-21 13:32:52'),
(99, 28, 'Took attendance for Session #1 - 1-A-Software Engineering 2 ()', '2025-04-21 13:33:03'),
(100, 28, 'Took attendance for Session #2 - 1-A-Software Engineering 2 ()', '2025-04-21 13:33:08'),
(101, 28, 'Accessed dashboard', '2025-04-21 13:33:09'),
(102, 28, 'Took attendance for Session #1 - 1-A-Web Technology ()', '2025-04-21 13:33:14'),
(103, 28, 'Took attendance for Session #2 - 1-A-Web Technology ()', '2025-04-21 13:33:17'),
(104, 28, 'Accessed dashboard', '2025-04-21 13:33:18'),
(105, 28, 'Accessed dashboard', '2025-04-21 13:37:12'),
(106, 28, 'Accessed dashboard', '2025-04-21 13:41:50'),
(107, 28, 'Accessed dashboard', '2025-04-21 13:43:57'),
(108, 28, 'Accessed dashboard', '2025-04-21 13:47:18'),
(109, 28, 'Accessed dashboard', '2025-04-21 13:52:18'),
(110, 28, 'Accessed dashboard', '2025-04-21 13:55:35'),
(111, 28, 'Accessed dashboard', '2025-04-21 13:57:39'),
(112, 28, 'Accessed dashboard', '2025-04-21 13:59:17'),
(113, 28, 'Took attendance for Session #1 - 1-A-Software Engineering 2 ()', '2025-04-21 13:59:23'),
(114, 28, 'Took attendance for Session #2 - 1-A-Software Engineering 2 ()', '2025-04-21 13:59:25'),
(115, 28, 'Accessed dashboard', '2025-04-21 13:59:26'),
(116, 28, 'Took attendance for Session #1 - 1-A-Web Technology ()', '2025-04-21 13:59:44'),
(117, 28, 'Took attendance for Session #2 - 1-A-Web Technology ()', '2025-04-21 13:59:45'),
(118, 28, 'Accessed dashboard', '2025-04-21 13:59:46'),
(119, 28, 'Accessed dashboard', '2025-04-21 14:00:38'),
(120, 28, 'Accessed dashboard', '2025-04-21 14:17:22'),
(121, 28, 'Accessed dashboard', '2025-04-21 14:21:15'),
(122, 28, 'Accessed dashboard', '2025-04-21 14:21:37'),
(123, 28, 'Accessed dashboard', '2025-04-21 14:23:15'),
(124, 28, 'Accessed dashboard', '2025-04-21 14:24:35'),
(125, 23, 'Accessed dashboard', '2025-04-21 14:25:19'),
(126, 23, 'Took attendance for Session #1 - Eight-1 ()', '2025-04-21 14:25:27'),
(127, 23, 'Took attendance for Session #2 - Eight-1 ()', '2025-04-21 14:25:34'),
(128, 23, 'Accessed dashboard', '2025-04-21 14:25:36'),
(129, 23, 'Accessed dashboard', '2025-04-21 14:28:10'),
(130, 23, 'Accessed dashboard', '2025-04-21 14:30:26'),
(131, 23, 'Accessed dashboard', '2025-04-21 14:32:06'),
(132, 23, 'Accessed dashboard', '2025-04-21 14:34:52'),
(133, 23, 'Accessed dashboard', '2025-04-21 14:36:24'),
(134, 23, 'Accessed dashboard', '2025-04-21 14:36:57'),
(135, 23, 'Accessed dashboard', '2025-04-21 14:39:31'),
(136, 23, 'Accessed dashboard', '2025-04-21 14:41:14'),
(137, 23, 'Accessed dashboard', '2025-04-21 14:41:15'),
(138, 23, 'Accessed dashboard', '2025-04-21 14:41:15'),
(139, 23, 'Accessed dashboard', '2025-04-21 14:41:16'),
(140, 23, 'Accessed dashboard', '2025-04-21 14:41:16'),
(141, 23, 'Accessed dashboard', '2025-04-21 14:41:16'),
(142, 23, 'Accessed dashboard', '2025-04-21 14:41:16'),
(143, 23, 'Accessed dashboard', '2025-04-21 14:41:16'),
(144, 23, 'Accessed dashboard', '2025-04-21 14:41:17'),
(145, 23, 'Accessed dashboard', '2025-04-21 14:41:17'),
(146, 23, 'Accessed dashboard', '2025-04-21 14:41:17'),
(147, 23, 'Accessed dashboard', '2025-04-21 14:41:17'),
(148, 23, 'Accessed dashboard', '2025-04-21 14:41:49'),
(149, 23, 'Accessed dashboard', '2025-04-21 14:42:40'),
(150, 23, 'Accessed dashboard', '2025-04-21 14:42:58'),
(151, 23, 'Accessed dashboard', '2025-04-21 14:46:42'),
(152, 23, 'Accessed dashboard', '2025-04-21 14:46:52'),
(153, 23, 'Accessed dashboard', '2025-04-21 14:47:07'),
(154, 23, 'Attempted to take attendance beyond daily limit', '2025-04-21 14:48:00'),
(155, 23, 'Accessed dashboard', '2025-04-21 14:48:30'),
(156, 23, 'Accessed dashboard', '2025-04-21 14:50:41'),
(157, 23, 'Accessed dashboard', '2025-04-21 14:51:01'),
(158, 23, 'Accessed dashboard', '2025-04-21 14:53:32'),
(159, 23, 'Accessed dashboard', '2025-04-21 14:53:54'),
(160, 42, 'Accessed dashboard', '2025-04-21 14:54:10'),
(161, 42, 'Took attendance for Session #1 - stage:4 software engineering-Artificial Intelligence (AI)  ()', '2025-04-21 14:54:32'),
(162, 42, 'Took attendance for Session #2 - stage:4 software engineering-Artificial Intelligence (AI)  ()', '2025-04-21 14:54:47'),
(163, 42, 'Accessed dashboard', '2025-04-21 14:54:48'),
(164, 42, 'Accessed dashboard', '2025-04-21 14:55:39'),
(165, 42, 'Accessed dashboard', '2025-04-21 14:56:14'),
(166, 42, 'Took attendance for Session #1 - stage:4 software engineering-Artificial Intelligence (AI)  ()', '2025-04-21 14:56:37'),
(167, 42, 'Took attendance for Session #2 - stage:4 software engineering-Artificial Intelligence (AI)  ()', '2025-04-21 14:56:44'),
(168, 42, 'Accessed dashboard', '2025-04-21 14:56:46'),
(169, 42, 'Accessed dashboard', '2025-04-21 14:56:57'),
(170, 42, 'Attempted to take attendance beyond daily limit', '2025-04-21 14:57:23'),
(171, 42, 'Accessed dashboard', '2025-04-21 14:57:24'),
(172, 28, 'Accessed dashboard', '2025-04-21 14:57:37'),
(173, 28, 'Took attendance for Session #1 - 1-A-Software Engineering 2 ()', '2025-04-21 14:57:44'),
(174, 28, 'Took attendance for Session #2 - 1-A-Software Engineering 2 ()', '2025-04-21 14:57:49'),
(175, 28, 'Accessed dashboard', '2025-04-21 14:57:50'),
(176, 28, 'Took attendance for Session #1 - 1-A-Web Technology ()', '2025-04-21 14:57:55'),
(177, 28, 'Accessed dashboard', '2025-04-21 14:57:56'),
(178, 28, 'Took attendance for Session #2 - 1-A-Web Technology ()', '2025-04-21 14:58:04'),
(179, 28, 'Accessed dashboard', '2025-04-21 14:58:05'),
(180, 28, 'Accessed dashboard', '2025-04-21 17:59:57'),
(181, 28, 'Accessed dashboard', '2025-04-21 18:01:02'),
(182, 28, 'Accessed dashboard', '2025-04-21 18:01:14'),
(183, 28, 'Accessed dashboard', '2025-04-21 18:01:28'),
(184, 23, 'Accessed dashboard', '2025-04-21 18:04:24'),
(185, 42, 'Accessed dashboard', '2025-04-21 18:55:58'),
(186, 42, 'Accessed dashboard', '2025-04-21 18:59:01'),
(187, 42, 'Accessed dashboard', '2025-04-21 19:02:53'),
(188, 28, 'Accessed dashboard', '2025-04-23 12:58:57'),
(189, 28, 'Took attendance for Session #1 - 1-A-Software Engineering 2 ()', '2025-04-23 12:59:05'),
(190, 28, 'Took attendance for Session #2 - 1-A-Software Engineering 2 ()', '2025-04-23 12:59:10'),
(191, 28, 'Attempted to take attendance beyond daily limit', '2025-04-23 12:59:14'),
(192, 28, 'Accessed dashboard', '2025-04-23 12:59:16'),
(193, 28, 'Took attendance for Session #1 - 1-A-Web Technology ()', '2025-04-23 12:59:21'),
(194, 28, 'Took attendance for Session #2 - 1-A-Web Technology ()', '2025-04-23 12:59:22'),
(195, 28, 'Accessed dashboard', '2025-04-23 12:59:24'),
(196, 28, 'Accessed dashboard', '2025-04-23 12:59:55'),
(197, 28, 'Accessed dashboard', '2025-04-23 13:00:37'),
(198, 28, 'Accessed dashboard', '2025-04-23 13:01:39'),
(199, 42, 'Accessed dashboard', '2025-04-24 07:50:37'),
(200, 42, 'Accessed dashboard', '2025-04-24 07:50:45'),
(201, 23, 'Accessed dashboard', '2025-05-02 07:58:27'),
(202, 23, 'Took attendance for Session #1 - Eight-1 ()', '2025-05-02 07:58:35'),
(203, 23, 'Took attendance for Session #2 - Eight-1 ()', '2025-05-02 07:58:41'),
(204, 23, 'Attempted to take attendance beyond daily limit', '2025-05-02 07:58:45'),
(205, 23, 'Accessed dashboard', '2025-05-02 07:59:04'),
(206, 23, 'Accessed dashboard', '2025-05-02 07:59:48'),
(207, 28, 'Accessed dashboard', '2025-05-02 08:00:40'),
(208, 28, 'Took attendance for Session #1 - 1-A-Software Engineering 2 ()', '2025-05-02 08:00:47'),
(209, 28, 'Accessed dashboard', '2025-05-02 08:00:53'),
(210, 28, 'Took attendance for Session #1 - 1-A-Web Technology ()', '2025-05-02 08:00:58'),
(211, 28, 'Accessed dashboard', '2025-05-02 08:00:59'),
(212, 28, 'Accessed dashboard', '2025-05-02 08:01:24'),
(213, 28, 'Accessed dashboard', '2025-05-02 08:02:32'),
(214, 28, 'Took attendance for Session #2 - 1-A-Software Engineering 2 ()', '2025-05-02 08:02:41'),
(215, 28, 'Accessed dashboard', '2025-05-02 08:02:43'),
(216, 28, 'Took attendance for Session #2 - 1-A-Web Technology ()', '2025-05-02 08:02:47'),
(217, 28, 'Accessed dashboard', '2025-05-02 08:02:48'),
(218, 28, 'Attempted to take attendance beyond daily limit', '2025-05-02 08:02:52'),
(219, 28, 'Accessed dashboard', '2025-05-02 08:03:13'),
(220, 23, 'Accessed dashboard', '2025-05-02 11:33:01'),
(221, 23, 'Took attendance for Session #1 - Eight-1 ()', '2025-05-02 11:33:09'),
(222, 23, 'Took attendance for Session #2 - Eight-1 ()', '2025-05-02 11:33:13'),
(223, 28, 'Accessed dashboard', '2025-05-02 11:33:27'),
(224, 28, 'Took attendance for Session #1 - 1-A-Software Engineering 2 ()', '2025-05-02 11:33:31'),
(225, 28, 'Took attendance for Session #2 - 1-A-Software Engineering 2 ()', '2025-05-02 11:33:33'),
(226, 28, 'Accessed dashboard', '2025-05-02 11:33:34'),
(227, 28, 'Took attendance for Session #1 - 1-A-Web Technology ()', '2025-05-02 11:33:40'),
(228, 28, 'Accessed dashboard', '2025-05-02 11:33:41'),
(229, 42, 'Accessed dashboard', '2025-05-02 11:33:52'),
(230, 42, 'Took attendance for Session #1 - stage:4 software engineering-Artificial Intelligence (AI)  ()', '2025-05-02 11:34:04'),
(231, 42, 'Took attendance for Session #2 - stage:4 software engineering-Artificial Intelligence (AI)  ()', '2025-05-02 11:34:14'),
(232, 42, 'Accessed dashboard', '2025-05-02 11:34:18'),
(233, 23, 'Accessed dashboard', '2025-05-02 12:22:59'),
(234, 23, 'Accessed dashboard', '2025-05-02 12:23:19'),
(235, 45, 'Accessed dashboard', '2025-05-02 12:36:37'),
(236, 45, 'Accessed dashboard', '2025-05-02 12:36:45'),
(237, 45, 'Took attendance for Session #1 - 1-A-Real Time software engineering ()', '2025-05-02 12:36:54'),
(238, 45, 'Accessed dashboard', '2025-05-02 12:36:56'),
(239, 45, 'Accessed dashboard', '2025-05-02 12:37:00'),
(240, 28, 'Accessed dashboard', '2025-05-02 12:37:25'),
(241, 28, 'Accessed dashboard', '2025-05-02 12:37:30'),
(242, 28, 'Attempted to take attendance beyond daily limit', '2025-05-02 12:37:36'),
(243, 28, 'Accessed dashboard', '2025-05-02 12:37:38'),
(244, 28, 'Took attendance for Session #2 - 1-A-Web Technology ()', '2025-05-02 12:37:44'),
(245, 28, 'Accessed dashboard', '2025-05-02 12:37:45'),
(246, 23, 'Accessed dashboard', '2025-05-02 12:39:41'),
(247, 23, 'Accessed dashboard', '2025-05-06 06:05:35'),
(248, 23, 'Accessed dashboard', '2025-05-06 06:10:26'),
(249, 23, 'Took attendance for Session #1 - Eight-1 ()', '2025-05-06 06:10:45'),
(250, 23, 'Took attendance for Session #2 - Eight-1 ()', '2025-05-06 06:10:49'),
(251, 23, 'Attempted to take attendance beyond daily limit', '2025-05-06 06:10:53'),
(252, 23, 'Accessed dashboard', '2025-05-06 06:10:55'),
(253, 23, 'Accessed dashboard', '2025-05-06 06:12:05'),
(254, 28, 'Accessed dashboard', '2025-05-06 06:12:36'),
(255, 23, 'Accessed dashboard', '2025-05-06 06:15:05'),
(256, 23, 'Accessed dashboard', '2025-05-06 06:18:16'),
(257, 23, 'Accessed dashboard', '2025-05-06 06:21:59'),
(258, 23, 'Accessed dashboard', '2025-05-06 06:28:39'),
(259, 45, 'Accessed dashboard', '2025-05-06 06:37:43'),
(260, 45, 'Accessed dashboard', '2025-05-06 06:37:59'),
(261, 45, 'Accessed dashboard', '2025-05-06 06:38:04'),
(262, 45, 'Took attendance for Session #1 - 1-A-Real Time software engineering ()', '2025-05-06 06:38:10'),
(263, 45, 'Took attendance for Session #2 - 1-A-Real Time software engineering ()', '2025-05-06 06:38:15'),
(264, 45, 'Accessed dashboard', '2025-05-06 06:38:17'),
(265, 45, 'Accessed dashboard', '2025-05-06 06:39:17'),
(266, 45, 'Accessed dashboard', '2025-05-06 06:39:36'),
(267, 45, 'Accessed dashboard', '2025-05-06 06:39:50'),
(284, 48, 'Accessed dashboard', '2025-05-06 07:19:27'),
(285, 48, 'Attempted to take attendance beyond daily limit', '2025-05-06 07:19:34'),
(286, 48, 'Accessed dashboard', '2025-05-06 07:19:35'),
(287, 49, 'Accessed dashboard', '2025-05-06 10:26:42'),
(288, 49, 'Took attendance for Session #1 - stage 4 group B-Software quality Asurance ()', '2025-05-06 10:27:17'),
(289, 49, 'Took attendance for Session #2 - stage 4 group B-Software quality Asurance ()', '2025-05-06 10:27:27');

-- --------------------------------------------------------

--
-- Table structure for table `tblterm`
--

CREATE TABLE `tblterm` (
  `Id` int(10) NOT NULL,
  `termName` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblterm`
--

INSERT INTO `tblterm` (`Id`, `termName`) VALUES
(1, 'First'),
(2, 'Second'),
(3, 'Third');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_classes`
--

CREATE TABLE `teacher_classes` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `class_arm_id` int(11) NOT NULL,
  `date_assigned` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_classes`
--

INSERT INTO `teacher_classes` (`id`, `teacher_id`, `class_id`, `class_arm_id`, `date_assigned`) VALUES
(37, 23, 3, 16, '2025-04-19 22:13:06'),
(42, 42, 15, 25, '2025-04-20 18:05:06'),
(44, 45, 12, 21, '2025-05-02 15:35:28'),
(45, 45, 12, 18, '2025-05-02 15:35:28'),
(46, 45, 12, 22, '2025-05-02 15:35:28'),
(47, 28, 12, 17, '2025-05-02 15:36:07'),
(48, 28, 12, 19, '2025-05-02 15:36:07'),
(49, 28, 12, 20, '2025-05-02 15:36:07'),
(54, 48, 12, 15, '2025-05-06 10:19:04'),
(55, 48, 3, 23, '2025-05-06 10:19:04'),
(56, 49, 16, 28, '2025-05-06 13:25:12');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_subjects`
--

CREATE TABLE `teacher_subjects` (
  `Id` int(10) NOT NULL,
  `teacher_id` int(10) NOT NULL,
  `subject_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `tblattendance`
--
ALTER TABLE `tblattendance`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `idx_class_date_session` (`classId`,`classArmId`,`dateTimeTaken`,`sessionNumber`),
  ADD KEY `idx_class_date` (`classId`,`classArmId`,`dateTimeTaken`),
  ADD KEY `idx_admission` (`admissionNo`);

--
-- Indexes for table `tblclass`
--
ALTER TABLE `tblclass`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `tblclassarms`
--
ALTER TABLE `tblclassarms`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `tblclassteacher`
--
ALTER TABLE `tblclassteacher`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `tbllecturetimes`
--
ALTER TABLE `tbllecturetimes`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `tblmessages`
--
ALTER TABLE `tblmessages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_receiver` (`receiverId`,`receiverType`),
  ADD KEY `idx_sender` (`senderId`,`senderType`);

--
-- Indexes for table `tblsessionterm`
--
ALTER TABLE `tblsessionterm`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `tblsettings`
--
ALTER TABLE `tblsettings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblstudents`
--
ALTER TABLE `tblstudents`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `tblsubjects`
--
ALTER TABLE `tblsubjects`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `tblteacherlogs`
--
ALTER TABLE `tblteacherlogs`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `teacher_log_idx` (`teacherId`);

--
-- Indexes for table `tblterm`
--
ALTER TABLE `tblterm`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `teacher_classes`
--
ALTER TABLE `teacher_classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_teacher_class` (`teacher_id`,`class_id`,`class_arm_id`),
  ADD KEY `idx_teacher` (`teacher_id`),
  ADD KEY `idx_class` (`class_id`),
  ADD KEY `idx_class_arm` (`class_arm_id`);

--
-- Indexes for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbladmin`
--
ALTER TABLE `tbladmin`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblattendance`
--
ALTER TABLE `tblattendance`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=844;

--
-- AUTO_INCREMENT for table `tblclass`
--
ALTER TABLE `tblclass`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tblclassarms`
--
ALTER TABLE `tblclassarms`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `tblclassteacher`
--
ALTER TABLE `tblclassteacher`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `tbllecturetimes`
--
ALTER TABLE `tbllecturetimes`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblmessages`
--
ALTER TABLE `tblmessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `tblsessionterm`
--
ALTER TABLE `tblsessionterm`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tblsettings`
--
ALTER TABLE `tblsettings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblstudents`
--
ALTER TABLE `tblstudents`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `tblsubjects`
--
ALTER TABLE `tblsubjects`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblteacherlogs`
--
ALTER TABLE `tblteacherlogs`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=290;

--
-- AUTO_INCREMENT for table `tblterm`
--
ALTER TABLE `tblterm`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `teacher_classes`
--
ALTER TABLE `teacher_classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblteacherlogs`
--
ALTER TABLE `tblteacherlogs`
  ADD CONSTRAINT `teacher_log_fk` FOREIGN KEY (`teacherId`) REFERENCES `tblclassteacher` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  ADD CONSTRAINT `teacher_subjects_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `tblclassteacher` (`Id`),
  ADD CONSTRAINT `teacher_subjects_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `tblsubjects` (`Id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
