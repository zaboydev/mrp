<?php defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_Request extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['purchase_request'];
    $this->load->helper($this->module['helper']);
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
    $this->load->library('email');
    $this->load->library('upload');
    if (empty($_SESSION['request']['request_to']))
      $_SESSION['request']['request_to'] = 1;
  }

  public function set_doc_number()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (empty($_GET['data']))
      $number = request_last_number();
    else
      $number = $_GET['data'];

    $_SESSION['request']['pr_number'] = $number;
  }

  public function set_pr_date()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['request']['pr_date'] = $_GET['data'];
  }

  public function set_required_date()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['request']['required_date'] = $_GET['data'];
  }

  public function set_created_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['request']['created_by'] = $_GET['data'];
  }

  public function set_suggested_supplier()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['request']['suggested_supplier'] = $_GET['data'];
  }

  public function set_deliver_to()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['request']['deliver_to'] = $_GET['data'];
  }

  public function set_notes()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['request']['notes'] = $_GET['data'];
  }
  public function set_request_to()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $_SESSION['request']['request_to'] = $_GET['data'];
    $result['status'] = "success";
    echo json_encode($result);
  }

  public function set_annual_cost_center_id($annual_cost_center_id)
  {
    $this->authorized($this->module, 'document');

    $_SESSION['request']['annual_cost_center_id'] = urldecode($annual_cost_center_id);
    $cost_center = findCostCenterByAnnualCostCenterId(urldecode($annual_cost_center_id));
    $cost_center_code = $cost_center['cost_center_code'];
    $cost_center_name = $cost_center['cost_center_name'];          
    $department_id    = $cost_center['department_id'];

    $_SESSION['request']['cost_center_id']          = $cost_center['id'];
    $_SESSION['request']['cost_center_name']        = $cost_center_name;
    $_SESSION['request']['cost_center_code']        = $cost_center_code;
    $_SESSION['request']['department_id']           = $department_id;
    $_SESSION['request']['head_dept']               = NULL;

    redirect($this->module['route'] . '/create');
  }

  public function set_head_dept()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['request']['head_dept'] = $_GET['data'];
  }

  public function del_item($key)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (isset($_SESSION['request']['items']))
      unset($_SESSION['request']['items'][$key]);
  }

  public function search_budget()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $category = $_SESSION['request']['category'];
    $entities = $this->model->searchBudget($category);

    foreach ($entities as $key => $value) {
      $entities[$key]['label'] = $value['product_name'];
      $entities[$key]['label'] .= ' || PN: ';
      $entities[$key]['label'] .= $value['product_code'];
      $entities[$key]['label'] .= '<small>';
      $entities[$key]['label'] .= 'Minimum Qty: <code>' . number_format($value['minimum_quantity'], 2) . '</code> || ';
      $entities[$key]['label'] .= 'On Hand Qty: <code>' . number_format($value['on_hand_quantity'], 2) . '</code> || ';
      $entities[$key]['label'] .= 'Left Plan Qty: <code>' . number_format($value['maximum_quantity'], 2) . '</code> ||';
      $entities[$key]['label'] .= 'source: <code>' . $value['source'] . '</code>';
      $entities[$key]['label'] .= '</small>';
    }

    echo json_encode($entities);
  }

  public function search_budget_for_relocation()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $category = $_SESSION['request']['category'];
    $entities = $this->model->searchBudgetForRelocation($category);

    foreach ($entities as $key => $value) {
      $entities[$key]['label'] = $value['product_name'];
      $entities[$key]['label'] .= ' || PN: ';
      $entities[$key]['label'] .= $value['product_code'];
      $entities[$key]['label'] .= '<small>';
      $entities[$key]['label'] .= 'Month Plan: <code>' . $value['bulan'] . '</code> || ';
      $entities[$key]['label'] .= 'Minimum Qty: <code>' . number_format($value['minimum_quantity'], 2) . '</code> || ';
      $entities[$key]['label'] .= 'On Hand Qty: <code>' . number_format($value['on_hand_quantity'], 2) . '</code> || ';
      $entities[$key]['label'] .= 'Left Plan Qty: <code>' . number_format($value['maximum_quantity'], 2) . '</code> ||';
      $entities[$key]['label'] .= 'source: <code>' . $value['source'] . '</code>';
      $entities[$key]['label'] .= '</small>';
    }

    echo json_encode($entities);
  }

  public function search_item_unbudgeted()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $category = $_SESSION['request']['category'];
    $entities = $this->model->searchItemUnbudgeted($category);

    foreach ($entities as $key => $value) {
      $entities[$key]['label'] = $value['product_name'];
      $entities[$key]['label'] .= ' || PN: ';
      $entities[$key]['label'] .= $value['product_code'];
      $entities[$key]['label'] .= '<small>';
      $entities[$key]['label'] .= 'Minimum Qty: <code>' . number_format($value['minimum_quantity'], 2) . '</code> || ';
      $entities[$key]['label'] .= 'On Hand Qty: <code>' . number_format($value['on_hand_quantity'], 2) . '</code> || ';
      $entities[$key]['label'] .= 'Left Plan Qty: <code>' . number_format($value['maximum_quantity'], 2) . '</code> ||';
      // $entities[$key]['label'] .= 'source: <code>'.$value['source'].'</code>';
      $entities[$key]['label'] .= '</small>';
    }

    echo json_encode($entities);
  }

  public function relocate($id)
  {
    if (is_granted($this->module, 'info') === FALSE) {
      $return['type'] = 'denied';
      $return['info'] = "You don't have permission to access this data. You may need to login again.";
    } else {
      $entity = $this->model->findById($id);

      $this->data['entity'] = $entity;

      $return['type'] = 'success';
      $return['info'] = $this->load->view($this->module['view'] . '/relocate', $this->data, TRUE);
    }

    echo json_encode($return);
  }

  public function search_items_by_part_number()
  {
    // if ($this->input->is_ajax_request() === FALSE)
    //   redirect($this->modules['secure']['route'] .'/denied');

    $category = $_SESSION['request']['category'];
    $entities = $this->model->searchItemsByPartNumber($category);

    foreach ($entities as $key => $value) {
      $entities[$key]['label']  = $value['part_number'].'| S.N : '.$value['serial_number'];
      $entities[$key]['label'] .= ' | ';
      $entities[$key]['label'] .= $value['description'];
    }

    echo json_encode($entities);
  }

  public function search_items_by_serial_number()
  {
    // if ($this->input->is_ajax_request() === FALSE)
    //   redirect($this->modules['secure']['route'] .'/denied');

    $category = $_SESSION['request']['category'];
    $entities = $this->model->searchItemsByPartNumber($category);

    foreach ($entities as $key => $value) {
      $entities[$key]['label']  = 'S.N : '.$value['serial_number'].'| P/N : '.$value['part_number'];
      $entities[$key]['label'] .= ' | ';
      $entities[$key]['label'] .= $value['description'];
    }

    echo json_encode($entities);
  }

  public function search_items_by_product_name()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $category = $_SESSION['request']['category'];
    $entities = $this->model->searchItemsByProductName($category);

    echo json_encode($entities);
  }

  public function search_item_groups()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $category = $_SESSION['request']['category'];
    $entities = $this->model->searchItemGroups($category);

    echo json_encode($entities);
  }

  public function get_available_vendors()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $category = $_SESSION['request']['category'];
    $entities = $this->model->getAvailableVendors($category);

    echo json_encode($entities);
  }

  public function index_data_source()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'index') === FALSE) {
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {
      $entities = $this->model->getIndex();
      $data     = array();
      $no       = $_POST['start'];
      $quantity = array();

      foreach ($entities as $row) {
        $no++;
        $col = array();
        if (is_granted($this->module, 'approval')){
          if ($row['status'] == 'waiting' && config_item('auth_username') == $row['head_dept']) {
            $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
          }
          elseif ($row['status'] == 'pending' && config_item('auth_role') == 'FINANCE MANAGER') {
            $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
          }
          elseif ($row['status'] == 'review operation support' && config_item('auth_role') == 'OPERATION SUPPORT') {
            $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
          } else {
            $col[] = print_number($no);
          }
        }
        elseif (is_granted($this->module, 'closing')) {
          if ($row['status'] == 'open') {
            $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
          } else {
            $col[] = print_number($no);
          }
        } else {
          $col[] = print_number($no);
        }

        $col[] = print_string($row['pr_number']);
        $col[] = print_date($row['pr_date'], 'd/m/Y');
        $col[] = print_date($row['required_date'], 'd/m/Y');
        $col[] = print_string($_SESSION['request']['request_to'] == 0 ? $row['category_name'] : $row['item_category']);
        $col[] = print_string($_SESSION['request']['request_to'] == 0 ? $row['product_name'] : $row['product_name']);
        // $col[] = '<a data-id="'.$row['id'].'" href="'.site_url($this->module['route'] .'/info/'. $row['id']).'">'.print_string($_SESSION['request']['request_to'] == 0 ? $row['product_code']:$row['part_number']).'</a>';
        $col[] = '<a data-id="item" data-item-row="' . $row['id'] . '" href="' . site_url($this->module['route'] . '/info/' . $row['id']) . '">' . print_string($row['product_code']) . '</a>';
        $col[] = print_string($row['serial_number']);
        $col[] = print_number(search_min_qty($row['product_code']), 2);
        // $col[] = print_number($row['min_qty'], 2);
        $col[] = '<a data-id="on-hand" data-item-row="' . $row['id'] . '" href="' . site_url($this->module['route'] . '/info_on_hand/' . $row['id']) . '">' . print_number($this->countOnhand($row['id']), 2) . '</a>';
        $col[] = print_number($row['quantity'], 2);
        $col[] = print_number($row['process_qty'], 2);
        $col[] = print_string(strtoupper($row['status']));
        // $col[] = print_string($row['suggested_supplier']);
        $col[] = print_string($row['status'] != 'pending' ? 'Budgeted' : $row['budget_status']);
        $col[] = print_person_name($row['created_by']);
        $col[] = print_string($row['pr_notes']) . ' ' . print_string($row['notes']);
        if ($row['status'] == 'waiting') {
          // if(config_item('auth_role') == 'CHIEF OF MAINTANCE' || config_item('auth_role') == 'SUPER ADMIN'){
          if (is_granted($this->module, 'approval') === TRUE && config_item('auth_role') == 'CHIEF OF MAINTANCE') {
            $col[] = '<input type="text" id="note_' . $row['id'] . '" autocomplete="off"/>';
          } else {
            $col[] = '';
          }
        } elseif ($row['status'] == 'pending') {
          // if(config_item('auth_role') == 'FINANCE MANAGER' || config_item('auth_role') == 'SUPER ADMIN'){
          if (is_granted($this->module, 'approval') === TRUE && config_item('auth_role') == 'FINANCE MANAGER') {
            $col[] = '<input type="text" id="note_' . $row['id'] . '" autocomplete="off"/>';
          } else {
            $col[] = '';
          }
        } elseif ($row['status'] == 'review operation support') {
          // if(config_item('auth_role') == 'OPERATION SUPPORT' || config_item('auth_role') == 'SUPER ADMIN'){
          if (is_granted($this->module, 'approval') === TRUE && config_item('auth_role') == 'OPERATION SUPPORT') {
            $col[] = '<input type="text" id="note_' . $row['id'] . '" autocomplete="off"/>';
          } else {
            $col[] = '';
          }
        } elseif ($row['status'] == 'open') {
          // if (config_item('auth_role') == 'PROCUREMENT' || config_item('auth_role') == 'SUPER ADMIN') {
          if (is_granted($this->module, 'closing') === TRUE) {
            $col[] = '<input type="text" id="note_' . $row['id'] . '" autocomplete="off"/>';
          } else {
            $col[] = '';
          }
        } else {
          $col[] = '';
        }

        // if($row['status']=="budgeted"){
        //   if(config_item('auth_role') == 'PROCUREMENT' || config_item('auth_role') == 'SUPER ADMIN'){
        //     $col[] = '<input type="text" id="note_'.$row['id'].'" autocomplete="off"/>';
        //   }         
        // } else {
        //   if (config_item('auth_role') == 'FINANCE MANAGER' || config_item('auth_role') == 'CHIEF OF MAINTANCE') {
        //     $col[] = '<input type="text" id="note_' . $row['id'] . '" autocomplete="off"/>';
        //   }  
        // }

        if (config_item('auth_role') == 'CHIEF OF MAINTANCE' || config_item('auth_role') == 'FINANCE MANAGER') {
          if (config_item('auth_role') == 'FINANCE MANAGER' && $row['status'] == 'pending') {
            $col[] = $row['price'] == 0 ? '<input type="number" id="price_' . $row['id'] . '" autocomplete="off" value=""/>' : '<input type="number" id="price_' . $row['id'] . '" autocomplete="off" value="' . $row['price'] . '"/>';
          } else {
            $col[] = print_number($row['price'], 2);
          }

          $col[] = print_number($row['total'], 2);
        }

        $col['DT_RowId'] = 'row_' . $row['id'];
        $col['DT_RowData']['pkey']  = $row['id'];

        if ($this->has_role($this->module, 'info')) {
          // $col['DT_RowAttr']['onClick']     = '$(this).popup();';
          $col['DT_RowAttr']['onClick']     = '';
          $col['DT_RowAttr']['data-id']     = $row['id'];
          $col['DT_RowAttr']['data-target'] = '#data-modal';
          $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] . '/info/' . $row['id']);
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
    $source =  $_SESSION['request']['request_to'] == 0 ? "Budget Control" : "MRP";
    $this->data['page']['title']            = $this->module['label'] . strtoupper(" " . $source);
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] . '/index_data_source');
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = NULL;
    $this->data['grid']['order_columns']    = array(
      0   => array(0 => 2,  1 => 'desc'),
      1   => array(0 => 3,  1 => 'desc'),
      2   => array(0 => 1,  1 => 'desc'),
      3   => array(0 => 4,  1 => 'asc'),
      4   => array(0 => 5,  1 => 'asc'),
      5   => array(0 => 6,  1 => 'asc'),
      6   => array(0 => 7,  1 => 'asc'),
      7   => array(0 => 8,  1 => 'asc'),
      8   => array(0 => 9,  1 => 'asc'),
      9   => array(0 => 10,  1 => 'asc'),
      10  => array(0 => 11,  1 => 'asc'),
      11  => array(0 => 12,  1 => 'asc'),
      12  => array(0 => 13,  1 => 'asc'),
    );

    $this->render_view($this->module['view'] . '/index');
  }

  public function info($id)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'info') === FALSE) {
      $return['type'] = 'denied';
      $return['info'] = "You don't have permission to access this data. You may need to login again.";
    } else {
      $entity = $this->model->findById($id);

      $this->data['entity'] = $entity;

      $return['type'] = 'success';
      $return['info'] = $this->load->view($this->module['view'] . '/info', $this->data, TRUE);
    }

    echo json_encode($return);
  }

  public function info_on_hand($id)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'info') === FALSE) {
      $return['type'] = 'denied';
      $return['info'] = "You don't have permission to access this data. You may need to login again.";
    } else {
      $entity = $this->model->info_on_hand($id);

      $this->data['entity'] = $entity;

      $return['type'] = 'success';
      $return['info'] = $this->load->view($this->module['view'] . '/info_on_hand', $this->data, TRUE);
    }

    echo json_encode($return);
  }

  public function info_item($id)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'info') === FALSE) {
      $return['type'] = 'denied';
      $return['info'] = "You don't have permission to access this data. You may need to login again.";
    } else {
      $entity = $this->model->find_item_by_id($id);

      $this->data['entity'] = $entity;

      $return['type'] = 'success';
      $return['info'] = $this->load->view($this->module['view'] . '/change_item', $this->data, TRUE);
    }

    echo json_encode($return);
  }

  public function print_pdf($id)
  {
    $this->authorized($this->module, 'print');

    $entity = $this->model->findPrlById($id);
    // $on_hand_stock = $this->model->findPrlById($id);

    $this->data['entity']           = $entity;
    $this->data['page']['title']    = strtoupper($this->module['label'])." LIST";
    $this->data['page']['content']  = $this->module['view'] . '/print_pdf';

    $html = $this->load->view($this->pdf_theme, $this->data, true);

    $pdfFilePath = str_replace('/', '-', $entity['pr_number']) . ".pdf";

    $this->load->library('m_pdf');

    $pdf = $this->m_pdf->load(null, 'A4-L');
    $pdf->WriteHTML($html);
    $pdf->Output($pdfFilePath, "I");
  }

  public function edit($id)
  {
    $this->authorized($this->module, 'document');

    // $entity   = $this->model->findById($id);
    $entity   = $this->model->findPrlById($id);

    // if (!isset($_SESSION['request'])){
    $_SESSION['request']              = $entity;
    $_SESSION['request']['id']        = $id;
    $_SESSION['request']['edit']      = $entity['pr_number'];
    $_SESSION['request']['category']  = $entity['category_name'];
    // }

    redirect($this->module['route'] . '/create');
    // $this->render_view($this->module['view'] .'/create');
  }

  public function create($category = NULL)
  {
    $this->authorized($this->module, 'document');

    if ($category !== NULL) {
      $category = urldecode($category);
      if ($category == 'BAHAN BAKAR') {
        $target_date = 7;
        $start_date  = date('Y-m-d');
        $date        = strtotime('+7 day', strtotime($start_date));
        $required_date    = date('Y-m-d', $date);
      } else {
        $target_date = 30;
        $start_date  = date('Y-m-d');
        $date        = strtotime('+30 day', strtotime($start_date));
        $required_date    = date('Y-m-d', $date);
      }

      $_SESSION['request']['items']               = array();
      $_SESSION['request']['category']            = $category;
      $_SESSION['request']['order_number']        = request_last_number();
      $_SESSION['request']['pr_number']           = request_last_number() . request_format_number();
      $_SESSION['request']['pr_date']             = date('Y-m-d');
      $_SESSION['request']['required_date']       = $required_date;
      $_SESSION['request']['created_by']          = config_item('auth_person_name');
      $_SESSION['request']['suggested_supplier']      = NULL;
      $_SESSION['request']['deliver_to']              = NULL;
      $_SESSION['request']['notes']                   = NULL;
      $_SESSION['request']['target_date']             = $target_date;
      $_SESSION['request']['annual_cost_center_id']   = null;
      $_SESSION['request']['cost_center_id']          = null;
      $_SESSION['request']['cost_center_name']        = null;
      $_SESSION['request']['cost_center_code']        = null;
      $_SESSION['request']['department_id']           = null;
      $_SESSION['request']['head_dept']               = NULL;
      $_SESSION['request']['attachment']              = array();

      redirect($this->module['route'] . '/create');
    }

    if (!isset($_SESSION['request']))
      redirect($this->module['route']);

    $this->data['page']['content']    = $this->module['view'] . '/create';
    $this->data['page']['offcanvas']  = $this->module['view'] . '/create_offcanvas_add_item';

    $this->render_view($this->module['view'] . '/create');
  }

  public function save()
  {
    // if ($this->input->is_ajax_request() == FALSE)
    //   redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') == FALSE) {
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      if (!isset($_SESSION['request']['items']) || empty($_SESSION['request']['items'])) {
        $data['success'] = FALSE;
        $data['message'] = 'Please add at least 1 item!';
      } else {
        $pr_number = $_SESSION['request']['pr_number'];

        $errors = array();

        if (isset($_SESSION['request']['edit'])) {
          if ($_SESSION['request']['edit'] != $pr_number && $this->model->isDocumentNumberExists($pr_number)) {
            $errors[] = 'Duplicate Document Number: ' . $pr_number . ' !';
          }
        } else {
          if ($this->model->isDocumentNumberExists($pr_number)) {
            $errors[] = 'Duplicate Document Number: ' . $pr_number . ' !';
          }
        }

        if($_SESSION['request']['annual_cost_center_id']=='' || $_SESSION['request']['annual_cost_center_id']==NULL){
          $errors[] = 'Please Select Department!!';
        }

        if($_SESSION['request']['head_dept']=='' || $_SESSION['request']['head_dept']==NULL){
          $errors[] = 'Please Select Head Dept!!';
        }

        if (!empty($errors)) {
          $data['success'] = FALSE;
          $data['message'] = implode('<br />', $errors);
        } else {
          if ($this->model->save()) {
            unset($_SESSION['request']);

            // SEND EMAIL NOTIFICATION HERE
            // $this->send_mail();
            $data['success'] = TRUE;
            $data['message'] = 'Document ' . $pr_number . ' has been saved. You will redirected now.';
          } else {
            $data['success'] = FALSE;
            $data['message'] = 'Error while saving this document. Please ask Technical Support.';
          }
        }
      }
    }

    echo json_encode($data);
  }

  public function save_change_item()
  {
    // if ($this->input->is_ajax_request() == FALSE)
    //   redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') == FALSE) {
      $data['type'] = FALSE;
      $data['info'] = 'You are not allowed to save this Document!';
    } else {
      if ($this->model->save_change_item()) {
        $data['type'] = 'success';
        $data['info'] = 'Update Success';
      } else {
        $data['type'] = 'danger';
        $data['info'] = 'Error while saving this document. Please ask Technical Support.';
      }
    }

    // echo json_encode($data);
    redirect($this->module['route']);
  }

  public function cancel($id)
  {
    // if ($this->input->is_ajax_request() === FALSE)
    //   redirect($this->modules['secure']['route'] .'/denied');
    $cancel =  $this->model->cancel($id);
    if ($cancel) {
      redirect($this->module['route']);
    }
  }

  public function add_item()
  {
    $this->authorized($this->module, 'document');

    if (isset($_POST) && !empty($_POST)) {

      $_SESSION['request']['items'][] = array(
        'inventory_monthly_budget_id' => $this->input->post('inventory_monthly_budget_id'),
        'group_name'                  => $this->input->post('group_name'),
        'product_name'                => trim(strtoupper($this->input->post('product_name'))),
        'part_number'                 => trim(strtoupper($this->input->post('part_number'))),
        'unit'                        => $this->input->post('unit'),
        'quantity'                    => $this->input->post('quantity'),
        'price'                       => $this->input->post('price'),
        'total'                       => $this->input->post('total'),
        'additional_info'             => $this->input->post('additional_info'),
        'ytd_quantity'                => $this->input->post('ytd_quantity'),
        'ytd_used_quantity'           => $this->input->post('ytd_used_quantity'),
        'ytd_budget'                  => $this->input->post('ytd_budget'),
        'ytd_used_budget'             => $this->input->post('ytd_used_budget'),
        'unbudgeted_item'             => $this->input->post('unbudgeted_item'),
        'relocation_item'             => $this->input->post('relocation_item'),
        'need_budget'                 => $this->input->post('need_budget'),
        'mtd_quantity'                => $this->input->post('mtd_quantity'),
        'mtd_used_quantity'           => $this->input->post('mtd_used_quantity'),
        'mtd_budget'                  => $this->input->post('mtd_budget'),
        'mtd_used_budget'             => $this->input->post('mtd_used_budget'),
        'part_number_relocation'      => $this->input->post('origin_budget'),
        'budget_value_relocation'     => $this->input->post('budget_value'),
        'reference_ipc'               => trim($this->input->post('reference_ipc')),
        'serial_number'               => trim($this->input->post('serial_number')),
        'minimum_quantity'     => $this->input->post('minimum_quantity'),
        // 'budget_value_relocation'      => $this->input->post('budget_value'),
      );
    }

    redirect($this->module['route'] . '/create');
  }

  public function discard()
  {
    $this->authorized($this->module['permission']['document']);

    unset($_SESSION['request']);

    redirect($this->module['route']);
  }

  public function delete_ajax()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'delete') === FALSE) {
      $alert['type']  = 'danger';
      $alert['info']  = 'You are not allowed to delete this data!';
    } else {
      $entity = $this->model->findById($this->input->post('id'));

      if ($this->model->isValidDocumentQuantity($entity['pr_number']) === FALSE) {
        $alert['type']  = 'danger';
        $alert['info']  = 'Stock quantity for document ' . $entity['pr_number'] . ' has been change. You are not allowed to delete this document. You can adjust stock to sync the quantity.';
      } else {
        if ($this->model->delete()) {
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

  public function multi_approve()
  {
    $id_purchase_order = $this->input->post('id_purchase_order');
    $id_purchase_order = str_replace("|", "", $id_purchase_order);
    $id_purchase_order = substr($id_purchase_order, 0, -1);
    $id_purchase_order = explode(",", $id_purchase_order);

    $str_price = $this->input->post('price');
    $price = str_replace("|", "", $str_price);
    $price = substr($price, 0, -3);
    $price = explode("##,", $price);

    $total = 0;
    $success = 0;
    $failed = sizeof($id_purchase_order);
    $x = 0;
    foreach ($id_purchase_order as $key) {
      if ($this->model->approve($key, $price[$x])) {
        $total++;
        $success++;
        $failed--;
        // $this->model->send_mail_approved($key,'approved');
      }
      $x++;
    }
    if ($success > 0) {
      // $id_role = 13;
      if($this->config->item('access_from')!='localhost'){
        // if ((config_item('auth_role') == 'FINANCE MANAGER')) {
        //   $id_role = 9;
        //   $this->model->send_mail_next_approval($id_purchase_order, $id_role);
        // }
        // if ((config_item('auth_role') == 'CHIEF OF MAINTANCE')) {
        //   $id_role = 15;
        //   $this->model->send_mail_next_approval($id_purchase_order, $id_role);
        // }
        $this->model->send_mail_approval($id_purchase_order, 'approve', config_item('auth_person_name'));
      }
      $this->session->set_flashdata('alert', array(
        'type' => 'success',
        'info' => $success . " data has been update!"
      ));
    }
    if ($failed > 0) {
      $this->session->set_flashdata('alert', array(
        'type' => 'danger',
        'info' => "There are " . $failed . " errors"
      ));
    }
    if ($total == 0) {
      $result['status'] = 'failed';
    } else {
      //$this->sendEmailHOS();
      $result['status'] = 'success';
    }
    echo json_encode($result);
  }

  public function multi_reject()
  {
    $str_id_purchase_order = $this->input->post('id_purchase_order');
    $str_notes = $this->input->post('notes');
    $id_purchase_order = str_replace("|", "", $str_id_purchase_order);
    $id_purchase_order = substr($id_purchase_order, 0, -1);
    $notes = str_replace("|", "", $str_notes);
    $notes = substr($notes, 0, -3);
    $id_purchase_order = explode(",", $id_purchase_order);
    $notes = explode("##,", $notes);
    $result = $this->model->multi_reject($id_purchase_order, $notes);
    if ($result) {
      $this->model->send_mail_approval($id_purchase_order, 'rejected', config_item('auth_person_name'));
      $return["status"] = "success";
      echo json_encode($return);
    } else {
      $return["status"] = "failed";
      echo json_encode($return);
    }
  }

  public function multi_closing()
  {
    $str_id_purchase_order = $this->input->post('id_purchase_order');
    $str_notes = $this->input->post('notes');
    $id_purchase_order = str_replace("|", "", $str_id_purchase_order);
    $id_purchase_order = substr($id_purchase_order, 0, -1);
    $notes = str_replace("|", "", $str_notes);
    $notes = substr($notes, 0, -3);
    $id_purchase_order = explode(",", $id_purchase_order);
    $notes = explode("##,", $notes);
    $result = $this->model->multi_closing($id_purchase_order, $notes);
    if ($result) {
      // $this->model->send_mail_approval($id_purchase_order, 'rejected', config_item('auth_person_name'));
      $return["status"] = "success";
      echo json_encode($return);
    } else {
      $return["status"] = "failed";
      echo json_encode($return);
    }
  }

  public function ajax_editItem($key)
  {
    $this->authorized($this->module, 'document');

    $entity = $_SESSION['request']['items'][$key];

    echo json_encode($entity);
  }

  public function edit_item($key)
  {
    $this->authorized($this->module, 'document');
    // $key = $key;

    if (isset($_POST) && !empty($_POST)) {

      $_SESSION['request']['items'][$key] = array(
        'inventory_monthly_budget_id' => $this->input->post('inventory_monthly_budget_id'),
        'group_name'                  => $this->input->post('group_name'),
        'product_name'                => trim(strtoupper($this->input->post('product_name'))),
        'part_number'                 => trim(strtoupper($this->input->post('part_number'))),
        'unit'                        => $this->input->post('unit'),
        'quantity'                    => $this->input->post('quantity'),
        'price'                       => $this->input->post('price'),
        'total'                       => $this->input->post('total'),
        'additional_info'             => $this->input->post('additional_info'),
        'ytd_quantity'                => $this->input->post('ytd_quantity'),
        'ytd_used_quantity'           => $this->input->post('ytd_used_quantity'),
        'ytd_budget'                  => $this->input->post('ytd_budget'),
        'ytd_used_budget'             => $this->input->post('ytd_used_budget'),
        'unbudgeted_item'             => $this->input->post('unbudgeted_item'),
        'relocation_item'             => $this->input->post('relocation_item'),
        'need_budget'                 => $this->input->post('need_budget'),
        'mtd_quantity'                => $this->input->post('mtd_quantity'),
        'mtd_used_quantity'           => $this->input->post('mtd_used_quantity'),
        'mtd_budget'                  => $this->input->post('mtd_budget'),
        'mtd_used_budget'             => $this->input->post('mtd_used_budget'),
        'part_number_relocation'      => $this->input->post('origin_budget'),
        'budget_value_relocation'     => $this->input->post('budget_value'),
        'reference_ipc'               => trim($this->input->post('reference_ipc')),
        'serial_number'               => trim($this->input->post('serial_number')),
      );
    }

    redirect($this->module['route'] . '/create');
  }

  public function create_item_purchase($category)
  {

    $id_purchase_order = $this->input->post('id_purchase_order');
    $id_purchase_order = str_replace("|", "", $id_purchase_order);
    $id_purchase_order = substr($id_purchase_order, 0, -1);
    $id_purchase_order = explode(",", $id_purchase_order);
    $on_hand_qty = $this->input->post('on_hand_qty');
    $on_hand_qty = explode("|", $on_hand_qty);
    $total = 0;
    $success = 0;
    $failed = sizeof($id_purchase_order);
    $_SESSION['request']['category']            = $category;
    $_SESSION['request']['items'] = array();
    $i = 0;
    foreach ($id_purchase_order as $key) {
      $items = $this->model->findItemByPartNumber($key);
      $budget = $this->model->findItemBudget($key);
      if ($budget->num_rows() > 0) {
        $row_budget    = $budget->unbuffered_row('array');
        $_SESSION['request']['items'][$i] = array(
          'inventory_monthly_budget_id' => $row_budget['id'],
          'ytd_quantity'                => $row_budget['ytd_quantity'],
          'ytd_used_quantity'           => $row_budget['ytd_used_quantity'],
          'ytd_budget'                  => $row_budget['ytd_budget'],
          'ytd_used_budget'             => $row_budget['ytd_used_budget'],
          // 'inventory_monthly_budget_id' => 0,
          'group_name'                  => $items['group'],
          'product_name'                => $items['description'],
          'part_number'                 => $items['part_number'],
          'unit'                        => $items['unit'],
          'quantity'                    => 0,
          'price'                       => $items['current_price'],
          'total'                       => 0,
          'additional_info'             => '',
          'on_hand_qty'                 => $on_hand_qty[$i],
          'unbudgeted_item'             => 0,
          'relocation_item'             => '',
          'need_budget'                 => '',
          'mtd_quantity'                => $row_budget['mtd_quantity'],
          'mtd_used_quantity'           => $row_budget['mtd_used_quantity'],
          'mtd_budget'                  => $row_budget['mtd_budget'],
          'mtd_used_budget'             => $row_budget['mtd_used_budget'],

        );
      } else {
        $_SESSION['request']['items'][$i] = array(
          'inventory_monthly_budget_id' => '',
          'ytd_quantity'                => 0,
          'ytd_used_quantity'           => 0,
          'ytd_budget'                  => 0,
          'ytd_used_budget'             => 0,
          // 'inventory_monthly_budget_id' => 0,
          'group_name'                  => $items['group'],
          'product_name'                => $items['description'],
          'part_number'                 => $items['part_number'],
          'unit'                        => $items['unit'],
          'quantity'                    => 0,
          'price'                       => $items['current_price'],
          'total'                       => 0,
          'additional_info'             => '',
          'on_hand_qty'                 => $on_hand_qty[$i],
          'unbudgeted_item'             => 1,
          'relocation_item'             => '',
          'need_budget'                 => '',
          'mtd_quantity'                => 0,
          'mtd_used_quantity'           => 0,
          'mtd_budget'                  => 0,
          'mtd_used_budget'             => 0,
        );
      }
      $i++;
    }
    $data['success'] = TRUE;
    echo json_encode($data);

    // $_SESSION['request']['order_number']        = request_last_number();
    // $_SESSION['request']['pr_number']           = request_last_number() . request_format_number();
    // $_SESSION['request']['pr_date']             = date('Y-m-d');
    // $_SESSION['request']['required_date']       = date('Y-m-d');
    // $_SESSION['request']['created_by']          = config_item('auth_person_name');
    // $_SESSION['request']['suggested_supplier']  = NULL;
    // $_SESSION['request']['deliver_to']          = NULL;
    // $_SESSION['request']['notes']               = NULL;

    // $this->data['page']['content']    = $this->module['view'] .'/create';
    // $this->data['page']['offcanvas']  = $this->module['view'] .'/create_offcanvas_add_item';

    // $this->render_view($this->module['view'] .'/create');

  }

  function create_purchase()
  {
    $_SESSION['request']['order_number']        = request_last_number();
    $_SESSION['request']['pr_number']           = request_last_number() . request_format_number();
    $_SESSION['request']['pr_date']             = date('Y-m-d');
    $_SESSION['request']['required_date']       = date('Y-m-d');
    $_SESSION['request']['created_by']          = config_item('auth_person_name');
    $_SESSION['request']['suggested_supplier']  = NULL;
    $_SESSION['request']['deliver_to']          = NULL;
    $_SESSION['request']['notes']               = NULL;

    $this->data['page']['content']    = $this->module['view'] . '/create';
    $this->data['page']['offcanvas']  = $this->module['view'] . '/create_offcanvas_add_item';

    $this->render_view($this->module['view'] . '/create');
  }

  public function send_mail()
  {

    $from_email = "bifa.acd@gmail.com";
    $to_email = "aidanurul99@rocketmail.com";

    //Load email library 
    $this->load->library('email');
    $config = array();
    $config['protocol'] = 'mail';
    $config['smtp_host'] = 'smtp.live.com';
    $config['smtp_user'] = 'baliflight@hotmail.com';
    $config['smtp_pass'] = 'b1f42015';
    $config['smtp_port'] = 587;
    $config['smtp_auth']        = true;
    $config['mailtype']         = 'html';
    $this->email->initialize($config);
    $this->email->set_newline("\r\n");
    $message = "<p>Dear Chief Of Maintenance,</p>";
    $message .= "<p>Berikut permintaan Purchase Request  dari Gudang :</p>";
    $message .= "<ul>";
    $message .= "</ul>";
    $message .= "<p>Silakan klik pilihan <strong style='color:blue;'>APPROVE</strong> untuk menyetujui atau <strong style='color:red;'>REJECT</strong> untuk menolak permintaan ini.</p>";
    $message .= "<p>Thanks and regards</p>";
    $this->email->from($from_email, 'Your Name');
    $this->email->to($to_email);
    $this->email->subject('Permintaan Approval Purchase Request');
    $this->email->message($message);
    // $return = $this->model->send_mail();
    //Send mail 
    if ($this->email->send())
      return $this->session->set_flashdata("email_sent", "email sent");
    else
      return $this->session->set_flashdata("email_sent", $this->email->print_debugger());
    // $this->session->set_flashdata("email_sent",$return); 
    $this->render_view($this->module['view'] . '/email_form');
  }

  public function countOnhand($prl_item_id)
  {
    $return = $this->model->tb_on_hand_stock($prl_item_id)->sum;
    return $return;
  }

  public function print_pdf_prl($poe_item_id)
  {
    $this->authorized($this->module, 'print');

    $entity = $this->model->findPrlByPoeItemid($poe_item_id);

    $this->data['entity']           = $entity;
    $this->data['page']['title']    = strtoupper($this->module['label'])." LIST";
    $this->data['page']['content']  = $this->module['view'] . '/print_pdf';

    $html = $this->load->view($this->pdf_theme, $this->data, true);

    $pdfFilePath = str_replace('/', '-', $entity['pr_number']) . ".pdf";

    $this->load->library('m_pdf');

    $pdf = $this->m_pdf->load(null, 'A4-L');
    $pdf->WriteHTML($html);
    $pdf->Output($pdfFilePath, "I");
  }

  public function attachment()
  {
    $this->authorized($this->module, 'document');

    $this->render_view($this->module['view'] . '/attachment');
  }

  public function add_attachment()
  {
    $result["status"] = 0;
    $date = new DateTime();
    
    $config['upload_path'] = 'attachment/purchase_request/';
    $config['allowed_types'] = 'jpg|png|jpeg|doc|docx|xls|xlsx|pdf';
    $config['max_size']  = 2000;

    $this->upload->initialize($config);

    if (!$this->upload->do_upload('attachment')) {
      $error = array('error' => $this->upload->display_errors());
    } else {

      $data = array('upload_data' => $this->upload->data());
      $url = $config['upload_path'] . $data['upload_data']['file_name'];
      array_push($_SESSION["request"]["attachment"], $url);
      $result["status"] = 1;
    }
    echo json_encode($result);
  }

  public function delete_attachment($index)
  {
    $file = FCPATH . $_SESSION["request"]["attachment"][$index];
    if (unlink($file)) {
      unset($_SESSION["request"]["attachment"][$index]);
      $_SESSION["request"]["attachment"] = array_values($_SESSION["request"]["attachment"]);
      redirect($this->module['route'] . "/attachment", 'refresh');
    }
  }

  public function manage_attachment($id)
  {
    $this->authorized($this->module, 'index');

    $this->data['manage_attachment'] = $this->model->getAttachmentByDocumentId($id);
    $this->data['id'] = $id;
    $this->render_view($this->module['view'] . '/manage_attachment');
  }

  public function delete_attachment_in_db($id_att, $id_poe)
  {
    $this->model->delete_attachment_in_db($id_att);

    redirect($this->module['route'] . "/manage_attachment/" . $id_poe, 'refresh');
  }

  public function add_attachment_to_db($id)
  {
    $result["status"] = 0;
    $date = new DateTime();
    
    $config['upload_path'] = 'attachment/purchase_request/';
    $config['allowed_types'] = 'jpg|png|jpeg|doc|docx|xls|xlsx|pdf';
    $config['max_size']  = 2000;

    $this->upload->initialize($config);

    if (!$this->upload->do_upload('attachment')) {
      $error = array('error' => $this->upload->display_errors());
    } else {
      $data = array('upload_data' => $this->upload->data());
      $url = $config['upload_path'] . $data['upload_data']['file_name'];
      // array_push($_SESSION["poe"]["attachment"], $url);
      $this->model->add_attachment_to_db($id, $url);
      $result["status"] = 1;
    }
    echo json_encode($result);
  }

  public function get_head_dept_user()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    $department_id = $_SESSION['request']['department_id'];
    $entities = list_user_in_head_department($department_id);

    echo json_encode($entities);
  }
}
