/*
Navicat MySQL Data Transfer

Source Server         : LOCALHOST
Source Server Version : 100119
Source Host           : localhost:3306
Source Database       : bengkelcms

Target Server Type    : MYSQL
Target Server Version : 100119
File Encoding         : 65001

Date: 2017-07-11 02:04:54
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for level_akses
-- ----------------------------
DROP TABLE IF EXISTS `level_akses`;
CREATE TABLE `level_akses` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_level` int(10) DEFAULT NULL,
  `id_menu` int(10) DEFAULT NULL,
  `c` int(10) DEFAULT NULL,
  `r` int(10) DEFAULT NULL,
  `u` int(10) DEFAULT NULL,
  `d` int(10) DEFAULT NULL,
  `e` int(10) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of level_akses
-- ----------------------------
INSERT INTO `level_akses` VALUES ('111', '1', '1', '1', '1', '1', '1', '1', '2017-07-09 20:33:29', '2017-07-09 20:33:29', null, 'EMP0000001', 'A');
INSERT INTO `level_akses` VALUES ('112', '1', '2', '1', '1', '1', '1', '1', '2017-07-09 20:33:29', '2017-07-09 20:33:29', null, 'EMP0000001', 'A');
INSERT INTO `level_akses` VALUES ('113', '1', '3', '1', '1', '1', '1', '1', '2017-07-09 20:33:29', '2017-07-09 20:33:29', null, 'EMP0000001', 'A');
INSERT INTO `level_akses` VALUES ('114', '1', '4', '1', '1', '1', '1', '1', '2017-07-09 20:33:29', '2017-07-09 20:33:29', null, 'EMP0000001', 'A');
INSERT INTO `level_akses` VALUES ('115', '1', '5', '1', '1', '1', '1', '1', '2017-07-09 20:33:30', '2017-07-09 20:33:30', null, 'EMP0000001', 'A');
INSERT INTO `level_akses` VALUES ('116', '1', '6', '1', '1', '1', '1', '1', '2017-07-09 20:33:30', '2017-07-09 20:33:30', null, 'EMP0000001', 'A');
INSERT INTO `level_akses` VALUES ('117', '1', '7', '1', '1', '1', '1', '1', '2017-07-09 20:33:30', '2017-07-09 20:33:30', null, 'EMP0000001', 'A');
INSERT INTO `level_akses` VALUES ('118', '1', '8', '1', '1', '1', '1', '1', '2017-07-09 20:33:30', '2017-07-09 20:33:30', null, 'EMP0000001', 'A');
INSERT INTO `level_akses` VALUES ('119', '1', '9', '1', '1', '1', '1', '1', '2017-07-09 20:33:30', '2017-07-09 20:33:30', null, 'EMP0000001', 'A');
INSERT INTO `level_akses` VALUES ('120', '1', '10', '1', '1', '1', '1', '1', '2017-07-09 20:33:30', '2017-07-09 20:33:30', null, 'EMP0000001', 'A');

-- ----------------------------
-- Table structure for logs
-- ----------------------------
DROP TABLE IF EXISTS `logs`;
CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `do` text CHARACTER SET utf8,
  `table` text CHARACTER SET utf8,
  `note` text,
  `primary` varchar(255) DEFAULT NULL,
  `url` text,
  `ip` text,
  `param` text,
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1456 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of logs
-- ----------------------------

-- ----------------------------
-- Table structure for lt_biayalain
-- ----------------------------
DROP TABLE IF EXISTS `lt_biayalain`;
CREATE TABLE `lt_biayalain` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `biayalain_id` varchar(20) NOT NULL,
  `user_id` varchar(20) DEFAULT NULL,
  `no_nota` varchar(100) DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL,
  `total_biaya` int(20) DEFAULT NULL,
  `type_cost` int(2) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of lt_biayalain
-- ----------------------------

-- ----------------------------
-- Table structure for lt_biayalain_detail
-- ----------------------------
DROP TABLE IF EXISTS `lt_biayalain_detail`;
CREATE TABLE `lt_biayalain_detail` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `biayalain_id` int(10) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `jumlah` int(10) DEFAULT NULL,
  `harga` bigint(20) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of lt_biayalain_detail
-- ----------------------------

-- ----------------------------
-- Table structure for lt_hutang
-- ----------------------------
DROP TABLE IF EXISTS `lt_hutang`;
CREATE TABLE `lt_hutang` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `hutang_id` varchar(20) NOT NULL,
  `pembelian_id` varchar(20) NOT NULL,
  `total` bigint(20) DEFAULT NULL,
  `bayar` bigint(20) DEFAULT NULL,
  `status_hutang` int(1) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of lt_hutang
-- ----------------------------

-- ----------------------------
-- Table structure for lt_loan
-- ----------------------------
DROP TABLE IF EXISTS `lt_loan`;
CREATE TABLE `lt_loan` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `loan_id` varchar(20) DEFAULT NULL,
  `user_id` varchar(20) DEFAULT NULL,
  `total` bigint(20) DEFAULT NULL,
  `bayar` bigint(20) DEFAULT NULL,
  `status_loan` int(2) DEFAULT NULL,
  `loan_type` int(2) DEFAULT NULL,
  `tanggal_jatuh_tempo` datetime DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of lt_loan
-- ----------------------------
INSERT INTO `lt_loan` VALUES ('104', 'LN00000001', 'EMP0000001', '0', '0', '2', '1', '2017-07-11 02:04:41', '2017-07-11 02:04:41', '2017-07-11 02:04:41', null, 'EMP0000001', 'A');
INSERT INTO `lt_loan` VALUES ('105', 'LN00000002', 'EMP0000003', '0', '0', '2', '1', '2017-07-11 02:04:41', '2017-07-11 02:04:41', '2017-07-11 02:04:41', null, 'EMP0000001', 'A');
INSERT INTO `lt_loan` VALUES ('106', 'LN00000003', 'EMP0000004', '0', '0', '2', '1', '2017-07-11 02:04:41', '2017-07-11 02:04:41', '2017-07-11 02:04:41', null, 'EMP0000001', 'A');
INSERT INTO `lt_loan` VALUES ('107', 'LN00000004', 'EMP0000005', '0', '0', '2', '1', '2017-07-11 02:04:41', '2017-07-11 02:04:41', '2017-07-11 02:04:41', null, 'EMP0000001', 'A');
INSERT INTO `lt_loan` VALUES ('108', 'LN00000005', 'EMP0000006', '0', '0', '2', '1', '2017-07-11 02:04:41', '2017-07-11 02:04:41', '2017-07-11 02:04:41', null, 'EMP0000001', 'A');
INSERT INTO `lt_loan` VALUES ('109', 'LN00000006', 'EMP0000007', '0', '0', '2', '1', '2017-07-11 02:04:41', '2017-07-11 02:04:41', '2017-07-11 02:04:41', null, 'EMP0000001', 'A');
INSERT INTO `lt_loan` VALUES ('110', 'LN00000007', 'EMP0000008', '0', '0', '2', '1', '2017-07-11 02:04:41', '2017-07-11 02:04:41', '2017-07-11 02:04:41', null, 'EMP0000001', 'A');
INSERT INTO `lt_loan` VALUES ('111', 'LN00000008', 'EMP0000009', '0', '0', '2', '1', '2017-07-11 02:04:41', '2017-07-11 02:04:41', '2017-07-11 02:04:41', null, 'EMP0000001', 'A');
INSERT INTO `lt_loan` VALUES ('112', 'LN00000009', 'EMP0000010', '0', '0', '2', '1', '2017-07-11 02:04:42', '2017-07-11 02:04:42', '2017-07-11 02:04:42', null, 'EMP0000001', 'A');
INSERT INTO `lt_loan` VALUES ('113', 'LN00000010', 'EMP0000002', '0', '0', '2', '1', '2017-07-11 02:04:42', '2017-07-11 02:04:42', '2017-07-11 02:04:42', null, 'EMP0000001', 'A');

-- ----------------------------
-- Table structure for lt_pemakaiansolar
-- ----------------------------
DROP TABLE IF EXISTS `lt_pemakaiansolar`;
CREATE TABLE `lt_pemakaiansolar` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pemakaian_solar_id` varchar(20) NOT NULL,
  `mobil_id` varchar(20) NOT NULL,
  `karyawan_id` varchar(20) DEFAULT NULL,
  `nama_pengisi` varchar(100) DEFAULT NULL,
  `tanggal_pemakaian` datetime DEFAULT '0000-00-00 00:00:00',
  `no_nota` varchar(100) DEFAULT NULL,
  `liter_pemakaian_solar` int(10) DEFAULT NULL,
  `solar_type_id` int(10) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of lt_pemakaiansolar
-- ----------------------------

-- ----------------------------
-- Table structure for lt_piutang
-- ----------------------------
DROP TABLE IF EXISTS `lt_piutang`;
CREATE TABLE `lt_piutang` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `piutang_id` varchar(20) NOT NULL,
  `penjualan_id` varchar(20) NOT NULL,
  `total` bigint(20) DEFAULT NULL,
  `bayar` bigint(20) DEFAULT NULL,
  `status_piutang` int(1) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of lt_piutang
-- ----------------------------

-- ----------------------------
-- Table structure for lt_route
-- ----------------------------
DROP TABLE IF EXISTS `lt_route`;
CREATE TABLE `lt_route` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `route_id` varchar(20) DEFAULT NULL,
  `route_type_a` int(10) DEFAULT NULL,
  `route_a` varchar(100) DEFAULT NULL,
  `route_type_b` int(10) DEFAULT NULL,
  `route_b` varchar(100) DEFAULT NULL,
  `distance` int(10) DEFAULT NULL,
  `hour` int(10) DEFAULT NULL,
  `minute` int(10) DEFAULT NULL,
  `second` int(10) DEFAULT NULL,
  `liter` int(10) DEFAULT NULL,
  `komisi` bigint(20) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of lt_route
-- ----------------------------

-- ----------------------------
-- Table structure for lt_solar
-- ----------------------------
DROP TABLE IF EXISTS `lt_solar`;
CREATE TABLE `lt_solar` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `harga_liter` int(15) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of lt_solar
-- ----------------------------

-- ----------------------------
-- Table structure for lt_user_type
-- ----------------------------
DROP TABLE IF EXISTS `lt_user_type`;
CREATE TABLE `lt_user_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of lt_user_type
-- ----------------------------
INSERT INTO `lt_user_type` VALUES ('1', 'Develop', '2017-05-28 16:52:00', '2017-07-09 20:33:30', null, 'EMP0000001', 'A');

-- ----------------------------
-- Table structure for menus
-- ----------------------------
DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of menus
-- ----------------------------
INSERT INTO `menus` VALUES ('1', 'Main Menu');
INSERT INTO `menus` VALUES ('2', 'HRD');
INSERT INTO `menus` VALUES ('3', 'Buying');
INSERT INTO `menus` VALUES ('4', 'Selling');
INSERT INTO `menus` VALUES ('5', 'Purchase Return');
INSERT INTO `menus` VALUES ('6', 'Sales Return');
INSERT INTO `menus` VALUES ('7', 'Addtional');
INSERT INTO `menus` VALUES ('8', 'Houling');
INSERT INTO `menus` VALUES ('9', 'Admin');
INSERT INTO `menus` VALUES ('10', 'Dashboard');

-- ----------------------------
-- Table structure for ms_barang
-- ----------------------------
DROP TABLE IF EXISTS `ms_barang`;
CREATE TABLE `ms_barang` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `barang_id` varchar(20) NOT NULL,
  `nama` varchar(500) NOT NULL,
  `kategori` int(10) DEFAULT NULL,
  `spesifikasi` varchar(500) DEFAULT NULL,
  `harga` bigint(20) DEFAULT NULL,
  `harga_jual` bigint(20) DEFAULT NULL,
  `stock` int(10) DEFAULT NULL,
  `is_available` int(1) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ms_barang
-- ----------------------------

-- ----------------------------
-- Table structure for ms_barang_temp
-- ----------------------------
DROP TABLE IF EXISTS `ms_barang_temp`;
CREATE TABLE `ms_barang_temp` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `qty` int(10) NOT NULL,
  `harga_jual` bigint(20) DEFAULT NULL,
  `harga_beli` bigint(20) DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `status` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ms_barang_temp
-- ----------------------------

-- ----------------------------
-- Table structure for ms_customer
-- ----------------------------
DROP TABLE IF EXISTS `ms_customer`;
CREATE TABLE `ms_customer` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `customer_id` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text,
  `no_telp` varchar(15) DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `fax` varchar(100) DEFAULT NULL,
  `no_rekening` varchar(50) DEFAULT NULL,
  `nama_rekening` varchar(100) DEFAULT NULL,
  `bank_nama` varchar(20) DEFAULT NULL,
  `npwp` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ms_customer
-- ----------------------------

-- ----------------------------
-- Table structure for ms_department
-- ----------------------------
DROP TABLE IF EXISTS `ms_department`;
CREATE TABLE `ms_department` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `delete_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ms_department
-- ----------------------------

-- ----------------------------
-- Table structure for ms_gaji
-- ----------------------------
DROP TABLE IF EXISTS `ms_gaji`;
CREATE TABLE `ms_gaji` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `karyawan_id` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `gaji` bigint(20) NOT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ms_gaji
-- ----------------------------

-- ----------------------------
-- Table structure for ms_jabatan
-- ----------------------------
DROP TABLE IF EXISTS `ms_jabatan`;
CREATE TABLE `ms_jabatan` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_date` datetime DEFAULT NULL,
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `delete_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ms_jabatan
-- ----------------------------

-- ----------------------------
-- Table structure for ms_jasa
-- ----------------------------
DROP TABLE IF EXISTS `ms_jasa`;
CREATE TABLE `ms_jasa` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `service_id` varchar(20) NOT NULL,
  `barang_id` varchar(20) DEFAULT NULL,
  `barang_name` varchar(500) DEFAULT NULL,
  `name` varchar(500) DEFAULT NULL,
  `price` int(20) DEFAULT NULL,
  `qty` int(10) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ms_jasa
-- ----------------------------

-- ----------------------------
-- Table structure for ms_karyawan
-- ----------------------------
DROP TABLE IF EXISTS `ms_karyawan`;
CREATE TABLE `ms_karyawan` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `karyawan_id` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text,
  `no_telp` varchar(15) DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `alamat_asal` text,
  `jabatan` varchar(50) DEFAULT NULL,
  `tempat_lahir` varchar(20) DEFAULT NULL,
  `tanggal_lahir` datetime DEFAULT NULL,
  `status_karyawan` varchar(15) DEFAULT NULL,
  `no_rekening` varchar(50) DEFAULT NULL,
  `nama_rekening` varchar(100) DEFAULT NULL,
  `bank_nama` varchar(20) DEFAULT NULL,
  `nama_emergency_karyawan` varchar(100) DEFAULT NULL,
  `alamat_emergency_karyawan` text,
  `no_kontak_emergency_karyawan` varchar(15) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ms_karyawan
-- ----------------------------
INSERT INTO `ms_karyawan` VALUES ('43', 'EMP0000001', 'Achmad Fauzi', 'Jakarta', '1234567890', '1234567890', 'Jakarta', '1', 'Jakarta', '1995-12-31 00:00:00', 'Permanent', '1234567890', 'Achmad Fauzi', 'BCA', 'Megandi', 'Jakarta', '1234567890', '2017-06-27 10:28:06', '2017-06-27 10:28:06', null, 'EMP0000001', 'A');
INSERT INTO `ms_karyawan` VALUES ('44', 'EMP0000002', 'Megandi', 'Jakarta', '1234567890', '1234567890', 'Jakarta', '12', 'Jakarta', '1997-07-10 00:00:00', 'Magang', '1234567890', 'Megandi', 'Mandiri', 'Achmad Fauzi', 'Jakarta', '1234567890', '2017-06-27 10:29:10', '2017-06-27 10:29:10', null, 'EMP0000001', 'A');
INSERT INTO `ms_karyawan` VALUES ('45', 'EMP0000003', 'Employee A', 'Dummy', '1234567890', null, null, '1', 'Dummy', '2017-06-27 00:00:00', 'Permanent', '1234567890', 'Dummy', 'Dummy', null, null, null, '2017-06-27 10:37:02', '2017-06-27 10:37:02', null, 'EMP0000001', 'A');
INSERT INTO `ms_karyawan` VALUES ('46', 'EMP0000004', 'Employee B', 'Dummy', '1234567890', null, null, '12', 'Dummy', '2017-06-27 00:00:00', 'Permanent', '1234567890', 'Dummy', 'Dummy', null, null, null, '2017-06-27 10:37:36', '2017-06-27 10:37:36', null, 'EMP0000001', 'A');
INSERT INTO `ms_karyawan` VALUES ('47', 'EMP0000005', 'Employee C', 'Dummy', '1234567890', null, null, '13', 'Dummy', '2017-06-27 00:00:00', 'Permanent', '1234567890', 'Dummy', 'Dummy', null, null, null, '2017-06-27 10:40:22', '2017-06-27 10:40:22', null, 'EMP0000001', 'A');
INSERT INTO `ms_karyawan` VALUES ('48', 'EMP0000006', 'Employee D', 'Dummy', '1234567890', null, null, '14', 'Dummy', '2017-06-27 00:00:00', 'Magang', '1234567890', 'Dummy', 'Dummy', null, null, null, '2017-06-27 10:42:25', '2017-06-27 10:42:25', null, 'EMP0000001', 'A');
INSERT INTO `ms_karyawan` VALUES ('49', 'EMP0000007', 'Employee E', 'Dummy', '1234567890', '1234567890', null, '14', 'Dummy', '2017-06-27 00:00:00', 'Magang', '1234567890', 'Dummy', 'Dummy', null, null, null, '2017-06-27 10:43:10', '2017-06-27 10:43:10', null, 'EMP0000001', 'A');
INSERT INTO `ms_karyawan` VALUES ('50', 'EMP0000008', 'Employee F', 'Dummy', '1234567890', null, null, '15', 'Dummy', '2017-06-27 00:00:00', 'Kontrak', '1234567890', 'Dummy', 'Dummy', null, null, null, '2017-06-27 10:47:34', '2017-06-27 10:47:34', null, 'EMP0000001', 'A');
INSERT INTO `ms_karyawan` VALUES ('51', 'EMP0000009', 'Employee G', 'Dummy', '1234567890', null, null, '11', 'Dummy', '2017-06-27 00:00:00', 'Kontrak', '1234567890', 'Dummy', 'Dummy', null, null, null, '2017-06-27 10:48:16', '2017-06-27 10:48:16', null, 'EMP0000001', 'A');
INSERT INTO `ms_karyawan` VALUES ('52', 'EMP0000010', 'Employee H', 'Dummy', '1234567890', null, null, '13', 'Dummy', '2017-06-27 00:00:00', 'Permanent', '1234567890', 'Dummy', 'Dummy', null, null, null, '2017-06-27 11:00:52', '2017-06-27 11:05:26', '0000-00-00 00:00:00', 'EMP0000001', 'A');

-- ----------------------------
-- Table structure for ms_kategori_barang
-- ----------------------------
DROP TABLE IF EXISTS `ms_kategori_barang`;
CREATE TABLE `ms_kategori_barang` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `type_date` int(2) DEFAULT NULL,
  `year` int(10) DEFAULT '0',
  `month` int(10) DEFAULT '0',
  `day` int(10) DEFAULT '0',
  `expiry_date` datetime DEFAULT NULL,
  `is_inventory` int(2) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `delete_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ms_kategori_barang
-- ----------------------------

-- ----------------------------
-- Table structure for ms_mobil
-- ----------------------------
DROP TABLE IF EXISTS `ms_mobil`;
CREATE TABLE `ms_mobil` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `mobil_id` varchar(20) NOT NULL,
  `type_car` varchar(50) NOT NULL,
  `customer_id` varchar(20) NOT NULL,
  `no_polisi_mobil` varchar(20) DEFAULT NULL,
  `alamat_pemilik` text,
  `merek_mobil` varchar(50) DEFAULT NULL,
  `tipe_mobil` varchar(50) DEFAULT NULL,
  `jenis_mobil` varchar(50) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `tahun_pembuatan_mobil` int(10) DEFAULT NULL,
  `warna_mobil` varchar(20) DEFAULT NULL,
  `no_rangka_mobil` varchar(20) DEFAULT NULL,
  `isi_silinder_mobil` varchar(100) DEFAULT NULL,
  `bahan_bakar_mobil` varchar(20) DEFAULT NULL,
  `no_bpkb_mobil` varchar(50) DEFAULT NULL,
  `tahun_registrasi_mobil` int(10) DEFAULT NULL,
  `indent_mobil` varchar(50) DEFAULT NULL,
  `status_mobil` varchar(20) DEFAULT NULL,
  `no_mesin_mobil` varchar(50) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ms_mobil
-- ----------------------------

-- ----------------------------
-- Table structure for ms_pelabuhan
-- ----------------------------
DROP TABLE IF EXISTS `ms_pelabuhan`;
CREATE TABLE `ms_pelabuhan` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pelabuhan_id` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text,
  `alamat_perusahaan_pelabuhan` text,
  `no_telp_perusahaan_pelabuhan` varchar(15) DEFAULT NULL,
  `npwp` varchar(50) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ms_pelabuhan
-- ----------------------------

-- ----------------------------
-- Table structure for ms_pembelian
-- ----------------------------
DROP TABLE IF EXISTS `ms_pembelian`;
CREATE TABLE `ms_pembelian` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pembelian_id` varchar(20) NOT NULL,
  `returdetail_id` int(20) DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL,
  `no_nota` varchar(100) DEFAULT NULL,
  `supplier_id` varchar(20) DEFAULT NULL,
  `status_bayar_pembelian` int(1) DEFAULT NULL,
  `tanggal_jatuh_tempo_pembelian` datetime DEFAULT NULL,
  `pembelian_dp` bigint(20) DEFAULT NULL,
  `pembelian_bayar` bigint(20) DEFAULT NULL,
  `pembelian_total` bigint(20) DEFAULT NULL,
  `status_transaksi` int(2) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ms_pembelian
-- ----------------------------

-- ----------------------------
-- Table structure for ms_penjualan
-- ----------------------------
DROP TABLE IF EXISTS `ms_penjualan`;
CREATE TABLE `ms_penjualan` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `penjualan_id` varchar(20) NOT NULL,
  `returdetail_id` int(20) DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL,
  `tanggal_jatuh_tempo_penjualan` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `no_nota` varchar(100) DEFAULT NULL,
  `customer_id` varchar(20) DEFAULT NULL,
  `status_bayar_penjualan` int(1) DEFAULT NULL,
  `penjualan_dp` bigint(20) DEFAULT NULL,
  `penjualan_bayar` bigint(20) DEFAULT NULL,
  `penjualan_total` bigint(20) DEFAULT NULL,
  `status_transaksi` int(2) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ms_penjualan
-- ----------------------------

-- ----------------------------
-- Table structure for ms_returpembelian
-- ----------------------------
DROP TABLE IF EXISTS `ms_returpembelian`;
CREATE TABLE `ms_returpembelian` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `retur_id` varchar(20) DEFAULT NULL,
  `pembelian_id` int(11) DEFAULT NULL,
  `total_return` bigint(20) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ms_returpembelian
-- ----------------------------

-- ----------------------------
-- Table structure for ms_returpenjualan
-- ----------------------------
DROP TABLE IF EXISTS `ms_returpenjualan`;
CREATE TABLE `ms_returpenjualan` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `retur_id` varchar(20) DEFAULT NULL,
  `penjualan_id` int(11) DEFAULT NULL,
  `total_return` bigint(20) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ms_returpenjualan
-- ----------------------------

-- ----------------------------
-- Table structure for ms_supplier
-- ----------------------------
DROP TABLE IF EXISTS `ms_supplier`;
CREATE TABLE `ms_supplier` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `supplier_id` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text,
  `no_telp` varchar(15) DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `fax` varchar(100) DEFAULT NULL,
  `no_rekening` varchar(50) DEFAULT NULL,
  `nama_rekening` varchar(100) DEFAULT NULL,
  `bank_nama` varchar(20) DEFAULT NULL,
  `npwp` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ms_supplier
-- ----------------------------

-- ----------------------------
-- Table structure for ms_tambang
-- ----------------------------
DROP TABLE IF EXISTS `ms_tambang`;
CREATE TABLE `ms_tambang` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `tambang_id` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text,
  `alamat_perusahaan_tambang` text,
  `no_telp_perusahaan_tambang` varchar(15) DEFAULT NULL,
  `npwp` varchar(50) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ms_tambang
-- ----------------------------

-- ----------------------------
-- Table structure for tr_credit_history
-- ----------------------------
DROP TABLE IF EXISTS `tr_credit_history`;
CREATE TABLE `tr_credit_history` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `piutang_id` int(10) DEFAULT NULL,
  `salreturndetail_id` int(10) DEFAULT NULL,
  `total_piutang` bigint(20) DEFAULT NULL,
  `total_pembayaran_piutang` bigint(20) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tr_credit_history
-- ----------------------------

-- ----------------------------
-- Table structure for tr_debt_history
-- ----------------------------
DROP TABLE IF EXISTS `tr_debt_history`;
CREATE TABLE `tr_debt_history` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `hutang_id` int(10) DEFAULT NULL,
  `returndetail_id` int(10) DEFAULT NULL,
  `total_hutang` bigint(20) DEFAULT NULL,
  `total_pembayaran_hutang` bigint(20) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tr_debt_history
-- ----------------------------

-- ----------------------------
-- Table structure for tr_deposit_customer
-- ----------------------------
DROP TABLE IF EXISTS `tr_deposit_customer`;
CREATE TABLE `tr_deposit_customer` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `customer_id` varchar(20) NOT NULL,
  `deposit` bigint(20) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tr_deposit_customer
-- ----------------------------

-- ----------------------------
-- Table structure for tr_deposit_supplier
-- ----------------------------
DROP TABLE IF EXISTS `tr_deposit_supplier`;
CREATE TABLE `tr_deposit_supplier` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `supplier_id` varchar(20) NOT NULL,
  `deposit` bigint(20) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tr_deposit_supplier
-- ----------------------------

-- ----------------------------
-- Table structure for tr_detail_pembelian
-- ----------------------------
DROP TABLE IF EXISTS `tr_detail_pembelian`;
CREATE TABLE `tr_detail_pembelian` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ms_pembelian_id` varchar(20) NOT NULL,
  `barang_id` varchar(100) NOT NULL,
  `qty` int(20) DEFAULT NULL,
  `qty_return` int(10) DEFAULT NULL,
  `sub_total_pembelian` bigint(20) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tr_detail_pembelian
-- ----------------------------

-- ----------------------------
-- Table structure for tr_detail_penjualan
-- ----------------------------
DROP TABLE IF EXISTS `tr_detail_penjualan`;
CREATE TABLE `tr_detail_penjualan` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `detail_penjualan_id` varchar(20) DEFAULT NULL,
  `barang_id` varchar(100) DEFAULT NULL,
  `qty` int(20) DEFAULT NULL,
  `qty_return` int(10) DEFAULT NULL,
  `sub_total_penjualan` bigint(20) DEFAULT NULL,
  `id_karyawan_kerja1` varchar(100) DEFAULT NULL,
  `id_karyawan_kerja2` varchar(100) DEFAULT NULL,
  `id_karyawan_kerja3` varchar(100) DEFAULT NULL,
  `id_karyawan_kerja4` varchar(100) DEFAULT NULL,
  `id_karyawan_kerja5` varchar(100) DEFAULT NULL,
  `type_sell` int(2) DEFAULT NULL,
  `type_report` int(2) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tr_detail_penjualan
-- ----------------------------

-- ----------------------------
-- Table structure for tr_houling
-- ----------------------------
DROP TABLE IF EXISTS `tr_houling`;
CREATE TABLE `tr_houling` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `houling_id` varchar(20) DEFAULT NULL,
  `tanggal_houling` datetime DEFAULT '0000-00-00 00:00:00',
  `mobil_id` varchar(20) DEFAULT NULL,
  `supir_id` varchar(20) DEFAULT NULL,
  `route_id` varchar(20) DEFAULT NULL,
  `solar_type_id` int(10) DEFAULT NULL,
  `tonase_id` varchar(20) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tr_houling
-- ----------------------------

-- ----------------------------
-- Table structure for tr_loan_history
-- ----------------------------
DROP TABLE IF EXISTS `tr_loan_history`;
CREATE TABLE `tr_loan_history` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `loan_id` int(10) DEFAULT NULL,
  `total_loan` bigint(20) DEFAULT NULL,
  `total_pembayaran_loan` bigint(20) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tr_loan_history
-- ----------------------------

-- ----------------------------
-- Table structure for tr_returpembelian
-- ----------------------------
DROP TABLE IF EXISTS `tr_returpembelian`;
CREATE TABLE `tr_returpembelian` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `returpembelian_id` varchar(20) NOT NULL,
  `barang_id` varchar(20) NOT NULL,
  `qty` int(20) DEFAULT NULL,
  `sub_total` bigint(20) DEFAULT NULL,
  `type_return` int(10) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tr_returpembelian
-- ----------------------------

-- ----------------------------
-- Table structure for tr_returpenjualan
-- ----------------------------
DROP TABLE IF EXISTS `tr_returpenjualan`;
CREATE TABLE `tr_returpenjualan` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `returpenjualan_id` varchar(20) NOT NULL,
  `barang_id` varchar(20) NOT NULL,
  `qty` int(20) DEFAULT NULL,
  `sub_total` bigint(20) DEFAULT NULL,
  `type_return` int(10) DEFAULT NULL,
  `type_sell` char(1) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tr_returpenjualan
-- ----------------------------

-- ----------------------------
-- Table structure for tr_tonase
-- ----------------------------
DROP TABLE IF EXISTS `tr_tonase`;
CREATE TABLE `tr_tonase` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_tonase` varchar(20) DEFAULT NULL,
  `id_route` varchar(20) DEFAULT NULL,
  `tonase_percent_a` int(20) DEFAULT NULL,
  `tonase_a` int(20) DEFAULT NULL,
  `tonase_percent_b` int(20) DEFAULT NULL,
  `tonase_b` int(20) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_modify_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tr_tonase
-- ----------------------------

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `karyawan_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level_id` int(20) NOT NULL,
  `department_id` int(20) NOT NULL,
  `position_id` int(20) NOT NULL,
  `photo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `delete_date` datetime DEFAULT NULL,
  `modify_user_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` char(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('2', 'EMP0000001', 'achmadfauzi@live.com', '$2y$10$.hfRrGmrI/fdkncR0.DmQOcuyosRq5eAgSTtftPWGPFQoWHXDti/i', '1', '7', '1', '0', '2017-04-08 18:39:36', '2017-07-10 15:52:15', null, 'EMP0000001', 'A', 'BSqfUv7taNHqxCXcUuX5nyia5ZdYrl1zfiZCw56itWUiJM3BdEAJNbBMnDqJ');
INSERT INTO `users` VALUES ('5', 'EMP0000002', 'me.gandi@gmail.com', '$2y$10$HJHCO7ffIzo9TZfYtE1yY.xtZEK2q/Vbw5aLRoJvsRMXF654VXEum', '1', '9', '12', '0', '2017-06-17 00:58:11', '2017-06-17 00:59:12', null, 'EMP0000001', 'A', 'BLn7MGJkrlaThyjMzq7aaLgqIUzqH2XZRHFxp3T1Py6oKKVJBeuMvl8jsxEm');
SET FOREIGN_KEY_CHECKS=1;
