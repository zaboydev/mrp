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
      'tb_receipts.id'                          => NULL,
      'tb_receipts.document_number'             => 'Document Number',
      'tb_receipts.received_date'               => 'Received Date',
      'tb_receipts.category'                    => 'Category',
      'tb_receipts.warehouse'                   => 'Base',
      'tb_master_items.description'             => 'Description',
      'tb_master_items.part_number'             => 'Part Number',
      'tb_master_items.alternate_part_number'   => 'Alt. Part Number',
      'tb_master_items.serial_number'           => 'Serial Number',
      'tb_stocks.condition'                     => 'Condition',
      'tb_receipt_items.received_quantity'      => 'Quantity',
      'tb_master_items.unit'                    => 'Unit',
      'tb_receipt_items.remarks'                => 'Remarks',
      'tb_receipts.received_from'               => 'Received From',
      'tb_receipts.received_by'                 => 'Received By',
      'tb_receipts.sent_by'                     => 'Sent By',
    );

    if (config_item('auth_role') != 'PIC STOCK'){
      $return['tb_receipt_items.received_unit_value']  = 'Value';
      $return['tb_receipt_items.received_total_value'] = 'Total Value';
    }

    return $return;
  }

  public function getSearchableColumns()
  {
    $return = array(
      'tb_receipts.document_number',
      'tb_receipts.category',
      'tb_receipts.warehouse',
      'tb_receipts.category',
      'tb_receipts.warehouse',
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.alternate_part_number',
      'tb_master_items.serial_number',
      'tb_stocks.condition',
      'tb_master_items.unit',
      'tb_receipt_items.remarks',
      'tb_receipts.received_from',
      'tb_receipts.received_by',
      'tb_receipts.sent_by',
    );

    return $return;
  }

  public function getOrderableColumns()
  {
    $return = array(
      null,
      'tb_receipts.document_number',
      'tb_receipts.received_date',
      'tb_receipts.category',
      'tb_receipts.warehouse',
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.alternate_part_number',
      'tb_master_items.serial_number',
      'tb_stocks.condition',
      'tb_receipt_items.received_quantity',
      'tb_master_items.unit',
      'tb_receipt_items.remarks',
      'tb_receipts.received_from',
      'tb_receipts.received_by',
      'tb_receipts.sent_by',
    );

    if (config_item('auth_role') != 'PIC STOCK'){
      $return[] = 'tb_receipt_items.received_unit_value';
      $return[] = 'tb_receipt_items.received_total_value';
    }

    return $return;
  }

  private function searchIndex()
  {
    if (!empty($_POST['columns'][2]['search']['value'])){
      $search_received_date = $_POST['columns'][2]['search']['value'];
      $range_received_date  = explode(' ', $search_received_date);

      $this->db->where('tb_receipts.received_date >= ', $range_received_date[0]);
      $this->db->where('tb_receipts.received_date <= ', $range_received_date[1]);
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
    $this->db->from('tb_receipts');
    $this->db->join('tb_receipt_items', 'tb_receipt_items.document_number = tb_receipts.document_number');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_receipt_items.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->where_in('tb_receipts.category', config_item('auth_inventory'));
    $this->db->where_in('tb_receipts.warehouse', config_item('auth_warehouses'));
    $this->db->like('tb_receipts.document_number', 'DP');

    $this->searchIndex();

    $column_order = $this->getOrderableColumns();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('tb_receipts.received_date', 'asc');
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
    $this->db->from('tb_receipts');
    $this->db->join('tb_receipt_items', 'tb_receipt_items.document_number = tb_receipts.document_number');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_receipt_items.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->where_in('tb_receipts.category', config_item('auth_inventory'));
    $this->db->where_in('tb_receipts.warehouse', config_item('auth_warehouses'));
    $this->db->like('tb_receipts.document_number', 'DP');

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->from('tb_receipts');
    $this->db->join('tb_receipt_items', 'tb_receipt_items.document_number = tb_receipts.document_number');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_receipt_items.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->where_in('tb_receipts.category', config_item('auth_inventory'));
    $this->db->where_in('tb_receipts.warehouse', config_item('auth_warehouses'));
    $this->db->like('tb_receipts.document_number', 'DP');

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function findById($id)
  {
    $this->db->where('id', $id);

    $query    = $this->db->get('tb_receipts');
    $delivery = $query->unbuffered_row('array');

    $select = array(
      'tb_stock_in_stores.*',
      'tb_stocks.condition',
      'tb_stock_in_stores.stores',
      'tb_master_items.serial_number',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.alternate_part_number',
      'tb_master_items.unit',
      'tb_master_items.group',
      'tb_receipt_items.received_quantity',
      'tb_receipt_items.remarks',
    );

    $this->db->select($select);
    $this->db->from('tb_receipt_items');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_receipt_items.stock_in_stores_id');
    // $this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_in_stores.serial_id', 'left');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->where('tb_receipt_items.document_number', $delivery['document_number']);

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
    $query = $this->db->get('tb_receipts');

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
    $received_date    = $_SESSION['delivery']['received_date'];
    $received_by      = (empty($_SESSION['delivery']['received_by'])) ? NULL : $_SESSION['delivery']['received_by'];
    $received_from    = (empty($_SESSION['delivery']['received_from'])) ? NULL : $_SESSION['delivery']['received_from'];
    $sent_by          = (empty($_SESSION['delivery']['sent_by'])) ? NULL : $_SESSION['delivery']['sent_by'];
    $approved_by      = (empty($_SESSION['delivery']['approved_by'])) ? NULL : $_SESSION['delivery']['approved_by'];
    $warehouse        = $_SESSION['delivery']['warehouse'];
    $category         = $_SESSION['delivery']['category'];
    $notes            = (empty($_SESSION['delivery']['notes'])) ? NULL : $_SESSION['delivery']['notes'];

    $this->db->trans_begin();

    if ($document_id === NULL){
      $this->db->set('document_number', $document_number);
      $this->db->set('received_from', $received_from);
      $this->db->set('received_date', $received_date);
      $this->db->set('received_by', $received_by);
      $this->db->set('sent_by', $sent_by);
      $this->db->set('approved_by', $approved_by);
      $this->db->set('category', $category);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('notes', $notes);
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->insert('tb_receipts');
    } else {
      /**
       * EDIT DOCUMENT
       * decrease quantity
       * create document revision
       */
      $this->db->select('document_number, warehouse,received_date');
      $this->db->where('id', $document_id);
      $this->db->from('tb_receipts');

      $query = $this->db->get();
      $row   = $query->unbuffered_row('array');

      $document_number  = $row['document_number'];
      $warehouse        = $row['warehouse'];
      // $received_date        = $row['received_date'];

      $old_document_number  = $row['document_number'];
      $old_warehouse        = $row['warehouse'];

      $this->db->select('tb_receipt_items.quantity_order, tb_receipt_items.id, tb_receipt_items.stock_in_stores_id, tb_receipt_items.received_quantity, tb_receipt_items.received_unit_value, tb_stock_in_stores.stock_id, tb_stock_in_stores.serial_id, tb_stock_in_stores.stores,tb_receipt_items.purchase_order_item_id');
      $this->db->from('tb_receipt_items');
      $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_receipt_items.stock_in_stores_id');
      $this->db->where('tb_receipt_items.document_number', $old_document_number);

      $query  = $this->db->get();
      $result = $query->result_array();

      foreach ($result as $data) {
        // $prev_old_stock = getStockActive($data['stock_id']);
        // $next_old_stock = floatval($prev_old_stock->total_quantity) - floatval($data['received_quantity']);

        $prev_old_stock = getStockPrev($data['stock_id'], $data['stores']);
        $next_old_stock = floatval($prev_old_stock) - floatval($data['received_quantity']);

        $this->db->set('stock_id', $data['stock_id']);
        $this->db->set('serial_id', $data['serial_id']);
        $this->db->set('warehouse', $old_warehouse);
        $this->db->set('stores', $data['stores']);
        $this->db->set('date_of_entry', $row['received_date']);
        $this->db->set('period_year', config_item('period_year'));
        $this->db->set('period_month', config_item('period_month'));
        $this->db->set('document_type', 'REVISION');
        $this->db->set('document_number', $old_document_number);
        $this->db->set('issued_to', $old_document_number);
        $this->db->set('issued_by', config_item('auth_person_name'));
        $this->db->set('remarks', 'REVISION');
        $this->db->set('prev_quantity', floatval($prev_old_stock));
        $this->db->set('balance_quantity', $next_old_stock);
        $this->db->set('quantity', 0 - floatval($data['received_quantity']));
        $this->db->set('unit_value', floatval($data['received_unit_value']));
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('stock_in_stores_id', $data['stock_in_stores_id']);
        $this->db->set('doc_type', 8);
        $this->db->set('tgl', date('Ymd', strtotime($row['received_date'])));
        $this->db->set('total_value', floatval($data['received_unit_value'] * (0 - floatval($data['received_quantity']))));
        $this->db->insert('tb_stock_cards');

        $this->db->where('id', $data['id']);
        $this->db->delete('tb_receipt_items');

        $this->db->where('id', $data['stock_in_stores_id']);
        $this->db->delete('tb_stock_in_stores');
      }

      /**
       * CREATE DELIVERY DOCUMENT
       */
      $this->db->set('document_number', $document_number);
      $this->db->set('received_date', $received_date);
      $this->db->set('received_from', $received_from);
      $this->db->set('received_by', $received_by);
      $this->db->set('sent_by', $sent_by);
      $this->db->set('approved_by', $approved_by);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('category', $category);
      $this->db->set('notes', $notes);
      $this->db->set('updated_at', date('Y-m-d'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->where('id', $document_id);
      $this->db->update('tb_receipts');
    }

    /**
     * PROCESSING DELIVERY ITEMS
     */
    foreach ($_SESSION['delivery']['items'] as $key => $data){
      $serial_number = (empty($data['serial_number'])) ? NULL : $data['serial_number'];
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
      if (isStockExists($item_id, strtoupper($data['condition']))){
        $stock_id = getStockId($item_id, strtoupper($data['condition']));
      } else {
        $this->db->set('item_id', $item_id);
        $this->db->set('condition', strtoupper($data['condition']));
        $this->db->set('initial_total_quantity', floatval($data['received_quantity']));
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->insert('tb_stocks');

        $stock_id = $this->db->insert_id();
      }

      // ADD to STORES
      if($warehouse=='WISNU'){
        $warehouse_id=1;
      }
      if($warehouse=='BANYUWANGI'){
        $warehouse_id=2;
      }
      if($warehouse=='SOLO'){
        $warehouse_id=3;
      }
      if($warehouse=='LOMBOK'){
        $warehouse_id=4;
      }
      if($warehouse=='JEMBER'){
        $warehouse_id=5;
      }
      if($warehouse=='PALANGKARAYA'){
        $warehouse_id=6;
      }
      if($warehouse=='WISNU REKONDISI'){
        $warehouse_id=7;
      }
      if($warehouse=='BSR REKONDISI'){
        $warehouse_id=8;
      }

      $this->db->set('stock_id', $stock_id);
      $this->db->set('serial_id', $serial_id);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('stores', strtoupper($data['stores']));
      $this->db->set('initial_quantity', floatval($data['received_quantity']));
      $this->db->set('initial_unit_value', floatval($data['received_unit_value']));
      $this->db->set('quantity', floatval($data['received_quantity']));
      $this->db->set('unit_value', floatval($data['received_unit_value']));
      $this->db->set('reference_document', $document_number);
      $this->db->set('received_date', $received_date);
      $this->db->set('received_by', $received_by);
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->set('remarks', $data['remarks']);      
      $this->db->set('warehouse_id', $warehouse_id);
      $this->db->insert('tb_stock_in_stores');

      $stock_in_stores_id = $this->db->insert_id();

      /**
       * INSERT INTO DELIVERY ITEMS
       */
      $this->db->set('document_number', $document_number);
      $this->db->set('stock_in_stores_id', $stock_in_stores_id);
      $this->db->set('received_quantity', floatval($data['received_quantity']));
      $this->db->set('received_unit_value', floatval($data['received_unit_value']));
      $this->db->set('received_total_value', floatval($data['received_unit_value']) * floatval($data['received_quantity']));
      $this->db->set('remarks', $data['remarks']);
      $this->db->insert('tb_receipt_items');

      /**
       * CREATE STOCK CARD
       */

      $prev_stock   = getStockPrev($stock_id, strtoupper($data['stores']));
      $next_stock   = floatval($prev_stock) + floatval($data['received_quantity']);

      $this->db->set('serial_id', $serial_id);
      $this->db->set('stock_id', $stock_id);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('stores', strtoupper($data['stores']));
      $this->db->set('date_of_entry', $received_date);
      $this->db->set('period_year', config_item('period_year'));
      $this->db->set('period_month', config_item('period_month'));
      $this->db->set('document_type', 'DELIVERY');
      $this->db->set('document_number', $document_number);
      $this->db->set('received_from', $received_from);
      $this->db->set('received_by', $received_by);
      $this->db->set('quantity', floatval($data['received_quantity']));
      $this->db->set('prev_quantity', floatval($prev_stock));
      $this->db->set('balance_quantity', $next_stock);
      $this->db->set('unit_value', floatval($data['received_unit_value']));
      $this->db->set('remarks', $data['remarks']);
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->set('doc_type', 8);
      $this->db->set('tgl', date('Ymd', strtotime($row['received_date'])));
      $this->db->set('total_value', floatval($data['received_unit_value'] * (0 + floatval($data['received_quantity']))));
      $this->db->insert('tb_stock_cards');
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

    $this->db->select('document_number, warehouse, received_date');
    $this->db->where('id', $id);
    $this->db->from('tb_receipts');

    $query = $this->db->get();
    $row   = $query->unbuffered_row('array');

    $document_number  = $row['document_number'];
    $warehouse        = $row['warehouse'];

    $this->db->select('tb_receipt_items.id, tb_receipt_items.stock_in_stores_id, tb_receipt_items.received_quantity, tb_receipt_items.received_unit_value, tb_stock_in_stores.stock_id, tb_stock_in_stores.serial_id, tb_stock_in_stores.stores');
    $this->db->from('tb_receipt_items');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_receipt_items.stock_in_stores_id');
    $this->db->where('tb_receipt_items.document_number', $document_number);

    $query  = $this->db->get();
    $result = $query->result_array();

    foreach ($result as $data) {
      $prev_old_stock = getStockPrev($data['stock_id'], $data['stores']);
      $next_old_stock = floatval($prev_old_stock) - floatval($data['received_quantity']);

      $this->db->set('stock_id', $data['stock_id']);
      $this->db->set('serial_id', $data['serial_id']);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('stores', $data['stores']);
      $this->db->set('date_of_entry', $row['received_date']);
      $this->db->set('period_year', config_item('period_year'));
      $this->db->set('period_month', config_item('period_month'));
      $this->db->set('document_type', 'REMOVAL');
      $this->db->set('document_number', $document_number);
      $this->db->set('issued_to', 'DELETE DOCUMENT');
      $this->db->set('prev_quantity', floatval($prev_old_stock));
      $this->db->set('balance_quantity', floatval($next_old_stock));
      $this->db->set('issued_by', config_item('auth_person_name'));
      $this->db->set('quantity', 0 - floatval($data['received_quantity']));
      $this->db->set('unit_value', floatval($data['received_unit_value']));
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->set('doc_type', 8);
      $this->db->set('tgl', date('Ymd', strtotime($row['received_date'])));
      $this->db->set('total_value', floatval($data['received_unit_value'] * (0 - floatval($data['received_quantity']))));
      $this->db->insert('tb_stock_cards');

      $this->db->where('id', $data['id']);
      $this->db->delete('tb_receipt_items');

      $this->db->where('id', $data['stock_in_stores_id']);
      $this->db->delete('tb_stock_in_stores');
    }

    $this->db->where('id', $id);
    $this->db->delete('tb_receipts');

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
}
