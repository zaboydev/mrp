<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['vendor']['visible']     = TRUE;
$config['module']['vendor']['main_warehouse']   = FALSE;
$config['module']['vendor']['parent']      = 'master';
$config['module']['vendor']['label']       = 'Vendor';
$config['module']['vendor']['name']        = 'vendor';
$config['module']['vendor']['route']       = 'vendor';
$config['module']['vendor']['view']        = config_item('module_path') .'vendor/';
$config['module']['vendor']['language']    = 'vendor_lang';
$config['module']['vendor']['model']       = 'Vendor_Model';
$config['module']['vendor']['table']       = 'tb_master_vendors';
$config['module']['vendor']['permission']  = array(
  'index'   => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPERVISOR,PIC STOCK,SUPER ADMIN',
  'create'  => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'edit'    => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'info'    => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'save'    => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'delete'  => 'ADMIN,SUPER ADMIN',
  );
