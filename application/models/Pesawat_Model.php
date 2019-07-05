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
      'nama_pesawat'      => 'Nama Pesawat',
      'keterangan'        => 'Keterangan'
    );
  }

  public function getSearchableColumns()
  {
    return array(
      'nama_pesawat',
      'keterangan',
    );
  }

  public function getOrderableColumns()
  {
    return array(
      null,
      'nama_pesawat',
      'keterangan',
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

}
