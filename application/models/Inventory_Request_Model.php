<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory_Request_Model extends MY_Model
{
	protected $connection;
	protected $categories;
	protected $budget_year;
	protected $budget_month;
    protected $modules;

	public function __construct()
	{
		parent::__construct();

		$this->connection   = $this->load->database('budgetcontrol', TRUE);
		$this->categories   = $this->getCategories();
		$this->budget_year  = find_budget_setting('Active Year');
		$this->budget_month = find_budget_setting('Active Month');
        $this->modules        = config_item('module');
        $this->data['modules']        = $this->modules;
	}

	public function getSelectedColumns()
	{
        $return = array(
            'tb_inventory_purchase_requisitions.id'                                     => NULL,
            'tb_inventory_purchase_requisitions.pr_number'                              => 'Document Number',
            'tb_inventory_purchase_requisitions.status'                                 => 'Status',
            'tb_product_categories.category_name'                                       => 'Category',
            // 'tb_departments.department_name'                                         => 'Department Name',
            'tb_cost_centers.cost_center_name'                                          => 'Cost Center',
            'tb_inventory_purchase_requisitions.pr_date'                                => 'Document Date',
            'tb_inventory_purchase_requisitions.required_date'                          => 'Required Date',
            'SUM(tb_inventory_purchase_requisition_details.total) as total_inventory'   => 'Total',
            'tb_inventory_purchase_requisitions.notes'                                      => 'Requisitions Notes',
            'tb_inventory_purchase_requisitions.approved_notes'                             => 'Notes',
            NULL                                                                            => 'Attachment',
        );
		if (config_item('as_head_department')=='yes') {
            $return['tb_departments.department_name']  = 'Dept. Name';
        }
        return $return;
	}

	public function getGroupedColumns()
    {
        return array(
            'tb_inventory_purchase_requisitions.id',
            'tb_inventory_purchase_requisitions.pr_number',
			'tb_inventory_purchase_requisitions.status',
			'tb_product_categories.category_name',
            'tb_cost_centers.cost_center_name',
            'tb_inventory_purchase_requisitions.pr_date',
            'tb_inventory_purchase_requisitions.required_date',
            'tb_inventory_purchase_requisitions.notes',
            'tb_departments.department_name',
            'tb_inventory_purchase_requisitions.approved_notes'
            // 'SUM(tb_capex_purchase_requisition_detail.total) as total_capex'
        );
    }

	public function getSearchableColumns()
	{
		return array(
			// 'tb_capex_purchase_requisitions.id',
            'tb_inventory_purchase_requisitions.pr_number',
            'tb_inventory_purchase_requisitions.status',
			'tb_product_categories.category_name',
            'tb_cost_centers.cost_center_name',
            // 'tb_capex_purchase_requisitions.pr_date',
            // 'tb_capex_purchase_requisitions.required_date',
            'tb_inventory_purchase_requisitions.notes',
            // 'tb_departments.department_name',
            'tb_inventory_purchase_requisitions.approved_notes'
		);
	}

	public function getOrderableColumns()
	{
        $return = array(
            null,
            'tb_inventory_purchase_requisitions.pr_number',
            'tb_inventory_purchase_requisitions.status',
            'tb_product_categories.category_name',
            // 'tb_departments.department_name',
            'tb_cost_centers.cost_center_name',
            'tb_inventory_purchase_requisitions.pr_date',
            'tb_inventory_purchase_requisitions.required_date',
            NULL,
            'tb_inventory_purchase_requisitions.notes',
            NULL
        );

        if (config_item('as_head_department')=='yes') {
            $return[]  = 'tb_departments.department_name';
        }
        return $return;
	}

  	private function searchIndex()
  	{
		if (!empty($_POST['columns'][1]['search']['value'])){
		  	$search_required_date = $_POST['columns'][1]['search']['value'];
		  	$range_date  = explode(' ', $search_required_date);

		  	$this->connection->where('tb_inventory_purchase_requisitions.required_date >= ', $range_date[0]);
		  	$this->connection->where('tb_inventory_purchase_requisitions.required_date <= ', $range_date[1]);
		}

		if (!empty($_POST['columns'][2]['search']['value'])){
		  	$search_status = $_POST['columns'][2]['search']['value'];

		  	if($search_status!='all'){
                $this->connection->where('tb_inventory_purchase_requisitions.status', $search_status);
            } 
		}else{
            if(config_item('auth_role') == 'BUDGETCONTROL'){
                $this->connection->where('tb_inventory_purchase_requisitions.status', 'pending');
            } 
            if (config_item('as_head_department')=='yes'){
                $this->connection->where('tb_inventory_purchase_requisitions.status', 'WAITING FOR HEAD DEPT');
            }
            
        }

		if (!empty($_POST['columns'][3]['search']['value'])){
            $search_cost_center = $_POST['columns'][3]['search']['value'];
            if($search_cost_center!='all'){
                $this->connection->where('tb_cost_centers.cost_center_name', $search_cost_center);
            }            
		}
		
		if (!empty($_POST['columns'][4]['search']['value'])){
            $search_category = $_POST['columns'][4]['search']['value'];

            $this->connection->where('UPPER(tb_product_categories.category_name)', strtoupper($search_category));
        }


		$i = 0;

		foreach ($this->getSearchableColumns() as $item){
		  	if ($_POST['search']['value']){
				$term = strtoupper($_POST['search']['value']);

				if ($i === 0){
				  	$this->connection->group_start();
				  	$this->connection->like('UPPER('.$item.')', $term);
				} else {
				  	$this->connection->or_like('UPPER('.$item.')', $term);
				}

				if (count($this->getSearchableColumns()) - 1 == $i)
				  	$this->connection->group_end();
		  	}

		  	$i++;
		}
  	}

  	function getCategories()
  	{
		$categories = array();
		$category   = array();

		foreach (config_item('auth_inventory') as $inventory) {
		  	$category[] = strtoupper($inventory);
		}

		$this->connection->select('id');
		$this->connection->from('tb_product_categories');
		$this->connection->where_in('UPPER(category_name)', $category);

		$query  = $this->connection->get();

		foreach ($query->result_array() as $key => $value) {
		  	$categories[] = $value['id'];
		}

		return $categories;
  	}

    function getProductCategories()
    {
    	$category   = array();

		foreach (config_item('auth_inventory') as $inventory) {
		  	$category[] = strtoupper($inventory);
		}

        $this->connection->select('tb_product_categories.id,tb_product_categories.category_name');
        $this->connection->from('tb_product_categories');
        $this->connection->where_in('UPPER(category_name)', $category);
        // $this->connection->join('tb_users_mrp_in_product_categories','tb_users_mrp_in_product_categories.product_category_id=tb_product_categories.id');
        // $this->connection->where('tb_users_mrp_in_product_categories.username', $_SESSION['username']);

        $query  = $this->connection->get();
        $result = $query->result_array();

        return $result;
    }

  	function getIndex($return = 'array')
  	{
		$this->connection->select(array_keys($this->getSelectedColumns()));
		$this->connection->from('tb_inventory_purchase_requisitions');
        $this->connection->join('tb_inventory_purchase_requisition_details', 'tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id = tb_inventory_purchase_requisitions.id','left');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_inventory_purchase_requisitions.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->join('tb_departments', 'tb_departments.id = tb_cost_centers.department_id');
        $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_inventory_purchase_requisitions.product_category_id');
        $this->connection->like('tb_inventory_purchase_requisitions.pr_number', $this->budget_year);
		if (is_granted($this->data['modules']['inventory_request'], 'approval') === FALSE) {
            $this->connection->where_in('tb_cost_centers.cost_center_name', config_item('auth_annual_cost_centers_name'));
        }
        $this->connection->group_by($this->getGroupedColumns());

		$this->searchIndex();

		$column_order = $this->getOrderableColumns();

		if (isset($_POST['order'])){
		  	foreach ($_POST['order'] as $key => $order){
				$this->connection->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
		  	}
		} 	else {
		  	$this->connection->order_by('id', 'desc');
		}

		if ($_POST['length'] != -1)
		  	$this->connection->limit($_POST['length'], $_POST['start']);

		$query = $this->connection->get();

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
		$this->connection->select(array_keys($this->getSelectedColumns()));
		$this->connection->from('tb_inventory_purchase_requisitions');
        $this->connection->join('tb_inventory_purchase_requisition_details', 'tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id = tb_inventory_purchase_requisitions.id','left');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_inventory_purchase_requisitions.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->join('tb_departments', 'tb_departments.id = tb_cost_centers.department_id');
        $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_inventory_purchase_requisitions.product_category_id');
        $this->connection->like('tb_inventory_purchase_requisitions.pr_number', $this->budget_year);
		if (is_granted($this->data['modules']['inventory_request'], 'approval') === FALSE) {
            $this->connection->where_in('tb_cost_centers.cost_center_name', config_item('auth_annual_cost_centers_name'));
        }
        $this->connection->group_by($this->getGroupedColumns());

		$this->searchIndex();

		$query = $this->connection->get();

		return $query->num_rows();
  	}

  	public function countIndex()
  	{
  		$this->connection->select(array_keys($this->getSelectedColumns()));
		$this->connection->from('tb_inventory_purchase_requisitions');
        $this->connection->join('tb_inventory_purchase_requisition_details', 'tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id = tb_inventory_purchase_requisitions.id','left');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_inventory_purchase_requisitions.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->join('tb_departments', 'tb_departments.id = tb_cost_centers.department_id');
        $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_inventory_purchase_requisitions.product_category_id');
        $this->connection->like('tb_inventory_purchase_requisitions.pr_number', $this->budget_year);
		if (is_granted($this->data['modules']['inventory_request'], 'approval') === FALSE) {
            $this->connection->where_in('tb_cost_centers.cost_center_name', config_item('auth_annual_cost_centers_name'));
        }
        $this->connection->group_by($this->getGroupedColumns());

		$query = $this->connection->get();

		return $query->num_rows();
  	}

    public function findById($id)
    {
        $this->connection->select(
        	'tb_inventory_purchase_requisitions.*, 
        	tb_cost_centers.cost_center_name,
        	tb_cost_centers.cost_center_code,
        	tb_annual_cost_centers.cost_center_id,
        	tb_product_categories.category_name,
        	tb_product_categories.category_code'
        );
        $this->connection->from('tb_inventory_purchase_requisitions');
        $this->connection->join('tb_product_categories', 'tb_inventory_purchase_requisitions.product_category_id = tb_product_categories.id');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_inventory_purchase_requisitions.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->where('tb_inventory_purchase_requisitions.id', $id);

        $query    = $this->connection->get();
        $request  = $query->unbuffered_row('array');

        $select = array(
            'tb_inventory_purchase_requisition_details.*',
            'tb_products.product_name',
            'tb_products.product_code as part_number',
            'tb_product_groups.group_name as group',
            'tb_inventory_monthly_budgets.product_id',
            'tb_inventory_monthly_budgets.ytd_quantity',
            'tb_inventory_monthly_budgets.ytd_budget',
            'tb_inventory_monthly_budgets.ytd_used_quantity',
            'tb_inventory_monthly_budgets.ytd_used_budget',
        );

        $group_by = array(
            'tb_inventory_purchase_requisition_details.id',
            'tb_products.product_name',
            'tb_products.product_code',
            'tb_inventory_monthly_budgets.product_id',
            'tb_product_groups.group_name',
            'tb_inventory_monthly_budgets.ytd_quantity',
            'tb_inventory_monthly_budgets.ytd_budget',
            'tb_inventory_monthly_budgets.ytd_used_quantity',
            'tb_inventory_monthly_budgets.ytd_used_budget',
        );

        $this->connection->select($select);
        $this->connection->from('tb_inventory_purchase_requisition_details');
        $this->connection->join('tb_inventory_monthly_budgets', 'tb_inventory_monthly_budgets.id = tb_inventory_purchase_requisition_details.inventory_monthly_budget_id');
        $this->connection->join('tb_products', 'tb_products.id = tb_inventory_monthly_budgets.product_id');
        $this->connection->join('tb_product_groups', 'tb_product_groups.id = tb_products.product_group_id');
        $this->connection->where('tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id', $id);
        $this->connection->group_by($group_by);

        $query = $this->connection->get();

        foreach ($query->result_array() as $key => $value){
            $request['items'][$key] 							= $value;
            $request['items'][$key]['balance_mtd_quantity']     = $value['ytd_quantity'] - $value['ytd_used_quantity'];
            $request['items'][$key]['balance_mtd_budget']       = $value['ytd_budget'] - $value['ytd_used_budget'];
            $request['items'][$key]['mtd_quantity']     		= $value['quantity'] + $value['ytd_quantity'] - $value['ytd_used_quantity'];
            $request['items'][$key]['mtd_budget']       		= $value['total'] + $value['ytd_budget'] - $value['ytd_used_budget'];

            $this->column_select = array(
                'SUM(tb_inventory_monthly_budgets.mtd_quantity) as quantity',
                'SUM(tb_inventory_monthly_budgets.mtd_budget) as budget',
                'SUM(tb_inventory_monthly_budgets.mtd_used_quantity) as used_quantity',
                'SUM(tb_inventory_monthly_budgets.mtd_used_budget) as used_budget',
                'tb_inventory_monthly_budgets.product_id',
            );

            $this->column_groupby = array(                
                'tb_inventory_monthly_budgets.product_id',
            );

            $this->connection->select($this->column_select);
            $this->connection->from('tb_inventory_monthly_budgets');
            $this->connection->where('tb_inventory_monthly_budgets.product_id', $value['product_id']);
            $this->connection->group_by($this->column_groupby);

            $query = $this->connection->get();
            $row   = $query->unbuffered_row('array');

            $request['items'][$key]['maximum_quantity'] 		= $value['quantity'] + $row['quantity'] - $row['used_quantity'];
            $request['items'][$key]['maximum_price']    		=  $value['total'] + $row['budget'] - $row['used_budget'];
            $request['items'][$key]['balance_ytd_quantity']     = $row['quantity'] - $row['used_quantity'];
            $request['items'][$key]['balance_ytd_budget']       = $row['budget'] - $row['used_budget'];     
            $request['items'][$key]['history']					= array();       
            $request['items'][$key]['history']          = $this->getHistory($request['annual_cost_center_id'],$value['product_id'],$request['order_number']);

            //get onhand & min qty
            $this->db->from('tb_master_items');
            $this->db->where('UPPER(tb_master_items.description)', strtoupper($value['product_name']));

            $query  = $this->db->get();

            if ($query->num_rows() > 0){
                $master_item = $query->unbuffered_row('array');

                $request['items'][$key]['minimum_quantity'] = $master_item['minimum_quantity'];

                $this->db->select('tb_stocks.total_quantity, tb_stocks.average_value');
                $this->db->from('tb_stocks');
                $this->db->where('tb_stocks.item_id', $master_item['id']);
                $this->db->where('tb_stocks.condition', 'SERVICEABLE');

                $query  = $this->db->get();

                if ($query->num_rows() > 0){
                    $stock = $query->unbuffered_row('array');

                    $request['items'][$key]['on_hand_quantity'] = $stock['total_quantity'];
                    $request['items'][$key]['price']            = $stock['average_value'];
                } else {
                    $request['items'][$key]['on_hand_quantity'] = 0;
                    $request['items'][$key]['price']            = 0;
                }
            } else {
                $request['items'][$key]['minimum_quantity'] = 0;
                $request['items'][$key]['on_hand_quantity'] = 0;
                $request['items'][$key]['price']            = 0;
            }
            //end
        }

        return $request;
    }

    public function getHistory($annual_cost_center_id,$product_id,$order_number)
    {
        $select = array(
          	'tb_inventory_purchase_requisitions.pr_number',
          	'tb_inventory_purchase_requisitions.pr_date',
          	'tb_inventory_purchase_requisitions.created_by',
          	'tb_inventory_purchase_requisition_details.id',
          	'tb_inventory_purchase_requisition_details.quantity',
          	'tb_inventory_purchase_requisition_details.unit',
          	'tb_inventory_purchase_requisition_details.price',
          	'tb_inventory_purchase_requisition_details.total',
          	'sum(case when tb_inventory_purchase_requisition_detail_progress.poe_qty is null then 0.00 else tb_inventory_purchase_requisition_detail_progress.poe_qty end) as "poe_qty"',  
          	'sum(case when tb_inventory_purchase_requisition_detail_progress.poe_value is null then 0.00 else tb_inventory_purchase_requisition_detail_progress.poe_value end) as "poe_value"',         
          	'sum(case when tb_inventory_purchase_requisition_detail_progress.po_qty is null then 0.00 else tb_inventory_purchase_requisition_detail_progress.po_qty end) as "po_qty"',  
          	'sum(case when tb_inventory_purchase_requisition_detail_progress.po_value is null then 0.00 else tb_inventory_purchase_requisition_detail_progress.po_value end) as "po_value"',         
          	'sum(case when tb_inventory_purchase_requisition_detail_progress.grn_qty is null then 0.00 else tb_inventory_purchase_requisition_detail_progress.grn_qty end) as "grn_qty"',  
          	'sum(case when tb_inventory_purchase_requisition_detail_progress.poe_value is null then 0.00 else tb_inventory_purchase_requisition_detail_progress.grn_value end) as "grn_value"',         
        );

        $group = array(
          	'tb_inventory_purchase_requisitions.pr_number',
          	'tb_inventory_purchase_requisitions.pr_date',
          	'tb_inventory_purchase_requisitions.created_by',
         	'tb_inventory_purchase_requisition_details.id',
          	'tb_inventory_purchase_requisition_details.quantity',
          	'tb_inventory_purchase_requisition_details.unit',
          	'tb_inventory_purchase_requisition_details.price',
          	'tb_inventory_purchase_requisition_details.total',  
        );

        $this->connection->select($select);
        $this->connection->from('tb_inventory_purchase_requisition_details');
        $this->connection->join('tb_inventory_purchase_requisitions', 'tb_inventory_purchase_requisitions.id = tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id');
        $this->connection->join('tb_inventory_monthly_budgets', 'tb_inventory_monthly_budgets.id = tb_inventory_purchase_requisition_details.inventory_monthly_budget_id');
        $this->connection->join('tb_inventory_purchase_requisition_detail_progress', 'tb_inventory_purchase_requisition_detail_progress.inventory_purchase_requisition_detail_id = tb_inventory_purchase_requisition_details.id','left');
        $this->connection->where('tb_inventory_purchase_requisitions.annual_cost_center_id', $annual_cost_center_id);
        $this->connection->where('tb_inventory_monthly_budgets.product_id', $product_id);
        $this->connection->where('tb_inventory_purchase_requisitions.order_number <',$order_number);
        $this->connection->group_by($group);
        $query  = $this->connection->get();
        $return = $query->result_array();

        return $return;
    }

    public function isDocumentNumberExists($pr_number)
    {
	   $this->connection->where('pr_number', $pr_number);
	   $query = $this->connection->get('tb_inventory_purchase_requisitions');

	   if ($query->num_rows() > 0)
	   return true;

	   return false;
    }

    public function isValidDocumentQuantity($pr_number)
    {
    	$this->connection->select_sum('tb_receipt_items.received_quantity', 'received_quantity');
    	$this->connection->select_sum('tb_stock_in_stores.quantity', 'stored_quantity');
    	$this->connection->select('tb_receipt_items.pr_number');
    	$this->connection->from('tb_receipt_items');
    	$this->connection->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_receipt_items.stock_in_stores_id');
    	$this->connection->where('tb_receipt_items.pr_number', $pr_number);
    	$this->connection->group_by('tb_receipt_items.pr_number');

    	$query  = $this->connection->get();
    	$row    = $query->unbuffered_row('array');

    	if ($row['received_quantity'] === $row['received_quantity'])
    	  return true;

    	return false;
    }

  	public function save()
  	{
		$document_id           = (isset($_SESSION['inventory']['id'])) ? $_SESSION['inventory']['id'] : NULL;
		$document_edit         = (isset($_SESSION['inventory']['edit'])) ? $_SESSION['inventory']['edit'] : NULL;
		$order_number          = $_SESSION['inventory']['order_number'];
		$pr_number             = $_SESSION['inventory']['order_number'].$_SESSION['inventory']['format_order_number'];
		$pr_date               = date('Y-m-d');
		$required_date         = $_SESSION['inventory']['required_date'];
		$deliver_to            = (empty($_SESSION['inventory']['deliver_to'])) ? NULL : $_SESSION['inventory']['deliver_to'];
		$suggested_supplier    = (empty($_SESSION['inventory']['suggested_supplier'])) ? NULL : $_SESSION['inventory']['suggested_supplier'];
		$created_by            = (empty($_SESSION['inventory']['created_by'])) ? NULL : $_SESSION['inventory']['created_by'];
	    $annual_cost_center_id = $_SESSION['inventory']['annual_cost_center_id'];
		$category              = $_SESSION['inventory']['category_name'];
		$product_category_id   = $_SESSION['inventory']['product_category_id'];
		$notes                 = (empty($_SESSION['inventory']['notes'])) ? NULL : $_SESSION['inventory']['notes'];

		$this->connection->trans_begin();

		if ($document_id === NULL){
		  	$this->connection->set('product_category_id', $product_category_id);
		  	$this->connection->set('order_number', $order_number);
		  	$this->connection->set('pr_number', $pr_number);
		  	$this->connection->set('pr_date', $pr_date);
		  	$this->connection->set('required_date', $required_date);
		  	$this->connection->set('suggested_supplier', $suggested_supplier);
		  	$this->connection->set('deliver_to', $deliver_to);
		  	$this->connection->set('status', 'pending');
		  	$this->connection->set('notes', $notes);
		  	$this->connection->set('created_by', $created_by);
		  	$this->connection->set('updated_by', config_item('auth_person_name'));
		  	$this->connection->set('created_at', date('Y-m-d H:i:s'));
		  	$this->connection->set('updated_at', date('Y-m-d H:i:s'));
	      	$this->connection->set('annual_cost_center_id', $annual_cost_center_id);
		  	$this->connection->insert('tb_inventory_purchase_requisitions');

		  	$document_id = $this->connection->insert_id();
		} else {
		  	$this->connection->set('required_date', $required_date);
		  	$this->connection->set('suggested_supplier', $suggested_supplier);
		  	$this->connection->set('deliver_to', $deliver_to);
		  	$this->connection->set('status', 'pending');
		  	$this->connection->set('notes', $notes);
		  	$this->connection->set('updated_at', date('Y-m-d'));
		  	$this->connection->set('updated_by', config_item('auth_person_name'));
		  	$this->connection->where('id', $document_id);
		  	$this->connection->update('tb_inventory_purchase_requisitions');

		  	$this->connection->select('tb_inventory_purchase_requisition_details.*');
            $this->connection->from('tb_inventory_purchase_requisition_details');
            $this->connection->where('tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id', $document_id);

            $query  = $this->connection->get();
            $result = $query->result_array();

            foreach ($result as $data) {
                $this->connection->from('tb_inventory_monthly_budgets');
                $this->connection->where('id', $data['inventory_monthly_budget_id']);

                $query        = $this->connection->get();
                $budget_monthly = $query->unbuffered_row('array');

                $year = $budget_monthly['year_number'];
                $month = $budget_monthly['month_number'];
                // $annual_cost_center_id = $budget_monthly['annual_cost_center_id'];
                $product_id = $budget_monthly['product_id'];

                for ($i = $month; $i < 13; $i++) {
                    $this->connection->set('ytd_used_quantity', 'ytd_used_quantity - ' . $data['quantity'], FALSE);
                    $this->connection->set('ytd_used_budget', 'ytd_used_budget - ' . $data['total'], FALSE);
                    // $this->connection->where('tb_capex_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);
                    $this->connection->where('tb_inventory_monthly_budgets.product_id', $product_id);
                    $this->connection->where('tb_inventory_monthly_budgets.year_number', $year);
                    $this->connection->where('tb_inventory_monthly_budgets.month_number', $i);
                    $this->connection->update('tb_inventory_monthly_budgets');
                }

                $this->connection->set('mtd_used_quantity', 'mtd_used_quantity - ' . $data['quantity'], FALSE);
                $this->connection->set('mtd_used_budget', 'mtd_used_budget +- ' . $data['total'], FALSE);
                $this->connection->where('id', $data['inventory_monthly_budget_id']);
                $this->connection->update('tb_inventory_monthly_budgets');
            }

            $this->connection->where('pr_number', $pr_number);
            $this->connection->delete('tb_inventory_used_budgets');

		  	$this->connection->where('inventory_purchase_requisition_id', $document_id);
		  	$this->connection->delete('tb_inventory_purchase_requisition_details');
		}

		foreach ($_SESSION['inventory']['items'] as $key => $data){
		    if (empty($data['inventory_monthly_budget_id']) || $data['inventory_monthly_budget_id'] == NULL){
	           // NEW GROUP
	            $this->connection->select('tb_product_groups.id');
	            $this->connection->from('tb_product_groups');
			    $this->connection->where('UPPER(tb_product_groups.group_name)', strtoupper($data['group_name']));

			    $query  = $this->connection->get();

				if ($query->num_rows() == 0){
			  		$this->connection->set('product_category_id', $product_category_id);
			  		$this->connection->set('group_name', $data['group_name']);
			  		$this->connection->set('group_code', strtoupper($data['group_name']));
			  		$this->connection->set('group_type', 'inventory');
			  		$this->connection->set('created_at', date('Y-m-d'));
			  		$this->connection->set('created_by', config_item('auth_person_name'));
			  		$this->connection->set('updated_at', date('Y-m-d'));
			  		$this->connection->set('updated_by', config_item('auth_person_name'));
			  		$this->connection->insert('tb_product_groups');

			  		$product_group_id = $this->connection->insert_id();
				} else {
			  		$product_group    = $query->unbuffered_row();
			  		$product_group_id = $product_group->id;
				}

				// NEW UNIT
				$this->connection->select('tb_product_measurements.id');
				$this->connection->from('tb_product_measurements');
				$this->connection->where('UPPER(tb_product_measurements.measurement_symbol)', strtoupper($data['unit']));

				$query  = $this->connection->get();

				if ($query->num_rows() == 0){
			  		$this->connection->set('measurement_name', $data['unit']);
			  		$this->connection->set('measurement_symbol', strtolower($data['unit']));
			  		$this->connection->set('created_at', date('Y-m-d'));
			  		$this->connection->set('created_by', config_item('auth_person_name'));
			  		$this->connection->set('updated_at', date('Y-m-d'));
			  		$this->connection->set('updated_by', config_item('auth_person_name'));
			  		$this->connection->insert('tb_product_measurements');

			  		$product_measurement_id = $this->connection->insert_id();
				} else {
			  		$product_measurement    = $query->unbuffered_row();
			  		$product_measurement_id = $product_measurement->id;
				}

				// NEW PRODUCT
				$this->connection->select('tb_products.id');
				$this->connection->from('tb_products');
				$this->connection->where('UPPER(tb_products.product_code)', strtoupper($data['part_number']));

				$query  = $this->connection->get();

				if ($query->num_rows() == 0){
			  		$this->connection->set('product_measurement_id', $product_measurement_id);
			  		$this->connection->set('product_group_id', $product_group_id);
			  		$this->connection->set('product_name', $data['product_name']);
			  		$this->connection->set('product_code', $data['part_number']);
			  		$this->connection->set('part_number', $data['part_number']);
			  		$this->connection->set('product_type', 'inventory');
			  		$this->connection->set('unit_measurement', $data['unit']);
			  		$this->connection->set('price', $data['price']);
			  		$this->connection->set('additional_info', $data['additional_info']);
			  		$this->connection->set('created_at', date('Y-m-d'));
			  		$this->connection->set('created_by', config_item('auth_person_name'));
			  		$this->connection->set('updated_at', date('Y-m-d'));
			  		$this->connection->set('updated_by', config_item('auth_person_name'));
			  		$this->connection->insert('tb_products');

			  		$product_id = $this->connection->insert_id();
				} else {
			  		$product    = $query->unbuffered_row();
			  		$product_id = $product->id;
				}

				// NEW PRICE
				$this->connection->from('tb_product_purchase_prices');
				$this->connection->where('tb_product_purchase_prices.product_id', $product_id);

				$query  = $this->connection->get();

				if ($query->num_rows() == 0){
			  		$this->connection->set('price_before', 0);
			  		$this->connection->set('current_price', $data['price']);
			  		$this->connection->set('cur_date', date('Y-m-d'));
			  		$this->connection->set('created_at', date('Y-m-d'));
			  		$this->connection->set('created_by', config_item('auth_person_name'));
			  		$this->connection->set('updated_at', date('Y-m-d'));
			  		$this->connection->set('updated_by', config_item('auth_person_name'));
			  		$this->connection->insert('tb_product_purchase_prices');

			  		$purchase_price_id = $this->connection->insert_id();
				} else {
			  		$purchase_price    = $query->unbuffered_row();
			  		$purchase_price_id = $purchase_price->id;

			  		$this->connection->set('price_before', $purchase_price->current_price);
			  		$this->connection->set('current_price', $data['price']);
			  		$this->connection->set('cur_date', date('Y-m-d'));
			  		$this->connection->set('updated_at', date('Y-m-d'));
			  		$this->connection->set('updated_by', config_item('auth_person_name'));
			  		$this->connection->where('id', $purchase_price_id);
			  		$this->connection->update('tb_product_purchase_prices');
				}

				// NEW BUDGET
				$this->connection->from('tb_inventory_monthly_budgets');
				$this->connection->where('tb_inventory_monthly_budgets.product_id', $product_id);
				$this->connection->where('tb_inventory_monthly_budgets.month_number', $this->budget_month);
				$this->connection->where('tb_inventory_monthly_budgets.year_number', $this->budget_year);

				$query  = $this->connection->get();

				if ($query->num_rows() == 0){
					$this->connection->set('product_id', $product_id);
					$this->connection->set('month_number', $this->budget_month);
					$this->connection->set('year_number', $this->budget_year);
					$this->connection->set('initial_quantity', floatval(0));
					$this->connection->set('initial_budget', floatval(0));
					$this->connection->set('mtd_quantity', floatval(0));
					$this->connection->set('mtd_budget', floatval(0));
					$this->connection->set('mtd_used_quantity', floatval(0));
					$this->connection->set('mtd_used_budget', floatval(0));
					$this->connection->set('mtd_used_quantity_import', floatval(0));
					$this->connection->set('mtd_used_budget_import', floatval(0));
					$this->connection->set('mtd_prev_month_quantity', floatval(0));
					$this->connection->set('mtd_prev_month_budget', floatval(0));
					$this->connection->set('mtd_prev_month_used_quantity', floatval(0));
					$this->connection->set('mtd_prev_month_used_budget', floatval(0));
					$this->connection->set('mtd_prev_month_used_quantity_import', floatval(0));
					$this->connection->set('mtd_prev_month_used_budget_import', floatval(0));
					$this->connection->set('ytd_quantity', floatval(0));
					$this->connection->set('ytd_budget', floatval(0));
					$this->connection->set('ytd_used_quantity', floatval(0));
					$this->connection->set('ytd_used_budget', floatval(0));
					$this->connection->set('ytd_used_quantity_import', floatval(0));
					$this->connection->set('ytd_used_budget_import', floatval(0));
					$this->connection->set('created_at', date('Y-m-d'));
					$this->connection->set('created_by', config_item('auth_person_name'));
					$this->connection->set('updated_at', date('Y-m-d'));
					$this->connection->set('updated_by', config_item('auth_person_name'));
					$this->connection->insert('tb_inventory_monthly_budgets');

					$inventory_monthly_budget_id = $this->connection->insert_id();

					for ($m = 1; $m < $this->budget_month; $m++){
					    // PREV BUDGET
					    $this->connection->set('product_id', $product_id);
					    $this->connection->set('month_number', $m);
					    $this->connection->set('year_number', $this->budget_year);
					    $this->connection->set('initial_quantity', floatval(0));
					    $this->connection->set('initial_budget', floatval(0));
					    $this->connection->set('mtd_quantity', floatval(0));
					    $this->connection->set('mtd_budget', floatval(0));
					    $this->connection->set('mtd_used_quantity', floatval(0));
					    $this->connection->set('mtd_used_budget', floatval(0));
					    $this->connection->set('mtd_used_quantity_import', floatval(0));
					    $this->connection->set('mtd_used_budget_import', floatval(0));
					    $this->connection->set('mtd_prev_month_quantity', floatval(0));
					    $this->connection->set('mtd_prev_month_budget', floatval(0));
					    $this->connection->set('mtd_prev_month_used_quantity', floatval(0));
					    $this->connection->set('mtd_prev_month_used_budget', floatval(0));
					    $this->connection->set('mtd_prev_month_used_quantity_import', floatval(0));
					    $this->connection->set('mtd_prev_month_used_budget_import', floatval(0));
					    $this->connection->set('ytd_quantity', floatval(0));
					    $this->connection->set('ytd_budget', floatval(0));
					    $this->connection->set('ytd_used_quantity', floatval(0));
					    $this->connection->set('ytd_used_budget', floatval(0));
					    $this->connection->set('ytd_used_quantity_import', floatval(0));
					    $this->connection->set('ytd_used_budget_import', floatval(0));
					    $this->connection->set('created_at', date('Y-m-d'));
					    $this->connection->set('created_by', config_item('auth_person_name'));
					    $this->connection->set('updated_at', date('Y-m-d'));
					    $this->connection->set('updated_by', config_item('auth_person_name'));
					    $this->connection->insert('tb_inventory_monthly_budgets');
					}

					for ($am = 12; $am > $this->budget_month; $am--){
					    // PREV BUDGET
					    $this->connection->set('product_id', $product_id);
					    $this->connection->set('month_number', $am);
					    $this->connection->set('year_number', $this->budget_year);
					    $this->connection->set('initial_quantity', floatval(0));
					    $this->connection->set('initial_budget', floatval(0));
					    $this->connection->set('mtd_quantity', floatval(0));
					    $this->connection->set('mtd_budget', floatval(0));
					    $this->connection->set('mtd_used_quantity', floatval(0));
					    $this->connection->set('mtd_used_budget', floatval(0));
					    $this->connection->set('mtd_used_quantity_import', floatval(0));
					    $this->connection->set('mtd_used_budget_import', floatval(0));
					    $this->connection->set('mtd_prev_month_quantity', floatval(0));
					    $this->connection->set('mtd_prev_month_budget', floatval(0));
					    $this->connection->set('mtd_prev_month_used_quantity', floatval(0));
					    $this->connection->set('mtd_prev_month_used_budget', floatval(0));
					    $this->connection->set('mtd_prev_month_used_quantity_import', floatval(0));
					    $this->connection->set('mtd_prev_month_used_budget_import', floatval(0));
					    $this->connection->set('ytd_quantity', floatval(0));
					    $this->connection->set('ytd_budget', floatval(0));
					    $this->connection->set('ytd_used_quantity', floatval(0));
					    $this->connection->set('ytd_used_budget', floatval(0));
					    $this->connection->set('ytd_used_quantity_import', floatval(0));
					    $this->connection->set('ytd_used_budget_import', floatval(0));
					    $this->connection->set('created_at', date('Y-m-d'));
					    $this->connection->set('created_by', config_item('auth_person_name'));
					    $this->connection->set('updated_at', date('Y-m-d'));
					    $this->connection->set('updated_by', config_item('auth_person_name'));
					    $this->connection->insert('tb_inventory_monthly_budgets');
					}
				} else {
				    $inventory_monthly_budget    = $query->unbuffered_row();
				    $inventory_monthly_budget_id = $inventory_monthly_budget->id;
				}
		    } else {
			  	$inventory_monthly_budget_id = $data['inventory_monthly_budget_id'];
			  	$product_id = $data['product_id'];
		    }

	        $year = $this->budget_year;
	        $month = $this->budget_month;

	        for ($i = $month; $i < 13; $i++) {
	            $this->connection->set('ytd_used_quantity', 'ytd_used_quantity + ' . $data['quantity'], FALSE);
	            $this->connection->set('ytd_used_budget', 'ytd_used_budget + ' . $data['total'], FALSE);
	            $this->connection->where('tb_inventory_monthly_budgets.product_id', $product_id);
	            $this->connection->where('tb_inventory_monthly_budgets.year_number', $year);
	            $this->connection->where('tb_inventory_monthly_budgets.month_number', $i);
	            $this->connection->update('tb_inventory_monthly_budgets');
	        }

	        //insert data on used budget 
	        $this->connection->set('inventory_monthly_budget_id', $capex_monthly_budget_id);
	        $this->connection->set('inventory_purchase_requisition_id', $document_id);
	        $this->connection->set('pr_number', $pr_number);
	        // $this->connection->set('cost_center', $cost_center_name);
	        $this->connection->set('year_number', $this->budget_year);
	        $this->connection->set('month_number', $this->budget_month);
	        $this->connection->set('product_name', $data['product_name']);
	        $this->connection->set('product_group', $data['group']);
	        $this->connection->set('product_code', $data['part_number']);
	        $this->connection->set('additional_info', $data['additional_info']);
	        $this->connection->set('used_budget', $data['total']);
	        $this->connection->set('used_quantity', $data['quantity']);
	        $this->connection->set('created_at', date('Y-m-d H:i:s'));
	        $this->connection->set('created_by', config_item('auth_person_name'));
	        $this->connection->set('part_number', $data['part_number']);
	        $this->connection->insert('tb_inventory_used_budgets');

	        $this->connection->set('mtd_used_quantity', 'mtd_used_quantity + ' . $data['quantity'], FALSE);
	        $this->connection->set('mtd_used_budget', 'mtd_used_budget + ' . $data['total'], FALSE);
	        $this->connection->where('id', $inventory_monthly_budget_id);
	        $this->connection->update('tb_inventory_monthly_budgets');

		    $this->connection->set('inventory_purchase_requisition_id', $document_id);
		    $this->connection->set('inventory_monthly_budget_id', $inventory_monthly_budget_id);
		    $this->connection->set('part_number', $data['part_number']);
		    $this->connection->set('additional_info', $data['additional_info']);
		    $this->connection->set('unit', $data['unit']);
		    $this->connection->set('sort_order', floatval($key));
		    $this->connection->set('quantity', floatval($data['quantity']));
		    $this->connection->set('price', floatval($data['price']));
		    $this->connection->set('total', floatval($data['total']));
            $this->connection->set('reference_ipc', $data['reference_ipc']);
		    $this->connection->insert('tb_inventory_purchase_requisition_details');
		}

        if(!empty($_SESSION['inventory']['attachment'])){
            foreach ($_SESSION["inventory"]["attachment"] as $key) {
                $this->connection->set('id_purchase', $document_id);
                $this->connection->set('file', $key);
                $this->connection->set('tipe', 'inventory');
                $this->connection->insert('tb_attachment');
            }
        }     

		if ($this->connection->trans_status() === FALSE)
		  	return FALSE;

		$this->connection->trans_commit();

        if($this->config->item('access_from')!='localhost'){
            $this->send_mail($document_id, 19);
        }

		return TRUE;
  	}

  	public function delete()
  	{
		$this->connection->trans_begin();

		$id = $this->input->post('id');

		$this->connection->select('pr_number, warehouse');
		$this->connection->where('id', $id);
		$this->connection->from('tb_inventory_purchase_requisitions');

		$query = $this->connection->get();
		$row   = $query->unbuffered_row('array');

		$pr_number  = $row['pr_number'];
		$warehouse        = $row['warehouse'];

		$this->connection->select('tb_receipt_items.id, tb_receipt_items.stock_in_stores_id, tb_receipt_items.received_quantity, tb_receipt_items.received_unit_value, tb_stock_in_stores.stock_id, tb_stock_in_stores.serial_id, tb_stock_in_stores.stores');
		$this->connection->from('tb_receipt_items');
		$this->connection->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_receipt_items.stock_in_stores_id');
		$this->connection->where('tb_receipt_items.pr_number', $pr_number);

		$query  = $this->connection->get();
		$result = $query->result_array();

		foreach ($result as $data) {
		  	$this->connection->set('stock_id', $data['stock_id']);
		  	$this->connection->set('serial_id', $data['serial_id']);
		  	$this->connection->set('warehouse', $warehouse);
		  	$this->connection->set('stores', $data['stores']);
		  	$this->connection->set('date_of_entry', date('Y-m-d'));
		  	$this->connection->set('period_year', config_item('period_year'));
		  	$this->connection->set('period_month', config_item('period_month'));
		  	$this->connection->set('document_type', 'REMOVAL');
		  	$this->connection->set('pr_number', $pr_number);
		  	$this->connection->set('issued_to', 'DELETE DOCUMENT');
		  	$this->connection->set('issued_by', config_item('auth_person_name'));
		  	$this->connection->set('quantity', 0 - floatval($data['received_quantity']));
		  	$this->connection->set('unit_value', floatval($data['received_unit_value']));
		  	$this->connection->insert('tb_stock_cards');

		  	$this->connection->where('id', $data['id']);
		  	$this->connection->delete('tb_receipt_items');

		  	$this->connection->where('id', $data['stock_in_stores_id']);
		  	$this->connection->delete('tb_stock_in_stores');
		}

		$this->connection->where('id', $id);
		$this->connection->delete('tb_inventory_purchase_requisitions');

		if ($this->connection->trans_status() === FALSE)
		  	return FALSE;

		$this->connection->trans_commit();
		return TRUE;
  	}

    public function searchBudget($category)
    {
        $query = "";
        $this->column_select = array(
            'SUM(tb_inventory_monthly_budgets.mtd_quantity) as quantity',
            'SUM(tb_inventory_monthly_budgets.mtd_budget) as budget',
            'SUM(tb_inventory_monthly_budgets.mtd_used_quantity) as used_quantity',
            'SUM(tb_inventory_monthly_budgets.mtd_used_budget) as used_budget',
            'tb_products.product_name',
            'tb_products.product_code',
            'tb_product_groups.group_name',
            'tb_product_measurements.measurement_symbol',
            'tb_product_purchase_prices.current_price',
            'tb_inventory_monthly_budgets.product_id',
        );

        $this->column_groupby = array(
            'tb_products.product_name',
            'tb_products.product_code',
            'tb_product_groups.group_name',
            'tb_product_measurements.measurement_symbol',
            'tb_product_purchase_prices.current_price',
            'tb_inventory_monthly_budgets.product_id',
        );

        $this->connection->select($this->column_select);
        $this->connection->from('tb_inventory_monthly_budgets');
        $this->connection->join('tb_products', 'tb_products.id = tb_inventory_monthly_budgets.product_id');
        $this->connection->join('tb_product_purchase_prices', 'tb_product_purchase_prices.product_id = tb_products.id');
        $this->connection->join('tb_product_measurements', 'tb_product_measurements.id = tb_products.product_measurement_id');
        $this->connection->join('tb_product_groups', 'tb_product_groups.id = tb_products.product_group_id');
        $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_product_groups.product_category_id');
        $this->connection->where('tb_product_categories.category_name', $category);
        $this->connection->where('tb_inventory_monthly_budgets.year_number', $this->budget_year);
        $this->connection->group_by($this->column_groupby);
        $this->connection->order_by('tb_products.product_name ASC, tb_products.product_code ASC');
        $query  = $this->connection->get();

        $result = $query->result_array();
        foreach ($result as $key => $value) {
            $result[$key]['maximum_quantity'] = $value['quantity'] - $value['used_quantity'];
            $result[$key]['maximum_price'] = $value['budget'] - $value['used_budget'];

            //get mtd_budget
            $select = array(
                'tb_inventory_monthly_budgets.ytd_quantity',
                'tb_inventory_monthly_budgets.ytd_budget',
                'tb_inventory_monthly_budgets.ytd_used_quantity',
                'tb_inventory_monthly_budgets.ytd_used_budget',
                'tb_inventory_monthly_budgets.id',
            );

            $this->connection->select($select);
            $this->connection->from('tb_inventory_monthly_budgets');
            $this->connection->where('tb_inventory_monthly_budgets.product_id', $value['product_id']);
            $this->connection->where('tb_inventory_monthly_budgets.month_number', $this->budget_month);
            $this->connection->where('tb_inventory_monthly_budgets.year_number', $this->budget_year);
            $query_row = $this->connection->get();
            $row   = $query_row->unbuffered_row('array');
            $result[$key]['mtd_quantity'] = $row['ytd_quantity'] - $row['ytd_used_quantity'];
            $result[$key]['mtd_budget'] = $row['ytd_budget'] - $row['ytd_used_budget'];
            $result[$key]['inventory_monthly_budget_id'] = $row['id'];
            //end get mtd budget

            //get onhand & min qty
            $this->db->from('tb_master_items');
            $this->db->where('UPPER(tb_master_items.description)', strtoupper($value['product_name']));

            $query  = $this->db->get();

            if ($query->num_rows() > 0){
                $master_item = $query->unbuffered_row('array');

                $result[$key]['minimum_quantity'] = $master_item['minimum_quantity'];

                $this->db->select('tb_stocks.total_quantity, tb_stocks.average_value');
                $this->db->from('tb_stocks');
                $this->db->where('tb_stocks.item_id', $master_item['id']);
                $this->db->where('tb_stocks.condition', 'SERVICEABLE');

                $query  = $this->db->get();

                if ($query->num_rows() > 0){
                    $stock = $query->unbuffered_row('array');

                    $result[$key]['on_hand_quantity'] = $stock['total_quantity'];
                    $result[$key]['price']            = $stock['average_value'];
                } else {
                    $result[$key]['on_hand_quantity'] = 0;
                    $result[$key]['price']            = 0;
                }
            } else {
                $result[$key]['minimum_quantity'] = 0;
                $result[$key]['on_hand_quantity'] = 0;
                $result[$key]['price']            = 0;
            }
            //end
        }
        return $result;
    }

  	public function searchItemsByPartNumber($category)
  	{
		$this->column_select = array(
		  	'tb_master_items.id',
		 	'tb_master_items.group',
		  	'tb_master_items.description',
		  	'tb_master_items.part_number',
		  	'tb_master_items.alternate_part_number',
		  	'tb_master_items.minimum_quantity',
		  	'tb_master_items.unit',
		  	'tb_stocks.total_quantity AS on_hand_quantity',
		  	'tb_stocks.average_value AS price'
		);

		$this->db->select($this->column_select);
		$this->db->from('tb_master_items');
		$this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
		$this->db->join('tb_stocks', 'tb_stocks.item_id = tb_master_items.id');
		$this->db->where('tb_master_item_groups.status', 'AVAILABLE');
		$this->db->where('tb_master_item_groups.category', $category);

		$this->db->order_by('tb_master_items.group ASC, tb_master_items.description ASC');

		$query  = $this->db->get();
		$result = $query->result_array();

		return $result;
  	}

  	public function searchItemsByProductName($category)
  	{
		$this->db->select('tb_master_items.description');
		$this->db->from('tb_master_items');
		$this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
		$this->db->where('UPPER(tb_master_item_groups.category)', strtoupper($category));

		$query  = $this->db->get();
		$result = $query->result_array();
		$return = array();

		foreach ($result as $row) {
		  	$return[] = $row['description'];
		}

		return $return;
  	}

  	public function searchItemGroups($category)
  	{
		$this->connection->select('tb_product_groups.group_name');
		$this->connection->from('tb_product_groups');
		$this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_product_groups.product_category_id');
		$this->connection->where('UPPER(tb_product_categories.category_name)', strtoupper($category));

		$query  = $this->connection->get();
		$result = $query->result_array();
		$return = array();

		foreach ($result as $row) {
		  	$return[] = $row['group_name'];
		}

		return $return;
  	}

  	public function getAvailableVendors($category)
  	{
		$this->db->select('tb_master_vendors.vendor');
		$this->db->from('tb_master_vendors');
		$this->db->join('tb_master_vendor_categories', 'tb_master_vendors.vendor = tb_master_vendor_categories.vendor');
		$this->db->where('UPPER(tb_master_vendors.status)', 'AVAILABLE');
		$this->db->where('UPPER(tb_master_vendor_categories.category)', strtoupper($category));

		$query  = $this->db->get();
		$result = $query->result_array();
		$return = array();

		foreach ($result as $row) {
		  	$return[] = $row['vendor'];
		}

		return $return;
  	}

  	public function approve($id,$notes)
    {
        $this->connection->trans_begin();

        $this->connection->select('tb_inventory_purchase_requisitions.*');
        $this->connection->from('tb_inventory_purchase_requisitions');
        $this->connection->where('tb_inventory_purchase_requisitions.id',$id);
        $query    = $this->connection->get();
        $request  = $query->unbuffered_row('array');
        $approval_notes = $request['approval_notes'];
        $department = getDepartmentByAnnualCostCenterId($request['annual_cost_center_id']);

        if(config_item('auth_role')=='BUDGETCONTROL' && $request['status']=='pending'){
            $this->connection->set('status','WAITING FOR HEAD DEPT');
            $this->connection->set('approved_date',date('Y-m-d H:i:s'));
            $this->connection->set('approved_by',config_item('auth_person_name'));
            if($notes!=''){
                $this->connection->set('approved_notes',$approval_notes.'Budgetcontrol : '.$notes.',');
            }            
            $this->connection->where('id',$id);
            $this->connection->update('tb_inventory_purchase_requisitions');
            $level = -1;
        }else if(config_item('as_head_department')=='yes' && config_item('head_department')==$department['department_name'] && $request['status']=='WAITING FOR HEAD DEPT'){
            $this->connection->set('status','approved');
            $this->connection->set('head_approved_date',date('Y-m-d H:i:s'));
            $this->connection->set('head_approved_by',config_item('auth_person_name'));
            if($notes!=''){
                $this->connection->set('approved_notes',$approval_notes.'Head-Dept : '.$notes);
            }
            $this->connection->where('id',$id);
            $this->connection->update('tb_inventory_purchase_requisitions');
            $level = 8;
        }

        

        if ($this->connection->trans_status() === FALSE)
            return FALSE;

        $this->connection->trans_commit();
        if($level>0){
            if($this->config->item('access_from')!='localhost'){
                $this->send_mail($id, $level,'approved');
            }
        }
        if($level<0){
            if($this->config->item('access_from')!='localhost'){
                $this->send_mail_to_head_dept($id);
            }
        }
        return TRUE;
    }

    public function send_mail($doc_id, $level, $tipe=null)
    {
        $this->connection->from('tb_inventory_purchase_requisitions');
        $this->connection->where('id', $doc_id);
        $query = $this->connection->get();
        $row = $query->unbuffered_row('array');

        $recipientList = $this->getNotifRecipient($level);
        $recipient = array();
        foreach ($recipientList as $key) {
          array_push($recipient, $key->email);
        }

        $from_email = "bifa.acd@gmail.com";
        $to_email = "aidanurul99@rocketmail.com";
        $ket_level = '';
        // if ($level == 14) {
        //   $ket_level = 'Finance Manager';
        // } elseif ($level == 10) {
        //   $ket_level = 'Head Of School';
        // } elseif ($level == 11) {
        //   $ket_level = 'Chief Of Finance';
        // } elseif ($level == 3) {
        //   $ket_level = 'VP Finance';
        // }elseif ($level == 16) {
        //   $ket_level = 'CHIEF OPERATION OFFICER';
        // }

        $levels_and_roles = config_item('levels_and_roles');
        $ket_level = $levels_and_roles[$level];

        //Load email library 
        $this->load->library('email');
        $this->email->set_newline("\r\n");
        $message = "<p>Dear " . $ket_level . "</p>";
        if($tipe!=null){
            $message .= "<p>Inventory Request Non-Sparepart Dibawah ini Sudah Terapproved. Silahkan Proses ke Inventory Order Evaluation:</p>";
            $message .= "<ul>";
            $message .= "</ul>";
            $message .= "<p>No Inventory Request Non-Sparepart : " . $row['pr_number'] . "</p>";
        }else{
            $message .= "<p>Berikut permintaan Persetujuan untuk Inventory Request Non-Sparepart :</p>";
            $message .= "<ul>";
            $message .= "</ul>";
            $message .= "<p>No Inventory Request Non-Sparepart : " . $row['pr_number'] . "</p>";
        }
        $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
        $message .= "<p>[ <a href='http://119.2.51.138:7323/expense_request/' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
        $message .= "<p>Thanks and regards</p>";
        $this->email->from($from_email, 'Material Resource Planning');
        $this->email->to($recipient);
        $this->email->subject('Permintaan Approval Inventory Request Non-Sparepart Pesawat No : ' . $row['pr_number']);
        $this->email->message($message);

        //Send mail 
        if ($this->email->send())
          return true;
        else
          return $this->email->print_debugger();
    }

    public function send_mail_to_head_dept($doc_id)
    {
        $this->connection->select('tb_inventory_purchase_requisitions.*');
        $this->connection->from('tb_inventory_purchase_requisitions');
        $this->connection->where('tb_inventory_purchase_requisitions.id',$doc_id);
        $query = $this->connection->get();
        $row = $query->unbuffered_row('array');
        $department = getDepartmentByAnnualCostCenterId($row['annual_cost_center_id']);
        $head_department_username = getHeadDeptByDeptid($department['id']);

        $recipientList = $this->getNotifRecipientByUsername($head_department_username);
        $recipient = array();
        foreach ($recipientList as $key) {
          array_push($recipient, $key->email);
        }

        $from_email = "bifa.acd@gmail.com";
        $to_email = "aidanurul99@rocketmail.com";
        $ket_level = '';
        // if ($level == 14) {
        //   $ket_level = 'Finance Manager';
        // } elseif ($level == 10) {
        //   $ket_level = 'Head Of School';
        // } elseif ($level == 11) {
        //   $ket_level = 'Chief Of Finance';
        // } elseif ($level == 3) {
        //   $ket_level = 'VP Finance';
        // }elseif ($level == 16) {
        //   $ket_level = 'CHIEF OPERATION OFFICER';
        // }

        //Load email library 
        $this->load->library('email');
        $this->email->set_newline("\r\n");
        $message = "<p>Dear Head Dept : " . $department['department_name'] . "</p>";
        $message .= "<p>Berikut permintaan Persetujuan untuk Inventory Request Non-Sparepart Pesawat :</p>";
        $message .= "<ul>";
        $message .= "</ul>";
        $message .= "<p>No Inventory Request Non-Sparepart Pesawat : " . $row['pr_number'] . "</p>";
        $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
        $message .= "<p>[ <a href='http://119.2.51.138:7323/expense_request/' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
        $message .= "<p>Thanks and regards</p>";
        $this->email->from($from_email, 'Material Resource Planning');
        $this->email->to($recipient);
        $this->email->subject('Permintaan Approval Inventory Request Non-Sparepart Pesawat No : ' . $row['pr_number']);
        $this->email->message($message);

        //Send mail 
        if ($this->email->send())
          return true;
        else
          return $this->email->print_debugger();
    }

    public function getNotifRecipient($level)
    {
        $this->db->select('email');
        $this->db->from('tb_auth_users');
        $this->db->where('auth_level', $level);
        return $this->db->get('')->result();
    }

    public function getNotifRecipientByUsername($username)
    {
        $this->db->select('email');
        $this->db->from('tb_auth_users');
        $this->db->where('username', $username);
        return $this->db->get('')->result();
    }

    public function listAttachment($id)
    {
        $this->connection->where('id_purchase', $id);
        $this->connection->where('tipe', 'inventory');
        return $this->connection->get('tb_attachment')->result();
    }
}
