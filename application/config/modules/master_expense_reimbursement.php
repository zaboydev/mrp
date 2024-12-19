<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['master_expense_reimbursement']['visible']     = TRUE;
$config['module']['master_expense_reimbursement']['main_warehouse']   = TRUE;
$config['module']['master_expense_reimbursement']['parent']      = 'master_data_hrd';
$config['module']['master_expense_reimbursement']['label']       = 'Expense Reimbursement';
$config['module']['master_expense_reimbursement']['name']        = 'expense_reimbursement';
$config['module']['master_expense_reimbursement']['route']       = 'expense_reimbursement';
$config['module']['master_expense_reimbursement']['view']        = config_item('module_path') .'expense_reimbursement/';
$config['module']['master_expense_reimbursement']['language']    = 'item_group_lang';
$config['module']['master_expense_reimbursement']['table']       = 'tb_master_expense_reimbursement';
$config['module']['master_expense_reimbursement']['model']       = 'Expense_Reimbursement_Model';
$config['module']['master_expense_reimbursement']['permission']  = array(
    'index'   => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'create'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'import'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'edit'    => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'info'    => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'save'    => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'delete'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'contract'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'contract_create'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'contract_edit'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'contract_delete'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
);
