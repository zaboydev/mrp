<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Mixing extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['mixing'];
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function index()
  {
    $this->authorized($this->module, 'index');

    

    $jenis = $this->module['name'];
   
    $this->data['page']['requirement']      = array('datatable');
    $this->data['jenis']                    = $jenis;
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source/'. $jenis);

    
    $this->data['page']['title']            = $this->module['label'];
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array( 9 );
    if (config_item('auth_role') == 'SUPERVISOR' || config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'SUPER ADMIN' || config_item('auth_role') == 'VP FINANCE'){
      $this->data['grid']['summary_columns'][] = 19;
    }
    // $this->data['grid']['summary_columns']  = array( 7, 8, 9, 10, 11 );
    $this->data['grid']['order_columns']    = array (
      0 => array ( 0 => 4, 1 => 'asc' ),
      1 => array ( 0 => 5, 1 => 'asc' ),
      2 => array ( 0 => 2, 1 => 'asc' ),
      3 => array ( 0 => 1, 1 => 'asc' ),
      4 => array ( 0 => 3, 1 => 'asc' ),
      5 => array ( 0 => 6, 1 => 'asc' ),
      6 => array ( 0 => 7, 1 => 'asc' ),
      7 => array ( 0 => 8, 1 => 'asc' ),
      8 => array ( 0 => 9, 1 => 'asc' ),
      9 => array ( 0 => 10, 1 => 'asc' ),
      //10 => array ( 0 => 11, 1 => 'asc' ),
      // 11 => array ( 0 => 12, 1 => 'asc' ),
      // 12 => array ( 0 => 13, 1 => 'asc' ),
      // 13 => array ( 0 => 14, 1 => 'asc' ),
    );

    $this->render_view($this->module['view'] .'/index');
  }

  public function index_data_source($jenis = 'stock')
  {
    $this->authorized($this->module, 'index');

    if ($jenis !== NULL){
      $jenis = (urldecode($jenis) === 'stock') ? NULL : urldecode($jenis);
    }else {
      $jenis = urldecode($jenis);
    }

    $entities = $this->model->getIndex($jenis);

    $data = array();
    $no = $_POST['start'];
    $initial_quantity = array();
    $received_quantity = array();
    $issued_quantity = array();
    $adjustment_quantity = array();
    $quantity = array();
    $total_price = array();

    foreach ($entities as $row){
      $no++;
      $col = array();
      $col[] = print_number($no);
      $col[]  = print_string($row['item_id']);
      $col[] = print_string($row['part_number']);
      $col[] = print_string($row['description']);
      $col[] = print_string($row['serial_number']);
      $col[]  = print_string($row['kode_stok']);
      $col[] = print_string($row['category']);
      $col[] = print_string($row['group']);
      $col[] = print_string($row['condition']);
      // $col[] = print_number($row['initial_quantity'], 2);
      // $col[] = print_number($row['received_quantity'], 2);
      // $col[] = print_number($row['issued_quantity'], 2);
      // $col[] = print_number($row['adjustment_quantity'], 2);
      $col[] = print_number($row['quantity'], 2);
      
      $col[] = print_number($row['unit_value'], 2);//sm
      $col[] = print_number($row['minimum_quantity'], 2);
      $col[] = print_string($row['unit']);      
      $col[]  = print_string($row['coa']);
      
      $col[] = print_string($row['stores']);
      $col[] = print_string($row['warehouse']);
      $col[] = print_string($row['remarks']);
      $col[] = print_string($row['reference_document']);
      $col[] = print_date($row['received_date'],'d F Y');
      if (config_item('auth_role') == 'SUPERVISOR' || config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'SUPER ADMIN' || config_item('auth_role') == 'VP FINANCE' ){
        $col[] = print_number(floatval($row['unit_value'])*floatval($row['quantity']), 2);
      }

      $col['DT_RowId'] = 'row_'. $row['id'];
      $col['DT_RowData']['pkey'] = $row['id'];

      if ($this->has_role($this->module, 'info')){
        $col['DT_RowAttr']['onClick']     = '$(this).popup();';
        $col['DT_RowAttr']['data-target'] = '#data-modal';
        $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] .'/info/'. $row['id'] .'/'. $row['stores']);
      }

      // $initial_quantity[] = $row['initial_quantity'];
      // $received_quantity[] = $row['received_quantity'];
      // $issued_quantity[] = $row['issued_quantity'];
      // $adjustment_quantity[] = $row['adjustment_quantity'];
      $quantity[] = $row['quantity'];
      if (config_item('auth_role') == 'SUPERVISOR' || config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'SUPER ADMIN' || config_item('auth_role') == 'VP FINANCE' ){
        $total_price[] = floatval($row['unit_value'])*floatval($row['quantity']);
      }

      $data[] = $col;
    }

    $result = array(
      "draw" => $_POST['draw'],
      "recordsTotal" => $this->model->countIndex($jenis),
      "recordsFiltered" => $this->model->countIndexFiltered($jenis),
      "data" => $data,
      "total" => array(
        9 => print_number(array_sum($quantity), 2)
        // 7 => print_number(array_sum($initial_quantity), 2),
        // 8 => print_number(array_sum($received_quantity), 2),
        // 9 => print_number(array_sum($issued_quantity), 2),
        // 10 => print_number(array_sum($adjustment_quantity), 2),
        // 11 => print_number(array_sum($quantity), 2)
      )
    );

    if (config_item('auth_role') == 'SUPERVISOR' || config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'SUPER ADMIN' || config_item('auth_role') == 'VP FINANCE' ){
        $result['total'][19] = print_number(array_sum($total_price), 2);
      }

    echo json_encode($result);
  }

  public function info($id, $stores)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'info') === FALSE){
      $return['type'] = 'denied';
      $return['info'] = "You don't have permission to access this data. You may need to login again.";
    } else {
      $stores   = urldecode($stores);
      $entity   = $this->model->findStock($id, $stores);

      $this->data['entity'] = $entity;

      $return['type'] = 'success';
      $return['info'] = $this->load->view($this->module['view'] .'/info', $this->data, TRUE);
    }

    echo json_encode($return);
  }

  public function search_stock_in_stores()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $entities = $this->model->searchStockInStores();

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

  public function mix($id = NULL)
  {
    $this->authorized($this->module, 'mix');

    if ($id !== NULL){
      $mixing_item = $this->model->findById($id);

      $_SESSION['mix']['mixed_items']       = array();
      $_SESSION['mix']['mixing_item']       = $id;
      $_SESSION['mix']['mixing_quantity']   = 0;
      $_SESSION['mix']['mixed_quantity']    = array();
      $_SESSION['mix']['serial_number']     = $mixing_item['serial_number'];
      $_SESSION['mix']['part_number']       = $mixing_item['part_number'];
      $_SESSION['mix']['description']       = $mixing_item['description'];
      $_SESSION['mix']['minimum_quantity']  = $mixing_item['minimum_quantity'];
      $_SESSION['mix']['unit']              = $mixing_item['unit'];
      $_SESSION['mix']['category']          = $mixing_item['category'];
      $_SESSION['mix']['group']             = $mixing_item['group'];
      $_SESSION['mix']['stores']            = $mixing_item['stores'];
      $_SESSION['mix']['warehouse']         = $mixing_item['warehouse'];
      $_SESSION['mix']['notes']             = NULL;

      redirect($this->module['route'] .'/mix');
    }

    if (!isset($_SESSION['mix']))
      redirect($this->module['route']);

    $_SESSION['mix']['mixing_quantity'] = array_sum($_SESSION['mix']['mixed_quantity']);

    $this->render_view($this->module['view'] .'/mix');
  }

  public function mix_save()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'mix') == FALSE){
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this data!';
    } else {
      if (!isset($_SESSION['mix']['mixed_items']) || empty($_SESSION['mix']['mixed_items']) || count($_SESSION['mix']['mixed_items']) < 2){
        $data['success'] = FALSE;
        $data['message'] = 'Please add at least 2 mixed items!';
      } else {
        if ($this->model->mix()){
          unset($_SESSION['mix']);

          $data['success'] = TRUE;
          $data['message'] = 'Mix items success. You will redirected now.';
        } else {
          $data['success'] = FALSE;
          $data['message'] = 'Error while saving data. Please ask Technical Support.';
        }
      }
    }

    echo json_encode($data);
  }

  public function mix_add_item()
  {
    $this->authorized($this->module, 'mix');

    if (isset($_POST) && !empty($_POST)){
      $_SESSION['mix']['mixed_items'][] = array(
        'description'             => trim(strtoupper($this->input->post('description'))),
        'part_number'             => trim(strtoupper($this->input->post('part_number'))),
        'serial_number'           => trim(strtoupper($this->input->post('serial_number'))),
        'mixed_quantity'          => $this->input->post('mixed_quantity'),
        'mixed_unit_value'        => $this->input->post('mixed_unit_value'),
        'minimum_quantity'        => $this->input->post('minimum_quantity'),
        'condition'               => $this->input->post('condition'),
        'stores'                  => trim(strtoupper($this->input->post('stores'))),
        'stock_in_stores_id'      => trim($this->input->post('stock_in_stores_id')),
        'unit'                    => trim($this->input->post('unit')),
        'group'                    => trim($this->input->post('group')),
      );

      end($_SESSION['mix']['mixed_items']);
      $last_key = key($_SESSION['mix']['mixed_items']);

      $_SESSION['mix']['mixed_quantity'][$last_key] = $this->input->post('mixed_quantity');
    }

    redirect($this->module['route'] .'/mix');
  }

  public function mix_del_item($key)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (isset($_SESSION['mix']['mixed_items'])){
      unset($_SESSION['mix']['mixed_items'][$key]);
      unset($_SESSION['mix']['mixed_quantity'][$key]);
    }

    $_SESSION['mix']['mixing_quantity'] = array_sum($_SESSION['mix']['mixed_quantity']);
  }

  public function mix_discard()
  {
    $this->authorized($this->module['permission']['mix']);

    unset($_SESSION['mix']);

    redirect($this->modules['stock']['route']);
  }

  public function adjustment($id)
  {
    $this->authorized($this->module, 'adjustment');

    $entity = $this->model->findById($id);

    if (!$entity or empty($entity)){
      $this->session->set_flashdata('alert', array(
        'type' => 'danger',
        'info' => 'Data not AVAILABLE to edit!'
      ));

      redirect($this->module['route']);
    }

    $this->data['entity'] = $entity;

    $this->render_view($this->module['view'] .'/adjustment');
  }

  public function adjustment_save($id)
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'adjustment') == FALSE){
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to adjust this stock!';
    } else {
      if ($this->model->adjustment($id)){
        $data['success'] = TRUE;
        $data['message'] = 'Adjustment stock success. You will redirected now.';
      } else {
        $data['success'] = FALSE;
        $data['message'] = 'Error while adjust stock. Please ask Technical Support.';
      }
    }

    echo json_encode($data);
  }

  public function relocation($id)
  {
    $this->authorized($this->module, 'relocation');

    $entity = $this->model->findById($id);

    if (!$entity or empty($entity)){
      $this->session->set_flashdata('alert', array(
        'type' => 'danger',
        'info' => 'Data not available to edit!'
      ));

      redirect($this->module['route']);
    }

    $this->data['entity'] = $entity;

    $this->render_view($this->module['view'] .'/relocation');
  }

  public function relocation_save($id)
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'relocation') == FALSE){
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to relocated this stock!';
    } else {
      if ($this->model->relocation($id)){
        $data['success'] = TRUE;
        $data['message'] = 'Relocation stock success. You will redirected now.';
      } else {
        $data['success'] = FALSE;
        $data['message'] = 'Error while relocated stock. Please ask Technical Support.';
      }
    }

    echo json_encode($data);
  }

  // public function import()
  // {
  //   $this->authorized($this->module, 'import');

  //   $this->load->library('form_validation');

  //   if (isset($_POST) && !empty($_POST)){
  //     $this->form_validation->set_rules('delimiter', 'Value Delimiter', 'trim|required');

  //     if ($this->form_validation->run() === TRUE){
  //       $file       = $_FILES['userfile']['tmp_name'];
  //       $delimiter  = $this->input->post('delimiter');

  //       if (($handle = fopen($file, "r")) !== FALSE){
  //         $row     = 1;
  //         $data    = array();
  //         $errors  = array();
  //         $user_id = array();
  //         $index   = 0;

  //         fgetcsv($handle);

  //         while (($col = fgetcsv($handle, 1024, $delimiter)) !== FALSE)
  //         {
  //           $row++;

  //           $group                  = clean_import($col[0]);
  //           $description            = clean_import($col[1]);
  //           $part_number            = clean_import($col[2]);
  //           $alternate_part_number  = clean_import($col[3]);
  //           $serial_number          = clean_import($col[4]);
  //           $minimum_quantity       = clean_float($col[5]);
  //           $unit                   = clean_import($col[6]);
  //           $condition              = clean_import($col[7], 'SERVICEABLE');
  //           $warehouse              = clean_import($col[8]);
  //           $stores                 = clean_import($col[9]);
  //           $quantity               = clean_float($col[10]);
  //           $expired_date           = clean_import($col[11]);
  //           $document_number        = clean_import($col[12]);
  //           $vendor                 = clean_import($col[13]);
  //           $received_date          = clean_import($col[14], date('Y-m-d'));
  //           $received_by            = clean_import($col[15]);
  //           $order_number           = clean_import($col[16]);
  //           $reference_number       = clean_import($col[17]);
  //           $awb_number             = clean_import($col[18]);
  //           $remarks                = clean_import($col[19]);
  //           $unit_value             = clean_float($col[20]);

  //           if (empty($part_number) || strlen($part_number) > 60
  //             || (!empty($group) && !$this->model->isItemGroupExists($group))
  //             || (!empty($alternate_part_number) && strlen($alternate_part_number) > 60)
  //             || (!empty($serial_number) && strlen($serial_number) > 60)
  //             || (!$this->model->isWarehouseExists($warehouse))
  //             || (!$this->model->isStoresExists($stores))
  //             || (!empty($document_number) && $warehouse !== config_item('main_warehouse'))
  //             || (!empty($vendor) && !$this->model->isVendorExists($vendor))
  //             || (!empty($received_date) && !valid_date($received_date))
  //             || (!empty($expired_date) && !valid_date($expired_date))
  //             || ($quantity == NULL)
  //             || ($minimum_quantity == NULL)
  //             || ($unit_value == NULL)
  //             || (!empty($order_number) && strlen($order_number) > 20)
  //             || (!empty($reference_number) && strlen($reference_number) > 60)
  //             || (!empty($awb_number) && strlen($awb_number) > 60)
  //           ){
  //             // VALIDATION ERROR
  //             if (!empty($group) && !$this->model->isItemGroupExists($group))
  //               $errors[] = 'Line '. $row .': ITEM GROUP '. $group .' not found!';

  //             if (empty($part_number) || strlen($part_number) > 60)
  //               $errors[] = 'Line '. $row .': PART NUMBER is empty or more than 60 characters!';

  //             if (!empty($alternate_part_number) && strlen($alternate_part_number) > 60)
  //               $errors[] = 'Line '. $row .': Max length for ALT PART NUMBER is 60 characters!';

  //             if (!empty($serial_number) && strlen($serial_number) > 60)
  //               $errors[] = 'Line '. $row .': Max length for SERIAL NUMBER is 60 characters!';

  //             if ($this->model->isWarehouseExists($warehouse) === FALSE)
  //               $errors[] = 'Line '. $row .': WAREHOUSE '. $warehouse .' not found!';

  //             if (!$this->model->isValidStores($stores, $warehouse, $group))
  //               $errors[] = 'Line '. $row .': STORES '. $stores .' at '. $warehouse .' for '. $group .' not found!';

  //             if (!empty($document_number) && $warehouse !== config_item('main_warehouse'))
  //               $errors[] = 'Line '. $row .': WAREHOUSE '. $warehouse .' is not allowed to create GRN!';

  //             if (!empty($vendor) && !$this->model->isVendorExists($vendor))
  //               $errors[] = 'Line '. $row .': VENDOR '. $vendor .' not found!';

  //             if (!empty($received_date) && !valid_date($received_date))
  //               $errors[] = 'Line '. $row .': Only YYYY-MM-DD format allowed for RECEIVED DATE!';

  //             if (!empty($expired_date) && !valid_date($expired_date))
  //               $errors[] = 'Line '. $row .': Only YYYY-MM-DD format allowed for EXPIRED DATE!';

  //             if ($quantity == NULL)
  //               $errors[] = 'Line '. $row .': Only number is allowed for QUANTITY!';

  //             if ($minimum_quantity == NULL)
  //               $errors[] = 'Line '. $row .': Only number is allowed for MINIMUM QUANTITY!';

  //             if ($unit_value == NULL)
  //               $errors[] = 'Line '. $row .': Only number is allowed for UNIT VALUE!';
  //           } else {
  //             // Check to create master item or not
  //             if (isItemExists($part_number)){
  //               // master item is found, skip to create item
  //               $data[$row]['create_item'] = FALSE;
  //             } else {
  //               // master item is not found, create new master item
  //               if (!empty($group) && !empty($description) && !empty($unit)){
  //                 // Requirement: item group, description, unit
  //                 $data[$row]['create_item'] = TRUE;

  //                 // create new unit if not found in master unit
  //                 if ($this->model->isItemUnitExists($unit))
  //                   $data[$row]['create_unit'] = FALSE;
  //                 else
  //                   $data[$row]['create_unit'] = TRUE;
  //               } else {
  //                 // Master item failed to create
  //                 $data[$row]['create_item'] = FALSE;

  //                 // check requirements
  //                 $errors_item = 'Line '. $row .': Cannot create item';

  //                 if (empty($group))
  //                   $errors_item.= ', ITEM GROUP';

  //                 if (empty($description))
  //                   $errors_item.= ', DESCRIPTION';

  //                 if (empty($unit))
  //                   $errors_item.= ', UNIT';

  //                 $errors_item.= ' is NULL!';

  //                 $errors[] = $errors_item;
  //               }
  //             }

  //             // create item serial number if not empty
  //             if (empty($serial_number)){
  //               $data[$row]['create_serial_number'] = FALSE;
  //             } else {
  //               $data[$row]['create_serial_number'] = TRUE;
  //             }
  //           }

  //           $data[$row]['group']                  = $group;
  //           $data[$row]['description']            = $description;
  //           $data[$row]['part_number']            = $part_number;
  //           $data[$row]['alternate_part_number']  = $alternate_part_number;
  //           $data[$row]['serial_number']          = $serial_number;
  //           $data[$row]['minimum_quantity']       = $minimum_quantity;
  //           $data[$row]['unit']                   = $unit;
  //           $data[$row]['condition']              = $condition;
  //           $data[$row]['warehouse']              = $warehouse;
  //           $data[$row]['stores']                 = $stores;
  //           $data[$row]['quantity']               = $quantity;
  //           $data[$row]['expired_date']           = $expired_date;
  //           $data[$row]['document_number']        = $document_number;
  //           $data[$row]['vendor']                 = $vendor;
  //           $data[$row]['received_date']          = $received_date;
  //           $data[$row]['received_by']            = $received_by;
  //           $data[$row]['order_number']           = $order_number;
  //           $data[$row]['reference_number']       = $reference_number;
  //           $data[$row]['awb_number']             = $awb_number;
  //           $data[$row]['remarks']                = $remarks;
  //           $data[$row]['unit_value']             = $unit_value;
  //         }

  //         fclose($handle);

  //         if (empty($errors)){
  //           if ($this->model->import($data)){
  //             $this->session->set_flashdata('alert', array(
  //               'type' => 'success',
  //               'info' => count($data)." data has been imported!"
  //             ));
  //           }

  //           redirect($this->module['route']);
  //         } else {
  //           foreach ($errors as $key => $value){
  //             $err[] = "\n#". $value;
  //           }

  //           $this->session->set_flashdata('alert', array(
  //             'type' => 'danger',
  //             'info' => "There are errors on data\n#". implode("\n#", $errors)
  //           ));
  //         }
  //       } else {
  //         $this->session->set_flashdata('alert', array(
  //           'type' => 'danger',
  //           'info' => 'Cannot open file!'
  //         ));
  //       }
  //     }
  //   }

  //   redirect($this->module['route']);
  // }

  public function import()
  {
    $this->authorized($this->module, 'import');

    $this->load->library('form_validation');

    if (isset($_POST) && !empty($_POST)){
      $this->form_validation->set_rules('delimiter', 'Value Delimiter', 'trim|required');

      if ($this->form_validation->run() === TRUE){
        $file       = $_FILES['userfile']['tmp_name'];
        $delimiter  = $this->input->post('delimiter');

        //... open file
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

            /******************
             * CHECK COLUMN 0
             ******************/
            $group = trim(strtoupper($col[0]));
            $data[$row]['group'] = $group;

            if ($group == '')
              $errors[] = 'Line '. $row .': group is null!';


            /**************************************************
             * CHECK COLUMN 1
             **********************************/
            $description = trim(strtoupper($col[1]));
            $data[$row]['description'] = $description;

            if ($description == '')
              $errors[] = 'Line '. $row .': description is null!';

            /******************
             * CHECK COLUMN 2
             ******************/
            $part_number = trim(strtoupper($col[2]));
            $data[$row]['part_number'] = $part_number;

            if ($part_number == '')
              $errors[] = 'Line '. $row .': part_number is null!';

            /******************
             * CHECK COLUMN 3
             ******************/
            $alternate_part_number = trim(strtoupper($col[3]));
            $data[$row]['alternate_part_number'] = $alternate_part_number;

            // if ($alternate_part_number == '')
            //   $errors[] = 'Line '. $row .': alternate_part_number is null!';

            /******************
             * CHECK COLUMN 4
             ******************/
            $serial_number = trim(strtoupper($col[4]));
            $data[$row]['serial_number'] = $serial_number;

            // if ($serial_number == '')
            //   $errors[] = 'Line '. $row .': serial_number is null!';

            /******************
             * CHECK COLUMN 5
             ******************/
            $minimum_quantity = trim(strtoupper($col[5]));
            $data[$row]['minimum_quantity'] = $minimum_quantity;

            if ($minimum_quantity == '')
              $errors[] = 'Line '. $row .': minimum_quantity is null!';

            /******************
             * CHECK COLUMN 6
             ******************/
            $unit = trim(strtoupper($col[6]));
            $data[$row]['unit'] = $unit;

            if ($unit == '')
              $errors[] = 'Line '. $row .': unit is null!';

            /******************
             * CHECK COLUMN 7
             ******************/
            $condition = trim(strtoupper($col[7]));
            $data[$row]['condition'] = $condition;

            if ($condition == '')
              $errors[] = 'Line '. $row .': condition is null!';

            /******************
             * CHECK COLUMN 8
             ******************/
            $warehouse = trim(strtoupper($col[8]));
            $data[$row]['warehouse'] = $warehouse;

            if ($warehouse == '')
              $errors[] = 'Line '. $row .': warehouse is null!';

            /******************
             * CHECK COLUMN 9
             ******************/
            $stores = trim(strtoupper($col[9]));
            $data[$row]['stores'] = $stores;

            if ($stores == '')
              $errors[] = 'Line '. $row .': stores is null!';

            /******************
             * CHECK COLUMN 10
             ******************/
            $quantity = trim(strtoupper($col[10]));
            $data[$row]['quantity'] = $quantity;

            if ($quantity == '')
              $errors[] = 'Line '. $row .': quantity is null!';

            /******************
             * CHECK COLUMN 11
             ******************/
            $expired_date = trim(strtoupper($col[11]));
            $data[$row]['expired_date'] = $expired_date;

            // if ($expired_date == '')
            //   $errors[] = 'Line '. $row .': expired_date is null!';

            /******************
             * CHECK COLUMN 12
             ******************/
            $document_number = trim(strtoupper($col[12]));
            $data[$row]['document_number'] = $document_number;

            // if ($document_number == '')
            //   $errors[] = 'Line '. $row .': document_number is null!';

            /******************
             * CHECK COLUMN 13
             ******************/
            $vendor = trim(strtoupper($col[13]));
            $data[$row]['vendor'] = $vendor;

            // if ($vendor == '')
            //   $errors[] = 'Line '. $row .': vendor is null!';

            /******************
             * CHECK COLUMN 14
             ******************/
            $received_date = trim(strtoupper($col[14]));
            $data[$row]['received_date'] = $received_date;

            if ($received_date == '')
              $errors[] = 'Line '. $row .': received_date is null!';

            /******************
             * CHECK COLUMN 15
             ******************/
            $received_by = trim(strtoupper($col[15]));
            $data[$row]['received_by'] = $received_by;

            // if ($received_by == '')
            //   $errors[] = 'Line '. $row .': received_by is null!';

            /******************
             * CHECK COLUMN 16
             ******************/
            $order_number = trim(strtoupper($col[16]));
            $data[$row]['order_number'] = $order_number;

            // if ($order_number == '')
            //   $errors[] = 'Line '. $row .': order_number is null!';

            /******************
             * CHECK COLUMN 17
             ******************/
            $reference_number = trim(strtoupper($col[17]));
            $data[$row]['reference_number'] = $reference_number;

            // if ($reference_number == '')
            //   $errors[] = 'Line '. $row .': reference_number is null!';

            /******************
             * CHECK COLUMN 18
             ******************/
            $awb_number = trim(strtoupper($col[2]));
            $data[$row]['awb_number'] = $awb_number;

            // if ($awb_number == '')
            //   $errors[] = 'Line '. $row .': awb_number is null!';

            /******************
             * CHECK COLUMN 19
             ******************/
            $remarks = trim(strtoupper($col[19]));
            $data[$row]['remarks'] = $remarks;

            if ($remarks == '')
              $errors[] = 'Line '. $row .': remarks is null!';

            /******************
             * CHECK COLUMN 20
             ******************/
            $unit_value = trim(strtoupper($col[20]));
            $data[$row]['unit_value'] = $unit_value;

            if ($unit_value == '')
              $errors[] = 'Line '. $row .': unit_value is null!';

            /******************
             * CHECK COLUMN 21
             ******************/
            $kode_stok = trim(strtoupper($col[21]));
            $data[$row]['kode_stok'] = $kode_stok;

            if ($kode_stok == '')
              $errors[] = 'Line '. $row .': kode_stok is null!';



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

  public function relocation_discard()
  {
    $this->authorized($this->module['permission']['relocation']);

    //unset($_SESSION['mix']);

    redirect($this->modules['stock']['route']);
  }

  public function index_mixing()
  {
    $this->authorized($this->module, 'mixing_document');

    $this->data['page']['title']            = $this->module['label'].' Report';
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumnsMixing());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source_mixing/');
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array( 11,12,13 );
    if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'PROCUREMENT'){
      // $this->data['grid']['summary_columns'][] = 15;
      $this->data['grid']['summary_columns'][] = 17;
    }
    $this->data['grid']['order_columns']    = array (
      0 => array ( 0 => 3, 1 => 'asc' ),
      1 => array ( 0 => 4, 1 => 'asc' ),
      2 => array ( 0 => 2, 1 => 'asc' ),
      3 => array ( 0 => 1, 1 => 'asc' ),
      4 => array ( 0 => 5, 1 => 'asc' ),
      5 => array ( 0 => 6, 1 => 'asc' ),
      6 => array ( 0 => 7, 1 => 'asc' ),
      7 => array ( 0 => 8, 1 => 'asc' ),
      8 => array ( 0 => 9, 1 => 'asc' ),
      9 => array ( 0 => 10, 1 => 'asc' ),
      10 => array ( 0 => 11, 1 => 'asc' ),
      11 => array ( 0 => 12, 1 => 'asc' ),
    );

    $this->render_view($this->module['view'] .'/index_mixing');
  }

  public function index_data_source_mixing()
  {
    $this->authorized($this->module, 'mixing_document');

    $entities = $this->model->getIndexMixing();

    $data = array();
    $no = $_POST['start'];
    $total_previous_quantity = array();
    $total_adjustment_quantity = array();
    $total_balance_quantity = array();
    $total_price = array();

    foreach ($entities as $row){
      $no++;
      $col = array();
      $col[] = print_number($no);
      $col[]  = print_string($row['id']);
      $col[] = print_date($row['date_of_entry'], 'Y-m-d');
      $col[] = print_string($row['part_number']);      
      $col[] = print_string($row['serial_number']);
      $col[] = print_string($row['description']);
      $col[] = print_string($row['category']);
      $col[] = print_string($row['group']);
      $col[] = print_string($row['warehouse']);
      $col[] = print_string($row['stores']);
      $col[] = print_string($row['condition']);
      $col[] = print_number($row['previous_quantity'], 2);
      $col[] = print_number($row['adjustment_quantity'], 2);
      $col[] = print_number($row['balance_quantity'], 2);
      $col[] = print_string($row['unit']);
      $col[] = $row['remarks'];
      if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'PROCUREMENT'){
        $col[] = print_number($row['unit_value'], 2);
        $col[] = print_number($row['total_value'], 2);
        $total_price[] = $row['total_value'];
      }
      
      $col['DT_RowId'] = 'row_'. $row['id'];
      $col['DT_RowData']['pkey'] = $row['id'];
      $total_previous_quantity[] = $row['previous_quantity'];
      $total_adjustment_quantity[] = $row['adjustment_quantity'];
      $total_balance_quantity[] = $row['balance_quantity'];
      

      if ($this->has_role($this->modules['stock_card'], 'info')){
        $col['DT_RowAttr']['onClick']   = '$(this).redirect("_self");';
        $col['DT_RowAttr']['data-href'] = site_url($this->modules['stock_card']['route'] .'/info/'. $row['id']);
      }

      $data[] = $col;
    }

    $result = array(
      "draw" => $_POST['draw'],
      "recordsTotal" => $this->model->countIndexMixing(),
      "recordsFiltered" => $this->model->countIndexFilteredMixing(),
      "data" => $data,
      "total" => array(
        12 => print_number(array_sum($total_adjustment_quantity), 2),
        // 9 => print_number(array_sum($total_adjustment_quantity), 2),
        // 10 => print_number(array_sum($total_balance_quantity), 2),
        // 14 => print_number(array_sum($total_price), 2),
      )
    );
    if (config_item('auth_role') != 'SUPERVISOR'){
        // $result['total'][15] = print_number(array_sum($total_price), 2);
        $result['total'][17] = print_number(array_sum($total_price), 2);
    }

    echo json_encode($result);
  }
}
