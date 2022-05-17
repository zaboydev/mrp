<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Daftar_Pajak_Model extends MY_Model
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = config_item('module')['daftar_pajak'];
  }

  public function getSelectedColumns()
  {
    return array(
      'id'                => NULL,
      'description'       => 'Name',
      'percentase'        => 'Percentase',
      'notes'             => 'Notes',
      'status'            => 'Status',
      'updated_at'        => 'Last Update',
      'status_change_by'  => 'Last Update Status By',
      'status_change_at'  => 'Last Update Status '
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'description',
      'percentase',
      'notes',
      'status'
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'description',
      'percentase',
      'notes',
      'status',
      'updated_at',
      'status_change_by',
      'status_change_at'
    );
  }

  private function searchIndex()
  {

    if (!empty($_POST['columns'][1]['search']['value'])){
      $status = $_POST['columns'][1]['search']['value'];

      $this->db->where('tb_master_daftar_pajak.status', $status);
    }else{
      $this->db->where('tb_master_daftar_pajak.status', 'AVAILABLE');
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
    $this->db->from('tb_master_daftar_pajak');

    $this->searchIndex();

    $column_order = $this->getOrderableColumns();

    if (isset($_POST['order'])){
      foreach ($_POST['order'] as $key => $order){
        $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('description', 'asc');
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
    $this->db->from('tb_master_daftar_pajak');

    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->from('tb_master_daftar_pajak');

    return $this->db->count_all_results();
  }

  public function findById($id)
  {
    $this->db->where('id', $id);
    $query = $this->db->get('tb_master_daftar_pajak');

    return $query->row_array();
  }

  public function insert()
  {
    $this->db->trans_begin();

    $this->db->set('description', $this->input->post('description'));
    $this->db->set('percentase', $this->input->post('percentase'));
    if($this->input->post('pemotongan')=='yes'){                
      $this->db->set('pemotongan', $this->input->post('pemotongan'));
    }else{
      $this->db->set('pemotongan', 'no');
    }
    $this->db->set('akun_pajak_penjualan', $this->input->post('akun_pajak_penjualan'));
    $this->db->set('akun_pajak_pembelian', $this->input->post('akun_pajak_pembelian'));
    $this->db->set('notes', $this->input->post('notes'));
    $this->db->set('created_by', config_item('auth_person_name'));
    $this->db->set('created_at', date('Y-m-d H:i:s'));
    $this->db->set('updated_by', config_item('auth_person_name'));
    $this->db->set('updated_at', date('Y-m-d H:i:s'));
    $this->db->insert('tb_master_daftar_pajak');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();

    return TRUE;
  }

  public function update($id)
  {
    $this->db->trans_begin();

    $this->db->set('description', $this->input->post('description'));
    $this->db->set('percentase', $this->input->post('percentase'));
    if($this->input->post('pemotongan')=='yes'){                
      $this->db->set('pemotongan', $this->input->post('pemotongan'));
    }else{
      $this->db->set('pemotongan', 'no');
    }
    $this->db->set('akun_pajak_penjualan', $this->input->post('akun_pajak_penjualan'));
    $this->db->set('akun_pajak_pembelian', $this->input->post('akun_pajak_pembelian'));
    $this->db->set('notes', $this->input->post('notes'));
    $this->db->set('updated_at', date('Y-m-d H:i:s'));
    $this->db->set('updated_by', config_item('auth_person_name'));
    $this->db->where('id', $id);
    $this->db->update('tb_master_daftar_pajak');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();

    return TRUE;
  }

  public function import(array $user_data)
  {
    $this->db->trans_begin();

    foreach ($user_data as $key => $data){
      $this->db->set('category', $data['category']);
      $this->db->set('group', strtoupper($data['group']));
      $this->db->set('code', strtoupper($data['code']));
      // $this->db->set('notes', $data['notes']);
      $this->db->set('created_by', config_item('auth_person_name'));
      $this->db->set('updated_by', config_item('auth_person_name'));
      $this->db->insert('tb_master_item_groups');
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

    $this->db->set('status', 'NOT AVAILABLE');
    $this->db->set('status_change_at', date('Y-m-d H:i:s'));
    $this->db->set('status_change_by', config_item('auth_person_name'));
    $this->db->where('id', $id);
    $this->db->update('tb_master_daftar_pajak');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();

    return TRUE;
  }

  public function isDaftarPajakExists($description, $status = NULL, $description_exception = NULL)
  {
    $this->db->from('tb_master_daftar_pajak');
    $this->db->where('description', strtoupper($description));

    if ($description_exception !== NULL)
      $this->db->where('description != ', $description_exception);

    if ($status !== NULL)
      $this->db->where('status', $status);

    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? true : false;
  }
}
