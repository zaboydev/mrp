<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Expired_Stock_Model extends MY_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function getSelectedColumns()
  {
    return array(
      'tb_stocks.id'                                  => NULL,
      'tb_master_items.part_number'                   => 'Part Number',
      'tb_master_items.description'                   => 'Description',
      'tb_master_items.serial_number'                 => 'Serial Number',
      'tb_stock_in_stores.expired_date'               => 'Expired Date',
      'tb_master_item_groups.category'                => 'Category',
      'tb_master_item_groups.group'                   => 'Group',
      'tb_stocks.condition'                           => 'Condition',
      // 'SUM(tb_stock_in_stores.quantity) - (SUM(tb_receipt_items.received_quantity) - SUM(tb_issuance_items.issued_quantity) + SUM(tb_stock_adjustments.adjustment_quantity)) as initial_quantity' => 'Initial Qty',
      // 'SUM(tb_receipt_items.received_quantity) as received_quantity' => 'Received Qty',
      // 'SUM(tb_issuance_items.issued_quantity) as issued_quantity' => 'Issued Qty',
      // 'SUM(tb_stock_adjustments.adjustment_quantity) as adjustment_quantity' => 'Adjusment Qty',
      'SUM(tb_stock_in_stores.quantity) as quantity'  => 'Stock Quantity',
      'tb_stock_in_stores.unit_value'                 => 'Price',
      'tb_master_items.minimum_quantity'              => 'Min. Stock',
      'tb_master_items.unit'                          => 'Unit',
      'tb_master_item_groups.coa'                     => 'COA',
      'tb_master_items.kode_stok'                     => 'Kode Stok',
      'tb_stock_in_stores.stores'                     => 'Stores',
      'tb_stock_in_stores.warehouse'                  => 'Base',
      'tb_stock_in_stores.remarks'                    => 'Remarks',
      'tb_stock_in_stores.reference_document'         => 'No Document',
      
    );
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
      'tb_stock_in_stores.unit_value', 
      'tb_stocks.condition',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit',
      'tb_stock_in_stores.stores',
      'tb_stock_in_stores.warehouse',
      'tb_stock_in_stores.remarks',
      'tb_stock_in_stores.expired_date',
      'tb_stock_in_stores.reference_document',
      'tb_master_item_groups.coa',
      'tb_master_items.kode_stok',
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.serial_number',
      'tb_master_item_groups.category',
      'tb_master_item_groups.group',
      // 'tb_stock_in_stores.unit_value' ,
      'tb_master_items.unit',
      'tb_stock_in_stores.stores',
      'tb_stock_in_stores.warehouse',
      'tb_stock_in_stores.remarks',
      'tb_stock_in_stores.reference_document',
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
      'tb_master_item_groups.category',
      'tb_master_item_groups.group',
      'tb_stocks.condition',
      'tb_stock_in_stores.unit_value' ,
      
      'SUM(tb_stock_in_stores.quantity)',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit',
      'tb_stock_in_stores.stores',
      'tb_stock_in_stores.warehouse',
      'tb_stock_in_stores.remarks' ,
      'tb_stock_in_stores.expired_date',
      'tb_stock_in_stores.reference_document',
    );
  }

  private function searchIndex()
  {
    if (!empty($_POST['columns'][1]['search']['value'])) {
      $search_received_date = $_POST['columns'][1]['search']['value'];
      $range_received_date  = explode(' ', $search_received_date);
      // $this->db->where('tb_receipts.received_date >= ', $range_received_date[0]);
      $this->db->where('tb_stock_in_stores.expired_date <= ', $range_received_date[1]);
    }else{
      $start_date  = date('Y-m-d');
      $date        = strtotime('+90 day',strtotime($start_date));
      $end_date    = date('Y-m-d', $date);
      $this->db->where('tb_stock_in_stores.expired_date <= ', $end_date);
    }

    if (!empty($_POST['columns'][3]['search']['value'])) {
      $search_category = $_POST['columns'][3]['search']['value'];

      $this->db->where('tb_master_item_groups.category', $search_category);
    }

    if (!empty($_POST['columns'][2]['search']['value'])) {
      $search_warehouse = $_POST['columns'][2]['search']['value'];

      $this->db->where('tb_stock_in_stores.warehouse', $search_warehouse);
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
    $this->db->from('tb_stock_in_stores');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    // $this->db->where('tb_stock_in_stores.expired_date >= ', $start_date);
    // $this->db->where('tb_stock_in_stores.expired_date <= ', $end_date);
    // $this->db->where('tb_stock_in_stores.expired_date >= ', $start_date);
    // $this->db->where('condition', $condition);
    $this->db->where('tb_stock_in_stores.quantity > 0');//tambahan untuk poin no 15 relokasi

    // if ($category !== NULL){
    //   $this->db->where('tb_master_item_groups.category', $category);
    // }

    // if ($warehouse !== NULL){
    //   if($warehouse == 'WISNU'){
    //     $this->db->group_start()
    //               ->like('tb_stock_in_stores.warehouse', 'WISNU')
    //               // ->or_where('tb_stock_in_stores_reports.warehouse=', 'WISNU REKONDISI')
    //               ->group_end();
    //   }
    //   if($warehouse == "all base rekondisi"){
    //     $this->db->group_start()
    //               ->like('tb_stock_in_stores.warehouse', 'REKONDISI')
    //               ->group_end();
    //   }
    //   if($warehouse == 'LOMBOK'){
    //     $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
    //   }
    //    if($warehouse == 'JEMBER'){
    //     $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
    //   }
    //    if($warehouse == 'SOLO'){
    //     $this->db->where('tb_stock_in_stores_.warehouse', $warehouse);
    //   }
    //    if($warehouse == 'PALANGKARAYA'){
    //     $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
    //   }
    //    if($warehouse == 'BSR REKONDISI'){
    //     $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
    //   }
    //    if($warehouse == 'BANYUWANGI'){
    //     $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
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
      $this->db->order_by('tb_stock_in_stores.reference_document', 'asc');
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
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_stock_in_stores');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    // $this->db->where('tb_stock_in_stores.received_date >= ', $start_date);
    // $this->db->where('tb_stock_in_stores.received_date <= ', $end_date);
    // $this->db->where('tb_stock_in_stores.expired_date <= ', $start_date);
    // $this->db->where('tb_stock_in_stores.expired_date >= ', $start_date);
    // $this->db->where('tb_stock_in_stores.expired_date <= ', $start_date);
    $this->db->where('tb_stock_in_stores.quantity > 0');//tambahan untuk poin no 15 relokasi
    // $this->db->where('condition', $condition);

    // if ($category !== NULL){
    //   $this->db->where('tb_master_item_groups.category', $category);
    // }

    // // if ($start_date != '-' && $end_date !='-'){
    // //   $this->db->where('tb_stock_in_stores.expired_date >= ', $start_date);
    // //   $this->db->where('tb_stock_in_stores.expired_date <= ', $end_date);
    // // }else{
    // //   // $this->db->where('tb_stock_in_stores.expired_date');
    // // }

    // if ($warehouse !== NULL){
    //   if($warehouse == 'WISNU'){
    //     $this->db->group_start()
    //               ->like('tb_stock_in_stores.warehouse', 'WISNU')
    //               // ->or_where('tb_stock_in_stores_reports.warehouse=', 'WISNU REKONDISI')
    //               ->group_end();
    //   }
    //   if($warehouse == "all base rekondisi"){
    //     $this->db->group_start()
    //               ->like('tb_stock_in_stores.warehouse', 'REKONDISI')
    //               ->group_end();
    //   }
    //   if($warehouse == 'LOMBOK'){
    //     $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
    //   }
    //    if($warehouse == 'JEMBER'){
    //     $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
    //   }
    //    if($warehouse == 'SOLO'){
    //     $this->db->where('tb_stock_in_stores_.warehouse', $warehouse);
    //   }
    //    if($warehouse == 'PALANGKARAYA'){
    //     $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
    //   }
    //    if($warehouse == 'BSR REKONDISI'){
    //     $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
    //   }
    //    if($warehouse == 'BANYUWANGI'){
    //     $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
    //   }      
    // }

    $this->db->group_by($this->getGroupedColumns());
    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_stock_in_stores');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    // $this->db->where('tb_stock_in_stores.received_date >= ', $start_date);
    // $this->db->where('tb_stock_in_stores.received_date <= ', $end_date);
    // $this->db->where('tb_stock_in_stores.expired_date <= ', $start_date);
    // $this->db->where('tb_stock_in_stores.expired_date >= ', $start_date);
    // $this->db->where('tb_stock_in_stores.expired_date <= ', $start_date);
    $this->db->where('tb_stock_in_stores.quantity > 0');//tambahan untuk poin no 15 relokasi
    // $this->db->where('condition', $condition);

    // if ($category !== NULL){
    //   $this->db->where('tb_master_item_groups.category', $category);
    // }

    // // if ($start_date != '-' && $end_date !='-'){
    // //   $this->db->where('tb_stock_in_stores.expired_date >= ', $start_date);
    // //   $this->db->where('tb_stock_in_stores.expired_date <= ', $end_date);
    // // }

    // if ($warehouse !== NULL){
    //   if($warehouse == 'WISNU'){
    //     $this->db->group_start()
    //               ->like('tb_stock_in_stores.warehouse', 'WISNU')
    //               // ->or_where('tb_stock_in_stores_reports.warehouse=', 'WISNU REKONDISI')
    //               ->group_end();
    //   }
    //   if($warehouse == "all base rekondisi"){
    //     $this->db->group_start()
    //               ->like('tb_stock_in_stores.warehouse', 'REKONDISI')
    //               ->group_end();
    //   }
    //   if($warehouse == 'LOMBOK'){
    //     $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
    //   }
    //    if($warehouse == 'JEMBER'){
    //     $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
    //   }
    //    if($warehouse == 'SOLO'){
    //     $this->db->where('tb_stock_in_stores_.warehouse', $warehouse);
    //   }
    //    if($warehouse == 'PALANGKARAYA'){
    //     $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
    //   }
    //    if($warehouse == 'BSR REKONDISI'){
    //     $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
    //   }
    //    if($warehouse == 'BANYUWANGI'){
    //     $this->db->where('tb_stock_in_stores.warehouse', $warehouse);
    //   }      
    // }

    $this->db->group_by($this->getGroupedColumns());

    $query = $this->db->get();

    return $query->num_rows();
  }

  
}
