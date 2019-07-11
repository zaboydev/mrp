<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Budgeting extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->module = $this->modules['budgeting'];
	    $this->load->model($this->module['model'], 'model');
	    $this->data['module'] = $this->module;
      $this->load->model('budget_cot_model','',true);
	}

	public function index()
	{
		$this->data['page']['title']            = $this->module['label'];
		$this->data['page']['requirement']      = array('datatable');
    	$this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    	$this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source');
    	$this->data['grid']['fixed_columns']    = 2;
      $this->data['grid']['summary_columns']  = array( 9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36 );
    	$this->data['grid']['order_columns']    = array (

      //10 => array ( 0 => 11, 1 => 'asc' ),
      // 11 => array ( 0 => 12, 1 => 'asc' ),
      // 12 => array ( 0 => 13, 1 => 'asc' ),
      // 13 => array ( 0 => 14, 1 => 'asc' ),
    );
		$this->render_view($this->module['view'] .'/index');
	}
	public function index_data_source()
  {
   	$this->authorized($this->module, 'index');
    $entities = $this->model->index();

    $onhand     = array();$qty_requirement     = array();$total_val     = array();$total_qty     = array();
    $jan_val     = array(); $feb_val     = array(); $mar_val     = array(); $apr_val     = array(); $mei_val     = array(); $jun_val     = array();
    $jan_qty     = array(); $feb_qty     = array(); $mar_qty     = array(); $apr_qty     = array(); $mei_qty     = array(); $jun_qty     = array();
    $jul_val     = array(); $ags_val     = array(); $sep_val     = array(); $okt_val     = array(); $nov_val     = array(); $des_val     = array();
    $jul_qty     = array(); $ags_qty     = array(); $sep_qty     = array(); $okt_qty     = array(); $nov_qty     = array(); $des_qty     = array();
    $no       = $_POST['start'];

    foreach ($entities as $row){
      $no++;
      $col = array();
      if($row->status=="ON PROCESS"){
        if(config_item('auth_role') == 'CHIEF OF MAINTENANCE' || config_item('auth_role') == 'SUPER ADMIN'){
          $col[] = '<input type="checkbox" id="cb_'.$row->id_cot.'"  data-id="'.$row->id_cot.'" name="" style="display: inline;">';
        }         
      } else {
        $col[] = print_number($no);
      }
      $col[] = print_string($row->item_description);
      $col[] = print_string($row->serial_number);
      $col[] = print_string($row->part_number);
      $col[] = print_string($row->group_name);
      $col[] = print_string($row->category);
      $col[] = print_string($row->year);
      $col[] = print_string($row->status);
      $col[] = print_number($row->current_price);
      $col[] = print_number($row->onhand);
      $col[] = print_number($row->qty_requirement < 0 ? 0 : $row->qty_requirement);
      $col[] = print_number($row->jan_val < 0 ? 0 : $row->jan_val);
      $col[] = print_number($row->jan_qty < 0 ? 0 : $row->jan_qty);
      $col[] = print_number($row->feb_val < 0 ? 0 : $row->feb_val);
      $col[] = print_number($row->feb_qty < 0 ? 0 : $row->feb_qty);
      $col[] = print_number($row->mar_val < 0 ? 0 : $row->mar_val);
      $col[] = print_number($row->mar_qty < 0 ? 0 : $row->mar_qty);
      $col[] = print_number($row->apr_val < 0 ? 0 : $row->apr_val);
      $col[] = print_number($row->apr_qty < 0 ? 0 : $row->apr_qty);
      $col[] = print_number($row->mei_val < 0 ? 0 : $row->mei_val);
      $col[] = print_number($row->mei_qty < 0 ? 0 : $row->mei_qty);
      $col[] = print_number($row->jun_val < 0 ? 0 : $row->jun_val);
      $col[] = print_number($row->jun_qty < 0 ? 0 : $row->jun_qty);
      $col[] = print_number($row->jul_val < 0 ? 0 : $row->jul_val);
      $col[] = print_number($row->jul_qty < 0 ? 0 : $row->jul_qty);
      $col[] = print_number($row->ags_val < 0 ? 0 : $row->ags_val);
      $col[] = print_number($row->ags_qty < 0 ? 0 : $row->ags_qty);
      $col[] = print_number($row->sep_val < 0 ? 0 : $row->sep_val);
      $col[] = print_number($row->sep_qty < 0 ? 0 : $row->sep_qty);
      $col[] = print_number($row->oct_val < 0 ? 0 : $row->oct_val);
      $col[] = print_number($row->oct_qty < 0 ? 0 : $row->oct_qty);
      $col[] = print_number($row->nov_val < 0 ? 0 : $row->nov_val);
      $col[] = print_number($row->nov_qty < 0 ? 0 : $row->nov_qty);
      $col[] = print_number($row->des_val < 0 ? 0 : $row->des_val);
      $col[] = print_number($row->des_qty < 0 ? 0 : $row->des_qty);
      $col[] = print_number($row->total_val < 0 ? 0 : $row->total_val);
      $col[] = print_number($row->total_qty < 0 ? 0 : $row->total_qty);

      // $unit_value[]   = $row['received_unit_value'];
      $onhand[]  = $row->onhand;$qty_requirement[]  = $row->qty_requirement;$total_val[]  = $row->total_val;$total_qty[]  = $row->total_qty;
      $jan_val[] = $row->jan_val; $feb_val[]= $row->feb_val; $mar_val[]= $row->mar_val; $apr_val[]= $row->apr_val; $mei_val[]= $row->mei_val; $jun_val[]= $row->jun_val;
      $jan_qty[] = $row->jan_qty; $feb_qty[]= $row->feb_qty; $mar_qty[]= $row->mar_qty; $apr_qty[]= $row->apr_qty; $mei_qty[]= $row->mei_qty; $jun_qty[]= $row->jun_qty;
      $jul_val[] = $row->jul_val; $ags_val[]= $row->ags_val; $sep_val[]= $row->sep_val; $okt_val[]= $row->okt_val; $nov_val[]= $row->nov_val; $des_val[]= $row->des_val;
      $jul_qty[] = $row->jul_qty; $ags_qty[]= $row->ags_qty; $sep_qty[]= $row->sep_qty; $okt_qty[]= $row->okt_qty; $nov_qty[]= $row->nov_qty; $des_qty[]= $row->des_qty;
      $col['DT_RowId'] = 'row_'. $row->id_cot;
      $col['DT_RowData']['pkey'] = $row->id_cot;
      $col['DT_RowAttr']['class']     = 'edit';
      $col['DT_RowAttr']['data-id']     = $row->id_cot;
      $col['DT_RowAttr']['data-status']     = $row->status;
      $col['DT_RowAttr']['data-target'] = '#data-modal';
      $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] .'/edit/'.$row->id_cot);
      $data[] = $col;
    }
     $result = array(
      "draw"                => $_POST['draw'],
      "recordsTotal"        => $this->model->countIndex(),
      "recordsFiltered"     => $this->model->countFilteredIndex(),
      "data"                => $data,
      "total"               => array(
          9   => print_number(array_sum($onhand), 2),
          10  => print_number(array_sum($qty_requirement), 2),
          11  => print_number(array_sum($jan_val), 2),
          12  => print_number(array_sum($jan_qty), 2),
          13  => print_number(array_sum($feb_val), 2),
          14  => print_number(array_sum($feb_qty), 2),
          15  => print_number(array_sum($mar_val), 2),
          16  => print_number(array_sum($mar_qty), 2),
          17  => print_number(array_sum($apr_val), 2),
          18  => print_number(array_sum($apr_qty), 2),
          19  => print_number(array_sum($mei_val), 2),
          20  => print_number(array_sum($mei_qty), 2),
          21  => print_number(array_sum($jun_val), 2),
          22  => print_number(array_sum($jun_qty), 2),
          23  => print_number(array_sum($jul_val), 2),
          24  => print_number(array_sum($jul_qty), 2),
          25  => print_number(array_sum($ags_val), 2),
          26  => print_number(array_sum($ags_qty), 2),
          27  => print_number(array_sum($sep_val), 2),
          28  => print_number(array_sum($sep_qty), 2),
          29  => print_number(array_sum($okt_val), 2),
          30  => print_number(array_sum($okt_qty), 2),
          31  => print_number(array_sum($nov_val), 2),
          32  => print_number(array_sum($nov_qty), 2),
          33  => print_number(array_sum($des_val), 2),
          34  => print_number(array_sum($des_qty), 2),
          35  => print_number(array_sum($total_val), 2),
          36  => print_number(array_sum($total_qty), 2),
        )
    );
    echo json_encode($result);
  }
  public function approve(){
  	if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');
  	$id_cots = $this->input->post('id_cots');
  	$id_cots = str_replace("|", "", $id_cots);
  	$id_cots = substr($id_cots, 0,-1);
  	$id_cots = explode(",", $id_cots);
  	$approve = $this->model->approveData($id_cots);
  	if($approve['update']>0){
      $this->session->set_flashdata('alert', array(
                'type' => 'success',
                'info' => $approve['update']." data has been update!"
      ));
    }
    if(sizeof($approve['error'])>0){
      $this->session->set_flashdata('alert', array(
                'type' => 'danger',
                'info' => "There are ".sizeof($approve['error'])." errors\n#". implode("\n#", $approve['error'])
      ));
    }
    $result['status'] = "success";
    echo json_encode($result);
  }
  public function edit($id){
     // if ($this->input->is_ajax_request() === FALSE)
     //  redirect($this->modules['secure']['route'] .'/denied');
      $entity   = $this->model->cotById($id);
      // $hours = $this->model->cotHourById($id);
      $hours = $this->model->cotQtyById($id);
      $this->data['id_cot'] = $id;
      $this->data['entity'] = $entity;
      $this->data['hour'] = $hours;
      $return['type'] = 'success';
      $return['info'] = $this->load->view($this->module['view'] .'/edit', $this->data, TRUE);
    echo json_encode($return);
  }
  public function save_budget(){
    // if ($this->input->is_ajax_request() === FALSE)
    //  redirect($this->modules['secure']['route'] .'/denied');
    //$edit = $this->model->updateBudget(); 
    $result["status"] = "failed";
    $onhand = $this->input->post('onhand');
    $id_cot = $this->input->post('id_cot');
    $updateOnhand = $this->model->updateOnhand($onhand,$id_cot);
    if($updateOnhand){
      $cot_hours = $this->input->post('cot_hour');
      $qty_requirement = $this->input->post('qty_requirement');
      $new_req = $qty_requirement - $onhand;
      $items = $this->model->itemById($id_cot);
      $price = $items->current_price;
      $updateBudget = $this->model->updateBudget($id_cot,$cot_hours,$new_req,$price);
      if($updateBudget == 12){
          $result["status"] = "success";
          $this->session->set_flashdata('alert', array(
                'type' => 'success',
                'info' => $approve['update']." data has been update!"
      ));
      }
    }
    echo json_encode($result);
  }
  public function update_onhand(){
    $result['status'] = "failed";
    $id_cot = $this->input->post('id_cot');
    $item = $this->model->itemById($id_cot);
    $id_stock = getStockId($item->id,"SERVICEABLE");
    $onhand = $this->budget_cot_model->countOnhand($id_stock);
    if($onhand != null){
      $result['status'] = "success";
      $result['onhand'] = $onhand->sum;
    }
    echo json_encode($result);
  }
}

/* End of file Budgeting.php */
/* Location: ./application/controllers/Budgeting.php */