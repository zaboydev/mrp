<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('isItemUnitExists')) {
  function isItemUnitExists($unit)
  {
    $CI =& get_instance();

    $CI->db->from('tb_master_item_units');
    $CI->db->where('UPPER(unit)', strtoupper($unit));

    $num_rows = $CI->db->count_all_results();

    return ($num_rows > 0) ? TRUE : FALSE;
  }
}

if ( ! function_exists('isitemGroupExists')) {
  function isItemGroupExists($group)
  {
    $CI =& get_instance();

    $CI->db->from('tb_master_item_groups');
    $CI->db->where('group', strtoupper($group));

    $num_rows = $CI->db->count_all_results();

    return ($num_rows > 0) ? TRUE : FALSE;
  }
}


if ( ! function_exists('isStoresExists')) {
  function isStoresExists($stores, $category = NULL)
  {
    $CI =& get_instance();

    $CI->db->from('tb_master_stores');
    $CI->db->where('UPPER(stores)', strtoupper($stores));

    if ($category !== NULL){
      $CI->db->where('category', $category);
    }

    $num_rows = $CI->db->count_all_results();

    return ($num_rows > 0) ? TRUE : FALSE;
  }
}

if ( ! function_exists('isItemExists')) {
  function isItemExists($part_number, $description, $serial_number = NULL)
  {
    $CI =& get_instance();

    $CI->db->from('tb_master_items');
    $CI->db->where('UPPER(part_number)', strtoupper($part_number));
    $CI->db->where('UPPER(description)', strtoupper($description));

    if ($serial_number !== NULL){
      $CI->db->where('UPPER(serial_number)', strtoupper($serial_number));
    }

    $num_rows = $CI->db->count_all_results();

    return ($num_rows > 0) ? TRUE : FALSE;
  }
}

if ( ! function_exists('getItemId')) {
  function getItemId($part_number, $description, $serial_number = NULL)
  {
    $CI =& get_instance();

    $CI->db->select('id');
    $CI->db->from('tb_master_items');
    $CI->db->where('UPPER(part_number)', strtoupper($part_number));
    $CI->db->where('UPPER(description)', strtoupper($description));

    if ($serial_number !== NULL){
      $CI->db->where('UPPER(serial_number)', strtoupper($serial_number));
    }

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $return = $row->id;

    return $return;
  }
}

if (!function_exists('getParNumberId')) {
  function getParNumberId($part_number)
  {
    $CI = &get_instance();

    $CI->db->select('id');
    $CI->db->from('tb_master_part_number');
    $CI->db->where('UPPER(part_number)', strtoupper($part_number));

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $return = $row->id;

    return $return;
  }
}

if ( ! function_exists('isSerialExists')) {
  function isSerialExists($item_id, $serial_number)
  {
    $CI =& get_instance();

    $CI->db->from('tb_master_item_serials');
    $CI->db->where('item_id', $item_id);
    $CI->db->where('UPPER(serial_number)', strtoupper($serial_number));

    $num_rows = $CI->db->count_all_results();

    return ($num_rows > 0) ? TRUE : FALSE;
  }
}

if ( ! function_exists('getSerial')) {
  function getSerial($item_id, $serial_number)
  {
    $CI =& get_instance();

    $CI->db->from('tb_master_item_serials');
    $CI->db->where('item_id', $item_id);
    $CI->db->where('UPPER(serial_number)', strtoupper($serial_number));

    $query  = $CI->db->get();
    $return = $query->unbuffered_row();

    return $return;
  }
}

if ( ! function_exists('isStockExists')) {
  function isStockExists($item_id, $condition)
  {
    $CI =& get_instance();

    $CI->db->from('tb_stocks');
    $CI->db->where('item_id', $item_id);
    $CI->db->where('UPPER(condition)', strtoupper($condition));

    $num_rows = $CI->db->count_all_results();

    return ($num_rows > 0) ? TRUE : FALSE;
  }
}

if ( ! function_exists('getStockId')) {
  function getStockId($item_id, $condition)
  {
    $CI =& get_instance();

    $CI->db->select('id');
    $CI->db->from('tb_stocks');
    $CI->db->where('item_id', $item_id);
    $CI->db->where('UPPER(condition)', strtoupper($condition));

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $return = $row->id;

    return $return;
  }
}

