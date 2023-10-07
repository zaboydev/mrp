<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['master_expense_duty']['visible']     = TRUE;
$config['module']['master_expense_duty']['main_warehouse']   = TRUE;
$config['module']['master_expense_duty']['parent']      = 'master_data_hrd';
$config['module']['master_expense_duty']['label']       = 'Expense Duty';
$config['module']['master_expense_duty']['name']        = 'expense_duty';
$config['module']['master_expense_duty']['route']       = 'expense_duty';
$config['module']['master_expense_duty']['view']        = config_item('module_path') .'expense_duty/';
$config['module']['master_expense_duty']['language']    = 'item_group_lang';
$config['module']['master_expense_duty']['table']       = 'tb_master_expense_duty';
$config['module']['master_expense_duty']['model']       = 'Expense_Duty_Model';
$config['module']['master_expense_duty']['permission']  = array(
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
