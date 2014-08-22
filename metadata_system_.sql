-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2014 年 8 月 09 日 15:13
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
-- テーブルの構造 `baseinfo`
--

CREATE TABLE IF NOT EXISTS `baseinfo` (
  `uniqid` bigint(20) NOT NULL DEFAULT '0',
  `local_code` int(11) DEFAULT NULL COMMENT '課室コード（県版）、市町村コード（市町村版）',
  `shubetu` char(1) DEFAULT NULL COMMENT '資料種別',
  `kanri_bango` int(11) DEFAULT NULL COMMENT '課室（県版）、市町村（市町村版）管理番号',
  `shiryo_jyuryobi` text COMMENT '資料受領日',
  `contributor` text COMMENT '資料提供者',
  `contributor_yomi` text COMMENT '資料提供者のヨミ',
  `bunrui_code` text COMMENT '分類コード',
  `bunsho_bunrui` text COMMENT '文書分類記号（県版）、市町村分類（市町村版）',
  `title` text COMMENT 'タイトル',
  `creator` text COMMENT '撮影者・作成者',
  `creator_yomi` text COMMENT '作成者のヨミ',
  `sakusei_nen` int(11) DEFAULT NULL COMMENT '作成日(年)',
  `sakusei_tuki` int(11) DEFAULT NULL COMMENT '作成日(月)',
  `sakusei_bi` int(11) DEFAULT NULL COMMENT '作成日(日)',
  `satuei_basho_zip` text COMMENT '撮影場所（〒番号)',
  `satuei_basho_address` text COMMENT '撮影場所住所',
  `satuei_basho_address_yomi` text COMMENT '撮影場所住所のヨミ',
  `haifu_basho` text COMMENT '配布場所',
  `haifu_basho_yomi` text COMMENT '配布場所のヨミ',
  `keyword` text COMMENT 'キーワード',
  `renraku_saki_zip` text COMMENT '作成者連絡先住所の〒番号',
  `renraku_saki_address` text COMMENT '作成者連絡先住所',
  `renraku_saki_tel` text COMMENT '作成者連絡先電話番号',
  `renraku_saki_other` text COMMENT 'その他の作成者連絡先',
  `kenri_shori` text COMMENT '権利処理（県版と市町村版で値制約が異なる。県版は「済」「未」、市町村版は9で済）なので、県版の「済」を9に書き換える 未処理は0',
  `open_level` char(1) DEFAULT NULL COMMENT '公開レベル　県版と市町村版で値制約が異なる。県版は、公開の場合、「公開」で、市町村版の場合は１が公開、2が限定公開、3が公開保留なので市町村側に合わせて公開は1とする。県版のxは、扱いがわからないのでそのままとしておく。',
  `horyu_reason` text COMMENT '保留理由',
  `media_code` text COMMENT '媒体コード',
  PRIMARY KEY (`uniqid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `baseinfo`
--

INSERT INTO `baseinfo` (`uniqid`, `local_code`, `shubetu`, `kanri_bango`, `shiryo_jyuryobi`, `contributor`, `contributor_yomi`, `bunrui_code`, `bunsho_bunrui`, `title`, `creator`, `creator_yomi`, `sakusei_nen`, `sakusei_tuki`, `sakusei_bi`, `satuei_basho_zip`, `satuei_basho_address`, `satuei_basho_address_yomi`, `haifu_basho`, `haifu_basho_yomi`, `keyword`, `renraku_saki_zip`, `renraku_saki_address`, `renraku_saki_tel`, `renraku_saki_other`, `kenri_shori`, `open_level`, `horyu_reason`, `media_code`) VALUES
(170400000001, 170400, 'b', 1, '2013-03-00', '×', '×', '6:10:13:14', 'L03-3', 'みやぎ風プロジェクト', '食産業振興課 食産業企画班', 'ショクサンギョウシンコウカ ショクサンギョウキカクハン', 2013, 3, 0, '', '石巻市，気仙沼市，登米市，大崎市，栗原市，涌谷町，亘理町，仙台市泉区', '', '商談会，企業，団体，公的機関', '', 'みやぎの元気は美味しい笑顔から！，復興への追い風を起こそう！', '', '', '', '', '0', '公', '', '1'),
(170400000002, 170400, 'b', 2, '2014-03-00', '×', '×', '6:10:13:14', 'L03-3', 'みやぎ風プロジェクト', '食産業振興課 食産業企画班', 'ショクサンギョウシンコウカ ショクサンギョウキカクハン', 2014, 3, 0, '', '仙台市，名取市，東松島市，登米市，栗原市，丸森町，気仙沼市，山元町，南三陸町，石巻市，松島町，', '', '小売業，企業，公的機関，団体，市場', '', 'みやぎの元気は美味しい笑顔から！，復興への追い風を起こそう！', '', '', '', '', '0', '公', '', '1'),
(170400000003, 170400, 'p', 3, '2013-02-00', '×', '×', '0.25972222222222', 'L03-3', 'おいしい宮城は元気です！', '食産業振興課 みやぎ米・県産品販売支援班', 'ショクサンギョウシンコウカ ミヤギマイ・ケンサンピンハンバイシエンハン', 0, 0, 0, '', '', '', '鉄道車内中吊り，駅貼り，公的機関，宿泊業', '', '', '', '', '', '', '0', '公', '', '1'),
(170400000004, 170400, 'p', 4, '2014-03-00', '×', '×', '0.25972222222222', 'L03-3', 'おいしい宮城は元気です！', '食産業振興課 みやぎ米・県産品販売支援班', 'ショクサンギョウシンコウカ ミヤギマイ・ケンサンピンハンバイシエンハン', 0, 0, 0, '', '', '', '公的機関，宿泊業', '', '', '', '', '', '', '0', '公', '', '1');

-- --------------------------------------------------------

--
-- テーブルの構造 `citycode`
--

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

-- --------------------------------------------------------

--
-- テーブルの構造 `content`
--

CREATE TABLE IF NOT EXISTS `content` (
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
  KEY `uniqid` (`uniqid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `content`
--

INSERT INTO `content` (`uniqid`, `md_type`, `md_title`, `md_copywriter`, `md_copywriter_other`, `md_copyrigher_uri`, `md_copyrighter_yomi`, `md_content_year`, `md_content_month`, `md_content_day`, `md_content_hour`, `md_content_min`, `md_content_sec`, `md_publish_year`, `md_publish_month`, `md_publish_day`, `md_setting_year`, `md_setting_month`, `md_seting_day`, `md_setting_place`, `md_issue_for`, `md_issue_year`, `md_issue_month`, `md_issue_day`, `md_narrator`, `md_content_restriction`) VALUES
(520501000004, 0, '', '', '', '', '', -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, '', '', 0, 0, 0, '', ''),
(520501000005, 0, '', '', '', '', '', -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, '', '', 0, 0, 0, '', ''),
(520501000006, 0, '', '', '', '', '', -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, '', '', 0, 0, 0, '', ''),
(520501000007, 0, '', '', '', '', '', -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, '', '', 0, 0, 0, '', ''),
(0, 0, 'aaa', '', '', '', '', -1, -1, -1, 0, 0, 0, -1, -1, -1, -1, -1, -1, '', '', 0, 0, 0, '', ''),
(2147483647, 0, 'aaa', '', '', '', '', -1, -1, -1, 0, 0, 0, -1, -1, -1, -1, -1, -1, '', '', 0, 0, 0, '', ''),
(520501000003, 0, '', '', '', '', '', -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, '', '', 0, 0, 0, '', '');

-- --------------------------------------------------------

--
-- テーブルの構造 `divisioncode`
--

CREATE TABLE IF NOT EXISTS `divisioncode` (
  `name` varchar(72) DEFAULT NULL,
  `code` int(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `divisioncode`
--

INSERT INTO `divisioncode` (`name`, `code`) VALUES
('総務部　秘書課', 121000),
('総務部　人事課', 120100),
('総務部　公務研修所', 120102),
('総務部　行政管理室', 123000),
('総務部　行政経営推進課', 123100),
('総務部　職員厚生課', 120200),
('総務部　私学文書課', 120300),
('総務部　県政情報公開室', 121700),
('総務部　公文書館', 121701),
('総務部　広報課', 120700),
('総務部　財政課', 120400),
('総務部　税務課', 120500),
('総務部　大河原県税事務所', 120520),
('総務部　仙台南県税事務所', 120521),
('総務部　仙台中央県税事務所', 120522),
('総務部　仙台北県税事務所', 120523),
('総務部　塩釜県税事務所', 120524),
('総務部　北部県税事務所', 120525),
('総務部　北部県税事務所栗原地域事務所', 120526),
('総務部　東部県税事務所', 120528),
('総務部　東部県税事務所登米地域事務所', 120527),
('総務部　気仙沼県税事務所', 120529),
('総務部　気仙沼県税事務所南三陸支所', 120530),
('総務部　地方税徴収対策室', 123200),
('総務部　市町村課', 120600),
('総務部　管財課', 120800),
('総務部　財産利用推進室', 121900),
('総務部　危機対策課', 122100),
('総務部　消防課', 122200),
('総務部　消防学校', 122221),
('総務部　※防災ヘリコプター管理事務所', 122222),
('震災復興・企画部　震災復興・企画総務課', 110100),
('震災復興・企画部　東京事務所', 110101),
('震災復興・企画部　震災復興政策課', 112200),
('震災復興・企画部　地域復興支援課', 111000),
('震災復興・企画部　総合交通対策課', 111400),
('震災復興・企画部　統計課', 110300),
('震災復興・企画部　情報政策課', 110500),
('震災復興・企画部　情報産業振興室', 112700),
('震災復興・企画部　情報システム課', 112800),
('震災復興・企画部　震災復興推進課', 113000),
('環境生活部　環境生活総務課', 130100),
('環境生活部　環境政策課', 130200),
('環境生活部　再生可能エネルギー室', 132700),
('環境生活部　環境対策課', 130300),
('環境生活部　保健環境センター', 130302),
('環境生活部　原子力安全対策課', 132600),
('環境生活部　原子力センター', 132601),
('環境生活部　自然保護課', 131100),
('環境生活部　食と暮らしの安全推進課', 132100),
('環境生活部　食肉衛生検査所', 132101),
('環境生活部　動物愛護センター', 132102),
('環境生活部　循環型社会推進課', 132800),
('環境生活部　竹の内産廃処分場対策室', 132200),
('環境生活部　震災廃棄物対策課', 132500),
('環境生活部　消費生活・文化課', 132300),
('環境生活部　共同参画社会推進課', 132400),
('保健福祉部　保健福祉総務課', 140100),
('保健福祉部　仙南保健福祉事務所', 140140),
('保健福祉部　仙台保健福祉事務所', 140141),
('保健福祉部　仙台保健福祉事務所岩沼支所', 140148),
('保健福祉部　仙台保健福祉事務所黒川支所', 140149),
('保健福祉部　北部保健福祉事務所', 140142),
('保健福祉部　北部保健福祉事務所栗原地域事務所', 140143),
('保健福祉部　東部保健福祉事務所', 140145),
('保健福祉部　東部保健福祉事務所登米地域事務所', 140144),
('保健福祉部　気仙沼保健福祉事務所', 140146),
('保健福祉部　震災援護室', 142600),
('保健福祉部　社会福祉課', 141500),
('保健福祉部　医療整備課', 140300),
('保健福祉部　高等看護専門学校', 140302),
('保健福祉部　長寿社会政策課', 140400),
('保健福祉部　健康推進課', 142200),
('保健福祉部　疾病・感染症対策室', 142300),
('保健福祉部　子育て支援課', 142500),
('保健福祉部　子ども総合センター', 142501),
('保健福祉部　中央児童相談所', 142502),
('保健福祉部　北部児童相談所', 142503),
('保健福祉部　東部児童相談所', 142504),
('保健福祉部　※東部児童相談所気仙沼支所', 142505),
('保健福祉部　女性相談センター', 142506),
('保健福祉部　さわらび学園', 142507),
('保健福祉部　障害福祉課', 140700),
('保健福祉部　リハビリテーション支援センター', 140711),
('保健福祉部　精神保健福祉センター', 140708),
('保健福祉部　拓桃医療療育センター', 140706),
('保健福祉部　薬務課', 140800),
('保健福祉部　国保医療課', 141700),
('経済商工観光部　経済商工観光総務課', 160100),
('経済商工観光部　大阪事務所', 160102),
('経済商工観光部　大阪事務所名古屋産業立地センター', 160103),
('経済商工観光部　富県宮城推進室', 160200),
('経済商工観光部　大河原地方振興事務所', 160210),
('経済商工観光部　総務部', 160211),
('経済商工観光部　地方振興部', 160212),
('経済商工観光部　農業振興部', 160213),
('経済商工観光部　畜産振興部', 160214),
('経済商工観光部　農業農村整備部', 160215),
('経済商工観光部　林業振興部', 160216),
('経済商工観光部　仙台地方振興事務所', 160220),
('経済商工観光部　総務部', 160221),
('経済商工観光部　地方振興部', 160222),
('経済商工観光部　農業振興部', 160223),
('経済商工観光部　畜産振興部', 160224),
('経済商工観光部　農業農村整備部', 160225),
('経済商工観光部　林業振興部', 160226),
('経済商工観光部　水産漁港部', 160227),
('経済商工観光部　北部地方振興事務所', 160230),
('経済商工観光部　総務部', 160231),
('経済商工観光部　地方振興部', 160232),
('経済商工観光部　農業振興部', 160233),
('経済商工観光部　畜産振興部', 160234),
('経済商工観光部　農業農村整備部', 160235),
('経済商工観光部　林業振興部', 160236),
('経済商工観光部　北部地方振興事務所栗原地域事務所', 160240),
('経済商工観光部　総務部', 160241),
('経済商工観光部　地方振興部', 160242),
('経済商工観光部　農業振興部', 160243),
('経済商工観光部　畜産振興部', 160244),
('経済商工観光部　農業農村整備部', 160245),
('経済商工観光部　林業振興部', 160246),
('経済商工観光部　東部地方振興事務所', 160260),
('経済商工観光部　総務部', 160261),
('経済商工観光部　地方振興部', 160262),
('経済商工観光部　農業振興部', 160263),
('経済商工観光部　畜産振興部', 160264),
('経済商工観光部　農業農村整備部', 160265),
('経済商工観光部　林業振興部', 160266),
('経済商工観光部　水産漁港部', 160267),
('経済商工観光部　東部地方振興事務所登米地域事務所', 160250),
('経済商工観光部　総務部', 160251),
('経済商工観光部　地方振興部', 160252),
('経済商工観光部　農業振興部', 160253),
('経済商工観光部　畜産振興部', 160254),
('経済商工観光部　農業農村整備部', 160255),
('経済商工観光部　林業振興部', 160256),
('経済商工観光部　気仙沼地方振興事務所', 160270),
('経済商工観光部　総務部', 160271),
('経済商工観光部　地方振興部', 160272),
('経済商工観光部　農林振興部', 160273),
('経済商工観光部　水産漁港部', 160274),
('経済商工観光部　新産業振興課', 160300),
('経済商工観光部　産業技術総合センター', 160301),
('経済商工観光部　自動車産業振興室', 161300),
('経済商工観光部　産業立地推進課', 160400),
('経済商工観光部　計量検定所', 160401),
('経済商工観光部　商工経営支援課', 160500),
('経済商工観光部　産業人材対策課', 161100),
('経済商工観光部　白石高等技術専門校', 161101),
('経済商工観光部　仙台高等技術専門校', 161102),
('経済商工観光部　大崎高等技術専門校', 161103),
('経済商工観光部　石巻高等技術専門校', 161104),
('経済商工観光部　気仙沼高等技術専門校', 161105),
('経済商工観光部　宮城障害者職業能力開発校', 161106),
('経済商工観光部　雇用対策課', 161200),
('経済商工観光部　観光課', 160700),
('経済商工観光部　松島公園管理事務所', 160701),
('経済商工観光部　国際経済・交流課', 161400),
('経済商工観光部　海外ビジネス支援室', 161500),
('農林水産部　農林水産総務課', 170100),
('農林水産部　農林水産政策室', 170200),
('農林水産部　農林水産経営支援課', 170300),
('農林水産部　食産業振興課', 170400),
('農林水産部　農業振興課', 170500),
('農林水産部　農業大学校', 170501),
('農林水産部　※農業大学校水田経営学部古川教場', 170502),
('農林水産部　※農業大学校畜産学部岩出山教場', 170504),
('農林水産部　※亘理農業改良普及センター', 170506),
('農林水産部　※本吉農業改良普及センター', 170513),
('農林水産部　農業・園芸総合研究所', 170514),
('農林水産部　古川農業試験場', 170515),
('農林水産部　農産園芸環境課', 170600),
('農林水産部　病害虫防除所', 170601),
('農林水産部　畜産課', 170700),
('農林水産部　※大河原家畜保健衛生所', 170701),
('農林水産部　仙台家畜保健衛生所', 170702),
('農林水産部　※北部家畜保健衛生所', 170703),
('農林水産部　※東部家畜保健衛生所', 170704),
('農林水産部　畜産試験場', 170705),
('農林水産部　農村振興課', 170800),
('農林水産部　王城寺原補償工事事務所', 170801),
('農林水産部　農村整備課', 170900),
('農林水産部　農地復興推進室', 171500),
('農林水産部　林業振興課', 171000),
('農林水産部　林業技術総合センター', 171001),
('農林水産部　森林整備課', 171100),
('農林水産部　水産業振興課', 171200),
('農林水産部　水産技術総合センター', 171202),
('農林水産部　水産技術総合センター気仙沼水産試験場', 171203),
('農林水産部　水産技術総合センター内水面水産試験場', 171204),
('農林水産部　水産業基盤整備課', 171300),
('農林水産部　漁港復興推進室', 171400),
('土木部　土木総務課', 180100),
('土木部　大河原土木事務所', 180101),
('土木部　仙台土木事務所', 180102),
('土木部　北部土木事務所', 180103),
('土木部　北部土木事務所栗原地域事務所', 180104),
('土木部　東部土木事務所', 180106),
('土木部　東部土木事務所登米地域事務所', 180105),
('土木部　気仙沼土木事務所', 180108),
('土木部　仙台塩釜港湾事務所', 180121),
('土木部　石巻港湾事務所', 180123),
('土木部　中南部下水道事務所', 180135),
('土木部　東部下水道事務所', 180136),
('土木部　仙台地方ダム総合事務所', 180141),
('土木部　大崎地方ダム総合事務所', 180142),
('土木部　栗原地方ダム総合事務所', 180143),
('土木部　仙台港背後地土地区画整理事務所', 180139),
('土木部　事業管理課', 184100),
('土木部　用地課', 181100),
('土木部　道路課', 184600),
('土木部　河川課', 180500),
('土木部　防災砂防課', 184700),
('土木部　港湾課', 184300),
('土木部　空港臨空地域課', 184800),
('土木部　都市計画課', 180200),
('土木部　復興まちづくり推進室', 185000),
('土木部　下水道課', 181000),
('土木部　建築宅地課', 181400),
('土木部　住宅課', 180900),
('土木部　復興住宅整備室', 185100),
('土木部　営繕課', 181300),
('土木部　設備課', 181900),
('出納局　会計課', 260100),
('出納局　契約課', 260200),
('出納局　検査課', 260300),
('出納局　会計指導検査室', 260400),
('選挙管理委員会事務局　', 300100),
('選挙管理委員会事務局　※大河原地方支局', 300101),
('選挙管理委員会事務局　※北部地方支局', 300103),
('選挙管理委員会事務局　※東部地方支局', 300106),
('選挙管理委員会事務局　※仙台中央地方支局', 300108),
('選挙管理委員会事務局　※仙台南地方支局', 300110),
('選挙管理委員会事務局　※塩釜地方支局', 300111),
('選挙管理委員会事務局　※仙台北地方支局', 300112),
('選挙管理委員会事務局　※気仙沼地方支局', 300113),
('人事委員会事務局　総務課', 310100),
('人事委員会事務局　職員課', 310200),
('監査委員事務局　総務課', 320100),
('労働委員会事務局　総務課', 330100),
('労働委員会事務局　審査調整課', 330200),
('収用委員会　', 340100),
('海区漁業調整委員会事務局　', 350100),
('内水面漁場管理委員会　', 360100),
('議会事務局　総務課', 400100),
('議会事務局　議事課', 400200),
('議会事務局　政務調査課', 400400),
('教育庁　総務課', 520100),
('教育庁　大河原教育事務所', 520101),
('教育庁　仙台教育事務所', 520102),
('教育庁　北部教育事務所', 520103),
('教育庁　北部教育事務所栗原地域事務所', 520104),
('教育庁　東部教育事務所', 520105),
('教育庁　東部教育事務所登米地域事務所', 520106),
('教育庁　南三陸教育事務所', 520107),
('教育庁　教育企画室', 522100),
('教育庁　福利課', 520700),
('教育庁　教職員課', 521500),
('教育庁　総合教育センター', 521503),
('教育庁　義務教育課', 521600),
('教育庁　特別支援教育室', 521900),
('教育庁　高校教育課', 521700),
('教育庁　※宮城丸', 521701),
('教育庁　施設整備課', 522200),
('教育庁　スポーツ健康課', 520900),
('教育庁　生涯学習課', 520500),
('教育庁　図書館', 520501),
('教育庁　美術館', 520502),
('教育庁　志津川自然の家', 520505),
('教育庁　蔵王自然の家', 520506),
('教育庁　松島自然の家', 520603),
('教育庁　文化財保護課', 520800),
('教育庁　東北歴史博物館', 520801),
('教育庁　多賀城跡調査研究所', 520802),
('県立学校　宮城県仙台第一高等学校', 525101),
('県立学校　仙台第二高等学校', 525102),
('県立学校　仙台第三高等学校', 525103),
('県立学校　石巻高等学校', 525107),
('県立学校　古川高等学校', 525108),
('県立学校　松島高等学校', 525301),
('県立学校　名取高等学校', 525302),
('県立学校　村田高等学校', 525303),
('県立学校　岩出山高等学校', 525306),
('県立学校　涌谷高等学校', 525307),
('県立学校　岩ケ崎高等学校', 525310),
('県立学校　佐沼高等学校', 525311),
('県立学校　登米高等学校', 525312),
('県立学校　志津川高等学校', 525313),
('県立学校　泉高等学校', 525314),
('県立学校　中新田高等学校', 525315),
('県立学校　女川高等学校', 525316),
('県立学校　仙台向山高等学校', 525317),
('県立学校　多賀城高等学校', 525318),
('県立学校　仙台南高等学校', 525319),
('県立学校　名取北高等学校', 525320),
('県立学校　松山高等学校', 525321),
('県立学校　泉松陵高等学校', 525322),
('県立学校　仙台西高等学校', 525323),
('県立学校　泉館山高等学校', 525324),
('県立学校　宮城広瀬高等学校', 525325),
('県立学校　利府高等学校', 525326),
('県立学校　石巻西高等学校', 525327),
('県立学校　気仙沼西高等学校', 525328),
('県立学校　柴田高等学校', 525329),
('県立学校　仙台東高等学校', 525330),
('県立学校　富谷高等学校', 525331),
('県立学校　宮城野高等学校', 525332),
('県立学校　蔵王高等学校', 525333),
('県立学校　迫桜高等学校', 525334),
('県立学校　角田高等学校', 525335),
('県立学校　築館高等学校', 525336),
('県立学校　気仙沼高等学校', 525337),
('県立学校　古川黎明高等学校', 525338),
('県立学校　石巻好文館高等学校', 525339),
('県立学校　宮城第一高等学校', 525340),
('県立学校　塩釜高等学校', 525341),
('県立学校　白石高等学校', 525342),
('県立学校　仙台二華高等学校', 525343),
('県立学校　仙台三桜高等学校', 525344),
('県立学校　貞山高等学校', 525351),
('県立学校　東松島高等学校', 525352),
('県立学校　田尻さくら高等学校', 525353),
('県立学校　美田園高等学校', 525354),
('県立学校　農業高等学校', 525401),
('県立学校　黒川高等学校', 525402),
('県立学校　柴田農林高等学校', 525403),
('県立学校　伊具高等学校', 525404),
('県立学校　亘理高等学校', 525405),
('県立学校　加美農業高等学校', 525407),
('県立学校　小牛田農林高等学校', 525408),
('県立学校　南郷高等学校', 525409),
('県立学校　上沼高等学校', 525411),
('県立学校　米山高等学校', 525412),
('県立学校　本吉響高等学校', 525413),
('県立学校　石巻北高等学校', 525414),
('県立学校　水産高等学校', 525431),
('県立学校　気仙沼向洋高等学校', 525432),
('県立学校　工業高等学校', 525441),
('県立学校　白石工業高等学校', 525442),
('県立学校　石巻工業高等学校', 525443),
('県立学校　古川工業高等学校', 525444),
('県立学校　米谷工業高等学校', 525446),
('県立学校　大河原商業高等学校', 525461),
('県立学校　石巻商業高等学校', 525462),
('県立学校　鹿島台商業高等学校', 525463),
('県立学校　一迫商業高等学校', 525464),
('県立学校　第二工業高等学校', 525501),
('県立学校　宮城県立視覚支援学校', 525601),
('県立学校　聴覚支援学校', 525602),
('県立学校　光明支援学校', 525604),
('県立学校　船岡支援学校', 525605),
('県立学校　拓桃支援学校', 525606),
('県立学校　西多賀支援学校', 525607),
('県立学校　山元支援学校', 525608),
('県立学校　金成支援学校', 525609),
('県立学校　角田支援学校', 525610),
('県立学校　石巻支援学校', 525611),
('県立学校　気仙沼支援学校', 525612),
('県立学校　古川支援学校', 525613),
('県立学校　名取支援学校', 525614),
('県立学校　支援学校小牛田高等学園', 525615),
('県立学校　利府支援学校', 525616),
('県立学校　迫支援学校', 525617),
('県立学校　支援学校岩沼高等学園', 525618),
('県立学校　宮城県古川黎明中学校', 525701),
('県立学校　仙台二華中学校', 525702),
('警察本部　公安委員会　', 810100),
('警察本部　総務部　総務課', 820100),
('企業局　公営事業課', 951000),
('企業局　大崎広域水道事務所', 951002),
('企業局　仙南仙塩広域水道事務所', 951003),
('企業局　水道経営管理室', 952000);

-- --------------------------------------------------------

--
-- テーブルの構造 `lot`
--

CREATE TABLE IF NOT EXISTS `lot` (
  `lotid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '0' COMMENT '0:not started 1:working 2:finished',
  `regist_date` datetime DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `finish_date` datetime DEFAULT NULL,
  `lot_memo` text,
  PRIMARY KEY (`lotid`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- テーブルのデータのダンプ `lot`
--

INSERT INTO `lot` (`lotid`, `userid`, `status`, `regist_date`, `start_date`, `finish_date`, `lot_memo`) VALUES
(3, 1, 1, '2014-08-04 06:15:49', '2014-08-04 18:47:28', NULL, NULL),
(4, 1, 0, '2014-08-09 21:55:17', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- テーブルの構造 `lotfile`
--

CREATE TABLE IF NOT EXISTS `lotfile` (
  `uniqid` bigint(20) NOT NULL,
  `lotid` int(11) DEFAULT NULL,
  `ord` int(11) NOT NULL,
  `filepath` text,
  `finish` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`uniqid`),
  KEY `lotid` (`lotid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `lotfile`
--

INSERT INTO `lotfile` (`uniqid`, `lotid`, `ord`, `filepath`, `finish`) VALUES
(170400000001, 4, 1, 'contents/520501000001', 1),
(170400000002, 4, 2, 'contents/520501000002', 0),
(170400000003, 4, 3, 'contents/520501000003', 0),
(170400000004, 4, 4, 'contents/520501000004', 0),
(520501000001, 3, 1, 'contents/520501000001', 1),
(520501000002, 3, 2, 'contents/520501000002', 1),
(520501000003, 3, 3, 'contents/520501000003', 0),
(520501000004, 3, 4, 'contents/520501000004', 0),
(520501000005, 3, 5, 'contents/520501000005', 0),
(520501000006, 3, 6, 'contents/520501000006', 0),
(520501000007, 3, 7, 'contents/520501000007', 0);

-- --------------------------------------------------------

--
-- テーブルの構造 `metadata`
--

CREATE TABLE IF NOT EXISTS `metadata` (
  `uniqid` bigint(20) NOT NULL DEFAULT '0',
  `md_type` text COMMENT '資料種別',
  `series_flag` tinyint(4) DEFAULT NULL COMMENT 'シリーズ（継続資料）か否か',
  `betu_title_flag` tinyint(4) DEFAULT NULL COMMENT '別タイトルの有無',
  `kiyo_flag` tinyint(4) DEFAULT NULL COMMENT '寄与者（寄贈者）の有無',
  `iban_flag` tinyint(4) DEFAULT NULL COMMENT '異版の有無',
  `license_flag` tinyint(4) DEFAULT NULL COMMENT 'ライセンスの有無',
  `inyou_flag` tinyint(4) DEFAULT NULL COMMENT '引用資料',
  `gov_issue` text COMMENT '政府刊行物・刊行元',
  `gov_issue_2` text COMMENT '官公庁刊行物',
  `gov_issue_chihou` text COMMENT '地方公共団体刊行物',
  `gov_issue_miyagi` text COMMENT '宮城県内地方公共団体刊行物',
  `for_handicapped` text COMMENT '視聴覚者向け資料',
  `original_shiryo_keitai` text COMMENT 'オリジナル資料の形態',
  `rippou_flag` tinyint(4) DEFAULT NULL COMMENT '立法資料',
  `doctor_flag` tinyint(4) DEFAULT NULL COMMENT '博士論文',
  `standard_id` text COMMENT '原資料の標準番号',
  `title` text COMMENT 'タイトル',
  `title_yomi` text COMMENT 'タイトルのヨミ',
  `series_title` text COMMENT 'シリーズタイトル',
  `series_title_yomi` text COMMENT 'シリーズタイトルのヨミ',
  `betu_series_title` text COMMENT 'タイトル',
  `betu_series_title_yomi` text COMMENT 'タイトルのヨミ',
  `betu_title` text COMMENT '別タイトル',
  `betu_title_yomi` text COMMENT '別タイトルのヨミ',
  `naiyo_saimoku_chosha` text COMMENT '内容細目タイトル',
  `naiyo_saimoku_title` text COMMENT '内容細目タイトルのヨミ',
  `naiyo_saimoku_title_yomi` text COMMENT '内容細目著者',
  `buhenmei` text COMMENT '部編名',
  `buhenmei_yomi` text COMMENT '部編名のヨミ',
  `makiji_bango` text COMMENT '巻次・部編番号',
  `makiji_bango_yomi` text COMMENT '巻次・部編番号のヨミ',
  `contributor` text COMMENT '寄与者（寄贈者）',
  `contributor_yomi` text COMMENT '寄与者（寄贈者）のヨミ',
  `creator` text COMMENT '寄与者（寄贈者）',
  `creator_yomi` text COMMENT '寄与者（寄贈者）のヨミ',
  `iban` text COMMENT '異版名(第x版）',
  `iban_chosha` text COMMENT '異版の著者名',
  `publisher` text COMMENT '出版社・公開者',
  `keyword` text COMMENT 'サブジェクト（キーワード）',
  `chuuki` text COMMENT '注記等',
  `youyaku` text COMMENT '要約',
  `mokuji` text COMMENT '目次',
  `sakusei_nen` int(11) DEFAULT NULL COMMENT '作成・撮影日',
  `sakusei_tuki` int(11) DEFAULT NULL COMMENT '作成・撮影日',
  `sakusei_bi` int(11) DEFAULT NULL COMMENT '作成・撮影日',
  `online_nen` int(11) DEFAULT NULL COMMENT 'Online資料採取日',
  `online_tuki` int(11) DEFAULT NULL COMMENT 'Online資料採取日',
  `online_bi` int(11) DEFAULT NULL COMMENT 'Online資料採取日',
  `koukai_nen` int(11) DEFAULT NULL COMMENT '公開日',
  `koukai_tuki` int(11) DEFAULT NULL COMMENT '公開日',
  `koukai_bi` int(11) DEFAULT NULL COMMENT '公開日',
  `language` text COMMENT '言語',
  `is_bubun` text COMMENT '引用資料 ～の一部分である',
  `oya_uri` text COMMENT '引用資料 親URIへの参照',
  `shigen_mei` text COMMENT '引用資料 参照する情報資源の名称',
  `has_bubun` text COMMENT '引用資料 ～を一部分として持つ',
  `ko_uri` text COMMENT '引用資料 子URIへの参照',
  `taisho_basho_uri` text COMMENT '情報資源が対象とする場所(URI)',
  `taisho_basho_ken` text COMMENT '情報資源が対象とする場所（県名）',
  `taisho_basho_shi` text COMMENT '情報資源が対象とする場所（市町村）',
  `taisho_basho_banchi` text COMMENT '情報資源が対象とする場所（街路番地）',
  `taisho_basho_ido` text COMMENT '情報資源が対象とする場所（緯度）',
  `taisho_basho_keido` text COMMENT '情報資源が対象とする場所（経度）',
  `satuei_ken` text COMMENT '撮影場所（県）',
  `satuei_shi` text COMMENT '撮影場所（市町村）',
  `satuei_banchi` text COMMENT '撮影場所（街路番地）',
  `satuei_keido` text COMMENT '撮影場所（経度）',
  `satusei_ido` text COMMENT '撮影場所（緯度）',
  `kanko_hindo` text COMMENT '刊行頻度',
  `kanko_status` char(1) DEFAULT NULL COMMENT '刊行状態',
  `kanko_kanji` text COMMENT '刊行巻次',
  `doctor` text COMMENT '博士論文 学位',
  `doctor_bango` text COMMENT '博士論文 報告番号',
  `doctor_nen` int(11) DEFAULT NULL COMMENT '博士論文 年',
  `doctor_tuki` int(11) DEFAULT NULL COMMENT '博士論文 月',
  `doctor_bi` int(11) DEFAULT NULL COMMENT '博士論文 日',
  `doctor_daigaku` text COMMENT '博士論文 授与大学',
  `doctor_daigaku_yomi` text COMMENT '博士論文 授与大学のヨミ',
  `keisai_go1` text COMMENT '通巻番号等 掲載通号',
  `keisai_go2` text COMMENT '通巻番号等 掲載号',
  `keisai_shimei` text COMMENT '通巻番号等 掲載誌名',
  `keisai_kan` text COMMENT '通巻番号等 掲載巻（論文の場合）',
  `keisai_page` text COMMENT '通巻番号等 掲載ページ',
  `open_level` text COMMENT '公開レベル',
  `license_info` text COMMENT 'ライセンス情報',
  `license_uri` text COMMENT 'URIへの参照',
  `license_holder` text COMMENT 'ライセンス保有者名',
  `license_chuki` text COMMENT '権利・利用条件に関する注記',
  `origina_shiryo_keitai` text COMMENT '資料形態',
  `hakubutu_kubun` text COMMENT '博物資料の区分',
  `shosha_flag` tinyint(4) DEFAULT NULL COMMENT '書写資料',
  `online_flag` tinyint(4) DEFAULT NULL COMMENT 'オンラインジャーナル',
  `teller` text COMMENT '話者',
  `teller_yomi` text COMMENT '話者のヨミ',
  `haifu_basho` text COMMENT '配布場所',
  `haifu_basho_yomi` text COMMENT '配布場所のヨミ',
  `haifu_nen` int(11) DEFAULT NULL COMMENT '配付日時',
  `haifu_tuki` int(11) DEFAULT NULL COMMENT '配付日時',
  `haifu_bi` int(11) DEFAULT NULL COMMENT '配付日時',
  `haifu_taisho` text COMMENT '配布対象（被災者等）',
  `keiji_basho` text COMMENT '配布場所',
  `keiji_basho_yomi` text COMMENT '配布場所のヨミ',
  `keiji_nen` int(11) DEFAULT NULL COMMENT '掲示・配付日時',
  `keiji_tuki` int(11) DEFAULT NULL COMMENT '掲示・配付日時',
  `keiji_bi` int(11) DEFAULT NULL COMMENT '掲示・配付日時',
  `shoshi_flag` int(11) DEFAULT NULL COMMENT '書誌データ',
  `chizu_kubun` text COMMENT '地図か地図帳か',
  `seigen` tinyint(4) DEFAULT NULL COMMENT '情報の質 0:該当しない,1:悲惨（閲覧注意）',
  PRIMARY KEY (`uniqid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `metadata`
--

INSERT INTO `metadata` (`uniqid`, `md_type`, `series_flag`, `betu_title_flag`, `kiyo_flag`, `iban_flag`, `license_flag`, `inyou_flag`, `gov_issue`, `gov_issue_2`, `gov_issue_chihou`, `gov_issue_miyagi`, `for_handicapped`, `original_shiryo_keitai`, `rippou_flag`, `doctor_flag`, `standard_id`, `title`, `title_yomi`, `series_title`, `series_title_yomi`, `betu_series_title`, `betu_series_title_yomi`, `betu_title`, `betu_title_yomi`, `naiyo_saimoku_chosha`, `naiyo_saimoku_title`, `naiyo_saimoku_title_yomi`, `buhenmei`, `buhenmei_yomi`, `makiji_bango`, `makiji_bango_yomi`, `contributor`, `contributor_yomi`, `creator`, `creator_yomi`, `iban`, `iban_chosha`, `publisher`, `subject`, `chuuki`, `youyaku`, `mokuji`, `sakusei_nen`, `sakusei_tuki`, `sakusei_bi`, `online_nen`, `online_tuki`, `online_bi`, `koukai_nen`, `koukai_tuki`, `koukai_bi`, `language`, `is_bubun`, `oya_uri`, `shigen_mei`, `has_bubun`, `ko_uri`, `taisho_basho_uri`, `taisho_basho_ken`, `taisho_basho_shi`, `taisho_basho_banchi`, `taisho_basho_ido`, `taisho_basho_keido`, `satuei_ken`, `satuei_shi`, `satuei_banchi`, `satuei_keido`, `satusei_ido`, `kanko_hindo`, `kanko_status`, `kanko_kanji`, `doctor`, `doctor_bango`, `doctor_nen`, `doctor_tuki`, `doctor_bi`, `doctor_daigaku`, `doctor_daigaku_yomi`, `keisai_go1`, `keisai_go2`, `keisai_shimei`, `keisai_kan`, `keisai_page`, `open_level`, `license_info`, `license_uri`, `license_holder`, `license_chuki`, `origina_shiryo_keitai`, `hakubutu_kubun`, `shosha_flag`, `online_flag`, `teller`, `teller_yomi`, `haifu_basho`, `haifu_basho_yomi`, `haifu_nen`, `haifu_tuki`, `haifu_bi`, `haifu_taisho`, `keiji_basho`, `keiji_basho_yomi`, `keiji_nen`, `keiji_tuki`, `keiji_bi`, `shoshi_flag`, `chizu_kubun`, `seigen`) VALUES
(520501000001, '図書', 0, 0, 0, 0, 0, 0, '', '該当しない', '該当しない', '', '', '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '', '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 'JPN', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'u', '', '', '', 0, 0, 0, '', '', '', '', '', '', '', '0', '', '', '', '', '', '', 0, 0, '', '', '', '', 0, 0, 0, '', '', '', 0, 0, 0, 0, '1', 0),
(520501000002, '', 0, 0, 0, 0, 0, 0, '', '該当しない', '該当しない', '', '', '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '', '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 'JPN', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'u', '', '', '', 0, 0, 0, '', '', '', '', '', '', '', '0', '', '', '', '', '', '', 0, 0, '', '', '', '', 0, 0, 0, '', '', '', 0, 0, 0, 0, '1', 0);

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `user_memo` text,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`userid`, `username`, `password`, `user_memo`) VALUES
(1, 'user1', 'user1', NULL),
(2, 'user2', 'user2', NULL);

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `lot`
--
ALTER TABLE `lot`
  ADD CONSTRAINT `lot_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE SET NULL;

--
-- テーブルの制約 `lotfile`
--
ALTER TABLE `lotfile`
  ADD CONSTRAINT `lotfile_ibfk_1` FOREIGN KEY (`lotid`) REFERENCES `lot` (`lotid`) ON DELETE SET NULL;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