//tambahan 18/Mei/2018
if ( ! function_exists('getStockinStoreId')) {
  function getStockinStoreId($stock_id)
  {
    $CI =& get_instance();

    $CI->db->select('id');
    $CI->db->from('tb_stock_in_stores');
    $CI->db->where('stock_id', $stock_id);

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $return = $row->id;

    return $return;
  }
}

if ( ! function_exists('isStockInStoresExists')) {
  function isStockInStoresExists($item_id, $stores, $condition = 'SERVICEABLE', $reference_document=NULL)
  {
    $CI =& get_instance();

    $CI->db->from('tb_stocks');
    $CI->db->join('tb_stock_in_stores', 'tb_stock_in_stores.stock_id = tb_stocks.id');
    $CI->db->where('tb_stocks.item_id', $item_id);
    $CI->db->where('UPPER(tb_stocks.condition)', strtoupper($condition));
    $CI->db->where('UPPER(tb_stock_in_stores.stores)', strtoupper($stores));

    if ($reference_document !== NULL){
      $CI->db->where('UPPER(tb_stock_in_stores.reference_document)', strtoupper($reference_document));
    }

    $num_rows = $CI->db->count_all_results();

    return ($num_rows > 0) ? TRUE : FALSE;
  }
}

if ( ! function_exists('getStockInStoresId')) {
  function getStockInStoresId($item_id, $stores, $condition = 'SERVICEABLE', $reference_document = NULL)
  {
    $CI =& get_instance();

    $CI->db->select('tb_stock_in_stores.id');
    $CI->db->from('tb_stocks');
    $CI->db->join('tb_stock_in_stores', 'tb_stock_in_stores.stock_id = tb_stocks.id');
    $CI->db->where('tb_stocks.item_id', $item_id);
    $CI->db->where('UPPER(tb_stocks.condition)', strtoupper($condition));
    $CI->db->where('UPPER(tb_stock_in_stores.stores)', strtoupper($stores));

    if ($reference_document !== NULL){
      $CI->db->where('UPPER(tb_stock_in_stores.reference_document)', strtoupper($reference_document));
    }

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $return = $row->id;

    return $return;
  }
}

if ( ! function_exists('getStockActive')) {
  function getStockActive($id)
  {
    $CI =& get_instance();

    $CI->db->select('total_quantity, grand_total_value');
    $CI->db->from('tb_stocks');
    $CI->db->where('id', $id);

    $query  = $CI->db->get();
    $return = $query->unbuffered_row();

    return $return;
  }
}

if ( ! function_exists('getStockQuantity')) {
  function getStockQuantity($item_id, $condition)
  {
    $CI =& get_instance();

    $CI->db->select('total_quantity');
    $CI->db->from('tb_stocks');
    $CI->db->where('item_id', $item_id);
    $CI->db->where('UPPER(condition)', strtoupper($condition));

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $return = $row->total_quantity;

    return $return;
  }
}

if ( ! function_exists('getStockValue')) {
  function getStockValue($item_id, $condition)
  {
    $CI =& get_instance();

    $CI->db->select('grand_total_value');
    $CI->db->from('tb_stocks');
    $CI->db->where('item_id', $item_id);
    $CI->db->where('UPPER(condition)', strtoupper($condition));

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $return = $row->grand_total_value;

    return $return;
  }
}

if ( ! function_exists('countItemStockInStores')) {
  function countItemStockInStores()
  {
    $CI =& get_instance();

    $CI->db->select('i.id');
    $CI->db->from('tb_stock_in_stores ss');
    $CI->db->join('tb_stocks s', 's.id = ss.stock_id');
    $CI->db->join('tb_master_items i', 'i.id = s.item_id');
    $CI->db->join('tb_master_item_groups g', 'g.group = i.group');
    $CI->db->where('s.condition', 'SERVICEABLE');
    $CI->db->where_in('ss.warehouse', config_item('auth_warehouses'));
    $CI->db->where_in('g.category', config_item('auth_inventory'));
    $CI->db->group_by('i.id');

    $query = $CI->db->get();

    return $query->num_rows();
  }
}

