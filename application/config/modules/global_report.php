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
  'index'     => 'PROCUREMENT,PIC PROCUREMENT,SUPERVISOR,FINANCE,OTHER,VP FINANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,SUPER ADMIN,FINANCE MANAGER',
  'info'      => 'PROCUREMENT,PIC PROCUREMENT,SUPERVISOR,FINANCE,OTHER,VP FINANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,SUPER ADMIN,FINANCE MANAGER',
  'print'     => 'PROCUREMENT,PIC PROCUREMENT,SUPERVISOR,FINANCE,OTHER,VP FINANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,SUPER ADMIN,FINANCE MANAGER',
  'document'  => 'PROCUREMENT,PIC PROCUREMENT,SUPERVISOR,HEAD OF SCHOOL,CHIEF OF FINANCE,SUPER ADMIN,FINANCE MANAGER',//tambhan supervisor
  'payment'   => 'FINANCE,SUPER ADMIN,FINANCE MANAGER',//tambhan supervisor
);
