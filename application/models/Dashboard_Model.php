<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_Model extends MY_Model
{
  protected $connection;

  public function __construct()
  {
    parent::__construct();
    $this->connection   = $this->load->database('budgetcontrol', TRUE);
    $this->budget_year  = find_budget_setting('Active Year');
    $this->budget_month = find_budget_setting('Active Month');
  }

  public function find_item_in_stores($warehouse, $term)
  {
    $sql = "SELECT t1.*, t2.description, t2.group, t2.alternate_part_number, t2.unit, t2.id AS item_in_stores_id
      FROM tb_stocks t1
      JOIN tb_master_items t2 ON t2.part_number = t1.part_number
      WHERE t1.warehouse = '$warehouse'
      AND t1.quantity > 0
      AND (
          t2.description ILIKE '%$term%' OR
          t1.part_number ILIKE '%$term%' OR
          t1.item_serial ILIKE '%$term%'
     )
      ORDER BY t2.description ASC, t1.part_number ASC
      ";

    $query = $this->db->query($sql);
    $result = $query->result_array();

    foreach ($result as $key => $value){
      $result[$key]['aircraft_types'] = $this->findItemModelsByPartNumber($value['part_number']);
      $result[$key]['balance_stock']  = $this->find_balance_stock($value['part_number'], $value['item_serial'], $value['warehouse']);
    }

    return $result;
  }

  public function find_balance_stock($part_number, $item_serial, $warehouse = null, $stores = null)
  {
    if ($warehouse !== null)
      $this->db->where('warehouse', $warehouse);

    if ($stores !== null)
      $this->db->where('warehouse', $warehouse);

    $this->db->where('part_number', $part_number);
    $this->db->where('item_serial', $item_serial);
    $this->db->where('condition !=', 'REJECTED');
    $this->db->select_sum('quantity', 'quantity');
    $query = $this->db->get('tb_stocks');
    $row = $query->row_array();

    return $row['quantity'];
  }

  public function countAdjustment()
  {
    $this->db->select(array(
      'tb_stock_adjustments.id',
      'tb_master_items.part_number',
      'tb_master_items.serial_number',
      'tb_master_items.description',
      'tb_master_item_groups.category',
      'tb_master_items.group',
      'tb_stocks.condition',
      'tb_stock_adjustments.created_by',
      'tb_stock_adjustments.created_at',
      'tb_stock_adjustments.previous_quantity',
      'tb_stock_adjustments.adjustment_quantity',
      'tb_stock_adjustments.balance_quantity',
      'tb_master_items.unit',
      'tb_stock_adjustments.remarks',
      'tb_stock_adjustments.adjustment_token',
    ));
    $this->db->from('tb_stock_adjustments');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_stock_adjustments.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_stock_adjustments.updated_status', 'PENDING');
    //$this->db->group_by($this->getGroupByColumns());
	
	  $this->db->where('EXTRACT(YEAR FROM tb_stock_adjustments.date_of_entry)::integer = ', date('Y'));

    $query = $this->db->get();

    return $query->num_rows();
  }

   public function countExpiredStock($start_date,$end_date)
  {
    $this->db->select(array(
      'tb_stock_in_stores.id',
    ));
    $this->db->from('tb_stock_in_stores');
    // $this->db->where('tb_stock_in_stores.expired_date >= ', $start_date);
    $this->db->where('tb_stock_in_stores.expired_date <= ', $end_date);
    $this->db->where('tb_stock_in_stores.quantity > 0');

    //$this->db->group_by($this->getGroupByColumns());

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function expiredStock($start_date,$end_date)
  {
    $this->db->select(array(
      'tb_stocks.id',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.serial_number',
      'tb_stock_in_stores.expired_date',
      'tb_master_item_groups.category',
      'tb_master_item_groups.group',
      'tb_stocks.condition',
      'SUM(tb_stock_in_stores.quantity) as quantity',
      'tb_stock_in_stores.unit_value',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit',
      'tb_master_item_groups.coa',
      'tb_master_items.kode_stok',
      'tb_stock_in_stores.stores',
      'tb_stock_in_stores.warehouse',
      'tb_stock_in_stores.remarks',
      'tb_stock_in_stores.reference_document',
    ));
    $this->db->from('tb_stock_in_stores');
     $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_stock_in_stores.expired_date >= ', $start_date);
    $this->db->where('tb_stock_in_stores.expired_date <= ', $end_date);
    // $this->db->where('tb_stock_in_stores.expired_date >= ', $start_date);
    $this->db->where('condition', 'SERVICEABLE');
    $this->db->where('tb_stock_in_stores.quantity > 0');
    $this->db->group_by(array(
      'tb_stocks.id',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.serial_number',
      'tb_master_item_groups.category',
      'tb_master_item_groups.group',
      'tb_stock_in_stores.unit_value', 
      'tb_stocks.condition',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit',
      'tb_stock_in_stores.stores',
      'tb_stock_in_stores.warehouse',
      'tb_stock_in_stores.remarks',
      'tb_stock_in_stores.expired_date',
      'tb_stock_in_stores.reference_document',
      'tb_master_item_groups.coa',
      'tb_master_items.kode_stok',
    ));

    //$this->db->group_by($this->getGroupByColumns());

    $query = $this->db->get();

    return $query->result_array();
  }

  public function count_prl($role){
    $status =['waiting','pending'];
    if($role=='CHIEF OF MAINTANCE'){
      $status = ['waiting'];
    }
    if($role=='FINANCE MANAGER'){
      $status = ['pending'];
    }
    if($role=='OPERATION SUPPORT'){
      $status = ['review operation support'];
    }
    $this->db->select('*');
    $this->db->from('tb_inventory_purchase_requisition_details');
    $this->db->where_in('tb_inventory_purchase_requisition_details.status', $status);
    $query = $this->db->get();

    return $query->num_rows();
  }

  public function count_poe($role){
    $status =['evaluation'];
    $this->db->distinct();
    $this->db->select('*');
    $this->db->from('tb_purchase_order_items');
    $this->db->join('tb_purchase_orders','tb_purchase_orders.id = tb_purchase_order_items.purchase_order_id','left');
    $this->db->where_in('tb_purchase_orders.status', $status);
    $query = $this->db->get();

    return $query->num_rows();
  }

  public function count_po($role){
    $this->db->select('*');
    $this->db->from('tb_po_item');
    $this->db->join('tb_po','tb_po.id=tb_po_item.purchase_order_id');
    $this->db->like('tb_po.review_status', 'WAITING');
    if($role == 'FINANCE MANAGER'){
      $this->db->like('tb_po.review_status', 'WAITING FOR FINANCE');
    }
    if($role == 'HEAD OF SCHOOL'){
      $this->db->like('tb_po.review_status', 'WAITING FOR HOS');
    }
    if($role == 'VP FINANCE'){
      $this->db->like('tb_po.review_status', 'WAITING FOR VP FINANCE');
    }
    if($role == 'CHIEF OF FINANCE'){
      $this->db->like('tb_po.review_status', 'WAITING FOR CFO REVIEW');
    }
    if($role == 'CHIEF OPERATION OFFICER'){
      $this->db->like('tb_po.review_status', 'WAITING FOR COO REVIEW');
    }
    $query = $this->db->get();

    return $query->num_rows();
  }

  public function send_mail()
  {
    $from_email = "bifa.acd@gmail.com";
    $to_email = "aidanurul99@rocketmail.com";
    

    //Load email library 
    $this->load->library('email');
    $this->email->set_newline("\r\n");
    $message = "<p>Dear </p>";
    $message .= "<p>Berikut permintaan Persetujuan untuk Expense Request :</p>";
    $message .= "<ul>";
    $message .= "</ul>";
    $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
    $message .= "<p>[ <a href='http://119.2.51.138:7323/expense_request/' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
    $message .= "<p>Thanks and regards</p>";
    $this->email->from($from_email, 'Material Resource Planning');
    $this->email->to($to_email);
    $this->email->subject('TEST SENDING EMAIL Dari SERVER');
    $this->email->message($message);

        //Send mail 
    if ($this->email->send())
      return true;
    else
      return $this->email->print_debugger();
  }

  public function count_capex_req($role){
    $status =['WAITING FOR HEAD DEPT','pending'];
    if($role=='BUDGETCONTROL'){
      $status = ['pending'];
    }
    if(config_item('as_head_department')=='yes'){
      $status = ['WAITING FOR HEAD DEPT'];
    }

    $this->connection->select('*');
    $this->connection->from('tb_capex_purchase_requisitions');
    $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_capex_purchase_requisitions.annual_cost_center_id');
    $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
    $this->connection->join('tb_departments', 'tb_departments.id = tb_cost_centers.department_id');
    $this->connection->where_in('tb_capex_purchase_requisitions.status', $status);
    $this->connection->like('tb_capex_purchase_requisitions.pr_number', $this->budget_year);
    if(config_item('as_head_department')=='yes'){
      $this->connection->where('tb_departments.department_name', config_item('head_department'));
    }
    $query = $this->connection->get();

    return $query->num_rows();
  }

  public function count_inventory_req($role){
    $status =['WAITING FOR HEAD DEPT','pending'];
    if($role=='BUDGETCONTROL'){
      $status = ['pending'];
    }
    if(config_item('as_head_department')=='yes'){
      $status = ['WAITING FOR HEAD DEPT'];
    }

    $this->connection->select('*');
    $this->connection->from('tb_inventory_purchase_requisitions');
    $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_inventory_purchase_requisitions.annual_cost_center_id');
    $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
    $this->connection->join('tb_departments', 'tb_departments.id = tb_cost_centers.department_id');
    $this->connection->where_in('tb_inventory_purchase_requisitions.status', $status);
    $this->connection->like('tb_inventory_purchase_requisitions.pr_number', $this->budget_year);
    if(config_item('as_head_department')=='yes'){
      $this->connection->where('tb_departments.department_name', config_item('head_department'));
    }
    $query = $this->connection->get();

    return $query->num_rows();
  }

  public function count_expense_req($role){
    $status =['WAITING FOR HEAD DEPT','pending'];
    if($role=='BUDGETCONTROL'){
      $status = ['pending'];
    }
    if($role=='FINANCE MANAGER'){
      $status = ['WAITING FOR FINANCE REVIEW'];
    }
    if($role=='HEAD OF SCHOOL'){
      $status = ['WAITING FOR HOS REVIEW'];
    }
    if($role=='VP FINANCE'){
      $status = ['WAITING FOR VP FINANCE REVIEW'];
    }
    if($role=='CHIEF OF FINANCE'){
      $status = ['WAITING FOR CFO REVIEW'];
    }
    if($role=='CHIEF OPERATION OFFICER'){
      $status = ['WAITING FOR COO REVIEW'];
    }
    if(config_item('as_head_department')=='yes'){
      $status = ['WAITING FOR HEAD DEPT'];
    }

    $this->connection->select('*');
    $this->connection->from('tb_expense_purchase_requisitions');
    $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_expense_purchase_requisitions.annual_cost_center_id');
    $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
    $this->connection->join('tb_departments', 'tb_departments.id = tb_cost_centers.department_id');
    $this->connection->where_in('tb_expense_purchase_requisitions.status', $status);
    $this->connection->like('tb_expense_purchase_requisitions.pr_number', $this->budget_year);
    if(config_item('as_head_department')=='yes'){
      $this->connection->where('tb_departments.department_name', config_item('head_department'));
    }
    $query = $this->connection->get();

    return $query->num_rows();
  }

  public function count_poe_local($role,$tipe){
    $status =['evaluation'];
    if($role=='PROCUREMENT MANAGER'){
      $status = ['evaluation'];
    }

    $this->db->select('*');
    $this->db->from('tb_purchase_orders');
    $this->db->where_in('tb_purchase_orders.status', $status);
    $this->db->where('tb_purchase_orders.tipe', $tipe);
    $query = $this->db->get();

    return $query->num_rows();
  }

  public function count_po_local($role,$tipe){
    $status =['no_status'];
    if($role=='ASSISTANT HOS'){
      $status = ['WAITING FOR AHOS REVIEW'];
    }
    if($role=='PROCUREMENT MANAGER'){
      $status = ['WAITING FOR PROC MNG REVIEW'];
    }
    if($role=='FINANCE MANAGER'){
      $status = ['WAITING FOR FINANCE REVIEW'];
    }
    if($role=='HEAD OF SCHOOL'){
      $status = ['WAITING FOR HOS REVIEW'];
    }
    if($role=='CHIEF OPERATION OFFICER'){
      $status = ['WAITING FOR COO REVIEW'];
    }
    if($role=='VP FINANCE'){
      $status = ['WAITING FOR VP FINANCE REVIEW'];
    }
    if($role=='CHIEF OF FINANCE'){
      $status = ['WAITING FOR CFO REVIEW'];
    }

    $this->db->select('*');
    $this->db->from('tb_purchase_orders');
    $this->db->where_in('tb_purchase_orders.status', $status);
    $this->db->where('tb_purchase_orders.tipe', $tipe);
    $query = $this->db->get();

    return $query->num_rows();
  }

  public function count_poe_local_not_approved($tipe){
    $status =['evaluation'];

    $this->db->select('*');
    $this->db->from('tb_purchase_orders');
    $this->db->where_in('tb_purchase_orders.status', $status);
    $this->db->where('tb_purchase_orders.tipe', $tipe);
    $query = $this->db->get();

    return $query->num_rows();
  }

  public function count_po_local_not_approved($tipe){
    $status = [
      'WAITING FOR AHOS REVIEW',
      'WAITING FOR PROC MNG REVIEW',
      'WAITING FOR FINANCE REVIEW',
      'WAITING FOR HOS REVIEW',
      'WAITING FOR COO REVIEW',
      'WAITING FOR VP FINANCE REVIEW',
      'WAITING FOR CFO REVIEW'
    ];

    $this->db->select('*');
    $this->db->from('tb_purchase_orders');
    $this->db->where_in('tb_purchase_orders.status', $status);
    $this->db->where('tb_purchase_orders.tipe', $tipe);
    $query = $this->db->get();

    return $query->num_rows();
  }

  public function count_prl_local_not_approved($tipe){
    $status =['approved','rejected','canceled'];

    if($tipe=='capex'){
      $this->connection->select('*');
      $this->connection->from('tb_capex_purchase_requisitions');
      $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_capex_purchase_requisitions.annual_cost_center_id');
      $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
      $this->connection->where_not_in('tb_capex_purchase_requisitions.status', $status);      
      $this->connection->like('tb_capex_purchase_requisitions.pr_number', $this->budget_year);
      if (count(config_item('auth_annual_cost_centers_name'))>0) {
        $this->connection->where_in('tb_cost_centers.cost_center_name', config_item('auth_annual_cost_centers_name'));
      }else{
        $this->connection->where_in('tb_cost_centers.cost_center_name', ['no_cost_center']);
      }
      $query = $this->connection->get();
      $return = $query->num_rows();
    }

    if($tipe=='inventory'){
      $this->connection->select('*');
      $this->connection->from('tb_inventory_purchase_requisitions');
      $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_inventory_purchase_requisitions.annual_cost_center_id');
      $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
      $this->connection->where_not_in('tb_inventory_purchase_requisitions.status', $status);      
      $this->connection->like('tb_inventory_purchase_requisitions.pr_number', $this->budget_year);
      if (count(config_item('auth_annual_cost_centers_name'))>0) {
        $this->connection->where_in('tb_cost_centers.cost_center_name', config_item('auth_annual_cost_centers_name'));
      }else{
        $this->connection->where_in('tb_cost_centers.cost_center_name', ['no_cost_center']);
      }
      $query = $this->connection->get();
      $return = $query->num_rows();
    }

    if($tipe=='expense'){
      $this->connection->select('*');
      $this->connection->from('tb_expense_purchase_requisitions');
      $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_expense_purchase_requisitions.annual_cost_center_id');
      $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
      $this->connection->where_not_in('tb_expense_purchase_requisitions.status', $status);      
      $this->connection->like('tb_expense_purchase_requisitions.pr_number', $this->budget_year);
      if (count(config_item('auth_annual_cost_centers_name'))>0) {
        $this->connection->where_in('tb_cost_centers.cost_center_name', config_item('auth_annual_cost_centers_name'));
      }else{
        $this->connection->where_in('tb_cost_centers.cost_center_name', ['no_cost_center']);
      }
      $query = $this->connection->get();
      $return = $query->num_rows();
      // $return = count(config_item('auth_annual_cost_centers_name'));
    }
    

    return $return;
  }

}
