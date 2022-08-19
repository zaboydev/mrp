<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pesawat_Model extends MY_Model
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = config_item('module')['pesawat'];
  }

  public function getSelectedColumns()
  {
    return array(
      'id'                => NULL,
      'nama_pesawat'      => 'Aircraft Code',
      'keterangan'        => 'Description',
      'base'              => 'Base',
      'created_by'        => 'Action'
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'nama_pesawat',
      'keterangan',
      'base',
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'nama_pesawat',
      'keterangan',
      'base',
    );
  }

  private function searchIndex()
  {
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
    $this->db->from('tb_master_pesawat');

    $this->searchIndex();

    $column_order = $this->getOrderableColumns();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('nama_pesawat','asc');
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
    $this->db->from('tb_master_pesawat');
    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->from('tb_master_pesawat');

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function findById($id)
  {
    $this->db->where('id', $id);
    $query = $this->db->get('tb_master_pesawat');

    return $query->row_array();
  }

  public function insert()
  {
    $this->db->trans_begin();

    $this->db->set('nama_pesawat', strtoupper($this->input->post('nama_pesawat')));
    $this->db->set('keterangan', strtoupper($this->input->post('keterangan')));
    $this->db->set('created_by', config_item('auth_username'));
    $this->db->set('updated_by', config_item('auth_username'));
    $this->db->set('crreated_at', date('Y-m-d H:i:s'));
    $this->db->set('updated_at', date('Y-m-d H:i:s'));
    $this->db->insert('tb_master_pesawat');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function update($id)
  {
    $this->db->trans_begin();

    $this->db->set('nama_pesawat', strtoupper($this->input->post('nama_pesawat')));
    $this->db->set('keterangan', strtoupper($this->input->post('keterangan')));
    $this->db->set('updated_at', date('Y-m-d H:i:s'));
    $this->db->set('updated_by', config_item('auth_username'));
    $this->db->where('id', $id);
    $this->db->update('tb_master_pesawat');


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
    $this->db->delete('tb_master_pesawat');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function getSelectedColumnsAircraftComponent()
  {
    return array(
      'id'                    => NULL,
      'type'                  => 'Type',
      'description'           => 'Description',
      'part_number'           => 'Part Number',
      'alternate_part_number' => 'Alt. Part Number',
      'serial_number'         => 'Serial Number',
      'interval'              => 'Interval',
      'installation_date'     => 'Installation Date',
      'installation_by'       => 'Installation By',
      'af_tsn'                => 'AF TSN',
      'equip_tsn'             => 'EQUIP TSN',
      'tso'                   => 'TSO',
      'due_at_af_tsn'         => 'Due at AF TSN',
      'remaining'             => 'Remaining',
      'remarks'               => 'Remarks',
    );
  }

  public function getSearchableColumnsAircraftComponent()
  {
    return array(
      'type',
      'description',
      'part_number',
      'alternate_part_number',
      'serial_number',
      // 'interval',
      // 'installation_date',
      'installation_by',
      // 'af_tsn',
      // 'equip_tsn',
      // 'tso',
      // 'due_at_af_tsn',
      // 'remaining',
      // 'remarks',
    );
  }

  public function getOrderableColumnsAircraftComponent()
  {
    return array(
      null,
      'type',
      'description',
      'part_number',
      'alternate_part_number',
      'serial_number',
      'interval',
      'installation_date',
      'installation_by',
      'af_tsn',
      'equip_tsn',
      'tso',
      'due_at_af_tsn',
      'remaining',
      'remarks',
    );
  }

  private function searchIndexAircraftComponent()
  {
    $i = 0;

    foreach ($this->getSearchableColumnsAircraftComponent() as $item){
      if ($_POST['search']['value']){
        if ($i === 0){
          $this->db->group_start();
          $this->db->like('UPPER('.$item.')', strtoupper($_POST['search']['value']));
        } else {
          $this->db->or_like('UPPER('.$item.')', strtoupper($_POST['search']['value']));
        }

        if (count($this->getSearchableColumnsAircraftComponent()) - 1 == $i){
          $this->db->group_end();
        }
      }

      $i++;
    }
  }

  function getIndexAircraftComponent($return = 'array')
  {
    $this->db->select(array_keys($this->getSelectedColumnsAircraftComponent()));
    $this->db->from('tb_aircraft_components');

    $this->searchIndexAircraftComponent();

    $column_order = $this->getOrderableColumnsAircraftComponent();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('type','asc');
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

  function countIndexFilteredAircraftComponent()
  {
    $this->db->from('tb_aircraft_components');
    $this->searchIndexAircraftComponent();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndexAircraftComponent()
  {
    $this->db->from('tb_aircraft_components');

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function searchIssuanceItems()
  {
    $selected = array(
      'tb_issuance_items.id',
      'tb_issuances.document_number',
      'tb_issuances.issued_date',
      'tb_issuances.issued_to',
      'tb_issuances.category',
      'tb_issuances.warehouse',
      'tb_master_items.description',
      'tb_master_items.id as item_id',
      'tb_master_items.part_number',
      'tb_master_items.alternate_part_number',
      'tb_master_items.serial_number',
      'tb_master_items.group',
      'tb_stocks.condition',
      'tb_issuance_items.issued_quantity',
      'tb_master_items.unit',
      'tb_master_item_groups.coa',
      'tb_master_items.kode_stok',
      'tb_issuance_items.remarks',
      'tb_issuances.issued_to',
      'tb_issuances.issued_by',
      'tb_issuances.required_by',
      'tb_issuances.requisition_reference',
      'tb_issuances.notes',
    );
    $this->db->select($selected);
    $this->db->from('tb_issuance_items');
    $this->db->join('tb_issuances', 'tb_issuance_items.document_number = tb_issuances.document_number');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_issuance_items.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->like('tb_issuances.document_number', 'MS');
    $this->db->where_not_in('tb_issuances.category', ['BAHAN BAKAR']);
    $this->db->where('tb_issuances.warehouse', $_SESSION['component']['base']);
    $this->db->where('tb_issuances.issued_to', $_SESSION['component']['aircraft_code']);
    $this->db->order_by('tb_issuances.issued_date','desc');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function infoIssuanceItem($issuance_item_id)
  {
    $selected = array(
      'tb_issuance_items.id',
      'tb_issuances.document_number',
      'tb_issuances.issued_date',
      'tb_issuances.issued_to',
      'tb_issuances.category',
      'tb_issuances.warehouse',
      'tb_master_items.description',
      'tb_master_items.id as item_id',
      'tb_master_items.part_number',
      'tb_master_items.alternate_part_number',
      'tb_master_items.serial_number',
      'tb_master_items.group',
      'tb_stocks.condition',
      'tb_issuance_items.issued_quantity',
      'tb_master_items.unit',
      'tb_master_item_groups.coa',
      'tb_master_items.kode_stok',
      'tb_issuance_items.remarks',
      'tb_issuances.issued_to',
      'tb_issuances.issued_by',
      'tb_issuances.required_by',
      'tb_issuances.requisition_reference',
      'tb_issuances.notes',
    );
    $this->db->select($selected);
    $this->db->from('tb_issuance_items');
    $this->db->join('tb_issuances', 'tb_issuance_items.document_number = tb_issuances.document_number');
    $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_issuance_items.stock_in_stores_id');
    $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('tb_issuance_items.id', $issuance_item_id);
    $query = $this->db->get();
    return $query->unbuffered_row('array');
  }

  public function save_component()
  {
    $this->db->trans_begin();

    $installation_date            = $_SESSION['component']['installation_date'];
    $aircraft_id                  = $_SESSION['component']['aircraft_id'];
    $aircraft_code                = $_SESSION['component']['aircraft_code'];
    $type                         = $_SESSION['component']['type'];
    $installation_by              = $_SESSION['component']['installation_by'];
    $base                         = $_SESSION['component']['base'];

    foreach ($_SESSION['component']['items'] as $key => $data) {
      $this->db->set('type', $type);
      $this->db->set('aircraft_id', $aircraft_id);
      $this->db->set('aircraft_code', $aircraft_code);
      $this->db->set('item_id', $data['item_id']);
      $this->db->set('part_number', $data['part_number']);
      $this->db->set('description', $data['description']);
      $this->db->set('alternate_part_number', $data['alternate_part_number']);
      $this->db->set('serial_number', $data['serial_number']);
      $this->db->set('interval', $data['interval']);
      $this->db->set('installation_date', $data['installation_date']);
      $this->db->set('installation_by', $installation_by);
      $this->db->set('af_tsn', $data['af_tsn']);
      $this->db->set('equip_tsn', $data['equip_tsn']);
      $this->db->set('tso', $data['tso']);
      $this->db->set('due_at_af_tsn', $data['due_at_af_tsn']);
      $this->db->set('remaining', $data['remaining']);
      $this->db->set('remarks', $data['remarks']);
      $this->db->set('issuance_item_id', $data['issued_item_id']);
      $this->db->set('issuance_document_number', $data['issuance_document_number']);
      $this->db->set('active', true);
      $this->db->insert('tb_aircraft_components');
    }

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

}
