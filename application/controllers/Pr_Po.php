<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pr_Po extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['pr_po'];
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
      $quantity     = array();
      $total_amount = array();
      $amount_paid  = array();
      
      foreach ($entities as $row){
        $no++;
        $col = array();        
        $col[] = print_number($no);
        $col[] = print_string($row['part_number']);
        $col[] = print_string($row['product_name']);
        $col[] = print_string($row['pr_number']);
        $col[] = print_number($row['pr_qty'], 2);
        // $col[] = print_number($row['pr_val'], 2);
        // $col[] = print_string($row['po_number']);
        $col[] = print_number($row['po_qty'], 2);
        $col[] = print_number($row['po_qty']*$row['unit_price'], 2);
        $col[] = print_number($row['sisa'], 2);
        $col[] = '';
        $col['DT_RowId'] = 'row_'. $row['id'];
        $col['DT_RowData']['pkey']  = $row['id'];

        $quantity[]     = $row['quantity'];
        $total_amount[] = $row['total_amount'];
        $amount_paid[]  = $row['amount_paid'];
        $data[]         = $col;
      }

      $result = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->model->countIndex(),
        "recordsFiltered" => $this->model->countIndexFiltered(),
        "data" => $data
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
    $this->data['grid']['summary_columns']  = array();
    $this->data['grid']['order_columns']    = array();
    $this->render_view($this->module['view'] .'/index');
  }

  
}
