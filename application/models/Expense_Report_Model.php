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
            $cost_centers[$key]['cc_code']      = strtoupper($cost_center_code);
            $cost_centers[$key]['budget_year']  = $this->getExpenseBudget($annual_cost_center_id,12,$year,'ytd_budget');
            $cost_centers[$key]['budget_used']      = 0;
            $cost_centers[$key]['budget_rest']      = $cost_centers[$key]['budget_year']-$cost_centers[$key]['budget_used'];
            if($cost_centers[$key]['budget_rest']!=0){
                $cost_centers[$key]['budget_rest_persen'] = ($cost_centers[$key]['budget_rest']/$cost_centers[$key]['budget_year'])*100;
            }else{
                $cost_centers[$key]['budget_rest_persen'] = 0;
            }
            for ($i=1;$i<=find_budget_setting('Active Month');$i++){
                $cost_centers[$key][$i.'-actual']           = 0;
                $cost_centers[$key][$i.'-budget']           = $this->getExpenseBudget($annual_cost_center_id,$i,$year,'mtd_budget');
                $cost_centers[$key][$i.'-mtd-ab-rp']        = $cost_centers[$key][$i.'-budget']-$cost_centers[$key][$i.'-actual'];
                $cost_centers[$key][$i.'-ytd-actual']       = 0;
                $cost_centers[$key][$i.'-ytd-budget']       = $this->getExpenseBudget($annual_cost_center_id,$i,$year,'ytd_budget');
                $cost_centers[$key][$i.'-ytd-ab-rp']        = $cost_centers[$key][$i.'-ytd-budget']-$cost_centers[$key][$i.'-ytd-actual'];
                if($cost_centers[$key][$i.'-mtd-ab-rp']!=0){
                    $cost_centers[$key][$i.'-mtd-ab-persen'] = ($cost_centers[$key][$i.'-mtd-ab-rp']/$cost_centers[$key][$i.'-budget'])*100;
                }else{
                    $cost_centers[$key][$i.'-mtd-ab-persen'] = 0;
                }

                if($cost_centers[$key][$i.'-ytd-ab-rp']!=0){
                    $cost_centers[$key][$i.'-ytd-ab-persen'] = ($cost_centers[$key][$i.'-ytd-ab-rp']/$cost_centers[$key][$i.'-ytd-budget'])*100;
                }else{
                    $cost_centers[$key][$i.'-ytd-ab-persen'] = 0;
                }
            }
        }

        return $cost_centers;
    }

    function getExpenseBudget($annual_cost_center_id, $month, $year,$select){
        $this->connection->select('sum(mtd_budget)');
        $this->connection->from('tb_expense_monthly_budgets');        
        $this->connection->where('tb_expense_monthly_budgets.annual_cost_center_id', $annual_cost_center_id); 
        if($select=='mtd_budget'){       
            $this->connection->where('tb_expense_monthly_budgets.month_number', $month);
        }elseif($select=='ytd_budget'){
            $this->connection->where('tb_expense_monthly_budgets.month_number <=',$month);
        }

        $query  = $this->connection->get('')->row()->sum;

        return $query;
    }

    function getExpenseBudgetDetail($annual_cost_center_id,$account_id, $month, $year,$select){
        $this->connection->select('sum(mtd_budget)');
        $this->connection->from('tb_expense_monthly_budgets');        
        $this->connection->where('tb_expense_monthly_budgets.annual_cost_center_id', $annual_cost_center_id); 
        $this->connection->where('tb_expense_monthly_budgets.account_id', $account_id); 
        if($select=='mtd_budget'){       
            $this->connection->where('tb_expense_monthly_budgets.month_number', $month);
        }elseif($select=='ytd_budget'){
            $this->connection->where('tb_expense_monthly_budgets.month_number <=',$month);
        }

        $query  = $this->connection->get('')->row()->sum;

        return $query;
    }

    public function getReportKonsolidasiDetail($year,$month,$view)
    {
        $accounts = $this->getAccounts();
        $month = $month;
        $year = $year;
        
        foreach ($accounts as $key => $account) {
            $account_id = $account['id'];
            $accounts[$key]['annual_cost_centers'] = get_annual_cost_centers($year,$view);
            // $annual_cost_centers = get_annual_cost_centers($active_year,$view);
            foreach ($accounts[$key]['annual_cost_centers'] as $i => $annual_cost_center) {
                // $accounts[$key]['annual_cost_centers'][$i] = $annual_cost_center;
                $annual_cost_center_id = $annual_cost_center['id'];                
                $accounts[$key]['annual_cost_centers'][$i]['ytd_budget'] = $this->getExpenseBudgetDetail($annual_cost_center_id,$account_id,$month,$year,'ytd_budget');
                $accounts[$key]['annual_cost_centers'][$i]['ytd_actual'] = 0;
                // $accounts[$key]['annual_cost_centers'][$i]['ytd_budget'] = 99;
            }
        }

        return $accounts;
    }

    public function getAccounts(){
        $this->connection->select('*');
        $this->connection->from('tb_accounts');
        $this->connection->order_by('tb_accounts.account_code','asc');
        $query = $this->connection->get();

        return $query->result_array();
    }


}

/* End of file Account_Payable_Model.php */
/* Location: ./application/models/Account_Payable_Model.php */
