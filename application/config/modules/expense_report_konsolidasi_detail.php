<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['expense_report_konsolidasi_detail']['visible']        = TRUE;
$config['module']['expense_report_konsolidasi_detail']['main_warehouse'] = TRUE;
$config['module']['expense_report_konsolidasi_detail']['parent']         = 'expense';
$config['module']['expense_report_konsolidasi_detail']['label']          = 'Report Konsolidasi Detail';
$config['module']['expense_report_konsolidasi_detail']['name']           = 'Report Konsolidasi Detail';
$config['module']['expense_report_konsolidasi_detail']['route']          = 'expense_report_konsolidasi_detail';
$config['module']['expense_report_konsolidasi_detail']['view']           = config_item('module_path') .'expense/report_konsolidasi_detail/';
$config['module']['expense_report_konsolidasi_detail']['language']       = 'account_payable_lang';
$config['module']['expense_report_konsolidasi_detail']['helper']         = 'expense_request_helper';
$config['module']['expense_report_konsolidasi_detail']['table']          = 'tb_expense_purchase_requisitions';
$config['module']['expense_report_konsolidasi_detail']['model']          = 'Expense_Report_Model';
$config['module']['expense_report_konsolidasi_detail']['permission']     = array(
  'index'     => 'SUPER ADMIN',
  'info'      => 'SUPER ADMIN,BUDGETCONTROL,VP FINANCE,PIC STAFF,HEAD OF SCHOOL,CHIEF OPERATION OFFICER',
  'print'     => 'SUPER ADMIN,BUDGETCONTROL,VP FINANCE,PIC STAFF,HEAD OF SCHOOL,CHIEF OPERATION OFFICER',
  'document'  => 'SUPER ADMIN,PIC STAFF',
  'approval'  => 'VP FINANCE,HEAD OF SCHOOL,CHIEF OPERATION OFFICER'
);
