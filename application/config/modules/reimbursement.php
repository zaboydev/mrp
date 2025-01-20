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
    'index'   => 'PIC STAFF,ADMIN,SUPER ADMIN,HR MANAGER,HEAD OF SCHOOL,FINANCE MANAGER,VP FINANCE,CHIEF OF FINANCE,CHIEF OPERATION OFFICER',
    'create'  => 'PIC STAFF,ADMIN,SUPER ADMIN,HEAD OF SCHOOL,VP FINANCE,CHIEF OF FINANCE,CHIEF OPERATION OFFICER',
    'import'  => 'ADMIN,SUPER ADMIN,VP FINANCE',
    'print'   => 'PIC STAFF,ADMIN,SUPER ADMIN,FINANCE MANAGER,VP FINANCE,CHIEF OF FINANCE,CHIEF OPERATION OFFICER',
    'info'    => 'PIC STAFF,ADMIN,SUPER ADMIN,HR MANAGER,HEAD OF SCHOOL,FINANCE MANAGER,VP FINANCE,CHIEF OF FINANCE,CHIEF OPERATION OFFICER',
    'save'    => 'PIC STAFF,ADMIN,SUPER ADMIN,HEAD OF SCHOOL,VP FINANCE,CHIEF OF FINANCE,CHIEF OPERATION OFFICER',
    'delete'  => 'ADMIN,SUPER ADMIN,HEAD OF SCHOOL,VP FINANCE,CHIEF OF FINANCE,CHIEF OPERATION OFFICER',
    'approval'  => 'SUPER ADMIN,HR MANAGER,FINANCE MANAGER,CHIEF OF FINANCE,CHIEF OPERATION OFFICER',
);
