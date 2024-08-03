<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Doc_Delivery extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['doc_delivery'];
    $this->load->model($this->module['model'], 'model');
    $this->load->helper('document');
    $this->data['module'] = $this->module;
  }

  public function set_doc_number()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (empty($_GET['data']))
      $number = delivery_last_number();
    else
      $number = $_GET['data'];

    $_SESSION['delivery']['document_number'] = $number;
  }

  public function set_received_date()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['delivery']['received_date'] = $_GET['data'];
  }

  public function set_received_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['delivery']['received_by'] = $_GET['data'];
  }

  public function set_received_from()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['delivery']['received_from'] = $_GET['data'];
  }

  public function set_sent_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['delivery']['sent_by'] = $_GET['data'];
  }

  public function set_approved_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['delivery']['approved_by'] = $_GET['data'];
  }

  public function set_notes()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['delivery']['notes'] = $_GET['data'];
  }

  public function del_item($key)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (isset($_SESSION['delivery']['items']))
      unset($_SESSION['delivery']['items'][$key]);
  }

  public function search_items_by_serial()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $category = $_SESSION['delivery']['category'];
    $entities = $this->model->searchItemsBySerial($category);

    foreach ($entities as $key => $value){
      $entities[$key]['label'] = $value['serial_number'];
    }

    echo json_encode($entities);
  }

  public function search_items_by_part_number()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $category = $_SESSION['delivery']['category'];
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
        $col[] = print_date($row['received_date']);
        $col[] = print_string($row['received_from']);
        $col[] = print_string($row['sent_by']);
        $col[] = print_string($row['category']);
        $col[] = print_string($row['warehouse']);
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
      5   => array( 0 => 6,  1 => 'asc' ),
      6   => array( 0 => 7,  1 => 'asc' ),
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

    if (isset($_SESSION['delivery']) === FALSE){
      $_SESSION['delivery'] = $entity;
      $_SESSION['delivery']['id'] = $id;
      $_SESSION['delivery']['edit'] = $entity['document_number'];
      $_SESSION['delivery']['document_number'] = $document_number;
    }

    redirect($this->module['route'] .'/create');
  }

  public function create($category = NULL)
  {
    $this->authorized($this->module, 'create');

    if ($category !== NULL){
      $category = urldecode($category);

      $_SESSION['delivery']['items']            = array();
      $_SESSION['delivery']['category']         = $category;
      $_SESSION['delivery']['document_number']  = delivery_last_number();
      $_SESSION['delivery']['received_date']    = date('Y-m-d');
      $_SESSION['delivery']['received_by']      = config_item('auth_person_name');
      $_SESSION['delivery']['received_from']    = NULL;
      $_SESSION['delivery']['sent_by']          = NULL;
      $_SESSION['delivery']['approved_by']      = NULL;
      $_SESSION['delivery']['warehouse']        = config_item('auth_warehouse');
      $_SESSION['delivery']['notes']            = NULL;

      redirect($this->module['route'] .'/create');
    }

    if (!isset($_SESSION['delivery']))
      redirect($this->module['route']);

    $this->data['page']['content']    = $this->module['view'] .'/create';
    $this->data['page']['offcanvas']  = $this->module['view'] .'/create_offcanvas_add_item';

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
      if (!isset($_SESSION['delivery']['items']) || empty($_SESSION['delivery']['items'])){
        $data['success'] = FALSE;
        $data['message'] = 'Please add at least 1 item!';
      } else {
        $document_number = $_SESSION['delivery']['document_number'] . delivery_format_number();

        $errors = array();

        if (isset($_SESSION['delivery']['edit'])){
          if ($_SESSION['delivery']['edit'] != $document_number && $this->model->isDocumentNumberExists($document_number)){
            $errors[] = 'Duplicate Document Number: '. $_SESSION['delivery']['document_number'] .' !';
          }
        } else {
          if ($this->model->isDocumentNumberExists($document_number)){
            $errors[] = 'Duplicate Document Number: '. $_SESSION['delivery']['document_number'] .' !';
          }
        }

        foreach ($_SESSION['delivery']['items'] as $key => $item) {
          if (isStoresExists($item['stores']) && isStoresExists($item['stores'], $_SESSION['delivery']['category']) === FALSE){
            $errors[] = 'Stores '. $item['stores'] .' exists for other inventory! Please change the stores.';
          }

          if (isItemExists($item['part_number'],$item['description']) && !empty($item['serial_number'])){
            $item_id = getItemId($item['part_number'],$item['description']);

            if (isSerialExists($item_id, $item['serial_number'])){
              $serial = getSerial($item_id, $item['serial_number']);

              if ($serial->quantity > 0){
                $errors[] = 'Serial number '. $item['serial_number'] .' contains quantity in stores '. $serial->stores .'/'. $serial->warehouse .'. Please recheck your document.';
              }
            }
          }
        }

        if (!empty($errors)){
          $data['success'] = FALSE;
          $data['message'] = implode('<br />', $errors);
        } else {
          if ($this->model->save()){
            unset($_SESSION['delivery']);

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

  public function add_item()
  {
    $this->authorized($this->module, 'add_item');

    if (isset($_POST) && !empty($_POST)){
      $_SESSION['delivery']['items'][] = array(
        'group'                   => $this->input->post('group'),
        'description'             => trim(strtoupper($this->input->post('description'))),
        'part_number'             => trim(strtoupper($this->input->post('part_number'))),
        'alternate_part_number'   => trim(strtoupper($this->input->post('alternate_part_number'))),
        'serial_number'           => trim(strtoupper($this->input->post('serial_number'))),
        'received_quantity'       => $this->input->post('received_quantity'),
        'minimum_quantity'        => $this->input->post('minimum_quantity'),
        'unit_value'              => $this->input->post('unit_value'),
        'condition'               => $this->input->post('condition'),
        'stores'                  => trim(strtoupper($this->input->post('stores'))),
        'unit'                    => trim($this->input->post('unit')),
        'remarks'                 => trim($this->input->post('remarks')),
      );
    }

    redirect($this->module['route'] .'/create');
  }

  public function discard()
  {
    $this->authorized($this->module['permission']['create']);

    unset($_SESSION['delivery']);

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
