<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['grn_payment']['visible']        = TRUE;
$config['module']['grn_payment']['main_warehouse'] = TRUE;
$config['module']['grn_payment']['parent']         = 'report';
$config['module']['grn_payment']['label']          = 'Purchase Register Vendor';
$config['module']['grn_payment']['name']           = 'Purchase Register Vendor';
$config['module']['grn_payment']['route']          = 'grn_payment';
$config['module']['grn_payment']['view']           = config_item('module_path') .'grn_payment/';
$config['module']['grn_payment']['language']       = 'prl_poe_lang';
$config['module']['grn_payment']['helper']         = 'material_slip_helper';
$config['module']['grn_payment']['table']          = 'tb_purchase_orders';
$config['module']['grn_payment']['model']          = 'Grn_Payment_Model';
$config['module']['grn_payment']['permission']     = array(
  'index'     => 'PROCUREMENT,PIC PROCUREMENT,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,FINANCE MANAGER,SUPER ADMIN,VP FINANCE,OPERATION SUPPORT,CHIEF OPERATION OFFICER',
  'info'      => 'PROCUREMENT,PIC PROCUREMENT,SUPER ADMIN',
  'print'     => 'PROCUREMENT,PIC PROCUREMENT,SUPER ADMIN',
  'document'  => 'PROCUREMENT,PIC PROCUREMENT,SUPER ADMIN',//tambhan supervisor
  'payment'   => 'FINANCE,SUPER ADMIN',//tambhan supervisor
);
