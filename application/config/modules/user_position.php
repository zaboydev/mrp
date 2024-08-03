<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['user_position']['visible']     = TRUE;
$config['module']['user_position']['main_warehouse']   = TRUE;
$config['module']['user_position']['parent']      = 'master_data_hrd';
$config['module']['user_position']['label']       = 'User Position';
$config['module']['user_position']['name']        = 'user_position';
$config['module']['user_position']['route']       = 'user_position';
$config['module']['user_position']['view']        = config_item('module_path') .'user_position/';
$config['module']['user_position']['language']    = 'item_group_lang';
$config['module']['user_position']['table']       = 'tb_master_positions';
$config['module']['user_position']['model']       = 'User_Position_Model';
$config['module']['user_position']['permission']  = array(
    'index'   => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'create'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'import'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'edit'    => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'info'    => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'save'    => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'delete'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
);
