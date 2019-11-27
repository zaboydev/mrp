<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['payment']['visible']        = TRUE;
$config['module']['payment']['main_warehouse'] = TRUE;
$config['module']['payment']['parent']         = 'account_payable';
$config['module']['payment']['label']          = 'Payment Purchase Order';
$config['module']['payment']['name']           = 'Payment Purchase Order';
$config['module']['payment']['route']          = 'payment';
$config['module']['payment']['view']           = config_item('module_path') .'payment/';
$config['module']['payment']['language']       = 'account_payable_lang';
$config['module']['payment']['helper']         = 'material_slip_helper';
$config['module']['payment']['table']          = 'tb_hutang';
$config['module']['payment']['model']          = 'Payment_Model';
$config['module']['payment']['permission']     = array(
  'index'     => 'FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER',
  'info'      => 'FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER',
  'print'     => 'FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER',
  'document'  => 'FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER',//tambhan supervisor
  'payment'   => 'FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER',
  'approval'  => 'FINANCE,SUPER ADMIN',//tambhan supervisor
);
