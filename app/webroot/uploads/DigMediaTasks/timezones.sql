-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 27, 2015 at 11:04 AM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `silkrouters`
--

-- --------------------------------------------------------

--
-- Table structure for table `timezones`
--

CREATE TABLE IF NOT EXISTS `timezones` (
`id` tinyint(3) unsigned NOT NULL,
  `timezone_location` varchar(30) NOT NULL DEFAULT '',
  `gmt` varchar(11) NOT NULL DEFAULT '',
  `offset` tinyint(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `timezones`
--

INSERT INTO `timezones` (`id`, `timezone_location`, `gmt`, `offset`) VALUES
(1, 'International Date Line West', '(GMT-12:00)', -12),
(2, 'Midway Island', '(GMT-11:00)', -11),
(3, 'Samoa', '(GMT-11:00)', -11),
(4, 'Hawaii', '(GMT-10:00)', -10),
(5, 'Alaska', '(GMT-09:00)', -9),
(6, 'Pacific Time (US & Canada)', '(GMT-08:00)', -8),
(7, 'Tijuana', '(GMT-08:00)', -8),
(8, 'Arizona', '(GMT-07:00)', -7),
(9, 'Mountain Time (US & Canada)', '(GMT-07:00)', -7),
(10, 'Chihuahua', '(GMT-07:00)', -7),
(11, 'La Paz', '(GMT-07:00)', -7),
(12, 'Mazatlan', '(GMT-07:00)', -7),
(13, 'Central Time (US & Canada)', '(GMT-06:00)', -6),
(14, 'Central America', '(GMT-06:00)', -6),
(15, 'Guadalajara', '(GMT-06:00)', -6),
(16, 'Mexico City', '(GMT-06:00)', -6),
(17, 'Monterrey', '(GMT-06:00)', -6),
(18, 'Saskatchewan', '(GMT-06:00)', -6),
(19, 'Eastern Time (US & Canada)', '(GMT-05:00)', -5),
(20, 'Indiana (East)', '(GMT-05:00)', -5),
(21, 'Bogota', '(GMT-05:00)', -5),
(22, 'Lima', '(GMT-05:00)', -5),
(23, 'Quito', '(GMT-05:00)', -5),
(24, 'Atlantic Time (Canada)', '(GMT-04:00)', -4),
(25, 'Caracas', '(GMT-04:00)', -4),
(26, 'La Paz', '(GMT-04:00)', -4),
(27, 'Santiago', '(GMT-04:00)', -4),
(28, 'Newfoundland', '(GMT-03:30)', -3),
(29, 'Brasilia', '(GMT-03:00)', -3),
(30, 'Buenos Aires', '(GMT-03:00)', -3),
(31, 'Georgetown', '(GMT-03:00)', -3),
(32, 'Greenland', '(GMT-03:00)', -3),
(33, 'Mid-Atlantic', '(GMT-02:00)', -2),
(34, 'Azores', '(GMT-01:00)', -1),
(35, 'Cape Verde Is.', '(GMT-01:00)', -1),
(36, 'Casablanca', '(GMT)', 0),
(37, 'Dublin', '(GMT)', 0),
(38, 'Edinburgh', '(GMT)', 0),
(39, 'Lisbon', '(GMT)', 0),
(40, 'London', '(GMT)', 0),
(41, 'Monrovia', '(GMT)', 0),
(42, 'Amsterdam', '(GMT+01:00)', 1),
(43, 'Belgrade', '(GMT+01:00)', 1),
(44, 'Berlin', '(GMT+01:00)', 1),
(45, 'Bern', '(GMT+01:00)', 1),
(46, 'Bratislava', '(GMT+01:00)', 1),
(47, 'Brussels', '(GMT+01:00)', 1),
(48, 'Budapest', '(GMT+01:00)', 1),
(49, 'Copenhagen', '(GMT+01:00)', 1),
(50, 'Ljubljana', '(GMT+01:00)', 1),
(51, 'Madrid', '(GMT+01:00)', 1),
(52, 'Paris', '(GMT+01:00)', 1),
(53, 'Prague', '(GMT+01:00)', 1),
(54, 'Rome', '(GMT+01:00)', 1),
(55, 'Sarajevo', '(GMT+01:00)', 1),
(56, 'Skopje', '(GMT+01:00)', 1),
(57, 'Stockholm', '(GMT+01:00)', 1),
(58, 'Vienna', '(GMT+01:00)', 1),
(59, 'Warsaw', '(GMT+01:00)', 1),
(60, 'West Central Africa', '(GMT+01:00)', 1),
(61, 'Zagreb', '(GMT+01:00)', 1),
(62, 'Athens', '(GMT+02:00)', 2),
(63, 'Bucharest', '(GMT+02:00)', 2),
(64, 'Cairo', '(GMT+02:00)', 2),
(65, 'Harare', '(GMT+02:00)', 2),
(66, 'Helsinki', '(GMT+02:00)', 2),
(67, 'Istanbul', '(GMT+02:00)', 2),
(68, 'Jerusalem', '(GMT+02:00)', 2),
(69, 'Kyev', '(GMT+02:00)', 2),
(70, 'Minsk', '(GMT+02:00)', 2),
(71, 'Pretoria', '(GMT+02:00)', 2),
(72, 'Riga', '(GMT+02:00)', 2),
(73, 'Sofia', '(GMT+02:00)', 2),
(74, 'Tallinn', '(GMT+02:00)', 2),
(75, 'Vilnius', '(GMT+02:00)', 2),
(76, 'Baghdad', '(GMT+03:00)', 3),
(77, 'Kuwait', '(GMT+03:00)', 3),
(78, 'Moscow', '(GMT+03:00)', 3),
(79, 'Nairobi', '(GMT+03:00)', 3),
(80, 'Riyadh', '(GMT+03:00)', 3),
(81, 'St. Petersburg', '(GMT+03:00)', 3),
(82, 'Volgograd', '(GMT+03:00)', 3),
(83, 'Tehran', '(GMT+03:30)', 3),
(84, 'Abu Dhabi', '(GMT+04:00)', 4),
(85, 'Baku', '(GMT+04:00)', 4),
(86, 'Muscat', '(GMT+04:00)', 4),
(87, 'Tbilisi', '(GMT+04:00)', 4),
(88, 'Yerevan', '(GMT+04:00)', 4),
(89, 'Kabul', '(GMT+04:30)', 4),
(90, 'Ekaterinburg', '(GMT+05:00)', 5),
(91, 'Islamabad', '(GMT+05:00)', 5),
(92, 'Karachi', '(GMT+05:00)', 5),
(93, 'Tashkent', '(GMT+05:00)', 5),
(94, 'Chennai', '(GMT+05:30)', 5),
(95, 'Kolkata', '(GMT+05:30)', 5),
(96, 'Mumbai', '(GMT+05:30)', 5),
(97, 'New Delhi', '(GMT+05:30)', 5),
(98, 'Kathmandu', '(GMT+05:45)', 5),
(99, 'Almaty', '(GMT+06:00)', 6),
(100, 'Astana', '(GMT+06:00)', 6),
(101, 'Dhaka', '(GMT+06:00)', 6),
(102, 'Novosibirsk', '(GMT+06:00)', 6),
(103, 'Sri Jayawardenepura', '(GMT+06:00)', 6),
(104, 'Rangoon', '(GMT+06:30)', 6),
(105, 'Bangkok', '(GMT+07:00)', 7),
(106, 'Hanoi', '(GMT+07:00)', 7),
(107, 'Jakarta', '(GMT+07:00)', 7),
(108, 'Krasnoyarsk', '(GMT+07:00)', 7),
(109, 'Beijing', '(GMT+08:00)', 8),
(110, 'Chongqing', '(GMT+08:00)', 8),
(111, 'Hong Kong', '(GMT+08:00)', 8),
(112, 'Irkutsk', '(GMT+08:00)', 8),
(113, 'Kuala Lumpur', '(GMT+08:00)', 8),
(114, 'Perth', '(GMT+08:00)', 8),
(115, 'Singapore', '(GMT+08:00)', 8),
(116, 'Taipei', '(GMT+08:00)', 8),
(117, 'Ulaan Bataar', '(GMT+08:00)', 8),
(118, 'Urumqi', '(GMT+08:00)', 8),
(119, 'Osaka', '(GMT+09:00)', 9),
(120, 'Sapporo', '(GMT+09:00)', 9),
(121, 'Seoul', '(GMT+09:00)', 9),
(122, 'Tokyo', '(GMT+09:00)', 9),
(123, 'Yakutsk', '(GMT+09:00)', 9),
(124, 'Adelaide', '(GMT+09:30)', 9),
(125, 'Darwin', '(GMT+09:30)', 9),
(126, 'Brisbane', '(GMT+10:00)', 10),
(127, 'Canberra', '(GMT+10:00)', 10),
(128, 'Guam', '(GMT+10:00)', 10),
(129, 'Hobart', '(GMT+10:00)', 10),
(130, 'Melbourne', '(GMT+10:00)', 10),
(131, 'Port Moresby', '(GMT+10:00)', 10),
(132, 'Sydney', '(GMT+10:00)', 10),
(133, 'Vladivostok', '(GMT+10:00)', 10),
(134, 'Magadan', '(GMT+11:00)', 11),
(135, 'New Caledonia', '(GMT+11:00)', 11),
(136, 'Solomon Is.', '(GMT+11:00)', 11),
(137, 'Auckland', '(GMT+12:00)', 12),
(138, 'Fiji', '(GMT+12:00)', 12),
(139, 'Kamchatka', '(GMT+12:00)', 12),
(140, 'Marshall Is.', '(GMT+12:00)', 12),
(141, 'Wellington', '(GMT+12:00)', 12),
(142, 'Nuku''alofa', '(GMT+13:00)', 13);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `timezones`
--
ALTER TABLE `timezones`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `timezones`
--
ALTER TABLE `timezones`
MODIFY `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=143;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;