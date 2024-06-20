-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 20, 2024 at 11:19 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!40101 SET NAMES utf8mb4 */
;
--
-- Database: `bindtogether`
--

-- --------------------------------------------------------
--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `ActivityID` int(11) NOT NULL,
  `AdminID` int(100) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Content` text NOT NULL,
  `Image` varchar(255) NOT NULL,
  `Category` varchar(100) NOT NULL,
  `Venue` varchar(255) NOT NULL,
  `Address` varchar(255) NOT NULL,
  `Date` date NOT NULL,
  `Time` time NOT NULL,
  `Status` varchar(100) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (
    `ActivityID`,
    `AdminID`,
    `Title`,
    `Content`,
    `Image`,
    `Category`,
    `Venue`,
    `Address`,
    `Date`,
    `Time`,
    `Status`
  )
VALUES (
    6,
    25,
    'TRYOUTS',
    'dvsfbtjntdymtxfxdhtjkul,uyfj',
    '../activityPictures/dancesport.jpg',
    'Sports Tryouts',
    'BPSU- MAIN Campus',
    'Balanga City, Bataan',
    '2024-05-20',
    '14:30:00',
    'Active'
  ),
  (
    7,
    28,
    'Organization',
    'hnjjryjmykulgyiv,nvksljvns;vk',
    '../activityPictures/auditions in sports.png',
    'Performers Auditions',
    'BPSU Main Campus',
    'Balanga CIty, Bataan',
    '2024-07-06',
    '13:47:00',
    'Active'
  );
-- --------------------------------------------------------
--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `AdminID` int(100) NOT NULL,
  `Fname` varchar(255) NOT NULL,
  `Mname` varchar(255) NOT NULL,
  `Lname` varchar(255) NOT NULL,
  `BirthDate` date NOT NULL,
  `PhoneNum` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Classification` varchar(255) NOT NULL,
  `Role` varchar(255) NOT NULL,
  `Gender` enum('male', 'female', '', '') NOT NULL,
  `Campus` varchar(255) NOT NULL,
  `Avatar` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Code` mediumint(50) NOT NULL,
  `Status` enum('verified', 'not_verified', '', '') NOT NULL,
  `Active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (
    `AdminID`,
    `Fname`,
    `Mname`,
    `Lname`,
    `BirthDate`,
    `PhoneNum`,
    `Email`,
    `Classification`,
    `Role`,
    `Gender`,
    `Campus`,
    `Avatar`,
    `Password`,
    `Code`,
    `Status`,
    `Active`
  )
VALUES (
    24,
    'Edgar',
    'Zara',
    'Tesoro',
    '2003-05-20',
    '09665478552',
    'eztesoro@bpsu.edu.ph',
    '',
    'SuperAdmin',
    'male',
    'Balanga Campus',
    '../upload/default_avatar.jpg',
    '$2y$10$8ScElNNOnbyMlHVESpBeU.EyliB8.A01SKXP0liEKkaPEbfhq0Zy6',
    0,
    'verified',
    1
  ),
  (
    25,
    'Jenifer',
    'Cinco',
    'Carandang',
    '2002-08-27',
    '09665478552',
    'dcramos@bpsu.edu.ph',
    '',
    'Sports Director',
    'female',
    'Main Campus',
    'admin_25_1717927634.jpg',
    '$2y$10$9Ezdc0pvKcSFwJQ.sS1HaesQgxQf8eW2x5nAqy9FQYgiPZ9TbEn1S',
    0,
    'verified',
    1
  ),
  (
    29,
    'leah',
    'Jean',
    'Magdato',
    '2003-06-20',
    '09953213216',
    'lmagdato@bpsu.edu.ph',
    'Volleyball',
    'Coach in Sports',
    'female',
    'Balanga Campus',
    '../upload/default_avatar.jpg',
    '$2y$10$Q1NlDyplX5LR0qFa7MAjfujbLfADM/pS4ABnNqY5wXL1G8OgI6c16',
    0,
    'verified',
    1
  ),
  (
    30,
    'Edwin Jon',
    '',
    'Hontiveros',
    '2002-10-17',
    '09052713896',
    'ejhontiveros@bpsu.edu.ph',
    '',
    'Performers and Artists Director',
    'male',
    'Main Campus',
    'admin_30_1718873766.png',
    '$2y$10$btXBDlC82NtJ5pStxnGjNu/8n84p7w0USvI0n4UPK3hs0bU4bt67C',
    337746,
    '',
    1
  );
