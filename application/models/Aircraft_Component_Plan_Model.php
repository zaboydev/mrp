<?php defined('BASEPATH') or exit('No direct script access allowed');

class Aircraft_Component_Plan_Model extends MY_Model
{
    protected $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = config_item('module')['aircraft_component_plan'];
    }

    public function getSelectedColumns()
    {
        return array(
            'No',
            'Status',
            'Date',
            'Year Plan',
            'A/C Reg',
            'A/C Type',
            'Description',
            'Part Number',
            'Alt Part Number',
            'Remarks'
        );
    }

    public function getSearchableColumns()
    {
        return array(
            'tb_aircraft_component_plan.aircraft_register',
            'tb_aircraft_component_plan.aircraft_type',
            'tb_aircraft_component_plan.part_number',
            'tb_aircraft_component_plan.description',
            'tb_aircraft_component_plan.alternate_part_number',
            'tb_aircraft_component_plan.remarks',
        );
    }

    public function getOrderableColumns()
    {
        return array(
            null,
            'tb_aircraft_component_plan.status',
            'tb_aircraft_component_plan.date',
            'tb_aircraft_component_plan.aircraft_hour',
            'tb_aircraft_component_plan.aircraft_register',
            'tb_aircraft_component_plan.aircraft_type',
            'tb_aircraft_component_plan.description',
            'tb_aircraft_component_plan.part_number',
            'tb_aircraft_component_plan.alternate_part_number',
            'tb_aircraft_component_plan.remarks',        
        );
    }

    private function searchIndex()
    {

        $i = 0;

        foreach ($this->getSearchableColumns() as $item) {
            if ($_POST['search']['value']) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like('UPPER(' . $item . ')', strtoupper($_POST['search']['value']));
                } else {
                    $this->db->or_like('UPPER(' . $item . ')', strtoupper($_POST['search']['value']));
                }

                if (count($this->getSearchableColumns()) - 1 == $i)
                    $this->db->group_end();
            }

            $i++;
        }
    }

    function getIndex($return = 'array')
    {
        $this->db->select('*');
        $this->db->from('tb_aircraft_component_plan');

        $this->searchIndex();

        $column_order = $this->getOrderableColumns();

        if (isset($_POST['order'])) {
            foreach ($_POST['order'] as $key => $order) {
                $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
            }
        } else {
            $this->db->order_by('id', 'asc');
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
        $this->db->select('*');
        $this->db->from('tb_aircraft_component_plan');

        $this->searchIndex();

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function countIndex()
    {
        $this->db->select('*');
        $this->db->from('tb_aircraft_component_plan');

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function findById($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('tb_aircraft_component_plan');

        return $query->row_array();
    }

    public function insert()
    {
        $this->db->trans_begin();

        $selected_aircraft = getAircraftByRegisterNumber($this->input->post('aircraft_register'));

        $this->db->set('date', $this->input->post('date'));
        $this->db->set('aircraft_id', $selected_aircraft['id']);
        $this->db->set('year_plan', $this->input->post('year_plan'));
        $this->db->set('aircraft_register', $this->input->post('aircraft_register'));
        $this->db->set('aircraft_type', $selected_aircraft['type']);
        $this->db->set('aircraft_base', $selected_aircraft['base']);
        $this->db->set('description', trim(strtoupper($this->input->post('description'))));
        $this->db->set('part_number', trim(strtoupper($this->input->post('part_number'))));
        $this->db->set('alternate_part_number', strtoupper($this->input->post('alternate_part_number')));
        $this->db->set('group', strtoupper($this->input->post('group')));
        $this->db->set('unit', strtoupper($this->input->post('unit')));  
        $this->db->set('planing_quantity', $this->input->post('planing_quantity')); 
        $this->db->set('left_planing_quantity', $this->input->post('planing_quantity')); 
        $this->db->set('remarks', $this->input->post('remarks'));
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('created_at', date('Y-m-d H:i:s'));
        $this->db->insert('tb_aircraft_component_plan');

        $serial_number = NULL;

        if (isItemExists(trim($this->input->post('part_number')),trim($this->input->post('description')), $serial_number) === FALSE) {
            $this->db->set('description', trim(strtoupper($this->input->post('description'))));
            $this->db->set('part_number', trim(strtoupper($this->input->post('part_number'))));
            $this->db->set('alternate_part_number', strtoupper($this->input->post('alternate_part_number')));
            $this->db->set('group', strtoupper($this->input->post('group')));
            $this->db->set('unit', strtoupper($this->input->post('unit')));
            $this->db->set('minimum_quantity', floatval(1));
            $this->db->set('kode_pemakaian', NULL);
            $this->db->set('notes', NULL);
            $this->db->set('kode_stok', NULL);
            $this->db->set('created_by', config_item('auth_person_name'));
            $this->db->set('updated_by', config_item('auth_person_name'));
            $this->db->insert('tb_master_items');
        }

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();

        return TRUE;
    }

    public function update($id)
    {
        $this->db->trans_begin();

        $selected_aircraft = getAircraftByRegisterNumber($this->input->post('aircraft_register'));

        $this->db->set('date', $this->input->post('date'));
        $this->db->set('aircraft_id', $selected_aircraft['id']);
        $this->db->set('year_plan', $this->input->post('year_plan'));
        $this->db->set('aircraft_register', $this->input->post('aircraft_register'));
        $this->db->set('aircraft_type', $selected_aircraft['type']);
        $this->db->set('aircraft_base', $selected_aircraft['base']);
        $this->db->set('description', trim(strtoupper($this->input->post('description'))));
        $this->db->set('part_number', trim(strtoupper($this->input->post('part_number'))));
        $this->db->set('alternate_part_number', strtoupper($this->input->post('alternate_part_number')));
        $this->db->set('group', strtoupper($this->input->post('group')));
        $this->db->set('unit', strtoupper($this->input->post('unit')));  
        $this->db->set('remarks', $this->input->post('notes'));
        $this->db->set('planing_quantity', $this->input->post('planing_quantity')); 
        $this->db->set('left_planing_quantity', $this->input->post('planing_quantity')); 
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->set('updated_at', date('Y-m-d H:i:s'));
        $this->db->update('id', $id);
        $this->db->update('tb_aircraft_component_plan');

        $serial_number = NULL;

        if (isItemExists(trim($this->input->post('part_number')),trim($this->input->post('description')), $serial_number) === FALSE) {
            $this->db->set('description', trim(strtoupper($this->input->post('description'))));
            $this->db->set('part_number', trim(strtoupper($this->input->post('part_number'))));
            $this->db->set('alternate_part_number', strtoupper($this->input->post('alternate_part_number')));
            $this->db->set('group', strtoupper($this->input->post('group')));
            $this->db->set('unit', strtoupper($this->input->post('unit')));
            $this->db->set('minimum_quantity', floatval(1));
            $this->db->set('kode_pemakaian', NULL);
            $this->db->set('notes', NULL);
            $this->db->set('kode_stok', NULL);
            $this->db->set('created_by', config_item('auth_person_name'));
            $this->db->set('updated_by', config_item('auth_person_name'));
            $this->db->insert('tb_master_items');
        }

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();

        return TRUE;
    }

    public function findByIds($ids)
    {
        $this->db->where_in('id', $ids);
        $query = $this->db->get('tb_aircraft_component_plan');

        return $query->result_array();
    }

    public function delete()
    {
        $this->db->trans_begin();

        $id = $this->input->post('id');

        $this->db->where('id', $id);
        $this->db->delete('tb_master_items');

        if ($this->db->trans_status() === FALSE)
        return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function import(array $user_data)
    {
        $this->db->trans_begin();

        foreach ($user_data as $key => $data) {
        // $part_number = isPartNumberExists(strtoupper($data['part_number']));
        if (isPartNumberExists(strtoupper($data['part_number'])) == FALSE) {
            $this->db->set('group', strtoupper($data['group']));
            $this->db->set('description', strtoupper($data['description']));
            $this->db->set('part_number', strtoupper($data['part_number']));
            $this->db->set('alternate_part_number', strtoupper($data['alternate_part_number']));
            $this->db->set('min_qty', 1);
            $this->db->set('unit', strtoupper($data['unit']));
            $this->db->set('kode_pemakaian', $data['kode_pemakaian']);
            $this->db->set('current_price', $data['current_price']);
            // $this->db->set('updated_by', config_item('auth_person_name'));
            // $this->db->set('updated_at', date('Y-m-d'));
            $this->db->set('kode_stok', $data['kode_stok']);
            $this->db->insert('tb_master_part_number');

            $item_id = $this->db->insert_id();
        }

        if (isItemExists(strtoupper($data['part_number'],$data['description'])) == FALSE) {
            $this->db->set('group', strtoupper($data['group']));
            $this->db->set('description', strtoupper($data['description']));
            $this->db->set('part_number', strtoupper($data['part_number']));
            $this->db->set('alternate_part_number', strtoupper($data['alternate_part_number']));
            $this->db->set('minimum_quantity', 1);
            $this->db->set('unit', strtoupper($data['unit']));
            $this->db->set('kode_pemakaian', $data['kode_pemakaian']);
            $this->db->set('current_price', $data['current_price']);
            $this->db->set('created_by', config_item('auth_person_name'));
            $this->db->insert('tb_master_items');
        }

        
        }

        if ($this->db->trans_status() === FALSE)
        return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function insertItemUnit($unit)
    {
        $this->db->trans_begin();

        $this->db->set('unit', strtoupper($unit));
        $this->db->set('description', null);
        $this->db->set('notes', null);
        $this->db->set('created_by', config_item('auth_person_name'));
        // $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->insert('tb_master_item_units');

        if ($this->db->trans_status() === FALSE)
        return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function searchItemsByPartNumber($category)
    {
        $this->column_select = array(
        'tb_master_items.id',
        'tb_master_items.group',
        'tb_master_items.description',
        'tb_master_items.part_number',
        'tb_master_items.alternate_part_number',
        'tb_master_items.minimum_quantity',
        'tb_master_items.unit',
        'tb_master_items.kode_stok',
        'tb_master_items.serial_number',
        );

        $this->db->select($this->column_select);
        $this->db->from('tb_master_items');
        $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
        $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
        $this->db->where_in('tb_master_item_groups.category', $category);
        $this->db->group_by($this->column_select);

        $this->db->order_by('tb_master_items.group ASC, tb_master_items.description ASC');

        $query  = $this->db->get();
        $result = $query->result_array();

        return $result;
    }
}
