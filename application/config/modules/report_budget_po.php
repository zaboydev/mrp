<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['report_budget_po']['visible']        = TRUE;
$config['module']['report_budget_po']['main_warehouse'] = TRUE;
$config['module']['report_budget_po']['parent']         = 'report';
$config['module']['report_budget_po']['label']          = 'Budget - PO Report';
$config['module']['report_budget_po']['name']           = 'Budget - PO Report';
$config['module']['report_budget_po']['route']          = 'global_report/budget_po_report';
$config['module']['report_budget_po']['view']           = config_item('module_path') .'global_report/';
$config['module']['report_budget_po']['language']       = 'global_report_lang';
$config['module']['report_budget_po']['helper']         = 'material_slip_helper';
$config['module']['report_budget_po']['table']          = 'tb_purchase_orders';
$config['module']['report_budget_po']['model']          = 'Global_Report_Model';
$config['module']['report_budget_po']['permission']     = array(
  'index'     => 'PIC PROCUREMENT,FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER',
  'info'      => 'PIC PROCUREMENT,FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER',
  'print'     => 'PIC PROCUREMENT,FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER',
  'document'  => 'PIC PROCUREMENT,HEAD OF SCHOOL,CHIEF OF FINANCE,SUPER ADMIN,FINANCE MANAGER',//tambhan supervisor
  'payment'   => 'FINANCE,SUPER ADMIN,FINANCE MANAGER',//tambhan supervisor
);
