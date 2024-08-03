<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pdf extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['pdf'];
    // $this->load->model($this->module['model'], 'model');
    $this->data['module'] = $this->module;
  }

  public function index()
  {
    if (!isset($_POST) or empty($_POST)){
      die("<p style='color: red; text-align: center; font-size: 2em; margin-top: 2em;'>You are not allowed refresh this page!</p>");
    }

    $title = json_decode($_POST['title'], true);

    $this->data['table']            = json_decode($_POST['datatable'], true);
    $this->data['page']['header']   = $title;
    $this->data['page']['title']    = $title;
    $this->data['page']['content']  = $this->module['view'] .'/index';

    $html = $this->load->view($this->pdf_theme, $this->data, TRUE);

    $pdfFilePath = $title .".pdf";

    $this->load->library('m_pdf');

    $pdf = $this->m_pdf->load(null, 'A4-L');
    $pdf->WriteHTML($html);
    $pdf->Output($pdfFilePath, "I");
  }

  public function preview()
  {
    if (!isset($_POST) or empty($_POST)){
      die("<p style='color: red; text-align: center; font-size: 2em; margin-top: 2em;'>You are not allowed refresh this page!</p>");
    }

    $title = json_decode($_POST['title'], true);

    $this->data['table'] = json_decode($_POST['table'], true);
    $this->data['page_title'] = $title;

    $html = $this->load->view($this->module['view'] .'/print', $this->data);
  }
}
