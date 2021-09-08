<?php defined('BASEPATH') or exit('No direct script access allowed');

class Account_Payable extends MY_Controller
{
  protected $module;
  protected $id_item = 0;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['account_payable'];
    $this->load->model($this->module['model'], 'model');
    $this->load->helper($this->module['helper']);
    $this->data['module'] = $this->module;
  }

  public function index()
  {
    $this->authorized($this->module, 'index');

    $this->data['page']['title']            = $this->module['label'];
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] . '/index_data_source');
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array(4, 5, 6);

    $this->data['grid']['order_columns']    = array();
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
        $evaluation_number = $this->model->getEvaluationNumber($row['id']);
        $poe_id = $this->model->getEvaluationId($row['id']);
        $col[]  = print_number($no);
        $col[]  = print_date($row['document_date']);
        $col[]  = print_string($row['document_number']);
        $col[]  = print_string($row['vendor']);
        $col[]  = print_number($row['grand_total'], 2);
        $col[]  = print_number($row['payment'], 2);
        $col[]  = print_number($row['remaining_payment'], 2);
        $col[]  = print_string($row['status']);
        $col[]  = '<a>'.$evaluation_number.'</a>';
        $col[]  = isAttachementExists($poe_id,'POE') ==0 ? '' : '<a href="#" class="btn btn-icon-toggle btn-info btn-sm ">
                        <i class="fa fa-eye" data-id="' . $poe_id . '"></i>
                        </a>';
        
        $total_value[] = $row['grand_total'];
        $total_bayar[] = $row['payment'];
        $total_sisa[] = $row['remaining_payment'];

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
          4 => print_number(array_sum($total_value), 2),
          5 => print_number(array_sum($total_bayar), 2),
          6 => print_number(array_sum($total_sisa), 2),
        )
      );
    }

    echo json_encode($result);
  }

  public function urgent($id)
  {
    $result['status'] = "failed";
    $urgent = $this->model->urgent($id);
    if ($urgent) {
      $result['status'] = "success";
      $this->sendEmail();
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

      $return['type'] = 'success';

      // if ($entity['status'] === 'evaluation') {
      //   $return['info'] = $this->load->view($this->modules['purchase_order_evaluation']['view'] . '/info', $this->data, TRUE);
      // } else {
        
      // }
      $return['info'] = $this->load->view($this->module['view'] . '/info', $this->data, TRUE);
    }

    echo json_encode($return);
  }

  public function print_prl($request_id,$tipe)
  {
    if($tipe=='EXPENSE'){
      redirect('expense_request/print_pdf/'.$request_id);
    }elseif($tipe=='CAPEX'){
      redirect('capex_request/print_pdf/'.$request_id);
    }elseif($tipe=='INVENTORY'){
      redirect('inventory_request/print_pdf/'.$request_id);
    }elseif($tipe=='INVENTORY MRP'){
      redirect('purchase_request/print_pdf/'.$request_id);
    }
    
  }
}
