<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['capex_purchase_order']['visible']        = TRUE;
$config['module']['capex_purchase_order']['main_warehouse'] = TRUE;
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
  'index'     => 'SUPER ADMIN,BUDGETCONTROL,PIC STAFF',
  'info'      => 'SUPER ADMIN,BUDGETCONTROL,PIC STAFF',
  'print'     => 'SUPER ADMIN,BUDGETCONTROL,PIC STAFF',
  'document'  => 'SUPER ADMIN,PIC STAFF',  
  'approval'  => 'SUPER ADMIN,VP FINANCE,HEAD OF SCHOOL,CHIEF OPERATION OFFICER',
);
