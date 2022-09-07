<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Commercial_Invoice_Model extends MY_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function getSelectedColumns()
  {
    $selected = array(
      'tb_returns.id'                       => NULL,
      'tb_returns.document_number'          => 'Document Number',
      'tb_returns.issued_date'              => 'Date',
      'tb_returns.category'                 => 'Category',
      'tb_returns.warehouse'                => 'Base',
      'tb_return_items.description'         => 'Description',
      'tb_return_items.part_number'         => 'Part Number',
      'tb_return_items.serial_number'       => 'Serial Number',
      'tb_return_items.condition'           => 'Condition',
      'tb_return_items.issued_quantity'     => 'Quantity',
      'tb_return_items.unit'                => 'Unit',
      'tb_return_items.awb_number'          => 'AWB Number',
      'tb_return_items.remarks'             => 'Remarks',
      'tb_returns.issued_to'                => 'Sent To',
      'tb_returns.issued_by'                => 'Released By',
      'tb_return_items.received_from'            => 'Received From',
    );

    if (config_item('auth_role') != 'PIC STOCK'){
      $selected['tb_return_items.insurance_unit_value']  = 'Value';
      $selected['tb_return_items.issued_total_value'] = 'Total Value IDR';
    }

    return $selected;
  }

  public function getSearchableColumns()
  {
    return array(
      'tb_returns.document_number',
      'tb_returns.category',
      'tb_returns.warehouse',
      'tb_return_items.description',
      'tb_return_items.part_number',
      'tb_return_items.serial_number',
      'tb_return_items.condition',
      'tb_return_items.unit',
      'tb_return_items.awb_number',
      'tb_return_items.remarks',
      'tb_returns.issued_to',
      'tb_returns.issued_by',
      'tb_returns.received_from'
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'tb_returns.document_number',
      'tb_returns.category',
      'tb_returns.warehouse',
      'tb_return_items.description',
      'tb_return_items.part_number',
      'tb_return_items.serial_number',
      'tb_return_items.condition',
      'tb_return_items.unit',
      'tb_return_items.awb_number',
      'tb_return_items.remarks',
      'tb_returns.issued_to',
      'tb_returns.issued_by',
      'tb_returns.received_from'
    );
  }

  private function searchIndex()
  {
    if (!empty($_POST['columns'][2]['search']['value'])){
      $search_issued_date = $_POST['columns'][2]['search']['value'];
      $range_issued_date  = explode(' ', $search_issued_date);

      $this->db->where('tb_returns.issued_date >= ', $range_issued_date[0]);
      $this->db->where('tb_returns.issued_date <= ', $range_issued_date[1]);
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
    $this->db->from('tb_returns');
    $this->db->join('tb_return_items', 'tb_return_items.return_id = tb_returns.id');
    $this->db->where_in('tb_returns.category', config_item('auth_inventory'));
    $this->db->where_in('tb_returns.warehouse', config_item('auth_warehouses'));
    $this->db->like('tb_returns.document_number', 'CI');

    $this->searchIndex();

    $column_order = $this->getOrderableColumns();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('id', 'desc');
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
    $this->db->from('tb_returns');
    $this->db->join('tb_return_items', 'tb_return_items.return_id = tb_returns.id');
    $this->db->where_in('tb_returns.category', config_item('auth_inventory'));
    $this->db->where_in('tb_returns.warehouse', config_item('auth_warehouses'));
    $this->db->like('tb_returns.document_number', 'CI');

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->from('tb_returns');
    $this->db->join('tb_return_items', 'tb_return_items.return_id = tb_returns.id');
    $this->db->where_in('tb_returns.category', config_item('auth_inventory'));
    $this->db->where_in('tb_returns.warehouse', config_item('auth_warehouses'));
    $this->db->like('tb_returns.document_number', 'CI');

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function findById($id)
  {
    $this->db->where('id', $id);

    $query    = $this->db->get('tb_returns');
    $issued   = $query->unbuffered_row('array');

    $select = array(
      'tb_return_items.*'
    );

    $this->db->select($select);
    $this->db->from('tb_return_items');
    $this->db->where('tb_return_items.return_id', $issued['id']);

    $query = $this->db->get();

    foreach ($query->result_array() as $key => $value){
      $issued['items'][$key] = $value;

      if (empty($issued['category'])){
        $this->db->select('category');
        $this->db->from('tb_master_item_groups');
        $this->db->where('group', $value['group']);

        $query = $this->db->get();
        $icat  = $query->unbuffered_row();

        $issued['category'] = $icat->category;
      }
    }

    return $issued;
  }

  public function isDocumentNumberExists($document_number)
  {
    $this->db->where('document_number', $document_number);
    $query = $this->db->get('tb_issuances');

    if ($query->num_rows() > 0)
      return true;

    return false;
  }

  public function isValidDocumentQuantity($document_number)
  {
    $this->db->select_sum('tb_issuance_items.issued_quantity', 'issued_quantity');
    $this->db->select_sum('tb_stock_in_stores.quantity', 'stored_quantity');
    $this->db->select('tb_issuance_items.document_number');
    $this->db->from('tb_issuance_items');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_issuance_items.stock_in_stores_id');
    $this->db->where('tb_issuance_items.document_number', $document_number);
    $this->db->group_by('tb_issuance_items.document_number');

    $query  = $this->db->get();
    $row    = $query->unbuffered_row('array');

    if ($row['issued_quantity'] === $row['issued_quantity'])
      return true;

    return false;
  }

  public function save()
  {
    $document_id      = (isset($_SESSION['return']['id'])) ? $_SESSION['return']['id'] : NULL;
    $document_edit    = (isset($_SESSION['return']['edit'])) ? $_SESSION['return']['edit'] : NULL;
    $document_number  = $_SESSION['return']['document_number'] . return_format_number();
    $issued_date      = $_SESSION['return']['issued_date'];
    $source           = $_SESSION['return']['source'];
    $issued_by        = (empty($_SESSION['return']['issued_by'])) ? NULL : $_SESSION['return']['issued_by'];
    $issued_to        = (empty($_SESSION['return']['issued_to'])) ? NULL : $_SESSION['return']['issued_to'];
    $issued_address   = (empty($_SESSION['return']['issued_address'])) ? NULL : $_SESSION['return']['issued_address'];
    $sent_by          = (empty($_SESSION['return']['sent_by'])) ? NULL : $_SESSION['return']['sent_by'];
    $known_by         = (empty($_SESSION['return']['known_by'])) ? NULL : $_SESSION['return']['known_by'];
    $approved_by      = (empty($_SESSION['return']['approved_by'])) ? NULL : $_SESSION['return']['approved_by'];
    $warehouse        = $_SESSION['return']['warehouse'];
    $category         = $_SESSION['return']['category'];
    $notes            = (empty($_SESSION['return']['notes'])) ? NULL : $_SESSION['return']['notes'];

    $this->db->trans_begin();

    if ($document_id === NULL){
      $this->db->set('document_number', $document_number);
      $this->db->set('issued_to', $issued_to);
      $this->db->set('issued_address', $issued_address);
      $this->db->set('issued_date', $issued_date);
      $this->db->set('issued_by', $issued_by);
      $this->db->set('sent_by', $sent_by);
      $this->db->set('known_by', $known_by);
      $this->db->set('approved_by', $approved_by);
      $this->db->set('category', $category);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('notes', $notes);
      $this->db->set('source', $source);
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->insert('tb_returns');
      $document_id = $this->db->insert_id();
    } else {
      /**
       * EDIT DOCUMENT
       * decrease quantity
       * create document revision
       */

      // if($source=='stock'){
        
      // }

      $this->db->select('tb_returns.*');
      $this->db->where('id', $document_id);
      $this->db->from('tb_returns');

      $query = $this->db->get();
      $row   = $query->unbuffered_row('array');

      $old_document_number  = $row['document_number'];
      $old_warehouse        = $row['warehouse'];

      $this->db->select('tb_return_items.*');
      $this->db->from('tb_return_items');
      $this->db->where('tb_return_items.return_id', $document_id);

      $query = $this->db->get();

      foreach ($query->result_array() as $data) {
        if($row['source'] == 'stock'){
          $this->db->select('tb_stock_in_stores.*');
          $this->db->where('id', $data['stock_in_stores_id']);
          $this->db->from('tb_stock_in_stores');

          $query = $this->db->get();
          $stock_in_stores   = $query->unbuffered_row('array');

          $prev_old_stock = getStockPrev($stock_in_stores['stock_id'], $stock_in_stores['stores']);
          $next_old_stock = floatval($prev_old_stock) + floatval($data['issued_quantity']);

          $this->db->set('stock_id', $stock_in_stores['stock_id']);
          $this->db->set('serial_id', $stock_in_stores['serial_id']);
          $this->db->set('warehouse', $old_warehouse);
          $this->db->set('stores', $stock_in_stores['stores']);
          $this->db->set('date_of_entry', $row['issued_date']);
          $this->db->set('period_year', config_item('period_year'));
          $this->db->set('period_month', config_item('period_month'));
          $this->db->set('document_type', 'REVISION RETURN');
          $this->db->set('remarks', 'REVISION');
          $this->db->set('document_number', $old_document_number);
          $this->db->set('received_from', $old_document_number);
          $this->db->set('received_by', config_item('auth_person_name'));
          $this->db->set('prev_quantity', floatval($prev_old_stock));
          $this->db->set('balance_quantity', $next_old_stock);
          $this->db->set('quantity', 0 + floatval($data['issued_quantity']));
          $this->db->set('unit_value', floatval($data['issued_unit_value']));
          $this->db->set('created_by', config_item('auth_person_name'));
          $this->db->set('stock_in_stores_id', $data['stock_in_stores_id']);
          $this->db->set('doc_type', 10);
          $this->db->set('tgl', date('Ymd', strtotime($row['issued_date'])));
          $this->db->set('total_value', floatval($data['issued_unit_value']) * (0 + floatval($data['issued_quantity'])));
          $this->db->insert('tb_stock_cards');

          $this->db->from('tb_stock_in_stores');
          $this->db->where('id', $data['stock_in_stores_id']);

          $query        = $this->db->get();
          $stock_stored = $query->unbuffered_row('array');
          $new_quantity = $stock_stored['quantity'] + $data['issued_quantity'];

          $this->db->where('id', $data['stock_in_stores_id']);
          $this->db->set('quantity', floatval($new_quantity));
          $this->db->update('tb_stock_in_stores');

          $this->db->where('id', $data['serial_id']);
          $this->db->set('quantity', 1);
          $this->db->update('tb_master_item_serials');

        }elseif($row['source']=='internal_delivery'){
          if ($data['internal_delivery_item_id'] != null) {
            $this->db->where('id', $data['internal_delivery_item_id']);
            $this->db->set('left_received_quantity', 'left_received_quantity +' . $data['issued_quantity'], FALSE);
            $this->db->update('tb_internal_delivery_items');
          }
        }
        
      }

      /**
       * CREATE RETURN DOCUMENT
       */
      $this->db->set('document_number', $document_number);
      $this->db->set('issued_date', $issued_date);
      $this->db->set('issued_to', $issued_to);
      $this->db->set('issued_address', $issued_address);
      $this->db->set('issued_by', $issued_by);
      $this->db->set('known_by', $known_by);
      $this->db->set('approved_by', $approved_by);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('category', $category);
      $this->db->set('notes', $notes);
      $this->db->set('source', $source);
      $this->db->set('updated_at', date('Y-m-d'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->where('id', $document_id);
      $this->db->update('tb_returns');

      /**
       * DELETE OLD RETURN ITEMS
       */
      $this->db->where('return_id', $document_id);
      $this->db->delete('tb_return_items');

      /**
       * DELETE OLD STOCK
       */
      // $this->db->where('reference_document', $document_edit);
      // $this->db->delete('tb_stock_in_stores');

      /**
       * UPDATE SERIAL
       */
      // $this->db->where('reference_document', $document_edit);
      // $this->db->set('quantity', 1);
      // $this->db->delete('tb_master_item_serials');
    }

    /**
     * PROCESSING RETURN ITEMS
     */
    foreach ($_SESSION['return']['items'] as $key => $data){
      if($source=='stock'){
        $stock_in_stores_id = $data['stock_in_stores_id'];

        $this->db->from('tb_stock_in_stores');
        $this->db->where('id', $stock_in_stores_id);

        $query        = $this->db->get();
        $stock_stored = $query->unbuffered_row('array');
        $new_quantity = $stock_stored['quantity'] - $data['issued_quantity'];

        // UPDATE STOCK in STORES
        $this->db->set('quantity', floatval($new_quantity));
        $this->db->where('id', $stock_in_stores_id);
        $this->db->update('tb_stock_in_stores');

        // UPDATE STOCK in SERIAL
        $this->db->set('quantity', 0);
        $this->db->set('reference_document', $document_number);
        $this->db->where('id', $stock_stored['serial_id']);
        $this->db->update('tb_master_item_serials');

        /**
         * CREATE STOCK CARD
         */

        $prev_old_stock = getStockPrev($stock_stored['stock_id'], strtoupper($stock_stored['stores']));
        $next_old_stock = floatval($prev_old_stock) - floatval($data['issued_quantity']);

        $this->db->set('serial_id', $stock_stored['serial_id']);
        $this->db->set('stock_id', $stock_stored['stock_id']);
        $this->db->set('warehouse', $stock_stored['warehouse']);
        $this->db->set('stores', strtoupper($stock_stored['stores']));
        $this->db->set('date_of_entry', $issued_date);
        $this->db->set('period_year', config_item('period_year'));
        $this->db->set('period_month', config_item('period_month'));
        $this->db->set('document_type', 'RETURN');
        $this->db->set('document_number', $document_number);
        $this->db->set('issued_to', $issued_to);
        $this->db->set('issued_by', $issued_by);
        $this->db->set('quantity', 0 - floatval($data['issued_quantity']));
        $this->db->set('unit_value', floatval($data['issued_unit_value']));
        $this->db->set('prev_quantity', floatval($prev_old_stock));
        $this->db->set('balance_quantity', $next_old_stock);
        $this->db->set('remarks', $data['remarks']);
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('stock_in_stores_id', $stock_in_stores_id);
        $this->db->set('doc_type', 10);
        $this->db->set('tgl', date('Ymd',strtotime($issued_date)));
        $this->db->set('total_value', floatval($data['issued_unit_value'])*(0 - floatval($data['issued_quantity'])));
        $this->db->insert('tb_stock_cards');
      }elseif ($source=='internal_delivery') {
        if (!empty($data['internal_delivery_item_id'])) {
          $this->db->from('tb_internal_delivery_items');
          $this->db->where('tb_internal_delivery_items.id', $data['internal_delivery_item_id']);
  
          $query  = $this->db->get();
          $row    = $query->unbuffered_row('array');
          $qty    = floatval($row['left_received_quantity']) - floatval($data['issued_quantity']);
  
          $this->db->where('id', $data['internal_delivery_item_id']);
          $this->db->set('left_received_quantity', 'left_received_quantity -' . $data['issued_quantity'], FALSE);
          $this->db->update('tb_internal_delivery_items');
  
          $left_qty_internal_delivery = countLeftQuantityInternalDelivery($row['internal_delivery_id']);
          if ($left_qty_internal_delivery == 0) {
            $this->db->where('id', $row['internal_delivery_id']);
            $this->db->set('status', 'CLOSED');
            $this->db->update('tb_internal_delivery');
          }
        }
      }

      /**
        * INSERT INTO RETURN ITEMS
      */
      $this->db->set('return_id', $document_id);
      if(!empty($data['stock_in_stores_id'])){
        $this->db->set('stock_in_stores_id', $data['stock_in_stores_id']);
      }
      if(!empty($data['internal_delivery_item_id'])){
        $this->db->set('internal_delivery_item_id', $data['internal_delivery_item_id']);
      }            
      $this->db->set('part_number', strtoupper($data['part_number']));
      $this->db->set('serial_number', strtoupper($data['serial_number']));
      $this->db->set('alternate_part_number', strtoupper($data['alternate_part_number']));
      $this->db->set('description', strtoupper($data['description']));
      $this->db->set('group', strtoupper($data['group']));
      $this->db->set('unit', strtoupper($data['unit']));
      $this->db->set('stores', strtoupper($data['stores']));
      $this->db->set('issued_quantity', floatval($data['issued_quantity']));
      $this->db->set('left_process_quantity', floatval($data['issued_quantity']));
      $this->db->set('issued_unit_value', floatval($data['issued_unit_value']));
      $this->db->set('issued_total_value', floatval($data['issued_unit_value']) * floatval($data['issued_quantity']));
      $this->db->set('insurance_unit_value', floatval($data['insurance_unit_value']));
      $this->db->set('insurance_currency', $data['insurance_currency']);
      $this->db->set('awb_number', $data['awb_number']);
      $this->db->set('remarks', $data['remarks']);
      $this->db->set('condition', $data['condition']);
      $this->db->set('received_from', $data['received_from']);
      $this->db->insert('tb_return_items');
      
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

    // $this->db->select('document_number, warehouse,issued_date');
    // $this->db->where('id', $id);
    // $this->db->from('tb_issuances');

    // $query = $this->db->get();
    // $row   = $query->unbuffered_row('array');

    // $document_number  = $row['document_number'];
    // $warehouse        = $row['warehouse'];

    // $this->db->select('tb_issuance_items.id, tb_issuance_items.stock_in_stores_id, tb_issuance_items.issued_quantity, tb_issuance_items.issued_unit_value, tb_stock_in_stores.stock_id, tb_stock_in_stores.serial_id, tb_stock_in_stores.stores');
    // $this->db->from('tb_issuance_items');
    // $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_issuance_items.stock_in_stores_id');
    // $this->db->where('tb_issuance_items.document_number', $document_number);

    // $query  = $this->db->get();
    // $result = $query->result_array();

    // foreach ($result as $data) {
    //   $prev_old_stock = getStockPrev($data['stock_id'], strtoupper($data['stores']));
    //   $next_old_stock = floatval($prev_old_stock) + floatval($data['issued_quantity']);

    //   $this->db->set('stock_id', $data['stock_id']);
    //   $this->db->set('serial_id', $data['serial_id']);
    //   $this->db->set('warehouse', $warehouse);
    //   $this->db->set('stores', $data['stores']);
    //   $this->db->set('date_of_entry', $row['issued_date']);
    //   $this->db->set('period_year', config_item('period_year'));
    //   $this->db->set('period_month', config_item('period_month'));
    //   $this->db->set('document_type', 'REMOVAL');
    //   $this->db->set('document_number', $document_number);
    //   $this->db->set('issued_to', 'DELETE DOCUMENT');
    //   $this->db->set('issued_by', config_item('auth_person_name'));
    //   $this->db->set('quantity', 0 + floatval($data['issued_quantity']));
    //   $this->db->set('unit_value', floatval($data['issued_unit_value']));
    //   $this->db->set('created_by', config_item('auth_person_name'));
    //   $this->db->set('prev_quantity', floatval($prev_old_stock));
    //   $this->db->set('balance_quantity', $next_old_stock);
    //   $this->db->set('doc_type', 10);
    //   $this->db->set('tgl', date('Ymd', strtotime($row['issued_date'])));
    //   $this->db->set('total_value', floatval($data['issued_unit_value']) * (0 + floatval($data['issued_quantity'])));
    //   $this->db->insert('tb_stock_cards');

    //   $this->db->from('tb_stock_in_stores');
    //   $this->db->where('id', $data['stock_in_stores_id']);
    //   $query        = $this->db->get();
    //   $stock_stored = $query->unbuffered_row('array');
    //   $new_quantity = $stock_stored['quantity'] + $data['issued_quantity'];

    //   $this->db->where('id', $data['stock_in_stores_id']);
    //   $this->db->set('quantity', floatval($new_quantity));
    //   $this->db->update('tb_stock_in_stores');

    //   $this->db->where('id', $data['serial_id']);
    //   $this->db->set('quantity', 1);
    //   $this->db->update('tb_master_item_serials');

    //   $this->db->where('id', $data['id']);
    //   $this->db->delete('tb_issuance_items');

    //   // $this->db->where('id', $data['id']);
    //   // $this->db->delete('tb_issuance_items');

    //   // $this->db->where('reference_document', $document_number);
    //   // $this->db->delete('tb_stock_in_stores');
    // }

    $this->db->select('tb_returns.*');
    $this->db->where('id', $id);
    $this->db->from('tb_returns');

    $query = $this->db->get();
    $row   = $query->unbuffered_row('array');

    $old_document_number  = $row['document_number'];
    $old_warehouse        = $row['warehouse'];

    $this->db->select('tb_return_items.*');
    $this->db->from('tb_return_items');
    $this->db->where('tb_return_items.return_id', $id);

    $query = $this->db->get();

    foreach ($query->result_array() as $data) {
      if($row['source'] == 'stock' && !empty($data['stock_in_stores_id'])){
        $this->db->select('tb_stock_in_stores.*');
        $this->db->where('id', $data['stock_in_stores_id']);
        $this->db->from('tb_stock_in_stores');

        $query = $this->db->get();
        $stock_in_stores   = $query->unbuffered_row('array');

        $prev_old_stock = getStockPrev($stock_in_stores['stock_id'], $stock_in_stores['stores']);
        $next_old_stock = floatval($prev_old_stock) + floatval($data['issued_quantity']);

        $this->db->set('stock_id', $stock_in_stores['stock_id']);
        $this->db->set('serial_id', $stock_in_stores['serial_id']);
        $this->db->set('warehouse', $old_warehouse);
        $this->db->set('stores', $stock_in_stores['stores']);
        $this->db->set('date_of_entry', $row['issued_date']);
        $this->db->set('period_year', config_item('period_year'));
        $this->db->set('period_month', config_item('period_month'));
        $this->db->set('document_type', 'REVISION RETURN');
        $this->db->set('remarks', 'REVISION');
        $this->db->set('document_number', $old_document_number);
        $this->db->set('received_from', $old_document_number);
        $this->db->set('received_by', config_item('auth_person_name'));
        $this->db->set('prev_quantity', floatval($prev_old_stock));
        $this->db->set('balance_quantity', $next_old_stock);
        $this->db->set('quantity', 0 + floatval($data['issued_quantity']));
        $this->db->set('unit_value', floatval($data['issued_unit_value']));
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('stock_in_stores_id', $data['stock_in_stores_id']);
        $this->db->set('doc_type', 10);
        $this->db->set('tgl', date('Ymd', strtotime($row['issued_date'])));
        $this->db->set('total_value', floatval($data['issued_unit_value']) * (0 + floatval($data['issued_quantity'])));
        $this->db->insert('tb_stock_cards');

        $this->db->from('tb_stock_in_stores');
        $this->db->where('id', $data['stock_in_stores_id']);

        $query        = $this->db->get();
        $stock_stored = $query->unbuffered_row('array');
        $new_quantity = $stock_stored['quantity'] + $data['issued_quantity'];

        $this->db->where('id', $data['stock_in_stores_id']);
        $this->db->set('quantity', floatval($new_quantity));
        $this->db->update('tb_stock_in_stores');

        $this->db->where('id', $data['serial_id']);
        $this->db->set('quantity', 1);
        $this->db->update('tb_master_item_serials');

      }elseif($row['source']=='internal_delivery'){
        if (!empty($data['internal_delivery_item_id'])) {
          $this->db->where('id', $data['internal_delivery_item_id']);
          $this->db->set('left_received_quantity', 'left_received_quantity +' . $data['issued_quantity'], FALSE);
          $this->db->update('tb_internal_delivery_items');
        }
      }
        
    }

      
    $this->db->where('return_id', $id);
    $this->db->delete('tb_return_items');

    $this->db->where('id', $id);
    $this->db->delete('tb_returns');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function searchStockInStores($category)
  {
    $this->column_select = array(
      'tb_stock_in_stores.id',
      'tb_stock_in_stores.stores',
      'tb_stock_in_stores.received_date',
      'tb_stock_in_stores.expired_date',
      'tb_stock_in_stores.unit_value',
      'tb_stock_in_stores.quantity',
      'tb_stocks.condition',
      'tb_master_item_serials.serial_number',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.alternate_part_number',
      'tb_master_items.group',
      'tb_master_items.unit',
      'tb_receipts.received_from',
    );

    $this->db->select($this->column_select);
    $this->db->from('tb_stock_in_stores');
    $this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_in_stores.serial_id', 'left');
    $this->db->join('tb_receipt_items', 'tb_receipt_items.stock_in_stores_id = tb_stock_in_stores.id');
    $this->db->join('tb_receipts', 'tb_receipts.document_number = tb_receipt_items.document_number');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
    $this->db->where('tb_master_item_groups.category', $category);
    // $this->db->where('tb_stocks.condition', 'SERVICEABLE');
    $this->db->where('tb_stock_in_stores.quantity > ', 0);
    $this->db->where('tb_stock_in_stores.warehouse', config_item('auth_warehouse'));

    $this->db->order_by('tb_stock_in_stores.received_date ASC');

    $query  = $this->db->get();
    $result = $query->result_array();

    return $result;
  }

  public function searchInternalDeliveryItem($category)
  {
    $this->column_select = array(
      'tb_internal_delivery_items.id',
      'tb_internal_delivery_items.unit_price as unit_value',
      'tb_internal_delivery_items.serial_number',
      'tb_internal_delivery_items.part_number',
      'tb_internal_delivery_items.description',
      'tb_internal_delivery_items.alternate_part_number',
      'tb_internal_delivery_items.left_received_quantity as quantity',
      'tb_internal_delivery_items.unit',
      'tb_internal_delivery.received_from',
      'tb_internal_delivery.received_date',
      'tb_internal_delivery.document_number',
      'tb_internal_delivery_items.group',
      'tb_internal_delivery_items.unit as unit_pakai',
      'tb_internal_delivery_items.condition',
    );

    $this->db->select($this->column_select);
    $this->db->from('tb_internal_delivery_items');
    $this->db->join('tb_internal_delivery', 'tb_internal_delivery.id = tb_internal_delivery_items.internal_delivery_id');
    $this->db->where('tb_internal_delivery.category', $category);
    // $this->db->where_in('tb_internal_delivery.status', ['APPROVED']);
    $this->db->where_in('tb_internal_delivery_items.condition', ['UNSERVICEABLE','REJECT']);
    $this->db->where('tb_internal_delivery_items.left_received_quantity > ', 0);
    $this->db->group_by(array(
      'tb_internal_delivery_items.id',
      'tb_internal_delivery_items.unit_price',
      'tb_internal_delivery_items.serial_number',
      'tb_internal_delivery_items.part_number',
      'tb_internal_delivery_items.description',
      'tb_internal_delivery_items.alternate_part_number',
      'tb_internal_delivery_items.left_received_quantity',
      'tb_internal_delivery_items.unit',
      'tb_internal_delivery.received_from',
      'tb_internal_delivery.document_number',
      'tb_internal_delivery_items.group',
      'tb_internal_delivery_items.condition',
      'tb_internal_delivery.received_date'
    ));

    $this->db->order_by('tb_internal_delivery.document_number ASC');
    $query  = $this->db->get();
    $result = $query->result_array();

    return $result;
    
  }

  public function infoSelecteditem($id)
  {
    if($_SESSION['return']['source']=='internal_delivery'){
      $this->column_select = array(
        'tb_internal_delivery_items.id',
        'tb_internal_delivery_items.unit_price as unit_value',
        'tb_internal_delivery_items.serial_number',
        'tb_internal_delivery_items.part_number',
        'tb_internal_delivery_items.description',
        'tb_internal_delivery_items.alternate_part_number',
        'tb_internal_delivery_items.left_received_quantity as quantity',
        'tb_internal_delivery_items.unit',
        'tb_internal_delivery.received_from',
        'tb_internal_delivery.received_date',
        'tb_internal_delivery.document_number',
        'tb_internal_delivery_items.group',
        'tb_internal_delivery_items.unit as unit_pakai',
        'tb_internal_delivery_items.condition',
      );
  
      $this->db->select($this->column_select);
      $this->db->from('tb_internal_delivery_items');
      $this->db->join('tb_internal_delivery', 'tb_internal_delivery.id = tb_internal_delivery_items.internal_delivery_id');
      $this->db->where('tb_internal_delivery_items.id', $id);
      $this->db->group_by(array(
        'tb_internal_delivery_items.id',
        'tb_internal_delivery_items.unit_price',
        'tb_internal_delivery_items.serial_number',
        'tb_internal_delivery_items.part_number',
        'tb_internal_delivery_items.description',
        'tb_internal_delivery_items.alternate_part_number',
        'tb_internal_delivery_items.left_received_quantity',
        'tb_internal_delivery_items.unit',
        'tb_internal_delivery.received_from',
        'tb_internal_delivery.document_number',
        'tb_internal_delivery_items.group',
        'tb_internal_delivery_items.condition',
        'tb_internal_delivery.received_date'
      ));
  
      $this->db->order_by('tb_internal_delivery.document_number ASC');
      $query  = $this->db->get();
      $result = $query->unbuffered_row('array');

    }elseif ($_SESSION['return']['source']=='stock') {
      $this->column_select = array(
        'tb_stock_in_stores.id',
        'tb_stock_in_stores.stores',
        'tb_stock_in_stores.received_date',
        'tb_stock_in_stores.expired_date',
        'tb_stock_in_stores.unit_value',
        'tb_stock_in_stores.quantity',
        'tb_stocks.condition',
        'tb_master_item_serials.serial_number',
        'tb_master_items.part_number',
        'tb_master_items.description',
        'tb_master_items.alternate_part_number',
        'tb_master_items.group',
        'tb_master_items.unit',
        'tb_receipts.received_from',
      );
  
      $this->db->select($this->column_select);
      $this->db->from('tb_stock_in_stores');
      $this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_in_stores.serial_id', 'left');
      $this->db->join('tb_receipt_items', 'tb_receipt_items.stock_in_stores_id = tb_stock_in_stores.id');
      $this->db->join('tb_receipts', 'tb_receipts.document_number = tb_receipt_items.document_number');
      $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
      $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
      $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
      $this->db->where('tb_stock_in_stores.id', $id);
  
      $this->db->order_by('tb_stock_in_stores.received_date ASC');
  
      $query  = $this->db->get();
      $result = $query->unbuffered_row('array');
    }
    

    return $result;
    
  }
}
