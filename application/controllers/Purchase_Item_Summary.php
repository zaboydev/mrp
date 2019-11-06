<?php defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_Item_Summary extends MY_Controller
{
    protected $module;
    protected $id_item = 0;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['purchase_item_summary'];
        $this->load->model($this->module['model'], 'model');
        $this->load->helper($this->module['helper']);
        $this->data['module'] = $this->module;
    }

    public function index()
    {
        $this->data['currency']                 = 'IDR';
        $this->data['page']['title']            = $this->module['label'];
        $this->data['account']                  = array();
        $this->data['items']                  = $this->model->getItems();
        $this->render_view($this->module['view'] . '/index');
    }

    public function getPo()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        $items_id = $this->input->post('items');
        if ($items_id != null && $items_id != 'all') {
            $items    = get_part_number($items_id);
        } else {
            $items    = $items_id;
        }
        $currency = $this->input->post('currency');
        $date = $this->input->post('date');
        $items = $this->model->getPurchaseItemSummary($items, $currency, $date);
        $this->data['items'] = $items;
        $return['info'] = $this->load->view($this->module['view'] . '/data', $this->data, TRUE);
        // $return['count_detail'] = $this->model->countdetailPoByVendor($vendor, $currency, $tipe);
        // $return['count_po'] = $this->model->countPoByVendor($vendor, $currency, $tipe);
        echo json_encode($return);
    }

    public function get_po_for_print($tipe,$currency,$items,$date=null)
    {
        // if ($this->input->is_ajax_request() === FALSE)
        //     redirect($this->modules['secure']['route'] . '/denied');

        // $vendor = $this->input->post('vendor');
        // $currency = $this->input->post('currency');
        // $date = $this->input->post('date');
        $items_id = $this->input->post('items');
        if ($items_id != null && $items_id != 'all') {
            $items    = get_part_number($items_id);
        } else {
            $items    = $items_id;
        }
        $items = $this->model->getPurchaseItemSummary($items, $currency, $date);
        $this->data['items'] = $items;
        $this->data['tipe'] = $tipe;
        $this->data['title']            = $this->module['label'];
        // $return['info'] = $this->load->view($this->module['view'] . '/print', $this->data, TRUE);
        // // $return['count_detail'] = $this->model->countdetailPoByVendor($vendor, $currency, $tipe);
        // // $return['count_po'] = $this->model->countPoByVendor($vendor, $currency, $tipe);
        // echo json_encode($return);

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
