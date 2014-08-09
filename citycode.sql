-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2014 年 8 月 06 日 05:11
-- サーバのバージョン： 5.5.36
-- PHP Version: 5.4.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `metadata_system_`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `citycode`
--

DROP TABLE IF EXISTS `citycode`;
CREATE TABLE IF NOT EXISTS `citycode` (
  `name` text COLLATE utf8_bin NOT NULL,
  `code` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- テーブルのデータのダンプ `citycode`
--

INSERT INTO `citycode` (`name`, `code`) VALUES
('仙台市', 100),
('仙台市青葉区', 101),
('仙台市宮城野区', 102),
('仙台市若林区', 103),
('仙台市太白区', 104),
('仙台市泉区', 105),
('石巻市', 202),
('塩竈市', 203),
('気仙沼市', 205),
('白石市', 206),
('名取市', 207),
('角田市', 208),
('多賀城市', 209),
('岩沼市', 211),
('登米市', 212),
('栗原市', 213),
('東松島市', 214),
('大崎市', 215),
('刈田郡蔵王町', 301),
('刈田郡七ケ宿町', 302),
('柴田郡大河原町', 321),
('柴田郡村田町', 322),
('柴田郡柴田町', 323),
('柴田郡川崎町', 324),
('伊具郡丸森町', 341),
('亘理郡亘理町', 361),
('亘理郡山元町', 362),
('宮城郡松島町', 401),
('宮城郡七ヶ浜町', 404),
('宮城郡利府町', 406),
('黒川郡大和町', 421),
('黒川郡大郷町', 422),
('黒川郡富谷町', 423),
('黒川郡大衡村', 424),
('加美郡色麻町', 444),
('加美郡加美町', 445),
('遠田郡涌谷町', 501),
('遠田郡美里町', 505),
('牡鹿郡女川町', 581),
('本吉郡南三陸町', 606);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
