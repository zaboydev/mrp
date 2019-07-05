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
    $this->data['grid']['summary_columns']  = array( 11 );
    $this->data['grid']['order_columns']    = array (
      0 => array ( 0 => 1, 1 => 'asc' ),
      1 => array ( 0 => 2, 1 => 'asc' ),
      2 => array ( 0 => 3, 1 => 'asc' ),
      3 => array ( 0 => 4, 1 => 'asc' ),
      4 => array ( 0 => 5, 1 => 'asc' ),
      5 => array ( 0 => 6, 1 => 'asc' )
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
      $col[] = print_string($row['group']);
      $col[] = print_string($row['description']);
      $col[] = print_string($row['part_number']);
      $col[] = print_number($row['quantity'], 2);
      $col[] = print_number($row['minimum_quantity'], 2);
      $col[] = print_string($row['unit']);
      $col['DT_RowId'] = 'row_'. $row['id'];
      $col['DT_RowData']['pkey'] = $row['id'];
      $quantity[] = $row['quantity'];

      if ($this->has_role($this->module, 'info')) {
        $col['DT_RowData']['single_click'] = site_url($this->module['route'] .'/info');
        $col['DT_RowData']['double_click'] = site_url($this->module['route'] .'/info');
      }

      $data[] = $col;
    }

    $result = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->model->countIndex(),
        "recordsFiltered" => $this->model->countIndexFiltered(),
        "data" => $data,
        "total" => array(
          11 => print_number(array_sum($quantity), 2)
       )
     );

    echo json_encode($result);
  }

  public function info($id)
  {
    $this->authorized($this->module, 'info');

    $entity = $this->model->findItemById($id);

    $this->data['page']['title']            = $entity['description'] .' '. $entity['part_number'];
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getInfoSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/info_ajax/'. $id);
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array( 11 );
    $this->data['grid']['order_columns']    = array ();
    // $this->data['grid']['order_columns']    = array (
    //   0 => array ( 0 => 1, 1 => 'desc' ),
    //   1 => array ( 0 => 2, 1 => 'asc' ),
    //   2 => array ( 0 => 3, 1 => 'asc' ),
    //   3 => array ( 0 => 4, 1 => 'asc' ),
    //   4 => array ( 0 => 5, 1 => 'asc' ),
    //   5 => array ( 0 => 6, 1 => 'asc' ),
    //   6 => array ( 0 => 7, 1 => 'asc' ),
    //   7 => array ( 0 => 8, 1 => 'asc' ),
    //   8 => array ( 0 => 9, 1 => 'asc' ),
    //   9 => array ( 0 => 10, 1 => 'asc' ),
    //   10 => array ( 0 => 11, 1 => 'asc' ),
    // );

    $this->render_view($this->module['view'] .'/info');
  }

  public function info_ajax($id)
  {
    $this->authorized($this->module, 'info');

    $item     = $this->model->findItemById($id);
    $entities = $this->model->getInfo($id);
    $data     = array();
    $no       = $_POST['start'];
    $quantity = array();

    foreach ($entities as $key => $row){
      $no++;
      $col = array();
      $col[] = print_number($no);
      $col[] = print_date($row['date_of_entry']);
      $col[] = print_string($row['document_number']);
      $col[] = print_string($row['serial_number']);
      $col[] = print_string($row['warehouse'], '');
      $col[] = print_string($row['stores'], '');
      $col[] = print_string($row['condition']);
      $col[] = print_string($row['received_from'], '');
      $col[] = print_string($row['received_by'], '');
      $col[] = print_string($row['issued_to'], '');
      $col[] = print_string($row['issued_by'], '');
      $col[] = print_number($row['quantity'], 2);
      $col[] = $row['remarks'];
      $col['DT_RowId'] = 'row_'. $key;
      $col['DT_RowData']['pkey'] = $key;
      $col['DT_RowClass'] = ($row['quantity'] < 0) ? 'text-accent' : '';
      $quantity[] = $row['quantity'];

      $data[] = $col;
    }

    $result = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->model->countInfo($id),
        "recordsFiltered" => $this->model->countInfoFiltered($id),
        "data" => $data,
        "total" => array(
          11 => print_number(array_sum($quantity), 2)
        )
      );

    echo json_encode($result);
  }
}
