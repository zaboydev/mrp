<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: imann
 * Date: 23/04/2016
 * Time: 19:39
 */
class MY_Model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function findAllUsers($status = NULL)
  {
    $this->db->from(config_item('module')['user']['table']);

    if ($status !== NULL)
      $this->db->where('banned', $status);

    $this->db->order_by('person_name', 'ASC');

    $query = $this->db->get();

    return $query->result_array();
  }

  public function findAllWarehouses($status = NULL)
  {
    $this->db->from(config_item('module')['warehouse']['table']);

    if ($status !== NULL)
      $this->db->where('status', $status);

    $this->db->order_by('warehouse', 'ASC');

    $query = $this->db->get();

    return $query->result_array();
  }

  public function findAllStores(array $warehouse = NULL, $status = NULL)
  {
    $this->db->from(config_item('module')['stores']['table']);
    $this->db->where_in('warehouse', config_item('auth_warehouses'));

    if ($status !== NULL)
      $this->db->where('status', $status);

    $this->db->order_by('warehouse ASC, stores ASC');

    $query = $this->db->get();

    return $query->result_array();
  }

  public function findAllItemCategories($status = NULL)
  {
    $this->db->from(config_item('module')['category']['table']);

    if ($status !== NULL)
      $this->db->where('status', $status);

    $this->db->order_by('category', 'ASC');

    $query = $this->db->get();

    return $query->result_array();
  }

  public function findAllItemGroups($status = NULL)
  {
    $this->db->from(config_item('module')['group']['table']);
    $this->db->where_in('category', config_item('auth_inventory'));

    if ($status !== NULL)
      $this->db->where('status', $status);

    $this->db->order_by('group', 'ASC');

    $query = $this->db->get();

    return $query->result_array();
  }

  public function findAllUnitOfMeasurements($status = NULL)
  {
    $this->db->from(config_item('module')['item_unit']['table']);

    if ($status !== NULL)
      $this->db->where('status', $status);

    $this->db->order_by('unit', 'asc');

    $query = $this->db->get();

    return $query->result_array();
  }

  public function findAllVendors($status = NULL)
  {
    $this->db->from(config_item('module')['vendor']['table']);

    if ($status !== NULL)
      $this->db->where('status', $status);

    $this->db->order_by('vendor', 'asc');

    $query = $this->db->get();

    return $query->result_array();
  }

  public function countStockInStores()
  {
    $sql = 'SELECT
        (SELECT SUM("stock"."quantity") FROM "tb_stock_in_stores" "stock" WHERE "stock"."warehouse" IN ?) AS "quantity",
        "t_stock"."item_id"
      FROM "tb_stocks" "t_stock"
      JOIN "tb_master_items" "t_item" ON "t_item"."id" = "t_stock"."item_id"
      JOIN "tb_master_item_groups" "t_group" ON "t_group"."group" = "t_item"."group"
      WHERE "t_group"."category" IN ?
      GROUP BY "t_stock"."item_id"';

    $query = $this->db->query($sql, array(
      config_item('auth_warehouses'), config_item('auth_inventory')
    ));

    return $query->num_rows();
  }

  public function countLowStockItems()
  {
    $sql = 'SELECT
        (SELECT SUM("stock"."quantity") FROM "tb_stock_in_stores" "stock") AS "quantity",
        "tb_stocks"."item_id"
      FROM "tb_stocks"
      JOIN "tb_master_items" ON "tb_master_items"."id" = "tb_stocks"."item_id"
      GROUP BY "tb_stocks"."item_id", "tb_master_items"."minimum_quantity"
      HAVING (SELECT SUM("stock"."quantity") FROM "tb_stock_in_stores" "stock") <= "tb_master_items"."minimum_quantity"';

    $query = $this->db->query($sql);

    return $query->num_rows();
  }

  public function countReceipts($warehouse = NULL)
  {
    $this->db->from('tb_receipts');

    if ($warehouse === NULL){
      $this->db->where_in('warehouse', config_item('auth_warehouses'));
    } else {
      $this->db->where('warehouse', $warehouse);
    }

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIssuances($warehouse = NULL)
  {
    $this->db->from('tb_issuances');

    if ($warehouse === NULL){
      $this->db->where_in('warehouse', config_item('auth_warehouses'));
    } else {
      $this->db->where('warehouse', $warehouse);
    }

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function isDocReceiptNumberExists($number, $exception = NULL)
  {
    $this->db->from('tb_doc_receipts');
    $this->db->where('document_number', strtoupper($number));

    if ($exception !== NULL)
      $this->db->where('document_number != ', $exception);

    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? true : false;
  }

  public function isSerialNumberExists($item_serial, $status = NULL, $item_serial_exception = NULL)
  {
    $this->db->from('tb_master_item_serials');
    $this->db->where('item_serial', strtoupper($item_serial));

    if ($item_serial_exception !== NULL)
      $this->db->where('item_serial != ', $item_serial_exception);

    if ($status !== NULL)
      $this->db->where('status', $status);

    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? true : false;
  }

  public function isItemDescriptionExists($description, $description_exception = NULL)
  {
    $this->db->from(config_item('module')['item']['table']);
    $this->db->where('description', strtoupper($description));

    if ($description_exception !== NULL)
      $this->db->where('description != ', $description_exception);

    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? true : false;
  }

  public function isItemExists($part_number, $serial_number = NULL, $part_number_exception = NULL, $serial_number_exception = NULL)
  {
    $this->db->from(config_item('module')['item']['table']);
    $this->db->where('part_number', strtoupper($part_number));

    if ($serial_number !== NULL)
      $this->db->where('serial_number != ', $serial_number);

    if ($part_number_exception !== NULL)
      $this->db->where('part_number != ', $part_number_exception);

    if ($serial_number_exception !== NULL)
      $this->db->where('serial_number != ', $serial_number_exception);

    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? true : false;
  }

  public function isPartNumberExists($part_number, $serial_number = NULL, $part_number_exception = NULL, $serial_number_exception = NULL)
  {
    $this->db->from('tb_master_part_number');
    $this->db->where('part_number', strtoupper($part_number));

    // if ($serial_number !== NULL)
    //   $this->db->where('serial_number != ', $serial_number);

    if ($part_number_exception !== NULL)
      $this->db->where('part_number != ', $part_number_exception);

    // if ($serial_number_exception !== NULL)
    //   $this->db->where('serial_number != ', $serial_number_exception);

    $query = $this->db->get();

    return ($query->num_rows() > 0) ? true : false;
  }

  public function isItemCategoryExists($category, $status = NULL, $item_category_exception = NULL)
  {
    $this->db->from(config_item('module')['category']['table']);
    $this->db->where('category', strtoupper($category));

    if ($item_category_exception !== NULL)
      $this->db->where('category != ', $item_category_exception);

    if ($status !== NULL)
      $this->db->where('status', $status);

    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? true : false;
  }

  public function isItemCategoryCodeExists($code, $status = NULL, $code_exception = NULL)
  {
    $this->db->from(config_item('module')['category']['table']);
    $this->db->where('code', strtoupper($code));

    if ($code_exception !== NULL)
      $this->db->where('code != ', $code_exception);

    if ($status !== NULL)
      $this->db->where('status', $status);

    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? true : false;
  }

  public function isItemGroupExists($group, $status = NULL, $item_group_exception = NULL)
  {
    $this->db->from(config_item('module')['group']['table']);
    $this->db->where('group', strtoupper($group));

    if ($item_group_exception !== NULL)
      $this->db->where('group != ', $item_group_exception);

    if ($status !== NULL)
      $this->db->where('status', $status);

    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? true : false;
  }

  public function isItemGroupCodeExists($code, $status = NULL, $code_exception = NULL)
  {
    $this->db->from(config_item('module')['group']['table']);
    $this->db->where('code', strtoupper($code));

    if ($code_exception !== NULL)
      $this->db->where('code != ', $code_exception);

    if ($status !== NULL)
      $this->db->where('status', $status);

    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? true : false;
  }

  public function isItemGroupCoaExists($coa, $status = NULL, $code_exception = NULL)
  {
    $this->db->from(config_item('module')['group']['table']);
    $this->db->where('coa', $coa);

    if ($code_exception !== NULL)
      $this->db->where('coa != ', $code_exception);

    if ($status !== NULL)
      $this->db->where('status', $status);

    $query = $this->db->get();

    return ($query->num_rows() > 0) ? true : false;
  }

  public function isItemUnitExists($unit, $status = NULL, $unit_exception = NULL)
  {
    $this->db->from(config_item('module')['item_unit']['table']);
    $this->db->where('unit', strtoupper($unit));

    if ($unit_exception !== NULL)
      $this->db->where('unit != ', $unit_exception);

    if ($status !== NULL)
      $this->db->where('status', $status);

    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? true : false;
  }

  public function isVendorExists($vendor, $status = NULL, $except = NULL)
  {
    $this->db->from(config_item('module')['vendor']['table']);
    $this->db->where('vendor', strtoupper($vendor));

    if ($except !== NULL)
      $this->db->where('vendor != ', $except);

    if ($status !== NULL)
      $this->db->where('status', $status);

    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? true : false;
  }

  public function isWarehouseExists($warehouse, $status = NULL, $warehouse_exception = NULL)
  {
    $this->db->from(config_item('module')['warehouse']['table']);
    $this->db->where('warehouse', strtoupper($warehouse));

    if ($warehouse_exception !== NULL)
      $this->db->where('warehouse != ', $warehouse_exception);

    if ($status !== NULL)
      $this->db->where('status', $status);

    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? true : false;
  }

  public function isStoresExists($stores, $warehouse = NULL, $status = NULL, $stores_exception = NULL)
  {
    $this->db->from(config_item('module')['stores']['table']);
    $this->db->where('stores', strtoupper($stores));

    if ($warehouse !== NULL)
      $this->db->where('warehouse', strtoupper($warehouse));

    if ($stores_exception !== NULL)
      $this->db->where('stores != ', $stores_exception);

    if ($status !== NULL)
      $this->db->where('status', $status);

    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? true : false;
  }

  public function isUsernameExists($username, $username_exception = NULL)
  {
    $this->db->from(config_item('module')['user']['table']);
    $this->db->where('username', strtoupper($username));

    if ($username_exception !== NULL)
      $this->db->where('username != ', $username_exception);

    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? true : false;
  }

  public function isUserEmailExists($email, $email_exception = NULL)
  {
    $this->db->from(config_item('module')['user']['table']);
    $this->db->where('email', strtoupper($email));

    if ($email_exception !== NULL)
      $this->db->where('email != ', $email_exception);

    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? true : false;
  }

  public function isKodeAkuntingExists($kode, $kodeAkunting_exception = NULL)
  {
    $this->db->from(config_item('module')['akunting']['table']);
    $this->db->where('kode_akunting', $kode);

    if ($kodeAkunting_exception !== NULL)
      $this->db->where('kode_akunting != ', $kodeAkunting_exception);

    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? true : false;
  }

  public function isKursDateExists($date)
  {
    $this->db->from(config_item('module')['kurs']['table']);
    $this->db->where('date', $date);

    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? true : false;
  }

  public function today_kurs($date)
  {
    // $year = date('Y');

    $CI =& get_instance();

    $CI->db->select('kurs_dollar');
    $CI->db->from( 'tb_master_kurs_dollar' );
    $CI->db->where('date', $date);

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $kurs_dollar   = $row->kurs_dollar;
    

    return $kurs_dollar;
  }

  public function isPesawatExists($nama_pesawat)
  {
    $this->db->from(config_item('module')['pesawat']['table']);
    $this->db->where('nama_pesawat', strtoupper($nama_pesawat));

    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? true : false;
  }

  public function isDeliveryToExists($warehouse, $status = NULL, $warehouse_exception = NULL)
  {
    $this->db->from(config_item('module')['deliver']['table']);
    $this->db->where('warehouse', strtoupper($warehouse));

    if ($warehouse_exception !== NULL)
      $this->db->where('warehouse != ', $warehouse_exception);

    if ($status !== NULL)
      $this->db->where('status', $status);

    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? true : false;
  }

  

}
