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

    public function create($category = NULL)
    {
        $this->authorized($this->module, 'document');

        if ($category !== NULL) {
            $category = urldecode($category);
            $arrayCtg = explode('-',$category);

            $_SESSION['saldo']['items']               = getAccountByCategory($arrayCtg);
            $_SESSION['saldo']['category']            = $category;
            $_SESSION['saldo']['document_number']     = saldo_last_number();
            $_SESSION['saldo']['date']                = date('Y-m-d');
            $_SESSION['saldo']['created']             = config_item('auth_person_name');
            $_SESSION['saldo']['notes']               = NULL;
            $_SESSION['saldo']['total_amount']        = 0;
            $_SESSION['saldo']['cash_account']        = NULL;
            $_SESSION['saldo']['source']              = 'mrp';

            redirect($this->module['route'] . '/create');
        }

        if (!isset($_SESSION['saldo']))
            redirect($this->module['route']);

        $this->data['page']['content']    = $this->module['view'] . '/create';
        $this->data['page']['title']      = 'create cash request';

        $this->render_view($this->module['view'] . '/create');
    }

    public function set_document_number()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        $_SESSION['saldo']['document_number'] = $_GET['data'];
    }

    public function set_date()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        $_SESSION['saldo']['date'] = $_GET['data'];
    }

    public function set_notes()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        $_SESSION['saldo']['notes'] = $_GET['data'];
    }


    public function discard()
    {
        // $this->authorized($this->module, 'document');

        unset($_SESSION['saldo']);

        redirect($this->module['route']);
    }

    public function info($category)
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'info') === FALSE) {
            $return['type'] = 'denied';
            $return['info'] = "You don't have permission to access this data. You may need to login again.";
        } else {
            $entity = $this->model->findById($category);

            $this->data['entity'] = $entity;
            $this->data['category']     = $category;

            $return['type'] = 'success';
            $return['info'] = $this->load->view($this->module['view'] . '/info', $this->data, TRUE);
        }

        echo json_encode($return);
    }

    public function save()
    {
        if ($this->input->is_ajax_request() === FALSE)
          redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'document') === FALSE) {
              $return['type'] = 'danger';
              $return['info'] = "You don't have permission to access this page!";
        } else {
            if (!isset($_SESSION['saldo']['items']) || empty($_SESSION['saldo']['items'])) {
                $data['success'] = FALSE;
                $data['message'] = 'Please add at least 1 item!';
            } else {
                $_SESSION['saldo']['document_number'] = saldo_last_number().saldo_format_number();
                $document_number = $_SESSION['saldo']['document_number'];
                $errors = array();

                if (isset($_SESSION['saldo']['edit'])) {
                    $document_number = $_SESSION['saldo']['edit'].'-R';
                    $_SESSION['saldo']['document_number'] = $document_number;
                    if ($_SESSION['saldo']['edit'] != $document_number && $this->model->isDocumentNumberExists($document_number)) {
                        $errors[] = 'Duplicate Document Number: ' . $document_number . ' !';
                    }
                } else {
                    if ($this->model->isDocumentNumberExists($document_number)) {
                        $errors[] = 'Duplicate Document Number: ' . $document_number . ' !';
                    }
                }

                if (!empty($errors)) {
                    $data['success'] = FALSE;
                    $data['message'] = implode('<br />', $errors);
                } else {
                    if ($this->model->save()) {
                        unset($_SESSION['saldo']);

                        $data['success'] = TRUE;
                        $data['message'] = 'Document ' . $document_number . ' has been saved. You will redirected now.';
                    } else {
                        $data['success'] = FALSE;
                        $data['message'] = 'Error while saving this document. Please ask Technical Support.';
                    }
                }
            }
        }

        echo json_encode($data);
    }

    public function edit($category)
    {
        $this->authorized($this->module, 'document');

        $entity = $this->model->findById($category);

        // $this->data['entity'] = $entity;
        // $this->data['id']     = $id;    
        // $_SESSION['payment']['attachment']            = array();

        // $this->render_view($this->module['view'] . '/edit');

        $document_number  = sprintf('%06s', substr($entity['transaction_number'], 0, 6));

        if (isset($_SESSION['saldo']) === FALSE){
          $_SESSION['saldo']                     = $entity;
          $_SESSION['saldo']['date']             = $entity['date'];
          $_SESSION['saldo']['edit']             = $entity['transaction_number'];
          $_SESSION['saldo']['document_number']  = $document_number;
          $_SESSION['saldo']['notes']            = $entity['notes'];
          $_SESSION['saldo']['category']         = $entity['category'];
        }

        redirect($this->module['route'] .'/create');
    }

    public function print_pdf($id)
    {
        $this->authorized($this->module, 'print');

        $entity = $this->model->findById($id);

        $this->data['entity']           = $entity;
        $this->data['page']['title']    = strtoupper($this->module['label']);
        $this->data['page']['content']  = $this->module['view'] .'/print_pdf';

        $html = $this->load->view($this->pdf_theme, $this->data, true);

        $pdfFilePath = str_replace('/', '-', $entity['transaction_number']) .".pdf";

        $this->load->library('m_pdf');

        $pdf = $this->m_pdf->load(null, 'A4-L');
        $pdf->WriteHTML($html);
        $pdf->Output($pdfFilePath, "I");
    }

}
