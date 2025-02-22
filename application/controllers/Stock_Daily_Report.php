<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_Daily_Report extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['stock_daily_report'];
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function index()
  {
    $this->authorized($this->module, 'index');

    $this->data['page']['title']            = $this->module['label'];
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source/');
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array( 6, 7, 8 );
    $this->data['grid']['order_columns']    = array ();

    $this->render_view($this->module['view'] .'/index');
  }

  public function index_data_source()
  {
    $this->authorized($this->module, 'index');

    $entities = $this->model->getIndex();

    $data = array();
    $no = $_POST['start'];
    $quantity = array();

    foreach ($entities as $row){
      $no++;
      $col = array();
      $col[] = print_number($no);
      $col[] = print_string($row['description']);
      $col[] = print_string($row['part_number']);
      $col[] = print_string($row['category']);
      $col[] = print_string($row['group']);
      $col[] = print_string($row['condition']);
      // $col[] = print_date($row['created_at']);
      $col[] = print_number($row['prev_quantity'], 2);
      $col[] = print_number($row['quantity'], 2);
      $col[] = print_number($row['balance_quantity'], 2);
      $col['DT_RowId'] = 'row_'. $row['id'];
      $col['DT_RowData']['pkey'] = $row['id'];
      $quantity[] = $row['quantity'];
      $prev_quantity[] = $row['prev_quantity'];
      $balance_quantity[] = $row['balance_quantity'];

      $data[] = $col;
    }

    $result = array(
      "draw" => $_POST['draw'],
      "recordsTotal" => $this->model->countIndex(),
      "recordsFiltered" => $this->model->countIndexFiltered(),
      "data" => $data,
      "total" => array(
        6 => print_number(array_sum($prev_quantity), 2),
        7 => print_number(array_sum($quantity), 2),
        8 => print_number(array_sum($balance_quantity), 2),
      )
    );

    echo json_encode($result);
  }
}
