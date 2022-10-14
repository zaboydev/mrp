<?php defined('BASEPATH') or exit('No direct script access allowed');

class Payment_Report extends MY_Controller
{
  protected $module;
  protected $id_item = 0;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['payment_report'];
    $this->load->helper($this->module['helper']);
    $this->load->model($this->module['model'], 'model');
    $this->load->library('upload');
    $this->load->helper('string');
    $this->data['module'] = $this->module;
  }

  public function index_data_source()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'index') === FALSE) {
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {
      $entities = $this->model->getIndexReport();
      $data     = array();
      $no       = $_POST['start'];
      $total_idr_unpaid         = array();
      $total_usd_unpaid         = array();
      $total_idr_paid           = array();
      $total_usd_paid           = array();
      $total_idr_purposed       = array();
      $total_usd_purposed       = array();
      $total_idr_balance        = array();
      $total_usd_balance        = array();

      foreach ($entities as $row) {
        $attachment = $this->model->checkAttachment($row['id']);
        $no++;
        $col = array();
        $col[] = print_number($no);      
        $col[]  = '<a class="link" data-id="openPo" href="javascript:;" data-item-row="' . $row['id'] . '" data-href="'.site_url($this->module['route'] .'/print_pdf/'. $row['id']).'" target="_blank" >'.print_string($row['no_transaksi']).'</a>';;      
        $col[]  = print_string($row['po_number']);     
        $col[]  = print_string($row['cash_credit']);
        $col[]  = print_string($row['vendor']);
        $col[]  = print_string($row['description']);
        $col[]  = print_string($row['status']);
        $col[]  = print_string($row['currency']);
        $col[]  = ($row['currency']=='IDR' && $row['status']!='PAID'&& $row['status']!='APPROVED')?print_number($row['amount_paid'], 2):print_number(0, 2);
        $col[]  = ($row['currency']!='IDR' && $row['status']!='PAID'&& $row['status']!='APPROVED')?print_number($row['amount_paid'], 2):print_number(0, 2);
        $col[]  = ($row['currency']=='IDR' && $row['status']=='APPROVED')?print_number($row['amount_paid'], 2):print_number(0, 2);
        $col[]  = ($row['currency']!='IDR' && $row['status']=='APPROVED')?print_number($row['amount_paid'], 2):print_number(0, 2);
        $col[]  = ($row['currency']=='IDR' && $row['status']=='PAID')?print_number($row['amount_paid'], 2):print_number(0, 2);
        $col[]  = ($row['currency']!='IDR' && $row['status']=='PAID')?print_number($row['amount_paid'], 2):print_number(0, 2);
        $col[]  = ($row['currency']=='IDR' && $row['status']!='PAID')?print_number($row['amount_paid'], 2):print_number(0, 2);
        $col[]  = ($row['currency']!='IDR' && $row['status']!='PAID')?print_number($row['amount_paid'], 2):print_number(0, 2);
        

        if($row['currency']=='IDR' && $row['status']=='APPROVED'){
          $total_idr_unpaid[] = $row['amount_paid'];
        }
        if($row['currency']!='IDR' && $row['status']=='APPROVED'){
          $total_usd_unpaid[] = $row['amount_paid'];
        }
        if($row['currency']=='IDR' && $row['status']=='PAID'){
          $total_idr_paid[] = $row['amount_paid'];
        }
        if($row['currency']!='IDR' && $row['status']=='PAID'){
          $total_usd_paid[] = $row['amount_paid'];
        }
        if($row['currency']=='IDR' && $row['status']!='PAID' && $row['status']!='APPROVED'){
          $total_idr_purposed[] = $row['amount_paid'];
        }
        if($row['currency']!='IDR' && $row['status']!='PAID' && $row['status']!='APPROVED'){
          $total_usd_purposed[] = $row['amount_paid'];
        }
        if($row['currency']=='IDR' && $row['status']!='PAID'){
          $total_idr_balance[] = $row['amount_paid'];
        }
        if($row['currency']!='IDR' && $row['status']!='PAID'){
          $total_usd_balance[] = $row['amount_paid'];
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
        "recordsTotal"    => $this->model->countIndexReport(),
        "recordsFiltered" => $this->model->countIndexFilteredReport(),
        "data"            => $data,
        "total"           => array(
          8 => print_number(array_sum($total_idr_purposed), 2),
          9 => print_number(array_sum($total_usd_purposed), 2),
          10 => print_number(array_sum($total_idr_unpaid), 2),
          11 => print_number(array_sum($total_usd_unpaid), 2),
          12 => print_number(array_sum($total_idr_paid), 2),
          13 => print_number(array_sum($total_usd_paid), 2),
          14 => print_number(array_sum($total_idr_balance), 2),
          15 => print_number(array_sum($total_usd_balance), 2),
        )
      );
    }

    echo json_encode($result);
  }

  public function index()
  {
    $this->authorized($this->module, 'index');
    unset($_SESSION['receipt']['id']);

    $this->data['page']['title']            = $this->module['label'];
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumnsReport());
    $this->data['grid']['data_source']      = site_url($this->module['route'] . '/index_data_source');
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array(8,9,10,11,12,13,14,15);

    // $this->data['grid']['order_columns']    = array();
    $this->data['grid']['order_columns']    = array(
      0   => array( 0 => 1,  1 => 'desc' ),
      1   => array( 0 => 2,  1 => 'desc' ),
      2   => array( 0 => 3,  1 => 'asc' ),
      3   => array( 0 => 4,  1 => 'asc' ),
      4   => array( 0 => 5,  1 => 'asc' ),
      5   => array( 0 => 6,  1 => 'asc' ),
    );

    $this->render_view($this->module['view'] . '/index-report');
  }

  public function get_accounts()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');
    // $vendor = $this->input->post('vendor');

    $type = $this->input->post('type');
    $accounts = getAccount($type);
    $option = '<option value="all">--All Account--</option>';
    foreach ($accounts as $key => $account) {
      $option .= '<option value="' . $account['coa'] . '">' . $account['coa'] . ' - ' . $account['group'] . '</option>';
    }

    $return = [
      'account' => $option
    ];
    echo json_encode($return);
  }

  public function create_2($category = NULL)
  {
    $this->data['currency']                 = 'IDR';
    $this->data['page']['title']            = $this->module['label'];
    $this->data['account']                  = $this->model->getAccount($this->data['currency']);
    $this->data['suplier']                  = $this->model->getSuplier($this->data['currency']);
    $this->data['no_transaksi']                  = $this->model->jrl_last_number();
    $this->render_view($this->module['view'] . '/create-2');
  }

  public function create($category = NULL)
  {
    $this->authorized($this->module, 'document');

    if ($category !== NULL) {
      $category = urldecode($category);

      $_SESSION['payment_request']['items']               = array();
      $_SESSION['payment_request']['category']            = $category;
      $_SESSION['payment_request']['document_number']     = payment_request_last_number();
      $_SESSION['payment_request']['date']                = date('Y-m-d');
      $_SESSION['payment_request']['purposed_date']       = date('Y-m-d');
      $_SESSION['payment_request']['created_by']          = config_item('auth_person_name');
      $_SESSION['payment_request']['currency']            = "IDR";
      $_SESSION['payment_request']['vendor']              = NULL;
      $_SESSION['payment_request']['notes']               = NULL;
      $_SESSION['payment_request']['total_amount']        = 0;

      redirect($this->module['route'] . '/create');
    }

    if (!isset($_SESSION['payment_request']))
      redirect($this->module['route']);

    $this->data['page']['content']    = $this->module['view'] . '/create';
    $this->data['page']['title']      = 'create payment request';

    $this->render_view($this->module['view'] . '/create');
  }
  
  public function getPo()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');
    $vendor = $this->input->post('vendor');
    $currency = $this->input->post('currency');
    $tipe = $this->input->post('tipe');
    $po = $this->model->getPoByVendor($vendor, $currency,$tipe);
    $this->data['po'] = $po;
    $return['info'] = $this->load->view($this->module['view'] . '/list_po', $this->data, TRUE);
    $return['count_detail'] = $this->model->countdetailPoByVendor($vendor, $currency, $tipe);
    $return['count_po'] = $this->model->countPoByVendor($vendor, $currency, $tipe);
    $return['count_po_additional'] = $this->model->countPoAdditionalByVendor($vendor, $currency, $tipe);
    echo json_encode($return);
  }

  public function save_2()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');
    $save = $this->model->save_2();
    if ($save) {
      $result["status"] = "success";
    } else {
      $result["status"] = "failed";
    }
    echo json_encode($result);
  }

  public function save()
  {
    if (is_granted($this->module, 'document') == FALSE) {
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      if (!isset($_SESSION['payment_request']['items']) || empty($_SESSION['payment_request']['items'])) {
        $data['success'] = FALSE;
        $data['message'] = 'Please add at least 1 request or vendor!';
      } else {
        $errors = array();

        $document_number = $_SESSION['payment_request']['document_number'] . payment_request_format_number();

        if (isset($_SESSION['payment_request']['edit'])) {
          if ($_SESSION['payment_request']['edit'] != $document_number && $this->model->isDocumentNumberExists($document_number)) {
            $errors[] = 'Duplicate Document Number: ' . $document_number. ' !';
          }
        } else {
          if ($this->model->isDocumentNumberExists($document_number)) {
            $_SESSION['payment_request']['document_number']     = payment_request_last_number();
            $document_number = $_SESSION['payment_request']['document_number'] . payment_request_format_number();
            // $errors[] = 'Duplicate Document Number: ' . $_SESSION['poe']['document_number'] . ' !';
          }
        }

        if (!empty($errors)) {
          $data['success'] = FALSE;
          $data['message'] = implode('<br />', $errors);
        } else {
          if ($this->model->save()) {
            unset($_SESSION['payment_request']);
            // $this->sendEmail();
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

  public function get_akun()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');
    // $vendor = $this->input->post('vendor');
    $currency = $this->input->post('currency');
    $akun = $this->model->getAccount($currency);
    $option = '<option>No Account</option>';
    foreach ($akun as $key) {
      $option .= '<option value="' . $key->coa . '">' . $key->coa . ' - ' . $key->group . '</option>';
    }
    echo json_encode($option);
  }

  public function get_supplier()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');
    // $vendor = $this->input->post('vendor');
    $currency = $this->input->post('currency');
    $supplier = $this->model->getSuplier($currency);
    $option = '<option>No Supplier</option>';
    foreach ($supplier as $key) {
      $option .= '<option value="' . $key->vendor . '">' . $key->vendor . ' - ' . $key->code . '</option>';
    }
    echo json_encode($option);
  }

  public function getPoDetail()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');
    // $vendor = $this->input->post('vendor');
    $id_po = $this->input->post('id_po');
    $po = $this->model->getPoDetail($id_po);
    $this->data['po'] = $po;
    $return['info'] = $this->load->view($this->module['view'] . '/list_detail_po', $this->data, TRUE);
    $return['count'] = $this->model->countPoDetail($id_po)+1;
    echo json_encode($return);
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

  public function multi_approve()
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

  public function bayar($id)
  {
    $this->authorized($this->module, 'payment');

    // if ($category !== NULL){
    $item       = $this->model->findById($id);

    $_SESSION['payment']                          = $item;
    $_SESSION['payment']['no_transaksi']          = $item['no_transaksi'];
    $_SESSION['payment']['vendor']                = $item['vendor'];
    $_SESSION['payment']['currency']              = $item['currency'];
    $_SESSION['payment']['po_payment_id']              = $item['id'];
    $_SESSION['payment']['total_amount']          = 0;
    foreach ($_SESSION['payment']['items'] as $i => $item){
      $_SESSION['payment']['total_amount']          = $_SESSION['payment']['total_amount']+$item['amount_paid'];
    }
    // $_SESSION['payment']['total_amount']          = $item['items']->sum('amount_paid');

    $this->data['account']                  = $this->model->getAccount($item['default_currency']);
    $this->data['suplier']                  = $this->model->getSuplier($item['default_currency']);
    

    $this->render_view($this->module['view'] . '/bayar');
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

  public function discard()
  {
    $this->authorized($this->module['permission']['document']);

    unset($_SESSION['payment']);

    redirect($this->module['route']);
  }

  public function listAttachment($id)
  {
    $data = $this->model->listAttachment($id);
    echo json_encode($data);
  }

  public function manage_attachment($id)
  {
    $this->authorized($this->module, 'document');

    $this->data['manage_attachment'] = $this->model->listAttachment_2($id);
    $this->data['id'] = $id;
    $this->render_view($this->module['view'] . '/manage_attachment');
  }

  public function add_attachment_to_db($id)
  {
    $result["status"] = 0;
    $date = new DateTime();
    // $config['file_name'] = $date->getTimestamp().random_string('alnum', 5);
    $config['upload_path'] = 'attachment/attachment_payment/';
    $config['allowed_types'] = 'jpg|png|jpeg|doc|docx|xls|xlsx|pdf';
    $config['max_size']  = 2000;

    $this->upload->initialize($config);

    if (!$this->upload->do_upload('attachment')) {
      $error = array('error' => $this->upload->display_errors());
    } else {
      $data = array('upload_data' => $this->upload->data());
      $url = $config['upload_path'] . $data['upload_data']['file_name'];
      // array_push($_SESSION["poe"]["attachment"], $url);
      $this->model->add_attachment_to_db($id, $url);
      $result["status"] = 1;
    }
    echo json_encode($result);
  }

  public function print_prl($request_id,$tipe)
  {
    if($tipe=='EXPENSE'){
      redirect('expense_request/print_pdf/'.$request_id);
    }elseif($tipe=='CAPEX'){
      redirect('capex_request/print_pdf/'.$request_id);
    }elseif($tipe=='INVENTORY'){
      redirect('inventory_request/print_pdf/'.$request_id);
    }else{
      redirect('purchase_request/print_pdf/'.$request_id);
    }
    
  }

  public function print_poe($poe_id,$tipe)
  {
    if($tipe=='EXPENSE'){
      redirect('expense_order_evaluation/print_pdf/'.$poe_id);
    }elseif($tipe=='CAPEX'){
      redirect('capex_order_evaluation/print_pdf/'.$poe_id);
    }elseif($tipe=='INVENTORY'){
      redirect('inventory_order_evaluation/print_pdf/'.$poe_id);
    }else{
      redirect('purchase_order_evaluation/print_pdf/'.$poe_id);
    }
    
  }

  public function print_po($id_po,$tipe)
  {
    if($tipe=='EXPENSE'){
      redirect('expense_purchase_order/print_pdf/'.$id_po);
    }elseif($tipe=='CAPEX'){
      redirect('capex_purchase_order/print_pdf/'.$id_po);
    }elseif($tipe=='INVENTORY'){
      redirect('inventory_purchase_order/print_pdf/'.$id_po);
    }else{
      redirect('purchase_order/print_pdf/'.$id_po);
    }
    
  }

  public function add_item()
  {
    $this->authorized($this->module, 'document');

    $vendor           = $_SESSION['payment_request']['vendor'];
    $default_currency = $_SESSION['payment_request']['currency'];

    $this->data['entities'] = $this->model->listItems($vendor,$default_currency);
    $this->data['page']['title']            = 'Add Items';

    $this->render_view($this->module['view'] . '/add_item');
  }

  public function set_date()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['payment_request']['date'] = $_GET['data'];
  }

  public function set_purposed_date()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['payment_request']['purposed_date'] = $_GET['data'];
  }

  public function set_notes()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['payment_request']['notes'] = $_GET['data'];
  }

  public function set_default_currency($currency)
  {
    $this->authorized($this->module, 'document');

    $currency = urldecode($currency);

    $_SESSION['payment_request']['currency']  = $currency;
    $_SESSION['payment_request']['items']   = array();

    redirect($this->module['route'] . '/create');
  }

  public function set_vendor($vendor)
  {
    $this->authorized($this->module, 'document');

    $vendor = urldecode($vendor);

    $_SESSION['payment_request']['vendor']  = $vendor;
    $_SESSION['payment_request']['items']   = array();

    redirect($this->module['route'] . '/create');
  }

  public function add_selected_item()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') == FALSE) {
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      if (isset($_POST['item_id']) && !empty($_POST['item_id'])) {
        $_SESSION['payment_request']['items'] = array();

        foreach ($_POST['item_id'] as $key => $item_id) {
          $item_id_explode  = explode('-', $item_id);
          $po_id = $item_id_explode[0];
          $po_item_id = $item_id_explode[1];
          $request = $this->model->infoItem($po_id,$po_item_id);

          if($po_item_id!=0){
            $_SESSION['payment_request']['items'][$item_id] = array(
              'po_number'               => $request['document_number'],
              'deskripsi'               => $request['part_number'].' | '.$request['description'],
              'quantity_received'       => floatval($request['quantity_received']),
              'amount_received'         => floatval($request['quantity_received'])*(floatval($request['unit_price'])+floatval($request['core_charge'])),
              'total_amount'            => floatval($request['total_amount']),
              'left_paid_request'       => floatval($request['left_paid_request']),
              'status'                  => $request['status'],
              'due_date'                => $request['due_date'],
              'amount_paid'             => floatval(0),
              'adj_value'               => floatval(0),
            );

            $_SESSION['payment_request']['items'][$item_id]['purchase_order_item_id'] = $po_item_id;
          }else{
            $_SESSION['payment_request']['items'][$item_id] = array(
              'po_number'               => $request['document_number'],
              'deskripsi'               => 'Additional Price (PPN, DISC, SHIPPING COST)',
              'quantity_received'       => floatval(0),
              'amount_received'         => floatval(0),
              'total_amount'            => floatval($request['additional_price']),
              'left_paid_request'       => floatval($request['additional_price_remaining_request']),
              'status'                  => $request['status'],
              'due_date'                => $request['due_date'],
              'amount_paid'             => floatval(0),
              'adj_value'               => floatval(0),
            );
            $_SESSION['payment_request']['items'][$item_id]['purchase_order_item_id'] = $po_item_id;
          }
          
          $_SESSION['payment_request']['items'][$item_id]['id_po'] = $po_id;
        }

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
    $this->authorized($this->module, 'document');

    $this->render_view($this->module['view'] . '/edit_item_payment');
  }

  public function update_item()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') == FALSE) {
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      if (isset($_POST['item']) && !empty($_POST['item'])) {
        $total_amount = array();
        foreach ($_POST['item'] as $id => $request) {

          $_SESSION['payment_request']['items'][$id]['amount_paid']            = $request['amount_paid'];
          $_SESSION['payment_request']['items'][$id]['adj_value']              = $request['adj_value'];
          $total_amount[] = $request['amount_paid'];
          
        }
        $_SESSION['payment_request']['total_amount'] = array_sum($total_amount);

        $data['success'] = TRUE;
      } else {
        $data['success'] = FALSE;
        $data['message'] = 'No data to update!';
      }
    }

    echo json_encode($data);
  }

  public function print_pdf($id)
  {
    $this->authorized($this->module, 'print');

    $entity = $this->model->findById($id);

    $this->data['entity']           = $entity;
    $this->data['page']['title']    = ($entity->status=='PAID')? $entity->type.' PAYMENT VOUCHER':strtoupper('Purpose Payment Purchase');
    $this->data['page']['content']  = $this->module['view'] .'/print_pdf';

    $html = $this->load->view($this->pdf_theme, $this->data, true);

    $pdfFilePath = str_replace('/', '-', $entity['document_number']) .".pdf";

    $this->load->library('m_pdf');

    $pdf = $this->m_pdf->load(null, 'A4-L');
    $pdf->WriteHTML($html);
    $pdf->Output($pdfFilePath, "I");
  }

}
