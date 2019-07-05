<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Item_Unit_Model extends MY_Model
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = config_item('module')['item_unit'];
  }

  public function getSelectedColumns()
  {
    return array(
      'id'          => NULL,
      'unit'        => 'Unit',
      'description' => 'Description',
      'notes'       => 'Notes',
      'updated_at'  => 'Last Update',
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'unit',
      'description',
      'notes',
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'unit',
      'description',
      'notes',
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

        if (count($this->getSearchableColumns()) - 1 == $i)
          $this->db->group_end();
      }

      $i++;
    }
  }

  function getIndex($return = 'array')
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_master_item_units');

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
    $this->db->from('tb_master_item_units');

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->from('tb_master_item_units');

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function findById($id)
  {
    $this->db->where('id', $id);
    $query = $this->db->get('tb_master_item_units');

    return $query->row_array();
  }

  public function insert()
  {
    $this->db->trans_begin();

    $this->db->set('unit', strtoupper($this->input->post('unit')));
    $this->db->set('description', $this->input->post('description'));
    $this->db->set('notes', $this->input->post('notes'));
    $this->db->set('created_by', config_item('auth_person_name'));
    $this->db->set('updated_by', config_item('auth_person_name'));
    $this->db->insert('tb_master_item_units');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function update($id)
  {
    $this->db->trans_begin();

    $this->db->set('unit', strtoupper($this->input->post('unit')));
    $this->db->set('description', $this->input->post('description'));
    $this->db->set('notes', $this->input->post('notes'));
    $this->db->set('updated_at', date('Y-m-d H:i:s'));
    $this->db->set('updated_by', config_item('auth_person_name'));
    $this->db->where('id', $id);
    $this->db->update('tb_master_item_units');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function import(array $user_data)
  {
    $this->db->trans_begin();

    foreach ($user_data as $key => $data){
      $this->db->set('unit', strtoupper($data['unit']));
      $this->db->set('description', $data['description']);
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->insert('tb_master_item_units');
    }

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function findByIds($ids)
  {
    $this->db->where_in('id', $ids);
    $query = $this->db->get('tb_master_item_units');

    return $query->result_array();
  }

  public function delete()
  {
    $this->db->trans_begin();

    $id = $this->input->post('id');

    $this->db->where('id', $id);
    $this->db->delete('tb_master_item_units');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }
}
