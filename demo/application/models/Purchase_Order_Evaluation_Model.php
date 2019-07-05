<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_Order_Evaluation_Model extends MY_Model
{
  protected $connection;
  protected $budget_year;
  protected $budget_month;

  public function __construct()
  {
    parent::__construct();

    $this->connection   = $this->load->database('budgetcontrol', TRUE);
    $this->budget_year  = find_budget_setting('Active Year');
    $this->budget_month = find_budget_setting('Active Month');
  }

  public function getSelectedColumns()
  {
    return array(
      'tb_purchase_orders.id'                          => NULL,
      'tb_purchase_orders.document_number'             => 'Document Number',
      'tb_purchase_orders.document_date'               => 'Date',
      'tb_purchase_orders.category'                    => 'Category',
      'tb_purchase_order_items.description'            => 'Description',
      'tb_purchase_order_items.part_number'            => 'Part Number',
      'tb_purchase_order_items.alternate_part_number'  => 'Alt. Part Number',
      'tb_purchase_order_items_vendors.quantity'       => 'Quantity',
      'tb_purchase_order_vendors.vendor'               => 'Vendor',
      'tb_purchase_orders.status'                      => 'Status',
      'tb_purchase_orders.document_reference'          => 'Reference',
      'tb_purchase_orders.created_by'                  => 'Created By',
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'tb_purchase_orders.document_number',
      'tb_purchase_orders.category',
      'tb_purchase_order_items.description',
      'tb_purchase_order_items.part_number',
      'tb_purchase_order_items.alternate_part_number',
      'tb_purchase_order_vendors.vendor',
      'tb_purchase_orders.status',
      'tb_purchase_orders.document_reference',
      'tb_purchase_orders.created_by',
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'tb_purchase_orders.document_number',
      'tb_purchase_orders.document_date',
      'tb_purchase_orders.category',
      'tb_purchase_order_items.description',
      'tb_purchase_order_items.part_number',
      'tb_purchase_order_items.alternate_part_number',
      'tb_purchase_order_items_vendors.quantity',
      'tb_purchase_order_vendors.vendor',
      'tb_purchase_orders.status',
      'tb_purchase_orders.document_reference',
      'tb_purchase_orders.created_by',
    );
  }

  private function searchIndex()
  {
    if (!empty($_POST['columns'][2]['search']['value'])){
      $search_document_date = $_POST['columns'][2]['search']['value'];
      $range_document_date  = explode(' ', $search_document_date);

      $this->db->where('tb_purchase_orders.document_date >= ', $range_document_date[0]);
      $this->db->where('tb_purchase_orders.document_date <= ', $range_document_date[1]);
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
    $this->db->from('tb_purchase_order_items_vendors');
    $this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.id = tb_purchase_order_items_vendors.poe_item_id');
    $this->db->join('tb_purchase_order_vendors', 'tb_purchase_order_vendors.id = tb_purchase_order_items_vendors.poe_vendor_id');
    $this->db->join('tb_purchase_orders', 'tb_purchase_orders.document_number = tb_purchase_order_items.document_number');
    $this->db->where('tb_purchase_order_items_vendors.selected', 't');
    $this->db->where_in('tb_purchase_orders.category', config_item('auth_inventory'));

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
    $this->db->from('tb_purchase_order_items_vendors');
    $this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.id = tb_purchase_order_items_vendors.poe_item_id');
    $this->db->join('tb_purchase_order_vendors', 'tb_purchase_order_vendors.id = tb_purchase_order_items_vendors.poe_vendor_id');
    $this->db->join('tb_purchase_orders', 'tb_purchase_orders.document_number = tb_purchase_order_items.document_number');
    $this->db->where('tb_purchase_order_items_vendors.selected', 't');
    $this->db->where_in('tb_purchase_orders.category', config_item('auth_inventory'));

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->from('tb_purchase_order_items_vendors');
    $this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.id = tb_purchase_order_items_vendors.poe_item_id');
    $this->db->join('tb_purchase_order_vendors', 'tb_purchase_order_vendors.id = tb_purchase_order_items_vendors.poe_vendor_id');
    $this->db->join('tb_purchase_orders', 'tb_purchase_orders.document_number = tb_purchase_order_items.document_number');
    $this->db->where('tb_purchase_order_items_vendors.selected', 't');
    $this->db->where_in('tb_purchase_orders.category', config_item('auth_inventory'));

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function findById($id)
  {
    $this->db->where('id', $id);

    $query  = $this->db->get('tb_purchase_orders');
    $poe    = $query->unbuffered_row('array');

    $this->db->from('tb_purchase_order_vendors');
    $this->db->where('tb_purchase_order_vendors.purchase_order_id', $id);

    $query = $this->db->get();

    foreach ($query->result_array() as $key => $vendor){
      $poe['vendors'][$key]['vendor'] = $vendor['vendor'];
      $poe['vendors'][$key]['is_selected'] = $vendor['is_selected'];
    }

    $this->db->from('tb_purchase_order_items');
    $this->db->where('tb_purchase_order_items.purchase_order_id', $id);

    $query = $this->db->get();

    foreach ($query->result_array() as $i => $item){
      $poe['request'][$i] = $item;
      $poe['request'][$i]['vendors'] = array();

      $selected_detail = array(
        'tb_purchase_order_items_vendors.*',
        'tb_purchase_order_vendors.vendor',
      );

      $this->db->select($selected_detail);
      $this->db->from('tb_purchase_order_items_vendors');
      $this->db->join('tb_purchase_order_vendors', 'tb_purchase_order_vendors.id = tb_purchase_order_items_vendors.purchase_order_vendor_id');
      $this->db->where('tb_purchase_order_items_vendors.purchase_order_item_id', $item['id']);

      $query = $this->db->get();

      foreach ($query->result_array() as $d => $detail) {
        $poe['request'][$i]['vendors'][$d] = $detail;
      }
    }

    return $poe;
  }

  public function isDocumentNumberExists($document_number)
  {
    $this->db->where('document_number', $document_number);
    $query = $this->db->get('tb_purchase_orders');

    if ($query->num_rows() > 0)
      return true;

    return false;
  }

  public function save()
  {
    $document_id          = (isset($_SESSION['poe']['id'])) ? $_SESSION['poe']['id'] : NULL;
    $document_edit        = (isset($_SESSION['poe']['edit'])) ? $_SESSION['poe']['edit'] : NULL;
    $document_number      = $_SESSION['poe']['document_number'] . poe_format_number();
    $document_date        = $_SESSION['poe']['document_date'];
    $created_by           = (empty($_SESSION['poe']['created_by'])) ? NULL : $_SESSION['poe']['created_by'];
    $document_reference   = (empty($_SESSION['poe']['document_reference'])) ? NULL : $_SESSION['poe']['document_reference'];
    $approved_by          = (empty($_SESSION['poe']['approved_by'])) ? NULL : $_SESSION['poe']['approved_by'];
    $status               = 'evaluation';
    $warehouse            = $_SESSION['poe']['warehouse'];
    $category             = $_SESSION['poe']['category'];
    $default_currency     = $_SESSION['poe']['default_currency'];
    $exchange_rate        = $_SESSION['poe']['exchange_rate'];
    $notes                = (empty($_SESSION['poe']['notes'])) ? NULL : $_SESSION['poe']['notes'];

    $this->db->trans_begin();

    if ($document_id === NULL){
      $this->db->set('evaluation_number', $document_number);
      $this->db->set('document_reference', $document_reference);
      $this->db->set('document_date', $document_date);
      $this->db->set('created_by', $created_by);
      $this->db->set('approved_by', $approved_by);
      $this->db->set('approved_by', $approved_by);
      $this->db->set('category', $category);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('default_currency', $default_currency);
      $this->db->set('exchange_rate', $exchange_rate);
      $this->db->set('status', $status);
      $this->db->set('notes', $notes);
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->insert('tb_purchase_orders');

      $document_id = $this->db->insert_id();
    } else {
      /**
       * CREATE DOCUMENT
       */
      $this->db->set('document_number', $document_number);
      $this->db->set('document_date', $document_date);
      $this->db->set('document_reference', $document_reference);
      $this->db->set('created_by', $created_by);
      $this->db->set('approved_by', $approved_by);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('category', $category);
      $this->db->set('default_currency', $default_currency);
      $this->db->set('exchange_rate', $exchange_rate);
      $this->db->set('status', $status);
      $this->db->set('notes', $notes);
      $this->db->set('updated_at', date('Y-m-d'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->where('id', $document_id);
      $this->db->update('tb_purchase_orders');

      /**
       * DELETE OLD ITEMS
       */
      $this->db->where('purchase_order_id', $document_id);
      $this->db->delete('tb_purchase_order_vendors');

      $this->db->where('purchase_order_id', $document_id);
      $this->db->delete('tb_purchase_order_items');
    }

    /**
     * PROCESSING VENDORS
     */
    foreach ($_SESSION['poe']['vendors'] as $key => $vendor){
      $this->db->set('purchase_order_id', $document_id);
      $this->db->set('vendor', $vendor['vendor']);
      $this->db->set('is_selected', $vendor['is_selected']);
      $this->db->insert('tb_purchase_order_vendors');

      if ($vendor['is_selected'] == 't'){
        $this->db->from('tb_master_vendors');
        $this->db->where('tb_master_vendors.vendor', $vendor['vendor']);

        $query = $this->db->get();
        $row   = $query->unbuffered_row('array');

        $this->db->set('vendor', $vendor['vendor']);
        $this->db->set('vendor_address', $row['address']);
        $this->db->set('vendor_country', $row['country']);
        $this->db->set('vendor_phone', $row['phone']);
        $this->db->set('vendor_attention', $row['email']);
        $this->db->set('updated_at', date('Y-m-d'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->where('id', $document_id);
        $this->db->update('tb_purchase_orders');
      }
    }

    /**
     * PROCESSING ITEMS
     */
    foreach ($_SESSION['poe']['request'] as $i => $item){
      $this->db->set('purchase_order_id', $document_id);
      $this->db->set('description', strtoupper($item['description']));
      $this->db->set('part_number', strtoupper($item['part_number']));
      $this->db->set('alternate_part_number', strtoupper($item['alternate_part_number']));
      $this->db->set('remarks', trim($item['remarks']));
      $this->db->set('quantity_requested', floatval($item['quantity_requested']));
      $this->db->set('unit_price_requested', floatval($item['unit_price_requested']));
      $this->db->set('total_amount_requested', floatval($item['total_amount_requested']));
      $this->db->set('unit', trim($item['unit']));
      $this->db->set('inventory_purchase_request_detail_id', $item['inventory_purchase_request_detail_id']);
      $this->db->insert('tb_purchase_order_items');

      $poe_item_id = $this->db->insert_id();

      foreach ($item['vendors'] as $d => $detail) {
        $this->db->from('tb_purchase_order_vendors');
        $this->db->where('tb_purchase_order_vendors.vendor', $detail['vendor']);
        $this->db->where('tb_purchase_order_vendors.purchase_order_id', $document_id);

        $query  = $this->db->get();
        $row    = $query->unbuffered_row('array');
        $poe_vendor_id  = $row['id'];
        $is_selected    = $row['is_selected'];

        $this->db->set('purchase_order_item_id', $poe_item_id);
        $this->db->set('purchase_order_vendor_id', $poe_vendor_id);
        $this->db->set('quantity', floatval($detail['quantity']));
        $this->db->set('left_received_quantity', floatval($detail['left_received_quantity']));
        $this->db->set('left_paid_quantity', floatval($detail['left_paid_quantity']));
        $this->db->set('unit_price', floatval($detail['unit_price']));
        $this->db->set('core_charge', floatval($detail['core_charge']));
        $this->db->set('total', floatval($detail['total']));
        $this->db->set('left_paid_amount', floatval($detail['left_paid_amount']));
        $this->db->set('alternate_part_number', strtoupper($detail['alternate_part_number']));
        $this->db->set('purchase_request_number', strtoupper($detail['purchase_request_number']));
        $this->db->insert('tb_purchase_order_items_vendors');

        if ($is_selected == 't'){
          $this->db->set('alternate_part_number', strtoupper($detail['alternate_part_number']));
          $this->db->set('purchase_request_number', strtoupper($detail['purchase_request_number']));
          $this->db->set('vendor', strtoupper($detail['vendor']));
          $this->db->set('quantity', floatval($detail['quantity']));
          $this->db->set('left_received_quantity', floatval($detail['left_received_quantity']));
          $this->db->set('left_paid_quantity', floatval($detail['left_paid_quantity']));
          $this->db->set('unit_price', floatval($detail['unit_price']));
          $this->db->set('core_charge', floatval($detail['core_charge']));
          $this->db->set('total_amount', floatval($detail['total']));
          $this->db->set('left_paid_amount', floatval($detail['left_paid_amount']));
          $this->db->where('id', $poe_item_id);
          $this->db->update('tb_purchase_order_items');
        }
      }

	  if ($document_id === NULL){
		  $this->db->set('closing_by', config_item('auth_person_name'));
		  $this->db->set('purchase_request_detail_id', $item['inventory_purchase_request_detail_id']);
		  $this->db->insert('tb_purchase_request_closures');
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

    $this->db->select('document_number, warehouse');
    $this->db->where('id', $id);
    $this->db->from('tb_purchase_orders');

    $query = $this->db->get();
    $row   = $query->unbuffered_row('array');

    $document_number  = $row['document_number'];
    $warehouse        = $row['warehouse'];

    $this->db->select('tb_purchase_order_items.id, tb_purchase_order_items.stock_in_stores_id, tb_purchase_order_items.received_quantity, tb_purchase_order_items.received_unit_value, tb_stock_in_stores.stock_id, tb_stock_in_stores.serial_id, tb_stock_in_stores.stores');
    $this->db->from('tb_purchase_order_items');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_purchase_order_items.stock_in_stores_id');
    $this->db->where('tb_purchase_order_items.document_number', $document_number);

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
      $this->db->set('unit_value', floatval($data['received_unit_value']));
      $this->db->insert('tb_stock_cards');

      $this->db->where('id', $data['id']);
      $this->db->delete('tb_purchase_order_items');

      $this->db->where('id', $data['stock_in_stores_id']);
      $this->db->delete('tb_stock_in_stores');
    }

    $this->db->where('id', $id);
    $this->db->delete('tb_purchase_orders');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function getDocumentClosures()
  {
    $this->db->select('tb_purchase_request_closures.purchase_request_detail_id');
    $this->db->from('tb_purchase_request_closures');

    $query  = $this->db->get();
    $result = array();

    foreach ($query->result_array() as $key => $value) {
      $result[] = $value['purchase_request_detail_id'];
    }

    return $result;
  }

  public function listRequest($category = NULL)
  {
    $this->connection->select(array(
      'tb_inventory_purchase_requisition_details.id',
      'tb_inventory_purchase_requisition_details.additional_info',
      'tb_inventory_purchase_requisition_details.quantity',
      'tb_product_categories.category_name',
      'tb_products.product_name',
      'tb_products.product_code',
      'tb_inventory_purchase_requisitions.pr_number',
      'tb_inventory_purchase_requisitions.pr_date',
      'tb_inventory_purchase_requisitions.required_date',
      'tb_inventory_purchase_requisitions.status',
      'tb_inventory_purchase_requisitions.suggested_supplier',
      'tb_inventory_purchase_requisitions.deliver_to',
      'tb_inventory_purchase_requisitions.created_by',
      'tb_inventory_purchase_requisitions.notes'
    ));

    $this->connection->from('tb_inventory_purchase_requisitions');
    $this->connection->join('tb_inventory_purchase_requisition_details', 'tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id = tb_inventory_purchase_requisitions.id');
    $this->connection->join('tb_inventory_monthly_budgets', 'tb_inventory_monthly_budgets.id = tb_inventory_purchase_requisition_details.inventory_monthly_budget_id');
    $this->connection->join('tb_products', 'tb_products.id = tb_inventory_monthly_budgets.product_id');
    $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_inventory_purchase_requisitions.product_category_id');
    $this->connection->where('tb_inventory_purchase_requisitions.status', 'approved');

    if ($category === NULL){
      $this->connection->where_in('UPPER(tb_product_categories.category_name)', config_item('auth_inventory'));
    } else {
      $this->connection->where('UPPER(tb_product_categories.category_name)', $category);
    }

    if ( empty($this->getDocumentClosures()) === FALSE ){
      $this->connection->where_not_in('tb_inventory_purchase_requisition_details.id', $this->getDocumentClosures());
    }

    $this->connection->like('tb_inventory_purchase_requisitions.pr_number', $this->budget_year);
    $this->connection->order_by('tb_inventory_purchase_requisitions.id', 'desc');

    $query = $this->connection->get();

    return $query->result_array();
  }

  public function infoRequest($id)
  {
    $this->connection->select(array(
      'tb_inventory_purchase_requisition_details.id',
      'tb_inventory_purchase_requisition_details.additional_info',
      'tb_inventory_purchase_requisition_details.quantity',
      'tb_inventory_purchase_requisition_details.price',
      'tb_product_categories.category_name',
      'tb_products.product_name',
      'tb_products.product_code',
      'tb_inventory_purchase_requisitions.pr_number',
      'tb_inventory_purchase_requisitions.pr_date',
      'tb_inventory_purchase_requisitions.required_date',
      'tb_inventory_purchase_requisitions.status',
      'tb_inventory_purchase_requisitions.suggested_supplier',
      'tb_inventory_purchase_requisitions.deliver_to',
      'tb_inventory_purchase_requisitions.created_by',
      'tb_inventory_purchase_requisitions.notes'
    ));

    $this->connection->from('tb_inventory_purchase_requisition_details');
    $this->connection->join('tb_inventory_purchase_requisitions', 'tb_inventory_purchase_requisitions.id = tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id');
    $this->connection->join('tb_inventory_monthly_budgets', 'tb_inventory_monthly_budgets.id = tb_inventory_purchase_requisition_details.inventory_monthly_budget_id');
    $this->connection->join('tb_products', 'tb_products.id = tb_inventory_monthly_budgets.product_id');
    $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_inventory_purchase_requisitions.product_category_id');
    $this->connection->where('tb_inventory_purchase_requisition_details.id', $id);

    $query = $this->connection->get();

    return $query->unbuffered_row('array');
  }

  public function searchRequestItem($category)
  {
    $select = array(
      'tb_inventory_purchase_requisition_details.*',
      'tb_inventory_purchase_requisitions.pr_number',
      'tb_inventory_purchase_requisitions.pr_date',
      'tb_inventory_purchase_requisitions.required_date',
      'tb_products.product_name',
    );

    $this->connection->select($select);
    $this->connection->from('tb_inventory_purchase_requisition_details');
    $this->connection->join('tb_inventory_purchase_requisitions', 'tb_inventory_purchase_requisitions.id = tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id');
    $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_inventory_purchase_requisitions.product_category_id');
    $this->connection->join('tb_inventory_monthly_budgets', 'tb_inventory_monthly_budgets.id = tb_inventory_purchase_requisition_details.inventory_monthly_budget_id');
    $this->connection->join('tb_products', 'tb_products.id = tb_inventory_monthly_budgets.product_id');
    $this->connection->where('UPPER(tb_product_categories.category_name)', $category);
    $this->connection->where('tb_inventory_purchase_requisitions.status', 'approved');
    $this->connection->like('tb_inventory_purchase_requisitions.pr_number', $this->budget_year);

    $this->connection->order_by('tb_products.product_name ASC, tb_inventory_purchase_requisition_details.part_number ASC');

    $query  = $this->connection->get();
    $result = $query->result_array();

    return $result;
  }
}
