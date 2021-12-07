<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['purchase_order']['visible']        = TRUE;
$config['module']['purchase_order']['main_warehouse'] = FALSE;
$config['module']['purchase_order']['parent']         = 'procurement';
$config['module']['purchase_order']['label']          = 'Purchase Order';
$config['module']['purchase_order']['name']           = 'purchase_order';
$config['module']['purchase_order']['route']          = 'purchase_order';
$config['module']['purchase_order']['view']           = config_item('module_path') .'purchase_order/';
$config['module']['purchase_order']['language']       = 'purchase_order_lang';
$config['module']['purchase_order']['helper']         = 'purchase_order_helper';
$config['module']['purchase_order']['table']          = 'tb_purchase_orders';
$config['module']['purchase_order']['model']          = 'Purchase_Order_Model';
$config['module']['purchase_order']['permission']     = array(
  'index'     => 'PIC PROCUREMENT,FINANCE,OTHER,VP FINANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,PROCUREMENT,SUPER ADMIN,FINANCE MANAGER,CHIEF OPERATION OFFICER',
  'info'      => 'PIC PROCUREMENT,FINANCE,OTHER,VP FINANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,PROCUREMENT,SUPER ADMIN,FINANCE MANAGER,CHIEF OPERATION OFFICER',
  'print'     => 'FINANCE SUPERVISOR,AP STAFF,PIC PROCUREMENT,FINANCE,OTHER,VP FINANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,PROCUREMENT,SUPER ADMIN,CHIEF OPERATION OFFICER,FINANCE MANAGER',
  'document'  => 'PIC PROCUREMENT,PROCUREMENT,SUPER ADMIN',//tambhan supervisor
  'payment'   => 'FINANCE,SUPER ADMIN',//tambhan supervisor
  'approval'  => 'FINANCE MANAGER,VP FINANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,CHIEF OPERATION OFFICER',
  'order'     => 'PROCUREMENT,SUPER ADMIN'
);
