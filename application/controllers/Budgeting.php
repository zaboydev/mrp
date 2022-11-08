<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Budgeting extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->module = $this->modules['budgeting'];
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
    $this->load->model('budget_cot_model', '', true);
  }

  public function index()
  {
    if (isset($_POST['year']) && $_POST['year'] !== NULL) {
      $year = $_POST['year'];
    } else {
      $year = date('Y');
    }
    $this->data['year']        = $year;
    $this->data['cotONProcess']        = cotProcessExists($year);
    $this->data['page']['title']            = $this->module['label'] . ' ' . $year;
    $this->data['page']['requirement']      = array('datatable');
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] . '/index_data_source/' . $year);
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array(9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36);
    $this->data['grid']['order_columns']    = array(

      //10 => array ( 0 => 11, 1 => 'asc' ),
      // 11 => array ( 0 => 12, 1 => 'asc' ),
      // 12 => array ( 0 => 13, 1 => 'asc' ),
      // 13 => array ( 0 => 14, 1 => 'asc' ),
    );
    $this->render_view($this->module['view'] . '/index');
  }
  public function index_data_source($year)
  {
    $this->authorized($this->module, 'index');
    if ($year !== NULL) {
      $year = $year;
    } else {
      $year = date('Y');
    }
    $entities = $this->model->index($year);

    $onhand     = array();
    $qty_requirement     = array();
    $total_val     = array();
    $total_qty     = array();
    $jan_val     = array();
    $feb_val     = array();
    $mar_val     = array();
    $apr_val     = array();
    $mei_val     = array();
    $jun_val     = array();
    $jan_qty     = array();
    $feb_qty     = array();
    $mar_qty     = array();
    $apr_qty     = array();
    $mei_qty     = array();
    $jun_qty     = array();
    $jul_val     = array();
    $ags_val     = array();
    $sep_val     = array();
    $okt_val     = array();
    $nov_val     = array();
    $des_val     = array();
    $jul_qty     = array();
    $ags_qty     = array();
    $sep_qty     = array();
    $okt_qty     = array();
    $nov_qty     = array();
    $des_qty     = array();
    $no       = $_POST['start'];

    foreach ($entities as $row) {
      $no++;
      $col = array();
      if ($row->status == "WAITING") {
        if (config_item('auth_role') == 'CHIEF OF MAINTANCE' || config_item('auth_role') == 'SUPER ADMIN') {
          $col[] = '<input type="checkbox" id="cb_' . $row->id_cot . '"  data-id="' . $row->id_cot . '" name="" style="display: inline;">';
        }
      } else {
        $col[] = print_number($no);
      }
      $col[] = print_string($row->item_description);
      $col[] = print_string($row->serial_number);
      $col[] = print_string($row->part_number);
      $col[] = print_string($row->group_name);
      $col[] = print_string($row->category);
      $col[] = print_string($row->year);
      $col[] = print_string($row->status);
      $col[] = print_number($row->current_price);
      $col[] = print_number($row->onhand);
      $col[] = print_number($row->qty_requirement < 0 ? 0 : $row->qty_requirement);
      $col[] = print_number($row->jan_val < 0 ? 0 : $row->jan_val);
      $col[] = print_number($row->jan_qty < 0 ? 0 : $row->jan_qty);
      $col[] = print_number($row->feb_val < 0 ? 0 : $row->feb_val);
      $col[] = print_number($row->feb_qty < 0 ? 0 : $row->feb_qty);
      $col[] = print_number($row->mar_val < 0 ? 0 : $row->mar_val);
      $col[] = print_number($row->mar_qty < 0 ? 0 : $row->mar_qty);
      $col[] = print_number($row->apr_val < 0 ? 0 : $row->apr_val);
      $col[] = print_number($row->apr_qty < 0 ? 0 : $row->apr_qty);
      $col[] = print_number($row->mei_val < 0 ? 0 : $row->mei_val);
      $col[] = print_number($row->mei_qty < 0 ? 0 : $row->mei_qty);
      $col[] = print_number($row->jun_val < 0 ? 0 : $row->jun_val);
      $col[] = print_number($row->jun_qty < 0 ? 0 : $row->jun_qty);
      $col[] = print_number($row->jul_val < 0 ? 0 : $row->jul_val);
      $col[] = print_number($row->jul_qty < 0 ? 0 : $row->jul_qty);
      $col[] = print_number($row->ags_val < 0 ? 0 : $row->ags_val);
      $col[] = print_number($row->ags_qty < 0 ? 0 : $row->ags_qty);
      $col[] = print_number($row->sep_val < 0 ? 0 : $row->sep_val);
      $col[] = print_number($row->sep_qty < 0 ? 0 : $row->sep_qty);
      $col[] = print_number($row->oct_val < 0 ? 0 : $row->oct_val);
      $col[] = print_number($row->oct_qty < 0 ? 0 : $row->oct_qty);
      $col[] = print_number($row->nov_val < 0 ? 0 : $row->nov_val);
      $col[] = print_number($row->nov_qty < 0 ? 0 : $row->nov_qty);
      $col[] = print_number($row->des_val < 0 ? 0 : $row->des_val);
      $col[] = print_number($row->des_qty < 0 ? 0 : $row->des_qty);
      $col[] = print_number($row->total_val < 0 ? 0 : $row->total_val);
      $col[] = print_number($row->total_qty < 0 ? 0 : $row->total_qty);

      // $unit_value[]   = $row['received_unit_value'];
      $onhand[]  = $row->onhand;
      $qty_requirement[]  = $row->qty_requirement;
      $total_val[]  = $row->total_val;
      $total_qty[]  = $row->total_qty;
      $jan_val[] = $row->jan_val;
      $feb_val[] = $row->feb_val;
      $mar_val[] = $row->mar_val;
      $apr_val[] = $row->apr_val;
      $mei_val[] = $row->mei_val;
      $jun_val[] = $row->jun_val;
      $jan_qty[] = $row->jan_qty;
      $feb_qty[] = $row->feb_qty;
      $mar_qty[] = $row->mar_qty;
      $apr_qty[] = $row->apr_qty;
      $mei_qty[] = $row->mei_qty;
      $jun_qty[] = $row->jun_qty;
      $jul_val[] = $row->jul_val;
      $ags_val[] = $row->ags_val;
      $sep_val[] = $row->sep_val;
      $okt_val[] = $row->okt_val;
      $nov_val[] = $row->nov_val;
      $des_val[] = $row->des_val;
      $jul_qty[] = $row->jul_qty;
      $ags_qty[] = $row->ags_qty;
      $sep_qty[] = $row->sep_qty;
      $okt_qty[] = $row->okt_qty;
      $nov_qty[] = $row->nov_qty;
      $des_qty[] = $row->des_qty;
      $col['DT_RowId'] = 'row_' . $row->id_cot;
      $col['DT_RowData']['pkey'] = $row->id_cot;
      $col['DT_RowAttr']['class']     = 'edit';
      $col['DT_RowAttr']['data-id']     = $row->id_cot;
      $col['DT_RowAttr']['data-status']     = $row->status;
      $col['DT_RowAttr']['data-target'] = '#data-modal';
      $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] . '/edit/' . $row->id_cot);
      $data[] = $col;
    }
    $result = array(
      "draw"                => $_POST['draw'],
      "recordsTotal"        => $this->model->countIndex($year),
      "recordsFiltered"     => $this->model->countFilteredIndex($year),
      "data"                => $data,
      "total"               => array(
        9   => print_number(array_sum($onhand), 2),
        10  => print_number(array_sum($qty_requirement), 2),
        11  => print_number(array_sum($jan_val), 2),
        12  => print_number(array_sum($jan_qty), 2),
        13  => print_number(array_sum($feb_val), 2),
        14  => print_number(array_sum($feb_qty), 2),
        15  => print_number(array_sum($mar_val), 2),
        16  => print_number(array_sum($mar_qty), 2),
        17  => print_number(array_sum($apr_val), 2),
        18  => print_number(array_sum($apr_qty), 2),
        19  => print_number(array_sum($mei_val), 2),
        20  => print_number(array_sum($mei_qty), 2),
        21  => print_number(array_sum($jun_val), 2),
        22  => print_number(array_sum($jun_qty), 2),
        23  => print_number(array_sum($jul_val), 2),
        24  => print_number(array_sum($jul_qty), 2),
        25  => print_number(array_sum($ags_val), 2),
        26  => print_number(array_sum($ags_qty), 2),
        27  => print_number(array_sum($sep_val), 2),
        28  => print_number(array_sum($sep_qty), 2),
        29  => print_number(array_sum($okt_val), 2),
        30  => print_number(array_sum($okt_qty), 2),
        31  => print_number(array_sum($nov_val), 2),
        32  => print_number(array_sum($nov_qty), 2),
        33  => print_number(array_sum($des_val), 2),
        34  => print_number(array_sum($des_qty), 2),
        35  => print_number(array_sum($total_val), 2),
        36  => print_number(array_sum($total_qty), 2),
      )
    );
    echo json_encode($result);
  }

  public function print_budget($year)
  {
    $data['data_budget'] = $this->model->select_budget($year);
    $this->render_view($this->module['view'] . '/print', $data);
  }

  public function approve()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');
    $id_cots = $this->input->post('id_cots');
    $id_cots = str_replace("|", "", $id_cots);
    $id_cots = substr($id_cots, 0, -1);
    $id_cots = explode(",", $id_cots);
    $approve = $this->model->approveData($id_cots);
    if ($approve['update'] > 0) {
      $this->session->set_flashdata('alert', array(
        'type' => 'success',
        'info' => $approve['update'] . " data has been update!"
      ));
    }
    if (sizeof($approve['error']) > 0) {
      $this->session->set_flashdata('alert', array(
        'type' => 'danger',
        'info' => "There are " . sizeof($approve['error']) . " errors\n#" . implode("\n#", $approve['error'])
      ));
    }
    $result['status'] = "success";
    echo json_encode($result);
  }
  public function edit($id)
  {
    // if ($this->input->is_ajax_request() === FALSE)
    //  redirect($this->modules['secure']['route'] .'/denied');
    $entity   = $this->model->cotById($id);
    // $hours = $this->model->cotHourById($id);
    $hours = $this->model->cotQtyById($id);
    $this->data['id_cot'] = $id;
    $this->data['entity'] = $entity;
    $this->data['hour'] = $hours;
    $return['type'] = 'success';
    $return['info'] = $this->load->view($this->module['view'] . '/edit', $this->data, TRUE);
    echo json_encode($return);
  }
  public function save_budget()
  {
    // if ($this->input->is_ajax_request() === FALSE)
    //  redirect($this->modules['secure']['route'] .'/denied');
    //$edit = $this->model->updateBudget(); 
    $result["status"] = "failed";
    $onhand = $this->input->post('onhand');
    $id_cot = $this->input->post('id_cot');
    $item_id = $this->input->post('item_id');
    $updateOnhand = $this->model->updateOnhand($onhand, $id_cot);
    if ($updateOnhand) {
      $cot_hours = $this->input->post('cot_hour');
      $qty_requirement = $this->input->post('qty_requirement');
      $new_req = $qty_requirement - $onhand;
      $items = $this->model->itemById($item_id);
      $price = $items->current_price;
      $updateBudget = $this->model->updateBudget($id_cot, $cot_hours, $new_req, $price);
      if ($updateBudget == 12) {
        $result["status"] = "success";
        $this->session->set_flashdata('alert', array(
          'type' => 'success',
          'info' => $result['update'] . " data has been update!"
        ));
      }
    }
    echo json_encode($result);
  }
  public function update_onhand()
  {
    $result['status'] = "failed";
    $id_cot = $this->input->post('id_cot');
    $item = $this->model->cotById($id_cot);
    $id_stock = getStockId($item->id, "SERVICEABLE");
    $onhand = $this->budget_cot_model->countOnhand($item->item_part_number);
    if ($onhand != null) {
      $result['status'] = "success";
      $result['onhand'] = $onhand->sum;
    }
    echo json_encode($result);
  }

  public function delete_ajax()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'delete') === FALSE) {
      $alert['type']  = 'danger';
      $alert['info']  = 'You are not allowed to delete this data!';
    } else {
      $entity = $this->model->cotById($this->input->post('id'));

      if ($this->model->delete()) {
        $alert['type'] = 'success';
        $alert['info'] = 'Data deleted.';
        $alert['link'] = site_url($this->module['route']);
      } else {
        $alert['type'] = 'danger';
        $alert['info'] = 'There are error while deleting data. Please try again later.';
      }
    }

    echo json_encode($alert);
  }

  public function pengajuan($year)
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if (is_granted($this->module, 'delete') === FALSE) {
      $alert['type']  = 'danger';
      $alert['info']  = 'You are not allowed to submit this data!';
    } else {
      // $entity = $this->model->cotById($this->input->post('id'));

      if ($this->model->pengajuan($year)) {
        $alert['type'] = 'success';
        $alert['info'] = 'Data Submit.';
        $alert['link'] = site_url($this->module['route']);
      } else {
        $alert['type'] = 'danger';
        $alert['info'] = 'There are error while submit data. Please try again later.';
      }
    }

    echo json_encode($alert);
  }

  public function import()
  {
    $this->authorized($this->module, 'import');

    $this->load->library('form_validation');

    if (isset($_POST) && !empty($_POST)) {
      $this->form_validation->set_rules('delimiter', 'Value Delimiter', 'trim|required');

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
          // $col = fgetcsv($handle, 1024, $delimiter);
          // var_dump($col);
          //... parsing line
          while (($col = fgetcsv($handle, 1024, $delimiter)) !== FALSE) {
            $row++;

            /******************
             * CHECK COLUMN 0
             ******************/
            $year = trim(strtoupper($col[0]));
            $data[$row]['year'] = $year;

            if ($year == '')
              $errors[] = 'Line ' . $row . ': year is null!';

            /******************
             * CHECK COLUMN 1
             ******************/
            $hours = trim(strtoupper($col[1]));
            $data[$row]['hours'] = $hours;

            if ($hours == '')
              $errors[] = 'Line ' . $row . ': hours is null!';

            /******************
             * CHECK COLUMN 2
             ******************/
            $name = trim(strtoupper($col[2]));
            $data[$row]['name'] = $name;

            if ($name == '')
              $errors[] = 'Line ' . $row . ': name is null!';

            /**************************************************
             * CHECK COLUMN 3
             **********************************/
            $part = trim(strtoupper($col[3]));
            $data[$row]['part'] = $part;

            if ($part == '')
              $errors[] = 'Line ' . $row . ': part is null!';

            /******************
             * CHECK COLUMN 4
             ******************/
            $serial_number = trim(strtoupper($col[4]));
            $data[$row]['serial_number'] = $serial_number;

            /**************************************************
             * CHECK COLUMN 5
             **********************************/
            $category = trim(strtoupper($col[5]));
            $data[$row]['category'] = $category;

            if ($category == '')
              $errors[] = 'Line ' . $row . ': category is null!';

            /**************************************************
             * CHECK COLUMN 6
             **********************************/
            $group = trim(strtoupper($col[6]));
            $data[$row]['group'] = $group;

            if ($group == '')
              $errors[] = 'Line ' . $row . ': group is null!';
            
            if (!isItemGroupExists($group))
              $errors[] = 'Line ' . $row . ': group '.$group.' not available ini table!';

            /**************************************************
             * CHECK COLUMN 7
             **********************************/
            $unit = trim(strtoupper($col[7]));
            $data[$row]['unit'] = $unit;

            if ($unit == '')
              $errors[] = 'Line ' . $row . ': unit is null!';

            /******************
             * CHECK COLUMN 8-31
             ******************/
            $data[$row]['budget'] = array();
            $object = new \stdClass();
            $object->month = 1;
            $object->val = trim(strtoupper($col[9]));
            $object->qty = trim(strtoupper($col[8]));
            array_push($data[$row]['budget'], $object);

            $object = new \stdClass();
            $object->month = 2;
            $object->val = trim(strtoupper($col[11]));
            $object->qty = trim(strtoupper($col[10]));
            array_push($data[$row]['budget'], $object);

            $object = new \stdClass();
            $object->month = 3;
            $object->val = trim(strtoupper($col[13]));
            $object->qty = trim(strtoupper($col[12]));
            array_push($data[$row]['budget'], $object);

            $object = new \stdClass();
            $object->month = 4;
            $object->val = trim(strtoupper($col[15]));
            $object->qty = trim(strtoupper($col[14]));
            array_push($data[$row]['budget'], $object);

            $object = new \stdClass();
            $object->month = 5;
            $object->val = trim(strtoupper($col[17]));
            $object->qty = trim(strtoupper($col[16]));
            array_push($data[$row]['budget'], $object);

            $object = new \stdClass();
            $object->month = 6;
            $object->val = trim(strtoupper($col[19]));
            $object->qty = trim(strtoupper($col[18]));
            array_push($data[$row]['budget'], $object);

            $object = new \stdClass();
            $object->month = 7;
            $object->val = trim(strtoupper($col[21]));
            $object->qty = trim(strtoupper($col[20]));
            array_push($data[$row]['budget'], $object);

            $object = new \stdClass();
            $object->month = 8;
            $object->val = trim(strtoupper($col[23]));
            $object->qty = trim(strtoupper($col[22]));
            array_push($data[$row]['budget'], $object);

            $object = new \stdClass();
            $object->month = 9;
            $object->val = trim(strtoupper($col[25]));
            $object->qty = trim(strtoupper($col[24]));
            array_push($data[$row]['budget'], $object);

            $object = new \stdClass();
            $object->month = 10;
            $object->val = trim(strtoupper($col[27]));
            $object->qty = trim(strtoupper($col[26]));
            array_push($data[$row]['budget'], $object);

            $object = new \stdClass();
            $object->month = 11;
            $object->val = trim(strtoupper($col[29]));
            $object->qty = trim(strtoupper($col[28]));
            array_push($data[$row]['budget'], $object);

            $object = new \stdClass();
            $object->month = 12;
            $object->val = trim(strtoupper($col[31]));
            $object->qty = trim(strtoupper($col[30]));
            array_push($data[$row]['budget'], $object);
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

              //redirect($this->module['route']);
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
}

/* End of file Budgeting.php */
/* Location: ./application/controllers/Budgeting.php */
