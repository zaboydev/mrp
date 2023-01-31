<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['reimbursement']['visible']         = TRUE;
$config['module']['reimbursement']['main_warehouse']   = TRUE;
$config['module']['reimbursement']['parent']      = 'perjalanan_dinas';
$config['module']['reimbursement']['label']       = 'Reimbursement';
$config['module']['reimbursement']['name']        = 'reimbursement';
$config['module']['reimbursement']['route']       = 'reimbursement';
$config['module']['reimbursement']['view']        = config_item('module_path') .'reimbursement/';
$config['module']['reimbursement']['language']    = 'item_group_lang';
$config['module']['reimbursement']['helper']      = 'reimbursement_helper';
$config['module']['reimbursement']['table']       = 'tb_reimbursements';
$config['module']['reimbursement']['model']       = 'reimbursement_Model';
$config['module']['reimbursement']['permission']  = array(
    'index'   => 'ADMIN,SUPER ADMIN,HR MANAGER,HEAD OF SCHOOL',
    'create'  => 'ADMIN,SUPER ADMIN',
    'import'  => 'ADMIN,SUPER ADMIN',
    'print'   => 'ADMIN,SUPER ADMIN',
    'info'    => 'ADMIN,SUPER ADMIN,HR MANAGER,HEAD OF SCHOOL',
    'save'    => 'ADMIN,SUPER ADMIN',
    'delete'  => 'ADMIN,SUPER ADMIN',
    'approval'  => 'SUPER ADMIN',
);
