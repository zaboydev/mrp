<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Material_Slip extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['material_slip'];
    $this->load->model($this->module['model'], 'model');
    $this->load->helper($this->module['helper']);
    $this->data['module'] = $this->module;
  }

  public function set_doc_number()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (empty($_GET['data'])){
      $number = usage_last_number();
    } else {
      $number = $_GET['data'];
    }

    $_SESSION['usage']['document_number'] = $number;
  }

  public function set_warehouse()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['usage']['warehouse'] = $_GET['data'];
  }

  public function set_issued_date()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['usage']['issued_date'] = $_GET['data'];
  }

  public function set_issued_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['usage']['issued_by'] = $_GET['data'];
  }

  public function set_issued_to()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['usage']['issued_to'] = $_GET['data'];
  }

  public function set_sent_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['usage']['sent_by'] = $_GET['data'];
  }

  public function set_known_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['usage']['known_by'] = $_GET['data'];
  }

  public function set_approved_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['usage']['approved_by'] = $_GET['data'];
  }

  public function set_required_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['usage']['required_by'] = $_GET['data'];
  }

  public function set_requisition_reference()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['usage']['requisition_reference'] = $_GET['data'];
  }

  public function set_notes()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['usage']['notes'] = $_GET['data'];
  }

  public function del_item($key)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (isset($_SESSION['usage']['items']))
      unset($_SESSION['usage']['items'][$key]);
  }

  public function search_stock_in_stores()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $category = $_SESSION['usage']['category'];
    $entities = $this->model->searchStockInStores($category);

    foreach ($entities as $key => $value){
      $entities[$key]['label'] = $value['description'];
      $entities[$key]['label'] .= ' || PN: ';
      $entities[$key]['label'] .= $value['part_number'];
      $entities[$key]['label'] .= ' || ';
      $entities[$key]['label'] .= $value['condition'];
      $entities[$key]['label'] .= '<small>';
      $entities[$key]['label'] .= ($value['serial_number'] !== "") ? "SN: ". $value['serial_number'] ." || " : "";
      $entities[$key]['label'] .= 'Stores: '. $value['stores'] .' || ';
      // $entities[$key]['label'] .= 'Received date: '. date('d/m/Y', strtotime($value['received_date'])) .' || ';
      // $entities[$key]['label'] .= 'Expired date: '. date('d/m/Y', strtotime($value['expired_date'])) .' || ';
      $entities[$key]['label'] .= 'Quantity: <code>'. number_format($value['quantity']) .' </code>';
      $entities[$key]['label'] .= ' || Unit: '.$value['unit'] .'';
      $entities[$key]['label'] .= '</small>';
    }

    echo json_encode($entities);
  }

  public function stores($stock_id)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $category = $_SESSION['usage']['category'];
    $entities = $this->model->stores($stock_id);

    foreach ($entities as $key => $value){
      $entities[$key]['label'] = $value['description'];
      $entities[$key]['label'] .= ' || PN: ';
      $entities[$key]['label'] .= $value['part_number'];
      $entities[$key]['label'] .= ' || ';
      $entities[$key]['label'] .= $value['condition'];
      $entities[$key]['label'] .= '<small>';
      $entities[$key]['label'] .= ($value['serial_number'] !== "") ? "SN: ". $value['serial_number'] ." || " : "";
      $entities[$key]['label'] .= 'Stores: '. $value['warehouse'] .' || ';
      // $entities[$key]['label'] .= 'Received date: '. date('d/m/Y', strtotime($value['received_date'])) .' || ';
      // $entities[$key]['label'] .= 'Expired date: '. date('d/m/Y', strtotime($value['expired_date'])) .' || ';
      $entities[$key]['label'] .= 'Quantity: <code>'. number_format($value['quantity']) .' </code>';
      $entities[$key]['label'] .= ' || Unit: '.$value['unit'] .'';
      $entities[$key]['label'] .= '</small>';
    }

    echo json_encode($entities);
  }

  // public function index_data_source()
  // {
  //   if ($this->input->is_ajax_request() === FALSE)
  //     redirect($this->modules['secure']['route'] .'/denied');

  //   if (is_granted($this->module, 'index') === FALSE){
  //     $return['type'] = 'danger';
  //     $return['info'] = "You don't have permission to access this page!";
  //   } else {
  //     $entities     = $this->model->getIndex();
  //     $data         = array();
  //     $no           = $_POST['start'];
  //     $quantity     = array();
   
  //     $unit_value   = array();
  //     $total_value  = array();

  //     foreach ($entities as $row){
  //       $no++;
  //       $col    = array();
  //       $col[]  = print_number($no);
  //       $col[]  = print_string($row['document_number']);
  //       $col[]  = print_date($row['issued_date']);
  //       $col[]  = print_string($row['category']);
  //       $col[]  = print_string($row['warehouse']);
  //       $col[]  = print_string($row['description']);
  //       $col[]  = print_string($row['part_number']);
  //       $col[]  = print_string($row['serial_number']);
  //       $col[]  = print_string($row['condition']);
  //       $col[]  = print_number($row['issued_quantity'], 2);
  //       $col[]  = print_string($row['unit']);
  //       $col[]  = print_string($row['coa']);
  //       $col[]  = print_string($row['kode_stok']);
  //       $col[]  = print_string($row['remarks']);
  //       $col[]  = print_string($row['issued_to']);
  //       $col[]  = print_string($row['issued_by']);
  //       $col[]  = print_string($row['required_by']);
  //       $col[]  = print_string($row['requisition_reference']);
  //       $col[]  = $row['notes'];

  //       if (config_item('auth_role') == 'SUPERVISOR'){
  //         $col[]  = print_string($row['stores'], 2);
  //         $col[]  = print_string($row['reference_document']);
  //       }

  //       if (config_item('auth_role') != 'PIC STOCK'){
  //         $col[]          = print_number($row['issued_unit_value'], 2);
  //         $col[]          = print_number($row['issued_total_value'], 2);

  //         $unit_value[]   = $row['issued_unit_value'];
  //         $total_value[]  = $row['issued_total_value'];
  //       }

  //       $col['DT_RowId'] = 'row_'. $row['id'];
  //       $col['DT_RowData']['pkey']  = $row['id'];

  //       if ($this->has_role($this->module, 'info')){
  //         $col['DT_RowAttr']['onClick']     = '$(this).popup();';
  //         $col['DT_RowAttr']['data-target'] = '#data-modal';
  //         $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] .'/info/'. $row['id']);
  //       }

  //       $quantity[] = $row['issued_quantity'];

  //       $data[] = $col;
  //     }

  //     $result = array(
  //       "draw"            => $_POST['draw'],
  //       "recordsTotal"    => $this->model->countIndex(),
  //       "recordsFiltered" => $this->model->countIndexFiltered(),
  //       "data"            => $data,
  //       "total"           => array(
  //         9 => print_number(array_sum($quantity), 2),
  //       )
  //     );

  //     if (config_item('auth_role') != 'PIC STOCK'){
  //       if (config_item('auth_role') == 'SUPERVISOR'){
  //         $result['total'][21] = print_number(array_sum($unit_value), 2);
  //         $result['total'][22] = print_number(array_sum($total_value), 2);
  //       } else {
  //         $result['total'][19] = print_number(array_sum($unit_value), 2);
  //         $result['total'][20] = print_number(array_sum($total_value), 2);
  //       }
  //     }
  //   }

  //   echo json_encode($result);
  // }
  //edit

  public function index_data_source()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'index') === FALSE){
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {

      $entities     = $this->model->getIndex();
      $data         = array();
      $no           = $_POST['start'];
      $quantity     = array();
   
      $unit_value   = array();
      $total_value  = array();

      foreach ($entities as $row){
        $no++;
        $col    = array();
        $col[]  = print_number($no);
        $col[]  = print_string($row['document_number']);
        // $col[]  = print_date($row['issued_date']);
        if (config_item('auth_role') == 'FINANCE'){
          $col[]  = date('d/m/Y', strtotime($row['issued_date']));
        }else{
          $col[]  = print_date($row['issued_date']);
        }
        $col[]  = print_string($row['category']);
        $col[]  = print_string($row['warehouse']);
        $col[]  = print_string($row['description']);
        $col[]  = print_string($row['item_id']);
        $col[]  = print_string($row['part_number']);
        $col[]  = print_string($row['serial_number']);
        $col[]  = print_string($row['condition']);
        $col[]  = print_number($row['issued_quantity'], 2);
        $col[]  = print_string($row['unit']);
        $col[]  = print_string($row['coa']);
        $col[]  = print_string($row['kode_stok']);
        $col[]  = print_string($row['remarks']);
        $col[]  = print_string($row['issued_to']);
        $col[]  = print_string($row['issued_by']);
        $col[]  = print_string($row['required_by']);
        $col[]  = print_string($row['requisition_reference']);
        $col[]  = $row['notes'];

        if (config_item('auth_role') == 'SUPERVISOR' || config_item('auth_role') == 'SUPER ADMIN'){
          $col[]  = print_string($row['stores'], 2);
          $col[]  = print_string($row['reference_document']);
        }

        if (config_item('auth_role') != 'PIC STOCK'){
          $col[]          = print_number($row['issued_unit_value'], 2);
          $col[]          = print_number($row['issued_total_value'], 2);
          // $col[]          = print_string($row['issued_unit_value']);
          // $col[]          = print_string($row['issued_total_value']);


          $unit_value[]   = $row['issued_unit_value'];
          $total_value[]  = $row['issued_total_value'];
        }
    		if (config_item('auth_role') == 'FINANCE'){
    			$col[]  = print_string($row['kode_pemakaian']);
    		}

        $col['DT_RowId'] = 'row_'. $row['id'];
        $col['DT_RowData']['pkey']  = $row['id'];

        if ($this->has_role($this->module, 'info')){
          $col['DT_RowAttr']['onClick']     = '$(this).popup();';
          $col['DT_RowAttr']['data-target'] = '#data-modal';
          $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] .'/info/'. $row['id']);
        }

        $quantity[] = $row['issued_quantity'];

        $data[] = $col;
      }

      $result = array(
        "draw"            => $_POST['draw'],
        "recordsTotal"    => $this->model->countIndex(),
        "recordsFiltered" => $this->model->countIndexFiltered(),
        "data"            => $data,
        "total"           => array(
          10 => print_number(array_sum($quantity), 2),
        )
      );

      if (config_item('auth_role') != 'PIC STOCK' || config_item('auth_role') == 'SUPER ADMIN'){
        if (config_item('auth_role') == 'SUPERVISOR'){
          $result['total'][22] = print_number(array_sum($unit_value), 2);
          $result['total'][23] = print_number(array_sum($total_value), 2);
        } else {
          $result['total'][20] = print_number(array_sum($unit_value), 2);
          $result['total'][21] = print_number(array_sum($total_value), 2);
        }
      }
    }

    echo json_encode($result);
  }

  // public function index()
  // {
  //   $this->authorized($this->module, 'index');

  //   $this->data['page']['title']            = $this->module['label'];
  //   $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
  //   $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source');
  //   $this->data['grid']['fixed_columns']    = 2;
  //   $this->data['grid']['summary_columns']  = array( 9 );

  //   if (config_item('auth_role') != 'PIC STOCK'){
  //     if (config_item('auth_role') == 'SUPERVISOR'){
  //       $this->data['grid']['summary_columns'][] = 21;
  //       $this->data['grid']['summary_columns'][] = 22;
  //     } else {
  //       $this->data['grid']['summary_columns'][] = 19;
  //       $this->data['grid']['summary_columns'][] = 20;
  //     }
  //   }

  //   $this->data['grid']['order_columns']    = array();

  //   $this->render_view($this->module['view'] .'/index');
  // }
  //edit

  public function index()
  {
    $this->authorized($this->module, 'index');

    $this->data['page']['title']            = $this->module['label'];
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source/');
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array( 10 );

    if (config_item('auth_role') != 'PIC STOCK'){
      if (config_item('auth_role') == 'SUPERVISOR'){
        $this->data['grid']['summary_columns'][] = 22;
        $this->data['grid']['summary_columns'][] = 23;
      } else {
        $this->data['grid']['summary_columns'][] = 20;
        $this->data['grid']['summary_columns'][] = 21;
      }
    }

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

    $pdf = $this->m_pdf->load(null, 'A4');
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

    if (isset($_SESSION['usage']) === FALSE){
      $_SESSION['usage']                     = $entity;
      $_SESSION['usage']['id']               = $id;
      $_SESSION['usage']['edit']             = $entity['document_number'];
      $_SESSION['usage']['document_number']  = $document_number;
    }

    redirect($this->module['route'] .'/create');
  }

  public function create($category = NULL)
  {
    $this->authorized($this->module, 'document');

    if ($category !== NULL){
      $category = urldecode($category);

      $_SESSION['usage']['items']                 = array();
      $_SESSION['usage']['category']              = $category;
      $_SESSION['usage']['document_number']       = usage_last_number();
      $_SESSION['usage']['issued_date']           = date('Y-m-d');
      $_SESSION['usage']['issued_by']             = config_item('auth_person_name');
      $_SESSION['usage']['issued_to']             = 'OTHER';
      $_SESSION['usage']['sent_by']               = NULL;
      $_SESSION['usage']['required_by']           = NULL;
      $_SESSION['usage']['requisition_reference'] = NULL;
      $_SESSION['usage']['approved_by']           = NULL;
      $_SESSION['usage']['warehouse']             = config_item('auth_warehouse');
      $_SESSION['usage']['notes']                 = NULL;

      redirect($this->module['route'] .'/create');
    }

    if (!isset($_SESSION['usage']))
      redirect($this->module['route']);

    $this->data['page']['content']    = $this->module['view'] .'/create';

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
      if (!isset($_SESSION['usage']['items']) || empty($_SESSION['usage']['items'])){
        $data['success'] = FALSE;
        $data['message'] = 'Please add at least 1 item!';
      } else {
        $document_number = $_SESSION['usage']['document_number'] . usage_format_number();

        $errors = array();

        if (isset($_SESSION['usage']['edit'])){
          if ($_SESSION['usage']['edit'] != $document_number && $this->model->isDocumentNumberExists($document_number)){
            $errors[] = 'Duplicate Document Number: '. $_SESSION['usage']['document_number'] .' !';
          }
        } else {
          if ($this->model->isDocumentNumberExists($document_number)){
            $errors[] = 'Duplicate Document Number: '. $_SESSION['usage']['document_number'] .' !';
          }
        }

        foreach ($_SESSION['usage']['items'] as $key => $item) {
          $part_number    = (empty($item['part_number'])) ? NULL : $item['part_number'];
          $description    = (empty($item['description'])) ? NULL : $item['description'];
          $serial_number  = (empty($item['serial_number'])) ? NULL : $item['serial_number'];
          $condition      = (empty($item['condition'])) ? 'SERVICEABLE' : $item['condition'];

          // if (isStoresExists($item['stores']) && isStoresExists($item['stores'], $_SESSION['usage']['category']) === FALSE){
          //   $errors[] = 'Stores '. $item['stores'] .' exists for other inventory! Please change the stores.';
          // }

          if (isItemExists($part_number,$description, $serial_number) && $serial_number !== NULL){
            $item_id = getItemId($part_number, $description, $serial_number);

            if (isset($_SESSION['usage']['edit'])){
              if (getStockQuantity($item_id, $description, $condition) > 0){
                $errors[] = 'Serial number '. $item['serial_number'] .' have quantity in stores '. $serial->stores .'/'. $serial->warehouse .'. Please recheck your document.';
              }
            } else {
              if (getStockQuantity($item_id, $condition) == 0){
                $errors[] = 'Serial number '. $item['serial_number'] .' no quantity in stores '. $serial->stores .'/'. $serial->warehouse .'. Please recheck your document.';
              }
            }
          }
        }

        $today    = date('Y-m-d');
        $date     = strtotime('-2 day',strtotime($today));
        $min_date = date('Y-m-d',$date);

        // if($_SESSION['usage']['issued_date'] < $min_date){
        //   $errors[] = 'Issued Date is too Old. Minimal Date is '.$date.' !';
        // }

        if (!empty($errors)){
          $data['success'] = FALSE;
          $data['message'] = implode('<br />', $errors);
        } else {
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
    }

    echo json_encode($data);
  }

  public function add_item()
  {
    $this->authorized($this->module, 'document');

    if (isset($_POST) && !empty($_POST)){
      $_SESSION['usage']['items'][] = array(
        'stock_id'      => $this->input->post('stock_in_stores_id'),
        'group'                   => $this->input->post('group'),
        'description'             => trim(strtoupper($this->input->post('description'))),
        'part_number'             => trim(strtoupper($this->input->post('part_number'))),
        'alternate_part_number'   => trim(strtoupper($this->input->post('alternate_part_number'))),
        'serial_number'           => trim(strtoupper($this->input->post('serial_number'))),
        'issued_quantity'         => $this->input->post('issued_quantity'),
        'issued_unit_value'       => $this->input->post('issued_unit_value'),
        'maximum_quantity'        => $this->input->post('maximum_quantity'),
        'condition'               => $this->input->post('condition'),
        'stores'                  => (strtoupper($this->input->post('stores'))),
        'unit'                    => trim($this->input->post('unit')),
        'remarks'                 => trim($this->input->post('remarks')),
        'unit_pakai'              => trim($this->input->post('unit_pakai')),
        'qty_konvers'              => trim($this->input->post('qty_konvers')),
      );
    }

    redirect($this->module['route'] .'/create');
  }

  public function discard()
  {
    $this->authorized($this->module['permission']['create']);

    unset($_SESSION['usage']);

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

            $document_number        = trim(strtoupper($col[0]));
            $issued_date            = trim(strtoupper($col[1]));
            $category               = trim(strtoupper($col[2]));
            $warehouse              = trim(strtoupper($col[3]));
            $description            = trim(strtoupper($col[4]));
            $part_number            = trim(strtoupper($col[5]));
            $serial_number          = trim(strtoupper($col[6]));
            $condition              = trim(strtoupper($col[7]));
            $quantity               = trim(strtoupper($col[8]));
            $unit                   = trim(strtoupper($col[9]));
            $remarks                = trim(strtoupper($col[10]));
            $issued_to              = trim(strtoupper($col[11]));
            $issued_by              = trim(strtoupper($col[12]));
            $required_by            = trim(strtoupper($col[13]));
            $requisition_reference  = trim(strtoupper($col[14]));
            $notes                  = trim(strtoupper($col[15]));
            $stores                 = trim(strtoupper($col[16]));
            $reference_document     = trim(strtoupper($col[17]));
            $value                  = trim(strtoupper($col[18]));
            $total_value            = trim(strtoupper($col[19]));

            if ($document_number == NULL)
              $errors[] = 'Line '. $row .': Document Number is empty!';

            if ($issued_date == NULL)
              $errors[] = 'Line '. $row .': Issued Date is empty!';

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
            } else {
              if($serial_number == NULL){
                if (isItemExists($part_number,$description) == FALSE){
                $errors[] = 'Line '. $part_number .$serial_number.': Item not found!';
                } else {
                  $item_id  = getItemId($part_number,$description);
                  if($reference_document==NULL){
                    if (isStockInStoresExists($item_id, $stores, $condition) == FALSE){
                      $errors[] = 'Line '. $row .': Stock '.$item_id.' '.$stores.' '.$condition.' '.$reference_document.' not found!';
                    }
                  }else{
                  if (isStockInStoresExists($item_id, $stores, $condition,$reference_document) == FALSE){
                      $errors[] = 'Line '. $row .': Stock '.$item_id.' '.$stores.' '.$condition.' '.$reference_document.' not found!';
                    }
                  }
                  
                }
              }else{
                if (isItemExists($part_number,$description,$serial_number) == FALSE){
                $errors[] = 'Line '. $part_number .$serial_number.': Item not found!';
                } else {
                  $item_id  = getItemId($part_number,$description, $serial_number);
                  if($reference_document==NULL){
                    if (isStockInStoresExists($item_id, $stores, $condition) == FALSE){
                      $errors[] = 'Line '. $row .': Stock '.$item_id.' '.$stores.' '.$condition.' '.$reference_document.' not found!';
                    }
                  }else{
                  if (isStockInStoresExists($item_id, $stores, $condition,$reference_document) == FALSE){
                      $errors[] = 'Line '. $row .': Stock '.$item_id.' '.$stores.' '.$condition.' '.$reference_document.' not found!';
                    }
                  }

                  
                }
              }
              
            }

            if ($stores == NULL){
              $errors[] = 'Line '. $row .': Issued Stores is empty!';
            } else {
              if (isStoresExists($stores, $category) === FALSE){
                $errors[] = 'Line '. $row .': Stores not found!';
              }
            }

            $data[$row]['document_number']        = $document_number;
            $data[$row]['issued_date']            = $issued_date;
            $data[$row]['category']               = $category;
            $data[$row]['warehouse']              = $warehouse;
            $data[$row]['description']            = $description;
            $data[$row]['part_number']            = $part_number;
            $data[$row]['serial_number']          = $serial_number;
            $data[$row]['condition']              = $condition;
            $data[$row]['quantity']               = $quantity;
            $data[$row]['unit']                   = $unit;
            $data[$row]['remarks']                = $remarks;
            $data[$row]['issued_to']              = $issued_to;
            $data[$row]['issued_by']              = $issued_by;
            $data[$row]['required_by']            = $required_by;
            $data[$row]['requisition_reference']  = $requisition_reference;
            $data[$row]['notes']                  = $notes;
            $data[$row]['stores']                 = $stores;
            $data[$row]['reference_document']     = $reference_document;
            $data[$row]['value']                  = $value;
            $data[$row]['total_value']            = $total_value;
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

  //tambahan
  public function ajax_editItem($key)
  {
    $this->authorized($this->module, 'document');    

    $entity = $_SESSION['usage']['items'][$key];

    echo json_encode($entity);
  }

  public function edit_item()
  {
    $this->authorized($this->module, 'document');

    $key=$this->input->post('edit_item_id');
    if (isset($_POST) && !empty($_POST)){
      //$receipts_items_id = $this->input->post('item_id')
      $_SESSION['usage']['items'][$key] = array(        
        'stock_id'      => $this->input->post('edit_stock_in_stores_id'),
        'group'                   => $this->input->post('edit_group'),
        'description'             => trim(strtoupper($this->input->post('edit_description'))),
        'part_number'             => trim(strtoupper($this->input->post('edit_part_number'))),
        'alternate_part_number'   => trim(strtoupper($this->input->post('edit_alternate_part_number'))),
        'serial_number'           => trim(strtoupper($this->input->post('edit_serial_number'))),
        'issued_quantity'         => $this->input->post('edit_issued_quantity'),
        'issued_unit_value'       => $this->input->post('edit_issued_unit_value'),
        'maximum_quantity'        => $this->input->post('edit_maximum_quantity'),
        'condition'               => $this->input->post('edit_condition'),
        'stores'                  => (strtoupper($this->input->post('edit_stores'))),
        'unit'                    => trim($this->input->post('edit_unit')),
        'remarks'                 => trim($this->input->post('edit_remarks')),        
        'unit_pakai'              => trim($this->input->post('unit_pakai')),
      );
    }
    redirect($this->module['route'] .'/create');
  }

  public function select_item()
  {
    $this->authorized($this->module, 'document');

    $category = $_SESSION['usage']['category'];
    $entities = $this->model->searchStockInStores($category);

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
      if (isset($_POST['stock_in_stores_id']) && !empty($_POST['stock_in_stores_id'])) {
        $_SESSION['usage']['items'] = array();

        foreach ($_POST['stock_in_stores_id'] as $key => $stock_in_stores_id) {
          $stock_in_stores = $this->model->infoStockInStores($stock_in_stores_id);

          $_SESSION['usage']['items'][$stock_in_stores_id] = array(
            'stock_in_stores_id'      => $stock_in_stores['id'],
            'stock_id'                => $stock_in_stores['stock_id'],
            'group'                   => $stock_in_stores['group'],
            'description'             => $stock_in_stores['description'],
            'part_number'             => $stock_in_stores['part_number'],
            'alternate_part_number'   => $stock_in_stores['alternate_part_number'],
            'serial_number'           => $stock_in_stores['serial_number'],
            'issued_quantity'         => 0,
            'issued_unit_value'       => 0,
            'maximum_quantity'        => $stock_in_stores['quantity'],
            'condition'               => $stock_in_stores['condition'],
            'stores'                  => $stock_in_stores['stores'],
            'unit'                    => $stock_in_stores['unit'],
            'remarks'                 => null,
            'unit_pakai'              => $stock_in_stores['unit_pakai'],
            'qty_konvers'             => 1,
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

          $_SESSION['usage']['items'][$id]['issued_quantity']           = $item['issued_quantity'];
          $_SESSION['usage']['items'][$id]['remarks']                   = $item['remarks'];
        }

        $data['success'] = TRUE;
      } else {
        $data['success'] = FALSE;
        $data['message'] = 'No data to update!';
      }
    }

    echo json_encode($data);
  }

  //tambahan
}
