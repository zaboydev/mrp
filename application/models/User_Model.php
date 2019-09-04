<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends MY_Model
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = config_item('module')['user'];
  }

  public function getSelectedColumns()
  {
    return array(
      'user_id'     => NULL,
      'person_name' => 'Person Name',
      'auth_level'  => 'Role',
      'warehouse'   => 'Warehouse',
      'username'    => 'Username',
      'email'       => 'Email',
      'banned'      => 'Status',
      'last_login'  => 'Last Login',
      'modified_at' => 'Last Update',
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'person_name',
      'warehouse',
      'username',
      'email',
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'person_name',
      'auth_level',
      'warehouse',
      'username',
      'email',
      'banned',
      'last_login',
      'modified_at',
    );
  }

  private function searchIndex()
  {
    if (!empty($_POST['columns'][2]['search']['value'])){
      $search_level = $_POST['columns'][2]['search']['value'];
      $this->db->where('auth_level', $search_level);
    }

    if (!empty($_POST['columns'][6]['search']['value'])){
      $search_banned = $_POST['columns'][6]['search']['value'];
      $this->db->where('banned', $search_banned);
    }

    if (!empty($_POST['columns'][7]['search']['value'])){
      $search_last_login = $_POST['columns'][7]['search']['value'];
      $range_last_login = explode(' ', $search_last_login);

      $this->db->where('last_login >= ', $range_last_login[0]);
      $this->db->where('last_login <= ', $range_last_login[1]);
    }

    if (!empty($_POST['columns'][8]['search']['value'])){
      $search_last_update = $_POST['columns'][8]['search']['value'];
      $range_last_update = explode(' ', $search_last_update);

      $this->db->where('modified_at >= ', $range_last_update[0]);
      $this->db->where('modified_at <= ', $range_last_update[1]);
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
    $this->db->from('tb_auth_users');

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
    $this->db->from('tb_auth_users');

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->from('tb_auth_users');

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function findOneBy($criteria)
  {
    $this->db->from('tb_auth_users');
    $this->db->where($criteria);

    $query = $this->db->get();

    return $query->unbuffered_row('array');
  }

  public function find_user_info($criteria, $select = '*', $array = FALSE)
  {
    $this->db->select($select);
    $this->db->from('tb_auth_users');
    $this->db->where($criteria);

    $query = $this->db->get();

    if ($array)
      return $query->row_array();

    return $query->row();
  }

  public function get_unused_id(array $except = null)
  {
    $random_unique_int = 2147483648 + mt_rand(-2147482448, 2147483647);

    if ($except !== null){
      if (in_array($random_unique_int, $except))
        return $this->get_unused_id();
    }

    $query = $this->db->where('user_id', $random_unique_int)
      ->get_where('tb_auth_users');

    if ($query->num_rows() > 0)
    {
      $query->free_result();

      return $this->get_unused_id();
    }

    return $random_unique_int;
  }

  public function _check_password_strength($password)
  {
    $regex = '(?=.{' . config_item('min_chars_for_password') . ',' . config_item('max_chars_for_password') . '})';
    $error = '<li>At least ' . config_item('min_chars_for_password') . ' characters</li>
          <li>Not more than ' . config_item('max_chars_for_password') . ' characters</li>';

    $regex .= '(?=.*\d)';
    $error .= '<li>One number</li>';

    $regex .= '(?=.*[a-z])';
    $error .= '<li>One lower case letter</li>';

    $regex .= '(?=.*[A-Z])';
    $error .= '<li>One upper case letter</li>';

    $regex .= '(?!.*\s)';
    $error .= '<li>No spaces, tabs, or other unseen characters</li>';

    $regex .= '(?!.*[\\\\\'"])';
    $error .= '<li>No backslash, apostrophe or quote characters</li>';

    // $regex .= '(?=.*[@#$%^&+=])';
    // $error .= '<li>One of the following characters must be in the password,  @ # $ % ^ & + =</li>';

    if (preg_match('/^' . $regex . '.*$/', $password, $matches))
    {
      return TRUE;
    }

    $this->form_validation->set_message(
      '_check_password_strength',
      '<span class="redfield">Password</span> must contain:
        <ol>
          ' . $error . '
        </ol>
      </span>'
   );

    return FALSE;
  }

  public function check_person_name($data = null){
    if ($data === null or trim($data) === '' or strlen($data) > 100)
      return false;

    return $data;
  }

  public function check_username($data = null){
    if ($data === null or trim($data) === '' or strlen($data) > 30)
      return false;

    if (preg_match('/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/',$data) === false)
      return false;

    if ($this->findOneBy(array('username' => $data), true))
      return false;

    return $data;
  }

  public function check_email($data = null){
    if ($data === null or trim($data) === '' or strlen($data) > 255)
      return false;

    if (filter_var($data, FILTER_VALIDATE_EMAIL) === false)
      return false;

    if ($this->findOneBy(array('email' => $data), true))
      return false;

    return $data;
  }

  public function check_password($data = null){
    if ($data === null or trim($data) === '' or strlen($data) > 75)
      return false;

    return $this->_check_password_strength($data);
  }

  public function check_role($data = null){
    if ($data === null or trim($data) === '' or strlen($data) > 20)
      return false;

    if (in_array($data,config_item('levels_and_roles')) === false)
      return false;

    $level = array_keys(config_item('levels_and_roles'), $data);

    return $level[0];
  }

  public function insert(array $user_data)
  {
    $this->db->trans_begin();

    $this->db->set($user_data);
    $this->db->insert('tb_auth_users');

    if ($this->db->trans_status() === FALSE)
        return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function update(array $user_data, array $criteria)
  {
      $this->db->trans_begin();

      $this->db->set($user_data);
      $this->db->where($criteria);
      $this->db->update('tb_auth_users');

      if ($this->db->trans_status() === FALSE)
          return FALSE;

      $this->db->trans_commit();
      return TRUE;
  }

  public function findByIds($ids)
  {
    $this->db->where_in('user_id', $ids);
    $query = $this->db->get('tb_auth_users');

    return $query->result_array();
  }

  public function delete(){
    $this->db->trans_begin();

    $id = $this->input->post('id');

    $this->db->where('user_id', $id);
    $this->db->delete('tb_auth_users');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function listAttachment_2($id){
    // $this->db->select('ttd_user');
    $this->db->where('user_id', $id);
    return $this->db->get('tb_auth_users')->result_array();
  }

  function add_attachment_to_db($user_id,$url){
    $this->db->trans_begin();

    $this->db->set('ttd_user', $url);
    // $this->db->set('file', $url);
    $this->db->where('user_id',$user_id);
    $this->db->update('tb_auth_users');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }
}
