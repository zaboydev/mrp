<?php defined('BASEPATH') or exit('No direct script access allowed');

class Capex_Request_Model extends MY_Model
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
            'tb_capex_purchase_requisitions.id'                         => NULL,
            'tb_capex_purchase_requisitions.pr_number'                  => 'Document Number',
            'tb_capex_purchase_requisitions.status'                     => 'Status',
            'tb_departments.department_name'                            => 'Department Name',
            'tb_cost_centers.cost_center_name'                          => 'Cost Center',
            'tb_capex_purchase_requisitions.pr_date'                    => 'Document Date',
            'tb_capex_purchase_requisitions.required_date'              => 'Required Date',
            'SUM(tb_capex_purchase_requisition_details.total) as total_capex'  => 'Total',
            'tb_capex_purchase_requisitions.notes'                      => 'Requisitions Notes',
            'tb_capex_purchase_requisitions.approved_notes'             => 'Notes',

        );
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
            'tb_departments.department_name',
            'tb_capex_purchase_requisitions.approved_notes'
        );
    }

    public function getOrderableColumns()
    {
        return array(
            null,
            'tb_capex_purchase_requisitions.pr_number',
            'tb_capex_purchase_requisitions.status',
            'tb_departments.department_name',
            'tb_cost_centers.cost_center_name',
            'tb_capex_purchase_requisitions.pr_date',
            'tb_capex_purchase_requisitions.required_date',
            NULL,
            'tb_capex_purchase_requisitions.notes',
            NULL
        );
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
        $this->connection->group_by($this->getGroupedColumns());

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
        $this->connection->group_by($this->getGroupedColumns());

        $query = $this->connection->get();

        return $query->num_rows();
    }

    public function findById($id)
    {
        $this->connection->select('tb_capex_purchase_requisitions.*, tb_cost_centers.cost_center_name');
        $this->connection->from('tb_capex_purchase_requisitions');
        $this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_capex_purchase_requisitions.annual_cost_center_id');
        $this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $this->connection->where('tb_capex_purchase_requisitions.id', $id);

        $query    = $this->connection->get();
        $request  = $query->unbuffered_row('array');

        $select = array(
          'tb_capex_purchase_requisition_details.*',
          'tb_products.product_name',
          'tb_products.product_code',
          'tb_capex_monthly_budgets.product_id',
          'SUM(tb_capex_monthly_budgets.mtd_quantity) AS fyp_quantity',
          'SUM(tb_capex_monthly_budgets.mtd_budget) AS fyp_budget',
          'SUM(tb_capex_monthly_budgets.mtd_used_quantity) AS fyp_used_quantity',
          'SUM(tb_capex_monthly_budgets.mtd_used_budget) AS fyp_used_budget',
        );

        $group_by = array(
          'tb_capex_purchase_requisition_details.id',
          'tb_products.product_name',
          'tb_products.product_code',
          'tb_capex_monthly_budgets.product_id',
        );

        $this->connection->select($select);
        $this->connection->from('tb_capex_purchase_requisition_details');
        $this->connection->join('tb_capex_monthly_budgets', 'tb_capex_monthly_budgets.id = tb_capex_purchase_requisition_details.capex_monthly_budget_id');
        $this->connection->join('tb_products', 'tb_products.id = tb_capex_monthly_budgets.product_id');
        $this->connection->where('tb_capex_purchase_requisition_details.capex_purchase_requisition_id', $id);
        $this->connection->group_by($group_by);

        $query = $this->connection->get();

        foreach ($query->result_array() as $key => $value){
          $request['items'][$key] = $value;

          $this->connection->from('tb_capex_monthly_budgets');
          $this->connection->where('tb_capex_monthly_budgets.annual_cost_center_id', $request['annual_cost_center_id']);
          $this->connection->where('tb_capex_monthly_budgets.product_id', $value['product_id']);
          $this->connection->where('tb_capex_monthly_budgets.month_number', $this->budget_month);
          // $this->connection->where('tb_capex_monthly_budgets.year_number', $this->budget_year);

          $query = $this->connection->get();
          $row   = $query->unbuffered_row('array');

          $request['items'][$key]['mtd_quantity'] = $row['mtd_quantity'];
          $request['items'][$key]['mtd_budget'] = $row['mtd_budget'];
          $request['items'][$key]['mtd_used_quantity'] = $row['mtd_used_quantity'];
          $request['items'][$key]['mtd_used_budget'] = $row['mtd_used_budget'];
          $request['items'][$key]['ytd_quantity'] = $row['ytd_quantity'];
          $request['items'][$key]['ytd_budget'] = $row['ytd_budget'];
          $request['items'][$key]['ytd_used_quantity'] = $row['ytd_used_quantity'];
          $request['items'][$key]['ytd_used_budget'] = $row['ytd_used_budget'];
        }

        return $request;
    }

    public function approve($id,$notes)
    {
        $this->connection->trans_begin();

        $this->connection->set('status','approved');
        $this->connection->set('approved_date',date('Y-m-d H:i:s'));
        $this->connection->set('approved_by',config_item('auth_person_name'));
        $this->connection->set('approved_notes',$notes);
        $this->connection->where('id',$id);
        $this->connection->update('tb_capex_purchase_requisitions');

        if ($this->connection->trans_status() === FALSE)
            return FALSE;

        $this->connection->trans_commit();
        return TRUE;
    }
}
