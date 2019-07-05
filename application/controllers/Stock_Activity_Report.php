<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_Activity_Report extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['stock_activity_report'];
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function index()
  {
    $this->authorized($this->module, 'index');

    $this->data['page']['title']            = $this->module['label'];
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source');
    $this->data['grid']['fixed_columns']    = 2;
    //$this->data['grid']['summary_columns']  = array(4,5);
    $this->data['grid']['order_columns']    = array (
      0 => array ( 0 => 1, 1 => 'asc' ),
      1 => array ( 0 => 2, 1 => 'asc' ),
      2 => array ( 0 => 3, 1 => 'asc' ),
      3 => array ( 0 => 4, 1 => 'asc' ),
      4 => array ( 0 => 5, 1 => 'asc' ),
      5 => array ( 0 => 6, 1 => 'asc' ),
      //6 => array ( 0 => 7, 1 => 'asc' ),
      // 6 => array ( 0 => 7, 1 => 'asc' ),
      // 7 => array ( 0 => 8, 1 => 'asc' ),
      // 8 => array ( 0 => 9, 1 => 'asc' ),
      // 9 => array ( 0 => 10, 1 => 'asc' ),
      // 10 => array ( 0 => 11, 1 => 'asc' ),
      // 11 => array ( 0 => 12, 1 => 'asc' ),
      // 12 => array ( 0 => 13, 1 => 'asc' ),
      // 13 => array ( 0 => 14, 1 => 'asc' ),
    );

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
      $col[] = print_string($row['part_number']);
      $col[] = print_string($row['serial_number']);
      $col[] = print_string($row['description']);
      $col[] = print_string($row['unit']);
      // $col[] = print_string($row['group']);   
      $col[] = print_string($row['category']); 
      $col[] = print_string($row['warehouse']);
      $col[] = print_string($row['stores']);
      $col[] = print_string($row['kode_stok']);
      $col[] = print_string($row['coa']);
      //$col[] = print_string($row['pn']);
      $col['DT_RowId'] = 'row_'. $row['id'];
      $col['DT_RowData']['pkey'] = $row['id'];

      if ($this->has_role($this->module, 'detail')) {
        $col['DT_RowAttr']['onClick'] = '$(this).redirect("_blank");';
        if($row['serial_number'] == ''){
            $col['DT_RowAttr']['data-href'] = site_url($this->module['route'] .'/detail?part_number='. $row['part_number'] .'&unit='. $row['unit'] .'&category='. $row['category'] .'&base='. $row['warehouse'].'&stores='. $row['stores'].'&desc='. $row['description'].'&coa='.$row['coa'].'&kode_stok='.$row['kode_stok'].'&serial_number=-');
        }else{
          $col['DT_RowAttr']['data-href'] = site_url($this->module['route'] .'/detail?part_number='. $row['part_number'] .'&unit='. $row['unit'] .'&category='. $row['category'] .'&base='. $row['warehouse'].'&stores='. $row['stores'].'&desc='. $row['description'].'&coa='.$row['coa'].'&kode_stok='.$row['kode_stok'].'&serial_number='.$row['serial_number']);
        }
        
      }

      $data[] = $col;
    }

    $result = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->model->countIndex(),
        "recordsFiltered" => $this->model->countIndexFiltered(),
        "data" => $data,
        // "total" => array(
        //   4 => 0,
        //   5 => 0,
        // )
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

    if (isset($_GET['serial_number']) && $_GET['serial_number'] !== NULL){
      $serial_number = $_GET['serial_number'];
    } else {
      $serial_number = NULL;
    }

    if (isset($_GET['desc']) && $_GET['desc'] !== NULL){
      $description = $_GET['desc'];
    } else {
      $description = NULL;
    }

    if (isset($_GET['unit']) && $_GET['unit'] !== NULL){
      $unit  = $_GET['unit'];
    } else {
      $unit  = NULL;
    }

    if (isset($_GET['category']) && $_GET['category'] !== NULL){
      $category = $_GET['category'];
    } else {
      $category = NULL;
    }

    if (isset($_GET['base']) && $_GET['base'] !== NULL){
      $base = $_GET['base'];
    } else {
      $base = NULL;
    }

    if (isset($_GET['stores']) && $_GET['stores'] !== NULL){
      $stores = $_GET['stores'];
    } else {
      $stores = NULL;
    }

    if (isset($_GET['coa']) && $_GET['coa'] !== NULL){
      $coa = $_GET['coa'];
    } else {
      $coa = NULL;
    }

    if (isset($_GET['kode_stok']) && $_GET['kode_stok'] !== NULL){
      $kode_stok = $_GET['kode_stok'];
    } else {
      $kode_stok = NULL;
    }

    // $this->data['selected_month']           = $period_month;
    // $this->data['selected_year']            = $period_year;
    $this->data['selected_category']        = $category;
    $this->data['selected_group']           = $group;
    //$this->data['selected_condition']       = $condition;
    $this->data['selected_warehouse']       = $base;

    $this->data['page']['title']            = $this->module['label'] .'  | Part Number : '. $part_number .' |  Serial Number : '.$serial_number.' | Unit : '. $unit .' |  Category : '. $category .'  | Base : '. $base .' |  Stores : '. $stores.' | Desc : '. $description.'  |  COA : '.$coa.'  |  Kode Stok : '.$kode_stok;
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getDetailSelectedColumns());
    //$this->data['grid']['data_source']      = site_url($this->module['route'] .'/detail_data_source/'. $part_number .'/'. $unit .'/'. $category .'/'. $base .'/'. $stores.'/'. $description);
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/detail_data_source?part_number='. $part_number .'&unit='. $unit.'&category='. $category.'&base='. $base.'&stores='. $stores.'&desc='. $description.'&serial_number='.$serial_number);
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array( 6,7 );
    $this->data['grid']['order_columns']    = array (
      
    );

    $this->render_view($this->module['view'] .'/detail');
  }

  
  public function detail_data_source()
  {
    $this->authorized($this->module, 'detail');

    

    if (isset($_GET['part_number']) && $_GET['part_number'] !== NULL){
      $part_number = $_GET['part_number'];
    } else {
      $part_number = NULL;
    }

    if (isset($_GET['serial_number']) && $_GET['serial_number'] !== NULL){
      $serial_number = $_GET['serial_number'];
    } else {
      $serial_number = NULL;
    }

    if (isset($_GET['desc']) && $_GET['desc'] !== NULL){
      $description = $_GET['desc'];
    } else {
      $description = NULL;
    }

    if (isset($_GET['unit']) && $_GET['unit'] !== NULL){
      $unit  = $_GET['unit'];
    } else {
      $unit  = NULL;
    }

    if (isset($_GET['category']) && $_GET['category'] !== NULL){
      $category = $_GET['category'];
    } else {
      $category = NULL;
    }

    if (isset($_GET['base']) && $_GET['base'] !== NULL){
      $base = $_GET['base'];
    } else {
      $base = NULL;
    }

    if (isset($_GET['stores']) && $_GET['stores'] !== NULL){
      $stores = $_GET['stores'];
    } else {
      $stores = NULL;
    }

    $entities = $this->model->getDetailIndex($part_number, $unit, $category, $base, $stores,$description,$serial_number);

    $data = array();
    $no   = $_POST['start'];

    $previous_quantity          = array();
    

    foreach ($entities as $row){
      $no++;
      $col = array();
      $col[] = print_number($no);
      $col[] = print_date($row['date_of_entry'],'d F Y');
      $col[] = print_string($row['serial_number']);
      $col[] = print_string($row['document_number']);
      $col[] = print_string($row['stores']);
      $col[] = print_number($row['prev_quantity'], 2);
      $col[] = print_number($row['in_qty'], 2);
      $col[] = print_number($row['out_qty'], 2);
      $col[] = print_number($row['unit_value'], 2);
      $col[] = print_number($row['balance_quantity'], 2);
      $col[] = print_string($row['issued_to']);

      $col['DT_RowId']              = 'row_'. $row['id'];
      $col['DT_RowData']['pkey']    = $row['id'];

      $previous_quantity[]          = $row['prev_quantity'];
      $in_qty[]                     = $row['in_qty'];
      $out_qty[]                    = $row['out_qty'];
      $balance_quantity[]           = $row['balance_quantity'];
      

    
      $data[] = $col;
    }

  
    $result = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->model->countDetail($part_number, $unit, $category,$base,$stores,$description,$serial_number),
        "recordsFiltered" => $this->model->countDetailIndexFiltered($part_number, $unit, $category,$base,$stores,$description,$serial_number),
        "data" => $data,
        "total" => array(
          // 5 => print_number(array_sum($previous_quantity), 2),
          6 => print_number(array_sum($in_qty), 2),
          7 => print_number(array_sum($out_qty), 2),
          // 8 => print_number(array_sum($balance_quantity), 2),
          
        )
      );

    echo json_encode($result);
  }


}
