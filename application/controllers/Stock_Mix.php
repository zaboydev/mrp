<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_Mix extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['stock_mix'];
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function create($category = NULL)
  {
    $this->authorized($this->module, 'create');

    if ($category !== NULL){
      $category = urldecode($category);

      $_SESSION['mix']['mixed_items']     = array();
      $_SESSION['mix']['mixing_item']     = NULL;
      $_SESSION['mix']['mixing_quantity'] = 0;
      $_SESSION['mix']['category']        = $category;
      $_SESSION['mix']['warehouse']       = config_item('auth_warehouse');
      $_SESSION['mix']['notes']           = NULL;

      redirect($this->module['route'] .'/create');
    }

    if (!isset($_SESSION['mix']))
      redirect($this->module['route']);

    $this->data['page']['content']    = $this->module['view'] .'/create';
    $this->data['page']['offcanvas']  = $this->module['view'] .'/create_offcanvas_add_item';

    $this->render_view($this->module['view'] .'/create');
  }

  public function save()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'create') == FALSE){
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      if (!isset($_SESSION['mix']['items']) || empty($_SESSION['mix']['items'])){
        $data['success'] = FALSE;
        $data['message'] = 'Please add at least 1 item!';
      } else {
        $document_number = $_SESSION['mix']['document_number'] . mix_format_number();

        $errors = array();

        if (isset($_SESSION['mix']['edit'])){
          if ($_SESSION['mix']['edit'] != $document_number && $this->model->isDocumentNumberExists($document_number)){
            $errors[] = 'Duplicate Document Number: '. $_SESSION['mix']['document_number'] .' !';
          }
        } else {
          if ($this->model->isDocumentNumberExists($document_number)){
            $errors[] = 'Duplicate Document Number: '. $_SESSION['mix']['document_number'] .' !';
          }
        }

        foreach ($_SESSION['mix']['items'] as $key => $item) {
          if (isStoresExists($item['stores']) && isStoresExists($item['stores'], $_SESSION['mix']['category']) === FALSE){
            $errors[] = 'Stores '. $item['stores'] .' exists for other inventory! Please change the stores.';
          }

          if (isItemExists($item['part_number'],$item['description']) && !empty($item['serial_number'])){
            $item_id = getItemId($item['part_number'],$item['description']);

            if (isSerialExists($item_id, $item['serial_number'])){
              $serial = getSerial($item_id, $item['serial_number']);

              if (isset($_SESSION['mix']['document_edit']) && !empty($_SESSION['mix']['document_edit'])){
                if ($serial->reference_document != $_SESSION['mix']['document_edit']){
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
            unset($_SESSION['mix']);

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
    $this->authorized($this->module, 'create');

    if (isset($_POST) && !empty($_POST)){
      $_SESSION['mix']['items'][] = array(
        'group'                   => $this->input->post('group'),
        'description'             => trim(strtoupper($this->input->post('description'))),
        'part_number'             => trim(strtoupper($this->input->post('part_number'))),
        'alternate_part_number'   => trim(strtoupper($this->input->post('alternate_part_number'))),
        'serial_number'           => trim(strtoupper($this->input->post('serial_number'))),
        'received_quantity'       => $this->input->post('received_quantity'),
        'received_unit_value'     => $this->input->post('received_unit_value'),
        'minimum_quantity'        => $this->input->post('minimum_quantity'),
        'condition'               => $this->input->post('condition'),
        'stores'                  => trim(strtoupper($this->input->post('stores'))),
        'purchase_order_number'   => trim(strtoupper($this->input->post('purchase_order_number'))),
        'purchase_order_item_id'  => trim($this->input->post('purchase_order_item_id')),
        'reference_number'        => trim(strtoupper($this->input->post('reference_number'))),
        'awb_number'              => trim(strtoupper($this->input->post('awb_number'))),
        'unit'                    => trim($this->input->post('unit')),
        'remarks'                 => trim($this->input->post('remarks')),
      );
    }

    redirect($this->module['route'] .'/create');
  }

  public function discard()
  {
    $this->authorized($this->module['permission']['document']);

    unset($_SESSION['mix']);

    redirect($this->module['route']);
  }
}
