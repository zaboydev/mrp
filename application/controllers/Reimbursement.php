<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reimbursement extends MY_Controller
{
    protected $module;
    protected $id_item=0;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['reimbursement'];
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
            $total_value  = array();

            foreach ($entities as $row){
                $cost_center = findCostCenter($row['annual_cost_center_id']);
                $cost_center_code = $cost_center['cost_center_code'];
                $cost_center_name = $cost_center['cost_center_name'];
                $department_name = $cost_center['department_name'];         
                $no++;
                $col = array();
                if (is_granted($this->module, 'approval')){
                    if($row['status']=='WAITING APPROVAL BY HEAD DEPT' && in_array($department_name,config_item('head_department')) && $row['head_dept']==config_item('auth_username') ){
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    }elseif($row['status']=='WAITING APPROVAL BY HR MANAGER' && in_array(config_item('auth_username'),list_username_in_head_department(11))){
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    }elseif($row['status']=='WAITING APPROVAL BY COO OR CFO'&& config_item('auth_role') == 'CHIEF OF FINANCE' || config_item('auth_role') == 'CHIEF OPERATION OFFICER'){
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    }elseif($row['status']=='REVISED'){
                        $col[] = print_number($no);
                    }else {
                        $col[] = print_number($no);
                    }
                    
                    // if($row['status']=='REVISED'){
                    //     $col[] = print_number($no);
                    // } else {
                    //     $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    // }
                }else{
                    $col[] = print_number($no);
                }            
                $col[] = print_date($row['date'], 'd F Y');    
                $col[] = print_string($row['document_number']);
                $col[] = print_string($row['pr_number']);
                $col[] = print_string($row['type']);
                $col[] = print_string($row['status']);
                $col[] = print_string($cost_center['cost_center_name']);
                $col[] = print_string($row['person_name']);
                $col[] = print_number($row['total'],2);
                $col[] = print_string($row['notes']);
                if($row['status']=='approved' || $row['status']=='closed'){
                    $col[] = '';
                }else{
                    if (is_granted($this->module, 'approval') === TRUE && in_array($row['status'],['WAITING APPROVAL BY HEAD DEPT','WAITING APPROVAL BY HR MANAGER','WAITING APPROVAL BY COO OR CFO'])) {
                        $col[] = '<input type="text" id="note_' . $row['id'] . '" autocomplete="off"/>';
                    }else{
                        $col[] = '';
                    }
                }

                $total_value[] = $row['total'];
                
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

            $result = array(
                "draw"            => $_POST['draw'],
                "recordsTotal"    => $this->model->countIndex(),
                "recordsFiltered" => $this->model->countIndexFiltered(),
                "data"            => $data,
                "total"           => array(
                    7 => print_number(array_sum($total_value), 2),
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
        $this->data['grid']['summary_columns']  = array(7);

        $this->data['grid']['order_columns']    = array(
            0   => array( 0 => 0,  1 => 'desc' ),
            1   => array( 0 => 1,  1 => 'desc' ),
            2   => array( 0 => 2,  1 => 'desc' ),
            3   => array( 0 => 3,  1 => 'desc' ),
            4   => array( 0 => 4,  1 => 'desc' ),
            5   => array( 0 => 5,  1 => 'desc' ),


        );

        $this->render_view($this->module['view'] .'/index');
    }

    public function get_employee_saldo()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');
        

        $employee_number = $_GET['employee_number'];
        $benefit_id = $_GET['type'];
        $position = $_GET['position'];

        $employee_has_benefit = $this->model->getEmployeeHasBenefit($employee_number,$benefit_id,$position);
        
        echo json_encode($employee_has_benefit);
    }

    public function get_expense_reimbursement()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');
        

        $id_expense = $_GET['id_expense_reimbursement'];

        $employee_has_benefit = $this->model->getExpenseReimbursement($id_expense);
        
        echo json_encode($employee_has_benefit);
    }


    public function get_expense_name()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');
        

        $id_expense = $_GET['id_type'];

        $expense = $this->model->getExpenseName($id_expense);
        
        echo json_encode($expense);
    }

    public function set_doc_number()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        if (empty($_GET['data']))
            $number = reimbursement_last_number();
        else
            $number = $_GET['data'];

        $_SESSION['reimbursement']['document_number'] = $number;
    }

    public function set_employee_has_benefit_id()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');


        $_SESSION['reimbursement']['employee_has_benefit_id'] = $_GET['data'];
    }

    public function set_saldo_balance()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');


        $_SESSION['reimbursement']['saldo_balance'] = $_GET['data'];
    }

    public function set_plafond_saldo_balance()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');


        $_SESSION['reimbursement']['plafond_balance'] = $_GET['data'];
    }

    public function set_description()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['reimbursement']['description'] = $number;
    }

    public function set_used_saldo_balance()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');


        $_SESSION['reimbursement']['used_balance'] = $_GET['data'];
    }

    public function set_employee_number()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['reimbursement']['employee_number'] = $_GET['data'];
    }

    public function set_occupation()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['reimbursement']['occupation'] = $_GET['data'];
    }

    public function set_type_reimbursement()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['reimbursement']['type'] = $_GET['data'];
    }

    public function set_type_reimbursement_id()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['reimbursement']['type_id'] = $_GET['data'];
    }

    public function set_account_code()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['reimbursement']['account_code'] = $_GET['data'];
    }

    public function set_id_benefit()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['reimbursement']['id_benefit'] = $_GET['data'];
    }

    public function set_benefitcode()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['reimbursement']['benefit_code'] = $_GET['data'];
    }

    // public function set_account_code_item()
    // {
    //     if ($this->input->is_ajax_request() === FALSE)
    //         redirect($this->modules['secure']['route'] .'/denied');

    //     $_SESSION['reimbursement']['account_code_item'] = $_GET['data'];
    // }

    public function set_head_dept()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['reimbursement']['head_dept'] = $_GET['data'];
    }

    public function set_from_base()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['reimbursement']['from_base'] = $_GET['data'];
    }

    public function set_notes()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['reimbursement']['notes'] = $_GET['data'];
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
            $department_name  = $cost_center['department_name'];

            $_SESSION['reimbursement']['items']                     = array();
            $_SESSION['reimbursement']['annual_cost_center_id']     = $annual_cost_center_id;
            $_SESSION['reimbursement']['cost_center_id']            = $cost_center_id;
            $_SESSION['reimbursement']['cost_center_name']          = $cost_center_name;
            $_SESSION['reimbursement']['cost_center_code']          = $cost_center_code;
            $_SESSION['reimbursement']['document_number']           = reimbursement_last_number();
            $_SESSION['reimbursement']['format_number']             = reimbursement_format_number();
            $_SESSION['reimbursement']['date']                      = date('Y-m-d');
            $_SESSION['reimbursement']['created_by']                = config_item('auth_person_name');
            $_SESSION['reimbursement']['warehouse']                 = config_item('auth_warehouse');
            $_SESSION['reimbursement']['notes']                     = NULL;
            $_SESSION['reimbursement']['employee_number']           = NULL;
            $_SESSION['reimbursement']['person_name']               = NULL;
            $_SESSION['reimbursement']['department_id']             = $department_id;
            $_SESSION['reimbursement']['occupation']                = NULL;
            $_SESSION['reimbursement']['department_name']           = $department_name;
            $_SESSION['reimbursement']['head_dept']                 = NULL;
            $_SESSION['reimbursement']['type']                      = 'Reimbursement';
            $_SESSION['reimbursement']['saldo_balance']             = 0;
            $_SESSION['reimbursement']['plafond_balance']           = 0;
            $_SESSION['reimbursement']['used_balance']              = 0;
            $_SESSION['reimbursement']['employee_has_benefit_id']   = NULL;
            $_SESSION['reimbursement']['account_code']   = NULL;

            redirect($this->module['route'] .'/create');
        }

        if (!isset($_SESSION['reimbursement']))
          redirect($this->module['route']);

        $this->data['page']['content']    = $this->module['view'] .'/create';
        $this->data['page']['offcanvas']  = $this->module['view'] .'/create_offcanvas_add_item';

        $this->render_view($this->module['view'] .'/create');
    }

    public function search_budget()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        $annual_cost_center_id = $_SESSION['reimbursement']['annual_cost_center_id'];
        $entities = $this->model->getExpenseReimbursementItem($annual_cost_center_id,$with_po);

        foreach ($entities as $key => $value) {
                $entities[$key]['label'] .= $value['account_code'];
                $entities[$key]['label'] .= $value['expense_name'];
        }

        echo json_encode($entities);
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
        $level_akun = config_item('auth_role');

        $this->data['entity']           = $entity;
        $this->data['level_akun']       = $level_akun;
        $this->data['page']['title']    = strtoupper($this->module['label']);
        $this->data['page']['content']  = $this->module['view'] .'/print_pdf';

        $html = $this->load->view($this->pdf_theme, $this->data, true);

        $pdfFilePath = str_replace('/', '-', $entity['document_number']) .".pdf";

        $this->load->library('m_pdf');

        $pdf = $this->m_pdf->load(null, 'A4-P');
        $pdf->WriteHTML($html);
        $pdf->Output($pdfFilePath, "I");
    }

    public function edit($id)
    {
        $this->authorized($this->module, 'create');

        $entity = $this->model->findById($id);

        $document_number    = sprintf('%06s', substr($entity['document_number'], 0, 6));
        $format_number      = substr($entity['document_number'], 6, 25);
        $revisi             = get_count_revisi($document_number.$format_number,'REIMBURSEMENT');

        if (isset($_SESSION['receipt']) === FALSE){
            $cost_center = findCostCenter($entity['annual_cost_center_id']);
            $cost_center_code = $cost_center['cost_center_code'];
            $cost_center_name = $cost_center['cost_center_name'];          
            $department_id    = $cost_center['department_id'];
            $employee_has_benefit    = $this->model->getEmployeeHasBenefitById($entity['employee_has_benefit_id']);

            $_SESSION['reimbursement']['annual_cost_center_id']     = $annual_cost_center_id;
            $_SESSION['reimbursement']['cost_center_id']            = $cost_center_id;
            $_SESSION['reimbursement']['cost_center_name']          = $cost_center_name;
            $_SESSION['reimbursement']['cost_center_code']          = $cost_center_code;
            $_SESSION['reimbursement']                              = $entity;
            $_SESSION['reimbursement']['id']                        = $id;
            $_SESSION['reimbursement']['edit']                      = $entity['document_number'];
            $_SESSION['reimbursement']['document_number']           = $document_number.'-R'.$revisi;
            $_SESSION['reimbursement']['format_number']             = $format_number;
            $_SESSION['reimbursement']['department_id']             = $department_id;
            $_SESSION['reimbursement']['person_in_charge']          = $entity['user_id'];
            $_SESSION['reimbursement']['saldo_balance']             = $employee_has_benefit['left_amount_plafond']+$entity['total'];
            $_SESSION['reimbursement']['dateline']                  = print_date($entity['start_date'], 'd-m-Y').' s/d '.print_date($entity['end_date'], 'd-m-Y');
            
        }

        redirect($this->module['route'] .'/create');
        //$this->render_view($this->module['view'] .'/edit');
    }

    public function save()
    {
        
        if ($this->input->is_ajax_request() == FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        

        if (is_granted($this->module, 'create') == FALSE){
            $data['success'] = FALSE;
            $data['message'] = 'You are not allowed to save this Document!';
        } else {
            $errors = array();

            $document_number = $_SESSION['reimbursement']['document_number'] . $_SESSION['reimbursement']['format_number'];

            if ($_SESSION['reimbursement']['head_dept']==NULL || $_SESSION['reimbursement']['head_dept']=='') {
                $errors[] = 'Attention!! Please select one of Head Dept for Approval';
            }

            if ($_SESSION['reimbursement']['notes']==NULL || $_SESSION['reimbursement']['notes']=='') {
                $errors[] = 'Attention!! Please Fill Notes!!';
            }

            if ($_SESSION['reimbursement']['saldo_balance']==0) {
                $errors[] = "Saldo balance is 0. You Can't create reimbursement";
            }
            

            if (!empty($errors)){
                $data['success'] = FALSE;
                $data['message'] = implode('<br />', $errors);
            } else {
                if ($this->model->save()){
                    unset($_SESSION['reimbursement']);
        
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

    public function add_item()
    {
        $this->authorized($this->module, 'create');

        if (isset($_POST) && !empty($_POST)){
            if($this->input->post('amount') >= $this->input->post('saldo_balance_modal')){
                $_SESSION['reimbursement']['items'][] = array(
                    'description'       => $this->input->post('description'),
                    'transaction_date'  => $this->input->post('date'),
                    'notes'             => $this->input->post('notes'),
                    'amount'            => $this->input->post('amount'),
                    'paid_amount'       => $this->input->post('paid_amount_modal'),
                    'account_code_item' => $this->input->post('account_code_item'),
    
                );    

            } else {
                $_SESSION['reimbursement']['items'][] = array(
                    'description'       => $this->input->post('description'),
                    'transaction_date'  => $this->input->post('date'),
                    'notes'             => $this->input->post('notes'),
                    'amount'            => $this->input->post('amount'),
                    'paid_amount'       => $this->input->post('paid_amount_modal'),
                    'account_code_item' => $this->input->post('account_code_item'),
    
                );    
            }
               
        }

        redirect($this->module['route'] .'/create');
    }

    public function discard()
    {
        $this->authorized($this->module['permission']['document']);

        unset($_SESSION['receipt']);

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

    public function ajax_editItem($key)
    {
        $this->authorized($this->module, 'create');    

        $entity = $_SESSION['reimbursement']['items'][$key];

        echo json_encode($entity);
    }

    public function edit_item($key)
    {
        
        $this->authorized($this->module, 'create');

        // $key=$this->input->post('item_id');
        if (isset($_POST) && !empty($_POST)){
            // $receipts_items_id = $this->input->post('item_id')
            $_SESSION['reimbursement']['items'][$key] = array(        
                'description'       => $this->input->post('description'),
                'transaction_date'  => $this->input->post('date'),
                'notes'             => $this->input->post('notes'),
                'amount'            => $this->input->post('amount'),
                'account_code_item' => $this->input->post('account_code_item'),


            );
        } 
        redirect($this->module['route'] .'/create');

    }

    public function del_item($item_index) {
        if (isset($_SESSION['reimbursement']['items'][$item_index])) {
            // Delete the item
            unset($_SESSION['reimbursement']['items'][$item_index]);
    
            // Recalculate the total
            $total = array_sum(array_column($_SESSION['reimbursement']['items'], 'amount'));
    
            // Return the new total in JSON format
            echo json_encode(['total' => $total]);
        } else {
            echo json_encode(['total' => 0]);
        }
    }

    public function multi_approve()
    {
        $document_id = $this->input->post('document_id');
        $document_id = str_replace("|", "", $document_id);
        $document_id = substr($document_id, 0, -1);
        $document_id = explode(",", $document_id);

        $str_notes = $this->input->post('notes');
        $notes = str_replace("|", "", $str_notes);
        $notes = substr($price, 0, -3);
        $notes = explode("##,", $notes);

        $total = 0;
        $success = 0;
        $failed = sizeof($document_id);
        $x = 0;

        

        $save_approval = $this->model->approve($document_id, $notes);
        if ($save_approval['status']) {

            
                if(!empty($save_approval['approved_ids'])){
                    foreach ($save_approval['approved_ids'] as $id) {

                        $this->model->create_expense_auto($id);
                        
                    }
                    $this->session->set_flashdata('alert', array(
                        'type' => 'success',
                        'info' => $save_approval['success'] . " expense has been create!"
                    ));
                } else {
                    $this->session->set_flashdata('alert', array(
                        'type' => 'success',
                        'info' => $save_approval['success'] . " data has been update!"
                    ));
                }
            
            
        }else{
            $this->session->set_flashdata('alert', array(
                'type' => 'danger',
                'info' => "There are " . $save_approval['failed'] . " errors"
            ));
        }
        
        if ($save_approval['status']) {
            $result['status'] = 'success';
        } else {
            $result['status'] = 'failed';
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
        $notes = substr($price, 0, -3);
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
        

        if ($success > 0) {
            // $this->model->send_mail_approval($id_expense_request, 'approve', config_item('auth_person_name'),$notes);
            $this->session->set_flashdata('alert', array(
                'type' => 'success',
                'info' => $success . " data has been update!"
            ));
        }
        if ($failed > 0) {
            $this->session->set_flashdata('alert', array(
                'type' => 'danger',
                'info' => "There are " . $failed . " errors"
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

    public function create_expense_ajax()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        if (is_granted($this->module, 'delete') === FALSE){
            $alert['type']  = 'danger';
            $alert['info']  = 'You are not allowed to delete this data!';
        } else {
            $create_expense = $this->model->create_expense();
            if ($create_expense['status']){
                $alert['type'] = 'success';
                $alert['info'] = 'Reimbursement has beenn create in to Expense Request #'.$create_expense['pr_number'];
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
        // $data = $this->model->send_mail(6,'head_dept','request');
        // $data = get_count_revisi('000005/SPD/BWD-BIFA/01/2023');
        // $data = get_travel_on_duty_last_number();
        // $data = $range_date  = explode('.', '000005/SPD/BWD-BIFA/01/2023');
        $data  = substr('000005/SPD/BWD-BIFA/01/2023', 7, 21);
        
        // $result['status'] = $send;
        echo json_encode($data);
    }

    //Attachment Function


    public function attachment()
    {
        $this->authorized($this->module, 'create');

        $this->render_view($this->module['view'] . '/attachment');
    }

    public function attachment_detail_spd($item_id,$type)
    {
        $this->authorized($this->module, 'create');
        $this->data['item_id'] = $item_id;
        $this->data['type'] = $type;
        $this->render_view($this->module['view'] . '/attachment_detail', $this->data);
    }

    public function add_attachment()
    {
      $result["status"] = 0;
      $date = new DateTime();
      // $config['file_name'] = $date->getTimestamp().random_string('alnum', 5);
      $config['upload_path'] = 'attachment/reimbursement/';
      $config['allowed_types'] = 'jpg|png|jpeg|doc|docx|xls|xlsx|pdf';
      $config['max_size']  = 2000;
  
      $this->upload->initialize($config);
  
      if (!$this->upload->do_upload('attachment')) {
        $error = array('error' => $this->upload->display_errors());
      } else {

        if (!isset($_SESSION["reimbursement"]["attachment"]) || !is_array($_SESSION["reimbursement"]["attachment"])) {
            $_SESSION["reimbursement"]["attachment"] = [];
        }
  
        $data = array('upload_data' => $this->upload->data());
        $url = $config['upload_path'] . $data['upload_data']['file_name'];
        array_push($_SESSION["reimbursement"]["attachment"], $url);
        $result["status"] = 1;
      }
      echo json_encode($result);
    }

    

    public function add_attachment_detail()
    {
        $result["status"] = 0;
        $date = new DateTime();
        $config['upload_path'] = 'attachment/reimbursement-detail/';
        $config['allowed_types'] = 'jpg|png|jpeg|doc|docx|xls|xlsx|pdf';
        $config['max_size']  = 2000;

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('attachment')) {
            $error = array('error' => $this->upload->display_errors());
        } else {

            $data = array('upload_data' => $this->upload->data());
            $url = $config['upload_path'] . $data['upload_data']['file_name'];
            $data_att = $url.'|,'.$this->input->post('id_poe').'|,'.$this->input->post('tipe');
            array_push($_SESSION["reimbursement"]["attachment_detail"],$data_att);
            $result["status"] = 1;
        }
        echo json_encode($result);
    }

    public function manage_attachment($id)
    {
        $this->authorized($this->module, 'info');

        $this->data['manage_attachment'] = $this->model->listAttachment($id);
        $this->data['id'] = $id;
        $this->data['tipe'] = 'REIMBURSEMENT';
        $this->render_view($this->module['view'] . '/manage_attachment');
    }

    public function manage_attachment_detail($id)
    {
        $this->authorized($this->module, 'info');

        $this->data['manage_attachment'] = $this->model->listAttachment($id,'REIMBURESEMENT-DETAIL');
        $this->data['id'] = $id;
        $this->data['tipe'] = 'REIMBURSEMENT-DETAIL';
        $this->render_view($this->module['view'] . '/manage_attachment');
    }

    public function add_attachment_to_db($id)
    {
        $result["status"] = 0;
        $date = new DateTime();
        if($this->input->post('tipe')=='REIMBURSEMENT'){
            $config['upload_path'] = 'attachment/reimbursement/';
        }else{
            $config['upload_path'] = 'attachment/reimbursement-detail/';
        }
        
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
            $this->model->add_attachment_to_db($id, $url,$this->input->post('tipe'));
            $result["status"] = 1;
        }
        echo json_encode($result);
    }

    public function delete_attachment($index)
    {
        $file = FCPATH . $_SESSION["reimbursement"]["attachment"][$index];
        if (unlink($file)) {
            unset($_SESSION["reimbursement"]["attachment"][$index]);
            $_SESSION["reimbursement"]["attachment"] = array_values($_SESSION["reimbursement"]["attachment"]);
            redirect($this->module['route'] . "/attachment", 'refresh');
        }
    }

    public function delete_attachment_detail($index,$item_id,$type)
    {
        $att = $_SESSION["reimbursement"]["attachment_detail"][$index];
        $att_explode = explode("|,", $att);
        $file = FCPATH . $att_explode[0];
        if (unlink($file)) {
            unset($_SESSION["reimbursement"]["attachment_detail"][$index]);
            $_SESSION["reimbursement"]["attachment_detail"] = array_values($_SESSION["reimbursement"]["attachment_detail"]);
            redirect($this->module['route'] . "/attachment_detail_reimbursement/".$item_id."/".$type, 'refresh');
        }
    }

    public function delete_attachment_in_db($id_att, $id_poe, $tipe='REIMBURSEMENT')
    {
        $this->model->delete_attachment_in_db($id_att);

        if ($tipe=='REIMBURSEMENT') {
            redirect($this->module['route'] . "/manage_attachment/" . $id_poe, 'refresh');
        }else{
            redirect($this->module['route'] . "/manage_attachment_detail/" . $id_poe, 'refresh');
        }

        
        // echo json_encode($result);
    }
}