-- --------------------------------------------------------
--
-- Table structure for table `calendar`
--

CREATE TABLE `calendar` (
  `EventID` int(11) NOT NULL,
  `EventName` varchar(255) DEFAULT NULL,
  `StartDate` date DEFAULT NULL,
  `StartTime` time NOT NULL,
  `EndDate` date DEFAULT NULL,
  `EndTime` time NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
-- --------------------------------------------------------
--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `CategoryID` int(100) NOT NULL,
  `Category` varchar(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`CategoryID`, `Category`)
VALUES (8, 'Event'),
  (9, 'Singing'),
  (10, 'Dancing'),
  (11, 'Announcement'),
  (14, 'Basketball'),
  (15, 'Volleyball');
-- --------------------------------------------------------
--
-- Table structure for table `chatbox`
--

CREATE TABLE `chatbox` (
  `ChatBoxID` int(50) NOT NULL,
  `UserID` int(50) NOT NULL,
  `Message` varchar(500) NOT NULL,
  `Fname` varchar(255) NOT NULL,
  `Lname` varchar(255) NOT NULL,
  `Avatar` varchar(200) NOT NULL,
  `Status` varchar(255) NOT NULL,
  `TimeStamp` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
-- --------------------------------------------------------
--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `CommentID` int(100) NOT NULL,
  `PostID` int(100) NOT NULL,
  `UserID` int(100) DEFAULT NULL,
  `AdminID` int(100) DEFAULT NULL,
  `Fname` varchar(255) NOT NULL,
  `Lname` varchar(255) NOT NULL,
  `Comment` varchar(255) NOT NULL,
  `Date` date NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (
    `CommentID`,
    `PostID`,
    `UserID`,
    `AdminID`,
    `Fname`,
    `Lname`,
    `Comment`,
    `Date`
  )
VALUES (
    33,
    76,
    39,
    NULL,
    'Immanuel',
    'Pabiton',
    'congratss',
    '2024-06-09'
  ),
  (
    34,
    76,
    38,
    NULL,
    'Fresally',
    'Manalo',
    'may pangit dun sa gitna',
    '2024-06-10'
  ),
  (
    35,
    85,
    43,
    NULL,
    'Merryliza',
    'Pabiton',
    'pangit',
    '2024-06-10'
  );
-- --------------------------------------------------------
--
-- Table structure for table `eventform`
--

CREATE TABLE `eventform` (
  `EventFormID` int(100) NOT NULL,
  `ActivityID` int(11) NOT NULL,
  `UserID` int(100) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `StudNum` varchar(255) NOT NULL,
  `ContactNum` varchar(255) NOT NULL,
  `YearLevel` varchar(255) NOT NULL,
  `Program` varchar(255) NOT NULL,
  `College` varchar(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
-- --------------------------------------------------------
--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `FeedBackID` int(50) NOT NULL,
  `UserID` int(50) NOT NULL,
  `Fname` varchar(255) NOT NULL,
  `Lname` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Subject` varchar(255) NOT NULL,
  `Message` varchar(500) NOT NULL,
  `TimeStamp` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (
    `FeedBackID`,
    `UserID`,
    `Fname`,
    `Lname`,
    `Email`,
    `Subject`,
    `Message`,
    `TimeStamp`
  )
VALUES (
    4,
    43,
    'Merryliza',
    'Pabiton',
    'mtpabiton@bpsu.edu.ph',
    'Concern',
    'improve the system',
    '2024-06-10 03:51:44.686895'
  );
-- --------------------------------------------------------
--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `LikeID` int(100) NOT NULL,
  `AdminID` int(255) DEFAULT NULL,
  `UserID` int(100) DEFAULT NULL,
  `PostID` int(100) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`LikeID`, `AdminID`, `UserID`, `PostID`)
VALUES (118, NULL, 41, 76),
  (119, NULL, 41, 81),
  (120, NULL, 41, 82),
  (121, NULL, 41, 77),
  (122, NULL, 41, 78),
  (124, NULL, 43, 85);
-- --------------------------------------------------------
--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `PostID` int(50) NOT NULL,
  `AdminID` int(50) NOT NULL,
  `Avatar` varchar(255) NOT NULL,
  `Fname` varchar(255) NOT NULL,
  `Lname` varchar(255) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Content` varchar(255) NOT NULL,
  `Category` varchar(255) NOT NULL,
  `MediaType` enum('text', 'image', 'video', '') NOT NULL,
  `MediaURL` text NOT NULL,
  `Date` date NOT NULL,
  `Status` varchar(100) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (
    `PostID`,
    `AdminID`,
    `Avatar`,
    `Fname`,
    `Lname`,
    `Title`,
    `Content`,
    `Category`,
    `MediaType`,
    `MediaURL`,
    `Date`,
    `Status`
  )
VALUES (
    76,
    26,
    '../upload/default_avatar.jpg',
    'Merryliza',
    'Pabiton',
    'Announcement',
    'Congratulations and thank you BPSU Student Athletes, Coaches, Trainers and Sports Council, for bringing honor and glory to our University!',
    'Announcement',
    'image',
    '../uploaded_media/photo1717855670 (1).jpeg',
    '2024-06-09',
    'Active'
  ),
  (
    77,
    26,
    '../upload/default_avatar.jpg',
    'Merryliza',
    'Pabiton',
    'Announcement for Sports',
    'Announcement Treyd! \r\nGAME SCHEDULE FOR TAGISAN SA TREYD DAY 4 (MARCH 21, 2024)',
    'Event',
    'image',
    '../uploaded_media/photo1717855428.jpeg',
    '2024-06-09',
    'Active'
  ),
  (
    78,
    26,
    '../upload/default_avatar.jpg',
    'Merryliza',
    'Pabiton',
    'Conversion Week',
    '𝕯𝖆𝖗𝖊 𝖙𝖔 𝖇𝖊 𝖑𝖊𝖌𝖊𝖓𝖉𝖆𝖗𝖞, 𝕻𝖍𝖊𝖓𝖔𝖒𝖊𝖓𝖆𝖑 𝕯𝖗𝖆𝖌𝖔𝖓𝖘! \r\nUnleash the fire within and get ready to compete at 𝙪𝙥𝙘𝙤𝙢𝙞𝙣𝙜 𝙩𝙧𝙮𝙤𝙪𝙩𝙨 𝙛𝙤𝙧 𝘾𝙤𝙣𝙫𝙚𝙧𝙨𝙞𝙤𝙣 𝙒𝙚𝙚𝙠 2024. Are you ready to embrace greatness? \r\nCollege of Technology is currently looking for an eager phenomenal players for',
    'Event',
    'image',
    '../uploaded_media/422586407_347574874846810_7472426185507332749_n.jpg',
    '2024-06-09',
    'Active'
  ),
  (
    81,
    27,
    '../upload/KOLORO_1645334335213[254].jpg',
    'Leah ',
    'Magdato',
    'Performers Arts',
    'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor i',
    'Event',
    'image',
    'performers.jpeg',
    '2024-06-09',
    'Active'
  ),
  (
    82,
    27,
    '../upload/KOLORO_1645334335213[254].jpg',
    'Leah ',
    'Magdato',
    '𝐒𝐢𝐧𝐢𝐧𝐠 𝐚𝐭 𝐊𝐨𝐦𝐮𝐧𝐢𝐤𝐚𝐬𝐲𝐨𝐧 𝐏𝐞𝐫𝐟𝐨𝐫𝐦𝐢𝐧𝐠 𝐀𝐫𝐭𝐬 ',
    '𝙁𝙪𝙡𝙡-𝙥𝙖𝙘𝙠𝙖𝙜𝙚 𝙏𝙧𝙚𝙮𝙙 𝙁𝙚𝙨𝙩!!!🤩 \r\nHello Peninsulares! Join our holiday cheer with BPSU 𝐒𝐢𝐧𝐢𝐧𝐠 𝐚𝐭 𝐊𝐨𝐦𝐮𝐧𝐢𝐤𝐚𝐬𝐲𝐨𝐧 𝐏𝐞𝐫𝐟𝐨𝐫𝐦𝐢𝐧𝐠 𝐀𝐫𝐭𝐬 𝐓𝐞𝐚𝐦 in a short musical play! Where festive performances, dance, and song came together for a joyous and fun-filled celebration! \r\nKa',
    'Event',
    'image',
    '../uploaded_media/photo1717855870.jpeg',
    '2024-06-09',
    'Active'
  ),
  (
    84,
    27,
    '../upload/KOLORO_1645334335213[254].jpg',
    'Leah ',
    'Magdato',
    'UNiwide',
    'cdsdvfbdngnfgmghg,hj,jfg',
    'Dancing',
    'video',
    '../uploaded_media/dancesport.jpg',
    '2024-06-10',
    'Deactivated'
  ),
  (
    85,
    28,
    '',
    'Edwin',
    'Hontiveros',
    'dfsdlkgdbb',
    'srtjsrtjryjyrjy',
    'Dancing',
    'image',
    '../uploaded_media/praktis in performing arts.png',
    '2024-06-10',
    'Active'
  ),
  (
    86,
    30,
    '',
    'Edwin Jon',
    'Hontiveros',
    'bhem',
    'bhem',
    'Announcement',
    'image',
    '../uploaded_media/bhemwp.jpg',
    '2024-06-20',
    'Active'
  ),
  (
    87,
    30,
    '',
    'Edwin Jon',
    'Hontiveros',
    'bhem2',
    'bhem2',
    'Announcement',
    'image',
    '../uploaded_media/curve.jpg',
    '2024-06-20',
    'Active'
  );
-- --------------------------------------------------------
--
-- Table structure for table `reminders`
--

CREATE TABLE `reminders` (
  `ReminderID` int(11) NOT NULL,
  `EventID` int(11) NOT NULL,
  `Email` varchar(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
-- --------------------------------------------------------
--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `ReportID` int(11) NOT NULL,
  `CommentID` int(11) NOT NULL,
  `ReportReason` varchar(255) NOT NULL,
  `OtherReason` text DEFAULT NULL,
  `ReportDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (
    `ReportID`,
    `CommentID`,
    `ReportReason`,
    `OtherReason`,
    `ReportDate`
  )
VALUES (6, 32, 'Spam', '', '2024-06-09 21:49:00'),
  (
    7,
    33,
    'False Information',
    '',
    '2024-06-09 21:55:12'
  ),
  (
    8,
    35,
    'Other',
    'not good',
    '2024-06-10 03:50:02'
  );
-- --------------------------------------------------------
--
-- Table structure for table `sms`
--

CREATE TABLE `sms` (
  `MessageID` int(200) NOT NULL,
  `UserID` int(200) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `AccountID` int(200) NOT NULL,
  `AccountName` varchar(255) NOT NULL,
  `PhoneNum` varchar(255) NOT NULL,
  `Message` varchar(255) NOT NULL,
  `SenderName` varchar(255) NOT NULL,
  `Network` varchar(255) NOT NULL,
  `Status` varchar(255) NOT NULL,
  `Type` varchar(255) NOT NULL,
  `Source` varchar(255) NOT NULL,
  `CreatedAt` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  `UpdatedAt` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
-- --------------------------------------------------------
--
-- Table structure for table `tryouts`
--

CREATE TABLE `tryouts` (
  `TryOutID` int(11) NOT NULL,
  `ActivityID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `StudNum` varchar(20) NOT NULL,
  `ContactNum` varchar(20) NOT NULL,
  `YearLevel` enum(
    'First Year',
    'Second Year',
    'Third Year',
    'Fourth Year'
  ) NOT NULL,
  `Program` varchar(100) NOT NULL,
  `College` varchar(100) NOT NULL,
  `Gender` enum('Male', 'Female', 'LGBTQIA+', 'Prefer not to say') NOT NULL,
  `Address` text NOT NULL,
  `FatherName` varchar(100) NOT NULL,
  `MotherName` varchar(100) NOT NULL,
  `ParentContactNum` varchar(20) NOT NULL,
  `DateOfBirth` date NOT NULL,
  `Height` varchar(255) NOT NULL,
  `Weight` varchar(200) NOT NULL,
  `ApprovalStatus` varchar(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `tryouts`
--

INSERT INTO `tryouts` (
    `TryOutID`,
    `ActivityID`,
    `UserID`,
    `Name`,
    `Email`,
    `StudNum`,
    `ContactNum`,
    `YearLevel`,
    `Program`,
    `College`,
    `Gender`,
    `Address`,
    `FatherName`,
    `MotherName`,
    `ParentContactNum`,
    `DateOfBirth`,
    `Height`,
    `Weight`,
    `ApprovalStatus`
  )
VALUES (
    5,
    6,
    43,
    'Merryliza Pabiton',
    'mtpabiton@bpsu.edu.ph',
    '21-01485',
    '09953213216',
    'Third Year',
    'Bachelor of Science in Information Technology',
    'CICT',
    'Female',
    'Samal, Bataan',
    'Allan Pabiton',
    'Angie Pabiton',
    '09953213216',
    '2003-06-24',
    '155',
    '50',
    'Approved'
  );
-- --------------------------------------------------------
--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(50) NOT NULL,
  `Fname` varchar(255) NOT NULL,
  `Mname` varchar(255) NOT NULL,
  `Lname` varchar(255) NOT NULL,
  `BirthDate` date NOT NULL,
  `PhoneNum` varchar(50) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Classification` varchar(255) NOT NULL,
  `Role` varchar(255) NOT NULL,
  `Gender` enum('male', 'female', '', '') NOT NULL,
  `Campus` varchar(255) NOT NULL,
  `Avatar` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Code` mediumint(50) NOT NULL,
  `Status` enum('verified', 'not_verified', '', '') NOT NULL,
  `Active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `users`
--

INSERT INTO `users` (
    `UserID`,
    `Fname`,
    `Mname`,
    `Lname`,
    `BirthDate`,
    `PhoneNum`,
    `Email`,
    `Classification`,
    `Role`,
    `Gender`,
    `Campus`,
    `Avatar`,
    `Password`,
    `Code`,
    `Status`,
    `Active`
  )
VALUES (
    38,
    'Fresally',
    'Jorda',
    'Manalo',
    '2003-05-20',
    '09694657610',
    'fjmanalo@bpsu.edu.ph',
    'Volleyball',
    'Athletes',
    'female',
    'Balanga Campus',
    '../upload/default_avatar.jpg',
    '$2y$10$CywLzbImQfSCvfHaS2vrKuNAT0ofNkPDg43ZpEo24wFscFtYaNiGa',
    0,
    'verified',
    1
  ),
  (
    39,
    'Immanuel',
    'Jorda',
    'Pabiton',
    '2003-06-07',
    '09694657610',
    'dipbonaobra@bpsu.edu.ph',
    'Basketball',
    'Athletes',
    'male',
    'Balanga Campus',
    '../upload/Vanellope.png',
    '$2y$10$404lcgkPhmdDhPm7aZiXeu.pgRY4JbiU0yovhU70GJ3VEEjFVFppi',
    0,
    'verified',
    1
  ),
  (
    40,
    'Alice',
    'Bamban',
    'Guo',
    '2004-02-01',
    '09472586312',
    'mgpcaudilla@bpsu.edu.ph',
    'Singing',
    'Performers and Artists',
    'female',
    'Main Campus',
    '../upload/Dannel2.jpg',
    '$2y$10$LqM.EaRqhgveHghhBD7WROxj55V/ZScZqZa6zGg1MhH0FsMdksyUi',
    0,
    'verified',
    1
  ),
  (
    41,
    'Joshua',
    'Ivan',
    'Reyes',
    '2003-04-20',
    '0965215215',
    'jifreyes@bpsu.edu.ph',
    'Dancing',
    'Performers and Artists',
    'male',
    'Balanga Campus',
    '../upload/default_avatar.jpg',
    '$2y$10$wzyqUEYkOlkE/g.vqvWTu.v3BwoUGY8j3hvpRrpVw6LK.8wS93x8K',
    0,
    'verified',
    1
  ),
  (
    43,
    'Merryliza',
    'Trompeta',
    'Pabiton',
    '2003-01-06',
    '099532',
    'mtpabiton@bpsu.edu.ph',
    'Volleyball',
    'Athletes',
    'female',
    'Balanga Campus',
    '../upload/default_avatar.jpg',
    '$2y$10$dtylVgkE39mt9Y.AW8f.YeugkAYMoR3m.gp04PtxGKwFXe0Epdi4a',
    0,
    'verified',
    1
  ),
  (
    45,
    'Edwin Jon',
    '',
    'Hontiveros',
    '2002-10-17',
    '09052713896',
    'ejhontiveros@bpsu.edu.ph',
    '',
    '',
    'male',
    '',
    'user_45_1718870421.jpg',
    '$2y$10$Gbse09A0sbhHS3J8QQobquVAHZVnfgcvuVioe8ieYjx.LhBbFCdC6',
    730484,
    '',
    1
  );
--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
ADD PRIMARY KEY (`ActivityID`);
--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
ADD PRIMARY KEY (`AdminID`);
--
-- Indexes for table `calendar`
--
ALTER TABLE `calendar`
ADD PRIMARY KEY (`EventID`);
--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
ADD PRIMARY KEY (`CategoryID`);
--
-- Indexes for table `chatbox`
--
ALTER TABLE `chatbox`
ADD PRIMARY KEY (`ChatBoxID`);
--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
ADD PRIMARY KEY (`CommentID`);
--
-- Indexes for table `eventform`
--
ALTER TABLE `eventform`
ADD PRIMARY KEY (`EventFormID`);
--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
ADD PRIMARY KEY (`FeedBackID`);
--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
ADD PRIMARY KEY (`LikeID`);
--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
ADD PRIMARY KEY (`PostID`);
--
-- Indexes for table `reminders`
--
ALTER TABLE `reminders`
ADD PRIMARY KEY (`ReminderID`),
  ADD KEY `fk_EventID` (`EventID`);
--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
ADD PRIMARY KEY (`ReportID`);
--
-- Indexes for table `sms`
--
ALTER TABLE `sms`
ADD PRIMARY KEY (`MessageID`);
--
-- Indexes for table `tryouts`
--
ALTER TABLE `tryouts`
ADD PRIMARY KEY (`TryOutID`);
--
-- Indexes for table `users`
--
ALTER TABLE `users`
ADD PRIMARY KEY (`UserID`);
--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
MODIFY `ActivityID` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 8;
--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
MODIFY `AdminID` int(100) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 31;
--
-- AUTO_INCREMENT for table `calendar`
--
ALTER TABLE `calendar`
MODIFY `EventID` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 10;
--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
MODIFY `CategoryID` int(100) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 16;
--
-- AUTO_INCREMENT for table `chatbox`
--
ALTER TABLE `chatbox`
MODIFY `ChatBoxID` int(50) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
MODIFY `CommentID` int(100) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 36;
--
-- AUTO_INCREMENT for table `eventform`
--
ALTER TABLE `eventform`
MODIFY `EventFormID` int(100) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 9;
--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
MODIFY `FeedBackID` int(50) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 5;
--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
MODIFY `LikeID` int(100) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 125;
--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
MODIFY `PostID` int(50) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 88;
--
-- AUTO_INCREMENT for table `reminders`
--
ALTER TABLE `reminders`
MODIFY `ReminderID` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 5;
--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
MODIFY `ReportID` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 9;
--
-- AUTO_INCREMENT for table `tryouts`
--
ALTER TABLE `tryouts`
MODIFY `TryOutID` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 6;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `UserID` int(50) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 46;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `reminders`
--
ALTER TABLE `reminders`
ADD CONSTRAINT `fk_EventID` FOREIGN KEY (`EventID`) REFERENCES `calendar` (`EventID`) ON DELETE CASCADE,
  ADD CONSTRAINT `reminders_ibfk_1` FOREIGN KEY (`EventID`) REFERENCES `calendar` (`EventID`);
COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;