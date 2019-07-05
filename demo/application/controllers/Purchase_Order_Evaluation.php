<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_Order_Evaluation extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['purchase_order_evaluation'];
    $this->load->helper($this->module['helper']);
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function set_doc_number()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (empty($_GET['data']))
      $number = poe_last_number();
    else
      $number = $_GET['data'];

    $_SESSION['poe']['document_number'] = $number;
  }

  public function set_document_date()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['poe']['document_date'] = $_GET['data'];
  }

  public function set_created_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['poe']['created_by'] = $_GET['data'];
  }

  public function set_document_reference()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['poe']['document_reference'] = $_GET['data'];
  }

  public function set_status()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['poe']['status'] = $_GET['data'];
  }

  public function set_approved_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['poe']['approved_by'] = $_GET['data'];
  }

  public function set_default_currency()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['poe']['default_currency'] = $_GET['data'];
  }

  public function set_exchange_rate()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['poe']['exchange_rate'] = $_GET['data'];
  }

  public function set_notes()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['poe']['notes'] = $_GET['data'];
  }

  public function search_request_item()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $category = $_SESSION['poe']['category'];
    $entities = $this->model->searchRequestItem($category);

    foreach ($entities as $key => $value){
      $entities[$key]['label'] = $value['product_name'];
      $entities[$key]['label'] .= ' || PN: ';
      $entities[$key]['label'] .= $value['part_number'];
      $entities[$key]['label'] .= '<small>';
      $entities[$key]['label'] .= 'PR number: '. $value['pr_number'] .' || ';
      $entities[$key]['label'] .= 'PR date: '. date('d/m/Y', strtotime($value['pr_date'])) .' || ';
      $entities[$key]['label'] .= 'Required date: '. date('d/m/Y', strtotime($value['required_date'])) .' || ';
      $entities[$key]['label'] .= 'Quantity: <code>'. number_format($value['quantity']) .'</code>';
      $entities[$key]['label'] .= '</small>';
    }

    echo json_encode($entities);
  }

  public function search_items_by_part_number()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $category = $_SESSION['poe']['category'];
    $entities = $this->model->searchItemsByPartNumber($category);

    foreach ($entities as $key => $value){
      $entities[$key]['label'] = $value['part_number'];
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
        $col[] = print_string($row['vendor']);
        $col[] = strtoupper($row['status']);
        $col[] = print_string($row['document_reference']);
        $col[] = print_string($row['created_by']);
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
    $this->data['grid']['order_columns']    = array(
      0   => array( 0 => 1,  1 => 'desc' ),
      1   => array( 0 => 2,  1 => 'desc' ),
      2   => array( 0 => 3,  1 => 'asc' ),
      3   => array( 0 => 4,  1 => 'asc' ),
      4   => array( 0 => 5,  1 => 'asc' ),
      5   => array( 0 => 6,  1 => 'asc' ),
      6   => array( 0 => 7,  1 => 'asc' ),
      7   => array( 0 => 8,  1 => 'asc' ),
      8   => array( 0 => 9,  1 => 'asc' ),
      9   => array( 0 => 10,  1 => 'asc' ),
      10   => array( 0 => 11,  1 => 'asc' ),
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

  public function print_pdf($id)
  {
    $this->authorized($this->module, 'print');

    $entity = $this->model->findById($id);

    $this->data['entity']           = $entity;
    $this->data['page']['title']    = strtoupper($this->module['label']);
    $this->data['page']['content']  = $this->module['view'] .'/print_pdf';

    $html = $this->load->view($this->pdf_theme, $this->data, true);

    $pdfFilePath = str_replace('/', '-', $entity['evaluation_number']) .".pdf";

    $this->load->library('m_pdf');

    $pdf = $this->m_pdf->load(null, 'A4-L');
    $pdf->WriteHTML($html);
    $pdf->Output($pdfFilePath, "I");
  }

  public function edit($id)
  {
    $this->authorized($this->module, 'document');

    $entity = $this->model->findById($id);

    $document_number  = sprintf('%06s', substr($entity['document_number'], 0, 6));

    if (isset($_SESSION['poe']) === FALSE){
      $_SESSION['poe']                     = $entity;
      $_SESSION['poe']['id']               = $id;
      $_SESSION['poe']['edit']             = $entity['document_number'];
      $_SESSION['poe']['document_number']  = $document_number;
    }

    redirect($this->module['route'] .'/create');
  }

  public function create($category = NULL)
  {
    $this->authorized($this->module, 'document');

    if ($category !== NULL){
      $category = urldecode($category);

      $_SESSION['poe']['request']             = array();
      $_SESSION['poe']['vendors']             = array();
      $_SESSION['poe']['warehouse']           = config_item('main_warehouse');
      $_SESSION['poe']['category']            = $category;
      $_SESSION['poe']['document_number']     = poe_last_number();
      $_SESSION['poe']['document_date']       = date('Y-m-d');
      $_SESSION['poe']['created_by']          = config_item('auth_person_name');
      $_SESSION['poe']['document_reference']  = NULL;
      $_SESSION['poe']['exchange_rate']       = 1.00;
      $_SESSION['poe']['default_currency']    = 'USD';
      $_SESSION['poe']['status']              = 'pending';
      $_SESSION['poe']['approved_by']         = NULL;
      $_SESSION['poe']['total_quantity']      = NULL;
      $_SESSION['poe']['total_price']         = NULL;
      $_SESSION['poe']['grand_total']         = NULL;
      $_SESSION['poe']['notes']               = NULL;

      redirect($this->module['route'] .'/create');
    }

    if (!isset($_SESSION['poe']))
      redirect($this->module['route']);

    $this->data['page']['content']    = $this->module['view'] .'/create';

    $this->render_view($this->module['view'] .'/create');
  }

  public function save()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') == FALSE){
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      if (!isset($_SESSION['poe']['request']) || empty($_SESSION['poe']['request']) || !isset($_SESSION['poe']['vendors']) || empty($_SESSION['poe']['vendors'])){
        $data['success'] = FALSE;
        $data['message'] = 'Please add at least 1 request or vendor!';
      } else {
        $errors = array();
        $has_selected = FALSE;

        foreach ($_SESSION['poe']['vendors'] as $key => $vendor) {
          if ($vendor['is_selected'] == 't'){
            $has_selected = TRUE;
          }
        }

        if ($has_selected == FALSE){
          $errors[] = 'No vendor qualified! Please approve 1 vendor.';
        }

        $document_number = $_SESSION['poe']['document_number'] . poe_format_number();

        if (isset($_SESSION['poe']['edit'])){
          if ($_SESSION['poe']['edit'] != $document_number && $this->model->isDocumentNumberExists($document_number)){
            $errors[] = 'Duplicate Document Number: '. $_SESSION['poe']['document_number'] .' !';
          }
        } else {
          if ($this->model->isDocumentNumberExists($document_number)){
            $errors[] = 'Duplicate Document Number: '. $_SESSION['poe']['document_number'] .' !';
          }
        }

        if (!empty($errors)){
          $data['success'] = FALSE;
          $data['message'] = implode('<br />', $errors);
        } else {
          if ($this->model->save()){
            unset($_SESSION['poe']);

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

  public function add_request()
  {
    $this->authorized($this->module, 'document');

    $this->data['entities'] = $this->model->listRequest($_SESSION['poe']['category']);

    $this->render_view($this->module['view'] .'/add_request');
  }

  public function add_selected_request()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') == FALSE){
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      if (isset($_POST['request_id']) && !empty($_POST['request_id'])){
        $_SESSION['poe']['request'] = array();

        foreach ($_POST['request_id'] as $key => $request_id) {
          $request = $this->model->infoRequest($request_id);

          $_SESSION['poe']['request'][$request_id] = array(
            'description'             => $request['product_name'],
            'part_number'             => $request['product_code'],
            'alternate_part_number'   => NULL,
            'serial_number'           => NULL,
            'unit'                    => $request['unit'],
            'quantity_requested'      => floatval($request['quantity']),
            'unit_price_requested'    => floatval($request['price']),
            'total_amount_requested'  => floatval($request['quantity']) * floatval($request['price']),
            'unit'                    => trim($this->input->post('unit')),
            'remarks'                 => $request['additional_info'],
            'purchase_request_number' => $request['pr_number'],
          );

          $_SESSION['poe']['request'][$request_id]['description'] = $request['product_name'];
          $_SESSION['poe']['request'][$request_id]['part_number'] = $request['product_code'];
          $_SESSION['poe']['request'][$request_id]['alternate_part_number'] = NULL;
          $_SESSION['poe']['request'][$request_id]['serial_number'] = NULL;
          $_SESSION['poe']['request'][$request_id]['unit'] = NULL;
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

  public function delete_request($key)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (isset($_SESSION['poe']['request']))
      unset($_SESSION['poe']['request'][$key]);
  }

  public function add_vendor()
  {
    $this->authorized($this->module, 'document');

    $this->render_view($this->module['view'] .'/add_vendor');
  }

  public function add_selected_vendor()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') == FALSE){
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      if (isset($_POST['vendor']) && !empty($_POST['vendor'])){
        $_SESSION['poe']['vendors'] = array();

        foreach ($_POST['vendor'] as $key => $vendor) {
          $_SESSION['poe']['vendors'][$key]['vendor'] = $vendor;
          $_SESSION['poe']['vendors'][$key]['is_selected'] = 'f';
        }

        foreach ($_SESSION['poe']['request'] as $id => $request) {
          foreach ($_POST['vendor'] as $key => $vendor) {
            $_SESSION['poe']['request'][$id]['vendors'][$key] = array(
              'vendor'                  => $vendor,
              'alternate_part_number'   => $request['alternate_part_number'],
              'quantity'                => $request['quantity_requested'],
              'left_received_quantity'  => $request['quantity_requested'],
              'left_paid_quantity'      => $request['quantity_requested'],
              'unit_price'              => $request['unit_price_requested'],
              'purchase_request_number' => $request['purchase_request_number'],
              'core_charge'             => floatval(0),
              'total'                   => $request['quantity_requested'] * $request['quantity_requested'],
              'left_paid_amount'        => $request['quantity_requested'] * $request['quantity_requested'],
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

  public function set_selected_vendor($key)
  {
    $this->authorized($this->module, 'document');

    foreach ($_SESSION['poe']['vendors'] as $v => $info){
      $_SESSION['poe']['vendors'][$v]['is_selected'] = 'f';
    }

    $_SESSION['poe']['vendors'][$key]['is_selected'] = 't';

    redirect($this->module['route'] .'/create');
  }

  public function edit_request()
  {
    $this->authorized($this->module, 'document');

    $this->render_view($this->module['view'] .'/edit_request');
  }

  public function update_request()
  {
    // if ($this->input->is_ajax_request() == FALSE)
    //   redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') == FALSE){
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      if (isset($_POST['request']) && !empty($_POST['request'])){
        // echo "<pre>";
        // var_dump($_POST['request']);
        // echo "</pre>";
        foreach ($_POST['request'] as $id => $request) {
          $quantity = floatval($_SESSION['poe']['request'][$id]['quantity_requested']);

          foreach ($request['vendors'] as $key => $vendor) {
            $_SESSION['poe']['request'][$id]['vendors'][$key]['alternate_part_number'] = $vendor['alternate_part_number'];

            $unit_price   = $vendor['unit_price'];
            $core_charge  = $vendor['core_charge'];
            $total_price  = ($unit_price * $quantity) + ($core_charge * $quantity);

            $_SESSION['poe']['request'][$id]['vendors'][$key]['unit_price'] = $unit_price;
            $_SESSION['poe']['request'][$id]['vendors'][$key]['quantity'] = $quantity;
            $_SESSION['poe']['request'][$id]['vendors'][$key]['left_received_quantity'] = $quantity;
            $_SESSION['poe']['request'][$id]['vendors'][$key]['left_paid_quantity'] = $quantity;
            $_SESSION['poe']['request'][$id]['vendors'][$key]['core_charge'] = $core_charge;
            $_SESSION['poe']['request'][$id]['vendors'][$key]['total'] = $total_price;
            $_SESSION['poe']['request'][$id]['vendors'][$key]['left_paid_amount'] = $total_price;
          }
        }
        // die();

        $data['success'] = TRUE;
      } else {
        $data['success'] = FALSE;
        $data['message'] = 'No data to update!';
      }
    }

    echo json_encode($data);
  }

  public function discard()
  {
    $this->authorized($this->module['permission']['document']);

    unset($_SESSION['poe']);

    redirect($this->modules['purchase_order']['route']);
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
