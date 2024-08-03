<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Po_Grn_Model extends MY_Model
{
  protected $connection;
  protected $budget_year;
  protected $budget_month;

  public function __construct()
  {
    parent::__construct();

    $this->connection   = $this->load->database('budgetcontrol', TRUE);
  }
  public function loadBase(){
    return $this->db->get('tb_master_warehouses')->result();
  }
  public function getSelectedColumns()
  {
    return array(
      'tb_po_item.id' => NULL,
      'tb_po.document_number as "po_number"'                => 'PO Number',
      'tb_po.vendor'                                        => 'Vendor',
      'tb_po_item.part_number'                              => 'Part Number',
      'tb_po_item.serial_number'                              => 'Serial Number',
      'tb_po_item.description'                              => 'Description',
      'tb_po.default_currency'                              => 'Currency',
      'tb_po_item.quantity as "po_qty"'                     => 'Qty Order',
      'tb_po_item.total_amount as "po_val"'                 => 'Value Order',
      'sum(case when tb_receipt_items.quantity_order is null then 0.00 else tb_receipt_items.quantity_order end) as "grn_qty"'                     => 'Qty Receipts',
      'tb_po_item.unit_price'             => 'Val Receipts',
      'tb_po_item.left_received_quantity as "grn_val_usd"'             => 'Qty Order Remaining',
    );
      
  }
  public function getSearchableColumns()
  {
    return array(
      'tb_po.document_number',
      'tb_po_item.part_number',
      'tb_po_item.description',
      'tb_po.default_currency',
      'tb_po.vendor',
    );
  }

  public function getGroupedColumns()
  {
    return array(
      'tb_po_item.id',
      'tb_po.document_number',
      'tb_po.vendor',
      'tb_po_item.part_number',
      'tb_po_item.description',
      'tb_po.default_currency',
      'tb_po_item.quantity',
      'tb_po_item.total_amount',
      // 'tb_po_item.quantity_received',
      'tb_po_item.unit_price',
      'tb_po_item.left_received_quantity',
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'tb_po.document_number',
      'tb_po_item.part_number',
      'tb_po_item.description',
      'tb_po.default_currency',
      'tb_po.vendor',
    );
  }
 
  private function searchIndex()
  {
    if (!empty($_POST['columns'][1]['search']['value'])){
      $vendor = $_POST['columns'][1]['search']['value'];

      $this->db->where('tb_po.vendor', $vendor);
    }

    if (!empty($_POST['columns'][2]['search']['value'])) {
      $search_received_date = $_POST['columns'][2]['search']['value'];
      $range_received_date  = explode(' ', $search_received_date);

      $this->db->where('tb_po.document_date >= ', $range_received_date[0]);
      $this->db->where('tb_po.document_date <= ', $range_received_date[1]);
    }

    if (!empty($_POST['columns'][3]['search']['value'])) {
      $type = $_POST['columns'][3]['search']['value'];
      if ($type != 'all' && $type != null) {
        if($type=='POM'){
          $this->db->where('tb_po.tipe_po', 'INVENTORY MRP');
        } else if($type=='POL'){
          $this->db->where_in('tb_po.tipe_po', ['INVENTORY','CAPEX','EXPENSE']);
        }        
      }
    }else{
      $this->db->where('tb_po.tipe_po', 'INVENTORY MRP');
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

  function getIndex($return = 'array')
  {
    
    $this->db->select(array_keys($this->getSelectedColumns()),false);
    $this->db->from('tb_po_item');
    $this->db->join('tb_po ', 'tb_po_item.purchase_order_id = tb_po.id');
    $this->db->join('tb_receipt_items', 'tb_receipt_items.purchase_order_item_id = tb_po_item.id','left');
    $this->db->join('tb_receipts', 'tb_receipt_items.document_number= tb_receipts.document_number','left');
    $this->db->where_in('tb_po.status',['ORDER','OPEN','CLOSE','ADVANCE']);
    $this->db->group_by($this->getGroupedColumns());
    $this->searchIndex();

    $column_order = $this->getOrderableColumns();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
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

  function countIndexFiltered()
  {
    $this->db->select(array_keys($this->getSelectedColumns()),false);
    $this->db->from('tb_po_item');
    $this->db->join('tb_po ', 'tb_po_item.purchase_order_id = tb_po.id');
    $this->db->join('tb_receipt_items', 'tb_receipt_items.purchase_order_item_id = tb_po_item.id','left');
    $this->db->join('tb_receipts', 'tb_receipt_items.document_number= tb_receipts.document_number','left');
    $this->db->where_in('tb_po.status',['ORDER','OPEN', 'CLOSE', 'ADVANCE']);
    $this->db->group_by($this->getGroupedColumns());
    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }
  
  public function countIndex()
  {
    $this->db->select(array_keys($this->getSelectedColumns()),false);
    $this->db->from('tb_po_item');
    $this->db->join('tb_po ', 'tb_po_item.purchase_order_id = tb_po.id');
    $this->db->join('tb_receipt_items', 'tb_receipt_items.purchase_order_item_id = tb_po_item.id','left');
    $this->db->join('tb_receipts', 'tb_receipt_items.document_number= tb_receipts.document_number','left');
    $this->db->where_in('tb_po.status',['ORDER','OPEN', 'CLOSE', 'ADVANCE']);
    $this->db->group_by($this->getGroupedColumns());
    $query = $this->db->get();

    return $query->num_rows();
  }

  public function findById($id)
  {
    $this->db->where('id', $id);

    $query  = $this->db->get('tb_purchase_orders');
    $poe    = $query->unbuffered_row('array');

    $this->db->from('tb_purchase_order_vendors');
    $this->db->where('tb_purchase_order_vendors.purchase_order_id', $id);

    $query = $this->db->get();

    foreach ($query->result_array() as $key => $vendor){
      $poe['vendors'][$key]['vendor'] = $vendor['vendor'];
      $poe['vendors'][$key]['is_selected'] = $vendor['is_selected'];
    }

    $this->db->from('tb_purchase_order_items');
    $this->db->where('tb_purchase_order_items.purchase_order_id', $id);

    $query = $this->db->get();

    foreach ($query->result_array() as $i => $item){
      $poe['request'][$i] = $item;
      $poe['request'][$i]['vendors'] = array();

      $selected_detail = array(
        'tb_purchase_order_items_vendors.*',
        'tb_purchase_order_vendors.vendor',
      );

      $this->db->select($selected_detail);
      $this->db->from('tb_purchase_order_items_vendors');
      $this->db->join('tb_purchase_order_vendors', 'tb_purchase_order_vendors.id = tb_purchase_order_items_vendors.purchase_order_vendor_id');
      $this->db->where('tb_purchase_order_items_vendors.purchase_order_item_id', $item['id']);

      $query = $this->db->get();

      foreach ($query->result_array() as $d => $detail) {
        $poe['request'][$i]['vendors'][$d] = $detail;
      }
    }

    return $poe;
  }

  
}
