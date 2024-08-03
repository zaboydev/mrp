<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['payment_voucher_purposed']['visible']        = TRUE;
$config['module']['payment_voucher_purposed']['main_warehouse'] = FALSE;
$config['module']['payment_voucher_purposed']['parent']         = 'finance';
$config['module']['payment_voucher_purposed']['label']          = 'Bank Register';
$config['module']['payment_voucher_purposed']['name']           = 'Bank Register';
$config['module']['payment_voucher_purposed']['route']          = 'payment_voucher_purposed';
$config['module']['payment_voucher_purposed']['view']           = config_item('module_path') .'payment_voucher_purposed/';
$config['module']['payment_voucher_purposed']['language']       = 'account_payable_lang';
$config['module']['payment_voucher_purposed']['helper']         = 'purchase_order_helper';
$config['module']['payment_voucher_purposed']['table']          = 'tb_po_payments';
$config['module']['payment_voucher_purposed']['model']          = 'Payment_Voucher_Purposed_Model';
$config['module']['payment_voucher_purposed']['permission']     = array(
  'index'               => 'TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN,AP STAFF',
  'info'                => 'TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN,AP STAFF',
  'print'               => 'TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN,AP STAFF',
  'document'            => 'SUPER ADMIN,FINANCE,AP STAFF',//tambhan supervisor
  'payment'             => 'FINANCE SUPERVISOR,TELLER,SUPER ADMIN',
  'approval'            => 'FINANCE MANAGER,SUPER ADMIN',
  'check'               => 'FINANCE MANAGER,SUPER ADMIN',
  'approve'             => 'VP FINANCE,SUPER ADMIN',
  'manage_attachment'   => 'SUPER ADMIN,TELLER,FINANCE SUPERVISOR,AP STAFF',
  'cancel'              => 'SUPER ADMIN',
  'change_account'      => 'FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
);
