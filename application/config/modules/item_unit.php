<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['item_unit']['visible']     = TRUE;
$config['module']['item_unit']['main_warehouse']   = TRUE;
$config['module']['item_unit']['parent']      = 'master';
$config['module']['item_unit']['label']       = 'Unit of Measurement';
$config['module']['item_unit']['name']        = 'item_unit';
$config['module']['item_unit']['route']       = 'item_unit';
$config['module']['item_unit']['view']        = config_item('module_path') .'item_unit/';
$config['module']['item_unit']['language']    = 'item_unit_lang';
$config['module']['item_unit']['table']       = 'tb_master_item_units';
$config['module']['item_unit']['model']       = 'Item_Unit_Model';
$config['module']['item_unit']['permission']  = array(
  'index'   => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'create'  => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'import'  => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'edit'    => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'info'    => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'save'    => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'delete'  => 'ADMIN,SUPER ADMIN',
  );
