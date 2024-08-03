<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_Daily_Report_Model extends MY_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function getSelectedColumns()
  {
    return array(
      NULL                                                        => NULL,
      'tb_master_items.description'                               => 'Description',
      'tb_master_items.part_number'                               => 'Part Number',
      'tb_master_item_groups.category'                            => 'Category',
      'tb_master_item_groups.group'                               => 'Group',
      'tb_stocks.condition'                                       => 'Condition',
      'SUM(tb_stock_cards.prev_quantity) as prev_quantity' => 'Prev. Qty',
      'SUM(tb_stock_cards.quantity) as quantity'                  => 'Quantity',
      'SUM(tb_stock_cards.prev_quantity) + SUM(tb_stock_cards.quantity) as balance_quantity'  => 'Balance',
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_item_groups.category',
      'tb_master_item_groups.group',
      'tb_stocks.condition',
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_item_groups.category',
      'tb_master_item_groups.group',
      'tb_stocks.condition',
      'SUM(tb_stock_cards.prev_quantity)',
      'SUM(tb_stock_cards.quantity)',
      'SUM(tb_stock_cards.prev_quantity) + SUM(tb_stock_cards.quantity)',
    );
  }

  public function getGroupByColumns()
  {
    return array(
      // 'tb_stocks.id',
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_item_groups.category',
      'tb_master_item_groups.group',
      'tb_stocks.condition',
    );
  }

  private function searchIndex()
  {
    if (!empty($_POST['columns'][0]['search']['value'])){
      $search_created_at = $_POST['columns'][0]['search']['value'];
      $range_created_at  = explode(' ', $search_created_at);

      $this->db->where('DATE(tb_stock_cards.date_of_entry) >= ', $range_created_at[0]);
      $this->db->where('DATE(tb_stock_cards.date_of_entry) <= ', $range_created_at[1]);
    } else {
      $this->db->where('DATE(tb_stock_cards.date_of_entry) >= ', date('Y-m-d'));
      $this->db->where('DATE(tb_stock_cards.date_of_entry) <= ', date('Y-m-d'));
    }

    if (!empty($_POST['columns'][111]['search']['value'])){
      $search_warehouse = $_POST['columns'][111]['search']['value'];

      $this->db->where('tb_stock_cards.warehouse', $search_warehouse);
    }

    if (!empty($_POST['columns'][3]['search']['value'])){
      $search_category = $_POST['columns'][3]['search']['value'];

      $this->db->where('tb_master_item_groups.category', $search_category);
    }

    if (!empty($_POST['columns'][5]['search']['value'])){
      $search_condition = $_POST['columns'][5]['search']['value'];

      $this->db->where('tb_stocks.condition', $search_condition);
    } else {
      $this->db->where('tb_stocks.condition', 'SERVICEABLE');
    }

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

  public function getIndex($return = 'array')
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_stock_cards');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_cards.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->group_by($this->getGroupByColumns());

    $this->searchIndex();

    $IndexOrderColumns = $this->getOrderableColumns();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($IndexOrderColumns[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('tb_master_items.description', 'asc');
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

  public function countIndexFiltered()
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_stock_cards');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_cards.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->group_by($this->getGroupByColumns());

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_stock_cards');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_cards.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->group_by($this->getGroupByColumns());

    $query = $this->db->get();

    return $query->num_rows();
  }
}
