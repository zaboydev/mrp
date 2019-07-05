-- SETTING
INSERT INTO tb_settings(setting_name, setting_value, setting_group, updated_by) VALUES
  ('MAIN BASE', 'WISNU', 'GENERAL', 'Umar Satrio'),
  ('ACTIVE_YEAR', '2017', 'PERIOD', 'Umar Satrio'),
  ('ACTIVE_MONTH', '1', 'PERIOD', 'Umar Satrio');


-- ITEM CONDITION
INSERT INTO tb_master_item_conditions(code, condition, created_by, updated_by) VALUES
  ('S/S', 'SERVICEABLE', 'Umar Satrio', 'Umar Satrio'),
  ('U/S', 'UNSERVICEABLE', 'Umar Satrio', 'Umar Satrio'),
  ('R/J', 'REJECT', 'Umar Satrio', 'Umar Satrio');


-- ITEM CATEGORY
INSERT INTO tb_master_item_categories (code, category, created_by, updated_by) VALUES
  ('SPR', 'SPARE PART', 'Umar Satrio', 'Umar Satrio'),
  ('ME', 'SPARE PART ME', 'Umar Satrio', 'Umar Satrio'),
  ('TLS', 'TOOLS', 'Umar Satrio', 'Umar Satrio'),
  ('BB', 'BAHAN BAKAR', 'Umar Satrio', 'Umar Satrio'),
  ('CAG', 'CAMPUS GOODS', 'Umar Satrio', 'Umar Satrio'),
  ('OFG', 'OFFICE GOODS', 'Umar Satrio', 'Umar Satrio');


-- ITEM GROUP
INSERT INTO tb_master_item_groups ("group", created_by, updated_by, code, category) VALUES ('FUEL', 'Umar Satrio', 'Umar Satrio', 'FUEL', 'BAHAN BAKAR');
INSERT INTO tb_master_item_groups ("group", status, created_by, updated_by, code, category) VALUES ('CONSUMABLE PART', 'AVAILABLE', 'Umar Satrio', 'Umar Satrio', 'COP', 'SPARE PART');
INSERT INTO tb_master_item_groups ("group", status, created_by, updated_by, code, category) VALUES ('ROTABLE PART', 'AVAILABLE', 'Umar Satrio', 'Umar Satrio', 'ROP', 'SPARE PART');
INSERT INTO tb_master_item_groups ("group", status, created_by, updated_by, code, category) VALUES ('TOOLS', 'AVAILABLE', 'Umar Satrio', 'Umar Satrio', 'TLS', 'TOOLS');
INSERT INTO tb_master_item_groups ("group", status, created_by, updated_by, code, category) VALUES ('REPAIRABLE PART', 'AVAILABLE', 'Bobby Aldan', 'Bobby Aldan', 'RER', 'SPARE PART');

INSERT INTO tb_auth_users (user_id, username, email, auth_level, banned, passwd, person_name, warehouse, created_at) VALUES
  (2145538920,'administrator','umar@baliflightacademy.com',5,'0','$2y$11$lUb2G6Ak.rBDGfdDuIhfZulgInQKX4xo7MJ75OEQPB4bp8J3CItaK','Umar Satrio','WISNU', now()),
  (2147484848,'bobby','bobby@baliflightacademy.com',7,'0','$2y$11$tQ6AUMLT2YlgUz3mQBQYveJyPlL3zTJvNakp5G3IUJpwXnRi.11zi','Bobby Aldan','WISNU', now()),
  (1148798805,'almaga','banyuwangibase@baliflightacademy.com',7,'0','$2y$11$TwVtM/HDoAo.3Q3FNZws6u0x2fFuH1h/jK82EDFYpmpDs6DP9ZX4y','BANYUWANGI BASE','BANYUWANGI', now()),
  (1482961669,'solobase','admin@yahoo.com',7,'0','$2y$11$IxxpeGganPB5CLQtj3JGJuKJYAgS2/e.XeH7ks.oMvDa.FO08DoAC','SOLO BASE','SOLO', now()),
  (941371708,'jemberbase','jemberbase@baliflightacademy.com',7,'0','$2y$11$1qqmiHtweD60Z3M8tbqRqeT5yd.UIAHn/LEzG3k/Fv5y0fuwaiJy2','JEMBER BASE','JEMBER', now()),
  (462378846,'lombokbase','lombokbase@baliflightacademy.com',7,'0','$2y$11$Rb110X0JO0iR6ku0UlCLsuZOrNaXO.B6wh3xbsO9MAPUWdtGI235K','LOMBOK BASE','LOMBOK', now()),
  (1378242314,'umarsatrio','umar@gmail.com',8,'0','$2y$11$u.1yoJVq/lD.xogbIU2lIukvZ9F0AGIhiJCwWM6XNSnBsKdEpGPoG','Umar Satrio','WISNU', now()),
  (645887575,'nyomansukadana','nyomansukadana@baliflightacademy.com',1,'0','$2y$11$FN7iG9TpSQ/5RrvKd2t1iu/pwQQcT8jUgG1THPy499GR1cfxxlpUm','Nyoman Sukadana','WISNU', now());


