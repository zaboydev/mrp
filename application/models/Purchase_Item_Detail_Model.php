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
        $range_date  = explode(' ', $date);
        $start_date  = $range_date[0];
        $end_date    = $range_date[1];

        $query      = $this->db->get('tb_receipts');
        $item       = $query->result_array();

        $select = array(
            
        );
    }


}

/* End of file Account_Payable_Model.php */
/* Location: ./application/models/Account_Payable_Model.php */
