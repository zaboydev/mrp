<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['payment']['visible']        = TRUE;
$config['module']['payment']['main_warehouse'] = TRUE;
$config['module']['payment']['parent']         = 'finance';
$config['module']['payment']['label']          = 'Payment';
$config['module']['payment']['name']           = 'Payment';
$config['module']['payment']['route']          = 'payment';
$config['module']['payment']['view']           = config_item('module_path') .'payment/';
$config['module']['payment']['language']       = 'account_payable_lang';
$config['module']['payment']['helper']         = 'material_slip_helper';
$config['module']['payment']['table']          = 'tb_hutang';
$config['module']['payment']['model']          = 'Payment_Model';
$config['module']['payment']['permission']     = array(
  'index'     => 'PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,CHIEF OF MAINTANCE,SUPER ADMIN',
  'info'      => 'PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,CHIEF OF MAINTANCE,SUPER ADMIN',
  'print'     => 'PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,CHIEF OF MAINTANCE,SUPER ADMIN',
  'document'  => 'PIC STOCK,SUPERVISOR,CHIEF OF MAINTANCE,SUPER ADMIN',//tambhan supervisor
  'payment'   => 'FINANCE,CHIEF OF MAINTANCE,SUPER ADMIN',
  'import'    => 'PIC STOCK,SUPERVISOR,VP FINANCE,CHIEF OF MAINTANCE,SUPER ADMIN',//tambhan supervisor
);
