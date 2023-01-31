<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['employee']['visible']     = TRUE;
$config['module']['employee']['main_warehouse']   = TRUE;
$config['module']['employee']['parent']      = 'master';
$config['module']['employee']['label']       = 'Employee';
$config['module']['employee']['name']        = 'employee';
$config['module']['employee']['route']       = 'employee';
$config['module']['employee']['view']        = config_item('module_path') .'employee/';
$config['module']['employee']['language']    = 'item_group_lang';
$config['module']['employee']['table']       = 'tb_master_employees';
$config['module']['employee']['model']       = 'Employee_Model';
$config['module']['employee']['permission']  = array(
    'index'   => 'ADMIN,SUPER ADMIN',
    'create'  => 'ADMIN,SUPER ADMIN',
    'import'  => 'ADMIN,SUPER ADMIN',
    'edit'    => 'ADMIN,SUPER ADMIN',
    'info'    => 'ADMIN,SUPER ADMIN',
    'save'    => 'ADMIN,SUPER ADMIN',
    'delete'  => 'ADMIN,SUPER ADMIN',
);
