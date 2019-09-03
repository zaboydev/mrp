<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pr_Po_Model extends MY_Model
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
      'tb_inventory_purchase_requisition_details.id'                          => NULL,
      'tb_inventory_purchase_requisition_details.part_number'                 => 'Part Number',
      'tb_inventory_purchase_requisition_details.product_name'                => 'Description',
      'tb_inventory_purchase_requisitions.pr_number'                          => 'PR Number',
      'tb_inventory_purchase_requisition_details.quantity as "pr_qty"'        => 'Req. Qty',
      // 'tb_inventory_purchase_requisition_details.total as "pr_val"'                => 'PR Val',
      // 'tb_purchase_orders.document_number as "po_number"'                     => 'PO Number',
      'sum(tb_purchase_order_items.quantity_received) as "po_qty"'            => 'PO QTY',
      'tb_purchase_order_items.unit_price'                 => 'PO VAL',
      'tb_inventory_purchase_requisition_details.sisa'                        => 'PR Amount Remaining',
    );
      
  }
  public function getSearchableColumns()
  {
    return array(
      'tb_inventory_purchase_requisitions.pr_number',
      // 'tb_purchase_orders.document_number as "po_number',
      'tb_inventory_purchase_requisition_details.part_number'
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'tb_inventory_purchase_requisitions.pr_number',
      'tb_inventory_purchase_requisition_details.quantity',
      'tb_inventory_purchase_requisition_details.total',
      // 'tb_purchase_orders.document_number',
      '(sum(tb_purchase_order_items.quantity_received))',
      '(sum(tb_purchase_order_items.total_amount))',
      'tb_inventory_purchase_requisition_details.sisa',
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
    
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_inventory_purchase_requisitions');
    $this->db->join('tb_inventory_purchase_requisition_details', 'tb_inventory_purchase_requisitions.id = tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id');
    $this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.inventory_purchase_request_detail_id = tb_inventory_purchase_requisition_details.id','left');
    $this->db->join('tb_purchase_orders', 'tb_purchase_order_items.purchase_order_id = tb_purchase_orders.id','left');
    $this->db->group_by('tb_inventory_purchase_requisitions.pr_number');
    // $this->db->group_by('tb_inventory_purchase_requisition_details.quantity');
    // $this->db->group_by('tb_inventory_purchase_requisition_details.total');
    // $this->db->group_by('tb_purchase_orders.document_number');
    $this->db->group_by('tb_inventory_purchase_requisition_details.sisa');
    $this->db->group_by('tb_inventory_purchase_requisition_details.id');
    $this->db->group_by('tb_purchase_order_items.unit_price');
    $this->searchIndex();

    $column_order = $this->getOrderableColumns();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('id', 'asc');
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
    $this->db->from('tb_inventory_purchase_requisitions');
    $this->db->join('tb_inventory_purchase_requisition_details', 'tb_inventory_purchase_requisitions.id = tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id');
    $this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.inventory_purchase_request_detail_id = tb_inventory_purchase_requisition_details.id','left');
    $this->db->join('tb_purchase_orders', 'tb_purchase_order_items.purchase_order_id = tb_purchase_orders.id','left');
   $this->db->group_by('tb_inventory_purchase_requisitions.pr_number');
    // $this->db->group_by('tb_inventory_purchase_requisition_details.quantity');
    // $this->db->group_by('tb_inventory_purchase_requisition_details.total');
    // $this->db->group_by('tb_purchase_orders.document_number');
    $this->db->group_by('tb_inventory_purchase_requisition_details.sisa');
    $this->db->group_by('tb_inventory_purchase_requisition_details.id');
    $this->db->group_by('tb_purchase_order_items.unit_price');
    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }
  
  public function countIndex()
  {
     $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_inventory_purchase_requisitions');
    $this->db->join('tb_inventory_purchase_requisition_details', 'tb_inventory_purchase_requisitions.id = tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id');
    $this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.inventory_purchase_request_detail_id = tb_inventory_purchase_requisition_details.id','left');
    $this->db->join('tb_purchase_orders', 'tb_purchase_order_items.purchase_order_id = tb_purchase_orders.id','left');
    $this->db->group_by('tb_inventory_purchase_requisitions.pr_number');
    // $this->db->group_by('tb_inventory_purchase_requisition_details.quantity');
    // $this->db->group_by('tb_inventory_purchase_requisition_details.total');
    // $this->db->group_by('tb_purchase_orders.document_number');
    $this->db->group_by('tb_inventory_purchase_requisition_details.sisa');
    $this->db->group_by('tb_inventory_purchase_requisition_details.id');
    $this->db->group_by('tb_purchase_order_items.unit_price');
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
