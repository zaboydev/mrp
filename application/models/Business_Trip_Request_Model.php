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
        $return = array(
            'No',
            'Document Number',
            'Status',
            'Document Date',
            'Department',
            'Person in Charge',
            'Destination',
            'Duration',
            'Date',
            'Purpose',
            'Approval Notes'
        );
        return $return;
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
        if (!empty($_POST['columns'][1]['search']['value'])){
            $search_required_date = $_POST['columns'][1]['search']['value'];
            $range_date  = explode(' ', $search_required_date);

            $this->db->where('tb_business_trip_purposes.date >= ', $range_date[0]);
            $this->db->where('tb_business_trip_purposes.date <= ', $range_date[1]);
        }

        if (!empty($_POST['columns'][2]['search']['value'])){
            $search_status = $_POST['columns'][2]['search']['value'];

            if($search_status!='all'){
                $this->db->where('tb_business_trip_purposes.status', $search_status);         
            }            
        }else{    
            if (config_item('as_head_department')=='yes'){
                $this->db->where('tb_business_trip_purposes.status ', 'WAITING APPROVAL BY HEAD DEPT');
                $this->db->where('tb_business_trip_purposes.head_dept ', config_item('auth_username'));
            }
            elseif (in_array(config_item('auth_username'),config_item('hr_manager'))){                
                $this->db->where('tb_business_trip_purposes.status ', 'WAITING APPROVAL BY HR MANAGER');
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

        $this->send_mail($document_id,'head_dept','request');

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
                'tb_business_trip_purposes.*',
                'tb_master_business_trip_destinations.business_trip_destination'
            );
            $this->db->select($selected);
            $this->db->where('tb_business_trip_purposes.id', $id);
            $this->db->join('tb_master_business_trip_destinations', 'tb_master_business_trip_destinations.id = tb_business_trip_purposes.business_trip_destination_id');
            $query      = $this->db->get('tb_business_trip_purposes');
            $spd        = $query->unbuffered_row('array');

            $cost_center = findCostCenter($spd['annual_cost_center_id']);
            $cost_center_code = $cost_center['cost_center_code'];
            $cost_center_name = $cost_center['cost_center_name'];
            $department_name = $cost_center['department_name'];

            if($spd['status']=='WAITING APPROVAL BY HEAD DEPT' && in_array($department_name,config_item('head_department')) && $spd['head_dept']==config_item('auth_username')){
                $this->db->set('status','WAITING APPROVAL BY HR MANAGER');
                $this->db->set('known_by',config_item('auth_person_name'));
                $this->db->where('id', $id);
                $this->db->update('tb_business_trip_purposes');

                $this->db->set('document_type','SPD');
                $this->db->set('document_number',$spd['document_number']);
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

            }elseif($spd['status']=='WAITING APPROVAL BY HR MANAGER' && in_array(list_user_in_head_department($cost_center['department_id']),config_item('auth_username'))){
                
            }
            $total++;
            $success++;
            $failed--;
        }

        

        if ($this->db->trans_status() === FALSE)
            return $return = ['status'=> FALSE,'total'=>$total,'success'=>$success,'failed'=>$failed];

        $this->send_mail($document_id, 'hr_manager');

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
                'tb_business_trip_purposes.*',
            );
            $this->db->select($selected);
            $this->db->where('tb_business_trip_purposes.id',$doc_id);
            $query      = $this->db->get('tb_business_trip_purposes');
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
                'tb_business_trip_purposes.*',
                'tb_master_business_trip_destinations.business_trip_destination'
            );
            $this->db->select($selected);
            $this->db->join('tb_master_business_trip_destinations', 'tb_master_business_trip_destinations.id = tb_business_trip_purposes.business_trip_destination_id');
            if(is_array($doc_id)){
                $this->db->where_in('tb_business_trip_purposes.id',$doc_id);
            }else{
                $this->db->where('tb_business_trip_purposes.id',$doc_id);
            }
            $query      = $this->db->get('tb_business_trip_purposes');
    
            $item_message = '<tbody>';
            foreach ($query->result_array() as $key => $item) {
                $item_message .= "<tr>";
                $item_message .= "<td>" . print_date($item['date']) . "</td>";
                $item_message .= "<td>" . $item['document_number'] . "</td>";
                $item_message .= "<td>" . $item['person_name'] . "</td>";
                $item_message .= "<td>" . $item['business_trip_destination'] . "</td>";
                $item_message .= "<td>" . print_date($item['start_date'],'d M Y').' s/d '.print_date($item['end_date'],'d M Y') . "</td>";
                $item_message .= "</tr>";
            }
            $item_message .= '</tbody>';

            $this->load->library('email');
            $this->email->set_newline("\r\n");
            $from_email = "bifa.acd@gmail.com";
            $to_email = "aidanurul99@rocketmail.com";
            $message = "<p>Dear ".$keterangan."</p>";
            $message .= "<p>SPD Berikut perlu Persetujuan Anda </p>";
            $message .= "<table class='table'>";
            $message .= "<thead>";
            $message .= "<tr>";
            $message .= "<th>Date</th>";
            $message .= "<th>No. SPD</th>";
            $message .= "<th>Name</th>";
            $message .= "<th>Destination</th>";
            $message .= "<th>Duration</th>";
            $message .= "</tr>";
            $message .= "</thead>";
            $message .= $item_message;
            $message .= "</table>";
            $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
            $message .= "<p>[ <a href='".$this->config->item('url_mrp')."' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
            $message .= "<p>Thanks and regards</p>";
            $this->email->from($from_email, 'Material Resource Planning');
            $this->email->to($recipient);
            $this->email->subject('Permintaan Approval SPD');
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
}
