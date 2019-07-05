<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_Model extends MY_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function find_item_in_stores($warehouse, $term)
  {
    $sql = "SELECT t1.*, t2.description, t2.group, t2.alternate_part_number, t2.unit, t2.id AS item_in_stores_id
      FROM tb_stocks t1
      JOIN tb_master_items t2 ON t2.part_number = t1.part_number
      WHERE t1.warehouse = '$warehouse'
      AND t1.quantity > 0
      AND (
          t2.description ILIKE '%$term%' OR
          t1.part_number ILIKE '%$term%' OR
          t1.item_serial ILIKE '%$term%'
     )
      ORDER BY t2.description ASC, t1.part_number ASC
      ";

    $query = $this->db->query($sql);
    $result = $query->result_array();

    foreach ($result as $key => $value){
      $result[$key]['aircraft_types'] = $this->findItemModelsByPartNumber($value['part_number']);
      $result[$key]['balance_stock']  = $this->find_balance_stock($value['part_number'], $value['item_serial'], $value['warehouse']);
    }

    return $result;
  }

  public function find_balance_stock($part_number, $item_serial, $warehouse = null, $stores = null)
  {
    if ($warehouse !== null)
      $this->db->where('warehouse', $warehouse);

    if ($stores !== null)
      $this->db->where('warehouse', $warehouse);

    $this->db->where('part_number', $part_number);
    $this->db->where('item_serial', $item_serial);
    $this->db->where('condition !=', 'REJECTED');
    $this->db->select_sum('quantity', 'quantity');
    $query = $this->db->get('tb_stocks');
    $row = $query->row_array();

    return $row['quantity'];
  }

  public function countAdjustment()
  {
    $this->db->select(array(
      'tb_stock_adjustments.id',
      'tb_master_items.part_number',
      'tb_master_items.serial_number',
      'tb_master_items.description',
      'tb_master_item_groups.category',
      'tb_master_items.group',
      'tb_stocks.condition',
      'tb_stock_adjustments.created_by',
      'tb_stock_adjustments.created_at',
      'tb_stock_adjustments.previous_quantity',
      'tb_stock_adjustments.adjustment_quantity',
      'tb_stock_adjustments.balance_quantity',
      'tb_master_items.unit',
      'tb_stock_adjustments.remarks',
      'tb_stock_adjustments.adjustment_token',
    ));
    $this->db->from('tb_stock_adjustments');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_stock_adjustments.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_stock_adjustments.updated_status', 'PENDING');
    //$this->db->group_by($this->getGroupByColumns());
	
	$this->db->where('EXTRACT(YEAR FROM tb_stock_adjustments.date_of_entry)::integer = ', date('Y'));

    $query = $this->db->get();

    return $query->num_rows();
  }

   public function countExpiredStock($start_date,$end_date)
  {
    $this->db->select(array(
      'tb_stock_in_stores.id',
    ));
    $this->db->from('tb_stock_in_stores');
    // $this->db->where('tb_stock_in_stores.expired_date >= ', $start_date);
    $this->db->where('tb_stock_in_stores.expired_date <= ', $end_date);
    $this->db->where('tb_stock_in_stores.quantity > 0');

    //$this->db->group_by($this->getGroupByColumns());

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function expiredStock($start_date,$end_date)
  {
    $this->db->select(array(
      'tb_stocks.id',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.serial_number',
      'tb_stock_in_stores.expired_date',
      'tb_master_item_groups.category',
      'tb_master_item_groups.group',
      'tb_stocks.condition',
      'SUM(tb_stock_in_stores.quantity) as quantity',
      'tb_stock_in_stores.unit_value',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit',
      'tb_master_item_groups.coa',
      'tb_master_items.kode_stok',
      'tb_stock_in_stores.stores',
      'tb_stock_in_stores.warehouse',
      'tb_stock_in_stores.remarks',
      'tb_stock_in_stores.reference_document',
    ));
    $this->db->from('tb_stock_in_stores');
     $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_stock_in_stores.expired_date >= ', $start_date);
    $this->db->where('tb_stock_in_stores.expired_date <= ', $end_date);
    // $this->db->where('tb_stock_in_stores.expired_date >= ', $start_date);
    $this->db->where('condition', 'SERVICEABLE');
    $this->db->where('tb_stock_in_stores.quantity > 0');
    $this->db->group_by(array(
      'tb_stocks.id',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.serial_number',
      'tb_master_item_groups.category',
      'tb_master_item_groups.group',
      'tb_stock_in_stores.unit_value', 
      'tb_stocks.condition',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit',
      'tb_stock_in_stores.stores',
      'tb_stock_in_stores.warehouse',
      'tb_stock_in_stores.remarks',
      'tb_stock_in_stores.expired_date',
      'tb_stock_in_stores.reference_document',
      'tb_master_item_groups.coa',
      'tb_master_items.kode_stok',
    ));

    //$this->db->group_by($this->getGroupByColumns());

    $query = $this->db->get();

    return $query->result_array();
  }

}
