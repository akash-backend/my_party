-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 31, 2018 at 03:31 AM
-- Server version: 5.6.41-84.1
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ctinf0eg_myparty`
--

-- --------------------------------------------------------

--
-- Table structure for table `accept_reject_tbl`
--

CREATE TABLE `accept_reject_tbl` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` int(11) NOT NULL COMMENT '1 - accept,2 - reject',
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `percent` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `company`, `email`, `password`, `city`, `percent`) VALUES
(1, '', '', 'admin@admin.com', 'e10adc3949ba59abbe56e057f20f883e', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `category_tbl`
--

CREATE TABLE `category_tbl` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `category_image` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '0 - inactive , 1 -active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `category_tbl`
--

INSERT INTO `category_tbl` (`category_id`, `category_name`, `category_image`, `status`) VALUES
(1, 'Entertainer', 'entertainers.png', 1),
(2, 'restaurants', 'restaurants.png', 1),
(4, 'theater', 'theaters.png', 1),
(5, 'cake makers', 'cake-makers.png', 1),
(6, 'party zones', 'party-zones.png', 1),
(7, 'sports', 'sports.png', 1),
(8, 'Bouncy Castles', 'pubs.png', 1),
(9, 'cafes', 'cafes.png', 1),
(10, 'others', 'others.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `event_join_user`
--

CREATE TABLE `event_join_user` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` int(11) NOT NULL COMMENT '0 - pending,1- accept,2 -reject',
  `created_at` datetime NOT NULL,
  `event_owner_status` int(11) NOT NULL COMMENT ' 0 - user, 1 - owner '
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `event_join_user`
--

INSERT INTO `event_join_user` (`id`, `event_id`, `user_id`, `status`, `created_at`, `event_owner_status`) VALUES
(1, 32, 2, 0, '2018-12-21 13:50:03', 1);

-- --------------------------------------------------------

--
-- Table structure for table `event_tbl`
--

CREATE TABLE `event_tbl` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_latitude` double NOT NULL,
  `event_longitude` double NOT NULL,
  `event_venue` text NOT NULL,
  `event_description` text NOT NULL,
  `event_start_date` datetime NOT NULL,
  `event_end_date` datetime NOT NULL,
  `event_created_at` datetime NOT NULL,
  `visibility_status` int(11) NOT NULL DEFAULT '1' COMMENT '1 - visibility 2 -invisibility',
  `payment_status` int(11) NOT NULL DEFAULT '1' COMMENT '1 - unblock payment,2 -block payment',
  `event_live_status` int(11) NOT NULL DEFAULT '1' COMMENT '0 -  off , 1 - on',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '1 - unblock, 2 - block'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `event_tbl`
--

INSERT INTO `event_tbl` (`id`, `user_id`, `event_name`, `event_latitude`, `event_longitude`, `event_venue`, `event_description`, `event_start_date`, `event_end_date`, `event_created_at`, `visibility_status`, `payment_status`, `event_live_status`, `status`) VALUES
(32, 2, 'Christmas event ', 22.722755, 75.887043, '505, Shekhar Central, Palasia A.B. Road, Manorama Ganj, Indore, Madhya Pradesh 452001, India', 'Geggea eheehh heah hegae yyeh gagegea geaheah x ghrars hehgeh geggeg badhraha gggg fgg fggg ftg fgg frgg ', '2018-12-25 13:45:57', '2018-12-25 17:45:00', '2018-12-21 13:50:03', 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `featured_images`
--

CREATE TABLE `featured_images` (
  `id` int(11) NOT NULL,
  `featured_images` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `group_chat`
--

CREATE TABLE `group_chat` (
  `id` int(11) NOT NULL,
  `user_from` int(11) DEFAULT NULL,
  `group_id` int(11) NOT NULL,
  `message` text CHARACTER SET utf8mb4,
  `comment` text CHARACTER SET utf8mb4 NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `type` int(11) NOT NULL COMMENT '1 - text , 2 -image, 3 - image and comment ',
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `note_tbl`
--

CREATE TABLE `note_tbl` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_start_date` datetime NOT NULL,
  `event_end_date` datetime NOT NULL,
  `venue` text NOT NULL,
  `venue_lat` varchar(255) NOT NULL,
  `venue_lng` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `event_image` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '1 -active 0-inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `note_tbl`
--

INSERT INTO `note_tbl` (`id`, `user_id`, `event_name`, `event_start_date`, `event_end_date`, `venue`, `venue_lat`, `venue_lng`, `description`, `event_image`, `status`) VALUES
(1, 1, 'my first promotion ', '2018-12-25 15:45:00', '2018-12-25 18:00:00', 'Indore, Madhya Pradesh, India', '22.7195687', '75.8577258', 'Promotion event', '54f9072a7df563cb4a0bde867ef48a81.jpg', 1),
(2, 1, 'new promotion ', '2018-12-23 13:56:00', '2018-12-23 17:56:00', 'Bhopal, Madhya Pradesh, India', '23.2599333', '77.412615', 'After follow ad promotion for testing in my profile list', '24e0c9b8d0d70bb2a5425fb872911d98.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notification_tbl`
--

CREATE TABLE `notification_tbl` (
  `id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_send_from` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `type` int(11) NOT NULL COMMENT '1 - follow,2 -notes,3 - accept, 4 - reject,5 - gift send',
  `note_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notification_tbl`
--

INSERT INTO `notification_tbl` (`id`, `message`, `user_id`, `user_send_from`, `date`, `type`, `note_id`, `event_id`) VALUES
(1, 'Started Following You', 1, 2, '2018-12-21 13:51:18', 1, 0, 0),
(2, 'create a new note', 2, 1, '2018-12-21 14:00:18', 2, 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `txn_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `payment_gross` float(10,2) NOT NULL,
  `currency_code` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `payer_email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `payment_status` varchar(20) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `country_code` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `paypal_mail_id` varchar(255) NOT NULL,
  `stripe_mail_id` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `user_days` varchar(500) NOT NULL,
  `social_id` varchar(255) NOT NULL,
  `password` varchar(255) CHARACTER SET macce COLLATE macce_bin NOT NULL,
  `user_image` varchar(128) NOT NULL,
  `ios_token` varchar(256) NOT NULL,
  `android_token` varchar(256) NOT NULL,
  `created_at` datetime NOT NULL,
  `user_latitude` varchar(255) NOT NULL,
  `user_longitude` varchar(255) NOT NULL,
  `user_type` int(11) NOT NULL COMMENT 'user-1 ,provider- 2',
  `singup_type` int(11) NOT NULL COMMENT '1 - simple signup, 2 -social ogin',
  `banner_image` varchar(255) NOT NULL,
  `banner_title` varchar(255) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `category` int(11) NOT NULL,
  `status` int(11) NOT NULL COMMENT '0 = block, 1 = not block'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `first_name`, `last_name`, `country_code`, `email`, `mobile`, `description`, `paypal_mail_id`, `stripe_mail_id`, `address`, `start_time`, `end_time`, `user_days`, `social_id`, `password`, `user_image`, `ios_token`, `android_token`, `created_at`, `user_latitude`, `user_longitude`, `user_type`, `singup_type`, `banner_image`, `banner_title`, `business_name`, `category`, `status`) VALUES
(1, 'devendra', 'dharwar', 0, 'devendra@mailinator.com', '1234567890', 'test description ', '', '', 'Agra Bombay Rd, South Tukoganj, Indore, Madhya Pradesh 452001, India', '00:00:00', '00:00:00', 'Monday - Saturday', '', 'e10adc3949ba59abbe56e057f20f883e', '71fc7ad30b55195c5175f91bee19acb5.jpg', '6269cd995b5e69bb2db52ede6415de5c64849f3c87a3c1f69b9c76135d5f039c', '', '2018-12-21 13:42:58', '22.7232080344125', '75.8867157422025', 2, 1, '6be8c7d175e62dc275d045ef66abc2eb.jpg', '', 'food store', 2, 1),
(2, 'Nidhi', 'Patni', 91, 'ctnidhi10@gmail.com', '9406649589', 'addressing some description ', 'ankit-buyer@creativethoughtsinfo.com', '', 'Agra Bombay Rd, South Tukoganj, Indore, Madhya Pradesh 452001, India', '00:00:00', '00:00:00', '', '', 'e10adc3949ba59abbe56e057f20f883e', '', '690f74c0e8549d275a1061d816a03a5e125584cd3006a6dc63166468864ffd9c', '', '2018-12-21 13:48:54', '22.723145228906', '75.8868734974918', 1, 1, '', '', '', 0, 1),
(3, 'willo', 'king', 353, 'willoking@gmail.com', '862751155', '', '', '', '154 Grianan Fidh, Woodside, Dublin 18, D18 T228, Ireland', '00:00:00', '00:00:00', '', '', '4a30dbf8cc00a8292098d9c43a05523c', '', '0ee442f10b5bb8df3d5d76c6509ce2469ba1e5647d9a1fa4c0aa81a192cfcae4', '', '2018-12-21 19:32:40', '53.2631183871718', '-6.21929262477308', 1, 1, '', '', '', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_following`
--

CREATE TABLE `user_following` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `following_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_following`
--

INSERT INTO `user_following` (`id`, `user_id`, `following_id`) VALUES
(1, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_send_request`
--

CREATE TABLE `user_send_request` (
  `user_send_request_id` int(111) NOT NULL,
  `user_id` int(111) NOT NULL,
  `provider_id` int(111) NOT NULL,
  `service_offer_category_id` varchar(111) COLLATE utf8_unicode_ci NOT NULL,
  `service_offer_subcategory_id` varchar(111) COLLATE utf8_unicode_ci NOT NULL,
  `service_date` date NOT NULL,
  `service_time` time NOT NULL,
  `service_hours` time NOT NULL,
  `service_address` text COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(5555) COLLATE utf8_unicode_ci NOT NULL,
  `service_status_message` varchar(555) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(111) NOT NULL COMMENT '0= send request, 1 = list all , 2 = start, 3 = complete, 4= cancell, 5 = payment done',
  `start_working_hours` datetime NOT NULL,
  `end_working_hours` datetime NOT NULL,
  `user_view` int(11) NOT NULL,
  `provider_view` int(11) NOT NULL,
  `start_latitude` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `start_longitude` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `end_latitude` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `end_longitude` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `working_latitude` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `working_longitude` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pick_up_latitude` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pick_up_longitude` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `drop_latitude` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `drop_longitude` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `notification_status` int(11) NOT NULL COMMENT '0 - not send , 1 - send',
  `request_type` int(11) NOT NULL COMMENT '1 - fixed , 2 - deleviry_type1 - fixed , 2 - deleviry_type',
  `barcode_id` varchar(555) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accept_reject_tbl`
--
ALTER TABLE `accept_reject_tbl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `accept_reject_tbl_ibfk_1` (`event_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category_tbl`
--
ALTER TABLE `category_tbl`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `event_join_user`
--
ALTER TABLE `event_join_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_tbl`
--
ALTER TABLE `event_tbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `featured_images`
--
ALTER TABLE `featured_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group_chat`
--
ALTER TABLE `group_chat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_chat_ibfk_1` (`group_id`);

--
-- Indexes for table `note_tbl`
--
ALTER TABLE `note_tbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification_tbl`
--
ALTER TABLE `notification_tbl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_tbl_ibfk_1` (`event_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_following`
--
ALTER TABLE `user_following`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accept_reject_tbl`
--
ALTER TABLE `accept_reject_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `category_tbl`
--
ALTER TABLE `category_tbl`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `event_join_user`
--
ALTER TABLE `event_join_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `event_tbl`
--
ALTER TABLE `event_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `featured_images`
--
ALTER TABLE `featured_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group_chat`
--
ALTER TABLE `group_chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `note_tbl`
--
ALTER TABLE `note_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notification_tbl`
--
ALTER TABLE `notification_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_following`
--
ALTER TABLE `user_following`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accept_reject_tbl`
--
ALTER TABLE `accept_reject_tbl`
  ADD CONSTRAINT `accept_reject_tbl_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event_tbl` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `group_chat`
--
ALTER TABLE `group_chat`
  ADD CONSTRAINT `group_chat_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `event_tbl` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
