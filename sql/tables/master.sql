-- DROP tb_settings
CREATE TABLE tb_settings
(
  id serial NOT NULL PRIMARY KEY,
  setting_name varchar(255) NOT NULL UNIQUE,
  setting_value varchar(255),
  setting_group varchar(60),
  updated_at timestamp(0) without time zone NOT NULL DEFAULT now(),
  updated_by varchar(100)
);

CREATE TABLE tb_master_item_categories
(
  id serial NOT NULL PRIMARY KEY,
  code varchar(20) UNIQUE,
  category varchar(60) NOT NULL UNIQUE,
  item_type varchar(20) NOT NULL DEFAULT 'INVENTORY'::varchar,
  status varchar(20) NOT NULL DEFAULT 'AVAILABLE'::varchar,
  notes text,
  created_at timestamp(0) without time zone NOT NULL DEFAULT now(),
  updated_at timestamp(0) without time zone NOT NULL DEFAULT now(),
  created_by varchar(100),
  updated_by varchar(100)
);

CREATE TABLE tb_master_item_groups
(
  id serial NOT NULL PRIMARY KEY,
  code varchar(20) UNIQUE,
  "group" varchar(60) NOT NULL UNIQUE,
  category varchar(60),
  CONSTRAINT tb_master_item_groups_category FOREIGN KEY (category)
    REFERENCES tb_master_item_categories (category) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  status varchar(20) NOT NULL DEFAULT 'AVAILABLE'::varchar,
  notes text,
  created_at timestamp without time zone NOT NULL DEFAULT now(),
  updated_at timestamp without time zone NOT NULL DEFAULT now(),
  created_by varchar(100),
  updated_by varchar(100)
);

CREATE TABLE tb_master_item_units
(
  id serial NOT NULL PRIMARY KEY,
  unit varchar(20) NOT NULL UNIQUE,
  description text,
  status varchar(20) NOT NULL DEFAULT 'AVAILABLE'::varchar,
  notes text,
  created_at timestamp without time zone NOT NULL DEFAULT now(),
  updated_at timestamp without time zone NOT NULL DEFAULT now(),
  created_by varchar(100),
  updated_by varchar(100)
);

CREATE TABLE tb_master_item_conditions
(
  id serial NOT NULL PRIMARY KEY,
  code varchar(20) NOT NULL UNIQUE,
  condition varchar(20) NOT NULL UNIQUE,
  notes text,
  status varchar(20) NOT NULL DEFAULT 'AVAILABLE'::varchar,
  created_at timestamp without time zone NOT NULL DEFAULT now(),
  updated_at timestamp without time zone NOT NULL DEFAULT now(),
  created_by varchar(100),
  updated_by varchar(100)
);

CREATE TABLE tb_master_items
(
  id bigserial NOT NULL PRIMARY KEY,
  "group" varchar(60),
  CONSTRAINT tb_master_items_group FOREIGN KEY ("group")
    REFERENCES tb_master_item_groups ("group") MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  part_number varchar(100) NOT NULL UNIQUE,
  alternate_part_number varchar(100),
  description varchar(100) NOT NULL,
  minimum_quantity numeric(15,2) NOT NULL DEFAULT 0.00,
  unit varchar(20) NOT NULL,
  status varchar(20) NOT NULL DEFAULT 'AVAILABLE'::varchar,
  CONSTRAINT tb_master_items_unit FOREIGN KEY (unit)
    REFERENCES tb_master_item_units (unit) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  notes text,
  created_at timestamp without time zone NOT NULL DEFAULT now(),
  updated_at timestamp without time zone NOT NULL DEFAULT now(),
  created_by varchar(100),
  updated_by varchar(100)
);

CREATE TABLE tb_master_warehouses
(
  id serial NOT NULL PRIMARY KEY,
  code varchar(20) UNIQUE,
  warehouse varchar(60) NOT NULL UNIQUE,
  address text,
  status varchar(20) NOT NULL DEFAULT 'AVAILABLE'::varchar,
  notes text,
  created_at timestamp without time zone NOT NULL DEFAULT now(),
  updated_at timestamp without time zone NOT NULL DEFAULT now(),
  created_by varchar(100),
  updated_by varchar(100)
);

CREATE TABLE tb_master_stores
(
  id serial NOT NULL PRIMARY KEY,
  stores varchar(60) NOT NULL UNIQUE,
  warehouse varchar(60) NOT NULL,
  CONSTRAINT tb_master_stores_warehouse FOREIGN KEY (warehouse)
    REFERENCES tb_master_warehouses (warehouse) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  category varchar(60) NOT NULL,
  CONSTRAINT tb_master_stores_category FOREIGN KEY (category)
    REFERENCES tb_master_item_categories (category) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  adress text,
  status varchar(20) NOT NULL DEFAULT 'AVAILABLE'::varchar,
  notes text,
  created_at timestamp without time zone NOT NULL DEFAULT now(),
  updated_at timestamp without time zone NOT NULL DEFAULT now(),
  created_by varchar(100),
  updated_by varchar(100)
);

CREATE TABLE tb_master_item_serials
(
  id serial NOT NULL PRIMARY KEY,
  item_id bigint NOT NULL,
  CONSTRAINT tb_master_item_serials_masteritemid FOREIGN KEY (item_id)
    REFERENCES tb_master_items (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  serial_number varchar(100) NOT NULL,
  CONSTRAINT tb_master_item_serials_uniquegroup UNIQUE (item_id, serial_number),
  warehouse varchar(60) NOT NULL,
  CONSTRAINT tb_master_item_serials_warehouse FOREIGN KEY (warehouse)
    REFERENCES tb_master_warehouses (warehouse) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  stores varchar(60) NOT NULL,
  CONSTRAINT tb_master_item_serials_stores FOREIGN KEY (stores)
    REFERENCES tb_master_stores (stores) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  condition varchar(20) NOT NULL,
  CONSTRAINT tb_master_item_serials_condition FOREIGN KEY (condition)
    REFERENCES tb_master_item_conditions (condition) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  quantity numeric(15,2) NOT NULL DEFAULT 0.00,
  updated_at timestamp without time zone NOT NULL DEFAULT now(), -- last update datetime
  updated_by varchar(100) -- last update person
);

CREATE TABLE tb_master_vendors
(
  id serial NOT NULL PRIMARY KEY,
  code varchar(20) UNIQUE,
  vendor varchar(100) NOT NULL UNIQUE,
  address text,
  email varchar(100),
  phone varchar(20),
  status varchar(20) NOT NULL DEFAULT 'AVAILABLE'::varchar,
  notes text,
  created_at timestamp without time zone NOT NULL DEFAULT now(),
  updated_at timestamp without time zone NOT NULL DEFAULT now(),
  created_by varchar(100),
  updated_by varchar(100)
);

CREATE TABLE tb_master_vendor_categories
(
  id serial NOT NULL PRIMARY KEY,
  vendor varchar(100) NOT NULL,
  CONSTRAINT tb_master_vendor_categories_vendor FOREIGN KEY (vendor)
    REFERENCES tb_master_vendors (vendor) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  category varchar(60) NOT NULL,
  CONSTRAINT tb_master_vendor_categories_category FOREIGN KEY (category)
    REFERENCES tb_master_item_categories (category) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE
);
