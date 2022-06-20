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
    $this->db->where('tb_purchase_orders.tipe', 'INVENTORY MRP');
    $this->db->where_in('tb_purchase_orders.status', $status);
    $query = $this->db->get();

    return $query->num_rows();
  }

  public function count_po($role){
    $this->db->select('*');
    $this->db->from('tb_po');
    // $this->db->join('tb_po','tb_po.id=tb_po_item.purchase_order_id');
    $this->db->where('tb_po.tipe_po', 'INVENTORY MRP');
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
    $this->email->subject('TEST SENDING EMAIL');
    $this->email->message($message);

        //Send mail 
    if ($this->email->send())
      return true;
    else
      return $this->email->print_debugger();
  }

  public function count_capex_req($role){
    $status = ['all'];
    if($role=='BUDGETCONTROL'){
      $status[] = 'pending';
    }
    if($role=='ASSISTANT HOS'){
      $status[] = 'WAITING FOR AHOS REVIEW';
    } 
    
    if($role=='HEAD DEPT UNIQ JKT'){
      $status[] = 'WAITING FOR HEAD DEPT UNIQ REVIEW';
    } 

    if($role=='FINANCE MANAGER'){
      $status[] = 'WAITING FOR FINANCE REVIEW';
    }
    if($role=='HEAD OF SCHOOL'){
      $status[] = 'WAITING FOR HOS REVIEW';
    }
    if($role=='VP FINANCE'){
      $status[] = 'WAITING FOR VP FINANCE REVIEW';
    }
    if($role=='CHIEF OF FINANCE'){
      $status[] = 'WAITING FOR CFO REVIEW';
    }
    if($role=='CHIEF OPERATION OFFICER'){
      $status[] = 'WAITING FOR COO REVIEW';
    }

    $this->connection->select('*');
    $this->connection->from('tb_capex_purchase_requisitions');
    $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_capex_purchase_requisitions.annual_cost_center_id');
    $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
    $this->connection->join('tb_departments', 'tb_departments.id = tb_cost_centers.department_id');
    $this->connection->like('tb_capex_purchase_requisitions.pr_number', $this->budget_year);
    $this->connection->where_in('tb_capex_purchase_requisitions.status', $status);
    $this->connection->where_in('tb_capex_purchase_requisitions.base', config_item('auth_warehouses'));
    $query = $this->connection->get();
    $count = $query->num_rows();

    $count_as_head_dept = 0;
    if(config_item('as_head_department')=='yes'){
      $status = 'WAITING FOR HEAD DEPT';
      $this->connection->select('*');
      $this->connection->from('tb_capex_purchase_requisitions');
      $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_capex_purchase_requisitions.annual_cost_center_id');
      $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
      $this->connection->join('tb_departments', 'tb_departments.id = tb_cost_centers.department_id');
      $this->connection->like('tb_capex_purchase_requisitions.pr_number', $this->budget_year);
      $this->connection->where('tb_capex_purchase_requisitions.status', $status);
      $this->connection->where_in('tb_capex_purchase_requisitions.base', config_item('auth_warehouses'));
      $this->connection->where_in('tb_departments.department_name', config_item('head_department'));
      $this->connection->where('tb_capex_purchase_requisitions.head_dept', config_item('auth_username'));
      $query_as_head_dept = $this->connection->get();
      $count_as_head_dept = $query_as_head_dept->num_rows();
    }


    return $count+$count_as_head_dept;
  }

  public function count_inventory_req($role){
    $status = ['all'];
    if($role=='BUDGETCONTROL'){
      $status[] = 'pending';
    }
    if($role=='ASSISTANT HOS'){
      $status[] = 'WAITING FOR AHOS REVIEW';
    }    
    if($role=='HEAD DEPT UNIQ JKT'){
      $status[] = 'WAITING FOR HEAD DEPT UNIQ REVIEW';
    } 

    $this->connection->select('*');
    $this->connection->from('tb_inventory_purchase_requisitions');
    $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_inventory_purchase_requisitions.annual_cost_center_id');
    $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
    $this->connection->join('tb_departments', 'tb_departments.id = tb_cost_centers.department_id');
    $this->connection->like('tb_inventory_purchase_requisitions.pr_number', $this->budget_year);
    $this->connection->where_in('tb_inventory_purchase_requisitions.status', $status);
    $this->connection->where_in('tb_inventory_purchase_requisitions.base', config_item('auth_warehouses'));
    $query = $this->connection->get();
    $count = $query->num_rows();

    $count_as_head_dept = 0;
    if(config_item('as_head_department')=='yes'){
      $status = 'WAITING FOR HEAD DEPT';
      $this->connection->select('*');
      $this->connection->from('tb_inventory_purchase_requisitions');
      $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_inventory_purchase_requisitions.annual_cost_center_id');
      $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
      $this->connection->join('tb_departments', 'tb_departments.id = tb_cost_centers.department_id');
      $this->connection->like('tb_inventory_purchase_requisitions.pr_number', $this->budget_year);
      $this->connection->where('tb_inventory_purchase_requisitions.status', $status);
      $this->connection->where_in('tb_inventory_purchase_requisitions.base', config_item('auth_warehouses'));
      $this->connection->where_in('tb_departments.department_name', config_item('head_department'));
      $this->connection->where('tb_inventory_purchase_requisitions.head_dept', config_item('auth_username'));
      $query_as_head_dept = $this->connection->get();
      $count_as_head_dept = $query_as_head_dept->num_rows();
    }


    return $count+$count_as_head_dept;
  }

  public function count_expense_req($role){
    $status =['all'];
    if($role=='BUDGETCONTROL'){
      $status[] = 'pending';
    }
    if($role=='ASSISTANT HOS'){
      $status[] = 'WAITING FOR AHOS REVIEW';
    }  
    if($role=='FINANCE MANAGER'){
      $status[] = 'WAITING FOR FINANCE REVIEW';
    }
    if($role=='HEAD OF SCHOOL'){
      $status[] = 'WAITING FOR HOS REVIEW';
    }
    if($role=='VP FINANCE'){
      $status[] = 'WAITING FOR VP FINANCE REVIEW';
    }
    if($role=='CHIEF OF FINANCE'){
      $status[] = 'WAITING FOR CFO REVIEW';
    }
    if($role=='CHIEF OPERATION OFFICER'){
      $status[] = 'WAITING FOR COO REVIEW';
    }
    if($role=='HEAD DEPT UNIQ JKT'){
      $status[] = 'WAITING FOR HEAD DEPT UNIQ REVIEW';
    } 

    $this->connection->select('*');
    $this->connection->from('tb_expense_purchase_requisitions');
    // $this->connection->join('tb_expense_purchase_requisition_details', 'tb_expense_purchase_requisition_details.expense_purchase_requisition_id = tb_expense_purchase_requisitions.id');
    // $this->connection->join('tb_expense_monthly_budgets', 'tb_expense_monthly_budgets.id = tb_expense_purchase_requisition_details.expense_monthly_budget_id');
    // $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_expense_monthly_budgets.annual_cost_center_id');
    // $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
    // $this->connection->join('tb_departments', 'tb_departments.id = tb_cost_centers.department_id');
    $this->connection->like('tb_expense_purchase_requisitions.pr_number', $this->budget_year);
    $this->connection->where_in('tb_expense_purchase_requisitions.base', config_item('auth_warehouses'));
    $this->connection->where_in('tb_expense_purchase_requisitions.status', $status);
    $query = $this->connection->get();
    $count = $query->num_rows();

    $count_as_head_dept = 0;
    if(config_item('as_head_department')=='yes'){
      $status = 'WAITING FOR HEAD DEPT';
      $this->connection->select('*');
      $this->connection->from('tb_expense_purchase_requisitions');
      // $this->connection->join('tb_expense_purchase_requisition_details', 'tb_expense_purchase_requisition_details.expense_purchase_requisition_id = tb_expense_purchase_requisitions.id');
      // $this->connection->join('tb_expense_monthly_budgets', 'tb_expense_monthly_budgets.id = tb_expense_purchase_requisition_details.expense_monthly_budget_id');
      $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_expense_purchase_requisitions.annual_cost_center_id');
      $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
      $this->connection->join('tb_departments', 'tb_departments.id = tb_cost_centers.department_id');
      $this->connection->where('tb_expense_purchase_requisitions.status', $status);
      $this->connection->like('tb_expense_purchase_requisitions.pr_number', $this->budget_year);
      $this->connection->where_in('tb_expense_purchase_requisitions.base', config_item('auth_warehouses'));
      $this->connection->where_in('tb_departments.department_name', config_item('head_department'));
      $this->connection->where('tb_expense_purchase_requisitions.head_dept', config_item('auth_username'));
      $query = $this->connection->get();
      $count_as_head_dept = $query->num_rows();
    }

    return $count+$count_as_head_dept;
  }

  public function count_poe_local($role,$tipe){
    $status =['evaluation'];
    if($role=='PROCUREMENT MANAGER'){
      $status = ['evaluation'];
    }

    $this->db->select('*');
    $this->db->from('tb_purchase_orders');
    $this->db->where_in('tb_purchase_orders.status', $status);
    $this->db->where('tb_purchase_orders.tipe',strtoupper($tipe));
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
    $this->db->from('tb_po');
    $this->db->where_in('tb_po.review_status', $status);
    $this->db->where('tb_po.tipe_po', strtoupper($tipe));
    $this->db->where_in('tb_po.base', config_item('auth_warehouses'));
    $query = $this->db->get();

    return $query->num_rows();
  }

  public function count_poe_local_not_approved($tipe){
    $status =['evaluation'];

    $this->db->select('*');
    $this->db->from('tb_purchase_orders');
    $this->db->where_in('tb_purchase_orders.status', $status);
    $this->db->where('tb_purchase_orders.tipe', strtoupper($tipe));
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
    $this->db->where('tb_purchase_orders.tipe', strtoupper($tipe));
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
      $this->connection->join('tb_expense_purchase_requisition_details', 'tb_expense_purchase_requisition_details.expense_purchase_requisition_id = tb_expense_purchase_requisitions.id');
      $this->connection->join('tb_expense_monthly_budgets', 'tb_expense_monthly_budgets.id = tb_expense_purchase_requisition_details.expense_monthly_budget_id');
      $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_expense_monthly_budgets.annual_cost_center_id');
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

  public function count_payment_request($role){
    $status =['no_status'];
    if($role=='FINANCE SUPERVISOR'){
      $status = ['WAITING CHECK BY FIN SPV'];
    }    
    if($role=='FINANCE MANAGER'){
      $status = ['WAITING REVIEW BY FIN MNG'];
    }
    if($role=='HEAD OF SCHOOL'){
      $status = ['WAITING REVIEW BY HOS'];
    }
    if($role=='CHIEF OPERATION OFFICER'){
      $status = ['WAITING REVIEW BY CEO'];
    }
    if($role=='VP FINANCE'){
      $status = ['WAITING REVIEW BY VP FINANCE'];
    }
    if($role=='CHIEF OF FINANCE'){
      $status = ['WAITING REVIEW BY CFO'];
    }

    $this->db->select('*');
    $this->db->from('tb_po_payments');
		// $this->db->join('tb_po_payments', 'tb_po_payments.id = tb_purchase_order_items_payments.po_payment_id');
    $this->db->where_in('tb_po_payments.status', $status);
    if($role=='FINANCE MANAGER'){
      $base = config_item('auth_warehouse');
			if($base!='JAKARTA'){
				$this->db->where('tb_po_payments.base !=','JAKARTA');
			}elseif($base=='JAKARTA'){
				$this->db->where('tb_po_payments.base','JAKARTA');
			}
    }
    $query = $this->db->get();

    return $query->num_rows();
  }

  public function count_ap($tipe){
    if($tipe=='maintenance'){
      $tipe_po = ['INVENTORY MRP'];
    }else{
      $tipe_po = ['CAPEX','EXPENSE','INVENTORY'];
    }
    $this->db->from('tb_po');
    $this->db->where_in('tb_po.status', ['ORDER', 'OPEN','ADVANCE']);
    $this->db->where_in('tb_po.tipe_po', $tipe_po);
    $query = $this->db->get();

    return $query->num_rows();
  }

  public function count_ap_expense(){
    $this->connection->from('tb_expense_purchase_requisitions');
    $this->connection->where('tb_expense_purchase_requisitions.with_po','f');
    $this->connection->where_in('tb_expense_purchase_requisitions.status', ['approved']);
    $this->connection->like('tb_expense_purchase_requisitions.pr_number', $this->budget_year);
    // $this->db->where_in('tb_po.tipe_po', $tipe_po);
    $query = $this->connection->get();

    return $query->num_rows();
  }

  public function count_purposed_payment($role,$source){
    $status =['no_status'];
    if($role=='FINANCE SUPERVISOR'){
      $status = ['WAITING CHECK BY FIN SPV'];
    }    
    if($role=='FINANCE MANAGER'){
      $status = ['WAITING REVIEW BY FIN MNG'];
    }
    if($role=='HEAD OF SCHOOL'){
      $status = ['WAITING REVIEW BY HOS'];
    }
    if($role=='CHIEF OPERATION OFFICER'){
      $status = ['WAITING REVIEW BY CEO'];
    }
    if($role=='VP FINANCE'){
      $status = ['WAITING REVIEW BY VP FINANCE'];
    }
    if($role=='CHIEF OF FINANCE'){
      $status = ['WAITING REVIEW BY CFO'];
    }

    $this->connection->select('*');
    $this->connection->from('tb_request_payments');
    // $this->db->join('tb_po_payments', 'tb_po_payments.id = tb_purchase_order_items_payments.po_payment_id');
    $this->connection->where('tb_request_payments.source', $source);
    $this->connection->where_in('tb_request_payments.status', $status);
    if($role=='FINANCE MANAGER'){
      $base = config_item('auth_warehouse');
      if($base!='JAKARTA'){
        $this->connection->where('tb_request_payments.base !=','JAKARTA');
      }elseif($base=='JAKARTA'){
        $this->connection->where('tb_request_payments.base','JAKARTA');
      }
    }
    $query = $this->connection->get();

    return $query->num_rows();
  }

  public function count_payment_request_need_to_pay(){
    $status =['APPROVED'];

    $this->db->select('*');
    $this->db->from('tb_po_payments');
    // $this->db->join('tb_po_payments', 'tb_po_payments.id = tb_purchase_order_items_payments.po_payment_id');
    $this->db->where_in('tb_po_payments.status', $status);
    // if($role=='FINANCE MANAGER'){
    //   $base = config_item('auth_warehouse');
    //   if($base!='JAKARTA'){
    //     $this->db->where('tb_po_payments.base !=','JAKARTA');
    //   }elseif($base=='JAKARTA'){
    //     $this->db->where('tb_po_payments.base','JAKARTA');
    //   }
    // }
    $query = $this->db->get();

    return $query->num_rows();
  }

  public function count_purposed_payment_need_to_pay($source){
    $status =['APPROVED'];
    $this->connection->select('*');
    $this->connection->from('tb_request_payments');
    // $this->db->join('tb_po_payments', 'tb_po_payments.id = tb_purchase_order_items_payments.po_payment_id');
    $this->connection->where('tb_request_payments.source', $source);
    $this->connection->where_in('tb_request_payments.status', $status);
    // if($role=='FINANCE MANAGER'){
    //   $base = config_item('auth_warehouse');
    //   if($base!='JAKARTA'){
    //     $this->connection->where('tb_request_payments.base !=','JAKARTA');
    //   }elseif($base=='JAKARTA'){
    //     $this->connection->where('tb_request_payments.base','JAKARTA');
    //   }
    // }
    $query = $this->connection->get();

    return $query->num_rows();
  }

  public function getListAttachment()
  {
    $query = $this->db->get('tb_attachment_poe');
    $data = array();
    $count = array();

    foreach($query->result_array() as $key => $att){
      $is_file_exists = file_exists($att['file']);
      $insert = [
        'id' => $att['id'],
        'id_poe' => $att['id_poe'],
        'file'=> $att['file'],
        'is_file_exists' => $is_file_exists
      ];
      if(!$is_file_exists){
        $data[] = $insert;
        $count[] = 1;
      }
    }
    return [
      'count'=>array_sum($count),
      'data'=>$data
    ];
  }

  public function isFileExist($id,$type){
    if($type=='mrp'){
      $this->db->select('*');
      $this->db->from('tb_attachment_poe');
      $this->db->where('id',$id);
      $query    = $this->db->get();
      $data  = $query->unbuffered_row('array');

      $is_file_exists = file_exists($data['file']);

    }elseif($type=='budgetcontrol'){
      $this->connection->select('*');
      $this->connection->from('tb_attachment');
      $this->connection->where('id',$id);
      $query    = $this->connection->get();
      $data     = $query->unbuffered_row('array');
      $is_file_exists = file_exists($data['file']);
    }

    return $is_file_exists;
  }

  public function findAttachmentbyId($id,$type){
    if($type=='mrp'){
      $this->db->select('*');
      $this->db->from('tb_attachment_poe');
      $this->db->where('id',$id);
      $query    = $this->db->get();
      $data  = $query->unbuffered_row('array');

    }elseif($type=='budgetcontrol'){
      $this->connection->select('*');
      $this->connection->from('tb_attachment');
      $this->connection->where('id',$id);
      $query    = $this->connection->get();
      $data  = $query->unbuffered_row('array');
    }

    return $data;
  }

}
