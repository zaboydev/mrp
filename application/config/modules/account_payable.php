<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['account_payable']['visible']        = TRUE;
$config['module']['account_payable']['main_warehouse'] = TRUE;
$config['module']['account_payable']['parent']         = 'account_payable';
$config['module']['account_payable']['label']          = 'Account Payable';
$config['module']['account_payable']['name']           = 'Account Payable';
$config['module']['account_payable']['route']          = 'account_payable';
$config['module']['account_payable']['view']           = config_item('module_path') .'account_payable/';
$config['module']['account_payable']['language']       = 'account_payable_lang';
$config['module']['account_payable']['helper']         = 'material_slip_helper';
$config['module']['account_payable']['table']          = 'tb_hutang';
$config['module']['account_payable']['model']          = 'Account_Payable_Model';
$config['module']['account_payable']['permission']     = array(
  'index'     => 'FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER,AP STAFF',
  'info'      => 'FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER,AP STAFF',
  'print'     => 'FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER,AP STAFF',
  'document'  => 'FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER,AP STAFF',//tambhan supervisor
  'payment'   => 'FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER,AP STAFF',
  'import'    => 'FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER,AP STAFF',//tambhan supervisor
);
