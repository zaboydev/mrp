<?php defined('BASEPATH') or exit('No direct script access allowed');

class Expense_Request_Model extends MY_Model
{
    protected $connection;
    protected $categories;
    protected $budget_year;
    protected $budget_month;

    public function __construct()
    {
        parent::__construct();

        $this->connection   = $this->load->database('budgetcontrol', TRUE);
        $this->categories   = $this->getCategories();
        $this->budget_year  = find_budget_setting('Active Year');
        $this->budget_month = find_budget_setting('Active Month');
    }

    public function getSelectedColumns()
    {
        return array(
            'tb_expense_purchase_requisitions.id'                               => NULL,
            'tb_expense_purchase_requisitions.pr_number'                        => 'Document Number',
            'tb_expense_purchase_requisitions.status'                           => 'Status',
            'tb_departments.department_name'                                    => 'Department Name',
            'tb_cost_centers.cost_center_name'                                  => 'Cost Center',
            'tb_expense_purchase_requisitions.pr_date'                          => 'Pr Date',
            'tb_expense_purchase_requisitions.required_date'                    => 'Required Date',
            // 'tb_accounts.account_name'                                           => 'Account',
            'SUM(tb_expense_purchase_requisition_details.total) as total_expense'  => 'Total',
            'tb_expense_purchase_requisitions.notes'                            => 'Notes',
        );
    }

    public function getGroupedColumns()
    {
        return array(
            'tb_expense_purchase_requisitions.id',
            'tb_expense_purchase_requisitions.pr_number',
            'tb_cost_centers.cost_center_name',
            'tb_expense_purchase_requisitions.pr_date',
            'tb_expense_purchase_requisitions.required_date',
            // 'tb_accounts.account_name',
            // 'tb_expense_purchase_requisition_details.total',
            'tb_expense_purchase_requisitions.notes',
            'tb_expense_purchase_requisitions.status',
            'tb_departments.department_name'
        );
    }

    public function getSearchableColumns()
    {
        return array(
            // 'tb_expense_purchase_requisitions.id',
            'tb_expense_purchase_requisitions.pr_number',
            'tb_cost_centers.cost_center_name',
            // 'tb_expense_purchase_requisitions.pr_date',
            // 'tb_expense_purchase_requisitions.required_date',
            // 'tb_accounts.account_name',
            // 'tb_expense_purchase_requisition_detail.total',
            'tb_expense_purchase_requisitions.notes',
            'tb_expense_purchase_requisitions.status',
            'tb_departments.department_name'
        );
    }

    public function getOrderableColumns()
    {
        return array(
            null,
            // 'tb_expense_purchase_requisitions.id',
            'tb_expense_purchase_requisitions.pr_number',
            'tb_expense_purchase_requisitions.status',
            'tb_departments.department_name',
            'tb_cost_centers.cost_center_name',
            'tb_expense_purchase_requisitions.pr_date',
            'tb_expense_purchase_requisitions.required_date',
            // 'tb_accounts.account_name',
            null,
            'tb_expense_purchase_requisitions.notes',
        );
    }

