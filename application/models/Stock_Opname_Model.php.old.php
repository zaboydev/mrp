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
      'tb_master_items.id'                          => NULL,
      'tb_master_items.part_number'                 => 'Part Number',
      'tb_master_items.description'                 => 'Description',
      'tb_master_items.serial_number'               => 'Serial Number',
      'tb_master_item_groups.category'              => 'Category',
      'tb_master_items.group'                       => 'Group',
      'tb_stock_opnames.condition'                  => 'Condition',
      'tb_stock_opnames.previous_total_quantity'    => 'Initial Stock',
      'tb_stock_opnames.total_received_quantity'    => 'Received',
      'tb_stock_opnames.total_issued_quantity'      => 'Issued',
      'tb_stock_opnames.total_adjustment_quantity'  => 'Adjustment',
      'tb_stock_opnames.current_total_quantity'     => 'Balance',
      'tb_stock_opnames.current_average_value'      => 'Avg. Value',
      'tb_master_items.minimum_quantity'            => 'Min. Quantity',
      'tb_master_items.unit'                        => 'Unit'
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
      'tb_stock_opnames.condition',
      'tb_stock_opnames.previous_total_quantity',
      'tb_stock_opnames.total_received_quantity',
      'tb_stock_opnames.total_issued_quantity',
      'tb_stock_opnames.total_adjustment_quantity',
      'tb_stock_opnames.current_total_quantity',
      'tb_stock_opnames.current_average_value',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit'
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
      'tb_master_items.condition',
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

  public function getIndex($period_month, $period_year, $condition = "SERVICEABLE", $category = NULL, $return = 'array')
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_stock_opnames');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stock_opnames.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('period_year', $period_year);
    $this->db->where('period_month', $period_month);
    $this->db->where('condition', $condition);

    if ($category !== NULL){
      $this->db->where('tb_master_item_groups.category', $category);
    }

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

  public function countIndexFiltered($period_month, $period_year, $condition = "SERVICEABLE", $category = NULL)
  {
    $this->db->from('tb_stock_opnames');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stock_opnames.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('period_year', $period_year);
    $this->db->where('period_month', $period_month);
    $this->db->where('condition', $condition);

    if ($category !== NULL){
      $this->db->where('tb_master_item_groups.category', $category);
    }

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex($period_month, $period_year, $condition = "SERVICEABLE", $category = NULL)
  {
    $this->db->from('tb_stock_opnames');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stock_opnames.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('period_year', $period_year);
    $this->db->where('period_month', $period_month);
    $this->db->where('condition', $condition);

    if ($category !== NULL){
      $this->db->where('tb_master_item_groups.category', $category);
    }

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

    // CREATE NEW STOCK OPNAME
    // get all stocks by inventory
    $this->db->select('tb_stocks.*');
    $this->db->from('tb_stocks');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));

    $query  = $this->db->get();
    $stocks = $query->result_array();

    foreach ($stocks as $stock) {
      $stock_id                   = intval($stock['id']);
      $item_id                    = intval($stock['item_id']);
      $condition                  = $stock['condition'];
      $initial_total_quantity     = floatval($stock['initial_total_quantity']);
      $initial_grand_total_value  = floatval($stock['initial_grand_total_value']);
      $initial_average_value      = floatval($stock['initial_average_value']);
      $average_value              = floatval($stock['average_value']);
      $grand_total_value          = floatval($stock['grand_total_value']);
      $total_quantity             = floatval($stock['total_quantity']);

      // get current period total stock adjustment
      $this->db->select_sum('tb_stock_adjustments.adjustment_quantity', 'total_adjustment_quantity');
      $this->db->select('tb_stock_in_stores.stock_id');
      $this->db->from('tb_stock_adjustments');
      $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_stock_adjustments.stock_in_stores_id');
      $this->db->where('tb_stock_in_stores.stock_id', $stock_id);
      $this->db->where('tb_stock_adjustments.period_year', $current_year);
      $this->db->where('tb_stock_adjustments.period_month', $current_month);
      $this->db->group_by('tb_stock_in_stores.stock_id');

      $query  = $this->db->get();

      if ($query->num_rows() > 0){
        $adjustment = $query->unbuffered_row('array');
        $total_adjustment_quantity = $adjustment['total_adjustment_quantity'];
      } else {
        $total_adjustment_quantity = 0;
      }

      // get current period total stock received
      $this->db->select_sum('tb_receipt_items.received_quantity', 'total_received_quantity');
      $this->db->select('tb_stock_in_stores.stock_id');
      // $this->db->select('SUM(tb_receipt_items.received_quantity) AS total_received_quantity');
      $this->db->from('tb_receipts');
      $this->db->join('tb_receipt_items', 'tb_receipt_items.document_number = tb_receipts.document_number');
      $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_receipt_items.stock_in_stores_id');
      $this->db->where('tb_stock_in_stores.stock_id', $stock_id);
      $this->db->where('EXTRACT(MONTH FROM tb_receipts.received_date)::integer = ', $current_month);
      $this->db->where('EXTRACT(YEAR FROM tb_receipts.received_date)::integer = ', $current_year);
      $this->db->group_by('tb_stock_in_stores.stock_id');

      $query  = $this->db->get();

      if ($query->num_rows() > 0){
        $received = $query->unbuffered_row('array');
        $total_received_quantity = floatval($received['total_received_quantity']);
      } else {
        $total_received_quantity = floatval(0);
      }

      // get current period total stock issued
      $this->db->select('tb_stock_in_stores.stock_id');
      $this->db->select_sum('tb_issuance_items.issued_quantity', 'total_issued_quantity');
      // $this->db->select('SUM(tb_issuance_items.issued_quantity) AS total_issued_quantity');
      $this->db->from('tb_issuances');
      $this->db->join('tb_issuance_items', 'tb_issuance_items.document_number = tb_issuances.document_number');
      $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_issuance_items.stock_in_stores_id');
      $this->db->where('tb_stock_in_stores.stock_id', $stock_id);
      $this->db->where('EXTRACT(MONTH FROM tb_issuances.issued_date)::integer = ', $current_month);
      $this->db->where('EXTRACT(YEAR FROM tb_issuances.issued_date)::integer = ', $current_year);
      $this->db->group_by('tb_stock_in_stores.stock_id');

      $query  = $this->db->get();

      if ($query->num_rows() > 0){
        $issued = $query->unbuffered_row('array');
        $total_issued_quantity = floatval($issued['total_issued_quantity']);
      } else {
        $total_issued_quantity = floatval(0);
      }

      // get quantity and value from previous stock opnames
      $this->db->from('tb_stock_opnames');
      $this->db->where('period_year', $previous_year);
      $this->db->where('period_month', $previous_month);
      $this->db->where('item_id', $item_id);
      $this->db->where('condition', $condition);

      $query  = $this->db->get();

      if ($query->num_rows() > 0){
        $previous_stock             = $query->unbuffered_row('array');
        $previous_total_quantity    = $previous_stock['current_total_quantity'];
        $previous_grand_total_value = $previous_stock['current_grand_total_value'];
        $previous_average_value     = $previous_stock['current_average_value'];
      } else {
        $initial_total_quantity     = $total_quantity - (($total_received_quantity - $total_issued_quantity) + ($total_adjustment_quantity));

        $previous_total_quantity    = $initial_total_quantity;
        $previous_grand_total_value = $initial_grand_total_value;
        $previous_average_value     = $initial_average_value;
      }

      // STOCK OPNAME
      $this->db->set('period_year', $current_year);
      $this->db->set('period_month', $current_month);
      $this->db->set('item_id', $item_id);
      $this->db->set('condition', $condition);
      $this->db->set('previous_total_quantity', $previous_total_quantity);
      $this->db->set('previous_grand_total_value', $previous_grand_total_value);
      $this->db->set('previous_average_value', $previous_average_value);
      $this->db->set('current_total_quantity', $total_quantity);
      $this->db->set('current_grand_total_value', $grand_total_value);
      $this->db->set('current_average_value', $average_value);
      $this->db->set('total_adjustment_quantity', $total_adjustment_quantity);
      $this->db->set('total_received_quantity', $total_received_quantity);
      $this->db->set('total_issued_quantity', $total_issued_quantity);
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->insert('tb_stock_opnames');
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
      $initial_quantity           = floatval($stock_detail['initial_quantity']);
      $initial_unit_value         = floatval($stock_detail['initial_unit_value']);
      $current_quantity           = floatval($stock_detail['quantity']);
      $current_unit_value         = floatval($stock_detail['unit_value']);
      $current_total_value        = $current_quantity * $current_unit_value;
      $current_average_value      = $current_total_value;

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
      $this->db->select('tb_receipt_items.stock_in_stores_id');
      $this->db->from('tb_receipts');
      $this->db->join('tb_receipt_items', 'tb_receipt_items.document_number = tb_receipts.document_number');
      $this->db->where('tb_receipt_items.stock_in_stores_id', $stock_in_stores_id);
      $this->db->where('EXTRACT(MONTH FROM tb_receipts.received_date)::integer = ', $current_month);
      $this->db->where('EXTRACT(YEAR FROM tb_receipts.received_date)::integer = ', $current_year);
      $this->db->group_by('tb_receipt_items.stock_in_stores_id');

      $query  = $this->db->get();

      if ($query->num_rows() > 0){
        $received = $query->unbuffered_row('array');
        $total_received_quantity      = floatval($received['total_received_quantity']);
        $total_received_total_value   = floatval($received['total_received_total_value']);

        if ($total_received_quantity == 0){
          $total_received_average_value = floatval(0);
        } else {
          $total_received_average_value = $total_received_total_value/$total_received_quantity;
        }
      } else {
        $total_received_quantity      = floatval(0);
        $total_received_total_value   = floatval(0);
        $total_received_average_value = floatval(0);
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
        $previous_stock_in_stores   = $query->unbuffered_row('array');
        $previous_quantity          = floatval($previous_stock_in_stores['current_quantity']);
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
      $this->db->set('current_average_value', $average_value);
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
}
