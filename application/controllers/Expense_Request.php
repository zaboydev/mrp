<?php defined('BASEPATH') or exit('No direct script access allowed');

class Expense_Request extends MY_Controller
{
    protected $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['expense_request'];
        $this->load->helper($this->module['helper']);
        $this->load->model($this->module['model'], 'model');
        $this->data['module'] = $this->module;
        $this->load->library('email');
        // if (empty($_SESSION['request']['request_to']))
        //   $_SESSION['request']['request_to'] = 1;
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
                $col[] = print_string($row['pr_number']);
                $col[] = print_string($row['cost_center_name']);
                $col[] = print_date($row['pr_date']);
                $col[] = print_date($row['required_date']);
                $col[] = print_string($row['account_name']);
                $col[] = print_number($row['total'],2);
                $col[] = $row['notes'];
                $col['DT_RowId'] = 'row_'. $row['id'];
                $col['DT_RowData']['pkey']  = $row['id'];

                $total[]         = $row['total'];

                if ($this->has_role($this->module, 'info')){
                    $col['DT_RowAttr']['onClick']     = '$(this).popup();';
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
                    5  => print_number(array_sum($total), 2),
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
        $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
        $this->data['grid']['data_source']      = site_url($this->module['route'] . '/index_data_source');
        $this->data['grid']['fixed_columns']    = 2;
        $this->data['grid']['summary_columns']  = array(6);
        $this->data['grid']['order_columns']    = array(
            // 0   => array( 0 => 2,  1 => 'desc' ),
            1   => array( 0 => 3,  1 => 'desc' ),
            2   => array( 0 => 1,  1 => 'desc' ),
            3   => array( 0 => 4,  1 => 'asc' ),
            4   => array( 0 => 5,  1 => 'asc' ),
            5   => array( 0 => 6,  1 => 'asc' ),
            6   => array( 0 => 7,  1 => 'asc' ),
            7   => array( 0 => 8,  1 => 'asc' ),
            
        );

        $this->render_view($this->module['view'] . '/index');
    }

