<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Konsolidasi_Model extends MY_Model
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = config_item('module')['kurs'];
  }

  public function getSelectedColumns()
  {
    return array(
      null => null,
      'tb_master_items.group'              => 'Group',
      // 'tb_master_item_categories.category'              => 'Category',
      // 'SUM(CASE WHEN tb_receipts.document_number like GRN THEN tb_receipt_items.received_total_value_dollar ELSE 0 END) AS grn_usd'                                  => 'GRN (USD)',
      // 'SUM(CASE WHEN tb_receipts.document_number like GRN THEN tb_receipt_items.received_total_value ELSE 0 END) AS grn_rp'                                  => 'GRN (Rp)',
      'SUM(tb_receipt_items.received_total_value_dollar) AS grn_usd'                                  => 'GRN (USD)',
      'SUM(tb_receipt_items.received_total_value) AS grn_rp'                                  => 'GRN (Rp)',
      'SUM(tb_issuance_items.issued_total_value) AS ms_usd'                                  => 'MS (USD)',
      'SUM(tb_issuance_items.issued_total_value) AS ms_rp'                                  => 'MS (Rp)',
    );
  }

  public function getSearchableColumns()
  {
    return array(
      // 'tb_master_item_categories.category',
      // 'tb_receipts.category',
      'tb_master_item_groups.group',
    );
  }

  public function getGroupedColumns()
  {
    return array(
      // 'tb_master_item_categories.category',
      // 'tb_receipts.category',
      // 'tb_master_item_groups.group',
      'tb_master_items.group',
    );
  }

  public function getOrderableColumns()
  {
    return array(
      // 'tb_master_item_categories.category',
      // 'tb_receipts.category',
      'tb_master_item_groups.group',
    );
  }

  private function searchIndex()
  {
    $i = 0;

    foreach ($this->getSearchableColumns() as $item){
      if ($_POST['search']['value']){
        if ($i === 0){
          $this->db->group_start();
          $this->db->like('UPPER('.$item.')', strtoupper($_POST['search']['value']));
        } else {
          $this->db->or_like('UPPER('.$item.')', strtoupper($_POST['search']['value']));
        }

        if (count($this->getSearchableColumns()) - 1 == $i){
          $this->db->group_end();
        }
      }

      $i++;
    }
  }

  function getIndex($return = 'array')
  {
    $start_date  = date('Y-m-d');
    $date        = strtotime('-1 day',strtotime($start_date));
    $end_date    = date('Y-m-d', $date);

    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_master_items');  
    // $this->db->from('tb_master_item_categories');  
    // $this->db->join('tb_master_item_categories', 'tb_master_item_categories.category = tb_master_item_groups.category');
    // $this->db->join('tb_receipts','tb_master_item_groups.category = tb_receipts.category');
    // $this->db->join('tb_master_items','tb_master_items.group = tb_master_item_groups.group');
    $this->db->join('tb_stocks','tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_stock_in_stores','tb_stock_in_stores.stock_id = tb_stocks.id');
    $this->db->join('tb_receipt_items','tb_receipt_items.stock_in_stores_id = tb_stock_in_stores.id');

    // $this->db->join('tb_issuances','tb_master_item_groups.category = tb_issuances.category');
    // $this->db->join('tb_issuance_items','tb_issuance_items.document_number = tb_issuances.document_number');
    $this->db->join('tb_issuance_items','tb_issuance_items.stock_in_stores_id = tb_stock_in_stores.id');
    $this->db->where('tb_issuance_items.issued_date_item', $start_date);
    // $this->db->where('tb_issuances.issued_date', $start_date);
    $this->db->where('tb_receipt_items.received_date_item', $start_date);
    $this->db->group_by($this->getGroupedColumns());

    $this->searchIndex();

    $column_order = $this->getOrderableColumns();

    // if (isset($_POST['order'])){
    //   foreach ($_POST['order'] as $key => $order){
    //     $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
    //   }
    // } else {
      // $this->db->order_by('tb_receipts.category','desc');
    // }

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
    $start_date  = date('Y-m-d');
    $date        = strtotime('-1 day',strtotime($start_date));
    $end_date    = date('Y-m-d', $date);

    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_master_items');  
    // $this->db->from('tb_master_item_categories');  
    // $this->db->join('tb_master_item_categories', 'tb_master_item_categories.category = tb_master_item_groups.category');
    // $this->db->join('tb_receipts','tb_master_item_groups.category = tb_receipts.category');
    // $this->db->join('tb_master_items','tb_master_items.group = tb_master_item_groups.group');
    $this->db->join('tb_stocks','tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_stock_in_stores','tb_stock_in_stores.stock_id = tb_stocks.id');
    $this->db->join('tb_receipt_items','tb_receipt_items.stock_in_stores_id = tb_stock_in_stores.id');

    // $this->db->join('tb_issuances','tb_master_item_groups.category = tb_issuances.category');
    // $this->db->join('tb_issuance_items','tb_issuance_items.document_number = tb_issuances.document_number');
    $this->db->join('tb_issuance_items','tb_issuance_items.stock_in_stores_id = tb_stock_in_stores.id');
    $this->db->where('tb_issuance_items.issued_date_item', $start_date);
    // $this->db->where('tb_issuances.issued_date', $start_date);
    $this->db->where('tb_receipt_items.received_date_item', $start_date);
    $this->db->group_by($this->getGroupedColumns());
    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $start_date  = date('Y-m-d');
    $date        = strtotime('-1 day',strtotime($start_date));
    $end_date    = date('Y-m-d', $date);

    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_master_items');  
    // $this->db->from('tb_master_item_categories');  
    // $this->db->join('tb_master_item_categories', 'tb_master_item_categories.category = tb_master_item_groups.category');
    // $this->db->join('tb_receipts','tb_master_item_groups.category = tb_receipts.category');
    // $this->db->join('tb_master_items','tb_master_items.group = tb_master_item_groups.group');
    $this->db->join('tb_stocks','tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_stock_in_stores','tb_stock_in_stores.stock_id = tb_stocks.id');
    $this->db->join('tb_receipt_items','tb_receipt_items.stock_in_stores_id = tb_stock_in_stores.id');

    // $this->db->join('tb_issuances','tb_master_item_groups.category = tb_issuances.category');
    // $this->db->join('tb_issuance_items','tb_issuance_items.document_number = tb_issuances.document_number');
    $this->db->join('tb_issuance_items','tb_issuance_items.stock_in_stores_id = tb_stock_in_stores.id');
    $this->db->where('tb_issuance_items.issued_date_item', $start_date);
    // $this->db->where('tb_issuances.issued_date', $start_date);
    $this->db->where('tb_receipt_items.received_date_item', $start_date);
    $this->db->group_by($this->getGroupedColumns());

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function findById($id)
  {
    $this->db->where('id', $id);
    $query = $this->db->get('tb_master_kurs_dollar');

    return $query->row_array();
  }

  public function insert()
  {
    $this->db->trans_begin();

    $this->db->set('kurs_dollar', strtoupper($this->input->post('kurs_dollar')));
    $this->db->set('date', strtoupper($this->input->post('tanggal')));
    $this->db->set('status', 'ACTIVE');
    $this->db->set('created_by', config_item('auth_username'));
    // $this->db->set('updated_by', config_item('auth_username'));
    $this->db->set('created_at', date('Y-m-d H:i:s'));
    // $this->db->set('updated_at', date('Y-m-d H:i:s'));
    $this->db->insert('tb_master_kurs_dollar');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function update($id)
  {
    $this->db->trans_begin();

    $this->db->set('kurs_dollar', strtoupper($this->input->post('kurs_dollar')));
    $this->db->set('update_at', date('Y-m-d H:i:s'));
    $this->db->set('update_by', config_item('auth_username'));
    $this->db->where('id', $id);
    $this->db->update('tb_master_kurs_dollar');

    $this->db->select('*');
    $this->db->from('tb_stock_in_stores');
    $this->db->where('kurs_dollar !=', '1');
    $this->db->where('received_date', strtoupper($this->input->post('date')));
    $query            = $this->db->get();
    $stock_in_stores  = $query->result_array();

    $kurs_baru = floatval($this->input->post('kurs_dollar'));

    foreach ($stock_in_stores as $stock_detail) {
      $unit_value_dollar_1 = $stock_detail['unit_value_dollar'];
      $this->db->set('kurs_dollar', strtoupper($this->input->post('kurs_dollar')));
      $this->db->set('unit_value', floatval($unit_value_dollar_1)*floatval($kurs_baru));
      $this->db->where('id', $stock_detail['id']);
      $this->db->update('tb_stock_in_stores'); 
    }

    //update tb_receipts items
    $this->db->select('id,received_unit_value_dollar,received_total_value_dollar,received_quantity');
    $this->db->from('tb_receipt_items');
    $this->db->where('kurs_dollar !=', '1');
    $this->db->where('received_date_item', strtoupper($this->input->post('date')));
    $query            = $this->db->get();
    $tb_receipt_items  = $query->result_array();

    // $kurs_baru = floatval($this->input->post('kurs_dollar'));

    foreach ($tb_receipt_items as $ri) {
      $unit_value         = floatval($ri['received_unit_value_dollar'])*floatval($kurs_baru);
      $total_unit_value   = floatval($unit_value)*floatval($ri['received_quantity']);
      $this->db->set('kurs_dollar', floatval($this->input->post('kurs_dollar')));
      $this->db->set('received_unit_value', floatval($unit_value));
      $this->db->set('received_total_value', floatval($total_unit_value));
      $this->db->where('id', $ri['id']);
      $this->db->update('tb_receipt_items'); 
    }

    // $this->db->set('kurs_dollar', strtoupper($this->input->post('kurs_dollar')));
    // $this->db->where('kurs_dollar !=', '1');
    // $this->db->where('received_date_item', strtoupper($this->input->post('date')));
    // $this->db->update('tb_receipt_items'); 


    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }
}
