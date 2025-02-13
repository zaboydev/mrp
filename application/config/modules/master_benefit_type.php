<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['master_benefit_type']['visible']     = TRUE;
$config['module']['master_benefit_type']['main_warehouse']   = TRUE;
$config['module']['master_benefit_type']['parent']      = 'master_data_hrd';
$config['module']['master_benefit_type']['label']       = 'Benefit Type';
$config['module']['master_benefit_type']['name']        = 'benefit_type';
$config['module']['master_benefit_type']['route']       = 'benefit_type';
$config['module']['master_benefit_type']['view']        = config_item('module_path') .'benefit_type/';
$config['module']['master_benefit_type']['language']    = 'item_group_lang';
$config['module']['master_benefit_type']['table']       = 'tb_master_benefit_type';
$config['module']['master_benefit_type']['model']       = 'Benefit_type_model';
$config['module']['master_benefit_type']['permission']  = array(
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
