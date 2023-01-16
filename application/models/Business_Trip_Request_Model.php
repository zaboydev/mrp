<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Business_Trip_Request_Model extends MY_Model
{
    protected $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = config_item('module')['business_trip_request'];
    }

    public function getSelectedColumns()
    {
        return array(
            'No',
            'Document Number',
            'Document Date',
            'Department',
            'Person in Charge',
            'Destination',
            'Duration',
            'Date',
            'Notes'
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
        $selected = array(
            'tb_business_trip_purposes.*',
            'tb_master_business_trip_destinations.business_trip_destination'
        );
        $this->db->select($selected);
        $this->db->join('tb_master_business_trip_destinations', 'tb_master_business_trip_destinations.id = tb_business_trip_purposes.business_trip_destination_id');
        $this->db->from('tb_business_trip_purposes');

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
        $this->db->join('tb_master_business_trip_destinations', 'tb_master_business_trip_destinations.id = tb_business_trip_purposes.business_trip_destination_id');
        $this->db->from('tb_business_trip_purposes');

        $this->searchIndex();

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function countIndex()
    {
        $this->db->join('tb_master_business_trip_destinations', 'tb_master_business_trip_destinations.id = tb_business_trip_purposes.business_trip_destination_id');
        $this->db->from('tb_business_trip_purposes');

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function findById($id)
    {
        $selected = array(
            'tb_business_trip_purposes.*',
            'tb_master_business_trip_destinations.business_trip_destination'
        );
        $this->db->select($selected);
        $this->db->where('tb_business_trip_purposes.id', $id);
        $this->db->join('tb_master_business_trip_destinations', 'tb_master_business_trip_destinations.id = tb_business_trip_purposes.business_trip_destination_id');
        $query      = $this->db->get('tb_business_trip_purposes');
        $row        = $query->unbuffered_row('array');

        $cost_center    = findCostCenter($row['annual_cost_center_id']);
        $head_dept      = findUserByUsername($row['head_dept']);
        $row['cost_center_code']    = $cost_center['cost_center_code'];
        $row['cost_center_name']    = $cost_center['cost_center_name'];
        $row['department_name']     = $cost_center['department_name']; 
        $row['department_id']       = $cost_center['department_id'];  
        $row['head_dept_name']       = $head_dept['person_name'];       

        $this->db->select('*');
        $this->db->from('tb_business_trip_purpose_items');
        $this->db->where('tb_business_trip_purpose_items.business_trip_purpose_id', $id);

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
        if (isset($_SESSION['business_trip']['id'])) {

        }

        // CREATE NEW DOCUMENT
        $document_id      = (isset($_SESSION['business_trip']['id'])) ? $_SESSION['business_trip']['id'] : NULL;
        $document_edit    = (isset($_SESSION['business_trip']['edit'])) ? $_SESSION['business_trip']['edit'] : NULL;
        $document_number  = sprintf('%06s', $_SESSION['business_trip']['document_number']) . travel_on_duty_format_number();
        $date             = $_SESSION['business_trip']['date'];
        $cost_center_code           = $_SESSION['business_trip']['cost_center_code'];
        $cost_center_name           = $_SESSION['business_trip']['cost_center_name'];
        $annual_cost_center_id      = $_SESSION['business_trip']['annual_cost_center_id'];
        $warehouse                  = $_SESSION['business_trip']['warehouse'];
        $notes                      = $_SESSION['business_trip']['notes'];
        $person_in_charge           = $_SESSION['business_trip']['person_in_charge'];
        $selected_person            = getUserById($person_in_charge);
        $person_name                = $selected_person['person_name'];
        $department_id              = $_SESSION['business_trip']['department_id'];
        $head_dept                      = $_SESSION['business_trip']['head_dept'];
        $business_trip_destination_id   = $_SESSION['business_trip']['business_trip_destination_id'];
        $duration                       = $_SESSION['business_trip']['duration'];
        $dateline                       = $_SESSION['business_trip']['dateline'];
        $occupation                     = $_SESSION['business_trip']['occupation'];
        $phone_number                   = $_SESSION['business_trip']['phone_number'];
        $id_number                      = $_SESSION['business_trip']['id_number'];
        $start_date                     = $_SESSION['business_trip']['start_date'];
        $end_date                       = $_SESSION['business_trip']['end_date'];
        $from_base                      = $_SESSION['business_trip']['from_base'];
        $transportation                 = $_SESSION['business_trip']['transportation'];

        $this->db->set('annual_cost_center_id', $annual_cost_center_id);
        $this->db->set('warehouse', $warehouse);
        $this->db->set('document_number', $document_number);
        $this->db->set('from_base', $from_base);
        $this->db->set('business_trip_destination_id', $business_trip_destination_id);
        $this->db->set('user_id', $person_in_charge);
        $this->db->set('person_name', $person_name);
        $this->db->set('date', $date);
        $this->db->set('occupation', $occupation);
        $this->db->set('id_number', $id_number);
        $this->db->set('phone_number', $phone_number);
        $this->db->set('transportation', $transportation);
        $this->db->set('duration', $duration);
        $this->db->set('start_date', $start_date);
        $this->db->set('end_date', $end_date);
        $this->db->set('head_dept', $head_dept);
        $this->db->set('notes', $notes);
        $this->db->set('request_by', config_item('auth_person_name'));
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->insert('tb_business_trip_purposes');
        $document_id = $this->db->insert_id();

        $this->db->set('document_type','SPD');
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

        $this->db->where('id', $id);
        $this->db->delete('tb_master_business_trip_destinations');

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
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
}
