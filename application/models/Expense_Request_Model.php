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
            'tb_cost_centers.cost_center_name'                                  => 'Cost Center',
            'tb_expense_purchase_requisitions.pr_date'                          => 'Pr Date',
            'tb_expense_purchase_requisitions.required_date'                    => 'Required Date',
            'tb_accounts.account_name'                                           => 'Account',
            'tb_expense_purchase_requisition_details.total'  => 'Total',
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
            'tb_accounts.account_name',
            'tb_expense_purchase_requisition_details.total',
            'tb_expense_purchase_requisitions.notes',
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
            'tb_accounts.account_name',
            // 'tb_expense_purchase_requisition_detail.total',
            'tb_expense_purchase_requisitions.notes',
        );
    }

    public function getOrderableColumns()
    {
        return array(
            null,
            // 'tb_expense_purchase_requisitions.id',
            'tb_expense_purchase_requisitions.pr_number',
            'tb_cost_centers.cost_center_name',
            'tb_expense_purchase_requisitions.pr_date',
            'tb_expense_purchase_requisitions.required_date',
            'tb_accounts.account_name',
            'tb_expense_purchase_requisition_details.total',
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
            // if($search_status!='all'){
                $this->connection->where('tb_expense_purchase_requisitions.status', $search_status);
            // }
        } 
        // else {
        //     $this->connection->where('tb_expense_purchase_requisitions.status', 'pending');
        // }

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
        $this->connection->join('tb_accounts', 'tb_accounts.id = tb_expense_monthly_budgets.account_id');
        $this->connection->like('tb_expense_purchase_requisitions.pr_number', $this->budget_year);
        // $this->connection->group_by($this->getGroupedColumns());

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
        $this->connection->from('tb_expense_purchase_requisitions');
        $this->connection->join('tb_expense_purchase_requisition_details', 'tb_expense_purchase_requisition_details.expense_purchase_requisition_id = tb_expense_purchase_requisitions.id');
        $this->connection->join('tb_expense_monthly_budgets', 'tb_expense_monthly_budgets.id = tb_expense_purchase_requisition_details.expense_monthly_budget_id');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_expense_monthly_budgets.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->join('tb_accounts', 'tb_accounts.id = tb_expense_monthly_budgets.account_id');
        $this->connection->like('tb_expense_purchase_requisitions.pr_number', $this->budget_year);
        // $this->connection->group_by($this->getGroupedColumns());

        $this->searchIndex();

        $query = $this->connection->get();

        return $query->num_rows();
    }

    public function countIndex()
    {
        $this->connection->from('tb_expense_purchase_requisitions');
        $this->connection->join('tb_expense_purchase_requisition_details', 'tb_expense_purchase_requisition_details.expense_purchase_requisition_id = tb_expense_purchase_requisitions.id');
        $this->connection->join('tb_expense_monthly_budgets', 'tb_expense_monthly_budgets.id = tb_expense_purchase_requisition_details.expense_monthly_budget_id');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_expense_monthly_budgets.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->join('tb_accounts', 'tb_accounts.id = tb_expense_monthly_budgets.account_id');
        $this->connection->like('tb_expense_purchase_requisitions.pr_number', $this->budget_year);
        // $this->connection->group_by($this->getGroupedColumns());

        $query = $this->connection->get();

        return $query->num_rows();
    }
}
