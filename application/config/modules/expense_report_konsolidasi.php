<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['expense_report_konsolidasi']['visible']        = TRUE;
$config['module']['expense_report_konsolidasi']['main_warehouse'] = TRUE;
$config['module']['expense_report_konsolidasi']['parent']         = 'expense';
$config['module']['expense_report_konsolidasi']['label']          = 'Expense Report Konsolidasi';
$config['module']['expense_report_konsolidasi']['name']           = 'Expense Report Konsolidasi';
$config['module']['expense_report_konsolidasi']['route']          = 'expense_report_konsolidasi';
$config['module']['expense_report_konsolidasi']['view']           = config_item('module_path') .'expense/report_konsolidasi/';
$config['module']['expense_report_konsolidasi']['language']       = 'account_payable_lang';
$config['module']['expense_report_konsolidasi']['helper']         = 'expense_request_helper';
$config['module']['expense_report_konsolidasi']['table']          = 'tb_expense_purchase_requisitions';
$config['module']['expense_report_konsolidasi']['model']          = 'Expense_Report_Model';
$config['module']['expense_report_konsolidasi']['permission']     = array(
  'index'     => 'SUPER ADMIN',
  'info'      => 'SUPER ADMIN,BUDGETCONTROL,VP FINANCE,PIC STAFF,HEAD OF SCHOOL,CHIEF OPERATION OFFICER',
  'print'     => 'SUPER ADMIN,BUDGETCONTROL,VP FINANCE,PIC STAFF,HEAD OF SCHOOL,CHIEF OPERATION OFFICER',
  'document'  => 'SUPER ADMIN,PIC STAFF',
  'approval'  => 'VP FINANCE,HEAD OF SCHOOL,CHIEF OPERATION OFFICER'
);
