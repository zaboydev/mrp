<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_Card extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['stock_card'];
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
      // 0 => array ( 0 => 1, 1 => 'asc' ),
      // 1 => array ( 0 => 2, 1 => 'asc' ),
      // 2 => array ( 0 => 3, 1 => 'asc' ),
      // 3 => array ( 0 => 4, 1 => 'asc' ),
      // 4 => array ( 0 => 5, 1 => 'asc' ),
      // 5 => array ( 0 => 6, 1 => 'asc' ),
      // 6 => array ( 0 => 7, 1 => 'asc' ),
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
      // $col[] = print_string($row['warehouse']);
      // $col[] = print_string($row['stores']);
      $col[] = print_string($row['kode_stok']);
      $col[] = print_string($row['coa']);
      //$col[] = print_string($row['pn']);
      $col['DT_RowId'] = 'row_'. $row['id'];
      $col['DT_RowData']['pkey'] = $row['id'];

      if ($this->has_role($this->module, 'detail')) {
        $col['DT_RowAttr']['onClick'] = '$(this).redirect("_blank");';
        $col['DT_RowAttr']['data-href'] = site_url($this->module['route'] .'/detail?stock_id='.$row['id']);
        // if($row['serial_number'] == ''){
        //     $col['DT_RowAttr']['data-href'] = site_url($this->module['route'] .'/detail?part_number='. $row['part_number'] .'&unit='. $row['unit'] .'&category='. $row['category'] .'&desc='. $row['description'].'&coa='.$row['coa'].'&kode_stok='.$row['kode_stok'].'&serial_number=-');
        // }else{
        //   $col['DT_RowAttr']['data-href'] = site_url($this->module['route'] .'/detail?part_number='. $row['part_number'] .'&unit='. $row['unit'] .'&category='. $row['category'] .'&desc='. $row['description'].'&coa='.$row['coa'].'&kode_stok='.$row['kode_stok'].'&serial_number='.$row['serial_number']);
        // }
        
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

    if (isset($_GET['stock_id']) && $_GET['stock_id'] !== NULL){
      $stock_id = $_GET['stock_id'];
    } else {
      $stock_id = NULL;
    }

    $item = $this->model->getItem($stock_id);

    $this->data['stock_id']               = $stock_id;
    $this->data['selected_stores']        = $stores;

    $this->data['page']['title']            = $this->module['label'] .'  | Part Number : '. $item['part_number'] .' |  Serial Number : '.$item['serial_number'].' | Unit : '. $item['unit'] .' |  Category : '. $item['category'] .'| Desc : '. $item['description'].' |  Kode Stok : '.$item['kode_stok'].$stores;
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getDetailSelectedColumns($stock_id));
    //$this->data['grid']['data_source']      = site_url($this->module['route'] .'/detail_data_source/'. $part_number .'/'. $unit .'/'. $category .'/'. $base .'/'. $stores.'/'. $description);
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/detail_data_source?stock_id='. $stock_id);
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array( 5,6,8 );
    $this->data['grid']['order_columns']    = array (
      
    );

    $this->render_view($this->module['view'] .'/detail');
  }

  
  public function detail_data_source()
  {
    $this->authorized($this->module, 'detail');

    if (isset($_GET['stock_id']) && $_GET['stock_id'] !== NULL){
      $stock_id = $_GET['stock_id'];
    } else {
      $stock_id = NULL;
    }

    $entities = $this->model->getDetailIndex($stock_id);

    $data = array();
    $no   = $_POST['start'];

    $previous_quantity          = array();
    $balance          = 0;
    
    foreach ($entities as $row){
      $balance = $balance+$row['total_value'];
      $no++;
      $col = array();
      $col[] = print_number($no);
      $col[] = print_date($row['date_of_entry'],'d F Y');
      $col[] = print_string($row['serial_number']);
      $col[] = print_string($row['document_number']);
      $col[] = print_string($row['stores']);
      // $col[] = print_number($row['prev_quantity'], 2);
      $col[] = print_number($row['in_qty'], 2);
      $col[] = print_number($row['out_qty'], 2);
      $col[] = print_number($row['unit_value'], 2);
      // $col[] = print_number(($row['in_qty']*$row['unit_value'])+($row['out_qty']*$row['unit_value']), 2);
      $col[] = print_number($row['total_value'], 2);
      $col[] = print_number($balance,2);

      $col['DT_RowId']              = 'row_'. $row['id'];
      $col['DT_RowData']['pkey']    = $row['id'];

      $previous_quantity[]          = $row['prev_quantity'];
      $in_qty[]                     = $row['in_qty'];
      $out_qty[]                    = $row['out_qty'];
      // $balance_quantity[]           = ($row['in_qty']*$row['unit_value'])+($row['out_qty']*$row['unit_value']);  
      $balance_quantity[]           = $row['total_value'];   

    
      $data[] = $col;
    }

  
    $result = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->model->countDetail($stock_id),
        "recordsFiltered" => $this->model->countDetailIndexFiltered($stock_id),
        "data" => $data,
        "total" => array(
          // 5 => print_number(array_sum($previous_quantity), 2),
          5 => print_number(array_sum($in_qty), 2),
          6 => print_number(array_sum($out_qty), 2),
          8 => print_number(array_sum($balance_quantity), 2),
          
        )
      );

    echo json_encode($result);
  }


}
