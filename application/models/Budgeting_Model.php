<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Budgeting_Model extends MY_Model {
	public $active_year;
	public function __construct()
  {
    parent::__construct();
    $this->active_year = $this->find_active_year();
  }
  public function find_active_year()
  {
      $this->db->where('setting_name', 'Active_Year');
      $query = $this->db->get('tb_settings');
      $row   = $query->row();
      return $row->setting_value;
  }
  public function getSelectedColumns()
  {
    $return = array(
     "#",
     "a.item_description"     => "Item",
     "a.serial_number"        => "Serial number",
     "a.part_number"          => "Part number",
     "a.group_name"           => "Group",
     "a.category"             => "Kategori",
     "a.year"                 => "Year",
     "a.status"               => "Status",
     "a.current_price"        => "Price",
     "a.onhand"               => "Onhand stock",
     "a.qty_requirement"      => "Requirement qty",
     "a.jan_val"              => "Jan val",
     "a.jan_qty"              => "Jan qty",
     "a.feb_val"              => "Feb val",
     "a.feb_qty"              => "Feb qty",
     "a.mar_val"              => "Mar val",
     "a.mar_qty"              => "Mar qty",
     "a.apr_val"              => "Apr val",
     "a.apr_qty"              => "Apr qty",
     "a.mei_val"              => "Mei val",
     "a.mei_qty"              => "Mei qty",
     "a.jun_val"              => "Jun val",
     "a.jun_qty"              => "Jun qty",
     "a.jul_val"              => "Jul val",
     "a.jul_qty"              => "Jul qty",
     "a.ags_val"              => "Ags val",
     "a.ags_qty"              => "Ags qty",
     "a.sep_val"              => "Sep val",
     "a.sep_qty"              => "Sep qty",
     "a.oct_val"              => "Okt val",
     "a.oct_qty"              => "Okt qty",
     "a.nov_val"              => "Nov val",
     "a.nov_qty"              => "Nov qty",
     "a.des_val"              => "Des val",
     "a.des_qty"              => "Des qty",
     "a.total_val"            => "Total val",
     "a.total_qty"            => "Total qty");
  
    return $return;

  }
  public function getSearchableColumns()
  {
    return array(
      'a.part_number',
      'a.item_description',
      'a.serial_number',
      'a.group_name',
      'a.category',
      'a.status',
      //'tb_stock_in_stores.received_date',
    );
  }
    public function getOrderableColumns()
  {
    return array(
      'a.part_number',
      'a.item_description',
      'a.serial_number',
      'a.group_name',
      'a.category',
      'a.status',
      'a.year',
      'a.onhand',
      'a.current_price',
      'a.qty_requirement',
      'a.jan_val',
      'a.jan_qty',
      'a.feb_val',
      'a.feb_qty',
      'a.mar_val',
      'a.mar_qty',
      'a.apr_val',
      'a.apr_qty',
      'a.mei_val',
      'a.mei_qty',
      'a.jun_val',
      'a.jun_qty',
      'a.jul_val',
      'a.jul_qty',
      'a.ags_val',
      'a.ags_qty',
      'a.sep_val',
      'a.sep_qty',
      'a.oct_val',
      'a.oct_qty',
      'a.nov_val',
      'a.nov_qty',
      'a.des_val',
      'a.des_qty',
      'a.total_val',
      'a.total_qty',
    );
  }
   private function searchIndex()
  {
    
    $i = 0;

    foreach ($this->getSearchableColumns() as $item){
      if ($_POST['search']['value']){
        $term = strtoupper($_POST['search']['value']);

        if ($i === 0){
          $this->db->group_start();
          $this->db->like('UPPER('.$item.')', $term);
        } else {
          $this->db->or_like('UPPER('.$item.')', $term);
        }

        if (count($this->getSearchableColumns()) - 1 == $i)
          $this->db->group_end();
      }

      $i++;
    }
  }
  public function index($year){
    $this->db->select('a.*');
    $this->db->from('f_budget_display() a');
    $this->db->where('year',$year);
    $this->searchIndex();
    $orderableColumns = $this->getOrderableColumns();
    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($orderableColumns[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    }
    if ($_POST['length'] != -1)
      $this->db->limit($_POST['length'], $_POST['start']);
    $query = $this->db->get();
    return $query->result();
  }
  public function countIndex($year){
    $this->db->select('a.*');
    $this->db->from('f_budget_display() a');
    $this->db->where('year',$year);
    return $this->db->get()->num_rows();
  }
    public function countFilteredIndex($year){
    $this->db->select('a.*');
    $this->db->from('f_budget_display() a');
    $this->db->where('year',$year);
    $this->searchIndex();
    return $this->db->get()->num_rows();
  }
  public function approveData($id_cots){
    $result['update'] = 0;
    $result['error'] = array();
    foreach ($id_cots as $key) {
      if($this->updateStatus($key)){
        $result['update']++;
      }else{
        $item = $this->itemById($key);
        array_push($result['error'], "Item ".$item->description);
      }
    }
    return $result;
  }
  function updateStatus($id_cot){
    $updated_by = config_item('auth_username');
    $updated_at = date("Y-m-d H:i:s");
    $data = array("status"=>strtoupper("approved"),"updated_by"=>$updated_by,"updated_at"=>$updated_at);
    $this->db->where('id', $id_cot);
    return $this->db->update('tb_budget_cot', $data);
  }
  function itemById($id){
    $this->db->select('tb_master_items.*');
    $this->db->from('tb_master_items');
    $this->db->join('tb_budget_cot', 'tb_budget_cot.id_item = tb_master_items.id');    
    $this->db->where('tb_budget_cot.id', $id);
    return $this->db->get()->row();
  }
  function cotById($id){
    $this->db->where('id', $id);
    return $this->db->get('tb_budget_cot')->row();
  }

  public function cotQtyById($id){
    $this->db->select('tb_budget.mtd_quantity,tb_budget.month_number');
    $this->db->from('tb_budget');
    $this->db->where('id_cot', $id);
    //echo $this->db->_compile_select();
    $this->db->order_by('month_number');
    return $this->db->get()->result();
  }

  public function cotHourById($id){
    $this->db->select('tb_budget.hour,tb_budget.month_number');
    $this->db->from('tb_budget');
    $this->db->where('id_cot', $id);
    //echo $this->db->_compile_select();
    $this->db->order_by('month_number');
    return $this->db->get()->result();
  }

  public function updateOnhand($onhand,$id_cot){
    $data = array("onhand"=>$onhand);
    $this->db->where('id', $id_cot);
    return $this->db->update('tb_budget_cot', $data);
  }
  public function updateBudget($id_cot,$cot_hour,$qty_req,$price){
    $data = array();
    $mtd_prev_month_budget = 0;
    $mtd_prev_month_quantity = 0;
    $updated_at = date("Y-m-d H:i:s");
    $updated_by = config_item('auth_username');
    $ytd_budget = 0;
    $ytd_quantity = 0;
    $result = 0;
    for ($i=1; $i <13 ; $i++) { 
      $monthHour = $this->input->post('m_'.$i);
      $monthQty = round(($monthHour/$cot_hour)*$qty_req);
      $margin = $i == 12 ? ($ytd_quantity+$monthQty)-$qty_req : 0;
      $initial_quantity = $monthQty-$margin;
      $initial_budget = $initial_quantity * $price;
      $mtd_budget = $initial_budget;
      $mtd_quantity = $initial_quantity;        
      $ytd_budget += $initial_budget ;
      $ytd_quantity += $initial_quantity;
      $hourMonthly = $monthHour;  
      $row = array("initial_budget"=>$initial_budget,"initial_quantity"=>$initial_quantity,"mtd_budget"=>$mtd_budget,"mtd_quantity"=>$mtd_quantity,"mtd_prev_month_budget"=>$mtd_prev_month_budget,"mtd_prev_month_quantity"=>$mtd_prev_month_quantity,"ytd_budget"=>$ytd_budget,"ytd_quantity"=>$ytd_quantity,"updated_at"=>$updated_at,"updated_by"=>$updated_by,"hour"=>$hourMonthly);
      $proccess = $this->updateDataBudget($id_cot,$i,$row);
      if($proccess){
        $result ++;
      }
      $mtd_prev_month_budget = $initial_budget;
      $mtd_prev_month_quantity = $initial_quantity;
    }
    return $result;
  }
  public function updateDataBudget($id_cot,$month_number,$row){
    $this->db->where('id_cot', $id_cot);
    $this->db->where('month_number', $month_number);
    return $this->db->update('tb_budget', $row);
  }

  public function select_budget($year){
    $this->db->select('*');
    $this->db->from('f_budget_display()');
    $this->db->where('year',$year);
    $query = $this->db->get();
    return $query->result();
  }

  public function send_mail() { 
    // $this->db->from('tb_inventory_purchase_requisitions');
    // $this->db->where('id',$doc_id);
    // $query = $this->db->get();
    // $row = $query->unbuffered_row('array');

    $recipientList = $this->getNotifRecipient(9);
    $recipient = array();
    foreach ($recipientList as $key ) {
      array_push($recipient, $key->email);
    }

    $from_email = "baliflight@hotmail.com"; 
    $to_email = "aidanurul99@rocketmail.com"; 
   
    //Load email library 
    $this->load->library('email'); 
    $config = array();
    $config['protocol'] = 'mail';
    $config['smtp_host'] = 'smtp.gmail.com';
    $config['smtp_user'] = 'kiddo2095@gmail.com';
    $config['smtp_pass'] = 'kyuhyun234';
    $config['smtp_port'] = 587;
    $config['smtp_auth']        = true;
    $config['mailtype']         = 'html';
    $this->email->initialize($config);
    $this->email->set_newline("\r\n");
    $message = "<p>Dear Chief of Maintenance</p>";
    $message .= "<p>Permintaan Baru untuk Persetujuan Budget. </p>";
    $message .= "<ul>";
    $message .= "</ul>"; 
    $message .= "<p>Silakan klik link dibawah ini untuk menuju list</p>";
    $message .= "<p>[ <a href='http://119.252.163.206/mrp_demo/budgeting/' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
    $message .= "<p>Thanks and regards</p>";
    $this->email->from($from_email, 'Material Resource Planning'); 
    $this->email->to($recipient);
    $this->email->subject('Permintaan Approval Budget'); 
    $this->email->message($message); 
     
    //Send mail 
    if($this->email->send()) 
      return true; 
    else 
      return $this->email->print_debugger();
  }
  public function getNotifRecipient($level){
    $this->db->select('email');
    $this->db->from('tb_auth_users');
    $this->db->where('auth_level',$level);
    return $this->db->get('')->result();
  }
}

/* End of file Budgeting_Model.php */
/* Location: ./application/models/Budgeting_Model.php */