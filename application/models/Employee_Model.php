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

        return $query->unbuffered_row('array');
    }

    public function insert(array $user_data)
    {
        $this->db->trans_begin();

        $this->db->set($user_data);
        $this->db->insert('tb_master_employees');

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
}
