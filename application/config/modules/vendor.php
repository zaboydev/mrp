<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['vendor']['visible']     = TRUE;
$config['module']['vendor']['main_warehouse']   = TRUE;
$config['module']['vendor']['parent']      = 'master';
$config['module']['vendor']['label']       = 'Vendor';
$config['module']['vendor']['name']        = 'vendor';
$config['module']['vendor']['route']       = 'vendor';
$config['module']['vendor']['view']        = config_item('module_path') .'vendor/';
$config['module']['vendor']['language']    = 'vendor_lang';
$config['module']['vendor']['model']       = 'Vendor_Model';
$config['module']['vendor']['table']       = 'tb_master_vendors';
$config['module']['vendor']['permission']  = array(
  'index'   => 'ADMIN,PIC PROCUREMENT,FINANCE,SUPERVISOR,PIC STOCK',
  'create'  => 'ADMIN,PIC PROCUREMENT,FINANCE',
  'edit'    => 'ADMIN,PIC PROCUREMENT,FINANCE',
  'info'    => 'ADMIN,PIC PROCUREMENT,FINANCE',
  'save'    => 'ADMIN,PIC PROCUREMENT,FINANCE',
  'delete'  => 'ADMIN',
  );
