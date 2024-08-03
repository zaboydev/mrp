<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Warehouse_Model extends MY_Model
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = config_item('module')['warehouse'];
  }

  public function getSelectedColumns()
  {
    return array(
      'id'                          => NULL,
      'code'                        => 'Code',
      'warehouse'                   => 'Warehouse',
      'alternate_warehouse_name'    => 'Alternate Name',
      'address'                     => 'Address',
      'updated_at'                  => 'Last Update',
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'code',
      'alternate_warehouse_name',
      'warehouse',
      'address',
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'code',
      'warehouse',
      'alternate_warehouse_name',
      'address',
      'updated_at',
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

        if (count($this->getSearchableColumns()) - 1 == $i){
          $this->db->group_end();
        }
      }

      $i++;
    }
  }

  function getIndex($return = 'array')
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_master_warehouses');

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
    $this->db->from('tb_master_warehouses');
    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->from('tb_master_warehouses');

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function findById($id)
  {
    $this->db->where('id', $id);
    $query = $this->db->get('tb_master_warehouses');

    return $query->row_array();
  }

  public function insert()
  {
    $this->db->trans_begin();

    $this->db->set('warehouse', strtoupper($this->input->post('warehouse')));
    $this->db->set('code', strtoupper($this->input->post('code')));
    $this->db->set('alternate_warehouse_name', strtoupper($this->input->post('alternate_warehouse_name')));
    $this->db->set('address', $this->input->post('address'));
    $this->db->set('notes', $this->input->post('notes'));
    $this->db->set('created_by', config_item('auth_username'));
    $this->db->set('updated_by', config_item('auth_username'));
    $this->db->insert('tb_master_warehouses');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function update($id)
  {
    $this->db->trans_begin();

    if (strtoupper($this->input->post('old_warehouse')) == config_item('main_warehouse')){
      $this->db->set('setting_value', strtoupper($this->input->post('warehouse')));
      $this->db->set('last_updated_on', date('Y-m-d H:i:s'));
      $this->db->set('last_updated_by', config_item('auth_username'));
      $this->db->where('setting_name', 'MAIN BASE');
      $this->db->update('tb_settings');
    }

    $this->db->set('warehouse', strtoupper($this->input->post('warehouse')));
    $this->db->set('code', strtoupper($this->input->post('code')));
    $this->db->set('alternate_warehouse_name', strtoupper($this->input->post('alternate_warehouse_name')));
    $this->db->set('address', $this->input->post('address'));
    $this->db->set('notes', $this->input->post('notes'));
    $this->db->set('updated_at', date('Y-m-d H:i:s'));
    $this->db->set('updated_by', config_item('auth_username'));
    $this->db->where('id', $id);
    $this->db->update('tb_master_warehouses');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function import(array $user_data)
  {
    $this->db->trans_begin();

    foreach ($user_data as $key => $data){
      $this->db->set('warehouse', strtoupper($data['warehouse']));
      $this->db->set('code', strtoupper($data['code']));
      $this->db->set('address', $data['address']);
      $this->db->set('notes', $data['notes']);
      $this->db->set('created_by', config_item('auth_username'));
      $this->db->set('updated_by', config_item('auth_username'));
      $this->db->insert('tb_master_warehouses');
    }

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function findByIds($ids)
  {
    $this->db->where_in('id', $ids);
    $query = $this->db->get('tb_master_warehouses');

    return $query->result_array();
  }

  public function delete()
  {
    $this->db->trans_begin();

    $id = $this->input->post('id');

    $this->db->where('id', $id);
    $this->db->delete('tb_master_warehouses');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }
}
