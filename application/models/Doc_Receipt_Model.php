<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Doc_Receipt_Model extends MY_Model
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = config_item('module')['doc_receipt'];
  }

  public function getSelectedColumns()
  {
    return array(
      'tb_doc_receipts.id'              => NULL,
      'tb_doc_receipts.document_number' => 'Document Number',
      'tb_doc_receipts.received_date'   => 'Received Date',
      'tb_doc_receipts.received_from'   => 'Vendor',
      'tb_doc_receipts.category'        => 'Category',
      'tb_doc_receipts.notes'           => 'notes',
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'tb_doc_receipts.document_number',
      'tb_doc_receipts.received_from',
      'tb_doc_receipts.category',
      'tb_doc_receipts.notes',
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'tb_doc_receipts.document_number',
      'tb_doc_receipts.received_date',
      'tb_doc_receipts.received_from',
      'tb_doc_receipts.category',
      'tb_doc_receipts.notes',
    );
  }

  private function searchIndex()
  {
    if (!empty($_POST['columns'][2]['search']['value'])){
      $search_received_date = $_POST['columns'][2]['search']['value'];
      $range_received_date  = explode(' ', $search_received_date);

      $this->db->where('tb_doc_receipts.received_date >= ', $range_received_date[0]);
      $this->db->where('tb_doc_receipts.received_date <= ', $range_received_date[1]);
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
    $this->db->from('tb_doc_receipts');

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
    $this->db->from('tb_doc_receipts');

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->from('tb_doc_receipts');

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function find($select, $id)
  {
    $this->db->select($select);
    $this->db->where('id', $id);
    $query = $this->db->get('tb_doc_receipts');
    $row = $query->row_array();

    return $row[$select];
  }

  public function findById($id)
  {
    $this->db->where('id', $id);

    $query  = $this->db->get('tb_doc_receipts');
    $row    = $query->unbuffered_row('array');

    $this->db->select('tb_stocks.*, tb_doc_receipt_items.stores, tb_doc_receipt_items.received_quantity, tb_doc_receipt_items.notes, tb_master_items.description, tb_master_items.unit, tb_master_items.group, tb_master_items.alternate_part_number');
    $this->db->from('tb_doc_receipt_items');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_doc_receipt_items.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->where('tb_doc_receipt_items.document_number', $row['document_number']);

    $query = $this->db->get();

    foreach ($query->result_array() as $key => $value){
      $row['items'][$key] = $value;

      if (empty($row['category'])){
        $this->db->select('category');
        $this->db->from('tb_master_item_groups');
        $this->db->where('group', $value['group']);

        $query = $this->db->get();
        $icat  = $query->unbuffered_row();

        $row['category'] = $icat->category;
      }
    }

    return $row;
  }

  public function isDocumentNumberExists($document_number)
  {
    $this->db->where('document_number', $document_number);
    $query = $this->db->get('tb_doc_receipts');

    if ($query->num_rows() > 0)
      return true;

    return false;
  }

  public function save()
  {
    $document_id      = (isset($_SESSION['receipt']['id'])) ? $_SESSION['receipt']['id'] : NULL;
    $document_edit    = (isset($_SESSION['receipt']['edit'])) ? $_SESSION['receipt']['edit'] : NULL;
    $document_number  = $_SESSION['receipt']['document_number'] . receipt_format_number();
    $received_date    = $_SESSION['receipt']['received_date'];
    $received_by      = $_SESSION['receipt']['received_by'];
    $received_from    = $_SESSION['receipt']['received_from'];
    $category         = $_SESSION['receipt']['category'];
    $notes            = $_SESSION['receipt']['notes'];
    $warehouse        = config_item('auth_warehouse');

    $this->db->trans_begin();

    if ($document_id === NULL){
      if (!empty($received_from))
        $this->db->set('received_from', $received_from);

      $this->db->set('document_number', $document_number);
      $this->db->set('received_date', $received_date);
      $this->db->set('received_by', $received_by);
      $this->db->set('category', $category);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('notes', $notes);
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->insert('tb_doc_receipts');
    } else {
      /**
       * CREATE ITEM LOGS
       * decrease quantity
       * create document revision
       */
      $this->db->from('tb_stock_cards');
      $this->db->where('document_type', 'RECEIPT');
      $this->db->where('document_number', $document_edit);

      $query = $this->db->get();

      foreach ($query->result_array() as $row) {
        if (!empty($row['serial_number']))
          $this->db->set('serial_number', $row['serial_number']);

        $this->db->set('part_number', $row['part_number']);
        $this->db->set('warehouse', $warehouse);
        $this->db->set('stores', $row['stores']);
        $this->db->set('date_of_entry', $received_date);
        $this->db->set('document_type', 'REVISION');
        $this->db->set('document_number', $document_edit);
        $this->db->set('issued_to', $document_number);
        $this->db->set('issued_by', config_item('auth_person_name'));
        $this->db->set('condition', $row['condition']);
        $this->db->set('quantity', 0 - floatval($row['quantity']));
        $this->db->set('remarks', 'REVISION');
		$this->db->set('created_by', config_item('auth_person_name'));
        $this->db->insert('tb_stock_cards');
      }

      /**
       * CREATE RECEIPT DOCUMENT
       */
      if (!empty($received_from))
        $this->db->set('received_from', $received_from);

      $this->db->set('document_number', $document_number);
      $this->db->set('received_date', $received_date);
      $this->db->set('received_by', $received_by);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('category', $category);
      $this->db->set('notes', "Document Revised from ". $document_edit ."\n\n". $notes);
      $this->db->set('updated_at', date('Y-m-d'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->where('id', $document_id);
      $this->db->update('tb_doc_receipts');

      /**
       * DELETE OLD RECEIPT ITEMS
       */
      $this->db->where('document_number', $document_edit);
      $this->db->delete('tb_doc_receipt_items');

      /**
       * DELETE OLD ITEM IN STORES
       */
      $this->db->where('document_number', $document_edit);
      $this->db->delete('tb_stocks');
    }

    /**
     * PROCESSING RECEIPT ITEMS
     */
    foreach ($_SESSION['receipt']['items'] as $key => $data){
      /**
       * CREATE UNIT OF MEASUREMENT IF NOT EXISTS
       */
      if ($this->isItemUnitExists($data['unit']) === FALSE){
        $this->db->set('unit', strtoupper($data['unit']));
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->insert('tb_master_item_units');
      }

      /**
       * CREATE STORES IF NOT EXISTS
       */
      if ($this->isStoresExists($data['stores']) === FALSE){
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
      if ($this->isItemExists($data['part_number']) === FALSE){
        $this->db->set('part_number', strtoupper($data['part_number']));
        $this->db->set('alternate_part_number', strtoupper($data['alternate_part_number']));
        $this->db->set('description', strtoupper($data['description']));
        $this->db->set('group', strtoupper($data['group']));
        $this->db->set('minimum_quantity', floatval($data['minimum_quantity']));
        $this->db->set('unit', strtoupper($data['unit']));
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->insert('tb_master_items');
      }

      /**
       * CREATE SERIAL NUMBER IF NOT EXISTS
       */
      if (!empty($data['serial_number']) && !$this->isSerialNumberExists($data['serial_number'])){
        $this->db->set('part_number', strtoupper($data['part_number']));
        $this->db->set('serial_number', strtoupper($data['serial_number']));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->insert('tb_master_item_serials');
      }

      /**
       * ADD ITEM INTO STOCK
       */
      if (!empty($data['serial_number']))
        $this->db->set('serial_number', strtoupper($data['serial_number']));

      if (!empty($data['order_number']))
        $this->db->set('order_number', strtoupper($data['order_number']));

      if (!empty($data['expired_date']))
        $this->db->set('expired_date', $data['expired_date']);

      if (!empty($data['reference_number']))
        $this->db->set('reference_number', strtoupper($data['reference_number']));

      if (!empty($data['awb_number']))
        $this->db->set('awb_number', strtoupper($data['awb_number']));

      $this->db->set('document_number', $document_number);
      $this->db->set('part_number', strtoupper($data['part_number']));
      $this->db->set('condition', strtoupper($data['condition']));
      $this->db->set('unit_value', floatval($data['unit_value']));
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->insert('tb_stocks');

      $stock_id = $this->db->insert_id();

      if (!empty($data['remarks']))
        $this->db->set('remarks', $data['remarks']);

      $this->db->set('stock_id', $stock_id);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('stores', strtoupper($data['stores']));
      $this->db->set('quantity', floatval($data['received_quantity']));
      $this->db->set('reference_document', $document_number);
      $this->db->set('received_date', $received_date);
      $this->db->set('received_by', $received_by);
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->insert('tb_stock_in_stores');

      /**
       * INSERT INTO RECEIPT ITEMS
       */
      if (!empty($data['serial_number']))
        $this->db->set('serial_number', $data['serial_number']);

      if (!empty($data['remarks']))
        $this->db->set('remarks', $data['remarks']);

      $this->db->set('document_number', $document_number);
      $this->db->set('stock_id', $stock_id);
      $this->db->set('part_number', strtoupper($data['part_number']));
      $this->db->set('condition', strtoupper($data['condition']));
      $this->db->set('stores', strtoupper($data['stores']));
      $this->db->set('received_quantity', floatval($data['received_quantity']));
      $this->db->insert('tb_doc_receipt_items');

      /**
       * CREATE ITEM LOGS
       *
       * create new document to stores item
       * it will be increase the quantity
       */
      if (!empty($data['serial_number']))
        $this->db->set('serial_number', strtoupper($data['serial_number']));

      if (!empty($received_from))
        $this->db->set('received_from', $received_from);

      if (!empty($data['remarks']))
        $this->db->set('remarks', $data['remarks']);

      $this->db->set('part_number', strtoupper($data['part_number']));
      $this->db->set('warehouse', $warehouse);
      $this->db->set('stores', strtoupper($data['stores']));
      $this->db->set('date_of_entry', $received_date);
      $this->db->set('document_type', 'RECEIPT');
      $this->db->set('document_number', $document_number);
      $this->db->set('received_by', $received_by);
      $this->db->set('condition', strtoupper($data['condition']));
      $this->db->set('quantity', floatval($data['received_quantity']));
	  $this->db->set('created_by', config_item('auth_person_name'));
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

    $this->db->select('document_number');
    $this->db->where('id', $id);
    $this->db->from('tb_doc_receipts');

    $query = $this->db->get();
    $row   = $query->unbuffered_row();
    $document_number = $row->document_number;

    $this->db->where('tb_doc_receipt_items.document_number', $document_number);
    $this->db->from('tb_doc_receipt_items');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_doc_receipt_items.stock_id');

    $query  = $this->db->get();
    $result = $query->result_array();

    foreach ($result as $data) {
      if (!empty($data['serial_number']))
        $this->db->set('serial_number', $data['serial_number']);

      if (!empty($data['remarks']))
        $this->db->set('remarks', $data['remarks']);

      $this->db->set('part_number', $data['part_number']);
      $this->db->set('warehouse', config_item('auth_warehouse'));
      $this->db->set('stores', $data['stores']);
      $this->db->set('date_of_entry', date('Y-m-d'));
      $this->db->set('document_type', 'REMOVAL');
      $this->db->set('document_number', $document_number);
      $this->db->set('issued_to', 'DELETE DOCUMENT');
      $this->db->set('issued_by', config_item('auth_person_name'));
      $this->db->set('condition', $data['condition']);
      $this->db->set('quantity', 0 - floatval($data['received_quantity']));
	  $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->insert('tb_stock_cards');
    }

    $this->db->where('document_number', $document_number);
    $this->db->delete('tb_doc_receipt_items');

    $this->db->where('document_number', $document_number);
    $this->db->delete('tb_stocks');

    $this->db->where('id', $id);
    $this->db->delete('tb_doc_receipts');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }
}
