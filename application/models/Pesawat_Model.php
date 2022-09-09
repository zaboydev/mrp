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
    $aircraft = $query->row_array();
    $aircraft['instrument_nf_array']  = explode(',', $aircraft['instrument_nf']);
    $aircraft['instrument_avionic_array']  = explode(',', $aircraft['instrument_avionic']);
    $aircraft['warehouse']  = findWarehouseByAlternateName($aircraft['base']);

    return $aircraft;
  }

  public function insert()
  {
    $this->db->trans_begin();

    $this->db->set('nama_pesawat', strtoupper($this->input->post('nama_pesawat')));
    $this->db->set('base', strtoupper($this->input->post('base')));
    $this->db->set('aircraft_serial_number', strtoupper($this->input->post('aircraft_serial_number')));
    $this->db->set('engine_serial_number', strtoupper($this->input->post('engine_serial_number')));
    $this->db->set('propeler_serial_number', strtoupper($this->input->post('engine_serial_number')));
    $this->db->set('fuel_capacity_usage', strtoupper($this->input->post('fuel_capacity_usage')));
    $this->db->set('fuel_capacity_mix', strtoupper($this->input->post('fuel_capacity_mix')));
    $this->db->set('instrument_nf', strtoupper($this->input->post('instrument_nf')));
    $this->db->set('instrument_avionic', strtoupper($this->input->post('instrument_avionic')));
    $this->db->set('date_of_manufacture', strtoupper($this->input->post('date_of_manufacture')));
    $this->db->set('keterangan', strtoupper($this->input->post('keterangan')));
    $this->db->set('engine_type', $this->input->post('engine_type'));
    if($this->input->post('engine_type')=='multi'){
      $this->db->set('engine_serial_number_2', strtoupper($this->input->post('engine_serial_number_2')));
      $this->db->set('propeler_serial_number_2', strtoupper($this->input->post('propeler_serial_number_2')));
    }   
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
    $instrument_nf = '';
    $instrument_nf_count = count($this->input->post('instrument_nf'));
    $nf = 1; 
    foreach ($this->input->post('instrument_nf') as $key => $instrument_nf_value){
      if($nf==$instrument_nf_count){
        $instrument_nf .= $instrument_nf_value;
      }else{
        $instrument_nf .= $instrument_nf_value.',';
      }
      $nf++;
    }

    $instrument_avionic = '';
    $instrument_avionic_count = count($this->input->post('instrument_avionic'));
    $avionic = 1; 
    foreach ($this->input->post('instrument_avionic') as $key => $instrument_avionic_value){
      if($avionic==$instrument_avionic_count){
        $instrument_avionic .= $instrument_avionic_value;
      }else{
        $instrument_avionic .= $instrument_avionic_value.',';
      }
      $avionic++;
    }

    $this->db->set('nama_pesawat', strtoupper($this->input->post('nama_pesawat')));
    $this->db->set('base', strtoupper($this->input->post('base')));
    $this->db->set('aircraft_serial_number', strtoupper($this->input->post('aircraft_serial_number')));
    $this->db->set('engine_serial_number', strtoupper($this->input->post('engine_serial_number')));
    $this->db->set('propeler_serial_number', strtoupper($this->input->post('engine_serial_number')));
    $this->db->set('engine_type', $this->input->post('engine_type'));
    if($this->input->post('engine_type')=='multi'){
      $this->db->set('engine_serial_number_2', strtoupper($this->input->post('engine_serial_number_2')));
      $this->db->set('propeler_serial_number_2', strtoupper($this->input->post('propeler_serial_number_2')));
    }  
    $this->db->set('fuel_capacity_usage', strtoupper($this->input->post('fuel_capacity_usage')));
    $this->db->set('fuel_capacity_mix', strtoupper($this->input->post('fuel_capacity_mix')));
    $this->db->set('instrument_nf', $instrument_nf);
    $this->db->set('instrument_avionic', $instrument_avionic);
    $this->db->set('date_of_manufacture', $this->input->post('date_of_manufacture'));
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

  function getIndexAircraftComponent($aircraft_id,$return = 'array')
  {
    $this->db->select(array_keys($this->getSelectedColumnsAircraftComponent()));
    $this->db->from('tb_aircraft_components');
    $this->db->where('tb_aircraft_components.aircraft_id',$aircraft_id);

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

  function countIndexFilteredAircraftComponent($aircraft_id)
  {
    $this->db->from('tb_aircraft_components');
    $this->db->where('tb_aircraft_components.aircraft_id',$aircraft_id);
    $this->searchIndexAircraftComponent();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndexAircraftComponent($aircraft_id)
  {
    $this->db->from('tb_aircraft_components');
    $this->db->where('tb_aircraft_components.aircraft_id',$aircraft_id);

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
    // $this->db->where('tb_issuances.warehouse', $_SESSION['component']['warehouse']);
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
      $item_id = $data['item_id'];
      $serial_number = (empty($data['serial_number'])) ? NULL : $data['serial_number'];
      if(empty($item_id)){
        if (isItemExists($data['part_number'], $data['description'], $serial_number) === FALSE) {
          $this->db->set('part_number', strtoupper($data['part_number']));
          $this->db->set('serial_number', strtoupper($data['serial_number']));
          $this->db->set('alternate_part_number', strtoupper($data['alternate_part_number']));
          $this->db->set('description', strtoupper($data['description']));
          $this->db->set('group', strtoupper($data['group']));
          $this->db->set('minimum_quantity', floatval(1));
          $this->db->set('kode_stok', null);
          $this->db->set('unit', strtoupper($data['unit']));
          $this->db->set('unit_pakai', strtoupper($data['unit']));
          $this->db->set('qty_konversi', 1);
          $this->db->set('created_by', config_item('auth_person_name'));
          $this->db->set('updated_by', config_item('auth_person_name'));
          $this->db->set('current_price', 1);
          $this->db->insert('tb_master_items');
          $item_id = $this->db->insert_id();
        }else{
          $item_id = getItemId($data['part_number'], $data['description'], $serial_number);
        }
      }

      if($data['previous_component_id']!=null){
        $this->db->set('active', false);
        $this->db->where('id',$data['previous_component_id']);
        $this->db->update('tb_aircraft_components');
      }
      $this->db->set('type', $type);
      $this->db->set('aircraft_id', $aircraft_id);
      $this->db->set('aircraft_code', $aircraft_code);
      $this->db->set('item_id', $item_id);
      $this->db->set('part_number', $data['part_number']);
      $this->db->set('description', $data['description']);
      $this->db->set('alternate_part_number', $data['alternate_part_number']);
      $this->db->set('serial_number', $serial_number);
      $this->db->set('interval', $data['interval']);
      $this->db->set('installation_date', $data['installation_date']);
      $this->db->set('installation_by', $installation_by);
      $this->db->set('af_tsn', $data['af_tsn']);
      $this->db->set('equip_tsn', $data['equip_tsn']);
      $this->db->set('tso', $data['tso']);
      $this->db->set('due_at_af_tsn', $data['due_at_af_tsn']);
      $this->db->set('remaining', $data['remaining']);
      $this->db->set('remarks', $data['remarks']);
      if(!empty($data['issuance_item_id'])){
        $this->db->set('issuance_item_id', $data['issued_item_id']);
      }      
      $this->db->set('issuance_document_number', $data['issuance_document_number']);
      $this->db->set('active', true);
      if(!empty($data['previous_component_id'])){
        $this->db->set('previous_component_id', $data['previous_component_id']);
      }
      $this->db->insert('tb_aircraft_components');


    }

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function searchItems()
  {
    $selected = array(
      'tb_master_items.*',
    );
    $this->db->select($selected);
    $this->db->from('tb_master_items');
    $this->db->order_by('tb_master_items.part_number','asc');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function infoItem($id)
  {
    $selected = array(
      'tb_master_items.*'
    );
    $this->db->select($selected);
    $this->db->from('tb_master_items');
    $this->db->where('tb_master_items.id', $id);
    $query = $this->db->get();
    return $query->unbuffered_row('array');
  }

}
