<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Expense_Duty_Model extends MY_Model
{
    protected $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = config_item('module')['master_expense_duty'];
    }

    public function getSelectedColumns()
    {
        return array(
            'No',
            'Expense Name',
            'Account Code',
            'Account Name',
            'Notes',
            'Last Update'
        );
    }

    public function getSearchableColumns()
    {
        return array(
            'expense_name',
            'account_code',
            'tb_master_coa.group',
            'notes',
        );
    }

    public function getOrderableColumns()
    {
        return array(
            null,
            'expense_name',
            'account_code',
            'tb_master_coa.group',
            'notes',
            'updated_at',
        );
    }

    private function searchIndex()
    {

        if (!empty($_POST['columns'][1]['search']['value'])){
            $status = $_POST['columns'][1]['search']['value'];

            $this->db->where('tb_master_expense_duty.status', $status);
        }else{
            $this->db->where('tb_master_expense_duty.status', 'AVAILABLE');
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
        $this->db->select('tb_master_expense_duty.*,tb_master_coa.group');
        $this->db->from('tb_master_expense_duty');
        $this->db->join('tb_master_coa','tb_master_coa.coa = tb_master_expense_duty.account_code','left');

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
        $this->db->from('tb_master_expense_duty');
        $this->db->join('tb_master_coa','tb_master_coa.coa=tb_master_expense_duty.account_code','left');

        $this->searchIndex();

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function countIndex()
    {
        $this->db->from('tb_master_expense_duty');
        $this->db->join('tb_master_coa','tb_master_coa.coa=tb_master_expense_duty.account_code','left');

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function findById($id)
    {
        $this->db->from('tb_master_expense_duty');
        $this->db->where('id',$id);

        $query = $this->db->get();

        return $query->unbuffered_row('array');
    }

    public function findOneBy($criteria)
    {
        $this->db->from('tb_master_expense_duty');
        $this->db->where($criteria);

        $query = $this->db->get();

        return $query->unbuffered_row('array');
    }

    public function insert(array $user_data)
    {
        $this->db->trans_begin();

        $this->db->set($user_data);
        $this->db->insert('tb_master_expense_duty');

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function update(array $user_data, $id)
    {
        $this->db->trans_begin();

        $this->db->set($user_data);
        $this->db->where('id',$id);
        $this->db->update('tb_master_expense_duty');

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function delete()
    {
        $this->db->trans_begin();

        $id = $this->input->post('id');

        $this->db->set('status','NOT AVAILABLE');
        $this->db->where('id', $id);
        $this->db->update('tb_master_expense_duty');

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function isDuplicateExpenseName($expense_name, $exception = NULL)
    {
        $this->db->select('expense_name');
        $this->db->from('tb_master_expense_duty');
        $this->db->where('UPPER(expense_name)', strtoupper($expense_name));

        if ($exception !== NULL)
        $this->db->where('UPPER(expense_name) != ', strtoupper($expense_name));

        $query  = $this->db->get();

        return ($query->num_rows() === 0) ? FALSE : TRUE;
    }

    public function import(array $user_data)
    {
        $this->db->trans_begin();

        foreach ($user_data as $key => $data){
            $this->db->set('expense_name', strtoupper($data['expense_name']));
            $this->db->set('account_code', $data['account_code']);
            $this->db->set('created_by', config_item('auth_person_name'));
            $this->db->set('updated_by', config_item('auth_person_name'));
            $this->db->insert('tb_master_expense_duty');
        }

        if ($this->db->trans_status() === FALSE)
        return FALSE;

        $this->db->trans_commit();

        return TRUE;
    }
}
