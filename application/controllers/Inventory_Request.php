<?php defined('BASEPATH') or exit('No direct script access allowed');

class Inventory_Request extends MY_Controller
{
    protected $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['inventory_request'];
        $this->load->helper($this->module['helper']);
        $this->load->model($this->module['model'], 'model');
        $this->data['module'] = $this->module;
        $this->load->library('email');
        $this->load->library('upload');
        // if (empty($_SESSION['request']['request_to']))
        //   $_SESSION['request']['request_to'] = 1;

        if (empty($_SESSION['inventory']['attachment']))
          $_SESSION['inventory']['attachment'] = array();
    
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
            }else if($row['status']=='WAITING FOR AHOS REVIEW' && config_item('auth_role')=='ASSISTANT HOS'){
                $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
            }else if($row['status']=='WAITING FOR HEAD DEPT UNIQ REVIEW' && config_item('auth_role')=='HEAD DEPT UNIQ JKT'){
                $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
            }else if($row['status']=='approved'){
                if(is_granted($this->module, 'closing') === TRUE){
                    $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                }else{
                    $col[] = print_number($no);
                }
            }else{                    
                $col[] = print_number($no);
            }
            $col[] = print_string($row['pr_number']);
            $col[] = print_string(strtoupper($row['status']));
            $col[] = print_string($row['category_name']);
            // $col[] = print_string($row['department_name']);
            $col[] = print_string($row['cost_center_name']);
            $col[] = print_date($row['pr_date']);
            $col[] = print_date($row['required_date']);
            $col[] = print_number($row['total_inventory'],2);
            $col[] = $row['notes'];
            if($row['status']=='close'){
                $col[] = $row['approved_notes'];
            }else{
                if (is_granted($this->module, 'approval') === TRUE) {
                    $col[] = '<input type="text" id="note_' . $row['id'] . '" autocomplete="off"/>';
                }else{  
                    if (is_granted($this->module, 'closing') === TRUE) {
                            $col[] = '<input type="text" id="note_' . $row['id'] . '" autocomplete="off"/>';
                    }  else{                                         
                        $col[] = $row['approved_notes'];
                    }   
                }
            }
            $col[] = isAttachementExists($row['id'],'inventory') ==0 ? '' : '<a href="#" data-id="' . $row["id"] . '" class="btn btn-icon-toggle btn-info btn-sm ">
                       <i class="fa fa-eye"></i>
                     </a>';
            $col[] = print_string($row['department_name']);
            $col['DT_RowId'] = 'row_'. $row['id'];
            $col['DT_RowData']['pkey']  = $row['id'];
            $total[]         = $row['total_inventory'];

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
                7  => print_number(array_sum($total), 2),
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
        $this->data['grid']['summary_columns']  = array(7);
        $this->data['grid']['order_columns']    = array(
             // 0   => array( 0 => 2,  1 => 'desc' ),
            0   => array( 0 => 1,  1 => 'desc' ),
            1   => array( 0 => 2,  1 => 'desc' ),
            2   => array( 0 => 3,  1 => 'asc' ),
            3   => array( 0 => 4,  1 => 'asc' ),
            4   => array( 0 => 5,  1 => 'asc' ),
            5   => array( 0 => 6,  1 => 'asc' ),
            6   => array( 0 => 8,  1 => 'asc' ),
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

    public function select_category($annual_cost_center_id)
    {
        if ($this->input->is_ajax_request() === FALSE)
        redirect($this->modules['secure']['route'] . '/denied');

        $categories = $this->model->getProductCategories();

        $this->data['entity'] = $categories;
        $this->data['cost_center_name'] = getCostCenterNameByAnnualCostCenterId($annual_cost_center_id);
        $this->data['annual_cost_center_id'] = $annual_cost_center_id;

        $return['type'] = 'success';
        $return['info'] = $this->load->view($this->module['view'] . '/select_category', $this->data, TRUE);

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
        $_SESSION['inventory']              = $entity;
        $_SESSION['inventory']['id']        = $id;
        $_SESSION['inventory']['edit']      = $entity['order_number'];
        $_SESSION['inventory']['annual_cost_center_id']     = $entity['annual_cost_center_id'];
        $_SESSION['inventory']['category_id']               = $entity['product_category_id'];
        $_SESSION['inventory']['pr_number']        = $entity['order_number'];
        // $_SESSION['capex']['items']            = array();
        
        // }

        redirect($this->module['route'] . '/create');
        // $this->render_view($this->module['view'] .'/create');
    }

    public function create($annual_cost_center_id = NULL,$product_category_id=NULL)
    {
        $this->authorized($this->module, 'document');

        if ($annual_cost_center_id !== NULL && $product_category_id!==NULL){
            $annual_cost_center_id    = urldecode($annual_cost_center_id);
            $product_category_id      = urldecode($product_category_id);

            $cost_center              = findCostCenter($annual_cost_center_id);
            $cost_center_code         = $cost_center['cost_center_code'];
            $cost_center_name         = $cost_center['cost_center_name'];


            $product_category         = findProductCategoryById($product_category_id);
            $category_name            = $product_category['category_name'];
            $category_code            = $product_category['category_code'];

            $_SESSION['inventory']['items']                   = array();
            $_SESSION['inventory']['annual_cost_center_id']   = $annual_cost_center_id;
            $_SESSION['inventory']['cost_center_id']          = $cost_center_id;
            $_SESSION['inventory']['cost_center_name']        = $cost_center_name;
            $_SESSION['inventory']['cost_center_code']        = $cost_center_code;
            $_SESSION['inventory']['product_category_id']     = $product_category_id;
            $_SESSION['inventory']['category_name']           = $category_name;
            $_SESSION['inventory']['category_code']           = $category_code;
            $_SESSION['inventory']['order_number']            = request_last_number();
            $_SESSION['inventory']['format_order_number']     = request_format_number($category_code);
            $_SESSION['inventory']['required_date']           = date('Y-m-d');
            $_SESSION['inventory']['created_by']              = config_item('auth_person_name');
            $_SESSION['inventory']['warehouse']               = config_item('auth_warehouse');
            $_SESSION['inventory']['notes']                   = NULL;
            $_SESSION['inventory']['suggested_supplier']      = NULL;
            $_SESSION['inventory']['deliver_to']              = NULL;

            redirect($this->module['route'] .'/create');
        }

        if (!isset($_SESSION['inventory']))
          redirect($this->module['route']);

        $this->data['page']['content']    = $this->module['view'] .'/create';
        $this->data['page']['offcanvas']  = $this->module['view'] .'/create_offcanvas_add_item';

        $this->render_view($this->module['view'] .'/create');
    }

    public function add_item()
    {
        $this->authorized($this->module, 'document');

        if (isset($_POST) && !empty($_POST)) {

          $_SESSION['inventory']['items'][] = array(
            // 'annual_cost_center_id' => $this->input->post('annual_cost_center_id'),
            'product_name'                => $this->input->post('product_name'),
            'part_number'                 => $this->input->post('part_number'),
            'unit'                        => strtoupper($this->input->post('unit')),
            'maximum_quantity'            => $this->input->post('maximum_quantity'),
            'maximum_price'               => $this->input->post('maximum_price'),
            'quantity'                    => $this->input->post('quantity'),
            'price'                       => $this->input->post('price'),
            'total'                       => $this->input->post('total'),
            'additional_info'             => $this->input->post('additional_info'),
            'on_hand_quantity'              => $this->input->post('on_hand_quantity'),
            'minimum_quantity'              => $this->input->post('minimum_quantity'),
            'inventory_monthly_budget_id'   => $this->input->post('inventory_monthly_budget_id'),
            'group'                         => $this->input->post('group_name'),
            'mtd_quantity'                  => $this->input->post('mtd_quantity'),
            'mtd_budget'                    => $this->input->post('mtd_budget'),
            'product_id'                    => $this->input->post('product_id'),
            'reference_ipc'                 => trim($this->input->post('reference_ipc')),
          );
        }

        redirect($this->module['route'] . '/create');
    }

    public function ajax_editItem($key)
    {
        $this->authorized($this->module, 'document');    

        $entity = $_SESSION['inventory']['items'][$key];

        echo json_encode($entity);
    }

    public function edit_item($key)
    {
        $this->authorized($this->module, 'document');

        // $key=$this->input->post('item_id');
        if (isset($_POST) && !empty($_POST)){
          //$receipts_items_id = $this->input->post('item_id')
            $_SESSION['inventory']['items'][$key] = array(        
                // 'annual_cost_center_id' => $this->input->post('annual_cost_center_id'),
                'product_name'                => $this->input->post('product_name'),
                'part_number'                 => $this->input->post('part_number'),
                'unit'                        => strtoupper($this->input->post('unit')),
                'maximum_quantity'            => $this->input->post('maximum_quantity'),
                'maximum_price'               => $this->input->post('maximum_price'),
                'quantity'                    => $this->input->post('quantity'),
                'price'                       => $this->input->post('price'),
                'total'                       => $this->input->post('total'),
                'additional_info'             => $this->input->post('additional_info'),
                'on_hand_quantity'              => $this->input->post('on_hand_quantity'),
                'minimum_quantity'              => $this->input->post('minimum_quantity'),
                'inventory_monthly_budget_id'   => $this->input->post('inventory_monthly_budget_id'),
                'group'                         => $this->input->post('group_name'),
                'mtd_quantity'                  => $this->input->post('mtd_quantity'),
                'mtd_budget'                    => $this->input->post('mtd_budget'),
                'product_id'                    => $this->input->post('product_id'),
                'reference_ipc'                 => trim($this->input->post('reference_ipc')),

            );
        }
        redirect($this->module['route'] .'/create');

    }

    public function del_item($key)
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        if (isset($_SESSION['inventory']['items']))
            unset($_SESSION['inventory']['items'][$key]);
    }

    public function save()
    {
        // if ($this->input->is_ajax_request() == FALSE)
        //   redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'document') == FALSE) {
            $data['success'] = FALSE;
            $data['message'] = 'You are not allowed to save this Document!';
        } else {
            if (!isset($_SESSION['inventory']['items']) || empty($_SESSION['inventory']['items'])) {
                $data['success'] = FALSE;
                $data['message'] = 'Please add at least 1 item!';
            } else {
                $_SESSION['inventory']['order_number'] = request_last_number();
                $pr_number = $_SESSION['inventory']['order_number'];

                $errors = array();

                if (isset($_SESSION['inventory']['edit'])) {
                    if ($_SESSION['inventory']['edit'] != $pr_number && $this->model->isDocumentNumberExists($pr_number)) {
                        // $errors[] = 'Duplicate Document Number: ' . $pr_number . ' !';
                        $_SESSION['inventory']['order_number'] = request_last_number();
                        $pr_number = $_SESSION['inventory']['order_number'];
                    }
                } else {
                    if ($this->model->isDocumentNumberExists($pr_number)) {
                        $errors[] = 'Duplicate Document Number: ' . $pr_number . ' !';
                        $_SESSION['inventory']['order_number'] = request_last_number();
                        $pr_number = $_SESSION['inventory']['order_number'];
                    }
                }

                if (!empty($errors)) {
                    $data['success'] = FALSE;
                    $data['message'] = implode('<br />', $errors);
                } else {
                    if ($this->model->save()) {
                        unset($_SESSION['inventory']);

                        // SEND EMAIL NOTIFICATION HERE
                        // $this->send_mail();
                        $data['success'] = TRUE;
                        $data['message'] = 'Document ' . $pr_number.$_SESSION['inventory']['format_order_number'] . ' has been saved. You will redirected now.';
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

        unset($_SESSION['inventory']);

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
        $id_inventory_request = $this->input->post('id_inventory_request');
        $id_inventory_request = str_replace("|", "", $id_inventory_request);
        $id_inventory_request = substr($id_inventory_request, 0, -1);
        $id_inventory_request = explode(",", $id_inventory_request);

        $str_notes = $this->input->post('notes');
        $notes = str_replace("|", "", $str_notes);
        $notes = substr($notes, 0, -3);
        $notes = explode("##,", $notes);

        $total = 0;
        $success = 0;
        $failed = sizeof($id_inventory_request);
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
        foreach ($id_inventory_request as $key) {
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
            $this->model->send_mail_approval($id_inventory_request, 'approve', config_item('auth_person_name'),$notes);

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
            $this->model->send_mail_approval($id_purchase_order, 'rejected', config_item('auth_person_name'),$notes);
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

    public function set_doc_number()
    {
        if ($this->input->is_ajax_request() === FALSE)
          redirect($this->modules['secure']['route'] .'/denied');

        if (empty($_GET['data']))
          $number = request_last_number();
        else
          $number = $_GET['data'];

        $_SESSION['inventory']['order_number'] = $number;
    }

    public function set_required_date()
    {
        if ($this->input->is_ajax_request() === FALSE)
          redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['inventory']['required_date'] = $_GET['data'];
    }

    public function set_suggested_supplier()
    {
        if ($this->input->is_ajax_request() === FALSE)
          redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['inventory']['suggested_supplier'] = $_GET['data'];
    }

    public function get_available_vendors()
    {
        if ($this->input->is_ajax_request() === FALSE)
          redirect($this->modules['secure']['route'] .'/denied');

        $category = $_SESSION['inventory']['category_name'];
        $entities = $this->model->getAvailableVendors($category);

        echo json_encode($entities);
    }

    public function set_deliver_to()
    {
        if ($this->input->is_ajax_request() === FALSE)
          redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['inventory']['deliver_to'] = $_GET['data'];
    }

    public function set_notes()
    {
        if ($this->input->is_ajax_request() === FALSE)
          redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['inventory']['notes'] = $_GET['data'];
    }

    public function search_budget()
    {
        if ($this->input->is_ajax_request() === FALSE)
          redirect($this->modules['secure']['route'] .'/denied');

        $category = $_SESSION['inventory']['category_name'];
        $entities = $this->model->searchBudget($category);

        foreach ($entities as $key => $value){
            $entities[$key]['label'] = $value['product_name'];
            $entities[$key]['label'] .= ' || PN: ';
            $entities[$key]['label'] .= $value['product_code'];
            $entities[$key]['label'] .= '<small>';
            $entities[$key]['label'] .= 'Minimum Qty: <code>'. number_format($value['minimum_quantity'], 2) .'</code> || ';
            $entities[$key]['label'] .= 'On Hand Qty: <code>'. number_format($value['on_hand_quantity'], 2) .'</code> || ';
            $entities[$key]['label'] .= 'Month to Date Budget: <code>' . number_format($value['mtd_budget'], 2) . '</code> ||';
            $entities[$key]['label'] .= 'Month to Date Qty: <code>' . number_format($value['mtd_quantity'], 2) . '</code>';
            $entities[$key]['label'] .= 'Year to Date Budget: <code>' . number_format($value['maximum_price'], 2) . '</code> ||';
            $entities[$key]['label'] .= 'Year to Date Qty: <code>' . number_format($value['maximum_quantity'], 2) . '</code>';
            $entities[$key]['label'] .= '</small>';
        }

        echo json_encode($entities);
    }

    public function listAttachment($id)
    {
        $data = $this->model->listAttachment($id);
        echo json_encode($data);
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
        $config['upload_path'] = 'attachment/inventory_request/'.$_SESSION['inventory']['cost_center_name'].'/';
        $config['allowed_types'] = 'jpg|png|jpeg|doc|docx|xls|xlsx|pdf';
        $config['max_size']  = 2000;

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('attachment')) {
          $error = array('error' => $this->upload->display_errors());
        } else {

          $data = array('upload_data' => $this->upload->data());
          $url = $config['upload_path'] . $data['upload_data']['orig_name'];
          array_push($_SESSION["inventory"]["attachment"], $url);
          $result["status"] = 1;
        }
        echo json_encode($result);
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
        $cost_center = getCostCenterByIdRequest($id,'inventory');
        $config['upload_path'] = 'attachment/inventory_request/'.$cost_center['cost_center_name'].'/';
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