-- AUTH USER CATEGORY
INSERT INTO tb_auth_user_categories (category, username) VALUES ('BAHAN BAKAR', 'almaga');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('BAHAN BAKAR', 'bobby');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('BAHAN BAKAR', 'jemberbase');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('BAHAN BAKAR', 'lombokbase');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('BAHAN BAKAR', 'solobase');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('CAMPUS GOODS', 'almaga');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('CAMPUS GOODS', 'bobby');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('CAMPUS GOODS', 'jemberbase');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('CAMPUS GOODS', 'lombokbase');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('CAMPUS GOODS', 'solobase');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('OFFICE GOODS', 'almaga');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('OFFICE GOODS', 'bobby');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('OFFICE GOODS', 'jemberbase');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('OFFICE GOODS', 'lombokbase');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('OFFICE GOODS', 'solobase');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('SPARE PART', 'almaga');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('SPARE PART', 'bobby');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('SPARE PART', 'jemberbase');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('SPARE PART', 'lombokbase');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('SPARE PART', 'solobase');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('TOOLS', 'almaga');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('TOOLS', 'bobby');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('TOOLS', 'jemberbase');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('TOOLS', 'lombokbase');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('TOOLS', 'solobase');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('SPARE PART ME', 'almaga');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('SPARE PART ME', 'bobby');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('SPARE PART ME', 'jemberbase');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('SPARE PART ME', 'lombokbase');
INSERT INTO tb_auth_user_categories (category, username) VALUES ('SPARE PART ME', 'solobase');


-- WAREHOUSE
INSERT INTO tb_master_warehouses (code, warehouse, notes, created_by, updated_by) VALUES
  ('WSN', 'WISNU', 'This is Main Warehouse/Base. You can assign main base in menu setting.', 'Umar Satrio', 'Umar Satrio'),
  ('BSR', 'BANYUWANGI', 'Blimbing Sari', 'Umar Satrio', 'Umar Satrio'),
  ('SOC', 'SOLO', 'Solo Bandara Adi Sumarmo', 'Umar Satrio', 'Umar Satrio'),
  ('LOP', 'LOMBOK', 'Lombok Praya', 'Umar Satrio', 'Umar Satrio'),
  ('JBB', 'JEMBER', 'Base in Jember', 'Umar Satrio', 'Umar Satrio');


