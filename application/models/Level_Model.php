<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Level_Model extends MY_Model
{
    protected $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = config_item('module')['level'];
    }

    public function getSelectedColumns()
    {
        return array(
            'No',
            'Level',
            'Code',
            'Notes',
            'Last Update'
        );
    }

    public function getSearchableColumns()
    {
        return array(
            'level',
            'code',
            'notes',
        );
    }

    public function getOrderableColumns()
    {
        return array(
            null,
            'level',
            'code',
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
        $this->db->from('tb_master_levels');

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
        $this->db->from('tb_master_levels');

        $this->searchIndex();

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function countIndex()
    {
        $this->db->from('tb_master_levels');

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function findById($id)
    {
        $this->db->from('tb_master_levels');
        $this->db->where('id',$id);

        $query = $this->db->get();

        return $query->unbuffered_row('array');
    }

    public function findOneBy($criteria)
    {
        $this->db->from('tb_master_levels');
        $this->db->where($criteria);

        $query = $this->db->get();

        return $query->unbuffered_row('array');
    }

    public function insert(array $user_data)
    {
        $this->db->trans_begin();

        $this->db->set($user_data);
        $this->db->insert('tb_master_levels');

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
        $this->db->update('tb_master_levels');

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
        $this->db->delete('tb_master_levels');

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function isDuplicateLevel($level, $exception = NULL)
    {
        $this->db->select('level');
        $this->db->from('tb_master_levels');
        $this->db->where('UPPER(level)', strtoupper($level));

        if ($exception !== NULL)
        $this->db->where('UPPER(level) != ', strtoupper($level));

        $query  = $this->db->get();

        return ($query->num_rows() === 0) ? FALSE : TRUE;
    }

    public function isDuplicateCode($code, $exception = NULL)
    {
        $this->db->select('code');
        $this->db->from('tb_master_levels');
        $this->db->where('UPPER(code)', strtoupper($code));

        if ($exception !== NULL)
        $this->db->where('UPPER(code) != ', strtoupper($exception));

        $query  = $this->db->get();

        return ($query->num_rows() === 0) ? FALSE : TRUE;
    }

    public function import(array $user_data)
    {
        $this->db->trans_begin();

        foreach ($user_data as $key => $data){
            $this->db->set('level', strtoupper($data['level']));
            $this->db->set('code', strtoupper($data['code']));
            $this->db->set('notes', $data['notes']);
            $this->db->set('created_by', config_item('auth_person_name'));
            $this->db->set('updated_by', config_item('auth_person_name'));
            $this->db->insert('tb_master_levels');
        }

        if ($this->db->trans_status() === FALSE)
        return FALSE;

        $this->db->trans_commit();

        return TRUE;
    }
}
