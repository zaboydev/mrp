<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Budget_Cot_Model extends MY_Model
{
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
      'tb_budget_cot.id' => NULL,
      'tb_master_items.description' => 'Description',      
      'tb_master_items.part_number' => 'Part Number',
      'tb_master_items.serial_number' => 'Serial Number',
      'tb_budget_cot.hours '=> 'Hours',
      'tb_budget_cot.year' => 'Year',
      'tb_budget_cot.qty_standar' => 'Standar Quantity',
      'tb_budget_cot.qty_requirement' => 'Requirement Quantity',
    );
  
    return $return;

  }
  public function getSearchableColumns()
  {
    return array(
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.serial_number',
      //'tb_stock_in_stores.received_date',
    );
  }

  public function getOrderableColumns()
  {
    return array(
      NULL,
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.serial_number',
      'tb_budget_cot.hours',
      'tb_budget_cot.year',
      'tb_budget_cot.qty_standar',
      'tb_budget_cot.qty_requirement',
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
  public function index(){
    $this->db->select('tb_master_items.description,tb_master_items.part_number,tb_master_items.serial_number,tb_budget_cot.*');
    $this->db->from('tb_budget_cot');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_budget_cot.id_item');
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
  public function countIndex(){
    $this->db->select('tb_master_items.description,tb_master_items.part_number,tb_master_items.serial_number,tb_budget_cot.*');
    $this->db->from('tb_budget_cot');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_budget_cot.id_item');
    $query = $this->db->get();
    return $query->num_rows();
  }
  public function getKelipatan(){
    return $this->db->get('tb_master_kelipatan')->result();
  }

  public function getKelipatanById($id){
    $this->db->where('id', $id);
    $row = $this->db->get('tb_master_kelipatan')->row();
    return $row->kelipatan;
  }
  public function itemCot()
  {
    // $this->db->select('tb_master_items.id, tb_master_items.description,tb_master_items.part_number');
    // $this->db->from('tb_master_items');
    // $this->db->join('tb_master_item_groups', 'tb_master_items.group = tb_master_item_groups.group');
    // $this->db->join('tb_master_item_categories', 'tb_master_item_groups.category = tb_master_item_categories.category');
    // $this->db->join('tb_auth_user_categories', 'tb_master_item_categories.category = tb_auth_user_categories.category');
    // $this->db->where('tb_auth_user_categories.username', config_item('auth_username'));
    // $this->db->group_start();
    //   $this->db->like('tb_master_items.description', trim(strtoupper($this->input->post('search'))), 'BOTH');
    //   $this->db->or_like('tb_master_items.part_number', trim(strtoupper($this->input->post('search'))), 'BOTH');
    //    $this->db->or_like('tb_master_items.serial_number', trim(strtoupper($this->input->post('search'))), 'BOTH');
    // $this->db->group_end();
    // if(($_POST['id_kategori'] != "all")&&($this->input->post('id_kategori')!=""))
    //   $this->db->where('tb_master_item_categories.category', $this->input->post('id_kategori'));
    // if ($_POST['length'] != -1)
    //   $this->db->limit($_POST['length'], $_POST['start']);
    // return $this->db->get()->result();
    $this->db->select('tb_master_items.description,tb_master_items.part_number');
    $this->db->from('tb_master_items');
    $this->db->join('tb_master_item_groups', 'tb_master_items.group = tb_master_item_groups.group');
    $this->db->join('tb_master_item_categories', 'tb_master_item_groups.category = tb_master_item_categories.category');
    $this->db->join('tb_auth_user_categories', 'tb_master_item_categories.category = tb_auth_user_categories.category');
    $this->db->where('tb_auth_user_categories.username', config_item('auth_username'));
    $this->db->group_start();
      $this->db->like('tb_master_items.description', trim(strtoupper($this->input->post('search'))), 'BOTH');
      $this->db->or_like('tb_master_items.part_number', trim(strtoupper($this->input->post('search'))), 'BOTH');
      // $this->db->or_like('tb_master_items.serial_number', trim(strtoupper($this->input->post('search'))), 'BOTH');
    $this->db->group_end();
    $this->db->group_by('tb_master_items.description,tb_master_items.part_number');
    if(($_POST['id_kategori'] != "all")&&($this->input->post('id_kategori')!=""))
      $this->db->where('tb_master_item_categories.category', $this->input->post('id_kategori'));
    if ($_POST['length'] != -1)
      $this->db->limit($_POST['length'], $_POST['start']);
    return $this->db->get()->result();
  }
  
  public function countItemCot()
  {
    $this->db->select('tb_master_items.id, tb_master_items.description');
    $this->db->from('tb_master_items');
    $this->db->join('tb_master_item_groups', 'tb_master_items.group = tb_master_item_groups.group');
    $this->db->join('tb_master_item_categories', 'tb_master_item_groups.category = tb_master_item_categories.category');
    $this->db->join('tb_auth_user_categories', 'tb_master_item_categories.category = tb_auth_user_categories.category');
    $this->db->where('tb_auth_user_categories.username', config_item('auth_username'));
    if(($_POST['id_kategori'] != "all")&&($this->input->post('id_kategori')!=""))
      $this->db->where('tb_master_item_categories.category', $this->input->post('id_kategori'));
    $this->db->group_start();
      $this->db->like('tb_master_items.description', trim(strtoupper($this->input->post('search'))), 'BOTH');
      $this->db->or_like('tb_master_items.part_number', trim(strtoupper($this->input->post('search'))), 'BOTH');
       $this->db->or_like('tb_master_items.serial_number', trim(strtoupper($this->input->post('search'))), 'BOTH');
    $this->db->group_end();
    return $this->db->get()->num_rows(); 
  }
  // public function cotProcess($hour,$year,$id_kelipatan,$kelipatan,$itemKey,$standardQuantity)
  public function cotProcess($hour,$year,$id_kelipatan,$kelipatan,$itemKey,$standardQuantity,$range1,$range2)
  {
    $result['insert'] = 0;
    $result['update'] = 0;
    $result['error'] = array();
    foreach ($itemKey as $part_number) {
        // $id_stock =  getStockId($key,"SERVICEABLE");
      $this->db->order_by('id',"asc")
      ->limit(1)
      ->like('part_number', $part_number)
      ->from('tb_master_items');
      $query_item = $this->db->get();
      $row_item   = $query_item->unbuffered_row('array');
      $key = $row_item['id'];

      $exist = $this->checkCotItems($hour,$year,$id_kelipatan,$key,$id_stock);
      $onhand = $this->countOnhand($part_number)->sum;
      if($exist['status']){
        $id = $exist['id'];
        $qty_standar = $standardQuantity->$part_number;//$standardQuantity->$key
        $oldData = $exist['data'];
        $range1_val  = $range1->$part_number;
        $range2_val  = $range2->$part_number;

         $update = $this->updateCot($id,$hour,$year,$id_kelipatan,$kelipatan,$key,$qty_standar,$onhand,$oldData);
        if($update['status']){
          $result['update'] +=1;
          $this->cotToBudgeting($id,$update['qty_requirement'],$hour,$key,$onhand,$range1_val,$range2_val);
        } else {
          $item = $this->itemById($key);
          array_push($result['error'], "Item ".$item->description);
        }
      }else{

        $qty_standar = $standardQuantity->$part_number;//$standardQuantity->$key
        $range1_val  = $range1->$part_number;
        $range2_val  = $range2->$part_number;
        $oldData     = $exist['data'];

        // $insert = $this->insertCot($hour,$year,$id_kelipatan,$kelipatan,$key,$qty_standar,$onhand,$part_number,$oldData);
        $insert = $this->insertCot($hour,$year,$id_kelipatan,$kelipatan,$key,$qty_standar,$onhand,$part_number,$oldData);
        
        if($insert['status']){
          $result['insert'] +=1;
          $id_cot = $this->db->insert_id();
          // $this->cotToBudgeting($id_cot,$insert['qty_requirement'],$hour,$key,$onhand);
           $this->cotToBudgeting($id_cot,$insert['qty_requirement'],$hour,$key,$onhand,$range1_val,$range2_val);
        } else {
          $item = $this->itemById($key);
          array_push($result['error'], "Item ".$item->description);
        }
      }
    }
    $this->send_mail();
    return $result;
  }
  // function cotToBudgeting($id_cot,$qty_requirement,$hour,$id_item,$onhand){
  //   $item = $this->itemById($id_item);
  //   $qty_requirement = $qty_requirement-$onhand < 0 ? 0 : $qty_requirement-$onhand;
  //   $price = $item->current_price;
  //   $avgMonthHour = floor($hour/12);
  //   $desHour = $hour-($avgMonthHour*11);
  //   $avgMonthQty = floor(($avgMonthHour/$hour)*$qty_requirement);
  //   $desQty = $qty_requirement - ($avgMonthQty*11);
  //   $initial_budget = 0;
  //   $initial_quantity = 0;
  //   $mtd_budget = 0;
  //   $mtd_quantity = 0;        
  //   $mtd_prev_month_budget = 0;
  //   $mtd_prev_month_quantity = 0;
  //   $ytd_budget = 0;
  //   $ytd_quantity = 0;
  //   $created_at = date('Y-m-d H:i:s');
  //   $updated_at = date('Y-m-d H:i:s');
  //   $created_by = config_item('auth_username');
  //   for ($i=1; $i <13 ; $i++) { 
  //     $month_number = $i;
  //     $initial_quantity = ($i == 12) ? $desQty : $avgMonthQty;
  //     $initial_budget = $initial_quantity * $price;
  //     $mtd_budget = $initial_budget;
  //     $mtd_quantity = $initial_quantity;        
  //     // $ytd_budget = $qty_requirement*$price;
  //     // $ytd_quantity = $qty_requirement;
  //     $ytd_budget = $ytd_budget+$mtd_budget;
  //     $ytd_quantity = $ytd_quantity+$mtd_quantity;
  //     $hourMonthly = ($i == 12) ? $desHour : $avgMonthHour;
  //     $isBudgetExist = $this->isBudgetExist($id_cot,$month_number);
  //     if(!$isBudgetExist['status']){
  //       $row = array("id_cot"=>$id_cot,"month_number"=>$month_number,"initial_budget"=>$initial_budget,"initial_quantity"=>$initial_quantity,"mtd_budget"=>$mtd_budget,"mtd_quantity"=>$mtd_quantity,"mtd_prev_month_budget"=>$mtd_prev_month_budget,"mtd_prev_month_quantity"=>$mtd_prev_month_quantity,"ytd_budget"=>$ytd_budget,"ytd_quantity"=>$ytd_quantity,"created_at"=>$created_at,"updated_at"=>$updated_at,"created_by"=>$created_by,"hour"=>$hourMonthly);
  //       $this->insertBudgeting($row);

  //     } else {
  //         $row = array("id_cot"=>$id_cot,"month_number"=>$month_number,"initial_budget"=>$initial_budget,"initial_quantity"=>$initial_quantity,"mtd_budget"=>$mtd_budget,"mtd_quantity"=>$mtd_quantity,"mtd_prev_month_budget"=>$mtd_prev_month_budget,"mtd_prev_month_quantity"=>$mtd_prev_month_quantity,"ytd_budget"=>$ytd_budget,"ytd_quantity"=>$ytd_quantity,"updated_at"=>$updated_at,"updated_by"=>$created_by,"hour"=>$hourMonthly);
  //            $this->updateBudgeting($isBudgetExist['id'],$row);
  //     }
  //     $mtd_prev_month_budget = $initial_budget;
  //     $mtd_prev_month_quantity = $initial_quantity;
  //   }
    

  // }

  function cotToBudgeting($id_cot,$qty_requirement,$hour,$id_item,$onhand,$range1_val,$range2_val){
    $item = $this->itemById($id_item);
    $qty_requirement = $qty_requirement-$onhand < 0 ? 0 : $qty_requirement-$onhand;
    $price = $item->current_price;
    $range = ($range2_val-$range1_val)+1;
    $avgMonthHour = floor($hour/$range);
    $desHour = $hour-($avgMonthHour*($range-1));
    $avgMonthQty = floor(($avgMonthHour/$hour)*$qty_requirement);
    $desQty = $qty_requirement - ($avgMonthQty*($range-1));
    $initial_budget = 0;
    $initial_quantity = 0;
    $mtd_budget = 0;
    $mtd_quantity = 0;        
    $mtd_prev_month_budget = 0;
    $mtd_prev_month_quantity = 0;
    $ytd_budget = 0;
    $ytd_quantity = 0;
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');
    $created_by = config_item('auth_username');
    for ($i=1; $i <13 ; $i++) { 
      $month_number = $i;
      if($i>=$range1_val && $i<=$range2_val){
        $initial_quantity = ($i == $range2_val) ? $desQty : $avgMonthQty;
        $initial_budget = $initial_quantity * $price;
        $mtd_budget = $initial_budget;
        $mtd_quantity = $initial_quantity;        
        // $ytd_budget = $qty_requirement*$price;
        // $ytd_quantity = $qty_requirement;
        $ytd_budget = $ytd_budget+$mtd_budget;
        $ytd_quantity = $ytd_quantity+$mtd_quantity;
        $hourMonthly = ($i == $range2_val) ? $desHour : $avgMonthHour;
      }else{
        $initial_quantity = 0;
        $initial_budget = 0;
        $mtd_budget = 0;
        $mtd_quantity = 0;        
        // $ytd_budget = $qty_requirement*$price;
        // $ytd_quantity = $qty_requirement;
        $ytd_budget = 0;
        $ytd_quantity = 0;
        $hourMonthly = 0;
      }
      
      $isBudgetExist = $this->isBudgetExist($id_cot,$month_number);
      if(!$isBudgetExist['status']){
        $row = array("id_cot"=>$id_cot,"month_number"=>$month_number,"initial_budget"=>$initial_budget,"initial_quantity"=>$initial_quantity,"mtd_budget"=>$mtd_budget,"mtd_quantity"=>$mtd_quantity,"mtd_prev_month_budget"=>$mtd_prev_month_budget,"mtd_prev_month_quantity"=>$mtd_prev_month_quantity,"ytd_budget"=>$ytd_budget,"ytd_quantity"=>$ytd_quantity,"created_at"=>$created_at,"updated_at"=>$updated_at,"created_by"=>$created_by,"hour"=>$hourMonthly);
        $this->insertBudgeting($row);

      } else {
          $row = array("id_cot"=>$id_cot,"month_number"=>$month_number,"initial_budget"=>$initial_budget,"initial_quantity"=>$initial_quantity,"mtd_budget"=>$mtd_budget,"mtd_quantity"=>$mtd_quantity,"mtd_prev_month_budget"=>$mtd_prev_month_budget,"mtd_prev_month_quantity"=>$mtd_prev_month_quantity,"ytd_budget"=>$ytd_budget,"ytd_quantity"=>$ytd_quantity,"updated_at"=>$updated_at,"updated_by"=>$created_by,"hour"=>$hourMonthly);
             $this->updateBudgeting($isBudgetExist['id'],$row);
      }
      $mtd_prev_month_budget = $initial_budget;
      $mtd_prev_month_quantity = $initial_quantity;
    }
    

  }

  function insertBudgeting($row){
    return $this->db->insert('tb_budget', $row);
  }
  function updateBudgeting($id,$row) {
    $this->db->where('id', $id);
    return $this->db->update('tb_budget', $row);
  }
  function isBudgetExist($id_cot,$month_number){
    $this->db->where('id_cot', $id_cot);
    $this->db->where('month_number', $month_number);
    $query = $this->db->get('tb_budget');
    $result['count'] = $query->num_rows();
    if($result['count']>0 ){
        $result['status'] = TRUE;
        $data = $query->result();
        $result['id'] = $data[0]->id;
    } else {
      $result['status'] = FALSE;
    }
    return $result;
  }
  function checkCotItems($hour,$year,$id_kelipatan,$id_item,$id_stock){
    $oldData['data'] = null;
    if($id_kelipatan === "0"){
      
      $used_qty = 0;
      $stock_in_store_ids = $this->stock_in_store_ids($id_item);
      foreach ($stock_in_store_ids as $key) {
        $issued_total = $this->count_issued_item($key->id,$year);
        $used_qty += $issued_total->sum;
      }
      $hour = $hour;
      $id_kelipatan = 0;
      $oldData['data'] = new \stdClass;
      $oldData['data']->qty_standar = "Other";
      $oldData['data']->qty_requirement = $used_qty;
      $oldData['data']->hours = $hour;
      $oldData['data']->id_kelipatan = 0;
    }
    if($id_kelipatan === "8"){
      
      // $used_qty = 0;
      // $stock_in_store_ids = $this->stock_in_store_ids($id_item);
      // foreach ($stock_in_store_ids as $key) {
      //   $issued_total = $this->count_issued_item($key->id,$year);
      //   $used_qty += $issued_total->sum;
      // }
      $hour = $hour;
      $id_kelipatan = 8;
      $oldData['data'] = new \stdClass;
      $oldData['data']->qty_standar = "1";
      $oldData['data']->qty_requirement = 1;
      $oldData['data']->hours = $hour;
      $oldData['data']->id_kelipatan = 8;
    }
    $this->db->where('hours', $hour);
    $this->db->where('year', $year);
    $this->db->where('id_item', $id_item);
    $this->db->from('tb_budget_cot');
    $query = $this->db->get();
    $result['data'] = $oldData;
    if ($query->num_rows()>0){
      $result['status'] = true;
      $row = $query->row();
      $result['id'] = $row->id;
      return $result;
    }else{
      $result['status'] = false;
      return $result;
    }
  }
  function stock_in_store_ids($id_stock){
    $this->db->select('id');
    $this->db->from('tb_stock_in_stores');
    $this->db->where('stock_id', $id_stock);
    return $this->db->get()->result();
  }
  function count_issued_item($stock_in_store_id,$year){
    $this->db->select('sum(issued_quantity)');
    $this->db->from('tb_issuance_items ');
    $this->db->where('stock_in_stores_id', $stock_in_store_id);
    $this->db->like('document_number','MS/%/'.($year-1));
    // $this->db->like('document_number', ($year-1));
    return $this->db->get()->row();
  }
  function countOnhand($id_stock){
    $this->db->select('sum(quantity)');
    $this->db->from('tb_stock_in_stores');
    //tambahan
    $this->db->join('tb_stocks','tb_stocks.id=tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items','tb_master_items.id=tb_stocks.item_id');
    $this->db->group_by('tb_master_items.part_number');
    //tambahan
    $this->db->where('tb_master_items.part_number', $id_stock);
    return $this->db->get('')->row();
  }
  function previousCot($id_item,$year){
    $this->db->where('id_item', $id_item);
    $this->db->where('year', ($year-1));
    $query = $this->db->get('tb_budget_cot');
    if($query->num_rows()>0){
      $result['status'] = true;
      $result['data'] = $query->row();
    }else {
      $result['status'] = false;
    }
    return $result;
  }
  function insertCot($hour,$year,$id_kelipatan,$kelipatan,$id_item,$qty_standar,$onhand,$part_number,$prevData = null){
    if($prevData['data'] == null){
      $qty_requirement = ($hour/$kelipatan)*$qty_standar;  
    } else {
      $hour = $prevData['data']->hours;
      $id_kelipatan = $prevData['data']->id_kelipatan;
      $qty_standar = $prevData['data']->qty_standar;
      $qty_requirement = $prevData['data']->qty_requirement;
    }
    
    $data = array("hours"=>$hour,"year"=>$year,"id_kelipatan"=>$id_kelipatan,"id_item"=>$id_item,"qty_standar"=>$qty_standar,"qty_requirement"=>$qty_requirement,"onhand"=>$onhand,"item_part_number"=>$part_number,"created_by"=>config_item('auth_person_name'));
    $result['status'] = $this->db->insert('tb_budget_cot', $data);
    $result['qty_requirement'] = $qty_requirement;
    return $result;
  }
  function itemById($id){
    $this->db->where('id', $id);
    return $this->db->get('tb_master_items')->row();
  }
  function updateCot($id,$hour,$year,$id_kelipatan,$kelipatan,$key,$qty_standar,$onhand,$prevData = null){
    if($prevData['data'] == null){
      $qty_requirement = ($hour/$kelipatan)*$qty_standar;  
    } else {
      $hour = $prevData['data']->hours;
      $id_kelipatan = $prevData['data']->id_kelipatan;
      $qty_standar = $prevData['data']->qty_standar;
      $qty_requirement = $prevData['data']->qty_requirement;
    }
    
    $data = array("hours"=>$hour,"year"=>$year,"id_kelipatan"=>$id_kelipatan,"id_item"=>$key,"qty_standar"=>$qty_standar,"qty_requirement"=>$qty_requirement,"onhand"=>$onhand);
    $this->db->where('id', $id);
    $result['status'] = $this->db->update('tb_budget_cot', $data);
    $result['qty_requirement'] = $qty_requirement;
    return $result;
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
    $message .= "<p>Permintaan Approval Baru di Budgeting.</p>";
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
