<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['expense_order_evaluation']['visible']        = TRUE;
$config['module']['expense_order_evaluation']['main_warehouse'] = TRUE;
$config['module']['expense_order_evaluation']['parent']         = 'expense';
$config['module']['expense_order_evaluation']['label']          = 'Expense Order Evaluation';
$config['module']['expense_order_evaluation']['name']           = 'Expense Order Evaluation';
$config['module']['expense_order_evaluation']['route']          = 'expense_order_evaluation';
$config['module']['expense_order_evaluation']['view']           = config_item('module_path') .'expense/order_evaluation/';
$config['module']['expense_order_evaluation']['language']       = 'account_payable_lang';
$config['module']['expense_order_evaluation']['helper']         = 'purchase_order_evaluation_helper';
$config['module']['expense_order_evaluation']['table']          = 'tb_expense_purchase_requisitions';
$config['module']['expense_order_evaluation']['model']          = 'Expense_Order_Evaluation_Model';
$config['module']['expense_order_evaluation']['permission']     = array(
  'index'     => 'SUPER ADMIN,PROCUREMENT,PIC PROCUREMENT,PROCUREMENT MANAGER',
  'info'      => 'SUPER ADMIN,PROCUREMENT,PIC PROCUREMENT,PROCUREMENT MANAGER',
  'print'     => 'SUPER ADMIN,PROCUREMENT,PIC PROCUREMENT,PROCUREMENT MANAGER',
  'document'  => 'SUPER ADMIN,PROCUREMENT,PIC PROCUREMENT',
  'approval'  => 'SUPER ADMIN,VP FINANCE,HEAD OF SCHOOL,CHIEF OPERATION OFFICER,PROCUREMENT MANAGER',
  'delete'    => 'SUPER ADMIN',
  // 'approval'  => 
);
