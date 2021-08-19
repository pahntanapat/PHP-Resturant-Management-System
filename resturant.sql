-- phpMyAdmin SQL Dump
-- version 4.2.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 20, 2014 at 09:29 PM
-- Server version: 5.6.19-log
-- PHP Version: 5.5.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `resturant`
--
CREATE DATABASE IF NOT EXISTS `resturant` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `resturant`;

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
`_id` bigint(20) unsigned NOT NULL,
  `nickname` text COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(16) CHARACTER SET utf8 NOT NULL,
  `password` varchar(20) CHARACTER SET utf8 NOT NULL,
  `pin` varchar(4) CHARACTER SET utf8 NOT NULL,
  `permission` smallint(5) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='รายชื่อพนักงาน' AUTO_INCREMENT=11 ;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`_id`, `nickname`, `phone`, `password`, `pin`, `permission`) VALUES
(2, 'ผู้จัดการร้าน', '0123456789', '12', '1234', 2047),
(3, 'แคชเชียร์', '9876543210', '9876', '1234', 8),
(4, 'เข้างาน', '111111111', '1111', '1111', 512),
(7, 'เสิร์ฟข้าว', '9876543210', '98765', '1234', 1025),
(8, 'ทำอาหาร', '9876543210', '0000', '1234', 2),
(9, 'บ๋อย', '0123456789', '9876', '1234', 9),
(10, 'บัญชี', '555555555', '55555', '5555', 448);

-- --------------------------------------------------------

--
-- Table structure for table `kitchen`
--

