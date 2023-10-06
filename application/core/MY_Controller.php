<?php defined('BASEPATH') OR exit('No direct script access allowed');

// require_once APPPATH . 'third_party/community_auth/core/Auth_Controller.php';

// class MY_Controller extends Auth_Controller
class MY_Controller extends CI_Controller
{
  protected $login_theme;
  protected $base_theme;
  protected $modules;
  protected $connection;

  public $data = array();

  public $roles;
  public $levels;

  public $main_warehouse;
  public $user_base;

  public function __construct()
  {
    parent::__construct();

    // $this->is_logged_in();
    
    $this->connection   = $this->load->database('budgetcontrol', TRUE);
    $this->config->load('app_config');
    $this->config->load('app_kernel');
    $this->config->load('tables');

    $this->load->helper('form');
    $this->load->helper('date');
    $this->load->model('app_model', '_model');

    $this->modules        = config_item('module');
    $this->main_warehouse = $this->main_warehouse();
    $this->user_base      = $this->user_base();

    $this->data['modules']        = $this->modules;
    $this->data['close_url']      = site_url();
    $this->data['main_warehouse'] = $this->main_warehouse;
    $this->data['user_base']      = $this->user_base;

    $this->data['page']['requirement']  = array();

    $this->roles = config_item('levels_and_roles');
    $this->levels = array_flip( $this->roles );

    $this->login_theme  = 'admin_lte/login';
    $this->base_theme   = config_item('theme_path') . '/page';
    $this->pdf_theme    = config_item('theme_path') . '/pdf';
    $this->form_theme   = config_item('theme_path') . '/form';
    $this->table_theme  = config_item('theme_path') . '/table';

    $this->config->set_item('main_warehouse', $this->main_warehouse);
    $this->config->set_item('auth_warehouses', $this->auth_warehouses());
    $this->config->set_item('auth_inventory', $this->auth_inventory());
    $this->config->set_item('auth_annual_cost_centers', $this->auth_annual_cost_centers());
    $this->config->set_item('auth_annual_cost_centers_id', $this->auth_annual_cost_centers_id());
    $this->config->set_item('auth_annual_cost_centers_name', $this->auth_annual_cost_centers_name());
    $this->config->set_item('period_year', get_setting('ACTIVE_YEAR'));
    $this->config->set_item('period_month', get_setting('ACTIVE_MONTH'));
    $this->config->set_item('auth_role', $this->get_auth_role());
    $this->config->set_item('auth_user_id', $_SESSION['user_id']);
    $this->config->set_item('auth_username', $_SESSION['username']);
    $this->config->set_item('auth_person_name', $_SESSION['person_name']);
    $this->config->set_item('auth_level', $_SESSION['auth_level']);
    $this->config->set_item('auth_warehouse', $_SESSION['warehouse']);
    $this->config->set_item('auth_email', $_SESSION['email']);
    $this->config->set_item('as_head_department', $this->as_head_department());
    $this->config->set_item('head_department', $this->head_department());
    $this->config->set_item('hr_manager', $this->hr_manager());
    $this->config->set_item('auth_department_id', $this->auth_department_id());
  }

  public function get_auth_role()
  {
    $levels_and_roles = config_item('levels_and_roles');

    return $levels_and_roles[$_SESSION['auth_level']];
  }

  public function get_auth_person_name()
  {
    $levels_and_roles = config_item('levels_and_roles');

    return $levels_and_roles[$_SESSION['auth_level']];
  }

  public function get_auth_session($data)
  {
    return $_SESSION[$data];
  }

  public function has_role($module, $roles)
  {
    if ( isset($module['permission'][$roles]) && in_array(config_item('auth_role'), (array)explode(',', $module['permission'][$roles])) ){
      return TRUE;
    }
    else{
      if (config_item('as_head_department')=='yes') {
        if($roles=='index'||$roles=='info'||$roles=='print'||$roles=='approval'){
          return TRUE;
        }else{
          return FALSE;
        }
      }else{
        return FALSE;
      }
    }
  }

