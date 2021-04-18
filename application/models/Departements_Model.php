<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Departements_Model extends MY_Model
{
  protected $module;
  protected $connection;
  // protected $categories;
  // protected $budget_year;
  // protected $budget_month;

  public function __construct()
  {
    parent::__construct();

    $this->module = config_item('module')['departements'];
    $this->connection   = $this->load->database('budgetcontrol', TRUE);
    // $this->categories   = $this->getCategories();
    // $this->budget_year  = find_budget_setting('Active Year');
    // $this->budget_month = find_budget_setting('Active Month');
  }

  public function getSelectedColumns()
  {
    return array(
      'tb_departments.id'          => NULL,
      'tb_divisions.division_name'        => 'Division Name',
      'tb_departments.department_name'        => 'Department Name',
      'tb_departments.department_code' => 'Department Code',
      'tb_departments.updated_at'  => 'Last Update',
      'tb_departments.updated_by'  => 'Last Update By',
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'tb_divisions.division_name',
      'tb_departments.department_name',
      'tb_departments.department_code',
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'tb_divisions.division_name',
      'tb_departments.department_name',
      'tb_departments.department_code',
      'tb_departments.updated_at',
      'tb_departments.updated_by',
    );
  }

  private function searchIndex()
  {
    $i = 0;

    foreach ($this->getSearchableColumns() as $item){
      if ($_POST['search']['value']){
        if ($i === 0){
          $this->connection->group_start();
          $this->connection->like('UPPER('.$item.')', strtoupper($_POST['search']['value']));
        } else {
          $this->connection->or_like('UPPER('.$item.')', strtoupper($_POST['search']['value']));
        }

        if (count($this->getSearchableColumns()) - 1 == $i)
          $this->connection->group_end();
      }

      $i++;
    }
  }

  function getIndex($return = 'array')
  {
    $this->connection->select(array_keys($this->getSelectedColumns()));
    $this->connection->from('tb_departments');
    $this->connection->join('tb_divisions','tb_divisions.id=tb_departments.division_id');

    $this->searchIndex();

    $column_order = $this->getOrderableColumns();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->connection->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->connection->order_by('id', 'desc');
    }

    if ($_POST['length'] != -1)
      $this->connection->limit($_POST['length'], $_POST['start']);

    $query = $this->connection->get();

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
    $this->connection->from('tb_departments');
    $this->connection->join('tb_divisions','tb_divisions.id=tb_departments.division_id');

    $this->searchIndex();

    $query = $this->connection->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->connection->from('tb_departments');
    $this->connection->join('tb_divisions','tb_divisions.id=tb_departments.division_id');

    $query = $this->connection->get();

    return $query->num_rows();
  }

  public function findById($id)
  {
    $this->connection->where('id', $id);
    $query = $this->connection->get('tb_departments');

    return $query->row_array();
  }

  public function insert()
  {
    $this->connection->trans_begin();

    $this->connection->set('department_name', strtoupper($this->input->post('department_name')));
    $this->connection->set('department_code', $this->input->post('department_code'));
    $this->connection->set('notes', $this->input->post('notes'));
    $this->connection->set('division_id', $this->input->post('division_id'));
    $this->connection->set('created_by', config_item('auth_person_name'));
    $this->connection->set('created_at', date('Y-m-d H:i:s'));
    $this->connection->set('updated_at', date('Y-m-d H:i:s'));
    $this->connection->set('updated_by', config_item('auth_person_name'));
    $this->connection->insert('tb_departments');

    if ($this->input->post('head_department')){
      $this->db->set('department_id',$id);
      $this->db->set('username',$this->input->post('head_department'));
      $this->db->set('status','active');
      $this->db->set('updated_at', date('Y-m-d H:i:s'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->insert('tb_head_department');

    }

    if ($this->connection->trans_status() === FALSE)
      return FALSE;

    $this->connection->trans_commit();
    return TRUE;
  }

  public function update($id)
  {
    $this->connection->trans_begin();

    $this->connection->set('department_name', strtoupper($this->input->post('department_name')));
    $this->connection->set('department_code', $this->input->post('department_code'));
    $this->connection->set('notes', $this->input->post('notes'));
    $this->connection->set('division_id', $this->input->post('division_id'));
    $this->connection->set('updated_at', date('Y-m-d H:i:s'));
    $this->connection->set('updated_by', config_item('auth_person_name'));
    $this->connection->where('id', $id);
    $this->connection->update('tb_departments');

    if ($this->input->post('head_department')){
      $this->db->set('status','not active');
      $this->db->where('department_id', $id);
      $this->db->where('status', 'active');
      $this->db->update('tb_head_department');

      $this->db->set('department_id',$id);
      $this->db->set('username',$this->input->post('head_department'));
      $this->db->set('status','active');
      $this->db->set('updated_at', date('Y-m-d H:i:s'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->insert('tb_head_department');

    }

    if ($this->connection->trans_status() === FALSE)
      return FALSE;

    $this->connection->trans_commit();
    return TRUE;
  }

  public function import(array $user_data)
  {
    $this->connection->trans_begin();

    foreach ($user_data as $key => $data){
      $this->connection->set('unit', strtoupper($data['unit']));
      $this->connection->set('description', $data['description']);
      $this->connection->set('created_by', config_item('auth_person_name'));
      $this->connection->set('updated_by', config_item('auth_person_name'));
      $this->connection->insert('tb_departments');
    }

    if ($this->connection->trans_status() === FALSE)
      return FALSE;

    $this->connection->trans_commit();
    return TRUE;
  }

  public function findByIds($ids)
  {
    $this->connection->where_in('id', $ids);
    $query = $this->connection->get('tb_departments');

    return $query->result_array();
  }

  public function delete()
  {
    $this->connection->trans_begin();

    $id = $this->input->post('id');

    $this->connection->where('id', $id);
    $this->connection->delete('tb_departments');

    if ($this->connection->trans_status() === FALSE)
      return FALSE;

    $this->connection->trans_commit();
    return TRUE;
  }

  public function isDepartmentNameExists($department_name, $department_name_exception = NULL)
  {
    $this->connection->from('tb_departments');
    $this->connection->where('UPPER(department_name)', strtoupper($department_name));

    if ($department_name_exception !== NULL)
      $this->connection->where('UPPER(department_name) != ', strtoupper($department_name_exception));

    $query = $this->connection->get();

    return ( $query->num_rows() > 0 ) ? true : false;
  }

  public function isDepartmentCodeExists($department_code,$department_code_exception = NULL)
  {
    $this->connection->from('tb_departments');
    $this->connection->where('UPPER(department_code)', strtoupper($department_code));

    if ($department_code_exception !== NULL)
      $this->connection->where('UPPER(department_code) != ', $department_code_exception);

    $query = $this->connection->get();

    return ( $query->num_rows() > 0 ) ? true : false;
  }
}
