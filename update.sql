alter table metadata add column naiyo_saimoku_chosha_yomi text COMMENT '内容細目著者のヨミ' after naiyo_saimoku_chosha;
alter table metadata change column naiyo_saimoku_chosha naiyo_saimoku_chosha text COMMENT '内容細目著者';
alter table metadata change column naiyo_saimoku_title naiyo_saimoku_title text COMMENT '内容細目タイトル';
alter table metadata change column naiyo_saimoku_title_yomi naiyo_saimoku_title_yomi text COMMENT '内容細目タイトルのヨミ';
alter table metadata_log add column naiyo_saimoku_chosha_yomi text COMMENT '内容細目著者のヨミ' after naiyo_saimoku_chosha;
alter table metadata_log change column naiyo_saimoku_chosha naiyo_saimoku_chosha text COMMENT '内容細目著者';
alter table metadata_log change column naiyo_saimoku_title naiyo_saimoku_title text COMMENT '内容細目タイトル';
alter table metadata_log change column naiyo_saimoku_title_yomi naiyo_saimoku_title_yomi text COMMENT '内容細目タイトルのヨミ';


alter table metadata add column sekou_taisho text COMMENT '施行対象';
alter table metadata add column sekou_nen int COMMENT '施行日時';
alter table metadata add column sekou_tuki int COMMENT '施行日時';
alter table metadata add column sekou_bi int COMMENT '施行日時';
alter table metadata_log add column sekou_taisho text COMMENT '施行対象';
alter table metadata_log add column sekou_nen int COMMENT '施行日時';
alter table metadata_log add column sekou_tuki int COMMENT '施行日時';
alter table metadata_log add column sekou_bi int COMMENT '施行日時';

alter table metadata change column subject keyword text COMMENT 'サブジェクト（キーワード）';
alter table metadata change column media_code original_shiryo_keitai text COMMENT 'オリジナル資料の形態';

alter table lotfile add column cdcode int;
update lotfile set cdcode = floor(uniqid/1000000);

alter table lotfile add column finish_date datetime;
update lotfile set finish_date = now() where finish = 1;

