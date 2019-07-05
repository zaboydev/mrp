<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Doc_Shipment_Model extends MY_Model
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = config_item('doc_shipment_table');
  }

  public function getSelectedColumns()
  {
    return array(
      'tb_doc_shipments.id'               => NULL,
      'tb_doc_shipments.document_number'  => 'Document Number',
      'tb_doc_shipments.warehouse'        => 'Origin',
      'tb_doc_shipments.issued_to'        => 'Destination',
      'tb_doc_shipments.issued_date'      => 'Sent Date',
      'tb_doc_shipments.issued_by'        => 'Released By',
      'tb_doc_shipments.sent_by'          => 'Sent By',
      'tb_doc_shipments.category'         => 'Category',
      'tb_doc_shipments.notes'            => 'notes',
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'tb_doc_shipments.document_number',
      'tb_doc_shipments.warehouse',
      'tb_doc_shipments.issued_to',
      'tb_doc_shipments.issued_by',
      'tb_doc_shipments.sent_by',
      'tb_doc_shipments.category',
      'tb_doc_shipments.notes',
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'tb_doc_shipments.document_number',
      'tb_doc_shipments.issued_date',
      'tb_doc_shipments.warehouse',
      'tb_doc_shipments.issued_to',
      'tb_doc_shipments.issued_date',
      'tb_doc_shipments.issued_by',
      'tb_doc_shipments.sent_by',
      'tb_doc_shipments.category',
      'tb_doc_shipments.notes',
    );
  }

  private function searchIndex()
  {
    if (!empty($_POST['columns'][2]['search']['value'])){
      $search_issued_date = $_POST['columns'][2]['search']['value'];
      $range_issued_date  = explode(' ', $search_issued_date);

      $this->db->where('tb_doc_shipments.issued_date >= ', $range_issued_date[0]);
      $this->db->where('tb_doc_shipments.issued_date <= ', $range_issued_date[1]);
    }

    if (!empty($_POST['columns'][5]['search']['value']))
      $this->db->where('tb_item_quantity_details.serial_number != ', '');

    if (!empty($_POST['columns'][6]['search']['value'])){
      $search_condition = $_POST['columns'][6]['search']['value'];
      $this->db->where('tb_item_quantity_details.condition ', $search_condition);
    }

    if (!empty($_POST['columns'][7]['search']['value'])){
      $search_unit_value = $_POST['columns'][7]['search']['value'];
      if ($search_unit_value == 'zero'){
        $this->db->where('tb_item_quantity_details.unit_value', '0.00');
      } else {
        $this->db->where('tb_item_quantity_details.unit_value > ', '0.00');
      }
    }

    if (!empty($_POST['columns'][8]['search']['value'])){
      $search_quantity = $_POST['columns'][8]['search']['value'];
      if ($search_quantity == 'zero'){
        $this->db->where('tb_item_quantity_details.quantity', '0.00');
      } else {
        $this->db->where('tb_item_quantity_details.quantity > ', '0.00');
      }
    }

    if (!empty($_POST['columns'][10]['search']['value'])){
      $search_total = $_POST['columns'][10]['search']['value'];
      if ($search_total == 'zero'){
        $this->db->where('total', '0.00');
      } else {
        $this->db->where('total > ', '0.00');
      }
    }

    if (!empty($_POST['columns'][11]['search']['value'])){
      $search_warehouse = $_POST['columns'][11]['search']['value'];
      $this->db->where('tb_doc_shipments.warehouse ', $search_warehouse);
    }

    if (!empty($_POST['columns'][12]['search']['value'])){
      $search_issued_to = $_POST['columns'][12]['search']['value'];
      $this->db->where('tb_doc_shipments.issued_to ', $search_issued_to);
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
    $this->db->from('tb_doc_shipments');
    $this->db->where_in('tb_doc_shipments.category', config_item('auth_inventory'));
    $this->db->where_in('tb_doc_shipments.warehouse', config_item('auth_warehouses'));
    $this->db->or_where_in('tb_doc_shipments.issued_to', config_item('auth_warehouses'));

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
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_doc_shipments');
    $this->db->where_in('tb_doc_shipments.category', config_item('auth_inventory'));
    $this->db->where_in('tb_doc_shipments.warehouse', config_item('auth_warehouses'));
    $this->db->or_where_in('tb_doc_shipments.issued_to', config_item('auth_warehouses'));

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_doc_shipments');
    $this->db->where_in('tb_doc_shipments.category', config_item('auth_inventory'));
    $this->db->where_in('tb_doc_shipments.warehouse', config_item('auth_warehouses'));
    $this->db->or_where_in('tb_doc_shipments.issued_to', config_item('auth_warehouses'));

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function findById($id)
  {
    $selected_columns = array(
      'tb_doc_shipments.*',
      'origin.address as origin_address',
      'destination.address as destination_address',
    );

    $this->db->select($selected_columns);
    $this->db->where('tb_doc_shipments.id', $id);
    $this->db->from('tb_doc_shipments');
    $this->db->join('tb_master_warehouses origin', 'origin.warehouse = tb_doc_shipments.warehouse');
    $this->db->join('tb_master_warehouses destination', 'destination.warehouse = tb_doc_shipments.issued_to');
    $query = $this->db->get();
    $row = $query->unbuffered_row('array');

    $selected_columns_join = array(
      'tb_doc_shipment_items.*',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.unit',
      'tb_master_items.group',
      'tb_master_items.alternate_part_number'
    );

    $this->db->select($selected_columns_join);
    $this->db->from('tb_doc_shipment_items');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_doc_shipment_items.item_id');
    $this->db->where('tb_doc_shipment_items.document_number', $row['document_number']);
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
    $query = $this->db->get('tb_doc_shipments');

    if ($query->num_rows() > 0)
      return true;

    return false;
  }

  public function save()
  {
    $document_id      = (isset($_SESSION['shipment']['id'])) ? $_SESSION['shipment']['id'] : NULL;
    $document_edit    = (isset($_SESSION['shipment']['edit'])) ? $_SESSION['shipment']['edit'] : NULL;
    $document_number  = $_SESSION['shipment']['document_number'] . shipment_format_number();
    $issued_date      = $_SESSION['shipment']['issued_date'];
    $issued_to        = $_SESSION['shipment']['issued_to'];
    $issued_by        = $_SESSION['shipment']['issued_by'];
    $sent_by          = $_SESSION['shipment']['sent_by'];
    $approved_by      = $_SESSION['shipment']['approved_by'];
    $category         = $_SESSION['shipment']['category'];
    $warehouse        = $_SESSION['shipment']['warehouse'];
    $notes            = $_SESSION['shipment']['notes'];

    $this->db->trans_begin();

    if ($document_id === NULL){
      $this->db->set('document_number', strtoupper($document_number));
      $this->db->set('issued_date', $issued_date);
      $this->db->set('issued_to', $issued_to);
      $this->db->set('issued_by', $issued_by);
      $this->db->set('sent_by', $sent_by);
      $this->db->set('approved_by', $approved_by);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('category', $category);
      $this->db->set('notes', $notes);
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->insert('tb_doc_shipments');
    } else {
      $this->db->set('document_number', strtoupper($document_number));
      $this->db->set('issued_date', $issued_date);
      $this->db->set('issued_to', $issued_to);
      $this->db->set('issued_by', $issued_by);
      $this->db->set('sent_by', $sent_by);
      $this->db->set('approved_by', $approved_by);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('category', $category);
      $this->db->set('notes', $notes ."\n\nDocument Revised from ". $document_edit);
      $this->db->set('updated_at', date('Y-m-d'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->where('id', $document_id);
      $this->db->update('tb_doc_shipments');

      // add back item to stores
      $this->db->from('tb_doc_shipment_items');
      $this->db->where('document_number', strtoupper($document_edit));
      $query = $this->db->get();

      foreach ($query->result_array() as $row) {
        $this->db->set('quantity', 'quantity + '. floatval($row['issued_quantity']), FALSE);
        $this->db->where('id', $row['stock_stores_id']);
        $this->db->update('tb_stock_in_stores');
      }

      $this->db->where('document_number', strtoupper($document_edit));
      $this->db->delete('tb_doc_shipment_items');

      // receive back item logs
      $this->db->from('tb_stock_cards');
      $this->db->where('document_type', 'SD');
      $this->db->where('document_number', $document_edit);

      $query = $this->db->get();

      foreach ($query->result_array() as $row) {
        if (!empty($row['serial_number']))
          $this->db->set('serial_number', strtoupper($row['serial_number']));

        $this->db->set('part_number', $row['part_number']);
        $this->db->set('warehouse', $row['warehouse']);
        $this->db->set('stores', $row['stores']);
        $this->db->set('date_of_entry', $issued_date);
        $this->db->set('document_type', 'EDIT');
        $this->db->set('document_number', $document_edit);
        $this->db->set('received_from', 'EDIT DOCUMENT');
        $this->db->set('received_by', config_item('auth_person_name'));
        $this->db->set('condition', $row['condition']);
        $this->db->set('quantity', floatval($row['quantity']));
        $this->db->set('unit_value', floatval($row['unit_value']));
        $this->db->set('remarks', $document_edit .' to '. $document_number .' on '. date('d/m/Y H:i') .' //'. $row['remarks']);
		$this->db->set('created_by', config_item('auth_person_name'));
        $this->db->insert('tb_stock_cards');
      }
    }

    foreach ($_SESSION['shipment']['items'] as $key => $data){
      // update stock stores
      $this->db->set('quantity', 'quantity - '. $data['issued_quantity'], FALSE);
      $this->db->where('id', $data['stock_stores_id']);
      $this->db->update('tb_stock_in_stores');

      //... document items
      if (!empty($data['serial_number']))
        $this->db->set('serial_number', $data['serial_number']);

      if (!empty($data['remarks']))
        $this->db->set('remarks', $data['remarks']);

      $this->db->set('document_number', $document_number);
      $this->db->set('part_number', $data['part_number']);
      $this->db->set('condition', $data['condition']);
      $this->db->set('issued_quantity', floatval($data['issued_quantity']));
      $this->db->set('unit_value', floatval($data['unit_value']));
      $this->db->set('estimated_unit_value', floatval($data['estimated_unit_value']));
      $this->db->set('stock_stores_id', intval($data['stock_stores_id']));
      $this->db->insert('tb_doc_shipment_items');

      //... item logs
      if (!empty($data['serial_number']))
        $this->db->set('serial_number', strtoupper($data['serial_number']));

      if (!empty($data['remarks']))
        $this->db->set('remarks', $data['remarks']);

      $this->db->set('part_number', strtoupper($data['part_number']));
      $this->db->set('warehouse', $warehouse);
      $this->db->set('stores', strtoupper($data['stores']));
      $this->db->set('date_of_entry', $issued_date);
      $this->db->set('document_type', 'SD');
      $this->db->set('document_number', $document_number);
      $this->db->set('issued_to', $issued_to);
      $this->db->set('issued_by', $issued_by);
      $this->db->set('condition', strtoupper($data['condition']));
      $this->db->set('quantity', 0 - floatval($data['issued_quantity']));
      $this->db->set('unit_value', floatval($data['unit_value']));
	  $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->insert('tb_stock_cards');
    }

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function receive_save($id)
  {
    $this->db->trans_begin();

    // update shipping document
    $this->db->set('received_date', $this->input->post('received_date'));
    $this->db->set('received_by', config_item('auth_person_name'));
    $this->db->where('id', $id);
    $this->db->update('tb_doc_shipments');

    // update items
    foreach ($this->input->post('item') as $item_id => $data){
      if (!empty($data['notes']))
        $this->db->set('notes', $data['notes']);

      $this->db->set('quantity_received', $data['quantity_received']);
      $this->db->where('id', $item_id);
      $this->db->update('tb_doc_shipment_items');

      $this->db->select('reference_id');
      $this->db->from('tb_doc_shipment_items');
      $this->db->where('id', $item_id);
      $query = $this->db->get();
      $row = $query->unbuffered_row('array');
      $reference_id = $row['reference_id'];

      $this->db->from('tb_stocks');
      $this->db->where('id', $reference_id);
      $query = $this->db->get();
      $item_in_stores = $query->unbuffered_row('array');

      // insert item in stores
      if (!empty($item_in_stores['serial_number']))
        $this->db->set('serial_number', $item_in_stores['serial_number']);

      if (!empty($data['remarks']))
        $this->db->set('remarks', $data['remarks']);

      $this->db->set('part_number', $item_in_stores['part_number']);
      $this->db->set('condition', $item_in_stores['condition']);
      $this->db->set('warehouse', config_item('auth_warehouse'));
      $this->db->set('stores', $data['stores']);
      $this->db->set('quantity', $data['quantity_received']);
      $this->db->set('updated_at', date('Y-m-d H:i:s'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->set('document_number', $item_in_stores['document_number']);
      $this->db->set('order_number', $item_in_stores['order_number']);
      $this->db->set('reference_number', $this->input->post('document_number'));
      $this->db->set('awb_number', $item_in_stores['awb_number']);
      $this->db->set('date_of_entry', $this->input->post('received_date'));
      $this->db->set('date_of_expired', $item_in_stores['date_of_expired']);
      $this->db->set('unit_value', $item_in_stores['unit_value']);
      $this->db->insert('tb_stocks');

      //... item logs
      if (!empty($item_in_stores['serial_number']))
        $this->db->set('serial_number', $item_in_stores['serial_number']);

      if (!empty($data['remarks']))
        $this->db->set('remarks', $data['remarks']);

      $this->db->set('part_number', $item_in_stores['part_number']);
      $this->db->set('condition', $item_in_stores['condition']);
      $this->db->set('warehouse', config_item('auth_warehouse'));
      $this->db->set('stores', $data['stores']);
      $this->db->set('quantity', 0 + floatval($data['quantity_received']));
      $this->db->set('document_type', 'SD');
      $this->db->set('document_number', $this->input->post('document_number'));
      $this->db->set('date_of_entry', $this->input->post('received_date'));
      $this->db->set('received_from', $this->input->post('warehouse'));
      $this->db->set('received_by', config_item('auth_person_name'));
      $this->db->set('unit_value', floatval($item_in_stores['unit_value']));
	  $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->insert('tb_stock_cards');
    }

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function delete(array $criteria)
  {
    $this->db->trans_begin();

    $this->db->where($criteria)
      ->delete('tb_doc_shipments');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();

    return TRUE;
  }
}
