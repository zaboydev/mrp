<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Business_Trip_Request extends MY_Controller
{
    protected $module;
    protected $id_item=0;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['business_trip_request'];
        $this->load->model($this->module['model'], 'model');
        $this->load->helper($this->module['helper']);
        $this->load->library('upload');        
        $this->load->library('email');
        $this->data['module'] = $this->module;
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
            $data     = array();
            $no       = $_POST['start'];
            $quantity     = array();
            $unit_value   = array();
            $total_value  = array();

            foreach ($entities as $row){
                $cost_center = findCostCenter($row['annual_cost_center_id']);
                $cost_center_code = $cost_center['cost_center_code'];
                $cost_center_name = $cost_center['cost_center_name'];
                $department_name = $cost_center['department_name'];         
                $no++;
                $col = array();
                if (is_granted($this->module, 'approval')){
                    if($row['status']=='WAITING APPROVAL BY HEAD DEPT' && in_array($department_name,config_item('head_department')) && $row['head_dept']==config_item('auth_username')){
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    }elseif($row['status']=='WAITING APPROVAL BY HR MANAGER' && in_array(config_item('auth_username'),list_username_in_head_department(11))){
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    }else{
                        $col[] = print_number($no);
                    }                    
                }else{
                    $col[] = print_number($no);
                }                
                $col[] = print_string($row['document_number']);
                $col[] = print_string($row['status']);
                $col[] = print_date($row['date']);
                $col[] = print_string($cost_center['cost_center_name']);
                $col[] = print_string($row['person_name']);
                $col[] = print_string($row['business_trip_destination']);
                $col[] = print_string($row['duration']);
                $col[] = print_date($row['start_date'], 'd M Y').' s/d '.print_date($row['end_date'], 'd M Y');
                $col[] = print_string($row['notes']);
                if($row['status']=='approved' || $row['status']=='closed'){
                    $col[] = '';
                }else{
                    if (is_granted($this->module, 'approval') === TRUE && in_array($row['status'],['WAITING APPROVAL BY HEAD DEPT','WAITING APPROVAL BY HR MANAGER'])) {
                        $col[] = '<input type="text" id="note_' . $row['id'] . '" autocomplete="off"/>';
                    }else{
                        $col[] = '';
                    }
                }
                
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
                "draw"            => $_POST['draw'],
                "recordsTotal"    => $this->model->countIndex(),
                "recordsFiltered" => $this->model->countIndexFiltered(),
                "data"            => $data,
                "total"           => array(
                    
                )
            );
        }

        echo json_encode($result);
    }

    public function index()
    {
        $this->authorized($this->module, 'index');

        $this->data['page']['title']            = $this->module['label'];
        $this->data['grid']['column']           = $this->model->getSelectedColumns();
        $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source');
        $this->data['grid']['fixed_columns']    = 3;
        $this->data['grid']['summary_columns']  = array();

        $this->data['grid']['order_columns']    = array(
            0   => array( 0 => 1,  1 => 'desc' ),
        );

        $this->render_view($this->module['view'] .'/index');
    }

    public function set_doc_number()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        if (empty($_GET['data']))
            $number = travel_on_duty_last_number();
        else
            $number = $_GET['data'];

        $_SESSION['business_trip']['document_number'] = $number;
    }

    public function set_person_in_charge()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['person_in_charge'] = $_GET['data'];
    }

    public function set_occupation()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['occupation'] = $_GET['data'];
    }

    public function set_dateline()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['dateline'] = $_GET['data'];
    }

    public function set_duration()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['duration'] = $_GET['data'];
    }

    public function set_start_date()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['start_date'] = $_GET['data'];
    }

    public function set_end_date()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['end_date'] = $_GET['data'];
    }

    public function set_head_dept()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['head_dept'] = $_GET['data'];
    }

    public function set_id_number()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['id_number'] = $_GET['data'];
    }

    public function set_phone_number()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['phone_number'] = $_GET['data'];
    }

    public function search_person_in_charge()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $entities = available_user(array('person_name', 'username'));

        foreach ($entities as $user){
            $arr_result[] = $user['person_name'];
        }

        echo json_encode($arr_result);
    }

    public function set_from_base()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['from_base'] = $_GET['data'];
    }

    public function set_transportation()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['transportation'] = $_GET['data'];
    }

    public function set_destination()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['business_trip_destination_id'] = $_GET['data'];
    }

    public function set_notes()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['notes'] = $_GET['data'];
    }

    public function set_approval_notes()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['business_trip']['approval_notes'] = $_GET['data'];
    }

    public function create($annual_cost_center_id = NULL)
    {
        $this->authorized($this->module, 'create');

        if ($annual_cost_center_id !== NULL){
            $annual_cost_center_id = urldecode($annual_cost_center_id);
            $cost_center = findCostCenter($annual_cost_center_id);
            $cost_center_code = $cost_center['cost_center_code'];
            $cost_center_name = $cost_center['cost_center_name'];          
            $department_id    = $cost_center['department_id'];

            $_SESSION['business_trip']['items']                     = array();
            $_SESSION['business_trip']['annual_cost_center_id']     = $annual_cost_center_id;
            $_SESSION['business_trip']['cost_center_id']            = $cost_center_id;
            $_SESSION['business_trip']['cost_center_name']          = $cost_center_name;
            $_SESSION['business_trip']['cost_center_code']          = $cost_center_code;
            $_SESSION['business_trip']['document_number']           = travel_on_duty_last_number();
            $_SESSION['business_trip']['format_number']             = travel_on_duty_format_number();
            $_SESSION['business_trip']['date']                      = date('Y-m-d');
            $_SESSION['business_trip']['created_by']                = config_item('auth_person_name');
            $_SESSION['business_trip']['warehouse']                 = config_item('auth_warehouse');
            $_SESSION['business_trip']['notes']                     = NULL;
            $_SESSION['business_trip']['person_in_charge']          = NULL;
            $_SESSION['business_trip']['department_id']             = $department_id;
            $_SESSION['business_trip']['head_dept']                 = NULL;
            $_SESSION['business_trip']['business_trip_destination_id']  = NULL;
            $_SESSION['business_trip']['duration']                      = NULL;
            $_SESSION['business_trip']['dateline']                      = NULL;
            $_SESSION['business_trip']['occupation']                    = NULL;
            $_SESSION['business_trip']['phone_number']                  = NULL;
            $_SESSION['business_trip']['id_number']                     = NULL;
            $_SESSION['business_trip']['start_date']                    = NULL;
            $_SESSION['business_trip']['end_date']                      = NULL;
            $_SESSION['business_trip']['from_base']                      = NULL;
            $_SESSION['business_trip']['transportation']                      = NULL;

            redirect($this->module['route'] .'/create');
        }

        if (!isset($_SESSION['business_trip']))
          redirect($this->module['route']);

        $this->data['page']['content']    = $this->module['view'] .'/create';
        $this->data['page']['offcanvas']  = $this->module['view'] .'/create_offcanvas_add_item';

        $this->render_view($this->module['view'] .'/create');
    }

    public function info($id)
    {
        if ($this->input->is_ajax_request() === FALSE)
        redirect($this->modules['secure']['route'] .'/denied');

        if (is_granted($this->module, 'info') === FALSE){
        $return['type'] = 'denied';
        $return['info'] = "You don't have permission to access this data. You may need to login again.";
        } else {
        $entity = $this->model->findById($id);

        $this->data['entity'] = $entity;

        $return['type'] = 'success';
        $return['info'] = $this->load->view($this->module['view'] .'/info', $this->data, TRUE);
        }

        echo json_encode($return);
    }

    public function print_pdf($id)
    {
        $this->authorized($this->module, 'print');

        $entity = $this->model->findById($id);

        $this->data['entity']           = $entity;
        $this->data['page']['title']    = strtoupper($this->module['label']);
        $this->data['page']['content']  = $this->module['view'] .'/print_pdf';

        $html = $this->load->view($this->pdf_theme, $this->data, true);

        $pdfFilePath = str_replace('/', '-', $entity['document_number']) .".pdf";

        $this->load->library('m_pdf');

        $pdf = $this->m_pdf->load(null, 'A4-P');
        $pdf->WriteHTML($html);
        $pdf->Output($pdfFilePath, "I");
    }

    public function edit($id,$type="edit")
    {
        $this->authorized($this->module, 'create');

        $entity = $this->model->findById($id);

        $document_number    = sprintf('%06s', substr($entity['document_number'], 0, 6));
        $format_number      = substr($entity['document_number'], 6, 21);
        $revisi             = get_count_revisi($document_number.$format_number);

        if (isset($_SESSION['business_trip']) === FALSE){
            $cost_center = findCostCenter($entity['annual_cost_center_id']);
            $cost_center_code = $cost_center['cost_center_code'];
            $cost_center_name = $cost_center['cost_center_name'];          
            $department_id    = $cost_center['department_id'];

            $_SESSION['business_trip']['annual_cost_center_id']     = $annual_cost_center_id;
            $_SESSION['business_trip']['cost_center_id']            = $cost_center_id;
            $_SESSION['business_trip']['cost_center_name']          = $cost_center_name;
            $_SESSION['business_trip']['cost_center_code']          = $cost_center_code;
            $_SESSION['business_trip']                              = $entity;
            $_SESSION['business_trip']['id']                        = $id;
            $_SESSION['business_trip']['edit_type']                 = 'edit';
            $_SESSION['business_trip']['edit']                      = $entity['document_number'];
            $_SESSION['business_trip']['document_number']           = $document_number;
            $_SESSION['business_trip']['format_number']             = $format_number.'-R'.$revisi;
            $_SESSION['business_trip']['department_id']             = $department_id;
            $_SESSION['business_trip']['person_in_charge']          = $entity['user_id'];
            $_SESSION['business_trip']['dateline']                  = print_date($entity['start_date'], 'd-m-Y').' s/d '.print_date($entity['end_date'], 'd-m-Y');
            
        }

        redirect($this->module['route'] .'/create');
        //$this->render_view($this->module['view'] .'/edit');
    }

    public function edit_approve($id)
    {
        $this->authorized($this->module, 'approval');

        $entity = $this->model->findById($id);

        $document_number    = sprintf('%06s', substr($entity['document_number'], 0, 6));
        $format_number      = substr($entity['document_number'], 6, 21);
        $revisi             = get_count_revisi($document_number.$format_number);

        if (isset($_SESSION['business_trip']) === FALSE){
            $cost_center = findCostCenter($entity['annual_cost_center_id']);
            $cost_center_code = $cost_center['cost_center_code'];
            $cost_center_name = $cost_center['cost_center_name'];          
            $department_id    = $cost_center['department_id'];

            $_SESSION['business_trip']['annual_cost_center_id']     = $annual_cost_center_id;
            $_SESSION['business_trip']['cost_center_id']            = $cost_center_id;
            $_SESSION['business_trip']['cost_center_name']          = $cost_center_name;
            $_SESSION['business_trip']['cost_center_code']          = $cost_center_code;
            $_SESSION['business_trip']                              = $entity;
            $_SESSION['business_trip']['id']                        = $id;
            $_SESSION['business_trip']['edit_type']                 = 'edit_approve';
            $_SESSION['business_trip']['edit']                      = $entity['document_number'];
            $_SESSION['business_trip']['document_number']           = $document_number;
            $_SESSION['business_trip']['format_number']             = $format_number.'-R'.$revisi;
            $_SESSION['business_trip']['department_id']             = $department_id;
            $_SESSION['business_trip']['person_in_charge']          = $entity['user_id'];
            $_SESSION['business_trip']['dateline']                  = print_date($entity['start_date'], 'd-m-Y').' s/d '.print_date($entity['end_date'], 'd-m-Y');
            
        }

        redirect($this->module['route'] .'/create_for_edit_approve');
        //$this->render_view($this->module['view'] .'/edit');
    }

    public function create_for_edit_approve($annual_cost_center_id = NULL)
    {
        $this->authorized($this->module, 'approval');

        if ($annual_cost_center_id !== NULL){
            $annual_cost_center_id = urldecode($annual_cost_center_id);
            $cost_center = findCostCenter($annual_cost_center_id);
            $cost_center_code = $cost_center['cost_center_code'];
            $cost_center_name = $cost_center['cost_center_name'];          
            $department_id    = $cost_center['department_id'];

            $_SESSION['business_trip']['items']                     = array();
            $_SESSION['business_trip']['annual_cost_center_id']     = $annual_cost_center_id;
            $_SESSION['business_trip']['cost_center_id']            = $cost_center_id;
            $_SESSION['business_trip']['cost_center_name']          = $cost_center_name;
            $_SESSION['business_trip']['cost_center_code']          = $cost_center_code;
            $_SESSION['business_trip']['document_number']           = travel_on_duty_last_number();
            $_SESSION['business_trip']['format_number']             = travel_on_duty_format_number();
            $_SESSION['business_trip']['date']                      = date('Y-m-d');
            $_SESSION['business_trip']['created_by']                = config_item('auth_person_name');
            $_SESSION['business_trip']['warehouse']                 = config_item('auth_warehouse');
            $_SESSION['business_trip']['notes']                     = NULL;
            $_SESSION['business_trip']['person_in_charge']          = NULL;
            $_SESSION['business_trip']['department_id']             = $department_id;
            $_SESSION['business_trip']['head_dept']                 = NULL;
            $_SESSION['business_trip']['business_trip_destination_id']  = NULL;
            $_SESSION['business_trip']['duration']                      = NULL;
            $_SESSION['business_trip']['dateline']                      = NULL;
            $_SESSION['business_trip']['occupation']                    = NULL;
            $_SESSION['business_trip']['phone_number']                  = NULL;
            $_SESSION['business_trip']['id_number']                     = NULL;
            $_SESSION['business_trip']['start_date']                    = NULL;
            $_SESSION['business_trip']['end_date']                      = NULL;
            $_SESSION['business_trip']['from_base']                      = NULL;
            $_SESSION['business_trip']['transportation']                      = NULL;

            redirect($this->module['route'] .'/create_for_edit_approve');
        }

        if (!isset($_SESSION['business_trip']))
          redirect($this->module['route']);

        $this->data['page']['content']    = $this->module['view'] .'/create';
        $this->data['page']['offcanvas']  = $this->module['view'] .'/create_offcanvas_add_item';

        $this->render_view($this->module['view'] .'/edit_approve');
    }

    public function save()
    {
        if ($this->input->is_ajax_request() == FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'create') == FALSE){
            $data['success'] = FALSE;
            $data['message'] = 'You are not allowed to save this Document!';
        } else {

            $document_number = $_SESSION['business_trip']['document_number'] . $_SESSION['business_trip']['format_number'];
            $errors = array();

            if ($_SESSION['business_trip']['head_dept']==NULL || $_SESSION['business_trip']['head_dept']=='') {
                $errors[] = 'Attention!! Please select one of Head Dept for Approval';
            }

            if ($_SESSION['business_trip']['notes']==NULL || $_SESSION['business_trip']['notes']=='') {
                $errors[] = 'Attention!! Please Fill Notes!!';
            }

            if (!empty($errors)){
                $data['success'] = FALSE;
                $data['message'] = implode('<br />', $errors);
            } else {
                if ($this->model->save()){
                    unset($_SESSION['business_trip']);
        
                    $data['success'] = TRUE;
                    $data['message'] = 'Document '. $document_number .' has been saved. You will redirected now.';
                } else {
                    $data['success'] = FALSE;
                    $data['message'] = 'Error while saving this document. Please ask Technical Support.';
                }
            }
        }

        echo json_encode($data);
    }

    public function save_approve()
    {
        if ($this->input->is_ajax_request() == FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'approval') == FALSE){
            $data['success'] = FALSE;
            $data['message'] = 'You are not allowed to save this Document!';
        } else {

            $document_number = $_SESSION['business_trip']['document_number'] . $_SESSION['business_trip']['format_number'];
            $errors = array();

            if ($_SESSION['business_trip']['head_dept']==NULL || $_SESSION['business_trip']['head_dept']=='') {
                $errors[] = 'Attention!! Please select one of Head Dept for Approval';
            }

            if ($_SESSION['business_trip']['notes']==NULL || $_SESSION['business_trip']['notes']=='') {
                $errors[] = 'Attention!! Please Fill Notes!!';
            }

            if (!empty($errors)){
                $data['success'] = FALSE;
                $data['message'] = implode('<br />', $errors);
            } else {
                if ($this->model->save()){
                    unset($_SESSION['business_trip']);
        
                    $data['success'] = TRUE;
                    $data['message'] = 'Document '. $document_number .' has been saved. You will redirected now.';
                } else {
                    $data['success'] = FALSE;
                    $data['message'] = 'Error while saving this document. Please ask Technical Support.';
                }
            }
        }

        echo json_encode($data);
    }

    public function add_item()
    {
        $this->authorized($this->module, 'document');

        if (isset($_POST) && !empty($_POST)){
        $_SESSION['receipt']['items'][] = array(
            //'id'                      => $id_item++,
            'group'                   => $this->input->post('group'),
            'description'             => trim(strtoupper($this->input->post('description'))),
            'part_number'             => trim(strtoupper($this->input->post('part_number'))),
            'alternate_part_number'   => trim(strtoupper($this->input->post('alternate_part_number'))),
            'serial_number'           => trim(strtoupper($this->input->post('serial_number'))),
            'received_quantity'       => $this->input->post('received_quantity'),
            'received_unit_value'     => $this->input->post('received_unit_value'),
            'received_unit_value_dollar'     => $this->input->post('received_unit_value_dollar'),
            'minimum_quantity'        => $this->input->post('minimum_quantity'),
            'condition'               => $this->input->post('condition'),
            'expired_date'            => $this->input->post('expired_date'),
            'stores'                  => trim(strtoupper($this->input->post('stores'))),
            'purchase_order_number'   => trim(strtoupper($this->input->post('purchase_order_number'))),
            'purchase_order_item_id'  => trim($this->input->post('purchase_order_item_id')),
            'reference_number'        => trim(strtoupper($this->input->post('reference_number'))),
            'awb_number'              => trim(strtoupper($this->input->post('awb_number'))),
            'unit'                    => trim($this->input->post('unit')),
            'received_unit'           => trim($this->input->post('received_unit')),
            'remarks'                 => trim($this->input->post('remarks')),
            'kode_stok'               => trim($this->input->post('kode_stok')),
            'currency'                => trim($this->input->post('kurs')),
            'unit_pakai'              => trim($this->input->post('unit_pakai')),
            'isi'                     => trim($this->input->post('isi')),
            'quantity_order'          => $this->input->post('quantity_order'),
            'value_order'             => $this->input->post('value_order'),
            'no_expired_date'         => $this->input->post('no_expired_date'),
            'tgl_nota'                => $this->input->post('tgl_nota'),
            'internal_delivery_item_id'  => trim($this->input->post('internal_delivery_item_id')),
            'aircraft_register_number'  => trim($this->input->post('aircraft_register_number')),

        );

        if (empty($_SESSION['receipt']['received_from'])){
            $_SESSION['receipt']['received_from'] = strtoupper($this->input->post('consignor'));
        }
        }

        redirect($this->module['route'] .'/create');
    }

    public function discard()
    {
        $this->authorized($this->module['permission']['document']);

        unset($_SESSION['receipt']);

        redirect($this->module['route']);
    }

    public function delete_ajax()
    {
        if ($this->input->is_ajax_request() === FALSE)
        redirect($this->modules['secure']['route'] .'/denied');

        if (is_granted($this->module, 'delete') === FALSE){
        $alert['type']  = 'danger';
        $alert['info']  = 'You are not allowed to delete this data!';
        } else {
        $entity = $this->model->findById($this->input->post('id'));

        if ($this->model->isValidDocumentQuantity($entity['document_number']) === FALSE){
            $alert['type']  = 'danger';
            $alert['info']  = 'Stock quantity for document ' . $entity['document_number'] . ' has been change. You are not allowed to delete this document. You can adjust stock to sync the quantity.';
        } else {
            if ($this->model->delete()){
            $alert['type'] = 'success';
            $alert['info'] = 'Data deleted.';
            $alert['link'] = site_url($this->module['route']);
            } else {
            $alert['type'] = 'danger';
            $alert['info'] = 'There are error while deleting data. Please try again later.';
            }
        }
        }

        echo json_encode($alert);
    }

    public function ajax_editItem($key)
    {
        $this->authorized($this->module, 'document');    

        $entity = $_SESSION['receipt']['items'][$key];

        echo json_encode($entity);
    }

    public function edit_item()
    {
        $this->authorized($this->module, 'document');

        $key=$this->input->post('item_id');
        if (isset($_POST) && !empty($_POST)){
        //$receipts_items_id = $this->input->post('item_id')
        $_SESSION['receipt']['items'][$key] = array(        
            'group'                   => $this->input->post('group'),
            'description'             => trim(strtoupper($this->input->post('description'))),
            'part_number'             => trim(strtoupper($this->input->post('part_number'))),
            'alternate_part_number'   => trim(strtoupper($this->input->post('alternate_part_number'))),
            'serial_number'           => trim(strtoupper($this->input->post('serial_number'))),
            'received_quantity'       => $this->input->post('received_quantity'),
            'received_unit_value'     => $this->input->post('received_unit_value'),
            'received_unit_value_dollar'     => $this->input->post('received_unit_value_dollar'),
            'minimum_quantity'        => $this->input->post('minimum_quantity'),
            'condition'               => $this->input->post('condition'),
            'expired_date'            => $this->input->post('expired_date'),
            'stores'                  => trim(strtoupper($this->input->post('stores'))),
            'purchase_order_number'   => trim(strtoupper($this->input->post('purchase_order_number'))),
            'purchase_order_item_id'  => trim($this->input->post('purchase_order_item_id')),
            'reference_number'        => trim(strtoupper($this->input->post('reference_number'))),
            'awb_number'              => trim(strtoupper($this->input->post('awb_number'))),
            'unit'                    => trim($this->input->post('unit')),
            'received_unit'           => trim($this->input->post('received_unit')),
            'remarks'                 => trim($this->input->post('remarks')),
            'kode_stok'               => trim($this->input->post('kode_stok')),
            'currency'                => trim($this->input->post('kurs')),        
            'unit_pakai'              => trim($this->input->post('unit_pakai')), 
            'isi'                     => trim($this->input->post('isi')),
            'quantity_order'          => $this->input->post('quantity_order'),
            'value_order'             => $this->input->post('value_order'),
            'no_expired_date'         => $this->input->post('no_expired_date'),
            'stock_in_stores_id'      => trim($this->input->post('stock_in_store_id')),
            'receipt_items_id'        => trim($this->input->post('receipt_items_id')),
            'tgl_nota'                => $this->input->post('tgl_nota'),        
            'internal_delivery_item_id'  => trim($this->input->post('internal_delivery_item_id')),
            'aircraft_register_number'  => trim($this->input->post('aircraft_register_number')),

        );
        }
        redirect($this->module['route'] .'/create');

    }

    public function multi_approve()
    {
        $document_id = $this->input->post('document_id');
        $document_id = str_replace("|", "", $document_id);
        $document_id = substr($document_id, 0, -1);
        $document_id = explode(",", $document_id);

        $str_notes = $this->input->post('notes');
        $notes = str_replace("|", "", $str_notes);
        $notes = substr($price, 0, -3);
        $notes = explode("##,", $notes);

        $total = 0;
        $success = 0;
        $failed = sizeof($document_id);
        $x = 0;

        $save_approval = $this->model->approve($document_id, $notes);
        if ($save_approval['status']) {
            $this->session->set_flashdata('alert', array(
                'type' => 'success',
                'info' => $save_approval['success'] . " data has been update!"
            ));
        }else{
            $this->session->set_flashdata('alert', array(
                'type' => 'danger',
                'info' => "There are " . $save_approval['failed'] . " errors"
            ));
        }

        if ($save_approval['success'] > 0) {
            // $this->model->send_mail_approval($id_expense_request, 'approve', config_item('auth_person_name'),$notes);
            $this->session->set_flashdata('alert', array(
                'type' => 'success',
                'info' => $success . " data has been update!"
            ));
        }
        if ($save_approval['failed'] > 0) {
            $this->session->set_flashdata('alert', array(
                'type' => 'danger',
                'info' => "There are " . $failed . " errors"
            ));
        }
        
        if ($save_approval['total'] == 0) {
            $result['status'] = 'failed';
        } else {
            $result['status'] = 'success';
        }
        echo json_encode($result);
    }

    public function multi_reject()
    {
        $document_id = $this->input->post('document_id');
        $document_id = str_replace("|", "", $document_id);
        $document_id = substr($document_id, 0, -1);
        $document_id = explode(",", $document_id);

        $str_notes = $this->input->post('notes');
        $notes = str_replace("|", "", $str_notes);
        $notes = substr($price, 0, -3);
        $notes = explode("##,", $notes);

        $total = 0;
        $success = 0;
        $failed = sizeof($document_id);
        $x = 0;

        $save_approval = $this->model->reject($document_id, $notes);
        if ($save_approval['status']) {
            $this->session->set_flashdata('alert', array(
                'type' => 'success',
                'info' => $save_approval['success'] . " data has been update!"
            ));
        }else{
            $this->session->set_flashdata('alert', array(
                'type' => 'danger',
                'info' => "There are " . $save_approval['failed'] . " errors"
            ));
        }

        if ($success > 0) {
            // $this->model->send_mail_approval($id_expense_request, 'approve', config_item('auth_person_name'),$notes);
            $this->session->set_flashdata('alert', array(
                'type' => 'success',
                'info' => $success . " data has been update!"
            ));
        }
        if ($failed > 0) {
            $this->session->set_flashdata('alert', array(
                'type' => 'danger',
                'info' => "There are " . $failed . " errors"
            ));
        }
        
        if ($save_approval['total'] == 0) {
            $result['status'] = 'failed';
        } else {
            $result['status'] = 'success';
        }
        echo json_encode($result);
    }

    public function import()
    {
        $this->authorized($this->module, 'import');


        redirect($this->module['route']);
    }  

    public function test()
    {
        $data = $this->model->send_mail_approval(10,'Ratining','edit_approve');
        // $data = get_count_revisi('000005/SPD/BWD-BIFA/01/2023');
        // $data = get_travel_on_duty_last_number();
        // $data = $range_date  = explode('.', '000005/SPD/BWD-BIFA/01/2023');
        // $data  = substr('000005/SPD/BWD-BIFA/01/2023', 7, 21);
        
        // $result['status'] = $send;
        echo json_encode($data);
    }
}
