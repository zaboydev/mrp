<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['employee']['visible']     = TRUE;
$config['module']['employee']['main_warehouse']   = TRUE;
$config['module']['employee']['parent']      = 'master_data_hrd';
$config['module']['employee']['label']       = 'Employee';
$config['module']['employee']['name']        = 'employee';
$config['module']['employee']['route']       = 'employee';
$config['module']['employee']['view']        = config_item('module_path') .'employee/';
$config['module']['employee']['language']    = 'item_group_lang';
$config['module']['employee']['table']       = 'tb_master_employees';
$config['module']['employee']['model']       = 'Employee_Model';
$config['module']['employee']['permission']  = array(
    'index'   => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'create'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'import'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'edit'    => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'info'    => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'save'    => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'delete'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
);
