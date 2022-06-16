<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['daftar_akun']['visible']     = TRUE;
$config['module']['daftar_akun']['main_warehouse']   = TRUE;
$config['module']['daftar_akun']['parent']      = 'master';
$config['module']['daftar_akun']['label']       = 'Daftar Akun';
$config['module']['daftar_akun']['name']        = 'Daftar Akun';
$config['module']['daftar_akun']['route']       = 'daftar_akun';
$config['module']['daftar_akun']['view']        = config_item('module_path') . 'daftar_akun/';
$config['module']['daftar_akun']['language']    = 'item_group_lang';
$config['module']['daftar_akun']['table']       = 'tb_master_coa';
$config['module']['daftar_akun']['model']       = 'Daftar_Akun_Model';
$config['module']['daftar_akun']['permission']  = array(
  'index'   => 'AP STAFF,ADMIN,FINANCE,SUPER ADMIN,BUDGETCONTROL,VP FINANCE,FINANCE MANAGER,CHIEF OF FINANCE,FINANCE SUPERVISOR',
  'create'  => 'AP STAFF,ADMIN,FINANCE,SUPER ADMIN,BUDGETCONTROL,VP FINANCE,FINANCE MANAGER,CHIEF OF FINANCE,FINANCE SUPERVISOR',
  'import'  => 'ADMIN,FINANCE,SUPER ADMIN,BUDGETCONTROL,VP FINANCE,FINANCE MANAGER,CHIEF OF FINANCE,FINANCE SUPERVISOR',
  'edit'    => 'AP STAFF,ADMIN,FINANCE,SUPER ADMIN,BUDGETCONTROL,VP FINANCE,FINANCE MANAGER,CHIEF OF FINANCE,FINANCE SUPERVISOR',
  'info'    => 'AP STAFF,ADMIN,FINANCE,SUPER ADMIN,BUDGETCONTROL,VP FINANCE,FINANCE MANAGER,CHIEF OF FINANCE,FINANCE SUPERVISOR',
  'save'    => 'AP STAFF,ADMIN,FINANCE,SUPER ADMIN,BUDGETCONTROL,VP FINANCE,FINANCE MANAGER,CHIEF OF FINANCE,FINANCE SUPERVISOR',
  'delete'  => 'AP STAFF,ADMIN,FINANCE,SUPER ADMIN,BUDGETCONTROL,VP FINANCE,FINANCE MANAGER,CHIEF OF FINANCE,FINANCE SUPERVISOR',
  );
