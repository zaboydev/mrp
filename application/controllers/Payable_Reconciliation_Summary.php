<?php defined('BASEPATH') or exit('No direct script access allowed');

class Payable_Reconciliation_Summary extends MY_Controller
{
    protected $module;
    protected $id_item = 0;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['payable_reconciliation_summary'];
        $this->load->model($this->module['model'], 'model');
        $this->load->helper($this->module['helper']);
        $this->data['module'] = $this->module;
    }

    public function index()
    {
        $this->data['currency']                 = 'IDR';
        $this->data['page']['title']            = $this->module['label'];
        $this->data['account']                  = array();
        $this->data['suplier']                  = $this->model->getSuplier();
        $this->data['grid']['data_source']      = site_url($this->module['route'] .'/getPo');
        $this->data['grid']['data_export']      = site_url($this->module['route'] .'/export');
        $this->render_view($this->module['view'] . '/index');
    }

    public function getPo()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        $vendor_id = $this->input->post('vendor');
        
        $items = $this->model->getPayableReconciliation();
        $this->data['items'] = $items;
        $return['info'] = $this->load->view($this->module['view'] . '/data', $this->data, TRUE);
        echo json_encode($return);
    }

    public function export()
    {
        if ($this->input->is_ajax_request() === FALSE)
          redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'index') === FALSE) {
            $return['type'] = 'denied';
            $return['info'] = "You don't have permission to access this data. You may need to login again.";
        } else {
            $export         = $_GET['export'];
            $date           = $_GET['date'];
            $vendor         = $_GET['vendor'];
            $currency       = $_GET['currency'];
            $method         = $_GET['method'];

            $return['open'] = site_url($this->module['route'] .'/get_export?'.'date='.$date.'&vendor='.$vendor.'&currency='.$currency.'&method='.$method.'&export='.$export);
          
        }

        echo json_encode($return);
    }  

    public function get_export(){
        $date = date('Y-m-d');
        $vendor = 'All Vendor';
        $currency = 'All Currency';
        $entity = $this->model->getPayableReconciliation();

        $this->data['items']     = $entity;
        $this->data['tipe']       = $_GET['export'];
        if(!empty($_GET['date'])){
            $date = date('Y-m-d');
        }
        if(!empty($_GET['vendor']) && $_GET['vendor'] != 'all'){
            $vendor = $_GET['vendor'];
        }
        if(!empty($_GET['currency']) && $_GET['currency'] != 'all'){
            $currency = $_GET['currency'];
        }
        $this->data['title']      = $this->module['label'];
        $this->data['periode']    = 'per '.$date;
        $this->data['currency']   = $currency;
        $this->data['vendor']     = $vendor;
        $this->data['method']     = $_GET['method'];
        $this->data['title_export']      = $this->module['label'].'-'.$date.'-'.$vendor.'-'.$currency;
        if($_GET['export']=='excel'){
            $this->render_view($this->module['view'] . '/print', $this->data);
        }else{
            $this->data['page']['title']    = strtoupper($this->module['label']);
            $this->data['page']['content']  = $this->module['view'] . '/print_pdf';
            $html = $this->load->view($this->pdf_theme, $this->data, true);

            $pdfFilePath = str_replace('/', '-', $this->data['title_export']) . ".pdf";

            $this->load->library('m_pdf');

            $pdf = $this->m_pdf->load(null, 'A4-L');
            $pdf->WriteHTML($html);
            $pdf->Output($pdfFilePath, "I");
        }
        
    }

    public function getPoApi(){
        $items = $this->model->getPayableReconciliation();
        echo json_encode($items);
    }
}
