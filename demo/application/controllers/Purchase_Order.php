<?php defined('BASEPATH') OR exit('No direct script access allowed');

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

  public function set_doc_number()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (empty($_GET['data']))
      $number = order_last_number();
    else
      $number = $_GET['data'];

    $_SESSION['order']['document_number'] = $number;
  }

  public function set_document_date()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['order']['document_date'] = $_GET['data'];
  }

  public function set_issued_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['order']['issued_by'] = $_GET['data'];
  }

  public function set_default_currency()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['order']['default_currency'] = $_GET['data'];
  }

  public function set_exchange_rate()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['order']['exchange_rate'] = $_GET['data'];
  }

  public function set_discount()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['order']['discount'] = $_GET['data'];
  }

  public function set_taxes()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['order']['taxes'] = $_GET['data'];
  }

  public function set_shipping_cost()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['order']['shipping_cost'] = $_GET['data'];
  }

  public function set_checked_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['order']['checked_by'] = $_GET['data'];
  }

  public function set_approved_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['order']['approved_by'] = $_GET['data'];
  }

  public function set_notes()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['order']['notes'] = $_GET['data'];
  }

  public function set_deliver_company()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['order']['deliver_company'] = $_GET['data'];
  }

  public function set_deliver_address()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['order']['deliver_address'] = $_GET['data'];
  }

  public function set_deliver_country()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['order']['deliver_country'] = $_GET['data'];
  }

  public function set_deliver_phone()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['order']['deliver_phone'] = $_GET['data'];
  }

  public function set_deliver_attention()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['order']['deliver_attention'] = $_GET['data'];
  }

  public function set_bill_company()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['order']['bill_company'] = $_GET['data'];
  }

  public function set_bill_address()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['order']['bill_address'] = $_GET['data'];
  }

  public function set_bill_country()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['order']['bill_country'] = $_GET['data'];
  }

  public function set_bill_phone()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['order']['bill_phone'] = $_GET['data'];
  }

  public function set_bill_attention()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

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
    $_SESSION['order']['vendor_attention']  = 'Phone: '. $row['phone'];
    $_SESSION['order']['items']   = array();

    redirect($this->module['route'] .'/create');
  }

  public function set_vendor_address()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['order']['vendor_address'] = $_GET['data'];
  }

  public function set_vendor_country()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['order']['vendor_country'] = $_GET['data'];
  }

  public function set_vendor_phone()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['order']['vendor_phone'] = $_GET['data'];
  }

  public function set_vendor_attention()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['order']['vendor_attention'] = $_GET['data'];
  }

  public function del_item($key)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (isset($_SESSION['order']['items']))
      unset($_SESSION['order']['items'][$key]);
  }

  public function search_poe_item()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $category = $_SESSION['order']['category'];
    $vendor   = $_SESSION['order']['vendor'];
    $entities = $this->model->searchPoeItem($category, $vendor);

    foreach ($entities as $key => $value){
      $entities[$key]['label'] = $value['description'];
      $entities[$key]['label'] .= ' || PN: ';
      $entities[$key]['label'] .= $value['part_number'];
      $entities[$key]['label'] .= '<small>';
      $entities[$key]['label'] .= 'POE number: '. $value['document_number'] .' || ';
      $entities[$key]['label'] .= 'POE date: '. date('d/m/Y', strtotime($value['document_date'])) .' || ';
      $entities[$key]['label'] .= 'Quantity: <code>'. number_format($value['quantity']) .'</code>';
      $entities[$key]['label'] .= '</small>';
    }

    echo json_encode($entities);
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
      $quantity = array();

      foreach ($entities as $row){
        $no++;
        $col = array();
        $col[] = print_number($no);
        $col[] = print_string($row['document_number']);
        $col[] = print_date($row['document_date']);
        $col[] = print_string($row['category']);
        $col[] = print_string($row['description']);
        $col[] = print_string($row['part_number']);
        $col[] = print_string($row['alternate_part_number']);
        $col[] = print_number($row['quantity'], 2);
        $col[] = print_number($row['quantity_requested'], 2);
        $col[] = print_number($row['quantity_received'], 2);
        $col[] = print_number($row['unit_price'], 2);
        $col[] = print_number($row['core_charge'], 2);
        $col[] = print_number($row['total_amount'], 2);
        $col[] = print_number($row['amount_paid'], 2);
        $col[] = print_string($row['remarks']);
        $col[] = print_string($row['reference_quotation']);
        $col[] = print_string($row['purchase_request_number']);
        $col[] = strtoupper($row['vendor']);
        $col[] = $row['notes'];
        $col['DT_RowId'] = 'row_'. $row['id'];
        $col['DT_RowData']['pkey']  = $row['id'];

        if ($this->has_role($this->module, 'info')){
          $col['DT_RowAttr']['onClick']     = '$(this).popup();';
          $col['DT_RowAttr']['data-target'] = '#data-modal';
          $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] .'/info/'. $row['id']);
        }

        if ($this->has_role($this->module, 'payment')){
          $col['DT_RowAttr']['onClick']     = '$(this).popup();';
          $col['DT_RowAttr']['data-target'] = '#data-modal';
          $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] .'/payment/'. $row['id']);
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

  public function index()
  {
    $this->authorized($this->module, 'index');

    $this->data['page']['title']            = $this->module['label'];
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source');
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = NULL;
    $this->data['grid']['order_columns']    = array();

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

      if ($entity['status'] === 'evaluation'){
        $return['info'] = $this->load->view($this->modules['purchase_order_evaluation']['view'] .'/info', $this->data, TRUE);
      } else {
        $return['info'] = $this->load->view($this->module['view'] .'/info', $this->data, TRUE);
      }
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

    $html = $this->load->view($this->module['view'] .'/pdf', $this->data, true);

    $pdfFilePath = str_replace('/', '-', $entity['document_number']) .".pdf";

    $this->load->library('m_pdf');

    $pdf = $this->m_pdf->load(null, 'A4');
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

    $this->render_view($this->module['view'] .'/approve');
  }

  public function payment($id)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'payment') === FALSE){
      $return['type'] = 'denied';
      $return['info'] = "You don't have permission to access this data. You may need to login again.";
    } else {
      $entity = $this->model->findDetailById($id);

      $this->data['entity'] = $entity;

      $return['type'] = 'success';
      $return['info'] = $this->load->view($this->module['view'] .'/payment', $this->data, TRUE);
    }

    echo json_encode($return);
  }

  public function payment_save($id)
  {
    $this->authorized($this->module, 'payment');

    if (isset($_POST) && !empty($_POST) && $this->model->payment_save($id)){
      redirect($this->module['route']);
    } else {
      die('error!');
    }
  }

  public function create($category = NULL)
  {
    $this->authorized($this->module, 'document');

    if ($category !== NULL){
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
      $_SESSION['order']['exchange_rate']       = 1.00;
      $_SESSION['order']['discount']            = 0.00;
      $_SESSION['order']['taxes']               = 0.00;
      $_SESSION['order']['shipping_cost']       = 0.00;
      $_SESSION['order']['total_quantity']      = NULL;
      $_SESSION['order']['total_price']         = NULL;
      $_SESSION['order']['grand_total']         = NULL;
      $_SESSION['order']['notes']               = NULL;

      redirect($this->module['route'] .'/create');
    }

    if (!isset($_SESSION['order']))
      redirect($this->module['route']);

    $this->data['page']['content'] = $this->module['view'] .'/create';

    $this->render_view($this->module['view'] .'/create');
  }

  public function save($id)
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') == FALSE){
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      $document_number = $_POST['document_number'] . order_format_number($_POST['category']);

      $errors = array();

      if (isset($_POST) === FALSE || empty($_POST)){
        $errors[] = 'No data posted!';
      }

      if ($this->model->isDocumentNumberExists($document_number)){
        $errors[] = 'Duplicate Document Number: '. $_POST['document_number'] .' !';
      }

      if (!empty($errors)){
        $data['success'] = FALSE;
        $data['message'] = implode('<br />', $errors);
      } else {
        if ($this->model->save($id)){
          $data['success'] = TRUE;
          $data['message'] = 'Document '. $document_number .' has been saved. You will redirected now.';
        } else {
          $data['success'] = FALSE;
          $data['message'] = 'Error while saving this document. Please ask Technical Support.';
        }
      }
    }

    echo json_encode($data);
  }

  public function add_item()
  {
    $this->authorized($this->module, 'document');

    if (isset($_POST) && !empty($_POST)){
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
        'purchase_order_evaluation_items_vendors_id' => $this->input->post('purchase_order_evaluation_items_vendors_id'),
      );
    }

    redirect($this->module['route'] .'/create');
  }

  public function edit_item($key)
  {
    $this->authorized($this->module, 'document');

    $this->data['key']    = $key;
    $this->data['entity'] = $_SESSION['order']['items'][$key];

    $this->render_view($this->module['view'] .'/edit_item');
  }

  public function update_item($key)
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') == FALSE){
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

    foreach ($_SESSION['order']['items'][$item_key]['vendors'] as $v => $vendor){
      $_SESSION['order']['items'][$item_key]['vendors'][$v]['selected'] = 'f';
    }

    $_SESSION['order']['items'][$item_key]['vendors'][$vendor_key]['selected'] = 't';

    redirect($this->module['route'] .'/create');
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
}
