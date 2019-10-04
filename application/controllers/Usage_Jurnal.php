<?php defined('BASEPATH') or exit('No direct script access allowed');

class Usage_Jurnal extends MY_Controller
{
  protected $module;
  protected $id_item = 0;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['usage_jurnal'];
    $this->load->model($this->module['model'], 'model');
    $this->load->helper($this->module['helper']);
    $this->data['module'] = $this->module;
  }

  public function index()
  {
    $this->authorized($this->module, 'index');

    $this->data['page']['title']            = $this->module['label'];
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] . '/index_data_source');
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array(5, 6);

    $this->data['grid']['order_columns']    = array();
    $this->render_view($this->module['view'] . '/index');
  }
  public function index_data_source()
  {
    if (is_granted($this->module, 'index') === FALSE) {
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {
      $entities = $this->model->getIndex();
      $data     = array();
      $no       = $_POST['start'];
      $quantity     = array();
      $unit_value   = array();
      $total_value  = array();
      $qty_debet    = array();
      $qty_kredit    = array();
      foreach ($entities as $row) {
        $no++;
        $col = array();
        $col[]  = print_number($no);
        $col[]  = print_string($row['no_jurnal']);
        $col[]  = print_string($row['tanggal_jurnal']);
        $col[]  = print_string($row['kode_rekening']);
        $col[]  = print_string($row['jenis_transaksi']);
        $col[]  = print_number($row['kredit'], 2);
        $col[]  = print_number($row['debet'], 2);
        $qty_debet[] = $row['kredit'];
        $qty_kredit[] = $row['kredit'];

        $col['DT_RowId'] = 'row_' . $row['id'];
        $col['DT_RowData']['pkey']  = $row['id'];
        $kode_rekening = $row['kode_rekening'];
        if ($kode_rekening == null) {
          $kode_rekening = "";
        }

        if ($this->has_role($this->module, 'info')) {
          $col['DT_RowAttr']['onClick']     = '$(this).popup();';
          $col['DT_RowAttr']['data-target'] = '#data-modal';
          $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] . '/info/' . $row['id']);
        }

        $data[] = $col;
      }

      $result = array(
        "draw"            => $_POST['draw'],
        "recordsTotal"    => $this->model->countIndex(),
        "recordsFiltered" => $this->model->countIndexFiltered(),
        "data"            => $data,
        "total"           => array(
          5 => print_number(array_sum($qty_debet), 2),
          6 => print_number(array_sum($qty_kredit), 2),
        )
      );
    }

    echo json_encode($result);
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

  public function edit($id)
  {
    $this->authorized($this->module, 'document');

    $entity = $this->model->findById($id);

    // if ($this->model->isValidDocumentQuantity($entity['document_number']) === FALSE) {
    //   $this->session->set_flashdata('alert', array(
    //     'type' => 'danger',
    //     'info' => 'Stock quantity for document ' . $entity['document_number'] . ' has been change. You are not allowed to edit this document. You can adjust stock to sync the quantity.'
    //   ));

    //   redirect(site_url($this->module['route']));
    // }

    $document_number  = $entity['no_jurnal'];

    if (isset($_SESSION['jurnal_usage']) === FALSE) {
      $_SESSION['jurnal_usage']                     = $entity;
      $_SESSION['jurnal_usage']['id']               = $id;
      $_SESSION['jurnal_usage']['edit']             = $entity['no_jurnal'];
      $_SESSION['jurnal_usage']['date']             = $entity['tanggal_jurnal'];
      $_SESSION['jurnal_usage']['document_number']  = $document_number;
    }

    // redirect($this->module['route'] . '/create');
    $this->render_view($this->module['view'] .'/edit');
  }

  public function ajax_editItem($key)
  {
    $this->authorized($this->module, 'document');

    $entity = $_SESSION['jurnal_usage']['items'][$key];

    echo json_encode($entity);
  }

  public function edit_item()
  {
    $this->authorized($this->module, 'document');

    $key = $this->input->post('item_id');
    if (isset($_POST) && !empty($_POST)) {
      //$receipts_items_id = $this->input->post('item_id')
      $_SESSION['jurnal_usage']['items'][$key] = array(
        'group'                   => $this->input->post('group'),
        'description'             => trim(strtoupper($this->input->post('description'))),
        'part_number'             => trim(strtoupper($this->input->post('part_number'))),
        'serial_number'           => trim(strtoupper($this->input->post('serial_number'))),
        'trs_kredit'              => $this->input->post('trs_kredit'),
        'trs_kredit_usd'          => $this->input->post('trs_kredit_usd'),
        'unit_value'              => $this->input->post('unit_value'),
        'stores'                  => trim(strtoupper($this->input->post('stores'))),
        'warehouse'               => $this->input->post('warehouse'),
        'kode_pemakaian'          => $this->input->post('kode_pemakaian'),
        'coa'                     => $this->input->post('coa'),
        'id_jurnal'               => $this->input->post('id_jurnal'),
        'currency'                => $this->input->post('currency'),
        'stock_in_stores_id'      => $this->input->post('stock_in_stores_id'),
        'id_jurnal_detail'        => $this->input->post('id_jurnal_detail'),
        'kode_akun_lawan'        => $this->input->post('kode_akun_lawan'),
      );
      $id_jurnal = $this->input->post('id_jurnal');
    }
    redirect($this->module['route'] . '/edit/'.$id_jurnal);
  }

  public function save()
  {
    // if ($this->input->is_ajax_request() == FALSE)
    //   redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'document') == FALSE) {
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      if (!isset($_SESSION['jurnal_usage']['items']) || empty($_SESSION['jurnal_usage']['items'])) {
        $data['success'] = FALSE;
        $data['message'] = 'Please add at least 1 item!';
      } else {
        $document_number = $_SESSION['jurnal_usage']['document_number'];

        $errors = array();

        if (!empty($errors)) {
          $data['success'] = FALSE;
          $data['message'] = implode('<br />', $errors);
        } else {
          if ($this->model->save()) {
            unset($_SESSION['jurnal_usage']);

            $data['success'] = TRUE;
            $data['message'] = 'Document ' . $document_number . ' has been saved. You will redirected now.';
          } else {
            $data['success'] = FALSE;
            $data['message'] = 'Error while saving this document. Please ask Technical Support.';
          }
        }
      }
    }

    echo json_encode($data);
  }

  public function discard()
  {
    $this->authorized($this->module['permission']['document']);

    unset($_SESSION['jurnal_usage']);

    redirect($this->module['route']);
  }

  public function create()
  {
    $this->authorized($this->module, 'document');

    // if ($category !== NULL) {
    //   $category = urldecode($category);

      $_SESSION['jurnal_usage']['items']            = array();
      // $_SESSION['jurnal_usage']['category']         = $category;
      $_SESSION['jurnal_usage']['document_number']  = receipt_last_number();
      $_SESSION['jurnal_usage']['received_date']    = date('Y-m-d');
      $_SESSION['jurnal_usage']['received_by']      = config_item('auth_person_name');
      $_SESSION['jurnal_usage']['received_from']    = NULL;
      $_SESSION['jurnal_usage']['known_by']         = NULL;
      $_SESSION['jurnal_usage']['approved_by']      = NULL;
      $_SESSION['jurnal_usage']['warehouse']        = config_item('auth_warehouse');
      $_SESSION['jurnal_usage']['notes']            = NULL;

      redirect($this->module['route'] . '/create');
    // }

    if (!isset($_SESSION['receipt']))
      redirect($this->module['route']);

    $this->data['page']['content']    = $this->module['view'] . '/create';
    $this->data['page']['offcanvas']  = $this->module['view'] . '/create_offcanvas_add_item';

    $this->render_view($this->module['view'] . '/create');
  }


  public function detail_tanggal($tanggal, $kode_rekening = "")
  {
    $this->authorized($this->module, 'index');
    $this->data['page']['title']            = $this->module['label'] . " " . $tanggal . " KODE REKENING " . $kode_rekening;
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumnsTanggal());
    $this->data['grid']['data_source']      = site_url($this->module['route'] . '/detail_tanggal_data_source/' . $tanggal . "/" . $kode_rekening);
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array(4, 5);

    $this->data['grid']['order_columns']    = array();
    $this->render_view($this->module['view'] . '/index_detail.php');
  }
  public function detail_tanggal_data_source($tanggal, $kode_rekening = "")
  {
    if (is_granted($this->module, 'index') === FALSE) {
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {
      $entities = $this->model->getIndexTanggal($tanggal, $kode_rekening);
      $data     = array();
      $no       = $_POST['start'];
      $quantity     = array();
      $unit_value   = array();
      $total_value  = array();
      $qty_debet    = array();
      $qty_kredit    = array();
      foreach ($entities as $row) {
        $no++;
        $col = array();
        $col[]  = print_number($no);
        $col[]  = print_string($row['no_jurnal']);
        $col[]  = print_string($row['grn_no']);
        $col[]  = print_string($row['jenis_transaksi']);
        $col[]  = print_string($row['trs_debet']);
        $col[]  = print_string($row['trs_kredit']);
        $col[]  = print_string($row['kode_rekening']);
        $qty_debet[] = $row['trs_debet'];
        $qty_kredit[] = $row['trs_kredit'];

        $col['DT_RowId'] = 'row_' . $row['id'];
        $col['DT_RowData']['pkey']  = $row['id'];

        if ($this->has_role($this->module, 'info')) {
          $col['DT_RowAttr']['onClick']     = '$(this).popup();';
          $col['DT_RowAttr']['data-target'] = '#data-modal';
          $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] . '/info/' . $row['id']);
        }

        $data[] = $col;
      }

      $result = array(
        "draw"            => $_POST['draw'],
        "recordsTotal"    => $this->model->countIndexTanggal($tanggal_jurnal, $kode_rekening),
        "recordsFiltered" => $this->model->countIndexFilteredTanggal($tanggal_jurnal, $kode_rekening),
        "data"            => $data,
        "total"           => array(
          4 => print_number(array_sum($qty_debet), 2),  5 => print_number(array_sum($qty_kredit), 2),
        )
      );
    }

    echo json_encode($result);
  }
}
