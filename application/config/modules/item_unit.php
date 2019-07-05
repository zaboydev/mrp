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
  'index'   => 'ADMIN,PIC PROCUREMENT,FINANCE',
  'create'  => 'ADMIN,PIC PROCUREMENT,FINANCE',
  'import'  => 'ADMIN,PIC PROCUREMENT,FINANCE',
  'edit'    => 'ADMIN,PIC PROCUREMENT,FINANCE',
  'info'    => 'ADMIN,PIC PROCUREMENT,FINANCE',
  'save'    => 'ADMIN,PIC PROCUREMENT,FINANCE',
  'delete'  => 'ADMIN',
  );
