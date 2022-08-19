<?php defined('BASEPATH') or exit('No direct script access allowed');

class Payment extends MY_Controller
{
  protected $module;
  protected $id_item = 0;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['payment'];
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
      $entities = $this->model->getIndex();
      $data     = array();
      $no       = $_POST['start'];
      $total_idr      = array();
      $total_usd      = array();

      foreach ($entities as $row) {
        $attachment = $this->model->checkAttachment($row['id']);
        $account = ($row['coa_kredit']!=NULL)?print_string($row['coa_kredit']).' '.print_string($row['akun_kredit']):'--select account--';
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
        $col[]  = '<a class="link" data-id="openPo" href="javascript:;" data-item-row="' . $row['id'] . '" data-href="'.site_url($this->module['route'] .'/print_pdf/'. $row['id']).'" target="_blank" >'.print_string($row['no_transaksi']).'</a>';
        $col[]  = print_date($row['tanggal']);
        $col[]  = print_string($row['no_cheque']);
        // $col[]  = print_string($row['document_number']);
        $col[]  = print_string($row['vendor']);
        // $col[]  = print_string($row['part_number']);
        // $col[]  = print_string($row['description']);
        $col[]  = print_string($row['currency']);
        // $col[]  = print_string($row['coa_kredit']).' '.print_string($row['akun_kredit']);
        $col[]  = '<a href="javascript:;" data-id="item" data-item-row="' . $row['id'] . '" data-href="' . site_url($this->module['route'] . '/change_account/' . $row['id']) . '">' . $account . '</a>'.'<input type="hidden" id="coa_kredit_' . $row['id'] . '" autocomplete="off" value="' . $row['coa_kredit'] . '"/>';
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
        $col[] = '<a class="link" data-id="openPo" href="javascript:;" data-item-row="' . $row['id'] . '" data-href="'.site_url($this->module['route'] .'/download_all/'. $row['id']).'" target="_blank">
                    <i class="fa fa-download"></i>
                  </a>';
        

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

  public function create_2_copy($category = NULL)
  {
    $this->data['currency']                 = 'IDR';
    $this->data['page']['title']            = $this->module['label'];
    $this->data['account']                  = $this->model->getAccount($this->data['currency']);
    $this->data['suplier']                  = $this->model->getSuplier($this->data['currency']);
    $this->data['no_transaksi']                  = $this->model->jrl_last_number();
    $this->render_view($this->module['view'] . '/create-2');
  }

  //ini yg dipake
  public function create_2($category = NULL)
  {
    $this->authorized($this->module, 'document');

    if ($category !== NULL) {
      $category = urldecode($category);

      $_SESSION['payment_request']['po']                  = array();
      $_SESSION['payment_request']['category']            = $category;
      $_SESSION['payment_request']['type']                = (config_item('auth_role')=='PIC STAFF')? 'CASH':'BANK';
      $_SESSION['payment_request']['document_number']     = payment_request_last_number($_SESSION['payment_request']['type']);
      $_SESSION['payment_request']['date']                = date('Y-m-d');
      $_SESSION['payment_request']['purposed_date']       = date('Y-m-d');
      $_SESSION['payment_request']['created_by']          = config_item('auth_person_name');
      $_SESSION['payment_request']['currency']            = "IDR";
      $_SESSION['payment_request']['vendor']              = NULL;
      $_SESSION['payment_request']['notes']               = NULL;
      $_SESSION['payment_request']['total_amount']        = 0;
      $_SESSION['payment_request']['coa_kredit']          = NULL;

      redirect($this->module['route'] . '/create_2');
    }

    if (!isset($_SESSION['payment_request']))
      redirect($this->module['route']);

    $this->data['page']['content']    = $this->module['view'] . '/create-2';
    $this->data['page']['title']      = 'create payment request';

    $this->render_view($this->module['view'] . '/create-2');
  }
  //ini yg dipake

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

  /**ini dipakai untuk ke halaman konfirmasi */
  public function save_2()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $errors = array();

    if ($this->input->post('amount')==0){
      $errors[] = 'Amount Purposed Tidak Boleh 0 !!';
    }

    if (!empty($errors)){
      $data['success'] = FALSE;
      $data['message'] = implode('<br />', $errors);
    } else {
      $po_items_id 			  = $this->input->post('po_item_id');
      $pos_id 				    = $this->input->post('po_id');
      $desc_items 			  = $this->input->post('desc');
      $value_items		 	  = $this->input->post('value');
      $adj_value_items	 	= $this->input->post('adj_value');
      $qty_paid	 			    = $this->input->post('qty_paid');
      $account_code		    = $this->input->post('account_code');
      $_SESSION['payment_request']['items'] = array();

      $amount_paid = array();

      foreach ($po_items_id as $key=>$po_item) {
        // if ($value_items[$key] != 0) {
        //   $_SESSION['payment_request']['items'][$key] = array(
        //     'po_items_id'     => $po_items_id[$key],
        //     'pos_id'          => $pos_id[$key],
        //     'desc_items'      => $desc_items[$key],
        //     'value_items'     => $value_items[$key],
        //     'adj_value_items' => $adj_value_items[$key],
        //     'qty_paid'        => $qty_paid[$key],
        //   );
        // }
        if ($value_items[$key] != 0){
                  
          if($po_item!=0){
            $request = $this->model->infoItemPo($pos_id[$key],$po_item);
            $_SESSION['payment_request']['items'][$key] = array(
              'po_number'               => $request['document_number'],
              'deskripsi'               => $request['part_number'].' | '.$request['description'],
              'quantity_received'       => floatval($request['quantity_received']),
              'amount_received'         => floatval($request['quantity_received'])*(floatval($request['unit_price'])+floatval($request['core_charge'])),
              'total_amount'            => floatval($request['total_amount']),
              'left_paid_request'       => floatval($request['left_paid_request']),
              'status'                  => $request['status'],
              'due_date'                => $request['due_date'],
              'amount_paid'             => floatval($value_items[$key]),
              'adj_value'               => floatval($adj_value_items[$key]),
              'qty_paid'                => $qty_paid[$key],
              'po_id'                   => $pos_id[$key],
              'account_code'            => $account_code[$key]
            );

            $_SESSION['payment_request']['items'][$key]['purchase_order_item_id'] = $po_item;
          }else{
            if($pos_id[$key]!=0){
              $request = $this->model->infoItemPo($pos_id[$key],$po_item);
              $_SESSION['payment_request']['items'][$key] = array(
                'po_number'               => $request['document_number'],
                'deskripsi'               => 'Additional Price (PPN, DISC, SHIPPING COST)',
                'quantity_received'       => floatval(0),
                'amount_received'         => floatval(0),
                'total_amount'            => floatval($request['additional_price']),
                'left_paid_request'       => floatval($request['additional_price_remaining_request']),
                'status'                  => $request['status'],
                'due_date'                => $request['due_date'],
                'amount_paid'             => floatval($value_items[$key]),
                'adj_value'               => floatval($adj_value_items[$key]),
                'qty_paid'                => $qty_paid[$key],
                'po_id'                   => $pos_id[$key],
                'account_code'            => $account_code[$key]
              );
              $_SESSION['payment_request']['items'][$key]['purchase_order_item_id'] = $po_item;
            }else{
              $_SESSION['payment_request']['items'][$key] = array(
                'po_number'               => '',
                'deskripsi'               => $desc_items[$key],
                'quantity_received'       => floatval(0),
                'amount_received'         => floatval(0),
                'total_amount'            => floatval($value_items[$key]),
                'left_paid_request'       => floatval($value_items[$key]),
                'status'                  => '',
                'due_date'                => null,
                'amount_paid'             => floatval($value_items[$key]),
                'adj_value'               => floatval($adj_value_items[$key]),
                'qty_paid'                => 0,
                'po_id'                   => $pos_id[$key],
                'account_code'            => $account_code[$key]
              );
              $_SESSION['payment_request']['items'][$key]['purchase_order_item_id'] = $po_item;
            }
            
          }
          $amount_paid[] = $value_items[$key];
        }
      }

      $_SESSION['payment_request']['total_amount'] = array_sum($amount_paid);
      // if ($this->model->save_2()) {
      //   unset($_SESSION['payment_request']);
      //   // $this->sendEmail();
      //   $data['success'] = TRUE;
      //   $data['message'] = 'Document has been saved. You will redirected now.';
      // } else {
      //   $data['success'] = FALSE;
      //   $data['message'] = 'Error while saving this document. Please ask Technical Support.';
      // }
      $data['success'] = TRUE;
      $data['message'] = 'Document has been saved. You will redirected now.';
    }    
    echo json_encode($data);
  }

