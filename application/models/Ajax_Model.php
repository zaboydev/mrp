<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax_Model extends MY_Model
{
  protected $connection;

  public function __construct()
  {
    parent::__construct();    
    $this->connection   = $this->load->database('budgetcontrol', TRUE);
  }

  protected function findTable($module)
  {
    $modules = config_item('module');

    return $modules[$module]['table'];
  }

  public function part_number_validation($value, $old_value = NULL)
  {
    $this->db->from(config_item('module')['item']['table']);

    if ($old_value !== NULL)
      $this->db->where('UPPER(part_number) != ', strtoupper($old_value));

    $this->db->where('UPPER(part_number)', strtoupper($value));

    $query = $this->db->get();

    return ($query->num_rows() > 0) ? FALSE : TRUE;
  }

  public function item_category_validation($value, $old_value = NULL)
  {
    $this->db->from(config_item('module')['category']['table']);

    if ($old_value !== NULL)
      $this->db->where('UPPER(category) != ', strtoupper($old_value));

    $this->db->where('UPPER(category)', strtoupper($value));

    $query = $this->db->get();

    return ($query->num_rows() > 0) ? FALSE : TRUE;
  }

  public function item_category_code_validation($value, $old_value = NULL)
  {
    $this->db->from(config_item('module')['category']['table']);

    if ($old_value !== NULL)
      $this->db->where('UPPER(code) != ', strtoupper($old_value));

    $this->db->where('UPPER(code)', strtoupper($value));

    $query = $this->db->get();

    return ($query->num_rows() > 0) ? FALSE : TRUE;
  }

  public function item_application_description_validation($value, $old_value = NULL)
  {
    $this->db->from(config_item('module')['item_application']['table']);

    if ($old_value !== NULL)
      $this->db->where('UPPER(description) != ', strtoupper($old_value));

    $this->db->where('UPPER(description)', strtoupper($value));

    $query = $this->db->get();

    return ($query->num_rows() > 0) ? FALSE : TRUE;
  }

  public function item_group_validation($value, $old_value = NULL)
  {
    $this->db->from(config_item('module')['group']['table']);

    if ($old_value !== NULL)
      $this->db->where('UPPER(group) != ', strtoupper($old_value));

    $this->db->where('UPPER(group)', strtoupper($value));

    $query = $this->db->get();

    return ($query->num_rows() > 0) ? FALSE : TRUE;
  }

  public function item_group_code_validation($value, $old_value = NULL)
  {
    $this->db->from(config_item('module')['group']['table']);

    if ($old_value !== NULL)
      $this->db->where('UPPER(code) != ', strtoupper($old_value));

    $this->db->where('UPPER(code)', strtoupper($value));

    $query = $this->db->get();

    return ($query->num_rows() > 0) ? FALSE : TRUE;
  }

  public function stores_validation($value, $old_value = NULL)
  {
    $this->db->from(config_item('module')['stores']['table']);

    if ($old_value !== NULL)
      $this->db->where('UPPER(stores) != ', strtoupper($old_value));

    $this->db->where('UPPER(stores)', strtoupper($value));

    $query = $this->db->get();

    return ($query->num_rows() > 0) ? FALSE : TRUE;
  }

  public function unit_validation($value, $old_value = NULL)
  {
    $this->db->from(config_item('module')['item_unit']['table']);

    if ($old_value !== NULL)
      $this->db->where('UPPER(unit) != ', strtoupper($old_value));

    $this->db->where('UPPER(unit)', strtoupper($value));

    $query = $this->db->get();

    return ($query->num_rows() > 0) ? FALSE : TRUE;
  }

  public function user_email_validation($value, $old_value = NULL)
  {
    $this->db->from(config_item('module')['user']['table']);

    if ($old_value !== NULL)
      $this->db->where('UPPER(email) != ', strtoupper($old_value));

    $this->db->where('UPPER(email)', strtoupper($value));

    $query = $this->db->get();

    return ($query->num_rows() > 0) ? FALSE : TRUE;
  }

  public function username_validation($value, $old_value = NULL)
  {
    $this->db->from(config_item('module')['user']['table']);

    if ($old_value !== NULL)
      $this->db->where('UPPER(username) != ', strtoupper($old_value));

    $this->db->where('UPPER(username)', strtoupper($value));

    $query = $this->db->get();

    return ($query->num_rows() > 0) ? FALSE : TRUE;
  }

  public function vendor_validation($value, $old_value = NULL)
  {
    $this->db->from(config_item('module')['vendor']['table']);

    if ($old_value !== NULL)
      $this->db->where('UPPER(vendor) != ', strtoupper($old_value));

    $this->db->where('UPPER(vendor)', strtoupper($value));

    $query = $this->db->get();

    return ($query->num_rows() > 0) ? FALSE : TRUE;
  }

  public function warehouse_validation($value, $old_value = NULL)
  {
    $this->db->from(config_item('module')['warehouse']['table']);

    if ($old_value !== NULL)
      $this->db->where('UPPER(warehouse) != ', strtoupper($old_value));

    $this->db->where('UPPER(warehouse)', strtoupper($value));

    $query = $this->db->get();

    return ($query->num_rows() > 0) ? FALSE : TRUE;
  }

  public function warehouse_alternate_name_validation($value, $old_value = NULL)
  {
    $this->db->from(config_item('module')['warehouse']['table']);

    if ($old_value !== NULL)
      $this->db->where('UPPER(alternate_warehouse_name) != ', strtoupper($old_value));

    $this->db->where('UPPER(alternate_warehouse_name)', strtoupper($value));

    $query = $this->db->get();

    return ($query->num_rows() > 0) ? FALSE : TRUE;
  }

  public function listItems(array $category = NULL)
  {
    $this->column_select = array(
      'tb_master_items.id',
      'tb_master_item_groups.category',
      'tb_master_items.group',
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.alternate_part_number',
      'tb_master_item_serials.serial_number',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit'
     );

    $this->column_group_by = array(
      'tb_master_items.id',
      'tb_master_item_groups.category',
      'tb_master_items.group',
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.alternate_part_number',
      'tb_master_item_serials.serial_number',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit'
     );

    $this->db->select($this->column_select);
    $this->db->from('tb_stocks');
    $this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stocks.serial_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_master_item_groups.status', 'AVAILABLE');

    if ($category !== NULL){
      $this->db->where_in('tb_master_item_groups.category', $category);
    }

    $this->db->order_by('tb_master_items.group ASC, tb_master_items.description ASC');
    $this->db->group_by($this->column_group_by);

    $query  = $this->db->get();
    $result = $query->result_array();

    return $result;
  }

  public function listGeneralStock(array $category = NULL)
  {
    $this->column_select = array(
      'tb_master_items.id',
      'tb_master_item_groups.category',
      'tb_master_items.group',
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.alternate_part_number',
      'tb_master_item_serials.serial_number',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit'
     );

    $this->column_group_by = array(
      'tb_master_items.id',
      'tb_master_item_groups.category',
      'tb_master_items.group',
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.alternate_part_number',
      'tb_master_item_serials.serial_number',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit'
     );

    $this->db->select_sum('tb_stocks.total_quantity', 'on_hand_quantity');
    $this->db->select($this->column_select);
    $this->db->from('tb_stocks');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_master_item_groups.status', 'AVAILABLE');

    if ($category !== NULL){
      $this->db->where_in('tb_master_item_groups.category', $category);
    }

    $this->db->order_by('tb_master_items.group ASC, tb_master_items.description ASC');

    $query  = $this->db->get();
    $result = $query->result_array();

    return $result;
  }

  public function getAvailableStock($warehouse = NULL, array $category = NULL)
  {
    $selected_columns = array(
      'tb_stocks.id',
      'tb_stocks.stock_id',
      'tb_stocks.quantity',
      'tb_stocks.warehouse',
      'tb_stocks.stores',
      'tb_stocks.received_date',
      'tb_stocks.document_number',
      'tb_stocks.expired_date',
      'tb_stocks.condition',
      'tb_master_item_serials.serial_number',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.group',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit',
    );

    $this->db->select($selected_columns);
    $this->db->from('tb_stock_in_stores tb_stocks');
    $this->db->join('tb_stocks tb_stocks', 'tb_stocks.id = tb_stocks.stock_id');
    $this->db->join('tb_master_items tb_master_items', 'tb_master_items.part_number = tb_stocks.item_id');

    if ($warehouse !== NULL)
      $this->db->where('tb_stocks.warehouse', $warehouse);

    if ($category !== NULL){
      $this->db->join('tb_master_item_groups tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
      $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
      $this->db->where_in('tb_master_item_groups.category', $category);
    }

    $this->db->where('tb_stocks.quantity > ', 0);
    $this->db->where('tb_stocks.quantity > tb_master_items.minimum_quantity');
    $this->db->order_by('tb_master_items.group ASC, tb_master_items.description ASC');

    $query  = $this->db->get();
    $result = $query->result_array();

    foreach ($result as $key => $row) {
      $result[$key]['available_quantity'] = $row['quantity'] - $row['minimum_quantity'];
      // $result[$key]['on_hand_quantity']   = count_on_hand_quantity($row['part_number']);
      $result[$key]['on_hand_quantity']   = count_stock_in_stores($row['stock_id']);
    }

    return $result;
  }

  public function listItemOnDeliveries($warehouse, array $category = NULL)
  {
    $this->db->select('t1.*, t2.description, t2.unit, t2.group, t2.minimum_quantity');
    $this->db->from($this->findTable('item_on_delivery') .' t1');
    $this->db->join('tb_master_items t2', 't2.part_number = t1.part_number');
    $this->db->where('t1.warehouse', $warehouse);

    if ($category !== NULL){
      $this->db->join('tb_master_item_groups' .' t3', 't3.group = t2.group');
      $this->db->where('t3.status', 'AVAILABLE');
      $this->db->where_in('t3.category', $category);
    }

    $this->db->where('t1.quantity > ', 0);
    $this->db->order_by('t2.group ASC, t2.description ASC');

    $query  = $this->db->get();
    $result = $query->result_array();

    foreach ($result as $key => $row) {
      $result[$key]['on_hand_quantity'] = $row['quantity'];
    }

    return $result;
  }

  public function listItemInUses(array $category = NULL)
  {
    $this->db->select('t1.*, t2.description, t2.unit, t2.group');
    $this->db->from($this->findTable('item_in_use') .' t1');
    $this->db->join('tb_master_items t2', 't2.part_number = t1.part_number');

    if ($category !== NULL){
      $this->db->join('tb_master_item_groups' .' t3', 't3.group = t2.group');
      $this->db->where('t3.status', 'AVAILABLE');
      $this->db->where_in('t3.category', $category);
    }

    $this->db->where('t1.quantity > ', 0);
    $this->db->order_by('t2.group ASC, t2.description ASC');

    $query  = $this->db->get();
    $result = $query->result_array();

    foreach ($result as $key => $row) {
      $result[$key]['available_quantity'] = $row['quantity'];
    }

    return $result;
  }

  public function listItemDescription($category = NULL)
  {
    if ($category !== NULL){
      $this->db->join('tb_master_item_groups',
        'tb_master_item_groups.group = tb_master_items.group');

      $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
      $this->db->where_in('category', $category);
    }

    $this->db->distinct();
    $this->db->select('tb_master_items.description');
    $this->db->from('tb_master_items');
    $this->db->order_by('tb_master_items.description', 'ASC');

    $query  = $this->db->get();
    $result = $query->result();

    $data  = array();

    foreach ($result as $row){
      if ($row->description != null)
        $data[] = $row->description;
    }

    return json_encode($data);
  }

  public function listItemPartNumber($category = NULL)
  {
    if ($category !== NULL){
      $this->db->join('tb_master_item_groups',
        'tb_master_item_groups.group = tb_master_items.group');

      $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
      $this->db->where_in('category', $category);
    }

    $this->db->select('tb_master_items.part_number');
    $this->db->from('tb_master_items');
    $this->db->order_by('tb_master_items.part_number', 'ASC');

    $query  = $this->db->get();
    $result = $query->result();

    $data  = array();

    foreach ($result as $row){
      if ($row->part_number != null)
        $data[] = $row->part_number;
    }

    return json_encode($data);
  }

  public function listItemAltPartNumber($category = NULL)
  {
    if ($category !== NULL){
      $this->db->join('tb_master_item_groups',
        'tb_master_item_groups.group = tb_master_items.group');

      $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
      $this->db->where_in('category', $category);
    }

    $this->db->distinct();
    $this->db->select('tb_master_items.alternate_part_number');
    $this->db->from('tb_master_items');
    $this->db->order_by('tb_master_items.alternate_part_number', 'ASC');

    $query  = $this->db->get();
    $result = $query->result();

    $data  = array();

    foreach ($result as $row){
      if ($row->alternate_part_number != null)
        $data[] = $row->alternate_part_number;
    }

    return json_encode($data);
  }

  public function listItemSerialNumber($category = NULL)
  {
    if ($category !== NULL){
      $this->db->join('tb_master_items', 'tb_master_items.id = tb_master_item_serials.item_id');
      $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
      $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
      $this->db->where_in('tb_master_item_groups.category', $category);
    }

    $this->db->select('tb_master_item_serials.serial_number');
    $this->db->from('tb_master_item_serials');
    $this->db->order_by('tb_master_item_serials.serial_number', 'ASC');

    $query  = $this->db->get();
    $result = $query->result();

    $data  = array();

    foreach ($result as $row){
      if ($row->serial_number != null)
        $data[] = $row->serial_number;
    }

    return json_encode($data);
  }

  public function listStores($category, $warehouse)
  {
    $this->db->select('stores');
    $this->db->from($this->findTable('stores'));
    $this->db->where('warehouse', $warehouse);
    $this->db->where_in('category', $category);
    $this->db->where('status', 'AVAILABLE');
    $this->db->order_by('stores', 'ASC');

    $query  = $this->db->get();
    $result = $query->result();

    $data  = array();

    foreach ($result as $row){
      if ($row->stores != null)
        $data[] = $row->stores;
    }

    return json_encode($data);
  }

  public function listItemUnits()
  {
    $this->db->select('unit');
    $this->db->from('tb_master_item_units');
    $this->db->where('status', 'AVAILABLE');
    $this->db->order_by('unit', 'ASC');

    $query  = $this->db->get();
    $result = $query->result();

    $data  = array();

    foreach ($result as $row){
      if ($row->unit != null)
        $data[] = $row->unit;
    }

    return json_encode($data);
  }

  public function findItemByPartNumber($part_number)
  {
    $this->db->from('tb_master_items');
    $this->db->where('part_number', $part_number);

    $query  = $this->db->get();
    $row    = $query->row_array();

    return json_encode($row);
  }

  public function findItemBySerialNumber($item_serial)
  {
    $this->db->select('tb_master_items.*');
    $this->db->from('tb_master_items');
    $this->db->join('tb_master_item_serials',
      'tb_master_item_serials.item_id = tb_master_items.part_number');
    $this->db->where('tb_master_item_serials.serial_number', $item_serial);

    $query  = $this->db->get();
    $row    = $query->row_array();

    return json_encode($row);
  }

  public function distinct($table, $select, array $criteria = null, $json = false)
  {
    $this->db->distinct();

    $this->db->select($select);

    if ($criteria !== null)
      $this->db->where($criteria);

    $this->db->order_by($select);

    $query  = $this->db->get($table);
    $result = $query->result();

    $data  = array();

    foreach ($result as $entity){
      if ($entity->$select != null)
        $data[] = $entity->$select;
    }

    if ($json === false)
      return $data;

    return json_encode($data);
  }

  public function department_name_validation($value, $old_value = NULL)
  {
    $this->connection->from('tb_divisions');

    if ($old_value !== NULL)
      $this->connection->where('UPPER(division_name) != ', strtoupper($old_value));

    $this->connection->where('UPPER(division_name)', strtoupper($value));

    $query = $this->connection->get();

    return ($query->num_rows() > 0) ? FALSE : TRUE;
  }

  public function user_position_validation($value, $old_value = NULL)
  {
    $this->db->from(config_item('module')['user_position']['table']);

    if ($old_value !== NULL)
      $this->db->where('UPPER(position) != ', strtoupper($old_value));

    $this->db->where('UPPER(position)', strtoupper($value));

    $query = $this->db->get();

    return ($query->num_rows() > 0) ? FALSE : TRUE;
  }

  public function user_position_code_validation($value, $old_value = NULL)
  {
    $this->db->from(config_item('module')['user_position']['table']);

    if ($old_value !== NULL)
      $this->db->where('UPPER(code) != ', strtoupper($old_value));

    $this->db->where('UPPER(code)', strtoupper($value));

    $query = $this->db->get();

    return ($query->num_rows() > 0) ? FALSE : TRUE;
  }

  public function level_validation($value, $old_value = NULL)
  {
    $this->db->from(config_item('module')['level']['table']);

    if ($old_value !== NULL)
      $this->db->where('UPPER(level) != ', strtoupper($old_value));

    $this->db->where('UPPER(level)', strtoupper($value));

    $query = $this->db->get();

    return ($query->num_rows() > 0) ? FALSE : TRUE;
  }

  public function level_code_validation($value, $old_value = NULL)
  {
    $this->db->from(config_item('module')['level']['table']);

    if ($old_value !== NULL)
      $this->db->where('UPPER(code) != ', strtoupper($old_value));

    $this->db->where('UPPER(code)', strtoupper($value));

    $query = $this->db->get();

    return ($query->num_rows() > 0) ? FALSE : TRUE;
  }

}
