<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['internal_delivery_shipping']['visible']          = TRUE;
$config['module']['internal_delivery_shipping']['main_warehouse']   = FALSE;
$config['module']['internal_delivery_shipping']['parent']           = 'document';
$config['module']['internal_delivery_shipping']['label']            = 'Shipping Internal Delivery';
$config['module']['internal_delivery_shipping']['name']             = 'internal_delivery_shipping';
$config['module']['internal_delivery_shipping']['route']            = 'internal_delivery_shipping';
$config['module']['internal_delivery_shipping']['view']             = config_item('module_path') .'internal_delivery/shipping/';
$config['module']['internal_delivery_shipping']['language']         = 'internal_delivery_lang';
$config['module']['internal_delivery_shipping']['helper']           = 'internal_delivery_helper';
$config['module']['internal_delivery_shipping']['table']            = 'tb_receipts';
$config['module']['internal_delivery_shipping']['model']            = 'Internal_Delivery_Shipping_Model';
$config['module']['internal_delivery_shipping']['permission']       = array(
  'index'     => 'PROCUREMENT,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN,MECHANIC',
  'info'      => 'PROCUREMENT,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN,MECHANIC',
  'document'  => 'PIC STOCK,SUPERVISOR,SUPER ADMIN,MECHANIC',
  'print'     => 'PIC STOCK,SUPERVISOR,VP FINANCE,FINANCE,PIC PROCUREMENT,SUPER ADMIN,MECHANIC',
  'delete'    => 'SUPERVISOR,VP FINANCE,SUPER ADMIN,MECHANIC',
  'approval'  => 'SUPERVISOR,PIC STOCK,MECHANIC,CHIEF OF MAINTANCE',
  'receipt'   => 'SUPER ADMIN,PIC STOCK,SUPERVISOR'
);
