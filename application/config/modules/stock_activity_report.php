<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['stock_activity_report']['visible']         = TRUE;
$config['module']['stock_activity_report']['main_warehouse']  = TRUE;
$config['module']['stock_activity_report']['parent']          = 'stock';
$config['module']['stock_activity_report']['label']           = 'Stock Card Inventory';
$config['module']['stock_activity_report']['name']            = 'stock_activity_report';
$config['module']['stock_activity_report']['route']           = 'stock_activity_report';
$config['module']['stock_activity_report']['view']            = config_item('module_path') .'stock_activity_report/';
$config['module']['stock_activity_report']['language']        = 'stock_activity_report_lang';
$config['module']['stock_activity_report']['table']           = 'tb_stock_activity_reports';
$config['module']['stock_activity_report']['model']           = 'Stock_Activity_Report_Model';
$config['module']['stock_activity_report']['permission']      = array(
  'index'     => 'SUPERVISOR,VP FINANCE,OTHER,SUPER ADMIN,PIC STOCK,PIC STAFF',
  'summary'   => 'SUPERVISOR,VP FINANCE,OTHER,SUPER ADMIN,PIC STOCK,PIC STAFF',
  'detail'    => 'SUPERVISOR,VP FINANCE,OTHER,SUPER ADMIN,PIC STOCK,PIC STAFF',
);
