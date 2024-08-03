<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Controller $controller
 * @property $data array
 *
 */
class Dashboard extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['dashboard'];

    $this->load->model($this->module['model'], 'model');

    $this->data['module'] = $this->module;
  }

  public function index()
  {
    $this->authorized($this->module, 'index');

    $this->load->helper('stock');
    $start_date  = date('Y-m-d');
    $date        = strtotime('+90 day',strtotime($start_date));
    $end_date    = date('Y-m-d', $date);

    $this->data['page_title'] = 'Welcome, in '. ucwords(config_item('auth_warehouse'));
    $this->data['json_description'] = $this->_model->search_stock_in_stores();

    $this->data['count_adjustmnet']                             = $this->model->countAdjustment();
    $this->data['count_expired_stock']                          = $this->model->countExpiredStock($start_date,$end_date);
    $this->data['expired_stock']                                = $this->model->expiredStock($start_date,$end_date);
    $this->data['count_prl']                                    = $this->model->count_prl(config_item('auth_role'));
    $this->data['count_poe']                                    = $this->model->count_poe(config_item('auth_role'));
    $this->data['count_po']                                     = $this->model->count_po(config_item('auth_role'));
    $this->data['count_capex_req']                              = $this->model->count_capex_req(config_item('auth_role'));
    $this->data['count_capex_evaluation']                       = $this->model->count_poe_local(config_item('auth_role'),'capex');
    $this->data['count_capex_order']                            = $this->model->count_po_local(config_item('auth_role'),'capex');
    $this->data['count_inventory_req']                          = $this->model->count_inventory_req(config_item('auth_role'));
    $this->data['count_inventory_evaluation']                   = $this->model->count_poe_local(config_item('auth_role'),'inventory');
    $this->data['count_inventory_order']                        = $this->model->count_po_local(config_item('auth_role'),'inventory');
    $this->data['count_expense_req']                            = $this->model->count_expense_req(config_item('auth_role'));
    $this->data['count_expense_evaluation']                     = $this->model->count_poe_local(config_item('auth_role'),'expense');
    $this->data['count_expense_order']                          = $this->model->count_po_local(config_item('auth_role'),'expense');

    $this->data['count_capex_req_not_approved']                 = $this->model->count_prl_local_not_approved('capex');
    $this->data['count_capex_evaluation_not_approved']          = $this->model->count_poe_local_not_approved('capex');
    $this->data['count_capex_order_not_approved']               = $this->model->count_po_local_not_approved('capex');
    $this->data['count_inventory_req_not_approved']             = $this->model->count_prl_local_not_approved('inventory');
    $this->data['count_inventory_evaluation_not_approved']      = $this->model->count_poe_local_not_approved('inventory');
    $this->data['count_inventory_order_not_approved']           = $this->model->count_po_local_not_approved('inventory');
    $this->data['count_expense_req_not_approved']               = $this->model->count_prl_local_not_approved('expense');
    $this->data['count_expense_evaluation_not_approved']        = $this->model->count_poe_local_not_approved('expense');
    $this->data['count_expense_order_not_approved']             = $this->model->count_po_local_not_approved('expense');

    $this->data['count_payment_request']                        = $this->model->count_payment_request(config_item('auth_role'));    
    $this->data['count_expense_purposed_payment']               = $this->model->count_purposed_payment(config_item('auth_role'),'EXPENSE');    
    $this->data['count_capex_purposed_payment']                 = $this->model->count_purposed_payment(config_item('auth_role'),'CAPEX');

    $this->data['count_payment_request_need_to_pay']                        = $this->model->count_payment_request_need_to_pay();    
    $this->data['count_expense_purposed_payment_need_to_pay']               = $this->model->count_purposed_payment_need_to_pay('EXPENSE');    
    $this->data['count_capex_purposed_payment_need_to_pay']                 = $this->model->count_purposed_payment_need_to_pay('CAPEX');

    $this->data['count_spd']            = $this->model->count_spd();
    $this->data['count_sppd']           = $this->model->count_sppd();
    $this->data['count_reimbursement']  = $this->model->count_reimbursement();
    $this->data['count_advance']        = $this->model->count_advance();


    $this->data['ap_maintenance']                               = $this->model->count_ap('maintenance');
    $this->data['ap_local']                                     = $this->model->count_ap('local');
    $this->data['ap_expense']                                     = $this->model->count_ap_expense();
    
    $this->base_theme = $this->module['view'] .'/other';
    $this->render_view();
  }

  public function close_period()
  {
    $this->require_min_level(1);

    $this->db->where('period', $this->input->post('period'));
    $this->db->where('year_number', date('Y'));
    $query = $this->db->get('tb_periods');

    if ($query->num_rows() > 0){
      $updated = $query->unbuffered_row();

      $this->db->set('start_date', $this->input->post('start'));
      $this->db->set('end_date', $this->input->post('end'));
      $this->db->set('updated_by', config_item('auth_username'));
      $this->db->where('id', $updated->id);

      if ($this->db->update('tb_periods')){
        $this->session->set_flashdata('alert', array(
          'type' => 'success',
          'info' => 'Close period updated.'
       ));
      }
    } else {
      $this->db->set('period', $this->input->post('period'));
      $this->db->set('year_number', date('Y'));
      $this->db->set('start_date', $this->input->post('start'));
      $this->db->set('end_date', $this->input->post('end'));
      $this->db->set('created_by', config_item('auth_username'));
      $this->db->set('updated_by', config_item('auth_username'));

      if ($this->db->insert('tb_periods')){
        $this->session->set_flashdata('alert', array(
          'type' => 'success',
          'info' => 'Period closed.'
       ));
      }
    }

    redirect(site_url());
  }

  public function ajax_find_item_in_stores()
  {
    $term = $_POST['item_key'];
    $warehouse = $_POST['warehouse'];

    $entities = $this->_model->find_item_in_stores($warehouse, $term);

    foreach ($entities as $key => $value){
      $item_serial = ($value['item_serial'] == '') ? '' : ' S/N: '. $value['item_serial'];

      $entities[$key]['label'] = $value['description'];
      $entities[$key]['label'] .= ' P/N: '. $value['part_number'];
      $entities[$key]['label'] .= $item_serial;
    }

    echo json_encode($entities);
  }

  public function test_send_email()
  {
    $send = $this->model->send_mail();
    
    $result['status'] = $send;
    echo json_encode($result);
  }

  public function get_list_attachment()
  {
    $data = $this->model->getListAttachment();
    
    // $result['status'] = $send;
    echo json_encode($data);
  }

  public function get_list_attachment_budgetcontrol()
  {
    $data = $this->model->getListAttachmentBudgetcontrol();
    
    // $result['status'] = $send;
    echo json_encode($data);
  }

  public function open_attachment($id,$type)
  {
    $file = $this->model->findAttachmentbyId($id,$type);
    if($this->model->isFileExist($id,$type)){
      redirect(base_url().$file['file']);
    }else{
      redirect(site_url().'secure/file_not_found');
    }
  }

  public function create_zip()
  {
    $create_zip = new ZipArchive();
    $file_name = "./attachment/test-new.zip";

    if ($create_zip->open($file_name, ZipArchive::CREATE)!==TRUE) {
        exit("cannot open the zip file <$file_name>\n");
    }
    $current_dir=getcwd();
    //Create files to add to the zip
    $create_zip->addFromString("file1 ". time().".txt" , "#1 This is This is the test file number one.\n"); 
    $create_zip->addFromString("file2 ". time().".txt", "#2 This is This is the test file number one.\n");
    //add files to the zip
    $create_zip->addFile($current_dir . "/attachment/attachment_payment/74a990fd69a293120f4ec38ec56437e9.jpg","testfromfile.jpg");
    echo "Number of files added: " . $create_zip->numFiles;
    echo "<br>";
    echo "Failed to add:" . $create_zip->status ;
    echo "direktori ".$current_dir;
    $create_zip->close();
  }
}
