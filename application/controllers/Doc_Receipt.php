<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Doc_Receipt extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['doc_receipt'];
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function set_doc_number()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (empty($_GET['data']))
      $number = receipt_last_number();
    else
      $number = $_GET['data'];

    $_SESSION['receipt']['document_number'] = $number;
  }

  public function set_received_date()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['receipt']['received_date'] = $_GET['data'];
  }

  public function set_received_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['receipt']['received_by'] = $_GET['data'];
  }

  public function set_received_from()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['receipt']['received_from'] = $_GET['data'];
  }

  public function set_notes()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['receipt']['notes'] = $_GET['data'];
  }

  public function del_item($key)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (isset($_SESSION['receipt']['items']))
      unset($_SESSION['receipt']['items'][$key]);
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
      0   => array( 0 => 2,  1 => 'desc' ),
      1   => array( 0 => 1,  1 => 'desc' ),
      2   => array( 0 => 3,  1 => 'asc' ),
      3   => array( 0 => 4,  1 => 'asc' ),
      4   => array( 0 => 5,  1 => 'asc' ),
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
        $col[] = print_date($row['received_date']);
        $col[] = print_string($row['received_from']);
        $col[] = print_string($row['category']);
        $col[] = $row['notes'];
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
    $this->data['page']['title']    = 'GOODS RECEIVED NOTE';
    $this->data['page']['content']  = $this->module['view'] .'/print_pdf';

    $html = $this->load->view($this->pdf_theme, $this->data, true);

    $pdfFilePath = str_replace('/', '', $entity['document_number']) .".pdf";

    $this->load->library('m_pdf');

    $pdf = $this->m_pdf->load(null, 'A4-L');
    $pdf->WriteHTML($html);
    $pdf->Output($pdfFilePath, "I");
  }

  public function edit($id)
  {
    $this->authorized($this->module, 'edit');

    $entity = $this->model->findById($id);
    $document_number = sprintf('%06s', substr($entity['document_number'], 0, 6));

    if (isset($_SESSION['receipt']) === FALSE){
      $_SESSION['receipt'] = $entity;
      $_SESSION['receipt']['id'] = $id;
      $_SESSION['receipt']['edit'] = $entity['document_number'];
      $_SESSION['receipt']['document_number'] = $document_number;
    }

    redirect($this->module['route'] .'/create');
  }

  public function create($category = NULL)
  {
    $this->authorized($this->module, 'create');

    if ($category !== NULL){
      $category = urldecode($category);

      $_SESSION['receipt']['items']           = array();
      $_SESSION['receipt']['category']   = $category;
      $_SESSION['receipt']['document_number'] = receipt_last_number();
      $_SESSION['receipt']['received_date']   = date('Y-m-d');
      $_SESSION['receipt']['received_by']     = config_item('auth_person_name');
      $_SESSION['receipt']['received_from']   = NULL;
      $_SESSION['receipt']['notes']           = NULL;

      redirect($this->module['route'] .'/create');
    }

    if (!isset($_SESSION['receipt']))
      redirect($this->module['route']);

    $this->render_view($this->module['view'] .'/create');
  }

  public function save()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'save') == FALSE){
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      if (!isset($_SESSION['receipt']['items']) || empty($_SESSION['receipt']['items'])){
        $data['success'] = FALSE;
        $data['message'] = 'Please add at least 1 item!';
      } else {
        $document_number = $_SESSION['receipt']['document_number'] . receipt_format_number();

        if (isset($_SESSION['receipt']['edit']) && $_SESSION['receipt']['edit'] !== $document_number){
          if ( $this->model->isDocumentNumberExists($document_number)){
            $data['success'] = FALSE;
            $data['message'] = 'Duplicate Document Number: '. $_SESSION['receipt']['document_number'] .' ! Try using '. receipt_last_number() .' for document number.';
          }
        }

        if ($this->model->save()){
          unset($_SESSION['receipt']);

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
    $this->authorized($this->module, 'add_item');

    if (isset($_POST) && !empty($_POST)){
      $_SESSION['receipt']['items'][] = array(
        'group'        => $this->input->post('group'),
        'description'       => trim(strtoupper($this->input->post('description'))),
        'part_number'       => trim(strtoupper($this->input->post('part_number'))),
        'alternate_part_number'   => trim(strtoupper($this->input->post('alternate_part_number'))),
        'item_serial'     => trim(strtoupper($this->input->post('item_serial'))),
        'received_quantity' => $this->input->post('received_quantity'),
        'minimum_quantity'  => $this->input->post('minimum_quantity'),
        'unit_value'        => $this->input->post('unit_value'),
        'condition'    => $this->input->post('condition'),
        'stores'            => trim(strtoupper($this->input->post('stores'))),
        'expired_date'      => trim($this->input->post('expired_date')),
        'unit'               => trim($this->input->post('unit')),
        'order_number'      => trim(strtoupper($this->input->post('order_number'))),
        'awb_number'        => trim($this->input->post('awb_number')),
        'reference_number'  => trim($this->input->post('reference_number')),
        'notes'             => trim($this->input->post('notes')),
      );
    }

    redirect($this->module['route'] .'/create');
  }

  public function discard()
  {
    $this->authorized($this->module['permission']['create']);

    unset($_SESSION['receipt']);

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
      if ($this->model->delete()){
        $alert['type'] = 'success';
        $alert['info'] = 'Data deleted.';
        $alert['link'] = site_url($this->module['route']);
      } else {
        $alert['type'] = 'danger';
        $alert['info'] = 'There are error while deleting data. Please try again later.';
      }
    }

    echo json_encode($alert);
  }
}
