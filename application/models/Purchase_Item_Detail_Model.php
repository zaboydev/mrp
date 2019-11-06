<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_Item_Detail_Model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        //Do your magic here
    }

    public function getSuplier()
    {
        $this->db->select('tb_master_vendors.id,tb_master_vendors.vendor,tb_master_vendors.code');
        // $this->db->join('tb_master_vendors_currency', 'tb_master_vendors_currency.vendor=tb_master_vendors.vendor');
        // $this->db->where('tb_master_vendors_currency.currency', $currency);
        $this->db->from('tb_master_vendors');
        $this->db->order_by('tb_master_vendors.id','asc');
        return $this->db->get('')->result();
    }

    public function getItems()
    {
        $this->db->select('tb_master_part_number.id,tb_master_part_number.part_number,tb_master_part_number.description');
        // $this->db->join('tb_master_vendors_currency', 'tb_master_vendors_currency.vendor=tb_master_vendors.vendor');
        // $this->db->where('tb_master_vendors_currency.currency', $currency);
        $this->db->from('tb_master_part_number');
        $this->db->order_by('tb_master_part_number.part_number', 'asc');
        return $this->db->get('')->result();
    }

    public function getPurchaseItem($vendor, $currency, $date){

        $this->db->select(
            array(
                'tb_po_item.part_number',
                'tb_po_item.description'
            )
        );
        $this->db->join('tb_po','tb_po.id=tb_po_item.purchase_order_id');
        $this->db->group_by(array(
            'tb_po_item.part_number',
            'tb_po_item.description'
        ));
        // $this->db->where('tb_po.review_status !=','REVISI');
        $this->db->where_not_in('tb_po.review_status', ['REVISI']);
        $this->db->where_not_in('tb_po.status', ['PURPOSED']);
        if($date!=null){
            $range_date  = explode('.', $date);
            $start_date  = $range_date[0];
            $end_date    = $range_date[1];

            $this->db->where('tb_po.document_date >=',$start_date);
            $this->db->where('tb_po.document_date <=', $end_date);
        }
        if($vendor!=null && $vendor!='all'){
            $this->db->where('tb_po.vendor', $vendor);
        }
        if ($currency != null && $currency != 'all') {
            $this->db->where('tb_po.default_currency', $currency);
        }
        $query      = $this->db->get('tb_po_item');
        $item       = $query->result_array();

        // $select = array(
        //     'tb_po.document_number',
        //     'tb_po.vendor',
        //     'tb_po.document_date',
        //     'tb_po.status',
        //     'tb_po.due_date',
        //     'tb_po_item.quantity',
        //     'tb_po_item.total_amount'
        // );

        foreach ($item as $key => $value) {

            $item[$key]['items_po'] = $this->getDetailItem($value['part_number'], $vendor, $currency, $date);
        }

        return $item;
    }

    function getDetailItem($part_number, $vendor, $currency, $date){
        $select = array(
            'tb_po.document_number',
            'tb_po.vendor',
            'tb_po.document_date',
            'tb_po.status',
            'tb_po.due_date',
            'tb_po_item.quantity',
            'tb_po_item.total_amount'
        );

        $this->db->select($select);
        $this->db->from('tb_po_item');
        $this->db->join('tb_po', 'tb_po.id = tb_po_item.purchase_order_id');
        $this->db->where('tb_po_item.part_number', $part_number);
        // $this->db->where('tb_po.review_status !=', 'REVISI');
        $this->db->where_not_in('tb_po.review_status', ['REVISI']);
        $this->db->where_not_in('tb_po.status', ['PURPOSED']);
        // $this->db->where('tb_po.default_currency', $currency);
        if ($date != null) {
            $range_date  = explode('.', $date);
            $start_date  = $range_date[0];
            $end_date    = $range_date[1];

            $this->db->where('tb_po.document_date >=', $start_date);
            $this->db->where('tb_po.document_date <=', $end_date);
        }
        if ($vendor != null && $vendor != 'all') {
            $this->db->where('tb_po.vendor', $vendor);
        }
        if ($currency != null && $currency != 'all') {
            $this->db->where('tb_po.default_currency', $currency);
        }
        $query = $this->db->get();
        $prl_item['po_items_count'] = $query->num_rows();

        foreach ($query->result_array() as $key => $value) {
            $prl_item['po_items'][$key] = $value;
        }


        return $prl_item;
    }

    public function getPurchaseSummary($vendor, $currency, $date)
    {

        $this->db->select(
            array(
                'tb_po.vendor'
            )
        );
        // $this->db->join('tb_po', 'tb_po.id=tb_po_item.purchase_order_id');
        $this->db->group_by(array(
            'tb_po.vendor',
            // 'tb_po_item.description'
        ));
        $this->db->where_not_in('tb_po.review_status', ['REVISI']);
        $this->db->where_not_in('tb_po.status', ['PURPOSED']);
        if ($date != null) {
            $range_date  = explode('.', $date);
            $start_date  = $range_date[0];
            $end_date    = $range_date[1];

            $this->db->where('tb_po.document_date >=', $start_date);
            $this->db->where('tb_po.document_date <=', $end_date);
        }
        if ($vendor != null && $vendor != 'all') {
            $this->db->where('tb_po.vendor', $vendor);
        }
        if ($currency != null && $currency != 'all') {
            $this->db->where('tb_po.default_currency', $currency);
        }
        $query      = $this->db->get('tb_po');
        $item       = $query->result_array();

        // $select = array(
        //     'tb_po.document_number',
        //     'tb_po.vendor',
        //     'tb_po.document_date',
        //     'tb_po.status',
        //     'tb_po.due_date',
        //     'tb_po_item.quantity',
        //     'tb_po_item.total_amount'
        // );

        foreach ($item as $key => $value) {

            $item[$key]['po'] = $this->getDetailSummary($value['vendor'], $currency, $date);
        }

        return $item;
    }

    function getDetailSummary($vendor, $currency, $date)
    {
        $select = array(
            'tb_po.document_number',
            'tb_po.default_currency',
            'tb_po.document_date',
            'tb_po.status',
            'tb_po.due_date',
            'tb_po.taxes',
            'tb_po.grand_total',
            'tb_po.remaining_payment'

        );

        $this->db->select($select);
        $this->db->from('tb_po');
        // $this->db->join('tb_po', 'tb_po.id = tb_po_item.purchase_order_id');
        $this->db->where('tb_po.vendor', $vendor);
        $this->db->where_not_in('tb_po.review_status', ['REVISI']);
        $this->db->where_not_in('tb_po.status', ['PURPOSED']);
        // $this->db->where('tb_po.default_currency', $currency);
        if ($date != null) {
            $range_date  = explode('.', $date);
            $start_date  = $range_date[0];
            $end_date    = $range_date[1];

            $this->db->where('tb_po.document_date >=', $start_date);
            $this->db->where('tb_po.document_date <=', $end_date);
        }
        if ($vendor != null && $vendor != 'all') {
            $this->db->where('tb_po.vendor', $vendor);
        }
        if ($currency != null && $currency != 'all') {
            $this->db->where('tb_po.default_currency', $currency);
        }
        $query = $this->db->get();
        $prl_item['po_count'] = $query->num_rows();

        foreach ($query->result_array() as $key => $value) {
            $prl_item['po_detail'][$key] = $value;
        }


        return $prl_item;
    }

    public function getSummaryPayment($vendor, $currency, $date)
    {

        $this->db->select(
            array(
                'tb_po.vendor'
            )
        );
        $this->db->join('tb_po_item', 'tb_po.id=tb_po_item.purchase_order_id');
        $this->db->join('tb_purchase_order_items_payments', 'tb_purchase_order_items_payments.purchase_order_item_id=tb_po_item.id');
        $this->db->group_by(array(
            'tb_po.vendor',
            // 'tb_po_item.description'
        ));
        $this->db->where_not_in('tb_po.review_status', ['REVISI']);
        $this->db->where_not_in('tb_po.status', ['PURPOSED']);
        if ($date != null) {
            $range_date  = explode('.', $date);
            $start_date  = $range_date[0];
            $end_date    = $range_date[1];

            $this->db->where('tb_purchase_order_items_payments.tanggal >=', $start_date);
            $this->db->where('tb_purchase_order_items_payments.tanggal <=', $end_date);
        }
        if ($vendor != null && $vendor != 'all') {
            $this->db->where('tb_po.vendor', $vendor);
        }
        if ($currency != null && $currency != 'all') {
            $this->db->where('tb_po.default_currency', $currency);
        }
        $query      = $this->db->get('tb_po');
        $item       = $query->result_array();

        // $select = array(
        //     'tb_po.document_number',
        //     'tb_po.vendor',
        //     'tb_po.document_date',
        //     'tb_po.status',
        //     'tb_po.due_date',
        //     'tb_po_item.quantity',
        //     'tb_po_item.total_amount'
        // );

        foreach ($item as $key => $value) {

            $item[$key]['po'] = $this->getDetailSummaryPayment($value['vendor'], $currency, $date);
        }

        return $item;
    }

    function getDetailSummaryPayment($vendor, $currency, $date)
    {
        $select = array(
            'tb_purchase_order_items_payments.no_cheque',
            'tb_purchase_order_items_payments.tanggal',
            'tb_po.document_number',
            'tb_po.document_date',
            'tb_po.grand_total',
            'tb_purchase_order_items_payments.amount_paid'
        );

        $this->db->select($select);
        $this->db->from('tb_po');
        $this->db->join('tb_po_item', 'tb_po.id=tb_po_item.purchase_order_id');
        $this->db->join('tb_purchase_order_items_payments', 'tb_purchase_order_items_payments.purchase_order_item_id=tb_po_item.id');
        // $this->db->join('tb_po', 'tb_po.id = tb_po_item.purchase_order_id');
        $this->db->where('tb_po.vendor', $vendor);
        $this->db->where_not_in('tb_po.review_status', ['REVISI']);
        $this->db->where_not_in('tb_po.status', ['PURPOSED']);
        // $this->db->where('tb_po.default_currency', $currency);
        if ($date != null) {
            $range_date  = explode('.', $date);
            $start_date  = $range_date[0];
            $end_date    = $range_date[1];

            $this->db->where('tb_purchase_order_items_payments.tanggal >=', $start_date);
            $this->db->where('tb_purchase_order_items_payments.tanggal <=', $end_date);
        }
        if ($vendor != null && $vendor != 'all') {
            $this->db->where('tb_po.vendor', $vendor);
        }
        if ($currency != null && $currency != 'all') {
            $this->db->where('tb_po.default_currency', $currency);
        }
        $query = $this->db->get();
        $prl_item['po_count'] = $query->num_rows();

        foreach ($query->result_array() as $key => $value) {
            $prl_item['po_detail'][$key] = $value;
        }


        return $prl_item;
    }

    public function getPurchaseItemSummary($items, $currency, $date)
    {

        $this->db->select(
            array(
                'tb_master_items.part_number',
                'tb_master_items.description'
            )
        );
        // $this->db->from('tb_master_items');
        $this->db->join('tb_stocks', 'tb_stocks.item_id = tb_master_items.id');
        $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.stock_id = tb_stocks.id');
        $this->db->join('tb_receipt_items', 'tb_receipt_items.stock_in_stores_id = tb_stock_in_stores.id');
        $this->db->join('tb_receipts', 'tb_receipt_items.document_number = tb_receipts.document_number');
        $this->db->like('tb_receipts.document_number', 'GRN');
        $this->db->group_by(array(
            'tb_master_items.part_number',
            'tb_master_items.description'
        ));
        if ($date != null) {
            $range_date  = explode('.', $date);
            $start_date  = $range_date[0];
            $end_date    = $range_date[1];

            $this->db->where('tb_receipts.received_date >=', $start_date);
            $this->db->where('tb_receipts.received_date <=', $end_date);
        }
        if ($items != null && $items != 'all') {
            $this->db->where('tb_master_items.part_number', $items);
        }
        // if ($currency != null && $currency != 'all') {
        //     $this->db->where('tb_po.default_currency', $currency);
        // }
        $query      = $this->db->get('tb_master_items');
        $item       = $query->result_array();

        foreach ($item as $key => $value) {

            $item[$key]['base'] = $this->getBasePurchaseItemSummary($value['part_number'], $currency, $date);
        }

        return $item;
    }

    public function getBasePurchaseItemSummary($items, $currency, $date)
    {

        $this->db->select(
            array(
                'tb_receipts.warehouse'
            )
        );
        $this->db->join('tb_receipt_items', 'tb_receipt_items.document_number = tb_receipts.document_number');
        $this->db->join('tb_stock_in_stores', 'tb_receipt_items.stock_in_stores_id = tb_stock_in_stores.id');
        $this->db->join('tb_stocks', 'tb_stock_in_stores.stock_id = tb_stocks.id');
        $this->db->join('tb_master_items', 'tb_stocks.item_id = tb_master_items.id' );
        $this->db->like('tb_receipts.document_number', 'GRN');
        $this->db->group_by(array(
            'tb_receipts.warehouse',
            // 'tb_master_items.description'
        ));
        if ($date != null) {
            $range_date  = explode('.', $date);
            $start_date  = $range_date[0];
            $end_date    = $range_date[1];

            $this->db->where('tb_receipts.received_date >=', $start_date);
            $this->db->where('tb_receipts.received_date <=', $end_date);
        }
        if ($items != null && $items != 'all') {
            $this->db->where('tb_master_items.part_number', $items);
        }
        // if ($currency != null && $currency != 'all') {
        //     $this->db->where('tb_po.default_currency', $currency);
        // }
        $query      = $this->db->get('tb_receipts');
        $item       = $query->result_array();

        // $select = array(
        //     'tb_po.document_number',
        //     'tb_po.vendor',
        //     'tb_po.document_date',
        //     'tb_po.status',
        //     'tb_po.due_date',
        //     'tb_po_item.quantity',
        //     'tb_po_item.total_amount'
        // );

        foreach ($item as $key => $value) {

            $item[$key]['items_grn'] = $this->getPurchaseItemSummaryDetail($value['warehouse'],$items, $currency, $date);
        }

        return $item;
    }

    function getPurchaseItemSummaryDetail($warehouse,$part_number, $currency, $date)
    {
        $select = array(
            'tb_receipts.received_from',
            'SUM(tb_receipt_items.received_quantity) as quantity',
            'SUM(tb_receipt_items.received_total_value) as total_value'
        );

        $this->db->select($select);
        $this->db->from('tb_receipt_items');
        $this->db->join('tb_receipts', 'tb_receipt_items.document_number = tb_receipts.document_number');
        $this->db->join('tb_stock_in_stores', 'tb_receipt_items.stock_in_stores_id = tb_stock_in_stores.id');
        $this->db->join('tb_stocks', 'tb_stock_in_stores.stock_id = tb_stocks.id');
        $this->db->join('tb_master_items', 'tb_stocks.item_id = tb_master_items.id');
        $this->db->like('tb_receipts.document_number', 'GRN');
        $this->db->group_by(array(
            'tb_receipts.received_from',
            // 'tb_master_items.description'
        ));
        if ($date != null) {
            $range_date  = explode('.', $date);
            $start_date  = $range_date[0];
            $end_date    = $range_date[1];

            $this->db->where('tb_receipts.received_date >=', $start_date);
            $this->db->where('tb_receipts.received_date <=', $end_date);
        }
        if ($part_number != null && $part_number != 'all') {
            $this->db->where('tb_master_items.part_number', $part_number);
        }
        if ($warehouse != null && $warehouse != 'all') {
            $this->db->where('tb_receipts.warehouse', $warehouse);
        }
        $query      = $this->db->get();
        // $item       = $query->result_array();
        // $query = $this->db->get();
        $prl_item['grn_items_count'] = $query->num_rows();

        foreach ($query->result_array() as $key => $value) {
            $prl_item['grn_items'][$key] = $value;
        }


        return $prl_item;
    }


}

/* End of file Account_Payable_Model.php */
/* Location: ./application/models/Account_Payable_Model.php */
