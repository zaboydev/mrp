CREATE OR REPLACE FUNCTION f_count_total_quantity_by_group(item_group text, document_type text, start_date date, end_date date)

RETURNS numeric AS $total$

DECLARE
	total numeric;

BEGIN
  SELECT SUM(quantity) into total
  FROM tb_item_quantity_details
  JOIN tb_items ON tb_items.part_number = tb_item_quantity_details.part_number
  WHERE document_type = document_type
  AND document_date >= start_date
  AND document_date < end_date
  AND item_group = item_group

  RETURN total;
END;

$total$ LANGUAGE plpgsql;
