<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['reimbursement_approval']['visible']         = true;
$config['module']['reimbursement_approval']['main_warehouse']   = FALSE;
$config['module']['reimbursement_approval']['parent']      = 'reimbursment';
$config['module']['reimbursement_approval']['label']       = 'Reimbursement Approval';
$config['module']['reimbursement_approval']['name']        = 'Reimbursement Approval';
$config['module']['reimbursement_approval']['route']       = 'reimbursement_approval';
$config['module']['reimbursement_approval']['view']        = config_item('module_path') .'reimbursement/approval';
$config['module']['reimbursement_approval']['language']    = 'item_group_lang';
$config['module']['reimbursement_approval']['helper']      = 'reimbursement_helper';
$config['module']['reimbursement_approval']['table']       = 'tb_reimbursements';
$config['module']['reimbursement_approval']['model']       = 'reimbursement_Model';
$config['module']['reimbursement_approval']['permission']  = array(
    'index'   => 'REIMBURSEMENT,PIC STAFF JKT,SUPERVISOR,PROCUREMENT,PROCUREMENT MANAGER,PIC STAFF,ADMIN,SUPER ADMIN,HR MANAGER,HEAD OF SCHOOL,FINANCE MANAGER,VP FINANCE,CHIEF OF FINANCE,CHIEF OPERATION OFFICER',
    'index_approval'   => 'REIMBURSEMENT,PIC STAFF JKT,SUPERVISOR,PROCUREMENT,PROCUREMENT MANAGER,PIC STAFF,ADMIN,SUPER ADMIN,HR MANAGER,HEAD OF SCHOOL,FINANCE MANAGER,VP FINANCE,CHIEF OF FINANCE,CHIEF OPERATION OFFICER',
    'create'  => 'REIMBURSEMENT,PIC STAFF JKT,HR MANAGER,HR STAFF,SUPERVISOR,PROCUREMENT,PROCUREMENT MANAGER,PIC STAFF,ADMIN,SUPER ADMIN,HEAD OF SCHOOL,VP FINANCE,CHIEF OF FINANCE,CHIEF OPERATION OFFICER',
    'import'  => 'ADMIN,SUPER ADMIN,VP FINANCE',
    'print'   => 'PIC STAFF JKT,SUPERVISOR,PROCUREMENT,PROCUREMENT MANAGER,PIC STAFF,ADMIN,SUPER ADMIN,FINANCE MANAGER,VP FINANCE,CHIEF OF FINANCE,CHIEF OPERATION OFFICER',
    'info'    => 'REIMBURSEMENT,PIC STAFF JKT,SUPERVISOR,PROCUREMENT,PROCUREMENT MANAGER,PIC STAFF,ADMIN,SUPER ADMIN,HR MANAGER,HEAD OF SCHOOL,FINANCE MANAGER,VP FINANCE,CHIEF OF FINANCE,CHIEF OPERATION OFFICER',
    'save'    => 'REIMBURSEMENT,PIC STAFF JKT,HR MANAGER,HR STAFF,SUPERVISOR,PROCUREMENT,PROCUREMENT MANAGER,PIC STAFF,ADMIN,SUPER ADMIN,HEAD OF SCHOOL,VP FINANCE,CHIEF OF FINANCE,CHIEF OPERATION OFFICER',
    'delete'  => 'PROCUREMENT,PROCUREMENT MANAGER,ADMIN,SUPER ADMIN,HEAD OF SCHOOL,VP FINANCE,CHIEF OF FINANCE,CHIEF OPERATION OFFICER',
    'approval'  => 'PROCUREMENT,PROCUREMENT MANAGER,SUPER ADMIN,HR MANAGER,FINANCE MANAGER,CHIEF OF FINANCE,CHIEF OPERATION OFFICER',
);
