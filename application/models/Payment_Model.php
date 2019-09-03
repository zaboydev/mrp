<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_Model extends MY_MODEL {
	public function __construct()
	{
		parent::__construct();
		
	}
	public function getAccount()
	{
		$this->db->select('group,coa');
		$this->db->from('tb_master_item_groups');
		$this->db->where('category', "BANK");
		return $this->db->get('')->result();
	}
	public function getSuplier()
	{
		$this->db->select('vendor');
		$this->db->from('tb_master_vendors');
		return $this->db->get('')->result();
	}
	function getPoByVendor($vendor){
		$this->db->select('tb_po.*');
		$this->db->from('tb_po');
		$this->db->where('vendor', $vendor);
		$this->db->where('remaining_payment >', 0);
		$this->db->where_in('status',['ORDER','OPEN']);
		// $this->db->where('document_number is not null', null,false);
		return $this->db->get()->result();
	}
	function save(){
		$item = $this->input->post('item');
		$account = $this->input->post('account');
		$vendor = $this->input->post('vendor');
		$no_cheque = $this->input->post('no_cheque');
		$tanggal = $this->input->post('date');
		$amount = $this->input->post('amount');
		$no_jurnal = $this->jrl_last_number();
		$this->db->set('no_jurnal', $no_jurnal);
        $this->db->set('tanggal_jurnal  ', date("Y-m-d"));
        $this->db->set('source', "AP");
        $this->db->insert('tb_jurnal');
        $id_jurnal = $this->db->insert_id();
        $this->db->set('id_jurnal',$id_jurnal);
        $this->db->set('jenis_transaksi',("SUPLIER PAYMENT IDR"));
        $this->db->set('trs_kredit',0);
        $this->db->set('trs_debet',$amount);
       	$this->db->set('kode_rekening',"2-1101");
        $this->db->insert('tb_jurnal_detail');
       	$jenis = $this->groupsBycoa($account);
        $this->db->set('id_jurnal',$id_jurnal);
        $this->db->set('jenis_transaksi',$jenis);
        $this->db->set('trs_debet',0);
        $this->db->set('trs_kredit',$amount);
        $this->db->set('kode_rekening',$account);
        $this->db->insert('tb_jurnal_detail');
        foreach ($item as $key) {
        	$this->db->set('purchase_order_item_id',$key["document_number"]);
	        $this->db->set('amount_paid',$key["value"]);
	        $this->db->set('created_by',config_item('auth_person_name'));
	        $this->db->set('no_cheque',$no_cheque);
	        $this->db->set('tanggal',$tanggal);
	        $this->db->set('no_transaksi',$no_jurnal);
	        $this->db->insert('tb_purchase_order_items_payments');

	        $this->db->set('remaining_payment','"remaining_payment" - '.$key["value"],false);
	        $this->db->where('id', $key["document_number"]);
	        $this->db->update('tb_po');

	     //    $this->db->where('id_po',$key["document_number"]);
	     //    $this->db->from('tb_hutang');
	     //    $query  = $this->db->get();
		    // $result_hutang = $query->result_array();

		    // foreach ($result_hutang as $hutang) {
		    // 	if($hutang[])
		    // }
        }
        if ($this->db->trans_status() === FALSE)
	      return FALSE;

	    $this->db->trans_commit();
	    return TRUE;
	}
	function groupsBycoa($account){
		$this->db->select('tb_master_item_groups.group');
		$this->db->from('tb_master_item_groups');
		$this->db->where('coa', $account);
		return $this->db->get()->row()->group;
	}
	function checkJurnalNumber(){
    return $this->db->get('tb_jurnal')->num_rows();
	  }
	  function jrl_last_number()
	  {
	    $div  = config_item('document_format_divider');
	    $year = date('Y');

	    $format = $div . 'JRL' . $year;
	    if($this->checkJurnalNumber() == 0 ){
	      $number = sprintf('%06s', 1);
	      $document_number = $number . $div . "JRL" . $div . $year;
	    } else {

	      $format = $div . "JRL" . $div . $year;
	      $this->db->select_max('no_jurnal','last_number');
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
}

/* End of file Jurnal_Model.php */
/* Location: ./application/models/Jurnal_Model.php */