-- STORES
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('BWI', 'BANYUWANGI', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('JEMBER', 'JEMBER', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('LOMBOK', 'LOMBOK', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('SOLO', 'SOLO', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A1-1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A1-2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A1-3', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A1-4', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A1-5', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A2-1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A2-2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A2-3', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A2-4', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A2-5', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A2-6', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A2-7', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A2-8', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A2-9', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A3-1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A3-10', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A3-11', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A3-12', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A3-13', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A3-14', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A3-15', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A3-16', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A3-17', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A3-18', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A3-19', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A3-2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A3-20', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A3-21', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A3-3', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A3-4', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A3-5', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A3-6', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A3-7', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A3-8', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A3-9', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A4-1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A4-2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A5-1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A5-2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A5-3', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A5-4', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A5-5', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A5-6', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A5-7', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A5-8', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('A5-9', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B1-1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B1-2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B1-3', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B2-1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B2-2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B2-3', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B2-4', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B2-5', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B3', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B3-1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B3-10', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B3-11', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B3-12', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B3-13', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B3-14', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B3-15', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B3-16', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B3-17', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:34', '2016-12-17 04:16:34', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B3-18', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B3-19', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B3-2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B3-20', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B3-3', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B3-4', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B3-5', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B3-6', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B3-7', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B3-8', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B3-9', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B4', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B4-1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B4-2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B4-3', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B5', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B5-1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B5-2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B5-3', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B5-4', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('B5-5', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('BOX RIVET ROOL', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C1-1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C1-2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C1-3', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C1-4', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C1-5', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C2-1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C2-2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C2-3', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C2-4', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C2-5', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C2-6', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C3', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C3-1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C3-2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C3-3', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C3-4', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C3-5', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C4', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C4-1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C4-2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C4-3', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C4-4', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C4-5', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('C5-1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('CONTAINER', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('CUPBOARD DOCUMENT', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D2-1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D2-10', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D2-11', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D2-12', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D2-13', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D2-14', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D2-15', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D2-16', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D2-2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D2-3', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D2-4', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D2-5', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D2-6', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D2-7', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D2-8', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D2-9', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D3', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D3-1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D3-10', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D3-11', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D3-12', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D3-13', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D3-14', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D3-15', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D3-2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D3-3', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D3-4', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D3-5', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D3-6', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D3-7', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D3-8', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D3-9', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D4', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D4-9', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('D5-15', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('E', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('E1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('E2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('E3', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('E4', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('E5', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('F', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('F1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('F2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('F3', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('F4', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('F5', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('FLAMABLE ROOM', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('G1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('G1-1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('G2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('G3', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('G4', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('G5', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('H-5', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('H1-4', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('H4-2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('I4-1', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('I5-2', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('QUARANTINE', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('SPECIAL', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('SPECIAL TOOLS', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('TOOLS', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');
INSERT INTO tb_master_stores (stores, warehouse, notes, status, created_at, updated_at, created_by, updated_by, category) VALUES ('WISNU', 'WISNU', NULL, 'AVAILABLE', '2016-12-17 04:16:35', '2016-12-17 04:16:35', 'Bobby Aldan', 'Bobby Aldan', 'SPARE PART');

-- VENDOR
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ('SOUTHEAST AEROSPACE', '1399 General Aviation Drive Melbourne International Airport Melbourne FL 32935 USA', 'jessica.costa@seaerospace.com', '(321) 255-9877', 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ('AVION LOGISTICS', NULL, NULL, NULL, 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ('PT.REKATAMA PUTRA GEGANA', NULL, NULL, NULL, 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ('PT.GAYA DINAMIKA ANGKASA', NULL, NULL, NULL, 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ('YINGLING AVIATION', NULL, NULL, NULL, 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ( 'PRATAMA MANDIRI', NULL, NULL, NULL, 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ( 'CHEMETALL ASIA PTE LTD', NULL, NULL, NULL, 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ( 'AIRFLITE AUSTRALIA PTY. LTD', NULL, NULL, NULL, 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ( 'MILSPEC', NULL, NULL, NULL, 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ( 'EPIC AVIATION', NULL, NULL, NULL, 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ( 'LUBRINDO JAYA', NULL, NULL, NULL, 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ( 'NAYLOR''S INSTRUMENT SERVICE, INC.', NULL, NULL, NULL, 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ( 'AUSTRALIAN AVIONICS', NULL, NULL, NULL, 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ( 'AVTEK', NULL, NULL, NULL, 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ( 'MERPATI MAINTENANCE FACILITY', NULL, NULL, NULL, 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ( 'LYCOMING ENGINE', NULL, NULL, NULL, 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ( 'MC FARLANE', NULL, NULL, NULL, 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ( 'CONTAINER', NULL, NULL, NULL, 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ( 'PT.EKATAMA PUTRA PERKASA', 'Jln.Boulevard Bukit Gading Raya No.3', 'customer.service@ekatamagroup.com', '(021)4514678', 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ( 'SUMBER CAHAYA PRIMA TEKNIK', 'LTC. LT G#1 Blok B 1 No12 Jln.Hayam Wuruk No127 Glodok jakarta barat', 'marketing@favoritepumps.com', '021-62201570', 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ( 'PIPER AIRCRAFT INC', '2926 Piper Drive Vero Beach, FL 32960 USA', 'sales@piper.com', '772-567-4361', 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ( 'HAWKER PACIFIC PTY LTD', 'DESPATCH 112 Airport Avenue Bankstown Airport 2200 BANKSTOWN AIRPORT NSW', 'aircraftmanagement@hawkerpacific.com', '+612970885555', 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ( 'REDBIRD FLIGHT SIMULATOR', '2301 EAST ST,ELMO ROAD,SUITE 100,AUSTIN,TX 78744,(512)301-0718 AUSTIN ALABAMA UNITED STATES OF AMERICA 78744', 'jmatthyssen@redbirdflight.com', '5123010718', 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ( 'CV. TELADAN GROUP INDONESIA', 'Jl.Moch. Rasyid No.9B, RT.12 RW.03 Kel.Mulyo Rejo, Kec, Sukun Malang Jawa Timur 65147', 'info@HildanSafety.co.id', '085237913060', 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ( 'EDMO ASIA PACIFIC', 'ABN:39 097 627 211 UNIT 2, 18 PENTLAND Rd SALISBURY SOUTH SOUTH AUSTRALIA 5106', 'accounts@edmoap.com.au', '1300133256', 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ('AVIALL PTE LTD', '2 Loyang Lane # 05-02 Singapore 508913', 'neny.hamid@aviall.com', '65 6540 9809  ', 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ( 'AIRCRAFT SPRUCE', '', 'info@aircraftspruce.com', '8932123', 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ('AEROSPACE PRODUCT INTERNATIONAL', 'Unit 3 Maryland Street, Green Meadows Subdivision Mabiga, Mabalacat City, Pampanga 2010 Philippines', 'rmercado@apiworldwide.com', '63 455995490', 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ( 'AEROMIL PACIFIC', '', 'aeromilpacific@gmail.com', '32143454', 'Umar Satrio', 'Umar Satrio');
INSERT INTO tb_master_vendors (vendor, address, email, phone, created_by, updated_by) VALUES ( 'NUVARIAN', 'solo', 'nuvarian@gmail.com', '982343', 'Umar Satrio', 'Umar Satrio');

-- VENDOR CATEGORY
INSERT INTO tb_master_vendor_categories (category, vendor) VALUES ('SPARE PART', 'AEROMIL PACIFIC');
INSERT INTO tb_master_vendor_categories (category, vendor) VALUES ('TOOLS', 'AEROMIL PACIFIC');
INSERT INTO tb_master_vendor_categories (category, vendor) VALUES ('SPARE PART ME', 'AEROMIL PACIFIC');
INSERT INTO tb_master_vendor_categories (category, vendor) VALUES ('OFFICE GOODS', 'NUVARIAN');
