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
    if((config_item('auth_role') == 'HEAD OF SCHOOL')||(config_item('auth_role') == 'CHIEF OF FINANCE')||(config_item('auth_role') == 'FINANCE MANAGER')||(config_item('auth_role') == 'VP FINANCE')){
      return array(
        "''".' as "temp"' => "Act.",     
        'tb_po.id' => NULL,
        'tb_po.document_number'              => 'Document Number',
        'tb_po.review_status'                => 'Review Status',
        'tb_po.document_date'                => 'Date',
        'tb_po.category'        => 'Category',
        'tb_po_item.description'             => 'Description',
        'tb_po_item.part_number'             => 'Part Number',
        'tb_po_item.alternate_part_number'   => 'Alt. Part Number',
        'tb_po_item.poe_number'            => 'Ref. POE',
        'tb_purchase_order_items.purchase_request_number' => 'Ref. PR',
        'tb_po.reference_quotation'          => 'Ref. Quotation',
        'tb_po.vendor'                       => 'Vendor',
        'tb_po_item.quantity'                => 'Order Qty',
        'tb_po_item.unit_price'              => 'Unit Price',
        'tb_po_item.core_charge'             => 'Core Charge',
        'tb_po_item.total_amount'            => 'Total Amount',
        // '(tb_po_item.quantity - tb_po_item.left_received_quantity) AS quantity_received' => 'Received Qty',
        // 'tb_po_item.left_received_quantity'      => 'Left Qty',
        // '(tb_po_item.total_amount - tb_po_item.left_paid_amount) AS amount_paid' => 'Paid Amount',
        'tb_po.notes'                        => 'Notes',
        'tb_po.approved_by_hos'              => null,
        'tb_po.approved_by_cof'              => null,
        'tb_purchase_orders.id as poe_id'              => null,
        'tb_purchase_order_items.id as poe_item_id'              => null
      );
    } else {
      return array(        
        // "''".' as "temp"' => "Act.", 
        'tb_po.id' => NULL,
        'tb_po.document_number'              => 'Document Number',
        'tb_po.review_status'                => 'Review Status',
        'tb_po.document_date'                => 'Date',
        'tb_po.category'        => 'Category',
        'tb_po_item.description'             => 'Description',
        'tb_po_item.part_number'             => 'Part Number',
        'tb_po_item.alternate_part_number'   => 'Alt. Part Number',
        'tb_po_item.poe_number'            => 'Ref. POE',
        'tb_purchase_order_items.purchase_request_number' => 'Ref. PR',
        'tb_po.reference_quotation'          => 'Ref. Quotation',
        'tb_po.vendor'                       => 'Vendor',
        'tb_po_item.quantity'                => 'Order Qty',
        'tb_po_item.unit_price'              => 'Unit Price',
        'tb_po_item.core_charge'             => 'Core Charge',
        'tb_po_item.total_amount'            => 'Total Amount',
        '(tb_po_item.quantity - tb_po_item.left_received_quantity) AS quantity_received' => 'Received Qty',
        'tb_po_item.left_received_quantity'      => 'Left Qty',
        // '(tb_po_item.total_amount - tb_po_item.left_paid_amount) AS amount_paid' => 'Paid Amount',
        'tb_po.notes'                        => 'Notes',
        'tb_po.approved_by_hos'              => null,
        'tb_po.approved_by_cof'              => null,
        'tb_purchase_orders.id as poe_id'              => null,
        'tb_purchase_order_items.id as poe_item_id'              => null
        
      );
    }
      
  }

  public function getSearchableColumns()
  {
    return array(
      'tb_po.document_number',
      'tb_po.category',
      'tb_po_item.description',
      'tb_po_item.part_number',
      'tb_po_item.alternate_part_number',
      'tb_po_item.evaluation_number',
      'tb_purchase_order_items.purchase_request_number',
      'tb_po.reference_quotation',
      'tb_po.vendor',
      'tb_po.notes',
      "'review_status'",
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      null,
      'tb_po.document_number',
      'tb_po.review_status',
      'tb_po.document_date',
      'tb_po.category',
      'tb_po_item.description',
      'tb_po_item.part_number',
      'tb_po_item.alternate_part_number',
      'tb_po_item.evaluation_number',
      'tb_purchase_order_items.purchase_request_number',
      'tb_po.reference_quotation',
      'tb_po.vendor',
      'tb_po_item.quantity',
      'tb_po_item.quantity_requested',
      '(tb_po_item.quantity - tb_po_item.left_received_quantity)',
      'tb_po_item.unit_price',
      'tb_po_item.core_charge',
      'tb_po_item.total_amount',
      '(tb_po_item.total_amount - tb_po_item.left_paid_amount) AS amount_paid',
      'tb_po.notes',

    );
  }

  
  private function searchIndex()
  {
    if (!empty($_POST['columns'][3]['search']['value'])){
      $search_category = $_POST['columns'][3]['search']['value'];

      $this->db->where('tb_po.category', $search_category);
    }

    if (!empty($_POST['columns'][2]['search']['value'])){
      $search_document_date = $_POST['columns'][2]['search']['value'];
      $range_document_date  = explode(' ', $search_document_date);

      $this->db->where('tb_po.document_date >= ', $range_document_date[0]);
      $this->db->where('tb_po.document_date <= ', $range_document_date[1]);
    }

    if (!empty($_POST['columns'][4]['search']['value'])){
      $status = $_POST['columns'][4]['search']['value'];
      if($status=='review'){
        $this->db->like('tb_po.review_status', 'WAITING');
      }elseif($status=='approved'){
        $this->db->where('tb_po.review_status', strtoupper($status));
      }elseif($status=='review_approved'){
        if(config_item('auth_role') == 'FINANCE MANAGER'){
          $this->db->like('tb_po.review_status', 'WAITING FOR HOS');
        }
        if(config_item('auth_role') == 'HEAD OF SCHOOL'){
          $this->db->like('tb_po.review_status', 'WAITING FOR VP FINANCE');
        }
        if(config_item('auth_role') == 'VP FINANCE'){
          $this->db->like('tb_po.review_status', 'WAITING FOR CFO');
        }
        if(config_item('auth_role') == 'CHIEF OF FINANCE'){
          $this->db->like('tb_po.review_status', 'APPROVED');
        }
      }elseif($status=='rejected'){
        $this->db->where('tb_po.review_status', strtoupper($status));
      }
      // elseif($status=='all'){
      //   $this->db->like('tb_po.review_status', 'WAITING');
      // }
    }else{
      if(config_item('auth_role') == 'FINANCE MANAGER'){
        $this->db->like('tb_po.review_status', 'WAITING FOR FINANCE');
      }
      if(config_item('auth_role') == 'HEAD OF SCHOOL'){
        $this->db->like('tb_po.review_status', 'WAITING FOR HOS');
      }
      if(config_item('auth_role') == 'VP FINANCE'){
        $this->db->like('tb_po.review_status', 'WAITING FOR VP FINANCE');
      }
      if(config_item('auth_role') == 'CHIEF OF FINANCE'){
        $this->db->like('tb_po.review_status', 'WAITING FOR CFO');
      }
      $this->db->where_not_in('tb_po.review_status', ['REVISI']);
      // else{
      //   $this->db->where('tb_po.review_status','!=','REVISI');
      // }
    }
    // else{
    //   $this->db->like('tb_po.review_status', 'WAITING');
    // }

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
    $this->db->from('tb_po_item');
    $this->db->join('tb_po', 'tb_po.id = tb_po_item.purchase_order_id');
    $this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.id = tb_po_item.poe_item_id','LEFT');
    $this->db->join('tb_purchase_orders', 'tb_purchase_orders.id = tb_purchase_order_items.purchase_order_id','LEFT');
    // $this->db->where('tb_po.review_status','!=','REVISI');
    $this->db->where_in('tb_po.category', config_item('auth_inventory'));

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
    // $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_po_item');
    $this->db->join('tb_po', 'tb_po.id = tb_po_item.purchase_order_id');
    $this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.id = tb_po_item.poe_item_id');
    $this->db->join('tb_purchase_orders', 'tb_purchase_orders.id = tb_purchase_order_items.purchase_order_id');
    // $this->db->where('tb_po.status', 'approved');
    $this->db->where_in('tb_po.category', config_item('auth_inventory'));

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    // $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_po_item');
    $this->db->join('tb_po', 'tb_po.id = tb_po_item.purchase_order_id');
    $this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.id = tb_po_item.poe_item_id');
    $this->db->join('tb_purchase_orders', 'tb_purchase_orders.id = tb_purchase_order_items.purchase_order_id');
    // $this->db->where('tb_po.status', 'approved');
    $this->db->where_in('tb_po.category', config_item('auth_inventory'));

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function approve($id)
  {
    $this->db->from('tb_po');
    $this->db->where('id',$id);

    $query  = $this->db->get();
    $row    = $query->unbuffered_row('array');
    $grandtotal = $row['grand_total'];
    $currency = $row['default_currency'];
    
    if((config_item('auth_role') == 'HEAD OF SCHOOL')){
      if ($currency=='IDR') {
        if($grandtotal>10000000){
          $level = 16;
          $this->db->set('review_status',strtoupper("waiting for coo review"));
          $this->db->set('known_by',config_item('auth_person_name'));
        }else{
          $level = 3;
          $this->db->set('review_status',strtoupper("waiting for vp finance review"));
          $this->db->set('known_by',config_item('auth_person_name'));
        }
      }else{
        if($grandtotal>1000){
          $level = 16;
          $this->db->set('review_status',strtoupper("waiting for coo review"));
          $this->db->set('known_by',config_item('auth_person_name'));
        }else{
          $level = 3;
          $this->db->set('review_status',strtoupper("waiting for vp finance review"));
          $this->db->set('known_by',config_item('auth_person_name'));
        }
      }      
    }
    if((config_item('auth_role') == 'CHIEF OF FINANCE')){
       $this->db->set('review_status',strtoupper("approved"));
       $this->db->set('status',strtoupper("order"));
       $this->db->set('approved_by',config_item('auth_person_name'));
    }

    if((config_item('auth_role') == 'FINANCE MANAGER')){
      $level = 10;
       $this->db->set('review_status',strtoupper("waiting for hos review"));
       $this->db->set('checked_by',config_item('auth_person_name'));
    }

    if((config_item('auth_role') == 'CHIEF OPERATION SUPPORT')){
      $level = 3;
       $this->db->set('review_status',strtoupper("waiting for vp finance review"));
       $this->db->set('coo_review',config_item('auth_person_name'));
    }

    if((config_item('auth_role') == 'VP FINANCE')){
      if ($currency=='IDR') {
        if($grandtotal>=3000000){
          $level = 11;
          $this->db->set('review_status',strtoupper("waiting for cfo review"));
          $this->db->set('check_review_by',config_item('auth_person_name'));
        }else{
          $level = 11;
          $this->db->set('review_status',strtoupper("approved"));
          $this->db->set('check_review_by',config_item('auth_person_name'));
        }
      }else{
        if($grandtotal>=300){
          $level = 11;
          $this->db->set('review_status',strtoupper("waiting for cfo review"));
          $this->db->set('check_review_by',config_item('auth_person_name'));
        }else{
          $level = 11;
          $this->db->set('review_status',strtoupper("approved"));
          $this->db->set('check_review_by',config_item('auth_person_name'));
        }
      }     
      
    }

    $this->send_mail($id,$level);
    $this->db->where('id', $id);
    return $this->db->update('tb_po');
  }


   /*public function getNotifRecipient($int){
    $this->db->select('email','person_name');
    $this->db->from('tb_auth_users');
    $this->db->where('auth_level',$int);
    return $this->db->get('')->result();
  }*/
  

  // public function findById($id)
  // {
  //   $this->db->where('id', $id);

  //   $query  = $this->db->get('tb_purchase_orders');
  //   $poe    = $query->unbuffered_row('array');

  //   $this->db->from('tb_purchase_order_vendors');
  //   $this->db->where('tb_purchase_order_vendors.purchase_order_id', $id);

  //   $query = $this->db->get();

  //   foreach ($query->result_array() as $key => $vendor){
  //     $poe['vendors'][$key]['vendor'] = $vendor['vendor'];
  //     $poe['vendors'][$key]['is_selected'] = $vendor['is_selected'];
  //   }

  //   $this->db->from('tb_purchase_order_items');
  //   $this->db->where('tb_purchase_order_items.purchase_order_id', $id);

  //   $query = $this->db->get();

  //   foreach ($query->result_array() as $i => $item){
  //     $poe['request'][$i] = $item;
  //     $poe['request'][$i]['vendors'] = array();

  //     $selected_detail = array(
  //       'tb_purchase_order_items_vendors.*',
  //       'tb_purchase_order_vendors.vendor',
  //     );

  //     $this->db->select($selected_detail);
  //     $this->db->from('tb_purchase_order_items_vendors');
  //     $this->db->join('tb_purchase_order_vendors', 'tb_purchase_order_vendors.id = tb_purchase_order_items_vendors.purchase_order_vendor_id');
  //     $this->db->where('tb_purchase_order_items_vendors.purchase_order_item_id', $item['id']);

  //     $query = $this->db->get();

  //     foreach ($query->result_array() as $d => $detail) {
  //       $poe['request'][$i]['vendors'][$d] = $detail;
  //     }
  //   }

  //   return $poe;
  // }

  public function findById($id)
  {
    $this->db->where('id', $id);

    $query  = $this->db->get('tb_po');
    $poe    = $query->unbuffered_row('array');    

    $select = array(
      'tb_po_item.*',
      'tb_purchase_order_items.purchase_request_number',
      // 'tb_purchase_order_items.ttd_issued_by'
    );

    $this->db->select($select);
    $this->db->from('tb_po_item');
    $this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.id = tb_po_item.poe_item_id');    
    $this->db->where('tb_po_item.purchase_order_id', $poe['id']);
    $query = $this->db->get();

    foreach ($query->result_array() as $key => $value){
      $poe['items'][$key] = $value;
    }

    return $poe;
  }

  public function findItemPoe($id)
  {
    $this->db->where('id', $id);
    $query  = $this->db->get('tb_purchase_order_vendors');
    $vendor    = $query->unbuffered_row('array');

    $select = array(
      'tb_purchase_order_items.id as purchase_order_evaluation_items_vendors_id',
      'tb_purchase_order_items.part_number',
      'tb_purchase_order_items.alternate_part_number',
      'tb_purchase_order_items.description',
      'tb_purchase_order_items.remarks',
      'tb_purchase_order_items.quantity',
      'tb_purchase_order_items.unit_price',
      'tb_purchase_order_items.core_charge',
      'tb_purchase_order_items.total_amount',
      'tb_purchase_order_items.unit',
      'tb_purchase_orders.evaluation_number',
    );

    $this->db->select($select);
    $this->db->from('tb_purchase_order_items');
    $this->db->join('tb_purchase_orders','tb_purchase_orders.id = tb_purchase_order_items.purchase_order_id');
    $this->db->where('purchase_order_id',$vendor['purchase_order_id']);
    $this->db->where('status_item','open');
    $this->db->where('tb_purchase_order_items.vendor',$vendor['vendor']);
    $query  = $this->db->get();

    return $query->result_array();
  }

  public function findPoe($id)
  {
    $this->db->where('id', $id);
    $query  = $this->db->get('tb_purchase_order_vendors');
    $vendor    = $query->unbuffered_row('array');

    $this->db->from('tb_master_vendors');
    $this->db->where('vendor', $vendor['vendor']);
    $query  = $this->db->get();
    $row    = $query->unbuffered_row('array');
    $result['vendor'] = $row['vendor'];
    $result['vendor_address']    = $row['address'];
    $result['vendor_country']    = $row['country'];
    $result['vendor_attention']  = 'Phone: '. $row['phone'];

    $this->db->select('*');
    $this->db->from('tb_purchase_orders');
    $this->db->where('id',$vendor['purchase_order_id']);
    $query  = $this->db->get();
    $poe  = $query->unbuffered_row('array');
    $result['default_currency'] = $poe['default_currency'];
    return $result;
  }

  public function findItemPo($id)
  {
    // $this->db->where('id', $id);
    // $query  = $this->db->get('tb_purchase_order_vendors');
    // $vendor    = $query->unbuffered_row('array');

    $select = array(
      'tb_po_item.poe_item_id as purchase_order_evaluation_items_vendors_id',
      'tb_po_item.id as id_item',
      'tb_po_item.part_number',
      'tb_po_item.alternate_part_number',
      'tb_po_item.description',
      'tb_po_item.remarks',
      'tb_po_item.quantity',
      'tb_po_item.unit_price',
      'tb_po_item.core_charge',
      'tb_po_item.total_amount',
      'tb_po_item.unit',
      'tb_po_item.poe_number as evaluation_number',
    );

    $this->db->select($select);
    $this->db->from('tb_po_item');
    $this->db->join('tb_po','tb_po.id = tb_po_item.purchase_order_id');
    $this->db->where('tb_po_item.purchase_order_id',$id);
    // $this->db->where('status_item','open');
    // $this->db->where('tb_purchase_order_items.vendor',$vendor['vendor']);
    $query  = $this->db->get();

    return $query->result_array();
  }

  public function findPo($id)
  {
    // $this->db->where('id', $id);
    // $query  = $this->db->get('tb_purchase_order_vendors');
    // $vendor    = $query->unbuffered_row('array');

    // $this->db->from('tb_master_vendors');
    // $this->db->where('vendor', $vendor['vendor']);
    // $query  = $this->db->get();
    // $row    = $query->unbuffered_row('array');
    // $result['vendor'] = $row['vendor'];
    // $result['vendor_address']    = $row['address'];
    // $result['vendor_country']    = $row['country'];
    // $result['vendor_attention']  = 'Phone: '. $row['phone'];

    $this->db->select('*');
    $this->db->from('tb_po');
    $this->db->where('id',$id);
    $query  = $this->db->get();
    $poe  = $query->unbuffered_row('array');
    // $result['default_currency'] = $poe['default_currency'];
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

  function reject($id,$notes){
    $this->db->set('review_status','REJECTED BY '.config_item('auth_role'));
    $this->db->set('status',strtoupper("rejected"));
    $this->db->set('notes',strtoupper($notes));
    $this->db->set('rejected_by',config_item('auth_person_name'));

    // $this->send_mail($id,$level);
    $this->db->where('id', $id);
    return $this->db->update('tb_po');
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
    $document_number      = order_format_number($_POST['category']).$_POST['document_number'];
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
    $payment_type        = $_POST['payment_type'];
    // $warehouse            = $_POST['warehouse'];
    // $category             = $_POST['category'];
    $notes                = (empty($_POST['notes'])) ? NULL : $_POST['notes'];

    $this->db->trans_begin();

    $this->db->select('sum(total_amount) as grand_total');
    $this->db->from('tb_purchase_order_items');
    $this->db->where('purchase_order_id', $id);
    $grand_total = $this->db->get()->row()->grand_total;

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
    $this->db->set('grand_total', $grand_total);
    $this->db->set('remaining_payment', $grand_total);
    $this->db->set('status', 'approved');
    $this->db->set('updated_at', date('Y-m-d'));
    $this->db->set('updated_by', config_item('auth_person_name'));
    $this->db->set('review_status', strtoupper('waiting for finance review'));
    $this->db->set('tipe', strtoupper($payment_type));
    $this->db->where('id', $id);
    $this->db->update('tb_purchase_orders');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function save_po()
  {
    $document_number      = order_format_number($_SESSION['order']['category']).$_SESSION['order']['document_number'];
    $document_date        = $_SESSION['order']['document_date'];
    $reference_quotation  = (empty($_SESSION['order']['reference_quotation'])) ? NULL : $_SESSION['order']['reference_quotation'];
    $issued_by            = (empty($_SESSION['order']['issued_by'])) ? NULL : $_SESSION['order']['issued_by'];
    $checked_by           = (empty($_SESSION['order']['checked_by'])) ? NULL : $_SESSION['order']['checked_by'];
    $approved_by          = (empty($_SESSION['order']['approved_by'])) ? NULL : $_SESSION['order']['approved_by'];
    $known_by             = (empty($_SESSION['order']['known_by'])) ? NULL : $_SESSION['order']['known_by'];
    $vendor               = $_SESSION['order']['vendor'];
    $vendor_address       = $_SESSION['order']['vendor_address'];
    $vendor_country       = $_SESSION['order']['vendor_country'];
    $vendor_phone         = $_SESSION['order']['vendor_phone'];
    $vendor_attention     = $_SESSION['order']['vendor_attention'];
    $deliver_company      = $_SESSION['order']['deliver_company'];
    $deliver_address      = $_SESSION['order']['deliver_address'];
    $deliver_country      = $_SESSION['order']['deliver_country'];
    $deliver_phone        = $_SESSION['order']['deliver_phone'];
    $deliver_attention    = $_SESSION['order']['deliver_attention'];
    $bill_company         = $_SESSION['order']['bill_company'];
    $bill_address         = $_SESSION['order']['bill_address'];
    $bill_country         = $_SESSION['order']['bill_country'];
    $bill_phone           = $_SESSION['order']['bill_phone'];
    $bill_attention       = $_SESSION['order']['bill_attention'];
    $default_currency     = $_SESSION['order']['default_currency'];
    $exchange_rate        = $_SESSION['order']['exchange_rate'];
    $discount             = $_SESSION['order']['discount'];
    $taxes                = $_SESSION['order']['taxes'];
    $shipping_cost        = $_SESSION['order']['shipping_cost'];
    $payment_type         = $_SESSION['order']['payment_type'];
    $warehouse            = $_SESSION['order']['warehouse'];
    $category             = $_SESSION['order']['category'];
    $notes                = (empty($_SESSION['order']['notes'])) ? NULL : $_SESSION['order']['notes'];
    $vendor_po               = $_SESSION['order']['vendor_po'];

    $this->db->trans_begin();

    $this->db->set('document_number', $document_number);
    $this->db->set('document_date', $document_date);
    $this->db->set('reference_quotation', $reference_quotation);
    $this->db->set('issued_by', $issued_by);
    $this->db->set('checked_by', $checked_by);
    $this->db->set('approved_by', $approved_by);
    $this->db->set('warehouse', $warehouse);
    $this->db->set('category', $category);
    $this->db->set('vendor', $vendor);
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
    $this->db->set('default_currency', $default_currency);
    $this->db->set('exchange_rate', $exchange_rate);
    $this->db->set('discount', $discount);
    $this->db->set('taxes', $taxes);
    $this->db->set('shipping_cost', $shipping_cost);
    $this->db->set('notes', $notes);
    $this->db->set('status', 'approved');
    $this->db->set('updated_at', date('Y-m-d'));
    $this->db->set('updated_by', config_item('auth_person_name'));
    $this->db->set('review_status', strtoupper('waiting for finance review'));
    $this->db->set('tipe', strtoupper($payment_type));
    // $this->db->where('id', $id);
    $this->db->insert('tb_po');

    $this->db->set('is_selected',false);
    $this->db->where('id', $vendor_po);
    $this->db->update('tb_purchase_order_vendors');

    $id_po = $this->db->insert_id();
    $total_qty = 0;
    $total_value = 0;

    foreach ($_SESSION['order']['items'] as $key => $item){
      $this->db->set('purchase_order_id', $id_po);
      $this->db->set('description', strtoupper($item['description']));
      $this->db->set('part_number', strtoupper($item['part_number']));
      $this->db->set('alternate_part_number', strtoupper($item['alternate_part_number']));
      $this->db->set('remarks', trim($item['remarks']));
      // $this->db->set('quantity_requested', floatval($item['quantity_requested']));
      // $this->db->set('unit_price_requested', floatval($item['unit_price_requested']));
      // $this->db->set('total_amount_requested', floatval($item['total_amount_requested']));
      $this->db->set('unit', trim($item['unit']));
      $this->db->set('poe_item_id', $item['purchase_order_evaluation_items_vendors_id']);
      // $this->db->set('alternate_part_number', strtoupper($detail['alternate_part_number']));
      // $this->db->set('purchase_request_number', strtoupper($detail['purchase_request_number']));
      // $this->db->set('vendor', strtoupper($detail['vendor']));
      $this->db->set('quantity', floatval($item['quantity']));
      $this->db->set('quantity_received', floatval(0));
      $this->db->set('left_received_quantity', floatval($item['quantity']));
      // $this->db->set('left_paid_quantity', floatval($detail['left_paid_quantity']));
      $this->db->set('unit_price', floatval($item['unit_price']));
      $this->db->set('core_charge', floatval($item['core_charge']));
      $this->db->set('total_amount', floatval($item['total_amount']));
      $this->db->set('left_paid_amount', floatval($item['total_amount']));
      $this->db->set('poe_number', $item['evaluation_number']);
      $this->db->insert('tb_po_item');
      $total_qty = $total_qty+$item['quantity'];
      $total_value = $total_value+$item['total_amount'];

      $this->db->set('quantity_received','"quantity_received" + '.$item['quantity'],false);
      $this->db->set('left_received_quantity','"left_received_quantity" - '.$item['quantity'],false);
      $this->db->where('id', $item['purchase_order_evaluation_items_vendors_id']);
      $this->db->update('tb_purchase_order_items');

      $this->db->where('id', $item['purchase_order_evaluation_items_vendors_id']);
      $detail_request = $this->db->get('tb_purchase_order_items')->row();
      if($detail_request->sisa == 0){
        $this->db->set('status_item','closed');
        $this->db->where('id', $item['purchase_order_evaluation_items_vendors_id']);
        $this->db->update('tb_purchase_order_items');
      }

    }

    $after_discount = $total_value - $discount;
    $total_taxes    = $after_discount * ( $taxes/100 );
    $after_taxes    = $after_discount + $total_taxes;
    $grandtotal     = $after_taxes + $shipping_cost;

    $this->db->set('total_quantity',floatval($total_qty));
    $this->db->set('total_price',floatval($total_value));
    $this->db->set('grand_total',floatval($grandtotal));
    $this->db->set('remaining_payment',floatval($grandtotal));
    $this->db->where('id', $id_po);
    $this->db->update('tb_po');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    $this->send_mail($id_po,14);
    return TRUE;
  }

  public function save_revisi_po()
  {
    $document_number      = order_format_number($_SESSION['order']['category']).$_SESSION['order']['document_number'];
    $document_date        = $_SESSION['order']['document_date'];
    $reference_quotation  = (empty($_SESSION['order']['reference_quotation'])) ? NULL : $_SESSION['order']['reference_quotation'];
    $issued_by            = (empty($_SESSION['order']['issued_by'])) ? NULL : $_SESSION['order']['issued_by'];
    $checked_by           = (empty($_SESSION['order']['checked_by'])) ? NULL : $_SESSION['order']['checked_by'];
    $approved_by          = (empty($_SESSION['order']['approved_by'])) ? NULL : $_SESSION['order']['approved_by'];
    $known_by             = (empty($_SESSION['order']['known_by'])) ? NULL : $_SESSION['order']['known_by'];
    $vendor               = $_SESSION['order']['vendor'];
    $vendor_address       = $_SESSION['order']['vendor_address'];
    $vendor_country       = $_SESSION['order']['vendor_country'];
    $vendor_phone         = $_SESSION['order']['vendor_phone'];
    $vendor_attention     = $_SESSION['order']['vendor_attention'];
    $deliver_company      = $_SESSION['order']['deliver_company'];
    $deliver_address      = $_SESSION['order']['deliver_address'];
    $deliver_country      = $_SESSION['order']['deliver_country'];
    $deliver_phone        = $_SESSION['order']['deliver_phone'];
    $deliver_attention    = $_SESSION['order']['deliver_attention'];
    $bill_company         = $_SESSION['order']['bill_company'];
    $bill_address         = $_SESSION['order']['bill_address'];
    $bill_country         = $_SESSION['order']['bill_country'];
    $bill_phone           = $_SESSION['order']['bill_phone'];
    $bill_attention       = $_SESSION['order']['bill_attention'];
    $default_currency     = $_SESSION['order']['default_currency'];
    $exchange_rate        = $_SESSION['order']['exchange_rate'];
    $discount             = $_SESSION['order']['discount'];
    $taxes                = $_SESSION['order']['taxes'];
    $shipping_cost        = $_SESSION['order']['shipping_cost'];
    $payment_type         = $_SESSION['order']['payment_type'];
    $warehouse            = $_SESSION['order']['warehouse'];
    $category             = $_SESSION['order']['category'];
    $notes                = (empty($_SESSION['order']['notes'])) ? NULL : $_SESSION['order']['notes'];
    $vendor_po            = $_SESSION['order']['vendor_po'];
    $id_po_lama           = $_SESSION['order']['id_po'];

    $this->db->trans_begin();

    $this->db->set('document_number', $document_number);
    $this->db->set('document_date', $document_date);
    $this->db->set('reference_quotation', $reference_quotation);
    $this->db->set('issued_by', $issued_by);
    $this->db->set('checked_by', $checked_by);
    $this->db->set('approved_by', $approved_by);
    $this->db->set('warehouse', $warehouse);
    $this->db->set('category', $category);
    $this->db->set('vendor', $vendor);
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
    $this->db->set('default_currency', $default_currency);
    $this->db->set('exchange_rate', $exchange_rate);
    $this->db->set('discount', $discount);
    $this->db->set('taxes', $taxes);
    $this->db->set('shipping_cost', $shipping_cost);
    $this->db->set('notes', $notes);
    $this->db->set('status', 'approved');
    $this->db->set('updated_at', date('Y-m-d'));
    $this->db->set('updated_by', config_item('auth_person_name'));
    $this->db->set('review_status', strtoupper('waiting for finance review'));
    $this->db->set('tipe', strtoupper($payment_type));
    // $this->db->where('id', $id);
    $this->db->insert('tb_po');
    $id_po = $this->db->insert_id();

    // $this->db->set('is_selected',false);
    // $this->db->where('id', $vendor_po);
    // $this->db->update('tb_purchase_order_vendors');
    $this->db->from('tb_po_item');
    $this->db->where('purchase_order_id',$id_po_lama);
    $query  = $this->db->get();
    $result = $query->result_array();

    foreach ($result as $data) {
      $this->db->set('quantity_received','"quantity_received" - '.$data['quantity'],false);
      $this->db->set('left_received_quantity','"left_received_quantity" + '.$data['quantity'],false);
      $this->db->where('id', $data['poe_item_id']);
      $this->db->update('tb_purchase_order_items');
    }
    $this->db->set('review_status','REVISI');
    $this->db->where('id', $id_po_lama);
    $this->db->update('tb_po');

    
    $total_qty = 0;
    $total_value = 0;

    foreach ($_SESSION['order']['items'] as $key => $item){
      $this->db->set('purchase_order_id', $id_po);
      $this->db->set('description', strtoupper($item['description']));
      $this->db->set('part_number', strtoupper($item['part_number']));
      $this->db->set('alternate_part_number', strtoupper($item['alternate_part_number']));
      $this->db->set('remarks', trim($item['remarks']));
      // $this->db->set('quantity_requested', floatval($item['quantity_requested']));
      // $this->db->set('unit_price_requested', floatval($item['unit_price_requested']));
      // $this->db->set('total_amount_requested', floatval($item['total_amount_requested']));
      $this->db->set('unit', trim($item['unit']));
      $this->db->set('poe_item_id', $item['purchase_order_evaluation_items_vendors_id']);
      // $this->db->set('alternate_part_number', strtoupper($detail['alternate_part_number']));
      // $this->db->set('purchase_request_number', strtoupper($detail['purchase_request_number']));
      // $this->db->set('vendor', strtoupper($detail['vendor']));
      $this->db->set('quantity', floatval($item['quantity']));
      $this->db->set('quantity_received', floatval(0));
      $this->db->set('left_received_quantity', floatval($item['quantity']));
      // $this->db->set('left_paid_quantity', floatval($detail['left_paid_quantity']));
      $this->db->set('unit_price', floatval($item['unit_price']));
      $this->db->set('core_charge', floatval($item['core_charge']));
      $this->db->set('total_amount', floatval($item['total_amount']));
      $this->db->set('left_paid_amount', floatval($item['total_amount']));
      $this->db->set('poe_number', $item['evaluation_number']);
      $this->db->insert('tb_po_item');
      $total_qty = $total_qty+$item['quantity'];
      $total_value = $total_value+$item['total_amount'];

      $this->db->set('quantity_received', $item['quantity']);
      $this->db->set('unit_price', $item['unit_price']);
      $this->db->set('core_charge', $item['core_charge']);
      $this->db->set('total_amount', floatval($item['total_amount']));
      $this->db->set('left_received_quantity',0);
      $this->db->set('quantity', floatval($item['quantity']));
      $this->db->set('status_item','closed');
      $this->db->where('id', $item['purchase_order_evaluation_items_vendors_id']);
      $this->db->update('tb_purchase_order_items');

      // $this->db->where('id', $item['purchase_order_evaluation_items_vendors_id']);
      // $detail_request = $this->db->get('tb_purchase_order_items')->row();
      // if($detail_request->sisa == 0){
      //   $this->db->set('status_item','closed');
      //   $this->db->where('id', $item['purchase_order_evaluation_items_vendors_id']);
      //   $this->db->update('tb_purchase_order_items');
      // }

    }

    $after_discount = $total_value - $discount;
    $total_taxes    = $after_discount * ( $taxes/100 );
    $after_taxes    = $after_discount + $total_taxes;
    $grandtotal     = $after_taxes + $shipping_cost;

    $this->db->set('total_quantity',floatval($total_qty));
    $this->db->set('total_price',floatval($total_value));
    $this->db->set('grand_total',floatval($grandtotal));
    $this->db->set('remaining_payment',floatval($grandtotal));
    $this->db->where('id', $id_po);
    $this->db->update('tb_po');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    $this->send_mail($id_po,14);
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
    // $this->db->select('tb_purchase_order_evaluation_vendors.id');
    // $this->db->from('tb_purchase_order_evaluation_vendors');
    // $this->db->where('UPPER(tb_purchase_order_evaluation_vendors.vendor)', $vendor);

    $subQuery =  $this->db->get_compiled_select();

    $select = array(
      'tb_purchase_order_items.*',
      'tb_purchase_orders.evaluation_number',
       'tb_purchase_orders.document_date',
      
    );

    $this->db->select($select);
    $this->db->from('tb_purchase_order_items');
    $this->db->join('tb_purchase_orders', 'tb_purchase_orders.id = tb_purchase_order_items.purchase_order_id');
    // $this->db->join('tb_purchase_order_evaluation_vendors', 'tb_purchase_order_evaluation_vendors.id = tb_purchase_order_evaluation_items_vendors.poe_vendor_id');
    // $this->db->join('tb_purchase_order_evaluations', 'tb_purchase_order_evaluations.document_number = tb_purchase_order_evaluation_items.document_number');
    $this->db->where('tb_purchase_orders.status', 'approved');
    $this->db->where('UPPER(tb_purchase_order_items.vendor)', $vendor);
    $this->db->where('tb_purchase_order_items.status_item', 'open');
    // $this->db->where('UPPER(tb_purchase_order_evaluation_vendors.vendor)', $vendor);
    // $this->db->where("tb_purchase_order_evaluation_items_vendors.poe_vendor_id IN ($subQuery)", NULL, FALSE);
    // $this->db->where('tb_purchase_order_evaluation_items_vendors.selected', 't');
    // $this->db->where('tb_purchase_order_evaluation_items_vendors.purchase_order_number IS NULL', null, false);

    $this->db->order_by('tb_purchase_order_items.id ASC');

    $query  = $this->db->get();
    $result = $query->result_array();

    return $result;
  }

  public function listAttachmentpoe($id){
    // $this->db->where('id',$id);
    // $this->db->from('tb_purchase_order_items');
    // $query  = $this->db->get();
    // $result = $query->unbuffered_row('array');
    // $poe_id = $result['purchase_order_id'];
    $poe_id = $id;

    $this->db->where('id_poe', $poe_id);
    return $this->db->get('tb_attachment_poe')->result();
  }

  public function send_mail($doc_id,$level) { 
    $this->db->from('tb_po');
    $this->db->where('id',$doc_id);
    $query = $this->db->get();
    $row = $query->unbuffered_row('array');

    $recipientList = $this->getNotifRecipient($level);
    $recipient = array();
    foreach ($recipientList as $key ) {
      array_push($recipient, $key->email);
    }

    $from_email = "bifa.acd@gmail.com"; 
    $to_email = "aidanurul99@rocketmail.com";
    $ket_level = '';
    if($level==14){
      $ket_level = 'Finance Manager';
    }elseif ($level==10) {
      $ket_level = 'Head Of School';
    } elseif($level==11){
      $ket_level = 'Chief Of Finance';
    }elseif($level==3){
      $ket_level = 'VP Finance';
    }
   
    //Load email library 
    $this->load->library('email'); 
    $config = array();
    $config['protocol'] = 'mail';
    $config['smtp_host'] = 'smtp.live.com';
    $config['smtp_user'] = 'bifa.acd@gmail.com';
    $config['smtp_pass'] = 'b1f42019';
    $config['smtp_port'] = 587;
    $config['smtp_auth']        = true;
    $config['mailtype']         = 'html';
    $this->email->initialize($config);
    $this->email->set_newline("\r\n");
    $message = "<p>Dear ".$ket_level."</p>";
    $message .= "<p>Berikut permintaan Persetujuan untuk Purchase Order :</p>";
    $message .= "<ul>";
    $message .= "</ul>";
    $message .= "<p>No Purchase Request : ".$row['document_number']."</p>";    
    $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
    $message .= "<p>[ <a href='http://119.252.163.206/mrp_demo/purchase_order/' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
    $message .= "<p>Thanks and regards</p>";
    $this->email->from($from_email, 'Material Resource Planning'); 
    $this->email->to($recipient);
    $this->email->subject('Permintaan Approval Purchase Order No : '.$row['document_number']); 
    $this->email->message($message); 
     
    //Send mail 
    if($this->email->send()) 
      return true; 
    else 
      return $this->email->print_debugger();
  }

  public function getNotifRecipient($level){
    $this->db->select('email');
    $this->db->from('tb_auth_users');
    $this->db->where('auth_level',$level);
    return $this->db->get('')->result();
  }

  public function send_mail_approval($id,$ket,$by) { 
    $item_message = '<tbody>';
    foreach ($id as $key) {  
      $this->db->select(
        array(
          'tb_po.document_number',
          'tb_po_item.description',
          'tb_po_item.part_number',
          'tb_po_item.quantity',
          'tb_po_item.total_amount',
          'tb_po_item.unit',
        )
      );    
      $this->db->from('tb_po_item');
      $this->db->join('tb_po','tb_po.id=tb_po_item.purchase_order_id');
      $this->db->where('tb_po.id',$key);
      $query = $this->db->get();
      $row = $query->result_array();

      foreach ($row as $item) {
        $item_message .= "<tr>";         
        $item_message .= "<td>".$item['document_number']."</td>";         
        $item_message .= "<td>".$item['part_number']."</td>";           
        $item_message .= "<td>".$item['description']."</td>";           
        $item_message .= "<td>".print_number($item['quantity'],2)."</td>";             
        $item_message .= "<td>".$item['unit']."</td>";          
        $item_message .= "<td>".print_number($item['total_amount'],2)."</td>";         
        $item_message .= "</tr>";
      }


      $this->db->select('issued_by');
      $this->db->from('tb_po');
      $this->db->where('id',$key);
      $query_po = $this->db->get();
      $row_po   = $query_po->unbuffered_row('array');
      $issued_by = $row_po['issued_by'];

      $recipientList = $this->getNotifRecipient_approval($issued_by);
      $recipient = array();
      foreach ($recipientList as $key ) {
        array_push($recipient, $key->email);
      }
    }
    $item_message .= '</tbody>';

    

    $from_email = "bifa.acd@gmail.com"; 
    $to_email = "aidanurul99@rocketmail.com";
    if($ket=='approve'){      
      $ket_level = 'Disetujui';
    }else{
      $ket_level = 'Ditolak';
    }
    // if($level==14){
    //   $ket_level = 'Finance Manager';
    // }elseif ($level==10) {
    //   $ket_level = 'Head Of School';
    // } elseif($level==11){
    //   $ket_level = 'Chief Of Finance';
    // }elseif($level==3){
    //   $ket_level = 'VP Finance';
    // }
   
    //Load email library 
    $this->load->library('email'); 
    $config = array();
    $config['protocol'] = 'mail';
    $config['smtp_host'] = 'smtp.live.com';
    $config['smtp_user'] = 'bifa.acd@gmail.com';
    $config['smtp_pass'] = 'b1f42019';
    $config['smtp_port'] = 587;
    $config['smtp_auth']        = true;
    $config['mailtype']         = 'html';
    $this->email->initialize($config);
    $this->email->set_newline("\r\n");
    $message = "<p>Hello</p>";
    $message .= "<p>Item Berikut telah ".$ket_level." oleh ".$by."</p>";
    $message .= "<table>";    
    $message .= "<thead>";   
    $message .= "<tr>";      
    $message .= "<th>No. Doc.</th>";       
    $message .= "<th>Part Number</th>";        
    $message .= "<th>Description</th>";        
    $message .= "<th>Qty Order</th>";        
    $message .= "<th>Unit</th>";        
    $message .= "<th>Total Val. Order</th>";    
    $message .= "</tr>";  
    $message .= "</thead>";
    $message .= $item_message;   
    $message .= "</table>";
    // $message .= "<p>No Purchase Request : ".$row['document_number']."</p>";    
    $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
    $message .= "<p>[ <a href='http://119.252.163.206/mrp_demo/purchase_order/' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
    $message .= "<p>Thanks and regards</p>";
    $this->email->from($from_email, 'Material Resource Planning'); 
    $this->email->to($recipient);
    $this->email->subject('Notification Approval'); 
    $this->email->message($message); 
     
    //Send mail 
    if($this->email->send()) 
      return true; 
    else 
      return $this->email->print_debugger();
  }

  public function getNotifRecipient_approval($name){
    $this->db->select('email');
    $this->db->from('tb_auth_users');
    $this->db->where('person_name',$name);
    return $this->db->get('')->result();
  }

  function import($data){
    $this->db->trans_begin();
    foreach ($data as $key) {
      // $check = $this->checkImport($key["document_no"],$key["poe_number"]);
      $company    = find_budget_setting('Company Name', 'head company');
      $address    = nl2br(find_budget_setting('Address', 'head company'));
      $country    = 'INDONESIA';
      $phone      = find_budget_setting('Phone No', 'head company');
      $attention  = 'Attn. Umar Satrio, Mobile. +62 081333312392';
      
      $check = $this->checkImport($key["document_no"]);
      $time = strtotime($key["date"]);
      $date = date("Y-m-d",$time);
      $po_id = "";
      if ($check != 1){
        if ($check == 0){
            $this->db->set('document_number',$key["document_no"]);
            $this->db->set('review_status',strtoupper("APPROVED"));
            $this->db->set('status',strtoupper("Order"));
            $this->db->set('document_date','2019-02-01');
            $this->db->set('category',$key["kategori"]);
            $this->db->set('evaluation_number',$key["poe_number"]);
            $this->db->set('reference_quotation',$key["ref_quot"]);
            $this->db->set('vendor',$key["vendor"]);
            $this->db->set('notes',$key["notes"]);
            $this->db->set('warehouse','WISNU');
            // $this->db->insert('tb_purchase_orders');
            $this->db->insert('tb_po');
            $po_id = $this->db->insert_id();
        } else {
            $this->db->select('id');
            $this->db->where('document_number', $key["document_no"]);
            $data = $this->db->get('tb_po')->row();
            $po_id = $data->id;
        }
            $this->db->set('purchase_order_id',$po_id);
            $this->db->set('description',$key["description"]);
            $this->db->set('part_number',$key["part_number"]);
            $this->db->set('alternate_part_number',$key["alt_part"]);
            $this->db->set('purchase_request_number',$key["pr_number"]);
            $this->db->set('poe_number',$key["poe_number"]);
            $this->db->set('quantity',str_replace(",", "", $key["order_qty"]));
            $this->db->set('quantity_requested',str_replace(",", "", $key["request_qty"]));
            $left_received_quantity = $key["request_qty"] - $key["receive_qty"];
            $this->db->set('left_received_quantity',$left_received_quantity);
            $this->db->set('unit_price',str_replace(",", "", $key["unit_price"]));
            $this->db->set('core_charge',str_replace(",", "", $key["core_charge"]));
            $this->db->set('total_amount',str_replace(",", "", $key["total_amount"]));
            $left_paid_amount = $key["total_amount"] - $key["paid_amount"];
            $this->db->set('left_paid_amount',$left_paid_amount);
            $this->db->set('unit',$key["unit"]);
            // $this->db->insert('tb_purchase_order_items');
            $this->db->insert('tb_po_item');

            $this->db->set('total_quantity','"total_quantity" + '.$key['order_qty'],false);
            $this->db->set('total_price','"total_price" + '.$key['total_amount'],false);
            $this->db->set('grand_total','"grand_total" + '.$key['total_amount'],false);
            $this->db->set('remaining_payment','"remaining_payment" + '.$key['total_amount'],false);
            $this->db->where('id', $po_id);
            $this->db->update('tb_po');
      }
    }
    if ($this->db->trans_status() === FALSE){
      return FALSE;
    }

    $this->db->trans_commit();
    return TRUE;
      
  }
  function checkImport($po_no){
    $this->db->where('document_number', $po_no);
    // $this->db->or_where('evaluation_number', $poe_number);
    $data = $this->db->get('tb_po');
    if ($data->num_rows() == 0) {
      return 0;
    } else if ($data->num_rows() == 1){
      $po = $data->row();
      // if(($po->document_number == $po_no) && ($po->evaluation_number == $poe_number)){
        // return 2;
      // } else {
        return 1;
      // }
    } else {
      return 1;
    }
    
  }

  public function getSelectedColumnsReport()
  {
    
      return array(        
        // "''".' as "temp"' => "Act.", 
        'tb_po.id' => NULL,
        'tb_po.document_number'              => 'Document Number',
        'tb_po.review_status'                => 'Review Status',
        'tb_po.document_date'                => 'Date',
        'tb_po.category'        => 'Category',
        'tb_po_item.description'             => 'Description',
        'tb_po_item.part_number'             => 'Part Number',
        'tb_po_item.alternate_part_number'   => 'Alt. Part Number',
        'tb_po_item.poe_number'            => 'Ref. POE',
        'tb_purchase_order_items.purchase_request_number' => 'Ref. PR',
        'tb_po.reference_quotation'          => 'Ref. Quotation',
        'tb_po.vendor'                       => 'Vendor',
        'tb_po_item.quantity'                => 'Order Qty',
        'tb_po_item.unit_price'              => 'Unit Price',
        'tb_po_item.core_charge'             => 'Core Charge',
        'tb_po_item.total_amount'            => 'Total Amount',
        '(tb_po_item.quantity - tb_po_item.left_received_quantity) AS quantity_received' => 'Received Qty',
        'tb_po_item.left_received_quantity'      => 'Left Qty',
        // '(tb_po_item.total_amount - tb_po_item.left_paid_amount) AS amount_paid' => 'Paid Amount',
        'tb_po.notes'                        => 'Notes',
        // 'tb_po.approved_by_hos'              => null,
        // 'tb_po.approved_by_cof'              => null,
        'tb_purchase_orders.id as poe_id'              => null,
        'tb_purchase_order_items.id as poe_item_id'              => null
        
      );      
  }

  public function getSearchableColumnsReport()
  {
    return array(
      'tb_po.document_number',
      'tb_po.category',
      'tb_po_item.description',
      'tb_po_item.part_number',
      'tb_po_item.alternate_part_number',
      'tb_po_item.evaluation_number',
      'tb_purchase_order_items.purchase_request_number',
      'tb_po.reference_quotation',
      'tb_po.vendor',
      'tb_po.notes',
      "'review_status'",
    );
  }

  public function getOrderableColumnsReport()
  {
    return array(
      null,
      null,
      'tb_po.document_number',
      'tb_po.review_status',
      'tb_po.document_date',
      'tb_po.category',
      'tb_po_item.description',
      'tb_po_item.part_number',
      'tb_po_item.alternate_part_number',
      'tb_po_item.evaluation_number',
      'tb_purchase_order_items.purchase_request_number',
      'tb_po.reference_quotation',
      'tb_po.vendor',
      'tb_po_item.quantity',
      'tb_po_item.quantity_requested',
      '(tb_po_item.quantity - tb_po_item.left_received_quantity)',
      'tb_po_item.unit_price',
      'tb_po_item.core_charge',
      'tb_po_item.total_amount',
      // '(tb_po_item.total_amount - tb_po_item.left_paid_amount) AS amount_paid',
      'tb_po.notes',

    );
  }

  
  private function searchIndexReport()
  {
    if (!empty($_POST['columns'][3]['search']['value'])){
      $search_category = $_POST['columns'][3]['search']['value'];

      $this->db->where('tb_po.category', $search_category);
    }

    if (!empty($_POST['columns'][2]['search']['value'])){
      $search_document_date = $_POST['columns'][2]['search']['value'];
      $range_document_date  = explode(' ', $search_document_date);

      $this->db->where('tb_po.document_date >= ', $range_document_date[0]);
      $this->db->where('tb_po.document_date <= ', $range_document_date[1]);
    }

    if (!empty($_POST['columns'][4]['search']['value'])){
      $status = $_POST['columns'][4]['search']['value'];
      if($status=='review'){
        $this->db->like('tb_po.review_status', 'WAITING');
      }elseif($status=='approved'){
        $this->db->where('tb_po.review_status', strtoupper($status));
      }elseif($status=='review_approved'){
        if(config_item('auth_role') == 'FINANCE MANAGER'){
          $this->db->like('tb_po.review_status', 'WAITING FOR HOS');
        }
        if(config_item('auth_role') == 'HEAD OF SCHOOL'){
          $this->db->like('tb_po.review_status', 'WAITING FOR VP FINANCE');
        }
        if(config_item('auth_role') == 'VP FINANCE'){
          $this->db->like('tb_po.review_status', 'WAITING FOR COF');
        }
        if(config_item('auth_role') == 'CHIEF OF FINANCE'){
          $this->db->like('tb_po.review_status', 'APPROVED');
        }
      }
      // elseif($status=='all'){
      //   $this->db->like('tb_po.review_status', 'WAITING');
      // }
    }else{
      if(config_item('auth_role') == 'FINANCE MANAGER'){
        $this->db->like('tb_po.review_status', 'WAITING FOR FINANCE');
      }
      if(config_item('auth_role') == 'HEAD OF SCHOOL'){
        $this->db->like('tb_po.review_status', 'WAITING FOR HOS');
      }
      if(config_item('auth_role') == 'VP FINANCE'){
        $this->db->like('tb_po.review_status', 'WAITING FOR VP FINANCE');
      }
      if(config_item('auth_role') == 'CHIEF OF FINANCE'){
        $this->db->like('tb_po.review_status', 'WAITING FOR COF');
      }
      // $this->db->where_not_in('tb_po.review_status', ['REVISI']);
      // else{
      //   $this->db->where('tb_po.review_status','!=','REVISI');
      // }
    }
    // else{
    //   $this->db->like('tb_po.review_status', 'WAITING');
    // }
    if (!empty($_POST['columns'][1]['search']['value'])){
      $vendor = $_POST['columns'][1]['search']['value'];
      if ($vendor!='all') {
        $this->db->where('tb_po.vendor',$vendor);
      }
    }

    $i = 0;

    foreach ($this->getSearchableColumnsReport() as $item){
      if ($_POST['search']['value']){
        $term = strtoupper($_POST['search']['value']);

        if ($i === 0){
          $this->db->group_start();
          $this->db->like('UPPER('.$item.')', $term);
        } else {
          $this->db->or_like('UPPER('.$item.')', $term);
        }

        if (count($this->getSearchableColumnsReport()) - 1 == $i)
          $this->db->group_end();
      }

      $i++;
    }
  }

  function getIndexReport($return = 'array')
  {
    
    $this->db->select(array_keys($this->getSelectedColumnsReport()));
    $this->db->from('tb_po_item');
    $this->db->join('tb_po', 'tb_po.id = tb_po_item.purchase_order_id');
    $this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.id = tb_po_item.poe_item_id','LEFT');
    $this->db->join('tb_purchase_orders', 'tb_purchase_orders.id = tb_purchase_order_items.purchase_order_id','LEFT');
    // $this->db->where('tb_po.review_status','!=','REVISI');
    $this->db->where_in('tb_po.category', config_item('auth_inventory'));

    // if (config_item('auth_role') == 'FINANCE'){
    //   $this->db->where('tb_purchase_order_items.left_paid_amount > ', 0);
    // }

    $this->searchIndexReport();

    $column_order = $this->getOrderableColumnsReport();

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

  function countIndexFilteredReport()
  {
    // $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_po_item');
    $this->db->join('tb_po', 'tb_po.id = tb_po_item.purchase_order_id');
    $this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.id = tb_po_item.poe_item_id');
    $this->db->join('tb_purchase_orders', 'tb_purchase_orders.id = tb_purchase_order_items.purchase_order_id');
    // $this->db->where('tb_po.status', 'approved');
    $this->db->where_in('tb_po.category', config_item('auth_inventory'));

    $this->searchIndexReport();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndexReport()
  {
    // $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_po_item');
    $this->db->join('tb_po', 'tb_po.id = tb_po_item.purchase_order_id');
    $this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.id = tb_po_item.poe_item_id');
    $this->db->join('tb_purchase_orders', 'tb_purchase_orders.id = tb_purchase_order_items.purchase_order_id');
    // $this->db->where('tb_po.status', 'approved');
    $this->db->where_in('tb_po.category', config_item('auth_inventory'));

    $query = $this->db->get();

    return $query->num_rows();
  }

}
