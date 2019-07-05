<?php defined('BASEPATH') OR exit('No direct script access allowed');

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
  }

  public function set_doc_number()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (empty($_GET['data']))
      $number = request_last_number();
    else
      $number = $_GET['data'];

    $_SESSION['request']['pr_number'] = $number;
  }

  public function set_pr_date()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['request']['pr_date'] = $_GET['data'];
  }

  public function set_required_date()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['request']['required_date'] = $_GET['data'];
  }

  public function set_created_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['request']['created_by'] = $_GET['data'];
  }

  public function set_suggested_supplier()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['request']['suggested_supplier'] = $_GET['data'];
  }

  public function set_deliver_to()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['request']['deliver_to'] = $_GET['data'];
  }

  public function set_notes()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['request']['notes'] = $_GET['data'];
  }

  public function del_item($key)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (isset($_SESSION['request']['items']))
      unset($_SESSION['request']['items'][$key]);
  }

  public function search_budget()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $category = $_SESSION['request']['category'];
    $entities = $this->model->searchBudget($category);

    foreach ($entities as $key => $value){
      $entities[$key]['label'] = $value['product_name'];
      $entities[$key]['label'] .= ' || PN: ';
      $entities[$key]['label'] .= $value['product_code'];
      $entities[$key]['label'] .= '<small>';
      $entities[$key]['label'] .= 'Minimum Qty: <code>'. number_format($value['minimum_quantity'], 2) .'</code> || ';
      $entities[$key]['label'] .= 'On Hand Qty: <code>'. number_format($value['on_hand_quantity'], 2) .'</code> || ';
      $entities[$key]['label'] .= 'Left Plan Qty: <code>'. number_format($value['maximum_quantity'], 2) .'</code>';
      $entities[$key]['label'] .= '</small>';
    }

    echo json_encode($entities);
  }

  public function search_items_by_part_number()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $category = $_SESSION['request']['category'];
    $entities = $this->model->searchItemsByPartNumber($category);

    foreach ($entities as $key => $value){
      $entities[$key]['label']  = $value['part_number'];
      $entities[$key]['label'] .= ' ';
      $entities[$key]['label'] .= $value['description'];
    }

    echo json_encode($entities);
  }

  public function search_items_by_product_name()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $category = $_SESSION['request']['category'];
    $entities = $this->model->searchItemsByProductName($category);

    echo json_encode($entities);
  }

  public function search_item_groups()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $category = $_SESSION['request']['category'];
    $entities = $this->model->searchItemGroups($category);

    echo json_encode($entities);
  }

  public function get_available_vendors()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $category = $_SESSION['request']['category'];
    $entities = $this->model->getAvailableVendors($category);

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
        $col[] = print_string($row['pr_number']);
        $col[] = print_date($row['pr_date']);
        $col[] = print_date($row['required_date']);
        $col[] = print_string($row['category_name']);
        $col[] = print_string($row['product_name']);
        $col[] = print_string($row['product_code']);
        $col[] = print_string($row['additional_info']);
        $col[] = print_number($row['quantity'], 2);
        $col[] = print_string($row['status']);
        $col[] = print_string($row['suggested_supplier']);
        $col[] = print_string($row['deliver_to']);
        $col[] = print_person_name($row['created_by']);
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
      1   => array( 0 => 3,  1 => 'desc' ),
      2   => array( 0 => 1,  1 => 'desc' ),
      3   => array( 0 => 4,  1 => 'asc' ),
      4   => array( 0 => 5,  1 => 'asc' ),
      5   => array( 0 => 6,  1 => 'asc' ),
      6   => array( 0 => 7,  1 => 'asc' ),
      7   => array( 0 => 8,  1 => 'asc' ),
      8   => array( 0 => 9,  1 => 'asc' ),
      9   => array( 0 => 10,  1 => 'asc' ),
      10  => array( 0 => 11,  1 => 'asc' ),
      11  => array( 0 => 12,  1 => 'asc' ),
      12  => array( 0 => 13,  1 => 'asc' ),
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

    $pdfFilePath = str_replace('/', '-', $entity['pr_number']) .".pdf";

    $this->load->library('m_pdf');

    $pdf = $this->m_pdf->load(null, 'A4-L');
    $pdf->WriteHTML($html);
    $pdf->Output($pdfFilePath, "I");
  }

  public function edit($id)
  {
    $this->authorized($this->module, 'document');

    $entity   = $this->model->findById($id);

    if (isset($_SESSION['request']) === FALSE){
      $_SESSION['request']              = $entity;
      $_SESSION['request']['id']        = $id;
      $_SESSION['request']['edit']      = $entity['pr_number'];
      $_SESSION['request']['category']  = $entity['category_name'];
    }

    redirect($this->module['route'] .'/create');
  }

  public function create($category = NULL)
  {
    $this->authorized($this->module, 'document');

    if ($category !== NULL){
      $category = urldecode($category);

      $_SESSION['request']['items']               = array();
      $_SESSION['request']['category']            = $category;
      $_SESSION['request']['order_number']        = request_last_number();
      $_SESSION['request']['pr_number']           = request_last_number() . request_format_number();
      $_SESSION['request']['pr_date']             = date('Y-m-d');
      $_SESSION['request']['required_date']       = date('Y-m-d');
      $_SESSION['request']['created_by']          = config_item('auth_person_name');
      $_SESSION['request']['suggested_supplier']  = NULL;
      $_SESSION['request']['deliver_to']          = NULL;
      $_SESSION['request']['notes']               = NULL;

      redirect($this->module['route'] .'/create');
    }

    if (!isset($_SESSION['request']))
      redirect($this->module['route']);

    $this->data['page']['content']    = $this->module['view'] .'/create';
    $this->data['page']['offcanvas']  = $this->module['view'] .'/create_offcanvas_add_item';

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
      if (!isset($_SESSION['request']['items']) || empty($_SESSION['request']['items'])){
        $data['success'] = FALSE;
        $data['message'] = 'Please add at least 1 item!';
      } else {
        $pr_number = $_SESSION['request']['pr_number'];

        $errors = array();

        if (isset($_SESSION['request']['edit'])){
          if ($_SESSION['request']['edit'] != $pr_number && $this->model->isDocumentNumberExists($pr_number)){
            $errors[] = 'Duplicate Document Number: '. $pr_number .' !';
          }
        } else {
          if ($this->model->isDocumentNumberExists($pr_number)){
            $errors[] = 'Duplicate Document Number: '. $pr_number .' !';
          }
        }

        if (!empty($errors)){
          $data['success'] = FALSE;
          $data['message'] = implode('<br />', $errors);
        } else {
          if ($this->model->save()){
            unset($_SESSION['request']);

            // SEND EMAIL NOTIFICATION HERE

            $data['success'] = TRUE;
            $data['message'] = 'Document '. $pr_number .' has been saved. You will redirected now.';
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
      $_SESSION['request']['items'][] = array(
        'inventory_monthly_budget_id' => $this->input->post('inventory_monthly_budget_id'),
        'group_name'                  => $this->input->post('group_name'),
        'product_name'                => $this->input->post('product_name'),
        'part_number'                 => $this->input->post('part_number'),
        'unit'                        => $this->input->post('unit'),
        'quantity'                    => $this->input->post('quantity'),
        'price'                       => $this->input->post('price'),
        'total'                       => $this->input->post('total'),
        'additional_info'             => $this->input->post('additional_info'),
      );
    }

    redirect($this->module['route'] .'/create');
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
      redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'delete') === FALSE){
      $alert['type']  = 'danger';
      $alert['info']  = 'You are not allowed to delete this data!';
    } else {
      $entity = $this->model->findById($this->input->post('id'));

      if ($this->model->isValidDocumentQuantity($entity['pr_number']) === FALSE){
        $alert['type']  = 'danger';
        $alert['info']  = 'Stock quantity for document ' . $entity['pr_number'] . ' has been change. You are not allowed to delete this document. You can adjust stock to sync the quantity.';
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
