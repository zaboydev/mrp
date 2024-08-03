<?php defined('BASEPATH') or exit('No direct script access allowed');

class Payment_Jurnal extends MY_Controller
{
    protected $module;
    protected $id_item = 0;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['jurnal_payment'];
        $this->load->model($this->module['model'], 'model');
        $this->load->helper($this->module['helper']);
        $this->data['module'] = $this->module;
    }

    public function index()
    {
        $this->authorized($this->module, 'index');

        $this->data['page']['title']            = $this->module['label'];
        $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
        $this->data['grid']['data_source']      = site_url($this->module['route'] . '/index_data_source');
        $this->data['grid']['fixed_columns']    = 2;
        $this->data['grid']['summary_columns']  = array(6, 7);

        $this->data['grid']['order_columns']    = array();
        $this->render_view($this->module['view'] . '/index');
    }
    public function index_data_source()
    {
        if (is_granted($this->module, 'index') === FALSE) {
            $return['type'] = 'danger';
            $return['info'] = "You don't have permission to access this page!";
        } else {
            $entities = $this->model->getIndex();
            $data     = array();
            $no       = $_POST['start'];
            $quantity     = array();
            $unit_value   = array();
            $total_value  = array();
            $qty_debet    = array();
            $qty_kredit    = array();
            foreach ($entities as $row) {
                $no++;
                $col = array();
                $col[]  = print_number($no);
                $col[]  = print_string($row['no_jurnal']);
                $col[]  = print_string($row['vendor']);
                $col[]  = print_string($row['tanggal_jurnal']);
                $col[]  = print_string($row['kode_rekening']);
                $col[]  = print_string($row['jenis_transaksi']);
                $col[]  = print_number($row['kredit'], 2);
                $col[]  = print_number($row['debet'], 2);
                $qty_debet[] = $row['kredit'];
                $qty_kredit[] = $row['kredit'];

                $col['DT_RowId'] = 'row_' . $row['tanggal_jurnal'];
                $col['DT_RowData']['pkey']  = $row['tanggal_jurnal'];

                // if ($this->has_role($this->module, 'info')){
                //   $col['DT_RowAttr']['onClick']     = "window.location.href='".site_url('jurnal/detail_tanggal/'.$row['tanggal_jurnal'].'/'.$row['kode_rekening'])."'";
                //   $col['DT_RowAttr']['data-target'] = '#data-modal';
                //   $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] .'/info/'. $row['id']);
                // }

                $data[] = $col;
            }

            $result = array(
                "draw"            => $_POST['draw'],
                "recordsTotal"    => $this->model->countIndex(),
                "recordsFiltered" => $this->model->countIndexFiltered(),
                "data"            => $data,
                "total"           => array(
                    6 => print_number(array_sum($qty_debet), 2),
                    7 => print_number(array_sum($qty_kredit), 2),
                )
            );
        }

        echo json_encode($result);
    }
}
