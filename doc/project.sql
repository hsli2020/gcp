-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.1.31-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win32
-- HeidiSQL Version:             9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table gcp.project
CREATE TABLE IF NOT EXISTS `project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_number` varchar(20) NOT NULL,
  `fund_name` varchar(10) NOT NULL,
  `project_size` int(11) NOT NULL,
  `site_name` varchar(40) NOT NULL,
  `address` varchar(80) NOT NULL,
  `store_number` int(11) NOT NULL,
  `operation_mode` varchar(40) NOT NULL,
  `primary_ip` varchar(20) NOT NULL,
  `backup_ip` varchar(20) NOT NULL,
  `ftpdir` varchar(80) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `project_number` (`project_number`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- Dumping data for table gcp.project: ~12 rows (approximately)
/*!40000 ALTER TABLE `project` DISABLE KEYS */;
INSERT INTO `project` (`id`, `project_number`, `fund_name`, `project_size`, `site_name`, `address`, `store_number`, `operation_mode`, `primary_ip`, `backup_ip`, `ftpdir`, `active`) VALUES
	(1, '152849', 'TPG', 1000, 'North Oakville', '201 Oak Park', 1024, 'Paralleling', '174.90.154.14', '74.198.113.1', 'GCP_Oakville_001EC6054859', 1),
	(2, '152850', 'TPG', 1000, 'Winston Churchill', '3050 Argentia Rd', 1080, 'Closed Transition', '174.90.154.8', '74.198.112.253', 'GCP_WinstonChurchill_001EC605493B', 1),
	(3, '152852', 'TPG', 1000, 'Newmarket', '18120 Yonge St', 1018, 'Closed Transition', '174.90.154.6', '74.198.113.7', 'GCP_Newmarket_001EC60548B3', 1),
	(4, '152853', 'TPG', 1000, 'Markham', '200 Bullock Ave', 1032, 'Paralleling', '174.90.154.15', '74.198.113.5', 'GCP_Markham_001EC6054946', 1),
	(5, '152854', 'TPG', 1000, 'Aurora', '15900 Bayview Ave', 1030, 'Closed Transition', '174.90.154.17', '74.198.113.4', '', 0),
	(6, '152855', 'TPG', 1000, 'Richmond Hill', '301 Hightech Rd', 1028, 'Paralleling', '174.90.154.18', '74.198.113.0', 'GCP_RichmondHill_001EC6054945', 1),
	(7, '152856', 'TPG', 1000, 'Kitchener', '875 Highland Rd', 2822, 'Closed Transition', '174.90.154.59', '74.198.112.252', 'GCP_Kitchener_001EC60545FE', 1),
	(8, '152857', 'TPG', 1000, 'Glen Erin', '5010 Glen Erin Dr', 1011, 'Closed Transition', '174.90.154.7', '74.198.113.8', 'GCP_Glen_Erin_001EC605493D', 1),
	(9, '152864', 'TPG', 1000, 'Milton', '820 Main St', 2810, 'Closed Transition', '174.90.154.62', '74.198.113.6', 'GCP_Milton_001EC6054944', 1),
	(10, '152865', 'TPG', 1000, 'Oshawa', '1385 Harmony Rd', 1043, 'Closed Transition', '174.90.154.60', '74.198.113.2', 'GCP_Oshawa_001EC605493F', 1),
	(11, '152866', 'TPG', 1000, 'Whitby', '200 Taunton Rd W', 1058, 'Closed Transition', '174.90.154.63', '74.198.112.254', '', 0),
	(12, '152871', 'TPG', 1000, 'South Orleans', '4270 Innes Rd', 1071, 'Closed Transition', '174.90.154.61', '74.198.112.251', 'GCP_SouthOrleans_001EC60549D0', 1);
/*!40000 ALTER TABLE `project` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
