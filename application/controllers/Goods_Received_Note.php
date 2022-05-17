<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Goods_Received_Note extends MY_Controller
{
  protected $module;
  protected $id_item=0;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['goods_received_note'];
    $this->load->model($this->module['model'], 'model');
    $this->load->helper($this->module['helper']);
    $this->load->library('upload');
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

    //redirect($this->module['route'] .'/create');
  }

  public function set_known_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['receipt']['known_by'] = $_GET['data'];
  }

  public function set_approved_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['receipt']['approved_by'] = $_GET['data'];
  }

  public function set_warehouse()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['receipt']['warehouse'] = $_GET['data'];
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
  public function ap_last()
  {
    echo $this->model->jrl_last_number();
  }
  public function search_purchase_order()
  {
    // if ($this->input->is_ajax_request() === FALSE)
    //   redirect($this->modules['secure']['route'] .'/denied');

    $category = $_SESSION['receipt']['category'];
    $vendor   = (empty($_SESSION['receipt']['received_from'])) ? NULL : $_SESSION['receipt']['received_from'];
    $entities = $this->model->searchPurchaseOrder($category, $vendor);

    foreach ($entities as $key => $value){
      $entities[$key]['label'] = $value['description'];
      $entities[$key]['label'] .= ' || PN: ';
      $entities[$key]['label'] .= $value['part_number'];
      $entities[$key]['label'] .= '<small>';
      $entities[$key]['label'] .= ($value['serial_number'] !== "") ? "SN: ". $value['serial_number'] ." || " : "";
      $entities[$key]['label'] .= 'Order Number: '. $value['document_number'] .' || ';
      $entities[$key]['label'] .= 'Consignor: '. $value['vendor'] .' || ';
      $entities[$key]['label'] .= 'Quantity: <code>'. number_format($value['left_received_quantity']) .'</code>';
      $entities[$key]['label'] .= '</small>';

      if ($value['default_currency'] == 'IDR'){
        $entities[$key]['unit_value'] = $value['unit_price'];
      } else {
        $entities[$key]['unit_value'] = $value['unit_price'] * $value['exchange_rate'];
      }
    }

    echo json_encode($entities);
  }

  public function search_items_by_serial()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $category = $_SESSION['receipt']['category'];
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

    $category = $_SESSION['receipt']['category'];
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
      $quantity     = array();
      $unit_value   = array();
      $total_value  = array();

      foreach ($entities as $row){
        $no++;
        $col = array();
        $col[]  = print_number($no);
        $col[]  = print_string($row['document_number']);
        $col[]  = print_date($row['received_date']);
        // $col[]  = print_string($row['received_date']);
        $col[]  = print_string($row['category']);
        // $col[]  = print_string($row['group']);
        $col[]  = print_string($row['warehouse']);
        // $col[]  = print_string($row['stores']);
        $col[]  = print_string($row['description']);
        $col[]  = print_string($row['item_id']);
        $col[]  = print_string($row['part_number']);
        $col[]  = print_string($row['alternate_part_number']);
        $col[]  = print_string($row['serial_number']);
        // $col[]  = print_string($row['minimum_quantity']);
        $col[]  = print_string($row['condition']);
        $col[]  = print_string($row['received_quantity']);
        $col[]  = print_string($row['unit']);
        $col[]  = print_string($row['coa']);
        $col[]  = print_string($row['kode_stok']);
        $col[]  = print_string($row['purchase_order_number']);
        $col[]  = print_string($row['awb_number']);
        $col[]  = print_string($row['reference_number']);
        $col[]  = print_date($row['tgl_nota']);
        $col[]  = print_string($row['remarks']);
        $col[]  = print_string($row['received_from']);
        $col[]  = print_string($row['received_by']);

        if (config_item('auth_role') != 'PIC STOCK'){
          $col[]  = print_number($row['received_unit_value'],2);
          $col[]  = print_number($row['received_unit_value']*$row['received_quantity'], 2);

          $unit_value[]   = $row['received_unit_value'];
          $total_value[]  = $row['received_unit_value']*$row['received_quantity'];
        }
        if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'SUPER ADMIN'){
			      if($row['kurs_dollar']==1){
      				$col[]  = print_number(0,2);
      			}else{
      				$col[]  = print_number($row['unit_value_dollar'],2);
      			}
      			$col[]  = print_number($row['kurs_dollar'],2);
        }

        $quantity[] = $row['received_quantity'];

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
        "draw"            => $_POST['draw'],
        "recordsTotal"    => $this->model->countIndex(),
        "recordsFiltered" => $this->model->countIndexFiltered(),
        "data"            => $data,
        "total"           => array(
          11 => print_number(array_sum($quantity), 2),
        )
      );

      if (config_item('auth_role') != 'PIC STOCK'){
        // $result['total'][17] = print_number(array_sum($unit_value), 2);
        $result['total'][21] = print_number(array_sum($total_value), 2);
      }
    }

    echo json_encode($result);
  }

  public function index()
  {
    $this->authorized($this->module, 'index');
    unset($_SESSION['receipt']['id']);

    $this->data['page']['title']            = $this->module['label'];
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source');
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array( 11 );

    if (config_item('auth_role') != 'PIC STOCK'){
      // $this->data['grid']['summary_columns'][] = 19;
      $this->data['grid']['summary_columns'][] = 21;
    }

    // $this->data['grid']['order_columns']    = array();
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
      11   => array( 0 => 12,  1 => 'asc' ),
      12   => array( 0 => 13,  1 => 'asc' ),
      13   => array( 0 => 14,  1 => 'asc' ),
      14   => array( 0 => 15,  1 => 'asc' ),
      15   => array( 0 => 16,  1 => 'asc' ),
      16   => array( 0 => 17,  1 => 'asc' ),
      17   => array( 0 => 18,  1 => 'asc' ),
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

    if (isset($_SESSION['receipt']) === FALSE){
      $_SESSION['receipt']                     = $entity;
      $_SESSION['receipt']['id']               = $id;
      $_SESSION['receipt']['edit']             = $entity['document_number'];
      $_SESSION['receipt']['document_number']  = $document_number;
    }

    redirect($this->module['route'] .'/create');
    //$this->render_view($this->module['view'] .'/edit');
  }

  public function create($category = NULL)
  {
    $this->authorized($this->module, 'document');

    if ($category !== NULL){
      $category = urldecode($category);

      $_SESSION['receipt']['items']            = array();
      $_SESSION['receipt']['category']         = $category;
      $_SESSION['receipt']['document_number']  = receipt_last_number();
      $_SESSION['receipt']['received_date']    = date('Y-m-d');
      $_SESSION['receipt']['received_by']      = config_item('auth_person_name');
      $_SESSION['receipt']['received_from']    = NULL;
      $_SESSION['receipt']['known_by']         = NULL;
      $_SESSION['receipt']['approved_by']      = NULL;
      $_SESSION['receipt']['warehouse']        = config_item('auth_warehouse');
      $_SESSION['receipt']['notes']            = NULL;

      redirect($this->module['route'] .'/create');
    }

    if (!isset($_SESSION['receipt']))
      redirect($this->module['route']);

    $this->data['page']['content']    = $this->module['view'] .'/create';
    $this->data['page']['offcanvas']  = $this->module['view'] .'/create_offcanvas_add_item';

    $this->render_view($this->module['view'] .'/create');
  }

  public function save()
  {
    // if ($this->input->is_ajax_request() == FALSE)
    //   redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') == FALSE){
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      if (!isset($_SESSION['receipt']['items']) || empty($_SESSION['receipt']['items'])){
        $data['success'] = FALSE;
        $data['message'] = 'Please add at least 1 item!';
      } else {
        $document_number = $_SESSION['receipt']['document_number'] . receipt_format_number();

        $errors = array();

        if (isset($_SESSION['receipt']['edit'])){
          if ($_SESSION['receipt']['edit'] != $document_number && $this->model->isDocumentNumberExists($document_number)){
            $errors[] = 'Duplicate Document Number: '. $_SESSION['receipt']['document_number'] .' !';
          }
        } else {
          if ($this->model->isDocumentNumberExists($document_number)){
            $errors[] = 'Duplicate Document Number: '. $_SESSION['receipt']['document_number'] .' !';
          }
        }

        foreach ($_SESSION['receipt']['items'] as $key => $item) {
          $part_number    = (empty($item['part_number'])) ? NULL : $item['part_number'];
          $serial_number  = (empty($item['serial_number'])) ? NULL : $item['serial_number'];
          $description    = (empty($item['description'])) ? NULL : $item['description'];
          $condition      = (empty($item['condition'])) ? 'SERVICEABLE' : $item['condition'];

          if (isStoresExists($item['stores']) && isStoresExists($item['stores'], $_SESSION['receipt']['category']) === FALSE){
            $errors[] = 'Stores '. $item['stores'] .' exists for other inventory! Please change the stores.';
          }

          if ($serial_number !== NULL && isItemExists($part_number, $description, $serial_number)){
            $item_id = getItemId($part_number, $description, $serial_number);

            if (!isset($_SESSION['receipt']['edit']) && getStockQuantity($item_id, $condition) > 0){
              $errors[] = 'Item with Serial number '. $serial_number .' still contains quantity.';
            }
          }
          //cek kurs
          // if (!isset($_SESSION['receipt']['edit'])){
          //   if($item['kurs']=='dollar' || $item['kurs']=='rupiah'){
          //     $today=date('Y-m-d');
              // if($this->model->isKursDateExists($_SESSION['receipt']['received_date']) === FALSE){
          //       $errors[] = 'Kurs Hari ini Belum diinput !! ';
          //     }
          //   }
          // }          
          
        }

        if (!empty($errors)){
          $data['success'] = FALSE;
          $data['message'] = implode('<br />', $errors);
        } else {
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
    }

    echo json_encode($data);
  }

  public function add_item()
  {
    $this->authorized($this->module, 'document');

    if (isset($_POST) && !empty($_POST)){
      $_SESSION['receipt']['items'][] = array(
        //'id'                      => $id_item++,
        'group'                   => $this->input->post('group'),
        'description'             => trim(strtoupper($this->input->post('description'))),
        'part_number'             => trim(strtoupper($this->input->post('part_number'))),
        'alternate_part_number'   => trim(strtoupper($this->input->post('alternate_part_number'))),
        'serial_number'           => trim(strtoupper($this->input->post('serial_number'))),
        'received_quantity'       => $this->input->post('received_quantity'),
        'received_unit_value'     => $this->input->post('received_unit_value'),
        'minimum_quantity'        => $this->input->post('minimum_quantity'),
        'condition'               => $this->input->post('condition'),
        'expired_date'            => $this->input->post('expired_date'),
        'stores'                  => trim(strtoupper($this->input->post('stores'))),
        'purchase_order_number'   => trim(strtoupper($this->input->post('purchase_order_number'))),
        'purchase_order_item_id'  => trim($this->input->post('purchase_order_item_id')),
        'reference_number'        => trim(strtoupper($this->input->post('reference_number'))),
        'awb_number'              => trim(strtoupper($this->input->post('awb_number'))),
        'unit'                    => trim($this->input->post('unit')),
        'received_unit'                    => trim($this->input->post('received_unit')),
        'remarks'                 => trim($this->input->post('remarks')),
        'kode_stok'               => trim($this->input->post('kode_stok')),
        'kurs'                    => trim($this->input->post('kurs')),
        'unit_pakai'              => trim($this->input->post('unit_pakai')),
        'isi'                     => trim($this->input->post('isi')),
        'quantity_order'          => $this->input->post('quantity_order'),
        'value_order'             => $this->input->post('value_order'),
        'no_expired_date'         => $this->input->post('no_expired_date'),
        'tgl_nota'                => $this->input->post('tgl_nota'),

      );

      if (empty($_SESSION['receipt']['received_from'])){
        $_SESSION['receipt']['received_from'] = strtoupper($this->input->post('consignor'));
      }
    }

    redirect($this->module['route'] .'/create');
  }

  public function discard()
  {
    $this->authorized($this->module['permission']['document']);

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

  //tambahan

  public function ajax_editItem($key)
  {
    $this->authorized($this->module, 'document');    

    $entity = $_SESSION['receipt']['items'][$key];

    echo json_encode($entity);
  }

  public function edit_item()
  {
    $this->authorized($this->module, 'document');

    $key=$this->input->post('item_id');
    if (isset($_POST) && !empty($_POST)){
      //$receipts_items_id = $this->input->post('item_id')
      $_SESSION['receipt']['items'][$key] = array(        
        'group'                   => $this->input->post('group'),
        'description'             => trim(strtoupper($this->input->post('description'))),
        'part_number'             => trim(strtoupper($this->input->post('part_number'))),
        'alternate_part_number'   => trim(strtoupper($this->input->post('alternate_part_number'))),
        'serial_number'           => trim(strtoupper($this->input->post('serial_number'))),
        'received_quantity'       => $this->input->post('received_quantity'),
        'received_unit_value'     => $this->input->post('received_unit_value'),
        'minimum_quantity'        => $this->input->post('minimum_quantity'),
        'condition'               => $this->input->post('condition'),
        'expired_date'            => $this->input->post('edit_expired_date'),
        'stores'                  => trim(strtoupper($this->input->post('stores'))),
        'purchase_order_number'   => trim(strtoupper($this->input->post('purchase_order_number'))),
        'purchase_order_item_id'  => trim($this->input->post('purchase_order_item_id')),
        'reference_number'        => trim(strtoupper($this->input->post('reference_number'))),
        'awb_number'              => trim(strtoupper($this->input->post('awb_number'))),
        'unit'                    => trim($this->input->post('unit')),
        'received_unit'           => trim($this->input->post('received_unit')),
        'remarks'                 => trim($this->input->post('remarks')),
        'kode_stok'               => trim($this->input->post('edit_kode_stok')),
        'kurs'                    => trim($this->input->post('edit_kurs')),        
        'unit_pakai'              => trim($this->input->post('unit_pakai')), 
        'isi'                     => trim($this->input->post('isi')),
        'quantity_order'          => $this->input->post('quantity_order'),
        'value_order'             => $this->input->post('value_order'),
        'no_expired_date'         => $this->input->post('edit_no_expired_date'),
        'stock_in_stores_id'      => trim($this->input->post('stock_in_store_id')),
        'receipt_items_id'        => trim($this->input->post('receipt_items_id')),
        'tgl_nota'                => $this->input->post('tgl_nota'),

      );
    }
    redirect($this->module['route'] .'/create');

  }

  public function save_edit(){
    unset($_SESSION['receipt']);

    $data['success'] = TRUE;
    $data['message'] = 'Document has been saved. You will redirected now.';
    echo json_encode($data);

  }

  //tambahan

  public function import()
  {
    $this->authorized($this->module, 'import');

    $this->load->library('form_validation');

    if (isset($_POST) && !empty($_POST)){
      $this->form_validation->set_rules('delimiter', 'Value Delimiter', 'trim|required');

      if ($this->form_validation->run() === TRUE){
        $file       = $_FILES['userfile']['tmp_name'];
        $delimiter  = $this->input->post('delimiter');

        if (($handle = fopen($file, "r")) !== FALSE){
          $row     = 1;
          $data    = array();
          $errors  = array();
          $user_id = array();
          $index   = 0;
          fgetcsv($handle); // skip first line (as header)

          //... parsing line
          while (($col = fgetcsv($handle, 1024, $delimiter)) !== FALSE)
          {
            $row++;

            $category        = trim(strtoupper($col[0]));
            $part_number            = trim(strtoupper($col[1]));
            $serial_number               = trim(strtoupper($col[2]));
            $condition              = trim(strtoupper($col[3]));
            $unit            = trim(strtoupper($col[4]));
            $description            = trim(strtoupper($col[5]));
            $alternate_part_number          = trim(strtoupper($col[6]));
            $group              = trim(strtoupper($col[7]));
            $minimum_quantity               = trim(strtoupper($col[8]));
            $stores                   = trim(strtoupper($col[9]));
            $warehouse                = trim(strtoupper($col[10]));
            $document_number              = trim(strtoupper($col[11]));
            $expired_date              = trim(strtoupper($col[12]));
            $vendor            = trim(strtoupper($col[13]));
            $remarks  = trim(strtoupper($col[14]));
            $received_date                  = trim(strtoupper($col[15]));
            $received_by                 = trim(strtoupper($col[16]));
            $received_quantity     = trim(strtoupper($col[17]));
            $received_unit_value                  = trim(strtoupper($col[18]));
            $purchase_order_number            = trim(strtoupper($col[19]));
            $reference_number            = trim(strtoupper($col[20]));
            $awb_number            = trim(strtoupper($col[21]));


            if ($document_number == NULL)
              $errors[] = 'Line '. $row .': Document Number is empty!';
            
            

            if ($received_date == NULL)
              $errors[] = 'Line '. $row .': Received Date is empty!';

            if ($warehouse == NULL)
              $errors[] = 'Line '. $row .': Base is empty!';

            if (isWarehouseExists($warehouse) == FALSE)
              $errors[] = 'Line '. $row .': Base '.$warehouse.'not found!';

            if ($category == NULL)
              $errors[] = 'Line '. $row .': Category is empty!';

            if (isItemCategoryExists($category) == FALSE)
              $errors[] = 'Line '. $row .': Category not found!';

            if ($part_number == NULL){
              $errors[] = 'Line '. $row .': Part Number is empty!';
            } 

            if ($stores == NULL){
              $errors[] = 'Line '. $row .': Issued Stores is empty!';
            } else {
              if (isStoresExists($stores, $category) === FALSE){
                $errors[] = 'Line '. $row .$stores.': Stores not found!';
              }
            }

            $data[$row]['category']                    = $category;
            $data[$row]['part_number']                 = $part_number;
            $data[$row]['serial_number']               = $serial_number;
            $data[$row]['condition']                   = $condition;
            $data[$row]['unit']                        = $unit;
            $data[$row]['description']                 = $description;
            $data[$row]['alternate_part_number']       = $alternate_part_number;
            $data[$row]['group']                       = $group;
            $data[$row]['minimum_quantity']            = $minimum_quantity;
            $data[$row]['stores']                      = $stores;
            $data[$row]['warehouse']                   = $warehouse;
            $data[$row]['document_number']             = $document_number;
            $data[$row]['expired_date']                = $expired_date;
            $data[$row]['vendor']                      = $vendor;
            $data[$row]['remarks']                     = $remarks;
            $data[$row]['received_date']               = $received_date;
            $data[$row]['received_by']                 = $received_by;
            $data[$row]['received_quantity']           = $received_quantity;
            $data[$row]['received_unit_value']         = $received_unit_value;
            $data[$row]['purchase_order_number']       = $purchase_order_number;
            $data[$row]['reference_number']            = $reference_number;
            $data[$row]['awb_number']                  = $awb_number;

          }
          fclose($handle);

          if (empty($errors)){
            /**
             * Insert into user table
             */
            if ($this->model->import($data)){
              //... send message to view
              $this->session->set_flashdata('alert', array(
                'type' => 'success',
                'info' => count($data)." data has been imported!"
              ));

              redirect($this->module['route']);
            }
          } else {
            foreach ($errors as $key => $value){
              $err[] = "\n#". $value;
            }

            $this->session->set_flashdata('alert', array(
              'type' => 'danger',
              'info' => "There are errors on data\n#". implode("\n#", $errors)
            ));
          }
        } else {
          $this->session->set_flashdata('alert', array(
            'type' => 'danger',
            'info' => 'Cannot open file!'
          ));
        }
      }
    }

    redirect($this->module['route']);
  }

  public function manage_attachment($id)
  {
    $this->authorized($this->module, 'index');

    $this->data['manage_attachment'] = $this->model->listAttachment($id);
    $this->data['id'] = $id;
    $this->data['page']['title']            = "Manage Attachment Goods Received Notes";
    $this->render_view($this->module['view'] . '/manage_attachment');
  }

  public function add_attachment_to_db($id)
  {
        $result["status"] = 0;
        $date = new DateTime();
        // $config['file_name'] = $date->getTimestamp().random_string('alnum', 5);
        // $cost_center = getCostCenterByIdRequest($id,'expense');
        $config['upload_path'] = 'attachment/good_received_notes/';
        $config['allowed_types'] = 'jpg|png|jpeg|doc|docx|xls|xlsx|pdf';
        $config['max_size']  = 2000;

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('attachment')) {
          $error = array('error' => $this->upload->display_errors());
        } else {
          $data = array('upload_data' => $this->upload->data());
          $url = $config['upload_path'] . $data['upload_data']['orig_name'];
          // array_push($_SESSION["poe"]["attachment"], $url);
          $this->model->add_attachment_to_db($id, $url);
          $result["status"] = 1;
        }
        echo json_encode($result);
    }

  
}
