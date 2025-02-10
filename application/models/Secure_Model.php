<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Secure_model extends MY_Model
{
  public function __construct()
  {
    parent::__construct();

    $this->load->config('migration', TRUE);
  }

  public function check_passwd( $hash, $password )
  {
    if( is_php('5.5') && password_verify( $password, $hash ) ){
      return TRUE;
    }else if( $hash === crypt( $password, $hash ) ){
      return TRUE;
    }

    return FALSE;
  }

  public function authentication($identity, $data = FALSE)
  {
    $this->db->from('tb_auth_users');
    $this->db->where('username', $identity);
    $this->db->or_where('email', $identity);
    // $this->db->where('passwd', $encrypt);

    $query  = $this->db->get();

    return ($query->num_rows() > 0)
      ? (($data === FALSE) ? TRUE : $query->unbuffered_row('array'))
      : FALSE;
  }

  public function authorized($identity, $password)
  {
    $authentication = $this->authentication($identity, TRUE);

    if ($authentication === FALSE){
      return FALSE;
    }

    return ($authentication === FALSE || $this->check_passwd($authentication['passwd'], $password) === FALSE || $authentication['banned'] == 1)
      ? FALSE
      : TRUE;

    $this->db->from('tb_auth_users');
    $this->db->where('username', $identity);
    $this->db->or_where('email', $identity);

    $query  = $this->db->get();

    if ($query->num_rows() > 0){
      return $query->unbuffered_row('array');
    } else {
      return FALSE;
    }
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

  public function send_email_contract($employees){
    $this->load->library('email');
    $this->email->set_newline("\r\n");
      if (!empty($employees)) {
       
        $recipient = array();
        
        array_push($recipient, 'andrio.zaboy@gmail.com');
        

        $message = "<h3>Notifikasi Kontrak Karyawan</h3>";
        $message .= "<p>Berikut daftar karyawan yang kontraknya akan berakhir dalam 20 hari:</p>";
        $message .= "<table border='1' cellpadding='5' cellspacing='0'>";
        $message .= "<tr><th>Employee Number</th><th>Name</th><th>Contract Number</th><th>End Date</th></tr>";

        foreach ($employees as $emp) {
            $message .= "<tr>
                <td>{$emp->employee_number}</td>
                <td>{$emp->name}</td>
                <td>{$emp->contract_number}</td>
                <td>{$emp->end_date}</td>
            </tr>";
        }
        $message .= "</table>";

        // Konfigurasi email
        $this->email->from('bifa.acd@gmail.com', 'HR Notification');
        $this->email->to($recipient);
        $this->email->subject('Pemberitahuan: Kontrak Karyawan Akan Habis');
        $this->email->message($message);

        // Kirim email
        if ($this->email->send()) {
            echo "Email berhasil dikirim.";
        } else {
            echo "Gagal mengirim email. ". $recipient[0];
            log_message('error', $this->email->print_debugger());
        }

        } else {
            echo "Tidak ada kontrak yang akan habis dalam 20 hari.";
        }
        // if(!empty($recipient)){            

        //     $this->load->library('email');
        //     $this->email->set_newline("\r\n");
        //     $from_email = "bifa.acd@gmail.com";
        //     $to_email = "aidanurul99@rocketmail.com";
        //     // $message = "<p>Dear ".$keterangan."</p>";
        //     $message .= "<p>SPPD Berikut Telah</p>";
        //     $message .= "<table style='border-collapse: collapse;padding: 1.2em 0;margin-bottom: 20pxwidth: 100%!important;background: #fff;'>";
        //     $message .= "<thead>";
        //     $message .= "<tr>";
        //     $message .= "<th style='padding: 2px 10px;text-align: left;font-size: 12px;border: 1px solid #999;'>Date</th>";
        //     $message .= "<th style='padding: 2px 10px;text-align: left;font-size: 12px;border: 1px solid #999;'>No. SPD</th>";
        //     $message .= "<th style='padding: 2px 10px;text-align: left;font-size: 12px;border: 1px solid #999;'>Name</th>";
        //     $message .= "<th style='padding: 2px 10px;text-align: left;font-size: 12px;border: 1px solid #999;'>From</th>";
        //     $message .= "<th style='padding: 2px 10px;text-align: left;font-size: 12px;border: 1px solid #999;'>Destination</th>";
        //     $message .= "<th style='padding: 2px 10px;text-align: left;font-size: 12px;border: 1px solid #999;'>Duration</th>";
        //     $message .= "</tr>";
        //     $message .= "</thead>";
        //     $message .= $item_message;
        //     $message .= "</table>";
        //     $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
        //     $message .= "<p>[ <a href='".$this->config->item('url_mrp')."' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
        //     $message .= "<p>Thanks and regards</p>";
        //     $this->email->from($from_email, 'Material Resource Planning');
        //     $this->email->to($recipient);
        //     $this->email->subject('Permintaan Approval SPPD');
        //     $this->email->message($message);
            
    
        //     // Send mail 
        //     if ($this->email->send())
        //       return true;
        //     else
        //       return $this->email->print_debugger();
        // }else{
        //     return true;
        // }
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
