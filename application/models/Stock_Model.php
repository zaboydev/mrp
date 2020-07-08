<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_Model extends MY_Model
{
  public function __construct()
  {
    parent::__construct();
    // $this->load->library('My_PHPMailer');

    

  }

  public function getSelectedColumns()
  {
    $return = array(
      'tb_stocks.id'                                  => NULL,
      'tb_master_items.id as item_id'                      => 'Item Id',
      'tb_master_items.part_number'                   => 'Part Number',
      'tb_master_items.description'                   => 'Description',
      'tb_master_items.serial_number'                 => 'Serial Number',
      'tb_master_items.kode_stok'                     => 'Stock Code',
      'tb_master_item_groups.category'                => 'Category',
      'tb_master_item_groups.group'                   => 'Group',
      'tb_stocks.condition'                           => 'Condition',
      // 'SUM(tb_stock_in_stores.quantity) - (SUM(tb_receipt_items.received_quantity) - SUM(tb_issuance_items.issued_quantity) + SUM(tb_stock_adjustments.adjustment_quantity)) as initial_quantity' => 'Initial Qty',
      // 'SUM(tb_receipt_items.received_quantity) as received_quantity' => 'Received Qty',
      // 'SUM(tb_issuance_items.issued_quantity) as issued_quantity' => 'Issued Qty',
      // 'SUM(tb_stock_adjustments.adjustment_quantity) as adjustment_quantity' => 'Adjusment Qty',
      'SUM(tb_stock_in_stores.quantity) as quantity'  => 'Stock Quantity',
      // 'tb_stock_in_stores.quantity'  => 'Stock Quantity',
      'tb_stock_in_stores.unit_value'                 => 'Price per Unit',//sm
      'tb_master_items.minimum_quantity'              => 'Min. Stock',
      'tb_master_items.unit'                          => 'Unit',
      'tb_master_item_groups.coa'                     => 'COA',
      
      'tb_stock_in_stores.stores'                     => 'Stores',
      'tb_stock_in_stores.warehouse'                  => 'Base',
      'tb_stock_in_stores.remarks'                    => 'Remarks',
      'tb_stock_in_stores.reference_document'         => 'No Document',//sm
      'tb_stock_in_stores.received_date'              => 'Received Date',//sm

      
    );
    if (config_item('auth_role') == 'SUPERVISOR' || config_item('auth_role') == 'SUPER ADMIN' || config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE'){
        $return[NULL ]                                           = 'Total Price';
    }

    return $return;
  }

  public function getGroupedColumns()
  {
    return array(
      'tb_stocks.id',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.serial_number',
      'tb_master_item_groups.category',
      'tb_master_item_groups.group',
      'tb_stock_in_stores.unit_value', //sm
      'tb_stocks.condition',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit',
      'tb_stock_in_stores.stores',
      'tb_stock_in_stores.warehouse',
      'tb_stock_in_stores.remarks',
      'tb_stock_in_stores.received_date',//sm
      'tb_stock_in_stores.reference_document',//sm
      'tb_master_item_groups.coa',
      'tb_master_items.kode_stok',
      'tb_master_items.id',
      // 'tb_stock_in_stores.quantity',
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.serial_number',
      // 'tb_master_item_groups.category',
      'tb_master_item_groups.group',
      // 'tb_stock_in_stores.unit_value' ,
      'tb_master_items.unit',
      'tb_stock_in_stores.stores',
      'tb_stock_in_stores.warehouse',
      'tb_stock_in_stores.remarks',
      'tb_stock_in_stores.reference_document',//sm
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
      // 'tb_master_item_groups.category',
      'tb_master_item_groups.group',
      'tb_stocks.condition',
      'tb_stock_in_stores.unit_value' ,//sm
      // 'SUM(tb_stock_in_stores.quantity) - (SUM(tb_receipt_items.received_quantity) - SUM(tb_issuance_items.issued_quantity) + SUM(tb_stock_adjustments.adjustment_quantity))',
      // 'SUM(tb_receipt_items.received_quantity)',
      // 'SUM(tb_issuance_items.issued_quantity)',
      // 'SUM(tb_stock_adjustments.adjustment_quantity)',
      'SUM(tb_stock_in_stores.quantity)',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit',
      'tb_stock_in_stores.stores',
      'tb_stock_in_stores.warehouse',
      'tb_stock_in_stores.remarks' ,
      'tb_stock_in_stores.received_date',//sm
      'tb_stock_in_stores.reference_document',//sm
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

  public function getIndex($condition = 'SERVICEABLE', $warehouse= NULL,$quantity = NULL,$category = NULL, $jenis = NULL,$start_date = NULL, $end_date = NULL, $return = 'array')
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_stock_in_stores');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    // $this->db->where('tb_stock_in_stores.received_date >= ', $start_date);
    // $this->db->where('tb_stock_in_stores.received_date <= ', $end_date);
    $this->db->where('condition', $condition);
    $this->db->where_in('tb_stock_in_stores.warehouse', config_item('auth_warehouses'));
    // $this->db->where('tb_stock_in_stores.quantity != 0');//tambahan untuk poin no 15 relokasi

    if($quantity !== NULL){
      if($quantity == 'a'){
        $this->db->where('tb_stock_in_stores.quantity = 0');
      }
      if($quantity == 'b'){
        $this->db->where('tb_stock_in_stores.quantity != 0');
      }
    }else{
      $this->db->where('tb_stock_in_stores.quantity != 0');
    }

    if ($category !== NULL){
      $this->db->where('tb_master_item_groups.category', $category);
    } else {
      $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));
    }

    // if ($jenis !== NULL){
    //   $this->db->where('tb_master_item_groups.category', $category);
    // } 
    if ($jenis == 'mixing') {
      $this->db->where('tb_stock_in_stores.stock_id = 3');
    }

    if ($start_date && $end_date !== NULL){
      $this->db->where('tb_stock_in_stores.received_date >= ', $start_date);
      $this->db->where('tb_stock_in_stores.received_date <= ', $end_date);
    }

    if ($warehouse !== NULL){
      if($warehouse == 'WISNU'){
        $this->db->group_start()
                  ->like('tb_stock_in_stores.warehouse', 'WISNU')
                  // ->or_where('tb_stock_in_stores_reports.warehouse=', 'WISNU REKONDISI')
                  ->group_end();
      }
      if($warehouse == "all base rekondisi"){
        $this->db->group_start()
                  ->like('tb_stock_in_stores.warehouse', 'REKONDISI')
                  ->group_end();
      }
      if($warehouse == 'LOMBOK'){
        $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
      }
       if($warehouse == 'JEMBER'){
        $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
      }
       if($warehouse == 'SOLO'){
        $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
      }
       if($warehouse == 'PALANGKARAYA'){
        $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
      }
       if($warehouse == 'BSR REKONDISI'){
        $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
      }
       if($warehouse == 'BANYUWANGI'){
        $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
      }
      if($warehouse == 'WISNU REKONDISI'){
        $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
      }      
    }

    $this->db->group_by($this->getGroupedColumns());

    $this->searchIndex();

    $orderableColumns = $this->getOrderableColumns();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($orderableColumns[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('tb_stock_in_stores.received_date', 'desc');
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

  public function countIndexFiltered($condition = 'SERVICEABLE', $warehouse= NULL,$quantity = NULL,$category = NULL, $jenis = NULL,$start_date = NULL, $end_date = NULL)
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_stock_in_stores');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    // $this->db->where('tb_stock_in_stores.received_date >= ', $start_date);
    // $this->db->where('tb_stock_in_stores.received_date <= ', $end_date);
    // $this->db->where('tb_stock_in_stores.quantity != 0');//tambahan untuk poin no 15 relokasi
    $this->db->where('condition', $condition);
    $this->db->where_in('tb_stock_in_stores.warehouse', config_item('auth_warehouses'));

    if($quantity !== NULL){
      if($quantity == 'a'){
        $this->db->where('tb_stock_in_stores.quantity = 0');
      }
      if($quantity == 'b'){
        $this->db->where('tb_stock_in_stores.quantity != 0');
      }
    }else{
      $this->db->where('tb_stock_in_stores.quantity != 0');
    }

    if ($category !== NULL){
      $this->db->where('tb_master_item_groups.category', $category);
    }

    if ($start_date && $end_date !== NULL){
      $this->db->where('tb_stock_in_stores.received_date >= ', $start_date);
      $this->db->where('tb_stock_in_stores.received_date <= ', $end_date);
    }

    if ($warehouse !== NULL){
      if($warehouse == 'WISNU'){
        $this->db->group_start()
                  ->like('tb_stock_in_stores.warehouse', 'WISNU')
                  // ->or_where('tb_stock_in_stores_reports.warehouse=', 'WISNU REKONDISI')
                  ->group_end();
      }
      if($warehouse == "all base rekondisi"){
        $this->db->group_start()
                  ->like('tb_stock_in_stores.warehouse', 'REKONDISI')
                  ->group_end();
      }
      if($warehouse == 'LOMBOK'){
        $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
      }
       if($warehouse == 'JEMBER'){
        $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
      }
       if($warehouse == 'SOLO'){
        $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
      }
       if($warehouse == 'PALANGKARAYA'){
        $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
      }
       if($warehouse == 'BSR REKONDISI'){
        $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
      }
       if($warehouse == 'BANYUWANGI'){
        $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
      }      
      if($warehouse == 'WISNU REKONDISI'){
        $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
      } 
    }

    if ($jenis == 'mixing') {
      $this->db->where('tb_stock_in_stores.stock_id = 3');
    }

    $this->db->group_by($this->getGroupedColumns());
    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex($condition = 'SERVICEABLE', $warehouse= NULL,$quantity = NULL,$category = NULL, $jenis = NULL,$start_date = NULL, $end_date = NULL)
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_stock_in_stores');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    // $this->db->where('tb_stock_in_stores.received_date >= ', $start_date);
    // $this->db->where('tb_stock_in_stores.received_date <= ', $end_date);
    // $this->db->where('tb_stock_in_stores.quantity != 0');//tambahan untuk poin no 15 relokasi
    $this->db->where('condition', $condition);
    $this->db->where_in('tb_stock_in_stores.warehouse', config_item('auth_warehouses'));

    if($quantity !== NULL){
      if($quantity == 'a'){
        $this->db->where('tb_stock_in_stores.quantity = 0');
      }
      if($quantity == 'b'){
        $this->db->where('tb_stock_in_stores.quantity != 0');
      }
    }
    else{
      $this->db->where('tb_stock_in_stores.quantity != 0');
    }

    if ($category !== NULL){
      $this->db->where('tb_master_item_groups.category', $category);
    }

    if ($start_date && $end_date !== NULL){
      $this->db->where('tb_stock_in_stores.received_date >= ', $start_date);
      $this->db->where('tb_stock_in_stores.received_date <= ', $end_date);
    }

    if ($warehouse !== NULL){
      if($warehouse == 'WISNU'){
        $this->db->group_start()
                  ->like('tb_stock_in_stores.warehouse', 'WISNU')
                  // ->or_where('tb_stock_in_stores_reports.warehouse=', 'WISNU REKONDISI')
                  ->group_end();
      }
      if($warehouse == "all base rekondisi"){
        $this->db->group_start()
                  ->like('tb_stock_in_stores.warehouse', 'REKONDISI')
                  ->group_end();
      }
      if($warehouse == 'LOMBOK'){
        $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
      }
       if($warehouse == 'JEMBER'){
        $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
      }
       if($warehouse == 'SOLO'){
        $this->db->where('tb_stock_in_stores_.warehouse', $warehouse);
      }
       if($warehouse == 'PALANGKARAYA'){
        $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
      }
       if($warehouse == 'BSR REKONDISI'){
        $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
      }
       if($warehouse == 'BANYUWANGI'){
        $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
      }      
      if($warehouse == 'WISNU REKONDISI'){
        $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
      } 
    }

    if ($jenis == 'mixing') {
      $this->db->where('tb_stock_in_stores.stock_id = 3');
    }

    $this->db->group_by($this->getGroupedColumns());

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function findStock($id, $stores)
  {
    $selected_column    = array_keys($this->getSelectedColumns());
    $selected_column[]  = 'tb_master_items.alternate_part_number';

    $grouped_column     = $this->getGroupedColumns();
    $grouped_column[]   = 'tb_master_items.alternate_part_number';

    $this->db->select($selected_column);
    $this->db->from('tb_stocks');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.stock_id = tb_stocks.id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_stock_in_stores.stores', $stores);
    $this->db->where('tb_stocks.id', $id);
    $this->db->group_by($grouped_column);

    $query  = $this->db->get();
    $row    = $query->unbuffered_row('array');

    $this->db->select('tb_stock_in_stores.*');
    $this->db->from('tb_stock_in_stores');
    // $this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_in_stores.serial_id', 'left');
    $this->db->where('tb_stock_in_stores.stock_id', $id);
    // $this->db->where('tb_stock_in_stores.quantity > 0');//tambahan untuk task #15 relokasi
    $this->db->where('tb_stock_in_stores.stores', $stores);

    $query  = $this->db->get();
    $result = $query->result_array();
    $row['items'] = $result;

    return $row;
  }

  public function findById($id)
  {
    $selected_column = array(
      'tb_stock_in_stores.*',
      'tb_stocks.condition',
      'tb_master_items.serial_number',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.alternate_part_number',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit',
      'tb_master_item_groups.group',
      'tb_master_item_groups.category',
    );

    $this->db->select($selected_column);
    $this->db->from('tb_stock_in_stores');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    // $this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_in_stores.serial_id', 'left');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_stock_in_stores.id', $id);

    $query  = $this->db->get();
    $row    = $query->unbuffered_row('array');

    return $row;
  }

  public function searchStockInStores()
  {
    $this->column_select = array(
      'tb_stock_in_stores.id',
      'tb_stock_in_stores.stores',
      'tb_stock_in_stores.received_date',
      'tb_stock_in_stores.expired_date',
      'tb_stock_in_stores.unit_value',
      'tb_stock_in_stores.quantity',
      'tb_stock_in_stores.qty_konvers',
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
    // $this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_in_stores.serial_id', 'left');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
    $this->db->where_in('tb_master_item_groups.group', ['FUEL','MOGAS']);
    $this->db->where('tb_master_item_groups.category', $_SESSION['mix']['category']);
    // $this->db->where('tb_stocks.condition', 'SERVICEABLE');
    $this->db->where('tb_stock_in_stores.quantity > ', 0);
    $this->db->where('tb_stock_in_stores.warehouse', $_SESSION['mix']['warehouse']);
    $this->db->where('tb_stock_in_stores.id != ', $_SESSION['mix']['mixing_item']);
    // $this->db->group_by('tb_stocks.id,tb_stocks.condition,tb_master_items.serial_number,tb_master_items.part_number,tb_master_items.description,tb_master_items.alternate_part_number,tb_master_items.group,tb_master_items.unit,tb_master_items.unit_pakai,tb_stock_in_stores.stores');
    $this->db->order_by('tb_stock_in_stores.received_date ASC');

    $query  = $this->db->get();
    $result = $query->result_array();

    return $result;
  }

  public function searchStockInStoresforAdjustment()
  {
    $this->column_select = array(
      'tb_stock_in_stores.id',
      'tb_stock_in_stores.stores',
      'tb_stock_in_stores.received_date',
      'tb_stock_in_stores.expired_date',
      'tb_stock_in_stores.unit_value',
      'tb_stock_in_stores.quantity',
      'tb_stocks.condition',
      'tb_master_items.serial_number',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.alternate_part_number',
      'tb_master_items.group',
      'tb_master_items.unit',
    );

    $this->db->select($this->column_select);
    $this->db->from('tb_stock_in_stores');
    // $this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_in_stores.serial_id', 'left');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
    // $this->db->where('tb_master_item_groups.group', $_SESSION['mix']['group']);
    $this->db->where('tb_master_item_groups.category', $_SESSION['adj']['category']);
    // $this->db->where('tb_stocks.condition', 'SERVICEABLE');
    $this->db->where('tb_stock_in_stores.quantity > ', 0);
    $this->db->where('tb_stock_in_stores.warehouse', $_SESSION['adj']['warehouse']);
    // $this->db->where('tb_stock_in_stores.id != ', $_SESSION['mix']['mixing_item']);

    $this->db->order_by('tb_stock_in_stores.received_date ASC');

    $query  = $this->db->get();
    $result = $query->result_array();

    return $result;
  }

  // public function adjustment($id)
  // {
  //   $this->db->trans_begin();

  //   // GET STOCK IN STORES
  //   $this->db->from('tb_stock_in_stores');
  //   $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
  //   $this->db->where('tb_stock_in_stores.id', $id);

  //   $query = $this->db->get();
  //   $stock = $query->unbuffered_row('array');

  //   $adjustment_quantity  = floatval($_POST['adjustment_quantity']);
  //   $remarks              = $_POST['remarks'];
  //   $date                 = date('Y-m-d');

  //   // RECALCULATE STOCK
  //   $current_quantity     = floatval($stock['quantity']);
  //   $stores_quantity      = $current_quantity + $adjustment_quantity;
  //   $prev_quantity        = floatval($stock['total_quantity']);
  //   $balance_quantity     = floatval($stock['total_quantity']) + $adjustment_quantity;
  //   $unit_value           = floatval($stock['unit_value']);
  //   $total_value          = $stores_quantity * $stock['unit_value'];
  //   $grand_total_value    = floatval($stock['grand_total_value']) + $total_value;

  //   if ($balance_quantity == 0){
  //     $average_value = 0;
  //   } else {
  //     $average_value = $grand_total_value / $balance_quantity;
  //   }

  //   // CREATE ADJUSTMENT
  //   if (!empty($remarks))
  //     $this->db->set('remarks', $remarks);

  //   $this->db->set('stock_in_stores_id', $id);
  //   $this->db->set('date_of_entry', $date);
  //   $this->db->set('period_year', config_item('period_year'));
  //   $this->db->set('period_month', config_item('period_month'));
  //   $this->db->set('previous_quantity', $current_quantity);
  //   $this->db->set('adjustment_quantity', $adjustment_quantity);
  //   $this->db->set('balance_quantity', $balance_quantity);
  //   $this->db->set('adjustment_token', date('YmdHis'));
  //   $this->db->set('created_by', config_item('auth_person_name'));
  //   $this->db->insert('tb_stock_adjustments');
  //   $insert_id = $this->db->insert_id();

  //   // UPDATE STOCK IN STORES
  //   // done by trigger: adjusment_update_stock_in_stores()

  //   // UPDATE STOCK
  //   // done by trigger: update_stock_in_stores_update_stock()

  //   // CREATE STOCK CARD
  //   // move to app_model::adjustment_approval()
  //   // if ($adjustment_quantity >= 0){
  //   //   $this->db->set('received_by', config_item('auth_person_name'));
  //   //   $this->db->set('received_from', 'ADJUSTMENT');
  //   // } else {
  //   //   $this->db->set('issued_by', config_item('auth_person_name'));
  //   //   $this->db->set('issued_to', 'ADJUSTMENT');
  //   // }

  //   // $this->db->set('stock_id', $stock['stock_id']);
  //   // $this->db->set('serial_id', $stock['serial_id']);
  //   // $this->db->set('warehouse', $stock['warehouse']);
  //   // $this->db->set('stores', $stock['stores']);
  //   // $this->db->set('date_of_entry', $date);
  //   // $this->db->set('period_year', config_item('period_year'));
  //   // $this->db->set('period_month', config_item('period_month'));
  //   // $this->db->set('document_type', 'ADJUSTMENT');
  //   // $this->db->set('quantity', $adjustment_quantity);
  //   // $this->db->set('prev_quantity', $prev_quantity);
  //   // $this->db->set('balance_quantity', $balance_quantity);
  //   // $this->db->set('unit_value', $unit_value);
  //   // $this->db->set('average_value', $average_value);
  //   // $this->db->set('created_by', config_item('auth_person_name'));
  //   // $this->db->set('remarks', $remarks);
  //   // $this->db->insert('tb_stock_cards');

  //   if ($this->db->trans_status() === FALSE)
  //     return FALSE;

  //   $this->db->trans_commit();

  //   // $this->send_adjustment_request($insert_id);

  //   return TRUE;
  // }

   public function adjustment()
  {
    $this->db->trans_begin();
    $document_number = sprintf('%06s', $_SESSION['adj']['document_number']) . adj_format_number();
    $warehouse        = $_SESSION['adj']['warehouse'];
    foreach ($_SESSION['adj']['items'] as $key => $data){
        $base = ['WISNU'=>1,'BANYUWANGI'=>2,'SOLO'=>3,'LOMBOK'=>4,'JEMBER'=>5,'PALANGKARAYA'=>6,'WISNU REKONDISI'=>7,'BSR REKONDISI'=>8,];
        $warehouse_id=$base[$warehouse];
        $stock_id   = getStockId($data['item_id'], strtoupper($data['condition']));
        $serial     = getSerial($data['item_id'], $data['serial_number']);
        $serial_id  = $serial->id;

        // ADD to STORES
        if ($data['required_adj']>0) {

          $this->db->set('stock_id', $stock_id);
          $this->db->set('serial_id', $serial_id);
          $this->db->set('warehouse', $warehouse);
          $this->db->set('stores', strtoupper($data['stores']));
          $this->db->set('initial_quantity', floatval(0));
          $this->db->set('quantity', floatval(0));
          $this->db->set('unit_value', floatval(0));
          $this->db->set('initial_unit_value', floatval(0));
          $this->db->set('unit_value_dollar', floatval(0));
          $this->db->set('initial_unit_value_dollar', floatval(0));
          $this->db->set('kurs_dollar', 1);

          // $this->db->set('reference_document', $document_number);
          // $this->db->set('received_date', $received_date);
          if($data['no_expired_date'] !== 'no'){            
            $this->db->set('no_expired_date', 'yes');
            $this->db->set('expired_date', $data['expired_date']);
          }else{
            $this->db->set('no_expired_date', 'no');
          }            
          // $this->db->set('received_by', 'HASIL ADJUSTMENT');
          $this->db->set('created_by', config_item('auth_person_name'));
          // $this->db->set('remarks', 'HASIL ADJUSTMENT');
          $this->db->set('qty_konvers', floatval(0));
          $this->db->set('warehouse_id', $warehouse_id);
          $this->db->insert('tb_stock_in_stores');
          $stock_in_stores_id = $this->db->insert_id();
        }else{
          $this->db->select_max('id','id');
          $this->db->where('stock_id', $stock_id);
          $this->db->where('quantity >=',$data['required_adj'] );
          $this->db->from('tb_stock_in_stores');

          $query  = $this->db->get();
          $row    = $query->unbuffered_row();
          $stock_in_stores_id   = $row->id;
        }

        $this->db->from('tb_stock_in_stores');
        $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
        $this->db->where('tb_stock_in_stores.id', $stock_in_stores_id);
            

        $query = $this->db->get();
        $stock = $query->unbuffered_row('array');

        $adjustment_quantity  = floatval($data['adj_quantity']);
        $remarks              = (empty($_SESSION['adj']['notes'])) ? NULL : $_SESSION['adj']['notes'];
        $date                 = date('Y-m-d');

        $prev_stock = getStockPrev($stock_id,$data['stores']);
        if ($prev_stock == 0) {
          $unit_value = getLastUnitValue($stock_id,$data['stores']);
        }else{
          $unit_value = getAverageValue($stock_id,$data['stores'])+$prev_stock;
        }


        // RECALCULATE STOCK
        $current_quantity     = floatval($stock['quantity']);
        $stores_quantity      = $current_quantity + $adjustment_quantity;
        $prev_quantity        = floatval($stock['quantity']);
        $balance_quantity     = floatval($stock['quantity']) + $adjustment_quantity;
        $unit_value           = $unit_value;
        // if($data['adj_value'] == 0){
        //   $unit_value         = $unit_value;
        // }else{
        //   $unit_value         = $data['adj_value'];
        // }        
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
        $this->db->set('unit_value', floatval($prev_stock));
        if ($adjustment_quantity==0) {
          $this->db->set('total_value', floatval($unit_value));
        }else{
          $this->db->set('total_value', floatval($unit_value*$adjustment_quantity));
        }      
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

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();

    // $this->send_adjustment_request($insert_id);

    return TRUE;
  }

  public function send_adjustment_request($data)
  {
    $this->db->select(array(
      'tb_stock_adjustments.id',
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
    ));
    $this->db->from('tb_stock_adjustments');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_stock_adjustments.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_stock_adjustments.id', $data);

    $query = $this->db->get();
    $row  = $query->unbuffered_row('array');

    $this->load->library('My_PHPMailer');

    $message = "<p>Dear VP Finance,</p>";
    $message .= "<p>Berikut permintaan adjustment (Adjustment Request) dari ". $row['created_by'] ." yang dibuat pada tanggal ". $row['created_at'] ." :</p>";
    $message .= "<ul>";
    $message .= "<li>Deskripsi Barang: <strong>".$row['description']."</strong></li>";
    $message .= "<li>Part Number: <strong>".$row['part_number']."</strong></li>";
    $message .= "<li>Kategori Barang: <strong>".$row['category']."</strong></li>";
    $message .= "<li>Group Barang: <strong>".$row['group']."</strong></li>";
    $message .= "<li>Kondisi Barang: <strong>".$row['condition']."</strong></li>";
    $message .= "<li>Jumlah Adjustment diminta: <strong>".number_format($row['adjustment_quantity'])." ".$row['unit']."</strong></li>";
    $message .= "<li>Jumlah Stock Sebelumnya: <strong>".number_format($row['previous_quantity'])." ".$row['unit']."</strong></li>";
    $message .= "<li>Jumlah Setelah Adjustment: <strong>".number_format($row['balance_quantity'])." ".$row['unit']."</strong></li>";
    $message .= "<li>Remarks: <strong>".$row['remarks']."</strong></li>";
    $message .= "</ul>";
    $message .= "<p>Silakan klik pilihan <strong style='color:blue;'>APPROVE</strong> untuk menyetujui atau <strong style='color:red;'>REJECT</strong> untuk menolak permintaan ini.</p>";
    $message .= "<p>[ <a href='http://119.2.51.138:7323/adjustment?mode=approved&id=".$row['id']."&token=".$row['adjustment_token']."' style='color:blue; font-weight:bold;'>APPROVE</a> ] | [ <a href='http://119.2.51.138:7323/adjustment?mode=rejected&id=".$row['id']."&token=".$row['adjustment_token']."' style='color:red; font-weight:bold;'>REJECT</a> ]</p>";
    $message .= "<p>Thanks and regards</p>";


    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPDebug = 2;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = "tls";

    $mail->Host = "smtp.live.com";
    $mail->Port = 587;

    // $mail->Host = "smtp.gmail.com";
    // $mail->Port = 587;

    $mail->Username = "baliflight@hotmail.com";
    $mail->Password = "b1f42015";
    $mail->SetFrom('baliflight@hotmail.com', 'Material Resource Planning Software');
    // $mail->AddReplyTo($data['b_email'], $data['b_name']);
    $mail->Subject = "Permintaan Adjustment barang ".$row['description']." P/N#".$row['part_number'];
    $mail->Body = $message;
    $mail->IsHTML(true);
    $mail->AddAddress('emilia@baliflightacademy.com', 'Emilia Chang');
    // $mail->AddAddress('emilia@baliflightacademy.com', 'Emilia Chang');
    // if(!$mail->Send()) {
    //   $this->pretty_dump($mail->ErrorInfo);
    // } else {
    //   return true;
    // }
    $mail->send();

    // $this->load->library('email');
    // $result = $this->email->from('kiddo2095@gmail.com')->to('aidanurul99@rocketmail.com')->subject('Permintaan Adjustment Barang')->message($message)->send();
    // $subject = "Permintaan Adjustment barang ".$row['description']." P/N#".$row['part_number'];

    // $this->load->library('email');
    // $result = $this->email->from('aidanurul99@rocketmail.com')->to('kiddo2095@gmail.com')->subject($subject)->message($message)->send();

    return true;

    
  }

  public function pretty_dump($variable)
  {
      echo '<pre>';

      print_r($variable);

      echo '</pre>';

      exit();
  }

  public function relocation($id)
  {
    $this->db->trans_begin();

    // GET STOCK IN STORES
    $this->db->from('tb_stock_in_stores');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->where('tb_stock_in_stores.id', $id);

    $query = $this->db->get();
    $stock = $query->unbuffered_row('array');

    $relocation_stores    = $_POST['relocation_stores'];
    $current_stores       = $_POST['relocation_current_stores'];
    $remarks              = (empty($_POST['remarks'])) ? 'RELOCATION FROM STORES '.$current_stores : $_POST['remarks'].' RELOCATION FROM STORES '.$current_stores;
    $date                 = date('Y-m-d');

    // RECALCULATE STOCK
    $current_quantity     = floatval($stock['quantity']);
    $stores_quantity      = $current_quantity + 0;
    $prev_quantity        = floatval($stock['total_quantity']);
    $balance_quantity     = floatval($stock['total_quantity']) + 0;
    $unit_value           = floatval($stock['unit_value']);
    $total_value          = $stores_quantity * $stock['unit_value'];
    $grand_total_value    = floatval($stock['grand_total_value']) + $total_value;

    $prev_qty_stores_lama = getStockPrev($stock['stock_id'],$current_stores);

    $prev_qty_stores_baru = getStockPrev($stock['stock_id'],$relocation_stores);

    if ($balance_quantity == 0){
      $average_value = 0;
    } else {
      $average_value = $grand_total_value / $balance_quantity;
    }

    // $this->db->select('stores');
    // $this->db->where('id', $id);
    // $stores=$this->db->get();

    // RELOCATE STOCK
    $this->db->set('stores', $relocation_stores);
    $this->db->where('id', $id);
    $this->db->update('tb_stock_in_stores');

    // CREATE STOCK CARD stores lama
    $this->db->set('received_by', config_item('auth_person_name'));
    $this->db->set('received_from', 'RELOCATION');
    $this->db->set('stock_id', $stock['stock_id']);
    $this->db->set('serial_id', $stock['serial_id']);
    $this->db->set('warehouse', $stock['warehouse']);
    $this->db->set('stores', $current_stores);
    $this->db->set('date_of_entry', $date);
    $this->db->set('period_year', config_item('period_year'));
    $this->db->set('period_month', config_item('period_month'));
    $this->db->set('document_type', 'RELOCATION');
    $this->db->set('document_number', $stock['reference_document']);
    //$this->db->set('quantity', floatval(0));
    $this->db->set('quantity', 0-floatval($current_quantity));
    $this->db->set('prev_quantity', $prev_qty_stores_lama);
    $this->db->set('balance_quantity', $balance_quantity);
    $this->db->set('unit_value', $unit_value);
    $this->db->set('average_value', $average_value);
    $this->db->set('created_by', config_item('auth_person_name'));
    $this->db->set('remarks', $remarks);
    $this->db->set('issued_to', $remarks);
    $this->db->set('stock_in_stores_id',$id);
    $this->db->set('doc_type', 9);
    $this->db->set('tgl', date('Ymd'));
    $this->db->set('total_value',0-(floatval($current_quantity)*$unit_value));
    $this->db->insert('tb_stock_cards');
	
    // CREATE STOCK CARD ke stores baru
    $this->db->set('received_by', config_item('auth_person_name'));
    $this->db->set('received_from', 'RELOCATION STORES');
    $this->db->set('stock_id', $stock['stock_id']);
    $this->db->set('serial_id', $stock['serial_id']);
    $this->db->set('warehouse', $stock['warehouse']);
    $this->db->set('stores', $relocation_stores);
    $this->db->set('date_of_entry', $date);
    $this->db->set('period_year', config_item('period_year'));
    $this->db->set('period_month', config_item('period_month'));
    $this->db->set('document_type', 'RELOCATION');
    $this->db->set('document_number', $stock['reference_document']);
    //$this->db->set('quantity', floatval(0));
    $this->db->set('quantity', floatval($current_quantity));
    $this->db->set('prev_quantity', $prev_qty_stores_baru);
    $this->db->set('balance_quantity', $balance_quantity);
    $this->db->set('unit_value', $unit_value);
    $this->db->set('average_value', $average_value);
    $this->db->set('created_by', config_item('auth_person_name'));
    $this->db->set('remarks', $remarks);
    $this->db->set('issued_to', $remarks);
    $this->db->set('stock_in_stores_id',$id);
    $this->db->set('doc_type', 9);
    $this->db->set('tgl', date('Ymd'));
    $this->db->set('total_value',(floatval($current_quantity)*$unit_value));
    $this->db->insert('tb_stock_cards');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function mix()
  {
    $this->db->trans_begin();

    $div  = config_item('document_format_divider');
    $base = (config_item('include_base_on_document') === TRUE) ? $div . config_item('auth_warehouse') : NULL;
    $mod  = config_item('module');
    $year = date('Y');

    //$this =& get_instance();

    $this->db->select('code');
    $this->db->from( 'tb_master_item_categories' );
    $this->db->where('category', 'BAHAN BAKAR');

    $query  = $this->db->get();
    $row    = $query->unbuffered_row();
    $format = '/MS/MIX/'.$year;

    // $this->db->select_max('document_number', 'last_number');
    // $this->db->from('tb_issuances');
    // $this->db->like('document_number', $format, 'before');

    $this->db->select_max('document_number', 'last_number')
        ->like('document_number', $format, 'before')
        ->from('tb_issuances');

    $query  = $this->db->get();
    $row    = $query->unbuffered_row();
    $last   = $row->last_number;
    $number = substr($last, 0, 6);
    $next   = $number + 1;
    $return = sprintf('%06s', $next);
    $document_number        = $return.$format;
    $value_mix = 0;

    //$document_number        = '000004/MS/BB/2018';
    $issued_date            = date('Y-m-d');
    $issued_by              = config_item('auth_person_name');
    $issued_to              = 'MIX';
    $required_by            = '';
    $requisition_reference  = '';
    $approved_by            = '';
    $warehouse              = config_item('auth_warehouse');
    $category               = 'BAHAN BAKAR';
    $notes                  = '';

    $this->db->set('document_number', $document_number);
    $this->db->set('issued_to', $issued_to);
    $this->db->set('issued_date', $issued_date);
    $this->db->set('issued_by', $issued_by);
    $this->db->set('required_by', $required_by);
    $this->db->set('requisition_reference', $requisition_reference);
    $this->db->set('approved_by', $approved_by);
    $this->db->set('category', $category);
    $this->db->set('warehouse', $warehouse);
    $this->db->set('notes', $notes);
    $this->db->set('created_by', config_item('auth_person_name'));
    $this->db->set('updated_by', config_item('auth_person_name'));
    $this->db->insert('tb_issuances');

    // DECREASE STOCK
    foreach ($_SESSION['mix']['mixed_items'] as $key => $mixed_items) {
      $this->db->from('tb_stock_in_stores');
      $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
      $this->db->where('tb_stock_in_stores.id', $mixed_items['stock_in_stores_id']);

      $query = $this->db->get();
      $stock = $query->unbuffered_row('array');

      $mixed_quantity     = 0 - floatval($mixed_items['mixed_quantity']);
      $remarks            = 'Mixed to '. $_SESSION['mix']['description'] .' ('. $_SESSION['mix']['part_number'] .')';
      $current_quantity   = floatval($stock['quantity']);
      $stores_quantity    = $current_quantity - $mixed_quantity;
      $prev_quantity      = getStockPrev($stock['stock_id'],$stock['stores']);
      $balance_quantity   = getStockPrev($stock['stock_id'],$stock['stores']) + $mixed_quantity;
      $unit_value         = floatval($stock['unit_value']);
      $total_value        = $stores_quantity * $stock['unit_value'];
      $grand_total_value  = floatval($stock['grand_total_value']) + $total_value;
      $value_mix          = $value_mix+($mixed_items['mixed_quantity']*$stock['unit_value']);

      if ($balance_quantity == 0){
        $average_value = 0;
      } else {
        $average_value = $grand_total_value / $balance_quantity;
      }

      if (!empty($remarks))
        $this->db->set('remarks', $remarks);

      $this->db->set('stock_in_stores_id', $mixed_items['stock_in_stores_id']);
      $this->db->set('date_of_entry', $issued_date);
      $this->db->set('period_year', config_item('period_year'));
      $this->db->set('period_month', config_item('period_month'));
      $this->db->set('previous_quantity', $current_quantity);
      $this->db->set('adjustment_quantity', $mixed_quantity);
      $this->db->set('balance_quantity', $balance_quantity);
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->set('as_mix', TRUE);
      $this->db->set('updated_status', 'APPROVED');
      $this->db->set('unit_value', $unit_value);
      $this->db->set('total_value', floatval($unit_value*$mixed_quantity));
      $this->db->set('document_number',$document_number);
      $this->db->insert('tb_stock_adjustments');

      if ($mixed_quantity >= 0){
        $this->db->set('received_by', config_item('auth_person_name'));
        $this->db->set('received_from', 'MIX');
      } else {
        $this->db->set('issued_by', config_item('auth_person_name'));
        $this->db->set('issued_to', 'MIX');
      }

      $this->db->set('stock_id', $stock['stock_id']);
      $this->db->set('serial_id', $stock['serial_id']);
      $this->db->set('warehouse', $stock['warehouse']);
      $this->db->set('document_number', $document_number);
      $this->db->set('stores', $stock['stores']);
      $this->db->set('date_of_entry', $issued_date);
      $this->db->set('period_year', config_item('period_year'));
      $this->db->set('period_month', config_item('period_month'));
      $this->db->set('document_type', 'MIX');
      $this->db->set('quantity', $mixed_quantity);
      $this->db->set('prev_quantity', $prev_quantity);
      $this->db->set('balance_quantity', $balance_quantity);
      $this->db->set('unit_value', $unit_value);
      $this->db->set('average_value', $average_value);
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->set('remarks', $remarks);
      $this->db->set('stock_in_stores_id',$mixed_items['stock_in_stores_id']);
      $this->db->set('doc_type',2);
      $this->db->set('tgl',date('Ymd',strtotime($issued_date)));
      $this->db->set('total_value',floatval($unit_value) * floatval($mixed_quantity));
      $this->db->insert('tb_stock_cards');

      $this->db->set('document_number', $document_number);
      $this->db->set('stock_in_stores_id', $mixed_items['stock_in_stores_id']);
      $this->db->set('issued_quantity', floatval($mixed_items['mixed_quantity']));
      $this->db->set('issued_unit_value', floatval($unit_value));
      $this->db->set('issued_total_value', floatval($unit_value) * floatval($mixed_items['mixed_quantity']));
      $this->db->set('remarks', 'Mixed to MIX FUEL');
      $this->db->insert('tb_issuance_items');

    }

    // INCREASE STOCK


    $this->db->from('tb_stock_in_stores');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->where('tb_stock_in_stores.id', $_SESSION['mix']['mixing_item']);

    $query = $this->db->get();
    $stock = $query->unbuffered_row('array');

    $mixing_quantity    = floatval($_SESSION['mix']['mixing_quantity']);
    $remarks            = $_SESSION['mix']['notes'];
    $date               = date('Y-m-d');
    
    $unit_value         = floatval($value_mix/$mixing_quantity);
    $total_value        = $stores_quantity * $stock['unit_value'];
    $grand_total_value  = floatval($stock['grand_total_value']) + $total_value;
    $base = ['WISNU'=>1,'BANYUWANGI'=>2,'SOLO'=>3,'LOMBOK'=>4,'JEMBER'=>5,'PALANGKARAYA'=>6,'WISNU REKONDISI'=>7,'BSR REKONDISI'=>8,];
    $warehouse_id=$base[$stock['warehouse']];


    if ($balance_quantity == 0){
      $average_value = 0;
    } else {
      $average_value = $grand_total_value / $balance_quantity;
    }

    $this->db->set('stock_id', $stock['stock_id']);
    $this->db->set('serial_id', $stock['serial_id']);
    $this->db->set('warehouse', $stock['warehouse']);
    $this->db->set('stores', strtoupper($stock['stores']));
    $this->db->set('initial_quantity', floatval($mixing_quantity));
    $this->db->set('quantity', floatval(0));
    $this->db->set('unit_value', floatval($unit_value));
    $this->db->set('initial_unit_value', floatval($unit_value));
    $this->db->set('unit_value_dollar', floatval(0));
    $this->db->set('initial_unit_value_dollar', floatval(0));
    $this->db->set('kurs_dollar', 1);

    $this->db->set('reference_document', $document_number);
    $this->db->set('received_date', $date);
    $this->db->set('no_expired_date', 'no');          
    $this->db->set('received_by', config_item('auth_person_name'));
    $this->db->set('created_by', config_item('auth_person_name'));
    $this->db->set('remarks', 'HASIL MIXING');
    $this->db->set('qty_konvers', floatval(0));
    $this->db->set('warehouse_id', $warehouse_id);
    $this->db->insert('tb_stock_in_stores');
    $stock_in_stores_id = $this->db->insert_id();

    $current_quantity   = floatval(0);
    $prev_quantity      = getStockPrev($stock['stock_id'],$stock['stores']);
    $stores_quantity    = $current_quantity + $mixing_quantity;
    // $balance_quantity   = floatval($stock['quantity']) + $mixing_quantity;
    $balance_quantity   = floatval($prev_quantity) + $mixing_quantity;
    

    if (!empty($remarks))
      $this->db->set('remarks', $remarks);

    // $this->db->set('stock_in_stores_id', $_SESSION['mix']['mixing_item']);
    $this->db->set('stock_in_stores_id', $stock_in_stores_id);
    $this->db->set('date_of_entry', $date);
    $this->db->set('period_year', config_item('period_year'));
    $this->db->set('period_month', config_item('period_month'));
    $this->db->set('previous_quantity', $current_quantity);
    $this->db->set('adjustment_quantity', $mixing_quantity);
    $this->db->set('balance_quantity', $balance_quantity);
    $this->db->set('created_by', config_item('auth_person_name'));
    $this->db->set('as_mix', TRUE);
    $this->db->set('total_value', floatval($unit_value*$mixing_quantity));
    $this->db->set('document_number',$document_number);
    $this->db->set('updated_status', 'APPROVED');
    $this->db->insert('tb_stock_adjustments');

    if ($mixing_quantity >= 0){
      $this->db->set('received_by', config_item('auth_person_name'));
      $this->db->set('received_from', 'MIX');
    } else {
      $this->db->set('issued_by', config_item('auth_person_name'));
      $this->db->set('issued_to', 'MIX');
    }

    $this->db->set('stock_id', $stock['stock_id']);
    $this->db->set('serial_id', $stock['serial_id']);
    $this->db->set('warehouse', $stock['warehouse']);
    $this->db->set('stores', $stock['stores']);
	  $this->db->set('document_number', $document_number);
    $this->db->set('date_of_entry', $date);
    $this->db->set('period_year', config_item('period_year'));
    $this->db->set('period_month', config_item('period_month'));
    $this->db->set('document_type', 'MIX');
    $this->db->set('quantity', $mixing_quantity);
    $this->db->set('prev_quantity', $prev_quantity);
    $this->db->set('balance_quantity', $balance_quantity);
    $this->db->set('unit_value', $unit_value);
    $this->db->set('average_value', $average_value);
    $this->db->set('created_by', config_item('auth_person_name'));
    $this->db->set('remarks', $remarks);
    $this->db->set('stock_in_stores_id',$stock_in_stores_id);
    $this->db->set('doc_type',2);
    $this->db->set('tgl',date('Ymd',strtotime($date)));
    $this->db->set('total_value',floatval($unit_value) * floatval($mixing_quantity));
    $this->db->insert('tb_stock_cards');


    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function isValidStores($stores, $warehouse, $group)
  {
    $this->db->select('tb_master_item_groups.category');
    $this->db->from('tb_master_item_groups');
    $this->db->where('UPPER(tb_master_item_groups.group)', strtoupper($group));

    $query  = $this->db->get();
    $row    = $query->unbuffered_row();

    $this->db->from('tb_master_stores');
    $this->db->where('UPPER(tb_master_stores.stores)', strtoupper($stores));
    $this->db->where('UPPER(tb_master_stores.warehouse)', strtoupper($warehouse));
    $this->db->where('UPPER(tb_master_stores.category)', strtoupper($row->category));

    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? true : false;
  }

  public function import(array $user_data)
  {
    $this->db->trans_begin();

    foreach ($user_data as $key => $data){
      $reference_document = (empty($data['reference_document']))
        ? NULL : strtoupper($data['reference_document']);

      $expired_date = (empty($data['expired_date']))
        ? NULL : $data['expired_date'];

      $serial_number = (empty($data['serial_number']))
        ? NULL : strtoupper($data['serial_number']);

      $alternate_part_number = (empty($data['alternate_part_number']))
        ? NULL : strtoupper($data['alternate_part_number']);

      $part_number      = strtoupper($data['part_number']);
      $description      = strtoupper($data['description']);
      $group            = strtoupper($data['group']);
      $unit             = strtoupper($data['unit']);
      $warehouse        = strtoupper($data['warehouse']);
      $stores           = strtoupper($data['stores']);
      $condition        = strtoupper($data['condition']);
      $minimum_quantity = floatval($data['minimum_quantity']);
      $quantity         = floatval($data['quantity']);
      $unit_value       = floatval($data['unit_value']);
      $received_date    = $data['received_date'];
      $received_by      = $data['received_by'];
      $remarks          = $data['remarks'];
      $kode_stok       = $data['kode_stok'];
      $period_year      = get_setting('ACTIVE_YEAR');
      $period_month     = get_setting('ACTIVE_MONTH');

      // CREATE OR SKIP ITEM UNIT
      if (isItemUnitExists($unit) === FALSE){
        $data = array(
          'unit' => $unit,
          'created_by' => config_item('auth_person_name'),
          'updated_by' => config_item('auth_person_name'),
        );

        $this->db->insert('tb_master_item_units', $data);

        if ($this->db->affected_rows() == 0){
          die('tb_master_item_units');
        }
      }


      // GET ITEM_ID, CREATE OR SKIP ITEM
      if (isItemExists($part_number, $serial_number) === FALSE){
        $data = array(
          'part_number'           => $part_number,
          'serial_number'         => $serial_number,
          'alternate_part_number' => $alternate_part_number,
          'description'           => $description,
          'group'                 => $group,
          'unit'                  => $unit,
          'minimum_quantity'      => $minimum_quantity,
          'kode_stok'             => $kode_stok,
          'created_by'            => config_item('auth_person_name'),
          'updated_by'            => config_item('auth_person_name'),
        );

        $this->db->insert('tb_master_items', $data);

        if ($this->db->affected_rows() == 0){
          die('tb_master_items');
        }

        $item_id = $this->db->insert_id();
      } else {
        $item_id = getItemId($part_number, $serial_number);
      }

      /**
       * CREATE part number IF NOT EXISTS in tb master part number
       */

      if (isPartNumberExists($data['part_number']) === FALSE){
        $this->db->set('part_number', strtoupper($data['part_number']));
        $this->db->set('min_qty', $data['minimum_quantity']);        
        $this->db->set('item_id', $item_id);        
        $this->db->set('qty', $quantity);
        $this->db->insert('tb_master_part_number');
      }
      else{
        $qty_awal = getPartnumberQty($data['part_number']);

        $qty_baru = floatval($data['quantity']) + floatval($qty_awal);

        $this->db->set('qty', $qty_baru);
        $this->db->where('part_number', strtoupper($data['part_number']));
        $this->db->update('tb_master_part_number');
      }

      // GET SERIAL_ID, OR CREATE SERIAL NUMBER
      if ($serial_number !== NULL){
        if (isSerialExists($item_id, $serial_number) === FALSE){
          $data = array(
            'item_id'       => $item_id,
            'serial_number' => $serial_number,
            'warehouse'     => $warehouse,
            'stores'        => $stores,
            'condition'     => $condition,
            'updated_by'    => config_item('auth_person_name'),
          );

          $this->db->set($data);
          $this->db->insert('tb_master_item_serials');

          if ($this->db->affected_rows() == 0){
            die('tb_master_item_serials');
          }

          $serial_id  = $this->db->insert_id();
        } else {
          $serial     = getSerial($item_id, $serial_number);
          $serial_id  = $serial->id;

          $data = array(
            'warehouse' => $warehouse,
            'stores' => $stores,
            'condition' => $condition,
          );

          $this->db->where('id', $serial_id);
          $this->db->set($data);
          $this->db->update('tb_master_item_serials');

          if ($this->db->affected_rows() == 0){
            die('update tb_master_item_serials');
          }
        }
      } else {
        $serial_id = NULL;
      }

      // GET STOCK_ID, OR CREATE STOCK
      if (isStockExists($item_id, $condition) === FALSE){
        $data = array(
          'item_id' => $item_id,
          'condition' => $condition,
          'initial_total_quantity' => $quantity,
          'initial_grand_total_value' => floatval($quantity * $unit_value),
          'initial_average_value' => $unit_value,
          'created_by' => config_item('auth_person_name'),
        );

        $this->db->insert('tb_stocks', $data);

        if ($this->db->affected_rows() == 0){
          die('tb_stocks');
        }

        $stock_id = $this->db->insert_id();
      } else {
        $stock_id = getStockId($item_id, $condition);
      }

      if($warehouse=='WISNU'){
        $warehouse_id=1;
      }
      if($warehouse=='BANYUWANGI'){
        $warehouse_id=2;
      }
      if($warehouse=='SOLO'){
        $warehouse_id=3;
      }
      if($warehouse=='LOMBOK'){
        $warehouse_id=4;
      }
      if($warehouse=='JEMBER'){
        $warehouse_id=5;
      }
      if($warehouse=='PALANGKARAYA'){
        $warehouse_id=6;
      }
      if($warehouse=='WISNU REKONDISI'){
        $warehouse_id=7;
      }
      if($warehouse=='BSR REKONDISI'){
        $warehouse_id=8;
      }

      // CREATE STOCK IN STORES
      $data = array(
        'stock_id'            => $stock_id,
        'serial_id'           => $serial_id,
        'reference_document'  => $reference_document,
        'warehouse'           => $warehouse,
        'stores'              => $stores,
        'initial_quantity'    => $quantity,
        'initial_unit_value'  => $unit_value,
        'previous_quantity'   => $quantity,
        'quantity'            => $quantity,
        'unit_value'          => $unit_value,
        'received_by'         => $received_by,
        'received_date'       => $received_date,
        'expired_date'        => $expired_date,
        'remarks'             => $remarks,
        'warehouse_id'        => $warehouse_id,
        'created_by'          => config_item('auth_person_name'),
      );

      $this->db->insert('tb_stock_in_stores', $data);

      if ($this->db->affected_rows() == 0){
        die('tb_stock_in_stores');
      }

      // UPDATE STOCK
      // done by trigger: insert_stock_in_stores_update_stock

      // UPDATE SERIAL NUMBER
      if ($serial_number !== NULL){
        $this->db->set('warehouse', $warehouse);
        $this->db->set('stores', $stores);
        $this->db->set('condition', $condition);
        $this->db->set('updated_at', date('Y-m-d'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->where('id', $serial_id);
        $this->db->update('tb_master_item_serials');

        if ($this->db->affected_rows() == 0){
          die('update tb_master_item_serials 2');
        }
      }

      // RECALCULATE STOCK
      $total_value        = $quantity * $unit_value;
      $stock_active       = getStockActive($stock_id);
      $balance_quantity   = floatval($stock_active->total_quantity) + $quantity;
      $grand_total_value  = floatval($stock_active->grand_total_value) + $total_value;
      $average_value      = ($balance_quantity == 0) ? 0 : floatval($grand_total_value / $balance_quantity);

      // CREATE STOCK CARD
      $data = array(
        'stock_id' => $stock_id,
        'serial_id' => $serial_id,
        'warehouse' => $warehouse,
        'stores' => $stores,
        'document_number' => $reference_document,
        'date_of_entry' => $received_date,
        'period_year' => $period_year,
        'period_month' => $period_month,
        'document_type' => 'IMPORT',
        'received_from' => 'IMPORT',
        'quantity' => $quantity,
        'balance_quantity' => $balance_quantity,
        'unit_value' => $unit_value,
        'average_value' => $average_value,
        'remarks' => $remarks,
        'created_by' => config_item('auth_person_name'),
      );

      if ($received_by !== NULL){
        $data['received_by'] = $received_by;
      } else {
        $data['received_by'] = config_item('auth_person_name');
      }

      $this->db->insert('tb_stock_cards', $data);

      if ($this->db->affected_rows() == 0){
        die('tb_stock_cards');
      }
    }

    if ($this->db->trans_status() === FALSE){
      return FALSE;
    }

    $this->db->trans_commit();
    return TRUE;
  }

  public function searchItemsBySerial($category)
  {
    $this->column_select = array(
      'tb_master_items.serial_number',
      'tb_master_items.id',
      'tb_master_items.group',
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.alternate_part_number',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit',
      'tb_master_items.kode_stok',
     );

    $this->db->select($this->column_select);
    $this->db->from('tb_master_items');
    $this->db->join('tb_stocks', 'tb_stocks.item_id = tb_master_items.id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_master_items.serial_number IS NOT NULL', NULL, FALSE);
    $this->db->where('tb_stocks.total_quantity', 0);
    $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
    $this->db->where('tb_master_item_groups.category', $category);

    $this->db->order_by('tb_master_items.serial_number ASC');

    $query  = $this->db->get();
    $result = $query->result_array();

    return $result;
  }

  public function searchItemsByPartNumber($category)
  {
    $this->column_select = array(
      'tb_master_items.id',
      'tb_master_items.group',
      'tb_master_items.description',
      'tb_master_items.part_number',
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

  public function getSelectedColumnsAdj()
  {
    return array(
      null  => null,
      'tb_master_items.id'             => 'Item Id',
      'tb_master_items.part_number'               => 'Part Number',
      'tb_master_items.serial_number'                      => 'Serial Number',
      'tb_master_items.description'               => 'Description',
      'tb_master_item_groups.category'            => 'Category',
      'tb_master_items.group'                     => 'Group',
      'tb_stock_in_stores.warehouse'              => 'Base',
      'tb_stocks.condition'                       => 'Condition',
      'tb_stock_adjustments.created_at'           => 'Date',
      'tb_stock_adjustments.previous_quantity'    => 'Prev. Quantity',
      'tb_stock_adjustments.adjustment_quantity'  => 'Adj. Quantity',
      'tb_stock_adjustments.balance_quantity'     => 'Balance Quantity',
      'tb_master_items.unit'                      => 'Unit',
      'tb_stock_adjustments.remarks'              => 'Remarks',
      'tb_stock_in_stores.unit_value'             => 'Price',
      null                                        => 'Total Price'
    );
  }

  public function getOrderableColumnsAdj()
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
    );
  }

  public function getSearchableColumnsAdj()
  {
    return array(
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_item_groups.category',
      'tb_master_items.group',
      'tb_stock_in_stores.warehouse',
      'tb_stocks.condition',
      'tb_stock_adjustments.remarks',
    );
  }

  private function searchIndexAdj()
  {
    if (!empty($_POST['columns'][0]['search']['value'])){
      $search_as_mix = $_POST['columns'][0]['search']['value'];

      $this->db->where('tb_stock_adjustments.as_mix', $search_as_mix);
    }

    if (!empty($_POST['columns'][6]['search']['value'])){
      $search_created_at = $_POST['columns'][6]['search']['value'];
      $range_created_at  = explode(' ', $search_created_at);

      $this->db->where('DATE(tb_stock_adjustments.created_at) >= ', $range_created_at[0]);
      $this->db->where('DATE(tb_stock_adjustments.created_at) <= ', $range_created_at[1]);
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

    foreach ($this->getSearchableColumnsAdj() as $item){
      if ($_POST['search']['value']){
        $term = strtoupper($_POST['search']['value']);

        if ($i === 0){
          $this->db->group_start();
          $this->db->like('UPPER('.$item.')', $term);
        } else {
          $this->db->or_like('UPPER('.$item.')', $term);
        }

        if (count($this->getSearchableColumnsAdj()) - 1 == $i)
          $this->db->group_end();
      }

      $i++;
    }
  }

  public function getIndexAdj($return = 'array')
  {
    $this->db->select(array_keys($this->getSelectedColumnsAdj()));
    $this->db->from('tb_stock_adjustments');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_stock_adjustments.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');

    $this->searchIndex();

    $orderableColumns = $this->getOrderableColumnsAdj();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($orderableColumns[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('id', 'desc');
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

  public function countIndexFilteredAdj()
  {
    $this->db->from('tb_stock_adjustments');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_stock_adjustments.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndexAdj()
  {
    $this->db->from('tb_stock_adjustments');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_stock_adjustments.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function getSelectedColumnsMixing()
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

    if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'PROCUREMENT'){
        $return['tb_stock_adjustments.unit_value']              = 'Price';   
        $return['tb_stock_adjustments.total_value']                                           = 'Total Price';
    }
    return $return;
  }

  public function getOrderableColumnsMixing()
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

  public function getSearchableColumnsMixing()
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

  private function searchIndexMixing()
  {
    if (!empty($_POST['columns'][0]['search']['value'])){
      $search_as_mix = $_POST['columns'][0]['search']['value'];

      $this->db->where('tb_stock_adjustments.as_mix', $search_as_mix);
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

    foreach ($this->getSearchableColumnsMixing() as $item){
      if ($_POST['search']['value']){
        $term = strtoupper($_POST['search']['value']);

        if ($i === 0){
          $this->db->group_start();
          $this->db->like('UPPER('.$item.')', $term);
        } else {
          $this->db->or_like('UPPER('.$item.')', $term);
        }

        if (count($this->getSearchableColumnsMixing()) - 1 == $i)
          $this->db->group_end();
      }

      $i++;
    }
  }

  public function getIndexMixing($return = 'array')
  {
    $this->db->select(array_keys($this->getSelectedColumnsMixing()));
    $this->db->from('tb_stock_adjustments');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_stock_adjustments.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_stock_adjustments.as_mix','t');
    // $this->db->where('EXTRACT(YEAR FROM tb_stock_adjustments.date_of_entry)::integer = ', date('Y')-1);

    $this->searchIndexMixing();

    $orderableColumns = $this->getOrderableColumnsMixing();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($orderableColumns[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('id', 'desc');
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

  public function countIndexFilteredMixing()
  {
    $this->db->from('tb_stock_adjustments');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_stock_adjustments.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_stock_adjustments.as_mix','t');
    // $this->db->where('EXTRACT(YEAR FROM tb_stock_adjustments.date_of_entry)::integer = ', date('Y')-1);

    $this->searchIndexMixing();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndexMixing()
  {
    $this->db->from('tb_stock_adjustments');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_stock_adjustments.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_stock_adjustments.as_mix','t');
    // $this->db->where('EXTRACT(YEAR FROM tb_stock_adjustments.date_of_entry)::integer = ', date('Y')-1);

    $query = $this->db->get();

    return $query->num_rows();
  }

  function coaByGroup($group)
  {
    $this->db->select('coa');
    $this->db->from('tb_master_item_groups');
    $this->db->where('group', $group);
    return $this->db->get()->row();
  }

  function codeByDescription($id)
  {
    $this->db->select('tb_master_items.kode_pemakaian');
    $this->db->select('tb_master_coa.group');
    $this->db->from('tb_master_items');
    $this->db->join('tb_stocks', 'tb_stocks.item_id=tb_master_items.id');
    $this->db->join('tb_master_coa', 'tb_master_coa.coa=tb_master_items.kode_pemakaian');
    $this->db->where('tb_stocks.id', $id);

    return $this->db->get()->row();
  }

}
