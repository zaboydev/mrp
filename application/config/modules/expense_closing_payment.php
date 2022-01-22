<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['expense_closing_payment']['visible']        = TRUE;
$config['module']['expense_closing_payment']['main_warehouse'] = FALSE;
$config['module']['expense_closing_payment']['parent']         = 'expense';
$config['module']['expense_closing_payment']['label']          = 'Expense Payment';
$config['module']['expense_closing_payment']['name']           = 'Expense Payment';
$config['module']['expense_closing_payment']['route']          = 'expense_closing_payment';
$config['module']['expense_closing_payment']['view']           = config_item('module_path') .'expense/closing_payment/';
$config['module']['expense_closing_payment']['language']       = 'account_payable_lang';
$config['module']['expense_closing_payment']['helper']         = 'purchase_order_helper';
$config['module']['expense_closing_payment']['table']          = 'tb_po';
$config['module']['expense_closing_payment']['model']          = 'Expense_Closing_Payment_Model';
$config['module']['expense_closing_payment']['permission']     = array(
  'index'               => 'AP STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
  'info'                => 'AP STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
  'print'               => 'AP STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
  'document'            => 'AP STAFF,SUPER ADMIN',//tambhan supervisor
  'payment'             => 'FINANCE SUPERVISOR,TELLER,SUPER ADMIN',
  'approval'            => 'FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
  'check'               => 'FINANCE MANAGER,SUPER ADMIN',
  'approve'             => 'VP FINANCE,SUPER ADMIN',
  'manage_attachment'   => 'SUPER ADMIN,TELLER,AP STAFF,FINANCE SUPERVISOR',
  'cancel'              => 'AP STAFF,SUPER ADMIN',
  'change_account'      => 'AP STAFF,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
);
