<?php defined('BASEPATH') or exit('No direct script access allowed');

class Expense_Closing_Payment extends MY_Controller
{
    protected $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['expense_closing_payment'];
        $this->load->helper($this->module['helper']);
        $this->load->model($this->module['model'], 'model');
        $this->data['module'] = $this->module;
        $this->load->library('email');
        $this->load->library('upload');
        $this->load->helper('string');
        if (empty($_SESSION['request_closing']['request_to']))
          $_SESSION['request_closing']['request_to'] = 1;
        if (empty($_SESSION['request_closing']['attachment']))
          $_SESSION['request_closing']['attachment'] = array();
    }

    public function index_data_source()
    {
        if ($this->input->is_ajax_request() === FALSE)
          redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'index') === FALSE) {
          $return['type'] = 'danger';
          $return['info'] = "You don't have permission to access this page!";
        } else {
            $entities = $this->model->getIndex();
            $data     = array();
            $no       = $_POST['start'];
            $total_idr      = array();
            $total_usd      = array();

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
                    }else if ($row['status'] == 'WAITING REVIEW BY VP FINANCE' && config_item('auth_role')=='VP FINANCE') {
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    }else{
                        $col[] = print_number($no);
                    }
                }else{
                    $col[] = print_number($no);
                }        
                $col[]  = '<a data-id="openPo" href="javascript:;" data-item-row="' . $row['id'] . '" data-href="'.site_url($this->module['route'] .'/print_pdf/'. $row['id']).'" target="_blank" >'.print_string($row['no_transaksi']).'</a>';
                $col[]  = print_date($row['tanggal']);
                $col[]  = print_string($row['no_cheque']);
                $col[]  = print_string($row['vendor']);
                $col[]  = print_string($row['currency']);
                $col[]  = '<a href="javascript:;" data-id="item" data-item-row="' . $row['id'] . '" data-href="' . site_url($this->module['route'] . '/change_account/' . $row['id']) . '">' . print_string($row['coa_kredit']).' '.print_string($row['akun_kredit']) . '</a>';
                if($row['currency']=='IDR'){
                    $col[]  = print_number($row['amount_paid'], 2);
                    $col[]  = print_number(0, 2);
                }else{
                    $col[]  = print_number(0, 2);
                    $col[]  = print_number($row['amount_paid'], 2);
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
                "recordsTotal"    => $this->model->countIndex(),
                "recordsFiltered" => $this->model->countIndexFiltered(),
                "data"            => $data,
                "total"           => array(
                  7 => print_number(array_sum($total_idr), 2),
                  8 => print_number(array_sum($total_usd), 2),
                )
            );
        }

        echo json_encode($result);
    }

    public function index()
    {
        $this->authorized($this->module, 'index');
        unset($_SESSION['request_closing']);

        $this->data['page']['title']            = $this->module['label'];
        $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
        $this->data['grid']['data_source']      = site_url($this->module['route'] . '/index_data_source');
        $this->data['grid']['fixed_columns']    = 2;
        $this->data['grid']['summary_columns']  = array(7,8);

        $this->data['grid']['order_columns']    = array();
        $this->data['grid']['order_columns']    = array(
          0   => array( 0 => 1,  1 => 'desc' ),
          1   => array( 0 => 2,  1 => 'desc' ),
          2   => array( 0 => 3,  1 => 'desc' ),
          3   => array( 0 => 4,  1 => 'desc' ),
          4   => array( 0 => 5,  1 => 'desc' ),
          5   => array( 0 => 6,  1 => 'desc' ),
          6   => array( 0 => 7,  1 => 'desc' ),
          7   => array( 0 => 8,  1 => 'desc' ),
        );

        $this->render_view($this->module['view'] . '/index');
    }

    public function info($id)
    {
        if ($this->input->is_ajax_request() === FALSE)
        redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'info') === FALSE) {
            $return['type'] = 'denied';
            $return['info'] = "You don't have permission to access this data. You may need to login again.";
        } else {
            $entity = $this->model->findById($id);
            $account = getAccountByCode($entity['coa_kredit']);

            $this->data['entity'] = $entity;
            $this->data['account'] = $account;
            $this->data['id']       = $id;

            $return['type'] = 'success';
            $return['info'] = $this->load->view($this->module['view'] . '/info', $this->data, TRUE);
        }

        echo json_encode($return);
    }

    public function closing_payment($expense_request_id)
    {
        $this->authorized($this->module, 'document');
        $entity = $this->model->findExpenseRequestByid($expense_request_id);

        // if (isset($_SESSION['request_closing']) === FALSE){
            $_SESSION['request_closing']['items']               = $entity['items'];
            $_SESSION['request_closing']['type']                = (config_item('auth_role')=='PIC STAFF')? 'CASH':'BANK';
            $_SESSION['request_closing']['document_number']     = payment_request_last_number($_SESSION['request_closing']['type']);
            $_SESSION['request_closing']['date']                = date('Y-m-d');
            $_SESSION['request_closing']['purposed_date']       = date('Y-m-d');
            $_SESSION['request_closing']['notes']               = $entity['notes'];
            $_SESSION['request_closing']['closing_notes']       = NULL;
            $_SESSION['request_closing']['account']             = NULL;
            $_SESSION['request_closing']['id']                  = $expense_request_id;
            $_SESSION['request_closing']['created_by']          = config_item('auth_person_name');
            $_SESSION['request_closing']['currency']            = "IDR";
            $_SESSION['request_closing']['vendor']              = NULL;
            $_SESSION['request_closing']['total_amount']        = 0;
            $_SESSION['request_closing']['coa_kredit']          = NULL;
            $_SESSION['request_closing']['category']            = 'EXP';
        // }        
        // $this->render_view($this->module['view'] .'/create');
        redirect($this->module['route'] .'/create');
    }

    public function create($category = NULL)
    {
        $this->authorized($this->module, 'document');

        if ($category !== NULL) {
            $category = urldecode($category);

            $_SESSION['request_closing']['items']               = array();
            $_SESSION['request_closing']['category']            = $category;
            $_SESSION['request_closing']['type']                = (config_item('auth_role')=='PIC STAFF')? 'CASH':'BANK';
            $_SESSION['request_closing']['document_number']     = payment_request_last_number($_SESSION['request_closing']['type']);
            $_SESSION['request_closing']['date']                = date('Y-m-d');
            $_SESSION['request_closing']['purposed_date']       = date('Y-m-d');
            $_SESSION['request_closing']['notes']               = NULL;
            $_SESSION['request_closing']['closing_notes']       = NULL;
            $_SESSION['request_closing']['account']             = NULL;
            $_SESSION['request_closing']['created_by']          = config_item('auth_person_name');
            $_SESSION['request_closing']['currency']            = "IDR";
            $_SESSION['request_closing']['vendor']              = NULL;
            $_SESSION['request_closing']['total_amount']        = 0;
            $_SESSION['request_closing']['total_amount']        = 0;
            $_SESSION['request_closing']['coa_kredit']          = NULL;

            redirect($this->module['route'] . '/create');
        }

        if (!isset($_SESSION['request_closing']))
            redirect($this->module['route']);

        $this->data['page']['content']    = $this->module['view'] . '/create';
        $this->data['page']['title']      = 'create payment request';

        $this->render_view($this->module['view'] . '/create');
    }

    public function save()
    {
        if ($this->input->is_ajax_request() == FALSE)
          redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'document') == FALSE){
            $data['success'] = FALSE;
            $data['message'] = 'You are not allowed to save this Document!';
        } else {

            // $_SESSION['request_closing']['document_number'] = request_payment_last_number().request_payment_format_number($_SESSION['request_closing']['type']);
            $document_number = $_SESSION['request_closing']['document_number'].payment_request_format_number($_SESSION['request_closing']['type']);

            $errors = array();

            if (isset($_SESSION['request_closing']['edit'])) {
                if ($_SESSION['request_closing']['edit'] != $document_number && $this->model->isDocumentNumberExists($document_number)) {
                    $errors[] = 'Duplicate Document Number: ' . $document_number. ' !';
                }
            } else {
                if ($this->model->isDocumentNumberExists($document_number)) {
                    $_SESSION['request_closing']['document_number']     = payment_request_last_number($_SESSION['request_closing']['type']);
                    $document_number = $_SESSION['request_closing']['document_number'] . payment_request_format_number($_SESSION['request_closing']['type']);
                    // $errors[] = 'Duplicate Document Number: ' . $_SESSION['poe']['document_number'] . ' !';
                }
            }

            if (!isset($_SESSION['request_closing']['vendor']) || empty($_SESSION['request_closing']['vendor'])){
                $errors[] = 'Pay To can not null! Please fill Paid to!';
            }

            // if (!isset($_SESSION['request_closing']['coa_kredit']) || empty($_SESSION['request_closing']['coa_kredit'])){
            //     $errors[] = 'Account can not null! Please select account!';
            // }
            if (!empty($errors)){
                $data['success'] = FALSE;
                $data['message'] = implode('<br />', $errors);
            } else {
                if ($this->model->save()){
                    unset($_SESSION['request_closing']);

                    $data['success'] = TRUE;
                    $data['message'] = 'Payment '. $document_number .' has been purposed to payment. You will redirected now.';
                } else {
                    $data['success'] = FALSE;
                    $data['message'] = 'Error while saving this document. Please ask Technical Support.';
                }
            }
            
        }

        echo json_encode($data);
    }

    public function set_doc_number()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        $_SESSION['request_closing']['document_number'] = $_GET['data'];
    }

    public function set_type_transaction()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        $_SESSION['request_closing']['type'] = $_GET['data'];
        $_SESSION['request_closing']['coa_kredit'] = null;
    }

    public function set_date()
    {
        if ($this->input->is_ajax_request() === FALSE)
          redirect($this->modules['secure']['route'] . '/denied');

        $_SESSION['request_closing']['date'] = $_GET['data'];
    }

    public function set_purposed_date()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        $_SESSION['request_closing']['purposed_date'] = $_GET['data'];
    }

    public function set_notes()
    {
        if ($this->input->is_ajax_request() === FALSE)
          redirect($this->modules['secure']['route'] . '/denied');

        $_SESSION['request_closing']['closing_notes'] = $_GET['data'];
    }

    public function set_account()
    {
        if ($this->input->is_ajax_request() === FALSE)
        redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['request_closing']['coa_kredit'] = $_GET['data'];
    }

    public function set_vendor()
    {
        if ($this->input->is_ajax_request() === FALSE)
        redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['request_closing']['vendor'] = $_GET['data'];
    }

    public function set_default_currency()
    {
        if ($this->input->is_ajax_request() === FALSE)
        redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['request_closing']['currency'] = $_GET['data'];
        $_SESSION['request_closing']['coa_kredit'] = null;
    }

    public function discard()
    {
        $this->authorized($this->module['permission']['document']);

        unset($_SESSION['request_closing']);

        redirect($this->module['route']);
    }

    public function get_accounts()
    {
        if ($this->input->is_ajax_request() === FALSE)
          redirect($this->modules['secure']['route'] . '/denied');
        // $vendor = $this->input->post('vendor');

        $type = $this->input->post('type');
        $accounts = getAccount($type);
        $option = '<option>--SELECT ACCOUNT--</option>';
        foreach ($accounts as $key => $account) {
          $option .= '<option value="' . $account['coa'] . '">' . $account['coa'] . ' - ' . $account['group'] . '</option>';
        }
        $format_number = payment_request_format_number($type);
        $document_number = payment_request_last_number($type);

        $return = [
          'account' => $option,
          'format_number' => $format_number,
          'document_number' => $document_number
        ];
        echo json_encode($return);
    }

    public function print_pdf($id)
    {
        $this->authorized($this->module, 'print');

        $entity = $this->model->findById($id);

        $this->data['entity']           = $entity;
        $this->data['page']['title']    = ($entity->status=='PAID')? $entity->type.' PAYMENT VOUCHER':strtoupper($this->module['label']);
        $this->data['page']['content']  = $this->module['view'] .'/print_pdf';

        $html = $this->load->view($this->pdf_theme, $this->data, true);

        $pdfFilePath = str_replace('/', '-', $entity['document_number']) .".pdf";

        $this->load->library('m_pdf');

        $pdf = $this->m_pdf->load(null, 'A4-L');
        $pdf->WriteHTML($html);
        $pdf->Output($pdfFilePath, "I");
    }

    public function multi_approve()
    {
        $id_purchase_order = $this->input->post('id_expense_request');
        $id_purchase_order = str_replace("|", "", $id_purchase_order);
        $id_purchase_order = substr($id_purchase_order, 0, -1);
        $id_purchase_order = explode(",", $id_purchase_order);

        $total = 0;
        $success = 0;
        $failed = sizeof($id_purchase_order);
        $x = 0;
        $level = 13;
        
        $success = $this->model->approve($id_purchase_order);
        // foreach ($id_purchase_order as $key) {
        //   if ($this->model->approve($key)) {
        //     $total++;
        //     $success++;
        //     $failed--;
        //   }
        //   $x++;
        // }
        // if ($success > 0) {
        //   $this->session->set_flashdata('alert', array(
        //     'type' => 'success',
        //     'info' => " data has been approved!"
        //   ));
        // }
        // if ($failed > 0) {
        //   $this->session->set_flashdata('alert', array(
        //     'type' => 'danger',
        //     'info' => "There are " . $failed . " errors"
        //   ));
        // }
        if ($success) {
          $result['status'] = 'success';
        } else {
          //$this->sendEmailHOS();
          $result['status'] = 'failed';
        }
        echo json_encode($result);
    }

    public function multi_reject()
    {
        $id_purchase_order = $this->input->post('id_purchase_order');
        $id_purchase_order = str_replace("|", "", $id_purchase_order);
        $id_purchase_order = substr($id_purchase_order, 0, -1);
        $id_purchase_order = explode(",", $id_purchase_order);

        $str_notes = $this->input->post('notes');
        $notes = str_replace("|", "", $str_notes);
        $notes = substr($notes, 0, -3);
        $notes = explode("##,", $notes);

        $total = 0;
        $success = 0;
        $failed = sizeof($id_purchase_order);
        $x = 0;

        $rejected = $this->model->reject($id_purchase_order,$notes);
        // foreach ($id_purchase_order as $key) {
        //   if ($this->model->rejected($key)) {
        //     $total++;
        //     $success++;
        //     $failed--;
        //     // $this->model->send_mail_approved($key,'approved');
        //   }
        //   $x++;
        // }
        // if ($success > 0) {
        //   // $id_role = 13;
        //   $this->session->set_flashdata('alert', array(
        //     'type' => 'success',
        //     'info' => $success . " data has been rejected!"
        //   ));
        // }
        // if ($failed > 0) {
        //   $this->session->set_flashdata('alert', array(
        //     'type' => 'danger',
        //     'info' => "There are " . $failed . " errors"
        //   ));
        // }
        if ($rejected) {
          $result['status'] = 'success';
        } else {
          //$this->sendEmailHOS();
          $result['status'] = 'failed';
        }
        echo json_encode($result);
    }

    public function bayar($id)
    {
        $this->authorized($this->module, 'payment');

        // if ($category !== NULL){
        $item       = $this->model->findById($id);

        $_SESSION['payment']                          = $item;
        $_SESSION['payment']['no_transaksi']          = $item['no_transaksi'];
        $_SESSION['payment']['vendor']                = $item['vendor'];
        $_SESSION['payment']['currency']              = $item['currency'];
        $_SESSION['payment']['base']                  = $item['base'];
        $_SESSION['payment']['po_payment_id']         = $item['id'];
        $_SESSION['payment']['total_amount']          = 0;
        $_SESSION['payment']['attachment']            = array();
        foreach ($_SESSION['payment']['request'] as $i => $request){
          $_SESSION['payment']['total_amount']          = $_SESSION['payment']['total_amount']+$request['amount_paid'];
        }
        // $_SESSION['payment']['total_amount']          = $item['items']->sum('amount_paid');
        

        $this->render_view($this->module['view'] . '/bayar');
    }

    public function attachment()
    {
        // $this->authorized($this->module, 'manage_attachment');
        $this->data['page']['title']    = "Attachment Payment";
        $this->data['type_att']       = 'payment';
        $this->render_view($this->module['view'] . '/attachment');
    }

    public function add_attachment()
    {
        $result["status"] = 0;
        $date = new DateTime();
        // $config['file_name'] = $date->getTimestamp().random_string('alnum', 5);
        $config['upload_path'] = 'attachment/expense_payment/';
        $config['allowed_types'] = 'jpg|png|jpeg|doc|docx|xls|xlsx|pdf';
        $config['max_size']  = 2000;

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('attachment')) {
          $error = array('error' => $this->upload->display_errors());
        } else {

          $data = array('upload_data' => $this->upload->data());
          $url = $config['upload_path'] . $data['upload_data']['orig_name'];
          array_push($_SESSION["payment"]["attachment"], $url);
          $result["status"] = 1;
        }
        echo json_encode($result);
    }

    public function save_pembayaran()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] . '/denied');
        
        $save = $this->model->save_pembayaran();
        if ($save) {
            unset($_SESSION['payment']);
            $result["status"] = "success";
        } else {
            $result["status"] = "failed";
        }
        echo json_encode($result);
    }

    public function manage_attachment($id)
    {
        // $this->authorized($this->module, 'manage_attachment');

        $this->data['manage_attachment'] = $this->model->listAttachments($id);
        $this->data['page']['title']    = "Manage Attachment Expense Payment";
        $this->data['id'] = $id;
        $this->render_view($this->module['view'] . '/manage_attachment');
    }

    public function add_item()
    {
        $this->authorized($this->module, 'document');

        $this->data['entities'] = $this->model->listRequests();
        $this->data['page']['title']            = 'Add Items';

        $this->render_view($this->module['view'] . '/add_item');
    }

    public function add_selected_item()
    {
        if ($this->input->is_ajax_request() == FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'document') == FALSE) {
            $data['success'] = FALSE;
            $data['message'] = 'You are not allowed to save this Document!';
        } else {
            if (isset($_POST['request_id']) && !empty($_POST['request_id'])) {
                $_SESSION['request_closing']['items'] = array();
                $total_amount = array();
                foreach ($_POST['request_id'] as $key => $request_id) {
                    // $item_id_explode  = explode('-', $item_id);
                    // $po_id = $item_id_explode[0];
                    // $po_item_id = $item_id_explode[1];
                    $request = $this->model->infoRequest($request_id);

                    $_SESSION['request_closing']['items'][$request_id] = array(
                        'request_id'                        => $request['id'],
                        'cost_center_name'                  => $request['cost_center_name'],
                        'notes'                             => $request['notes'],
                        'created_by'                        => $request['created_by'],
                        'status'                            => $request['status'],
                        'required_date'                     => $request['required_date'],
                        'pr_date'                           => $request['pr_date'],            
                        'pr_number'                         => $request['pr_number'],            
                        'process_amount'                    => $request['process_amount'],            
                        'total'                             => $request['total'],            
                        'amount'                            => $request['amount'],
                        'reference_ipc'                     => $request['reference_ipc']
                    );
                    $_SESSION['request_closing']['items'][$request_id]['request_detail'] = $request['items'];
                    $total_amount[] = $request['total']-$request['process_amount'];
                }

                $_SESSION['request_closing']['total_amount'] = array_sum($total_amount);

                $data['success'] = TRUE;
            } else {
                $data['success'] = FALSE;
                $data['message'] = 'Please select any request!';
            }
        }

        echo json_encode($data);
    }

    public function search_vendor()
    {
        if ($this->input->is_ajax_request() === FALSE)
          redirect($this->modules['secure']['route'] .'/denied');

        $entities = search_vendors_by_currency();

        foreach ($entities as $vendor){
            $arr_result[] = $vendor->vendor;
        }

        echo json_encode($arr_result);
    }

    public function cancel_ajax()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'cancel') === FALSE) {
            $alert['type']  = 'danger';
            $alert['info']  = 'You are not allowed to cancel this request!';
        } else {
            if ($this->model->cancel()) {
                $alert['type'] = 'success';
                $alert['info'] = 'Payment Request canceled.';
                $alert['link'] = site_url($this->module['route']);
            } else {
                $alert['type'] = 'danger';
                $alert['info'] = 'There are error while canceling data. Please try again later.';
            }
        }

        echo json_encode($alert);
    }
}
