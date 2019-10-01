<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Item_Model extends MY_Model
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = config_item('module')['item'];
  }

  public function getSelectedColumns()
  {
    return array(
      null => null,
      'tb_master_items.id'                    => 'Item Id',      
      'tb_master_items.part_number'           => 'Part Number',
      'tb_master_items.description'           => 'Description',
      'tb_master_items.kode_stok'             => 'Stock Code',
      //'tb_master_items.kode_pemakaian'        => 'Usage Code',
      'tb_master_items.alternate_part_number' => 'Alternate P/N',
      'tb_master_items.serial_number' 		    => 'Serial Number',
      // 'tb_master_item_groups.category'        => 'Category',
      'tb_master_item_groups.group'           => 'Group',
      'tb_master_items.minimum_quantity'      => 'Minimum Qty',
      'tb_master_items.unit'                  => 'Unit',      
      'tb_master_items.updated_at'            => 'Last Update',
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.alternate_part_number',
      'tb_master_items.serial_number',
      // 'tb_master_item_groups.category',
      'tb_master_item_groups.group',
      'tb_master_items.unit',
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.alternate_part_number',
      'tb_master_items.serial_number',
      // 'tb_master_item_groups.category',
      'tb_master_item_groups.group',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit',
      'tb_master_items.updated_at',
    );
  }

  private function searchIndex()
  {
    if (!empty($_POST['columns'][4]['search']['value'])){
      $search_item_category = $_POST['columns'][4]['search']['value'];

      $this->db->where('tb_master_item_groups.category', $search_item_category);
    }

    if (!empty($_POST['columns'][5]['search']['value'])){
      $search_item_group = $_POST['columns'][5]['search']['value'];

      $this->db->where('tb_master_item_groups.group', $search_item_group);
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

        if (count($this->getSearchableColumns()) - 1 == $i)
          $this->db->group_end();
      }

      $i++;
    }
  }

  function getIndex($return = 'array')
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_master_items');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));

    $this->searchIndex();

    $column_order = $this->getOrderableColumns();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('id', 'asc');
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
    $this->db->from('tb_master_items');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->from('tb_master_items');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where_in('tb_master_item_groups.category', config_item('auth_inventory'));

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function findById($id)
  {
    $this->db->where('id', $id);
    $query = $this->db->get('tb_master_items');

    return $query->row_array();
  }

  public function insert()
  {
    $this->db->trans_begin();

    $this->db->set('description', strtoupper($this->input->post('description')));
    $this->db->set('part_number', strtoupper($this->input->post('part_number')));
    $this->db->set('alternate_part_number', strtoupper($this->input->post('alternate_part_number')));
    $this->db->set('group', strtoupper($this->input->post('group')));
    $this->db->set('unit', strtoupper($this->input->post('unit')));
    $this->db->set('minimum_quantity', floatval($this->input->post('minimum_quantity')));
    $this->db->set('kode_pemakaian', strtoupper($this->input->post('kode_pemakaian')));
    $this->db->set('notes', $this->input->post('notes'));
    $this->db->set('kode_stok', strtoupper($this->input->post('kode_stok')));
    $this->db->set('created_by', config_item('auth_person_name'));
    $this->db->set('updated_by', config_item('auth_person_name'));
    $this->db->insert('tb_master_items');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();

    return TRUE;
  }

  public function update($id)
  {
    $this->db->trans_begin();

    if ($this->input->post('mixable')){
      $this->db->set('mixable', 't');
    } else {
      $this->db->set('mixable', 'f');
    }

    $this->db->set('description', strtoupper($this->input->post('description')));
    $this->db->set('part_number', strtoupper($this->input->post('part_number')));
    $this->db->set('serial_number', strtoupper($this->input->post('serial_number')));
    $this->db->set('alternate_part_number', strtoupper($this->input->post('alternate_part_number')));
    $this->db->set('group', strtoupper($this->input->post('group')));
    $this->db->set('unit', strtoupper($this->input->post('unit')));
    $this->db->set('minimum_quantity', floatval($this->input->post('minimum_quantity')));
    $this->db->set('notes', $this->input->post('notes'));
    $this->db->set('kode_stok', strtoupper($this->input->post('kode_stok')));
    $this->db->set('kode_pemakaian', strtoupper($this->input->post('kode_pemakaian')));
    $this->db->set('updated_at', date('Y-m-d H:i:s'));
    $this->db->set('updated_by', config_item('auth_person_name'));
    $this->db->where('id', $id);
    $this->db->update('tb_master_items');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();

    return TRUE;
  }

  public function import(array $user_data)
  {
    $this->db->trans_begin();

    foreach ($user_data as $key => $data){
      $this->db->set('group', strtoupper($data['group']));
      $this->db->set('description', $data['description']);
      $this->db->set('part_number', strtoupper($data['part_number']));
      $this->db->set('alternate_part_number', strtoupper($data['alternate_part_number']));
      $this->db->set('minimum_quantity', strtoupper($data['minimum_quantity']));
      $this->db->set('unit', strtoupper($data['unit']));
      $this->db->set('notes', $data['notes']);
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->set('updated_at', date('Y-m-d'));
      $this->db->set('serial_number', $data['serial_number']);
      $this->db->insert('tb_master_items');

      $item_id = $this->db->insert_id();

      if (isSerialExists($item_id, $data['serial_number']) === FALSE){
          $this->db->set('item_id', $item_id);
          $this->db->set('serial_number', strtoupper($data['serial_number']));
          $this->db->set('warehouse', strtoupper($data['warehouse']));
          $this->db->set('stores', strtoupper($data['stores']));
          $this->db->set('condition', strtoupper($data['condition']));
          $this->db->set('reference_document', 'IMPORT');
          $this->db->set('updated_by', config_item('auth_person_name'));
          $this->db->insert('tb_master_item_serials');

          // $serial_id  = $this->db->insert_id();
        } else {
          $serial     = getSerial($item_id, $data['serial_number']);
          $serial_id  = $serial->id;

          $this->db->set('quantity', 1);
          $this->db->set('warehouse', strtoupper($data['warehouse']));
          $this->db->set('stores', strtoupper($data['stores']));
          $this->db->set('condition', strtoupper($data['condition']));
          $this->db->set('updated_by', config_item('auth_person_name'));
          $this->db->set('reference_document', 'IMPORT');
          $this->db->where('id', $serial_id);
          $this->db->update('tb_master_item_serials');
        }

    }

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function import_item_price(array $user_data)
  {
    $this->db->trans_begin();

    foreach ($user_data as $key => $data){
      $this->db->set('base_price', $data['price']);
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->where('part_number', strtoupper($data['part_number']));
      $this->db->update('tb_master_items');

      $this->db->set('unit_value', $data['price']);
      $this->db->where('part_number', strtoupper($data['part_number']));
      $this->db->update('tb_item_quantity_details');

      $this->db->set('unit_value', $data['price']);
      $this->db->where('part_number', strtoupper($data['part_number']));
      $this->db->update('tb_stocks');

      $this->db->set('unit_value', $data['price']);
      $this->db->where('part_number', strtoupper($data['part_number']));
      $this->db->update('tb_item_in_uses');
    }

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function findByIds($ids)
  {
    $this->db->where_in('id', $ids);
    $query = $this->db->get('tb_master_items');

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

  // public function import(array $user_data)
  // {
  //   $this->db->trans_begin();

  //   foreach ($user_data as $key => $data) {
  //     // $part_number = isPartNumberExists(strtoupper($data['part_number']));
  //     if(isPartNumberExists(strtoupper($data['part_number']))==FALSE){
  //       // $this->db->set('group', strtoupper($data['group']));
  //       $this->db->set('description', $data['description']);
  //       $this->db->set('part_number', strtoupper($data['part_number']));
  //       // $this->db->set('alternate_part_number', strtoupper($data['alternate_part_number']));
  //       // $this->db->set('minimum_quantity', strtoupper($data['minimum_quantity']));
  //       $this->db->set('unit', strtoupper($data['unit']));
  //       // $this->db->set('notes', $data['notes']);
  //       // $this->db->set('created_by', config_item('auth_person_name'));
  //       // $this->db->set('updated_by', config_item('auth_person_name'));
  //       // $this->db->set('updated_at', date('Y-m-d'));
  //       // $this->db->set('serial_number', $data['serial_number']);
  //       $this->db->insert('tb_master_part_number');

  //       $item_id = $this->db->insert_id();
  //     }
  //   }

  //   if ($this->db->trans_status() === FALSE)
  //     return FALSE;

  //   $this->db->trans_commit();
  //   return TRUE;
  // }
}

