<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Aircraft_Component_Plan extends MY_Controller
{
    protected $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['aircraft_component_plan'];
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
            $entities = $this->model->getIndex();
            $data     = array();
            $no       = $_POST['start'];
            $total = array();

            foreach ($entities as $row) {
                $no++;
                $col = array();
                $col[] = print_number($no); 
                $col[] = print_string($row['status']);   
                $col[] = print_date($row['date'],'d M Y'); 
                $col[] = print_string($row['year_plan']);
                $col[] = print_string($row['aircraft_register']);
                $col[] = print_string($row['aircraft_type']);   
                $col[] = print_string($row['description']);  
                $col[] = print_string($row['part_number']);
                $col[] = print_string($row['alternate_part_number']); 
                $col[] = print_string($row['remarks']);

                $col['DT_RowId'] = 'row_'. $row['id'];
                $col['DT_RowData']['pkey']  = $row['id'];

                if ($this->has_role($this->module, 'edit')){
                    $col['DT_RowAttr']['onClick']     = '';
                    $col['DT_RowAttr']['data-id']     = $row['id'];
                    $col['DT_RowAttr']['data-target'] = '#data-modal';
                    $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] .'/edit/'. $row['id']);
                }

                $data[] = $col;
            }

            $result = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->model->countIndex(),
                "recordsFiltered" => $this->model->countIndexFiltered(),
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
        $this->data['page']['title']            = 'Aircraft Component Plan';
        $this->data['grid']['column']           = $this->model->getSelectedColumns();
        $this->data['grid']['data_source']      = site_url($this->module['route'] . '/index_data_source');
        $this->data['grid']['fixed_columns']    = 6;
        $this->data['grid']['summary_columns']  = NULL;
        $this->data['grid']['order_columns']    = array(
            // 0   => array( 0 => 2,  1 => 'desc' ),
            // 0   => array( 0 => 1,  1 => 'asc' ),
            // 1   => array( 0 => 3,  1 => 'asc' ),
            // 2   => array( 0 => 4,  1 => 'asc' ),
            // 3   => array( 0 => 5,  1 => 'asc' ),
            // 4   => array( 0 => 9,  1 => 'asc' ),
            
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

    public function create()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'create') === FALSE) {
            $return['type'] = 'danger';
            $return['info'] = "You don't have permission to create data!";
        } else {
            $return['type'] = 'success';
            $return['info'] = $this->load->view($this->module['view'] . '/create', $this->data, TRUE);
        }

        echo json_encode($return);        
    }

    public function edit($id)
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'edit') === FALSE) {
            $return['type'] = 'danger';
            $return['info'] = "You don't have permission to edit this data!";
        } else {
            $entity = $this->model->findById($id);

            $this->data['entity'] = $entity;

            $return['type'] = 'success';
            $return['info'] = $this->load->view($this->module['view'] . '/edit', $this->data, TRUE);
        }

        echo json_encode($return);
    }

    public function save()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'save') === FALSE) {
            $return['type'] = 'danger';
            $return['info'] = "You don't have permission to access this page!";
        } else {
            if ($this->input->post('id')) {
                if ($this->model->update($this->input->post('id'))) {
                    $return['type'] = 'success';
                    $return['info'] = 'Component Planning ' . $this->input->post('part_number') . ' updated.';
                } else {
                    $return['type'] = 'danger';
                    $return['info'] = 'There are error while updating data. Please try again later.';
                }
            } else {
                if ($this->model->insert()) {
                    $return['type'] = 'success';
                    $return['info'] = 'Component Planning ' . $this->input->post('part_number') . ' created.';
                } else {
                    $return['type'] = 'danger';
                    $return['info'] = 'There are error while updating data. Please try again later.';
                }
            }
        }

        echo json_encode($return);
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

    public function search_component_aircraft()
    {
        // if ($this->input->is_ajax_request() === FALSE)
        //   redirect($this->modules['secure']['route'] .'/denied');

        $entities = $this->model->searchComponentAircraft();  

        echo json_encode($entities);
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

    public function search_items_by_part_number()
    {
        if ($this->input->is_ajax_request() === FALSE)
        redirect($this->modules['secure']['route'] .'/denied');

        $entities = $this->model->searchItemsByPartNumber(config_item('auth_inventory'));

        foreach ($entities as $key => $value){
            $entities[$key]['label'] = $value['description'].' || P/N : '.$value['part_number'].' || S/N : '.$value['serial_number'];
        }

        echo json_encode($entities);
    }
}
