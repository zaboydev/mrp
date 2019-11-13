<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['supplier_payment_history']['visible']        = TRUE;
$config['module']['supplier_payment_history']['main_warehouse'] = TRUE;
$config['module']['supplier_payment_history']['parent']         = 'account_payable';
$config['module']['supplier_payment_history']['label']          = 'Supplier Payment History';
$config['module']['supplier_payment_history']['name']           = 'Supplier Payment History';
$config['module']['supplier_payment_history']['route']          = 'supplier_payment_history';
$config['module']['supplier_payment_history']['view']           = config_item('module_path') . 'supplier_payment_history/';
$config['module']['supplier_payment_history']['language']       = 'prl_poe_lang';
$config['module']['supplier_payment_history']['helper']         = 'material_slip_helper';
$config['module']['supplier_payment_history']['table']          = 'tb_purchase_orders';
$config['module']['supplier_payment_history']['model']          = 'Purchase_Item_Detail_Model';
$config['module']['supplier_payment_history']['permission']     = array(
    'index'     => 'FINANCE,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,FINANCE MANAGER,SUPER ADMIN,VP FINANCE,OPERATION SUPPORT,CHIEF OPERATION OFFICER',
    
);
