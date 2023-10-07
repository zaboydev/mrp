<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['master_transportation']['visible']     = TRUE;
$config['module']['master_transportation']['main_warehouse']   = TRUE;
$config['module']['master_transportation']['parent']      = 'master_data_hrd';
$config['module']['master_transportation']['label']       = 'Transportation';
$config['module']['master_transportation']['name']        = 'transportation';
$config['module']['master_transportation']['route']       = 'transportation';
$config['module']['master_transportation']['view']        = config_item('module_path') .'transportation/';
$config['module']['master_transportation']['language']    = 'item_group_lang';
$config['module']['master_transportation']['table']       = 'tb_master_transportations';
$config['module']['master_transportation']['model']       = 'Transportation_Model';
$config['module']['master_transportation']['permission']  = array(
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
