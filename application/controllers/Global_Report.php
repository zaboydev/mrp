<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Global_Report extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['global_report'];
    $this->load->helper($this->module['helper']);
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function index_data_source()
  {
    if (is_granted($this->module, 'index') === FALSE){
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {
      $entities     = $this->model->getIndex();
      $data         = array();
      $no           = $_POST['start'];
      $pr_qty     = array();
      $pr_val     = array();
      $poe_qty     = array();
      $poe_val     = array();
      $po_qty     = array();
      $po_val     = array();
      $grn_qty     = array();
      $grn_val     = array();
      $sisa     = array();
      
      foreach ($entities as $row){
        $no++;
        $col = array();        
        $col[] = print_number($no);
        $col[] = print_string($row['pr_number']);
        $col[] = print_string($row['description']);
        $col[] = print_string($row['part_number']);
        $col[] = print_number($row['pr_qty'], 2);
        $col[] = print_number($row['pr_val'], 2);
        $col[] = print_number($row['poe_qty'], 2);
        $col[] = print_number($row['poe_val'], 2);
        $col[] = print_number($row['po_qty'], 2);
        $col[] = print_number($row['po_val'], 2);
        $col[] = print_number($row['grn_qty'], 2);
        $col[] = print_number($row['grn_val'], 2);
        $col[] = print_number($row['sisa'], 2);
        $col[] = '';
        $col['DT_RowId'] = 'row_'. $row['id'];
        $col['DT_RowData']['pkey']  = $row['id'];

        $pr_qty[]       = $row['pr_qty'];
        $pr_val[]       = $row['pr_val'];
        $poe_qty[]      = $row['poe_qty'];
        $poe_val[]      = $row['poe_val'];
        $po_qty[]       = $row['po_qty'];
        $po_val[]       = $row['po_val'];
        $grn_qty[]      = $row['grn_qty'];
        $grn_val[]      = $row['grn_val'];
        $sisa[]      = $row['sisa'];

        $data[] = $col;
      }

      $result = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->model->countIndex(),
        "recordsFiltered" => $this->model->countIndexFiltered(),
        "data" => $data,
        "total" => array(
          4   => print_number(array_sum($pr_qty), 2),
          5   => print_number(array_sum($pr_val), 2),
          6   => print_number(array_sum($poe_qty), 2),
          7   => print_number(array_sum($poe_val), 2),
          8   => print_number(array_sum($po_qty), 2),
          9   => print_number(array_sum($po_val), 2),
          10   => print_number(array_sum($grn_qty), 2),
          11   => print_number(array_sum($grn_val), 2),
          12  => print_number(array_sum($sisa), 2)
        )
      );
    }

    echo json_encode($result);
  }
  public function index()
  {
    $this->authorized($this->module, 'index');

    $this->data['page']['title']            = $this->module['label'];
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source');
    $this->data['grid']['fixed_columns']    = 3;
    $this->data['grid']['summary_columns']  = array(4,5,6,7,8,9,10,11,12);
    $this->data['grid']['order_columns']    = array();
    $this->render_view($this->module['view'] .'/index');
  }

  public function budget_po_report()
  {
    $this->data['page']['title']            = "Budget - Purchase Order Report";
    $data['month'] = [
      '1' => 'Januari',
      '2' => 'Februari',
      '3' => 'Maret',
      '4' => 'April',
      '5' => 'Mei',
      '6' => 'Juni', 
      '7' => 'Juli',
      '8' => 'Agustus',
      '9' => 'September',
      '10' => 'Oktober',
      '11' => 'November',
      '12' => 'Desember',
    ];
    $_SESSION['global_report']['month'] = date('m');
    $_SESSION['global_report']['year'] = date('Y');
    $_SESSION['global_report']['opname_start_date'] = last_update();
    $this->render_view($this->module['view'] .'/report',$data);
  }

  public function selected_month()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['global_report']['month'] = $_GET['data'];
  }

  public function selected_year()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['global_report']['year'] = $_GET['data'];
  }

  public function print_report(){
    $month = $_SESSION['global_report']['month'];
    $year = $_SESSION['global_report']['year'];
    $data['data_budget'] = $this->model->select_budget($month,$year);
    foreach ($data['data_budget'] as $budget) {
      $data['mtd_qty'][$budget['item_part_number']] = $this->model->qty_po($month,$year,'mtd',$budget['item_part_number']);
      $data['mtd_val'][$budget['item_part_number']] = $this->model->val_po($month,$year,'mtd',$budget['item_part_number']);
      $data['ytd_qty'][$budget['item_part_number']] = $this->model->qty_po($month,$year,'ytd',$budget['item_part_number']);
      $data['ytd_val'][$budget['item_part_number']] = $this->model->val_po($month,$year,'ytd',$budget['item_part_number']);
    }
    $this->render_view($this->module['view'] .'/print_report',$data);
  }

  
}
