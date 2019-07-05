<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Controller $controller
 * @property $data array
 *
 */
class Dashboard extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['dashboard'];

    $this->load->model($this->module['model'], 'model');

    $this->data['module'] = $this->module;
  }

  public function index()
  {
    $this->authorized($this->module, 'index');

    $this->load->helper('stock');
    $start_date  = date('Y-m-d');
    $date        = strtotime('+90 day',strtotime($start_date));
    $end_date    = date('Y-m-d', $date);

    $this->data['page_title'] = 'Welcome, in '. ucwords(config_item('auth_warehouse'));
    $this->data['json_description'] = $this->_model->search_stock_in_stores();

    $this->data['count_adjustmnet']    = $this->model->countAdjustment();
    $this->data['count_expired_stock'] = $this->model->countExpiredStock($start_date,$end_date);
    $this->data['expired_stock']       = $this->model->expiredStock($start_date,$end_date);

    $this->base_theme = $this->module['view'] .'/other';
    $this->render_view();
  }

  public function close_period()
  {
    $this->require_min_level(1);

    $this->db->where('period', $this->input->post('period'));
    $this->db->where('year_number', date('Y'));
    $query = $this->db->get('tb_periods');

    if ($query->num_rows() > 0){
      $updated = $query->unbuffered_row();

      $this->db->set('start_date', $this->input->post('start'));
      $this->db->set('end_date', $this->input->post('end'));
      $this->db->set('updated_by', config_item('auth_username'));
      $this->db->where('id', $updated->id);

      if ($this->db->update('tb_periods')){
        $this->session->set_flashdata('alert', array(
          'type' => 'success',
          'info' => 'Close period updated.'
       ));
      }
    } else {
      $this->db->set('period', $this->input->post('period'));
      $this->db->set('year_number', date('Y'));
      $this->db->set('start_date', $this->input->post('start'));
      $this->db->set('end_date', $this->input->post('end'));
      $this->db->set('created_by', config_item('auth_username'));
      $this->db->set('updated_by', config_item('auth_username'));

      if ($this->db->insert('tb_periods')){
        $this->session->set_flashdata('alert', array(
          'type' => 'success',
          'info' => 'Period closed.'
       ));
      }
    }

    redirect(site_url());
  }

  public function ajax_find_item_in_stores()
  {
    $term = $_POST['item_key'];
    $warehouse = $_POST['warehouse'];

    $entities = $this->_model->find_item_in_stores($warehouse, $term);

    foreach ($entities as $key => $value){
      $item_serial = ($value['item_serial'] == '') ? '' : ' S/N: '. $value['item_serial'];

      $entities[$key]['label'] = $value['description'];
      $entities[$key]['label'] .= ' P/N: '. $value['part_number'];
      $entities[$key]['label'] .= $item_serial;
    }

    echo json_encode($entities);
  }
}