    public function info($id)
    {
        if ($this->input->is_ajax_request() === FALSE)
        redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'info') === FALSE) {
        $return['type'] = 'denied';
        $return['info'] = "You don't have permission to access this data. You may need to login again.";
        } else {
        $entity = $this->model->findById($id);

        $this->data['entity'] = $entity;

        $return['type'] = 'success';
        $return['info'] = $this->load->view($this->module['view'] . '/info', $this->data, TRUE);
        }

        echo json_encode($return);
    }

    public function print_pdf($id)
    {
        $this->authorized($this->module, 'print');

        $entity = $this->model->findPrlById($id);
        // $on_hand_stock = $this->model->findPrlById($id);

        $this->data['entity']           = $entity;
        $this->data['page']['title']    = strtoupper($this->module['label']);
        $this->data['page']['content']  = $this->module['view'] . '/print_pdf';

        $html = $this->load->view($this->pdf_theme, $this->data, true);

        $pdfFilePath = str_replace('/', '-', $entity['pr_number']) . ".pdf";

        $this->load->library('m_pdf');

        $pdf = $this->m_pdf->load(null, 'A4-L');
        $pdf->WriteHTML($html);
        $pdf->Output($pdfFilePath, "I");
    }

    public function edit($id)
    {
        $this->authorized($this->module, 'document');

        // $entity   = $this->model->findById($id);
        $entity   = $this->model->findPrlById($id);

        // if (!isset($_SESSION['request'])){
        $_SESSION['request']              = $entity;
        $_SESSION['request']['id']        = $id;
        $_SESSION['request']['edit']      = $entity['pr_number'];
        $_SESSION['request']['category']  = $entity['category_name'];
        // }

        redirect($this->module['route'] . '/create');
        // $this->render_view($this->module['view'] .'/create');
    }

    public function create($category = NULL)
    {
        $this->authorized($this->module, 'document');

        if ($category !== NULL) {
        $category = urldecode($category);
        if ($category == 'BAHAN BAKAR') {
            $target_date = 7;
            $start_date  = date('Y-m-d');
            $date        = strtotime('+7 day', strtotime($start_date));
            $required_date    = date('Y-m-d', $date);
        } else {
            $target_date = 30;
            $start_date  = date('Y-m-d');
            $date        = strtotime('+30 day', strtotime($start_date));
            $required_date    = date('Y-m-d', $date);
        }

        $_SESSION['request']['items']               = array();
        $_SESSION['request']['category']            = $category;
        $_SESSION['request']['order_number']        = request_last_number();
        $_SESSION['request']['pr_number']           = request_last_number() . request_format_number();
        $_SESSION['request']['pr_date']             = date('Y-m-d');
        $_SESSION['request']['required_date']       = $required_date;
        $_SESSION['request']['created_by']          = config_item('auth_person_name');
        $_SESSION['request']['suggested_supplier']  = NULL;
        $_SESSION['request']['deliver_to']          = NULL;
        $_SESSION['request']['notes']               = NULL;
        $_SESSION['request']['target_date']         = $target_date;

        redirect($this->module['route'] . '/create');
        }

        if (!isset($_SESSION['request']))
        redirect($this->module['route']);

        $this->data['page']['content']    = $this->module['view'] . '/create';
        $this->data['page']['offcanvas']  = $this->module['view'] . '/create_offcanvas_add_item';

        $this->render_view($this->module['view'] . '/create');
    }

    public function save()
    {
        // if ($this->input->is_ajax_request() == FALSE)
        //   redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'document') == FALSE) {
        $data['success'] = FALSE;
        $data['message'] = 'You are not allowed to save this Document!';
        } else {
        if (!isset($_SESSION['request']['items']) || empty($_SESSION['request']['items'])) {
            $data['success'] = FALSE;
            $data['message'] = 'Please add at least 1 item!';
        } else {
            $pr_number = $_SESSION['request']['pr_number'];

            $errors = array();

            if (isset($_SESSION['request']['edit'])) {
            if ($_SESSION['request']['edit'] != $pr_number && $this->model->isDocumentNumberExists($pr_number)) {
                $errors[] = 'Duplicate Document Number: ' . $pr_number . ' !';
            }
            } else {
            if ($this->model->isDocumentNumberExists($pr_number)) {
                $errors[] = 'Duplicate Document Number: ' . $pr_number . ' !';
            }
            }

            if (!empty($errors)) {
            $data['success'] = FALSE;
            $data['message'] = implode('<br />', $errors);
            } else {
            if ($this->model->save()) {
                unset($_SESSION['request']);

                // SEND EMAIL NOTIFICATION HERE
                // $this->send_mail();
                $data['success'] = TRUE;
                $data['message'] = 'Document ' . $pr_number . ' has been saved. You will redirected now.';
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
        $this->authorized($this->module['permission']['document']);

        unset($_SESSION['request']);

        redirect($this->module['route']);
    }

    public function delete_ajax()
    {
        if ($this->input->is_ajax_request() === FALSE)
        redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'delete') === FALSE) {
        $alert['type']  = 'danger';
        $alert['info']  = 'You are not allowed to delete this data!';
        } else {
        $entity = $this->model->findById($this->input->post('id'));

        if ($this->model->isValidDocumentQuantity($entity['pr_number']) === FALSE) {
            $alert['type']  = 'danger';
            $alert['info']  = 'Stock quantity for document ' . $entity['pr_number'] . ' has been change. You are not allowed to delete this document. You can adjust stock to sync the quantity.';
        } else {
            if ($this->model->delete()) {
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

    public function multi_approve()
    {
        $id_purchase_order = $this->input->post('id_purchase_order');
        $id_purchase_order = str_replace("|", "", $id_purchase_order);
        $id_purchase_order = substr($id_purchase_order, 0, -1);
        $id_purchase_order = explode(",", $id_purchase_order);

        $str_price = $this->input->post('price');
        $price = str_replace("|", "", $str_price);
        $price = substr($price, 0, -3);
        $price = explode("##,", $price);

        $total = 0;
        $success = 0;
        $failed = sizeof($id_purchase_order);
        $x = 0;
        foreach ($id_purchase_order as $key) {
        if ($this->model->approve($key, $price[$x])) {
            $total++;
            $success++;
            $failed--;
            // $this->model->send_mail_approved($key,'approved');
        }
        $x++;
        }
        if ($success > 0) {
        // $id_role = 13;
        if ((config_item('auth_role') == 'FINANCE MANAGER')) {
            $id_role = 9;
            $this->model->send_mail_next_approval($id_purchase_order, $id_role);
        }
        if ((config_item('auth_role') == 'CHIEF OF MAINTANCE')) {
            $id_role = 15;
            $this->model->send_mail_next_approval($id_purchase_order, $id_role);
        }
        $this->model->send_mail_approval($id_purchase_order, 'approve', config_item('auth_person_name'));
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
        if ($total == 0) {
        $result['status'] = 'failed';
        } else {
        //$this->sendEmailHOS();
        $result['status'] = 'success';
        }
        echo json_encode($result);
    }

    public function multi_reject()
    {
        $str_id_purchase_order = $this->input->post('id_purchase_order');
        $str_notes = $this->input->post('notes');
        $id_purchase_order = str_replace("|", "", $str_id_purchase_order);
        $id_purchase_order = substr($id_purchase_order, 0, -1);
        $notes = str_replace("|", "", $str_notes);
        $notes = substr($notes, 0, -3);
        $id_purchase_order = explode(",", $id_purchase_order);
        $notes = explode("##,", $notes);
        $result = $this->model->multi_reject($id_purchase_order, $notes);
        if ($result) {
        $this->model->send_mail_approval($id_purchase_order, 'rejected', config_item('auth_person_name'));
        $return["status"] = "success";
        echo json_encode($return);
        } else {
        $return["status"] = "failed";
        echo json_encode($return);
        }
    }

    public function multi_closing()
    {
        $str_id_purchase_order = $this->input->post('id_purchase_order');
        $str_notes = $this->input->post('notes');
        $id_purchase_order = str_replace("|", "", $str_id_purchase_order);
        $id_purchase_order = substr($id_purchase_order, 0, -1);
        $notes = str_replace("|", "", $str_notes);
        $notes = substr($notes, 0, -3);
        $id_purchase_order = explode(",", $id_purchase_order);
        $notes = explode("##,", $notes);
        $result = $this->model->multi_closing($id_purchase_order, $notes);
        if ($result) {
        // $this->model->send_mail_approval($id_purchase_order, 'rejected', config_item('auth_person_name'));
        $return["status"] = "success";
        echo json_encode($return);
        } else {
        $return["status"] = "failed";
        echo json_encode($return);
        }
    }
}
