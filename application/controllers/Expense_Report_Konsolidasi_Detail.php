<?php defined('BASEPATH') or exit('No direct script access allowed');

class Expense_Report_Konsolidasi_Detail extends MY_Controller
{
    protected $module;
    protected $id_item = 0;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['expense_report_konsolidasi_detail'];
        $this->load->model($this->module['model'], 'model');
        $this->load->helper($this->module['helper']);
        $this->data['module'] = $this->module;
    }

    public function index()
    {
        $this->data['currency']                 = 'IDR';
        $this->data['page']['title']            = $this->module['label'];
        $this->data['account']                  = array();
        
        $active_month = find_budget_setting('Active Month');
        $active_year = find_budget_setting('Active Year');
        if (is_granted($this->module, 'info') === TRUE) {
            $view = 'all';
        }else{
            $view = 'not_all';
        }
        
        $this->data['month'] = $active_month;
        $this->data['year'] = $active_year;
        $this->data['year_number'] = getYearNumber();
        $this->data['cost_centers'] = get_annual_cost_centers($active_year,$view);
        $this->data['colspan'] = count($this->data['cost_centers']);
        // $this->data['suplier']                  = $this->model->getSuplier();
        $this->render_view($this->module['view'] . '/index');
    }

    public function get_data()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] . '/denied');
        
        $active_month = find_budget_setting('Active Month');
        $active_year = find_budget_setting('Active Year');
        if (is_granted($this->module, 'info') === TRUE) {
            $view = 'all';
        }else{
            $view = 'not_all';
        }
        $items = $this->model->getReportKonsolidasiDetail($active_year,$active_month,$view);
        $this->data['items'] = $items;
        $return['info'] = $this->load->view($this->module['view'] . '/data', $this->data, TRUE);
        echo json_encode($return);
    }

    public function get_data_for_print($tipe)
    {
        // if ($this->input->is_ajax_request() === FALSE)
        //     redirect($this->modules['secure']['route'] . '/denied');

        $active_month = find_budget_setting('Active Month');
        $active_year = find_budget_setting('Active Year');
        if (is_granted($this->module, 'info') === TRUE) {
            $view = 'all';
        }else{
            $view = 'not_all';
        }
        $items = $this->model->getReportKonsolidasiDetail($active_year,$active_month,$view);
        $this->data['items'] = $items;
        $this->data['cost_centers'] = get_annual_cost_centers($active_year,$view);        
        $this->data['colspan'] = count($this->data['cost_centers']);
        $this->data['title']            = $this->module['label'].' '.getMonthName($active_month).' '.$active_year;
        $this->data['tipe'] = $tipe;

        $this->render_view($this->module['view'] . '/print', $this->data);

    }
}
