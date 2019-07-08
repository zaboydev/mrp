<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['item']['visible']     = TRUE;
$config['module']['item']['main_warehouse']   = TRUE;
$config['module']['item']['parent']      = 'master';
$config['module']['item']['label']       = 'Item';
$config['module']['item']['name']        = 'item';
$config['module']['item']['route']       = 'item';
$config['module']['item']['view']        = config_item('module_path') .'item/';
$config['module']['item']['language']    = 'item_lang';
$config['module']['item']['table']       = 'tb_master_items';
$config['module']['item']['model']       = 'Item_Model';
$config['module']['item']['permission']  = array(
  'index'   => 'ADMIN,FINANCE,SUPERVISOR,SUPER ADMIN',
  'show'    => 'ADMIN,FINANCE,SUPERVISOR,SUPER ADMIN',
  'create'  => 'ADMIN,FINANCE,SUPERVISOR,SUPER ADMIN',
  'edit'    => 'ADMIN,FINANCE,SUPERVISOR,SUPER ADMIN',
  'info'    => 'ADMIN,FINANCE,SUPERVISOR,SUPER ADMIN',
  'save'    => 'ADMIN,FINANCE,SUPERVISOR,SUPER ADMIN',
  'import'  => 'ADMIN,FINANCE,SUPERVISOR,SUPER ADMIN',
  'delete'  => 'ADMIN,FINANCE,SUPERVISOR,SUPER ADMIN',
  );
