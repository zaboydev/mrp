<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_Model extends MY_Model
{
    protected $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = config_item('module')['user_position'];
    }

    public function getSelectedColumns()
    {
        return array(
            'No',
            'Employee Number',
            'Name',
            'Department',
            'Occupation',
            'Last Update'
        );
    }

    public function getSearchableColumns()
    {
        return array(
            'employee_number',
            'name',
            'position',
        );
    }

    public function getOrderableColumns()
    {
        return array(
            null,
            'employee_number',
            'name',
            'position',
            null,
            'updated_at',
        );
    }

    private function searchIndex()
    {
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
        $this->db->select('*');
        $this->db->from('tb_master_employees');

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
        $this->db->from('tb_master_employees');

        $this->searchIndex();

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function countIndex()
    {
        $this->db->from('tb_master_employees');

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function findById($id)
    {
        $this->db->where('employee_number', $id);
        $query      = $this->db->get('tb_master_employees');
        $row        = $query->unbuffered_row('array');

        $department = getDepartmentById($row['department_id']);
        $row['department_name'] = $department['department_name'];

        return $row;
    }

    public function findOneBy($criteria)
    {
        $this->db->from('tb_master_employees');
        $this->db->where($criteria);

        $query = $this->db->get();
        $row   = $query->unbuffered_row('array');

        $department = getDepartmentById($row['department_id']);
        $row['department_name'] = $department['department_name'];

        return $row;
    }

    public function insert(array $user_data)
    {
        $this->db->trans_begin();

        $this->db->set($user_data);
        $this->db->insert('tb_master_employees');

        //insert employee benefit
        // $employee_benefit = getEmployeeBenefitByOccupation($user_data['position']);

        // foreach ($employee_benefit as $key => $value) {
        //     $this->db->set('employee_number',$user_data['employee_number']);
        //     $this->db->set('year',$value['year']);
        //     $this->db->set('employee_benefit_item_id',$value['id']);
        //     $this->db->set('amount_plafond',$value['amount']);
        //     $this->db->set('left_amount_plafond',$value['amount']);
        //     $this->db->set('used_amount_plafond',0);
        //     $this->db->set('created_by', config_item('auth_person_name'));
        //     $this->db->set('updated_by', config_item('auth_person_name'));
        //     $this->db->insert('tb_employee_has_benefit');
        // }

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function update(array $user_data, $id)
    {
        $this->db->trans_begin();

        $this->db->set($user_data);
        $this->db->where('employee_number',$id);
        $this->db->update('tb_master_employees');

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function delete()
    {
        $this->db->trans_begin();

        $id = $this->input->post('id');

        $this->db->where('employee_number', $id);
        $this->db->delete('tb_master_employees');

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function insert_batch(array $user_data)
    {
        $this->db->trans_begin();

        foreach ($user_data as $key => $data){
            $department = getDepartmentByName($data['department']);
            $employee_id = $this->get_unused_id();
            $this->db->set('employee_number', strtoupper($data['employee_number']));
            $this->db->set('employee_id', $employee_id);
            $this->db->set('department_id', $department['id']);
            $this->db->set('name', strtoupper($data['name']));
            $this->db->set('date_of_birth', $data['date_of_birth']);
            $this->db->set('gender', strtoupper($data['gender']));
            $this->db->set('religion', strtoupper($data['religion']));
            $this->db->set('phone_number', $data['phone_number']);
            $this->db->set('marital_status', $data['marital_status']);
            $this->db->set('email', $data['email']);
            $this->db->set('address', $data['address']);
            $this->db->set('position', strtoupper($data['jabatan']));
            $this->db->set('identity_number', $data['identity_number']);
            $this->db->set('identity_type', strtoupper($data['identity_type']));
            $this->db->set('warehouse', strtoupper($data['base']));
            $this->db->set('bank_account', $data['bank_account']);
            $this->db->set('bank_account_name', $data['bank_account_name']);
            $this->db->set('npwp', $data['npwp']);
            $this->db->set('basic_salary', $data['basic_salary']);
            $this->db->set('tanggal_bergabung', $data['tanggal_bergabung']);
            $this->db->set('created_by', config_item('auth_person_name'));
            $this->db->set('updated_by', config_item('auth_person_name'));
            $this->db->insert('tb_master_employees');
        }

        if ($this->db->trans_status() === FALSE)
        return FALSE;

        $this->db->trans_commit();

        return TRUE;
    }
    
    public function get_unused_id(array $except = null)
    {
        $random_unique_int = 2147483648 + mt_rand(-2147482448, 2147483647);

        if ($except !== null){
        if (in_array($random_unique_int, $except))
            return $this->get_unused_id();
        }

        $query = $this->db->where('user_id', $random_unique_int)
        ->get_where('tb_auth_users');

        if ($query->num_rows() > 0)
        {
        $query->free_result();

        return $this->get_unused_id();
        }

        return $random_unique_int;
    }

    public function getSelectedColumnsContract()
    {
        return array(
            'No',
            'Date',
            'Contract No.',
            'Periode',
            'File',
            'Status',
        );
    }

    public function getSearchableColumnsForContract()
    {
        return array(
            'contract_number',
        );
    }

    public function getOrderableColumnsForContract()
    {
        return array(
            null,
            'created_at',
            'contract_number',
            NULL,
            null,
            null,
        );
    }

    private function searchIndexForContract()
    {
        if (!empty($_POST['columns'][1]['search']['value'])) {
            $employee_number = $_POST['columns'][1]['search']['value'];      
            $this->db->where('employee_number', $employee_number);
        }
        $i = 0;

        foreach ($this->getSearchableColumnsForContract() as $item){
            if ($_POST['search']['value']){
                if ($i === 0){
                $this->db->group_start();
                $this->db->like('UPPER('.$item.')', strtoupper($_POST['search']['value']));
                } else {
                $this->db->or_like('UPPER('.$item.')', strtoupper($_POST['search']['value']));
                }

                if (count($this->getSearchableColumnsForContract()) - 1 == $i)
                $this->db->group_end();
            }

            $i++;
        }
    }

    function getIndexForContract($employee_number,$return = 'array')
    {
        $this->db->select('*');
        $this->db->where('employee_number',$employee_number);
        $this->db->from('tb_employee_contracts');

        $this->searchIndexForContract();

        $column_order = $this->getOrderableColumnsForContract();

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

    function countIndexFilteredForContract($employee_number)
    {
        $this->db->from('tb_employee_contracts');
        $this->db->where('employee_number',$employee_number);

        $this->searchIndexForContract();

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function countIndexForContract($employee_number)
    {
        $this->db->from('tb_employee_contracts');
        $this->db->where('employee_number',$employee_number);

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function insert_contract(array $user_data)
    {
        $this->db->trans_begin();

        $this->db->set('status', 'NOT ACTIVE');
        $this->db->where('employee_number', $user_data['employee_number']);
        $this->db->update('tb_employee_contracts');

        $this->db->set($user_data);
        $this->db->insert('tb_employee_contracts');

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function update_contract(array $user_data,$id)
    {
        $this->db->trans_begin();

        $this->db->set($user_data);
        $this->db->where('id',$id);
        $this->db->update('tb_employee_contracts');
        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function findContractById($id)
    {
        $this->db->select(array(
            'tb_employee_contracts.*',
            'tb_master_employees.name'
        ));
        $this->db->where('tb_employee_contracts.id', $id);
        $this->db->join('tb_master_employees', 'tb_master_employees.employee_number = tb_employee_contracts.employee_number');
        $query      = $this->db->get('tb_employee_contracts');
        $row        = $query->unbuffered_row('array');

        return $row;
    }

    public function findBenefitById($id)
    {
        $this->db->where('id', $id);
        $query      = $this->db->get('tb_master_employee_benefits');
        $row        = $query->unbuffered_row('array');       

        return $row;
    }

    public function findContractActive($employee_number)
    {
        $this->db->select(array(
            'tb_employee_contracts.*'
        ));
        $this->db->where('tb_employee_contracts.employee_number', $employee_number);
        $this->db->where('tb_employee_contracts.status', 'ACTIVE');
        $query      = $this->db->get('tb_employee_contracts');
        $row        = $query->unbuffered_row('array');

        return $row;
    }

    public function getSelectedColumnsForBenefit()
    {
        return array(
            'No',
            'Benefit',
            'Periode',
            'Plafond',
            'Used',
            'Balance',
        );
    }

    public function getSearchableColumnsForBenefit()
    {
        return array(
            'tb_master_employee_benefits.employee_benefit',
        );
    }

    public function getOrderableColumnsForBenefit()
    {
        return array(
            null,
            'tb_master_employee_benefits.employee_benefit',
            NULL,
            'tb_employee_has_benefit.amount_plafond',
            'tb_employee_has_benefit.used_amount_plafond',
            'tb_employee_has_benefit.left_amount_plafon',
        );
    }

    private function searchIndexForBenefit()
    {
        if (!empty($_POST['columns'][1]['search']['value'])) {
            $employee_number = $_POST['columns'][1]['search']['value'];      
            $this->db->where('tb_employee_has_benefit.employee_number', $employee_number);
        }

        if (!empty($_POST['columns'][2]['search']['value'])) {
            $employee_contract_id = $_POST['columns'][1]['search']['value'];      
            $this->db->where('tb_employee_has_benefit.employee_contract_id', $employee_contract_id);
        }

        $i = 0;

        foreach ($this->getSearchableColumnsForBenefit() as $item){
            if ($_POST['search']['value']){
                if ($i === 0){
                $this->db->group_start();
                $this->db->like('UPPER('.$item.')', strtoupper($_POST['search']['value']));
                } else {
                $this->db->or_like('UPPER('.$item.')', strtoupper($_POST['search']['value']));
                }

                if (count($this->getSearchableColumnsForBenefit()) - 1 == $i)
                $this->db->group_end();
            }

            $i++;
        }
    }

    function getIndexForBenefit($employee_number,$return = 'array')
    {
        $this->db->select(array(
            'tb_employee_contracts.start_date',
            'tb_employee_contracts.end_date',
            'tb_employee_has_benefit.id',
            'tb_employee_has_benefit.amount_plafond',
            'tb_employee_has_benefit.used_amount_plafond',
            'tb_employee_has_benefit.left_amount_plafond',
            'tb_master_employee_benefits.employee_benefit'
        ));
        $this->db->join('tb_employee_contracts', 'tb_employee_contracts.id = tb_employee_has_benefit.employee_contract_id');
        $this->db->join('tb_master_employee_benefits', 'tb_master_employee_benefits.id = tb_employee_has_benefit.employee_benefit_id');
        $this->db->where('tb_employee_has_benefit.employee_number',$employee_number);
        $this->db->from('tb_employee_has_benefit');

        $this->searchIndexForBenefit();

        $column_order = $this->getOrderableColumnsForBenefit();

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

    function countIndexFilteredForBenefit($employee_number)
    {
        $this->db->select(array(
            'tb_employee_contracts.start_date',
            'tb_employee_contracts.end_date',
            'tb_employee_has_benefit.id',
            'tb_employee_has_benefit.amount_plafond',
            'tb_employee_has_benefit.used_amount_plafond',
            'tb_employee_has_benefit.left_amount_plafond',
            'tb_master_employee_benefits.employee_benefit'
        ));
        $this->db->join('tb_employee_contracts', 'tb_employee_contracts.id = tb_employee_has_benefit.employee_contract_id');
        $this->db->join('tb_master_employee_benefits', 'tb_master_employee_benefits.id = tb_employee_has_benefit.employee_benefit_id');
        $this->db->where('tb_employee_has_benefit.employee_number',$employee_number);
        $this->db->from('tb_employee_has_benefit');

        $this->searchIndexForBenefit();

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function countIndexForBenefit($employee_number)
    {
        $this->db->select(array(
            'tb_employee_contracts.start_date',
            'tb_employee_contracts.end_date',
            'tb_employee_has_benefit.id',
            'tb_employee_has_benefit.amount_plafond',
            'tb_employee_has_benefit.used_amount_plafond',
            'tb_employee_has_benefit.left_amount_plafond',
            'tb_master_employee_benefits.employee_benefit'
        ));
        $this->db->join('tb_employee_contracts', 'tb_employee_contracts.id = tb_employee_has_benefit.employee_contract_id');
        $this->db->join('tb_master_employee_benefits', 'tb_master_employee_benefits.id = tb_employee_has_benefit.employee_benefit_id');
        $this->db->where('tb_employee_has_benefit.employee_number',$employee_number);
        $this->db->from('tb_employee_has_benefit');

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function insert_benefit(array $user_data)
    {
        $this->db->trans_begin();

        $this->db->set($user_data);
        $this->db->insert('tb_employee_has_benefit');

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function isBenefitExist($employee_benefit_id, $employee_contract_id,$employee_benefit_id_exception = NULL, $employee_contract_id_exception = NULL)
    {
        $this->db->from('tb_employee_has_benefit');
        $this->db->where('employee_benefit_id', $employee_benefit_id);
        $this->db->where('employee_contract_id', $employee_contract_id);

        if ($employee_benefit_id_exception !== NULL)
            $this->db->where('employee_benefit_id != ', $employee_benefit_id_exception);

        if ($employee_contract_id_exception !== NULL)
            $this->db->where('employee_contract_id != ', $employee_contract_id_exception);

        $query = $this->db->get();

        return ( $query->num_rows() > 0 ) ? true : false;
    }

    public function findEmployeeBenefitById($id)
    {
        $this->db->select(array(
            'tb_employee_contracts.start_date',
            'tb_employee_contracts.end_date',
            'tb_employee_contracts.contract_number',
            'tb_employee_has_benefit.id',
            'tb_employee_has_benefit.amount_plafond',
            'tb_employee_has_benefit.used_amount_plafond',
            'tb_employee_has_benefit.left_amount_plafond',
            'tb_master_employee_benefits.employee_benefit',
            'tb_master_employees.name',
            'tb_master_employees.position',
            'tb_employee_has_benefit.employee_number',
            'tb_employee_has_benefit.employee_benefit_id',
            'tb_employee_has_benefit.employee_contract_id',
        ));
        $this->db->join('tb_employee_contracts', 'tb_employee_contracts.id = tb_employee_has_benefit.employee_contract_id');
        $this->db->join('tb_master_employee_benefits', 'tb_master_employee_benefits.id = tb_employee_has_benefit.employee_benefit_id');
        $this->db->join('tb_master_employees', 'tb_master_employees.employee_number = tb_employee_has_benefit.employee_number');
        $this->db->where('tb_employee_has_benefit.id', $id);
        $query      = $this->db->get('tb_employee_has_benefit');
        $row        = $query->unbuffered_row('array');
        
        $this->db->select('tb_used_benefits.*');
        $this->db->where('tb_used_benefits.employee_has_benefit_id', $id);
        $queryUsed      = $this->db->get('tb_used_benefits');

        foreach ($queryUsed->result_array() as $key => $value){
            $row['itemUseds'][$key] = $value;
        }

        return $row;
    }

    public function update_benefit(array $user_data,$id)
    {
        $this->db->trans_begin();

        $this->db->set($user_data);
        $this->db->where('id',$id);
        $this->db->update('tb_employee_has_benefit');
        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

}
