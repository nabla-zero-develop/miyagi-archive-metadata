alter table metadata change column subject keyword text COMMENT 'サブジェクト（キーワード）';
alter table metadata change column media_code original_shiryo_keitai text COMMENT 'オリジナル資料の形態';

alter table lotfile add column cdcode int;
update lotfile set cdcode = floor(uniqid/1000000);

alter table lotfile add column finish_date datetime;
update lotfile set finish_date = now() where finish = 1;