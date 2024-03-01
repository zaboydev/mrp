<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_Report_Model extends MY_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  // SUMMARY REPORT

  public function getIndexSelectedColumns()
  {
    $return = array(
      'tb_stock_cards.stock_id'                     => NULL,
      'tb_master_items.id as item_id'                      => 'Item Id',
      'tb_master_items.part_number'                 => 'Part Number',
      'tb_master_items.description'                 => 'Description',
      'tb_master_items.serial_number'               => 'Serial Number',
      'tb_master_items.kode_stok'                   => 'Stock Code',
      'tb_master_item_groups.coa'                   => 'COA',
      'tb_master_items.group'                       => 'Category',
      'tb_master_item_categories.category'          => 'Group',
      'tb_stocks.condition'                         => 'Condition',
      'SUM(tb_stock_cards.quantity) as qty'         => 'Quantity',
      'tb_master_items.unit'                        => 'Unit',
      // 'tb_stock_cards.unit_value'                   => 'Price',
      'tb_master_items.minimum_quantity'            => 'Min. Qty',
      'tb_stock_cards.stores'                       => 'Stores',
      // 'tb_stock_cards.warehouse'                    => 'Stores',
      );

      if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'SUPER ADMIN'){
        // $return['tb_stock_cards.unit_value']                  = 'Price';
        $return['SUM(tb_stock_cards.total_value) as total_value']         = 'Total Price';
        //$return[NULL]         = 'Total Price';
      }
      
      //'tb_stock_in_stores_reports.received_date' => 'Received Date',     
      
    return $return;
  }

  public function getIndexGroupedColumns()
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
      // 'tb_stock_cards.warehouse',
      'tb_master_items.id'
    );
    // if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE'){
    //     $retur['tb_stock_cards.unit_value'];
    // }

    return $retur;
  }

  public function getIndexOrderableColumns()
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
      // 'tb_stock_cards.warehouse',
    );
  }

  public function getIndexSearchableColumns()
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
      // 'tb_stock_cards.warehouse',
    );
  }

  private function searchIndex()
  {
    
    $now=date('Y-m-d');
    

    $i = 0;
    foreach ($this->getIndexSearchableColumns() as $item){
      
      if ($_POST['search']['value']){
        $term = strtoupper($_POST['search']['value']);

        if ($i === 0){
          $this->db->group_start();
          $this->db->like('UPPER('.$item.')', $term);
        } else {
          $this->db->or_like('UPPER('.$item.')', $term);
        }

        if (count($this->getIndexSearchableColumns()) - 1 == $i)
          $this->db->group_end();
      }

      $i++;
    }
  }

  public function getIndex($condition = NULL, $warehouse= NULL, $start_date = NULL, $end_date = NULL, $category = NULL,  $return = 'array')
  {
    
    $this->db->select(array_keys($this->getIndexSelectedColumns()));
    // $this->db->select('tb_stock_in_stores_reports.received_date');
    $this->db->from('tb_stock_cards');
    $this->db->join('tb_stocks', 'tb_stock_cards.stock_id = tb_stocks.id');
    $this->db->join('tb_master_stores', 'tb_master_stores.stores = tb_stock_cards.stores','left');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->join('tb_master_item_categories', 'tb_master_item_categories.category = tb_master_item_groups.category');
    // $this->db->where('quantity != ', 0);
	  // $this->db->where('tb_stocks.condition', $condition);

    if ($condition !== NULL){
      $this->db->where('tb_stocks.condition', $condition);
    }
    

    if ($category !== NULL){
      $this->db->where('tb_master_item_categories.category', $category);
    } else {
      $this->db->where_in('tb_master_item_categories.category', config_item('auth_inventory'));
    }

    if ($start_date !== NULL && $end_date !== NULL){
      // $this->db->where('tb_stock_in_stores.received_date >= ', $start_date);
      $this->db->where('tb_stock_cards.date_of_entry <= ', $end_date);
    }

    if ($warehouse !== NULL){
      if($warehouse == 'WISNU-'){
        $this->db->group_start()
                  ->like('tb_master_stores.warehouse', 'WISNU')
                  // ->or_where('tb_stock_in_stores_reports.warehouse=', 'WISNU REKONDISI')
                  ->group_end();
      }
      else if($warehouse == "all base rekondisi"){
        $this->db->group_start()
                  ->like('tb_master_stores.warehouse', 'REKONDISI')
                  ->group_end();
      }
      else{
        $this->db->where('tb_master_stores.warehouse', $warehouse);
      }
         
    }
    $this->db->group_by($this->getIndexGroupedColumns());

    $this->searchIndex();

    $orderableColumns = $this->getIndexOrderableColumns();

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

  public function countIndexFiltered($condition = NULL, $warehouse= NULL, $start_date = NULL, $end_date = NULL, $category = NULL)
  {
    $this->db->select(array_keys($this->getIndexSelectedColumns()));
    // $this->db->select('tb_stock_in_stores_reports.received_date');
    $this->db->from('tb_stock_cards');
    $this->db->join('tb_stocks', 'tb_stock_cards.stock_id = tb_stocks.id');
    $this->db->join('tb_master_stores', 'tb_master_stores.stores = tb_stock_cards.stores');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->join('tb_master_item_categories', 'tb_master_item_categories.category = tb_master_item_groups.category');
    $this->db->where('quantity != ', 0);
	  // $this->db->where('tb_stocks.condition', $condition);
    if ($condition !== NULL){
      $this->db->where('tb_stocks.condition', $condition);
    }
    $this->db->group_by($this->getIndexGroupedColumns());
    if ($category !== NULL){
      $this->db->where('tb_master_item_categories.category', $category);
    } else {
      $this->db->where_in('tb_master_item_categories.category', config_item('auth_inventory'));
    }

    if ($start_date !== NULL && $end_date !== NULL){
      // $this->db->where('tb_stock_in_stores.received_date >= ', $start_date);
      $this->db->where('tb_stock_cards.date_of_entry <= ', $end_date);
    }

    if ($warehouse !== NULL){
      if($warehouse == 'WISNU-'){
        $this->db->group_start()
                  ->like('tb_master_stores.warehouse', 'WISNU')
                  // ->or_where('tb_stock_in_stores_reports.warehouse=', 'WISNU REKONDISI')
                  ->group_end();
      }
      elseif($warehouse == "all base rekondisi"){
        $this->db->group_start()
                  ->like('tb_master_stores.warehouse', 'REKONDISI')
                  ->group_end();
      }
      else{
        $this->db->where('tb_master_stores.warehouse', $warehouse);
      }
    }
   

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex($condition = NULL, $warehouse= NULL, $start_date = NULL, $end_date = NULL, $category = NULL)
  {
    $this->db->select(array_keys($this->getIndexSelectedColumns()));
    // $this->db->select('tb_stock_in_stores_reports.received_date');
    $this->db->from('tb_stock_cards');
    $this->db->join('tb_stocks', 'tb_stock_cards.stock_id = tb_stocks.id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_stores', 'tb_master_stores.stores = tb_stock_cards.stores');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->join('tb_master_item_categories', 'tb_master_item_categories.category = tb_master_item_groups.category');
    $this->db->where('quantity != ', 0);
	  // $this->db->where('tb_stocks.condition', $condition);
    if ($condition !== NULL){
      $this->db->where('tb_stocks.condition', $condition);
    }
    $this->db->group_by($this->getIndexGroupedColumns());
    if ($category !== NULL){
      $this->db->where('tb_master_item_categories.category', $category);
    } else {
      $this->db->where_in('tb_master_item_categories.category', config_item('auth_inventory'));
    }

    if ($start_date !== NULL && $end_date !== NULL){
      // $this->db->where('tb_stock_in_stores.received_date >= ', $start_date);
      $this->db->where('tb_stock_cards.date_of_entry <= ', $end_date);
    }

    if ($warehouse !== NULL){
      if($warehouse == 'WISNU-'){
        $this->db->group_start()
                  ->like('tb_master_stores.warehouse', 'WISNU')
                  // ->or_where('tb_stock_in_stores_reports.warehouse=', 'WISNU REKONDISI')
                  ->group_end();
      }
      elseif($warehouse == "all base rekondisi"){
        $this->db->group_start()
                  ->like('tb_master_stores.warehouse', 'REKONDISI')
                  ->group_end();
      }
      else{
        $this->db->where('tb_master_stores.warehouse', $warehouse);
      }
    }

    $query = $this->db->get();

    return $query->num_rows();
  }

  // SUMMARY REPORT

  public function getSummarySelectedColumns()
  {
    return array(
      'tb_master_item_groups.id' => NULL,
      'tb_master_item_groups.group' => 'Group',

      'tb_master_items.part_number'   => 'Part Number',
      'tb_master_items.description'               => 'Description',
      'tb_master_item_groups.category' => 'Category',
      'tb_stock_in_stores_reports.condition' => 'Condition',
      'SUM(tb_stock_in_stores_reports.previous_quantity) AS previous_quantity' => 'Initial Qty',
      'SUM(tb_stock_in_stores_reports.total_received_quantity) AS total_received_quantity' => 'Received Qty',
      'SUM(tb_stock_in_stores_reports.total_issued_quantity) AS total_issued_quantity' => 'Issued Qty',
      'SUM(tb_stock_in_stores_reports.total_adjustment_quantity) AS total_adjustment_quantity' => 'Adjustment Qty',
      'SUM(tb_stock_in_stores_reports.current_quantity) AS current_quantity' => 'Balance Qty',
      'SUM(tb_stock_in_stores_reports.current_total_value) AS current_total_value' => 'Total Value',
      '(CASE WHEN SUM(tb_stock_in_stores_reports.current_quantity) = 0 THEN 0 ELSE SUM(tb_stock_in_stores_reports.current_total_value)/SUM(tb_stock_in_stores_reports.current_quantity) END) AS current_average_value' => 'Average Value',
    );
  }

  public function getSummaryGroupedColumns()
  {
    return array(
      'tb_master_item_groups.id',
      'tb_master_item_groups.group',
      'tb_master_item_groups.category',
      'tb_stock_in_stores_reports.condition',
    );
  }

  public function getSummaryOrderableColumns()
  {
    return array(
      null,
      'tb_master_item_groups.group',
      'tb_master_item_groups.category',
      'tb_stock_in_stores_reports.condition',
      'SUM(tb_stock_in_stores_reports.previous_quantity)',
      'SUM(tb_stock_in_stores_reports.total_received_quantity)',
      'SUM(tb_stock_in_stores_reports.total_issued_quantity)',
      'SUM(tb_stock_in_stores_reports.total_adjustment_quantity)',
      'SUM(tb_stock_in_stores_reports.current_quantity)',
      'SUM(tb_stock_in_stores_reports.current_total_value)',
      '(CASE WHEN SUM(tb_stock_in_stores_reports.current_quantity) = 0 THEN 0 ELSE SUM(tb_stock_in_stores_reports.current_total_value)/SUM(tb_stock_in_stores_reports.current_quantity) END)',
    );
  }

  public function getSummarySearchableColumns()
  {
    return array(
      'tb_master_item_groups.group',
      'tb_master_item_groups.category',
      'tb_stock_in_stores_reports.condition',
    );
  }

  private function searchSummary()
  {
    $i = 0;

    foreach ($this->getSummarySearchableColumns() as $item){
      if ($_POST['search']['value']){
        $term = strtoupper($_POST['search']['value']);

        if ($i === 0){
          $this->db->group_start();
          $this->db->like('UPPER('.$item.')', $term);
        } else {
          $this->db->or_like('UPPER('.$item.')', $term);
        }

        if (count($this->getSummarySearchableColumns()) - 1 == $i)
          $this->db->group_end();
      }

      $i++;
    }
  }

  public function getSummary($period_month, $period_year, $condition = "SERVICEABLE", $category = NULL, $return = 'array')
  {
    $this->db->select(array_keys($this->getSummarySelectedColumns()));
    $this->db->from('tb_stock_in_stores_reports');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stock_in_stores_reports.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('period_year', $period_year);
    $this->db->where('period_month', $period_month);
    $this->db->where('tb_stock_in_stores_reports.condition', $condition);

    if ($category === NULL){
      $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));
    } else {
      $this->db->where('tb_master_item_groups.category', $category);
    }

    $this->db->group_by($this->getSummaryGroupedColumns());

    $this->searchSummary();

    $orderableColumns = $this->getSummaryOrderableColumns();

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

  public function countSummaryFiltered($period_month, $period_year, $condition = "SERVICEABLE", $category = NULL)
  {
    $this->db->select(array_keys($this->getSummarySelectedColumns()));
    $this->db->from('tb_stock_in_stores_reports');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stock_in_stores_reports.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('period_year', $period_year);
    $this->db->where('period_month', $period_month);
    $this->db->where('tb_stock_in_stores_reports.condition', $condition);

    if ($category === NULL){
      $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));
    } else {
      $this->db->where('tb_master_item_groups.category', $category);
    }

    $this->db->group_by($this->getSummaryGroupedColumns());

    $this->searchSummary();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countSummary($period_month, $period_year, $condition = "SERVICEABLE", $category = NULL)
  {
    $this->db->select(array_keys($this->getSummarySelectedColumns()));
    $this->db->from('tb_stock_in_stores_reports');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stock_in_stores_reports.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('period_year', $period_year);
    $this->db->where('period_month', $period_month);
    $this->db->where('tb_stock_in_stores_reports.condition', $condition);

    if ($category === NULL){
      $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));
    } else {
      $this->db->where('tb_master_item_groups.category', $category);
    }

    $this->db->group_by($this->getSummaryGroupedColumns());

    $query = $this->db->get();

    return $query->num_rows();
  }

  // DETAIL REPORT

  public function getDetailSelectedColumns()
  {
    return array(
      'tb_master_items.id' => NULL,
      'tb_master_items.part_number' => 'Part Number',
      'tb_master_items.description' => 'Description',
      'tb_master_items.serial_number' => 'Serial Number',
      'tb_master_item_groups.category' => 'Category',
      'tb_master_items.group' => 'Group',
      'tb_stock_in_stores_reports.condition' => 'Condition',
      'tb_stock_in_stores_reports.warehouse' => 'Base',
      'tb_stock_in_stores_reports.stores' => 'Stores',
      'SUM(tb_stock_in_stores_reports.previous_quantity) AS previous_quantity' => 'Initial Qty',
      'SUM(tb_stock_in_stores_reports.total_received_quantity) AS total_received_quantity' => 'Received Qty',
      'SUM(tb_stock_in_stores_reports.total_issued_quantity) AS total_issued_quantity' => 'Issued Qty',
      'SUM(tb_stock_in_stores_reports.total_adjustment_quantity) AS total_adjustment_quantity' => 'Adjustment Qty',
      'SUM(tb_stock_in_stores_reports.current_quantity) AS current_quantity' => 'Balance Qty',
      'SUM(tb_stock_in_stores_reports.current_total_value) AS current_total_value' => 'Total Value',
      '(CASE WHEN SUM(tb_stock_in_stores_reports.current_quantity) = 0 THEN 0 ELSE SUM(tb_stock_in_stores_reports.current_total_value)/SUM(tb_stock_in_stores_reports.current_quantity) END) AS current_average_value' => 'Average Value',
      'tb_master_items.minimum_quantity' => 'Minimum Qty',
      'tb_master_items.unit' => 'Unit'
    );
  }

  public function getDetailGroupedColumns()
  {
    return array(
      'tb_master_items.id',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.serial_number',
      'tb_master_item_groups.category',
      'tb_master_items.group',
      'tb_stock_in_stores_reports.condition',
      'tb_stock_in_stores_reports.warehouse',
      'tb_stock_in_stores_reports.stores',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit'
    );
  }

  public function getDetailOrderableColumns()
  {
    return array(
      null,
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.serial_number',
      'tb_master_item_groups.category',
      'tb_master_items.group',
      'tb_stock_in_stores_reports.condition',
      'tb_stock_in_stores_reports.warehouse',
      'tb_stock_in_stores_reports.stores',
      'SUM(tb_stock_in_stores_reports.previous_quantity)',
      'SUM(tb_stock_in_stores_reports.total_received_quantity)',
      'SUM(tb_stock_in_stores_reports.total_issued_quantity)',
      'SUM(tb_stock_in_stores_reports.total_adjustment_quantity)',
      'SUM(tb_stock_in_stores_reports.current_quantity)',
      'SUM(tb_stock_in_stores_reports.current_total_value)',
      '(CASE WHEN SUM(tb_stock_in_stores_reports.current_quantity) = 0 THEN 0 ELSE SUM(tb_stock_in_stores_reports.current_total_value)/SUM(tb_stock_in_stores_reports.current_quantity) END)',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit'
    );
  }

  public function getDetailSearchableColumns()
  {
    return array(
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.serial_number',
      'tb_master_item_groups.category',
      'tb_master_items.group',
      'tb_stock_in_stores_reports.condition',
      'tb_stock_in_stores_reports.warehouse',
      'tb_stock_in_stores_reports.stores',
    );
  }

  private function searchDetail()
  {
    $i = 0;

    foreach ($this->getDetailSearchableColumns() as $item){
      if ($_POST['search']['value']){
        $term = strtoupper($_POST['search']['value']);

        if ($i === 0){
          $this->db->group_start();
          $this->db->like('UPPER('.$item.')', $term);
        } else {
          $this->db->or_like('UPPER('.$item.')', $term);
        }

        if (count($this->getDetailSearchableColumns()) - 1 == $i)
          $this->db->group_end();
      }

      $i++;
    }
  }

  public function getDetail($period_month, $period_year, $condition = "SERVICEABLE", $warehouse = NULL, $category = NULL, $group = NULL, $return = 'array')
  {
    $this->db->select(array_keys($this->getDetailSelectedColumns()));
    $this->db->from('tb_stock_in_stores_reports');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stock_in_stores_reports.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('period_year', $period_year);
    $this->db->where('period_month', $period_month);
    $this->db->where('tb_stock_in_stores_reports.condition', $condition);

    if ($category !== NULL){
      $this->db->where('tb_master_item_groups.category', $category);
    } else {
      $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));
    }

    if ($warehouse !== NULL){
      $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
    }

    if ($group !== NULL){
      $this->db->where('tb_master_item_groups.group', $group);
    }

    $this->db->group_by($this->getDetailGroupedColumns());

    $this->searchDetail();

    $orderableColumns = $this->getDetailOrderableColumns();

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

  public function countDetailFiltered($period_month, $period_year, $condition = "SERVICEABLE", $warehouse = NULL, $category = NULL, $group = NULL)
  {
    $this->db->select(array_keys($this->getDetailSelectedColumns()));
    $this->db->from('tb_stock_in_stores_reports');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stock_in_stores_reports.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('period_year', $period_year);
    $this->db->where('period_month', $period_month);
    $this->db->where('tb_stock_in_stores_reports.condition', $condition);

    if ($category !== NULL){
      $this->db->where('tb_master_item_groups.category', $category);
    } else {
      $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));
    }

    if ($warehouse !== NULL){
      $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
    }

    if ($group !== NULL){
      $this->db->where('tb_master_item_groups.group', $group);
    }

    $this->db->group_by($this->getDetailGroupedColumns());

    $this->searchDetail();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countDetail($period_month, $period_year, $condition = "SERVICEABLE", $warehouse = NULL, $category = NULL, $group = NULL)
  {
    $this->db->select(array_keys($this->getDetailSelectedColumns()));
    $this->db->from('tb_stock_in_stores_reports');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stock_in_stores_reports.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('period_year', $period_year);
    $this->db->where('period_month', $period_month);
    $this->db->where('tb_stock_in_stores_reports.condition', $condition);

    if ($category !== NULL){
      $this->db->where('tb_master_item_groups.category', $category);
    } else {
      $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));
    }

    if ($warehouse !== NULL){
      $this->db->where('tb_stock_in_stores_reports.warehouse', $warehouse);
    }

    if ($group !== NULL){
      $this->db->where('tb_master_item_groups.group', $group);
    }

    $this->db->group_by($this->getDetailGroupedColumns());

    $query = $this->db->get();

    return $query->num_rows();
  }


  // SUMMARY REPORT NO SHIIPPING

  public function getIndexSelectedColumns_no_shipping()
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
      // 'SUM(CASE WHEN tb_stock_cards.doc_type != 3 AND tb_stock_cards.doc_type != 4 THEN tb_stock_cards.quantity ELSE 0 END) as qty'         => 'Quantity',
      'tb_master_items.unit'                        => 'Unit',
      // 'tb_stock_cards.unit_value'                   => 'Price',
      // 'tb_master_items.minimum_quantity'            => 'Min. Qty',
      // 'tb_stock_cards.stores'                       => 'Stores',
      // 'tb_stock_cards.warehouse'                    => 'Stores',
      );

      if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'SUPER ADMIN'){
        // $return['SUM(CASE WHEN tb_stock_cards.doc_type != 3 AND tb_stock_cards.doc_type != 4 THEN tb_stock_cards.total_value ELSE 0 END) as total_value']         = 'Total Price';
        $return['SUM(tb_stock_cards.total_value) as total_value']         = 'Total Price';
      }
      
      //'tb_stock_in_stores_reports.received_date' => 'Received Date',     
      
    return $return;
  }

  public function getIndexGroupedColumns_no_shipping()
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
      // 'tb_master_items.minimum_quantity',
      'tb_master_item_groups.coa',
      'tb_master_items.serial_number',
      'tb_master_items.unit',
      // 'tb_stock_cards.warehouse',
      'tb_master_items.id'
    );
    // if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE'){
    //     $retur['tb_stock_cards.unit_value'];
    // }

    return $retur;
  }

  public function getIndexOrderableColumns_no_shipping()
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
      // 'tb_stock_cards.stores',
      'tb_master_items.unit',
      // 'tb_stock_cards.warehouse',
    );
  }

  public function getIndexSearchableColumns_no_shipping()
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
      // 'tb_master_stores.stores',
      'tb_master_items.unit',
      // 'tb_stock_cards.warehouse',
    );
  }

  private function searchIndex_no_shipping()
  {
    
    $now=date('Y-m-d');
    

    $i = 0;
    foreach ($this->getIndexSearchableColumns() as $item){
      
      if ($_POST['search']['value']){
        $term = strtoupper($_POST['search']['value']);

        if ($i === 0){
          $this->db->group_start();
          $this->db->like('UPPER('.$item.')', $term);
        } else {
          $this->db->or_like('UPPER('.$item.')', $term);
        }

        if (count($this->getIndexSearchableColumns()) - 1 == $i)
          $this->db->group_end();
      }

      $i++;
    }
  }

  public function getIndex_no_shipping($condition = NULL, $warehouse= NULL, $start_date = NULL, $end_date = NULL, $category = NULL,  $return = 'array')
  {
    
    $this->db->select(array_keys($this->getIndexSelectedColumns_no_shipping()));
    // $this->db->select('tb_stock_in_stores_reports.received_date');
    $this->db->from('tb_stock_cards');
    $this->db->join('tb_stocks', 'tb_stock_cards.stock_id = tb_stocks.id');
    $this->db->join('tb_master_stores', 'tb_master_stores.stores = tb_stock_cards.stores','left');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->join('tb_master_item_categories', 'tb_master_item_categories.category = tb_master_item_groups.category');
    // $this->db->where('quantity != ', 0);
    // $this->db->where('tb_stocks.condition', $condition);
    if ($condition !== NULL){
      $this->db->where('tb_stocks.condition', $condition);
    }
    

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
      if($warehouse == 'WISNU-'){
        $this->db->group_start()
                  ->like('tb_master_stores.warehouse', 'WISNU')
                  // ->or_where('tb_stock_in_stores_reports.warehouse=', 'WISNU REKONDISI')
                  ->group_end();
      }
      else if($warehouse == "all base rekondisi"){
        $this->db->group_start()
                  ->like('tb_master_stores.warehouse', 'REKONDISI')
                  ->group_end();
      }
      else{
        $this->db->where('tb_master_stores.warehouse', $warehouse);
      }
         
    }
    $this->db->group_by($this->getIndexGroupedColumns_no_shipping());

    $this->searchIndex();

    $orderableColumns = $this->getIndexOrderableColumns_no_shipping();

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

  public function countIndexFiltered_no_shipping($condition = 'SERVICEABLE', $warehouse= NULL, $start_date = NULL, $end_date = NULL, $category = NULL)
  {
    $this->db->select(array_keys($this->getIndexSelectedColumns_no_shipping()));
    // $this->db->select('tb_stock_in_stores_reports.received_date');
    $this->db->from('tb_stock_cards');
    $this->db->join('tb_stocks', 'tb_stock_cards.stock_id = tb_stocks.id');
    $this->db->join('tb_master_stores', 'tb_master_stores.stores = tb_stock_cards.stores','left');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->join('tb_master_item_categories', 'tb_master_item_categories.category = tb_master_item_groups.category');
    // $this->db->where('quantity != ', 0);
    // $this->db->where('tb_stocks.condition', $condition);
    
    if ($condition !== NULL){
      $this->db->where('tb_stocks.condition', $condition);
    }
    

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
      if($warehouse == 'WISNU-'){
        $this->db->group_start()
                  ->like('tb_master_stores.warehouse', 'WISNU')
                  // ->or_where('tb_stock_in_stores_reports.warehouse=', 'WISNU REKONDISI')
                  ->group_end();
      }
      else if($warehouse == "all base rekondisi"){
        $this->db->group_start()
                  ->like('tb_master_stores.warehouse', 'REKONDISI')
                  ->group_end();
      }
      else{
        $this->db->where('tb_master_stores.warehouse', $warehouse);
      }
         
    }
    
    $this->db->group_by($this->getIndexGroupedColumns_no_shipping());   

    $this->searchIndex_no_shipping();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex_no_shipping($condition = 'SERVICEABLE', $warehouse= NULL, $start_date = NULL, $end_date = NULL, $category = NULL)
  {
    $this->db->select(array_keys($this->getIndexSelectedColumns_no_shipping()));
    // $this->db->select('tb_stock_in_stores_reports.received_date');
    $this->db->from('tb_stock_cards');
    $this->db->join('tb_stocks', 'tb_stock_cards.stock_id = tb_stocks.id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_stores', 'tb_master_stores.stores = tb_stock_cards.stores','left');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->join('tb_master_item_categories', 'tb_master_item_categories.category = tb_master_item_groups.category');
    // $this->db->where('quantity != ', 0);
    // $this->db->where('tb_stocks.condition', $condition);
    
    if ($condition !== NULL){
      $this->db->where('tb_stocks.condition', $condition);
    }
    

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
      if($warehouse == 'WISNU-'){
        $this->db->group_start()
                  ->like('tb_master_stores.warehouse', 'WISNU')
                  // ->or_where('tb_stock_in_stores_reports.warehouse=', 'WISNU REKONDISI')
                  ->group_end();
      }
      else if($warehouse == "all base rekondisi"){
        $this->db->group_start()
                  ->like('tb_master_stores.warehouse', 'REKONDISI')
                  ->group_end();
      }
      else{
        $this->db->where('tb_master_stores.warehouse', $warehouse);
      }
         
    }
    
    $this->db->group_by($this->getIndexGroupedColumns_no_shipping());

    $query = $this->db->get();

    return $query->num_rows();
  }

  // SUMMARY REPORT

  public function getIndexSelectedColumns_super_admin()
  {
    $return = array(
      'tb_stock_cards.stock_id'                     => NULL,
      'tb_master_items.id as item_id'                      => 'Item Id',
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
      // 'tb_stock_cards.unit_value'                   => 'Price',
      // 'tb_master_items.minimum_quantity'            => 'Min. Qty',
      'tb_stock_cards.stores'                       => 'Stores',
      // 'tb_stock_cards.warehouse'                    => 'Stores',
      );

      if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE' || config_item('auth_role') == 'SUPER ADMIN'){
        $return['tb_stock_cards.unit_value']                  = 'Price';
        $return['SUM(tb_stock_cards.total_value) as total_value']         = 'Total Price';
        //$return[NULL]         = 'Total Price';
      }
      
      //'tb_stock_in_stores_reports.received_date' => 'Received Date',     
      
    return $return;
  }

  public function getIndexGroupedColumns_super_admin()
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
      'tb_stock_cards.unit_value',
      'tb_master_items.minimum_quantity',
      'tb_master_item_groups.coa',
      'tb_master_items.serial_number',
      'tb_stock_cards.stores',
      'tb_master_items.unit',
      // 'tb_stock_cards.warehouse',
      'tb_master_items.id'
    );
    // if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE'){
    //     $retur['tb_stock_cards.unit_value'];
    // }

    return $retur;
  }

  public function getIndexOrderableColumns_super_admin()
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
      // 'tb_stock_cards.warehouse',
    );
  }

  public function getIndexSearchableColumns_super_admin()
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
      // 'tb_stock_cards.warehouse',
    );
  }

  private function searchIndex_super_admin()
  {
    
    $now=date('Y-m-d');
    

    $i = 0;
    foreach ($this->getIndexSearchableColumns_super_admin() as $item){
      
      if ($_POST['search']['value']){
        $term = strtoupper($_POST['search']['value']);

        if ($i === 0){
          $this->db->group_start();
          $this->db->like('UPPER('.$item.')', $term);
        } else {
          $this->db->or_like('UPPER('.$item.')', $term);
        }

        if (count($this->getIndexSearchableColumns_super_admin()) - 1 == $i)
          $this->db->group_end();
      }

      $i++;
    }
  }

  public function getIndex_super_admin($condition = 'SERVICEABLE', $warehouse= NULL, $start_date = NULL, $end_date = NULL, $category = NULL,  $return = 'array')
  {
    
    $this->db->select(array_keys($this->getIndexSelectedColumns_super_admin()));
    // $this->db->select('tb_stock_in_stores_reports.received_date');
    $this->db->from('tb_stock_cards');
    $this->db->join('tb_stocks', 'tb_stock_cards.stock_id = tb_stocks.id');
    // $this->db->join('tb_master_stores', 'tb_master_stores.stores = tb_stock_cards.stores');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->join('tb_master_item_categories', 'tb_master_item_categories.category = tb_master_item_groups.category');
    // $this->db->where('quantity != ', 0);
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
      if($warehouse == 'WISNU-'){
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
    $this->db->group_by($this->getIndexGroupedColumns_super_admin());

    $this->searchIndex();

    $orderableColumns = $this->getIndexOrderableColumns_super_admin();

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

  public function countIndexFiltered_super_admin($condition = 'SERVICEABLE', $warehouse= NULL, $start_date = NULL, $end_date = NULL, $category = NULL)
  {
    $this->db->select(array_keys($this->getIndexSelectedColumns_super_admin()));
    // $this->db->select('tb_stock_in_stores_reports.received_date');
    $this->db->from('tb_stock_cards');
    $this->db->join('tb_stocks', 'tb_stock_cards.stock_id = tb_stocks.id');
    $this->db->join('tb_master_stores', 'tb_master_stores.stores = tb_stock_cards.stores');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->join('tb_master_item_categories', 'tb_master_item_categories.category = tb_master_item_groups.category');
    $this->db->where('quantity != ', 0);
	  $this->db->where('tb_stocks.condition', $condition);
    $this->db->group_by($this->getIndexGroupedColumns_super_admin());
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
      if($warehouse == 'WISNU-'){
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
   

    $this->searchIndex_super_admin();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex_super_admin($condition = 'SERVICEABLE', $warehouse= NULL, $start_date = NULL, $end_date = NULL, $category = NULL)
  {
    $this->db->select(array_keys($this->getIndexSelectedColumns_super_admin()));
    // $this->db->select('tb_stock_in_stores_reports.received_date');
    $this->db->from('tb_stock_cards');
    $this->db->join('tb_stocks', 'tb_stock_cards.stock_id = tb_stocks.id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_stores', 'tb_master_stores.stores = tb_stock_cards.stores');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->join('tb_master_item_categories', 'tb_master_item_categories.category = tb_master_item_groups.category');
    $this->db->where('quantity != ', 0);
	  $this->db->where('tb_stocks.condition', $condition);
    $this->db->group_by($this->getIndexGroupedColumns_super_admin());
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
      if($warehouse == 'WISNU-'){
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
}
