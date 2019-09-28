<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Payment_Model extends MY_MODEL
{
	public function __construct()
	{
		parent::__construct();
	}
	public function getAccount($currency)
	{
		$this->db->select('group,coa');
		$this->db->from('tb_master_item_groups');
		$this->db->like('group', $currency);
		$this->db->where('category', "BANK");
		return $this->db->get('')->result();
	}
	public function getSuplier($currency)
	{
		$this->db->select('vendor,code');
		$this->db->where('currency', $currency);
		$this->db->from('tb_master_vendors');
		return $this->db->get('')->result();
	}
	function getPoByVendor($vendor, $currency)
	{
		$this->db->select('tb_po.*');
		$this->db->from('tb_po');
		$this->db->where('vendor', $vendor);
		$this->db->where('default_currency', $currency);
		$this->db->where('remaining_payment >', 0);
		$this->db->where_in('status', ['ORDER', 'OPEN']);
		$this->db->order_by('id','asc');
		$po = $this->db->get()->result_array();
		foreach ($po as $detail) {
			$this->db->select('*');
			$this->db->from('tb_po_item');
			// $this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.id = tb_po_item.poe_item_id', 'left');
			$this->db->where('tb_po_item.purchase_order_id', $detail['id']);
			$this->db->order_by('id', 'asc');
			$query = $this->db->get();

			foreach ($query->result_array() as $key => $value) {
				$po['items'][$detail['id']][$key] = $value;
			}
		}
		return $po;
	}

	function countPoByVendor($vendor, $currency)
	{
		$this->db->select('tb_po.*');
		$this->db->from('tb_po');
		// $this->db->join('tb_po','tb_po.id=tb_po_item.purchase_order_id');
		$this->db->where('tb_po.vendor', $vendor);
		$this->db->where('tb_po.default_currency', $currency);
		$this->db->where('tb_po.remaining_payment >', 0);
		$this->db->where_in('tb_po.status', ['OPEN','ORDER']);
		// $this->db->where('document_number is not null', null,false);
		return $this->db->get()->num_rows();
	}

	function countdetailPoByVendor($vendor, $currency)
	{
		$this->db->select('tb_po_item.*');
		$this->db->from('tb_po_item');
		$this->db->join('tb_po', 'tb_po.id=tb_po_item.purchase_order_id');
		$this->db->where('tb_po.vendor', $vendor);
		$this->db->where('tb_po.default_currency', $currency);
		$this->db->where('tb_po.remaining_payment >', 0);
		$this->db->where_in('tb_po.status', ['ORDER', 'OPEN']);
		// $this->db->where('document_number is not null', null,false);
		return $this->db->get()->num_rows();
	}
	function save()
	{
		$this->db->trans_begin();
		$item = $this->input->post('item');
		$account = $this->input->post('account');
		$vendor = $this->input->post('vendor');
		$no_cheque = $this->input->post('no_cheque');
		$tanggal = $this->input->post('date');
		$amount = $this->input->post('amount');
		$no_jurnal = $this->jrl_last_number();
		$currency = $this->input->post('currency');
		$kurs = $this->tgl_kurs(date("Y-m-d"));
		if ($currency == 'IDR') {
			$amount_idr = $amount;
			$amount_usd = $amount / $kurs;
		} else {
			$amount_usd = $amount;
			$amount_idr = $amount * $kurs;
		}

		$this->db->set('no_jurnal', $no_jurnal);
		$this->db->set('tanggal_jurnal  ', date("Y-m-d"));
		$this->db->set('source', "AP");
		$this->db->set('vendor', $vendor);
		$this->db->set('grn_no', $no_jurnal);
		$this->db->insert('tb_jurnal');
		$id_jurnal = $this->db->insert_id();

		$this->db->set('id_jurnal', $id_jurnal);
		$this->db->set('jenis_transaksi', ("SUPLIER PAYABLE " . $currency));
		$this->db->set('trs_kredit', 0);
		$this->db->set('trs_debet', $amount_idr);
		$this->db->set('trs_kredit_usd', 0);
		$this->db->set('trs_debet_usd', $amount_usd);
		$this->db->set('kode_rekening', "2-1101");
		$this->db->set('currency', $currency);
		$this->db->insert('tb_jurnal_detail');

		$jenis = $this->groupsBycoa($account);
		$this->db->set('id_jurnal', $id_jurnal);
		$this->db->set('jenis_transaksi', $jenis);
		$this->db->set('trs_debet', 0);
		$this->db->set('trs_kredit', $amount_idr);
		$this->db->set('trs_debet_usd', 0);
		$this->db->set('trs_kredit_usd', $amount_usd);
		$this->db->set('kode_rekening', $account);
		$this->db->set('currency', $currency);
		$this->db->insert('tb_jurnal_detail');
		foreach ($item as $key) {
			if ($key["value"] > 0) {
				$this->db->set('purchase_order_item_id', $key["document_number"]);
				$this->db->set('amount_paid', $key["value"]);
				$this->db->set('created_by', config_item('auth_person_name'));
				$this->db->set('no_cheque', $no_cheque);
				$this->db->set('tanggal', $tanggal);
				$this->db->set('no_transaksi', $no_jurnal);
				$this->db->insert('tb_purchase_order_items_payments');

				$this->db->set('left_paid_amount', '"left_paid_amount" - ' . $key["value"], false);
				// $this->db->set('payment', '"payment" + ' . $key["value"], false);
				$this->db->where('id', $key["document_number"]);
				$this->db->update('tb_po_item');

				$id_po = $this->get_id_po($key["document_number"]);


				$this->db->set('remaining_payment', '"remaining_payment" - ' . $key["value"], false);
				$this->db->set('payment', '"payment" + ' . $key["value"], false);
				$this->db->where('id', $id_po);
				$this->db->update('tb_po');

				//    $this->db->where('id_po',$key["document_number"]);
				//    $this->db->from('tb_hutang');
				//    $query  = $this->db->get();
				// $result_hutang = $query->result_array();

				// foreach ($result_hutang as $hutang) {
				// 	if($hutang[])
				// }
			}
		}
		if ($this->db->trans_status() === FALSE)
			return FALSE;

		$this->db->trans_commit();
		return TRUE;
	}
	function groupsBycoa($account)
	{
		$this->db->select('tb_master_item_groups.group');
		$this->db->from('tb_master_item_groups');
		$this->db->where('coa', $account);
		return $this->db->get()->row()->group;
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

	// if ( ! function_exists('tgl_kurs')) {
	function tgl_kurs($date)
	{

		// $CI =& get_instance();
		$kurs_dollar = 0;
		$tanggal = $date;

		while ($kurs_dollar == 0) {
			// $CI->db->select('kurs_dollar');
			// $CI->db->from( 'tb_master_kurs_dollar' );
			// $CI->db->where('date', $date);

			// $query  = $CI->db->get();
			// $row    = $query->unbuffered_row();
			// $kurs_dollar   = $row->kurs_dollar;


			$this->db->select('kurs_dollar');
			$this->db->from('tb_master_kurs_dollar');
			$this->db->where('date', $tanggal);

			$query = $this->db->get();

			if ($query->num_rows() > 0) {
				$row    = $query->unbuffered_row();
				$kurs_dollar   = $row->kurs_dollar;
			} else {
				$kurs_dollar = 0;
			}
			$tgl = strtotime('-1 day', strtotime($tanggal));
			$tanggal = date('Y-m-d', $tgl);
		}

		return $kurs_dollar;
	}

	function get_id_po($item_id)
	{
		$this->db->select('purchase_order_id');
		$this->db->where('id', $item_id);
		$this->db->from('tb_po_item');
		$query  = $this->db->get();
		$row    = $query->unbuffered_row();
		$return = $row->purchase_order_id;

		return $return;
	}
	// }
}

/* End of file Jurnal_Model.php */
/* Location: ./application/models/Jurnal_Model.php */
