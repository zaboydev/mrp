<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['tujuan_perjalanan_dinas']['visible']         = TRUE;
$config['module']['tujuan_perjalanan_dinas']['main_warehouse']   = TRUE;
$config['module']['tujuan_perjalanan_dinas']['parent']      = 'master';
$config['module']['tujuan_perjalanan_dinas']['label']       = 'Tujuan Dinas';
$config['module']['tujuan_perjalanan_dinas']['name']        = 'tujuan_perjalanan_dinas';
$config['module']['tujuan_perjalanan_dinas']['route']       = 'tujuan_perjalanan_dinas';
$config['module']['tujuan_perjalanan_dinas']['view']        = config_item('module_path') .'tujuan_perjalanan_dinas/';
$config['module']['tujuan_perjalanan_dinas']['language']    = 'item_group_lang';
$config['module']['tujuan_perjalanan_dinas']['table']       = 'tb_master_tujuan_dinas';
$config['module']['tujuan_perjalanan_dinas']['model']       = 'Tujuan_Perjalanan_Dinas_Model';
$config['module']['tujuan_perjalanan_dinas']['permission']  = array(
    'index'   => 'ADMIN,SUPER ADMIN',
    'create'  => 'ADMIN,SUPER ADMIN',
    'import'  => 'ADMIN,SUPER ADMIN',
    'edit'    => 'ADMIN,SUPER ADMIN',
    'info'    => 'ADMIN,SUPER ADMIN',
    'save'    => 'ADMIN,SUPER ADMIN',
    'delete'  => 'ADMIN,SUPER ADMIN',
);
