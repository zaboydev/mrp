<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends MY_Controller
{
    protected $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['employee'];
        $this->load->model($this->module['model'], 'model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('upload');
        $this->load->helper('string');
        $this->data['module'] = $this->module;
    }

    public function index()
    {
        $this->authorized($this->module, 'index');

        $this->data['page']['title']        = $this->module['label'];
        $this->data['page']['requirement']  = array('datatable', 'form_create', 'form_edit');
        $this->data['grid']['column']           = $this->model->getSelectedColumns();
        $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source');
        $this->data['grid']['fixed_columns']    = 2;
        $this->data['grid']['summary_columns']  = NULL;
        $this->data['grid']['order_columns']    = array (
          0 => array (0 => 1, 1 => 'asc'),
          1 => array (0 => 2, 1 => 'asc'),
          2 => array (0 => 3, 1 => 'asc'),
          3 => array (0 => 4, 1 => 'asc')
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
                $department = getDepartmentById($row['department_id']);
                $no++;
                $col = array();
                $col[] = print_number($no);
                $col[] = print_string($row['employee_number']);
                $col[] = print_string($row['name']);
                $col[] = print_string($department['department_name']);
                $col[] = print_string($row['position']);
                $col[] = print_date($row['updated_at']);
                $col['DT_RowId'] = 'row_'. $row['id'];
                $col['DT_RowData']['pkey']  = $row['id'];
                $col['DT_RowAttr']['onClick']     = '$(this).popup();';
                $col['DT_RowAttr']['data-target'] = '#data-modal';
                $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] .'/edit/'. $row['employee_number']);

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
            $entity = $this->model->findById($id);

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
                if ($this->model->isEmployeeNumberExists($this->input->post('employee_number'), $this->input->post('employee_number_exception'))){
                    $return['type'] = 'danger';
                    $return['info'] = 'Duplicate Employee Number! Employee Number '. $this->input->post('employee_number') .' already exists.';
                } else {
                    $form_data = array(
                        'employee_number'           => $this->input->post('employee_number'),
                        'name'                      => $this->input->post('name'),
                        'user_id'                   => $this->input->post('user_id'),
                        'date_of_birth'             => $this->input->post('date_of_birth'),
                        'gender'                    => $this->input->post('gender'),
                        'religion'                  => $this->input->post('religion'),
                        'marital_status'            => $this->input->post('marital_status'),
                        'phone_number'              => $this->input->post('phone_number'),
                        'email'                     => $this->input->post('email'),
                        'address'                   => $this->input->post('address'),
                        'position'                  => $this->input->post('position'),
                        'plafon_biaya_dinas'        => $this->input->post('plafon_biaya_dinas'),
                        'plafon_biaya_kesehatan'    => $this->input->post('plafon_biaya_kesehatan'),
                        'cuti'                      => $this->input->post('cuti'),
                        'warehouse'                      => $this->input->post('warehouse'),
                        'identity_type'             => $this->input->post('identity_type'),
                        'identity_number'           => $this->input->post('identity_number'),
                        'bank_account'              => $this->input->post('bank_account'),
                        'bank_account_name'         => $this->input->post('bank_account_name'),
                        'npwp'                      => $this->input->post('npwp'),
                        'basic_salary'              => $this->input->post('basic_salary'),
                        'tanggal_bergabung'         => $this->input->post('tanggal_bergabung'),
                        'left_plafon_biaya_dinas'           => $this->input->post('plafon_biaya_dinas'),
                        'left_plafon_biaya_kesehatan'       => $this->input->post('plafon_biaya_kesehatan'),
                        'left_cuti'                         => $this->input->post('cuti'),
                        'updated_by'                        => config_item('auth_person_name'),
                        'updated_at'                        => date('Y-m-d H:i:s'),
                    );

                    $criteria = $this->input->post('id');

                    if ($this->model->update($form_data, $criteria)){
                        $return['type'] = 'success';
                        $return['info'] = 'Employee ' . $this->input->post('name') .' updated.';
                    } else {
                        $return['type'] = 'danger';
                        $return['info'] = 'There are error while updating data. Please try again later.';
                    }
                }
            } else {
                if ($this->model->isEmployeeNumberExists($this->input->post('employee_number'))){
                    $return['type'] = 'danger';
                    $return['info'] = 'Duplicate Employee Number! Employee Number '. $this->input->post('employee_number') .' already exists.';
                } else {

                    $form_data = array(
                        'employee_number'           => $this->input->post('employee_number'),
                        'name'                      => $this->input->post('name'),
                        'user_id'                   => $this->input->post('user_id'),
                        'date_of_birth'             => $this->input->post('date_of_birth'),
                        'gender'                    => $this->input->post('gender'),
                        'religion'                  => $this->input->post('religion'),
                        'marital_status'            => $this->input->post('marital_status'),
                        'phone_number'              => $this->input->post('phone_number'),
                        'email'                     => $this->input->post('email'),
                        'address'                   => $this->input->post('address'),
                        'department_id'             => $this->input->post('department_id'),
                        'position'                  => $this->input->post('position'),
                        'plafon_biaya_dinas'        => $this->input->post('plafon_biaya_dinas'),
                        'plafon_biaya_kesehatan'    => $this->input->post('plafon_biaya_kesehatan'),
                        'cuti'                      => $this->input->post('cuti'),
                        'warehouse'                      => $this->input->post('warehouse'),
                        'identity_type'             => $this->input->post('identity_type'),
                        'identity_number'           => $this->input->post('identity_number'),
                        'bank_account'              => $this->input->post('bank_account'),
                        'bank_account_name'         => $this->input->post('bank_account_name'),
                        'npwp'                      => $this->input->post('npwp'),
                        'basic_salary'              => $this->input->post('basic_salary'),
                        'tanggal_bergabung'         => $this->input->post('tanggal_bergabung'),
                        'left_plafon_biaya_dinas'           => $this->input->post('plafon_biaya_dinas'),
                        'left_plafon_biaya_kesehatan'       => $this->input->post('plafon_biaya_kesehatan'),
                        'left_cuti'                         => $this->input->post('cuti'),
                        'updated_by'                        => config_item('auth_person_name'),
                        'updated_at'                        => date('Y-m-d H:i:s'),
                    );

                    if ($this->model->insert($form_data)){
                        $return['type'] = 'success';
                        $return['info'] = 'Employee ' . $this->input->post('name') .' updated.';
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
                $return['info'] = 'Position ' . $this->input->post('position') .' deleted.';
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
}
