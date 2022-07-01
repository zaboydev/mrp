<?php defined('BASEPATH') or exit('No direct script access allowed');

class Material_Slip_Model extends MY_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function getSelectedColumns()
  {
    $selected = array(
      'tb_issuances.id'                       => NULL,
      'tb_issuances.document_number'          => 'Document Number',
      'tb_issuances.issued_date'              => 'Issued Date',
      'tb_issuances.category'                 => 'Category',
      'tb_issuances.warehouse'                => 'Base',
      'tb_master_items.description'           => 'Description',
      'tb_master_items.id as item_id'                      => 'Item Id',
      'tb_master_items.part_number'           => 'Part Number',
      'tb_master_items.serial_number'         => 'Serial Number',
      'tb_stocks.condition'                   => 'Condition',
      'tb_issuance_items.issued_quantity'     => 'Quantity',
      'tb_master_items.unit'                  => 'Unit',
      'tb_master_item_groups.coa'               => 'COA',
      'tb_master_items.kode_stok'               => 'Kode Stok',
      'tb_issuance_items.remarks'             => 'Remarks',
      'tb_issuances.issued_to'                => 'Issued To',
      'tb_issuances.issued_by'                => 'Issued By',
      'tb_issuances.required_by'              => 'Required By',
      'tb_issuances.requisition_reference'    => 'Requisition Ref.',
      'tb_issuances.notes'                    => 'Note/IPC Ref.',
    );

    if (config_item('auth_role') == 'SUPERVISOR' || config_item('auth_role') == 'SUPER ADMIN') {
      $selected['tb_stock_in_stores.stores'] = 'Issued Stores';
      $selected['tb_stock_in_stores.reference_document'] = 'Rec. Document';
    }

    if (config_item('auth_role') != 'PIC STOCK') {
      $selected['tb_issuance_items.issued_unit_value']  = 'Value';
      $selected['tb_issuance_items.issued_total_value'] = 'Total Value';
    }

    if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE') {
      $selected['tb_master_items.kode_pemakaian']    = 'Biaya Pemakaian';
    }

    return $selected;
  }

  public function getSearchableColumns()
  {
    $searchable = array(
      'tb_issuances.document_number',
      'tb_issuances.category',
      'tb_issuances.warehouse',
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.serial_number',
      'tb_stocks.condition',
      'tb_master_items.unit',
      'tb_issuance_items.remarks',
      'tb_issuances.issued_to',
      'tb_issuances.issued_by',
      'tb_issuances.required_by',
      'tb_issuances.requisition_reference',
      'tb_issuances.notes',
    );

    if (config_item('auth_role') == 'SUPERVISOR') {
      $searchable[] = 'tb_stock_in_stores.stores';
      $searchable[] = 'tb_stock_in_stores.reference_document';
    }
    if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE') {
      $searchable[] = 'tb_master_items.kode_pemakaian';
    }

    return $searchable;
  }

  public function getOrderableColumns()
  {
    $orderable = array(
      null,
      'tb_issuances.document_number',
      'tb_issuances.issued_date',
      'tb_issuances.category',
      'tb_issuances.warehouse',
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.serial_number',
      'tb_stocks.condition',
      'tb_issuance_items.issued_quantity',
      'tb_master_items.unit',
      'tb_issuance_items.remarks',
      'tb_issuances.issued_to',
      'tb_issuances.issued_by',
      'tb_issuances.required_by',
      'tb_issuances.requisition_reference',
      'tb_issuances.notes',
    );

    if (config_item('auth_role') == 'SUPERVISOR') {
      $orderable[] = 'tb_stock_in_stores.stores';
      $orderable[] = 'tb_stock_in_stores.reference_document';
    }

    if (config_item('auth_role') != 'PIC STOCK') {
      $orderable[] = 'tb_issuance_items.issued_unit_value';
      $orderable[] = 'tb_issuance_items.issued_total_value';
    }

    if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'VP FINANCE') {
      $orderable[] = 'tb_master_items.kode_pemakaian';
    }

    return $orderable;
  }

  private function searchIndex()
  {
    if (!empty($_POST['columns'][1]['search']['value']) && !empty($_POST['columns'][2]['search']['value'])) {
      $range_issued_date[0] = $_POST['columns'][1]['search']['value'];
      $range_issued_date[1] = $_POST['columns'][2]['search']['value'];
      // $range_issued_date  = explode(' ', $search_issued_date);

      $this->db->where('tb_issuances.issued_date >= ', $range_issued_date[0]);
      $this->db->where('tb_issuances.issued_date <= ', $range_issued_date[1]);
    }

    if (!empty($_POST['columns'][3]['search']['value'])) {
      $search_warehouse = $_POST['columns'][3]['search']['value'];
      // $this->db->where('tb_issuances.warehouse', $search_warehouse);
      if($search_warehouse == 'WISNU'){
        $this->db->group_start()
        ->like('tb_issuances.warehouse', 'WISNU')
        ->group_end();
      }
      else if($search_warehouse == "all base rekondisi"){
        $this->db->group_start()
        ->like('tb_issuances.warehouse', 'REKONDISI')
        ->group_end();
      }
      else{
        $this->db->where('tb_issuances.warehouse', $search_warehouse);
      }   
    }

    if (!empty($_POST['columns'][4]['search']['value'])) {
      // $search_category = $_POST['columns'][4]['search']['value'];
      // $this->db->where('tb_issuances.category', $search_category);

      $search_category = $_POST['columns'][4]['search']['value'];
      $categories  = explode(',', $search_category);

      $this->db->where_in('tb_issuances.category', $categories);
    }

    if (!empty($_POST['columns'][5]['search']['value'])) {
      $search_issued_to = $_POST['columns'][5]['search']['value'];

      if($search_issued_to != 'ALL'){
        $this->db->like('UPPER(tb_issuances.issued_to)', strtoupper($search_issued_to));
      }      
    }

    $i = 0;

    foreach ($this->getSearchableColumns() as $item) {
      if ($_POST['search']['value']) {
        $term = strtoupper($_POST['search']['value']);

        if ($i === 0) {
          $this->db->group_start();
          $this->db->like('UPPER(' . $item . ')', $term);
        } else {
          $this->db->or_like('UPPER(' . $item . ')', $term);
        }

        if (count($this->getSearchableColumns()) - 1 == $i)
          $this->db->group_end();
      }

      $i++;
    }
  }

  function getIndex($return = 'array')
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_issuances');
    $this->db->join('tb_issuance_items', 'tb_issuance_items.document_number = tb_issuances.document_number');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_issuance_items.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where_in('tb_issuances.category', config_item('auth_inventory'));
    $this->db->where_in('tb_issuances.warehouse', config_item('auth_warehouses'));
    // $this->db->where('tb_issuances.issued_date >= ', $start_date);
    // $this->db->where('EXTRACT(YEAR FROM tb_issuances.issued_date)::integer = ', date('Y')-1);    
    $this->db->where('tb_issuances.issued_to !=', 'MIX');
    $this->db->like('tb_issuances.document_number', 'MS');

    $this->searchIndex();

    $column_order = $this->getOrderableColumns();

    if (isset($_POST['order'])) {
      foreach ($_POST['order'] as $key => $order) {
        $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('id', 'desc');
    }

    if ($_POST['length'] != -1)
      $this->db->limit($_POST['length'], $_POST['start']);

    $query = $this->db->get();

    if ($return === 'object') {
      return $query->result();
    } elseif ($return === 'json') {
      return json_encode($query->result());
    } else {
      return $query->result_array();
    }
  }

  function countIndexFiltered()
  {
    $this->db->from('tb_issuances');
    $this->db->join('tb_issuance_items', 'tb_issuance_items.document_number = tb_issuances.document_number');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_issuance_items.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->where_in('tb_issuances.category', config_item('auth_inventory'));
    $this->db->where_in('tb_issuances.warehouse', config_item('auth_warehouses'));
    $this->db->where('tb_issuances.issued_to !=', 'MIX');
    $this->db->like('tb_issuances.document_number', 'MS');
    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->from('tb_issuances');
    $this->db->join('tb_issuance_items', 'tb_issuance_items.document_number = tb_issuances.document_number');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_issuance_items.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->where_in('tb_issuances.category', config_item('auth_inventory'));
    $this->db->where_in('tb_issuances.warehouse', config_item('auth_warehouses'));
    $this->db->where('tb_issuances.issued_to !=', 'MIX');
    $this->db->like('tb_issuances.document_number', 'MS');
    $query = $this->db->get();

    return $query->num_rows();
  }

  public function findById($id)
  {
    $this->db->where('id', $id);

    $query    = $this->db->get('tb_issuances');
    $issued   = $query->unbuffered_row('array');

    $select = array(
      'tb_stock_in_stores.*',
      'tb_stocks.condition',
      'tb_stock_in_stores.stores',
      'tb_stock_in_stores.quantity',
      'tb_master_items.serial_number',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.alternate_part_number',
      'tb_master_items.unit',
      'tb_master_items.group',
      'tb_issuance_items.issued_quantity',
      'tb_issuance_items.issued_unit_value',
      'tb_issuance_items.remarks',
    );

    $this->db->select($select);
    $this->db->from('tb_issuance_items');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_issuance_items.stock_in_stores_id');
    // $this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_in_stores.serial_id', 'left');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->where('tb_issuance_items.document_number', $issued['document_number']);

    $query = $this->db->get();

    foreach ($query->result_array() as $key => $value) {
      $issued['items'][$key] = $value;
      $issued['items'][$key]['stock_in_stores_id'] = $value['id'];

      if (empty($issued['category'])) {
        $this->db->select('category');
        $this->db->from('tb_master_item_groups');
        $this->db->where('group', $value['group']);

        $query = $this->db->get();
        $icat  = $query->unbuffered_row();

        $issued['category'] = $icat->category;
      }
    }

    return $issued;
  }

  public function isDocumentNumberExists($document_number)
  {
    $this->db->where('document_number', $document_number);
    $query = $this->db->get('tb_issuances');

    if ($query->num_rows() > 0)
      return true;

    return false;
  }

  public function getDocumentId($document_number)
  {
    $this->db->select('tb_issuances.id');
    $this->db->from('tb_issuances');
    $this->db->where('UPPER(tb_issuances.document_number)', strtoupper($document_number));

    $query  = $this->db->get();
    $row    = $query->unbuffered_row();

    return $row->id;
  }

  public function isValidDocumentQuantity($document_number)
  {
    $this->db->select_sum('tb_issuance_items.issued_quantity', 'issued_quantity');
    $this->db->select_sum('tb_stock_in_stores.quantity', 'stored_quantity');
    $this->db->select('tb_issuance_items.document_number');
    $this->db->from('tb_issuance_items');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_issuance_items.stock_in_stores_id');
    $this->db->where('tb_issuance_items.document_number', $document_number);
    $this->db->group_by('tb_issuance_items.document_number');

    $query  = $this->db->get();
    $row    = $query->unbuffered_row('array');

    if ($row['issued_quantity'] === $row['issued_quantity'])
      return true;

    return false;
  }

  public function save()
  {
    $this->db->trans_begin();

    // DELETE OLD DOCUMENT
    if (isset($_SESSION['usage']['id'])) {
      $id = $_SESSION['usage']['id'];

      $this->db->select('document_number, warehouse,issued_date');
      $this->db->where('id', $id);
      $this->db->from('tb_issuances');

      $query = $this->db->get();
      $row   = $query->unbuffered_row('array');

      $old_document_number  = $row['document_number'];
      $old_warehouse        = $row['warehouse'];

      $this->db->select('tb_issuance_items.id, tb_issuance_items.stock_in_stores_id, tb_issuance_items.issued_quantity, tb_issuance_items.issued_unit_value, tb_stock_in_stores.stock_id, tb_stock_in_stores.serial_id, tb_stock_in_stores.stores');
      $this->db->from('tb_issuance_items');
      $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_issuance_items.stock_in_stores_id');
      $this->db->where('tb_issuance_items.document_number', $old_document_number);

      $query  = $this->db->get();
      $result = $query->result_array();

      foreach ($result as $data) {
        // $prev_old_stock = getStockActive($data['stock_id']);
        // $next_old_stock = floatval($prev_old_stock->total_quantity) + floatval($data['issued_quantity']);

        $prev_old_stock = getStockPrev($data['stock_id'], $data['stores']);
        $next_old_stock = floatval($prev_old_stock) + floatval($data['issued_quantity']);

        $this->db->set('stock_id', $data['stock_id']);
        $this->db->set('serial_id', $data['serial_id']);
        $this->db->set('warehouse', $old_warehouse);
        $this->db->set('stores', $data['stores']);
        $this->db->set('date_of_entry', $row['issued_date']);
        $this->db->set('period_year', config_item('period_year'));
        $this->db->set('period_month', config_item('period_month'));
        $this->db->set('document_type', 'REVISION USAGE');
        $this->db->set('remarks', 'REVISION');
        $this->db->set('document_number', $old_document_number);
        $this->db->set('received_from', $old_document_number);
        $this->db->set('received_by', config_item('auth_person_name'));
        $this->db->set('prev_quantity', floatval($prev_old_stock));
        $this->db->set('balance_quantity', $next_old_stock);
        $this->db->set('quantity', 0 + floatval($data['issued_quantity']));
        $this->db->set('unit_value', floatval($data['issued_unit_value']));
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('stock_in_stores_id', $data['stock_in_stores_id']);
        $this->db->set('doc_type', 6);
        $this->db->set('tgl', date('Ymd', strtotime($row['issued_date'])));
        $this->db->set('total_value', floatval($data['issued_unit_value']) * (0 + floatval($data['issued_quantity'])));
        $this->db->insert('tb_stock_cards');

        $this->db->from('tb_stock_in_stores');
        $this->db->where('id', $data['stock_in_stores_id']);

        $query        = $this->db->get();
        $stock_stored = $query->unbuffered_row('array');
        $new_quantity = $stock_stored['quantity'] + $data['issued_quantity'];

        $this->db->where('id', $data['stock_in_stores_id']);
        $this->db->set('quantity', floatval($new_quantity));
        $this->db->update('tb_stock_in_stores');

        $this->db->where('id', $data['serial_id']);
        $this->db->set('quantity', 1);
        $this->db->update('tb_master_item_serials');

        $this->db->where('id', $data['id']);
        $this->db->delete('tb_issuance_items');
      }

      $this->db->where('id', $id);
      $this->db->delete('tb_issuances');

      //delete tb_jurnal & tb_jurnal_detail
      $this->db->select('id');
      $this->db->from('tb_jurnal');
      $this->db->where('grn_no', $old_document_number);
      $old_id_jurnal = $this->db->get()->result();
      foreach ($old_id_jurnal as $key) {
        $this->db->where('id_jurnal', $key->id);
        $this->db->delete('tb_jurnal_detail');
      }
      $this->db->where('grn_no', $old_document_number);
      $this->db->delete('tb_jurnal');
    }
    // CREATE NEW DOCUMENT
    $document_number        = sprintf('%06s', $_SESSION['usage']['document_number']) . usage_format_number();
    $issued_date            = $_SESSION['usage']['issued_date'];
    $issued_by              = (empty($_SESSION['usage']['issued_by'])) ? NULL : $_SESSION['usage']['issued_by'];
    $issued_to              = (empty($_SESSION['usage']['issued_to'])) ? NULL : $_SESSION['usage']['issued_to'];
    $required_by            = (empty($_SESSION['usage']['required_by'])) ? NULL : $_SESSION['usage']['required_by'];
    $requisition_reference  = (empty($_SESSION['usage']['requisition_reference'])) ? NULL : $_SESSION['usage']['requisition_reference'];
    $approved_by            = (empty($_SESSION['usage']['approved_by'])) ? NULL : $_SESSION['usage']['approved_by'];
    $warehouse              = $_SESSION['usage']['warehouse'];
    $category               = $_SESSION['usage']['category'];
    $notes                  = (empty($_SESSION['usage']['notes'])) ? NULL : $_SESSION['usage']['notes'];

    $this->db->set('document_number', $document_number);
    $this->db->set('issued_to', $issued_to);
    $this->db->set('issued_date', $issued_date);
    $this->db->set('issued_by', $issued_by);
    $this->db->set('required_by', $required_by);
    $this->db->set('requisition_reference', $requisition_reference);
    $this->db->set('approved_by', $approved_by);
    $this->db->set('category', $category);
    $this->db->set('warehouse', $warehouse);
    $this->db->set('notes', $notes);
    $this->db->set('created_by', config_item('auth_person_name'));
    $this->db->set('updated_by', config_item('auth_person_name'));
    // $this->db->set('group', strtoupper($data['group']));
    // $this->db->set('doc_type', 4);
    $this->db->insert('tb_issuances');

    $this->db->set('no_jurnal', $document_number);
    $this->db->set('tanggal_jurnal  ', date("Y-m-d"));
    $this->db->set('grn_no', $document_number);
    $this->db->set('source', "INV-OUT");
    $this->db->set('keterangan', 'Material Slip issued to '. $issued_to.' doc no : '. $document_number);
    $this->db->insert('tb_jurnal');
    $id_jurnal = $this->db->insert_id();
    // PROCESSING USAGE ITEMS
    foreach ($_SESSION['usage']['items'] as $key => $data) {
      if (in_array($_SESSION['usage']['category'],['EXPENSE','CAPEX'])){
        $this->db->from('tb_stock_in_stores');
        $this->db->where('id', $data['stock_in_stores_id']);
        $query        = $this->db->get();
        $stock_stored = $query->unbuffered_row('array');

        $stock_in_stores_id = $stock_stored['id'];
        $ms           = $data['issued_quantity'];
        $new_quantity = $stock_stored['quantity'] - $ms;
        if ($stock_stored['kurs_dollar'] == 1) {
          $currency = 'IDR';
        } else {
          $currency = 'USD';
        }   
        
        $prev_stock   = getStockPrev($stock_stored['stock_id'], $stock_stored['stores']);
        $next_stock   = floatval($prev_stock) - floatval($ms);

        // UPDATE STOCK in STORES
        $this->db->set('quantity', floatval($new_quantity));
        // $this->db->set('qty_konvers', floatval($quantity));
        $this->db->where('id', $stock_in_stores_id);
        $this->db->update('tb_stock_in_stores');

        // UPDATE STOCK in SERIAL
        $this->db->set('quantity', 0);
        $this->db->set('reference_document', $document_number);
        $this->db->where('id', $stock_stored['serial_id']);
        $this->db->update('tb_master_item_serials');

        // *
        //  * INSERT INTO USAGE ITEMS
        $unit_value = $stock_stored['unit_value'];

        $this->db->set('document_number', $document_number);
        $this->db->set('stock_in_stores_id', $stock_in_stores_id);
        $this->db->set('issued_quantity', floatval($ms));
        $this->db->set('issued_unit_value', floatval($stock_stored['unit_value']));
        $this->db->set('issued_total_value', floatval($stock_stored['unit_value']) * floatval($ms));
        $x = floatval($stock_stored['unit_value']) * floatval($ms);
        $this->db->set('remarks', $data['remarks']);
        $this->db->insert('tb_issuance_items');

        $coa = $this->coaByGroup(strtoupper($data['group']));
        $kode = $this->codeByDescription($stock_stored['stock_id']);
        $this->db->set('id_jurnal', $id_jurnal);
        $this->db->set('jenis_transaksi', $data['group']);
        $this->db->set('trs_kredit', $x);
        $this->db->set('trs_debet', 0);
        $this->db->set('trs_kredit_usd', floatval($stock_stored['unit_value_dollar']) * floatval($ms));
        $this->db->set('trs_debet_usd', 0);
        $this->db->set('kode_rekening', $coa->coa);
        $this->db->set('stock_in_stores_id', $stock_in_stores_id);
        $this->db->set('currency', $currency);
        $this->db->set('kode_rekening_lawan', $kode->kode_pemakaian);
        $this->db->insert('tb_jurnal_detail');
        $jenis_transaksi = $this->groupByKode($kode->kode_pemakaian);
        $this->db->set('id_jurnal', $id_jurnal);
        $this->db->set('jenis_transaksi', strtoupper($kode->group));
        $this->db->set('trs_debet', $x);
        $this->db->set('trs_kredit', 0);
        $this->db->set('trs_debet_usd', floatval($stock_stored['unit_value_dollar']) * floatval($ms));
        $this->db->set('trs_kredit_usd', 0);
        $this->db->set('kode_rekening', $kode->kode_pemakaian);
        $this->db->set('stock_in_stores_id', $stock_in_stores_id);
        $this->db->set('currency', $currency);
        $this->db->set('kode_rekening_lawan', $coa->coa);
        $this->db->insert('tb_jurnal_detail');
        
        /**
          * CREATE STOCK CARD
        */
        $this->db->set('serial_id', $stock_stored['serial_id']);
        $this->db->set('stock_id', $stock_stored['stock_id']);
        $this->db->set('warehouse', $stock_stored['warehouse']);
        $this->db->set('stores', strtoupper($stock_stored['stores']));
        $this->db->set('date_of_entry', $issued_date);
        $this->db->set('period_year', config_item('period_year'));
        $this->db->set('period_month', config_item('period_month'));
        $this->db->set('document_type', 'USAGE');
        $this->db->set('document_number', $document_number);
        $this->db->set('issued_to', $issued_to);
        $this->db->set('issued_by', $issued_by);
        $this->db->set('prev_quantity', floatval($prev_stock));
        $this->db->set('balance_quantity', floatval($next_stock));
        // $this->db->set('prev_quantity', floatval($data['maximum_quantity']));
        // $this->db->set('balance_quantity', floatval($data['maximum_quantity'])-floatval($data['issued_quantity']));

        $this->db->set('quantity', 0 - floatval($ms));
        $this->db->set('unit_value', floatval($stock_stored['unit_value']));
        $this->db->set('remarks', $data['remarks']);
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('stock_in_stores_id', $stock_in_stores_id);
        $this->db->set('doc_type', 6);
        $this->db->set('tgl', date('Ymd', strtotime($issued_date)));
        $this->db->set('total_value', floatval($stock_stored['unit_value']) * (0 - floatval($ms)));
        $this->db->insert('tb_stock_cards');

      }else{
        $stock_id = $data['stock_id'];

        $this->db->from('tb_stock_in_stores');
        $this->db->where('quantity > 0');
        $this->db->where('stock_id', $stock_id);
        //$this->db->where('warehouse', $warehouse);
        $this->db->where('stores', $data['stores']);
        $this->db->order_by('tb_stock_in_stores.received_date', 'asc');

        $query        = $this->db->get();
        // $stock_stored = $query->unbuffered_row('array');
        $stock        = $query->result_array();

        $ms           = $data['issued_quantity'];

        foreach ($stock as $stock_stored) {
          if ($ms > 0) {
            if ($stock_stored['quantity'] >= $ms) {
              $stock_in_stores_id = $stock_stored['id'];

              // if($data['unit_pakai']!=$data['unit']){
              //   $item_id        = getItemId($data['part_number'],$data['serial_number']);
              //   $konversi       = getKonversi($item_id);
              //   // $hasil_konversi = floatval($konversi)*floatval($stock_stored['quantity']);
              //   $quantity       = floatval($data['qty_konvers'])-floatval($data['issued_quantity']);
              //   $new_quantity   = floatval($quantity)/floatval($konversi);
              // }else{
              $new_quantity = $stock_stored['quantity'] - $ms;
              if ($stock_stored['kurs_dollar'] == 1) {
                $currency = 'IDR';
              } else {
                $currency = 'USD';
              }
              // }      

              $prev_stock   = getStockPrev($stock_stored['stock_id'], $stock_stored['stores']);
              $next_stock   = floatval($prev_stock) - floatval($ms);

              // UPDATE STOCK in STORES
              $this->db->set('quantity', floatval($new_quantity));
              // $this->db->set('qty_konvers', floatval($quantity));
              $this->db->where('id', $stock_in_stores_id);
              $this->db->update('tb_stock_in_stores');

              // UPDATE STOCK in SERIAL
              $this->db->set('quantity', 0);
              $this->db->set('reference_document', $document_number);
              $this->db->where('id', $stock_stored['serial_id']);
              $this->db->update('tb_master_item_serials');

              //upate stock in tb master part number
              // $qty_awal = getPartnumberQty($data['part_number']);

              // $qty_baru = floatval($qty_awal) - floatval($data['issued_quantity']);

              // $this->db->set('qty', $qty_baru);
              // $this->db->where('part_number', strtoupper($data['part_number']));
              // $this->db->update('tb_master_part_number');

              // *
              //  * INSERT INTO USAGE ITEMS
              $unit_value = $stock_stored['unit_value'];

              $this->db->set('document_number', $document_number);
              $this->db->set('stock_in_stores_id', $stock_in_stores_id);
              $this->db->set('issued_quantity', floatval($ms));
              $this->db->set('issued_unit_value', floatval($stock_stored['unit_value']));
              $this->db->set('issued_total_value', floatval($stock_stored['unit_value']) * floatval($ms));
              $x = floatval($stock_stored['unit_value']) * floatval($ms);
              $this->db->set('remarks', $data['remarks']);
              $this->db->insert('tb_issuance_items');

              $coa = $this->coaByGroup(strtoupper($data['group']));
              $kode = $this->codeByDescription($stock_stored['stock_id']);
              $this->db->set('id_jurnal', $id_jurnal);
              $this->db->set('jenis_transaksi', $data['group']);
              $this->db->set('trs_kredit', $x);
              $this->db->set('trs_debet', 0);
              $this->db->set('trs_kredit_usd', floatval($stock_stored['unit_value_dollar']) * floatval($ms));
              $this->db->set('trs_debet_usd', 0);
              $this->db->set('kode_rekening', $coa->coa);
              $this->db->set('stock_in_stores_id', $stock_in_stores_id);
              $this->db->set('currency', $currency);
              $this->db->set('kode_rekening_lawan', $kode->kode_pemakaian);
              $this->db->insert('tb_jurnal_detail');
              $jenis_transaksi = $this->groupByKode($kode->kode_pemakaian);
              $this->db->set('id_jurnal', $id_jurnal);
              $this->db->set('jenis_transaksi', strtoupper($kode->group));
              $this->db->set('trs_debet', $x);
              $this->db->set('trs_kredit', 0);
              $this->db->set('trs_debet_usd', floatval($stock_stored['unit_value_dollar']) * floatval($ms));
              $this->db->set('trs_kredit_usd', 0);
              $this->db->set('kode_rekening', $kode->kode_pemakaian);
              $this->db->set('stock_in_stores_id', $stock_in_stores_id);
              $this->db->set('currency', $currency);
              $this->db->set('kode_rekening_lawan', $coa->coa);
              $this->db->insert('tb_jurnal_detail');
              /**
               * CREATE STOCK CARD
               */
              $this->db->set('serial_id', $stock_stored['serial_id']);
              $this->db->set('stock_id', $stock_stored['stock_id']);
              $this->db->set('warehouse', $stock_stored['warehouse']);
              $this->db->set('stores', strtoupper($stock_stored['stores']));
              $this->db->set('date_of_entry', $issued_date);
              $this->db->set('period_year', config_item('period_year'));
              $this->db->set('period_month', config_item('period_month'));
              $this->db->set('document_type', 'USAGE');
              $this->db->set('document_number', $document_number);
              $this->db->set('issued_to', $issued_to);
              $this->db->set('issued_by', $issued_by);
              $this->db->set('prev_quantity', floatval($prev_stock));
              $this->db->set('balance_quantity', floatval($next_stock));
              // $this->db->set('prev_quantity', floatval($data['maximum_quantity']));
              // $this->db->set('balance_quantity', floatval($data['maximum_quantity'])-floatval($data['issued_quantity']));

              $this->db->set('quantity', 0 - floatval($ms));
              $this->db->set('unit_value', floatval($stock_stored['unit_value']));
              $this->db->set('remarks', $data['remarks']);
              $this->db->set('created_by', config_item('auth_person_name'));
              $this->db->set('stock_in_stores_id', $stock_in_stores_id);
              $this->db->set('doc_type', 6);
              $this->db->set('tgl', date('Ymd', strtotime($issued_date)));
              $this->db->set('total_value', floatval($stock_stored['unit_value']) * (0 - floatval($ms)));
              $this->db->insert('tb_stock_cards');
              $ms = $ms - $ms;
            } else {
              $stock_in_stores_id = $stock_stored['id'];
              // if($data['unit_pakai']!=$data['unit']){
              //   $item_id        = getItemId($data['part_number'],$data['serial_number']);
              //   $konversi       = getKonversi($item_id);
              //   // $hasil_konversi = floatval($konversi)*floatval($stock_stored['quantity']);
              //   $quantity       = floatval($data['qty_konvers'])-floatval($data['issued_quantity']);
              //   $new_quantity   = floatval($quantity)/floatval($konversi);
              // }else{
              // $new_quantity = $stock_stored['quantity'] - $data['issued_quantity'];
              // }
              if ($stock_stored['kurs_dollar'] == 1) {
                $currency = 'IDR';
              } else {
                $currency = 'USD';
              }
              $new_quantity = 0;
              $prev_stock   = getStockPrev($stock_stored['stock_id'], $data['stores']);
              $next_stock   = floatval($prev_stock) - floatval($stock_stored['quantity']);
              // UPDATE STOCK in STORES
              $this->db->set('quantity', floatval($new_quantity));
              // $this->db->set('qty_konvers', floatval($quantity));
              $this->db->where('id', $stock_in_stores_id);
              $this->db->update('tb_stock_in_stores');

              // UPDATE STOCK in SERIAL
              $this->db->set('quantity', 0);
              $this->db->set('reference_document', $document_number);
              $this->db->where('id', $stock_stored['serial_id']);
              $this->db->update('tb_master_item_serials');

              //upate stock in tb master part number
              // $qty_awal = getPartnumberQty($data['part_number']);

              // $qty_baru = floatval($qty_awal) - floatval($data['issued_quantity']);

              // $this->db->set('qty', $qty_baru);
              // $this->db->where('part_number', strtoupper($data['part_number']));
              // $this->db->update('tb_master_part_number');

              // *
              //  * INSERT INTO USAGE ITEMS
              $unit_value = $stock_stored['unit_value'];

              $this->db->set('document_number', $document_number);
              $this->db->set('stock_in_stores_id', $stock_in_stores_id);
              $this->db->set('issued_quantity', floatval($stock_stored['quantity']));
              $this->db->set('issued_unit_value', floatval($stock_stored['unit_value']));
              $this->db->set('issued_total_value', floatval($stock_stored['unit_value']) * floatval($stock_stored['quantity']));
              $x = floatval($stock_stored['unit_value']) * floatval($stock_stored['quantity']);
              $this->db->set('remarks', $data['remarks']);
              $this->db->insert('tb_issuance_items');

              $coa = $this->coaByGroup(strtoupper($data['group']));
              $kode = $this->codeByDescription($stock_stored['stock_id']);

              $this->db->set('id_jurnal', $id_jurnal);
              $this->db->set('jenis_transaksi', $data['group']);
              $this->db->set('trs_kredit', $x);
              $this->db->set('trs_debet', 0);
              $this->db->set('trs_kredit_usd', floatval($stock_stored['unit_value_dollar']) * floatval($ms));
              $this->db->set('trs_debet_usd', 0);
              $this->db->set('kode_rekening', $coa->coa);
              $this->db->set('stock_in_stores_id', $stock_in_stores_id);
              $this->db->set('currency', $currency);
              $this->db->set('kode_rekening_lawan', $kode->kode_pemakaian);           
              $this->db->insert('tb_jurnal_detail');

              $jenis_transaksi = $this->groupByKode($kode->kode_pemakaian);
              $this->db->set('id_jurnal', $id_jurnal);
              $this->db->set('jenis_transaksi', strtoupper($kode->group));
              $this->db->set('trs_debet', $x);
              $this->db->set('trs_kredit', 0);
              $this->db->set('trs_debet_usd', floatval($stock_stored['unit_value_dollar']) * floatval($ms));
              $this->db->set('trs_kredit_usd', 0);
              $this->db->set('kode_rekening', $kode->kode_pemakaian);
              $this->db->set('stock_in_stores_id', $stock_in_stores_id);
              $this->db->set('kode_rekening_lawan', $coa->coa);
              $this->db->set('currency', $currency);                
              $this->db->insert('tb_jurnal_detail');
              /**
               * CREATE STOCK CARD
               */
              $this->db->set('serial_id', $stock_stored['serial_id']);
              $this->db->set('stock_id', $stock_stored['stock_id']);
              $this->db->set('warehouse', $stock_stored['warehouse']);
              $this->db->set('stores', strtoupper($stock_stored['stores']));
              $this->db->set('date_of_entry', $issued_date);
              $this->db->set('period_year', config_item('period_year'));
              $this->db->set('period_month', config_item('period_month'));
              $this->db->set('document_type', 'USAGE');
              $this->db->set('document_number', $document_number);
              $this->db->set('issued_to', $issued_to);
              $this->db->set('issued_by', $issued_by);
              $this->db->set('prev_quantity', floatval($prev_stock));
              $this->db->set('balance_quantity', floatval($next_stock));
              // $this->db->set('prev_quantity', floatval($data['maximum_quantity']));
              // $this->db->set('balance_quantity', floatval($data['maximum_quantity'])-floatval($data['issued_quantity']));

              $this->db->set('quantity', 0 - floatval($stock_stored['quantity']));
              $this->db->set('unit_value', floatval($stock_stored['unit_value']));
              $this->db->set('remarks', $data['remarks']);
              $this->db->set('created_by', config_item('auth_person_name'));
              $this->db->set('stock_in_stores_id', $stock_in_stores_id);
              $this->db->set('doc_type', 6);
              $this->db->set('tgl', date('Ymd', strtotime($issued_date)));
              $this->db->set('total_value', floatval($stock_stored['unit_value']) * (0 - floatval($stock_stored['quantity'])));
              $this->db->insert('tb_stock_cards');

              $ms = $ms - $stock_stored['quantity'];
            }
          }
        }
      }
      
    }



    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function delete()
  {
    $this->db->trans_begin();

    $id = $this->input->post('id');

    $this->db->select('document_number, warehouse,issued_date');
    $this->db->where('id', $id);
    $this->db->from('tb_issuances');

    $query = $this->db->get();
    $row   = $query->unbuffered_row('array');

    $document_number  = $row['document_number'];
    $warehouse        = $row['warehouse'];
    $issued_date      = $row['issued_date'];

    $this->db->select('tb_issuance_items.id, tb_issuance_items.stock_in_stores_id, tb_issuance_items.issued_quantity, tb_issuance_items.issued_unit_value, tb_stock_in_stores.stock_id, tb_stock_in_stores.serial_id, tb_stock_in_stores.stores');
    $this->db->from('tb_issuance_items');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_issuance_items.stock_in_stores_id');
    $this->db->where('tb_issuance_items.document_number', $document_number);

    $query  = $this->db->get();
    $result = $query->result_array();

    foreach ($result as $data) {
      // $prev_old_stock = getStockActive($data['stock_id']);
      // $next_old_stock = floatval($prev_old_stock->total_quantity) + floatval($data['issued_quantity']);

      $prev_old_stock = getStockPrev($data['stock_id'], $data['stores']);
      $next_old_stock = floatval($prev_old_stock) + floatval($data['issued_quantity']);

      $this->db->set('stock_id', $data['stock_id']);
      $this->db->set('serial_id', $data['serial_id']);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('stores', $data['stores']);
      $this->db->set('date_of_entry', $issued_date);
      $this->db->set('period_year', config_item('period_year'));
      $this->db->set('period_month', config_item('period_month'));
      $this->db->set('document_type', 'REMOVAL USAGE');
      $this->db->set('remarks', 'DELETE DOCUMENT');
      $this->db->set('document_number', $document_number);
      $this->db->set('received_from', $document_number);
      $this->db->set('received_by', config_item('auth_person_name'));
      $this->db->set('prev_quantity', floatval($prev_old_stock->total_quantity));
      $this->db->set('balance_quantity', $next_old_stock);
      $this->db->set('quantity', 0 + floatval($data['issued_quantity']));
      $this->db->set('unit_value', floatval($data['issued_unit_value']));
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->set('stock_in_stores_id', $data['stock_in_stores_id']);
      $this->db->set('doc_type', 6);
      $this->db->set('tgl', date('Ymd', strtotime($issued_date)));
      $this->db->set('total_value', floatval($data['issued_unit_value']) * (0 + floatval($data['issued_quantity'])));
      $this->db->insert('tb_stock_cards');

      $this->db->from('tb_stock_in_stores');
      $this->db->where('id', $data['stock_in_stores_id']);

      $query        = $this->db->get();
      $stock_stored = $query->unbuffered_row('array');
      $new_quantity = $stock_stored['quantity'] + $data['issued_quantity'];

      $this->db->where('id', $data['stock_in_stores_id']);
      $this->db->set('quantity', floatval($new_quantity));
      $this->db->update('tb_stock_in_stores');

      $this->db->where('id', $data['serial_id']);
      $this->db->set('quantity', 1);
      $this->db->update('tb_master_item_serials');

      $this->db->where('id', $data['id']);
      $this->db->delete('tb_issuance_items');
    }

    $this->db->where('id', $id);
    $this->db->delete('tb_issuances');

    //delete tb_jurnal & tb_jurnal_detail
    $this->db->select('id');
    $this->db->from('tb_jurnal');
    $this->db->where('grn_no', $document_number);
    $old_id_jurnal = $this->db->get()->result();
    foreach ($old_id_jurnal as $key) {
      $this->db->where('id_jurnal', $key->id);
      $this->db->delete('tb_jurnal_detail');
    }
    $this->db->where('grn_no', $document_number);
    $this->db->delete('tb_jurnal');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function import(array $user_data)
  {
    $this->db->trans_begin();

    foreach ($user_data as $key => $data) {
      $document_number = (empty($data['document_number']))
        ? NULL : strtoupper($data['document_number']);

      $issued_date = (empty($data['issued_date']))
        ? NULL : strtoupper($data['issued_date']);

      $category = (empty($data['category']))
        ? NULL : strtoupper($data['category']);

      $warehouse = (empty($data['warehouse']))
        ? NULL : strtoupper($data['warehouse']);

      $description = (empty($data['description']))
        ? NULL : strtoupper($data['description']);

      $part_number = (empty($data['part_number']))
        ? NULL : strtoupper($data['part_number']);

      $serial_number = (empty($data['serial_number']))
        ? NULL : strtoupper($data['serial_number']);

      $condition = (empty($data['condition']))
        ? NULL : strtoupper($data['condition']);

      $issued_quantity = (empty($data['quantity']))
        ? 0 : floatval($data['quantity']);

      $unit = (empty($data['unit']))
        ? NULL : strtoupper($data['unit']);

      $remarks = (empty($data['remarks']))
        ? NULL : strtoupper($data['remarks']);

      $issued_to = (empty($data['issued_to']))
        ? NULL : strtoupper($data['issued_to']);

      $issued_by = (empty($data['issued_by']))
        ? NULL : strtoupper($data['issued_by']);

      $required_by = (empty($data['required_by']))
        ? NULL : strtoupper($data['required_by']);

      $requisition_reference = (empty($data['requisition_reference']))
        ? NULL : strtoupper($data['requisition_reference']);

      $notes = (empty($data['notes']))
        ? NULL : strtoupper($data['notes']);

      $stores = (empty($data['stores']))
        ? NULL : strtoupper($data['stores']);

      $reference_document = (empty($data['reference_document']))
        ? NULL : strtoupper($data['reference_document']);

      $issued_unit_value = (empty($data['value']))
        ? 0 : floatval($data['value']);

      $issued_total_value = (empty($data['total_value']))
        ? 0 : floatval($data['total_value']);

      $period_year  = get_setting('ACTIVE_YEAR');
      $period_month = get_setting('ACTIVE_MONTH');

      // start
      if ($this->isDocumentNumberExists($document_number)) {
        $document_id = $this->getDocumentId($document_number);
      } else {
        $this->db->set('document_number', $document_number);
        $this->db->set('issued_to', $issued_to);
        $this->db->set('issued_date', $issued_date);
        $this->db->set('issued_by', $issued_by);
        $this->db->set('required_by', $required_by);
        $this->db->set('requisition_reference', $requisition_reference);
        $this->db->set('approved_by', 'user');
        $this->db->set('category', $category);
        $this->db->set('warehouse', $warehouse);
        $this->db->set('notes', $notes);
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->insert('tb_issuances');

        $document_id = $this->db->insert_id();
      }

      // PROCESSING USAGE ITEMS
      $item_id  = getItemId($part_number,$description, $serial_number);
      $stock_in_stores_id = getStockInStoresId($item_id, $stores, $condition, $reference_document);
      // $stock_in_stores_id = getStockInStoresId($item_id, $stores, $condition);

      $this->db->from('tb_stock_in_stores');
      $this->db->where('id', $stock_in_stores_id);

      $query        = $this->db->get();
      $stock_stored = $query->unbuffered_row('array');
      $new_quantity = $stock_stored['quantity'] - $issued_quantity;

      $unit_value   = floatval($stock_stored['unit_value']);
      $total_value   = $unit_value * $issued_quantity;

      // $prev_stock   = getStockActive($stock_stored['stock_id']);
      // $next_stock   = floatval($prev_stock->total_quantity) - floatval($issued_quantity);

      $prev_stock   = getStockPrev($stock_stored['stock_id'], $data['stores']);
      $next_stock   = floatval($prev_stock) - floatval($data['issued_quantity']);

      // UPDATE STOCK in STORES
      $this->db->set('quantity', floatval($new_quantity));
      $this->db->where('id', $stock_in_stores_id);
      $this->db->update('tb_stock_in_stores');

      // UPDATE STOCK in SERIAL
      $this->db->set('quantity', 0);
      $this->db->set('reference_document', $document_number);
      $this->db->where('id', $stock_stored['serial_id']);
      $this->db->update('tb_master_item_serials');

      /**
       * INSERT INTO USAGE ITEMS
       */
      $this->db->set('document_number', $document_number);
      $this->db->set('stock_in_stores_id', $stock_in_stores_id);
      $this->db->set('issued_quantity', floatval($issued_quantity));
      $this->db->set('issued_unit_value', floatval($unit_value));
      $this->db->set('issued_total_value', floatval($total_value));
      $this->db->set('remarks', $remarks);
      $this->db->insert('tb_issuance_items');

      //upate stock in tb master part number
      $qty_awal = getPartnumberQty($part_number);

      $qty_baru = floatval($qty_awal) - floatval($issued_quantity);

      $this->db->set('qty', $qty_baru);
      $this->db->where('part_number', $part_number);
      $this->db->update('tb_master_part_number');

      /**
       * CREATE STOCK CARD
       */
      $this->db->set('serial_id', $stock_stored['serial_id']);
      $this->db->set('stock_id', $stock_stored['stock_id']);
      $this->db->set('warehouse', $stock_stored['warehouse']);
      $this->db->set('stores', strtoupper($stock_stored['stores']));
      $this->db->set('date_of_entry', $issued_date);
      $this->db->set('period_year', config_item('period_year'));
      $this->db->set('period_month', config_item('period_month'));
      $this->db->set('document_type', 'USAGE');
      $this->db->set('document_number', $document_number);
      $this->db->set('issued_to', $issued_to);
      $this->db->set('issued_by', $issued_by);
      $this->db->set('prev_quantity', floatval($prev_stock));
      $this->db->set('balance_quantity', $next_stock);
      $this->db->set('quantity', 0 - floatval($issued_quantity));
      $this->db->set('unit_value', floatval($unit_value));
      $this->db->set('remarks', $remarks);
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->insert('tb_stock_cards');
    }

    if ($this->db->trans_status() === FALSE) {
      return FALSE;
    }

    $this->db->trans_commit();
    return TRUE;
  }

  public function searchStockInStores($category)
  {
    if(in_array($category,['EXPENSE','CAPEX'])){
      $this->column_select = array(
        'tb_stock_in_stores.id',
        'tb_stock_in_stores.stores',
        'tb_stock_in_stores.quantity',
        'tb_stocks.condition',
        'tb_master_items.serial_number',
        'tb_master_items.part_number',
        'tb_master_items.description',
        'tb_master_items.alternate_part_number',
        'tb_master_items.group',
        'tb_master_items.unit',
        'tb_master_items.unit_pakai',
        'tb_stock_in_stores.received_date',
        'tb_stock_in_stores.expired_date',
        'tb_receipt_items.purchase_order_number'
      );
  
      $warehouse = $_SESSION['usage']['warehouse'];
  
      $this->db->select($this->column_select);
      $this->db->from('tb_stock_in_stores');
      $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
      $this->db->join('tb_receipt_items', 'tb_receipt_items.stock_in_stores_id = tb_stock_in_stores.id');
      $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
      $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
      $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
      $this->db->where('tb_master_item_groups.category', $category);
      $this->db->where_in('tb_stocks.condition', ['SERVICEABLE','UNSERVICEABLE']);
      $this->db->where('tb_stock_in_stores.quantity > ', 0);
      $this->db->where('UPPER(tb_stock_in_stores.warehouse)', strtoupper($warehouse));
      // $this->db->group_by('tb_stocks.id,tb_stocks.condition,tb_master_items.serial_number,tb_master_items.part_number,tb_master_items.description,tb_master_items.alternate_part_number,tb_master_items.group,tb_master_items.unit,tb_master_items.unit_pakai,tb_stock_in_stores.stores');
      $this->db->order_by('tb_stock_in_stores.received_date','ASC');
  
      $query  = $this->db->get();
      $result = $query->result_array();
  
      return $result;
    }else{
      $this->column_select = array(
        'tb_stocks.id',
        'tb_stock_in_stores.stores',
        // 'tb_stock_in_stores.received_date',
        // 'tb_stock_in_stores.expired_date',
        // 'tb_stock_in_stores.unit_value',
        'SUM(tb_stock_in_stores.quantity) as quantity',
        // 'tb_stock_in_stores.qty_konvers',
        'tb_stocks.condition',
        'tb_master_items.serial_number',
        'tb_master_items.part_number',
        'tb_master_items.description',
        'tb_master_items.alternate_part_number',
        'tb_master_items.group',
        'tb_master_items.unit',
        'tb_master_items.unit_pakai',
      );
  
      $warehouse = $_SESSION['usage']['warehouse'];
  
      $this->db->select($this->column_select);
      $this->db->from('tb_stock_in_stores');
      $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
      $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
      $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
      $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
      $this->db->where('tb_master_item_groups.category', $category);
      $this->db->where('tb_stocks.condition', 'SERVICEABLE');
      $this->db->where('tb_stock_in_stores.quantity > ', 0);
      $this->db->where('UPPER(tb_stock_in_stores.warehouse)', strtoupper($warehouse));
      $this->db->group_by('tb_stocks.id,tb_stocks.condition,tb_master_items.serial_number,tb_master_items.part_number,tb_master_items.description,tb_master_items.alternate_part_number,tb_master_items.group,tb_master_items.unit,tb_master_items.unit_pakai,tb_stock_in_stores.stores');
      // $this->db->order_by('tb_stock_in_stores.received_date','ASC');
  
      $query  = $this->db->get();
      $result = $query->result_array();
  
      return $result;
    }
    
  }

  function checkJurnalNumber()
  {
    return $this->db->get('tb_jurnal')->num_rows();
  }
  function jrl_last_number()
  {
    $div  = config_item('document_format_divider');
    $year = date('Y');

    $format = $div . 'JRL' . $year;
    if ($this->checkJurnalNumber() == 0) {
      $number = sprintf('%06s', 1);
      $document_number = $number . $div . "JRL" . $div . $year;
    } else {

      $format = $div . "JRL" . $div . $year;
      $this->db->select_max('no_jurnal', 'last_number');
      $this->db->from('tb_jurnal');
      $this->db->like('no_jurnal', $format, 'before');
      $query = $this->db->get('');
      $row    = $query->unbuffered_row();
      $last   = $row->last_number;
      $number = substr($last, 0, 6);
      $next   = $number + 1;
      $number = sprintf('%06s', $next);
      $document_number = $number . $div . "JRL" . $div . $year;
    }
    return $document_number;
  }
  function coaByGroup($group)
  {
    $this->db->select('coa');
    $this->db->from('tb_master_item_groups');
    $this->db->where('group', $group);
    return $this->db->get()->row();
  }
  function codeByDescription($id)
  {
    $this->db->select('tb_master_items.kode_pemakaian');
    $this->db->select('tb_master_coa.group');
    $this->db->from('tb_master_items');
    $this->db->join('tb_stocks', 'tb_stocks.item_id=tb_master_items.id');
    $this->db->join('tb_master_coa', 'tb_master_coa.coa=tb_master_items.kode_pemakaian');
    $this->db->where('tb_stocks.id', $id);

    return $this->db->get()->row();
  }

  function groupByKode($id)
  {
    $this->db->select('group');
    $this->db->from('tb_master_item_groups');
    $this->db->where('coa', $id);
    return $this->db->get()->row();
  }

  public function stores($stock_id)
  {
    $this->column_select = array(
      'tb_stock_in_stores.id',
      'tb_stock_in_stores.stores',
      'tb_stock_in_stores.received_date',
      'tb_stock_in_stores.reference_document',
      // 'tb_stock_in_stores.unit_value',
      'SUM(tb_stock_in_stores.quantity) as quantity',
      // 'tb_stock_in_stores.qty_konvers',
      // 'tb_stocks.condition',
      // 'tb_master_items.serial_number',
      // 'tb_master_items.part_number',
      // 'tb_master_items.description',
      // 'tb_master_items.alternate_part_number',
      // 'tb_master_items.group',
      // 'tb_master_items.unit',
      // 'tb_master_items.unit_pakai',
    );

    $warehouse = $_SESSION['usage']['warehouse'];

    $this->db->select($this->column_select);
    $this->db->from('tb_stock_in_stores');
    // $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    // $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    // $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    // $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
    // $this->db->where('tb_master_item_groups.category', $category);
    // $this->db->where('tb_stocks.condition', 'SERVICEABLE');
    $this->db->where('tb_stock_in_stores.stock_id', $stock_id);
    $this->db->where('tb_stock_in_stores.quantity > ', 0);
    $this->db->where('UPPER(tb_stock_in_stores.warehouse)', strtoupper($warehouse));
    $this->db->group_by('tb_stock_in_stores.id,tb_stock_in_stores.stores');
    $this->db->order_by('tb_stock_in_stores.received_date', 'asc');

    $query  = $this->db->get();
    $result = $query->result_array();

    return $result;
  }

  public function infoStockInStores($id)
  {
    $this->column_select = array(
      'tb_stock_in_stores.id',
      'tb_stock_in_stores.stores',
      'tb_stock_in_stores.quantity',
      'tb_stocks.condition',
      'tb_master_items.serial_number',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.alternate_part_number',
      'tb_master_items.group',
      'tb_master_items.unit',
      'tb_master_items.unit_pakai',
      'tb_stock_in_stores.received_date',
      'tb_stock_in_stores.expired_date',
      'tb_receipt_items.purchase_order_number'
    );

    $this->db->select($this->column_select);
    $this->db->from('tb_stock_in_stores');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_receipt_items', 'tb_receipt_items.stock_in_stores_id = tb_stock_in_stores.id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
    $this->db->where('tb_stock_in_stores.id', $id);

    $query  = $this->db->get();
    $result = $query->unbuffered_row('array');

    return $result;
  }
}