    private function searchIndex()
    {
        if (!empty($_POST['columns'][1]['search']['value'])){
            $search_required_date = $_POST['columns'][1]['search']['value'];
            $range_date  = explode(' ', $search_required_date);

            $this->connection->where('tb_expense_purchase_requisitions.required_date >= ', $range_date[0]);
            $this->connection->where('tb_expense_purchase_requisitions.required_date <= ', $range_date[1]);
        }

        if (!empty($_POST['columns'][2]['search']['value'])){
            $search_status = $_POST['columns'][2]['search']['value'];

            if($search_status!='all'){
                $this->connection->where('tb_expense_purchase_requisitions.status', $search_status);
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
        $this->connection->from('tb_expense_purchase_requisitions');
        $this->connection->join('tb_expense_purchase_requisition_details', 'tb_expense_purchase_requisition_details.expense_purchase_requisition_id = tb_expense_purchase_requisitions.id');
        $this->connection->join('tb_expense_monthly_budgets', 'tb_expense_monthly_budgets.id = tb_expense_purchase_requisition_details.expense_monthly_budget_id');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_expense_monthly_budgets.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->join('tb_departments', 'tb_departments.id = tb_cost_centers.department_id');
        // $this->connection->join('tb_accounts', 'tb_accounts.id = tb_expense_monthly_budgets.account_id');
        $this->connection->like('tb_expense_purchase_requisitions.pr_number', $this->budget_year);
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
        $this->connection->select(array_keys($this->getSelectedColumns()));
        $this->connection->from('tb_expense_purchase_requisitions');
        $this->connection->join('tb_expense_purchase_requisition_details', 'tb_expense_purchase_requisition_details.expense_purchase_requisition_id = tb_expense_purchase_requisitions.id');
        $this->connection->join('tb_expense_monthly_budgets', 'tb_expense_monthly_budgets.id = tb_expense_purchase_requisition_details.expense_monthly_budget_id');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_expense_monthly_budgets.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->join('tb_departments', 'tb_departments.id = tb_cost_centers.department_id');
        // $this->connection->join('tb_accounts', 'tb_accounts.id = tb_expense_monthly_budgets.account_id');
        $this->connection->like('tb_expense_purchase_requisitions.pr_number', $this->budget_year);
        $this->connection->group_by($this->getGroupedColumns());

        $this->searchIndex();

        $query = $this->connection->get();

        return $query->num_rows();
    }

    public function countIndex()
    {
        $this->connection->select(array_keys($this->getSelectedColumns()));
        $this->connection->from('tb_expense_purchase_requisitions');
        $this->connection->join('tb_expense_purchase_requisition_details', 'tb_expense_purchase_requisition_details.expense_purchase_requisition_id = tb_expense_purchase_requisitions.id');
        $this->connection->join('tb_expense_monthly_budgets', 'tb_expense_monthly_budgets.id = tb_expense_purchase_requisition_details.expense_monthly_budget_id');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_expense_monthly_budgets.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->join('tb_departments', 'tb_departments.id = tb_cost_centers.department_id');
        // $this->connection->join('tb_accounts', 'tb_accounts.id = tb_expense_monthly_budgets.account_id');
        $this->connection->like('tb_expense_purchase_requisitions.pr_number', $this->budget_year);
        $this->connection->group_by($this->getGroupedColumns());

        $query = $this->connection->get();

        return $query->num_rows();
    }

    public function findById($id)
    {
        $this->connection->select('tb_expense_purchase_requisitions.*, tb_cost_centers.cost_center_name');
        $this->connection->from('tb_expense_purchase_requisitions');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_expense_purchase_requisitions.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->where('tb_expense_purchase_requisitions.id', $id);

        $query    = $this->connection->get();
        $request  = $query->unbuffered_row('array');

        $select = array(
            'tb_expense_purchase_requisition_details.*',
            'tb_accounts.account_name',
            'tb_accounts.account_code',
            'tb_expense_monthly_budgets.account_id',
            'tb_expense_monthly_budgets.ytd_budget',
            'tb_expense_monthly_budgets.ytd_used_budget',
        );

        $group_by = array(
            'tb_expense_purchase_requisition_details.id',
            'tb_accounts.account_name',
            'tb_accounts.account_code',
            'tb_expense_monthly_budgets.account_id',
            'tb_expense_monthly_budgets.ytd_budget',
            'tb_expense_monthly_budgets.ytd_used_budget',
        );

        $this->connection->select($select);
        $this->connection->from('tb_expense_purchase_requisition_details');
        $this->connection->join('tb_expense_monthly_budgets', 'tb_expense_monthly_budgets.id = tb_expense_purchase_requisition_details.expense_monthly_budget_id');
        $this->connection->join('tb_accounts', 'tb_accounts.id = tb_expense_monthly_budgets.account_id');
        $this->connection->where('tb_expense_purchase_requisition_details.expense_purchase_requisition_id', $id);
        $this->connection->group_by($group_by);

        $query = $this->connection->get();

        foreach ($query->result_array() as $key => $value){
            $request['items'][$key] = $value;
            $request['items'][$key]['balance_mtd_budget']       = $value['ytd_budget'] - $value['ytd_used_budget'];

            $this->column_select = array(
                'SUM(tb_expense_monthly_budgets.mtd_budget) as budget',
                'SUM(tb_expense_monthly_budgets.mtd_used_budget) as used_budget',
                'tb_expense_monthly_budgets.account_id',
                'tb_expense_monthly_budgets.annual_cost_center_id',
            );

            $this->column_groupby = array(                
                'tb_expense_monthly_budgets.account_id',
                'tb_expense_monthly_budgets.annual_cost_center_id',
            );

            $this->connection->select($this->column_select);
            $this->connection->from('tb_expense_monthly_budgets');
            $this->connection->where('tb_expense_monthly_budgets.annual_cost_center_id', $request['annual_cost_center_id']);
            $this->connection->where('tb_expense_monthly_budgets.account_id', $value['account_id']);
            $this->connection->group_by($this->column_groupby);

            $query = $this->connection->get();
            $row   = $query->unbuffered_row('array');

            $request['items'][$key]['maximum_price']        =  $value['total'] + $row['budget'] - $row['used_budget'];
            $request['items'][$key]['balance_ytd_budget']   = $row['budget'] - $row['used_budget'];            
            $request['items'][$key]['history']              = $this->getHistory($request['annual_cost_center_id'],$value['account_id'],$request['order_number']);
        }

        return $request;
    }

    public function getHistory($annual_cost_center_id,$account_id,$order_number)
    {
        $select = array(
          'tb_expense_purchase_requisitions.pr_number',
          'tb_expense_purchase_requisitions.pr_date',
          'tb_expense_purchase_requisitions.created_by',
          'tb_expense_purchase_requisition_details.amount',
          'tb_expense_purchase_requisition_details.total',
        );
        $this->connection->select($select);
        $this->connection->from('tb_expense_purchase_requisition_details');
        $this->connection->join('tb_expense_purchase_requisitions', 'tb_expense_purchase_requisitions.id = tb_expense_purchase_requisition_details.expense_purchase_requisition_id');
        $this->connection->join('tb_expense_monthly_budgets', 'tb_expense_monthly_budgets.id = tb_expense_purchase_requisition_details.expense_monthly_budget_id');
        $this->connection->where('tb_expense_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);
        $this->connection->where('tb_expense_monthly_budgets.account_id', $account_id);
        $this->connection->where('tb_expense_purchase_requisitions.order_number <',$order_number);
        $query  = $this->connection->get();

        return $query->result_array();
    }

    public function approve($id,$notes)
    {
        $this->connection->trans_begin();

        $this->connection->select('tb_expense_purchase_requisitions.*');
        $this->connection->from('tb_expense_purchase_requisitions');
        $this->connection->where('tb_expense_purchase_requisitions.id',$id);
        $query    = $this->connection->get();
        $request  = $query->unbuffered_row('array');
        $approval_notes = $request['approval_notes'];
        $total = $this->countTotalExpense($id);

        if(config_item('auth_role')=='BUDGETCONTROL'){
            $this->connection->set('status','WAITING FOR HEAD DEPT');
            $this->connection->set('approved_date',date('Y-m-d H:i:s'));
            $this->connection->set('approved_by',config_item('auth_person_name'));
            if($notes!=''){
                $this->connection->set('approved_notes',$approval_notes.'Budgetcontrol : '.$notes);
            }            
            $this->connection->where('id',$id);
            $this->connection->update('tb_expense_purchase_requisitions');
        }
        if(config_item('as_head_department')=='yes'){
            $this->connection->set('status','WAITING FOR FINANCE REVIEW');
            $this->connection->set('head_approved_date',date('Y-m-d H:i:s'));
            $this->connection->set('head_approved_by',config_item('auth_person_name'));
            if($notes!=''){
                $this->connection->set('approved_notes',$approval_notes.'Head : '.$notes);
            }
            $this->connection->where('id',$id);
            $this->connection->update('tb_expense_purchase_requisitions');
        }

        if(config_item('auth_role')=='VP FINANCE'){
            $this->connection->set('status','WAITING FOR HOS REVIEW');
            $this->connection->set('finance_approved_date',date('Y-m-d H:i:s'));
            $this->connection->set('finance_approved_by',config_item('auth_person_name'));
            if($notes!=''){
                $this->connection->set('approved_notes',$approval_notes.'Finance : '.$notes);
            }            
            $this->connection->where('id',$id);
            $this->connection->update('tb_expense_purchase_requisitions');
        }

        if(config_item('auth_role')=='HEAD OF SCHOOL'){
            if($total>15000000){
                $this->connection->set('status','WAITING FOR COO REVIEW');
            }else{
                $this->connection->set('status','approved');
            }            
            $this->connection->set('hos_approved_date',date('Y-m-d H:i:s'));
            $this->connection->set('hos_approved_by',config_item('auth_person_name'));
            if($notes!=''){
                $this->connection->set('approved_notes',$approval_notes.'HOS : '.$notes);
            }            
            $this->connection->where('id',$id);
            $this->connection->update('tb_expense_purchase_requisitions');
        }

        if(config_item('auth_role')=='CHIEF OPERATION OFFICER'){
            $this->connection->set('status','approved');
            $this->connection->set('ceo_approved_date',date('Y-m-d H:i:s'));
            $this->connection->set('ceo_approved_by',config_item('auth_person_name'));
            if($notes!=''){
                $this->connection->set('approved_notes',$approval_notes.'COO : '.$notes);
            }            
            $this->connection->where('id',$id);
            $this->connection->update('tb_expense_purchase_requisitions');
        }

        // $this->connection->set('status','approved');
        // $this->connection->set('approved_date',date('Y-m-d H:i:s'));
        // $this->connection->set('approved_by',config_item('auth_person_name'));
        // $this->connection->set('approved_notes',$notes);
        // $this->connection->where('id',$id);
        // $this->connection->update('tb_expense_purchase_requisitions');

        if ($this->connection->trans_status() === FALSE)
            return FALSE;

        $this->connection->trans_commit();
        return TRUE;
    }

    public function searchBudget($annual_cost_center_id)
    {
        $query = "";
        $this->column_select = array(
            // 'SUM(tb_expense_monthly_budgets.mtd_quantity) as quantity',
            'SUM(tb_expense_monthly_budgets.mtd_budget) as budget',
            // 'SUM(tb_expense_monthly_budgets.mtd_used_quantity) as used_quantity',
            'SUM(tb_expense_monthly_budgets.mtd_used_budget) as used_budget',
            'tb_expense_monthly_budgets.account_id',
            'tb_accounts.account_name',
            'tb_accounts.account_code',
            // 'tb_product_groups.group_name',
            // 'tb_product_measurements.measurement_symbol',
            // 'tb_product_purchase_prices.current_price',
            'tb_expense_monthly_budgets.annual_cost_center_id',
        );

        $this->column_groupby = array(
            // 'SUM(tb_capex_monthly_budgets.mtd_quantity) as quantity',
            // 'SUM(tb_capex_monthly_budgets.mtd_budget) as budget',
            // 'SUM(tb_capex_monthly_budgets.mtd_used_quantity) as used_quantity',
            // 'SUM(tb_capex_monthly_budgets.mtd_used_budget) as used_budget',
            'tb_expense_monthly_budgets.account_id',
            'tb_accounts.account_name',
            'tb_accounts.account_code',
            'tb_expense_monthly_budgets.annual_cost_center_id',
        );

        $this->connection->select($this->column_select);
        $this->connection->from('tb_expense_monthly_budgets');
        $this->connection->join('tb_accounts', 'tb_accounts.id = tb_expense_monthly_budgets.account_id');
        // $this->connection->join('tb_product_purchase_prices', 'tb_product_purchase_prices.product_id = tb_products.id');
        // $this->connection->join('tb_product_measurements', 'tb_product_measurements.id = tb_products.product_measurement_id');
        // $this->connection->join('tb_product_groups', 'tb_product_groups.id = tb_products.product_group_id');
        // $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_product_groups.product_category_id');
        $this->connection->where('tb_expense_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);
        $this->connection->group_by($this->column_groupby);
        $this->connection->order_by('tb_accounts.account_code ASC, tb_accounts.account_name ASC');
          $query  = $this->connection->get();

        $result = $query->result_array();
        foreach ($result as $key => $value) {
          // $result[$key]['maximum_quantity'] = $value['quantity'] - $value['used_quantity'];
          $result[$key]['maximum_price'] = $value['budget'] - $value['used_budget'];
          // $result[$key]['maximum_quantity'] = $value['mtd_quantity'] - $value['mtd_used_quantity'];
          // $result[$key]['maximum_price'] = $value['mtd_budget'] - $value['mtd_used_budget'];
        }
        return $result;
    }

    public function isDocumentNumberExists($pr_number)
    {
        $this->connection->where('order_number', $pr_number);
        $query = $this->connection->get('tb_expense_purchase_requisitions');

        if ($query->num_rows() > 0)
          return true;

        return false;
    }

    public function isOrderNumberExists($order_number)
    {
        $this->connection->where('order_number', $order_number);
        $query = $this->connection->get('tb_expense_purchase_requisitions');

        if ($query->num_rows() > 0)
          return true;

        return false;
    }

    public function save()
    {
        $document_id          = (isset($_SESSION['expense']['id'])) ? $_SESSION['expense']['id'] : NULL;
        $document_edit        = (isset($_SESSION['expense']['edit'])) ? $_SESSION['expense']['edit'] : NULL;
        $order_number         = $_SESSION['expense']['pr_number'];
        $cost_center_code     = $_SESSION['expense']['cost_center_code'];
        $cost_center_name     = $_SESSION['expense']['cost_center_name'];
        $annual_cost_center_id     = $_SESSION['expense']['annual_cost_center_id'];
        if($this->model->isOrderNumberExists($order_number)==false){
            $order_number       = request_last_number();
        }
        $pr_number            = $order_number.request_format_number($cost_center_code);
        $pr_date              = date('Y-m-d');
        $required_date        = $_SESSION['expense']['required_date'];
        
        $created_by           = config_item('auth_person_name');
        $notes                = (empty($_SESSION['expense']['notes'])) ? NULL : $_SESSION['expense']['notes'];
        $unbudgeted           = 0;

        $this->connection->trans_begin();
        // $this->db->trans_begin();
        if ($document_id === NULL) {
            $this->connection->set('annual_cost_center_id', $annual_cost_center_id);
            // $this->connection->set('product_category_id', NULL);
            $this->connection->set('order_number', $order_number);
            $this->connection->set('pr_number', $pr_number);
            $this->connection->set('pr_date', $pr_date);
            $this->connection->set('required_date', $required_date);
            // $this->connection->set('suggested_supplier', $suggested_supplier);
            // $this->connection->set('deliver_to', $deliver_to);
            $this->connection->set('status', 'pending');
            $this->connection->set('notes', $notes);
            $this->connection->set('base', config_item('main_warehouse'));
            $this->connection->set('created_by', $created_by);
            $this->connection->set('updated_by', config_item('auth_person_name'));
            $this->connection->set('base', config_item('main_warehouse'));
            $this->connection->set('created_at', date('Y-m-d H:i:s'));
            $this->connection->set('updated_at', date('Y-m-d H:i:s'));
            $this->connection->insert('tb_expense_purchase_requisitions');

            $document_id = $this->connection->insert_id();
        } else {
            $this->connection->set('required_date', $required_date);
            // $this->connection->set('suggested_supplier', $suggested_supplier);
            // $this->connection->set('deliver_to', $deliver_to);
            $this->connection->set('status', 'pending');
            $this->connection->set('base', config_item('main_warehouse'));
            $this->connection->set('notes', $notes);
            $this->connection->set('updated_at', date('Y-m-d'));
            $this->connection->set('updated_by', config_item('auth_person_name'));
            $this->connection->where('id', $document_id);
            $this->connection->update('tb_expense_purchase_requisitions');

            $this->connection->where('expense_purchase_requisition_id', $document_id);
            $this->connection->delete('tb_expense_purchase_requisition_details');
        }
          // request from budget control
            foreach ($_SESSION['expense']['items'] as $key => $data) {
                

                // GET BUDGET MONTHLY ID
                $this->connection->from('tb_expense_monthly_budgets');
                $this->connection->where('tb_expense_monthly_budgets.account_id', $data['account_id']);
                $this->connection->where('tb_expense_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);
                $this->connection->where('tb_expense_monthly_budgets.month_number', $this->budget_month);
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
                        $this->connection->set('account_id', $data['account_id']);
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
                        $this->connection->insert('tb_expense_monthly_budgets');

                        $expense_monthly_budget_id = $this->connection->insert_id();

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
                    $expense_monthly_budget    = $query->unbuffered_row();
                    $expense_monthly_budget_id = $expense_monthly_budget->id;
                    // //jika ada budget status langsung approved
                    // $this->connection->set('status', 'approved');
                    // $this->connection->where('id', $document_id);
                    // $this->connection->update('tb_expense_purchase_requisitions');
                    // //jika ada budget status langsung approved

                    // old budget 
                    // $this->connection->where('id', $capex_monthly_budget_id);
                    // $temp = $this->connection->get('tb_capex_monthly_budgets')->row();
                    $year = $this->budget_year;
                    $month = $this->budget_month;

                    for ($i = $month; $i < 13; $i++) {
                        $this->connection->set('ytd_used_budget', 'ytd_used_budget + ' . $data['amount'], FALSE);
                        $this->connection->where('tb_expense_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);
                        $this->connection->where('tb_expense_monthly_budgets.account_id', $data['account_id']);
                        $this->connection->where('tb_expense_monthly_budgets.month_number', $i);
                        $this->connection->update('tb_expense_monthly_budgets');
                    }
                    // $this->connection->where('tb_capex_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);
                    // $this->connection->where('product_id', $temp->product_id);
                    // // $this->connection->where('year_number', $year);
                    // $this->connection->where('month_number', $month);

                    //insert data on used budget 
                    $this->connection->set('expense_monthly_budget_id', $expense_monthly_budget_id);
                    $this->connection->set('expense_purchase_requisition_id', $document_id);
                    $this->connection->set('pr_number', $pr_number);
                    $this->connection->set('cost_center', $cost_center_name);
                    $this->connection->set('year_number', $this->budget_year);
                    $this->connection->set('month_number', $this->budget_month);
                    $this->connection->set('account_name', $data['account_name']);
                    $this->connection->set('account_code', $data['account_code']);
                    $this->connection->set('used_budget', $data['amount']);
                    $this->connection->set('created_at', date('Y-m-d H:i:s'));
                    $this->connection->set('created_by', config_item('auth_person_name'));
                    // $this->connection->set('part_number', $data['part_number']);
                    $this->connection->insert('tb_expense_used_budgets');

                    
                    // $this->connection->set('ytd_used_quantity', 'ytd_used_quantity + ' . $data['quantity'], FALSE);
                    // $this->connection->set('ytd_used_budget', 'ytd_used_budget + ' . $data['amount'], FALSE);
                    $this->connection->set('mtd_used_budget', 'mtd_used_budget + ' . $data['amount'], FALSE);
                    $this->connection->where('id', $expense_monthly_budget_id);
                    $this->connection->update('tb_expense_monthly_budgets');
                }

                $this->connection->set('expense_purchase_requisition_id', $document_id);
                $this->connection->set('expense_monthly_budget_id', $expense_monthly_budget_id);
                $this->connection->set('sort_order', floatval($key));
                // $this->connection->set('sisa', floatval($data['amount']));
                $this->connection->set('amount', floatval($data['amount']));
                $this->connection->set('total', floatval($data['amount']));
                $this->connection->insert('tb_expense_purchase_requisition_details');
            }
        

        if (($this->connection->trans_status() === FALSE) && ($this->db->trans_status() === FALSE))
          return FALSE;

        $this->connection->trans_commit();
        $this->db->trans_commit();
        // if ($unbudgeted > 0) {
        //   $this->send_mail_finance($document_id);
        // } else {
        //   $this->send_mail($document_id);
        // }

        return TRUE;
    }

    public function countTotalExpense(){
        $this->connection->select('sum(total)');
        $this->connection->from('tb_expense_purchase_requisition_details');
        $this->connection->group_by('tb_expense_purchase_requisition_details.expense_purchase_requisition_id');
        $this->connection->where('tb_expense_purchase_requisition_details.expense_purchase_requisition_id', $prl_item_id);
        return $this->connection->get('')->row()->sum;
    }
}
