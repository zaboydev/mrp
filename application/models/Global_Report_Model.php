<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Global_Report_Model extends MY_Model
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
      'tb_inventory_purchase_requisitions.pr_number'                          => 'No Request',
      'tb_master_items.description'                                           => 'Item',
      'tb_master_items.part_number'                                           => 'Part Number',
      'tb_inventory_purchase_requisition_details.quantity as "pr_qty"'        => 'PR QTY',
      'tb_inventory_purchase_requisition_details.total as "pr_val"'           => 'PR VAL',
      'sum(case when tb_purchase_order_items.quantity is null then 0.00 else tb_purchase_order_items.quantity 
end) as "poe_qty"'                                                            => 'POE QTY',
      'sum(case when tb_purchase_order_items.total_amount is null then 0.00 else tb_purchase_order_items.total_amount 
end) as "poe_val"'                                                            => 'POE VAL',
      'sum(case when tb_po_item.quantity is null then 0.00 else tb_po_item.quantity 
end) as "po_qty"'                                                             => 'PO QTY',
      'sum(case when tb_po_item.total_amount is null then 0.00 else tb_po_item.total_amount 
end) as "po_val"'                                                             => 'PO VAL',
      'sum(case when tb_receipt_items.received_quantity is null then 0.00 else tb_receipt_items.received_quantity 
end ) as "grn_qty"'                                                           => 'GRN QTY',
      'sum(case when tb_receipt_items.received_total_value is null then 0.00 else tb_receipt_items.received_total_value 
end ) as "grn_val"'                                                           => 'GRN VAL',
      'tb_inventory_purchase_requisition_details.sisa'                        => 'PR Remaining',
    );
      
  }
  public function getSearchableColumns()
  {
    return array(
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_inventory_purchase_requisitions.pr_number'
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_inventory_purchase_requisitions.pr_number',
      'tb_inventory_purchase_requisition_details.quantity',
      'tb_inventory_purchase_requisition_details.total',
      '(sum(case when tb_purchase_order_items.quantity is null then 0.00 else tb_purchase_order_items.quantity 
end))',
      '(sum(case when tb_purchase_order_items.total_amount is null then 0.00 else tb_purchase_order_items.total_amount 
end))',
      '(sum(case when tb_purchase_orders.document_number is null then 0.00 else tb_purchase_order_items.quantity 
end))',
      '(sum(case when tb_purchase_orders.document_number is null then 0.00 else tb_purchase_order_items.total_amount 
end))',
      '(sum(case when tb_receipt_items.received_quantity is null then 0.00 else tb_receipt_items.received_quantity 
end ))',
      '(sum(case when tb_receipt_items.received_total_value is null then 0.00 else tb_receipt_items.received_total_value 
end ))',
      'sum(case when tb_po_item.quantity is null then 0.00 else tb_po_item.quantity 
end)',
      'sum(case when tb_po_item.total_amount is null then 0.00 else tb_po_item.total_amount 
