<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Receipt_Nota_Model extends MY_Model
{
  protected $connection;

  public function __construct()
  {
    parent::__construct();
    //Do your magic here
    $this->connection   = $this->load->database('budgetcontrol', TRUE);
  }

  public function getSuplier()
  {
    $this->db->select('tb_master_vendors.id,tb_master_vendors.vendor,tb_master_vendors.code');
    $this->db->from('tb_master_vendors');
    $this->db->order_by('tb_master_vendors.vendor','asc');
    return $this->db->get('')->result();
  }

  public function getData()
  {

        $select = array(
            'tb_po.id',
            'tb_po.vendor',
            'tb_po.document_number',
            'tb_po.grand_total'
        );
        $this->db->select($select);
        $this->db->join('tb_po_item','tb_po_item.purchase_order_id=tb_po.id');
        $this->db->join('tb_receipt_items','tb_receipt_items.purchase_order_item_id=tb_po_item.id');
        $this->db->join('tb_receipts','tb_receipts.document_number=tb_receipt_items.document_number');
        $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id=tb_receipt_items.stock_in_stores_id');
        $this->db->order_by('tb_po.vendor','asc');
        $this->db->group_by(array(
            'tb_po.id',
            'tb_po.vendor',
            'tb_po.document_number',
            'tb_po.grand_total'
        ));
        $this->db->like('tb_receipts.document_number', 'GRN');

        if (!empty($_GET['vendor']) && $_GET['vendor'] != 'all') {
            $vendor = $_GET['vendor'];
            $this->db->where('tb_po.vendor', $vendor);
        }

        if (!empty($_GET['date'])) {
            $date = $_GET['date'];
            $range_date  = explode('.', $_GET['date']);
            $start_date  = $range_date[0];
            $end_date    = $range_date[1];

            $this->db->where('tb_receipts.received_date >=', $start_date);
            $this->db->where('tb_receipts.received_date <=', $end_date);
        }
        $query      = $this->db->get('tb_po');
        $return       = $query->result_array();

        foreach ($return as $key => $value) {
            $select = array(
                'tb_receipt_items.reference_number',
                'tb_stock_in_stores.tgl_nota',
                'sum(case when tb_receipt_items.received_total_value is null then 0.00 else tb_receipt_items.received_total_value end) as "received_total_value_idr"',
                'sum(case when tb_receipt_items.received_total_value_dollar is null then 0.00 else tb_receipt_items.received_total_value_dollar end) as "received_total_value_dollar"'
            );
            $this->db->select($select);
            // $this->db->join('tb_po_item','tb_po_item.purchase_order_id=tb_po.id');
            $this->db->join('tb_po_item','tb_receipt_items.purchase_order_item_id=tb_po_item.id');
            $this->db->join('tb_receipts','tb_receipts.document_number=tb_receipt_items.document_number');
            $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id=tb_receipt_items.stock_in_stores_id');
            // $this->db->order_by('tb_po.vendor','asc');
            $this->db->group_by(array(
                'tb_receipt_items.reference_number',
                'tb_stock_in_stores.tgl_nota',
            ));
            $this->db->like('tb_receipts.document_number', 'GRN');
            $this->db->where('tb_po_item.purchase_order_id', $value['id']);
    
            if (!empty($_GET['date'])) {
                $date = $_GET['date'];
                $range_date  = explode('.', $_GET['date']);
                $start_date  = $range_date[0];
                $end_date    = $range_date[1];
    
                $this->db->where('tb_receipts.received_date >=', $start_date);
                $this->db->where('tb_receipts.received_date <=', $end_date);
            }
            $queryreceipt       = $this->db->get('tb_receipt_items');
            $receipt            = $queryreceipt->result_array();

            $return[$key]['receipt'] = $receipt;
        }

        return $return;
    }
  
}

/* End of file Account_Payable_Model.php */
/* Location: ./application/models/Account_Payable_Model.php */
