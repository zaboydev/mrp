<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['employee_benefit']['visible']         = TRUE;
$config['module']['employee_benefit']['main_warehouse']   = FALSE;
$config['module']['employee_benefit']['parent']      = 'master_data_hrd';
$config['module']['employee_benefit']['label']       = "Employee`s Benefit";
$config['module']['employee_benefit']['name']        = 'employee_benefit';
$config['module']['employee_benefit']['route']       = 'employee_benefit';
$config['module']['employee_benefit']['view']        = config_item('module_path') .'employee_benefit/';
$config['module']['employee_benefit']['language']    = 'item_group_lang';
$config['module']['employee_benefit']['table']       = 'tb_master_employee_benefit';
$config['module']['employee_benefit']['model']       = 'Employee_Benefit_Model';
$config['module']['employee_benefit']['permission']  = array(
    'index'   => 'ADMIN,SUPER ADMIN,HRD',
    'create'  => 'ADMIN,SUPER ADMIN,HRD',
    'import'  => 'ADMIN,SUPER ADMIN,HRD',
    'edit'    => 'ADMIN,SUPER ADMIN,HRD',
    'info'    => 'ADMIN,SUPER ADMIN,HRD',
    'save'    => 'ADMIN,SUPER ADMIN,HRD',
    'delete'  => 'ADMIN,SUPER ADMIN,HRD',
);
