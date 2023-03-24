<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tujuan_Perjalanan_Dinas_Model extends MY_Model
{
    protected $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = config_item('module')['tujuan_perjalanan_dinas'];
    }

    public function getSelectedColumns()
    {
        return array(
            'No',
            'Tujuan Perjalanan Dinas',
            'Expense Amount',
            'Notes',
            'Last Update'
        );
    }

    public function getSearchableColumns()
    {
        return array(
            'business_trip_destination',
            'code',
            'notes',
        );
    }

    public function getOrderableColumns()
    {
        return array(
            null,
            'business_trip_destination',
            null,
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
        $this->db->where('tb_master_business_trip_destinations.deleted_at IS NULL', null, false);
        $this->db->from('tb_master_business_trip_destinations');

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
        $this->db->where('tb_master_business_trip_destinations.deleted_at IS NULL', null, false);
        $this->db->from('tb_master_business_trip_destinations');

        $this->searchIndex();

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function countIndex()
    {
        $this->db->where('tb_master_business_trip_destinations.deleted_at IS NULL', null, false);
        $this->db->from('tb_master_business_trip_destinations');

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function findById($id)
    {
        $this->db->where('id', $id);
        $query      = $this->db->get('tb_master_business_trip_destinations');
        $row        = $query->unbuffered_row('array');

        $this->db->select('tb_master_business_trip_destination_items.level');
        $this->db->from('tb_master_business_trip_destination_items');
        $this->db->where('tb_master_business_trip_destination_items.business_trip_purposes_id', $id);
        $this->db->group_by('tb_master_business_trip_destination_items.level');

        $query_level = $this->db->get();

        foreach ($query_level->result_array() as $key => $level) {
            $row['levels'][$key]['level'] = $level['level'];            
        }

        $this->db->select('tb_master_business_trip_destination_items.expense_name');
        $this->db->from('tb_master_business_trip_destination_items');
        $this->db->where('tb_master_business_trip_destination_items.deleted_at IS NULL', null, false);
        $this->db->where('tb_master_business_trip_destination_items.business_trip_purposes_id', $id);
        $this->db->group_by('tb_master_business_trip_destination_items.expense_name');

        $query = $this->db->get();

        foreach ($query->result_array() as $key => $value) {            
            $row['items'][$key] = $value;
            foreach ($row['levels'] as $id => $level) {
                $this->db->select('tb_master_business_trip_destination_items.*');
                $this->db->from('tb_master_business_trip_destination_items');
                $this->db->where('tb_master_business_trip_destination_items.deleted_at IS NULL', null, false);
                $this->db->where('tb_master_business_trip_destination_items.expense_name', $value['expense_name']);
                $this->db->where('tb_master_business_trip_destination_items.level', $level['level']);
                $query_level_amount = $this->db->get();
                $row_level_amount        = $query_level_amount->unbuffered_row('array');
                $row['items'][$key]['levels'][$id]['amount'] = $row_level_amount['amount'];
                $row['items'][$key]['levels'][$id]['day'] = $row_level_amount['day'];
                $row['items'][$key]['levels'][$id]['notes'] = $row_level_amount['notes'];
            }            
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

        $id          = (isset($_SESSION['tujuan_dinas']['id'])) ? $_SESSION['tujuan_dinas']['id'] : NULL;
        if($id!=NULL){
            $this->db->set('business_trip_destination', strtoupper($_SESSION['tujuan_dinas']['business_trip_destination']));
            $this->db->set('notes', $_SESSION['tujuan_dinas']['notes']);
            $this->db->set('updated_by', config_item('auth_person_name'));
            $this->db->where('id', $id);
            $this->db->update('tb_master_business_trip_destinations');
            $business_trip_destination_id = $id;
            
            $this->db->set('deleted_at', date('Y-m-d H:i:s'));
            $this->db->set('deleted_by', config_item('auth_person_name'));
            $this->db->where('business_trip_purposes_id', $id);
            $this->db->update('tb_master_business_trip_destination_items');
        }else{
            $this->db->set('business_trip_destination', strtoupper($_SESSION['tujuan_dinas']['business_trip_destination']));
            $this->db->set('notes', $_SESSION['tujuan_dinas']['notes']);
            $this->db->set('created_by', config_item('auth_person_name'));
            $this->db->set('updated_by', config_item('auth_person_name'));
            $this->db->insert('tb_master_business_trip_destinations');
            $business_trip_destination_id = $this->db->insert_id();
        }        

        foreach ($_SESSION['tujuan_dinas']['items'] as $i => $item) {
            foreach ($_SESSION['tujuan_dinas']['levels'] as $key => $level){
                $amount = $_SESSION['tujuan_dinas']['items'][$i]['levels'][$key]['amount'];
                $day    = $_SESSION['tujuan_dinas']['items'][$i]['levels'][$key]['day'];
                $notes  = $_SESSION['tujuan_dinas']['items'][$i]['levels'][$key]['notes'];
                $this->db->set('business_trip_purposes_id', $business_trip_destination_id);
                $this->db->set('level', $level['level']);
                $this->db->set('expense_name', $item['expense_name']);
                $this->db->set('amount', $amount);
                $this->db->set('day', $day);
                $this->db->set('notes', $notes);
                $this->db->set('fix', $item['fix']);
                $this->db->set('created_by', config_item('auth_person_name'));
                $this->db->set('updated_by', config_item('auth_person_name'));
                $this->db->insert('tb_master_business_trip_destination_items');
            }            
        }

        if ($this->db->trans_status() === FALSE)
            return FALSE;

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
        
        $this->db->set('deleted_at', date('Y-m-d H:i:s'));
        $this->db->set('deleted_by', config_item('auth_person_name'));
        $this->db->where('business_trip_purposes_id', $id);
        $this->db->update('tb_master_business_trip_destination_items');

        $this->db->set('deleted_at', date('Y-m-d H:i:s'));
        $this->db->set('deleted_by', config_item('auth_person_name'));
        $this->db->where('id', $id);
        $this->db->update('tb_master_business_trip_destinations');

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function countExpenseAmount($business_trip_purposes_id)
    {
        $this->db->select_sum('tb_master_business_trip_destination_items.amount', 'expense_amount');
        $this->db->where('tb_master_business_trip_destination_items.deleted_at IS NULL', null, false);
        $this->db->where('tb_master_business_trip_destination_items.business_trip_purposes_id',$business_trip_purposes_id);
        $this->db->from('tb_master_business_trip_destination_items');
        $query  = $this->db->get();
        $row    = $query->unbuffered_row('array');
        
        return $row['expense_amount'];
    }

    public function isDestinationExist($business_trip_destination)
    {
        $this->db->where('tb_master_business_trip_destinations.business_trip_destination', $business_trip_destination);
        $this->db->where('tb_master_business_trip_destinations.deleted_at IS NULL', null, false);
        $query = $this->db->get('tb_master_business_trip_destinations');

        if ($query->num_rows() > 0)
            return true;

        return false;
    }
}
