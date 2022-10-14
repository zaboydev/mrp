<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Saldo_Model extends MY_Model
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

    function getTransaksi()
    {
        $account 			= $this->input->post('account');
        $start_date 	    = $this->input->post('start_date');
        $end_date 			= $this->input->post('end_date');

        $select = array(
            'tb_jurnal.tanggal_jurnal',
            'tb_jurnal.no_jurnal',
            'tb_jurnal.vendor',
            'tb_jurnal.keterangan',
            'tb_jurnal_detail.trs_debet',
            'tb_jurnal_detail.trs_kredit',
            'tb_jurnal_detail.trs_debet_usd',
            'tb_jurnal_detail.trs_kredit_usd',
        );
        $this->db->select($select);
        $this->db->from('tb_jurnal_detail');
        $this->db->join('tb_jurnal', 'tb_jurnal.id=tb_jurnal_detail.id_jurnal');
        $this->db->order_by('tb_jurnal.tanggal_jurnal','asc');
        if(!empty($account)){
            $this->db->where('tb_jurnal_detail.kode_rekening',$account);
        }

        if(!empty($start_date)&&!empty($end_date)){
            $this->db->where('tb_jurnal.tanggal_jurnal >=',$start_date);
            $this->db->where('tb_jurnal.tanggal_jurnal <=',$end_date);
        }
        $query      = $this->db->get();
        $item       = $query->result_array();

        return $item;
    }

    function getSaldoAwal()
    {
        $account 			= $this->input->post('account');
        $start_date 	    = $this->input->post('start_date');
        $end_date 			= $this->input->post('end_date');

        $select_debet = array(
            'tb_jurnal_detail.kode_rekening',
            'sum(tb_jurnal_detail.trs_debet) as saldo_awal',
            'sum(tb_jurnal_detail.trs_debet_usd) as saldo_awal_usd',
        );
        $this->db->select($select_debet);
        $this->db->from('tb_jurnal_detail');
        $this->db->join('tb_jurnal', 'tb_jurnal.id=tb_jurnal_detail.id_jurnal');
        $this->db->group_by(array(
            'tb_jurnal_detail.kode_rekening',
        ));
        if(!empty($account)){
            $this->db->where('tb_jurnal_detail.kode_rekening',$account);
        }

        if(!empty($start_date)&&!empty($end_date)){
            $this->db->where('tb_jurnal.tanggal_jurnal <',$start_date);
        }
        $query      = $this->db->get();
        $saldo_debet       = $query->row();

        $select_kredit = array(
            'tb_jurnal_detail.kode_rekening',
            'sum(tb_jurnal_detail.trs_kredit) as saldo_awal',
            'sum(tb_jurnal_detail.trs_kredit_usd) as saldo_awal_usd',
        );
        $this->db->select($select_kredit);
        $this->db->from('tb_jurnal_detail');
        $this->db->join('tb_jurnal', 'tb_jurnal.id=tb_jurnal_detail.id_jurnal');
        $this->db->group_by(array(
            'tb_jurnal_detail.kode_rekening',
        ));
        if(!empty($account)){
            $this->db->where('tb_jurnal_detail.kode_rekening',$account);
        }

        if(!empty($start_date)&&!empty($end_date)){
            $this->db->where('tb_jurnal.tanggal_jurnal <',$start_date);
        }
        $query      = $this->db->get();
        $saldo_kredit       = $query->row();

        $return = [
            'saldo_awal' => $saldo_debet->saldo_awal-$saldo_kredit->saldo_awal,
            'saldo_awal_usd' => $saldo_debet_usd->saldo_awal_usd-$saldo_kredit_usd->saldo_awal_usd
        ];

        return $return;
    }

    function getSaldoAkhir()
    {
        $account 			= $this->input->post('account');
        $start_date 	    = $this->input->post('start_date');
        $end_date 			= $this->input->post('end_date');

        $select_debet = array(
            'tb_jurnal_detail.kode_rekening',
            'sum(tb_jurnal_detail.trs_debet) as saldo_akhir',
            'sum(tb_jurnal_detail.trs_debet_usd) as saldo_akhir_usd',
        );
        $this->db->select($select_debet);
        $this->db->from('tb_jurnal_detail');
        $this->db->join('tb_jurnal', 'tb_jurnal.id=tb_jurnal_detail.id_jurnal');
        $this->db->group_by(array(
            'tb_jurnal_detail.kode_rekening',
        ));
        if(!empty($account)){
            $this->db->where('tb_jurnal_detail.kode_rekening',$account);
        }

        if(!empty($start_date)&&!empty($end_date)){
            $this->db->where('tb_jurnal.tanggal_jurnal <=',$end_date);
        }
        $query      = $this->db->get();
        $saldo_debet       = $query->row();

        $select_kredit = array(
            'tb_jurnal_detail.kode_rekening',
            'sum(tb_jurnal_detail.trs_kredit) as saldo_akhir',
            'sum(tb_jurnal_detail.trs_kredit_usd) as saldo_akhir_usd',
        );
        $this->db->select($select_kredit);
        $this->db->from('tb_jurnal_detail');
        $this->db->join('tb_jurnal', 'tb_jurnal.id=tb_jurnal_detail.id_jurnal');
        $this->db->group_by(array(
            'tb_jurnal_detail.kode_rekening',
        ));
        if(!empty($account)){
            $this->db->where('tb_jurnal_detail.kode_rekening',$account);
        }

        if(!empty($start_date)&&!empty($end_date)){
            $this->db->where('tb_jurnal.tanggal_jurnal <=',$end_date);
        }
        $query      = $this->db->get();
        $saldo_kredit       = $query->row();

        $return = [
            'saldo_akhir' => $saldo_debet->saldo_akhir-$saldo_kredit->saldo_akhir,
            'saldo_akhir_usd' => $saldo_debet_usd->saldo_akhir_usd-$saldo_kredit_usd->saldo_akhir_usd
        ];

        return $return;
    }

    public function isDocumentNumberExists($document_number)
    {
        $this->db->where('transaction_number', $document_number);
        $query = $this->db->get('tb_saldo_awal');

        if ($query->num_rows() > 0)
          return true;

        return false;
    }

    public function save(){
        $this->db->trans_begin();
        $this->connection->trans_begin();

        if (isset($_SESSION['saldo']['edit'])) {
            //JURNAL
            $this->db->where('no_jurnal', $_SESSION['saldo']['edit']);
            $query    = $this->db->get('tb_jurnal');
            $jurnal       = $query->unbuffered_row('array');

            $this->db->where('id_jurnal',$jurnal['id']);
            $this->db->delete('tb_jurnal_detail');

            $this->db->where('id',$jurnal['id']);
            $this->db->delete('tb_jurnal');

            //tb saldo awal

            $this->db->where('category',$_SESSION['saldo']['category']);
            $this->db->delete('tb_saldo_awal');
        }

        $document_number      = $_SESSION['saldo']['document_number'];
        $tanggal              = $_SESSION['saldo']['date'];
        $category             = $_SESSION['saldo']['category'];
        $created_by           = $_SESSION['saldo']['created_by'];
        $notes                = (empty($_SESSION['saldo']['notes'])) ? NULL : $_SESSION['saldo']['notes'];
        $base                 = config_item('auth_warehouse');
        $kurs                   = kurs($tanggal);

        $account_code               = $this->input->post('account_code');
        $debit                      = $this->input->post('debit');
        $credit                     = $this->input->post('credit');

        $this->db->set('no_jurnal', $document_number);
        $this->db->set('tanggal_jurnal  ', date("Y-m-d",strtotime($tanggal)));
        $this->db->set('source', "SALDO AWAL");
        $this->db->set('vendor', NULL);
        $this->db->set('grn_no', $document_number);
        $this->db->set('keterangan', $notes);
        $this->db->set('created_by',config_item('auth_person_name'));
        $this->db->set('created_at',date('Y-m-d'));
        $this->db->insert('tb_jurnal');
        $id_jurnal = $this->db->insert_id();

        $total_debet = array();
        $total_kredit = array();

        foreach ($account_code as $key => $item){
            $this->db->set('transaction_number',$document_number);
            $this->db->set('category',$category);
            $this->db->set('date',$tanggal);
            $this->db->set('account_code',$item);
            $this->db->set('debit',$debit[$key]);
            $this->db->set('credit',$credit[$key]);
            $this->db->set('created_by', config_item('auth_person_name'));
            $this->db->set('created_at', date('Y-m-d'));
            $this->db->insert('tb_saldo_awal');

            $akun = getAccountMrpByCode($item);
            $this->db->set('id_jurnal', $id_jurnal);
            $this->db->set('jenis_transaksi', strtoupper($akun->group));
            $this->db->set('trs_kredit', $credit[$key]);
            $this->db->set('trs_debet', $debit[$key]);
            $this->db->set('trs_kredit_usd', 0);
            $this->db->set('trs_debet_usd', $amount_usd);
            $this->db->set('kode_rekening', $akun->coa);
            $this->db->set('currency', $currency);
            $this->db->insert('tb_jurnal_detail');

            $total_kredit[] = $credit[$key];
            $total_debet[]  = $debit[$key];
        }

        $sumTotalDebit = array_sum($total_debet);
        $sumTotalKredit = array_sum($total_kredit);

        $selisih = $sumTotalDebit-$sumTotalKredit;

        $kode_akun_opening_balance = '3-9999';
        $akun = getAccountMrpByCode($kode_akun_opening_balance);
        $this->db->set('id_jurnal', $id_jurnal);
        $this->db->set('jenis_transaksi', strtoupper($akun->group));
        $this->db->set('trs_kredit', $selisih);
        $this->db->set('trs_debet', 0);
        $this->db->set('trs_kredit_usd', 0);
        $this->db->set('trs_debet_usd', 0);
        $this->db->set('kode_rekening', $akun->coa);
        $this->db->set('currency', $currency);
        $this->db->insert('tb_jurnal_detail');

        if ($this->db->trans_status() === FALSE || $this->connection->trans_status() === FALSE)
          return FALSE;

        $this->db->trans_commit();
        $this->connection->trans_commit();

        return TRUE;
    }

    public function findById($category)
    {
        $select = array(
            'tb_saldo_awal.date',
            'tb_saldo_awal.transaction_number',
            'tb_saldo_awal.notes',
            'tb_saldo_awal.category',
            'tb_saldo_awal.created_by',
            'tb_saldo_awal.created_at',
            
        );

        $this->db->select($select);
        $this->db->from('tb_saldo_awal');
        $this->db->where('tb_saldo_awal.category', $category);
        $this->db->group_by($select);
        $query = $this->db->get();
        $saldo_awal = $query->unbuffered_row('array');

        $select = array(
            'tb_saldo_awal.date',
            'tb_saldo_awal.transaction_number',
            'tb_saldo_awal.notes',
            'tb_saldo_awal.category',
            'tb_saldo_awal.debit',
            'tb_saldo_awal.credit',
            'tb_saldo_awal.account_code as coa',
            'tb_master_coa.group'            
        );

        $this->db->select($select);
        $this->db->from('tb_saldo_awal');
        $this->db->where('tb_saldo_awal.category', $category);
        $this->db->join('tb_master_coa', 'tb_master_coa.coa = tb_saldo_awal.account_code','left');
        $this->db->order_by('tb_saldo_awal.account_code','asc');
        $query_item = $this->db->get();
        foreach ($query_item->result_array() as $key => $value) {
            $saldo_awal['items'][$key] = $value;
        }

        return $saldo_awal;
    }
  
}

/* End of file Account_Payable_Model.php */
/* Location: ./application/models/Account_Payable_Model.php */
