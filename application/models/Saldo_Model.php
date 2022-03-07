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
  
}

/* End of file Account_Payable_Model.php */
/* Location: ./application/models/Account_Payable_Model.php */
