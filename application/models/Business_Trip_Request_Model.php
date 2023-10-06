<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Business_Trip_Request_Model extends MY_Model
{
    protected $module;
    protected $connection;
    protected $budget_year;
    protected $budget_month;

    public function __construct()
    {
        parent::__construct();

        $this->module = config_item('module')['business_trip_request'];
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
            'document_number',
            'status',
            'date',
            'person_name',
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
            
            if (in_array(config_item('auth_username'),config_item('hr_manager'))){                
                $this->db->where_in('tb_business_trip_purposes.status ', ['WAITING APPROVAL BY HR MANAGER','WAITING APPROVAL BY HEAD DEPT']);
            }
            elseif (config_item('as_head_department')=='yes'){
                $this->db->where('tb_business_trip_purposes.status ', 'WAITING APPROVAL BY HEAD DEPT');
                $this->db->where('tb_business_trip_purposes.head_dept ', config_item('auth_username'));
            }else{
                $this->db->where_not_in('tb_business_trip_purposes.status ', ['REVISED']);
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
        if(is_granted($this->module, 'approval') === FALSE){
            $this->db->where_in('tb_business_trip_purposes.annual_cost_center_id', config_item('auth_annual_cost_centers_id'));
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
        if($row['reference_document']!=null){            
            $reference_document = json_decode($row['reference_document']);
            $row['pr_number'] = $reference_document[2];
            $row['url_expense'] = $reference_document[3];
        }       

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
            $id = $_SESSION['business_trip']['id'];

            $this->db->select('*');
            $this->db->where('id', $id);
            $this->db->from('tb_business_trip_purposes');

            $query = $this->db->get();
            $row   = $query->unbuffered_row('array');
            
            $this->db->set('status','REVISED');
            $this->db->where('id', $_SESSION['business_trip']['id']);
            $this->db->update('tb_business_trip_purposes');

            $this->db->set('document_type','SPD');
            $this->db->set('document_number',$row['document_number']);
            $this->db->set('document_id', $id);
            $this->db->set('action','revised by');
            $this->db->set('date', date('Y-m-d'));
            $this->db->set('username', config_item('auth_username'));
            $this->db->set('person_name', config_item('auth_person_name'));
            $this->db->set('roles', config_item('auth_role'));
            $this->db->set('notes', $_SESSION['business_trip']['approval_notes']);
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
        // $document_id      = (isset($_SESSION['business_trip']['id'])) ? $_SESSION['business_trip']['id'] : NULL;
        $document_edit    = (isset($_SESSION['business_trip']['edit'])) ? $_SESSION['business_trip']['edit'] : NULL;
        $document_number  = sprintf('%06s', $_SESSION['business_trip']['document_number']) . $_SESSION['business_trip']['format_number'];
        $date             = $_SESSION['business_trip']['date'];
        $cost_center_code           = $_SESSION['business_trip']['cost_center_code'];
        $cost_center_name           = $_SESSION['business_trip']['cost_center_name'];
        $annual_cost_center_id      = $_SESSION['business_trip']['annual_cost_center_id'];
        $warehouse                  = $_SESSION['business_trip']['warehouse'];
        $notes                      = $_SESSION['business_trip']['notes'];
        $person_in_charge           = $_SESSION['business_trip']['person_in_charge'];
        $selected_person            = getEmployeeByEmployeeNumber($person_in_charge);
        $person_name                = $selected_person['name'];
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
        $remarks_transport                 = $_SESSION['business_trip']['remarks_transport'];
        $command_by                     = $_SESSION['business_trip']['command_by'];
        $level                          = getLevelByPosition($occupation);

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
        $this->db->set('remarks_transport', $remarks_transport);
        $this->db->set('duration', $duration);
        $this->db->set('start_date', $start_date);
        $this->db->set('end_date', $end_date);
        $this->db->set('head_dept', $head_dept);
        $this->db->set('notes', $notes);
        $this->db->set('command_by', $command_by);
        $this->db->set('paid_amount', 0);
        // if (isset($_SESSION['business_trip']['edit_type']) && $_SESSION['business_trip']['edit_type']=='edit_approve') {
        //     $this->db->set('status','WAITING APPROVAL BY HR MANAGER');
        //     $this->db->set('known_by',config_item('auth_person_name'));
        // }
        $this->db->set('request_by', (isset($_SESSION['business_trip']['edit_type']) && $_SESSION['business_trip']['edit_type']=='edit_approve')?$row['request_by']:config_item('auth_person_name'));
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->insert('tb_business_trip_purposes');
        $document_id = $this->db->insert_id();

        $this->db->set('document_type','SPD');
        $this->db->set('document_number',$document_number);
        $this->db->set('document_id', $document_id);
        $this->db->set('action','requested by');
        $this->db->set('date', $date);
        $this->db->set('username', (isset($_SESSION['business_trip']['edit_type']) && $_SESSION['business_trip']['edit_type']=='edit_approve')?$getSignRequestBy['username']:config_item('auth_username'));
        $this->db->set('person_name', (isset($_SESSION['business_trip']['edit_type']) && $_SESSION['business_trip']['edit_type']=='edit_approve')?$getSignRequestBy['person_name']:config_item('auth_person_name'));
        $this->db->set('roles', (isset($_SESSION['business_trip']['edit_type']) && $_SESSION['business_trip']['edit_type']=='edit_approve')?$getSignRequestBy['roles']:config_item('auth_role'));
        $this->db->set('notes', null);
        $this->db->set('sign', (isset($_SESSION['business_trip']['edit_type']) && $_SESSION['business_trip']['edit_type']=='edit_approve')?$getSignRequestBy['sign']:get_ttd(config_item('auth_person_name')));
        $this->db->set('created_at', date('Y-m-d H:i:s'));
        $this->db->insert('tb_signers');

        if (isset($_SESSION['business_trip']['edit_type']) && $_SESSION['business_trip']['edit_type']=='edit_approve') {
            $this->db->set('document_type','SPD');
            $this->db->set('document_number',$document_number);
            $this->db->set('document_id', $document_id);
            $this->db->set('action','known by');
            $this->db->set('date', date('Y-m-d'));
            $this->db->set('username', config_item('auth_username'));
            $this->db->set('person_name', config_item('auth_person_name'));
            $this->db->set('roles', config_item('auth_role'));
            $this->db->set('notes', $_SESSION['business_trip']['approval_notes']);
            $this->db->set('sign', get_ttd(config_item('auth_person_name')));
            $this->db->set('created_at', date('Y-m-d H:i:s'));
            $this->db->insert('tb_signers');
        }

        $expenses = destination_list_expense($business_trip_destination_id,$level);

        foreach ($expenses as $expense) {
            $qty = ceil($duration/$expense['day']);
            $this->db->set('business_trip_purpose_id', $document_id);
            $this->db->set('business_trip_destination_item_id', $expense['id']);
            $this->db->set('expense_name', $expense['expense_name']);
            $this->db->set('expense_description', NULL);
            $this->db->set('qty', $qty);
            $this->db->set('amount', $expense['amount']);
            $this->db->set('total', $expense['amount']*$qty);
            $this->db->set('real_qty', 0);
            $this->db->set('real_amount', 0);
            $this->db->set('real_total', 0);
            $this->db->set('account_code', $expense['account_code']);
            $this->db->set('created_by', config_item('auth_person_name'));
            $this->db->set('updated_by', config_item('auth_person_name'));
            $this->db->insert('tb_business_trip_purpose_items');
        }

        if(!empty($_SESSION['business_trip']['attachment'])){
            foreach ($_SESSION["business_trip"]["attachment"] as $key) {
                $this->db->set('id_poe', $document_id);
                $this->db->set('id_po', $document_id);
                $this->db->set('file', $key);
                $this->db->set('tipe', 'SPD');
                $this->db->set('tipe_att', 'other');
                $this->db->insert('tb_attachment_poe');
            }
        }

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        if(isset($_SESSION['business_trip']['edit_type']) && $_SESSION['business_trip']['edit_type']=='edit_approve'){
            $this->send_mail($document_id, 'hr_manager');
            $this->send_mail_approval($document_id,config_item('auth_person_name'),'edit_approve');
        }else{
            $this->send_mail($document_id,'head_dept','request');
        }
            

        $this->db->trans_commit();
        return TRUE;
    }

    public function save_hr_approve()
    {
        $this->db->trans_begin();

        $id = $_SESSION['business_trip']['id'];

        $this->db->select('*');
        $this->db->where('id', $id);
        $this->db->from('tb_business_trip_purposes');

        $query = $this->db->get();
        $row   = $query->unbuffered_row('array');

        $this->db->set('status','APPROVED');
        $this->db->set('approved_by',config_item('auth_person_name'));
        $this->db->set('transportation',$this->input->post('transportation'));
        $this->db->set('remarks_transport',$this->input->post('remarks_transport'));
        $this->db->set('type',$this->input->post('spd_type'));
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
        $account_code          = $this->input->post('account_code');

        $this->db->where('business_trip_purpose_id', $id);
        $this->db->delete('tb_business_trip_purpose_items');

        foreach ($expense_name as $key=>$expense_name_item){
            if($expense_name_item!=''){
                $this->db->set('business_trip_purpose_id', $id);
                $this->db->set('business_trip_destination_item_id', NULL);
                $this->db->set('expense_name', $expense_name_item);
                $this->db->set('expense_description', NULL);
                $this->db->set('account_code', $account_code[$key]);
                $this->db->set('qty', $qty[$key]);
                $this->db->set('amount', $amount[$key]);
                $this->db->set('total', ($qty[$key]*$amount[$key]));
                $this->db->set('created_by', config_item('auth_person_name'));
                $this->db->set('updated_by', config_item('auth_person_name'));
                $this->db->insert('tb_business_trip_purpose_items');
            }
            
        }

        if($this->input->post('spd_type')=='expense'){
            $create_next = $this->create_expense($id);
        }else{
            $create_next = $this->create_advance($id);
        }        

        if ($this->db->trans_status() === FALSE || $create_next === FALSE)
            return FALSE;

        // $this->send_mail_approval($id,config_item('auth_person_name'),'approve');            

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
                $this->db->set('status','REJECTED');
                $this->db->set('rejected_by',config_item('auth_person_name'));
                $this->db->where('id', $id);
                $this->db->update('tb_business_trip_purposes');

                $this->db->set('document_type','SPD');
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
            $message .= "<p>SPD Berikut perlu Persetujuan Anda </p>";
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

    public function send_mail_approval($doc_id,$approver,$status)
    {
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
        $this->db->where('tb_signers.document_type', 'SPD');
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
            $message .= "<p>SPD Berikut Telah ".$status_desc." oleh ".$approver."</p>";
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

    public function listAttachment($id)
    {
        $this->db->where('id_poe', $id);
        $this->db->where('tipe', 'SPD');
        $this->db->where(array('deleted_at' => NULL));
        return $this->db->get('tb_attachment_poe')->result_array();
    }

    function add_attachment_to_db($id_poe, $url,$tipe_att='other')
    {
        $this->db->trans_begin();

        $this->db->set('id_poe', $id_poe);
        $this->db->set('id_po', $id_poe);
        $this->db->set('file', $url);
        $this->db->set('tipe', 'SPD');
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

        $this->db->set('deleted_at',date('Y-m-d'));
        $this->db->set('deleted_by', config_item('auth_person_name'));
        $this->db->where('id', $id_att);
        $this->db->update('tb_attachment_poe');

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
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

        //create expense
        $url_spd = site_url('business_trip_request/print_pdf/'.$id);
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
        $this->connection->set('notes', 'expense spd : #'.$data['document_number']);
        $this->connection->set('created_by', config_item('auth_person_name'));
        $this->connection->set('updated_by', config_item('auth_person_name'));
        $this->connection->set('created_at', date('Y-m-d H:i:s'));
        $this->connection->set('updated_at', date('Y-m-d H:i:s'));
        $this->connection->set('with_po', false);
        $this->connection->set('head_dept', $data['head_dept']);
        $this->connection->set('base', config_item('auth_warehouse'));
        if($data['advance_spd']>0){            
            $this->connection->set('advance_account_code', NULL);
            $this->connection->set('advance_nominal', 0);
        }        
        $this->connection->set('revisi', 1);//expense dari SPD tidak bisa direvisi
        $this->connection->set('reference_document', json_encode(['SPD',$id,$data['document_number'],$url_spd]));
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
                $this->connection->set('initial_budget', floatval(0));
                $this->connection->set('mtd_budget', floatval($item['real_total']));
                $this->connection->set('mtd_used_budget', floatval(0));
                $this->connection->set('mtd_used_budget_import', floatval(0));
                $this->connection->set('mtd_prev_month_budget', floatval(0));
                $this->connection->set('mtd_prev_month_used_budget', floatval(0));
                $this->connection->set('mtd_prev_month_used_budget_import', floatval(0));
                $this->connection->set('ytd_budget', floatval($item['real_total']));
                $this->connection->set('ytd_used_budget', floatval(0));
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
                $this->connection->set('amount', $item['total']);
                $this->connection->set('previous_budget', 0);
                $this->connection->set('new_budget', $item['total']);
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
                $this->connection->set('ytd_used_budget', 'ytd_used_budget + ' . $item['total'], FALSE);
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
            $this->connection->set('used_budget', $item['total']);
            $this->connection->set('created_at', date('Y-m-d H:i:s'));
            $this->connection->set('created_by', config_item('auth_person_name'));
            $this->connection->insert('tb_expense_used_budgets');

            //update monthly budget
            $this->connection->set('mtd_used_budget', 'mtd_used_budget + ' . $item['total'], FALSE);
            $this->connection->where('id', $expense_monthly_budget_id);
            $this->connection->update('tb_expense_monthly_budgets');

            $this->connection->set('expense_purchase_requisition_id', $document_id);
            $this->connection->set('expense_monthly_budget_id', $expense_monthly_budget_id);
            $this->connection->set('sort_order', floatval($key));
            // $this->connection->set('sisa', floatval($data['amount']));
            $this->connection->set('amount', floatval($item['total']));
            $this->connection->set('total', floatval($item['total']));
            $this->connection->set('reference_ipc', $data['document_number']);
            $this->connection->insert('tb_expense_purchase_requisition_details');
        }

        $url_expense = site_url('expense_request/print_pdf/'.$document_id);
        $this->db->set('status','OPEN');
        $this->db->set('payment_status', "EXPENSE REQUEST"); 
        $this->db->set('reference_document', json_encode(['EXP',$document_id,$pr_number,$url_expense]));
        $this->db->where('id', $id);
        $this->db->update('tb_business_trip_purposes');          

        if ($this->db->trans_status() === FALSE || $this->connection->trans_status() === FALSE)
            return ['status'=>FALSE,'pr_number'=>$pr_number];

        $this->db->trans_commit();
        $this->connection->trans_commit();
        return ['status'=>TRUE,'pr_number'=>$pr_number];
    }

    //SPD PAYMENT

    public function getSelectedColumnsForPayment()
    {
        $return = array(
            'tb_advance_payments.id'                                                 => NULL,
            'tb_advance_payments.document_number as no_transaksi'                    => 'Transaction Number',
            'tb_advance_payments.tanggal'                                            => 'Date',
            'tb_advance_payments.no_cheque'                                          => 'No Cheque',
            'tb_advance_payments.vendor'                                             => 'Pay TO',
            'tb_advance_payments.currency'                                           => 'Currency',
            'tb_advance_payments.coa_kredit'                                         => 'Account',
            'SUM(tb_advance_payments_details.amount_paid) as amount_request'         => 'Amount IDR',
            'tb_advance_payments.akun_kredit'                                        => 'Amount USD',
            'tb_advance_payments.status'                                             => 'Status',
            'tb_advance_payments.rejected_notes'                                     => 'Attachment',
            'tb_advance_payments.base'                                               => 'Base',
            'tb_advance_payments.notes'                                              => 'Notes',
        );
        if(is_granted($this->data['modules']['expense_closing_payment'], 'approval')){
            $return['tb_advance_payments.approval_notes']  = 'Input Notes';
        }else{
            $return['tb_advance_payments.approval_notes']  = 'Approval/Rejected Notes';
        }



        return $return;
    }

    public function getSearchableColumnsForPayment()
    {
        $return = array(
            // 'tb_purchase_order_items_payments.id',
            'tb_advance_payments.document_number',
            // 'tb_purchase_order_items_payments.tanggal',
            'tb_advance_payments.no_cheque',
            // 'tb_advance_payments.document_number',
            // 'tb_po_item.part_number',
            // 'tb_purchase_order_items_payments.deskripsi',
            'tb_advance_payments.currency',
            'tb_advance_payments.coa_kredit',
            'tb_advance_payments.akun_kredit',
            // 'tb_purchase_order_items_payments.amount_paid',
            'tb_advance_payments.created_by',
            'tb_advance_payments.vendor',
            'tb_advance_payments.status',
            'tb_advance_payments.base'
            // 'tb_purchase_order_items_payments.created_at',
        );

        return $return;
    }

    public function getOrderableColumnsForPayment()
    {
        $return = array(
            NULL,
            'tb_advance_payments.document_number',
            'tb_advance_payments.tanggal',
            'tb_advance_payments.no_cheque',
            // 'tb_po.document_number',
            'tb_advance_payments.vendor',
            // 'tb_po_item.part_number',
            // 'tb_purchase_order_items_payments.deskripsi',
            'tb_advance_payments.currency',          
            'tb_advance_payments.coa_kredit',
            // 'tb_purchase_order_items_payments.amount_paid',
            'tb_advance_payments.base',
            'tb_advance_payments.notes',
            // 'tb_advance_payments.created_at'
        );

        return $return;
    }

    public function getGroupedColumnsForPayment()
    {
        $return = array(
            'tb_advance_payments.id',
            'tb_advance_payments.document_number',
            'tb_advance_payments.tanggal',
            'tb_advance_payments.no_cheque',
            'tb_advance_payments.vendor',
            'tb_advance_payments.currency',
            'tb_advance_payments.status',
            'tb_advance_payments.base',
            'tb_advance_payments.notes',
            'tb_advance_payments.approval_notes',
            'tb_advance_payments.rejected_notes',
            'tb_advance_payments.amount_paid'
        );

        return $return;
    }

    private function searchIndexForPayment()
    {
        if (!empty($_POST['columns'][1]['search']['value'])) {
            $search_received_date = $_POST['columns'][1]['search']['value'];
            $range_received_date  = explode(' ', $search_received_date);

            $this->db->where('tb_advance_payments.tanggal >= ', $range_received_date[0]);
            $this->db->where('tb_advance_payments.tanggal <= ', $range_received_date[1]);
        }

        if (!empty($_POST['columns'][2]['search']['value'])) {
            $vendor = $_POST['columns'][2]['search']['value'];

            $this->db->where('tb_advance_payments.vendor', $vendor);
        }

        if (!empty($_POST['columns'][3]['search']['value'])) {
            $currency = $_POST['columns'][3]['search']['value'];

            if ($currency != 'all') {
                $this->db->where('tb_advance_payments.currency', $currency);
            }
        }

        if (!empty($_POST['columns'][4]['search']['value'])) {
            $status = $_POST['columns'][4]['search']['value'];
            if($status!='all'){
                $this->db->like('tb_advance_payments.status', $status);
            }           
        } else {
            if(is_granted($this->data['modules']['spd_payment'], 'approval')){
                if (config_item('auth_role') == 'FINANCE MANAGER') {
                    $status[] = 'WAITING REVIEW BY FIN MNG';
                }
                if (config_item('auth_role') == 'HEAD OF SCHOOL') {
                    $status[] = 'WAITING APPROVAL BY HOS';
                }
                $this->db->where_in('tb_advance_payments.status', $status);
            }elseif(is_granted($this->data['modules']['spd_payment'], 'review')){
				$status[] = 'APPROVED';
				$this->db->where_in('tb_advance_payments.status', $status);
			}else{
                if (config_item('auth_role') == 'TELLER') {
                    $status[] = 'APPROVED';
                    $this->db->where_in('tb_advance_payments.status', $status);
                }
            }       
            
        }

        if (!empty($_POST['columns'][5]['search']['value'])) {
            $base = $_POST['columns'][5]['search']['value'];
            if($base!='ALL'){
                if($base!='JAKARTA'){
                    $this->db->where('tb_advance_payments.base !=','JAKARTA');
                }elseif($base=='JAKARTA'){
                    $this->db->where('tb_advance_payments.base','JAKARTA');
                }   
            }
                    
        } else {
            if(config_item('auth_role') == 'AP STAFF' || config_item('auth_role') == 'FINANCE MANAGER'){
                $base = config_item('auth_warehouse');
                if($base!='JAKARTA'){
                    $this->db->where('tb_advance_payments.base !=','JAKARTA');
                }elseif($base=='JAKARTA'){
                    $this->db->where('tb_advance_payments.base','JAKARTA');
                }   
            }
            
        }

        if (!empty($_POST['columns'][6]['search']['value'])) {
			$type = $_POST['columns'][6]['search']['value'];
			if($type!='all'){
				$this->db->like('tb_advance_payments.type', $type);
			}			
		}

		if (!empty($_POST['columns'][7]['search']['value'])) {
			$account = $_POST['columns'][7]['search']['value'];
			if($account!='all'){
				$this->db->like('tb_advance_payments.coa_kredit', $account);
			}			
		}

        $i = 0;

        foreach ($this->getSearchableColumnsForPayment() as $item) {
            if ($_POST['search']['value']) {
                $term = strtoupper($_POST['search']['value']);

                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like('UPPER(' . $item . ')', $term);
                } else {
                    $this->db->or_like('UPPER(' . $item . ')', $term);
                }

                if (count($this->getSearchableColumnsForPayment()) - 1 == $i)
                    $this->db->group_end();
            }

            $i++;
        }
    }

    function getIndexForPayment($return = 'array')
    {
        $selected = array_keys($this->getSelectedColumnsForPayment());
        $selected[] = 'tb_advance_payments.amount_paid';
        $this->db->select($selected);
        $this->db->from('tb_advance_payments');
        $this->db->join('tb_advance_payments_details', 'tb_advance_payments.id = tb_advance_payments_details.advance_id');
        $this->db->where('tb_advance_payments.source','SPD');
        $this->db->group_by($this->getGroupedColumnsForPayment());

        $this->searchIndexForPayment();

        $column_order = $this->getOrderableColumnsForPayment();

        if (isset($_POST['order'])) {
            foreach ($_POST['order'] as $key => $order) {
                $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
            }
        } else {
            $this->db->order_by('tb_advance_payments.tanggal', 'desc');
        }

        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);

        $query = $this->db->get();

        if ($return === 'object') {
            return $query->result();
        } elseif ($return === 'json') {
            return json_encode($query->result());
        } else {
            return $query->result_array();
        }
    }

    function countIndexFilteredForPayment()
    {
        $this->db->select(array_keys($this->getSelectedColumnsForPayment()));
        $this->db->from('tb_advance_payments');
        $this->db->join('tb_advance_payments_details', 'tb_advance_payments.id = tb_advance_payments_details.advance_id');
        $this->db->where('tb_advance_payments.source','SPD');
        $this->db->group_by($this->getGroupedColumns());

        $this->searchIndex();

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function countIndexForPayment()
    {
        $this->db->select(array_keys($this->getSelectedColumnsForPayment()));
        $this->db->from('tb_advance_payments');
        $this->db->join('tb_advance_payments_details', 'tb_advance_payments.id = tb_advance_payments_details.advance_id');
        $this->db->where('tb_advance_payments.source','SPD');
        $this->db->group_by($this->getGroupedColumnsForPayment());

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function listSpd()
	{

        $selected = array(
            'sum(tb_business_trip_purpose_items.total) as total',
            'tb_business_trip_purposes.paid_amount',
            'tb_business_trip_purposes.document_number',
            'tb_business_trip_purposes.date',
            'tb_business_trip_purposes.person_name',
            'tb_business_trip_purposes.duration',
            'tb_business_trip_purposes.id',
            'tb_master_business_trip_destinations.business_trip_destination'
        );
        $this->db->select($selected);
        $this->db->join('tb_business_trip_purpose_items', 'tb_business_trip_purpose_items.business_trip_purpose_id = tb_business_trip_purposes.id');
        $this->db->join('tb_master_business_trip_destinations', 'tb_master_business_trip_destinations.id = tb_business_trip_purposes.business_trip_destination_id');
        $this->db->where('tb_business_trip_purposes.status','APPROVED');
        $this->db->where('tb_business_trip_purposes.payment_status','OPEN');
        $this->db->where('tb_business_trip_purposes.type','advance');
        $this->db->group_by(
            array(
                'tb_business_trip_purposes.paid_amount',
                'tb_business_trip_purposes.document_number',
                'tb_business_trip_purposes.date',
                'tb_business_trip_purposes.person_name',
                'tb_business_trip_purposes.duration',
                'tb_business_trip_purposes.id',
                'tb_master_business_trip_destinations.business_trip_destination'
            )
        );
        $query      = $this->db->get('tb_business_trip_purposes');       


        return $query->result_array();
    }

    public function infoSpd($id)
    {
        $selected = array(
            'sum(tb_business_trip_purpose_items.total) as total',
            'tb_business_trip_purposes.paid_amount',
            'tb_business_trip_purposes.document_number',
            'tb_business_trip_purposes.date',
            'tb_business_trip_purposes.person_name',
            'tb_business_trip_purposes.duration',
            'tb_business_trip_purposes.id',
            'tb_master_business_trip_destinations.business_trip_destination'
        );
        $this->db->select($selected);
        $this->db->join('tb_business_trip_purpose_items', 'tb_business_trip_purpose_items.business_trip_purpose_id = tb_business_trip_purposes.id');
        $this->db->join('tb_master_business_trip_destinations', 'tb_master_business_trip_destinations.id = tb_business_trip_purposes.business_trip_destination_id');
        $this->db->where('tb_business_trip_purposes.status','APPROVED');
        $this->db->where('tb_business_trip_purposes.payment_status','OPEN');
        $this->db->group_by(
            array(
                'tb_business_trip_purposes.paid_amount',
                'tb_business_trip_purposes.document_number',
                'tb_business_trip_purposes.date',
                'tb_business_trip_purposes.person_name',
                'tb_business_trip_purposes.duration',
                'tb_business_trip_purposes.id',
                'tb_master_business_trip_destinations.business_trip_destination'
            )
        );
        $this->db->where('tb_business_trip_purposes.id', $id);
        $query      = $this->db->get('tb_business_trip_purposes'); 
        $data       = $query->unbuffered_row('array');        

        return $data;
    }

    public function savePayment()
    {
        $this->db->trans_begin();

        // DELETE OLD DOCUMENT
        
        if (isset($_SESSION['spd_payment']['id'])) {
            $id = $_SESSION['spd_payment']['id'];

            $this->db->select('*');
            $this->db->where('id', $id);
            $this->db->from('tb_advance_payments');

            $query = $this->db->get();
            $oldDocument   = $query->unbuffered_row('array');
            
            $this->db->set('status','REVISED');
            $this->db->where('id', $_SESSION['spd_payment']['id']);
            $this->db->update('tb_advance_payments');

            $this->db->set('document_type','SPD PAYMENT');
            $this->db->set('document_number',$oldDocument['document_number']);
            $this->db->set('document_id', $id);
            $this->db->set('action','revised by');
            $this->db->set('date', date('Y-m-d'));
            $this->db->set('username', config_item('auth_username'));
            $this->db->set('person_name', config_item('auth_person_name'));
            $this->db->set('roles', config_item('auth_role'));
            $this->db->set('notes', $_SESSION['business_trip']['approval_notes']);
            $this->db->set('sign', get_ttd(config_item('auth_person_name')));
            $this->db->set('created_at', date('Y-m-d H:i:s'));
            $this->db->insert('tb_signers');

        }

        // CREATE NEW DOCUMENT
        // $document_id      = (isset($_SESSION['business_trip']['id'])) ? $_SESSION['business_trip']['id'] : NULL;
        $document_edit    = (isset($_SESSION['spd_payment']['edit'])) ? $_SESSION['spd_payment']['edit'] : NULL;
        $document_number  = sprintf('%06s', $_SESSION['spd_payment']['document_number']) . $_SESSION['spd_payment']['format_number'];
        $closing_date           = $_SESSION['spd_payment']['date'];
        $purposed_date          = $_SESSION['spd_payment']['purposed_date'];
        $vendor                 = $_SESSION['spd_payment']['vendor'];
        $closing_by             = config_item('auth_person_name');
        $notes                  = (empty($_SESSION['spd_payment']['notes'])) ? NULL : $_SESSION['spd_payment']['notes'];
        $account                = $_SESSION['spd_payment']['coa_kredit'];
        $type                   = $_SESSION['spd_payment']['type'];

        $base                   = config_item('auth_warehouse');
        $akun_kredit            = getAccountByCode($account);
        $total_purposed_payment = array();
        $currency               = $_SESSION['spd_payment']['currency'];
        $kurs                   = $this->tgl_kurs(date("Y-m-d"));

        // if ($id === NULL) {
            
        // }else{
        //     //utk edit
        //     $advance_id = $id;
        // }

        $this->db->set('document_number', $document_number);
        $this->db->set('source', 'SPD');
        $this->db->set('vendor', strtoupper($vendor));
        $this->db->set('tanggal', $closing_date);
        $this->db->set('purposed_date', $purposed_date);
        $this->db->set('currency', $currency);
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('created_at', date('Y-m-d'));
        $this->db->set('base', $base);
        $this->db->set('notes', $notes);
        $this->db->set('coa_kredit', $account);
        $this->db->set('akun_kredit', $akun_kredit->group);         
        if($type=='CASH'){
            $this->db->set('status','APPROVED');
            $this->db->set('cash_request','OPEN');
            $this->db->set('paid_by', config_item('auth_person_name'));
            $this->db->set('paid_at', date("Y-m-d",strtotime($closing_date)));
        }else{
            // if($base=='JAKARTA'){
            $this->db->set('status','WAITING REVIEW BY FIN MNG');
            // }
        }
        $this->db->set('type',$type);
        $this->db->insert('tb_advance_payments');
        $advance_id = $this->db->insert_id();

        $this->db->set('document_number', $document_number);
        $this->db->set('source', 'ADVANCE');            
        $this->db->insert('tb_po_payment_no_transaksi');

        if($type=='CASH2'){
            $this->db->set('no_jurnal', $document_number);
            $this->db->set('tanggal_jurnal  ', date("Y-m-d",strtotime($closing_date)));
            $this->db->set('source', "SPD");
            $this->db->set('vendor', $vendor);
            $this->db->set('grn_no', $document_number);
            $this->db->set('keterangan', strtoupper("pembayaran advance spd"));
            $this->db->set('created_by',config_item('auth_person_name'));
            $this->db->set('created_at',date('Y-m-d'));
            $this->db->insert('tb_jurnal');
            $id_jurnal = $this->db->insert_id();
        }

        $this->db->set('document_type','SPD PAYMENT');
        $this->db->set('document_number',$document_number);
        $this->db->set('document_id', $advance_id);
        $this->db->set('action','created by');
        $this->db->set('date', date('Y-m-d'));
        $this->db->set('username', config_item('auth_username'));
        $this->db->set('person_name', config_item('auth_person_name'));
        $this->db->set('roles', config_item('auth_role'));
        $this->db->set('notes', NULL);
        $this->db->set('sign', get_ttd(config_item('auth_person_name')));
        $this->db->set('created_at', date('Y-m-d H:i:s'));
        $this->db->insert('tb_signers');

        $spd_ids         = $this->input->post('spd_id');
        $amount         = $this->input->post('amount_paid');
        $account_code   = $this->input->post('account_code');
        $spd_number     = $this->input->post('spd_number');
        $remarks        = $this->input->post('remarks');

        foreach ($spd_ids as $key=>$spd_id){           

            $selectedAccount = getAccountMrpByCode($account_code[$key]);

            $total_purposed_payment[] = $amount[$key];
            
            $this->db->set('advance_id', $advance_id); 
            $this->db->set('spd_id', $spd_id); 
            $this->db->set('spd_number', $spd_number[$key]);
            $this->db->set('amount_paid', $amount[$key]);
            $this->db->set('remarks', $remarks[$key]);
            $this->db->set('account_code', $account_code[$key]);
            $this->db->set('deskripsi', $selectedAccount->coa.' '.$selectedAccount->group);
            $this->db->set('created_by', config_item('auth_person_name'));
            $this->db->set('adj_value', 0);
            $this->db->set('quantity_paid', 1);
            $this->db->set('uang_muka', 0);
            $this->db->insert('tb_advance_payments_details');

            if($spd_id!=NULL){
                
                $this->db->set('payment_status', "PAYMENT PURPOSED");
                

                if($type=='CASH2'){
                    $this->db->set('paid_amount', '"paid_amount" + ' . $amount[$key], false);
                    $this->db->set('payment_status', "PAID");
                }               
                $this->db->where('id', $spd_id);
                $this->db->update('tb_business_trip_purposes');
            }
            

            if($type=='CASH2'){
                if ($currency == 'IDR') {
                    $amount_idr = $amount[$key];
                    $amount_usd = $amount[$key] / $kurs;
                } else {
                    $amount_usd = $amount[$key];
                    $amount_idr = $amount[$key] * $kurs;
                }

                        
                $akun = getAccountMrpByCode($account_code[$key]);

                $this->db->set('id_jurnal', $id_jurnal);
                $this->db->set('jenis_transaksi', strtoupper($akun->group));
                $this->db->set('trs_kredit', 0);
                $this->db->set('trs_debet', $amount_idr);
                $this->db->set('trs_kredit_usd', 0);
                $this->db->set('trs_debet_usd', $amount_usd);
                $this->db->set('kode_rekening', $akun->coa);
                $this->db->set('currency', $currency);
                $this->db->insert('tb_jurnal_detail');
            }
        }


        if($type=='CASH2'){
            $total_amount = array_sum($total_purposed_payment);
            if ($currency == 'IDR') {
                $amount_idr = $total_amount;
                $amount_usd = $total_amount / $kurs;
            } else {
                $amount_usd = $total_amount;
                $amount_idr = $total_amount * $kurs;
            }
            $this->db->set('id_jurnal', $id_jurnal);
            $this->db->set('jenis_transaksi', $akun_kredit->group);
            $this->db->set('trs_debet', 0);
            $this->db->set('trs_kredit', $amount_idr);
            $this->db->set('trs_debet_usd', 0);
            $this->db->set('trs_kredit_usd', $amount_usd);
            $this->db->set('kode_rekening', $account);
            $this->db->set('currency', $currency);
            $this->db->insert('tb_jurnal_detail');
        }        

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        if($type!='CASH'){
            // $this->send_mail($request_payment_id,14,$base);
        }

        return TRUE;
    }

    function tgl_kurs($date)
    {
        // $CI =& get_instance();
        $kurs_dollar = 0;
        $tanggal = $date;

        while ($kurs_dollar == 0) {

            $this->db->select('kurs_dollar');
            $this->db->from('tb_master_kurs_dollar');
            $this->db->where('date', $tanggal);

            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $row    = $query->unbuffered_row();
                $kurs_dollar   = $row->kurs_dollar;
            } else {
                $kurs_dollar = 0;
            }
            $tgl = strtotime('-1 day', strtotime($tanggal));
            $tanggal = date('Y-m-d', $tgl);
        }

        return $kurs_dollar;
    }

    public function findByIdForPayment($id)
    {
        $this->db->select('tb_advance_payments.*');
        $this->db->where('tb_advance_payments.id', $id);
        $this->db->from('tb_advance_payments');
        $query    = $this->db->get();
        $request  = $query->unbuffered_row('array');

        $select = array(
            'tb_advance_payments_details.id',
            'tb_advance_payments_details.document_id',
            'tb_advance_payments_details.amount_paid',
            'sum(tb_business_trip_purpose_items.total) as spd_amount',
            'tb_business_trip_purposes.document_number as spd_number',
            'tb_business_trip_purposes.date as spd_date',
            'tb_business_trip_purposes.person_name as spd_person_incharge',
            'tb_business_trip_purposes.duration',
            'tb_advance_payments_details.account_code',
            'tb_master_business_trip_destinations.business_trip_destination',
            'tb_advance_payments_details.remarks'
        );

        $this->db->select($select);
        $this->db->from('tb_advance_payments_details');
        $this->db->join('tb_business_trip_purposes', 'tb_business_trip_purposes.id = tb_advance_payments_details.document_id');
        $this->db->join('tb_business_trip_purpose_items', 'tb_business_trip_purpose_items.business_trip_purpose_id = tb_business_trip_purposes.id');
        $this->db->join('tb_master_business_trip_destinations', 'tb_master_business_trip_destinations.id = tb_business_trip_purposes.business_trip_destination_id');
        $this->db->where('tb_advance_payments_details.advance_id', $id);
        $this->db->group_by(array(
            'tb_advance_payments_details.id',
            'tb_advance_payments_details.document_id',
            'tb_advance_payments_details.amount_paid',
            'tb_business_trip_purposes.document_number',
            'tb_business_trip_purposes.date',
            'tb_business_trip_purposes.person_name',
            'tb_business_trip_purposes.duration',
            'tb_master_business_trip_destinations.business_trip_destination',
            'tb_advance_payments_details.remarks'
        ));

        $query = $this->db->get();

        foreach ($query->result_array() as $key => $req){
            $request['request'][$key] = $req;            
        }

        if($request['status']=='PAID'){
            $this->db->select('tb_jurnal.*');
            $this->db->where('tb_jurnal.no_jurnal', $request['document_number']);
            $this->db->from('tb_jurnal');
            $queryJurnal    = $this->db->get();
            $jurnal         = $queryJurnal->unbuffered_row('array');

            $this->db->select('tb_jurnal_detail.*');
            $this->db->from('tb_jurnal_detail');
            $this->db->where('tb_jurnal_detail.id_jurnal', $jurnal['id']);

            $queryDetailJurnal = $this->db->get();

            foreach ($queryDetailJurnal->result_array() as $key => $detail){
                $request['jurnalDetail'][$key] = $detail;
            }
        }

        $this->db->select('*');
        $this->db->from('tb_signers');
        $this->db->where('tb_signers.document_number', $request['document_number']);
        $query_signers = $this->db->get();
        foreach ($query_signers->result_array() as $key => $valuesigners) {
            $request['signers'][$valuesigners['action']]['sign'] = $valuesigners['sign'];
            $request['signers'][$valuesigners['action']]['person_name'] = $valuesigners['person_name'];
            $request['signers'][$valuesigners['action']]['date'] = $valuesigners['date'];
            $request['signers'][$valuesigners['action']]['action'] = $valuesigners['action'];
            $request['signers'][$valuesigners['action']]['roles'] = $valuesigners['roles'];
        }

        return $request;
    }

    public function approveForPayment($request_payment_id,$approval_notes)
    {
        $this->db->trans_begin();

        $send_to_vp_finance = array();
        $total = 0;

        foreach ($request_payment_id as $key) {
            $id = $key;
            $this->db->select('tb_advance_payments.*');
            $this->db->from('tb_advance_payments');
            $this->db->where('tb_advance_payments.id',$id);
            $query              = $this->db->get();
            $request_payment    = $query->unbuffered_row('array');
            $currency       = $request_payment['currency'];
            $level          = 0;
            $status         = '';

            if (config_item('auth_role')=='FINANCE MANAGER' && $request_payment['status'] == 'WAITING REVIEW BY FIN MNG') {
                if($request_payment['base']=='JAKARTA'){
                    $this->db->set('status', 'WAITING APPROVAL BY HOS');
                    $status = 'WAITING APPROVAL HOS';
                    $level = 10;
                }else{
                    $this->db->set('status', 'WAITING APPROVAL BY HOS');
                    $status = 'WAITING APPROVAL HOS';
                    $level = 10;
                }           
                $this->db->set('review_by', config_item('auth_person_name'));
                $this->db->set('review_at', date('Y-m-d'));
                $this->db->where('id', $id);
                $this->db->update('tb_advance_payments');

                $this->db->set('document_type','ADVANCE');
                $this->db->set('document_number',$request_payment['document_number']);
                $this->db->set('document_id', $id);
                $this->db->set('action','review by');
                $this->db->set('date', date('Y-m-d'));
                $this->db->set('username', config_item('auth_username'));
                $this->db->set('person_name', config_item('auth_person_name'));
                $this->db->set('roles', config_item('auth_role'));
                $this->db->set('notes', $approval_notes[$x]);
                $this->db->set('sign', get_ttd(config_item('auth_person_name')));
                $this->db->set('created_at', date('Y-m-d H:i:s'));
                $this->db->insert('tb_signers');
            }elseif (config_item('auth_role')=='HEAD OF SCHOOL' && $request_payment['status'] == 'WAITING APPROVAL BY HOS') {
                
                $level = 10;
                $this->db->set('status', 'APPROVED');          
                $this->db->set('approved_by', config_item('auth_person_name'));
                $this->db->set('approved_at', date('Y-m-d'));
                $this->db->where('id', $id);
                $this->db->update('tb_advance_payments');

                $this->db->set('document_type','ADVANCE');
                $this->db->set('document_number',$request_payment['document_number']);
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
            }

            $total++;
        }

        if($level!=0){
            $this->send_email_advance($doc_id,$level);
        }     

        if ($this->db->trans_status() === FALSE)
            return [
                'status'    => FALSE,
                'failed'    => $total,
                'success'   => 0
            ];

        $this->db->trans_commit();        
        
        return [
            'status'    => TRUE,
            'success'   => $total,
            'failed'    => 0
        ];
    }

    public function rejectForPayment($request_payment_id,$approval_notes)
    {
        $this->db->trans_begin();

        $send_to_vp_finance = array();
        $total = 0;

        foreach ($request_payment_id as $key) {
            $id = $key;
            $this->db->select('tb_advance_payments.*');
            $this->db->from('tb_advance_payments');
            $this->db->where('tb_advance_payments.id',$id);
            $query              = $this->db->get();
            $request_payment    = $query->unbuffered_row('array');
            $currency       = $request_payment['currency'];
            $level          = 0;
            $status         = '';

            if (config_item('auth_role')=='FINANCE MANAGER' && $request_payment['status'] == 'WAITING REVIEW BY FIN MNG') {
                if($request_payment['base']=='JAKARTA'){
                    $this->db->set('status', 'REJECTED');
                    $status = 'REJECTED';
                    $level = 0;
                }else{
                    $this->db->set('status', 'REJECTED');
                    $status = 'REJECTED';
                    $level = 0;
                }           
                $this->db->set('rejected_by', config_item('auth_person_name'));
                $this->db->set('rejected_at', date('Y-m-d'));
                $this->db->where('id', $id);
                $this->db->update('tb_advance_payments');

                $this->db->set('document_type','SPD PAYMENT');
                $this->db->set('document_number',$request_payment['document_number']);
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
        }

        

        if ($this->db->trans_status() === FALSE)
            return [
                'status'    => FALSE,
                'failed'    => $total,
                'success'   => 0
            ];
            
        if(!empty($send_to_vp_finance)){
            $this->send_mail($send_to_vp_finance,3);
        }

        $this->db->trans_commit();        
        
        return [
            'status'    => TRUE,
            'success'   => $total,
            'failed'    => 0
        ];
    }

    function save_pembayaran()
    {
        $this->db->trans_begin();
        // $item = $this->input->post('item');
        $account        = $this->input->post('account');
        $vendor         = $this->input->post('vendor');
        $no_cheque      = $this->input->post('no_cheque');
        $tanggal        = $this->input->post('date');
        $amount         = $this->input->post('amount');
        $no_jurnal      = $this->input->post('no_transaksi');
        $currency       = $this->input->post('currency');
        $no_konfirmasi  = $this->input->post('no_konfirmasi');
        $paid_base      = $this->input->post('paid_base');
        $kurs           = $this->tgl_kurs(date("Y-m-d"));
        $tipe           = $this->input->post('tipe');
        $payment_number       = $this->input->post('payment_number');
        $payment_format_number  = $this->input->post('payment_format_number');
        $po_payment_id          = $this->input->post('po_payment_id');
        if ($currency == 'IDR') {
            $amount_idr = $amount;
            $amount_usd = $amount / $kurs;
        } else {
            $amount_usd = $amount;
            $amount_idr = $amount * $kurs;
        }

        $this->db->set('document_number', $payment_number);
        $this->db->set('source', 'ADVANCE');            
        $this->db->insert('tb_po_payment_no_transaksi');


        $this->db->set('no_jurnal', $no_jurnal);
        $this->db->set('tanggal_jurnal  ', date("Y-m-d",strtotime($tanggal)));
        $this->db->set('source', "SPD");
        $this->db->set('vendor', $vendor);
        $this->db->set('grn_no', $no_jurnal);
        $this->db->set('keterangan', strtoupper("spd advance"));
        $this->db->set('created_by',config_item('auth_person_name'));
        $this->db->set('created_at',date('Y-m-d'));
        $this->db->insert('tb_jurnal');
        $id_jurnal = $this->db->insert_id();

        $akun_kredit = getAccountByCode($account);
        $this->db->set('id_jurnal', $id_jurnal);
        $this->db->set('jenis_transaksi', $akun_kredit->group);
        $this->db->set('trs_debet', 0);
        $this->db->set('trs_kredit', $amount_idr);
        $this->db->set('trs_debet_usd', 0);
        $this->db->set('trs_kredit_usd', $amount_usd);
        $this->db->set('kode_rekening', $account);
        $this->db->set('currency', $currency);
        $this->db->insert('tb_jurnal_detail');

        $this->db->set('coa_kredit', $account);
        $this->db->set('no_cheque', $no_cheque);
        $this->db->set('akun_kredit', $akun_kredit->group);
        $this->db->set('no_konfirmasi', $no_konfirmasi);
        $this->db->set('paid_base', $paid_base);
        $this->db->set('vendor', $vendor);
        $this->db->set('payment_number', $payment_number.$payment_format_number);
        $this->db->set('amount_paid', $amount);
        // $this->db->set('status', "PAID");
        $this->db->set('paid_by', config_item('auth_person_name'));
        $this->db->set('paid_at', date("Y-m-d",strtotime($tanggal)));
        $this->db->where('id', $po_payment_id);
        $this->db->update('tb_advance_payments');

        $total_request = array();
        foreach ($_SESSION['bayar']['request'] as $i => $request) {
            $total_request[] = $request['amount_paid'];
            if($request['amount_paid']==$amount){
                // $this->db->set('payment_status', "PAID"); 
            }else{
                $this->db->set('payment_status', "OPEN"); 
            }                 
            $this->db->set('paid_amount', '"paid_amount" + ' . $amount, false);
            $this->db->set('paid_at', date("Y-m-d",strtotime($tanggal)));         
            $this->db->where('id', $request['document_id']);
            $this->db->update('tb_business_trip_purposes');            

            if ($currency == 'IDR') {
                $amount_idr = $amount;
                $amount_usd = $amount / $kurs;
            } else {
                $amount_usd = $amount;
                $amount_idr = $amount * $kurs;
            }

            $akun = getAccountMrpByCode($request['account_code']);

            $this->db->set('id_jurnal', $id_jurnal);
            $this->db->set('jenis_transaksi', strtoupper($akun->group));
            $this->db->set('trs_kredit', ($amount_idr<0)? ($amount_idr*-1):0);
            $this->db->set('trs_debet', ($amount_idr>0)? $amount_idr:0);

            $this->db->set('trs_kredit_usd', ($amount_usd<0)? ($amount_usd*-1):0);
            $this->db->set('trs_debet_usd', ($amount_usd>0)? $amount_usd:0);

            $this->db->set('kode_rekening', $akun->coa);
            $this->db->set('currency', $currency);
            $this->db->insert('tb_jurnal_detail');

            
        }

        if(array_sum($total_request==$amount)){
            $this->db->set('status', "PAID");
            $this->db->where('id', $po_payment_id);
            $this->db->update('tb_advance_payments');
        }else{
            $this->db->set('status', "OPEN");
            $this->db->where('id', $po_payment_id);
            $this->db->update('tb_advance_payments');
        }

        foreach ($_SESSION["bayar"]["attachment"] as $file) {
            $this->db->set('id_poe', $po_payment_id);
            $this->db->set('tipe', "ADV PAYMENT");
            $this->db->set('file', $file);
            $this->db->set('type_att', "payment");
            $this->db->insert('tb_attachment_poe');
        }
        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function create_advance($spd_id)
    {
        $this->db->trans_begin();

        // DELETE OLD DOCUMENT
        $spd = $this->findById($spd_id);

        // CREATE NEW DOCUMENT
        $document_number        = next_advance_document_number();
        $closing_date           = date('Y-m-d');
        $purposed_date          = date('Y-m-d');
        $vendor                 = $spd['person_name'];
        $closing_by             = config_item('auth_person_name');
        $notes                  = "Advance Request SPD #".$spd['document_number'];
        $account                = NULL;
        $type                   = 'CASH';

        $base                   = config_item('auth_warehouse');
        $akun_kredit            = NULL;
        $total_purposed_payment = array();
        $currency               = 'IDR';
        $kurs                   = $this->tgl_kurs(date("Y-m-d"));

        $this->db->set('document_number', $document_number);
        $this->db->set('source', 'SPD');
        $this->db->set('vendor', strtoupper($vendor));
        $this->db->set('tanggal', $closing_date);
        $this->db->set('purposed_date', $purposed_date);
        $this->db->set('currency', $currency);
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('created_at', date('Y-m-d'));
        $this->db->set('base', $base);
        $this->db->set('notes', $notes);
        $this->db->set('coa_kredit', $account);
        if($akun_kredit!=NULL){
            $this->db->set('akun_kredit', $akun_kredit->group);
        }
        $this->db->set('status','WAITING REVIEW BY FIN MNG');
        $this->db->set('type',$type);
        $this->db->insert('tb_advance_payments');
        $advance_payment_id = $this->db->insert_id();

        // $this->db->set('document_number', $document_number);
        // $this->db->set('source', 'SPD');            
        // $this->db->insert('tb_po_payment_no_transaksi');

        $this->db->set('document_type','ADVANCE');
        $this->db->set('document_number',$document_number);
        $this->db->set('document_id', $advance_payment_id);
        $this->db->set('action','created by');
        $this->db->set('date', date('Y-m-d'));
        $this->db->set('username', config_item('auth_username'));
        $this->db->set('person_name', config_item('auth_person_name'));
        $this->db->set('roles', 'HR MANAGER');
        $this->db->set('notes', NULL);
        $this->db->set('sign', get_ttd(config_item('auth_person_name')));
        $this->db->set('created_at', date('Y-m-d H:i:s'));
        $this->db->insert('tb_signers');  

        foreach ($spd['items'] as $item){  
            $total_purposed_payment[] = $item['total'];
        }

        //input detail advance
        $akun_advance_dinas = get_set_up_akun(6);
        $selectedAccount = getAccountMrpByCode($akun_advance_dinas->coa);
        $this->db->set('advance_id', $advance_payment_id); 
        $this->db->set('document_id', $spd['id']); 
        $this->db->set('document_number', $spd['document_number']);
        $this->db->set('amount_paid', array_sum($total_purposed_payment));
        $this->db->set('remarks', NULL);
        $this->db->set('account_code', $akun_advance_dinas->coa);
        $this->db->set('deskripsi', $selectedAccount->coa.' '.$selectedAccount->group);
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('adj_value', 0);
        $this->db->set('quantity_paid', 1);
        $this->db->set('uang_muka', 0);
        $this->db->insert('tb_advance_payments_details');

        $url_advance = site_url('spd_payment/print_pdf/'.$advance_payment_id);
        $this->db->set('status','OPEN');
        $this->db->set('reference_document', json_encode(['ADV',$advance_payment_id,$document_number,$url_advance]));

        $this->db->set('payment_status', "ADVANCE PURPOSED");            
        $this->db->where('id', $spd['id']);
        $this->db->update('tb_business_trip_purposes');     

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        
        $this->send_email_advance($advance_id,14);

        return TRUE;
    }

    public function send_email_advance($doc_id,$role)
    {
        $recipientList = getNotifRecipientByRoleLevel($role);
        $recipient = array();
        foreach ($recipientList as $key) {
          array_push($recipient, $key['email']);
        }

        $levels_and_roles = config_item('levels_and_roles');

        if(!empty($recipient)){
            $selected = array(
                'tb_advance_payments.*',
            );
            $this->db->select($selected);
            if(is_array($doc_id)){
                $this->db->where_in('tb_advance_payments.id',$doc_id);
            }else{
                $this->db->where('tb_advance_payments.id',$doc_id);
            }
            $query      = $this->db->get('tb_advance_payments');
    
            $item_message = '<tbody>';
            foreach ($query->result_array() as $key => $item) {
                $item_message .= "<tr>";
                $item_message .= "<td style='padding: 2px 10px;text-align: left;font-size: 11px;border: 1px solid #999;'>" . print_date($item['date']) . "</td>";
                $item_message .= "<td style='padding: 2px 10px;text-align: left;font-size: 11px;border: 1px solid #999;'>" . $item['document_number'] . "</td>";
                $item_message .= "<td style='padding: 2px 10px;text-align: left;font-size: 11px;border: 1px solid #999;'>" . $item['notes'] . "</td>";
                $item_message .= "</tr>";
            }
            $item_message .= '</tbody>';

            $this->load->library('email');
            $this->email->set_newline("\r\n");
            $from_email = "bifa.acd@gmail.com";
            $to_email = "aidanurul99@rocketmail.com";
            $message = "<p>Dear ".$levels_and_roles[$role]."</p>";
            $message .= "<p>Advance Berikut perlu Persetujuan Anda </p>";
            $message .= "<table style='border-collapse: collapse;padding: 1.2em 0;margin-bottom: 20pxwidth: 100%!important;background: #fff;'>";
            $message .= "<thead>";
            $message .= "<tr>";
            $message .= "<th style='padding: 2px 10px;text-align: left;font-size: 12px;border: 1px solid #999;'>Date</th>";
            $message .= "<th style='padding: 2px 10px;text-align: left;font-size: 12px;border: 1px solid #999;'>No. Adv</th>";
            $message .= "<th style='padding: 2px 10px;text-align: left;font-size: 12px;border: 1px solid #999;'>Description</th>";
            $message .= "</tr>";
            $message .= "</thead>";
            $message .= $item_message;
            $message .= "</table>";
            $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
            $message .= "<p>[ <a href='".$this->config->item('url_mrp')."' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
            $message .= "<p>Thanks and regards</p>";
            $this->email->from($from_email, 'Material Resource Planning');
            $this->email->to($recipient);
            $this->email->subject('Permintaan Approval Advance');
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
