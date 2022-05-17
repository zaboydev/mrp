<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['daftar_pajak']['visible']     = TRUE;
$config['module']['daftar_pajak']['main_warehouse']   = TRUE;
$config['module']['daftar_pajak']['parent']      = 'master';
$config['module']['daftar_pajak']['label']       = 'Daftar Pajak';
$config['module']['daftar_pajak']['name']        = 'daftar_pajak';
$config['module']['daftar_pajak']['route']       = 'daftar_pajak';
$config['module']['daftar_pajak']['view']        = config_item('module_path') .'daftar_pajak/';
$config['module']['daftar_pajak']['language']    = 'item_group_lang';
$config['module']['daftar_pajak']['table']       = 'tb_master_daftar_pajak';
$config['module']['daftar_pajak']['model']       = 'Daftar_Pajak_Model';
$config['module']['daftar_pajak']['permission']  = array(
  'index'   => 'ADMIN,FINANCE,SUPER ADMIN,VP FINANCE,CHIEF OF FINANCE,FINANCE MANAGER,FINANCE SUPERVISOR',
  'create'  => 'ADMIN,FINANCE,SUPER ADMIN,VP FINANCE,CHIEF OF FINANCE,FINANCE MANAGER,FINANCE SUPERVISOR',
  'import'  => 'ADMIN,FINANCE,SUPER ADMIN,VP FINANCE,CHIEF OF FINANCE,FINANCE MANAGER,FINANCE SUPERVISOR',
  'edit'    => 'ADMIN,FINANCE,SUPER ADMIN,VP FINANCE,CHIEF OF FINANCE,FINANCE MANAGER,FINANCE SUPERVISOR',
  'info'    => 'ADMIN,FINANCE,SUPER ADMIN,VP FINANCE,CHIEF OF FINANCE,FINANCE MANAGER,FINANCE SUPERVISOR',
  'save'    => 'ADMIN,FINANCE,SUPER ADMIN,VP FINANCE,CHIEF OF FINANCE,FINANCE MANAGER,FINANCE SUPERVISOR',
  'delete'  => 'ADMIN,FINANCE,SUPER ADMIN,VP FINANCE,CHIEF OF FINANCE,FINANCE MANAGER,FINANCE SUPERVISOR',
  );
