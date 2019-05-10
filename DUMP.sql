/*
SQLyog Ultimate v13.1.1 (64 bit)
MySQL - 5.5.21 : Database - doctrine
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`doctrine` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `doctrine`;

/*Table structure for table `enquete` */

DROP TABLE IF EXISTS `enquete`;

CREATE TABLE `enquete` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(250) NOT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=latin1;

/*Data for the table `enquete` */

insert  into `enquete`(`id`,`descricao`) values 
(65,'Quantas horas por semana você usa nas Redes Sociais?'),
(67,'Você tem uma Conta no Google__?'),
(70,'O Fusca deve voltar a ser fabricado?');

/*Table structure for table `membership` */

DROP TABLE IF EXISTS `membership`;

CREATE TABLE `membership` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `membership` */

insert  into `membership`(`id`,`username`,`password`,`status`) values 
(1,'admin','21232f297a57a5a743894a0e4a801fc3',1),
(2,'igor','dd97813dd40be87559aaefed642c3fbb',1),
(3,'teste','e10adc3949ba59abbe56e057f20f883e',1);

/*Table structure for table `subenquete` */

DROP TABLE IF EXISTS `subenquete`;

CREATE TABLE `subenquete` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `idEnquete` int(20) DEFAULT NULL,
  `descricao` text,
  `nota` text,
  `status` text,
  PRIMARY KEY (`id`),
  KEY `idEnquete` (`idEnquete`),
  CONSTRAINT `FK_subenquete` FOREIGN KEY (`idEnquete`) REFERENCES `enquete` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

/*Data for the table `subenquete` */

insert  into `subenquete`(`id`,`idEnquete`,`descricao`,`nota`,`status`) values 
(4,70,'SIM','5','1'),
(5,70,'TETSES','4','1'),
(6,70,'SIM COM CERTEZA','6','1'),
(7,65,'10 horas por semana','2','1'),
(8,67,'SIM','5','1'),
(9,67,'SIM','5','1'),
(13,67,'NÃO','4','1');

/*Table structure for table `subenquete1_1` */

DROP TABLE IF EXISTS `subenquete1_1`;

CREATE TABLE `subenquete1_1` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `descricao` tinytext,
  `nota` tinytext,
  `status` tinytext,
  `idEnquete` int(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `subenquete1_1` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
