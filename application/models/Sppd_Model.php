<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sppd_Model extends MY_Model
{
    protected $module;
    protected $connection;
    protected $budget_year;
    protected $budget_month;

    public function __construct()
    {
        parent::__construct();

        $this->module = config_item('module')['sppd'];
        // $this->data['modules']        = $this->modules;
        $this->connection   = $this->load->database('budgetcontrol', TRUE);
        $this->budget_year  = find_budget_setting('Active Year');
        $this->budget_month = find_budget_setting('Active Month');
    }

    public function getSelectedColumns()
    {
        $return = array(
            'No',
            'Document Number',
            'Status',
            'Document Date',
            'Department',
            'SPD Number',
            'Person in Charge',
            'Destination',
            'Date',
            'Notes',
            'Approval/Rejected Notes'
        );
        return $return;
    }

    public function getSearchableColumns()
    {
        return array(
            'tb_sppd.document_number',
            'tb_sppd.status',
            'tb_business_trip_purposes.document_number',
            'tb_business_trip_purposes.person_name',
            'tb_master_business_trip_destinations.business_trip_destination',
        );
    }

    public function getOrderableColumns()
    {
        return array(
            'tb_sppd.id',
            'tb_sppd.document_number',
            'tb_sppd.status',
            'tb_sppd.date',
            NULL,
            'tb_business_trip_purposes.document_number',
            'tb_business_trip_purposes.person_name',
            'tb_master_business_trip_destinations.business_trip_destination',
            'tb_business_trip_purposes.date',
            NULL,
        );
    }

    private function searchIndex()
    {
        if (!empty($_POST['columns'][1]['search']['value'])){
            $search_required_date = $_POST['columns'][1]['search']['value'];
            $range_date  = explode(' ', $search_required_date);

            $this->db->where('tb_sppd.date >= ', $range_date[0]);
            $this->db->where('tb_sppd.date <= ', $range_date[1]);
        }

        if (!empty($_POST['columns'][2]['search']['value'])){
            $search_status = $_POST['columns'][2]['search']['value'];

            if($search_status!='all'){
                $this->db->where('tb_sppd.status', $search_status);         
            }            
        }else{                
            if (in_array(config_item('auth_username'),config_item('hr_manager'))){                
                $this->db->where_in('tb_sppd.status ', ['WAITING APPROVAL BY HR MANAGER','WAITING APPROVAL BY HEAD DEPT']);
            }
            elseif (config_item('as_head_department')=='yes'){
                $this->db->where('tb_sppd.status ', 'WAITING APPROVAL BY HEAD DEPT');
                $this->db->where('tb_sppd.head_dept ', config_item('auth_username'));
            }
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
        $selected = array(
            'tb_sppd.*',
            'tb_business_trip_purposes.document_number as spd_number',
            'tb_business_trip_purposes.date as spd_date',
            'tb_business_trip_purposes.person_name',
            'tb_master_business_trip_destinations.business_trip_destination'
        );
        $this->db->select($selected);
        $this->db->join('tb_business_trip_purposes', 'tb_business_trip_purposes.id = tb_sppd.spd_id');
        $this->db->join('tb_master_business_trip_destinations', 'tb_master_business_trip_destinations.id = tb_business_trip_purposes.business_trip_destination_id');
        $this->db->from('tb_sppd');
        if(is_granted($this->module, 'approval') === FALSE){
            $this->db->where_in('tb_sppd.annual_cost_center_id', config_item('auth_annual_cost_centers_id'));
        }

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
        if(is_granted($this->module, 'approval') === FALSE){
            $this->db->where_in('tb_business_trip_purposes.annual_cost_center_id', config_item('auth_annual_cost_centers_id'));
        }

        $this->searchIndex();

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function countIndex()
    {
        $this->db->join('tb_master_business_trip_destinations', 'tb_master_business_trip_destinations.id = tb_business_trip_purposes.business_trip_destination_id');
        $this->db->from('tb_business_trip_purposes');
        if(is_granted($this->module, 'approval') === FALSE){
            $this->db->where_in('tb_business_trip_purposes.annual_cost_center_id', config_item('auth_annual_cost_centers_id'));
        }

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function findById($id)
    {
        $selected = array(
            'tb_sppd.*',
            'tb_business_trip_purposes.document_number as spd_number',
            'tb_business_trip_purposes.person_name',
            'tb_business_trip_purposes.occupation',
            'tb_business_trip_purposes.from_base',
            'tb_business_trip_purposes.duration',
            'tb_business_trip_purposes.start_date',
            'tb_business_trip_purposes.end_date',
            'tb_business_trip_purposes.user_id',
            'tb_business_trip_purposes.business_trip_destination_id',
            'tb_master_business_trip_destinations.business_trip_destination'
        );
        $this->db->select($selected);
        $this->db->where('tb_sppd.id', $id);
        $this->db->join('tb_business_trip_purposes', 'tb_business_trip_purposes.id = tb_sppd.spd_id');
        $this->db->join('tb_master_business_trip_destinations', 'tb_master_business_trip_destinations.id = tb_business_trip_purposes.business_trip_destination_id');
        $query      = $this->db->get('tb_sppd');
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
        $this->db->where('tb_business_trip_purpose_items.business_trip_purpose_id', $row['spd_id']);

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
        $this->connection->trans_begin();

        // DELETE OLD DOCUMENT
        
        if (isset($_SESSION['sppd']['id'])) {
            $id = $_SESSION['sppd']['id'];

            $this->db->select('*');
            $this->db->where('id', $id);
            $this->db->from('tb_sppd');

            $query = $this->db->get();
            $row   = $query->unbuffered_row('array');
            
            $this->db->set('status','REVISED');
            $this->db->where('id', $_SESSION['sppd']['id']);
            $this->db->update('tb_sppd');

            $this->db->set('document_type','SPPD');
            $this->db->set('document_number',$row['document_number']);
            $this->db->set('document_id', $id);
            $this->db->set('action','revised by');
            $this->db->set('date', date('Y-m-d'));
            $this->db->set('username', config_item('auth_username'));
            $this->db->set('person_name', config_item('auth_person_name'));
            $this->db->set('roles', config_item('auth_role'));
            $this->db->set('notes', $_SESSION['sppd']['approval_notes']);
            $this->db->set('sign', get_ttd(config_item('auth_person_name')));
            $this->db->set('created_at', date('Y-m-d H:i:s'));
            $this->db->insert('tb_signers');

            $this->db->select('*');
            $this->db->from('tb_signers');
            $this->db->where('tb_signers.document_number', $row['document_number']);
            $this->db->where('tb_signers.action','requested by');
            $querygetSignRequestBy = $this->db->get();
            $getSignRequestBy   = $querygetSignRequestBy->unbuffered_row('array');
        }

        // CREATE NEW DOCUMENT
        // $document_id      = (isset($_SESSION['sppd']['id'])) ? $_SESSION['sppd']['id'] : NULL;
        $document_edit    = (isset($_SESSION['sppd']['edit'])) ? $_SESSION['sppd']['edit'] : NULL;
        $document_number  = sprintf('%06s', $_SESSION['sppd']['document_number']) . $_SESSION['sppd']['format_number'];
        $date             = $_SESSION['sppd']['date'];
        $cost_center_code           = $_SESSION['sppd']['cost_center_code'];
        $cost_center_name           = $_SESSION['sppd']['cost_center_name'];
        $annual_cost_center_id      = $_SESSION['sppd']['annual_cost_center_id'];
        $warehouse                  = $_SESSION['sppd']['warehouse'];
        $notes                      = $_SESSION['sppd']['notes'];
        $person_in_charge           = $_SESSION['sppd']['person_in_charge'];
        $selected_person            = getEmployeeByEmployeeNumber($person_in_charge);
        $person_name                = $selected_person['name'];
        $department_id              = $_SESSION['sppd']['department_id'];
        $head_dept                      = $_SESSION['sppd']['head_dept'];
        $business_trip_destination_id   = $_SESSION['sppd']['business_trip_destination_id'];
        $duration                       = $_SESSION['sppd']['duration'];
        $dateline                       = $_SESSION['sppd']['dateline'];
        $occupation                     = $_SESSION['sppd']['occupation'];
        $phone_number                   = $_SESSION['sppd']['phone_number'];
        $id_number                      = $_SESSION['sppd']['id_number'];
        $start_date                     = $_SESSION['sppd']['start_date'];
        $end_date                       = $_SESSION['sppd']['end_date'];
        $from_base                      = $_SESSION['sppd']['from_base'];
        $transportation                 = $_SESSION['sppd']['transportation'];
        $command_by                     = $_SESSION['sppd']['command_by'];
        $spd_id                         = $_SESSION['sppd']['spd_id'];
        $advance_spd                    = $_SESSION['sppd']['advance'];
        $spd                            = $this->findSpdById($spd_id);
        $level                          = getLevelByPosition($occupation);

        $this->db->set('annual_cost_center_id', $annual_cost_center_id);
        $this->db->set('warehouse', $warehouse);
        $this->db->set('document_number', $document_number);
        $this->db->set('spd_id', $spd_id);
        $this->db->set('spd_number', $spd['document_number']);
        $this->db->set('date', $date);
        $this->db->set('head_dept', $head_dept);
        $this->db->set('advance_spd', $advance_spd);
        $this->db->set('notes', $notes);
        $this->db->set('request_by', (isset($_SESSION['sppd']['edit_type']) && $_SESSION['sppd']['edit_type']=='edit_approve')?$row['request_by']:config_item('auth_person_name'));
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->insert('tb_sppd');
        $document_id = $this->db->insert_id();


        //update status SPD
        $this->db->set('status','CLOSED');
        $this->db->where('id', $spd_id);
        $this->db->update('tb_business_trip_purposes');
        //end

        $this->db->set('document_type','SPPD');
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

        $item_ids       = $this->input->post('item_id');
        $qty            = $this->input->post('qty');
        $amount         = $this->input->post('amount');
        $total          = $this->input->post('total');
        $account_code          = $this->input->post('account_code');
        $total_relisasi = 0;

        foreach ($item_ids as $key=>$item_id) {
            $total_relisasi = $total_relisasi+$total[$key];
            $this->db->set('real_qty', $qty[$key]);
            $this->db->set('real_amount', $amount[$key]);
            $this->db->set('real_total', $total[$key]);
            $this->db->where('id',$item_id);
            $this->db->update('tb_business_trip_purpose_items');
        }

        if(!empty($_SESSION['sppd']['attachment'])){
            foreach ($_SESSION["sppd"]["attachment"] as $key) {
                $this->db->set('id_poe', $document_id);
                $this->db->set('id_po', $document_id);
                $this->db->set('file', $key);
                $this->db->set('tipe', 'SPPD');
                $this->db->set('tipe_att', 'other');
                $this->db->insert('tb_attachment_poe');
            }
        }

        if(!empty($_SESSION['sppd']['attachment_detail'])){
            foreach ($_SESSION["sppd"]["attachment_detail"] as $key) {
                $att = explode("|,", $key);
                $this->db->set('id_poe', $att[1]);
                $this->db->set('id_po', $document_id);
                $this->db->set('file', $att[0]);
                $this->db->set('tipe', $att[2]);
                $this->db->set('tipe_att', 'other');
                $this->db->insert('tb_attachment_poe');
            }
        }

        if ($this->db->trans_status() === FALSE || $this->connection->trans_status() === FALSE)
            return FALSE;

        if(isset($_SESSION['sppd']['edit_type']) && $_SESSION['sppd']['edit_type']=='edit_approve'){
            $this->send_mail($document_id, 'hr_manager');
            $this->send_mail_approval($document_id,config_item('auth_person_name'),'edit_approve');
        }else{
            $this->send_mail($document_id,'head_dept','request');
        }
            

        $this->db->trans_commit();
        $this->connection->trans_commit();
        return TRUE;
    }

    public function save_hr_approve()
    {
        $this->db->trans_begin();

        $id = $_SESSION['sppd']['id'];

        $this->db->select('*');
        $this->db->where('id', $id);
        $this->db->from('tb_business_trip_purposes');

        $query = $this->db->get();
        $row   = $query->unbuffered_row('array');

        $this->db->set('status','APPROVED');
        $this->db->set('approved_by',config_item('auth_person_name'));
        $this->db->where('id', $id);
        $this->db->update('tb_business_trip_purposes');

        
        $this->db->set('document_type','SPD');
        $this->db->set('document_number',$row['document_number']);
        $this->db->set('document_id', $id);
        $this->db->set('action','approved by');
        $this->db->set('date', date('Y-m-d'));
        $this->db->set('username', config_item('auth_username'));
        $this->db->set('person_name', config_item('auth_person_name'));
        $this->db->set('roles', 'HR');
        $this->db->set('notes', $this->input->post('approval_notes'));
        $this->db->set('sign', get_ttd(config_item('auth_person_name')));
        $this->db->set('created_at', date('Y-m-d H:i:s'));
        $this->db->insert('tb_signers');

        $expense_name   = $this->input->post('expense_name');
        $qty            = $this->input->post('qty');
        $amount         = $this->input->post('amount');
        $total          = $this->input->post('total');

        $this->db->where('business_trip_purpose_id', $id);
        $this->db->delete('tb_business_trip_purpose_items');

        foreach ($expense_name as $key=>$expense_name_item){
            if($expense_name_item!=''){
                $this->db->set('business_trip_purpose_id', $id);
                $this->db->set('business_trip_destination_item_id', NULL);
                $this->db->set('expense_name', $expense_name_item);
                $this->db->set('expense_description', NULL);
                $this->db->set('qty', $qty[$key]);
                $this->db->set('amount', $amount[$key]);
                $this->db->set('total', ($qty[$key]*$amount[$key]));
                $this->db->set('created_by', config_item('auth_person_name'));
                $this->db->set('updated_by', config_item('auth_person_name'));
                $this->db->insert('tb_business_trip_purpose_items');
            }
            
        }

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->send_mail_approval($id,config_item('auth_person_name'),'approve');            

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

    public function approve($document_id,$approval_notes)
    {
        $this->db->trans_begin();

        $total      = 0;
        $success    = 0;
        $failed     = sizeof($document_id);
        $x          = 0;

        foreach ($document_id as $id) {
            $selected = array(
                'tb_sppd.*',
                'tb_business_trip_purposes.document_number as spd_number',
                'tb_business_trip_purposes.date as spd_date',
                'tb_business_trip_purposes.person_name',
                'tb_master_business_trip_destinations.business_trip_destination'
            );
            $this->db->select($selected);
            $this->db->join('tb_business_trip_purposes', 'tb_business_trip_purposes.id = tb_sppd.spd_id');
            $this->db->where('tb_sppd.id', $id);
            $this->db->join('tb_master_business_trip_destinations', 'tb_master_business_trip_destinations.id = tb_business_trip_purposes.business_trip_destination_id');
            $query      = $this->db->get('tb_sppd');
            $sppd        = $query->unbuffered_row('array');

            $cost_center = findCostCenter($sppd['annual_cost_center_id']);
            $cost_center_code = $cost_center['cost_center_code'];
            $cost_center_name = $cost_center['cost_center_name'];
            $department_name = $cost_center['department_name'];

            if($sppd['status']=='WAITING APPROVAL BY HEAD DEPT' && in_array($department_name,config_item('head_department')) && $sppd['head_dept']==config_item('auth_username')){
                $this->db->set('status','WAITING APPROVAL BY HR MANAGER');
                $this->db->set('known_by',config_item('auth_person_name'));
                $this->db->where('id', $id);
                $this->db->update('tb_sppd');

                $this->db->set('document_type','SPPD');
                $this->db->set('document_number',$sppd['document_number']);
                $this->db->set('document_id', $id);
                $this->db->set('action','known by');
                $this->db->set('date', date('Y-m-d'));
                $this->db->set('username', config_item('auth_username'));
                $this->db->set('person_name', config_item('auth_person_name'));
                $this->db->set('roles', config_item('auth_role'));
                $this->db->set('notes', $approval_notes[$x]);
                $this->db->set('sign', get_ttd(config_item('auth_person_name')));
                $this->db->set('created_at', date('Y-m-d H:i:s'));
                $this->db->insert('tb_signers');

            }elseif($sppd['status']=='WAITING APPROVAL BY HR MANAGER' && in_array(config_item('auth_username'),list_username_in_head_department(11))){
                $this->db->set('status','APPROVED');
                $this->db->set('approved_by',config_item('auth_person_name'));
                $this->db->where('id', $id);
                $this->db->update('tb_sppd');

                $this->db->set('document_type','SPPD');
                $this->db->set('document_number',$sppd['document_number']);
                $this->db->set('document_id', $id);
                $this->db->set('action','approved by');
                $this->db->set('date', date('Y-m-d'));
                $this->db->set('username', config_item('auth_username'));
                $this->db->set('person_name', config_item('auth_person_name'));
                $this->db->set('roles', config_item('auth_role'));
                $this->db->set('notes', $approval_notes[$x]);
                $this->db->set('sign', get_ttd(config_item('auth_person_name')));
                $this->db->set('created_at', date('Y-m-d H:i:s'));
                $this->db->insert('tb_signers');

                $create_expense = $this->create_expense($id);

                if($create_expense['status'] === FALSE)
                    return $return = ['status'=> FALSE,'total'=>$total,'success'=>$success,'failed'=>$failed];
            }
            $total++;
            $success++;
            $failed--;
        }

        

        if ($this->db->trans_status() === FALSE)
            return $return = ['status'=> FALSE,'total'=>$total,'success'=>$success,'failed'=>$failed];

        $this->send_mail($document_id, 'hr_manager');
        $this->send_mail_approval($document_id,config_item('auth_person_name'),'approve');

        $this->db->trans_commit();
        return $return = ['status'=> TRUE,'total'=>$total,'success'=>$success,'failed'=>$failed];
    }

    public function reject($document_id,$approval_notes)
    {
        $this->db->trans_begin();

        $total      = 0;
        $success    = 0;
        $failed     = sizeof($document_id);
        $x          = 0;

        foreach ($document_id as $id) {
            $selected = array(
                'tb_sppd.*',
                'tb_business_trip_purposes.document_number as spd_number',
                'tb_business_trip_purposes.date as spd_date',
                'tb_business_trip_purposes.person_name',
                'tb_master_business_trip_destinations.business_trip_destination'
            );
            $this->db->select($selected);
            $this->db->join('tb_business_trip_purposes', 'tb_business_trip_purposes.id = tb_sppd.spd_id');
            $this->db->where('tb_sppd.id', $id);
            $this->db->join('tb_master_business_trip_destinations', 'tb_master_business_trip_destinations.id = tb_business_trip_purposes.business_trip_destination_id');
            $query      = $this->db->get('tb_sppd');
            $spd        = $query->unbuffered_row('array');

            $cost_center = findCostCenter($spd['annual_cost_center_id']);
            $cost_center_code = $cost_center['cost_center_code'];
            $cost_center_name = $cost_center['cost_center_name'];
            $department_name = $cost_center['department_name'];

            if($spd['status']=='WAITING APPROVAL BY HEAD DEPT' && in_array($department_name,config_item('head_department')) && $spd['head_dept']==config_item('auth_username')){
                $this->db->set('status','REJECTED');
                $this->db->set('rejected_by',config_item('auth_person_name'));
                $this->db->where('id', $id);
                $this->db->update('tb_sppd');

                $this->db->set('document_type','SPPD');
                $this->db->set('document_number',$spd['document_number']);
                $this->db->set('document_id', $id);
                $this->db->set('action','rejected by');
                $this->db->set('date', date('Y-m-d'));
                $this->db->set('username', config_item('auth_username'));
                $this->db->set('person_name', config_item('auth_person_name'));
                $this->db->set('roles', config_item('auth_role'));
                $this->db->set('notes', $approval_notes[$x]);
                $this->db->set('sign', get_ttd(config_item('auth_person_name')));
                $this->db->set('created_at', date('Y-m-d H:i:s'));
                $this->db->insert('tb_signers');

            }elseif($spd['status']=='WAITING APPROVAL BY HR MANAGER' && in_array(list_user_in_head_department($cost_center['department_id']),config_item('auth_username'))){
                $this->db->set('status','REJECTED');
                $this->db->set('rejected_by',config_item('auth_person_name'));
                $this->db->where('id', $id);
                $this->db->update('tb_sppd');

                $this->db->set('document_type','SPPD');
                $this->db->set('document_number',$spd['document_number']);
                $this->db->set('document_id', $id);
                $this->db->set('action','rejected by');
                $this->db->set('date', date('Y-m-d'));
                $this->db->set('username', config_item('auth_username'));
                $this->db->set('person_name', config_item('auth_person_name'));
                $this->db->set('roles', config_item('auth_role'));
                $this->db->set('notes', $approval_notes[$x]);
                $this->db->set('sign', get_ttd(config_item('auth_person_name')));
                $this->db->set('created_at', date('Y-m-d H:i:s'));
                $this->db->insert('tb_signers');
            }
            $total++;
            $success++;
            $failed--;
        }

        

        if ($this->db->trans_status() === FALSE)
            return $return = ['status'=> FALSE,'total'=>$total,'success'=>$success,'failed'=>$failed];

        // $this->send_mail($document_id, 'hr_manager');
        
        $this->send_mail_approval($document_id,config_item('auth_person_name'),'reject');

        $this->db->trans_commit();
        return $return = ['status'=> TRUE,'total'=>$total,'success'=>$success,'failed'=>$failed];
    }

    public function send_mail($doc_id,$next_approval,$tipe='request')
    {
        if($next_approval=='hr_manager'){
            $recipientList = getNotifRecipientHrManager();
            $keterangan = 'HR Manager';
        }elseif($next_approval=='head_dept'){
            $selected = array(
                'tb_sppd.*',
            );
            $this->db->select($selected);
            $this->db->where('tb_sppd.id',$doc_id);
            $query      = $this->db->get('tb_sppd');
            $row        = $query->unbuffered_row('array');
            $department = getDepartmentByAnnualCostCenterId($row['annual_cost_center_id']);
            $keterangan = "Head Dept : " . $department['department_name'];

            $recipientList = getNotifRecipient_byUsername($row['head_dept']);
        }

        $recipient = array();
        foreach ($recipientList as $key) {
          array_push($recipient, $key['email']);
        }

        if(!empty($recipient)){
            $selected = array(
                'tb_sppd.*',
                'tb_business_trip_purposes.document_number as spd_number',
                'tb_business_trip_purposes.date as spd_date',
                'tb_business_trip_purposes.person_name',
                'tb_master_business_trip_destinations.business_trip_destination'
            );
            $this->db->select($selected);
            $this->db->join('tb_business_trip_purposes', 'tb_business_trip_purposes.id = tb_sppd.spd_id');
            $this->db->join('tb_master_business_trip_destinations', 'tb_master_business_trip_destinations.id = tb_business_trip_purposes.business_trip_destination_id');
            if(is_array($doc_id)){
                $this->db->where_in('tb_sppd.id',$doc_id);
            }else{
                $this->db->where('tb_sppd.id',$doc_id);
            }
            $query      = $this->db->get('tb_sppd');
    
            $item_message = '<tbody>';
            foreach ($query->result_array() as $key => $item) {
                $item_message .= "<tr>";
                $item_message .= "<td style='padding: 2px 10px;text-align: left;font-size: 11px;border: 1px solid #999;'>" . print_date($item['date']) . "</td>";
                $item_message .= "<td style='padding: 2px 10px;text-align: left;font-size: 11px;border: 1px solid #999;'>" . $item['document_number'] . "</td>";
                $item_message .= "<td style='padding: 2px 10px;text-align: left;font-size: 11px;border: 1px solid #999;'>" . $item['person_name'] . "</td>";
                $item_message .= "<td style='padding: 2px 10px;text-align: left;font-size: 11px;border: 1px solid #999;'>" . $item['from_base'] . "</td>";
                $item_message .= "<td style='padding: 2px 10px;text-align: left;font-size: 11px;border: 1px solid #999;'>" . $item['business_trip_destination'] . "</td>";
                $item_message .= "<td style='padding: 2px 10px;text-align: left;font-size: 11px;border: 1px solid #999;'>" . print_date($item['start_date'],'d M Y').' s/d '.print_date($item['end_date'],'d M Y') . "</td>";
                $item_message .= "</tr>";
            }
            $item_message .= '</tbody>';

            $this->load->library('email');
            $this->email->set_newline("\r\n");
            $from_email = "bifa.acd@gmail.com";
            $to_email = "aidanurul99@rocketmail.com";
            $message = "<p>Dear ".$keterangan."</p>";
            $message .= "<p>SPPD Berikut perlu Persetujuan Anda </p>";
            $message .= "<table style='border-collapse: collapse;padding: 1.2em 0;margin-bottom: 20pxwidth: 100%!important;background: #fff;'>";
            $message .= "<thead>";
            $message .= "<tr>";
            $message .= "<th style='padding: 2px 10px;text-align: left;font-size: 12px;border: 1px solid #999;'>Date</th>";
            $message .= "<th style='padding: 2px 10px;text-align: left;font-size: 12px;border: 1px solid #999;'>No. SPPD</th>";
            $message .= "<th style='padding: 2px 10px;text-align: left;font-size: 12px;border: 1px solid #999;'>Name</th>";
            $message .= "<th style='padding: 2px 10px;text-align: left;font-size: 12px;border: 1px solid #999;'>From</th>";
            $message .= "<th style='padding: 2px 10px;text-align: left;font-size: 12px;border: 1px solid #999;'>Destination</th>";
            $message .= "<th style='padding: 2px 10px;text-align: left;font-size: 12px;border: 1px solid #999;'>Duration</th>";
            $message .= "</tr>";
            $message .= "</thead>";
            $message .= $item_message;
            $message .= "</table>";
            $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
            $message .= "<p>[ <a href='".$this->config->item('url_mrp')."' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
            $message .= "<p>Thanks and regards</p>";
            $this->email->from($from_email, 'Material Resource Planning');
            $this->email->to($recipient);
            $this->email->subject('Permintaan Approval SPPD');
            $this->email->message($message);
            
    
            // Send mail 
            if ($this->email->send())
              return true;
            else
              return $this->email->print_debugger();
        }else{
            return true;
        }

        
    }

    public function send_mail_approval($doc_id,$approver,$status)
    {
        $selected = array(
            'tb_sppd.*',
            'tb_business_trip_purposes.document_number as spd_number',
            'tb_business_trip_purposes.date as spd_date',
            'tb_business_trip_purposes.person_name',
            'tb_master_business_trip_destinations.business_trip_destination'
        );
        $this->db->select($selected);
        $this->db->join('tb_business_trip_purposes', 'tb_business_trip_purposes.id = tb_sppd.spd_id');
        $this->db->join('tb_master_business_trip_destinations', 'tb_master_business_trip_destinations.id = tb_business_trip_purposes.business_trip_destination_id');
        if(is_array($doc_id)){
            $this->db->where_in('tb_sppd.id',$doc_id);
        }else{
            $this->db->where('tb_sppd.id',$doc_id);
        }
        $query      = $this->db->get('tb_sppd');

        $item_message = '<tbody>';
        foreach ($query->result_array() as $key => $item) {
            $item_message .= "<tr>";
            $item_message .= "<td style='padding: 2px 10px;text-align: left;font-size: 11px;border: 1px solid #999;'>" . print_date($item['date']) . "</td>";
            $item_message .= "<td style='padding: 2px 10px;text-align: left;font-size: 11px;border: 1px solid #999;'>" . $item['document_number'] . "</td>";
            $item_message .= "<td style='padding: 2px 10px;text-align: left;font-size: 11px;border: 1px solid #999;'>" . $item['person_name'] . "</td>";
            $item_message .= "<td style='padding: 2px 10px;text-align: left;font-size: 11px;border: 1px solid #999;'>" . $item['from_base'] . "</td>";
            $item_message .= "<td style='padding: 2px 10px;text-align: left;font-size: 11px;border: 1px solid #999;'>" . $item['business_trip_destination'] . "</td>";
            $item_message .= "<td style='padding: 2px 10px;text-align: left;font-size: 11px;border: 1px solid #999;'>" . print_date($item['start_date'],'d M Y').' s/d '.print_date($item['end_date'],'d M Y') . "</td>";
            $item_message .= "</tr>";
        }
        $item_message .= '</tbody>';

        $this->db->select('*');
        $this->db->from('tb_signers');
        $this->db->where('tb_signers.document_type', 'SPPD');
        if(is_array($doc_id)){
            $this->db->where_in('tb_signers.document_id',$doc_id);
        }else{
            $this->db->where('tb_signers.document_id',$doc_id);
        }
        $this->db->where('tb_signers.action','requested by');
        $querygetSignRequestBy = $this->db->get();
        $resultquerygetSignRequestBy = $querygetSignRequestBy->result_array();
        $getSignRequestBy = array();

        foreach ($resultquerygetSignRequestBy as $row) {
            $getSignRequestBy[] = $row['username'];
        }

        $recipientList = getNotifRecipient_byUsername($getSignRequestBy);

        $recipient = array();
        foreach ($recipientList as $key) {
          array_push($recipient, $key['email']);
        }

        if($status=='approve'){
            $status_desc = 'Di Setujui';
        }elseif($status=='reject'){
            $status_desc = 'Di Tolak';
        }elseif($status=='edit_approve'){
            $status_desc = 'Di Revisi & Di Setujui';
        }

        if(!empty($recipient)){            

            $this->load->library('email');
            $this->email->set_newline("\r\n");
            $from_email = "bifa.acd@gmail.com";
            $to_email = "aidanurul99@rocketmail.com";
            // $message = "<p>Dear ".$keterangan."</p>";
            $message .= "<p>SPPD Berikut Telah ".$status_desc." oleh ".$approver."</p>";
            $message .= "<table style='border-collapse: collapse;padding: 1.2em 0;margin-bottom: 20pxwidth: 100%!important;background: #fff;'>";
            $message .= "<thead>";
            $message .= "<tr>";
            $message .= "<th style='padding: 2px 10px;text-align: left;font-size: 12px;border: 1px solid #999;'>Date</th>";
            $message .= "<th style='padding: 2px 10px;text-align: left;font-size: 12px;border: 1px solid #999;'>No. SPD</th>";
            $message .= "<th style='padding: 2px 10px;text-align: left;font-size: 12px;border: 1px solid #999;'>Name</th>";
            $message .= "<th style='padding: 2px 10px;text-align: left;font-size: 12px;border: 1px solid #999;'>From</th>";
            $message .= "<th style='padding: 2px 10px;text-align: left;font-size: 12px;border: 1px solid #999;'>Destination</th>";
            $message .= "<th style='padding: 2px 10px;text-align: left;font-size: 12px;border: 1px solid #999;'>Duration</th>";
            $message .= "</tr>";
            $message .= "</thead>";
            $message .= $item_message;
            $message .= "</table>";
            $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
            $message .= "<p>[ <a href='".$this->config->item('url_mrp')."' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
            $message .= "<p>Thanks and regards</p>";
            $this->email->from($from_email, 'Material Resource Planning');
            $this->email->to($recipient);
            $this->email->subject('Permintaan Approval SPPD');
            $this->email->message($message);
            
    
            // Send mail 
            if ($this->email->send())
              return true;
            else
              return $this->email->print_debugger();
        }else{
            return true;
        }

        
    }

    public function listAttachment($id,$tipe='SPD')
    {
        $this->db->where('id_poe', $id);
        $this->db->where('tipe', $tipe);
        $this->db->where(array('deleted_at' => NULL));
        return $this->db->get('tb_attachment_poe')->result_array();
    }

    function add_attachment_to_db($id_poe, $url, $tipe, $tipe_att='other')
    {
        $this->db->trans_begin();

        $this->db->set('id_poe', $id_poe);
        $this->db->set('id_po', $id_poe);
        $this->db->set('file', $url);
        $this->db->set('tipe', $tipe);
        $this->db->set('tipe_att', $tipe_att);
        $this->db->insert('tb_attachment_poe');

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    function delete_attachment_in_db($id_att)
    {
        $this->db->trans_begin();

        // $this->db->select('*');
        // $this->db->where('tb_attachment_poe.id', $id_att);
        // $query      = $this->db->get('tb_attachment_poe');
        // $row        = $query->unbuffered_row('array');

        // $file = FCPATH . $row['file'];
        // if (unlink($file)) {
        //     $this->db->set('deleted_at',date('Y-m-d'));
        //     $this->db->set('deleted_by', config_item('auth_person_name'));
        //     $this->db->where('id', $id_att);
        //     $this->db->update('tb_attachment_poe');
        // }

        $this->db->set('deleted_at',date('Y-m-d'));
        $this->db->set('deleted_by', config_item('auth_person_name'));
        $this->db->where('id', $id_att);
        $this->db->update('tb_attachment_poe');

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function findSpdById($id)
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

        $this->db->select('*');
        $this->db->from('tb_business_trip_purpose_items');
        $this->db->where('tb_business_trip_purpose_items.business_trip_purpose_id', $id);

        $query = $this->db->get();

        foreach ($query->result_array() as $key => $value) {
            $row['items'][$key] = $value;
        }

        return $row;
    }

    public function getExpenseOrderNumber(){
        $this->connection->select_max('order_number', 'last_number');
        $this->connection->from('tb_expense_purchase_requisitions');
        $query        = $this->connection->get();
        if($query->num_rows() > 0){      
            $request      = $query->unbuffered_row('array');
            $last_number  = $request['last_number'];
            $return       = $last_number + 1;
        }else{
            $return = 1;
        }

        return $return;
    }

    public function findCostCenter($annual_cost_center_id){
        $this->connection->select(array('cost_center_code','cost_center_name','department_id'));
        $this->connection->from( 'tb_cost_centers' );
        $this->connection->join('tb_annual_cost_centers','tb_annual_cost_centers.cost_center_id=tb_cost_centers.id');
        $this->connection->where('tb_annual_cost_centers.id', $annual_cost_center_id);

        $query    = $this->connection->get();
        $cost_center = $query->unbuffered_row('array');

        return $cost_center;
    }

    public function getExpenseFormatNumber($cost_center_code){
        $return = '/Exp/'.$cost_center_code.'/'.find_budget_setting('Active Year');

        return $return;
    }

    public function getAccountByAccountCode($account_code){
        $this->connection->select('*');
        $this->connection->from( 'tb_accounts' );
        $this->connection->where('tb_accounts.account_code', $account_code);

        $query    = $this->connection->get();
        $account = $query->unbuffered_row('array');

        return $account;
    }

    public function create_expense($id)
    {
        $this->db->trans_begin();        
        $this->connection->trans_begin();

        // $id = $this->input->post('id');

        $data = $this->findById($id);
        $date = date('Y-m-d');
        $akun_advance_dinas = get_set_up_akun(6);
        $cekSettingApproval = cekSettingApproval('EXPENSE from SPPD');

        $url_spd = site_url('sppd/print_pdf/'.$id);
        $order_number = $this->getExpenseOrderNumber();
        $cost_center = $this->findCostCenter($data['annual_cost_center_id']);
        $format_number = $this->getExpenseFormatNumber($cost_center['cost_center_code']);
        $pr_number = $order_number.$format_number;
        $this->connection->set('annual_cost_center_id', $data['annual_cost_center_id']);
        $this->connection->set('order_number', $order_number);
        $this->connection->set('pr_number', $pr_number);
        $this->connection->set('pr_date', $date);
        $this->connection->set('required_date', $date);
        $this->connection->set('status', 'pending');
        $this->connection->set('notes', 'expense sppd : #'.$data['document_number']);
        $this->connection->set('created_by', config_item('auth_person_name'));
        $this->connection->set('updated_by', config_item('auth_person_name'));
        $this->connection->set('created_at', date('Y-m-d H:i:s'));
        $this->connection->set('updated_at', date('Y-m-d H:i:s'));
        $this->connection->set('with_po', false);
        $this->connection->set('head_dept', $data['head_dept']);
        $this->connection->set('base', config_item('auth_warehouse'));
        if($data['advance_spd']>0){            
            $this->connection->set('advance_account_code', $akun_advance_dinas->coa);
            $this->connection->set('advance_nominal', $data['advance_spd']);
        }
        $this->connection->set('reference_document', json_encode(['SPPD',$id,$data['document_number'],$url_spd]));
        $this->connection->set('revisi', 1);//expense dari SPPD tidak bisa direvisi        
        $this->connection->set('approval_type', ($cekSettingApproval=='FULL APPROVAL')? 'FULL':'NOT FULL');
        $this->connection->insert('tb_expense_purchase_requisitions');

        $document_id = $this->connection->insert_id();

        foreach ($data['items'] as $key => $item) {
            $account = $this->getAccountByAccountCode($item['account_code']);

            // GET BUDGET MONTHLY ID
            $this->connection->from('tb_expense_monthly_budgets');
            $this->connection->where('tb_expense_monthly_budgets.account_id', $account['id']);
            $this->connection->where('tb_expense_monthly_budgets.annual_cost_center_id', $data['annual_cost_center_id']);
            $this->connection->where('tb_expense_monthly_budgets.month_number', $this->budget_month);
            // $this->connection->where('tb_capex_monthly_budgets.year_number', $this->budget_year);

            $query  = $this->connection->get();
            if ($query->num_rows() == 0) {
                //jika budget tidak ada
                // // NEW BUDGET
                $this->connection->set('annual_cost_center_id', $data['annual_cost_center_id']);
                $this->connection->set('account_id', $account['id']);
                $this->connection->set('month_number', $this->budget_month);
                // $this->connection->set('year_number', $this->budget_year);
                $this->connection->set('initial_quantity', floatval(0));
                $this->connection->set('initial_budget', floatval(0));
                $this->connection->set('mtd_quantity', floatval(0));
                $this->connection->set('mtd_budget', floatval($item['real_total']));
                $this->connection->set('mtd_used_quantity', floatval(0));
                $this->connection->set('mtd_used_budget', floatval(0));
                $this->connection->set('mtd_used_quantity_import', floatval(0));
                $this->connection->set('mtd_used_budget_import', floatval(0));
                $this->connection->set('mtd_prev_month_quantity', floatval(0));
                $this->connection->set('mtd_prev_month_budget', floatval(0));
                $this->connection->set('mtd_prev_month_used_quantity', floatval(0));
                $this->connection->set('mtd_prev_month_used_budget', floatval(0));
                $this->connection->set('mtd_prev_month_used_quantity_import', floatval(0));
                $this->connection->set('mtd_prev_month_used_budget_import', floatval(0));
                $this->connection->set('ytd_quantity', floatval(0));
                $this->connection->set('ytd_budget', floatval($item['real_total']));
                $this->connection->set('ytd_used_quantity', floatval(0));
                $this->connection->set('ytd_used_budget', floatval(0));
                $this->connection->set('ytd_used_quantity_import', floatval(0));
                $this->connection->set('ytd_used_budget_import', floatval(0));
                $this->connection->set('created_at', date('Y-m-d'));
                $this->connection->set('created_by', config_item('auth_person_name'));
                $this->connection->set('updated_at', date('Y-m-d'));
                $this->connection->set('updated_by', config_item('auth_person_name'));
                $this->connection->insert('tb_expense_monthly_budgets');

                $expense_monthly_budget_id = $this->connection->insert_id();

                //create expense unbudgeted
                $this->connection->set('annual_cost_center_id', $data['annual_cost_center_id']);
                $this->connection->set('expense_monthly_budget_id', $expense_monthly_budget_id);
                $this->connection->set('year_number', $this->budget_year);
                $this->connection->set('amount', $item['real_total']);
                $this->connection->set('previous_budget', 0);
                $this->connection->set('new_budget', $item[['real_total']]);
                $this->connection->set('created_at', date('Y-m-d'));
                $this->connection->set('created_by', config_item('auth_person_name'));
                $this->connection->set('notes', NULL);
                $this->connection->insert('tb_expense_unbudgeted');
            }else{
                $expense_monthly_budget    = $query->unbuffered_row();
                $expense_monthly_budget_id = $expense_monthly_budget->id;
            }

            $year = $this->budget_year;
            $month = $this->budget_month;

            for ($i = $month; $i < 13; $i++) {
                $this->connection->set('ytd_used_budget', 'ytd_used_budget + ' . $item['real_total'], FALSE);
                $this->connection->where('tb_expense_monthly_budgets.annual_cost_center_id', $data['annual_cost_center_id']);
                $this->connection->where('tb_expense_monthly_budgets.account_id', $account['id']);
                $this->connection->where('tb_expense_monthly_budgets.month_number', $i);
                $this->connection->update('tb_expense_monthly_budgets');
            }

            //insert data on used budget 
            $this->connection->set('expense_monthly_budget_id', $expense_monthly_budget_id);
            $this->connection->set('expense_purchase_requisition_id', $document_id);
            $this->connection->set('pr_number', $pr_number);
            $this->connection->set('cost_center', $cost_center['cost_center_name']);
            $this->connection->set('year_number', $this->budget_year);
            $this->connection->set('month_number', $this->budget_month);
            $this->connection->set('account_name', $account['account_name']);
            $this->connection->set('account_code', $account['account_code']);
            $this->connection->set('used_budget', $item['real_total']);
            $this->connection->set('created_at', date('Y-m-d H:i:s'));
            $this->connection->set('created_by', config_item('auth_person_name'));
            $this->connection->insert('tb_expense_used_budgets');

            //update monthly budget
            $this->connection->set('mtd_used_budget', 'mtd_used_budget + ' . $item['real_total'], FALSE);
            $this->connection->where('id', $expense_monthly_budget_id);
            $this->connection->update('tb_expense_monthly_budgets');

            $this->connection->set('expense_purchase_requisition_id', $document_id);
            $this->connection->set('expense_monthly_budget_id', $expense_monthly_budget_id);
            $this->connection->set('sort_order', floatval($key));
            // $this->connection->set('sisa', floatval($data['amount']));
            $this->connection->set('amount', floatval($item['real_total']));
            $this->connection->set('total', floatval($item['real_total']));
            $this->connection->set('reference_ipc', $data['document_number']);
            $this->connection->insert('tb_expense_purchase_requisition_details');
        }

        $url_expense = site_url('expense_request/print_pdf/'.$document_id);
        $this->db->set('status','EXPENSE REQUEST');
        $this->db->set('reference_document', json_encode(['EXP',$document_id,$pr_number,$url_expense]));
        $this->db->where('id', $id);
        $this->db->update('tb_sppd');        

        if ($this->db->trans_status() === FALSE || $this->connection->trans_status() === FALSE)
            return ['status'=>FALSE,'pr_number'=>$pr_number];

        $this->db->trans_commit();
        $this->connection->trans_commit();
        return ['status'=>TRUE,'pr_number'=>$pr_number];
    }
}
