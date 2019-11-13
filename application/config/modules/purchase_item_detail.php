<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['purchase_item_detail']['visible']        = TRUE;
$config['module']['purchase_item_detail']['main_warehouse'] = TRUE;
$config['module']['purchase_item_detail']['parent']         = 'account_payable';
$config['module']['purchase_item_detail']['label']          = 'Purchase Item Detail';
$config['module']['purchase_item_detail']['name']           = 'Purchase Item Detail';
$config['module']['purchase_item_detail']['route']          = 'purchase_item_detail';
$config['module']['purchase_item_detail']['view']           = config_item('module_path') . 'purchase_item_detail/';
$config['module']['purchase_item_detail']['language']       = 'prl_poe_lang';
$config['module']['purchase_item_detail']['helper']         = 'material_slip_helper';
$config['module']['purchase_item_detail']['table']          = 'tb_purchase_orders';
$config['module']['purchase_item_detail']['model']          = 'Purchase_Item_Detail_Model';
$config['module']['purchase_item_detail']['permission']     = array(
  'index'     => 'PROCUREMENT,PIC PROCUREMENT,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,FINANCE MANAGER,SUPER ADMIN,VP FINANCE,OPERATION SUPPORT,CHIEF OPERATION OFFICER,FINANCE',
  'info'      => 'PROCUREMENT,PIC PROCUREMENT,SUPER ADMIN,FINANCE',
  'print'     => 'PROCUREMENT,PIC PROCUREMENT,SUPER ADMIN,FINANCE',
  'document'  => 'PROCUREMENT,PIC PROCUREMENT,SUPER ADMIN,FINANCE',//tambhan supervisor
  'payment'   => 'FINANCE,SUPER ADMIN',//tambhan supervisor
);
