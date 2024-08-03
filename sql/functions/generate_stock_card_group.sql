CREATE OR REPLACE FUNCTION f_stock_card_by_group(item_group text, start_date, end_date date)

RETURNS TABLE(
  id bigint, item_group text, description text, part_number text, alt_part_number text, minimum_quantity numeric, uom text, received numeric, issued numeric, initial numeric
) AS $BODY$

DECLARE
  rec RECORD;

BEGIN
  FOR rec IN
    SELECT
      t2.id,
      t2.item_group,
      t2.description,
      t1.part_number,
      t2.alt_part_number,
      t2.minimum_quantity,
      t2.uom
    FROM tb_item_quantity_details t1
    JOIN tb_items t2 ON t2.part_number = t1.part_number
    WHERE t1.document_date >= start_date
    AND t1.document_date < end_date
    GROUP BY
      t2.id,
      t2.item_group,
      t2.description,
      t1.part_number,
      t2.alt_part_number,
      t2.minimum_quantity,
      t2.uom
    ORDER BY t2.item_group ASC, t2.description ASC, t1.part_number ASC
  LOOP
    FOR month IN 1..12
    LOOP
      SELECT t_bgt.mtd_budget, t_bgt.mtd_quantity
      FROM tb_capex_monthly_budgets t_bgt
      WHERE t_bgt.year_number = yearnumber
      AND t_bgt.month_number = month
      AND t_bgt.product_id = rec.id
      INTO monthly_budget, monthly_quantity;

      IF month = 1 THEN
              jan_val = monthly_budget;
                jan_qty = monthly_quantity;
      ELSIF month = 2 THEN
              feb_val = monthly_budget;
                feb_qty = monthly_quantity;
      ELSIF month = 3 THEN
              mar_val = monthly_budget;
                mar_qty = monthly_quantity;
      ELSIF month = 4 THEN
              apr_val = monthly_budget;
                apr_qty = monthly_quantity;
      ELSIF month = 5 THEN
              mei_val = monthly_budget;
                mei_qty = monthly_quantity;
      ELSIF month = 6 THEN
              jun_val = monthly_budget;
                jun_qty = monthly_quantity;
      ELSIF month = 7 THEN
              jul_val = monthly_budget;
                jul_qty = monthly_quantity;
      ELSIF month = 8 THEN
              ags_val = monthly_budget;
                ags_qty = monthly_quantity;
      ELSIF month = 9 THEN
              sep_val = monthly_budget;
                sep_qty = monthly_quantity;
      ELSIF month = 10 THEN
              oct_val = monthly_budget;
                oct_qty = monthly_quantity;
      ELSIF month = 11 THEN
              nov_val = monthly_budget;
                nov_qty = monthly_quantity;
      ELSE
              des_val = monthly_budget;
                des_qty = monthly_quantity;
      END IF;
    END LOOP;

    total_val = jan_val + feb_val + mar_val + apr_val + mei_val + jun_val + jul_val + ags_val + sep_val + oct_val + nov_val + des_val;
    total_qty = jan_qty + feb_qty + mar_qty + apr_qty + mei_qty + jun_qty + jul_qty + ags_qty + sep_qty + oct_qty + nov_qty + des_qty;

    RETURN NEXT;
  END LOOP;
  RETURN;
END;

$BODY$ LANGUAGE plpgsql;
