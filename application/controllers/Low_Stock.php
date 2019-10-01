<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Low_Stock extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['low_stock'];
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function index()
  {
    $this->authorized($this->module, 'index');
    if (isset($_POST['category']) && $_POST['category'] !== NULL) {
      $category = $_POST['category'];
    } else {
      $category = 'SPARE PART';
    }

    $this->data['selected_category']        = $category;
    $this->data['page']['title']            = $this->module['label'].' '.$category;
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] . '/index_data_source/'. $category);
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array(5);
    $this->data['grid']['order_columns']    = array ();

    $this->render_view($this->module['view'] .'/index');
  }

  public function index_data_source($category='SPARE PART')
  {
    $this->authorized($this->module, 'index');

    if ($category !== NULL){
      $category = (urldecode($category) === 'SPARE PART') ? 'SPARE PART' : urldecode($category);
    } 
    else {
      $category = urldecode($category);
    }

    $entities = $this->model->getIndex($category);

    $data = array();
    $no = $_POST['start'];
    $quantity = array();

    foreach ($entities as $row){
      if($row['minimum_quantity']>=$row['qty']){
        $no++;
        $col = array();
        $col[] = '<input type="checkbox" id="cb_'.$no.'" data-qty="'.$row['qty'].'" data-id="'.$row['part_number'].'" name="" style="display: inline;">';
        // $col[] = print_number($no);
        $col[] = print_string($row['part_number']);
        $col[] = print_string($row['description']);
        $col[] = print_number($row['minimum_quantity'],2);
        $col[] = print_string($row['condition']);
        $col[] = print_number($row['qty'],2);
        $col['DT_RowId'] = 'row_'. $row['id'];
        $col['DT_RowData']['pkey'] = $row['id'];
        $quantity[] = $row['qty'];
        // $prev_quantity[] = $row['previous_quantity'];
        // $balance_quantity[] = $row['balance_quantity'];

        

        // if ($this->has_role($this->module, 'detail')) {
        //   $col['DT_RowAttr']['onClick'] = '$(this).redirect("_blank");';
        //   $col['DT_RowAttr']['data-href'] = site_url($this->module['route'] .'/detail?part_number='. $row['part_number']);
        // }

        $data[] = $col;
      }
      
    }

    $result = array(
      "draw" => $_POST['draw'],
      // "recordsTotal" => $this->model->countIndex(),
      "recordsTotal" => $this->model->countIndex($category),
      "recordsFiltered" => $this->model->countIndexFiltered($category),
      "data" => $data,
      "total" => array(
        5 => print_number(array_sum($quantity), 2),
        // 7 => print_number(array_sum($quantity), 2),
        // 8 => print_number(array_sum($balance_quantity), 2),
      )
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

    
    $this->data['page']['title']            = $this->module['label'].' Part Number : '.$part_number;
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getDetailSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/detail_data_source?part_number='.$part_number);
    $this->data['grid']['fixed_columns']    = 2;
    
    $this->data['grid']['summary_columns']  = array( 6 );
    $this->data['grid']['order_columns']    = array (
      0 => array ( 0 => 3, 1 => 'asc' ),
      1 => array ( 0 => 4, 1 => 'asc' ),
      2 => array ( 0 => 2, 1 => 'asc' ),
      3 => array ( 0 => 1, 1 => 'asc' ),
      4 => array ( 0 => 5, 1 => 'asc' ),
      5 => array ( 0 => 6, 1 => 'asc' ),
      6 => array ( 0 => 7, 1 => 'asc' ),
      7 => array ( 0 => 8, 1 => 'asc' ),
      8 => array ( 0 => 8, 1 => 'asc' ),
    );

    $this->render_view($this->module['view'] .'/detail');
  }

  public function detail_data_source()
  {
    $this->authorized($this->module, 'index');

    if (isset($_GET['part_number']) && $_GET['part_number'] !== NULL){
      $part_number = $_GET['part_number'];
    } else {
      $part_number = NULL;
    }

    $entities = $this->model->getDetailIndex($part_number);

    $data = array();
    $no = $_POST['start'];
    $total_quantity = array();

    foreach ($entities as $row){
      $no++;
      $col = array();
      $col[] = print_number($no);
      $col[] = print_string($row['part_number']);
      $col[] = print_string($row['description']);
      $col[] = print_string($row['serial_number']);
      $col[] = print_string($row['condition']);
      $col[] = print_number($row['minimum_quantity']);
      $col[] = print_number($row['quantity'],2);      
      $col[] = print_number($row['minimum_quantity'], 2);
      $col[] = print_string($row['unit']);
      $col[] = print_string($row['stores']);
      $col[] = print_string($row['base']);
      $col['DT_RowId'] = 'row_'. $row['id'];
      $col['DT_RowData']['pkey'] = $row['id'];
      $total_quantity[]=$row['quantity'];


      // if ($this->has_role($this->modules['stock_card'], 'info')){
      //   $col['DT_RowAttr']['onClick']     = '$(this).redirect("_self");';
      //   $col['DT_RowAttr']['data-href'] = site_url($this->modules['stock_card']['route'] .'/info/'. $row['id']);
      // }

      $data[] = $col;
    }

    $result = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->model->countIndex(),
        "recordsFiltered" => $this->model->countIndexFiltered(),
        "data" => $data,
        "total" => array(
          6 => print_number(array_sum($total_quantity), 2)
       )
     );

    echo json_encode($result);
  }



}
