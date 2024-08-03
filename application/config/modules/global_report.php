<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['global_report']['visible']        = TRUE;
$config['module']['global_report']['main_warehouse'] = TRUE;
$config['module']['global_report']['parent']         = 'report';
$config['module']['global_report']['label']          = 'Global Report';
$config['module']['global_report']['name']           = 'Global Report';
$config['module']['global_report']['route']          = 'global_report';
$config['module']['global_report']['view']           = config_item('module_path') .'global_report/';
$config['module']['global_report']['language']       = 'global_report_lang';
$config['module']['global_report']['helper']         = 'material_slip_helper';
$config['module']['global_report']['table']          = 'tb_purchase_orders';
$config['module']['global_report']['model']          = 'Global_Report_Model';
$config['module']['global_report']['permission']     = array(
  'index'     => 'PROCUREMENT,PIC PROCUREMENT,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,FINANCE MANAGER,SUPER ADMIN,VP FINANCE,OPERATION SUPPORT,CHIEF OPERATION OFFICER',
  'info'      => 'PROCUREMENT,PIC PROCUREMENT,SUPER ADMIN',
  'print'     => 'PROCUREMENT,PIC PROCUREMENT,SUPER ADMIN',
  'document'  => 'PROCUREMENT,PIC PROCUREMENT,SUPER ADMIN',//tambhan supervisor
  'payment'   => 'FINANCE,SUPER ADMIN,FINANCE MANAGER,SUPER ADMIN',//tambhan supervisor
);
