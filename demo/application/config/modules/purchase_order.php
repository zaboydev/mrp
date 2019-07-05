<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['purchase_order']['visible']        = TRUE;
$config['module']['purchase_order']['main_warehouse'] = TRUE;
$config['module']['purchase_order']['parent']         = 'document';
$config['module']['purchase_order']['label']          = 'Purchase Order';
$config['module']['purchase_order']['name']           = 'purchase_order';
$config['module']['purchase_order']['route']          = 'purchase_order';
$config['module']['purchase_order']['view']           = config_item('module_path') .'purchase_order/';
$config['module']['purchase_order']['language']       = 'purchase_order_lang';
$config['module']['purchase_order']['helper']         = 'purchase_order_helper';
$config['module']['purchase_order']['table']          = 'tb_purchase_orders';
$config['module']['purchase_order']['model']          = 'Purchase_Order_Model';
$config['module']['purchase_order']['permission']     = array(
  'index'     => 'PIC PROCUREMENT,SUPERVISOR,FINANCE,OTHER',
  'info'      => 'PIC PROCUREMENT,SUPERVISOR,FINANCE,OTHER',
  'print'     => 'PIC PROCUREMENT,SUPERVISOR,FINANCE,OTHER',
  'document'  => 'PIC PROCUREMENT',
  'payment'   => 'FINANCE',
);
