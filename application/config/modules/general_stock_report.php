<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['general_stock_report']['visible']           = TRUE;
$config['module']['general_stock_report']['main_warehouse']    = TRUE;
$config['module']['general_stock_report']['parent']            = 'stock';
$config['module']['general_stock_report']['label']             = 'General Stock Report';
$config['module']['general_stock_report']['name']              = 'general_stock_report';
$config['module']['general_stock_report']['route']             = 'general_stock_report';
$config['module']['general_stock_report']['view']              = config_item('module_path') .'general_stock_report/';
$config['module']['general_stock_report']['language']          = 'stock_general_lang';
$config['module']['general_stock_report']['table']             = 'tb_stocks';
$config['module']['general_stock_report']['model']             = 'General_Stock_Report_Model';
$config['module']['general_stock_report']['permission']        = array(
  'index'       => 'PROCUREMENT,PIC PROCUREMENT,SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN',
  'show'        => 'PROCUREMENT,PIC PROCUREMENT,SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN',
  'document_non_shipping' => 'SUPER ADMIN,FINANCE,VP FINANCE',
);
