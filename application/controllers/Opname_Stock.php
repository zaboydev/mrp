<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Opname_Stock extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['opname_stock'];
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function index()
  {
    $unpublish = unpublish();
    if($unpublish > 0){
      redirect($this->module['route'] .'/indexUnpublish');
    }else{
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
      $this->data['grid']['column']           = array_values($this->model->getIndexOpnameSelectedColumns());
      // $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source/'. $period_month .'/'. $period_year .'/'. $condition);
      $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source/'. $condition .'/'. $warehouse.'/'. $category.'/'.$start_date.'/'.$end_date);
      $this->data['grid']['fixed_columns']    = 2;
      $this->data['grid']['summary_columns']  = array( 9);
      if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE'){
        $this->data['grid']['summary_columns'][] = 15;
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
    
  }

  //public function index_data_source($start_date, $end_date, $condition = "SERVICEABLE", $warehouse = 'ALL BASES', $category = NULL)
  public function index_data_source($condition = 'SERVICEABLE', $warehouse='ALL BASES', $category = 'all', $start_date = NULL, $end_date = NULL)
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

    $entities = $this->model->getIndexOpname($condition, $warehouse, $start_date, $end_date, $category);

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
      $col[] = print_number($row['unit_value'], 2);
      $col[] = print_number($row['minimum_quantity'], 2);            
      $col[] = print_string($row['stores']);
      $col[] = print_string($row['warehouse']);
      if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE'){        
        $col[] = print_number($row['total_value'], 2);
      }



      

      $col['DT_RowId']              = 'row_'. $row['id'];
      $col['DT_RowData']['pkey']    = $row['id'];

      // $previous_quantity[]          = $row['previous_quantity'];
      // $total_received_quantity[]    = $row['total_received_quantity'];
      // $total_issued_quantity[]      = $row['total_issued_quantity'];
      // $total_adjustment_quantity[]  = $row['total_adjustment_quantity'];
      $current_quantity[]           = $row['qty'];
      if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE'){
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
        "recordsTotal" => $this->model->countIndexOpname($condition, $warehouse, $start_date, $end_date, $category),
        "recordsFiltered" => $this->model->countIndexOpnameFiltered($condition, $warehouse, $start_date, $end_date, $category),
        "data" => $data,
        "total" => array(
          
          9 => print_number(array_sum($current_quantity), 2),
          
        )
      );
    if(config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE'){
      $result['total'][15] = print_number(array_sum($current_total_value), 2);
    }

    echo json_encode($result);
  }

  public function index_cancel()
  {
    $this->authorized($this->module, 'index');

    // if (isset($_POST['month']) && $_POST['month'] !== NULL){
    //   $period_month = $_POST['month'];
    // } else {
    //   $period_month = config_item('period_month')-1;
    // }

    // if (isset($_POST['year']) && $_POST['year'] !== NULL){
    //   $period_year = $_POST['year'];
    // } else {
    //   $period_year = config_item('period_year');
    // }

    if (isset($_POST['start_date_cancel']) && $_POST['start_date_cancel'] && isset($_POST['end_date_cancel']) && $_POST['end_date_cancel'] !== NULL){
      $start_date  = $_POST['start_date_cancel'];
      $end_date = $_POST['end_date_cancel'];
    } else {
      // $last_month = config_item('period_month')-1;
      // $day = new DateTime('first day of last month');
      // $start_date  = $day->format('Y-m-d');
      // $day2 = new DateTime('last day of last month');
      // $end_date = $day2->format('Y-m-d');
      $start_date = start_date_last_opname();
      $end_date = end_date_last_opname();
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
    if (isset($_POST['quantity']) && $_POST['quantity'] !== NULL){
      $quantity = $_POST['quantity'];
    } else {
      $quantity = 'all';
    }

    $this->data['selected_month']           = $period_month;
    $this->data['selected_year']            = $period_year;
    $this->data['selected_category']        = $category;
    $this->data['selected_condition']       = $condition;
    $this->data['selected_warehouse']       = $warehouse;
    $this->data['selected_quantity']        = $quantity;

    $this->data['page']['title']            = 'Report Cancel'.$this->module['label'] .' '. $warehouse.' '. $category .' '. $condition.' / PERIODE : '.print_date($start_date, 'd F Y').' - '.print_date($end_date, 'd F Y');
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    //$this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source/'. $start_date .'/'. $end_date .'/'. $condition .'/'. $warehouse .'/'. $category);
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source_cancel/'. $start_date .'/'. $end_date .'/'. $condition .'/'. $warehouse .'/'. $quantity.'/'.$category);
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array( 15, 16, 17, 18, 19,20, 21 );
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
      // 17 => array ( 0 => 18, 1 => 'asc' ),
      // 18 => array ( 0 => 19, 1 => 'asc' ),
      // 19 => array ( 0 => 20, 1 => 'asc' ),
      // 20 => array ( 0 => 21, 1 => 'asc' ),
      // 21 => array ( 0 => 22, 1 => 'asc' ),
      // 22 => array ( 0 => 23, 1 => 'asc' ),
    );

    $this->render_view($this->module['view'] .'/index_cancel');
  }

  //public function index_data_source($start_date, $end_date, $condition = "SERVICEABLE", $warehouse = 'ALL BASES', $category = NULL)
  public function index_data_source_cancel($start_date, $end_date, $condition = "SERVICEABLE", $warehouse = 'ALL BASES', $quantity = 'all', $category = NULL)
  {
    $this->authorized($this->module, 'index');

    if ($category !== NULL){
      $category = urldecode($category);
    }

    if ($warehouse !== NULL){
      $warehouse = (urldecode($warehouse) === 'ALL BASES') ? NULL : urldecode($warehouse);
    }else {
      $warehouse = urldecode($warehouse);
    }

    if ($quantity !== NULL){
      $quantity = (urldecode($quantity) === 'all') ? NULL : urldecode($quantity);
    }else {
      $quantity = urldecode($quantity);
    }

    $entities = $this->model->getIndexCancel($start_date, $end_date, $condition, $warehouse, $category, $quantity);
    //$entities = $this->model->getIndex($start_date, $end_date, $condition, $warehouse, $category);


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
      //tambahan
      $col[] = print_string($row['purchase_order_number']);
      $col[] = print_string($row['reference_document']);
      $col[] = print_date($row['received_date'], 'd F Y');
      if(print_date($row['expired_date'], 'd F Y') == 'UNKNOWN'){
        $col[] = print_string('-');
      }else{
        $col[] = print_date($row['expired_date'], 'd F Y');
      }
      $col[] = print_string($row['coa']);
      $col[] = print_string($row['kode_stok']);
      
      //end tambahan
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
        "recordsTotal" => $this->model->countIndexCancel($start_date, $end_date, $condition, $warehouse, $category, $quantity),
        "recordsFiltered" => $this->model->countIndexFilteredCancel($start_date, $end_date, $condition, $warehouse, $category, $quantity),
        "data" => $data,
        "total" => array(
          15 => print_number(array_sum($previous_quantity), 2),
          16 => print_number(array_sum($total_received_quantity), 2),
          17 => print_number(array_sum($total_issued_quantity), 2),
          18 => print_number(array_sum($total_adjustment_quantity), 2),
          19 => print_number(array_sum($current_quantity), 2),
          20 => print_number(array_sum($current_total_value), 2),
          21 => print_number($current_average_value, 2),
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

  public function opname_stock()
  {
    
    // if ($this->input->is_ajax_request() == FALSE)
    //   redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'create_opname') == FALSE){
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to do this operation!';
    } else {
      // $start_date = $this->input->post('opname_start_date');
      // $end_date   = $this->input->post('opname_end_date');

      $start_date = last_update();
      $end_date   = $_SESSION['opname']['opname_end_date'];
      if ($this->model->start_stock_opname($start_date,$end_date)){
        unset($_SESSION['opname']);
        $data['success'] = TRUE;
        $data['message'] = 'Stock opname operation success. You will redirected now.';
      } else {
        $data['success'] = FALSE;
        $data['message'] = 'Error while opname stock. Please ask Technical Support.';
      }
    }

    echo json_encode($data);
    
    // redirect($this->module['route']);
  }

  public function cancel_opname_stock()
  {
    
    // if ($this->input->is_ajax_request() == FALSE)
    //   redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'create') == FALSE){
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to do this operation!';
    } else {
      $start_date = $_SESSION['cancel_opname']['opname_start_date'];
      $end_date   = $_SESSION['cancel_opname']['opname_end_date'];
      if ($this->model->cancel_opname_stock($start_date,$end_date)){
        // unset($_SESSION['opname']);
        $data['success'] = TRUE;
        $data['message'] = 'Stock opname operation success. You will redirected now.';
      } else {
        $data['success'] = FALSE;
        $data['message'] = 'Error while opname stock. Please ask Technical Support.';
      }
    }

    echo json_encode($data);
    
    // redirect($this->module['route']);
  }

  public function create_stock_opname()
  {
    $this->data['page']['title']            = "Start Closed Periode";
    $this->render_view($this->module['view'] .'/create_stock_opname_2');
  }

  public function cancel_stock_opname()
  {
    $this->data['page']['title']            = "Start Closed Periode";
    $_SESSION['cancel_opname']['opname_start_date'] = start_date_last_opname();
    $_SESSION['cancel_opname']['opname_end_date']   = end_date_last_opname();
    $this->render_view($this->module['view'] .'/cancel_stock_opname');
  }

  public function set_opname_end_date()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['opname']['opname_end_date'] = $_GET['data'];
  }

  public function set_qty_act($key)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['qty_act'][$key] = $_GET['data'];
  }

  public function set_opname_start_date()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['opname']['opname_start_date'] = $_GET['data'];
  }

  public function create_opname()
  {
    $this->data['page']['title']            = "Start Closed Periode";
    $this->render_view($this->module['view'] .'/create_opname_stock');
  }

  public function indexUnpublish()
  {
    $this->authorized($this->module, 'index');

    $period_year = config_item('period_year');
    $period_month = config_item('period_month')-1;

    $unpublish_date = unpublish_date();
    $start_date   = $unpublish_date->start_date;
    $end_date     = $unpublish_date->end_date;
    $status       = $unpublish_date->status;
    $periode      = print_date($end_date,'d F Y');
    
    $this->data['selected_category']        = $category;
    $this->data['selected_condition']       = $condition;
    $this->data['selected_warehouse']       = $warehouse;
    $this->data['page']['title']            = $this->module['label']. ' : '.$periode;
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getIndexUnpublishSelectedColumns());
    
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source_unpublish/'.$start_date.'/'.$end_date.'/'.$status);
    $this->data['grid']['fixed_columns']    = 3;
    $this->data['grid']['summary_columns']  = array(8);
    if (config_item('auth_role') == 'SUPERVISOR' || config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'PIC PROCUREMENT'){
        $this->data['grid']['summary_columns'][] = 10;
        $this->data['grid']['summary_columns'][] = 11;
    }
    if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'PIC PROCUREMENT'){
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
    );

    $this->render_view($this->module['view'] .'/index_unpublish');
  }

  //public function index_data_source($start_date, $end_date, $condition = "SERVICEABLE", $warehouse = 'ALL BASES', $category = NULL)
  public function index_data_source_unpublish($start_date,$end_date,$status)
  {
    $this->authorized($this->module, 'index_unpublish');

    $entities = $this->model->getIndexUnpublish($start_date,$end_date,$status);

    $data = array();
    $no   = $_POST['start'];

    $total_qty_sistem       = array();
    $total_qty_actual       = array();
    $total_required_adj     = array();
    $total_value_sistem     = array();

    foreach ($entities as $row){
      $no++;
      $col = array();
      $col[] = print_number($no);
      // $col[] = print_string($row['item_id']);
      $col[] = print_string($row['part_number']);
      $col[] = print_string($row['serial_number']);      
      $col[] = print_string($row['kode_stok']);
      $col[] = print_string($row['description']);
      $col[] = print_string($row['condition']);
      $col[] = print_string($row['base']);
      $col[] = print_string($row['stores']);
      $col[] = print_number($row['qty_actual'],2);
      
      // $col[] = print_number($row['balance'],2);
      $col[] = print_string($row['unit']);
      if (config_item('auth_role') == 'SUPERVISOR' || config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'PIC PROCUREMENT'){
        $col[] = print_number($row['qty_sistem'], 2);
        $col[] = print_number($row['required_adj'],2);
      }
      if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'PIC PROCUREMENT'){
        $col[] = print_number($row['total_value_sistem'], 2);
      }
      if (config_item('auth_role') == 'SUPERVISOR') {
        $col[] = print_string($row['update_by']);
      }      

      $col['DT_RowId'] = 'row_'. $row['id'];
      $col['DT_RowData']['pkey']  = $row['id'];
      $col['DT_RowAttr']['onClick']     = '$(this).popup();';
      $col['DT_RowAttr']['data-target'] = '#data-modal';
      $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] .'/detail_unpublish/'. $row['id']);
      
      
      $total_qty_actual[]           = $row['qty_actual'];
      
      if(config_item('auth_role') == 'SUPERVISOR' || config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'PIC PROCUREMENT'){
        $total_qty_sistem[]           = $row['qty_sistem'];
        $total_required_adj[]         = $row['required_adj'];
      }
      if(config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'PIC PROCUREMENT'){
        $total_value_sistem[]           = $row['total_value_sistem'];
      }

      $data[] = $col;

    }

    // $current_average_value = (array_sum($current_quantity) == 0) ? floatval(0) : array_sum($current_total_value)/array_sum($current_quantity);

    $result = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->model->countIndexUnpublish($start_date,$end_date,$status),
        "recordsFiltered" => $this->model->countIndexUnpublishFiltered($start_date,$end_date,$status),
        "data" => $data,
        "total" => array(
          8 => print_number(array_sum($total_qty_actual), 2),
        )
      );
    if(config_item('auth_role') == 'SUPERVISOR' || config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'PIC PROCUREMENT'){
      $result['total'][10] = print_number(array_sum($total_qty_sistem), 2);      
      $result['total'][11] = print_number(array_sum($total_required_adj), 2);
    }
    if(config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'PIC PROCUREMENT'){
      $result['total'][12] = print_number(array_sum($total_value_sistem), 2);
    }
    echo json_encode($result);
  }

  public function detail_unpublish($id)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'update_unpublish') === FALSE){
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to edit this data!";
    } else {
      $entity = $this->model->detail_unpublish($id);

      $this->data['entity'] = $entity;

      $return['type'] = 'success';
      $return['info'] = $this->load->view($this->module['view'] .'/detail_unpublish', $this->data, TRUE);
    }

    echo json_encode($return);
  }

  public function save_unpublish()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'save_unpublish') === FALSE){
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {
      if ($this->model->save_unpublish()){
        $return['type'] = 'success';
        $return['info'] = 'Data already Saved.';
      } else {
        $return['type'] = 'danger';
        $return['info'] = 'There are error while updating data. Please try again later.';
      }
    }
    echo json_encode($return);
  }

  public function publish()
  {
    // if ($this->input->is_ajax_request() === FALSE)
    //   redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'publish') === FALSE){
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {
      $unpublish = unpublish_date();
      $start_date = $unpublish->start_date;
      $end_date   = $unpublish->end_date;
      // $return['type'] = TRUE;
      // $return['info'] = 'Data already Saved.'.$start_date.'-'.$end_date;
      if ($this->model->publish($start_date,$end_date)){
        $return['type'] = 'success';
        $return['info'] = 'Data already Saved.'.$start_date.'-'.$end_date;
      } else {
        $return['type'] = 'danger';
        $return['info'] = 'There are error while updating data. Please try again later.';
      }

    }
    // echo json_encode($return);
    redirect($this->module['route'] .'/index');
  }

  public function set_qty_actual()
  {
    // if ($this->input->is_ajax_request() === FALSE)
    //   redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'publish') === FALSE){
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {
      // $unpublish_date = unpublish_date();
      // $start_date = $unpublish_date['start_date'];
      // $end_date   = $unpublish_date['end_date'];
      // $return['type'] = TRUE;
      // $return['info'] = 'Data already Saved.'.$start_date.'-'.$end_date;
      if ($this->model->set_qty_fisik()){
        $return['type'] = 'success';
        $return['info'] = 'Data already Saved.'.$start_date.'-'.$end_date;
      } else {
        $return['type'] = 'danger';
        $return['info'] = 'There are error while updating data. Please try again later.';
      }

    }
    // echo json_encode($return);
    redirect($this->module['route'] .'/index');
  }

  public function coba($id)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'update_unpublish') === FALSE){
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to edit this data!";
    } else {
      $entity = $this->model->coba('2018-01-01','2018-12-31');

      $this->data['entity'] = $entity;

      $return['type'] = 'success';
      $return['info'] = $this->load->view($this->module['view'] .'/try', $this->data, TRUE);
    }

    echo json_encode($return);
  }
}
