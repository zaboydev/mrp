<?php defined('BASEPATH') or exit('No direct script access allowed');

class Item extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['item'];
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function index()
  {
    $this->authorized($this->module, 'index');

    $this->data['page']['title']        = 'Item';
    $this->data['page']['requirement']  = array('datatable', 'form_create', 'form_edit');
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] . '/index_data_source');
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = NULL;
    $this->data['grid']['order_columns']    = array(
      0 => array(0 => 1, 1 => 'asc'),
      1 => array(0 => 2, 1 => 'asc'),
      2 => array(0 => 3, 1 => 'asc'),
      3 => array(0 => 4, 1 => 'asc'),
      4 => array(0 => 5, 1 => 'asc'),
      5 => array(0 => 6, 1 => 'asc'),
      6 => array(0 => 7, 1 => 'asc'),
      // 7 => array ( 0 => 8, 1 => 'desc' ),
      // 8 => array ( 0 => 9, 1 => 'desc' ),
    );

    $this->render_view($this->module['view'] . '/index');
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

      $data = array();
      $no   = $_POST['start'];

      foreach ($entities as $row) {
        $no++;
        $col = array();
        $col[] = print_number($no);
        // $col[] = print_string($row['id']);        
        $col[] = print_string($row['part_number']);
        $col[] = print_string($row['description']);
        $col[] = print_string($row['kode_stok']);
        //$col[] = print_string($row['kode_pemakaian']);
        $col[] = print_string($row['alternate_part_number']);
        // $col[] = print_string($row['serial_number']);
        // $col[] = print_string($row['category']);
        $col[] = print_string($row['group']);
        $col[] = print_number($row['minimum_quantity'], 2);
        $col[] = print_number($this->countOnhand($row['part_number']),2);
        $col[] = print_string($row['unit']);
        // $col[] = print_date($row['updated_at']);

        $col['DT_RowId'] = 'row_' . $row['id'];
        $col['DT_RowData']['pkey']  = $row['id'];
        $col['DT_RowAttr']['onClick']     = '$(this).popup();';
        $col['DT_RowAttr']['data-target'] = '#data-modal';
        $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] . '/edit/' . $row['id']);

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
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'create') === FALSE) {
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to create data!";
    } else {
      $return['type'] = 'success';
      $return['info'] = $this->load->view($this->module['view'] . '/create', $this->data, TRUE);
    }

    echo json_encode($return);
  }

  public function edit($id)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'edit') === FALSE) {
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to edit this data!";
    } else {
      $entity = $this->model->findById($id);

      $this->data['entity'] = $entity;

      $return['type'] = 'success';
      $return['info'] = $this->load->view($this->module['view'] . '/edit', $this->data, TRUE);
    }

    echo json_encode($return);
  }

  public function save()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'save') === FALSE) {
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {
      if ($this->input->post('id')) {
        // if ($this->model->isItemDescriptionExists($this->input->post('description'), $this->input->post('description_exception'))) {
        //   $return['type'] = 'danger';
        //   $return['info'] = 'Duplicate Item! Description ' . $this->input->post('description') . ' already exists.';
        // } else
        if ($this->model->isPartNumberExists($this->input->post('part_number'), $this->input->post('serial_number'), $this->input->post('part_number_exception'), $this->input->post('serial_number_exception'))) {
          $return['type'] = 'danger';
          $return['info'] = 'Duplicate Item! Part Number ' . $this->input->post('part_number') . ' & Serial ' . $this->input->post('serial_number') . ' already exists.';
        } else {
          if ($this->model->update($this->input->post('id'))) {
            $return['type'] = 'success';
            $return['info'] = 'Item with part number ' . $this->input->post('part_number') . ' updated.';
          } else {
            $return['type'] = 'danger';
            $return['info'] = 'There are error while updating data. Please try again later.';
          }
        }
      } else {
        // if ($this->model->isItemDescriptionExists($this->input->post('description'))) {
        //   $return['type'] = 'danger';
        //   $return['info'] = 'Duplicate Description! Description ' . $this->input->post('description') . ' already exists.';
        // } else
        if ($this->model->isPartNumberExists($this->input->post('part_number'), $this->input->post('serial_number'))) {
          $return['type'] = 'danger';
          $return['info'] = 'Duplicate Part Number! Part Number ' . $this->input->post('part_number') . ' already exists.';
        } else {
          if ($this->model->insert()) {
            $return['type'] = 'success';
            $return['info'] = 'Item with part number ' . $this->input->post('part_number') . ' created.';
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
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'delete') === FALSE) {
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to delete this data!";
    } else {
      if ($this->model->delete()) {
        $return['type'] = 'success';
        $return['info'] = 'Item with part number ' . $this->input->post('part_number') . ' deleted.';
      } else {
        $return['type'] = 'danger';
        $return['info'] = 'There are error while trying to delete data. Please try again later.';
      }
    }

    echo json_encode($return);
  }

  public function import_2()
  {
    $this->authorized($this->module, 'import');

    //... load library to build form and validate it
    $this->load->library('form_validation');

    /**
     * Processing data
     * if form submitted
     */
    if (isset($_POST) && !empty($_POST)) {
      //... set rules of validation
      $this->form_validation->set_rules('delimiter', 'Value Delimiter', 'trim|required');

      /**
       * Processing validation
       * Run OK
       */
      if ($this->form_validation->run() === TRUE) {
        $file       = $_FILES['userfile']['tmp_name'];
        $delimiter  = $this->input->post('delimiter');

        //... open file
        if (($handle = fopen($file, "r")) !== FALSE) {
          $row     = 1;
          $data    = array();
          $errors  = array();
          $user_id = array();
          $index   = 0;
          fgetcsv($handle); // skip first line (as header)

          //... parsing line
          while (($col = fgetcsv($handle, 1024, $delimiter)) !== FALSE) {
            $row++;

            /******************
             * CHECK COLUMN 0
             ******************/
            $group = trim(strtoupper($col[0]));
            $data[$row]['group'] = $group;

            if ($group == '')
              $errors[] = 'Line ' . $row . ': group is null!';

            if ($this->model->isItemGroupExists($group) == FALSE)
              $errors[] = 'Line ' . $row . ': Unknown item group ' . $group;

            /***************************************************
             * CHECK COLUMN 1
             ***********************************/
            $description = (trim($col[1]) == '') ? null : trim(strtoupper($col[1]));
            $data[$row]['description'] = $description;

            if ($description === null)
              $errors[] = 'Line ' . $row . ': description is null!';

            /***************************************************
             * CHECK COLUMN 2
             ***********************************/
            $part_number = (trim($col[2]) == '') ? null : trim(strtoupper($col[2]));
            $data[$row]['part_number'] = $part_number;

            if ($part_number === null)
              $errors[] = 'Line ' . $row . ': part number is null!';

            if (strlen($part_number) > 50)
              $errors[] = 'Line ' . $row . ': part number is too long!';

            /******************************************************
             * CHECK COLUMN 3
             *******************************************/
            $alternate_part_number = (trim($col[3]) == '') ? null : trim(strtoupper($col[3]));
            $data[$row]['alternate_part_number'] = $alternate_part_number;

            if (strlen($alternate_part_number) > 50)
              $errors[] = 'Line ' . $row . ': alt part number is too long!';

            /******************************************************
             * CHECK COLUMN 4
             *******************************************/
            $minimum_quantity = (trim($col[4]) == '') ? 0 : trim(strtoupper($col[4]));
            $data[$row]['minimum_quantity'] = $minimum_quantity;

            if (is_numeric($minimum_quantity) === FALSE)
              $errors[] = 'Line ' . $row . ': minimum quantity is not numeric!';

            /******************
             * CHECK COLUMN 5
             ******************/
            $unit = trim(strtoupper($col[5]));
            $data[$row]['unit'] = $unit;

            if ($unit == '')
              $errors[] = 'Line ' . $row . ': Unit is null!';

            if ($this->model->isItemUnitExists($unit) == FALSE)
              $errors[] = 'Line ' . $row . ': Unknown unit ' . $unit;

            /******************
             * CHECK COLUMN 6
             ******************/
            $serial_number = trim(strtoupper($col[6]));
            $data[$row]['serial_number'] = $serial_number;

            if ($serial_number == '')
              $errors[] = 'Line ' . $row . ': Serial Number is null!';

            // if ($this->model->isItemUnitExists($unit) == FALSE)
            //   $errors[] = 'Line '. $row .': Unknown unit '. $unit;

            /******************
             * CHECK COLUMN 7
             ******************/
            $warehouse = trim(strtoupper($col[7]));
            $data[$row]['warehouse'] = $warehouse;

            if ($warehouse == '')
              $errors[] = 'Line ' . $row . ': Warehouse is null!';

            /******************
             * CHECK COLUMN 8
             ******************/
            $stores = trim(strtoupper($col[8]));
            $data[$row]['stores'] = $stores;

            if ($stores == '')
              $errors[] = 'Line ' . $row . ': stores is null!';

            /******************
             * CHECK COLUMN 9
             ******************/
            $condition = trim(strtoupper($col[9]));
            $data[$row]['condition'] = $condition;

            if ($condition == '')
              $errors[] = 'Line ' . $row . ': condition is null!';


            /**************************************************************
             * CHECK DUPLICATE PART NUMBER
             ******************************************/
            if ($this->model->isItemExists($part_number))
              $errors[] = 'Line ' . $row . ': Duplicate part number ' . $part_number;

            /* CHECK COLUMN 8
             ******************/
            $stores = trim(strtoupper($col[8]));
            $data[$row]['stores'] = $stores;

            if ($stores == '')
              $errors[] = 'Line ' . $row . ': stores is null!';

            /******************
             * CHECK COLUMN 9
             ******************/
            $condition = trim(strtoupper($col[9]));
            $data[$row]['condition'] = $condition;
          }
          fclose($handle);

          if (empty($errors)) {
            /**
             * Insert into user table
             */
            if ($this->model->import($data)) {
              //... send message to view
              $this->session->set_flashdata('alert', array(
                'type' => 'success',
                'info' => count($data) . " data has been imported!"
              ));

              redirect($this->module['route']);
            }
          } else {
            foreach ($errors as $key => $value) {
              $err[] = "\n#" . $value;
            }

            $this->session->set_flashdata('alert', array(
              'type' => 'danger',
              'info' => "There are errors on data\n#" . implode("\n#", $errors)
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

  public function price()
  {
    // ========================= ACCESS DENIED ========================== //
    $this->authorized($this->module['permission']['price']);

    // ========================= ACCESS GRANTED ========================= //

    //... load library to build form and validate it
    $this->load->library('form_validation');

    /**
     * Processing data
     * if form submitted
     */
    if (isset($_POST) && !empty($_POST)) {
      //... set rules of validation
      $this->form_validation->set_rules('delimiter', 'Value Delimiter', 'trim|required');

      /**
       * Processing validation
       * Run OK
       */
      if ($this->form_validation->run() === TRUE) {
        $file       = $_FILES['userfile']['tmp_name'];
        $delimiter  = $this->input->post('delimiter');

        //... open file
        if (($handle = fopen($file, "r")) !== FALSE) {
          $row     = 1;
          $data    = array();
          $errors  = array();
          $user_id = array();
          $index   = 0;
          fgetcsv($handle); // skip first line (as header)

          //... parsing line
          while (($col = fgetcsv($handle, 1024, $delimiter)) !== FALSE) {
            $row++;

            /***************************************************
             * CHECK COLUMN 2
             ***********************************/
            $part_number = (trim($col[2]) == '') ? null : trim(strtoupper($col[2]));
            $data[$row]['part_number'] = $part_number;

            if ($part_number === null)
              $errors[] = 'Line ' . $row . ': part number is null!';

            if (strlen($part_number) > 20)
              $errors[] = 'Line ' . $row . ': part number is too long!';

            if ($this->model->is_duplicate($part_number) == FALSE)
              $errors[] = 'Line ' . $row . ': Unknown part number ' . $part_number;

            /******************************************************
             * CHECK COLUMN 7
             *******************************************/
            $price = (trim($col[7]) == '') ? 0 : trim($col[7]);
            $data[$row]['price'] = $price;

            if (is_numeric($price) === FALSE)
              $errors[] = 'Line ' . $row . ': price is not numeric!';
          }
          fclose($handle);

          if (empty($errors)) {
            /**
             * Insert into user table
             */
            if ($this->model->import_item_price($data)) {
              //... send message to view
              $this->session->set_flashdata('alert', array(
                'type' => 'success',
                'info' => count($data) . " data has been imported!"
              ));

              redirect($this->module['route']);
            }
          } else {
            foreach ($errors as $key => $value) {
              $err[] = "\n#" . $value;
            }

            $this->session->set_flashdata('alert', array(
              'type' => 'danger',
              'info' => "There are errors on data\n#" . implode("\n#", $errors)
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

    //... set view data
    $this->data['page_title'] = lang('page_title_price');
    $this->data['page_content'] = $this->module['view'] . '/import';

    $this->render_view();
  }

  public function import()
  {
    $this->authorized($this->module, 'import');

    //... load library to build form and validate it
    $this->load->library('form_validation');

    /**
     * Processing data
     * if form submitted
     */
    if (isset($_POST) && !empty($_POST)) {
      //... set rules of validation
      $this->form_validation->set_rules('delimiter', 'Value Delimiter', 'trim|required');

      /**
       * Processing validation
       * Run OK
       */
      if ($this->form_validation->run() === TRUE) {
        $file       = $_FILES['userfile']['tmp_name'];
        $delimiter  = $this->input->post('delimiter');

        //... open file
        if (($handle = fopen($file, "r")) !== FALSE) {
          $row     = 1;
          $data    = array();
          $errors  = array();
          $user_id = array();
          $index   = 0;
          fgetcsv($handle); // skip first line (as header)

          //... parsing line
          while (($col = fgetcsv($handle, 1024, $delimiter)) !== FALSE) {
            $row++;

            /******************
             * CHECK COLUMN 0
             ******************/
            // $group = trim(strtoupper($col[0]));
            // $data[$row]['group'] = $group;

            // if ($group == '')
            //   $errors[] = 'Line ' . $row . ': group is null!';

            // if ($this->model->isItemGroupExists($group) == FALSE)
            //   $errors[] = 'Line ' . $row . ': Unknown item group ' . $group;

            /***************************************************
             * CHECK COLUMN 1
             ***********************************/
            $description = (trim($col[0]) == '') ? null : trim(strtoupper($col[0]));
            $data[$row]['description'] = $description;

            if ($description === null)
              $errors[] = 'Line ' . $row . ': description is null!';

            /***************************************************
             * CHECK COLUMN 2
             ***********************************/
            $part_number = (trim($col[1]) == '') ? null : trim(strtoupper($col[1]));
            $data[$row]['part_number'] = $part_number;

            if ($part_number === null)
              $errors[] = 'Line ' . $row . ': part number is null!';

            if (strlen($part_number) > 50)
              $errors[] = 'Line ' . $row . ': part number is too long!';


            $unit = trim(strtoupper($col[2]));
            $data[$row]['unit'] = $unit;

            if ($unit == '')
              $errors[] = 'Line ' . $row . ': Unit is null!';

            if ($this->model->isItemUnitExists($unit) == FALSE)
              $errors[] = 'Line ' . $row . ': Unknown unit ' . $unit;

            $group = trim(strtoupper($col[3]));
            $data[$row]['group'] = $group;

            if ($group == '')
              $errors[] = 'Line ' . $row . ': group is null!';


            $alternate_part_number = trim(strtoupper($col[4]));
            $data[$row]['alternate_part_number'] = $alternate_part_number;

            // if ($alternate_part_number == '')
            //   $errors[] = 'Line ' . $row . ': alternate_part_number is null!';

            $minimum_quantity = trim(strtoupper($col[5]));
            $data[$row]['minimum_quantity'] = $minimum_quantity;

            if ($minimum_quantity == '')
              $errors[] = 'Line ' . $row . ': minimum_quantity is null!';

            $kode_pemakaian = trim(strtoupper($col[6]));
            $data[$row]['kode_pemakaian'] = $kode_pemakaian;

            // if ($kode_pemakaian == '')
            //   $errors[] = 'Line ' . $row . ': kode_pemakaian is null!';

            $current_price = trim(strtoupper($col[7]));
            $data[$row]['current_price'] = $current_price;

            // if ($current_price == '')
            //   $errors[] = 'Line ' . $row . ': current_price is null!';

            $kode_stok = trim(strtoupper($col[8]));
            $data[$row]['kode_stok'] = $kode_stok;

            // if ($current_price == '')
            //   $errors[] = 'Line ' . $row . ': current_price is null!';


          }
          fclose($handle);

          if (empty($errors)) {
            /**
             * Insert into user table
             */
            if ($this->model->import($data)) {
              //... send message to view
              $this->session->set_flashdata('alert', array(
                'type' => 'success',
                'info' => count($data) . " data has been imported!"
              ));

              redirect($this->module['route']);
            }
          } else {
            foreach ($errors as $key => $value) {
              $err[] = "\n#" . $value;
            }

            $this->session->set_flashdata('alert', array(
              'type' => 'danger',
              'info' => "There are errors on data\n#" . implode("\n#", $errors)
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

  public function countOnhand($part_number)
  {
    $return = $this->model->currentStock($part_number)->sum;
    return $return;
  }
}
