<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Po_Grn extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['po_grn'];
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
      $quantity_order     = array();
      $value_order = array();
      $quantity_receipt     = array();
      $value_receipt = array();
      
      foreach ($entities as $row){
        $no++;
        $col = array();        
        $col[] = print_number($no);
        $col[] = print_string($row['po_number']);
        $col[] = print_string($row['vendor']);
        $col[] = print_string($row['part_number']);
        $col[] = print_string($row['description']);
        $col[] = print_string($row['default_currency']);
        $col[] = print_number($row['po_qty'], 2);
        $col[] = print_number($row['po_val'], 2);
        // $col[] = print_string($row['grn_number']);
        $col[] = print_number($row['grn_qty'], 2);
        if($row['default_currency']=='IDR'){
          $col[] = print_number($row['grn_val_idr'], 2);
          $value_receipt[]            = $row['grn_val_idr'];
        }else{
          $col[] = print_number($row['grn_val_usd'], 2);
          $value_receipt[]            = $row['grn_val_usd'];
        }
        $col[] = print_number($row['po_qty']-$row['grn_qty'], 2);
        $col['DT_RowId'] = 'row_'. $row['id'];
        $col['DT_RowData']['pkey']  = $row['id'];

        $quantity_order[]           = $row['po_qty'];
        $value_order[]              = $row['po_val'];
        $quantity_receipt[]         = $row['grn_qty'];
        $data[]         = $col;
      }

      $result = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->model->countIndex(),
        "recordsFiltered" => $this->model->countIndexFiltered(),
        "data" => $data,
        "total" => array(
          6 => print_number(array_sum($quantity_order),2),
          7 => print_number(array_sum($value_order),2),
          8 => print_number(array_sum($quantity_receipt),2),
          9 => print_number(array_sum($value_receipt),2),
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
    $this->data['grid']['summary_columns']  = array(6,7,8,9);
    $this->data['grid']['order_columns']    = array();
    $this->render_view($this->module['view'] .'/index');
  }

  
}
