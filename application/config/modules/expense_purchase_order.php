<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['expense_purchase_order']['visible']        = TRUE;
$config['module']['expense_purchase_order']['main_warehouse'] = FALSE;
$config['module']['expense_purchase_order']['parent']         = 'expense';
$config['module']['expense_purchase_order']['label']          = 'Expense Purchase Order';
$config['module']['expense_purchase_order']['name']           = 'Expense Purchase Order';
$config['module']['expense_purchase_order']['route']          = 'expense_purchase_order';
$config['module']['expense_purchase_order']['view']           = config_item('module_path') .'expense/order/';
$config['module']['expense_purchase_order']['language']       = 'account_payable_lang';
$config['module']['expense_purchase_order']['helper']         = 'purchase_order_helper';
$config['module']['expense_purchase_order']['table']          = 'tb_po';
$config['module']['expense_purchase_order']['model']          = 'Expense_Purchase_Order_Model';
$config['module']['expense_purchase_order']['permission']     = array(
  'index'     => 'ASSISTANT HOS,PROCUREMENT,PIC PROCUREMENT,SUPER ADMIN,PROCUREMENT MANAGER,FINANCE MANAGER,VP FINANCE,HEAD OF SCHOOL,CHIEF OPERATION OFFICER,CHIEF OF FINANCE',
  'info'      => 'ASSISTANT HOS,PROCUREMENT,PIC PROCUREMENT,SUPER ADMIN,PROCUREMENT MANAGER,FINANCE MANAGER,VP FINANCE,HEAD OF SCHOOL,CHIEF OPERATION OFFICER,CHIEF OF FINANCE',
  'print'     => 'FINANCE SUPERVISOR,AP STAFF,ASSISTANT HOS,PROCUREMENT,PIC PROCUREMENT,SUPER ADMIN,PROCUREMENT MANAGER,FINANCE MANAGER,VP FINANCE,HEAD OF SCHOOL,CHIEF OPERATION OFFICER,CHIEF OF FINANCE',
  'document'  => 'PROCUREMENT,PIC PROCUREMENT,SUPER ADMIN',  
  'order'     => 'PROCUREMENT,SUPER ADMIN,PIC PROCUREMENT',
  'approval'  => 'ASSISTANT HOS,SUPER ADMIN,VP FINANCE,HEAD OF SCHOOL,CHIEF OPERATION OFFICER,PROCUREMENT MANAGER,FINANCE MANAGER,CHIEF OF FINANCE',
  'manage_attachment' => 'PIC PROCUREMENT,PROCUREMENT,SUPER ADMIN,AP STAFF,FINANCE MANAGER,FINANCE'
);
