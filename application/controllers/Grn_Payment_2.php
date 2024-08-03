<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Grn_Payment_2 extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['grn_payment'];
    $this->load->helper($this->module['helper']);
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function index_data_source()
  {
    
    if (is_granted($this->module, 'index') === FALSE){
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {
      $entities     = $this->model->getIndex();
      $data         = array();
      $no           = $_POST['start'];
      $remaining     = array();
      $total_amount = array();
      $amount_paid  = array();
      
      foreach ($entities as $row){
        $no++;
        $col = array();        
        $col[] = print_number($no);
        $col[] = print_string($row['document_number']);
        $col[] = print_date($row['document_date'],'d-M-Y');
        $col[] = print_string($row['vendor']);
        $col[] = print_string($row['default_currency']);
        $col[] = print_number($row['grand_total'], 2);
        $col[] = print_number($row['value_payment'], 2);
        $col[] = print_number($row['remaining_payment'], 2);
        $col[] = '';
        $col['DT_RowId'] = 'row_'. $row['id'];
        $col['DT_RowData']['pkey']  = $row['id'];

        $remaining[]     = $row['remaining_payment'];
        $total_amount[] = $row['grand_total'];
        $amount_paid[]  = $row['value_payment'];
        $data[]         = $col;
      }

      $result = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->model->countIndex(),
        "recordsFiltered" => $this->model->countIndexFiltered(),
        "data" => $data,
        "total" => array(
          5 => print_number(array_sum($total_amount),2),
          6 => print_number(array_sum($amount_paid),2),
          7 => print_number(array_sum($remaining),2),
        )
      );
    }

    echo json_encode($result);
  }
  public function index()
  {
    $this->authorized($this->module, 'index');

    $this->data['page']['title']            = $this->module['label'];
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source');
    $this->data['grid']['fixed_columns']    = 3;
    $this->data['grid']['summary_columns']  = array(5,6,7);
    $this->data['grid']['order_columns']    = array();
    $this->render_view($this->module['view'] .'/index');
  }

  
}
