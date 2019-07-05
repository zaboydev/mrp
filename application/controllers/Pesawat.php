<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pesawat extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['pesawat'];
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function index()
  {
    $this->authorized($this->module, 'index');

    $this->data['page']['title']            = 'Pesawat';
    $this->data['page']['requirement']      = array('datatable', 'form_create', 'form_edit');
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source');
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = NULL;
    $this->data['grid']['order_columns']    = array (
      // 0 => array ( 0 => 1, 1 => 'asc' ),
      // 1 => array ( 0 => 2, 1 => 'asc' ),
      // 2 => array ( 0 => 3, 1 => 'desc' ),
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
        $col[] = print_string($row['nama_pesawat']);
        $col[] = print_string($row['keterangan']);
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
      $return['type'] = 'success';
      $return['info'] = $this->load->view($this->module['view'] .'/tambah', $this->data, TRUE);
    }

    echo json_encode($return);
  }

  public function edit($id)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'edit') === FALSE){
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to edit this data!";
    } else {
      $entity = $this->model->findById($id);

      $this->data['entity'] = $entity;

      $return['type'] = 'success';
      $return['info'] = $this->load->view($this->module['view'] .'/edit', $this->data, TRUE);
    }

    echo json_encode($return);
  }

  public function save()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'save') === FALSE){
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {
      if ($this->input->post('id')){
          if ($this->model->update($this->input->post('id'))){
            $return['type'] = 'success';
            $return['info'] = print_string($this->input->post('nama_pesawat')).' updated';
          } else {
            $return['type'] = 'danger';
            $return['info'] = 'There are error while updating data. Please try again later.';
          }
      } else {
        if ($this->model->isPesawatExists($this->input->post('nama_pesawat'))){
          $return['type'] = 'danger';
          $return['info'] = 'Duplicate Airplane Name!'.$this->input->post('nama_pesawat').' already exists';
        } else {
          if ($this->model->insert()){
            $return['type'] = 'success';
            $return['info'] = print_string($this->input->post('nama_pesawat')).' created';
          } else {
            $return['type'] = 'danger';
            $return['info'] = 'There are error while updating data. Please try again later.';
          }
        }
      }
    }

    echo json_encode($return);
  }

  public function delete()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'delete') === FALSE){
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to delete this data!";
    } else {
      if ($this->model->delete()){
        $return['type'] = 'success';
        $return['info'] = 'Pesawat ' . $this->input->post('nama_pesawat') .' deleted.';
      } else {
        $return['type'] = 'danger';
        $return['info'] = 'There are error while trying to delete data. Please try again later.';
      }
    }

    echo json_encode($return);
  }
}
