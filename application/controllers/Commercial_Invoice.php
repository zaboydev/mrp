<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Commercial_Invoice extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['commercial_invoice'];
    $this->load->helper($this->module['helper']);
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function set_doc_number()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (empty($_GET['data']))
      $number = return_last_number();
    else
      $number = $_GET['data'];

    $_SESSION['return']['document_number'] = $number;
  }

  public function set_issued_date()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['return']['issued_date'] = $_GET['data'];
  }

  public function set_issued_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['return']['issued_by'] = $_GET['data'];
  }

  public function set_issued_to()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['return']['issued_to'] = $_GET['data'];
  }

  public function set_issued_address()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['return']['issued_address'] = $_GET['data'];
  }

  public function set_sent_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['return']['sent_by'] = $_GET['data'];
  }

  public function set_known_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['return']['known_by'] = $_GET['data'];
  }

  public function set_approved_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['return']['approved_by'] = $_GET['data'];
  }

  public function set_required_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['return']['required_by'] = $_GET['data'];
  }

  public function set_requisition_reference()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['return']['requisition_reference'] = $_GET['data'];
  }

  public function set_notes()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['return']['notes'] = $_GET['data'];
  }

  public function del_item($key)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (isset($_SESSION['return']['items']))
      unset($_SESSION['return']['items'][$key]);
  }

  public function set_source($source)
  {
    // if ($this->input->is_ajax_request() === FALSE)
    //   redirect($this->modules['secure']['route'] .'/denied');

    // $_SESSION['receipt']['source'] = $_GET['data'];
    $this->authorized($this->module, 'document');

    $source = urldecode($source);

    $_SESSION['return']['source']              = $source;
    $_SESSION['return']['items']                  = array();

    redirect($this->module['route'] . '/create');
  }

  public function search_stock_in_stores()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $category = $_SESSION['return']['category'];
    if($_SESSION['return']['source']=='internal_delivery'){
      $entities = $this->model->searchInternalDeliveryItem($category);
    }else{
      $entities = $this->model->searchStockInStores($category);
    }    

    foreach ($entities as $key => $value){
      $entities[$key]['label'] = $value['description'];
      $entities[$key]['label'] .= ' || PN: ';
      $entities[$key]['label'] .= $value['part_number'];
      $entities[$key]['label'] .= ' || ';
      $entities[$key]['label'] .= $value['condition'];
      $entities[$key]['label'] .= '<small>';
      $entities[$key]['label'] .= ($value['serial_number'] !== "") ? "SN: ". $value['serial_number'] ." || " : "";
      $entities[$key]['label'] .= 'Stores: '. $value['stores'] .' || ';
      $entities[$key]['label'] .= 'Received From: '. $value['received_from'] .' || ';
      $entities[$key]['label'] .= 'Received date: '. date('d/m/Y', strtotime($value['received_date'])) .' || ';
      if($_SESSION['return']['source']=='stock'){
        $entities[$key]['label'] .= 'Expired date: '. date('d/m/Y', strtotime($value['expired_date'])) .' || ';
      }      
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
      $unit_value   = array();
      $total_value  = array();
      $total_value_usd  = array();

      foreach ($entities as $row){
        $no++;
        $col    = array();

        $col[]  = print_number($no);
        $col[]  = print_string($row['document_number']);
        $col[]  = print_string($row['status']);
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
        $col[]  = print_string($row['received_from']);
        if (config_item('auth_role') != 'PIC STOCK'){
          $col[]          = print_number($row['insurance_unit_value'], 2);
          $col[]          = print_number($row['insurance_unit_value']*$row['issued_quantity'], 2);


          $unit_value[]   = $row['insurance_unit_value'];
          $total_value[]  = $row['insurance_unit_value']*$row['issued_quantity'];
        }
        if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE'){          
          $col[]  = print_number($row['kurs_dollar'], 2);
          $col[]  = print_number($row['unit_value_dollar']*$row['issued_quantity'], 2);
          $col[]  = print_string($row['kode_pemakaian']);


          $total_value_usd[]  = $row['unit_value_dollar']*$row['issued_quantity'];
        }
        $quantity[] = $row['issued_quantity'];

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
        "total" => array(
          9 => print_number(array_sum($quantity), 2),
        )
      );
      if (config_item('auth_role') != 'PIC STOCK'){
        // $result['total'][15] = print_number(array_sum($unit_value), 2);
        $result['total'][17] = print_number(array_sum($total_value), 2);
      }
      if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE'){
        // $result['total'][15] = print_number(array_sum($unit_value), 2);
        $result['total'][19] = print_number(array_sum($total_value), 2);
      }
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
    $this->data['grid']['summary_columns']  = array(9);
    if (config_item('auth_role') != 'PIC STOCK'){
      // $this->data['grid']['summary_columns'][] = 15;
      $this->data['grid']['summary_columns'][] = 17;
    }
    if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE'){
      $this->data['grid']['summary_columns'][] = 19;
    }
    $this->data['grid']['order_columns']    = array(
      0   => array( 0 => 1,  1 => 'desc' ),
      1   => array( 0 => 2,  1 => 'desc' ),
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
    $this->authorized($this->module, 'print');

    $entity = $this->model->findById($id);

    $this->data['entity']           = $entity;
    $this->data['page']['title']    = strtoupper($this->module['label']);
    $this->data['page']['content']  = $this->module['view'] .'/print_pdf';

    $html = $this->load->view($this->module['view'] .'/pdf', $this->data, true);

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

    if (isset($_SESSION['return']) === FALSE){
      $_SESSION['return']                     = $entity;
      $_SESSION['return']['id']               = $id;
      $_SESSION['return']['edit']             = $entity['document_number'];
      $_SESSION['return']['document_number']  = $document_number;
    }

    redirect($this->module['route'] .'/create');
  }

  public function create($category = NULL)
  {
    $this->authorized($this->module, 'document');

    if ($category !== NULL){
      $category = urldecode($category);

      $_SESSION['return']['items']            = array();
      $_SESSION['return']['category']         = $category;
      $_SESSION['return']['document_number']  = return_last_number();
      $_SESSION['return']['issued_date']      = date('Y-m-d');
      $_SESSION['return']['issued_by']        = config_item('auth_person_name');
      $_SESSION['return']['issued_to']        = NULL;
      $_SESSION['return']['issued_address']   = NULL;
      $_SESSION['return']['sent_by']          = NULL;
      $_SESSION['return']['known_by']         = NULL;
      $_SESSION['return']['approved_by']      = NULL;
      $_SESSION['return']['warehouse']        = config_item('auth_warehouse');
      $_SESSION['return']['notes']            = 'Not commercial value (total value for insurance purpose) ';
      $_SESSION['return']['source']           = 'internal_delivery';

      redirect($this->module['route'] .'/create');
    }

    if (!isset($_SESSION['return']))
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
      if (!isset($_SESSION['return']['items']) || empty($_SESSION['return']['items'])){
        $data['success'] = FALSE;
        $data['message'] = 'Please add at least 1 item!';
      } else {
        $document_number = $_SESSION['return']['document_number'] . return_format_number();

        $errors = array();

        if (isset($_SESSION['return']['edit'])){
          if ($_SESSION['return']['edit'] != $document_number && $this->model->isDocumentNumberExists($document_number)){
            $errors[] = 'Duplicate Document Number: '. $_SESSION['return']['document_number'] .' !';
          }
        } else {
          if ($this->model->isDocumentNumberExists($document_number)){
            $errors[] = 'Duplicate Document Number: '. $_SESSION['return']['document_number'] .' !';
          }
        }

        foreach ($_SESSION['return']['items'] as $key => $item) {
          // if (isStoresExists($item['stores']) && isStoresExists($item['stores'], $_SESSION['return']['category']) === FALSE){
          //   $errors[] = 'Stores '. $item['stores'] .' exists for other inventory! Please change the stores.';
          // }

          if (isItemExists($item['part_number'],$item['description']) && !empty($item['serial_number'])){
            $item_id = getItemId($item['part_number'],$item['description']);

            if (isSerialExists($item_id, $item['serial_number'])){
              $serial = getSerial($item_id, $item['serial_number']);

              if (isset($_SESSION['return']['document_edit']) && !empty($_SESSION['return']['document_edit'])){
                if ($serial->reference_document != $_SESSION['return']['document_edit']){
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
            unset($_SESSION['return']);

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
      $_SESSION['return']['items'][] = array(
        'stock_in_stores_id'      => $this->input->post('stock_in_stores_id'),
        'group'                   => $this->input->post('group'),
        'description'             => trim(strtoupper($this->input->post('description'))),
        'part_number'             => trim(strtoupper($this->input->post('part_number'))),
        'alternate_part_number'   => trim(strtoupper($this->input->post('alternate_part_number'))),
        'serial_number'           => trim(strtoupper($this->input->post('serial_number'))),
        'issued_quantity'         => $this->input->post('issued_quantity'),
        'issued_unit_value'       => $this->input->post('issued_unit_value'),
        'maximum_quantity'        => $this->input->post('maximum_quantity'),
        'insurance_unit_value'    => floatval($this->input->post('insurance_unit_value')),
        'insurance_currency'      => trim(strtoupper($this->input->post('insurance_currency'))),
        'awb_number'              => $this->input->post('awb_number'),
        'condition'               => $this->input->post('condition'),
        'stores'                  => trim(strtoupper($this->input->post('stores'))),
        'unit'                    => trim($this->input->post('unit')),
        'remarks'                 => trim($this->input->post('remarks')),
        'internal_delivery_item_id'      => $this->input->post('internal_delivery_item_id'),
        'received_from'      => $this->input->post('received_from'),
      );
    }

    redirect($this->module['route'] .'/create');
  }

  public function ajax_editItem($key)
  {
    $this->authorized($this->module, 'document');    

    $entity = $_SESSION['return']['items'][$key];

    echo json_encode($entity);
  }

  public function update_item()
  {
    $this->authorized($this->module, 'document');

    $key=$this->input->post('item_id');
    if (isset($_POST) && !empty($_POST)){
      //$receipts_items_id = $this->input->post('item_id')
      $_SESSION['return']['items'][$key] = array( 
        'stock_in_stores_id'      => $this->input->post('stock_in_stores_id'),
        'group'                   => $this->input->post('group'),
        'description'             => trim(strtoupper($this->input->post('description'))),
        'part_number'             => trim(strtoupper($this->input->post('part_number'))),
        'alternate_part_number'   => trim(strtoupper($this->input->post('alternate_part_number'))),
        'serial_number'           => trim(strtoupper($this->input->post('serial_number'))),
        'issued_quantity'         => $this->input->post('issued_quantity'),
        'issued_unit_value'       => $this->input->post('issued_unit_value'),
        'maximum_quantity'        => $this->input->post('maximum_quantity'),
        'insurance_unit_value'    => floatval($this->input->post('insurance_unit_value')),
        'insurance_currency'      => trim(strtoupper($this->input->post('insurance_currency'))),
        'awb_number'              => $this->input->post('awb_number'),
        'condition'               => $this->input->post('condition'),
        'stores'                  => trim(strtoupper($this->input->post('stores'))),
        'unit'                    => trim($this->input->post('unit')),
        'remarks'                 => trim($this->input->post('remarks')),
        'internal_delivery_item_id'      => $this->input->post('internal_delivery_item_id'),
        'received_from'      => $this->input->post('received_from'),
        'dateline'      => $this->input->post('dateline'),
      );
    }
    

    redirect($this->module['route'] .'/create');
  }

  public function discard()
  {
    $this->authorized($this->module['permission']['create']);

    unset($_SESSION['return']);

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

      // if ($this->model->isValidDocumentQuantity($entity['document_number']) === FALSE){
      //   $alert['type']  = 'danger';
      //   $alert['info']  = 'Stock quantity for document ' . $entity['document_number'] . ' has been change. You are not allowed to delete this document. You can adjust stock to sync the quantity.';
      // } else {
        if ($this->model->delete()){
          $alert['type'] = 'success';
          $alert['info'] = 'Data deleted.';
          $alert['link'] = site_url($this->module['route']);
        } else {
          $alert['type'] = 'danger';
          $alert['info'] = 'There are error while deleting data. Please try again later.';
        }
      // }
    }

    echo json_encode($alert);
  }

  public function select_item()
  {
    $this->authorized($this->module, 'document');

    $category = $_SESSION['return']['category'];
    if($_SESSION['return']['source']=='internal_delivery'){
      $entities = $this->model->searchInternalDeliveryItem($category);
    }else{
      $entities = $this->model->searchStockInStores($category);
    }   

    $this->data['entities'] = $entities;
    $this->data['page']['title']            = 'Select Item';

    $this->render_view($this->module['view'] . '/select_item');
  }

  public function add_selected_item()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') == FALSE) {
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      if (isset($_POST['item_id']) && !empty($_POST['item_id'])) {
        $_SESSION['return']['items'] = array();

        foreach ($_POST['item_id'] as $key => $item_id) {
          $item = $this->model->infoSelecteditem($item_id);

          $_SESSION['return']['items'][$item_id] = array(
            'stock_in_stores_id'      => ($_SESSION['return']['source']=='stock')?$item['id']:null,
            'group'                   => $item['group'],
            'description'             => $item['description'],
            'part_number'             => $item['part_number'],
            'alternate_part_number'   => $item['alternate_part_number'],
            'serial_number'           => $item['serial_number'],
            'issued_quantity'         => $item['quantity'],
            'issued_unit_value'       => $item['unit_value'],
            'maximum_quantity'        => $item['quantity'],
            'insurance_unit_value'    => 0,
            'insurance_currency'      => 'IDR',
            'awb_number'              => $item['awb_number'],
            'condition'               => $item['condition'],
            'stores'                  => $item['stores'],
            'unit'                    => $item['unit'],
            'remarks'                 => $item['remarks'],
            'internal_delivery_item_id'      => ($_SESSION['return']['source']=='internal_delivery')?$item['id']:null,
            'received_from'           => $item['received_from'],
            'dateline'    => 30,
          );
        }

        $data['success'] = TRUE;
      } else {
        $data['success'] = FALSE;
        $data['message'] = 'Please select any request!';
      }
    }

    echo json_encode($data);
  }

  public function edit_selected_item()
  {
    $this->authorized($this->module, 'document');

    $this->render_view($this->module['view'] . '/edit_item');
  }

  public function update_selected_item()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') == FALSE) {
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      if (isset($_POST['item']) && !empty($_POST['item'])) {
        foreach ($_POST['item'] as $id => $item) {

          $_SESSION['return']['items'][$id]['issued_quantity']             = $item['issued_quantity'];    
          $_SESSION['return']['items'][$id]['remarks']                     = $item['remarks'];   
          $_SESSION['return']['items'][$id]['issued_unit_value']           = $item['issued_unit_value']; 
          $_SESSION['return']['items'][$id]['insurance_unit_value']        = $item['insurance_unit_value']; 
          $_SESSION['return']['items'][$id]['insurance_currency']          = $item['insurance_currency']; 
          $_SESSION['return']['items'][$id]['awb_number']                  = $item['awb_number']; 
        }

        $data['success'] = TRUE;
      } else {
        $data['success'] = FALSE;
        $data['message'] = 'No data to update!';
      }
    }

    echo json_encode($data);
  }
}
