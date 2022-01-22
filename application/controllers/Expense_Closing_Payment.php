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
                    if ($row['status'] == 'WAITING CHECK BY FIN SPV' && config_item('auth_role')=='FINANCE SUPERVISOR') {
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    }else if ($row['status'] == 'WAITING REVIEW BY FIN MNG' && config_item('auth_role')=='FINANCE MANAGER') {
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
                    }else if ($row['status'] == 'WAITING REVIEW BY HOS' && config_item('auth_role')=='HEAD OF SCHOOL') {
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    }else if ($row['status'] == 'WAITING REVIEW BY VP FINANCE' && config_item('auth_role')=='VP FINANCE') {
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    }else if ($row['status'] == 'WAITING REVIEW BY CEO' && config_item('auth_role')=='CHIEF OPERATION OFFICER') {
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    }else if ($row['status'] == 'WAITING REVIEW BY CFO' && config_item('auth_role')=='CHIEF OF FINANCE') {
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
                // $col[]  = print_string($row['document_number']);
                $col[]  = print_string($row['vendor']);
                // $col[]  = print_string($row['part_number']);
                // $col[]  = print_string($row['description']);
                $col[]  = print_string($row['currency']);
                // $col[]  = print_string($row['coa_kredit']).' '.print_string($row['akun_kredit']);
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
                $col[]  = print_string($row['created_by']);
                $col[]  = print_date($row['created_at']);

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
        unset($_SESSION['payment_request']);

        $this->data['page']['title']            = $this->module['label'];
        $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
        $this->data['grid']['data_source']      = site_url($this->module['route'] . '/index_data_source');
        $this->data['grid']['fixed_columns']    = 2;
        $this->data['grid']['summary_columns']  = array(7,8);

        $this->data['grid']['order_columns']    = array();
        // $this->data['grid']['order_columns']    = array(
        //   0   => array( 0 => 1,  1 => 'desc' ),
        //   1   => array( 0 => 2,  1 => 'desc' ),
        //   2   => array( 0 => 3,  1 => 'asc' ),
        //   3   => array( 0 => 4,  1 => 'asc' ),
        //   4   => array( 0 => 5,  1 => 'asc' ),
        //   5   => array( 0 => 6,  1 => 'asc' ),
        //   6   => array( 0 => 7,  1 => 'asc' ),
        //   7   => array( 0 => 8,  1 => 'asc' ),
        // );

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
            $_SESSION['request_closing']['document__number']    = request_payment_last_number();
            $_SESSION['request_closing']['date']                = date('Y-m-d');
            $_SESSION['request_closing']['purposed_date']       = date('Y-m-d');
            $_SESSION['request_closing']['notes']               = $entity['notes'];
            $_SESSION['request_closing']['closing_notes']       = NULL;
            $_SESSION['request_closing']['account']             = NULL;
            $_SESSION['request_closing']['id']                  = $expense_request_id;
            $_SESSION['request_closing']['type']                = 'BANK';
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
            $_SESSION['request_closing']['document_number']     = request_payment_last_number();
            $_SESSION['request_closing']['date']                = date('Y-m-d');
            $_SESSION['request_closing']['purposed_date']       = date('Y-m-d');
            $_SESSION['request_closing']['created_by']          = config_item('auth_person_name');
            $_SESSION['request_closing']['currency']            = "IDR";
            $_SESSION['request_closing']['vendor']              = NULL;
            $_SESSION['request_closing']['notes']               = NULL;
            $_SESSION['request_closing']['total_amount']        = 0;

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

            $_SESSION['request_closing']['document_number'] = request_payment_last_number().request_payment_format_number($_SESSION['request_closing']['type']);

            $errors = array();

            if (!isset($_SESSION['request_closing']['vendor']) || empty($_SESSION['request_closing']['vendor'])){
                $errors[] = 'Pay To can not null! Please fill Paid to!';
            }

            if (!isset($_SESSION['request_closing']['coa_kredit']) || empty($_SESSION['request_closing']['coa_kredit'])){
                $errors[] = 'Account can not null! Please select account!';
            }
            if (!empty($errors)){
                $data['success'] = FALSE;
                $data['message'] = implode('<br />', $errors);
            } else {
                if ($this->model->save()){
                    unset($_SESSION['request_closing']);

                    $data['success'] = TRUE;
                    $data['message'] = 'Expense '. $_SESSION['payment_request']['document_number'] .' has been purposed to payment. You will redirected now.';
                } else {
                    $data['success'] = FALSE;
                    $data['message'] = 'Error while saving this document. Please ask Technical Support.';
                }
            }
            
        }

        echo json_encode($data);
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
        $format_number = request_payment_format_number($type);

        $return = [
          'account' => $option,
          'format_number' => $format_number
        ];
        echo json_encode($return);
    }

    // public function create_2($category = NULL)
    // {
    //     $this->authorized($this->module, 'document');

    //     if ($category !== NULL) {
    //       $category = urldecode($category);

    //       $_SESSION['payment_request']['po']                  = array();
    //       $_SESSION['payment_request']['category']            = $category;
    //       $_SESSION['payment_request']['type']                = 'BANK';
    //       $_SESSION['payment_request']['document_number']     = payment_request_last_number();
    //       $_SESSION['payment_request']['date']                = date('Y-m-d');
    //       $_SESSION['payment_request']['purposed_date']       = date('Y-m-d');
    //       $_SESSION['payment_request']['created_by']          = config_item('auth_person_name');
    //       $_SESSION['payment_request']['currency']            = "IDR";
    //       $_SESSION['payment_request']['vendor']              = NULL;
    //       $_SESSION['payment_request']['notes']               = NULL;
    //       $_SESSION['payment_request']['total_amount']        = 0;
    //       $_SESSION['payment_request']['coa_kredit']          = NULL;

    //       redirect($this->module['route'] . '/create_2');
    //     }

    //     if (!isset($_SESSION['payment_request']))
    //       redirect($this->module['route']);

    //     $this->data['page']['content']    = $this->module['view'] . '/create-2';
    //     $this->data['page']['title']      = 'create payment request';

    //     $this->render_view($this->module['view'] . '/create-2');
    // }

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

        $pdf = $this->m_pdf->load(null, 'A4-L');
        $pdf->WriteHTML($html);
        $pdf->Output($pdfFilePath, "I");
    }
}