end)',
      "tb_inventory_purchase_requisition_details.sisa",
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
    $this->db->from('tb_inventory_purchase_requisition_details');
    $this->db->join('tb_inventory_purchase_requisitions', 'tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id = tb_inventory_purchase_requisitions.id','left');
    $this->db->join('tb_purchase_order_items', 'tb_inventory_purchase_requisition_details.id = tb_purchase_order_items.inventory_purchase_request_detail_id','left');
    $this->db->join('tb_purchase_orders', 'tb_purchase_orders.id = tb_purchase_order_items.purchase_order_id','left');
    $this->db->join('tb_po_item', 'tb_po_item.poe_item_id = tb_purchase_order_items.id','left');
    $this->db->join('tb_receipt_items', 'tb_receipt_items.purchase_order_item_id = tb_po_item.id','left');
    $this->db->join('tb_budget', 'tb_inventory_purchase_requisition_details.budget_id = tb_budget.id');
    $this->db->join('tb_budget_cot', 'tb_budget_cot.id = tb_budget.id_cot');
    $this->db->join('tb_master_items', 'tb_budget_cot.id_item = tb_master_items.id');
    $this->db->group_by('tb_master_items.description');
    $this->db->group_by('tb_master_items.part_number');
    $this->db->group_by('tb_inventory_purchase_requisitions.pr_number');
    $this->db->group_by('tb_inventory_purchase_requisition_details.quantity');
    $this->db->group_by('tb_inventory_purchase_requisition_details.total');
    $this->db->group_by('tb_inventory_purchase_requisition_details.sisa');
    $this->db->group_by('tb_inventory_purchase_requisition_details.id');
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
    $this->db->from('tb_inventory_purchase_requisition_details');
    $this->db->join('tb_inventory_purchase_requisitions', 'tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id = tb_inventory_purchase_requisitions.id','left');
    $this->db->join('tb_purchase_order_items', 'tb_inventory_purchase_requisition_details.id = tb_purchase_order_items.inventory_purchase_request_detail_id','left');
    $this->db->join('tb_purchase_orders', 'tb_purchase_orders.id = tb_purchase_order_items.purchase_order_id','left');
    $this->db->join('tb_po_item', 'tb_po_item.poe_item_id = tb_purchase_order_items.id','left');
    $this->db->join('tb_receipt_items', 'tb_receipt_items.purchase_order_item_id = tb_purchase_order_items.id','left');
    $this->db->join('tb_budget', 'tb_inventory_purchase_requisition_details.budget_id = tb_budget.id');
    $this->db->join('tb_budget_cot', 'tb_budget_cot.id = tb_budget.id_cot');
    $this->db->join('tb_master_items', 'tb_budget_cot.id_item = tb_master_items.id');
    $this->db->group_by('tb_master_items.description');
    $this->db->group_by('tb_master_items.part_number');
    $this->db->group_by('tb_inventory_purchase_requisitions.pr_number');
    $this->db->group_by('tb_inventory_purchase_requisition_details.quantity');
    $this->db->group_by('tb_inventory_purchase_requisition_details.total');
    $this->db->group_by('tb_inventory_purchase_requisition_details.sisa');
    $this->db->group_by('tb_inventory_purchase_requisition_details.id');

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }
  
  public function countIndex()
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_inventory_purchase_requisition_details');
    $this->db->join('tb_inventory_purchase_requisitions', 'tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id = tb_inventory_purchase_requisitions.id','left');
    $this->db->join('tb_purchase_order_items', 'tb_inventory_purchase_requisition_details.id = tb_purchase_order_items.inventory_purchase_request_detail_id','left');
    $this->db->join('tb_purchase_orders', 'tb_purchase_orders.id = tb_purchase_order_items.purchase_order_id','left');
    $this->db->join('tb_po_item', 'tb_po_item.poe_item_id = tb_purchase_order_items.id','left');
    $this->db->join('tb_receipt_items', 'tb_receipt_items.purchase_order_item_id = tb_purchase_order_items.id','left');
    $this->db->join('tb_budget', 'tb_inventory_purchase_requisition_details.budget_id = tb_budget.id');
    $this->db->join('tb_budget_cot', 'tb_budget_cot.id = tb_budget.id_cot');
    $this->db->join('tb_master_items', 'tb_budget_cot.id_item = tb_master_items.id');
    $this->db->group_by('tb_master_items.description');
    $this->db->group_by('tb_master_items.part_number');
    $this->db->group_by('tb_inventory_purchase_requisitions.pr_number');
    $this->db->group_by('tb_inventory_purchase_requisition_details.quantity');
    $this->db->group_by('tb_inventory_purchase_requisition_details.total');
    $this->db->group_by('tb_inventory_purchase_requisition_details.sisa');
    $this->db->group_by('tb_inventory_purchase_requisition_details.id');

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
