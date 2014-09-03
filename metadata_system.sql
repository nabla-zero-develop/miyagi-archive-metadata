-- MySQL dump 10.13  Distrib 5.1.73, for redhat-linux-gnu (x86_64)
--
-- Host: localhost    Database: metadata_system
-- ------------------------------------------------------
-- Server version	5.1.73-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `content`
--

DROP TABLE IF EXISTS `content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content` (
  `uniqid` bigint(20) DEFAULT NULL,
  `md_type` int(11) DEFAULT NULL,
  `md_title` text,
  `md_copywriter` text,
  `md_copywriter_other` text,
  `md_copyrigher_uri` text,
  `md_copyrighter_yomi` text,
  `md_content_year` int(11) DEFAULT NULL,
  `md_content_month` int(11) DEFAULT NULL,
  `md_content_day` int(11) DEFAULT NULL,
  `md_content_hour` int(11) DEFAULT NULL,
  `md_content_min` int(11) DEFAULT NULL,
  `md_content_sec` int(11) DEFAULT NULL,
  `md_publish_year` int(11) DEFAULT NULL,
  `md_publish_month` int(11) DEFAULT NULL,
  `md_publish_day` int(11) DEFAULT NULL,
  `md_setting_year` int(11) DEFAULT NULL,
  `md_setting_month` int(11) DEFAULT NULL,
  `md_seting_day` int(11) DEFAULT NULL,
  `md_setting_place` text,
  `md_issue_for` text,
  `md_issue_year` int(11) DEFAULT NULL,
  `md_issue_month` int(11) DEFAULT NULL,
  `md_issue_day` int(11) DEFAULT NULL,
  `md_narrator` text,
  `md_content_restriction` text,
  KEY (`uniqid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lotfiles`
--

DROP TABLE IF EXISTS `lotfile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lotfile` (
  `uniqid` bigint(20) NOT NULL,
  `lotid` int(11),
  `ord` int(11) NOT NULL,
  `filepath` text,
  `finish` tinyint(4) DEFAULT '0',
  `cdcode` int(11),
  `finish_date` datetime,
  PRIMARY KEY (`uniqid`),
  KEY (`lotid`),
  FOREIGN KEY (`lotid`) REFERENCES lot(`lotid`)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `lot`;
CREATE TABLE `lot` (
  `lotid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11),
  `status` tinyint default 0 COMMENT '0:not started 1:working 2:finished',
  `regist_date` datetime,
  `start_date` datetime,
  `finish_date` datetime,
  `lot_memo` text,
  PRIMARY KEY (`lotid`),
  FOREIGN KEY (`userid`) REFERENCES users(`userid`)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `user_memo` text,
  PRIMARY KEY (`userid`)
);

DROP TABLE IF EXISTS `baseinfo`
CREATE TABLE baseinfo(
uniqid bigint,
file_id int NOT NULL,
local_code int COMMENT '課室コード（県版）、市町村コード（市町村版）',
shubetu char COMMENT '資料種別',
kanri_bango int COMMENT '課室（県版）、市町村（市町村版）管理番号',
shiryo_jyuryobi text COMMENT '資料受領日',
contributor text COMMENT '資料提供者',
contributor_yomi text COMMENT '資料提供者のヨミ',
bunrui_code text COMMENT '分類コード',
bunsho_bunrui text COMMENT '文書分類記号（県版）、市町村分類（市町村版）',
title text COMMENT 'タイトル',
creator text COMMENT '撮影者・作成者',
creator_yomi text COMMENT '作成者のヨミ',
sakusei_nen int COMMENT '作成日(年)',
sakusei_tuki int COMMENT '作成日(月)',
sakusei_bi int COMMENT '作成日(日)',
satuei_basho_zip text COMMENT '撮影場所（〒番号)',
satuei_basho_address text COMMENT '撮影場所住所',
satuei_basho_address_yomi text COMMENT '撮影場所住所のヨミ',
haifu_basho text COMMENT '配布場所',
haifu_basho_yomi text COMMENT '配布場所のヨミ',
keyword text COMMENT 'キーワード',
renraku_saki_zip text COMMENT '作成者連絡先住所の〒番号',
renraku_saki_address text COMMENT '作成者連絡先住所',
renraku_saki_tel text COMMENT '作成者連絡先電話番号',
renraku_saki_other text COMMENT 'その他の作成者連絡先',
kenri_shori text COMMENT '権利処理（県版と市町村版で値制約が異なる。県版は「済」「未」、市町村版は9で済）なので、県版の「済」を9に書き換える 未処理は0',
open_level char COMMENT '公開レベル　県版と市町村版で値制約が異なる。県版は、公開の場合、「公開」で、市町村版の場合は１が公開、2が限定公開、3が公開保留なので市町村側に合わせて公開は1とする。県版のxは、扱いがわからないのでそのままとしておく。',
horyu_reason text COMMENT '保留理由',
original_shiryo_keitai text COMMENT '媒体コード',
PRIMARY KEY (uniqid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `baseinfo_file`
CREATE TABLE baseinfo_file(
id int auto_increment,
filename text,
cdcode int,
PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-08-02 16:49:08
