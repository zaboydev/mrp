<?php defined('BASEPATH') or exit('No direct script access allowed');

class Expense_Request_Model extends MY_Model
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
            'tb_expense_purchase_requisitions.id'                               => NULL,
            'tb_expense_purchase_requisitions.pr_number'                        => 'Document Number',
            'tb_expense_purchase_requisitions.status'                           => 'Status',
            'tb_cost_centers.cost_center_name'                                  => 'Cost Center',
            'tb_expense_purchase_requisitions.pr_date'                          => 'Pr Date',
            'tb_expense_purchase_requisitions.required_date'                    => 'Required Date',
            // 'tb_accounts.account_name'                                           => 'Account',
            'SUM(tb_expense_purchase_requisition_details.total) as total_expense'  => 'Total',
            'tb_expense_purchase_requisitions.notes'                            => 'Notes',
            'tb_expense_purchase_requisitions.approved_notes'                             => 'Notes',
            NULL                                                                => 'Attachment',
        );
        if (config_item('as_head_department')=='yes') {
            $return['tb_departments.department_name']  = 'Dept. Name';
        }
        return $return;
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
            'tb_departments.department_name',
            'tb_expense_purchase_requisitions.approved_notes'
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
            'tb_expense_purchase_requisitions.approved_notes'
            // 'tb_departments.department_name'
        );
    }

    public function getOrderableColumns()
    {
        $return = array(
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
            'tb_expense_purchase_requisitions.approved_notes',
            null
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

            $this->connection->where('tb_expense_purchase_requisitions.required_date >= ', $range_date[0]);
            $this->connection->where('tb_expense_purchase_requisitions.required_date <= ', $range_date[1]);
        }

        if (!empty($_POST['columns'][2]['search']['value'])){
            $search_status = $_POST['columns'][2]['search']['value'];

            if($search_status!='all'){
                if($search_status=='review'){
                    $status = [];
                    if(is_granted($this->data['modules']['expense_request'], 'approval')){
                        if(config_item('auth_role') == 'BUDGETCONTROL'){
                            $status[] = 'pending';
                        } 
                        if (config_item('auth_role') == 'ASSISTANT HOS') {
                            $status[] = 'WAITING FOR AHOS REVIEW';
                        }
                        if (config_item('auth_role') == 'FINANCE MANAGER') {
                            $status[] = 'WAITING FOR FINANCE REVIEW';
                        }
                        if (config_item('auth_role') == 'HEAD OF SCHOOL') {
                            $status[] = 'WAITING FOR HOS REVIEW';
                        }
                        if (config_item('auth_role') == 'VP FINANCE') {
                            $status[] = 'WAITING FOR VP FINANCE REVIEW';
                        }
                        if (config_item('auth_role') == 'CHIEF OF FINANCE') {
                            $status[] = 'WAITING FOR CFO REVIEW';
                        }
                        if (config_item('auth_role') == 'CHIEF OPERATION OFFICER') {  
                            $status[] = 'WAITING FOR COO REVIEW';
                        }
                        if (config_item('as_head_department')=='yes'){  
                            $status[] = 'WAITING FOR HEAD DEPT';
                        }
                        $this->connection->where_in('tb_expense_purchase_requisitions.status', $status);
                    }else{
                        $this->connection->like('tb_expense_purchase_requisitions.status', 'WAITING');
                    }
                }elseif($search_status=='review_approved'){
                    if(config_item('auth_role') == 'BUDGETCONTROL'){
                        $status = ['WAITING FOR HEAD DEPT','WAITING FOR FINANCE REVIEW','WAITING FOR HOS REVIEW','WAITING FOR COO REVIEW','WAITING FOR VP FINANCE REVIEW','WAITING FOR CFO REVIEW','approved'];    
                    } 
                    if (config_item('auth_role') == 'ASSISTANT HOS') {   
                        $status = ['WAITING FOR HEAD DEPT','WAITING FOR FINANCE REVIEW','WAITING FOR HOS REVIEW','WAITING FOR COO REVIEW','WAITING FOR VP FINANCE REVIEW','WAITING FOR CFO REVIEW','approved'];
                    }
                    if (config_item('auth_role') == 'FINANCE MANAGER') {
                        $status = ['WAITING FOR HOS REVIEW','WAITING FOR COO REVIEW','WAITING FOR VP FINANCE REVIEW','WAITING FOR CFO REVIEW','approved'];    
                    }
                    if (config_item('auth_role') == 'HEAD OF SCHOOL') {
                        $status = ['WAITING FOR COO REVIEW','WAITING FOR VP FINANCE REVIEW','WAITING FOR CFO REVIEW','approved'];
                    }
                    if (config_item('auth_role') == 'VP FINANCE') {
                        $status = ['WAITING FOR CFO REVIEW','approved'];
                    }
                    if (config_item('auth_role') == 'CHIEF OF FINANCE') {
                        $status = ['approved'];
                    }
                    if (config_item('auth_role') == 'CHIEF OPERATION OFFICER') {
                        $status = ['WAITING FOR VP FINANCE REVIEW','WAITING FOR CFO REVIEW','approved'];
                    }
                    if (config_item('as_head_department')=='yes'){
                        $status = ['WAITING FOR FINANCE REVIEW','WAITING FOR HOS REVIEW','WAITING FOR COO REVIEW','WAITING FOR VP FINANCE REVIEW','WAITING FOR CFO REVIEW','approved'];
                    }
                    $this->connection->where_in('tb_expense_purchase_requisitions.status', $status);
                }else{
                    $this->connection->where('tb_expense_purchase_requisitions.status', $search_status);
                }                
            }            
        }else{    
            $status = [];
            if(is_granted($this->data['modules']['expense_request'], 'approval')){
                if(config_item('auth_role') == 'BUDGETCONTROL'){
                    $status[] = 'pending';
                } 
                if (config_item('auth_role') == 'ASSISTANT HOS') {
                    $status[] = 'WAITING FOR AHOS REVIEW';
                }
                if (config_item('auth_role') == 'FINANCE MANAGER') {
                    $status[] = 'WAITING FOR FINANCE REVIEW';
                }
                if (config_item('auth_role') == 'HEAD OF SCHOOL') {
                    $status[] = 'WAITING FOR HOS REVIEW';
                }
                if (config_item('auth_role') == 'VP FINANCE') {
                    $status[] = 'WAITING FOR VP FINANCE REVIEW';
                }
                if (config_item('auth_role') == 'CHIEF OF FINANCE') {
                    $status[] = 'WAITING FOR CFO REVIEW';
                }
                if (config_item('auth_role') == 'CHIEF OPERATION OFFICER') {  
                    $status[] = 'WAITING FOR COO REVIEW';
                }
                if (config_item('as_head_department')=='yes'){  
                    $status[] = 'WAITING FOR HEAD DEPT';
                }
                $this->connection->where_in('tb_expense_purchase_requisitions.status', $status);
            }else{
                if(config_item('auth_role') == 'PIC PROCUREMENT'||config_item('auth_role') == 'AP STAFF'){
                    $this->connection->where('tb_expense_purchase_requisitions.status', 'approved');
                }
            }   
        }

        if (!empty($_POST['columns'][3]['search']['value'])){
            $search_cost_center = $_POST['columns'][3]['search']['value'];
            if($search_cost_center!='all'){
                $this->connection->where('tb_cost_centers.cost_center_name', $search_cost_center);
            }            
        }

        // if (!empty($_POST['columns'][4]['search']['value'])){
        //     $search_category = $_POST['columns'][4]['search']['value'];

        //     $this->connection->where('UPPER(tb_product_categories.category_name)', strtoupper($search_category));
        // }

        $i = 0;

        foreach ($this->getSearchableColumns() as $item){
            if ($_POST['search']['value']){
                    $this->connection->like('UPPER('.$item.')', $term);
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
        if(config_item('auth_role') == 'PIC STAFF' || config_item('auth_role') == 'SUPER ADMIN'){
            $this->connection->where_in('tb_cost_centers.cost_center_name', config_item('auth_annual_cost_centers_name'));
        }
        $this->connection->where_in('tb_expense_purchase_requisitions.base', config_item('auth_warehouses'));
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
        if(config_item('auth_role') == 'PIC STAFF' || config_item('auth_role') == 'SUPER ADMIN'){
            $this->connection->where_in('tb_cost_centers.cost_center_name', config_item('auth_annual_cost_centers_name'));
        }
        $this->connection->where_in('tb_expense_purchase_requisitions.base', config_item('auth_warehouses'));
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
        if(config_item('auth_role') == 'PIC STAFF' || config_item('auth_role') == 'SUPER ADMIN'){
            $this->connection->where_in('tb_cost_centers.cost_center_name', config_item('auth_annual_cost_centers_name'));
        }
        $this->connection->where_in('tb_expense_purchase_requisitions.base', config_item('auth_warehouses'));
        $this->connection->group_by($this->getGroupedColumns());

        $query = $this->connection->get();

        return $query->num_rows();
    }

    public function findById($id)
    {
        $this->connection->select('tb_expense_purchase_requisitions.*, tb_cost_centers.cost_center_name, tb_cost_centers.cost_center_code');
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
        $with_po = $request['with_po'];
        $total = $this->countTotalExpense($id);
        $department = getDepartmentByAnnualCostCenterId($request['annual_cost_center_id']);
        $cost_center = getCostCenterByAnnualCostCenterId($request['annual_cost_center_id']);

        if(config_item('auth_role')=='BUDGETCONTROL' && $request['status']=='pending'){
            if($request['base']=='BANYUWANGI'){
                $this->connection->set('status','WAITING FOR AHOS REVIEW');
                $level = 22;
            }else{
                $this->connection->set('status','WAITING FOR HEAD DEPT');
                $level = -1;
            }
            
            $this->connection->set('approved_date',date('Y-m-d H:i:s'));
            $this->connection->set('approved_by',config_item('auth_person_name'));
            if($notes!=''){
                $this->connection->set('approved_notes',$approval_notes.'Budgetcontrol : '.$notes);
            }            
            $this->connection->where('id',$id);
            $this->connection->update('tb_expense_purchase_requisitions');
            
        }

        if(config_item('auth_role')=='ASSISTANT HOS' && $request['status']=='WAITING FOR AHOS REVIEW'){
            $this->connection->set('status','WAITING FOR HEAD DEPT');
            $this->connection->set('ahos_approved_date',date('Y-m-d H:i:s'));
            $this->connection->set('ahos_approved_by',config_item('auth_person_name'));
            if($notes!=''){
                $this->connection->set('approved_notes',$approval_notes.'AHOS : '.$notes);
            }            
            $this->connection->where('id',$id);
            $this->connection->update('tb_expense_purchase_requisitions');
            $level = -1;
            
        }
        if(config_item('as_head_department')=='yes' && config_item('head_department')==$department['department_name'] && $request['status']=='WAITING FOR HEAD DEPT'){
            if($with_po=='t'){
                $this->connection->set('status','approved');
                $this->connection->set('head_approved_date',date('Y-m-d H:i:s'));
                $this->connection->set('head_approved_by',config_item('auth_person_name'));
                if($notes!=''){
                    $this->connection->set('approved_notes',$approval_notes.'Head : '.$notes);
                }
                $this->connection->where('id',$id);
                $this->connection->update('tb_expense_purchase_requisitions');
                $level = 8;
            }else{
                $this->connection->set('status','WAITING FOR FINANCE REVIEW');
                $this->connection->set('head_approved_date',date('Y-m-d H:i:s'));
                $this->connection->set('head_approved_by',config_item('auth_person_name'));
                if($notes!=''){
                    $this->connection->set('approved_notes',$approval_notes.'Head : '.$notes);
                }
                $this->connection->where('id',$id);
                $this->connection->update('tb_expense_purchase_requisitions');
                $level = 14;
            }
            
        }

        if(config_item('auth_role')=='FINANCE MANAGER' && $request['status']=='WAITING FOR FINANCE REVIEW'){
            if($cost_center['id']==$this->config->item('head_office_cost_center_id')){
                $this->connection->set('status','WAITING FOR VP FINANCE REVIEW');                
                $level = 3;
            }else{
                $this->connection->set('status','WAITING FOR HOS REVIEW');
                $level = 10;
            }
            $this->connection->set('finance_approved_date',date('Y-m-d H:i:s'));
            $this->connection->set('finance_approved_by',config_item('auth_person_name'));
            if($notes!=''){
                $this->connection->set('approved_notes',$approval_notes.'Finance : '.$notes);
            }            
            $this->connection->where('id',$id);
            $this->connection->update('tb_expense_purchase_requisitions');
            
        }

        if(config_item('auth_role')=='HEAD OF SCHOOL' && $request['status']=='WAITING FOR HOS REVIEW'){
            if($total>15000000){
                $this->connection->set('status','WAITING FOR COO REVIEW');
                $level = 16;
            }else{
                $this->connection->set('status','approved');
                $level = 0;
            }            
            $this->connection->set('hos_approved_date',date('Y-m-d H:i:s'));
            $this->connection->set('hos_approved_by',config_item('auth_person_name'));
            if($notes!=''){
                $this->connection->set('approved_notes',$approval_notes.'HOS : '.$notes);
            }            
            $this->connection->where('id',$id);
            $this->connection->update('tb_expense_purchase_requisitions');
        }

        if(config_item('auth_role')=='CHIEF OPERATION OFFICER' && $request['status']=='WAITING FOR COO REVIEW'){
            $this->connection->set('status','approved');
            $this->connection->set('ceo_approved_date',date('Y-m-d H:i:s'));
            $this->connection->set('ceo_approved_by',config_item('auth_person_name'));
            if($notes!=''){
                $this->connection->set('approved_notes',$approval_notes.'COO : '.$notes);
            }            
            $this->connection->where('id',$id);
            $this->connection->update('tb_expense_purchase_requisitions');
            $level = 0;
        }

        if(config_item('auth_role')=='VP FINANCE' && $request['status']=='WAITING FOR VP FINANCE REVIEW'){
            if($total>15000000){
                $this->connection->set('status','WAITING FOR CFO REVIEW');
                $level = 11;
            }else{
                $this->connection->set('status','approved');
                $level = 0;
            }            
            $this->connection->set('hos_approved_date',date('Y-m-d H:i:s'));
            $this->connection->set('hos_approved_by',config_item('auth_person_name'));
            if($notes!=''){
                $this->connection->set('approved_notes',$approval_notes.'VP FINANCE : '.$notes);
            }            
            $this->connection->where('id',$id);
            $this->connection->update('tb_expense_purchase_requisitions');
        }

        if(config_item('auth_role')=='CHIEF OF FINANCE' && $request['status']=='WAITING FOR CFO REVIEW'){
            $this->connection->set('status','approved');
            $this->connection->set('ceo_approved_date',date('Y-m-d H:i:s'));
            $this->connection->set('ceo_approved_by',config_item('auth_person_name'));
            if($notes!=''){
                $this->connection->set('approved_notes',$approval_notes.'CFO : '.$notes);
            }            
            $this->connection->where('id',$id);
            $this->connection->update('tb_expense_purchase_requisitions');
            $level = 0;
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

        if($level>0){
            if($this->config->item('access_from')!='localhost'){
                $this->send_mail($id, $level);
            }
        }
        if($level<0){
            if($this->config->item('access_from')!='localhost'){
                $this->send_mail_to_head_dept($id);
            }
        }
        return TRUE;
    }

    public function searchBudget($annual_cost_center_id,$with_po = NULL)
    {
        $item_no_po = $this->items_no_po();
        $query = "";
        $this->column_select = array(
            'SUM(tb_expense_monthly_budgets.mtd_budget) as budget',
            'SUM(tb_expense_monthly_budgets.mtd_used_budget) as used_budget',
            'tb_expense_monthly_budgets.account_id',
            'tb_accounts.account_name',
            'tb_accounts.account_code',
            'tb_expense_monthly_budgets.annual_cost_center_id',
        );

        $this->column_groupby = array(
            'tb_expense_monthly_budgets.account_id',
            'tb_accounts.account_name',
            'tb_accounts.account_code',
            'tb_expense_monthly_budgets.annual_cost_center_id',
        );

        $this->connection->select($this->column_select);
        $this->connection->from('tb_expense_monthly_budgets');
        $this->connection->join('tb_accounts', 'tb_accounts.id = tb_expense_monthly_budgets.account_id');
        $this->connection->where('tb_expense_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);
        if ($with_po !== NULL || !empty($with_po)) {
            if($with_po=='f'){
                $this->connection->where_in('tb_accounts.id',$item_no_po);
            }else{
                $this->connection->where_not_in('tb_accounts.id',$item_no_po);
            }            
        }
        $this->connection->group_by($this->column_groupby);
        $this->connection->order_by('tb_accounts.account_code ASC, tb_accounts.account_name ASC');
          $query  = $this->connection->get();

        $result = $query->result_array();
        foreach ($result as $key => $value) {
            $result[$key]['maximum_price'] = $value['budget'] - $value['used_budget'];
            $select = array(
                'tb_expense_monthly_budgets.ytd_budget',
                'tb_expense_monthly_budgets.ytd_used_budget',
                'tb_expense_monthly_budgets.id',
            );

            $this->connection->select($select);
            $this->connection->from('tb_expense_monthly_budgets');
            $this->connection->where('tb_expense_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);
            $this->connection->where('tb_expense_monthly_budgets.account_id', $value['account_id']);
            $this->connection->where('tb_expense_monthly_budgets.month_number', $this->budget_month);
            $query_row = $this->connection->get();
            $row   = $query_row->unbuffered_row('array');
            $result[$key]['mtd_budget']                 = $row['ytd_budget'] - $row['ytd_used_budget'];
            $result[$key]['expense_monthly_budget_id']  = $row['id'];
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
        $with_po              = $_SESSION['expense']['with_po'];
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
            $this->connection->set('created_by', $created_by);
            $this->connection->set('updated_by', config_item('auth_person_name'));
            $this->connection->set('created_at', date('Y-m-d H:i:s'));
            $this->connection->set('updated_at', date('Y-m-d H:i:s'));
            $this->connection->set('with_po', $with_po);
            $this->connection->set('base', config_item('auth_warehouse'));
            $this->connection->insert('tb_expense_purchase_requisitions');

            $document_id = $this->connection->insert_id();
        } else {
            $this->connection->set('required_date', $required_date);
            // $this->connection->set('suggested_supplier', $suggested_supplier);
            // $this->connection->set('deliver_to', $deliver_to);
            $this->connection->set('status', 'pending');
            $this->connection->set('base', config_item('auth_warehouse'));
            $this->connection->set('notes', $notes);
            $this->connection->set('updated_at', date('Y-m-d'));
            $this->connection->set('updated_by', config_item('auth_person_name'));
            $this->connection->set('with_po', $with_po);
            $this->connection->where('id', $document_id);
            $this->connection->update('tb_expense_purchase_requisitions');

            $this->connection->select('tb_expense_purchase_requisition_details.*');
            $this->connection->from('tb_expense_purchase_requisition_details');
            $this->connection->where('tb_expense_purchase_requisition_details.expense_purchase_requisition_id', $document_id);

            $query  = $this->connection->get();
            $result = $query->result_array();

            foreach ($result as $data) {
                $this->connection->from('tb_expense_monthly_budgets');
                $this->connection->where('id', $data['expense_monthly_budget_id']);

                $query        = $this->connection->get();
                $budget_monthly = $query->unbuffered_row('array');

                $year = $this->budget_year;
                $month = $budget_monthly['month_number'];
                $annual_cost_center_id = $budget_monthly['annual_cost_center_id'];
                $account_id = $budget_monthly['account_id'];

                for ($i = $month; $i < 13; $i++) {
                    // $this->connection->set('ytd_used_quantity', 'ytd_used_quantity - ' . $data['quantity'], FALSE);
                    $this->connection->set('ytd_used_budget', 'ytd_used_budget - ' . $data['total'], FALSE);
                    $this->connection->where('tb_expense_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);
                    $this->connection->where('tb_expense_monthly_budgets.account_id', $account_id);
                        // $this->connection->where('year_number', $year);
                    $this->connection->where('tb_expense_monthly_budgets.month_number', $i);
                    $this->connection->update('tb_expense_monthly_budgets');
                }

                // $this->connection->set('mtd_used_quantity', 'mtd_used_quantity - ' . $data['quantity'], FALSE);
                $this->connection->set('mtd_used_budget', 'mtd_used_budget +- ' . $data['total'], FALSE);
                $this->connection->where('id', $data['expense_monthly_budget_id']);
                $this->connection->update('tb_expense_monthly_budgets');
            }

            $this->connection->where('expense_purchase_requisition_id', $document_id);
            $this->connection->delete('tb_expense_purchase_requisition_details');

            $this->connection->where('pr_number', $pr_number);
            $this->connection->delete('tb_expense_used_budgets');
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
                $this->connection->set('reference_ipc', $data['reference_ipc']);
                $this->connection->insert('tb_expense_purchase_requisition_details');
            }

        if(!empty($_SESSION['expense']['attachment'])){
            foreach ($_SESSION["expense"]["attachment"] as $key) {
                $this->connection->set('id_purchase', $document_id);
                $this->connection->set('file', $key);
                $this->connection->set('tipe', 'expense');
                $this->connection->insert('tb_attachment');
            }
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

    public function countTotalExpense(){
        $this->connection->select('sum(total)');
        $this->connection->from('tb_expense_purchase_requisition_details');
        $this->connection->group_by('tb_expense_purchase_requisition_details.expense_purchase_requisition_id');
        $this->connection->where('tb_expense_purchase_requisition_details.expense_purchase_requisition_id', $prl_item_id);
        return $this->connection->get('')->row()->sum;
    }

    public function findPrlByPoeItemid($poe_item_id)
    {
        $prl_item_id = getPrlid($poe_item_id);//perbaikan di app helper

        $this->connection->select('tb_expense_purchase_requisition_details.expense_purchase_requisition_id');
        $this->connection->from('tb_expense_purchase_requisition_details');
        $this->connection->where('tb_expense_purchase_requisition_details.id', $prl_item_id);
        $query      = $this->connection->get();
        $poe_item   = $query->unbuffered_row('array');
        $id         = $poe_item['expense_purchase_requisition_id'];

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

    function items_no_po()
    {
        $expense_item_no_po = array();

        $this->connection->select('account_id');
        $this->connection->from('tb_expense_item_without_po');

        $query  = $this->connection->get();

        foreach ($query->result_array() as $key => $value) {
        $expense_item_no_po[] = $value['account_id'];
        }

        return $expense_item_no_po;
    }

    public function send_mail($doc_id, $level,$tipe=null)
    {
        $this->connection->from('tb_expense_purchase_requisitions');
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
        if($id==8){
            $message .= "<p>Expense Request Dibawah ini Sudah Terapproved. Silahkan Proses ke Expense Order Evaluation:</p>";
            $message .= "<ul>";
            $message .= "</ul>";
            $message .= "<p>No Expense Request : " . $row['pr_number'] . "</p>";
        }else{
            $message .= "<p>Berikut permintaan Persetujuan untuk Expense Request :</p>";
            $message .= "<ul>";
            $message .= "</ul>";
            $message .= "<p>No Expense Request : " . $row['pr_number'] . "</p>";
        }
        $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
        $message .= "<p>[ <a href='http://119.2.51.138:7323/expense_request/' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
        $message .= "<p>Thanks and regards</p>";
        $this->email->from($from_email, 'Material Resource Planning');
        $this->email->to($recipient);
        $this->email->subject('Permintaan Approval Expense Request No : ' . $row['pr_number']);
        $this->email->message($message);

        //Send mail 
        if ($this->email->send())
          return true;
        else
          return $this->email->print_debugger();
    }

    public function send_mail_to_head_dept($doc_id)
    {
        $this->connection->select('tb_expense_purchase_requisitions.*');
        $this->connection->from('tb_expense_purchase_requisitions');
        $this->connection->where('tb_expense_purchase_requisitions.id',$doc_id);
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
        $message .= "<p>Berikut permintaan Persetujuan untuk Expense Request :</p>";
        $message .= "<ul>";
        $message .= "</ul>";
        $message .= "<p>No Expense Request : " . $row['pr_number'] . "</p>";
        $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
        $message .= "<p>[ <a href='http://119.2.51.138:7323/expense_request/' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
        $message .= "<p>Thanks and regards</p>";
        $this->email->from($from_email, 'Material Resource Planning');
        $this->email->to($recipient);
        $this->email->subject('Permintaan Approval Expense Request No : ' . $row['pr_number']);
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
        $this->connection->where('tipe', 'expense');
        return $this->connection->get('tb_attachment')->result();
    }

    public function listAttachment_2($id)
    {
        $this->connection->where('id_purchase', $id);
        $this->connection->where('tipe', 'expense');
        return $this->connection->get('tb_attachment')->result_array();
    }

    function add_attachment_to_db($id, $url)
    {
        $this->connection->trans_begin();

        $this->connection->set('id_purchase', $id);
        $this->connection->set('tipe', 'expense');
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

            $this->connection->from('tb_expense_purchase_requisition_details');
            $this->connection->where('expense_purchase_requisition_id', $id);

            $query  = $this->connection->get();
            $items    = $query->result_array();

            foreach ($items as $row) {
                $this->connection->where('id', $row['expense_monthly_budget_id']);
                $query = $this->connection->get('tb_expense_monthly_budgets');
                $oldBudget =  $query->row();
                $month_number = $oldBudget->month_number;
                $account_id = $oldBudget->account_id;
                $annual_cost_center_id = $oldBudget->annual_cost_center_id;
                $this->connection->set('mtd_used_budget', 'mtd_used_budget - ' . $row['total'], FALSE);
                $this->connection->where('id', $row['expense_monthly_budget_id']);
                $this->connection->update('tb_expense_monthly_budgets');
                for ($i = $month_number; $i < 13; $i++) {                    
                    $this->connection->set('ytd_used_budget', 'ytd_used_budget - ' . $row['total'], FALSE);
                    $this->connection->where('month_number', $i);
                    $this->connection->where('account_id', $account_id);
                    $this->connection->where('annual_cost_center_id', $annual_cost_center_id);
                    $this->connection->update('tb_expense_monthly_budgets');
                }
                $this->connection->where('expense_purchase_requisition_id', $row['id']);
                $this->connection->delete('tb_expense_used_budgets');
            }

            $this->connection->set('status', 'rejected');
            $this->connection->set('rejected_by', config_item('auth_person_name'));
            $this->connection->set('rejected_date', date('Y-m-d H:i:s'));
            $this->connection->set('rejected_notes', $notes[$x]);
            // $this->db->set('approved_by', config_item('auth_person_name'));
            $this->connection->where('id', $id);
            $check = $this->connection->update('tb_expense_purchase_requisitions');            

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

    public function send_mail_approval($id, $ket, $by, $notes)
    {
		$item_message = '<tbody>';
		$x = 0;
        foreach ($id as $key) {
            $this->connection->from('tb_expense_purchase_requisitions');
            $this->connection->where('tb_expense_purchase_requisitions.id', $key);
            $query = $this->connection->get();
            $row = $query->unbuffered_row('array');
            
            $item_message .= "<tr>";
            $item_message .= "<td>" . $row['pr_number'] . "</td>";
			$item_message .= "<td>" . $row['notes'] . "</td>";
			if($ket!='approve'){
				$item_message .= "<td>" . $notes[$x] . "</td>";
			}
            $item_message .= "</tr>";

            $issued_by = $row['created_by'];

            $recipientList = $this->getNotifRecipient_approval($issued_by);
            $recipient = array();
            foreach ($recipientList as $key) {
                array_push($recipient, $key->email);
			}
			$x++;
        }
        $item_message .= '</tbody>';

        $from_email = "bifa.acd@gmail.com";
        $to_email = "aidanurul99@rocketmail.com";
        if ($ket == 'approve') {
			$ket_level = 'Disetujui';
			$tindakan = 'Approval';
        } else {
			$ket_level = 'Ditolak';
			$tindakan = 'Rejection';
        }

        //Load email library 
        $this->load->library('email');
        
        $this->email->set_newline("\r\n");
        $message = "<p>Hello</p>";
        $message .= "<p>Expense Request Berikut telah " . $ket_level . " oleh " . $by . "</p>";
        $message .= "<table>";
        $message .= "<thead>";
        $message .= "<tr>";
        $message .= "<th>No. Request.</th>";
		$message .= "<th>Notes</th>";
		if($ket!='approve'){
			$message .= "<th>".ucwords($ket)." Notes</th>";
		}        
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
        $this->email->subject('Notification '.$tindakan);
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

    function multi_closing($id_purchase_order, $notes)
    {
        $this->connection->trans_begin();
        $this->db->trans_begin();
        $x = 0;
        $return = 0;
        $rejected_note = '';
        foreach ($id_purchase_order as $id) {
            $this->connection->from('tb_expense_purchase_requisition_details');
            $this->connection->where('tb_expense_purchase_requisition_details.expense_purchase_requisition_id', $id);
            $query = $this->connection->get();

            foreach ($query->result_array() as $key => $value){
                $this->connection->set('process_amount', $value['total']);
                $this->connection->where('id', $value['id']);
                $this->connection->update('tb_expense_purchase_requisition_details');

                if(!isItemRequestAlreadyInClosures($value['id'],'EXPENSE')){
                    $this->db->set('closing_by', config_item('auth_person_name'));
                    $this->db->set('notes', $notes[$x]);
                    $this->db->set('tipe', 'EXPENSE');
                    $this->db->set('purchase_request_detail_id', $value['id']);
                    $this->db->insert('tb_purchase_request_closures');
                }                
            }        
            
            $this->connection->set('status','close');
            $this->connection->set('closing_date',date('Y-m-d H:i:s'));
            $this->connection->set('closing_by',config_item('auth_person_name'));
            $this->connection->set('closing_notes',$notes[$x]);
            $this->connection->where('id',$id);
            $check = $this->connection->update('tb_expense_purchase_requisitions');

            if ($check) {
                $return++;
            }
            $x++;
        }

        // if(($return == $x)&&($return > 0)){
        //   return true;
        // }else{
        //   return false;
        // }

        if ($this->db->trans_status() === FALSE || $this->connection->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        $this->connection->trans_commit();
        return TRUE;
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

        $this->connection->select('*');
        $this->connection->from('tb_expense_purchase_requisitions');
        $this->connection->join('tb_expense_purchase_requisition_details', 'tb_expense_purchase_requisition_details.expense_purchase_requisition_id = tb_expense_purchase_requisitions.id');
        $this->connection->join('tb_expense_monthly_budgets', 'tb_expense_monthly_budgets.id = tb_expense_purchase_requisition_details.expense_monthly_budget_id');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_expense_monthly_budgets.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->join('tb_departments', 'tb_departments.id = tb_cost_centers.department_id');
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
        $this->connection->join('tb_expense_purchase_requisition_details', 'tb_expense_purchase_requisition_details.expense_purchase_requisition_id = tb_expense_purchase_requisitions.id');
        $this->connection->join('tb_expense_monthly_budgets', 'tb_expense_monthly_budgets.id = tb_expense_purchase_requisition_details.expense_monthly_budget_id');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_expense_monthly_budgets.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->join('tb_departments', 'tb_departments.id = tb_cost_centers.department_id');
        $this->connection->where('tb_expense_purchase_requisitions.status', $status);
        $this->connection->like('tb_expense_purchase_requisitions.pr_number', $this->budget_year);
        $this->connection->where_in('tb_expense_purchase_requisitions.base', config_item('auth_warehouses'));
        $this->connection->where('tb_departments.department_name', config_item('head_department'));
        $query = $this->connection->get();
        $count_as_head_dept = $query->num_rows();
        }

        return $count+$count_as_head_dept;
    }
}
