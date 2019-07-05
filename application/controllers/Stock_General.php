<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_General extends MY_Controller
{
  protected $module;
  //public $today;

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
    $today=date('Y-m-d');

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


    $this->data['selected_category']        = $category;
    $this->data['selected_condition']       = $condition;
    $this->data['page']['title']            = $this->module['label'] .' / '. $category .' / '. $condition.' / '.$periode;
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source/'. $condition .'/'. $category.'/'.$start_date.'/'.$end_date);
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array( 6,7,8,9,10,11,12,13,14 );
    $this->data['grid']['order_columns']    = array();

    $this->render_view($this->module['view'] .'/index');
  }

  public function index_data_source($condition = "SERVICEABLE", $category = 'all',$start_date=NULL, $end_date = NULL )
  {
    $this->authorized($this->module, 'index');
    $today=date('Y-m-d');

    // if ($start_date && $end_date !== NULL){
    //   $start_date  = urldecode($start_date);
    //   $end_date = urldecode($end_date);
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

    $entities = $this->model->getIndex($condition, $category, $start_date, $end_date);

    $data = array();
    $no = $_POST['start'];
    $grand_quantity = array();

    foreach ($entities as $row){
      $no++;
      $col = array();
      $col[] = print_number($no);
      $col[] = print_string($row['part_number']);
      $col[] = print_string($row['serial_number']);
      $col[] = print_string($row['category']);
      $col[] = print_string($row['condition']);
      $col[] = print_string($row['unit']);
      $col[] = print_number($row['wisnu_qty']);
      $col[] = print_number($row['byw_qty']);
      $col[] = print_number($row['solo_qty']);
      $col[] = print_number($row['lmbk_qty']);
      $col[] = print_number($row['jmbr_qty']);
      $col[] = print_number($row['plkry_qty']);
      $col[] = print_number($row['wisnu_rekon_qty']);
      $col[] = print_number($row['bsr_qty']);
      $col[] = print_number($row['wisnu_rekon_qty']+$row['bsr_qty']+$row['wisnu_qty']+$row['byw_qty']+$row['solo_qty']+$row['lmbk_qty']+$row['jmbr_qty']+$row['plkry_qty']);

      $col['DT_RowId'] = 'row_'. $row['id'];
      $col['DT_RowData']['pkey'] = $row['id'];
      $grand_quantity[] = $row['wisnu_qty'];
      $grand_quantity_byw[] = $row['byw_qty'];
      $grand_quantity_solo[] = $row['solo_qty'];
      $grand_quantity_jmbr[] = $row['jmbr_qty'];
      $grand_quantity_lmbk[] = $row['lmbk_qty'];
      $grand_quantity_plkry[] = $row['plkry_qty'];
      $grand_quantit_wisnu_rekony[] = $row['wisnu_rekon_qty'];
      $grand_quantity_bsr[] = $row['bsr_qty'];

      $data[] = $col;
    }

    $result = array(
      "draw" => $_POST['draw'],
      "recordsTotal" => $this->model->countIndex($condition, $category,$start_date, $end_date),
      "recordsFiltered" => $this->model->countIndexFiltered($condition, $category,$start_date, $end_date),
      "data" => $data,
      "total" => array(
        6 => print_number(array_sum($grand_quantity)),
        7 => print_number(array_sum($grand_quantity_byw)),
        8 => print_number(array_sum($grand_quantity_solo)),
        9 => print_number(array_sum($grand_quantity_lmbk)),
        10 => print_number(array_sum($grand_quantity_jmbr)),
        11 => print_number(array_sum($grand_quantity_plkry)),
        12 => print_number(array_sum($grand_quantit_wisnu_rekony)),
        13 => print_number(array_sum($grand_quantity_bsr)),
        14 => print_number(array_sum($grand_quantity_bsr)+array_sum($grand_quantit_wisnu_rekony)+array_sum($grand_quantity_plkry)+array_sum($grand_quantity_jmbr)+array_sum($grand_quantity_lmbk)+array_sum($grand_quantity_solo)+array_sum($grand_quantity_byw)+array_sum($grand_quantity))
      )
    );

    echo json_encode($result);
  }
}
