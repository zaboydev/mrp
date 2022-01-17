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
        $col[]  = print_string($row['status']);
        $col[]  = print_date($row['tanggal']);
        $col[]  = print_string($row['document_number']);
        $col[]  = print_string($row['request_by']);
        $col[]  = print_string($row['cash_account_code']);
        $col[]  = print_number($row['request_amount'], 2);
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

    $this->data['page']['content']    = $this->module['view'] . '/create';
    $this->data['page']['title']      = 'create Cash request';

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
      if ($this->input->post('id')) {
        $update = $this->model->update($this->input->post('id'));
        if ($update['type']) {
          $return['type'] = 'success';
          $return['info'] = 'Cash Request number ' . $insert['document_number'] . ' revised.';
        } else {
          $return['type'] = 'danger';
          $return['info'] = 'There are error while updating data. Please try again later.';
        }
      } else {
        $insert = $this->model->insert();
        if ($insert['type']) {
          $return['type'] = 'success';
          $return['info'] = 'Cash Request number ' . $insert['document_number'] . ' created.';
        } else {
          $return['type'] = 'danger';
          $return['info'] = 'There are error while updating data. Please try again later.';
        }
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

}
