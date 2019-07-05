TRUNCATE tb_stock_opnames RESTART IDENTITY;
UPDATE tb_settings SET setting_value = 2 WHERE id = 6;

/*
UPDATE tb_receipts SET
  received_date = tb_issuances.received_date
FROM tb_issuances
WHERE tb_receipts.document_number = tb_issuances.document_number;

INSERT INTO tb_receipts (document_number, received_from, received_date, received_by, category, warehouse)
SELECT
  tb_issuances.document_number,
  tb_issuances.warehouse,
  tb_issuance_item_receipts.received_date,
  tb_issuance_item_receipts.received_by,
  tb_issuances.category,
  tb_issuances.issued_to
FROM tb_issuance_item_receipts
JOIN tb_issuance_items ON tb_issuance_items.id = tb_issuance_item_receipts.issuance_item_id
JOIN tb_issuances ON tb_issuances.document_number = tb_issuance_items.document_number
GROUP BY
  tb_issuances.document_number,
  tb_issuances.warehouse,
  tb_issuance_item_receipts.received_date,
  tb_issuance_item_receipts.received_by,
  tb_issuances.category,
  tb_issuances.issued_to;

UPDATE tb_receipts SET
  created_by = 'ALMAGA',
  updated_by = 'ALMAGA'
WHERE created_by IS NULL;

INSERT INTO tb_receipt_items (document_number, stock_in_stores_id, received_quantity, received_unit_value, received_total_value, remarks)
SELECT document_number, tb_issuance_item_receipts.stock_in_stores_id, received_quantity, received_unit_value, received_total_value, tb_issuance_item_receipts.remarks
FROM tb_issuance_item_receipts
JOIN tb_issuance_items ON tb_issuance_items.id = tb_issuance_item_receipts.issuance_item_id;

UPDATE tb_issuances SET received_date = '2017-02-03 00:00:00' WHERE id = 809;
UPDATE tb_issuance_item_receipts SET received_date = '2017-02-03 00:00:00' WHERE id = 2;
UPDATE tb_receipts SET received_date = '2017-02-03' WHERE id = 52;
UPDATE tb_stock_cards SET date_of_entry = '2017-02-03 00:00:00' WHERE id = 3357;
*/
