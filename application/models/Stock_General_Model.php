<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_General_Model extends MY_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function getSelectedColumns()
  {
    return array(
      NULL                                    => 'No',
      'tb_master_items.part_number'           => 'Part Number',
      'tb_master_items.serial_number'           => 'Serial Number',
      'tb_master_item_groups.category'        => 'Category',
      'tb_stocks.condition'                   => 'Condition',
      'tb_master_items.unit'                  => 'Unit',
      'SUM(CASE WHEN tb_stock_in_stores.warehouse_id = 1 THEN tb_stock_in_stores.quantity ELSE 0 END) AS wisnu_qty' => 'Wisnu',
      'SUM(CASE WHEN tb_stock_in_stores.warehouse_id = 2 THEN tb_stock_in_stores.quantity ELSE 0 END) AS byw_qty' => 'Banyuwangi',
      'SUM(CASE WHEN tb_stock_in_stores.warehouse_id = 3 THEN tb_stock_in_stores.quantity ELSE 0 END) AS solo_qty' => 'Solo',
      'SUM(CASE WHEN tb_stock_in_stores.warehouse_id = 4 THEN tb_stock_in_stores.quantity ELSE 0 END) AS lmbk_qty' => 'Lombok',
      'SUM(CASE WHEN tb_stock_in_stores.warehouse_id = 5 THEN tb_stock_in_stores.quantity ELSE 0 END) AS jmbr_qty' => 'Jember',
      'SUM(CASE WHEN tb_stock_in_stores.warehouse_id = 6 THEN tb_stock_in_stores.quantity ELSE 0 END) AS plkry_qty' => 'Palangkaraya',
      'SUM(CASE WHEN tb_stock_in_stores.warehouse_id = 7 THEN tb_stock_in_stores.quantity ELSE 0 END) AS wisnu_rekon_qty' => 'Wisnu Rekondisi',
      'SUM(CASE WHEN tb_stock_in_stores.warehouse_id = 8 THEN tb_stock_in_stores.quantity ELSE 0 END) AS bsr_qty' => 'BSR Rekondisi',
      //'SUM(CASE WHEN tb_master_items.part_number THEN tb_stock_in_stores.quantity ELSE 0 END) AS total' => 'Total',
      'SUM(CASE WHEN tb_stock_in_stores.warehouse_id = 8 THEN tb_stock_in_stores.quantity ELSE 0 END) AS total_qty' => 'Total',
    );
  }

  public function getGroupedColumns()
  {
    return array(
      'tb_master_items.part_number',
      'tb_master_items.serial_number',
      'tb_master_item_groups.category',
     
      'tb_stocks.condition',
      'tb_master_items.unit',
     
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'tb_master_items.part_number',
      'tb_master_items.serial_number',
      'tb_master_item_groups.category',
      'tb_stocks.condition',
      'tb_master_items.unit',
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'tb_master_items.part_number',
      'tb_master_items.serial_number',
      'tb_master_item_groups.category',
      'tb_stocks.condition',
      'tb_master_items.unit',
    );
  }

  private function searchIndex()
  {
    $i = 0;

    foreach ($this->getSearchableColumns() as $item){
      if ($_POST['search']['value']){
        $term = strtoupper($_POST['search']['value']);

        if ($i === 0){
          $this->db->group_start();
          $this->db->like('UPPER('.$item.')', $term);
        } else {
          $this->db->or_like('UPPER('.$item.')', $term);
        }

        if (count($this->getSearchableColumns()) - 1 == $i)
          $this->db->group_end();
      }

      $i++;
    }
  }

  public function getIndex($condition = "SERVICEABLE", $category = NULL, $start_date=NULL, $end_date=NULL, $return = 'array' )
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_stock_in_stores');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    // $this->db->where('tb_stock_in_stores.received_date >= ', $start_date);
    // $this->db->where('tb_stock_in_stores.received_date <= ', $end_date);
    $this->db->where('condition', $condition);

    if ($start_date && $end_date !== NULL){
      $this->db->where('tb_stock_in_stores.received_date >= ', $start_date);
      $this->db->where('tb_stock_in_stores.received_date <= ', $end_date);
    }

    if ($category !== NULL){
      $this->db->where('tb_master_item_groups.category', $category);
    }

    $this->db->group_by($this->getGroupedColumns());
    $this->searchIndex();

    $orderableColumns = $this->getOrderableColumns();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($orderableColumns[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('tb_master_items.part_number', 'asc');
    }

    if ($_POST['length'] != -1)
      $this->db->limit($_POST['length'], $_POST['start']);

    $query = $this->db->get();

    if ($return === 'object'){
      return $query->result();
    } elseif ($return === 'json'){
      return json_encode($query->result());
    } else {
      return $query->result_array();
    }
  }

  public function countIndexFiltered($condition = "SERVICEABLE", $category = NULL, $start_date=NULL, $end_date=NULL)
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_stock_in_stores');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    // $this->db->where('tb_stock_in_stores.received_date >= ', $start_date);
    // $this->db->where('tb_stock_in_stores.received_date <= ', $end_date);
    $this->db->where('condition', $condition);

    if ($start_date && $end_date !== NULL){
      $this->db->where('tb_stock_in_stores.received_date >= ', $start_date);
      $this->db->where('tb_stock_in_stores.received_date <= ', $end_date);
    }

    if ($category !== NULL){
      $this->db->where('tb_master_item_groups.category', $category);
    }

    $this->db->group_by($this->getGroupedColumns());
    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex($condition = "SERVICEABLE", $category = NULL, $start_date=NULL, $end_date=NULL)
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_stock_in_stores');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    // $this->db->where('tb_stock_in_stores.received_date >= ', $start_date);
    // $this->db->where('tb_stock_in_stores.received_date <= ', $end_date);
    $this->db->where('condition', $condition);

    if ($start_date && $end_date !== NULL){
      $this->db->where('tb_stock_in_stores.received_date >= ', $start_date);
      $this->db->where('tb_stock_in_stores.received_date <= ', $end_date);
    }

    if ($category !== NULL){
      $this->db->where('tb_master_item_groups.category', $category);
    }

    $this->db->group_by($this->getGroupedColumns());
    $query = $this->db->get();

    return $query->num_rows();
  }
}
