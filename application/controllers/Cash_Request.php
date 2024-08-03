<?php defined('BASEPATH') or exit('No direct script access allowed');

class Cash_Request extends MY_Controller
{
  protected $module;
  protected $id_item = 0;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['cash_request'];
    $this->load->model($this->module['model'], 'model');
    $this->load->helper($this->module['helper']);
    $this->load->library('upload');
    $this->data['module'] = $this->module;
  }

  public function index($tipe_po=null)
  {
    $this->authorized($this->module, 'index');
    if($tipe_po!=null){
      $_SESSION['ap']['tipe_po'] = $tipe_po;
    }

    $this->data['page']['title']            = $this->module['label'];
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] . '/index_data_source');
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array(6);

    $this->data['grid']['order_columns']    = array(
      0   => array( 0 => 1,  1 => 'desc' ),
      1   => array( 0 => 2,  1 => 'desc' ),
      2   => array( 0 => 3,  1 => 'desc' ),
      3   => array( 0 => 4,  1 => 'asc' ),
      4   => array( 0 => 5,  1 => 'asc' ),
      5   => array( 0 => 6,  1 => 'asc' ),
      6   => array( 0 => 7,  1 => 'asc' ),
    );
    $this->render_view($this->module['view'] . '/index');
  }

  public function index_data_source()
  {
    if (is_granted($this->module, 'index') === FALSE) {
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {
      $entities = $this->model->getIndex();
      $data     = array();
      $no       = $_POST['start'];
      $total_sisa     = array();
      $total_bayar   = array();
      $total_value  = array();

      foreach ($entities as $row) {
        $no++;
        $col = array();
        $akun_cash = getAccountByCode($row['cash_account_code']);
        $akun_bank = getAccountByCode($row['coa_kredit']);
        if (is_granted($this->module, 'approval') === TRUE) {
          if ($row['status'] == 'WAITING REVIEW BY FIN MNG' && (config_item('auth_role')=='SUPER ADMIN' || config_item('auth_role')=='FINANCE MANAGER')) {
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
        $col[]  = '<a data-id="openPo" href="javascript:;" data-item-row="' . $row['id'] . '" data-href="'.site_url($this->module['route'] .'/print_pdf/'. $row['id']).'" target="_blank" >'.print_string($row['document_number']).'</a>';
        // $col[]  = print_string($row['document_number']);
        $col[]  = print_date($row['tanggal']);
        $col[]  = print_string($row['status']);
        $col[]  = print_string($row['request_by']);
        $col[]  = print_string($row['cash_account_code']).' '.print_string($akun_cash->group);
        $col[]  = print_number($row['request_amount'], 2);
        if($row['coa_kredit']==NULL){
          $col[] = '<a href="javascript:;" data-id="item" data-item-row="' . $row['id'] . '" data-href="' . site_url($this->module['route'] . '/change_account/' . $row['id']) . '">--select account--</a>'.'<input type="hidden" id="coa_kredit_' . $row['id'] . '" autocomplete="off" value="' . $row['coa_kredit'] . '"/>';
        }else{
          $col[] = '<a href="javascript:;" data-id="item" data-item-row="' . $row['id'] . '" data-href="' . site_url($this->module['route'] . '/change_account/' . $row['id']) . '">' . print_string($row['coa_kredit']).' '.print_string($akun_bank->group) . '</a>'.'<input type="hidden" id="coa_kredit_' . $row['id'] . '" autocomplete="off" value="' . $row['coa_kredit'] . '"/>';
        }
        // $col[]  = print_string($row['coa_kredit']).' '.print_string($akun_bank->group);
        $col[]  = print_string($row['notes']);
        
        $total_value[] = $row['request_amount'];

        $col['DT_RowId'] = 'row_' . $row['id'];
        $col['DT_RowData']['pkey']  = $row['id'];

        if ($this->has_role($this->module, 'info')) {
          // $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] . '/info/' . $row['id']);
          $col['DT_RowAttr']['data-id'] = $row['id'];
          $col['DT_RowAttr']['onClick']     = '';
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
          6 => print_number(array_sum($total_value), 2),
        )
      );
    }

    echo json_encode($result);
  }

  public function set_date()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['cash_request']['date'] = $_GET['data'];
  }

  public function set_request_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['cash_request']['request_by'] = $_GET['data'];
  }

  public function set_account()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['cash_request']['cash_account'] = $_GET['data'];
  }

  public function set_amount()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['cash_request']['total_amount'] = $_GET['data'];
  }

  public function set_notes()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['cash_request']['notes'] = $_GET['data'];
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

      $this->data['entity'] = $entity;
      $this->data['id']     = $id;

      $return['type'] = 'success';
      $return['info'] = $this->load->view($this->module['view'] . '/info', $this->data, TRUE);
    }

    echo json_encode($return);
  }

  public function create($category = NULL)
  {
    $this->authorized($this->module, 'document');

    if ($category !== NULL) {
      $category = urldecode($category);

      $_SESSION['cash_request']['items']                  = array();
      $_SESSION['cash_request']['category']            = $category;
      $_SESSION['cash_request']['document_number']     = payment_request_last_number('BANK');
      $_SESSION['cash_request']['date']                = date('Y-m-d');
      $_SESSION['cash_request']['request_by']          = config_item('auth_person_name');
      $_SESSION['cash_request']['notes']               = NULL;
      $_SESSION['cash_request']['total_amount']        = 0;
      $_SESSION['cash_request']['cash_account']        = NULL;
      $_SESSION['cash_request']['source']              = 'mrp';

      redirect($this->module['route'] . '/create');
    }

    if (!isset($_SESSION['cash_request']))
      redirect($this->module['route']);

    $this->data['page']['content']    = $this->module['view'] . '/create';
    $this->data['page']['title']      = 'create cash request';

    $this->render_view($this->module['view'] . '/create');
  }

  public function save()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') === FALSE) {
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {
      if (!isset($_SESSION['cash_request']['items']) || empty($_SESSION['cash_request']['items'])) {
        $data['success'] = FALSE;
        $data['message'] = 'Please add at least 1 item!';
      } else {
        $_SESSION['cash_request']['document_number'] = payment_request_last_number('BANK').payment_request_format_number('BANK');
        $document_number = $_SESSION['cash_request']['document_number'];
        $errors = array();

        if (isset($_SESSION['cash_request']['edit'])) {
          $document_number = $_SESSION['cash_request']['edit'].'-R';
          $_SESSION['cash_request']['document_number'] = $document_number;
          if ($_SESSION['cash_request']['edit'] != $document_number && $this->model->isDocumentNumberExists($document_number)) {
            $errors[] = 'Duplicate Document Number: ' . $document_number . ' !';
          }
        } else {
          if ($this->model->isDocumentNumberExists($document_number)) {
            $errors[] = 'Duplicate Document Number: ' . $document_number . ' !';
          }
        }

        if (!empty($errors)) {
          $data['success'] = FALSE;
          $data['message'] = implode('<br />', $errors);
        } else {
          if ($this->model->save()) {
            unset($_SESSION['cash_request']);

            $data['success'] = TRUE;
            $data['message'] = 'Document ' . $document_number . ' has been saved. You will redirected now.';
          } else {
            $data['success'] = FALSE;
            $data['message'] = 'Error while saving this document. Please ask Technical Support.';
          }
        }
      }
    }

    echo json_encode($data);
  }

  public function bayar($id)
  {
    $this->authorized($this->module, 'payment');

    $entity = $this->model->findById($id);

    $this->data['entity'] = $entity;
    $this->data['id']     = $id;    
    $_SESSION['payment']['attachment']            = array();

    $this->render_view($this->module['view'] . '/bayar');
  }

  public function save_payment($id)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'payment') === FALSE) {
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {
      $insert_payment = $this->model->insert_payment($id);
      if ($insert_payment['type']) {
        $return['type'] = 'success';
        $return['info'] = 'Cash Request number ' . $insert_payment['document_number'] . ' has been paid.';
      } else {
        $return['type'] = 'danger';
        $return['info'] = 'There are error while updating data. Please try again later.';
      }
    }

    echo json_encode($return);
  }

  public function multi_approve()
  {
    $id_purchase_order = $this->input->post('id_purchase_order');
    $id_purchase_order = str_replace("|", "", $id_purchase_order);
    $id_purchase_order = substr($id_purchase_order, 0, -1);
    $id_purchase_order = explode(",", $id_purchase_order);

    $total = 0;
    $success = 0;
    $failed = sizeof($id_purchase_order);
    $x = 0;
    $level = 13;
    
    foreach ($id_purchase_order as $key) {
      if ($this->model->approve($key)) {
        $total++;
        $success++;
        $failed--;
      }
      $x++;
    }
    if ($success > 0) {
      $this->session->set_flashdata('alert', array(
        'type' => 'success',
        'info' => " data has been approved!"
      ));
    }
    if ($failed > 0) {
      $this->session->set_flashdata('alert', array(
        'type' => 'danger',
        'info' => "There are " . $failed . " errors"
      ));
    }
    if ($total == 0) {
      $result['status'] = 'failed';
    } else {
      //$this->sendEmailHOS();
      $result['status'] = 'success';
    }
    echo json_encode($result);
  }

  public function multi_reject()
  {
    $id_purchase_order = $this->input->post('id_purchase_order');
    $id_purchase_order = str_replace("|", "", $id_purchase_order);
    $id_purchase_order = substr($id_purchase_order, 0, -1);
    $id_purchase_order = explode(",", $id_purchase_order);

    // $str_price = $this->input->post('price');
    // $price = str_replace("|", "", $str_price);
    // $price = substr($price, 0, -3);
    // $price = explode("##,", $price);

    $total = 0;
    $success = 0;
    $failed = sizeof($id_purchase_order);
    $x = 0;
    foreach ($id_purchase_order as $key) {
      if ($this->model->rejected($key)) {
        $total++;
        $success++;
        $failed--;
        // $this->model->send_mail_approved($key,'approved');
      }
      $x++;
    }
    if ($success > 0) {
      // $id_role = 13;
      $this->session->set_flashdata('alert', array(
        'type' => 'success',
        'info' => $success . " data has been rejected!"
      ));
    }
    if ($failed > 0) {
      $this->session->set_flashdata('alert', array(
        'type' => 'danger',
        'info' => "There are " . $failed . " errors"
      ));
    }
    if ($total == 0) {
      $result['status'] = 'failed';
    } else {
      //$this->sendEmailHOS();
      $result['status'] = 'success';
    }
    echo json_encode($result);
  }

  public function attachment()
  {
    // $this->authorized($this->module, 'manage_attachment');
    $this->data['page']['title']    = "Attachment Payment";
    $this->render_view($this->module['view'] . '/attachment');
  }

  public function add_attachment()
  {
    $result["status"] = 0;
    $date = new DateTime();
    // $config['file_name'] = $date->getTimestamp().random_string('alnum', 5);
    $config['upload_path'] = 'attachment/cash_request_payment/';
    $config['allowed_types'] = 'jpg|png|jpeg|doc|docx|xls|xlsx|pdf';
    $config['max_size']  = 2000;

    $this->upload->initialize($config);

    if (!$this->upload->do_upload('attachment')) {
      $error = array('error' => $this->upload->display_errors());
    } else {

      $data = array('upload_data' => $this->upload->data());
      $url = $config['upload_path'] . $data['upload_data']['file_name'];
      array_push($_SESSION["payment"]["attachment"], $url);
      $result["status"] = 1;
    }
    echo json_encode($result);
  }

  public function discard()
  {
    // $this->authorized($this->module, 'document');

    unset($_SESSION['cash_request']);

    redirect($this->module['route']);
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

    $pdf = $this->m_pdf->load(null, 'A4-L');
    $pdf->WriteHTML($html);
    $pdf->Output($pdfFilePath, "I");
  }

  public function edit($id)
  {
    $this->authorized($this->module, 'document');

    $entity = $this->model->findById($id);

    // $this->data['entity'] = $entity;
    // $this->data['id']     = $id;    
    // $_SESSION['payment']['attachment']            = array();

    // $this->render_view($this->module['view'] . '/edit');

    $document_number  = sprintf('%06s', substr($entity['document_number'], 0, 6));

    if (isset($_SESSION['cash_request']) === FALSE){
      $_SESSION['cash_request']                     = $entity;
      $_SESSION['cash_request']['id']               = $id;
      $_SESSION['cash_request']['date']             = $entity['tanggal'];
      $_SESSION['cash_request']['total_amount']     = $entity['request_amount'];
      $_SESSION['cash_request']['edit']             = $entity['document_number'];
      $_SESSION['cash_request']['document_number']  = $document_number;
      $_SESSION['cash_request']['cash_account']        = $entity['cash_account_code'];;
    }

    redirect($this->module['route'] .'/create');
  }

  public function add_item()
  {
    $this->authorized($this->module, 'document');


    $this->data['entities_po'] = $this->model->listCashPaymentsPo();
    $this->data['entities_non_po'] = $this->model->listCashPaymentsNonPo();
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
      if (isset($_POST['payment_id']) && !empty($_POST['payment_id'])) {
        $_SESSION['cash_request']['items'] = array();
        $total_amount = array();

        foreach ($_POST['payment_id'] as $key => $payment_id) {
          $payment_id_explode  = explode('-', $payment_id);
          $id = $payment_id_explode[0];
          $source = $payment_id_explode[1];
          $payment = $this->model->infopayment($id,$source);

          $_SESSION['cash_request']['items'][$payment_id] = array(
            'payment_id'                      => $payment['id'],
            'source'                          => $source,
            'no_transaksi'                    => $payment['document_number'],
            'date'                            => $payment['tanggal'],
            'vendor'                          => $payment['vendor'],
            'amount'                          => $payment['amount_paid'],
          );
          $total_amount[] = $payment['amount_paid'];
        }

        $_SESSION['cash_request']['total_amount'] = array_sum($total_amount);

        $data['success'] = TRUE;
      } else {
        $data['success'] = FALSE;
        $data['message'] = 'Please select any request!';
      }
    }

    echo json_encode($data);
  }

  public function del_item($key)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (isset($_SESSION['cash_request']['items']))
      unset($_SESSION['cash_request']['items'][$key]);
  }

  public function change_account($id)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'approval') === FALSE) {
      $return['type'] = 'denied';
      $return['info'] = "You don't have permission to access this data. You may need to login again.";
    } else {
      $entity = $this->model->findById($id);
      if($entity['type']=='BANK'){
        if($entity['status']=='PAID'){
          $return['type'] = 'denied';
          $return['info'] = "This Transaction already paid. You cant change account. Please contack the technician.";
        }else{
          $this->data['entity'] = $entity;
          $return['type'] = 'success';
          $return['info'] = $this->load->view($this->module['view'] . '/change_account', $this->data, TRUE);
        }
        
      }else{
        $return['type'] = 'denied';
        $return['info'] = "This Transaction type is CASH. You cant change account. You have to edit this Transaction.";
      }      
    }

    echo json_encode($return);
  }

  public function save_change_account2()
  {
    // if ($this->input->is_ajax_request() == FALSE)
    //   redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'approval') == FALSE) {
      $data['type'] = FALSE;
      $data['info'] = 'You are not allowed to save this Document!';
    } else {
      if ($this->model->save_change_account()) {
        $data['type'] = 'success';
        $data['info'] = 'Update Success';
      } else {
        $data['type'] = 'danger';
        $data['info'] = 'Error while saving this document. Please ask Technical Support.';
      }
    }

    // echo json_encode($data);
    redirect($this->module['route']);
  }

  public function save_change_account()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'approval') === FALSE){
      $alert['type']  = 'danger';
      $alert['info']  = 'You are not allowed to change this data!';
    } else {
      if ($this->model->save_change_account()){
        $alert['type'] = 'success';
        $alert['info'] = 'Account changed.';
        $alert['link'] = site_url($this->module['route']);
      } else {
        $alert['type'] = 'danger';
        $alert['info'] = 'There are error while change data. Please try again later.';
      }
    }

    echo json_encode($alert);
  }

  public function view_manage_attachment_payment($payment_id,$source)
  {
    if($source=='mrp'){
      redirect('payment/manage_attachment/'.$payment_id);
    }else{
      redirect('expense_closing_payment/manage_attachment/'.$payment_id);
    }
    
  }

}
