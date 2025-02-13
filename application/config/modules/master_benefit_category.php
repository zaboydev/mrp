<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['master_benefit_category']['visible']     = TRUE;
$config['module']['master_benefit_category']['main_warehouse']   = TRUE;
$config['module']['master_benefit_category']['parent']      = 'master_data_hrd';
$config['module']['master_benefit_category']['label']       = 'Benefit Category';
$config['module']['master_benefit_category']['name']        = 'benefit_category';
$config['module']['master_benefit_category']['route']       = 'benefit_category';
$config['module']['master_benefit_category']['view']        = config_item('module_path') .'benefit_category/';
$config['module']['master_benefit_category']['language']    = 'item_group_lang';
$config['module']['master_benefit_category']['table']       = 'tb_master_benefit_category';
$config['module']['master_benefit_category']['model']       = 'Benefit_category_model';
$config['module']['master_benefit_category']['permission']  = array(
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