alter table baseinfo add column file_id int NOT NULL after uniqid;
CREATE TABLE baseinfo_file(
id int auto_increment,
filename text,
cdcode int,
PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `metadata_log`;
create table metadata_log(
log_id bigint auto_increment COMMENT 'ログテーブルの通し番号',
log_date timestamp COMMENT '変更のあった日',
log_ip text COMMENT '変更した接続元IPアドレス',
log_user int COMMENT '変更したユーザーのID',
uniqid bigint,
md_type text COMMENT '資料種別',
series_flag tinyint COMMENT 'シリーズ（継続資料）か否か',
betu_title_flag tinyint COMMENT '別タイトルの有無',
kiyo_flag tinyint COMMENT '寄与者（寄贈者）の有無',
iban_flag tinyint COMMENT '異版の有無',
license_flag tinyint COMMENT 'ライセンスの有無',
inyou_flag tinyint COMMENT '引用資料',
gov_issue text COMMENT '政府刊行物・刊行元',
gov_issue_2 text COMMENT '官公庁刊行物',
gov_issue_chihou text COMMENT '地方公共団体刊行物',
gov_issue_miyagi text COMMENT '宮城県内地方公共団体刊行物',
for_handicapped text COMMENT '視聴覚者向け資料',
original_shiryo_keitai text COMMENT 'オリジナル資料の形態',
rippou_flag tinyint COMMENT '立法資料',
doctor_flag tinyint COMMENT '博士論文',
standard_id text COMMENT '原資料の標準番号',
title text COMMENT 'タイトル',
title_yomi text COMMENT 'タイトルのヨミ',
series_title text COMMENT 'シリーズタイトル',
series_title_yomi text COMMENT 'シリーズタイトルのヨミ',
betu_series_title text COMMENT 'タイトル',
betu_series_title_yomi text COMMENT 'タイトルのヨミ',
betu_title text COMMENT '別タイトル',
betu_title_yomi text COMMENT '別タイトルのヨミ',
naiyo_saimoku_chosha text COMMENT '内容細目タイトル',
naiyo_saimoku_title text COMMENT '内容細目タイトルのヨミ',
naiyo_saimoku_title_yomi text COMMENT '内容細目著者',
buhenmei text COMMENT '部編名',
buhenmei_yomi text COMMENT '部編名のヨミ',
makiji_bango text COMMENT '巻次・部編番号',
makiji_bango_yomi text COMMENT '巻次・部編番号のヨミ',
contributor text COMMENT '寄与者（寄贈者）',
contributor_yomi text COMMENT '寄与者（寄贈者）のヨミ',
creator text COMMENT '寄与者（寄贈者）',
creator_yomi text COMMENT '寄与者（寄贈者）のヨミ',
iban text COMMENT '異版名(第x版）',
iban_chosha text COMMENT '異版の著者名',
publisher text COMMENT '出版社・公開者',
keyword text COMMENT 'サブジェクト（キーワード）',
chuuki text COMMENT '注記等',
youyaku text COMMENT '要約',
mokuji text COMMENT '目次',
sakusei_nen int COMMENT '作成・撮影日',
sakusei_tuki int COMMENT '作成・撮影日',
sakusei_bi int COMMENT '作成・撮影日',
online_nen int COMMENT 'Online資料採取日',
online_tuki int COMMENT 'Online資料採取日',
online_bi int COMMENT 'Online資料採取日',
koukai_nen int COMMENT '公開日',
koukai_tuki int COMMENT '公開日',
koukai_bi int COMMENT '公開日',
language text COMMENT '言語',
is_bubun text COMMENT '引用資料 ～の一部分である',
oya_uri text COMMENT '引用資料 親URIへの参照',
shigen_mei text COMMENT '引用資料 参照する情報資源の名称',
has_bubun text COMMENT '引用資料 ～を一部分として持つ',
ko_uri text COMMENT '引用資料 子URIへの参照',
taisho_basho_uri text COMMENT '情報資源が対象とする場所(URI)',
taisho_basho_ken text COMMENT '情報資源が対象とする場所（県名）',
taisho_basho_shi text COMMENT '情報資源が対象とする場所（市町村）',
taisho_basho_banchi text COMMENT '情報資源が対象とする場所（街路番地）',
taisho_basho_ido text COMMENT '情報資源が対象とする場所（緯度）',
taisho_basho_keido text COMMENT '情報資源が対象とする場所（経度）',
satuei_ken text COMMENT '撮影場所（県）',
satuei_shi text COMMENT '撮影場所（市町村）',
satuei_banchi text COMMENT '撮影場所（街路番地）',
satuei_keido text COMMENT '撮影場所（経度）',
satusei_ido text COMMENT '撮影場所（緯度）',
kanko_hindo text COMMENT '刊行頻度',
kanko_status char COMMENT '刊行状態',
kanko_kanji text COMMENT '刊行巻次',
doctor text COMMENT '博士論文 学位',
doctor_bango text COMMENT '博士論文 報告番号',
doctor_nen int COMMENT '博士論文 年',
doctor_tuki int COMMENT '博士論文 月',
doctor_bi int COMMENT '博士論文 日',
doctor_daigaku text COMMENT '博士論文 授与大学',
doctor_daigaku_yomi text COMMENT '博士論文 授与大学のヨミ',
keisai_go1 text COMMENT '通巻番号等 掲載通号',
keisai_go2 text COMMENT '通巻番号等 掲載号',
keisai_shimei text COMMENT '通巻番号等 掲載誌名',
keisai_kan text COMMENT '通巻番号等 掲載巻（論文の場合）',
keisai_page text COMMENT '通巻番号等 掲載ページ',
open_level text COMMENT '公開レベル',
license_info text COMMENT 'ライセンス情報',
license_uri text COMMENT 'URIへの参照',
license_holder text COMMENT 'ライセンス保有者名',
license_chuki text COMMENT '権利・利用条件に関する注記',
origina_shiryo_keitai text COMMENT '資料形態',
hakubutu_kubun text COMMENT '博物資料の区分',
shosha_flag tinyint COMMENT '書写資料',
online_flag tinyint COMMENT 'オンラインジャーナル',
teller text COMMENT '話者',
teller_yomi text COMMENT '話者のヨミ',
haifu_basho text COMMENT '配布場所',
haifu_basho_yomi text COMMENT '配布場所のヨミ',
haifu_nen int COMMENT '配付日時',
haifu_tuki int COMMENT '配付日時',
haifu_bi int COMMENT '配付日時',
haifu_taisho text COMMENT '配布対象（被災者等）',
keiji_basho text COMMENT '配布場所',
keiji_basho_yomi text COMMENT '配布場所のヨミ',
keiji_nen int COMMENT '掲示・配付日時',
keiji_tuki int COMMENT '掲示・配付日時',
keiji_bi int COMMENT '掲示・配付日時',
shoshi_flag int COMMENT '書誌データ',
chizu_kubun text COMMENT '地図か地図帳か',
seigen tinyint COMMENT '情報の質 0:該当しない,1:悲惨（閲覧注意）',
skip_reason text COMMENT '入力スキップ理由（作業時用）',
PRIMARY KEY(`log_id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
