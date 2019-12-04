<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Payment_Model extends MY_MODEL
{
	public function __construct()
	{
		parent::__construct();
	}

	public function getSelectedColumns()
	{
		$return = array(
			'tb_purchase_order_items_payments.id'                          		=> NULL,
			'tb_purchase_order_items_payments.no_transaksi'             		=> 'Transaction Number',
			'tb_purchase_order_items_payments.tanggal'               			=> 'Date',
			'tb_purchase_order_items_payments.no_cheque'                    	=> 'No Cheque',
			'tb_po.document_number'                   							=> 'PO#',
			'tb_po.vendor'                   									=> 'Vendor',
			// 'tb_po_item.part_number'             								=> 'Part Number',
			'tb_purchase_order_items_payments.deskripsi as description'			=> 'Description',
			'tb_po.default_currency'             								=> 'Currency',
			'tb_purchase_order_items_payments.amount_paid'   					=> 'Amount',
			'tb_purchase_order_items_payments.status'	                     	=> 'Status',
			NULL											           			=> 'Attachment',
			'tb_purchase_order_items_payments.created_by'           			=> 'Created by',
			'tb_purchase_order_items_payments.created_at'                     	=> 'Created At',
		);

		return $return;
	}

	public function getSearchableColumns()
	{
		$return = array(
			// 'tb_purchase_order_items_payments.id',
			'tb_purchase_order_items_payments.no_transaksi',
			// 'tb_purchase_order_items_payments.tanggal',
			'tb_purchase_order_items_payments.no_cheque',
			'tb_po.document_number',
			// 'tb_po_item.part_number',
			'tb_purchase_order_items_payments.deskripsi',
			'tb_po.default_currency',
			// 'tb_purchase_order_items_payments.amount_paid',
			'tb_purchase_order_items_payments.created_by',
			'tb_po.vendor',
			'tb_purchase_order_items_payments.status'
			// 'tb_purchase_order_items_payments.created_at',
		);

		return $return;
	}

	public function getOrderableColumns()
	{
		$return = array(
			NULL,
			'tb_purchase_order_items_payments.no_transaksi',
			'tb_purchase_order_items_payments.tanggal',
			'tb_purchase_order_items_payments.no_cheque',
			'tb_po.document_number',
			// 'tb_po_item.part_number',
			'tb_purchase_order_items_payments.deskripsi',
			'tb_po.default_currency',
			'tb_purchase_order_items_payments.amount_paid',
			'tb_purchase_order_items_payments.created_by',
			'tb_purchase_order_items_payments.created_at',
			'tb_po.vendor'
		);

		return $return;
	}

	private function searchIndex()
	{
		if (!empty($_POST['columns'][1]['search']['value'])) {
			$search_received_date = $_POST['columns'][1]['search']['value'];
			$range_received_date  = explode(' ', $search_received_date);

			$this->db->where('tb_purchase_order_items_payments.tanggal >= ', $range_received_date[0]);
			$this->db->where('tb_purchase_order_items_payments.tanggal <= ', $range_received_date[1]);
		}

		if (!empty($_POST['columns'][2]['search']['value'])) {
			$vendor = $_POST['columns'][2]['search']['value'];

			$this->db->where('tb_po.vendor', $vendor);
		}

		if (!empty($_POST['columns'][3]['search']['value'])) {
			$currency = $_POST['columns'][3]['search']['value'];

			if ($currency != 'all') {
				$this->db->where('tb_po.default_currency', $currency);
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
		$this->db->from('tb_purchase_order_items_payments');
		// $this->db->join('tb_po_item', 'tb_purchase_order_items_payments.purchase_order_item_id = tb_po_item.id');
		$this->db->join('tb_po', 'tb_po.id = tb_purchase_order_items_payments.id_po');
		// $this->db->join('tb_attachment_payment', 'tb_purchase_order_items_payments.no_transaksi = tb_attachment_payment.no_transaksi', 'left');

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
		$this->db->select(array_keys($this->getSelectedColumns()));
		$this->db->from('tb_purchase_order_items_payments');
		// $this->db->join('tb_po_item', 'tb_purchase_order_items_payments.purchase_order_item_id = tb_po_item.id');
		$this->db->join('tb_po', 'tb_po.id = tb_purchase_order_items_payments.id_po');
		// $this->db->join('tb_attachment_payment', 'tb_purchase_order_items_payments.no_transaksi = tb_attachment_payment.no_transaksi', 'left');

		$this->searchIndex();

		$query = $this->db->get();

		return $query->num_rows();
	}

	public function countIndex()
	{
		$this->db->select(array_keys($this->getSelectedColumns()));
		$this->db->from('tb_purchase_order_items_payments');
		// $this->db->join('tb_po_item', 'tb_purchase_order_items_payments.purchase_order_item_id = tb_po_item.id');
		$this->db->join('tb_po', 'tb_po.id = tb_purchase_order_items_payments.id_po');
		// $this->db->join('tb_attachment_payment', 'tb_purchase_order_items_payments.no_transaksi = tb_attachment_payment.no_transaksi', 'left');

		$query = $this->db->get();

		return $query->num_rows();
	}

	public function getAccount($currency)
	{
		$this->db->select('group,coa');
		$this->db->from('tb_master_coa');
		$this->db->like('group', $currency);
		$this->db->where('category', "Bank");
		return $this->db->get('')->result();
	}

	public function getSuplier($currency)
	{
		$this->db->select('tb_master_vendors.vendor,tb_master_vendors.code');
		$this->db->join('tb_master_vendors_currency', 'tb_master_vendors_currency.vendor=tb_master_vendors.vendor');
		$this->db->where('tb_master_vendors_currency.currency', $currency);
		$this->db->from('tb_master_vendors');
		return $this->db->get('')->result();
	}

	function getPoByVendor($vendor, $currency, $tipe)
	{
		$this->db->select('tb_po.*');
		$this->db->from('tb_po');
		$this->db->where('vendor', $vendor);
		$this->db->where('default_currency', $currency);
		$this->db->where('remaining_payment >', 0);
		$this->db->where_in('status', ['OPEN', 'ORDER']);
		$this->db->order_by('id', 'asc');
		$po = $this->db->get()->result_array();
		foreach ($po as $detail) {
			$this->db->select('*');
			$this->db->from('tb_po_item');
			$this->db->join('tb_po', 'tb_po_item.purchase_order_id = tb_po.id');
			$this->db->where('tb_po_item.purchase_order_id', $detail['id']);
			$this->db->order_by('tb_po_item.id', 'asc');
			$query = $this->db->get();

			foreach ($query->result_array() as $key => $value) {
				$po['items'][$detail['id']][$key] = $value;
			}
		}
		return $po;
	}

	function countPoByVendor($vendor, $currency, $tipe)
	{
		$this->db->select('tb_po.*');
		$this->db->from('tb_po');
		// $this->db->join('tb_po','tb_po.id=tb_po_item.purchase_order_id');
		$this->db->where('tb_po.vendor', $vendor);
		$this->db->where('tb_po.default_currency', $currency);
		$this->db->where('tb_po.remaining_payment >', 0);
		$this->db->where_in('tb_po.status', ['OPEN', 'ORDER']);
		// $this->db->where('document_number is not null', null,false);
		return $this->db->get()->num_rows();
	}

	function countPoAdditionalByVendor($vendor, $currency, $tipe)
	{
		$this->db->select('tb_po.*');
		$this->db->from('tb_po');
		// $this->db->join('tb_po','tb_po.id=tb_po_item.purchase_order_id');
		$this->db->where('tb_po.vendor', $vendor);
		$this->db->where('tb_po.default_currency', $currency);
		$this->db->where('tb_po.remaining_payment >', 0);
		$this->db->where('tb_po.additional_price_remaining >', 0);
		$this->db->where_in('tb_po.status', ['OPEN', 'ORDER']);
		// $this->db->where('document_number is not null', null,false);
		return $this->db->get()->num_rows();
	}

	function countdetailPoByVendor($vendor, $currency, $tipe)
	{
		$this->db->select('tb_po_item.*');
		$this->db->from('tb_po_item');
		$this->db->join('tb_po', 'tb_po.id=tb_po_item.purchase_order_id');
		$this->db->where('tb_po.vendor', $vendor);
		$this->db->where('tb_po.default_currency', $currency);
		$this->db->where('tb_po.remaining_payment >', 0);
		$this->db->where_in('tb_po.status', ['OPEN', 'ORDER']);
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
		$tipe = $this->input->post('tipe');
		if ($currency == 'IDR') {
			$amount_idr = $amount;
			$amount_usd = $amount / $kurs;
		} else {
			$amount_usd = $amount;
			$amount_idr = $amount * $kurs;
		}


		// $this->db->set('no_jurnal', $no_jurnal);
		// $this->db->set('tanggal_jurnal  ', date("Y-m-d"));
		// $this->db->set('source', "AP");
		// $this->db->set('vendor', $vendor);
		// $this->db->set('grn_no', $no_jurnal);
		// $this->db->insert('tb_jurnal');
		// $id_jurnal = $this->db->insert_id();

		// $jenis = $this->groupsBycoa($account);
		// $this->db->set('id_jurnal', $id_jurnal);
		// $this->db->set('jenis_transaksi', $jenis);
		// $this->db->set('trs_debet', 0);
		// $this->db->set('trs_kredit', $amount_idr);
		// $this->db->set('trs_debet_usd', 0);
		// $this->db->set('trs_kredit_usd', $amount_usd);
		// $this->db->set('kode_rekening', $account);
		// $this->db->set('currency', $currency);
		// $this->db->insert('tb_jurnal_detail');
		foreach ($item as $key) {
			if ($key["value"] > 0) {
				$id_po = $key['id_po'];
				$status = $this->get_status_po($id_po);

				if($key["document_number"]!=0){
					$this->db->set('purchase_order_item_id', $key["document_number"]);
				}				
				$this->db->set('id_po', $id_po);
				$this->db->set('deskripsi', $key['desc']);
				$this->db->set('amount_paid', $key["value"]);
				$this->db->set('created_by', config_item('auth_person_name'));
				$this->db->set('no_cheque', $no_cheque);
				$this->db->set('tanggal', $tanggal);
				$this->db->set('no_transaksi', $no_jurnal);
				$this->db->set('coa_kredit', $account);
				// $this->db->set('akun_kredit', $jenis);
				if ($status == "ORDER") {
					$this->db->set('uang_muka', $key["value"]);
				}
				if ($currency == 'USD') {
					$this->db->set('kurs', $kurs);
				} else {
					$this->db->set('kurs', 1);
				}
				$this->db->insert('tb_purchase_order_items_payments');

				// $this->db->set('left_paid_amount', '"left_paid_amount" - ' . $key["value"], false);
				// // $this->db->set('payment', '"payment" + ' . $key["value"], false);
				// $this->db->where('id', $key["document_number"]);
				// $this->db->update('tb_po_item');				

				// $this->db->set('remaining_payment', '"remaining_payment" - ' . $key["value"], false);
				// $this->db->set('payment', '"payment" + ' . $key["value"], false);
				// $this->db->where('id', $id_po);
				// $this->db->update('tb_po');

				// if ($status == "ORDER") {
				// 	if($currency=='IDR'){						
				// 		$id_master_akun = 3;
				// 	}else{
				// 		$id_master_akun = 4;
				// 	}
				// 	$jenis_transaksi = 'Down Payment Inventories ' . $currency;
				// 	$this->db->set('uang_muka', '"uang_muka" + ' . $key["value"], false);
				// 	$this->db->where('id', $key["document_number"]);
				// 	$this->db->update('tb_po_item');
				// } else {
				// 	if ($currency == 'IDR') {
				// 		$id_master_akun = 1;
				// 	} else {
				// 		$id_master_akun = 2;
				// 	}
				// 	$jenis_transaksi = 'SUPLIER PAYABLE ' . $currency;
				// }
				// $akun = get_set_up_akun($id_master_akun);

				// $this->db->set('id_jurnal', $id_jurnal);
				// $this->db->set('jenis_transaksi', strtoupper($akun->group));
				// $this->db->set('trs_kredit', 0);
				// $this->db->set('trs_debet', $amount_idr);
				// $this->db->set('trs_kredit_usd', 0);
				// $this->db->set('trs_debet_usd', $amount_usd);
				// $this->db->set('kode_rekening', $akun->coa);
				// $this->db->set('currency', $currency);
				// $this->db->insert('tb_jurnal_detail');

				// //    $this->db->where('id_po',$key["document_number"]);
				// //    $this->db->from('tb_hutang');
				// //    $query  = $this->db->get();
				// // $result_hutang = $query->result_array();

				// // foreach ($result_hutang as $hutang) {
				// // 	if($hutang[])
				// // }
				// $left_qty_po = leftQtyPo($id_po);
				// $left_amount_po = leftAmountPo($id_po);
				// if ($left_amount_po == 0) {
				// 	$this->db->where('id', $id_po);
				// 	$this->db->set('status', 'ADVANCE');
				// 	$this->db->update('tb_po');
				// }
				// if ($left_qty_po == 0 && $left_amount_po == 0) {
				// 	$this->db->where('id', $id_po);
				// 	$this->db->set('status', 'CLOSED');
				// 	$this->db->update('tb_po');
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

		$format = $div . 'BPV' . $year;
		if ($this->checkJurnalNumber() == 0) {
			$number = sprintf('%06s', 1);
			$document_number = $number . $div . "BPV" . $div . $year;
		} else {

			$format = $div . "BPV" . $div . $year;
			$this->db->select_max('no_transaksi', 'last_number');
			$this->db->from('tb_purchase_order_items_payments');
			$this->db->like('tb_purchase_order_items_payments', $format, 'before');
			$query = $this->db->get('');
			$row    = $query->unbuffered_row();
			$last   = $row->last_number;
			$number = substr($last, 0, 6);
			$next   = $number + 1;
			$number = sprintf('%06s', $next);
			$document_number = $number . $div . "BPV" . $div . $year;
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

	function get_status_po($id)
	{
		$this->db->select('status');
		$this->db->where('id', $id);
		$this->db->from('tb_po');
		$query  = $this->db->get();
		$row    = $query->unbuffered_row();
		$return = $row->status;

		return $return;
	}

	public function findById($id)
	{
		$this->db->where('id', $id);

		$query    = $this->db->get('tb_purchase_order_items_payments');
		$payment_item = $query->unbuffered_row('array');
		$no_jurnal = $payment_item['no_transaksi'];

		$select = array(
			'tb_purchase_order_items_payments.no_transaksi',
			'tb_purchase_order_items_payments.tanggal',
			'tb_purchase_order_items_payments.no_cheque',
			'tb_purchase_order_items_payments.created_by',
			'tb_purchase_order_items_payments.created_at',
			'tb_purchase_order_items_payments.coa_kredit',
			'tb_purchase_order_items_payments.akun_kredit',
			'tb_po.vendor',
			'tb_purchase_order_items_payments.status',
			'tb_po.default_currency',
		);

		$this->db->select($select);
		$this->db->from('tb_purchase_order_items_payments');
		// $this->db->join('tb_po_item', 'tb_purchase_order_items_payments.purchase_order_item_id = tb_po_item.id');
		$this->db->join('tb_po', 'tb_po.id = tb_purchase_order_items_payments.id_po');
		$this->db->where('tb_purchase_order_items_payments.no_transaksi', $no_jurnal);
		$this->db->group_by($select);
		$query = $this->db->get();
		$payment = $query->unbuffered_row('array');

		$select = array(
			// 'tb_po_item.part_number',
			'tb_purchase_order_items_payments.deskripsi as description',
			'tb_purchase_order_items_payments.amount_paid',
			'tb_purchase_order_items_payments.purchase_order_item_id',
			'tb_purchase_order_items_payments.id_po',
			'tb_purchase_order_items_payments.uang_muka',
			'tb_purchase_order_items_payments.id',
			'tb_po.document_number',
			'tb_po.default_currency',
		);

		$this->db->select($select);
		$this->db->from('tb_purchase_order_items_payments');
		// $this->db->join('tb_po_item', 'tb_purchase_order_items_payments.purchase_order_item_id = tb_po_item.id');
		$this->db->join('tb_po', 'tb_po.id = tb_purchase_order_items_payments.id_po');
		$this->db->where('tb_purchase_order_items_payments.no_transaksi', $no_jurnal);
		$this->db->order_by('tb_purchase_order_items_payments.id_po','asc');

		$query_item = $this->db->get();

		foreach ($query_item->result_array() as $key => $value) {
			$payment['items'][$key] = $value;
		}

		return $payment;
	}

	public function approve($id)
	{
		$this->db->trans_begin();

		$this->db->set('status', 'APPROVED');
		$this->db->where('id', $id);
		$this->db->update('tb_purchase_order_items_payments');

		if ($this->db->trans_status() === FALSE)
			return FALSE;

		$this->db->trans_commit();
		return TRUE;
	}

	public function rejected($id)
	{
		$this->db->trans_begin();

		$this->db->set('status', 'REJECTED');
		$this->db->where('id', $id);
		$this->db->update('tb_purchase_order_items_payments');

		if ($this->db->trans_status() === FALSE)
			return FALSE;

		$this->db->trans_commit();
		return TRUE;
	}

	function save_pembayaran()
	{
		$this->db->trans_begin();
		// $item = $this->input->post('item');
		$account = $this->input->post('account');
		$vendor = $this->input->post('vendor');
		$no_cheque = $this->input->post('no_cheque');
		$tanggal = $this->input->post('date');
		$amount = $this->input->post('amount');
		$no_jurnal = $this->input->post('no_transaksi');
		$currency = $this->input->post('currency');
		$kurs = $this->tgl_kurs(date("Y-m-d"));
		$tipe = $this->input->post('tipe');
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
		foreach ($_SESSION['payment']['items'] as $i => $key) {
			$id_po = $key['id_po'];
			$status = $this->get_status_po($id_po);

			// $this->db->set('purchase_order_item_id', $key["document_number"]);
			// $this->db->set('amount_paid', $key["value"]);
			// $this->db->set('created_by', config_item('auth_person_name'));
			$this->db->set('no_cheque', $no_cheque);
			$this->db->set('tanggal', $tanggal);
			$this->db->set('status', "PAID");
			$this->db->set('coa_kredit', $account);
			$this->db->set('akun_kredit', $jenis);
			// if ($status == "ORDER") {
			// 	$this->db->set('uang_muka', $key["value"]);
			// }
			if ($currency == 'USD') {
				$this->db->set('kurs', $kurs);
			} else {
				$this->db->set('kurs', 1);
			}
			$this->db->where('id', $key["id"]);
			$this->db->update('tb_purchase_order_items_payments');

			if($key['purchase_order_item_id']!==null){
				$this->db->set('left_paid_amount', '"left_paid_amount" - ' . $key["amount_paid"], false);
				// $this->db->set('payment', '"payment" + ' . $key["value"], false);
				$this->db->where('id', $key["purchase_order_item_id"]);
				$this->db->update('tb_po_item');
			}else{
				$this->db->set('additional_price_remaining', '"additional_price_remaining" - ' . $key["amount_paid"], false);
				// $this->db->set('payment', '"payment" + ' . $key["amount_paid"], false);
				$this->db->where('id', $id_po);
				$this->db->update('tb_po');
			}

			$this->db->set('remaining_payment', '"remaining_payment" - ' . $key["amount_paid"], false);
			$this->db->set('payment', '"payment" + ' . $key["amount_paid"], false);
			$this->db->where('id', $id_po);
			$this->db->update('tb_po');

			if ($currency == 'IDR') {
				$amount_idr = $key["amount_paid"];
				$amount_usd = $key["amount_paid"] / $kurs;
			} else {
				$amount_usd = $key["amount_paid"];
				$amount_idr = $key["amount_paid"] * $kurs;
			}

			if ($key['uang_muka'] > 0) {
				if ($currency == 'IDR') {
					$id_master_akun = 3;
				} else {
					$id_master_akun = 4;
				}
				$jenis_transaksi = 'Down Payment Inventories ' . $currency;
				// $this->db->set('uang_muka', '"uang_muka" + ' . $key["value"], false);
				// $this->db->where('id', $key["document_number"]);
				// $this->db->update('tb_po_item');
			} else {
				if ($currency == 'IDR') {
					$id_master_akun = 1;
				} else {
					$id_master_akun = 2;
				}
				$jenis_transaksi = 'SUPLIER PAYABLE ' . $currency;
			}
			$akun = get_set_up_akun($id_master_akun);

			$this->db->set('id_jurnal', $id_jurnal);
			$this->db->set('jenis_transaksi', strtoupper($akun->group));
			$this->db->set('trs_kredit', 0);
			$this->db->set('trs_debet', $amount_idr);
			$this->db->set('trs_kredit_usd', 0);
			$this->db->set('trs_debet_usd', $amount_usd);
			$this->db->set('kode_rekening', $akun->coa);
			$this->db->set('currency', $currency);
			$this->db->insert('tb_jurnal_detail');

			//    $this->db->where('id_po',$key["document_number"]);
			//    $this->db->from('tb_hutang');
			//    $query  = $this->db->get();
			// $result_hutang = $query->result_array();

			// foreach ($result_hutang as $hutang) {
			// 	if($hutang[])
			// }
			$left_qty_po = leftQtyPo($id_po);
			$left_amount_po = leftAmountPo($id_po);
			if ($left_amount_po == 0) {
				$this->db->where('id', $id_po);
				$this->db->set('status', 'ADVANCE');
				$this->db->update('tb_po');
			}
			if ($left_qty_po == 0 && $left_amount_po == 0) {
				$this->db->where('id', $id_po);
				$this->db->set('status', 'CLOSED');
				$this->db->update('tb_po');
			}
		}
		if ($this->db->trans_status() === FALSE)
			return FALSE;

		$this->db->trans_commit();
		return TRUE;
	}

	public function listAttachment($id)
	{
		$this->db->where('id', $id);
		$query    = $this->db->get('tb_purchase_order_items_payments');
		$payment_item = $query->unbuffered_row('array');
		$no_transaksi = $payment_item['no_transaksi'];

		$this->db->where('no_transaksi', $no_transaksi);
		return $this->db->get('tb_attachment_payment')->result();
	}

	public function listAttachment_2($id)
	{
		$this->db->where('id', $id);
		$query    = $this->db->get('tb_purchase_order_items_payments');
		$payment_item = $query->unbuffered_row('array');
		$no_transaksi = $payment_item['no_transaksi'];

		$this->db->where('no_transaksi', $no_transaksi);
		return $this->db->get('tb_attachment_payment')->result_array();
	}

	function add_attachment_to_db($id, $url)
	{
		$this->db->trans_begin();

		$this->db->where('id', $id);
		$query    = $this->db->get('tb_purchase_order_items_payments');
		$payment_item = $query->unbuffered_row('array');
		$no_transaksi = $payment_item['no_transaksi'];

		$this->db->set('no_transaksi', $no_transaksi);
		$this->db->set('file', $url);
		$this->db->insert('tb_attachment_payment');

		if ($this->db->trans_status() === FALSE)
			return FALSE;

		$this->db->trans_commit();
		return TRUE;
	}

	public function checkAttachment($id)
	{
		$this->db->where('id', $id);
		$query    = $this->db->get('tb_purchase_order_items_payments');
		$payment_item = $query->unbuffered_row('array');
		$no_transaksi = $payment_item['no_transaksi'];

		$this->db->where('no_transaksi', $no_transaksi);
		$this->db->from('tb_attachment_payment');
		$num_rows = $this->db->count_all_results();

		return $num_rows;
	}
	// }
}

/* End of file Jurnal_Model.php */
/* Location: ./application/models/Jurnal_Model.php */
