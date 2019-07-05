<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Shipping_Document_Receipt_Model extends MY_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function getSelectedColumns()
  {
    return array(
      'tb_issuances.id'                     => NULL,
      'tb_issuances.document_number'        => 'Document Number',
      'tb_issuances.issued_date'            => 'Date',
      'tb_issuances.warehouse'              => 'From',
      'tb_issuances.issued_by'              => 'Released By',
      'tb_issuances.received_date'          => 'Received Date',
      'tb_issuances.received_by'            => 'Received By',
      'tb_issuances.category'               => 'Category',
      'tb_issuances.notes'                  => 'Notes',
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'tb_issuances.document_number',
      'tb_issuances.warehouse',
      'tb_issuances.issued_by',
      'tb_issuances.received_by',
      'tb_issuances.category',
      'tb_issuances.notes',
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'tb_issuances.document_number',
      'tb_issuances.issued_date',
      'tb_issuances.warehouse',
      'tb_issuances.issued_by',
      'tb_issuances.received_by',
      'tb_issuances.category',
      'tb_issuances.notes',
    );
  }

  private function searchIndex()
  {
    if (!empty($_POST['columns'][2]['search']['value'])){
      $search_issued_date = $_POST['columns'][2]['search']['value'];
      $range_issued_date  = explode(' ', $search_issued_date);

      $this->db->where('tb_issuances.issued_date >= ', $range_issued_date[0]);
      $this->db->where('tb_issuances.issued_date <= ', $range_issued_date[1]);
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
    $this->db->from('tb_issuances');
    $this->db->where_in('tb_issuances.category', config_item('auth_inventory'));
    $this->db->where_in('tb_issuances.issued_to', config_item('auth_warehouses'));
    $this->db->like('tb_issuances.document_number', 'SD');

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
    $this->db->from('tb_issuances');
    $this->db->where_in('tb_issuances.category', config_item('auth_inventory'));
    $this->db->where_in('tb_issuances.warehouse', config_item('auth_warehouses'));
    $this->db->like('tb_issuances.document_number', 'SD');

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->from('tb_issuances');
    $this->db->where_in('tb_issuances.category', config_item('auth_inventory'));
    $this->db->where_in('tb_issuances.warehouse', config_item('auth_warehouses'));
    $this->db->like('tb_issuances.document_number', 'SD');

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function findById($id)
  {
    $this->db->where('id', $id);

    $query    = $this->db->get('tb_issuances');
    $issued   = $query->unbuffered_row('array');

    $select = array(
      'tb_issuance_items.*',
      'tb_stocks.condition',
      'tb_stock_in_stores.stores',
      'tb_master_items.serial_number',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.alternate_part_number',
      'tb_master_items.unit',
      'tb_master_items.group',
    );

    $this->db->select($select);
    $this->db->from('tb_issuance_items');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_issuance_items.stock_in_stores_id');
    // $this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_in_stores.serial_id', 'left');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->where('tb_issuance_items.document_number', $issued['document_number']);

    $query = $this->db->get();

    foreach ($query->result_array() as $key => $value){
      $issued['items'][$key] = $value;

      $this->db->from('tb_issuance_item_receipts');
      $this->db->where('tb_issuance_item_receipts.issuance_item_id', $value['id']);

      $query = $this->db->get();

      foreach ($query->result_array() as $i => $item) {
        $issued['items'][$key]['received'][$i] = $item;

        $this->db->from('tb_issuance_item_receipt_details');
        $this->db->where('tb_issuance_item_receipt_details.issuance_item_receipt_id', $item['id']);

        $query = $this->db->get();

        foreach ($query->result_array() as $d => $detail) {
          $issued['items'][$key]['received'][$i]['details'][$d] = $detail;
        }
      }
    }

    return $issued;
  }

  public function findStores($warehouse, $category)
  {
    $this->db->select('tb_master_stores.stores');
    $this->db->from('tb_master_stores');
    $this->db->where('UPPER(tb_master_stores.warehouse)', strtoupper($warehouse));
    $this->db->where_in('tb_master_stores.category', $category);
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

  public function save($id)
  {
    $this->db->trans_begin();

    $category         = $this->input->post('category');
    $warehouse        = $this->input->post('warehouse');
    $document_number  = $this->input->post('document_number');
    $received_from    = $this->input->post('received_from');
    $received_date    = $this->input->post('received_date');
    $received_by      = $this->input->post('received_by');

    $this->db->set('received_date', $received_date);
    $this->db->set('received_by', $received_by);
    $this->db->where('id', $id);
    $this->db->update('tb_issuances');

    $this->db->set('document_number', $document_number);
    $this->db->set('received_from', $received_from);
    $this->db->set('received_date', $received_date);
    $this->db->set('received_by', $received_by);
    // $this->db->set('known_by', $known_by);
    // $this->db->set('approved_by', $approved_by);
    $this->db->set('category', $category);
    $this->db->set('warehouse', $warehouse);
    // $this->db->set('notes', $notes);
    $this->db->set('created_by', config_item('auth_person_name'));
    $this->db->set('updated_by', config_item('auth_person_name'));
    $this->db->insert('tb_receipts');

    foreach ($_POST['items'] as $id => $data){
      $serial_number = (empty($data['serial_number'])) ? NULL : $data['serial_number'];
      $item_id = getItemId($data['part_number'], $serial_number);

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
       * ADD ITEM INTO STOCK
       */
      $stock_id   = getStockId($item_id, strtoupper($data['condition']));
      $prev_stock = getStockActive($stock_id);
      $next_stock = floatval($prev_stock->total_quantity) + floatval($data['received_quantity']);

      /**
       * ADD ITEM INTO STORES
       */
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
      $this->db->insert('tb_stock_in_stores');

      $stock_in_stores_id = $this->db->insert_id();

      /**
       * INSERT INTO DELIVERY ITEMS
       */
      $this->db->set('issuance_item_id', $data['issuance_item_id']);
      $this->db->set('stock_in_stores_id', $stock_in_stores_id);
      $this->db->set('received_quantity', floatval($data['received_quantity']));
      $this->db->set('received_unit_value', floatval($data['received_unit_value']));
      $this->db->set('received_total_value', floatval($data['received_unit_value']) * floatval($data['received_quantity']));
      $this->db->set('remarks', $data['remarks']);
      $this->db->insert('tb_issuance_item_receipts');

      /**
       * INSERT INTO RECEIPT ITEMS
       */
      // $this->db->set('issuance_item_id', $data['issuance_item_id']);
      // $this->db->set('stock_in_stores_id', $stock_in_stores_id);
      // $this->db->set('received_quantity', floatval($data['received_quantity']));
      // $this->db->set('received_unit_value', floatval($data['received_unit_value']));
      // $this->db->set('received_total_value', floatval($data['received_unit_value']) * floatval($data['received_quantity']));
      // $this->db->set('remarks', $data['remarks']);
      // $this->db->insert('tb_issuance_item_receipts');

      $this->db->set('document_number', $document_number);
      $this->db->set('stock_in_stores_id', $stock_in_stores_id);
      $this->db->set('received_quantity', floatval($data['received_quantity']));
      $this->db->set('received_unit_value', floatval($data['received_unit_value']));
      $this->db->set('received_total_value', floatval($data['received_unit_value']) * floatval($data['received_quantity']));
      // $this->db->set('purchase_order_number', $data['purchase_order_number']);
      // $this->db->set('reference_number', $data['reference_number']);
      // $this->db->set('awb_number', $data['awb_number']);
      $this->db->set('remarks', $data['remarks']);
      $this->db->insert('tb_receipt_items');

      /**
       * CREATE STOCK CARD
       */
      $this->db->set('serial_id', $serial_id);
      $this->db->set('stock_id', $stock_id);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('stores', strtoupper($data['stores']));
      $this->db->set('date_of_entry', $received_date);
      $this->db->set('period_year', config_item('period_year'));
      $this->db->set('period_month', config_item('period_month'));
      $this->db->set('document_type', 'SHIPMENT');
      $this->db->set('document_number', $document_number);
      $this->db->set('received_from', $received_from);
      $this->db->set('received_by', $received_by);
      $this->db->set('prev_quantity', floatval($prev_stock->total_quantity));
      $this->db->set('balance_quantity', $next_stock);
      $this->db->set('quantity', floatval($data['received_quantity']));
      $this->db->set('unit_value', floatval($data['received_unit_value']));
      $this->db->set('remarks', $data['remarks']);
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

    $this->db->select('document_number, warehouse');
    $this->db->where('id', $id);
    $this->db->from('tb_issuances');

    $query = $this->db->get();
    $row   = $query->unbuffered_row('array');

    $document_number  = $row['document_number'];
    $warehouse        = $row['warehouse'];

    $this->db->select('tb_issuance_items.id, tb_issuance_items.stock_in_stores_id, tb_issuance_items.issued_quantity, tb_issuance_items.issued_unit_value, tb_stock_in_stores.stock_id, tb_stock_in_stores.serial_id, tb_stock_in_stores.stores');
    $this->db->from('tb_issuance_items');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_issuance_items.stock_in_stores_id');
    $this->db->where('tb_issuance_items.document_number', $document_number);

    $query  = $this->db->get();
    $result = $query->result_array();

    foreach ($result as $data) {
      $this->db->set('stock_id', $data['stock_id']);
      $this->db->set('serial_id', $data['serial_id']);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('stores', $data['stores']);
      $this->db->set('date_of_entry', date('Y-m-d'));
      $this->db->set('period_year', config_item('period_year'));
      $this->db->set('period_month', config_item('period_month'));
      $this->db->set('document_type', 'REMOVAL');
      $this->db->set('document_number', $document_number);
      $this->db->set('issued_to', 'DELETE DOCUMENT');
      $this->db->set('issued_by', config_item('auth_person_name'));
      $this->db->set('quantity', 0 - floatval($data['issued_quantity']));
      $this->db->set('unit_value', floatval($data['issued_unit_value']));
      $this->db->insert('tb_stock_cards');

      $this->db->where('id', $data['id']);
      $this->db->delete('tb_issuance_items');

      $this->db->where('id', $data['stock_in_stores_id']);
      $this->db->delete('tb_stock_in_stores');
    }

    $this->db->where('id', $id);
    $this->db->delete('tb_issuances');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }
}
