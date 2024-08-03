<?php defined('BASEPATH') OR exit('No direct script access allowed');

class App_Model extends MY_Model
{
  protected $tb_auth_user_histories;
  protected $user_id;

  public function __construct()
  {
    parent::__construct();

    $this->tb_auth_user_histories = 'tb_auth_user_histories';
    $this->user_id = config_item('auth_user_id');
  }

  public function set_user_history(array $data)
  {
    return $this->db->insert($this->tb_auth_user_histories, $data);
  }

  public function login()
  {
    $data = array(
      'user_id'   => config_item('auth_user_id'),
      'status'    => 'login',
      'notes'   => 'User '. config_item('auth_username') .' has logged in.',
   );

    return $this->set_user_history($data);
  }

  public function logout()
  {
    $data = array(
      'user_id'   => config_item('auth_user_id'),
      'status'    => 'logout',
      'notes'   => 'User '. config_item('auth_username') .' has logged out.',
   );

    return $this->set_user_history($data);
  }

  public function search_stock_in_stores()
  {
    $selected = array(
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.alternate_part_number',
      'tb_master_items.serial_number',
     );

    $this->db->select($selected);
    $this->db->from('tb_master_items');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_master_item_groups.status', 'AVAILABLE');

    $this->db->order_by('tb_master_items.group ASC, tb_master_items.description ASC, tb_master_items.part_number ASC, tb_master_items.serial_number ASC');

    $query  = $this->db->get();
    $result = $query->result_array();
    $data   = array();

    foreach ($result as $row) {
      $alternate_part_number = (empty($row['alternate_part_number']))
        ? NULL : ' | '. $row['alternate_part_number'];

      $serial_number = (empty($row['serial_number']))
        ? NULL : ' | '. $row['serial_number'];

      $data[] = $row['description'] .' | '. $row['part_number'] . $alternate_part_number . $serial_number;
    }

    return json_encode($data);
  }

  public function distinct($table, $select, array $criteria = null, $json = false)
  {
    $this->db->distinct();

    $this->db->select($select);

    if ($criteria !== null)
      $this->db->where($criteria);

    $this->db->order_by($select);

    $query  = $this->db->get($table);
    $result = $query->result();

    $data  = array();

    foreach ($result as $entity){
      if ($entity->$select != null)
        $data[] = $entity->$select;
    }

    if ($json === false)
      return $data;

    return json_encode($data);
  }

  public function find_items($term, $warehouse)
  {
    $term = urldecode($term);
    $term = strtoupper($term);
    $term = explode(' | ', $term);

    for ($t = 0; $t < count($term); $t++){
      if ($t === 0){
        $search = "t2.description ILIKE '%$term[$t]%' OR t2.part_number ILIKE '%$term[$t]%' OR t2.serial_number ILIKE '%$term[$t]%'";
      } else {
        $search .= " OR t2.description ILIKE '%$term[$t]%' OR t2.part_number ILIKE '%$term[$t]%' OR t2.serial_number ILIKE '%$term[$t]%'";
      }
    }

    // $selected = array(
    //   'tb_stock_in_stores.*',
    //   'tb_stocks.condition',
    //   'tb_master_items.part_number',
    //   'tb_master_items.alternate_part_number',
    //   'tb_master_items.serial_number',
    //   'tb_master_items.description',
    //   'tb_master_items.group',
    //   'tb_master_items.unit',
    // );
    //
    // $this->db->select($selected);
    // $this->db->from('tb_stocks');
    // $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    // $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.stock_id = tb_stocks.id');
    // $this->db->where('tb_stock_in_stores.quantity > ', 0);

    if ($warehouse === 'ALL BASE'){
      $sql = "SELECT t3.*, t1.condition, t2.serial_number, t2.part_number, t2.description, t2.group, t2.alternate_part_number, t2.unit
                FROM tb_stocks t1
                JOIN tb_master_items t2 ON t2.id = t1.item_id
                JOIN tb_stock_in_stores t3 ON t3.stock_id = t1.id
                -- LEFT JOIN tb_master_item_serials t4 ON t4.id = t3.serial_id
                WHERE t3.quantity > 0
                AND (
                  $search
                )
                ORDER BY t2.description ASC, t2.part_number ASC
            ";
    } else {
      $sql = "SELECT t3.*, t1.condition, t2.serial_number, t2.part_number, t2.description, t2.group, t2.alternate_part_number, t2.unit
                FROM tb_stocks t1
                JOIN tb_master_items t2 ON t2.id = t1.item_id
                JOIN tb_stock_in_stores t3 ON t3.stock_id = t1.id
                -- LEFT JOIN tb_master_item_serials t4 ON t4.id = t3.serial_id
                WHERE t3.quantity > 0
                AND t3.warehouse = '$warehouse'
                AND (
                  $search
                )
                ORDER BY t2.description ASC, t2.part_number ASC
            ";
    }

    $query  = $this->db->query($sql);
    $result = $query->result_array();

    return $result;
  }

