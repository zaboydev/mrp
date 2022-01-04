<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Account_Payable_Model extends MY_Model
{
  protected $connection;

  public function __construct()
  {
    parent::__construct();
    //Do your magic here
    $this->connection   = $this->load->database('budgetcontrol', TRUE);
  }
  public function getSelectedColumns()
  {
    return array(
      // "''".' as "temp"' => "Act.", 
      'tb_po.id' => NULL,
      'tb_po.document_date'                => 'Date',
      'tb_po.document_number'              => 'Document Number',
      'tb_po.vendor'                       => 'Vendor',
      'tb_po.grand_total'                  => 'Total Amount',
      'tb_po.payment'                      => 'Amount Due',
      'tb_po.remaining_payment'            => 'Remaining Payment',
      'tb_po.status'                       => 'Review Status',
      'tb_po.tipe_po'                                 => 'Evaluation Number',
      'tb_po.evaluation_number'            => 'Evaluation Att',
      // 'tb_po.category'        => 'Category',


    );
  }
  public function getSearchableColumns()
  {
    $return = array(
      // 'tb_po.id',
      'tb_po.document_number',
      'tb_po.status',
      // 'tb_po.document_date',
      'tb_po.vendor'

    );

    return $return;
  }

  public function getGroupedColumns()
  {
    return array(
      'tb_po.id',
      'tb_po.document_number',
      'tb_po.status',
      'tb_po.document_date',
      'tb_po.vendor'
    );
  }

  public function getOrderableColumns()
  {
    $return = array(
      'tb_po.id',
      'tb_po.document_number',
      'tb_po.status',
      'tb_po.document_date',
      'tb_po.vendor'
      // 'tb_po_item.total_amount',
    );
    return $return;
  }

  private function searchIndex()
  {
    if (!empty($_POST['columns'][2]['search']['value'])) {
      $search_received_date = $_POST['columns'][2]['search']['value'];
      $range_received_date  = explode(' ', $search_received_date);

      $this->db->where('tb_po.document_date >= ', $range_received_date[0]);
      $this->db->where('tb_po.document_date <= ', $range_received_date[1]);
    }

    if (!empty($_POST['columns'][1]['search']['value'])) {
      $vendor = $_POST['columns'][1]['search']['value'];

      $this->db->where('tb_po.vendor', $vendor);
    }

    if (!empty($_POST['columns'][3]['search']['value'])) {
      $status = $_POST['columns'][3]['search']['value'];

      if($status != 'all'){
        $this->db->where('tb_po.status', $status);
      }
    }

    if (!empty($_POST['columns'][4]['search']['value'])) {
      $tipe_po = $_POST['columns'][4]['search']['value'];

      if($tipe_po != 'all'){
        if($tipe_po=='po_local'){
          $this->db->where_in('tb_po.tipe_po', ['INVENTORY','CAPEX','EXPENSE']);
        }elseif($tipe_po=='maintenance'){
          $this->db->where_in('tb_po.tipe_po', ['INVENTORY MRP']);
        }else{
          $this->db->where('tb_po.tipe_po', $tipe_po);
        }        
      }
    }else{
      $tipe_po = $_SESSION['ap']['tipe_po'];
      if($tipe_po=='po_local'){
          $this->db->where_in('tb_po.tipe_po', ['INVENTORY','CAPEX','EXPENSE']);
      }elseif($tipe_po=='maintenance'){
        $this->db->where_in('tb_po.tipe_po', ['INVENTORY MRP']);
      }
    }

    $i = 0;
    foreach ($this->getSearchableColumns() as $item) {
      if ($_POST['search']['value']) {
        $term = strtoupper($_POST['search']['value']);

        if ($i === 0) {
          $this->db->group_start();
          $this->db->like('UPPER(' . $item . ')', $term);
        } else {
          $this->db->or_like('UPPER(' . $item . ')', $term);
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
    $this->db->from('tb_po');
    $this->db->where_in('tb_po.status', ['ORDER', 'OPEN', 'CLOSED','ADVANCE']);
    // $this->db->where_in('tb_po.category', config_item('auth_inventory'));
    // $this->db->join('tb_po_item','tb_po.purchase_order_id=tb_po_item.id');
    // $this->db->group_by($this->getGroupedColumns());
    $this->searchIndex();
    $column_order = $this->getOrderableColumns();

    if (isset($_POST['order'])) {
      foreach ($_POST['order'] as $key => $order) {
        $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('id', 'desc');
    }

    if ($_POST['length'] != -1)
      $this->db->limit($_POST['length'], $_POST['start']);

    $query = $this->db->get();

    if ($return === 'object') {
      return $query->result();
    } elseif ($return === 'json') {
      return json_encode($query->result());
    } else {
      return $query->result_array();
    }
  }

  function countIndexFiltered()
  {
    $this->db->from('tb_po');
    $this->db->where_in('tb_po.status', ['ORDER', 'OPEN', 'CLOSED', 'ADVANCE']);
    // $this->db->where_in('tb_po.category', config_item('auth_inventory'));
    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->from('tb_po');
    $this->db->where_in('tb_po.status', ['ORDER', 'OPEN', 'CLOSED', 'ADVANCE']);
    // $this->db->where_in('tb_po.category', config_item('auth_inventory'));
    $query = $this->db->get();

    return $query->num_rows();
  }
  public function findById($id)
  {
    $this->db->where('id', $id);

    $query    = $this->db->get('tb_po');
    $po = $query->unbuffered_row('array');

    $select = array(
      'tb_po_item.*',
      'tb_po_item.purchase_request_number',
      'tb_purchase_order_items.id as poe_item_id',
      'tb_purchase_order_items.inventory_purchase_request_detail_id as request_item_id',
      
    );

    $this->db->select($select);
    $this->db->from('tb_po_item');
    $this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.id = tb_po_item.poe_item_id', 'LEFT');    
    $this->db->where('tb_po_item.purchase_order_id', $po['id']);

    $query = $this->db->get();

    foreach ($query->result_array() as $key => $value) {
      $po['items'][$key] = $value;
      $po['items'][$key]['request_id'] = $this->getRequestId($value['request_item_id'],$po['tipe_po']);
      $po['items'][$key]['receipts'] = $this->getReceiptItems($value['id']);
    }

    $select_payment = array(
      'tb_po_item.part_number',
      'tb_po_item.description',
      'tb_po_item.total_amount',
      'tb_purchase_order_items_payments.*'
    );

    $this->db->select($select_payment);
    $this->db->from('tb_purchase_order_items_payments');
    $this->db->join('tb_po_item','tb_po_item.id=tb_purchase_order_items_payments.purchase_order_item_id');
    $this->db->where('tb_po_item.purchase_order_id', $po['id']);

    $query_payment = $this->db->get();

    foreach ($query_payment->result_array() as $key => $value) {
      $po['payments'][$key] = $value;
    }
    $po['count_payment'] = $query_payment->num_rows();

    return $po;
  }

  public function getReceiptItems($purchase_order_item_id){
    $select = array(
      'tb_receipts.id',
      'tb_receipts.document_number',
      'tb_receipts.received_date',
      'tb_receipts.received_by',
      'tb_receipt_items.received_quantity',
      'tb_receipt_items.received_unit_value',
      'tb_receipt_items.received_total_value',
      
    );

    $this->db->select($select);
    $this->db->from('tb_receipt_items');
    $this->db->join('tb_receipts', 'tb_receipts.document_number = tb_receipt_items.document_number');    
    $this->db->where('tb_receipt_items.purchase_order_item_id', $purchase_order_item_id);

    $query = $this->db->get();

    return $query->result_array();
  }

  public function urgent($id)
  {
    $this->db->where('id', $id);
    $this->db->set('status', 'urgent');
    return $this->db->update('tb_hutang');
  }

  public function getNotifRecipient()
  {
    $this->db->select('email');
    $this->db->from('tb_auth_users');
    $this->db->where('auth_level', 2);
    return $this->db->get('')->result();
  }

  public function getEvaluationNumber($po_id)
  {
    $this->db->select('poe_number');
    $this->db->where('purchase_order_id', $po_id);
    $query    = $this->db->get('tb_po_item');
    $item_po = $query->unbuffered_row('array');
    return $item_po['poe_number'];
  }

  public function getEvaluationId($po_id)
  {
    $no_evaluasi = $this->getEvaluationNumber($po_id);
    $this->db->select('id');
    $this->db->where('evaluation_number', $no_evaluasi);
    $query    = $this->db->get('tb_purchase_orders');
    $evaluation = $query->unbuffered_row();
    return $evaluation->id;
  }

  public function getRequestId($request_item_id,$tipe)
  {
    $return = 0;
    if($tipe=='INVENTORY MRP'){
      $this->db->select('inventory_purchase_requisition_id');
      $this->db->where('id', $request_item_id);
      $this->db->from('tb_inventory_purchase_requisition_details');
      $query    = $this->db->get();
      $request = $query->unbuffered_row();
      $return = $request->inventory_purchase_requisition_id;
    }

    if($tipe=='INVENTORY'){
      $this->connection->select('inventory_purchase_requisition_id');
      $this->connection->where('id', $request_item_id);
      $this->connection->from('tb_inventory_purchase_requisition_details');
      $query    = $this->connection->get();
      $request = $query->unbuffered_row();
      $return = $request->inventory_purchase_requisition_id;
    }

    if($tipe=='EXPENSE'){
      $this->connection->select('expense_purchase_requisition_id');
      $this->connection->where('id', $request_item_id);
      $this->connection->from('tb_expense_purchase_requisition_details');
      $query    = $this->connection->get();
      $request = $query->unbuffered_row();
      $return = $request->expense_purchase_requisition_id;
    }

    if($tipe=='CAPEX'){
      $this->connection->select('capex_purchase_requisition_id');
      $this->connection->where('id', $request_item_id);
      $this->connection->from('tb_capex_purchase_requisition_details');
      $query    = $this->connection->get();
      $request = $query->unbuffered_row();
      $return = $request->capex_purchase_requisition_id;
    }
    return $return;
  }

  public function getSelectedPoe($poe_id)
  {
    // $no_evaluasi = $this->getEvaluationNumber($po_id);
    $this->db->select('*');
    $this->db->where('id', $poe_id);
    $query    = $this->db->get('tb_purchase_orders');
    $evaluation = $query->unbuffered_row();
    return $evaluation;
  }

  public function listAttachmentPoePo($id,$tipe)
  {
    $this->db->where('id_poe', $id);
    $this->db->where('tipe', $tipe);
    $this->db->where(array('deleted_at' => NULL));
    return $this->db->get('tb_attachment_poe')->result();
  }

  public function listAttachmentRequest($poe_id,$tipe)
  {
    $this->db->select('inventory_purchase_request_detail_id');
    $this->db->where('purchase_order_id', $poe_id);
    $query    = $this->db->get('tb_purchase_order_items');    
    $result = $query->result_array();
    $request_item_id = array();

    foreach ($result as $row) {
      $request_item_id[] = $row['inventory_purchase_request_detail_id'];
    }

    //get request id
    if($tipe=='EXPENSE'){
      $this->connection->select('expense_purchase_requisition_id');
      $this->connection->where_in('id', $request_item_id);
      $queryrequest_id    = $this->connection->get('tb_expense_purchase_requisition_details');    
      $resultrequest_id = $queryrequest_id->result_array();
      $request_id = array();

      foreach ($resultrequest_id as $row) {
        $request_id[] = $row['expense_purchase_requisition_id'];
      }
      $tipe_request = 'expense';

    }
    if($tipe=='CAPEX'){
      $this->connection->select('capex_purchase_requisition_id');
      $this->connection->where_in('id', $request_item_id);
      $queryrequest_id    = $this->connection->get('tb_capex_purchase_requisition_details');    
      $resultrequest_id = $queryrequest_id->result_array();
      $request_id = array();

      foreach ($resultrequest_id as $row) {
        $request_id[] = $row['capex_purchase_requisition_id'];
      }
      $tipe_request = 'capex';

    }

    if($tipe=='INVENTORY'){
      $this->connection->select('inventory_purchase_requisition_id');
      $this->connection->where_in('id', $request_item_id);
      $queryrequest_id    = $this->connection->get('tb_inventory_purchase_requisition_details');    
      $resultrequest_id = $queryrequest_id->result_array();
      $request_id = array();

      foreach ($resultrequest_id as $row) {
        $request_id[] = $row['inventory_purchase_requisition_id'];
      }
      $tipe_request = 'inventory';

    }

    if($tipe=='EXPENSE' || $tipe=='CAPEX' || $tipe=='INVENTORY'){
      $this->connection->where_in('id_purchase', $request_id);
      $this->connection->where('tipe', $tipe_request);
      return $this->connection->get('tb_attachment')->result();
    }else{
      return [];
    }
    
  }
}

/* End of file Account_Payable_Model.php */
/* Location: ./application/models/Account_Payable_Model.php */
