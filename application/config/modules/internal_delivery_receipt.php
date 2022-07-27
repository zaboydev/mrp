<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['internal_delivery_receipt']['visible']          = TRUE;
$config['module']['internal_delivery_receipt']['main_warehouse']   = FALSE;
$config['module']['internal_delivery_receipt']['parent']           = 'document';
$config['module']['internal_delivery_receipt']['label']            = 'Internal Delivery Receipt';
$config['module']['internal_delivery_receipt']['name']             = 'internal_delivery_receipt';
$config['module']['internal_delivery_receipt']['route']            = 'internal_delivery_receipt';
$config['module']['internal_delivery_receipt']['view']             = config_item('module_path') .'internal_delivery/';
$config['module']['internal_delivery_receipt']['language']         = 'internal_delivery_lang';
$config['module']['internal_delivery_receipt']['helper']           = 'internal_delivery_helper';
$config['module']['internal_delivery_receipt']['table']            = 'tb_receipts';
$config['module']['internal_delivery_receipt']['model']            = 'Internal_Delivery_Model';
$config['module']['internal_delivery_receipt']['permission']       = array(
  'index'     => 'PROCUREMENT,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN,MECHANIC',
  'info'      => 'PROCUREMENT,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN,MECHANIC',
  'document'  => 'PIC STOCK,SUPERVISOR,SUPER ADMIN,MECHANIC',
  'print'     => 'PIC STOCK,SUPERVISOR,VP FINANCE,FINANCE,PIC PROCUREMENT,SUPER ADMIN,MECHANIC',
  'delete'    => 'SUPERVISOR,VP FINANCE,SUPER ADMIN,MECHANIC',
  'approval'  => 'SUPERVISOR,PIC STOCK,MECHANIC,CHIEF OF MAINTANCE',
  'receipt'   => 'SUPER ADMIN,PIC STOCK'
);
