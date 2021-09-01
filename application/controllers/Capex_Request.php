<?php defined('BASEPATH') or exit('No direct script access allowed');

class Capex_Request extends MY_Controller
{
    protected $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['capex_request'];
        $this->load->helper($this->module['helper']);
        $this->load->model($this->module['model'], 'model');
        $this->data['module'] = $this->module;
        $this->load->library('email');
        $this->load->library('upload');
        $this->load->helper('string');
        
        if (empty($_SESSION['capex']['attachment']))
          $_SESSION['capex']['attachment'] = array();
    }

    public function set_doc_number()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        if (empty($_GET['data']))
            $number = request_last_number();
        else
            $number = $_GET['data'];

        $_SESSION['capex']['pr_number'] = $number;
    }

    public function get_available_vendors()
    {
        if ($this->input->is_ajax_request() === FALSE)
          redirect($this->modules['secure']['route'] . '/denied');

        // $category = $_SESSION['request']['category'];
        $entities = $this->model->getAvailableVendors();

        echo json_encode($entities);
    }

    public function set_required_date()
    {
        if ($this->input->is_ajax_request() === FALSE)
          redirect($this->modules['secure']['route'] . '/denied');

        $_SESSION['capex']['required_date'] = $_GET['data'];
    }

    public function set_suggested_supplier()
    {
        if ($this->input->is_ajax_request() === FALSE)
          redirect($this->modules['secure']['route'] . '/denied');

        $_SESSION['capex']['suggested_supplier'] = $_GET['data'];
    }

    public function set_deliver_to()
    {
        if ($this->input->is_ajax_request() === FALSE)
          redirect($this->modules['secure']['route'] . '/denied');

        $_SESSION['capex']['deliver_to'] = $_GET['data'];
    }

    public function set_notes()
    {
        if ($this->input->is_ajax_request() === FALSE)
          redirect($this->modules['secure']['route'] . '/denied');

        $_SESSION['capex']['notes'] = $_GET['data'];
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
                if ($row['status'] == 'WAITING FOR HEAD DEPT' && config_item('as_head_department')=='yes' && config_item('head_department')==$row['department_name']) {
                    $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                }else if($row['status']=='pending' && config_item('auth_role')=='BUDGETCONTROL'){
                    $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                }else{                    
                    $col[] = print_number($no);
                }
                $col[] = print_string($row['pr_number']);
                $col[] = print_string(strtoupper($row['status']));
                // $col[] = print_string($row['department_name']);
                $col[] = print_string($row['cost_center_name']);
                $col[] = print_date($row['pr_date']);
                $col[] = print_date($row['required_date']);
                $col[] = print_number($row['total_capex'],2);
                $col[] = $row['notes'];
                if ($row['status'] == 'WAITING FOR HEAD DEPT' && config_item('as_head_department')=='yes' && config_item('head_department')==$row['department_name']) {
                    $col[] = '<input type="text" id="note_' . $row['id'] . '" autocomplete="off"/>';
                }else if($row['status']=='pending' && config_item('auth_role')=='BUDGETCONTROL'){
                    $col[] = '<input type="text" id="note_' . $row['id'] . '" autocomplete="off"/>';
                }else{                    
                    $col[] = $row['approved_notes'];
                }
                $col[] = isAttachementExists($row['id'],'capex') ==0 ? '' : '<a href="#" data-id="' . $row["id"] . '" class="btn btn-icon-toggle btn-info btn-sm ">
                       <i class="fa fa-eye"></i>
                     </a>';
                if (config_item('as_head_department')=='yes'){
                    $col[] = print_string(strtoupper($row['department_name']));
                }

                $col['DT_RowId'] = 'row_'. $row['id'];
                $col['DT_RowData']['pkey']  = $row['id'];

                $total[]         = $row['total_capex'];

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
                    6  => print_number(array_sum($total), 2),
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
            0   => array( 0 => 1,  1 => 'desc' ),
            1   => array( 0 => 2,  1 => 'desc' ),
            2   => array( 0 => 3,  1 => 'asc' ),
            3   => array( 0 => 4,  1 => 'asc' ),
            4   => array( 0 => 5,  1 => 'asc' ),
            5   => array( 0 => 6,  1 => 'asc' ),
            // 6   => array( 0 => 8,  1 => 'asc' ),
            // 7   => array( 0 => 8,  1 => 'asc' ),
            
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

        $entity = $this->model->findById($id);
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
        $entity   = $this->model->findById($id);

        // if (!isset($_SESSION['request'])){
        $_SESSION['capex']              = $entity;
        $_SESSION['capex']['id']        = $id;
        $_SESSION['capex']['edit']      = $entity['pr_number'];
        $_SESSION['capex']['annual_cost_center_id']   = $entity['annual_cost_center_id'];        
        $_SESSION['capex']['pr_number']        = $entity['order_number'];
        // $_SESSION['capex']['items']            = array();
        
        // }

        redirect($this->module['route'] . '/create');
        // $this->render_view($this->module['view'] .'/create');
    }

    public function create($annual_cost_center_id = NULL)
    {
        $this->authorized($this->module, 'document');

        if ($annual_cost_center_id !== NULL){
          $annual_cost_center_id = urldecode($annual_cost_center_id);
          $cost_center = findCostCenter($annual_cost_center_id);
          $cost_center_code = $cost_center['cost_center_code'];
          $cost_center_name = $cost_center['cost_center_name'];

          $_SESSION['capex']['items']            = array();
          $_SESSION['capex']['annual_cost_center_id']   = $annual_cost_center_id;
          $_SESSION['capex']['cost_center_id']   = $cost_center_id;
          $_SESSION['capex']['cost_center_name'] = $cost_center_name;
          $_SESSION['capex']['cost_center_code'] = $cost_center_code;
          $_SESSION['capex']['order_number']        = request_last_number();
          $_SESSION['capex']['format_order_number']        = request_format_number($_SESSION['capex']['cost_center_code']);
          $_SESSION['capex']['required_date']    = date('Y-m-d');
          $_SESSION['capex']['created_by']       = config_item('auth_person_name');
          $_SESSION['capex']['warehouse']        = config_item('auth_warehouse');
          $_SESSION['capex']['notes']            = NULL;
          $_SESSION['capex']['suggested_supplier'] = NULL;
          $_SESSION['capex']['deliver_to']          = NULL;

          redirect($this->module['route'] .'/create');
        }

        if (!isset($_SESSION['capex']))
          redirect($this->module['route']);

        $this->data['page']['content']    = $this->module['view'] .'/create';
        $this->data['page']['offcanvas']  = $this->module['view'] .'/create_offcanvas_add_item';

        $this->render_view($this->module['view'] .'/create');
    }

    public function save()
    {
        // if ($this->input->is_ajax_request() == FALSE)
        //   redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'document') == FALSE) {
            $data['success'] = FALSE;
            $data['message'] = 'You are not allowed to save this Document!';
        } else {
            if (!isset($_SESSION['capex']['items']) || empty($_SESSION['capex']['items'])) {
                $data['success'] = FALSE;
                $data['message'] = 'Please add at least 1 item!';
            } else {
                $pr_number = $_SESSION['capex']['order_number'].$_SESSION['capex']['format_order_number'];

                $errors = array();

                if (isset($_SESSION['capex']['edit'])) {
                    if ($_SESSION['capex']['edit'] != $pr_number && $this->model->isDocumentNumberExists($pr_number)) {
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
                        unset($_SESSION['capex']);

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

        unset($_SESSION['capex']);

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
        $id_capex_request = $this->input->post('id_capex_request');
        $id_capex_request = str_replace("|", "", $id_capex_request);
        $id_capex_request = substr($id_capex_request, 0, -1);
        $id_capex_request = explode(",", $id_capex_request);

        $str_notes = $this->input->post('notes');
        $notes = str_replace("|", "", $str_notes);
        $notes = substr($notes, 0, -3);
        $notes = explode("##,", $notes);

        $total = 0;
        $success = 0;
        $failed = sizeof($id_capex_request);
        $x = 0;
        // $approve = $this->model->approve($id_capex_request, $notes);
        // if ($approve['status']) {
        //     $this->session->set_flashdata('alert', array(
        //         'type' => 'success',
        //         'info' => $approve['total'] . " data has been update!"
        //     ));
        // }else{
        //     $this->session->set_flashdata('alert', array(
        //         'type' => 'danger',
        //         'info' => "Error while approve this document. Please ask Technical Support."
        //     ));
        // }
        foreach ($id_capex_request as $key) {
            if ($this->model->approve($key, $notes[$x])) {
                $total++;
                $success++;
                $failed--;
                // $this->model->send_mail_approved($key,'approved');
            }
            $x++;
        }
        if ($success > 0) {
            // // jika berhasil bisa kirim email disini
            // // $id_role = 13;
            // if ((config_item('auth_role') == 'FINANCE MANAGER')) {
            //     $id_role = 9;
            //     $this->model->send_mail_next_approval($id_purchase_order, $id_role);
            // }
            // if ((config_item('auth_role') == 'CHIEF OF MAINTANCE')) {
            //     $id_role = 15;
            //     $this->model->send_mail_next_approval($id_purchase_order, $id_role);
            // }
            $this->model->send_mail_approval($id_capex_request, 'approve', config_item('auth_person_name'));

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

    public function search_budget()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        $annual_cost_center_id = $_SESSION['capex']['annual_cost_center_id'];
        $entities = $this->model->searchBudget($annual_cost_center_id);

        foreach ($entities as $key => $value) {
            $entities[$key]['label'] = $value['product_name'];
            $entities[$key]['label'] .= ' || PN: ';
            $entities[$key]['label'] .= $value['product_code'];
            $entities[$key]['label'] .= ' || Unit: ';
            $entities[$key]['label'] .= $value['measurement_symbol'];
            $entities[$key]['label'] .= '<small>';
            $entities[$key]['label'] .= 'Month to Date Budget: <code>' . number_format($value['mtd_budget'], 2) . '</code> ||';
            $entities[$key]['label'] .= 'Month to Date Qty: <code>' . number_format($value['mtd_quantity'], 2) . '</code>';
            $entities[$key]['label'] .= 'Year to Date Budget: <code>' . number_format($value['maximum_price'], 2) . '</code> ||';
            $entities[$key]['label'] .= 'Year to Date Qty: <code>' . number_format($value['maximum_quantity'], 2) . '</code>';
            $entities[$key]['label'] .= '</small>';
        }

        echo json_encode($entities);
    }

    public function add_item()
    {
        $this->authorized($this->module, 'document');

        if (isset($_POST) && !empty($_POST)) {

          $_SESSION['capex']['items'][] = array(
            'annual_cost_center_id' => $this->input->post('annual_cost_center_id'),
            'product_name'                => $this->input->post('product_name'),
            'part_number'                 => $this->input->post('part_number'),
            'unit'                        => $this->input->post('unit'),
            'maximum_quantity'            => $this->input->post('maximum_quantity'),
            'maximum_price'               => $this->input->post('maximum_price'),
            'quantity'                    => $this->input->post('quantity'),
            'price'                       => $this->input->post('price'),
            'total'                       => $this->input->post('total'),
            'additional_info'             => $this->input->post('additional_info'),
            'unbudgeted_item'             => $this->input->post('unbudgeted_item'),
            'relocation_item'             => $this->input->post('relocation_item'),
            'need_budget'                 => $this->input->post('need_budget'),
            'part_number_relocation'      => $this->input->post('origin_budget'),
            'budget_value_relocation'     => $this->input->post('budget_value'),
            'group'                       => $this->input->post('group_name'),
            'mtd_quantity'                => $this->input->post('mtd_quantity'),
            'mtd_budget'                  => $this->input->post('mtd_budget'),
            'reference_ipc'               => trim($this->input->post('reference_ipc')),
          );
        }

        redirect($this->module['route'] . '/create');
    }

    public function ajax_editItem($key)
    {
        $this->authorized($this->module, 'document');    

        $entity = $_SESSION['capex']['items'][$key];

        echo json_encode($entity);
    }

    public function edit_item($key)
    {
        $this->authorized($this->module, 'document');

        // $key=$this->input->post('item_id');
        if (isset($_POST) && !empty($_POST)){
          //$receipts_items_id = $this->input->post('item_id')
            $_SESSION['capex']['items'][$key] = array(        
                'annual_cost_center_id' => $this->input->post('annual_cost_center_id'),
                'product_name'                => $this->input->post('product_name'),
                'part_number'                 => $this->input->post('part_number'),
                'unit'                        => $this->input->post('unit'),
                'maximum_quantity'            => $this->input->post('maximum_quantity'),
                'maximum_price'               => $this->input->post('maximum_price'),
                'quantity'                    => $this->input->post('quantity'),
                'price'                       => $this->input->post('price'),
                'total'                       => $this->input->post('total'),
                'additional_info'             => $this->input->post('additional_info'),
                'unbudgeted_item'             => $this->input->post('unbudgeted_item'),
                'relocation_item'             => $this->input->post('relocation_item'),
                'need_budget'                 => $this->input->post('need_budget'),
                'part_number_relocation'      => $this->input->post('origin_budget'),
                'budget_value_relocation'     => $this->input->post('budget_value'),
                'group'                       => $this->input->post('group_name'),
                'mtd_quantity'                => $this->input->post('mtd_quantity'),
                'mtd_budget'                  => $this->input->post('mtd_budget'),
                'reference_ipc'               => trim($this->input->post('reference_ipc')),

            );
        }
        redirect($this->module['route'] .'/create');

    }

    public function del_item($key)
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        if (isset($_SESSION['capex']['items']))
            unset($_SESSION['capex']['items'][$key]);
    }

    public function attachment()
    {
        $this->authorized($this->module, 'document');

        $this->render_view($this->module['view'] . '/attachment');
    }

    public function add_attachment()
    {
        $result["status"] = 0;
        $date = new DateTime();
        // $config['file_name'] = $date->getTimestamp().random_string('alnum', 5);
        $config['upload_path'] = 'attachment/capex_request/'.$_SESSION['capex']['cost_center_name'].'/';
        $config['allowed_types'] = 'jpg|png|jpeg|doc|docx|xls|xlsx|pdf';
        $config['max_size']  = 2000;

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('attachment')) {
          $error = array('error' => $this->upload->display_errors());
        } else {

          $data = array('upload_data' => $this->upload->data());
          $url = $config['upload_path'] . $data['upload_data']['orig_name'];
          array_push($_SESSION["capex"]["attachment"], $url);
          $result["status"] = 1;
        }
        echo json_encode($result);
    }

    public function delete_attachment($index)
    {
        $file = FCPATH . $_SESSION["capex"]["attachment"][$index];
        if (unlink($file)) {
            unset($_SESSION["capex"]["attachment"][$index]);
            $_SESSION["capex"]["attachment"] = array_values($_SESSION["capex"]["attachment"]);
            redirect($this->module['route'] . "/attachment", 'refresh');
        }
    }

    public function print_pdf_prl($poe_item_id)
    {
        $this->authorized($this->module, 'print');

        $entity = $this->model->findPrlByPoeItemid($poe_item_id);

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

    public function listAttachment($id)
    {
        $data = $this->model->listAttachment($id);
        echo json_encode($data);
    }

    public function manage_attachment($id)
    {
        $this->authorized($this->module, 'document');

        $this->data['manage_attachment'] = $this->model->listAttachment_2($id);
        $this->data['id'] = $id;
        $this->render_view($this->module['view'] . '/manage_attachment');
    }

    public function add_attachment_to_db($id)
    {
        $result["status"] = 0;
        $date = new DateTime();
        // $config['file_name'] = $date->getTimestamp().random_string('alnum', 5);
        $cost_center = getCostCenterByIdRequest($id,'capex');
        $config['upload_path'] = 'attachment/capex_request/'.$cost_center['cost_center_name'].'/';
        $config['allowed_types'] = 'jpg|png|jpeg|doc|docx|xls|xlsx|pdf';
        $config['max_size']  = 2000;

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('attachment')) {
        $error = array('error' => $this->upload->display_errors());
        } else {
        $data = array('upload_data' => $this->upload->data());
        $url = $config['upload_path'] . $data['upload_data']['orig_name'];
        // array_push($_SESSION["poe"]["attachment"], $url);
        $this->model->add_attachment_to_db($id, $url);
        $result["status"] = 1;
        }
        echo json_encode($result);
    }
}