  public function update()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $errors = array();

    if ($this->input->post('amount')==0){
      $errors[] = 'Amount Purposed Tidak Boleh 0 !!';
    }

    if (!empty($errors)){
      $data['success'] = FALSE;
      $data['message'] = implode('<br />', $errors);
    } else {
      if ($this->model->update()) {
        unset($_SESSION['payment_request']);
        // $this->sendEmail();
        $data['success'] = TRUE;
        $data['message'] = 'Document has been saved. You will redirected now.';
      } else {
        $data['success'] = FALSE;
        $data['message'] = 'Error while saving this document. Please ask Technical Support.';
      }
    }
    echo json_encode($data);
  }

  /**ini yang dipakai */
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

        // $_SESSION['payment_request']['document_number'] = payment_request_last_number();

        $document_number = $_SESSION['payment_request']['document_number'] . payment_request_format_number($_SESSION['payment_request']['type']);

        if (isset($_SESSION['payment_request']['edit'])) {
          if ($_SESSION['payment_request']['edit'] != $document_number && $this->model->isDocumentNumberExists($document_number)) {
            $errors[] = 'Duplicate Document Number: ' . $document_number. ' !';
          }
        } else {
          if ($this->model->isDocumentNumberExists($document_number)) {
            $_SESSION['payment_request']['document_number']     = payment_request_last_number();
            $document_number = $_SESSION['payment_request']['document_number'] . payment_request_format_number($_SESSION['payment_request']['type']);
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

  public function edit($id)
  {
    $this->authorized($this->module, 'document');

    $entity = $this->model->findById($id);

    $document_number  = sprintf('%06s', substr($entity['document_number'], 0, 6));

    if (isset($_SESSION['payment_request']) === FALSE){
      $_SESSION['payment_request']                     = $entity;
      $_SESSION['payment_request']['id']               = $id;
      $_SESSION['payment_request']['date']             = $entity['tanggal'];
      $_SESSION['payment_request']['edit']             = $entity['document_number'];
      $_SESSION['payment_request']['document_number']  = $document_number;
      $_SESSION['payment_request']['total_amount']     = $this->model->countTotalPayment($id);
    }

    // redirect($this->module['route'] .'/create');
    $this->render_view($this->module['view'] . '/edit');
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
    $_SESSION['payment']['base']                  = $item['base'];
    $_SESSION['payment']['po_payment_id']              = $item['id'];
    $_SESSION['payment']['total_amount']          = 0;
    $_SESSION['payment']['attachment']            = array();
    foreach ($_SESSION['payment']['po'] as $i => $item){
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
    // $this->authorized($this->module, 'document');

    unset($_SESSION['payment_request']);

    redirect($this->module['route']);
  }

  public function listAttachment($id)
  {
    $data = $this->model->listAttachment($id);
    echo json_encode($data);
  }

  public function manage_attachment($id)
  {
    // $this->authorized($this->module, 'manage_attachment');

    $this->data['manage_attachment'] = $this->model->listAttachment_2($id);
    $this->data['page']['title']    = "Manage Attachment Payment";
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
      $url = $config['upload_path'] . $data['upload_data']['orig_name'];
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

  public function set_doc_number()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['payment_request']['document_number'] = $_GET['data'];
  }

  public function set_type_transaction()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['payment_request']['type'] = $_GET['data'];
    $_SESSION['payment_request']['coa_kredit'] = null;
  }

  public function set_date()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['payment_request']['date'] = $_GET['data'];
  }

  public function set_account()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['payment_request']['coa_kredit'] = $_GET['data'];
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
    // if ($this->input->is_ajax_request() === FALSE)
    //   redirect($this->modules['secure']['route'] . '/denied');

    // $_SESSION['payment_request']['currency'] = $_GET['data'];
    // $_SESSION['payment_request']['po']   = array();

    $this->authorized($this->module, 'document');

    $currency = urldecode($currency);

    $_SESSION['payment_request']['vendor']              = NULL;
    $_SESSION['payment_request']['currency']            = $currency;
    $_SESSION['payment_request']['po']                  = array();
    $_SESSION['payment_request']['total_amount']        = 0;

    redirect($this->module['route'] . '/create_2');
  }

  public function set_vendor($vendor)
  {
    // if ($this->input->is_ajax_request() === FALSE)
    //   redirect($this->modules['secure']['route'] . '/denied');

    // $_SESSION['payment_request']['vendor'] = $_GET['data'];
    // $_SESSION['payment_request']['po']   = array();

    $this->authorized($this->module, 'document');

    $vendor = urldecode($vendor);

    $_SESSION['payment_request']['vendor']              = $vendor;
    $_SESSION['payment_request']['po']                  = array();
    $_SESSION['payment_request']['total_amount']        = 0;

    redirect($this->module['route'] . '/create_2');
  }

  public function add_selected_item()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') == FALSE) {
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      if (isset($_POST['po_id']) && !empty($_POST['po_id'])) {
        $_SESSION['payment_request']['po'] = array();
        $total_amount = array();
        foreach ($_POST['po_id'] as $key => $po_id) {
          // $item_id_explode  = explode('-', $item_id);
          // $po_id = $item_id_explode[0];
          // $po_item_id = $item_id_explode[1];
          $po = $this->model->infoPo($po_id);

          $_SESSION['payment_request']['po'][$po_id] = array(
            'po_id'                     => $po['id'],
            'document_number'           => $po['document_number'],
            'status'                    => $po['status'],
            'due_date'                  => $po['due_date'],
            'grand_total'               => $po['grand_total'],
            'payment'                   => $po['payment'],
            'remaining_payment_request' => $po['remaining_payment_request'],            
            'tipe_po'                   => $po['tipe_po']
          );
          $_SESSION['payment_request']['po'][$po_id]['items_po'] = array();

          $po_items = $this->model->infoItem($po_id);
          $i = 0;

          foreach ($po_items as $key => $value) {
            if($value['left_paid_request']>0){
              $_SESSION['payment_request']['po'][$po_id]['items_po'][$key] = array(
                'po_id'               => $value['purchase_order_id'],
                'po_item_id'          => $value['id'],
                'part_number'         => $value['part_number'],
                'description'         => $value['description'],
                'due_date'            => $po['due_date'],
                'quantity_received'   => $value['quantity_received'],
                'unit_price'          => $value['unit_price'],
                'core_charge'         => $value['core_charge'],
                'total_amount'        => $value['total_amount'],
                'left_paid_request'   => $value['left_paid_request'],
                'quantity'            => $value['quantity'],
                'quantity_paid'       => $value['quantity_paid'],
                'value'               => floatval(0),
                'adj_value'           => floatval(0),
                'qty_paid'            => floatval($value['quantity']-$value['quantity_paid'])
              );
              $i++;
              $total_amount[] = $value['left_paid_request'];
            }            
          }

          if($po['additional_price_remaining_request']!=0){
            $_SESSION['payment_request']['po'][$po_id]['items_po'][$i] = array(
              'po_id'               => $po['id'],
              'po_item_id'          => 0,
              'part_number'         => 'Additional Price',
              'description'         => 'Additional Price (PPN, Diskon, Shipping Cost)',
              'due_date'            => $po['due_date'],
              'quantity_received'   => floatval(1),
              'unit_price'          => floatval(0),
              'core_charge'         => floatval(0),
              'total_amount'        => floatval($po['additional_price']),
              'left_paid_request'   => floatval($po['additional_price_remaining_request']),
              'quantity'            => floatval(1),
              'quantity_paid'       => floatval(1),
              'value'               => floatval(0),
              'adj_value'           => floatval(0),
              'qty_paid'            => floatval(1)
            );
            $total_amount[] = $po['additional_price_remaining_request'];
          }
        }

        $_SESSION['payment_request']['total_amount'] = array_sum($total_amount);

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
    $this->data['page']['title']    = ($entity->status=='PAID')? $entity->type.' PAYMENT VOUCHER':strtoupper($this->module['label']);
    $this->data['page']['content']  = $this->module['view'] .'/print_pdf';

    $html = $this->load->view($this->pdf_theme, $this->data, true);

    $pdfFilePath = str_replace('/', '-', $entity['document_number']) .".pdf";

    $this->load->library('m_pdf');

    $pdf = $this->m_pdf->load(null, 'A4-L');
    $pdf->WriteHTML($html);
    $pdf->Output($pdfFilePath, "I");
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
    $config['upload_path'] = 'attachment/payment/';
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

  public function delete_attachment_in_db($id_att, $id_poe)
  {
    $this->model->delete_attachment_in_db($id_att);

    redirect($this->module['route'] . "/manage_attachment/" . $id_poe, 'refresh');
    // echo json_encode($result);
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

  public function view_manage_attachment_po($po_id,$tipe)
  {
    if($tipe=='EXPENSE'){
      redirect('expense_purchase_order/manage_attachment/'.$po_id);
    }elseif($tipe=='CAPEX'){
      redirect('capex_purchase_order/manage_attachment/'.$po_id);
    }elseif($tipe=='INVENTORY'){
      redirect('inventory_purchase_order/manage_attachment/'.$po_id);
    }else{
      redirect('purchase_order/manage_attachment/'.$po_id);
    }
    
  }

  public function konfirmasi()
  {
    $this->authorized($this->module, 'document');

    // $this->data['entities'] = $this->model->listRequest($_SESSION['poe']['category']);
    $this->data['page']['title']            = 'Confirmation Request';

    $this->render_view($this->module['view'] . '/konfirmasi');
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

    if (is_granted($this->module, 'change_account') == FALSE) {
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

  public function search_vendor()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $entities = search_vendors_by_currency($_SESSION['payment_request']['currency']);

    foreach ($entities as $vendor){
      $arr_result[] = $vendor->vendor;
    }

    echo json_encode($arr_result);
  }

  public function find_by_id($id){
    $entity = $this->model->findById($id);
    echo json_encode($entity);
  }

  public function download_all($id)
  {
    //download bpv
    $entity = $this->model->findById($id);

    $this->data['entity']           = $entity;
    $this->data['page']['title']    = ($entity->status=='PAID')? $entity->type.' PAYMENT VOUCHER':strtoupper($this->module['label']);
    $this->data['page']['content']  = $this->module['view'] .'/print_pdf';

    $html = $this->load->view($this->pdf_theme, $this->data, true);

    $pdfFilePath = str_replace('/', '-', $entity['document_number']);
    $filename = $pdfFilePath.".pdf";

    if(cekDirektori("./download/".$pdfFilePath)){
      $this->load->library('m_pdf');

      $pdf = $this->m_pdf->load(null, 'A4-L');
      $pdf->WriteHTML($html);
      // $pdf->Output($pdfFilePath, "I");
      $pdf->Output("./download/".$pdfFilePath."/".$filename, "F");
    }

    //PO
    $path_po = array();
    $path_po[0]['path'] = $pdfFilePath."/".$filename;
    $path_po[0]['file_name'] = $filename;
    $n=1;

    $path_att = array();
    $n_att = 0;
    foreach($entity['attachment'] as $key => $attachment){
      $file  = explode('/', $attachment['file']);
      $path_att[$n_att]['path'] = $attachment['file'];
      $path_att[$n_att]['file_name'] = end($file);
      $path_att[$n_att]['tipe_att'] = 'payment';
      $n_att++;
    }

    foreach($entity['po'] as $key => $item){
      $purchase_order_id = $item['id_po'];
      if($purchase_order_id!=null){
        //purchase order
        if($item['tipe_po']=='INVENTORY MRP'){
          $modules_name = 'purchase_order';
          $entity_po  = $this->model->findPurchaseOrderById($purchase_order_id,$item['tipe_po']);
        }elseif ($item['tipe_po']=='EXPENSE') {
          $modules_name = 'expense_purchase_order';
          $entity_po  = $this->model->findPurchaseOrderById($purchase_order_id,$item['tipe_po']);
        }elseif ($item['tipe_po']=='CAPEX') {
          $modules_name = 'capex_purchase_order';
          $entity_po  = $this->model->findPurchaseOrderById($purchase_order_id,$item['tipe_po']);
        }elseif ($item['tipe_po']=='INVENTORY') {
          $modules_name = 'inventory_purchase_order';
          $entity_po  = $this->model->findPurchaseOrderById($purchase_order_id,$item['tipe_po']);
        }

        $this->data['entity']           = $entity_po;
        if (strpos($entity_po['document_number'], 'W') !== FALSE){
          $this->data['page']['title']    = 'WORK ORDER';
        }else{
          $this->data['page']['title']    = 'PURCHASE ORDER';
        }
        // $this->data['page']['content']  = $this->modules['expense_purchase_order']['view'] .'/print_pdf';

        $html = $this->load->view($this->modules[$modules_name]['view'] . '/pdf', $this->data, true);

        $filename_po = str_replace('/', '-', $entity_po['document_number']).".pdf";

        if(cekDirektori("./download/".$pdfFilePath)){
          $this->load->library('m_pdf');

          $pdf = $this->m_pdf->load(null, 'A4-L');
          $pdf->WriteHTML($html);
          // $pdf->Output($pdfFilePath, "I");
          $pdf->Output("./download/".$pdfFilePath."/".$filename_po, "F");
          $path_po[$n]['path'] = $pdfFilePath."/".$filename_po;
          $path_po[$n]['file_name'] = $filename_po;
          $n++;
        }
        foreach($entity_po['attachment'] as $key => $attachment){
          $file  = explode('/', $attachment['file']);
          $path_att[$n_att]['path'] = $attachment['file'];
          $path_att[$n_att]['file_name'] = end($file);
          $path_att[$n_att]['tipe_att'] = 'order';
          $n_att++;
        }

        //purchase order evaluation
        $poe_ids = array();
        foreach ($item['items'] as $key => $item_po) {
          if(!in_array($item_po['poe_id'],$poe_ids)){
            $poe_ids[] = $item_po['poe_id'];
          }          
        }

        if(!empty($poe_ids)){
          foreach ($poe_ids as $key => $poe_id) {
            if($item['tipe_po']=='INVENTORY MRP'){
              $modules_name = 'purchase_order_evaluation';
              $entity_poe  = $this->model->findPurchaseOrderEvaluationById($poe_id,$item['tipe_po']);
            }elseif ($item['tipe_po']=='EXPENSE') {
              $modules_name = 'expense_order_evaluation';
              $entity_poe  = $this->model->findPurchaseOrderEvaluationById($poe_id,$item['tipe_po']);
            }elseif ($item['tipe_po']=='CAPEX') {
              $modules_name = 'capex_order_evaluation';
              $entity_poe  = $this->model->findPurchaseOrderEvaluationById($poe_id,$item['tipe_po']);
            }elseif ($item['tipe_po']=='INVENTORY') {
              $modules_name = 'inventory_order_evaluation';
              $entity_poe  = $this->model->findPurchaseOrderEvaluationById($poe_id,$item['tipe_po']);
            }
    
            $this->data['entity']           = $entity_poe;
            $this->data['page']['title']    = 'PURCHASE ORDER EVALUATION';
            $this->data['page']['content']  = $this->modules[$modules_name]['view'] .'/print_pdf';

            $html = $this->load->view($this->pdf_theme, $this->data, true);
    
            $filename_poe = str_replace('/', '-', $entity_poe['evaluation_number']).".pdf";
    
            if(cekDirektori("./download/".$pdfFilePath)){
              $this->load->library('m_pdf');
    
              $pdf = $this->m_pdf->load(null, 'A4-L');
              $pdf->WriteHTML($html);
              // $pdf->Output($pdfFilePath, "I");
              $pdf->Output("./download/".$pdfFilePath."/".$filename_poe, "F");
              $path_po[$n]['path'] = $pdfFilePath."/".$filename_poe;
              $path_po[$n]['file_name'] = $filename_poe;
              $n++;
            }
            foreach($entity_poe['attachment'] as $key => $attachment){
              $file  = explode('/', $attachment['file']);
              $path_att[$n_att]['path'] = $attachment['file'];
              $path_att[$n_att]['file_name'] = end($file);
              $path_att[$n_att]['tipe_att'] = 'evaluation';
              $n_att++;
            }

            $request_item_ids = array();
            foreach ($entity_poe['request'] as $key => $request) {
              if(!in_array($request['inventory_purchase_request_detail_id'],$request_item_ids)){
                $request_item_ids[] = $request['inventory_purchase_request_detail_id'];
              }          
            }
          }
        }

        if(!empty($request_item_ids)){
          $request_ids = array();
          foreach ($request_item_ids as $key => $request_item_id) {
            $request_id = $this->model->getRequestIdByItemId($request_item_id,$item['tipe_po']);
            if(!in_array($request['id'],$request_ids)){
              $request_ids[] = $request_id;
            }
          }
        }

        if(!empty($request_ids)){
          foreach ($request_ids as $key => $request_id) {
            if($item['tipe_po']=='INVENTORY MRP'){
              $modules_name = 'purchase_request';
              $entity_request  = $this->model->findPurchaseRequestById($request_id,$item['tipe_po']);
            }elseif ($item['tipe_po']=='EXPENSE') {
              $modules_name = 'expense_request';
              $entity_request  = $this->model->findPurchaseRequestById($request_id,$item['tipe_po']);
            }elseif ($item['tipe_po']=='CAPEX') {
              $modules_name = 'capex_request';
              $entity_request  = $this->model->findPurchaseRequestById($request_id,$item['tipe_po']);
            }elseif ($item['tipe_po']=='INVENTORY') {
              $modules_name = 'inventory_request';
              $entity_request  = $this->model->findPurchaseRequestById($request_id,$item['tipe_po']);
            }
    
            $this->data['entity']           = $entity_request;
            if ($item['tipe_po']!='INVENTORY MRP') {
              $this->data['page']['title']    = $item['tipe_po'].' REQUEST';
            }else{
              $this->data['page']['title']    = 'PURCHASE REQUEST';
            }
            
            $this->data['page']['content']  = $this->modules[$modules_name]['view'] .'/print_pdf';

            $html = $this->load->view($this->pdf_theme, $this->data, true);
    
            $filename_request = str_replace('/', '-', $entity_request['pr_number']).".pdf";
    
            if(cekDirektori("./download/".$pdfFilePath)){
              $this->load->library('m_pdf');
    
              $pdf = $this->m_pdf->load(null, 'A4-L');
              $pdf->WriteHTML($html);
              // $pdf->Output($pdfFilePath, "I");
              $pdf->Output("./download/".$pdfFilePath."/".$filename_request, "F");
              $path_po[$n]['path'] = $pdfFilePath."/".$filename_request;
              $path_po[$n]['file_name'] = $filename_request;
              $n++;
            }
            foreach($entity_request['attachment'] as $key => $attachment){
              $file  = explode('/', $attachment['file']);
              $path_att[$n_att]['path'] = $attachment['file'];
              $path_att[$n_att]['file_name'] = end($file);
              $path_att[$n_att]['tipe_att'] = 'request';
              $n_att++;
            }
          }
        }
          
        
      }
    }
    

    // echo json_encode($path_att);

    $create_zip = new ZipArchive();
    $zip_name = "./download/".$pdfFilePath.".zip";

    if ($create_zip->open($zip_name, ZipArchive::CREATE)!==TRUE) {
      exit("cannot open the zip file <$zip_name>\n");
    }
    foreach($path_po as $key=>$po){
      $create_zip->addFile("./download/".$pdfFilePath."/".$po['file_name'] ,$po['file_name']);//add pdf PO
    }
    foreach($path_att as $key=>$att){
      $create_zip->addFile($att['path'] ,$att['file_name']);//add attachment
    }     
    $create_zip->close();

    redirect($zip_name);
    
  }

}
