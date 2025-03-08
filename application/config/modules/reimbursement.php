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
    'index'   => 'ADMIN DEPARTMENT,ADMIN JKT,ADMIN LUAR JKT,REIMBURSEMENT,PIC STAFF JKT,SUPERVISOR,PROCUREMENT,PROCUREMENT MANAGER,PIC STAFF,ADMIN,SUPER ADMIN,HR MANAGER,HEAD OF SCHOOL,FINANCE MANAGER,VP FINANCE,CHIEF OF FINANCE,CHIEF OPERATION OFFICER',
    'index_approval'   => 'SUPER ADMIN,HR MANAGER,HEAD OF SCHOOL,VP FINANCE,CHIEF OF FINANCE,CHIEF OPERATION OFFICER',
    'create'  => 'ADMIN DEPARTMENT,ADMIN JKT,ADMIN LUAR JKT,REIMBURSEMENT,PIC STAFF JKT,HR MANAGER,HR STAFF,SUPERVISOR,PROCUREMENT,PROCUREMENT MANAGER,PIC STAFF,ADMIN,SUPER ADMIN,HEAD OF SCHOOL,VP FINANCE,CHIEF OF FINANCE,CHIEF OPERATION OFFICER',
    'import'  => 'ADMIN,SUPER ADMIN,VP FINANCE',
    'print'   => 'ADMIN DEPARTMENT,ADMIN JKT,ADMIN LUAR JKT,REIMBBURSEMENT,PIC STAFF JKT,SUPERVISOR,PROCUREMENT,PROCUREMENT MANAGER,PIC STAFF,ADMIN,SUPER ADMIN,FINANCE MANAGER,VP FINANCE,CHIEF OF FINANCE,CHIEF OPERATION OFFICER',
    'info'    => 'ADMIN DEPARTMENT,ADMIN JKT,ADMIN LUAR JKT,REIMBURSEMENT,PIC STAFF JKT,SUPERVISOR,PROCUREMENT,PROCUREMENT MANAGER,PIC STAFF,ADMIN,SUPER ADMIN,HR MANAGER,HEAD OF SCHOOL,FINANCE MANAGER,VP FINANCE,CHIEF OF FINANCE,CHIEF OPERATION OFFICER',
    'save'    => 'ADMIN DEPARTMENT,ADMIN JKT,ADMIN LUAR JKT,REIMBURSEMENT,PIC STAFF JKT,HR MANAGER,HR STAFF,SUPERVISOR,PROCUREMENT,PROCUREMENT MANAGER,PIC STAFF,ADMIN,SUPER ADMIN,HEAD OF SCHOOL,VP FINANCE,CHIEF OF FINANCE,CHIEF OPERATION OFFICER',
    'delete'  => 'PROCUREMENT,PROCUREMENT MANAGER,ADMIN,SUPER ADMIN,HEAD OF SCHOOL,VP FINANCE,CHIEF OF FINANCE,CHIEF OPERATION OFFICER',
    'approval'  => 'PROCUREMENT,PROCUREMENT MANAGER,SUPER ADMIN,HR MANAGER,FINANCE MANAGER,CHIEF OF FINANCE,CHIEF OPERATION OFFICER',
);
