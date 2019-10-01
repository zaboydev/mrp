<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Account_Payable_Model extends MY_Model
{
  public function __construct()
  {
    parent::__construct();
    //Do your magic here
  }
  public function getSelectedColumns()
  {
    return array(
      // "''".' as "temp"' => "Act.", 
      'tb_po.id' => NULL,
      'tb_po.document_date'                => 'Date',
      'tb_po.document_number'              => 'Document Number',
      'tb_po.vendor'                       => 'Vendor',
      'tb_po.grand_total'                  => 'Total Amount',
      'tb_po.payment'                      => 'Amount Due',
      'tb_po.remaining_payment'                      => 'Remaining Payment',
      'tb_po.status'                       => 'Review Status',
      // 'tb_po.category'        => 'Category',


    );
  }
  public function getSearchableColumns()
  {
    $return = array(
      'tb_po.id',
      'tb_po.document_number',
      'tb_po.status',
      'tb_po.document_date',
      'tb_po.vendor'

    );

    return $return;
  }

  public function getGroupedColumns()
  {
    return array(
      'tb_po.id',
      'tb_po.document_number',
      'tb_po.status',
      'tb_po.document_date',
      'tb_po.vendor'
    );
  }

  public function getOrderableColumns()
  {
    $return = array(
      'tb_po.id',
      'tb_po.document_number',
      'tb_po.status',
      'tb_po.document_date',
      'tb_po.vendor'
      // 'tb_po_item.total_amount',
    );
    return $return;
  }

  private function searchIndex()
  {
    if (!empty($_POST['columns'][2]['search']['value'])) {
      $search_received_date = $_POST['columns'][2]['search']['value'];
      $range_received_date  = explode(' ', $search_received_date);

      $this->db->where('tb_po.document_date >= ', $range_received_date[0]);
      $this->db->where('tb_po.document_date <= ', $range_received_date[1]);
    }

    if (!empty($_POST['columns'][1]['search']['value'])) {
      $vendor = $_POST['columns'][1]['search']['value'];

      $this->db->where('tb_po.vendor', $vendor);
    }

    if (!empty($_POST['columns'][3]['search']['value'])) {
      $status = $_POST['columns'][3]['search']['value'];

      if($status != 'all'){
        $this->db->where('tb_po.status', $status);
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
    $this->db->from('tb_po');
    $this->db->where_in('tb_po.status', ['ORDER', 'OPEN', 'CLOSED','ADVANCE']);
    // $this->db->join('tb_po_item','tb_po.purchase_order_id=tb_po_item.id');
    // $this->db->group_by($this->getGroupedColumns());
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
    $this->db->from('tb_po');
    $this->db->where_in('tb_po.status', ['ORDER', 'OPEN', 'CLOSED', 'ADVANCE']);
    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->from('tb_po');
    $this->db->where_in('tb_po.status', ['ORDER', 'OPEN', 'CLOSED', 'ADVANCE']);
    $query = $this->db->get();

    return $query->num_rows();
  }
  public function findById($id)
  {
    $this->db->where('id', $id);

    $query    = $this->db->get('tb_po');
    $po = $query->unbuffered_row('array');

    $select = array(
      'tb_po_item.*',
      
    );

    $this->db->select($select);
    $this->db->from('tb_po_item');    
    $this->db->where('tb_po_item.purchase_order_id', $po['id']);

    $query = $this->db->get();

    foreach ($query->result_array() as $key => $value) {
      $po['items'][$key] = $value;
    }

    $select_payment = array(
      'tb_po_item.part_number',
      'tb_po_item.description',
      'tb_po_item.total_amount',
      'tb_purchase_order_items_payments.*'
    );

    $this->db->select($select_payment);
    $this->db->from('tb_purchase_order_items_payments');
    $this->db->join('tb_po_item','tb_po_item.id=tb_purchase_order_items_payments.purchase_order_item_id');
    $this->db->where('tb_po_item.purchase_order_id', $po['id']);

    $query_payment = $this->db->get();

    foreach ($query_payment->result_array() as $key => $value) {
      $po['payments'][$key] = $value;
    }
    $po['count_payment'] = $query_payment->num_rows();

    return $po;
  }
  public function urgent($id)
  {
    $this->db->where('id', $id);
    $this->db->set('status', 'urgent');
    return $this->db->update('tb_hutang');
  }

  public function getNotifRecipient()
  {
    $this->db->select('email');
    $this->db->from('tb_auth_users');
    $this->db->where('auth_level', 2);
    return $this->db->get('')->result();
  }
}

/* End of file Account_Payable_Model.php */
/* Location: ./application/models/Account_Payable_Model.php */
