<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Controller $controller
 * @property M_pdf $m_pdf
 * @property model $model
 *
 * Add additional libraries you wish
 * to use in your controllers here
 *
 * @property $data array
 * @property $rule array
 *
 */
class Purchase_Order_Old extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['purchase_order'];
    $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  /**
   * Records list
   */
  public function index()
  {
    // ========================= ACCESS DENIED ========================== //
    if ($this->verify_role($this->module['permission']['index']) == FALSE)
      redirect('secure/denied');

    // ========================= ACCESS GRANTED ========================= //

    $entities = $this->model->find_all();
    $this->set_data('entities', $entities);

    $this->set_data('page_title', lang('page_title_index'));
    $this->set_data('page_content', $this->module['view'] .'/index');

    // where to go if single click perform
    if ($this->has_role($this->module, 'show')){
      $this->data['singleClickUrl'] = site_url($this->module['route'] .'/show');
    }

    // where to go if double click perform
    if ($this->has_role($this->module, 'edit')){
      $this->data['doubleClickUrl'] = site_url($this->module['route'] .'/edit');
    }

    $this->render_view();
  }

  /**
   * Create New Record
   */
  public function select_poe()
  {
    // ====================== ACCESS DENIED ======================= //
    if ($this->verify_role($this->module['permission']['create']) == FALSE)
      redirect('secure/denied');

    // ====================== ACCESS GRANTED ====================== //

    $entities = $this->model->find_poe();
    $this->set_data('entities', $entities);

    /**
     * Processing data
     * if form submitted
     */
    if (isset($_POST) && !empty($_POST)){
      $_SESSION['po']['reference_poe'] = $this->input->post('reference_poe');

      redirect($this->module['route'] .'/select_vendor');
    }

    //... set view data
    $this->set_data('page_content', $this->module['view'] .'/form_select_poe');
    $this->set_data('page_script', $this->module['view'] .'/form_select_poe_script');
    $this->set_data('page_title', lang('page_title_create'));

    //... render view
    $this->render_view();
  }

  /**
   * Create New Record
   */
  public function select_vendor()
  {
    // ====================== ACCESS DENIED ======================= //
    if ($this->verify_role($this->module['permission']['create']) == FALSE)
      redirect('secure/denied');

    // ====================== ACCESS GRANTED ====================== //

    if (isset($_SESSION['po']['reference_poe']) === FALSE || empty($_SESSION['po']['reference_poe']))
      redirect($this->module['route'] .'/select_poe');

    $entities = $this->model->find_vendors();
    $this->set_data('entities', $entities);

    /**
     * Processing data
     * if form submitted
     */
    if (isset($_POST) && !empty($_POST)){
      $vendor_id = $this->input->post('vendor');

      $_SESSION['po']['vendor_id'] = $vendor_id;

      $vendor = $this->model->find_vendor_by_id($vendor_id);

      $_SESSION['po']['vendor_name'] = $vendor->vendor;

      $_SESSION['po']['vendor_address'] = $vendor->vendor;
      $_SESSION['po']['vendor_address'].= "\n". $vendor->address;
      $_SESSION['po']['vendor_address'].= "\nPh. ". $vendor->phone;

      $items = $this->model->find_items_by_vendor($vendor_id);

      if ($items == NULL or !$items or count($items) == 0){
        $this->session->set_flashdata('alert', array(
          'type' => 'warning',
          'info' => 'Vendor ' . $vendor->vendor . ' does not have any items. Select another vendor.'
       ));
      } else {
        foreach ($items as $key => $value){
          $_SESSION['po']['item'][$key]['imb_id'] = $value->imb_id;
          $_SESSION['po']['item'][$key]['item_name'] = $value->item_name;
          $_SESSION['po']['item'][$key]['item_code'] = $value->item_code;
          $_SESSION['po']['item'][$key]['part_number'] = $value->item_part_number;
          $_SESSION['po']['item'][$key]['alternate_part_number'] = $value->item_alternate_part_number;
          $_SESSION['po']['item'][$key]['quantity'] = $value->item_quantity;
          $_SESSION['po']['item'][$key]['unit_price'] = $value->unit_price;
          $_SESSION['po']['item'][$key]['core_charge'] = $value->core_charge;
          $_SESSION['po']['item'][$key]['notes'] = $value->notes;
        }

        redirect($this->module['route'] .'/save');
      }
    }

    //... set view data
    $this->set_data('page_content', $this->module['view'] .'/form_select_vendor');
    $this->set_data('page_title', lang('page_title_create'));
    $this->set_data('page_script', $this->module['view'] .'/form_select_poe_script');

    //... render view
    $this->render_view();
  }

  public function discard()
  {
    // ====================== ACCESS DENIED ======================= //
    if ($this->verify_role($this->module['permission']['create']) == FALSE)
      redirect('secure/denied');

    // ====================== ACCESS GRANTED ====================== //

    unset($_SESSION['po']);

    redirect($this->module['route']);
  }

  public function save()
  {
    // ====================== ACCESS DENIED ======================= //
    if ($this->verify_role($this->module['permission']['create']) == FALSE)
      redirect('secure/denied');

    // ====================== ACCESS GRANTED ====================== //

    if (isset($_SESSION['po']['reference_poe']) === FALSE || empty($_SESSION['po']['reference_poe']))
      redirect($this->module['route'] .'/select_poe');

    if (isset($_SESSION['po']['vendor_id']) === FALSE || empty($_SESSION['po']['vendor_id']))
      redirect($this->module['route'] .'/select_vendor');

    // $entities = $this->model->find_budgets_by_ids();
    // $this->pretty_dump($_SESSION['po']);

    /**
     * Processing data
     * if form submitted
     */
    if (isset($_POST) && !empty($_POST)){
      // ... check over budget each detail

      $po_no   = $this->input->post('po_number');

      // $this->pretty_dump($this->input->post());

      if ($this->model->save()){
        //... send message to view
        $this->session->set_flashdata('alert', array(
          'type' => 'success',
          'info' => 'PO #' . $po_number . ' saved.'
       ));

        unset($_SESSION['po']);

        // redirect($this->module['route'] .'/show/'. $poe_no);
        redirect($this->module['route']);
      }

      redirect($this->module['route'] .'/save');
    }

    //... set view data
    $this->set_data('page_content', $this->module['view'] .'/form_create');
    $this->set_data('page_script', $this->module['view'] .'/form_create_script');
    $this->set_data('page_title', lang('page_title_create'));

    //... render view
    $this->render_view();
  }

  public function create()
  {
    // ====================== ACCESS DENIED ======================= //
    if ($this->verify_role($this->module['permission']['create']) == FALSE)
      redirect('secure/denied');

    // ====================== ACCESS GRANTED ====================== //

    if (isset($_SESSION['po']) === FALSE){
      $_SESSION['po']['id'] = NULL;
    }

    redirect($this->module['route'] .'/select_poe');
  }

  public function edit($id)
  {
    // ====================== ACCESS DENIED ======================= //

    if ($this->verify_role($this->module['permission']['edit']) == FALSE)
      redirect('secure/denied');

    // ====================== ACCESS GRANTED ====================== //

    //... get entity data from $id given
    $entity = $this->model->find_one_by_id($id);
    // $this->pretty_dump($entity);

    if (isset($_SESSION['po']) === FALSE){
      $_SESSION['po'] = $entity;
    }

    redirect($this->module['route'] .'/save');
  }

  /**
   * Edit record by id $id
   *
   * @param $id
   */
  public function show($id)
  {
    // ====================== ACCESS DENIED ======================= //

    if ($this->verify_role($this->module['permission']['show']) == FALSE)
      redirect('secure/denied');

    // ====================== ACCESS GRANTED ====================== //

    //... get entity data from $id given
    $entity = $this->model->find_one_by_id($id);
    $this->set_data('entity', $entity);

    // $this->pretty_dump($entity);

    //... set view data
    $this->set_data('page_content', $this->module['view'] .'/show');
    $this->set_data('page_script', $this->module['view'] .'/show_script');
    $this->set_data('page_title', lang('page_title_show'));

    if ($this->verify_role($this->module['permission']['index'])){
      $this->set_page_nav('index', anchor($this->module['route'], lang('nav_index')));
      $this->set_data('close_url', site_url($this->module['route']));
    }

    if ($this->verify_role($this->module['permission']['create']))
      $this->set_page_nav('create', anchor($this->module['route'] .'/create', lang('nav_create')));

    if ($this->verify_role($this->module['permission']['edit']))
      $this->set_page_nav('edit', anchor($this->module['route'] .'/edit/'.$id, lang('nav_edit')));

    //... render view
    $this->render_view();
  }

  /**
   * Generate PDF
   */
  public function print_pdf($id)
  {
    // ====================== ACCESS DENIED ======================= //

    if ($this->verify_role($this->module['permission']['show']) == FALSE)
      redirect('secure/denied');

    // ====================== ACCESS GRANTED ====================== //

    $entity = $this->model->find_one_by_id($id);
    $this->set_data('entity', $entity);

    // $this->set_data('page_content', $this->module['view'] .'/print');
    $this->set_data('page_header', 'PURCHASE ORDER');
    $this->set_data('page_title', 'PURCHASE ORDER');

    //load the view and saved it into $html variable
    $html = $this->load->view($this->module['view'] .'/print', $this->get_data(), TRUE);

    //this the the PDF filename that user will get to download
    $pdfFilePath = "PO_". $entity['po_number'] .".pdf";

    //load mPDF library
    $this->load->library('m_pdf');

    //generate the PDF from the given html
    $pdf = $this->m_pdf->load("en-GB-x", "A4-L", 0, "", 12, 12, 60, 12, 9, 6);
    $pdf->WriteHTML($html);
    $pdf->Output($pdfFilePath, "I");
  }
}
