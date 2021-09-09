<?php defined('BASEPATH') or exit('No direct script access allowed');

class Expense_Order_Evaluation extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['expense_order_evaluation'];
    $this->load->helper($this->module['helper']);
    $this->load->model($this->module['model'], 'model');
    $this->load->library('upload');
    $this->load->helper('string');
    $this->data['module'] = $this->module;
    if (empty($_SESSION['expense_poe']['source']))
      $_SESSION['expense_poe']['source'] = 1;
    if (empty($_SESSION['expense_poe']['attachment']))
      $_SESSION['expense_poe']['attachment'] = array();
  }

  public function set_doc_number()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (empty($_GET['data']))
      $number = poe_last_number();
    else
      $number = $_GET['data'];

    $_SESSION['expense_poe']['document_number'] = $number;
  }

  public function set_document_date()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['expense_poe']['document_date'] = $_GET['data'];
  }

  public function set_created_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['expense_poe']['created_by'] = $_GET['data'];
  }

  public function set_document_reference()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['expense_poe']['document_reference'] = $_GET['data'];
  }

  public function set_status()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['expense_poe']['status'] = $_GET['data'];
  }

  public function set_approved_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['expense_poe']['approved_by'] = $_GET['data'];
  }

  public function set_default_currency()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['expense_poe']['default_currency'] = $_GET['data'];
  }

  public function set_default_approval()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['expense_poe']['approval'] = $_GET['data'];
  }

  public function set_exchange_rate()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['expense_poe']['exchange_rate'] = $_GET['data'];
  }

  public function set_notes()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['expense_poe']['notes'] = $_GET['data'];
  }

  public function search_request_item()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $category = $_SESSION['expense_poe']['category'];
    $entities = $this->model->searchRequestItem($category);

    foreach ($entities as $key => $value) {
      if($value['total']-$value['process_amount']>0){
        $entities[$key]['label'] = $value['account_name'];
        $entities[$key]['label'] .= ' || Account Code: ';
        $entities[$key]['label'] .= $value['account_code'];
        $entities[$key]['label'] .= '<small>';
        $entities[$key]['label'] .= 'PR number: ' . $value['pr_number'] . ' || ';
        $entities[$key]['label'] .= 'PR date: ' . date('d/m/Y', strtotime($value['pr_date'])) . ' || ';
        $entities[$key]['label'] .= 'Required date: ' . date('d/m/Y', strtotime($value['required_date'])) . ' || ';
        $entities[$key]['label'] .= 'Amount: <code>' . number_format($value['total']-$value['process_amount']) . '</code>';
        $entities[$key]['label'] .= '</small>';
      }
    }

    echo json_encode($entities);
  }

  public function search_items_by_part_number()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $category = $_SESSION['expense_poe']['category'];
    $entities = $this->model->searchItemsByPartNumber($category);

    foreach ($entities as $key => $value) {
      $entities[$key]['label'] = 'PN : '.$value['part_number'];
      $entities[$key]['label'] .= ' | Desc : '.$value['part_number'];
    }

    echo json_encode($entities);
  }

  public function search_items_by_description()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $category = $_SESSION['expense_poe']['category'];
    $entities = $this->model->searchItemsByDescription($category);

    foreach ($entities as $key => $value) {
      $entities[$key]['label'] = 'PN : '.$value['part_number'];
      $entities[$key]['label'] .= ' | Desc : '.$value['part_number'];
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
        if (strtoupper($row['status']) == "EVALUATION") {
          if (config_item('auth_role') == 'PROCUREMENT MANAGER' || config_item('auth_role') == 'SUPER ADMIN') {
            $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
          } else {
            $col[] = print_number($no);
          }
        }elseif (strtoupper($row['status']) == strtoupper("waiting for purchase")) {
          if (config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'SUPER ADMIN') {
            $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
          } else {
            $col[] = print_number($no);
          }
        } else {
          $col[] = print_number($no);
        }
        $col[] = print_string($row['evaluation_number']);
        $col[] = print_string($row['purchase_request_number']);
        $col[] = print_date($row['document_date']);
        // $col[] = print_string($row['category']);
        $col[] = print_string($row['description']);
        $col[] = print_string($row['part_number']);
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
        if (strtoupper($row['status']) == "EVALUATION" && ((config_item('auth_role') == 'PROCUREMENT MANAGER')||(config_item('auth_role') == 'SUPER ADMIN'))) {
          $col[] = '<input type="text" id="note_' . $row['id'] . '" autocomplete="off"/>';
        }elseif (strtoupper($row['status']) == strtoupper("waiting for purchase") && (config_item('auth_role') == 'VP FINANCE'|| config_item('auth_role') == 'SUPER ADMIN')) {
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
    $data = $this->model->listAttachment($id);
    echo json_encode($data);
  }

  public function multi_reject()
  {
    $str_id_purchase_order = $this->input->post('id_purchase_order');
    $str_notes = $this->input->post('notes');
    $id_purchase_order = str_replace("|", "", $str_id_purchase_order);
    $id_purchase_order = substr($id_purchase_order, 0, -1);
    $id_purchase_order = explode(",", $id_purchase_order);
    
    $notes = str_replace("|", "", $str_notes);
    $notes = substr($notes, 0, -3);
    $notes = explode("##,", $notes);

    $result = $this->model->multi_reject($id_purchase_order, $notes);
    if ($result) {
      $this->model->send_mail_approval($id_purchase_order, 'rejected', config_item('auth_person_name'), $notes);
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
    $this->data['grid']['order_columns']    = array(
      // 0   => array( 0 => 2,  1 => 'desc' ),
      0   => array( 0 => 1,  1 => 'desc' ),
      1   => array( 0 => 2,  1 => 'desc' ),
      2   => array( 0 => 3,  1 => 'desc' ),
      3   => array( 0 => 4,  1 => 'desc' ),
      4   => array( 0 => 5,  1 => 'desc' ),
      5   => array( 0 => 6,  1 => 'desc' ),
      6   => array( 0 => 7,  1 => 'desc' ),
      7   => array( 0 => 8,  1 => 'desc' ),
      8   => array( 0 => 9,  1 => 'desc' ),
    );
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

      $return['type'] = 'success';
      $return['info'] = $this->load->view($this->module['view'] . '/info', $this->data, TRUE);
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

    $notes = str_replace("|", "", $str_notes);
    $notes = substr($notes, 0, -3);
    $notes = explode("##,", $notes);
    foreach ($id_purchase_order as $key) {
      if ($this->model->approve($key)) {
        $total++;
        $success++;
        $failed--;
      }
    }
    if ($success > 0) {
      $this->model->send_mail_approval($id_purchase_order, 'approve', config_item('auth_person_name'),$notes);
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

    if (!isset($_SESSION['expense_poe']['request'])) {
      $_SESSION['expense_poe']                     = $entity;
      $_SESSION['expense_poe']['id']               = $id;
      $_SESSION['expense_poe']['edit']             = $entity['evaluation_number'];
      $_SESSION['expense_poe']['document_number']  = $document_number;
      $_SESSION['expense_poe']['attachment'] = $entity['attachment'];
      $_SESSION['expense_poe']['approval'] = $entity['approved_by']=='without_approval'? 'without_approval':'with_approval';
    }

    redirect($this->module['route'] . '/create');
  }

  public function create($category = NULL)
  {
    $this->authorized($this->module, 'document');

    if ($category !== NULL) {
      $category = urldecode($category);

      $_SESSION['expense_poe']['request']             = array();
      $_SESSION['expense_poe']['vendors']             = array();
      $_SESSION['expense_poe']['warehouse']           = config_item('main_warehouse');
      $_SESSION['expense_poe']['category']            = $category;
      $_SESSION['expense_poe']['document_number']     = poe_last_number();
      $_SESSION['expense_poe']['document_date']       = date('Y-m-d');
      $_SESSION['expense_poe']['created_by']          = config_item('auth_person_name');
      $_SESSION['expense_poe']['document_reference']  = NULL;
      $_SESSION['expense_poe']['exchange_rate']       = 1.00;
      $_SESSION['expense_poe']['default_currency']    = 'IDR';
      $_SESSION['expense_poe']['approval']            = 'with_approval';
      $_SESSION['expense_poe']['status']              = 'evaluation';
      $_SESSION['expense_poe']['approved_by']         = NULL;
      $_SESSION['expense_poe']['total_quantity']      = NULL;
      $_SESSION['expense_poe']['total_price']         = NULL;
      $_SESSION['expense_poe']['grand_total']         = NULL;
      $_SESSION['expense_poe']['notes']               = NULL;

      redirect($this->module['route'] . '/create');
    }

    if (!isset($_SESSION['expense_poe']))
      redirect($this->module['route']);

    $this->data['page']['content']    = $this->module['view'] . '/create';

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
      if (!isset($_SESSION['expense_poe']['request']) || empty($_SESSION['expense_poe']['request']) || !isset($_SESSION['expense_poe']['vendors']) || empty($_SESSION['expense_poe']['vendors'])) {
        $data['success'] = FALSE;
        $data['message'] = 'Please add at least 1 request or vendor!';
      } else {
        $errors = array();
        $has_selected = FALSE;

        foreach ($_SESSION['expense_poe']['request'] as $key => $item) {
          foreach ($item['vendors'] as $d => $detail) {
            if ($detail['is_selected'] == 't') {
              $has_selected = TRUE;
            }
          }
        }

        if ($has_selected == FALSE) {
          $errors[] = 'No vendor qualified For one of Item! Please approve 1 vendor for 1 Item.';
        }

        $document_number = $_SESSION['expense_poe']['document_number'] . poe_format_number();

        if (isset($_SESSION['expense_poe']['edit'])) {
          if ($_SESSION['expense_poe']['edit'] != $document_number && $this->model->isDocumentNumberExists($document_number)) {
            $errors[] = 'Duplicate Document Number: ' . $_SESSION['expense_poe']['document_number'] . ' !';
          }
        } else {
          if ($this->model->isDocumentNumberExists($document_number)) {
            $errors[] = 'Duplicate Document Number: ' . $_SESSION['expense_poe']['document_number'] . ' !';
          }
        }

        if (!empty($errors)) {
          $data['success'] = FALSE;
          $data['message'] = implode('<br />', $errors);
        } else {
          if ($this->model->save()) {
            unset($_SESSION['expense_poe']);
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
  public function set_source()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['expense_poe']['source'] = $_GET['data'];
    $result['status'] = "success";
    echo json_encode($result);
  }
  public function add_request()
  {
    $this->authorized($this->module, 'document');

    $this->data['entities'] = $this->model->listRequest($_SESSION['expense_poe']['category']);
    $this->data['page']['title']            = "Add Request";

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
        $_SESSION['expense_poe']['request'] = array();

        foreach ($_POST['request_id'] as $key => $request_id) {
          $request = $this->model->infoRequest($request_id);

          $_SESSION['expense_poe']['request'][$request_id] = array(
            'description'             => $request['account_name'],
            'part_number'             => $request['account_code'],
            'alternate_part_number'   => NULL,
            'serial_number'           => NULL,
            'unit'                    => NULL,
            'quantity'                => floatval(1),
            'sisa'                    => floatval(1),
            'unit_price'              => floatval($request['amount']),
            'amount'                  => floatval($request['amount']),
            'core_charge'             => floatval(0),
            'total_amount'            => floatval($request['total']),
            'quantity_requested'      => floatval(1),
            'unit_price_requested'    => floatval($request['amount']),
            'total_amount_requested'  => floatval($request['total']),
            'unit'                    => NULL,
            'remarks'                 => $request['notes'],
            'purchase_request_number' => $request['pr_number'],
            'konversi'                => 1,
          );

          $_SESSION['expense_poe']['request'][$request_id]['inventory_purchase_request_detail_id'] = $request_id;
          $_SESSION['expense_poe']['request'][$request_id]['vendors'] = array();
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
    
    $this->data['page']['title']            = "Edit Request";

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
    $config['upload_path'] = 'attachment/expense_order_evaluation/';
    $config['allowed_types'] = 'jpg|png|jpeg|doc|docx|xls|xlsx|pdf';
    $config['max_size']  = 2000;

    $this->upload->initialize($config);

    if (!$this->upload->do_upload('attachment')) {
      $error = array('error' => $this->upload->display_errors());
    } else {

      $data = array('upload_data' => $this->upload->data());
      $url = $config['upload_path'] . $data['upload_data']['orig_name'];
      array_push($_SESSION['expense_poe']["attachment"], $url);
      $result["status"] = 1;
    }
    echo json_encode($result);
  }

  public function add_attachment_to_db($id_poe)
  {
    $result["status"] = 0;
    $date = new DateTime();
    // $config['file_name'] = $date->getTimestamp().random_string('alnum', 5);
    $config['upload_path'] = 'attachment/expense_order_evaluation/';
    $config['allowed_types'] = 'jpg|png|jpeg|doc|docx|xls|xlsx|pdf';
    $config['max_size']  = 2000;

    $this->upload->initialize($config);

    if (!$this->upload->do_upload('attachment')) {
      $error = array('error' => $this->upload->display_errors());
    } else {
      $data = array('upload_data' => $this->upload->data());
      $url = $config['upload_path'] . $data['upload_data']['orig_name'];
      // array_push($_SESSION['expense_poe']["attachment"], $url);
      $this->model->add_attachment_to_db($id_poe, $url);
      $result["status"] = 1;
    }
    echo json_encode($result);
  }

  public function delete_attachment($index)
  {
    $file = FCPATH . $_SESSION['expense_poe']["attachment"][$index];
    if (unlink($file)) {
      unset($_SESSION['expense_poe']["attachment"][$index]);
      $_SESSION['expense_poe']["attachment"] = array_values($_SESSION['expense_poe']["attachment"]);
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
          $quantity = floatval($_SESSION['expense_poe']['request'][$id]['quantity_requested']);

          $_SESSION['expense_poe']['request'][$id]['part_number'] = $request['part_number'];
          $_SESSION['expense_poe']['request'][$id]['amount']    = $request['amount'];
          $_SESSION['expense_poe']['request'][$id]['alternate_part_number'] = $request['alternate_part_number'];
          $_SESSION['expense_poe']['request'][$id]['remarks']     = $request['remarks'];
          $_SESSION['expense_poe']['request'][$id]['unit']     = $request['unit'];
          $_SESSION['expense_poe']['request'][$id]['konversi']     = $request['konversi'];

          foreach ($request['vendors'] as $key => $vendor) {
            // $_SESSION['expense_poe']['request'][$id]['alternate_part_number'] = $unit_price;

            $unit_price   = ($vendor['unit_price']!='')?$vendor['unit_price']:0;
            $core_charge  = ($vendor['core_charge']!='')?$vendor['core_charge']:0;
            $total_price  = ($unit_price * $request['quantity']) + ($core_charge * $request['quantity']);

            $_SESSION['expense_poe']['request'][$id]['vendors'][$key]['unit_price']   = $unit_price;
            // $_SESSION['expense_poe']['request'][$id]['vendors'][$key]['unit_price']   = $unit_price;
            $_SESSION['expense_poe']['request'][$id]['vendors'][$key]['quantity']     = $request['quantity'];
            $_SESSION['expense_poe']['request'][$id]['vendors'][$key]['core_charge']  = $core_charge;
            $_SESSION['expense_poe']['request'][$id]['vendors'][$key]['total']        = $total_price;
            $_SESSION['expense_poe']['request'][$id]['vendors'][$key]['left_received_quantity'] = $request['quantity'];
            $_SESSION['expense_poe']['request'][$id]['vendors'][$key]['left_paid_quantity']     = $request['quantity'];
            $_SESSION['expense_poe']['request'][$id]['vendors'][$key]['left_paid_amount']       = $total_price;
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

    if (isset($_SESSION['expense_poe']['request']))
      unset($_SESSION['expense_poe']['request'][$key]);
  }

  public function add_vendor()
  {
    $this->authorized($this->module, 'document');

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
        $_SESSION['expense_poe']['vendors'] = array();

        foreach ($_POST['vendor'] as $key => $vendor) {
          $vendor_currency = $vendor;
          $range_vendor_currency = explode('-', $vendor_currency);

          $_SESSION['expense_poe']['vendors'][$key]['vendor'] = $vendor;
          $_SESSION['expense_poe']['vendors'][$key]['vendor_currency'] = $range_vendor_currency[0];
          $_SESSION['expense_poe']['vendors'][$key]['vendor_name'] = $range_vendor_currency[1];
          $_SESSION['expense_poe']['vendors'][$key]['is_selected'] = 'f';
        }

        foreach ($_SESSION['expense_poe']['request'] as $id => $request) {
          $min = 0;
          $cheaper = 'f';
          foreach ($_POST['vendor'] as $key => $vendor) {
            // if($min>0){
            //   $cheaper = 't';
            //   $min = $request['unit_price_requested'];
            // }else{
            //   if($min > $request['unit_price_requested']){
            //     $cheaper = 't';
            //     $_SESSION['expense_poe']['request'][$id]['vendors'][$key]['is_cheaper']='f';
            //     $min = $request['unit_price_requested'];
            //   }else{
            //     $cheaper = 'f';
            //   }
            // }
            $_SESSION['expense_poe']['request'][$id]['vendors'][$key] = array(
              'vendor'                  => $vendor,
              'is_selected'             => 'f',
              'quantity'                => $request['quantity_requested'],
              'left_received_quantity'  => $request['quantity_requested'],
              'left_paid_quantity'      => $request['quantity_requested'],
              'unit_price'              => $request['unit_price_requested'],
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

  //   foreach ($_SESSION['expense_poe']['vendors'] as $v => $info){
  //     $_SESSION['expense_poe']['vendors'][$v]['is_selected'] = 'f';
  //   }

  //   $_SESSION['expense_poe']['vendors'][$key]['is_selected'] = 't';

  //   redirect($this->module['route'] .'/create');
  // }

  //new
  public function set_selected_vendor($item, $key_item)
  {
    $this->authorized($this->module, 'document');

    // foreach ($_SESSION['expense_poe']['request'] as $id => $request) {
    // foreach ($_SESSION['expense_poe']['vendor'] as $key => $vendor) {
    //   $_SESSION['expense_poe']['request'][$item]['vendors'][$key]['is_selected'] = 'f';
    // }
    // }

    foreach ($_SESSION['expense_poe']['vendors'] as $v => $info) {
      $_SESSION['expense_poe']['request'][$item]['vendors'][$v]['is_selected'] = 'f';
    }

    // $_SESSION['expense_poe']['vendors'][$key]['is_selected'] = 't';
    $_SESSION['expense_poe']['request'][$item]['vendors'][$key_item]['is_selected'] = 't';

    redirect($this->module['route'] . '/create');
  }

  public function discard()
  {
    $this->authorized($this->module['permission']['document']);
    foreach ($_SESSION['expense_poe']["attachment"] as $key) {
      $url = FCPATH . $key;
      if (is_file($url)) {
        unlink($url);
      }
    }
    unset($_SESSION['expense_poe']);

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

  public function add_item()
  {
    $this->authorized($this->module, 'document');

    if (isset($_POST) && !empty($_POST)) {
          $request_id = $this->input->post('inventory_purchase_request_detail_id');
          $_SESSION['expense_poe']['request'][] = array(
            'description'             => $this->input->post('description'),
            'part_number'             => $this->input->post('part_number'),
            'alternate_part_number'   => NULL,
            'serial_number'           => NULL,
            'unit'                    => $this->input->post('unit'),
            'group'                    => $this->input->post('group'),
            'quantity'                => $this->input->post('quantity'),
            'sisa'                    => $this->input->post('quantity'),
            'unit_price'              => $this->input->post('price'),
            'amount'                  => $this->input->post('total'),
            'core_charge'             => floatval(0),
            'total_amount'            => $this->input->post('total'),
            'quantity_requested'      => $this->input->post('quantity'),
            'unit_price_requested'    => $this->input->post('price'),
            'total_amount_requested'  => $this->input->post('total'),
            'remarks'                 => $this->input->post('remarks'),
            'purchase_request_number' => $this->input->post('purchase_request_number'),
            'konversi'                => 1,
            'inventory_purchase_request_detail_id' => $this->input->post('inventory_purchase_request_detail_id'),
            'vendors'                 => array()
          );

          // $_SESSION['expense_poe']['request'][$request_id]['inventory_purchase_request_detail_id'] = $request_id;
          // $_SESSION['expense_poe']['request'][$request_id]['vendors'] = array();
    }

    if(count($_SESSION['expense_poe']['request'])==1){
      $_SESSION['expense_poe']['vendors'] = array();
    }

    redirect($this->module['route'] . '/create');
  }
}
