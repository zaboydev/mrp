<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['account_payable_mutation']['visible']        = FALSE;
$config['module']['account_payable_mutation']['main_warehouse'] = TRUE;
$config['module']['account_payable_mutation']['parent']         = 'account_payable';
$config['module']['account_payable_mutation']['label']          = 'Account Payable Mutation';
$config['module']['account_payable_mutation']['name']           = 'Account Payable Mutation';
$config['module']['account_payable_mutation']['route']          = 'account_payable_mutation';
$config['module']['account_payable_mutation']['view']           = config_item('module_path') . 'account_payable_mutation/';
$config['module']['account_payable_mutation']['language']       = 'prl_poe_lang';
$config['module']['account_payable_mutation']['helper']         = 'material_slip_helper';
$config['module']['account_payable_mutation']['table']          = 'tb_purchase_orders';
$config['module']['account_payable_mutation']['model']          = 'Purchase_Item_Detail_Model';
$config['module']['account_payable_mutation']['permission']     = array(
    'index'     => 'PROCUREMENT,FINANCE,VP FINANCE,SUPER ADMIN,FINANCE MANAGER',
    
);
