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
        $this->db->select('tb_master_vendors.vendor,tb_master_vendors.code');
        // $this->db->join('tb_master_vendors_currency', 'tb_master_vendors_currency.vendor=tb_master_vendors.vendor');
        // $this->db->where('tb_master_vendors_currency.currency', $currency);
        $this->db->from('tb_master_vendors');
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
        $this->db->where('tb_po.review_status !=','REVISI');
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
        $this->db->where('tb_po.review_status !=', 'REVISI');
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


}

/* End of file Account_Payable_Model.php */
/* Location: ./application/models/Account_Payable_Model.php */
