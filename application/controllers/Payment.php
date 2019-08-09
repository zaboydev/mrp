<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends MY_Controller
{
  protected $module;
  protected $id_item=0;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['payment'];
    $this->load->model($this->module['model'], 'model');
    $this->load->helper($this->module['helper']);
    $this->data['module'] = $this->module;
  }

 public function index(){
    $this->data['page']['title']            = $this->module['label'];
    $this->data['account']                  = $this->model->getAccount();
    $this->data['suplier']                  = $this->model->getSuplier();
    $this->render_view($this->module['view'] .'/index');
 }
 public function getPo(){
  if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');
  $vendor = $this->input->post('vendor');
  $po = $this->model->getPoByVendor($vendor);
  echo json_encode($po);
 }
 public function save(){
  if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');
  $save = $this->model->save();
  if($save){
    $result["status"] = "success";
  } else {
    $result["status"] = "failed";
  }
  echo json_encode($result);
 }
}
