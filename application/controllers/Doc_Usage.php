<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Doc_Usage extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['doc_usage'];
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function set_document_number()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (empty($_GET['data']))
      $number = usage_last_number();
    else
      $number = $_GET['data'];

    $_SESSION['usage']['document_number'] = $number;
  }

  public function set_document_issued_date()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['usage']['issued_date'] = $_GET['data'];
  }

  public function set_document_issued_by()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['usage']['issued_by'] = $_GET['data'];
  }

  public function set_document_issued_to()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['usage']['issued_to'] = $_GET['data'];
  }

  public function set_document_required_by()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['usage']['required_by'] = $_GET['data'];
  }

  public function set_document_approved_by()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['usage']['approved_by'] = $_GET['data'];
  }

  public function set_document_notes()
  {
    $_SESSION['usage']['notes'] = $_GET['data'];
  }

  public function index()
  {
    $this->authorized($this->module, 'index');

    $this->data['page']['title']            = $this->module['label'];
    $this->data['page']['requirement']      = array('datatable', 'datamodal');
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source');
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = NULL;
    $this->data['grid']['order_columns']    = array (
      0 => array ( 0 => 1, 1 => 'desc' ),
      1 => array ( 0 => 2, 1 => 'desc' ),
      2 => array ( 0 => 3, 1 => 'asc' ),
      3 => array ( 0 => 4, 1 => 'asc' ),
      4 => array ( 0 => 5, 1 => 'asc' ),
      5 => array ( 0 => 6, 1 => 'asc' ),
      6 => array ( 0 => 7, 1 => 'asc' ),
      7 => array ( 0 => 8, 1 => 'asc' ),
    );

    $this->render_view($this->module['view'] .'/index');
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
        $col[] = print_string($row['warehouse']);
        $col[] = print_string($row['issued_to']);
        $col[] = print_string($row['category']);
        $col[] = print_date($row['issued_date']);
        $col[] = print_string($row['required_by']);
        $col[] = print_string($row['issued_by']);
        $col[] = $row['notes'];
        $col['DT_RowId'] = 'row_'. $row['id'];
        $col['DT_RowData']['pkey'] = $row['id'];

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
    $this->authorized($this->module, 'show');

    $entity = $this->model->findById($id);

    $this->data['entity']           = $entity;
    $this->data['page']['title']    = 'SHIPPING DOCUMENT';
    $this->data['page']['content']  = $this->module['view'] .'/print_pdf';

    $html = $this->load->view($this->pdf_theme, $this->data, true);

    $pdfFilePath = str_replace('/', '', $entity['document_number']) .".pdf";

    $this->load->library('m_pdf');

    $pdf = $this->m_pdf->load(null, 'A4-L');
    $pdf->WriteHTML($html);
    $pdf->Output($pdfFilePath, "I");
  }

  public function create($category = NULL)
  {
    $this->authorized($this->module, 'create');

    if ($category !== NULL){
      $category = urldecode($category);

      $_SESSION['usage']['items']            = array();
      $_SESSION['usage']['category']    = $category;
      $_SESSION['usage']['document_number']  = usage_last_number();
      $_SESSION['usage']['issued_date']      = date('Y-m-d');
      $_SESSION['usage']['issued_by']        = NULL;
      $_SESSION['usage']['issued_to']        = NULL;
      $_SESSION['usage']['approved_by']      = NULL;
      $_SESSION['usage']['warehouse']        = config_item('auth_warehouse');
      $_SESSION['usage']['required_by']          = config_item('auth_person_name');
      $_SESSION['usage']['notes']            = NULL;

      redirect($this->module['route'] .'/create');
    }

    if (!isset($_SESSION['usage'])){
      redirect($this->module['route']);
    }

    $this->data['floating_actions']['save']['title']  = 'Save Document';
    $this->data['floating_actions']['save']['icon']   = 'md md-save';
    $this->data['floating_actions']['save']['link']   = site_url($this->module['route'] .'/save');
    $this->data['floating_actions']['save']['id']     = 'btn-submit-document';
    $this->data['floating_actions']['save']['class']  = 'btn-lg btn-danger ink-reaction';

    $this->data['page']['content'] = $this->module['view'] .'/create';

    $this->render_view($this->module['view'] .'/create');
  }

  public function edit($id)
  {
    $this->authorized($this->module, 'edit');

    $entity = $this->model->findById($id);
    $document_number = sprintf('%06s', substr($entity['document_number'], 0, 6));

    if (isset($_SESSION['usage']) === FALSE){
      $_SESSION['usage'] = $entity;
      $_SESSION['usage']['id']   = $id;
      $_SESSION['usage']['edit'] = $entity['document_number'];
      $_SESSION['usage']['document_number'] = $document_number;
    }

    redirect($this->module['route'] .'/create');
  }

  public function save()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'save') == FALSE){
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this document!';
    } else {
      if (!isset($_SESSION['usage']['items']) || empty($_SESSION['usage']['items'])){
        $data['success'] = FALSE;
        $data['message'] = 'Please add at least 1 item!';
      } else {
        $document_number = $_SESSION['usage']['document_number'] . usage_format_number();

        if (isset($_SESSION['usage']['edit']) && $_SESSION['usage']['edit'] !== $document_number){
          if ( $this->model->isDocumentNumberExists($document_number)){
            $data['success'] = FALSE;
            $data['message'] = 'Duplicate Document Number: '. $_SESSION['usage']['document_number'] .' ! Try using '. usage_last_number() .' for document number.';
          }
        }

        if ($this->model->save()){
          unset($_SESSION['usage']);

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

  public function add_item($id = NULL)
  {
    $this->authorized($this->module, 'add_item');

    if (isset($_POST) && !empty($_POST)){
      if (!isset($_SESSION['usage'])){
        $_SESSION['usage']['items']            = array();
        $_SESSION['usage']['category']         = available_categories($this->input->post('group'));
        $_SESSION['usage']['document_number']  = usage_last_number();
        $_SESSION['usage']['issued_date']      = date('Y-m-d');
        $_SESSION['usage']['issued_by']        = NULL;
        $_SESSION['usage']['issued_to']        = NULL;
        $_SESSION['usage']['required_by']          = config_item('auth_person_name');
        $_SESSION['usage']['warehouse']        = config_item('auth_warehouse');
        $_SESSION['usage']['notes']            = NULL;
      }

      $_SESSION['usage']['items'][] = array(
        'group'            => trim(strtoupper($this->input->post('group'))),
        'description'           => trim(strtoupper($this->input->post('description'))),
        'part_number'           => trim(strtoupper($this->input->post('part_number'))),
        'item_serial'         => trim(strtoupper($this->input->post('item_serial'))),
        'quantity'              => trim($this->input->post('quantity')),
        'condition'        => trim(strtoupper($this->input->post('condition'))),
        'stores'                => trim(strtoupper($this->input->post('stores'))),
        'issued_quantity'       => trim($this->input->post('issued_quantity')),
        'unit'                   => trim(strtoupper($this->input->post('unit'))),
        'estimated_unit_value'  => trim($this->input->post('estimated_unit_value')),
        'unit_value'            => trim($this->input->post('unit_value')),
        'stock_stores_id'   => trim($this->input->post('stock_stores_id')),
        'notes'                 => trim($this->input->post('notes')),
      );

      redirect($this->module['route'] .'/create');
    }

    if ($id !== NULL){
      $this->data['entity'] = $this->model->findItemInStoresById($id);
      $this->data['page']['content'] = $this->module['view'] .'/add_item';

      $this->render_view();
    }
  }

  public function del_item($key)
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (isset($_SESSION['usage']['items']))
      unset($_SESSION['usage']['items'][$key]);
  }

  public function discard()
  {
    $this->authorized($this->module['permission']['create']);

    unset($_SESSION['usage']);

    redirect($this->module['route']);
  }

  public function receive($id)
  {
    $this->authorized($this->module, 'receive');

    $this->data['entity'] = $this->model->findById($id);

    $this->render_view($this->module['view'] .'/receive');
  }

  public function receive_save($id)
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'receive') == FALSE){
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this document!';
    } else {
      if ($this->model->receive_save($id)){
        $data['success'] = TRUE;
        $data['message'] = 'Receive has been saved. You will redirected now.';
      } else {
        $data['success'] = FALSE;
        $data['message'] = 'Error while saving this document. Please ask Technical Support.';
      }
    }

    echo json_encode($data);
  }
}
