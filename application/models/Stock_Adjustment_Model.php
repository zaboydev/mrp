<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_Adjustment_Model extends MY_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function getSelectedColumns()
  {
    $return =  array(
      null  => null,
      'tb_master_items.id'                        => 'Item Id',
      'tb_stock_adjustments.date_of_entry'        => 'Date',
      'tb_master_items.part_number'               => 'Part Number',
      'tb_master_items.serial_number'             => 'Serial Number',
      'tb_master_items.description'               => 'Description',
      'tb_master_item_groups.category'            => 'Category',
      'tb_master_items.group'                     => 'Group',
      'tb_stock_in_stores.warehouse'              => 'Base',
      'tb_stock_in_stores.stores'                 => 'Stores',
      'tb_stocks.condition'                       => 'Condition',
      'tb_stock_adjustments.previous_quantity'    => 'Prev. Quantity',
      'tb_stock_adjustments.adjustment_quantity'  => 'Adj. Quantity',
      'tb_stock_adjustments.balance_quantity'     => 'Balance Quantity',
      'tb_master_items.unit'                      => 'Unit',
      'tb_stock_adjustments.remarks'              => 'Remarks',
    );

    if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'PROCUREMENT' || config_item('auth_role') == 'SUPER ADMIN'){   
        $return['tb_stock_adjustments.unit_value']              = 'Price';   
        $return['tb_stock_adjustments.total_value']                                           = 'Total Price';
       
    }
    return $return;
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_item_groups.category',
      'tb_master_items.group',
      'tb_stock_in_stores.warehouse',
      'tb_stocks.condition',
      'tb_stock_adjustments.created_at',
      'tb_stock_adjustments.previous_quantity',
      'tb_stock_adjustments.adjustment_quantity',
      'tb_stock_adjustments.balance_quantity',
      'tb_master_items.unit',
      'tb_stock_adjustments.remarks',
      'tb_stock_in_stores.stores',
      'tb_stock_adjustments.total_value'
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_item_groups.category',
      'tb_master_items.group',
      'tb_stock_in_stores.warehouse',
      'tb_stocks.condition',
      'tb_stock_adjustments.remarks',
      'tb_stock_in_stores.stores'
    );
  }

  private function searchIndex()
  {
    if (!empty($_POST['columns'][0]['search']['value'])){
      $search_as_mix = $_POST['columns'][0]['search']['value'];

      $this->db->where('tb_stock_adjustments.as_mix', $search_as_mix);
    }
	
	if (!empty($_POST['columns'][1]['search']['value'])){
      $status = $_POST['columns'][1]['search']['value'];

      $this->db->where('tb_stock_adjustments.updated_status', $status);
    }

    if (!empty($_POST['columns'][6]['search']['value'])){
      $search_created_at = $_POST['columns'][6]['search']['value'];
      $range_created_at  = explode(' ', $search_created_at);

      $this->db->where('DATE(tb_stock_adjustments.date_of_entry) >= ', $range_created_at[0]);
      $this->db->where('DATE(tb_stock_adjustments.date_of_entry) <= ', $range_created_at[1]);
    }

    if (!empty($_POST['columns'][3]['search']['value'])){
      $search_category = $_POST['columns'][3]['search']['value'];

      $this->db->where('tb_master_item_groups.category', $search_category);
    }

    if (!empty($_POST['columns'][4]['search']['value'])){
      $search_warehouse = $_POST['columns'][4]['search']['value'];

      $this->db->where('tb_stock_in_stores.warehouse', $search_warehouse);
    }

    if (!empty($_POST['columns'][5]['search']['value'])){
      $search_condition = $_POST['columns'][5]['search']['value'];

      $this->db->where('tb_stocks.condition', $search_condition);
    } else {
      $this->db->where('tb_stocks.condition', 'SERVICEABLE');
    }

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

  public function getIndex($return = 'array')
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_stock_adjustments');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_stock_adjustments.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_stock_adjustments.as_mix','f');
    // $this->db->where('EXTRACT(YEAR FROM tb_stock_adjustments.date_of_entry)::integer = ', date('Y'));

    $this->searchIndex();

    $orderableColumns = $this->getOrderableColumns();

    if (isset($_POST['order'])){
      //foreach ($_POST['order'] as $key => $order){
        //$this->db->order_by($orderableColumns[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      //}
	  $this->db->order_by('date_of_entry', 'desc');
    } else {
      $this->db->order_by('date_of_entry', 'desc');
    }

    if ($_POST['length'] != -1)
      $this->db->limit($_POST['length'], $_POST['start']);

    $query = $this->db->get();

    if ($return === 'object'){
      return $query->result();
    } elseif ($return === 'json'){
      return json_encode($query->result());
    } else {
      return $query->result_array();
    }
  }

  public function countIndexFiltered()
  {
    $this->db->from('tb_stock_adjustments');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_stock_adjustments.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_stock_adjustments.as_mix','f');
	  // $this->db->where('EXTRACT(YEAR FROM tb_stock_adjustments.date_of_entry)::integer = ', date('Y'));

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->from('tb_stock_adjustments');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_stock_adjustments.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_stock_adjustments.as_mix','f');
	  // $this->db->where('EXTRACT(YEAR FROM tb_stock_adjustments.date_of_entry)::integer = ', date('Y'));

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function searchItemsByPartNumber($category)
  {
    $this->column_select = array(
      'tb_master_items.id',
      'tb_master_items.group',
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.serial_number',
      'tb_master_items.alternate_part_number',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit',
      'tb_master_items.kode_stok'
     );

    $this->db->select($this->column_select);
    $this->db->from('tb_master_items');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
    $this->db->where('tb_master_item_groups.category', $category);

    $this->db->order_by('tb_master_items.group ASC, tb_master_items.description ASC');

    $query  = $this->db->get();
    $result = $query->result_array();

    return $result;
  }

  public function adjustment()
  {
    $this->db->trans_begin();
    $document_number = sprintf('%06s', $_SESSION['adj']['document_number']) . adj_format_number();
    $warehouse        = $_SESSION['adj']['warehouse'];
    foreach ($_SESSION['adj']['items'] as $key => $data){
        $stock_in_stores_id_awal = $data['stock_in_stores_id'];
        $this->db->from('tb_stock_in_stores');
        $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
        $this->db->where('tb_stock_in_stores.id', $stock_in_stores_id_awal);          

        $query = $this->db->get();
        $stock = $query->unbuffered_row('array');

        $base = ['WISNU'=>1,'BANYUWANGI'=>2,'SOLO'=>3,'LOMBOK'=>4,'JEMBER'=>5,'PALANGKARAYA'=>6,'WISNU REKONDISI'=>7,'BSR REKONDISI'=>8,];
        $warehouse_id=$base[$warehouse];
        $stock_id   = $stock['stock_id'];
        // $serial     = getSerial($data['item_id'], $data['serial_number']);
        $serial_id  = $stock['serial_id'];
        $prev_stock = getStockPrev($stock_id,$data['stores']);
        // if ($prev_stock == 0) {
        //   $unit_value = getMaxUnitValue($stock_id);
        // }else{
        //   $unit_value = getAverageValue($stock_id);
        // }

        //ADD to STORES        
        
        $stock_in_stores_id = $data['stock_in_stores_id'];

        $this->db->from('tb_stock_in_stores');
        $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
        $this->db->where('tb_stock_in_stores.id', $stock_in_stores_id);
            

        $query = $this->db->get();
        $stock = $query->unbuffered_row('array');

        $adjustment_quantity  = floatval($data['adj_quantity']);
        $remarks              = (empty($_SESSION['adj']['notes'])) ? NULL : $_SESSION['adj']['notes'];
        $date                 = $_SESSION['adj']['date'];       


        // RECALCULATE STOCK
        $current_quantity     = floatval($stock['quantity']);
        $stores_quantity      = $current_quantity + $adjustment_quantity;
        $prev_quantity        = floatval($stock['quantity']);
        $balance_quantity     = floatval($stock['quantity']) + $adjustment_quantity;
        $unit_value           = $data['adj_value']*$stock['kurs_dollar'];
		    $selisih  		 	  = floatval($unit_value)-floatval($stock['unit_value']);
		    $total_value 		  = $stock['quantity']*$selisih;
        if($adjustment_quantity > 0){
			   $unit_value         = $stock['unit_value'];
			   $total_value 		= $adjustment_quantity*$unit_value;
        }       
        // $total_value          = $stores_quantity * $stock['unit_value'];
        // $grand_total_value    = floatval($stock['grand_total_value']) + $total_value;

        // if ($balance_quantity == 0){
        //   $average_value = 0;
        // } else {
        //   $average_value = $grand_total_value / $balance_quantity;
        // }

        // CREATE ADJUSTMENT
        if (!empty($remarks))
          $this->db->set('remarks', $remarks);

        $this->db->set('stock_in_stores_id', $stock_in_stores_id);
        $this->db->set('date_of_entry', $date);
        $this->db->set('period_year', config_item('period_year'));
        $this->db->set('period_month', config_item('period_month'));
        $this->db->set('previous_quantity', $current_quantity);
        $this->db->set('adjustment_quantity', $adjustment_quantity);
        $this->db->set('balance_quantity', $balance_quantity);
        $this->db->set('adjustment_token', date('YmdHis'));
        $this->db->set('unit_value', floatval($unit_value));
		    $this->db->set('total_value', floatval($total_value));   
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('document_number', $document_number);
        $this->db->insert('tb_stock_adjustments');
        $insert_id = $this->db->insert_id();
    }

    

    // UPDATE STOCK IN STORES
    // done by trigger: adjusment_update_stock_in_stores()

    // UPDATE STOCK
    // done by trigger: update_stock_in_stores_update_stock()

    // CREATE STOCK CARD
    // move to app_model::adjustment_approval()
    // if ($adjustment_quantity >= 0){
    //   $this->db->set('received_by', config_item('auth_person_name'));
    //   $this->db->set('received_from', 'ADJUSTMENT');
    // } else {
    //   $this->db->set('issued_by', config_item('auth_person_name'));
    //   $this->db->set('issued_to', 'ADJUSTMENT');
    // }

    // $this->db->set('stock_id', $stock['stock_id']);
    // $this->db->set('serial_id', $stock['serial_id']);
    // $this->db->set('warehouse', $stock['warehouse']);
    // $this->db->set('stores', $stock['stores']);
    // $this->db->set('date_of_entry', $date);
    // $this->db->set('period_year', config_item('period_year'));
    // $this->db->set('period_month', config_item('period_month'));
    // $this->db->set('document_type', 'ADJUSTMENT');
    // $this->db->set('quantity', $adjustment_quantity);
    // $this->db->set('prev_quantity', $prev_quantity);
    // $this->db->set('balance_quantity', $balance_quantity);
    // $this->db->set('unit_value', $unit_value);
    // $this->db->set('average_value', $average_value);
    // $this->db->set('created_by', config_item('auth_person_name'));
    // $this->db->set('remarks', $remarks);
    // $this->db->insert('tb_stock_cards');    

    $this->send_mail($document_number);

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();

    return TRUE;
  }

  public function searchStockInStores($category)
  {
    $this->column_select = array(
      'tb_stock_in_stores.*',
      'tb_stocks.condition',
      'tb_master_items.serial_number',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.alternate_part_number',
      'tb_master_items.group',
      'tb_master_items.unit',
      'tb_master_items.unit_pakai',
    );

    $this->db->select($this->column_select);
    $this->db->from('tb_stock_in_stores');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');   
    $this->db->where('tb_master_item_groups.category', $category);	
	  $this->db->like('tb_stock_in_stores.reference_document', 'GRN');

    $query  = $this->db->get();
    $result = $query->result_array();

    return $result;
  }

  public function send_mail($document_number) { 
    $this->db->select(array(
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_item_groups.category',
      'tb_master_items.group',
      'tb_stocks.condition',
      'tb_stock_adjustments.created_by',
      'tb_stock_adjustments.created_at',
      'tb_stock_adjustments.previous_quantity',
      'tb_stock_adjustments.adjustment_quantity',
      'tb_stock_adjustments.balance_quantity',
      'tb_master_items.unit',
      'tb_stock_adjustments.remarks',
      'tb_stock_adjustments.adjustment_token',
      'tb_stock_adjustments.total_value',
    ));
    $this->db->from('tb_stock_adjustments');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_stock_adjustments.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_stock_adjustments.document_number',$document_number);
    $query = $this->db->get();
    $row = $query->result_array();

    $recipientList = $this->getNotifRecipient(3);
    $recipient = array();
    foreach ($recipientList as $key ) {
      array_push($recipient, $key->email);
    }

    $from_email = "bifa.acd@gmail.com";
    $to_email = "aidanurul99@rocketmail.com"; 
   
    //Load email library 
    $this->load->library('email'); 
    $config = array();
    $config['protocol'] = 'mail';
    $config['smtp_host'] = 'smtp.live.com';
    $config['smtp_user'] = 'bifa.acd@gmail.com';
    $config['smtp_pass'] = 'b1f42019';
    $config['smtp_port'] = 587;
    $config['smtp_auth']        = true;
    $config['mailtype']         = 'html';
    $this->email->initialize($config);
    $this->email->set_newline("\r\n");
    $message = "<p>Dear VP Finance</p>";
    $message .= "<p>Berikut permintaan Adjustment Baru dari Gudang :</p>";
    $message .= "<p>No Adjustment : ".$document_number."</p>";
    $message .= "<table width='100%'>";
    $message .= "<thead>
      <tr>
      <th width='20%'>Part Number</th>
      <th width='20%'>Description</th>
      <th width='20%'>Qty. Adj</th>
      <th width='20%'>Val. Adj</th>
      <th width='20%'>Remarks</th>
      </tr>
    </thead>";
    foreach ($row as $item) {
      $message .= '<tr>';
      $message .= '<td>'.$item['part_number'].'</td>';
      $message .= '<td>'.$item['description'].'</td>';
      $message .= '<td align="center">'.print_number($item['adjustment_quantity'],2).'</td>';
      $message .= '<td align="center">'.print_number($item['total_value'],2).'</td>';
      $message .= '<td>' . $item['remarks'] . '</td>';
      $message .= '</tr>';
    }
    $message .= "</table>";    
    $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
    $message .= "<p>[ <a href='http://119.252.163.206/permintaan_adjustment/' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
    $message .= "<p>Thanks and regards</p>";
    $this->email->from($from_email, 'Material Resource Planning'); 
    $this->email->to($recipient);
    $this->email->subject('Permintaan Approval Adjustment No : '.$document_number); 
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
