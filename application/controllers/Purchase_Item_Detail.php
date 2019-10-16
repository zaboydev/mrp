<?php defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_Item_Detail extends MY_Controller
{
    protected $module;
    protected $id_item = 0;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['purchase_item_detail'];
        $this->load->model($this->module['model'], 'model');
        $this->load->helper($this->module['helper']);
        $this->data['module'] = $this->module;
    }

    public function index()
    {
        $this->data['currency']                 = 'IDR';
        $this->data['page']['title']            = $this->module['label'];
        $this->data['account']                  = array();
        $this->data['suplier']                  = $this->model->getSuplier();;
        $this->render_view($this->module['view'] . '/index');
    }

    public function getPo()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        $vendor = $this->input->post('vendor');
        $currency = $this->input->post('currency');
        $date = $this->input->post('date');
        $item = $this->model->getPurchaseItem($vendor, $currency, $date);
        $this->data['item'] = $item;
        $return['info'] = $this->load->view($this->module['view'] . '/data', $this->data, TRUE);
        // $return['count_detail'] = $this->model->countdetailPoByVendor($vendor, $currency, $tipe);
        // $return['count_po'] = $this->model->countPoByVendor($vendor, $currency, $tipe);
        echo json_encode($return);
    }
}
