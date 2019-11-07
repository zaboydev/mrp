<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['purchase_order_report']['visible']        = TRUE;
$config['module']['purchase_order_report']['main_warehouse'] = TRUE;
$config['module']['purchase_order_report']['parent']         = 'report';
$config['module']['purchase_order_report']['label']          = 'Purchase Order Report';
$config['module']['purchase_order_report']['name']           = 'Purchase Order Report';
$config['module']['purchase_order_report']['route']          = 'purchase_order_report';
$config['module']['purchase_order_report']['view']           = config_item('module_path') . 'purchase_order/';
$config['module']['purchase_order_report']['language']       = 'prl_poe_lang';
$config['module']['purchase_order_report']['helper']         = 'purchase_order_helper';
$config['module']['purchase_order_report']['table']          = 'tb_purchase_orders';
$config['module']['purchase_order_report']['model']          = 'Purchase_Order_Model';
$config['module']['purchase_order_report']['permission']     = array(
  'index'     => 'PROCUREMENT,PIC PROCUREMENT,SUPER ADMIN,FINANCE',
  'info'      => 'PROCUREMENT,PIC PROCUREMENT,SUPER ADMIN,FINANCE',
  'print'     => 'PROCUREMENT,PIC PROCUREMENT,SUPER ADMIN,FINANCE',
  'document'  => 'PROCUREMENT,PIC PROCUREMENT,SUPER ADMIN,FINANCE',//tambhan supervisor
  'payment'   => 'FINANCE,SUPER ADMIN',//tambhan supervisor
);
