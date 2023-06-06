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

        $data_selected = array(
            'tb_employee_has_benefit.*',
            'tb_master_employee_benefits.employee_benefit'
        );

        $this->db->select($data_selected);
        $this->db->from('tb_employee_has_benefit');
        $this->db->join('tb_master_employee_benefit_items','tb_master_employee_benefit_items.id = tb_employee_has_benefit.employee_benefit_item_id');
        $this->db->join('tb_master_employee_benefits','tb_master_employee_benefit_items.employee_benefit_id = tb_master_employee_benefits.id');
        $this->db->where('tb_employee_has_benefit.employee_number', $id);
        $this->db->where('tb_employee_has_benefit.year', date('Y'));

        $query = $this->db->get();

        foreach ($query->result_array() as $key => $value) {            
            $row['benefit'][$key] = $value;       

            $data_selected = array(
                'tb_used_benefits.*',
            );
    
            $this->db->select($data_selected);
            $this->db->from('tb_used_benefits');
            $this->db->where('tb_used_benefits.employee_has_benefit_id', $value['id']);
            $this->db->where('tb_used_benefits.status', 'AVAILABLE');
    
            $query = $this->db->get();
    
            foreach ($query->result_array() as $key2 => $valueUsed) {            
                $row['benefit'][$key]['used'][$key2] = $valueUsed;
                $link = null;
                if($valueUsed['document_type']=='REIMBURSEMENT'){
                    $link = site_url('reimbursement/print/' . $valueUsed['document_id']);
                }                 
                $row['benefit'][$key]['used'][$key2]['link'] = $link;       
            }
        }

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
        $employee_benefit = getEmployeeBenefitByOccupation($user_data['position']);

        foreach ($employee_benefit as $key => $value) {
            $this->db->set('employee_number',$user_data['employee_number']);
            $this->db->set('year',$value['year']);
            $this->db->set('employee_benefit_item_id',$value['id']);
            $this->db->set('amount_plafond',$value['amount']);
            $this->db->set('left_amount_plafond',$value['amount']);
            $this->db->set('used_amount_plafond',0);
            $this->db->set('created_by', config_item('auth_person_name'));
            $this->db->set('updated_by', config_item('auth_person_name'));
            $this->db->insert('tb_employee_has_benefit');
        }

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
            $this->db->set('employee_number', strtoupper($data['employee_number']));
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
}
