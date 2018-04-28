-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.1.16-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win32
-- HeidiSQL Version:             9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table gcp.modbus
CREATE TABLE IF NOT EXISTS `modbus` (
  `address` varchar(10) NOT NULL,
  `data_type` varchar(20) NOT NULL,
  `tag_name` varchar(40) NOT NULL,
  `description` varchar(80) NOT NULL,
  PRIMARY KEY (`address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table gcp.modbus: ~0 rows (approximately)
/*!40000 ALTER TABLE `modbus` DISABLE KEYS */;
INSERT INTO `modbus` (`address`, `data_type`, `tag_name`, `description`) VALUES
	('20038', 'Float', 'S_Gen_real_enrg', 'Gen. real energy'),
	('20503', 'Bool', 'EZ_G_13', 'The genset runs mains parallel'),
	('20507', 'Bool', 'Emergency_Mode', ''),
	('20508', 'Bool', 'Dig_Input_0', 'Utility breaker status'),
	('20509', 'Bool', 'Dig_Input_1', 'Gen breaker status'),
	('20510', 'Bool', 'RTAC_Trip', 'Trip command'),
	('20511', 'Bool', 'RTAC_Block', 'Block Command'),
	('20512', 'Bool', 'RTAC_Perm_Stat', 'Permission Status'),
	('20513', 'Bool', 'RTAC_Allow', 'Allow Connection Permit'),
	('20519', 'Bool', 'M_86GLo_Tr', '86G Lock Out Trip'),
	('20520', 'Bool', 'M_86MLo_Tr', '86U Lock Out Trip'),
	('20523', 'Bool', 'M_Start_Auto', 'Remote Start Initiated'),
	('20532', 'Word', 'Total_Gen_Power', 'Generator Power (kW)'),
	('20535', 'Int', 'Total_mains_pow', 'Store Load (kW)'),
	('20537', 'Bool', 'M_Start_Inhibit', ''),
	('20546', 'Word', 'Hrs_until_maint', 'Hours until next maintenance'),
	('20547', 'Word', 'Gen_Hrs', 'Gen. hours of operation * * * Scale Req *'),
	('20613', 'Bool', 'SEL_D_1', '50 PHASE'),
	('20614', 'Bool', 'SEL_D_2', '50 GROUND'),
	('20615', 'Bool', 'SEL_D_3', 'SEL LO TRIP: 50 NEGSEQ'),
	('20616', 'Bool', 'SEL_D_4', 'SEL LO TRIP: 51 PHASE'),
	('20617', 'Bool', 'SEL_D_5', 'SEL LO TRIP: 51 GROUND'),
	('20618', 'Bool', 'SEL_D_6', 'SEL LO TRIP:51 NEGSEQ'),
	('20619', 'Bool', 'SEL_D_7', 'SEL LO TRIP: NEUTRAL 50'),
	('20620', 'Bool', 'SEL_D_8', 'SEL LO TRIP: NEUTRAL 51'),
	('20621', 'Bool', 'SEL_D_9', 'SEL LO TRIP: 67 PHASE'),
	('20622', 'Bool', 'SEL_D_10', 'SEL LO TRIP: 67 GROUND'),
	('20623', 'Bool', 'SEL_D_11', 'SEL LO TRIP: 67 NEGSEQ'),
	('20624', 'Bool', 'SEL_D_12', 'SEL LO TRIP: 46 NEGSEQ'),
	('20625', 'Bool', 'SEL_D_13', 'SEL LO TRIP: 49T THERMAL'),
	('20626', 'Bool', 'SEL_D_14', 'SEL LO TRIP: GND DIFF 87N'),
	('20627', 'Bool', 'SEL_D_15', 'SEL LO TRIP: RESTR DIFF 87R'),
	('20628', 'Bool', 'SEL_D_16', 'SEL LO TRIP: UNRSTR DIFF 87U'),
	('20629', 'Bool', 'SEL_DD_1', 'SEL TRIP HI: UNDERVOLT 27P'),
	('20630', 'Bool', 'SEL_DD_2', 'SEL TRIP HI: OVERVOLT 59P'),
	('20631', 'Bool', 'SEL_DD_3', 'RESERVED'),
	('20632', 'Bool', 'SEL_DD_4', 'SEL TRIP HI: POWER ELEMENTS'),
	('20633', 'Bool', 'SEL_DD_5', 'SEL TRIP HI: FREQUENCY 81'),
	('20634', 'Bool', 'SEL_DD_6', 'SEL TRIP HI: VOLTS/HERTZ'),
	('20635', 'Bool', 'SEL_DD_7', 'SEL TRIP HI: RESTRCTD EARTH'),
	('20636', 'Bool', 'SEL_DD_8', 'SEL TRIP HI: RTD TRIP'),
	('20637', 'Bool', 'SEL_DD_9', 'SEL TRIP HI: BREAKER FAIL'),
	('20638', 'Bool', 'SEL_DD_10', 'SEL TRIP HI: REMOTE TRIP'),
	('20639', 'Bool', 'SEL_DD_11', 'BACKUP'),
	('20640', 'Bool', 'SEL_DD_12', 'SEL TRIP HI: 40 FLD LOSS'),
	('20641', 'Bool', 'SEL_DD_13', 'SEL TRIP HI: 64G/64F GND'),
	('20642', 'Bool', 'SEL_DD_14', 'SEL TRIP HI: INADVERTENT ENRG'),
	('20643', 'Bool', 'SEL_DD_15', 'SEL TRIP HI: OUT OF STEP'),
	('20644', 'Bool', 'SEL_DD_16', 'SEL TRIP HI: TRIP'),
	('20645', 'Bool', 'SEL_DDD_1', 'SEL WARNING LO: BREAKER MONITOR'),
	('20646', 'Bool', 'SEL_DDD_2', 'SEL WARNING LO: DEMAND ALARM'),
	('20647', 'Bool', 'SEL_DDD_3', 'SEL WARNING LO: RTD FAULT'),
	('20648', 'Bool', 'SEL_DDD_4', 'CONFIG FAULT'),
	('20649', 'Bool', 'SEL_DDD_5', 'COMM FAULT'),
	('20650', 'Bool', 'SEL_DDD_6', 'COMM IDLE'),
	('20651', 'Bool', 'SEL_DDD_7', 'COMM LOSS'),
	('20652', 'Bool', 'SEL_DDD_8', 'SEL WARNING LO: DIFF ALARM 87A'),
	('20653', 'Bool', 'SEL_DDD_9', 'SEL WARNING LO: 5TH HARMONIC'),
	('20654', 'Bool', 'SEL_DDD_10', 'SEL WARNING LO: RTD ALARM'),
	('20655', 'Bool', 'SEL_DDD_11', 'SEL WARNING LO: LOSS OF POTENTIAL'),
	('20656', 'Bool', 'SEL_DDD_12', 'SEL WARNING LO: AI HI/LO ALARM'),
	('20657', 'Bool', 'SEL_DDD_13', 'SEL WARNING LO: 49A THERMAL ALARM'),
	('20658', 'Bool', 'SEL_DDD_14', 'SEL WARNING LO: HALARM'),
	('20659', 'Bool', 'SEL_DDD_15', 'SEL WARNING LO: SALARM'),
	('20660', 'Float', 'M_Gen_Power_fac', 'Power Factor'),
	('20661', 'Bool', 'SEL_D4_1', 'SEL WARNING HI: UNDERVOLT 27P'),
	('20662', 'Bool', 'SEL_D4_2', 'SEL WARNING HI: OVERVOLT 59P'),
	('20663', 'Bool', 'SEL_D4_3', 'SEL WARNING HI: 46 NEGSEQ'),
	('20664', 'Float', 'M_Av_Gen_DeltaV', 'Volts'),
	('20668', 'Float', 'M_Main_power_pf', 'Power Factor'),
	('20670', 'Float', 'M_Av_Main_Del_V', 'Volts'),
	('20676', 'Float', 'M_Av_Main_Curnt', 'Amps (Current)'),
	('20678', 'Float', 'M_Av_Gen_Crnt', 'Amps (Current)'),
	('20680', 'Float', 'M_Gen_real_enrg', 'Kilo-Watt'),
	('20684', 'Float', 'M_Total_Main_po', 'Kilo-Watt'),
	('20840', 'Word', 'EZ_Com_Status', 'EZ_Com_Status'),
	('20842', 'Word', 'SEL_Com_Status', 'SEL_Com_Status'),
	('20844', 'Word', 'ACMG_Com_Status', 'ACMG_Com_Status'),
	('20895', 'Word', 'EMCP_Status', 'EMCP_Status'),
	('21026', 'Word', 'Genset_Status', 'Generator Run Status');
/*!40000 ALTER TABLE `modbus` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
