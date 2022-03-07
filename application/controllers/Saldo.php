<?php defined('BASEPATH') or exit('No direct script access allowed');

class Saldo extends MY_Controller
{
    protected $module;
    protected $id_item = 0;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['saldo'];
        $this->load->model($this->module['model'], 'model');
        $this->load->helper($this->module['helper']);
        $this->load->library('upload');
        $this->data['module'] = $this->module;
    }

    public function index($tipe_po=null)
    {
        $this->data['currency']                 = 'IDR';
        $this->data['page']['title']            = $this->module['label'];
        $this->data['account']                  = array();
        // $this->data['suplier']                  = $this->model->getSuplier();
        $this->render_view($this->module['view'] . '/index');
    }

    public function get_data()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        $items = $this->model->getTransaksi();
        $saldo_awal = $this->model->getSaldoAwal();
        $saldo_akhir = $this->model->getSaldoAkhir();
        $start_date  = $this->input->post('start_date');
        $end_date  = $this->input->post('end_date');
        $tanggal_saldo_awal        = strtotime('-1 day',strtotime($start_date));
        $this->data['items'] = $items;
        $this->data['saldo_awal'] = $saldo_awal;
        $this->data['saldo_akhir'] = $saldo_akhir;
        $this->data['tanggal_saldo_awal'] = date('Y-m-d', $tanggal_saldo_awal);
        $this->data['tanggal_saldo_akhir'] = $end_date;
        $return['info'] = $this->load->view($this->module['view'] . '/data', $this->data, TRUE);
        echo json_encode($return);
    }

}
