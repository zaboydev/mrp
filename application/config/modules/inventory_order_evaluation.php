<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['inventory_order_evaluation']['visible']        = TRUE;
$config['module']['inventory_order_evaluation']['main_warehouse'] = TRUE;
$config['module']['inventory_order_evaluation']['parent']         = 'inventory';
$config['module']['inventory_order_evaluation']['label']          = 'Inventory Order Evaluation';
$config['module']['inventory_order_evaluation']['name']           = 'Inventory Order Evaluation';
$config['module']['inventory_order_evaluation']['route']          = 'Inventory_Order_Evaluation';
$config['module']['inventory_order_evaluation']['view']           = config_item('module_path') .'inventory/order_evaluation/';
$config['module']['inventory_order_evaluation']['language']       = 'account_payable_lang';
$config['module']['inventory_order_evaluation']['helper']         = 'purchase_order_evaluation_helper';
$config['module']['inventory_order_evaluation']['table']          = 'tb_inventory_purchase_requisitions';
$config['module']['inventory_order_evaluation']['model']          = 'Inventory_Order_Evaluation_Model';
$config['module']['inventory_order_evaluation']['permission']     = array(
  'index'     => 'SUPER ADMIN,PROCUREMENT,PIC PROCUREMENT',
  'info'      => 'SUPER ADMIN,PROCUREMENT,PIC PROCUREMENT',
  'print'     => 'SUPER ADMIN,PROCUREMENT,PIC PROCUREMENT',
  'document'  => 'SUPER ADMIN,PROCUREMENT,PIC PROCUREMENT',
  'approval'  => 'SUPER ADMIN,VP FINANCE,HEAD OF SCHOOL,CHIEF OPERATION OFFICER',
  'delete'    => 'SUPER ADMIN',
  // 'approval'  => 
);
