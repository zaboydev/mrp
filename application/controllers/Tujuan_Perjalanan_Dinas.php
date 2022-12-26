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
        $this->authorized($this->module, 'document');

        $entity = $this->model->findById($id);

        if ($this->model->isValidDocumentQuantity($entity['document_number']) === FALSE){
        $this->session->set_flashdata('alert', array(
            'type' => 'danger',
            'info' => 'Stock quantity for document ' . $entity['document_number'] . ' has been change. You are not allowed to edit this document. You can adjust stock to sync the quantity.'
        ));

        redirect(site_url($this->module['route']));
        }

        $document_number  = sprintf('%06s', substr($entity['document_number'], 0, 6));

        if (isset($_SESSION['receipt']) === FALSE){
        $_SESSION['receipt']                     = $entity;
        $_SESSION['receipt']['id']               = $id;
        $_SESSION['receipt']['edit']             = $entity['document_number'];
        $_SESSION['receipt']['document_number']  = $document_number;
        }

        redirect($this->module['route'] .'/create');
        //$this->render_view($this->module['view'] .'/edit');
    }

    public function create($category = NULL)
    {
        $this->authorized($this->module, 'create');

        $this->data['page']['content']    = $this->module['view'] .'/create';
        $this->data['page']['offcanvas']  = $this->module['view'] .'/create_offcanvas_add_item';

        $this->render_view($this->module['view'] .'/create');
    }

    public function save()
    {
        if ($this->input->is_ajax_request() == FALSE)
          redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'create') == FALSE){
            $data['success'] = FALSE;
            $data['message'] = 'You are not allowed to save this Document!';
        } else {
            if ($this->input->post('id')){

            }else{
                if ($this->model->insert()){
                    $data['success']    = TRUE;
                    $data['message']       = 'Business Trip Destination ' . $this->input->post('position') .' created.';
                } else {
                    $data['success']    = FALSE;
                    $data['message']       = 'There are error while updating data. Please try again later.';
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