if ( ! function_exists('countItemLowStock')) {
  function countItemLowStock()
  {
    $CI =& get_instance();

    $CI->db->select_sum('ss.quantity', 'quantity');
    $CI->db->select('i.id, i.minimum_quantity');
    $CI->db->from('tb_stock_in_stores ss');
    $CI->db->join('tb_stocks s', 's.id = ss.stock_id');
    $CI->db->join('tb_master_items i', 'i.id = s.item_id');
    $CI->db->where('s.condition', 'SERVICEABLE');
    $CI->db->having('SUM(ss.quantity) <= i.minimum_quantity');
    $CI->db->group_by('i.id, i.minimum_quantity');

    $query = $CI->db->get();

    return $query->num_rows();
  }
}

if ( ! function_exists('getKonversi')) {
  function getKonversi($item_id)
  {
    $CI =& get_instance();

    $CI->db->select('qty_konversi');
    $CI->db->from('tb_master_items');
    $CI->db->where('id', $item_id);

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $return = $row->qty_konversi;

    return $return;
  }
}

if ( ! function_exists('getPartnumberQty')) {
  function getPartnumberQty($part_number)
  {
    $CI =& get_instance();

    $CI->db->select('qty');
    $CI->db->from('tb_master_part_number');
    $CI->db->where('UPPER(part_number)', strtoupper($part_number));

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $return = $row->qty;

    return $return;
  }
}

if (!function_exists('getPartnumber')) {
  function getPartnumber($id)
  {
    $CI = &get_instance();

    $CI->db->select('part_number');
    $CI->db->from('tb_master_part_number');
    $CI->db->where('id', strtoupper($id));

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $return = $row->part_number;

    return $return;
  }
}

if ( ! function_exists('isPartNumberExists')) {
  function isPartNumberExists($part_number)
  {
    $CI =& get_instance();

    $CI->db->from('tb_master_part_number');
    $CI->db->where('UPPER(part_number)', strtoupper($part_number));


    $num_rows = $CI->db->count_all_results();

    return ($num_rows > 0) ? TRUE : FALSE;
  }
}

if ( ! function_exists('getStockPrev')) {
  function getStockPrev($stock_id,$stores)
  {
    $CI =& get_instance();

    $CI->db->select('quantity');
    $CI->db->from('tb_stock_in_stores');
    $CI->db->where('stock_id', $stock_id);
    $CI->db->where('stores', strtoupper($stores));

    $query  = $CI->db->get();
    $result = $query->result_array();
    $return = 0;

    foreach ($result as $row) {
      $return = $return+$row['quantity'];
    }

    return $return;
  }
}

if ( ! function_exists('getAverageValue')) {
  function getAverageValue($stock_id)
  {
    $CI =& get_instance();

    $CI->db->select('quantity');
    $CI->db->select('unit_value');
    $CI->db->from('tb_stock_in_stores');
    $CI->db->where('stock_id', $stock_id);
    // $CI->db->where('stores', strtoupper($stores));

    $query  = $CI->db->get();
    $result = $query->result_array();
    $return_total_value = 0;
    $return = 0;
    $total_qty = 0;

    foreach ($result as $row) {
      $total_value        = $row['quantity']*$row['unit_value'];
      // $total_qty          = $total_qty+$row['quantity'];
      $return_total_value = $return_total_value+$total_value;
    }
    // if($total_qty==0){
    //   $return = 0;
    // }else{
    //   $return = $return_total_value/$total_qty;
    // }
    $return = $return_total_value;  

    return $return;
  }
}

