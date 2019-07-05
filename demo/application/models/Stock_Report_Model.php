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
    return array(
      'tb_master_item_categories.id' => NULL,
      'tb_master_item_categories.category' => 'Category',
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

  public function getIndexGroupedColumns()
  {
    return array(
      'tb_master_item_categories.id',
      'tb_master_item_categories.category',
      'tb_stock_in_stores_reports.condition',
    );
  }

  public function getIndexOrderableColumns()
  {
    return array(
      null,
      'tb_master_item_categories.category',
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

  public function getIndexSearchableColumns()
  {
    return array(
      'tb_master_item_categories.category',
      'tb_stock_in_stores_reports.condition',
    );
  }

  private function searchIndex()
  {
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

  public function getIndex($period_month, $period_year, $condition = "SERVICEABLE", $return = 'array')
  {
    $this->db->select(array_keys($this->getIndexSelectedColumns()));
    $this->db->from('tb_stock_in_stores_reports');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stock_in_stores_reports.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->join('tb_master_item_categories', 'tb_master_item_categories.category = tb_master_item_groups.category');
    $this->db->where('period_year', $period_year);
    $this->db->where('period_month', $period_month);
    $this->db->where('tb_stock_in_stores_reports.condition', $condition);
    $this->db->where_in('tb_master_item_categories.category', config_item('auth_inventory'));
    $this->db->group_by($this->getIndexGroupedColumns());

    $this->searchIndex();

    $orderableColumns = $this->getIndexOrderableColumns();

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

  public function countIndexFiltered($period_month, $period_year, $condition = "SERVICEABLE")
  {
    $this->db->select(array_keys($this->getIndexSelectedColumns()));
    $this->db->from('tb_stock_in_stores_reports');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stock_in_stores_reports.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->join('tb_master_item_categories', 'tb_master_item_categories.category = tb_master_item_groups.category');
    $this->db->where('period_year', $period_year);
    $this->db->where('period_month', $period_month);
    $this->db->where('tb_stock_in_stores_reports.condition', $condition);
    $this->db->where_in('tb_master_item_categories.category', config_item('auth_inventory'));
    $this->db->group_by($this->getIndexGroupedColumns());

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex($period_month, $period_year, $condition = "SERVICEABLE")
  {
    $this->db->select(array_keys($this->getIndexSelectedColumns()));
    $this->db->from('tb_stock_in_stores_reports');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stock_in_stores_reports.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->join('tb_master_item_categories', 'tb_master_item_categories.category = tb_master_item_groups.category');
    $this->db->where('period_year', $period_year);
    $this->db->where('period_month', $period_month);
    $this->db->where('tb_stock_in_stores_reports.condition', $condition);
    $this->db->where_in('tb_master_item_categories.category', config_item('auth_inventory'));
    $this->db->group_by($this->getIndexGroupedColumns());

    $query = $this->db->get();

    return $query->num_rows();
  }

  // SUMMARY REPORT

  public function getSummarySelectedColumns()
  {
    return array(
      'tb_master_item_groups.id' => NULL,
      'tb_master_item_groups.group' => 'Group',
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
}
