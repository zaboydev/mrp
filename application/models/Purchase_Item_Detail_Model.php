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
        $this->db->order_by('tb_master_vendors.vendor','asc');
        return $this->db->get('')->result();
    }

    public function getItems()
    {
        $this->db->select('tb_master_part_number.id,tb_master_part_number.part_number,tb_master_part_number.description');
        $this->db->group_by('tb_master_part_number.id,tb_master_part_number.part_number,tb_master_part_number.description');
        $this->db->from('tb_master_part_number');
        $this->db->order_by('tb_master_part_number.part_number', 'asc');
        return $this->db->get('')->result();
    }

    public function getAllItems(){
        $this->db->from('tb_master_items');
        $this->db->order_by('tb_master_items.part_number', 'asc');
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
        $this->db->join('tb_receipt_items', 'tb_po_item.id=tb_receipt_items.purchase_order_item_id');
        $this->db->group_by(array(
            'tb_po_item.part_number',
            'tb_po_item.description'
        ));
        // $this->db->where('tb_po.review_status !=','REVISI');
        // $this->db->where_not_in('tb_po.review_status', ['REVISI']);
        // $this->db->where_not_in('tb_po.status', ['PURPOSED']);
        if($date!=null){
            $range_date  = explode('.', $date);
            $start_date  = $range_date[0];
            $end_date    = $range_date[1];

            $this->db->where('tb_receipt_items.received_date_item >=',$start_date);
            $this->db->where('tb_receipt_items.received_date_item <=', $end_date);
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
            'tb_receipt_items.purchase_order_number as document_number',
            'tb_po.vendor',
            'tb_receipt_items.received_date_item as document_date',
            'tb_po.status',
            'tb_receipt_items.kurs_dollar',
            'tb_po.due_date',
            'tb_po_item.unit',
            'tb_receipt_items.quantity_order as quantity',
            '(tb_receipt_items.value_order*tb_receipt_items.quantity_order) as total_amount'
        );

        $this->db->select($select);
        $this->db->from('tb_po_item');
        $this->db->join('tb_po', 'tb_po.id = tb_po_item.purchase_order_id');
        $this->db->join('tb_receipt_items', 'tb_po_item.id=tb_receipt_items.purchase_order_item_id');
        $this->db->where('tb_po_item.part_number', $part_number);
        // $this->db->where('tb_po.review_status !=', 'REVISI');
        // $this->db->where_not_in('tb_po.review_status', ['REVISI']);
        // $this->db->where_not_in('tb_po.status', ['PURPOSED']);
        // $this->db->where('tb_po_item.quantity_received != 0');
        if ($date != null) {
            $range_date  = explode('.', $date);
            $start_date  = $range_date[0];
            $end_date    = $range_date[1];

            $this->db->where('tb_receipt_items.received_date_item >=', $start_date);
            $this->db->where('tb_receipt_items.received_date_item <=', $end_date);
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
        $this->db->where_not_in('tb_po.status', ['PURPOSED','ORDER']);
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
        $this->db->where_not_in('tb_po.status', ['PURPOSED', 'ORDER']);
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
        $this->db->where('tb_purchase_order_items_payments.status', 'PAID');
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
            'tb_po.default_currency',
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
        $this->db->where('tb_purchase_order_items_payments.status', 'PAID');
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

    // public function getPurchaseItemSummary($items, $currency,$vendor, $date)
    public function getPurchaseItemSummary()
    {
        if (!empty($_GET['items']) && $_GET['items'] != 'all') {
            $item     = $_GET['items'];
            $part_number    = $this->getPartNumberById($item);
        }else{
            $part_number = 'all';
        }
        $currency = 'all';
        $vendor = 'all';
        $date = 'all';

        $this->db->select(
            array(
                'tb_master_items.part_number',
                'tb_master_items.description'
            )
        );
        $this->db->from('tb_master_items');
        $this->db->join('tb_stocks', 'tb_stocks.item_id = tb_master_items.id');
        $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.stock_id = tb_stocks.id');
        $this->db->join('tb_receipt_items', 'tb_receipt_items.stock_in_stores_id = tb_stock_in_stores.id');
        $this->db->join('tb_receipts', 'tb_receipt_items.document_number = tb_receipts.document_number');
        $this->db->join('tb_po_item', 'tb_receipt_items.purchase_order_item_id = tb_po_item.id');
        $this->db->like('tb_receipts.document_number', 'GRN');
        $this->db->group_by(array(
            'tb_master_items.part_number',
            'tb_master_items.description'
        ));
        if (!empty($_GET['date'])) {
            $date = $_GET['date'];
            $range_date  = explode('.', $_GET['date']);
            $start_date  = $range_date[0];
            $end_date    = $range_date[1];

            $this->db->where('tb_receipts.received_date >=', $start_date);
            $this->db->where('tb_receipts.received_date <=', $end_date);
        }
        if ($part_number != 'all') {
            $this->db->where('tb_master_items.part_number', $part_number);
        }
        if (!empty($_GET['currency']) && $_GET['currency'] != 'all') {
            $currency = $_GET['currency'];
            if($currency=='IDR'){
                $this->db->where('tb_receipt_items.kurs_dollar',1);
            }else{
                $this->db->where('tb_receipt_items.kurs_dollar > 1');
            }
        }

        if (!empty($_GET['vendor']) && $_GET['vendor'] != 'all') {
            $vendor = $_GET['vendor'];
            $this->db->where('tb_receipts.received_from', $vendor);
        }
        $query      = $this->db->get();
        $item       = $query->result_array();

        foreach ($item as $key => $value) {

            $item[$key]['base'] = $this->getBasePurchaseItemSummary($value['part_number'], $currency, $vendor, $date);
        }

        return $item;
    }

    public function getPartNumberById($id){
        $this->db->select('part_number');
        $this->db->from('tb_master_part_number');

        $this->db->where('id', $id);

        $query  = $this->db->get();
        $row    = $query->unbuffered_row();
        $return = $row->part_number;

        return $return;
    }

    public function getBasePurchaseItemSummary($items, $currency, $vendor, $date)
    {

        $this->db->select(
            array(
                'tb_receipts.warehouse'
            )
        );
        $this->db->join('tb_receipt_items', 'tb_receipt_items.document_number = tb_receipts.document_number');
        $this->db->join('tb_po_item', 'tb_receipt_items.purchase_order_item_id = tb_po_item.id');
        $this->db->join('tb_stock_in_stores', 'tb_receipt_items.stock_in_stores_id = tb_stock_in_stores.id');
        $this->db->join('tb_stocks', 'tb_stock_in_stores.stock_id = tb_stocks.id');
        $this->db->join('tb_master_items', 'tb_stocks.item_id = tb_master_items.id' );
        $this->db->like('tb_receipts.document_number', 'GRN');
        $this->db->group_by(array(
            'tb_receipts.warehouse',
            // 'tb_master_items.description'
        ));
        if ($date != null && $date != 'all') {
            $range_date  = explode('.', $date);
            $start_date  = $range_date[0];
            $end_date    = $range_date[1];

            $this->db->where('tb_receipts.received_date >=', $start_date);
            $this->db->where('tb_receipts.received_date <=', $end_date);
        }
        if ($items != null && $items != 'all') {
            $this->db->where('tb_master_items.part_number', $items);
        }
        if ($currency != null && $currency != 'all') {
            if ($currency == 'IDR') {
                $this->db->where('tb_receipt_items.kurs_dollar', 1);
            } else {
                $this->db->where('tb_receipt_items.kurs_dollar > 1');
            }
        }

        if ($vendor != null && $vendor != 'all') {
            $this->db->where('tb_receipts.received_from', $vendor);
        }
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

            $item[$key]['items_grn'] = $this->getPurchaseItemSummaryDetail($value['warehouse'],$items, $currency, $vendor, $date);
        }

        return $item;
    }

    function getPurchaseItemSummaryDetail($warehouse,$part_number, $currency, $vendor, $date)
    {
        $select = array(
            'tb_receipts.received_from',
            'tb_master_items.unit',
            'SUM(tb_receipt_items.received_quantity) as quantity',
            'SUM(tb_receipt_items.received_total_value) as total_value_idr',
            'SUM(tb_receipt_items.received_total_value_dollar) as total_value_usd',
            'tb_receipt_items.kurs_dollar'
        );

        $this->db->select($select);
        $this->db->from('tb_receipt_items');
        $this->db->join('tb_po_item', 'tb_receipt_items.purchase_order_item_id = tb_po_item.id');
        $this->db->join('tb_receipts', 'tb_receipt_items.document_number = tb_receipts.document_number');
        $this->db->join('tb_stock_in_stores', 'tb_receipt_items.stock_in_stores_id = tb_stock_in_stores.id');
        $this->db->join('tb_stocks', 'tb_stock_in_stores.stock_id = tb_stocks.id');
        $this->db->join('tb_master_items', 'tb_stocks.item_id = tb_master_items.id');
        $this->db->like('tb_receipts.document_number', 'GRN');
        $this->db->group_by(array(
            'tb_receipts.received_from',
            'tb_master_items.unit',
            'tb_receipt_items.kurs_dollar'
            // 'tb_master_items.description'
        ));
        if ($date != null && $date != 'all') {
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
        if ($currency != null && $currency != 'all') {
            if ($currency == 'IDR') {
                $this->db->where('tb_receipt_items.kurs_dollar', 1);
            } else {
                $this->db->where('tb_receipt_items.kurs_dollar > 1');
            }
        }

        if ($vendor != null && $vendor != 'all') {
            $this->db->where('tb_receipts.received_from', $vendor);
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

    // public function getPayableReconciliation($vendor, $currency, $date, $method)
    public function getPayableReconciliation()
    {

        $select = array(
            'tb_po.vendor',
            'tb_po.default_currency as currency',
            'sum(tb_hutang.amount_idr) as idr',
            'sum(tb_hutang.amount_usd) as usd',
            'sum(tb_purchase_order_items_payments.paid) as payment'
        );
        $this->db->select($select);
        $this->db->join('tb_hutang','tb_hutang.id_po=tb_po.id','left');
        $this->db->join('tb_purchase_order_items_payments', 'tb_purchase_order_items_payments.id_po=tb_po.id','left');
        $this->db->order_by('tb_po.vendor','asc');
        $this->db->group_by(array(
            'tb_po.vendor',
            'tb_po.default_currency'
        ));
        $this->db->where_not_in('tb_po.review_status', ['REVISI']);
        $this->db->where_not_in('tb_po.status', ['PURPOSED']);

        if (!empty($_GET['vendor']) && $_GET['vendor'] != 'all') {
            $vendor = $_GET['vendor'];
            $this->db->where('tb_po.vendor', $vendor);
        }
        if (!empty($_GET['currency']) && $_GET['currency'] != 'all') {
            $currency = $_GET['currency'];
            $this->db->where('tb_po.default_currency', $currency);
        }
        $query      = $this->db->get('tb_po');
        $item       = $query->result_array();



        foreach ($item as $key => $value) {
            $prl_item = array();

            // $item[$key]['po'] = $this->getDetailPayableReconciliation($value['vendor'], $currency, $date,$method);
            $select = array(
                'tb_hutang.amount_idr',
                'tb_hutang.amount_usd',
                'tb_purchase_order_items_payments.paid',
                'tb_po.due_date',
                'tb_po.default_currency',
                'tb_hutang.tanggal'
            );
            $this->db->select($select);
            $this->db->join('tb_po_item', 'tb_hutang.id_po_item=tb_po_item.id');
            $this->db->join('tb_purchase_order_items_payments', 'tb_purchase_order_items_payments.purchase_order_item_id=tb_po_item.id', 'left');
            $this->db->join('tb_po', 'tb_po.id=tb_po_item.purchase_order_id');
            $this->db->where_not_in('tb_po.review_status', ['REVISI']);
            $this->db->where_not_in('tb_po.status', ['PURPOSED']);
            $this->db->where('tb_po.vendor', $value['vendor']);
            $this->db->where('tb_po.default_currency', $value['currency']);
            
            $query = $this->db->get('tb_hutang');
            $prl_item['po_count'] = $query->num_rows();
            $a=0;$b=0;$c=0;$d=0;

            foreach ($query->result_array() as $key2 => $value) {
                if($value['default_currency']=='USD'){
                    $amount = $value['amount_usd'] - $value['paid'];
                }else{
                    $amount = $value['amount_idr'] - $value['paid'];
                }
                if($method=='PO'){
                    $datetime2 = new DateTime($value['due_date']);
                }else{
                    $datetime2 = new DateTime($value['tanggal']);
                }
                $datetime1 = new DateTime($date);
                $difference = $datetime1->diff($datetime2);
                $selisih = $difference->days;
                // if($selisih<31){
                $a=$amount;
                
                $prl_item['po_detail'][$key2]['ket'] = $selisih;
                $prl_item['po_detail'][$key2]['date'] = $datetime2;
                $prl_item['po_detail'][$key2]['a'] = $a;

            }
            $item[$key]['po'] = $prl_item;

        }

        return $item;
    }

    function getDetailPayableReconciliation($vendor, $currency, $date,$method)
    {
        $select = array(
            'tb_hutang.amount_idr',
            'tb_hutang.amount_usd',
            'tb_purchase_order_items_payments.paid',
            'tb_po.due_date',
            'tb_hutang.currency',
            'tb_hutang.tanggal'
        );
        // if($method=='PO'){
        //     $select['tb_po.due_date as payable_date'];
        // }else{
        //     $select['tb_hutang.tanggal as payable_date'];
        // }

        $this->db->select($select);
        $this->db->join('tb_po_item', 'tb_hutang.id_po_item=tb_po_item.id');
        $this->db->join('tb_purchase_order_items_payments', 'tb_purchase_order_items_payments.purchase_order_item_id=tb_po_item.id', 'left');
        $this->db->join('tb_po', 'tb_po.id=tb_po_item.purchase_order_id');
        $this->db->group_by($select);
        $this->db->where_not_in('tb_po.review_status', ['REVISI']);
        $this->db->where_not_in('tb_po.status', ['PURPOSED']);
        // $this->db->where('tb_purchase_order_items_payments.status', 'PAID');
        // if ($date != null) {
        //     $range_date  = explode('.', $date);
        //     $start_date  = $range_date[0];
        //     $end_date    = $range_date[1];

        //     $this->db->where('tb_po.document_date >=', $start_date);
        //     $this->db->where('tb_po.document_date <=', $end_date);
        // }
        if ($vendor != null && $vendor != 'all') {
            $this->db->where('tb_po.vendor', $vendor);
        }
        if ($currency != null && $currency != 'all') {
            $this->db->where('tb_po.default_currency', $currency);
        }
        $query = $this->db->get('tb_hutang');
        $prl_item['po_count'] = $query->num_rows();

        $a=0;$b=0;$c=0;$d=0;

        foreach ($query->result_array() as $key => $value) {
            if($value['currency']=='USD'){
                $amount = $value['amount_usd'] - $value['paid'];
            }else{
                $amount = $value['amount_idr'] - $value['paid'];
            }
            if($method=='PO'){
                $datetime2 = new DateTime($value['due_date']);
            }else{
                $datetime2 = new DateTime($value['tanggal']);
            }
            $datetime1 = new DateTime($date);
            $difference = $datetime1->diff($datetime2);
            $selisih = $difference->days;
            // if($selisih<31){
                $a=$amount;
            // }
            // if ($selisih >= 31 && $selisih<=60 ) {
            //     $b += $amount;
            //     // $prl_item['po_detail']['31_60_idr'] = $value['amount_idr'] - $value['amount_paid'];
            //     // $prl_item['po_detail']['31_60_usd'] = $value['amount_usd'] - $value['amount_paid'];
            // }
            // if ($selisih >= 61 && $selisih <= 90) {
            //     $c += $amount;
            //     // $prl_item['po_detail']['61_90_idr'] = $value['amount_idr'] - $value['amount_paid'];
            //     // $prl_item['po_detail']['61_90_usd'] = $value['amount_usd'] - $value['amount_paid'];
            // }
            // if ($selisih >90) {
            //     $d += $amount;
            //     // $prl_item['po_detail']['90_idr'] = $value['amount_idr'] - $value['amount_paid'];
            //     // $prl_item['po_detail']['90_usd'] = $value['amount_usd'] - $value['amount_paid'];
            // }
            // echo $difference->days;
            // $prl_item['po_detail'][$key] = $value;
            $prl_item['po_detail'][$key]['ket'] = $selisih;
            $prl_item['po_detail'][$key]['date'] = $datetime2;
            $prl_item['po_detail'][$key]['a'] = $a;
            // $prl_item['po_detail'][$key]['b'] = $b;
            // $prl_item['po_detail'][$key]['c'] = $c;
            // $prl_item['po_detail'][$key]['d'] = $d;

        }


        return $prl_item;
    }

    public function getPayableMutation($vendor, $date)
    {

        $this->db->select(
            array(
                'tb_hutang.vendor',
                // 'tb_hutang.amount_idr',
                // 'tb_hutang.amount_usd',
                // 'tb_hutang.tanggal',

            )
        );
        $this->db->join('tb_po_item', 'tb_po_item.id=tb_hutang.id_po_item');
        $this->db->join('tb_po', 'tb_po_item.purchase_order_id=tb_po.id');
        // $this->db->join('tb_receipt_items', 'tb_po_item.id=tb_receipt_items.purchase_order_item_id');
        $this->db->group_by(array(
            'tb_hutang.vendor',
            // 'tb_po_item.description'
        ));
        // $this->db->where('tb_po.review_status !=','REVISI');
        // $this->db->where_not_in('tb_po.review_status', ['REVISI']);
        // $this->db->where_not_in('tb_po.status', ['PURPOSED']);
        if ($date != null) {
            $range_date  = explode('.', $date);
            $start_date  = $range_date[0];
            $end_date    = $range_date[1];

            // $this->db->where('tb_receipt_items.received_date_item >=', $start_date);
            $this->db->where('tb_hutang.tanggal <=', $end_date);
        }
        if ($vendor != null && $vendor != 'all') {
            $this->db->where('tb_hutang.vendor', $vendor);
        }
        // if ($currency != null && $currency != 'all') {
        //     $this->db->where('tb_hutang.currency', $currency);
        // }
        $query      = $this->db->get('tb_hutang');
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
            $item[$key]['saldo_awal_usd'] = $this->getValue('saldo_awal',$value['vendor'], 'USD',$date);
            $item[$key]['saldo_awal_idr'] = $this->getValue('saldo_awal', $value['vendor'], 'IDR', $date);
            $item[$key]['pembelian_usd'] = $this->getValue('pembelian', $value['vendor'], 'USD', $date);
            $item[$key]['pembelian_idr'] = $this->getValue('pembelian', $value['vendor'], 'IDR', $date);
            $item[$key]['payment_saldo_awal_usd'] = $this->getPayment('saldo_awal', $value['vendor'], 'USD', $date);
            $item[$key]['payment_saldo_awal_idr'] = $this->getPayment('saldo_awal', $value['vendor'], 'IDR', $date);
            $item[$key]['payment_usd'] = $this->getPayment('pembelian', $value['vendor'], 'USD', $date);
            $item[$key]['payment_idr'] = $this->getPayment('pembelian', $value['vendor'], 'IDR', $date);
        }

        return $item;
    }

    function getValue($tipe,$vendor,$currency, $date)
    {
        $select = array(
            // 'tb_hutang.id_po_item',
            // 'tb_hutang.currency',
            'sum(tb_hutang.amount_idr) as idr',
            'sum(tb_hutang.amount_usd) as usd',
            // 'sum(tb_purchase_order_items_payments.amount_paid) as payment'
        );
        $this->db->select($select);
        $this->db->from('tb_hutang');
        $this->db->join('tb_po_item', 'tb_hutang.id_po_item=tb_po_item.id');
        // $this->db->join('tb_purchase_order_items_payments', 'tb_purchase_order_items_payments.purchase_order_item_id=tb_po_item.id', 'left');
        $this->db->join('tb_po', 'tb_po.id=tb_po_item.purchase_order_id');
        // $this->db->group_by(array(
        //     // 'tb_hutang.id_po_item',
        //     'tb_hutang.currency'
        //     // 'tb_po_item.description'
        // ));
        if ($date != null) {
            $range_date  = explode('.', $date);
            $start_date  = $range_date[0];
            $end_date    = $range_date[1];

            if($tipe=='saldo_awal'){
                $this->db->where('tb_hutang.tanggal <', $start_date);
            }
            if($tipe=='pembelian'){
                $this->db->where('tb_hutang.tanggal >=', $start_date);
                $this->db->where('tb_hutang.tanggal <=', $end_date);
            }            
            
        }
        if ($vendor != null && $vendor != 'all') {
            $this->db->where('tb_hutang.vendor', $vendor);
        }
        if ($currency != null && $currency != 'all') {
            $this->db->where('tb_hutang.currency', $currency);
        }
        $query = $this->db->get();


        $return = 0;
        foreach ($query->result_array() as $key => $value) {
            if($currency=='USD'){
                $return = $return + $value['usd'];
            }else{
                $return = $return+$value['idr'];
            }
            
        }
        return $return;
    }

    function getPayment($tipe, $vendor, $currency, $date)
    {
        $select = array(
            // 'tb_hutang.vendor',
            // 'tb_hutang.currency',
            // 'sum(tb_hutang.amount_idr) as idr',
            // 'sum(tb_hutang.amount_usd) as usd',
            'sum(tb_purchase_order_items_payments.amount_paid) as payment'
        );
        $this->db->select($select);
        $this->db->from('tb_purchase_order_items_payments');
        $this->db->join('tb_po_item', 'tb_purchase_order_items_payments.purchase_order_item_id=tb_po_item.id');
        // $this->db->join('tb_purchase_order_items_payments', 'tb_purchase_order_items_payments.purchase_order_item_id=tb_po_item.id', 'left');
        $this->db->join('tb_po', 'tb_po.id=tb_po_item.purchase_order_id');
        $this->db->where('tb_purchase_order_items_payments.status','PAID');
        // $this->db->group_by(array(
        //     // 'tb_hutang.vendor',
        //     'tb_hutang.currency'
        //     // 'tb_po_item.description'
        // ));
        if ($date != null) {
            $range_date  = explode('.', $date);
            $start_date  = $range_date[0];
            $end_date    = $range_date[1];

            if ($tipe == 'saldo_awal') {
                $this->db->where('tb_purchase_order_items_payments.tanggal <', $start_date);
            }
            if ($tipe == 'pembelian') {
                $this->db->where('tb_purchase_order_items_payments.tanggal >=', $start_date);
                $this->db->where('tb_purchase_order_items_payments.tanggal <=', $end_date);
            }
        }
        if ($vendor != null && $vendor != 'all') {
            $this->db->where('tb_po.vendor', $vendor);
        }
        if ($currency != null && $currency != 'all') {
            $this->db->where('tb_po.default_currency', $currency);
        }
        $query = $this->db->get();
        $return = 0;
        foreach ($query->result_array() as $key => $value) {
            if ($currency == 'USD') {
                $return = $return + $value['payment'];
            } else {
                $return = $return + $value['payment'];
            }
        }
        return $return;
    }


}

/* End of file Account_Payable_Model.php */
/* Location: ./application/models/Account_Payable_Model.php */
