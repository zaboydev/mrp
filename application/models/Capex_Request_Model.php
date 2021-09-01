<?php defined('BASEPATH') or exit('No direct script access allowed');

class Capex_Request_Model extends MY_Model
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
        // $this->categories   = $this->getCategories();
        $this->budget_year  = find_budget_setting('Active Year');
        $this->budget_month = find_budget_setting('Active Month');
        $this->modules        = config_item('module');
        $this->data['modules']        = $this->modules;
    }

    public function getSelectedColumns()
    {
        $return = array(
            'tb_capex_purchase_requisitions.id'                         => NULL,
            'tb_capex_purchase_requisitions.pr_number'                  => 'Document Number',
            'tb_capex_purchase_requisitions.status'                     => 'Status',
            // 'tb_departments.department_name'                            => 'Department Name',
            'tb_cost_centers.cost_center_name'                          => 'Cost Center',
            'tb_capex_purchase_requisitions.pr_date'                    => 'Document Date',
            'tb_capex_purchase_requisitions.required_date'              => 'Required Date',
            'SUM(tb_capex_purchase_requisition_details.total) as total_capex'  => 'Total',
            'tb_capex_purchase_requisitions.notes'                      => 'Requisitions Notes',
            'tb_capex_purchase_requisitions.approved_notes'             => 'Notes',
            NULL                    => 'Attachment',
        );
        if (config_item('as_head_department')=='yes') {
            $return['tb_departments.department_name']  = 'Dept. Name';
        }
        return $return;
    }

    public function getGroupedColumns()
    {
        return array(
            'tb_capex_purchase_requisitions.id',
            'tb_capex_purchase_requisitions.pr_number',
            'tb_capex_purchase_requisitions.status',
            'tb_cost_centers.cost_center_name',
            'tb_capex_purchase_requisitions.pr_date',
            'tb_capex_purchase_requisitions.required_date',
            'tb_capex_purchase_requisitions.notes',
            'tb_departments.department_name',
            'tb_capex_purchase_requisitions.approved_notes'
            // 'SUM(tb_capex_purchase_requisition_detail.total) as total_capex'
        );
    }

    public function getSearchableColumns()
    {
        return array(
            // 'tb_capex_purchase_requisitions.id',
            'tb_capex_purchase_requisitions.pr_number',
            'tb_capex_purchase_requisitions.status',
            'tb_cost_centers.cost_center_name',
            // 'tb_capex_purchase_requisitions.pr_date',
            // 'tb_capex_purchase_requisitions.required_date',
            'tb_capex_purchase_requisitions.notes',
            // 'tb_departments.department_name',
            'tb_capex_purchase_requisitions.approved_notes'
        );
    }

    public function getOrderableColumns()
    {
        $return = array(
            null,
            'tb_capex_purchase_requisitions.pr_number',
            'tb_capex_purchase_requisitions.status',
            // 'tb_departments.department_name',
            'tb_cost_centers.cost_center_name',
            'tb_capex_purchase_requisitions.pr_date',
            'tb_capex_purchase_requisitions.required_date',
            NULL,
            'tb_capex_purchase_requisitions.notes',
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

            $this->connection->where('tb_capex_purchase_requisitions.required_date >= ', $range_date[0]);
            $this->connection->where('tb_capex_purchase_requisitions.required_date <= ', $range_date[1]);
        }

        if (!empty($_POST['columns'][2]['search']['value'])){
            $search_status = $_POST['columns'][2]['search']['value'];

            if($search_status!='all'){
                $this->connection->where('tb_capex_purchase_requisitions.status', $search_status);
            }    
                
        }else{
            if(config_item('auth_role') == 'BUDGETCONTROL'){
                $this->connection->where('tb_capex_purchase_requisitions.status', 'pending');
            } 
            if (config_item('as_head_department')=='yes'){
                $this->connection->where('tb_capex_purchase_requisitions.status', 'WAITING FOR HEAD DEPT');
            }
            
        }

        if (!empty($_POST['columns'][3]['search']['value'])){
            $search_cost_center = $_POST['columns'][3]['search']['value'];
            if($search_cost_center!='all'){
                $this->connection->where('tb_cost_centers.cost_center_name', $search_cost_center);
            }            
        }

        // if (!empty($_POST['columns'][3]['search']['value'])){
        //     $search_category = $_POST['columns'][3]['search']['value'];

        //     $this->connection->where('UPPER(tb_product_categories.category_name)', strtoupper($search_category));
        // }

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

    function getIndex($return = 'array')
    {
        $this->connection->select(array_keys($this->getSelectedColumns()));
        $this->connection->from('tb_capex_purchase_requisitions');
        $this->connection->join('tb_capex_purchase_requisition_details', 'tb_capex_purchase_requisition_details.capex_purchase_requisition_id = tb_capex_purchase_requisitions.id','left');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_capex_purchase_requisitions.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->join('tb_departments', 'tb_departments.id = tb_cost_centers.department_id');
        $this->connection->like('tb_capex_purchase_requisitions.pr_number', $this->budget_year);
        if (is_granted($this->data['modules']['capex_request'], 'approval') === FALSE) {
            $this->connection->where_in('tb_cost_centers.cost_center_name', config_item('auth_annual_cost_centers_name'));
        }
        $this->connection->group_by($this->getGroupedColumns());

        $this->searchIndex();

        $column_order = $this->getOrderableColumns();

        if (isset($_POST['order'])){
            foreach ($_POST['order'] as $key => $order){
                $this->connection->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
            }
        } else {
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
        $this->connection->from('tb_capex_purchase_requisitions');
        $this->connection->join('tb_capex_purchase_requisition_details', 'tb_capex_purchase_requisition_details.capex_purchase_requisition_id = tb_capex_purchase_requisitions.id','left');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_capex_purchase_requisitions.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->join('tb_departments', 'tb_departments.id = tb_cost_centers.department_id');
        $this->connection->like('tb_capex_purchase_requisitions.pr_number', $this->budget_year);
        if (is_granted($this->data['modules']['capex_request'], 'approval') === FALSE) {
            $this->connection->where_in('tb_cost_centers.cost_center_name', config_item('auth_annual_cost_centers_name'));
        }$this->connection->group_by($this->getGroupedColumns());

        $this->searchIndex();

        $query = $this->connection->get();

        return $query->num_rows();
    }

    public function countIndex()
    {
        $this->connection->select(array_keys($this->getSelectedColumns()));
        $this->connection->from('tb_capex_purchase_requisitions');
        $this->connection->join('tb_capex_purchase_requisition_details', 'tb_capex_purchase_requisition_details.capex_purchase_requisition_id = tb_capex_purchase_requisitions.id','left');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_capex_purchase_requisitions.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->join('tb_departments', 'tb_departments.id = tb_cost_centers.department_id');
        $this->connection->like('tb_capex_purchase_requisitions.pr_number', $this->budget_year);
        if (is_granted($this->data['modules']['capex_request'], 'approval') === FALSE) {
            $this->connection->where_in('tb_cost_centers.cost_center_name', config_item('auth_annual_cost_centers_name'));
        }
        $this->connection->group_by($this->getGroupedColumns());

        $query = $this->connection->get();

        return $query->num_rows();
    }

    public function findById($id)
    {
        $this->connection->select('tb_capex_purchase_requisitions.*, tb_cost_centers.cost_center_name,tb_cost_centers.cost_center_code,tb_annual_cost_centers.cost_center_id');
        $this->connection->from('tb_capex_purchase_requisitions');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_capex_purchase_requisitions.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->where('tb_capex_purchase_requisitions.id', $id);

        $query    = $this->connection->get();
        $request  = $query->unbuffered_row('array');

        $select = array(
            'tb_capex_purchase_requisition_details.*',
            'tb_products.product_name',
            'tb_products.product_code as part_number',
            'tb_product_groups.group_name as group',
            'tb_capex_monthly_budgets.product_id',
            'tb_capex_monthly_budgets.ytd_quantity',
            'tb_capex_monthly_budgets.ytd_budget',
            'tb_capex_monthly_budgets.ytd_used_quantity',
            'tb_capex_monthly_budgets.ytd_used_budget',
        );

        $group_by = array(
            'tb_capex_purchase_requisition_details.id',
            'tb_products.product_name',
            'tb_products.product_code',
            'tb_capex_monthly_budgets.product_id',
            'tb_product_groups.group_name',
            'tb_capex_monthly_budgets.ytd_quantity',
            'tb_capex_monthly_budgets.ytd_budget',
            'tb_capex_monthly_budgets.ytd_used_quantity',
            'tb_capex_monthly_budgets.ytd_used_budget',
        );

        $this->connection->select($select);
        $this->connection->from('tb_capex_purchase_requisition_details');
        $this->connection->join('tb_capex_monthly_budgets', 'tb_capex_monthly_budgets.id = tb_capex_purchase_requisition_details.capex_monthly_budget_id');
        $this->connection->join('tb_products', 'tb_products.id = tb_capex_monthly_budgets.product_id');
        // $this->connection->join('tb_product_measurements', 'tb_product_measurements.id = tb_products.product_measurement_id');
        $this->connection->join('tb_product_groups', 'tb_product_groups.id = tb_products.product_group_id');
        $this->connection->where('tb_capex_purchase_requisition_details.capex_purchase_requisition_id', $id);
        $this->connection->group_by($group_by);

        $query = $this->connection->get();

        foreach ($query->result_array() as $key => $value){
            $request['items'][$key] = $value;
            $request['items'][$key]['balance_mtd_quantity']     = $value['ytd_quantity'] - $value['ytd_used_quantity'];
            $request['items'][$key]['balance_mtd_budget']       = $value['ytd_budget'] - $value['ytd_used_budget'];
            $request['items'][$key]['mtd_quantity']     = $value['quantity'] + $value['ytd_quantity'] - $value['ytd_used_quantity'];
            $request['items'][$key]['mtd_budget']       = $value['total'] + $value['ytd_budget'] - $value['ytd_used_budget'];

            $this->column_select = array(
                'SUM(tb_capex_monthly_budgets.mtd_quantity) as quantity',
                'SUM(tb_capex_monthly_budgets.mtd_budget) as budget',
                'SUM(tb_capex_monthly_budgets.mtd_used_quantity) as used_quantity',
                'SUM(tb_capex_monthly_budgets.mtd_used_budget) as used_budget',
                'tb_capex_monthly_budgets.product_id',
                'tb_capex_monthly_budgets.annual_cost_center_id',
            );

            $this->column_groupby = array(                
                'tb_capex_monthly_budgets.product_id',
                'tb_capex_monthly_budgets.annual_cost_center_id',
            );

            $this->connection->select($this->column_select);
            $this->connection->from('tb_capex_monthly_budgets');
            $this->connection->where('tb_capex_monthly_budgets.annual_cost_center_id', $request['annual_cost_center_id']);
            $this->connection->where('tb_capex_monthly_budgets.product_id', $value['product_id']);
            $this->connection->group_by($this->column_groupby);

            $query = $this->connection->get();
            $row   = $query->unbuffered_row('array');

            $request['items'][$key]['maximum_quantity'] = $value['quantity'] + $row['quantity'] - $row['used_quantity'];
            $request['items'][$key]['maximum_price']    =  $value['total'] + $row['budget'] - $row['used_budget'];
            $request['items'][$key]['balance_ytd_quantity']     = $row['quantity'] - $row['used_quantity'];
            $request['items'][$key]['balance_ytd_budget']       = $row['budget'] - $row['used_budget'];            
            $request['items'][$key]['history']          = $this->getHistory($request['annual_cost_center_id'],$value['product_id'],$request['order_number']);
        }

        return $request;
    }

    public function getHistory($annual_cost_center_id,$product_id,$order_number)
    {
        $select = array(
          'tb_capex_purchase_requisitions.pr_number',
          'tb_capex_purchase_requisitions.pr_date',
          'tb_capex_purchase_requisitions.created_by',
          'tb_capex_purchase_requisition_details.id',
          'tb_capex_purchase_requisition_details.quantity',
          'tb_capex_purchase_requisition_details.unit',
          'tb_capex_purchase_requisition_details.price',
          'tb_capex_purchase_requisition_details.total',
          'sum(case when tb_capex_purchase_requisition_detail_progress.poe_qty is null then 0.00 else tb_capex_purchase_requisition_detail_progress.poe_qty end) as "poe_qty"',  
          'sum(case when tb_capex_purchase_requisition_detail_progress.poe_value is null then 0.00 else tb_capex_purchase_requisition_detail_progress.poe_value end) as "poe_value"', 
          'sum(case when tb_capex_purchase_requisition_detail_progress.po_qty is null then 0.00 else tb_capex_purchase_requisition_detail_progress.po_qty end) as "po_qty"', 
          'sum(case when tb_capex_purchase_requisition_detail_progress.po_value is null then 0.00 else tb_capex_purchase_requisition_detail_progress.po_value end) as "po_value"',   
          'sum(case when tb_capex_purchase_requisition_detail_progress.grn_value is null then 0.00 else tb_capex_purchase_requisition_detail_progress.grn_value end) as "grn_value"',
          'sum(case when tb_capex_purchase_requisition_detail_progress.grn_qty is null then 0.00 else tb_capex_purchase_requisition_detail_progress.grn_qty end) as "grn_qty"',        
        );

        $group = array(
          'tb_capex_purchase_requisitions.pr_number',
          'tb_capex_purchase_requisitions.pr_date',
          'tb_capex_purchase_requisitions.created_by',
          'tb_capex_purchase_requisition_details.id',
          'tb_capex_purchase_requisition_details.quantity',
          'tb_capex_purchase_requisition_details.unit',
          'tb_capex_purchase_requisition_details.price',
          'tb_capex_purchase_requisition_details.total',      
        );

        $this->connection->select($select);
        $this->connection->from('tb_capex_purchase_requisition_details');
        $this->connection->join('tb_capex_purchase_requisitions', 'tb_capex_purchase_requisitions.id = tb_capex_purchase_requisition_details.capex_purchase_requisition_id');
        $this->connection->join('tb_capex_monthly_budgets', 'tb_capex_monthly_budgets.id = tb_capex_purchase_requisition_details.capex_monthly_budget_id');
        $this->connection->join('tb_capex_purchase_requisition_detail_progress', 'tb_capex_purchase_requisition_detail_progress.capex_purchase_requisition_detail_id = tb_capex_purchase_requisition_details.id','left');
        $this->connection->where('tb_capex_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);
        $this->connection->where('tb_capex_monthly_budgets.product_id', $product_id);
        $this->connection->where('tb_capex_purchase_requisitions.order_number <',$order_number);
        $this->connection->group_by($group);
        $query  = $this->connection->get();
        $return = $query->result_array();

        return $return;
    }

    public function approve($id,$notes)
    {
        $this->connection->trans_begin();

        $this->connection->select('tb_capex_purchase_requisitions.*');
        $this->connection->from('tb_capex_purchase_requisitions');
        $this->connection->where('tb_capex_purchase_requisitions.id',$id);
        $query    = $this->connection->get();
        $request  = $query->unbuffered_row('array');
        $approval_notes = $request['approval_notes'];
        $department = getDepartmentByAnnualCostCenterId($request['annual_cost_center_id']);

        if(config_item('auth_role')=='BUDGETCONTROL' && $request['status']=='pending'){
            $this->connection->set('status','WAITING FOR HEAD DEPT');
            $this->connection->set('approved_date',date('Y-m-d H:i:s'));
            $this->connection->set('approved_by',config_item('auth_person_name'));
            if($notes!=''){
                $this->connection->set('approved_notes',$approval_notes.'Budgetcontrol : '.$notes);
            }            
            $this->connection->where('id',$id);
            $this->connection->update('tb_capex_purchase_requisitions');
            $level = -1;
        }else if(config_item('as_head_department')=='yes' && config_item('head_department')==$department['department_name'] && $request['status']=='WAITING FOR HEAD DEPT'){
            $this->connection->set('status','approved');
            $this->connection->set('head_approved_date',date('Y-m-d H:i:s'));
            $this->connection->set('head_approved_by',config_item('auth_person_name'));
            if($notes!=''){
                $this->connection->set('approved_notes',$approval_notes.'Head-Dept : '.$notes);
            }
            $this->connection->where('id',$id);
            $this->connection->update('tb_capex_purchase_requisitions');
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

    public function searchBudget($annual_cost_center_id)
    {
        $query = "";
        $this->column_select = array(
            'SUM(tb_capex_monthly_budgets.mtd_quantity) as quantity',
            'SUM(tb_capex_monthly_budgets.mtd_budget) as budget',
            'SUM(tb_capex_monthly_budgets.mtd_used_quantity) as used_quantity',
            'SUM(tb_capex_monthly_budgets.mtd_used_budget) as used_budget',
            'tb_products.product_name',
            'tb_products.product_code',
            'tb_product_groups.group_name',
            'tb_product_measurements.measurement_symbol',
            'tb_product_purchase_prices.current_price',
            'tb_capex_monthly_budgets.annual_cost_center_id',
            'tb_capex_monthly_budgets.product_id',
        );

        $this->column_groupby = array(
            'tb_products.product_name',
            'tb_products.product_code',
            'tb_product_groups.group_name',
            'tb_product_measurements.measurement_symbol',
            'tb_product_purchase_prices.current_price',
            'tb_capex_monthly_budgets.annual_cost_center_id',
            'tb_capex_monthly_budgets.product_id',
        );

        $this->connection->select($this->column_select);
        $this->connection->from('tb_capex_monthly_budgets');
        $this->connection->join('tb_products', 'tb_products.id = tb_capex_monthly_budgets.product_id');
        $this->connection->join('tb_product_purchase_prices', 'tb_product_purchase_prices.product_id = tb_products.id');
        $this->connection->join('tb_product_measurements', 'tb_product_measurements.id = tb_products.product_measurement_id');
        $this->connection->join('tb_product_groups', 'tb_product_groups.id = tb_products.product_group_id');
        $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_product_groups.product_category_id');
        $this->connection->where('tb_capex_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);
        $this->connection->group_by($this->column_groupby);
        $this->connection->order_by('tb_products.product_name ASC, tb_products.product_code ASC');
        $query  = $this->connection->get();

        $result = $query->result_array();
        foreach ($result as $key => $value) {
            $result[$key]['maximum_quantity'] = $value['quantity'] - $value['used_quantity'];
            $result[$key]['maximum_price'] = $value['budget'] - $value['used_budget'];
            $select = array(
                'tb_capex_monthly_budgets.ytd_quantity',
                'tb_capex_monthly_budgets.ytd_budget',
                'tb_capex_monthly_budgets.ytd_used_quantity',
                'tb_capex_monthly_budgets.ytd_used_budget',
            );

            $this->connection->select($select);
            $this->connection->from('tb_capex_monthly_budgets');
            $this->connection->where('tb_capex_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);
            $this->connection->where('tb_capex_monthly_budgets.product_id', $value['product_id']);
            $this->connection->where('tb_capex_monthly_budgets.month_number', $this->budget_month);
            $query_row = $this->connection->get();
            $row   = $query_row->unbuffered_row('array');
            $result[$key]['mtd_quantity'] = $row['ytd_quantity'] - $row['ytd_used_quantity'];
            $result[$key]['mtd_budget'] = $row['ytd_budget'] - $row['ytd_used_budget'];
        }
        return $result;
    }

    public function getAvailableVendors($category)
    {
        $this->db->select('tb_master_vendors.vendor');
        $this->db->from('tb_master_vendors');
        $this->db->join('tb_master_vendor_categories', 'tb_master_vendors.vendor = tb_master_vendor_categories.vendor');
        $this->db->where('UPPER(tb_master_vendors.status)', 'AVAILABLE');
        // $this->db->where('UPPER(tb_master_vendor_categories.category)', strtoupper($category));

        $query  = $this->db->get();
        $result = $query->result_array();
        $return = array();

        foreach ($result as $row) {
          $return[] = $row['vendor'];
        }

        return $return;
    }

    public function isDocumentNumberExists($pr_number)
    {
        $this->connection->where('pr_number', $pr_number);
        $query = $this->connection->get('tb_capex_purchase_requisitions');

        if ($query->num_rows() > 0)
          return true;

        return false;
    }

    public function isOrderNumberExists($order_number)
    {
        $this->connection->where('order_number', $order_number);
        $query = $this->connection->get('tb_capex_purchase_requisitions');

        if ($query->num_rows() > 0)
          return true;

        return false;
    }

    public function save()
    {
        $document_id          = (isset($_SESSION['capex']['id'])) ? $_SESSION['capex']['id'] : NULL;
        $document_edit        = (isset($_SESSION['capex']['edit'])) ? $_SESSION['capex']['edit'] : NULL;
        $order_number         = $_SESSION['capex']['order_number'];
        $cost_center_code     = $_SESSION['capex']['cost_center_code'];
        $cost_center_name     = $_SESSION['capex']['cost_center_name'];
        $annual_cost_center_id     = $_SESSION['capex']['annual_cost_center_id'];
        // if($this->model->isOrderNumberExists($order_number)==false){
        //     $order_number       = request_last_number();
        // }
        $pr_number            = $order_number.request_format_number($_SESSION['capex']['cost_center_code']);
        $pr_date              = date('Y-m-d');
        $required_date        = $_SESSION['capex']['required_date'];
        $deliver_to           = (empty($_SESSION['capex']['deliver_to'])) ? NULL : $_SESSION['capex']['deliver_to'];
        $suggested_supplier   = (empty($_SESSION['capex']['suggested_supplier'])) ? NULL : $_SESSION['capex']['suggested_supplier'];
        $created_by           = config_item('auth_person_name');
        $notes                = (empty($_SESSION['capex']['notes'])) ? NULL : $_SESSION['capex']['notes'];
        $unbudgeted           = 0;

        $this->connection->trans_begin();
        // $this->db->trans_begin();
        if ($document_id === NULL) {
            $this->connection->set('annual_cost_center_id', $annual_cost_center_id);
            $this->connection->set('product_category_id', NULL);
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
            $this->connection->insert('tb_capex_purchase_requisitions');

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
            $this->connection->update('tb_capex_purchase_requisitions');

            $this->connection->select('tb_capex_purchase_requisition_details.*');
            $this->connection->from('tb_capex_purchase_requisition_details');
            $this->connection->where('tb_capex_purchase_requisition_details.capex_purchase_requisition_id', $document_id);

            $query  = $this->connection->get();
            $result = $query->result_array();

            foreach ($result as $data) {
                $this->connection->from('tb_capex_monthly_budgets');
                $this->connection->where('id', $data['capex_monthly_budget_id']);

                $query        = $this->connection->get();
                $budget_monthly = $query->unbuffered_row('array');

                $year = $this->budget_year;
                $month = $budget_monthly['month_number'];
                $annual_cost_center_id = $budget_monthly['annual_cost_center_id'];
                $product_id = $budget_monthly['product_id'];

                for ($i = $month; $i < 13; $i++) {
                    $this->connection->set('ytd_used_quantity', 'ytd_used_quantity - ' . $data['quantity'], FALSE);
                    $this->connection->set('ytd_used_budget', 'ytd_used_budget - ' . $data['total'], FALSE);
                    $this->connection->where('tb_capex_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);
                    $this->connection->where('tb_capex_monthly_budgets.product_id', $product_id);
                        // $this->connection->where('year_number', $year);
                    $this->connection->where('tb_capex_monthly_budgets.month_number', $i);
                    $this->connection->update('tb_capex_monthly_budgets');
                }

                $this->connection->set('mtd_used_quantity', 'mtd_used_quantity - ' . $data['quantity'], FALSE);
                $this->connection->set('mtd_used_budget', 'mtd_used_budget +- ' . $data['total'], FALSE);
                $this->connection->where('id', $data['capex_monthly_budget_id']);
                $this->connection->update('tb_capex_monthly_budgets');
            }

            $this->connection->where('capex_purchase_requisition_id', $document_id);
            $this->connection->delete('tb_capex_purchase_requisition_details');

            $this->connection->where('pr_number', $pr_number);
            $this->connection->delete('tb_capex_used_budgets');


        }

        if(!empty($_SESSION['capex']['attachment'])){
            foreach ($_SESSION["capex"]["attachment"] as $key) {
                $this->connection->set('id_purchase', $document_id);
                $this->connection->set('file', $key);
                $this->connection->set('tipe', 'capex');
                $this->connection->insert('tb_attachment');
            }
        }

        // request from budget control
        foreach ($_SESSION['capex']['items'] as $key => $data) {
                // NEW GROUP

                // $data['group_name'] = '1. OFFICE SUPPLIES & CAMPUS EQUIPMENT';
                $this->connection->select('tb_product_groups.id');
                $this->connection->from('tb_product_groups');
                $this->connection->where('UPPER(tb_product_groups.group_name)', strtoupper($data['group']));

                $query  = $this->connection->get();

                if ($query->num_rows() == 0) {
                    $this->connection->set('product_category_id', $product_category_id);
                    $this->connection->set('group_name', $data['group']);
                    $this->connection->set('group_code', strtoupper($data['group']));
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

                if ($query->num_rows() == 0) {
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

                if ($query->num_rows() == 0) {
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

                if ($query->num_rows() == 0) {
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

                // GET BUDGET MONTHLY ID
                $this->connection->from('tb_capex_monthly_budgets');
                $this->connection->where('tb_capex_monthly_budgets.product_id', $product_id);
                $this->connection->where('tb_capex_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);
                $this->connection->where('tb_capex_monthly_budgets.month_number', $this->budget_month);
                // $this->connection->where('tb_capex_monthly_budgets.year_number', $this->budget_year);

                $query  = $this->connection->get();

                if ($query->num_rows() == 0) {
                    // // NEW BUDGET
                    // $this->connection->from('tb_capex_monthly_budgets');
                    // $this->connection->where('tb_capex_monthly_budgets.product_id', $product_id);
                    // $this->connection->where('tb_capex_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);
                    // $this->connection->where('tb_capex_monthly_budgets.month_number', $this->budget_month);
                    // $this->connection->where('tb_capex_monthly_budgets.year_number', $this->budget_year);

                    // $query  = $this->connection->get();

                    // if ($query->num_rows() == 0) {
                        $this->connection->set('annual_cost_center_id', $annual_cost_center_id);
                        $this->connection->set('product_id', $product_id);
                        $this->connection->set('month_number', $this->budget_month);
                        // $this->connection->set('year_number', $this->budget_year);
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
                        $this->connection->insert('tb_capex_monthly_budgets');

                        $capex_monthly_budget_id = $this->connection->insert_id();

                        // for ($m = 1; $m < $this->budget_month; $m++) {
                        //     // PREV BUDGET
                        //     $this->connection->set('annual_cost_center_id', $annual_cost_center_id);
                        //     $this->connection->set('product_id', $product_id);
                        //     $this->connection->set('month_number', $m);
                            //// $this->connection->set('year_number', $this->budget_year);
                        //     $this->connection->set('initial_quantity', floatval(0));
                        //     $this->connection->set('initial_budget', floatval(0));
                        //     $this->connection->set('mtd_quantity', floatval(0));
                        //     $this->connection->set('mtd_budget', floatval(0));
                        //     $this->connection->set('mtd_used_quantity', floatval(0));
                        //     $this->connection->set('mtd_used_budget', floatval(0));
                        //     $this->connection->set('mtd_used_quantity_import', floatval(0));
                        //     $this->connection->set('mtd_used_budget_import', floatval(0));
                        //     $this->connection->set('mtd_prev_month_quantity', floatval(0));
                        //     $this->connection->set('mtd_prev_month_budget', floatval(0));
                        //     $this->connection->set('mtd_prev_month_used_quantity', floatval(0));
                        //     $this->connection->set('mtd_prev_month_used_budget', floatval(0));
                        //     $this->connection->set('mtd_prev_month_used_quantity_import', floatval(0));
                        //     $this->connection->set('mtd_prev_month_used_budget_import', floatval(0));
                        //     $this->connection->set('ytd_quantity', floatval(0));
                        //     $this->connection->set('ytd_budget', floatval(0));
                        //     $this->connection->set('ytd_used_quantity', floatval(0));
                        //     $this->connection->set('ytd_used_budget', floatval(0));
                        //     $this->connection->set('ytd_used_quantity_import', floatval(0));
                        //     $this->connection->set('ytd_used_budget_import', floatval(0));
                        //     $this->connection->set('created_at', date('Y-m-d'));
                        //     $this->connection->set('created_by', config_item('auth_person_name'));
                        //     $this->connection->set('updated_at', date('Y-m-d'));
                        //     $this->connection->set('updated_by', config_item('auth_person_name'));
                        //     $this->connection->insert('tb_capex_monthly_budgets');
                        // }

                        // for ($am = 12; $am > $this->budget_month; $am--) {
                        //     // PREV BUDGET
                        //     $this->connection->set('annual_cost_center_id', $annual_cost_center_id);
                        //     $this->connection->set('product_id', $product_id);
                        //     $this->connection->set('month_number', $am);
                        // //     $this->connection->set('year_number', $this->budget_year);
                        //     $this->connection->set('initial_quantity', floatval(0));
                        //     $this->connection->set('initial_budget', floatval(0));
                        //     $this->connection->set('mtd_quantity', floatval(0));
                        //     $this->connection->set('mtd_budget', floatval(0));
                        //     $this->connection->set('mtd_used_quantity', floatval(0));
                        //     $this->connection->set('mtd_used_budget', floatval(0));
                        //     $this->connection->set('mtd_used_quantity_import', floatval(0));
                        //     $this->connection->set('mtd_used_budget_import', floatval(0));
                        //     $this->connection->set('mtd_prev_month_quantity', floatval(0));
                        //     $this->connection->set('mtd_prev_month_budget', floatval(0));
                        //     $this->connection->set('mtd_prev_month_used_quantity', floatval(0));
                        //     $this->connection->set('mtd_prev_month_used_budget', floatval(0));
                        //     $this->connection->set('mtd_prev_month_used_quantity_import', floatval(0));
                        //     $this->connection->set('mtd_prev_month_used_budget_import', floatval(0));
                        //     $this->connection->set('ytd_quantity', floatval(0));
                        //     $this->connection->set('ytd_budget', floatval(0));
                        //     $this->connection->set('ytd_used_quantity', floatval(0));
                        //     $this->connection->set('ytd_used_budget', floatval(0));
                        //     $this->connection->set('ytd_used_quantity_import', floatval(0));
                        //     $this->connection->set('ytd_used_budget_import', floatval(0));
                        //     $this->connection->set('created_at', date('Y-m-d'));
                        //     $this->connection->set('created_by', config_item('auth_person_name'));
                        //     $this->connection->set('updated_at', date('Y-m-d'));
                        //     $this->connection->set('updated_by', config_item('auth_person_name'));
                        //     $this->connection->insert('tb_capex_monthly_budgets');
                        // }
                    // } else {
                    //     $capex_monthly_budget    = $query->unbuffered_row();
                    //     $capex_monthly_budget_id = $capex_monthly_budget->id;
                    // }
                } else {
                    $capex_monthly_budget    = $query->unbuffered_row();
                    $capex_monthly_budget_id = $capex_monthly_budget->id;
                    // //jika ada budget status langsung approved
                    // $this->connection->set('status', 'approved');
                    // $this->connection->where('id', $document_id);
                    // $this->connection->update('tb_capex_purchase_requisitions');
                    // //jika ada budget status langsung approved

                    // old budget 
                    // $this->connection->where('id', $capex_monthly_budget_id);
                    // $temp = $this->connection->get('tb_capex_monthly_budgets')->row();
                    $year = $this->budget_year;
                    $month = $this->budget_month;

                    for ($i = $month; $i < 13; $i++) {
                        $this->connection->set('ytd_used_quantity', 'ytd_used_quantity + ' . $data['quantity'], FALSE);
                        $this->connection->set('ytd_used_budget', 'ytd_used_budget + ' . $data['total'], FALSE);
                        $this->connection->where('tb_capex_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);
                        $this->connection->where('tb_capex_monthly_budgets.product_id', $product_id);
                        // $this->connection->where('year_number', $year);
                        $this->connection->where('tb_capex_monthly_budgets.month_number', $i);
                        $this->connection->update('tb_capex_monthly_budgets');
                    }
                    // $this->connection->where('tb_capex_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);
                    // $this->connection->where('product_id', $temp->product_id);
                    // // $this->connection->where('year_number', $year);
                    // $this->connection->where('month_number', $month);

                    //insert data on used budget 
                    $this->connection->set('capex_monthly_budget_id', $capex_monthly_budget_id);
                    $this->connection->set('capex_purchase_requisition_id', $document_id);
                    $this->connection->set('pr_number', $pr_number);
                    $this->connection->set('cost_center', $cost_center_name);
                    $this->connection->set('year_number', $this->budget_year);
                    $this->connection->set('month_number', $this->budget_month);
                    $this->connection->set('product_name', $data['product_name']);
                    $this->connection->set('product_group', $data['group_name']);
                    $this->connection->set('product_code', $data['part_number']);
                    $this->connection->set('additional_info', $data['additional_info']);
                    $this->connection->set('used_budget', $data['total']);
                    $this->connection->set('used_quantity', $data['quantity']);
                    $this->connection->set('created_at', date('Y-m-d H:i:s'));
                    $this->connection->set('created_by', config_item('auth_person_name'));
                    $this->connection->set('part_number', $data['part_number']);
                    $this->connection->insert('tb_capex_used_budgets');

                    // $this->connection->set('ytd_used_quantity', 'ytd_used_quantity + ' . $data['quantity'], FALSE);
                    // $this->connection->set('ytd_used_budget', 'ytd_used_budget + ' . $data['total'], FALSE);
                    $this->connection->set('mtd_used_quantity', 'mtd_used_quantity + ' . $data['quantity'], FALSE);
                    $this->connection->set('mtd_used_budget', 'mtd_used_budget + ' . $data['total'], FALSE);
                    $this->connection->where('id', $capex_monthly_budget_id);
                    $this->connection->update('tb_capex_monthly_budgets');
                }

                $this->connection->set('capex_purchase_requisition_id', $document_id);
                $this->connection->set('capex_monthly_budget_id', $capex_monthly_budget_id);
                $this->connection->set('part_number', $data['part_number']);
                $this->connection->set('additional_info', $data['additional_info']);
                $this->connection->set('unit', $data['unit']);
                $this->connection->set('sort_order', floatval($key));
                $this->connection->set('quantity', floatval($data['quantity']));
                // $this->connection->set('sisa', floatval($data['quantity']));
                $this->connection->set('price', floatval($data['price']));
                $this->connection->set('total', floatval($data['total']));
                $this->connection->set('reference_ipc', $data['reference_ipc']);
                $this->connection->insert('tb_capex_purchase_requisition_details');
        }
        

        if (($this->connection->trans_status() === FALSE) && ($this->db->trans_status() === FALSE))
          return FALSE;

        $this->connection->trans_commit();
        $this->db->trans_commit();
        if($this->config->item('access_from')!='localhost'){
            $this->send_mail($document_id, 19);
        }

        return TRUE;
    }

    public function findPrlByPoeItemid($poe_item_id)
    {
        $prl_item_id = getPrlid($poe_item_id);

        $this->connection->select('tb_capex_purchase_requisition_details.capex_purchase_requisition_id');
        $this->connection->from('tb_capex_purchase_requisition_details');
        $this->connection->where('tb_capex_purchase_requisition_details.id', $prl_item_id);
        $query      = $this->connection->get();
        $poe_item   = $query->unbuffered_row('array');
        $id         = $poe_item['capex_purchase_requisition_id'];

        $this->connection->select('tb_capex_purchase_requisitions.*, tb_cost_centers.cost_center_name,tb_cost_centers.cost_center_code,tb_annual_cost_centers.cost_center_id');
        $this->connection->from('tb_capex_purchase_requisitions');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_capex_purchase_requisitions.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->where('tb_capex_purchase_requisitions.id', $id);

        $query    = $this->connection->get();
        $request  = $query->unbuffered_row('array');

        $select = array(
            'tb_capex_purchase_requisition_details.*',
            'tb_products.product_name',
            'tb_products.product_code as part_number',
            'tb_product_groups.group_name as group',
            'tb_capex_monthly_budgets.product_id',
            'tb_capex_monthly_budgets.ytd_quantity',
            'tb_capex_monthly_budgets.ytd_budget',
            'tb_capex_monthly_budgets.ytd_used_quantity',
            'tb_capex_monthly_budgets.ytd_used_budget',
        );

        $group_by = array(
            'tb_capex_purchase_requisition_details.id',
            'tb_products.product_name',
            'tb_products.product_code',
            'tb_capex_monthly_budgets.product_id',
            'tb_product_groups.group_name',
            'tb_capex_monthly_budgets.ytd_quantity',
            'tb_capex_monthly_budgets.ytd_budget',
            'tb_capex_monthly_budgets.ytd_used_quantity',
            'tb_capex_monthly_budgets.ytd_used_budget',
        );

        $this->connection->select($select);
        $this->connection->from('tb_capex_purchase_requisition_details');
        $this->connection->join('tb_capex_monthly_budgets', 'tb_capex_monthly_budgets.id = tb_capex_purchase_requisition_details.capex_monthly_budget_id');
        $this->connection->join('tb_products', 'tb_products.id = tb_capex_monthly_budgets.product_id');
        // $this->connection->join('tb_product_measurements', 'tb_product_measurements.id = tb_products.product_measurement_id');
        $this->connection->join('tb_product_groups', 'tb_product_groups.id = tb_products.product_group_id');
        $this->connection->where('tb_capex_purchase_requisition_details.capex_purchase_requisition_id', $id);
        $this->connection->group_by($group_by);

        $query = $this->connection->get();

        foreach ($query->result_array() as $key => $value){
            $request['items'][$key] = $value;
            $request['items'][$key]['balance_mtd_quantity']     = $value['ytd_quantity'] - $value['ytd_used_quantity'];
            $request['items'][$key]['balance_mtd_budget']       = $value['ytd_budget'] - $value['ytd_used_budget'];
            $request['items'][$key]['mtd_quantity']     = $value['quantity'] + $value['ytd_quantity'] - $value['ytd_used_quantity'];
            $request['items'][$key]['mtd_budget']       = $value['total'] + $value['ytd_budget'] - $value['ytd_used_budget'];

            $this->column_select = array(
                'SUM(tb_capex_monthly_budgets.mtd_quantity) as quantity',
                'SUM(tb_capex_monthly_budgets.mtd_budget) as budget',
                'SUM(tb_capex_monthly_budgets.mtd_used_quantity) as used_quantity',
                'SUM(tb_capex_monthly_budgets.mtd_used_budget) as used_budget',
                'tb_capex_monthly_budgets.product_id',
                'tb_capex_monthly_budgets.annual_cost_center_id',
            );

            $this->column_groupby = array(                
                'tb_capex_monthly_budgets.product_id',
                'tb_capex_monthly_budgets.annual_cost_center_id',
            );

            $this->connection->select($this->column_select);
            $this->connection->from('tb_capex_monthly_budgets');
            $this->connection->where('tb_capex_monthly_budgets.annual_cost_center_id', $request['annual_cost_center_id']);
            $this->connection->where('tb_capex_monthly_budgets.product_id', $value['product_id']);
            $this->connection->group_by($this->column_groupby);

            $query = $this->connection->get();
            $row   = $query->unbuffered_row('array');

            $request['items'][$key]['maximum_quantity'] = $value['quantity'] + $row['quantity'] - $row['used_quantity'];
            $request['items'][$key]['maximum_price']    =  $value['total'] + $row['budget'] - $row['used_budget'];
            $request['items'][$key]['balance_ytd_quantity']     = $row['quantity'] - $row['used_quantity'];
            $request['items'][$key]['balance_ytd_budget']       = $row['budget'] - $row['used_budget'];            
            $request['items'][$key]['history']          = $this->getHistory($request['annual_cost_center_id'],$value['product_id'],$request['order_number']);
        }

        return $request;
    }

    public function send_mail($doc_id, $level,$tipe=null)
    {
        $this->connection->from('tb_capex_purchase_requisitions');
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
            $message .= "<p>Capex Request Dibawah ini Sudah Terapproved. Silahkan Proses ke Capex Order Evaluation:</p>";
            $message .= "<ul>";
            $message .= "</ul>";
            $message .= "<p>No Capex Request : " . $row['pr_number'] . "</p>";
        }else{
            $message .= "<p>Berikut permintaan Persetujuan untuk Capex Request :</p>";
            $message .= "<ul>";
            $message .= "</ul>";
            $message .= "<p>No Capex Request : " . $row['pr_number'] . "</p>";
        }
        
        $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
        $message .= "<p>[ <a href='http://119.2.51.138:7323/expense_request/' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
        $message .= "<p>Thanks and regards</p>";
        $this->email->from($from_email, 'Material Resource Planning');
        $this->email->to($recipient);
        if($tipe!=null){
            $this->email->subject('Approval Capex Request No : ' . $row['pr_number']);
        }else{
            $this->email->subject('Permintaan Approval Capex Request No : ' . $row['pr_number']);
        }
        
        $this->email->message($message);

        //Send mail 
        if ($this->email->send())
          return true;
        else
          return $this->email->print_debugger();
    }

    public function send_mail_to_head_dept($doc_id)
    {
        $this->connection->select('tb_capex_purchase_requisitions.*');
        $this->connection->from('tb_capex_purchase_requisitions');
        $this->connection->where('tb_capex_purchase_requisitions.id',$doc_id);
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
        $message .= "<p>Berikut permintaan Persetujuan untuk Capex Request :</p>";
        $message .= "<ul>";
        $message .= "</ul>";
        $message .= "<p>No Capex Request : " . $row['pr_number'] . "</p>";
        $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
        $message .= "<p>[ <a href='http://119.2.51.138:7323/expense_request/' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
        $message .= "<p>Thanks and regards</p>";
        $this->email->from($from_email, 'Material Resource Planning');
        $this->email->to($recipient);
        $this->email->subject('Permintaan Approval Capex Request No : ' . $row['pr_number']);
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
        $this->connection->where('tipe', 'capex');
        return $this->connection->get('tb_attachment')->result();
    }

    public function listAttachment_2($id)
    {
        $this->connection->where('id_purchase', $id);
        $this->connection->where('tipe', 'capex');
        return $this->connection->get('tb_attachment')->result_array();
    }

    function add_attachment_to_db($id, $url)
    {
        $this->connection->trans_begin();

        $this->connection->set('id_purchase', $id);
        $this->connection->set('tipe', 'capex');
        $this->connection->set('file', $url);
        $this->connection->insert('tb_attachment');

        if ($this->connection->trans_status() === FALSE)
        return FALSE;

        $this->connection->trans_commit();
        return TRUE;
    }

    function multi_reject($id_purchase_order, $notes)
    {
        $this->connection->trans_begin();
        $x = 0;
        $return = 0;
        $rejected_note = '';
        foreach ($id_purchase_order as $id) {

            $this->connection->from('tb_capex_purchase_requisition_details');
            $this->connection->where('capex_purchase_requisition_id', $id);

            $query  = $this->connection->get();
            $items    = $query->result_array();

            foreach ($items as $row) {
                $this->connection->where('id', $row['capex_monthly_budget_id']);
                $query = $this->connection->get('tb_capex_monthly_budgets');
                $oldBudget =  $query->row();
                $month_number = $oldBudget->month_number;
                $annual_cost_center_id = $oldBudget->annual_cost_center_id;
                $product_id = $oldBudget->product_id;
                $this->connection->set('mtd_used_budget', 'mtd_used_budget - ' . $row['total'], FALSE);
                $this->connection->set('mtd_used_quantity', 'mtd_used_quantity - ' . $row['quantity'], FALSE);
                $this->connection->where('id', $row['capex_monthly_budget_id']);
                $this->connection->update('tb_capex_monthly_budgets');
                for ($i = $month_number; $i < 13; $i++) {                    
                    $this->connection->set('ytd_used_budget', 'ytd_used_budget - ' . $row['total'], FALSE);
                    $this->connection->set('ytd_used_quantity', 'ytd_used_quantity - ' . $row['quantity'], FALSE);
                    $this->connection->where('month_number', $i);
                    $this->connection->where('annual_cost_center_id', $annual_cost_center_id);
                    $this->connection->where('product_id', $product_id);
                    $this->connection->update('tb_capex_monthly_budgets');
                }
                $this->connection->where('capex_purchase_requisition_id', $row['id']);
                $this->connection->delete('tb_capex_used_budgets');
            }

            $this->connection->set('status', 'rejected');
            $this->connection->set('rejected_by', config_item('auth_person_name'));
            $this->connection->set('rejected_date', date('Y-m-d H:i:s'));
            $this->connection->set('rejected_notes', $notes[$x]);
            // $this->db->set('approved_by', config_item('auth_person_name'));
            $this->connection->where('id', $id);
            $check = $this->connection->update('tb_capex_purchase_requisitions');            

            if ($check) {
                $return++;
            }
            $x++;
            // $this->send_mail_approved($id,'rejected');
        }

        // if(($return == $x)&&($return > 0)){
        //   return true;
        // }else{
        //   return false;
        // }

        if ($this->connection->trans_status() === FALSE)
        return FALSE;

        $this->connection->trans_commit();
        return TRUE;
    }

    public function send_mail_approval($id, $ket, $by)
    {
        $item_message = '<tbody>';
        foreach ($id as $key) {
            $this->connection->from('tb_capex_purchase_requisitions');
            $this->connection->where('tb_capex_purchase_requisitions.id', $key);
            $query = $this->connection->get();
            $row = $query->unbuffered_row('array');
            
            $item_message .= "<tr>";
            $item_message .= "<td>" . $row['pr_number'] . "</td>";
            $item_message .= "<td>" . $row['notes'] . "</td>";
            $item_message .= "</tr>";

            $issued_by = $row['created_by'];

            $recipientList = $this->getNotifRecipient_approval($issued_by);
            $recipient = array("aidanurul99@rocketmail.com");
            foreach ($recipientList as $key) {
                array_push($recipient, $key->email);
            }
        }
        $item_message .= '</tbody>';

        $from_email = "bifa.acd@gmail.com";
        $to_email = "aidanurul99@rocketmail.com";
        if ($ket == 'approve') {
            $ket_level = 'Disetujui';
        } else {
            $ket_level = 'Ditolak';
        }

        //Load email library 
        $this->load->library('email');
        // $config = array();
        // $config['protocol'] = 'mail';
        // $config['smtp_host'] = 'smtp.live.com';
        // $config['smtp_user'] = 'bifa.acd@gmail.com';
        // $config['smtp_pass'] = 'b1f42019';
        // $config['smtp_port'] = 587;
        // $config['smtp_auth']        = true;
        // $config['mailtype']         = 'html';
        // $this->email->initialize($config);
        $this->email->set_newline("\r\n");
        $message = "<p>Hello</p>";
        $message .= "<p>Capex Request Berikut telah " . $ket_level . " oleh " . $by . "</p>";
        $message .= "<table>";
        $message .= "<thead>";
        $message .= "<tr>";
        $message .= "<th>No. Request.</th>";
        $message .= "<th>Notes</th>";
        $message .= "</tr>";
        $message .= "</thead>";
        $message .= $item_message;
        $message .= "</table>";
        // $message .= "<p>No Purchase Request : ".$row['document_number']."</p>";    
        $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
        $message .= "<p>[ <a href='http://119.2.51.138:7323/purchase_order/' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
        $message .= "<p>Thanks and regards</p>";
        $this->email->from($from_email, 'Material Resource Planning');
        $this->email->to($recipient);
        $this->email->subject('Notification Approval');
        $this->email->message($message);

        //Send mail 
        if ($this->email->send())
            return true;
        else
            return $this->email->print_debugger();
    }

    public function getNotifRecipient_approval($name)
    {
        $this->db->select('email');
        $this->db->from('tb_auth_users');
        $this->db->where('person_name', $name);
        return $this->db->get('')->result();
    }
}
