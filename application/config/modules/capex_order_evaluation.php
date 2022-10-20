<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['capex_order_evaluation']['visible']        = TRUE;
$config['module']['capex_order_evaluation']['main_warehouse'] = FALSE;
$config['module']['capex_order_evaluation']['parent']         = 'capex';
$config['module']['capex_order_evaluation']['label']          = 'Capex Order Evaluation';
$config['module']['capex_order_evaluation']['name']           = 'Capex Order Evaluation';
$config['module']['capex_order_evaluation']['route']          = 'capex_order_evaluation';
$config['module']['capex_order_evaluation']['view']           = config_item('module_path') .'capex/order_evaluation/';
$config['module']['capex_order_evaluation']['language']       = 'account_payable_lang';
$config['module']['capex_order_evaluation']['helper']         = 'purchase_order_evaluation_helper';
$config['module']['capex_order_evaluation']['table']          = 'tb_capex_purchase_requisitions';
$config['module']['capex_order_evaluation']['model']          = 'Capex_Order_Evaluation_Model';
$config['module']['capex_order_evaluation']['permission']     = array(
  'index'     => 'SUPER ADMIN,PROCUREMENT,PIC PROCUREMENT,PROCUREMENT MANAGER',
  'info'      => 'SUPER ADMIN,PROCUREMENT,PIC PROCUREMENT,PROCUREMENT MANAGER',
  'print'     => 'FINANCE SUPERVISOR,AP STAFF,SUPER ADMIN,PROCUREMENT,PIC PROCUREMENT,PROCUREMENT MANAGER,FINANCE,FINANCE MANAGER,CHIEF OPERATION OFFICER,CHIEF OF FINANCE,HEAD OF SCHOOL,VP FINANCE',
  'document'  => 'SUPER ADMIN,PROCUREMENT,PIC PROCUREMENT,PROCUREMENT MANAGER',
  'approval'  => 'SUPER ADMIN,PROCUREMENT MANAGER',
  'delete'    => 'SUPER ADMIN',
  // 'approval'  => 
);
