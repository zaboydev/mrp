<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['payment']['visible']        = TRUE;
$config['module']['payment']['main_warehouse'] = TRUE;
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
  'index'     => 'AP STAFF,TELLER,FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER',
  'info'      => 'AP STAFF,TELLER,FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER',
  'print'     => 'FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER',
  'document'  => 'AP STAFF,SUPER ADMIN',//tambhan supervisor
  'payment'   => 'TELLER,SUPER ADMIN',
  'approval'  => 'FINANCE MANAGER,VP FINANCE,SUPER ADMIN',//tambhan supervisor
  'check'     => 'FINANCE MANAGER,SUPER ADMIN',
  'approve'   => 'VP FINANCE,SUPER ADMIN'
);
