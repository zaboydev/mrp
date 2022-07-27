<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Internal_Delivery_Receipt extends MY_Controller
{
    protected $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['internal_delivery_receipt'];
        $this->load->model($this->module['model'], 'model');
        $this->load->helper($this->module['helper']);
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
            $entities = $this->model->getIndexReceipt();
            $data     = array();
            $no       = $_POST['start'];
            $quantity = array();
            $unit_value   = array();
            $total_value  = array();

            foreach ($entities as $row){
                $no++;
                $col    = array();
                $col[]  = print_number($no);
                $col[]  = print_string($row['document_number']);
                $col[]  = print_date($row['send_date']);
                $col[]  = print_string($row['status']);
                $col[]  = print_string($row['category']);
                $col[]  = print_string($row['warehouse']);
                $col[]  = print_string($row['description']);
                $col[]  = print_string($row['part_number']);
                $col[]  = print_string($row['alternate_part_number']);
                $col[]  = print_string($row['serial_number']);
                $col[]  = print_string($row['condition']);
                $col[]  = print_string($row['quantity']);
                $col[]  = print_string($row['unit']);
                $col[]  = print_string($row['remarks']);
                $col[]  = print_string($row['received_from']);
                $col[]  = print_string($row['received_by']);
                $col[]  = print_string($row['sent_by']);

                if (config_item('auth_role') != 'PIC STOCK'){
                    $col[]  = print_number($row['unit_price'], 2);
                    $col[]  = print_number($row['total_amount'], 2);

                    $unit_value[]   = $row['unit_price'];
                    $total_value[]  = $row['total_amount'];
                }

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
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->model->countIndexReceipt(),
                "recordsFiltered" => $this->model->countIndexFilteredReceipt(),
                "data" => $data,
            );

            if (config_item('auth_role') != 'PIC STOCK'){
                $result['total'][16] = print_number(array_sum($unit_value), 2);
                $result['total'][17] = print_number(array_sum($total_value), 2);
            }
        }

        echo json_encode($result);
    }

    public function index()
    {
        $this->authorized($this->module, 'index');

        $this->data['page']['title']            = $this->module['label'];
        $this->data['grid']['column']           = array_values($this->model->getSelectedColumnsReceipt());
        $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source');
        $this->data['grid']['fixed_columns']    = 2;
        $this->data['grid']['summary_columns']  = array();
        $this->data['grid']['order_columns']    = array(
            0   => array( 0 => 1,  1 => 'desc' ),
            1   => array( 0 => 2,  1 => 'desc' ),
            2   => array( 0 => 3,  1 => 'asc' ),
            3   => array( 0 => 4,  1 => 'asc' ),
            4   => array( 0 => 5,  1 => 'asc' ),
            5   => array( 0 => 6,  1 => 'asc' ),
            6   => array( 0 => 7,  1 => 'asc' ),
            7   => array( 0 => 8,  1 => 'asc' ),
        );

        if (config_item('auth_role') != 'PIC STOCK'){
            $this->data['grid']['summary_columns'][] = 16;
            $this->data['grid']['summary_columns'][] = 17;
        }

        $this->render_view($this->module['view'] .'/indexReceipt');
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
            $return['info'] = $this->load->view($this->module['view'] .'/infoReceipt', $this->data, TRUE);
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

        $pdf = $this->m_pdf->load(null, 'A4-L');
        $pdf->WriteHTML($html);
        $pdf->Output($pdfFilePath, "I");
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

        if (isset($_SESSION['delivery']) === FALSE){
            $_SESSION['delivery']                     = $entity;
            $_SESSION['delivery']['id']               = $id;
            $_SESSION['delivery']['edit']             = $entity['document_number'];
            $_SESSION['delivery']['document_number']  = $document_number;
        }

        redirect($this->module['route'] .'/create');
    }

    public function save()
    {
        if ($this->input->is_ajax_request() == FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'document') == FALSE){
            $data['success'] = FALSE;
            $data['message'] = 'You are not allowed to save this Document!';
        } else {
            if (!isset($_SESSION['delivery']['items']) || empty($_SESSION['delivery']['items'])){
                $data['success'] = FALSE;
                $data['message'] = 'Please add at least 1 item!';
            } else {
                $document_number = $_SESSION['delivery']['document_number'] . delivery_format_number();

                $errors = array();

                if (isset($_SESSION['delivery']['edit'])){
                    if ($_SESSION['delivery']['edit'] != $document_number && $this->model->isDocumentNumberExists($document_number)){
                        $errors[] = 'Duplicate Document Number: '. $_SESSION['delivery']['document_number'] .' !';
                    }
                } else {
                    if ($this->model->isDocumentNumberExists($document_number)){
                        $errors[] = 'Duplicate Document Number: '. $_SESSION['delivery']['document_number'] .' !';
                    }
                }

                foreach ($_SESSION['delivery']['items'] as $key => $item) {
                    if (isStoresExists($item['stores']) && isStoresExists($item['stores'], $_SESSION['delivery']['category']) === FALSE){
                        $errors[] = 'Stores '. $item['stores'] .' exists for other inventory! Please change the stores.';
                    }

                    if (isItemExists($item['part_number'],$item['description']) && !empty($item['serial_number'])){
                        $item_id = getItemId($item['part_number'],$item['description']);

                        if (isSerialExists($item_id, $item['serial_number'])){
                            $serial = getSerial($item_id, $item['serial_number']);

                            if (isset($_SESSION['delivery']['document_edit']) && !empty($_SESSION['delivery']['document_edit'])){
                                if ($serial->reference_document != $_SESSION['delivery']['document_edit']){
                                    if ($serial->quantity > 0){
                                        $errors[] = 'Serial number '. $item['serial_number'] .' contains quantity in stores '. $serial->stores .'/'. $serial->warehouse .'. Please recheck your document.';
                                    }
                                }
                            }
                        }
                    }
                }

                if (!empty($errors)){
                    $data['success'] = FALSE;
                    $data['message'] = implode('<br />', $errors);
                } else {
                    if ($this->model->save()){
                        unset($_SESSION['delivery']);

                        $data['success'] = TRUE;
                        $data['message'] = 'Document '. $document_number .' has been saved. You will redirected now.';
                    } else {
                        $data['success'] = FALSE;
                        $data['message'] = 'Error while saving this document. Please ask Technical Support.';
                    }
                }
            }
        }

        echo json_encode($data);
    }

    public function discard()
    {
        $this->authorized($this->module['permission']['create']);

        unset($_SESSION['delivery']);

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

    public function receipt_ajax()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        if (is_granted($this->module, 'delete') === FALSE){
            $alert['type']  = 'danger';
            $alert['info']  = 'You are not allowed to received this data!';
        } else {
            $entity = $this->model->findById($this->input->post('id'));

            if ($this->model->receipt()){
                $alert['type'] = 'success';
                $alert['info'] = 'Data Received.';
                $alert['link'] = site_url($this->module['route']);
            } else {
                $alert['type'] = 'danger';
                $alert['info'] = 'There are error while receiving data. Please try again later.';
            }
        }

        echo json_encode($alert);
    }
}
