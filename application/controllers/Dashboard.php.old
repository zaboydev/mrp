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

    $this->data['page_title'] = 'Welcome, in '. ucwords(config_item('auth_warehouse'));

    // if (config_item('auth_role') == 'ADMIN'){
    //   $this->base_theme = $this->module['view'] .'/administrator';
    // } elseif (config_item('auth_role') == 'PIC PROCUREMENT'){
    //   $this->base_theme = $this->module['view'] .'/pic_procurement';
    // } else {
    //   $this->data['total_receipts']         = $this->model->countReceipts();
    //   $this->data['total_issuances']        = $this->model->countIssuances();
    //   $this->data['total_in_stores_items']  = $this->model->countStockInStores();
    //   $this->data['total_low_stock_items']  = $this->model->countLowStockItems();
    //
    //   if (config_item('auth_role') == 'PIC STOCK'){
    //     $this->base_theme = $this->module['view'] .'/other';
    //     // $this->base_theme = $this->module['view'] .'/pic_inventory';
    //   } else {
    //     $this->base_theme = $this->module['view'] .'/other';
    //   }
    // }

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
