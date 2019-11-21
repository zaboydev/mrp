<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Usage_Jurnal_Model extends MY_MODEL
{
  public function __construct()
  {
    parent::__construct();
  }
  public function getSelectedColumns()
  {
    $return = array(
      'tb_jurnal.id as id'            => NULL,
      'tb_jurnal.no_jurnal'                       => 'No Jurnal',
      'tb_jurnal.vendor'                          => 'Vendor',
      'tb_jurnal.tanggal_jurnal'                  => 'Date',
      'tb_jurnal_detail.kode_rekening'            => 'Account Code',
      'tb_jurnal_detail.jenis_transaksi'          => 'Description',
      'sum(trs_debet) as debet'                   => 'Debet',
      'sum(trs_kredit) as kredit'                 => 'Kredit',
      'tb_jurnal.source'                => NULL
    );
    return $return;
  }

  public function getSearchableColumns()
  {
    $return = array(
      'tb_jurnal.tanggal_jurnal',
      'tb_jurnal.no_jurnal',
      // 'tb_jurnal.vendor',
      'tb_jurnal_detail.kode_rekening',
      'tb_jurnal_detail.jenis_transaksi',
      'sum(trs_kredit) as kredit',
      'sum(trs_debet) as debet',
    );

    return $return;
  }

  public function getGroupbyColumns()
  {
    $return = array(
      'tb_jurnal.tanggal_jurnal',
      'tb_jurnal.no_jurnal',
      'tb_jurnal.id',
      'tb_jurnal_detail.kode_rekening',
      'tb_jurnal_detail.jenis_transaksi',
      'tb_jurnal.source'
      // 'sum(trs_kredit) as kredit',
      // 'sum(trs_debet) as debet',
    );

    return $return;
  }

  public function getOrderableColumns()
  {
    $return = array(
      null,
      'tb_jurnal.tanggal_jurnal',
      'tb_jurnal.no_jurnal',
      // 'tb_jurnal.vendor',
      'tb_jurnal_detail.kode_rekening',
      'tb_jurnal_detail.jenis_transaksi',
      'sum(trs_kredit) as kredit',
      'sum(trs_debet) as debet',
    );
    return $return;
  }

  private function searchIndex()
  {
    if (!empty($_POST['columns'][1]['search']['value'])) {
      $search_received_date = $_POST['columns'][1]['search']['value'];
      $range_received_date  = explode(' ', $search_received_date);

      $this->db->where('tb_jurnal.tanggal_jurnal >= ', $range_received_date[0]);
      $this->db->where('tb_jurnal.tanggal_jurnal <= ', $range_received_date[1]);
    }else{
      $this->db->where('tb_jurnal.tanggal_jurnal >= ', date('Y-m-d'));
      $this->db->where('tb_jurnal.tanggal_jurnal <= ', date('Y-m-d'));
    }

    if (!empty($_POST['columns'][2]['search']['value'])) {
      $vendor = $_POST['columns'][2]['search']['value'];
      if ($vendor != 'all') {
        $this->db->where('tb_jurnal.vendor', $vendor);
      }      
    }

    if (!empty($_POST['columns'][3]['search']['value'])) {
      $tipe = $_POST['columns'][3]['search']['value'];
      if ($tipe != 'all') {
        if ($tipe == 'Purchase') {
          $this->db->where('tb_jurnal.source', 'INV-IN');
        }
        if ($tipe == 'Inventory') {
          $this->db->where('tb_jurnal.source', 'INV-OUT');
        }
        if ($tipe == 'Payment') {
          $this->db->where('tb_jurnal.source', 'AP');
        }
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
    $this->db->from('tb_jurnal');
    $this->db->join('tb_jurnal_detail', 'tb_jurnal.id = tb_jurnal_detail.id_jurnal');
    // $this->db->where('source', 'INV-OUT');
    $this->db->group_by($this->getGroupbyColumns());
    $this->searchIndex();
    $column_order = $this->getOrderableColumns();

    if (isset($_POST['order'])) {
      foreach ($_POST['order'] as $key => $order) {
        $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('tb_jurnal.id', 'desc');
    }
    // $this->db->group_by(array("tb_jurnal.tanggal_jurnal", "tb_jurnal_detail.kode_rekening"));
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
    $this->db->from('tb_jurnal');
    $this->db->join('tb_jurnal_detail', 'tb_jurnal.id = tb_jurnal_detail.id_jurnal');
    // $this->db->where('source', 'INV-OUT');

    $this->searchIndex();
    $this->db->group_by($this->getGroupbyColumns());
    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_jurnal');
    $this->db->join('tb_jurnal_detail', 'tb_jurnal.id = tb_jurnal_detail.id_jurnal');
    // $this->db->where('source', 'INV-OUT');
    $this->db->group_by($this->getGroupbyColumns());
    $query = $this->db->get();

    return $query->num_rows();
  }

  public function getSelectedColumnsTanggal()
  {
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
  
  private function searchIndexTanggal()
  {
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

  function getIndexTanggal($tanggal_jurnal, $kode_rekening, $return = 'array')
  {
    $this->db->select(array_keys($this->getSelectedColumnsTanggal()));
    $this->db->from('tb_jurnal');
    $this->db->join('tb_jurnal_detail', 'tb_jurnal.id = tb_jurnal_detail.id_jurnal');
    $this->db->where('tb_jurnal.tanggal_jurnal', $tanggal_jurnal);
    if ($kode_rekening != "") {
      $this->db->where('tb_jurnal_detail.kode_rekening', $kode_rekening);
    } else {
      $this->db->where('tb_jurnal_detail.kode_rekening is NULL', null, false);
    }
    $this->searchIndexTanggal();
    $column_order = $this->getOrderableColumnsTanggal();

    if (isset($_POST['order'])) {
      foreach ($_POST['order'] as $key => $order) {
        $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('tb_jurnal_detail.id', 'desc');
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
  
  function countIndexFilteredTanggal($tanggal_jurnal, $kode_rekening)
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
  
  public function countIndexTanggal($tanggal_jurnal, $kode_rekening)
  {
    $this->db->select(array_keys($this->getSelectedColumnsTanggal()));
    $this->db->from('tb_jurnal');
    $this->db->join('tb_jurnal_detail', 'tb_jurnal.id = tb_jurnal_detail.id_jurnal');
    $this->db->where('tb_jurnal.tanggal_jurnal', $tanggal_jurnal);
    $this->db->where('tb_jurnal_detail.kode_rekening', $kode_rekening);
    $query = $this->db->get();

    return $query->num_rows();
  }

  public function findById($id)
  {
    $this->db->where('id', $id);

    $query    = $this->db->get('tb_jurnal');
    $jurnal = $query->unbuffered_row('array');

    if($jurnal['source']=='INV-OUT'){
      $select = array(
        'tb_jurnal_detail.id as id_jurnal_detail',
        'tb_jurnal_detail.trs_kredit',
        'tb_jurnal_detail.trs_kredit_usd',
        'tb_jurnal_detail.id_jurnal',
        'tb_jurnal_detail.currency',
        'tb_jurnal_detail.stock_in_stores_id',
        'tb_jurnal_detail.kode_rekening_lawan as kode_pemakaian',
        'tb_stock_in_stores.unit_value',
        'tb_stock_in_stores.stores',
        'tb_stock_in_stores.warehouse',
        'tb_master_items.part_number',
        'tb_master_items.description',
        // 'tb_master_items.kode_pemakaian',
        'tb_master_items.serial_number',
        'tb_master_item_groups.coa',
        'tb_master_item_groups.group'
      );

      $this->db->select($select);
      $this->db->from('tb_jurnal_detail');
      $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id=tb_jurnal_detail.stock_in_stores_id');
      $this->db->join('tb_stocks', 'tb_stock_in_stores.stock_id=tb_stocks.id');
      $this->db->join('tb_master_items', 'tb_master_items.id=tb_stocks.item_id');
      $this->db->join('tb_master_item_groups', 'tb_master_items.group=tb_master_item_groups.group');
      $this->db->where('tb_jurnal_detail.id_jurnal', $jurnal['id']);
      $this->db->where('tb_jurnal_detail.trs_kredit > 0');
      $query = $this->db->get();

      foreach ($query->result_array() as $key => $value) {
        $jurnal['items'][$key] = $value;
      }
    }

    if ($jurnal['source'] == 'INV-IN') {
      $select = array(
        'tb_jurnal_detail.id as id_jurnal_detail',
        'tb_jurnal_detail.trs_debet',
        'tb_jurnal_detail.trs_debet_usd',
        'tb_jurnal_detail.id_jurnal',
        'tb_jurnal_detail.currency',
        'tb_jurnal_detail.stock_in_stores_id',
        'tb_jurnal_detail.kode_rekening_lawan as kode_pemakaian',
        'tb_stock_in_stores.unit_value',
        'tb_stock_in_stores.stores',
        'tb_stock_in_stores.warehouse',
        'tb_master_items.part_number',
        'tb_master_items.description',
        // 'tb_master_items.kode_pemakaian',
        'tb_master_items.serial_number',
        'tb_master_item_groups.coa',
        'tb_master_item_groups.group'
      );

      $this->db->select($select);
      $this->db->from('tb_jurnal_detail');
      $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id=tb_jurnal_detail.stock_in_stores_id');
      $this->db->join('tb_stocks', 'tb_stock_in_stores.stock_id=tb_stocks.id');
      $this->db->join('tb_master_items', 'tb_master_items.id=tb_stocks.item_id');
      $this->db->join('tb_master_item_groups', 'tb_master_items.group=tb_master_item_groups.group');
      $this->db->where('tb_jurnal_detail.id_jurnal', $jurnal['id']);
      $this->db->where('tb_jurnal_detail.trs_debet > 0');
      $query = $this->db->get();

      foreach ($query->result_array() as $key => $value) {
        $jurnal['items'][$key] = $value;
      }
    }

    if ($jurnal['source'] == 'INV-IN-WO') {
      $select = array(
        'tb_jurnal_detail.id as id_jurnal_detail',
        'tb_jurnal_detail.trs_debet',
        'tb_jurnal_detail.trs_debet_usd',
        'tb_jurnal_detail.id_jurnal',
        'tb_jurnal_detail.currency',
        // 'tb_jurnal_detail.stock_in_stores_id',
        'tb_jurnal_detail.kode_rekening_lawan as kode_pemakaian',
        // 'tb_stock_in_stores.unit_value',
        // 'tb_stock_in_stores.stores',
        // 'tb_stock_in_stores.warehouse',
        'tb_jurnal_detail.description as part_number',
        'tb_jurnal_detail.description',
        // 'tb_master_items.kode_pemakaian',
        // 'tb_master_items.serial_number',
        'tb_jurnal_detail.kode_rekening as coa',
        'tb_jurnal_detail.jenis_transaksi as group'
      );

      $this->db->select($select);
      $this->db->from('tb_jurnal_detail');
      // $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id=tb_jurnal_detail.stock_in_stores_id');
      // $this->db->join('tb_stocks', 'tb_stock_in_stores.stock_id=tb_stocks.id');
      // $this->db->join('tb_master_items', 'tb_master_items.id=tb_stocks.item_id');
      // $this->db->join('tb_master_item_groups', 'tb_master_items.group=tb_master_item_groups.group');
      $this->db->where('tb_jurnal_detail.id_jurnal', $jurnal['id']);
      $this->db->where('tb_jurnal_detail.trs_debet > 0');
      $query = $this->db->get();

      foreach ($query->result_array() as $key => $value) {
        $jurnal['items'][$key] = $value;
      }
    }

    return $jurnal;
  }

  public function save()
  {
    $this->db->trans_begin();
    if($_SESSION['jurnal_usage']['source']=='INV-OUT'){
      if (isset($_SESSION['jurnal_usage']['id'])) {
        $id_jurnal = $_SESSION['jurnal_usage']['id'];

        $this->db->where('id_jurnal', $id_jurnal);
        $this->db->delete('tb_jurnal_detail');
      }
      foreach ($_SESSION['jurnal_usage']['items'] as $key => $data) {

        $coa = $this->coaByGroup(strtoupper($data['group']));
        $this->db->set('id_jurnal', $id_jurnal);
        $this->db->set('jenis_transaksi', $data['group']);
        $this->db->set('trs_kredit', floatval($data['trs_kredit']));
        $this->db->set('trs_debet', 0);
        $this->db->set('trs_kredit_usd', floatval($data['trs_kredit_usd']));
        $this->db->set('trs_debet_usd', 0);
        $this->db->set('kode_rekening', $coa->coa);
        $this->db->set('stock_in_stores_id', $data['stock_in_stores_id']);
        $this->db->set('currency', $data['currency']);
        $this->db->set('kode_rekening_lawan', $data['kode_pemakaian']);
        $this->db->insert('tb_jurnal_detail');

        // $kode = $this->codeByDescription($stock_stored['stock_id']);
        $jenis_transaksi = $this->groupByKode($data['kode_pemakaian']);
        $this->db->set('id_jurnal', $id_jurnal);
        $this->db->set('jenis_transaksi', strtoupper($jenis_transaksi->group));
        $this->db->set('trs_debet', floatval($data['trs_kredit']));
        $this->db->set('trs_kredit', 0);
        $this->db->set('trs_debet_usd', floatval($data['trs_kredit_usd']));
        $this->db->set('trs_kredit_usd', 0);
        $this->db->set('kode_rekening', $data['kode_pemakaian']);
        $this->db->set('stock_in_stores_id', $data['stock_in_stores_id']);
        $this->db->set('currency', $data['currency']);
        $this->db->set('kode_rekening_lawan', $coa->coa);
        $this->db->insert('tb_jurnal_detail');
      }

    }
    if ($_SESSION['jurnal_usage']['source'] == 'INV-IN' || $_SESSION['jurnal_usage']['source'] == 'INV-IN-WO') {
      foreach ($_SESSION['jurnal_usage']['items'] as $key => $data) {
        $jenis_transaksi = $this->groupByKode($data['coa']);
        $this->db->set('jenis_transaksi', strtoupper($jenis_transaksi->group));
        $this->db->set('kode_rekening', $data['coa']);
        $this->db->where('id',$data['id_jurnal_detail']);
        $this->db->update('tb_jurnal_detail');
      }
    }
    


    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  function coaByGroup($group)
  {
    $this->db->select('coa');
    $this->db->from('tb_master_item_groups');
    $this->db->where('group', $group);
    return $this->db->get()->row();
  }

  function groupByKode($id)
  {
    $this->db->select('group');
    $this->db->from('tb_master_coa');
    $this->db->where('coa', $id);
    return $this->db->get()->row();
  }

  function itemIdByStockInStoresId($stock_in_stores_id){
    $this->db->select('id');
    $this->db->from('tb_master_items');
    $this->db->join('tb_stocks','tb_master_items.id=tb_stocks.item_id');
    $this->db->join('tb_stock_in_stores','tb_stocks.id=tb_stock_in_stores.stock_id');
    $this->db->where('tb_stock_in_stores.id', $stock_in_stores_id);
    return $this->db->get()->row();
  }
}

/* End of file Jurnal_Model.php */
/* Location: ./application/models/Jurnal_Model.php */
