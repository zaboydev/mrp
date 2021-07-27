<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['expense_closing_payment']['visible']        = TRUE;
$config['module']['expense_closing_payment']['main_warehouse'] = TRUE;
$config['module']['expense_closing_payment']['parent']         = 'expense';
$config['module']['expense_closing_payment']['label']          = 'Expense Closing Payment';
$config['module']['expense_closing_payment']['name']           = 'Expense Closing Payment';
$config['module']['expense_closing_payment']['route']          = 'expense_closing_payment';
$config['module']['expense_closing_payment']['view']           = config_item('module_path') .'expense/closing_payment/';
$config['module']['expense_closing_payment']['language']       = 'account_payable_lang';
$config['module']['expense_closing_payment']['helper']         = 'purchase_order_helper';
$config['module']['expense_closing_payment']['table']          = 'tb_po';
$config['module']['expense_closing_payment']['model']          = 'Expense_Closing_Payment_Model';
$config['module']['expense_closing_payment']['permission']     = array(
  'index'     => 'SUPER ADMIN,BUDGETCONTROL,TELLER,AP STAFF',
  'info'      => 'SUPER ADMIN,BUDGETCONTROL,TELLER,AP STAFF',
  'print'     => 'SUPER ADMIN,BUDGETCONTROL,TELLER,AP STAFF',
  'document'  => 'SUPER ADMIN,TELLER,AP STAFF',  
  'approval'  => 'SUPER ADMIN,VP FINANCE,HEAD OF SCHOOL,CHIEF OPERATION OFFICER',
);
