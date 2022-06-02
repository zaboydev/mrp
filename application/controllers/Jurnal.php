<?php defined('BASEPATH') or exit('No direct script access allowed');

class Jurnal extends MY_Controller
{
  protected $module;
  protected $id_item = 0;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['jurnal'];
    $this->load->model($this->module['model'], 'model');
    $this->load->helper($this->module['helper']);
    $this->data['module'] = $this->module;
  }

  public function index()
  {
    $this->authorized($this->module, 'index');

    $this->data['page']['title']            = $this->module['label'];
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source');
    $this->data['grid']['data_export']      = site_url($this->module['route'] .'/export');
    $this->render_view($this->module['view'] . '/index');
  }

  public function info($id)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'info') === FALSE) {
      $return['type'] = 'denied';
      $return['info'] = "You don't have permission to access this data. You may need to login again.";
    } else {
      $entity = $this->model->findById($id);

      $this->data['entity'] = $entity;

      $return['type'] = 'success';
      $return['info'] = $this->load->view($this->module['view'] . '/info', $this->data, TRUE);
    }

    echo json_encode($return);
  }

  public function index_data_source()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'index') === FALSE) {
      $return['type'] = 'denied';
      $return['info'] = "You don't have permission to access this data. You may need to login again.";
    } else {
      $entity = $this->model->getIndex();

      $this->data['entity'] = $entity;

      $return['type'] = 'success';
      $return['info'] = $this->load->view($this->module['view'] . '/data', $this->data, TRUE);
    }

    echo json_encode($return);
  }  

  public function export()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'index') === FALSE) {
      $return['type'] = 'denied';
      $return['info'] = "You don't have permission to access this data. You may need to login again.";
    } else {
      $export     = $_GET['export'];
      $start_date = $_GET['start_date'];
      $end_date   = $_GET['end_date'];
      $tipe       = $_GET['type'];

      $return['open'] = site_url($this->module['route'] .'/get_export?'.'start_date='.$start_date.'&end_date='.$end_date.'&type='.$tipe.'&export='.$export);
      
    }

    echo json_encode($return);
  }  

  public function get_export(){
    $entity = $this->model->getIndex();

    $this->data['entity']     = $entity;
    $this->data['tipe']       = $_GET['export'];
    $this->data['title']      = 'Journal Report '.$_GET['start_date'].' s/d '.$_GET['end_date'].'-'.$_GET['type'];
    $this->data['periode']    = $_GET['start_date'].' s/d '.$_GET['end_date'];
    $this->render_view($this->module['view'] . '/print', $this->data);
  }

}
