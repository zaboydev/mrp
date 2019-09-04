<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usage_Jurnal_Model extends MY_MODEL {
	public function __construct()
	{
		parent::__construct();
		
	}
	public function getSelectedColumns()
  {
    $return = array(
      'tb_jurnal.tanggal_jurnal as id'                          => NULL,
      'tb_jurnal.tanggal_jurnal'             => 'Tanggal',
      'tb_jurnal_detail.kode_rekening'               => 'Kode Rekening',
      'sum(trs_kredit) as kredit'                    => 'Kredit',
      'sum(trs_debet) as debet'                   => 'Debet',
  );
    return $return;
  }
  public function getSelectedColumnsTanggal(){
    $return = array(
      'tb_jurnal_detail.id'                          => NULL,
      'tb_jurnal.no_jurnal'                          => 'No Jurnal',
      'tb_jurnal.grn_no'                             => 'No MS',
      'tb_jurnal_detail.jenis_transaksi'                    => 'Jenis Transaksi',
      'tb_jurnal_detail.trs_kredit'                   => 'Kredit',
      'tb_jurnal_detail.trs_debet'                   => 'Debet',
  );
    return $return;
  }
  public function getSearchableColumns()
  {
    $return = array(
      'tb_jurnal.tanggal_jurnal',
      'tb_jurnal_detail.kode_rekening',
      'sum(trs_kredit) as kredit',
      'sum(trs_debet) as debet',
    );

    return $return;
  }
  public function getSearchableColumnsTanggal()
  {
    $return = array(
       'tb_jurnal.no_jurnal',
      'tb_jurnal.grn_no',
      'tb_jurnal.vendor',
      'tb_jurnal_detail.jenis_transaksi',
      'tb_jurnal_detail.trs_kredit',
      'tb_jurnal_detail.trs_debet',
    );

    return $return;
  }
  public function getOrderableColumns()
  {
    $return = array(
      null,
      'tb_jurnal.tanggal_jurnal',
      'tb_jurnal_detail.kode_rekening',
      'sum(trs_kredit) as kredit',
      'sum(trs_debet) as debet',
    );
    return $return;
  }
  public function getOrderableColumnsTanggal()
  {
    $return = array(
      null,
       'tb_jurnal.no_jurnal',
      'tb_jurnal.grn_no',
      'tb_jurnal.vendor',
      'tb_jurnal_detail.jenis_transaksi',
      'tb_jurnal_detail.trs_kredit',
      'tb_jurnal_detail.trs_debet',
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
   private function searchIndexTanggal()
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
    $this->db->from('tb_jurnal');
    $this->db->join('tb_jurnal_detail', 'tb_jurnal.id = tb_jurnal_detail.id_jurnal');
    $this->db->where('tb_jurnal.source', "INV-OUT");
    $this->searchIndex();
    $column_order = $this->getOrderableColumns();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('tb_jurnal.tanggal_jurnal', 'desc');
    }
    $this->db->group_by(array("tb_jurnal.tanggal_jurnal", "tb_jurnal_detail.kode_rekening"));
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
  function getIndexTanggal($tanggal_jurnal,$kode_rekening,$return = 'array')
  {
    $this->db->select(array_keys($this->getSelectedColumnsTanggal()));
    $this->db->from('tb_jurnal');
    $this->db->join('tb_jurnal_detail', 'tb_jurnal.id = tb_jurnal_detail.id_jurnal');
    $this->db->where('tb_jurnal.tanggal_jurnal', $tanggal_jurnal);
    if($kode_rekening != ""){
      $this->db->where('tb_jurnal_detail.kode_rekening', $kode_rekening);  
    } else {
      $this->db->where('tb_jurnal_detail.kode_rekening is NULL', null,false);  
    }
    $this->searchIndexTanggal();
    $column_order = $this->getOrderableColumnsTanggal();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('tb_jurnal_detail.id', 'desc');
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
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_jurnal');
    $this->db->join('tb_jurnal_detail', 'tb_jurnal.id = tb_jurnal_detail.id_jurnal');
    $this->db->where('tb_jurnal.source', "INV-OUT");

    $this->searchIndex();
    $this->db->group_by(array("tb_jurnal.tanggal_jurnal", "tb_jurnal_detail.kode_rekening"));
    $query = $this->db->get();

    return $query->num_rows();
  }
  function countIndexFilteredTanggal($tanggal_jurnal,$kode_rekening)
  {
    $this->db->select(array_keys($this->getSelectedColumnsTanggal()));
    $this->db->from('tb_jurnal');
    $this->db->join('tb_jurnal_detail', 'tb_jurnal.id = tb_jurnal_detail.id_jurnal');
    $this->db->where('tb_jurnal.tanggal_jurnal', $tanggal_jurnal);
    $this->db->where('tb_jurnal_detail.kode_rekening', $kode_rekening);
    $this->searchIndexTanggal();
    $query = $this->db->get();

    return $query->num_rows();
  }
  public function countIndex()
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_jurnal');
    $this->db->join('tb_jurnal_detail', 'tb_jurnal.id = tb_jurnal_detail.id_jurnal');
    $this->db->where('tb_jurnal.source', "INV-OUT");
    $this->db->group_by(array("tb_jurnal.tanggal_jurnal", "tb_jurnal_detail.kode_rekening"));
    $query = $this->db->get();

    return $query->num_rows();
  }
  public function countIndexTanggal($tanggal_jurnal,$kode_rekening)
  {
     $this->db->select(array_keys($this->getSelectedColumnsTanggal()));
    $this->db->from('tb_jurnal');
    $this->db->join('tb_jurnal_detail', 'tb_jurnal.id = tb_jurnal_detail.id_jurnal');
    $this->db->where('tb_jurnal.tanggal_jurnal', $tanggal_jurnal);
    $this->db->where('tb_jurnal_detail.kode_rekening', $kode_rekening);
    $query = $this->db->get();

    return $query->num_rows();
  }
  public function findById($id){
  	$this->db->where('id', $id);

    $query    = $this->db->get('tb_hutang');
    $receipt = $query->unbuffered_row('array');

    $select = array(
      'tb_jurnal.*'
    );

    $this->db->select($select);
    $this->db->from('tb_jurnal');
    $this->db->where('tb_jurnal.grn_no', $receipt['no_grn']);
    $query = $this->db->get()->unbuffered_row('array');
    $receipt['id_jurnal'] = $query['id'];
    $receipt['no_jurnal'] = $query['no_jurnal'];
    $receipt['tanggal_jurnal'] = $query['tanggal_jurnal'];
    $receipt['keterangan'] = $query['keterangan'];
    $this->db->select("tb_jurnal_detail.*");
    $this->db->from('tb_jurnal_detail');
    $this->db->where('id_jurnal',$receipt['id_jurnal']);
    
    $jurnal_detail = $this->db->get()->result();
    $receipt['item'] = $jurnal_detail;
    return $receipt;
  }

}

/* End of file Jurnal_Model.php */
/* Location: ./application/models/Jurnal_Model.php */
