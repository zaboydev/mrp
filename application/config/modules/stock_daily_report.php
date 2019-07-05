<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['stock_daily_report']['visible']         = FALSE;
$config['module']['stock_daily_report']['main_warehouse']  = TRUE;
$config['module']['stock_daily_report']['parent']          = 'stock';
$config['module']['stock_daily_report']['label']           = 'Stock Daily Report';
$config['module']['stock_daily_report']['name']            = 'stock_daily_report';
$config['module']['stock_daily_report']['route']           = 'stock_daily_report';
$config['module']['stock_daily_report']['view']            = config_item('module_path') .'stock_daily_report/';
$config['module']['stock_daily_report']['language']        = 'stock_daily_report_lang';
$config['module']['stock_daily_report']['table']           = 'tb_stock_daily_reports';
$config['module']['stock_daily_report']['model']           = 'Stock_Daily_Report_Model';
$config['module']['stock_daily_report']['permission']      = array(
  'index'     => 'SUPERVISOR,VP FINANCE,OTHER',
  'summary'   => 'SUPERVISOR,VP FINANCE,OTHER',
  'detail'    => 'SUPERVISOR,VP FINANCE,OTHER',
);
