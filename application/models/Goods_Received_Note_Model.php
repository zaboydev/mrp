<?php defined('BASEPATH') or exit('No direct script access allowed');

class Goods_Received_Note_Model extends MY_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function getSelectedColumns()
  {
    $return = array(
      'tb_receipts.id'                          => NULL,
      'tb_receipts.document_number'             => 'Document Number',
      'tb_receipts.received_date'               => 'Received Date',
      'tb_receipts.category'                    => 'Category',
      // 'tb_master_item_groups.group'             => 'Group',
      'tb_receipts.warehouse'                   => 'Base',
      // 'tb_stock_in_stores.stores'                   => 'Stores',
      'tb_master_items.description'             => 'Description',
      'tb_master_items.id as item_id'                      => 'Item Id',
      'tb_master_items.part_number'             => 'Part Number',
      'tb_master_items.alternate_part_number'   => 'Alt. Part Number',
      'tb_master_items.serial_number'           => 'Serial Number',
      // 'tb_master_items.minimum_quantity'           => 'Min qty',
      'tb_stocks.condition'                     => 'Condition',
      'tb_receipt_items.received_quantity'      => 'Quantity',
      // 'tb_master_items.unit'                    => 'Unit',
      'tb_master_items.unit'                    => 'Unit',
      'tb_master_item_groups.coa'               => 'COA',
      'tb_master_items.kode_stok'               => 'Kode Stok',
      'tb_receipt_items.purchase_order_number'  => 'Order Number',
      'tb_receipt_items.awb_number'             => 'AWB Number',
      'tb_receipt_items.remarks'                => 'Remarks',
      'tb_receipts.received_from'               => 'Received From',
      'tb_receipts.received_by'                 => 'Received By',
    );

    if (config_item('auth_role') != 'PIC STOCK') {
      $return['tb_receipt_items.received_unit_value']  = 'Value';
      $return[null] = 'Total Value';
      // $return['tb_receipt_items.received_unit_value+tb_stock_cards.quantity) as balance_quantity']  => 'Balance',
    }
    if (config_item('auth_role') == 'FINANCE' || config_item('auth_role') == 'SUPER ADMIN') {
      $return['tb_stock_in_stores.unit_value_dollar']  = 'Price USD';
      $return['tb_stock_in_stores.kurs_dollar']  = 'Kurs';
    }

    return $return;
  }

  public function getSearchableColumns()
  {
    $return = array(
      'tb_receipts.document_number',
      'tb_receipts.category',
      'tb_receipts.warehouse',
      'tb_receipts.category',
      'tb_receipts.warehouse',
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.alternate_part_number',
      'tb_master_items.serial_number',
      'tb_stocks.condition',
      'tb_master_items.unit',
      'tb_receipt_items.purchase_order_number',
      'tb_receipt_items.awb_number',
      'tb_receipt_items.remarks',
      'tb_receipts.received_from',
      'tb_receipts.received_by',
    );

    return $return;
  }

  public function getOrderableColumns()
  {
    $return = array(
      null,
      'tb_receipts.document_number',
      'tb_receipts.received_date',
      'tb_receipts.category',
      'tb_receipts.warehouse',
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.alternate_part_number',
      'tb_master_items.serial_number',
      'tb_stocks.condition',
      'tb_receipt_items.received_quantity',
      'tb_master_items.unit',
      'tb_receipt_items.purchase_order_number',
      'tb_receipt_items.awb_number',
      'tb_receipt_items.remarks',
      'tb_receipts.received_from',
      'tb_receipts.received_by',
    );

    if (config_item('auth_role') != 'PIC STOCK' || config_item('auth_role') == 'SUPER ADMIN') {
      $return[] = 'tb_receipt_items.received_unit_value';
      //$return[] = 'tb_receipt_items.received_total_value';
    }

    return $return;
  }

  private function searchIndex()
  {
    if (!empty($_POST['columns'][2]['search']['value'])) {
      $search_received_date = $_POST['columns'][2]['search']['value'];
      $range_received_date  = explode(' ', $search_received_date);

      $this->db->where('tb_receipts.received_date >= ', $range_received_date[0]);
      $this->db->where('tb_receipts.received_date <= ', $range_received_date[1]);
    }

    if (!empty($_POST['columns'][3]['search']['value'])) {
      $search_category = $_POST['columns'][3]['search']['value'];

      $this->db->where('tb_receipts.category', $search_category);
    }

    if (!empty($_POST['columns'][4]['search']['value'])) {
      $search_warehouse = $_POST['columns'][4]['search']['value'];

      $this->db->where('tb_receipts.warehouse', $search_warehouse);
    }

    if (!empty($_POST['columns'][5]['search']['value'])) {
      $search_description = $_POST['columns'][5]['search']['value'];

      $this->db->like('UPPER(tb_master_items.description)', strtoupper($search_description));
    }

    if (!empty($_POST['columns'][6]['search']['value'])) {
      $search_part_number = $_POST['columns'][6]['search']['value'];

      $this->db->like('UPPER(tb_master_items.part_number)', strtoupper($search_part_number));
    }

    if (!empty($_POST['columns'][9]['search']['value'])) {
      $search_condition = $_POST['columns'][9]['search']['value'];

      $this->db->like('UPPER(tb_stocks.condition)', strtoupper($search_condition));
    }

    if (!empty($_POST['columns'][15]['search']['value'])) {
      $search_received_from = $_POST['columns'][15]['search']['value'];

      $this->db->like('UPPER(tb_receipts.received_from)', strtoupper($search_received_from));
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
    $this->db->from('tb_receipts');
    $this->db->join('tb_receipt_items', 'tb_receipt_items.document_number = tb_receipts.document_number');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_receipt_items.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    // $this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_in_stores.serial_id', 'left');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where_in('tb_receipts.category', config_item('auth_inventory'));
    $this->db->like('tb_receipts.document_number', 'GRN');

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
    $this->db->from('tb_receipts');
    $this->db->join('tb_receipt_items', 'tb_receipt_items.document_number = tb_receipts.document_number');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_receipt_items.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    // $this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_in_stores.serial_id', 'left');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->where_in('tb_receipts.category', config_item('auth_inventory'));
    $this->db->like('tb_receipts.document_number', 'GRN');

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->from('tb_receipts');
    $this->db->join('tb_receipt_items', 'tb_receipt_items.document_number = tb_receipts.document_number');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_receipt_items.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    // $this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_in_stores.serial_id', 'left');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->where_in('tb_receipts.category', config_item('auth_inventory'));
    $this->db->like('tb_receipts.document_number', 'GRN');

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function findById($id)
  {
    $this->db->where('id', $id);

    $query    = $this->db->get('tb_receipts');
    $receipt = $query->unbuffered_row('array');

    $select = array(
      'tb_stock_in_stores.*',
      'tb_stocks.condition',
      'tb_stock_in_stores.stores',
      'tb_master_items.serial_number',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.alternate_part_number',
      'tb_master_items.unit as unit_pakai',
      'tb_master_items.unit',
      'tb_master_items.group',
      'tb_master_items.minimum_quantity',
      'tb_receipt_items.id as receipt_items_id', //tambahan
      'tb_receipt_items.received_quantity',
      'tb_receipt_items.received_unit_value',
      'tb_receipt_items.received_unit_value_dollar',
      'tb_receipt_items.purchase_order_number',
      'tb_receipt_items.purchase_order_item_id',
      'tb_receipt_items.reference_number',
      'tb_receipt_items.awb_number',
      'tb_receipt_items.remarks',
      'tb_receipt_items.kode_akunting',
      'tb_receipt_items.quantity_order',
      'tb_receipt_items.value_order',
      'tb_receipt_items.isi',
      //tambahan
      // 'tb_master_items.unit_pakai',
      'tb_master_items.kode_stok',
      'tb_master_items.qty_konversi',
      'tb_receipt_items.received_unit',
      'tb_receipt_items.stock_in_stores_id',
    );

    $this->db->select($select);
    $this->db->from('tb_receipt_items');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_receipt_items.stock_in_stores_id');
    // $this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_in_stores.serial_id', 'left');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    // $this->db->join('tb_master_item_konversi', 'tb_master_item_konversi.item_id = tb_master_items.id');//tambahan
    $this->db->where('tb_receipt_items.document_number', $receipt['document_number']);

    $query = $this->db->get();

    foreach ($query->result_array() as $key => $value) {
      $receipt['items'][$key] = $value;

      if (empty($receipt['category'])) {
        $this->db->select('category');
        $this->db->from('tb_master_item_groups');
        $this->db->where('group', $value['group']);

        $query = $this->db->get();
        $icat  = $query->unbuffered_row();

        $receipt['category'] = $icat->category;
      }
    }

    return $receipt;
  }

  public function isDocumentNumberExists($document_number)
  {
    $this->db->where('document_number', $document_number);
    $query = $this->db->get('tb_receipts');

    if ($query->num_rows() > 0)
      return true;

    return false;
  }

  public function isValidDocumentQuantity($document_number)
  {
    $this->db->select_sum('tb_receipt_items.received_quantity', 'received_quantity');
    $this->db->select_sum('tb_stock_in_stores.quantity', 'stored_quantity');
    $this->db->select('tb_receipt_items.document_number');
    $this->db->from('tb_receipt_items');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_receipt_items.stock_in_stores_id');
    $this->db->where('tb_receipt_items.document_number', $document_number);
    $this->db->group_by('tb_receipt_items.document_number');

    $query  = $this->db->get();
    $row    = $query->unbuffered_row('array');

    if ($row['received_quantity'] === $row['received_quantity'])
      return true;

    return false;
  }

  public function save()
  {
    $this->db->trans_begin();

    // DELETE OLD DOCUMENT
    if (isset($_SESSION['receipt']['id'])) {
      $id = $_SESSION['receipt']['id'];

      $this->db->select('document_number, warehouse,received_date');
      $this->db->where('id', $id);
      $this->db->from('tb_receipts');

      $query = $this->db->get();
      $row   = $query->unbuffered_row('array');

      $document_number  = $row['document_number'];
      $warehouse        = $row['warehouse'];
      // $received_date        = $row['received_date'];

      $old_document_number  = $row['document_number'];
      $old_warehouse        = $row['warehouse'];

      $this->db->select('tb_receipt_items.quantity_order, tb_receipt_items.id, tb_receipt_items.stock_in_stores_id, tb_receipt_items.received_quantity, tb_receipt_items.received_unit_value, tb_stock_in_stores.stock_id, tb_stock_in_stores.serial_id, tb_stock_in_stores.stores,tb_receipt_items.purchase_order_item_id');
      $this->db->from('tb_receipt_items');
      $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_receipt_items.stock_in_stores_id');
      $this->db->where('tb_receipt_items.document_number', $old_document_number);

      $query  = $this->db->get();
      $result = $query->result_array();

      foreach ($result as $data) {
        // $prev_old_stock = getStockActive($data['stock_id']);
        // $next_old_stock = floatval($prev_old_stock->total_quantity) - floatval($data['received_quantity']);

        $prev_old_stock = getStockPrev($data['stock_id'], $data['stores']);
        $next_old_stock = floatval($prev_old_stock) - floatval($data['received_quantity']);

        $this->db->set('stock_id', $data['stock_id']);
        $this->db->set('serial_id', $data['serial_id']);
        $this->db->set('warehouse', $old_warehouse);
        $this->db->set('stores', $data['stores']);
        $this->db->set('date_of_entry', $row['received_date']);
        $this->db->set('period_year', config_item('period_year'));
        $this->db->set('period_month', config_item('period_month'));
        $this->db->set('document_type', 'REVISION');
        $this->db->set('document_number', $old_document_number);
        $this->db->set('issued_to', $old_document_number);
        $this->db->set('issued_by', config_item('auth_person_name'));
        $this->db->set('remarks', 'REVISION');
        $this->db->set('prev_quantity', floatval($prev_old_stock));
        $this->db->set('balance_quantity', $next_old_stock);
        $this->db->set('quantity', 0 - floatval($data['received_quantity']));
        $this->db->set('unit_value', floatval($data['received_unit_value']));
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('stock_in_stores_id', $data['stock_in_stores_id']);
        $this->db->set('doc_type', 5);
        $this->db->set('tgl', date('Ymd', strtotime($row['received_date'])));
        $this->db->set('total_value', floatval($data['received_unit_value'] * (0 - floatval($data['received_quantity']))));
        $this->db->insert('tb_stock_cards');

        if ($data['purchase_order_item_id'] != null) {
          $this->db->where('id', $data['purchase_order_item_id']);
          $this->db->set('left_received_quantity', 'left_received_quantity +' . $data['quantity_order'], FALSE);
          $this->db->set('quantity_received', 'quantity_received - ' . $data['quantity_order'], FALSE);
          $this->db->update('tb_po_item');
        }

        $this->db->where('id', $data['id']);
        $this->db->delete('tb_receipt_items');

        $this->db->where('id', $data['stock_in_stores_id']);
        $this->db->delete('tb_stock_in_stores');
      }

      $this->db->where('id', $id);
      $this->db->delete('tb_receipts');

      $this->db->where('no_grn', $document_number);
      $this->db->delete('tb_hutang');

      $id_jurnal = getIdJurnal($document_number);

      $this->db->where('id_jurnal', $id_jurnal);
      $this->db->delete('tb_jurnal_detail');

      $this->db->where('id', $id_jurnal);
      $this->db->delete('tb_jurnal');
    }


    // CREATE NEW DOCUMENT
    $document_id      = (isset($_SESSION['receipt']['id'])) ? $_SESSION['receipt']['id'] : NULL;
    $document_edit    = (isset($_SESSION['receipt']['edit'])) ? $_SESSION['receipt']['edit'] : NULL;
    $document_number  = sprintf('%06s', $_SESSION['receipt']['document_number']) . receipt_format_number();
    $received_date    = $_SESSION['receipt']['received_date'];
    $received_by      = (empty($_SESSION['receipt']['received_by'])) ? NULL : $_SESSION['receipt']['received_by'];
    $received_from    = (empty($_SESSION['receipt']['received_from'])) ? NULL : $_SESSION['receipt']['received_from'];
    $known_by         = (empty($_SESSION['receipt']['known_by'])) ? NULL : $_SESSION['receipt']['known_by'];
    $approved_by      = (empty($_SESSION['receipt']['approved_by'])) ? NULL : $_SESSION['receipt']['approved_by'];
    $warehouse        = $_SESSION['receipt']['warehouse'];
    $category         = $_SESSION['receipt']['category'];
    $notes            = (empty($_SESSION['receipt']['notes'])) ? NULL : $_SESSION['receipt']['notes'];
    $kurs = tgl_kurs($received_date);

    $this->db->set('document_number', $document_number);
    $this->db->set('received_from', $received_from);
    $this->db->set('received_date', $received_date);
    $this->db->set('received_by', $received_by);
    $this->db->set('known_by', $known_by);
    $this->db->set('approved_by', $approved_by);
    $this->db->set('category', $category);
    $this->db->set('warehouse', $warehouse);
    $this->db->set('notes', $notes);
    $this->db->set('created_by', config_item('auth_person_name'));
    $this->db->set('updated_by', config_item('auth_person_name'));
    $this->db->insert('tb_receipts');

    // PROCESSING RECEIPT ITEMS
    $received_total_value = 0;

    //insert tb_jurnal
    $this->db->set('no_jurnal', $document_number);
    $this->db->set('vendor', $received_from);
    $this->db->set('tanggal_jurnal', $received_date);
    $this->db->set('grn_no', $document_number);    
    $this->db->set('keterangan', "Purchase ". $received_from);
    $this->db->set('source', "INV-IN");
    $this->db->insert('tb_jurnal');
    $id_jurnal = $this->db->insert_id();
    //end insert tb_jurnal

    // PROCESSING RECEIPT ITEMS
    foreach ($_SESSION['receipt']['items'] as $key => $data) {
      $serial_number = (empty($data['serial_number'])) ? NULL : $data['serial_number'];

      /**
       * CREATE UNIT OF MEASUREMENT IF NOT EXISTS
       */
      if (isItemUnitExists($data['unit']) === FALSE) {
        $this->db->set('unit', strtoupper($data['unit']));
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->insert('tb_master_item_units');
      }

      if (!empty($data['unit_pakai'])) {
        if (isItemUnitExists($data['unit_pakai']) === FALSE) {
          $this->db->set('unit', strtoupper($data['unit_pakai']));
          $this->db->set('created_by', config_item('auth_person_name'));
          $this->db->set('updated_by', config_item('auth_person_name'));
          $this->db->insert('tb_master_item_units');
        }
      }

      /**
       * CREATE STORES IF NOT EXISTS
       */
      if (isStoresExists($data['stores']) === FALSE && isStoresExists($data['stores'], $category) === FALSE) {
        $this->db->set('stores', strtoupper($data['stores']));
        $this->db->set('warehouse', $warehouse);
        $this->db->set('category', $category);
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->insert('tb_master_stores');
      }

      /**
       * CREATE ITEM IF NOT EXISTS
       */
      if (isItemExists($data['part_number'], $serial_number) === FALSE) {
        $this->db->set('part_number', strtoupper($data['part_number']));
        $this->db->set('serial_number', strtoupper($data['serial_number']));
        $this->db->set('alternate_part_number', strtoupper($data['alternate_part_number']));
        $this->db->set('description', strtoupper($data['description']));
        $this->db->set('group', strtoupper($data['group']));
        $this->db->set('minimum_quantity', floatval($data['minimum_quantity']));
        // $this->db->set('unit', strtoupper($data['unit']));
        $this->db->set('kode_stok', strtoupper($data['kode_stok']));
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        if (!empty($data['unit_pakai'])) {
          $this->db->set('unit', strtoupper($data['unit_pakai']));
          $this->db->set('unit_pakai', $data['unit']);
          $this->db->set('qty_konversi', $data['isi']);
          // $qty_konvers = floatval($data['received_quantity']) * floatval($data['isi']);
          $qty_konvers = floatval($data['received_quantity']);
        } else {
          $this->db->set('unit', strtoupper($data['unit']));

          $qty_konvers = floatval($data['received_quantity']);
        }
        $this->db->insert('tb_master_items');

        $item_id = $this->db->insert_id();
      } else {
        $item_id = getItemId($data['part_number'], $serial_number);
        if (!empty($data['unit_pakai']) && !empty($data['isi'])) {
          // $qty_konvers = floatval($data['received_quantity']) * floatval($data['isi']);
          $qty_konvers = floatval($data['received_quantity']);
        } else {
          $qty_konvers = floatval($data['received_quantity']);
        }
      }

      /**
       * CREATE part number IF NOT EXISTS in tb master part number
       */

      if (isPartNumberExists($data['part_number']) === FALSE){
        $this->db->set('part_number', strtoupper($data['part_number']));
        $this->db->set('min_qty', $data['minimum_quantity']);        
        // $this->db->set('item_id', $item_id);        
        $this->db->set('qty', $data['received_quantity']);
        $this->db->set('description', strtoupper($data['description']));
        $this->db->set('unit', strtoupper($data['unit']));
        $this->db->insert('tb_master_part_number');
      }
      // else{
      //   if (isset($_SESSION['receipt']['id'])){

      //   }else{
      //     $qty_awal = getPartnumberQty($data['part_number']);

      //     $qty_baru = floatval($data['received_quantity']) + floatval($qty_awal);

      //     $this->db->set('qty', $qty_baru);
      //     $this->db->where('part_number', strtoupper($data['part_number']));
      //     $this->db->update('tb_master_part_number');
      //   }
      // }




      /**
       * CREATE SERIAL NUMBER IF NOT EXISTS
       */
      if (!empty($data['serial_number'])) {
        if (isSerialExists($item_id, $data['serial_number']) === FALSE) {
          $this->db->set('item_id', $item_id);
          $this->db->set('serial_number', strtoupper($data['serial_number']));
          $this->db->set('warehouse', $warehouse);
          $this->db->set('stores', strtoupper($data['stores']));
          $this->db->set('condition', strtoupper($data['condition']));
          $this->db->set('reference_document', $document_number);
          $this->db->set('updated_by', config_item('auth_person_name'));
          $this->db->insert('tb_master_item_serials');

          $serial_id  = $this->db->insert_id();
        } else {
          $serial     = getSerial($item_id, $data['serial_number']);
          $serial_id  = $serial->id;

          $this->db->set('quantity', 1);
          $this->db->set('warehouse', $warehouse);
          $this->db->set('stores', strtoupper($data['stores']));
          $this->db->set('condition', strtoupper($data['condition']));
          $this->db->set('updated_by', config_item('auth_person_name'));
          $this->db->set('reference_document', $document_number);
          $this->db->where('id', $serial_id);
          $this->db->update('tb_master_item_serials');
        }
      } else {
        $serial_id = NULL;
      }

      /**
       * ADD ITEM INTO STOCK
       */
      if (isStockExists($item_id, strtoupper($data['condition']))) {
        $stock_id = getStockId($item_id, strtoupper($data['condition']));
        /**
         * CREATE STOCK CARD
         */
        // $prev_stock   = getStockActive($stock_id);
        $prev_stock   = getStockPrev($stock_id, strtoupper($data['stores']));
        $next_stock   = floatval($prev_stock) + floatval($data['received_quantity']);

        // if (!isset($_SESSION['receipt']['id'])){
        $this->db->set('serial_id', $serial_id);
        $this->db->set('stock_id', $stock_id);
        $this->db->set('warehouse', $warehouse);
        $this->db->set('stores', strtoupper($data['stores']));
        $this->db->set('date_of_entry', $received_date);
        $this->db->set('period_year', config_item('period_year'));
        $this->db->set('period_month', config_item('period_month'));
        $this->db->set('document_type', 'RECEIPT');
        $this->db->set('document_number', $document_number);
        $this->db->set('received_from', $received_from);
        $this->db->set('received_by', $received_by);
        $this->db->set('prev_quantity', floatval($prev_stock));
        $this->db->set('balance_quantity', $next_stock);
        $this->db->set('quantity', floatval($qty_konvers));
        if ($data['kurs'] == 'rupiah' || $data['kurs_dollar'] == 1) {
          $this->db->set('unit_value', floatval($data['received_unit_value']));
        } else {
          // $this->db->set('unit_value_dollar', floatval($data['received_unit_value']));
          $this->db->set('unit_value', floatval($kurs) * floatval($data['received_unit_value']));
        }
        // $this->db->set('kurs_dollar', floatval($kurs));
        $this->db->set('remarks', $data['remarks']);
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('doc_type', 5);
        $this->db->set('tgl', date('Ymd', strtotime($received_date)));
        $this->db->insert('tb_stock_cards');
        $stock_card_id = $this->db->insert_id();
        // }


      } else {
        $this->db->set('item_id', $item_id);
        $this->db->set('condition', strtoupper($data['condition']));
        $this->db->set('initial_total_quantity', floatval($data['received_quantity']));
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->insert('tb_stocks');

        $stock_id = $this->db->insert_id();

        /**
         * CREATE STOCK CARD
         */
        // $prev_stock   = getStockActive($stock_id);
        // $next_stock   = floatval($prev_stock->total_quantity) + floatval($data['received_quantity']);

        // if (!isset($_SESSION['receipt']['id'])){
        $this->db->set('serial_id', $serial_id);
        $this->db->set('stock_id', $stock_id);
        $this->db->set('warehouse', $warehouse);
        $this->db->set('stores', strtoupper($data['stores']));
        $this->db->set('date_of_entry', $received_date);
        $this->db->set('period_year', config_item('period_year'));
        $this->db->set('period_month', config_item('period_month'));
        $this->db->set('document_type', 'RECEIPT');
        $this->db->set('document_number', $document_number);
        $this->db->set('received_from', $received_from);
        $this->db->set('received_by', $received_by);
        $this->db->set('prev_quantity', floatval(0));
        $this->db->set('balance_quantity', floatval(0) + floatval($data['received_quantity']));
        $this->db->set('quantity', floatval($qty_konvers));
        if ($data['kurs'] == 'rupiah' || $data['kurs_dollar'] == 1) {
          $this->db->set('unit_value', floatval($data['received_unit_value']));
        } else {
          // $this->db->set('unit_value_dollar', floatval($data['received_unit_value']));
          $this->db->set('unit_value', floatval($kurs) * floatval($data['received_unit_value']));
        }
        // $this->db->set('kurs_dollar', floatval($kurs));
        $this->db->set('remarks', $data['remarks']);
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('doc_type', 5);
        $this->db->set('tgl', date('Ymd', strtotime($received_date)));
        $this->db->insert('tb_stock_cards');
        $stock_card_id = $this->db->insert_id();
        // }
      }

      $base = ['WISNU' => 1, 'BANYUWANGI' => 2, 'SOLO' => 3, 'LOMBOK' => 4, 'JEMBER' => 5, 'PALANGKARAYA' => 6, 'WISNU REKONDISI' => 7, 'BSR REKONDISI' => 8,];
      $warehouse_id = $base[$warehouse];
      $date = date('Y-m-d');
      $kurs = tgl_kurs($received_date);


      // ADD to STORES

      $this->db->set('stock_id', $stock_id);
      $this->db->set('serial_id', $serial_id);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('stores', strtoupper($data['stores']));
      $this->db->set('initial_quantity', floatval($data['received_quantity']));
      // $this->db->set('quantity', floatval($data['received_quantity']));
      $this->db->set('quantity', floatval($qty_konvers));


      if ($data['kurs_dollar'] == 1 || $data['kurs'] == 'rupiah') {
        $this->db->set('unit_value', floatval($data['received_unit_value']));
        $this->db->set('initial_unit_value', floatval($data['received_unit_value']));
        $this->db->set('unit_value_dollar', floatval($data['received_unit_value']) / floatval($kurs));
        $this->db->set('initial_unit_value_dollar', floatval($data['received_unit_value']) / floatval($kurs));
        $this->db->set('kurs_dollar', 1);
        $harga = $data['received_unit_value'];
        $currency = 'IDR';
        $harga_usd = floatval($data['received_unit_value']) / floatval($kurs);

        // $this->db->set('current_price',floatval($data['received_unit_value']));
        // $this->db->where('id',$item_id);
        // $this->db->update('tb_master_items');
      } else {
        if ($data['kurs'] == 'dollar') {
          $this->db->set('unit_value_dollar', floatval($data['received_unit_value']));
          $this->db->set('unit_value', floatval($kurs) * floatval($data['received_unit_value']));
          $this->db->set('initial_unit_value_dollar', floatval($data['received_unit_value']));
          $this->db->set('kurs_dollar', floatval($kurs));
          $this->db->set('initial_unit_value', floatval($kurs) * floatval($data['received_unit_value']));
          $harga = floatval($kurs) * floatval($data['received_unit_value']);
          $currency = 'USD';
          $harga_usd = floatval($data['received_unit_value']);
          // $this->db->set('current_price',floatval($harga));
          // $this->db->where('id',$item_id);
          // $this->db->update('tb_master_items');

        } else {

          $this->db->set('unit_value_dollar', floatval($data['received_unit_value_dollar']));
          $this->db->set('unit_value', floatval($data['received_unit_value']));
          $this->db->set('initial_unit_value_dollar', floatval($data['received_unit_value_dollar']));
          $this->db->set('kurs_dollar', floatval($data['kurs_dollar']));
          $this->db->set('initial_unit_value', floatval($kurs) * floatval($data['received_unit_value']));
          $harga = floatval($data['received_unit_value']);
          $currency = 'IDR';
          $harga_usd = floatval($data['received_unit_value_dollar']);
          // $this->db->set('current_price',floatval($harga));
          // $this->db->where('id',$item_id);
          // $this->db->update('tb_master_items');

        }
      }

      $this->db->set('reference_document', $document_number);
      $this->db->set('received_date', $received_date);
      if ($data['no_expired_date'] !== 'no') {
        $this->db->set('no_expired_date', 'yes');
      } else {
        $this->db->set('no_expired_date', 'no');
      }
      if (!empty($data['expired_date'])) {
        $this->db->set('expired_date', $data['expired_date']);
      }
      $this->db->set('received_by', $received_by);
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->set('remarks', $data['remarks']);
      $this->db->set('qty_konvers', floatval($qty_konvers));
      $this->db->set('warehouse_id', $warehouse_id);
      $this->db->insert('tb_stock_in_stores');

      $stock_in_stores_id = $this->db->insert_id();

      $this->db->set('stock_in_stores_id', $stock_in_stores_id);
      $this->db->set('unit_value', $harga);
      $this->db->set('total_value', floatval($qty_konvers) * floatval($harga));
      $this->db->where('id', $stock_card_id);
      $this->db->update('tb_stock_cards');

      $this->db->set('current_price', floatval($harga));
      $this->db->where('id', $item_id);
      $this->db->update('tb_master_items');

      // }


      /**
       * INSERT INTO RECEIPT ITEMS
       */
      $uang_muka = 0;
      if (!empty($data['purchase_order_item_id'])) {
        $this->db->from('tb_po_item');
        $this->db->where('tb_po_item.id', $data['purchase_order_item_id']);

        $query  = $this->db->get();
        $row    = $query->unbuffered_row('array');
        $qty    = floatval($row['left_received_quantity']) - floatval($data['quantity_order']);

        $this->db->where('id', $data['purchase_order_item_id']);
        $this->db->set('left_received_quantity', 'left_received_quantity -' . $data['quantity_order'], FALSE);
        $this->db->set('quantity_received', 'quantity_received + ' . $data['quantity_order'], FALSE);
        $this->db->update('tb_po_item');

        $left_qty_po = leftQtyPo($row['purchase_order_id']);
        $left_amount_po = leftAmountPo($row['purchase_order_id']);
        $uang_muka = $row['uang_muka'];
        if ($left_qty_po == 0) {
          $this->db->where('id', $row['purchase_order_id']);
          $this->db->set('status', 'OPEN');
          $this->db->update('tb_po');
        }
        if ($left_qty_po == 0 && $left_amount_po == 0) {
          $this->db->where('id', $row['purchase_order_id']);
          $this->db->set('status', 'CLOSED');
          $this->db->update('tb_po');
        }
      }

      if (!empty($data['purchase_order_item_id'])) {
        $this->db->set('purchase_order_item_id', $data['purchase_order_item_id']);
      }
      $this->db->set('document_number', $document_number);
      $this->db->set('stock_in_stores_id', $stock_in_stores_id);
      $this->db->set('received_quantity', floatval($qty_konvers));
      if ($data['kurs_dollar'] == 1 || $data['kurs'] == 'rupiah') {
        $this->db->set('received_unit_value', floatval($data['received_unit_value']));
        $this->db->set('received_total_value', floatval($data['received_unit_value']) * floatval($qty_konvers));
        $received_total_value +=  floatval($data['received_unit_value']) * floatval($qty_konvers);
        $x = floatval($data['received_unit_value']) * floatval($qty_konvers);
        $this->db->set('kurs_dollar', 1);
      } else {
        if ($data['kurs'] == 'dollar') {
          $this->db->set('received_unit_value_dollar', floatval($data['received_unit_value']));
          $this->db->set('received_unit_value', floatval($kurs) * floatval($data['received_unit_value']));
          $this->db->set('received_total_value', floatval($kurs) * floatval($data['received_unit_value']) * floatval($data['received_quantity']));
          $received_total_value += floatval($kurs) * floatval($data['received_unit_value']) * floatval($data['received_quantity']);
          $x = floatval($kurs) * floatval($data['received_unit_value']) * floatval($data['received_quantity']);
          $this->db->set('received_total_value_dollar', floatval($data['received_unit_value']) * floatval($data['received_quantity']));
          $this->db->set('kurs_dollar', floatval($kurs));
        } else {
          $this->db->set('received_unit_value_dollar', floatval($data['received_unit_value_dollar']));
          $this->db->set('received_unit_value', floatval($data['received_unit_value']));
          $this->db->set('received_total_value', floatval($data['received_unit_value']) * floatval($data['received_quantity']));
          $received_total_value += floatval($data['received_unit_value']) * floatval($data['received_quantity']);
          $x = floatval($data['received_unit_value']) * floatval($data['received_quantity']);
          $this->db->set('received_total_value_dollar', floatval($data['received_unit_value_dollar']) * floatval($data['received_quantity']));
          $this->db->set('kurs_dollar', floatval($data['kurs_dollar']));
        }
      }

      $this->db->set('purchase_order_number', $data['purchase_order_number']);
      $this->db->set('reference_number', $data['reference_number']);
      $this->db->set('awb_number', $data['awb_number']);
      $this->db->set('remarks', $data['remarks']);
      $this->db->set('received_date_item', $received_date);
      $this->db->set('quantity_order', floatval($data['quantity_order']));
      $this->db->set('value_order', floatval($data['value_order']));
      $this->db->set('isi', floatval($data['isi']));
      if (!empty($data['unit_pakai']) && !empty($data['isi'])) {
        $this->db->set('received_unit', strtoupper($data['received_unit']));
      } else {
        $this->db->set('received_unit', strtoupper($data['received_unit']));
      }

      // $this->db->set('group', strtoupper($data['group']));
      // $this->db->set('doc_type', 3);     
      $this->db->insert('tb_receipt_items');
      if ($currency == 'IDR') {
        $id_master_akun = 1;
        // $kode = "2-101";
        // $x_total = $harga*floatval($data['received_quantity']);
      } else {
        $id_master_akun = 1;
        // $kode = "2-1102";
        // $x_total = $harga_usd*floatval($data['received_quantity']);
      }

      $akun_payable = get_set_up_akun($id_master_akun);
      $coa = $this->coaByGroup(strtoupper($data['group']));
      $this->db->set('id_jurnal', $id_jurnal);
      $this->db->set('jenis_transaksi', $data['group']);
      $this->db->set('trs_debet', $harga * floatval($data['received_quantity']));
      $this->db->set('trs_kredit', 0);
      $this->db->set('trs_debet_usd', $harga_usd * floatval($data['received_quantity']));
      $this->db->set('trs_kredit_usd', 0);
      $this->db->set('kode_rekening', $coa->coa);
      $this->db->set('stock_in_stores_id', $stock_in_stores_id);
      $this->db->set('currency', $currency);
      $this->db->set('kode_rekening_lawan', $akun_payable->coa);
      $this->db->insert('tb_jurnal_detail');

      $this->db->set('id_jurnal', $id_jurnal);
      $this->db->set('jenis_transaksi', strtoupper($akun_payable->group));
      $this->db->set('trs_debet', 0);
      $this->db->set('trs_kredit', $harga * floatval($data['received_quantity']));
      $this->db->set('trs_debet_usd', 0);
      $this->db->set('trs_kredit_usd', $harga_usd * floatval($data['received_quantity']));
      $this->db->set('kode_rekening', $akun_payable->coa);
      $this->db->set('stock_in_stores_id', $stock_in_stores_id);
      $this->db->set('currency', $currency);
      $this->db->set('kode_rekening_lawan', $coa->coa);
      $this->db->insert('tb_jurnal_detail');

      $this->db->set('document_no', $this->ap_last_number());
      $this->db->set('tanggal', date("Y-m-d"));
      $this->db->set('no_grn', $document_number);
      $this->db->set('vendor', $received_from);
      $this->db->set('amount_idr', $harga * floatval($data['received_quantity']));
      $this->db->set('amount_usd', $harga_usd * floatval($data['received_quantity']));
      $this->db->set('payment', 0);
      $this->db->set('no_po', $data['purchase_order_number']);
      if (!empty($data['purchase_order_item_id'])) {
        $this->db->set('id_po', $row['purchase_order_id']);
        $this->db->set('id_po_item', $data['purchase_order_item_id']);
      }      
      $this->db->set('currency', $currency);
      $this->db->set('status', "waiting for payment");
      $this->db->set('stock_in_stores_id', $stock_in_stores_id);
      $this->db->insert('tb_hutang');

      $this->db->set('current_price',floatval($harga));
      $this->db->where('id',$item_id);
      $this->db->update('tb_master_items');
    } //end foreach items




    // $this->db->set('id_jurnal',$id_jurnal);
    // $this->db->set('jenis_transaksi',strtoupper("kredit"));
    // $this->db->set('trs_debet',0);
    // $this->db->set('trs_kredit',$received_total_value);
    // $this->db->set('kode_rekening','2-1101');
    // $this->db->insert('tb_jurnal_detail');


    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function delete()
  {
    $this->db->trans_begin();

    $id = $this->input->post('id');

    $this->db->select('document_number, warehouse, received_date');
    $this->db->where('id', $id);
    $this->db->from('tb_receipts');

    $query = $this->db->get();
    $row   = $query->unbuffered_row('array');

    $document_number  = $row['document_number'];
    $warehouse        = $row['warehouse'];

    $this->db->select('tb_receipt_items.purchase_order_item_id, tb_receipt_items.quantity_order, tb_receipt_items.id, tb_receipt_items.stock_in_stores_id, tb_receipt_items.received_quantity, tb_receipt_items.received_unit_value, tb_stock_in_stores.stock_id, tb_stock_in_stores.serial_id, tb_stock_in_stores.stores');
    $this->db->from('tb_receipt_items');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_receipt_items.stock_in_stores_id');
    $this->db->where('tb_receipt_items.document_number', $document_number);

    $query  = $this->db->get();
    $result = $query->result_array();

    foreach ($result as $data) {
      // $prev_old_stock = getStockActive($data['stock_id']);
      // $next_old_stock = floatval($prev_old_stock->total_quantity) - floatval($data['issued_quantity']);

      $prev_old_stock = getStockPrev($data['stock_id'], $data['stores']);
      $next_old_stock = floatval($prev_old_stock) - floatval($data['issued_quantity']);

      $this->db->set('stock_id', $data['stock_id']);
      $this->db->set('serial_id', $data['serial_id']);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('stores', $data['stores']);
      $this->db->set('date_of_entry', $row['received_date']);
      $this->db->set('period_year', config_item('period_year'));
      $this->db->set('period_month', config_item('period_month'));
      $this->db->set('document_type', 'REMOVAL');
      $this->db->set('document_number', $document_number);
      $this->db->set('issued_to', $document_number);
      $this->db->set('issued_by', config_item('auth_person_name'));
      $this->db->set('remarks', 'DELETE DOCUMENT');
      $this->db->set('prev_quantity', floatval($prev_old_stock));
      $this->db->set('balance_quantity', $next_old_stock);
      $this->db->set('quantity', 0 - floatval($data['received_quantity']));
      $this->db->set('unit_value', floatval($data['received_unit_value']));
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->set('stock_in_stores_id', $data['stock_in_stores_id']);
      $this->db->set('doc_type', 5);
      $this->db->set('tgl', date('Ymd', strtotime($row['received_date'])));
      $this->db->set('total_value', floatval($data['received_unit_value'] * (0 - floatval($data['received_quantity']))));
      $this->db->insert('tb_stock_cards');

      if ($data['purchase_order_item_id'] != null) {
        $this->db->where('id', $data['purchase_order_item_id']);
        $this->db->set('left_received_quantity', 'left_received_quantity +' . $data['quantity_order'], FALSE);
        $this->db->set('quantity_received', 'quantity_received - ' . $data['quantity_order'], FALSE);
        $this->db->update('tb_po_item');
      }

      $this->db->where('id', $data['id']);
      $this->db->delete('tb_receipt_items');

      $this->db->where('id', $data['stock_in_stores_id']);
      $this->db->delete('tb_stock_in_stores');
    }

    $this->db->where('id', $id);
    $this->db->delete('tb_receipts');

    $this->db->where('no_grn', $document_number);
    $this->db->delete('tb_hutang');

    $id_jurnal = getIdJurnal($document_number);

    $this->db->where('id_jurnal', $id_jurnal);
    $this->db->delete('tb_jurnal_detail');

    $this->db->where('id', $id_jurnal);
    $this->db->delete('tb_jurnal');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function searchPurchaseOrder($category, $vendor = NULL)
  {
    $this->column_select = array(
      'tb_po_item.*',
      'tb_po.vendor',
      'tb_po.document_number',
      'tb_po.default_currency',
      'tb_po.exchange_rate',
      'tb_master_items.group',
      'tb_master_items.kode_stok',
      'tb_master_items.unit as unit_pakai',
    );

    $this->db->select($this->column_select);
    $this->db->from('tb_po_item');
    $this->db->join('tb_po', 'tb_po.id = tb_po_item.purchase_order_id');
    $this->db->join('tb_master_items', 'tb_master_items.part_number = tb_po_item.part_number', 'left');
    $this->db->join('tb_master_item_groups', 'tb_master_items.group = tb_master_item_groups.group', 'left');
    // $this->db->where('tb_master_item_groups.category', $category);
    $this->db->where_in('tb_po.status', ['ORDER', 'OPEN', 'ADVANCE']);
    $this->db->where('tb_po_item.left_received_quantity > ', 0);

    if ($vendor !== NULL || !empty($vendor)) {
      $this->db->where('tb_po.vendor', $vendor);
    }

    $this->db->order_by('tb_po_item.part_number ASC, tb_po_item.description ASC');
    $query  = $this->db->get();
    $result = $query->result_array();

    return $result;
  }

  public function searchItemsBySerial($category)
  {
    $this->column_select = array(
      'tb_master_items.serial_number',
      'tb_master_items.id',
      'tb_master_items.group',
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.alternate_part_number',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit',
      'tb_master_items.kode_stok',
    );

    $this->db->select($this->column_select);
    $this->db->from('tb_master_items');
    $this->db->join('tb_stocks', 'tb_stocks.item_id = tb_master_items.id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_master_items.serial_number IS NOT NULL', NULL, FALSE);
    $this->db->where('tb_stocks.total_quantity', 0);
    $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
    $this->db->where('tb_master_item_groups.category', $category);

    $this->db->order_by('tb_master_items.serial_number ASC');

    $query  = $this->db->get();
    $result = $query->result_array();

    return $result;
  }

  public function searchItemsByPartNumber($category)
  {
    $this->column_select = array(
      'tb_master_items.id',
      'tb_master_items.group',
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.alternate_part_number',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit',
      'tb_master_items.kode_stok'
    );

    $this->db->select($this->column_select);
    $this->db->from('tb_master_items');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
    $this->db->where('tb_master_item_groups.category', $category);

    $this->db->order_by('tb_master_items.group ASC, tb_master_items.description ASC');

    $query  = $this->db->get();
    $result = $query->result_array();

    return $result;
  }

  //tambahan

  public function getDocumentId($document_number)
  {
    $this->db->select('tb_receipts.id');
    $this->db->from('tb_receipts');
    $this->db->where('UPPER(tb_receipts.document_number)', strtoupper($document_number));

    $query  = $this->db->get();
    $row    = $query->unbuffered_row();

    return $row->id;
  }

  public function findItemById($id)
  {

    $select = array(
      'tb_stock_in_stores.*',
      'tb_stocks.condition',
      'tb_stock_in_stores.stores',
      'tb_master_items.id', //tambahan
      'tb_master_items.serial_number',
      'tb_master_items.part_number',
      'tb_master_items.description',
      'tb_master_items.alternate_part_number',
      'tb_master_items.unit',
      'tb_master_items.kode_stok',
      'tb_master_items.group',
      'tb_master_items.minimum_quantity', //tambahan
      'tb_receipts.category', //tambahan
      'tb_receipts.warehouse', //tambahan
      'tb_receipt_items.id', //tambahan
      'tb_receipt_items.document_number', //tambahan
      'tb_receipt_items.received_quantity',
      'tb_receipt_items.received_unit_value',
      'tb_receipt_items.purchase_order_number',
      'tb_receipt_items.reference_number',
      'tb_receipt_items.awb_number',
      'tb_receipt_items.remarks',
    );

    $this->db->select($select);
    $this->db->from('tb_receipt_items');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_receipt_items.stock_in_stores_id');
    // $this->db->join('tb_master_item_serials', 'tb_master_item_serials.id = tb_stock_in_stores.serial_id', 'left');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_receipts', 'tb_receipts.document_number = tb_receipt_items.document_number');
    $this->db->where('tb_receipt_items.id', $id);

    $query = $this->db->get();

    return $query->unbuffered_row();
  }

  public function import(array $user_data)
  {
    $this->db->trans_begin();

    foreach ($user_data as $key => $data) {
      $category = (empty($data['category']))
        ? NULL : strtoupper($data['category']);

      $part_number = (empty($data['part_number']))
        ? NULL : strtoupper($data['part_number']);

      $serial_number = (empty($data['serial_number']))
        ? NULL : strtoupper($data['serial_number']);

      $condition = (empty($data['condition']))
        ? NULL : strtoupper($data['condition']);

      $unit = (empty($data['unit']))
        ? NULL : strtoupper($data['unit']);

      $description = (empty($data['description']))
        ? NULL : strtoupper($data['description']);

      $alternate_part_number = (empty($data['alternate_part_number']))
        ? NULL : strtoupper($data['alternate_part_number']);

      $group = (empty($data['group']))
        ? NULL : strtoupper($data['group']);

      $minimum_quantity = (empty($data['minimum_quantity']))
        ? 0 : floatval($data['minimum_quantity']);

      $stores = (empty($data['stores']))
        ? NULL : strtoupper($data['stores']);

      $warehouse = (empty($data['warehouse']))
        ? NULL : strtoupper($data['warehouse']);

      $document_number = (empty($data['document_number']))
        ? NULL : strtoupper($data['document_number']);

      $expired_date = (empty($data['expired_date']))
        ? NULL : $data['expired_date'];

      $vendor = (empty($data['vendor']))
        ? NULL : strtoupper($data['vendor']);

      $remarks = (empty($data['remarks']))
        ? NULL : strtoupper($data['remarks']);

      $received_date = (empty($data['received_date']))
        ? NULL : $data['received_date'];

      $received_by = (empty($data['received_by']))
        ? NULL : strtoupper($data['received_by']);

      $received_quantity = (empty($data['received_quantity']))
        ? 0 : floatval($data['received_quantity']);

      $received_unit_value = (empty($data['received_unit_value']))
        ? 0 : floatval($data['received_unit_value']);

      $purchase_order_number = (empty($data['purchase_order_number']))
        ? NULL : strtoupper($data['purchase_order_number']);

      $reference_number = (empty($data['reference_number']))
        ? NULL : strtoupper($data['reference_number']);

      $awb_number = (empty($data['awb_number']))
        ? NULL : strtoupper($data['awb_number']);

      $period_year  = get_setting('ACTIVE_YEAR');
      $period_month = get_setting('ACTIVE_MONTH');

      // $document_id      = (isset($_SESSION['receipt']['id'])) ? $_SESSION['receipt']['id'] : NULL;
      // $document_edit    = (isset($_SESSION['receipt']['edit'])) ? $_SESSION['receipt']['edit'] : NULL;
      // $document_number  = sprintf('%06s', $_SESSION['receipt']['document_number']) . receipt_format_number();
      // $received_date    = $_SESSION['receipt']['received_date'];
      // $received_by      = (empty($_SESSION['receipt']['received_by'])) ? NULL : $_SESSION['receipt']['received_by'];
      // $received_from    = (empty($_SESSION['receipt']['received_from'])) ? NULL : $_SESSION['receipt']['received_from'];
      $known_by         = (isset($data['known_by'])) ? NULL : NULL;
      $approved_by      = (isset($data['approved_by'])) ? NULL : NULL;
      // $warehouse        = $_SESSION['receipt']['warehouse'];
      // $category         = $_SESSION['receipt']['category'];
      // $notes            = (empty($_SESSION['receipt']['notes'])) ? NULL : $_SESSION['receipt']['notes'];

      if ($this->isDocumentNumberExists($document_number)) {
        $document_id = $this->getDocumentId($document_number);
      } else {
        $this->db->set('document_number', $document_number);
        $this->db->set('received_from', $vendor);
        $this->db->set('received_date', $received_date);
        $this->db->set('received_by', $received_by);
        $this->db->set('known_by', $known_by);
        $this->db->set('approved_by', $approved_by);
        $this->db->set('category', $category);
        $this->db->set('warehouse', $warehouse);
        $this->db->set('notes', NULL);
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->insert('tb_receipts');

        $document_id = $this->db->insert_id();
      }



      /**
       * CREATE UNIT OF MEASUREMENT IF NOT EXISTS
       */
      if (isItemUnitExists($unit) === FALSE) {
        $this->db->set('unit', $unit);
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->insert('tb_master_item_units');
      }

      /**
       * CREATE STORES IF NOT EXISTS
       */
      if (isStoresExists($stores) === FALSE && isStoresExists($stores, $category) === FALSE) {
        $this->db->set('stores', $stores);
        $this->db->set('warehouse', $warehouse);
        $this->db->set('category', $category);
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->insert('tb_master_stores');
      }

      /**
       * CREATE ITEM IF NOT EXISTS
       */
      if (isItemExists($part_number, $serial_number) === FALSE) {
        $this->db->set('part_number', $part_number);
        $this->db->set('serial_number', $serial_number);
        $this->db->set('alternate_part_number', $alternate_part_number);
        $this->db->set('description', $description);
        $this->db->set('group', $group);
        $this->db->set('minimum_quantity', floatval($minimum_quantity));
        $this->db->set('unit', $unit);
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->insert('tb_master_items');

        $item_id = $this->db->insert_id();
      } else {
        $item_id = getItemId($part_number, $serial_number);
      }

      /**
       * CREATE part number IF NOT EXISTS in tb master part number
       */

      if (isPartNumberExists($part_number) === FALSE) {
        $this->db->set('part_number', $part_number);
        $this->db->set('min_qty', $minimum_quantity);
        $this->db->set('item_id', $item_id);
        $this->db->set('qty', $received_quantity);
        $this->db->insert('tb_master_part_number');
      } else {
        $qty_awal = getPartnumberQty($part_number);

        $qty_baru = floatval($received_quantity) + floatval($qty_awal);

        $this->db->set('qty', $qty_baru);
        $this->db->where('part_number', $part_number);
        $this->db->update('tb_master_part_number');
      }




      /**
       * CREATE SERIAL NUMBER IF NOT EXISTS
       */
      if (!empty($data['serial_number'])) {
        if (isSerialExists($item_id, $serial_number) === FALSE) {
          $this->db->set('item_id', $item_id);
          $this->db->set('serial_number', $serial_number);
          $this->db->set('warehouse', $warehouse);
          $this->db->set('stores', $stores);
          $this->db->set('condition', $condition);
          $this->db->set('reference_document', $document_number);
          $this->db->set('updated_by', config_item('auth_person_name'));
          $this->db->insert('tb_master_item_serials');

          $serial_id  = $this->db->insert_id();
        } else {
          $serial     = getSerial($item_id, $serial_number);
          $serial_id  = $serial->id;

          $this->db->set('quantity', 1);
          $this->db->set('warehouse', $warehouse);
          $this->db->set('stores', $stores);
          $this->db->set('condition', $condition);
          $this->db->set('updated_by', config_item('auth_person_name'));
          $this->db->set('reference_document', $document_number);
          $this->db->where('id', $serial_id);
          $this->db->update('tb_master_item_serials');
        }
      } else {
        $serial_id = NULL;
      }

      /**
       * ADD ITEM INTO STOCK
       */
      if (isStockExists($item_id, $condition)) {
        $stock_id = getStockId($item_id, $condition);

        /**
         * CREATE STOCK CARD
         */
        // $prev_stock   = getStockActive($stock_id);
        // $next_stock   = floatval($prev_stock->total_quantity) + floatval($received_quantity);

        $prev_stock   = getStockPrev($stock_id, $stores);
        $next_stock   = floatval($prev_stock) + floatval($received_quantity);

        $this->db->set('serial_id', $serial_id);
        $this->db->set('stock_id', $stock_id);
        $this->db->set('warehouse', $warehouse);
        $this->db->set('stores', $stores);
        $this->db->set('date_of_entry', $received_date);
        $this->db->set('period_year', config_item('period_year'));
        $this->db->set('period_month', config_item('period_month'));
        $this->db->set('document_type', 'RECEIPT');
        $this->db->set('document_number', $document_number);
        $this->db->set('received_from', $vendor);
        $this->db->set('received_by', $received_by);
        $this->db->set('prev_quantity', floatval($prev_stock));
        $this->db->set('balance_quantity', $next_stock);
        $this->db->set('quantity', floatval($received_quantity));
        // if($data['kurs'] == 'rupiah'){
        $this->db->set('unit_value', floatval($data['received_unit_value']));
        // }else{
        //   $this->db->set('unit_value_dollar', floatval($data['received_unit_value']));
        //   $this->db->set('unit_value', floatval($kurs)*floatval($data['received_unit_value']));
        // }
        // $this->db->set('kurs_dollar', floatval($kurs));
        $this->db->set('remarks', $remarks);
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->insert('tb_stock_cards');
      } else {
        $this->db->set('item_id', $item_id);
        $this->db->set('condition', $condition);
        $this->db->set('initial_total_quantity', $received_quantity);
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->insert('tb_stocks');

        $stock_id = $this->db->insert_id();

        /**
         * CREATE STOCK CARD
         */
        // $prev_stock   = getStockActive($stock_id);
        // $next_stock   = floatval($prev_stock->total_quantity) + floatval($received_quantity);

        $this->db->set('serial_id', $serial_id);
        $this->db->set('stock_id', $stock_id);
        $this->db->set('warehouse', $warehouse);
        $this->db->set('stores', $stores);
        $this->db->set('date_of_entry', $received_date);
        $this->db->set('period_year', config_item('period_year'));
        $this->db->set('period_month', config_item('period_month'));
        $this->db->set('document_type', 'RECEIPT');
        $this->db->set('document_number', $document_number);
        $this->db->set('received_from', $vendor);
        $this->db->set('received_by', $received_by);
        $this->db->set('prev_quantity', floatval(0));
        $this->db->set('balance_quantity', floatval(0) + floatval($received_quantity));
        $this->db->set('quantity', floatval($received_quantity));
        // if($data['kurs'] == 'rupiah'){
        $this->db->set('unit_value', floatval($data['received_unit_value']));
        // }else{
        //   $this->db->set('unit_value_dollar', floatval($data['received_unit_value']));
        //   $this->db->set('unit_value', floatval($kurs)*floatval($data['received_unit_value']));
        // }
        // $this->db->set('kurs_dollar', floatval($kurs));
        $this->db->set('remarks', $remarks);
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->insert('tb_stock_cards');
      }

      if ($warehouse == 'WISNU') {
        $warehouse_id = 1;
      }
      if ($warehouse == 'BANYUWANGI') {
        $warehouse_id = 2;
      }
      if ($warehouse == 'SOLO') {
        $warehouse_id = 3;
      }
      if ($warehouse == 'LOMBOK') {
        $warehouse_id = 4;
      }
      if ($warehouse == 'JEMBER') {
        $warehouse_id = 5;
      }
      if ($warehouse == 'PALANGKARAYA') {
        $warehouse_id = 6;
      }
      if ($warehouse == 'WISNU REKONDISI') {
        $warehouse_id = 7;
      }
      if ($warehouse == 'BSR REKONDISI') {
        $warehouse_id = 8;
      }

      $kurs = kurs_dollar();

      // ADD to STORES
      $this->db->set('stock_id', $stock_id);
      $this->db->set('serial_id', $serial_id);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('stores', $stores);
      $this->db->set('initial_quantity', floatval($received_quantity));
      $this->db->set('quantity', floatval($received_quantity));
      // if($data['kurs'] == 'rupiah'){
      $this->db->set('unit_value', floatval($data['received_unit_value']));
      $this->db->set('initial_unit_value', floatval($data['received_unit_value']));
      // }else{
      //   $this->db->set('unit_value_dollar', floatval($data['received_unit_value']));
      //   $this->db->set('unit_value', floatval($kurs)*floatval($data['received_unit_value']));
      //   $this->db->set('initial_unit_value_dollar', floatval($data['received_unit_value']));
      //   $this->db->set('initial_unit_value', floatval($kurs)*floatval($data['received_unit_value']));
      // }
      $this->db->set('kurs_dollar', 1);
      $this->db->set('reference_document', $document_number);
      $this->db->set('received_date', $received_date);
      $this->db->set('expired_date', $expired_date);
      $this->db->set('received_by', $received_by);
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->set('remarks', $remarks);
      $this->db->set('warehouse_id', $warehouse_id);
      $this->db->insert('tb_stock_in_stores');

      $stock_in_stores_id = $this->db->insert_id();

      /**
       * INSERT INTO RECEIPT ITEMS
       */
      // if (!empty($data['purchase_order_item_id'])){
      //   $this->db->from('tb_purchase_order_items');
      //   $this->db->where('tb_purchase_order_items.id', $data['purchase_order_item_id']);

      //   $query  = $this->db->get();
      //   $row    = $query->unbuffered_row('array');
      //   $qty    = floatval($row['left_received_quantity']) - floatval($data['received_quantity']);

      //   $this->db->where('id', $data['purchase_order_item_id']);
      //   $this->db->set('left_received_quantity', $qty);
      //   $this->db->update('tb_purchase_order_items');
      // }

      // if (!empty($data['purchase_order_item_id'])){
      //   $this->db->set('purchase_order_item_id', $data['purchase_order_item_id']);
      // }

      $this->db->set('document_number', $document_number);
      $this->db->set('stock_in_stores_id', $stock_in_stores_id);
      $this->db->set('received_quantity', floatval($data['received_quantity']));
      // if($data['kurs'] == 'rupiah'){
      $this->db->set('received_unit_value', floatval($data['received_unit_value']));
      $this->db->set('received_total_value', floatval($data['received_unit_value']) * floatval($data['received_quantity']));
      $this->db->set('kurs_dollar', 1);
      $this->db->set('purchase_order_number', $purchase_order_number);
      $this->db->set('reference_number', $reference_number);
      $this->db->set('awb_number', $awb_number);
      $this->db->set('remarks', $remarks);
      // $this->db->set('received_from', $vendor);
      // $this->db->set('kode_akunting', $data['kode_akunting']);
      $this->db->insert('tb_receipt_items');

      // /**
      //  * CREATE STOCK CARD
      //  */
      // $prev_stock   = getStockActive($stock_id);
      // $next_stock   = floatval($prev_stock->total_quantity) + floatval($received_quantity);

      // $this->db->set('serial_id', $serial_id);
      // $this->db->set('stock_id', $stock_id);
      // $this->db->set('warehouse', $warehouse);
      // $this->db->set('stores', $stores);
      // $this->db->set('date_of_entry', $received_date);
      // $this->db->set('period_year', config_item('period_year'));
      // $this->db->set('period_month', config_item('period_month'));
      // $this->db->set('document_type', 'RECEIPT');
      // $this->db->set('document_number', $document_number);
      // $this->db->set('received_from', $vendor);
      // $this->db->set('received_by', $received_by);
      // $this->db->set('prev_quantity', floatval($prev_stock->total_quantity));
      // $this->db->set('balance_quantity', $next_stock);
      // $this->db->set('quantity', floatval($received_quantity));
      // // if($data['kurs'] == 'rupiah'){
      //   $this->db->set('unit_value', floatval($data['received_unit_value']));
      // // }else{
      // //   $this->db->set('unit_value_dollar', floatval($data['received_unit_value']));
      // //   $this->db->set('unit_value', floatval($kurs)*floatval($data['received_unit_value']));
      // // }
      // // $this->db->set('kurs_dollar', floatval($kurs));
      // $this->db->set('remarks', $remarks);
      // $this->db->set('created_by', config_item('auth_person_name'));
      // $this->db->insert('tb_stock_cards');

      /**
       * CREATE master item konversi
       */
      // if(!empty($data['unit_pakai'] && !empty('isi'))){
      //   $this->db->set('item_id', $item_id);
      //   $this->db->set('unit_beli', $data['unit']);
      //   $this->db->set('unit_pakai', $data['unit_pakai']);
      //   $this->db->set('qty_konversi',$data['isi']);
      //   $this->db->insert('tb_master_item_konversi');
      // }      

    }

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  function checkAPNumber()
  {
    return $this->db->get('tb_hutang')->num_rows();
  }
  function ap_last_number()
  {
    $div  = config_item('document_format_divider');
    $year = date('Y');

    $format = $div . 'AP' . $year;
    if ($this->checkAPNumber() == 0) {
      $number = sprintf('%06s', 1);
      $document_number = $number . $div . "AP" . $div . $year;
    } else {
      $format = $div . "AP" . $div . $year;
      $this->db->select_max('document_no', 'last_number');
      $this->db->from('tb_hutang');
      $this->db->like('document_no', $format, 'before');
      $query = $this->db->get('');
      $row    = $query->unbuffered_row();
      $last   = $row->last_number;
      $number = substr($last, 0, 6);
      $next   = $number + 1;
      $number = sprintf('%06s', $next);
      $document_number = $number . $div . "AP" . $div . $year;
    }
    return $document_number;
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
}
