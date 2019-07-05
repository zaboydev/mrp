<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Item_Category_Model extends MY_Model
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = config_item('module')['category'];
    // $this->conn   = $this->load->database('budgetcontrol', TRUE);
  }

  public function getSelectedColumns()
  {
    return array(
      'id'          => NULL,
      'category'    => 'Category',
      'code'        => 'Code',
      'notes'       => 'Notes',
      'updated_at'  => 'Last Update',
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'category',
      'code',
      'notes',
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'category',
      'code',
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
    $this->db->from('tb_master_item_categories');

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
    $this->db->from('tb_master_item_categories');

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->from('tb_master_item_categories');

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function findById($id)
  {
    $this->db->where('id', $id);

    $query = $this->db->get('tb_master_item_categories');

    return $query->row_array();
  }

  public function insert()
  {
    $this->db->trans_begin();

    $this->db->set('category', strtoupper($this->input->post('category')));
    $this->db->set('code', strtoupper($this->input->post('code')));
    $this->db->set('notes', $this->input->post('notes'));
    $this->db->set('created_by', config_item('auth_person_name'));
    $this->db->set('updated_by', config_item('auth_person_name'));
    $this->db->insert('tb_master_item_categories');

    if ($this->input->post('user')){
      foreach ($this->input->post('user') as $key => $user){
        $this->db->set('category', strtoupper($this->input->post('category')));
        $this->db->set('username', $user);
        $this->db->insert('tb_auth_user_categories');
      }
    }

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();

    $_SESSION['last_query'] = $this->db->last_query();

    return TRUE;
  }

  public function update($id)
  {
    $this->db->trans_begin();

    $this->db->select('category');
    $this->db->from('tb_master_item_categories');
    $this->db->where('id', $id);

    $query        = $this->db->get();
    $old_category = $query->unbuffered_row('array');

    $this->db->where('category', $old_category['category']);
    $this->db->delete('tb_auth_user_categories');

    $this->db->set('category', strtoupper($this->input->post('category')));
    $this->db->set('code', strtoupper($this->input->post('code')));
    $this->db->set('notes', $this->input->post('notes'));
    $this->db->set('updated_at', date('Y-m-d H:i:s'));
    $this->db->set('updated_by', config_item('auth_person_name'));
    $this->db->where('id', $id);
    $this->db->update('tb_master_item_categories');

    if ($this->input->post('user')){
      foreach ($this->input->post('user') as $key => $user){
        $this->db->set('category', strtoupper($this->input->post('category')));
        $this->db->set('username', $user);
        $this->db->insert('tb_auth_user_categories');
      }
    }

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();

    $_SESSION['last_query'] = $this->db->last_query();

    return TRUE;
  }

  public function isDuplicateCategory($category, $exception = NULL)
  {
    $this->db->select('category');
    $this->db->from('tb_master_item_categories');
    $this->db->where('UPPER(category)', strtoupper($category));

    if ($exception !== NULL)
      $this->db->where('UPPER(category) != ', strtoupper($exception));

    $query  = $this->db->get();

    return ($query->num_rows() === 0) ? FALSE : TRUE;
  }

  public function isDuplicateCode($code, $exception = NULL)
  {
    $this->db->select('code');
    $this->db->from('tb_master_item_categories');
    $this->db->where('UPPER(code)', strtoupper($code));

    if ($exception !== NULL)
      $this->db->where('UPPER(code) != ', strtoupper($exception));

    $query  = $this->db->get();

    return ($query->num_rows() === 0) ? FALSE : TRUE;
  }

  public function import(array $user_data)
  {
    $this->db->trans_begin();

    foreach ($user_data as $key => $data){
      $this->db->set('category', strtoupper($data['category']));
      $this->db->set('code', strtoupper($data['code']));
      // $this->db->set('notes', $data['notes']);
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->insert('tb_master_item_categories');
    }

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();

    return TRUE;
  }

  public function findByIds($ids)
  {
    $this->db->where_in('id', $ids);
    $query = $this->db->get('tb_master_item_categories');

    return $query->result_array();
  }

  public function delete()
  {
    $this->db->trans_begin();

    $id = $this->input->post('id');

    $this->db->where('id', $id);
    $this->db->delete('tb_master_item_categories');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();

    return TRUE;
  }
}
