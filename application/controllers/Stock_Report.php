<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_Report extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['stock_report'];
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function index()
  {
    $this->authorized($this->module, 'index');

    $period_year = config_item('period_year');
    $period_month = config_item('period_month')-1;


    if (isset($_POST['start_date']) && $_POST['start_date'] && isset($_POST['end_date']) && $_POST['end_date'] !== NULL){
      $start_date  = $_POST['start_date'];
      $end_date = $_POST['end_date'];
      $periode=print_date($start_date,'d F Y').' - '.print_date($end_date,'d F Y');
    } else {
      $start_date  = NULL;
      $end_date = NULL;
      $periode = 'ALL Periode';

    }

    if (isset($_POST['condition']) && $_POST['condition'] !== NULL){
      $condition  = $_POST['condition'];
    } else {
      $condition  = "all";
    }

    if (isset($_POST['category']) && $_POST['category'] !== NULL){
      $category = $_POST['category'];
    } else {
      $category = 'all';
    }

    if (isset($_POST['warehouse']) && $_POST['warehouse'] !== NULL){
      $warehouse = $_POST['warehouse'];
    } else {
      $warehouse = 'ALL BASES';
    }
    
    $this->data['selected_category']        = $category;
    $this->data['selected_condition']       = $condition;
    $this->data['selected_warehouse']       = $warehouse;
    $this->data['document']       = 'index_no_shipping';
    $this->data['document_super_admin']       = 'index_super_admin';


    // $now=date('Y-m-d');
    // if (!empty($_POST['columns'][6]['search']['value'])){
    //   $search_issued_date = $_POST['columns'][6]['search']['value'];
    //   $range_issued_date  = explode(' ', $search_issued_date);
    //   $this->data['page']['title']            = $this->module['label'].' '.print_date($range_issued_date[0]).' - '.print_date($range_issued_date[1]);
    // }if (empty($_POST['columns'][6]['search']['value'])){
    //   $this->data['page']['title']            = $this->module['label'].' '.print_date($now);
    // }

    //$this->data['page']['title']            = $this->module['label'];
    $this->data['page']['title']            = $this->module['label'] .' '. $warehouse.' '. $category .' '. $condition.' / PERIODE : '.$periode;
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getIndexSelectedColumns());
    // $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source/'. $period_month .'/'. $period_year .'/'. $condition);
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source/'. $condition .'/'. $warehouse.'/'. $category.'/'.$start_date.'/'.$end_date);
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array( 10);
    if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'SUPER ADMIN'){
      $this->data['grid']['summary_columns'][] = 14;
    }
    $this->data['grid']['order_columns']    = array (
      // 0 => array ( 0 => 3, 1 => 'asc' ),
      // 1 => array ( 0 => 4, 1 => 'asc' ),
      // 2 => array ( 0 => 2, 1 => 'asc' ),
      // 3 => array ( 0 => 1, 1 => 'asc' ),
      // 4 => array ( 0 => 5, 1 => 'asc' ),
      // 5 => array ( 0 => 6, 1 => 'asc' ),
      // 6 => array ( 0 => 7, 1 => 'asc' ),
      // 7 => array ( 0 => 8, 1 => 'asc' ),
      // 8 => array ( 0 => 9, 1 => 'asc' ),
      // 9 => array ( 0 => 10, 1 => 'asc' ),
      /*10 => array ( 0 => 11, 1 => 'asc' ),
      11 => array ( 0 => 12, 1 => 'asc' ),*/
      // 12 => array ( 0 => 13, 1 => 'asc' ),
    );

    $this->render_view($this->module['view'] .'/index');
  }

  public function index_data_source($condition = 'all', $warehouse='ALL BASES', $category = 'all', $start_date = NULL, $end_date = NULL)
  {
    $this->authorized($this->module, 'index');

    if ($warehouse !== NULL){
      $warehouse = (urldecode($warehouse) === 'ALL BASES') ? NULL : urldecode($warehouse);
    } 
    else {
      $warehouse = urldecode($warehouse);
    }

    if ($condition !== NULL){
      $condition = (urldecode($condition) === 'all') ? NULL : urldecode($condition);
    } 
    else {
      $condition = urldecode($condition);
    }

    // if ($category !== NULL){
    //   $category = urldecode($category);
    // }
    if ($category !== NULL){
      $category = (urldecode($category) === 'all') ? NULL : urldecode($category);
    } 
    else {
      $category = urldecode($category);
    }

    if ($start_date && $end_date !== NULL){
      $start_date  = urldecode($start_date);
      $end_date = urldecode($end_date);
    }

    $entities = $this->model->getIndex($condition, $warehouse, $start_date, $end_date, $category);

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
      $col[]  = print_string($row['item_id']);
      $col[] = print_string($row['part_number']);
      $col[] = print_string($row['description']);
      $col[] = print_string($row['serial_number']);
      $col[] = print_string($row['kode_stok']);
      $col[] = print_string($row['coa']);
      $col[] = print_string($row['category']);
      $col[] = print_string($row['group']);
      $col[] = print_string($row['condition']);
      $col[] = print_number($row['qty'], 2);
      $col[] = print_string($row['unit']);    
      // $col[] = print_number($row['unit_value'], 2);
      $col[] = print_number($row['minimum_quantity'], 2);            
      $col[] = print_string($row['stores']);
      // $col[] = print_string($row['warehouse']);
      if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'SUPER ADMIN'){
        $col[] = print_number($row['total_value'], 2);
        
        // $col[] = print_number($row['total_value'], 2);
      }



      

      $col['DT_RowId']              = 'row_'. $row['id'];
      $col['DT_RowData']['pkey']    = $row['id'];

      // $previous_quantity[]          = $row['previous_quantity'];
      // $total_received_quantity[]    = $row['total_received_quantity'];
      // $total_issued_quantity[]      = $row['total_issued_quantity'];
      // $total_adjustment_quantity[]  = $row['total_adjustment_quantity'];
      $current_quantity[]           = $row['qty'];
      if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'SUPER ADMIN'){
        // $current_total_value[]        = $row['total_value'];
        $current_total_value[]        = $row['total_value'];
      }
      // $current_average_value[]      = $row['current_average_value'];

      // if ($this->has_role($this->module, 'summary')){
      //   $col['DT_RowAttr']['onClick']   = '$(this).redirect("_self");';
      //   $col['DT_RowAttr']['data-href'] = site_url($this->module['route'] .'/summary?year='. $period_year .'&month='. $period_month .'&condition='. $condition .'&category='. $row['category']);
      // }

      $data[] = $col;
    }

    // $current_average_value = (array_sum($current_quantity) == 0) ? floatval(0) : array_sum($current_total_value)/array_sum($current_quantity);

    $result = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->model->countIndex($condition, $warehouse, $start_date, $end_date, $category),
        "recordsFiltered" => $this->model->countIndexFiltered($condition, $warehouse, $start_date, $end_date, $category),
        "data" => $data,
        "total" => array(
          
          10 => print_number(array_sum($current_quantity), 2),
          
        )
      );
    if(config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'SUPER ADMIN'){
      $result['total'][14] = print_number(array_sum($current_total_value), 2);
    }

    echo json_encode($result);
  }

  public function summary()
  {
    $this->authorized($this->module, 'summary');

    if (isset($_GET['category']) && $_GET['category'] !== NULL){
      $category = $_GET['category'];
    } else {
      $category = NULL;
    }

    if (isset($_GET['month']) && $_GET['month'] !== NULL){
      $period_month = $_GET['month'];
    } else {
      $period_month = config_item('period_month');
    }

    if (isset($_GET['year']) && $_GET['year'] !== NULL){
      $period_year = $_GET['year'];
    } else {
      $period_year = config_item('period_year');
    }

    if (isset($_GET['condition']) && $_GET['condition'] !== NULL){
      $condition  = $_GET['condition'];
    } else {
      $condition  = "SERVICEABLE";
    }

    $this->data['selected_category']        = $category;
    $this->data['selected_month']           = $period_month;
    $this->data['selected_year']            = $period_year;
    $this->data['selected_condition']       = $condition;

    $this->data['page']['title']            = $this->module['label'] .' '. $category .' '. $condition .' '. numberToMonthName($period_month) .' '. $period_year;
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getSummarySelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/summary_data_source/'. $period_month .'/'. $period_year .'/'. $condition .'/'. $category);
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array( 4, 5, 6, 7, 8, 9, 10 );
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
    );

    $this->render_view($this->module['view'] .'/summary');
  }

  public function summary_data_source($period_month, $period_year, $condition = "SERVICEABLE", $category = NULL)
  {
    $this->authorized($this->module, 'summary');

    if ($category !== NULL){
      $category = urldecode($category);
    }

    $entities = $this->model->getSummary($period_month, $period_year, $condition, $category);

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
      $col[] = print_string($row['group']);
      $col[] = print_string($row['category']);
      $col[] = print_string($row['condition']);
      $col[] = print_number($row['previous_quantity'], 2);
      $col[] = print_number($row['total_received_quantity'], 2);
      $col[] = print_number($row['total_issued_quantity'], 2);
      $col[] = print_number($row['total_adjustment_quantity'], 2);
      $col[] = print_number($row['current_quantity'], 2);
      $col[] = print_number($row['current_total_value'], 2);
      $col[] = print_number($row['current_average_value'], 2);

      $col['DT_RowId']              = 'row_'. $row['id'];
      $col['DT_RowData']['pkey']    = $row['id'];

      $previous_quantity[]          = $row['previous_quantity'];
      $total_received_quantity[]    = $row['total_received_quantity'];
      $total_issued_quantity[]      = $row['total_issued_quantity'];
      $total_adjustment_quantity[]  = $row['total_adjustment_quantity'];
      $current_quantity[]           = $row['current_quantity'];
      $current_total_value[]        = $row['current_total_value'];
      $current_average_value[]      = $row['current_average_value'];

      if ($this->has_role($this->module, 'detail')){
        $col['DT_RowAttr']['onClick']   = '$(this).redirect("_self");';
        $col['DT_RowAttr']['data-href'] = site_url($this->module['route'] .'/detail?year='. $period_year .'&month='. $period_month .'&condition='. $condition .'&category='. $row['category'] .'&group='. $row['group']);
      }

      $data[] = $col;
    }

    $current_average_value = (array_sum($current_quantity) == 0) ? floatval(0) : array_sum($current_total_value)/array_sum($current_quantity);

    $result = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->model->countSummary($period_month, $period_year, $condition, $category),
        "recordsFiltered" => $this->model->countSummaryFiltered($period_month, $period_year, $condition, $category),
        "data" => $data,
        "total" => array(
          4 => print_number(array_sum($previous_quantity), 2),
          5 => print_number(array_sum($total_received_quantity), 2),
          6 => print_number(array_sum($total_issued_quantity), 2),
          7 => print_number(array_sum($total_adjustment_quantity), 2),
          8 => print_number(array_sum($current_quantity), 2),
          9 => print_number(array_sum($current_total_value), 2),
          10 => print_number($current_average_value, 2),
        )
      );

    echo json_encode($result);
  }

  public function detail()
  {
    $this->authorized($this->module, 'detail');

    if (isset($_GET['month']) && $_GET['month'] !== NULL){
      $period_month = $_GET['month'];
    } else {
      $period_month = config_item('period_month');
    }

    if (isset($_GET['year']) && $_GET['year'] !== NULL){
      $period_year = $_GET['year'];
    } else {
      $period_year = config_item('period_year');
    }

    if (isset($_GET['condition']) && $_GET['condition'] !== NULL){
      $condition  = $_GET['condition'];
    } else {
      $condition  = "SERVICEABLE";
    }

    if (isset($_GET['category']) && $_GET['category'] !== NULL){
      $category = $_GET['category'];
    } else {
      $category = NULL;
    }

    if (isset($_GET['group']) && $_GET['group'] !== NULL){
      $group = $_GET['group'];
    } else {
      $group = NULL;
    }

    if (isset($_GET['warehouse']) && $_GET['warehouse'] !== NULL){
      $warehouse = $_GET['warehouse'];
    } else {
      $warehouse = 'ALL BASES';
    }

    $this->data['selected_month']           = $period_month;
    $this->data['selected_year']            = $period_year;
    $this->data['selected_category']        = $category;
    $this->data['selected_group']           = $group;
    $this->data['selected_condition']       = $condition;
    $this->data['selected_warehouse']       = $warehouse;

    $this->data['page']['title']            = $this->module['label'] .' '. $warehouse .' '. $category .' '. $group .' '. $condition .' '. numberToMonthName($period_month) .' '. $period_year;
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getDetailSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/detail_data_source/'. $period_month .'/'. $period_year .'/'. $condition .'/'. $warehouse .'/'. $category .'/'. $group);
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

    $this->render_view($this->module['view'] .'/detail');
  }

  public function detail_data_source($period_month, $period_year, $condition = "SERVICEABLE", $warehouse = 'ALL BASES', $category = NULL, $group = NULL)
  {
    $this->authorized($this->module, 'detail');

    if ($category !== NULL){
      $category = urldecode($category);
    }

    if ($group !== NULL){
      $group = urldecode($group);
    }

    if ($warehouse !== NULL){
      $warehouse = (urldecode($warehouse) === 'ALL BASES') ? NULL : urldecode($warehouse);
    } else {
      $warehouse = NULL;
    }

    $entities = $this->model->getDetail($period_month, $period_year, $condition, $warehouse, $category, $group);

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
        "recordsTotal" => $this->model->countDetail($period_month, $period_year, $condition, $warehouse, $category, $group),
        "recordsFiltered" => $this->model->countDetailFiltered($period_month, $period_year, $condition, $warehouse, $category, $group),
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
  
  public function index_no_shipping()
  {
    $this->authorized($this->module, 'index');

    $period_year = config_item('period_year');
    $period_month = config_item('period_month')-1;


    if (isset($_POST['start_date']) && $_POST['start_date'] && isset($_POST['end_date']) && $_POST['end_date'] !== NULL){
      $start_date  = $_POST['start_date'];
      $end_date = $_POST['end_date'];
      $periode=print_date($start_date,'d F Y').' - '.print_date($end_date,'d F Y');
    } else {
      $start_date  = NULL;
      $end_date = NULL;
      $periode = 'ALL Periode';

    }

    if (isset($_POST['condition']) && $_POST['condition'] !== NULL){
      $condition  = $_POST['condition'];
    } else {
      $condition  = "SERVICEABLE";
    }

    if (isset($_POST['category']) && $_POST['category'] !== NULL){
      $category = $_POST['category'];
    } else {
      $category = 'all';
    }

    if (isset($_POST['warehouse']) && $_POST['warehouse'] !== NULL){
      $warehouse = $_POST['warehouse'];
    } else {
      $warehouse = 'ALL BASES';
    }
    
    $this->data['selected_category']        = $category;
    $this->data['selected_condition']       = $condition;
    $this->data['selected_warehouse']       = $warehouse;
    $this->data['document']       = 'index';
    $this->data['document_super_admin']       = 'index_super_admin';


    // $now=date('Y-m-d');
    // if (!empty($_POST['columns'][6]['search']['value'])){
    //   $search_issued_date = $_POST['columns'][6]['search']['value'];
    //   $range_issued_date  = explode(' ', $search_issued_date);
    //   $this->data['page']['title']            = $this->module['label'].' '.print_date($range_issued_date[0]).' - '.print_date($range_issued_date[1]);
    // }if (empty($_POST['columns'][6]['search']['value'])){
    //   $this->data['page']['title']            = $this->module['label'].' '.print_date($now);
    // }

    //$this->data['page']['title']            = $this->module['label'];
    $this->data['page']['title']            = 'ACCOUNTING '.$this->module['label'] .' '. $warehouse.' '. $category .' '. $condition.' / PERIODE : '.$periode;
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getIndexSelectedColumns_no_shipping());
    // $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source/'. $period_month .'/'. $period_year .'/'. $condition);
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source_no_shipping/'. $condition .'/'. $warehouse.'/'. $category.'/'.$start_date.'/'.$end_date);
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array( 10);
    if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'SUPER ADMIN' || config_item('auth_role') == 'VP FINANCE'){
      $this->data['grid']['summary_columns'][] = 12;
    }
    $this->data['grid']['order_columns']    = array (
      // 0 => array ( 0 => 3, 1 => 'asc' ),
      // 1 => array ( 0 => 4, 1 => 'asc' ),
      // 2 => array ( 0 => 2, 1 => 'asc' ),
      // 3 => array ( 0 => 1, 1 => 'asc' ),
      // 4 => array ( 0 => 5, 1 => 'asc' ),
      // 5 => array ( 0 => 6, 1 => 'asc' ),
      // 6 => array ( 0 => 7, 1 => 'asc' ),
      // 7 => array ( 0 => 8, 1 => 'asc' ),
      // 8 => array ( 0 => 9, 1 => 'asc' ),
      // 9 => array ( 0 => 10, 1 => 'asc' ),
      /*10 => array ( 0 => 11, 1 => 'asc' ),
      11 => array ( 0 => 12, 1 => 'asc' ),*/
      // 12 => array ( 0 => 13, 1 => 'asc' ),
    );

    $this->render_view($this->module['view'] .'/index');
  }

  public function index_data_source_no_shipping($condition = 'all', $warehouse='ALL BASES', $category = 'all', $start_date = NULL, $end_date = NULL)
  {
    $this->authorized($this->module, 'index');

    if ($warehouse !== NULL){
      $warehouse = (urldecode($warehouse) === 'ALL BASES') ? NULL : urldecode($warehouse);
    } 
    else {
      $warehouse = urldecode($warehouse);
    }

    if ($condition !== NULL){
      $condition = (urldecode($condition) === 'all') ? NULL : urldecode($condition);
    } 
    else {
      $condition = urldecode($condition);
    }

    // if ($category !== NULL){
    //   $category = urldecode($category);
    // }
    if ($category !== NULL){
      $category = (urldecode($category) === 'all') ? NULL : urldecode($category);
    } 
    else {
      $category = urldecode($category);
    }

    if ($start_date && $end_date !== NULL){
      $start_date  = urldecode($start_date);
      $end_date = urldecode($end_date);
    }

    $entities = $this->model->getIndex_no_shipping($condition, $warehouse, $start_date, $end_date, $category);

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
      $col[]  = print_string($row['item_id']);
      $col[] = print_string($row['part_number']);
      $col[] = print_string($row['description']);
      $col[] = print_string($row['serial_number']);
      $col[] = print_string($row['kode_stok']);
      $col[] = print_string($row['coa']);
      $col[] = print_string($row['category']);
      $col[] = print_string($row['group']);
      $col[] = print_string($row['condition']);
      $col[] = print_number($row['qty'], 2);
      $col[] = print_string($row['unit']);    
      // $col[] = print_number($row['unit_value'], 2);
      // $col[] = print_number($row['minimum_quantity'], 2);            
      // $col[] = print_string($row['stores']);
      // $col[] = print_string($row['warehouse']);
      if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'SUPER ADMIN' || config_item('auth_role') == 'VP FINANCE'){
        $col[] = print_number($row['total_value'], 2);
        
        // $col[] = print_number($row['total_value'], 2);
      }
      // $col[] = print_string($row['warehouse']);



      

      $col['DT_RowId']              = 'row_'. $row['id'];
      $col['DT_RowData']['pkey']    = $row['id'];

      // $previous_quantity[]          = $row['previous_quantity'];
      // $total_received_quantity[]    = $row['total_received_quantity'];
      // $total_issued_quantity[]      = $row['total_issued_quantity'];
      // $total_adjustment_quantity[]  = $row['total_adjustment_quantity'];
      $current_quantity[]           = $row['qty'];
      if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'SUPER ADMIN' || config_item('auth_role') == 'VP FINANCE'){
        // $current_total_value[]        = $row['total_value'];
        $current_total_value[]        = $row['total_value'];
      }
      // $current_average_value[]      = $row['current_average_value'];

      // if ($this->has_role($this->module, 'summary')){
      //   $col['DT_RowAttr']['onClick']   = '$(this).redirect("_self");';
      //   $col['DT_RowAttr']['data-href'] = site_url($this->module['route'] .'/summary?year='. $period_year .'&month='. $period_month .'&condition='. $condition .'&category='. $row['category']);
      // }

      $data[] = $col;
    }

    // $current_average_value = (array_sum($current_quantity) == 0) ? floatval(0) : array_sum($current_total_value)/array_sum($current_quantity);

    $result = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->model->countIndex_no_shipping($condition, $warehouse, $start_date, $end_date, $category),
        "recordsFiltered" => $this->model->countIndexFiltered_no_shipping($condition, $warehouse, $start_date, $end_date, $category),
        "data" => $data,
        "total" => array(
          
          10 => print_number(array_sum($current_quantity), 2),
          
        )
      );
    if(config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'SUPER ADMIN' || config_item('auth_role') == 'VP FINANCE'){
      $result['total'][12] = print_number(array_sum($current_total_value), 2);
    }

    echo json_encode($result);
  }

  public function index_super_admin()
  {
    $this->authorized($this->module, 'document_super_admin');

    $period_year = config_item('period_year');
    $period_month = config_item('period_month')-1;


    if (isset($_POST['start_date']) && $_POST['start_date'] && isset($_POST['end_date']) && $_POST['end_date'] !== NULL){
      $start_date  = $_POST['start_date'];
      $end_date = $_POST['end_date'];
      $periode=print_date($start_date,'d F Y').' - '.print_date($end_date,'d F Y');
    } else {
      $start_date  = NULL;
      $end_date = NULL;
      $periode = 'ALL Periode';

    }

    if (isset($_POST['condition']) && $_POST['condition'] !== NULL){
      $condition  = $_POST['condition'];
    } else {
      $condition  = "SERVICEABLE";
    }

    if (isset($_POST['category']) && $_POST['category'] !== NULL){
      $category = $_POST['category'];
    } else {
      $category = 'all';
    }

    if (isset($_POST['warehouse']) && $_POST['warehouse'] !== NULL){
      $warehouse = $_POST['warehouse'];
    } else {
      $warehouse = 'ALL BASES';
    }
    
    $this->data['selected_category']        = $category;
    $this->data['selected_condition']       = $condition;
    $this->data['selected_warehouse']       = $warehouse;
    $this->data['document']       = 'index';
    $this->data['document_super_admin']       = 'index';


    // $now=date('Y-m-d');
    // if (!empty($_POST['columns'][6]['search']['value'])){
    //   $search_issued_date = $_POST['columns'][6]['search']['value'];
    //   $range_issued_date  = explode(' ', $search_issued_date);
    //   $this->data['page']['title']            = $this->module['label'].' '.print_date($range_issued_date[0]).' - '.print_date($range_issued_date[1]);
    // }if (empty($_POST['columns'][6]['search']['value'])){
    //   $this->data['page']['title']            = $this->module['label'].' '.print_date($now);
    // }

    //$this->data['page']['title']            = $this->module['label'];
    $this->data['page']['title']            = 'SUPER ADMIN '.$this->module['label'] .' '. $warehouse.' '. $category .' '. $condition.' / PERIODE : '.$periode;
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getIndexSelectedColumns_super_admin());
    // $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source/'. $period_month .'/'. $period_year .'/'. $condition);
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source_super_admin/'. $condition .'/'. $warehouse.'/'. $category.'/'.$start_date.'/'.$end_date);
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array( 10);
    if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'SUPER ADMIN' || config_item('auth_role') == 'VP FINANCE'){
      $this->data['grid']['summary_columns'][] = 14;
    }
    $this->data['grid']['order_columns']    = array (
      // 0 => array ( 0 => 3, 1 => 'asc' ),
      // 1 => array ( 0 => 4, 1 => 'asc' ),
      // 2 => array ( 0 => 2, 1 => 'asc' ),
      // 3 => array ( 0 => 1, 1 => 'asc' ),
      // 4 => array ( 0 => 5, 1 => 'asc' ),
      // 5 => array ( 0 => 6, 1 => 'asc' ),
      // 6 => array ( 0 => 7, 1 => 'asc' ),
      // 7 => array ( 0 => 8, 1 => 'asc' ),
      // 8 => array ( 0 => 9, 1 => 'asc' ),
      // 9 => array ( 0 => 10, 1 => 'asc' ),
      /*10 => array ( 0 => 11, 1 => 'asc' ),
      11 => array ( 0 => 12, 1 => 'asc' ),*/
      // 12 => array ( 0 => 13, 1 => 'asc' ),
    );

    $this->render_view($this->module['view'] .'/index');
  }

  public function index_data_source_super_admin($condition = 'SERVICEABLE', $warehouse='ALL BASES', $category = 'all', $start_date = NULL, $end_date = NULL)
  {
    $this->authorized($this->module, 'document_super_admin');

    if ($warehouse !== NULL){
      $warehouse = (urldecode($warehouse) === 'ALL BASES') ? NULL : urldecode($warehouse);
    } 
    else {
      $warehouse = urldecode($warehouse);
    }

    // if ($category !== NULL){
    //   $category = urldecode($category);
    // }
    if ($category !== NULL){
      $category = (urldecode($category) === 'all') ? NULL : urldecode($category);
    } 
    else {
      $category = urldecode($category);
    }

    if ($start_date && $end_date !== NULL){
      $start_date  = urldecode($start_date);
      $end_date = urldecode($end_date);
    }

    $entities = $this->model->getIndex_super_admin($condition, $warehouse, $start_date, $end_date, $category);

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
      $col[]  = print_string($row['item_id']);
      $col[] = print_string($row['part_number']);
      $col[] = print_string($row['description']);
      $col[] = print_string($row['serial_number']);
      $col[] = print_string($row['kode_stok']);
      $col[] = print_string($row['coa']);
      $col[] = print_string($row['category']);
      $col[] = print_string($row['group']);
      $col[] = print_string($row['condition']);
      $col[] = print_number($row['qty'], 2);
      $col[] = print_string($row['unit']);    
      // $col[] = print_number($row['unit_value'], 2);
      // $col[] = print_number($row['minimum_quantity'], 2);            
      $col[] = print_string($row['stores']);
      // $col[] = print_string($row['warehouse']);
      if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'SUPER ADMIN' || config_item('auth_role') == 'VP FINANCE'){
        
        $col[] = print_number($row['unit_value'], 2);
        $col[] = print_number($row['total_value'], 2);
        
        // $col[] = print_number($row['total_value'], 2);
      }



      

      $col['DT_RowId']              = 'row_'. $row['id'];
      $col['DT_RowData']['pkey']    = $row['id'];

      // $previous_quantity[]          = $row['previous_quantity'];
      // $total_received_quantity[]    = $row['total_received_quantity'];
      // $total_issued_quantity[]      = $row['total_issued_quantity'];
      // $total_adjustment_quantity[]  = $row['total_adjustment_quantity'];
      $current_quantity[]           = $row['qty'];
      if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'SUPER ADMIN' || config_item('auth_role') == 'VP FINANCE'){
        // $current_total_value[]        = $row['total_value'];
        $current_total_value[]        = $row['total_value'];
      }
      // $current_average_value[]      = $row['current_average_value'];

      // if ($this->has_role($this->module, 'summary')){
      //   $col['DT_RowAttr']['onClick']   = '$(this).redirect("_self");';
      //   $col['DT_RowAttr']['data-href'] = site_url($this->module['route'] .'/summary?year='. $period_year .'&month='. $period_month .'&condition='. $condition .'&category='. $row['category']);
      // }

      $data[] = $col;
    }

    // $current_average_value = (array_sum($current_quantity) == 0) ? floatval(0) : array_sum($current_total_value)/array_sum($current_quantity);

    $result = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->model->countIndex_super_admin($condition, $warehouse, $start_date, $end_date, $category),
        "recordsFiltered" => $this->model->countIndexFiltered_super_admin($condition, $warehouse, $start_date, $end_date, $category),
        "data" => $data,
        "total" => array(
          
          10 => print_number(array_sum($current_quantity), 2),
          
        )
      );
    if(config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'SUPER ADMIN' || config_item('auth_role') == 'VP FINANCE'){
      $result['total'][14] = print_number(array_sum($current_total_value), 2);
    }

    echo json_encode($result);
  }
}
