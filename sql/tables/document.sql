-- average_value = (SUM(total_value) AS grand_total_value) / total_quantity

-- GENERAL STOCK
CREATE TABLE tb_stocks
(
  id bigserial NOT NULL PRIMARY KEY,
  item_id bigint NOT NULL,
  CONSTRAINT tb_stocks_itemid FOREIGN KEY (item_id)
    REFERENCES tb_master_items (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  condition varchar(20) NOT NULL,
  CONSTRAINT tb_stocks_condition FOREIGN KEY (condition)
    REFERENCES tb_master_item_conditions (condition) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  initial_total_quantity numeric(15,2) NOT NULL DEFAULT 0.00, -- first time import/audit quantity
  total_quantity numeric(15,2) NOT NULL DEFAULT 0.00, -- current quantity
  grand_total_value numeric(15,2) NOT NULL DEFAULT 0.00, -- current average value
  created_at timestamp without time zone NOT NULL DEFAULT now(),
  created_by varchar(100)
);

CREATE TABLE tb_stock_opnames
(
  id bigserial NOT NULL PRIMARY KEY,
  period_year smallint NOT NULL,
  period_month smallint NOT NULL,
  item_id bigint NOT NULL,
  CONSTRAINT tb_stock_opnames_itemid FOREIGN KEY (item_id)
    REFERENCES tb_master_items (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  condition varchar(20) NOT NULL,
  CONSTRAINT tb_stock_opnames_condition FOREIGN KEY (condition)
    REFERENCES tb_master_item_conditions (condition) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  previous_total_quantity numeric(15,2) NOT NULL DEFAULT 0.00, -- previous period total quantity
  previous_grand_total_value numeric(15,2) NOT NULL DEFAULT 0.00, -- previous period average value
  current_total_quantity numeric(15,2) NOT NULL DEFAULT 0.00, -- current quantity
  current_grand_total_value numeric(15,2) NOT NULL DEFAULT 0.00, -- current average value
  created_at timestamp without time zone NOT NULL DEFAULT now(),
  created_by varchar(100)
);

-- IN STORES
CREATE TABLE tb_stock_in_stores
(
  id bigserial NOT NULL PRIMARY KEY,
  stock_id bigint NOT NULL, -- tb_stocks id
  CONSTRAINT tb_stock_in_stores_stockid FOREIGN KEY (stock_id)
    REFERENCES tb_stocks (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  warehouse varchar(60) NOT NULL,
  CONSTRAINT tb_stock_in_stores_warehouse FOREIGN KEY (warehouse)
    REFERENCES tb_master_warehouses (warehouse) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  stores varchar(60) NOT NULL,
  CONSTRAINT tb_stock_in_stores_stores FOREIGN KEY (stores)
    REFERENCES tb_master_stores (stores) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  serial_id int, -- tb_master_item_serials
  CONSTRAINT tb_stock_in_stores_serialid FOREIGN KEY (serial_id)
    REFERENCES tb_master_item_serials (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  initial_quantity numeric(15,2) NOT NULL DEFAULT 1.00,
  initial_unit_value numeric(15,2) NOT NULL DEFAULT 1.00,
  quantity numeric(15,2) NOT NULL DEFAULT 1.00,
  unit_value numeric(15,2) NOT NULL DEFAULT 1.00,
  reference_document varchar(20), -- Related Document Number (GRN/SD/DP)
  expired_date date,
  received_date date,
  received_by varchar(100),
  notes text,
  created_at timestamp without time zone NOT NULL DEFAULT now(),
  created_by varchar(100)
);

CREATE TABLE tb_stock_cards
(
  id bigserial NOT NULL PRIMARY KEY,
  item_id bigint NOT NULL,
  CONSTRAINT tb_stock_cards_itemid FOREIGN KEY (item_id)
    REFERENCES tb_master_items (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  serial_id int,
  CONSTRAINT tb_stock_cards_serialid FOREIGN KEY (serial_id)
    REFERENCES tb_master_item_serials (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  warehouse varchar(60) NOT NULL,
  CONSTRAINT tb_stock_cards_warehouse FOREIGN KEY (warehouse)
    REFERENCES tb_master_warehouses (warehouse) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  stores varchar(60) DEFAULT NULL::varchar,
  CONSTRAINT tb_stock_cards_stores FOREIGN KEY (stores)
    REFERENCES tb_master_stores (stores) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  condition varchar(20) NOT NULL,
  CONSTRAINT tb_stock_cards_condition FOREIGN KEY (condition)
    REFERENCES tb_master_item_conditions (condition) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  date_of_entry timestamp without time zone DEFAULT now(),
  period_year smallint NOT NULL,
  period_month smallint NOT NULL,
  document_type varchar(20) NOT NULL,
  document_number varchar(20),
  received_from varchar(100),
  received_by varchar(100),
  issued_to varchar(100),
  issued_by varchar(100),
  quantity numeric(15,2) NOT NULL DEFAULT 0.00,
  balance_quantity numeric(15,2) NOT NULL DEFAULT 0.00,
  unit_value numeric(15,2) NOT NULL DEFAULT 0.00,
  average_value numeric(15,2) NOT NULL DEFAULT 0.00,
  remarks text,
  created_at timestamp without time zone NOT NULL DEFAULT now(),
  created_by varchar(100)
);

CREATE TABLE tb_stock_adjustments
(
  id bigserial NOT NULL PRIMARY KEY,
  stock_in_stores_id bigint NOT NULL,
  CONSTRAINT tb_stock_adjustments_stockinstoresid FOREIGN KEY (stock_in_stores_id)
    REFERENCES tb_stock_in_stores (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  date_of_entry timestamp without time zone DEFAULT now(),
  period_year smallint NOT NULL,
  period_month smallint NOT NULL,
  previous_quantity numeric(15,2) NOT NULL DEFAULT 0.00,
  adjustment_quantity numeric(15,2) NOT NULL DEFAULT 0.00,
  balance_quantity numeric(15,2) NOT NULL DEFAULT 0.00,
  remarks text,
  created_at timestamp without time zone NOT NULL DEFAULT now(),
  created_by varchar(100)
);

-- Common Name: GOODS RECEIVED NOTE
CREATE TABLE tb_doc_receipts
(
  id bigserial NOT NULL PRIMARY KEY,
  category varchar(60) NOT NULL,
  CONSTRAINT tb_doc_receipts_category FOREIGN KEY (category)
    REFERENCES tb_master_item_categories (category) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  warehouse varchar(60) NOT NULL,
  CONSTRAINT tb_doc_receipt_items_warehouse FOREIGN KEY (warehouse)
    REFERENCES tb_master_warehouses (warehouse) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  document_number varchar(20) NOT NULL UNIQUE,
  received_date date NOT NULL,
  received_by varchar(100),
  received_from varchar(100), -- VENDOR
  CONSTRAINT tb_doc_receipts_receivedfrom FOREIGN KEY (received_from)
    REFERENCES tb_master_vendors (vendor) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  status varchar(20),
  notes text,
  created_at timestamp without time zone NOT NULL DEFAULT now(),
  created_by varchar(100),
  updated_at timestamp without time zone NOT NULL DEFAULT now(),
  updated_by varchar(100)
);

CREATE TABLE tb_doc_receipt_items
(
  id bigserial NOT NULL PRIMARY KEY,
  document_number varchar(20) NOT NULL, -- tb_doc_receipts document_number
  CONSTRAINT tb_doc_receipt_items_documentnumber FOREIGN KEY (document_number)
    REFERENCES tb_doc_receipts (document_number) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  stock_id bigint NOT NULL, -- tb_stocks id
  CONSTRAINT tb_doc_receipt_items_stockid FOREIGN KEY (stock_id)
    REFERENCES tb_stocks (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  item_id bigint NOT NULL, -- tb_master_items item_id
  CONSTRAINT tb_doc_receipt_items_itemid FOREIGN KEY (item_id)
    REFERENCES tb_master_items (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  serial_id int, -- tb_master_item_serials
  CONSTRAINT tb_doc_receipt_items_serialid FOREIGN KEY (serial_id)
    REFERENCES tb_master_item_serials (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  condition varchar(20) NOT NULL,
  CONSTRAINT tb_doc_receipt_items_condition FOREIGN KEY (condition)
    REFERENCES tb_master_item_conditions (condition) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  stores varchar(60) NOT NULL,
  CONSTRAINT tb_doc_receipt_items_stores FOREIGN KEY (stores)
    REFERENCES tb_master_stores (stores) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  received_quantity numeric(15,2) NOT NULL DEFAULT 0.00,
  unit_value numeric(15,2) NOT NULL DEFAULT 1.00,
  expired_date date,
  purchase_order_number varchar(20), -- PO Number
  reference_number varchar(60), -- INV/REF
  awb_number varchar(100), -- AIRWAY BILL
  remarks text
);

-- Common Name: INTERNAL DELIVERY/DOKUMEN PENGIRIMAN INTERNAL
CREATE TABLE tb_doc_deliveries
(
  id bigserial NOT NULL PRIMARY KEY,
  category varchar(60) NOT NULL,
  CONSTRAINT tb_doc_deliveries_category FOREIGN KEY (category)
    REFERENCES tb_master_item_categories (category) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  warehouse varchar(60) NOT NULL,
  CONSTRAINT tb_doc_deliveries_items_warehouse FOREIGN KEY (warehouse)
    REFERENCES tb_master_warehouses (warehouse) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  document_number varchar(20) NOT NULL UNIQUE,
  received_date date NOT NULL,
  received_by varchar(100), -- RECEIVED BY
  received_from varchar(60), -- AIRCRAFT OR OTHER
  sent_by varchar(100),
  approved_by varchar(100),
  status varchar(20),
  notes text,
  created_at timestamp without time zone NOT NULL DEFAULT now(),
  created_by varchar(100),
  updated_at timestamp without time zone NOT NULL DEFAULT now(),
  updated_by varchar(100)
);

CREATE TABLE tb_doc_delivery_items
(
  id bigserial NOT NULL PRIMARY KEY,
  document_number varchar(20) NOT NULL, -- tb_doc_deliveries document_number
  CONSTRAINT tb_doc_delivery_items_documentnumber FOREIGN KEY (document_number)
    REFERENCES tb_doc_deliveries (document_number) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  stock_id bigint NOT NULL, -- tb_stocks id
  CONSTRAINT tb_doc_delivery_items_stockid FOREIGN KEY (stock_id)
    REFERENCES tb_stocks (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  item_id bigint NOT NULL, -- tb_master_items item_id
  CONSTRAINT tb_doc_delivery_items_itemid FOREIGN KEY (item_id)
    REFERENCES tb_master_items (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  serial_id int, -- tb_master_item_serials
  CONSTRAINT tb_doc_delivery_items_serialid FOREIGN KEY (serial_id)
    REFERENCES tb_master_item_serials (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  condition varchar(20) NOT NULL,
  CONSTRAINT tb_doc_delivery_items_condition FOREIGN KEY (condition)
    REFERENCES tb_master_item_conditions (condition) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  stores varchar(60) NOT NULL,
  CONSTRAINT tb_doc_delivery_items_stores FOREIGN KEY (stores)
    REFERENCES tb_master_stores (stores) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  received_quantity numeric(15,2) NOT NULL DEFAULT 1.00,
  unit_value numeric(15,2) NOT NULL DEFAULT 1.00,
  remarks text
);

-- Common Name: MATERIAL SLIP
CREATE TABLE tb_doc_usages
(
  id bigserial NOT NULL PRIMARY KEY,
  category varchar(60) NOT NULL,
  CONSTRAINT tb_doc_usages_category FOREIGN KEY (category)
    REFERENCES tb_master_item_categories (category) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  warehouse varchar(60) NOT NULL,
  CONSTRAINT tb_doc_usages_items_warehouse FOREIGN KEY (warehouse)
    REFERENCES tb_master_warehouses (warehouse) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  document_number varchar(20) NOT NULL UNIQUE,
  issued_date date NOT NULL,
  issued_by varchar(100),
  issued_to varchar(100), -- AIRCRAFT OR OTHER
  required_by varchar(100),
  approved_by varchar(100),
  requisition_reference varchar(100) NOT NULL DEFAULT NULL::varchar,
  status varchar(20),
  notes text,
  created_at timestamp without time zone NOT NULL DEFAULT now(),
  created_by varchar(100),
  updated_at timestamp without time zone NOT NULL DEFAULT now(),
  updated_by varchar(100)
);

CREATE TABLE tb_doc_usage_items
(
  id bigserial NOT NULL PRIMARY KEY,
  document_number varchar(20) NOT NULL, -- tb_doc_usages document_number
  CONSTRAINT tb_doc_usage_items_documentnumber FOREIGN KEY (document_number)
    REFERENCES tb_doc_usages (document_number) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  stock_in_stores_id bigint NOT NULL, -- tb_stock_in_stores id
  CONSTRAINT tb_doc_usage_items_stockinstoresid FOREIGN KEY (stock_in_stores_id)
    REFERENCES tb_stock_in_stores (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  item_id bigint NOT NULL, -- tb_master_items item_id
  CONSTRAINT tb_doc_usage_items_itemid FOREIGN KEY (item_id)
    REFERENCES tb_master_items (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  serial_id int, -- tb_master_item_serials
  CONSTRAINT tb_doc_usage_items_serialid FOREIGN KEY (serial_id)
    REFERENCES tb_master_item_serials (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  condition varchar(20) NOT NULL,
  CONSTRAINT tb_doc_usage_items_condition FOREIGN KEY (condition)
    REFERENCES tb_master_item_conditions (condition) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  issued_quantity numeric(15,2) NOT NULL DEFAULT 1.00,
  unit_value numeric(15,2) NOT NULL DEFAULT 1.00,
  required_by varchar(100),
  remarks text
);

-- Common Name: SHIPPING DOCUMENT/DOKUMEN PENGIRIMAN OUTBASE
CREATE TABLE tb_doc_shipments
(
  id bigserial NOT NULL PRIMARY KEY,
  category varchar(60) NOT NULL,
  CONSTRAINT tb_doc_shipments_category FOREIGN KEY (category)
    REFERENCES tb_master_item_categories (category) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  warehouse varchar(60) NOT NULL,
  CONSTRAINT tb_doc_shipments_items_warehouse FOREIGN KEY (warehouse)
    REFERENCES tb_master_warehouses (warehouse) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  document_number varchar(20) NOT NULL UNIQUE,
  issued_date date NOT NULL, -- SENT DATE
  issued_by varchar(100), -- RELEASED BY
  issued_to varchar(100), -- WAREHOUSE
  CONSTRAINT tb_doc_shipments_items_issuedto FOREIGN KEY (issued_to)
    REFERENCES tb_master_warehouses (warehouse) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  sent_by varchar(100),
  approved_by varchar(100),
  awb_number varchar(100), -- AIRWAY BILL
  status varchar(20),
  notes text,
  created_at timestamp without time zone NOT NULL DEFAULT now(),
  created_by varchar(100),
  updated_at timestamp without time zone NOT NULL DEFAULT now(),
  updated_by varchar(100)
);

CREATE TABLE tb_doc_shipment_items
(
  id bigserial NOT NULL PRIMARY KEY,
  document_number varchar(20) NOT NULL, -- tb_doc_shipments document_number
  CONSTRAINT tb_doc_shipment_items_documentnumber FOREIGN KEY (document_number)
    REFERENCES tb_doc_shipments (document_number) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  stock_in_stores_id bigint NOT NULL, -- tb_stock_in_stores id
  CONSTRAINT tb_doc_shipment_items_stockinstoresid FOREIGN KEY (stock_in_stores_id)
    REFERENCES tb_stock_in_stores (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  item_id bigint NOT NULL, -- tb_master_items item_id
  CONSTRAINT tb_doc_shipment_items_itemid FOREIGN KEY (item_id)
    REFERENCES tb_master_items (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  serial_id int, -- tb_master_item_serials
  CONSTRAINT tb_doc_shipment_items_serialid FOREIGN KEY (serial_id)
    REFERENCES tb_master_item_serials (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  condition varchar(20) NOT NULL,
  CONSTRAINT tb_doc_shipment_items_condition FOREIGN KEY (condition)
    REFERENCES tb_master_item_conditions (condition) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  issued_quantity numeric(15,2) NOT NULL DEFAULT 1.00,
  unit_value numeric(15,2) NOT NULL DEFAULT 1.00,
  remarks text
);

CREATE TABLE tb_doc_shipment_item_receipts
(
  id bigserial NOT NULL PRIMARY KEY,
  shipment_item_id bigint NOT NULL, -- tb_doc_shipment_items id
  CONSTRAINT tb_doc_shipment_item_receipts_id FOREIGN KEY (shipment_item_id)
    REFERENCES tb_doc_shipment_items (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  stores varchar(60) NOT NULL,
  CONSTRAINT tb_doc_shipment_item_receipts_stores FOREIGN KEY (stores)
    REFERENCES tb_master_stores (stores) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  received_quantity numeric(15,2) NOT NULL DEFAULT 1.00,
  received_date timestamp without time zone NOT NULL DEFAULT now(),
  received_by varchar(100),
  notes text
);

-- Common Name: COMMERCIAL INVOICE
CREATE TABLE tb_doc_returns
(
  id bigserial NOT NULL PRIMARY KEY,
  category varchar(60) NOT NULL,
  CONSTRAINT tb_doc_returns_category FOREIGN KEY (category)
    REFERENCES tb_master_item_categories (category) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  warehouse varchar(60) NOT NULL,
  CONSTRAINT tb_doc_returns_items_warehouse FOREIGN KEY (warehouse)
    REFERENCES tb_master_warehouses (warehouse) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  document_number varchar(20) NOT NULL UNIQUE,
  issued_date date NOT NULL, -- SENT DATE
  issued_by varchar(100), -- RELEASED BY
  issued_to varchar(100), -- VENDOR
  CONSTRAINT tb_doc_returns_items_issuedto FOREIGN KEY (issued_to)
    REFERENCES tb_master_vendors (vendor) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  sent_by varchar(100),
  approved_by varchar(100),
  awb_number varchar(100), -- AIRWAY BILL
  status varchar(20),
  notes text,
  created_at timestamp without time zone NOT NULL DEFAULT now(),
  created_by varchar(100),
  updated_at timestamp without time zone NOT NULL DEFAULT now(),
  updated_by varchar(100)
);

CREATE TABLE tb_doc_return_items
(
  id bigserial NOT NULL PRIMARY KEY,
  document_number varchar(20) NOT NULL, -- tb_doc_returns document_number
  CONSTRAINT tb_doc_return_items_documentnumber FOREIGN KEY (document_number)
    REFERENCES tb_doc_returns (document_number) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  stock_in_stores_id bigint NOT NULL, -- tb_stock_in_stores id
  CONSTRAINT tb_doc_return_items_stockinstoresid FOREIGN KEY (stock_in_stores_id)
    REFERENCES tb_stock_in_stores (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  item_id bigint NOT NULL, -- tb_master_items item_id
  CONSTRAINT tb_doc_return_items_itemid FOREIGN KEY (item_id)
    REFERENCES tb_master_items (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  serial_id int, -- tb_master_item_serials
  CONSTRAINT tb_doc_return_items_serialid FOREIGN KEY (serial_id)
    REFERENCES tb_master_item_serials (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  condition varchar(20) NOT NULL,
  CONSTRAINT tb_doc_return_items_condition FOREIGN KEY (condition)
    REFERENCES tb_master_item_conditions (condition) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  issued_quantity numeric(15,2) NOT NULL DEFAULT 1.00,
  unit_value numeric(15,2) NOT NULL DEFAULT 1.00,
  remarks text
);

-- Common Name: PURCHASE REQUEST
-- Common Name: PURCHASE ORDER EVALUATION
CREATE TABLE tb_purchase_order_evaluations
(
  id bigserial NOT NULL PRIMARY KEY,
  document_number varchar(20) NOT NULL UNIQUE,
  document_date date NOT NULL,
  document_reference varchar(100),
  total_quantity numeric(15,2) DEFAULT 0.00,
  total_price numeric(15,2) DEFAULT 0.00,
  grand_total numeric(15,2) DEFAULT 0.00,
  status varchar(20) DEFAULT 'pending'::varchar,
  notes text,
  approved_at timestamp without time zone,
  approved_by varchar(100),
  rejected_at timestamp without time zone,
  rejected_by varchar(100),
  canceled_at timestamp without time zone,
  canceled_by varchar(100),
  created_at timestamp without time zone DEFAULT now(),
  created_by varchar(100)
);

CREATE TABLE tb_purchase_order_evaluation_vendors
(
  id bigserial NOT NULL PRIMARY KEY,
  document_number varchar(20) NOT NULL,
  CONSTRAINT tb_poe_vendors_documentnumber FOREIGN KEY (document_number)
    REFERENCES tb_purchase_order_evaluations (document_number) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  vendor varchar(100),
  CONSTRAINT tb_poe_vendors_vendor FOREIGN KEY (vendor)
    REFERENCES tb_master_vendors (vendor) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT tb_poe_vendors_uniquegroup UNIQUE (document_number, vendor)
);

CREATE TABLE tb_purchase_order_evaluation_requests
(
  id bigserial NOT NULL PRIMARY KEY,
  document_number varchar(50) NOT NULL,
  CONSTRAINT tb_poe_requests_documentnumber FOREIGN KEY (document_number)
    REFERENCES tb_purchase_order_evaluations (document_number) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  inventory_purchase_request_id bigint NOT NULL,
  inventory_purchase_request_number varchar(50)
);

CREATE TABLE tb_purchase_order_evaluation_request_items
(
  id bigserial NOT NULL PRIMARY KEY,
  purchase_order_evaluation_request_id bigint NOT NULL,
  CONSTRAINT tb_poe_request_items_poerequestid FOREIGN KEY (purchase_order_evaluation_request_id)
    REFERENCES tb_purchase_order_evaluation_requests (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  inventory_purchase_request_detail_id bigint NOT NULL,
  description varchar(255),
  part_number varchar(60),
  additional_info varchar(255),
  quantity numeric(15,2) DEFAULT 0.00,
  price numeric(15,2) DEFAULT 0.00,
  total numeric(15,2) DEFAULT 0.00,
  unit varchar(20)
);

CREATE TABLE tb_purchase_order_evaluation_request_items_vendors
(
  id bigserial NOT NULL PRIMARY KEY,
  purchase_order_evaluation_request_item_id bigint NOT NULL,
  CONSTRAINT tb_poe_request_items_vendors_poerequestitemid FOREIGN KEY (purchase_order_evaluation_request_item_id)
    REFERENCES tb_purchase_order_evaluation_request_items (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  purchase_order_evaluation_vendor_id bigint NOT NULL,
  CONSTRAINT tb_poe_request_items_vendors_poevendorid FOREIGN KEY (purchase_order_evaluation_vendor_id)
    REFERENCES tb_purchase_order_evaluation_vendors (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  order_quantity numeric(15,2) DEFAULT 0.00,
  order_unit_price numeric(15,2) DEFAULT 0.00,
  order_total numeric(15,2) DEFAULT 0.00,
  core_charge numeric(15,2) DEFAULT 0.00,
  selected boolean DEFAULT false,
  CONSTRAINT tb_poe_request_items_vendors_uniquegroup UNIQUE (purchase_order_evaluation_request_item_id, purchase_order_evaluation_vendor_id)
);


-- Common Name: PURCHASE ORDER
-- Table: tb_purchase_orders

CREATE TABLE tb_purchase_orders
(
  id bigserial NOT NULL PRIMARY KEY,
  document_number character varying(20) NOT NULL UNIQUE,
  document_date date NOT NULL,
  document_body text,
  category character varying(60) NOT NULL,
  CONSTRAINT tb_purchase_orders_category FOREIGN KEY (category)
    REFERENCES tb_master_item_categories (category) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  warehouse character varying(60) NOT NULL,
  CONSTRAINT tb_purchase_orders_warehouse FOREIGN KEY (warehouse)
    REFERENCES tb_master_warehouses (warehouse) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  reference_quotation character varying(100),
  reference_poe character varying(100),
  vendor varchar(100),
  CONSTRAINT tb_purchase_orders_vendor FOREIGN KEY (vendor)
    REFERENCES tb_master_vendors (vendor) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT,
  vendor_address text,
  vendor_country character varying(60),
  vendor_phone character varying(20),
  vendor_attention character varying(100),
  deliver_company character varying(100) NOT NULL DEFAULT 'PT. Bali Widya Dirgantara'::character varying,
  deliver_address text,
  deliver_country character varying(60) NOT NULL,
  deliver_phone character varying(20) NOT NULL,
  deliver_attention character varying(100) NOT NULL,
  bill_company character varying(100) NOT NULL DEFAULT 'PT. Bali Widya Dirgantara'::character varying,
  bill_address text,
  bill_country character varying(60) NOT NULL,
  bill_attention character varying(100) NOT NULL,
  bill_phone character varying(20) NOT NULL,
  issued_by character varying(60),
  checked_by character varying(60),
  approved_by character varying(60),
  notes text,
  created_at timestamp without time zone DEFAULT now(),
  created_by character varying(100),
  updated_at timestamp without time zone DEFAULT now(),
  updated_by character varying(100)
);

CREATE TABLE tb_purchase_order_items
(
  id bigserial NOT NULL PRIMARY KEY,
  purchase_order_id bigint NOT NULL,
  CONSTRAINT tb_purchase_order_items_purchaseorderid FOREIGN KEY (purchase_order_id)
    REFERENCES tb_purchase_orders (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  purchase_order_evaluation_items_vendors_id bigint NOT NULL,
  CONSTRAINT tb_purchase_order_items_poeitemsvendorsid FOREIGN KEY (purchase_order_evaluation_items_vendors_id)
    REFERENCES tb_purchase_order_evaluation_items_vendors (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE,
  description character varying(100),
  part_number character varying(60),
  alternate_part_number character varying(60),
  serial_number character varying(60),
  quantity numeric(15,2) DEFAULT 1.00,
  unit_price numeric(15,2) DEFAULT 1.00,
  core_charge numeric(15,2) DEFAULT 1.00,
  remarks character varying(255)
);
