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

  // public function index()
  // {
  //   $this->authorized($this->module, 'index');

  //   //$entities = $this->model->find_all();

  //   //$this->data['entities'] = $entities;
  //   $this->data['page_title'] = lang('page_title_index');
  //   $this->data['page_content'] = $this->module['view'] .'/index';

  //   $this->render_view();
  // }

  public function index()
  {
    $this->authorized($this->module, 'index');

    $this->data['page']['title']        = 'Setting';
    $this->data['page']['requirement']  = array('datatable', 'form_create', 'form_edit');
    $this->data['grid']['column']           = $this->model->getSelectedColumns();
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source');
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = NULL;
    $this->data['grid']['order_columns']    = array (
      0 => array (0 => 1, 1 => 'asc'),
    );

    $this->render_view($this->module['view'] .'/index');
  }

  public function index_data_source()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'index') === FALSE){
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {
      $entities = $this->model->getIndex();

      $data = array();
      $no   = $_POST['start'];

      foreach ($entities as $row){
        $no++;
        $col = array();
        $col[] = print_number($no);
        $col[] = print_string($row['setting_name']);
        $col[] = print_string($row['setting_value']);
        $col[] = print_date($row['updated_at']);
        $col[] = print_string($row['updated_by']);
        $col['DT_RowId'] = 'row_'. $row['id'];
        $col['DT_RowData']['pkey']  = $row['id'];
        $col['DT_RowAttr']['onClick']     = '$(this).popup();';
        $col['DT_RowAttr']['data-target'] = '#data-modal';
        $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] .'/edit/'. $row['id']);

        $data[] = $col;
      }

      $return = array(
        "draw"            => $_POST['draw'],
        "recordsTotal"    => $this->model->countIndex(),
        "recordsFiltered" => $this->model->countIndexFiltered(),
        "data"            => $data,
      );
    }

    echo json_encode($return);
  }

  public function create()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        if (is_granted($this->module, 'create') === FALSE){
            $return['type'] = 'danger';
            $return['info'] = "You don't have permission to create data!";
        } else {
          $entity = $this->model->find_all();

            $this->data['entity'] = $entity;
            $return['type'] = 'success';
            $return['info'] = $this->load->view($this->module['view'] .'/index-2', $this->data, TRUE);
        }

        echo json_encode($return);
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

    $warehouses = $this->model->findAllWarehouses();
    $this->data['warehouses'] = $warehouses;

    $setting_value = $this->model->find_by_setting_name('MAIN BASE');
    $this->data['setting_value'] = $setting_value;

    $this->data['page_content'] = $this->module['view'] .'/warehouse';
    $this->data['page_header'] = lang('page_header');
    $this->data['page_title'] = lang('page_title_warehouse');
    $this->data['page_desc'] = NULL;

    $this->render_view();
  }

  public function save()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'create') === FALSE){
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {
      $data_update = array();

      //main warehouse
      $data_update[] = array(
        'id'    => 1,
        'setting_name'    => 'MAIN BASE',
        'setting_value' => $this->input->post('main_warehouse'),
        'old_value' => $this->input->post('old_value_main_warehouse')
      );

      //expense from spd
      $data_update[] = array(
        'id'    => 7,
        'setting_name'    => 'EXPENSE from SPD',
        'setting_value' => str_replace("_"," ",strtoupper($this->input->post('expense_from_spd'))),
        'old_value' => $this->input->post('old_value_expense_from_spd')
      );

      //expense from sppd
      $data_update[] = array(
        'id'    => 8,
        'setting_name'    => 'EXPENSE from SPPD',
        'setting_value' => str_replace("_"," ",strtoupper($this->input->post('expense_from_sppd'))),
        'old_value' => $this->input->post('old_value_expense_from_sppd')
      );

      if ($this->model->update_setting($data_update)){
        $return['type'] = 'success';
        $return['info'] = 'Setting Update';
      } else {
        $return['type'] = 'danger';
        $return['info'] = 'There are error while updating data. Please try again later.';
      }
            
    }
    echo json_encode($return);
  }
}
