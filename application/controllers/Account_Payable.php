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
    $this->data['grid']['summary_columns']  = array(4, 5);

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
      $quantity     = array();
      $total_bayar   = array();
      $total_value  = array();

      foreach ($entities as $row) {
        $no++;
        $col = array();
        $col[]  = print_number($no);
        $col[]  = print_date($row['document_date']);
        $col[]  = print_string($row['document_number']);
        $col[]  = print_string($row['vendor']);
        $col[]  = print_number($row['grand_total'], 2);
        $col[]  = print_number($row['payment'], 2);
        $col[]  = print_string($row['status']);
        $total_value[] = $row['grand_total'];
        $total_bayar[] = $row['payment'];

        $col['DT_RowId'] = 'row_' . $row['id'];
        $col['DT_RowData']['pkey']  = $row['id'];

        if ($this->has_role($this->module, 'info')) {
          $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] . '/info/' . $row['id']);
          $col['DT_RowAttr']['data-id'] = $row['id'];
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
  
}
