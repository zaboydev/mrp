<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['capex_purchase_order']['visible']        = TRUE;
$config['module']['capex_purchase_order']['main_warehouse'] = FALSE;
$config['module']['capex_purchase_order']['parent']         = 'capex';
$config['module']['capex_purchase_order']['label']          = 'Capex Purchase Order';
$config['module']['capex_purchase_order']['name']           = 'Capex Purchase Order';
$config['module']['capex_purchase_order']['route']          = 'capex_purchase_order';
$config['module']['capex_purchase_order']['view']           = config_item('module_path') .'capex/order/';
$config['module']['capex_purchase_order']['language']       = 'account_payable_lang';
$config['module']['capex_purchase_order']['helper']         = 'purchase_order_helper';
$config['module']['capex_purchase_order']['table']          = 'tb_po';
$config['module']['capex_purchase_order']['model']          = 'Capex_Purchase_Order_Model';
$config['module']['capex_purchase_order']['permission']     = array(
  'index'     => 'ASSISTANT HOS,PROCUREMENT,PIC PROCUREMENT,SUPER ADMIN,PROCUREMENT MANAGER,FINANCE MANAGER,VP FINANCE,HEAD OF SCHOOL,CHIEF OPERATION OFFICER,CHIEF OF FINANCE',
  'info'      => 'ASSISTANT HOS,PROCUREMENT,PIC PROCUREMENT,SUPER ADMIN,PROCUREMENT MANAGER,FINANCE MANAGER,VP FINANCE,HEAD OF SCHOOL,CHIEF OPERATION OFFICER,CHIEF OF FINANCE',
  'print'     => 'ASSISTANT HOS,PROCUREMENT,PIC PROCUREMENT,SUPER ADMIN,PROCUREMENT MANAGER,FINANCE MANAGER,VP FINANCE,HEAD OF SCHOOL,CHIEF OPERATION OFFICER,CHIEF OF FINANCE',
  'document'  => 'PROCUREMENT,PIC PROCUREMENT,SUPER ADMIN',  
  'approval'  => 'ASSISTANT HOS,SUPER ADMIN,VP FINANCE,HEAD OF SCHOOL,CHIEF OPERATION OFFICER,PROCUREMENT MANAGER,FINANCE MANAGER,CHIEF OF FINANCE',
);
