<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_Card_Model extends MY_Model
{
  public $index_column_select;
  public $index_column_order;
  public $index_column_search;

  public function __construct()
  {
    parent::__construct();

    $this->index_column_select = array(
      'date_of_entry',
      'document_number',
      'serial_number',
      'warehouse',
      'stores',
      'condition',
      'received_from',
      'received_by',
      'issued_to',
      'issued_by',
      'quantity',
      'notes'
     );

    $this->index_column_order = array(
      null,
      'date_of_entry',
      'document_number',
      'serial_number',
      'warehouse',
      'stores',
      'condition',
      'received_from',
      'received_by',
      'issued_to',
      'issued_by',
      'quantity',
      'notes'
    );

    $this->index_column_search = array(
      'date_of_entry',
      'document_number',
      'serial_number',
      'warehouse',
      'stores',
      'condition',
      'received_from',
      'received_by',
      'issued_to',
      'issued_by',
      'quantity',
      'notes'
    );
  }

  private function searchIndex()
  {
    if (!empty($_POST['columns'][11]['search']['value'])){
      $quantity = $_POST['columns'][11]['search']['value'];
      if ($quantity == 'zero'){
        $this->db->where('quantity = ', '0.00');
      } elseif ($quantity == 'gt_zero'){
        $this->db->where('quantity > ', '0.00');
      }
    }

    $i = 0;

    foreach ($this->index_column_search as $item){
      if ($_POST['search']['value']){
        $term = strtoupper($_POST['search']['value']);

        if ($i === 0){
          $this->db->group_start();
          $this->db->like('UPPER('.$item.')', $term);
        } else {
          $this->db->or_like('UPPER('.$item.')', $term);
        }

        if (count($this->index_column_search) - 1 == $i)
          $this->db->group_end();
      }

      $i++;
    }
  }

  public function getIndex($return = 'array')
  {
    $this->db->select($this->index_column_select);
    $this->db->from('tb_stock_cards');

    $this->searchIndex();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($this->index_column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('date_of_entry', 'desc');
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
    $this->db->from('tb_stock_cards');

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->from('tb_stock_cards');

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function getInfoSelectedColumns()
  {
    return array(
      NULL                                    => 'No.',
      'tb_stock_cards.date_of_entry'          => 'Date',
      'tb_stock_cards.document_number'        => 'Ref. Document',
      'tb_master_item_serials.serial_number'  => 'Serial Number',
      'tb_stock_cards.warehouse'              => 'Warehouse',
      'tb_stock_cards.stores'                 => 'Stores',
      'tb_stocks.condition'                   => 'Condition',
      'tb_stock_cards.received_from'          => 'Received From',
      'tb_stock_cards.received_by'            => 'Received By',
      'tb_stock_cards.issued_to'              => 'Issued To',
      'tb_stock_cards.issued_by'              => 'Issued By',
      'tb_stock_cards.quantity'               => 'Quantity',
      'tb_stock_cards.remarks'                => 'Remarks'
    );
  }

  public function getInfoSearchableColumns()
  {
    return array(
      'tb_stock_cards.document_number',
      'tb_master_item_serials.serial_number',
      'tb_stock_cards.warehouse',
      'tb_stock_cards.stores',
      'tb_stock_cards.received_from',
      'tb_stock_cards.received_by',
      'tb_stock_cards.issued_to',
      'tb_stock_cards.issued_by',
      'tb_stock_cards.remarks'
    );
  }

  public function getInfoOrderableColumns()
  {
    return array(
      null,
      'tb_stock_cards.date_of_entry',
      'tb_stock_cards.document_number',
      'tb_master_item_serials.serial_number',
      'tb_stock_cards.warehouse',
      'tb_stock_cards.stores',
      'tb_stocks.condition',
      'tb_stock_cards.received_from',
      'tb_stock_cards.received_by',
      'tb_stock_cards.issued_to',
      'tb_stock_cards.issued_by',
      'tb_stock_cards.quantity',
      'tb_stock_cards.remarks'
    );
  }

  private function searchInfo()
  {
    if (!empty($_POST['columns'][1]['search']['value'])){
      $search_date_of_entry = $_POST['columns'][1]['search']['value'];
      $range_date_of_entry  = explode(' ', $search_date_of_entry);

      $this->db->where('tb_stock_cards.date_of_entry >= ', $range_date_of_entry[0]);
      $this->db->where('tb_stock_cards.date_of_entry <= ', $range_date_of_entry[1]);
    }

    if (!empty($_POST['columns'][4]['search']['value'])){
      $search_warehouse = $_POST['columns'][4]['search']['value'];

      $this->db->where('tb_stock_cards.warehouse', $search_warehouse);
    }

    $i = 0;

    foreach ($this->getInfoSearchableColumns() as $item){
      if ($_POST['search']['value']){
        $term = strtoupper($_POST['search']['value']);

        if ($i === 0){
          $this->db->group_start();
          $this->db->like('UPPER('.$item.')', $term);
        } else {
          $this->db->or_like('UPPER('.$item.')', $term);
        }

        if (count($this->getInfoSearchableColumns()) - 1 == $i)
          $this->db->group_end();
      }

      $i++;
    }
  }

  public function getInfo($item_id, $return = 'array')
  {
    $this->db->select(array_keys($this->getInfoSelectedColumns()));
    $this->db->from('tb_stock_cards');
    $this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_cards.serial_id', 'left');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_cards.stock_id');
    $this->db->where('tb_stocks.item_id', $item_id);

    $this->searchInfo();

    $infoOrderColumns = $this->getInfoOrderableColumns();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($infoOrderColumns[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('date_of_entry', 'desc');
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

  public function countInfoFiltered($item_id)
  {
    $this->db->from('tb_stock_cards');
    $this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_cards.serial_id', 'left');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_cards.stock_id');
    $this->db->where('tb_stocks.item_id', $item_id);

    $this->searchInfo();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countInfo($item_id)
  {
    $this->db->from('tb_stock_cards');
    $this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_cards.serial_id', 'left');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_cards.stock_id');
    $this->db->where('tb_stocks.item_id', $item_id);

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function findItemById($id)
  {
    $this->db->from('tb_master_items');
    $this->db->where('tb_master_items.id', $id);

    $query = $this->db->get();
    $row   = $query->row_array();

    $row['total_quantity'] = $this->countQuantityByPartNumber($row['id']);

    return $row;
  }

  public function findItemStockCard($item_id)
  {
    $this->db->from('tb_master_items');
    $this->db->where('item_id', $item_id);
    $this->db->order_by('date_of_entry', 'DESC');

    $query = $this->db->get();
    $result = $query->result_array();

    return $result;
  }

  public function countQuantityByPartNumber($item_id)
  {
    $this->db->select_sum('tb_stocks.total_quantity', 'quantity');
    $this->db->select('tb_stocks.item_id');
    $this->db->from('tb_stocks');
    $this->db->where('tb_stocks.item_id', $item_id);
    $this->db->group_by('tb_stocks.item_id');

    $query  = $this->db->get();
    $row    = $query->row_array();

    return $row['quantity'];
  }

  public function find_all_item_in_stores_quantities($part_number)
  {
    $this->db->where('part_number', $part_number);
    $this->db->order_by('warehouse asc, stores asc');
    $query = $this->db->get('tb_stocks');

    return $query->result_array();
  }

  public function find_all_serial_numbers($part_number)
  {
    $this->db->where('part_number', $part_number);
    $this->db->order_by('serial_number', 'ASC');
    $query = $this->db->get('tb_master_item_serials');

    return $query->result_array();
  }

  public function get_item_value($field, $part_number)
  {
    $this->db->select($field);
    $this->db->where('part_number', $part_number);
    $query = $this->db->get($this->module['table']);
    $row = $query->row_array();

    return $row[$field];
  }

  public function find_serial_number()
  {
    if (config_item('auth_warehouse') !== config_item('main_warehouse'))
      $this->db->where('t1.warehouse', $warehouse);

    $this->db->where('t1.item_location', 'in stores');
    $this->db->select('t2.id, t1.part_number, t1.serial_number, t1.warehouse, t1.stores, t1.condition, t2.group, t2.description, t2.alternate_part_number, t2.unit');
    $this->db->join('tb_master_items t2', 't2.part_number = t1.part_number');
    $this->db->order_by('t2.group asc, t2.description asc, t1.stores asc');

    $query = $this->db->get('tb_master_item_serials t1');

    $result = $query->result_array();

    foreach ($result as $key => $row){
      //.. find item model
      $item_models = $this->find_all_item_models($row['part_number']);

      $result[$key]['aircraft_types'] = $item_models;
    }

    return $result;
  }

  public function is_duplicate($part_number)
  {
    $query = $this->db->get_where($this->module['table'], array(
      'part_number' => strtoupper($part_number),
   ));

    if ($query->num_rows() > 0)
      return true;

    return false;
  }

  public function get_item_quantities_quantity($part_number, $condition = null, $item_location = null)
  {
    $this->db->select('quantity');
    $this->db->where('part_number', $part_number);

    if ($condition !== null)
      $this->db->where('condition', $condition);

    if ($item_location !== null)
      $this->db->where('item_location', $item_location);

    $query = $this->db->get('tb_item_quantities');

    if ($query->num_rows() > 1){
      $result = $query->result_array();
      $quantity = array();

      foreach ($result as $row){
        $quantity[] = $row['quantity'];
      }

      return array_sum($quantity);
    } elseif ($query->num_rows() == 1){
      $row = $query->row_array();

      return $row['quantity'];
    } else {
      return 0;
    }
  }

  public function import(array $user_data)
  {
    $this->db->trans_begin();

    foreach ($user_data as $key => $data){
      //... tb_master_item_units
      if ($this->isItemUnitExists($data['unit']) === FALSE){
        $this->db->set('unit', strtoupper($data['unit']));
        $this->db->set('created_by', config_item('auth_username'));
        $this->db->set('updated_by', config_item('auth_username'));
        $this->db->insert(config_item('module')['item_unit']['table']);
      }

      //... tb_master_items
      if ($this->is_items_exists($data['part_number']) === FALSE){
        $this->db->set('part_number', strtoupper($data['part_number']));
        $this->db->set('alternate_part_number', strtoupper($data['alternate_part_number']));
        $this->db->set('description', strtoupper($data['description']));
        $this->db->set('group', strtoupper($data['group']));
        $this->db->set('unit', strtoupper($data['unit']));
        $this->db->set('created_by', config_item('auth_username'));
        $this->db->set('updated_by', config_item('auth_username'));
        $this->db->insert('tb_master_items');
      }

      //... tb_master_item_serials
      if ($this->isSerialNumberExists($data['serial_number']) === FALSE){
        $this->db->set('part_number', strtoupper($data['part_number']));
        $this->db->set('serial_number', strtoupper($data['serial_number']));
        $this->db->set('updated_by', config_item('auth_username'));
        $this->db->insert('tb_master_item_serials');
      }

      //... tb_item_models
      if ($data['aircraft_types'] !== NULL){
        $this->db->where('part_number', $data['part_number']);
        $this->db->delete('tb_item_models');

        foreach (explode(';', $data['aircraft_types']) as $aircraft_type){
          $this->db->set('part_number', $data['part_number']);
          $this->db->set('aircraft_type', $aircraft_type);
          $this->db->insert('tb_item_models');
        }
      }

      //... tb_stocks
      $this->db->set('part_number', strtoupper($data['part_number']));
      $this->db->set('serial_number', strtoupper($data['serial_number']));
      $this->db->set('warehouse', strtoupper($data['warehouse']));
      $this->db->set('stores', strtoupper($data['stores']));
      $this->db->set('condition', strtoupper($data['condition']));
      $this->db->set('quantity', strtoupper($data['quantity']));
      $this->db->set('notes', 'import');
      $this->db->set('date_of_entry', date('Y-m-d'));
      $this->db->set('updated_by', config_item('auth_username'));
      $this->db->insert('tb_stocks');

      //... tb_item_quantity_details
      $this->db->set('part_number', strtoupper($data['part_number']));
      $this->db->set('serial_number', strtoupper($data['serial_number']));
      $this->db->set('item_location', 'in stores');
      $this->db->set('condition', strtoupper($data['condition']));
      $this->db->set('warehouse', strtoupper($data['warehouse']));
      $this->db->set('stores', strtoupper($data['stores']));
      $this->db->set('quantity', $data['quantity']);
      $this->db->set('notes', 'import');
      $this->db->set('updated_by', config_item('auth_username'));
      $this->db->set('received_by', config_item('auth_person_name'));
      $this->db->set('document_date', date('Y-m-d'));
      $this->db->insert('tb_item_quantity_details');
    }

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  /**
   * Delete (a) record(s)
   *
   * @param array $criteria
   * @return bool
   */
  public function delete(array $criteria){
    $this->db->trans_begin();

    $this->db->where($criteria)
      ->delete($this->module['table']);

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }
}
