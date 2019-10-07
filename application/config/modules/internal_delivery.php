<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['internal_delivery']['visible']          = TRUE;
$config['module']['internal_delivery']['main_warehouse']   = FALSE;
$config['module']['internal_delivery']['parent']           = 'document';
$config['module']['internal_delivery']['label']            = 'Internal Delivery';
$config['module']['internal_delivery']['name']             = 'internal_delivery';
$config['module']['internal_delivery']['route']            = 'internal_delivery';
$config['module']['internal_delivery']['view']             = config_item('module_path') .'internal_delivery/';
$config['module']['internal_delivery']['language']         = 'internal_delivery_lang';
$config['module']['internal_delivery']['helper']           = 'internal_delivery_helper';
$config['module']['internal_delivery']['table']            = 'tb_receipts';
$config['module']['internal_delivery']['model']            = 'Internal_Delivery_Model';
$config['module']['internal_delivery']['permission']       = array(
  'index'     => 'PROCUREMENT,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'info'      => 'PROCUREMENT,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'document'  => 'PIC STOCK,SUPERVISOR,SUPER ADMIN',
  'print'     => 'PIC STOCK,SUPERVISOR,VP FINANCE,FINANCE,PIC PROCUREMENT,SUPER ADMIN',
  'delete'    => 'SUPERVISOR,VP FINANCE,SUPER ADMIN',
);
