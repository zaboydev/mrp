<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pesawat extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['pesawat'];
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function index()
  {
    $this->authorized($this->module, 'index');

    $this->data['page']['title']            = 'Pesawat';
    $this->data['page']['requirement']      = array('datatable', 'form_create', 'form_edit');
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source');
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = NULL;
    $this->data['grid']['order_columns']    = array (
      // 0 => array ( 0 => 1, 1 => 'asc' ),
      // 1 => array ( 0 => 2, 1 => 'asc' ),
      // 2 => array ( 0 => 3, 1 => 'desc' ),
    );

    $this->render_view($this->module['view'] .'/index');
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

      $data = array();
      $no   = $_POST['start'];

      foreach ($entities as $row){
        $no++;
        $col = array();
        $col[] = print_number($no);
        $col[] = print_string($row['nama_pesawat']);
        $col[] = print_string($row['keterangan']);
        $col[] = print_string($row['base']);
        $col[] = '<a href="'.site_url($this->module['route'] . '/index_aircraft_component/' . $row['id']).'" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction btn-sm"><i class="md md-list"></i><small class="top right">Show Component</small></a>';
        $col['DT_RowId'] = 'row_'. $row['id'];
        $col['DT_RowData']['pkey']  = $row['id'];
        $col['DT_RowAttr']['onClick']     = '$(this).popup();';
        $col['DT_RowAttr']['data-target'] = '#data-modal';
        $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] .'/edit/'. $row['id']);

        $data[] = $col;
      }

      $return = array(
          "draw"            => $_POST['draw'],
          "recordsTotal"    => $this->model->countIndex(),
          "recordsFiltered" => $this->model->countIndexFiltered(),
          "data"            => $data,
       );
    }

    echo json_encode($return);
  }

  public function create()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'create') === FALSE){
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to create data!";
    } else {
      $return['type'] = 'success';
      $return['info'] = $this->load->view($this->module['view'] .'/tambah', $this->data, TRUE);
    }

    echo json_encode($return);
  }

  public function edit($id)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'edit') === FALSE){
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to edit this data!";
    } else {
      $entity = $this->model->findById($id);

      $this->data['entity'] = $entity;

      $return['type'] = 'success';
      $return['info'] = $this->load->view($this->module['view'] .'/edit', $this->data, TRUE);
    }

    echo json_encode($return);
  }

  public function save()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'save') === FALSE){
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {
      if ($this->input->post('id')){
          if ($this->model->update($this->input->post('id'))){
            $return['type'] = 'success';
            $return['info'] = print_string($this->input->post('nama_pesawat')).' updated';
          } else {
            $return['type'] = 'danger';
            $return['info'] = 'There are error while updating data. Please try again later.';
          }
      } else {
        if ($this->model->isPesawatExists($this->input->post('nama_pesawat'))){
          $return['type'] = 'danger';
          $return['info'] = 'Duplicate Airplane Name!'.$this->input->post('nama_pesawat').' already exists';
        } else {
          if ($this->model->insert()){
            $return['type'] = 'success';
            $return['info'] = print_string($this->input->post('nama_pesawat')).' created';
          } else {
            $return['type'] = 'danger';
            $return['info'] = 'There are error while updating data. Please try again later.';
          }
        }
      }
    }

    echo json_encode($return);
  }

  public function delete()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'delete') === FALSE){
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to delete this data!";
    } else {
      if ($this->model->delete()){
        $return['type'] = 'success';
        $return['info'] = 'Pesawat ' . $this->input->post('nama_pesawat') .' deleted.';
      } else {
        $return['type'] = 'danger';
        $return['info'] = 'There are error while trying to delete data. Please try again later.';
      }
    }

    echo json_encode($return);
  }

  public function index_aircraft_component($aircraft_id)
  {
    $this->authorized($this->module, 'index');
    $aircraft = $this->model->findById($aircraft_id);

    $this->data['page']['title']            = 'Component Status : '.$aircraft['nama_pesawat'];
    $this->data['page']['requirement']      = array('datatable', 'form_create', 'form_edit');
    $this->data['page']['aircraft_id']      = $aircraft_id;
    $this->data['page']['aircraft_code']    = $aircraft['nama_pesawat'];
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumnsAircraftComponent());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source_aircraft_component/'.$aircraft_id);
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = NULL;
    $this->data['grid']['order_columns']    = array (
      // 0 => array ( 0 => 1, 1 => 'asc' ),
      // 1 => array ( 0 => 2, 1 => 'asc' ),
      // 2 => array ( 0 => 3, 1 => 'desc' ),
    );

    $this->render_view($this->module['view'] .'/component/index');
  }

  public function index_data_source_aircraft_component($aircraft_id)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (is_granted($this->module, 'index') === FALSE){
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {
      $entities = $this->model->getIndexAircraftComponent($aircraft_id);

      $data = array();
      $no   = $_POST['start'];

      foreach ($entities as $row){
        $no++;
        $col = array();
        $col[] = print_number($no);
        $col[] = print_string(strtoupper($row['type']));
        $col[] = print_string($row['description']);
        $col[] = print_string($row['part_number']);
        $col[] = print_string($row['alternate_part_number']);
        $col[] = print_string($row['serial_number']);
        $col[] = print_string($row['interval']);
        $col[] = print_date($row['installation_date']);
        $col[] = print_string($row['installation_by']);
        $col[] = print_string($row['af_tsn']);
        $col[] = print_string($row['equip_tsn']);
        $col[] = print_string($row['tso']);
        $col[] = print_string($row['due_at_af_tsn']);
        $col[] = print_string($row['remaining']);
        $col[] = print_string($row['remarks']);
        
        $col['DT_RowId'] = 'row_'. $row['id'];
        $col['DT_RowData']['pkey']  = $row['id'];
        $col['DT_RowAttr']['onClick']     = '$(this).popup();';
        $col['DT_RowAttr']['data-target'] = '#data-modal';
        $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] .'/edit/'. $row['id']);

        $data[] = $col;
      }

      $return = array(
          "draw"            => $_POST['draw'],
          "recordsTotal"    => $this->model->countIndexAircraftComponent($aircraft_id),
          "recordsFiltered" => $this->model->countIndexFilteredAircraftComponent($aircraft_id),
          "data"            => $data,
       );
    }

    echo json_encode($return);
  }

  public function discard($aircraft_id=NULL)
  {
    $this->authorized($this->module['permission']['document']);

    unset($_SESSION['component']);

    redirect($this->module['route'].'/index_aircraft_component/'.$aircraft_id);
  }

  public function set_installation_date()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['component']['installation_date'] = $_GET['data'];
  }

  public function set_installation_by()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    $_SESSION['component']['installation_by'] = $_GET['data'];
  }

  public function create_component($type = NULL,$aircraft_id=NULL)
  {
    $this->authorized($this->module, 'create_component');


    if ($type !== NULL && $aircraft_id!=NULL){
      $type = urldecode($type);
      $aircraft = $this->model->findById($aircraft_id);

      $_SESSION['component']['items']                 = array();
      $_SESSION['component']['aircraft_id']           = $aircraft_id;
      $_SESSION['component']['aircraft_code']         = $aircraft['nama_pesawat'];
      $_SESSION['component']['base']                  = $aircraft['base'];
      $_SESSION['component']['warehouse']             = $aircraft['warehouse'];
      $_SESSION['component']['type']                  = $type;
      $_SESSION['component']['installation_date']     = date('Y-m-d');
      $_SESSION['component']['installation_by']       = config_item('auth_person_name');
      $_SESSION['component']['source']                = isComponentExist($aircraft['nama_pesawat'])? 'change':'new';

      redirect($this->module['route'] .'/create_component');
    }

    if (!isset($_SESSION['component']))
      redirect($this->module['route']);

    $this->data['page']['content']    = $this->module['view'] .'/component/create';
    $this->data['page']['offcanvas']  = $this->module['view'] .'/component/create_offcanvas_add_item';
    $this->data['page']['title']      = "Create Aircraft Component";
    $this->data['page']['route']      = site_url($this->module['route'] . '/index_aircraft_component/' . $_SESSION['component']['aircraft_id']);

    $this->render_view($this->module['view'] .'/component/create');
  }

  public function select_item($type)
  {
    $this->authorized($this->module, 'create_component');

    $aircraft_code = $_SESSION['component']['aircraft_code'];
    if($_SESSION['component']['source']=='change'){
      $entities = $this->model->searchIssuanceItems();
    }elseif ($type=='new') {
      $entities = $this->model->searchItems();
    }
    

    $this->data['entities'] = $entities;
    $this->data['type']     = $type;
    $this->data['page']['title']            = 'Select Item';

    $this->render_view($this->module['view'] . '/component/select_item');
  }

  public function add_selected_item()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'create_component') == FALSE) {
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      if (isset($_POST['issuance_item_id']) && !empty($_POST['issuance_item_id'])) {
        $_SESSION['component']['items'] = array();

        foreach ($_POST['issuance_item_id'] as $key => $issuance_item_id) {
          
          if($_SESSION['component']['source']=='change'){
            $issuance_item = $this->model->infoIssuanceItem($issuance_item_id);
          }elseif ($_SESSION['component']['source']=='new') {
            $issuance_item = $this->model->infoItem($issuance_item_id);
          }

          $_SESSION['component']['items'][$issuance_item_id] = array(
            'group'                   => $issuance_item['group'],
            'description'             => trim(strtoupper($issuance_item['description'])),
            'part_number'             => trim(strtoupper($issuance_item['part_number'])),
            'alternate_part_number'   => trim(strtoupper($issuance_item['alternate_part_number'])),
            'serial_number'           => trim(strtoupper($issuance_item['serial_number'])),
            'item_id'                 => ($_SESSION['component']['source']=='new')? $issuance_item['id']:$issuance_item['item_id'],
            'installation_date'       => $_SESSION['component']['source']=='change'?$issuance_item['issued_date']:null,
            'issuance_document_number'=> $_SESSION['component']['source']=='change'?trim(strtoupper($issuance_item['document_number'])):null,
            'issuance_item_id'        => $_SESSION['component']['source']=='change'?$issuance_item_id:null,
            'unit'                    => $issuance_item['unit'],
            // 'interval'                => null,
            // 'af_tsn'                  => null,
            // 'equip_tsn'               => null,
            // 'tso'                     => null,
            // 'due_at_af_tsn'           => null,
            // 'remaining'               => null,
            // 'remarks'                 => null,
            // 'quantity'                => $_SESSION['component']['source']=='change'?$issuance_item['issued_quantity']:null,
            // 'condition'               => $_SESSION['component']['source']=='change'?$issuance_item['condition']:null,
            
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
    $this->authorized($this->module, 'create_component');

    $this->data['page']['title']            = 'Update Item';
    $this->render_view($this->module['view'] . '/component/edit_item');
  }

  public function update_selected_item()
  {
    if ($this->input->is_ajax_request() == FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'create_component') == FALSE) {
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      if (isset($_POST) && !empty($_POST)){
        $item_id                  = $this->input->post('item_id');
        $part_number              = $this->input->post('part_number');
        $issuance_document_number = $this->input->post('issuance_document_number');
        $issued_item_id           = $this->input->post('issued_item_id');
        $serial_number            = $this->input->post('serial_number');
        $alternate_part_number    = $this->input->post('alternate_part_number');
        $description              = $this->input->post('description');
        $unit                     = $this->input->post('unit');
        $group                    = $this->input->post('group');
        $previous_component_id    = $this->input->post('previous_component_id');
        $installation_date        = $this->input->post('installation_date');

        $_SESSION['component']['items'] = array();
        foreach ($part_number as $key=>$part_number) {
          $_SESSION['component']['items'][] = array(
            'group'                   => $group[$key],
            'description'             => trim(strtoupper($description[$key])),
            'part_number'             => trim(strtoupper($part_number)),
            'alternate_part_number'   => trim(strtoupper($alternate_part_number[$key])),
            'serial_number'           => trim(strtoupper($serial_number[$key])),
            'item_id'                 => $item_id[$key],
            'issuance_document_number'=> $issuance_document_number[$key],
            'issuance_item_id'        => $issuance_item_id[$key],
            'unit'                    => $unit[$key],
            'previous_component_id'   => $previous_component_id[$key],
            'installation_date'       => $installation_date[$key],
            'interval'                => null,
            'af_tsn'                  => null,
            'equip_tsn'               => null,
            'tso'                     => null,
            'due_at_af_tsn'           => null,
            'remaining'               => null,
            'remarks'                 => null,
            'quantity'                => null,
            'condition'               => null,
          );
        }

        $data['success'] = TRUE;
      } else {
        $data['success'] = FALSE;
        $data['message'] = 'No data to update!';
      }
    }

    echo json_encode($data);
  }

  public function del_item($key)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] .'/denied');

    if (isset($_SESSION['component']['items']))
      unset($_SESSION['component']['items'][$key]);
  }

  public function save_component()
  {
    // if ($this->input->is_ajax_request() == FALSE)
    //   redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'create_component') == FALSE){
      $data['success'] = FALSE;
      $data['message'] = 'You are not allowed to save this Document!';
    } else {
      if (!isset($_SESSION['component']['items']) || empty($_SESSION['component']['items'])){
        $data['success'] = FALSE;
        $data['message'] = 'Please add at least 1 item!';
      } else {
        $errors = array();

        if (!empty($errors)){
          $data['success'] = FALSE;
          $data['message'] = implode('<br />', $errors);
        } else {
          if ($this->model->save_component()){
            unset($_SESSION['component']);

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

  public function info($id)
  {
    $entity = $this->model->findById($id);

    echo json_encode($entity);
  }
}