CREATE TABLE `kitchen` (
`id` bigint(20) unsigned NOT NULL,
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `printer` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='ห้องครัว และ printer ประจำครัว' AUTO_INCREMENT=9 ;

--
-- Dumping data for table `kitchen`
--

INSERT INTO `kitchen` (`id`, `name`, `printer`) VALUES
(2, 'จานเดียว', 'Brother DCP-J140W Printer (Copy 1)'),
(5, 'บาร์น้ำ', 'Adobe PDF'),
(8, 'ก๋วยเตี๋ยว', 'Microsoft XPS Document Writer');

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
`_id` bigint(20) unsigned NOT NULL,
  `fname` text COLLATE utf8_unicode_ci NOT NULL,
  `lname` text COLLATE utf8_unicode_ci,
  `phone` varchar(16) CHARACTER SET utf8 DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `reg_date` date NOT NULL,
  `exp_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='ลูกค้าสมาชิก' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `menu_dir_food`
--

CREATE TABLE `menu_dir_food` (
`id` bigint(20) unsigned NOT NULL COMMENT 'รหัส',
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT 'ชื่อหมวดหมู่',
  `parent` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'อยู่ในหมวดหมู่ ..., 0 = หมวดหมู่อาหาร (หลัก)'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='หมวดหมู่อาหาร' AUTO_INCREMENT=20 ;

--
-- Dumping data for table `menu_dir_food`
--

INSERT INTO `menu_dir_food` (`id`, `name`, `parent`) VALUES
(1, 'ก๋วยเตี๋ยว', 0),
(2, 'เกาเหลา', 0),
(4, 'จานเดียว', 0),
(6, 'เครื่องดื่ม', 0),
(7, 'ของหวาน', 0),
(8, 'ของแถม', 0),
(9, 'กับแกล้ม', 0),
(10, 'ขนม', 7),
(11, 'ปิ้ง ย่าง เผา', 9),
(12, 'ต้ม แกง', 9),
(14, 'ผัด', 9),
(17, 'ดิบ', 9),
(18, 'ดิบกว่า', 17),
(19, 'ดิบน้อย', 17);

-- --------------------------------------------------------

--
-- Table structure for table `menu_dir_ing`
--

CREATE TABLE `menu_dir_ing` (
`id` bigint(20) unsigned NOT NULL COMMENT 'รหัส',
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT 'ชื่อหมวดหมู่',
  `parent` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'อยู่ในหมวดหมู่ ..., 0 = หมวดหมู่อาหาร (หลัก)',
  `lim` smallint(5) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='หมวดหมู่อาหาร' AUTO_INCREMENT=10 ;

--
-- Dumping data for table `menu_dir_ing`
--

INSERT INTO `menu_dir_ing` (`id`, `name`, `parent`, `lim`) VALUES
(1, 'ก๋วยเตี๋ยว', 0, 0),
(2, 'เครื่องดื่ม', 0, 0),
(3, 'กับแกล้ม', 0, 0),
(4, 'ดี', 3, 1),
(7, 'เนื้อ', 3, 1),
(8, 'ผักกับ', 3, 0),
(9, 'อื่นๆ', 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `menu_food_adj`
--

CREATE TABLE `menu_food_adj` (
`id` bigint(20) unsigned NOT NULL,
  `parent` bigint(20) unsigned NOT NULL DEFAULT '0',
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `abbr` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `detail` text COLLATE utf8_unicode_ci,
  `price` decimal(6,2) unsigned NOT NULL DEFAULT '0.00',
  `kit_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'รหัสห้องครัว',
  `state` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ingredient` text COLLATE utf8_unicode_ci COMMENT 'ส่วนประกอบ'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='อาหารที่ลูกค้าสั่งส่วนประกอบเองได้' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `menu_food_adj`
--

INSERT INTO `menu_food_adj` (`id`, `parent`, `name`, `abbr`, `detail`, `price`, `kit_id`, `state`, `ingredient`) VALUES
(1, 9, 'ลาบ', '', '', '60.00', 2, 12, '7\n4\n8\n9');

-- --------------------------------------------------------

--
-- Table structure for table `menu_food_fix`
--

CREATE TABLE `menu_food_fix` (
`id` bigint(20) unsigned NOT NULL,
  `parent` bigint(20) unsigned NOT NULL,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `abbr` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `detail` text COLLATE utf8_unicode_ci,
  `price` decimal(6,2) unsigned NOT NULL DEFAULT '0.00',
  `kit_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'รหัสห้องครัว',
  `state` tinyint(3) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='รายการอาหารที่ set ไว้แล้ว' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `menu_food_fix`
--

INSERT INTO `menu_food_fix` (`id`, `parent`, `name`, `abbr`, `detail`, `price`, `kit_id`, `state`) VALUES
(2, 9, 'หลู้หมู', 'หลู้ ม.', 'ภายในเดือนนี้ ฟรีผักกับลาบจานเล็ก 1 จาน', '49.75', 2, 13),
(3, 9, 'หลู้ควาย', 'หลู้ ค.', 'เนื้อควายแท้', '60.00', 2, 8);

-- --------------------------------------------------------

--
-- Table structure for table `menu_food_ing`
--

CREATE TABLE `menu_food_ing` (
`id` bigint(20) unsigned NOT NULL,
  `parent` bigint(20) unsigned NOT NULL DEFAULT '0',
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `abbr` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `detail` text COLLATE utf8_unicode_ci,
  `price` decimal(6,2) unsigned NOT NULL DEFAULT '0.00',
  `state` tinyint(3) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='รายการส่วนประกอบอาหาร' AUTO_INCREMENT=19 ;

--
-- Dumping data for table `menu_food_ing`
--

INSERT INTO `menu_food_ing` (`id`, `parent`, `name`, `abbr`, `detail`, `price`, `state`) VALUES
(1, 4, 'ดีหมู', 'D.ม.', 'น้ำดีหมู', '0.00', 12),
(2, 4, 'ดีวัว', 'D.ว.', '', '5.50', 8),
(4, 4, 'ดีควาย', 'D.ค.', '', '5.00', 12),
(5, 4, 'ดีงู', 'D.ง.', '3 ที่/วัน', '20.00', 9),
(6, 7, 'หมู', 'ม.', 'เนื้อแดง 100%', '5.00', 12),
(7, 7, 'ไก่', 'ก.', 'ระวังไข้หวัดนก', '0.00', 5),
(8, 7, 'ปลา', 'ป.', 'ไม่คาว', '7.50', 12),
(9, 7, 'วัว', 'ว.', '', '10.00', 12),
(10, 7, 'ควาย', 'ค.', 'มีบางช่วง', '13.00', 13),
(11, 8, 'สลัด', 'ผักสลัด', '', '0.00', 12),
(12, 8, 'ผักคาวตอง', 'ผ.คต.', '', '0.00', 8),
(13, 8, 'สะเดา', 'ผ.สด.', '', '1.00', 8),
(14, 8, 'ผักกาดขาว', 'ผ.กาด', '', '0.00', 4),
(15, 8, 'ผักชี', 'ผ.ชี', '', '0.00', 12),
(16, 9, 'คั่วให้สุก', 'คั่ว', '', '3.00', 12),
(17, 9, 'เผ็ดน้อย', '', '', '0.00', 12),
(18, 9, 'เข้มข้น', '', '', '0.00', 12);

-- --------------------------------------------------------

--
-- Table structure for table `order_customer`
--

CREATE TABLE `order_customer` (
`id` bigint(20) unsigned NOT NULL,
  `table_no` tinyint(3) unsigned DEFAULT NULL,
  `cus_name` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `people` tinyint(3) unsigned NOT NULL,
  `start` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='ลูกค้าที่มาซื้อ' AUTO_INCREMENT=9 ;

--
-- Dumping data for table `order_customer`
--

INSERT INTO `order_customer` (`id`, `table_no`, `cus_name`, `people`, `start`) VALUES
(8, 1, NULL, 4, '2014-06-19 15:03:24');

-- --------------------------------------------------------

--
-- Table structure for table `order_list`
--

CREATE TABLE `order_list` (
`id` bigint(20) unsigned NOT NULL,
  `cus_id` bigint(20) unsigned NOT NULL COMMENT 'id จาก table order_customer',
  `full_menu` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'ชื่อเต็ม',
  `abbr_menu` text COLLATE utf8_unicode_ci COMMENT 'ย่อ',
  `note` text COLLATE utf8_unicode_ci COMMENT 'หมายเหตุ',
  `eat_here` tinyint(1) NOT NULL,
  `kit_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'รหัสห้องครัว',
  `price` decimal(6,2) NOT NULL COMMENT 'ราคาต่อหน่วย',
  `amount` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'จำนวน',
  `state` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'สถานะ เลขฐาน 2 [ยกเลิก?],[พิมพ์?],[ยืนยัน?]',
  `detail` text COLLATE utf8_unicode_ci COMMENT 'รายละเอียด JSON',
  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'เวลาสั่ง'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='รายการเมนูลูกค้าสั่ง' AUTO_INCREMENT=12 ;

--
-- Dumping data for table `order_list`
--

INSERT INTO `order_list` (`id`, `cus_id`, `full_menu`, `abbr_menu`, `note`, `eat_here`, `kit_id`, `price`, `amount`, `state`, `detail`, `time`) VALUES
(8, 8, 'หลู้หมู', 'หลู้ ม.', 'จัดจ้าน', 1, 2, '49.75', 1, 7, '{"name":"\\u0e2b\\u0e25\\u0e39\\u0e49\\u0e2b\\u0e21\\u0e39","abbr":"\\u0e2b\\u0e25\\u0e39\\u0e49 \\u0e21.","note":"\\u0e08\\u0e31\\u0e14\\u0e08\\u0e49\\u0e32\\u0e19","eatHere":true,"cancel":false,"dish":1,"time":{"sec":1403165052,"usec":0},"ingrd":[{"name":"\\u0e2b\\u0e25\\u0e39\\u0e49\\u0e2b\\u0e21\\u0e39 (\\u0e2b\\u0e25\\u0e39\\u0e49 \\u0e21.)","price":"49.75","group":"\\u0e01\\u0e31\\u0e1a\\u0e41\\u0e01\\u0e25\\u0e49\\u0e21"}]}', '2014-06-19 15:04:12'),
(9, 8, 'หลู้ควาย', 'หลู้ ค.', '', 1, 2, '60.00', 1, 3, '{"name":"\\u0e2b\\u0e25\\u0e39\\u0e49\\u0e04\\u0e27\\u0e32\\u0e22","abbr":"\\u0e2b\\u0e25\\u0e39\\u0e49 \\u0e04.","note":"","eatHere":true,"cancel":false,"dish":1,"time":{"sec":1403165074,"usec":0},"ingrd":[{"name":"\\u0e2b\\u0e25\\u0e39\\u0e49\\u0e04\\u0e27\\u0e32\\u0e22 (\\u0e2b\\u0e25\\u0e39\\u0e49 \\u0e04.)","price":"60.00","group":"\\u0e01\\u0e31\\u0e1a\\u0e41\\u0e01\\u0e25\\u0e49\\u0e21"}]}', '2014-06-19 15:04:34'),
(10, 8, 'ลาบ + หมู + ดีหมู + สลัด, ผักคาวตอง, สะเดา, ผักชี + คั่วให้สุก, เข้มข้น', 'ลาบ + ม. + D.ม. + ผักสลัด, ผ.คต., ผ.สด., ผ.ชี + คั่ว, เข้มข้น', 'แซ็บๆ', 1, 2, '69.00', 1, 3, '{"name":"\\u0e25\\u0e32\\u0e1a + \\u0e2b\\u0e21\\u0e39 + \\u0e14\\u0e35\\u0e2b\\u0e21\\u0e39 + \\u0e2a\\u0e25\\u0e31\\u0e14, \\u0e1c\\u0e31\\u0e01\\u0e04\\u0e32\\u0e27\\u0e15\\u0e2d\\u0e07, \\u0e2a\\u0e30\\u0e40\\u0e14\\u0e32, \\u0e1c\\u0e31\\u0e01\\u0e0a\\u0e35 + \\u0e04\\u0e31\\u0e48\\u0e27\\u0e43\\u0e2b\\u0e49\\u0e2a\\u0e38\\u0e01, \\u0e40\\u0e02\\u0e49\\u0e21\\u0e02\\u0e49\\u0e19","abbr":"\\u0e25\\u0e32\\u0e1a + \\u0e21. + D.\\u0e21. + \\u0e1c\\u0e31\\u0e01\\u0e2a\\u0e25\\u0e31\\u0e14, \\u0e1c.\\u0e04\\u0e15., \\u0e1c.\\u0e2a\\u0e14., \\u0e1c.\\u0e0a\\u0e35 + \\u0e04\\u0e31\\u0e48\\u0e27, \\u0e40\\u0e02\\u0e49\\u0e21\\u0e02\\u0e49\\u0e19","note":"\\u0e41\\u0e0b\\u0e47\\u0e1a\\u0e46","eatHere":true,"cancel":false,"dish":1,"time":{"sec":1403168459,"usec":0},"ingrd":[{"name":"\\u0e25\\u0e32\\u0e1a","price":"60.00","group":"\\u0e01\\u0e31\\u0e1a\\u0e41\\u0e01\\u0e25\\u0e49\\u0e21"},{"name":"\\u0e14\\u0e35\\u0e2b\\u0e21\\u0e39 (D.\\u0e21.)","price":"0.00","group":"\\u0e14\\u0e35"},{"name":"\\u0e2b\\u0e21\\u0e39 (\\u0e21.)","price":"5.00","group":"\\u0e40\\u0e19\\u0e37\\u0e49\\u0e2d"},{"name":"\\u0e2a\\u0e25\\u0e31\\u0e14 (\\u0e1c\\u0e31\\u0e01\\u0e2a\\u0e25\\u0e31\\u0e14)","price":"0.00","group":"\\u0e1c\\u0e31\\u0e01\\u0e01\\u0e31\\u0e1a"},{"name":"\\u0e1c\\u0e31\\u0e01\\u0e04\\u0e32\\u0e27\\u0e15\\u0e2d\\u0e07 (\\u0e1c.\\u0e04\\u0e15.)","price":"0.00","group":"\\u0e1c\\u0e31\\u0e01\\u0e01\\u0e31\\u0e1a"},{"name":"\\u0e2a\\u0e30\\u0e40\\u0e14\\u0e32 (\\u0e1c.\\u0e2a\\u0e14.)","price":"1.00","group":"\\u0e1c\\u0e31\\u0e01\\u0e01\\u0e31\\u0e1a"},{"name":"\\u0e1c\\u0e31\\u0e01\\u0e0a\\u0e35 (\\u0e1c.\\u0e0a\\u0e35)","price":"0.00","group":"\\u0e1c\\u0e31\\u0e01\\u0e01\\u0e31\\u0e1a"},{"name":"\\u0e04\\u0e31\\u0e48\\u0e27\\u0e43\\u0e2b\\u0e49\\u0e2a\\u0e38\\u0e01 (\\u0e04\\u0e31\\u0e48\\u0e27)","price":"3.00","group":"\\u0e2d\\u0e37\\u0e48\\u0e19\\u0e46"},{"name":"\\u0e40\\u0e02\\u0e49\\u0e21\\u0e02\\u0e49\\u0e19","price":"0.00","group":"\\u0e2d\\u0e37\\u0e48\\u0e19\\u0e46"}]}', '2014-06-19 16:00:59'),
(11, 8, 'ลาบ + ปลา + ดีควาย + สลัด, ผักกาดขาว + เข้มข้น', 'ลาบ + ป. + D.ค. + ผักสลัด, ผ.กาด + เข้มข้น', '', 0, 2, '72.50', 2, 3, '{"name":"\\u0e25\\u0e32\\u0e1a + \\u0e1b\\u0e25\\u0e32 + \\u0e14\\u0e35\\u0e04\\u0e27\\u0e32\\u0e22 + \\u0e2a\\u0e25\\u0e31\\u0e14, \\u0e1c\\u0e31\\u0e01\\u0e01\\u0e32\\u0e14\\u0e02\\u0e32\\u0e27 + \\u0e40\\u0e02\\u0e49\\u0e21\\u0e02\\u0e49\\u0e19","abbr":"\\u0e25\\u0e32\\u0e1a + \\u0e1b. + D.\\u0e04. + \\u0e1c\\u0e31\\u0e01\\u0e2a\\u0e25\\u0e31\\u0e14, \\u0e1c.\\u0e01\\u0e32\\u0e14 + \\u0e40\\u0e02\\u0e49\\u0e21\\u0e02\\u0e49\\u0e19","note":"","eatHere":false,"cancel":false,"dish":2,"time":{"sec":1403168486,"usec":0},"ingrd":[{"name":"\\u0e25\\u0e32\\u0e1a","price":"60.00","group":"\\u0e01\\u0e31\\u0e1a\\u0e41\\u0e01\\u0e25\\u0e49\\u0e21"},{"name":"\\u0e14\\u0e35\\u0e04\\u0e27\\u0e32\\u0e22 (D.\\u0e04.)","price":"5.00","group":"\\u0e14\\u0e35"},{"name":"\\u0e1b\\u0e25\\u0e32 (\\u0e1b.)","price":"7.50","group":"\\u0e40\\u0e19\\u0e37\\u0e49\\u0e2d"},{"name":"\\u0e2a\\u0e25\\u0e31\\u0e14 (\\u0e1c\\u0e31\\u0e01\\u0e2a\\u0e25\\u0e31\\u0e14)","price":"0.00","group":"\\u0e1c\\u0e31\\u0e01\\u0e01\\u0e31\\u0e1a"},{"name":"\\u0e1c\\u0e31\\u0e01\\u0e01\\u0e32\\u0e14\\u0e02\\u0e32\\u0e27 (\\u0e1c.\\u0e01\\u0e32\\u0e14)","price":"0.00","group":"\\u0e1c\\u0e31\\u0e01\\u0e01\\u0e31\\u0e1a"},{"name":"\\u0e40\\u0e02\\u0e49\\u0e21\\u0e02\\u0e49\\u0e19","price":"0.00","group":"\\u0e2d\\u0e37\\u0e48\\u0e19\\u0e46"}]}', '2014-06-19 16:01:26');

-- --------------------------------------------------------

--
-- Table structure for table `promotion`
--

CREATE TABLE `promotion` (
`id` bigint(20) unsigned NOT NULL,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `detail` text COLLATE utf8_unicode_ci NOT NULL,
  `discount` decimal(6,2) NOT NULL DEFAULT '0.00',
  `state` tinyint(4) NOT NULL DEFAULT '0' COMMENT '[เฉพาะสมาชิก][discount=percent/money][active]'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `promotion`
--

INSERT INTO `promotion` (`id`, `name`, `detail`, `discount`, `state`) VALUES
(1, 'ส่วนลดสมาชิก', '', '5.00', 7);

-- --------------------------------------------------------

--
-- Table structure for table `working`
--

CREATE TABLE `working` (
`id` bigint(20) unsigned NOT NULL,
  `emp_id` bigint(20) unsigned NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='พนักงานที่เข้างานแล้ว' AUTO_INCREMENT=1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
 ADD UNIQUE KEY `_id` (`_id`);

--
-- Indexes for table `kitchen`
--
ALTER TABLE `kitchen`
 ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
 ADD UNIQUE KEY `_id` (`_id`);

--
-- Indexes for table `menu_dir_food`
--
ALTER TABLE `menu_dir_food`
 ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `menu_dir_ing`
--
ALTER TABLE `menu_dir_ing`
 ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `menu_food_adj`
--
ALTER TABLE `menu_food_adj`
 ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `menu_food_fix`
--
ALTER TABLE `menu_food_fix`
 ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `menu_food_ing`
--
ALTER TABLE `menu_food_ing`
 ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `order_customer`
--
ALTER TABLE `order_customer`
 ADD UNIQUE KEY `id` (`id`), ADD UNIQUE KEY `table_no` (`table_no`), ADD UNIQUE KEY `cust_name` (`cus_name`);

--
-- Indexes for table `order_list`
--
ALTER TABLE `order_list`
 ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `promotion`
--
ALTER TABLE `promotion`
 ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `working`
--
ALTER TABLE `working`
 ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
MODIFY `_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `kitchen`
--
ALTER TABLE `kitchen`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
MODIFY `_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `menu_dir_food`
--
ALTER TABLE `menu_dir_food`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'รหัส',AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `menu_dir_ing`
--
ALTER TABLE `menu_dir_ing`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'รหัส',AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `menu_food_adj`
--
ALTER TABLE `menu_food_adj`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `menu_food_fix`
--
ALTER TABLE `menu_food_fix`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `menu_food_ing`
--
ALTER TABLE `menu_food_ing`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `order_customer`
--
ALTER TABLE `order_customer`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `order_list`
--
ALTER TABLE `order_list`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `promotion`
--
ALTER TABLE `promotion`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `working`
--
ALTER TABLE `working`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
