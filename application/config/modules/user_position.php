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
    'index'   => 'ADMIN,SUPER ADMIN,HRD',
    'create'  => 'ADMIN,SUPER ADMIN,HRD',
    'import'  => 'ADMIN,SUPER ADMIN,HRD',
    'edit'    => 'ADMIN,SUPER ADMIN,HRD',
    'info'    => 'ADMIN,SUPER ADMIN,HRD',
    'save'    => 'ADMIN,SUPER ADMIN,HRD',
    'delete'  => 'ADMIN,SUPER ADMIN,HRD',
);
