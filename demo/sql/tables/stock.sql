-- GENERAL STOCK
CREATE TABLE tb_inventories
(
  id bigserial NOT NULL PRIMARY KEY,
  document_number character varying(20), -- GRN Number
  CONSTRAINT tb_inventories_documentnumber FOREIGN KEY (document_number)
    REFERENCES tb_doc_receipts (document_number) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  item_id character varying(50) NOT NULL,
  CONSTRAINT tb_inventories_itemid FOREIGN KEY (item_id)
    REFERENCES tb_items (item_id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  serial_number character varying(50),
  CONSTRAINT tb_inventory_stores_stores_serialnumber FOREIGN KEY (serial_number)
    REFERENCES tb_item_serial_numbers (serial_number) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  item_condition character varying(20) NOT NULL DEFAULT 'SERVICEABLE'::character varying,
  expired_date date,
  unit_value numeric(15,2) DEFAULT 0.00,
  order_number character varying(20), -- PO Number
  reference_number character varying(50), -- INV/REF
  awb_number character varying(50), -- AIRWAY BILL
  created_at timestamp without time zone DEFAULT now(),
  created_by character varying(50)
);

-- IN STORES
CREATE TABLE tb_inventory_stores
(
  id bigserial NOT NULL PRIMARY KEY,
  inventory_id bigint NOT NULL, -- tb_inventories id
  CONSTRAINT tb_inventory_stores_inventoryid FOREIGN KEY (inventory_id)
    REFERENCES tb_inventories (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  warehouse character varying(20) NOT NULL,
  CONSTRAINT tb_inventory_stores_warehouse FOREIGN KEY (warehouse)
    REFERENCES tb_warehouses (warehouse) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  stores character varying(50) NOT NULL,
  CONSTRAINT tb_inventory_stores_stores FOREIGN KEY (stores)
    REFERENCES tb_stores (stores) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  quantity numeric(15,2) NOT NULL DEFAULT 1.00,
  reference_document character varying(20), -- Related Document Number (GRN/SD/DP)
  received_date date,
  received_by character varying(50),
  notes text,
  created_at timestamp without time zone DEFAULT now(),
  created_by character varying(50)
);

CREATE TABLE tb_inventory_logs
(
  item_id character varying(50) NOT NULL,
  CONSTRAINT tb_inventory_logs_itemid FOREIGN KEY (item_id)
    REFERENCES tb_master_items (item_id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  serial_number character varying(50) DEFAULT NULL::character varying,
  CONSTRAINT tb_inventory_logs_serialnumber FOREIGN KEY (serial_number)
    REFERENCES tb_master_item_serials (serial_number) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  warehouse character varying(20) NOT NULL,
  CONSTRAINT tb_inventory_logs_warehouse FOREIGN KEY (warehouse)
    REFERENCES tb_master_warehouses (warehouse) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  stores character varying(50) DEFAULT NULL::character varying,
  CONSTRAINT tb_inventory_logs_stores FOREIGN KEY (stores)
    REFERENCES tb_master_stores (stores) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  date_of_entry timestamp without time zone DEFAULT now(),
  document_type character varying(20) NOT NULL,
  document_number character varying(20) DEFAULT NULL::character varying,
  received_from character varying(50) DEFAULT NULL::character varying,
  received_by character varying(50) DEFAULT NULL::character varying,
  issued_to character varying(50) DEFAULT NULL::character varying,
  issued_by character varying(50) DEFAULT NULL::character varying,
  item_condition character varying(20) DEFAULT 'SERVICEABLE'::character varying,
  quantity numeric(15,2) DEFAULT 0.00,
  balance_quantity numeric(15,2) DEFAULT 0.00,
  unit_value numeric(15,2) DEFAULT 0.00,
  average_value numeric(15,2) DEFAULT 0.00,
  notes text
);
