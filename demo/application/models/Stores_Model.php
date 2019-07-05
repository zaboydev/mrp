<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stores_Model extends MY_Model
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = config_item('module')['stores'];
  }

  public function getSelectedColumns()
  {
    return array(
      'id'          => NULL,
      'stores'      => 'Stores',
      'warehouse'   => 'Warehouse',
      'category'    => 'Category',
      'notes'       => 'Notes',
      'updated_at'  => 'Last Update',
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'stores',
      'warehouse',
      'category',
      'notes',
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'stores',
      'warehouse',
      'category',
      'notes',
      'updated_at',
    );
  }

  private function searchIndex()
  {
    if (!empty($_POST['columns'][2]['search']['value'])){
      $search_warehouse = $_POST['columns'][2]['search']['value'];

      $this->db->where('warehouse', $search_warehouse);
    }

    if (!empty($_POST['columns'][3]['search']['value'])){
      $search_item_category = $_POST['columns'][3]['search']['value'];

      $this->db->where('category', $search_item_category);
    }

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
    $this->db->from('tb_master_stores');
    $this->db->where_in('warehouse', config_item('auth_warehouses'));
    $this->db->where_in('category', config_item('auth_inventory'));

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
    $this->db->from('tb_master_stores');
    $this->db->where_in('warehouse', config_item('auth_warehouses'));
    $this->db->where_in('category', config_item('auth_inventory'));

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->from('tb_master_stores');
    $this->db->where_in('warehouse', config_item('auth_warehouses'));
    $this->db->where_in('category', config_item('auth_inventory'));

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function findById($id)
  {
    $this->db->from('tb_master_stores');
    $this->db->where('id', $id);
    $query = $this->db->get();

    return $query->row_array();
  }

  public function isDuplicateStores($exception = NULL)
  {
    $stores = strtoupper($this->input->post('stores'));

    $this->db->select('stores');
    $this->db->from('tb_master_stores');
    $this->db->where('UPPER(stores)', $stores);

    if ($exception !== NULL)
      $this->db->where('UPPER(stores) != ', strtoupper($exception));

    $query  = $this->db->get();

    return ($query->num_rows() === 0) ? FALSE : TRUE;
  }

  public function insert()
  {
    $this->db->trans_begin();

    $this->db->set('stores', strtoupper($this->input->post('stores')));
    $this->db->set('warehouse', strtoupper($this->input->post('warehouse')));
    $this->db->set('category', strtoupper($this->input->post('category')));
    $this->db->set('notes', $this->input->post('notes'));
    $this->db->set('created_at', date('Y-m-d H:i:s'));
    $this->db->set('created_by', config_item('auth_person_name'));
    $this->db->set('updated_at', date('Y-m-d H:i:s'));
    $this->db->set('updated_by', config_item('auth_person_name'));
    $this->db->insert('tb_master_stores');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();

    return TRUE;
  }

  public function update($id)
  {
    $this->db->trans_begin();

    $this->db->set('stores', strtoupper($this->input->post('stores')));
    $this->db->set('warehouse', strtoupper($this->input->post('warehouse')));
    $this->db->set('category', strtoupper($this->input->post('category')));
    $this->db->set('notes', $this->input->post('notes'));
    $this->db->set('updated_at', date('Y-m-d H:i:s'));
    $this->db->set('updated_by', config_item('auth_person_name'));
    $this->db->where('id', $id);
    $this->db->update('tb_master_stores');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function import(array $user_data)
  {
    $this->db->trans_begin();

    foreach ($user_data as $key => $data){
      $entity = array(
        'warehouse'         => $data['warehouse'],
        'stores'            => $data['stores'],
        'category'          => $data['category'],
        'notes'             => $data['notes'],
        'created_at'        => date('Y-m-d H:i:s'),
        'created_by'        => $this->auth_person_name,
        'updated_at'        => date('Y-m-d H:i:s'),
        'updated_by'        => $this->auth_person_name,
     );

      $this->db->insert('tb_master_stores', $entity);
    }

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function findByIds($ids)
  {
    $this->db->from('tb_master_stores');
    $this->db->where_in('id', $ids);
    $query = $this->db->get();

    return $query->result_array();
  }

  public function delete()
  {
    $this->db->trans_begin();

    $id = $this->input->post('id');

    $this->db->where('id', $id);
    $this->db->delete('tb_master_stores');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }
}
