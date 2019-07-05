<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['setting'];
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function index()
  {
    $this->authorized($this->module, 'index');

    //$entities = $this->model->find_all();

    //$this->data['entities'] = $entities;
    $this->data['page_title'] = lang('page_title_index');
    $this->data['page_content'] = $this->module['view'] .'/index';

    $this->render_view();
  }

  public function warehouse()
  {
    $this->authorized($this->module, 'warehouse');

    $this->load->library('form_validation');

    $validation_rules = array(
      array(
        'field' => 'setting_value',
        'label' => 'Main Warehouse',
        'rules' => array(
          'trim',
          'required',
       ),
        'errors' => array(
          'required' => 'The Main Warehouse field is required.',
       )
     ),
   );

    $this->form_validation->set_rules($validation_rules);

    if ($this->form_validation->run() === TRUE)
    {
      if ($this->model->update('MAIN BASE') === FALSE){
        $this->session->set_flashdata('alert', array(
          'type' => 'danger',
          'info' => 'Update failed! Please try again later.'
       ));

        redirect($this->module['route'] .'/warehouse');
      }

      $this->session->set_flashdata('alert', array(
        'type' => 'success',
        'info' => 'Update Success!'
     ));
    }

    $warehouses = $this->model->findAllWarehouses('AVAILABLE');
    $this->data['warehouses'] = $warehouses;

    $setting_value = $this->model->find_by_setting_name('MAIN BASE');
    $this->data['setting_value'] = $setting_value;

    $this->data['page_content'] = $this->module['view'] .'/warehouse';
    $this->data['page_header'] = lang('page_header');
    $this->data['page_title'] = lang('page_title_warehouse');
    $this->data['page_desc'] = NULL;

    $this->render_view();
  }
}
