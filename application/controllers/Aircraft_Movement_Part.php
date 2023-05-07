<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Aircraft_Movement_Part extends MY_Controller
{
    protected $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['aircraft_movement_part'];
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
                $col[] = print_date(strtoupper($row['date_of_ajlb']), 'd M Y');
                $col[] = '';
                $col[] = print_string($row['aircraft_register']);
                $col[] = print_string($row['aircraft_type']);
                $col[] = print_string($row['aircraft_base']);
                $col[] = print_string($row['remove_description']);
                $col[] = print_string($row['remove_part_number']);
                $col[] = print_string($row['remove_serial_number']);
                $col[] = print_string($row['install_serial_number']);
                $col[] = print_date($row['remove_date'], 'd M Y');
                $col[] = print_string($row['remove_tsn']);
                $col[] = print_string($row['remove_tso']);
                $col[] = print_string($row['condition']);
                $col[] = ($row['install_date']!=null)?print_date($row['install_date'], 'd M Y'):'';
                $col[] = print_string($row['install_tsn']);
                $col[] = print_string($row['install_tso']);
                $col[] = print_string($row['pic']);
                $col[] = print_string($row['category']);
                $col[] = print_string($row['remarks']);

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
        $source =  $_SESSION['request']['request_to'] == 0 ? "Budget Control" : "MRP";
        $this->data['page']['title']            = 'Aircraft Movement Part';
        $this->data['grid']['column']           = $this->model->getHeader();
        $this->data['grid']['data_source']      = site_url($this->module['route'] . '/index_data_source');
        $this->data['grid']['fixed_columns']    = 2;
        $this->data['grid']['summary_columns']  = array();
        $this->data['grid']['order_columns']    = array(
            // 0   => array( 0 => 2,  1 => 'desc' ),
            0   => array( 0 => 1,  1 => 'desc' ),
            1   => array( 0 => 2,  1 => 'desc' ),
            2   => array( 0 => 3,  1 => 'desc' ),
            3   => array( 0 => 4,  1 => 'desc' ),
            4   => array( 0 => 5,  1 => 'desc' ),
            5   => array( 0 => 6,  1 => 'desc' ),
            6   => array( 0 => 7,  1 => 'desc' ),
            
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
        $this->authorized($this->module, 'create');


        if ($type !== NULL){
            $type = urldecode($type);

            $_SESSION['movement_part']['items']                     = array();
            $_SESSION['movement_part']['type']                      = $type;
            

            redirect($this->module['route'] .'/create');
        }

        if (!isset($_SESSION['movement_part']))
            redirect($this->module['route']);

        $this->data['page']['content']    = $this->module['view'] .'/component_status/create';
        $this->data['page']['offcanvas']  = $this->module['view'] .'/component_status/create_offcanvas_add_item';
        $this->data['page']['title']      = 'Create '.str_replace("_", " & ", $_SESSION['movement_part']['type']).' Part';
        $this->data['page']['route']      = site_url($this->module['route'] . '/index_aircraft_component/' . $_SESSION['component']['aircraft_id']);

        // if($_SESSION['movement_part']['type']=='remove'){
        //     $this->render_view($this->module['view'] .'/create');
        // }else{
        $this->render_view($this->module['view'] .'/create');
        // }        
    }

    public function save()
    {
        if ($this->input->is_ajax_request() == FALSE)
        redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'create') == FALSE){
            $data['success'] = FALSE;
            $data['message'] = 'You are not allowed to save this Document!';
        } else {
            $errors = array();

            if (!empty($errors)){
                $data['success'] = FALSE;
                $data['message'] = implode('<br />', $errors);
            } else {
                if ($this->model->save()){
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

    public function add_item()
    {
        $this->authorized($this->module, 'create');

        if (isset($_POST) && !empty($_POST)){
            $_SESSION['movement_part']['items'][] = array(
                'aircraft_register'             => $this->input->post('aircraft_register'),
                'group_part'                    => $this->input->post('group_part'),
                'date_of_ajlb'                  => $this->input->post('date_of_ajlb'),
                'component_remove_id'           => $this->input->post('component_remove_id'),
                'remove_part_number'            => trim(strtoupper($this->input->post('remove_part_number'))),
                'remove_serial_number'          => trim(strtoupper($this->input->post('remove_serial_number'))),
                'remove_description'            => trim(strtoupper($this->input->post('remove_description'))),            
                'remove_alternate_part_number'  => trim(strtoupper($this->input->post('remove_alternate_part_number'))),
                'remove_date'                   => $this->input->post('remove_date'),
                'remove_tsn'                    => $this->input->post('remove_tsn'),
                'remove_tso'                    => $this->input->post('remove_tso'),
                'remove_category'               => $this->input->post('remove_category'),
                'pic'                           => strtoupper($this->input->post('pic')),
                'condition'                     => $this->input->post('condition'),
                'remarks'                       => $this->input->post('remarks'),
                'quantity'                      => null,
                'source'                        => ($_SESSION['movement_part']['type']=='remove')? NULL:$this->input->post('source'), 
                'source_item_id'                => ($_SESSION['movement_part']['type']=='remove')? NULL:$this->input->post('source_item_id'),     
                'component_install_id'          => ($_SESSION['movement_part']['type']=='remove')? NULL:$this->input->post('component_install_id'),
                'install_part_number'           => ($_SESSION['movement_part']['type']=='remove')? NULL:$this->input->post('install_part_number'),
                'install_serial_number'         => ($_SESSION['movement_part']['type']=='remove')? NULL:$this->input->post('install_serial_number'),
                'install_description'           => ($_SESSION['movement_part']['type']=='remove')? NULL:$this->input->post('install_description'),
                'install_alternate_part_number' => ($_SESSION['movement_part']['type']=='remove')? NULL:$this->input->post('install_alternate_part_number'),
                'install_date'                  => ($_SESSION['movement_part']['type']=='remove')? NULL:$this->input->post('install_date'),
                'install_tsn'                   => ($_SESSION['movement_part']['type']=='remove')? NULL:$this->input->post('install_tsn'),
                'install_tso'                   => ($_SESSION['movement_part']['type']=='remove')? NULL:$this->input->post('install_tso'),   
                'issuance_document_number'      => ($_SESSION['movement_part']['type']=='remove')? NULL:$this->input->post('issuance_document_number'),
                'interval_component_install'            => ($_SESSION['movement_part']['type']=='remove')? NULL:$this->input->post('interval_component_install'),
                'interval_satuan_component_install'     => ($_SESSION['movement_part']['type']=='remove')? NULL:$this->input->post('interval_satuan_component_install'),               
            );            
        }

        redirect($this->module['route'] .'/create');
    }

    public function search_item_by_source()
    {
        // if ($this->input->is_ajax_request() === FALSE)
        //   redirect($this->modules['secure']['route'] .'/denied');

        $source     = $this->input->post('source');
        $aircraft   = $this->input->post('aircraft');
        $remove_part_number   = $this->input->post('remove_part_number');
        $entities   = $this->model->searchItemBySource($source,$aircraft,$remove_part_number);  

        echo json_encode($entities);
    }
}
