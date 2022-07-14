<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Payment_Model extends MY_MODEL
{
	public function __construct()
	{
		parent::__construct();

		$this->connection   = $this->load->database('budgetcontrol', TRUE);
		$this->modules        = config_item('module');
        $this->data['modules']        = $this->modules;
	}

	public function getSelectedColumns()
	{
		$return = array(
			'tb_po_payments.id'                          						=> NULL,
			'tb_po_payments.document_number as no_transaksi'             		=> 'Transaction Number',
			'tb_po_payments.tanggal'               								=> 'Date',
			'tb_po_payments.no_cheque'                    						=> 'No Cheque',
			// 'tb_po.document_number'                   							=> 'PO#',
			'tb_po_payments.vendor'                   							=> 'Vendor',
			// 'tb_po_item.part_number'             								=> 'Part Number',
			// 'tb_purchase_order_items_payments.deskripsi as description'			=> 'Description',
			'tb_po_payments.currency'             								=> 'Currency',
			'tb_po_payments.coa_kredit'             								=> 'Account',
            'SUM(tb_purchase_order_items_payments.amount_paid) as amount_paid'  => 'Amount IDR',
			'tb_po_payments.akun_kredit'   										=> 'Amount USD',
			'tb_po_payments.status'	                     						=> 'Status',
			'tb_po_payments.notes'					           					=> 'Attachment',
			'tb_po_payments.base'                     							=> 'Base',
			'tb_po_payments.created_by'           								=> 'Created by',
			'tb_po_payments.created_at'                     					=> 'Created At',
			'tb_po_payments.checked_by'                     					=> 'Action',
		);

		return $return;
	}

	public function getSearchableColumns()
	{
		$return = array(
			// 'tb_purchase_order_items_payments.id',
			'tb_po_payments.document_number',
			// 'tb_purchase_order_items_payments.tanggal',
			'tb_po_payments.no_cheque',
			// 'tb_po_payments.document_number',
			// 'tb_po_item.part_number',
			// 'tb_purchase_order_items_payments.deskripsi',
			'tb_po_payments.currency',
			'tb_po_payments.coa_kredit',
			'tb_po_payments.akun_kredit',
			// 'tb_purchase_order_items_payments.amount_paid',
			'tb_po_payments.created_by',
			'tb_po_payments.vendor',
			'tb_po_payments.status',
			'tb_po_payments.base'
			// 'tb_purchase_order_items_payments.created_at',
		);

		return $return;
	}

	public function getOrderableColumns()
	{
		$return = array(
			NULL,
			'tb_po_payments.document_number',
			'tb_po_payments.tanggal',
			'tb_po_payments.no_cheque',
			// 'tb_po.document_number',
			'tb_po_payments.vendor',
			// 'tb_po_item.part_number',
			// 'tb_purchase_order_items_payments.deskripsi',
			'tb_po_payments.currency',			
			'tb_po_payments.coa_kredit',
			// 'tb_purchase_order_items_payments.amount_paid',
			'tb_po_payments.base',
			'tb_po_payments.created_by',
			'tb_po_payments.created_at'
		);

		return $return;
	}

	public function getGroupedColumns()
	{
		$return = array(
			'tb_po_payments.id',
			'tb_po_payments.document_number',
			'tb_po_payments.tanggal',
			'tb_po_payments.no_cheque',
			'tb_po_payments.vendor',
			'tb_po_payments.currency',
			'tb_po_payments.status',
			'tb_po_payments.base',
			'tb_po_payments.created_by',
			'tb_po_payments.created_at',
			'tb_po_payments.akun_kredit',
			'tb_po_payments.notes',
			'tb_po_payments.checked_by'
		);

		return $return;
	}

	private function searchIndex()
	{
		if (!empty($_POST['columns'][1]['search']['value'])) {
			$search_received_date = $_POST['columns'][1]['search']['value'];
			$range_received_date  = explode(' ', $search_received_date);

			$this->db->where('tb_po_payments.tanggal >= ', $range_received_date[0]);
			$this->db->where('tb_po_payments.tanggal <= ', $range_received_date[1]);
		}

		if (!empty($_POST['columns'][2]['search']['value'])) {
			$vendor = $_POST['columns'][2]['search']['value'];

			$this->db->where('tb_po_payments.vendor', $vendor);
		}

		if (!empty($_POST['columns'][3]['search']['value'])) {
			$currency = $_POST['columns'][3]['search']['value'];

			if ($currency != 'all') {
				$this->db->where('tb_po_payments.currency', $currency);
			}
		}

		if (!empty($_POST['columns'][4]['search']['value'])) {
			$status = $_POST['columns'][4]['search']['value'];
			if($status!='all'){
				$this->db->like('tb_po_payments.status', $status);
			}			
		} else {
			if(is_granted($this->data['modules']['payment'], 'approval')){
				if (config_item('auth_role') == 'FINANCE SUPERVISOR') {
					$status[] = 'WAITING CHECK BY FIN SPV';
				}
				if (config_item('auth_role') == 'FINANCE MANAGER') {
					$status[] = 'WAITING REVIEW BY FIN MNG';
				}
				if (config_item('auth_role') == 'HEAD OF SCHOOL') {
					$status[] = 'WAITING REVIEW BY HOS';
				}
				if (config_item('auth_role') == 'CHIEF OPERATION OFFICER') {
					$status[] = 'WAITING REVIEW BY CEO';
				}
				if (config_item('auth_role') == 'VP FINANCE') {
					$status[] = 'WAITING REVIEW BY VP FINANCE';
				}
				if (config_item('auth_role') == 'CHIEF OF FINANCE') {
					$status[] = 'WAITING REVIEW BY CFO';
				}
				$this->db->where_in('tb_po_payments.status', $status);
			}elseif(is_granted($this->data['modules']['payment'], 'review')){
				$status[] = 'APPROVED';
				$this->db->where_in('tb_po_payments.status', $status);
			}else{
				if (config_item('auth_role') == 'TELLER') {
					$status[] = 'APPROVED';
					$this->db->where_in('tb_po_payments.status', $status);
				}
			}		
			
		}

		if (!empty($_POST['columns'][5]['search']['value'])) {
			$base = $_POST['columns'][5]['search']['value'];
			if($base!='ALL'){
				if($base!='JAKARTA'){
					$this->db->where('tb_po_payments.base !=','JAKARTA');
				}elseif($base=='JAKARTA'){
					$this->db->where('tb_po_payments.base','JAKARTA');
				}	
			}
					
		} else {
			if(config_item('auth_role') == 'AP STAFF' || config_item('auth_role') == 'FINANCE MANAGER'){
				$base = config_item('auth_warehouse');
				if($base!='JAKARTA'){
					$this->db->where('tb_po_payments.base !=','JAKARTA');
				}elseif($base=='JAKARTA'){
					$this->db->where('tb_po_payments.base','JAKARTA');
				}	
			}elseif(config_item('auth_role')=='PIC STAFF'){
			    $this->db->where_in('tb_po_payments.base', config_item('auth_warehouses'));
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
		$this->db->from('tb_po_payments');
		$this->db->join('tb_purchase_order_items_payments', 'tb_po_payments.id = tb_purchase_order_items_payments.po_payment_id');
		// if(is_granted($this->data['modules']['payment'], 'document') === TRUE){
        //     $this->db->where_in('tb_po_payments.base', config_item('auth_warehouses'));
        // }        
		// $this->db->join('tb_po', 'tb_po.id = tb_purchase_order_items_payments.id_po');
		// $this->db->join('tb_attachment_payment', 'tb_purchase_order_items_payments.no_transaksi = tb_attachment_payment.no_transaksi', 'left');
		$this->db->group_by($this->getGroupedColumns());

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
		$this->db->from('tb_po_payments');
		$this->db->join('tb_purchase_order_items_payments', 'tb_po_payments.id = tb_purchase_order_items_payments.po_payment_id');
		// if(is_granted($this->data['modules']['payment'], 'document') === TRUE){
        //     $this->db->where_in('tb_po_payments.base', config_item('auth_warehouses'));
		// }
		// $this->db->join('tb_po', 'tb_po.id = tb_purchase_order_items_payments.id_po');
		// $this->db->join('tb_attachment_payment', 'tb_purchase_order_items_payments.no_transaksi = tb_attachment_payment.no_transaksi', 'left');
		$this->db->group_by($this->getGroupedColumns());

		$this->searchIndex();

		$query = $this->db->get();

		return $query->num_rows();
	}

	public function countIndex()
	{
		$this->db->select(array_keys($this->getSelectedColumns()));
		$this->db->from('tb_po_payments');
		$this->db->join('tb_purchase_order_items_payments', 'tb_po_payments.id = tb_purchase_order_items_payments.po_payment_id');
		// if(is_granted($this->data['modules']['payment'], 'document') === TRUE){
        //     $this->db->where_in('tb_po_payments.base', config_item('auth_warehouses'));
		// }
		// $this->db->join('tb_po', 'tb_po.id = tb_purchase_order_items_payments.id_po');
		// $this->db->join('tb_attachment_payment', 'tb_purchase_order_items_payments.no_transaksi = tb_attachment_payment.no_transaksi', 'left');
		$this->db->group_by($this->getGroupedColumns());

		$query = $this->db->get();

		return $query->num_rows();
	}

	public function getAccount($currency)
	{
		$this->db->select('group,coa');
		$this->db->from('tb_master_coa');
		// $this->db->like('group', $currency);
		$this->db->where('category', "Bank");
		return $this->db->get('')->result();
	}

	public function getSuplier($currency)
	{
		$this->db->select('tb_master_vendors.vendor,tb_master_vendors.code');
		$this->db->join('tb_master_vendors_currency', 'tb_master_vendors_currency.vendor=tb_master_vendors.vendor');
		$this->db->where('tb_master_vendors_currency.currency', $currency);
		$this->db->from('tb_master_vendors');
		$this->db->order_by('tb_master_vendors.vendor');
		return $this->db->get('')->result();
	}

	function getPoByVendor($vendor, $currency, $tipe)
	{
		$this->db->select('tb_po.*');
		$this->db->from('tb_po');
		$this->db->where('vendor', $vendor);
		$this->db->where('default_currency', $currency);
		$this->db->where('remaining_payment_request >', 0);
		$this->db->where_in('status', ['OPEN', 'ORDER']);
		$this->db->order_by('tb_po.due_date', 'asc');
		$po = $this->db->get();
		$list_po = array();
		// foreach ($po as $detail) {
		foreach ($po->result_array() as $key => $detail) {
			$list_po[$key]= $detail;
			$this->db->select('*');
			$this->db->from('tb_po_item');
			// $this->db->join('tb_po', 'tb_po_item.purchase_order_id = tb_po.id');
			$this->db->where('tb_po_item.purchase_order_id', $detail['id']);
			$this->db->where('tb_po_item.left_paid_request >', 0);
			$this->db->order_by('tb_po_item.id', 'asc');
			$query = $this->db->get();

			foreach ($query->result_array() as $i => $value) {
				$list_po[$key]['items'][$i] = $value;
			}
		}
		return $list_po;
	}

	function countPoByVendor($vendor, $currency, $tipe)
	{
		$this->db->select('tb_po.*');
		$this->db->from('tb_po');
		// $this->db->join('tb_po','tb_po.id=tb_po_item.purchase_order_id');
		$this->db->where('tb_po.vendor', $vendor);
		$this->db->where('tb_po.default_currency', $currency);
		$this->db->where('tb_po.remaining_payment_request >', 0);
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
		$this->db->where('tb_po.additional_price_remaining_request !=', 0);
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
		$this->db->where('tb_po.remaining_payment_request >', 0);
		$this->db->where_in('tb_po.status', ['OPEN', 'ORDER']);
		// $this->db->where('document_number is not null', null,false);
		return $this->db->get()->num_rows();
	}

	function save_2()
	{
		$this->db->trans_begin();
		// $item 					= $this->input->post('item');
		$po_items_id 			= $this->input->post('po_item_id');
		$pos_id 				= $this->input->post('po_id');
		$desc_items 			= $this->input->post('desc');
		$value_items		 	= $this->input->post('value');
		$adj_value_items	 	= $this->input->post('adj_value');
		$qty_paid	 			= $this->input->post('qty_paid');

		$document_id          	= (isset($_SESSION['payment_request']['id'])) ? $_SESSION['payment_request']['id'] : NULL;
		$document_edit        	= (isset($_SESSION['payment_request']['edit'])) ? $_SESSION['payment_request']['edit'] : NULL;
		$document_number      	= $_SESSION['payment_request']['document_number'] . payment_request_format_number('BANK');
		$date      				= $_SESSION['payment_request']['date'];
		$purposed_date      	= $_SESSION['payment_request']['purposed_date'];
		$currency      			= $_SESSION['payment_request']['currency'];
		$vendor      			= $_SESSION['payment_request']['vendor'];
		$coa_kredit      			= $_SESSION['payment_request']['coa_kredit'];
		$notes      			= (empty($_SESSION['payment_request']['notes'])) ? NULL : $_SESSION['payment_request']['notes'];
		$kurs 					= $this->tgl_kurs(date("Y-m-d"));		
		$total_amount   		= floatval($_SESSION['payment_request']['total_amount']);
		$base 					= config_item('auth_warehouse');

		if ($currency == 'IDR') {
			$amount_idr = $total_amount;
			$amount_usd = $total_amount / $kurs;
		} else {
			$amount_usd = $total_amount;
			$amount_idr = $total_amount * $kurs;
		}
		
		if ($document_id === NULL) {
			$this->db->set('document_number', $document_number);
			$this->db->set('vendor', $vendor);
			$this->db->set('tanggal', $date);
			$this->db->set('purposed_date', $purposed_date);
			$this->db->set('currency', $currency);
			$this->db->set('created_by', config_item('auth_person_name'));
			$this->db->set('created_at', date('Y-m-d'));
			$this->db->set('base', $base);
			$this->db->set('notes', $notes);
			$this->db->set('coa_kredit', $coa_kredit);
			if($base=='JAKARTA'){
				$this->db->set('status','WAITING REVIEW BY FIN MNG');
			}
			$this->db->insert('tb_po_payments');
			$po_payment_id = $this->db->insert_id();
		}else{
			//utk edit
			$po_payment_id = $document_id;
		}
		$id_payment = array();
		// foreach ($item as $key) {
		foreach ($po_items_id as $key=>$po_item) {
			if ($value_items[$key] != 0) {
				$id_po = $pos_id[$key];
				$status = $this->get_status_po($id_po);

				if($po_item!=0){
					$this->db->set('purchase_order_item_id', $po_item);
				}				
				$this->db->set('id_po', $id_po);
				$this->db->set('po_payment_id', $po_payment_id);
				$this->db->set('deskripsi', $desc_items[$key]);
				$this->db->set('amount_paid', $value_items[$key]);
				$this->db->set('created_by', config_item('auth_person_name'));
				$this->db->set('no_cheque', null);
				$this->db->set('tanggal', $date);
				$this->db->set('no_transaksi', $document_number);
				$this->db->set('coa_kredit', null);
				$this->db->set('adj_value', $adj_value_items[$key]);
				$this->db->set('quantity_paid', $qty_paid[$key]);
				// $this->db->set('akun_kredit', $jenis);
				if ($status == "ORDER") {
					$this->db->set('uang_muka', $value_items[$key]);
				}
				if ($currency == 'USD') {
					$this->db->set('kurs', $kurs);
				} else {
					$this->db->set('kurs', 1);
				}
				// $this->db->set('base', config_item('auth_warehouse'));
				$this->db->insert('tb_purchase_order_items_payments');
				$id = $this->db->insert_id();
				$id_payment[] = $id;
				$val_request = $value_items[$key]-$adj_value_items[$key];

				if($po_item!=0){
					$this->db->set('left_paid_request', '"left_paid_request" - ' . $val_request, false);
					$this->db->set('quantity_paid', '"quantity_paid" + ' . $qty_paid[$key], false);
					$this->db->where('id', $po_item);
					$this->db->update('tb_po_item');
				}else{
					$this->db->set('additional_price_remaining_request', '"additional_price_remaining_request" - ' . $val_request, false);
					$this->db->where('id', $id_po);
					$this->db->update('tb_po');					
				}

				$this->db->set('remaining_payment_request', '"remaining_payment_request" - ' . $val_request, false);
				$this->db->where('id', $id_po);
				$this->db->update('tb_po');
			}
		}
		if ($this->db->trans_status() === FALSE)
			return FALSE;

		$this->db->trans_commit();
		if($base=='JAKARTA'){
			$this->send_mail($po_payment_id,14,$base);
		}else{
			$this->send_mail($po_payment_id,26);
		}
		
		return TRUE;
	}

	function update()
	{
		$this->db->trans_begin();
		// $item 					= $this->input->post('item');
		$po_items_id 			= $this->input->post('po_item_id');
		$pos_id 				= $this->input->post('po_id');
		$desc_items 			= $this->input->post('desc');
		$value_items		 	= $this->input->post('value');
		$adj_value_items	 	= $this->input->post('adj_value');
		$qty_paid			 	= $this->input->post('qty_paid');

		$document_id          	= (isset($_SESSION['payment_request']['id'])) ? $_SESSION['payment_request']['id'] : NULL;
		$document_edit        	= (isset($_SESSION['payment_request']['edit'])) ? $_SESSION['payment_request']['edit'] : NULL;
		$document_number      	= $_SESSION['payment_request']['document_number'] . payment_request_format_number($_SESSION['payment_request']['type']).'-R';
		$date      				= $_SESSION['payment_request']['date'];
		$purposed_date      	= $_SESSION['payment_request']['purposed_date'];
		$currency      			= $_SESSION['payment_request']['currency'];
		$vendor      			= $_SESSION['payment_request']['vendor'];
		$coa_kredit      		= $_SESSION['payment_request']['coa_kredit'];
		$type      				= $_SESSION['payment_request']['type'];
		$notes      			= (empty($_SESSION['payment_request']['notes'])) ? NULL : $_SESSION['payment_request']['notes'];
		$kurs 					= $this->tgl_kurs(date("Y-m-d"));		
		$total_amount   		= floatval($_SESSION['payment_request']['total_amount']);
		$base 					= config_item('auth_warehouse');
		$akun_kredit 			= getAccountByCode($coa_kredit);

		if ($currency == 'IDR') {
			$amount_idr = $total_amount;
			$amount_usd = $total_amount / $kurs;
		} else {
			$amount_usd = $total_amount;
			$amount_idr = $total_amount * $kurs;
		}

		//edit dokument sebelumnya
		// if ($document_id === NULL) {

			$this->db->select('tb_po_payments.*');
			$this->db->from('tb_po_payments');
			$this->db->where('tb_po_payments.id', $document_id);
			$query = $this->db->get();
			$po_payment = $query->unbuffered_row('array');
			
			$this->db->set('status', 'REVISI');
			$this->db->set('revisi', 'f');
			$this->db->where('id',$document_id);
			$this->db->update('tb_po_payments');

			if($po_payment['status']!='REJECTED' && $po_payment['status']!='CANCELED'){
				$this->db->select('tb_purchase_order_items_payments.*');
				$this->db->from('tb_purchase_order_items_payments');
				$this->db->where('tb_purchase_order_items_payments.po_payment_id', $document_id);

				$item_payment = $this->db->get();

				foreach ($item_payment->result_array() as $key => $item) {
					$id_po = $item['id_po'];
					$value = $item["amount_paid"]+$item["adj_value"];
					if($item['purchase_order_item_id']!==null){
						if($po_payment['type']=='CASH'){
							$this->db->set('left_paid_amount', '"left_paid_amount" + ' . $value, false);
						}
						$this->db->set('left_paid_request', '"left_paid_request" + ' . $value, false);					
						$this->db->set('quantity_paid', '"quantity_paid" - ' . $qty_paid[$key], false);
						$this->db->where('id', $item["purchase_order_item_id"]);
						$this->db->update('tb_po_item');
					}else{
						if($po_payment['type']=='CASH'){
							$this->db->set('additional_price_remaining', '"additional_price_remaining" + ' . $value, false);							
						}
						$this->db->set('additional_price_remaining_request', '"additional_price_remaining_request" + ' . $value, false);
						$this->db->where('id', $id_po);
						$this->db->update('tb_po');
					}
					if($item['id_po']!=0){
						if($po_payment['type']=='CASH'){
							$this->db->set('remaining_payment', '"remaining_payment" + ' . $value, false);
							$this->db->set('payment', '"payment" - ' . $value, false);							
						}
						$this->db->set('remaining_payment_request', '"remaining_payment_request" + ' . $value, false);
						$this->db->where('id', $id_po);
						$this->db->update('tb_po');
					}
				}
			}

			//hapus tb jurnal & tb jurnal detail jika type==CASH
			if($po_payment['type']=='CASH'){
				$this->db->select('tb_jurnal.*');
				$this->db->from('tb_jurnal');
				$this->db->where('tb_jurnal.no_jurnal', $po_payment['document_number']);
				$query = $this->db->get();
				$jurnal = $query->unbuffered_row('array');

				$this->db->where('id_jurnal',$jurnal['id']);
				$this->db->delete('tb_jurnal_detail');

				$this->db->where('id',$jurnal['id']);
				$this->db->delete('tb_jurnal');
			}
			

		// }
		
		$this->db->set('document_number', $document_number);
		$this->db->set('vendor', $vendor);
		$this->db->set('tanggal', $date);
		$this->db->set('purposed_date', $purposed_date);
		$this->db->set('currency', $currency);
		$this->db->set('created_by', config_item('auth_person_name'));
		$this->db->set('created_at', date('Y-m-d'));
		$this->db->set('base', $base);
		$this->db->set('notes', $notes);
		$this->db->set('revisi', 'f');
		$this->db->set('coa_kredit', $coa_kredit);
		$this->db->set('akun_kredit', $akun_kredit->group);
		if($type=='CASH'){
			$this->db->set('status','PAID');
			$this->db->set('paid_by', config_item('auth_person_name'));
			$this->db->set('paid_at', date("Y-m-d",strtotime($date)));
		}else{
			// if($base=='JAKARTA'){
			$this->db->set('status','WAITING REVIEW BY FIN MNG');
			// }
		}
		$this->db->set('type',$type);
		$this->db->insert('tb_po_payments');
		$po_payment_id = $this->db->insert_id();
		$id_payment = array();

		if($type=='CASH'){
			$this->db->set('no_jurnal', $document_number);
			$this->db->set('tanggal_jurnal  ', date("Y-m-d",strtotime($date)));
			$this->db->set('source', "AP");
			$this->db->set('vendor', $vendor);
			$this->db->set('grn_no', $document_number);
			$this->db->set('keterangan', strtoupper("pembayaran purchase order"));
			$this->db->set('created_by',config_item('auth_person_name'));
        	$this->db->set('created_at',date('Y-m-d'));
			$this->db->insert('tb_jurnal');
			$id_jurnal = $this->db->insert_id();
		}

		// foreach ($item as $key) {
		$total_idr = array();
		$total_usd = array();
		foreach ($po_items_id as $key=>$po_item) {
			if ($value_items[$key] != 0) {
				$id_po = $pos_id[$key];
				$status = $this->get_status_po($id_po);

				if($po_item!=0){
					$this->db->set('purchase_order_item_id', $po_item);
				}				
				$this->db->set('id_po', $id_po);
				$this->db->set('po_payment_id', $po_payment_id);
				$this->db->set('deskripsi', $desc_items[$key]);
				$this->db->set('amount_paid', $value_items[$key]);
				$this->db->set('created_by', config_item('auth_person_name'));
				$this->db->set('no_cheque', null);
				$this->db->set('tanggal', $date);
				$this->db->set('no_transaksi', $document_number);
				$this->db->set('coa_kredit', null);				
				$this->db->set('quantity_paid', $qty_paid[$key]);
				if($adj_value_items[$key]!=null){
					$this->db->set('adj_value', $adj_value_items[$key]);
					$adj_value = $adj_value_items[$key];
				}else{
					$adj_value = 0;
				}
				
				// $this->db->set('akun_kredit', $jenis);
				if ($status == "ORDER") {
					$this->db->set('uang_muka', $value_items[$key]);
				}
				if ($currency == 'USD') {
					$this->db->set('kurs', $kurs);
				} else {
					$this->db->set('kurs', 1);
				}
				if($type=='CASH'){
					$this->db->set('status','PAID');
				}
				// $this->db->set('base', config_item('auth_warehouse'));
				$this->db->insert('tb_purchase_order_items_payments');
				$id = $this->db->insert_id();
				$id_payment[] = $id;
				$val_request = $value_items[$key]-$adj_value;

				if($po_item!=0){
					if($type=='CASH'){						
						$this->db->set('left_paid_amount', '"left_paid_amount" - ' . $val_request, false);
					}
					$this->db->set('left_paid_request', '"left_paid_request" - ' . $val_request, false);					
					$this->db->set('quantity_paid', '"quantity_paid" + ' . $qty_paid[$key], false);
					$this->db->where('id', $po_item);
					$this->db->update('tb_po_item');
				}else{
					if($type=='CASH'){
						$this->db->set('additional_price_remaining', '"additional_price_remaining" - ' . $val_request, false);
					}
					$this->db->set('additional_price_remaining_request', '"additional_price_remaining_request" - ' . $val_request, false);
					$this->db->where('id', $id_po);
					$this->db->update('tb_po');					
				}

				if($type=='CASH'){
					$this->db->set('remaining_payment', '"remaining_payment" - ' . $val_request, false);
					$this->db->set('payment', '"payment" + ' . $val_request, false);
				}
				$this->db->set('remaining_payment_request', '"remaining_payment_request" - ' . $val_request, false);
				$this->db->where('id', $id_po);
				$this->db->update('tb_po');

				if($type=='CASH'){
					if ($currency == 'IDR') {
						$amount_idr = $val_request;
						$amount_usd = $val_request / $kurs;

						$total_idr[] = $amount_idr;
						$total_usd[] = $amount_usd;
					} else {
						$amount_usd = $val_request;
						$amount_idr = $val_request * $kurs;

						$total_idr[] = $amount_idr;
						$total_usd[] = $amount_usd;
					}

					if ($status == "ORDER") {
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
			}
		}

		if($type=='CASH'){

			$jenis = $this->groupsBycoa($coa_kredit);
			$this->db->set('id_jurnal', $id_jurnal);
			$this->db->set('jenis_transaksi', $jenis);
			$this->db->set('trs_debet', 0);
			$this->db->set('trs_kredit', array_sum($total_idr));
			$this->db->set('trs_debet_usd', 0);
			$this->db->set('trs_kredit_usd', array_sum($total_usd));
			$this->db->set('kode_rekening', $coa_kredit);
			$this->db->set('currency', $currency);
			$this->db->insert('tb_jurnal_detail');
		}
		if ($this->db->trans_status() === FALSE)
			return FALSE;

		$this->db->trans_commit();
		// if($base=='JAKARTA'){
		// 	$this->send_mail($po_payment_id,14,$base);
		// }else{
		// 	$this->send_mail($po_payment_id,26);
		// }

		if($type!='CASH'){
			$this->send_mail($po_payment_id,14,$base);
		}
		
		return TRUE;
	}

	function save(){
		$this->db->trans_begin();

		$document_id          	= (isset($_SESSION['payment_request']['id'])) ? $_SESSION['payment_request']['id'] : NULL;
		$document_edit        	= (isset($_SESSION['payment_request']['edit'])) ? $_SESSION['payment_request']['edit'] : NULL;
		$document_number      	= $_SESSION['payment_request']['document_number'] . payment_request_format_number($_SESSION['payment_request']['type']);
		$date      				= $_SESSION['payment_request']['date'];
		$purposed_date      	= $_SESSION['payment_request']['purposed_date'];
		$currency      			= $_SESSION['payment_request']['currency'];
		$vendor      			= $_SESSION['payment_request']['vendor'];
		$coa_kredit      		= $_SESSION['payment_request']['coa_kredit'];
		$type      				= $_SESSION['payment_request']['type'];
		$notes      			= (empty($_SESSION['payment_request']['notes'])) ? NULL : $_SESSION['payment_request']['notes'];
		$kurs 					= $this->tgl_kurs(date("Y-m-d"));		
		$total_amount   		= floatval($_SESSION['payment_request']['total_amount']);
		$base 					= config_item('auth_warehouse');
		$akun_kredit 			= getAccountByCode($coa_kredit);

		if ($currency == 'IDR') {
			$amount_idr = $total_amount;
			$amount_usd = $total_amount / $kurs;
		} else {
			$amount_usd = $total_amount;
			$amount_idr = $total_amount * $kurs;
		}

		if ($document_id === NULL) {
			$this->db->set('document_number', $document_number);
			$this->db->set('vendor', $vendor);
			$this->db->set('tanggal', $date);
			$this->db->set('purposed_date', $purposed_date);
			$this->db->set('currency', $currency);
			$this->db->set('created_by', config_item('auth_person_name'));
			$this->db->set('created_at', date('Y-m-d'));
			$this->db->set('base', $base);
			$this->db->set('notes', $notes);
			$this->db->set('coa_kredit', $coa_kredit);
			$this->db->set('akun_kredit', $akun_kredit->group);			
			if($type=='CASH'){
				$this->db->set('status','APPROVED');
				$this->db->set('cash_request','OPEN');
				$this->db->set('paid_by', config_item('auth_person_name'));
				$this->db->set('paid_at', date("Y-m-d",strtotime($date)));
			}else{
				// if($base=='JAKARTA'){
				$this->db->set('status','WAITING REVIEW BY FIN MNG');
				// }
			}
			$this->db->set('type',$type);
			$this->db->insert('tb_po_payments');
			$po_payment_id = $this->db->insert_id();

			$this->db->set('document_number', $document_number);
            $this->db->set('source', 'EXPENSE');            
            $this->db->insert('tb_po_payment_no_transaksi');

			if($type=='CASH2'){
				$this->db->set('no_jurnal', $document_number);
				$this->db->set('tanggal_jurnal  ', date("Y-m-d",strtotime($date)));
				$this->db->set('source', "AP");
				$this->db->set('vendor', $vendor);
				$this->db->set('grn_no', $document_number);
				$this->db->set('keterangan', strtoupper("pembayaran purchase order"));
				$this->db->set('created_by',config_item('auth_person_name'));
        		$this->db->set('created_at',date('Y-m-d'));
				$this->db->insert('tb_jurnal');
				$id_jurnal = $this->db->insert_id();

				$jenis = $this->groupsBycoa($coa_kredit);
				$this->db->set('id_jurnal', $id_jurnal);
				$this->db->set('jenis_transaksi', $jenis);
				$this->db->set('trs_debet', 0);
				$this->db->set('trs_kredit', $amount_idr);
				$this->db->set('trs_debet_usd', 0);
				$this->db->set('trs_kredit_usd', $amount_usd);
				$this->db->set('kode_rekening', $coa_kredit);
				$this->db->set('currency', $currency);
				$this->db->insert('tb_jurnal_detail');
			}
		}else{
			//utk edit
			$po_payment_id = $document_id;
		}

		//items
		foreach ($_SESSION['payment_request']['items'] as $key => $item) {
			if ($item["amount_paid"] != 0) {
				$id_po = $item['po_id'];
				$status = $this->get_status_po($id_po);

				if($item["purchase_order_item_id"]!=0){
					$this->db->set('purchase_order_item_id', $item["purchase_order_item_id"]);
				}				
				$this->db->set('id_po', $id_po);
				$this->db->set('po_payment_id', $po_payment_id);
				$this->db->set('deskripsi', $item['deskripsi']);
				$this->db->set('amount_paid', $item["amount_paid"]);
				$this->db->set('created_by', config_item('auth_person_name'));
				$this->db->set('no_cheque', null);
				$this->db->set('tanggal', $date);
				$this->db->set('no_transaksi', $document_number);
				$this->db->set('coa_kredit', null);
				$this->db->set('adj_value', $item["adj_value"]);
				$this->db->set('quantity_paid', $item['qty_paid']);
				if ($status == "ORDER") {
					$this->db->set('uang_muka', $item["amount_paid"]);
				}
				if ($currency == 'USD') {
					$this->db->set('kurs', $kurs);
				} else {
					$this->db->set('kurs', 1);
				}
				if($type=='CASH'){
					$this->db->set('status','APPROVED');
				}
				$this->db->insert('tb_purchase_order_items_payments');
				$id = $this->db->insert_id();
				// $id_payment[] = $id;
				$val_request = $item["amount_paid"]-$item["adj_value"];

				if($item['purchase_order_item_id']!=0){
					if($type=='CASH2'){						
						$this->db->set('left_paid_amount', '"left_paid_amount" - ' . $val_request, false);
					}
					$this->db->set('left_paid_request', '"left_paid_request" - ' . $val_request, false);
					$this->db->set('quantity_paid', '"quantity_paid" + ' . $item['qty_paid'], false);
					$this->db->where('id', $item["purchase_order_item_id"]);
					$this->db->update('tb_po_item');
				}else{
					if($type=='CASH2'){
						$this->db->set('additional_price_remaining', '"additional_price_remaining" - ' . $val_request, false);
					}
					$this->db->set('additional_price_remaining_request', '"additional_price_remaining_request" - ' . $val_request, false);
					$this->db->where('id', $id_po);
					$this->db->update('tb_po');
				}

				if($type=='CASH2'){
					$this->db->set('remaining_payment', '"remaining_payment" - ' . $val_request, false);
					$this->db->set('payment', '"payment" + ' . $val_request, false);
				}
				$this->db->set('remaining_payment_request', '"remaining_payment_request" - ' . $val_request, false);
				$this->db->where('id', $id_po);
				$this->db->update('tb_po');

				if($type=='CASH2'){
					if ($currency == 'IDR') {
						$amount_idr = $val_request;
						$amount_usd = $val_request / $kurs;
					} else {
						$amount_usd = $val_request;
						$amount_idr = $val_request * $kurs;
					}

					if ($status == "ORDER") {
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

							
			}
		}

		if ($this->db->trans_status() === FALSE)
			return FALSE;

		$this->db->trans_commit();
		if($type!='CASH'){
			$this->send_mail($po_payment_id,14,$base);
		}
		// if($base=='JAKARTA'){
		// 	$this->send_mail($po_payment_id,14,$base);
		// }else{
		// 	$this->send_mail($po_payment_id,26);
		// }
		return TRUE;
	}

	public function isDocumentNumberExists($document_number)
	{
		$this->db->where('document_number', $document_number);
		$query = $this->db->get('tb_po_payments');

		if ($query->num_rows() > 0)
			return true;

		return false;
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
			$this->db->like('tb_purchase_order_items_payments.no_transaksi', $format, 'before');
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

	public function findById_2($id)
	{
		// $this->db->where('id', $id);

		$this->db->select('tb_po_payments.*,tb_master_coa.group');
		$this->db->join('tb_master_coa','tb_master_coa.coa = tb_po_payments.coa_kredit','left');
		$this->db->from('tb_po_payments');
		$this->db->where('tb_po_payments.id', $id);
		$query = $this->db->get();
		$payment = $query->unbuffered_row('array');
		$payment['no_transaksi'] = $payment['document_number'];

		$select = array(
			// 'tb_po_item.part_number',
			'tb_purchase_order_items_payments.deskripsi as description',
			'tb_purchase_order_items_payments.amount_paid',
			'tb_purchase_order_items_payments.quantity_paid',
			'tb_purchase_order_items_payments.purchase_order_item_id',
			'tb_purchase_order_items_payments.id_po',
			'tb_purchase_order_items_payments.uang_muka',
			'tb_purchase_order_items_payments.id',
			'tb_purchase_order_items_payments.quantity_paid',
			'tb_po.document_number',
			'tb_po.default_currency',
			'tb_po.tipe_po',
			'tb_po.due_date',
			'tb_po.tipe'
			
		);

		$this->db->select($select);
		$this->db->from('tb_purchase_order_items_payments');
		$this->db->join('tb_po', 'tb_po.id = tb_purchase_order_items_payments.id_po','left');
		$this->db->where('tb_purchase_order_items_payments.po_payment_id', $id);
		$this->db->order_by('tb_purchase_order_items_payments.id_po','asc');

		$query_item = $this->db->get();
		$total = 0;

		foreach ($query_item->result_array() as $key => $value) {
			$payment['items'][$key] = $value;
			$total = $total+$value['amount_paid'];
			if($value['purchase_order_item_id']!=null){
				$item 										= $this->getItemPoById($value['purchase_order_item_id']);
				$payment['items'][$key]['poe_number'] 		= $item['poe_number'];
				$poe 										= $this->getPoe($item['poe_number']);
				$payment['items'][$key]['poe_id'] 			= $poe['id'];
				$payment['items'][$key]['poe_type'] 		= $poe['tipe'];
				$payment['items'][$key]['item'] 			= $item;
				$payment['items'][$key]['request_number'] 	= $item['purchase_request_number'];
				$payment['items'][$key]['request_id'] 		= $this->getRequestId($item['purchase_request_number'],$value['tipe_po']);
				$payment['items'][$key]['history']          = $this->getHistory($value['id'],$value['id_po'],$value['purchase_order_item_id']);	
			}else{				
				$payment['items'][$key]['poe_number'] 		= null;
				$payment['items'][$key]['request_number'] 	= null;
				$payment['items'][$key]['history']          = $this->getHistory($value['id'],$value['id_po']);
				$payment['items'][$key]['grn']				= [];
				if($value['id_po']!=0){
					$payment['items'][$key]['item']				= $this->additionalPriceInfo($value['id_po']);
				}
			}			
			
		}
		$payment['total_amount'] = $total;

		return $payment;
	}

	public function findById($id)
	{
		// $this->db->where('id', $id);

		$this->db->select('tb_po_payments.*,tb_master_coa.group');
		$this->db->join('tb_master_coa','tb_master_coa.coa = tb_po_payments.coa_kredit','left');
		$this->db->from('tb_po_payments');
		$this->db->where('tb_po_payments.id', $id);
		$query = $this->db->get();
		$payment = $query->unbuffered_row('array');
		$payment['no_transaksi'] = $payment['document_number'];

		$select = array(
			'sum(tb_purchase_order_items_payments.amount_paid) as amount_paid',
			'sum(tb_purchase_order_items_payments.quantity_paid) as quantity_paid',
			'tb_purchase_order_items_payments.id_po',
			'tb_purchase_order_items_payments.po_payment_id',
			'tb_po.document_number',
			'tb_po.due_date',
			'tb_po.tipe_po',
			
		);

		$this->db->select($select);
		$this->db->from('tb_purchase_order_items_payments');
		$this->db->join('tb_po', 'tb_po.id = tb_purchase_order_items_payments.id_po','left');
		$this->db->where('tb_purchase_order_items_payments.po_payment_id', $id);
		$this->db->order_by('tb_purchase_order_items_payments.id_po','asc');
		$this->db->group_by(array(
            'tb_purchase_order_items_payments.id_po',
			'tb_purchase_order_items_payments.po_payment_id',
			'tb_po.document_number',
			'tb_po.due_date',
			'tb_po.tipe_po',
        ));

		$query_item = $this->db->get();
		$total = 0;

		foreach ($query_item->result_array() as $key => $value) {
			$payment['po'][$key] = $value;
			$payment['po'][$key]['grn_list'] = $this->getReceiptListByPurchaseId($value['id_po']);
			$total = $total+$value['amount_paid'];

			$select = array(
				// 'tb_po_item.part_number',
				'tb_purchase_order_items_payments.deskripsi as description',
				'tb_purchase_order_items_payments.amount_paid',
				'tb_purchase_order_items_payments.quantity_paid',
				'tb_purchase_order_items_payments.purchase_order_item_id',
				'tb_purchase_order_items_payments.id_po',
				'tb_purchase_order_items_payments.uang_muka',
				'tb_purchase_order_items_payments.id',
				'tb_purchase_order_items_payments.quantity_paid',
				'tb_po.document_number',
				'tb_po.default_currency',
				'tb_po.tipe_po',
				'tb_po.due_date',
				'tb_po.tipe'
				
			);

			$this->db->select($select);
			$this->db->from('tb_purchase_order_items_payments');
			$this->db->join('tb_po', 'tb_po.id = tb_purchase_order_items_payments.id_po','left');
			$this->db->where('tb_purchase_order_items_payments.po_payment_id', $value['po_payment_id']);
			$this->db->where('tb_purchase_order_items_payments.id_po', $value['id_po']);
			$this->db->order_by('tb_purchase_order_items_payments.purchase_order_item_id','asc');

			$query_item = $this->db->get();
			foreach ($query_item->result_array() as $i => $value) {
				$payment['po'][$key]['items'][$i] = $value;
				// $total = $total+$value['amount_paid'];
				if($value['purchase_order_item_id']!=null){
					$item 													= $this->getItemPoById($value['purchase_order_item_id']);
					$payment['po'][$key]['items'][$i]['poe_number'] 		= $item['poe_number'];
					$poe 													= $this->getPoe($item['poe_number']);
					$payment['po'][$key]['items'][$i]['poe_id'] 			= $poe['id'];
					$payment['po'][$key]['items'][$i]['poe_type'] 			= $poe['tipe'];
					$payment['po'][$key]['items'][$i]['item'] 				= $item;
					$payment['po'][$key]['items'][$i]['request_number'] 	= $item['purchase_request_number'];
					$payment['po'][$key]['items'][$i]['request_id'] 		= $this->getRequestId($item['purchase_request_number'],$value['tipe_po']);
					$payment['po'][$key]['items'][$i]['history']          	= $this->getHistory($value['id'],$value['id_po'],$value['purchase_order_item_id']);	
				}else{				
					$payment['po'][$key]['items'][$i]['poe_number'] 		= null;
					$payment['po'][$key]['items'][$i]['request_number'] 	= null;
					$payment['po'][$key]['items'][$i]['history']          	= $this->getHistory($value['id'],$value['id_po']);
					$payment['po'][$key]['items'][$i]['grn']				= [];
					if($value['id_po']!=0){
						$payment['po'][$key]['items'][$i]['item']			= $this->additionalPriceInfo($value['id_po']);
					}
				}			
				
			}
					
			
		}
		$payment['total_amount'] = $total;

		if($payment['status']=='PAID'){
            $this->db->select('tb_jurnal.*');
            $this->db->where('tb_jurnal.no_jurnal', $payment['document_number']);
            $this->db->from('tb_jurnal');
            $queryJurnal    = $this->db->get();
            $jurnal         = $queryJurnal->unbuffered_row('array');

            $this->db->select('tb_jurnal_detail.*');
            $this->db->from('tb_jurnal_detail');
            $this->db->where('tb_jurnal_detail.id_jurnal', $jurnal['id']);

            $queryDetailJurnal = $this->db->get();

            foreach ($queryDetailJurnal->result_array() as $key => $detail){
                $payment['jurnalDetail'][$key] = $detail;
            }
        }
		$this->db->where('id_poe', $id);
		$this->db->where('tipe', 'PAYMENT');
    	$this->db->where(array('deleted_at' => NULL));
		$payment['attachment'] = $this->db->get('tb_attachment_poe')->result_array();

		return $payment;
	}

	public function additionalPriceInfo($id)
	{
		$select = array(
	      'tb_po.additional_price',
	      'tb_po.additional_price_remaining_request'	      
	    );

	    $this->db->select($select);
		$this->db->from('tb_po');
		$this->db->where('tb_po.id', $id);
		$query = $this->db->get();
		$po = $query->unbuffered_row('array');

		return $po;
	}

	public function getReceiptListByPurchaseId($purchase_order_id)
	{
	    $select = array(
	      'tb_receipts.id',
	      'tb_receipts.document_number'
	      
	    );

	    $this->db->select($select);
	    $this->db->from('tb_receipts');
	    $this->db->join('tb_receipt_items', 'tb_receipts.document_number = tb_receipt_items.document_number');
	    $this->db->join('tb_po_item', 'tb_po_item.id = tb_receipt_items.purchase_order_item_id');   
	    $this->db->join('tb_po', 'tb_po.id = tb_po_item.purchase_order_id'); 
	    $this->db->where('tb_po.id', $purchase_order_id);
		$this->db->group_by($select);

	    $query = $this->db->get();

	    return $query->result_array();
	}

	public function getReceiptItems($purchase_order_item_id)
	{
	    $select = array(
	      'tb_receipts.id',
	      'tb_receipts.document_number',
	      'tb_receipts.received_date',
	      'tb_receipts.received_by',
	      'tb_receipt_items.received_quantity',
	      'tb_receipt_items.received_unit_value',
		  'tb_receipt_items.received_total_value',
		  'tb_receipt_items.quantity_order'
	      
	    );

	    $this->db->select($select);
	    $this->db->from('tb_receipt_items');
	    $this->db->join('tb_receipts', 'tb_receipts.document_number = tb_receipt_items.document_number');    
	    $this->db->where('tb_receipt_items.purchase_order_item_id', $purchase_order_item_id);

	    $query = $this->db->get();

	    return $query->result_array();
	}

	public function getItemPoById($id)
	{
		$this->db->select(array(
			'tb_po_item.poe_number',
			'tb_po_item.purchase_request_number',
			'tb_po_item.part_number',
			'tb_po_item.quantity',
			'tb_po_item.total_amount',
			'tb_po_item.unit_price',
			'tb_po_item.left_received_quantity',
			'tb_po_item.quantity_received',
			'tb_po_item.core_charge',
			'tb_po_item.left_paid_request',
			'sum(case when tb_receipt_items.quantity_order is null then 0.00 else tb_receipt_items.quantity_order end) as "grn_qty"'
		)
		);
		$this->db->from('tb_po_item');
		$this->db->join('tb_receipt_items', 'tb_receipt_items.purchase_order_item_id = tb_po_item.id','left');
		$this->db->where('tb_po_item.id', $id);
		$this->db->group_by(array(
			'tb_po_item.poe_number',
			'tb_po_item.purchase_request_number',
			'tb_po_item.part_number',
			'tb_po_item.quantity',
			'tb_po_item.total_amount',
			'tb_po_item.unit_price',
			'tb_po_item.left_received_quantity',
			'tb_po_item.quantity_received',
			'tb_po_item.core_charge',
			'tb_po_item.left_paid_request',
		)
		);
		$query = $this->db->get();
		$item = $query->unbuffered_row('array');

		$item['grn'] = $this->getReceiptItems($id);

		return $item;
	}

	public function getRequestId($pr_number,$tipe)
  	{
	    $return = 0;
	    if($tipe=='INVENTORY MRP'){
	      $this->db->select('id');
	      $this->db->where('UPPER(tb_inventory_purchase_requisitions.pr_number)', $pr_number);
	      $this->db->from('tb_inventory_purchase_requisitions');
	      $query    = $this->db->get();
	      $request = $query->unbuffered_row();
	      $return = $request->id;
	    }

	    if($tipe=='INVENTORY'){
	      $this->connection->select('id');
	      $this->connection->where('UPPER(tb_inventory_purchase_requisitions.pr_number)', $pr_number);
	      $this->connection->from('tb_inventory_purchase_requisitions');
	      $query    = $this->connection->get();
	      $request = $query->unbuffered_row();
	      $return = $request->id;
	    }

	    if($tipe=='EXPENSE'){
	      $this->connection->select('id');
	      $this->connection->where('UPPER(tb_expense_purchase_requisitions.pr_number)', $pr_number);
	      $this->connection->from('tb_expense_purchase_requisitions');
	      $query    = $this->connection->get();
	      $request = $query->unbuffered_row();
	      $return = $request->id;
	    }

	    if($tipe=='CAPEX'){
	      $this->connection->select('id');
	      $this->connection->where('UPPER(tb_capex_purchase_requisitions.pr_number)', $pr_number);
	      $this->connection->from('tb_capex_purchase_requisitions');
	      $query    = $this->connection->get();
	      $request = $query->unbuffered_row();
	      $return = $request->id;
	    }
	    return $return;
  	}

  	public function getPoe($poe_number)
	{
	    // $return = 0;
	    $this->db->select('id,tipe');
	    $this->db->where('evaluation_number', $poe_number);
	    $this->db->from('tb_purchase_orders');
	    $query    = $this->db->get();
	    $return = $query->unbuffered_row('array');
	    
	    return $return;
	}

	public function getHistory($id,$id_po,$purchase_order_item_id=null)
    {
        $select = array(
          'tb_po_payments.document_number',
          'tb_po_payments.status',
          'tb_po_payments.tanggal',
          'tb_po_payments.currency',
          'tb_purchase_order_items_payments.amount_paid'
        );
        $this->db->select($select);
        $this->db->from('tb_purchase_order_items_payments');
        $this->db->join('tb_po_payments', 'tb_po_payments.id = tb_purchase_order_items_payments.po_payment_id');
        if($purchase_order_item_id!=null){
        	$this->db->where('tb_purchase_order_items_payments.purchase_order_item_id', $purchase_order_item_id);
        }else{
        	$this->db->where('tb_purchase_order_items_payments.id_po', $id_po);
        	$this->db->where('tb_purchase_order_items_payments.purchase_order_item_id', null);
        }        
        $this->db->where('tb_purchase_order_items_payments.id <',$id);
        $query  = $this->db->get();

        return $query->result_array();
    }

	public function approve($id)
	{
		$this->db->trans_begin();

		$this->db->select('tb_po_payments.*');
        $this->db->from('tb_po_payments');
        $this->db->where('tb_po_payments.id',$id);
        $query    		= $this->db->get();
		$po_payment  	= $query->unbuffered_row('array');
		$total 			= $this->countTotalPayment($id);
		$currency 		= $po_payment['currency'];
		$level 			= 0;
		$status 		= '';

		if (config_item('auth_role')=='FINANCE MANAGER' && $po_payment['status'] == 'WAITING REVIEW BY FIN MNG') {
			if($po_payment['base']=='JAKARTA'){
				$this->db->set('status', 'APPROVED');
				$status = 'APPROVED';
				$level = 0;
			}else{
				$this->db->set('status', 'APPROVED');
				$status = 'APPROVED';
				$level = 0;
			}			
			$this->db->set('review_by', config_item('auth_person_name'));
			$this->db->set('review_at', date('Y-m-d'));
			$this->db->where('id', $id);
			$this->db->update('tb_po_payments');
		}

		// if (config_item('auth_role')=='VP FINANCE' && $po_payment['status'] == 'WAITING REVIEW BY VP FINANCE') {
		// 	$this->db->set('status', 'APPROVED');
		// 	$status = 'APPROVED';
		// 	$level = 0;
		// 	$this->db->set('known_by', config_item('auth_person_name'));
		// 	$this->db->set('known_at', date('Y-m-d'));
		// 	$this->db->where('id', $id);
		// 	$this->db->update('tb_po_payments');
		// }
		
		if($status!=''){
			$this->db->set('status', $status);
			$this->db->where('po_payment_id', $id);
			$this->db->update('tb_purchase_order_items_payments');
		}

		if ($this->db->trans_status() === FALSE)
			return FALSE;

		if($level!=0){
			$this->send_mail($id,$level,$po_payment['base']);
		}
		$this->db->trans_commit();
		return TRUE;
	}

	public function countTotalPayment($id){
        $this->db->select('sum(amount_paid)');
        $this->db->from('tb_purchase_order_items_payments');
        $this->db->group_by('tb_purchase_order_items_payments.po_payment_id');
        $this->db->where('tb_purchase_order_items_payments.po_payment_id', $id);
        return $this->db->get('')->row()->sum;
    }

	public function rejected($id)
	{
		$this->db->trans_begin();

		$this->db->select('tb_po_payments.*');
        $this->db->from('tb_po_payments');
        $this->db->where('tb_po_payments.id',$id);
        $query    		= $this->db->get();
		$po_payment  	= $query->unbuffered_row('array');
		$total 			= $this->countTotalPayment($id);
		$currency 		= $po_payment['currency'];
		$level 			= 0;

		if($po_payment['status']!='REJECTED'){
			$this->db->set('status', 'REJECTED');
			$this->db->set('rejected_by', config_item('auth_person_name'));
			$this->db->set('rejected_at', date('Y-m-d'));
			$this->db->where('id', $id);
			$this->db->update('tb_po_payments');
			$status = 'REJECTED';
		}		

		$this->db->select('tb_purchase_order_items_payments.*');
        $this->db->from('tb_purchase_order_items_payments');
        $this->db->where('tb_purchase_order_items_payments.po_payment_id',$id);
		$query    		= $this->db->get();
		
		foreach ($query->result_array() as $key => $item) {
			$id_po = $item['id_po'];
			$value = $item["amount_paid"]+$item["adj_value"];
			if($item['purchase_order_item_id']!==null){
				$this->db->set('left_paid_request', '"left_paid_request" + ' . $value, false);
				// $this->db->set('payment', '"payment" + ' . $key["value"], false);
				$this->db->where('id', $item["purchase_order_item_id"]);
				$this->db->update('tb_po_item');
			}else{
				$this->db->set('additional_price_remaining_request', '"additional_price_remaining_request" + ' . $value, false);
				// $this->db->set('payment', '"payment" + ' . $key["amount_paid"], false);
				$this->db->where('id', $id_po);
				$this->db->update('tb_po');
			}
			if($item['id_po']!=0){
				$this->db->set('remaining_payment_request', '"remaining_payment_request" + ' . $value, false);
				// $this->db->set('payment', '"payment" + ' . $item["amount_paid"], false);
				$this->db->where('id', $id_po);
				$this->db->update('tb_po');
			}
			
		}

		$this->db->set('status', $status);
		$this->db->where('po_payment_id', $id);
		$this->db->update('tb_purchase_order_items_payments');


		// $this->db->set('status', 'REJECTED');
		// $this->db->where('id', $id);
		// $this->db->update('tb_purchase_order_items_payments');

		if ($this->db->trans_status() === FALSE)
			return FALSE;

		$this->db->trans_commit();
		return TRUE;
	}

	function save_pembayaran()
	{
		$this->db->trans_begin();
		// $item = $this->input->post('item');
		$account 		= $this->input->post('account');
		$vendor 		= $this->input->post('vendor');
		$no_cheque 		= $this->input->post('no_cheque');
		$tanggal 		= $this->input->post('date');
		$amount 		= $this->input->post('amount');
		$no_jurnal 		= $this->input->post('no_transaksi');
		$currency 		= $this->input->post('currency');
		$no_konfirmasi 	= $this->input->post('no_konfirmasi');
		$paid_base 		= $this->input->post('paid_base');
		$kurs 			= $this->tgl_kurs(date("Y-m-d"));
		$tipe 			= $this->input->post('tipe');
		$po_payment_id 			= $this->input->post('po_payment_id');
		if ($currency == 'IDR') {
			$amount_idr = $amount;
			$amount_usd = $amount / $kurs;
		} else {
			$amount_usd = $amount;
			$amount_idr = $amount * $kurs;
		}


		$this->db->set('no_jurnal', $no_jurnal);
		$this->db->set('tanggal_jurnal  ', date("Y-m-d",strtotime($tanggal)));
		$this->db->set('source', "AP");
		$this->db->set('vendor', $vendor);
		$this->db->set('grn_no', $no_jurnal);
		$this->db->set('keterangan', strtoupper("pembayraran purchase order"));
		$this->db->set('created_by',config_item('auth_person_name'));
        $this->db->set('created_at',date('Y-m-d'));
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

		$this->db->set('coa_kredit', $account);
		$this->db->set('no_cheque', $no_cheque);
		$this->db->set('akun_kredit', $jenis);
		$this->db->set('no_konfirmasi', $no_konfirmasi);
		$this->db->set('paid_base', $paid_base);
		$this->db->set('status', "PAID");
		$this->db->set('paid_by', config_item('auth_person_name'));
		$this->db->set('paid_at', date("Y-m-d",strtotime($tanggal)));
		$this->db->where('id', $po_payment_id);
		$this->db->update('tb_po_payments');

		foreach ($_SESSION['payment']['po'] as $i => $po){
			foreach ($po['items'] as $j => $key) {
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
				$this->db->set('paid', $key["amount_paid"]);
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
		}

		foreach ($_SESSION["payment"]["attachment"] as $file) {
			$this->db->set('id_poe', $po_payment_id);
			$this->db->set('tipe', "PAYMENT");
			$this->db->set('file', $file);
			$this->db->set('tipe_att', "PAYMENT");
			$this->db->insert('tb_attachment_poe');
		}
		if ($this->db->trans_status() === FALSE)
			return FALSE;

		$this->db->trans_commit();
		return TRUE;
	}

	public function listAttachment($id)
	{
		// $this->db->where('id', $id);
		// $query    = $this->db->get('tb_purchase_order_items_payments');
		// $payment_item = $query->unbuffered_row('array');
		// $no_transaksi = $payment_item['no_transaksi'];

		// $this->db->where('no_transaksi', $no_transaksi);
		// return $this->db->get('tb_attachment_payment')->result();

		$this->db->where('id_poe', $id);
		$this->db->where('tipe', 'PAYMENT');
    	$this->db->where(array('deleted_at' => NULL));
		return $this->db->get('tb_attachment_poe')->result();
	}

	public function listAttachment_2($id)
	{
		// $this->db->where('id', $id);
		// $query    = $this->db->get('tb_po_payments');
		// $payment_item = $query->unbuffered_row('array');
		// $no_transaksi = $payment_item['document_number'];

		// $this->db->where('no_transaksi', $no_transaksi);
		// return $this->db->get('tb_attachment_payment')->result_array();
		$this->db->where('id_poe', $id);
		$this->db->where('tipe', 'PAYMENT');
    	$this->db->where(array('deleted_at' => NULL));
		return $this->db->get('tb_attachment_poe')->result_array();
	}

	function add_attachment_to_db($id, $url)
	{
		$this->db->trans_begin();

		// $this->db->where('id', $id);
		// $query    = $this->db->get('tb_po_payments');
		// $payment_item = $query->unbuffered_row('array');
		// $no_transaksi = $payment_item['document_number'];

		// $this->db->set('no_transaksi', $no_transaksi);
		// $this->db->set('file', $url);
		// $this->db->insert('tb_attachment_payment');

		$this->db->set('id_poe', $id);
		$this->db->set('tipe', "PAYMENT");
		$this->db->set('file', $url);
		$this->db->set('tipe_att', "PAYMENT");
		$this->db->insert('tb_attachment_poe');

		if ($this->db->trans_status() === FALSE)
			return FALSE;

		$this->db->trans_commit();
		return TRUE;
	}

	public function checkAttachment($id)
	{
		// $this->db->where('id', $id);
		// $query    = $this->db->get('tb_po_payments');
		// $payment_item = $query->unbuffered_row('array');
		// $no_transaksi = $payment_item['document_number'];

		// $this->db->where('no_transaksi', $no_transaksi);
		// $this->db->from('tb_attachment_payment');
		// $num_rows = $this->db->count_all_results();

		$this->db->where('id_poe', $id);
		$this->db->where('tipe', 'PAYMENT');
		$this->db->from('tb_attachment_poe');
		$num_rows = $this->db->count_all_results();

		return $num_rows;
	}

	function delete_attachment_in_db($id_att)
	{
		$this->db->trans_begin();

		// $this->db->where('id', $id_att);
		// $this->db->delete('tb_attachment_poe');
		$this->db->set('deleted_at',date('Y-m-d'));
		$this->db->set('deleted_by', config_item('auth_person_name'));
		$this->db->where('id', $id_att);
		$this->db->update('tb_attachment_poe');

		if ($this->db->trans_status() === FALSE)
		return FALSE;

		$this->db->trans_commit();
		return TRUE;
	}

	public function getNotifRecipient($level,$base=null)
	{
		$this->db->select('email');
		$this->db->from('tb_auth_users');
		$this->db->where('auth_level', $level);
		if($level==14){
			if($base=='JAKARTA'){
				$this->db->where('warehouse', $base);
			}else{
				$this->db->where('warehouse !=', 'JAKARTA');
			}			
		}
		return $this->db->get('')->result();
	}

	public function send_mail_2($level)
	{
		$recipientList = $this->getNotifRecipient($level);
		$recipient = array();
		foreach ($recipientList as $key) {
		array_push($recipient, $key->email);
		}

		$from_email = "bifa.acd@gmail.com";
		// $to_email = "aidanurul99@rocketmail.com";
		$ket_level = '';
		if ($level == 14) {
		$ket_level = 'Finance Manager';
		} elseif ($level == 10) {
		$ket_level = 'Head Of School';
		} elseif ($level == 11) {
		$ket_level = 'Chief Of Finance';
		} elseif ($level == 3) {
		$ket_level = 'VP Finance';
		}

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
		$message = "<p>Dear " . $ket_level . "</p>";
		$message .= "<p>Kamu mendapatkan pesan untuk persetujuan pembayaran purchase order.</p>";
		$message .= "<ul>";
		$message .= "</ul>";
		// $message .= "<p>No Purchase Order : " . $row['document_number'] . "</p>";
		$message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
		$message .= "<p>[ <a href='".$this->config->item('url_mrp')."/payment' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
		$message .= "<p>Thanks and regards</p>";
		$this->email->from($from_email, 'Material Resource Planning');
		$this->email->to($recipient);
		$this->email->subject('Permintaan Approval Payment Purchase Order');
		$this->email->message($message);

		//Send mail 
		if ($this->email->send())
		return true;
		else
		return $this->email->print_debugger();
	}

	public function send_mail($doc_id, $level,$base=null)
    {
		$this->db->select(
			array(
			'tb_po_payments.document_number as no_transaksi',
			'SUM(tb_purchase_order_items_payments.amount_paid) as total',
			'tb_po_payments.currency',
			'tb_po_payments.tanggal',
			)
		);
		$this->db->from('tb_po_payments');
		$this->db->join('tb_purchase_order_items_payments','tb_po_payments.id = tb_purchase_order_items_payments.po_payment_id');
		$this->db->group_by(
            array(
                'tb_po_payments.document_number',
				'tb_po_payments.currency',
				'tb_po_payments.tanggal',
            )
		);
		if(is_array($doc_id)){
            $this->db->where_in('tb_purchase_order_items_payments.po_payment_id',$doc_id);
        }else{
            $this->db->where('tb_purchase_order_items_payments.po_payment_id',$doc_id);
		}
		
		$query = $this->db->get();
		$item_message = '<tbody>';
		foreach ($query->result_array() as $key => $item) {
			$item_message .= "<tr>";
			$item_message .= "<td>" . print_date($item['tanggal']) . "</td>";
			$item_message .= "<td>" . $item['no_transaksi'] . "</td>";
			$item_message .= "<td>" . $item['currency'] . "</td>";
			$item_message .= "<td>" . print_number($item['amount_paid'], 2) . "</td>";
			$item_message .= "</tr>";
		}
		$item_message .= '</tbody>';
        

		if($base!=null){
			$recipientList = $this->getNotifRecipient($level,$base);
		}else{
			$recipientList = $this->getNotifRecipient($level);
		}		
        $recipient = array();
        foreach ($recipientList as $key) {
          array_push($recipient, $key->email);
        }

        $from_email = "bifa.acd@gmail.com";
        $to_email = "aidanurul99@rocketmail.com";
        $ket_level = '';

        $levels_and_roles = config_item('levels_and_roles');
        $ket_level = $levels_and_roles[$level];

        //Load email library 
        $this->load->library('email');
        $this->email->set_newline("\r\n");
		$message = "<p>Dear " . $ket_level . "</p>";
		$message .= "<p>Payment Request Berikut perlu Persetujuan Anda </p>";
        $message .= "<table>";
        $message .= "<thead>";
        $message .= "<tr>";
        $message .= "<th>Tanggal</th>";
        $message .= "<th>No Payment Request</th>";
        $message .= "<th>Currency</th>";
        $message .= "<th>Nominal</th>";
        $message .= "</tr>";
        $message .= "</thead>";
        $message .= $item_message;
        $message .= "</table>";
        $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
        $message .= "<p>[ <a href='".$this->config->item('url_mrp')."/payment/' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
        $message .= "<p>Thanks and regards</p>";
        $this->email->from($from_email, 'Material Resource Planning');
        $this->email->to($recipient);
        $this->email->subject('Permintaan Approval Payment Request');
        $this->email->message($message);

        //Send mail 
        if ($this->email->send())
          return true;
        else
          return $this->email->print_debugger();
	}
	
	public function listItems($vendor, $currency)
	{
		$this->db->select('tb_po.*');
		$this->db->from('tb_po');
		$this->db->where('vendor', $vendor);
		$this->db->where('default_currency', $currency);
		$this->db->where('remaining_payment_request >', 0);
		if(config_item('auth_role')=='PIC STAFF'){
			$this->db->where_in('tb_po.base', config_item('auth_warehouses'));
		}
		$this->db->where_in('status', ['OPEN', 'ORDER']);
		$this->db->order_by('tb_po.due_date', 'asc');
		$po = $this->db->get();
		// $list_po = array();
		// // foreach ($po as $detail) {
		// foreach ($po->result_array() as $key => $detail) {
		// 	$list_po[$key]= $detail;
		// 	$this->db->select('*');
		// 	$this->db->from('tb_po_item');
		// 	// $this->db->join('tb_po', 'tb_po_item.purchase_order_id = tb_po.id');
		// 	$this->db->where('tb_po_item.purchase_order_id', $detail['id']);
		// 	$this->db->where('tb_po_item.left_paid_request >', 0);
		// 	$this->db->order_by('tb_po_item.id', 'asc');
		// 	$query = $this->db->get();

		// 	foreach ($query->result_array() as $i => $value) {
		// 		$list_po[$key]['items'][$i] = $value;
		// 	}
		// }
		// return $list_po;
		return $po->result_array();
	}

	public function infoItemPo($po_id, $po_item_id)
	{
		if($po_item_id!=0){
			$this->db->select('*');
			$this->db->join('tb_po','tb_po.id = tb_po_item.purchase_order_id');
			$this->db->from('tb_po_item');
			$this->db->where('tb_po_item.id',$po_item_id);
			$query = $this->db->get();
			$return = $query->unbuffered_row('array');
		}else{
			$this->db->select('*');
			$this->db->from('tb_po');
			$this->db->where('id',$po_id);
			$query = $this->db->get();
			$return = $query->unbuffered_row('array');
		}

		return $return;
	}

	public function infoItem($po_id)
	{
		$this->db->select(array('tb_po_item.*','tb_po.due_date'));
		$this->db->join('tb_po','tb_po.id = tb_po_item.purchase_order_id');
		$this->db->from('tb_po_item');
		$this->db->where('tb_po_item.purchase_order_id',$po_id);
		$query = $this->db->get();
		$return = $query->result_array();

		return $return;
	}

	public function infoPo($po_id)
	{
		$this->db->select('*');
		// $this->db->join('tb_po','tb_po.id = tb_po_item.purchase_order_id');
		$this->db->from('tb_po');
		$this->db->where('tb_po.id',$po_id);
		$query = $this->db->get();
		return $query->unbuffered_row('array');
	}
	// }

	//report
	public function getSelectedColumnsReport()
	{
		$return = array(
			'tb_po_payments.id'                          						=> NULL,
			'tb_po_payments.document_number as no_transaksi'             		=> 'Transaction Number',
			'tb_po.document_number as po_number'               					=> '#PO',
			'tb_po.tipe as cash_credit'               							=> 'Cash/Credit',
			'tb_po_payments.vendor'                   							=> 'Vendor',
			'tb_purchase_order_items_payments.deskripsi as description'			=> 'Description',
			'tb_po_payments.status'	                     						=> 'Status',
			'tb_po_payments.currency'             								=> 'Currency',
            'tb_purchase_order_items_payments.remarks'  						=> 'Purposed IDR',
			'tb_purchase_order_items_payments.paid'								=> 'Purposed USD',
            'tb_purchase_order_items_payments.amount_paid'  					=> 'Unpaid IDR',
			'tb_purchase_order_items_payments.paid_by'							=> 'Unpaid USD',
			'tb_purchase_order_items_payments.checked_by'						=> 'Paid IDR',
			'tb_purchase_order_items_payments.approved_by'						=> 'Paid USD',
			'tb_po_payments.no_konfirmasi'										=> 'Balance IDR',
			'tb_po_payments.paid_base'											=> 'Balance USD',
		);

		return $return;
	}

	public function getSearchableColumnsReport()
	{
		$return = array(
			// 'tb_po_payments.id',
			'tb_po_payments.document_number',
			'tb_po.document_number',
			'tb_po_payments.vendor',
			'tb_purchase_order_items_payments.deskripsi',
			'tb_po_payments.status',
			'tb_po_payments.currency',
		);

		return $return;
	}

	public function getOrderableColumnsReport()
	{
		$return = array(
			null,//'tb_po_payments.id',
			'tb_po_payments.document_number',
			null,
			'tb_po.document_number',
			'tb_po_payments.vendor',
			'tb_purchase_order_items_payments.deskripsi',
			'tb_po_payments.status',
			'tb_po_payments.currency',
            null,//'tb_purchase_order_items_payments.amount_paid',
			null,//'tb_po_payments.akun_kredit',
            null,//'tb_purchase_order_items_payments.amount_paid',
			null,//'tb_po_payments.akun_kredit',
		);

		return $return;
	}

	public function getGroupedColumnsReport()
	{
		$return = array(
			'tb_po_payments.id',
			'tb_po_payments.document_number',
			'tb_po.document_number',
			'tb_po_payments.vendor',
			'tb_purchase_order_items_payments.deskripsi',
			'tb_po_payments.status',
			'tb_po_payments.currency',
		);

		return $return;
	}

	private function searchIndexReport()
	{
		if (!empty($_POST['columns'][1]['search']['value'])) {
			$search_received_date = $_POST['columns'][1]['search']['value'];
			$range_received_date  = explode(' ', $search_received_date);

			$this->db->where('tb_po_payments.tanggal >= ', $range_received_date[0]);
			$this->db->where('tb_po_payments.tanggal <= ', $range_received_date[1]);
		}

		if (!empty($_POST['columns'][2]['search']['value'])) {
			$vendor = $_POST['columns'][2]['search']['value'];

			$this->db->where('tb_po_payments.vendor', $vendor);
		}

		if (!empty($_POST['columns'][3]['search']['value'])) {
			$currency = $_POST['columns'][3]['search']['value'];

			if ($currency != 'all') {
				$this->db->where('tb_po_payments.currency', $currency);
			}
		}

		if (!empty($_POST['columns'][4]['search']['value'])) {
			$status = $_POST['columns'][4]['search']['value'];
			if($status!='all'){
				$this->db->like('tb_po_payments.status', $status);
			}			
		}

		$i = 0;

		foreach ($this->getSearchableColumnsReport() as $item) {
			if ($_POST['search']['value']) {
				$term = strtoupper($_POST['search']['value']);

				if ($i === 0) {
					$this->db->group_start();
					$this->db->like('UPPER(' . $item . ')', $term);
				} else {
					$this->db->or_like('UPPER(' . $item . ')', $term);
				}

				if (count($this->getSearchableColumnsReport()) - 1 == $i)
					$this->db->group_end();
			}

			$i++;
		}
	}

	function getIndexReport($return = 'array')
	{
		$this->db->select(array_keys($this->getSelectedColumnsReport()));
		$this->db->from('tb_purchase_order_items_payments');
		$this->db->join('tb_po_payments', 'tb_po_payments.id = tb_purchase_order_items_payments.po_payment_id');
		$this->db->join('tb_po', 'tb_po.id = tb_purchase_order_items_payments.id_po','left');
		$this->db->where_not_in('tb_po_payments.status',['REJECTED','CANCELED','REVISI']);
		// $this->db->join('tb_attachment_payment', 'tb_purchase_order_items_payments.no_transaksi = tb_attachment_payment.no_transaksi', 'left');
		// $this->db->group_by($this->getGroupedColumnsReport());

		$this->searchIndexReport();

		$column_order = $this->getOrderableColumnsReport();

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

	function countIndexFilteredReport()
	{
		$this->db->select(array_keys($this->getSelectedColumnsReport()));
		$this->db->from('tb_purchase_order_items_payments');
		$this->db->join('tb_po_payments', 'tb_po_payments.id = tb_purchase_order_items_payments.po_payment_id');
		$this->db->join('tb_po', 'tb_po.id = tb_purchase_order_items_payments.id_po','left');
		$this->db->where_not_in('tb_po_payments.status',['REJECTED','CANCELED','REVISI']);
		// $this->db->join('tb_attachment_payment', 'tb_purchase_order_items_payments.no_transaksi = tb_attachment_payment.no_transaksi', 'left');
		// $this->db->group_by($this->getGroupedColumnsReport());

		$this->searchIndex();

		$query = $this->db->get();

		return $query->num_rows();
	}

	public function countIndexReport()
	{
		$this->db->select(array_keys($this->getSelectedColumnsReport()));
		$this->db->from('tb_purchase_order_items_payments');
		$this->db->join('tb_po_payments', 'tb_po_payments.id = tb_purchase_order_items_payments.po_payment_id');
		$this->db->join('tb_po', 'tb_po.id = tb_purchase_order_items_payments.id_po','left');
		$this->db->where_not_in('tb_po_payments.status',['REJECTED','CANCELED','REVISI']);
		// $this->db->join('tb_attachment_payment', 'tb_purchase_order_items_payments.no_transaksi = tb_attachment_payment.no_transaksi', 'left');
		// $this->db->group_by($this->getGroupedColumnsReport());

		$query = $this->db->get();

		return $query->num_rows();
	}

	public function cancel()
	{
		$this->db->trans_begin();

		$id = $this->input->post('id');
        $notes = $this->input->post('notes');

		$this->db->select('tb_po_payments.*');
        $this->db->from('tb_po_payments');
        $this->db->where('tb_po_payments.id',$id);
        $query    		= $this->db->get();
		$po_payment  	= $query->unbuffered_row('array');
		$total 			= $this->countTotalPayment($id);
		$currency 		= $po_payment['currency'];
		$level 			= 0;

		if($po_payment['status']!='CANCELED'){
			$this->db->set('status', 'CANCELED');
			$this->db->set('canceled_by', config_item('auth_person_name'));
			$this->db->set('canceled_at', date('Y-m-d'));
			$this->db->where('id', $id);
			$this->db->update('tb_po_payments');
			$status = 'CANCELED';
		}		

		$this->db->select('tb_purchase_order_items_payments.*');
        $this->db->from('tb_purchase_order_items_payments');
        $this->db->where('tb_purchase_order_items_payments.po_payment_id',$id);
		$query    		= $this->db->get();
		
		foreach ($query->result_array() as $key => $item) {
			$id_po = $item['id_po'];
			$value = $item["amount_paid"]+$item["adj_value"];
			if($item['purchase_order_item_id']!==null){
				$this->db->set('left_paid_request', '"left_paid_request" + ' . $value, false);
				$this->db->where('id', $item["purchase_order_item_id"]);
				$this->db->update('tb_po_item');
			}else{
				$this->db->set('additional_price_remaining_request', '"additional_price_remaining_request" + ' . $value, false);
				$this->db->where('id', $id_po);
				$this->db->update('tb_po');
			}
			if($item['id_po']!=0){
				$this->db->set('remaining_payment_request', '"remaining_payment_request" + ' . $value, false);
				$this->db->where('id', $id_po);
				$this->db->update('tb_po');
			}
			
		}

		$this->db->set('status', $status);
		$this->db->where('po_payment_id', $id);
		$this->db->update('tb_purchase_order_items_payments');


		// $this->db->set('status', 'REJECTED');
		// $this->db->where('id', $id);
		// $this->db->update('tb_purchase_order_items_payments');

		if ($this->db->trans_status() === FALSE)
			return FALSE;

		$this->db->trans_commit();
		return TRUE;
	}

	public function save_change_account()
	{
		$this->db->trans_begin();

		$coa_kredit = $this->input->post('account');
		$akun_kredit = getAccountByCode($coa_kredit);

		$this->db->set('coa_kredit', $coa_kredit);
		$this->db->set('akun_kredit', $akun_kredit->group);
		$this->db->where('id', $this->input->post('id'));
		$this->db->update('tb_po_payments');

		if ($this->db->trans_status() === FALSE)
		return FALSE;

		$this->db->trans_commit();

		return TRUE;
	}

	public function findPurchaseOrderById($id,$tipe_po)
	{
		if($tipe_po=='EXPENSE'){
			$this->db->where('id', $id);

			$query  = $this->db->get('tb_po');
			$poe    = $query->unbuffered_row('array');

			$select = array(
				'tb_po_item.*',
				'tb_po_item.purchase_request_number',
				'tb_purchase_orders.id as poe_id',
				'tb_purchase_order_items.id as poe_item_id',
				'tb_purchase_order_items.inventory_purchase_request_detail_id as prl_item_id',
				// 'tb_inventory_purchase_requisition_details.reference_ipc',
			);

			$this->db->select($select);
			$this->db->from('tb_po_item');
			$this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.id = tb_po_item.poe_item_id', 'LEFT');
			$this->db->join('tb_purchase_orders', 'tb_purchase_orders.id = tb_purchase_order_items.purchase_order_id', 'LEFT');
			// $this->db->join('tb_inventory_purchase_requisition_details','tb_inventory_purchase_requisition_details.id=tb_purchase_order_items.inventory_purchase_request_detail_id');
			$this->db->where('tb_po_item.purchase_order_id', $poe['id']);
			$query = $this->db->get();

			$department = array();
			$request_number = array();
			$poe['department'] = '';
			$poe['request_number'] = '';

			$total = $query->num_rows();
			$no     = 1;

			foreach ($query->result_array() as $key => $value) {
				$poe['items'][$key] = $value;
				$poe['items'][$key]['reference_ipc']    = getReferenceIpc($value['prl_item_id'],'expense');
				$poe['items'][$key]['history']          = $this->getHistoryExpenseOrder($value['prl_item_id']);
				$get_department = getRequest($value['prl_item_id'],'expense','department_name');
				if(count($department)>0){
					if(!in_array($get_department,$department)){
						$department[] = $get_department;
						$poe['department'] .= $get_department.', ';
						if($no==$total){
							$poe['department'] .= $get_department;
						}else{
							$poe['department'] .= $get_department.', ';
						}
					}
				}else{
					$department[] = $get_department;
					if($no==$total){
						$poe['department'] .= $get_department;
					}else{
						$poe['department'] .= $get_department.', ';
					}
				}

				$get_request_number = getRequest($value['prl_item_id'],'expense','pr_number');
				if(count($request_number)>0){
					if(!in_array($get_request_number,$request_number)){
						$request_number[] = $get_request_number;
						if($no==$total){
							$poe['request_number'] .= $get_request_number;
						}else{
							$poe['request_number'] .= $get_request_number.', ';
						}
					}
				}else{
					$request_number[] = $get_request_number;
					if($no==$total){
						$poe['request_number'] .= $get_request_number;
					}else{
						$poe['request_number'] .= $get_request_number.', ';
					}
				}
				$no++;
			}

			// $poe['department']      = $department;
			// $poe['request_number']  = $request_number;
			$this->db->where('id_poe', $id);
			$this->db->where('tipe', 'PO');
			$this->db->where(array('deleted_at' => NULL));
			$poe['attachment'] = $this->db->get('tb_attachment_poe')->result_array();

			return $poe;

		}elseif($tipe_po=='INVENTORY MRP'){
			$this->db->where('id', $id);

			$query  = $this->db->get('tb_po');
			$poe    = $query->unbuffered_row('array');

			$select = array(
				'tb_po_item.*',
				'tb_po_item.purchase_request_number',
				'tb_purchase_orders.id as poe_id',
				'tb_purchase_order_items.id as poe_item_id',
				'tb_inventory_purchase_requisition_details.reference_ipc',
			);

			$this->db->select($select);
			$this->db->from('tb_po_item');
			$this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.id = tb_po_item.poe_item_id', 'LEFT');
			$this->db->join('tb_purchase_orders', 'tb_purchase_orders.id = tb_purchase_order_items.purchase_order_id', 'LEFT');
			$this->db->join('tb_inventory_purchase_requisition_details','tb_inventory_purchase_requisition_details.id=tb_purchase_order_items.inventory_purchase_request_detail_id', 'LEFT');
			$this->db->where('tb_po_item.purchase_order_id', $poe['id']);
			$query = $this->db->get();

			foreach ($query->result_array() as $key => $value) {
				$poe['items'][$key] = $value;
				$poe['items'][$key]['history']          = $this->getHistoryPurchaseOrder($value['poe_item_id']);
			}

			$notes = explode('-', $poe['notes']);
			$poe['revision_of_po_number'] = $notes[0];
			$poe['notes_'] = $notes[1];

			$this->db->where('id_poe', $id);
			$this->db->where('tipe', 'PO');
			$this->db->where(array('deleted_at' => NULL));
			$poe['attachment'] = $this->db->get('tb_attachment_poe')->result_array();

			return $poe;
		}elseif($tipe_po=='CAPEX'){
			$this->db->where('id', $id);

			$query  = $this->db->get('tb_po');
			$poe    = $query->unbuffered_row('array');

			$select = array(
				'tb_po_item.*',
				'tb_po_item.purchase_request_number',
				'tb_purchase_orders.id as poe_id',
				'tb_purchase_order_items.id as poe_item_id',
				'tb_purchase_order_items.inventory_purchase_request_detail_id as prl_item_id',
				// 'tb_inventory_purchase_requisition_details.reference_ipc',
			);

			$this->db->select($select);
			$this->db->from('tb_po_item');
			$this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.id = tb_po_item.poe_item_id', 'LEFT');
			$this->db->join('tb_purchase_orders', 'tb_purchase_orders.id = tb_purchase_order_items.purchase_order_id', 'LEFT');
			// $this->db->join('tb_inventory_purchase_requisition_details','tb_inventory_purchase_requisition_details.id=tb_purchase_order_items.inventory_purchase_request_detail_id');
			$this->db->where('tb_po_item.purchase_order_id', $poe['id']);
			$query = $this->db->get();

			$department = array();
			$request_number = array();
			$poe['department'] = '';
			$poe['request_number'] = '';

			$total = $query->num_rows();
			$no     = 1;

			foreach ($query->result_array() as $key => $value) {
				$poe['items'][$key] = $value;
				$poe['items'][$key]['reference_ipc']    = getReferenceIpc($value['prl_item_id'],'capex');
				$poe['items'][$key]['history']          = $this->getHistoryCapexOrder($value['prl_item_id']);
				$get_department = getRequest($value['prl_item_id'],'capex','department_name');
				if(count($department)>0){
					if(!in_array($get_department,$department)){
						$department[] = $get_department;
						$poe['department'] .= $get_department.', ';
						if($no==$total){
							$poe['department'] .= $get_department;
						}else{
							$poe['department'] .= $get_department.', ';
						}
					}
				}else{
					$department[] = $get_department;
					if($no==$total){
						$poe['department'] .= $get_department;
					}else{
						$poe['department'] .= $get_department.', ';
					}
				}

				$get_request_number = getRequest($value['prl_item_id'],'capex','pr_number');
				if(count($request_number)>0){
					if(!in_array($get_request_number,$request_number)){
						$request_number[] = $get_request_number;
						if($no==$total){
							$poe['request_number'] .= $get_request_number;
						}else{
							$poe['request_number'] .= $get_request_number.', ';
						}
					}
				}else{
					$request_number[] = $get_request_number;
						if($no==$total){
						$poe['request_number'] .= $get_request_number;
					}else{
						$poe['request_number'] .= $get_request_number.', ';
					}
				}
				$no++;
			}

			$this->db->where('id_poe', $id);
			$this->db->where('tipe', 'PO');
			$this->db->where(array('deleted_at' => NULL));
			$poe['attachment'] = $this->db->get('tb_attachment_poe')->result_array();

			return $poe;
		}
		
	}

	public function getHistoryExpenseOrder($id)
  	{
        $select = array(
			'tb_expense_purchase_requisitions.pr_number',
			'tb_expense_purchase_requisitions.pr_date',
			'tb_expense_purchase_requisitions.created_by',
			'tb_expense_purchase_requisition_details.id',
			// 'tb_expense_purchase_requisition_details.quantity',
			// 'tb_expense_purchase_requisition_details.unit',
			'tb_expense_purchase_requisition_details.amount',
			'tb_expense_purchase_requisition_details.total',
			'sum(case when tb_expense_purchase_requisition_detail_progress.poe_qty is null then 0.00 else tb_expense_purchase_requisition_detail_progress.poe_qty end) as "poe_qty"',  
			'sum(case when tb_expense_purchase_requisition_detail_progress.poe_value is null then 0.00 else tb_expense_purchase_requisition_detail_progress.poe_value end) as "poe_value"', 
			'sum(case when tb_expense_purchase_requisition_detail_progress.po_qty is null then 0.00 else tb_expense_purchase_requisition_detail_progress.po_qty end) as "po_qty"', 
			'sum(case when tb_expense_purchase_requisition_detail_progress.po_value is null then 0.00 else tb_expense_purchase_requisition_detail_progress.po_value end) as "po_value"',   
			'sum(case when tb_expense_purchase_requisition_detail_progress.grn_value is null then 0.00 else tb_expense_purchase_requisition_detail_progress.grn_value end) as "grn_value"',
			'sum(case when tb_expense_purchase_requisition_detail_progress.grn_qty is null then 0.00 else tb_expense_purchase_requisition_detail_progress.grn_qty end) as "grn_qty"',        
        );

        $group = array(
			'tb_expense_purchase_requisitions.pr_number',
			'tb_expense_purchase_requisitions.pr_date',
			'tb_expense_purchase_requisitions.created_by',
			'tb_expense_purchase_requisition_details.id',
			// 'tb_expense_purchase_requisition_details.quantity',
			// 'tb_expense_purchase_requisition_details.unit',
			'tb_expense_purchase_requisition_details.amount',
			'tb_expense_purchase_requisition_details.total',      
        );

        $this->connection->select($select);
        $this->connection->from('tb_expense_purchase_requisition_details');
        $this->connection->join('tb_expense_purchase_requisitions', 'tb_expense_purchase_requisitions.id = tb_expense_purchase_requisition_details.expense_purchase_requisition_id');
        $this->connection->join('tb_expense_monthly_budgets', 'tb_expense_monthly_budgets.id = tb_expense_purchase_requisition_details.expense_monthly_budget_id');
        $this->connection->join('tb_expense_purchase_requisition_detail_progress', 'tb_expense_purchase_requisition_detail_progress.expense_purchase_requisition_detail_id = tb_expense_purchase_requisition_details.id','left');
        $this->connection->where('tb_expense_purchase_requisition_details.id', $id);
        $this->connection->group_by($group);
        $query  = $this->connection->get();
        $return = $query->result_array();

        return $return;
  	}

	public function getHistoryPurchaseOrder($poe_item_id)
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
		  'sum(case when tb_purchase_request_items_on_hand_stock.on_hand_stock is null then 0.00 else tb_purchase_request_items_on_hand_stock.on_hand_stock end) as "on_hand_stock"',        
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
		$this->db->join('tb_receipt_items', 'tb_receipt_items.purchase_order_item_id = tb_po_item.id','left');
		$this->db->join('tb_purchase_request_items_on_hand_stock', 'tb_purchase_request_items_on_hand_stock.prl_item_id = tb_inventory_purchase_requisition_details.id','left');
		$this->db->where('tb_purchase_order_items.id', $poe_item_id);
		$this->db->group_by($group);
		$query  = $this->db->get();
		$return = $query->result_array();
	
		return $return;
			
	}

	public function getHistoryCapexOrder($id)
  	{
        $select = array(
          'tb_capex_purchase_requisitions.pr_number',
          'tb_capex_purchase_requisitions.pr_date',
          'tb_capex_purchase_requisitions.created_by',
          'tb_capex_purchase_requisition_details.id',
          'tb_capex_purchase_requisition_details.quantity',
          'tb_capex_purchase_requisition_details.unit',
          'tb_capex_purchase_requisition_details.price',
          'tb_capex_purchase_requisition_details.total',
          'sum(case when tb_capex_purchase_requisition_detail_progress.poe_qty is null then 0.00 else tb_capex_purchase_requisition_detail_progress.poe_qty end) as "poe_qty"',  
          'sum(case when tb_capex_purchase_requisition_detail_progress.poe_value is null then 0.00 else tb_capex_purchase_requisition_detail_progress.poe_value end) as "poe_value"', 
          'sum(case when tb_capex_purchase_requisition_detail_progress.po_qty is null then 0.00 else tb_capex_purchase_requisition_detail_progress.po_qty end) as "po_qty"', 
          'sum(case when tb_capex_purchase_requisition_detail_progress.po_value is null then 0.00 else tb_capex_purchase_requisition_detail_progress.po_value end) as "po_value"',   
          'sum(case when tb_capex_purchase_requisition_detail_progress.grn_value is null then 0.00 else tb_capex_purchase_requisition_detail_progress.grn_value end) as "grn_value"',
          'sum(case when tb_capex_purchase_requisition_detail_progress.grn_qty is null then 0.00 else tb_capex_purchase_requisition_detail_progress.grn_qty end) as "grn_qty"',        
        );

        $group = array(
          'tb_capex_purchase_requisitions.pr_number',
          'tb_capex_purchase_requisitions.pr_date',
          'tb_capex_purchase_requisitions.created_by',
          'tb_capex_purchase_requisition_details.id',
          'tb_capex_purchase_requisition_details.quantity',
          'tb_capex_purchase_requisition_details.unit',
          'tb_capex_purchase_requisition_details.price',
          'tb_capex_purchase_requisition_details.total',      
        );

        $this->connection->select($select);
        $this->connection->from('tb_capex_purchase_requisition_details');
        $this->connection->join('tb_capex_purchase_requisitions', 'tb_capex_purchase_requisitions.id = tb_capex_purchase_requisition_details.capex_purchase_requisition_id');
        $this->connection->join('tb_capex_monthly_budgets', 'tb_capex_monthly_budgets.id = tb_capex_purchase_requisition_details.capex_monthly_budget_id');
        $this->connection->join('tb_capex_purchase_requisition_detail_progress', 'tb_capex_purchase_requisition_detail_progress.capex_purchase_requisition_detail_id = tb_capex_purchase_requisition_details.id','left');
        $this->connection->where('tb_capex_purchase_requisition_details.id', $id);
        $this->connection->group_by($group);
        $query  = $this->connection->get();
        $return = $query->result_array();

        return $return;
  	}

	public function findPurchaseOrderEvaluationById($id,$tipe_po){
		if($tipe_po=='EXPENSE'){
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

			$this->db->from('tb_purchase_order_items');
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
				$poe['request'][$i]['history']          = $this->getHistoryExpenseOrderEvaluation($item['inventory_purchase_request_detail_id']);
				$poe['request'][$i]['vendors'] = array();
				$poe['request'][$i]['reference_ipc']     = getReferenceIpcByPrlItemId($item['inventory_purchase_request_detail_id'],'expense');

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
			
			$poe['status_edit'] = getStatusEditPoe($poe['evaluation_number']);
			$this->db->where('id_poe', $id);
			$this->db->where('tipe', 'POE');
			$this->db->where(array('deleted_at' => NULL));
			$poe['attachment'] = $this->db->get('tb_attachment_poe')->result_array();
			return $poe;
		}elseif ($tipe_po=='CAPEX') {
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

			$this->db->from('tb_purchase_order_items');
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
				$poe['request'][$i]['history']          = $this->getHistoryCapexOrderEvaluation($item['inventory_purchase_request_detail_id']);
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
			
			$poe['status_edit'] = getStatusEditPoe($poe['evaluation_number']);
			$this->db->where('id_poe', $id);
			$this->db->where('tipe', 'POE');
			$this->db->where(array('deleted_at' => NULL));
			$poe['attachment'] = $this->db->get('tb_attachment_poe')->result_array();
			return $poe;
		}elseif ($tipe_po=='INVENTORY') {
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

			$this->db->from('tb_purchase_order_items');
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
				$poe['request'][$i]['history']          = $this->getHistoryInventoryOrderEvaluation($item['inventory_purchase_request_detail_id']);
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
			
			$poe['status_edit'] = getStatusEditPoe($poe['evaluation_number']);
			$this->db->where('id_poe', $id);
			$this->db->where('tipe', 'POE');
			$this->db->where(array('deleted_at' => NULL));
			$poe['attachment'] = $this->db->get('tb_attachment_poe')->result_array();
			return $poe;
		}elseif ($tipe_po=='INVENTORY MRP') {
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
				$poe['request'][$i]['history']          = $this->getHistoryInventoryMrpOrderEvaluation($item['inventory_purchase_request_detail_id']);
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
			
			$poe['status_edit'] = getStatusEditPoe($poe['evaluation_number']);
			$this->db->where('id_poe', $id);
			$this->db->where('tipe', 'POE');
			$this->db->where(array('deleted_at' => NULL));
			$poe['attachment'] = $this->db->get('tb_attachment_poe')->result_array();
			return $poe;
		}
	}

	public function getHistoryExpenseOrderEvaluation($id)
  	{
        $select = array(
          'tb_expense_purchase_requisitions.pr_number',
          'tb_expense_purchase_requisitions.pr_date',
          'tb_expense_purchase_requisitions.created_by',
          'tb_expense_purchase_requisition_details.id',
          'tb_expense_purchase_requisition_details.amount',
          'tb_expense_purchase_requisition_details.total',
          'sum(case when tb_expense_purchase_requisition_detail_progress.poe_qty is null then 0.00 else tb_expense_purchase_requisition_detail_progress.poe_qty end) as "poe_qty"',  
          'sum(case when tb_expense_purchase_requisition_detail_progress.poe_value is null then 0.00 else tb_expense_purchase_requisition_detail_progress.poe_value end) as "poe_value"', 
          'sum(case when tb_expense_purchase_requisition_detail_progress.po_qty is null then 0.00 else tb_expense_purchase_requisition_detail_progress.po_qty end) as "po_qty"', 
          'sum(case when tb_expense_purchase_requisition_detail_progress.po_value is null then 0.00 else tb_expense_purchase_requisition_detail_progress.po_value end) as "po_value"',   
          'sum(case when tb_expense_purchase_requisition_detail_progress.grn_value is null then 0.00 else tb_expense_purchase_requisition_detail_progress.grn_value end) as "grn_value"',
          'sum(case when tb_expense_purchase_requisition_detail_progress.grn_qty is null then 0.00 else tb_expense_purchase_requisition_detail_progress.grn_qty end) as "grn_qty"',        
        );

        $group = array(
          'tb_expense_purchase_requisitions.pr_number',
          'tb_expense_purchase_requisitions.pr_date',
          'tb_expense_purchase_requisitions.created_by',
          'tb_expense_purchase_requisition_details.id',
          // 'tb_expense_purchase_requisition_details.quantity',
          // 'tb_expense_purchase_requisition_details.unit',
          'tb_expense_purchase_requisition_details.amount',
          'tb_expense_purchase_requisition_details.total',      
        );

        $this->connection->select($select);
        $this->connection->from('tb_expense_purchase_requisition_details');
        $this->connection->join('tb_expense_purchase_requisitions', 'tb_expense_purchase_requisitions.id = tb_expense_purchase_requisition_details.expense_purchase_requisition_id');
        $this->connection->join('tb_expense_monthly_budgets', 'tb_expense_monthly_budgets.id = tb_expense_purchase_requisition_details.expense_monthly_budget_id');
        $this->connection->join('tb_expense_purchase_requisition_detail_progress', 'tb_expense_purchase_requisition_detail_progress.expense_purchase_requisition_detail_id = tb_expense_purchase_requisition_details.id','left');
        $this->connection->where('tb_expense_purchase_requisition_details.id', $id);
        $this->connection->group_by($group);
        $query  = $this->connection->get();
        $return = $query->result_array();

        return $return;
  	}

	public function getHistoryCapexOrderEvaluation($id)
  	{
        $select = array(
          	'tb_capex_purchase_requisitions.pr_number',
          	'tb_capex_purchase_requisitions.pr_date',
          	'tb_capex_purchase_requisitions.created_by',
          	'tb_capex_purchase_requisition_details.id',
          	'tb_capex_purchase_requisition_details.quantity',
          	'tb_capex_purchase_requisition_details.unit',
          	'tb_capex_purchase_requisition_details.price',
          	'tb_capex_purchase_requisition_details.total',
          	'sum(case when tb_capex_purchase_requisition_detail_progress.poe_qty is null then 0.00 else tb_capex_purchase_requisition_detail_progress.poe_qty end) as "poe_qty"',  
          	'sum(case when tb_capex_purchase_requisition_detail_progress.poe_value is null then 0.00 else tb_capex_purchase_requisition_detail_progress.poe_value end) as "poe_value"', 
          	'sum(case when tb_capex_purchase_requisition_detail_progress.po_qty is null then 0.00 else tb_capex_purchase_requisition_detail_progress.po_qty end) as "po_qty"', 
          	'sum(case when tb_capex_purchase_requisition_detail_progress.po_value is null then 0.00 else tb_capex_purchase_requisition_detail_progress.po_value end) as "po_value"',   
          	'sum(case when tb_capex_purchase_requisition_detail_progress.grn_value is null then 0.00 else tb_capex_purchase_requisition_detail_progress.grn_value end) as "grn_value"',
          	'sum(case when tb_capex_purchase_requisition_detail_progress.grn_qty is null then 0.00 else tb_capex_purchase_requisition_detail_progress.grn_qty end) as "grn_qty"',        
        );

        $group = array(
          	'tb_capex_purchase_requisitions.pr_number',
          	'tb_capex_purchase_requisitions.pr_date',
          	'tb_capex_purchase_requisitions.created_by',
          	'tb_capex_purchase_requisition_details.id',
          	'tb_capex_purchase_requisition_details.quantity',
          	'tb_capex_purchase_requisition_details.unit',
          	'tb_capex_purchase_requisition_details.price',
          	'tb_capex_purchase_requisition_details.total',      
        );

        $this->connection->select($select);
        $this->connection->from('tb_capex_purchase_requisition_details');
        $this->connection->join('tb_capex_purchase_requisitions', 'tb_capex_purchase_requisitions.id = tb_capex_purchase_requisition_details.capex_purchase_requisition_id');
        $this->connection->join('tb_capex_monthly_budgets', 'tb_capex_monthly_budgets.id = tb_capex_purchase_requisition_details.capex_monthly_budget_id');
        $this->connection->join('tb_capex_purchase_requisition_detail_progress', 'tb_capex_purchase_requisition_detail_progress.capex_purchase_requisition_detail_id = tb_capex_purchase_requisition_details.id','left');
        $this->connection->where('tb_capex_purchase_requisition_details.id', $id);
        $this->connection->group_by($group);
        $query  = $this->connection->get();
        $return = $query->result_array();

        return $return;
  	}

	public function getHistoryInventoryOrderEvaluation($id)
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
			'sum(case when tb_inventory_purchase_requisition_detail_progress.poe_qty is null then 0.00 else tb_inventory_purchase_requisition_detail_progress.poe_qty end) as "poe_qty"',  
			'sum(case when tb_inventory_purchase_requisition_detail_progress.poe_value is null then 0.00 else tb_inventory_purchase_requisition_detail_progress.poe_value end) as "poe_value"', 
			'sum(case when tb_inventory_purchase_requisition_detail_progress.po_qty is null then 0.00 else tb_inventory_purchase_requisition_detail_progress.po_qty end) as "po_qty"', 
			'sum(case when tb_inventory_purchase_requisition_detail_progress.po_value is null then 0.00 else tb_inventory_purchase_requisition_detail_progress.po_value end) as "po_value"',   
			'sum(case when tb_inventory_purchase_requisition_detail_progress.grn_value is null then 0.00 else tb_inventory_purchase_requisition_detail_progress.grn_value end) as "grn_value"',
			'sum(case when tb_inventory_purchase_requisition_detail_progress.grn_qty is null then 0.00 else tb_inventory_purchase_requisition_detail_progress.grn_qty end) as "grn_qty"',        
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

        $this->connection->select($select);
        $this->connection->from('tb_inventory_purchase_requisition_details');
        $this->connection->join('tb_inventory_purchase_requisitions', 'tb_inventory_purchase_requisitions.id = tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id');
        $this->connection->join('tb_inventory_monthly_budgets', 'tb_inventory_monthly_budgets.id = tb_inventory_purchase_requisition_details.inventory_monthly_budget_id');
        $this->connection->join('tb_inventory_purchase_requisition_detail_progress', 'tb_inventory_purchase_requisition_detail_progress.inventory_purchase_requisition_detail_id = tb_inventory_purchase_requisition_details.id','left');
        $this->connection->where('tb_inventory_purchase_requisition_details.id', $id);
        $this->connection->group_by($group);
        $query  = $this->connection->get();
        $return = $query->result_array();

        return $return;
  	}

	public function getHistoryInventoryMrpOrderEvaluation($inventory_purchase_request_detail_id)
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
		$this->db->where_in('tb_po.status',['PURPOSE','OPEN','ORDER','CLOSE']);
		$this->db->group_by($group);
		$query  = $this->db->get();
		$return = $query->result_array();

		return $return;
        
  	}

	public function getRequestIdByItemId($id,$tipe_po){
		if ($tipe_po=='EXPENSE') {
			$this->connection->select('tb_expense_purchase_requisition_details.*');
			$this->connection->from('tb_expense_purchase_requisition_details');
			$this->connection->where('tb_expense_purchase_requisition_details.id', $id);

			$query    = $this->connection->get();
			$request_item  = $query->unbuffered_row('array');

			return $request_item['expense_purchase_requisition_id'];
		}elseif ($tipe_po=='CAPEX') {
			$this->connection->select('tb_capex_purchase_requisition_details.*');
			$this->connection->from('tb_capex_purchase_requisition_details');
			$this->connection->where('tb_capex_purchase_requisition_details.id', $id);

			$query    = $this->connection->get();
			$request_item  = $query->unbuffered_row('array');

			return $request_item['capex_purchase_requisition_id'];
		}elseif ($tipe_po=='INVENTORY') {
			$this->connection->select('tb_inventory_purchase_requisition_details.*');
			$this->connection->from('tb_inventory_purchase_requisition_details');
			$this->connection->where('tb_inventory_purchase_requisition_details.id', $id);

			$query    = $this->connection->get();
			$request_item  = $query->unbuffered_row('array');

			return $request_item['inventory_purchase_requisition_id'];
		}elseif ($tipe_po=='INVENTORY MRP') {
			$this->db->select('tb_inventory_purchase_requisition_details.*');
			$this->db->from('tb_inventory_purchase_requisition_details');
			$this->db->where('tb_inventory_purchase_requisition_details.id', $id);

			$query    = $this->db->get();
			$request_item  = $query->unbuffered_row('array');

			return $request_item['inventory_purchase_requisition_id'];
		}
	}
	
	public function findPurchaseRequestById($id,$tipe_po){
		if ($tipe_po=='EXPENSE') {
			$this->connection->select('tb_expense_purchase_requisitions.*, tb_cost_centers.cost_center_name, tb_cost_centers.cost_center_code, tb_cost_centers.department_id');
			$this->connection->from('tb_expense_purchase_requisitions');
			$this->connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_expense_purchase_requisitions.annual_cost_center_id');
			$this->connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
			$this->connection->where('tb_expense_purchase_requisitions.id', $id);

			$query    = $this->connection->get();
			$request  = $query->unbuffered_row('array');

			$select = array(
				'tb_expense_purchase_requisition_details.*',
				'tb_accounts.account_name',
				'tb_accounts.account_code',
				'tb_expense_monthly_budgets.account_id',
				'tb_expense_monthly_budgets.ytd_budget',
				'tb_expense_monthly_budgets.ytd_used_budget',
			);

			$group_by = array(
				'tb_expense_purchase_requisition_details.id',
				'tb_accounts.account_name',
				'tb_accounts.account_code',
				'tb_expense_monthly_budgets.account_id',
				'tb_expense_monthly_budgets.ytd_budget',
				'tb_expense_monthly_budgets.ytd_used_budget',
			);

			$this->connection->select($select);
			$this->connection->from('tb_expense_purchase_requisition_details');
			$this->connection->join('tb_expense_monthly_budgets', 'tb_expense_monthly_budgets.id = tb_expense_purchase_requisition_details.expense_monthly_budget_id');
			$this->connection->join('tb_accounts', 'tb_accounts.id = tb_expense_monthly_budgets.account_id');
			$this->connection->where('tb_expense_purchase_requisition_details.expense_purchase_requisition_id', $id);
			$this->connection->group_by($group_by);

			$query = $this->connection->get();

			foreach ($query->result_array() as $key => $value){
				$request['items'][$key] = $value;
				$request['items'][$key]['balance_mtd_budget']       = $value['ytd_budget'] - $value['ytd_used_budget'];
				$this->column_select = array(
					'SUM(tb_expense_monthly_budgets.mtd_budget) as budget',
					'SUM(tb_expense_monthly_budgets.mtd_used_budget) as used_budget',
					'tb_expense_monthly_budgets.account_id',
					'tb_expense_monthly_budgets.annual_cost_center_id',
				);

				$this->column_groupby = array(                
					'tb_expense_monthly_budgets.account_id',
					'tb_expense_monthly_budgets.annual_cost_center_id',
				);

				$this->connection->select($this->column_select);
				$this->connection->from('tb_expense_monthly_budgets');
				$this->connection->where('tb_expense_monthly_budgets.annual_cost_center_id', $request['annual_cost_center_id']);
				$this->connection->where('tb_expense_monthly_budgets.account_id', $value['account_id']);
				$this->connection->group_by($this->column_groupby);

				$query = $this->connection->get();
				$row   = $query->unbuffered_row('array');

				$request['items'][$key]['maximum_price']        =  $value['total'] + $row['budget'] - $row['used_budget'];
				$request['items'][$key]['balance_ytd_budget']   = $row['budget'] - $row['used_budget'];            
				$request['items'][$key]['history']              = $this->getHistoryExpenseRequest($request['annual_cost_center_id'],$value['account_id'],$request['order_number']);
			}
			$this->connection->where('id_purchase', $id);
			$this->connection->where('tipe', 'expense');
			$data = $this->connection->get('tb_attachment')->result_array();
			
			$request["attachment"]  = $data;
			return $request;
		}
	}

	public function getHistoryExpenseRequest($annual_cost_center_id,$account_id,$order_number)
    {
        $select = array(
            'tb_expense_purchase_requisitions.pr_number',
            'tb_expense_purchase_requisitions.pr_date',
            'tb_expense_purchase_requisitions.created_by',
            'tb_expense_purchase_requisition_details.amount',
            'tb_expense_purchase_requisition_details.total',
        );
        $this->connection->select($select);
        $this->connection->from('tb_expense_purchase_requisition_details');
        $this->connection->join('tb_expense_purchase_requisitions', 'tb_expense_purchase_requisitions.id = tb_expense_purchase_requisition_details.expense_purchase_requisition_id');
        $this->connection->join('tb_expense_monthly_budgets', 'tb_expense_monthly_budgets.id = tb_expense_purchase_requisition_details.expense_monthly_budget_id');
        $this->connection->where('tb_expense_monthly_budgets.annual_cost_center_id', $annual_cost_center_id);
        $this->connection->where('tb_expense_monthly_budgets.account_id', $account_id);
        $this->connection->where('tb_expense_purchase_requisitions.order_number <',$order_number);
        $this->connection->order_by('tb_expense_purchase_requisitions.order_number','desc');
        $query  = $this->connection->get();

        return $query->result_array();
    }

	public function getSelectedColumnsDetailReport()
	{
		$return = array(
			'tb_po_payments.id'                          						=> NULL,
			'tb_po_payments.tanggal'               								=> 'Date',
			'tb_po_payments.vendor'                   							=> 'Vendor',
			'tb_po_payments.document_number as payment_number'             		=> 'Payment Voucher',
			'tb_po.document_number as purchase_order_number'    				=> 'Purchase Order',
			'tb_purchase_orders.evaluation_number'             					=> 'Purchase Order Evaluation',
            'tb_purchase_order_items.purchase_request_number'  					=> 'Request',
		);

		return $return;
	}

	public function getSearchableColumnsDetailReport()
	{
		$return = array(
			'tb_po_payments.vendor',
			'tb_po_payments.document_number as payment_number',
			'tb_po.document_number as purchase_order_number',
			'tb_purchase_orders.evaluation_number',
            'tb_purchase_order_items.purchase_request_number',
		);

		return $return;
	}

	public function getOrderableColumnsDetailReport()
	{
		$return = array(
			NULL,
			'tb_po_payments.tanggal',
			'tb_po_payments.vendor',
			'tb_po_payments.document_number as payment_number',
			'tb_po.document_number as purchase_order_number',
			'tb_purchase_orders.evaluation_number',
            'tb_purchase_order_items.purchase_request_number',
		);

		return $return;
	}

	public function getGroupedColumnsDetailReport()
	{
		$return = array(
			'tb_po_payments.id',
			'tb_po_payments.tanggal',
			'tb_po_payments.vendor',
			'tb_po_payments.document_number as payment_number',
			'tb_po.document_number as purchase_order_number',
			'tb_purchase_orders.evaluation_number',
            'tb_purchase_order_items.purchase_request_number',
		);

		return $return;
	}

	private function searchIndexDetailReport()
	{

		$i = 0;

		foreach ($this->getSearchableColumnsDetailReport() as $item) {
			if ($_POST['search']['value']) {
				$term = strtoupper($_POST['search']['value']);

				if ($i === 0) {
					$this->db->group_start();
					$this->db->like('UPPER(' . $item . ')', $term);
				} else {
					$this->db->or_like('UPPER(' . $item . ')', $term);
				}

				if (count($this->getSearchableColumnsDetailReport()) - 1 == $i)
					$this->db->group_end();
			}

			$i++;
		}
	}

	function getIndexDetailReport($return = 'array')
	{
		$this->db->select(array_keys($this->getSelectedColumnsDetailReport()));
		$this->db->from('tb_purchase_order_items_payments');
		$this->db->join('tb_po_payments', 'tb_po_payments.id = tb_purchase_order_items_payments.po_payment_id');
		$this->db->join('tb_po_item','tb_po_item.id = ztb_purchase_order_items_payments.purchase_order_item_id','left');
		$this->db->join('tb_po', 'tb_po.id = tb_po_item.purchase_order_id');
		
		$this->db->group_by($this->getGroupedColumnsDetailReport());

		$this->searchIndexDetailReport();

		$column_order = $this->getOrderableColumnsDetailReport();

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

	function countIndexFilteredDetailReport()
	{
		$this->db->select(array_keys($this->getSelectedColumnsDetailReport()));
		$this->db->from('tb_po_payments');
		$this->db->join('tb_purchase_order_items_payments', 'tb_po_payments.id = tb_purchase_order_items_payments.po_payment_id');
		// if(is_granted($this->data['modules']['payment'], 'document') === TRUE){
        //     $this->db->where_in('tb_po_payments.base', config_item('auth_warehouses'));
		// }
		// $this->db->join('tb_po', 'tb_po.id = tb_purchase_order_items_payments.id_po');
		// $this->db->join('tb_attachment_payment', 'tb_purchase_order_items_payments.no_transaksi = tb_attachment_payment.no_transaksi', 'left');
		$this->db->group_by($this->getGroupedColumnsDetailReport());

		$this->searchIndex();

		$query = $this->db->get();

		return $query->num_rows();
	}

	public function countIndexDetailReport()
	{
		$this->db->select(array_keys($this->getSelectedColumnsDetailReport()));
		$this->db->from('tb_po_payments');
		$this->db->join('tb_purchase_order_items_payments', 'tb_po_payments.id = tb_purchase_order_items_payments.po_payment_id');
		// if(is_granted($this->data['modules']['payment'], 'document') === TRUE){
        //     $this->db->where_in('tb_po_payments.base', config_item('auth_warehouses'));
		// }
		// $this->db->join('tb_po', 'tb_po.id = tb_purchase_order_items_payments.id_po');
		// $this->db->join('tb_attachment_payment', 'tb_purchase_order_items_payments.no_transaksi = tb_attachment_payment.no_transaksi', 'left');
		$this->db->group_by($this->getGroupedColumnsDetailReport());

		$query = $this->db->get();

		return $query->num_rows();
	}
}

/* End of file Payment_Model.php */
/* Location: ./application/models/Payment_Model.php */
