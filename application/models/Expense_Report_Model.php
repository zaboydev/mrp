<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Expense_Report_Model extends MY_Model
{
    protected $connection;
    protected $budget_year;
    protected $budget_month;

    public function __construct()
    {
        parent::__construct();
        //Do your magic here
        $this->connection   = $this->load->database('budgetcontrol', TRUE);
        // $this->budget_year  = find_budget_setting('Active Year');
        // $this->budget_month = find_budget_setting('Active Month');
    }

    public function getReportKonsolidasi()
    {
        $cost_centers = config_item('auth_annual_cost_centers');
        $year = find_budget_setting('Active Year');
        foreach (config_item('auth_annual_cost_centers') as $key => $value) {
            $annual_cost_center_id = $value['id'];
            $cost_center = findCostCenter($annual_cost_center_id);
            $cost_center_code = $cost_center['cost_center_code'];
            $cost_centers[$key]['cc_code'] = strtoupper($cost_center_code);
            for ($i=1;$i<=find_budget_setting('Active Month');$i++){
                $cost_centers[$key][$i.'-budget'] = $this->getExpenseBudget($annual_cost_center_id,$i,$year,'mtd_budget');
            }
        }

        return $cost_centers;
    }

    function getExpenseBudget($annual_cost_center_id, $month, $year,$select){
        $this->connection->select($select);
        $this->connection->from('tb_expense_monthly_budgets');        
        $this->connection->where('tb_expense_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);        
        $this->connection->where('tb_expense_monthly_budgets.month_number', $month);

        $query  = $this->connection->get();
        $row    = $query->unbuffered_row();
        if($select=='mtd_budget'){
            $return = $row->mtd_budget;
        }elseif($select='ytd_budget'){
            $return = $row->ytd_budget;
        }

        return $return;
    }


}

/* End of file Account_Payable_Model.php */
/* Location: ./application/models/Account_Payable_Model.php */
