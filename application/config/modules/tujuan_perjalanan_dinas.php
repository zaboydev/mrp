<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['tujuan_perjalanan_dinas']['visible']         = TRUE;
$config['module']['tujuan_perjalanan_dinas']['main_warehouse']   = TRUE;
$config['module']['tujuan_perjalanan_dinas']['parent']      = 'master_data_hrd';
$config['module']['tujuan_perjalanan_dinas']['label']       = 'Tujuan Dinas';
$config['module']['tujuan_perjalanan_dinas']['name']        = 'tujuan_perjalanan_dinas';
$config['module']['tujuan_perjalanan_dinas']['route']       = 'tujuan_perjalanan_dinas';
$config['module']['tujuan_perjalanan_dinas']['view']        = config_item('module_path') .'tujuan_perjalanan_dinas/';
$config['module']['tujuan_perjalanan_dinas']['language']    = 'item_group_lang';
$config['module']['tujuan_perjalanan_dinas']['table']       = 'tb_master_tujuan_dinas';
$config['module']['tujuan_perjalanan_dinas']['model']       = 'Tujuan_Perjalanan_Dinas_Model';
$config['module']['tujuan_perjalanan_dinas']['permission']  = array(
    'index'   => 'ADMIN,SUPER ADMIN,HRD',
    'create'  => 'ADMIN,SUPER ADMIN,HRD',
    'import'  => 'ADMIN,SUPER ADMIN,HRD',
    'edit'    => 'ADMIN,SUPER ADMIN,HRD',
    'info'    => 'ADMIN,SUPER ADMIN,HRD',
    'save'    => 'ADMIN,SUPER ADMIN,HRD',
    'delete'  => 'ADMIN,SUPER ADMIN,HRD',
);
