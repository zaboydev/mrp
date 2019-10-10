<?php defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_Order extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['purchase_order'];
    $this->load->helper($this->module['helper']);
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function index()
  {
    $this->authorized($this->module, 'index');

    $this->data['page']['title']            = $this->module['label'];
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] . '/index_data_source');
    $this->data['grid']['fixed_columns']    = 3;
    if ((config_item('auth_role') == 'VP FINANCE') || (config_item('auth_role') == 'FINANCE MANAGER') || (config_item('auth_role') == 'HEAD OF SCHOOL') || (config_item('auth_role') == 'CHIEF OF FINANCE')) {
      $this->data['grid']['summary_columns']  = array(13, 16);
    } else {
      $this->data['grid']['summary_columns']  = array(12, 15);
    }
    $this->data['grid']['order_columns']    = array();
    $this->render_view($this->module['view'] . '/index');
  }

  public function index_data_source()
  {
    // if ($this->input->is_ajax_request() === FALSE)
    //   redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'index') === FALSE) {
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {
      $entities     = $this->model->getIndex();
      $data         = array();
      $no           = $_POST['start'];
      $quantity     = array();
      $total_amount = array();
      $amount_paid  = array();

      foreach ($entities as $row) {
        $no++;
        $col = array();

        if ((config_item('auth_role') == 'VP FINANCE') || (config_item('auth_role') == 'FINANCE MANAGER') || (config_item('auth_role') == 'HEAD OF SCHOOL') || (config_item('auth_role') == 'CHIEF OF FINANCE') || (config_item('auth_role') == 'CHIEF OPERATION OFFICER')) {
          if ((config_item('auth_role') == 'FINANCE MANAGER') && ($row['review_status'] == strtoupper("waiting for finance review"))) {
            $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
          } else if ((config_item('auth_role') == 'HEAD OF SCHOOL') && ($row['review_status'] == strtoupper("waiting for hos review"))) {
            $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
          } else if ((config_item('auth_role') == 'CHIEF OF FINANCE') && ($row['review_status'] == strtoupper("waiting for cfo review"))) {
            $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
          } else if ((config_item('auth_role') == 'VP FINANCE') && ($row['review_status'] == strtoupper("waiting for vp finance review"))) {
            $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
          } else if ((config_item('auth_role') == 'CHIEF OPERATION OFFICER') && ($row['review_status'] == strtoupper("waiting for coo review"))) {
            $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
          } else {
            $col[] = '';
          }
        }
        // if ((config_item('auth_role') == 'PROCUREMENT') || (config_item('auth_role') == 'SUPER ADMIN')) {
        //   if ($row['review_status'] == strtoupper("approved")) {
        //     $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
        //   } else {
        //     $col[] = '';
        //   }
        // }
        $col[] = print_number($no);
        $col[] = print_string($row['document_number'], 'N/A');
        $col[] = print_string($row['review_status']);
        if ((config_item('auth_role') != 'HEAD OF SCHOOL') && (config_item('auth_role') != 'CHIEF OF FINANCE') && (config_item('auth_role') != 'FINANCE MANAGER') && (config_item('auth_role') != 'VP FINANCE') && (config_item('auth_role') != 'CHIEF OPERATION OFFICER')) {

          $col[] = print_string($row['status']);
        }
        $col[] = print_date($row['document_date']);
        // $col[] = print_string($row['category']);
        $col[] = print_string($row['description']);
        $col[] = print_string($row['part_number']);
        $col[] = print_string($row['alternate_part_number']);
        $col[] = '<a href="' . site_url($this->modules['purchase_order_evaluation']['route'] . '/print_pdf/' . $row['poe_id']) . '" target="_blank" >' . print_string($row['poe_number']) . '</a><a href="#" class="btn btn-icon-toggle btn-info btn-sm "><i class="fa fa-eye" data-id="' . $row['poe_id'] . '"></i></a>';
        $col[] = '<a href="' . site_url($this->modules['purchase_request']['route'] . '/print_pdf_prl/' . $row['poe_item_id']) . '" target="_blank" >' . print_string($row['purchase_request_number']) . '</a>';
        $col[] = print_string($row['reference_quotation']);
        $col[] = strtoupper($row['vendor']);
        if ((config_item('auth_role') == 'HEAD OF SCHOOL') || (config_item('auth_role') == 'CHIEF OF FINANCE') || (config_item('auth_role') == 'FINANCE MANAGER') || (config_item('auth_role') == 'VP FINANCE') || (config_item('auth_role') == 'CHIEF OPERATION OFFICER')) {
          $col[] = print_number($row['quantity'], 2);
          $col[] = print_number($row['unit_price'], 2);
          $col[] = print_number($row['core_charge'], 2);
          $col[] = print_number($row['total_amount'], 2);
          if ($row['review_status'] === "APPROVED" || $row[$field] === "1") {
            $col[] = '';
          } else {
            $col[] = '<input type="text" id="note_' . $row['id'] . '" autocomplete="off"/>';
          }
          $col[] = null;
          $col[] = null;
          $col[] = null;
          $col[] = null;
          $col[] = null;
          $col[] = null;
        } else {
          $col[] = print_number($row['quantity'], 2);
          $col[] = print_number($row['unit_price'], 2);
          $col[] = print_number($row['core_charge'], 2);
          $col[] = print_number($row['total_amount'], 2);
          $col[] = print_number($row['quantity_received'], 2);
          $col[] = print_number($row['left_received_quantity'], 2);
          // $col[] = print_number($row['amount_paid'], 2);   
          if ($row['review_status'] === "APPROVED" || $row[$field] === "1") {
            $col[] = '';
          } else {
            if ($row['document_number'] == null) {
              $col[] = '';
            } else {
              if ((config_item('auth_role') == 'HEAD OF SCHOOL') || (config_item('auth_role') == 'CHIEF OF FINANCE')) {
                $col[] = '<input type="text" id="note_' . $row['id'] . '" autocomplete="off"/>';
              } else {
                $col[] = '';
              }
            }
          }
          $col[] = null;
          $col[] = null;
          $col[] = null;
          $col[] = null;
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

        // if ($this->has_role($this->module, 'payment') && $row['status'] == 'ORDER') {
        //   $col['DT_RowAttr']['onClick']     = '$(this).popup();';
        //   $col['DT_RowAttr']['data-target'] = '#data-modal';
        //   $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] . '/payment/' . $row['id']);
        // }

        $quantity[]     = $row['quantity'];
        $total_amount[] = $row['total_amount'];
        $amount_paid[]  = $row['amount_paid'];
        $data[]         = $col;
      }

      $result = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->model->countIndex(),
        "recordsFiltered" => $this->model->countIndexFiltered(),
        "data" => $data,
        "total" => array(
          // 13 => print_number(array_sum($quantity),2),
          // 16 => print_number(array_sum($total_amount),2),
          // 19 => print_number(array_sum($amount_paid),2),
        )
      );
      if ((config_item('auth_role') == 'VP FINANCE') || (config_item('auth_role') == 'FINANCE MANAGER') || (config_item('auth_role') == 'HEAD OF SCHOOL') || (config_item('auth_role') == 'CHIEF OF FINANCE')) {
        // $result['total'][17] = print_number(array_sum($unit_value), 2);
        $result['total'][13] = print_number(array_sum($quantity), 2);
        $result['total'][16] = print_number(array_sum($total_amount), 2);
      } else {
        $result['total'][12] = print_number(array_sum($quantity), 2);
        $result['total'][15] = print_number(array_sum($total_amount), 2);
      }
    }

    echo json_encode($result);
  }

  public function set_doc_number()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (empty($_GET['data']))
      $number = order_last_number();
    else
      $number = $_GET['data'];

    $_SESSION['order']['document_number'] = $number;
  }

  public function set_document_date()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['document_date'] = $_GET['data'];
  }

  public function set_term_payment()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['term_payment'] = $_GET['data'];
  }

  public function set_issued_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['issued_by'] = $_GET['data'];
  }

  public function set_default_currency()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['default_currency'] = $_GET['data'];
  }

  public function set_payment_type()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['payment_type'] = $_GET['data'];
  }

  public function set_exchange_rate()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['exchange_rate'] = $_GET['data'];
  }

  public function set_discount()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['discount'] = $_GET['data'];
  }

  public function set_taxes()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['taxes'] = $_GET['data'];
  }

  public function set_shipping_cost()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['shipping_cost'] = $_GET['data'];
  }

  public function set_checked_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['checked_by'] = $_GET['data'];
  }

  public function set_approved_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['approved_by'] = $_GET['data'];
  }

  public function set_notes()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['notes'] = $_GET['data'];
  }

  public function set_deliver_company()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['deliver_company'] = $_GET['data'];
  }

  public function set_deliver_address()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['deliver_address'] = $_GET['data'];
  }

  public function set_deliver_country()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['deliver_country'] = $_GET['data'];
  }

  public function set_deliver_phone()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['deliver_phone'] = $_GET['data'];
  }

  public function set_deliver_attention()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['deliver_attention'] = $_GET['data'];
  }

  public function set_bill_company()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['bill_company'] = $_GET['data'];
  }

  public function set_bill_address()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['bill_address'] = $_GET['data'];
  }

  public function set_bill_country()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['bill_country'] = $_GET['data'];
  }

  public function set_bill_phone()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['bill_phone'] = $_GET['data'];
  }

  public function set_bill_attention()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['bill_attention'] = $_GET['data'];
  }

  public function set_vendor($vendor)
  {
    $this->authorized($this->module, 'document');

    $vendor = urldecode($vendor);

    $this->db->from('tb_master_vendors');
    $this->db->where('vendor', $vendor);
    $query  = $this->db->get();
    $row    = $query->unbuffered_row('array');

    $_SESSION['order']['vendor']  = $vendor;
    $_SESSION['order']['vendor_address']    = $row['address'];
    $_SESSION['order']['vendor_country']    = $row['country'];
    $_SESSION['order']['vendor_attention']  = 'Phone: ' . $row['phone'];
    // $_SESSION['order']['default_currency']  = $row['currency'];
    $_SESSION['order']['items']   = array();

    redirect($this->module['route'] . '/create');
  }

  public function set_vendor_address()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['vendor_address'] = $_GET['data'];
  }

  public function set_vendor_country()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['vendor_country'] = $_GET['data'];
  }

  public function set_vendor_phone()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['vendor_phone'] = $_GET['data'];
  }

  public function set_vendor_attention()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['order']['vendor_attention'] = $_GET['data'];
  }

  public function del_item($key)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (isset($_SESSION['order']['items']))
      unset($_SESSION['order']['items'][$key]);
  }

  public function search_poe_item()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $category = $_SESSION['order']['category'];
    $vendor   = $_SESSION['order']['vendor'];
    $entities = $this->model->searchPoeItem($category, $vendor);

    foreach ($entities as $key => $value) {
      $entities[$key]['label'] = $value['description'];
      $entities[$key]['label'] .= ' || PN: ';
      $entities[$key]['label'] .= $value['part_number'];
      $entities[$key]['label'] .= '<small>';
      $entities[$key]['label'] .= 'POE number: ' . $value['evaluation_number'] . ' || ';
      $entities[$key]['label'] .= 'POE date: ' . date('d/m/Y', strtotime($value['document_date'])) . ' || ';
      $entities[$key]['label'] .= 'Quantity: <code>' . number_format($value['quantity']) . '</code>';
      $entities[$key]['label'] .= '</small>';
    }

    echo json_encode($entities);
  }



  public function search_deliver()
  {
    $base = $this->model->loadBase();

    foreach ($base as $key) {
      $key->label = $key->warehouse;
    }
    echo json_encode($base);
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
      // if((config_item('auth_role') == 'FINANCE')){
      //     $this->sendEmail(10);
      // }
      // if((config_item('auth_role') == 'HEAD OF SCHOOL')){
      //     $this->sendEmail(3);
      // }
      // if((config_item('auth_role') == 'VP FINANCE')){
      //     $this->sendEmail(11);
      // }
      $result['status'] = 'success';
    }
    echo json_encode($result);
  }

  public function sendEmail($int)
  {
    $recipientList = $this->model->getNotifRecipient($int);
    $recipient = array();
    $person_name = array();
    foreach ($recipientList as $key) {
      array_push($recipient, $key->email);
      array_push($person_name, $key->person_name);
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
    $this->email->to('aidanurul99@rocketmail.com');

    $message = "<p>Dear " . $person_name . ",</p>";
    $message .= "<p>Ada Purchase Order yang perlu Di Tinjau,</p>";
    $message .= "<p>Klik Link Dibawah ini untuk menuju Purchase Order Terkait</p>";
    $message .= "<p>[ <a href='http://119.252.163.206/purchase_order' style='color:blue; font-weight:bold;'>Purchase_Order</a> ]</p>";
    $message .= "<p>Thanks and regards</p>";
    $this->email->subject('Notification Purchase Order');
    $this->email->message($message);

    $this->email->send();
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

      if ($entity['status'] === 'evaluation') {
        $return['info'] = $this->load->view($this->modules['purchase_order_evaluation']['view'] . '/info', $this->data, TRUE);
      } else {
        $return['info'] = $this->load->view($this->module['view'] . '/info', $this->data, TRUE);
      }
    }

    echo json_encode($return);
  }

  public function multi_reject()
  {
    $id_purchase_order = $this->input->post('id_purchase_order');
    $id_purchase_order = str_replace("|", "", $id_purchase_order);
    $id_purchase_order = substr($id_purchase_order, 0, -1);
    $id_purchase_order = explode(",", $id_purchase_order);

    $str_notes = $this->input->post('notes');
    $notes = str_replace("|", "", $str_notes);
    $notes = substr($notes, 0, -3);
    $notes = explode("##,", $notes);
    $total = 0;
    $success = 0;
    $failed = sizeof($id_purchase_order);
    $x = 0;
    foreach ($id_purchase_order as $key) {
      if ($this->model->reject($key, $notes[$x])) {
        $total++;
        $success++;
        $failed--;
      }
      $x++;
    }
    if ($success > 0) {
      $this->model->send_mail_approval($id_purchase_order, 'rejected', config_item('auth_person_name'));
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
      // if((config_item('auth_role') == 'FINANCE')){
      //     $this->sendEmail(10);
      // }
      // if((config_item('auth_role') == 'HEAD OF SCHOOL')){
      //     $this->sendEmail(3);
      // }
      // if((config_item('auth_role') == 'VP FINANCE')){
      //     $this->sendEmail(11);
      // }
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

    $html = $this->load->view($this->module['view'] . '/pdf', $this->data, true);

    $pdfFilePath = str_replace('/', '-', $entity['document_number']) . ".pdf";

    $this->load->library('m_pdf');

    $pdf = $this->m_pdf->load(null, 'A4-L');
    $pdf->WriteHTML($html);
    $pdf->Output($pdfFilePath, "I");
  }

  public function approve($id)
  {
    $this->authorized($this->module, 'document');

    $this->data['company']          = find_budget_setting('Company Name', 'head company');
    $this->data['address']          = nl2br(find_budget_setting('Address', 'head company'));
    $this->data['country']          = 'INDONESIA';
    $this->data['phone']            = find_budget_setting('Phone No', 'head company');
    $this->data['attention']        = 'Attn. Umar Satrio, Mobile. +62 081333312392';
    $this->data['entity']           = $this->model->findById($id);
    $this->data['document_number']  = order_last_number($this->data['entity']['category']);
    $this->data['tipe']             = 'CREDIT';
    $this->data['issued_by']        = config_item('auth_person_name');

    $this->render_view($this->module['view'] . '/approve');
  }

  public function payment($id)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'payment') === FALSE) {
      $return['type'] = 'denied';
      $return['info'] = "You don't have permission to access this data. You may need to login again.";
    } else {
      $entity = $this->model->findDetailById($id);

      $this->data['entity'] = $entity;

      $return['type'] = 'success';
      $return['info'] = $this->load->view($this->module['view'] . '/payment', $this->data, TRUE);
    }

    echo json_encode($return);
  }

  public function payment_save($id)
  {
    $this->authorized($this->module, 'payment');

    if (isset($_POST) && !empty($_POST) && $this->model->payment_save($id)) {
      redirect($this->module['route']);
    } else {
      die('error!');
    }
  }

  public function create($category = NULL)
  {
    $this->authorized($this->module, 'document');

    if ($category !== NULL) {
      $category   = urldecode($category);
      $company    = find_budget_setting('Company Name', 'head company');
      $address    = nl2br(find_budget_setting('Address', 'head company'));
      $country    = 'INDONESIA';
      $phone      = find_budget_setting('Phone No', 'head company');
      $attention  = 'Attn. Umar Satrio, Mobile. +62 081333312392';

      $_SESSION['order']['items']               = array();
      $_SESSION['order']['vendor']              = NULL;
      $_SESSION['order']['warehouse']           = config_item('main_warehouse');
      $_SESSION['order']['category']            = $category;
      $_SESSION['order']['document_number']     = order_last_number();
      $_SESSION['order']['document_date']       = date('Y-m-d');
      $_SESSION['order']['vendor']              = NULL;
      $_SESSION['order']['vendor_address']      = NULL;
      $_SESSION['order']['vendor_country']      = NULL;
      $_SESSION['order']['vendor_phone']        = NULL;
      $_SESSION['order']['vendor_attention']    = NULL;
      $_SESSION['order']['deliver_company']     = $company;
      $_SESSION['order']['deliver_address']     = $address;
      $_SESSION['order']['deliver_country']     = 'INDONESIA';
      $_SESSION['order']['deliver_phone']       = $phone;
      $_SESSION['order']['deliver_attention']   = $attention;
      $_SESSION['order']['bill_company']        = $company;
      $_SESSION['order']['bill_address']        = $address;
      $_SESSION['order']['bill_country']        = 'INDONESIA';
      $_SESSION['order']['bill_phone']          = $phone;
      $_SESSION['order']['bill_attention']      = $attention;
      $_SESSION['order']['reference_quotation'] = NULL;
      $_SESSION['order']['issued_by']           = config_item('auth_person_name');
      $_SESSION['order']['checked_by']          = NULL;
      $_SESSION['order']['approved_by']         = NULL;
      $_SESSION['order']['default_currency']    = 'USD';
      $_SESSION['order']['payment_type']        = 'CREDIT';
      $_SESSION['order']['exchange_rate']       = 1.00;
      $_SESSION['order']['discount']            = 0.00;
      $_SESSION['order']['taxes']               = 0.00;
      $_SESSION['order']['shipping_cost']       = 0.00;
      $_SESSION['order']['total_quantity']      = NULL;
      $_SESSION['order']['total_price']         = NULL;
      $_SESSION['order']['grand_total']         = NULL;
      $_SESSION['order']['notes']               = NULL;
      $_SESSION['order']['term_payment']               = 0;

      redirect($this->module['route'] . '/create');
    }

    if (!isset($_SESSION['order']))
      redirect($this->module['route']);

    $this->data['page']['content'] = $this->module['view'] . '/create';

    $this->render_view($this->module['view'] . '/create');
  }

  public function create_po($vendor_id)
  {
    $this->authorized($this->module, 'document');

    // if ($category !== NULL){
    $item       = $this->model->findItemPoe($vendor_id);
    $order      = $this->model->findPoe($vendor_id);
    $category   = urldecode('SPARE PART');
    $company    = find_budget_setting('Company Name', 'head company');
    $address    = nl2br(find_budget_setting('Address', 'head company'));
    $country    = 'INDONESIA';
    $phone      = find_budget_setting('Phone No', 'head company');
    $attention  = 'Attn. Umar Satrio, Mobile. +62 081333312392';

    $_SESSION['order']['items']               = $item;
    $_SESSION['order']['vendor_po']           = $vendor_id;
    $_SESSION['order']['vendor']              = $order['vendor'];
    $_SESSION['order']['warehouse']           = config_item('main_warehouse');
    $_SESSION['order']['category']            = $category;
    $_SESSION['order']['document_number']     = order_last_number();
    $_SESSION['order']['document_date']       = date('Y-m-d');
    $_SESSION['order']['vendor']              = $order['vendor'];
    $_SESSION['order']['vendor_address']      = $order['vendor_address'];
    $_SESSION['order']['vendor_country']      = $order['vendor_country'];
    $_SESSION['order']['vendor_phone']        = $order['vendor_phone'];
    $_SESSION['order']['vendor_attention']    = $order['vendor_attention'];
    $_SESSION['order']['deliver_company']     = $company;
    $_SESSION['order']['deliver_address']     = $address;
    $_SESSION['order']['deliver_country']     = 'INDONESIA';
    $_SESSION['order']['deliver_phone']       = $phone;
    $_SESSION['order']['deliver_attention']   = $attention;
    $_SESSION['order']['bill_company']        = $company;
    $_SESSION['order']['bill_address']        = $address;
    $_SESSION['order']['bill_country']        = 'INDONESIA';
    $_SESSION['order']['bill_phone']          = $phone;
    $_SESSION['order']['bill_attention']      = $attention;
    $_SESSION['order']['reference_quotation'] = NULL;
    $_SESSION['order']['issued_by']           = config_item('auth_person_name');
    $_SESSION['order']['checked_by']          = NULL;
    $_SESSION['order']['approved_by']         = NULL;
    $_SESSION['order']['default_currency']    = $order['default_currency'];
    $_SESSION['order']['payment_type']        = 'CREDIT';
    $_SESSION['order']['exchange_rate']       = 1.00;
    $_SESSION['order']['discount']            = 0.00;
    $_SESSION['order']['taxes']               = 0.00;
    $_SESSION['order']['shipping_cost']       = 0.00;
    $_SESSION['order']['total_quantity']      = NULL;
    $_SESSION['order']['total_price']         = NULL;
    $_SESSION['order']['grand_total']         = NULL;
    $_SESSION['order']['notes']               = NULL;
    $_SESSION['order']['term_payment']               = 0;

    // redirect($this->module['route'] .'/create_po/'.$vendor_id);
    // }

    // if (!isset($_SESSION['order']))
    //   redirect($this->module['route']);

    // $this->data['page']['content'] = $this->module['view'] .'/create';

    $this->render_view($this->module['view'] . '/create');
  }

  public function edit($id)
  {
    $this->authorized($this->module, 'document');

    // if ($category !== NULL){
    $item       = $this->model->findItemPo($id);
    $order      = $this->model->findPo($id);
    $category   = urldecode('SPARE PART');
    $company    = find_budget_setting('Company Name', 'head company');
    $address    = nl2br(find_budget_setting('Address', 'head company'));
    $country    = 'INDONESIA';
    $phone      = find_budget_setting('Phone No', 'head company');
    $attention  = 'Attn. Umar Satrio, Mobile. +62 081333312392';

    if (isset($_SESSION['order']) === FALSE) {
      $_SESSION['order']['items']               = $item;
      $_SESSION['order']['vendor_po']           = '';
      $_SESSION['order']['id_po']               = $id;
      $_SESSION['order']['vendor']              = $order['vendor'];
      $_SESSION['order']['warehouse']           = config_item('main_warehouse');
      $_SESSION['order']['category']            = $category;
      $_SESSION['order']['document_number']     = substr($order['document_number'], 3, 6) . 'R';
      $_SESSION['order']['document_date']       = date('Y-m-d');
      $_SESSION['order']['vendor']              = $order['vendor'];
      $_SESSION['order']['vendor_address']      = $order['vendor_address'];
      $_SESSION['order']['vendor_country']      = $order['vendor_country'];
      $_SESSION['order']['vendor_phone']        = $order['vendor_phone'];
      $_SESSION['order']['vendor_attention']    = $order['vendor_attention'];
      $_SESSION['order']['deliver_company']     = $company;
      $_SESSION['order']['deliver_address']     = $address;
      $_SESSION['order']['deliver_country']     = 'INDONESIA';
      $_SESSION['order']['deliver_phone']       = $phone;
      $_SESSION['order']['deliver_attention']   = $attention;
      $_SESSION['order']['bill_company']        = $company;
      $_SESSION['order']['bill_address']        = $address;
      $_SESSION['order']['bill_country']        = 'INDONESIA';
      $_SESSION['order']['bill_phone']          = $phone;
      $_SESSION['order']['bill_attention']      = $attention;
      $_SESSION['order']['reference_quotation'] = NULL;
      $_SESSION['order']['issued_by']           = config_item('auth_person_name');
      $_SESSION['order']['checked_by']          = NULL;
      $_SESSION['order']['approved_by']         = NULL;
      $_SESSION['order']['default_currency']    = $order['default_currency'];
      $_SESSION['order']['payment_type']        = $order['tipe'];
      $_SESSION['order']['exchange_rate']       = 1.00;
      $_SESSION['order']['discount']            = $order['discount'];
      $_SESSION['order']['taxes']               = $order['taxes'];
      $_SESSION['order']['shipping_cost']       = $order['shipping_cost'];
      $_SESSION['order']['total_quantity']      = NULL;
      $_SESSION['order']['total_price']         = NULL;
      $_SESSION['order']['grand_total']         = NULL;
      $_SESSION['order']['notes']               = NULL;
    }

    $this->render_view($this->module['view'] . '/edit');
  }

  public function save($id)
  {
    // if ($this->input->is_ajax_request() == FALSE)
    //   redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') == FALSE) {
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      $document_number = order_format_number($_POST['category']) . $_POST['document_number'];

      $errors = array();

      if (isset($_POST) === FALSE || empty($_POST)) {
        $errors[] = 'No data posted!';
      }

      if ($this->model->isDocumentNumberExists($document_number)) {
        $errors[] = 'Duplicate Document Number: ' . $_POST['document_number'] . ' !';
      }

      if (!empty($errors)) {
        $data['success'] = FALSE;
        $data['message'] = implode('<br />', $errors);
      } else {

        if ($this->model->save($id)) {
          $data['success'] = TRUE;
          $data['message'] = 'Document ' . $document_number . ' has been saved. You will redirected now.';
          // $this->sendEmail(2);
        } else {
          $data['success'] = FALSE;
          $data['message'] = 'Error while saving this document. Please ask Technical Support.';
        }
      }
    }

    echo json_encode($data);
  }

  public function save_po()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') == FALSE) {
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      $document_number = order_format_number($_SESSION['orders']['category']) . $_SESSION['order']['document_number'];

      $errors = array();

      if (!isset($_SESSION['order']['items']) || empty($_SESSION['order']['items'])) {
        $errors[] = 'Please add at least 1 item!';
      }

      if ($this->model->isDocumentNumberExists($document_number)) {
        $errors[] = 'Duplicate Document Number: ' . $_SESSION['orders']['document_number'] . ' !';
      }

      if (!empty($errors)) {
        $data['success'] = FALSE;
        $data['message'] = implode('<br />', $errors);
      } else {
        if ($this->model->save_po()) {
          $data['success'] = TRUE;
          $data['message'] = 'Document ' . $document_number . ' has been saved. You will redirected now.';
          // $this->sendEmail(2);
        } else {
          $data['success'] = FALSE;
          $data['message'] = 'Error while saving this document. Please ask Technical Support.';
        }
      }
    }

    echo json_encode($data);
  }

  public function save_revisi_po()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') == FALSE) {
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      $document_number = order_format_number($_SESSION['orders']['category']) . $_SESSION['order']['document_number'];

      $errors = array();

      if (!isset($_SESSION['order']['items']) || empty($_SESSION['order']['items'])) {
        $errors[] = 'Please add at least 1 item!';
      }

      if ($this->model->isDocumentNumberExists($document_number)) {
        $errors[] = 'Duplicate Document Number: ' . $_SESSION['orders']['document_number'] . ' !';
      }

      if (!empty($errors)) {
        $data['success'] = FALSE;
        $data['message'] = implode('<br />', $errors);
      } else {
        if ($this->model->save_revisi_po()) {
          $data['success'] = TRUE;
          $data['message'] = 'Document ' . $document_number . ' has been saved. You will redirected now.';
          // $this->sendEmail(2);
        } else {
          $data['success'] = FALSE;
          $data['message'] = 'Error while saving this document. Please ask Technical Support.';
        }
      }
    }

    echo json_encode($data);
  }

  public function import()
  {


    $this->load->library('form_validation');

    if (isset($_POST) && !empty($_POST)) {
      $this->form_validation->set_rules('delimiter', 'Value Delimiter', 'trim|required');

      if ($this->form_validation->run() === TRUE) {
        $file       = $_FILES['userfile']['tmp_name'];
        $delimiter  = $this->input->post('delimiter');

        //... open file
        if (($handle = fopen($file, "r")) !== FALSE) {
          $row     = 1;
          $data    = array();
          $errors  = array();
          $user_id = array();
          $index   = 0;
          fgetcsv($handle); // skip first line (as header)

          //... parsing line
          while (($col = fgetcsv($handle, 1024, $delimiter)) !== FALSE) {
            $row++;
            // po number
            $document_no = trim(strtoupper($col[1]));
            $data[$row]['document_no'] = $document_no;
            if ($document_no == '')
              $errors[] = 'Line ' . $row . ': document no is null!';

            // po status
            // $po_status = trim(strtoupper($col[2]));
            // $data[$row]['po_status'] = 'APPROVED';

            //po date 
            $date = trim(strtoupper($col[2]));
            $data[$row]['date'] = $date;

            // kategori
            $kategori = trim(strtoupper($col[3]));
            $data[$row]['kategori'] = $kategori;
            if ($kategori == '')
              $errors[] = 'Line ' . $row . ': kategori is null!';

            // description
            $description = trim(strtoupper($col[4]));
            $data[$row]['description'] = $description;
            if ($description == '')
              $errors[] = 'Line ' . $row . ': description is null!';

            // part_number
            $part_number = trim(strtoupper($col[5]));
            $data[$row]['part_number'] = $part_number;
            if ($part_number == '')
              $errors[] = 'Line ' . $row . ': part_number is null!';

            // alt_part
            $alt_part = trim(strtoupper($col[6]));
            $data[$row]['alt_part'] = $alt_part;

            // poe_number
            $poe_number = trim(strtoupper($col[7]));
            $data[$row]['poe_number'] = $poe_number;
            if ($poe_number == '')
              $errors[] = 'Line ' . $row . ': evaluation number is null!';

            // pr_number
            $pr_number = trim(strtoupper($col[8]));
            $data[$row]['pr_number'] = $pr_number;
            if ($pr_number == '')
              $errors[] = 'Line ' . $row . ': request number is null!';

            // ref_quot
            $ref_quot = trim(strtoupper($col[9]));
            $data[$row]['ref_quot'] = $ref_quot;

            // vendor
            $vendor = trim(strtoupper($col[10]));
            $data[$row]['vendor'] = $vendor;
            if ($vendor == '')
              $errors[] = 'Line ' . $row . ': vendor is null!';

            // order_qty
            $order_qty = trim(strtoupper($col[11]));
            $data[$row]['order_qty'] = $order_qty;

            // currency
            $currency = trim(strtoupper($col[12]));
            $data[$row]['currency'] = $currency;
            if ($currency == '')
              $errors[] = 'Line ' . $row . ': currency is null!';

            // request_qty
            // $request_qty = trim(strtoupper($col[12]));
            // $data[$row]['request_qty'] = $request_qty;

            // receive_qty
            // $kategori = trim(strtoupper($col[13]));
            // $data[$row]['receive_qty'] = $receive_qty;

            // unit_price
            $unit_price = trim(strtoupper($col[13]));
            $data[$row]['unit_price'] = $unit_price;

            // core_charge
            $core_charge = trim(strtoupper($col[14]));
            $data[$row]['core_charge'] = $core_charge;

            // total_amount
            $total_amount = trim(strtoupper($col[15]));
            $data[$row]['total_amount'] = $total_amount;

            // paid_amount
            // $paid_amount = trim(strtoupper($col[17]));
            // $data[$row]['paid_amount'] = $paid_amount;

            // notes
            $notes = trim(strtoupper($col[16]));
            $data[$row]['notes'] = $notes;

            // warehouse
            // $warehouse = trim(strtoupper($col[19]));
            // $data[$row]['warehouse'] = $warehouse;
            // if ($warehouse == '')
            // $errors[] = 'Line '. $row .': warehouse is null!';

            // currency
            $unit = trim(strtoupper($col[17]));
            $data[$row]['unit'] = $unit;
            if ($unit == '')
              $errors[] = 'Line ' . $row . ': unit is null!';
          }
          fclose($handle);
          if (empty($errors)) {
            /**
             * Insert into user table
             */
            if ($this->model->import($data)) {
              //... send message to view
              $this->session->set_flashdata('alert', array(
                'type' => 'success',
                'info' => count($data) . " data has been imported!"
              ));

              redirect($this->module['route']);
            }
          } else {
            foreach ($errors as $key => $value) {
              $err[] = "\n#" . $value;
            }

            $this->session->set_flashdata('alert', array(
              'type' => 'danger',
              'info' => "There are errors on data\n#" . implode("\n#", $errors)
            ));

            redirect($this->module['route']);
          }
        } else {
          $this->session->set_flashdata('alert', array(
            'type' => 'danger',
            'info' => 'Cannot open file!'
          ));
        }
      }
    }
  }
  public function add_item()
  {
    $this->authorized($this->module, 'document');

    if (isset($_POST) && !empty($_POST)) {
      $quantity = floatval($this->input->post('quantity'));

      $_SESSION['order']['items'][] = array(
        'part_number'           => trim(strtoupper($this->input->post('part_number'))),
        'alternate_part_number' => trim(strtoupper($this->input->post('alternate_part_number'))),
        'description'           => trim(strtoupper($this->input->post('description'))),
        'remarks'               => trim($this->input->post('remarks')),
        'quantity'              => $quantity,
        'unit_price'            => $this->input->post('unit_price'),
        'core_charge'           => $this->input->post('core_charge'),
        'total_amount'          => $this->input->post('total_amount'),
        'unit'                  => trim($this->input->post('unit')),
        'evaluation_number'     => trim($this->input->post('evaluation_number')),
        'purchase_order_evaluation_items_vendors_id' => $this->input->post('purchase_order_evaluation_items_vendors_id'),
      );
    }

    redirect($this->module['route'] . '/create');
  }

  public function edit_item($key)
  {
    $this->authorized($this->module, 'document');

    $this->data['key']    = $key;
    $this->data['entity'] = $_SESSION['order']['items'][$key];

    $this->render_view($this->module['view'] . '/edit_item');
  }

  public function update_item($key)
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') == FALSE) {
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      $quantity = floatval($this->input->post('quantity'));

      $_SESSION['order']['items'][$key]['quantity'] = $quantity;
      $_SESSION['order']['items'][$key]['alternate_part_number'] = trim(strtoupper($this->input->post('alternate_part_number')));

      foreach ($_POST['vendor'] as $v => $vendor) {
        $unit_price   = $vendor['unit_price'];
        $core_charge  = $vendor['core_charge'];
        $total_price  = ($unit_price * $quantity) + ($core_charge * $quantity);

        $_SESSION['order']['items'][$key]['vendors'][$v]['unit_price'] = $unit_price;
        $_SESSION['order']['items'][$key]['vendors'][$v]['quantity'] = $quantity;
        $_SESSION['order']['items'][$key]['vendors'][$v]['core_charge'] = $core_charge;
        $_SESSION['order']['items'][$key]['vendors'][$v]['total'] = $total_price;
      }

      $data['success'] = TRUE;
    }

    echo json_encode($data);
  }

  public function set_selected_vendor($item_key, $vendor_key)
  {
    $this->authorized($this->module, 'document');

    foreach ($_SESSION['order']['items'][$item_key]['vendors'] as $v => $vendor) {
      $_SESSION['order']['items'][$item_key]['vendors'][$v]['selected'] = 'f';
    }

    $_SESSION['order']['items'][$item_key]['vendors'][$vendor_key]['selected'] = 't';

    redirect($this->module['route'] . '/create');
  }

  public function discard()
  {
    $this->authorized($this->module['permission']['document']);

    unset($_SESSION['order']);

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

  public function listAttachmentpoe($id)
  {
    $data = $this->model->listAttachmentpoe($id);
    echo json_encode($data);
  }

  public function ajax_editItem($key)
  {
    $this->authorized($this->module, 'document');

    $entity = $_SESSION['order']['items'][$key];

    echo json_encode($entity);
  }

  public function edit_item_order()
  {
    $this->authorized($this->module, 'document');

    $key = $this->input->post('item_id');
    if (isset($_POST) && !empty($_POST)) {
      $quantity = floatval($this->input->post('quantity'));

      $_SESSION['order']['items'][$key] = array(
        'part_number'           => trim(strtoupper($this->input->post('part_number'))),
        'alternate_part_number' => trim(strtoupper($this->input->post('alternate_part_number'))),
        'description'           => trim(strtoupper($this->input->post('description'))),
        'remarks'               => trim($this->input->post('remarks')),
        'quantity'              => $quantity,
        'unit_price'            => $this->input->post('unit_price'),
        'core_charge'           => $this->input->post('core_charge'),
        'total_amount'          => $this->input->post('total_amount'),
        'unit'                  => trim($this->input->post('unit')),
        'evaluation_number'     => trim($this->input->post('evaluation_number')),
        'purchase_order_evaluation_items_vendors_id' => $this->input->post('purchase_order_evaluation_items_vendors_id'),

      );
    }
    redirect($this->module['route'] . '/edit/' . $_SESSION['order']['id_po']);
  }

  public function index_report()
  {
    $this->authorized($this->module, 'index');
    if (isset($_POST['vendor']) && $_POST['vendor'] !== NULL) {
      $vendor = $_POST['vendor'];
    } else {
      $vendor = 'all';
    }

    $this->data['selected_vendor'] = $vendor;

    $this->data['page']['title']            = $this->module['label'];
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumnsReport());
    $this->data['grid']['data_source']      = site_url($this->module['route'] . '/index_data_source_report');
    $this->data['grid']['fixed_columns']    = 3;
    $this->data['grid']['summary_columns']  = array(12, 15);
    $this->data['grid']['order_columns']    = array();
    $this->render_view($this->module['view'] . '/index_report');
  }

  public function index_data_source_report()
  {
    // if ($this->input->is_ajax_request() === FALSE)
    //   redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'index') === FALSE) {
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {
      $entities     = $this->model->getIndexReport();
      $data         = array();
      $no           = $_POST['start'];
      $quantity     = array();
      $total_amount = array();
      $amount_paid  = array();

      foreach ($entities as $row) {
        $no++;
        $col = array();
        $col[] = print_number($no);
        $col[] = print_string($row['document_number'], 'N/A');
        $col[] = print_string($row['review_status']);
        $col[] = print_date($row['document_date']);
        $col[] = print_string($row['category']);
        $col[] = print_string($row['description']);
        $col[] = print_string($row['part_number']);
        $col[] = print_string($row['alternate_part_number']);
        $col[] = '<a href="' . site_url($this->modules['purchase_order_evaluation']['route'] . '/print_pdf/' . $row['poe_id']) . '" target="_blank" >' . print_string($row['poe_number']) . '</a><a href="#" class="btn btn-icon-toggle btn-info btn-sm "><i class="fa fa-eye" data-id="' . $row['poe_id'] . '"></i></a>';
        $col[] = '<a href="' . site_url($this->modules['purchase_request']['route'] . '/print_pdf_prl/' . $row['poe_item_id']) . '" target="_blank" >' . print_string($row['purchase_request_number']) . '</a>';
        $col[] = print_string($row['reference_quotation']);
        $col[] = strtoupper($row['vendor']);
        $col[] = print_number($row['quantity'], 2);
        $col[] = print_number($row['unit_price'], 2);
        $col[] = print_number($row['core_charge'], 2);
        $col[] = print_number($row['total_amount'], 2);
        $col[] = print_number($row['quantity_received'], 2);
        $col[] = print_number($row['left_received_quantity'], 2);
        // $col[] = print_number($row['amount_paid'], 2);   
        $col[] = print_string($row['notes']);
        $col[] = null;
        $col[] = null;
        $col[] = null;

        $col['DT_RowId'] = 'row_' . $row['id'];
        $col['DT_RowData']['pkey']  = $row['id'];

        if ($this->has_role($this->module, 'info')) {
          $col['DT_RowAttr']['onClick']     = '';
          $col['DT_RowAttr']['data-id']     = $row['id'];
          $col['DT_RowAttr']['data-target'] = '#data-modal';
          $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] . '/info/' . $row['id']);
        }

        if ($this->has_role($this->module, 'payment') && $row['status'] == 'ORDER') {
          $col['DT_RowAttr']['onClick']     = '$(this).popup();';
          $col['DT_RowAttr']['data-target'] = '#data-modal';
          $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] . '/payment/' . $row['id']);
        }

        $quantity[]     = $row['quantity'];
        $total_amount[] = $row['total_amount'];
        $amount_paid[]  = $row['amount_paid'];
        $data[]         = $col;
      }

      $result = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->model->countIndexReport(),
        "recordsFiltered" => $this->model->countIndexFilteredReport(),
        "data" => $data,
        "total" => array(
          12 => print_number(array_sum($quantity), 2),
          15 => print_number(array_sum($total_amount), 2),
          // 19 => print_number(array_sum($amount_paid),2),
        )
      );
    }

    echo json_encode($result);
  }

  public function order()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'order') === FALSE) {
      $alert['type']  = 'danger';
      $alert['info']  = 'You are not allowed to delete this data!';
    } else {
      $entity = $this->model->findById($this->input->post('id'));

      if ($this->model->order()) {
        $alert['type'] = 'success';
        $alert['info'] = 'Purchase Order '. $entity['document_number'].' has been ordered.';
        $alert['link'] = site_url($this->module['route']);
      } else {
        $alert['type'] = 'danger';
        $alert['info'] = 'There are error while processing data. Please try again later.';
      }
    }

    echo json_encode($alert);
  }
}
