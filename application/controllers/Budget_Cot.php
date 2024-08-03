<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Budget_Cot extends MY_Controller {
	protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['budget_cot'];
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

	public function index()
	{
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source');

    
    $this->data['page']['title']            = $this->module['label'];
    $this->data['grid']['fixed_columns']    = 2;

    // $this->data['grid']['summary_columns']  = array( 7, 8, 9, 10, 11 );
    $this->data['grid']['order_columns']    = array (
      0 => array ( 0 => 4, 1 => 'asc' ),
      1 => array ( 0 => 5, 1 => 'asc' ),
      2 => array ( 0 => 2, 1 => 'asc' ),
      3 => array ( 0 => 1, 1 => 'asc' ),
      4 => array ( 0 => 3, 1 => 'asc' ),
      5 => array ( 0 => 6, 1 => 'asc' ),

    );

    $this->render_view($this->module['view'] .'/index');		
	}
	public function index_data_source()
  {
    $this->authorized($this->module, 'index');
    $entities = $this->model->index();

    $data = array();
    $no = $_POST['start'];
    foreach ($entities as $row){
      $no++;
      $col = array();
      $col[] = print_number($no);
      $col[] = print_string($row->description);
      $col[] = print_string($row->part_number);
      $col[] = print_string($row->serial_number);
      $col[]  = print_string($row->hours);
      $col[] = print_string($row->year);
      $col[] = print_string($row->qty_standar);
      $col[] = print_string($row->qty_requirement);
      $col['DT_RowId'] = 'row_'. $row->id;
      $col['DT_RowData']['pkey'] = $row->id;
      $data[] = $col;
    }

    $result = array(
      "draw" => $_POST['draw'],
      "recordsTotal" => $this->model->countIndex(),
      "recordsFiltered" => $this->model->countIndex(),
      "data" => $data,
    );
    echo json_encode($result);
  }
  public function add_cot(){
    //var_dump($_POST);
    $this->data['hour'] = $this->input->post('hour');
    $this->data['year'] = $this->input->post('year');
    $this->data['id_kategori'] = $this->input->post('category');
    $id_kelipatan = $this->input->post('id_kelipatan');
    $this->data['id_kelipatan'] = $id_kelipatan;
    $this->data['kelipatan'] = $this->model->getKelipatanById($id_kelipatan);
    $this->data['page']['title']            = 'Add Proyeksi COT';
   $this->render_view($this->module['view'] .'/form_cot');
  }
  public function itemCot()
  {
    $data = $this->model->itemCot();
    $dataCount = $this->model->countItemCot();
    $limit = $this->input->post('length');
    $result['data'] = $data;
    $result['dataCount'] = $dataCount;
    $result['page_count'] = floor($dataCount/$limit) + ($dataCount%$limit === 0 ? 0:1);
    echo json_encode($result);
  }
  public function saveCot()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');
    $itemKey = str_replace('|', "", $this->input->post('key'));
    $itemKey = substr($itemKey, 0, -1);
    $itemKey = explode(",", $itemKey);
    $standardQuantity = str_replace(".", "", $this->input->post('standardQuantity'));
    $standardQuantity = json_decode($standardQuantity);
    $range1 = str_replace(".", "", $this->input->post('range1'));
    $range1 = json_decode($range1);
    $range2 = str_replace(".", "", $this->input->post('range2'));
    $range2 = json_decode($range2);
    $hour = str_replace(".", "", $this->input->post('hour'));
    $year = $this->input->post('year');
    $id_kelipatan = $this->input->post('id_kelipatan');
    $kelipatan = $this->input->post('kelipatan');
    // $process = $this->model->cotProcess($hour,$year,$id_kelipatan,$kelipatan,$itemKey,$standardQuantity);
    $process = $this->model->cotProcess($hour,$year,$id_kelipatan,$kelipatan,$itemKey,$standardQuantity,$range1,$range2);
    if($process['insert']>0){
      $this->session->set_flashdata('alert', array(
                'type' => 'success',
                'info' => $process['insert']." data has been inserted!"
      ));
    }
    if($process['update']>0){
      $this->session->set_flashdata('alert', array(
                'type' => 'success',
                'info' => $process['update']." data has been update!"
      ));
    }
    if(sizeof($process['error'])>0){
      $this->session->set_flashdata('alert', array(
                'type' => 'danger',
                'info' => "There are ".sizeof($process['error'])." errors\n#". implode("\n#", $process['error'])
      ));
    }
    $result['status'] = "success";
    echo json_encode($result);
  }
}

/* End of file Budget_Cot.php */
/* Location: ./application/controllers/Budget_Cot.php */