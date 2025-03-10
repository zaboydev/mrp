<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['profile']['visible']     = FALSE;
$config['module']['profile']['main_warehouse']   = FALSE;
$config['module']['profile']['parent']      = 'profile';
$config['module']['profile']['label']       = 'Profile';
$config['module']['profile']['name']        = 'profile';
$config['module']['profile']['route']       = 'profile';
$config['module']['profile']['view']        = config_item('module_path') .'profile/';
$config['module']['profile']['language']    = 'item_group_lang';
$config['module']['profile']['table']       = 'tb_master_employees';
$config['module']['profile']['model']       = 'Profile_Model';
$config['module']['profile']['permission']  = array(
    'index'   => 'ADMIN JKT,ADMIN LUAR JKT,ADMIN DEPARTMENT,REIMBURSEMENT,PPC,HR STAFF,MECHANIC,FINANCE SUPERVISOR,PIC STAFF JKT,HEAD DEPT UNIQ JKT,PIC STAFF UNIQ JKT,OTHER,FINANCE,VP FINANCE,ADMIN,SUPERVISOR,PIC STOCK,PIC PROCUREMENT,CHIEF OF MAINTANCE,HEAD OF SCHOOL,CHIEF OF FINANCE,PROCUREMENT,SUPER ADMIN,FINANCE MANAGER,OPERATION SUPPORT,CHIEF OPERATION OFFICER,AP STAFF,TELLER,BUDGETCONTROL,PIC STAFF,PROCUREMENT MANAGER,ASSISTANT HOS',
    'create'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'import'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'edit'    => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'info'    => 'ADMIN JKT,ADMIN LUAR JKT,ADMIN DEPARTMENT,REIMBURSEMENT,ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'save'    => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'delete'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'contract'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'contract_create'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'contract_edit'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'contract_delete'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
);
