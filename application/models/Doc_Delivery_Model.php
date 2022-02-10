<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Doc_Delivery_Model extends MY_Model
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = config_item('module')['doc_delivery'];
  }

  public function getSelectedColumns()
  {
    return array(
      'tb_doc_deliveries.id'              => NULL,
      'tb_doc_deliveries.document_number' => 'Document Number',
      'tb_doc_deliveries.received_date'   => 'Received Date',
      'tb_doc_deliveries.received_from'   => 'Received From',
      'tb_doc_deliveries.sent_by'         => 'Sent By',
      'tb_doc_deliveries.category'        => 'Category',
      'tb_doc_deliveries.warehouse'       => 'Warehouse',
      'tb_doc_deliveries.notes'           => 'Notes',
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'tb_doc_deliveries.document_number',
      'tb_doc_deliveries.received_from',
      'tb_doc_deliveries.sent_by',
      'tb_doc_deliveries.category',
      'tb_doc_deliveries.warehouse',
      'tb_doc_deliveries.notes',
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'tb_doc_deliveries.document_number',
      'tb_doc_deliveries.received_date',
      'tb_doc_deliveries.received_from',
      'tb_doc_deliveries.sent_by',
      'tb_doc_deliveries.category',
      'tb_doc_deliveries.warehouse',
      'tb_doc_deliveries.notes',
    );
  }

  private function searchIndex()
  {
    if (!empty($_POST['columns'][2]['search']['value'])){
      $search_received_date = $_POST['columns'][2]['search']['value'];
      $range_received_date  = explode(' ', $search_received_date);

      $this->db->where('tb_doc_deliveries.received_date >= ', $range_received_date[0]);
      $this->db->where('tb_doc_deliveries.received_date <= ', $range_received_date[1]);
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
    $this->db->from('tb_doc_deliveries');

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
    $this->db->from('tb_doc_deliveries');

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->from('tb_doc_deliveries');

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function findById($id)
  {
    $this->db->where('id', $id);

    $query  = $this->db->get('tb_doc_deliveries');
    $row    = $query->unbuffered_row('array');
    $select = array(
      'tb_stock_in_stores.*',
      'tb_doc_delivery_items.condition',
      'tb_doc_delivery_items.stores',
      'tb_doc_delivery_items.received_quantity',
      'tb_doc_delivery_items.remarks',
      'tb_master_item_serials.serial_number',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.alternate_part_number',
      'tb_master_items.unit',
      'tb_master_items.group',
    );

    $this->db->select($select);
    $this->db->from('tb_doc_delivery_items');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_doc_delivery_items.stock_in_stores_id');
    $this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_doc_delivery_items.serial_id', 'left');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_doc_delivery_items.item_id');
    $this->db->where('tb_doc_delivery_items.document_number', $row['document_number']);

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
    $query = $this->db->get('tb_doc_deliveries');

    if ($query->num_rows() > 0)
      return true;

    return false;
  }

  public function save()
  {
    $document_id      = (isset($_SESSION['delivery']['id'])) ? $_SESSION['delivery']['id'] : NULL;
    $document_edit    = (isset($_SESSION['delivery']['edit'])) ? $_SESSION['delivery']['edit'] : NULL;
    $document_number  = $_SESSION['delivery']['document_number'] . delivery_format_number();
    $received_date    = $_SESSION['delivery']['received_date'];
    $received_by      = $_SESSION['delivery']['received_by'];
    $received_from    = $_SESSION['delivery']['received_from'];
    $sent_by          = $_SESSION['delivery']['sent_by'];
    $approved_by      = $_SESSION['delivery']['approved_by'];
    $warehouse        = $_SESSION['delivery']['warehouse'];
    $category         = $_SESSION['delivery']['category'];
    $notes            = $_SESSION['delivery']['notes'];

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
      $this->db->insert('tb_doc_deliveries');
    } else {
      /**
       * EDIT DOCUMENT
       * decrease quantity
       * create document revision
       */
      $this->db->from('tb_stock_cards');
      $this->db->where('document_type', 'DELIVERY');
      $this->db->where('document_number', $document_edit);

      $query = $this->db->get();

      foreach ($query->result_array() as $row) {
        $this->db->set('stock_id', $row['stock_id']);
        $this->db->set('serial_id', $row['serial_id']);
        $this->db->set('warehouse', $warehouse);
        $this->db->set('stores', $row['stores']);
        $this->db->set('date_of_entry', $received_date);
        $this->db->set('period_year', config_item('period_year'));
        $this->db->set('period_month', config_item('period_month'));
        $this->db->set('document_type', 'REVISION');
        $this->db->set('document_number', $document_edit);
        $this->db->set('issued_to', $document_number);
        $this->db->set('issued_by', config_item('auth_person_name'));
        $this->db->set('quantity', 0 - floatval($row['quantity']));
        $this->db->set('remarks', 'REVISION');
		$this->db->set('created_by', config_item('auth_person_name'));
        $this->db->insert('tb_stock_cards');
      }

      /**
       * CREATE DELIVERY DOCUMENT
       */
      if (!empty($received_from))
        $this->db->set('received_from', $received_from);

      if (!empty($approved_by))
        $this->db->set('approved_by', $approved_by);

      $this->db->set('document_number', $document_number);
      $this->db->set('received_date', $received_date);
      $this->db->set('received_by', $received_by);
      $this->db->set('sent_by', $sent_by);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('category', $category);
      $this->db->set('notes', "Document Revised from ". $document_edit ."\n\n". $notes);
      $this->db->set('updated_at', date('Y-m-d'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->where('id', $document_id);
      $this->db->update('tb_doc_deliveries');

      /**
       * DELETE OLD DELIVERY ITEMS
       */
      $this->db->where('document_number', $document_edit);
      $this->db->delete('tb_doc_delivery_items');

      /**
       * DELETE OLD STOCK
       */
      $this->db->where('reference_document', $document_edit);
      $this->db->delete('tb_stock_in_stores');
    }

    /**
     * PROCESSING DELIVERY ITEMS
     */
    foreach ($_SESSION['delivery']['items'] as $key => $data){
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
      if (isItemExists($data['part_number'],$data['description']) === FALSE){
        $this->db->set('part_number', strtoupper($data['part_number']));
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
        $item_id = getItemId($data['part_number'],$data['description']);
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
      $this->db->set('stock_id', $stock_id);
      $this->db->set('serial_id', $serial_id);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('stores', strtoupper($data['stores']));
      $this->db->set('initial_quantity', floatval($data['received_quantity']));
      $this->db->set('initial_unit_value', floatval($data['unit_value']));
      $this->db->set('quantity', floatval($data['received_quantity']));
      $this->db->set('unit_value', floatval($data['unit_value']));
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
      $this->db->set('document_number', $document_number);
      $this->db->set('remarks', $data['remarks']);
      $this->db->set('serial_id', $serial_id);
      $this->db->set('stock_in_stores_id', $stock_in_stores_id);
      $this->db->set('item_id', $item_id);
      $this->db->set('condition', strtoupper($data['condition']));
      $this->db->set('stores', strtoupper($data['stores']));
      $this->db->set('received_quantity', floatval($data['received_quantity']));
      $this->db->set('unit_value', floatval($data['unit_value']));
      $this->db->insert('tb_doc_delivery_items');

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
      $this->db->set('document_type', 'DELIVERY');
      $this->db->set('document_number', $document_number);
      $this->db->set('received_from', $received_from);
      $this->db->set('received_by', $received_by);
      $this->db->set('quantity', floatval($data['received_quantity']));
      $this->db->set('unit_value', floatval($data['unit_value']));
      $this->db->set('remarks', $data['remarks']);
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

    $this->db->select('document_number, warehouse');
    $this->db->where('id', $id);
    $this->db->from('tb_doc_deliveries');

    $query = $this->db->get();
    $row   = $query->unbuffered_row();
    $document_number = $row->document_number;
    $warehouse = $row->warehouse;

    $this->db->select('tb_doc_delivery_items.*, tb_stock_in_stores.stock_id');
    $this->db->where('tb_doc_delivery_items.document_number', $document_number);
    $this->db->from('tb_doc_delivery_items');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_doc_delivery_items.stock_in_stores_id');

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
      $this->db->set('quantity', 0 - floatval($data['received_quantity']));
      $this->db->set('unit_value', floatval($data['unit_value']));
	  $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->insert('tb_stock_cards');

      $this->db->where('id', $data['id']);
      $this->db->delete('tb_doc_delivery_items');

      $this->db->where('id', $data['stock_in_stores_id']);
      $this->db->delete('tb_stock_in_stores');
    }

    $this->db->where('id', $id);
    $this->db->delete('tb_doc_deliveries');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function searchItemsBySerial($category)
  {
    $this->column_select = array(
      'tb_master_item_serials.serial_number',
      'tb_master_items.id',
      'tb_master_items.group',
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.alternate_part_number',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit'
     );

    $this->db->select($this->column_select);
    $this->db->from('tb_master_item_serials');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_master_item_serials.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_master_item_serials.quantity', 0);
    $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
    $this->db->where('tb_master_item_groups.category', $category);

    $this->db->order_by('tb_master_item_serials.serial_number ASC');

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
      'tb_master_items.unit'
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
