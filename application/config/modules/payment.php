<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['payment']['visible']        = TRUE;
$config['module']['payment']['main_warehouse'] = FALSE;
$config['module']['payment']['parent']         = 'account_payable';
$config['module']['payment']['label']          = 'Purpose Payment Purchase';
$config['module']['payment']['name']           = 'Purpose Payment Purchase';
$config['module']['payment']['route']          = 'payment';
$config['module']['payment']['view']           = config_item('module_path') .'payment/';
$config['module']['payment']['language']       = 'account_payable_lang';
$config['module']['payment']['helper']         = 'material_slip_helper';
$config['module']['payment']['table']          = 'tb_hutang';
$config['module']['payment']['model']          = 'Payment_Model';
$config['module']['payment']['permission']     = array(
  'index'               => 'PIC STAFF,AP STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
  'info'                => 'PIC STAFF,AP STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
  'print'               => 'PIC STAFF,AP STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
  'document'            => 'PIC STAFF,AP STAFF,SUPER ADMIN',//tambhan supervisor
  'payment'             => 'PIC STAFF,FINANCE SUPERVISOR,TELLER,SUPER ADMIN',
  'approval'            => 'FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
  'check'               => 'FINANCE MANAGER,SUPER ADMIN',
  'approve'             => 'VP FINANCE,SUPER ADMIN',
  'manage_attachment'   => 'PIC STAFF,SUPER ADMIN,TELLER,AP STAFF,FINANCE SUPERVISOR',
  'cancel'              => 'PIC STAFF,AP STAFF,SUPER ADMIN',
  'change_account'      => 'PIC STAFF,AP STAFF,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
);
