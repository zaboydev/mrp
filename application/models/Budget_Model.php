<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_Request_Model extends MY_Model
{
  protected $module;
  protected $connection;
  protected $active_year;
  protected $active_month;

  public function __construct()
  {
    parent::__construct();

    $this->module = config_item('module')['purchase_request']['table'];
    $this->connection = $this->load->database('budgetcontrol', TRUE);
    $this->active_year  = $this->find_active_year();
    $this->active_month = $this->find_active_month();
  }

  public function find_active_year()
  {
      $this->connection->where('setting_name', 'Active Year');
      $query = $this->connection->get('tb_settings');
      $row   = $query->row();

      return $row->setting_value;
  }

  public function find_active_month()
  {
      $this->connection->where('setting_name', 'Active Month');
      $query = $this->connection->get('tb_settings');
      $row   = $query->row();

      return $row->setting_value;
  }

  public function find_budget($item, $group)
  {
    $this->connection->from('tb_products t1');
    $this->connection->join('tb_stocks_monthly_budgets t2', 't2.product_id = t1.id');
    $this->connection->join('tb_product_groups t3', 't3.id = t1.product_group_id');
    $this->connection->where('t1.product_name', $item);
    $this->connection->where('t3.group_code', $group);
    $this->connection->where('t2.year_number', $this->active_year);
    $this->connection->where('t2.month_number', $this->active_month);
    $query = $this->connection->get();

    return $query->unbuffered_row('array');
  }

  public function distinct($table, $select, array $criteria = null, $json = false){
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

  public function find($select, $id)
  {
    $this->db->select($select);
    $this->db->where('id', $id);
    $query = $this->db->get($this->module['table']);
    $row = $query->row_array();

    return $row[$select];
  }

  public function find_all($start = null, $end = null)
  {
    $this->db->select('t1.*, t2.group, t2.description, t2.part_number, t2.alternate_part_number, t2.item_serial, t2.quantity, t2.unit, t2.order_number, t2.additional_info');
    $this->db->from('tb_purchase_requests t1');
    $this->db->join('tb_purchase_request_items t2', 't2.document_number = t1.document_number');

    if ($start !== null and $end !== null){
      $this->db->where('t1.request_date >=', $start);
      $this->db->where('t1.request_date <', $end);
    } else {
      if ($start === null and $end === null){
        $month = date('m');
        $year  = date('Y');

        $this->db->where('EXTRACT(MONTH FROM t1.request_date) =', $month);
        $this->db->where('EXTRACT(YEAR FROM t1.request_date) =', $year);
      } else {
        if ($start !== null and $end === null){
          $this->db->where('t1.request_date >=', $start);
        } else {
          $dates = explode('-', $end);
          $month = $dates[1];
          $year  = $dates[0];

          $this->db->where('EXTRACT(MONTH FROM t1.request_date) =', $month);
          $this->db->where('EXTRACT(YEAR FROM t1.request_date) =', $year);
        }
      }
    }

    $query = $this->db->get();

    return $query->result_array();
  }

  public function findById($id)
  {
    $this->db->from(config_item('module')['purchase_request']['table']);
    $this->db->where('id', $id);
    $query = $this->db->get();
    $row = $query->unbuffered_row('array');

    $this->db->select('*');
    $this->db->from('tb_purchase_request_items');
    $this->db->where('document_number', $row['document_number']);
    $query = $this->db->get();
    $row['items'] = $query->result_array();

    foreach ($row['items'] as $key => $item){
      $this->connection->from('tb_products t1');
      $this->connection->join('tb_stocks_monthly_budgets t2', 't2.product_id = t1.id');
      $this->connection->join('tb_product_groups t3', 't3.id = t1.product_group_id');
      $this->connection->where('t1.product_name', $item['description']);
      $this->connection->where('t3.group_code', $item['group']);
      $this->connection->where('t2.year_number', $this->active_year);
      $this->connection->where('t2.month_number', $this->active_month);
      $query = $this->connection->get();
      $row['items'][$key]['budget'] = $query->row_array();
    }

    return $row;
  }

  public function find_item_by_part_number($part_number)
  {
    $this->db->where('part_number', $part_number);
    $query = $this->db->get('tb_master_items');

    $row = $query->row_array();

    return $row;
  }

  public function find_item_by_item_serial($item_serial)
  {
    $this->db->select('t1.*');
    $this->db->join('tb_master_item_serials t2', 't2.part_number = t1.part_number');
    $this->db->where('t2.item_serial', $_POST['item_serial']);
    $query = $this->db->get('tb_master_items t1');

    $row = $query->row_array();

    return $row;
  }

  public function find_item_models($part_number)
  {
    $this->db->where('part_number', $part_number);
    $query = $this->db->get('tb_item_models');
    $result = $query->result_array();
    $aircraft_types = array();

    foreach ($query->result_array() as $key => $row){
      $aircraft_types[$key] = $row['aircraft_type'];
    }

    return $aircraft_types;
  }

  public function findAllItemGroups()
  {
    $this->db->order_by('group', 'asc');
    $this->db->where('status', 'AVAILABLE');
    $query = $this->db->get('tb_master_item_groups');

    return $query->result_array();
  }

  public function findAllVendors()
  {
    $this->db->order_by('vendor', 'asc');
    $this->db->where('status', 'AVAILABLE');
    $query = $this->db->get(config_item('module')['vendor']['table']);

    return $query->result_array();
  }

  public function findAllAircraftTypes()
  {
    $this->db->order_by('aircraft_type', 'asc');
    $this->db->where('status', 'AVAILABLE');
    $query = $this->db->get(config_item('module')['aircraft_type']['table']);

    return $query->result_array();
  }

  public function isDocumentNumberExists($document_number)
  {
    $this->db->where('document_number', $document_number);
    $query = $this->db->get(config_item('module')['purchase_request']['table']);

    if ($query->num_rows() > 0)
      return true;

    return false;
  }

  public function isItemUnitExists($unit)
  {
    $query = $this->db->get_where(config_item('module')['item_unit']['table'], array(
      'unit' => strtoupper($unit),
   ));

    if ($query->num_rows() > 0)
      return true;

    return false;
  }

  public function isAircraftTypeExists($aircraft_type)
  {
    $query = $this->db->get_where(config_item('module')['aircraft_type']['table'], array(
      'aircraft_type' => strtoupper($aircraft_type),
   ));

    if ($query->num_rows() > 0)
      return true;

    return false;
  }

  public function isStoresExists($stores)
  {
    $query = $this->db->get_where(config_item('module')['stores']['table'], array(
      'stores' => strtoupper($stores),
      'warehouse' => config_item('main_warehouse'),
   ));

    if ($query->num_rows() > 0)
      return true;

    return false;
  }

  public function isVendorExists($vendor)
  {
    $query = $this->db->get_where(config_item('module')['vendor']['table'], array(
      'vendor' => strtoupper($vendor),
   ));

    if ($query->num_rows() > 0)
      return true;

    return false;
  }

  public function isItemGroupExists($group)
  {
    $query = $this->db->get_where('tb_master_item_groups', array(
      'group' => strtoupper($group),
   ));

    if ($query->num_rows() > 0)
      return true;

    return false;
  }

  public function isAircraftExists($aircraft)
  {
    $query = $this->db->get_where(config_item('module')['aircraft']['table'], array(
      'aircraft' => strtoupper($aircraft),
   ));

    if ($query->num_rows() > 0)
      return true;

    return false;
  }

  public function isItemExists($part_number)
  {
    $query = $this->db->get_where('tb_master_items', array(
      'part_number' => strtoupper($part_number),
   ));

    if ($query->num_rows() > 0)
      return true;

    return false;
  }

  public function isSerialNumberExists($item_serial)
  {
    $query = $this->db->get_where('tb_master_item_serials', array(
      'item_serial' => strtoupper($item_serial),
   ));

    if ($query->num_rows() > 0)
      return true;

    return false;
  }

  public function is_item_in_stores_exists($part_number, $stores)
  {
    $query = $this->db->get_where('tb_stocks', array(
      'part_number' => strtoupper($part_number),
      'stores' => strtoupper($stores),
      'warehouse' => config_item('main_warehouse'),
   ));

    if ($query->num_rows() > 0)
      return true;

    return false;
  }

  public function save()
  {
    //... by default document status is NEW
    $document_status = 'NEW';
    $document_number = NULL;

    //... but if posted id is not null && exists, document status is REVISED
    if (isset($_POST['id']) && $_POST['id'] !== ""){
      $document_status = 'REVISED';
      $document_number = $this->find('document_number', $_POST['id']);
    }

    $this->db->trans_begin();

    if ($document_status === 'NEW'){
      //... new document
      $this->db->set('document_number', strtoupper($_POST['document_number']));
      $this->db->set('request_date', strtoupper($_POST['request_date']));
      $this->db->set('request_by', $_POST['request_by']);

      if ($_POST['vendor'] != '')
        $this->db->set('vendor', strtoupper($_POST['vendor']));

      // $this->db->set('notes', $_POST['notes']);
      $this->db->set('created_by', config_item('auth_username'));
      $this->db->set('updated_by', config_item('auth_username'));
      $this->db->insert(config_item('module')['purchase_request']['table']);
    } else {
      //... revised document
      $this->db->set('document_status', 'REVISED');
      $this->db->set('request_date', strtoupper($_POST['request_date']));
      $this->db->set('request_by', $_POST['request_by']);
      $this->db->set('notes', $_POST['notes']);
      // $this->db->set('vendor', strtoupper($_POST['vendor']));
      // $this->db->set('notes', 'import _POST');
      $this->db->set('updated_at', date('Y-m-d H:i:s'));
      $this->db->set('updated_by', config_item('auth_username'));
      $this->db->where('document_number', strtoupper($_POST['document_number']));
      $this->db->insert(config_item('module')['purchase_request']['table']);
    }

    //... insert new document data
    foreach ($_POST['items'] as $key => $data){
      //... item in stores quantity details
      $this->db->set('group', strtoupper($data['group']));
      $this->db->set('description', strtoupper($data['description']));
      $this->db->set('part_number', strtoupper($data['part_number']));
      $this->db->set('alternate_part_number', strtoupper($data['alternate_part_number']));
      $this->db->set('item_serial', strtoupper($data['item_serial']));
      $this->db->set('quantity', $data['quantity']);
      $this->db->set('unit', strtoupper($data['unit']));
      $this->db->set('document_number', strtoupper($_POST['document_number']));
      $this->db->set('additional_info', $data['additional_info']);
      $this->db->set('updated_by', config_item('auth_username'));
      $this->db->insert('tb_purchase_request_items');
    }

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function import(array $user_data)
  {
    $this->db->trans_begin();

    foreach ($user_data as $key => $data){
      if (strtotime($data['request_date'])){
        $request_date = $data['request_date'];
      } else {
        $datetime = DateTime::createFromFormat('d/m/Y', $data['request_date']);
        $request_date = $datetime->format('Y-m-d');
      }

      if ($this->isDocumentNumberExists($data['document_number']) === FALSE){
        $this->db->set('document_number', strtoupper($data['document_number']));
        $this->db->set('request_date', strtoupper($request_date));
        $this->db->set('request_by', $data['request_by']);
        $this->db->set('vendor', strtoupper($data['vendor']));
        // $this->db->set('notes', 'import data');
        $this->db->set('created_by', config_item('auth_username'));
        $this->db->set('updated_by', config_item('auth_username'));
        $this->db->insert(config_item('module')['purchase_request']['table']);
      }

      if ($this->isItemUnitExists($data['unit']) === FALSE){
        $this->db->set('unit', strtoupper($data['unit']));
        $this->db->set('created_by', config_item('auth_username'));
        $this->db->set('updated_by', config_item('auth_username'));
        $this->db->insert(config_item('module')['item_unit']['table']);
      }

      if ($this->isStoresExists($data['stores']) === FALSE){
        $this->db->set('stores', strtoupper($data['stores']));
        $this->db->set('warehouse', config_item('main_warehouse'));
        $this->db->set('created_by', config_item('auth_username'));
        $this->db->set('updated_by', config_item('auth_username'));
        $this->db->insert('tb_master_items');
      }

      if ($this->isItemExists($data['part_number'],$data['description']) === FALSE){
        $this->db->set('part_number', trim(strtoupper($data['part_number'])));
        $this->db->set('alternate_part_number', trim(strtoupper($data['alternate_part_number'])));
        $this->db->set('description', trim(strtoupper($data['description'])));
        $this->db->set('group', strtoupper($data['group']));
        $this->db->set('unit', strtoupper($data['unit']));
        $this->db->set('created_by', config_item('auth_username'));
        $this->db->set('updated_by', config_item('auth_username'));
        $this->db->insert('tb_master_items');
      }

      if ($this->is_item_in_stores_exists($data['part_number'], $data['stores']) === FALSE){
        $this->db->set('part_number', strtoupper($data['part_number']));
        $this->db->set('stores', strtoupper($data['stores']));
        $this->db->set('warehouse', config_item('main_warehouse'));
        $this->db->insert('tb_stocks');
      }

      if ($data['item_serial'] != NULL){
        if ($this->isSerialNumberExists($data['stores']) === FALSE){
          $this->db->set('part_number', strtoupper($data['part_number']));
          $this->db->set('item_serial', strtoupper($data['item_serial']));
          $this->db->set('item_location', 'in stores');
          $this->db->set('condition', strtoupper($data['condition']));
          $this->db->set('warehouse', config_item('main_warehouse'));
          $this->db->set('stores', strtoupper($data['stores']));
          $this->db->set('vendor', strtoupper($data['vendor']));
          $this->db->set('notes', 'import grn#'. strtoupper($data['document_number']));
          $this->db->set('updated_by', config_item('auth_username'));
          $this->db->set('updated_by', config_item('auth_username'));
          $this->db->insert('tb_master_item_serials');
        } else {
          // $this->db->set('part_number', strtoupper($data['part_number']));
          $this->db->set('item_location', 'in stores');
          $this->db->set('condition', strtoupper($data['condition']));
          $this->db->set('warehouse', config_item('main_warehouse'));
          $this->db->set('stores', strtoupper($data['stores']));
          $this->db->set('vendor', strtoupper($data['vendor']));
          $this->db->set('notes', 'import grn#'. strtoupper($data['document_number']));
          // $this->db->set('created_by', config_item('auth_username'));
          $this->db->set('updated_by', config_item('auth_username'));
          $this->db->where('item_serial', strtoupper($data['item_serial']));
          $this->db->update('tb_master_item_serials');
        }

        //... insert into item serial number detail
        $this->db->set('item_serial', strtoupper($data['item_serial']));
        $this->db->set('document_type', 'GRN');
        $this->db->set('document_number', strtoupper($data['document_number']));
        $this->db->set('condition', strtoupper($data['condition']));
        $this->db->set('warehouse', config_item('main_warehouse'));
        $this->db->set('stores', strtoupper($data['stores']));
        $this->db->set('vendor', strtoupper($data['vendor']));
        $this->db->set('notes', 'import grn#'. strtoupper($data['document_number']));
        $this->db->set('updated_by', config_item('auth_username'));
        $this->db->insert('tb_item_item_serial_details');
      }

      //... item models
      $this->db->where('part_number', strtoupper($data['part_number']));
      $this->db->delete('tb_item_models');

      if ($data['aircraft_types'] != ''){
        foreach (explode(';', $data['aircraft_types']) as $aircraft_type){
          $this->db->set('part_number', strtoupper($data['part_number']));
          $this->db->set('aircraft_type', strtoupper($aircraft_type));
          $this->db->insert('tb_item_models');
        }
      }

      //... item in stores quantity details
      $this->db->set('part_number', strtoupper($data['part_number']));
      $this->db->set('item_serial', strtoupper($data['item_serial']));
      $this->db->set('condition', strtoupper($data['condition']));
      $this->db->set('warehouse', config_item('main_warehouse'));
      $this->db->set('stores', strtoupper($data['stores']));
      $this->db->set('quantity', $data['quantity']);
      $this->db->set('document_number', strtoupper($data['document_number']));
      $this->db->set('order_number', strtoupper($data['order_number']));
      $this->db->set('reference_number', strtoupper($data['reference_number']));
      $this->db->set('awb_number', strtoupper($data['awb_number']));
      $this->db->set('notes', $data['notes']);
      $this->db->set('date_of_entry', strtoupper($request_date));
      $this->db->set('updated_by', config_item('auth_username'));
      $this->db->insert('tb_stocks');

      //... item quantity details
      $this->db->set('part_number', strtoupper($data['part_number']));
      $this->db->set('item_serial', strtoupper($data['item_serial']));
      $this->db->set('item_location', 'in stores');
      $this->db->set('condition', strtoupper($data['condition']));
      $this->db->set('warehouse', config_item('main_warehouse'));
      $this->db->set('stores', strtoupper($data['stores']));
      $this->db->set('quantity', $data['quantity']);
      $this->db->set('document_type', 'GRN');
      $this->db->set('document_number', strtoupper($data['document_number']));
      $this->db->set('order_number', strtoupper($data['order_number']));
      $this->db->set('reference_number', strtoupper($data['reference_number']));
      $this->db->set('awb_number', strtoupper($data['awb_number']));
      $this->db->set('notes', $data['notes']);
      $this->db->set('updated_by', config_item('auth_username'));
      $this->db->set('document_date', strtoupper($request_date));
      $this->db->set('request_by', $data['request_by']);
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
