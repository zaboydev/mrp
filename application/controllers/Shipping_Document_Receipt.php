<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Shipping_Document_Receipt extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['shipping_document_receipt'];
    $this->load->helper($this->module['helper']);
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function set_received_date()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['receive']['received_date'] = $_GET['data'];
  }

  public function set_received_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['receive']['received_by'] = $_GET['data'];
  }

  public function set_known_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['receive']['known_by'] = $_GET['data'];
  }

  public function set_notes()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['receive']['notes'] = $_GET['data'];
  }

  public function del_item($key)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (isset($_SESSION['receive']['items']))
      unset($_SESSION['receive']['items'][$key]);
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
        $col[] = print_date($row['issued_date'],'d/m/Y');
        $col[] = print_date($row['received_date'],'d/m/Y');
        $col[] = print_string($row['warehouse']);
        $col[] = print_string($row['issued_to']);
        $col[] = print_string($row['coa']);
        $col[] = print_string($row['kode_stok']);
        $col[] = print_string($row['item_id']);
        $col[] = print_string($row['part_number']);
        $col[] = print_string($row['serial_number']);
        $col[] = print_string($row['description']);
        $col[] = print_number($row['issued_quantity'],2);
        $col[] = print_number($row['issued_quantity']-$row['left_received_quantity'],2);
        $col[] = print_string($row['unit']);
        $col[] = print_string($row['remarks']);
        $col[] = print_string($row['issued_by']);
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
      5   => array( 0 => 6,  1 => 'desc' ),
      6   => array( 0 => 7,  1 => 'asc' ),
      7   => array( 0 => 7,  1 => 'asc' ),
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

    $pdfFilePath = str_replace('/', '-', $entity['document_number']) .".pdf";

    $this->load->library('m_pdf');

    $pdf = $this->m_pdf->load(null, 'A4-L');
    $pdf->WriteHTML($html);
    $pdf->Output($pdfFilePath, "I");
  }

  public function search_stores()
  {
    if (empty($_GET['warehouse'])){
      $warehouse = config_item('auth_warehouse');
    } else {
      $warehouse = urldecode($_GET['warehouse']);
    }

    if (empty($_GET['category'])){
      $category = config_item('auth_inventory');
    } else {
      $category = (array)urldecode($_GET['category']);
    }

    $entities = $this->model->findStores($warehouse, $category);

    echo $entities;
  }

  public function receive($id)
  {
    $this->authorized($this->module, 'document');

    $this->data['id']     = $id;
    $this->data['entity'] = $this->model->findById($id);

    $this->render_view($this->module['view'] .'/receive');
  }

  public function save($id)
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') == FALSE){
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      $errors = array();

      foreach ($this->input->post('items') as $i => $item) {
        if (isStoresExists($item['stores']) && isStoresExists($item['stores'], $_POST['category']) === FALSE){
          $errors[] = 'Stores '. $item['stores'] .' exists for other inventory! Please change the stores.';
        }

        if (isItemExists($item['part_number'],$item['description']) && !empty($item['serial_number'])){
          $item_id = getItemId($item['part_number'],$item['description']);

          if (isSerialExists($item_id, $item['serial_number'])){
            $serial = getSerial($item_id, $item['serial_number']);

            //if ($serial->quantity > 0){
              //$errors[] = 'Serial number '. $item['serial_number'] .' contains quantity in stores '. $serial->//stores .'/'. $serial->warehouse .'. Please recheck your document.';
            //}
          }
        }
      }

      if (!empty($errors)){
        $data['success'] = FALSE;
        $data['message'] = implode('<br />', $errors);
      } else {
        if ($this->model->save($id)){
          unset($_POST);

          $data['success'] = TRUE;
          $data['message'] = 'Document '. $this->input->post('document_number') .' has been saved. You will redirected now.';
        } else {
          $data['success'] = FALSE;
          $data['message'] = 'Error while saving this document. Please ask Technical Support.';
        }
      }
    }

    echo json_encode($data);
  }

  public function discard()
  {
    $this->authorized($this->module['permission']['create']);

    unset($_SESSION['receive']);

    redirect($this->module['route']);
  }

  public function send_back($issuance_item_id)
  {
    // if ($this->input->is_ajax_request() == FALSE)
    //   redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'send_back') == FALSE){
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to sent this item back!';
    } else {
      $errors = array();

      if ($this->model->send_back($issuance_item_id)){
          unset($_POST);

          $data['success'] = TRUE;
          $data['message'] = 'Item Has Been Sent Back. You will redirected now.';
      } else {
          $data['success'] = FALSE;
          $data['message'] = 'Error while Sent Back. Please ask Technical Support.';
      }
    }

    echo json_encode($data);
  }
}
