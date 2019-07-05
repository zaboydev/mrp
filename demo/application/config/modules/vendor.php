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
  'index'   => 'ADMIN,PIC PROCUREMENT,PIC STOCK,SUPERVISOR',
  'create'  => 'ADMIN,PIC PROCUREMENT,PIC STOCK,SUPERVISOR',
  'edit'    => 'ADMIN,PIC PROCUREMENT,PIC STOCK,SUPERVISOR',
  'info'    => 'ADMIN,PIC PROCUREMENT,PIC STOCK,SUPERVISOR',
  'save'    => 'ADMIN,PIC PROCUREMENT,PIC STOCK,SUPERVISOR',
  'delete'  => 'ADMIN,PIC STOCK,PIC PROCUREMENT,SUPERVISOR',
  );
