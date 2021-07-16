<?php defined('BASEPATH') or exit('No direct script access allowed');

class Expense_Closing_Payment_Model extends MY_Model
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
            'tb_expense_purchase_requisitions.closing_date'                        => 'Closing Date',
            'tb_expense_purchase_requisitions.account'                        => 'Account',
            'SUM(tb_expense_purchase_requisition_details.total) as total_expense'  => 'Total',
            'tb_expense_purchase_requisitions.closing_notes'                       => 'Notes',
        );
    }

    public function getGroupedColumns()
    {
        return array(
            'tb_expense_purchase_requisitions.id',
            'tb_expense_purchase_requisitions.pr_number',
            'tb_cost_centers.cost_center_name',
            'tb_expense_purchase_requisitions.pr_date',
            'tb_expense_purchase_requisitions.closing_date',
            'tb_expense_purchase_requisitions.account',
            // 'tb_expense_purchase_requisition_details.total',
            'tb_expense_purchase_requisitions.closing_notes',
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
            'tb_expense_purchase_requisitions.closing_notes',
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
            'tb_expense_purchase_requisitions.closing_date',
            'tb_expense_purchase_requisitions.account',
            null,
            'tb_expense_purchase_requisitions.notes',
        );
    }

    private function searchIndex()
    {
        if (!empty($_POST['columns'][1]['search']['value'])){
            $search_required_date = $_POST['columns'][1]['search']['value'];
            $range_date  = explode(' ', $search_required_date);

            $this->connection->where('tb_expense_purchase_requisitions.closing_date >= ', $range_date[0]);
            $this->connection->where('tb_expense_purchase_requisitions.closing_date <= ', $range_date[1]);
        }

        if (!empty($_POST['columns'][2]['search']['value'])){
            $search_cost_center = $_POST['columns'][2]['search']['value'];
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
        $this->connection->from('tb_expense_purchase_requisitions');
        $this->connection->join('tb_expense_purchase_requisition_details', 'tb_expense_purchase_requisition_details.expense_purchase_requisition_id = tb_expense_purchase_requisitions.id');
        $this->connection->join('tb_expense_monthly_budgets', 'tb_expense_monthly_budgets.id = tb_expense_purchase_requisition_details.expense_monthly_budget_id');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_expense_monthly_budgets.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->join('tb_departments', 'tb_departments.id = tb_cost_centers.department_id');
        // $this->connection->join('tb_accounts', 'tb_accounts.id = tb_expense_monthly_budgets.account_id');
        $this->connection->like('tb_expense_purchase_requisitions.pr_number', $this->budget_year);
        $this->connection->where('tb_expense_purchase_requisitions.with_po', 'f');
        $this->connection->where('tb_expense_purchase_requisitions.status', 'close');
        $this->connection->where_in('tb_cost_centers.cost_center_name', config_item('auth_annual_cost_centers_name'));
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
        $this->connection->where('tb_expense_purchase_requisitions.with_po', 'f');
        $this->connection->where('tb_expense_purchase_requisitions.status', 'close');
        $this->connection->where_in('tb_cost_centers.cost_center_name', config_item('auth_annual_cost_centers_name'));
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
        $this->connection->where('tb_expense_purchase_requisitions.with_po', 'f');
        $this->connection->where('tb_expense_purchase_requisitions.status', 'close');
        $this->connection->where_in('tb_cost_centers.cost_center_name', config_item('auth_annual_cost_centers_name'));
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
        $id                 = (isset($_SESSION['expense_closing']['id'])) ? $_SESSION['expense_closing']['id'] : NULL;
        $closing_date       = $_SESSION['expense_closing']['date'];
        $closing_by         = config_item('auth_person_name');
        $notes              = (empty($_SESSION['expense_closing']['closing_notes'])) ? NULL : $_SESSION['expense_closing']['closing_notes'];
        $account            = $_SESSION['expense_closing']['account'];

        $this->connection->trans_begin();

        $this->connection->set('closing_date', $closing_date);
        $this->connection->set('status', 'close');
        $this->connection->set('closing_notes', $notes);
        $this->connection->set('closing_by', $closing_by);
        $this->connection->set('account', $account);
        $this->connection->where('id', $id);
        $this->connection->update('tb_expense_purchase_requisitions');

        $this->connection->select('tb_expense_purchase_requisition_details.total, tb_expense_purchase_requisition_details.id');
        $this->connection->from('tb_expense_purchase_requisition_details');
        $this->connection->where('tb_expense_purchase_requisition_details.expense_purchase_requisition_id', $id);

        $query  = $this->connection->get();
        $result = $query->result_array();

        foreach ($result as $data) {
            $this->connection->set('process_amount', '"process_amount" + ' . $data['total'], false);
            $this->connection->where('id', $data['id']);
            $this->connection->update('tb_expense_purchase_requisition_details');
        }
        

        if ($this->connection->trans_status() === FALSE)
          return FALSE;

        $this->connection->trans_commit();

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
        $prl_item_id = getPrlid($poe_item_id);

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

    public function findExpenseRequestByid($id)
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
        }

        return $request;
    }
}
