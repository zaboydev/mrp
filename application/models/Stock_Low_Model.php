<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_Low_Model extends MY_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function getSelectedColumns()
  {
    return array(
      'tb_master_items.id'                => NULL,
      'tb_master_items.part_number'       => 'Part Number',
      'tb_master_items.serial_number'     => 'Serial Number',
      'tb_master_items.description'       => 'Description',
      'tb_master_item_groups.category'    => 'Category',
      'tb_master_items.group'             => 'Group',
      'tb_stocks.total_quantity'          => 'Available Quantity',      
      'tb_master_items.minimum_quantity'  => 'Minimum Quantity',
      'tb_master_items.unit'              => 'Unit'
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_item_groups.category',
      'tb_master_items.group',
      'tb_stocks.total_quantity',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit'
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_item_groups.category',
      'tb_master_items.group',
    );
  }

  public function getGroupedColumns()
  {
    return array(
      'tb_master_part_number.part_number',
      'tb_master_part_number.id',
      'tb_master_items.serial_number'
      // 'tb_master_item_groups.category'
     
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

  public function getIndex($category = NULL, $return = 'array')
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_stocks');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    // $this->db->join('tb_master_part_number', 'tb_master_part_number.part_number = tb_master_items.part_number');
    $this->db->where('tb_stocks.condition', 'SERVICEABLE');
    $this->db->where('tb_stocks.total_quantity <= tb_master_items.minimum_quantity');
    // $this->db->group_by($this->getGroupedColumns());

    if ($category !== NULL){
      $this->db->where('tb_master_item_groups.category', $category);
    }

    $this->searchIndex();

    $orderableColumns = $this->getOrderableColumns();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($orderableColumns[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('tb_master_items.id', 'desc');
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

  public function countIndexFiltered($category = NULL)
  {
    $this->db->from('tb_stocks');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_stocks.condition', 'SERVICEABLE');
    $this->db->where('tb_stocks.total_quantity <= tb_master_items.minimum_quantity');

    if ($category !== NULL){
      $this->db->where('tb_master_item_groups.category', $category);
    }

    $this->searchIndex();
    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex($category = NULL)
  {
    $this->db->from('tb_stocks');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_stocks.condition', 'SERVICEABLE');
    $this->db->where('tb_stocks.total_quantity <= tb_master_items.minimum_quantity');

    if ($category !== NULL){
      $this->db->where('tb_master_item_groups.category', $category);
    }

    $query = $this->db->get();

    return $query->num_rows();
  }
}
