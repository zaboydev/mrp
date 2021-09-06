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
    if (empty($_SESSION['poe']['source']))
      $_SESSION['poe']['source'] = 1;
    if (empty($_SESSION['poe']['attachment']))
      $_SESSION['poe']['attachment'] = array();
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

  public function sendEmail()
  {
    $recipientList = $this->model->getNotifRecipient(9);
    $recipient = array();
    foreach ($recipientList as $key) {
      array_push($recipient, $key->email);
    }
    $this->load->library('email');
    $config = array(
      'protocol' => 'smtp',
      'smtp_host' => 'smtp.mailtrap.io',
      'smtp_port' => 2525,
      'smtp_user' => '8fe5a91a10cc87',
      'smtp_pass' => '1cd529218bc7b0',
      'crlf' => "\r\n",
      'newline' => "\r\n"
    );
    $this->email->initialize($config);
    $this->email->from('bifa.Team@gmail.com', 'Bifa Team');
    $this->email->to($recipient);
    $html = '<html><head>
    <meta http-equiv="\&quot;Content-Type\&quot;" content="\&quot;text/html;" charset="utf-8\&quot;">
    <style>
    .content {
      max-width: 500px;
      margin: auto;
    }
    .title{
      width: 60%;
    }
    </style></head>
    <body> 
    <div class="content">
    <div bgcolor="#0aa89e">
    <table align="center" bgcolor="#fff" border="0" cellpadding="0" cellspacing="0" style="background-color:#fff;margin:5% auto;width:100%;max-width:600px">
    <tbody><tr>
    <td>
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#0aa89e" style="padding:10px 15px;font-size:14px">
      <tbody><tr>
      <td width="60%" align="left" style="padding:5px 0 0">
      <span style="font-size:18px;font-weight:300;color:#ffffff">
                                BIFA
                            </span>
                        </td>
                        <td width="40%" align="right" style="padding:5px 0 0">
                            <span style="font-size:18px;font-weight:300;color:#ffffff">
                                Notification
                            </span>
                        </td>
                    </tr>
                </tbody></table>
            </td>
        </tr>        
        <tr>
            <td style="padding:25px 15px 10px">
                <table width="100%">
                    <tbody><tr>
                        <td>
                            <h1 style="margin:0;font-size:16px;font-weight:bold;line-height:24px;color:rgba(0,0,0,0.70)">Halo Team</h1>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="margin:0;font-size:16px;line-height:24px;color:rgba(0,0,0,0.70)">Ada item baru pada daftar Purchase Order Evaluation silakan di cek</p>
                        </td>
                    </tr>
                </tbody></table>
            </td>
        </tr>
    </tbody></table>
    <p>&nbsp;<br></p>
    </div></div>  
    </body></html>';
    $this->email->subject('Notification');
    $this->email->message($html);

    $this->email->send();
  }

  public function sendEmailHOS()
  {
    $recipientList = $this->model->getNotifRecipientHOS();
    $recipient = array();
    foreach ($recipientList as $key) {
      array_push($recipient, $key->email);
    }
    $this->load->library('email');
    $config = array(
      'protocol' => 'smtp',
      'smtp_host' => 'smtp.mailtrap.io',
      'smtp_port' => 2525,
      'smtp_user' => '8fe5a91a10cc87',
      'smtp_pass' => '1cd529218bc7b0',
      'crlf' => "\r\n",
      'newline' => "\r\n"
    );
    $this->email->initialize($config);
    $this->email->from('bifa.Team@gmail.com', 'Bifa Team');
    $this->email->to($recipient);
    $html = '<html><head> 
                          <meta http-equiv="\&quot;Content-Type\&quot;" content="\&quot;text/html;" charset="utf-8\&quot;">
                          <style>
                              .content {
                                  max-width: 500px;
                                  margin: auto;
                              }
                              .title{
                                  width: 60%;
                              }

                          </style></head>
                          
                          <body> 
                              <div class="content">
      <div bgcolor="#0aa89e">
          <table align="center" bgcolor="#fff" border="0" cellpadding="0" cellspacing="0" style="background-color:#fff;margin:5% auto;width:100%;max-width:600px">
              
              <tbody><tr>
                  <td>
                      <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#0aa89e" style="padding:10px 15px;font-size:14px">
                          <tbody><tr>
                              <td width="60%" align="left" style="padding:5px 0 0">
                                  <span style="font-size:18px;font-weight:300;color:#ffffff">
                                      BIFA
                                  </span>
                              </td>
                              <td width="40%" align="right" style="padding:5px 0 0">
                                  <span style="font-size:18px;font-weight:300;color:#ffffff">
                                      Notification
                                  </span>
                              </td>
                          </tr>
                      </tbody></table>
                  </td>
              </tr>        
              <tr>
                  <td style="padding:25px 15px 10px">
                      <table width="100%">
                          <tbody><tr>
                              <td>
                                  <h1 style="margin:0;font-size:16px;font-weight:bold;line-height:24px;color:rgba(0,0,0,0.70)">Halo Team</h1>
                              </td>
                          </tr>
                          <tr>
                              <td>
                                  <p style="margin:0;font-size:16px;line-height:24px;color:rgba(0,0,0,0.70)">Ada item baru pada daftar Purchase Order  silakan di cek</p>
                              </td>
                          </tr>
                      </tbody></table>
                  </td>
              </tr>
          </tbody></table>
      <p>&nbsp;<br></p>
      </div>

                                  </div>  
                                          
                              
      </body></html>';
    $this->email->subject('Notification Purchase Order');
    $this->email->message($html);

    $this->email->send();
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
          if (config_item('auth_role') == 'CHIEF OF MAINTANCE' || config_item('auth_role') == 'SUPER ADMIN') {
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
        if (strtoupper($row['status']) == "EVALUATION" && ((config_item('auth_role') == 'CHIEF OF MAINTANCE'))) {
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
            $errors[] = 'Duplicate Document Number: ' . $_SESSION['poe']['document_number'] . ' !';
          }
        }

        if (!empty($errors)) {
          $data['success'] = FALSE;
          $data['message'] = implode('<br />', $errors);
        } else {
          if ($this->model->save()) {
            unset($_SESSION['poe']);
            $this->sendEmail();
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

    $_SESSION['poe']['source'] = $_GET['data'];
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
            'alternate_part_number'   => NULL,
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
          );

          $_SESSION['poe']['request'][$request_id]['inventory_purchase_request_detail_id'] = $request_id;
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
      $url = $config['upload_path'] . $data['upload_data']['orig_name'];
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
      $url = $config['upload_path'] . $data['upload_data']['orig_name'];
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
}
