<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_benefit_model extends MY_Model
{
    protected $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = config_item('module')['employee_benefit'];
    }

    public function getSelectedColumns()
    {
        return array(
            'No',
            'Employee Benefit',
            'Notes',
            'Last Update'
        );
    }

    public function getSearchableColumns()
    {
        return array(
            'employee_benefit',
            'notes',
        );
    }

    public function getOrderableColumns()
    {
        return array(
            null,
            'employee_benefit',
            'notes',
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
        $this->db->where('tb_master_employee_benefits.deleted_at IS NULL', null, false);
        $this->db->from('tb_master_employee_benefits');

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
        $this->db->where('tb_master_employee_benefits.deleted_at IS NULL', null, false);
        $this->db->from('tb_master_employee_benefits');

        $this->searchIndex();

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function countIndex()
    {
        $this->db->where('tb_master_employee_benefits.deleted_at IS NULL', null, false);
        $this->db->from('tb_master_employee_benefits');

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function findById($id)
    {
        $this->db->where('id', $id);
        $query      = $this->db->get('tb_master_employee_benefits');
        $row        = $query->unbuffered_row('array');

        $this->db->select('tb_master_employee_benefit_items.*');
        $this->db->from('tb_master_employee_benefit_items');
        $this->db->where('tb_master_employee_benefit_items.deleted_at IS NULL', null, false);
        $this->db->where('tb_master_employee_benefit_items.employee_benefit_id', $id);

        $query = $this->db->get();

        foreach ($query->result_array() as $key => $value) {            
            $row['levels'][$key] = $value;       
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

    public function insert()
    {
        $this->db->trans_begin();

        $id          = (isset($_SESSION['benefit']['id'])) ? $_SESSION['benefit']['id'] : NULL;
        if($id!=NULL){
            $this->db->set('employee_benefit', trim($_SESSION['benefit']['employee_benefit']));
            $this->db->set('notes', $_SESSION['benefit']['notes']);
            $this->db->set('kode_akun', $_SESSION['benefit']['kode_akun']);
            $this->db->set('spesific_gender', $_SESSION['benefit']['gender']);
            $this->db->set('updated_by', config_item('auth_person_name'));
            $this->db->where('id', $id);
            $this->db->update('tb_master_employee_benefits');
            $employee_benefit_id = $id;
            
            $this->db->set('deleted_at', date('Y-m-d H:i:s'));
            $this->db->set('deleted_by', config_item('auth_person_name'));
            $this->db->where('employee_benefit_id', $id);
            $this->db->update('tb_master_employee_benefit_items');
        }else{
            $this->db->set('employee_benefit', $_SESSION['benefit']['employee_benefit']);
            $this->db->set('kode_akun', $_SESSION['benefit']['kode_akun']);
            $this->db->set('notes', $_SESSION['benefit']['notes']);
            $this->db->set('spesific_gender', $_SESSION['benefit']['gender']);
            $this->db->set('created_by', config_item('auth_person_name'));
            $this->db->set('updated_by', config_item('auth_person_name'));
            $this->db->set('reimbursement', 't');
            $this->db->insert('tb_master_employee_benefits');
            $employee_benefit_id = $this->db->insert_id();
        }        

        foreach ($_SESSION['benefit']['levels'] as $key => $level){
            $amount = $_SESSION['benefit']['items'][$i]['levels'][$key]['amount'];
            $this->db->set('employee_benefit_id', $employee_benefit_id);
            $this->db->set('level', $level['level']);
            $this->db->set('year', date('Y'));
            $this->db->set('amount', $level['amount']);
            $this->db->set('created_by', config_item('auth_person_name'));
            $this->db->set('updated_by', config_item('auth_person_name'));
            $this->db->insert('tb_master_employee_benefit_items');
        }  

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function update($id)
    {
        
    }

    public function delete()
    {
        $this->db->trans_begin();

        $id = $this->input->post('id');
        
        $this->db->set('deleted_at', date('Y-m-d H:i:s'));
        $this->db->set('deleted_by', config_item('auth_person_name'));
        $this->db->where('employee_benefit_id', $id);
        $this->db->update('tb_master_employee_benefit_items');

        $this->db->set('deleted_at', date('Y-m-d H:i:s'));
        $this->db->set('deleted_by', config_item('auth_person_name'));
        $this->db->where('id', $id);
        $this->db->update('tb_master_employee_benefits');

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function isEmployeeBenefitExist($employee_benefit)
    {
        $this->db->where('tb_master_employee_benefit_items.employee_benefit', $employee_benefit);
        $this->db->where('tb_master_employee_benefit_items.deleted_at IS NULL', null, false);
        $query = $this->db->get('tb_master_employee_benefit_items');

        if ($query->num_rows() > 0)
            return true;

        return false;
    }
}
