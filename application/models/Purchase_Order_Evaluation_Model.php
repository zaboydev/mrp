<?php defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_Order_Evaluation_Model extends MY_Model
{
  protected $connection;
  protected $budget_year;
  protected $budget_month;

  public function __construct()
  {
    parent::__construct();

    $this->connection   = $this->load->database('budgetcontrol', TRUE);
    $this->budget_year  = find_budget_setting('Active Year');
    $this->budget_month = find_budget_setting('Active Month');
  }

  public function getSelectedColumns()
  {
    return array(
      'tb_purchase_orders.id'                       => NULL,
      'tb_purchase_orders.evaluation_number'        => 'Document Number',
      'tb_purchase_order_items.purchase_request_number'  => 'PR Number',
      'tb_purchase_orders.document_date'            => 'Date',
      'tb_purchase_orders.category'                 => 'Category',
      'tb_purchase_order_items.description'         => 'Description',
      'tb_purchase_order_items.part_number'         => 'Part Number',
      'tb_purchase_order_items.serial_number'       => 'Serial Number',
      'tb_purchase_order_items.quantity'            => 'Quantity',
      'tb_purchase_order_items.vendor'              => 'Vendor',
      'tb_purchase_order_items.unit_price'          => 'Price',
      'tb_purchase_orders.status'                   => 'Status',
      'tb_attachment_poe.id_poe as attachment'      => 'Attachment',
      'tb_purchase_orders.notes'                    => 'Notes',


    );
  }
  
  public function listAttachment($id)
  {
    $this->db->where('id_poe', $id);
    $this->db->where('tipe', 'POE');
    return $this->db->get('tb_attachment_poe')->result();
  }

  public function listAttachment_2($id)
  {
    $this->db->where('id_poe', $id);
    $this->db->where('tipe', 'POE');
    return $this->db->get('tb_attachment_poe')->result_array();
  }

  /*public function getNotifRecipient(){
    $this->db->select('email');
    $this->db->from('tb_auth_users');
    $this->db->where('auth_level',9);
    return $this->db->get('')->result();
  }*/

  public function getNotifRecipientHOS()
  {
    $this->db->select('email');
    $this->db->from('tb_auth_users');
    $this->db->where('auth_level', 2);
    return $this->db->get('')->result();
  }

  public function getSearchableColumns()
  {
    return array(
      'tb_purchase_orders.document_number',
      'tb_purchase_orders.category',
      'tb_purchase_order_items.description',
      'tb_purchase_order_items.part_number',
      'tb_purchase_order_items.serial_number',
      'tb_purchase_orders.status',
      'tb_purchase_orders.vendor',
      'tb_purchase_order_items.purchase_request_number',
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'tb_purchase_orders.document_number',
      'tb_purchase_orders.document_date',
      'tb_purchase_orders.category',
      'tb_purchase_order_items.description',
      'tb_purchase_order_items.part_number',
      'tb_purchase_order_items.serial_number',
      'tb_purchase_order_items.quantity',
      'tb_purchase_orders.vendor',
      'tb_purchase_order_items.unit_price',
      'tb_purchase_orders.status',
      'tb_purchase_order_items.purchase_request_number',
    );
  }

  private function searchIndex()
  {
    if (!empty($_POST['columns'][7]['search']['value'])) {
      $search_status = $_POST['columns'][7]['search']['value'];

      $this->db->where('tb_purchase_orders.status', $search_status);
    } else {
      if (config_item('auth_role') == 'CHIEF OF MAINTANCE') {
        $this->db->where('tb_purchase_orders.status', 'evaluation');
      }
    }

    if (!empty($_POST['columns'][2]['search']['value'])) {
      $search_document_date = $_POST['columns'][2]['search']['value'];
      $range_document_date  = explode(' ', $search_document_date);

      $this->db->where('tb_purchase_orders.document_date >= ', $range_document_date[0]);
      $this->db->where('tb_purchase_orders.document_date <= ', $range_document_date[1]);
    }

    if (!empty($_POST['columns'][3]['search']['value'])) {
      $search_category = $_POST['columns'][3]['search']['value'];

      $this->db->where('tb_purchase_orders.category', $search_category);
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

  function multi_reject($id_purchase_order, $notes)
  {
    $x = 0;
    $return = 0;
    foreach ($id_purchase_order as $id) {
      $this->db->where('purchase_order_id', $id);
      $tb_purchase_order_items = $this->db->get('tb_purchase_order_items')->result();
      foreach ($tb_purchase_order_items as $key) {
        $inventory_purchase_request_detail_id = $key->inventory_purchase_request_detail_id;
        $this->db->where('id', $inventory_purchase_request_detail_id);
        $this->db->set('sisa', '"sisa" + ' . $key->quantity, false);
        $this->db->update('tb_inventory_purchase_requisition_details');

        // $this->db->where('id', $inventory_purchase_request_detail_id);
        // $detail_request = $this->db->get('tb_inventory_purchase_requisition_details')->row();
        // if($detail_request->sisa == 0){
        // $this->db->set('closing_by', config_item('auth_person_name'));

        //deletetb_purchase_request_closures
        $this->db->where('purchase_request_detail_id', $inventory_purchase_request_detail_id);
        $this->db->delete('tb_purchase_request_closures');
        // }
      }
      $this->db->set('status', 'rejected');
      $this->db->set('notes', $notes[$x]);
      $this->db->set('approved_by', config_item('auth_person_name'));
      $this->db->where('id', $id);
      $check = $this->db->update('tb_purchase_orders');
      if ($check) {
        $return++;
      }
      $x++;
    }
    if (($return == $x) && ($return > 0)) {
      return true;
    } else {
      return false;
    }
  }

  function getIndex($return = 'array')
  {
    $this->db->distinct();
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_purchase_order_items_vendors');
    $this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.id = tb_purchase_order_items_vendors.purchase_order_item_id');
    $this->db->join('tb_purchase_order_vendors', 'tb_purchase_order_vendors.id = tb_purchase_order_items_vendors.purchase_order_vendor_id');
    $this->db->join('tb_purchase_orders', 'tb_purchase_orders.id = tb_purchase_order_items.purchase_order_id');
    $this->db->join('tb_attachment_poe', 'tb_purchase_orders.id = tb_attachment_poe.id_poe', 'left');
    $this->db->where('tb_purchase_order_items_vendors.is_selected', 't');
    $this->db->where_in('tb_purchase_orders.category', config_item('auth_inventory'));
    $this->db->where('tb_purchase_orders.tipe', 'INVENTORY MRP');    
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
    // $this->db->from('tb_purchase_order_items_vendors');
    // $this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.id = tb_purchase_order_items_vendors.purchase_order_item_id');
    // $this->db->join('tb_purchase_order_vendors', 'tb_purchase_order_vendors.id = tb_purchase_order_items_vendors.purchase_order_vendor_id');
    // $this->db->join('tb_purchase_orders', 'tb_purchase_orders.id = tb_purchase_order_items.purchase_order_id');
    // $this->db->where('tb_purchase_order_vendors.is_selected', 't');
    // $this->db->where_in('tb_purchase_orders.category', config_item('auth_inventory'));
    $this->db->distinct();
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_purchase_order_items_vendors');
    $this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.id = tb_purchase_order_items_vendors.purchase_order_item_id');
    $this->db->join('tb_purchase_order_vendors', 'tb_purchase_order_vendors.id = tb_purchase_order_items_vendors.purchase_order_vendor_id');
    $this->db->join('tb_purchase_orders', 'tb_purchase_orders.id = tb_purchase_order_items.purchase_order_id');
    $this->db->join('tb_attachment_poe', 'tb_purchase_orders.id = tb_attachment_poe.id_poe', 'left');
    $this->db->where('tb_purchase_order_items_vendors.is_selected', 't');
    $this->db->where_in('tb_purchase_orders.category', config_item('auth_inventory'));
    $this->db->where('tb_purchase_orders.tipe', 'INVENTORY MRP');    
    $this->searchIndex();

    // $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    // $this->db->from('tb_purchase_order_items_vendors');
    // $this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.id = tb_purchase_order_items_vendors.purchase_order_item_id');
    // $this->db->join('tb_purchase_order_vendors', 'tb_purchase_order_vendors.id = tb_purchase_order_items_vendors.purchase_order_vendor_id');
    // $this->db->join('tb_purchase_orders', 'tb_purchase_orders.id = tb_purchase_order_items.purchase_order_id');
    // $this->db->where('tb_purchase_order_vendors.is_selected', 't');
    // $this->db->where_in('tb_purchase_orders.category', config_item('auth_inventory'));
    $this->db->distinct();
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_purchase_order_items_vendors');
    $this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.id = tb_purchase_order_items_vendors.purchase_order_item_id');
    $this->db->join('tb_purchase_order_vendors', 'tb_purchase_order_vendors.id = tb_purchase_order_items_vendors.purchase_order_vendor_id');
    $this->db->join('tb_purchase_orders', 'tb_purchase_orders.id = tb_purchase_order_items.purchase_order_id');
    $this->db->join('tb_attachment_poe', 'tb_purchase_orders.id = tb_attachment_poe.id_poe', 'left');
    $this->db->where('tb_purchase_order_items_vendors.is_selected', 't');
    $this->db->where_in('tb_purchase_orders.category', config_item('auth_inventory'));
    $this->db->where('tb_purchase_orders.tipe', 'INVENTORY MRP');    
    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function findById($id)
  {
    $this->db->where('id', $id);

    $query  = $this->db->get('tb_purchase_orders');
    $poe    = $query->unbuffered_row('array');

    $this->db->from('tb_purchase_order_vendors');
    $this->db->order_by('id', 'asc');
    $this->db->where('tb_purchase_order_vendors.purchase_order_id', $id);

    $query = $this->db->get();

    foreach ($query->result_array() as $key => $vendor) {
      $poe['vendors'][$key]['id'] = $vendor['id'];
      $poe['vendors'][$key]['vendor'] = $vendor['currency'] . '-' . $vendor['vendor'];
      $poe['vendors'][$key]['vendor_name'] = $vendor['vendor'];
      $poe['vendors'][$key]['is_selected'] = $vendor['is_selected'];
      $poe['vendors'][$key]['vendor_currency'] = $vendor['currency'];
    }

    $selected_detail_poe = array(
      'tb_purchase_order_items.*',
      'tb_inventory_purchase_requisition_details.reference_ipc',
    );

    $this->db->select($selected_detail_poe);
    $this->db->from('tb_purchase_order_items');
    $this->db->join('tb_inventory_purchase_requisition_details','tb_inventory_purchase_requisition_details.id=tb_purchase_order_items.inventory_purchase_request_detail_id');
    $this->db->where('tb_purchase_order_items.purchase_order_id', $id);


    $query = $this->db->get();

    foreach ($query->result_array() as $i => $item) {
      $this->db->from('tb_purchase_order_vendors');
      $this->db->where('tb_purchase_order_vendors.purchase_order_id', $item['id']);

      $query = $this->db->get();

      foreach ($query->result_array() as $key => $vendor) {
        // $poe['vendors'][$key]['vendor'] = $vendor['vendor'];
        $poe['request'][$i]['is_selected'] = $vendor['is_selected'];
      }
      $poe['request'][$i] = $item;
      $poe['request'][$i]['history']          = $this->getHistory($item['inventory_purchase_request_detail_id']);
      $poe['request'][$i]['vendors'] = array();

      $selected_detail = array(
        'tb_purchase_order_items_vendors.*',
        'tb_purchase_order_vendors.vendor',
        'tb_purchase_order_vendors.currency'
      );

      $this->db->select($selected_detail);
      $this->db->from('tb_purchase_order_items_vendors');
      $this->db->join('tb_purchase_order_vendors', 'tb_purchase_order_vendors.id = tb_purchase_order_items_vendors.purchase_order_vendor_id');
      $this->db->where('tb_purchase_order_items_vendors.purchase_order_item_id', $item['id']);
      $this->db->order_by('purchase_order_vendor_id', 'asc');

      $query = $this->db->get();

      foreach ($query->result_array() as $d => $detail) {
        $poe['request'][$i]['vendors'][$d] = $detail;
        $poe['request'][$i]['vendors'][$d]['vendor'] = $detail['currency'] . '-' . $detail['vendor'];
      
      }
    }
    $this->db->where('id_poe', $id);
    $data = $this->db->get('tb_attachment_poe')->result();
    $attachment = array();
    foreach ($data as $key) {
      array_push($attachment, $key->file);
    }
    $poe["attachment"] = $attachment;
    return $poe;
  }

  public function getHistory($inventory_purchase_request_detail_id)
  {

    $select = array(
      'tb_inventory_purchase_requisitions.pr_number',
      'tb_inventory_purchase_requisitions.pr_date',
      'tb_inventory_purchase_requisitions.created_by',
      'tb_inventory_purchase_requisition_details.id',
      'tb_inventory_purchase_requisition_details.quantity',
      'tb_inventory_purchase_requisition_details.unit',
      'tb_inventory_purchase_requisition_details.price',
      'tb_inventory_purchase_requisition_details.total',
      'sum(case when tb_purchase_order_items.quantity is null then 0.00 else tb_purchase_order_items.quantity end) as "poe_qty"',  
      'sum(case when tb_purchase_order_items.total_amount is null then 0.00 else tb_purchase_order_items.total_amount end) as "poe_value"',  
      'sum(case when tb_po_item.quantity is null then 0.00 else tb_po_item.quantity end) as "po_qty"',  
      'sum(case when tb_po_item.total_amount is null then 0.00 else tb_po_item.total_amount end) as "po_value"',
      'sum(case when tb_receipt_items.received_quantity is null then 0.00 else tb_receipt_items.received_quantity end) as "grn_qty"',  
      'sum(case when tb_receipt_items.received_total_value is null then 0.00 else tb_receipt_items.received_total_value end) as "grn_value"',       
    );

    $group = array(
      'tb_inventory_purchase_requisitions.pr_number',
      'tb_inventory_purchase_requisitions.pr_date',
      'tb_inventory_purchase_requisitions.created_by',
      'tb_inventory_purchase_requisition_details.id',
      'tb_inventory_purchase_requisition_details.quantity',
      'tb_inventory_purchase_requisition_details.unit',
      'tb_inventory_purchase_requisition_details.price',
      'tb_inventory_purchase_requisition_details.total',
    );

    $this->db->select($select);
    $this->db->from('tb_inventory_purchase_requisition_details');
    $this->db->join('tb_inventory_purchase_requisitions', 'tb_inventory_purchase_requisitions.id = tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id');
    $this->db->join('tb_purchase_order_items', 'tb_inventory_purchase_requisition_details.id = tb_purchase_order_items.inventory_purchase_request_detail_id','left');
    $this->db->join('tb_po_item', 'tb_po_item.poe_item_id = tb_purchase_order_items.id','left');
    $this->db->join('tb_po', 'tb_po_item.purchase_order_id = tb_po.id','left');
    $this->db->join('tb_receipt_items', 'tb_receipt_items.purchase_order_item_id = tb_po_item.id','left');
    $this->db->where('tb_inventory_purchase_requisition_details.id', $inventory_purchase_request_detail_id);
    $this->db->where_in('tb_po.status',['PURPOSED','OPEN','ORDER','CLOSE']);
    $this->db->group_by($group);
    $query  = $this->db->get();
    $return = $query->result_array();

    return $return;
        
  }

  public function isDocumentNumberExists($document_number)
  {
    $this->db->where('document_number', $document_number);
    $query = $this->db->get('tb_purchase_orders');

    if ($query->num_rows() > 0)
      return true;

    return false;
  }

  public function approve($id)
  {
    $this->db->trans_begin();

    $this->db->set('status', 'approved');
    $this->db->set('review_status', strtoupper("waiting for purchase"));
    $this->db->set('updated_at', date('Y-m-d'));
    $this->db->set('updated_by', config_item('auth_person_name'));
    $this->db->set('approved_by', config_item('auth_person_name'));
    $this->db->where('id', $id);
    $this->db->update('tb_purchase_orders');

    $this->db->set('status_item', 'open');
    $this->db->where('purchase_order_id', $id);
    $this->db->update('tb_purchase_order_items');

    $request_item_ids = getRequestItemIdsByPoeId($id);
    $this->db->set('last_activity', 'POE Approved, waiting for PO');
    $this->db->where_in('tb_inventory_purchase_requisition_details.id', $request_item_ids);
    $this->db->update('tb_inventory_purchase_requisition_details');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function save()
  {

    // $this->db->trans_begin();

    $document_id          = (isset($_SESSION['poe']['id'])) ? $_SESSION['poe']['id'] : NULL;
    $document_edit        = (isset($_SESSION['poe']['edit'])) ? $_SESSION['poe']['edit'] : NULL;
    $document_number      = $_SESSION['poe']['document_number'] . poe_format_number();
    $document_date        = $_SESSION['poe']['document_date'];
    $created_by           = (empty($_SESSION['poe']['created_by'])) ? NULL : $_SESSION['poe']['created_by'];
    $document_reference   = (empty($_SESSION['poe']['document_reference'])) ? NULL : $_SESSION['poe']['document_reference'];
    $approved_by          = (empty($_SESSION['poe']['approved_by'])) ? NULL : $_SESSION['poe']['approved_by'];
    $status               = 'evaluation';
    $warehouse            = $_SESSION['poe']['warehouse'];
    $category             = $_SESSION['poe']['category'];
    $default_currency     = $_SESSION['poe']['default_currency'];
    $approval             = $_SESSION['poe']['approval'];
    if ($approval == 'without_approval') {
      $status               = 'approved';
    }
    $exchange_rate        = $_SESSION['poe']['exchange_rate'];
    $notes                = (empty($_SESSION['poe']['notes'])) ? NULL : $_SESSION['poe']['notes'];

    $this->db->trans_begin();

    if ($document_id === NULL) {
      /**
       * CREATE DOCUMENT
       */
      $this->db->set('evaluation_number', $document_number);
      $this->db->set('document_reference', $document_reference);
      $this->db->set('document_date', $document_date);
      $this->db->set('created_by', $created_by);
      // $this->db->set('approved_by', $approved_by);
      if ($approval == 'without_approval') {
        $this->db->set('approved_by', $approval);
      }
      $this->db->set('category', $category);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('default_currency', $default_currency);
      $this->db->set('exchange_rate', $exchange_rate);
      $this->db->set('status', $status);
      $this->db->set('notes', $notes);
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->set('updated_by', config_item('auth_person_name'));

      $this->db->insert('tb_purchase_orders');

      $document_id = $this->db->insert_id();
    } else {
      $status_poe = getStatusPOE($document_number);
      $this->db->set('evaluation_number', $document_number);
      $this->db->set('document_date', $document_date);
      $this->db->set('document_reference', $document_reference);
      $this->db->set('created_by', $created_by);
      if ($approval == 'without_approval') {
        $this->db->set('approved_by', $approval);
      }
      $this->db->set('warehouse', $warehouse);
      $this->db->set('category', $category);
      $this->db->set('default_currency', $default_currency);
      $this->db->set('exchange_rate', $exchange_rate);
      $this->db->set('status', $status_poe);
      $this->db->set('notes', $notes);
      $this->db->set('updated_at', date('Y-m-d'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->where('id', $document_id);
      $this->db->update('tb_purchase_orders');

      /**
       * DELETE OLD ITEMS
       */
      $this->db->where('purchase_order_id', $document_id);
      $this->db->delete('tb_purchase_order_vendors');

      $this->db->where('purchase_order_id', $document_id);
      $tb_purchase_order_items = $this->db->get('tb_purchase_order_items')->result();
      foreach ($tb_purchase_order_items as $key) {
        $inventory_purchase_request_detail_id = $key->inventory_purchase_request_detail_id;
        $this->db->where('id', $inventory_purchase_request_detail_id);
        $this->db->set('sisa', '"sisa" + ' . $key->quantity_prl, false);
        $this->db->update('tb_inventory_purchase_requisition_details');
      }
      $this->db->where('purchase_order_id', $document_id);
      $this->db->delete('tb_purchase_order_items');

      $this->db->where('id_poe', $document_id);
      $this->db->delete('tb_attachment_poe');
    }

    /**
     * PROCESSING VENDORS
     */
    foreach ($_SESSION['poe']['vendors'] as $key => $vendor) {
      $this->db->set('purchase_order_id', $document_id);
      $this->db->set('vendor', $vendor['vendor_name']);
      $this->db->set('is_selected', false);
      $this->db->set('currency', $vendor['vendor_currency']);
      $this->db->insert('tb_purchase_order_vendors');

      // if ($vendor['is_selected'] == 't'){
      //   $this->db->from('tb_master_vendors');
      //   $this->db->where('tb_master_vendors.vendor', $vendor['vendor']);

      //   $query = $this->db->get();
      //   $row   = $query->unbuffered_row('array');

      //   $this->db->set('vendor', $vendor['vendor']);
      //   $this->db->set('vendor_address', $row['address']);
      //   $this->db->set('vendor_country', $row['country']);
      //   $this->db->set('vendor_phone', $row['phone']);
      //   $this->db->set('vendor_attention', $row['email']);
      //   $this->db->set('updated_at', date('Y-m-d'));
      //   $this->db->set('updated_by', config_item('auth_person_name'));
      //   $this->db->where('id', $document_id);
      //   $this->db->update('tb_purchase_orders');
      // }
    }
    foreach ($_SESSION["poe"]["attachment"] as $key) {
      $this->db->set('id_poe', $document_id);
      $this->db->set('file', $key);
      $this->db->insert('tb_attachment_poe');
    }
    /**
     * PROCESSING ITEMS
     */
    foreach ($_SESSION['poe']['request'] as $i => $item) {
      $this->db->set('purchase_order_id', $document_id);
      $this->db->set('description', strtoupper($item['description']));
      $this->db->set('part_number', strtoupper($item['part_number']));
      $this->db->set('serial_number', strtoupper($item['serial_number']));
      $this->db->set('alternate_part_number', strtoupper($item['alternate_part_number']));
      $this->db->set('remarks', trim($item['remarks']));
      $this->db->set('quantity_requested', floatval($item['quantity_requested']));
      $this->db->set('unit_price_requested', floatval($item['unit_price_requested']));
      $this->db->set('total_amount_requested', floatval($item['total_amount_requested']));
      $this->db->set('unit', trim($item['unit']));
      $this->db->set('inventory_purchase_request_detail_id', $item['inventory_purchase_request_detail_id']);
      $this->db->insert('tb_purchase_order_items');
      $inventory_purchase_request_detail_id = $item['inventory_purchase_request_detail_id'];
      $poe_item_id = $this->db->insert_id();

      foreach ($item['vendors'] as $d => $detail) {
        $vendor_currency = $detail['vendor'];
        $range_vendor_currency = explode('-', $vendor_currency);

        // $_SESSION['poe']['vendors'][$key]['vendor'] = $vendor;
        // $_SESSION['poe']['vendors'][$key]['vendor_currency'] = $range_vendor_currency[0];
        // $_SESSION['poe']['vendors'][$key]['vendor_name'] = $range_vendor_currency[1];
        // if ($detail['is_selected'] == 't'){

        //   $this->db->set('purchase_order_id', $document_id);
        //   $this->db->set('vendor', $detail['vendor']);
        //   $this->db->set('is_selected', $detail['is_selected']);
        //   $this->db->insert('tb_purchase_order_vendors');
        // } 
        // $purchase_order_vendors_id = $this->db->insert_id();

        $this->db->from('tb_purchase_order_vendors');
        $this->db->where('tb_purchase_order_vendors.vendor', $range_vendor_currency[1]);
        $this->db->where('tb_purchase_order_vendors.purchase_order_id', $document_id);

        $query  = $this->db->get();
        $row    = $query->unbuffered_row('array');
        // $poe_vendor_id  = $this->db->insert_id();
        $poe_vendor_id  = $row['id'];
        $is_selected    = $detail['is_selected'];

        $this->db->set('purchase_order_item_id', $poe_item_id);
        $this->db->set('purchase_order_vendor_id', $poe_vendor_id);
        $this->db->set('quantity', floatval($detail['quantity']));
        $this->db->set('left_received_quantity', floatval($detail['left_received_quantity']));
        $this->db->set('left_paid_quantity', floatval($detail['left_paid_quantity']));
        $this->db->set('unit_price', floatval($detail['unit_price']));
        $this->db->set('core_charge', floatval($detail['core_charge']));
        $this->db->set('total', floatval($detail['total']));
        $this->db->set('left_paid_amount', floatval($detail['left_paid_amount']));
        $this->db->set('alternate_part_number', strtoupper($detail['alternate_part_number']));
        $this->db->set('purchase_request_number', strtoupper($detail['purchase_request_number']));
        $this->db->set('is_selected', $detail['is_selected']);
        $this->db->insert('tb_purchase_order_items_vendors');

        if ($is_selected == 't') {
          $item_status = 'closed';
          if ($approval == 'without_approval') {
            $item_status               = 'open';
          }
          // $this->db->set('alternate_part_number', strtoupper($detail['alternate_part_number']));
          $this->db->set('purchase_request_number', strtoupper($detail['purchase_request_number']));
          $this->db->set('vendor', strtoupper($range_vendor_currency[1]));
          $this->db->set('quantity', floatval($detail['quantity']));
          $this->db->set('left_received_quantity', floatval($detail['left_received_quantity']));
          $this->db->set('left_paid_quantity', floatval($detail['left_paid_quantity']));
          $this->db->set('unit_price', floatval($detail['unit_price']));
          $this->db->set('core_charge', floatval($detail['core_charge']));
          $this->db->set('total_amount', floatval($detail['total']));
          $this->db->set('left_paid_amount', floatval($detail['left_paid_amount']));
          $this->db->set('quantity_prl', floatval($detail['quantity'] * $item['konversi']));
          $this->db->set('value_prl', floatval($detail['total'] / ($detail['quantity'] * $item['konversi'])));
          $this->db->set('konversi', floatval($item['konversi']));
          $this->db->set('status_item', $item_status);
          $this->db->where('id', $poe_item_id);
          $this->db->update('tb_purchase_order_items');
          $quantity_prl = floatval($detail['quantity'] * $item['konversi']);

          $this->db->set('sisa', '"sisa" - ' . $quantity_prl, false);
          if ($approval == 'without_approval') {
            $this->db->set('last_activity', 'POE Approved '.$document_number,', waiting for PO');
          }else{
            $this->db->set('last_activity', 'POE Created '.$document_number.' waiting approval');
          }
          $this->db->where('id', $inventory_purchase_request_detail_id);
          $this->db->update('tb_inventory_purchase_requisition_details');
            
          $this->db->set('is_selected', 't');       
          $this->db->where('id', $poe_vendor_id);
          $this->db->update('tb_purchase_order_vendors');

          // $this->db->select('sisa');
          // $this->db->where('id', $inventory_purchase_request_detail_id);
          // $this->db->from('tb_inventory_purchase_requisition_details');
          // $query = $this->db->get();
          // $result = $query->unbuffered_row('array');
          // if($result==0){
          //   $this->db->set('status','closed');
          //   $this->db->where('id', $inventory_purchase_request_detail_id);
          //   $this->db->update('tb_inventory_purchase_requisition_details');
          // }


        }
      }

      if ($document_edit === NULL) {
        $this->db->where('id', $inventory_purchase_request_detail_id);
        $detail_request = $this->db->get('tb_inventory_purchase_requisition_details')->row();
        if ($detail_request->sisa <= 0) {

          $this->db->set('status', 'closed');
          $this->db->where('id', $item['inventory_purchase_request_detail_id']);
          $this->db->update('tb_inventory_purchase_requisition_details');

          $this->db->set('closing_by', config_item('auth_person_name'));
          $this->db->set('purchase_request_detail_id', $item['inventory_purchase_request_detail_id']);
          $this->db->insert('tb_purchase_request_closures');
        }
      }
    }

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    if ($approval != 'without_approval' && $document_id === NULL) {
      $this->send_mail($document_id);
    }
    return TRUE;
  }

  public function delete()
  {
    $this->db->trans_begin();

    $id = $this->input->post('id');

    $this->db->select('document_number, warehouse');
    $this->db->where('id', $id);
    $this->db->from('tb_purchase_orders');

    $query = $this->db->get();
    $row   = $query->unbuffered_row('array');

    $document_number  = $row['document_number'];
    $warehouse        = $row['warehouse'];

    $this->db->select('tb_purchase_order_items.id, tb_purchase_order_items.stock_in_stores_id, tb_purchase_order_items.received_quantity, tb_purchase_order_items.received_unit_value, tb_stock_in_stores.stock_id, tb_stock_in_stores.serial_id, tb_stock_in_stores.stores');
    $this->db->from('tb_purchase_order_items');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_purchase_order_items.stock_in_stores_id');
    $this->db->where('tb_purchase_order_items.document_number', $document_number);

    $query  = $this->db->get();
    $result = $query->result_array();

    foreach ($result as $data) {
      $this->db->set('stock_id', $data['stock_id']);
      $this->db->set('serial_id', $data['serial_id']);
      $this->db->set('warehouse', $warehouse);
      $this->db->set('stores', $data['stores']);
      $this->db->set('date_of_entry', date('Y-m-d'));
      $this->db->set('period_year', config_item('period_year'));
      $this->db->set('period_month', config_item('period_month'));
      $this->db->set('document_type', 'REMOVAL');
      $this->db->set('document_number', $document_number);
      $this->db->set('issued_to', 'DELETE DOCUMENT');
      $this->db->set('issued_by', config_item('auth_person_name'));
      $this->db->set('quantity', 0 - floatval($data['received_quantity']));
      $this->db->set('unit_value', floatval($data['received_unit_value']));
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->insert('tb_stock_cards');

      $this->db->where('id', $data['id']);
      $this->db->delete('tb_purchase_order_items');

      $this->db->where('id', $data['stock_in_stores_id']);
      $this->db->delete('tb_stock_in_stores');
    }

    $this->db->where('id', $id);
    $this->db->delete('tb_purchase_orders');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function getDocumentClosures()
  {
    $this->db->select('tb_purchase_request_closures.purchase_request_detail_id');
    $this->db->from('tb_purchase_request_closures');

    $query  = $this->db->get();
    $result = array();

    foreach ($query->result_array() as $key => $value) {
      $result[] = $value['purchase_request_detail_id'];
    }

    return $result;
  }

  public function listRequest($category = NULL)
  {
    if ($_SESSION['poe']['source'] == 0) {
      $this->connection->select(array(
        'tb_inventory_purchase_requisition_details.id',
        'tb_inventory_purchase_requisition_details.additional_info',
        'tb_inventory_purchase_requisition_details.quantity',
        'tb_inventory_purchase_requisition_details.sisa',
        'tb_product_categories.category_name',
        'tb_products.product_name',
        'tb_products.product_code',
        'tb_inventory_purchase_requisitions.pr_number',
        'tb_inventory_purchase_requisitions.pr_date',
        'tb_inventory_purchase_requisitions.required_date',
        'tb_inventory_purchase_requisitions.status',
        'tb_inventory_purchase_requisitions.suggested_supplier',
        'tb_inventory_purchase_requisitions.deliver_to',
        'tb_inventory_purchase_requisitions.created_by',
        'tb_inventory_purchase_requisitions.notes'
      ));

      $this->connection->from('tb_inventory_purchase_requisitions');
      $this->connection->join('tb_inventory_purchase_requisition_details', 'tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id = tb_inventory_purchase_requisitions.id');
      $this->connection->join('tb_inventory_monthly_budgets', 'tb_inventory_monthly_budgets.id = tb_inventory_purchase_requisition_details.inventory_monthly_budget_id');
      $this->connection->join('tb_products', 'tb_products.id = tb_inventory_monthly_budgets.product_id');
      $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_inventory_purchase_requisitions.product_category_id');
      $this->connection->where('tb_inventory_purchase_requisitions.status', 'approved');
      $this->connection->group_start();
      $this->connection->where('tb_inventory_purchase_requisition_details.sisa >', 0);
      $this->connection->where('tb_inventory_purchase_requisition_details.sisa is not null', null, false);
      $this->connection->group_end();

      if ($category === NULL) {
        $this->connection->where_in('UPPER(tb_product_categories.category_name)', config_item('auth_inventory'));
      } else {
        $this->connection->where('UPPER(tb_product_categories.category_name)', $category);
      }

      if (empty($this->getDocumentClosures()) === FALSE) {
        $this->connection->where_not_in('tb_inventory_purchase_requisition_details.id', $this->getDocumentClosures());
      }

      $this->connection->like('tb_inventory_purchase_requisitions.pr_number', $this->budget_year);
      $this->connection->order_by('tb_inventory_purchase_requisitions.id', 'desc');

      $query = $this->connection->get();
    } else {
      $group = array(
        'tb_inventory_purchase_requisition_details.id',
        'tb_inventory_purchase_requisition_details.additional_info',
        'tb_inventory_purchase_requisition_details.quantity',
        'tb_inventory_purchase_requisition_details.sisa',
        'tb_inventory_purchase_requisitions.item_category',
        'tb_inventory_purchase_requisition_details.product_name',
        'tb_inventory_purchase_requisition_details.part_number',
        'tb_inventory_purchase_requisitions.pr_number',
        'tb_inventory_purchase_requisitions.pr_date',
        'tb_inventory_purchase_requisitions.required_date',
        'tb_inventory_purchase_requisitions.status',
        'tb_inventory_purchase_requisitions.suggested_supplier',
        'tb_inventory_purchase_requisitions.deliver_to',
        'tb_inventory_purchase_requisitions.created_by',
        'tb_inventory_purchase_requisitions.notes'
      );
      $this->db->select(array(
        'tb_inventory_purchase_requisition_details.id',
        'tb_inventory_purchase_requisition_details.additional_info',
        'tb_inventory_purchase_requisition_details.quantity',
        'tb_inventory_purchase_requisition_details.sisa',
        'tb_inventory_purchase_requisitions.item_category as "category_name"',
        'tb_inventory_purchase_requisition_details.product_name as "product_name"',
        'tb_inventory_purchase_requisition_details.part_number as "product_code"',
        'tb_inventory_purchase_requisitions.pr_number',
        'tb_inventory_purchase_requisitions.pr_date',
        'tb_inventory_purchase_requisitions.required_date',
        'tb_inventory_purchase_requisitions.status',
        'tb_inventory_purchase_requisitions.suggested_supplier',
        'tb_inventory_purchase_requisitions.deliver_to',
        'tb_inventory_purchase_requisitions.created_by',
        'tb_inventory_purchase_requisitions.notes',
        'sum(case when tb_purchase_order_items.quantity is null then 0.00 else tb_purchase_order_items.quantity end) as "poe_qty"',   
        'sum(case when tb_po_item.quantity is null then 0.00 else tb_po_item.quantity end) as "po_qty"',
        'sum(case when tb_receipt_items.received_quantity is null then 0.00 else tb_receipt_items.received_quantity end) as "grn_qty"', 
      ));

      $this->db->from('tb_inventory_purchase_requisition_details');
      $this->db->join('tb_inventory_purchase_requisitions', 'tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id = tb_inventory_purchase_requisitions.id');
      $this->db->join('tb_budget', 'tb_budget.id = tb_inventory_purchase_requisition_details.budget_id');
      $this->db->join('tb_budget_cot', 'tb_budget_cot.id = tb_budget.id_cot');
      $this->db->join('tb_master_items', 'tb_budget_cot.id_item = tb_master_items.id');
      $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
      $this->db->join('tb_purchase_order_items', 'tb_inventory_purchase_requisition_details.id = tb_purchase_order_items.inventory_purchase_request_detail_id','left');
      $this->db->join('tb_po_item', 'tb_po_item.poe_item_id = tb_purchase_order_items.id','left');
      $this->db->join('tb_receipt_items', 'tb_receipt_items.purchase_order_item_id = tb_po_item.id','left');
      $this->db->where('tb_inventory_purchase_requisition_details.status', 'open');
      $this->db->group_start();
      $this->db->where('tb_inventory_purchase_requisition_details.sisa >', 0);
      $this->db->where('tb_inventory_purchase_requisition_details.sisa is not null', null, false);
      $this->db->group_end();
      // if ($category === NULL){
      //   $this->db->where_in('UPPER(tb_master_item_groups.category)', config_item('auth_inventory'));
      // } else {
      //   $this->db->where('UPPER(tb_master_item_groups.category)', $category);
      // }
      // $this->db->like('tb_inventory_purchase_requisitions.pr_number', $this->budget_year);
      $this->db->group_by($group);
      $this->db->order_by('tb_inventory_purchase_requisitions.required_date', 'asc');
      $query = $this->db->get();
    }


    return $query->result_array();
  }

  public function infoRequest($id)
  {
    if ($_SESSION['poe']['source'] == 0) {
      $this->connection->select(array(
        'tb_inventory_purchase_requisition_details.id',
        'tb_inventory_purchase_requisition_details.additional_info',
        'tb_inventory_purchase_requisition_details.quantity',
        'tb_inventory_purchase_requisition_details.sisa',
        'tb_inventory_purchase_requisition_details.price',
        'tb_inventory_purchase_requisition_details.unit',
        'tb_product_categories.category_name',
        'tb_products.product_name',
        'tb_products.product_code',
        'tb_inventory_purchase_requisitions.pr_number',
        'tb_inventory_purchase_requisitions.pr_date',
        'tb_inventory_purchase_requisitions.required_date',
        'tb_inventory_purchase_requisitions.status',
        'tb_inventory_purchase_requisitions.suggested_supplier',
        'tb_inventory_purchase_requisitions.deliver_to',
        'tb_inventory_purchase_requisitions.created_by',
        'tb_inventory_purchase_requisitions.notes'
      ));

      $this->connection->from('tb_inventory_purchase_requisition_details');
      $this->connection->join('tb_inventory_purchase_requisitions', 'tb_inventory_purchase_requisitions.id = tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id');
      $this->connection->join('tb_inventory_monthly_budgets', 'tb_inventory_monthly_budgets.id = tb_inventory_purchase_requisition_details.inventory_monthly_budget_id');
      $this->connection->join('tb_products', 'tb_products.id = tb_inventory_monthly_budgets.product_id');
      $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_inventory_purchase_requisitions.product_category_id');
      $this->connection->where('tb_inventory_purchase_requisition_details.id', $id);
      $query = $this->connection->get();
    } else {
      $this->db->select(array(
        'tb_inventory_purchase_requisition_details.id',
        'tb_inventory_purchase_requisition_details.additional_info',
        'tb_inventory_purchase_requisition_details.quantity',
        'tb_inventory_purchase_requisition_details.sisa',
        'tb_inventory_purchase_requisitions.item_category as "category_name"',
        'tb_inventory_purchase_requisition_details.product_name as "product_name"',
        'tb_inventory_purchase_requisition_details.part_number as "product_code"',
        'tb_inventory_purchase_requisitions.pr_number',
        'tb_inventory_purchase_requisitions.pr_date',
        'tb_inventory_purchase_requisitions.required_date',
        'tb_inventory_purchase_requisitions.status',
        'tb_inventory_purchase_requisitions.suggested_supplier',
        'tb_inventory_purchase_requisitions.deliver_to',
        'tb_inventory_purchase_requisitions.created_by',
        'tb_inventory_purchase_requisitions.notes',
        'tb_inventory_purchase_requisition_details.unit',
        'tb_inventory_purchase_requisition_details.serial_number',
      ));

      $this->db->from('tb_inventory_purchase_requisitions');
      $this->db->join('tb_inventory_purchase_requisition_details', 'tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id = tb_inventory_purchase_requisitions.id');
      $this->db->join('tb_budget', 'tb_budget.id = tb_inventory_purchase_requisition_details.budget_id');
      $this->db->join('tb_budget_cot', 'tb_budget_cot.id = tb_budget.id_cot');
      // $this->db->join('tb_master_items', 'tb_budget_cot.id_item = tb_master_items.id');
      // $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
      $this->db->where('tb_inventory_purchase_requisition_details.id', $id);
      $query = $this->db->get();
    }
    return $query->unbuffered_row('array');
  }

  public function searchRequestItem($category)
  {
    $select = array(
      'tb_inventory_purchase_requisition_details.*',
      'tb_inventory_purchase_requisitions.pr_number',
      'tb_inventory_purchase_requisitions.pr_date',
      'tb_inventory_purchase_requisitions.required_date',
      'tb_products.product_name',
    );

    $this->connection->select($select);
    $this->connection->from('tb_inventory_purchase_requisition_details');
    $this->connection->join('tb_inventory_purchase_requisitions', 'tb_inventory_purchase_requisitions.id = tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id');
    $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_inventory_purchase_requisitions.product_category_id');
    $this->connection->join('tb_inventory_monthly_budgets', 'tb_inventory_monthly_budgets.id = tb_inventory_purchase_requisition_details.inventory_monthly_budget_id');
    $this->connection->join('tb_products', 'tb_products.id = tb_inventory_monthly_budgets.product_id');
    $this->connection->where('UPPER(tb_product_categories.category_name)', $category);
    $this->connection->where('tb_inventory_purchase_requisitions.status', 'approved');
    $this->connection->like('tb_inventory_purchase_requisitions.pr_number', $this->budget_year);

    $this->connection->order_by('tb_products.product_name ASC, tb_inventory_purchase_requisition_details.part_number ASC');

    $query  = $this->connection->get();
    $result = $query->result_array();

    return $result;
  }

  function add_attachment_to_db($id_poe, $url)
  {
    $this->db->trans_begin();

    $this->db->set('id_poe', $id_poe);
    $this->db->set('file', $url);
    $this->db->insert('tb_attachment_poe');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  function delete_attachment_in_db($id_att)
  {
    $this->db->trans_begin();

    $this->db->where('id', $id_att);
    $this->db->delete('tb_attachment_poe');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function send_mail($doc_id)
  {
    $this->db->from('tb_purchase_orders');
    $this->db->where('id', $doc_id);
    $query = $this->db->get();
    $row = $query->unbuffered_row('array');

    $recipientList = $this->getNotifRecipient(9);
    $recipient = array();
    foreach ($recipientList as $key) {
      array_push($recipient, $key->email);
    }

    $from_email = "bifa.acd@gmail.com";
    $to_email = "aidanurul99@rocketmail.com";

    //Load email library 
    $this->load->library('email');
    // $config = array();
    // $config['protocol'] = 'mail';
    // $config['smtp_host'] = 'smtp.live.com';
    // $config['smtp_user'] = 'bifa.acd@gmail.com';
    // $config['smtp_pass'] = 'b1f42019';
    // $config['smtp_port'] = 587;
    // $config['smtp_auth']        = true;
    // $config['mailtype']         = 'html';
    // $this->email->initialize($config);
    $this->email->set_newline("\r\n");
    $message = "<p>Dear Chief of Maintenance</p>";
    $message .= "<p>Berikut permintaan Persetujuan untuk Purchase Order Evaluation :</p>";
    $message .= "<ul>";
    $message .= "</ul>";
    $message .= "<p>No Purchase Order Evaluation : " . $row['evaluation_number'] . "</p>";
    $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
    $message .= "<p>[ <a href='http://119.2.51.138:7323/purchase_order_evaluation/' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
    $message .= "<p>Thanks and regards</p>";
    $this->email->from($from_email, 'Material Resource Planning');
    $this->email->to($recipient);
    $this->email->subject('Permintaan Approval Purchase Order Evaluation No : ' . $row['evaluation_number']);
    $this->email->message($message);

    //Send mail 
    if ($this->email->send())
      return true;
    else
      return $this->email->print_debugger();
  }

  public function getNotifRecipient($level)
  {
    $this->db->select('email');
    $this->db->from('tb_auth_users');
    $this->db->where('auth_level', $level);
    return $this->db->get('')->result();
  }

  public function send_mail_approval($id, $ket, $by)
  {
    $item_message = '<tbody>';
    foreach ($id as $key) {
      $this->db->select(
        array(
          'tb_purchase_orders.document_number',
          'tb_purchase_order_items.description',
          'tb_purchase_order_items.part_number',
          'tb_purchase_order_items.quantity',
          'tb_purchase_order_items.total_amount',
          'tb_purchase_order_items.unit',
        )
      );
      $this->db->from('tb_purchase_order_items');
      $this->db->join('tb_purchase_orders', 'tb_purchase_orders.id=tb_purchase_order_items.purchase_order_id');
      $this->db->where('tb_purchase_orders.id', $key);
      $query = $this->db->get();
      $row = $query->result_array();

      foreach ($row as $item) {
        $item_message .= "<tr>";
        $item_message .= "<td>" . $item['evaluation_number'] . "</td>";
        $item_message .= "<td>" . $item['part_number'] . "</td>";
        $item_message .= "<td>" . $item['description'] . "</td>";
        $item_message .= "<td>" . print_number($item['quantity'], 2) . "</td>";
        $item_message .= "<td>" . $item['unit'] . "</td>";
        $item_message .= "<td>" . print_number($item['total_amount'], 2) . "</td>";
        $item_message .= "</tr>";
      }


      $this->db->select('created_by');
      $this->db->from('tb_purchase_orders');
      $this->db->where('id', $key);
      $query_po = $this->db->get();
      $row_po   = $query_po->unbuffered_row('array');
      $issued_by = $row_po['created_by'];

      $recipientList = $this->getNotifRecipient_approval($issued_by);
      $recipient = array();
      foreach ($recipientList as $key) {
        array_push($recipient, $key->email);
      }
    }
    $item_message .= '</tbody>';

    $from_email = "bifa.acd@gmail.com";
    $to_email = "aidanurul99@rocketmail.com";
    if ($ket == 'approve') {
      $ket_level = 'Disetujui';
    } else {
      $ket_level = 'Ditolak';
    }
    // if($level==14){
    //   $ket_level = 'Finance Manager';
    // }elseif ($level==10) {
    //   $ket_level = 'Head Of School';
    // } elseif($level==11){
    //   $ket_level = 'Chief Of Finance';
    // }elseif($level==3){
    //   $ket_level = 'VP Finance';
    // }

    //Load email library 
    $this->load->library('email');
    // $config = array();
    // $config['protocol'] = 'mail';
    // $config['smtp_host'] = 'smtp.live.com';
    // $config['smtp_user'] = 'bifa.acd@gmail.com';
    // $config['smtp_pass'] = 'b1f42019';
    // $config['smtp_port'] = 587;
    // $config['smtp_auth']        = true;
    // $config['mailtype']         = 'html';
    // $this->email->initialize($config);
    $this->email->set_newline("\r\n");
    $message = "<p>Hello</p>";
    $message .= "<p>Item Berikut telah " . $ket_level . " oleh " . $by . "</p>";
    $message .= "<table>";
    $message .= "<thead>";
    $message .= "<tr>";
    $message .= "<th>No. Doc.</th>";
    $message .= "<th>Part Number</th>";
    $message .= "<th>Description</th>";
    $message .= "<th>Qty Order</th>";
    $message .= "<th>Unit</th>";
    $message .= "<th>Total Val. Order</th>";
    $message .= "</tr>";
    $message .= "</thead>";
    $message .= $item_message;
    $message .= "</table>";
    // $message .= "<p>No Purchase Request : ".$row['document_number']."</p>";    
    $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
    $message .= "<p>[ <a href='http://119.2.51.138:7323/purchase_order/' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
    $message .= "<p>Thanks and regards</p>";
    $this->email->from($from_email, 'Material Resource Planning');
    $this->email->to($recipient);
    $this->email->subject('Notification Approval');
    $this->email->message($message);

    //Send mail 
    if ($this->email->send())
      return true;
    else
      return $this->email->print_debugger();
  }

  public function getNotifRecipient_approval($name)
  {
    $this->db->select('email');
    $this->db->from('tb_auth_users');
    $this->db->where('person_name', $name);
    return $this->db->get('')->result();
  }

  public function getStatusEditPoe($id)
  {
    
    $this->db->from('tb_purchase_order_vendors');
    $this->db->where('purchase_order_id', $id);
    $this->db->where('is_selected', 't');
    $num_rows = $this->db->count_all_results();

    return ($num_rows > 0) ? 'yes' : 'no';
  }
}
