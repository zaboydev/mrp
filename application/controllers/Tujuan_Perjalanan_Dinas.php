<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tujuan_Perjalanan_Dinas extends MY_Controller
{
    protected $module;
    protected $id_item=0;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['tujuan_perjalanan_dinas'];
        $this->load->model($this->module['model'], 'model');
        // $this->load->helper($this->module['helper']);
        $this->load->library('upload');
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
                $expense_amount = $this->model->countExpenseAmount($row['id']);
                $no++;
                $col = array();
                $col[] = print_number($no);
                $col[] = print_string($row['business_trip_destination']);
                $col[] = print_number($expense_amount,2);
                $col[] = print_string($row['notes']);
                $col[] = print_date($row['updated_at']);
                $col['DT_RowId'] = 'row_'. $row['id'];
                $col['DT_RowData']['pkey']  = $row['id'];
                
                if ($this->has_role($this->module, 'info')){
                    $col['DT_RowAttr']['onClick']     = '$(this).popup();';
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
        $this->data['grid']['fixed_columns']    = 1;
        $this->data['grid']['summary_columns']  = array();

        $this->data['grid']['order_columns']    = array(
            0   => array( 0 => 1,  1 => 'desc' ),
        );

        $this->render_view($this->module['view'] .'/index');
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

    public function edit($id)
    {
        $this->authorized($this->module, 'create');

        $entity = $this->model->findById($id);

        if (!isset($_SESSION['tujuan_dinas']['items'])) {
            $_SESSION['tujuan_dinas']                     = $entity;
            $_SESSION['tujuan_dinas']['id']               = $id;
            $_SESSION['tujuan_dinas']['edit']             = $entity['id'];
        }

        redirect($this->module['route'] . '/create');
    }

    public function set_notes()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        $_SESSION['tujuan_dinas']['notes'] = $_GET['data'];
    }

    public function set_business_trip_destination()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        $_SESSION['tujuan_dinas']['business_trip_destination'] = $_GET['data'];
    }

    public function create($category = NULL)
    {
        $this->authorized($this->module, 'create');

        if ($category !== NULL) {
            $category = urldecode($category);
      
            $_SESSION['tujuan_dinas']['items']                      = array();
            $_SESSION['tujuan_dinas']['levels']                     = array();
            $_SESSION['tujuan_dinas']['business_trip_destination']  = NULL;
            $_SESSION['tujuan_dinas']['notes']                      = NULL;
      
            redirect($this->module['route'] . '/create');
        }

        $this->data['page']['content']    = $this->module['view'] .'/create';
        $this->data['page']['offcanvas']  = $this->module['view'] .'/create_offcanvas_add_item';

        $this->render_view($this->module['view'] .'/create');
    }

    public function add_expense_item()
    {
        $this->authorized($this->module, 'create');
        $this->data['page']['title']            = 'Add Expense Item';

        $this->render_view($this->module['view'] . '/add_expense_item');
    }

    public function add_input_expense()
    {
        if ($this->input->is_ajax_request() == FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'create') == FALSE) {
            $data['success'] = FALSE;
            $data['message'] = 'You are not allowed to save this Document!';
        } else {
            if (isset($_POST['expense_name']) && !empty($_POST['expense_name'])) {
                $expense_names   = $this->input->post('expense_name');
                $_SESSION['tujuan_dinas']['items'] = array();
                foreach ($expense_names as $key=>$expense_name){
                    $_SESSION['tujuan_dinas']['items'][$key] = array(
                        'expense_name'             => trim(strtoupper($expense_name)),
                    );
                    $_SESSION['tujuan_dinas']['items'][$key]['levels'] = array();
                }

                $data['success'] = TRUE;
            } else {
                $data['success'] = FALSE;
                $data['message'] = 'Please select any request!';
            }
        }

        echo json_encode($data);
    }

    public function add_level()
    {
        $this->authorized($this->module, 'create');
        $this->data['page']['title']            = 'Add Level';

        $this->render_view($this->module['view'] . '/add_level');
    }

    public function add_selected_level()
    {
        if ($this->input->is_ajax_request() == FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'create') == FALSE) {
            $data['success'] = FALSE;
            $data['message'] = 'You are not allowed to save this Document!';
        } else {
        if (isset($_POST['level']) && !empty($_POST['level'])) {
            $_SESSION['tujuan_dinas']['levels'] = array();

            foreach ($_POST['level'] as $key => $level) {
                $_SESSION['tujuan_dinas']['levels'][$key]['level'] = $level;
            }

            foreach ($_SESSION['tujuan_dinas']['items'] as $id => $item) {
                $min = 0;
                $cheaper = 'f';
                foreach ($_POST['level'] as $key => $level) {
                    $_SESSION['tujuan_dinas']['items'][$id]['levels'][$key] = array(
                        'level'     => $level,
                        'amount'    => floatval(0),
                    );
                }
            }

            $data['success'] = TRUE;
        } else {
            $data['success'] = FALSE;
            $data['message'] = 'Please select any vendors!';
        }
        }

        echo json_encode($data);
    }

    public function edit_expense()
    {
        $this->authorized($this->module, 'create');

        $this->render_view($this->module['view'] . '/edit_expense');
    }

    public function update_expense()
    {
        if ($this->input->is_ajax_request() == FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'create') == FALSE) {
            $data['success'] = FALSE;
            $data['message'] = 'You are not allowed to save this Document!';
        } else {
            if (isset($_POST['request']) && !empty($_POST['request'])) {
                foreach ($_POST['request'] as $id => $request) {
                    $quantity = floatval($_SESSION['poe']['request'][$id]['quantity_requested']);

                    $_SESSION['tujuan_dinas']['items'][$id]['expense_name']           = trim(strtoupper($request['expense_name']));

                    foreach ($request['levels'] as $key => $level) {
                        
                        $amount   = $level['amount'];

                        $_SESSION['tujuan_dinas']['items'][$id]['levels'][$key]['amount']   = $amount;
                    }
                }

                $data['success'] = TRUE;
            } else {
                $data['success'] = FALSE;
                $data['message'] = 'No data to update!';
            }
        }

        echo json_encode($data);
    }

    public function save()
    {
        if ($this->input->is_ajax_request() == FALSE)
          redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'create') == FALSE){
            $data['success'] = FALSE;
            $data['message'] = 'You are not allowed to save this Document!';
        } else {
            if (!isset($_SESSION['tujuan_dinas']['items']) || empty($_SESSION['tujuan_dinas']['items']) || !isset($_SESSION['tujuan_dinas']['levels']) || empty($_SESSION['tujuan_dinas']['levels'])) {
                $data['success'] = FALSE;
                $data['message'] = 'Please add at least 1 Item Expense !!';
            }else{
                $errors = array();
                if (!isset($_SESSION['tujuan_dinas']['business_trip_destination']) || empty($_SESSION['tujuan_dinas']['business_trip_destination'])){
                    $errors[] = 'Tujuan Dinas Harus isi.';
                }

                if (!empty($errors)) {
                    $data['success'] = FALSE;
                    $data['message'] = implode('<br />', $errors);
                } else {
                    if ($this->model->insert()) {
                        unset($_SESSION['tujuan_dinas']);
                        $data['success'] = TRUE;
                        $data['message'] = 'Data has been saved. You will redirected now.';
                    } else {
                        $data['success'] = FALSE;
                        $data['message'] = 'Error while saving this Data. Please ask Technical Support.';
                    }
                }
            }
        }

        echo json_encode($data);
    }

    public function discard()
    {
        $this->authorized($this->module['permission']['create']);

        unset($_SESSION['tujuan_dinas']);

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

            if ($this->model->delete()){
                $alert['type'] = 'success';
                $alert['info'] = 'Data deleted.';
                $alert['link'] = site_url($this->module['route']);
            } else {
                $alert['type'] = 'danger';
                $alert['info'] = 'There are error while deleting data. Please try again later.';
            }
        }

        echo json_encode($alert);
    }

    public function import()
    {
        $this->authorized($this->module, 'import');

        $this->load->library('form_validation');

        if (isset($_POST) && !empty($_POST)){
        $this->form_validation->set_rules('delimiter', 'Value Delimiter', 'trim|required');

        if ($this->form_validation->run() === TRUE){
            $file       = $_FILES['userfile']['tmp_name'];
            $delimiter  = $this->input->post('delimiter');

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

                $category        = trim(strtoupper($col[0]));
                $part_number            = trim(strtoupper($col[1]));
                $serial_number               = trim(strtoupper($col[2]));
                $condition              = trim(strtoupper($col[3]));
                $unit            = trim(strtoupper($col[4]));
                $description            = trim(strtoupper($col[5]));
                $alternate_part_number          = trim(strtoupper($col[6]));
                $group              = trim(strtoupper($col[7]));
                $minimum_quantity               = trim(strtoupper($col[8]));
                $stores                   = trim(strtoupper($col[9]));
                $warehouse                = trim(strtoupper($col[10]));
                $document_number              = trim(strtoupper($col[11]));
                $expired_date              = trim(strtoupper($col[12]));
                $vendor            = trim(strtoupper($col[13]));
                $remarks  = trim(strtoupper($col[14]));
                $received_date                  = trim(strtoupper($col[15]));
                $received_by                 = trim(strtoupper($col[16]));
                $received_quantity     = trim(strtoupper($col[17]));
                $received_unit_value                  = trim(strtoupper($col[18]));
                $purchase_order_number            = trim(strtoupper($col[19]));
                $reference_number            = trim(strtoupper($col[20]));
                $awb_number            = trim(strtoupper($col[21]));


                if ($document_number == NULL)
                $errors[] = 'Line '. $row .': Document Number is empty!';
                
                

                if ($received_date == NULL)
                $errors[] = 'Line '. $row .': Received Date is empty!';

                if ($warehouse == NULL)
                $errors[] = 'Line '. $row .': Base is empty!';

                if (isWarehouseExists($warehouse) == FALSE)
                $errors[] = 'Line '. $row .': Base '.$warehouse.'not found!';

                if ($category == NULL)
                $errors[] = 'Line '. $row .': Category is empty!';

                if (isItemCategoryExists($category) == FALSE)
                $errors[] = 'Line '. $row .': Category not found!';

                if ($part_number == NULL){
                $errors[] = 'Line '. $row .': Part Number is empty!';
                } 

                if ($stores == NULL){
                $errors[] = 'Line '. $row .': Issued Stores is empty!';
                } else {
                if (isStoresExists($stores, $category) === FALSE){
                    $errors[] = 'Line '. $row .$stores.': Stores not found!';
                }
                }

                $data[$row]['category']                    = $category;
                $data[$row]['part_number']                 = $part_number;
                $data[$row]['serial_number']               = $serial_number;
                $data[$row]['condition']                   = $condition;
                $data[$row]['unit']                        = $unit;
                $data[$row]['description']                 = $description;
                $data[$row]['alternate_part_number']       = $alternate_part_number;
                $data[$row]['group']                       = $group;
                $data[$row]['minimum_quantity']            = $minimum_quantity;
                $data[$row]['stores']                      = $stores;
                $data[$row]['warehouse']                   = $warehouse;
                $data[$row]['document_number']             = $document_number;
                $data[$row]['expired_date']                = $expired_date;
                $data[$row]['vendor']                      = $vendor;
                $data[$row]['remarks']                     = $remarks;
                $data[$row]['received_date']               = $received_date;
                $data[$row]['received_by']                 = $received_by;
                $data[$row]['received_quantity']           = $received_quantity;
                $data[$row]['received_unit_value']         = $received_unit_value;
                $data[$row]['purchase_order_number']       = $purchase_order_number;
                $data[$row]['reference_number']            = $reference_number;
                $data[$row]['awb_number']                  = $awb_number;

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

        redirect($this->module['route']);
    }  
}
