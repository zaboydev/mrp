<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_General extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['stock_general'];
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function index()
  {
    $this->authorized($this->module, 'index');

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

    $this->data['selected_category']        = $category;
    $this->data['selected_condition']       = $condition;
    $this->data['page']['title']            = $this->module['label'] .' '. $category .' '. $condition;
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source/'. $condition .'/'. $category);
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array( 7 );
    $this->data['grid']['order_columns']    = array (
      0 => array ( 0 => 3, 1 => 'asc' ),
      1 => array ( 0 => 4, 1 => 'asc' ),
      2 => array ( 0 => 2, 1 => 'asc' ),
      3 => array ( 0 => 1, 1 => 'asc' ),
      4 => array ( 0 => 5, 1 => 'asc' ),
      5 => array ( 0 => 6, 1 => 'asc' ),
      6 => array ( 0 => 7, 1 => 'desc' ),
      7 => array ( 0 => 8, 1 => 'asc' ),
      8 => array ( 0 => 9, 1 => 'asc' ),
    );

    $this->render_view($this->module['view'] .'/index');
  }

  public function index_data_source($condition = "SERVICEABLE", $category = NULL)
  {
    $this->authorized($this->module, 'index');

    if ($category !== NULL){
      $category = urldecode($category);
    }

    $entities = $this->model->getIndex($condition, $category);

    $data = array();
    $no = $_POST['start'];
    $grand_quantity = array();

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
      $col[] = print_number($row['total_quantity'], 2);
      $col[] = print_number($row['minimum_quantity'], 2);
      $col[] = print_string($row['unit']);
      $col['DT_RowId'] = 'row_'. $row['id'];
      $col['DT_RowData']['pkey'] = $row['id'];
      $grand_quantity[] = $row['total_quantity'];

      if ($this->has_role($this->modules['stock_card'], 'info')){
        $col['DT_RowAttr']['onClick']   = '$(this).redirect("_self");';
        $col['DT_RowAttr']['data-href'] = site_url($this->modules['stock_card']['route'] .'/info/'. $row['id']);
      }

      $data[] = $col;
    }

    $result = array(
      "draw" => $_POST['draw'],
      "recordsTotal" => $this->model->countIndex($condition, $category),
      "recordsFiltered" => $this->model->countIndexFiltered($condition, $category),
      "data" => $data,
      "total" => array(
        7 => print_number(array_sum($grand_quantity), 2)
      )
    );

    echo json_encode($result);
  }
}
