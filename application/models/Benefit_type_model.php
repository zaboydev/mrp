<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Benefit_type_model extends MY_Model
{
    protected $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = config_item('module')['master_benefit_type'];
    }

    public function getSelectedColumns()
    {
        return array(
            'No',
            'Benefit Type',
            'Notes',
            'Last Update'
        );
    }

    public function getSearchableColumns()
    {
        return array(
            'benefit_type',
            'notes',
        );
    }

    public function getOrderableColumns()
    {
        return array(
            null,
            'benefit_type',
            'notes',
            'updated_at',
        );
    }

    private function searchIndex()
    {

        // if (!empty($_POST['columns'][1]['search']['value'])){
        //     $status = $_POST['columns'][1]['search']['value'];

        //     $this->db->where('tb_master_benefit_type.status', $status);
        // }else{
        //     $this->db->where('tb_master_benefit_type.status', 'AVAILABLE');
        // }

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
            'tb_master_benefit_type.*',
        );
        $this->db->select($selected);
        $this->db->from('tb_master_benefit_type');


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
        $this->db->from('tb_master_benefit_type');
        // $this->db->join('tb_master_coa','tb_master_coa.coa=tb_master_expense_reimbursement.account_code','left');

        $this->searchIndex();

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function countIndex()
    {
        $this->db->from('tb_master_benefit_type');
        // $this->db->join('tb_master_coa','tb_master_coa.coa=tb_master_expense_reimbursement.account_code','left');

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function findById($id)
    {
        $this->db->from('tb_master_benefit_type');
        $this->db->where('id',$id);

        $query = $this->db->get();

        return $query->unbuffered_row('array');
    }

    public function findOneBy($criteria)
    {
        $this->db->from('tb_master_benefit_type');
        $this->db->where($criteria);

        $query = $this->db->get();

        return $query->unbuffered_row('array');
    }

    public function insert(array $user_data)
    {
        $this->db->trans_begin();

        $this->db->set($user_data);
        $this->db->insert('tb_master_benefit_type');

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
        $this->db->update('tb_master_benefit_type');

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
        $this->db->update('tb_master_expense_reimbursement');

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function isDuplicateBenefitType($benefit_type, $exception = NULL)
    {
        $this->db->select('benefit_type');
        $this->db->from('tb_master_benefit_type');
        $this->db->where('UPPER(benefit_type)', strtoupper($benefit_type));

        if ($exception !== NULL)
        $this->db->where('UPPER(benefit_type) != ', strtoupper($benefit_type));

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
            $this->db->insert('tb_master_expense_reimbursement');
        }

        if ($this->db->trans_status() === FALSE)
        return FALSE;

        $this->db->trans_commit();

        return TRUE;
    }
}
