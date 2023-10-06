<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Spd_Payment extends MY_Controller
{
    protected $module;
    protected $id_item=0;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['spd_payment'];
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
            $entities = $this->model->getIndexForPayment();
            $data     = array();
            $no       = $_POST['start'];
            $quantity     = array();
            $unit_value   = array();
            $total_value  = array();

            foreach ($entities as $row) {
                // $attachment = $this->model->checkAttachment($row['id']);
                $attachment = 0;
                $no++;
                $col = array();
                if (is_granted($this->module, 'approval') === TRUE) {
                    if ($row['status'] == 'WAITING REVIEW BY FIN MNG' && config_item('auth_role')=='FINANCE MANAGER') {
                        if(config_item('auth_warehouse')=='JAKARTA'){
                            if($row['base']=='JAKARTA'){
                            $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                            }else{
                                $col[] = print_number($no);
                            }
                        }else{
                            if($row['base']!='JAKARTA'){
                                $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                            }else{
                                $col[] = print_number($no);
                            }
                        }
                    }else if ($row['status'] == 'WAITING APPROVAL BY HOS' && config_item('auth_role')=='HEAD OF SCHOOL') {
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    }else{
                        $col[] = print_number($no);
                    }
                }else{
                    $col[] = print_number($no);
                }        
                $col[]  = '<a class="link" data-id="openPo" href="javascript:;" data-item-row="' . $row['id'] . '" data-href="'.site_url($this->module['route'] .'/print_pdf/'. $row['id']).'" target="_blank" >'.print_string($row['no_transaksi']).'</a>';
                $col[]  = print_date($row['tanggal']);
                $col[]  = print_string($row['no_cheque']);
                $col[]  = print_string($row['vendor']);
                $col[]  = print_string($row['currency']);
                $col[]  = '<a href="javascript:;" data-id="item" data-item-row="' . $row['id'] . '" data-href="' . site_url($this->module['route'] . '/change_account/' . $row['id']) . '">' . print_string($row['coa_kredit']).' '.print_string($row['akun_kredit']) . '</a>';
                if($row['currency']=='IDR'){
                    $col[]  = (in_array($row['status'],['PAID','OPEN']))?print_number($row['amount_paid'], 2):print_number($row['amount_request'], 2);
                    $col[]  = print_number(0, 2);
                }else{
                    $col[]  = print_number(0, 2);
                    $col[]  = (in_array($row['status'],['PAID','OPEN']))?print_number($row['amount_paid'], 2):print_number($row['amount_request'], 2);
                }        
                $col[]  = print_string($row['status']);
                $col[] = $attachment == 0 ? '' : '<a href="#" data-id="' . $row["id"] . '" class="btn btn-icon-toggle btn-info btn-sm ">
                               <i class="fa fa-eye"></i>
                             </a>';
                $col[]  = print_string($row['base']);
                $col[]  = print_string($row['notes']);
                if($row['status'] == 'WAITING REVIEW BY FIN MNG' || $row['status'] == 'WAITING REVIEW BY VP FINANCE'){
                    if (is_granted($this->module, 'approval') === TRUE) {
                        $col[] = '<input type="text" id="note_' . $row['id'] . '" autocomplete="off"/>';
                    }else{
                        $col[] = $row['approval_notes'];
                    }
                    
                }elseif($row['status']=='REJECTED'){
                    $col[] = $row['rejected_notes'];
                }else{
                    $col[] = $row['approval_notes'];
                }

                if($row['currency']=='IDR'){
                    $total_idr[] = $row['amount_paid'];
                }else{
                    $total_usd[] = $row['amount_paid'];
                }
            

                $col['DT_RowId'] = 'row_' . $row['id'];
                $col['DT_RowData']['pkey']  = $row['id'];

                if ($this->has_role($this->module, 'info')) {
                    // $col['DT_RowAttr']['onClick']     = '$(this).popup();';
                    $col['DT_RowAttr']['onClick']     = '';
                    $col['DT_RowAttr']['data-id']     = $row['id'];
                    $col['DT_RowAttr']['data-target'] = '#data-modal';
                    $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] . '/info/' . $row['id']);
                }

                $data[] = $col;
            }

            $result = array(
                "draw"            => $_POST['draw'],
                "recordsTotal"    => $this->model->countIndexForPayment(),
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
        $this->data['grid']['column']           = $this->model->getSelectedColumnsForPayment();
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

        $_SESSION['spd_payment']['document_number'] = $number;
    }

    public function set_default_currency()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['spd_payment']['currency'] = $_GET['data'];
    }

    public function set_type_transaction()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['spd_payment']['type'] = $_GET['data'];
        $_SESSION['spd_payment']['coa_kredit'] = null;
    }

    public function set_date()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['spd_payment']['date'] = $_GET['data'];
    }

    public function set_purposed_date()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['spd_payment']['purposed_date'] = $_GET['data'];
    }

    public function set_account()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['spd_payment']['coa_kredit'] = $_GET['data'];
    }

    public function set_vendor()
    {
        if ($this->input->is_ajax_request() === FALSE)
        redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['spd_payment']['vendor'] = $_GET['data'];
    }

    

    public function set_notes()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['spd_payment']['notes'] = $_GET['data'];
    }

    public function create($category = NULL)
    {
        $this->authorized($this->module, 'create');

        if ($category !== NULL) {
            $category = urldecode($category);

            $_SESSION['spd_payment']['items']               = array();
            $_SESSION['spd_payment']['category']            = $category;
            $_SESSION['spd_payment']['type']                = (config_item('auth_role')=='PIC STAFF')? 'CASH':'BANK';
            $_SESSION['spd_payment']['document_number']     = payment_request_last_number($_SESSION['spd_payment']['type']);
            $_SESSION['spd_payment']['format_number']       = payment_request_format_number($_SESSION['spd_payment']['type']);
            $_SESSION['spd_payment']['date']                = date('Y-m-d');
            $_SESSION['spd_payment']['purposed_date']       = date('Y-m-d');
            $_SESSION['spd_payment']['notes']               = NULL;
            $_SESSION['spd_payment']['closing_notes']       = NULL;
            $_SESSION['spd_payment']['account']             = NULL;
            $_SESSION['spd_payment']['created_by']          = config_item('auth_person_name');
            $_SESSION['spd_payment']['currency']            = "IDR";
            $_SESSION['spd_payment']['vendor']              = NULL;
            $_SESSION['spd_payment']['total_amount']        = 0;
            $_SESSION['spd_payment']['total_amount']        = 0;
            $_SESSION['spd_payment']['coa_kredit']          = NULL;

            redirect($this->module['route'] . '/create');
        }

        if (!isset($_SESSION['spd_payment']))
            redirect($this->module['route']);

        $this->data['page']['content']    = $this->module['view'] . '/create';
        $this->data['page']['title']      = 'create payment request';

        $this->render_view($this->module['view'] . '/create');
    }

    public function edit($id)
    {
        $this->authorized($this->module, 'create');

        $entity = $this->model->findByIdForPayment($id);

        $document_number    = sprintf('%06s', substr($entity['document_number'], 0, 6));
        $format_number      = substr($entity['document_number'], 6, 9);
        $revisi             = get_count_revisi($document_number.$format_number,'PAYMENT');

        if (isset($_SESSION['spd_payment']) === FALSE){
            
            $_SESSION['spd_payment']                              = $entity;
            $_SESSION['spd_payment']['id']                        = $id;
            $_SESSION['spd_payment']['edit_type']                 = 'edit';
            $_SESSION['spd_payment']['edit']                      = $entity['document_number'];
            $_SESSION['spd_payment']['document_number']           = $document_number;
            $_SESSION['spd_payment']['format_number']             = $format_number.'-R'.$revisi;
            $_SESSION['spd_payment']['department_id']             = $department_id;
            $_SESSION['spd_payment']['date']                      = $entity['tanggal'];
            $_SESSION['spd_payment']['items']                     = $entity['request'];
            
        }

        redirect($this->module['route'] .'/create');
        // $this->render_view($this->module['view'] .'/edit');
    }

    public function save()
    {
        if ($this->input->is_ajax_request() == FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'create') == FALSE){
            $data['success'] = FALSE;
            $data['message'] = 'You are not allowed to save this Document!';
        } else {

            $document_number = $_SESSION['spd_payment']['document_number'] . $_SESSION['spd_payment']['format_number'];
            $errors = array();

            if (!empty($errors)){
                $data['success'] = FALSE;
                $data['message'] = implode('<br />', $errors);
            } else {
                if ($this->model->savePayment()){
                    unset($_SESSION['spd_payment']);
        
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

        $this->data['entities'] = $this->model->listSpd();
        $this->data['page']['title']            = 'Add Items';

        $this->render_view($this->module['view'] . '/add_item');
    }

    public function add_selected_item()
    {
        if ($this->input->is_ajax_request() == FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'create') == FALSE) {
            $data['success'] = FALSE;
            $data['message'] = 'You are not allowed to save this Document!';
        } else {
            if (isset($_POST['spd_id']) && !empty($_POST['spd_id'])) {
                $_SESSION['spd_payment']['items'] = array();
                $total_amount = array();
                $akun_advance_dinas = get_set_up_akun(6);
                foreach ($_POST['spd_id'] as $key => $spd_id) {
                    
                    $spd = $this->model->infoSpd($spd_id);

                    $_SESSION['spd_payment']['items'][$spd_id] = array(
                        'spd_id'                => $spd['id'],
                        'spd_number'            => $spd['document_number'],
                        'spd_amount'            => $spd['total'],
                        'spd_date'              => $spd['date'],
                        'spd_person_incharge'   => $spd['person_name'],
                        'amount_paid'           => 0,
                        'remarks'               => null,
                        'account_code'          => $akun_advance_dinas->coa
                    );
                }

                // $_SESSION['spd_payment']['total_amount'] = array_sum($total_amount);

                $data['success'] = TRUE;
            } else {
                $data['success'] = FALSE;
                $data['message'] = 'Please select any request!';
            }
        }

        echo json_encode($data);
    }

    public function edit_item()
    {
        $this->authorized($this->module, 'create');

        $this->render_view($this->module['view'] . '/edit_item');
    }

    public function update_item()
    {
        if ($this->input->is_ajax_request() == FALSE)
        redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'create') == FALSE) {
        $data['success'] = FALSE;
        $data['message'] = 'You are not allowed to save this Document!';
        } else {
        if (isset($_POST['spd']) && !empty($_POST['spd'])) {
            foreach ($_POST['spd'] as $id => $spd) {
                $amount_paid = floatval($spd['amount_paid']);

                $_SESSION['spd_payment']['items'][$id]['amount_paid'] = $amount_paid;
            }

            $data['success'] = TRUE;
        } else {
            $data['success'] = FALSE;
            $data['message'] = 'No data to update!';
        }
        }

        echo json_encode($data);
    }

    public function info($id)
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        if (is_granted($this->module, 'info') === FALSE){
            $return['type'] = 'denied';
            $return['info'] = "You don't have permission to access this data. You may need to login again.";
        } else {
            $entity = $this->model->findByIdForPayment($id);

            $this->data['entity'] = $entity;            
            $this->data['id']       = $id;

            $return['type'] = 'success';
            $return['info'] = $this->load->view($this->module['view'] .'/info', $this->data, TRUE);
        }

        echo json_encode($return);
    }

    public function print_pdf($id)
    {
        $this->authorized($this->module, 'print');

        $entity = $this->model->findByIdForPayment($id);

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

    public function save_approve()
    {
        if ($this->input->is_ajax_request() == FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'approval') == FALSE){
            $data['success'] = FALSE;
            $data['message'] = 'You are not allowed to save this Document!';
        } else {

            $document_number = $_SESSION['spd_payment']['document_number'] . $_SESSION['spd_payment']['format_number'];
            $errors = array();

            if ($_SESSION['spd_payment']['head_dept']==NULL || $_SESSION['spd_payment']['head_dept']=='') {
                $errors[] = 'Attention!! Please select one of Head Dept for Approval';
            }

            if ($_SESSION['spd_payment']['notes']==NULL || $_SESSION['spd_payment']['notes']=='') {
                $errors[] = 'Attention!! Please Fill Notes!!';
            }

            if (!empty($errors)){
                $data['success'] = FALSE;
                $data['message'] = implode('<br />', $errors);
            } else {
                if ($this->model->save()){
                    unset($_SESSION['spd_payment']);
        
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
            $_SESSION['spd_payment']['id']                        = $id;
            $_SESSION['spd_payment']['edit']                      = $entity['document_number'];
            $_SESSION['spd_payment']['document_number']           = $entity['document_number'];
            $_SESSION['spd_payment']['format_number']             = $format_number.'-R'.$revisi;
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

            $document_number = $_SESSION['spd_payment']['document_number'];
            $errors = array();

            if (!empty($errors)){
                $data['success'] = FALSE;
                $data['message'] = implode('<br />', $errors);
            } else {
                if ($this->model->save_hr_approve()){
                    unset($_SESSION['spd_payment']);
        
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

        unset($_SESSION['spd_payment']);

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
        $document_id = $this->input->post('id_expense_request');
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

        $save_approval = $this->model->approveForPayment($document_id, $notes);
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

        if ($save_approval['success'] > 0) {
            // $this->model->send_mail_approval($id_expense_request, 'approve', config_item('auth_person_name'),$notes);
            $this->session->set_flashdata('alert', array(
                'type' => 'success',
                'info' => $save_approval['success'] . " data has been update!"
            ));
        }
        if ($save_approval['failed'] > 0) {
            $this->session->set_flashdata('alert', array(
                'type' => 'danger',
                'info' => "There are " . $failed . " errors"
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
        $document_id = $this->input->post('id_expense_request');
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
        
        if ($save_approval['status']) {
            $result['status'] = 'success';
        } else {
            $result['status'] = 'failed';
        }
        echo json_encode($result);
    }

    public function bayar($id)
    {
        $this->authorized($this->module, 'payment');

        // if ($category !== NULL){
        $item       = $this->model->findByIdForPayment($id);

        $_SESSION['bayar']                          = $item;
        $_SESSION['bayar']['no_transaksi']          = $item['document_number'];
        $_SESSION['bayar']['vendor']                = $item['vendor'];
        $_SESSION['bayar']['currency']              = $item['currency'];
        $_SESSION['bayar']['base']                  = $item['base'];
        $_SESSION['bayar']['po_payment_id']         = $item['id'];
        $_SESSION['bayar']['total_amount']          = 0;
        $_SESSION['bayar']['payment_number']        = payment_request_last_number($item['type']);
        $_SESSION['bayar']['format_number']         = payment_request_format_number($item['type']);
        $_SESSION['bayar']['attachment']            = array();
        foreach ($_SESSION['bayar']['request'] as $i => $request){
          $_SESSION['bayar']['total_amount']          = $_SESSION['bayar']['total_amount']+$request['amount_paid'];
        }
        // $_SESSION['payment']['total_amount']          = $item['items']->sum('amount_paid');
        

        $this->render_view($this->module['view'] . '/bayar');
    }

    public function save_pembayaran()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] . '/denied');
        
        $save = $this->model->save_pembayaran();
        if ($save) {
            unset($_SESSION['bayar']);
            $result["status"] = "success";
        } else {
            $result["status"] = "failed";
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
