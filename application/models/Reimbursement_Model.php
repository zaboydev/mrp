<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reimbursement_Model extends MY_Model
{
    protected $module;
    protected $budget_year;
    protected $connection;
    protected $budget_month;

    public function __construct()
    {
        parent::__construct();
        
        $this->budget_year  = find_budget_setting('Active Year');
        $this->module = config_item('module')['reimbursement'];
        $this->connection   = $this->load->database('budgetcontrol', TRUE);
        $this->budget_year  = find_budget_setting('Active Year');
        $this->budget_month = find_budget_setting('Active Month');
    }

    public function getSelectedColumns()
    {
        $return = array(
            'No',
            'Document Date',
            'Document Number',
            'Type',
            'Status',
            'Department',
            'Name',
            'Amount',
            'Approval Notes'
        );
        return $return;
    }

    public function getSearchableColumns()
    {
        return array(
            'document_number',
            'person_name',
        );
    }

    public function getOrderableColumns()
    {
        return array(
            null,
            'date',
            'document_number',
            'type',
            'status',
        );
    }

    private function searchIndex()
    {
        if (!empty($_POST['columns'][1]['search']['value'])){
            $search_required_date = $_POST['columns'][1]['search']['value'];
            $range_date  = explode(' ', $search_required_date);

            $this->db->where('tb_reimbursements.date >= ', $range_date[0]);
            $this->db->where('tb_reimbursements.date <= ', $range_date[1]);
        }

        if (!empty($_POST['columns'][2]['search']['value'])){
            $search_status = $_POST['columns'][2]['search']['value'];

            if($search_status!='all'){
                $this->db->where('tb_reimbursements.status', $search_status);         
            }            
        }else{    
            if (config_item('as_head_department')=='yes' && !in_array(config_item('auth_username'),config_item('hr_manager'))){
                $this->db->where('tb_reimbursements.status ', 'WAITING APPROVAL BY HEAD DEPT');
                $this->db->where('tb_reimbursements.head_dept ', config_item('auth_username'));
            }
            elseif (in_array(config_item('auth_username'),config_item('hr_manager'))){                
                $this->db->where('tb_reimbursements.status ', 'WAITING APPROVAL BY HR MANAGER');
            }
            elseif (config_item('auth_role')=='FINANCE MANAGER'){                
                $this->db->where('tb_reimbursements.status ', 'WAITING APPROVAL BY FINANCE MANAGER');
            }
        }

        $i = 0;

        foreach ($this->getSearchableColumns() as $item){
            if ($_POST['search']['value']){
                if ($i === 0){
                $this->db->group_start();
                $this->db->like('UPPER('.$item.')', strtoupper($_POST['search']['value']));
                } else {
                $this->db->or_like('UPPER('.$item.')', strtoupper($_POST['search']['value']));
                }

                if (count($this->getSearchableColumns()) - 1 == $i)
                $this->db->group_end();
            }

            $i++;
        }
    }

    function getIndex($return = 'array')
    {
        $selected = array(
            'tb_reimbursements.*',
        );
        $this->db->select($selected);
        $this->db->from('tb_reimbursements');

        $this->searchIndex();

        $column_order = $this->getOrderableColumns();

        if (isset($_POST['order'])){
            foreach ($_POST['order'] as $key => $order){
                $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
            }
        } else {
            $this->db->order_by('id', 'desc');
        }

        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);

        $query = $this->db->get();

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
        $this->db->from('tb_reimbursements');

        $this->searchIndex();

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function countIndex()
    {
        $this->db->from('tb_reimbursements');

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function findById($id)
    {
        $selected = array(
            'tb_reimbursements.*',
        );
        $this->db->select($selected);
        $this->db->where('tb_reimbursements.id', $id);
        $query      = $this->db->get('tb_reimbursements');
        $row        = $query->unbuffered_row('array');

        $cost_center    = findCostCenter($row['annual_cost_center_id']);
        $head_dept      = findUserByUsername($row['head_dept']);
        $row['cost_center_code']    = $cost_center['cost_center_code'];
        $row['cost_center_name']    = $cost_center['cost_center_name'];
        $row['department_name']     = $cost_center['department_name']; 
        $row['department_id']       = $cost_center['department_id'];  
        $row['head_dept_name']       = $head_dept['person_name'];       

        $this->db->select('*');
        $this->db->from('tb_reimbursement_items');
        $this->db->where('tb_reimbursement_items.reimbursement_id', $id);

        $query = $this->db->get();

        foreach ($query->result_array() as $key => $value) {
            $row['items'][$key] = $value;
        }

        $this->db->select('*');
        $this->db->from('tb_signers');
        $this->db->where('tb_signers.document_number', $row['document_number']);
        $query_signers = $this->db->get();
        foreach ($query_signers->result_array() as $key => $valuesigners) {
            $row['signers'][$valuesigners['action']]['sign'] = $valuesigners['sign'];
            $row['signers'][$valuesigners['action']]['person_name'] = $valuesigners['person_name'];
            $row['signers'][$valuesigners['action']]['date'] = $valuesigners['date'];
            $row['signers'][$valuesigners['action']]['action'] = $valuesigners['action'];
            $row['signers'][$valuesigners['action']]['roles'] = $valuesigners['roles'];
        }

        return $row;
    }

    public function findOneBy($criteria)
    {
        $this->db->from('tb_master_business_trip_destinations');
        $this->db->where($criteria);

        $query = $this->db->get();

        return $query->unbuffered_row('array');
    }

    public function save()
    {
        $this->db->trans_begin();

        // DELETE OLD DOCUMENT
        if (isset($_SESSION['reimbursement']['id'])) {
            $id = $_SESSION['reimbursement']['id'];

            $this->db->select('*');
            $this->db->where('id', $id);
            $this->db->from('tb_reimbursements');

            $query = $this->db->get();
            $row   = $query->unbuffered_row('array');
            
            $this->db->set('status','REVISED');
            $this->db->where('id', $_SESSION['reimbursement']['id']);
            $this->db->update('tb_reimbursements');

            $this->db->set('document_type','RF');
            $this->db->set('document_number',$row['document_number']);
            $this->db->set('document_id', $id);
            $this->db->set('action','revised by');
            $this->db->set('date', $date);
            $this->db->set('username', config_item('auth_username'));
            $this->db->set('person_name', config_item('auth_person_name'));
            $this->db->set('roles', config_item('auth_role'));
            $this->db->set('notes', null);
            $this->db->set('sign', get_ttd(config_item('auth_person_name')));
            $this->db->set('created_at', date('Y-m-d H:i:s'));
            $this->db->insert('tb_signers');
        }

        // CREATE NEW DOCUMENT
        $document_edit    = (isset($_SESSION['reimbursement']['edit'])) ? $_SESSION['reimbursement']['edit'] : NULL;
        $document_number  = sprintf('%06s', $_SESSION['reimbursement']['document_number']) . $_SESSION['reimbursement']['format_number'];
        $date             = $_SESSION['reimbursement']['date'];
        $cost_center_code           = $_SESSION['reimbursement']['cost_center_code'];
        $cost_center_name           = $_SESSION['reimbursement']['cost_center_name'];
        $annual_cost_center_id      = $_SESSION['reimbursement']['annual_cost_center_id'];
        $warehouse                  = $_SESSION['reimbursement']['warehouse'];
        $notes                      = $_SESSION['reimbursement']['notes'];
        $employee_number            = $_SESSION['reimbursement']['employee_number'];
        $selected_person            = getEmployeeByEmployeeNumber($employee_number);
        $person_name                = $selected_person['name'];
        $department_id              = $_SESSION['reimbursement']['department_id'];
        $head_dept                  = $_SESSION['reimbursement']['head_dept'];
        $occupation                 = $_SESSION['reimbursement']['occupation'];
        $type                       = $_SESSION['reimbursement']['type'];
        $account_code               = $_SESSION['reimbursement']['account_code'];
        $employee_has_benefit_id                       = $_SESSION['reimbursement']['employee_has_benefit_id'];

        $this->db->set('annual_cost_center_id', $annual_cost_center_id);
        $this->db->set('warehouse', $warehouse);
        $this->db->set('employee_has_benefit_id', $employee_has_benefit_id);
        $this->db->set('document_number', $document_number);
        $this->db->set('type', $type);
        $this->db->set('employee_number', $employee_number);
        $this->db->set('person_name', $person_name);
        $this->db->set('date', $date);
        $this->db->set('occupation', $occupation);
        $this->db->set('head_dept', $head_dept);
        $this->db->set('notes', $notes);
        $this->db->set('account_code', $account_code);
        $this->db->set('total', 0);
        $this->db->set('request_by', config_item('auth_person_name'));
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->insert('tb_reimbursements');
        $document_id = $this->db->insert_id();

        $this->db->set('document_type','RF');
        $this->db->set('document_number',$document_number);
        $this->db->set('document_id', $document_id);
        $this->db->set('action','requested by');
        $this->db->set('date', $date);
        $this->db->set('username', config_item('auth_username'));
        $this->db->set('person_name', config_item('auth_person_name'));
        $this->db->set('roles', config_item('auth_role'));
        $this->db->set('notes', null);
        $this->db->set('sign', get_ttd(config_item('auth_person_name')));
        $this->db->set('created_at', date('Y-m-d H:i:s'));
        $this->db->insert('tb_signers');

        $total = array();

        // PROCESSING reimbursement ITEMS
        foreach ($_SESSION['reimbursement']['items'] as $key => $data) {
            
            /**
             * INSERT INTO TABLE REIMBURSEMENT ITEMS
             */
            $this->db->set('reimbursement_id', $document_id);
            $this->db->set('description', $data['description']);
            $this->db->set('transaction_date', $data['transaction_date']);
            $this->db->set('notes', $data['notes']);
            $this->db->set('amount', $data['amount']);
            $this->db->set('created_by', config_item('auth_person_name'));
            $this->db->set('updated_by', config_item('auth_person_name'));
            $this->db->insert('tb_reimbursement_items');

            /**
             * UPDATE TOTAL IN TABLE REIMBURSEMENT
             */
            $this->db->where('id', $document_id);
            $this->db->set('total', 'total +' . $data['amount'], FALSE);
            $this->db->update('tb_reimbursements');

            $this->db->set('used_amount_plafond', 'used_amount_plafond + ' . $data['amount'], FALSE);
            $this->db->set('left_amount_plafond', 'left_amount_plafond - ' . $data['amount'], FALSE);
            $this->db->where('tb_employee_has_benefit.id', $employee_has_benefit_id);
            $this->db->update('tb_employee_has_benefit');

            $total[] = $data['amount'];
        }

        $this->db->set('employee_has_benefit_id', $employee_has_benefit_id);
        $this->db->set('document_type', "REIMBURSEMENT");
        $this->db->set('document_id', $document_id);
        $this->db->set('document_number', $document_number);
        $this->db->set('date', $date);
        $this->db->set('year', date('Y'));
        $this->db->set('used_amount', array_sum($total));
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->insert('tb_used_benefits');

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        // $this->send_mail($document_id,'head_dept','request');

        $this->db->trans_commit();
        return TRUE;
    }

    public function update($id)
    {
        $this->db->trans_begin();

        $this->db->set('business_trip_destination', $this->input->post('business_trip_destination'));
        $this->db->set('notes', $this->input->post('notes'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->set('updated_at', date('Y-m-d H:i:s'));
        $this->db->where('id', $id);
        $this->db->update('tb_master_business_trip_destinations');
        $business_trip_destination_id = $id;

        $this->db->where('business_trip_purposes_id', $business_trip_destination_id);
        $this->db->delete('tb_master_business_trip_destination_items');

        $expense_names   = $this->input->post('expense_name');
        $amounts         = $this->input->post('amount');

        foreach ($expense_names as $key=>$expense_name){
            $this->db->set('business_trip_purposes_id', $business_trip_destination_id);
            $this->db->set('expense_name', $expense_name);
            $this->db->set('amount', $amounts[$key]);
            $this->db->set('created_by', config_item('auth_person_name'));
            $this->db->set('updated_by', config_item('auth_person_name'));
            $this->db->insert('tb_master_business_trip_destination_items');
        }

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function delete()
    {
        $this->db->trans_begin();

        $id = $this->input->post('id');

        $this->db->where('id', $id);
        $this->db->delete('tb_master_business_trip_destinations');

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function create_expense()
    {
        $this->db->trans_begin();        
        $this->connection->trans_begin();

        $id = $this->input->post('id');

        $reimbursement = $this->findById($id);
        $date = date('Y-m-d');
        $cekSettingApproval = cekSettingApproval('EXPENSE from REIMBURSEMENT');

        $url_rf = site_url('reimbursement/print_pdf/'.$id);
        $order_number = $this->getExpenseOrderNumber();
        $cost_center = $this->findCostCenter($reimbursement['annual_cost_center_id']);
        $format_number = $this->getExpenseFormatNumber($cost_center['cost_center_code']);
        $pr_number = $order_number.$format_number;
        $this->connection->set('annual_cost_center_id', $reimbursement['annual_cost_center_id']);
        $this->connection->set('order_number', $order_number);
        $this->connection->set('pr_number', $pr_number);
        $this->connection->set('pr_date', $date);
        $this->connection->set('required_date', $date);
        $this->connection->set('status', 'pending');
        $this->connection->set('notes', 'expense reimbursement : #'.$reimbursement['document_number']);
        $this->connection->set('created_by', config_item('auth_person_name'));
        $this->connection->set('updated_by', config_item('auth_person_name'));
        $this->connection->set('created_at', date('Y-m-d H:i:s'));
        $this->connection->set('updated_at', date('Y-m-d H:i:s'));
        $this->connection->set('with_po', false);
        $this->connection->set('head_dept', $reimbursement['head_dept']);
        $this->connection->set('base', config_item('auth_warehouse'));
        $this->connection->set('revisi', 1);//expense dari reimbursement tidak bisa direvisi
        $this->connection->set('approval_type', ($cekSettingApproval=='FULL APPROVAL')? 'FULL':'NOT FULL');
        $this->connection->set('reference_document', json_encode(['RF',$id,$reimbursement['document_number'],$url_rf]));
        $this->connection->insert('tb_expense_purchase_requisitions');

        $document_id = $this->connection->insert_id();

        $account = $this->getAccountByAccountCode($reimbursement['account_code']);

        // GET BUDGET MONTHLY ID
        $this->connection->from('tb_expense_monthly_budgets');
        $this->connection->where('tb_expense_monthly_budgets.account_id', $account['id']);
        $this->connection->where('tb_expense_monthly_budgets.annual_cost_center_id', $reimbursement['annual_cost_center_id']);
        $this->connection->where('tb_expense_monthly_budgets.month_number', $this->budget_month);
        // $this->connection->where('tb_capex_monthly_budgets.year_number', $this->budget_year);

        $query  = $this->connection->get();
        if ($query->num_rows() == 0) {
            //jika budget tidak ada
            // // NEW BUDGET
            $this->connection->set('annual_cost_center_id', $reimbursement['annual_cost_center_id']);
            $this->connection->set('account_id', $account['id']);
            $this->connection->set('month_number', $this->budget_month);
            // $this->connection->set('year_number', $this->budget_year);
            $this->connection->set('initial_quantity', floatval(0));
            $this->connection->set('initial_budget', floatval(0));
            $this->connection->set('mtd_quantity', floatval(0));
            $this->connection->set('mtd_budget', floatval($reimbursement['total']));
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
            $this->connection->set('ytd_budget', floatval($reimbursement['total']));
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

            //create expense unbudgeted
            $this->connection->set('annual_cost_center_id', $reimbursement['annual_cost_center_id']);
            $this->connection->set('expense_monthly_budget_id', $expense_monthly_budget_id);
            $this->connection->set('year_number', $this->budget_year);
            $this->connection->set('amount', $reimbursement['total']);
            $this->connection->set('previous_budget', 0);
            $this->connection->set('new_budget', $reimbursement[['total']]);
            $this->connection->set('created_at', date('Y-m-d'));
            $this->connection->set('created_by', config_item('auth_person_name'));
            $this->connection->set('notes', NULL);
            $this->connection->insert('tb_expense_unbudgeted');
        }else{
            $expense_monthly_budget    = $query->unbuffered_row();
            $expense_monthly_budget_id = $expense_monthly_budget->id;
        }

        $year = $this->budget_year;
        $month = $this->budget_month;

        for ($i = $month; $i < 13; $i++) {
            $this->connection->set('ytd_used_budget', 'ytd_used_budget + ' . $reimbursement['total'], FALSE);
            $this->connection->where('tb_expense_monthly_budgets.annual_cost_center_id', $reimbursement['annual_cost_center_id']);
            $this->connection->where('tb_expense_monthly_budgets.account_id', $account['id']);
            $this->connection->where('tb_expense_monthly_budgets.month_number', $i);
            $this->connection->update('tb_expense_monthly_budgets');
        }

        //insert data on used budget 
        $this->connection->set('expense_monthly_budget_id', $expense_monthly_budget_id);
        $this->connection->set('expense_purchase_requisition_id', $document_id);
        $this->connection->set('pr_number', $pr_number);
        $this->connection->set('cost_center', $cost_center['cost_center_name']);
        $this->connection->set('year_number', $this->budget_year);
        $this->connection->set('month_number', $this->budget_month);
        $this->connection->set('account_name', $account['account_name']);
        $this->connection->set('account_code', $account['account_code']);
        $this->connection->set('used_budget', $reimbursement['total']);
        $this->connection->set('created_at', date('Y-m-d H:i:s'));
        $this->connection->set('created_by', config_item('auth_person_name'));
        $this->connection->insert('tb_expense_used_budgets');

        //update monthly budget
        $this->connection->set('mtd_used_budget', 'mtd_used_budget + ' . $reimbursement['total'], FALSE);
        $this->connection->where('id', $expense_monthly_budget_id);
        $this->connection->update('tb_expense_monthly_budgets');

        $this->connection->set('expense_purchase_requisition_id', $document_id);
        $this->connection->set('expense_monthly_budget_id', $expense_monthly_budget_id);
        $this->connection->set('sort_order', floatval($key));
        // $this->connection->set('sisa', floatval($data['amount']));
        $this->connection->set('amount', floatval($reimbursement['total']));
        $this->connection->set('total', floatval($reimbursement['total']));
        $this->connection->set('reference_ipc', $reimbursement['document_number']);
        $this->connection->insert('tb_expense_purchase_requisition_details');

        $url_expense = site_url('expense_request/print_pdf/'.$document_id);
        $this->db->set('status','EXPENSE REQUEST');
        // $this->db->set('reference_document', json_encode(['RF',$id,$reimbursement['document_number'],$url_expense]));
        $this->db->where('id', $id);
        $this->db->update('tb_reimbursements');

        if ($this->db->trans_status() === FALSE || $this->connection->trans_status() === FALSE)
            return ['status'=>FALSE,'pr_number'=>$pr_number];

        $this->db->trans_commit();
        $this->connection->trans_commit();
        return ['status'=>TRUE,'pr_number'=>$pr_number];
    }

    public function getEmployeeHasBenefit($employee_number,$employee_benefit,$position)
    {
        $level = getLevelByPosition($position);
        if(isEmployeeContractActiveExist($employee_number)){
            $kontrak_active = findContractActive($employee_number);
            $this->db->select('tb_employee_has_benefit.*');
            $this->db->join('tb_master_employee_benefits','tb_master_employee_benefits.id=tb_employee_has_benefit.employee_benefit_id');
            $this->db->where('tb_master_employee_benefits.employee_benefit',$employee_benefit);
            $this->db->where('tb_employee_has_benefit.employee_number',$employee_number);
            $this->db->where('tb_employee_has_benefit.employee_contract_id',$kontrak_active['id']);
            $this->db->where('tb_employee_has_benefit.deleted_at IS NULL', null, false);
            $this->db->from('tb_employee_has_benefit');
            $queryemployee_has_benefit  = $this->db->get();
            $rowemployee_has_benefit    = $queryemployee_has_benefit->unbuffered_row('array');

            if($queryemployee_has_benefit->num_rows()>0){
                $return['status'] = 'success';
                $return['saldo_balance'] = $rowemployee_has_benefit['left_amount_plafond'];
                $return['employee_has_benefit_id'] = $rowemployee_has_benefit['id'];
            }else{
                $return['status'] = 'warning';
                $return['saldo_balance'] = 0;
                $return['employee_has_benefit_id'] = null;
                $return['message'] = 'Karyawan ini tidak memiliki Saldo untuk Benefit ini';
            }            
            
            return $return;
        }else{
            $return['status'] = 'warning';
            $return['saldo_balance'] = 0;
            $return['employee_has_benefit_id'] = null;
            $return['message'] = 'Karyawan ini tidak memiliki Kontrak Aktif dan Saldo balance';
            
            return $return;
        }

        

        
    }

    public function getEmployeeHasBenefitById($employee_has_benefit_id)
    {
        $kontrak_active = findContractActive($employee_number);
        $this->db->select('tb_employee_has_benefit.*');
        $this->db->where('tb_employee_has_benefit.id',$employee_has_benefit_id);
        $this->db->from('tb_employee_has_benefit');
        $queryemployee_has_benefit  = $this->db->get();
        $rowemployee_has_benefit    = $queryemployee_has_benefit->unbuffered_row('array');

        return $rowemployee_has_benefit;
    }

    public function countExpenseAmount($business_trip_purposes_id)
    {
        $this->db->select_sum('tb_master_business_trip_destination_items.amount', 'expense_amount');
        $this->db->where('tb_master_business_trip_destination_items.business_trip_purposes_id',$business_trip_purposes_id);
        $this->db->from('tb_master_business_trip_destination_items');
        $query  = $this->db->get();
        $row    = $query->unbuffered_row('array');
        
        return $row['expense_amount'];
    }

    public function approve($document_id,$approval_notes)
    {
        $this->db->trans_begin();

        $total      = 0;
        $success    = 0;
        $failed     = sizeof($document_id);
        $x          = 0;
        $send_email_to = NULL;

        foreach ($document_id as $id) {
            $selected = array(
                'tb_reimbursements.*',
                // 'tb_master_business_trip_destinations.business_trip_destination'
            );
            $this->db->select($selected);
            $this->db->where('tb_reimbursements.id', $id);
            // $this->db->join('tb_master_business_trip_destinations', 'tb_master_business_trip_destinations.id = tb_business_trip_purposes.business_trip_destination_id');
            $query      = $this->db->get('tb_reimbursements');
            $spd        = $query->unbuffered_row('array');

            $cost_center = findCostCenter($spd['annual_cost_center_id']);
            $cost_center_code = $cost_center['cost_center_code'];
            $cost_center_name = $cost_center['cost_center_name'];
            $department_name = $cost_center['department_name'];

            if($spd['status']=='WAITING APPROVAL BY HEAD DEPT' && in_array($department_name,config_item('head_department')) && $spd['head_dept']==config_item('auth_username')){
                $this->db->set('status','WAITING APPROVAL BY HR MANAGER');
                $this->db->set('validated_by',config_item('auth_person_name'));
                $this->db->where('id', $id);
                $this->db->update('tb_reimbursements');

                $this->db->set('document_type','RF');
                $this->db->set('document_number',$spd['document_number']);
                $this->db->set('document_id', $id);
                $this->db->set('action','validated by');
                $this->db->set('date', date('Y-m-d'));
                $this->db->set('username', config_item('auth_username'));
                $this->db->set('person_name', config_item('auth_person_name'));
                $this->db->set('roles', config_item('auth_role'));
                $this->db->set('notes', $approval_notes[$x]);
                $this->db->set('sign', get_ttd(config_item('auth_person_name')));
                $this->db->set('created_at', date('Y-m-d H:i:s'));
                $this->db->insert('tb_signers');
                $send_email_to = 'hr_manager';

            }elseif($spd['status']=='WAITING APPROVAL BY HR MANAGER' && in_array(config_item('auth_username'),config_item('hr_manager'))){
                $this->db->set('status','WAITING APPROVAL BY FINANCE MANAGER');
                $this->db->set('hr_approved_by',config_item('auth_person_name'));
                $this->db->where('id', $id);
                $this->db->update('tb_reimbursements');

                $this->db->set('document_type','RF');
                $this->db->set('document_number',$spd['document_number']);
                $this->db->set('document_id', $id);
                $this->db->set('action','hr approved by');
                $this->db->set('date', date('Y-m-d'));
                $this->db->set('username', config_item('auth_username'));
                $this->db->set('person_name', config_item('auth_person_name'));
                $this->db->set('roles', config_item('auth_role'));
                $this->db->set('notes', $approval_notes[$x]);
                $this->db->set('sign', get_ttd(config_item('auth_person_name')));
                $this->db->set('created_at', date('Y-m-d H:i:s'));
                $this->db->insert('tb_signers');
                $send_email_to = 'finance_manager';
            }elseif($spd['status']=='WAITING APPROVAL BY FINANCE MANAGER' && config_item('auth_role')=='FINANCE MANAGER'){
                $this->db->set('status','APPROVED');
                $this->db->set('finance_approved_by',config_item('auth_person_name'));
                $this->db->where('id', $id);
                $this->db->update('tb_reimbursements');

                $this->db->set('document_type','RF');
                $this->db->set('document_number',$spd['document_number']);
                $this->db->set('document_id', $id);
                $this->db->set('action','finance approved by');
                $this->db->set('date', date('Y-m-d'));
                $this->db->set('username', config_item('auth_username'));
                $this->db->set('person_name', config_item('auth_person_name'));
                $this->db->set('roles', config_item('auth_role'));
                $this->db->set('notes', $approval_notes[$x]);
                $this->db->set('sign', get_ttd(config_item('auth_person_name')));
                $this->db->set('created_at', date('Y-m-d H:i:s'));
                $this->db->insert('tb_signers');
                $send_email_to = NULL;
            }
            $total++;
            $success++;
            $failed--;
        }

        

        if ($this->db->trans_status() === FALSE)
            return $return = ['status'=> FALSE,'total'=>$total,'success'=>$success,'failed'=>$failed];

        if($send_email_to!=NULL){
            $this->send_mail($document_id, $send_email_to);
        }
        

        $this->db->trans_commit();
        return $return = ['status'=> TRUE,'total'=>$total,'success'=>$success,'failed'=>$failed];
    }

    public function reject($document_id,$approval_notes)
    {
        $this->db->trans_begin();

        $total      = 0;
        $success    = 0;
        $failed     = sizeof($document_id);
        $x          = 0;

        foreach ($document_id as $id) {
            $selected = array(
                'tb_business_trip_purposes.*',
                'tb_master_business_trip_destinations.business_trip_destination'
            );
            $this->db->select($selected);
            $this->db->where('tb_business_trip_purposes.id', $id);
            $this->db->join('tb_master_business_trip_destinations', 'tb_master_business_trip_destinations.id = tb_business_trip_purposes.business_trip_destination_id');
            $query      = $this->db->get('tb_business_trip_purposes');
            $spd        = $query->unbuffered_row('array');

            $cost_center = findCostCenter($spd['annual_cost_center_id']);
            $cost_center_code = $cost_center['cost_center_code'];
            $cost_center_name = $cost_center['cost_center_name'];
            $department_name = $cost_center['department_name'];

            if($spd['status']=='WAITING APPROVAL BY HEAD DEPT' && in_array($department_name,config_item('head_department')) && $spd['head_dept']==config_item('auth_username')){
                $this->db->set('status','REJECTED');
                $this->db->set('rejected_by',config_item('auth_person_name'));
                $this->db->where('id', $id);
                $this->db->update('tb_business_trip_purposes');

                $this->db->set('document_type','SPD');
                $this->db->set('document_number',$spd['document_number']);
                $this->db->set('document_id', $id);
                $this->db->set('action','rejected by');
                $this->db->set('date', date('Y-m-d'));
                $this->db->set('username', config_item('auth_username'));
                $this->db->set('person_name', config_item('auth_person_name'));
                $this->db->set('roles', config_item('auth_role'));
                $this->db->set('notes', $approval_notes[$x]);
                $this->db->set('sign', get_ttd(config_item('auth_person_name')));
                $this->db->set('created_at', date('Y-m-d H:i:s'));
                $this->db->insert('tb_signers');

            }elseif($spd['status']=='WAITING APPROVAL BY HR MANAGER' && in_array(list_user_in_head_department($cost_center['department_id']),config_item('auth_username'))){
                
            }
            $total++;
            $success++;
            $failed--;
        }

        

        if ($this->db->trans_status() === FALSE)
            return $return = ['status'=> FALSE,'total'=>$total,'success'=>$success,'failed'=>$failed];

        // $this->send_mail($document_id, 'hr_manager');

        $this->db->trans_commit();
        return $return = ['status'=> TRUE,'total'=>$total,'success'=>$success,'failed'=>$failed];
    }

    public function send_mail($doc_id,$next_approval,$tipe='request')
    {
        if($next_approval=='hr_manager'){
            $recipientList = getNotifRecipientHrManager();
            $keterangan = 'HR Manager';
        }elseif($next_approval=='head_dept'){
            $selected = array(
                'tb_reimbursements.*',
            );
            $this->db->select($selected);
            $this->db->where('tb_reimbursements.id',$doc_id);
            $query      = $this->db->get('tb_reimbursements');
            $row        = $query->unbuffered_row('array');
            $department = getDepartmentByAnnualCostCenterId($row['annual_cost_center_id']);
            $keterangan = "Head Dept : " . $department['department_name'];

            $recipientList = getNotifRecipient_byUsername($row['head_dept']);
        }elseif($next_approval=='finance_manager'){
            $recipientList = getNotifRecipientFinManager();
            $keterangan = 'Finance Manager';
        }

        $recipient = array();
        foreach ($recipientList as $key) {
          array_push($recipient, $key['email']);
        }

        if(!empty($recipient)){
            $selected = array(
                'tb_reimbursements.*',
            );
            $this->db->select($selected);
            if(is_array($doc_id)){
                $this->db->where_in('tb_reimbursements.id',$doc_id);
            }else{
                $this->db->where('tb_reimbursements.id',$doc_id);
            }
            $query      = $this->db->get('tb_reimbursements');
    
            $item_message = '<tbody>';
            foreach ($query->result_array() as $key => $item) {
                $item_message .= "<tr>";
                $item_message .= "<td>" . print_date($item['date']) . "</td>";
                $item_message .= "<td>" . $item['document_number'] . "</td>";
                $item_message .= "<td>" . $item['type'] . "</td>";
                $item_message .= "<td>" . $item['person_name'] . "</td>";
                $item_message .= "</tr>";
            }
            $item_message .= '</tbody>';

            $this->load->library('email');
            $this->email->set_newline("\r\n");
            $from_email = "bifa.acd@gmail.com";
            $to_email = "aidanurul99@rocketmail.com";
            $message = "<p>Dear ".$keterangan."</p>";
            $message .= "<p>SPD Berikut perlu Persetujuan Anda </p>";
            $message .= "<table class='table'>";
            $message .= "<thead>";
            $message .= "<tr>";
            $message .= "<th>Date</th>";
            $message .= "<th>No. Reimbursement</th>";
            $message .= "<th>Type</th>";
            $message .= "<th>Name</th>";
            $message .= "</tr>";
            $message .= "</thead>";
            $message .= $item_message;
            $message .= "</table>";
            $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
            $message .= "<p>[ <a href='".$this->config->item('url_mrp')."' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
            $message .= "<p>Thanks and regards</p>";
            $this->email->from($from_email, 'Material Resource Planning');
            $this->email->to($recipient);
            $this->email->subject('Permintaan Approval Reimbursement');
            $this->email->message($message);
            
    
            // Send mail 
            if ($this->email->send())
              return true;
            else
              return $this->email->print_debugger();
        }else{
            return true;
        }
        
    }

    public function getExpenseOrderNumber(){
        $this->connection->select_max('order_number', 'last_number');
        $this->connection->from('tb_expense_purchase_requisitions');
        $query        = $this->connection->get();
        if($query->num_rows() > 0){      
            $request      = $query->unbuffered_row('array');
            $last_number  = $request['last_number'];
            $return       = $last_number + 1;
        }else{
            $return = 1;
        }

        return $return;
    }

    public function findCostCenter($annual_cost_center_id){
        $this->connection->select(array('cost_center_code','cost_center_name','department_id'));
        $this->connection->from( 'tb_cost_centers' );
        $this->connection->join('tb_annual_cost_centers','tb_annual_cost_centers.cost_center_id=tb_cost_centers.id');
        $this->connection->where('tb_annual_cost_centers.id', $annual_cost_center_id);

        $query    = $this->connection->get();
        $cost_center = $query->unbuffered_row('array');

        return $cost_center;
    }

    public function getExpenseFormatNumber($cost_center_code){
        $return = '/Exp/'.$cost_center_code.'/'.find_budget_setting('Active Year');

        return $return;
    }

    public function getAccountByAccountCode($account_code){
        $this->connection->select('*');
        $this->connection->from( 'tb_accounts' );
        $this->connection->where('tb_accounts.account_code', $account_code);

        $query    = $this->connection->get();
        $account = $query->unbuffered_row('array');

        return $account;
    }
}
