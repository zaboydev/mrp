<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Aircraft_Movement_Part_Model extends MY_Model
{
    protected $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = config_item('module')['aircraft_movement_part'];
    }

    public function getSelectedColumnsRemovePart(){
        return array(
            'Date of AJLB',
            'A/C Hour TTIS',
            'A/C Reg',
            'A/C Type',
            'Base Station',
            'Remove Date',
            'Remove TSN',
            'Remove TSO',
            'PIC',
            'Description Part',
            'Part Number Off',
            'Serial Number Off',
            'Part TSN',
            'Part TSO',
            'Category',
            'Status',
            'Remarks'
        );
    }

    public function getSelectedColumnsInstallRemovePart(){
        return array(
            'Date of AJLB',
            'A/C Hour TTIS',
            'A/C Reg',
            'A/C Type',
            'Base Station',
            'Install Date',
            'Install TSN',
            'Install TSO',            
            'Description Part',
            'Part Number On',
            'Serial Number On',
            'Remove Date',
            'Remove TSN',
            'Remove TSO',
            'PIC',
            'Part Number Off',
            'Serial Number Off',
            'Category',
            'Remarks'
        );
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

        $this->db->from('tb_aircraft_components');
        $this->db->where('tb_aircraft_components.aircraft_id', $id);
        $query = $this->db->get();

        return $aircraft;
    }

    public function findByComponentPesawatByAircraftId($id,$type=NULL)
    {
        $selected = array(
        'tb_aircraft_components.*'
        );
        $this->db->select($selected);
        // $this->db->select(array_keys($this->getSelectedColumnsAircraftComponent()));
        $this->db->from('tb_aircraft_components');
        $this->db->where('tb_aircraft_components.aircraft_id',$id);
        $this->db->where('tb_aircraft_components.active','t');
        if($type!=NULL){      
        $this->db->where('tb_aircraft_components.type',$type);
        }
        $query = $this->db->get();
        $component = array();
        foreach ($query->result_array() as $key => $value) {
        $component[$key] = $value;
        }

        return $component;
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
        $this->db->set('type', strtoupper($this->input->post('aircraft_type')));
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
        if($instrument_nf_count){
        foreach ($this->input->post('instrument_nf') as $key => $instrument_nf_value){
            if($nf==$instrument_nf_count){
            $instrument_nf .= $instrument_nf_value;
            }else{
            $instrument_nf .= $instrument_nf_value.',';
            }
            $nf++;
        }
        }
        

        $instrument_avionic = '';
        $instrument_avionic_count = count($this->input->post('instrument_avionic'));
        $avionic = 1; 
        if($instrument_avionic_count){
        foreach ($this->input->post('instrument_avionic') as $key => $instrument_avionic_value){
            if($avionic==$instrument_avionic_count){
            $instrument_avionic .= $instrument_avionic_value;
            }else{
            $instrument_avionic .= $instrument_avionic_value.',';
            }
            $avionic++;
        }
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
        $this->db->set('type', strtoupper($this->input->post('aircraft_type')));
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
        'interval'              => 'Interval (Hour & Date)',
        'historical'              => 'Historical',
        'installation_date'     => 'Installation Date',
        'installation_by'       => 'Installation By',
        'af_tsn'                => 'AF TSN',
        'equip_tsn'             => 'EQUIP TSN',
        'tso'                   => 'TSO',
        'due_at_af_tsn'         => 'Due at AF TSN (Hour & Date)',
        'remaining'             => 'Remaining (Hour & Date)',
        'remarks'               => 'Remarks',
        'updated_at'               => 'Last Updated at',
        'updated_by'               => 'Last Updated By',
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
        // 'interval',
        // 'installation_date',
        // 'installation_by',
        // 'af_tsn',
        // 'equip_tsn',
        // 'tso',
        // 'due_at_af_tsn',
        // 'remaining',
        // 'remarks',
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
        $selected = array(
        'tb_aircraft_components.*'
        );
        $this->db->select($selected);
        // $this->db->select(array_keys($this->getSelectedColumnsAircraftComponent()));
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
        $this->db->set('interval_satuan', $data['interval_satuan']);
        $this->db->set('installation_date', $data['installation_date']);
        $this->db->set('historical', $data['historical']);
        $this->db->set('installation_by', $installation_by);
        $this->db->set('af_tsn', $data['af_tsn']);
        $this->db->set('equip_tsn', $data['equip_tsn']);
        $this->db->set('tso', $data['tso']);
        // $this->db->set('due_at_af_tsn', NULL);
        // $this->db->set('remaining', NULL);
        $this->db->set('remarks', $data['remarks']);
        if(!empty($data['next_due_date'])){
            $this->db->set('next_due_date', $data['next_due_date']);
        }      
        $this->db->set('next_due_hour', $data['next_due_hour']);
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

    public function findAircraftComponetByAircraftId($aircraft_id)
    {
        $this->db->where('aircraft_id', $aircraft_id);
        $this->db->where('active',true);
        $query = $this->db->get('tb_aircraft_components');
        $aircraft_components = $query->result_array();

        return $aircraft_components;
    }

    public function getDocumentNumberComponentStatus()
    {
        $format = date('Ymd');
        $this->db->select_max('document_number', 'last_number');
        $this->db->like('tb_aircraft_component_status.document_number', $format, 'after');
        $this->db->from('tb_aircraft_component_status');

        $query  = $this->db->get();
        $row    = $query->unbuffered_row();
        $last   = $row->last_number;
        $number = substr($last, 3, 6);
        $next   = $number + 1;
        $return = sprintf('%03s', $next);

        return $format.$return;
    }

    public function saveComponentStatus()
    {
        $this->db->trans_begin();

        $status_date  = $this->input->post('status_date');
        $prepared_by  = $this->input->post('prepared_by');
        $aircraft_id  = $this->input->post('aircraft_id');
        $base         = $this->input->post('base');
        $tsn          = $this->input->post('tsn');
        $notes          = $this->input->post('notes');
        $document_number = $this->getDocumentNumberComponentStatus();

        $this->db->set('document_number', $document_number);
        $this->db->set('status_date', $status_date);
        $this->db->set('prepared_by', $prepared_by);
        $this->db->set('aircraft_id', $aircraft_id);
        $this->db->set('tsn', $tsn);
        $this->db->set('status', 'WAITING FOR CHECKED BY COM');
        $this->db->set('base', $base);
        $this->db->set('notes', $notes);
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->insert('tb_aircraft_component_status');
        $aircraft_component_status_id = $this->db->insert_id();

        foreach ($_POST['items'] as $id => $data){
        $this->db->set('aircraft_component_status_id', $aircraft_component_status_id);
        $this->db->set('aircraft_component_id', $data['aircraft_component_id']);
        $this->db->set('interval', $data['interval']);
        $this->db->set('interval_date', $data['interval_date']);
        $this->db->set('af_tsn', $data['af_tsn']);
        $this->db->set('equip_tsn', $data['equip_tsn']);
        $this->db->set('tso', $data['tso']);
        $this->db->set('due_at_af_tsn', $data['due_at_af_tsn']);
        $this->db->set('due_at_af_tsn_date', $data['due_at_af_tsn_date']);
        $this->db->set('remaining', $data['remaining']);
        $this->db->set('remaining_date', $data['remaining_date']);
        $this->db->set('remarks', $data['remarks']);
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->insert('tb_aircraft_component_status_details');

        // $this->db->set('interval', $data['interval']);
        // $this->db->set('interval_date', $data['interval_date']);
        // $this->db->set('af_tsn', $data['af_tsn']);
        // $this->db->set('equip_tsn', $data['equip_tsn']);
        // $this->db->set('tso', $data['tso']);
        // $this->db->set('due_at_af_tsn', $data['due_at_af_tsn']);
        // $this->db->set('due_at_af_tsn_date', $data['due_at_af_tsn_date']);
        // $this->db->set('remaining', $data['remaining']);
        // $this->db->set('remaining_date', $data['remaining_date']);
        // $this->db->set('remarks', $data['remarks']);
        // $this->db->set('updated_by', config_item('auth_person_name'));
        // $this->db->set('updated_at', date('Y-m-d H:i:s'));
        // $this->db->where('id',$data['aircraft_component_id']);
        // $this->db->update('tb_aircraft_components');
        }

        if ($this->db->trans_status() === FALSE)
        return FALSE;

        $this->db->trans_commit();

        $this->send_mail($aircraft_component_status_id);

        return TRUE;

    }

    public function getSelectedColumnsComponentStatus()
    {
        return array(
        'tb_aircraft_component_status.id'                   => NULL,
        'tb_aircraft_component_status.status'               => 'Status',
        'tb_master_pesawat.nama_pesawat'                    => 'Aircraft Code',
        'tb_aircraft_component_status.base'                 => 'Base',
        'tb_aircraft_component_status.status_date'          => 'Status Date',
        'tb_aircraft_component_status.tsn'                  => 'TSN',
        'tb_aircraft_component_status.notes'                => 'Notes',
        'tb_aircraft_component_status.prepared_by'          => 'Prepared By',
        'tb_aircraft_component_status.approval_notes'       => 'Approval Notes',
        );
    }

    public function getSearchableColumnsComponentStatus()
    {
        return array(
        // 'tb_aircraft_component_status.id',
        'tb_aircraft_component_status.status',
        'tb_master_pesawat.nama_pesawat',
        'tb_aircraft_component_status.base',
        // 'tb_aircraft_component_status.status_date',
        // 'tb_aircraft_component_status.tsn',
        'tb_aircraft_component_status.notes',
        'tb_aircraft_component_status.prepared_by',
        );
    }

    public function getOrderableColumnsComponentStatus()
    {
        return array(
        null,
        'tb_aircraft_component_status.status',
        'tb_master_pesawat.nama_pesawat',
        'tb_aircraft_component_status.base',
        'tb_aircraft_component_status.status_date',
        'tb_aircraft_component_status.tsn',
        'tb_aircraft_component_status.notes',
        'tb_aircraft_component_status.prepared_by',
        );
    }

    private function searchIndexComponentStatus()
    {

        if (!empty($_POST['columns'][1]['search']['value'])){
        $search_required_date = $_POST['columns'][1]['search']['value'];
        $range_date  = explode(' ', $search_required_date);

        $this->db->where('tb_aircraft_component_status.status_date >= ', $range_date[0]);
        $this->db->where('tb_aircraft_component_status.status_date <= ', $range_date[1]);
        }

        if (!empty($_POST['columns'][2]['search']['value'])){
        $aircraft_id = $_POST['columns'][2]['search']['value'];
        if($aircraft_id!='all'){
            $this->db->where('tb_aircraft_component_status.aircraft_id', $aircraft_id);
        }            
        }
        $i = 0;

        foreach ($this->getSearchableColumnsComponentStatus() as $item){
        if ($_POST['search']['value']){
            if ($i === 0){
            $this->db->group_start();
            $this->db->like('UPPER('.$item.')', strtoupper($_POST['search']['value']));
            } else {
            $this->db->or_like('UPPER('.$item.')', strtoupper($_POST['search']['value']));
            }

            if (count($this->getSearchableColumnsComponentStatus()) - 1 == $i){
            $this->db->group_end();
            }
        }

        $i++;
        }
    }

    function getIndexComponentStatus($return = 'array')
    {
        $this->db->select(array_keys($this->getSelectedColumnsComponentStatus()));
        $this->db->from('tb_aircraft_component_status');
        $this->db->join('tb_master_pesawat', 'tb_master_pesawat.id = tb_aircraft_component_status.aircraft_id');

        $this->searchIndexComponentStatus();

        $column_order = $this->getOrderableColumnsComponentStatus();

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

    function countIndexFilteredComponentStatus()
    {
        $this->db->from('tb_aircraft_component_status');
        $this->db->join('tb_master_pesawat', 'tb_master_pesawat.id = tb_aircraft_component_status.aircraft_id');
        $this->searchIndex();

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function countIndexComponentStatus()
    {
        $this->db->from('tb_aircraft_component_status');
        $this->db->join('tb_master_pesawat', 'tb_master_pesawat.id = tb_aircraft_component_status.aircraft_id');

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function approve($id,$notes)
    {
        $this->db->trans_begin();

        $this->db->set('status', 'APPROVED');
        $this->db->set('approval_notes', $notes);
        $this->db->set('checked_by', config_item('auth_person_name'));
        $this->db->set('checked_at', date('Y-m-d'));
        $this->db->where('id', $id);
        $this->db->update('tb_aircraft_component_status');

        $this->db->from('tb_aircraft_component_status_details');
        $this->db->where('tb_aircraft_component_status_details.aircraft_component_status_id', $id);

        $query = $this->db->get();

        foreach ($query->result_array() as $key => $value) {
        $this->db->set('interval', $value['interval']);
        $this->db->set('interval_date', $value['interval_date']);
        $this->db->set('af_tsn', $value['af_tsn']);
        $this->db->set('equip_tsn', $value['equip_tsn']);
        $this->db->set('tso', $value['tso']);
        $this->db->set('due_at_af_tsn', $value['due_at_af_tsn']);
        $this->db->set('due_at_af_tsn_date', $value['due_at_af_tsn_date']);
        $this->db->set('remaining', $value['remaining']);
        $this->db->set('remaining_date', $value['remaining_date']);
        $this->db->set('remarks', $value['remarks']);
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->set('updated_at', date('Y-m-d H:i:s'));
        $this->db->where('id',$value['aircraft_component_id']);
        $this->db->update('tb_aircraft_components');
        }

        if ($this->db->trans_status() === FALSE)
        return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function send_mail($doc_id)
    {
        $this->db->from('tb_aircraft_component_status');
        $this->db->join('tb_master_pesawat', 'tb_master_pesawat.id = tb_aircraft_component_status.aircraft_id');
        $this->db->where('id', $doc_id);
        $query = $this->db->get();
        $row = $query->unbuffered_row('array');

        $recipientList = $this->getNotifRecipient(9);
        $recipient = array();
        foreach ($recipientList as $key) {
        array_push($recipient, $key->email);
        }

        $from_email = "bifa.acd@gmail.com";
        $to_email = "aidanurul99@rocketmail.com";

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
        $message = "<p>Dear Chief Of Maintenance</p>";
        $message .= "<p>Berikut permintaan Pengecekan untuk Laporan Aircraft Component Status :</p>";
        $message .= "<ul>";
        $message .= "</ul>";
        $message .= "<p>Document Number : " . $row['document_number'] . "</p>";
        $message .= "<p>Aircrat Reg No : " . $row['nama_pesawat'] . "</p>";
        $message .= "<p>Silakan klik link dibawah ini untuk menuju list approval</p>";
        $message .= "<p>[ <a href='".$this->config->item('url_mrp')."' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
        $message .= "<p>Thanks and regards</p>";
        $this->email->from($from_email, 'Material Resource Planning');
        $this->email->to($recipient);
        $this->email->subject('Permintaan Approval Laporan Aircraft Component Status No : ' . $row['document_number']).' Aircraft Reg. No : '. $row['nama_pesawat'];
        $this->email->message($message);

        //Send mail 
        if ($this->email->send())
        return true;
        else
        return $this->email->print_debugger();
    }

    public function getNotifRecipient($level)
    {
        $this->db->select('email');
        $this->db->from('tb_auth_users');
        $this->db->where('auth_level', $level);
        return $this->db->get('')->result();
    }

    public function send_mail_approval($id, $ket, $by, $notes)
    {
            $item_message = '<tbody>';
            $x = 0;
        foreach ($id as $key) {
        $this->db->from('tb_aircraft_component_status');
        $this->db->join('tb_master_pesawat', 'tb_master_pesawat.id = tb_aircraft_component_status.aircraft_id');
        $this->db->where('id', $key);
        $query = $this->db->get();
        $row = $query->unbuffered_row('array');
                
        $item_message .= "<tr>";
        $item_message .= "<td>" . $row['document_number'] . "</td>";
                $item_message .= "<td>" . $row['nama_pesawat'] . "</td>";
                $item_message .= "<td>" . $notes[$x] . "</td>";
        $item_message .= "</tr>";

        $prepared_by = $row['prepared_by'];

        $recipientList = $this->getNotifRecipient_approval($prepared_by);
        $recipient = array();
        foreach ($recipientList as $key) {
            array_push($recipient, $key->email);
                }
                $x++;
        }
        $item_message .= '</tbody>';

        $from_email = "bifa.acd@gmail.com";
        $to_email = "aidanurul99@rocketmail.com";
        if ($ket == 'approve') {
                $ket_level = 'Disetujui';
                $tindakan = 'Approval';
        } else {
                $ket_level = 'Ditolak';
                $tindakan = 'Rejection';
        }

        //Load email library 
        $this->load->library('email');
            
        $this->email->set_newline("\r\n");
        $message = "<p>Hello</p>";
        $message .= "<p>Laporan Aircraft Component Status Berikut telah " . $ket_level . " oleh " . $by . "</p>";
        $message .= "<table>";
        $message .= "<thead>";
        $message .= "<tr>";
        $message .= "<th>Document Number</th>";
            $message .= "<th>Aircraft Reg. No</th>";
            $message .= "<th>".ucwords($ket)." Notes</th>";   
        $message .= "</tr>";
        $message .= "</thead>";
        $message .= $item_message;
        $message .= "</table>";
        // $message .= "<p>No Purchase Request : ".$row['document_number']."</p>";    
        $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
        $message .= "<p>[ <a href='".$this->config->item('url_mrp')."' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
        $message .= "<p>Thanks and regards</p>";
        $this->email->from($from_email, 'Material Resource Planning');
        $this->email->to($recipient);
        $this->email->subject('Notification '.$tindakan);
        $this->email->message($message);

        //Send mail 
        if ($this->email->send())
        return true;
        else
        return $this->email->print_debugger();
    }

    public function getNotifRecipient_approval($name)
    {
        $this->db->select('email');
        $this->db->from('tb_auth_users');
        $this->db->where('person_name', $name);
        return $this->db->get('')->result();
    }

}
