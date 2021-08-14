<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['expense_request']['visible']        = TRUE;
$config['module']['expense_request']['main_warehouse'] = TRUE;
$config['module']['expense_request']['parent']         = 'expense';
$config['module']['expense_request']['label']          = 'Expense Request';
$config['module']['expense_request']['name']           = 'Expense Request';
$config['module']['expense_request']['route']          = 'expense_request';
$config['module']['expense_request']['view']           = config_item('module_path') .'expense/request/';
$config['module']['expense_request']['language']       = 'account_payable_lang';
$config['module']['expense_request']['helper']         = 'expense_request_helper';
$config['module']['expense_request']['table']          = 'tb_expense_purchase_requisitions';
$config['module']['expense_request']['model']          = 'Expense_Request_Model';
$config['module']['expense_request']['permission']     = array(
  'index'     => 'SUPER ADMIN,BUDGETCONTROL,VP FINANCE,PIC STAFF,HEAD OF SCHOOL,CHIEF OPERATION OFFICER,FINANCE MANAGER',
  'info'      => 'SUPER ADMIN,BUDGETCONTROL,VP FINANCE,PIC STAFF,HEAD OF SCHOOL,CHIEF OPERATION OFFICER,FINANCE MANAGER',
  'print'     => 'SUPER ADMIN,BUDGETCONTROL,VP FINANCE,PIC STAFF,HEAD OF SCHOOL,CHIEF OPERATION OFFICER,FINANCE MANAGER',
  'document'  => 'SUPER ADMIN,PIC STAFF',
  'approval'  => 'BUDGETCONTROL,VP FINANCE,HEAD OF SCHOOL,CHIEF OPERATION OFFICER,FINANCE MANAGER'
);
