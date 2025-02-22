<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Annual_Cost_Centers_Model extends MY_Model
{
  protected $module;
  protected $connection;
  // protected $categories;
  protected $budget_year;
  // protected $budget_month;

  public function __construct()
  {
    parent::__construct();

    // $this->module = config_item('module')['annual_cost_centers'];
    $this->connection   = $this->load->database('budgetcontrol', TRUE);
    // $this->categories   = $this->getCategories();
    $this->budget_year  = $this->find_budget_setting('Active Year');
    // $this->budget_month = find_budget_setting('Active Month');
  }

  public function getSelectedColumns()
  {
    return array(
      'tb_annual_cost_centers.id'               => NULL,
      'tb_cost_center_groups.group_name'        => 'Group Name',
      'tb_cost_centers.cost_center_code'        => 'Cost Center Code',
      'tb_cost_centers.cost_center_name'        => 'Cost Center Name',
      'tb_departments.department_name'          => 'Department Name',
      'tb_annual_cost_centers.registered_on'               => 'Register On',
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'tb_cost_center_groups.group_name',
      'tb_cost_centers.cost_center_name',
      'tb_departments.department_name'
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'tb_cost_center_groups.group_name',
      'tb_cost_centers.cost_center_code',
      'tb_cost_centers.cost_center_name',
      'tb_departments.department_name',
      'tb_annual_cost_centers.registered_on',
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

  function find_budget_setting($name)
  {

    $this->connection->from('tb_settings');
    $this->connection->where('setting_name', $name);

    $query    = $this->connection->get();
    $setting  = $query->unbuffered_row('array');
    $return   = $setting['setting_value'];

    return $return;
  }

  function getIndex($return = 'array')
  {
    $this->connection->select(array_keys($this->getSelectedColumns()));
    $this->connection->from('tb_annual_cost_centers');
    $this->connection->join('tb_cost_centers','tb_cost_centers.id=tb_annual_cost_centers.cost_center_id');
    $this->connection->join('tb_cost_center_groups','tb_cost_center_groups.id=tb_cost_centers.group_id');
    $this->connection->join('tb_departments','tb_departments.id=tb_cost_centers.department_id');
    $this->connection->where('year_number',$this->budget_year);

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
    // $this->connection->from('tb_departments');
    $this->connection->from('tb_annual_cost_centers');
    $this->connection->join('tb_cost_centers','tb_cost_centers.id=tb_annual_cost_centers.cost_center_id');
    $this->connection->join('tb_cost_center_groups','tb_cost_center_groups.id=tb_cost_centers.group_id');
    $this->connection->join('tb_departments','tb_departments.id=tb_cost_centers.department_id');
    $this->connection->where('year_number',$this->budget_year);

    $this->searchIndex();

    $query = $this->connection->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    // $this->connection->from('tb_departments');
    $this->connection->from('tb_annual_cost_centers');
    $this->connection->join('tb_cost_centers','tb_cost_centers.id=tb_annual_cost_centers.cost_center_id');
    $this->connection->join('tb_cost_center_groups','tb_cost_center_groups.id=tb_cost_centers.group_id');
    $this->connection->join('tb_departments','tb_departments.id=tb_cost_centers.department_id');
    $this->connection->where('year_number',$this->budget_year);

    $query = $this->connection->get();

    return $query->num_rows();
  }

  public function findById($id)
  {
    $this->connection->select(array('tb_annual_cost_centers.*','tb_cost_centers.cost_center_name'));
    $this->connection->where('tb_annual_cost_centers.id', $id);
    $this->connection->join('tb_cost_centers','tb_cost_centers.id=tb_annual_cost_centers.cost_center_id');
    $query = $this->connection->get('tb_annual_cost_centers');

    return $query->row_array();
  }

  public function insert()
  {
    $this->connection->trans_begin();

    $year = $this->input->post('year');
    $this->connection->where('year_number', $year);
    $this->connection->delete('tb_annual_cost_centers');
    
    if ($this->input->post('cost_center_id')){
      foreach ($this->input->post('cost_center_id') as $key => $cost_center_id){
        $this->connection->set('cost_center_id', $cost_center_id);
        $this->connection->set('year_number', $year);
        $this->connection->insert('tb_annual_cost_centers');
      }
    }

    if ($this->connection->trans_status() === FALSE)
      return FALSE;

    $this->connection->trans_commit();
    return TRUE;
  }

  public function update($id)
  {
    $this->connection->trans_begin();

    $this->connection->where('annual_cost_center_id', $id);
    $this->connection->delete('tb_users_mrp_in_annual_cost_centers');

    if ($this->input->post('user')){
      foreach ($this->input->post('user') as $key => $user){
        $this->connection->set('annual_cost_center_id', $id);
        $this->connection->set('username', $user);
        $this->connection->insert('tb_users_mrp_in_annual_cost_centers');
      }

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
