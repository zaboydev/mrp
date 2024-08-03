INSERT INTO tb_master_warehouses(code, warehouse, created_by, updated_by) VALUES
  ('WSN', 'WISNU', 'Umar Satrio', 'Umar Satrio'),
  ('BSR', 'BANYUWANGI', 'Umar Satrio', 'Umar Satrio'),
  ('SOC', 'SOLO', 'Umar Satrio', 'Umar Satrio'),
  ('JBB', 'JEMBER', 'Umar Satrio', 'Umar Satrio'),
  ('LOP', 'LOMBOK', 'Umar Satrio', 'Umar Satrio');

INSERT INTO tb_master_item_categories(item_category, code, item_type, status, created_by, updated_by) VALUES
  ('TOOLS','TLS','inventory','available','Umar Satrio','Umar Satrio'),
  ('BAHAN BAKAR','BB','inventory','available','Umar Satrio','Umar Satrio'),
  ('SPARE PART ME','ME','inventory','available','Umar Satrio','Umar Satrio'),
  ('OFFICE GOODS','OFG','inventory','available','Umar Satrio','Umar Satrio'),
  ('CAMPUS GOODS','CAG','inventory','available','Umar Satrio','Umar Satrio'),
  ('SPARE PART','SPR','inventory','available','Umar Satrio','Umar Satrio');

INSERT INTO tb_auth_users
  (user_id, username, email, auth_level, banned, passwd, person_name, warehouse, created_at)
VALUES (2145538920,'administrator','umar@baliflightacademy.com',5,'0','$2y$11$lUb2G6Ak.rBDGfdDuIhfZulgInQKX4xo7MJ75OEQPB4bp8J3CItaK','Umar Satrio','WISNU', now()),
(2147484848,'bobby','bobby@baliflightacademy.com',7,'0','$2y$11$tQ6AUMLT2YlgUz3mQBQYveJyPlL3zTJvNakp5G3IUJpwXnRi.11zi','Bobby Aldan','WISNU', now()),
(1148798805,'almaga','banyuwangibase@baliflightacademy.com',7,'0','$2y$11$TwVtM/HDoAo.3Q3FNZws6u0x2fFuH1h/jK82EDFYpmpDs6DP9ZX4y','BANYUWANGI BASE','BANYUWANGI', now()),
(1482961669,'solobase','admin@yahoo.com',7,'0','$2y$11$IxxpeGganPB5CLQtj3JGJuKJYAgS2/e.XeH7ks.oMvDa.FO08DoAC','SOLO BASE','SOLO', now()),
(941371708,'jemberbase','jemberbase@baliflightacademy.com',7,'0','$2y$11$1qqmiHtweD60Z3M8tbqRqeT5yd.UIAHn/LEzG3k/Fv5y0fuwaiJy2','JEMBER BASE','JEMBER', now()),
(462378846,'lombokbase','lombokbase@baliflightacademy.com',7,'0','$2y$11$Rb110X0JO0iR6ku0UlCLsuZOrNaXO.B6wh3xbsO9MAPUWdtGI235K','LOMBOK BASE','LOMBOK', now()),
(1378242314,'umarsatrio','umar@gmail.com',8,'0','$2y$11$u.1yoJVq/lD.xogbIU2lIukvZ9F0AGIhiJCwWM6XNSnBsKdEpGPoG','Umar Satrio','WISNU', now()),
(645887575,'nyomansukadana','nyomansukadana@baliflightacademy.com',1,'0','$2y$11$FN7iG9TpSQ/5RrvKd2t1iu/pwQQcT8jUgG1THPy499GR1cfxxlpUm','Nyoman Sukadana','WISNU', now());
