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
  'index'   => 'ADMIN,FINANCE,SUPERVISOR',
  'show'    => 'ADMIN,FINANCE,SUPERVISOR',
  'create'  => 'ADMIN,FINANCE,SUPERVISOR',
  'edit'    => 'ADMIN,FINANCE,SUPERVISOR',
  'info'    => 'ADMIN,FINANCE,SUPERVISOR',
  'save'    => 'ADMIN,FINANCE,SUPERVISOR',
  'import'  => 'ADMIN,FINANCE,SUPERVISOR',
  'delete'  => 'ADMIN,FINANCE,SUPERVISOR',
  );
