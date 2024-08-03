<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Transportation_Model extends MY_Model
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
            'Transportation',
            'Contact Name',
            'Contact Number',
            'Contact Email',
            'Last Update'
        );
    }

    public function getSearchableColumns()
    {
        return array(
            'transportation',
            'contact_name',
            'contact_number',
            'contact_email',
        );
    }

    public function getOrderableColumns()
    {
        return array(
            null,
            'transportation',
            'contact_name',
            'contact_number',
            'contact_email',
            'updated_at',
        );
    }

    private function searchIndex()
    {

        if (!empty($_POST['columns'][1]['search']['value'])){
            $status = $_POST['columns'][1]['search']['value'];

            $this->db->where('tb_master_transportations.status', $status);
        }else{
            $this->db->where('tb_master_transportations.status', 'AVAILABLE');
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
        $this->db->select('tb_master_transportations.*');
        $this->db->from('tb_master_transportations');

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
        $this->db->from('tb_master_transportations');

        $this->searchIndex();

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function countIndex()
    {
        $this->db->from('tb_master_transportations');

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function findById($id)
    {
        $this->db->from('tb_master_transportations');
        $this->db->where('id',$id);

        $query = $this->db->get();

        return $query->unbuffered_row('array');
    }

    public function findOneBy($criteria)
    {
        $this->db->from('tb_master_transportations');
        $this->db->where($criteria);

        $query = $this->db->get();

        return $query->unbuffered_row('array');
    }

    public function insert(array $user_data)
    {
        $this->db->trans_begin();

        $this->db->set($user_data);
        $this->db->insert('tb_master_transportations');

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
        $this->db->update('tb_master_transportations');

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
        $this->db->update('tb_master_transportations');

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function isDuplicateTransportationName($expense_name, $exception = NULL)
    {
        $this->db->select('transportation');
        $this->db->from('tb_master_transportations');
        $this->db->where('UPPER(transportation)', strtoupper($transportation));

        if ($exception !== NULL)
        $this->db->where('UPPER(transportation) != ', strtoupper($exception));

        $query  = $this->db->get();

        return ($query->num_rows() === 0) ? FALSE : TRUE;
    }

    public function import(array $user_data)
    {
        $this->db->trans_begin();

        foreach ($user_data as $key => $data){
            $this->db->set('transportation', strtoupper($data['transportation']));
            $this->db->set('contact_name', strtoupper($data['contact_name']));
            $this->db->set('contact_number', $data['contact_number']);
            $this->db->set('contact_email', $data['contact_email']);
            $this->db->set('created_by', config_item('auth_person_name'));
            $this->db->set('updated_by', config_item('auth_person_name'));
            $this->db->insert('tb_master_transportations');
        }

        if ($this->db->trans_status() === FALSE)
        return FALSE;

        $this->db->trans_commit();

        return TRUE;
    }
}
