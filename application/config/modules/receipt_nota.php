<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['receipt_nota']['visible']        = TRUE;
$config['module']['receipt_nota']['main_warehouse'] = FALSE;
$config['module']['receipt_nota']['parent']         = 'account_payable';
$config['module']['receipt_nota']['label']          = 'Receipt Nota';
$config['module']['receipt_nota']['name']           = 'Receipt Nota';
$config['module']['receipt_nota']['route']          = 'receipt_nota';
$config['module']['receipt_nota']['view']           = config_item('module_path') .'receipt_nota/';
$config['module']['receipt_nota']['language']       = 'account_payable_lang';
$config['module']['receipt_nota']['helper']         = 'material_slip_helper';
$config['module']['receipt_nota']['table']          = 'tb_hutang';
$config['module']['receipt_nota']['model']          = 'Receipt_Nota_Model';
$config['module']['receipt_nota']['permission']     = array(
  'index'     => 'FINANCE SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER,AP STAFF',
  'info'      => 'FINANCE SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER,AP STAFF',
  'print'     => 'FINANCE SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER,AP STAFF',
  'document'  => 'FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER,AP STAFF',//tambhan supervisor
  'payment'   => 'FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER,AP STAFF',
  'import'    => 'FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER,AP STAFF',//tambhan supervisor
);
