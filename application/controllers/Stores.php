<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stores extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['stores'];
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function index()
  {
    $this->authorized($this->module, 'index');

    $this->data['page']['title']        = 'Stores';
    $this->data['page']['requirement']  = array('datatable', 'form_create', 'form_edit');
    $this->data['grid']['column']          = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']     = site_url($this->module['route'] .'/index_data_source');
    $this->data['grid']['fixed_columns']   = 2;
    $this->data['grid']['summary_columns'] = NULL;
    $this->data['grid']['order_columns']   = array (
      0 => array ( 0 => 1, 1 => 'asc' ),
      1 => array ( 0 => 2, 1 => 'asc' ),
      2 => array ( 0 => 3, 1 => 'asc' ),
      3 => array ( 0 => 4, 1 => 'desc' )
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
        $col[] = print_string($row['stores']);
        $col[] = print_string($row['warehouse']);
        $col[] = print_string($row['category']);
        $col[] = print_string($row['notes'], '');
        $col[] = print_date($row['updated_at']);
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
      $return['info'] = $this->load->view($this->module['view'] .'/create', $this->data, TRUE);
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
        if ($this->model->isDuplicateStores($this->input->post('stores_exception'))){
          $return['type'] = 'danger';
          $return['info'] = 'Duplicate Stores! Stores '. $this->input->post('stores') .' already exists.';
        } else {
          if ($this->model->update($this->input->post('id'))){
            $return['type'] = 'success';
            $return['info'] = 'Stores ' . $this->input->post('stores') .' updated.';
          } else {
            $return['type'] = 'danger';
            $return['info'] = 'There are error while updating data. Please try again later.';
          }
        }
      } else {
        if ($this->model->isDuplicateStores()){
          $return['type'] = 'danger';
          $return['info'] = 'Duplicate Stores! Stores '. $this->input->post('stores') .' already exists.';
        } else {
          if ($this->model->insert()){
            $return['type'] = 'success';
            $return['info'] = 'Stores ' . $this->input->post('stores') .' created.';
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
        $return['info'] = 'Stores ' . $this->input->post('stores') .' deleted.';
      } else {
        $return['type'] = 'danger';
        $return['info'] = 'There are error while trying to delete data. Please try again later.';
      }
    }

    echo json_encode($return);
  }

  public function import()
  {
    // ========================= ACCESS DENIED ========================== //
    $this->authorized($this->module, 'import');

    // ========================= ACCESS GRANTED ========================= //

    //... load library to build form and validate it
    $this->load->library('form_validation');

    /**
     * Processing data
     * if form submitted
     */
    if (isset($_POST) && !empty($_POST)){
      //... set rules of validation
      $this->form_validation->set_rules('delimiter', 'Value Delimiter', 'trim|required');

      /**
       * Processing validation
       * Run OK
       */
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

            $stores     = (clean_import($col[0]) == '')   ? NULL  : clean_import(strtoupper($col[0]));
            $warehouse  = (clean_import($col[1]) == '')   ? NULL  : clean_import(strtoupper($col[1]));
            $category   = (clean_import($col[2]) == '')   ? NULL  : clean_import(strtoupper($col[2]));
            $notes      = (clean_import($col[3]) == '')   ? NULL  : clean_import(strtoupper($col[3]));

            if ($stores == NULL)
              $errors[] = 'Line '. $row .': Stores is null!';

            if (strlen($stores) > 20)
              $errors[] = 'Line '. $row .': Stores is too long!';

            if ($this->model->isStoresExists($stores))
              $errors[] = 'Line '. $row .': Duplicate stores '. $stores;

            if ($warehouse == NULL)
              $errors[] = 'Line '. $row .': warehouse is null!';

            if ($this->model->isWarehouseExists($warehouse) == FALSE)
              $errors[] = 'Line '. $row .': Unknown warehouse '. $warehouse;

            if ($category == NULL)
              $errors[] = 'Line '. $row .': Category is null!';

            if ($this->model->isItemCategoryExists($category) == FALSE)
              $errors[] = 'Line '. $row .': Unknown category '. $category;

            $data[$row]['stores'] = $stores;
            $data[$row]['warehouse'] = $warehouse;
            $data[$row]['category'] = $category;
            $data[$row]['notes'] = $notes;
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

    //... set view data
    // $this->data['page_title'] = lang('page_title_import');
    // $this->data['page_content'] = $this->module['view'] .'/import';
    // $this->render_view();
  }
}
