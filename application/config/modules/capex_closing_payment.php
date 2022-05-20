<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['capex_closing_payment']['visible']        = TRUE;
$config['module']['capex_closing_payment']['main_warehouse'] = FALSE;
$config['module']['capex_closing_payment']['parent']         = 'capex';
$config['module']['capex_closing_payment']['label']          = 'Capex Purpose Payment';
$config['module']['capex_closing_payment']['name']           = 'Capex Purpose Payment';
$config['module']['capex_closing_payment']['route']          = 'capex_closing_payment';
$config['module']['capex_closing_payment']['view']           = config_item('module_path') .'capex/closing_payment/';
$config['module']['capex_closing_payment']['language']       = 'account_payable_lang';
$config['module']['capex_closing_payment']['helper']         = 'purchase_order_helper';
$config['module']['capex_closing_payment']['table']          = 'tb_po';
$config['module']['capex_closing_payment']['model']          = 'Capex_Closing_Payment_Model';
$config['module']['capex_closing_payment']['permission']     = array(
  'index'               => 'PIC STAFF,AP STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN,CHIEF OPERATION OFFICER,CHIEF OF FINANCE,VP FINANCE',
  'info'                => 'PIC STAFF,AP STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN,CHIEF OPERATION OFFICER,CHIEF OF FINANCE,VP FINANCE',
  'print'               => 'PIC STAFF,AP STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
  'document'            => 'PIC STAFF,AP STAFF,SUPER ADMIN',//tambhan supervisor
  'payment'             => 'PIC STAFF,FINANCE SUPERVISOR,TELLER,SUPER ADMIN',
  'approval'            => 'FINANCE MANAGER,SUPER ADMIN',
  'check'               => 'FINANCE MANAGER,SUPER ADMIN',
  'approve'             => 'VP FINANCE,SUPER ADMIN',
  'manage_attachment'   => 'PIC STAFF,SUPER ADMIN,TELLER,AP STAFF,FINANCE SUPERVISOR',
  'cancel'              => 'PIC STAFF,AP STAFF,SUPER ADMIN',
  'change_account'      => 'PIC STAFF,AP STAFF,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
  'review'              => ',CHIEF OPERATION OFFICER,CHIEF OF FINANCE,VP FINANCE'
);
