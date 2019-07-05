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
}
