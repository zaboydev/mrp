<?php defined('BASEPATH') or exit('No direct script access allowed');

class Expense_Report_Konsolidasi extends MY_Controller
{
    protected $module;
    protected $id_item = 0;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['expense_report_konsolidasi'];
        $this->load->model($this->module['model'], 'model');
        $this->load->helper($this->module['helper']);
        $this->data['module'] = $this->module;
    }

    public function index()
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

        $items = $this->model->getReportKonsolidasi();
        $this->data['items'] = $items;
        $return['info'] = $this->load->view($this->module['view'] . '/data', $this->data, TRUE);
        echo json_encode($return);
    }

    public function get_data_for_print($tipe)
    {
        // if ($this->input->is_ajax_request() === FALSE)
        //     redirect($this->modules['secure']['route'] . '/denied');

        $items = $this->model->getReportKonsolidasi();
        $active_month = find_budget_setting('Active Month');
        $active_year = find_budget_setting('Active Year');
        $this->data['items'] = $items;
        $this->data['title']            = $this->module['label'].' per '.getMonthName($active_month).' '.$active_year;
        $this->data['tipe'] = $tipe;

        $this->render_view($this->module['view'] . '/print', $this->data);

    }

    public function print_report()
    {
        $vendor = $this->input->post('vendor');
        $currency = $this->input->post('currency');
        $date = $this->input->post('date');
        $tipe = $this->input->post('tipe');
        $items = $items = $this->model->getPurchaseItem($vendor, $currency, $date);
        $this->data['items'] = $items;
        $this->data['tipe'] = $tipe;
        $this->load->view($this->module['view'] . '/print', $this->data);
    }
}
