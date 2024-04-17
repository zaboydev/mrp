<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Business_Trip_Request extends MY_Controller
{
    protected $module;
    protected $id_item=0;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['business_trip_request'];
        $this->load->model($this->module['model'], 'model');
        $this->load->helper($this->module['helper']);
        $this->load->library('upload');        
        $this->load->library('email');
        $this->data['module'] = $this->module;
    }

    public function index_data_source()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        if (is_granted($this->module, 'index') === FALSE){
            $return['type'] = 'danger';
            $return['info'] = "You don't have permission to access this page!";
        } else {
            $entities = $this->model->getIndex();
            $data     = array();
            $no       = $_POST['start'];
            $quantity     = array();
            $unit_value   = array();
            $total_value  = array();
            $today = date('Y-m-d');

            foreach ($entities as $row){
                
                $cost_center = findCostCenter($row['annual_cost_center_id']);
                $cost_center_code = $cost_center['cost_center_code'];
                $cost_center_name = $cost_center['cost_center_name'];
                $department_name = $cost_center['department_name'];
                $approval_notes = findApprovalRejectedNotes('SPD',$row['document_number'],'approved');
                $rejected_notes = findApprovalRejectedNotes('SPD',$row['document_number'],'rejected');
                if (viewOrNot($row['status'],$row['head_dept'],$department_name)){
                    $no++;
                    $col = array();
                    if (is_granted($this->module, 'approval')){
                        if($row['status']=='WAITING APPROVAL BY HEAD DEPT' && $row['head_dept']==config_item('auth_username')){
                            $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                        }
                        // elseif($row['status']=='WAITING APPROVAL BY HR MANAGER' && in_array(config_item('auth_username'),list_username_in_head_department(11))){
                        //     $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                        // }
                        else{
                            $col[] = print_number($no);
                        }                    
                    }else{
                        $col[] = print_number($no);
                    }                
                    $col[] = print_string($row['document_number']);
                    if($today>$row['end_date'] && $row['status']!='CLOSED' && in_array($row['payment_status'],['PAID'])){
                        $col[] = "<span class='label-status warning'>". ('Waiting For SPPD')."</span>";
                    }
                    elseif($today>=$row['start_date'] && $today<=$row['end_date'] && in_array($row['payment_status'],['PAID'])){
                        $col[] = "<span>". strtoupper('ON GOING SPD')."</span>";
                    }
                    else{
                        $col[] = "<span>". print_string($row['status'])."</span>";
                    }                    
                    $col[] = print_date($row['date']);
                    $col[] = print_string($cost_center['department_name']);
                    $col[] = print_string($row['person_name']);
                    $col[] = print_string($row['business_trip_destination']);
                    $col[] = print_string($row['duration']);
                    $col[] = print_date($row['start_date'], 'd M Y').' s/d '.print_date($row['end_date'], 'd M Y');
                    $col[] = print_string($row['notes']);
                    if($row['status']=='approved' || $row['status']=='closed' || $row['status']=='OPEN'){
                        
                        $col[] = $approval_notes;
                    }else{
                        if (is_granted($this->module, 'approval') === TRUE && in_array($row['status'],['WAITING APPROVAL BY HEAD DEPT','WAITING APPROVAL BY HR MANAGER'])) {
                            $col[] = '<input type="text" id="note_' . $row['id'] . '" autocomplete="off"/>';
                        }else{
                            $col[] = $rejected_notes;
                        }
                    }
                    
                    $col['DT_RowId'] = 'row_'. $row['id'];
                    $col['DT_RowData']['pkey']  = $row['id'];
                    
                    if ($this->has_role($this->module, 'info')){
                        $col['DT_RowAttr']['onClick']     = '';
                        $col['DT_RowAttr']['data-id']     = $row['id'];
                        $col['DT_RowAttr']['data-target'] = '#data-modal';
                        $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] .'/info/'. $row['id']);
                    }

                    $data[] = $col;
                }         
                
            }

            $result = array(
                "draw"            => $_POST['draw'],
                "recordsTotal"    => $this->model->countIndex(),
                "recordsFiltered" => $no,
                "data"            => $data,
                "total"           => array(
                    
                )
            );
        }

        echo json_encode($result);
    }

    public function index()
    {
        $this->authorized($this->module, 'index');

        $this->data['page']['title']            = $this->module['label'];
        $this->data['grid']['column']           = $this->model->getSelectedColumns();
        $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source');
        $this->data['grid']['fixed_columns']    = 3;
        $this->data['grid']['summary_columns']  = array();

        $this->data['grid']['order_columns']    = array(
            0   => array( 0 => 1,  1 => 'desc' ),
        );

        $this->render_view($this->module['view'] .'/index');
    }

    public function set_doc_number()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        if (empty($_GET['data']))
            $number = travel_on_duty_last_number();
        else
            $number = $_GET['data'];

        $_SESSION['business_trip']['document_number'] = $number;
    }

    public function set_person_in_charge()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['person_in_charge'] = $_GET['data'];
    }

    public function set_occupation()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['occupation'] = $_GET['data'];
    }

    public function set_dateline()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['dateline'] = $_GET['data'];
    }

    public function set_duration()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['duration'] = $_GET['data'];
    }

    public function set_start_date()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['start_date'] = $_GET['data'];
    }

    public function set_end_date()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['end_date'] = $_GET['data'];
    }

    public function set_head_dept()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['head_dept'] = $_GET['data'];
    }

    public function set_id_number()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['id_number'] = $_GET['data'];
    }

    public function set_phone_number()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['phone_number'] = $_GET['data'];
    }

    public function search_person_in_charge()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $entities = available_user(array('person_name', 'username'));

        foreach ($entities as $user){
            $arr_result[] = $user['person_name'];
        }

        echo json_encode($arr_result);
    }

    public function set_from_base()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['from_base'] = $_GET['data'];
    }

    public function set_transportation()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['transportation'] = $_GET['data'];
    }

    public function set_destination()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['business_trip_destination_id'] = $_GET['data'];
    }

    public function set_notes()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['notes'] = $_GET['data'];
    }

    public function set_command_by()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['command_by'] = $_GET['data'];
    }

    public function set_remarks_transport()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['remarks_transport'] = $_GET['data'];
    }

    public function set_approval_notes()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['approval_notes'] = $_GET['data'];
    }

    public function create($annual_cost_center_id = NULL)
    {
        $this->authorized($this->module, 'create');

        if ($annual_cost_center_id !== NULL){
            $annual_cost_center_id = urldecode($annual_cost_center_id);
            $cost_center = findCostCenter($annual_cost_center_id);
            $cost_center_code = $cost_center['cost_center_code'];
            $cost_center_name = $cost_center['cost_center_name'];          
            $department_id    = $cost_center['department_id'];

            $_SESSION['business_trip']['items']                     = array();
            $_SESSION['business_trip']['annual_cost_center_id']     = $annual_cost_center_id;
            $_SESSION['business_trip']['cost_center_id']            = $cost_center_id;
            $_SESSION['business_trip']['cost_center_name']          = $cost_center_name;
            $_SESSION['business_trip']['cost_center_code']          = $cost_center_code;
            $_SESSION['business_trip']['document_number']           = travel_on_duty_last_number();
            $_SESSION['business_trip']['format_number']             = travel_on_duty_format_number();
            $_SESSION['business_trip']['date']                      = date('Y-m-d');
            $_SESSION['business_trip']['created_by']                = config_item('auth_person_name');
            $_SESSION['business_trip']['warehouse']                 = config_item('auth_warehouse');
            $_SESSION['business_trip']['notes']                     = NULL;
            $_SESSION['business_trip']['person_in_charge']          = NULL;
            $_SESSION['business_trip']['department_id']             = $department_id;
            $_SESSION['business_trip']['head_dept']                 = NULL;
            $_SESSION['business_trip']['business_trip_destination_id']  = NULL;
            $_SESSION['business_trip']['duration']                      = NULL;
            $_SESSION['business_trip']['dateline']                      = NULL;
            $_SESSION['business_trip']['occupation']                    = NULL;
            $_SESSION['business_trip']['phone_number']                  = NULL;
            $_SESSION['business_trip']['id_number']                     = NULL;
            $_SESSION['business_trip']['start_date']                    = NULL;
            $_SESSION['business_trip']['end_date']                      = NULL;
            $_SESSION['business_trip']['from_base']                     = NULL;
            $_SESSION['business_trip']['transportation']                = NULL;
            $_SESSION['business_trip']['remarks_transport']             = NULL;
            $_SESSION['business_trip']['command_by']                    = NULL;
            $_SESSION['business_trip']['attachment']                    = array();

            redirect($this->module['route'] .'/create');
        }

        if (!isset($_SESSION['business_trip']))
          redirect($this->module['route']);

        $this->data['page']['content']    = $this->module['view'] .'/create';
        $this->data['page']['offcanvas']  = $this->module['view'] .'/create_offcanvas_add_item';

        $this->render_view($this->module['view'] .'/create');
    }

    public function info($id)
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        if (is_granted($this->module, 'info') === FALSE){
            $return['type'] = 'denied';
            $return['info'] = "You don't have permission to access this data. You may need to login again.";
        } else {
            $entity = $this->model->findById($id);

            $this->data['entity'] = $entity;

            $return['type'] = 'success';
            $return['info'] = $this->load->view($this->module['view'] .'/info', $this->data, TRUE);
        }

        echo json_encode($return);
    }

    public function print_pdf($id)
    {
        $this->authorized($this->module, 'print');

        $entity = $this->model->findById($id);

        $this->data['entity']           = $entity;
        $this->data['page']['title']    = strtoupper($this->module['label']);
        $this->data['page']['content']  = $this->module['view'] .'/print_pdf';

        $html = $this->load->view($this->pdf_theme, $this->data, true);

        $pdfFilePath = str_replace('/', '-', $entity['document_number']) .".pdf";

        $this->load->library('m_pdf');

        $pdf = $this->m_pdf->load(null, 'A4-P');
        $pdf->WriteHTML($html);
        $pdf->Output($pdfFilePath, "I");
    }

    public function edit($id,$type="edit")
    {
        $this->authorized($this->module, 'create');

        $entity = $this->model->findById($id);

        $document_number    = sprintf('%06s', substr($entity['document_number'], 0, 6));
        $format_number      = substr($entity['document_number'], 6, 21);
        $revisi             = get_count_revisi($document_number.$format_number,'SPD');

        if (isset($_SESSION['business_trip']) === FALSE){
            $cost_center = findCostCenter($entity['annual_cost_center_id']);
            $cost_center_code = $cost_center['cost_center_code'];
            $cost_center_name = $cost_center['cost_center_name'];          
            $department_id    = $cost_center['department_id'];

            $_SESSION['business_trip']['annual_cost_center_id']     = $annual_cost_center_id;
            $_SESSION['business_trip']['cost_center_id']            = $cost_center_id;
            $_SESSION['business_trip']['cost_center_name']          = $cost_center_name;
            $_SESSION['business_trip']['cost_center_code']          = $cost_center_code;
            $_SESSION['business_trip']                              = $entity;
            $_SESSION['business_trip']['id']                        = $id;
            $_SESSION['business_trip']['edit_type']                 = 'edit';
            $_SESSION['business_trip']['edit']                      = $entity['document_number'];
            $_SESSION['business_trip']['document_number']           = $document_number;
            $_SESSION['business_trip']['format_number']             = $format_number.'-R'.$revisi;
            $_SESSION['business_trip']['department_id']             = $department_id;
            $_SESSION['business_trip']['person_in_charge']          = $entity['user_id'];
            $_SESSION['business_trip']['dateline']                  = print_date($entity['start_date'], 'd-m-Y').' s/d '.print_date($entity['end_date'], 'd-m-Y');
            
        }

        redirect($this->module['route'] .'/create');
        //$this->render_view($this->module['view'] .'/edit');
    }

    public function extend($id,$type="extend")
    {
        $this->authorized($this->module, 'create');

        $entity = $this->model->findById($id);

        $document_number    = travel_on_duty_last_number();
        $format_number      = travel_on_duty_format_number();

        if (isset($_SESSION['business_trip']) === FALSE){
            $cost_center = findCostCenter($entity['annual_cost_center_id']);
            $cost_center_code = $cost_center['cost_center_code'];
            $cost_center_name = $cost_center['cost_center_name'];          
            $department_id    = $cost_center['department_id'];
            $get_start_date   = strtotime('+1 day',strtotime($entity['end_date']));
            $start_date       = date('Y-m-d', $get_start_date);

            $_SESSION['business_trip']['annual_cost_center_id']     = $annual_cost_center_id;
            $_SESSION['business_trip']['cost_center_id']            = $cost_center_id;
            $_SESSION['business_trip']['cost_center_name']          = $cost_center_name;
            $_SESSION['business_trip']['cost_center_code']          = $cost_center_code;
            $_SESSION['business_trip']                              = $entity;
            // $_SESSION['business_trip']['id']                        = $id;
            $_SESSION['business_trip']['edit_type']                 = $type;
            // $_SESSION['business_trip']['edit']                      = $entity['document_number'];
            $_SESSION['business_trip']['document_number']           = $document_number;
            $_SESSION['business_trip']['format_number']             = $format_number;
            $_SESSION['business_trip']['department_id']             = $department_id;
            $_SESSION['business_trip']['person_in_charge']          = $entity['user_id'];
            $_SESSION['business_trip']['start_date']                = $start_date;
            $_SESSION['business_trip']['end_date']                  = $start_date;
            $_SESSION['business_trip']['duration']                  = 1;
            $_SESSION['business_trip']['dateline']                  = print_date($_SESSION['business_trip']['start_date'], 'd-m-Y').' s/d '.print_date($_SESSION['business_trip']['end_date'], 'd-m-Y');
            $_SESSION['business_trip']['additional_notes']          = 'extend from SPD #'.$entity['document_number'];
            
        }

        redirect($this->module['route'] .'/create');
        //$this->render_view($this->module['view'] .'/edit');
    }

    public function edit_approve($id)
    {
        $this->authorized($this->module, 'approval');

        $entity = $this->model->findById($id);

        $document_number    = sprintf('%06s', substr($entity['document_number'], 0, 6));
        $format_number      = substr($entity['document_number'], 6, 21);
        $revisi             = get_count_revisi($document_number.$format_number,'SPD');

        if (isset($_SESSION['business_trip']) === FALSE){
            $cost_center = findCostCenter($entity['annual_cost_center_id']);
            $cost_center_code = $cost_center['cost_center_code'];
            $cost_center_name = $cost_center['cost_center_name'];          
            $department_id    = $cost_center['department_id'];

            $_SESSION['business_trip']['annual_cost_center_id']     = $annual_cost_center_id;
            $_SESSION['business_trip']['cost_center_id']            = $cost_center_id;
            $_SESSION['business_trip']['cost_center_name']          = $cost_center_name;
            $_SESSION['business_trip']['cost_center_code']          = $cost_center_code;
            $_SESSION['business_trip']                              = $entity;
            $_SESSION['business_trip']['id']                        = $id;
            $_SESSION['business_trip']['edit_type']                 = 'edit_approve';
            $_SESSION['business_trip']['edit']                      = $entity['document_number'];
            $_SESSION['business_trip']['document_number']           = $document_number;
            $_SESSION['business_trip']['format_number']             = $format_number.'-R'.$revisi;
            $_SESSION['business_trip']['department_id']             = $department_id;
            $_SESSION['business_trip']['person_in_charge']          = $entity['user_id'];
            $_SESSION['business_trip']['dateline']                  = print_date($entity['start_date'], 'd-m-Y').' s/d '.print_date($entity['end_date'], 'd-m-Y');
            
        }

        redirect($this->module['route'] .'/create_for_edit_approve');
        //$this->render_view($this->module['view'] .'/edit');
    }

    public function create_for_edit_approve($annual_cost_center_id = NULL)
    {
        $this->authorized($this->module, 'approval');

        if ($annual_cost_center_id !== NULL){
            $annual_cost_center_id = urldecode($annual_cost_center_id);
            $cost_center = findCostCenter($annual_cost_center_id);
            $cost_center_code = $cost_center['cost_center_code'];
            $cost_center_name = $cost_center['cost_center_name'];          
            $department_id    = $cost_center['department_id'];

            $_SESSION['business_trip']['items']                     = array();
            $_SESSION['business_trip']['annual_cost_center_id']     = $annual_cost_center_id;
            $_SESSION['business_trip']['cost_center_id']            = $cost_center_id;
            $_SESSION['business_trip']['cost_center_name']          = $cost_center_name;
            $_SESSION['business_trip']['cost_center_code']          = $cost_center_code;
            $_SESSION['business_trip']['document_number']           = travel_on_duty_last_number();
            $_SESSION['business_trip']['format_number']             = travel_on_duty_format_number();
            $_SESSION['business_trip']['date']                      = date('Y-m-d');
            $_SESSION['business_trip']['created_by']                = config_item('auth_person_name');
            $_SESSION['business_trip']['warehouse']                 = config_item('auth_warehouse');
            $_SESSION['business_trip']['notes']                     = NULL;
            $_SESSION['business_trip']['person_in_charge']          = NULL;
            $_SESSION['business_trip']['department_id']             = $department_id;
            $_SESSION['business_trip']['head_dept']                 = NULL;
            $_SESSION['business_trip']['business_trip_destination_id']  = NULL;
            $_SESSION['business_trip']['duration']                      = NULL;
            $_SESSION['business_trip']['dateline']                      = NULL;
            $_SESSION['business_trip']['occupation']                    = NULL;
            $_SESSION['business_trip']['phone_number']                  = NULL;
            $_SESSION['business_trip']['id_number']                     = NULL;
            $_SESSION['business_trip']['start_date']                    = NULL;
            $_SESSION['business_trip']['end_date']                      = NULL;
            $_SESSION['business_trip']['from_base']                      = NULL;
            $_SESSION['business_trip']['transportation']                      = NULL;

            redirect($this->module['route'] .'/create_for_edit_approve');
        }

        if (!isset($_SESSION['business_trip']))
          redirect($this->module['route']);

        $this->data['page']['content']    = $this->module['view'] .'/create';
        $this->data['page']['offcanvas']  = $this->module['view'] .'/create_offcanvas_add_item';
        $this->data['page']['title']      = "EDIT & APPROVE SURAT PERJALANAN DINAS";

        $this->render_view($this->module['view'] .'/edit_approve');
    }

    public function save()
    {
        if ($this->input->is_ajax_request() == FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'create') == FALSE){
            $data['success'] = FALSE;
            $data['message'] = 'You are not allowed to save this Document!';
        } else {

            $document_number = $_SESSION['business_trip']['document_number'] . $_SESSION['business_trip']['format_number'];
            $errors = array();

            if ($_SESSION['business_trip']['head_dept']==NULL || $_SESSION['business_trip']['head_dept']=='') {
                $errors[] = 'Attention!! Please select one of Head Dept for Approval';
            }

            if ($_SESSION['business_trip']['notes']==NULL || $_SESSION['business_trip']['notes']=='') {
                $errors[] = 'Attention!! Please Fill Notes!!';
            }

            if ($_SESSION['business_trip']['transportation']==NULL || $_SESSION['business_trip']['transportation']=='') {
                $errors[] = 'Attention!! Please Fill Transportation!!';
            }

            if (strtotime($_SESSION['business_trip']['start_date']) > strtotime($_SESSION['business_trip']['end_date'])) {
                $errors[] = 'Attention!! Start Date must earlier than End Date!! Change Your Start Date';
            }

            if($this->model->cekDateline($_SESSION['business_trip']['person_in_charge'],$_SESSION['business_trip']['start_date'],$_SESSION['business_trip']['end_date'])){
                $errors[] = 'This employee still has an unfinished business trip for The Date!! You Should select the right Date';
            }

            if (!empty($errors)){
                $data['success'] = FALSE;
                $data['message'] = implode('<br />', $errors);
            } else {
                if ($this->model->save()){
                    unset($_SESSION['business_trip']);
        
                    $data['success'] = TRUE;
                    $data['message'] = 'Document '. $document_number .' has been saved. You will redirected now.';
                } else {
                    $data['success'] = FALSE;
                    $data['message'] = 'Error while saving this document. Please ask Technical Support.';
                }
            }
        }

        echo json_encode($data);
    }

    public function save_approve()
    {
        if ($this->input->is_ajax_request() == FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'approval') == FALSE){
            $data['success'] = FALSE;
            $data['message'] = 'You are not allowed to save this Document!';
        } else {

            $document_number = $_SESSION['business_trip']['document_number'] . $_SESSION['business_trip']['format_number'];
            $errors = array();

            if ($_SESSION['business_trip']['head_dept']==NULL || $_SESSION['business_trip']['head_dept']=='') {
                $errors[] = 'Attention!! Please select one of Head Dept for Approval';
            }

            if ($_SESSION['business_trip']['notes']==NULL || $_SESSION['business_trip']['notes']=='') {
                $errors[] = 'Attention!! Please Fill Notes!!';
            }

            if (!empty($errors)){
                $data['success'] = FALSE;
                $data['message'] = implode('<br />', $errors);
            } else {
                if ($this->model->save()){
                    unset($_SESSION['business_trip']);
        
                    $data['success'] = TRUE;
                    $data['message'] = 'Document '. $document_number .' has been saved. You will redirected now.';
                } else {
                    $data['success'] = FALSE;
                    $data['message'] = 'Error while saving this document. Please ask Technical Support.';
                }
            }
        }

        echo json_encode($data);
    }

    public function hr_approve($id)
    {
        $this->authorized($this->module, 'approval');

        $entity = $this->model->findById($id);

        if($entity['status']=='WAITING APPROVAL BY HR MANAGER' && in_array(config_item('auth_username'),list_username_in_head_department(11))){
            $_SESSION['business_trip']['id']                        = $id;
            $_SESSION['business_trip']['edit']                      = $entity['document_number'];
            $_SESSION['business_trip']['document_number']           = $entity['document_number'];
            $_SESSION['business_trip']['format_number']             = $format_number.'-R'.$revisi;
            $this->data['entity'] = $entity;        

            $this->data['page']['content']    = $this->module['view'] .'/create';
            $this->data['page']['offcanvas']  = $this->module['view'] .'/create_offcanvas_add_item';
            
            $this->data['page']['title']      = "HR APPROVE SURAT PERJALANAN DINAS";

            $this->render_view($this->module['view'] .'/hr_approve', $this->data);
        }else{
            redirect(site_url('secure/denied'));
        }        
    }

    public function save_hr_approve()
    {
        if ($this->input->is_ajax_request() == FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'approval') == FALSE){
            $data['success'] = FALSE;
            $data['message'] = 'You are not allowed to save this Document!';
        } else {

            $document_number = $_SESSION['business_trip']['document_number'];
            $errors = array();

            if (!empty($errors)){
                $data['success'] = FALSE;
                $data['message'] = implode('<br />', $errors);
            } else {
                if ($this->model->save_hr_approve()){
                    unset($_SESSION['business_trip']);
        
                    $data['success'] = TRUE;
                    $data['message'] = 'Document '. $document_number .' has been approved by HR. You will redirected now.';
                } else {
                    $data['success'] = FALSE;
                    $data['message'] = 'Error while saving this document. Please ask Technical Support.';
                }
            }
        }

        echo json_encode($data);
    }

    public function discard()
    {
        $this->authorized($this->module['permission']['create']);

        unset($_SESSION['business_trip']);

        redirect($this->module['route']);
    }

    public function delete_ajax()
    {
        if ($this->input->is_ajax_request() === FALSE)
        redirect($this->modules['secure']['route'] .'/denied');

        if (is_granted($this->module, 'delete') === FALSE){
        $alert['type']  = 'danger';
        $alert['info']  = 'You are not allowed to delete this data!';
        } else {
        $entity = $this->model->findById($this->input->post('id'));

        if ($this->model->isValidDocumentQuantity($entity['document_number']) === FALSE){
            $alert['type']  = 'danger';
            $alert['info']  = 'Stock quantity for document ' . $entity['document_number'] . ' has been change. You are not allowed to delete this document. You can adjust stock to sync the quantity.';
        } else {
            if ($this->model->delete()){
            $alert['type'] = 'success';
            $alert['info'] = 'Data deleted.';
            $alert['link'] = site_url($this->module['route']);
            } else {
            $alert['type'] = 'danger';
            $alert['info'] = 'There are error while deleting data. Please try again later.';
            }
        }
        }

        echo json_encode($alert);
    }

    public function multi_approve()
    {
        $document_id = $this->input->post('document_id');
        $document_id = str_replace("|", "", $document_id);
        $document_id = substr($document_id, 0, -1);
        $document_id = explode(",", $document_id);

        $str_notes = $this->input->post('notes');
        $notes = str_replace("|", "", $str_notes);
        $notes = substr($notes, 0, -3);
        $notes = explode("##,", $notes);

        $total = 0;
        $success = 0;
        $failed = sizeof($document_id);
        $x = 0;

        $save_approval = $this->model->approve($document_id, $notes);
        if ($save_approval['status']) {
            $this->session->set_flashdata('alert', array(
                'type' => 'success',
                'info' => $save_approval['success'] . " data has been update!"
            ));
        }else{
            $this->session->set_flashdata('alert', array(
                'type' => 'danger',
                'info' => "There are " . $save_approval['failed'] . " errors"
            ));
        }
        
        if ($save_approval['total'] == 0) {
            $result['status'] = 'failed';
        } else {
            $result['status'] = 'success';
        }
        echo json_encode($result);
    }

    public function multi_reject()
    {
        $document_id = $this->input->post('document_id');
        $document_id = str_replace("|", "", $document_id);
        $document_id = substr($document_id, 0, -1);
        $document_id = explode(",", $document_id);

        $str_notes = $this->input->post('notes');
        $notes = str_replace("|", "", $str_notes);
        $notes = substr($notes, 0, -3);
        $notes = explode("##,", $notes);

        $total = 0;
        $success = 0;
        $failed = sizeof($document_id);
        $x = 0;

        $save_approval = $this->model->reject($document_id, $notes);
        if ($save_approval['status']) {
            $this->session->set_flashdata('alert', array(
                'type' => 'success',
                'info' => $save_approval['success'] . " data has been update!"
            ));
        }else{
            $this->session->set_flashdata('alert', array(
                'type' => 'danger',
                'info' => "There are " . $save_approval['failed'] . " errors"
            ));
        }
        
        if ($save_approval['total'] == 0) {
            $result['status'] = 'failed';
        } else {
            $result['status'] = 'success';
        }
        echo json_encode($result);
    }

    public function import()
    {
        $this->authorized($this->module, 'import');


        redirect($this->module['route']);
    }  

    public function attachment()
    {
        $this->authorized($this->module, 'create');

        $this->render_view($this->module['view'] . '/attachment');
    }

    public function add_attachment()
    {
        $result["status"] = 0;
        $date = new DateTime();
        $config['upload_path'] = 'attachment/spd/';
        $config['allowed_types'] = 'jpg|png|jpeg|doc|docx|xls|xlsx|pdf';
        $config['max_size']  = 2000;

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('attachment')) {
            $error = array('error' => $this->upload->display_errors());
        } else {

            $data = array('upload_data' => $this->upload->data());
            $url = $config['upload_path'] . $data['upload_data']['file_name'];
            array_push($_SESSION["business_trip"]["attachment"], $url);
            $result["status"] = 1;
        }
        echo json_encode($result);
    }

    public function manage_attachment($id)
    {
        $this->authorized($this->module, 'info');

        $this->data['manage_attachment'] = $this->model->listAttachment($id);
        $this->data['id'] = $id;
        $this->render_view($this->module['view'] . '/manage_attachment');
    }

    public function add_attachment_to_db($id)
    {
        $result["status"] = 0;
        $date = new DateTime();
        $config['upload_path'] = 'attachment/spd/';
        $config['allowed_types'] = 'jpg|png|jpeg|doc|docx|xls|xlsx|pdf';
        $config['max_size']  = 2000;

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('attachment')) {
            $error = array('error' => $this->upload->display_errors());
            $result["status"] = $error;
        } else {
            $data = array('upload_data' => $this->upload->data());
            $url = $config['upload_path'] . $data['upload_data']['file_name'];
            // array_push($_SESSION["poe"]["attachment"], $url);
            $this->model->add_attachment_to_db($id, $url);
            $result["status"] = 1;
        }
        echo json_encode($result);
    }

    public function delete_attachment_in_db($id_att, $id_poe)
    {
        $this->model->delete_attachment_in_db($id_att);

        redirect($this->module['route'] . "/manage_attachment/" . $id_poe, 'refresh');
        // echo json_encode($result);
    }

    public function create_expense_ajax()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        if (is_granted($this->module, 'create') === FALSE){
            $alert['type']  = 'danger';
            $alert['info']  = 'You are not allowed to create this data!';
        } else {
            $create_expense = $this->model->create_expense();
            if ($create_expense['status']){
                $alert['type'] = 'success';
                $alert['info'] = 'SPD has beenn created in to Expense Request #'.$create_expense['pr_number'];
                $alert['link'] = site_url($this->module['route']);
            } else {
                $alert['type'] = 'danger';
                $alert['info'] = 'There are error while creating data. Please try again later.';
            }
        }

        echo json_encode($alert);
    }

    public function test()
    {
        $data = $this->model->send_mail_approval(10,'Ratining','edit_approve');
        // $data = get_count_revisi('000005/SPD/BWD-BIFA/01/2023');
        // $data = get_travel_on_duty_last_number();
        // $data = $range_date  = explode('.', '000005/SPD/BWD-BIFA/01/2023');
        // $data  = substr('000005/SPD/BWD-BIFA/01/2023', 7, 21);
        
        // $result['status'] = $send;
        echo json_encode($data);
    }
}
