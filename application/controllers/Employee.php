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
                $col['DT_RowAttr']['onClick'] = '$(this).redirect("_blank");';
                $col['DT_RowAttr']['data-href'] = site_url($this->module['route'] .'/detail/'. $row['employee_id']);

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

    public function detail($employee_number)
    {
        $this->authorized($this->module, 'index');
        $id = 

        $entity = $this->model->findById($id);

        $this->data['page']['content']    = $this->module['view'] .'/create';
        $this->data['page']['offcanvas']  = $this->module['view'] .'/create_offcanvas_add_item';
        $this->data['entity']             = $entity;
        $this->data['title']                = $entity['name'].' '.$entity['employee_number'];

        $this->render_view($this->module['view'] .'/detail');
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
                        'employee_id'                       => $this->input->post('employee_id'),
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
                        'employee_id'                       => $this->model->get_unused_id(),
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
                        //... 1st column is employee number
                        $employee_number = (trim($col[0]) == '') ? null : trim($col[0]);
                        $data[$row]['employee_number'] = $employee_number;

                        if ($employee_number === null)
                            $errors[] = 'Line ' . $row . ': employee number is null!';

                        if($this->model->isEmployeeNumberExists($employee_number))
                            $errors[] = 'Line ' . $row . ': Duplicated EMployee Number!';

                        //... 2nd column is name
                        $name = (trim($col[1]) == '') ? null : trim(strtoupper($col[1]));
                        $data[$row]['name'] = $name;

                        if ($name === null)
                            $errors[] = 'Line ' . $row . ': Column name is null!';

                        //... 3rd column is department
                        $department = (trim($col[2]) == '') ? null : trim($col[2]);
                        $data[$row]['department'] = $department;

                        if ($department === null)
                            $errors[] = 'Line ' . $row . ': employee number is null!';

                        if(!isDepartmentExists($department))
                            $errors[] = 'Line ' . $row . ': Department '.$department.' not exists!';

                        //... 4th column is date_of_birth
                        $date_of_birth = (trim($col[3]) == '') ? null : trim($col[3]);
                        $data[$row]['date_of_birth'] = $date_of_birth;
                        
                        //... 5th column is gender
                        $gender = (trim($col[4]) == '') ? null : strtolower(trim($col[4]));
                        $data[$row]['gender'] = $gender;

                        if ($gender === null)
                            $errors[] = 'Line ' . $row . ': gender is null!';
                        
                        if(!in_array($gender,['male','female'])){
                            $errors[] = 'Line ' . $row . ': gender is between male or female !';
                        }

                        //... 6th column is religion
                        $religion = (trim($col[5]) == '') ? null : trim($col[5]);
                        $data[$row]['religion'] = $religion;

                        //... 7th column is phone_number
                        $phone_number = (trim($col[6]) == '') ? null : trim($col[6]);
                        $data[$row]['gender'] = $phone_number;

                        if ($phone_number === null)
                            $errors[] = 'Line ' . $row . ': phone_number is null!';
                        
                        //... 8th column is marital_status
                        $marital_status = (trim($col[7]) == '') ? null : strtolower(trim($col[7]));
                        $data[$row]['marital_status'] = $marital_status;

                        if ($marital_status === null)
                            $errors[] = 'Line ' . $row . ': marital_status is null!';

                        if(!in_array($marital_status,['married','single'])){
                            $errors[] = 'Line ' . $row . ': marital_status is between married or single !';
                        }

                        //... 9th column is email
                        $email = (trim($col[8]) == '') ? null : trim($col[8]);
                        $data[$row]['email'] = $email;

                        if ($email === null)
                            $errors[] = 'Line ' . $row . ': marital_status is null!';

                        //... 10th column is address
                        $address = (trim($col[9]) == '') ? null : trim($col[9]);
                        $data[$row]['address'] = $address;

                        //... 11th column is jabatan
                        $jabatan = (trim($col[10]) == '') ? null : trim($col[10]);
                        $data[$row]['jabatan'] = $jabatan;

                        if ($jabatan === null)
                            $errors[] = 'Line ' . $row . ': jabatan is null!';
                        
                        if(!$this->model->isPositionExists($jabatan))
                            $errors[] = 'Line ' . $row . ': Jabatan '.$jabatan.' not exists!';

                        //... 12th column is tipe_identitas
                        $tipe_identitas = (trim($col[11]) == '') ? null : trim($col[11]);
                        $data[$row]['tipe_identitas'] = $tipe_identitas;

                        if ($tipe_identitas === null)
                            $errors[] = 'Line ' . $row . ': tipe_identitas is null!';
                        
                        //... 13th column is identitas_number
                        $identitas_number = (trim($col[12]) == '') ? null : trim($col[12]);
                        $data[$row]['identitas_number'] = $identitas_number;

                        if ($identitas_number === null)
                            $errors[] = 'Line ' . $row . ': identitas_number is null!';

                        //... 14th column is base
                        $base = (trim($col[13]) == '') ? null : trim($col[13]);
                        $data[$row]['base'] = $base;

                        if ($base === null)
                            $errors[] = 'Line ' . $row . ': base is null!';

                        if(!$this->model->isWarehouseExists($base)){
                            $errors[] = 'Line ' . $row . ': base is no exists!';
                        }

                        //... 15th column is base
                        $bank_account_number = (trim($col[14]) == '') ? null : trim($col[14]);
                        $data[$row]['bank_account_number'] = $bank_account_number;

                        if ($bank_account_number === null)
                            $errors[] = 'Line ' . $row . ': bank_account_number is null!';

                        //... 16th column is bank_name
                        $bank_name = (trim($col[15]) == '') ? null : trim($col[15]);
                        $data[$row]['bank_name'] = $bank_name;

                        if ($bank_name === null)
                            $errors[] = 'Line ' . $row . ': bank_name is null!';

                        //... 17th column is npwp
                        $npwp = (trim($col[16]) == '') ? null : trim($col[16]);
                        $data[$row]['npwp'] = $npwp;

                        if ($npwp === null)
                            $errors[] = 'Line ' . $row . ': npwp is null!';

                        //... 18th column is basic_salary
                        $basic_salary = (trim($col[17]) == '') ? null : trim($col[17]);
                        $data[$row]['basic_salary'] = $basic_salary;

                        if ($basic_salary === null)
                            $errors[] = 'Line ' . $row . ': basic_salary is null!';

                        //... 19th column is tanggal_bergabung
                        $tanggal_bergabung = (trim($col[18]) == '') ? null : trim($col[18]);
                        $data[$row]['tanggal_bergabung'] = $tanggal_bergabung;

                        if ($tanggal_bergabung === null)
                            $errors[] = 'Line ' . $row . ': tanggal_bergabung is null!';

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

        redirect($this->module['route']);
    }
}
