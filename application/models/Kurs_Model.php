<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Kurs_Model extends MY_Model
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = config_item('module')['kurs'];
  }

  public function getSelectedColumns()
  {
    return array(
      'id'              => NULL,
      'kurs_dollar'     => 'Kurs Dollar',
      'date'            => 'Tanggal Aktif',
      'created_by'      => 'Created By',
      'status'          => 'Status',
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'kurs_dollar',
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'kurs_dollar',
    );
  }

  private function searchIndex()
  {
  	if (!empty($_POST['columns'][2]['search']['value'])){
      $search_received_date = $_POST['columns'][2]['search']['value'];
      $range_received_date  = explode(' ', $search_received_date);

      $this->db->where('tb_master_kurs_dollar.date >= ', $range_received_date[0]);
      $this->db->where('tb_master_kurs_dollar.date <= ', $range_received_date[1]);
    }
    $i = 0;

    foreach ($this->getSearchableColumns() as $item){
      if ($_POST['search']['value']){
        if ($i === 0){
          $this->db->group_start();
          $this->db->like('UPPER('.$item.')', strtoupper($_POST['search']['value']));
        } else {
          $this->db->or_like('UPPER('.$item.')', strtoupper($_POST['search']['value']));
        }

        if (count($this->getSearchableColumns()) - 1 == $i){
          $this->db->group_end();
        }
      }

      $i++;
    }
  }

  function getIndex($return = 'array')
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_master_kurs_dollar');

    $this->searchIndex();

    $column_order = $this->getOrderableColumns();

    // if (isset($_POST['order'])){
    //   foreach ($_POST['order'] as $key => $order){
    //     $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
    //   }
    // } else {
      $this->db->order_by('date','desc');
    // }

    if ($_POST['length'] != -1)
      $this->db->limit($_POST['length'], $_POST['start']);

    $query = $this->db->get();

    if ($return === 'object'){
      return $query->result();
    } elseif ($return === 'json'){
      return json_encode($query->result());
    } else {
      return $query->result_array();
    }
  }

  function countIndexFiltered()
  {
    $this->db->from('tb_master_kurs_dollar');
    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->from('tb_master_kurs_dollar');

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function findById($id)
  {
    $this->db->where('id', $id);
    $query = $this->db->get('tb_master_kurs_dollar');

    return $query->row_array();
  }

  	public function insert()
  	{
    	$this->db->trans_begin();

	    $this->db->set('kurs_dollar', strtoupper($this->input->post('kurs_dollar')));
	    $this->db->set('date', strtoupper($this->input->post('tanggal')));
	    $this->db->set('status', 'ACTIVE');
	    $this->db->set('created_by', config_item('auth_username'));
	    $this->db->set('created_at', date('Y-m-d H:i:s'));
	    $this->db->insert('tb_master_kurs_dollar');

	    //update value rupiah di tb_stock_in_stores
	    $this->db->select('*');
	    $this->db->from('tb_stock_in_stores');
	    $this->db->where('kurs_dollar !=', '1');
	    $this->db->where('received_date', strtoupper($this->input->post('tanggal')));
	    $query            = $this->db->get();
	    $stock_in_stores  = $query->result_array();

	    $kurs_baru = floatval($this->input->post('kurs_dollar'));

	    foreach ($stock_in_stores as $stock_detail) {
	    	$unit_value_dollar 		= floatval($stock_detail['unit_value'])/floatval($stock_detail['kurs_dollar']);
	    	$initial_unit_value_dollar = floatval($stock_detail['initial_unit_value'])/floatval($stock_detail['kurs_dollar']);
	      	$unit_value_dollar_1 = $unit_value_dollar;
	      	$initial_unit_value_dollar_1 = $initial_unit_value_dollar;
	      	$this->db->set('kurs_dollar', strtoupper($this->input->post('kurs_dollar')));
	      	$this->db->set('unit_value', floatval($unit_value_dollar_1)*floatval($kurs_baru));
	      	// $this->db->set('initial_unit_value', floatval($initial_unit_value_dollar_1)*floatval($kurs_baru));
	      	$this->db->set('unit_value_dollar', floatval($unit_value_dollar));
	      	// $this->db->set('initial_unit_value_dollar', floatval($initial_unit_value_dollar));
	      	$this->db->where('id', $stock_detail['id']);
	      	$this->db->update('tb_stock_in_stores');

	      	//update value rupiah di tb_receipts items
		    $this->db->select('*');
		    $this->db->from('tb_receipt_items');
		    // $this->db->where('kurs_dollar !=', '1');
		    $this->db->where('stock_in_stores_id', $stock_detail['id']);
		    $query            = $this->db->get();
		    $tb_receipt_items  = $query->result_array();

		    // $kurs_baru = floatval($this->input->post('kurs_dollar'));

		    foreach ($tb_receipt_items as $ri) {
		      $unit_value         = floatval($unit_value_dollar)*floatval($kurs_baru);
		      $total_unit_value   = floatval($unit_value)*floatval($ri['received_quantity']);
		      $received_unit_value_dollar = floatval($unit_value)/floatval($kurs_baru);
		      $this->db->set('kurs_dollar', floatval($this->input->post('kurs_dollar')));
		      $this->db->set('received_unit_value', floatval($unit_value));
		      $this->db->set('received_total_value', floatval($total_unit_value));
		      $this->db->set('received_unit_value_dollar', floatval($received_unit_value_dollar));
		      $this->db->set('received_total_value_dollar', floatval($received_unit_value_dollar)*floatval($ri['received_quantity']));
		      $this->db->where('id', $ri['id']);
		      $this->db->update('tb_receipt_items'); 
		    }

		    //update value dollar di tb_receipts items
		    $this->db->select('*');
		    $this->db->from('tb_issuance_items');
		    $this->db->where('stock_in_stores_id', $stock_detail['id']);
		    // $this->db->where('received_date_item', strtoupper($this->input->post('tanggal')));
		    $query            = $this->db->get();
		    $tb_issuance_items  = $query->result_array();

		    // $kurs_baru = floatval($this->input->post('kurs_dollar'));

		    foreach ($tb_issuance_items as $ms) {
			    $issued_unit_value         = floatval($unit_value_dollar_1)*floatval($kurs_baru);
			    $issued_total_value        = floatval($issued_unit_value)*floatval($ms['issued_quantity']);
			    // $this->db->set('kurs_dollar', floatval($this->input->post('kurs_dollar')));
			    $this->db->set('issued_unit_value', floatval($issued_unit_value));
			    $this->db->set('issued_total_value', floatval($issued_total_value));
			    $this->db->where('id', $ms['id']);
			    $this->db->update('tb_issuance_items'); 
		    }

		    $this->db->select('*');
		    $this->db->from('tb_stock_cards');
		    $this->db->where('stock_in_stores_id',$stock_detail['id']);

		    $query_tb_stock_cards   = $this->db->get();
		    $tb_stock_cards  		= $query_tb_stock_cards->result_array();

		    foreach ($tb_stock_cards as $card) {
		    	$unit_value         = floatval($unit_value_dollar)*floatval($kurs_baru);
		    	$this->db->set('unit_value', floatval($unit_value));
				$this->db->set('total_value', floatval($unit_value)*floatval($card['quantity']));
			    $this->db->where('id', $card['id']);
			    $this->db->update('tb_stock_cards');

		    }


	    }

	    //update value dollar di tb_stock_in_stores
	    $this->db->select('*');
	    $this->db->from('tb_stock_in_stores');
	    $this->db->where('kurs_dollar', '1');
	    $this->db->where('received_date', strtoupper($this->input->post('tanggal')));
	    $query            = $this->db->get();
	    $stock_in_stores  = $query->result_array();

	    // $kurs_baru = floatval($this->input->post('kurs_dollar'));

	    foreach ($stock_in_stores as $stock_detail) {
	      	$unit_value_1 = $stock_detail['unit_value'];
	      	$initial_unit_value_1 = $stock_detail['initial_unit_value'];
	      	$this->db->set('unit_value_dollar', floatval($unit_value_1)/floatval($kurs_baru));
	      	$this->db->set('initial_unit_value_dollar', floatval($initial_unit_value_1)/floatval($kurs_baru));
	      	$this->db->where('id', $stock_detail['id']);
	      	$this->db->update('tb_stock_in_stores');

	      	//update value dollar di tb_receipts items
		    $this->db->select('*');
		    $this->db->from('tb_receipt_items');
		    $this->db->where('stock_in_stores_id', $stock_detail['id']);
		    // $this->db->where('received_date_item', strtoupper($this->input->post('tanggal')));
		    $query            = $this->db->get();
		    $tb_receipt_items  = $query->result_array();

		    // $kurs_baru = floatval($this->input->post('kurs_dollar'));

		    foreach ($tb_receipt_items as $ri) {
			    $received_unit_value_dollar         = floatval($ri['received_unit_value'])/floatval($kurs_baru);
			    $received_total_value_dollar        = floatval($unit_value)*floatval($ri['received_quantity']);
			    // $this->db->set('kurs_dollar', floatval($this->input->post('kurs_dollar')));
			    $this->db->set('received_unit_value_dollar', floatval($received_unit_value_dollar));
			    $this->db->set('received_total_value_dollar', floatval($received_total_value_dollar));
			    $this->db->where('id', $ri['id']);
			    $this->db->update('tb_receipt_items'); 
		    }

		    //update value dollar di tb_receipts items
		    // $this->db->select('*');
		    // $this->db->from('tb_issuance_items');
		    // $this->db->where('stock_in_stores_id', $stock_detail['id']);
		    // // $this->db->where('received_date_item', strtoupper($this->input->post('tanggal')));
		    // $query            = $this->db->get();
		    // $tb_issuance_items  = $query->result_array();

		    // // $kurs_baru = floatval($this->input->post('kurs_dollar'));

		    // foreach ($tb_issuance_items as $ms) {
			   //  $issued_unit_value         = floatval($unit_value_1)*floatval($kurs_baru);
			   //  $issued_total_value        = floatval($issued_unit_value)*floatval($ms['issued_quantity']);
			   //  // $this->db->set('kurs_dollar', floatval($this->input->post('kurs_dollar')));
			   //  $this->db->set('issued_unit_value', floatval($issued_unit_value_dollar));
			   //  $this->db->set('issued_total_value', floatval($issued_unit_value_dollar));
			   //  $this->db->where('id', $ms['id']);
			   //  $this->db->update('tb_issuance_items'); 
		    // }

	    }

	    if ($this->db->trans_status() === FALSE)
      	return FALSE;

    	$this->db->trans_commit();
    	return TRUE;
  	}

  	public function update($id)
  	{
	    $this->db->trans_begin();

	    $this->db->set('kurs_dollar', strtoupper($this->input->post('kurs_dollar')));
	    $this->db->set('update_at', date('Y-m-d H:i:s'));
	    $this->db->set('update_by', config_item('auth_username'));
	    $this->db->where('id', $id);
	    $this->db->update('tb_master_kurs_dollar');

	    //update value rupiah di tb_stock_in_stores
	    $this->db->select('*');
	    $this->db->from('tb_stock_in_stores');
	    $this->db->where('kurs_dollar !=', '1');
	    $this->db->where('received_date', strtoupper($this->input->post('date')));
	    $query            = $this->db->get();
	    $stock_in_stores  = $query->result_array();

	    $kurs_baru = floatval($this->input->post('kurs_dollar'));

	    foreach ($stock_in_stores as $stock_detail) {
	    	$unit_value_dollar 		= floatval($stock_detail['unit_value'])/floatval($stock_detail['kurs_dollar']);
	    	$initial_unit_value_dollar = floatval($stock_detail['initial_unit_value'])/floatval($stock_detail['kurs_dollar']);
	      	$unit_value_dollar_1 = $unit_value_dollar;
	      	$initial_unit_value_dollar_1 = $initial_unit_value_dollar;
	      	$this->db->set('kurs_dollar', strtoupper($this->input->post('kurs_dollar')));
	      	$this->db->set('unit_value', floatval($unit_value_dollar_1)*floatval($kurs_baru));
	      	$this->db->set('initial_unit_value', floatval($initial_unit_value_dollar_1)*floatval($kurs_baru));
	      	$this->db->set('unit_value_dollar', floatval($unit_value_dollar));
	      	$this->db->set('initial_unit_value_dollar', floatval($initial_unit_value_dollar));
	      	$this->db->where('id', $stock_detail['id']);
	      	$this->db->update('tb_stock_in_stores');

	      	//update value rupiah di tb_receipts items
		    $this->db->select('*');
		    $this->db->from('tb_receipt_items');
		    // $this->db->where('kurs_dollar !=', '1');
		    $this->db->where('stock_in_stores_id', $stock_detail['id']);
		    $query            = $this->db->get();
		    $tb_receipt_items  = $query->result_array();

		    // $kurs_baru = floatval($this->input->post('kurs_dollar'));

		    foreach ($tb_receipt_items as $ri) {
		      $unit_value         = floatval($unit_value_dollar)*floatval($kurs_baru);
		      $total_unit_value   = floatval($unit_value)*floatval($ri['received_quantity']);
		      $received_unit_value_dollar = floatval($unit_value)/floatval($kurs_baru);
		      $this->db->set('kurs_dollar', floatval($this->input->post('kurs_dollar')));
		      $this->db->set('received_unit_value', floatval($unit_value));
		      $this->db->set('received_total_value', floatval($total_unit_value));
		      $this->db->set('received_unit_value_dollar', floatval($received_unit_value_dollar));
		      $this->db->set('received_total_value_dollar', floatval($received_unit_value_dollar)*floatval($ri['received_quantity']));
		      $this->db->where('id', $ri['id']);
		      $this->db->update('tb_receipt_items'); 
		    }

		    //update value dollar di tb_receipts items
		    $this->db->select('*');
		    $this->db->from('tb_issuance_items');
		    $this->db->where('stock_in_stores_id', $stock_detail['id']);
		    // $this->db->where('received_date_item', strtoupper($this->input->post('tanggal')));
		    $query            = $this->db->get();
		    $tb_issuance_items  = $query->result_array();

		    // $kurs_baru = floatval($this->input->post('kurs_dollar'));

		    foreach ($tb_issuance_items as $ms) {
			    $issued_unit_value         = floatval($unit_value_dollar_1)*floatval($kurs_baru);
			    $issued_total_value        = floatval($issued_unit_value)*floatval($ms['issued_quantity']);
			    // $this->db->set('kurs_dollar', floatval($this->input->post('kurs_dollar')));
			    $this->db->set('issued_unit_value', floatval($issued_unit_value));
			    $this->db->set('issued_total_value', floatval($issued_total_value));
			    $this->db->where('id', $ms['id']);
			    $this->db->update('tb_issuance_items'); 
		    }

		    $this->db->select('*');
		    $this->db->from('tb_stock_cards');
		    $this->db->where('stock_in_stores_id',$stock_detail['id']);

		    $query_tb_stock_cards   = $this->db->get();
		    $tb_stock_cards  		= $query_tb_stock_cards->result_array();

		    foreach ($tb_stock_cards as $card) {
		    	$unit_value         = floatval($unit_value_dollar)*floatval($kurs_baru);
		    	$this->db->set('unit_value', floatval($unit_value));
				$this->db->set('total_value', floatval($unit_value)*floatval($card['quantity']));
			    $this->db->where('id', $card['id']);
			    $this->db->update('tb_stock_cards');

		    }

	    }

	    //update value dollar di tb_stock_in_stores
	    $this->db->select('*');
	    $this->db->from('tb_stock_in_stores');
	    $this->db->where('kurs_dollar', '1');
	    $this->db->where('received_date', strtoupper($this->input->post('date')));
	    $query            = $this->db->get();
	    $stock_in_stores  = $query->result_array();

	    // $kurs_baru = floatval($this->input->post('kurs_dollar'));

	    foreach ($stock_in_stores as $stock_detail) {
	      	$unit_value_1 = $stock_detail['unit_value'];
	      	$initial_unit_value_1 = $stock_detail['initial_unit_value'];
	      	$this->db->set('unit_value_dollar', floatval($unit_value_1)/floatval($kurs_baru));
	      	$this->db->set('initial_unit_value_dollar', floatval($initial_unit_value_1)/floatval($kurs_baru));
	      	$this->db->where('id', $stock_detail['id']);
	      	$this->db->update('tb_stock_in_stores');

	      	//update value dollar di tb_receipts items
		    $this->db->select('*');
		    $this->db->from('tb_receipt_items');
		    $this->db->where('stock_in_stores_id', $stock_detail['id']);
		    // $this->db->where('received_date_item', strtoupper($this->input->post('tanggal')));
		    $query            = $this->db->get();
		    $tb_receipt_items  = $query->result_array();

		    // $kurs_baru = floatval($this->input->post('kurs_dollar'));

		    foreach ($tb_receipt_items as $ri) {
			    $received_unit_value_dollar         = floatval($ri['received_unit_value'])/floatval($kurs_baru);
			    $received_total_value_dollar        = floatval($unit_value)*floatval($ri['received_quantity']);
			    // $this->db->set('kurs_dollar', floatval($this->input->post('kurs_dollar')));
			    $this->db->set('received_unit_value_dollar', floatval($received_unit_value_dollar));
			    $this->db->set('received_total_value_dollar', floatval($received_total_value_dollar));
			    $this->db->where('id', $ri['id']);
			    $this->db->update('tb_receipt_items'); 
		    }

		    //update value dollar di tb_receipts items
		    // $this->db->select('*');
		    // $this->db->from('tb_issuance_items');
		    // $this->db->where('stock_in_stores_id', $stock_detail['id']);
		    // // $this->db->where('received_date_item', strtoupper($this->input->post('tanggal')));
		    // $query            = $this->db->get();
		    // $tb_issuance_items  = $query->result_array();

		    // // $kurs_baru = floatval($this->input->post('kurs_dollar'));

		    // foreach ($tb_issuance_items as $ms) {
			   //  $issued_unit_value         = floatval($unit_value_1)*floatval($kurs_baru);
			   //  $issued_total_value        = floatval($issued_unit_value)*floatval($ms['issued_quantity']);
			   //  // $this->db->set('kurs_dollar', floatval($this->input->post('kurs_dollar')));
			   //  $this->db->set('issued_unit_value', floatval($issued_unit_value_dollar));
			   //  $this->db->set('issued_total_value', floatval($issued_unit_value_dollar));
			   //  $this->db->where('id', $ms['id']);
			   //  $this->db->update('tb_issuance_items'); 
		    // }  
	    }


	    if ($this->db->trans_status() === FALSE)
	      return FALSE;

	    $this->db->trans_commit();
	    return TRUE;
  	}

  	public function delete()
  	{
	    $this->db->trans_begin();

	    $id = $this->input->post('id');

	    $this->db->where('id', $id);
	    $this->db->delete('tb_master_kurs_dollar');

	    if ($this->db->trans_status() === FALSE)
	      return FALSE;

	    $this->db->trans_commit();
	    return TRUE;
  	}
}
