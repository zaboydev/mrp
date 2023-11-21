<?php defined('BASEPATH') OR exit('No direct script access allowed');

class General_Stock_Report extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['general_stock_report'];
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function index()
  {
    $this->authorized($this->module, 'index');

    $period_year = config_item('period_year');
    $period_month = config_item('period_month')-1;


    if (isset($_POST['start_date']) || $_POST['start_date'] || isset($_POST['end_date']) || $_POST['end_date'] !== NULL){
      $start_date   = $_POST['start_date'];
      $end_date     = $_POST['end_date'];
      $tgl          = date('Ymd',strtotime($end_date));
      $tgl_awal     = date('Ymd',strtotime($start_date));
      $periode      = print_date($start_date,'d F Y').' - '.print_date($end_date,'d F Y');
    } else {
      $start_date   = date('Y-m-d');
      $end_date     = date('Y-m-d');
      $tgl          = date('Ymd',strtotime($end_date));
      $tgl_awal     = date('Ymd',strtotime($start_date));
      $periode      = print_date($start_date,'d F Y').' - '.print_date($end_date,'d F Y');

    }

    if (isset($_POST['condition']) && $_POST['condition'] !== NULL){
      $condition  = $_POST['condition'];
    } else {
      $condition  = "all condition";
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
    $this->data['start_date']               = $start_date;
    $this->data['end_date']                 = $end_date;
	  $this->data['document']                 = 'index_no_shipping';

    $this->data['page']['title']            = $this->module['label'] .' '. $warehouse.' '. $category .' '. $condition.' / PERIODE : '.$periode;
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getIndexSelectedColumns($tgl,$tgl_awal));
    
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source/'. $condition .'/'. $warehouse.'/'. $category.'/'.$start_date.'/'.$end_date);
    $this->data['grid']['fixed_columns']    = 4;
    $this->data['grid']['summary_columns']  = array(8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25);
    // if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE'){
    //   $this->data['grid']['summary_columns'][] = 15;
    // }
    $this->data['grid']['order_columns']    = array ();

    $this->render_view($this->module['view'] .'/index');
  }

  public function index_data_source($condition = 'all condition', $warehouse='ALL BASES', $category = 'all', $start_date = NULL, $end_date = NULL)
  {
    $this->authorized($this->module, 'index');

    if ($warehouse !== NULL){
      $warehouse = (urldecode($warehouse) === 'ALL BASES') ? NULL : urldecode($warehouse);
    } 
    else {
      $warehouse = urldecode($warehouse);
    }

    if ($condition !== NULL){
      $condition = (urldecode($condition) === 'all condition') ? NULL : urldecode($condition);
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

    $iniatial_qty        = array();
    $grn_qty             = array();
    $ms_qty              = array();
    $mix_qty             = array();
    $adj_qty             = array();
    $ship_out_qty        = array();
    $ship_in_qty         = array();
    $balance_qty         = array();
    $iniatial_total_value        = array();
    $grn_total_value             = array();
    $ms_total_value              = array();
    $mix_total_value             = array();
    $adj_total_value             = array();
    $ship_out_total_value        = array();
    $ship_in_total_value         = array();
    $balance_total_value         = array();
    $retur_total_value         = array();
    $retur_qty         = array();

    foreach ($entities as $row){
      $no++;
      $col = array();
      $col[] = print_number($no);
      $col[]  = print_string($row['item_id']);
      $col[] = print_string($row['part_number']);
      $col[] = print_string($row['serial_number']);
      $col[] = print_string($row['description']);
      $col[] = print_string($row['condition']);
      $col[] = print_string($row['kode_stok']);	  
      $col[] = print_string($row['coa']);
      $col[] = print_number($row['qty'], 2);
      $col[] = print_number($row['total_value'], 2);
      $col[] = print_number($row['grn_qty'], 2);
      $col[] = print_number($row['grn_total_value'], 2);
      $col[] = print_number($row['ms_qty'], 2);
      $col[] = print_number($row['ms_total_value'], 2);
      $col[] = print_number($row['mix_qty'], 2);
      $col[] = print_number($row['mix_total_value'], 2);
      $col[] = print_number($row['adj_qty'], 2);
      $col[] = print_number($row['adj_total_value'], 2);
      $col[] = print_number($row['ship_out_qty'], 2);
      $col[] = print_number($row['ship_out_total_value'], 2);
      $col[] = print_number($row['ship_in_qty'], 2);
      $col[] = print_number($row['ship_in_total_value'], 2);
      $col[] = print_number($row['retur_qty'], 2);
      $col[] = print_number($row['retur_total_value'], 2);
      // $col[] = print_number($row['qty']+$row['ship_out_qty']+$row['ship_in_qty']+$row['adj_qty']+$row['mix_qty']+$row['ms_qty']+$row['grn_qty'], 2);
      // $col[] = print_number($row['total_value']+$row['ship_out_total_value']+$row['ship_in_total_value']+$row['adj_total_value']+$row['mix_total_value']+$row['ms_total_value']+$row['grn_total_value'], 2);
      $col[] = print_number($row['balance_qty'], 2);
      $col[] = print_number($row['balance_total_value'], 2);

      // $col[] = print_string($row['unit']);           
      // $col[] = print_string($row['stores']);
      // $col[] = print_string($row['warehouse']);
      if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'SUPER ADMIN' || config_item('auth_role') == 'VP FINANCE'){        
        $col[] = print_string($row['kode_pemakaian']);
      }
      // $iniatial_qty[]        = $row['qty']+$row['ship_out_qty']+$row['ship_in_qty']+$row['adj_qty']+$row['mix_qty']+$row['ms_qty']+$row['grn_qty'];
      $iniatial_qty[]        = $row['balance_qty'];
      $grn_qty[]             = $row['grn_qty'];
      $ms_qty[]              = $row['ms_qty'];
      $mix_qty[]             = $row['mix_qty'];
      $adj_qty[]             = $row['adj_qty'];
      $ship_out_qty[]        = $row['ship_out_qty'];
      $ship_in_qty[]         = $row['ship_in_qty'];
      $balance_qty[]         = $row['qty'];

      // $iniatial_total_value[]        = $row['total_value']+$row['ship_out_total_value']+$row['ship_in_total_value']+$row['adj_total_value']+$row['mix_total_value']+$row['ms_total_value']+$row['grn_total_value'];
      $iniatial_total_value[]        = $row['balance_total_value'];
      $grn_total_value[]             = $row['grn_total_value'];
      $ms_total_value[]              = $row['ms_total_value'];
      $mix_total_value[]             = $row['mix_total_value'];
      $adj_total_value[]             = $row['adj_total_value'];
      $ship_out_total_value[]        = $row['ship_out_total_value'];
      $ship_in_total_value[]         = $row['ship_in_total_value'];
      $balance_total_value[]         = $row['total_value'];
      $retur_total_value[]         = $row['retur_total_value']; 
      $retur_qty[]           = $row['retur_qty'];         

      $col['DT_RowId']              = 'row_'. $row['id'];
      $col['DT_RowData']['pkey']    = $row['id'];
      
      $current_quantity[]           = $row['qty'];
      // if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE'){
      //   $current_total_value[]        = $row['qty']*$row['unit_value'];
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
          //6   => print_number(array_sum($balance_qty), 2),
          8   => print_number(array_sum($balance_qty), 2),
          9   => print_number(array_sum($balance_total_value), 2),
          10   => print_number(array_sum($grn_qty), 2),
          11   => print_number(array_sum($grn_total_value), 2),
          12   => print_number(array_sum($ms_qty), 2),
          13   => print_number(array_sum($ms_total_value), 2),
          14   => print_number(array_sum($mix_qty), 2),
          15  => print_number(array_sum($mix_total_value), 2),
          16  => print_number(array_sum($adj_qty), 2),
          17  => print_number(array_sum($adj_total_value), 2),
          18  => print_number(array_sum($ship_out_qty), 2),
          19  => print_number(array_sum($ship_out_total_value), 2),
          20  => print_number(array_sum($ship_in_qty), 2),
          21  => print_number(array_sum($ship_in_total_value), 2),
          22  => print_number(array_sum($retur_qty), 2),
          23  => print_number(array_sum($retur_total_value), 2),
          24  => print_number(array_sum($iniatial_qty), 2),
          25  => print_number(array_sum($iniatial_total_value), 2),
        )
      );
    // if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE'){
    //     $result['total'][15] = print_number(array_sum($current_total_value), 2);
    //   }

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


    if (isset($_POST['start_date']) || $_POST['start_date'] || isset($_POST['end_date']) || $_POST['end_date'] !== NULL){
      $start_date   = $_POST['start_date'];
      $end_date     = $_POST['end_date'];
      $tgl          = date('Ymd',strtotime($end_date));
      $tgl_awal     = date('Ymd',strtotime($start_date));
      $periode      = print_date($start_date,'d F Y').' - '.print_date($end_date,'d F Y');
    } else {
      $start_date   = date('Y-m-d');
      $end_date     = date('Y-m-d');
      $tgl          = date('Ymd',strtotime($end_date));
      $tgl_awal     = date('Ymd',strtotime($start_date));
      $periode      = print_date($start_date,'d F Y').' - '.print_date($end_date,'d F Y');

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
    $this->data['start_date']               = $start_date;
    $this->data['end_date']                 = $end_date;
    $this->data['document']                 = 'index';

    $this->data['page']['title']            = 'ACCOUNTING '. $this->module['label'] .' '. $warehouse.' '. $category .' '. $condition.' / PERIODE : '.$periode;
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getIndexSelectedColumns_no_shipping($tgl,$tgl_awal));
    
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source_no_shipping/'. $condition .'/'. $warehouse.'/'. $category.'/'.$start_date.'/'.$end_date);
    $this->data['grid']['fixed_columns']    = 4;
    $this->data['grid']['summary_columns']  = array(7,8,9,10,11,12,13,14,15,16,17,18,19,20);
    // if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE'){
    //   $this->data['grid']['summary_columns'][] = 15;
    // }
    $this->data['grid']['order_columns']    = array ();

    $this->render_view($this->module['view'] .'/index');
  }

  public function index_data_source_no_shipping($condition = 'SERVICEABLE', $warehouse='ALL BASES', $category = 'all', $start_date = NULL, $end_date = NULL)
  {
    $this->authorized($this->module, 'index');

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

    $entities = $this->model->getIndex_no_shipping($condition, $warehouse, $start_date, $end_date, $category);

    $data = array();
    $no   = $_POST['start'];

    $iniatial_qty        = array();
    $grn_qty             = array();
    $ms_qty              = array();
    $mix_qty             = array();
    $adj_qty             = array();
    $ship_out_qty        = array();
    $ship_in_qty         = array();
    $balance_qty         = array();
    $iniatial_total_value        = array();
    $grn_total_value             = array();
    $ms_total_value              = array();
    $mix_total_value             = array();
    $adj_total_value             = array();
    $ship_out_total_value        = array();
    $ship_in_total_value         = array();
    $balance_total_value         = array();
    $retur_total_value         = array();
    $retur_qty         = array();

    foreach ($entities as $row){
      $no++;
      $col = array();
      $col[] = print_number($no);
      $col[]  = print_string($row['item_id']);
      $col[] = print_string($row['part_number']);
      $col[] = print_string($row['serial_number']);
      $col[] = print_string($row['description']);
      $col[] = print_string($row['kode_stok']);
	    $col[] = print_string($row['coa']);
      $col[] = print_number($row['qty'], 2);
      $col[] = print_number($row['total_value'], 2);
      $col[] = print_number($row['grn_qty'], 2);
      $col[] = print_number($row['grn_total_value'], 2);
      $col[] = print_number($row['ms_qty'], 2);
      $col[] = print_number($row['ms_total_value'], 2);
      $col[] = print_number($row['mix_qty'], 2);
      $col[] = print_number($row['mix_total_value'], 2);
      $col[] = print_number($row['adj_qty'], 2);
      $col[] = print_number($row['adj_total_value'], 2);
      $col[] = print_number($row['retur_qty'], 2);
      $col[] = print_number($row['retur_total_value'], 2);
      // $col[] = print_number($row['ship_out_qty'], 2);
      // $col[] = print_number($row['ship_out_total_value'], 2);
      // $col[] = print_number($row['ship_in_qty'], 2);
      // $col[] = print_number($row['ship_in_total_value'], 2);
      
      // $col[] = print_number($row['qty']+$row['ship_out_qty']+$row['ship_in_qty']+$row['adj_qty']+$row['mix_qty']+$row['ms_qty']+$row['grn_qty'], 2);
      // $col[] = print_number($row['total_value']+$row['ship_out_total_value']+$row['ship_in_total_value']+$row['adj_total_value']+$row['mix_total_value']+$row['ms_total_value']+$row['grn_total_value'], 2);
      $col[] = print_number($row['balance_qty'], 2);
      $col[] = print_number($row['balance_total_value'], 2);
	    $col[] = print_string($row['kode_pemakaian']);

      // $col[] = print_string($row['unit']);           
      // $col[] = print_string($row['stores']);
      // $col[] = print_string($row['warehouse']);
      // if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE'){        
      //   $col[] = print_number($row['qty']*$row['unit_value'], 2);
      // }
      // $iniatial_qty[]        = $row['qty']+$row['ship_out_qty']+$row['ship_in_qty']+$row['adj_qty']+$row['mix_qty']+$row['ms_qty']+$row['grn_qty'];
      $iniatial_qty[]        = $row['balance_qty'];
      $grn_qty[]             = $row['grn_qty'];
      $ms_qty[]              = $row['ms_qty'];
      $mix_qty[]             = $row['mix_qty'];
      $adj_qty[]             = $row['adj_qty'];
      $ship_out_qty[]        = $row['ship_out_qty'];
      $ship_in_qty[]         = $row['ship_in_qty'];
      $balance_qty[]         = $row['qty'];

      // $iniatial_total_value[]        = $row['total_value']+$row['ship_out_total_value']+$row['ship_in_total_value']+$row['adj_total_value']+$row['mix_total_value']+$row['ms_total_value']+$row['grn_total_value'];
      $iniatial_total_value[]        = $row['balance_total_value'];
      $grn_total_value[]             = $row['grn_total_value'];
      $ms_total_value[]              = $row['ms_total_value'];
      $mix_total_value[]             = $row['mix_total_value'];
      $adj_total_value[]             = $row['adj_total_value'];
      $ship_out_total_value[]        = $row['ship_out_total_value'];
      $ship_in_total_value[]         = $row['ship_in_total_value'];
      $balance_total_value[]         = $row['total_value'];     
      $retur_total_value[]         = $row['retur_total_value']; 
      $retur_qty[]           = $row['retur_qty'];         

      $col['DT_RowId']              = 'row_'. $row['id'];
      $col['DT_RowData']['pkey']    = $row['id'];
      
      $current_quantity[]           = $row['qty'];
      // if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE'){
      //   $current_total_value[]        = $row['qty']*$row['unit_value'];
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
          7   => print_number(array_sum($balance_qty), 2),
          8   => print_number(array_sum($balance_total_value), 2),
          9   => print_number(array_sum($grn_qty), 2),
          10   => print_number(array_sum($grn_total_value), 2),
          11   => print_number(array_sum($ms_qty), 2),
          12   => print_number(array_sum($ms_total_value), 2),
          13   => print_number(array_sum($mix_qty), 2),
          14   => print_number(array_sum($mix_total_value), 2),
          15  => print_number(array_sum($adj_qty), 2),
          16  => print_number(array_sum($adj_total_value), 2),
          // 16  => print_number(array_sum($ship_out_qty), 2),
          // 17  => print_number(array_sum($ship_out_total_value), 2),
          // 18  => print_number(array_sum($ship_in_qty), 2),
          // 19  => print_number(array_sum($ship_in_total_value), 2),
          17  => print_number(array_sum($retur_qty), 2),
          18  => print_number(array_sum($retur_total_value), 2),
          19  => print_number(array_sum($iniatial_qty), 2),
          20  => print_number(array_sum($iniatial_total_value), 2),
        )
      );
    // if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE'){
    //     $result['total'][15] = print_number(array_sum($current_total_value), 2);
    //   }

    echo json_encode($result);
  }
}
