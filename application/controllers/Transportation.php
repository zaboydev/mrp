<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Transportation extends MY_Controller
{
    protected $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['master_transportation'];
        $this->load->model($this->module['model'], 'model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('upload');
        $this->load->helper('string');
        $this->data['module'] = $this->module;
    }

    public function index()
    {
        $this->authorized($this->module, 'index');

        $this->data['page']['title']        = 'Transportation';
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
                $no++;
                $col = array();
                $col[] = print_number($no);
                $col[] = print_string($row['transportation']);
                $col[] = print_string($row['contact_name']);
                $col[] = print_string($row['contact_number']);
                $col[] = print_string($row['contact_email']);
                $col[] = print_date($row['updated_at']);
                $col['DT_RowId'] = 'row_'. $row['id'];
                $col['DT_RowData']['pkey']  = $row['id'];
                $col['DT_RowAttr']['onClick']     = '$(this).popup();';
                $col['DT_RowAttr']['data-target'] = '#data-modal';
                $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] .'/edit/'. $row['id']);

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
                if ($this->model->isTransportationExists($this->input->post('transportation'), $this->input->post('transportation_exception'))){
                    $return['type'] = 'danger';
                    $return['info'] = 'Duplicate transportation Name! transportation Name '. $this->input->post('transportation') .' already exists.';
                } else {
                    $position_data = array(
                        'transportation'    => strtoupper($this->input->post('transportation')),
                        'contact_name'      => strtoupper($this->input->post('contact_name')),
                        'contact_number'    => $this->input->post('contact_number'),
                        'contact_email'     => $this->input->post('contact_email'),
                        'updated_by'        => config_item('auth_person_name'),
                        'updated_at'        => date('Y-m-d H:i:s'),
                    );

                    $criteria = $this->input->post('id');

                    if ($this->model->update($position_data, $criteria)){
                        $return['type'] = 'success';
                        $return['info'] = 'Expense Name ' . $this->input->post('expense_name') .' updated.';
                    } else {
                        $return['type'] = 'danger';
                        $return['info'] = 'There are error while updating data. Please try again later.';
                    }
                }
            } else {
                if ($this->model->isTransportationExists($this->input->post('transportation'))){
                    $return['type'] = 'danger';
                    $return['info'] = 'Duplicate transportation Name! transportation Name '. $this->input->post('transportation') .' already exists.';
                } else {

                    $position_data = array(
                        'transportation'    => strtoupper($this->input->post('transportation')),
                        'contact_name'      => strtoupper($this->input->post('contact_name')),
                        'contact_number'    => $this->input->post('contact_number'),
                        'contact_email'     => $this->input->post('contact_email'),
                        'created_by'    => config_item('auth_person_name'),
                        'created_at'    => date('Y-m-d H:i:s'),
                        'updated_by'    => config_item('auth_person_name'),
                        'updated_at'    => date('Y-m-d H:i:s'),
                    );

                    if ($this->model->insert($position_data)){
                        $return['type'] = 'success';
                        $return['info'] = 'transportation Name for ' . $this->input->post('transportation') .' created.';
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
        // ========================= ACCESS DENIED ========================== //
        $this->authorized($this->module, 'import');

        // ========================= ACCESS GRANTED ========================= //

        //... load library to build form and validate it
        $this->load->library('form_validation');

        /**
         * Processing data
         * if form submitted
         */
        if (isset($_POST) && !empty($_POST)){
            //... set rules of validation
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
                    while (($col = fgetcsv($handle, 1024, $delimiter)) !== FALSE)
                    {
                        $row++;

                        /******************
                         * CHECK COLUMN 0
                         ******************/
                        $position = trim(strtoupper($col[0]));
                        $data[$row]['position'] = $position;

                        if ($position == '')
                        $errors[] = 'Line '. $row .': position is null!';

                        if ($this->model->isDuplicatePosition($position))
                            $errors[] = 'Line '. $row .': Duplicate position '. $position;

                        /***************************************************
                         * CHECK COLUMN 1
                         ***********************************/
                        $code = (trim($col[1]) == '') ? null : trim($col[1]);
                        $data[$row]['code'] = $code;

                        // if ($code == '')
                        // $errors[] = 'Line '. $row .': code is null!';

                        if ($code != '' && $this->model->isDuplicateCode($code))
                        $errors[] = 'Line '. $row .': Duplicate code '. $code;

                        /***************************************************
                         * CHECK COLUMN 2
                         ***********************************/
                        $level = (trim($col[2]) == '') ? null : trim(strtoupper($col[2]));
                        $data[$row]['level'] = $level;

                        if ($level == '')
                        $errors[] = 'Line '. $row .': level is null!';

                        if (!$this->model->isLevelExists($level))
                        $errors[] = 'Line '. $row .': Level '. $level.' not Exists. Please Register it First!!';

                        /***************************************************
                         * CHECK COLUMN 3
                         ***********************************/
                        $notes = (trim($col[2]) == '') ? null : trim($col[2]);
                        $data[$row]['notes'] = $notes;


                    }
                    fclose($handle);

                    if (empty($errors)){
                        /**
                         * Insert into user table
                         */
                        if ($this->model->import($data)){
                            //... send message to view
                            $this->session->set_flashdata('alert', array(
                                'type' => 'success',
                                'info' => count($data)." data has been imported!"
                            ));

                            redirect($this->module['route']);
                        }
                    } else {
                        foreach ($errors as $key => $value){
                            $err[] = "\n#". $value;
                        }

                        $this->session->set_flashdata('alert', array(
                            'type' => 'danger',
                            'info' => "There are errors on data\n#". implode("\n#", $errors)
                        ));
                    }
                } else {
                    $this->session->set_flashdata('alert', array(
                        'type' => 'danger',
                        'info' => 'Cannot open file!'
                    ));
                }
            }
        }

        //... set view data
        $this->data['page_title'] = lang('page_title_import');
        $this->data['page_content'] = $this->module['view'] .'/import';

        $this->render_view();
    }
}
