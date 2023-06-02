<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Aircraft_Robbing_Part_Model extends MY_Model
{
    protected $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = config_item('module')['aircraft_robbing_part'];
    }

    public function getHeader(){
        return array(
            '',
            'Description Part',
            'Part Number',
            'Serial Number',
            'TSN',
            'TSO',
            'Date (Remove)',
            'A/C Reg (Remove)',
            'A/C Type (Remove)',
            'Base Station (Remove)',
            'PIC (Remove)',
            'Date of AJLB (Install)',
            'A/C Hour TTIS (Install)',
            'A/C Reg (Install)',
            'A/C Type (Install)',
            'Base Station (Install)',
            'PIC (Install)',
            'Remarks'
        );
    }

    public function getHeaderColumn(){
        return array(
            'Date' => '1' ,
            'DESCRIPTION ROBBING PART' => '5',
            'REMOVAL PART' => '5',
            'INSTALLATION PART' => '6',
            '' => '1' ,
        );
    }

    public function getSelectedColumnForRemovePart()
    {
        return array(
            'id',
            'date_of_ajlb',
            'aircraft_register',
            'aircraft_type',
            'aircraft_base',
            'remove_date',
            'remove_tsn',
            'remove_tso',
            'pic',
            'remove_description',
            'remove_part_number',
            'remove_alternate_part_number',
            'remove_serial_number',
            'category',
            'status',
            'remarks',
            // 'aircraft_base',
            // 'aircraft_base'
        );
    }

    public function getSearchableColumns()
    {
        return array(
            // 'id',
            // 'date_of_ajlb',
            'aircraft_register',
            'aircraft_type',
            'aircraft_base',
            // 'remove_date',
            'remove_tsn',
            'remove_tso',
            'pic',
            'remove_description',
            'remove_part_number',
            'remove_alternate_part_number',
            'remove_serial_number',
            'category',
            'status',
            'remarks',
            // 'aircraft_base',
            // 'aircraft_base'
        );
    }

    public function getOrderableColumns()
    {
        return array(
            NULL,
            'description',
            'part_number',
            'serial_number',
            NULL,
            NULL,
            'remove_date',
            'remove_aircraft_register',
            'remove_aircraft_type',
            'remove_aircraft_base',
            'remove_pic',
            'date_of_ajlb',
            NULL,
            'install_aircraft_register',
            'install_aircraft_type',
            'install_aircraft_base',
            'install_pic',
            'remarks',
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
        $this->db->select('*');
        $this->db->from('tb_aircraft_robbing_parts');

        $this->searchIndex();

        $column_order = $this->getOrderableColumns();

        if (isset($_POST['order'])){
            foreach ($_POST['order'] as $key => $order){
                $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
            }
        } else {
            $this->db->order_by('tb_aircraft_robbing_parts.id','asc');
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
        $this->db->from('tb_aircraft_robbing_parts');
        $this->searchIndex();

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function countIndex()
    {
        $this->db->from('tb_aircraft_robbing_parts');

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function findById($id)
    {
        $this->db->where('id', $id);

        $query    = $this->db->get('tb_aircraft_robbing_parts');
        $aircraft_robbing_parts = $query->unbuffered_row('array');

        return $aircraft_robbing_parts;
    }

    public function findComponentById($id)
    {
        $this->db->where('id', $id);

        $query    = $this->db->get('tb_aircraft_components');
        $aircraft_components = $query->unbuffered_row('array');

        return $aircraft_components;
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

    public function searchItemBySource($source,$aircraft)
    {
        if($source=='inventory'){
            $selected = array(
                'tb_issuance_items.id'                  => 'Remarks',
                'tb_issuances.document_number'          => 'Document Number',
                'tb_issuances.issued_date'              => 'Issued Date',
                'tb_issuances.category'                 => 'Category',
                'tb_issuances.warehouse'                => 'Base',
                'tb_master_items.description'           => 'Description',
                'tb_master_items.id as item_id'         => 'Item Id',
                'tb_master_items.part_number'           => 'Part Number',
                'tb_master_items.serial_number'         => 'Serial Number',
                'tb_issuance_items.issued_quantity'     => 'Quantity',
                'tb_master_items.unit'                  => 'Unit',
                'tb_issuance_items.remarks'             => 'Remarks',
                'tb_issuances.issued_to'                => 'Issued To',
                'tb_issuances.issued_by'                => 'Issued By',
                'tb_issuances.required_by'              => 'Required By',
                'tb_issuances.notes'                    => 'Note/IPC Ref.',
                'tb_master_items.alternate_part_number'           => 'Part Number',
            );
            $this->db->select(array_keys($selected));
            $this->db->from('tb_issuances');
            $this->db->join('tb_issuance_items', 'tb_issuance_items.document_number = tb_issuances.document_number');
            $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_issuance_items.stock_in_stores_id');
            $this->db->join('tb_stocks', 'tb_stocks.id = tb_stock_in_stores.stock_id');
            $this->db->join('tb_master_items', 'tb_master_items.id = tb_stocks.item_id');
            $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
            $this->db->where_in('tb_issuances.category', config_item('auth_inventory'));
            $this->db->where_in('tb_issuances.warehouse', config_item('auth_warehouses')); 
            $this->db->where('tb_issuances.issued_to', $aircraft);
            $this->db->where('tb_issuances.category !=','BAHAN BAKAR');
            $this->db->like('tb_issuances.document_number', 'MS');
            $this->db->order_by('tb_issuances.category', 'asc');
            $query = $this->db->get();
        }

        if($source=='robbing'){
            $selected = array(
                'tb_aircraft_robbing_parts.*',
            );
            $this->db->select($selected);
            $this->db->from('tb_aircraft_robbing_parts');
            $this->db->where('tb_aircraft_robbing_parts.component_install_id is NULL', NULL, FALSE);
            $this->db->order_by('tb_aircraft_robbing_parts.id', 'asc');
            $query = $this->db->get();
        }

        return $query->result_array();
    }

    public function install($id)
    {
        $this->db->trans_begin();

        $install_date                   = $_POST['install_date'];
        $install_aircraft_register      = $_POST['install_aircraft_register'];
        $install_pic                    = $_POST['install_pic'];
        $remarks                        = $_POST['remarks'];
        $selected_aircraft              = getAircraftByRegisterNumber($install_aircraft_register);
        $selected_robbing_part          = $this->findById($id);
        $selected_component             = $this->findComponentById($selected_robbing_part['component_remove_id']);
        
        $item_id = $selected_component['item'];
                
        $this->db->set('type', $selected_component['type']);
        $this->db->set('aircraft_id', $selected_aircraft['id']);
        $this->db->set('aircraft_code', $selected_aircraft['nama_pesawat']);
        $this->db->set('item_id', $item_id);
        $this->db->set('part_number', $selected_component['part_number']);
        $this->db->set('description', $selected_component['description']);
        $this->db->set('alternate_part_number', $selected_component['alternate_part_number']);
        $this->db->set('serial_number', $selected_component['serial_number']);
        $this->db->set('interval', $selected_component['interval']);
        $this->db->set('interval_satuan', $selected_component['interval_satuan']);
        $this->db->set('installation_date', $install_date);
        $this->db->set('installation_by', $install_pic);
        $this->db->set('af_tsn', $selected_robbing_part['remove_tsn']);
        $this->db->set('equip_tsn', $selected_robbing_part['remove_tsn']);
        $this->db->set('tso', $selected_robbing_part['remove_tso']);
        $this->db->set('remarks', $remarks);
        if($selected_component['next_due_date']!=NULL){
            $this->db->set('next_due_date', $data['next_due_date']);
        }
        if($selected_component['next_due_hour']!=NULL){
            $this->db->set('next_due_hour', $data['next_due_hour']);
        } 
        $this->db->set('issuance_item_id', $selected_component['issuance_item_id']);     
        $this->db->set('issuance_document_number', $selected_component['issuance_document_number']); 
        $this->db->set('active', true);
        $this->db->set('created_at', date('Y-m-d H:i:s'));
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->insert('tb_aircraft_components');
        $component_install_id = $this->db->insert_id();

        $this->db->set('component_install_id', $component_install_id);  
        $this->db->set('date_of_ajlb', $install_date);      
        $this->db->set('install_aircraft_id', $selected_aircraft['id']);      
        $this->db->set('install_aircraft_register', $selected_aircraft['nama_pesawat']);   
        $this->db->set('install_aircraft_type', $selected_aircraft['type']);      
        $this->db->set('install_aircraft_base', $selected_aircraft['base']);     
        $this->db->set('install_pic', $install_pic);      
        $this->db->set('install_remarks', $remarks); 
        $this->db->set('updated_by', config_item('auth_person_name')); 
        $this->db->set('updated_at', date('Y-m-d H:i:s'));  
        $this->db->where('id',$id);  
        $this->db->update('tb_aircraft_robbing_parts');


        if ($this->db->trans_status() === FALSE)
        return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

}
