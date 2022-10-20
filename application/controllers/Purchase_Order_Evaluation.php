<?php defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_Order_Evaluation extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['purchase_order_evaluation'];
    $this->load->helper($this->module['helper']);
    $this->load->model($this->module['model'], 'model');
    $this->load->library('upload');
    $this->load->helper('string');
    $this->data['module'] = $this->module;
    if (empty($_SESSION['poe']['source_request']))
      $_SESSION['poe']['source_request'] = 1;
    if (empty($_SESSION['poe']['attachment']))
      $_SESSION['poe']['attachment'] = array();
  }

  public function set_source($source)
  {
    $this->authorized($this->module, 'document');

    $source = urldecode($source);

    $_SESSION['poe']['source']              = $source;
    $_SESSION['poe']['request']             = array();
      $_SESSION['poe']['vendors']             = array();

    redirect($this->module['route'] . '/create');
  }

  public function set_doc_number()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (empty($_GET['data']))
      $number = poe_last_number();
    else
      $number = $_GET['data'];

    $_SESSION['poe']['document_number'] = $number;
  }

  public function set_document_date()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['poe']['document_date'] = $_GET['data'];
  }

  public function set_created_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['poe']['created_by'] = $_GET['data'];
  }

  public function set_document_reference()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['poe']['document_reference'] = $_GET['data'];
  }

  public function set_status()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['poe']['status'] = $_GET['data'];
  }

  public function set_approved_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['poe']['approved_by'] = $_GET['data'];
  }

  public function set_default_currency()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['poe']['default_currency'] = $_GET['data'];
  }

  public function set_default_approval()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['poe']['approval'] = $_GET['data'];
  }

  public function set_exchange_rate()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['poe']['exchange_rate'] = $_GET['data'];
  }

  public function set_notes()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['poe']['notes'] = $_GET['data'];
  }

  public function set_annual_cost_center_id()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['poe']['annual_cost_center_id'] = $_GET['data'];
    $cost_center = findCostCenterByAnnualCostCenterId($_GET['data']);
    $cost_center_code = $cost_center['cost_center_code'];
    $cost_center_name = $cost_center['cost_center_name'];          
    $department_id    = $cost_center['department_id'];

    $_SESSION['poe']['cost_center_id']          = $cost_center['id'];
    $_SESSION['poe']['cost_center_name']        = $cost_center_name;
    $_SESSION['poe']['cost_center_code']        = $cost_center_code;
    $_SESSION['poe']['department_id']           = $department_id;
  }

  public function set_head_dept()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['poe']['head_dept'] = $_GET['data'];
  }

  public function search_request_item()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $category = $_SESSION['poe']['category'];
    $entities = $this->model->searchRequestItem($category);

    foreach ($entities as $key => $value) {
      $entities[$key]['label'] = $value['product_name'];
      $entities[$key]['label'] .= ' || PN: ';
      $entities[$key]['label'] .= $value['part_number'];
      $entities[$key]['label'] .= '<small>';
      $entities[$key]['label'] .= 'PR number: ' . $value['pr_number'] . ' || ';
      $entities[$key]['label'] .= 'PR date: ' . date('d/m/Y', strtotime($value['pr_date'])) . ' || ';
      $entities[$key]['label'] .= 'Required date: ' . date('d/m/Y', strtotime($value['required_date'])) . ' || ';
      $entities[$key]['label'] .= 'Quantity: <code>' . number_format($value['quantity']) . '</code>';
      $entities[$key]['label'] .= '</small>';
    }

    echo json_encode($entities);
  }

  public function search_items_by_part_number()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $category = $_SESSION['poe']['category'];
    $entities = $this->model->searchItemsByPartNumber($category);

    foreach ($entities as $key => $value) {
      $entities[$key]['label'] = $value['part_number'];
    }

    echo json_encode($entities);
  }
  
  public function index_data_source()
  {
    // if ($this->input->is_ajax_request() === FALSE)
    //   redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'index') === FALSE) {
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {
      $entities = $this->model->getIndex();
      $data     = array();
      $no       = $_POST['start'];
      $quantity = array();

      foreach ($entities as $row) {
        $no++;
        $col = array();
        if(is_granted($this->module, 'approval')){
          if (strtoupper($row['status']) == "EVALUATION" && config_item('auth_username') == $row['head_dept']) {
            $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
          } else {
            $col[] = print_number($no);
          }
        }else{
          $col[] = print_number($no);
        }
        $col[] = print_string($row['evaluation_number']);
        $col[] = print_string($row['purchase_request_number']);
        $col[] = print_date($row['document_date']);
        $col[] = print_string($row['category']);
        $col[] = print_string($row['description']);
        $col[] = print_string($row['part_number']);
        $col[] = print_string($row['serial_number']);
        $col[] = print_number($row['quantity'], 2);
        $col[] = print_string($row['vendor']);
        $col[] = print_number($row['unit_price'], 2);
        $col[] = print_string(strtoupper($row['status']));
        $col[] = $row['attachment'] == null ? '' : '<a href="#" data-id="' . $row["id"] . '" class="btn btn-icon-toggle btn-info btn-sm ">
                       <i class="fa fa-eye"></i>
                     </a>';
        // $col[] ='<a href="#" data-id="'.$row["id"].'" class="btn btn-icon-toggle btn-info btn-sm ">
        //                <i class="fa fa-eye"></i>
        //             </a>';
        $col[] = print_string($row['notes']);
        if (strtoupper($row['status']) == "EVALUATION" && ((config_item('auth_username') == $row['head_dept']))) {
          $col[] = '<input type="text" id="note_' . $row['id'] . '" autocomplete="off"/>';
        } else {
          $col[] = null;
        }

        $col['DT_RowId'] = 'row_' . $row['id'];
        $col['DT_RowData']['pkey']  = $row['id'];

        if ($this->has_role($this->module, 'info')) {
          $col['DT_RowAttr']['onClick']     = '';
          $col['DT_RowAttr']['data-id']     = $row['id'];
          $col['DT_RowAttr']['data-target'] = '#data-modal';
          $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] . '/info/' . $row['id']);
        }

        $data[] = $col;
      }

      $result = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->model->countIndex(),
        "recordsFiltered" => $this->model->countIndexFiltered(),
        "data" => $data,
      );
    }

    echo json_encode($result);
  }
  
  public function listAttachment($id)
  {
    // $data = $this->model->listAttachment($id);
    // echo json_encode($data);
    $this->data['entity'] = $this->model->listAttachment($id);
    $return['info'] = $this->load->view($this->module['view'] . '/listAttachment', $this->data, TRUE);
    echo json_encode($return);
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
      $return["status"] = "success";
      echo json_encode($return);
    } else {
      $return["status"] = "failed";
      echo json_encode($return);
    }
  }
  
  public function index()
  {
    $this->authorized($this->module, 'index');

    $this->data['page']['title']            = $this->module['label'];
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] . '/index_data_source');
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = NULL;
    $this->data['grid']['order_columns']    = array();
    $grid2 = array("note" => "Note");
    $grid = $this->model->getSelectedColumns() + $grid2;
    $this->data['grid']['column']           = array_values($grid);

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
      $this->data['edit'] = $this->model->getStatusEditPoe($id);

      $return['type'] = 'success';
      $return['info'] = $this->load->view($this->module['view'] . '/info-2', $this->data, TRUE);
    }

    echo json_encode($return);
  }

  public function approve($id)
  {
    $this->authorized($this->module, 'approval');

    if ($this->model->approve($id)) {
      //$this->sendEmailHOS();
      redirect($this->module['route']);
    } else {
      die('error!');
    }
  }

  public function multi_approve()
  {
    $id_purchase_order = $this->input->post('id_purchase_order');
    $id_purchase_order = str_replace("|", "", $id_purchase_order);
    $id_purchase_order = substr($id_purchase_order, 0, -1);
    $id_purchase_order = explode(",", $id_purchase_order);
    $total = 0;
    $success = 0;
    $failed = sizeof($id_purchase_order);
    foreach ($id_purchase_order as $key) {
      if ($this->model->approve($key)) {
        $total++;
        $success++;
        $failed--;
      }
    }
    if ($success > 0) {
      if($this->config->item('access_from')!='localhost'){
        $this->model->send_mail_approval($id_purchase_order, 'approve', config_item('auth_person_name'));
      }
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

  public function print_pdf($id)
  {
    $this->authorized($this->module, 'print');

    $entity = $this->model->findById($id);

    $this->data['entity']           = $entity;
    $this->data['page']['title']    = strtoupper($this->module['label']);
    $this->data['page']['content']  = $this->module['view'] . '/print_pdf';

    $html = $this->load->view($this->pdf_theme, $this->data, true);

    $pdfFilePath = str_replace('/', '-', $entity['document_number']) . ".pdf";

    $this->load->library('m_pdf');

    $pdf = $this->m_pdf->load(null, 'A4-L');
    $pdf->WriteHTML($html);
    $pdf->Output($pdfFilePath, "I");
  }

  public function edit($id)
  {
    $this->authorized($this->module, 'document');

    $entity = $this->model->findById($id);
    $document_number  = sprintf('%06s', substr($entity['evaluation_number'], 0, 6));

    if (!isset($_SESSION['poe']['request'])) {
      $_SESSION['poe']                     = $entity;
      $_SESSION['poe']['id']               = $id;
      $_SESSION['poe']['edit']             = $entity['evaluation_number'];
      $_SESSION['poe']['document_number']  = $document_number;
      $_SESSION['poe']['attachment'] = $entity['attachment'];
      
      $_SESSION['poe']['approval'] = $entity['approved_by']=='without_approval'? 'without_approval':'with_approval';
    }

    redirect($this->module['route'] . '/create');
  }

  public function create($category = NULL)
  {
    $this->authorized($this->module, 'document');

    if ($category !== NULL) {
      $category = urldecode($category);

      $_SESSION['poe']['request']             = array();
      $_SESSION['poe']['vendors']             = array();
      $_SESSION['poe']['warehouse']           = config_item('auth_warehouse');
      $_SESSION['poe']['category']            = $category;
      $_SESSION['poe']['document_number']     = poe_last_number();
      $_SESSION['poe']['document_date']       = date('Y-m-d');
      $_SESSION['poe']['created_by']          = config_item('auth_person_name');
      $_SESSION['poe']['document_reference']  = NULL;
      $_SESSION['poe']['exchange_rate']       = 1.00;
      $_SESSION['poe']['default_currency']    = 'IDR';
      $_SESSION['poe']['approval']            = 'with_approval';
      $_SESSION['poe']['status']              = 'evaluation';
      $_SESSION['poe']['approved_by']         = NULL;
      $_SESSION['poe']['total_quantity']      = NULL;
      $_SESSION['poe']['total_price']         = NULL;
      $_SESSION['poe']['grand_total']         = NULL;
      $_SESSION['poe']['notes']               = NULL;
      $_SESSION['poe']['tipe']                = 'INVENTORY MRP';
      $_SESSION['poe']['source']                  = 'request';
      $_SESSION['poe']['annual_cost_center_id']   = null;
      $_SESSION['poe']['cost_center_id']          = null;
      $_SESSION['poe']['cost_center_name']        = null;
      $_SESSION['poe']['cost_center_code']        = null;
      $_SESSION['poe']['department_id']           = null;
      $_SESSION['poe']['head_dept']               = NULL;

      redirect($this->module['route'] . '/create');
    }

    if (!isset($_SESSION['poe']))
      redirect($this->module['route']);

    $this->data['page']['content']    = $this->module['view'] . '/create';

    $this->render_view($this->module['view'] . '/create-2');
  }

  public function save()
  {
    // if ($this->input->is_ajax_request() == FALSE)
    //   redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') == FALSE) {
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      if (!isset($_SESSION['poe']['request']) || empty($_SESSION['poe']['request']) || !isset($_SESSION['poe']['vendors']) || empty($_SESSION['poe']['vendors'])) {
        $data['success'] = FALSE;
        $data['message'] = 'Please add at least 1 request or vendor!';
      } else {
        $errors = array();
        $has_selected = FALSE;

        foreach ($_SESSION['poe']['request'] as $key => $item) {
          foreach ($item['vendors'] as $d => $detail) {
            if ($detail['is_selected'] == 't') {
              $has_selected = TRUE;
            }
          }
        }

        if ($has_selected == FALSE) {
          $errors[] = 'No vendor qualified For one of Item! Please approve 1 vendor for 1 Item.';
        }

        $document_number = $_SESSION['poe']['document_number'] . poe_format_number();

        if (isset($_SESSION['poe']['edit'])) {
          if ($_SESSION['poe']['edit'] != $document_number && $this->model->isDocumentNumberExists($document_number)) {
            $errors[] = 'Duplicate Document Number: ' . $_SESSION['poe']['document_number'] . ' !';
          }
        } else {
          if ($this->model->isDocumentNumberExists($document_number)) {
            $_SESSION['poe']['document_number']     = poe_last_number();
            $document_number = $_SESSION['poe']['document_number'] . poe_format_number();
            // $errors[] = 'Duplicate Document Number: ' . $_SESSION['poe']['document_number'] . ' !';
          }
        }

        if (!empty($errors)) {
          $data['success'] = FALSE;
          $data['message'] = implode('<br />', $errors);
        } else {
          if ($this->model->save()) {
            unset($_SESSION['poe']);
            // $this->sendEmail();
            $data['success'] = TRUE;
            $data['message'] = 'Document ' . $document_number . ' has been saved. You will redirected now.';
          } else {
            $data['success'] = FALSE;
            $data['message'] = 'Error while saving this document. Please ask Technical Support.';
          }
        }
      }
    }

    echo json_encode($data);
  }
  public function set_source_request()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['poe']['source_request'] = $_GET['data'];
    $result['status'] = "success";
    echo json_encode($result);
  }
  public function add_request()
  {
    $this->authorized($this->module, 'document');

    $this->data['entities'] = $this->model->listRequest($_SESSION['poe']['category']);
    $this->data['page']['title']            = 'Add Request';

    $this->render_view($this->module['view'] . '/add_request');
  }

  public function add_selected_request()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') == FALSE) {
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      if (isset($_POST['request_id']) && !empty($_POST['request_id'])) {
        $_SESSION['poe']['request'] = array();

        foreach ($_POST['request_id'] as $key => $request_id) {
          $request = $this->model->infoRequest($request_id);

          $_SESSION['poe']['request'][$request_id] = array(
            'description'             => $request['product_name'],
            'part_number'             => $request['product_code'],
            'alternate_part_number'   => ($_SESSION['poe']['source']=='return')?$request['alternate_part_number']:null,
            'serial_number'           => $request['serial_number'],
            'unit'                    => $request['unit'],
            'quantity'                => floatval($request['sisa']),
            'sisa'                    => floatval($request['sisa']),
            'unit_price'              => floatval($request['price']),
            'core_charge'             => floatval(0),
            'total_amount'            => floatval($request['quantity']) * floatval($request['price']),
            'quantity_requested'      => floatval($request['quantity']),
            'unit_price_requested'    => floatval($request['price']),
            'total_amount_requested'  => floatval($request['quantity']) * floatval($request['price']),
            'unit'                    => $request['unit'],
            'remarks'                 => $request['remarks'],
            'purchase_request_number' => $request['pr_number'],
            'konversi'                => 1,
            'group'                   => $request['group_name'],
          );

          $_SESSION['poe']['request'][$request_id]['inventory_purchase_request_detail_id'] = ($_SESSION['poe']['source']=='request')?$request_id:null;
          $_SESSION['poe']['request'][$request_id]['return_item_id'] = ($_SESSION['poe']['source']=='return')?$request_id:null;
          $_SESSION['poe']['request'][$request_id]['vendors'] = array();
        }

        $data['success'] = TRUE;
      } else {
        $data['success'] = FALSE;
        $data['message'] = 'Please select any request!';
      }
    }

    echo json_encode($data);
  }

  public function edit_request()
  {
    $this->authorized($this->module, 'document');

    $this->render_view($this->module['view'] . '/edit_request');
  }

  public function attachment()
  {
    $this->authorized($this->module, 'document');

    $this->render_view($this->module['view'] . '/attachment');
  }

  public function manage_attachment($id_poe)
  {
    $this->authorized($this->module, 'document');

    $this->data['manage_attachment'] = $this->model->listAttachment_2($id_poe);
    $this->data['id_poe'] = $id_poe;
    $this->render_view($this->module['view'] . '/manage_attachment');
  }


  public function add_attachment()
  {
    $result["status"] = 0;
    $date = new DateTime();
    // $config['file_name'] = $date->getTimestamp().random_string('alnum', 5);
    $config['upload_path'] = 'attachment/';
    $config['allowed_types'] = 'jpg|png|jpeg|doc|docx|xls|xlsx|pdf';
    $config['max_size']  = 2000;

    $this->upload->initialize($config);

    if (!$this->upload->do_upload('attachment')) {
      $error = array('error' => $this->upload->display_errors());
    } else {

      $data = array('upload_data' => $this->upload->data());
      $url = $config['upload_path'] . $data['upload_data']['file_name'];
      array_push($_SESSION["poe"]["attachment"], $url);
      $result["status"] = 1;
    }
    echo json_encode($result);
  }

  public function add_attachment_to_db($id_poe)
  {
    $result["status"] = 0;
    $date = new DateTime();
    // $config['file_name'] = $date->getTimestamp().random_string('alnum', 5);
    $config['upload_path'] = 'attachment/';
    $config['allowed_types'] = 'jpg|png|jpeg|doc|docx|xls|xlsx|pdf';
    $config['max_size']  = 2000;

    $this->upload->initialize($config);

    if (!$this->upload->do_upload('attachment')) {
      $error = array('error' => $this->upload->display_errors());
    } else {
      $data = array('upload_data' => $this->upload->data());
      $url = $config['upload_path'] . $data['upload_data']['file_name'];
      // array_push($_SESSION["poe"]["attachment"], $url);
      $this->model->add_attachment_to_db($id_poe, $url);
      $result["status"] = 1;
    }
    echo json_encode($result);
  }

  public function delete_attachment($index)
  {
    $file = FCPATH . $_SESSION["poe"]["attachment"][$index];
    if (unlink($file)) {
      unset($_SESSION["poe"]["attachment"][$index]);
      $_SESSION["poe"]["attachment"] = array_values($_SESSION["poe"]["attachment"]);
      redirect($this->module['route'] . "/attachment", 'refresh');
    }
  }

  public function delete_attachment_in_db($id_att, $id_poe)
  {
    $this->model->delete_attachment_in_db($id_att);

    redirect($this->module['route'] . "/manage_attachment/" . $id_poe, 'refresh');
    // echo json_encode($result);
  }

  public function update_request()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') == FALSE) {
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      if (isset($_POST['request']) && !empty($_POST['request'])) {
        foreach ($_POST['request'] as $id => $request) {
          $quantity = floatval($_SESSION['poe']['request'][$id]['quantity_requested']);

          $_SESSION['poe']['request'][$id]['part_number']           = $request['part_number'];
          $_SESSION['poe']['request'][$id]['serial_number']         = $request['serial_number'];
          $_SESSION['poe']['request'][$id]['quantity']              = $request['quantity'];
          $_SESSION['poe']['request'][$id]['alternate_part_number'] = $request['alternate_part_number'];
          $_SESSION['poe']['request'][$id]['remarks']               = $request['remarks'];
          $_SESSION['poe']['request'][$id]['unit']                  = $request['unit'];
          $_SESSION['poe']['request'][$id]['konversi']              = $request['konversi'];

          foreach ($request['vendors'] as $key => $vendor) {
            // $_SESSION['poe']['request'][$id]['alternate_part_number'] = $unit_price;

            $unit_price   = $vendor['unit_price'];
            $core_charge  = $vendor['core_charge'];
            $total_price  = ($unit_price * $request['quantity']) + ($core_charge * $request['quantity']);

            $_SESSION['poe']['request'][$id]['vendors'][$key]['unit_price']   = $unit_price;
            $_SESSION['poe']['request'][$id]['vendors'][$key]['unit_price']   = $unit_price;
            $_SESSION['poe']['request'][$id]['vendors'][$key]['quantity']     = $request['quantity'];
            $_SESSION['poe']['request'][$id]['vendors'][$key]['core_charge']  = $core_charge;
            $_SESSION['poe']['request'][$id]['vendors'][$key]['total']        = $total_price;
            $_SESSION['poe']['request'][$id]['vendors'][$key]['left_received_quantity'] = $request['quantity'];
            $_SESSION['poe']['request'][$id]['vendors'][$key]['left_paid_quantity']     = $request['quantity'];
            $_SESSION['poe']['request'][$id]['vendors'][$key]['left_paid_amount']       = $total_price;
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

  public function delete_request($key)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (isset($_SESSION['poe']['request']))
      unset($_SESSION['poe']['request'][$key]);
  }

  public function add_vendor()
  {
    $this->authorized($this->module, 'document');
    $this->data['page']['title']            = 'Add Vendor';

    $this->render_view($this->module['view'] . '/add_vendor');
  }

  public function add_selected_vendor()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') == FALSE) {
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      if (isset($_POST['vendor']) && !empty($_POST['vendor'])) {
        $_SESSION['poe']['vendors'] = array();

        foreach ($_POST['vendor'] as $key => $vendor) {
          $vendor_currency = $vendor;
          $range_vendor_currency = explode('-', $vendor_currency);

          $_SESSION['poe']['vendors'][$key]['vendor'] = $vendor;
          $_SESSION['poe']['vendors'][$key]['vendor_currency'] = $range_vendor_currency[0];
          $_SESSION['poe']['vendors'][$key]['vendor_name'] = $range_vendor_currency[1];
          $_SESSION['poe']['vendors'][$key]['is_selected'] = 'f';
        }

        foreach ($_SESSION['poe']['request'] as $id => $request) {
          $min = 0;
          $cheaper = 'f';
          foreach ($_POST['vendor'] as $key => $vendor) {
            // if($min>0){
            //   $cheaper = 't';
            //   $min = $request['unit_price_requested'];
            // }else{
            //   if($min > $request['unit_price_requested']){
            //     $cheaper = 't';
            //     $_SESSION['poe']['request'][$id]['vendors'][$key]['is_cheaper']='f';
            //     $min = $request['unit_price_requested'];
            //   }else{
            //     $cheaper = 'f';
            //   }
            // }
            $_SESSION['poe']['request'][$id]['vendors'][$key] = array(
              'vendor'                  => $vendor,
              'is_selected'             => 'f',
              'quantity'                => $request['quantity_requested'],
              'left_received_quantity'  => $request['quantity_requested'],
              'left_paid_quantity'      => $request['quantity_requested'],
              // 'unit_price'              => $request['unit_price_requested'],
              'unit_price'              => 0,
              'purchase_request_number' => $request['purchase_request_number'],
              'core_charge'             => floatval(0),
              'total'                   => $request['quantity_requested'] * $request['quantity_requested'],
              'left_paid_amount'        => $request['quantity_requested'] * $request['quantity_requested'],
              'is_cheaper'              => $cheaper,
              'remarks'                 => $request['remarks'],
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

  // public function set_selected_vendor($key)
  // {
  //   $this->authorized($this->module, 'document');

  //   foreach ($_SESSION['poe']['vendors'] as $v => $info){
  //     $_SESSION['poe']['vendors'][$v]['is_selected'] = 'f';
  //   }

  //   $_SESSION['poe']['vendors'][$key]['is_selected'] = 't';

  //   redirect($this->module['route'] .'/create');
  // }

  //new
  public function set_selected_vendor($item, $key_item)
  {
    $this->authorized($this->module, 'document');

    // foreach ($_SESSION['poe']['request'] as $id => $request) {
    // foreach ($_SESSION['poe']['vendor'] as $key => $vendor) {
    //   $_SESSION['poe']['request'][$item]['vendors'][$key]['is_selected'] = 'f';
    // }
    // }

    foreach ($_SESSION['poe']['vendors'] as $v => $info) {
      $_SESSION['poe']['request'][$item]['vendors'][$v]['is_selected'] = 'f';
    }

    // $_SESSION['poe']['vendors'][$key]['is_selected'] = 't';
    $_SESSION['poe']['request'][$item]['vendors'][$key_item]['is_selected'] = 't';

    redirect($this->module['route'] . '/create');
  }

  public function discard()
  {
    $this->authorized($this->module['permission']['document']);
    foreach ($_SESSION['poe']["attachment"] as $key) {
      $url = FCPATH . $key;
      if (is_file($url)) {
        unlink($url);
      }
    }
    unset($_SESSION['poe']);

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

      if ($this->model->isValidDocumentQuantity($entity['document_number']) === FALSE) {
        $alert['type']  = 'danger';
        $alert['info']  = 'Stock quantity for document ' . $entity['document_number'] . ' has been change. You are not allowed to delete this document. You can adjust stock to sync the quantity.';
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

  public function get_head_dept_user()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $department_id = $_SESSION['poe']['department_id'];
    $entities = list_user_in_head_department($department_id);

    echo json_encode($entities);
  }
}
