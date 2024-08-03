<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Grn_Payment_Model_2 extends MY_Model
{
  protected $connection;
  protected $budget_year;
  protected $budget_month;

  public function __construct()
  {
    parent::__construct();

    $this->connection   = $this->load->database('budgetcontrol', TRUE);
  }
  public function loadBase(){
    return $this->db->get('tb_master_warehouses')->result();
  }
  public function getSelectedColumns()
  {
    return array(
      'tb_po.id' => NULL,
      'tb_po.document_number'                             => 'PO Number',
      'tb_po.document_date'                               => 'Date',
      'tb_po.vendor'                                      => 'Vendor',
      'tb_po.default_currency'                            => 'Currency',
      'tb_po.grand_total'                                 => 'Value Order',
      'case when tb_purchase_order_items_payments.amount_paid is null then 0.00 else tb_purchase_order_items_payments.amount_paid end as "value_payment"'                     => 'Value Payment',
      'tb_po.remaining_payment'                => 'Remaining Payment',
    );
      
  }
  public function getSearchableColumns()
  {
    return array(
      'tb_po.document_number',
      'tb_po.document_date',
      'tb_po.default_currency',
      'tb_po.vendor',
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'tb_po.document_number',
      'tb_po.document_date',
      'tb_po.default_currency',
      'tb_po.vendor',
    );
  }
 
  private function searchIndex()
  {
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
    
    $this->db->select(array_keys($this->getSelectedColumns()),false);
    $this->db->from('tb_po');
    $this->db->join('tb_purchase_order_items_payments', 'tb_purchase_order_items_payments.purchase_order_item_id = tb_po.id','left');
    $this->db->where_in('tb_po.status',['ORDER','OPEN','CLOSE']);
    // $this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.id  = tb_receipt_items.purchase_order_item_id','left');
    // $this->db->join('tb_purchase_orders', 'tb_purchase_order_items.purchase_order_id = tb_purchase_orders.id','left');
    // $this->db->join('tb_purchase_order_items_payments', 'tb_purchase_order_items_payments.purchase_order_item_id = tb_purchase_orders.id ','left');
    // $this->db->like('tb_receipts.document_number', 'GRN');
    
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
    $this->db->select(array_keys($this->getSelectedColumns()),false);
    $this->db->from('tb_po');
    $this->db->join('tb_purchase_order_items_payments', 'tb_purchase_order_items_payments.purchase_order_item_id = tb_po.id','left');
    $this->db->where_in('tb_po.status',['ORDER','OPEN','CLOSE']);
    // $this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.id  = tb_receipt_items.purchase_order_item_id','left');
    // $this->db->join('tb_purchase_orders', 'tb_purchase_order_items.purchase_order_id = tb_purchase_orders.id','left');
    // $this->db->join('tb_purchase_order_items_payments', 'tb_purchase_order_items_payments.purchase_order_item_id = tb_purchase_orders.id ','left');
    // $this->db->like('tb_receipts.document_number', 'GRN');
    
    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }
  
  public function countIndex()
  {
    $this->db->select(array_keys($this->getSelectedColumns()),false);
    $this->db->from('tb_po');
    $this->db->join('tb_purchase_order_items_payments', 'tb_purchase_order_items_payments.purchase_order_item_id = tb_po.id','left');
    $this->db->where_in('tb_po.status',['ORDER','OPEN','CLOSE']);
    // $this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.id  = tb_receipt_items.purchase_order_item_id','left');
    // $this->db->join('tb_purchase_orders', 'tb_purchase_order_items.purchase_order_id = tb_purchase_orders.id','left');
    // $this->db->join('tb_purchase_order_items_payments', 'tb_purchase_order_items_payments.purchase_order_item_id = tb_purchase_orders.id ','left');
    // $this->db->like('tb_receipts.document_number', 'GRN');
    
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

  
}
