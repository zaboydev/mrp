<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_Card_Model extends MY_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function getSelectedColumns()
  {
    return array(
      // 'tb_master_item_groups.category'        => NULL,
      'tb_stocks.id'                                 => 'No.',
      'tb_master_items.part_number'           => 'Part Number',
      'tb_master_items.serial_number'         => 'Serial Number',
      'tb_master_items.description'           => 'Description', 
      'tb_master_items.unit'                  => 'Unit',
      'tb_master_item_groups.category'        => 'Category',
      // 'tb_stock_in_stores.warehouse'          => 'Warehouse',
      // 'tb_stock_in_stores.stores'             => 'Stores',
      'tb_master_items.kode_stok'             => 'Kode Stok',
      'tb_master_item_groups.coa'             => 'COA',
      //'COUNT(tb_master_items.part_number) as pn'                 => 'ENTRY',
      //'tb_stock_cards.balance_quantity'       => 'Balance Quantity',
    );
  }

  public function getSearchableColumns()
  {
    return array(
      //'tb_stock_cards.created_by',
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.serial_number',
      //'tb_stock_cards.document_number',
      // 'tb_stock_cards.warehouse',
      // 'tb_stock_cards.stores',
      'tb_master_items.unit',      
      'tb_master_item_groups.category',
      // 'tb_stock_in_stores.warehouse',
      // 'tb_stock_in_stores.stores',
      //'tb_stock_cards.remarks'
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      //'tb_stock_cards.created_at',
      //'tb_stock_cards.created_by',
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.serial_number',
      //'tb_stocks.condition',
      //'tb_stock_cards.document_number',
      // 'tb_stock_cards.warehouse',
      // 'tb_stock_cards.stores',
      'tb_master_items.unit',
      'tb_master_item_groups.category',
      //'tb_stock_cards.prev_quantity',
      //'tb_stock_cards.quantity',
      //'tb_stock_cards.prev_quantity + tb_stock_cards.quantity',
      //'tb_stock_cards.remarks'
      // 'tb_stock_in_stores.warehouse',
      // 'tb_stock_in_stores.stores',
    );
  }

  public function getGroupedColumns()
  {
    return array(
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.serial_number',
      // 'tb_stock_cards.warehouse',
      // 'tb_stock_cards.stores',
      'tb_master_items.unit',
      'tb_master_item_groups.category',
      'tb_stocks.id',
      'tb_master_items.id',
      'tb_master_item_groups.id',      
      // 'tb_stock_cards.id',
      // 'tb_stock_in_stores.warehouse',
      // 'tb_stock_in_stores.stores',
      // 'tb_stock_in_stores.id',
     
    );
  }

  private function searchIndex()
  {
    // if (!empty($_POST['columns'][1]['search']['value'])){
    //   $search_created_at = $_POST['columns'][1]['search']['value'];
    //   $range_created_at  = explode(' ', $search_created_at);

    //   $this->db->where('DATE(tb_stock_cards.created_at) >= ', $range_created_at[0]);
    //   $this->db->where('DATE(tb_stock_cards.created_at) <= ', $range_created_at[1]);
    // }

    // if (!empty($_POST['columns'][3]['search']['value'])){
    //   $search_description = strtoupper($_POST['columns'][3]['search']['value']);

    //   $this->db->where('UPPER(tb_master_items.description)', $search_description);
    // }
    if (!empty($_POST['columns'][1]['search']['value'])){
      $search_issued_date = $_POST['columns'][1]['search']['value'];
      $range_issued_date  = explode(' ', $search_issued_date);

      $this->db->where('tb_stock_cards.date_of_entry >= ', $range_issued_date[0].' 00:00:00');
      $this->db->where('tb_stock_cards.date_of_entry <= ', $range_issued_date[1].' 00:00:00');
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
    // $this->db->from('tb_stock_cards');
    $this->db->from('tb_master_items');
    //$this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_cards.serial_id', 'left');
    // $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_stocks', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_items.group = tb_master_item_groups.group');
    // $this->db->where('tb_stocks.item_id', $item_id);
    $this->db->group_by($this->getGroupedColumns());

    $this->searchIndex();

    $IndexOrderColumns = $this->getOrderableColumns();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($IndexOrderColumns[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('tb_master_items.part_number', 'asc');
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
    //edit
    // $this->db->from('tb_stock_cards');
    // //$this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_cards.serial_id', 'left');
    // $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_cards.stock_id');
    // $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    // $this->db->join('tb_master_item_groups', 'tb_master_items.group = tb_master_item_groups.group');
    // // $this->db->where('tb_stocks.item_id', $item_id);
    //edit
    $this->db->from('tb_master_items');
    //$this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_cards.serial_id', 'left');
    // $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_stocks', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_items.group = tb_master_item_groups.group');
    $this->db->group_by($this->getGroupedColumns());

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    //edit
    // $this->db->from('tb_stock_cards');
    // //$this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_cards.serial_id', 'left');
    // $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_cards.stock_id');
    // $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    // $this->db->join('tb_master_item_groups', 'tb_master_items.group = tb_master_item_groups.group');
    // // $this->db->where('tb_stocks.item_id', $item_id);
    //edit

    $this->db->from('tb_master_items');
    //$this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_cards.serial_id', 'left');
    // $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_stocks', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_items.group = tb_master_item_groups.group');

    $this->db->group_by($this->getGroupedColumns());

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function getDetailSelectedColumns($stock_id)
  {
    return array(
      NULL                                    => 'No',
      'tb_stock_cards.date_of_entry'          => 'Date',
      'tb_master_items.serial_number'         => 'Serial Number',
      'tb_stock_cards.document_number'        => 'Ref. Document',
      'tb_stock_cards.stores'                 => 'Stores',
      // 'tb_stock_cards.prev_quantity'          => 'Prev. Qty',
      '(CASE WHEN tb_stock_cards.quantity > 0 THEN tb_stock_cards.quantity ELSE 0 END) AS in_qty'                 => 'In Qty',
      '(CASE WHEN tb_stock_cards.quantity < 0 THEN tb_stock_cards.quantity ELSE 0 END) AS out_qty'                 => 'Out Qty',
      'tb_stock_cards.unit_value'          => 'Price',
      // '(tb_stock_cards.prev_quantity+tb_stock_cards.quantity) as balance_quantity'       => 'Balance',
      // 'tb_stock_cards.balance_quantity'         => 'Balance',
      // null => 'Total',
      'tb_stock_cards.issued_to'                => 'Total Price',
      'tb_stock_cards.total_value'                => 'Balance',

    );
  }

  public function getDetailSearchableColumns()
  {
    return array(
      // 'tb_stock_cards.created_at'             => 'Date',
      'tb_master_items.serial_number',
      'tb_stock_cards.document_number',
      'tb_stock_cards.stores',
      // 'tb_stock_cards.prev_quantity',
      // '(CASE WHEN tb_stock_cards.quantity > 0 THEN tb_stock_cards.quantity ELSE 0 END) AS in_qty'                => 'In Qty',
      // '(CASE WHEN tb_stock_cards.quantity < 0 THEN tb_stock_cards.quantity ELSE 0 END) AS out_qty'               => 'Out Qty',
      // 'tb_stock_cards.balance_quantity',
      'tb_stock_cards.remarks',
    );
  }

  public function getDetailOrderableColumns()
  {
    return array(
      null,
      'tb_stock_cards.created_at',
      'tb_master_items.serial_number',
      'tb_stock_cards.document_number',
      'tb_stock_cards.stores',
      'tb_stock_cards.prev_quantity',
      // '(CASE WHEN tb_stock_cards.quantity > 0 THEN tb_stock_cards.quantity ELSE 0 END) AS in_qty'                => 'In Qty',
      // '(CASE WHEN tb_stock_cards.quantity < 0 THEN tb_stock_cards.quantity ELSE 0 END) AS out_qty'               => 'Out Qty',
      'tb_stock_cards.balance_quantity',
      // 'tb_stock_cards.issued_to',
      'tb_stock_cards.total_value',
    );
  }

  private function searchDetailIndex()
  {
    if (!empty($_POST['columns'][1]['search']['value'])){
      $stores = $_POST['columns'][1]['search']['value'];
      // $range_issued_date  = explode(' ', $search_issued_date);

      $this->db->where('tb_stock_cards.stores', $stores);
    }

    if (!empty($_POST['columns'][2]['search']['value'])){
      $search_received_date = $_POST['columns'][2]['search']['value'];
      $range_received_date  = explode(' ', $search_received_date);

      $this->db->where('tb_stock_cards.date_of_entry >= ', $range_received_date[0]);
      $this->db->where('tb_stock_cards.date_of_entry <= ', $range_received_date[1]);
    }

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

  //public function getDetailIndex($part_number=NULL, $description=NULL, $unit=NULL, $category=NULL,$base=NULL,$stores=NULL,$return = 'array')
  public function getDetailIndex($stock_id, $return = 'array')
  {
    $this->db->select(array_keys($this->getDetailSelectedColumns($stock_id)));
    $this->db->from('tb_stock_cards');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_cards.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_items.group = tb_master_item_groups.group');
    $this->db->where('tb_stock_cards.stock_id', $stock_id);

    // if ($category !== NULL){
    //   $this->db->where('tb_master_item_groups.category', $category);
    // }

    // if ($base !== NULL){
    //   $this->db->where('tb_stock_cards.warehouse', $base);
    // }

    // if ($part_number !== NULL){
    //   $this->db->where('tb_master_items.part_number', $part_number);
    // }

    // if ($serial_number !== NULL && $serial_number != '-'){
    //   $this->db->where('tb_master_items.serial_number', $serial_number);
    // }

    // if ($description !== NULL){
    //   $this->db->where('tb_master_items.description', $description);
    // }

    // if ($unit !== NULL){
    //   $this->db->where('tb_master_items.unit', $unit);
    // }
    // if ($stores !== NULL){
    //   $this->db->where('tb_stock_cards.stores', $stores);
    // }

    $this->searchDetailIndex();

    $IndexOrderColumns = $this->getDetailOrderableColumns();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($IndexOrderColumns[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('tb_stock_cards.id', 'asc');
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

  public function countDetailIndexFiltered($stock_id)
  {
    $this->db->from('tb_stock_cards');
    //$this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_cards.serial_id', 'left');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_cards.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_items.group = tb_master_item_groups.group');
    $this->db->where('tb_stock_cards.stock_id', $stock_id);
    // $this->db->where('tb_stocks.item_id', $item_id);
    //$this->db->group_by($this->getGroupedColumns());

    // if ($category !== NULL){
    //   $this->db->where('tb_master_item_groups.category', $category);
    // }

    // if ($base !== NULL){
    //   $this->db->where('tb_stock_cards.warehouse', $base);
    // }

    // if ($serial_number !== NULL && $serial_number != '-'){
    //   $this->db->where('tb_master_items.serial_number', $serial_number);
    // }
    
    // if ($part_number !== NULL){
    //   $this->db->where('tb_master_items.part_number', $part_number);
    // }
    // if ($description !== NULL){
    //   $this->db->where('tb_master_items.description', $description);
    // }
    // if ($unit !== NULL){
    //   $this->db->where('tb_master_items.unit', $unit);
    // }
    // if ($stores !== NULL){
    //   $this->db->where('tb_stock_cards.stores', $stores);
    // }

    $this->searchDetailIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countDetail($stock_id)
  {
    $this->db->from('tb_stock_cards');
    //$this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_cards.serial_id', 'left');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_cards.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_items.group = tb_master_item_groups.group');
    $this->db->where('tb_stock_cards.stock_id', $stock_id);
    //$this->db->group_by($this->getGroupedColumns());

    // if ($category !== NULL){
    //   $this->db->where('tb_master_item_groups.category', $category);
    // } else {
    //   $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));
    // }

    // if ($base !== NULL){
    //   $this->db->where('tb_stock_cards.warehouse', $base);
    // }

    // if ($serial_number !== NULL && $serial_number != '-'){
    //   $this->db->where('tb_master_items.serial_number', $serial_number);
    // }

    // if ($part_number !== NULL){
    //   $this->db->where('tb_master_items.part_number', $part_number);
    // }
    // if ($description !== NULL){
    //   $this->db->where('tb_master_items.description', $description);
    // }
    // if ($unit !== NULL){
    //   $this->db->where('tb_master_items.unit', $unit);
    // }

    // if ($stores !== NULL){
    //   $this->db->where('tb_stock_cards.stores', $stores);
    // }

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function getItem($stock_id)
  {
    // $this->db->where('id', $id);

    // $query    = $this->db->get('tb_receipts');
    // $receipt = $query->unbuffered_row('array');

    $select = array(
      // 'tb_stock_in_stores.*',
      'tb_stocks.condition',
      // 'tb_stock_in_stores.stores',
      'tb_master_items.serial_number',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.alternate_part_number',
      'tb_master_items.unit',
      'tb_master_items.group',
      // 'tb_master_items.minimum_quantity',
      // 'tb_receipt_items.id as receipt_items_id',//tambahan
      // 'tb_receipt_items.received_quantity',
      // 'tb_receipt_items.received_unit_value',
      // 'tb_receipt_items.received_unit_value_dollar',
      // 'tb_receipt_items.purchase_order_number',
      // 'tb_receipt_items.reference_number',
      // 'tb_receipt_items.awb_number',
      // 'tb_receipt_items.remarks',
      // 'tb_receipt_items.kode_akunting',
      //tambahan
      // 'tb_master_items.unit_pakai',
      'tb_master_items.kode_stok',
      // 'tb_master_items.qty_konversi',
      // 'tb_receipt_items.received_unit',
      // 'tb_receipt_items.stock_in_stores_id',
    );

    $this->db->select($select);
    $this->db->from('tb_master_items');
    // $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_receipt_items.stock_in_stores_id');
    // $this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_in_stores.serial_id', 'left');
    $this->db->join('tb_stocks', 'tb_stocks.item_id = tb_master_items.id');
    // $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');   
    // $this->db->join('tb_master_item_konversi', 'tb_master_item_konversi.item_id = tb_master_items.id');//tambahan
    $this->db->where('tb_stocks.id', $stock_id);

    $query = $this->db->get();

    // foreach ($query->result_array() as $key => $value){
    //   $receipt['items'][$key] = $value;

    //   if (empty($receipt['category'])){
    //     $this->db->select('category');
    //     $this->db->from('tb_master_item_groups');
    //     $this->db->where('group', $value['group']);

    //     $query = $this->db->get();
    //     $icat  = $query->unbuffered_row();

    //     $receipt['category'] = $icat->category;
    //   }
    // }

    return $query->unbuffered_row('array');
  }
}
