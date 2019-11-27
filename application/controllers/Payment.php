<?php defined('BASEPATH') or exit('No direct script access allowed');

class Payment extends MY_Controller
{
  protected $module;
  protected $id_item = 0;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['payment'];
    $this->load->model($this->module['model'], 'model');
    $this->load->helper($this->module['helper']);
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
      $total     = array();

      foreach ($entities as $row) {
        $no++;
        $col = array();
        if ($row['status'] == 'WAITING') {
          // if(config_item('auth_role') == 'CHIEF OF MAINTANCE' || config_item('auth_role') == 'SUPER ADMIN'){
          if (is_granted($this->module, 'approval') === TRUE) {
            $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
          } else {
            $col[] = print_number($no);
          }
        }else{
          $col[]  = print_number($no);
        }
        $col[]  = print_string($row['no_transaksi']);
        $col[]  = print_date($row['tanggal']);
        $col[]  = print_string($row['no_cheque']);
        $col[]  = print_string($row['document_number']);
        $col[]  = print_string($row['vendor']);
        $col[]  = print_string($row['part_number']);
        $col[]  = print_string($row['description']);
        $col[]  = print_string($row['default_currency']);
        $col[]  = print_number($row['amount_paid'], 2);
        $col[]  = print_string($row['status']);
        $col[]  = print_string($row['created_by']);
        $col[]  = print_date($row['created_at']);

      

        $total[] = $row['amount_paid'];

        $col['DT_RowId'] = 'row_' . $row['id'];
        $col['DT_RowData']['pkey']  = $row['id'];

        if ($this->has_role($this->module, 'info')) {
          // $col['DT_RowAttr']['onClick']     = '$(this).popup();';
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
          9 => print_number(array_sum($total), 2),
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
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] . '/index_data_source');
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array(9);

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

  public function create()
  {
    $this->data['currency']                 = 'IDR';
    $this->data['page']['title']            = $this->module['label'];
    $this->data['account']                  = $this->model->getAccount($this->data['currency']);
    $this->data['suplier']                  = $this->model->getSuplier($this->data['currency']);
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
    echo json_encode($return);
  }

  public function save()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');
    $save = $this->model->save();
    if ($save) {
      $result["status"] = "success";
    } else {
      $result["status"] = "failed";
    }
    echo json_encode($result);
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
    foreach ($id_purchase_order as $key) {
      if ($this->model->approve($key)) {
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
        'info' => $success . " data has been approved!"
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
    $this->authorized($this->module, 'document');

    // if ($category !== NULL){
    $item       = $this->model->findById($id);

    $_SESSION['payment']                          = $item;
    $_SESSION['payment']['no_transaksi']          = $item['no_transaksi'];
    $_SESSION['payment']['vendor']                = $item['vendor'];

    $this->data['account']                  = $this->model->getAccount($this->data['currency']);
    $this->data['suplier']                  = $this->model->getSuplier($this->data['currency']);
    

    $this->render_view($this->module['view'] . '/bayar');
  }

}
