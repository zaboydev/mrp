<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cash_Request_Model extends MY_Model
{
  protected $connection;

  public function __construct()
  {
    parent::__construct();
    //Do your magic here
    $this->connection   = $this->load->database('budgetcontrol', TRUE);
    $this->modules        = config_item('module');
    $this->data['modules']        = $this->modules;
  }
  public function getSelectedColumns()
  {
    $return = array(
      // "''".' as "temp"' => "Act.", 
      'tb_cash_request.id'                  => NULL,
      'tb_cash_request.status'              => 'Document Number',
      'tb_cash_request.tanggal'             => 'Date',
      'tb_cash_request.document_number'     => 'Status',
      'tb_cash_request.request_by'          => 'Request By',
      'tb_cash_request.cash_account_code'   => 'Cash Account',
      'tb_cash_request.request_amount'      => 'Request Amount',
      'tb_cash_request.coa_kredit'          => 'Transfer From',
      'tb_cash_request.notes'               => 'Notes',
    );

    return $return;
  }
  public function getSearchableColumns()
  {
    $return = array(
      'tb_cash_request.document_number',
      'tb_cash_request.request_by',
      'tb_cash_request.cash_account_code',
      'tb_cash_request.cash_account_name',
      'tb_cash_request.notes',
      'tb_cash_request.cash_account_name',
      'tb_cash_request.coa_kredit',
      'tb_cash_request.akun_kredit'

    );

    return $return;
  }

  public function getGroupedColumns()
  {
    return array(
      'tb_po.id',
      'tb_po.document_number',
      'tb_po.status',
      'tb_po.document_date',
      'tb_po.vendor'
    );
  }

  public function getOrderableColumns()
  {
    $return = array(
      null,
      'tb_cash_request.document_number',
      'tb_cash_request.tanggal',
      'tb_cash_request.status',
      'tb_cash_request.request_by',
      'tb_cash_request.cash_account_code',
      'tb_cash_request.request_amount',
      'tb_cash_request.coa_kredit',
      'tb_cash_request.notes',
    );
    return $return;
  }

  private function searchIndex()
  {
    if (!empty($_POST['columns'][1]['search']['value'])) {
      $search_received_date = $_POST['columns'][1]['search']['value'];
      $range_received_date  = explode(' ', $search_received_date);

      $this->db->where('tb_cash_request.tanggal >= ', $range_received_date[0]);
      $this->db->where('tb_cash_request.tanggal <= ', $range_received_date[1]);
    }

    if (!empty($_POST['columns'][2]['search']['value'])) {
      $status = $_POST['columns'][2]['search']['value'];

      if($status != 'all'){
        $this->db->where('tb_cash_request.status', $status);
      }
    }else{
      if(is_granted($this->data['modules']['cash_request'], 'approval')){
        if (config_item('auth_role') == 'FINANCE MANAGER') {
          $status[] = 'WAITING REVIEW BY FIN MNG';
        }
        if (config_item('auth_role') == 'VP FINANCE') {
          $status[] = 'WAITING REVIEW BY VP FINANCE';
        }
        $this->db->where_in('tb_cash_request.status', $status);
      }else{
        if (config_item('auth_role') == 'TELLER') {
          $status[] = 'APPROVED';
          $this->db->where_in('tb_cash_request.status', $status);
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
    $this->db->from('tb_cash_request');
    $this->searchIndex();
    $column_order = $this->getOrderableColumns();

    if (isset($_POST['order'])) {
      foreach ($_POST['order'] as $key => $order) {
        $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
      }
    } else {
      $this->db->order_by('id', 'desc');
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
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_cash_request');
    $this->searchIndex();

    $query = $this->db->get();

    return $query->num_rows();
  }

  public function countIndex()
  {
    $this->db->select(array_keys($this->getSelectedColumns()));
    $this->db->from('tb_cash_request');
    $query = $this->db->get();

    return $query->num_rows();
  }


  public function findById($id)
  {
    $this->db->where('id', $id);

    $query    = $this->db->get('tb_cash_request');
    $po       = $query->unbuffered_row('array');

    return $po;
  }

  public function insert()
  {
    $this->db->trans_begin();

    if($this->input->post('cash_request_id')){
      $this->db->set('status','REVISI');
      $this->db->set('revisi','f');
      $this->db->where('id',$this->input->post('cash_request_id'));
      $this->db->update('tb_cash_request');
      $document_number = $this->input->post('order_number');
    }else{
      $document_number = cash_request_order_number().cash_request_format_number();
    }
    
    $cash_account      = getAccountByCode($this->input->post('cash_account'));

    $this->db->set('document_number', $document_number);
    $this->db->set('tanggal', $this->input->post('date'));
    $this->db->set('status','WAITING REVIEW BY FIN MNG');
    $this->db->set('request_by', $this->input->post('request_by'));
    $this->db->set('currency','IDR');
    $this->db->set('request_amount', $this->input->post('request_amount'));
    $this->db->set('notes', $this->input->post('notes'));
    $this->db->set('base', config_item('auth_warehouse'));
    $this->db->set('cash_account_code', $this->input->post('cash_account'));
    $this->db->set('cash_account_name', $cash_account->group);
    $this->db->set('created_by', config_item('auth_person_name'));
    $this->db->set('created_at', date('Y-m-d'));
    if($this->input->post('cash_request_id')){
      $this->db->set('revisi','f');
    }
    $this->db->insert('tb_cash_request');
    

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();

    return [
      'document_number' => $document_number,
      'type'            =>TRUE
    ];
  }

  public function insert_payment($id)
  {
    $this->db->trans_begin();

    $entity = $this->findById($id);

    $kurs       = $this->tgl_kurs(date("Y-m-d"));
    if ($entity['currency'] == 'IDR') {
      $amount_idr = $entity['request_amount'];
      $amount_usd = $entity['request_amount'] / $kurs;
    } else {
      $amount_usd = $entity['request_amount'];
      $amount_idr = $entity['request_amount'] * $kurs;
    }

    $document_number = $this->input->post('order_number');
    $akun_kredit      = getAccountByCode($this->input->post('coa_kredit'));
    $akun_debet      = getAccountByCode($entity['cash_account_code']);

    $this->db->set('no_cheque', $this->input->post('no_cheque'));
    $this->db->set('coa_kredit', $this->input->post('coa_kredit'));
    $this->db->set('akun_kredit',$akun_kredit->group);
    $this->db->set('paid_at', $this->input->post('paid_at'));
    $this->db->set('paid_by', $this->input->post('paid_by'));
    $this->db->set('no_konfirmasi', $this->input->post('no_konfirmasi'));
    $this->db->set('paid_base', config_item('auth_warehouse'));
    $this->db->set('status','PAID');
    $this->db->where('id',$id);
    $this->db->update('tb_cash_request');

    if($this->input->post('edit')=='yes'){
      $this->db->select('tb_jurnal.*');
      $this->db->from('tb_jurnal');
      $this->db->where('tb_jurnal.no_jurnal', $entity['document_number']);
      $query = $this->db->get();
      $jurnal = $query->unbuffered_row('array');

      $this->db->where('id_jurnal',$jurnal['id']);
      $this->db->delete('tb_jurnal_detail');

      $this->db->where('id',$jurnal['id']);
      $this->db->delete('tb_jurnal');
    }

    $this->db->set('no_jurnal', $document_number);
    $this->db->set('tanggal_jurnal  ', date("Y-m-d",strtotime($this->input->post('paid_at'))));
    $this->db->set('source', "CASH REQUEST");
    // $this->db->set('vendor', $vendor);
    $this->db->set('grn_no', $document_number);
    $this->db->set('keterangan', strtoupper("pembayaran cash request"));
    $this->db->insert('tb_jurnal');
    $id_jurnal = $this->db->insert_id();

    //debet
    $this->db->set('id_jurnal', $id_jurnal);
    $this->db->set('jenis_transaksi', $akun_debet->group);
    $this->db->set('trs_debet', $amount_idr);
    $this->db->set('trs_kredit', 0);
    $this->db->set('trs_debet_usd', $amount_usd);
    $this->db->set('trs_kredit_usd', 0);
    $this->db->set('kode_rekening', $entity['cash_account_code']);
    $this->db->set('currency', $entity['currency']);
    $this->db->insert('tb_jurnal_detail');

    //kredit
    $this->db->set('id_jurnal', $id_jurnal);
    $this->db->set('jenis_transaksi', $akun_kredit->group);
    $this->db->set('trs_debet', 0);
    $this->db->set('trs_kredit', $amount_idr);
    $this->db->set('trs_debet_usd', 0);
    $this->db->set('trs_kredit_usd', $amount_usd);
    $this->db->set('kode_rekening', $this->input->post('coa_kredit'));
    $this->db->set('currency', $entity['currency']);
    $this->db->insert('tb_jurnal_detail');

    foreach ($_SESSION["payment"]["attachment"] as $file) {
      $this->db->set('id_poe', $id);
      $this->db->set('tipe', "CASH REQUEST");
      $this->db->set('file', $file);
      $this->db->set('tipe_att', "CASH REQUEST PAYMENT");
      $this->db->insert('tb_attachment_poe');
    }
    

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();

    return [
      'document_number' => $document_number,
      'type'            =>TRUE
    ];
  }

  public function approve($id)
  {
    $this->db->trans_begin();

    $this->db->select('tb_cash_request.*');
    $this->db->from('tb_cash_request');
    $this->db->where('tb_cash_request.id',$id);
    $query        = $this->db->get();
    $cash_request   = $query->unbuffered_row('array');
    $currency     = $cash_request['currency'];
    $level      = 0;
    $status     = '';

    if (config_item('auth_role')=='FINANCE MANAGER' && $cash_request['status'] == 'WAITING REVIEW BY FIN MNG') {
      if($cash_request['base']=='JAKARTA'){
        $this->db->set('status', 'WAITING REVIEW BY VP FINANCE');
        $status = 'WAITING REVIEW BY VP FINANCE';
        $level = 3;
      }else{
        $this->db->set('status', 'APPROVED');
        $status = 'APPROVED';
        $level = 0;
      }     
      $this->db->set('review_by', config_item('auth_person_name'));
      $this->db->set('review_at', date('Y-m-d'));
      $this->db->where('id', $id);
      $this->db->update('tb_cash_request');
    }

    if (config_item('auth_role')=='VP FINANCE' && $cash_request['status'] == 'WAITING REVIEW BY VP FINANCE') {
      $this->db->set('status', 'APPROVED');
      $status = 'WAITING REVIEW BY CFO';
      $level = 0;
      $this->db->set('known_by', config_item('auth_person_name'));
      $this->db->set('known_at', date('Y-m-d'));
      $this->db->where('id', $id);
      $this->db->update('tb_cash_request');
    }

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    if($level!=0){
      $this->send_mail($id,$level,$cash_request['base']);
    }
    $this->db->trans_commit();
    return TRUE;
  }

  public function rejected($id)
  {
    $this->db->trans_begin();

    $this->db->select('tb_cash_request.*');
    $this->db->from('tb_cash_request');
    $this->db->where('tb_cash_request.id',$id);
    $query          = $this->db->get();
    $cash_request   = $query->unbuffered_row('array');
    $level      = 0;

    if($cash_request['status']!='REJECTED'){
      $this->db->set('status', 'REJECTED');
      $this->db->set('rejected_by', config_item('auth_person_name'));
      $this->db->set('rejected_at', date('Y-m-d'));
      $this->db->where('id', $id);
      $this->db->update('tb_cash_request');
      $status = 'REJECTED';
    }  

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  public function send_mail($doc_id, $level,$base=null)
  {
    $this->db->select('tb_cash_request.*');
    $this->db->from('tb_cash_request');
    $this->db->where('tb_cash_request.id',$id);
    $query        = $this->db->get();
    $cash_request   = $query->unbuffered_row('array');


    if($base!=null){
      $recipientList = $this->getNotifRecipient($level,$base);
    }else{
      $recipientList = $this->getNotifRecipient($level);
    }   

    $recipient = array();
    foreach ($recipientList as $key) {
      array_push($recipient, $key->email);
    }

    $from_email = "bifa.acd@gmail.com";
    $to_email = "aidanurul99@rocketmail.com";
    $ket_level = '';

    $levels_and_roles = config_item('levels_and_roles');
    $ket_level = $levels_and_roles[$level];

    //Load email library 
    $this->load->library('email');
    $this->email->set_newline("\r\n");
    $message = "<p>Dear " . $ket_level . "</p>";
    $message .= "<p>Cash Request No ".$cash_request['document_number']." Berikut perlu Persetujuan Anda </p>";
    $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
    $message .= "<p>[ <a href='http://119.2.51.138:7323/cash_request/' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
    $message .= "<p>Thanks and regards</p>";
    $this->email->from($from_email, 'Material Resource Planning');
    $this->email->to($recipient);
    $this->email->subject('Permintaan Approval Cash Request');
    $this->email->message($message);

    //Send mail 
    if ($this->email->send())
      return true;
    else
      return $this->email->print_debugger();
  }

  public function getNotifRecipient($level,$base=null)
  {
    $this->db->select('email');
    $this->db->from('tb_auth_users');
    $this->db->where('auth_level', $level);
    if($level==14){
      if($base=='JAKARTA'){
        $this->db->where('warehouse', $base);
      }else{
        $this->db->where('warehouse !=', 'JAKARTA');
      }     
    }
    return $this->db->get('')->result();
  }
}

/* End of file Account_Payable_Model.php */
/* Location: ./application/models/Account_Payable_Model.php */
