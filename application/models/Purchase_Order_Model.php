<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_Order_Model extends MY_Model
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
  public function loadBase(){
    return $this->db->get('tb_master_warehouses')->result();
  }
  public function getSelectedColumns()
  {
    if((config_item('auth_role') == 'HEAD OF SCHOOL')||(config_item('auth_role') == 'CHIEF OF FINANCE')||(config_item('auth_role') == 'FINANCE')){
      return array(
      "''".' as "temp"' => "Act.",     
      'tb_purchase_orders.id' => NULL,
      'tb_purchase_orders.document_number'              => 'Document Number',
      'tb_purchase_orders.review_status' => 'Review Status',
      'tb_purchase_orders.document_date'                => 'Date',
      'tb_purchase_orders.category'                     => 'Category',
      'tb_purchase_order_items.description'             => 'Description',
      'tb_purchase_order_items.part_number'             => 'Part Number',
      'tb_purchase_order_items.alternate_part_number'   => 'Alt. Part Number',
      'tb_purchase_orders.evaluation_number'            => 'Ref. POE',
      'tb_purchase_order_items.purchase_request_number' => 'Ref. PR',
      'tb_purchase_orders.reference_quotation'          => 'Ref. Quotation',
      'tb_purchase_orders.vendor'                       => 'Vendor',
      'tb_purchase_order_items.quantity'                => 'Order Qty',
      'tb_purchase_order_items.quantity_requested'      => 'Requested Qty',
      '(tb_purchase_order_items.quantity - tb_purchase_order_items.left_received_quantity) AS quantity_received' => 'Received Qty',
      'tb_purchase_order_items.unit_price'              => 'Unit Price',
      'tb_purchase_order_items.core_charge'             => 'Core Charge',
      'tb_purchase_order_items.total_amount'            => 'Total Amount',
      '(tb_purchase_order_items.total_amount - tb_purchase_order_items.left_paid_amount) AS amount_paid' => 'Paid Amount',
      'tb_purchase_orders.notes'                        => 'Notes',
      'tb_purchase_orders.approved_by_hos'              => null,
      'tb_purchase_orders.approved_by_cof'              => null,
    );
    } else {
      return array(
      'tb_purchase_orders.id' => NULL,
      'tb_purchase_orders.document_number'              => 'Document Number',
      'tb_purchase_orders.review_status'                => 'Review Status',
      'tb_purchase_orders.document_date'                => 'Date',
      'tb_purchase_orders.category'                     => 'Category',
      'tb_purchase_order_items.description'             => 'Description',
      'tb_purchase_order_items.part_number'             => 'Part Number',
      'tb_purchase_order_items.alternate_part_number'   => 'Alt. Part Number',
      'tb_purchase_orders.evaluation_number'            => 'Ref. POE',
      'tb_purchase_order_items.purchase_request_number' => 'Ref. PR',
      'tb_purchase_orders.reference_quotation'          => 'Ref. Quotation',
      'tb_purchase_orders.vendor'                       => 'Vendor',
      'tb_purchase_order_items.quantity'                => 'Order Qty',
      'tb_purchase_order_items.quantity_requested'      => 'Requested Qty',
      '(tb_purchase_order_items.quantity - tb_purchase_order_items.left_received_quantity) AS quantity_received' => 'Received Qty',
      'tb_purchase_order_items.unit_price'              => 'Unit Price',
      'tb_purchase_order_items.core_charge'             => 'Core Charge',
      'tb_purchase_order_items.total_amount'            => 'Total Amount',
      '(tb_purchase_order_items.total_amount - tb_purchase_order_items.left_paid_amount) AS amount_paid' => 'Paid Amount',
      'tb_purchase_orders.notes'                        => 'Notes',
      'tb_purchase_orders.approved_by_hos'              => null,
      'tb_purchase_orders.approved_by_cof'              => null,
      
    );
    }
      
  }
   public function getNotifRecipient($int){
    $this->db->select('email');
    $this->db->from('tb_auth_users');
    $this->db->where('auth_level',$int);
    return $this->db->get('')->result();
  }
  public function getSearchableColumns()
  {
    return array(
      'tb_purchase_orders.document_number',
      'tb_purchase_orders.category',
      'tb_purchase_order_items.description',
      'tb_purchase_order_items.part_number',
      'tb_purchase_order_items.alternate_part_number',
      'tb_purchase_orders.evaluation_number',
      'tb_purchase_order_items.purchase_request_number',
      'tb_purchase_orders.reference_quotation',
      'tb_purchase_orders.vendor',
      'tb_purchase_orders.notes',
      "'review_status'",
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      null,
      'tb_purchase_orders.document_number',
      'tb_purchase_orders.review_status',
      'tb_purchase_orders.document_date',
      'tb_purchase_orders.category',
      'tb_purchase_order_items.description',
      'tb_purchase_order_items.part_number',
      'tb_purchase_order_items.alternate_part_number',
      'tb_purchase_orders.evaluation_number',
      'tb_purchase_order_items.purchase_request_number',
      'tb_purchase_orders.reference_quotation',
      'tb_purchase_order.vendor',
      'tb_purchase_order_items.quantity',
      'tb_purchase_order_items.quantity_requested',
      '(tb_purchase_order_items.quantity - tb_purchase_order_items.left_received_quantity)',
      'tb_purchase_order_items.unit_price',
      'tb_purchase_order_items.core_charge',
      'tb_purchase_order_items.total_amount',
      '(tb_purchase_order_items.total_amount - tb_purchase_order_items.left_paid_amount) AS amount_paid',
      'tb_purchase_orders.notes',

    );
  }

  private function searchIndex()
  {
    if (!empty($_POST['columns'][3]['search']['value'])){
      $search_category = $_POST['columns'][3]['search']['value'];

      $this->db->where('tb_purchase_orders.category', $search_category);
    }

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
    $this->db->from('tb_purchase_order_items');
    $this->db->join('tb_purchase_orders', 'tb_purchase_orders.id = tb_purchase_order_items.purchase_order_id');
    $this->db->where('tb_purchase_orders.status', 'approved');
    $this->db->where_in('tb_purchase_orders.category', config_item('auth_inventory'));

    // if (config_item('auth_role') == 'FINANCE'){
    //   $this->db->where('tb_purchase_order_items.left_paid_amount > ', 0);
    // }

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
    //echo $this->db->_compile_select();
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
    $this->db->from('tb_purchase_order_items');
    $this->db->join('tb_purchase_orders', 'tb_purchase_orders.id = tb_purchase_order_items.purchase_order_id');
    $this->db->where('tb_purchase_orders.status', 'approved');
    $this->db->where_in('tb_purchase_orders.category', config_item('auth_inventory'));

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }
  public function approve($id)
  {
    
    if((config_item('auth_role') == 'HEAD OF SCHOOL')){
      $this->db->set('review_status',strtoupper("waiting for cof review"));
      $this->db->set('known_by',config_item('auth_person_name'));
    }
    if((config_item('auth_role') == 'CHIEF OF FINANCE')){
       $this->db->set('review_status',strtoupper("approved"));
       $this->db->set('approved_by',config_item('auth_person_name'));
    }
    if((config_item('auth_role') == 'FINANCE')){

       $this->db->set('review_status',strtoupper("waiting for hos review"));
       $this->db->set('checked_by',config_item('auth_person_name'));
    }
    $this->db->where('id', $id);
    return $this->db->update('tb_purchase_orders');
  }
  public function countIndex()
  {
    $this->db->from('tb_purchase_order_items');
    $this->db->join('tb_purchase_orders', 'tb_purchase_orders.id = tb_purchase_order_items.purchase_order_id');
    $this->db->where('tb_purchase_orders.status', 'approved');
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

  public function findDetailById($id)
  {
    $this->db->select(array(
      'tb_purchase_order_items.*',
      'tb_purchase_orders.document_number',
      'tb_purchase_orders.evaluation_number',
      'tb_purchase_orders.issued_by',
      'tb_purchase_orders.document_date',
      'tb_purchase_orders.warehouse',
      'tb_purchase_orders.category',
      'tb_purchase_orders.default_currency',
      'tb_purchase_orders.exchange_rate',
    ));
    $this->db->from('tb_purchase_order_items');
    $this->db->where('tb_purchase_order_items.id', $id);
    $this->db->join('tb_purchase_orders', 'tb_purchase_orders.id = tb_purchase_order_items.purchase_order_id', 'left');

    $query  = $this->db->get();
    $order  = $query->unbuffered_row('array');

    return $order;
  }
  function multi_reject($id_purchase_order,$notes){
    $x = 0;
    $return = 0;
    foreach ($id_purchase_order as $id) {
      $this->db->where('purchase_order_id', $id);
      $tb_purchase_order_items = $this->db->get('tb_purchase_order_items')->result();
      foreach ($tb_purchase_order_items as $key) {
        $inventory_purchase_request_detail_id = $key->inventory_purchase_request_detail_id;
        $this->db->where('id', $inventory_purchase_request_detail_id);
        $this->db->set('sisa','"sisa" + '.$key->quantity,false);
        $this->db->update('tb_inventory_purchase_requisition_details');
      }
      $this->db->set('status','rejected');
      $this->db->set('notes',$notes[$x]);
      $this->db->where('id',$id);
      $check = $this->db->update('tb_purchase_orders');
      if($check){
        $return++;
      }
      $x++;
    }
    if(($return == $x)&&($return > 0)){
      return true;
    }else{
      return false;
    }
  }
  public function findById2($id)
  {
    $this->db->where('id', $id);

    $query  = $this->db->get('tb_purchase_orders');
    $order  = $query->unbuffered_row('array');

    $selected_item = array(
      'tb_purchase_order_items.*',
    );

    $this->db->select($selected_item);
    $this->db->from('tb_purchase_order_items');
    $this->db->where('tb_purchase_order_items.purchase_order_id', $id);

    $query = $this->db->get();

    foreach ($query->result_array() as $i => $item){
      $order['items'][$i] = $item;
    }

    return $order;
  }

  public function isDocumentNumberExists($document_number)
  {
    $this->db->where('document_number', $document_number);
    $query = $this->db->get('tb_purchase_orders');

    if ($query->num_rows() > 0)
      return true;

    return false;
  }

  public function payment_save($id)
  {
    $amount_paid  = floatval($this->input->post('amount_paid'));
    $remarks      = (empty($_POST['remarks'])) ? NULL : $_POST['remarks'];

    $this->db->trans_begin();

    $this->db->set('purchase_order_item_id', $id);
    $this->db->set('amount_paid', $amount_paid);
    $this->db->set('remarks', $remarks);
    $this->db->set('created_at', date('Y-m-d'));
    $this->db->set('created_by', config_item('auth_person_name'));
    $this->db->insert('tb_purchase_order_items_payments');

    $this->db->from('tb_purchase_order_items');
    $this->db->where('tb_purchase_order_items.id', $id);

    $query  = $this->db->get();
    $order  = $query->unbuffered_row('array');

    $left_paid_amount = floatval($order['left_paid_amount']) - $amount_paid;

    $this->db->set('left_paid_amount', $left_paid_amount);
    $this->db->where('id', $id);
    $this->db->update('tb_purchase_order_items');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function save($id)
  {
    $document_number      = $_POST['document_number'] . order_format_number($_POST['category']);
    $document_date        = $_POST['document_date'];
    $reference_quotation  = (empty($_POST['reference_quotation'])) ? NULL : $_POST['reference_quotation'];
    $issued_by            = (empty($_POST['issued_by'])) ? NULL : $_POST['issued_by'];
    $checked_by           = (empty($_POST['checked_by'])) ? NULL : $_POST['checked_by'];
    $approved_by          = (empty($_POST['approved_by'])) ? NULL : $_POST['approved_by'];
    $known_by             = (empty($_POST['known_by'])) ? NULL : $_POST['known_by'];
    // $vendor               = $_POST['vendor'];
    $vendor_address       = $_POST['vendor_address'];
    $vendor_country       = $_POST['vendor_country'];
    $vendor_phone         = $_POST['vendor_phone'];
    $vendor_attention     = $_POST['vendor_attention'];
    $deliver_company      = $_POST['deliver_company'];
    $deliver_address      = $_POST['deliver_address'];
    $deliver_country      = $_POST['deliver_country'];
    $deliver_phone        = $_POST['deliver_phone'];
    $deliver_attention    = $_POST['deliver_attention'];
    $bill_company         = $_POST['bill_company'];
    $bill_address         = $_POST['bill_address'];
    $bill_country         = $_POST['bill_country'];
    $bill_phone           = $_POST['bill_phone'];
    $bill_attention       = $_POST['bill_attention'];
    // $default_currency     = $_POST['default_currency'];
    // $exchange_rate        = $_POST['exchange_rate'];
    $discount             = $_POST['discount'];
    $taxes                = $_POST['taxes'];
    $shipping_cost        = $_POST['shipping_cost'];
    // $warehouse            = $_POST['warehouse'];
    // $category             = $_POST['category'];
    $notes                = (empty($_POST['notes'])) ? NULL : $_POST['notes'];

    $this->db->trans_begin();

    $this->db->set('document_number', $document_number);
    $this->db->set('document_date', $document_date);
    $this->db->set('reference_quotation', $reference_quotation);
    $this->db->set('issued_by', $issued_by);
    $this->db->set('checked_by', $checked_by);
    $this->db->set('approved_by', $approved_by);
    // $this->db->set('warehouse', $warehouse);
    // $this->db->set('category', $category);
    // $this->db->set('vendor', $vendor);
    $this->db->set('vendor_address', $vendor_address);
    $this->db->set('vendor_country', $vendor_country);
    $this->db->set('vendor_phone', $vendor_phone);
    $this->db->set('vendor_attention', $vendor_attention);
    $this->db->set('deliver_company', $deliver_company);
    $this->db->set('deliver_address', $deliver_address);
    $this->db->set('deliver_country', $deliver_country);
    $this->db->set('deliver_phone', $deliver_phone);
    $this->db->set('deliver_attention', $deliver_attention);
    $this->db->set('bill_company', $bill_company);
    $this->db->set('bill_address', $bill_address);
    $this->db->set('bill_country', $bill_country);
    $this->db->set('bill_phone', $bill_phone);
    $this->db->set('bill_attention', $bill_attention);
    // $this->db->set('default_currency', $default_currency);
    // $this->db->set('exchange_rate', $exchange_rate);
    $this->db->set('discount', $discount);
    $this->db->set('taxes', $taxes);
    $this->db->set('shipping_cost', $shipping_cost);
    $this->db->set('notes', $notes);
    $this->db->set('status', 'approved');
    $this->db->set('updated_at', date('Y-m-d'));
    $this->db->set('updated_by', config_item('auth_person_name'));
    $this->db->set('review_status', strtoupper('waiting for finance review'));
    $this->db->where('id', $id);
    $this->db->update('tb_purchase_orders');

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
	  $this->db->set('created_by', config_item('auth_person_name'));
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

  public function searchPoeItem($category, $vendor)
  {
    $this->db->select('tb_purchase_order_evaluation_vendors.id');
    $this->db->from('tb_purchase_order_evaluation_vendors');
    $this->db->where('UPPER(tb_purchase_order_evaluation_vendors.vendor)', $vendor);

    $subQuery =  $this->db->get_compiled_select();

    $select = array(
      'tb_purchase_order_evaluation_items_vendors.*',
      'tb_purchase_order_evaluations.document_number',
      'tb_purchase_order_evaluations.document_date',
      'tb_purchase_order_evaluation_items.part_number',
      'tb_purchase_order_evaluation_items.description',
      'tb_purchase_order_evaluation_items.unit',
    );

    $this->db->select($select);
    $this->db->from('tb_purchase_order_evaluation_items_vendors');
    $this->db->join('tb_purchase_order_evaluation_items', 'tb_purchase_order_evaluation_items.id = tb_purchase_order_evaluation_items_vendors.poe_item_id');
    // $this->db->join('tb_purchase_order_evaluation_vendors', 'tb_purchase_order_evaluation_vendors.id = tb_purchase_order_evaluation_items_vendors.poe_vendor_id');
    $this->db->join('tb_purchase_order_evaluations', 'tb_purchase_order_evaluations.document_number = tb_purchase_order_evaluation_items.document_number');
    $this->db->where('tb_purchase_order_evaluations.status', 'approved');
    $this->db->where('UPPER(tb_purchase_order_evaluations.category)', $category);
    // $this->db->where('UPPER(tb_purchase_order_evaluation_vendors.vendor)', $vendor);
    $this->db->where("tb_purchase_order_evaluation_items_vendors.poe_vendor_id IN ($subQuery)", NULL, FALSE);
    $this->db->where('tb_purchase_order_evaluation_items_vendors.selected', 't');
    $this->db->where('tb_purchase_order_evaluation_items_vendors.purchase_order_number IS NULL', null, false);

    $this->db->order_by('tb_purchase_order_evaluation_items.description ASC, tb_purchase_order_evaluation_items.part_number ASC');

    $query  = $this->db->get();
    $result = $query->result_array();

    return $result;
  }
}