if ( ! function_exists('getLastUnitValue')) {
  function getMaxUnitValue($stock_id)
  {
    $CI =& get_instance();

    // // $CI->db->select('quantity');
    // $CI->db->select('unit_value');
    // $CI->db->from('tb_stock_in_stores');
    // $CI->db->where('stock_id', $stock_id);
    // $CI->db->where('stores', strtoupper($stores));
    // $CI->db->orderBy('id', 'desc');
    // $CI->db->limit(1);

    $CI->db->select_max('unit_value', 'unit_value');
    $CI->db->where('stock_id', $stock_id);
    // $CI->db->where('stores', strtoupper($stores));
    $CI->db->from('tb_stock_in_stores');

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $last   = $row->unit_value;

    // $CI->db->select('unit_value');
    // $CI->db->where('id', $last);
    // $CI->db->from('tb_stock_in_stores');

    // $query    = $CI->db->get();
    // $result   = $query->unbuffered_row();
    // $return   = $result->unit_value;

    return $last;
  }

  if ( ! function_exists('getLeftQty')) {
    function getLeftQty($document_number)
    {
      $CI =& get_instance();

      $CI->db->select('left_received_quantity');
      $CI->db->from('tb_issuance_items');
      $CI->db->where('document_number', $document_number);
      // $CI->db->where('stores', strtoupper($stores));

      $query  = $CI->db->get();
      $result = $query->result_array();
      $return = 0;

      foreach ($result as $row) {
        $return = $return+$row['left_received_quantity'];
      }

      return $return;
    }
  }

  if (!function_exists('leftQtyPo')) {
    function leftQtyPo($id_po)
    {
      $CI = &get_instance();

      $CI->db->select('left_received_quantity');
      $CI->db->from('tb_po_item');
      $CI->db->where('purchase_order_id', $id_po);
      // $CI->db->where('stores', strtoupper($stores));

      $query  = $CI->db->get();
      $result = $query->result_array();
      $return = 0;

      foreach ($result as $row) {
        $return = $return + $row['left_received_quantity'];
      }

      return $return;
    }
  }

  if (!function_exists('leftAmountPo')) {
    function leftAmountPo($id_po)
    {
      $CI = &get_instance();

      $CI->db->select('remaining_payment');
      $CI->db->from('tb_po');
      $CI->db->where('id', $id_po);
      // $CI->db->where('stores', strtoupper($stores));

      $query  = $CI->db->get();
      $result = $query->unbuffered_row();
      $return = $result->remaining_payment;

      // foreach ($result as $row) {
      //   $return = $return + $row['left_received_quantity'];
      // }

      return $return;
    }
  }

  if (!function_exists('cotProcessExists')) {
    function cotProcessExists($year)
    {
      $CI = &get_instance();

      $CI->db->from('tb_budget_cot');
      $CI->db->where('year', $year);
      $CI->db->where('status', 'ON PROCESS');

      $num_rows = $CI->db->count_all_results();

      return $num_rows;
    }
  }

  if (!function_exists('getPoeId')) {
    function getPoeId($id)
    {
      $CI = &get_instance();

      $CI->db->select('purchase_order_id');
      $CI->db->from('tb_purchase_order_vendors');
      $CI->db->where('id', $id);
      // $CI->db->where('UPPER(condition)', strtoupper($condition));

      $query  = $CI->db->get();
      $row    = $query->unbuffered_row();
      $return = $row->purchase_order_id;

      return $return;
    }
  }

  if (!function_exists('poeVendor')) {
    function poeVendor($purchase_order_id)
    {
      $CI = &get_instance();

      $CI->db->from('tb_purchase_order_vendors');
      $CI->db->where('purchase_order_id', $purchase_order_id);
      $CI->db->where('is_selected', true);

      $num_rows = $CI->db->count_all_results();

      return $num_rows;
    }
  }

  if (!function_exists('uangMuka')) {
    function uangMuka($po_item_id)
    {
      $CI = &get_instance();

      $CI->db->from('tb_purchase_order_items_payments');
      $CI->db->where('purchase_order_item_id', $po_item_id);
      $CI->db->where('uang_muka > 0');
      $CI->db->where('status','PAID');

      $num_rows = $CI->db->count_all_results();

      return ($num_rows > 0) ? TRUE : FALSE;
    }
  }

  if (!function_exists('sumJurnal')) {
    function sumJurnal($id_jurnal, $tipe)
    {
      $CI = &get_instance();
      if($tipe=='kredit'){
        $CI->db->select('trs_kredit as value');
      }else{
        $CI->db->select('trs_debet as value');
      }
      
      $CI->db->from('tb_jurnal_detail');
      $CI->db->where('id_jurnal', $id_jurnal);
      // $CI->db->where('stores', strtoupper($stores));

      $query  = $CI->db->get();
      $result = $query->result_array();
      $return = 0;

      foreach ($result as $row) {
        $return = $return + $row['value'];
      }

      return $return;
    }
  }

  function search_min_qty($part_number)
  {
    $CI =& get_instance();

    $CI->db->select('min_qty');
    $CI->db->from('tb_master_part_number');
    $CI->db->where('UPPER(part_number)', strtoupper($part_number));


    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $return = $row->min_qty;

    return $return;
  }

}


