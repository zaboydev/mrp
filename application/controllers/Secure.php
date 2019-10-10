<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Secure extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['secure'];
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function login()
  {
    if ($this->uri->uri_string() == 'secure/login')
      show_404();

    // if (strtolower($_SERVER['REQUEST_METHOD']) == 'post')
    //   $this->require_min_level(1);

    $this->load->helper('form');
    // $this->setup_login_form();

    if (isset($_GET['keywords']) && trim($_GET['keywords']) != ''){
      $this->data['entities'] = $this->_model->find_items($_GET['keywords'], $_GET['warehouse']);
    } else {
      $this->data['entities'] = FALSE;
    }

    $this->data['json_description'] = $this->_model->search_stock_in_stores();
    $this->data['warehouses'] = $this->_model->findAllWarehouses('AVAILABLE');
    $this->data['warehouse'] = (isset($_GET['warehouse'])) ? $_GET['warehouse'] : 'ALL BASE';

    $this->render_view($this->module['view'] .'/login');
  }

  public function authentication()
  {
    $login_string = $this->input->post('login_string', TRUE);
    $login_pass   = $this->input->post('login_pass', TRUE);

    if ($this->model->authorized($login_string, $login_pass) === TRUE){
      $authentication = $this->model->authentication($login_string, TRUE);

      $user_data  = array(
        'is_logged_in'  => TRUE,
        'user_id'       => $authentication['user_id'],
        'username'      => $authentication['username'],
        'person_name'   => $authentication['person_name'],
        'email'         => $authentication['email'],
        'auth_level'    => $authentication['auth_level'],
        'banned'        => $authentication['banned'],
        'warehouse'     => $authentication['warehouse']
      );

      $this->session->set_userdata($user_data);

      redirect($this->modules['dashboard']['route']);
    } else {
      redirect(LOGIN_PAGE . '?failed=1');
    }
  }

  public function logout()
  {
    $this->session->sess_destroy();

    redirect(site_url('login?logout=1'));
  }

  public function search()
  {
    // $this->authorized($this->module, 'search');

    if (isset($_GET['keywords']) && trim($_GET['keywords']) != ''){
      $this->data['entities'] = $this->_model->find_items($_GET['keywords'], $_GET['warehouse']);
    } else {
      $this->data['entities'] = FALSE;
    }

    $this->render_view($this->module['view'] .'/search');
  }

  public function adjustment()
  {
    // $this->authorized($this->module, 'search');
    $mode   = (isset($_GET['mode']) && trim($_GET['mode']) != '') ? $_GET['mode'] : NULL;
    $id     = (isset($_GET['id']) && trim($_GET['id']) != '') ? $_GET['id'] : NULL;
    $token  = (isset($_GET['token']) && trim($_GET['token']) != '') ? $_GET['token'] : NULL;

    $this->data['mode']   = $mode;
    $this->data['id']     = $id;
    $this->data['token']  = $token;

    if ($mode != NULL && $id != NULL && $token != NULL){
      $this->data['success'] = $this->_model->adjustment_approval($mode, $id, $token);
    } else {
      $this->data['success'] = FALSE;
    }

    $this->render_view($this->module['view'] .'/adjustment');
  }

  public function denied()
  {
    $this->authorized($this->module, 'denied');

    $this->data['page_header']  = 'Resticted';
    $this->data['page_title']   = 'Access Denied';
    $this->data['page_desc']    = NULL;

    $this->render_view($this->module['view'] .'/denied');
  }

  public function connection()
  {
    $this->authorized($this->module, 'connection');

    $this->data['page_header'] = 'Server Down';
    $this->data['page_title'] = 'Server Connection Failed';
    $this->data['page_desc'] = NULL;

    $this->render_view($this->module['view'] .'/connection');
  }

  public function maintenance()
  {
    $this->authorized($this->module, 'maintenance');

    $this->data['page_header'] = 'Restricted';
    $this->data['page_title'] = 'Under Maintenance';
    $this->data['page_desc'] = NULL;

    $this->render_view($this->module['view'] .'/maintenance');
  }

  public function password()
  {
    $this->authorized($this->module, 'password');

    if ($this->input->post())
    {
      if ($this->input->post('passwd') === $this->input->post('passconf')){
        $passwd = $this->hash_passwd($this->input->post('passwd'));

        $this->db->set('passwd', $passwd);
        $this->db->where('user_id', config_item('auth_user_id'));

        if ($this->db->update('tb_auth_users'))
        {
          $this->session->set_flashdata('alert', array(
            'type' => 'success',
            'info' => 'Password Anda telah diperbarui.'
          ));
        } else {
          $this->session->set_flashdata('alert', array(
            'type' => 'danger',
            'info' => 'Tidak bisa menyimpan data.'
          ));
        }
      } else {
        $this->session->set_flashdata('alert', array(
          'type' => 'danger',
          'info' => 'Password tidak cocok.'
        ));
      }
    }

    $this->data['page_content'] = '_modules/secure/password';
    $this->data['page_header'] = 'Security Option';
    $this->data['page_title'] = 'Change Password';
    $this->data['page_desc'] = NULL;

    $this->data['page']['has_form'] = TRUE;

    $this->render_view($this->module['view'] .'/password');
  }

  public function cron_job_send_email()
  {
    $this->_model->cron_job_send_email();
  }
}
