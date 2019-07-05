<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Budget extends MY_Controller
{
  protected $module;


  public function __construct()
  {
    parent::__construct();

    $this->module    = 'budget';
    $this->module['view'] = '_modules/'.$this->module;

    $this->lang->load($this->module['language']);
    $this->load->model($this->module['model'], 'model');
    // $this->load->helper($this->module);

    $this->set_data('module', $this->module);
    $this->set_data('page_header', lang('page_header'));
    $this->set_data('page_desc', NULL);
  }

  public function index()
  {
    $this->authorized($this->module, 'index');

    // ========================= ACCESS GRANTED ========================= //

    //... default value
    $start_date = null;
    $end_date = null;

    if (isset($_GET['start']) && !empty($_GET['start']))
      $start_date = $_GET['start'];

    if (isset($_GET['end']) && !empty($_GET['end']))
      $end_date = $_GET['end'];

    //... set page title
    if ($start_date == null && $end_date == null){
      $this->set_data('page_title', sprintf(lang('page_title_index'), date('F Y')));
    } else {
      if ($start_date != null && $end_date != null){
        $this->set_data('page_title', sprintf(lang('page_title_index_range'), date_format(date_create($start_date), 'd F Y'), date_format(date_create($end_date), 'd F Y')));
      } else {
        if ($start_date != null){
          $date = new DateTime($start_date);
          $this->set_data('page_title', sprintf(lang('page_title_index_start'), $date->format('d F Y')));
        } else {
          $date = new DateTime($end_date);
          $this->set_data('page_title', sprintf(lang('page_title_index'), $date->format('F Y')));
        }
      }
    }

    $entities = $this->model->find_all($start_date, $end_date);

    $this->set_data('entities', $entities);
    $this->set_data('page_content', $this->module['view'] .'/index');

    if ($this->has_role($this->module, 'delete')){
      $this->data['deleteUrl'] = site_url($this->module['route'] .'/delete');
    } else { $this->data['deleteUrl'] = NULL; }

    if ($this->has_role($this->module, 'create')){
      $this->data['createUrl'] = site_url($this->module['route'] .'/create');
    } else { $this->data['createUrl'] = NULL; }

    if ($this->has_role($this->module, 'import')){
      $this->data['importUrl'] = site_url($this->module['route'] .'/import');
    } else { $this->data['importUrl'] = NULL; }

    // where to go if single click perform
    if ($this->has_role($this->module, 'show')){
      $this->data['singleClickUrl'] = site_url($this->module['route'] .'/show');
    }

    // where to go if double click perform
    if ($this->has_role($this->module, 'edit')){
      $this->data['doubleClickUrl'] = site_url($this->module['route'] .'/edit');
    }

    if ($this->has_role($this->module, 'create'))
      $this->set_page_nav('create', anchor($this->module['route'] .'/create', '<i class="fa fa-edit"></i> Create'));

    if ($this->has_role($this->module, 'import'))
      $this->set_page_nav('import', anchor($this->module['route'] .'/import', '<i class="fa fa-download"></i> Import'));

    $this->set_data('start_date', $start_date);
    $this->set_data('end_date', $end_date);
    $this->set_data('sidebar', $this->module['view'] .'/sidebar');

    $this->render_view();
  }

  /**
   * Records list
   */
  public function show($id)
  {
    $this->authorized($this->module, 'show');

    // ========================= ACCESS GRANTED ========================= //

    $entity = $this->model->findById($id);

    $this->set_data('entity', $entity);
    $this->set_data('page_title', lang('page_title_show'));

    if (config_item('auth_role') === 'PIC STOCK'){
      $this->set_data('page_content', $this->module['view'] .'/show');
    } else {
      $this->set_data('page_content', $this->module['view'] .'/show_detail');
    }

    if ($this->has_role($this->module, 'index'))
      $this->set_page_nav('index', anchor($this->module['route'] .'?warehouse=GENERAL', '<i class="fa fa-arrow-circle-left"></i> Back'));

    if ($this->has_role($this->module, 'import'))
      $this->set_page_nav('import', anchor($this->module['route'] .'/import', '<i class="fa fa-download"></i> Import'));

    $this->render_view();
  }

  public function print_pdf($id)
  {
    $this->authorized($this->module, 'show');

    // ========================= ACCESS GRANTED ========================= //

    $entity = $this->model->findById($id);

    $this->set_data('entity', $entity);
    $this->set_data('page_title', lang('page_title_show'));
    $this->set_data('page_content', $this->module['view'] .'/print');
    $this->set_data('page_header', lang('page_header'));

    //load the view and saved it into $html variable
    $html = $this->load->view($this->pdf_theme, $this->data, true);

    //this the the PDF filename that user will get to download
    $pdfFilePath = "MS_LIST_". date('d_m_Y') .".pdf";

    //load mPDF library
    $this->load->library('m_pdf');

    //generate the PDF from the given html
    $pdf = $this->m_pdf->load(null, 'A4-L');
    $pdf->WriteHTML($html);
    $pdf->Output($pdfFilePath, "I");
  }

  public function relocation()
  {
    $this->authorized($this->module['permission']['relocation']);

    // ====================== ACCESS GRANTED ====================== //

    if (isset($_SESSION['budget']) === FALSE){
      $_SESSION['budget'] = array();
    }

    if (isset($_GET['item']) && isset($_GET['group'])){
      $_SESSION['budget']['group'] = $_GET['group'];
      $_SESSION['budget']['destination'] = array(
        'item' => $_GET['item'],
     );
    }

    redirect($this->module['route'] .'/save');
  }

  public function edit($id)
  {
    $this->authorized($this->module, 'edit');

    // ====================== ACCESS GRANTED ====================== //

    //... get entity data from $id given
    $entity = $this->model->findById($id);

    if (isset($_SESSION['budget']) === FALSE){
      $_SESSION['budget'] = $entity;
    }

    redirect($this->module['route'] .'/save');
  }

  public function save()
  {
    $this->authorized($this->module['permission']['create'], $this->module['permission']['edit']);

    // ====================== ACCESS GRANTED ====================== //

    if (!isset($_SESSION['budget']))
      redirect($this->module['route']);

    /**
     * Processing data
     * if form submitted
     */
    if (isset($_POST) && !empty($_POST)){
      // ... check document number
      if ($_POST['id'] === NULL && $this->model->isDocumentNumberExists($_POST['document_number'])){
        $this->session->set_flashdata('alert', array(
          'type' => 'danger',
          'info' => 'PR #' . $this->input->post('document_number') . ' has been used!'
       ));

        redirect($this->module['route'] .'/save');
      }

      $this->pretty_dump($_POST);

      if ($this->model->save()){
        //... send message to view
        $this->session->set_flashdata('alert', array(
          'type' => 'success',
          'info' => 'PR#' . $this->input->post('document_number') . ' saved.'
       ));

        unset($_SESSION['budget']);

        // redirect($this->module['route'] .'/show/'. $pr_number);
        redirect($this->module['route']);
      }
    }

    //... set view data
    $this->set_data('vendors', $this->model->findAllVendors('AVAILABLE'));
    $this->set_data('page_content', $this->module['view'] .'/save');
    $this->set_data('page_script', $this->module['view'] .'/save_script');
    $this->set_data('page_title', lang('page_title_save'));

    //... render view
    $this->render_view();
  }

  public function add_item()
  {
    $this->authorized($this->module['permission']['create'], $this->module['permission']['edit']);

    // ====================== ACCESS GRANTED ====================== //

    if (!isset($_SESSION['budget']))
      redirect($this->module['route']);

    /**
     * Processing data
     * if form submitted
     */
    if (isset($_POST) && !empty($_POST)){
      //... cek budget
      $budget = $this->model->find_budget($this->input->post('description'), $this->input->post('group'));

      //... insert into session
      $_SESSION['budget']['items'][] = array(
        'group'        => $this->input->post('group'),
        'description'       => trim(strtoupper($this->input->post('description'))),
        'part_number'       => trim(strtoupper($this->input->post('part_number'))),
        'alternate_part_number'   => trim(strtoupper($this->input->post('alternate_part_number'))),
        'item_serial'     => trim(strtoupper($this->input->post('item_serial'))),
        'quantity'          => $this->input->post('quantity'),
        'unit'               => trim($this->input->post('unit')),
        'additional_info'   => trim($this->input->post('additional_info')),
        'mtd_budget'        => $budget['mtd_budget'],
        'mtd_quantity'      => $budget['mtd_quantity'],
        'balance_budget'    => $budget['ytd_budget'] - $budget['ytd_used_budget'],
        'balance_quantity'  => $budget['ytd_quantity'] - $budget['ytd_used_quantity'],
     );

      redirect($this->module['route'] .'/save');
    }

    //... set view data
    $this->set_data('item_groups', $this->model->findAllItemGroups('AVAILABLE'));
    $this->set_data('json_description', $this->model->distinct('tb_master_items', 'description', NULL, TRUE));
    $this->set_data('json_part_number', $this->model->distinct('tb_master_items', 'part_number', NULL, TRUE));
    $this->set_data('json_alternate_part_number', $this->model->distinct('tb_master_items', 'alternate_part_number', NULL, TRUE));
    $this->set_data('search_serial_number', $this->model->distinct('tb_master_item_serials', 'item_serial', NULL, TRUE));
    $this->set_data('json_unit', $this->model->distinct('tb_master_item_units', 'unit', NULL, TRUE));
    $this->set_data('page_content', $this->module['view'] .'/add_item');
    $this->set_data('page_script', $this->module['view'] .'/add_item_script');
    $this->set_data('page_title', lang('page_title_add_item'));

    //... render view
    $this->render_view();
  }

  public function edit_item($key)
  {
    $this->authorized($this->module['permission']['create'], $this->module['permission']['edit']);

    // ====================== ACCESS GRANTED ====================== //

    if (!isset($_SESSION['budget']))
      redirect($this->module['route']);

    if (!isset($_SESSION['budget']['items'][$key]) or $_SESSION['budget']['items'][$key] == NULL)
      redirect($this->module['route'] .'/save');

    /**
     * Processing data
     * if form submitted
     */
    if (isset($_POST) && !empty($_POST)){
      $_SESSION['budget']['items'][$key] = array(
        'group'        => $this->input->post('group'),
        'description'       => trim(strtoupper($this->input->post('description'))),
        'part_number'       => trim(strtoupper($this->input->post('part_number'))),
        'alternate_part_number'   => trim(strtoupper($this->input->post('alternate_part_number'))),
        'item_serial'     => trim(strtoupper($this->input->post('item_serial'))),
        'quantity'          => $this->input->post('quantity'),
        'unit'               => trim($this->input->post('unit')),
        'additional_info'   => trim($this->input->post('additional_info')),
     );

      redirect($this->module['route'] .'/save');
    }

    //... set view data
    $this->set_data('item_groups', $this->model->findAllItemGroups('AVAILABLE'));
    $this->set_data('json_description', $this->model->distinct('tb_master_items', 'description', NULL, TRUE));
    $this->set_data('json_part_number', $this->model->distinct('tb_master_items', 'part_number', NULL, TRUE));
    $this->set_data('json_alternate_part_number', $this->model->distinct('tb_master_items', 'alternate_part_number', NULL, TRUE));
    $this->set_data('search_serial_number', $this->model->distinct('tb_master_item_serials', 'item_serial', NULL, TRUE));
    $this->set_data('json_unit', $this->model->distinct('tb_master_item_units', 'unit', NULL, TRUE));
    $this->set_data('entity', $_SESSION['budget']['items'][$key]);
    $this->set_data('page_content', $this->module['view'] .'/edit_item');
    $this->set_data('page_script', $this->module['view'] .'/add_item_script');
    $this->set_data('page_title', lang('page_title_add_item'));

    //... render view
    $this->render_view();
  }

  public function ajax_find_item_serial()
  {
    echo json_encode($this->model->find_item_by_item_serial($_POST['item_serial']));
  }

  public function ajax_find_part_number()
  {
    echo json_encode($this->model->find_item_by_part_number($_POST['part_number']));
  }

  public function delete_item($key)
  {
    $this->authorized($this->module['permission']['create'], $this->module['permission']['edit']);

    // ====================== ACCESS GRANTED ====================== //

    if (!isset($_SESSION['budget']))
      redirect($this->module['route']);

    if (isset($_SESSION['budget']['items']) === FALSE)
      redirect($this->module['route'] .'/add_item');

    unset($_SESSION['budget']['items'][$key]);

    redirect($this->module['route'] .'/save');
  }

  public function discard()
  {
    $this->authorized($this->module['permission']['create'], $this->module['permission']['edit']);

    // ====================== ACCESS GRANTED ====================== //

    unset($_SESSION['budget']);

    redirect($this->module['route']);
  }

  public function import()
  {
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

            /******************
             * CHECK COLUMN 0
             ******************/
            $document_number = (trim($col[0]) == '') ? NULL : trim(strtoupper($col[0]));
            $data[$row]['document_number'] = $document_number;

            if ($document_number === NULL)
              $errors[] = 'Line '. $row .': PR number is NULL!';

            if (strlen($document_number) > 100)
              $errors[] = 'Line '. $row .': PR number is too long!';

            /******************
             * CHECK COLUMN 1
             ******************/
            $request_date = (trim($col[1]) == '') ? NULL : trim(strtoupper($col[1]));
            $data[$row]['request_date'] = $request_date;

            if ($request_date === NULL)
              $errors[] = 'Line '. $row .': PR date is NULL!';

            /******************
             * CHECK COLUMN 2
             ******************/
            $vendor = (trim($col[2]) == '') ? NULL : trim(strtoupper($col[2]));
            $data[$row]['vendor'] = $vendor;

            if (strlen($vendor) > 100)
              $errors[] = 'Line '. $row .': vendor is too long!';

            if ($vendor !== NULL && $this->model->isVendorExists($vendor) === FALSE)
              $errors[] = 'Line '. $row .': Unknown vendor '. $vendor;

            /******************
             * CHECK COLUMN 3
             ******************/
            $request_by = (trim($col[3]) == '') ? NULL : trim(strtoupper($col[3]));
            $data[$row]['request_by'] = $request_by;

            if ($request_by === NULL)
              $errors[] = 'Line '. $row .': Signature is NULL!';

            /******************
             * CHECK COLUMN 4
             ******************/
            $group = (trim($col[4]) == '') ? NULL : trim(strtoupper($col[4]));
            $data[$row]['group'] = $group;

            if ($group == NULL)
              $errors[] = 'Line '. $row .': item group is NULL!';

            if (strlen($group) > 20)
              $errors[] = 'Line '. $row .': item group is too long!';

            if ($this->model->isItemGroupExists($group) === FALSE)
              $errors[] = 'Line '. $row .': Unknown item group '. $group;

            /******************
             * CHECK COLUMN 5
             ******************/
            $description = (trim($col[5]) == '') ? NULL : trim(strtoupper($col[5]));
            $data[$row]['description'] = $description;

            /******************
             * CHECK COLUMN 6
             ******************/
            $part_number = (trim($col[6]) == '') ? NULL : trim(strtoupper($col[6]));
            $data[$row]['part_number'] = $part_number;

            if ($part_number === NULL)
              $errors[] = 'Line '. $row .': Part Number is NULL!';

            /******************
             * CHECK COLUMN 7
             ******************/
            $alternate_part_number = (trim($col[7]) == '') ? NULL : trim(strtoupper($col[7]));
            $data[$row]['alternate_part_number'] = $alternate_part_number;

            /******************
             * CHECK COLUMN 8
             ******************/
            $item_serial = (trim($col[8]) == '') ? NULL : trim(strtoupper($col[8]));
            $data[$row]['item_serial'] = $item_serial;

            if ($item_serial !== NULL && $this->model->isSerialNumberExists($item_serial))
              $errors[] = 'Line '. $row .': Duplicate serial number '. $item_serial;

            /******************
             * CHECK COLUMN 9
             ******************/
            $quantity = (trim($col[9]) == '') ? NULL : trim(strtoupper($col[9]));
            $data[$row]['quantity'] = $quantity;

            if ($quantity === NULL or $quantity === 0 or is_numeric($quantity) === FALSE)
              $errors[] = 'Line '. $row .': Quantity is NULL or 0!';

            /******************
             * CHECK COLUMN 10
             ******************/
            $unit = (trim($col[10]) == '') ? 'EA' : trim(strtoupper($col[10]));
            $data[$row]['unit'] = $unit;

            /******************
             * CHECK COLUMN 11
             ******************/
            $condition = (trim($col[11]) == '') ? NULL : trim($col[11]);
            $data[$row]['condition'] = $condition;

            /******************************************
             * ITEM CONDITION : S/S, U/S, REJECTED
             ******************************************/
            $conditions = array('S/S', 'U/S', 'REJECTED');

            if (in_array($condition, $conditions) == FALSE){
              $errors[] = 'Line '. $row .': Unknown item condition '. $condition;
            }

            /******************
             * CHECK COLUMN 12
             ******************/
            $aircraft_types = (trim($col[12]) == '') ? NULL : trim(strtoupper($col[12]));
            $data[$row]['aircraft_types'] = $aircraft_types;

            if ($aircraft_types !== NULL){
              foreach (explode(';', $aircraft_types) as $ac_type){
                if ($this->model->isAircraftTypeExists($ac_type) === FALSE)
                  $errors[] = 'Line '. $row .': Unknown aircraft type '. $ac_type;
              }
            }

            /******************
             * CHECK COLUMN 13
             ******************/
            $order_number = (trim($col[13]) == '') ? NULL : trim(strtoupper($col[13]));
            $data[$row]['order_number'] = $order_number;

            /******************
             * CHECK COLUMN 14
             ******************/
            $reference_number = (trim($col[14]) == '') ? NULL : trim(strtoupper($col[14]));
            $data[$row]['reference_number'] = $reference_number;

            /******************
             * CHECK COLUMN 15
             ******************/
            $awb_number = (trim($col[15]) == '') ? NULL : trim(strtoupper($col[15]));
            $data[$row]['awb_number'] = $awb_number;

            /******************
             * CHECK COLUMN 16
             ******************/
            $stores = (trim($col[16]) == '') ? NULL : trim(strtoupper($col[16]));
            $data[$row]['stores'] = $stores;

            /******************
             * CHECK COLUMN 17
             ******************/
            $notes = (trim($col[17]) == '') ? NULL : trim(strtoupper($col[17]));
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

    //... set view data
    $this->set_data('page_title', lang('page_title_import'));
    $this->set_data('page_content', $this->module['view'] .'/import');

    if ($this->has_role($this->module, 'index'))
      $this->set_page_nav('index', anchor($this->module['route'], '<i class="fa fa-arrow-circle-left"></i> Back'));

    if ($this->has_role($this->module, 'create'))
      $this->set_page_nav('create', anchor($this->module['route'] .'/create', '<i class="fa fa-edit"></i> Create'));

    $this->render_view();
  }
}
