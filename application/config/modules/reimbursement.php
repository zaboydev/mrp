<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['reimbursement']['visible']         = true;
$config['module']['reimbursement']['main_warehouse']   = FALSE;
$config['module']['reimbursement']['parent']      = 'reimbursment';
$config['module']['reimbursement']['label']       = 'Reimbursement';
$config['module']['reimbursement']['name']        = 'reimbursement';
$config['module']['reimbursement']['route']       = 'reimbursement';
$config['module']['reimbursement']['view']        = config_item('module_path') .'reimbursement/';
$config['module']['reimbursement']['language']    = 'item_group_lang';
$config['module']['reimbursement']['helper']      = 'reimbursement_helper';
$config['module']['reimbursement']['table']       = 'tb_reimbursements';
$config['module']['reimbursement']['model']       = 'reimbursement_Model';
$config['module']['reimbursement']['permission']  = array(
    'index'   => 'ADMIN,SUPER ADMIN,HR MANAGER,HEAD OF SCHOOL,FINANCE MANAGER',
    'create'  => 'ADMIN,SUPER ADMIN',
    'import'  => 'ADMIN,SUPER ADMIN',
    'print'   => 'ADMIN,SUPER ADMIN,FINANCE MANAGER',
    'info'    => 'ADMIN,SUPER ADMIN,HR MANAGER,HEAD OF SCHOOL,FINANCE MANAGER',
    'save'    => 'ADMIN,SUPER ADMIN',
    'delete'  => 'ADMIN,SUPER ADMIN',
    'approval'  => 'SUPER ADMIN,HR MANAGER,FINANCE MANAGER',
);
