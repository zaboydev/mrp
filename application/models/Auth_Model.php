<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends MY_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->load->config('migration', TRUE);
  }

  public function get_auth_data($user_string)
  {
    $selected_columns = array(
      'username',
      'email',
      'auth_level',
      'passwd',
      'user_id',
      'banned'
   );

    $query = $this->db->select($selected_columns)
      ->from(config_item('user_table'))
      ->where('LOWER(username) =', strtolower($user_string))
      ->or_where('LOWER(email) =', strtolower($user_string))
      ->limit(1)
      ->get();

    if ($query->num_rows() == 1)
      return $query->row();

    return FALSE;
  }

  public function login_update($user_id, $login_time, $session_id)
  {
    if (config_item('disallow_multiple_logins') === TRUE)
    {
      $this->db->where('user_id', $user_id)
        ->delete(config_item('auth_sessions_table'));
    }

    $data = array('last_login' => $login_time);

    $this->db->where('user_id' , $user_id)
      ->update(config_item('user_table') , $data);

    $data = array(
      'id'         => $session_id,
      'user_id'    => $user_id,
      'login_time' => $login_time,
      'ip_address' => $this->input->ip_address(),
      'user_agent' => $this->_user_agent()
   );

    $this->db->insert(config_item('auth_sessions_table') , $data);
  }

  protected function _user_agent()
  {
    $this->load->library('user_agent');

    if ($this->agent->is_browser()){
      $agent = $this->agent->browser() . ' ' . $this->agent->version();
    } elseif ($this->agent->is_robot()){
      $agent = $this->agent->robot();
    } elseif ($this->agent->is_mobile()){
      $agent = $this->agent->mobile();
    } else {
      $agent = 'Unidentified User Agent';
    }

    $platform = $this->agent->platform();

    return $platform
      ? $agent . ' on ' . $platform
      : $agent;
  }

  public function check_login_status($user_id, $login_time)
  {
    $selected_columns = array(
      'u.username',
      'u.email',
      'u.auth_level',
      'u.user_id',
      'u.banned'
   );

    $this->db->select($selected_columns)
      ->from(config_item('user_table') . ' u')
      ->join(config_item('auth_sessions_table') . ' s', 'u.user_id = s.user_id')
      ->where('s.user_id', $user_id)
      ->where('s.login_time', $login_time);

    if (is_null($this->session->regenerated_session_id))
    {
      $this->db->where('s.id', $this->session->session_id);
    }

    else
    {
      $this->db->where('s.id', $this->session->pre_regenerated_session_id);
    }

    $this->db->limit(1);
    $query = $this->db->get();

    if ($query->num_rows() == 1)
      return $query->row();

    return FALSE;
  }

  public function update_user_session_id($user_id)
  {
    if (! is_null($this->session->regenerated_session_id))
    {
      $this->db->where('user_id', $user_id)
        ->where('id', $this->session->pre_regenerated_session_id)
        ->update(
          config_item('auth_sessions_table'),
          array('id' => $this->session->regenerated_session_id)
       );
    }
  }

  public function clear_expired_holds()
  {
    $expiration = date('Y-m-d H:i:s', time() - config_item('seconds_on_hold'));

    $this->db->delete(config_item('IP_hold_table'), array('time <' => $expiration));

    $this->db->delete(config_item('username_or_email_hold_table'), array('time <' => $expiration));
  }

  public function clear_login_errors()
  {
    $expiration = date('Y-m-d H:i:s', time() - config_item('seconds_on_hold'));

    $this->db->delete(config_item('errors_table'), array('time <' => $expiration));
  }

  public function check_holds($recovery)
  {
    $ip_hold = $this->check_ip_hold();

    $string_hold = $this->check_username_or_email_hold($recovery);

    if ($ip_hold === TRUE OR $string_hold === TRUE)
      return TRUE;

    return FALSE;
  }

  public function check_ip_hold()
  {
    $ip_hold = $this->db->get_where(
      config_item('IP_hold_table'),
      array('ip_address' => $this->input->ip_address())
   );

    if ($ip_hold->num_rows() > 0)
      return TRUE;

    return FALSE;
  }

  public function check_username_or_email_hold($recovery)
  {
    $posted_string = (! $recovery)
      ? $this->input->post('login_string')
      : $this->input->post('email', TRUE);

    if (! empty($posted_string) && strlen($posted_string) < 256)
    {
      $string_hold = $this->db->get_where(
        config_item('username_or_email_hold_table'),
        array('username_or_email' => $posted_string)
     );

      if ($string_hold->num_rows() > 0)
        return TRUE;
    }

    return FALSE;
  }

  public function create_login_error($data)
  {
    $this->db->set($data)
      ->insert(config_item('errors_table'));
  }

  public function check_login_attempts($string)
  {
    $ip_address = $this->input->ip_address();

    $count1 = $this->db->where('ip_address', $ip_address)
      ->count_all_results(config_item('errors_table'));

    if ($count1 == config_item('max_allowed_attempts'))
    {
      $data = array(
        'ip_address' => $ip_address,
        'time'       => date('Y-m-d H:i:s')
     );

      $this->db->set($data)
        ->insert(config_item('IP_hold_table'));
    }

    elseif (
      $count1 > config_item('max_allowed_attempts') &&
      $count1 >= config_item('deny_access_at')
   )
    {
      if (config_item('deny_access_at') > 0)
      {
        $data = array(
          'ip_address'  => $ip_address,
          'time'        => date('Y-m-d H:i:s'),
          'reason_code' => '1'
       );

        $this->_insert_denial($data);

        header('HTTP/1.1 403 Forbidden');
        die('<h1>Forbidden</h1><p>You don\'t have permission to access ANYTHING on this server.</p><hr><address>Go fly a kite!</address>');
      }
    }

    $count2 = 0;

    if ($string != '')
    {
      $count2 = $this->db->where('username_or_email', $string)
        ->count_all_results(config_item('errors_table'));

      if ($count2 == config_item('max_allowed_attempts'))
      {
        $data = array(
          'username_or_email' => $string,
          'time'              => date('Y-m-d H:i:s')
       );

        $this->db->set($data)
          ->insert(config_item('username_or_email_hold_table'));
      }
    }

    return max($count1, $count2);
  }

  public function get_deny_list($field = FALSE)
  {
    if ($field !== FALSE)
      $this->db->select($field);

    $query = $this->db->from(config_item('denied_access_table'))->get();

    if ($query->num_rows() > 0)
      return $query->result();

    return FALSE;
  }

  protected function _insert_denial($data)
  {
    if ($data['ip_address'] == '0.0.0.0')
      return FALSE;

    $this->db->set($data)
      ->insert(config_item('denied_access_table'));

    $this->_rebuild_deny_list();
  }

  protected function _remove_denial($ips)
  {
    $i = 0;

    foreach ($ips as $ip)
    {
      if ($i == 0){
        $this->db->where('ip_address', $ip);
      } else {
        $this->db->or_where('ip_address', $ip);
      }

      $i++;
    }

    $this->db->delete(config_item('denied_access_table'));

    $this->_rebuild_deny_list();
  }

  private function _rebuild_deny_list()
  {
    $query_result = $this->get_deny_list('ip_address');

    if ($query_result !== FALSE)
    {
      $deny_list = "\n" . '<Limit GET POST>' . "\n" . 'order deny,allow';

      foreach ($query_result as $row)
        $deny_list .= "\n" . 'deny from ' . $row->ip_address;

      $deny_list .= "\n" . '</Limit>' . "\n";
    }
    else
    {
      $deny_list = "\n";
    }

    $htaccess = config_item('apache_config_file_location');

    $this->load->helper('file');

    $initial_file_permissions = fileperms($htaccess);

    @chmod($htaccess, 0644);

    $string = read_file($htaccess);

    $pattern = '/(?<=# BEGIN DENY LIST --)(.|\n)*(?=# END DENY LIST --)/';

    $string = preg_replace($pattern, $deny_list, $string);

    if (! write_file($htaccess, $string))
      die('Could not write to Apache configuration file');

    @chmod($htaccess, $initial_file_permissions);
  }

  public function logout($user_id, $session_id)
  {
    $this->db->where('user_id' , $user_id)
      ->where('id', $session_id)
      ->delete(config_item('auth_sessions_table'));
  }

  public function auth_sessions_gc()
  {
    if (config_item('sess_driver') == 'database')
    {
      $this->db->query('
                DELETE a
                FROM `' . config_item('auth_sessions_table') . '` a
                LEFT JOIN `' . config_item('sessions_table') . '` b
                ON  b.id = a.id
                WHERE b.id IS NULL
            ');
    }

    if (config_item('sess_expiration') != 0)
    {
      switch($this->config->item('db_type', 'migration')){
        case 'mysql':
          $query = 'DELETE FROM `' . config_item('auth_sessions_table')
            . '` WHERE modified_at < CURDATE() - INTERVAL '
            . config_item('sess_expiration') . ' SECOND';
          break;
        case 'postgre':

          $query = 'DELETE FROM ' . config_item('auth_sessions_table')
            . ' WHERE  modified_at BETWEEN now()::timestamp - (interval \'1s\') * '
            . config_item('sess_expiration') . ' AND now()::timestamp;';

          break;
      }

      $this->db->query($query);
    }
  }
}
