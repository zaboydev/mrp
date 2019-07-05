<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_Opname extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['stock_opname'];
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function index()
  {
    $this->authorized($this->module, 'index');

    if (isset($_POST['month']) && $_POST['month'] !== NULL){
      $period_month = $_POST['month'];
    } else {
      $period_month = config_item('period_month');
    }

    if (isset($_POST['year']) && $_POST['year'] !== NULL){
      $period_year = $_POST['year'];
    } else {
      $period_year = config_item('period_year');
    }

    if (isset($_POST['condition']) && $_POST['condition'] !== NULL){
      $condition  = $_POST['condition'];
    } else {
      $condition  = "SERVICEABLE";
    }

    if (isset($_POST['category']) && $_POST['category'] !== NULL){
      $category = $_POST['category'];
    } else {
      $category = NULL;
    }

    if (isset($_POST['warehouse']) && $_POST['warehouse'] !== NULL){
      $warehouse = $_POST['warehouse'];
    } else {
      $warehouse = 'ALL BASES';
    }

    $this->data['selected_month']           = $period_month;
    $this->data['selected_year']            = $period_year;
    $this->data['selected_category']        = $category;
    $this->data['selected_condition']       = $condition;
    $this->data['selected_warehouse']       = $warehouse;

    $this->data['page']['title']            = $this->module['label'] .' '. $warehouse .' '. $category .' '. $condition .' '. numberToMonthName($period_month) .' '. $period_year;
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source/'. $period_month .'/'. $period_year .'/'. $condition .'/'. $warehouse .'/'. $category);
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array( 9, 10, 11, 12, 13, 14, 15 );
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
      12 => array ( 0 => 13, 1 => 'asc' ),
      13 => array ( 0 => 14, 1 => 'asc' ),
      14 => array ( 0 => 15, 1 => 'asc' ),
      15 => array ( 0 => 16, 1 => 'asc' ),
      16 => array ( 0 => 17, 1 => 'asc' ),
    );

    $this->render_view($this->module['view'] .'/index');
  }

  public function index_data_source($period_month, $period_year, $condition = "SERVICEABLE", $warehouse = 'ALL BASES', $category = NULL)
  {
    $this->authorized($this->module, 'index');

    if ($category !== NULL){
      $category = urldecode($category);
    }

    if ($warehouse !== NULL){
      $warehouse = (urldecode($warehouse) === 'ALL BASES') ? NULL : urldecode($warehouse);
    } else {
      $warehouse = NULL;
    }

    $entities = $this->model->getIndex($period_month, $period_year, $condition, $warehouse, $category);

    $data = array();
    $no   = $_POST['start'];

    $previous_quantity          = array();
    $total_received_quantity    = array();
    $total_issued_quantity      = array();
    $total_adjustment_quantity  = array();
    $current_quantity           = array();
    $current_total_value        = array();
    $current_average_value      = array();

    foreach ($entities as $row){
      $no++;
      $col = array();
      $col[] = print_number($no);
      $col[] = print_string($row['part_number']);
      $col[] = print_string($row['description']);
      $col[] = print_string($row['serial_number']);
      $col[] = print_string($row['category']);
      $col[] = print_string($row['group']);
      $col[] = print_string($row['condition']);
      $col[] = print_string($row['warehouse']);
      $col[] = print_string($row['stores']);
      $col[] = print_number($row['previous_quantity'], 2);
      $col[] = print_number($row['total_received_quantity'], 2);
      $col[] = print_number($row['total_issued_quantity'], 2);
      $col[] = print_number($row['total_adjustment_quantity'], 2);
      $col[] = print_number($row['current_quantity'], 2);
      $col[] = print_number($row['current_total_value'], 2);
      $col[] = print_number($row['current_average_value'], 2);
      $col[] = print_number($row['minimum_quantity'], 2);
      $col[] = print_string($row['unit']);

      $col['DT_RowId']              = 'row_'. $row['id'];
      $col['DT_RowData']['pkey']    = $row['id'];

      $previous_quantity[]          = $row['previous_quantity'];
      $total_received_quantity[]    = $row['total_received_quantity'];
      $total_issued_quantity[]      = $row['total_issued_quantity'];
      $total_adjustment_quantity[]  = $row['total_adjustment_quantity'];
      $current_quantity[]           = $row['current_quantity'];
      $current_total_value[]        = $row['current_total_value'];
      $current_average_value[]      = $row['current_average_value'];

      // if ($this->has_role($this->modules['stock_card'], 'info')){
      //   $col['DT_RowAttr']['onClick']   = '$(this).redirect("_self");';
      //   $col['DT_RowAttr']['data-href'] = site_url($this->modules['stock_card']['route'] .'/info/'. $row['id']);
      // }

      $data[] = $col;
    }

    $current_average_value = (array_sum($current_quantity) == 0) ? floatval(0) : array_sum($current_total_value)/array_sum($current_quantity);

    $result = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->model->countIndex($period_month, $period_year, $condition, $warehouse, $category),
        "recordsFiltered" => $this->model->countIndexFiltered($period_month, $period_year, $condition, $warehouse, $category),
        "data" => $data,
        "total" => array(
          9 => print_number(array_sum($previous_quantity), 2),
          10 => print_number(array_sum($total_received_quantity), 2),
          11 => print_number(array_sum($total_issued_quantity), 2),
          12 => print_number(array_sum($total_adjustment_quantity), 2),
          13 => print_number(array_sum($current_quantity), 2),
          14 => print_number(array_sum($current_total_value), 2),
          15 => print_number($current_average_value, 2),
        )
      );

    echo json_encode($result);
  }

  public function create()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'create') == FALSE){
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to do this operation!';
    } else {
      if ($this->model->create()){
        $data['success'] = TRUE;
        $data['message'] = 'Stock opname operation success. You will redirected now.';
      } else {
        $data['success'] = FALSE;
        $data['message'] = 'Error while opname stock. Please ask Technical Support.';
      }
    }

    echo json_encode($data);
  }
}
