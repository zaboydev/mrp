<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['level']['visible']     = TRUE;
$config['module']['level']['main_warehouse']   = TRUE;
$config['module']['level']['parent']      = 'master_data_hrd';
$config['module']['level']['label']       = 'Level';
$config['module']['level']['name']        = 'level';
$config['module']['level']['route']       = 'level';
$config['module']['level']['view']        = config_item('module_path') .'level/';
$config['module']['level']['language']    = 'item_group_lang';
$config['module']['level']['table']       = 'tb_master_levels';
$config['module']['level']['model']       = 'Level_Model';
$config['module']['level']['permission']  = array(
    'index'   => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'create'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'import'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'edit'    => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'info'    => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'save'    => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'delete'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
);
