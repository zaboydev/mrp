<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['akunting']['visible']         = FALSE;
$config['module']['akunting']['main_warehouse']  = TRUE;
$config['module']['akunting']['parent']          = 'master';
$config['module']['akunting']['label']           = 'Kode Akunting';
$config['module']['akunting']['name']            = 'akunting';
$config['module']['akunting']['route']           = 'akunting';
$config['module']['akunting']['view']            = config_item('module_path') .'akunting/';
$config['module']['akunting']['language']        = 'akunting_lang';
$config['module']['akunting']['table']           = 'tb_master_kode_akunting';
$config['module']['akunting']['model']           = 'Akunting_Model';
$config['module']['akunting']['permission']      = array(
  'index'   => 'ADMIN,SUPERVISOR,VP FINANCE,SUPER ADMIN',
  'create'  => 'ADMIN,SUPERVISOR,VP FINANCE,SUPER ADMIN',
  'edit'    => 'ADMIN,SUPERVISOR,VP FINANCE,SUPER ADMIN',
  'info'    => 'ADMIN,SUPERVISOR,VP FINANCE,SUPER ADMIN',
  'save'    => 'ADMIN,SUPERVISOR,VP FINANCE,SUPER ADMIN',
  'delete'  => 'ADMIN,SUPERVISOR,VP FINANCE,SUPER ADMIN',  
  'import'  => 'ADMIN,SUPERVISOR,VP FINANCE,SUPER ADMIN',
  );
