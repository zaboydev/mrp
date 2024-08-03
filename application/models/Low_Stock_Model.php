<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Low_Stock_Model extends MY_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function getSelectedColumns()
  {
    return array(
      null                                   => NULL,
      null => null,
      'tb_master_items.part_number'                          => 'Part Number',
      'tb_master_items.description'                              => 'Description',
      'tb_master_items.minimum_quantity'                                => 'Minimum Quantity',
      'tb_stocks.condition'               => 'Condition',
      'sum(quantity) as qty'                                  => 'Available Quantity',
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'tb_master_items.part_number',
      'tb_master_items.description',
      // 'tb_master_items.minimum_quantity',
      'tb_stocks.condition'

    );
  }

  public function getOrderableColumns()
  {
    return array(
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.minimum_quantity',
      'sum(quantity) as qty',
      'tb_stocks.condition'
    );
  }

  public function getGroupByColumns()
  {
    return array(
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.minimum_quantity',
      'tb_stocks.condition'
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

  public function getIndex($category = 'SPARE PART',$return = 'array')
  {
    
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_stock_in_stores');
    $this->db->join('tb_stocks', 'tb_stocks.id=tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_stocks.item_id=tb_master_items.id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group=tb_master_items.group');
    // $this->db->where('tb_master_part_number.qty <= tb_master_part_number.min_qty');
    if ($category !== NULL) {
      $this->db->where('tb_master_item_groups.category', $category);
    }
    $this->db->group_by($this->getGroupByColumns());

    $this->searchIndex();

    $IndexOrderColumns = $this->getOrderableColumns();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($IndexOrderColumns[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
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

  public function countIndexFiltered($category = 'SPARE PART')
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_stock_in_stores');
    $this->db->join('tb_stocks', 'tb_stocks.id=tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_stocks.item_id=tb_master_items.id');
    // $this->db->where('tb_master_part_number.qty <= tb_master_part_number.min_qty');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group=tb_master_items.group');
    // $this->db->where('tb_master_part_number.qty <= tb_master_part_number.min_qty');
    if ($category !== NULL) {
      $this->db->where('tb_master_item_groups.category', $category);
    }
    $this->db->group_by($this->getGroupByColumns());

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex($category = 'SPARE PART')
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_stock_in_stores');
    $this->db->join('tb_stocks', 'tb_stocks.id=tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_stocks.item_id=tb_master_items.id');
    // $this->db->where('tb_master_part_number.qty <= tb_master_part_number.min_qty');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group=tb_master_items.group');
    // $this->db->where('tb_master_part_number.qty <= tb_master_part_number.min_qty');
    if ($category !== NULL) {
      $this->db->where('tb_master_item_groups.category', $category);
    }
    $this->db->group_by($this->getGroupByColumns());

    $query = $this->db->get();

    return $query->num_rows();
  }


// detail stock low
  public function getDetailSelectedColumns()
  {
    return array(
      'tb_stocks.id'                                  => NULL,
      'tb_master_items.part_number'                   => 'Part Number',
      'tb_master_items.description'                   => 'Description',
      'tb_master_items.serial_number'                 => 'Serial Number',
      'tb_stocks.condition'                           => 'Condition',
      'tb_master_items.minimum_quantity'              => 'Min. Stock',
      'SUM(tb_stock_in_stores.quantity) as quantity'  => 'Stock Quantity',
      'tb_master_items.unit'                          => 'Unit',
      'tb_stock_in_stores.stores'                     => 'Stores',
      'tb_stock_in_stores.warehouse'                  => 'Base',

    );
  }

  public function getDetailOrderableColumns()
  {
    return array(
      NULL,
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.serial_number',
      'tb_stocks.condition',
      'SUM(tb_stock_in_stores.quantity)',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit',
      'tb_stock_in_stores.stores',
      'tb_stock_in_stores.warehouse',
    );
  }

  public function getDetailSearchableColumns()
  {
    return array(
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.serial_number',
      'tb_stocks.condition',
      // 'SUM(tb_stock_in_stores.quantity)',
      // 'tb_master_items.minimum_quantity',
      'tb_master_items.unit',
      'tb_stock_in_stores.stores',
      'tb_stock_in_stores.warehouse',
    );
  }

  public function getDetailGroupByColumns()
  {
    return array(
      'tb_stocks.id',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.serial_number',
      'tb_stocks.condition',
      // 'SUM(tb_stock_in_stores.quantity)',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit',
      'tb_stock_in_stores.stores',
      'tb_stock_in_stores.warehouse',
    );
  }

  private function searchDetailIndex()
  {
    $i = 0;

    foreach ($this->getDetailSearchableColumns() as $item){
      if ($_POST['search']['value']){
        $term = strtoupper($_POST['search']['value']);

        if ($i === 0){
          $this->db->group_start();
          $this->db->like('UPPER('.$item.')', $term);
        } else {
          $this->db->or_like('UPPER('.$item.')', $term);
        }

        if (count($this->getDetailSearchableColumns()) - 1 == $i)
          $this->db->group_end();
      }

      $i++;
    }
  }

  public function getDetailIndex($part_number,$return = 'array')
  {
    $this->db->select(array_keys($this->getDetailSelectedColumns()));
    $this->db->from('tb_stock_in_stores');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    // $this->db->where('tb_stocks.condition', 'SERVICEABLE');
    $this->db->group_by($this->getDetailGroupByColumns());

    if ($part_number !== NULL){
      $this->db->where('tb_master_items.part_number', $part_number);
    }

    $this->searchDetailIndex();

    $IndexOrderColumns = $this->getDetailOrderableColumns();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($IndexOrderColumns[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('tb_master_items.part_number', 'desc');
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

  public function countDetailIndexFiltered($part_number=NULL)
  {
    $this->db->select(array_keys($this->getDetailSelectedColumns()));
    $this->db->from('tb_stock_in_stores');    
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->group_by($this->getDetailGroupByColumns());

    if ($part_number !== NULL){
      $this->db->where('tb_master_items.part_number', $part_number);
    }

    $this->searchDetailIndex();
    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countDetailIndex($part_number=NULL)
  {
    $this->db->select(array_keys($this->getDetailSelectedColumns()));
    $this->db->from('tb_stock_in_stores');    
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->group_by($this->getDetailGroupByColumns());

    if ($part_number !== NULL){
      $this->db->where('tb_master_items.part_number', $part_number);
    }

    // $this->searchDetailIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  
}
