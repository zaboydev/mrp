<?php defined('BASEPATH') or exit('No direct script access allowed');

class Receipt_Nota extends MY_Controller
{
  protected $module;
  protected $id_item = 0;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['receipt_nota'];
    $this->load->model($this->module['model'], 'model');
    $this->load->helper($this->module['helper']);
    $this->data['module'] = $this->module;
  }

  public function index($tipe_po=null)
  {
    $this->data['currency']                 = 'IDR';
    $this->data['page']['title']            = $this->module['label'];
    $this->data['account']                  = array();
    $this->data['suplier']                  = $this->model->getSuplier();
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/get_data');
    $this->data['grid']['data_export']      = site_url($this->module['route'] .'/export');
    $this->render_view($this->module['view'] . '/index');
  }

  public function get_data()
  {
    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->modules['secure']['route'] . '/denied');

    if(!empty($_GET['date'])){
      $getDate = $_GET['date'];
      $range_date  = explode('.', $getDate);
      $date = $range_date[0].' sd '.$range_date[1];
    }
    if(!empty($_GET['vendor']) && $_GET['vendor'] != 'all'){
        $vendor = $_GET['vendor'];
    }
        
    $entity = $this->model->getData();
    $this->data['entities'] = $entity;
    $this->data['periode']    = $date;
    $this->data['vendor']     = $vendor;
    $return['info'] = $this->load->view($this->module['view'] . '/data', $this->data, TRUE);
    echo json_encode($return);
  }

  public function get_data_api(){
    $entity = $this->model->getData();
    echo json_encode($entity);
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

      $return['open'] = site_url($this->module['route'] .'/get_export?'.'date='.$date.'&vendor='.$vendor.'&export='.$export);
          
    }

    echo json_encode($return);
  }  

  public function get_export(){
    $date = date('Y-m-d');
    $vendor = 'All Vendor';
    $entity = $this->model->getData();

    $this->data['entities']     = $entity;
    $this->data['tipe']         = $_GET['export'];
    if(!empty($_GET['date'])){
      $getDate = $_GET['date'];
      $range_date  = explode('.', $getDate);
      $date = $range_date[0].' sd '.$range_date[1];
    }
    if(!empty($_GET['vendor']) && $_GET['vendor'] != 'all'){
      $vendor = $_GET['vendor'];
    }
        
    $this->data['title']      = $this->module['label'];
    $this->data['periode']    = $date;
    
    $this->data['vendor']     = $vendor;
    $this->data['title_export']      = $this->module['label'].'-'.$date.'-'.$vendor;
    if($_GET['export']=='excel'){
      $this->render_view($this->module['view'] . '/print', $this->data);
    }else{
      // $this->render_view($this->module['view'] . '/print', $this->data);
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
}