  public function find_item_in_stores($warehouse, $term)
  {
    $sql = "SELECT t1.*, t2.description, t2.group, t2.alternate_part_number, t2.unit, t2.id AS item_in_stores_id
            FROM tb_stocks t1
            JOIN tb_master_items t2 ON t2.part_number = t1.part_number
            WHERE t1.warehouse = '$warehouse'
            AND t1.quantity > 0
            AND (
                t2.description ILIKE '%$term%' OR
                t1.part_number ILIKE '%$term%' OR
                t1.item_serial ILIKE '%$term%'
           )
            ORDER BY t2.description ASC, t1.part_number ASC
        ";

    $query = $this->db->query($sql);
    $result = $query->result_array();

    foreach ($result as $key => $value){
      $result[$key]['aircraft_types'] = $this->findItemModelsByPartNumber($value['part_number']);
      $result[$key]['balance_stock']  = $this->find_balance_stock($value['part_number'], $value['item_serial'], $value['warehouse']);
    }

    return $result;
  }

  public function adjustment_approval($updated_status, $id, $adjustment_token)
  {
    $updated_status = strtoupper($updated_status);

    // $this->db->select('tb_master_items.part_number');
    $this->db->from('tb_stock_adjustments');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_stock_adjustments.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->where('tb_stock_adjustments.id', $id);
    $this->db->where('tb_stock_adjustments.adjustment_token', $adjustment_token);
    $this->db->where('tb_stock_adjustments.updated_status', 'PENDING');

    $query = $this->db->get();

    if ($query->num_rows() === 0){
      return '<div class="alert alert-danger">Data request not found!</div>';
    }

    $stock = $query->unbuffered_row('array');

    $this->db->trans_begin();

    $this->db->where('id', $id);
    $this->db->set('adjustment_token', NULL);
    $this->db->set('updated_at', date('Y-m-d H:i:s'));
    $this->db->set('updated_status', $updated_status);

    $update = ($this->db->update('tb_stock_adjustments')) ? true : false;

    if ($update === true){
      if ($updated_status == 'APPROVED'){
        if ($stock['adjustment_quantity'] >= 0){
          $this->db->set('received_by', config_item('auth_person_name'));
          $this->db->set('received_from', 'ADJUSTMENT');
        } else {
          $this->db->set('issued_by', config_item('auth_person_name'));
          $this->db->set('issued_to', 'ADJUSTMENT');
        }

        // RECALCULATE STOCK
        $this->db->from('tb_stock_adjustments');
        $this->db->where('tb_stock_adjustments.id', $id);
        $query = $this->db->get();
        $stock_adj = $query->unbuffered_row('array');

        $current_quantity     = floatval($stock['quantity']);
        $stores_quantity      = $current_quantity + $stock['adjustment_quantity'];
        $prev_quantity        = floatval($stock['total_quantity']);
        $balance_quantity     = floatval($stock['total_quantity']) + $stock['adjustment_quantity'];
        $unit_value           = floatval($stock_adj['unit_value']);
        $total_value          = floatval($stock_adj['total_value']);
        $grand_total_value    = floatval($stock['grand_total_value']) + $total_value;

        if ($balance_quantity == 0){
          $average_value = 0;
        } else {
          $average_value = $grand_total_value / $balance_quantity;
        }
		    $this->db->from('tb_stock_in_stores');
        $this->db->where('tb_stock_in_stores.id', $stock_adj['stock_in_stores_id']);
        $query_tb_stock_in_stores = $this->db->get();
        $tb_stock_in_stores = $query_tb_stock_in_stores->unbuffered_row('array');
		
		    $prev_old_stock = getStockPrev($tb_stock_in_stores['stock_id'],$tb_stock_in_stores['stores'])+ floatval($stock['adjustment_quantity']);
        $next_old_stock = floatval($prev_old_stock) + floatval($stock['adjustment_quantity']);

        $this->db->set('stock_id', $stock['stock_id']);
        $this->db->set('serial_id', $stock['serial_id']);
        $this->db->set('warehouse', $stock['warehouse']);
        $this->db->set('stores', $stock['stores']);
        $this->db->set('date_of_entry', date('Y-m-d'));
        $this->db->set('period_year', config_item('period_year'));
        $this->db->set('period_month', config_item('period_month'));
        $this->db->set('document_type', 'ADJUSTMENT');
        $this->db->set('document_number', $stock_adj['document_number']);
        $this->db->set('quantity', $stock_adj['adjustment_quantity']);
        $this->db->set('prev_quantity', $prev_old_stock);
        $this->db->set('balance_quantity', $next_old_stock);
        $this->db->set('unit_value', $unit_value);
        $this->db->set('average_value', $average_value);
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('remarks', 'adjustment stock');
        $this->db->set('tgl', date('Ymd'));
        $this->db->set('total_value', $total_value);
        $this->db->set('doc_type', 1);
        $this->db->set('stock_in_stores_id', $stock_adj['stock_in_stores_id']);
        $this->db->insert('tb_stock_cards');

        //$this->db->set('reference_document', $stock_adj['document_number']);
        //$this->db->set('received_date', $date);
        $this->db->set('unit_value', $unit_value);
        //$this->db->set('initial_unit_value', $unit_value);
        $this->db->where('id',$stock_adj['stock_in_stores_id']);
        $this->db->update('tb_stock_in_stores');

        //upate stock in tb master part number
        // $qty_awal = getPartnumberQty($stock['part_number']);

        // $qty_baru = floatval($qty_awal) + floatval($stock['adjustment_quantity']);

        // $this->db->set('qty', $qty_baru);
        // $this->db->where('part_number', strtoupper($stock['part_number']));
        // $this->db->update('tb_master_part_number');
        $return = '<div class="alert alert-info">Adjustment request has been approved.</div>';
      } else {
        $return = '<div class="alert alert-danger">Adjustment request rejected.</div>';
      }
    } else {
      $return = '<div class="alert alert-danger">Error while processing request! Please try again later.</div>';
    }

    if ($this->db->trans_status() === FALSE)
      return '<div class="alert alert-danger">Error while processing request! Please try again later.</div>';

    $this->db->trans_commit();
    return $return;
  }

  public function find_balance_stock($part_number, $item_serial, $warehouse = null, $stores = null)
  {
    if ($warehouse !== null)
      $this->db->where('warehouse', $warehouse);

    if ($stores !== null)
      $this->db->where('warehouse', $warehouse);

    $this->db->where('part_number', $part_number);
    $this->db->where('item_serial', $item_serial);
    $this->db->where('condition !=', 'REJECTED');
    $this->db->select_sum('quantity', 'quantity');
    $query = $this->db->get('tb_stocks');
    $row = $query->row_array();

    return $row['quantity'];
  }

  public function cron_job_send_email()
  {
    
  }
}
