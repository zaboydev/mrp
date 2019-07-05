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
    foreach ($entities as $row){
      $col = array();
      $col[] = ($row->status === strtoupper("approved")||config_item('auth_role') !== 'CHIEF OF MAINTANCE') ? '' : '<input type="checkbox" id="cb_'.$row->id_cot.'"  data-id="'.$row->id_cot.'" name="" style="display: inline;">';
      $col[] = print_string($row->item_description);;
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
      "draw" => $_POST['draw'],
      "recordsTotal" => $this->model->countIndex(),
      "recordsFiltered" => $this->model->countFilteredIndex(),
      "data" => $data,
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
      $hours = $this->model->cotHourById($id);
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