  public function authorized($module, $roles = NULL)
  {
    if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === TRUE){
      if ($this->main_warehouse() == NULL || config_item('maintenance') == TRUE)
        redirect(site_url('secure/maintenance'));

      if ($roles === NULL)
        return TRUE;

      if (is_array($roles) ){
        foreach ($roles as $role) {
          if ( $this->has_role($module, $role) === FALSE ){
            continue;
          } else{
            return TRUE;
            break;
          }
        }

        redirect(site_url('secure/denied'));
      } else {
        if ($this->has_role($module, $roles) === FALSE)
          redirect(site_url('secure/denied'));
      }

      $this->data['page']['role'] = $roles;

      return TRUE;
    } else {
      redirect(site_url('login'));
    }
  }

  public function hash_passwd( $password, $random_salt = '' )
  {
    // If no salt provided for older PHP versions, make one
    if( ! is_php('5.5') && empty( $random_salt ) )
      $random_salt = $this->random_salt();

    // PHP 5.5+ uses new password hashing function
    if( is_php('5.5') ){
      return password_hash( $password, PASSWORD_BCRYPT, array( 'cost' => 11 ) );
    }

    // Older versions of PHP use crypt
    else if( is_php('5.3.7') ){
      return crypt( $password, '$2y$10$' . $random_salt );
    }else{
      return crypt( $password, '$2a$09$' . $random_salt );
    }
  }

  protected function main_warehouse()
  {
    $this->db->select('setting_value');
    $this->db->from('tb_settings');
    $this->db->where('setting_name', 'MAIN BASE');
    $query = $this->db->get();

    $warehouse = $query->unbuffered_row();

    return $warehouse->setting_value;
  }

  protected function user_base()
  {
    if ($_SESSION['warehouse'] === $this->main_warehouse){
      return 'main_warehouse';
    } else {
      return 'out_base';
    }
  }

  protected function auth_warehouses()
  {
    $return = array();

    if ($this->get_auth_role() == 'PIC STOCK'){
      $return[] = $_SESSION['warehouse'];
    } elseif ($this->get_auth_role() == 'ASSISTANT HOS'){
      $return[] = $_SESSION['warehouse'];
    } elseif ($this->get_auth_role() == 'AP STAFF'){
      $return[] = $_SESSION['warehouse'];
    } elseif ($this->get_auth_role() == 'PIC STAFF'){
      if($this->as_head_department()=='yes'){
        $this->db->select('warehouse');
        $this->db->from('tb_master_warehouses');

        $query = $this->db->get();
        $result = $query->result_array();

        foreach ($result as $row) {
          $return[] = $row['warehouse'];
        }
      }else{
        $return[] = $_SESSION['warehouse'];
      }      
    } else {
      $this->db->select('warehouse');
      $this->db->from('tb_master_warehouses');

      $query = $this->db->get();
      $result = $query->result_array();

      foreach ($result as $row) {
        $return[] = $row['warehouse'];
      }
    }

    return $return;
  }

  protected function as_head_department()
  {
    $this->db->select('department_id');
    $this->db->from('tb_head_department');
    $this->db->where('username',$_SESSION['username']);
    $this->db->where('status','active');
    $query  = $this->db->get();
    
    return ( $query->num_rows() > 0 ) ? 'yes' : 'no';
  }

  protected function head_department()
  {
    $this->db->select('department_id');
    $this->db->from('tb_head_department');
    $this->db->where('username',$_SESSION['username']);
    $this->db->where('status','active');
    $query  = $this->db->get();
    
    $return = array();
    if($query->num_rows() > 0){      
      $result = $query->result_array();

      foreach ($result as $row) {
        $department_id = $row['department_id'];
        $this->connection->select('department_name');
        $this->connection->from('tb_departments');
        $this->connection->where('id',$department_id);
        $query  = $this->connection->get();
        $result = $query->unbuffered_row('array');
        $department_name = $result['department_name'];
        $return[] = $department_name;
      }
    }else{
      $return[] = 'no_head_department';
    }

    return $return;
  }

  protected function auth_inventory()
  {
    if ($_SESSION['auth_level'] > 5){
      $this->db->select('category');
      $this->db->from('tb_auth_user_categories');
      $this->db->where('username', $_SESSION['username']);
    } else {
      $this->db->select('category');
      $this->db->from('tb_master_item_categories');
      $this->db->order_by('category', 'ASC');
    }

    $query  = $this->db->get();
    $result = $query->result_array();
    $return = array();

    foreach ($result as $row) {
      $return[] = $row['category'];
    }

    return $return;
  }

  protected function auth_annual_cost_centers()
  {
    $year = $this->find_budget_setting('Active Year');
    if ($_SESSION['auth_level'] > 5){
      $this->connection->select(array('cost_center_name','tb_annual_cost_centers.id'));
      $this->connection->from('tb_users_mrp_in_annual_cost_centers');
      $this->connection->join('tb_annual_cost_centers','tb_annual_cost_centers.id=tb_users_mrp_in_annual_cost_centers.annual_cost_center_id');
      $this->connection->join('tb_cost_centers','tb_cost_centers.id=tb_annual_cost_centers.cost_center_id');
      $this->connection->where('tb_users_mrp_in_annual_cost_centers.username', $_SESSION['username']);
      $this->connection->where('tb_annual_cost_centers.year_number', $year);
    } else {
      $this->connection->select(array('cost_center_name','tb_annual_cost_centers.id'));
      $this->connection->from('tb_users_mrp_in_annual_cost_centers');
      $this->connection->join('tb_annual_cost_centers','tb_annual_cost_centers.id=tb_users_mrp_in_annual_cost_centers.annual_cost_center_id');
      $this->connection->join('tb_cost_centers','tb_cost_centers.id=tb_annual_cost_centers.cost_center_id');
      $this->connection->where('tb_users_mrp_in_annual_cost_centers.username', $_SESSION['username']);
      $this->connection->where('tb_annual_cost_centers.year_number', $year);
    }

    $query  = $this->connection->get();
    $result = $query->result_array();
    

    return $result;
  }

  protected function auth_annual_cost_centers_id()
  {
    $year = $this->find_budget_setting('Active Year');
    $this->connection->select(array('tb_users_mrp_in_annual_cost_centers.annual_cost_center_id'));
    $this->connection->from('tb_users_mrp_in_annual_cost_centers');
    $this->connection->join('tb_annual_cost_centers','tb_annual_cost_centers.id=tb_users_mrp_in_annual_cost_centers.annual_cost_center_id');
    $this->connection->where('tb_users_mrp_in_annual_cost_centers.username', $_SESSION['username']);
    $this->connection->where('tb_annual_cost_centers.year_number', $year);

    $query  = $this->connection->get();
    $result = $query->result_array();
    $return = array();

    foreach ($result as $row) {
      $return[] = $row['annual_cost_center_id'];
    }

    return $return;
  }

  protected function hr_manager()
  {
    $this->db->select('tb_head_department.username,tb_auth_users.person_name');
    $this->db->from('tb_head_department');
    $this->db->join('tb_auth_users','tb_auth_users.username=tb_head_department.username');
    $this->db->where('tb_head_department.department_id', 11);
    $this->db->where('tb_head_department.status', 'active');
    $this->db->order_by('tb_head_department.username', 'ASC');

    $query  = $this->db->get();
    $result = $query->result_array();
        
    $return = array();
    foreach ($result as $key) {
      $return[] = $key['username'];
    }

    return $return;
  }

  protected function auth_annual_cost_centers_name()
  {
    $year = $this->find_budget_setting('Active Year');
    if ($_SESSION['auth_level'] > 5){
      $this->connection->select('cost_center_name');
      $this->connection->from('tb_users_mrp_in_annual_cost_centers');
      $this->connection->join('tb_annual_cost_centers','tb_annual_cost_centers.id=tb_users_mrp_in_annual_cost_centers.annual_cost_center_id');
      $this->connection->join('tb_cost_centers','tb_cost_centers.id=tb_annual_cost_centers.cost_center_id');
      $this->connection->where('tb_users_mrp_in_annual_cost_centers.username', $_SESSION['username']);
      $this->connection->where('tb_annual_cost_centers.year_number', $year);
    } else {
      $this->connection->select('cost_center_name');
      $this->connection->from('tb_users_mrp_in_annual_cost_centers');
      $this->connection->join('tb_annual_cost_centers','tb_annual_cost_centers.id=tb_users_mrp_in_annual_cost_centers.annual_cost_center_id');
      $this->connection->join('tb_cost_centers','tb_cost_centers.id=tb_annual_cost_centers.cost_center_id');
      $this->connection->order_by('cost_center_name', 'ASC');
    }

    $query  = $this->connection->get();
    $result = $query->result_array();
    $return = array();

    foreach ($result as $row) {
      $return[] = $row['cost_center_name'];
    }

    return $return;
  }

  protected function auth_department_id()
  {
    $year = $this->find_budget_setting('Active Year');
    $this->connection->select(array('tb_cost_centers.department_id'));
    $this->connection->from('tb_users_mrp_in_annual_cost_centers');
    $this->connection->join('tb_annual_cost_centers','tb_annual_cost_centers.id=tb_users_mrp_in_annual_cost_centers.annual_cost_center_id');
    $this->connection->join('tb_cost_centers','tb_cost_centers.id=tb_annual_cost_centers.cost_center_id');
    $this->connection->where('tb_users_mrp_in_annual_cost_centers.username', $_SESSION['username']);
    $this->connection->where('tb_annual_cost_centers.year_number', $year);

    $query  = $this->connection->get();
    $result = $query->result_array();
    $return = array();

    foreach ($result as $row) {
      $return[] = $row['department_id'];
    }

    return $return;
  }

  protected function find_budget_setting($name)
  {

    $this->connection->from('tb_settings');
    $this->connection->where('setting_name', $name);

    $query    = $this->connection->get();
    $setting  = $query->unbuffered_row('array');
    $return   = $setting['setting_value'];

    return $return;
  }

  protected function cell_string($string, $emptyString = 'N/A')
  {
    if (empty($string))
      $string = $emptyString;

    return $string;
  }

  protected function cell_date($date)
  {
    if (empty($date))
      $date = 'N/A';

    return nice_date($date, 'F d, Y');
  }

  protected function cell_numeric($number, $decimal = 0)
  {
    if (empty($number))
      $number = 0;

    return '<span style="display:block; text-align:right">'. number_format($number, $decimal) .'</span>';
  }

  protected function cell_config($config, $data)
  {
    if (empty($data)){
      return 'N/A';
    }

    $conf = config_item($config);

    return $conf[$data];
  }

  protected function pretty_dump($variable)
  {
    echo '<pre>';

    print_r($variable);

    echo '</pre>';

    exit();
  }

  protected function render_view($theme = NULL, $data = NULL, $print = FALSE)
  {
    if ($data === NULL)
      $data = $this->data;

    if ($theme === NULL)
      $theme = $this->base_theme;
    $this->load->view($theme, $data, $print);
  }

  protected function render_maintenance()
  {
    if ($this->require_min_level(1))
    {
      $data['page_content'] = '_modules/secure/maintenance';
      $data['page_header'] = 'Resticted';
      $data['page_title'] = 'Under Maintenance';
      $data['page_desc'] = NULL;

      $this->render_view('admin_lte/dashboard', $data);
    }
  }

  protected function render_alert($message, $type = 'info', $dismissable = TRUE)
  {
    $html = '<div class="alert alert-'.$type.' alert-dismissable">';

    if ($dismissable === TRUE)
      $html .= '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';

    $html .= nl2br($message);
    $html .= '</div>';

    return $html;
  }

  protected function monthName($month, $case = NULL)
  {
    $month = intval($month);

    switch ($month) {
      case 1:
      case 01:
        $print = 'January';
        break;

      case 2:
      case 02:
        $print = 'February';
        break;

      case 3:
      case 03:
        $print = 'March';
        break;

      case 4:
      case 04:
        $print = 'April';
        break;

      case 5:
      case 05:
        $print = 'May';
        break;

      case 6:
      case 06:
        $print = 'June';
        break;

      case 7:
      case 07:
        $print = 'July';
        break;

      case 8:
      case 08:
        $print = 'August';
        break;

      case 9:
      case 09:
        $print = 'September';
        break;

      case 10:
        $print = 'October';
        break;

      case 11:
        $print = 'November';
        break;

      case 12:
        $print = 'December';
        break;
    }

    if ($case == 'uppercase'){
      $print = strtoupper($print);
    } else if ($case == 'lowercase') {
      $print = strtolower($print);
    }

    return $print;
  }
}
