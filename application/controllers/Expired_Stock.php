<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Expired_Stock extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['expired_stock'];
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function index()
  {
    $this->authorized($this->module, 'index');

    // if (isset($_POST['start_date']) && $_POST['start_date'] !== NULL){
    //   $start_date  = $_POST['start_date'];
    //   $date        = strtotime('+90 day',strtotime($start_date));
    //   $end_date    = date('Y-m-d', $date);
    //   $periode = print_date($end_date,'d F Y');
    // } else {
    //   $start_date  = date('Y-m-d');
    //   $date        = strtotime('+90 day',strtotime($start_date));
    //   $end_date    = date('Y-m-d', $date);
    //   $periode = print_date($end_date,'d F Y');

    // }

    // if (isset($_POST['condition']) && $_POST['condition'] !== NULL){
    //   $condition  = $_POST['condition'];
    // } else {
    //   $condition  = "SERVICEABLE";
    // }

    // if (isset($_POST['category']) && $_POST['category'] !== NULL){
    //   $category = $_POST['category'];
    // } else {
    //   $category = NULL;
    // }

    // if (isset($_POST['warehouse']) && $_POST['warehouse'] !== NULL){
    //   $warehouse = $_POST['warehouse'];
    // } else {
    //   $warehouse = 'ALL BASES';
    // }

    $this->data['page']['title']            = $this->module['label'];
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source');
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array( 7 );
    // $this->data['grid']['summary_columns']  = array( 7, 8, 9, 10, 11 );
    $this->data['grid']['order_columns']    = array (
      0 => array ( 0 => 4, 1 => 'asc' ),
      1 => array ( 0 => 5, 1 => 'asc' ),
      2 => array ( 0 => 2, 1 => 'asc' ),
      3 => array ( 0 => 1, 1 => 'asc' ),
      4 => array ( 0 => 3, 1 => 'asc' ),
      5 => array ( 0 => 6, 1 => 'asc' ),
      6 => array ( 0 => 7, 1 => 'asc' ),
      7 => array ( 0 => 8, 1 => 'asc' ),
      8 => array ( 0 => 9, 1 => 'asc' ),
      9 => array ( 0 => 10, 1 => 'asc' ),
      //10 => array ( 0 => 11, 1 => 'asc' ),
      // 11 => array ( 0 => 12, 1 => 'asc' ),
      // 12 => array ( 0 => 13, 1 => 'asc' ),
      // 13 => array ( 0 => 14, 1 => 'asc' ),
    );

    $this->render_view($this->module['view'] .'/index');
  }

  public function index_data_source()
  {
    $this->authorized($this->module, 'index');

    // if ($warehouse !== NULL){
    //   $warehouse = (urldecode($warehouse) === 'ALL BASES') ? NULL : urldecode($warehouse);
    // } 
    // else {
    //   $warehouse = urldecode($warehouse);
    // }

    // if ($category !== NULL){
    //   $category = urldecode($category);
    // }

    $entities = $this->model->getIndex();

    $data = array();
    $no = $_POST['start'];
    $initial_quantity = array();
    $received_quantity = array();
    $issued_quantity = array();
    $adjustment_quantity = array();
    $quantity = array();

    foreach ($entities as $row){
      $no++;
      $col = array();
      $col[] = print_number($no);
      $col[] = print_string($row['part_number']);
      $col[] = print_string($row['description']);
      $col[] = print_string($row['serial_number']);
      if(print_date($row['expired_date'], 'd F Y') == 'UNKNOWN'){
        $col[] = print_string('-');
      }else{
        $col[] = print_date($row['expired_date'], 'd F Y');
      }
      $col[] = print_string($row['category']);
      $col[] = print_string($row['group']);
      $col[] = print_string($row['condition']);
      // $col[] = print_number($row['initial_quantity'], 2);
      // $col[] = print_number($row['received_quantity'], 2);
      // $col[] = print_number($row['issued_quantity'], 2);
      // $col[] = print_number($row['adjustment_quantity'], 2);
      $col[] = print_number($row['quantity'], 2);
      $col[] = print_number($row['unit_value'], 2);
      $col[] = print_number($row['minimum_quantity'], 2);
      $col[] = print_string($row['unit']);      
      $col[]  = print_string($row['coa']);
      $col[]  = print_string($row['kode_stok']);
      $col[] = print_string($row['stores']);
      $col[] = print_string($row['warehouse']);
      $col[] = print_string($row['remarks']);
      $col[] = print_string($row['reference_document']);
      

      $col['DT_RowId'] = 'row_'. $row['id'];
      $col['DT_RowData']['pkey'] = $row['id'];
      // $prev_quantity[] = $row['previous_quantity'];
      // $balance_quantity[] = $row['balance_quantity'];

      $quantity[] = $row['quantity'];

      $data[] = $col;
    }

    $result = array(
      "draw" => $_POST['draw'],
      "recordsTotal" => $this->model->countIndex(),
      "recordsFiltered" => $this->model->countIndexFiltered(),
      "data" => $data,
      "total" => array(
        7 => print_number(array_sum($quantity), 2),
        // 7 => print_number(array_sum($quantity), 2),
        // 8 => print_number(array_sum($balance_quantity), 2),
      )
    );

    echo json_encode($result);
  }

  

  public function detail()
  {
    $this->authorized($this->module, 'detail');

    if (isset($_GET['part_number']) && $_GET['part_number'] !== NULL){
      $part_number = $_GET['part_number'];
    } else {
      $part_number = NULL;
    }

    
    $this->data['page']['title']            = $this->module['label'].' Part Number : '.$part_number;
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getDetailSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/detail_data_source?part_number='.$part_number);
    $this->data['grid']['fixed_columns']    = 2;
    
    $this->data['grid']['summary_columns']  = array( 6 );
    $this->data['grid']['order_columns']    = array (
      0 => array ( 0 => 3, 1 => 'asc' ),
      1 => array ( 0 => 4, 1 => 'asc' ),
      2 => array ( 0 => 2, 1 => 'asc' ),
      3 => array ( 0 => 1, 1 => 'asc' ),
      4 => array ( 0 => 5, 1 => 'asc' ),
      5 => array ( 0 => 6, 1 => 'asc' ),
      6 => array ( 0 => 7, 1 => 'asc' ),
      7 => array ( 0 => 8, 1 => 'asc' ),
      8 => array ( 0 => 8, 1 => 'asc' ),
    );

    $this->render_view($this->module['view'] .'/detail');
  }

  public function detail_data_source()
  {
    $this->authorized($this->module, 'index');

    if (isset($_GET['part_number']) && $_GET['part_number'] !== NULL){
      $part_number = $_GET['part_number'];
    } else {
      $part_number = NULL;
    }

    $entities = $this->model->getDetailIndex($part_number);

    $data = array();
    $no = $_POST['start'];
    $total_quantity = array();

    foreach ($entities as $row){
      $no++;
      $col = array();
      $col[] = print_number($no);
      $col[] = print_string($row['part_number']);
      $col[] = print_string($row['serial_number']);
      $col[] = print_string($row['description']);
      $col[] = print_string($row['category']);
      $col[] = print_string($row['group']);
      $col[] = print_number($row['total_quantity'],2);      
      $col[] = print_number($row['minimum_quantity'], 2);
      $col[] = print_string($row['unit']);
      $col['DT_RowId'] = 'row_'. $row['id'];
      $col['DT_RowData']['pkey'] = $row['id'];
      $total_quantity[]=$row['total_quantity'];


      if ($this->has_role($this->modules['stock_card'], 'info')){
        $col['DT_RowAttr']['onClick']     = '$(this).redirect("_self");';
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
          6 => print_number(array_sum($total_quantity), 2)
       )
     );

    echo json_encode($result);
  }



}
