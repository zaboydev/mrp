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
      'tb_issuances.id'                       => NULL,
      'tb_issuances.document_number'          => 'Document Number',
      'tb_issuances.issued_date'              => 'Date',
      'tb_issuances.category'                 => 'Category',
      'tb_issuances.warehouse'                => 'Base',
      'tb_master_items.description'           => 'Description',
      'tb_master_items.part_number'           => 'Part Number',
      'tb_master_item_serials.serial_number'  => 'Serial Number',
      'tb_stocks.condition'                   => 'Condition',
      'tb_issuance_items.issued_quantity'     => 'Quantity',
      'tb_master_items.unit'                  => 'Unit',
      'tb_issuance_items.awb_number'          => 'AWB Number',
      'tb_issuance_items.remarks'             => 'Remarks',
      'tb_issuances.issued_to'                => 'Sent To',
      'tb_issuances.issued_by'                => 'Released By',
      'tb_receipts.received_from'                => 'Supplier',
    );

    if (config_item('auth_role') != 'PIC STOCK'){
      $selected['tb_issuance_items.issued_unit_value']  = 'Value';
      $selected['tb_issuance_items.issued_total_value'] = 'Total Value';
    }
  
    if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE'){
        $selected['tb_master_items.kode_pemakaian']    = 'Biaya Pemakaian';
    }

    return $selected;
  }

  public function getSearchableColumns()
  {
    return array(
      'tb_issuances.document_number',
      'tb_issuances.category',
      'tb_issuances.warehouse',
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_item_serials.serial_number',
      'tb_stocks.condition',
      'tb_master_items.unit',
      'tb_issuance_items.awb_number',
      'tb_issuance_items.remarks',
      'tb_issuances.issued_to',
      'tb_issuances.issued_by',
      'tb_receipts.received_from'
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'tb_issuances.document_number',
      'tb_issuances.issued_date',
      'tb_issuances.category',
      'tb_issuances.warehouse',
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_item_serials.serial_number',
      'tb_stocks.condition',
      'tb_issuance_items.issued_quantity',
      'tb_master_items.unit',
      'tb_issuance_items.awb_number',
      'tb_issuance_items.remarks',
      'tb_issuances.issued_to',
      'tb_issuances.issued_by',
      'tb_receipts.received_from'
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
    $this->db->join('tb_issuance_items', 'tb_issuance_items.document_number = tb_issuances.document_number');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_issuance_items.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_in_stores.serial_id', 'left');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_receipts', 'tb_stock_in_stores.reference_document = tb_receipts.document_number');
    $this->db->where_in('tb_issuances.category', config_item('auth_inventory'));
    $this->db->where_in('tb_issuances.warehouse', config_item('auth_warehouses'));
    $this->db->like('tb_issuances.document_number', 'CI');

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
    $this->db->join('tb_issuance_items', 'tb_issuance_items.document_number = tb_issuances.document_number');
    $this->db->where_in('tb_issuances.category', config_item('auth_inventory'));
    $this->db->where_in('tb_issuances.warehouse', config_item('auth_warehouses'));
    $this->db->like('tb_issuances.document_number', 'CI');

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->from('tb_issuances');
    $this->db->join('tb_issuance_items', 'tb_issuance_items.document_number = tb_issuances.document_number');
    $this->db->where_in('tb_issuances.category', config_item('auth_inventory'));
    $this->db->where_in('tb_issuances.warehouse', config_item('auth_warehouses'));
    $this->db->like('tb_issuances.document_number', 'CI');

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
      'tb_master_item_serials.serial_number',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.alternate_part_number',
      'tb_master_items.unit',
      'tb_master_items.group',
    );

    $this->db->select($select);
    $this->db->from('tb_issuance_items');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_issuance_items.stock_in_stores_id');
    $this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_in_stores.serial_id', 'left');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->where('tb_issuance_items.document_number', $issued['document_number']);

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
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->insert('tb_issuances');
    } else {
      /**
       * EDIT DOCUMENT
       * decrease quantity
       * create document revision
       */
      $this->db->from('tb_stock_cards');
      $this->db->where('document_type', 'RETURN');
      $this->db->where('document_number', $document_edit);

      $query = $this->db->get();

      foreach ($query->result_array() as $row) {
        $this->db->set('stock_id', $row['stock_id']);
        $this->db->set('serial_id', $row['serial_id']);
        $this->db->set('warehouse', $warehouse);
        $this->db->set('stores', $row['stores']);
        $this->db->set('date_of_entry', $issued_date);
        $this->db->set('period_year', config_item('period_year'));
        $this->db->set('period_month', config_item('period_month'));
        $this->db->set('document_type', 'REVISION');
        $this->db->set('document_number', $document_edit);
        $this->db->set('issued_to', $document_number);
        $this->db->set('issued_by', config_item('auth_person_name'));
        $this->db->set('quantity', 0 + floatval($row['quantity']));
        $this->db->set('unit_value', floatval($row['unit_value']));
        $this->db->set('remarks', 'REVISION');
		    $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->insert('tb_stock_cards');
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
      $this->db->set('updated_at', date('Y-m-d'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->where('id', $document_id);
      $this->db->update('tb_issuances');

      /**
       * DELETE OLD RETURN ITEMS
       */
      $this->db->where('document_number', $document_edit);
      $this->db->delete('tb_issuance_items');

      /**
       * DELETE OLD STOCK
       */
      $this->db->where('reference_document', $document_edit);
      $this->db->delete('tb_stock_in_stores');

      /**
       * UPDATE SERIAL
       */
      $this->db->where('reference_document', $document_edit);
      $this->db->set('quantity', 1);
      $this->db->delete('tb_master_item_serials');
    }

    /**
     * PROCESSING RETURN ITEMS
     */
    foreach ($_SESSION['return']['items'] as $key => $data){
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
       * INSERT INTO RETURN ITEMS
       */
      $this->db->set('document_number', $document_number);
      $this->db->set('stock_in_stores_id', $stock_in_stores_id);
      $this->db->set('issued_quantity', floatval($data['issued_quantity']));
      $this->db->set('issued_unit_value', floatval($data['issued_unit_value']));
      $this->db->set('issued_total_value', floatval($data['issued_unit_value']) * floatval($data['issued_quantity']));
      $this->db->set('insurance_unit_value', floatval($data['insurance_unit_value']));
      $this->db->set('insurance_currency', $data['insurance_currency']);
      $this->db->set('awb_number', $data['awb_number']);
      $this->db->set('remarks', $data['remarks']);
      $this->db->insert('tb_issuance_items');

      /**
       * CREATE STOCK CARD
       */
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
      $this->db->set('remarks', $data['remarks']);
	    $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->set('stock_in_stores_id', $stock_in_stores_id);
      $this->db->set('doc_type', 10);
      $this->db->set('tgl', date('Ymd',strtotime($issued_date)));
      $this->db->set('total_value', floatval($data['issued_unit_value'])*(0 - floatval($data['issued_quantity'])));
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
	  $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->insert('tb_stock_cards');

      $this->db->where('id', $data['id']);
      $this->db->delete('tb_issuance_items');

      $this->db->where('reference_document', $document_number);
      $this->db->delete('tb_stock_in_stores');
    }

    $this->db->where('id', $id);
    $this->db->delete('tb_issuances');

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
    );

    $this->db->select($this->column_select);
    $this->db->from('tb_stock_in_stores');
    $this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_in_stores.serial_id', 'left');
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
}
