<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['user'];
    $this->load->model($this->module['model'], 'model');
    $this->load->helper(array('form', 'url'));
    $this->load->library('upload');
    $this->load->helper('string');
    $this->data['module'] = $this->module;
  }

  public function index()
  {
    $this->authorized($this->module, 'index');

    $this->data['page']['title']        = 'User';
    $this->data['page']['requirement']  = array('datatable', 'form_create', 'form_edit');
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source');
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = NULL;
    $this->data['grid']['order_columns']    = array (
      0 => array (0 => 1, 1 => 'asc'),
      1 => array (0 => 2, 1 => 'asc'),
      2 => array (0 => 3, 1 => 'asc'),
      3 => array (0 => 4, 1 => 'asc'),
      4 => array (0 => 5, 1 => 'asc'),
      5 => array (0 => 6, 1 => 'asc'),
      6 => array (0 => 7, 1 => 'desc'),
      7 => array (0 => 8, 1 => 'desc')
    );

    $this->render_view($this->module['view'] .'/index');
  }

  public function index_data_source()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'index') === FALSE){
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {
      $entities = $this->model->getIndex();

      $data = array();
      $no   = $_POST['start'];

      foreach ($entities as $row){
        $no++;
        $col = array();
        $col[] = print_number($no);
        $col[] = print_string($row['person_name']);
        $col[] = print_config('levels_and_roles', $row['auth_level']);
        $col[] = print_string($row['warehouse']);
        $col[] = print_string($row['username']);
        $col[] = print_string($row['email']);
        $col[] = ($row['banned'] == 0) ? 'active' : 'banned';
        $col[] = print_date($row['last_login']);
        $col[] = print_date($row['modified_at']);
        $col['DT_RowId'] = 'row_'. $row['user_id'];
        $col['DT_RowData']['pkey']  = $row['user_id'];
        $col['DT_RowAttr']['onClick']     = '$(this).popup();';
        $col['DT_RowAttr']['data-target'] = '#data-modal';
        $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] .'/edit/'. $row['user_id']);

        $data[] = $col;
      }

      $return = array(
          "draw"            => $_POST['draw'],
          "recordsTotal"    => $this->model->countIndex(),
          "recordsFiltered" => $this->model->countIndexFiltered(),
          "data"            => $data,
       );
    }

    echo json_encode($return);
  }

  public function create()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'create') === FALSE){
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to create data!";
    } else {
      $return['type'] = 'success';
      $return['info'] = $this->load->view($this->module['view'] .'/create', $this->data, TRUE);
    }

    echo json_encode($return);
  }

  public function edit($id)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'edit') === FALSE){
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to edit this data!";
    } else {
      $entity = $this->model->findOneBy(array('user_id' => $id));

      $this->data['entity'] = $entity;

      $return['type'] = 'success';
      $return['info'] = $this->load->view($this->module['view'] .'/edit', $this->data, TRUE);
    }

    echo json_encode($return);
  }

  public function save()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'save') === FALSE){
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {
      if ($this->input->post('id')){
        if ($this->model->isUsernameExists($this->input->post('username'), $this->input->post('username_exception'))){
          $return['type'] = 'danger';
          $return['info'] = 'Duplicate Username! Username '. $this->input->post('username') .' already exists.';
        } else {
          $user_data = array(
            'username'    => $this->input->post('username'),
            'person_name'    => $this->input->post('person_name'),
            'email'       => $this->input->post('email'),
            'banned'      => $this->input->post('banned'),
            'auth_level'  => $this->input->post('auth_level'),
            'warehouse'   => $this->input->post('warehouse'),
            'modified_at' => date('Y-m-d H:i:s'),
            // 'ttd_user'    => $this->_uploadImage()
          );

          // if (!empty($_FILES["attachment"]["name"])) {
          //     $user_data['ttd_user'] = $this->_uploadImage();
          // }

          if ($this->input->post('passwd')){
            $passwd = $this->hash_passwd($this->input->post('passwd'));
            $user_data['passwd'] = $passwd;
          }
          // if (!empty($_FILES['userfile']['name'])) {
          //   $upload = $this->_do_upload();
          //   $user_data['ttd_user'] = $upload;
          // }

          $criteria = array('user_id' => $this->input->post('id'));

          if ($this->model->update($user_data, $criteria)){
            $return['type'] = 'success';
            $return['info'] = 'User ' . $this->input->post('person_name') .' updated.';
          } else {
            $return['type'] = 'danger';
            $return['info'] = 'There are error while updating data. Please try again later.';
          }
        }
      } else {
        if ($this->model->isUsernameExists($this->input->post('username'))){
          $return['type'] = 'danger';
          $return['info'] = 'Duplicate Username! Username '. $this->input->post('username') .' already exists.';
        } else {
          $passwd = $this->hash_passwd($this->input->post('passwd'));

          $user_data = array(
            'user_id'    => $this->model->get_unused_id(),
            'username'   => $this->input->post('username'),
            'person_name'   => $this->input->post('person_name'),
            'passwd'     => $passwd,
            'email'      => $this->input->post('email'),
            'auth_level' => $this->input->post('auth_level'),
            'warehouse'  => $this->input->post('warehouse'),
            'created_at' => date('Y-m-d H:i:s'),
            // 'ttd_user'    => $this->_uploadImage()
          );
          // if (!empty($_FILES['userfile']['name'])) {
          //   $upload = $this->_do_upload();
          //   $user_data['ttd_user'] = $upload;
          // }



          if ($this->model->insert($user_data)){
            $return['type'] = 'success';
            $return['info'] = 'User for ' . $this->input->post('person_name') .' created.';
          } else {
            $return['type'] = 'danger';
            $return['info'] = 'There are error while updating data. Please try again later.';
          }
        }
      }
    }

    echo json_encode($return);
  }

  public function delete()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'delete') === FALSE){
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to delete this data!";
    } else {
      if ($this->model->delete()){
        $return['type'] = 'success';
        $return['info'] = 'User ' . $this->input->post('person_name') .' deleted.';
      } else {
        $return['type'] = 'danger';
        $return['info'] = 'There are error while trying to delete data. Please try again later.';
      }
    }

    echo json_encode($return);
  }

  public function import()
  {
    $this->authorized($this->module, 'import');

    $this->load->library('form_validation');

    if (isset($_POST) && !empty($_POST)){
      // $this->form_validation->set_rules('userfile', 'CSV File', 'trim|required');
      $this->form_validation->set_rules('delimiter', 'Value Delimiter', 'trim|required');

      /**
       * Processing validation
       * Run OK
       */
      if ($this->form_validation->run() === TRUE){
        $file       = $_FILES['userfile']['tmp_name'];
        $delimiter  = $this->input->post('delimiter');

        //... open file
        if (($handle = fopen($file, "r")) !== FALSE){
          $row     = 1;
          $data    = array();
          $errors  = array();
          $user_id = array();
          $index   = 0;
          fgetcsv($handle); // skip first line (as header)

          //... parsing line
          while (($col = fgetcsv($handle, 1024, $delimiter)) !== FALSE){
            //... 1st column is person name
            $realname   = $this->model->check_person_name($col[0]);
            //... 2nd column is username
            $username   = $this->model->check_username($col[1]);
            //... 3rd column is email
            $email      = $this->model->check_email($col[2]);
            //... 4th column is role
            $user_role  = $this->model->check_role($col[3]);
            //... 5th column is password
            $password   = $this->model->check_password($col[4]);

            if ($realname && $username && $user_role && $email && $password){
              //... encrypt the password
              $password        = $this->hash_passwd($password);
              $user_id[$index] = $this->model->get_unused_id($user_id);

              //... set user data for insert into table
              $data[] = array(
                'user_id'    => $user_id[$index],
                'username'   => $username,
                'person_name'   => $realname,
                'passwd'     => $password,
                'email'      => $email,
                'auth_level' => $user_role,
                'created_at' => date('Y-m-d H:i:s'),
             );
            } else {
              $errors[] = $row;
            }

            $row++;
          }
          fclose($handle);

          if (empty($errors)){
            /**
             * Insert into user table
             */
            if ($this->model->insert_batch($data)){
              //... send message to view
              $this->session->set_flashdata('alert', array(
                'type' => 'success',
                'info' => count($data)." data has been imported!"
             ));

              redirect('user');
            }
          }

          $this->session->set_flashdata('alert', array(
            'type' => 'danger',
            'info' => 'There are errors on line '. json_encode($errors)
         ));
        } else {
          $this->session->set_flashdata('alert', array(
            'type' => 'danger',
            'info' => 'Cannot open file!'
         ));
        }
      }
    }

    //... set view data
    $this->data['page_content'] = $this->module['view'] .'/import';
    $this->data['page_title']   = 'Import From CSV';

    //... render view
    $this->render_view();
  }

  public function _do_upload()
{
    $config['upload_path'] = './ttd_user/';
    $config['allowed_types']  = 'gif|jpg|png';
    $config['max_size']       = 100;
    $config['max_widht']      = 1000;
    $config['max_height']     = 1000;
    $config['file_name']      = round(microtime(true)*1000);
 
    $this->load->library('upload', $config);
    if (!$this->upload->do_upload('photo')) {
      // $this->session->set_flashdata('msg', $this->upload->display_errors('',''));
      return $this->upload->display_errors('','');
    }
    return $this->upload->data('file_name');
}

  public function ttd($id)
  {
    // $this->authorized($this->module, 'document');
    
    $this->render_view($this->module['view'] .'/attachment');
  }

  public function upload_ttd($user_id)
  {
    $this->authorized($this->module, 'create');

    $this->data['manage_attachment'] = $this->model->listAttachment_2($user_id);
    $this->data['id_poe'] = $user_id;
    $this->render_view($this->module['view'] .'/manage_ttd');
  }

  public function add_attachment_to_db($user_id)
  {
    $result["status"] = 0;
    $date = new DateTime();
    $config['file_name'] = $date->getTimestamp().random_string('alnum', 5);
    $config['upload_path'] = 'ttd_user/';
    $config['allowed_types'] = 'jpg|png|jpeg|doc|docx|xls|xlsx|pdf';
    $config['max_size']  = 2000;
    
    $this->upload->initialize($config);
    
    if ( ! $this->upload->do_upload('attachment'))
    {
      $error = array('error' => $this->upload->display_errors());
    }
    else
    {
      $data = array('upload_data' => $this->upload->data());
      $url = $data['upload_data']['file_name'];
      // array_push($_SESSION["poe"]["attachment"], $url);
      $this->model->add_attachment_to_db($user_id,$url);
      $result["status"] = 1;
    }
    echo json_encode($result);
  }
}
