<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['master_akun']['visible']     = TRUE;
$config['module']['master_akun']['main_warehouse']   = TRUE;
$config['module']['master_akun']['parent']      = 'master';
$config['module']['master_akun']['label']       = 'Set Up Account';
$config['module']['master_akun']['name']        = 'Set Up Account';
$config['module']['master_akun']['route']       = 'master_akun';
$config['module']['master_akun']['view']        = config_item('module_path') . 'master_akun/';
$config['module']['master_akun']['language']    = 'item_group_lang';
$config['module']['master_akun']['table']       = 'tb_master_akun';
$config['module']['master_akun']['model']       = 'Master_Akun_Model';
$config['module']['master_akun']['permission']  = array(
  'index'   => 'ADMIN,FINANCE,SUPER ADMIN',
//   'create'  => 'ADMIN,FINANCE,SUPER ADMIN',
//   'import'  => 'ADMIN,FINANCE,SUPER ADMIN',
  'edit'    => 'ADMIN,FINANCE,SUPER ADMIN',
  'info'    => 'ADMIN,FINANCE,SUPER ADMIN',
  'save'    => 'ADMIN,FINANCE,SUPER ADMIN',
//   'delete'  => 'ADMIN,FINANCE,SUPER ADMIN',
  );
