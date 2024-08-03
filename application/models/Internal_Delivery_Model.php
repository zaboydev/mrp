<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Internal_Delivery_Model extends MY_Model
{
  public function __construct()

  {
    parent::__construct();
  }

  public function getSelectedColumns()
  {
    $return = array(
      'tb_internal_delivery.id'                          => NULL,
      'tb_internal_delivery.document_number'             => 'Document Number',
      'tb_internal_delivery.received_date'                   => 'Date',
      'tb_internal_delivery.status'                      => 'Status',
      'tb_internal_delivery.category'                    => 'Category',
      'tb_internal_delivery.warehouse'                   => 'Base',
      // 'tb_internal_delivery.send_to_warehouse'           => 'Send to Base',
      'tb_internal_delivery_items.description'           => 'Description',
      'tb_internal_delivery_items.part_number'           => 'Part Number',
      'tb_internal_delivery_items.alternate_part_number' => 'Alt. Part Number',
      'tb_internal_delivery_items.serial_number'         => 'Serial Number',
      'tb_internal_delivery_items.condition'             => 'Condition',
      'tb_internal_delivery_items.quantity'              => 'Quantity',
      'tb_internal_delivery_items.unit'                  => 'Unit',
      'tb_internal_delivery_items.remarks'               => 'Remarks',
      'tb_internal_delivery.received_from'               => 'Received From',
      'tb_internal_delivery.received_by'                 => 'Received By',
      'tb_internal_delivery.sent_by'                     => 'Sent By',
    );

    if (config_item('auth_role') != 'PIC STOCK'){
      $return['tb_internal_delivery_items.unit_price']  = 'Value';
      $return['tb_internal_delivery_items.total_amount'] = 'Total Value';
    }

    return $return;
  }

  public function getSearchableColumns()
  {
    $return = array(
      'tb_internal_delivery.document_number',
      'tb_internal_delivery.status',
      'tb_internal_delivery.category',
      'tb_internal_delivery.warehouse',
      'tb_internal_delivery.category',
      'tb_internal_delivery.warehouse',
      'tb_internal_delivery_items.description',
      'tb_internal_delivery_items.part_number',
      'tb_internal_delivery_items.alternate_part_number',
      'tb_internal_delivery_items.serial_number',
      'tb_internal_delivery_items.condition',
      'tb_internal_delivery_items.unit',
      'tb_internal_delivery_items.remarks',
      'tb_internal_delivery.received_from',
      'tb_internal_delivery.received_by',
      'tb_internal_delivery.sent_by',
    );

    return $return;
  }

  public function getOrderableColumns()
  {
    $return = array(
      null,
      'tb_internal_delivery.document_number',
      'tb_internal_delivery.received_date',
      'tb_internal_delivery.status',
      'tb_internal_delivery.category',
      'tb_internal_delivery.warehouse',
      'tb_internal_delivery.category',
      'tb_internal_delivery.warehouse',
      'tb_internal_delivery_items.description',
      'tb_internal_delivery_items.part_number',
      'tb_internal_delivery_items.alternate_part_number',
      'tb_internal_delivery_items.serial_number',
      'tb_internal_delivery_items.condition',
      'tb_internal_delivery_items.unit',
      'tb_internal_delivery_items.remarks',
      'tb_internal_delivery.received_from',
      'tb_internal_delivery.received_by',
      'tb_internal_delivery.sent_by',
    );

    if (config_item('auth_role') != 'PIC STOCK'){
      $return[] = 'tb_internal_delivery_items.unit_price';
      $return[] = 'tb_internal_delivery_items.total_amount';
    }

    return $return;
  }

  private function searchIndex()
  {
    if (!empty($_POST['columns'][2]['search']['value'])){
      $search_received_date = $_POST['columns'][2]['search']['value'];
      $range_received_date  = explode(' ', $search_received_date);

      $this->db->where('tb_internal_delivery.received_date >= ', $range_received_date[0]);
      $this->db->where('tb_internal_delivery.received_date <= ', $range_received_date[1]);
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

  function getIndex($return = 'array')
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_internal_delivery');
    $this->db->join('tb_internal_delivery_items', 'tb_internal_delivery_items.internal_delivery_id = tb_internal_delivery.id');
    $this->db->where_in('tb_internal_delivery.category', config_item('auth_inventory'));
    $this->db->where_in('tb_internal_delivery.warehouse', config_item('auth_warehouses'));
    $this->db->like('tb_internal_delivery.document_number', 'DP');

    $this->searchIndex();

    $column_order = $this->getOrderableColumns();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('tb_internal_delivery.received_date', 'asc');
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

  function countIndexFiltered()
  {
    $this->db->from('tb_internal_delivery');
    $this->db->join('tb_internal_delivery_items', 'tb_internal_delivery_items.internal_delivery_id = tb_internal_delivery.id');
    $this->db->where_in('tb_internal_delivery.category', config_item('auth_inventory'));
    $this->db->where_in('tb_internal_delivery.warehouse', config_item('auth_warehouses'));
    $this->db->like('tb_internal_delivery.document_number', 'DP');


    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->from('tb_internal_delivery');
    $this->db->join('tb_internal_delivery_items', 'tb_internal_delivery_items.internal_delivery_id = tb_internal_delivery.id');
    $this->db->where_in('tb_internal_delivery.category', config_item('auth_inventory'));
    $this->db->where_in('tb_internal_delivery.warehouse', config_item('auth_warehouses'));
    $this->db->like('tb_internal_delivery.document_number', 'DP');

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function findById($id)
  {
    $this->db->where('id', $id);

    $query    = $this->db->get('tb_internal_delivery');
    $delivery = $query->unbuffered_row('array');

    $select = array(
      'tb_internal_delivery_items.*'
    );

    $this->db->select($select);
    $this->db->from('tb_internal_delivery_items');
    $this->db->where('tb_internal_delivery_items.internal_delivery_id', $id);

    $query = $this->db->get();

    foreach ($query->result_array() as $key => $value){
      $delivery['items'][$key] = $value;

      if (empty($delivery['category'])){
        $this->db->select('category');
        $this->db->from('tb_master_item_groups');
        $this->db->where('group', $value['group']);

        $query = $this->db->get();
        $icat  = $query->unbuffered_row();

        $delivery['category'] = $icat->category;
      }
    }

    return $delivery;
  }

  public function isDocumentNumberExists($document_number)
  {
    $this->db->where('document_number', $document_number);
    $query = $this->db->get('tb_internal_delivery');

    if ($query->num_rows() > 0)
      return true;

    return false;
  }

  public function isValidDocumentQuantity($document_number)
  {
    $this->db->select_sum('tb_receipt_items.received_quantity', 'received_quantity');
    $this->db->select_sum('tb_stock_in_stores.quantity', 'stored_quantity');
    $this->db->select('tb_receipt_items.document_number');
    $this->db->from('tb_receipt_items');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_receipt_items.stock_in_stores_id');
    $this->db->where('tb_receipt_items.document_number', $document_number);
    $this->db->group_by('tb_receipt_items.document_number');

    $query  = $this->db->get();
    $row    = $query->unbuffered_row('array');

    if ($row['received_quantity'] === $row['received_quantity'])
      return true;

    return false;
  }

  public function save()
  {
    $document_id      = (isset($_SESSION['delivery']['id'])) ? $_SESSION['delivery']['id'] : NULL;
    $document_edit    = (isset($_SESSION['delivery']['edit'])) ? $_SESSION['delivery']['edit'] : NULL;
    $document_number  = sprintf('%06s', $_SESSION['delivery']['document_number']) . delivery_format_number();
    $send_date        = $_SESSION['delivery']['send_date'];
    $received_by      = (empty($_SESSION['delivery']['received_by'])) ? NULL : $_SESSION['delivery']['received_by'];
    $received_from    = (empty($_SESSION['delivery']['received_from'])) ? NULL : $_SESSION['delivery']['received_from'];
    $sent_by          = (empty($_SESSION['delivery']['sent_by'])) ? NULL : $_SESSION['delivery']['sent_by'];
    $approved_by      = (empty($_SESSION['delivery']['approved_by'])) ? NULL : $_SESSION['delivery']['approved_by'];
    $warehouse        = $_SESSION['delivery']['warehouse'];
    $send_to_warehouse        = $_SESSION['delivery']['send_to_warehouse'];
    $category         = $_SESSION['delivery']['category'];
    $notes            = (empty($_SESSION['delivery']['notes'])) ? NULL : $_SESSION['delivery']['notes'];

    $this->db->trans_begin();

    if ($document_id === NULL){
      $this->db->set('document_number', $document_number);
      $this->db->set('received_from', $received_from);
      $this->db->set('send_date', $send_date);
      $this->db->set('received_date', $send_date);
      // $this->db->set('received_by', $received_by);
      $this->db->set('sent_by', $sent_by);
      // $this->db->set('approved_by', $approved_by);
      $this->db->set('category', $category);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('send_to_warehouse', $send_to_warehouse);
      $this->db->set('notes', $notes);
      $this->db->set('status', 'APPROVED');
      $this->db->set('type', ($warehouse==$send_to_warehouse)? '1':'2');
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->insert('tb_internal_delivery');
      $document_id = $this->db->insert_id();
    } else {

      $this->db->where('internal_delivery_id', $document_id);
      $this->db->delete('tb_internal_delivery_items');

      /**
       * CREATE DELIVERY DOCUMENT
       */
      $this->db->set('document_number', $document_number);
      $this->db->set('send_date', $send_date);
      $this->db->set('received_from', $received_from);
      // $this->db->set('received_by', $received_by);
      $this->db->set('sent_by', $sent_by);
      // $this->db->set('approved_by', $approved_by);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('category', $category);
      $this->db->set('notes', $notes);
      $this->db->set('status', 'APPROVED');
      $this->db->set('updated_at', date('Y-m-d'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->set('send_to_warehouse', $send_to_warehouse);
      $this->db->set('type', ($warehouse==$send_to_warehouse)? '1':'2');
      $this->db->where('id', $document_id);
      $this->db->update('tb_internal_delivery');
    }

    /**
     * PROCESSING DELIVERY ITEMS
     */
    foreach ($_SESSION['delivery']['items'] as $key => $data){
      $serial_number = (empty($data['serial_number'])) ? NULL : $data['serial_number'];
      $aircraft_mapping_id = (empty($data['aircraft_mapping_id'])) ? NULL : $data['aircraft_mapping_id'];
      /**
       * CREATE UNIT OF MEASUREMENT IF NOT EXISTS
       */
      if (isItemUnitExists($data['unit']) === FALSE){
        $this->db->set('unit', strtoupper($data['unit']));
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->insert('tb_master_item_units');
      }

      /**
       * CREATE STORES IF NOT EXISTS
       */
      if (isStoresExists($data['stores']) === FALSE && isStoresExists($data['stores'], $category) === FALSE){
        $this->db->set('stores', strtoupper($data['stores']));
        $this->db->set('warehouse', $warehouse);
        $this->db->set('category', $category);
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->insert('tb_master_stores');
      }

      /**
       * CREATE ITEM IF NOT EXISTS
       */
      if (isItemExists($data['part_number'],$data['description'],$serial_number) === FALSE){
        $this->db->set('part_number', strtoupper($data['part_number']));
        $this->db->set('serial_number', strtoupper($data['serial_number']));
        $this->db->set('alternate_part_number', strtoupper($data['alternate_part_number']));
        $this->db->set('description', strtoupper($data['description']));
        $this->db->set('group', strtoupper($data['group']));
        $this->db->set('minimum_quantity', floatval($data['minimum_quantity']));
        $this->db->set('unit', strtoupper($data['unit']));
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->insert('tb_master_items');

        $item_id = $this->db->insert_id();
      } else {
        $item_id = getItemId($data['part_number'],$data['description'],$serial_number);
      }

      /**
       * CREATE part number IF NOT EXISTS in tb master part number
       */

      if (isPartNumberExists($data['part_number']) === FALSE){
        $this->db->set('part_number', strtoupper($data['part_number']));
        $this->db->set('min_qty', $data['minimum_quantity']);        
        $this->db->set('item_id', $item_id);        
        $this->db->set('qty', $data['received_quantity']);
        $this->db->insert('tb_master_part_number');
      }
      else{
        $qty_awal = getPartnumberQty($data['part_number']);

        $qty_baru = floatval($data['received_quantity']) + floatval($qty_awal);

        $this->db->set('qty', $qty_baru);
        $this->db->where('part_number', strtoupper($data['part_number']));
        $this->db->update('tb_master_part_number');
      }

      /**
       * CREATE SERIAL NUMBER IF NOT EXISTS
       */
      if (!empty($data['serial_number'])){
        if (isSerialExists($item_id, $data['serial_number']) === FALSE){
          $this->db->set('item_id', $item_id);
          $this->db->set('serial_number', strtoupper($data['serial_number']));
          $this->db->set('warehouse', $warehouse);
          $this->db->set('stores', strtoupper($data['stores']));
          $this->db->set('condition', strtoupper($data['condition']));
          $this->db->set('updated_by', config_item('auth_person_name'));
          $this->db->insert('tb_master_item_serials');
      
          $serial_id  = $this->db->insert_id();
        } else {
          $serial     = getSerial($item_id, $data['serial_number']);
          $serial_id  = $serial->id;
      
          $this->db->set('quantity', 1);
          $this->db->set('warehouse', $warehouse);
          $this->db->set('stores', strtoupper($data['stores']));
          $this->db->set('condition', strtoupper($data['condition']));
          $this->db->set('updated_by', config_item('auth_person_name'));
          $this->db->where('id', $serial_id);
          $this->db->update('tb_master_item_serials');
        }
      } else {
        $serial_id = NULL;
      }

      /**
       * ADD ITEM INTO STOCK
       */

      /**
       * INSERT INTO DELIVERY ITEMS
       */
      $this->db->set('internal_delivery_id', $document_id);
      $this->db->set('part_number', strtoupper($data['part_number']));
      $this->db->set('serial_number', strtoupper($data['serial_number']));
      $this->db->set('alternate_part_number', strtoupper($data['alternate_part_number']));
      $this->db->set('description', strtoupper($data['description']));
      $this->db->set('group', strtoupper($data['group']));
      $this->db->set('minimum_quantity', floatval($data['minimum_quantity']));
      $this->db->set('unit', strtoupper($data['unit']));
      $this->db->set('stores', strtoupper($data['stores']));
      $this->db->set('condition', strtoupper($data['condition']));
      $this->db->set('quantity', floatval($data['quantity']));
      $this->db->set('left_received_quantity', floatval($data['quantity']));
      $this->db->set('unit_price', floatval($data['unit_price']));
      $this->db->set('total_amount', floatval($data['unit_price']) * floatval($data['quantity']));
      $this->db->set('remarks', $data['remarks']);
      $this->db->set('aircraft_mapping_id', $aircraft_mapping_id);
      $this->db->insert('tb_internal_delivery_items');

      /**
       * UPDATE AIRCRAFT MAPPING PART
       */
      if($aircraft_mapping_id!=NULL){
        $this->db->set('status', strtoupper('CLOSED'));
        $this->db->set('vendor', strtoupper('STORES'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->where('id', $data['aircraft_mapping_id']);
        $this->db->update('tb_aircraft_mapping_parts');
      }      
    }

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function delete()
  {
    $this->db->trans_begin();

    $id = $this->input->post('id');

    $this->db->where('internal_delivery_id', $id);
      $this->db->delete('tb_internal_delivery_items');

    $this->db->where('id', $id);
    $this->db->delete('tb_internal_delivery');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function searchItemsBySerial($category)
  {
    $this->column_select = array(
      'tb_master_items.serial_number',
      'tb_master_items.id',
      'tb_master_items.group',
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.alternate_part_number',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit'
     );

    $this->db->select($this->column_select);
    $this->db->from('tb_master_items');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->join('tb_stocks', 'tb_stocks.item_id = tb_master_items.id');
    // $this->db->where('tb_stocks.condition', 'SERVICEABLE');
    $this->db->where('tb_master_items.serial_number IS NOT NULL', NULL, FALSE);
    $this->db->where('tb_stocks.total_quantity', 0);
    $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
    $this->db->where('tb_master_item_groups.category', $category);

    $this->db->order_by('tb_master_items.serial_number ASC');

    $query  = $this->db->get();
    $result = $query->result_array();

    return $result;
  }

  public function searchItemsByPartNumber($category)
  {
    $this->column_select = array(
      'tb_master_items.id',
      'tb_master_items.group',
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.alternate_part_number',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit',
      'tb_master_items.serial_number',
     );

    $this->db->select($this->column_select);
    $this->db->from('tb_master_items');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
    $this->db->where('tb_master_item_groups.category', $category);

    $this->db->order_by('tb_master_items.group ASC, tb_master_items.description ASC');

    $query  = $this->db->get();
    $result = $query->result_array();

    return $result;
  }

  public function getSelectedColumnsReceipt()
  {
    $return = array(
      'tb_internal_delivery.id'                          => NULL,
      'tb_internal_delivery.document_number'             => 'Document Number',
      'tb_internal_delivery.send_date'                   => 'Send Date',
      'tb_internal_delivery.status'                      => 'Status',
      'tb_internal_delivery.category'                    => 'Category',
      'tb_internal_delivery.warehouse'                   => 'Base',
      'tb_internal_delivery_items.description'           => 'Description',
      'tb_internal_delivery_items.part_number'           => 'Part Number',
      'tb_internal_delivery_items.alternate_part_number' => 'Alt. Part Number',
      'tb_internal_delivery_items.serial_number'         => 'Serial Number',
      'tb_internal_delivery_items.condition'             => 'Condition',
      'tb_internal_delivery_items.quantity'              => 'Quantity',
      'tb_internal_delivery_items.unit'                  => 'Unit',
      'tb_internal_delivery_items.remarks'               => 'Remarks',
      'tb_internal_delivery.received_from'               => 'Received From',
      'tb_internal_delivery.received_by'                 => 'Received By',
      'tb_internal_delivery.sent_by'                     => 'Sent By',
    );

    if (config_item('auth_role') != 'PIC STOCK'){
      $return['tb_internal_delivery_items.unit_price']  = 'Value';
      $return['tb_internal_delivery_items.total_amount'] = 'Total Value';
    }

    return $return;
  }

  public function getSearchableColumnsReceipt()
  {
    $return = array(
      'tb_internal_delivery.document_number',
      'tb_internal_delivery.status',
      'tb_internal_delivery.category',
      'tb_internal_delivery.warehouse',
      'tb_internal_delivery.category',
      'tb_internal_delivery.warehouse',
      'tb_internal_delivery_items.description',
      'tb_internal_delivery_items.part_number',
      'tb_internal_delivery_items.alternate_part_number',
      'tb_internal_delivery_items.serial_number',
      'tb_internal_delivery_items.condition',
      'tb_internal_delivery_items.unit',
      'tb_internal_delivery_items.remarks',
      'tb_internal_delivery.received_from',
      'tb_internal_delivery.received_by',
      'tb_internal_delivery.sent_by',
    );

    return $return;
  }

  public function getOrderableColumnsReceipt()
  {
    $return = array(
      null,
      'tb_internal_delivery.document_number',
      'tb_internal_delivery.send_date',
      'tb_internal_delivery.status',
      'tb_internal_delivery.category',
      'tb_internal_delivery.warehouse',
      'tb_internal_delivery.category',
      'tb_internal_delivery.warehouse',
      'tb_internal_delivery_items.description',
      'tb_internal_delivery_items.part_number',
      'tb_internal_delivery_items.alternate_part_number',
      'tb_internal_delivery_items.serial_number',
      'tb_internal_delivery_items.condition',
      'tb_internal_delivery_items.unit',
      'tb_internal_delivery_items.remarks',
      'tb_internal_delivery.received_from',
      'tb_internal_delivery.received_by',
      'tb_internal_delivery.sent_by',
    );

    if (config_item('auth_role') != 'PIC STOCK'){
      $return[] = 'tb_internal_delivery_items.unit_price';
      $return[] = 'tb_internal_delivery_items.total_amount';
    }

    return $return;
  }

  private function searchIndexReceipt()
  {
    if (!empty($_POST['columns'][2]['search']['value'])){
      $search_received_date = $_POST['columns'][2]['search']['value'];
      $range_received_date  = explode(' ', $search_received_date);

      $this->db->where('tb_internal_delivery.received_date >= ', $range_received_date[0]);
      $this->db->where('tb_internal_delivery.received_date <= ', $range_received_date[1]);
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

  function getIndexReceipt($return = 'array')
  {
    $this->db->select(array_keys($this->getSelectedColumnsReceipt()));
    $this->db->from('tb_internal_delivery');
    $this->db->join('tb_internal_delivery_items', 'tb_internal_delivery_items.internal_delivery_id = tb_internal_delivery.id');
    $this->db->where_in('tb_internal_delivery.category', config_item('auth_inventory'));
    $this->db->where_in('tb_internal_delivery.warehouse', config_item('auth_warehouses'));
    $this->db->like('tb_internal_delivery.document_number', 'DP');
    $this->db->where('tb_internal_delivery.type','2');

    $this->searchIndexReceipt();

    $column_order = $this->getOrderableColumns();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('tb_internal_delivery.received_date', 'asc');
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

  function countIndexFilteredReceipt()
  {
    $this->db->from('tb_internal_delivery');
    $this->db->join('tb_internal_delivery_items', 'tb_internal_delivery_items.internal_delivery_id = tb_internal_delivery.id');
    $this->db->where_in('tb_internal_delivery.category', config_item('auth_inventory'));
    $this->db->where_in('tb_internal_delivery.warehouse', config_item('auth_warehouses'));
    $this->db->like('tb_internal_delivery.document_number', 'DP');
    $this->db->where('tb_internal_delivery.type','2');

    $this->searchIndexReceipt();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndexReceipt()
  {
    $this->db->from('tb_internal_delivery');
    $this->db->join('tb_internal_delivery_items', 'tb_internal_delivery_items.internal_delivery_id = tb_internal_delivery.id');
    $this->db->where_in('tb_internal_delivery.category', config_item('auth_inventory'));
    $this->db->where_in('tb_internal_delivery.warehouse', config_item('auth_warehouses'));
    $this->db->like('tb_internal_delivery.document_number', 'DP');
    $this->db->where('tb_internal_delivery.type','2');

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function receipt()
  {
    $this->db->trans_begin();

    $id = $this->input->post('id');

    $this->db->set('received_by', config_item('auth_username'));
    $this->db->set('received_date', date('Y-m-d'));
    $this->db->set('status','RECEIVED');
    $this->db->where('id', $id);
    $this->db->update('tb_internal_delivery');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function searchMappingPart($category=NULL)
  {
    $this->column_select = array(
      'tb_aircraft_mapping_parts.*',
      'tb_master_items.unit',
      'tb_master_items.group'
    );

    $this->db->select($this->column_select);
    $this->db->from('tb_aircraft_mapping_parts');
    $this->db->join('tb_aircraft_components','tb_aircraft_components.id = tb_aircraft_mapping_parts.component_remove_id');
    $this->db->join('tb_master_items','tb_master_items.id = tb_aircraft_components.item_id');
    $this->db->where_in('tb_aircraft_mapping_parts.status', ['OPEN']);

    if ($_SESSION['delivery']['received_from'] !== NULL || !empty($_SESSION['delivery']['received_from'])) {
      $this->db->where('tb_aircraft_mapping_parts.remove_aircraft_register', $_SESSION['delivery']['received_from']);
    }

    $this->db->order_by('tb_aircraft_mapping_parts.part_number ASC, tb_aircraft_mapping_parts.description ASC');
    $query  = $this->db->get();
    $result = $query->result_array();

    return $result;    
  }
}
