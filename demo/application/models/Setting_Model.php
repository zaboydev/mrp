<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_Model extends MY_Model
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = config_item('setting_table');
  }

  public function find_by_setting_name($setting_name)
  {
    $this->db->select('setting_value');
    $this->db->where('setting_name', $setting_name);
    $query = $this->db->get($this->module['table']);
    $row = $query->row_array();

    return $row['setting_value'];
  }

  public function findAllWarehouses('AVAILABLE')
  {
    $this->db->order_by('warehouse', 'asc');
    $this->db->where('status', 'AVAILABLE');
    $query = $this->db->get(config_item('module')['warehouse']['table']);

    return $query->result_array();
  }

  public function update($setting_name)
  {
    $this->db->trans_begin();

    $this->db->set('setting_value', $this->input->post('setting_value'))
      ->where('setting_name', $setting_name)
      ->update('tb_settings');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    
    return TRUE;
  }
}
