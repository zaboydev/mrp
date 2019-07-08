<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_Opname_Model extends MY_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function getSelectedColumns()
  {
    return array(
      'tb_master_items.id'                                                                      => NULL,
      'tb_master_items.part_number'                                                             => 'Part Number',
      'tb_master_items.description'                                                             => 'Description',
      'tb_master_items.serial_number'                                                           => 'Serial Number',
      'tb_master_item_groups.category'                                                          => 'Category',
      'tb_master_items.group'                                                                   => 'Group',
      'tb_stock_in_stores_reports.condition'                                                    => 'Condition',
      //tambahan
      // 'tb_stock_in_stores_reports.purchase_order_number'                                        => 'No. PO',
      // 'tb_stock_in_stores_reports.reference_document'                                           => 'Document Number',
      // 'tb_stock_in_stores_reports.received_date'                                                => 'Received Date',
      // 'tb_stock_in_stores_reports.expired_date'                                                 => 'Expired Date',
      'tb_master_item_groups.coa'                                                               => 'COA',
      'tb_master_items.kode_stok'                                                               => 'Stock Code',
      //end tambahan
      'tb_stock_in_stores_reports.warehouse'                                                    => 'Base',
      'tb_stock_in_stores_reports.stores'                                                       => 'Stores',
      'SUM(tb_stock_in_stores_reports.previous_quantity) AS previous_quantity'                  => 'Initial Qty',
      'SUM(tb_stock_in_stores_reports.previous_total_value) AS previous_total_value'                  => 'Initial Total Value',
      'SUM(tb_stock_in_stores_reports.total_received_quantity) AS total_received_quantity'      => 'Received Qty',      
      'SUM(tb_stock_in_stores_reports.total_received_total_value) AS total_received_total_value'      => 'Received Total Value',
      'SUM(tb_stock_in_stores_reports.total_issued_quantity) AS total_issued_quantity'          => 'Issued Qty',      
      'SUM(tb_stock_in_stores_reports.total_issued_total_value) AS total_issued_total_value'          => 'Issued Total Value',
      'SUM(tb_stock_in_stores_reports.total_adjustment_quantity) AS total_adjustment_quantity'  => 'Adjustment Qty',      
      'SUM(tb_stock_in_stores_reports.total_adjustment_total_value) AS total_adjustment_total_value'  => 'Adjustment Total Value',
      'SUM(tb_stock_in_stores_reports.current_quantity) AS current_quantity'                    => 'Balance Qty',
      'SUM(tb_stock_in_stores_reports.current_total_value) AS current_total_value'              => 'Total Value',
      // 'SUM(tb_stock_in_stores_reports.current_average_value) AS current_average_value' => 'Average Value',
      // '(CASE WHEN SUM(tb_stock_in_stores_reports.current_quantity) = 0 THEN 0 ELSE SUM(tb_stock_in_stores_reports.current_total_value)/SUM(tb_stock_in_stores_reports.current_quantity) END) AS current_average_value'             => 'Average Value',
      'tb_master_items.minimum_quantity'                                                        => 'Minimum Qty',
      'tb_master_items.unit'                                                                    => 'Unit',
    );
  }

  public function getGroupedColumns()
  {
    return array(
      'tb_master_items.id',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.serial_number',
      'tb_master_item_groups.category',
      'tb_master_items.group',
      'tb_stock_in_stores_reports.condition',
      //tambahan
      'tb_stock_in_stores_reports.purchase_order_number',
      'tb_stock_in_stores_reports.reference_document',
      'tb_stock_in_stores_reports.received_date',
      'tb_stock_in_stores_reports.expired_date',
      //end tambahan
      'tb_stock_in_stores_reports.warehouse',
      'tb_stock_in_stores_reports.stores',
      // 'SUM(tb_stock_in_stores_reports.previous_quantity)',
      // 'SUM(tb_stock_in_stores_reports.total_received_quantity)',
      // 'SUM(tb_stock_in_stores_reports.total_issued_quantity)',
      // 'SUM(tb_stock_in_stores_reports.total_adjustment_quantity)',
      // 'SUM(tb_stock_in_stores_reports.current_quantity)',
      // 'SUM(tb_stock_in_stores_reports.current_total_value)',
      // '(SUM(tb_stock_in_stores_reports.current_total_value)/SUM(tb_stock_in_stores_reports.current_quantity))',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit',
      'tb_master_item_groups.coa',
      'tb_master_items.kode_stok',
      //'tb_stocks.item_id',
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.serial_number',
      'tb_master_item_groups.category',
      'tb_master_items.group',
      'tb_stock_in_stores_reports.condition',
      //tambahan
      //'tb_receipt_items.purchase_order_number',
      'tb_stock_in_stores_reports.purchase_order_number',
      'tb_stock_in_stores_reports.reference_document',
      'tb_stock_in_stores_reports.received_date',
      'tb_stock_in_stores_reports.expired_date',
      //end tambahan
      'tb_stock_in_stores_reports.warehouse',
      'tb_stock_in_stores_reports.stores',
      'SUM(tb_stock_in_stores_reports.previous_quantity)',
      'SUM(tb_stock_in_stores_reports.total_received_quantity)',
      'SUM(tb_stock_in_stores_reports.total_issued_quantity)',
      'SUM(tb_stock_in_stores_reports.total_adjustment_quantity)',
      'SUM(tb_stock_in_stores_reports.current_quantity)',
      'SUM(tb_stock_in_stores_reports.current_total_value)',
      // 'SUM(tb_stock_in_stores_reports.current_average_value)',
      '(CASE WHEN SUM(tb_stock_in_stores_reports.current_quantity) = 0 THEN 0 ELSE SUM(tb_stock_in_stores_reports.current_total_value)/SUM(tb_stock_in_stores_reports.current_quantity) END)',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit',
      //'tb_stocks.item_id',
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.serial_number',
      'tb_master_item_groups.category',
      'tb_master_items.group',
      'tb_stock_in_stores_reports.condition',
      //tambahan
      //'tb_receipt_items.purchase_order_number',
      'tb_stock_in_stores_reports.purchase_order_number',
      'tb_stock_in_stores_reports.reference_document',
      //'tb_stock_in_stores_reports.received_date',
      //'tb_stock_in_stores_reports.expired_date',
      //end tambahan
      'tb_stock_in_stores_reports.warehouse',
      'tb_stock_in_stores_reports.stores',
      //'tb_stocks.item_id',
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

  public function getIndex($start_date, $end_date, $condition = "SERVICEABLE", $warehouse = NULL, $category = NULL, $quantity = NULL, $return = 'array')
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    //$this->db->select('tb_stocks.item_id');
    $this->db->from('tb_stock_in_stores_reports');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stock_in_stores_reports.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    // $this->db->where('tb_stock_in_stores_reports.period_year', $period_year);
    // $this->db->where('tb_stock_in_stores_reports.period_month', $period_month);
    $this->db->where('tb_stock_in_stores_reports.date_opname_start >=', $start_date);
    $this->db->where('tb_stock_in_stores_reports.date_opname_end <=', $end_date);
    $this->db->where('tb_stock_in_stores_reports.condition', $condition);
    $this->db->where('tb_stock_in_stores_reports.status', 'good');

    if($quantity !== NULL){
      if($quantity == 'a'){
        $this->db->where('tb_stock_in_stores_reports.current_quantity = 0');
      }
      if($quantity == 'b'){
        $this->db->where('tb_stock_in_stores_reports.current_quantity > 0');
      }
      // if($quantity == 'all'){
      //   $this->db->where('tb_stock_in_stores_reports.current_quantity > 0');
      // }
    }

    if ($warehouse !== NULL){
      if($warehouse == 'WISNU'){
        $this->db->group_start()
                  ->like('tb_stock_in_stores_reports.warehouse', 'WISNU')
                  // ->or_where('tb_stock_in_stores_reports.warehouse=', 'WISNU REKONDISI')
                  ->group_end();
      }
      if($warehouse == "all base rekondisi"){
        $this->db->group_start()
                  ->like('tb_stock_in_stores_reports.warehouse', 'REKONDISI')
                  ->group_end();
      }
      if($warehouse == 'LOMBOK'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'JEMBER'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'SOLO'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'PALANGKARAYA'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'BSR REKONDISI'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'BANYUWANGI'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }      
    }

    if ($category !== NULL){
      $this->db->where('tb_master_item_groups.category', $category);
    } else {
      $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));
    }

    // if ($quantity !== NULL){
    //   if($quantity == 'a'){
    //     $qty=0;
    //     $this->db->where('tb_stock_in_stores_reports.current_quantity <=', $qty);
    //   }if($quantity == 'b'){
    //     $qty=0;
    //     $this->db->where('tb_stock_in_stores_reports.current_quantity >', $qty);
    //   }if($quantity='all'){
    //     $this->db->where('tb_stock_in_stores_reports.current_quantity >= 0');
    //   }
      
    // }

    $this->db->group_by($this->getGroupedColumns());

    $this->searchIndex();

    $orderableColumns = $this->getOrderableColumns();

    if (isset($_POST['order'])){
      // foreach ($_POST['order'] as $key => $order){
      //   $this->db->order_by($orderableColumns[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      // }
      $this->db->order_by('id', 'acs');
    } else {
      $this->db->order_by('id', 'asc');
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

  public function countIndexFiltered($start_date, $end_date, $condition = "SERVICEABLE", $warehouse = NULL, $category = NULL, $quantity = NULL)
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_stock_in_stores_reports');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stock_in_stores_reports.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    //$this->db->join('tb_receipt_items', 'tb_receipt_items.stock_in_stores_id = tb_master_items.group');
    // $this->db->where('tb_stock_in_stores_reports.period_year', $period_year);
    // $this->db->where('tb_stock_in_stores_reports.period_month', $period_month);
    // $this->db->where('tb_stock_in_stores_reports.received_date >=', $start_date);
    // $this->db->where('tb_stock_in_stores_reports.received_date <=', $end_date);
    $this->db->where('tb_stock_in_stores_reports.date_opname_start >=', $start_date);
    $this->db->where('tb_stock_in_stores_reports.date_opname_end <=', $end_date);
    $this->db->where('tb_stock_in_stores_reports.condition', $condition);
    $this->db->where('tb_stock_in_stores_reports.status', 'good');

    if($quantity !== NULL){
      if($quantity == 'a'){
        $this->db->where('tb_stock_in_stores_reports.current_quantity = 0');
      }
      if($quantity == 'b'){
        $this->db->where('tb_stock_in_stores_reports.current_quantity > 0');
      }
    }

    if ($warehouse !== NULL){
      if($warehouse == 'WISNU'){
        $this->db->group_start()
                  ->like('tb_stock_in_stores_reports.warehouse', 'WISNU')
                  // ->or_where('tb_stock_in_stores_reports.warehouse=', 'WISNU REKONDISI')
                  ->group_end();
      }
      if($warehouse == "all base rekondisi"){
        $this->db->group_start()
                  ->like('tb_stock_in_stores_reports.warehouse', 'REKONDISI')
                  ->group_end();
      }
      if($warehouse == 'LOMBOK'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'JEMBER'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'SOLO'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'PALANGKARAYA'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'BSR REKONDISI'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'BANYUWANGI'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }      
    }

    if ($category !== NULL){
      $this->db->where('tb_master_item_groups.category', $category);
    } else {
      $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));
    }

    $this->db->group_by($this->getGroupedColumns());

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex($start_date, $end_date, $condition = "SERVICEABLE", $warehouse = NULL, $category = NULL, $quantity = NULL)
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_stock_in_stores_reports');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stock_in_stores_reports.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    // $this->db->where('tb_stock_in_stores_reports.period_year', $period_year);
    // $this->db->where('tb_stock_in_stores_reports.period_month', $period_month);

    // $this->db->where('tb_stock_in_stores_reports.received_date >=', $start_date);
    // $this->db->where('tb_stock_in_stores_reports.received_date <=', $end_date);

    $this->db->where('tb_stock_in_stores_reports.date_opname_start >=', $start_date);
    $this->db->where('tb_stock_in_stores_reports.date_opname_end <=', $end_date);
    $this->db->where('tb_stock_in_stores_reports.condition', $condition);
    $this->db->where('tb_stock_in_stores_reports.status', 'good');

    if($quantity !== NULL){
      if($quantity == 'a'){
        $this->db->where('tb_stock_in_stores_reports.current_quantity = 0');
      }
      if($quantity == 'b'){
        $this->db->where('tb_stock_in_stores_reports.current_quantity > 0');
      }
    }

    if ($warehouse !== NULL){
      if($warehouse == 'WISNU'){
        $this->db->group_start()
                  ->like('tb_stock_in_stores_reports.warehouse', 'WISNU')
                  // ->or_where('tb_stock_in_stores_reports.warehouse=', 'WISNU REKONDISI')
                  ->group_end();
      }
      if($warehouse == "all base rekondisi"){
        $this->db->group_start()
                  ->like('tb_stock_in_stores_reports.warehouse', 'REKONDISI')
                  ->group_end();
      }
      if($warehouse == 'LOMBOK'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'JEMBER'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'SOLO'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'PALANGKARAYA'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'BSR REKONDISI'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'BANYUWANGI'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }      
    }

    if ($category !== NULL){
      $this->db->where('tb_master_item_groups.category', $category);
    } else {
      $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));
    }

    $this->db->group_by($this->getGroupedColumns());

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function getIndexCancel($start_date, $end_date, $condition = "SERVICEABLE", $warehouse = NULL, $category = NULL, $quantity = NULL, $return = 'array')
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    //$this->db->select('tb_stocks.item_id');
    $this->db->from('tb_stock_in_stores_reports');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stock_in_stores_reports.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    // $this->db->where('tb_stock_in_stores_reports.period_year', $period_year);
    // $this->db->where('tb_stock_in_stores_reports.period_month', $period_month);
    $this->db->where('tb_stock_in_stores_reports.date_opname_start >=', $start_date);
    $this->db->where('tb_stock_in_stores_reports.date_opname_end <=', $end_date);
    $this->db->where('tb_stock_in_stores_reports.condition', $condition);
    $this->db->where('tb_stock_in_stores_reports.status', 'cancel');

    if($quantity !== NULL){
      if($quantity == 'a'){
        $this->db->where('tb_stock_in_stores_reports.current_quantity = 0');
      }
      if($quantity == 'b'){
        $this->db->where('tb_stock_in_stores_reports.current_quantity > 0');
      }
      // if($quantity == 'all'){
      //   $this->db->where('tb_stock_in_stores_reports.current_quantity > 0');
      // }
    }

    if ($warehouse !== NULL){
      if($warehouse == 'WISNU'){
        $this->db->group_start()
                  ->like('tb_stock_in_stores_reports.warehouse', 'WISNU')
                  // ->or_where('tb_stock_in_stores_reports.warehouse=', 'WISNU REKONDISI')
                  ->group_end();
      }
      if($warehouse == "all base rekondisi"){
        $this->db->group_start()
                  ->like('tb_stock_in_stores_reports.warehouse', 'REKONDISI')
                  ->group_end();
      }
      if($warehouse == 'LOMBOK'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'JEMBER'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'SOLO'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'PALANGKARAYA'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'BSR REKONDISI'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'BANYUWANGI'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }      
    }

    if ($category !== NULL){
      $this->db->where('tb_master_item_groups.category', $category);
    } else {
      $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));
    }

    // if ($quantity !== NULL){
    //   if($quantity == 'a'){
    //     $qty=0;
    //     $this->db->where('tb_stock_in_stores_reports.current_quantity <=', $qty);
    //   }if($quantity == 'b'){
    //     $qty=0;
    //     $this->db->where('tb_stock_in_stores_reports.current_quantity >', $qty);
    //   }if($quantity='all'){
    //     $this->db->where('tb_stock_in_stores_reports.current_quantity >= 0');
    //   }
      
    // }

    $this->db->group_by($this->getGroupedColumns());

    $this->searchIndex();

    $orderableColumns = $this->getOrderableColumns();

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

  public function countIndexFilteredCancel($start_date, $end_date, $condition = "SERVICEABLE", $warehouse = NULL, $category = NULL, $quantity = NULL)
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_stock_in_stores_reports');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stock_in_stores_reports.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    //$this->db->join('tb_receipt_items', 'tb_receipt_items.stock_in_stores_id = tb_master_items.group');
    // $this->db->where('tb_stock_in_stores_reports.period_year', $period_year);
    // $this->db->where('tb_stock_in_stores_reports.period_month', $period_month);
    // $this->db->where('tb_stock_in_stores_reports.received_date >=', $start_date);
    // $this->db->where('tb_stock_in_stores_reports.received_date <=', $end_date);
    $this->db->where('tb_stock_in_stores_reports.date_opname_start >=', $start_date);
    $this->db->where('tb_stock_in_stores_reports.date_opname_end <=', $end_date);
    $this->db->where('tb_stock_in_stores_reports.condition', $condition);
    $this->db->where('tb_stock_in_stores_reports.status', 'cancel');

    if($quantity !== NULL){
      if($quantity == 'a'){
        $this->db->where('tb_stock_in_stores_reports.current_quantity = 0');
      }
      if($quantity == 'b'){
        $this->db->where('tb_stock_in_stores_reports.current_quantity > 0');
      }
    }

    if ($warehouse !== NULL){
      if($warehouse == 'WISNU'){
        $this->db->group_start()
                  ->like('tb_stock_in_stores_reports.warehouse', 'WISNU')
                  // ->or_where('tb_stock_in_stores_reports.warehouse=', 'WISNU REKONDISI')
                  ->group_end();
      }
      if($warehouse == "all base rekondisi"){
        $this->db->group_start()
                  ->like('tb_stock_in_stores_reports.warehouse', 'REKONDISI')
                  ->group_end();
      }
      if($warehouse == 'LOMBOK'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'JEMBER'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'SOLO'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'PALANGKARAYA'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'BSR REKONDISI'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'BANYUWANGI'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }      
    }

    if ($category !== NULL){
      $this->db->where('tb_master_item_groups.category', $category);
    } else {
      $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));
    }

    $this->db->group_by($this->getGroupedColumns());

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndexCancel($start_date, $end_date, $condition = "SERVICEABLE", $warehouse = NULL, $category = NULL, $quantity = NULL)
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_stock_in_stores_reports');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stock_in_stores_reports.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    // $this->db->where('tb_stock_in_stores_reports.period_year', $period_year);
    // $this->db->where('tb_stock_in_stores_reports.period_month', $period_month);

    // $this->db->where('tb_stock_in_stores_reports.received_date >=', $start_date);
    // $this->db->where('tb_stock_in_stores_reports.received_date <=', $end_date);

    $this->db->where('tb_stock_in_stores_reports.date_opname_start >=', $start_date);
    $this->db->where('tb_stock_in_stores_reports.date_opname_end <=', $end_date);
    $this->db->where('tb_stock_in_stores_reports.condition', $condition);
    $this->db->where('tb_stock_in_stores_reports.status', 'cancel');

    if($quantity !== NULL){
      if($quantity == 'a'){
        $this->db->where('tb_stock_in_stores_reports.current_quantity = 0');
      }
      if($quantity == 'b'){
        $this->db->where('tb_stock_in_stores_reports.current_quantity > 0');
      }
    }

    if ($warehouse !== NULL){
      if($warehouse == 'WISNU'){
        $this->db->group_start()
                  ->like('tb_stock_in_stores_reports.warehouse', 'WISNU')
                  // ->or_where('tb_stock_in_stores_reports.warehouse=', 'WISNU REKONDISI')
                  ->group_end();
      }
      if($warehouse == "all base rekondisi"){
        $this->db->group_start()
                  ->like('tb_stock_in_stores_reports.warehouse', 'REKONDISI')
                  ->group_end();
      }
      if($warehouse == 'LOMBOK'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'JEMBER'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'SOLO'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'PALANGKARAYA'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'BSR REKONDISI'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }
       if($warehouse == 'BANYUWANGI'){
        $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
      }      
    }

    if ($category !== NULL){
      $this->db->where('tb_master_item_groups.category', $category);
    } else {
      $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));
    }

    $this->db->group_by($this->getGroupedColumns());

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function create()
  {
    $this->db->trans_begin();

    // get current period
    $current_year   = intval(config_item('period_year'));
    $current_month  = intval(config_item('period_month'));

    // get previous period
    if ($current_month === 1){
      $previous_month = 12;
      $previous_year  = $current_year - 1;
    } else {
      $previous_month = $current_month - 1;
      $previous_year  = $current_year;
    }

    // CREATE NEW STOCK REPORT
    // get all stocks by inventory
    $this->db->select('tb_stock_in_stores.*, tb_stocks.item_id, tb_stocks.condition');
    $this->db->from('tb_stock_in_stores');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));

    $query            = $this->db->get();
    $stock_in_stores  = $query->result_array();

    foreach ($stock_in_stores as $stock_detail) {
      $stock_in_stores_id         = intval($stock_detail['id']);
      $item_id                    = intval($stock_detail['item_id']);
      $condition                  = $stock_detail['condition'];
      $warehouse                  = $stock_detail['warehouse'];
      $stores                     = $stock_detail['stores'];
      $reference_document         = $stock_detail['reference_document'];
      $expired_date               = $stock_detail['expired_date'];
      $received_date              = $stock_detail['received_date'];
      $received_by                = $stock_detail['received_by'];
      $remarks                    = $stock_detail['remarks'];
      // $initial_quantity           = floatval($stock_detail['initial_quantity']);
      // $initial_unit_value         = floatval($stock_detail['initial_unit_value']);
      // $current_quantity           = floatval($stock_detail['quantity']);
      // $current_unit_value         = floatval($stock_detail['unit_value']);
      // $current_total_value        = $current_quantity * $current_unit_value;
      $current_average_value      = ($current_quantity == 0) ? floatval(0) : $current_total_value/$current_quantity;

      // set previous stock
      // $this->db->set('previous_quantity', $current_quantity);
      // $this->db->where('id', $stock_in_stores_id);
      // $this->db->update('tb_stock_in_stores');

      // get current period total stock adjustment
      $this->db->select_sum('tb_stock_adjustments.adjustment_quantity', 'total_adjustment_quantity');
      $this->db->select('tb_stock_adjustments.stock_in_stores_id');
      $this->db->from('tb_stock_adjustments');
      $this->db->where('tb_stock_adjustments.stock_in_stores_id', $stock_in_stores_id);
      $this->db->where('tb_stock_adjustments.period_year', $current_year);
      $this->db->where('tb_stock_adjustments.period_month', $current_month);
      $this->db->group_by('tb_stock_adjustments.stock_in_stores_id');

      $query  = $this->db->get();

      if ($query->num_rows() > 0){
        $adjustment = $query->unbuffered_row('array');
        $total_adjustment_quantity = $adjustment['total_adjustment_quantity'];
      } else {
        $total_adjustment_quantity = floatval(0);
      }

      // get current period total stock received
      $this->db->select_sum('tb_receipt_items.received_quantity', 'total_received_quantity');
      $this->db->select_sum('tb_receipt_items.received_total_value', 'total_received_total_value');
      $this->db->select('tb_receipt_items.purchase_order_number');
      $this->db->select('tb_receipt_items.stock_in_stores_id');
      $this->db->from('tb_receipts');
      $this->db->join('tb_receipt_items', 'tb_receipt_items.document_number = tb_receipts.document_number');
      $this->db->where('tb_receipt_items.stock_in_stores_id', $stock_in_stores_id);
      $this->db->where('EXTRACT(MONTH FROM tb_receipts.received_date)::integer = ', $current_month);
      $this->db->where('EXTRACT(YEAR FROM tb_receipts.received_date)::integer = ', $current_year);
      // $this->db->group_by('tb_receipt_items.stock_in_stores_id');
      $this->db->group_by('tb_receipt_items.stock_in_stores_id,tb_receipt_items.purchase_order_number');

      $query  = $this->db->get();

      if ($query->num_rows() > 0){
        $received = $query->unbuffered_row('array');
        $total_received_quantity      = floatval($received['total_received_quantity']);
        $total_received_total_value   = floatval($received['total_received_total_value']);
        //tambahan
        $purchase_order_number = $received['purchase_order_number'];

        if ($total_received_quantity == 0){
          $total_received_average_value = floatval(0);
        } else {
          $total_received_average_value = $total_received_total_value/$total_received_quantity;
        }
      } else {
        $total_received_quantity      = floatval(0);
        $total_received_total_value   = floatval(0);
        $total_received_average_value = floatval(0);
        //tambahan
        $purchase_order_number = $received['purchase_order_number'];

      }

      // get current period total stock issued
      $this->db->select_sum('tb_issuance_items.issued_quantity', 'total_issued_quantity');
      $this->db->select_sum('tb_issuance_items.issued_total_value', 'total_issued_total_value');
      $this->db->select('tb_issuance_items.stock_in_stores_id');
      $this->db->from('tb_issuances');
      $this->db->join('tb_issuance_items', 'tb_issuance_items.document_number = tb_issuances.document_number');
      $this->db->where('tb_issuance_items.stock_in_stores_id', $stock_in_stores_id);
      $this->db->where('EXTRACT(MONTH FROM tb_issuances.issued_date)::integer = ', $current_month);
      $this->db->where('EXTRACT(YEAR FROM tb_issuances.issued_date)::integer = ', $current_year);
      $this->db->group_by('tb_issuance_items.stock_in_stores_id');

      $query  = $this->db->get();

      if ($query->num_rows() > 0){
        $issued = $query->unbuffered_row('array');
        $total_issued_quantity        = floatval($issued['total_issued_quantity']);
        $total_issued_total_value     = floatval($issued['total_issued_total_value']);

        if ($total_issued_quantity == 0){
          $total_issued_average_value = floatval(0);
        } else {
          $total_issued_average_value = $total_issued_total_value/$total_issued_quantity;
        }
      } else {
        $total_issued_quantity      = floatval(0);
        $total_issued_total_value   = floatval(0);
        $total_issued_average_value = floatval(0);
      }

      // get quantity and value from previous stock opnames
      $this->db->from('tb_stock_in_stores_reports');
      $this->db->where('period_year', $previous_year);
      $this->db->where('period_month', $previous_month);
      $this->db->where('item_id', $item_id);
      $this->db->where('condition', $condition);
      $this->db->where('warehouse', $warehouse);
      $this->db->where('stores', $stores);

      $query  = $this->db->get();

      if ($query->num_rows() > 0){
        $initial_quantity = $current_quantity - (($total_received_quantity - $total_issued_quantity) + ($total_adjustment_quantity)); 

        $previous_quantity      = $initial_quantity;

        $previous_stock_in_stores   = $query->unbuffered_row('array');
        // $previous_quantity          = floatval($previous_stock_in_stores['current_quantity']);
        $previous_unit_value        = floatval($previous_stock_in_stores['current_unit_value']);
        $previous_total_value       = floatval($previous_stock_in_stores['current_total_value']);
        $previous_average_value     = floatval($previous_stock_in_stores['current_average_value']);
      } else {
        $initial_quantity = $current_quantity - (($total_received_quantity - $total_issued_quantity) + ($total_adjustment_quantity));

        $previous_quantity      = $initial_quantity;
        $previous_unit_value    = $initial_unit_value;
        $previous_total_value   = $initial_quantity * $initial_unit_value;

        if ($previous_quantity == 0){
          $previous_average_value = floatval(0);
        } else {
          $previous_average_value = $previous_total_value/$previous_quantity;
        }
      }

      // STOCK REPORTS
      $this->db->set('period_year', $current_year);
      $this->db->set('period_month', $current_month);
      $this->db->set('item_id', $item_id);
      $this->db->set('condition', $condition);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('stores', $stores);
      $this->db->set('previous_quantity', $previous_quantity);
      $this->db->set('previous_unit_value', $previous_unit_value);
      $this->db->set('previous_total_value', $previous_total_value);
      $this->db->set('previous_average_value', $previous_average_value);
      $this->db->set('current_quantity', $current_quantity);
      $this->db->set('current_unit_value', $current_unit_value);
      $this->db->set('current_total_value', $current_total_value);
      $this->db->set('current_average_value', $current_average_value);
      $this->db->set('total_received_quantity', $total_received_quantity);
      $this->db->set('total_received_total_value', $total_received_total_value);
      $this->db->set('total_received_average_value', $total_received_average_value);
      $this->db->set('total_issued_quantity', $total_issued_quantity);
      $this->db->set('total_issued_total_value', $total_issued_total_value);
      $this->db->set('total_issued_average_value', $total_issued_average_value);
      $this->db->set('total_adjustment_quantity', $total_adjustment_quantity);
      $this->db->set('reference_document', $reference_document);
      $this->db->set('expired_date', $expired_date);
      $this->db->set('received_date', $received_date);
      $this->db->set('received_by', $received_by);
      $this->db->set('remarks', $remarks);
      $this->db->set('created_by', config_item('auth_person_name'));
      //tambahan
      $this->db->set('purchase_order_number', $purchase_order_number);
      //tambahan

      $this->db->insert('tb_stock_in_stores_reports');
    }

    // CLOSE CURRENT PERIOD, SET TO NEXT PERIOD
    if ($current_month == 12){
      $next_month = 1;
      $next_year  = $current_year + 1;
    } else {
      $next_month = $current_month + 1;
      $next_year  = $current_year;
    }

    $this->db->set('setting_value', $next_year);
    $this->db->where('setting_name', 'ACTIVE_YEAR');
    $this->db->update('tb_settings');

    $this->db->set('setting_value', $next_month);
    $this->db->where('setting_name', 'ACTIVE_MONTH');
    $this->db->update('tb_settings');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  // public function opname_stock($start_date,$end_date)
  // {
  //   $this->db->trans_begin();

  //   // get current period
  //   $current_year   = intval(config_item('period_year'));
  //   $current_month  = intval(config_item('period_month'));

  //   // get previous period
  //   if ($current_month === 1){
  //     $previous_month = 12;
  //     $previous_year  = $current_year - 1;
  //   } else {
  //     $previous_month = $current_month - 1;
  //     $previous_year  = $current_year;
  //   }

  //   // CREATE NEW STOCK REPORT
  //   // get all stocks by inventory
  //   $this->db->select('tb_stock_in_stores.*, tb_stocks.item_id, tb_stocks.condition');
  //   $this->db->from('tb_stock_in_stores');
  //   $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
  //   $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
  //   $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
  //   $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));
  //   $this->db->where('tb_stock_in_stores.received_date <=', $end_date);
  //   // $this->db->where('tb_stock_in_stores.received_date <=', $end_date);

  //   $query            = $this->db->get();
  //   $stock_in_stores  = $query->result_array();

  //   foreach ($stock_in_stores as $stock_detail) {
  //     $stock_in_stores_id         = intval($stock_detail['id']);
  //     $item_id                    = intval($stock_detail['item_id']);
  //     $condition                  = $stock_detail['condition'];
  //     $warehouse                  = $stock_detail['warehouse'];
  //     $stores                     = $stock_detail['stores'];
  //     $reference_document         = $stock_detail['reference_document'];
  //     $expired_date               = $stock_detail['expired_date'];
  //     $received_date              = $stock_detail['received_date'];
  //     $received_by                = $stock_detail['received_by'];
  //     $remarks                    = $stock_detail['remarks'];
  //     // $initial_quantity           = floatval($stock_detail['initial_quantity']);
  //     // $initial_unit_value         = floatval($stock_detail['initial_unit_value']);
  //     // $current_quantity           = floatval($stock_detail['quantity']);
  //     // $current_unit_value         = floatval($stock_detail['unit_value']);
  //     // $current_total_value        = $current_quantity * $current_unit_value;
  //     // $current_average_value      = ($current_quantity == 0) ? floatval(0) : $current_total_value/$current_quantity;

  //     // set previous stock
  //     // $this->db->set('previous_quantity', $current_quantity);
  //     // $this->db->where('id', $stock_in_stores_id);
  //     // $this->db->update('tb_stock_in_stores');

  //     // get current period total stock adjustment
  //     $this->db->select_sum('tb_stock_adjustments.adjustment_quantity', 'total_adjustment_quantity');
  //     $this->db->select('tb_stock_adjustments.stock_in_stores_id');
  //     $this->db->from('tb_stock_adjustments');
  //     $this->db->where('tb_stock_adjustments.stock_in_stores_id', $stock_in_stores_id);
  //     $this->db->where('tb_stock_adjustments.date_of_entry >=', $start_date);
  //     $this->db->where('tb_stock_adjustments.date_of_entry <=', $end_date);
  //     $this->db->where('tb_stock_adjustments.updated_status !=', 'PENDING');
  //     // $this->db->where('tb_stock_adjustments.period_year', $current_year);
  //     // $this->db->where('tb_stock_adjustments.period_month', $current_month);
  //     $this->db->group_by('tb_stock_adjustments.stock_in_stores_id');

  //     $query  = $this->db->get();

  //     if ($query->num_rows() > 0){
  //       $adjustment = $query->unbuffered_row('array');
  //       $total_adjustment_quantity = $adjustment['total_adjustment_quantity'];
  //     } else {
  //       $total_adjustment_quantity = floatval(0);
  //     }

  //     // get current period total stock received
  //     $this->db->select_sum('tb_receipt_items.received_quantity', 'total_received_quantity');
  //     $this->db->select_sum('tb_receipt_items.received_total_value', 'total_received_total_value');
  //     $this->db->select('tb_receipt_items.purchase_order_number');
  //     $this->db->select('tb_receipt_items.stock_in_stores_id');
  //     $this->db->from('tb_receipts');
  //     $this->db->join('tb_receipt_items', 'tb_receipt_items.document_number = tb_receipts.document_number');
  //     $this->db->where('tb_receipt_items.stock_in_stores_id', $stock_in_stores_id);
  //     $this->db->where('tb_receipts.received_date >=', $start_date);
  //     $this->db->where('tb_receipts.received_date <=', $end_date);
  //     // $this->db->where('EXTRACT(MONTH FROM tb_receipts.received_date)::integer = ', $current_month);
  //     // $this->db->where('EXTRACT(YEAR FROM tb_receipts.received_date)::integer = ', $current_year);
  //     $this->db->group_by('tb_receipt_items.stock_in_stores_id');
  //     $this->db->group_by('tb_receipt_items.purchase_order_number');

  //     $query  = $this->db->get();

  //     if ($query->num_rows() > 0){
  //       $received = $query->unbuffered_row('array');
  //       $total_received_quantity      = floatval($received['total_received_quantity']);
  //       $total_received_total_value   = floatval($received['total_received_total_value']);
  //       //tambahan
  //       $purchase_order_number = $received['purchase_order_number'];

  //       if ($total_received_quantity == 0){
  //         $total_received_average_value = floatval(0);
  //       } else {
  //         $total_received_average_value = $total_received_total_value/$total_received_quantity;
  //       }
  //     } else {
  //       $total_received_quantity      = floatval(0);
  //       $total_received_total_value   = floatval(0);
  //       $total_received_average_value = floatval(0);
  //       //tambahan
  //       $purchase_order_number = $received['purchase_order_number'];

  //     }

  //     // get current period total stock issued
  //     $this->db->select_sum('tb_issuance_items.issued_quantity', 'total_issued_quantity');
  //     $this->db->select_sum('tb_issuance_items.issued_total_value', 'total_issued_total_value');
  //     $this->db->select('tb_issuance_items.stock_in_stores_id');
  //     $this->db->from('tb_issuances');
  //     $this->db->join('tb_issuance_items', 'tb_issuance_items.document_number = tb_issuances.document_number');
  //     $this->db->where('tb_issuance_items.stock_in_stores_id', $stock_in_stores_id);
  //     $this->db->where('tb_issuances.issued_date >=', $start_date);
  //     $this->db->where('tb_issuances.issued_date <=', $end_date);
  //     $this->db->where('tb_issuances.issued_to !=', 'MIX');
  //     // $this->db->where('EXTRACT(MONTH FROM tb_issuances.issued_date)::integer = ', $current_month);
  //     // $this->db->where('EXTRACT(YEAR FROM tb_issuances.issued_date)::integer = ', $current_year);
  //     $this->db->group_by('tb_issuance_items.stock_in_stores_id');

  //     $query  = $this->db->get();

  //     if ($query->num_rows() > 0){
  //       $issued = $query->unbuffered_row('array');
  //       $total_issued_quantity        = floatval($issued['total_issued_quantity']);
  //       $total_issued_total_value     = floatval($issued['total_issued_total_value']);

  //       if ($total_issued_quantity == 0){
  //         $total_issued_average_value = floatval(0);
  //       } else {
  //         $total_issued_average_value = $total_issued_total_value/$total_issued_quantity;
  //       }
  //     } else {
  //       $total_issued_quantity      = floatval(0);
  //       $total_issued_total_value   = floatval(0);
  //       $total_issued_average_value = floatval(0);
  //     }

  //     // get quantity and value from previous stock opnames
  //     $this->db->from('tb_stock_in_stores_reports');
  //     $this->db->where('period_year', $previous_year);
  //     $this->db->where('period_month', $previous_month);
  //     $this->db->where('item_id', $item_id);
  //     $this->db->where('condition', $condition);
  //     $this->db->where('warehouse', $warehouse);
  //     $this->db->where('stores', $stores);

  //     $query  = $this->db->get();
  //     $initial_quantity           = floatval($stock_detail['initial_quantity']);
  //     $initial_unit_value         = floatval($stock_detail['initial_unit_value']);
      

  //     if ($query->num_rows() > 0){
  //       // $initial_quantity = $current_quantity - (($total_received_quantity - $total_issued_quantity) + ($total_adjustment_quantity));

  //       // $previous_quantity      = $initial_quantity;

  //       $previous_stock_in_stores   = $query->unbuffered_row('array');
  //       $initial_quantity           = $current_quantity - (($total_received_quantity - $total_issued_quantity) + ($total_adjustment_quantity));

  //       $previous_quantity          = floatval($previous_stock_in_stores['current_quantity']);
  //       // $previous_quantity          = floatval($previous_stock_in_stores['current_quantity']);
  //       $previous_unit_value        = floatval($previous_stock_in_stores['current_unit_value']);
  //       $previous_total_value       = floatval($previous_stock_in_stores['current_total_value']);
  //       $previous_average_value     = floatval($previous_stock_in_stores['current_average_value']);
  //     } else {
  //       $initial_quantity = $current_quantity - (($total_received_quantity - $total_issued_quantity) + ($total_adjustment_quantity));

  //       $previous_quantity      = 0;
  //       $previous_unit_value    = $initial_unit_value;
  //       $previous_total_value   = $initial_quantity * $initial_unit_value;

  //       if ($previous_quantity == 0){
  //         $previous_average_value = floatval(0);
  //       } else {
  //         $previous_average_value = $previous_total_value/$previous_quantity;
  //       }
  //     }

  //     // $current_quantity           = floatval($stock_detail['quantity']);
  //     $current_quantity           = $previous_quantity + (($total_received_quantity - $total_issued_quantity) + ($total_adjustment_quantity));
  //     $current_unit_value         = floatval($stock_detail['unit_value']);
  //     $current_total_value        = $current_quantity * $current_unit_value;
  //     $current_average_value      = ($current_quantity == 0) ? floatval(0) : $current_total_value/$current_quantity;

  //     // STOCK REPORTS
  //     $this->db->set('period_year', $current_year);
  //     $this->db->set('period_month', $current_month);
  //     $this->db->set('item_id', $item_id);
  //     $this->db->set('condition', $condition);
  //     $this->db->set('warehouse', $warehouse);
  //     $this->db->set('stores', $stores);
  //     $this->db->set('previous_quantity', $previous_quantity);
  //     $this->db->set('previous_unit_value', $previous_unit_value);
  //     $this->db->set('previous_total_value', $previous_total_value);
  //     $this->db->set('previous_average_value', $previous_average_value);
  //     $this->db->set('current_quantity', $current_quantity);
  //     $this->db->set('current_unit_value', $current_unit_value);
  //     $this->db->set('current_total_value', $current_total_value);
  //     $this->db->set('current_average_value', $current_average_value);
  //     $this->db->set('total_received_quantity', $total_received_quantity);
  //     $this->db->set('total_received_total_value', $total_received_total_value);
  //     $this->db->set('total_received_average_value', $total_received_average_value);
  //     $this->db->set('total_issued_quantity', $total_issued_quantity);
  //     $this->db->set('total_issued_total_value', $total_issued_total_value);
  //     $this->db->set('total_issued_average_value', $total_issued_average_value);
  //     $this->db->set('total_adjustment_quantity', $total_adjustment_quantity);
  //     $this->db->set('reference_document', $reference_document);
  //     $this->db->set('expired_date', $expired_date);
  //     $this->db->set('received_date', $received_date);
  //     $this->db->set('received_by', $received_by);
  //     $this->db->set('remarks', $remarks);
  //     $this->db->set('date_opname_start', $start_date);
  //     $this->db->set('date_opname_end', $end_date);
  //     $this->db->set('created_by', config_item('auth_person_name'));
  //     //tambahan
  //     $this->db->set('purchase_order_number', $purchase_order_number);
  //     $this->db->set('status', 'good');
  //     //tambahan
  //     $this->db->insert('tb_stock_in_stores_reports');
  //   }

  //   // CLOSE CURRENT PERIOD, SET TO NEXT PERIOD
  //   if ($current_month == 12){
  //     $next_month = 1;
  //     $next_year  = $current_year + 1;
  //   } else {
  //     $next_month = $current_month + 1;
  //     $next_year  = $current_year;
  //   }

  //   //update tb_setting
  //   $this->db->set('setting_value', $next_year);
  //   $this->db->where('setting_name', 'ACTIVE_YEAR');
  //   $this->db->update('tb_settings');

  //   $this->db->set('setting_value', $next_month);
  //   $this->db->where('setting_name', 'ACTIVE_MONTH');
  //   $this->db->update('tb_settings');


  //   //update tb_last_opname
  //   $this->db->set('status', 'opnamed');
  //   // $this->db->set('end_date', $end_date);
  //   $this->db->where('status', 'last_opname');
  //   $this->db->update('tb_last_opname');

  //   $this->db->set('start_date', $start_date);
  //   $this->db->set('end_date', $end_date);
  //   $this->db->set('status', 'last_opname');
  //   $this->db->set('condition', 'good');
  //   $this->db->set('created_at', date('Y-m-d'));
  //   $this->db->insert('tb_last_opname');

  //   if ($this->db->trans_status() === FALSE)
  //     return FALSE;

  //   $this->db->trans_commit();
  //   return TRUE;
  // }

  // public function opname_stock($start_date,$end_date)
  // {
  //   $this->db->trans_begin();

  //   // get current period
  //   $current_year   = intval(config_item('period_year'));
  //   $current_month  = intval(config_item('period_month'));

  //   // get previous period
  //   if ($current_month === 1){
  //     $previous_month = 12;
  //     $previous_year  = $current_year - 1;
  //   } else {
  //     $previous_month = $current_month - 1;
  //     $previous_year  = $current_year;
  //   }

  //   // CREATE NEW STOCK REPORT
  //   // $this->db->select('id');
  //   // $this->db->where_in('tb_master_items.group', config_item('auth_inventory'));
  //   // $this->db->from('tb_master_items');
  //   // $query            = $this->db->get();
  //   // $items            = $query->result_array();

  //   // foreach ($items as $item){
  //   //   //SERVICEABLE
  //   //   $this->db->select('id');
  //   //   $this->db->where('item_id',$item['id']);
  //   //   $THIS->DB->where('SERVICEABLE');
  //   //   $this->db->from('tb_stocks');
  //   //   $query            = $this->db->get();
  //   //   $stocks           = $query->result_array();
  //   //   foreach ($stocks as $stock) {
  //   //     $this->db->select('id');
  //   //     $this->db->where('stock_id',$stock['id']);
  //   //     $this->db->where('tb_stock_in_stores.received_date <=', $end_date);
  //   //     $this->db->from('tb_stock_in_stores');
  //   //     $query            = $this->db->get();
  //   //     $stock_in_stores  = $query->result_array();
  //   //     foreach ($stock_in_stores as $stock_detail) {
          
  //   //     }

  //   //   }

  //   // }
  //   // get all stocks by inventory
  //   $this->db->select('tb_stock_in_stores.*, tb_stocks.item_id, tb_stocks.condition');
  //   $this->db->from('tb_stock_in_stores');
  //   $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
  //   $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
  //   $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
  //   $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));
  //   $this->db->where('tb_stock_in_stores.received_date <=', $end_date);
  //   // $this->db->where('tb_stock_in_stores.received_date <=', $end_date);

  //   $query            = $this->db->get();
  //   $stock_in_stores  = $query->result_array();

  //   foreach ($stock_in_stores as $stock_detail) {
  //     $stock_in_stores_id         = intval($stock_detail['id']);
  //     $item_id                    = intval($stock_detail['item_id']);
  //     $condition                  = $stock_detail['condition'];
  //     $warehouse                  = $stock_detail['warehouse'];
  //     $stores                     = $stock_detail['stores'];
  //     $reference_document         = $stock_detail['reference_document'];
  //     $expired_date               = $stock_detail['expired_date'];
  //     $received_date              = $stock_detail['received_date'];
  //     $received_by                = $stock_detail['received_by'];
  //     $remarks                    = $stock_detail['remarks'];
  //     // $initial_quantity           = floatval($stock_detail['initial_quantity']);
  //     // $initial_unit_value         = floatval($stock_detail['initial_unit_value']);
  //     // $current_quantity           = floatval($stock_detail['quantity']);
  //     // $current_unit_value         = floatval($stock_detail['unit_value']);
  //     // $current_total_value        = $current_quantity * $current_unit_value;
  //     // $current_average_value      = ($current_quantity == 0) ? floatval(0) : $current_total_value/$current_quantity;

  //     // set previous stock
  //     // $this->db->set('previous_quantity', $current_quantity);
  //     // $this->db->where('id', $stock_in_stores_id);
  //     // $this->db->update('tb_stock_in_stores');

  //     // get current period total stock adjustment
  //     $this->db->select_sum('tb_stock_adjustments.adjustment_quantity', 'total_adjustment_quantity');
  //     $this->db->select('tb_stock_adjustments.stock_in_stores_id');
  //     $this->db->from('tb_stock_adjustments');
  //     $this->db->where('tb_stock_adjustments.stock_in_stores_id', $stock_in_stores_id);
  //     $this->db->where('tb_stock_adjustments.date_of_entry >=', $start_date);
  //     $this->db->where('tb_stock_adjustments.date_of_entry <=', $end_date);
  //     $this->db->where('tb_stock_adjustments.updated_status !=', 'PENDING');
  //     // $this->db->where('tb_stock_adjustments.period_year', $current_year);
  //     // $this->db->where('tb_stock_adjustments.period_month', $current_month);
  //     $this->db->group_by('tb_stock_adjustments.stock_in_stores_id');

  //     $query  = $this->db->get();

  //     if ($query->num_rows() > 0){
  //       $adjustment = $query->unbuffered_row('array');
  //       $total_adjustment_quantity = $adjustment['total_adjustment_quantity'];
  //     } else {
  //       $total_adjustment_quantity = floatval(0);
  //     }

  //     // get current period total stock received
  //     $this->db->select_sum('tb_receipt_items.received_quantity', 'total_received_quantity');
  //     $this->db->select_sum('tb_receipt_items.received_total_value', 'total_received_total_value');
  //     $this->db->select('tb_receipt_items.purchase_order_number');
  //     $this->db->select('tb_receipt_items.stock_in_stores_id');
  //     $this->db->from('tb_receipts');
  //     $this->db->join('tb_receipt_items', 'tb_receipt_items.document_number = tb_receipts.document_number');
  //     $this->db->where('tb_receipt_items.stock_in_stores_id', $stock_in_stores_id);
  //     $this->db->where('tb_receipts.received_date >=', $start_date);
  //     $this->db->where('tb_receipts.received_date <=', $end_date);
  //     // $this->db->where('EXTRACT(MONTH FROM tb_receipts.received_date)::integer = ', $current_month);
  //     // $this->db->where('EXTRACT(YEAR FROM tb_receipts.received_date)::integer = ', $current_year);
  //     $this->db->group_by('tb_receipt_items.stock_in_stores_id');
  //     $this->db->group_by('tb_receipt_items.purchase_order_number');

  //     $query  = $this->db->get();

  //     if ($query->num_rows() > 0){
  //       $received = $query->unbuffered_row('array');
  //       $total_received_quantity      = floatval($received['total_received_quantity']);
  //       $total_received_total_value   = floatval($received['total_received_total_value']);
  //       //tambahan
  //       $purchase_order_number = $received['purchase_order_number'];

  //       if ($total_received_quantity == 0){
  //         $total_received_average_value = floatval(0);
  //       } else {
  //         $total_received_average_value = $total_received_total_value/$total_received_quantity;
  //       }
  //     } else {
  //       $total_received_quantity      = floatval(0);
  //       $total_received_total_value   = floatval(0);
  //       $total_received_average_value = floatval(0);
  //       //tambahan
  //       $purchase_order_number = $received['purchase_order_number'];

  //     }

  //     // get current period total stock issued
  //     $this->db->select_sum('tb_issuance_items.issued_quantity', 'total_issued_quantity');
  //     $this->db->select_sum('tb_issuance_items.issued_total_value', 'total_issued_total_value');
  //     $this->db->select('tb_issuance_items.stock_in_stores_id');
  //     $this->db->from('tb_issuances');
  //     $this->db->join('tb_issuance_items', 'tb_issuance_items.document_number = tb_issuances.document_number');
  //     $this->db->where('tb_issuance_items.stock_in_stores_id', $stock_in_stores_id);
  //     $this->db->where('tb_issuances.issued_date >=', $start_date);
  //     $this->db->where('tb_issuances.issued_date <=', $end_date);
  //     $this->db->where('tb_issuances.issued_to !=', 'MIX');
  //     // $this->db->where('EXTRACT(MONTH FROM tb_issuances.issued_date)::integer = ', $current_month);
  //     // $this->db->where('EXTRACT(YEAR FROM tb_issuances.issued_date)::integer = ', $current_year);
  //     $this->db->group_by('tb_issuance_items.stock_in_stores_id');

  //     $query  = $this->db->get();

  //     if ($query->num_rows() > 0){
  //       $issued = $query->unbuffered_row('array');
  //       $total_issued_quantity        = floatval($issued['total_issued_quantity']);
  //       $total_issued_total_value     = floatval($issued['total_issued_total_value']);

  //       if ($total_issued_quantity == 0){
  //         $total_issued_average_value = floatval(0);
  //       } else {
  //         $total_issued_average_value = $total_issued_total_value/$total_issued_quantity;
  //       }
  //     } else {
  //       $total_issued_quantity      = floatval(0);
  //       $total_issued_total_value   = floatval(0);
  //       $total_issued_average_value = floatval(0);
  //     }

  //     // get quantity and value from previous stock opnames
  //     $this->db->from('tb_stock_in_stores_reports');
  //     $this->db->where('period_year', $previous_year);
  //     $this->db->where('period_month', $previous_month);
  //     $this->db->where('item_id', $item_id);
  //     $this->db->where('condition', $condition);
  //     $this->db->where('warehouse', $warehouse);
  //     $this->db->where('stores', $stores);

  //     $query  = $this->db->get();
  //     $initial_quantity           = floatval($stock_detail['initial_quantity']);
  //     $initial_unit_value         = floatval($stock_detail['initial_unit_value']);
      

  //     if ($query->num_rows() > 0){
  //       // $initial_quantity = $current_quantity - (($total_received_quantity - $total_issued_quantity) + ($total_adjustment_quantity));

  //       // $previous_quantity      = $initial_quantity;

  //       $previous_stock_in_stores   = $query->unbuffered_row('array');
  //       $initial_quantity           = $current_quantity - (($total_received_quantity - $total_issued_quantity) + ($total_adjustment_quantity));

  //       $previous_quantity          = floatval($previous_stock_in_stores['current_quantity']);
  //       // $previous_quantity          = floatval($previous_stock_in_stores['current_quantity']);
  //       $previous_unit_value        = floatval($previous_stock_in_stores['current_unit_value']);
  //       $previous_total_value       = floatval($previous_stock_in_stores['current_total_value']);
  //       $previous_average_value     = floatval($previous_stock_in_stores['current_average_value']);
  //     } else {
  //       $initial_quantity = $current_quantity - (($total_received_quantity - $total_issued_quantity) + ($total_adjustment_quantity));

  //       $previous_quantity      = 0;
  //       $previous_unit_value    = $initial_unit_value;
  //       $previous_total_value   = $initial_quantity * $initial_unit_value;

  //       if ($previous_quantity == 0){
  //         $previous_average_value = floatval(0);
  //       } else {
  //         $previous_average_value = $previous_total_value/$previous_quantity;
  //       }
  //     }

  //     // $current_quantity           = floatval($stock_detail['quantity']);
  //     $current_quantity           = $previous_quantity + (($total_received_quantity - $total_issued_quantity) + ($total_adjustment_quantity));
  //     $current_unit_value         = floatval($stock_detail['unit_value']);
  //     $current_total_value        = $current_quantity * $current_unit_value;
  //     $current_average_value      = ($current_quantity == 0) ? floatval(0) : $current_total_value/$current_quantity;

  //     // STOCK REPORTS
  //     $this->db->set('period_year', $current_year);
  //     $this->db->set('period_month', $current_month);
  //     $this->db->set('item_id', $item_id);
  //     $this->db->set('condition', $condition);
  //     $this->db->set('warehouse', $warehouse);
  //     $this->db->set('stores', $stores);
  //     $this->db->set('previous_quantity', $previous_quantity);
  //     $this->db->set('previous_unit_value', $previous_unit_value);
  //     $this->db->set('previous_total_value', $previous_total_value);
  //     $this->db->set('previous_average_value', $previous_average_value);
  //     $this->db->set('current_quantity', $current_quantity);
  //     $this->db->set('current_unit_value', $current_unit_value);
  //     $this->db->set('current_total_value', $current_total_value);
  //     $this->db->set('current_average_value', $current_average_value);
  //     $this->db->set('total_received_quantity', $total_received_quantity);
  //     $this->db->set('total_received_total_value', $total_received_total_value);
  //     $this->db->set('total_received_average_value', $total_received_average_value);
  //     $this->db->set('total_issued_quantity', $total_issued_quantity);
  //     $this->db->set('total_issued_total_value', $total_issued_total_value);
  //     $this->db->set('total_issued_average_value', $total_issued_average_value);
  //     $this->db->set('total_adjustment_quantity', $total_adjustment_quantity);
  //     $this->db->set('reference_document', $reference_document);
  //     $this->db->set('expired_date', $expired_date);
  //     $this->db->set('received_date', $received_date);
  //     $this->db->set('received_by', $received_by);
  //     $this->db->set('remarks', $remarks);
  //     $this->db->set('date_opname_start', $start_date);
  //     $this->db->set('date_opname_end', $end_date);
  //     $this->db->set('created_by', config_item('auth_person_name'));
  //     //tambahan
  //     $this->db->set('purchase_order_number', $purchase_order_number);
  //     $this->db->set('status', 'good');
  //     //tambahan
  //     $this->db->insert('tb_stock_in_stores_reports');
  //   }

  //   // CLOSE CURRENT PERIOD, SET TO NEXT PERIOD
  //   if ($current_month == 12){
  //     $next_month = 1;
  //     $next_year  = $current_year + 1;
  //   } else {
  //     $next_month = $current_month + 1;
  //     $next_year  = $current_year;
  //   }

  //   //update tb_setting
  //   $this->db->set('setting_value', $next_year);
  //   $this->db->where('setting_name', 'ACTIVE_YEAR');
  //   $this->db->update('tb_settings');

  //   $this->db->set('setting_value', $next_month);
  //   $this->db->where('setting_name', 'ACTIVE_MONTH');
  //   $this->db->update('tb_settings');


  //   //update tb_last_opname
  //   $this->db->set('status', 'opnamed');
  //   // $this->db->set('end_date', $end_date);
  //   $this->db->where('status', 'last_opname');
  //   $this->db->update('tb_last_opname');

  //   $this->db->set('start_date', $start_date);
  //   $this->db->set('end_date', $end_date);
  //   $this->db->set('status', 'last_opname');
  //   $this->db->set('condition', 'good');
  //   $this->db->set('created_at', date('Y-m-d'));
  //   $this->db->insert('tb_last_opname');

  //   if ($this->db->trans_status() === FALSE)
  //     return FALSE;

  //   $this->db->trans_commit();
  //   return TRUE;
  // }
  //stock mutasi
  public function opname_stock($start_date,$end_date)
  {
    $this->db->trans_begin();

    // get current period
    $current_year   = intval(config_item('period_year'));
    $current_month  = intval(config_item('period_month'));

    // get previous period
    if ($current_month === 1){
      $previous_month = 12;
      $previous_year  = $current_year - 1;
    } else {
      $previous_month = $current_month - 1;
      $previous_year  = $current_year;
    }

    // $this->db->select('tb_stocks.item_id,tb_stocks.condition,tb_stocks.id,tb_stock_in_stores.stores,tb_stock_in_stores.warehouse');
    // $this->db->select('tb_stocks.item_id,tb_stocks.condition,tb_stocks.id');
    // $this->db->from('tb_stock_in_stores');
    // $this->db->join('tb_stocks', 'tb_stock_in_stores.stock_id = tb_stocks.id');
    // // $this->db->where('tb_stock_in_stores.received_date <=', $end_date);
    // $this->db->group_by('tb_stocks.id,tb_stock_in_stores.stores,tb_stock_in_stores.warehouse');
    $this->db->select('tb_stocks.id, tb_stocks.item_id, tb_stocks.condition, tb_stock_cards.stores, tb_master_stores.warehouse');
    $this->db->from('tb_stock_cards');
    $this->db->join('tb_stocks', 'tb_stock_cards.stock_id = tb_stocks.id');
    $this->db->join('tb_master_stores', 'tb_stock_cards.stores = tb_master_stores.stores');
    $this->db->group_by('tb_stocks.id, tb_stock_cards.stores, tb_master_stores.warehouse');
    $this->db->order_by('tb_stocks.id');
    $query_tb_stocks  = $this->db->get();
    $tb_stocks        = $query_tb_stocks->result_array();
    foreach ($tb_stocks as $stocks) {
      // $this->db->select('tb_master_stores.stores,tb_master_stores.warehouse');
      // $this->db->from('tb_master_stores');
      // $query_tb_stores  = $this->db->get();
      // $tb_stores        = $query_tb_stores->result_array();

      // foreach ($tb_stores as $data) {

        
      // }
        // get quantity and value from previous stock opnames
        $this->db->from('tb_stock_in_stores_reports');
        $this->db->where('period_year', $previous_year);
        $this->db->where('period_month', $previous_month);
        $this->db->where('item_id', $stocks['item_id']);
        $this->db->where('condition', $stocks['condition']);
        $this->db->where('warehouse', $stocks['warehouse']);
        $this->db->where('stores', $stocks['stores']);
        $query  = $this->db->get();
        // $initial_quantity           = floatval($stock_detail['initial_quantity']);
        // $initial_unit_value         = floatval($stock_detail['initial_unit_value']);
        

        if ($query->num_rows() > 0){
          // $initial_quantity = $current_quantity - (($total_received_quantity - $total_issued_quantity) + ($total_adjustment_quantity));

          // $previous_quantity      = $initial_quantity;

          $previous_stock_in_stores   = $query->unbuffered_row('array');
          $initial_quantity           = $current_quantity - (($total_received_quantity - $total_issued_quantity) + ($total_adjustment_quantity));

          $previous_quantity          = floatval($previous_stock_in_stores['current_quantity']);
          // $previous_quantity          = floatval($previous_stock_in_stores['current_quantity']);
          $previous_unit_value        = floatval($previous_stock_in_stores['current_unit_value']);
          $previous_total_value       = floatval($previous_stock_in_stores['current_total_value']);
          $previous_average_value     = floatval($previous_stock_in_stores['current_average_value']);
        } else {
          // $initial_quantity = $current_quantity - (($total_received_quantity - $total_issued_quantity) + ($total_adjustment_quantity));

          $previous_quantity      = 0;
          $previous_unit_value    = 0;
          $previous_total_value   = 0;

          if ($previous_quantity == 0){
            $previous_average_value = floatval(0);
          } else {
            $previous_average_value = $previous_total_value/$previous_quantity;
          }
        }

        //cari current qty
        $this->db->select('SUM(quantity) as qty');
        $this->db->select('SUM(total_value) as total_value');
        $this->db->from('tb_stock_cards');
        $this->db->where('tb_stock_cards.stock_id',$stocks['id']);
        $this->db->where('tb_stock_cards.stores',$stocks['stores']);
        $this->db->where('tb_stock_cards.date_of_entry <=',$end_date);
        $query_stock_card  = $this->db->get();

        if ($query_stock_card->num_rows() > 0){
          $current_stock              = $query_stock_card->unbuffered_row('array');
          
          $current_quantity           = floatval($current_stock['qty']);
          $current_total_value        = floatval($current_stock['total_value']);
          $current_average_value      = ($current_quantity == 0) ? floatval(0) : $current_total_value/$current_quantity;
          $current_unit_value         = floatval($current_average_value);       
          
        }else{
          $current_quantity           = 0;
          $current_total_value        = 0;
          $current_average_value      = 0;
          $current_unit_value         = 0; 
        }
        
        // cari received stock
        $doc_received = array(4,5,8,9,7);
        $this->db->select('SUM(quantity) as qty');
        $this->db->select('SUM(total_value) as total_value');
        $this->db->from('tb_stock_cards');
        $this->db->where('tb_stock_cards.stock_id',$stocks['id']);
        $this->db->where('tb_stock_cards.stores',$stocks['stores']);
        $this->db->where('tb_stock_cards.date_of_entry >=',$start_date);
        $this->db->where('tb_stock_cards.date_of_entry <=',$end_date);
        $this->db->where_in('tb_stock_cards.doc_type',$doc_received);
        $query_doc_received  = $this->db->get();

        if ($query_doc_received->num_rows() > 0){
          $received_stock              = $query_doc_received->unbuffered_row('array');
          
          $received_quantity           = floatval($received_stock['qty']);
          $received_total_value        = floatval($received_stock['total_value']);
          $received_average_value      = ($received_quantity == 0) ? floatval(0) : $received_total_value/$received_quantity;
          $received_unit_value         = floatval($received_average_value);       
          
        }else{
          $received_quantity           = 0;
          $received_total_value        = 0;
          $received_average_value      = 0;
          $received_unit_value         = 0; 
        }

        // cari issued stock
        $doc_issued = array(3,6,9,10);
        $this->db->select('SUM(quantity) as qty');
        $this->db->select('SUM(total_value) as total_value');
        $this->db->from('tb_stock_cards');
        $this->db->where('tb_stock_cards.stock_id',$stocks['id']);
        $this->db->where('tb_stock_cards.stores',$stocks['stores']);
        $this->db->where('tb_stock_cards.date_of_entry >=',$start_date);
        $this->db->where('tb_stock_cards.date_of_entry <=',$end_date);
        $this->db->where_in('tb_stock_cards.doc_type',$doc_issued);
        $query_doc_issued  = $this->db->get();

        if ($query_doc_issued->num_rows() > 0){
          $issued_stock              = $query_doc_issued->unbuffered_row('array');
          
          $issued_quantity           = floatval($issued_stock['qty']);
          $issued_total_value        = floatval($issued_stock['total_value']);
          $issued_average_value      = ($issued_quantity == 0) ? floatval(0) : $issued_total_value/$issued_quantity;
          $issued_unit_value         = floatval($issued_average_value);       
          
        }else{
          $issued_quantity           = 0;
          $issued_total_value        = 0;
          $issued_average_value      = 0;
          $issued_unit_value         = 0; 
        }

        // cari adjustment stock
        $doc_adjustment = array(1,2);
        $this->db->select('SUM(quantity) as qty');
        $this->db->select('SUM(total_value) as total_value');
        $this->db->from('tb_stock_cards');
        $this->db->where('tb_stock_cards.stock_id',$stocks['id']);
        $this->db->where('tb_stock_cards.stores',$stocks['stores']);
        $this->db->where('tb_stock_cards.date_of_entry >=',$start_date);
        $this->db->where('tb_stock_cards.date_of_entry <=',$end_date);
        $this->db->where_in('tb_stock_cards.doc_type',$doc_adjustment);
        $query_doc_adjustment  = $this->db->get();

        if ($query_doc_adjustment->num_rows() > 0){
          $adjustment_stock              = $query_doc_adjustment->unbuffered_row('array');
          
          $adjustment_quantity           = floatval($adjustment_stock['qty']);
          $adjustment_total_value        = floatval($adjustment_stock['total_value']);
          $adjustment_average_value      = ($adjustment_quantity == 0) ? floatval(0) : $adjustment_total_value/$adjustment_quantity;
          $adjustment_unit_value         = floatval($adjustment_average_value);       
          
        }else{
          $adjustment_quantity           = 0;
          $adjustment_total_value        = 0;
          $adjustment_average_value      = 0;
          $adjustment_unit_value         = 0; 
        }

        $this->db->set('period_year', $current_year);
        $this->db->set('period_month', $current_month);
        $this->db->set('item_id', $stocks['item_id']);
        $this->db->set('condition', $stocks['condition']);
        $this->db->set('warehouse', $stocks['warehouse']);
        $this->db->set('stores', $stocks['stores']);
        $this->db->set('previous_quantity', $previous_quantity);
        $this->db->set('previous_unit_value', $previous_unit_value);
        $this->db->set('previous_total_value', $previous_total_value);
        $this->db->set('previous_average_value', $previous_average_value);
        $this->db->set('current_quantity', $current_quantity);
        $this->db->set('current_unit_value', $current_unit_value);
        $this->db->set('current_total_value', $current_total_value);
        $this->db->set('current_average_value', $current_average_value);
        $this->db->set('total_received_quantity', $received_quantity);
        $this->db->set('total_received_total_value', $received_total_value);
        $this->db->set('total_received_average_value', $received_average_value);
        $this->db->set('total_issued_quantity', $issued_quantity);
        $this->db->set('total_issued_total_value', $issued_total_value);
        $this->db->set('total_issued_average_value', $issued_average_value);
        $this->db->set('total_adjustment_quantity', $adjustment_quantity);
        $this->db->set('total_adjustment_total_value', $adjustment_total_value);
        $this->db->set('total_adjustment_average_value', $adjustment_average_value);
        // $this->db->set('reference_document', 0);
        // $this->db->set('expired_date', $expired_date);
        // $this->db->set('received_date', $received_date);
        // $this->db->set('received_by', $received_by);
        // $this->db->set('remarks', $remarks);
        $this->db->set('date_opname_start', $start_date);
        $this->db->set('date_opname_end', $end_date);
        $this->db->set('created_by', config_item('auth_person_name'));
        //tambahan
        // $this->db->set('purchase_order_number', $purchase_order_number);
        $this->db->set('status', 'good');
        //tambahan
        $this->db->insert('tb_stock_in_stores_reports');
      
    }

    if ($current_month == 12){
      $next_month = 1;
      $next_year  = $current_year + 1;
    } else {
      $next_month = $current_month + 1;
      $next_year  = $current_year;
    }

    //update tb_setting
    $this->db->set('setting_value', $next_year);
    $this->db->where('setting_name', 'ACTIVE_YEAR');
    $this->db->update('tb_settings');

    $this->db->set('setting_value', $next_month);
    $this->db->where('setting_name', 'ACTIVE_MONTH');
    $this->db->update('tb_settings');   


    //update tb_last_opname
    $this->db->set('status', 'opnamed');
    // $this->db->set('end_date', $end_date);
    $this->db->where('status', 'last_opname');
    $this->db->update('tb_last_opname');

    $this->db->set('start_date', $start_date);
    $this->db->set('end_date', $end_date);
    $this->db->set('status', 'last_opname');
    $this->db->set('condition', 'good');
    $this->db->insert('tb_last_opname');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function cancel_opname_stock($start_date,$end_date)
  {
    $this->db->trans_begin();    

    // $this->db->set('status', 'good');
    // $this->db->where('status', 'cancel');
    // $this->db->update('tb_stock_in_stores_reports');

    $this->db->set('status', 'cancel');
    $this->db->where('date_opname_start', $start_date);
    $this->db->where('date_opname_end', $end_date);
    $this->db->update('tb_stock_in_stores_reports');

    $this->db->order_by('id',"desc")
        ->limit(1)
        ->where('condition','good')
        ->where('status','opnamed')
        ->from('tb_last_opname');

    $query  = $this->db->get();
      $row    = $query->unbuffered_row();
      $id     = $row->id;

      // ubah tanggal stock opname sebelumnya menjadi last opname
      $this->db->set('status','last_opname');
      $this->db->where('id',$id);
      $this->db->update('tb_last_opname'); 

      //mengubah kondisi tanggal stok opname yang dicancel
      $this->db->set('condition','cancel');
      $this->db->set('status','opnamed');
      $this->db->where('start_date',$start_date);
      $this->db->where('end_date',$end_date);
      $this->db->update('tb_last_opname');

      
    

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function getIndexOpnameSelectedColumns()
  {
    $return = array(
      'tb_stock_cards.stock_id'                     => NULL,
      'tb_master_items.id as item_id'               => 'Item Id',
      'tb_master_items.part_number'                 => 'Part Number',
      'tb_master_items.description'                 => 'Description',
      'tb_master_items.serial_number'               => 'Serial Number',
      'tb_master_items.kode_stok'                   => 'Stock Code',
      'tb_master_item_groups.coa'                   => 'COA',
      'tb_master_items.group'                       => 'Group',
      'tb_master_item_categories.category'          => 'Category',
      'tb_stocks.condition'                         => 'Condition',
      'SUM(tb_stock_cards.quantity) as qty'         => 'Quantity',
      'tb_master_items.unit'                        => 'Unit',
      'tb_master_items.minimum_quantity'            => 'Min. Qty',
      'tb_stock_cards.stores'                       => 'Stores',
      'tb_master_stores.warehouse'                  => 'Base',
      );

      if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'SUPER ADMIN'){
        // $return['tb_stock_cards.unit_value']                  = 'Price';
        $return['SUM(tb_stock_cards.total_value) as total_value']         = 'Total Price';
      }
      
      //'tb_stock_in_stores_reports.received_date' => 'Received Date',     
      
    return $return;
  }

  public function getIndexOpnameGroupedColumns()
  {
    $retur =  array(
      'tb_stock_cards.stock_id',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.kode_stok',
      'tb_master_items.group',
      'tb_master_item_categories.category',
      'tb_stocks.condition',
      // 'tb_stock_cards.quantity',
      // 'tb_stock_cards.unit_value',
      'tb_master_items.minimum_quantity',
      'tb_master_item_groups.coa',
      'tb_master_items.serial_number',
      'tb_stock_cards.stores',
      'tb_master_items.unit',
      'tb_master_stores.warehouse',
      'tb_master_items.id'
    );
    if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'SUPER ADMIN'){
        $retur['tb_stock_cards.unit_value'];
    }

    return $retur;
  }

  public function getIndexOpnameOrderableColumns()
  {
    return array(
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.kode_stok',
      'tb_master_items.group',
      'tb_master_item_categories.category',
      'tb_stocks.condition',
      // 'tb_stock_cards.quantity',
      // 'tb_stock_cards.unit_value',
      'tb_master_items.minimum_quantity',
      'tb_master_item_groups.coa',
      'tb_master_items.serial_number',
      'tb_stock_cards.stores',
      'tb_master_items.unit',
      'tb_master_stores.warehouse',
    );
  }

  public function getIndexOpnameSearchableColumns()
  {
    return array(
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.kode_stok',
      'tb_master_items.group',
      'tb_master_item_categories.category',
      'tb_stocks.condition',
      // 'tb_stock_cards.quantity',
      // 'tb_stock_cards.unit_value',
      // 'tb_master_items.minimum_quantity',
      'tb_master_item_groups.coa',
      'tb_master_items.serial_number',
      'tb_stock_cards.stores',
      'tb_master_items.unit',
      'tb_stock_cards.warehouse',
    );
  }

  private function searchIndexOpname()
  {
    
    $now=date('Y-m-d');
    

    $i = 0;
    foreach ($this->getIndexOpnameSearchableColumns() as $item){
      
      if ($_POST['search']['value']){
        $term = strtoupper($_POST['search']['value']);

        if ($i === 0){
          $this->db->group_start();
          $this->db->like('UPPER('.$item.')', $term);
        } else {
          $this->db->or_like('UPPER('.$item.')', $term);
        }

        if (count($this->getIndexOpnameSearchableColumns()) - 1 == $i)
          $this->db->group_end();
      }

      $i++;
    }
  }

  public function getIndexOpname($condition = 'SERVICEABLE', $warehouse= NULL, $start_date = NULL, $end_date = NULL, $category = NULL,  $return = 'array')
  {
    
    $this->db->select(array_keys($this->getIndexOpnameSelectedColumns()));
    // $this->db->select('tb_stock_in_stores_reports.received_date');
    $this->db->from('tb_stock_cards');
    $this->db->join('tb_stocks', 'tb_stock_cards.stock_id = tb_stocks.id');
     $this->db->join('tb_master_stores', 'tb_master_stores.stores = tb_stock_cards.stores');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->join('tb_master_item_categories', 'tb_master_item_categories.category = tb_master_item_groups.category');
    // $this->db->where('quantity != ', 0);
    $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));
    $this->db->where('tb_stocks.condition', $condition);
    

    if ($category !== NULL){
      $this->db->where('tb_master_item_categories.category', $category);
    } else {
      $this->db->where_in('tb_master_item_categories.category', config_item('auth_inventory'));
    }

    if ($start_date && $end_date !== NULL){
      // $this->db->where('tb_stock_in_stores.received_date >= ', $start_date);
      $this->db->where('tb_stock_cards.date_of_entry <= ', $end_date);
    }

    if ($warehouse !== NULL){
      if($warehouse == 'WISNU'){
        $this->db->group_start()
                  ->like('tb_stock_cards.warehouse', 'WISNU')
                  // ->or_where('tb_stock_in_stores_reports.warehouse=', 'WISNU REKONDISI')
                  ->group_end();
      }
      if($warehouse == "all base rekondisi"){
        $this->db->group_start()
                  ->like('tb_stock_cards.warehouse', 'REKONDISI')
                  ->group_end();
      }
      if($warehouse == 'LOMBOK'){
        $this->db->where('tb_stock_cards.warehouse', $warehouse);
      }
       if($warehouse == 'JEMBER'){
        $this->db->where('tb_stock_cards.warehouse', $warehouse);
      }
       if($warehouse == 'SOLO'){
        $this->db->where('tb_stock_cards.warehouse', $warehouse);
      }
       if($warehouse == 'PALANGKARAYA'){
        $this->db->where('tb_stock_cards.warehouse', $warehouse);
      }
       if($warehouse == 'BSR REKONDISI'){
        $this->db->where('tb_stock_cards.warehouse', $warehouse);
      }
       if($warehouse == 'BANYUWANGI'){
        $this->db->where('tb_stock_cards.warehouse', $warehouse);
      }
      if($warehouse == 'WISNU REKONDISI'){
        $this->db->where('tb_stock_cards.warehouse', $warehouse);
      }      
    }
    $this->db->group_by($this->getIndexOpnameGroupedColumns());

    $this->searchIndex();

    $orderableColumns = $this->getIndexOpnameOrderableColumns();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($orderableColumns[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('tb_master_items.id', 'asc');
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

  public function countIndexOpnameFiltered($condition = 'SERVICEABLE', $warehouse= NULL, $start_date = NULL, $end_date = NULL, $category = NULL)
  {
    $this->db->select(array_keys($this->getIndexOpnameSelectedColumns()));
    // $this->db->select('tb_stock_in_stores_reports.received_date');
    $this->db->from('tb_stock_cards');
    $this->db->join('tb_stocks', 'tb_stock_cards.stock_id = tb_stocks.id');
     $this->db->join('tb_master_stores', 'tb_master_stores.stores = tb_stock_cards.stores');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->join('tb_master_item_categories', 'tb_master_item_categories.category = tb_master_item_groups.category');
    $this->db->where('quantity != ', 0);
    $this->db->where('tb_stocks.condition', $condition);
    $this->db->group_by($this->getIndexOpnameGroupedColumns());
    if ($category !== NULL){
      $this->db->where('tb_master_item_categories.category', $category);
    } else {
      $this->db->where_in('tb_master_item_categories.category', config_item('auth_inventory'));
    }

    if ($start_date && $end_date !== NULL){
      // $this->db->where('tb_stock_in_stores.received_date >= ', $start_date);
      $this->db->where('tb_stock_cards.date_of_entry <= ', $end_date);
    }

    if ($warehouse !== NULL){
      if($warehouse == 'WISNU'){
        $this->db->group_start()
                  ->like('tb_stock_cards.warehouse', 'WISNU')
                  // ->or_where('tb_stock_in_stores_reports.warehouse=', 'WISNU REKONDISI')
                  ->group_end();
      }
      if($warehouse == "all base rekondisi"){
        $this->db->group_start()
                  ->like('tb_stock_cards.warehouse', 'REKONDISI')
                  ->group_end();
      }
      if($warehouse == 'LOMBOK'){
        $this->db->where('tb_stock_cards.warehouse', $warehouse);
      }
       if($warehouse == 'JEMBER'){
        $this->db->where('tb_stock_cards.warehouse', $warehouse);
      }
       if($warehouse == 'SOLO'){
        $this->db->where('tb_stock_cards.warehouse', $warehouse);
      }
       if($warehouse == 'PALANGKARAYA'){
        $this->db->where('tb_stock_cards.warehouse', $warehouse);
      }
       if($warehouse == 'BSR REKONDISI'){
        $this->db->where('tb_stock_cards.warehouse', $warehouse);
      }
       if($warehouse == 'BANYUWANGI'){
        $this->db->where('tb_stock_cards.warehouse', $warehouse);
      }
      if($warehouse == 'WISNU REKONDISI'){
        $this->db->where('tb_stock_cards.warehouse', $warehouse);
      }      
    }
   

    $this->searchIndexOpname();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndexOpname($condition = 'SERVICEABLE', $warehouse= NULL, $start_date = NULL, $end_date = NULL, $category = NULL)
  {
    $this->db->select(array_keys($this->getIndexOpnameSelectedColumns()));
    // $this->db->select('tb_stock_in_stores_reports.received_date');
    $this->db->from('tb_stock_cards');
    $this->db->join('tb_stocks', 'tb_stock_cards.stock_id = tb_stocks.id');
     $this->db->join('tb_master_stores', 'tb_master_stores.stores = tb_stock_cards.stores');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->join('tb_master_item_categories', 'tb_master_item_categories.category = tb_master_item_groups.category');
    $this->db->where('quantity != ', 0);
    $this->db->where('tb_stocks.condition', $condition);
    $this->db->group_by($this->getIndexOpnameGroupedColumns());
    if ($category !== NULL){
      $this->db->where('tb_master_item_categories.category', $category);
    } else {
      $this->db->where_in('tb_master_item_categories.category', config_item('auth_inventory'));
    }

    if ($start_date && $end_date !== NULL){
      // $this->db->where('tb_stock_in_stores.received_date >= ', $start_date);
      $this->db->where('tb_stock_cards.date_of_entry <= ', $end_date);
    }

    if ($warehouse !== NULL){
      if($warehouse == 'WISNU'){
        $this->db->group_start()
                  ->like('tb_stock_cards.warehouse', 'WISNU')
                  // ->or_where('tb_stock_in_stores_reports.warehouse=', 'WISNU REKONDISI')
                  ->group_end();
      }
      if($warehouse == "all base rekondisi"){
        $this->db->group_start()
                  ->like('tb_stock_cards.warehouse', 'REKONDISI')
                  ->group_end();
      }
      if($warehouse == 'LOMBOK'){
        $this->db->where('tb_stock_cards.warehouse', $warehouse);
      }
       if($warehouse == 'JEMBER'){
        $this->db->where('tb_stock_cards.warehouse', $warehouse);
      }
       if($warehouse == 'SOLO'){
        $this->db->where('tb_stock_cards.warehouse', $warehouse);
      }
       if($warehouse == 'PALANGKARAYA'){
        $this->db->where('tb_stock_cards.warehouse', $warehouse);
      }
       if($warehouse == 'BSR REKONDISI'){
        $this->db->where('tb_stock_cards.warehouse', $warehouse);
      }
       if($warehouse == 'BANYUWANGI'){
        $this->db->where('tb_stock_cards.warehouse', $warehouse);
      }
      if($warehouse == 'WISNU REKONDISI'){
        $this->db->where('tb_stock_cards.warehouse', $warehouse);
      }      
    }

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function start_stock_opname($start_date,$end_date){
    $this->db->trans_begin();

    $this->db->select('tb_stock_in_stores.stock_id,tb_stock_in_stores.stores,tb_stock_in_stores.warehouse');
    $this->db->from('tb_stock_in_stores');
    $this->db->where('tb_stock_in_stores.received_date <=', $end_date);
    $this->db->group_by('tb_stock_in_stores.stock_id,tb_stock_in_stores.stores,tb_stock_in_stores.warehouse');
    $query_tb_stocks  = $this->db->get();
    $tb_stocks        = $query_tb_stocks->result_array();

    foreach ($tb_stocks as $stocks) {
      $this->db->select('SUM(quantity) as qty');
      $this->db->select('SUM(total_value) as total_value');
      $this->db->from('tb_stock_cards');
      $this->db->where('tb_stock_cards.stock_id',$stocks['stock_id']);
      $this->db->where('tb_stock_cards.stores',$stocks['stores']);
      $this->db->where('tb_stock_cards.date_of_entry <=',$end_date);
      $query_stock_card  = $this->db->get();
      $tb_stock_card     = $query_stock_card->result_array();
      foreach ($tb_stock_card as $value) {
        $this->db->set('stock_id', $stocks['stock_id']);
        $this->db->set('base', $stocks['warehouse']);
        $this->db->set('stores', $stocks['stores']);
        $this->db->set('qty_sistem', $value['qty']);
        $this->db->set('total_value_sistem', $value['total_value']);
        $this->db->set('qty_actual', 0);
        $this->db->set('average_value_actual', 0);
        $this->db->set('total_value_actual', 0);
        $this->db->set('status', 'unpublish');
        $this->db->set('start_date', $start_date);
        $this->db->set('end_date', $end_date);
        $this->db->set('required_adj', 0-$value['qty']);
        $this->db->set('balance', 0);
        $this->db->set('required_value_adj', 0);
        if ($value['qty']==0) {
          $this->db->set('average_value_sistem', floatval(0));
        }else{
          $this->db->set('average_value_sistem', floatval($value['total_value']/$value['qty']));
        }
        $this->db->insert('tbl_stock_opname');
      }    
      

    }

    $this->db->set('start_date', $start_date);
    $this->db->set('end_date', $end_date);
    $this->db->set('status', 'unpublish');
    $this->db->set('condition', 'unpublish');
    $this->db->insert('tb_last_opname');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;

  }

  public function getIndexUnpublishSelectedColumns()
  {
    $return = array(
      'tbl_stock_opname.id'                       => NULL,      
      // 'tb_master_items.id as item_id'               => 'Item Id',
      'tb_master_items.part_number'                 => 'Part Number',
      'tb_master_items.serial_number'               => 'Serial Number',
      'tb_master_items.kode_stok'                   => 'Stock Code',
      'tb_master_items.description'                 => 'Description',
      'tb_stocks.condition'                         => 'Condition',      
      'tbl_stock_opname.base'                       => 'Base',
      'tbl_stock_opname.stores'                     => 'Stores',
      'tbl_stock_opname.qty_actual'                 => 'Qty Fisik',
      // 'tbl_stock_opname.balance'                 => 'Balance',
      'tb_master_items.unit'                        => 'Unit',
    );
    if (config_item('auth_role') == 'SUPERVISOR' || config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'PIC PROCUREMENT'){
        // $return['tb_stock_cards.unit_value']                  = 'Price';
        $return['tbl_stock_opname.qty_sistem']         = 'Stock Qty';
        $return['tbl_stock_opname.required_adj']       = 'Required Adj';
    }
    if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'PIC PROCUREMENT'){
        // $return['tb_stock_cards.unit_value']                  = 'Price';
        $return['tbl_stock_opname.total_value_sistem']         = 'Total Value';
    }
    if (config_item('auth_role') == 'SUPERVISOR') {
      $return['tbl_stock_opname.update_by']         = 'Edited By';
    }
      
      
    return $return;
  }

  public function getIndexUnpublishGroupedColumns()
  {
    $retur =  array(
      'tbl_stock_opname.id',
      'tb_master_items.id',
      'tb_master_items.kode_stok',
      'tb_master_items.part_number',
      'tb_master_items.serial_number',
      'tb_master_items.description',
      'tb_stocks.condition',
      // 'tbl_stock_opname.qty_sistem',
      'tb_master_items.unit',
      // 'tbl_stock_opname.required_adj',
      'tbl_stock_opname.base',
      'tbl_stock_opname.stores',
      'tb_master_item_groups.category',
      // 'tbl_stock_opname.balance'

    );
    if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'PIC PROCUREMENT'){
        // $return['tb_stock_cards.unit_value']                  = 'Price';
        $return['tbl_stock_opname.total_value_sistem'];
        $return['tbl_stock_opname.required_adj'];
    }
    if (config_item('auth_role') == 'SUPERVISOR' || config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'PIC PROCUREMENT'){
        // $return['tb_stock_cards.unit_value']                  = 'Price';
        $return['tbl_stock_opname.qty_sistem'];
    }
    if (config_item('auth_role') == 'SUPERVISOR') {
      $return['tbl_stock_opname.update_by'];
    }

    return $retur;
  }

  public function getIndexUnpublishOrderableColumns()
  {
    return array(
      'tb_master_items.id as item_id',
      'tb_master_items.kode_stok',
      'tb_master_items.part_number',
      'tb_master_items.serial_number',
      'tb_master_items.description',
      'tb_stocks.condition',
      'tbl_stock_opname.qty_sistem',
      'tb_master_items.unit',
      'tbl_stock_opname.required_adj',
      'tbl_stock_opname.total_value_sistem',
      'tbl_stock_opname.base',
      'tbl_stock_opname.stores',
      'tb_master_item_groups.category'
      // 'tbl_stock_opname.balance'
    );
  }

  public function getIndexUnpublishSearchableColumns()
  {
    return array(
      // 'tb_master_items.id as item_id',
      'tb_master_items.kode_stok',
      'tb_master_items.part_number',
      'tb_master_items.serial_number',
      'tb_master_items.description',
      'tb_stocks.condition',
      // 'tbl_stock_opname.qty_sistem',
      'tb_master_items.unit',
      // 'tbl_stock_opname.required_adj',
      'tbl_stock_opname.base',
      'tbl_stock_opname.stores',
      'tb_master_item_groups.category'
      // 'tbl_stock_opname.balance'
    );
  }

  private function searchIndexUnpublish()
  {
    
    $now=date('Y-m-d');

    if (!empty($_POST['columns'][3]['search']['value'])){
      $category = $_POST['columns'][3]['search']['value'];

      $this->db->where('tb_master_item_groups.category', $category);
    }

    if (!empty($_POST['columns'][4]['search']['value'])){
      $stores = $_POST['columns'][4]['search']['value'];

      $this->db->where('tbl_stock_opname.stores', $stores);
    }
    if (!empty($_POST['columns'][5]['search']['value'])){
      $required_adj = $_POST['columns'][5]['search']['value'];
      if($required_adj==0){
        $this->db->where('tbl_stock_opname.required_adj',0);
      }else{
        $this->db->where('tbl_stock_opname.required_adj !=',0);
      }
    }
    

    $i = 0;
    foreach ($this->getIndexUnpublishSearchableColumns() as $item){
      
      if ($_POST['search']['value']){
        $term = strtoupper($_POST['search']['value']);

        if ($i === 0){
          $this->db->group_start();
          $this->db->like('UPPER('.$item.')', $term);
        } else {
          $this->db->or_like('UPPER('.$item.')', $term);
        }

        if (count($this->getIndexUnpublishSearchableColumns()) - 1 == $i)
          $this->db->group_end();
      }

      $i++;
    }
  }

  public function getIndexUnpublish($start_date, $end_date, $jenis,  $return = 'array')
  {
    
    $this->db->select(array_keys($this->getIndexUnpublishSelectedColumns()));
    // $this->db->select('tb_stock_in_stores_reports.received_date');
    $this->db->from('tbl_stock_opname');
    $this->db->join('tb_stocks', 'tbl_stock_opname.stock_id = tb_stocks.id');
    // $this->db->join('tb_master_stores', 'tb_master_stores.stores = tb_stock_cards.stores');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    // $this->db->join('tb_master_item_categories', 'tb_master_item_categories.category = tb_master_item_groups.category');
    $this->db->where('tbl_stock_opname.start_date', $start_date);
    $this->db->where('tbl_stock_opname.end_date', $end_date);
    $this->db->where('tbl_stock_opname.status', 'unpublish');
    // $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));
    // $this->db->where('tb_stocks.condition', $condition);    

    
    $this->db->group_by($this->getIndexUnpublishGroupedColumns());

    $this->searchIndexUnpublish();

    $orderableColumns = $this->getIndexUnpublishOrderableColumns();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($orderableColumns[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('tb_master_items.id','asc');
      $this->db->order_by('tbl_stock_opname.stores','asc');
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

  public function countIndexUnpublishFiltered($start_date, $end_date, $jenis)
  {
    $this->db->select(array_keys($this->getIndexUnpublishSelectedColumns()));
    // $this->db->select('tb_stock_in_stores_reports.received_date');
    $this->db->from('tbl_stock_opname');
    $this->db->join('tb_stocks', 'tbl_stock_opname.stock_id = tb_stocks.id');
    // $this->db->join('tb_master_stores', 'tb_master_stores.stores = tb_stock_cards.stores');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    // $this->db->join('tb_master_item_categories', 'tb_master_item_categories.category = tb_master_item_groups.category');
    $this->db->where('tbl_stock_opname.start_date', $start_date);
    $this->db->where('tbl_stock_opname.end_date', $end_date);
    $this->db->where('tbl_stock_opname.status', 'unpublish');
    // $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));
    // $this->db->where('tb_stocks.condition', $condition);
   

    $this->searchIndexUnpublish();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndexUnpublish($start_date, $end_date, $jenis)
  {
    $this->db->select(array_keys($this->getIndexUnpublishSelectedColumns()));
    // $this->db->select('tb_stock_in_stores_reports.received_date');
    $this->db->from('tbl_stock_opname');
    $this->db->join('tb_stocks', 'tbl_stock_opname.stock_id = tb_stocks.id');
    // $this->db->join('tb_master_stores', 'tb_master_stores.stores = tb_stock_cards.stores');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    // $this->db->join('tb_master_item_categories', 'tb_master_item_categories.category = tb_master_item_groups.category');
    $this->db->where('tbl_stock_opname.start_date', $start_date);
    $this->db->where('tbl_stock_opname.end_date', $end_date);
    $this->db->where('tbl_stock_opname.status', 'unpublish');
    // $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));
    // $this->db->where('tb_stocks.condition', $condition);
    $query = $this->db->get();

    return $query->num_rows();
  }

  public function save_unpublish()
  {
    $this->db->trans_begin();

    $qty_actual = $this->input->post('qty_actual');
    $id         = $this->input->post('id');

    $this->db->select('*');
    $this->db->from('tbl_stock_opname');
    $this->db->where('id',$id);
    $query = $this->db->get();
    $detail = $query->row_array();

    if ($qty_actual==$detail['qty_sistem']) {
      $total_value_actual   = $detail['total_value_sistem'];
    }else{
      $total_value_actual   = $qty_actual*$detail['average_value_sistem'];
    }    
    $average_value_actual = $detail['average_value_actual'];
    $required_adj         = $qty_actual-$detail['qty_sistem'];
    $balance              = $qty_actual - ($detail['qty_sistem']+$required_adj);
    $required_value_adj   = $detail['average_value_sistem'];

    $this->db->set('qty_actual', $qty_actual);
    $this->db->set('average_value_actual', $average_value_actual);
    $this->db->set('total_value_actual', $total_value_actual);
    $this->db->set('required_adj', $required_adj);
    $this->db->set('balance', $balance);
    $this->db->set('required_value_adj', $required_value_adj);
    $this->db->set('update_by', config_item('auth_person_name'));
    $this->db->where('id',$id);
    $this->db->update('tbl_stock_opname');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function detail_unpublish($id)
  {
    $this->db->where('id', $id);

    $query    = $this->db->get('tbl_stock_opname');
    $receipt = $query->unbuffered_row('array');

    $select = array(
      'tbl_stock_opname.*',
      'tb_stocks.condition',
      // 'tb_stock_in_stores.stores',
      'tb_master_items.serial_number',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tbl_stock_opname.qty_actual',
      'tbl_stock_opname.total_value_sistem',
      
    );

    $this->db->select($select);
    $this->db->from('tbl_stock_opname');
    $this->db->join('tb_stocks', 'tb_stocks.id = tbl_stock_opname.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');  
    $this->db->where('tbl_stock_opname.id', $id);

    $query = $this->db->get();
    $return = $query->row_array();

    return $return;
  }

  public function publish($start_date,$end_date)
  {
    $this->db->trans_begin();    

    //imput adjustment
    $this->db->select('*');
    $this->db->where('start_date',$start_date);    
    $this->db->where('end_date',$end_date);
    $this->db->where('required_adj !=',0);
    $this->db->from('tbl_stock_opname');
    $query = $this->db->get();
    $stock_opname = $query->result_array();

    //doc_number
    $year = date('Y');
    $format = '/ADJ/ALL'. $year;

    $this->db->select_max('document_number', 'last_number');
    $this->db->from('tb_stock_adjustments');
    $this->db->like('document_number', $format, 'before');
    $query  = $this->db->get();
    $row    = $query->unbuffered_row();
    $last   = $row->last_number;
    $number = substr($last, 0, 6);
    $next   = $number + 1;
    $return = sprintf('%06s', $next);    
    $document_number = $return.$format;

    foreach ($stock_opname as $data) {
        $base = ['WISNU'=>1,'BANYUWANGI'=>2,'SOLO'=>3,'LOMBOK'=>4,'JEMBER'=>5,'PALANGKARAYA'=>6,'WISNU REKONDISI'=>7,'BSR REKONDISI'=>8,];
        $warehouse    = 'WISNU';
        $stores       = 'FUEL WSN';

        $warehouse_id = $base[$warehouse];
        $stock_id     = $data['stock_id'];
        $serial       = getSerial($data['item_id'], $data['serial_number']);
        $serial_id    = $serial->id;

        // ADD to STORES

        if ($data['required_adj']>0) {
          $this->db->set('stock_id', $stock_id);
          $this->db->set('serial_id', $serial_id);
          $this->db->set('warehouse', $warehouse);
          $this->db->set('stores', strtoupper($stores));
          $this->db->set('initial_quantity', floatval(0));
          $this->db->set('quantity', floatval(0));
          $this->db->set('unit_value', floatval($data['average_value_sistem']));
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
          $this->db->set('received_by', 'HASIL ADJUSTMENT');
          $this->db->set('created_by', config_item('auth_person_name'));
          $this->db->set('remarks', 'HASIL ADJUSTMENT');
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

        $adjustment_quantity  = floatval($data['required_adj']);
        $remarks              = 'PERMINTAAN ADJ DARI STOCK OPNAME';
        $date                 = date('Y-m-d');

        $prev_stock = getStockPrev($stock_id,$stores);
        if ($prev_stock == 0) {
          $unit_value = $stock['unit_value'];
        }else{
          $unit_value = $stock['unit_value'];
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
        $this->db->set('unit_value', floatval($unit_value));
        // if ($adjustment_quantity==0) {
        //   $this->db->set('total_value', floatval($unit_value));
        // }else{
          
        // }
        $this->db->set('total_value', floatval($unit_value*$adjustment_quantity));      
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('document_number', $document_number);
        $this->db->set('remarks', $remarks);
        $this->db->insert('tb_stock_adjustments');
        $insert_id = $this->db->insert_id();
    }

    $this->db->set('status','publish');
    $this->db->where('start_date',$start_date);
    $this->db->where('end_date',$end_date);
    $this->db->where('tbl_stock_opname');

    //ganti tanggal last publish sebelumnya menjadi publish
    $this->db->set('status','published');
    $this->db->where('status', 'last_publish');
    $this->db->update('tb_last_opname');

    //upate tanggal unpublish menjadi last publish
    $this->db->set('status', 'last_publish');
    $this->db->set('condition', 'publish');
    $this->db->where('status', 'unpublish');
    $this->db->where('start_date',$start_date);    
    $this->db->where('end_date',$end_date);
    $this->db->update('tb_last_opname');
    

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function set_qty_fisik()
  {
    $this->db->trans_begin();

    // $qty_actual = $this->input->post('qty_actual');
    // $id         = $this->input->post('id');

    $this->db->select('*');
    $this->db->from('tbl_stock_opname');
    $this->db->where('qty_actual',0);    
    $this->db->where('qty_sistem !=',0);
    $query = $this->db->get();
    $result = $query->result_array();

    foreach ($result as $data) {
      $qty_actual = $data['qty_sistem'];


      if ($qty_actual==$data['qty_sistem']) {
        $total_value_actual   = $data['total_value_sistem'];
      }else{
        $total_value_actual   = $qty_actual*$data['average_value_sistem'];
      }    
      $average_value_actual = $data['average_value_actual'];
      $required_adj         = $qty_actual-$data['qty_sistem'];
      $balance              = $qty_actual-($data['qty_sistem']+$required_adj);
      $required_value_adj   = $required_adj*$data['average_value_sistem'];

      $this->db->set('qty_actual', $qty_actual);
      $this->db->set('average_value_actual', $average_value_actual);
      $this->db->set('total_value_actual', $total_value_actual);
      $this->db->set('required_adj', $required_adj);
      $this->db->set('balance', $balance);
      $this->db->set('required_value_adj', $required_value_adj);
      $this->db->set('update_by', config_item('auth_person_name'));
      $this->db->where('id',$data['id']);
      $this->db->update('tbl_stock_opname');
    }    

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function coba($start_date,$end_date){
    $this->db->trans_begin();

    $this->db->select('tb_stock_in_stores.stock_id,tb_stock_in_stores.stores,tb_stock_in_stores.warehouse');
    // $this->db->select('tb_stock_in_stores.stock_id');
    $this->db->from('tb_stock_in_stores');
    // $this->db->join('tb_stocks','tb_stocks.id = tb_stock_in_stores.stock_id');
    // $this->db->where('tb_stock_in_stores.received_date <=', $end_date);
    $this->db->group_by('tb_stock_in_stores.stock_id,tb_stock_in_stores.stores,tb_stock_in_stores.warehouse');
    $this->db->order_by('tb_stock_in_stores.stock_id');
    $query_tb_stocks  = $this->db->get();
    $tb_stocks        = $query_tb_stocks->result();

    
    return $tb_stocks ;

  }


}
