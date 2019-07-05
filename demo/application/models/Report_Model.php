<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Auth_model
 *
 * Created by PhpStorm.
 * User: imann
 * Date: 19/04/2016
 * Time: 18:52
 *
 * @property CI_DB_query_builder $db
 */
class Report_Model extends MY_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function findPeriodInfo($year, $month)
  {
    $this->db->where('period', $month);
    $this->db->where('year_number', $year);
    $query = $this->db->get('tb_periods');

    return $query->unbuffered_row();
  }

  public function findItemModels($part_number)
  {
    $this->db->order_by('aircraft_type', 'asc');
    $this->db->where('part_number', $part_number);
    $query = $this->db->get('tb_item_models');
    $result = $query->result_array();

    $aircraft_types = array();

    foreach ($result as $row){
      $aircraft_types[] = $row['aircraft_type'];
    }

    return $aircraft_types;
  }

  public function findItemInfo($id)
  {
    $this->db->where('id', $id);
    $query = $this->db->get('tb_master_items');

    return $query->row();
  }

  public function findItemGroupInfo($group)
  {
    if ($group == 'ALL'){
      $row = array(
        'group' => 'ALL',
        'description' => 'All Group'
     );

      return (object) $row;
    } else {
      $this->db->where('group', $group);
      $query = $this->db->get('tb_master_item_groups');

      return $query->row();
    }
  }

  public function findSummaryStock($start_date, $end_date)
  {
    $sql = "SELECT
      t2.id,
      t2.group,
      t2.description,
      t1.warehouse,
      t1.condition,
      SUM (
        CASE
          WHEN t1.date_of_entry >= '$start_date'
            AND t1.date_of_entry < '$end_date'
          THEN t1.quantity
        END
     ) AS quantity,
      SUM (
        CASE
          WHEN t1.date_of_entry < '$start_date'
          THEN t1.quantity
        END
     ) AS initial
    FROM tb_stocks t1
    JOIN tb_master_items t3 ON t3.part_number = t1.part_number
    JOIN tb_master_item_groups t2 ON t2.group = t3.group
    GROUP BY
      t2.id,
      t2.group,
      t2.description,
      t1.warehouse,
      t1.condition
    ORDER BY t2.description ASC, t1.warehouse ASC";

    $query = $this->db->query($sql);
    $result = $query->result_array();

    return $result;
  }

  public function findGeneralStock($end_date, $start_date = NULL, $group = 'ALL', $warehouse = 'GENERAL', $condition = 'S/S')
  {
    $select[] = 't2.group';
    $select[] = 't2.description';
    $select[] = 't1.part_number';
    $select[] = 't1.item_serial';
    $select[] = 't2.alternate_part_number';
    $select[] = 't2.minimum_quantity';
    $select[] = 't2.unit';
    $select[] = 't1.warehouse';
    $select[] = 't1.stores';
    $select[] = 't1.condition';

    if ($start_date !== NULL)
      $this->db->where('t1.date_of_entry >=', $start_date);

    if ($group !== 'ALL')
      $this->db->where('t2.group', $group);

    if ($warehouse !== 'GENERAL')
      $this->db->where('t1.warehouse', $warehouse);

    if ($condition !== 'ALL')
      $this->db->where('t1.condition', $condition);

    $this->db->where('t1.date_of_entry <= ', $end_date);
    $this->db->select($select);
    $this->db->select_sum('t1.quantity', 'quantity');
    $this->db->from('tb_stocks t1');
    $this->db->join('tb_master_items t2', 't2.part_number = t1.part_number');
    $this->db->group_by($select);
    $this->db->order_by('t2.group ASC, t2.description ASC, t1.part_number ASC');
    $query = $this->db->get();

    $result = $query->result_array();

    foreach ($result as $key => $row){
      $item_models = $this->findItemModels($row['part_number']);

      $result[$key]['aircraft_types'] = $item_models;
    }

    return $result;
  }

  public function findItemInitialQuantity($part_number, $item_serial = NULL, $end_date)
  {
    $item_serial = ($item_serial === NULL) ? "" : "AND item_serial = '$item_serial'";
    $sql = "SELECT SUM(quantity) AS initial_quantity
      FROM tb_item_quantity_details
      WHERE document_type = 'GRN'
      AND document_date < '$end_date'
      AND part_number = '$part_number'
      $item_serial";

    $query = $this->db->query($sql);
    $row = $query->row();

    return $row->initial_quantity;
  }

  public function findStockCard($end_date, $start_date)
  {
    $sql = "SELECT
      t1.id,
      t1.group,
      t1.description,
      (
        SELECT SUM(quantity)
        FROM tb_item_quantity_details
        JOIN tb_master_items ON tb_master_items.part_number = tb_item_quantity_details.part_number
        WHERE document_type = 'GRN'
        AND document_date >= '$start_date'
        AND document_date < '$end_date'
        AND group = t1.group
     ) AS received,
      (
        SELECT SUM(quantity)
        FROM tb_item_quantity_details
        JOIN tb_master_items ON tb_master_items.part_number = tb_item_quantity_details.part_number
        WHERE (document_type = 'CI' OR document_type = 'MS')
        AND document_date >= '$start_date'
        AND document_date < '$end_date'
        AND group = t1.group
     ) AS issued,
      (
        SELECT SUM(quantity)
        FROM tb_item_quantity_details
        JOIN tb_master_items ON tb_master_items.part_number = tb_item_quantity_details.part_number
        WHERE document_type = 'GRN'
        AND document_date < '$start_date'
        AND group = t1.group
     ) AS initial
    FROM tb_master_item_groups t1
    ORDER BY t1.group ASC";

    $query = $this->db->query($sql);
    $result = $query->result_array();

    return $result;
  }

  public function findStockCardGroup($group, $end_date, $start_date)
  {
    $where = ($group != 'ALL') ? "WHERE t2.group = '$group'" : "";

    $sql = "SELECT
          t2.id,
          t2.group,
          t2.description,
          t1.part_number,
          t2.alternate_part_number,
          t2.minimum_quantity,
          t2.unit,
          SUM (
            CASE
              WHEN t1.document_type = 'GRN'
              AND t1.document_date >= '$start_date'
              AND t1.document_date < '$end_date'
            THEN t1.quantity END
         ) AS received,
          SUM (
            CASE
              WHEN (t1.document_type = 'MS' OR t1.document_type = 'CI')
              AND t1.document_date >= '$start_date'
              AND t1.document_date < '$end_date'
            THEN t1.quantity END
         ) AS issued,
          SUM (
            CASE
              WHEN t1.document_type = 'GRN'
              AND t1.document_date < '$start_date'
            THEN t1.quantity END
         ) AS initial
        FROM tb_item_quantity_details t1
        JOIN tb_master_items t2 ON t2.part_number = t1.part_number
        $where
        GROUP BY
          t2.id,
          t2.group,
          t2.description,
          t1.part_number,
          t2.alternate_part_number,
          t2.minimum_quantity,
          t2.unit
        ORDER BY t2.group ASC, t2.description ASC, t1.part_number ASC";

    $query = $this->db->query($sql);
    $result = $query->result_array();

    return $result;
  }

  public function findStockCardDetail($id, $end_date, $start_date)
  {
    $sql = "SELECT
      t2.id,
      t2.group,
      t2.description,
      t1.part_number,
      t2.alternate_part_number,
      t1.item_serial,
      t2.unit,
      SUM (
        CASE
          WHEN t1.document_type = 'GRN'
          AND t1.document_date >= '$start_date'
          AND t1.document_date < '$end_date'
        THEN t1.quantity END
     ) AS received,
      SUM (
        CASE
          WHEN (t1.document_type = 'MS' OR t1.document_type = 'CI')
          AND t1.document_date >= '$start_date'
          AND t1.document_date < '$end_date'
        THEN t1.quantity END
     ) AS issued,
      SUM (
        CASE
          WHEN t1.document_type = 'GRN'
          AND t1.document_date < '$start_date'
        THEN t1.quantity END
     ) AS initial
    FROM tb_item_quantity_details t1
    JOIN tb_master_items t2 ON t2.part_number = t1.part_number
    WHERE t2.id = '$id'
    GROUP BY
      t2.id,
      t2.group,
      t2.description,
      t1.part_number,
      t2.alternate_part_number,
      t1.item_serial,
      t2.unit
    ORDER BY t1.item_serial ASC";

    $query = $this->db->query($sql);
    $result = $query->result_array();

    return $result;
  }

  public function findStockCardInfo($part_number, $item_serial = NULL, $end_date, $start_date = NULL)
  {
    $item_serial = ($item_serial !== NULL) ? "AND t1.item_serial = '$item_serial'" : "";

    $sql = "SELECT
      t1.*,
      (CASE
        WHEN t1.document_type = 'GRN'
        THEN t1.quantity
        WHEN (t1.document_type = 'MS' OR t1.document_type = 'CI')
        THEN -t1.quantity
        ELSE 0.00
        END) AS quantity,
      (CASE
        WHEN t1.document_type = 'MS'
        THEN t1.aircraft ELSE t1.vendor
        END) AS issued_to
    FROM tb_item_quantity_details t1
    JOIN tb_master_items t2 ON t2.part_number = t1.part_number
    WHERE t1.part_number = '$part_number'
    AND t1.document_date >= '$start_date'
    AND t1.document_date < '$end_date'
    $item_serial
    AND (t1.document_type = 'GRN' OR t1.document_type = 'MS' OR t1.document_type = 'CI')
    ORDER BY t1.document_date ASC
    ";

    $query = $this->db->query($sql);
    $result = $query->result_array();

    return $result;
  }

  public function findGRN($start_date, $end_date)
  {
    $select[] = 't1.*';
    $select[] = 't2.part_number';
    $select[] = 't2.item_serial';
    $select[] = 't2.condition';
    $select[] = 't2.quantity';
    $select[] = 't2.stores';
    $select[] = 't2.order_number';
    $select[] = 't2.reference_number';
    $select[] = 't2.awb_number';
    $select[] = 't2.notes';
    $select[] = 't3.description';

    $this->db->select($select);
    $this->db->from('tb_doc_receipts t1');
    $this->db->join('tb_item_quantity_details t2', 't2.document_number = t1.document_number');
    $this->db->join('tb_master_items t3', 't3.part_number = t2.part_number');
    $this->db->where('t2.document_type', 'GRN');
    $this->db->where('t1.received_date >=', $start_date);
    $this->db->where('t1.received_date <', $end_date);
    $query = $this->db->get();

    return $query->result_array();
  }

  public function findCommercialInvoice($start_date, $end_date)
  {
    $select[] = 't1.*';
    $select[] = 't2.*';
    $select[] = 't3.description';
    $select[] = 't3.unit';
    $select[] = 't3.group';

    $this->db->select($select);
    $this->db->from('tb_doc_returns t1');
    $this->db->join('tb_item_quantity_details t2', 't2.document_number = t1.document_number');
    $this->db->join('tb_master_items t3', 't3.part_number = t2.part_number');
    $this->db->where('t2.document_type', 'CI');
    $this->db->where('t1.document_date >=', $start_date);
    $this->db->where('t1.document_date <=', $end_date);
    $query = $this->db->get();

    return $query->result_array();
  }

  public function findShippingDocument($start_date, $end_date)
  {
    $select[] = 't1.*';
    $select[] = 't2.*';
    $select[] = 't3.description';
    $select[] = 't3.unit';
    $select[] = 't3.group';

    $this->db->select($select);
    $this->db->from('tb_doc_shipments t1');
    $this->db->join('tb_item_quantity_details t2', 't2.document_number = t1.document_number');
    $this->db->join('tb_master_items t3', 't3.part_number = t2.part_number');
    $this->db->where('t2.document_type', 'SD');
    $query = $this->db->get();

    return $query->result_array();
  }

  public function findMaterialSlip($warehouse = 'GENERAL', $start_date, $end_date)
  {
    $select[] = 't1.document_date';
    $select[] = 't1.document_number';
    $select[] = 't1.description_required';
    $select[] = 't1.warehouse';
    $select[] = 't2.aircraft';
    $select[] = 't2.part_number';
    $select[] = 't2.item_serial';
    $select[] = 't2.quantity';
    $select[] = 't1.requestition_reference';
    $select[] = 't1.notes';

    $this->db->select($select);
    $this->db->from('tb_doc_usages t1');
    $this->db->where('t2.document_type', 'MS');
    $this->db->join('tb_item_quantity_details t2', 't2.document_number = t1.document_number');

    if ($warehouse != 'GENERAL'){
      $this->db->where('t2.warehouse', $warehouse);
    }

    $this->db->where('t1.document_date >=', $start_date);
    $this->db->where('t1.document_date <=', $end_date);
    $query = $this->db->get();

    return $query->result_array();
  }
}
