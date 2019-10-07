<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['stock_report']['visible']         = TRUE;
$config['module']['stock_report']['main_warehouse']  = FALSE;
$config['module']['stock_report']['parent']          = 'stock';
$config['module']['stock_report']['label']           = 'Stock Report';
$config['module']['stock_report']['name']            = 'stock_report';
$config['module']['stock_report']['route']           = 'stock_report';
$config['module']['stock_report']['view']            = config_item('module_path') .'stock_report/';
$config['module']['stock_report']['language']        = 'stock_report_lang';
$config['module']['stock_report']['table']           = 'tb_stock_reports';
$config['module']['stock_report']['model']           = 'Stock_Report_Model';
$config['module']['stock_report']['permission']      = array(
  'index'     => 'PROCUREMENT,PIC PROCUREMENT,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'summary'   => 'PROCUREMENT,PIC PROCUREMENT,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'detail'    => 'PROCUREMENT,PIC PROCUREMENT,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'document_non_shipping' => 'FINANCE, VP FINANCE,SUPER ADMIN'
);
