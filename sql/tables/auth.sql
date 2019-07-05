CREATE TABLE tb_auth_ci_sessions
(
  id varchar(40) NOT NULL PRIMARY KEY,
  ip_address varchar(60) NOT NULL,
  "timestamp" integer NOT NULL DEFAULT 0,
  data bytea NOT NULL
);

CREATE INDEX tb_auth_ci_sessions_timestamp
  ON tb_auth_ci_sessions
  USING btree
  ("timestamp");

CREATE TABLE tb_auth_denied_access
(
  ai serial NOT NULL PRIMARY KEY,
  ip_address varchar(60) NOT NULL,
  "time" timestamp without time zone NOT NULL,
  reason_code smallint NOT NULL DEFAULT 0
);

CREATE TABLE tb_auth_ips_on_hold
(
  ai serial NOT NULL PRIMARY KEY,
  ip_address varchar(60) NOT NULL,
  "time" timestamp without time zone NOT NULL
);

CREATE TABLE tb_auth_login_errors
(
  ai serial NOT NULL PRIMARY KEY,
  username_or_email varchar(255) NOT NULL,
  ip_address varchar(60) NOT NULL,
  "time" timestamp without time zone NOT NULL
);

CREATE TABLE tb_auth_sessions
(
  id varchar(40) NOT NULL PRIMARY KEY,
  user_id bigint NOT NULL,
  login_time timestamp without time zone,
  ip_address varchar(60) NOT NULL,
  user_agent varchar(60) DEFAULT NULL::varchar,
  modified_at timestamp without time zone DEFAULT '2016-04-17 12:42:31.466'::timestamp without time zone
);

CREATE TABLE tb_auth_user_histories
(
  user_id bigint NOT NULL,
  datetime_log timestamp without time zone NOT NULL DEFAULT now(),
  status varchar(20),
  remarks varchar(255),
  reference_table varchar(60),
  reference_id bigint,
  CONSTRAINT tb_auth_user_histories_pkey PRIMARY KEY (user_id, datetime_log)
);

CREATE TABLE tb_auth_username_or_email_on_hold
(
  ai serial NOT NULL PRIMARY KEY,
  username_or_email varchar(255) NOT NULL,
  "time" timestamp without time zone NOT NULL
);

CREATE TYPE banned_bool AS ENUM
  ('0', '1');

CREATE OR REPLACE FUNCTION null_safe_cmp(varchar, varchar)
  RETURNS integer AS
  'SELECT CASE WHEN $1 IS NULL AND $2 IS NULL THEN 1 WHEN ($1 IS NULL AND $2 IS NOT NULL)OR ($1 IS NOT NULL AND $2 IS NULL) THEN 0 ELSE CASE WHEN $1 = $2 THEN 1 ELSE 0 END END;'
  LANGUAGE sql IMMUTABLE
  COST 100;

CREATE TABLE tb_auth_users
(
  user_id bigint NOT NULL PRIMARY KEY,
  person_name varchar(100) NOT NULL,
  user_code varchar(10) UNIQUE,
  username varchar(40) NOT NULL UNIQUE,
  email varchar(255) NOT NULL UNIQUE,
  warehouse varchar(60),
  CONSTRAINT tb_auth_users_warehouse FOREIGN KEY (warehouse)
    REFERENCES tb_master_warehouses (warehouse) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  auth_level smallint NOT NULL,
  banned banned_bool NOT NULL DEFAULT '0'::banned_bool,
  passwd varchar(60) NOT NULL,
  passwd_recovery_code varchar(60) DEFAULT NULL::varchar,
  passwd_recovery_date timestamp without time zone,
  passwd_modified_at timestamp without time zone,
  last_login timestamp without time zone,
  created_at timestamp without time zone NOT NULL,
  modified_at timestamp without time zone
);

CREATE OR REPLACE FUNCTION ca_passwd_modified()
  RETURNS trigger AS
  'BEGIN IF (null_safe_cmp(NEW.passwd, OLD.passwd) = 0) THEN NEW.passwd_modified_at := current_timestamp; END IF;RETURN NEW;END;'
  LANGUAGE plpgsql VOLATILE
  COST 100;

CREATE TRIGGER ca_passwd_trigger
  BEFORE UPDATE
  ON tb_auth_users
  FOR EACH ROW
  EXECUTE PROCEDURE ca_passwd_modified();

CREATE TABLE tb_auth_user_categories
(
  id serial NOT NULL PRIMARY KEY,
  username varchar(40) NOT NULL,
  CONSTRAINT tb_auth_user_categories_username FOREIGN KEY (username)
    REFERENCES tb_auth_users (username) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  category varchar(60) NOT NULL,
  CONSTRAINT tb_auth_user_categories_category FOREIGN KEY (category)
    REFERENCES tb_master_item_categories (category) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE
);
