<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Aircraft_Component_Status extends MY_Controller
{
    protected $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['aircraft_component_status'];
        $this->load->model($this->module['model'], 'model');
        $this->data['module'] = $this->module;
    }

    public function index_data_source()
    {
        if ($this->input->is_ajax_request() === FALSE)
        redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'index') === FALSE) {
            $return['type'] = 'danger';
            $return['info'] = "You don't have permission to access this page!";
        } else {
            $entities = $this->model->getIndexComponentStatus();
            $data     = array();
            $no       = $_POST['start'];
            $total = array();

            foreach ($entities as $row) {
                $no++;
                if ($row['status'] == 'WAITING FOR CHECKED BY COM' && (config_item('auth_role')=='CHIEF OF MAINTANCE' || config_item('auth_role')=='SUPER ADMIN')) {
                    $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                }else{
                    $col[] = print_number($no);
                }                
                $col[] = print_string(strtoupper($row['status']));
                $col[] = print_string($row['nama_pesawat']);
                $col[] = print_string($row['base']);
                $col[] = print_date($row['status_date']);
                $col[] = print_string($row['tsn']);
                $col[] = print_string($row['notes']);
                $col[] = print_string($row['prepared_by']);

                $col['DT_RowId'] = 'row_'. $row['id'];
                $col['DT_RowData']['pkey']  = $row['id'];

                if ($this->has_role($this->module, 'info')){
                    $col['DT_RowAttr']['onClick']     = '';
                    $col['DT_RowAttr']['data-id']     = $row['id'];
                    $col['DT_RowAttr']['data-target'] = '#data-modal';
                    $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] .'/info/'. $row['id']);
                }

                $data[] = $col;
            }

            $result = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->model->countIndexComponentStatus(),
                "recordsFiltered" => $this->model->countIndexFilteredComponentStatus(),
                "data" => $data,
                "total" => array(
                    
                )
            );
        }

        echo json_encode($result);
    }

    public function index()
    {
        $this->authorized($this->module, 'index');
        $source =  $_SESSION['request']['request_to'] == 0 ? "Budget Control" : "MRP";
        $this->data['page']['title']            = $this->module['label'] . strtoupper(" " . $source);
        $this->data['grid']['column']           = array_values($this->model->getSelectedColumnsComponentStatus());
        $this->data['grid']['data_source']      = site_url($this->module['route'] . '/index_data_source');
        $this->data['grid']['fixed_columns']    = 2;
        $this->data['grid']['summary_columns']  = array();
        $this->data['grid']['order_columns']    = array(
            // 0   => array( 0 => 2,  1 => 'desc' ),
            0   => array( 0 => 1,  1 => 'desc' ),
            1   => array( 0 => 2,  1 => 'desc' ),
            2   => array( 0 => 3,  1 => 'asc' ),
            3   => array( 0 => 4,  1 => 'asc' ),
            4   => array( 0 => 5,  1 => 'asc' ),
            5   => array( 0 => 6,  1 => 'asc' ),
            6   => array( 0 => 7,  1 => 'asc' ),
            
        );

        $this->render_view($this->module['view'] . '/index');
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
            $return['info'] = 'Pesawat ' . $this->input->post('nama_pesawat') .' deleted.';
        } else {
            $return['type'] = 'danger';
            $return['info'] = 'There are error while trying to delete data. Please try again later.';
        }
        }

        echo json_encode($return);
    }

    public function create($type=NULL)
    {
        $this->authorized($this->module, 'create_component_status');


        if ($type !== NULL){
            $type = urldecode($type);

            $_SESSION['component_status']['items']                  = array();
            $_SESSION['component_status']['aircraft_id']            = null;
            $_SESSION['component_status']['aircraft_code']          = null;
            $_SESSION['component_status']['base']                   = null;
            $_SESSION['component_status']['tsn']                    = null;
            $_SESSION['component_status']['notes']                  = null;
            $_SESSION['component_status']['type']                   = $type;
            $_SESSION['component_status']['status_date']            = date('Y-m-d');
            $_SESSION['component_status']['prepared_by']            = config_item('auth_person_name');

            redirect($this->module['route'] .'/create');
        }

        if (!isset($_SESSION['component_status']))
            redirect($this->module['route']);

        $this->data['page']['content']    = $this->module['view'] .'/component_status/create';
        $this->data['page']['offcanvas']  = $this->module['view'] .'/component_status/create_offcanvas_add_item';
        $this->data['page']['title']      = "Create Aircraft Component Status";
        $this->data['page']['route']      = site_url($this->module['route'] . '/index_aircraft_component/' . $_SESSION['component']['aircraft_id']);

        $this->render_view($this->module['view'] .'/create');
    }

    public function save()
    {
        if ($this->input->is_ajax_request() == FALSE)
        redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'create_component_status') == FALSE){
        $data['success'] = FALSE;
        $data['message'] = 'You are not allowed to save this Document!';
        } else {
        $errors = array();

        if (!empty($errors)){
            $data['success'] = FALSE;
            $data['message'] = implode('<br />', $errors);
        } else {
            if ($this->model->saveComponentStatus()){
            unset($_POST);

            $data['success'] = TRUE;
            $data['message'] = 'Document '. $this->input->post('document_number') .' has been saved. You will redirected now.';
            } else {
            $data['success'] = FALSE;
            $data['message'] = 'Error while saving this document. Please ask Technical Support.';
            }
        }
        }

        echo json_encode($data);
    }

    public function discard()
    {
        $this->authorized($this->module['permission']['document']);

        unset($_SESSION['component_status']);

        // redirect($this->module['route'].'/index_aircraft_component/'.$aircraft_id);
        redirect($this->module['route']);
    }

    public function info($id)
    {
        $entity = $this->model->findById($id);

        echo json_encode($entity);
    }    

    public function set_aircraft_id($aircraft_id)
    {

        $this->authorized($this->module, 'create_component_status');

        $aircraft_id = urldecode($aircraft_id);

        $aircraft = $this->model->findById($aircraft_id);
        $aircraft_component = $this->model->findAircraftComponetByAircraftId($aircraft_id);

        $_SESSION['component_status']['aircraft_id']              = $aircraft_id;
        $_SESSION['component_status']['items']                    = $aircraft_component;
        $_SESSION['component_status']['aircraft_code']            = $aircraft['nama_pesawat'];
        $_SESSION['component_status']['base']                     = $aircraft['base'];

        redirect($this->module['route'] . '/create');
    }

    public function set_tsn()
    {
        if ($this->input->is_ajax_request() === FALSE)
        redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['component_status']['tsn'] = $_GET['data'];
    }

    public function set_notes()
    {
        if ($this->input->is_ajax_request() === FALSE)
        redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['component_status']['notes'] = $_GET['data'];
    }

    public function set_status_date()
    {
        if ($this->input->is_ajax_request() === FALSE)
        redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['component_status']['status_date'] = $_GET['data'];
    }

    public function set_prepared_by()
    {
        if ($this->input->is_ajax_request() === FALSE)
        redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['component_status']['prepared_by'] = $_GET['data'];
    }
}
