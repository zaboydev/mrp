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

  public function find_all()
  {
    $this->db->select('*');
    $this->db->where_in('tb_settings.setting_group',['GENERAL','HRD']);
    $this->db->order_by('tb_settings.id', 'ASC');
    $query = $this->db->get('tb_settings');
    $row = $query->result_array();

    return $row;
  }

  public function findAllWarehouses($status = 'AVAILABLE')
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

  public function update_setting(array $user_data)
  {
    $this->db->trans_begin();

    foreach ($user_data as $key => $data){
      $this->db->set('setting_value', $data['setting_value']);
      $this->db->set('updated_at', date('Y-m-d H:i:s'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->where('id', $data['id']);
      $this->db->update('tb_settings');

      if($data['setting_value']!=$data['old_value']){
        $this->db->set('setting_name', $data['setting_name']);
        $this->db->set('old_value', $data['old_value']);
        $this->db->set('new_value', $data['setting_value']);
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->insert('tb_history_setting');
      }
    }

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();

    return TRUE;
  }

  public function getSelectedColumns()
  {
    return array(
      'No',
      'Setting Name',
      'Setting Value',
      'Last Update',
      'Updated by'
    );
  }

  public function getSearchableColumns()
    {
        return array(
            'setting_name',
            'setting_value',
            'updated_by'
        );
    }

    public function getOrderableColumns()
    {
        return array(
            null,
        );
    }

    private function searchIndex()
    {

        $i = 0;

        foreach ($this->getSearchableColumns() as $item){
            if ($_POST['search']['value']){
                if ($i === 0){
                $this->db->group_start();
                $this->db->like('UPPER('.$item.')', strtoupper($_POST['search']['value']));
                } else {
                $this->db->or_like('UPPER('.$item.')', strtoupper($_POST['search']['value']));
                }

                if (count($this->getSearchableColumns()) - 1 == $i)
                $this->db->group_end();
            }

            $i++;
        }
    }

    function getIndex($return = 'array')
    {
        $this->db->select('tb_settings.*');
        $this->db->from('tb_settings');
        $this->db->where_in('tb_settings.setting_group',['HRD']);

        $this->searchIndex();

        $column_order = $this->getOrderableColumns();

        if (isset($_POST['order'])){
            foreach ($_POST['order'] as $key => $order){
                $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
            }
        } else {
            $this->db->order_by('id', 'desc');
        }

        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);

        $query = $this->db->get();

        if ($return === 'object'){
            return $query->result();
        } elseif ($return === 'json'){
            return json_encode($query->result());
        } else {
            return $query->result_array();
        }
    }

    function countIndexFiltered()
    {        
      $this->db->select('tb_settings.*');
      $this->db->from('tb_settings');
      $this->db->where_in('tb_settings.setting_group',['HRD']);

        $this->searchIndex();

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function countIndex()
    {
      $this->db->select('tb_settings.*');
      $this->db->from('tb_settings');
      $this->db->where_in('tb_settings.setting_group',['HRD']);
        $query = $this->db->get();

        return $query->num_rows();
    }
}
