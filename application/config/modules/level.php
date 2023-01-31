<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['level']['visible']     = TRUE;
$config['module']['level']['main_warehouse']   = TRUE;
$config['module']['level']['parent']      = 'master';
$config['module']['level']['label']       = 'Level';
$config['module']['level']['name']        = 'level';
$config['module']['level']['route']       = 'level';
$config['module']['level']['view']        = config_item('module_path') .'level/';
$config['module']['level']['language']    = 'item_group_lang';
$config['module']['level']['table']       = 'tb_master_levels';
$config['module']['level']['model']       = 'Level_Model';
$config['module']['level']['permission']  = array(
    'index'   => 'ADMIN,SUPER ADMIN',
    'create'  => 'ADMIN,SUPER ADMIN',
    'import'  => 'ADMIN,SUPER ADMIN',
    'edit'    => 'ADMIN,SUPER ADMIN',
    'info'    => 'ADMIN,SUPER ADMIN',
    'save'    => 'ADMIN,SUPER ADMIN',
    'delete'  => 'ADMIN,SUPER ADMIN',
);
