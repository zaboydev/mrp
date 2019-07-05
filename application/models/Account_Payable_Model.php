<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account_Payable_Model extends MY_Model {
	public function __construct()
		{
			parent::__construct();
			//Do your magic here
		}	
	public function getSelectedColumns()
  {
    $return = array(
      'tb_hutang.id'                          => NULL,
      'tb_hutang.document_no'             => 'Document Number',
      'tb_hutang.tanggal'               => 'Tanggal',
      'tb_hutang.no_grn'                    => 'No GRN',
      'tb_hutang.vendor'                   => 'Vendor',
      // 'tb_stock_in_stores.stores'                   => 'Stores',
      'tb_hutang.amount'             => 'Amount',
      'tb_hutang.sisa'             => 'Sisa',
      'tb_hutang.status'   => 'Status',
    );
    return $return;
  }
  public function getSearchableColumns()
  {
    $return = array(
      'tb_hutang.document_no',
      'tb_hutang.tanggal',
      'tb_hutang.no_grn',
      'tb_hutang.vendor',
      'tb_hutang.amount',
      'tb_hutang.sisa',
      'tb_hutang.status',
    );

    return $return;
  }

  public function getOrderableColumns()
  {
    $return = array(
      null,
      'tb_hutang.document_no',
      'tb_hutang.tanggal',
      'tb_hutang.no_grn',
      'tb_hutang.vendor',
      'tb_hutang.amount',
      'tb_hutang.sisa',
      'tb_hutang.status',
    );
    return $return;
  }

  private function searchIndex()
  {
     $i = 0;
    foreach ($this->getSearchableColumns() as $item){
      if ($_POST['search']['value']){
        $term = strtoupper($_POST['search']['value']);

        if ($i === 0){
          $this->db->group_start();
          $this->db->like('UPPER('.$item.')', $term);
        } else {
          $this->db->or_like('UPPER('.$item.')', $term);
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
    $this->db->from('tb_hutang');
    $this->searchIndex();
    $column_order = $this->getOrderableColumns();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('id', 'desc');
    }

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
    $this->db->from('tb_hutang');
    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->from('tb_hutang');
    $query = $this->db->get();

    return $query->num_rows();
  }
  public function findById($id){
  	$this->db->where('id', $id);

    $query    = $this->db->get('tb_hutang');
    $receipt = $query->unbuffered_row('array');
    return $receipt;
  }
  public function urgent($id)
  {
    $this->db->where('id', $id);
    $this->db->set('status','urgent');
    return $this->db->update('tb_hutang');
  }
  
  public function getNotifRecipient(){
    $this->db->select('email');
    $this->db->from('tb_auth_users');
    $this->db->where('auth_level', 2);
    return $this->db->get('')->result();
  }
  
}

/* End of file Account_Payable_Model.php */
/* Location: ./application/models/Account_Payable_Model.php */