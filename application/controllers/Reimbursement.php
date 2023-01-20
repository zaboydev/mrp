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
                    if($row['status']=='WAITING APPROVAL BY HEAD DEPT' && in_array($department_name,config_item('head_department')) && $row['head_dept']==config_item('auth_username')){
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    }elseif($row['status']=='WAITING APPROVAL BY HR MANAGER' && in_array(config_item('auth_username'),list_username_in_head_department(11))){
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    }else{
                        $col[] = print_number($no);
                    }                    
                }else{
                    $col[] = print_number($no);
                }                
                $col[] = print_string($row['document_number']);
                $col[] = print_string($row['type']);
                $col[] = print_string($row['status']);
                $col[] = print_date($row['date']);
                $col[] = print_string($cost_center['cost_center_name']);
                $col[] = print_string($row['person_name']);
                $col[] = print_number($row['total'],2);
                $col[] = print_string($row['notes']);
                if($row['status']=='approved' || $row['status']=='closed'){
                    $col[] = '';
                }else{
                    if (is_granted($this->module, 'approval') === TRUE && in_array($row['status'],['WAITING APPROVAL BY HEAD DEPT','WAITING APPROVAL BY HR MANAGER'])) {
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
            0   => array( 0 => 1,  1 => 'desc' ),
        );

        $this->render_view($this->module['view'] .'/index');
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
            $_SESSION['reimbursement']['type']                      = 'Duty Allowance';

            redirect($this->module['route'] .'/create');
        }

        if (!isset($_SESSION['reimbursement']))
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

    public function edit($id)
    {
        $this->authorized($this->module, 'create');

        $entity = $this->model->findById($id);

        $document_number    = sprintf('%06s', substr($entity['document_number'], 0, 6));
        $format_number      = substr($entity['document_number'], 6, 21);
        $revisi             = get_count_revisi($document_number.$format_number);

        if (isset($_SESSION['receipt']) === FALSE){
            $cost_center = findCostCenter($entity['annual_cost_center_id']);
            $cost_center_code = $cost_center['cost_center_code'];
            $cost_center_name = $cost_center['cost_center_name'];          
            $department_id    = $cost_center['department_id'];

            $_SESSION['reimbursement']['annual_cost_center_id']     = $annual_cost_center_id;
            $_SESSION['reimbursement']['cost_center_id']            = $cost_center_id;
            $_SESSION['reimbursement']['cost_center_name']          = $cost_center_name;
            $_SESSION['reimbursement']['cost_center_code']          = $cost_center_code;
            $_SESSION['reimbursement']                              = $entity;
            $_SESSION['reimbursement']['id']                        = $id;
            $_SESSION['reimbursement']['edit']                      = $entity['document_number'];
            $_SESSION['reimbursement']['document_number']           = $document_number;
            $_SESSION['reimbursement']['format_number']             = $format_number.'-R'.$revisi;
            $_SESSION['reimbursement']['department_id']             = $department_id;
            $_SESSION['reimbursement']['person_in_charge']          = $entity['user_id'];
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

            $document_number = $_SESSION['reimbursement']['document_number'] . $_SESSION['reimbursement']['format_number'];
            $errors = array();

            if ($_SESSION['reimbursement']['head_dept']==NULL || $_SESSION['reimbursement']['head_dept']=='') {
                $errors[] = 'Attention!! Please select one of Head Dept for Approval';
            }

            if ($_SESSION['reimbursement']['notes']==NULL || $_SESSION['reimbursement']['notes']=='') {
                $errors[] = 'Attention!! Please Fill Notes!!';
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
            $_SESSION['reimbursement']['items'][] = array(
                'description'       => $this->input->post('description'),
                'transaction_date'  => $this->input->post('date'),
                'notes'             => $this->input->post('notes'),
                'amount'            => $this->input->post('amount'),
            );        
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
        $this->authorized($this->module, 'document');    

        $entity = $_SESSION['reimbursement']['items'][$key];

        echo json_encode($entity);
    }

    public function edit_item()
    {
        $this->authorized($this->module, 'document');

        $key=$this->input->post('item_id');
        if (isset($_POST) && !empty($_POST)){
        //$receipts_items_id = $this->input->post('item_id')
        $_SESSION['reimbursement']['items'][$key] = array(        
            'group'                   => $this->input->post('group'),
            'description'             => trim(strtoupper($this->input->post('description'))),
            'part_number'             => trim(strtoupper($this->input->post('part_number'))),
            'alternate_part_number'   => trim(strtoupper($this->input->post('alternate_part_number'))),
            'serial_number'           => trim(strtoupper($this->input->post('serial_number'))),
            'received_quantity'       => $this->input->post('received_quantity'),
            'received_unit_value'     => $this->input->post('received_unit_value'),
            'received_unit_value_dollar'     => $this->input->post('received_unit_value_dollar'),
            'minimum_quantity'        => $this->input->post('minimum_quantity'),
            'condition'               => $this->input->post('condition'),
            'expired_date'            => $this->input->post('expired_date'),
            'stores'                  => trim(strtoupper($this->input->post('stores'))),
            'purchase_order_number'   => trim(strtoupper($this->input->post('purchase_order_number'))),
            'purchase_order_item_id'  => trim($this->input->post('purchase_order_item_id')),
            'reference_number'        => trim(strtoupper($this->input->post('reference_number'))),
            'awb_number'              => trim(strtoupper($this->input->post('awb_number'))),
            'unit'                    => trim($this->input->post('unit')),
            'received_unit'           => trim($this->input->post('received_unit')),
            'remarks'                 => trim($this->input->post('remarks')),
            'kode_stok'               => trim($this->input->post('kode_stok')),
            'currency'                => trim($this->input->post('kurs')),        
            'unit_pakai'              => trim($this->input->post('unit_pakai')), 
            'isi'                     => trim($this->input->post('isi')),
            'quantity_order'          => $this->input->post('quantity_order'),
            'value_order'             => $this->input->post('value_order'),
            'no_expired_date'         => $this->input->post('no_expired_date'),
            'stock_in_stores_id'      => trim($this->input->post('stock_in_store_id')),
            'receipt_items_id'        => trim($this->input->post('receipt_items_id')),
            'tgl_nota'                => $this->input->post('tgl_nota'),        
            'internal_delivery_item_id'  => trim($this->input->post('internal_delivery_item_id')),
            'aircraft_register_number'  => trim($this->input->post('aircraft_register_number')),

        );
        }
        redirect($this->module['route'] .'/create');

    }

    public function del_item($key)
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        if (isset($_SESSION['reimbursement']['items']))
            unset($_SESSION['reimbursement']['items'][$key]);
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
}
