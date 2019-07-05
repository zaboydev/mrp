<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Shipping_Document extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['shipping_document'];
    $this->load->helper($this->module['helper']);
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function set_doc_number()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (empty($_GET['data']))
      $number = shipment_last_number();
    else
      $number = $_GET['data'];

    $_SESSION['shipment']['document_number'] = $number;
  }

  public function set_issued_date()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['shipment']['issued_date'] = $_GET['data'];
  }

  public function set_issued_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['shipment']['issued_by'] = $_GET['data'];
  }

  public function set_issued_to()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['shipment']['issued_to'] = $_GET['data'];
  }

  public function set_warehouse()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['shipment']['warehouse'] = $_GET['data'];
  }

  public function set_sent_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['shipment']['sent_by'] = $_GET['data'];
  }

  public function set_known_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['shipment']['known_by'] = $_GET['data'];
  }

  public function set_approved_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['shipment']['approved_by'] = $_GET['data'];
  }

  public function set_required_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['shipment']['required_by'] = $_GET['data'];
  }

  public function set_requisition_reference()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['shipment']['requisition_reference'] = $_GET['data'];
  }

  public function set_notes()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['shipment']['notes'] = $_GET['data'];
  }

  public function del_item($key)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (isset($_SESSION['shipment']['items']))
      unset($_SESSION['shipment']['items'][$key]);
  }

  public function search_stock_in_stores()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $category   = $_SESSION['shipment']['category'];
    $warehouse  = $_SESSION['shipment']['warehouse'];
    $entities   = $this->model->searchStockInStores($category, $warehouse);

    foreach ($entities as $key => $value){
      $entities[$key]['label'] = $value['description'];
      $entities[$key]['label'] .= ' || PN: ';
      $entities[$key]['label'] .= $value['part_number'];
      $entities[$key]['label'] .= ' || ';
      $entities[$key]['label'] .= $value['condition'];
      $entities[$key]['label'] .= '<small>';
      $entities[$key]['label'] .= ($value['serial_number'] !== "") ? "SN: ". $value['serial_number'] ." || " : "";
      $entities[$key]['label'] .= 'Stores: '. $value['stores'] .' || ';
      $entities[$key]['label'] .= 'Received date: '. date('d/m/Y', strtotime($value['received_date'])) .' || ';
      $entities[$key]['label'] .= 'Expired date: '. date('d/m/Y', strtotime($value['expired_date'])) .' || ';
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
        $col[]  = print_number($no);
        $col[]  = print_string($row['document_number']);
        $col[]  = print_date($row['issued_date']);
        $col[]  = print_string($row['category']);
        $col[]  = print_string($row['warehouse']);
        $col[]  = print_string($row['description']);
        $col[]  = print_string($row['part_number']);
        $col[]  = print_string($row['serial_number']);
        $col[]  = print_string($row['condition']);
        $col[]  = print_string($row['issued_quantity']);
        $col[]  = print_string($row['unit']);
        $col[]  = print_string($row['awb_number']);
        $col[]  = print_string($row['remarks']);
        $col[]  = print_string($row['issued_to']);
        $col[]  = print_string($row['issued_by']);
        $col[]  = print_date($row['received_date']);

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

  public function edit($id)
  {
    $this->authorized($this->module, 'document');

    $entity = $this->model->findById($id);

    if ($this->model->isValidDocumentQuantity($entity['document_number']) === FALSE){
      $this->session->set_flashdata('alert', array(
        'type' => 'danger',
        'info' => 'Stock quantity for document ' . $entity['document_number'] . ' has been change. You are not allowed to edit this document. You can adjust stock to sync the quantity.'
      ));

      redirect(site_url($this->module['route']));
    }

    $document_number  = sprintf('%06s', substr($entity['document_number'], 0, 6));

    if (isset($_SESSION['shipment']) === FALSE){
      $_SESSION['shipment']                     = $entity;
      $_SESSION['shipment']['id']               = $id;
      $_SESSION['shipment']['edit']             = $entity['document_number'];
      $_SESSION['shipment']['document_number']  = $document_number;
    }

    redirect($this->module['route'] .'/create');
  }

  public function create($category = NULL)
  {
    $this->authorized($this->module, 'document');

    if ($category !== NULL){
      $category = urldecode($category);

      $_SESSION['shipment']['items']            = array();
      $_SESSION['shipment']['category']         = $category;
      $_SESSION['shipment']['document_number']  = shipment_last_number();
      $_SESSION['shipment']['issued_date']      = date('Y-m-d');
      $_SESSION['shipment']['issued_by']        = config_item('auth_person_name');
      $_SESSION['shipment']['issued_to']        = NULL;
      $_SESSION['shipment']['sent_by']          = NULL;
      $_SESSION['shipment']['known_by']         = NULL;
      $_SESSION['shipment']['approved_by']      = NULL;
      $_SESSION['shipment']['warehouse']        = config_item('auth_warehouse');
      $_SESSION['shipment']['notes']            = 'Not commercial value (total value for insurance purpose) ';

      redirect($this->module['route'] .'/create');
    }

    if (!isset($_SESSION['shipment']))
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
      if (!isset($_SESSION['shipment']['items']) || empty($_SESSION['shipment']['items'])){
        $data['success'] = FALSE;
        $data['message'] = 'Please add at least 1 item!';
      } else {
        $document_number = $_SESSION['shipment']['document_number'] . shipment_format_number();

        $errors = array();

        if (isset($_SESSION['shipment']['edit'])){
          if ($_SESSION['shipment']['edit'] != $document_number && $this->model->isDocumentNumberExists($document_number)){
            $errors[] = 'Duplicate Document Number: '. $_SESSION['shipment']['document_number'] .' !';
          }
        } else {
          if ($this->model->isDocumentNumberExists($document_number)){
            $errors[] = 'Duplicate Document Number: '. $_SESSION['shipment']['document_number'] .' !';
          }
        }

        foreach ($_SESSION['shipment']['items'] as $key => $item) {
          if (isStoresExists($item['stores']) && isStoresExists($item['stores'], $_SESSION['shipment']['category']) === FALSE){
            $errors[] = 'Stores '. $item['stores'] .' exists for other inventory! Please change the stores.';
          }

          if (isItemExists($item['part_number']) && !empty($item['serial_number'])){
            $item_id = getItemId($item['part_number']);

            if (isSerialExists($item_id, $item['serial_number'])){
              $serial = getSerial($item_id, $item['serial_number']);

              if (isset($_SESSION['shipment']['document_edit']) && !empty($_SESSION['shipment']['document_edit'])){
                if ($serial->reference_document != $_SESSION['shipment']['document_edit']){
                  if ($serial->quantity > 0){
                    $errors[] = 'Serial number '. $item['serial_number'] .' contains quantity in stores '. $serial->stores .'/'. $serial->warehouse .'. Please recheck your document.';
                  }
                }
              }
            }
          }
        }

        if (!empty($errors)){
          $data['success'] = FALSE;
          $data['message'] = implode('<br />', $errors);
        } else {
          if ($this->model->save()){
            unset($_SESSION['shipment']);

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
    $this->authorized($this->module, 'document');

    if (isset($_POST) && !empty($_POST)){
      $_SESSION['shipment']['items'][] = array(
        'stock_in_stores_id'      => $this->input->post('stock_in_stores_id'),
        'group'                   => $this->input->post('group'),
        'description'             => trim(strtoupper($this->input->post('description'))),
        'part_number'             => trim(strtoupper($this->input->post('part_number'))),
        'alternate_part_number'   => trim(strtoupper($this->input->post('alternate_part_number'))),
        'serial_number'           => trim(strtoupper($this->input->post('serial_number'))),
        'issued_quantity'         => floatval($this->input->post('issued_quantity')),
        'issued_unit_value'       => floatval($this->input->post('issued_unit_value')),
        'maximum_quantity'        => floatval($this->input->post('maximum_quantity')),
        'insurance_unit_value'    => floatval($this->input->post('insurance_unit_value')),
        'insurance_currency'      => trim(strtoupper($this->input->post('insurance_currency'))),
        'awb_number'              => $this->input->post('awb_number'),
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

    unset($_SESSION['shipment']);

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
