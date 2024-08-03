<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Permintaan_Adjustment extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['permintaan_adjustment'];
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function index()
  {
    $this->authorized($this->module, 'index');

    $this->data['page']['title']            = $this->module['label'];
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source/');
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array();
    $this->data['grid']['order_columns']    = array ();

    $this->render_view($this->module['view'] .'/index');
  }

  public function index_data_source()
  {
    $this->authorized($this->module, 'index');

    $entities = $this->model->getIndex();

    $data = array();
    $no = $_POST['start'];
    $quantity = array();
	$prev_quantity = array();
	$balance_quantity = array();

    foreach ($entities as $row){
      $no++;
      $col = array();
      $col[] = print_number($no);
      $col[] = print_string($row['part_number']);
      $col[] = print_string($row['description']);
      $col[] = print_string($row['category']);
      $col[] = print_string($row['group']);
      $col[] = print_string($row['condition']);
      // $col[] = print_date($row['created_at']);
      $col[] = print_number($row['previous_quantity'], 2);
      $col[] = print_number($row['adjustment_quantity'], 2);
      $col[] = print_number($row['balance_quantity'], 2);
      $col[] = print_string($row['unit']);
      $col[] = print_string($row['created_by']);
      $col[] = print_date($row['created_at'], 'd F Y');
      $col['DT_RowId'] = 'row_'. $row['id'];
      $col['DT_RowData']['pkey'] = $row['id'];
      $quantity[] = $row['adjustment_quantity'];
      $prev_quantity[] = $row['previous_quantity'];
      $balance_quantity[] = $row['balance_quantity'];

      

      if ($this->has_role($this->module, 'detail')) {
        $col['DT_RowAttr']['onClick'] = '$(this).redirect("");';
        $col['DT_RowAttr']['data-href'] = site_url($this->module['route'] .'/detail?id='. $row['id']);
      }

      $data[] = $col;
    }

    $result = array(
      "draw" => $_POST['draw'],
      "recordsTotal" => $this->model->countIndex(),
      "recordsFiltered" => $this->model->countIndexFiltered(),
      "data" => $data,
      "total" => array(
        6 => print_number(array_sum($prev_quantity), 2),
        7 => print_number(array_sum($quantity), 2),
        8 => print_number(array_sum($balance_quantity), 2),
      )
    );

    echo json_encode($result);
  }

  public function detail(){

    // $this->authorized($this->module, 'detail');

    if (isset($_GET['id']) && $_GET['id'] !== NULL){
      $id = $_GET['id'];
    } else {
      $id = NULL;
    }    

    $entity = $this->model->findById($id);

    if (!$entity or empty($entity)){
      $this->session->set_flashdata('alert', array(
        'type' => 'danger',
        'info' => 'Data not AVAILABLE to edit!'
      ));

      redirect($this->module['route']);
    }

    $this->data['entity'] = $entity;

    $this->render_view($this->module['view'] .'/detail');

  }



}
