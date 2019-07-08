<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_Adjustment extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['stock_adjustment'];
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
    $this->data['grid']['summary_columns']  = array( 11,12,13 );
    if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'PROCUREMENT'){
      $this->data['grid']['summary_columns'][]=17;
    }
    $this->data['grid']['order_columns']    = array (
      0 => array ( 0 => 3, 1 => 'asc' ),
      1 => array ( 0 => 4, 1 => 'asc' ),
      2 => array ( 0 => 2, 1 => 'asc' ),
      3 => array ( 0 => 1, 1 => 'asc' ),
      4 => array ( 0 => 5, 1 => 'asc' ),
      5 => array ( 0 => 6, 1 => 'asc' ),
      6 => array ( 0 => 7, 1 => 'asc' ),
      7 => array ( 0 => 8, 1 => 'asc' ),
      8 => array ( 0 => 9, 1 => 'asc' ),
      9 => array ( 0 => 10, 1 => 'asc' ),
      10 => array ( 0 => 11, 1 => 'asc' ),
      11 => array ( 0 => 12, 1 => 'asc' ),
    );

    $this->render_view($this->module['view'] .'/index');
  }

  public function index_data_source()
  {
    $this->authorized($this->module, 'index');

    $entities = $this->model->getIndex();

    $data = array();
    $no = $_POST['start'];
    $total_previous_quantity = array();
    $total_adjustment_quantity = array();
    $total_balance_quantity = array();
    $total_price = array();

    foreach ($entities as $row){
      $no++;
      $col = array();
      $col[] = print_number($no);
      $col[]  = print_string($row['id']);
      $col[] = print_date($row['date_of_entry'], 'Y-m-d');
      $col[] = print_string($row['part_number']);      
      $col[] = print_string($row['serial_number']);
      $col[] = print_string($row['description']);
      $col[] = print_string($row['category']);
      $col[] = print_string($row['group']);
      $col[] = print_string($row['warehouse']);
      $col[] = print_string($row['stores']);
      $col[] = print_string($row['condition']);
      $col[] = print_number($row['previous_quantity'], 2);
      $col[] = print_number($row['adjustment_quantity'], 2);
      $col[] = print_number($row['balance_quantity'], 2);
      $col[] = print_string($row['unit']);
      $col[] = $row['remarks'];
      if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'PROCUREMENT' || config_item('auth_role') == 'SUPER ADMIN'){
        $col[] = print_number($row['unit_value'], 2);
        $col[] = print_number($row['total_value'], 2);
        $total_price[] = $row['total_value'];
      }
      
      $col['DT_RowId'] = 'row_'. $row['id'];
      $col['DT_RowData']['pkey'] = $row['id'];
      $total_previous_quantity[] = $row['previous_quantity'];
      $total_adjustment_quantity[] = $row['adjustment_quantity'];
      $total_balance_quantity[] = $row['balance_quantity'];
      

      if ($this->has_role($this->modules['stock_card'], 'info')){
        $col['DT_RowAttr']['onClick']   = '$(this).redirect("_self");';
        $col['DT_RowAttr']['data-href'] = site_url($this->modules['stock_card']['route'] .'/info/'. $row['id']);
      }

      $data[] = $col;
    }

    $result = array(
      "draw" => $_POST['draw'],
      "recordsTotal" => $this->model->countIndex(),
      "recordsFiltered" => $this->model->countIndexFiltered(),
      "data" => $data,
      "total" => array(
        11 => print_number(array_sum($total_previous_quantity), 2),
        12 => print_number(array_sum($total_adjustment_quantity), 2),
        13 => print_number(array_sum($total_balance_quantity), 2),
        // 14 => print_number(array_sum($total_price), 2),
      )
    );
    if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'PROCUREMENT' || config_item('auth_role') == 'SUPER ADMIN'){
      $result['total'][16] = print_number(array_sum($total_price), 2);
    }

    echo json_encode($result);
  }
}
