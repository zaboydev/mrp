<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['payable_reconciliation_summary']['visible']        = TRUE;
$config['module']['payable_reconciliation_summary']['main_warehouse'] = TRUE;
$config['module']['payable_reconciliation_summary']['parent']         = 'account_payable';
$config['module']['payable_reconciliation_summary']['label']          = 'Payable Reconciliation';
$config['module']['payable_reconciliation_summary']['name']           = 'Payable Reconciliation';
$config['module']['payable_reconciliation_summary']['route']          = 'payable_reconciliation_summary';
$config['module']['payable_reconciliation_summary']['view']           = config_item('module_path') . 'payable_reconciliation/';
$config['module']['payable_reconciliation_summary']['language']       = 'prl_poe_lang';
$config['module']['payable_reconciliation_summary']['helper']         = 'material_slip_helper';
$config['module']['payable_reconciliation_summary']['table']          = 'tb_purchase_orders';
$config['module']['payable_reconciliation_summary']['model']          = 'Purchase_Item_Detail_Model';
$config['module']['payable_reconciliation_summary']['permission']     = array(
    'index'     => 'FINANCE,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,FINANCE MANAGER,SUPER ADMIN,VP FINANCE,OPERATION SUPPORT,CHIEF OPERATION OFFICER',
    
);
